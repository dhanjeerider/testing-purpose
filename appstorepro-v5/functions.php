<?php
// functions.php — AppStore Pro V5

function aspv5_setup() {
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
		'primary'    => __( 'Primary Menu', 'aspv5' ),
		'slide-menu' => __( 'Slide Menu', 'aspv5' ),
		'legal-menu' => __( 'Legal Menu', 'aspv5' ),
		'footer-nav' => __( 'Footer Navigation', 'aspv5' ),
	] );

	load_theme_textdomain( 'aspv5', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'aspv5_setup' );

// Widget Areas
function aspv5_widgets_init() {
	register_sidebar( [
		'name'          => __( 'Sidebar', 'aspv5' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s mb-6">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title text-base font-bold mb-3 text-gray-800 dark:text-gray-100">',
		'after_title'   => '</h2>',
	] );
	register_sidebar( [
		'name'          => __( 'Footer Widget Area', 'aspv5' ),
		'id'            => 'footer-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s mb-6">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title text-sm font-bold mb-3 text-gray-700 dark:text-gray-300 uppercase tracking-wider">',
		'after_title'   => '</h3>',
	] );
}
add_action( 'widgets_init', 'aspv5_widgets_init' );

// Anti-flicker inline script (dark mode + color theme)
function aspv5_anti_flicker() {
	?>
	<script>
	(function(){
		var t=localStorage.getItem('aspv5_theme');
		if(t==='dark'){document.documentElement.classList.add('dark');}
		var c=localStorage.getItem('aspv5_color');
		if(c){try{var d=JSON.parse(c);var hex=/^#[0-9A-Fa-f]{6}$/;if(hex.test(d.primary)){document.documentElement.style.setProperty('--asp-primary',d.primary);document.documentElement.style.setProperty('--asp-primary-light',d.light||d.primary);}}catch(e){}}
		document.documentElement.classList.add('js');
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'aspv5_anti_flicker', 1 );

// Include inc files
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/taxonomies.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/apk-extractor.php';
require get_template_directory() . '/inc/shortcodes.php';
require get_template_directory() . '/inc/widgets.php';

// Flush rewrite rules on theme activation
function aspv5_flush_rewrite_on_switch() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'aspv5_flush_rewrite_on_switch' );

if ( ! isset( $content_width ) ) {
	$content_width = 1200;
}
