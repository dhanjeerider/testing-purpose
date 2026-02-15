<?php
/**
 * Header template
 *
 * Provides the site header markup replicating the HTML structure from the
 * original MXSeries website. All CSS classes and IDs are preserved for
 * compatibility with existing stylesheets. The search forms and menus
 * are dynamically generated via WordPress functions.
 *
 * @package MXSeries HTML Theme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="header" class="main">
    <div class="hbox container">
        <div class="logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php
                if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    // Fallback: display site title when no custom logo is set.
                    echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                }
                ?>
            </a>
        </div>
     
<!-- Secondary navigation menu below header -->
<?php if ( has_nav_menu( 'secondary' ) ) : ?>
<nav class="secondary-nav">
    <div class="menu-secondary-container">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'secondary',
            'container'      => false,
            'menu_id'        => 'secondary-menu',
            'menu_class'     => 'menu',
        ) );
        ?>
    </div>
</nav>
<?php endif; ?>

   <div class="headitems ">
            <div id="advc-menu" class="search">
                <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="text" placeholder="<?php esc_attr_e( 'Search...', 'mxseries-html-theme' ); ?>" name="s" id="s" value="<?php echo get_search_query(); ?>" autocomplete="off" />
                    <button class="search-button" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        <div class="live-search ltr"></div>
    </div>
</header>
<!-- Search overlay shown when search button is clicked -->
<div id="header-search-overlay" class="header-search-overlay" style="display: none; padding: 10px; border: 1px solid #ccc; margin-top: 10px; text-align: center;">
    <?php get_search_form(); ?>
</div>

<div class="fixheadresp">
    <header class="responsive">
        <div class="nav"><a id="nav-button" class="aresp nav-resp" data-toggle="off-canvas" href="#"></a></div>
        <!-- Button to toggle the search overlay. It uses a data attribute so our JS can attach events. -->
        <div class="search"><a class="aresp search-resp search-overlay-toggle" href="#"></a></div>
        <div class="logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php
                if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                }
                ?>
            </a>
        </div>
    </header>
    <div class="search_responsive">
        <form method="get" id="form-search-resp" class="form-resp-ab" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="text" placeholder="<?php esc_attr_e( 'Search...', 'mxseries-html-theme' ); ?>" name="s" id="ms" value="<?php echo get_search_query(); ?>" autocomplete="off" />
            <button type="submit" class="search-button"><span class="serch"></span></button>
        </form>
        <div class="live-search"></div>
    </div>
 	   <div id="arch-menu" class="menuresp">
       <div class="menu">       		<span id="closeoffcanvas">❌</span>
		
			<div class="menu-mainmenu-container">
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_id'        => 'primary-menu-responsive',
                        'menu_class'     => 'menu',
                    ) );
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="scrollmenu"></div>


<style>/* ===== Secondary Nav Wrapper ===== */
.secondary-nav {
    background: #0f0f0f;
    border-top: 1px solid rgba(255,255,255,0.06);
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.secondary-nav .menu {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 28px;
    list-style: none;
    margin: 0;
    padding: 14px 0;
}

/* ===== Top Level Links ===== */
.secondary-nav .menu > li {
    position: relative;
}

.secondary-nav .menu > li > a {
    color: #ddd;
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    padding: 6px 2px;
    transition: 0.3s ease;
    position: relative;
}

/* Animated underline */
.secondary-nav .menu > li > a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    width: 0%;
    height: 2px;
    background: #e50914;
    transition: 0.3s ease;
}

/* Hover Effect */
.secondary-nav .menu > li:hover > a {
    color: #fff;
}

.secondary-nav .menu > li:hover > a::after {
    width: 100%;
}

/* Active Menu */
.secondary-nav .current-menu-item > a {
    color: #fff;
}

.secondary-nav .current-menu-item > a::after {
    width: 100%;
}

/* ===== Dropdown Menu ===== */
.secondary-nav .menu li ul {
    position: absolute;
    top: 45px;
    left: 0;
    min-width: 180px;
    background: #151515;
    border-radius: 8px;
    padding: 10px 0;
    list-style: none;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: 0.3s ease;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    z-index: 999;
}

/* Show dropdown on hover */
.secondary-nav .menu li:hover > ul {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Dropdown links */
.secondary-nav .menu li ul li {
    padding: 0;
}

.secondary-nav .menu li ul li a {
    display: block;
    padding: 10px 18px;
    color: #ccc;
    font-size: 14px;
    transition: 0.3s ease;
}

.secondary-nav .menu li ul li a:hover {
    background: rgba(229,9,20,0.1);
    color: #fff;
}

/* ===== Smooth Hover Glow ===== */
.secondary-nav .menu > li:hover {
    transform: translateY(-2px);
    transition: 0.2s ease;
}#primary-menu-repsonsive ul{margin-top:22px!important;}
video{max-width:100%}
</style>	<script>
document.getElementById("closeoffcanvas").onclick = function() {
    document.getElementById("arch-menu").style.display = "none";
};
</script>
