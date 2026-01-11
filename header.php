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
        <div class="header-main">
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <h1 class="site-title">
                        <a href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a>
                    </h1>
                <?php endif; ?>
            </div>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-menu',
                    'container' => false,
                    'fallback_cb' => 'default_menu'
                ));
                
                function default_menu() {
                    echo '<ul class="nav-menu">';
                    echo '<li><a href="' . home_url('/') . '">Home</a></li>';
                    wp_list_categories(array(
                        'title_li' => '',
                        'depth' => 1,
                        'number' => 5
                    ));
                    echo '</ul>';
                }
                ?>
            </nav>
        </div>
    </div>
</header>

<main class="site-main">
