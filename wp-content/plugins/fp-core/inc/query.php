<?php
/**
 * Public-facing query behaviour and legacy URL routes.
 *
 *  - Jobs & scholarships whose deadline has passed are hidden from every public
 *    LIST (archives, fellowships, grants, country pages, search, homepage
 *    widgets). Nothing is deleted or unpublished: detail pages still open from
 *    old links, and the admin shows them marked "Expired".
 *  - /fellowships      = open jobs whose position type contains "fellow"
 *  - /grants           = open scholarships whose level contains "grant"
 *  - /jobs/<country>, /scholarships/<country> = open items in that country
 *  - /p/<slug>         = a WordPress Page
 *  - /search?q=        = site search across articles, publications, jobs, scholarships
 *  - List filters via ?type= &discipline= &location=Remote &country= &level= &region=
 */
defined('ABSPATH') || exit;

add_action('init', 'fp_register_rewrites', 20);

function fp_register_rewrites(): void {
    $countries = implode('|', array_map('preg_quote', array_values(fp_country_slugs())));

    add_rewrite_rule('^fellowships/?$', 'index.php?post_type=fp_job&fp_view=fellowships', 'top');
    add_rewrite_rule('^fellowships/page/([0-9]+)/?$', 'index.php?post_type=fp_job&fp_view=fellowships&paged=$matches[1]', 'top');
    add_rewrite_rule('^grants/?$', 'index.php?post_type=fp_scholarship&fp_view=grants', 'top');
    add_rewrite_rule('^grants/page/([0-9]+)/?$', 'index.php?post_type=fp_scholarship&fp_view=grants&paged=$matches[1]', 'top');
    add_rewrite_rule("^jobs/($countries)/?$", 'index.php?post_type=fp_job&fp_country=$matches[1]', 'top');
    add_rewrite_rule("^jobs/($countries)/page/([0-9]+)/?$", 'index.php?post_type=fp_job&fp_country=$matches[1]&paged=$matches[2]', 'top');
    add_rewrite_rule("^scholarships/($countries)/?$", 'index.php?post_type=fp_scholarship&fp_country=$matches[1]', 'top');
    add_rewrite_rule("^scholarships/($countries)/page/([0-9]+)/?$", 'index.php?post_type=fp_scholarship&fp_country=$matches[1]&paged=$matches[2]', 'top');
    add_rewrite_rule('^p/([^/]+)/?$', 'index.php?pagename=$matches[1]', 'top');
    add_rewrite_rule('^search/?$', 'index.php?fp_search=1', 'top');
    add_rewrite_rule('^search/page/([0-9]+)/?$', 'index.php?fp_search=1&paged=$matches[1]', 'top');
}

add_filter('query_vars', function (array $vars) {
    return array_merge($vars, ['fp_view', 'fp_country', 'fp_search']);
});

/** True for the public "open opportunities" lists. */
function fp_is_open_list_query(WP_Query $q): bool {
    if (is_admin() || $q->is_singular() || $q->get('fp_all')) return false;
    // Lookups by slug/ID (e.g. single-page resolution, internal code) are not lists.
    if ($q->get('name') || $q->get('p') || $q->get('post__in')) return false;
    $types = (array) $q->get('post_type');
    return (bool) array_intersect($types, ['fp_job', 'fp_scholarship']);
}

/** Meta query clause that keeps only items whose deadline is still ahead. */
function fp_open_clause(): array {
    return [
        'key'     => '_fp_deadline_sort',
        'value'   => current_time('mysql'),
        'compare' => '>=',
        'type'    => 'DATETIME',
    ];
}

