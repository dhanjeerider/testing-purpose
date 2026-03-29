<?php
// functions.php — AppStore Pro

// Theme Setup
function appstorepro_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_image_size( 'app-icon', 96, 96, true );
	add_image_size( 'app-hero', 800, 400, true );
	add_image_size( 'app-screenshot', 400, 700, true );

	register_nav_menus( [
		'primary'    => __( 'Primary Menu', 'appstorepro' ),
		'slide-menu' => __( 'Slide Menu', 'appstorepro' ),
		'legal-menu' => __( 'Legal Menu', 'appstorepro' ),
		'footer-nav' => __( 'Footer Navigation', 'appstorepro' ),
	] );

	load_theme_textdomain( 'appstorepro', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'appstorepro_setup' );

// Widget Areas
function appstorepro_widgets_init() {
	register_sidebar( [
		'name'          => __( 'Sidebar', 'appstorepro' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	] );
	register_sidebar( [
		'name'          => __( 'Footer Widget Area', 'appstorepro' ),
		'id'            => 'footer-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	] );
}
add_action( 'widgets_init', 'appstorepro_widgets_init' );

// Anti-flicker inline script
function appstorepro_anti_flicker() {
	?>
	<script>
	(function(){
		var t=localStorage.getItem('pas_theme');
		if(t==='dark'){document.documentElement.classList.add('dark-mode');}
		var c=localStorage.getItem('pas_color');
		if(c){try{var d=JSON.parse(c);var hex=/^#[0-9A-Fa-f]{6}$/;if(hex.test(d.primary)&&hex.test(d.light)){var r=document.documentElement.style;r.setProperty('--primary',d.primary);r.setProperty('--primary-light',d.light);if(d.bg&&/^(#[0-9A-Fa-f]{6}|rgba?\()/.test(d.bg)){r.setProperty('--primary-bg',d.bg);}}}catch(e){}}
		document.documentElement.classList.add('js');
		window._pasRevealFailsafe=setTimeout(function(){var e=document.querySelectorAll('.pas-reveal');for(var i=0;i<e.length;i++){e[i].classList.add('pas-visible');}},2000);
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'appstorepro_anti_flicker', 1 );

// Include inc files
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/taxonomies.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/apk-extractor.php';
require get_template_directory() . '/inc/shortcodes.php';

// Flush rewrite rules when the theme is activated.
//
// Without flushing, custom taxonomies such as `app-category` may not have
// their rewrite rules registered properly, which can lead to 404 errors on
// pages like `/app-category/` or `/app-category/games/`. The
// `after_switch_theme` hook runs once whenever the active theme is changed,
// allowing us to call `flush_rewrite_rules()` safely. See
// https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
// for details.
function appstorepro_flush_rewrite_on_switch() {
    /*
     * Flush rewrite rules when switching themes. We deliberately avoid
     * re-triggering the entire `init` hook here. Calling
     * `do_action( 'init' )` can inadvertently execute every callback
     * registered on `init` (including from plugins), which may consume
     * significant memory and time. Instead, rely on the fact that by
     * the time this hook runs, the theme's post types and taxonomies
     * have already been registered in the current request, so a simple
     * flush is sufficient. See https://developer.wordpress.org/reference/hooks/after_switch_theme/.
     */
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'appstorepro_flush_rewrite_on_switch' );

// Content width
if ( ! isset( $content_width ) ) {
	$content_width = 1200;
}
