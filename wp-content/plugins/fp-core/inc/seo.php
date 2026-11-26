<?php
/**
 * SEO: titles and descriptions identical to the old site, canonical URLs,
 * crawl-trap protection for filter combinations, H1 hygiene, and structured
 * data (JobPosting for Google for Jobs, Article, Organization, Breadcrumbs).
 */
defined('ABSPATH') || exit;

/** Title + description for the current request, mirroring the old site's wording. */
function fp_seo_meta(): array {
    $site = get_bloginfo('name');
    if (is_front_page()) return ["$site — " . get_bloginfo('description'), fp_setting('footer_mission')];

    $country_slug = get_query_var('fp_country');
    $country = $country_slug ? array_search($country_slug, fp_country_slugs(), true) : '';
    $view = get_query_var('fp_view');

    if (is_post_type_archive('fp_job')) {
        if ($country) return ["Jobs in $country — Academic Jobs & Fellowships", "Academic jobs and fellowships in $country, sorted by deadline."];
        if ($view === 'fellowships') return ['Fellowships', 'Research, graduate, postgraduate, postdoctoral, visiting, and professional fellowships.'];
        return ['Academic Jobs & Fellowships', 'Faculty positions, postdocs, research fellowships, and PhD opportunities.'];
    }
    if (is_post_type_archive('fp_scholarship')) {
        if ($country) return ["Scholarships in $country — Scholarships & Funding", "Scholarships and funding opportunities in $country, sorted by deadline."];
        if ($view === 'grants') return ['Grants', 'Research, student, faculty, and early career researcher grants.'];
        return ['Scholarships & Funding', 'Scholarships, grants, and funding opportunities for students and researchers worldwide.'];
    }
    if (is_post_type_archive('fp_article')) return ['Research Articles', 'Original, data-driven analysis on global affairs, the economy, climate, and society.'];
    if (is_post_type_archive('fp_publication')) return ['Publications', 'Flagship journals and annual reports that bring together leading contributors around a single theme.'];
    if (is_tax('fp_category')) {
        $t = get_queried_object();
        return [$t->name . ' — Research Articles', $t->description ?: "Research articles on {$t->name}."];
    }
    if (get_query_var('fp_search') || is_search()) {
        $q = get_search_query();
        return [$q !== '' ? "Search — $q" : 'Search', 'Search articles, publications, jobs, and scholarships.'];
    }
    if (is_page('about')) return ['About Us', 'We are an independent research platform committed to evidence, ideas, and impact.'];
    if (is_page('submit-opportunity')) return ['Submit an Opportunity', "Let us know about a job, fellowship, scholarship, or grant to feature on $site."];

    if (is_singular()) {
        $p = get_queried_object();
        $custom_title = fp_get('meta_title', $p);
        $custom_desc  = fp_get('meta_description', $p);

        if ($p->post_type === 'fp_job' || $p->post_type === 'fp_scholarship') {
            [$title, $desc] = fp_listing_seo($p);
            return [$custom_title ?: $title, $custom_desc ?: $desc];
        }
        $desc = $custom_desc ?: fp_get('standfirst', $p) ?: ($p->post_excerpt ?: $p->post_content);
        return [$custom_title ?: $p->post_title, $desc];
    }
    if (is_404()) return ['Page not found', ''];
    return ['', ''];
}

/**
 * Google title + description for a job or scholarship, built from its fields so every
 * listing is search-friendly without extra typing:
 *   "Medical Lab Scientist (Non-CA) — Holy Cross Hospital, Lehi"
 *   "Medical Lab Scientist (Non-CA) at Holy Cross Hospital in Lehi, Utah, USA. $31.63–$45.88 per hour. Rolling applications. See requirements and how to apply."
 */
