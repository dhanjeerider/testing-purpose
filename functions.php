<?php
/**
 * Theme Functions
 */

// Theme Setup
function news_theme_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Image sizes
    add_image_size('news-large', 1200, 675, true);
    add_image_size('news-medium', 800, 450, true);
    add_image_size('news-small', 400, 225, true);
    add_image_size('news-thumb', 300, 200, true);
    
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu'
    ));
}
add_action('after_setup_theme', 'news_theme_setup');

// Widget Areas
function news_theme_widgets_init() {
    register_sidebar(array(
        'name'          => 'Main Sidebar',
        'id'            => 'main-sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => 'Footer Widget ' . $i,
            'id'            => 'footer-widget-' . $i,
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
}
add_action('widgets_init', 'news_theme_widgets_init');

// Enqueue Scripts and Styles
function news_theme_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_style('news-theme-style', get_template_directory_uri() . '/assets/css/style.css', array('bootstrap'), '1.0.0');
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
    wp_enqueue_script('news-theme-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'news_theme_scripts');
