<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#007bff">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.json">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Search Popup -->
<div class="search-popup" id="searchPopup">
    <div class="search-popup-content">
        <button class="search-close" id="searchClose">&times;</button>
        <form role="search" method="get" class="search-form-popup" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" class="search-field-popup" placeholder="Search articles..." name="s" autocomplete="off">
            <button type="submit" class="search-submit-popup">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div id="searchResults" class="search-results-live"></div>
    </div>
</div>

<!-- Header Top Bar -->
<div class="header-top-bar" style="background: var(--card-bg); border-bottom: 1px solid var(--border-color); padding: 5px 0;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-top-left">
                <span class="text-muted" style="font-size: 12px;">
                    <i class="far fa-calendar"></i> <?php echo date('F j, Y'); ?>
                </span>
            </div>
            <div class="header-top-right d-flex gap-2">
                <!-- Custom Header Menu -->
                <?php
                if (has_nav_menu('custom-header')) {
                    wp_nav_menu(array(
                        'theme_location' => 'custom-header',
                        'container' => false,
                        'menu_class' => 'custom-header-menu',
                        'depth' => 1,
                        'fallback_cb' => false,
                    ));
                }
                ?>
                
                <!-- Notification Bell -->
                <button class="btn btn-sm btn-outline-secondary p-1" id="notificationBell" title="Notifications" style="font-size: 12px;">
                    <i class="far fa-bell"></i>
                    <span class="badge bg-danger notification-count" style="font-size: 9px;">3</span>
                </button>
                
                <!-- Search Icon -->
                <button class="btn btn-sm btn-outline-secondary p-1" id="searchToggle" title="Search" style="font-size: 12px;">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Join Telegram -->
                <a href="https://t.me/yourchannel" class="btn btn-sm btn-primary p-1 px-2" target="_blank" rel="noopener" title="Join Telegram" style="font-size: 12px;">
                    <i class="fab fa-telegram-plane"></i> Join
                </a>
                
                <!-- Dark Mode Toggle -->
                <button class="btn btn-sm btn-outline-secondary p-1" id="darkModeToggle" title="Toggle Dark Mode" style="font-size: 12px;">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="site-header" style="background: var(--card-bg); border-bottom: 2px solid var(--border-color); padding: 10px 0;">
    <nav class="navbar navbar-expand-lg p-0">
        <div class="container">
            <!-- Logo -->
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="navbar-brand m-0" href="<?php echo esc_url(home_url('/')); ?>">
                    <h1 class="mb-0" style="color: var(--text-color); font-size: 1.5rem; font-weight: 700;"><?php bloginfo('name'); ?></h1>
                </a>
            <?php endif; ?>
            
            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="padding: 5px 10px; font-size: 14px;">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Main Mobile Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                $menu_args = array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'navbar-nav ms-auto',
                    'fallback_cb' => '__return_false',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth' => 2,
                );
                
                // Add walker only if class exists
                if (class_exists('WP_Bootstrap_Navwalker')) {
                    $menu_args['walker'] = new WP_Bootstrap_Navwalker();
                }
                
                wp_nav_menu($menu_args);
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
