<?php
get_header();
$slides   = fp_hero_slides();
$pub      = fp_featured_publication();
$articles = get_posts(['post_type' => 'fp_article', 'numberposts' => 30]);
$n_art    = (int) wp_count_posts('fp_article')->publish;
$n_pub    = (int) wp_count_posts('fp_publication')->publish;
$n_jobs   = fp_count_open('fp_job');
$n_sch    = fp_count_open('fp_scholarship');

// Sidebar = 3 newest; "Recent" = next 3 not already shown in the hero or sidebar.
$sidebar    = array_slice($articles, 0, 3);
$hero_hrefs = array_map(fn($s) => untrailingslashit((string) fp_get('href', $s)), $slides);
$skip       = wp_list_pluck($sidebar, 'ID');
$recent     = array_slice(array_values(array_filter($articles, function ($a) use ($hero_hrefs, $skip) {
    return !in_array($a->ID, $skip, true) && !in_array('/articles/' . $a->post_name, $hero_hrefs, true);
})), 0, 3);

$tagline = fp_setting('tagline') ?: 'Evidence. Insight. Impact.';
$mission = fp_setting('footer_mission') ?: 'An independent research platform turning rigorous evidence into ideas and real-world impact.';
?>
<div class="container py-4">
  <div class="row align-items-center g-5 py-4">
    <div class="col-lg-7">
      <span class="badge rounded-pill text-navy fw-semibold px-3 py-2" style="background:rgba(242,107,58,.18);font-size:12px;text-transform:uppercase;letter-spacing:.1em;">
        <?php echo esc_html(fp_setting('site_name')); ?> · Independent research
      </span>
      <h1 class="text-navy fw-bold mt-3" style="font-size:3.2rem;line-height:1.05;"><?php echo esc_html($tagline); ?></h1>
      <p class="text-muted2 mt-3" style="font-size:18px;max-width:560px;"><?php echo esc_html($mission); ?></p>
      <div class="mt-4 d-flex gap-2 flex-wrap">
        <a href="<?php echo fr_url('/articles'); ?>" class="btn btn-gold rounded-pill">Explore research →</a>
        <a href="<?php echo fr_url('/publications'); ?>" class="btn btn-navy rounded-pill">Browse publications →</a>
      </div>
      <p class="text-muted2 mt-3" style="font-size:14px;">
        <strong class="text-navy"><?php echo $n_art; ?></strong> articles ·
        <strong class="text-navy"><?php echo $n_pub; ?></strong> publications ·
        <strong class="text-navy"><?php echo $n_jobs + $n_sch; ?></strong> opportunities
      </p>
    </div>
    <div class="col-lg-5">
      <?php if ($slides): $s = $slides[0]; $simg = fp_image_url($s); ?>
        <a href="<?php echo fr_url((string) fp_get('href', $s)); ?>" class="d-block rounded-4 overflow-hidden text-white text-decoration-none bg-navy shadow">
          <?php if ($simg): ?><img src="<?php echo esc_url($simg); ?>" class="w-100" style="aspect-ratio:4/3;object-fit:cover;" alt=""><?php endif; ?>
          <div class="p-4">
            <p class="text-gold text-uppercase fw-bold mb-1" style="font-size:11px;letter-spacing:.14em;"><?php echo esc_html(fp_get('eyebrow', $s)); ?></p>
            <h2 class="text-white fs-4 fw-bold"><?php echo esc_html(get_the_title($s)); ?></h2>
          </div>
        </a>
      <?php elseif ($pub): $cover = fp_image_url($pub, 'cover_image'); ?>
        <a href="<?php echo esc_url(get_permalink($pub)); ?>" class="d-block rounded-4 overflow-hidden text-white text-decoration-none bg-navy shadow">
          <?php if ($cover): ?><img src="<?php echo esc_url($cover); ?>" class="w-100" style="aspect-ratio:4/3;object-fit:cover;" alt=""><?php endif; ?>
          <div class="p-4">
            <p class="text-gold text-uppercase fw-bold mb-1" style="font-size:11px;">Featured publication</p>
            <h2 class="text-white fs-4 fw-bold"><?php echo esc_html(get_the_title($pub)); ?></h2>
          </div>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-5 mt-4">
    <div class="col-lg-8">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-navy fs-3">Recent Research Articles</h2>
        <a href="<?php echo fr_url('/articles'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill">View all →</a>
      </div>
      <?php if ($recent): ?>
        <div class="row g-4">
          <?php foreach ($recent as $a) get_template_part('partials/article-card', null, ['post' => $a, 'col' => 'col-md-6', 'excerpt' => 110]); ?>
        </div>
      <?php else: ?>
        <p class="text-muted2">New research is on the way. Check back soon.</p>
      <?php endif; ?>
    </div>
    <div class="col-lg-4">
      <h2 class="text-navy fs-5 mb-3">Latest Articles</h2>
      <?php foreach ($sidebar as $a): ?>
        <a href="<?php echo esc_url(get_permalink($a)); ?>" class="d-flex gap-3 mb-3 text-decoration-none">
          <div>
            <h4 class="font-display text-navy" style="font-size:15px;"><?php echo esc_html(get_the_title($a)); ?></h4>
            <p class="text-muted2 mb-0" style="font-size:12px;"><?php echo esc_html(fp_format_date(get_post_time('U', false, $a))); ?></p>
          </div>
        </a>
      <?php endforeach; ?>
      <a href="<?php echo fr_url('/articles'); ?>" class="d-inline-block mt-2" style="font-size:14px;">View all research articles →</a>
    </div>
  </div>

  <div class="bg-navy rounded-4 text-white text-center py-5 px-4 mt-5">
    <p class="text-gold text-uppercase fw-bold" style="font-size:11px;letter-spacing:.16em;">Evidence. Insight. Impact.</p>
    <div class="row mt-4">
      <?php foreach ([[$n_art, 'Research articles'], [$n_pub, 'Publications'], [$n_jobs, 'Open opportunities'], [$n_sch, 'Scholarships & grants']] as [$v, $l]): ?>
        <div class="col-6 col-md-3">
          <div class="font-display fw-bold" style="font-size:2.2rem;"><?php echo (int) $v; ?><span class="text-gold">+</span></div>
          <div class="text-white-50" style="font-size:13px;"><?php echo esc_html($l); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="row g-4 mt-1">
    <?php
    $quick = [
      ['Jobs & Fellowships', 'Find faculty positions, postdocs, and research fellowships.', '/jobs'],
      ['Scholarships & Grants', 'Discover scholarships, grants, and funding opportunities worldwide.', '/scholarships'],
      ['Publications', 'Explore our journals, reports, and special editions.', '/publications'],
      ['About Us', 'We are an independent research platform committed to evidence, ideas, and impact.', '/about'],
    ];
    foreach ($quick as [$t, $x, $h]): ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?php echo fr_url($h); ?>" class="card h-100 border-0 shadow-sm p-3 text-decoration-none d-flex flex-column">
          <h3 class="text-navy fs-6"><?php echo esc_html($t); ?></h3>
          <p class="text-muted2 flex-grow-1" style="font-size:14px;"><?php echo esc_html($x); ?></p>
          <span style="font-size:14px;">Explore →</span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php get_footer();
