<?php
// inc/taxonomies.php — Register App Category taxonomy

function aspv5_register_taxonomies() {
	$labels = [
		'name'                       => _x( 'App & Game Categories', 'taxonomy general name', 'aspv5' ),
		'singular_name'              => _x( 'Category', 'taxonomy singular name', 'aspv5' ),
		'search_items'               => __( 'Search Categories', 'aspv5' ),
		'popular_items'              => __( 'Popular Categories', 'aspv5' ),
		'all_items'                  => __( 'All Categories', 'aspv5' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Category', 'aspv5' ),
		'update_item'                => __( 'Update Category', 'aspv5' ),
		'add_new_item'               => __( 'Add New Category', 'aspv5' ),
		'new_item_name'              => __( 'New Category Name', 'aspv5' ),
		'separate_items_with_commas' => __( 'Separate categories with commas', 'aspv5' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'aspv5' ),
		'choose_from_most_used'      => __( 'Choose from the most used categories', 'aspv5' ),
		'not_found'                  => __( 'No categories found.', 'aspv5' ),
		'menu_name'                  => __( 'Categories', 'aspv5' ),
	];

    // Use a hierarchical taxonomy so the base slug (e.g. /app-category/)
    // behaves like a category archive and does not return a 404. When
    // hierarchical is true, WordPress automatically creates rewrite rules
    // similar to the default `category` taxonomy, allowing you to browse
    // the base path and individual category terms. See
    // https://developer.wordpress.org/reference/functions/register_taxonomy/
    // for details.
    $args = [
        'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => [ 'slug' => 'app-category', 'with_front' => false ],
		'show_in_rest'          => true,
	];

	register_taxonomy( 'app-category', [ 'app', 'game' ], $args );
}
add_action( 'init', 'aspv5_register_taxonomies' );

// ── Add category image field ──────────────────────────────────────────────────
function aspv5_category_image_field() {
	?>
	<div class="form-field term-image-wrap">
		<label for="appstorepro-category-image"><?php esc_html_e( 'Category Image', 'aspv5' ); ?></label>
		<div id="appstorepro-category-image-wrapper" style="margin-top: 10px;">
			<img id="appstorepro-category-image-preview" src="" alt="" style="max-width: 150px; height: auto; display: none; margin-bottom: 10px;">
		</div>
		<input type="hidden" id="appstorepro-category-image" name="aspv5_category_image" value="">
		<button type="button" class="button" id="appstorepro-category-image-btn"><?php esc_html_e( 'Upload Image', 'aspv5' ); ?></button>
		<button type="button" class="button" id="appstorepro-category-image-remove" style="display: none;"><?php esc_html_e( 'Remove Image', 'aspv5' ); ?></button>
	</div>
	<?php
}
add_action( 'app-category_add_form_fields', 'aspv5_category_image_field' );

// ── Edit category image field ────────────────────────────────────────────────
function aspv5_category_edit_image_field( $term ) {
	$image_id = get_term_meta( $term->term_id, '_aspv5_category_image', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
	?>
	<tr class="form-field term-image-wrap">
		<th scope="row"><label for="appstorepro-category-image"><?php esc_html_e( 'Category Image', 'aspv5' ); ?></label></th>
		<td>
			<div id="appstorepro-category-image-wrapper" style="margin-bottom: 10px;">
				<?php if ( $image_url ) : ?>
					<img id="appstorepro-category-image-preview" src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width: 150px; height: auto; margin-bottom: 10px;">
				<?php else : ?>
					<img id="appstorepro-category-image-preview" src="" alt="" style="max-width: 150px; height: auto; display: none; margin-bottom: 10px;">
				<?php endif; ?>
			</div>
			<input type="hidden" id="appstorepro-category-image" name="aspv5_category_image" value="<?php echo esc_attr( $image_id ); ?>">
			<button type="button" class="button" id="appstorepro-category-image-btn"><?php esc_html_e( 'Upload Image', 'aspv5' ); ?></button>
			<?php if ( $image_id ) : ?>
				<button type="button" class="button" id="appstorepro-category-image-remove"><?php esc_html_e( 'Remove Image', 'aspv5' ); ?></button>
			<?php else : ?>
				<button type="button" class="button" id="appstorepro-category-image-remove" style="display: none;"><?php esc_html_e( 'Remove Image', 'aspv5' ); ?></button>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}
add_action( 'app-category_edit_form_fields', 'aspv5_category_edit_image_field' );

// ── Save category image ───────────────────────────────────────────────────────
function aspv5_save_category_image( $term_id ) {
	if ( isset( $_POST['aspv5_category_image'] ) && ! empty( $_POST['aspv5_category_image'] ) ) {
		$image_id = sanitize_text_field( wp_unslash( $_POST['aspv5_category_image'] ) );
		update_term_meta( $term_id, '_aspv5_category_image', $image_id );
	} else {
		delete_term_meta( $term_id, '_aspv5_category_image' );
	}
}
add_action( 'edited_app-category', 'aspv5_save_category_image' );
add_action( 'create_app-category', 'aspv5_save_category_image' );

// ── Enqueue media uploader for category page ──────────────────────────────────
function aspv5_enqueue_category_media() {
	$screen = get_current_screen();
	if ( $screen && 'edit-app-category' === $screen->id ) {
		wp_enqueue_media();
		wp_enqueue_script( 'appstorepro-category-image', 
			get_template_directory_uri() . '/assets/js/category-image.js', 
			[ 'jquery', 'media-upload', 'media-views' ], 
			wp_get_theme()->get( 'Version' ), 
			true 
		);
	}
}
add_action( 'admin_enqueue_scripts', 'aspv5_enqueue_category_media' );

// ── Get category image ────────────────────────────────────────────────────────
function aspv5_get_category_image( $term_id, $size = 'thumbnail' ) {
	$image_id = get_term_meta( $term_id, '_aspv5_category_image', true );
	if ( $image_id ) {
		return wp_get_attachment_image_url( $image_id, $size );
	}
	return '';
}
