<?php
/**
 * AJAX News Theme Functions
 */

// Theme Setup
function news_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Image sizes for consistent display
    add_image_size('news-large', 1200, 675, true);      // 16:9 ratio
    add_image_size('news-medium', 800, 450, true);      // 16:9 ratio
    add_image_size('news-small', 400, 225, true);       // 16:9 ratio
    add_image_size('news-thumb', 300, 200, true);       // 3:2 ratio
    add_image_size('news-square', 400, 400, true);      // 1:1 ratio
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu'
    ));
}
add_action('after_setup_theme', 'news_theme_setup');

// Register Widget Areas (Hooks)
function news_theme_widgets_init() {
    
    // Header Widget Area (Below Header)
    register_sidebar(array(
        'name'          => 'Header Widget Area',
        'id'            => 'header-widget-area',
        'description'   => 'Widget area below header - perfect for breaking news ticker',
        'before_widget' => '<div id="%1$s" class="widget header-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Before Content Widget Area
    register_sidebar(array(
        'name'          => 'Before Content',
        'id'            => 'before-content',
        'description'   => 'Widget area before main content',
        'before_widget' => '<div id="%1$s" class="widget before-content-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // After Content Widget Area
    register_sidebar(array(
        'name'          => 'After Content',
        'id'            => 'after-content',
        'description'   => 'Widget area after main content',
        'before_widget' => '<div id="%1$s" class="widget after-content-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Sidebar Widget Area
    register_sidebar(array(
        'name'          => 'Main Sidebar',
        'id'            => 'main-sidebar',
        'description'   => 'Main sidebar widget area',
        'before_widget' => '<div id="%1$s" class="widget sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Footer Widget Areas (4 columns)
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf('Footer Widget Area %d', $i),
            'id'            => sprintf('footer-widget-%d', $i),
            'description'   => sprintf('Footer widget area column %d', $i),
            'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
    
    // Homepage Widget Areas
    register_sidebar(array(
        'name'          => 'Homepage Top',
        'id'            => 'homepage-top',
        'description'   => 'Widget area at top of homepage',
        'before_widget' => '<div id="%1$s" class="widget homepage-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title section-title">',
        'after_title'   => '</h2>',
    ));
    
    register_sidebar(array(
        'name'          => 'Homepage Middle',
        'id'            => 'homepage-middle',
        'description'   => 'Widget area in middle of homepage',
        'before_widget' => '<div id="%1$s" class="widget homepage-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title section-title">',
        'after_title'   => '</h2>',
    ));
    
    register_sidebar(array(
        'name'          => 'Homepage Bottom',
        'id'            => 'homepage-bottom',
        'description'   => 'Widget area at bottom of homepage',
        'before_widget' => '<div id="%1$s" class="widget homepage-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title section-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'news_theme_widgets_init');

// Enqueue Styles and Scripts
function news_theme_enqueue_scripts() {
    // CSS Files
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_style('material-design', get_template_directory_uri() . '/assets/css/material-design.css', array('bootstrap'), '1.0.0');
    wp_enqueue_style('news-theme', get_template_directory_uri() . '/assets/css/news-theme.css', array('material-design'), '1.0.0');
    
    // JavaScript Files
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
    wp_enqueue_script('news-theme', get_template_directory_uri() . '/assets/js/news-theme.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'news_theme_enqueue_scripts');

// Custom Widgets
require_once get_template_directory() . '/inc/widgets/class-recent-posts-widget.php';
require_once get_template_directory() . '/inc/widgets/class-category-posts-widget.php';
require_once get_template_directory() . '/inc/widgets/class-trending-posts-widget.php';

// Template Tags
require_once get_template_directory() . '/inc/template-tags.php';

// Register Custom Widgets
function news_theme_register_widgets() {
    register_widget('News_Recent_Posts_Widget');
    register_widget('News_Category_Posts_Widget');
    register_widget('News_Trending_Posts_Widget');
}
add_action('widgets_init', 'news_theme_register_widgets');
