<?php
// inc/enqueue.php — Enqueue scripts and styles for AppStore Pro V5

function aspv5_enqueue_scripts() {
	$ver = wp_get_theme()->get( 'Version' );

	// Boxicons
	wp_enqueue_style(
		'aspv5-boxicons',
		'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css',
		[],
		'2.1.4'
	);

	// Theme base styles (minimal overrides for things Tailwind can't do)
	wp_enqueue_style(
		'aspv5-base',
		get_template_directory_uri() . '/assets/css/base.css',
		[],
		$ver
	);

	// Main JS
	wp_enqueue_script(
		'aspv5-main',
		get_template_directory_uri() . '/assets/js/main.js',
		[],
		$ver,
		true
	);

	wp_localize_script( 'aspv5-main', 'AspV5', [
		'homeUrl'        => esc_url( home_url( '/' ) ),
		'ajaxUrl'        => esc_url( admin_url( 'admin-ajax.php' ) ),
		'isRtl'          => is_rtl() ? 'true' : 'false',
		'nonce'          => wp_create_nonce( 'aspv5_nonce' ),
		'defaultLayout'  => esc_js( get_theme_mod( 'aspv5_default_layout', 'grid' ) ),
		'colorThemes'    => wp_json_encode( aspv5_get_color_themes() ),
	] );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'aspv5_enqueue_scripts' );

// Enqueue Tailwind CDN in the <head> (must run before wp_enqueue_scripts)
function aspv5_enqueue_tailwind() {
	?>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
	tailwind.config = {
		darkMode: 'class',
		theme: {
			extend: {
				colors: {
					primary: 'var(--asp-primary)',
				},
				fontFamily: {
					display: ['"Google Sans"', '"SF Pro Display"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
					body:    ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
				},
				borderRadius: {
					'2xl': '1rem',
					'3xl': '1.25rem',
				},
			},
		},
	};
	</script>
	<?php
}
add_action( 'wp_head', 'aspv5_enqueue_tailwind', 2 );

// APK Extractor JS
function aspv5_enqueue_apk_extractor() {
	$ver = wp_get_theme()->get( 'Version' );
	wp_enqueue_script(
		'aspv5-apk-extractor',
		get_template_directory_uri() . '/assets/js/apk-extractor.js',
		[],
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'aspv5_enqueue_apk_extractor' );

// Category image uploader script (admin only)
function aspv5_enqueue_category_media() {
	$screen = get_current_screen();
	if ( $screen && 'edit-app-category' === $screen->id ) {
		wp_enqueue_media();
		wp_enqueue_script( 'aspv5-category-image',
			get_template_directory_uri() . '/assets/js/category-image.js',
			[ 'jquery', 'media-upload', 'media-views' ],
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'aspv5_enqueue_category_media' );
