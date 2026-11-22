<?php
/** /jobs, /fellowships and /jobs/<country> */
get_header();
global $wp_query;
$view    = get_query_var('fp_view');
$cslug   = get_query_var('fp_country');
$country = $cslug ? array_search($cslug, fp_country_slugs(), true) : '';
$n       = (int) $wp_query->found_posts;
?>
<div class="container py-4">
  <?php
  if ($country) {
      fr_page_head("Jobs in $country", "Academic jobs and fellowships in $country — sorted by deadline.");
  } elseif ($view === 'fellowships') {
      fr_page_head('Fellowships', 'Research, graduate, postdoctoral, and visiting fellowships — sorted by deadline, with expired listings removed automatically.');
      get_template_part('partials/filter-bar', null, ['base_path' => '/fellowships', 'groups' => fp_filter_groups('fp_job', 'fellowships')]);
  } else {
      fr_page_head('Academic Jobs & Fellowships', 'Faculty positions, postdocs, fellowships, and funded PhDs — sorted by deadline, with expired listings removed automatically.');
      get_template_part('partials/filter-bar', null, ['base_path' => '/jobs', 'groups' => fp_filter_groups('fp_job')]);
  }
  ?>
  <p class="fw-semibold text-navy mb-3"><?php echo $n; ?> open opportunit<?php echo $n === 1 ? 'y' : 'ies'; ?></p>
  <?php
  get_template_part('partials/opp-grid', null, [
      'posts'       => $wp_query->posts,
      'empty_title' => 'No open positions right now',
      'empty_text'  => $country
          ? "No jobs in $country match right now — check back soon."
          : 'All current listings have closed or nothing matches your filters. New opportunities are posted regularly — check back soon.',
  ]);
  fr_pagination();
  if ($country): ?>
    <a href="<?php echo fr_url('/jobs'); ?>" class="d-inline-block mt-3" style="font-size:14px;">View all jobs →</a>
  <?php endif; ?>
</div>
<?php get_footer();
