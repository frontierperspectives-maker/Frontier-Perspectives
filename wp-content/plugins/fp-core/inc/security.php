<?php
/**
 * Hardening: no XML-RPC, no username enumeration, login throttling, security
 * headers, no version leaks, no code editing from the dashboard.
 */
defined('ABSPATH') || exit;

/* ---------- XML-RPC off (brute-force amplification via system.multicall) ---------- */
// Refuse xmlrpc.php outright: WordPress re-adds the system.* methods after any filter.
if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
    status_header(403);
    header('Content-Type: text/plain');
    exit('XML-RPC is disabled.');
}
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', fn() => []);
add_filter('wp_headers', function ($h) { unset($h['X-Pingback']); return $h; });
remove_action('wp_head', 'rsd_link');

/* ---------- Username enumeration ---------- */
// /?author=N and /author/<name> → home (the site has no author archives).
add_action('template_redirect', function () {
    if (is_author() || (isset($_GET['author']) && !is_admin())) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}, 0);
// REST users list/details only for logged-in editors.
add_filter('rest_endpoints', function ($endpoints) {
    if (current_user_can('list_users')) return $endpoints;
    foreach (array_keys($endpoints) as $route) {
        if (str_starts_with($route, '/wp/v2/users')) unset($endpoints[$route]);
    }
    return $endpoints;
});
// oEmbed would also leak the author name.
add_filter('oembed_response_data', function ($data) { unset($data['author_name'], $data['author_url']); return $data; });

/* ---------- Login throttling ---------- */
function fp_client_ip(): string {
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}
// Two independent limits: per IP (single attacker) and per username (botnets rotating IPs).
const FP_LOGIN_MAX_FAILS_IP   = 6;
const FP_LOGIN_MAX_FAILS_USER = 10;
const FP_LOGIN_LOCK_MIN       = 20;

function fp_login_keys(string $username): array {
    return [
        'ip'   => 'fp_login_fail_ip_' . md5(fp_client_ip()),
        'user' => 'fp_login_fail_u_' . md5(strtolower(trim($username))),
    ];
}

add_filter('authenticate', function ($user, $username) {
    if (empty($username)) return $user;
    $k = fp_login_keys((string) $username);
    if ((int) get_transient($k['ip']) >= FP_LOGIN_MAX_FAILS_IP || (int) get_transient($k['user']) >= FP_LOGIN_MAX_FAILS_USER) {
        return new WP_Error('fp_locked', sprintf('Too many failed login attempts. Please try again in %d minutes.', FP_LOGIN_LOCK_MIN));
    }
    return $user;
}, 99, 2);

add_action('wp_login_failed', function ($username) {
    foreach (fp_login_keys((string) $username) as $key) {
        set_transient($key, (int) get_transient($key) + 1, FP_LOGIN_LOCK_MIN * MINUTE_IN_SECONDS);
    }
});
add_action('wp_login', function ($username) {
    foreach (fp_login_keys((string) $username) as $key) delete_transient($key);
});

// Don't reveal whether the username or the password was wrong.
add_filter('login_errors', function ($msg) {
    if (str_contains($msg, 'Too many failed')) return $msg;
    return '<strong>Error:</strong> The username/email or password is incorrect.';
});

/* ---------- Security headers ---------- */
add_action('send_headers', function () {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    if (is_ssl()) header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
});

/* ---------- Version leaks ---------- */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/* ---------- No theme/plugin file editor in the dashboard ---------- */
add_filter('map_meta_cap', function ($caps, $cap) {
    return in_array($cap, ['edit_themes', 'edit_plugins', 'edit_files'], true) ? ['do_not_allow'] : $caps;
}, 10, 2);

/* ---------- No WordPress emoji script (it loads images from wordpress.org; browsers render emoji natively) ---------- */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('emoji_svg_url', '__return_false');
add_filter('wp_resource_hints', function ($urls, $type) {
    return $type === 'dns-prefetch' ? array_values(array_filter($urls, fn($u) => !str_contains((string) (is_array($u) ? ($u['href'] ?? '') : $u), 's.w.org'))) : $urls;
}, 10, 2);
