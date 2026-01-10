<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Dark Mode Toggle -->
<div class="dark-mode-toggle" id="darkModeToggle">
    <i class="fas fa-moon"></i>
</div>

<!-- Header -->
<header class="site-header bg-white shadow-sm" style="background: var(--card-bg) !important;">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <!-- Logo -->
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <h1 class="h3 mb-0"><?php bloginfo('name'); ?></h1>
                </a>
            <?php endif; ?>
            
            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'navbar-nav ms-auto',
                    'fallback_cb' => '__return_false',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth' => 2,
                    'walker' => new WP_Bootstrap_Navwalker(),
                ));
                ?>
            </div>
        </div>
    </nav>
</header>

<!-- AJAX Loader -->
<div class="ajax-loader" id="ajaxLoader">
    <div class="spinner"></div>
    <p class="mt-3">Loading...</p>
</div>

<!-- Main Content Wrapper -->
<div id="page-content" class="page-transition">
