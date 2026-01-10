<?php
/**
 * AJAX News Theme Functions
 */

// Theme Setup
function ajax_news_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'ajax-news-theme'),
        'footer' => __('Footer Menu', 'ajax-news-theme'),
    ));
    
    // Set thumbnail sizes
    set_post_thumbnail_size(800, 450, true);
    add_image_size('news-thumb', 400, 250, true);
    add_image_size('news-large', 1200, 675, true);
}
add_action('after_setup_theme', 'ajax_news_theme_setup');

// Enqueue Scripts and Styles
function ajax_news_theme_scripts() {
    // Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0');
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Theme Style
    wp_enqueue_style('ajax-news-theme-style', get_stylesheet_uri(), array('bootstrap'), '1.0.0');
    
    // Custom CSS
    wp_enqueue_style('ajax-news-custom', get_template_directory_uri() . '/assets/css/custom.css', array(), '1.0.0');
    
    // Bootstrap JS
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), '5.3.0', true);
    
    // Plyr.io Video Player
    wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.7.8/plyr.css', array(), '3.7.8');
    wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.7.8/plyr.js', array(), '3.7.8', true);
    
    // AJAX Navigation Script
    wp_enqueue_script('ajax-navigation', get_template_directory_uri() . '/assets/js/ajax-navigation.js', array('jquery'), '1.0.0', true);
    
    // Reactions Script
    wp_enqueue_script('reactions', get_template_directory_uri() . '/assets/js/reactions.js', array('jquery'), '1.0.0', true);
    
    // Dark Mode Script
    wp_enqueue_script('dark-mode', get_template_directory_uri() . '/assets/js/dark-mode.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('ajax-navigation', 'ajaxNewsTheme', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax_news_nonce'),
        'home_url' => home_url('/'),
    ));
    
    wp_localize_script('reactions', 'reactionsData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('reactions_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'ajax_news_theme_scripts');

// AJAX Load Content Handler
function ajax_load_content() {
    check_ajax_referer('ajax_news_nonce', 'nonce');
    
    $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
    
    if (empty($url)) {
        wp_send_json_error('Invalid URL');
    }
    
    // Get post ID from URL
    $post_id = url_to_postid($url);
    
    if ($post_id) {
        // Single post
        $post = get_post($post_id);
        setup_postdata($post);
        
        ob_start();
        get_template_part('template-parts/content', 'single');
        $content = ob_get_clean();
        
        wp_reset_postdata();
    } else {
        // Archive or home page
        ob_start();
        if (is_home() || is_front_page()) {
            get_template_part('template-parts/content', 'archive');
        }
        $content = ob_get_clean();
    }
    
    wp_send_json_success(array(
        'content' => $content,
        'title' => get_the_title($post_id),
    ));
}
add_action('wp_ajax_load_content', 'ajax_load_content');
add_action('wp_ajax_nopriv_load_content', 'ajax_load_content');

// Include Bootstrap Nav Walker
require_once get_template_directory() . '/inc/bootstrap-navwalker.php';

// Include Shortcodes
require_once get_template_directory() . '/inc/shortcodes.php';

// Include Reactions Functions
require_once get_template_directory() . '/inc/reactions.php';

// Register Widget Areas
function ajax_news_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'ajax-news-theme'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here.', 'ajax-news-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 1', 'ajax-news-theme'),
        'id'            => 'footer-1',
        'description'   => __('Footer widget area 1', 'ajax-news-theme'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'ajax_news_widgets_init');

// Custom Excerpt Length
function ajax_news_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'ajax_news_excerpt_length');

// Custom Excerpt More
function ajax_news_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'ajax_news_excerpt_more');
