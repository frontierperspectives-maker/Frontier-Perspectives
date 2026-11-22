<?php
get_header();
the_post();
$p = get_post();
$deadline = fp_get('deadline'); $note = fp_get('deadline_note'); $regions = fp_get('eligible_regions');
?>
<div class="container py-4">
  <?php fr_breadcrumbs(['Scholarships & Funding' => '/scholarships', get_the_title() => '']); ?>
  <div class="row g-5">
    <div class="col-lg-8">
      <?php if (fp_is_expired()): ?>
        <div class="alert alert-warning rounded-4">This opportunity has closed. <a href="<?php echo fr_url('/scholarships'); ?>">See open scholarships →</a></div>
      <?php endif; ?>
      <?php if ($l = fp_get('level')): ?><span class="pill" style="background:rgba(242,107,58,.18);color:var(--gold);"><?php echo esc_html($l); ?></span><?php endif; ?>
      <h1 class="text-navy fw-bold mt-2" style="font-size:2.2rem;"><?php the_title(); ?></h1>
      <p class="text-muted2 fs-5"><?php echo esc_html(fp_get('funder')); ?></p>

      <dl class="row bg-mist rounded-3 p-4 mx-0 mt-3">
        <?php if ($c = fp_get('country')): ?><div class="col-6"><dt class="text-muted2 text-uppercase fw-bold" style="font-size:11px;">Country</dt><dd class="text-navy fw-semibold" style="font-size:14px;"><?php echo esc_html($c); ?></dd></div><?php endif; ?>
        <?php if ($regions): ?><div class="col-6"><dt class="text-muted2 text-uppercase fw-bold" style="font-size:11px;">Eligible regions</dt><dd class="text-navy fw-semibold" style="font-size:14px;"><?php echo esc_html(implode(', ', $regions)); ?></dd></div><?php endif; ?>
        <div class="col-12"><dt class="text-muted2 text-uppercase fw-bold" style="font-size:11px;">Application deadline</dt>
          <dd><?php echo fr_deadline_html($deadline, $note, false); ?></dd></div>
      </dl>

      <div class="prose mt-4"><?php echo fr_content(); ?></div>

      <div class="mt-4 pt-4 border-top d-flex flex-wrap align-items-center gap-3">
        <?php if ($url = fp_get('apply_url')): ?><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold rounded-pill">Apply / Official page</a><?php endif; ?>
        <p class="text-muted2 mb-0" style="font-size:12px;">
          <?php echo $note ? 'Applications: ' . esc_html($note) : ($deadline ? 'Applications close ' . esc_html(fp_format_date_long($deadline)) : 'Deadline not specified'); ?> · You will be taken to the funder's website.
        </p>
      </div>
    </div>
    <div class="col-lg-4">
      <h2 class="text-navy text-uppercase fw-bold" style="font-size:12px;letter-spacing:.1em;">More opportunities</h2>
      <?php foreach (fp_open_items('fp_scholarship', 5, $p->ID) as $m): ?>
        <a href="<?php echo esc_url(get_permalink($m)); ?>" class="d-block mt-3 text-decoration-none">
          <p class="font-display mb-0" style="color:var(--link);"><?php echo esc_html(get_the_title($m)); ?></p>
          <p class="text-muted2 mb-0" style="font-size:12px;"><?php echo esc_html(fp_get('funder', $m)); ?></p>
        </a>
      <?php endforeach; ?>
      <a href="<?php echo fr_url('/scholarships'); ?>" class="d-inline-block mt-3" style="font-size:14px;">View all →</a>
    </div>
  </div>
</div>
<?php get_footer();
