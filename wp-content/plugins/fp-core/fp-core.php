<?php
/**
 * Plugin Name: Frontier Perspectives Core
 * Description: Content model and site behaviour for Frontier Perspectives — articles, publications, jobs, scholarships, authors, hero slides, settings, contact messages, deadline auto-hide and legacy URL routes.
 * Version: 1.4.0
 * Requires PHP: 8.0
 * Author: Frontier Perspectives
 * Text Domain: fp-core
 */

defined('ABSPATH') || exit;

define('FP_CORE_VERSION', '1.4.0');
define('FP_CORE_DIR', plugin_dir_path(__FILE__));

/** Sentinel used by the old site for "no fixed deadline". Items with it never expire. */
define('FP_NO_DEADLINE', '2099-12-31 23:59:59');

require_once FP_CORE_DIR . 'inc/fields.php';
require_once FP_CORE_DIR . 'inc/post-types.php';
require_once FP_CORE_DIR . 'inc/meta-boxes.php';
require_once FP_CORE_DIR . 'inc/query.php';
require_once FP_CORE_DIR . 'inc/settings.php';
require_once FP_CORE_DIR . 'inc/contact.php';
require_once FP_CORE_DIR . 'inc/admin.php';
require_once FP_CORE_DIR . 'inc/helpers.php';
require_once FP_CORE_DIR . 'inc/import.php';
require_once FP_CORE_DIR . 'inc/seo.php';
require_once FP_CORE_DIR . 'inc/security.php';
require_once FP_CORE_DIR . 'inc/legal.php';
require_once FP_CORE_DIR . 'inc/indexing.php';

/**
 * One-time settings migrations, run whenever the plugin version changes.
 * URLs keep the old site's exact shape: no trailing slash (/jobs, /articles/<slug>).
 */
add_action('init', function () {
    if (get_option('fp_core_version') === FP_CORE_VERSION) return;
    update_option('permalink_structure', '/%postname%');
    update_option('fp_core_version', FP_CORE_VERSION);
    flush_rewrite_rules();
}, 99);

/** Old admin URLs → WordPress admin. */
add_action('template_redirect', function () {
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    $base = trim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
    if ($base !== '' && str_starts_with($path, $base)) $path = trim(substr($path, strlen($base)), '/');
    if ($path === 'admin' || str_starts_with($path, 'admin/')) {
        wp_safe_redirect(admin_url(), 301);
        exit;
    }
}, 1);

register_activation_hook(__FILE__, function () {
    fp_register_post_types();
    fp_register_rewrites();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
