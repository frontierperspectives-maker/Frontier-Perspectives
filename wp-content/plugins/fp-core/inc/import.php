<?php
/**
 * One-way importer from the old CodeIgniter site's database.
 *
 * Runs on the server: it reads the old site's own config file for the DB
 * connection (read-only SELECTs, nothing is written to the old database) and
 * copies uploads from the old /uploads folder into the Media Library.
 *
 * Idempotent: every imported item remembers its old ID (_fp_legacy_id), so
 * re-running updates existing posts instead of duplicating them. Safe to run
 * again right before cutover to pick up last-minute changes.
 *
 * Tools → Import Old Site.
 */
defined('ABSPATH') || exit;

class FP_Importer {
    private mysqli $db;
    private string $old_root;
    private array $media = [];   // old "/uploads/x.jpg" => new URL
    private array $ids = [];     // [table][old_id] => new post/term id
    public array $log = [];
    public array $counts = [];

    public function __construct(?string $old_root = null) {
        $this->old_root = rtrim($old_root ?: self::default_old_root(), '/');
        $this->media = get_option('fp_import_media_map', []);
    }

    /** Staging lives in public_html/staging, so the old site is the parent folder. */
    public static function default_old_root(): string {
        return dirname(untrailingslashit(ABSPATH));
    }

