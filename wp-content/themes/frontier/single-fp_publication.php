<?php
get_header();
the_post();
$cover    = fp_image_url(null, 'cover_image');
$pdf      = fp_get('pdf_url');
$toc      = fp_get('table_of_contents');
$people   = array_filter(array_map('get_post', fp_get('contributors')));
$articles = array_filter(array_map('get_post', fp_get('articles')), fn($a) => $a && $a->post_status === 'publish');
?>
<div class="container py-4">
  <?php fr_breadcrumbs(['Publications' => '/publications', get_the_title() => '']); ?>
  <div class="row g-5">
    <div class="col-lg-4">
      <?php if ($cover): ?><img src="<?php echo esc_url($cover); ?>" class="w-100 rounded-4 shadow" alt=""><?php endif; ?>
      <?php if ($pdf): ?><a href="<?php echo esc_url($pdf); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold rounded-pill w-100 mt-3">Download PDF</a><?php endif; ?>
      <?php if ($issn = fp_get('issn')): ?><p class="text-muted2 mt-3 text-center" style="font-size:12px;">ISSN <?php echo esc_html($issn); ?></p><?php endif; ?>
    </div>
    <div class="col-lg-8">
      <?php if ($t = fp_get('publication_type')): ?><span class="pill"><?php echo esc_html($t); ?></span><?php endif; ?>
      <h1 class="text-navy fw-bold mt-2" style="font-size:2rem;"><?php the_title(); ?></h1>
      <?php if ($e = fp_get('edition')): ?><p class="fw-semibold" style="color:var(--navy-light);"><?php echo esc_html($e); ?></p><?php endif; ?>
      <div class="prose mt-3"><?php echo fr_content(); ?></div>
      <?php if ($toc): ?>
        <div class="mt-4"><h2 class="fs-5 text-navy">Table of contents</h2>
          <ol class="mt-2"><?php foreach ($toc as $line): ?><li><?php echo esc_html($line); ?></li><?php endforeach; ?></ol></div>
      <?php endif; ?>
      <?php if ($articles): ?>
        <div class="mt-4"><h2 class="fs-5 text-navy">Articles in this publication</h2>
          <ul class="mt-2"><?php foreach ($articles as $a): ?><li><a href="<?php echo esc_url(get_permalink($a)); ?>"><?php echo esc_html(get_the_title($a)); ?></a></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <?php if ($people): ?>
        <div class="mt-4"><h2 class="fs-5 text-navy">Contributors</h2>
          <div class="d-flex flex-wrap gap-4 mt-2">
            <?php foreach ($people as $c): $role = fp_get('role', $c); ?>
              <div style="font-size:14px;"><strong class="text-navy"><?php echo esc_html($c->post_title); ?></strong><?php if ($role): ?><br><span class="text-muted2" style="font-size:12px;"><?php echo esc_html($role); ?></span><?php endif; ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php get_footer();
