<?php
/**
 * Single source of truth for every custom field. Meta boxes, saving, REST
 * exposure and the importer are all driven from this array, so adding a field
 * is a one-line change.
 *
 * Field types: text, textarea, url, email, date, bool, select, list (array of
 * strings, one per line), image (URL from the media library), relation (array
 * of post IDs of `post_type`), number.
 */

defined('ABSPATH') || exit;

function fp_country_options(): array {
    return ['United States', 'United Kingdom', 'Canada', 'Australia', 'New Zealand', 'Europe', 'Asia', 'Middle East', 'Africa'];
}

/** Country name => URL slug, used for /jobs/<country> pages. Mirrors the old site exactly. */
function fp_country_slugs(): array {
    return [
        'United States' => 'usa', 'United Kingdom' => 'uk', 'Canada' => 'canada',
        'Australia' => 'australia', 'New Zealand' => 'new-zealand', 'Europe' => 'europe',
        'Asia' => 'asia', 'Middle East' => 'middle-east', 'Africa' => 'africa',
    ];
}

function fp_fields(?string $post_type = null): array {
    $seo = [
        'meta_title'       => ['label' => 'SEO title', 'type' => 'text', 'group' => 'SEO'],
        'meta_description' => ['label' => 'SEO description', 'type' => 'textarea', 'group' => 'SEO'],
        'og_image'         => ['label' => 'Social share image', 'type' => 'image', 'group' => 'SEO'],
    ];

    $all = [
        'fp_article' => [
            'standfirst'    => ['label' => 'Standfirst (summary)', 'type' => 'textarea', 'help' => 'Short summary shown under the headline and on cards.'],
            'author'        => ['label' => 'Author', 'type' => 'relation', 'post_type' => 'fp_author', 'single' => true],
            'image'         => ['label' => 'Main image', 'type' => 'image', 'help' => 'Or set a Featured Image — either works.'],
            'image_alt'     => ['label' => 'Image description (alt)', 'type' => 'text'],
            'image_caption' => ['label' => 'Caption', 'type' => 'text'],
            'image_credit'  => ['label' => 'Photo credit', 'type' => 'text'],
            'key_findings'  => ['label' => 'Key findings', 'type' => 'list', 'help' => 'One finding per line.'],
            'keywords'      => ['label' => 'Keywords', 'type' => 'list', 'help' => 'One per line. Used for SEO.'],
        ] + $seo,

        'fp_publication' => [
            'edition'           => ['label' => 'Edition', 'type' => 'text'],
            'publication_type'  => ['label' => 'Type', 'type' => 'select', 'options' => ['Journal', 'Report', 'Special Edition']],
            'cover_image'       => ['label' => 'Cover image', 'type' => 'image'],
            'pdf_url'           => ['label' => 'PDF', 'type' => 'image', 'help' => 'Upload or choose the PDF from the media library.'],
            'issn'              => ['label' => 'ISSN', 'type' => 'text'],
            'table_of_contents' => ['label' => 'Table of contents', 'type' => 'list', 'help' => 'One line per entry.'],
            'contributors'      => ['label' => 'Contributors', 'type' => 'relation', 'post_type' => 'fp_author'],
            'articles'          => ['label' => 'Articles in this publication', 'type' => 'relation', 'post_type' => 'fp_article'],
            'featured'          => ['label' => 'Feature on homepage', 'type' => 'bool'],
        ] + $seo,

        'fp_job' => [
            'institution'      => ['label' => 'Institution', 'type' => 'text', 'required' => true],
            'institution_logo' => ['label' => 'Institution logo', 'type' => 'image'],
            'location'         => ['label' => 'Location', 'type' => 'text'],
            'country'          => ['label' => 'Country / region', 'type' => 'select', 'options' => fp_country_options(), 'help' => 'Controls which Jobs country page this appears on.'],
            'remote_ok'        => ['label' => 'Remote OK', 'type' => 'bool'],
            'position_type'    => ['label' => 'Position type', 'type' => 'select', 'options' => [
                'Job', 'Faculty', 'Postdoc', 'PhD', 'Research Staff',
                'Research Fellowship', 'Graduate Fellowship', 'Postgraduate Fellowship',
                'Postdoctoral Fellowship', 'Visiting Fellowship', 'Professional Fellowship',
            ], 'help' => 'Any "… Fellowship" type also appears on the Fellowships page.'],
            'discipline'       => ['label' => 'Field / discipline', 'type' => 'text'],
            'salary'           => ['label' => 'Salary / stipend', 'type' => 'text'],
            'deadline'         => ['label' => 'Application deadline', 'type' => 'date', 'help' => 'Leave blank if there is no fixed date. Expired items are hidden from the public lists automatically.'],
            'deadline_note'    => ['label' => 'Deadline note', 'type' => 'text', 'help' => 'e.g. Open Until Filled, Rolling admissions'],
            'apply_url'        => ['label' => 'Application link', 'type' => 'url', 'required' => true],
            'keywords'         => ['label' => 'Keywords', 'type' => 'list'],
        ],

        'fp_scholarship' => [
            'funder'           => ['label' => 'Funder', 'type' => 'text', 'required' => true],
            'funder_logo'      => ['label' => 'Funder logo', 'type' => 'image'],
            'level'            => ['label' => 'Level / type', 'type' => 'select', 'options' => [
                'BS/MS', 'PhD', 'Post Doc', 'Research Grant', 'Student Grant', 'Faculty Grant', 'Early Career Researcher Grant',
            ], 'help' => 'Any "… Grant" level also appears on the Grants page.'],
            'country'          => ['label' => 'Country / region', 'type' => 'select', 'options' => fp_country_options()],
            'eligible_regions' => ['label' => 'Eligible regions', 'type' => 'list', 'help' => 'One per line, e.g. Worldwide'],
            'deadline'         => ['label' => 'Application deadline', 'type' => 'date', 'help' => 'Leave blank if there is no fixed date.'],
            'deadline_note'    => ['label' => 'Deadline note', 'type' => 'text'],
            'apply_url'        => ['label' => 'Application link', 'type' => 'url', 'required' => true],
            'keywords'         => ['label' => 'Keywords', 'type' => 'list'],
        ],

        'fp_author' => [
            'role'   => ['label' => 'Role', 'type' => 'text'],
            'avatar' => ['label' => 'Photo', 'type' => 'image'],
            'links'  => ['label' => 'Links', 'type' => 'list', 'help' => 'One URL per line.'],
        ],

        'fp_hero' => [
            'standfirst'  => ['label' => 'Standfirst', 'type' => 'textarea'],
            'image'       => ['label' => 'Image', 'type' => 'image'],
            'eyebrow'     => ['label' => 'Eyebrow label', 'type' => 'text', 'default' => 'Research Article'],
            'href'        => ['label' => 'Link target', 'type' => 'text', 'default' => '/', 'help' => 'e.g. /articles/my-article'],
            'linked_date' => ['label' => 'Date shown', 'type' => 'date'],
        ],
    ];

    return $post_type ? ($all[$post_type] ?? []) : $all;
}

