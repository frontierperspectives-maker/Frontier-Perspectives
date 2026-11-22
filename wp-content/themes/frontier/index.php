<?php
/** Fallback for anything without a dedicated template. */
get_header(); ?>
<div class="container py-4">
  <?php fr_page_head(wp_strip_all_tags(get_the_archive_title()) ?: get_bloginfo('name')); ?>
  <?php if (have_posts()): ?>
    <ul class="list-unstyled">
      <?php while (have_posts()): the_post(); ?>
        <li class="border-bottom pb-3 mb-3"><a href="<?php the_permalink(); ?>" class="font-display fs-5"><?php the_title(); ?></a></li>
      <?php endwhile; ?>
    </ul>
    <?php fr_pagination(); ?>
  <?php else: ?>
    <p class="text-muted2">Nothing here yet.</p>
  <?php endif; ?>
</div>
<?php get_footer();
