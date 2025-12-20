<?php 
/**
 * Bootstrap Pagination
 */
function dhanjee_pagination() {
    global $wp_query;

    $big = 999999999;

    $pages = paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'prev_text'  => __('<i class="material-icons">&#xE314;</i><span class="material-text">Previous</span>'),
        'next_text'  => __('<span class="material-left">Next</span><i class="material-icons">&#xE315;</i>'),
        'type'  => 'array',
    ) );
    
    if( is_array( $pages ) ) {
        echo '<div class="pagination-wrap"><ul class="pagination">';
        foreach ( $pages as $page ) {
            echo "<li>$page</li>";
        }
        echo '</ul></div>';
    }
}

/**
 * Boost site performance
 */
function dhanjee_boost_site(){
    add_filter('style_loader_src', 'dhanjee_remove_version', 9999 );
    add_filter('script_loader_src', 'dhanjee_remove_version', 9999 );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' ); 
}

/**
 * Remove version from assets
 */
function dhanjee_remove_version($src){
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}

/**
 * Get first image from post content
 */
function catch_that_image($post = null) {
    if(!$post) global $post;
    
    $first_img = '';
    $content = $post->post_content ?? '';
    
    if(!empty($content)) {
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
        $first_img = $matches[1][0] ?? '';
    }

    if(empty($first_img)) {
        $first_img = get_template_directory_uri().'/assets/default.png';
    }
    
    return $first_img;
}

/**
 * Theme intial setup
 */
function dhanjee_theme_setup(){
    // register navigation menu
    register_nav_menu( 'primary', __( 'Header Menu', '9xmovies' ) );
    register_nav_menu( 'footer', __( 'Footer Menu', '9xmovies' ) );
}

/**
 * Widget init
 */
function dhanjee_widgets_init(){
    $args = array(
        'name' => __( 'Main Sidebar', '9xmovies' ),
        'id' => 'sidebar',
        'description' => __( 'Widgets in this area will be shown on all posts and pages.', '9xmovies' ),
        'before_widget' => '<div class="widget-title">',
        'after_widget'  => '</div>',
        'before_title'  => '<i class="material-icons">&#xE1BD;</i>&nbsp;<span class="material-text">',
        'after_title'   => '</span></div><div class="widget-body">',
    );
    register_sidebar($args);
}

/**
 * Theme style and js setup
 */
function dhanjee_site_enqueue(){
    // Main theme stylesheet
    wp_enqueue_style('dhanjee-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    
    // Google fonts and Material Icons
    wp_enqueue_style('dhanjee-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap', array(), null);
    wp_enqueue_style('dhanjee-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), null);
    
    // jQuery (use WordPress bundled version)
    wp_enqueue_script('jquery');
    
    // Custom theme script
    wp_enqueue_script('dhanjee-script', get_template_directory_uri().'/script/script.min.js', array('jquery'), wp_get_theme()->get('Version'), true);
}

/**
 * Theme supports
 */
add_theme_support('title-tag');
add_theme_support('post-thumbnails');


/**
 * Admin Settings Page
 */
function dhanjee_admin_menu(){
    add_menu_page(
        '9xMovies Settings',
        '9xMovies Theme',
        'manage_options',
        'dhanjee-settings',
        'dhanjee_settings_page',
        'dashicons-admin-generic',
        60
    );
}

function dhanjee_settings_page(){
    if(isset($_POST['dhanjee_save_settings'])){
        $options = array(
            'site_logo' => sanitize_text_field($_POST['site_logo']),
            'favicon_icon' => sanitize_text_field($_POST['favicon_icon']),
            'header_code' => wp_kses_post($_POST['header_code']),
            'footer_code' => wp_kses_post($_POST['footer_code']),
            'custom_css' => sanitize_textarea_field($_POST['custom_css']),
            'custom_js' => sanitize_textarea_field($_POST['custom_js']),
            'boost_site' => isset($_POST['boost_site']) ? 'yes' : 'no',
            'site_notice' => wp_kses_post($_POST['site_notice']),
        );
        update_option('dhanjee_options', $options);
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    $opts = get_option('dhanjee_options', array(
        'site_logo' => get_template_directory_uri().'/assets/logo.png',
        'favicon_icon' => '',
        'header_code' => '',
        'footer_code' => '',
        'custom_css' => '',
        'custom_js' => '',
        'boost_site' => 'no',
        'site_notice' => '',
    ));
    ?>
    <div class="wrap">
        <h1>9xMovies Theme Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="site_logo">Site Logo URL</label></th>
                    <td>
                        <input type="text" name="site_logo" id="site_logo" value="<?php echo esc_attr($opts['site_logo']); ?>" class="regular-text">
                        <p class="description">Enter logo image URL</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="favicon_icon">Favicon Icon URL</label></th>
                    <td>
                        <input type="text" name="favicon_icon" id="favicon_icon" value="<?php echo esc_attr($opts['favicon_icon']); ?>" class="regular-text">
                        <p class="description">Enter favicon image URL</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="header_code">Header Code</label></th>
                    <td>
                        <textarea name="header_code" id="header_code" rows="5" class="large-text code"><?php echo esc_textarea($opts['header_code']); ?></textarea>
                        <p class="description">Code will be added to &lt;head&gt; section</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="footer_code">Footer Code</label></th>
                    <td>
                        <textarea name="footer_code" id="footer_code" rows="5" class="large-text code"><?php echo esc_textarea($opts['footer_code']); ?></textarea>
                        <p class="description">Code will be added before &lt;/body&gt;</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_css">Custom CSS</label></th>
                    <td>
                        <textarea name="custom_css" id="custom_css" rows="10" class="large-text code"><?php echo esc_textarea($opts['custom_css']); ?></textarea>
                        <p class="description">Add your custom CSS code</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_js">Custom JavaScript</label></th>
                    <td>
                        <textarea name="custom_js" id="custom_js" rows="10" class="large-text code"><?php echo esc_textarea($opts['custom_js']); ?></textarea>
                        <p class="description">Add your custom JavaScript code (without &lt;script&gt; tags)</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="site_notice">Site Notice</label></th>
                    <td>
                        <textarea name="site_notice" id="site_notice" rows="3" class="large-text"><?php echo esc_textarea($opts['site_notice']); ?></textarea>
                        <p class="description">Display notice message on site</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="boost_site">Boost Performance</label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="boost_site" id="boost_site" value="yes" <?php checked($opts['boost_site'], 'yes'); ?>>
                            Enable performance optimizations
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'dhanjee_save_settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Action & Filters
 */
add_action('init', 'dhanjee_theme_setup');
add_action('wp_enqueue_scripts', 'dhanjee_site_enqueue');
add_action('widgets_init', 'dhanjee_widgets_init');
add_action('admin_menu', 'dhanjee_admin_menu');

$get_options = get_option('dhanjee_options');

if(!empty($get_options) && isset($get_options['boost_site']) && $get_options['boost_site'] == 'yes')
    dhanjee_boost_site();

add_filter('the_content', 'content_filter_remove');

function content_filter_remove($content){
    $content = preg_replace('/<a[^>]+?href="(\/[^"]+?\.html)"\s*?target="_blank">/', '<a href="https://watchvideo.us/$1" target="_blank">', $content);
    return $content;
}