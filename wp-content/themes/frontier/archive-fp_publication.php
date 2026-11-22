<?php get_header(); ?>
<div class="container py-4">
  <?php fr_page_head('Publications', 'Flagship journals and annual reports that bring together leading contributors around a single theme.'); ?>
  <?php if (have_posts()): ?>
    <div class="row g-4">
      <?php while (have_posts()): the_post(); $cover = fp_image_url(null, 'cover_image', 'medium_large'); ?>
        <div class="col-md-6 col-lg-4">
          <a href="<?php the_permalink(); ?>" class="card h-100 border-0 shadow-sm text-decoration-none p-3">
            <?php if ($cover): ?><img src="<?php echo esc_url($cover); ?>" class="rounded-3 mb-3" style="aspect-ratio:3/4;object-fit:cover;" alt="" loading="lazy"><?php endif; ?>
            <?php if ($t = fp_get('publication_type')): ?><span class="pill align-self-start"><?php echo esc_html($t); ?></span><?php endif; ?>
            <h3 class="text-navy fs-5 mt-2"><?php the_title(); ?></h3>
            <?php if ($e = fp_get('edition')): ?><p class="fw-semibold mb-0" style="font-size:13px;color:var(--navy-light);"><?php echo esc_html($e); ?></p><?php endif; ?>
            <p class="text-muted2" style="font-size:14px;"><?php echo esc_html(fp_plain_text(get_the_content(), 110)); ?></p>
          </a>
        </div>
      <?php endwhile; ?>
    </div>
    <?php fr_pagination(); ?>
  <?php else: ?>
    <div class="fr-empty rounded-4 p-5 text-center"><h3 class="text-navy">No publications yet</h3><p class="text-muted2">Check back soon.</p></div>
  <?php endif; ?>
</div>
<?php get_footer();
