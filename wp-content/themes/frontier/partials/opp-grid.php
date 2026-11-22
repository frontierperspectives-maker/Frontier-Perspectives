<?php
/**
 * Opportunity cards. Args: posts (WP_Post[]), empty_title, empty_text.
 */
$posts = $args['posts'] ?? [];
if (!$posts): ?>
  <div class="fr-empty rounded-4 p-5 text-center">
    <h3 class="text-navy"><?php echo esc_html($args['empty_title'] ?? 'Nothing here yet'); ?></h3>
    <p class="text-muted2 mt-2"><?php echo esc_html($args['empty_text'] ?? ''); ?></p>
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($posts as $p): $o = fp_opp_card($p); ?>
      <div class="col-md-6 col-lg-4">
        <div class="opp-card card h-100 p-3 border-0">
          <div class="d-flex align-items-start gap-3">
            <span class="opp-logo">
              <?php if ($o['org_logo']): ?><img src="<?php echo esc_url($o['org_logo']); ?>" alt="" loading="lazy">
              <?php else: ?><span class="font-display text-navy fs-5"><?php echo esc_html(mb_substr((string) $o['org'], 0, 1)); ?></span><?php endif; ?>
            </span>
            <div class="min-w-0">
              <a href="<?php echo esc_url($o['href']); ?>" class="d-block fw-semibold font-display text-decoration-none" style="color:var(--link);"><?php echo esc_html($o['title']); ?></a>
              <p class="text-muted2 mb-0" style="font-size:13px;"><?php echo esc_html($o['org']); ?></p>
            </div>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
            <?php if ($o['amount']): ?><span class="fw-semibold" style="font-size:14px;"><?php echo esc_html($o['amount']); ?></span><?php endif; ?>
            <?php echo fr_deadline_html($o['deadline'], $o['deadline_note']); ?>
          </div>
          <?php if ($o['description']): ?><p class="text-muted2 mt-2" style="font-size:14px;"><?php echo esc_html($o['description']); ?></p><?php endif; ?>
          <?php if ($o['tags']): ?>
            <div class="mt-2"><?php foreach ($o['tags'] as $t): ?><span class="pill me-1"><?php echo esc_html($t); ?></span><?php endforeach; ?></div>
          <?php endif; ?>
          <div class="mt-auto pt-3 fr-card-footer">
            <?php if ($o['apply_url']): ?>
              <a href="<?php echo esc_url($o['apply_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold btn-sm rounded-pill">Apply Now</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif;
