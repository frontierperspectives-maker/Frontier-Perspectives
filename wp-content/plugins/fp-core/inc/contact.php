<?php
/**
 * Contact form. Same contract as the old site: POST /contact with
 * name, email, subject, message (+ hidden honeypot "website") → JSON.
 * Messages are stored as private "Messages" posts and emailed to the
 * contact address from Site Settings.
 */
defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('fp_message', [
        'labels'          => ['name' => 'Messages', 'singular_name' => 'Message', 'edit_item' => 'View Message'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => false,
        'menu_icon'       => 'dashicons-email-alt',
        'menu_position'   => 25,
        'supports'        => ['title', 'editor'],
        'capability_type' => 'post',
        'capabilities'    => ['create_posts' => 'do_not_allow'],
        'map_meta_cap'    => true,
    ]);
    add_rewrite_rule('^contact/?$', 'index.php?fp_contact=1', 'top');
});

add_filter('query_vars', fn($v) => array_merge($v, ['fp_contact']));

add_action('template_redirect', function () {
    if (!get_query_var('fp_contact')) return;
    // (priority 1: run before WordPress's canonical-URL redirect.) WordPress has
    // already flagged this "page" as a 404 by now, so every response below sets
    // its own status code explicitly.

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Humans landing on /contact get the form page.
        $page = get_page_by_path('contact') ?: get_page_by_path('submit-opportunity');
        wp_safe_redirect($page ? get_permalink($page) : home_url('/about'), 302);
        exit;
    }

    $p = wp_unslash($_POST);
    if (!empty($p['website'])) wp_send_json(['ok' => true], 200); // honeypot

    $ip_key = 'fp_contact_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    $count  = (int) get_transient($ip_key);
    if ($count >= 5) wp_send_json(['error' => 'Too many messages — please try again later.'], 429);

    $name    = sanitize_text_field($p['name'] ?? '');
    $email   = sanitize_email($p['email'] ?? '');
    $subject = sanitize_text_field($p['subject'] ?? '');
    $message = sanitize_textarea_field($p['message'] ?? '');

    if (!$name || !$subject || !$message || !is_email($email)) {
        wp_send_json(['error' => 'Please fill in all fields with a valid email.'], 400);
    }
    if (mb_strlen($name) > 120 || mb_strlen($subject) > 200 || mb_strlen($message) > 5000) {
        wp_send_json(['error' => 'Your message is too long — please shorten it (max 5,000 characters).'], 400);
    }
    // Typical spam: a wall of links.
    if (preg_match_all('~https?://~i', $message) > 5) {
        wp_send_json(['error' => 'Please include no more than 5 links.'], 400);
    }

    $id = wp_insert_post([
        'post_type'    => 'fp_message',
        'post_status'  => 'private',
        'post_title'   => $subject,
        'post_content' => $message,
    ]);
    if (is_wp_error($id) || !$id) wp_send_json(['error' => 'Sorry, something went wrong. Please try again.'], 500);

    update_post_meta($id, '_fp_name', $name);
    update_post_meta($id, '_fp_email', $email);
    update_post_meta($id, '_fp_is_read', '');
    set_transient($ip_key, $count + 1, 10 * MINUTE_IN_SECONDS);

    $to = fp_setting('contact_email') ?: get_option('admin_email');
    wp_mail($to, '[' . get_bloginfo('name') . '] ' . $subject, "From: $name <$email>\n\n$message", ['Reply-To: ' . $name . ' <' . $email . '>']);

    wp_send_json(['ok' => true], 200);
}, 1);

// Admin list: show sender + read status; opening a message marks it read.
add_filter('manage_fp_message_posts_columns', fn($c) => ['cb' => $c['cb'], 'title' => 'Subject', 'fp_from' => 'From', 'fp_read' => 'Status', 'date' => 'Received']);
add_action('manage_fp_message_posts_custom_column', function ($col, $id) {
    if ($col === 'fp_from') {
        $email = get_post_meta($id, '_fp_email', true);
        printf('%s<br><a href="mailto:%s">%s</a>', esc_html(get_post_meta($id, '_fp_name', true)), esc_attr($email), esc_html($email));
    }
    if ($col === 'fp_read') echo get_post_meta($id, '_fp_is_read', true) ? 'Read' : '<strong>New</strong>';
}, 10, 2);

add_action('add_meta_boxes_fp_message', function (WP_Post $post) {
    update_post_meta($post->ID, '_fp_is_read', '1');
    add_meta_box('fp_message_from', 'From', function ($post) {
        $email = get_post_meta($post->ID, '_fp_email', true);
        printf('<p><strong>%s</strong><br><a href="mailto:%s">%s</a></p>', esc_html(get_post_meta($post->ID, '_fp_name', true)), esc_attr($email), esc_html($email));
    }, 'fp_message', 'side', 'high');
});

// Unread count bubble in the admin menu.
add_action('admin_menu', function () {
    global $menu;
    $unread = (int) (new WP_Query([
        'post_type' => 'fp_message', 'post_status' => 'private', 'fields' => 'ids', 'posts_per_page' => 1,
        'meta_query' => [['key' => '_fp_is_read', 'value' => '']],
    ]))->found_posts;
    if (!$unread) return;
    foreach ($menu as &$item) {
        if (($item[2] ?? '') === 'edit.php?post_type=fp_message') {
            $item[0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $unread . '</span></span>';
        }
    }
}, 99);
