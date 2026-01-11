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
        'custom-header' => __('Custom Header Menu', 'ajax-news-theme'),
        'primary' => __('Main Mobile Menu', 'ajax-news-theme'),
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
    
    // Material Design CSS
    wp_enqueue_style('material-design', get_template_directory_uri() . '/assets/css/material-design.css', array(), '1.0.0');
    
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
    
    // Header Enhancements
    wp_enqueue_script('header-enhancements', get_template_directory_uri() . '/assets/js/header-enhancements.js', array('jquery'), '1.0.0', true);
    
    // Load More
    wp_enqueue_script('load-more', get_template_directory_uri() . '/assets/js/load-more.js', array('jquery'), '1.0.0', true);
    
    // Layout Switcher JS
    wp_enqueue_script('layout-switcher', get_template_directory_uri() . '/assets/js/layout-switcher.js', array('jquery'), '1.0.0', true);
    
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
    
    // Parse URL to get query
    $url_parts = parse_url($url);
    $path = isset($url_parts['path']) ? $url_parts['path'] : '';
    
    // Get post ID from URL
    $post_id = url_to_postid($url);
    
    ob_start();
    
    if ($post_id) {
        // Single post - load actual template
        query_posts(array('p' => $post_id));
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                get_template_part('template-parts/content', 'single-ajax');
            }
        }
        wp_reset_query();
    } else {
        // Archive or home page
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        query_posts(array('paged' => $paged));
        
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                get_template_part('template-parts/content', 'loop');
            }
        }
        wp_reset_query();
    }
    
    $content = ob_get_clean();
    
    wp_send_json_success(array(
        'content' => $content,
        'title' => $post_id ? get_the_title($post_id) : get_bloginfo('name'),
    ));
}
add_action('wp_ajax_load_content', 'ajax_load_content');
add_action('wp_ajax_nopriv_load_content', 'ajax_load_content');

// Include Bootstrap Nav Walker
if (file_exists(get_template_directory() . '/inc/bootstrap-navwalker.php')) {
    require_once get_template_directory() . '/inc/bootstrap-navwalker.php';
}

// Include Shortcodes
if (file_exists(get_template_directory() . '/inc/shortcodes.php')) {
    require_once get_template_directory() . '/inc/shortcodes.php';
}

// Include Reactions Functions
if (file_exists(get_template_directory() . '/inc/reactions.php')) {
    require_once get_template_directory() . '/inc/reactions.php';
}

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

// Add Schema Markup
function ajax_news_schema_markup() {
    if (is_single()) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => get_the_title(),
            'image' => get_the_post_thumbnail_url($post->ID, 'full'),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url(),
                ),
            ),
            'description' => get_the_excerpt(),
        );
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'ajax_news_schema_markup');

// Add Open Graph Tags
function ajax_news_og_tags() {
    if (is_single()) {
        global $post;
        echo '<meta property="og:type" content="article" />';
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />';
        echo '<meta property="og:description" content="' . esc_attr(get_the_excerpt()) . '" />';
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />';
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />';
        
        if (has_post_thumbnail()) {
            $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
            echo '<meta property="og:image" content="' . esc_url($thumbnail) . '" />';
            echo '<meta property="og:image:width" content="1200" />';
            echo '<meta property="og:image:height" content="630" />';
        }
        
        // Twitter Cards
        echo '<meta name="twitter:card" content="summary_large_image" />';
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '" />';
        echo '<meta name="twitter:description" content="' . esc_attr(get_the_excerpt()) . '" />';
        if (has_post_thumbnail()) {
            echo '<meta name="twitter:image" content="' . esc_url($thumbnail) . '" />';
        }
    }
}
add_action('wp_head', 'ajax_news_og_tags');

// Load More Posts AJAX
function ajax_news_load_more_posts() {
    check_ajax_referer('ajax_news_nonce', 'nonce');
    
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $layout = isset($_POST['layout']) ? sanitize_text_field($_POST['layout']) : 'grid';
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 6,
        'paged' => $paged,
        'post_status' => 'publish',
    );
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/content', $layout);
        }
    }
    
    $content = ob_get_clean();
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'content' => $content,
        'has_more' => $paged < $query->max_num_pages,
    ));
}
add_action('wp_ajax_load_more_posts', 'ajax_news_load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'ajax_news_load_more_posts');

// Register Custom Gutenberg Blocks
function ajax_news_register_blocks() {
    // Register block category
    add_filter('block_categories_all', function($categories) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'ajax-news-blocks',
                    'title' => 'News Layouts',
                ),
            )
        );
    });
}
add_action('init', 'ajax_news_register_blocks');

// Live Search AJAX Handler
function ajax_news_live_search() {
    check_ajax_referer('ajax_news_nonce', 'nonce');
    
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    
    if (empty($query)) {
        wp_send_json_error();
    }
    
    $args = array(
        's' => $query,
        'posts_per_page' => 5,
        'post_status' => 'publish',
    );
    
    $search_query = new WP_Query($args);
    
    $html = '';
    
    if ($search_query->have_posts()) {
        $html .= '<div class="search-results-list">';
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $html .= '<a href="' . get_permalink() . '" class="search-result-item ajax-link">';
            if (has_post_thumbnail()) {
                $html .= get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'search-result-thumb'));
            }
            $html .= '<div class="search-result-content">';
            $html .= '<h6>' . get_the_title() . '</h6>';
            $html .= '<small>' . get_the_date() . '</small>';
            $html .= '</div>';
            $html .= '</a>';
        }
        $html .= '</div>';
    } else {
        $html = '<p class="text-center p-3 text-muted">No results found</p>';
    }
    
    wp_reset_postdata();
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_live_search', 'ajax_news_live_search');
add_action('wp_ajax_nopriv_live_search', 'ajax_news_live_search');

// Register Service Worker for PWA
function ajax_news_register_service_worker() {
    if (is_front_page() || is_home()) {
        ?>
        <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo get_template_directory_uri(); ?>/service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');
                })
                .catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
        </script>
        <?php
    }
}
add_action('wp_footer', 'ajax_news_register_service_worker');

// Theme Customizer
function ajax_news_customize_register($wp_customize) {
    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title' => __('Hero Section', 'ajax-news-theme'),
        'priority' => 30,
    ));
    
    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Breaking News & Latest Updates',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'ajax-news-theme'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    // Hero Subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Stay informed with real-time news coverage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Hero Subtitle', 'ajax-news-theme'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    // Show Hero Section
    $wp_customize->add_setting('show_hero', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('show_hero', array(
        'label' => __('Show Hero Section', 'ajax-news-theme'),
        'section' => 'hero_section',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'ajax_news_customize_register');
