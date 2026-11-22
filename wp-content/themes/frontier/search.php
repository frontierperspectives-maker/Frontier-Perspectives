<?php
get_header();
$q = get_search_query();
$labels = ['fp_article' => 'Article', 'fp_publication' => 'Publication', 'fp_job' => 'Job', 'fp_scholarship' => 'Scholarship'];
?>
<div class="container py-4">
  <?php fr_page_head('Search', $q !== '' ? 'Results for "' . $q . '"' : 'Search articles, publications, jobs, and scholarships.'); ?>
  <form method="get" action="<?php echo fr_url('/search'); ?>" class="mb-4" style="max-width:480px;" role="search">
    <label class="screen-reader-text" for="fr-search-page">Search</label>
    <input id="fr-search-page" type="search" name="q" value="<?php echo esc_attr($q); ?>" class="form-control form-control-lg" placeholder="Search…">
  </form>
  <?php if ($q === ''): ?>
    <p class="text-muted2">Type something above to search the site.</p>
  <?php elseif (!have_posts()): ?>
    <div class="fr-empty rounded-4 p-5 text-center"><h3 class="text-navy">No results for "<?php echo esc_html($q); ?>"</h3><p class="text-muted2">Try a different search term.</p></div>
  <?php else: ?>
    <ul class="list-unstyled">
      <?php while (have_posts()): the_post(); ?>
        <li class="border-bottom pb-3 mb-3">
          <span class="pill"><?php echo esc_html($labels[get_post_type()] ?? 'Page'); ?></span>
          <a href="<?php the_permalink(); ?>" class="d-block mt-1 font-display fs-5"><?php the_title(); ?></a>
        </li>
      <?php endwhile; ?>
    </ul>
    <?php fr_pagination(); ?>
  <?php endif; ?>
</div>
<?php get_footer();
