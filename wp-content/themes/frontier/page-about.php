<?php
/** /about — mission, sections, team (Authors) and contact box. Page content, if any, is shown under the mission. */
get_header();
the_post();
$mission = fp_setting('footer_mission') ?: 'We exist to put rigorous evidence at the centre of public debate — independent of party, funder, and fashion.';
$email   = fp_setting('contact_email');
$team    = get_posts(['post_type' => 'fp_author', 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC']);
?>
<div class="container py-4">
  <div style="width:48px;height:4px;background:var(--gold);border-radius:2px;" class="mb-3"></div>
  <h1 class="text-navy fw-bold mb-4"><?php the_title(); ?></h1>
  <blockquote class="fr-quote ps-4 mx-auto" style="max-width:700px;">
    <p class="font-display text-navy" style="font-size:1.5rem;">"<?php echo esc_html($mission); ?>"</p>
  </blockquote>

  <?php if (trim(get_the_content())): ?><div class="prose mx-auto" style="max-width:760px;"><?php the_content(); ?></div><?php endif; ?>

  <div class="row g-4 mt-4">
    <?php foreach ([
      ['Research Articles', 'Original, data-driven analysis on global affairs, the economy, climate, and society — written to be read, not shelved.'],
      ['Publications', 'Flagship journals and annual reports that bring together leading contributors around a single theme.'],
      ['Opportunities', 'A curated board of academic jobs, fellowships, scholarships, and funding for the global research community.'],
    ] as [$t, $x]): ?>
      <div class="col-md-4">
        <h2 class="text-navy fs-5 fr-underline pb-1 d-inline-block"><?php echo esc_html($t); ?></h2>
        <p class="text-muted2 mt-2"><?php echo esc_html($x); ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($team): ?>
    <div class="mt-5">
      <h2 class="text-navy text-uppercase fw-bold" style="font-size:12px;letter-spacing:.1em;">Our Team</h2>
      <div class="row g-4 mt-1">
        <?php foreach ($team as $t):
            $initials = implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $t->post_title), 0, 2)));
            $avatar = fp_image_url($t, 'avatar', 'thumbnail'); $role = fp_get('role', $t); $bio = fp_plain_text($t->post_content, 400); ?>
          <div class="col-md-4 d-flex gap-3">
            <?php if ($avatar): ?>
              <img src="<?php echo esc_url($avatar); ?>" alt="" class="rounded-circle flex-shrink-0" style="width:56px;height:56px;object-fit:cover;">
            <?php else: ?>
              <span class="rounded-circle bg-navy text-white d-flex align-items-center justify-content-center font-display flex-shrink-0" style="width:56px;height:56px;font-size:16px;"><?php echo esc_html($initials); ?></span>
            <?php endif; ?>
            <div>
              <p class="font-display text-navy mb-0"><?php echo esc_html($t->post_title); ?></p>
              <?php if ($role): ?><p class="text-gold fw-semibold mb-0" style="font-size:13px;"><?php echo esc_html($role); ?></p><?php endif; ?>
              <?php if ($bio): ?><p class="text-muted2 mb-0" style="font-size:13px;"><?php echo esc_html($bio); ?></p><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="bg-mist rounded-4 p-4 text-center mt-5">
    <h2 class="text-navy fs-4">Questions, or have an opportunity to list?</h2>
    <p class="text-muted2 mx-auto mt-2" style="max-width:520px;">
      For research or partnership enquiries, <?php if ($email): ?>write to <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a><?php else: ?>get in touch<?php endif; ?>.
      To submit a job, fellowship, scholarship, or grant, use our <a href="<?php echo fr_url('/submit-opportunity'); ?>">Submit Opportunity</a> page.
    </p>
  </div>
</div>
<?php get_footer();
