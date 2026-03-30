<?php
// inc/post-types.php — Register App CPT

function aspv5_register_app_cpt() {
	$labels = [
		'name'                  => _x( 'Apps', 'Post type general name', 'aspv5' ),
		'singular_name'         => _x( 'App', 'Post type singular name', 'aspv5' ),
		'menu_name'             => _x( 'Apps', 'Admin Menu text', 'aspv5' ),
		'name_admin_bar'        => _x( 'App', 'Add New on Toolbar', 'aspv5' ),
		'add_new'               => __( 'Add New', 'aspv5' ),
		'add_new_item'          => __( 'Add New App', 'aspv5' ),
		'new_item'              => __( 'New App', 'aspv5' ),
		'edit_item'             => __( 'Edit App', 'aspv5' ),
		'view_item'             => __( 'View App', 'aspv5' ),
		'all_items'             => __( 'All Apps', 'aspv5' ),
		'search_items'          => __( 'Search Apps', 'aspv5' ),
		'parent_item_colon'     => __( 'Parent Apps:', 'aspv5' ),
		'not_found'             => __( 'No apps found.', 'aspv5' ),
		'not_found_in_trash'    => __( 'No apps found in Trash.', 'aspv5' ),
		'featured_image'        => __( 'App Cover Image', 'aspv5' ),
		'set_featured_image'    => __( 'Set cover image', 'aspv5' ),
		'remove_featured_image' => __( 'Remove cover image', 'aspv5' ),
		'use_featured_image'    => __( 'Use as cover image', 'aspv5' ),
		'archives'              => __( 'App archives', 'aspv5' ),
		'insert_into_item'      => __( 'Insert into app', 'aspv5' ),
		'uploaded_to_this_item' => __( 'Uploaded to this app', 'aspv5' ),
		'items_list'            => __( 'Apps list', 'aspv5' ),
		'items_list_navigation' => __( 'Apps list navigation', 'aspv5' ),
		'filter_items_list'     => __( 'Filter apps list', 'aspv5' ),
	];

	$args = [
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => [ 'slug' => 'apps', 'with_front' => false ],
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-smartphone',
		'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
		'show_in_rest'       => true,
		'taxonomies'         => [ 'app-category' ],
	];

	register_post_type( 'app', $args );
}
add_action( 'init', 'aspv5_register_app_cpt' );

// Register Game CPT (shares UI/meta with Apps)
function aspv5_register_game_cpt() {
	$labels = [
		'name'                  => _x( 'Games', 'Post type general name', 'aspv5' ),
		'singular_name'         => _x( 'Game', 'Post type singular name', 'aspv5' ),
		'menu_name'             => _x( 'Games', 'Admin Menu text', 'aspv5' ),
		'name_admin_bar'        => _x( 'Game', 'Add New on Toolbar', 'aspv5' ),
		'add_new'               => __( 'Add New', 'aspv5' ),
		'add_new_item'          => __( 'Add New Game', 'aspv5' ),
		'new_item'              => __( 'New Game', 'aspv5' ),
		'edit_item'             => __( 'Edit Game', 'aspv5' ),
		'view_item'             => __( 'View Game', 'aspv5' ),
		'all_items'             => __( 'All Games', 'aspv5' ),
		'search_items'          => __( 'Search Games', 'aspv5' ),
		'parent_item_colon'     => __( 'Parent Games:', 'aspv5' ),
		'not_found'             => __( 'No games found.', 'aspv5' ),
		'not_found_in_trash'    => __( 'No games found in Trash.', 'aspv5' ),
		'featured_image'        => __( 'Game Cover Image', 'aspv5' ),
		'set_featured_image'    => __( 'Set cover image', 'aspv5' ),
		'remove_featured_image' => __( 'Remove cover image', 'aspv5' ),
		'use_featured_image'    => __( 'Use as cover image', 'aspv5' ),
		'archives'              => __( 'Game archives', 'aspv5' ),
		'insert_into_item'      => __( 'Insert into game', 'aspv5' ),
		'uploaded_to_this_item' => __( 'Uploaded to this game', 'aspv5' ),
		'items_list'            => __( 'Games list', 'aspv5' ),
		'items_list_navigation' => __( 'Games list navigation', 'aspv5' ),
		'filter_items_list'     => __( 'Filter games list', 'aspv5' ),
	];

	$args = [
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => [ 'slug' => 'games', 'with_front' => false ],
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 6,
		'menu_icon'          => 'dashicons-games',
		'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
		'show_in_rest'       => true,
		'taxonomies'         => [ 'app-category' ],
	];

	register_post_type( 'game', $args );
}
add_action( 'init', 'aspv5_register_game_cpt' );