function fp_listing_seo(WP_Post $p): array {
    $is_job = $p->post_type === 'fp_job';
    $org    = trim((string) fp_get($is_job ? 'institution' : 'funder', $p));
    $loc    = trim((string) ($is_job ? fp_get('location', $p) : ''));
    $country = trim((string) fp_get('country', $p));
    $city   = trim(explode(',', $loc ?: $country)[0]);

    $title = $p->post_title . ($org ? " — $org" : '');
    if ($city && stripos($p->post_title . ' ' . $org, $city) === false) $title .= ", $city";

    $deadline = fp_get('deadline', $p); $note = trim((string) fp_get('deadline_note', $p));
    if ($note && mb_strlen($note) > 60) {           // keep the first clause, cut on a word
        $note = preg_split('/[,.;:]/u', $note)[0];
        if (mb_strlen($note) > 60) { $note = mb_substr($note, 0, 60); $note = mb_substr($note, 0, (int) mb_strrpos($note, ' ')); }
    }
    $when = $note ? rtrim($note, " .,;:") : ($deadline ? 'Apply by ' . date_i18n('j F Y', strtotime($deadline)) : '');
    $bits = [];
    if ($is_job) {
        $bits[] = $p->post_title . ($org ? " at $org" : '') . ($loc ? " in $loc" : ($country ? " in $country" : '')) . '.';
        if ($s = trim((string) fp_get('salary', $p))) $bits[] = rtrim($s, '.') . '.';
    } else {
        $level = trim((string) fp_get('level', $p));
        $bits[] = $p->post_title . ($org ? " by $org" : '') . '.';
        if ($level || $country) $bits[] = trim(implode(' · ', array_filter([$level, $country]))) . '.';
    }
    if ($when) $bits[] = "$when.";
    $bits[] = $is_job ? 'See requirements and how to apply.' : 'See eligibility, benefits and how to apply.';
    return [$title, implode(' ', $bits)];
}

add_filter('pre_get_document_title', function ($title) {
    [$t] = fp_seo_meta();
    if ($t === '') return $title;
    $paged = (int) get_query_var('paged');
    return $paged > 1 ? "$t — Page $paged" : $t;
}, 20);

/** True when the URL carries list filters (?type=, ?country=, …) — near-duplicate content. */
function fp_is_filtered_view(): bool {
    foreach (['type', 'discipline', 'location', 'country', 'level', 'region', 'category'] as $k) {
        if (!empty($_GET[$k])) return true;
    }
    return false;
}

/** Canonical URL for the current page (singular pages are handled by WordPress core). */
function fp_canonical_url(): string {
    if (is_singular()) return (string) wp_get_canonical_url();
    if (is_search() || is_404()) return '';
    $path = strtok(home_url(add_query_arg([])), '?');
    if (fp_is_filtered_view()) $path = preg_replace('~/page/\d+$~', '', $path); // filtered → base list
    return $path;
}

add_action('wp_head', function () {
    $canon = fp_canonical_url();
    if ($canon && !is_singular()) printf("<link rel=\"canonical\" href=\"%s\">\n", esc_url($canon));
}, 2);

/** Filter combinations and search results: keep out of the index, but let links be followed. */
add_filter('wp_robots', function (array $robots) {
    if (fp_is_filtered_view() || is_search() || get_query_var('fp_search')) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
        unset($robots['max-image-preview']);
    }
    return $robots;
});

/** Imported descriptions often contain their own <h1>; the page title is the only H1. */
add_filter('the_content', function ($html) {
    if (!in_array(get_post_type(), ['fp_job', 'fp_scholarship', 'fp_article', 'fp_publication', 'page'], true)) return $html;
    return preg_replace(['~<h1(\s|>)~i', '~</h1>~i'], ['<h2$1', '</h2>'], $html);
}, 5);

/* ---------- Structured data ---------- */

function fp_jsonld(array $data): void {
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}

function fp_org_schema(): array {
    $org = ['@type' => 'Organization', '@id' => home_url('/#org'), 'name' => get_bloginfo('name'), 'url' => home_url('/')];
    if ($logo = fp_setting('logo')) $org['logo'] = $logo;
    $same = array_column(fp_social_links(), 'url');
    if ($same) $org['sameAs'] = $same;
    return $org;
}

