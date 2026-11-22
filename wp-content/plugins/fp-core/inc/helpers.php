<?php
/** Template helpers used by the theme, plus SEO meta output. */
defined('ABSPATH') || exit;

function fp_format_date($d): string      { return $d ? strtoupper(date_i18n('M j, Y', is_numeric($d) ? $d : strtotime($d))) : ''; }
function fp_format_date_long($d): string { return $d ? date_i18n('F j, Y', is_numeric($d) ? $d : strtotime($d)) : ''; }
/** HTML → one line of plain text. Tags become spaces so "Trust</p><p>Employer" doesn't fuse into "TrustEmployer". */
function fp_plain_text($html, int $max = 300): string {
    $text = wp_strip_all_tags(str_replace('<', ' <', (string) $html));
    $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    if (mb_strlen($text) <= $max) return $text;
    // Cut on a word boundary and add an ellipsis.
    $cut = mb_substr($text, 0, $max);
    $sp  = mb_strrpos($cut, ' ');
    return rtrim($sp > $max * 0.6 ? mb_substr($cut, 0, $sp) : $cut, " ,.;:–—-") . '…';
}
function fp_reading_time($html): int     { return max(1, (int) round(str_word_count(wp_strip_all_tags((string) $html)) / 200)); }

/** Whole calendar days from today to the deadline: 0 = today, 1 = tomorrow, negative = passed. */
function fp_days_until(?string $date): ?int {
    if (!$date) return null;
    $today = new DateTimeImmutable(current_time('Y-m-d'));
    $due   = new DateTimeImmutable(substr($date, 0, 10));
    return (int) $today->diff($due)->format('%r%a');
}

/** Main image URL for an article/hero/etc: explicit field first, then featured image. */
function fp_image_url($post = null, string $field = 'image', string $size = 'large'): string {
    $post = get_post($post);
    if (!$post) return '';
    $url = (string) fp_get($field, $post);
    if (!$url && has_post_thumbnail($post)) $url = (string) get_the_post_thumbnail_url($post, $size);
    return $url;
}

/** Card data for job/scholarship lists — same shape as the old site's opp cards. */
function fp_opp_card($post = null): array {
    $post = get_post($post);
    $is_job = $post->post_type === 'fp_job';
    $tags = $is_job
        ? array_filter([fp_get('position_type', $post), fp_get('discipline', $post)])
        : array_filter(array_merge([fp_get('level', $post), fp_get('country', $post)], fp_get('eligible_regions', $post)));
    return [
        'href'          => get_permalink($post),
        'title'         => get_the_title($post),
        'org'           => fp_get($is_job ? 'institution' : 'funder', $post),
        'org_logo'      => fp_get($is_job ? 'institution_logo' : 'funder_logo', $post),
        'amount'        => $is_job ? fp_get('salary', $post) : '',
        'tags'          => array_values(array_unique($tags)),
        'description'   => fp_plain_text($post->post_content, 140),
        'deadline'      => fp_get('deadline', $post),
        'deadline_note' => fp_get('deadline_note', $post),
        'apply_url'     => fp_get('apply_url', $post),
        'expired'       => fp_is_expired($post),
    ];
}

/** Open items for "More opportunities" sidebars. */
function fp_open_items(string $type, int $limit = 5, int $exclude = 0): array {
    return get_posts([
        'post_type' => $type, 'post_status' => 'publish', 'numberposts' => $limit,
        'post__not_in' => $exclude ? [$exclude] : [], 'meta_query' => [fp_open_clause()],
        'meta_key' => '_fp_deadline_sort', 'orderby' => 'meta_value', 'order' => 'ASC',
    ]);
}

function fp_hero_slides(): array {
    return get_posts(['post_type' => 'fp_hero', 'post_status' => 'publish', 'numberposts' => 10, 'orderby' => 'menu_order', 'order' => 'ASC']);
}

/** Featured publication, falling back to the newest one (old homepage behaviour). */
function fp_featured_publication(): ?WP_Post {
    $p = get_posts(['post_type' => 'fp_publication', 'numberposts' => 1, 'meta_query' => [['key' => fp_meta_key('featured'), 'value' => '1']]]);
    if (!$p) $p = get_posts(['post_type' => 'fp_publication', 'numberposts' => 1]);
    return $p[0] ?? null;
}

function fp_count_open(string $type): int {
    return (int) (new WP_Query(['post_type' => $type, 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => 1, 'meta_query' => [fp_open_clause()], 'no_found_rows' => false]))->found_posts;
}

/* ---------- SEO: per-item title/description/OG from the SEO box ---------- */
add_action('wp_head', function () {
    // Leave it to a dedicated SEO plugin if one is installed.
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

    [, $desc] = fp_seo_meta();
    $image = ''; $type = 'website';
    if (is_singular()) {
        $post  = get_queried_object();
        $image = fp_get('og_image', $post) ?: fp_image_url($post) ?: fp_image_url($post, 'cover_image')
            ?: fp_get('institution_logo', $post) ?: fp_get('funder_logo', $post);
        $type  = $post->post_type === 'fp_article' ? 'article' : 'website';
    }
    $image = $image ?: fp_setting('logo');
    $title = wp_get_document_title();
    $url   = fp_canonical_url() ?: home_url(add_query_arg([]));
    $desc  = fp_plain_text($desc, 160);

    if ($desc) printf("<meta name=\"description\" content=\"%s\">\n", esc_attr($desc));
    printf("<meta property=\"og:title\" content=\"%s\">\n<meta property=\"og:type\" content=\"%s\">\n<meta property=\"og:url\" content=\"%s\">\n<meta property=\"og:site_name\" content=\"%s\">\n<meta property=\"og:locale\" content=\"%s\">\n",
        esc_attr($title), esc_attr($type), esc_url($url), esc_attr(get_bloginfo('name')), esc_attr(get_locale()));
    if ($desc)  printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($desc));
    printf("<meta name=\"twitter:card\" content=\"%s\">\n", $image ? 'summary_large_image' : 'summary');
    if ($image) printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url($image));
}, 5);
