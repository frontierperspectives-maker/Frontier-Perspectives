<?php
/** Admin conveniences: deadline/Expired columns, Duplicate, bulk publish/unpublish. */
defined('ABSPATH') || exit;

const FP_CONTENT_TYPES = ['fp_article', 'fp_publication', 'fp_job', 'fp_scholarship', 'fp_author', 'fp_hero'];

/* ---------- Deadline + status columns for jobs & scholarships ---------- */
foreach (['fp_job', 'fp_scholarship'] as $fp_type) {
    add_filter("manage_{$fp_type}_posts_columns", function ($cols) use ($fp_type) {
        $new = [];
        foreach ($cols as $k => $v) {
            $new[$k] = $v;
            if ($k === 'title') {
                $new['fp_org']      = $fp_type === 'fp_job' ? 'Institution' : 'Funder';
                $new['fp_kind']     = $fp_type === 'fp_job' ? 'Type' : 'Level';
                $new['fp_deadline'] = 'Deadline';
            }
        }
        return $new;
    });
    add_action("manage_{$fp_type}_posts_custom_column", function ($col, $id) use ($fp_type) {
        if ($col === 'fp_org')  echo esc_html(fp_get($fp_type === 'fp_job' ? 'institution' : 'funder', $id));
        if ($col === 'fp_kind') echo esc_html(fp_get($fp_type === 'fp_job' ? 'position_type' : 'level', $id));
        if ($col === 'fp_deadline') {
            $d = fp_get('deadline', $id);
            echo $d ? esc_html(date_i18n('M j, Y', strtotime($d))) : '<em>' . esc_html(fp_get('deadline_note', $id) ?: 'No fixed date') . '</em>';
            if (fp_is_expired($id)) echo ' <span style="background:#fde2e1;color:#b42318;border-radius:999px;padding:1px 8px;font-size:11px;font-weight:600">Expired</span>';
        }
    }, 10, 2);
    add_filter("manage_edit-{$fp_type}_sortable_columns", fn($c) => $c + ['fp_deadline' => 'fp_deadline']);
}
add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() && $q->is_main_query() && $q->get('orderby') === 'fp_deadline') {
        $q->set('meta_key', '_fp_deadline_sort');
        $q->set('orderby', 'meta_value');
    }
});

/* ---------- Duplicate ---------- */
add_filter('post_row_actions', function ($actions, WP_Post $post) {
    if (in_array($post->post_type, FP_CONTENT_TYPES, true) && current_user_can('edit_post', $post->ID)) {
        $url = wp_nonce_url(admin_url('admin-post.php?action=fp_duplicate&post=' . $post->ID), 'fp_duplicate_' . $post->ID);
        $actions['fp_duplicate'] = '<a href="' . esc_url($url) . '">Duplicate</a>';
    }
    return $actions;
}, 10, 2);

add_action('admin_post_fp_duplicate', function () {
    $id = (int) ($_GET['post'] ?? 0);
    check_admin_referer('fp_duplicate_' . $id);
    $src = get_post($id);
    if (!$src || !current_user_can('edit_post', $id)) wp_die('Not allowed.');

    $new_id = wp_insert_post([
        'post_type'    => $src->post_type,
        'post_status'  => 'draft',
        'post_title'   => $src->post_title . ' (copy)',
        'post_content' => $src->post_content,
        'post_excerpt' => $src->post_excerpt,
        'menu_order'   => $src->menu_order,
    ]);
    foreach (get_post_meta($id) as $key => $values) {
        if (in_array($key, ['_edit_lock', '_edit_last', '_wp_old_slug'], true)) continue;
        foreach ($values as $v) add_post_meta($new_id, $key, maybe_unserialize($v));
    }
    foreach (get_object_taxonomies($src->post_type) as $tax) {
        wp_set_object_terms($new_id, wp_get_object_terms($id, $tax, ['fields' => 'ids']), $tax);
    }
    wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_id));
    exit;
});

/* ---------- Bulk publish / unpublish ---------- */
foreach (FP_CONTENT_TYPES as $fp_type) {
    add_filter("bulk_actions-edit-$fp_type", fn($a) => $a + ['fp_publish' => 'Publish', 'fp_unpublish' => 'Unpublish (move to draft)']);
    add_filter("handle_bulk_actions-edit-$fp_type", function ($redirect, $action, $ids) {
        if (!in_array($action, ['fp_publish', 'fp_unpublish'], true)) return $redirect;
        $n = 0;
        foreach ($ids as $id) {
            if (!current_user_can('edit_post', $id)) continue;
            wp_update_post(['ID' => $id, 'post_status' => $action === 'fp_publish' ? 'publish' : 'draft']);
            $n++;
        }
        return add_query_arg('fp_bulk_done', $n, $redirect);
    }, 10, 3);
}
add_action('admin_notices', function () {
    if (!empty($_GET['fp_bulk_done'])) {
        printf('<div class="notice notice-success is-dismissible"><p>%d item(s) updated.</p></div>', (int) $_GET['fp_bulk_done']);
    }
});

/* ---------- Hero slides: show order + link ---------- */
add_filter('manage_fp_hero_posts_columns', fn($c) => ['cb' => $c['cb'], 'title' => 'Title', 'fp_order' => 'Order', 'fp_href' => 'Links to', 'date' => 'Date']);
add_action('manage_fp_hero_posts_custom_column', function ($col, $id) {
    if ($col === 'fp_order') echo (int) get_post_field('menu_order', $id);
    if ($col === 'fp_href')  echo esc_html(fp_get('href', $id));
}, 10, 2);