/** Meta key for a field. Prefixed to avoid clashing with other plugins. */
function fp_meta_key(string $field): string {
    return '_fp_' . $field;
}

/** Read a field with sensible typing. */
function fp_get(string $field, $post = null) {
    $post = get_post($post);
    if (!$post) return null;
    $def = fp_fields($post->post_type)[$field] ?? ['type' => 'text'];
    $val = get_post_meta($post->ID, fp_meta_key($field), true);

    switch ($def['type']) {
        case 'list':
            return is_array($val) ? array_values(array_filter($val, 'strlen')) : [];
        case 'relation':
            $ids = is_array($val) ? array_map('intval', $val) : ($val ? [(int) $val] : []);
            return !empty($def['single']) ? ($ids[0] ?? 0) : $ids;
        case 'bool':
            return (bool) $val;
        default:
            return ($val === '' && isset($def['default'])) ? $def['default'] : $val;
    }
}

/** Sanitize a raw value for a field definition. */
function fp_sanitize_field(array $def, $raw) {
    switch ($def['type']) {
        case 'textarea':
            return sanitize_textarea_field((string) $raw);
        case 'url':
        case 'image':
            return esc_url_raw(trim((string) $raw));
        case 'email':
            return sanitize_email((string) $raw);
        case 'bool':
            return $raw ? '1' : '';
        case 'number':
            return (string) (int) $raw;
        case 'date':
            $raw = trim((string) $raw);
            if ($raw === '') return '';
            $ts = strtotime($raw);
            return $ts ? gmdate('Y-m-d', $ts) : '';
        case 'select':
            $raw = (string) $raw;
            return in_array($raw, $def['options'], true) ? $raw : '';
        case 'list':
            $items = is_array($raw) ? $raw : preg_split('/\r\n|\r|\n/', (string) $raw);
            return array_values(array_filter(array_map('sanitize_text_field', $items), 'strlen'));
        case 'relation':
            $ids = is_array($raw) ? $raw : [$raw];
            return array_values(array_filter(array_map('intval', $ids)));
        default:
            return sanitize_text_field((string) $raw);
    }
}
