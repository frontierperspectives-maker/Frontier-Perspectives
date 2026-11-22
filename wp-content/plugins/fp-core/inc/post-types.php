<?php
defined('ABSPATH') || exit;

add_action('init', 'fp_register_post_types', 5);

function fp_register_post_types(): void {
    $common = [
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => true,
        'menu_position'=> 5,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
    ];

    $types = [
        'fp_article'     => ['Articles', 'Article', 'articles', 'dashicons-media-document'],
        'fp_publication' => ['Publications', 'Publication', 'publications', 'dashicons-book-alt'],
        'fp_job'         => ['Jobs & Fellowships', 'Job', 'jobs', 'dashicons-businessperson'],
        'fp_scholarship' => ['Scholarships & Grants', 'Scholarship', 'scholarships', 'dashicons-awards'],
        'fp_author'      => ['Authors', 'Author', 'authors', 'dashicons-id'],
    ];

    foreach ($types as $type => [$plural, $singular, $slug, $icon]) {
        // Authors are listed on /about but never had pages of their own.
        $visibility = $type === 'fp_author'
            ? ['public' => false, 'show_ui' => true, 'has_archive' => false, 'supports' => ['title', 'editor', 'thumbnail', 'revisions', 'custom-fields']]
            : [];
        register_post_type($type, $visibility + $common + [
            'labels' => [
                'name'          => $plural,
                'singular_name' => $singular,
                'add_new_item'  => "Add New $singular",
                'edit_item'     => "Edit $singular",
                'view_item'     => "View $singular",
                'search_items'  => "Search $plural",
                'not_found'     => "No $plural found",
                'all_items'     => "All $plural",
            ],
            'menu_icon' => $icon,
            'rewrite'   => ['slug' => $slug, 'with_front' => false],
            'taxonomies'=> $type === 'fp_article' ? ['fp_category', 'post_tag'] : [],
        ]);
    }

    // Hero slides: admin-only, ordered by menu_order ("Order" box in the editor).
    register_post_type('fp_hero', [
        'labels'       => ['name' => 'Hero Slides', 'singular_name' => 'Hero Slide', 'add_new_item' => 'Add New Hero Slide', 'edit_item' => 'Edit Hero Slide'],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-images-alt2',
        'menu_position'=> 20,
        'supports'     => ['title', 'page-attributes', 'custom-fields'],
    ]);

    register_taxonomy('fp_category', ['fp_article'], [
        'labels'            => ['name' => 'Categories', 'singular_name' => 'Category'],
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'articles/category', 'with_front' => false],
    ]);

    // Expose every schema field over REST (used by the editor and the importer).
    foreach (fp_fields() as $type => $fields) {
        foreach ($fields as $name => $def) {
            $is_array = in_array($def['type'], ['list', 'relation'], true);
            register_post_meta($type, fp_meta_key($name), [
                'single'        => true,
                'type'          => $is_array ? 'array' : 'string',
                'show_in_rest'  => $is_array
                    ? ['schema' => ['type' => 'array', 'items' => ['type' => $def['type'] === 'relation' ? 'integer' : 'string']]]
                    : true,
                'auth_callback' => fn() => current_user_can('edit_posts'),
                'sanitize_callback' => fn($v) => fp_sanitize_field($def, $v),
            ]);
        }
    }
    // Hidden, always-set sort/filter key for deadlines (blank deadline => far future).
    foreach (['fp_job', 'fp_scholarship'] as $type) {
        register_post_meta($type, '_fp_deadline_sort', [
            'single' => true, 'type' => 'string', 'show_in_rest' => false,
            'auth_callback' => fn() => current_user_can('edit_posts'),
        ]);
    }
}

/** Keep _fp_deadline_sort in step with the deadline field, whichever way it was saved. */
function fp_sync_deadline(int $post_id): void {
    $type = get_post_type($post_id);
    if (!in_array($type, ['fp_job', 'fp_scholarship'], true)) return;
    $d = get_post_meta($post_id, fp_meta_key('deadline'), true);
    // Deadline is inclusive: an item due today stays visible all day.
    $sort = $d ? $d . ' 23:59:59' : FP_NO_DEADLINE;
    if (get_post_meta($post_id, '_fp_deadline_sort', true) !== $sort) {
        update_post_meta($post_id, '_fp_deadline_sort', $sort);
    }
}
add_action('save_post', fn($id) => fp_sync_deadline((int) $id), 99);
add_action('updated_post_meta', function ($mid, $post_id, $key) { if ($key === fp_meta_key('deadline')) fp_sync_deadline((int) $post_id); }, 10, 3);
add_action('added_post_meta',   function ($mid, $post_id, $key) { if ($key === fp_meta_key('deadline')) fp_sync_deadline((int) $post_id); }, 10, 3);
add_action('deleted_post_meta', function ($mids, $post_id, $key) { if ($key === fp_meta_key('deadline')) fp_sync_deadline((int) $post_id); }, 10, 3);

/**
 * Everything uses the classic editor: one scrolling page with the title, a Word-like
 * text box, every field visible underneath, and a simple Publish box. One editor to
 * learn for a non-technical owner (the block editor hides custom fields in a
 * collapsible bottom panel).
 */
add_filter('use_block_editor_for_post_type', '__return_false', 100);
add_filter('use_block_editor_for_post', '__return_false', 100);

/** Hide edit-screen boxes the listings don't use, so only meaningful fields are shown. */
add_action('add_meta_boxes', function () {
    foreach (['fp_job', 'fp_scholarship', 'fp_publication', 'fp_author', 'fp_hero'] as $type) {
        remove_meta_box('postcustom', $type, 'normal');   // "Custom Fields"
        remove_meta_box('postexcerpt', $type, 'normal');  // "Excerpt"
        remove_meta_box('slugdiv', $type, 'normal');
    }
    remove_meta_box('postcustom', 'fp_article', 'normal');
    remove_meta_box('postexcerpt', 'fp_article', 'normal'); // the Standfirst field is the summary
    remove_meta_box('postcustom', 'page', 'normal');
    remove_meta_box('slugdiv', 'fp_article', 'normal'); // the address is edited under the title
    remove_meta_box('slugdiv', 'page', 'normal');
    foreach (['fp_job', 'fp_scholarship', 'fp_hero'] as $type) {
        remove_meta_box('postimagediv', $type, 'side');   // logos/images have their own fields
    }
}, 99);
