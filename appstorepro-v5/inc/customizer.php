<?php
// inc/customizer.php — AppStore Pro V5 Customizer options

function aspv5_customize_register( $wp_customize ) {

	// ── Panel ─────────────────────────────────────────────────────────────────
	$wp_customize->add_panel( 'aspv5_theme_settings', [
		'title'    => __( 'Theme Settings', 'aspv5' ),
		'priority' => 130,
	] );

	// ── Header ───────────────────────────────────────────────────────────────
	$wp_customize->add_section( 'aspv5_header', [
		'title' => __( 'Header Settings', 'aspv5' ),
		'panel' => 'aspv5_theme_settings',
	] );
	$wp_customize->add_setting( 'aspv5_logo_url', [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
	$wp_customize->add_control( 'aspv5_logo_url', [
		'label'   => __( 'Logo URL', 'aspv5' ),
		'section' => 'aspv5_header',
		'type'    => 'url',
	] );
	$wp_customize->add_setting( 'aspv5_site_name', [ 'default' => get_bloginfo( 'name' ), 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'aspv5_site_name', [
		'label'   => __( 'Site Name', 'aspv5' ),
		'section' => 'aspv5_header',
		'type'    => 'text',
	] );
	$wp_customize->add_setting( 'aspv5_tagline', [ 'default' => get_bloginfo( 'description' ), 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'aspv5_tagline', [
		'label'   => __( 'Tagline', 'aspv5' ),
		'section' => 'aspv5_header',
		'type'    => 'text',
	] );

	// ── Colors ───────────────────────────────────────────────────────────────
	$wp_customize->add_section( 'aspv5_colors', [
		'title' => __( 'Colors', 'aspv5' ),
		'panel' => 'aspv5_theme_settings',
	] );
	$wp_customize->add_setting( 'aspv5_primary_color', [
		'default'           => '#FF6A00',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	] );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'aspv5_primary_color', [
		'label'   => __( 'Primary Color', 'aspv5' ),
		'section' => 'aspv5_colors',
	] ) );

	// ── Layout Options ───────────────────────────────────────────────────────
	$wp_customize->add_section( 'aspv5_layout', [
		'title'       => __( 'Layout Options', 'aspv5' ),
		'panel'       => 'aspv5_theme_settings',
		'description' => __( 'Control how apps and games are displayed on archive, category and search pages.', 'aspv5' ),
	] );

	$wp_customize->add_setting( 'aspv5_default_layout', [
		'default'           => 'grid',
		'sanitize_callback' => 'aspv5_sanitize_layout',
	] );
	$wp_customize->add_control( 'aspv5_default_layout', [
		'label'   => __( 'Default Archive Layout', 'aspv5' ),
		'section' => 'aspv5_layout',
		'type'    => 'select',
		'choices' => [
			'grid'    => __( 'Grid (2–3 columns)', 'aspv5' ),
			'list'    => __( 'List (rows)', 'aspv5' ),
			'banner'  => __( 'Banner / Poster (backdrop image)', 'aspv5' ),
			'compact' => __( 'Compact (4+ columns)', 'aspv5' ),
		],
	] );

	$wp_customize->add_setting( 'aspv5_show_layout_switcher', [
		'default'           => '1',
		'sanitize_callback' => 'aspv5_sanitize_checkbox',
	] );
	$wp_customize->add_control( 'aspv5_show_layout_switcher', [
		'label'   => __( 'Show layout switcher on archive pages', 'aspv5' ),
		'section' => 'aspv5_layout',
		'type'    => 'checkbox',
	] );

	$wp_customize->add_setting( 'aspv5_apps_per_page', [
		'default'           => '24',
		'sanitize_callback' => 'absint',
	] );
	$wp_customize->add_control( 'aspv5_apps_per_page', [
		'label'   => __( 'Items per archive page', 'aspv5' ),
		'section' => 'aspv5_layout',
		'type'    => 'number',
	] );

	// ── Homepage ─────────────────────────────────────────────────────────────
	$wp_customize->add_section( 'aspv5_homepage', [
		'title' => __( 'Homepage Settings', 'aspv5' ),
		'panel' => 'aspv5_theme_settings',
	] );
	$wp_customize->add_setting( 'aspv5_hero_title', [ 'default' => 'Premium Mods,', 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'aspv5_hero_title', [
		'label'   => __( 'Hero Title', 'aspv5' ),
		'section' => 'aspv5_homepage',
		'type'    => 'text',
	] );
	$wp_customize->add_setting( 'aspv5_hero_subtitle', [ 'default' => 'Ad-free apps, unlocked games & unlimited resources for Android.', 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'aspv5_hero_subtitle', [
		'label'   => __( 'Hero Subtitle', 'aspv5' ),
		'section' => 'aspv5_homepage',
		'type'    => 'textarea',
	] );
	$wp_customize->add_setting( 'aspv5_hero_badge', [ 'default' => '★ TRUSTED MOD STORE', 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'aspv5_hero_badge', [
		'label'   => __( 'Hero Badge Text', 'aspv5' ),
		'section' => 'aspv5_homepage',
		'type'    => 'text',
	] );
	$wp_customize->add_setting( 'aspv5_featured_layout', [
		'default'           => 'banner',
		'sanitize_callback' => 'aspv5_sanitize_layout',
	] );
	$wp_customize->add_control( 'aspv5_featured_layout', [
		'label'   => __( 'Featured Apps Card Style', 'aspv5' ),
		'section' => 'aspv5_homepage',
		'type'    => 'select',
		'choices' => [
			'banner'  => __( 'Banner / Poster', 'aspv5' ),
			'grid'    => __( 'Grid Cards', 'aspv5' ),
			'compact' => __( 'Compact', 'aspv5' ),
		],
	] );

	// ── Footer ───────────────────────────────────────────────────────────────
	$wp_customize->add_section( 'aspv5_footer', [
		'title' => __( 'Footer Settings', 'aspv5' ),
		'panel' => 'aspv5_theme_settings',
	] );
	$wp_customize->add_setting( 'aspv5_footer_text', [ 'default' => '© ' . gmdate( 'Y' ) . ' AppStore Pro V5. All rights reserved.', 'sanitize_callback' => 'wp_kses_post' ] );
	$wp_customize->add_control( 'aspv5_footer_text', [
		'label'   => __( 'Footer Text', 'aspv5' ),
		'section' => 'aspv5_footer',
		'type'    => 'textarea',
	] );
	$wp_customize->add_setting( 'aspv5_footer_telegram_url', [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
	$wp_customize->add_control( 'aspv5_footer_telegram_url', [
		'label'   => __( 'Telegram URL', 'aspv5' ),
		'section' => 'aspv5_footer',
		'type'    => 'url',
	] );
	$wp_customize->add_setting( 'aspv5_footer_telegram_members', [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'aspv5_footer_telegram_members', [
		'label'   => __( 'Telegram Members Count', 'aspv5' ),
		'section' => 'aspv5_footer',
		'type'    => 'text',
	] );

	// ── Social Links ─────────────────────────────────────────────────────────
	$wp_customize->add_section( 'aspv5_social', [
		'title' => __( 'Social Links', 'aspv5' ),
		'panel' => 'aspv5_theme_settings',
	] );
	$wp_customize->add_setting( 'aspv5_social_telegram', [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
	$wp_customize->add_control( 'aspv5_social_telegram', [
		'label'   => __( 'Telegram URL', 'aspv5' ),
		'section' => 'aspv5_social',
		'type'    => 'url',
	] );
	$wp_customize->add_setting( 'aspv5_social_youtube', [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
	$wp_customize->add_control( 'aspv5_social_youtube', [
		'label'   => __( 'YouTube URL', 'aspv5' ),
		'section' => 'aspv5_social',
		'type'    => 'url',
	] );
}
add_action( 'customize_register', 'aspv5_customize_register' );

// Sanitize layout option
function aspv5_sanitize_layout( $value ) {
	$valid = [ 'grid', 'list', 'banner', 'compact' ];
	return in_array( $value, $valid, true ) ? $value : 'grid';
}

// Sanitize checkbox
function aspv5_sanitize_checkbox( $value ) {
	return ( '1' === $value || true === $value ) ? '1' : '0';
}