add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin()) return;

    // /articles?category=<slug> (old site's category filter pills).
    if ($q->is_main_query() && $q->is_post_type_archive('fp_article') && !empty($_GET['category'])) {
        $q->set('tax_query', [[
            'taxonomy' => 'fp_category', 'field' => 'slug',
            'terms'    => sanitize_title(wp_unslash($_GET['category'])),
        ]]);
    }

    // /search?q=… → WordPress search over the four public content types.
    if ($q->is_main_query() && $q->get('fp_search')) {
        $term = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
        $q->set('s', $term);
        $q->set('post_type', ['fp_article', 'fp_publication', 'fp_job', 'fp_scholarship']);
        $q->is_search = true;
        $q->is_home = false;
        if ($term === '') $q->set('post__in', [0]);
    }

    // Hide expired jobs/scholarships from search results too.
    if ($q->is_search() && !$q->is_singular()) {
        $mq = (array) $q->get('meta_query');
        $mq[] = [
            'relation' => 'OR',
            ['key' => '_fp_deadline_sort', 'compare' => 'NOT EXISTS'],
            fp_open_clause(),
        ];
        $q->set('meta_query', $mq);
        return;
    }

    if (!fp_is_open_list_query($q)) return;

    $mq = (array) $q->get('meta_query');
    $mq[] = fp_open_clause();

    $view    = $q->get('fp_view');
    $country = $q->get('fp_country');
    $is_job  = in_array('fp_job', (array) $q->get('post_type'), true);

    if ($view === 'fellowships') {
        $mq[] = ['key' => fp_meta_key('position_type'), 'value' => 'fellow', 'compare' => 'LIKE'];
    } elseif ($view === 'grants') {
        $mq[] = ['key' => fp_meta_key('level'), 'value' => 'grant', 'compare' => 'LIKE'];
    }
    if ($country) {
        $name = array_search($country, fp_country_slugs(), true);
        $mq[] = ['key' => fp_meta_key('country'), 'value' => $name ?: '__none__'];
    }

    // Main-query list filters from the URL (?type=…&country=… etc).
    if ($q->is_main_query()) {
        foreach (fp_active_filters($is_job ? 'fp_job' : 'fp_scholarship') as $field => $value) {
            if ($field === 'location') {
                $mq[] = ['key' => fp_meta_key('remote_ok'), 'value' => '1'];
            } elseif ($field === 'eligible_regions') {
                // Stored as a serialized array; match the exact quoted string.
                $mq[] = ['key' => fp_meta_key('eligible_regions'), 'value' => serialize((string) $value), 'compare' => 'LIKE'];
            } else {
                $mq[] = ['key' => fp_meta_key($field), 'value' => $value];
            }
        }
        $q->set('posts_per_page', 24);
    }

    $q->set('meta_query', $mq);
    // Soonest deadline first, like the old site.
    $q->set('meta_key', '_fp_deadline_sort');
    $q->set('orderby', 'meta_value');
    $q->set('order', 'ASC');
});

/**
 * URL filter params → field names, per content type. Values are validated
 * against what exists so arbitrary input never reaches the query unchecked.
 */
function fp_filter_params(string $type): array {
    return $type === 'fp_job'
        ? ['type' => 'position_type', 'discipline' => 'discipline', 'location' => 'location', 'country' => 'country']
        : ['level' => 'level', 'region' => 'eligible_regions', 'country' => 'country'];
}

function fp_active_filters(string $type): array {
    $out = [];
    foreach (fp_filter_params($type) as $param => $field) {
        if (!isset($_GET[$param]) || $_GET[$param] === '') continue;
        $value = sanitize_text_field(wp_unslash($_GET[$param]));
        if ($param === 'location' && $value !== 'Remote') continue;
        $out[$field] = $value;
    }
    return $out;
}

/**
 * Options for each filter group, built from the currently open items (exactly
 * how the old site built its filter pills).
 */
function fp_filter_groups(string $type, string $view = ''): array {
    $args = [
        'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids',
        'meta_query' => [fp_open_clause()], 'suppress_filters' => false,
    ];
    if ($view === 'fellowships') $args['meta_query'][] = ['key' => fp_meta_key('position_type'), 'value' => 'fellow', 'compare' => 'LIKE'];
    if ($view === 'grants')      $args['meta_query'][] = ['key' => fp_meta_key('level'), 'value' => 'grant', 'compare' => 'LIKE'];
    $ids = get_posts($args);

    $collect = function (string $field) use ($ids) {
        $vals = [];
        foreach ($ids as $id) {
            $v = get_post_meta($id, fp_meta_key($field), true);
            foreach ((array) $v as $x) if ($x !== '' && $x !== null) $vals[$x] = true;
        }
        return array_keys($vals);
    };

    if ($type === 'fp_job') {
        return [
            ['param' => 'type', 'label' => 'Type', 'options' => $collect('position_type')],
            ['param' => 'discipline', 'label' => 'Field', 'options' => $collect('discipline')],
            ['param' => 'location', 'label' => 'Location', 'options' => ['Remote']],
            ['param' => 'country', 'label' => 'Country', 'options' => $collect('country')],
        ];
    }
    return [
        ['param' => 'level', 'label' => $view === 'grants' ? 'Type' : 'Level', 'options' => $collect('level')],
        ['param' => 'region', 'label' => 'Region', 'options' => $collect('eligible_regions')],
        ['param' => 'country', 'label' => 'Country', 'options' => $collect('country')],
    ];
}

/** Is this job/scholarship past its deadline? */
function fp_is_expired($post = null): bool {
    $post = get_post($post);
    if (!$post || !in_array($post->post_type, ['fp_job', 'fp_scholarship'], true)) return false;
    $sort = get_post_meta($post->ID, '_fp_deadline_sort', true) ?: FP_NO_DEADLINE;
    return $sort < current_time('mysql');
}

/** Old URL /contact is the contact form endpoint; /about and /submit-opportunity are normal Pages. */
add_filter('template_include', function ($template) {
    if (get_query_var('fp_search')) {
        $t = locate_template(['search.php']);
        if ($t) return $t;
    }
    return $template;
});
