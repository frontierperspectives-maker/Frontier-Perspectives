<?php $email = fp_setting('contact_email'); $mission = fp_setting('footer_mission'); ?>
</main>
<footer class="site-footer">
  <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3 py-5 border-bottom border-white border-opacity-10">
    <h2 class="text-white mb-0" style="max-width:560px;font-size:2rem;">Let's explore ideas <span class="text-gold">that matter.</span></h2>
    <?php if ($email): ?>
      <a href="mailto:<?php echo esc_attr($email); ?>" class="btn btn-gold rounded-pill">Get in touch →</a>
    <?php endif; ?>
  </div>
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-2">
          <span class="brand-logo" style="width:34px;height:34px;">F</span>
          <span class="text-white fw-bold font-display fs-5"><?php echo esc_html(fp_setting('site_name')); ?></span>
        </div>
        <p class="text-gold mt-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.1em;"><?php echo esc_html(fp_setting('tagline')); ?></p>
        <?php if ($mission): ?><p class="mt-2" style="max-width:280px;font-size:14px;"><?php echo esc_html($mission); ?></p><?php endif; ?>
      </div>
      <div class="col-6 col-md-2">
        <h3>Explore</h3>
        <ul class="list-unstyled mt-3">
          <li class="mb-2"><a href="<?php echo fr_url('/articles'); ?>">Research Articles</a></li>
          <li class="mb-2"><a href="<?php echo fr_url('/publications'); ?>">Publications</a></li>
          <li class="mb-2"><a href="<?php echo fr_url('/about'); ?>">About Us</a></li>
          <li class="mb-2"><a href="<?php echo fr_url('/search'); ?>">Search</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-2">
        <h3>Opportunities</h3>
        <ul class="list-unstyled mt-3">
          <li class="mb-2"><a href="<?php echo fr_url('/jobs'); ?>">Jobs</a></li>
          <li class="mb-2"><a href="<?php echo fr_url('/fellowships'); ?>">Fellowships</a></li>
          <li class="mb-2"><a href="<?php echo fr_url('/scholarships'); ?>">Scholarships</a></li>
          <li class="mb-2"><a href="<?php echo fr_url('/grants'); ?>">Grants</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-2">
        <h3>Connect</h3>
        <ul class="list-unstyled mt-3">
          <?php foreach (fp_social_links() as $s): ?>
            <li class="mb-2"><a href="<?php echo esc_url($s['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($s['label']); ?></a></li>
          <?php endforeach; ?>
          <?php if ($email): ?><li class="mb-2"><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li><?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="border-top border-white border-opacity-10 py-3">
    <div class="container d-flex flex-wrap justify-content-between gap-2" style="font-size:12px;">
      <p class="mb-0">© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(fp_setting('site_name')); ?>. All rights reserved.</p>
      <p class="mb-0 d-flex gap-3">
        <?php foreach (['privacy' => 'Privacy', 'terms' => 'Terms'] as $slug => $label):
            $pg = get_page_by_path($slug);
            if ($pg && $pg->post_status === 'publish'): ?>
          <a href="<?php echo fr_url("/p/$slug"); ?>"><?php echo esc_html($label); ?></a>
        <?php endif; endforeach; ?>
      </p>
    </div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
