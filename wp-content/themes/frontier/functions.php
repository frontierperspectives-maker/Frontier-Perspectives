<?php
defined('ABSPATH') || exit;

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
});

add_action('wp_enqueue_scripts', function () {
    $ver = wp_get_theme()->get('Version');
    // Bootstrap is bundled with the theme (no third-party CDN: faster, private, no outside dependency).
    $dir = get_template_directory_uri() . '/assets';
    wp_enqueue_style('bootstrap', "$dir/vendor/bootstrap.min.css", [], '5.3.3');
    wp_enqueue_style('frontier', "$dir/app.css", ['bootstrap'], $ver);
    wp_enqueue_script('bootstrap', "$dir/vendor/bootstrap.bundle.min.js", [], '5.3.3', ['in_footer' => true, 'strategy' => 'defer']);
    wp_add_inline_script('bootstrap', "document.addEventListener('scroll',function(){var h=document.querySelector('.site-header');if(h)h.classList.toggle('scrolled',window.scrollY>8);});");
});

// The theme needs the core plugin; fail gracefully if it is switched off.
add_action('template_redirect', function () {
    if (!function_exists('fp_get') && !is_admin()) {
        wp_die('The Frontier Perspectives Core plugin must be active for this theme to work.', 'Plugin required', ['response' => 503]);
    }
});

/** Site-relative URL helper so links work on staging and live alike. */
function fr_url(string $path = '/'): string {
    // Absolute links (e.g. a hero slide pointing elsewhere) pass through unchanged.
    if (preg_match('#^(https?:)?//|^mailto:#i', $path)) return esc_url($path);
    return esc_url(home_url($path));
}

/** Main navigation — mirrors the old site's menu, including dropdown sub-pages. */
function fr_nav_items(): array {
    $countries = [['label' => 'All Jobs', 'href' => '/jobs']];
    foreach (fp_country_slugs() as $name => $slug) $countries[] = ['label' => $name, 'href' => "/jobs/$slug"];

    $q = fn(string $base, string $param, string $v) => $base . '?' . $param . '=' . rawurlencode($v);
    return [
        ['label' => 'Research Articles', 'href' => '/articles'],
        ['label' => 'Jobs', 'href' => '/jobs', 'children' => $countries],
        ['label' => 'Fellowships', 'href' => '/fellowships', 'children' => [
            ['label' => 'All Fellowships', 'href' => '/fellowships'],
            ['label' => 'Research Fellowships', 'href' => $q('/fellowships', 'type', 'Research Fellowship')],
            ['label' => 'Graduate Fellowships', 'href' => $q('/fellowships', 'type', 'Graduate Fellowship')],
            ['label' => 'Postgraduate Fellowships', 'href' => $q('/fellowships', 'type', 'Postgraduate Fellowship')],
            ['label' => 'Postdoctoral Fellowships', 'href' => $q('/fellowships', 'type', 'Postdoctoral Fellowship')],
            ['label' => 'Visiting Fellowships', 'href' => $q('/fellowships', 'type', 'Visiting Fellowship')],
            ['label' => 'Professional Fellowships', 'href' => $q('/fellowships', 'type', 'Professional Fellowship')],
        ]],
        ['label' => 'Scholarships', 'href' => '/scholarships', 'children' => [
            ['label' => 'All Scholarships', 'href' => '/scholarships'],
            ['label' => 'BS, MS Scholarships', 'href' => $q('/scholarships', 'level', 'BS/MS')],
            ['label' => 'PhD Scholarships', 'href' => $q('/scholarships', 'level', 'PhD')],
            ['label' => 'Post Doc Scholarships', 'href' => $q('/scholarships', 'level', 'Post Doc')],
        ]],
        ['label' => 'Grants', 'href' => '/grants', 'children' => [
            ['label' => 'All Grants', 'href' => '/grants'],
            ['label' => 'Research Grants', 'href' => $q('/grants', 'level', 'Research Grant')],
            ['label' => 'Student Grants', 'href' => $q('/grants', 'level', 'Student Grant')],
            ['label' => 'Faculty Grants', 'href' => $q('/grants', 'level', 'Faculty Grant')],
            ['label' => 'Early Career Researcher Grants', 'href' => $q('/grants', 'level', 'Early Career Researcher Grant')],
        ]],
        ['label' => 'About', 'href' => '/about'],
    ];
}

/** Current request path relative to the site root, e.g. "/jobs/usa". */
function fr_current_path(): string {
    $home = wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
    $path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return '/' . ltrim(substr($path, strlen(rtrim($home, '/'))), '/');
}

/** Gold accent bar + H1 + intro used at the top of list pages. */
function fr_page_head(string $title, string $intro = ''): void {
    echo '<div class="mb-4"><div style="width:48px;height:4px;background:var(--gold);border-radius:2px;" class="mb-3"></div>';
    echo '<h1 class="text-navy fw-bold">' . esc_html($title) . '</h1>';
    if ($intro) echo '<p class="text-muted2">' . esc_html($intro) . '</p>';
    echo '</div>';
}

function fr_breadcrumbs(array $trail): void {
    $parts = ['<a href="' . fr_url('/') . '">Home</a>'];
    foreach ($trail as $label => $href) {
        $parts[] = $href ? '<a href="' . fr_url($href) . '">' . esc_html($label) . '</a>' : esc_html($label);
    }
    echo '<p class="text-muted2" style="font-size:13px;">' . implode(' / ', $parts) . '</p>';
}

function fr_pagination(): void {
    $links = paginate_links(['type' => 'array', 'prev_text' => '← Previous', 'next_text' => 'Next →']);
    if ($links) echo '<nav class="fp-pagination" aria-label="Pages">' . implode('', $links) . '</nav>';
}

/** Deadline label with the "urgent" style when ≤14 days remain (old site behaviour). */
function fr_deadline_html(?string $deadline, ?string $note, bool $with_countdown = true): string {
    if ($note) return '<span class="deadline">' . esc_html($note) . '</span>';
    if (!$deadline) return '<span class="deadline">Deadline not specified</span>';
    $d = fp_days_until($deadline);
    $urgent = $d !== null && $d <= 14;
    $tail = '';
    if ($with_countdown && $urgent && $d >= 0) {
        $tail = $d === 0 ? ' · Closes today' : ' · ' . $d . ' day' . ($d === 1 ? '' : 's') . ' left';
    }
    return '<span class="deadline' . ($urgent ? ' urgent' : '') . '">Deadline ' . esc_html(fp_format_date($deadline)) . $tail . '</span>';
}

/** Rich text from the editor; WordPress already filtered it on save (KSES). */
function fr_content($post = null): string {
    $post = get_post($post);
    return apply_filters('the_content', $post->post_content);
}
