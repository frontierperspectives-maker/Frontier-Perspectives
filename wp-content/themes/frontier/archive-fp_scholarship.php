<?php
/** /scholarships, /grants and /scholarships/<country> */
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
      fr_page_head("Scholarships in $country", "Scholarships and funding opportunities in $country — sorted by deadline.");
  } elseif ($view === 'grants') {
      fr_page_head('Grants', 'Research, student, faculty, and early career researcher grants — sorted by deadline, expired listings removed automatically.');
      get_template_part('partials/filter-bar', null, ['base_path' => '/grants', 'groups' => fp_filter_groups('fp_scholarship', 'grants')]);
  } else {
      fr_page_head('Scholarships & Funding', 'Scholarships, grants, and research funding from around the world — sorted by deadline, expired listings removed automatically.');
      get_template_part('partials/filter-bar', null, ['base_path' => '/scholarships', 'groups' => fp_filter_groups('fp_scholarship')]);
  }
  ?>
  <p class="fw-semibold text-navy mb-3"><?php echo $n; ?> open opportunit<?php echo $n === 1 ? 'y' : 'ies'; ?></p>
  <?php
  get_template_part('partials/opp-grid', null, [
      'posts'       => $wp_query->posts,
      'empty_title' => 'No open opportunities right now',
      'empty_text'  => 'All current listings have closed or nothing matches your filters. New opportunities are posted regularly — check back soon.',
  ]);
  fr_pagination();
  if ($country): ?>
    <a href="<?php echo fr_url('/scholarships'); ?>" class="d-inline-block mt-3" style="font-size:14px;">View all scholarships →</a>
  <?php endif; ?>
</div>
<?php get_footer();
