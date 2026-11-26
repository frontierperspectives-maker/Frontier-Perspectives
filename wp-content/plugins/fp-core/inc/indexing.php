<?php
/**
 * Google Indexing API — the official fast lane for job pages.
 *
 * Whenever a job is published/updated Google is told "URL_UPDATED"; when it is
 * unpublished or trashed, "URL_DELETED". Once a day, jobs whose deadline passed
 * yesterday are re-sent so Google sees the expired date and drops them from the
 * jobs panel. (Google accepts this API for job-posting pages only, so scholarships
 * and articles rely on the normal sitemap.)
 *
 * Setup: Site Settings → Google Indexing → paste the service-account JSON key.
 * The key is stored in a non-autoloaded option and never displayed again.
 * Default Google quota: 200 notifications per day.
 */
defined('ABSPATH') || exit;

const FP_IDX_OPTION = 'fp_indexing_key';   // service-account JSON (private)
const FP_IDX_LOG    = 'fp_indexing_log';   // last 30 results
const FP_IDX_TYPES  = ['fp_job'];

function fp_idx_key(): ?array {
    $k = json_decode((string) get_option(FP_IDX_OPTION, ''), true);
    return (is_array($k) && !empty($k['client_email']) && !empty($k['private_key'])) ? $k : null;
}

function fp_idx_b64url(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }

/** OAuth access token from the service account (cached ~50 minutes). */
function fp_idx_token(): ?string {
    if ($t = get_transient('fp_indexing_token')) return $t;
    $k = fp_idx_key(); if (!$k) return null;
    $now = time();
    $head = fp_idx_b64url(wp_json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claim = fp_idx_b64url(wp_json_encode([
        'iss' => $k['client_email'], 'scope' => 'https://www.googleapis.com/auth/indexing',
        'aud' => $k['token_uri'] ?? 'https://oauth2.googleapis.com/token', 'iat' => $now, 'exp' => $now + 3600,
    ]));
    $sig = '';
    if (!openssl_sign("$head.$claim", $sig, $k['private_key'], 'sha256WithRSAEncryption')) {
        fp_idx_log('—', 'key', 'Could not sign with the private key (is the JSON complete?)'); return null;
    }
    $r = wp_remote_post($k['token_uri'] ?? 'https://oauth2.googleapis.com/token', ['timeout' => 15, 'body' => [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => "$head.$claim." . fp_idx_b64url($sig),
    ]]);
    $body = is_wp_error($r) ? [] : json_decode(wp_remote_retrieve_body($r), true);
    if (empty($body['access_token'])) {
        fp_idx_log('—', 'token', is_wp_error($r) ? $r->get_error_message() : ($body['error_description'] ?? $body['error'] ?? 'no token'));
        return null;
    }
    set_transient('fp_indexing_token', $body['access_token'], 50 * MINUTE_IN_SECONDS);
    return $body['access_token'];
}

/** Send one notification. Returns true on success. */
function fp_idx_notify(string $url, string $type = 'URL_UPDATED'): bool {
    $token = fp_idx_token(); if (!$token) return false;
    $r = wp_remote_post('https://indexing.googleapis.com/v3/urlNotifications:publish', [
        'timeout' => 15,
        'headers' => ['Authorization' => "Bearer $token", 'Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['url' => $url, 'type' => $type]),
    ]);
    $code = is_wp_error($r) ? 0 : (int) wp_remote_retrieve_response_code($r);
    $ok = $code === 200;
    $msg = $ok ? 'OK' : (is_wp_error($r) ? $r->get_error_message() : ((json_decode(wp_remote_retrieve_body($r), true)['error']['message'] ?? "HTTP $code")));
    fp_idx_log($url, $type, $msg);
    return $ok;
}

function fp_idx_log(string $url, string $type, string $msg): void {
    $log = (array) get_option(FP_IDX_LOG, []);
    array_unshift($log, ['time' => current_time('mysql'), 'url' => $url, 'type' => $type, 'result' => mb_substr($msg, 0, 200)]);
    update_option(FP_IDX_LOG, array_slice($log, 0, 30), false);
}

/* ---------- Automatic notifications (queued so saving stays fast) ---------- */
add_action('transition_post_status', function ($new, $old, $post) {
    if (!fp_idx_key() || !in_array($post->post_type, FP_IDX_TYPES, true) || wp_is_post_revision($post) || wp_is_post_autosave($post)) return;
    if ($new === 'publish') {
        $type = 'URL_UPDATED';
    } elseif ($old === 'publish') {
        $type = 'URL_DELETED';             // unpublished, trashed or made private
    } else {
        return;
    }
    $url = $type === 'URL_DELETED'
        ? home_url('/jobs/' . preg_replace('/__trashed$/', '', $post->post_name))
        : get_permalink($post);
    wp_schedule_single_event(time() + 5, 'fp_idx_send', [$url, $type]);
}, 10, 3);

add_action('fp_idx_send', fn($url, $type) => fp_idx_notify($url, $type), 10, 2);

// Daily: re-send jobs whose deadline passed yesterday, so Google drops them from the jobs panel.
add_action('init', function () {
    if (!wp_next_scheduled('fp_idx_daily')) wp_schedule_event(strtotime('tomorrow 01:15'), 'daily', 'fp_idx_daily');
});
add_action('fp_idx_daily', function () {
    if (!fp_idx_key()) return;
    $yesterday = wp_date('Y-m-d', strtotime('-1 day', current_time('timestamp')));
    $ids = get_posts(['post_type' => 'fp_job', 'post_status' => 'publish', 'numberposts' => 100, 'fields' => 'ids', 'fp_all' => true,
        'meta_query' => [['key' => fp_meta_key('deadline'), 'value' => $yesterday]]]);
    foreach ($ids as $id) fp_idx_notify(get_permalink($id), 'URL_UPDATED');
});

/* ---------- Settings page: Site Settings → Google Indexing ---------- */
add_action('admin_menu', function () {
    add_submenu_page('fp-settings', 'Google Indexing', 'Google Indexing', 'manage_options', 'fp-indexing', 'fp_idx_render_page');
}, 20);

function fp_idx_render_page(): void {
    if (!current_user_can('manage_options')) return;
    $notice = '';
    if (!empty($_POST['fp_idx_action']) && check_admin_referer('fp_idx')) {
        $action = sanitize_key($_POST['fp_idx_action']);
        if ($action === 'save') {
            $raw = trim(wp_unslash($_POST['fp_idx_json'] ?? ''));
            $k = json_decode($raw, true);
            if (!is_array($k) || ($k['type'] ?? '') !== 'service_account' || empty($k['client_email']) || empty($k['private_key'])) {
                $notice = '<div class="notice notice-error"><p>That doesn\'t look like a service-account JSON key. Paste the whole contents of the downloaded <code>.json</code> file.</p></div>';
            } else {
                update_option(FP_IDX_OPTION, wp_json_encode($k), false);
                delete_transient('fp_indexing_token');
                $ok = (bool) fp_idx_token();
                $notice = $ok ? '<div class="notice notice-success"><p>Key saved and accepted by Google.</p></div>'
                              : '<div class="notice notice-warning"><p>Key saved, but Google did not accept it yet — see the log below.</p></div>';
            }
        } elseif ($action === 'remove') {
            delete_option(FP_IDX_OPTION); delete_transient('fp_indexing_token');
            $notice = '<div class="notice notice-success"><p>Key removed. Automatic notifications are off.</p></div>';
        } elseif ($action === 'sendall' && fp_idx_key()) {
            $ids = get_posts(['post_type' => 'fp_job', 'post_status' => 'publish', 'numberposts' => 150, 'fields' => 'ids', 'meta_query' => [fp_open_clause()]]);
            $sent = 0; foreach ($ids as $id) $sent += fp_idx_notify(get_permalink($id)) ? 1 : 0;
            $notice = sprintf('<div class="notice notice-info"><p>Sent %d of %d open jobs to Google.</p></div>', $sent, count($ids));
        }
    }
    $k = fp_idx_key();
    $log = (array) get_option(FP_IDX_LOG, []);
    ?>
    <div class="wrap">
      <h1>Google Indexing</h1>
      <?php echo $notice; // built above from fixed strings ?>
      <p>When this is connected, Google is told within seconds whenever a job is published, changed or removed — so new jobs can appear in Google's job search within hours instead of days.</p>
      <h2>Status</h2>
      <?php if ($k): ?>
        <p>✅ <strong>Connected</strong> as <code><?php echo esc_html($k['client_email']); ?></code>.
           This address must be an <strong>Owner</strong> in Google Search Console (Settings → Users and permissions).</p>
        <form method="post" style="display:inline"><?php wp_nonce_field('fp_idx'); ?><input type="hidden" name="fp_idx_action" value="sendall">
          <?php submit_button('Send all open jobs to Google now', 'primary', 'submit', false); ?></form>
        <form method="post" style="display:inline;margin-left:8px"><?php wp_nonce_field('fp_idx'); ?><input type="hidden" name="fp_idx_action" value="remove">
          <?php submit_button('Remove key', 'secondary', 'submit', false, ['onclick' => "return confirm('Remove the Google key?')"]); ?></form>
        <p class="description">Google allows about 200 notifications per day. Use "Send all" once after connecting, not daily — new and changed jobs are sent automatically.</p>
      <?php else: ?>
        <p>❌ <strong>Not connected.</strong> Paste the service-account key below.</p>
        <form method="post"><?php wp_nonce_field('fp_idx'); ?><input type="hidden" name="fp_idx_action" value="save">
          <textarea name="fp_idx_json" rows="8" class="large-text code" placeholder='{ "type": "service_account", "project_id": "…", "private_key": "-----BEGIN PRIVATE KEY-----…", "client_email": "…@….iam.gserviceaccount.com", … }' autocomplete="off" spellcheck="false"></textarea>
          <p class="description">Open the downloaded <code>.json</code> file in a text editor, copy everything, paste it here. It is stored privately and never shown again.</p>
          <?php submit_button('Save key'); ?></form>
      <?php endif; ?>
      <h2>Recent activity</h2>
      <?php if (!$log): ?><p>Nothing sent yet.</p><?php else: ?>
        <table class="widefat striped"><thead><tr><th>Time</th><th>Type</th><th>Page</th><th>Result</th></tr></thead><tbody>
        <?php foreach ($log as $e): ?>
          <tr><td><?php echo esc_html($e['time']); ?></td><td><?php echo esc_html($e['type']); ?></td>
              <td><?php echo esc_html(str_replace(home_url(), '', $e['url'])); ?></td><td><?php echo esc_html($e['result']); ?></td></tr>
        <?php endforeach; ?></tbody></table>
      <?php endif; ?>
    </div>
    <?php
}