/** Best-effort split of "City, Region, Country" into a PostalAddress. */
function fp_address(WP_Post $p): array {
    $addr = ['@type' => 'PostalAddress'];
    $loc = trim((string) fp_get('location', $p));
    $country = (string) fp_get('country', $p);
    $iso = ['United States' => 'US', 'United Kingdom' => 'GB', 'Canada' => 'CA', 'Australia' => 'AU', 'New Zealand' => 'NZ'][$country] ?? '';
    if ($loc) {
        $parts = array_map('trim', explode(',', $loc));
        $addr['addressLocality'] = $parts[0];
        if (count($parts) > 2) $addr['addressRegion'] = $parts[1];
    }
    $addr['addressCountry'] = $iso ?: ($country ?: 'Worldwide');
    return $addr;
}

/**
 * Turn free-text salary ("$31.63–$45.88 per hour", "AUD $80,000–$120,000 per year",
 * "£32,073 - £39,043 per annum", "USD 25.53-25.80") into Google's MonetaryAmount.
 * Returns null when no number is found. Never throws.
 */
function fp_parse_salary(string $text, string $country = ''): ?array {
    $t = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $t = str_replace(["\u{2013}", "\u{2014}", "\u{2212}", ' to '], '-', $t);

    // Currency: explicit code first, then symbol, then country.
    $currency = null;
    if (preg_match('/\b(USD|GBP|EUR|AUD|CAD|NZD|PKR|INR|AED|SAR|CHF|SGD|JPY|CNY|ZAR|NGN|KES)\b/i', $t, $m)) $currency = strtoupper($m[1]);
    elseif (str_contains($t, '£')) $currency = 'GBP';
    elseif (str_contains($t, '€')) $currency = 'EUR';
    elseif (str_contains($t, '$')) $currency = ['Canada' => 'CAD', 'Australia' => 'AUD', 'New Zealand' => 'NZD'][$country] ?? 'USD';
    if (!$currency) return null;

    // Numbers (allow 1,234.56 and 45k).
    preg_match_all('/(\d{1,3}(?:,\d{3})+|\d+)(?:\.(\d+))?\s*(k)?\b/i', $t, $mm, PREG_SET_ORDER);
    $nums = [];
    foreach ($mm as $m) {
        $n = (float) (str_replace(',', '', $m[1]) . (isset($m[2]) && $m[2] !== '' ? '.' . $m[2] : ''));
        if (!empty($m[3])) $n *= 1000;
        if ($n > 0) $nums[] = $n;
    }
    if (!$nums) return null;
    $min = min(array_slice($nums, 0, 2)); $max = max(array_slice($nums, 0, 2));

    // Period: stated, or inferred from size.
    $lt = strtolower($t);
    $unit = match (true) {
        (bool) preg_match('/\b(hour|hr|hourly)\b|\/\s*h\b/', $lt) => 'HOUR',
        (bool) preg_match('/\b(day|daily)\b/', $lt)                => 'DAY',
        (bool) preg_match('/\b(week|weekly|wk)\b/', $lt)           => 'WEEK',
        (bool) preg_match('/\b(month|monthly|pcm)\b/', $lt)        => 'MONTH',
        (bool) preg_match('/\b(year|yearly|annum|annual|annually|p\.?a\.?|yr)\b/', $lt) => 'YEAR',
        $max < 300   => 'HOUR',
        $max < 20000 => 'MONTH',
        default      => 'YEAR',
    };

    $value = ['@type' => 'QuantitativeValue', 'unitText' => $unit];
    if ($min == $max) $value['value'] = $min; else { $value['minValue'] = $min; $value['maxValue'] = $max; }
    return ['@type' => 'MonetaryAmount', 'currency' => $currency, 'value' => $value];
}

