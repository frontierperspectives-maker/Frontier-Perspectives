<?php get_header(); the_post(); ?>
<div class="container py-4 prose" style="max-width:760px;">
  <div style="width:48px;height:4px;background:var(--gold);border-radius:2px;" class="mb-3"></div>
  <h1 class="text-navy fw-bold mb-4"><?php the_title(); ?></h1>
  <?php the_content(); ?>
</div>
<?php get_footer();