    private function connect(): void {
        $cfg_file = $this->old_root . '/application/config/database.php';
        if (!is_readable($cfg_file)) throw new RuntimeException("Old site config not found at $cfg_file");

        // Load CodeIgniter's config in an isolated scope, exposing only $db.
        $db = (static function (string $file) {
            if (!defined('BASEPATH')) define('BASEPATH', dirname($file, 3) . '/system/');
            if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
            $db = [];
            include $file;
            return $db['default'] ?? null;
        })($cfg_file);
        if (!$db) throw new RuntimeException('Could not read DB settings from old config.');

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->db = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);
        $this->db->set_charset('utf8mb4');
        $this->db->query('SET SESSION TRANSACTION READ ONLY');
    }

    private function rows(string $table, string $order = 'id'): array {
        $res = $this->db->query("SELECT * FROM `$table`" . ($order ? " ORDER BY `$order`" : ''));
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    private function json($v): array {
        $d = $v ? json_decode($v, true) : [];
        return is_array($d) ? $d : [];
    }

    /**
     * Find a previously imported post by legacy id. Direct SQL on purpose: it
     * must see every post (expired, draft, private) regardless of the public
     * query filters. Oldest match wins.
     */
    private function existing(string $type, int $old_id): int {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_fp_legacy_id'
             WHERE p.post_type = %s AND m.meta_value = %s AND p.post_status <> 'trash' ORDER BY p.ID ASC LIMIT 1",
            $type, (string) $old_id
        ));
    }

    /** Remove accidental duplicates (same type + legacy id), keeping the oldest post. */
    private function dedupe(): void {
        global $wpdb;
        $dupes = $wpdb->get_results(
            "SELECT p.ID, p.post_type, m.meta_value AS legacy FROM {$wpdb->posts} p
             JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_fp_legacy_id'
             WHERE p.post_type IN ('fp_article','fp_publication','fp_job','fp_scholarship','fp_author','fp_hero','fp_message','page')
             ORDER BY p.ID ASC"
        );
        $seen = [];
        foreach ($dupes as $d) {
            $k = $d->post_type . ':' . $d->legacy;
            if (isset($seen[$k])) {
                wp_delete_post((int) $d->ID, true);
                $this->log[] = "Removed duplicate {$d->post_type} #{$d->ID} (old id {$d->legacy})";
            } else {
                $seen[$k] = (int) $d->ID;
            }
        }
    }

    /** Copy an old upload into the Media Library (once) and return its new URL. */
    public function media(?string $path): string {
        $path = trim((string) $path);
        if ($path === '') return '';
        if (isset($this->media[$path])) return $this->media[$path];

        $rel = null;
        if (preg_match('~^/?uploads/([^?#]+)$~', $path, $m)) $rel = $m[1];
        elseif (preg_match('~^https?://(www\.)?frontierperspectives\.com/uploads/([^?#]+)$~i', $path, $m)) $rel = $m[2];
        if (!$rel) return $path; // external URL: keep as-is

        $src = $this->old_root . '/uploads/' . basename($rel);
        if (!is_readable($src)) { $this->log[] = "Missing upload: $path"; return $path; }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $tmp = wp_tempnam(basename($src));
        copy($src, $tmp);
        $id = media_handle_sideload(['name' => basename($src), 'tmp_name' => $tmp], 0);
        if (is_wp_error($id)) { @unlink($tmp); $this->log[] = "Upload failed $path: " . $id->get_error_message(); return $path; }

        update_post_meta($id, '_fp_legacy_path', $path);
        $url = wp_get_attachment_url($id);
        $this->media[$path] = $url;
        update_option('fp_import_media_map', $this->media, false);
        return $url;
    }

    /** Rewrite /uploads/... references inside rich HTML and sanitize it. */
    private function html(?string $html): string {
        $html = (string) $html;
        $html = preg_replace_callback('#(src|href)=(["\'])((?:https?://(?:www\.)?frontierperspectives\.com)?/uploads/[^"\']+)\2#i',
            fn($m) => $m[1] . '=' . $m[2] . esc_url($this->media($m[3])) . $m[2], $html);
        return wp_kses_post($html);
    }

    /**
     * Insert or update one post, set its meta from fp_fields() names, and
     * remember the mapping. $meta keys are field names (without prefix).
     */
    private function upsert(string $table, string $type, int $old_id, array $post, array $meta = []): int {
        $id = $this->existing($type, $old_id);
        $post += ['post_type' => $type, 'comment_status' => 'closed', 'ping_status' => 'closed'];
        if ($id) { $post['ID'] = $id; $res = wp_update_post(wp_slash($post), true); }
        else     { $res = wp_insert_post(wp_slash($post), true); }
        if (is_wp_error($res)) { $this->log[] = "$table #$old_id failed: " . $res->get_error_message(); return 0; }
        $id = (int) $res;

        update_post_meta($id, '_fp_legacy_id', $old_id);
        $defs = fp_fields($type);
        foreach ($meta as $field => $value) {
            $clean = isset($defs[$field]) ? fp_sanitize_field($defs[$field], $value) : $value;
            if ($clean === '' || $clean === [] || $clean === null) delete_post_meta($id, fp_meta_key($field));
            else update_post_meta($id, fp_meta_key($field), $clean);
        }
        fp_sync_deadline($id);

        if (!empty($post['post_name']) && get_post_field('post_name', $id) !== $post['post_name']) {
            $this->log[] = "Slug changed for $table #$old_id: {$post['post_name']} → " . get_post_field('post_name', $id);
        }
        $this->ids[$table][$old_id] = $id;
        $this->counts[$table] = ($this->counts[$table] ?? 0) + 1;
        return $id;
    }

    private static function status(array $r, string $flag = 'published'): string {
        return !empty($r[$flag]) ? 'publish' : 'draft';
    }

    /** Old site stores "no fixed deadline" as 2099-12-31. */
    private static function deadline(?string $d): string {
        return (!$d || str_starts_with($d, '2099')) ? '' : substr($d, 0, 10);
    }

    public function run(): array {
        @set_time_limit(600);
        wp_raise_memory_limit('admin');
        wp_defer_term_counting(true);
        $this->connect();
        $this->dedupe();

        // Categories → taxonomy terms.
        foreach ($this->rows('categories') as $r) {
            $term = term_exists($r['slug'], 'fp_category');
            $term = $term ?: wp_insert_term($r['title'], 'fp_category', ['slug' => $r['slug'], 'description' => (string) $r['description']]);
            if (!is_wp_error($term)) {
                wp_update_term((int) $term['term_id'], 'fp_category', ['name' => $r['title'], 'description' => (string) $r['description']]);
                $this->ids['categories'][(int) $r['id']] = (int) $term['term_id'];
                $this->counts['categories'] = ($this->counts['categories'] ?? 0) + 1;
            }
        }

        foreach ($this->rows('authors') as $r) {
            $this->upsert('authors', 'fp_author', (int) $r['id'],
                ['post_title' => $r['name'], 'post_name' => $r['slug'], 'post_content' => (string) $r['bio'], 'post_status' => 'publish'],
                ['role' => $r['role'], 'avatar' => $this->media($r['avatar']), 'links' => $this->json($r['links'])]);
        }

        foreach ($this->rows('articles') as $r) {
            $status = self::status($r);
            $date   = $r['published_at'];
            if ($status === 'publish' && $r['scheduled_at'] && strtotime($r['scheduled_at']) > time()) {
                $status = 'future'; $date = $r['scheduled_at'];
            }
            $id = $this->upsert('articles', 'fp_article', (int) $r['id'], [
                'post_title' => $r['title'], 'post_name' => $r['slug'], 'post_content' => $this->html($r['body']),
                'post_excerpt' => (string) $r['standfirst'], 'post_status' => $status,
                'post_date' => $date, 'post_date_gmt' => get_gmt_from_date($date),
            ], [
                'standfirst' => $r['standfirst'],
                'author' => isset($this->ids['authors'][(int) $r['author_id']]) ? [$this->ids['authors'][(int) $r['author_id']]] : [],
                'image' => $this->media($r['image']), 'image_alt' => $r['image_alt'], 'image_caption' => $r['image_caption'], 'image_credit' => $r['image_credit'],
                'key_findings' => $this->json($r['key_findings']), 'keywords' => $this->json($r['keywords']),
                'meta_title' => $r['meta_title'], 'meta_description' => $r['meta_description'], 'og_image' => $this->media($r['og_image']),
            ]);
            if ($id) {
                $cat = $this->ids['categories'][(int) $r['category_id']] ?? 0;
                wp_set_object_terms($id, $cat ? [$cat] : [], 'fp_category');
                wp_set_object_terms($id, array_map('strval', $this->json($r['tags'])), 'post_tag');
            }
        }

        $contrib = []; foreach ($this->rows('publication_contributors', '') as $r) $contrib[(int) $r['publication_id']][] = (int) $r['author_id'];
        $pub_art = []; foreach ($this->rows('publication_articles', '') as $r) $pub_art[(int) $r['publication_id']][] = (int) $r['article_id'];
        foreach ($this->rows('publications') as $r) {
            $old = (int) $r['id'];
            $this->upsert('publications', 'fp_publication', $old, [
                'post_title' => $r['title'], 'post_name' => $r['slug'], 'post_content' => $this->html($r['description']),
                'post_status' => self::status($r), 'post_date' => $r['published_at'], 'post_date_gmt' => get_gmt_from_date($r['published_at']),
            ], [
                'edition' => $r['edition'], 'publication_type' => $r['publication_type'], 'cover_image' => $this->media($r['cover_image']),
                'pdf_url' => $this->media($r['pdf_url']), 'issn' => $r['issn'], 'table_of_contents' => $this->json($r['table_of_contents']),
                'featured' => $r['featured'],
                'contributors' => array_values(array_filter(array_map(fn($a) => $this->ids['authors'][$a] ?? 0, $contrib[$old] ?? []))),
                'articles' => array_values(array_filter(array_map(fn($a) => $this->ids['articles'][$a] ?? 0, $pub_art[$old] ?? []))),
                'meta_title' => $r['meta_title'], 'meta_description' => $r['meta_description'], 'og_image' => $this->media($r['og_image']),
            ]);
        }

        foreach ($this->rows('jobs') as $r) {
            $this->upsert('jobs', 'fp_job', (int) $r['id'], [
                'post_title' => $r['title'], 'post_name' => $r['slug'], 'post_content' => $this->html($r['description']),
                'post_status' => self::status($r), 'post_date' => $r['posted_at'], 'post_date_gmt' => get_gmt_from_date($r['posted_at']),
            ], [
                'institution' => $r['institution'], 'institution_logo' => $this->media($r['institution_logo']),
                'location' => $r['location'], 'country' => $r['country'], 'remote_ok' => $r['remote_ok'],
                'position_type' => $r['position_type'], 'discipline' => $r['discipline'], 'salary' => trim((string) $r['salary']),
                'deadline' => self::deadline($r['deadline']), 'deadline_note' => $r['deadline_note'],
                'apply_url' => $r['apply_url'], 'keywords' => $this->json($r['keywords']),
            ]);
        }

        foreach ($this->rows('scholarships') as $r) {
            $this->upsert('scholarships', 'fp_scholarship', (int) $r['id'], [
                'post_title' => $r['name'], 'post_name' => $r['slug'], 'post_content' => $this->html($r['description']),
                'post_status' => self::status($r), 'post_date' => $r['posted_at'], 'post_date_gmt' => get_gmt_from_date($r['posted_at']),
            ], [
                'funder' => $r['funder'], 'funder_logo' => $this->media($r['funder_logo']), 'level' => $r['level'],
                'country' => $r['country'], 'eligible_regions' => $this->json($r['eligible_regions']),
                'deadline' => self::deadline($r['deadline']), 'deadline_note' => $r['deadline_note'],
                'apply_url' => $r['apply_url'], 'keywords' => $this->json($r['keywords']),
            ]);
        }

        foreach ($this->rows('hero_slides') as $r) {
            $this->upsert('hero_slides', 'fp_hero', (int) $r['id'], [
                'post_title' => $r['title'], 'post_status' => self::status($r, 'active'), 'menu_order' => (int) $r['sort_order'],
            ], [
                'standfirst' => $r['standfirst'], 'image' => $this->media($r['image']), 'eyebrow' => $r['eyebrow'],
                'href' => $r['href'], 'linked_date' => $r['linked_date'] ? substr($r['linked_date'], 0, 10) : '',
            ]);
        }

        // Simple pages (served at /p/<slug>) — native WordPress Pages.
        foreach ($this->rows('pages') as $r) {
            $this->upsert('pages', 'page', (int) $r['id'],
                ['post_title' => $r['title'], 'post_name' => $r['slug'], 'post_content' => $this->html($r['body']), 'post_status' => 'publish']);
        }
        // Built-in pages the old site rendered from code.
        foreach (['about' => 'About Us', 'submit-opportunity' => 'Submit an Opportunity'] as $slug => $title) {
            if (!get_page_by_path($slug)) {
                wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_content' => '']);
                $this->counts['built-in pages'] = ($this->counts['built-in pages'] ?? 0) + 1;
            }
        }

        // Settings singleton.
        $s = $this->rows('settings', '')[0] ?? null;
        if ($s) {
            update_option('blogname', $s['site_name']);
            update_option('blogdescription', $s['tagline']);
            $social = [];
            foreach ($this->json($s['social_links']) as $l) {
                if (!empty($l['url'])) $social[] = trim(($l['platform'] ?? $l['label'] ?? '') . ' | ' . $l['url'], ' |');
            }
            update_option('fp_settings', [
                'logo' => $this->media($s['logo']), 'contact_email' => sanitize_email((string) $s['contact_email']),
                'footer_mission' => (string) $s['footer_mission'], 'social_links' => $social,
            ]);
            $this->counts['settings'] = 1;
        }

        // Contact messages (private).
        foreach ($this->rows('contact_messages') as $r) {
            $id = $this->upsert('contact_messages', 'fp_message', (int) $r['id'], [
                'post_title' => $r['subject'], 'post_content' => $r['message'], 'post_status' => 'private',
                'post_date' => $r['created_at'], 'post_date_gmt' => get_gmt_from_date($r['created_at']),
            ]);
            if ($id) {
                update_post_meta($id, '_fp_name', $r['name']);
                update_post_meta($id, '_fp_email', $r['email']);
                update_post_meta($id, '_fp_is_read', $r['is_read'] ? '1' : '');
            }
        }

        wp_defer_term_counting(false);
        update_option('fp_import_last', ['time' => current_time('mysql'), 'counts' => $this->counts, 'log' => array_slice($this->log, 0, 200)], false);
        return ['counts' => $this->counts, 'log' => $this->log];
    }
}

