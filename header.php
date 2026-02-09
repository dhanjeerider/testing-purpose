<?php
/**
 * The header for the mvdrive theme.
 *
 * This file displays all of the <head> section and everything up until the
 * opening of the main content area.  It contains a custom header and
 * responsive side menu modeled after the original MoviesDrive layout.  The
 * structure and class names are preserved to ensure compatibility with the
 * user‑supplied CSS.  Site names and URLs have been replaced with dynamic
 * WordPress functions.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head><link href="https://dhanjeerider.github.io/themecdn.io/mvsdrv.css" rel="stylesheet"/><meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="header">
    <div class="header-container">
        <button class="hamburger" id="hamburgerBtn">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="logo">
            <span class="logo-text">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <?php
                    // Display the custom logo if set in the Customizer.
                    if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        // Fallback: use a placeholder image stored in the theme's assets directory.
                        $logo_src = get_template_directory_uri() . '/assets/logo.png';
                        ?>
                        <img class="logo-image" src="<?php echo esc_url( $logo_src ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                        <?php
                    }
                    ?>
                </a>
            </span>
        </div>
        <nav class="nav-menu" id="navMenu">
            <ul class="nav-list">
                <?php
                // Get dynamic menu - limit to 3 items
                $menu_items = wp_get_nav_menu_items('primary');
                if ($menu_items && is_array($menu_items)) {
                    $count = 0;
                    foreach ($menu_items as $item) {
                        if ($count >= 3) break;
                        if ($item->menu_item_parent == 0) {
                            $has_children = false;
                            foreach ($menu_items as $child) {
                                if ($child->menu_item_parent == $item->ID) {
                                    $has_children = true;
                                    break;
                                }
                            }
                            ?>
                            <li class="nav-item<?php echo $has_children ? ' has-submenu' : ''; ?>">
                                <a href="<?php echo esc_url($item->url); ?>" class="nav-link">
                                    <?php echo esc_html($item->title); ?>
                                    <?php if ($has_children): ?>
                                        <svg class="dropdown-icon" viewBox="0 0 24 24"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"></path></svg>
                                    <?php endif; ?>
                                </a>
                                <?php if ($has_children): ?>
                                    <ul class="submenu">
                                        <?php foreach ($menu_items as $child): ?>
                                            <?php if ($child->menu_item_parent == $item->ID): ?>
                                                <li><a href="<?php echo esc_url($child->url); ?>"><?php echo esc_html($child->title); ?></a></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                            <?php
                            $count++;
                        }
                    }
                }
                ?>
            </ul>
        </nav>
        <div class="search-bar">
            <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none">
                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"></circle>
                    <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"></path>
                </svg>
                <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search here..." class="search-input">
            </form>
        </div>
    </div>
</header>

<div class="menu-overlay" id="menuOverlay"></div>

<aside class="side-menu" id="sideMenu">
    <div class="side-menu-header">
        <div class="logo">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none">
                <path d="M8 5V19L19 12L8 5Z" fill="currentColor"></path>
            </svg>
            <span class="logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
        </div>
        <button class="close-menu" id="closeMenuBtn">
            <svg viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2"></path></svg>
        </button>
    </div>
    <nav class="side-nav">
        <ul class="side-nav-list">
            <li class="side-nav-item"><a href="<?php echo esc_url( home_url( '/category/adult/' ) ); ?>" class="side-nav-link">🔞 Adult++</a></li>
            <li class="side-nav-item has-submenu">
                <a href="#" class="side-nav-link">OTT <svg class="dropdown-icon" viewBox="0 0 24 24"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"></path></svg></a>
                <ul class="side-submenu">
                    <li><a href="<?php echo esc_url( home_url( '/category/netflix/' ) ); ?>">NetFlix</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/amazon-prime-video/' ) ); ?>">Amazon Prime Video</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=jio+hotstar' ) ); ?>">Jio HotStar</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=Disney+' ) ); ?>">Disney+</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=Apple+TV+' ) ); ?>">Apple TV+</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=HBO+Max' ) ); ?>">HBO Max</a></li>
                </ul>
            </li>
            <li class="side-nav-item has-submenu">
                <a href="#" class="side-nav-link">Movies <svg class="dropdown-icon" viewBox="0 0 24 24"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"></path></svg></a>
                <ul class="side-submenu">
                    <li><a href="<?php echo esc_url( home_url( '/category/hollywood/' ) ); ?>">HollyWood</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/bollywood/' ) ); ?>">BollyWood</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/south/' ) ); ?>">South Indian</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=hindi' ) ); ?>">Hindi Movies</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=english' ) ); ?>">English Movies</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/dual-audio/' ) ); ?>">Dual Audio</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/k-drama/' ) ); ?>">Korean</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=punjabi' ) ); ?>">Punjabi</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=bengali' ) ); ?>">Bengali</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/?s=gujarati' ) ); ?>">Gujarati</a></li>
                </ul>
            </li>
            <li class="side-nav-item"><a href="<?php echo esc_url( home_url( '/category/web/' ) ); ?>" class="side-nav-link">Web‑Series</a></li>
            <li class="side-nav-item has-submenu">
                <a href="#" class="side-nav-link">Genre <svg class="dropdown-icon" viewBox="0 0 24 24"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"></path></svg></a>
                <ul class="side-submenu">
                    <li><a href="<?php echo esc_url( home_url( '/category/dual-audio/' ) ); ?>">Dual Audio</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/hindi-dubbed/' ) ); ?>">Hindi Dubbed</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/action/' ) ); ?>">Action</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/romance/' ) ); ?>">Romance</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/horror/' ) ); ?>">Horror</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/comedy/' ) ); ?>">Comedy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/drama/' ) ); ?>">Drama</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/fight/' ) ); ?>">Fight</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/animation/' ) ); ?>">Animation</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/crime/' ) ); ?>">Crime</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/documentary/' ) ); ?>">Documentary</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/family/' ) ); ?>">Family</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/scifi/' ) ); ?>">Sci‑Fi</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/thriller/' ) ); ?>">Thriller</a></li>
                </ul>
            </li>
            <li class="side-nav-item"><a href="<?php echo esc_url( home_url( '/category/anime/' ) ); ?>" class="side-nav-link">Anime</a></li>
            <li class="side-nav-item"><a href="<?php echo esc_url( home_url( '/category/2160p-4k/' ) ); ?>" class="side-nav-link">4K</a></li>
            <li class="side-nav-item"><a href="<?php echo esc_url( home_url( '/category/k-drama/' ) ); ?>" class="side-nav-link">K‑Drama</a></li>
        </ul>
    </nav>
</aside>

<!-- The main content begins in the template files (index.php, single.php, etc.) -->