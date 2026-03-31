<?php
// inc/meta-boxes.php — App Details meta box

function aspv5_add_meta_boxes() {
	add_meta_box(
		'aspv5_app_details',
		__( 'App Details', 'aspv5' ),
		'aspv5_app_details_callback',
		[ 'app', 'game' ],
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'aspv5_add_meta_boxes' );

function aspv5_app_details_callback( $post ) {
	wp_nonce_field( 'aspv5_app_details_save', 'aspv5_app_details_nonce' );

	$fields = [
		[ 'key' => '_app_package',          'label' => 'Package ID (e.g. com.example.app)', 'type' => 'text' ],
		[ 'key' => '_app_version',          'label' => 'App Version',                'type' => 'text' ],
		[ 'key' => '_app_size',             'label' => 'File Size',                  'type' => 'text' ],
		[ 'key' => '_app_developer',        'label' => 'Developer',                  'type' => 'text' ],
		[ 'key' => '_app_downloads',        'label' => 'Downloads (e.g. 1M+)',      'type' => 'text' ],
		[ 'key' => '_app_icon_url',         'label' => 'App Icon URL',               'type' => 'url' ],
		[ 'key' => '_app_download_url',     'label' => 'Download URL',               'type' => 'url' ],
		[ 'key' => '_app_play_store_url',   'label' => 'Play Store URL',             'type' => 'url' ],
		[ 'key' => '_app_rating',           'label' => 'Rating (e.g. 4.5)',          'type' => 'text' ],
		[ 'key' => '_app_android_version',  'label' => 'Min Android Version',        'type' => 'text' ],
		[ 'key' => '_app_hero_image_url',   'label' => 'Hero/Banner Image URL',      'type' => 'url' ],
		[ 'key' => '_app_telegram_url',     'label' => 'Telegram Channel URL',       'type' => 'url' ],
		[ 'key' => '_app_telegram_members', 'label' => 'Telegram Members Count',     'type' => 'text' ],
		[ 'key' => '_app_youtube_url',      'label' => 'YouTube Tutorial URL',       'type' => 'url' ],
		[ 'key' => '_app_mod_info',         'label' => 'MOD Info',                   'type' => 'text' ],
	];

	$category_icon_options = [
		''          => '— Select —',
		'app'       => 'App',
		'game'      => 'Game',
		'education' => 'Education',
		'tools'     => 'Tools',
		'social'    => 'Social',
		'music'     => 'Music',
		'video'     => 'Video',
		'photo'     => 'Photo',
		'health'    => 'Health',
		'finance'   => 'Finance',
		'news'      => 'News',
		'travel'    => 'Travel',
		'shopping'  => 'Shopping',
		'sports'    => 'Sports',
		'books'     => 'Books',
	];

	echo '<table class="form-table"><tbody>';

	foreach ( $fields as $field ) {
		$value = get_post_meta( $post->ID, $field['key'], true );
		$id    = esc_attr( ltrim( $field['key'], '_' ) );
		echo '<tr>';
		echo '<th><label for="' . $id . '">' . esc_html( $field['label'] ) . '</label></th>';
		echo '<td><input type="' . esc_attr( $field['type'] ) . '" id="' . $id . '" name="' . esc_attr( $field['key'] ) . '" value="' . esc_attr( $value ) . '" class="large-text"></td>';
		echo '</tr>';
	}

	// Screenshots textarea
	$screenshots = get_post_meta( $post->ID, '_app_screenshots', true );
	echo '<tr>';
	echo '<th><label for="app_screenshots">' . esc_html__( 'Screenshot URLs (one per line)', 'aspv5' ) . '</label></th>';
	echo '<td><textarea id="app_screenshots" name="_app_screenshots" rows="5" class="large-text">' . esc_textarea( $screenshots ) . '</textarea></td>';
	echo '</tr>';

	// Category icon select
	$cat_icon = get_post_meta( $post->ID, '_app_category_icon', true );
	echo '<tr>';
	echo '<th><label for="app_category_icon">' . esc_html__( 'Category Icon', 'aspv5' ) . '</label></th>';
	echo '<td><select id="app_category_icon" name="_app_category_icon">';
	foreach ( $category_icon_options as $val => $label ) {
		echo '<option value="' . esc_attr( $val ) . '"' . selected( $cat_icon, $val, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></td>';
	echo '</tr>';

	// Is MOD checkbox
	$is_mod = get_post_meta( $post->ID, '_app_is_mod', true );
	echo '<tr>';
	echo '<th><label for="app_is_mod">' . esc_html__( 'Is MOD APK?', 'aspv5' ) . '</label></th>';
	echo '<td><input type="checkbox" id="app_is_mod" name="_app_is_mod" value="1"' . checked( $is_mod, '1', false ) . '></td>';
	echo '</tr>';

	echo '</tbody></table>';
}

function aspv5_save_app_meta( $post_id ) {
	if ( ! isset( $_POST['aspv5_app_details_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aspv5_app_details_nonce'] ) ), 'aspv5_app_details_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = [
		'_app_version', '_app_size', '_app_developer', '_app_rating',
		'_app_android_version', '_app_telegram_members', '_app_mod_info', '_app_category_icon',
		'_app_package', '_app_downloads',
	];
	$url_fields = [
		'_app_icon_url', '_app_download_url', '_app_play_store_url',
		'_app_hero_image_url', '_app_telegram_url', '_app_youtube_url',
	];

	foreach ( $text_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	foreach ( $url_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, esc_url_raw( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	if ( isset( $_POST['_app_screenshots'] ) ) {
		update_post_meta( $post_id, '_app_screenshots', sanitize_textarea_field( wp_unslash( $_POST['_app_screenshots'] ) ) );
	}

	$is_mod = isset( $_POST['_app_is_mod'] ) ? '1' : '0';
	update_post_meta( $post_id, '_app_is_mod', $is_mod );
}
add_action( 'save_post_app', 'aspv5_save_app_meta' );
add_action( 'save_post_game', 'aspv5_save_app_meta' );