add_action('wp_head', function () {
    if (is_front_page()) {
        fp_jsonld(['@context' => 'https://schema.org', '@graph' => [
            fp_org_schema(),
            ['@type' => 'WebSite', '@id' => home_url('/#website'), 'url' => home_url('/'), 'name' => get_bloginfo('name'),
             'publisher' => ['@id' => home_url('/#org')],
             'potentialAction' => ['@type' => 'SearchAction', 'target' => home_url('/search') . '?q={search_term_string}', 'query-input' => 'required name=search_term_string']],
        ]]);
        return;
    }
    if (!is_singular(['fp_job', 'fp_scholarship', 'fp_article', 'fp_publication'])) return;

    $p = get_queried_object();
    $crumbs = [
        'fp_job' => ['Jobs & Fellowships', '/jobs'], 'fp_scholarship' => ['Scholarships & Funding', '/scholarships'],
        'fp_article' => ['Research Articles', '/articles'], 'fp_publication' => ['Publications', '/publications'],
    ][$p->post_type];
    $graph = [[
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $crumbs[0], 'item' => home_url($crumbs[1])],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $p->post_title, 'item' => get_permalink($p)],
        ],
    ]];

    if ($p->post_type === 'fp_job') {
        // Google for Jobs. Expired listings keep validThrough in the past so Google drops them.
        $job = [
            '@type' => 'JobPosting',
            'title' => $p->post_title,
            'description' => wp_kses_post(apply_filters('the_content', $p->post_content)),
            'datePosted' => get_post_time('c', true, $p),
            'hiringOrganization' => array_filter(['@type' => 'Organization', 'name' => fp_get('institution', $p), 'logo' => fp_get('institution_logo', $p)]),
            'directApply' => false,
            'url' => get_permalink($p),
            'identifier' => ['@type' => 'PropertyValue', 'name' => get_bloginfo('name'), 'value' => (string) $p->ID],
        ];
        if ($d = fp_get('deadline', $p)) $job['validThrough'] = $d . 'T23:59:59' . wp_date('P');
        $type = (string) fp_get('position_type', $p);
        $job['employmentType'] = match (true) {
            stripos($type, 'fellow') !== false, stripos($type, 'postdoc') !== false, $type === 'PhD' => 'OTHER',
            default => 'FULL_TIME',
        };
        if (fp_get('remote_ok', $p)) {
            $job['jobLocationType'] = 'TELECOMMUTE';
            $job['applicantLocationRequirements'] = ['@type' => 'Country', 'name' => fp_get('country', $p) ?: 'Worldwide'];
        }
        if (fp_get('location', $p) || fp_get('country', $p)) {
            $job['jobLocation'] = ['@type' => 'Place', 'address' => fp_address($p)];
        }
        if ($salary = fp_parse_salary((string) fp_get('salary', $p), (string) fp_get('country', $p))) {
            $job['baseSalary'] = $salary;
        }
        $graph[] = $job;
    } elseif ($p->post_type === 'fp_article') {
        $author = fp_get('author', $p) ? get_post(fp_get('author', $p)) : null;
        $graph[] = array_filter([
            '@type' => 'Article',
            'headline' => mb_substr($p->post_title, 0, 110),
            'description' => fp_plain_text(fp_get('standfirst', $p), 300),
            'image' => fp_image_url($p) ?: null,
            'datePublished' => get_post_time('c', true, $p),
            'dateModified' => get_post_modified_time('c', true, $p),
            'author' => $author ? ['@type' => 'Person', 'name' => $author->post_title] : ['@id' => home_url('/#org')],
            'publisher' => fp_org_schema(),
            'mainEntityOfPage' => get_permalink($p),
        ]);
    }
    fp_jsonld(['@context' => 'https://schema.org', '@graph' => $graph]);
}, 6);

/* ---------- Sitemap & feeds ---------- */

// WordPress core sitemap (/wp-sitemap.xml): no user sitemap (it would expose usernames), no tag pages.
add_filter('wp_sitemaps_add_provider', fn($provider, $name) => $name === 'users' ? false : $provider, 10, 2);
add_filter('wp_sitemaps_taxonomies', function ($t) { unset($t['post_tag'], $t['category'], $t['post_format']); return $t; });
add_filter('wp_sitemaps_post_types', function ($t) { unset($t['post'], $t['attachment']); return $t; });

// /sitemap.xml (common guess by bots and tools) → the real one.
add_action('init', fn() => add_rewrite_rule('^sitemap\.xml$', 'index.php?sitemap=index', 'top'));

// Main RSS feed (/feed) lists research articles.
add_action('pre_get_posts', function (WP_Query $q) {
    if ($q->is_main_query() && $q->is_feed() && !$q->get('post_type')) $q->set('post_type', ['fp_article']);
});