/* ---------- Admin screen: Tools → Import Old Site ---------- */
add_action('admin_menu', function () {
    add_management_page('Import Old Site', 'Import Old Site', 'manage_options', 'fp-import', 'fp_render_import_page');
});

function fp_render_import_page(): void {
    if (!current_user_can('manage_options')) return;
    $result = null;
    if (isset($_POST['fp_run_import']) && check_admin_referer('fp_run_import')) {
        try { $result = (new FP_Importer())->run(); }
        catch (Throwable $e) { $result = ['error' => $e->getMessage()]; }
    }
    $last = get_option('fp_import_last');
    ?>
    <div class="wrap">
      <h1>Import from the old site</h1>
      <p>Copies everything from the old site's database and <code>/uploads</code> folder into WordPress. It only <em>reads</em> the old site — nothing there is changed.
         Running it again updates items instead of duplicating them, so you can re-run it right before going live.</p>
      <p>Old site folder: <code><?php echo esc_html(FP_Importer::default_old_root()); ?></code></p>
      <form method="post"><?php wp_nonce_field('fp_run_import'); ?><?php submit_button('Run import now', 'primary', 'fp_run_import'); ?></form>
      <?php if ($result): ?>
        <h2>Result</h2>
        <?php if (!empty($result['error'])): ?><div class="notice notice-error"><p><?php echo esc_html($result['error']); ?></p></div><?php endif; ?>
        <?php if (!empty($result['counts'])): ?><pre><?php echo esc_html(print_r($result['counts'], true)); ?></pre><?php endif; ?>
        <?php if (!empty($result['log'])): ?><h3>Notes</h3><pre><?php echo esc_html(implode("\n", $result['log'])); ?></pre><?php endif; ?>
      <?php elseif ($last): ?>
        <p>Last run: <?php echo esc_html($last['time']); ?></p><pre><?php echo esc_html(print_r($last['counts'], true)); ?></pre>
      <?php endif; ?>
    </div>
    <?php
}
