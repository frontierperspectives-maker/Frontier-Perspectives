<?php
/** Article card. Args: image (bool), col (CSS classes), excerpt (chars). */
$post  = get_post($args['post'] ?? null);
$col   = $args['col'] ?? 'col-md-6 col-lg-4';
$len   = $args['excerpt'] ?? 120;
$cat   = get_the_terms($post, 'fp_category');
$cat   = ($cat && !is_wp_error($cat)) ? $cat[0]->name : '';
$img   = !empty($args['image']) ? fp_image_url($post, 'image', 'medium_large') : '';
?>
<div class="<?php echo esc_attr($col); ?>">
  <a href="<?php echo esc_url(get_permalink($post)); ?>" class="card h-100 border-0 shadow-sm text-decoration-none p-3">
    <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" class="rounded-3 mb-3" style="aspect-ratio:16/10;object-fit:cover;" alt="" loading="lazy"><?php endif; ?>
    <?php if ($cat): ?><span class="text-gold text-uppercase fw-bold" style="font-size:11px;"><?php echo esc_html($cat); ?></span><?php endif; ?>
    <h3 class="text-navy fs-5 mt-2"><?php echo esc_html(get_the_title($post)); ?></h3>
    <p class="text-muted2" style="font-size:14px;"><?php echo esc_html(fp_plain_text(fp_get('standfirst', $post), $len)); ?></p>
    <?php if (!empty($args['image'])): ?><p class="text-muted2 mb-0" style="font-size:12px;"><?php echo esc_html(fp_format_date(get_post_time('U', false, $post))); ?></p><?php endif; ?>
  </a>
</div>
