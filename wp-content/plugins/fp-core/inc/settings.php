<?php
/**
 * Site Settings (singleton). Site name and tagline are WordPress's own
 * Settings → General values; the rest live in one `fp_settings` option.
 */
defined('ABSPATH') || exit;

function fp_settings_fields(): array {
    return [
        'logo'           => ['label' => 'Logo', 'type' => 'image', 'help' => 'Optional. Without one the "F" monogram is shown.'],
        'contact_email'  => ['label' => 'Contact email', 'type' => 'email', 'help' => 'Contact-form messages are emailed here (and always saved under Messages).'],
        'footer_mission' => ['label' => 'Footer mission statement', 'type' => 'textarea'],
        'social_links'   => ['label' => 'Social links', 'type' => 'list', 'help' => 'One per line as "Label | https://url", e.g. LinkedIn | https://linkedin.com/company/…'],
    ];
}

function fp_setting(string $key, $default = '') {
    if ($key === 'site_name') return get_bloginfo('name');
    if ($key === 'tagline')   return get_bloginfo('description');
    $all = get_option('fp_settings', []);
    return $all[$key] ?? $default;
}

/** Social links as [['label' => …, 'url' => …], …]. */
function fp_social_links(): array {
    $out = [];
    foreach ((array) fp_setting('social_links', []) as $line) {
        [$label, $url] = array_map('trim', array_pad(explode('|', $line, 2), 2, ''));
        if (!$url && filter_var($label, FILTER_VALIDATE_URL)) { $url = $label; $label = parse_url($url, PHP_URL_HOST); }
        if ($url) $out[] = ['label' => $label ?: $url, 'url' => $url];
    }
    return $out;
}

add_action('admin_menu', function () {
    add_menu_page('Site Settings', 'Site Settings', 'manage_options', 'fp-settings', 'fp_render_settings_page', 'dashicons-admin-settings', 80);
});

add_action('admin_init', function () {
    register_setting('fp_settings_group', 'fp_settings', [
        'type' => 'array',
        'sanitize_callback' => function ($input) {
            $out = [];
            foreach (fp_settings_fields() as $k => $def) $out[$k] = fp_sanitize_field($def, $input[$k] ?? '');
            return $out;
        },
    ]);
});

function fp_render_settings_page(): void {
    if (!current_user_can('manage_options')) return;
    wp_enqueue_media();
    $vals = get_option('fp_settings', []);
    ?>
    <div class="wrap">
      <h1>Site Settings</h1>
      <p>Site name and tagline are set under <a href="<?php echo esc_url(admin_url('options-general.php')); ?>">Settings → General</a>
        (currently <strong><?php echo esc_html(get_bloginfo('name')); ?></strong> — <em><?php echo esc_html(get_bloginfo('description')); ?></em>).</p>
      <form method="post" action="options.php">
        <?php settings_fields('fp_settings_group'); ?>
        <table class="form-table" role="presentation">
        <?php foreach (fp_settings_fields() as $k => $def):
            $v = $vals[$k] ?? ''; $name = "fp_settings[$k]"; $id = "fp_set_$k"; ?>
          <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($def['label']); ?></label></th>
            <td>
              <?php if ($def['type'] === 'textarea'): ?>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" rows="3" class="large-text"><?php echo esc_textarea($v); ?></textarea>
              <?php elseif ($def['type'] === 'list'): ?>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" rows="4" class="large-text"><?php echo esc_textarea(implode("\n", (array) $v)); ?></textarea>
              <?php elseif ($def['type'] === 'image'): ?>
                <input type="url" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($v); ?>" class="regular-text">
                <button type="button" class="button fp-media-pick" data-target="<?php echo esc_attr($id); ?>">Choose / upload</button>
              <?php else: ?>
                <input type="<?php echo $def['type'] === 'email' ? 'email' : 'text'; ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($v); ?>" class="regular-text">
              <?php endif; ?>
              <?php if (!empty($def['help'])): ?><p class="description"><?php echo esc_html($def['help']); ?></p><?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <script>
    jQuery(function ($) {
      $(document).on('click', '.fp-media-pick', function (e) {
        e.preventDefault();
        var t = $('#' + $(this).data('target')), f = wp.media({ title: 'Choose file', multiple: false });
        f.on('select', function () { t.val(f.state().get('selection').first().toJSON().url); });
        f.open();
      });
    });
    </script>
    <?php
}
