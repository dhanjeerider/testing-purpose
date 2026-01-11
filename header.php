<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-logo">
                <a href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a>
            </div>
            <nav>
                <?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'nav-menu', 'fallback_cb' => false)); ?>
            </nav>
            <a href="<?php echo home_url('/contact'); ?>" class="header-btn">Contact</a>
        </div>
    </div>
</header>

<main class="site-main">
