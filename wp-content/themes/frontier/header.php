<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $current = fr_current_path(); $logo = fp_setting('logo'); ?>
<header class="site-header border-bottom">
  <nav class="navbar navbar-expand-lg py-3" aria-label="Main">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo fr_url('/'); ?>">
        <?php if ($logo): ?>
          <img src="<?php echo esc_url($logo); ?>" alt="" style="width:40px;height:40px;border-radius:12px;object-fit:cover;">
        <?php else: ?>
          <span class="brand-logo">F</span>
        <?php endif; ?>
        <span>
          <span class="d-block fw-bold text-navy font-display" style="font-size:1.15rem;line-height:1.1;"><?php echo esc_html(fp_setting('site_name')); ?></span>
          <span class="d-block text-muted2" style="font-size:11px;"><?php echo esc_html(fp_setting('tagline')); ?></span>
        </span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto">
          <?php foreach (fr_nav_items() as $n):
              $active = $current !== '/' && str_starts_with($current, $n['href']); ?>
            <?php if (!empty($n['children'])): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle<?php echo $active ? ' active' : ''; ?>" href="<?php echo fr_url($n['href']); ?>" data-bs-toggle="dropdown" aria-expanded="false"><?php echo esc_html($n['label']); ?></a>
                <ul class="dropdown-menu">
                  <?php foreach ($n['children'] as $c): ?>
                    <li><a class="dropdown-item" href="<?php echo fr_url($c['href']); ?>"><?php echo esc_html($c['label']); ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link<?php echo $active ? ' active' : ''; ?>" href="<?php echo fr_url($n['href']); ?>"><?php echo esc_html($n['label']); ?></a></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
        <form class="d-flex me-2" action="<?php echo fr_url('/search'); ?>" method="get" role="search">
          <label class="screen-reader-text" for="fr-search">Search</label>
          <input id="fr-search" class="form-control form-control-sm rounded-pill" type="search" name="q" placeholder="Search…">
        </form>
        <a href="<?php echo fr_url('/submit-opportunity'); ?>" class="btn btn-gold rounded-pill">Submit Opportunity</a>
      </div>
    </div>
  </nav>
</header>
<main id="main">
