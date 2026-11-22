<?php
/** Schema-driven meta boxes: one "Details" box (plus "SEO") per content type. */
defined('ABSPATH') || exit;

add_action('add_meta_boxes', function (string $post_type) {
    $fields = fp_fields($post_type);
    if (!$fields) return;

    $groups = [];
    foreach ($fields as $name => $def) $groups[$def['group'] ?? 'Details'][$name] = $def;

    foreach ($groups as $group => $group_fields) {
        add_meta_box(
            'fp_box_' . sanitize_key($group),
            $group,
            'fp_render_meta_box',
            $post_type,
            $group === 'SEO' ? 'side' : 'normal',
            $group === 'SEO' ? 'low' : 'high',
            ['fields' => $group_fields]
        );
    }
});

function fp_render_meta_box(WP_Post $post, array $box): void {
    wp_nonce_field('fp_save_meta', 'fp_meta_nonce');
    echo '<div class="fp-fields">';
    foreach ($box['args']['fields'] as $name => $def) {
        $id    = 'fp_' . $name;
        $input = 'fp[' . $name . ']';
        $val   = fp_get($name, $post);
        $req   = !empty($def['required']) ? ' required' : '';

        echo '<p class="fp-field fp-field-' . esc_attr($def['type']) . '">';
        if ($def['type'] !== 'bool') {
            echo '<label for="' . esc_attr($id) . '"><strong>' . esc_html($def['label']) . '</strong></label><br>';
        }

        switch ($def['type']) {
            case 'textarea':
                printf('<textarea id="%s" name="%s" rows="3" class="widefat"%s>%s</textarea>', esc_attr($id), esc_attr($input), $req, esc_textarea((string) $val));
                break;
            case 'list':
                printf('<textarea id="%s" name="%s" rows="4" class="widefat">%s</textarea>', esc_attr($id), esc_attr($input), esc_textarea(implode("\n", (array) $val)));
                break;
            case 'bool':
                printf('<label><input type="checkbox" id="%s" name="%s" value="1"%s> <strong>%s</strong></label>', esc_attr($id), esc_attr($input), checked($val, true, false), esc_html($def['label']));
                break;
            case 'select':
                printf('<select id="%s" name="%s" class="widefat"><option value="">— Select —</option>', esc_attr($id), esc_attr($input));
                foreach ($def['options'] as $opt) printf('<option value="%1$s"%2$s>%1$s</option>', esc_attr($opt), selected($val, $opt, false));
                echo '</select>';
                break;
            case 'date':
                printf('<input type="date" id="%s" name="%s" value="%s">', esc_attr($id), esc_attr($input), esc_attr((string) $val));
                break;
            case 'url':
                printf('<input type="url" id="%s" name="%s" value="%s" class="widefat" placeholder="https://…"%s>', esc_attr($id), esc_attr($input), esc_attr((string) $val), $req);
                break;
            case 'image':
                printf(
                    '<span class="fp-media"><input type="url" id="%1$s" name="%2$s" value="%3$s" class="widefat fp-media-url"> <button type="button" class="button fp-media-pick" data-target="%1$s">Choose / upload</button></span>',
                    esc_attr($id), esc_attr($input), esc_attr((string) $val)
                );
                if ($val && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', (string) $val)) {
                    printf('<br><img src="%s" alt="" style="max-height:80px;margin-top:6px;border-radius:6px">', esc_url((string) $val));
                }
                break;
            case 'relation':
                $choices = get_posts(['post_type' => $def['post_type'], 'numberposts' => 500, 'post_status' => ['publish', 'draft', 'future'], 'orderby' => 'title', 'order' => 'ASC']);
                $selected = (array) $val;
                if (!empty($def['single'])) {
                    printf('<select id="%s" name="%s" class="widefat"><option value="">— None —</option>', esc_attr($id), esc_attr($input));
                    foreach ($choices as $c) printf('<option value="%d"%s>%s</option>', $c->ID, selected(in_array($c->ID, $selected, true), true, false), esc_html($c->post_title));
                    echo '</select>';
                } else {
                    printf('<select id="%s" name="%s[]" class="widefat" multiple size="%d">', esc_attr($id), esc_attr($input), min(8, max(3, count($choices))));
                    foreach ($choices as $c) printf('<option value="%d"%s>%s</option>', $c->ID, selected(in_array($c->ID, $selected, true), true, false), esc_html($c->post_title));
                    echo '</select><br><small>Hold Ctrl / Cmd to select several.</small>';
                }
                break;
            default:
                printf('<input type="text" id="%s" name="%s" value="%s" class="widefat"%s>', esc_attr($id), esc_attr($input), esc_attr((string) $val), $req);
        }

        if (!empty($def['help'])) echo '<br><small class="description">' . esc_html($def['help']) . '</small>';
        echo '</p>';
    }
    echo '</div>';
}

add_action('save_post', function (int $post_id, WP_Post $post) {
    if (!isset($_POST['fp_meta_nonce']) || !wp_verify_nonce($_POST['fp_meta_nonce'], 'fp_save_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $input = wp_unslash($_POST['fp'] ?? []);
    foreach (fp_fields($post->post_type) as $name => $def) {
        // Unchecked checkboxes and empty multi-selects are absent from POST.
        $raw = $input[$name] ?? ($def['type'] === 'relation' ? [] : '');
        $clean = fp_sanitize_field($def, $raw);
        if ($clean === '' || $clean === []) {
            delete_post_meta($post_id, fp_meta_key($name));
        } else {
            update_post_meta($post_id, fp_meta_key($name), $clean);
        }
    }
}, 10, 2);

// Media library picker for image/PDF fields.
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', <<<'JS'
jQuery(function ($) {
  $(document).on('click', '.fp-media-pick', function (e) {
    e.preventDefault();
    var target = $('#' + $(this).data('target'));
    var frame = wp.media({ title: 'Choose file', multiple: false });
    frame.on('select', function () { target.val(frame.state().get('selection').first().toJSON().url).trigger('change'); });
    frame.open();
  });
});
JS);
});
