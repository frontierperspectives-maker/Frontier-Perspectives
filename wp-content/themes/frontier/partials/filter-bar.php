<?php
/**
 * Filter pills. Args: base_path ("/jobs"), groups (from fp_filter_groups()).
 * Current values are read from the query string, so links stay shareable.
 */
$base    = $args['base_path'] ?? '/';
$groups  = $args['groups'] ?? [];
$current = [];
foreach ($groups as $g) {
    if (isset($_GET[$g['param']]) && $_GET[$g['param']] !== '') {
        $current[$g['param']] = sanitize_text_field(wp_unslash($_GET[$g['param']]));
    }
}
$link = function (array $q) use ($base) {
    return fr_url($base) . ($q ? '?' . http_build_query($q, '', '&amp;', PHP_QUERY_RFC3986) : '');
};
?>
<div class="mb-4">
  <?php foreach ($groups as $g): if (!$g['options']) continue; ?>
    <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
      <span class="text-muted2 fw-bold text-uppercase me-2" style="font-size:11px;letter-spacing:.08em;width:80px;"><?php echo esc_html($g['label']); ?></span>
      <?php $q = $current; unset($q[$g['param']]); ?>
      <a href="<?php echo $link($q); ?>" rel="nofollow" class="filter-pill<?php echo empty($current[$g['param']]) ? ' active' : ''; ?>">All</a>
      <?php foreach ($g['options'] as $opt): $q2 = $current; $q2[$g['param']] = $opt; ?>
        <a href="<?php echo $link($q2); ?>" rel="nofollow" class="filter-pill<?php echo (($current[$g['param']] ?? '') === $opt) ? ' active' : ''; ?>"><?php echo esc_html($opt); ?></a>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>
