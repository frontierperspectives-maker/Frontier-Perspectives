<?php
get_header();
global $wp_query;
$cats = get_terms(['taxonomy' => 'fp_category', 'hide_empty' => true]);
$cur  = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
?>
<div class="container py-4">
  <?php fr_page_head('Research Articles', 'Original, data-driven analysis on global affairs, the economy, climate, and society — written to be read, not shelved.'); ?>
  <?php if ($cats && !is_wp_error($cats)): ?>
    <div class="mb-4">
      <a href="<?php echo fr_url('/articles'); ?>" class="filter-pill<?php echo !$cur ? ' active' : ''; ?>">All</a>
      <?php foreach ($cats as $c): ?>
        <a href="<?php echo fr_url('/articles') . '?category=' . rawurlencode($c->slug); ?>" class="filter-pill<?php echo $cur === $c->slug ? ' active' : ''; ?>"><?php echo esc_html($c->name); ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php if (have_posts()): ?>
    <div class="row g-4">
      <?php while (have_posts()): the_post(); get_template_part('partials/article-card', null, ['image' => true]); endwhile; ?>
    </div>
    <?php fr_pagination(); ?>
  <?php else: ?>
    <div class="fr-empty rounded-4 p-5 text-center"><h3 class="text-navy">No articles yet</h3><p class="text-muted2">New research is on the way. Check back soon.</p></div>
  <?php endif; ?>
</div>
<?php get_footer();
