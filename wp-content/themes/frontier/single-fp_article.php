<?php
get_header();
the_post();
$p        = get_post();
$cat      = get_the_terms($p, 'fp_category');
$cat      = ($cat && !is_wp_error($cat)) ? $cat[0] : null;
$author   = fp_get('author') ? get_post(fp_get('author')) : null;
$img      = fp_image_url($p);
$findings = fp_get('key_findings');
$tags     = get_the_terms($p, 'post_tag');
$more     = get_posts(['post_type' => 'fp_article', 'numberposts' => 3, 'post__not_in' => [$p->ID]]);
?>
<div class="container py-4" style="max-width:820px;">
  <?php fr_breadcrumbs(['Research Articles' => '/articles', get_the_title() => '']); ?>
  <?php if ($cat): ?><span class="text-gold text-uppercase fw-bold" style="font-size:12px;"><?php echo esc_html($cat->name); ?></span><?php endif; ?>
  <h1 class="text-navy fw-bold mt-2" style="font-size:2.1rem;"><?php the_title(); ?></h1>
  <p class="text-muted2 fs-5"><?php echo esc_html(fp_get('standfirst')); ?></p>
  <div class="d-flex align-items-center gap-2 text-muted2" style="font-size:13px;">
    <?php if ($author): ?><span><?php echo esc_html($author->post_title); ?></span> · <?php endif; ?>
    <span><?php echo esc_html(get_the_date('F j, Y')); ?></span> · <span><?php echo fp_reading_time($p->post_content); ?> min read</span>
  </div>
  <?php if ($img): ?>
    <img src="<?php echo esc_url($img); ?>" class="w-100 rounded-4 mt-4" style="aspect-ratio:16/9;object-fit:cover;" alt="<?php echo esc_attr(fp_get('image_alt')); ?>">
    <?php $cap = fp_get('image_caption'); $credit = fp_get('image_credit'); if ($cap || $credit): ?>
      <p class="text-muted2 mt-2" style="font-size:12px;"><?php echo esc_html($cap); ?><?php echo $credit ? ' · ' . esc_html($credit) : ''; ?></p>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($findings): ?>
    <div class="bg-navy text-white rounded-4 p-4 mt-4">
      <p class="text-gold text-uppercase fw-bold" style="font-size:12px;letter-spacing:.1em;">Key findings</p>
      <ul class="mt-2 mb-0"><?php foreach ($findings as $k): ?><li><?php echo esc_html($k); ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="prose mt-4"><?php echo fr_content(); ?></div>

  <?php if ($tags && !is_wp_error($tags)): ?>
    <div class="mt-4"><?php foreach ($tags as $t): ?><span class="pill me-1"><?php echo esc_html($t->name); ?></span><?php endforeach; ?></div>
  <?php endif; ?>

  <?php if ($more): ?>
    <div class="mt-5 pt-4 border-top">
      <h2 class="text-navy fs-4 mb-3">More research</h2>
      <div class="row g-4">
        <?php foreach ($more as $m): ?>
          <div class="col-md-4">
            <a href="<?php echo esc_url(get_permalink($m)); ?>" class="card h-100 border-0 shadow-sm text-decoration-none p-3">
              <h3 class="text-navy fs-6"><?php echo esc_html(get_the_title($m)); ?></h3>
              <p class="text-muted2 mb-0" style="font-size:13px;"><?php echo esc_html(fp_plain_text(fp_get('standfirst', $m), 90)); ?></p>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php get_footer();
