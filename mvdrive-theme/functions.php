<?php
/**
 * Functions and definitions for the mvdrive theme.
 *
 * This file sets up theme defaults and registers support for various
 * WordPress features.  It also defines helper functions for rendering the
 * notice and Telegram call‑to‑action sections.  Options for these sections
 * are exposed via the WordPress Customizer.  See the Customizer
 * documentation for more information on how to register settings and
 * controls【297308509864910†L59-L63】.
 */

if ( ! function_exists( 'mvdrive_theme_setup' ) ) {
    /**
     * Sets up theme defaults and registers support for various WordPress
     * features.
     *
     * Runs on the `after_setup_theme` hook.
     */
    function mvdrive_theme_setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );

        // Register navigation menus.  Although the sample header uses static
        // markup, registering menus allows users to assign their own menus
        // via the WordPress admin area.
        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'mvdrive' ),
            'side'    => __( 'Side Menu', 'mvdrive' ),
        ) );
    }
}
add_action( 'after_setup_theme', 'mvdrive_theme_setup' );

/**
 * Enqueue theme scripts and styles.
 */
function mvdrive_enqueue_scripts() {
    // Enqueue the main stylesheet
    wp_enqueue_style( 'mvdrive-style', get_stylesheet_uri(), array(), '2.0' );
    
    // Enqueue theme JavaScript
    wp_enqueue_script( 
        'mvdrive-theme-js', 
        get_template_directory_uri() . '/assets/theme.js', 
        array(), 
        '2.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'mvdrive_enqueue_scripts' );

/**
 * Register widget areas.  Widgets will appear in the sidebar of single
 * posts, as shown in the single.php template.
 */
function mvdrive_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'mvdrive' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Widgets in this area will be shown on single posts.', 'mvdrive' ),
        'before_widget' => '<div class="sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'mvdrive_widgets_init' );

/**
 * Register settings and controls with the WordPress Customizer.  The
 * Customizer provides a unified interface for users to customize various
 * aspects of their theme【297308509864910†L59-L63】.  Here we expose settings for
 * the site‑wide notice and for the Telegram call‑to‑action, allowing
 * administrators to adjust the text and URLs without editing theme files.
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object.
 */
function mvdrive_customize_register( $wp_customize ) {
    /*
     * Notice Section
     */
    $wp_customize->add_section( 'mvdrive_notice_section', array(
        'title'       => __( 'Notice Section', 'mvdrive' ),
        'description' => __( 'Customize the notice text and link displayed on the home page.', 'mvdrive' ),
        'priority'    => 30,
    ) );

    // Notice text setting and control.
    $wp_customize->add_setting( 'mvdrive_notice_text', array(
        'default'           => __( 'Avoid fake copies of our site. Bookmark our official domain.', 'mvdrive' ),
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'mvdrive_notice_text', array(
        'label'    => __( 'Notice Text', 'mvdrive' ),
        'section'  => 'mvdrive_notice_section',
        'type'     => 'textarea',
    ) );

    // Notice link text setting and control.
    $wp_customize->add_setting( 'mvdrive_notice_link_text', array(
        'default'           => __( 'Official Site', 'mvdrive' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'mvdrive_notice_link_text', array(
        'label'    => __( 'Notice Link Text', 'mvdrive' ),
        'section'  => 'mvdrive_notice_section',
        'type'     => 'text',
    ) );

    // Notice link URL setting and control.
    $wp_customize->add_setting( 'mvdrive_notice_link_url', array(
        'default'           => home_url( '/' ),
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'mvdrive_notice_link_url', array(
        'label'    => __( 'Notice Link URL', 'mvdrive' ),
        'section'  => 'mvdrive_notice_section',
        'type'     => 'url',
    ) );

    /*
     * Telegram Call‑to‑Action Section
     */
    $wp_customize->add_section( 'mvdrive_telegram_section', array(
        'title'       => __( 'Telegram Call To Action', 'mvdrive' ),
        'description' => __( 'Customize the Telegram button text and link that appear on single posts.', 'mvdrive' ),
        'priority'    => 40,
    ) );

    // Telegram title text.
    $wp_customize->add_setting( 'mvdrive_telegram_title', array(
        'default'           => __( 'Join our Telegram Channel', 'mvdrive' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'mvdrive_telegram_title', array(
        'label'    => __( 'Telegram Title', 'mvdrive' ),
        'section'  => 'mvdrive_telegram_section',
        'type'     => 'text',
    ) );

    // Telegram subtitle text.
    $wp_customize->add_setting( 'mvdrive_telegram_subtitle', array(
        'default'           => __( 'Get instant updates on new releases', 'mvdrive' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'mvdrive_telegram_subtitle', array(
        'label'    => __( 'Telegram Subtitle', 'mvdrive' ),
        'section'  => 'mvdrive_telegram_section',
        'type'     => 'text',
    ) );

    // Telegram link URL.
    $wp_customize->add_setting( 'mvdrive_telegram_link', array(
        'default'           => 'https://t.me/yourchannel',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'mvdrive_telegram_link', array(
        'label'    => __( 'Telegram Link URL', 'mvdrive' ),
        'section'  => 'mvdrive_telegram_section',
        'type'     => 'url',
    ) );
}
add_action( 'customize_register', 'mvdrive_customize_register' );

/**
 * Output the notice section HTML.  Call this function from a template
 * (typically index.php) to display the notice.  The HTML uses the
 * classes from the original MoviesDrive layout so that the user’s CSS
 * continues to apply.  The text and link values are pulled from the
 * Customizer settings defined above.
 */
function mvdrive_display_notice() {
    $notice_text  = get_theme_mod( 'mvdrive_notice_text' );
    $link_text    = get_theme_mod( 'mvdrive_notice_link_text' );
    $link_url     = get_theme_mod( 'mvdrive_notice_link_url' );

    if ( empty( $notice_text ) ) {
        return;
    }

    echo '<div class="notice-section">';
    echo '<div class="notice-icon">';
    // Simple info icon SVG copied from the original markup.
    echo '<svg viewBox="0 0 24 24" fill="none"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"></path></svg>';
    echo '</div>';
    echo '<p class="notice-text">';
    echo wp_kses_post( $notice_text );
    if ( $link_text && $link_url ) {
        echo ' <a href="' . esc_url( $link_url ) . '" class="notice-link"><strong>' . esc_html( $link_text ) . '</strong></a>';
    }
    echo '</p>';
    echo '<button class="notice-close" id="noticeClose"><svg viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2"></path></svg></button>';
    echo '</div>';
}

/**
 * Output the Telegram call‑to‑action HTML.  Call this function from
 * single.php to display a button encouraging users to join the Telegram
 * channel.  The title, subtitle and link are pulled from the Customizer
 * settings defined above.  The markup retains the original classes to
 * ensure compatibility with the existing CSS.
 */
function mvdrive_display_telegram_cta() {
    $title    = get_theme_mod( 'mvdrive_telegram_title' );
    $subtitle = get_theme_mod( 'mvdrive_telegram_subtitle' );
    $link     = get_theme_mod( 'mvdrive_telegram_link' );

    if ( ! $title && ! $subtitle && ! $link ) {
        return;
    }

    echo '<div class="telegram-cta">';
    echo '<svg class="telegram-icon" viewBox="0 0 24 24" fill="currentColor">';
    echo '<path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.697.064-1.226-.461-1.901-.903-1.056-.692-1.653-1.123-2.678-1.799-1.185-.781-.417-1.21.258-1.911.177-.184 3.247-2.977 3.307-3.23.007-.032.015-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.139-5.062 3.345-.479.329-.913.489-1.302.481-.428-.009-1.252-.242-1.865-.44-.751-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.015 3.333-1.386 4.025-1.627 4.477-1.635.099-.002.321.023.465.141.121.099.154.232.17.324.015.093.034.305.019.471z"></path>';
    echo '</svg>';
    echo '<div class="telegram-content">';
    if ( $title ) {
        echo '<span class="telegram-title">' . esc_html( $title ) . '</span>';
    }
    if ( $subtitle ) {
        echo '<span class="telegram-subtitle">' . esc_html( $subtitle ) . '</span>';
    }
    echo '</div>';
    if ( $link ) {
        echo '<a href="' . esc_url( $link ) . '" target="_blank" class="telegram-btn">Join Now</a>';
    }
    echo '</div>';
}

?>