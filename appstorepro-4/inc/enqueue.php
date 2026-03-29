<?php
// inc/enqueue.php — Enqueue scripts and styles

function appstorepro_enqueue_scripts() {
	$ver = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'appstorepro-boxicons',
		'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css',
		[],
		'2.1.4'
	);

	wp_enqueue_style(
		'appstorepro-main',
		get_template_directory_uri() . '/assets/css/main.css',
		[],
		$ver
	);

	wp_enqueue_script(
		'appstorepro-main',
		get_template_directory_uri() . '/assets/js/main.js',
		[],
		$ver,
		true
	);

	wp_localize_script( 'appstorepro-main', 'AppStorePro', [
		'homeUrl'    => esc_url( home_url( '/' ) ),
		'ajaxUrl'    => esc_url( admin_url( 'admin-ajax.php' ) ),
		'isRtl'      => is_rtl() ? 'true' : 'false',
		'nonce'      => wp_create_nonce( 'appstorepro_nonce' ),
	] );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'appstorepro_enqueue_scripts' );
