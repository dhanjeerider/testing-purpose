<?php
/**
 * MXSeries HTML Theme functions and definitions
 *
 * This file registers custom post types, taxonomies, meta boxes and
 * helper functions. It also enqueues the stylesheet and sets up
 * theme support options.
 *
 * @package MXSeries HTML Theme
 */

if ( ! defined( 'MXSERIES_HTML_THEME_DIR' ) ) {
    define( 'MXSERIES_HTML_THEME_DIR', get_template_directory() );
}

/**
 * Theme setup.
 *
 * Adds support for various WordPress features and registers navigation menus.
 */
function mxseries_html_theme_setup() {
    // Make theme available for translation.
    load_theme_textdomain( 'mxseries-html-theme', MXSERIES_HTML_THEME_DIR . '/languages' );

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for post thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // Register nav menu (not used in header markup but allows assignment via admin).
    register_nav_menus( array(
        'primary'   => __( 'Primary Menu', 'mxseries-html-theme' ),
        'secondary' => __( 'Secondary Menu', 'mxseries-html-theme' ),
        'footer'    => __( 'Footer Menu', 'mxseries-html-theme' ),
    ) );

    // Enable HTML5 markup support.
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
}
add_action( 'after_setup_theme', 'mxseries_html_theme_setup' );

/**
 * Enqueue theme stylesheet. The CSS file is intentionally empty so that
 * users can provide their own styles.
 */
function mxseries_html_theme_scripts() {
    wp_enqueue_style( 'mxseries-html-theme-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'mxseries_html_theme_scripts' );

/**
 * Register custom post types and taxonomies.
 */
function mxseries_register_content_types() {
    /*
     * Register the Model taxonomy for actors instead of a custom post type.
     * Each model is now stored as a term with its own metadata (image,
     * series count and view count). This taxonomy is non-hierarchical so
     * that actors behave like tags.
     */
    $model_labels = array(
        'name'          => _x( 'Models', 'taxonomy general name', 'mxseries-html-theme' ),
        'singular_name' => _x( 'Model', 'taxonomy singular name', 'mxseries-html-theme' ),
        'search_items'  => __( 'Search Models', 'mxseries-html-theme' ),
        'all_items'     => __( 'All Models', 'mxseries-html-theme' ),
        'edit_item'     => __( 'Edit Model', 'mxseries-html-theme' ),
        'update_item'   => __( 'Update Model', 'mxseries-html-theme' ),
        'add_new_item'  => __( 'Add New Model', 'mxseries-html-theme' ),
        'new_item_name' => __( 'New Model Name', 'mxseries-html-theme' ),
        'menu_name'     => __( 'Models', 'mxseries-html-theme' ),
    );

    $model_args = array(
        'hierarchical'      => false,
        'labels'            => $model_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        /*
         * Use a different base slug for the model taxonomy to avoid
         * colliding with the top‑level `/model` page used to display
         * all model terms. Each model archive will now live under
         * `/models/{term}` instead of `/model/{term}`.
         */
        'rewrite'           => array( 'slug' => 'models' ),
        'show_in_rest'      => true,
    );
    register_taxonomy( 'model', array( 'post' ), $model_args );

    // Register OTT taxonomy for posts.
    $ott_labels = array(
        'name'              => _x( 'OTT Platforms', 'taxonomy general name', 'mxseries-html-theme' ),
        'singular_name'     => _x( 'OTT Platform', 'taxonomy singular name', 'mxseries-html-theme' ),
        'search_items'      => __( 'Search OTT Platforms', 'mxseries-html-theme' ),
        'all_items'         => __( 'All OTT Platforms', 'mxseries-html-theme' ),
        'edit_item'         => __( 'Edit OTT Platform', 'mxseries-html-theme' ),
        'update_item'       => __( 'Update OTT Platform', 'mxseries-html-theme' ),
        'add_new_item'      => __( 'Add New OTT Platform', 'mxseries-html-theme' ),
        'new_item_name'     => __( 'New OTT Platform Name', 'mxseries-html-theme' ),
        'menu_name'         => __( 'OTT Platforms', 'mxseries-html-theme' ),
    );

    $ott_args = array(
        'hierarchical'      => false,
        'labels'            => $ott_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        /*
         * Change the OTT taxonomy slug to avoid conflicting with a
         * top‑level `/ott` page used to list all platforms. Each
         * platform archive will now live under `/ott-platforms/{term}`.
         */
        'rewrite'           => array( 'slug' => 'ott-platforms' ),
        'show_in_rest'      => true,
    );
    register_taxonomy( 'ott', array( 'post' ), $ott_args );
}
add_action( 'init', 'mxseries_register_content_types' );

/**
 * Flush rewrite rules on theme activation.
 *
 * Changing taxonomy slugs (e.g. model → models, ott → ott‑platforms) requires
 * the WordPress rewrite rules to be refreshed. This hook ensures that
 * permalinks are rebuilt when the theme is first activated.
 */
function mxseries_flush_rewrite_on_activation() {
    // We call flush_rewrite_rules() to regenerate permalinks for custom taxonomies.
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'mxseries_flush_rewrite_on_activation' );

/**
 * Add meta boxes for posts and models.
 */
function mxseries_add_meta_boxes() {
    // Meta box for episodes and other meta on posts.
    add_meta_box(
        'mxseries_post_meta',
        __( 'Series Details', 'mxseries-html-theme' ),
        'mxseries_post_meta_box_callback',
        'post',
        'normal',
        'default'
    );

}
add_action( 'add_meta_boxes', 'mxseries_add_meta_boxes' );

/**
 * Meta box callback for posts (series).
 *
 * @param WP_Post $post
 */
function mxseries_post_meta_box_callback( $post ) {
    // Use nonce for verification.
    wp_nonce_field( 'mxseries_save_post_meta', 'mxseries_post_meta_nonce' );

    // Retrieve existing values from post meta.
    $video_url     = get_post_meta( $post->ID, '_mxseries_video_url', true );
    $view_count    = get_post_meta( $post->ID, '_mxseries_view_count', true );
    $likes_count   = get_post_meta( $post->ID, '_mxseries_likes_count', true );
    $dislikes_count = get_post_meta( $post->ID, '_mxseries_dislikes_count', true );
    $trailer_url   = get_post_meta( $post->ID, '_mxseries_trailer_url', true );
    
    // Retrieve selected model terms for this post.
    $associated_models = wp_get_post_terms( $post->ID, 'model', array( 'fields' => 'ids' ) );
    if ( ! is_array( $associated_models ) ) {
        $associated_models = array();
    }

    ?>
    <p>
        <label for="mxseries_video_url"><strong><?php _e( 'Video URL', 'mxseries-html-theme' ); ?></strong></label>
        <input type="text" id="mxseries_video_url" name="mxseries_video_url" value="<?php echo esc_attr( $video_url ); ?>" style="width: 100%;" placeholder="<?php esc_attr_e( 'Enter MP4 video URL or iframe code', 'mxseries-html-theme' ); ?>" />
    </p>

    <hr style="margin: 30px 0;" />

    <p>
        <label for="mxseries_view_count"><strong><?php _e( 'View Count', 'mxseries-html-theme' ); ?>:</strong></label>
        <input type="number" id="mxseries_view_count" name="mxseries_view_count" value="<?php echo esc_attr( $view_count ); ?>" min="0" style="width: 100%;" />
    </p>
    <p>
        <label for="mxseries_likes_count"><strong><?php _e( 'Likes Count', 'mxseries-html-theme' ); ?>:</strong></label>
        <input type="number" id="mxseries_likes_count" name="mxseries_likes_count" value="<?php echo esc_attr( $likes_count ? $likes_count : rand( 100, 500 ) ); ?>" min="0" style="width: 100%;" readonly />
        <small><?php _e( 'Auto-generated on first save', 'mxseries-html-theme' ); ?></small>
    </p>
    <p>
        <label for="mxseries_dislikes_count"><strong><?php _e( 'Dislikes Count', 'mxseries-html-theme' ); ?>:</strong></label>
        <input type="number" id="mxseries_dislikes_count" name="mxseries_dislikes_count" value="<?php echo esc_attr( $dislikes_count ? $dislikes_count : rand( 10, 50 ) ); ?>" min="0" style="width: 100%;" readonly />
        <small><?php _e( 'Auto-generated on first save', 'mxseries-html-theme' ); ?></small>
    </p>
    <p>
        <label for="mxseries_selected_models"><strong><?php _e( 'Associated Models', 'mxseries-html-theme' ); ?></strong></label><br />
        <select id="mxseries_selected_models" name="mxseries_selected_models[]" multiple style="width: 100%; height: 150px;">
            <?php
            $model_terms = get_terms( array(
                'taxonomy'   => 'model',
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ) );
            foreach ( $model_terms as $term ) {
                $selected = in_array( $term->term_id, $associated_models ) ? 'selected' : '';
                echo '<option value="' . esc_attr( $term->term_id ) . '" ' . $selected . '>' . esc_html( $term->name ) . '</option>';
            }
            ?>
        </select>
        <small><?php _e( 'Hold down the Ctrl (Windows) or Command (Mac) key to select multiple models.', 'mxseries-html-theme' ); ?></small>
    </p>
    <p>
        <label for="mxseries_trailer_url"><strong><?php _e( 'Trailer URL', 'mxseries-html-theme' ); ?></strong></label>
        <input type="text" id="mxseries_trailer_url" name="mxseries_trailer_url" value="<?php echo esc_attr( $trailer_url ); ?>" style="width: 100%;" />
        <small><?php _e( 'Optional: Provide an MP4 URL to show a trailer on hover over series cards.', 'mxseries-html-theme' ); ?></small>
    </p>
    <?php
}

/**
 * Save meta fields for posts.
 *
 * @param int $post_id
 */
function mxseries_save_post_meta( $post_id ) {
    // Check nonce.
    if ( ! isset( $_POST['mxseries_post_meta_nonce'] ) || ! wp_verify_nonce( $_POST['mxseries_post_meta_nonce'], 'mxseries_save_post_meta' ) ) {
        return;
    }
    // Check autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // Check user permissions.
    if ( isset( $_POST['post_type'] ) && 'post' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }
    // Sanitize and save episode count - DEPRECATED (auto-calculated now)
    // Keeping for backwards compatibility
    
    // Save video URL (single video)
    if ( isset( $_POST['mxseries_video_url'] ) ) {
        $video_url = trim( $_POST['mxseries_video_url'] );
        
        if ( ! empty( $video_url ) ) {
            // Detect if it's iframe or MP4
            if ( strpos( $video_url, '<iframe' ) !== false || strpos( $video_url, 'iframe' ) !== false ) {
                $video_url = wp_kses_post( $video_url );
            } else {
                $video_url = esc_url_raw( $video_url );
            }
            update_post_meta( $post_id, '_mxseries_video_url', $video_url );
        } else {
            delete_post_meta( $post_id, '_mxseries_video_url' );
        }
    }
    
    // Initialize likes and dislikes if not set
    if ( ! get_post_meta( $post_id, '_mxseries_likes_count', true ) ) {
        update_post_meta( $post_id, '_mxseries_likes_count', rand( 100, 500 ) );
    }
    if ( ! get_post_meta( $post_id, '_mxseries_dislikes_count', true ) ) {
        update_post_meta( $post_id, '_mxseries_dislikes_count', rand( 10, 50 ) );
    }

    // Sanitize and save view count.
    $view_count = isset( $_POST['mxseries_view_count'] ) ? intval( $_POST['mxseries_view_count'] ) : 0;
    update_post_meta( $post_id, '_mxseries_view_count', $view_count );

    // Save selected model terms. Use taxonomy API instead of meta.
    if ( isset( $_POST['mxseries_selected_models'] ) && is_array( $_POST['mxseries_selected_models'] ) ) {
        $models = array_map( 'intval', $_POST['mxseries_selected_models'] );
        // Assign the selected models to the post. Nonexistent values are ignored.
        wp_set_object_terms( $post_id, $models, 'model' );
    } else {
        // If no models selected, remove all model term relationships.
        wp_set_object_terms( $post_id, array(), 'model' );
    }

    /*
     * After updating the model assignments for this post, recalculate the
     * series count for all model terms. This ensures the “Series Count”
     * displayed on model pages always reflects the current number of
     * posts assigned to each model (similar to how category counts work).
     */
    mxseries_update_model_series_counts();

    // Save trailer URL.
    if ( isset( $_POST['mxseries_trailer_url'] ) ) {
        $trailer_url = esc_url_raw( $_POST['mxseries_trailer_url'] );
        if ( $trailer_url ) {
            update_post_meta( $post_id, '_mxseries_trailer_url', $trailer_url );
        } else {
            delete_post_meta( $post_id, '_mxseries_trailer_url' );
        }
    }
}
add_action( 'save_post', 'mxseries_save_post_meta' );

// Recalculate model series counts when a post is deleted.
add_action( 'deleted_post', 'mxseries_update_model_series_counts' );

/**
 * AJAX handler for like/dislike functionality
 */
function mxseries_handle_like_dislike() {
    // Check nonce for security
    check_ajax_referer( 'mxseries_like_nonce', 'nonce' );
    
    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';
    
    if ( ! $post_id || ! in_array( $action_type, array( 'like', 'dislike' ) ) ) {
        wp_send_json_error( array( 'message' => 'Invalid request' ) );
    }
    
    // Get current counts
    $likes_key = '_mxseries_likes_count';
    $dislikes_key = '_mxseries_dislikes_count';
    
    $likes = get_post_meta( $post_id, $likes_key, true );
    $dislikes = get_post_meta( $post_id, $dislikes_key, true );
    
    // Initialize if not set
    if ( ! $likes ) $likes = rand( 100, 500 );
    if ( ! $dislikes ) $dislikes = rand( 10, 50 );
    
    // Update count
    if ( $action_type === 'like' ) {
        $likes++;
        update_post_meta( $post_id, $likes_key, $likes );
    } else {
        $dislikes++;
        update_post_meta( $post_id, $dislikes_key, $dislikes );
    }
    
    wp_send_json_success( array(
        'likes' => $likes,
        'dislikes' => $dislikes
    ) );
}
add_action( 'wp_ajax_mxseries_like_dislike', 'mxseries_handle_like_dislike' );
add_action( 'wp_ajax_nopriv_mxseries_like_dislike', 'mxseries_handle_like_dislike' );


/**
 * Add custom fields to OTT taxonomy terms for storing thumbnail URL.
 */
function mxseries_ott_add_form_fields( $taxonomy ) {
    ?>
    <div class="form-field">
        <label for="mxseries_ott_thumbnail"><?php _e( 'Thumbnail URL', 'mxseries-html-theme' ); ?></label>
        <input type="text" name="mxseries_ott_thumbnail" id="mxseries_ott_thumbnail" value="" />
        <button type="button" class="button mxseries-upload-ott-image" style="margin-top:5px;">
            <?php _e( 'Upload', 'mxseries-html-theme' ); ?>
        </button>
        <p class="description"><?php _e( 'Enter or upload an image to represent this OTT platform.', 'mxseries-html-theme' ); ?></p>
    </div>
    <div class="form-field">
        <label for="mxseries_ott_category"><?php _e( 'Assign Category', 'mxseries-html-theme' ); ?></label>
        <select name="mxseries_ott_category" id="mxseries_ott_category">
            <option value="0"><?php _e( '— None —', 'mxseries-html-theme' ); ?></option>
            <?php
            $categories = get_categories( array( 'hide_empty' => false ) );
            foreach ( $categories as $cat ) {
                echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>';
            }
            ?>
        </select>
        <p class="description"><?php _e( 'Choose a WordPress category to associate with this OTT platform. Optional.', 'mxseries-html-theme' ); ?></p>
    </div>
    <?php
}
add_action( 'ott_add_form_fields', 'mxseries_ott_add_form_fields' );

/**
 * Edit form for OTT taxonomy term to show existing thumbnail field.
 */
function mxseries_ott_edit_form_fields( $term ) {
    $thumbnail = get_term_meta( $term->term_id, 'mxseries_ott_thumbnail', true );
    $category = get_term_meta( $term->term_id, 'mxseries_ott_category', true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="mxseries_ott_thumbnail"><?php _e( 'Thumbnail URL', 'mxseries-html-theme' ); ?></label></th>
        <td>
            <input type="text" name="mxseries_ott_thumbnail" id="mxseries_ott_thumbnail" value="<?php echo esc_attr( $thumbnail ); ?>" />
            <button type="button" class="button mxseries-upload-ott-image" style="margin-top:5px;">
                <?php _e( 'Upload', 'mxseries-html-theme' ); ?>
            </button>
            <p class="description"><?php _e( 'Enter or upload an image to represent this OTT platform.', 'mxseries-html-theme' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="mxseries_ott_category"><?php _e( 'Assign Category', 'mxseries-html-theme' ); ?></label></th>
        <td>
            <select name="mxseries_ott_category" id="mxseries_ott_category">
                <option value="0"><?php _e( '— None —', 'mxseries-html-theme' ); ?></option>
                <?php
                $categories = get_categories( array( 'hide_empty' => false ) );
                foreach ( $categories as $cat ) {
                    $selected = ( $category == $cat->term_id ) ? 'selected' : '';
                    echo '<option value="' . esc_attr( $cat->term_id ) . '" ' . $selected . '>' . esc_html( $cat->name ) . '</option>';
                }
                ?>
            </select>
            <p class="description"><?php _e( 'Choose a WordPress category to associate with this OTT platform. Optional.', 'mxseries-html-theme' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'ott_edit_form_fields', 'mxseries_ott_edit_form_fields' );

/**
 * Save OTT taxonomy term metadata.
 *
 * @param int $term_id
 */
function mxseries_save_ott_meta( $term_id ) {
    if ( isset( $_POST['mxseries_ott_thumbnail'] ) ) {
        $thumbnail = esc_url_raw( $_POST['mxseries_ott_thumbnail'] );
        update_term_meta( $term_id, 'mxseries_ott_thumbnail', $thumbnail );
    }
    if ( isset( $_POST['mxseries_ott_category'] ) ) {
        $cat_id = intval( $_POST['mxseries_ott_category'] );
        if ( $cat_id > 0 ) {
            update_term_meta( $term_id, 'mxseries_ott_category', $cat_id );
        } else {
            delete_term_meta( $term_id, 'mxseries_ott_category' );
        }
    }
}
add_action( 'created_ott', 'mxseries_save_ott_meta' );
add_action( 'edited_ott', 'mxseries_save_ott_meta' );

/**
 * Add custom fields to the Model taxonomy add form. These fields allow admins
 * to set a thumbnail image URL, series count and view count for each actor.
 * The series and view counts are optional and can be calculated
 * automatically but may be manually overridden.
 *
 * @param string $taxonomy Taxonomy slug.
 */
function mxseries_model_add_form_fields( $taxonomy ) {
    ?>
    <div class="form-field">
        <label for="mxseries_model_image"><?php _e( 'Profile Image URL', 'mxseries-html-theme' ); ?></label>
        <input type="text" name="mxseries_model_image" id="mxseries_model_image" value="" />
        <button type="button" class="button mxseries-upload-model-image" style="margin-top:5px;">
            <?php _e( 'Upload', 'mxseries-html-theme' ); ?>
        </button>
        <p class="description"><?php _e( 'Enter or upload an image for this model.', 'mxseries-html-theme' ); ?></p>
    </div>
    <div class="form-field">
        <label for="mxseries_series_count"><?php _e( 'Series Count', 'mxseries-html-theme' ); ?></label>
        <input type="number" name="mxseries_series_count" id="mxseries_series_count" value="0" min="0" />
        <p class="description"><?php _e( 'Number of series featuring this model. Optional.', 'mxseries-html-theme' ); ?></p>
    </div>
    <div class="form-field">
        <label for="mxseries_model_view_count"><?php _e( 'View Count', 'mxseries-html-theme' ); ?></label>
        <input type="number" name="mxseries_model_view_count" id="mxseries_model_view_count" value="0" min="0" />
        <p class="description"><?php _e( 'Number of views across all series featuring this model. Optional.', 'mxseries-html-theme' ); ?></p>
    </div>
    <?php
}
add_action( 'model_add_form_fields', 'mxseries_model_add_form_fields' );

/**
 * Edit form for Model taxonomy term. Displays existing meta values for
 * thumbnail, series count and view count so they can be updated.
 *
 * @param WP_Term $term Term being edited.
 */
function mxseries_model_edit_form_fields( $term ) {
    $image      = get_term_meta( $term->term_id, 'mxseries_model_image', true );
    $series_cnt = get_term_meta( $term->term_id, 'mxseries_series_count', true );
    $view_cnt   = get_term_meta( $term->term_id, 'mxseries_model_view_count', true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="mxseries_model_image"><?php _e( 'Profile Image URL', 'mxseries-html-theme' ); ?></label></th>
        <td>
            <input type="text" name="mxseries_model_image" id="mxseries_model_image" value="<?php echo esc_attr( $image ); ?>" />
            <button type="button" class="button mxseries-upload-model-image" style="margin-top:5px;">
                <?php _e( 'Upload', 'mxseries-html-theme' ); ?>
            </button>
            <p class="description"><?php _e( 'Enter or upload an image for this model.', 'mxseries-html-theme' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="mxseries_series_count"><?php _e( 'Series Count', 'mxseries-html-theme' ); ?></label></th>
        <td>
            <input type="number" name="mxseries_series_count" id="mxseries_series_count" value="<?php echo esc_attr( $series_cnt ); ?>" min="0" />
            <p class="description"><?php _e( 'Number of series featuring this model. Optional.', 'mxseries-html-theme' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="mxseries_model_view_count"><?php _e( 'View Count', 'mxseries-html-theme' ); ?></label></th>
        <td>
            <input type="number" name="mxseries_model_view_count" id="mxseries_model_view_count" value="<?php echo esc_attr( $view_cnt ); ?>" min="0" />
            <p class="description"><?php _e( 'Number of views across all series featuring this model. Optional.', 'mxseries-html-theme' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'model_edit_form_fields', 'mxseries_model_edit_form_fields' );

/**
 * Save metadata for the Model taxonomy term. Handles both create and edit
 * actions.
 *
 * @param int $term_id Term ID.
 */
function mxseries_save_model_term_meta( $term_id ) {
    if ( isset( $_POST['mxseries_model_image'] ) ) {
        $image = esc_url_raw( $_POST['mxseries_model_image'] );
        if ( $image ) {
            update_term_meta( $term_id, 'mxseries_model_image', $image );
        } else {
            delete_term_meta( $term_id, 'mxseries_model_image' );
        }
    }
    if ( isset( $_POST['mxseries_series_count'] ) ) {
        $series_cnt = intval( $_POST['mxseries_series_count'] );
        update_term_meta( $term_id, 'mxseries_series_count', $series_cnt );
    }
    if ( isset( $_POST['mxseries_model_view_count'] ) ) {
        $view_cnt = intval( $_POST['mxseries_model_view_count'] );
        update_term_meta( $term_id, 'mxseries_model_view_count', $view_cnt );
    }
}
add_action( 'created_model', 'mxseries_save_model_term_meta' );
add_action( 'edited_model', 'mxseries_save_model_term_meta' );

/**
 * Enqueue scripts for media upload buttons on taxonomy term edit pages.
 *
 * Loads WordPress's media uploader and a custom script that binds
 * upload buttons to taxonomy image fields. Only runs on edit/add
 * pages for the model and OTT taxonomies.
 *
 * @param string $hook Current admin page hook.
 */
function mxseries_admin_enqueue_scripts( $hook ) {
    // Only load on term edit screens (edit-tags.php or term.php).
    if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
        return;
    }
    $taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';
    if ( ! in_array( $taxonomy, array( 'model', 'ott' ), true ) ) {
        return;
    }
    // Enqueue WordPress media library.
    wp_enqueue_media();
    // Enqueue our custom media uploader script.
    wp_enqueue_script(
        'mxseries-media',
        get_template_directory_uri() . '/js/mxseries-media.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
add_action( 'admin_enqueue_scripts', 'mxseries_admin_enqueue_scripts' );
// Recalculate model series counts when a model term is created or edited.
add_action( 'created_model', 'mxseries_update_model_series_counts' );
add_action( 'edited_model', 'mxseries_update_model_series_counts' );

/**
 * Update series count metadata for all model terms.
 *
 * Each time a post is saved or a model term relationship changes, this
 * function can be called to synchronize the `mxseries_series_count`
 * term meta with the actual number of posts assigned to each model.
 * WordPress stores the count on the term object itself (`$term->count`),
 * but mirroring it in term meta avoids re‑querying on the front end.
 */
function mxseries_update_model_series_counts() {
    $terms = get_terms( array(
        'taxonomy'   => 'model',
        'hide_empty' => false,
    ) );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return;
    }
    foreach ( $terms as $t ) {
        $count = isset( $t->count ) ? intval( $t->count ) : 0;
        update_term_meta( $t->term_id, 'mxseries_series_count', $count );
    }
}

/**
 * Increment view count for a post when it is viewed on single pages.
 */
function mxseries_track_post_views( $post_id ) {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    // Avoid counting views during preview.
    if ( is_preview() ) {
        return;
    }
    $count = get_post_meta( $post_id, '_mxseries_view_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    $count++;
    update_post_meta( $post_id, '_mxseries_view_count', $count );
}
add_action( 'wp_head', function() {
    if ( is_singular( 'post' ) ) {
        $post_id = get_queried_object_id();
        if ( $post_id ) {
            mxseries_track_post_views( $post_id );
        }
    }
} );

/**
 * Increment view count for a model term when its archive page is viewed.
 */
function mxseries_track_model_views() {
    if ( is_tax( 'model' ) ) {
        $term    = get_queried_object();
        if ( $term && isset( $term->term_id ) ) {
            $count = get_term_meta( $term->term_id, 'mxseries_model_view_count', true );
            if ( empty( $count ) ) {
                $count = 0;
            }
            $count++;
            update_term_meta( $term->term_id, 'mxseries_model_view_count', $count );
        }
    }
}
add_action( 'wp', 'mxseries_track_model_views' );

/**
 * Utility function to format time difference as used in card titles.
 *
 * Accepts a timestamp or MySQL datetime and returns a short relative time string
 * (e.g., "3 Mo Ago", "1 Yr Ago").
 *
 * @param string|int $time The time to compare against current time.
 * @return string
 */
function mxseries_relative_time( $time ) {
    $timestamp = is_numeric( $time ) ? intval( $time ) : strtotime( $time );
    $current   = current_time( 'timestamp' );
    $diff      = $current - $timestamp;
    if ( $diff < 0 ) {
        return __( '0 Sec Ago', 'mxseries-html-theme' );
    }
    $units = array(
        'year'   => YEAR_IN_SECONDS,
        'month'  => MONTH_IN_SECONDS,
        'week'   => WEEK_IN_SECONDS,
        'day'    => DAY_IN_SECONDS,
        'hour'   => HOUR_IN_SECONDS,
        'minute' => MINUTE_IN_SECONDS,
        'second' => 1,
    );
    foreach ( $units as $unit => $seconds ) {
        if ( $diff >= $seconds ) {
            $value = floor( $diff / $seconds );
            switch ( $unit ) {
                case 'year':
                    $abbr = _n( '%s Yr Ago', '%s Yrs Ago', $value, 'mxseries-html-theme' );
                    break;
                case 'month':
                    $abbr = _n( '%s Mo Ago', '%s Mos Ago', $value, 'mxseries-html-theme' );
                    break;
                case 'week':
                    $abbr = _n( '%s Wk Ago', '%s Wks Ago', $value, 'mxseries-html-theme' );
                    break;
                case 'day':
                    $abbr = _n( '%s Day Ago', '%s Days Ago', $value, 'mxseries-html-theme' );
                    break;
                case 'hour':
                    $abbr = _n( '%s Hr Ago', '%s Hrs Ago', $value, 'mxseries-html-theme' );
                    break;
                case 'minute':
                    $abbr = _n( '%s Min Ago', '%s Mins Ago', $value, 'mxseries-html-theme' );
                    break;
                default:
                    $abbr = _n( '%s Sec Ago', '%s Secs Ago', $value, 'mxseries-html-theme' );
                    break;
            }
            return sprintf( $abbr, $value );
        }
    }
    return __( '0 Sec Ago', 'mxseries-html-theme' );
}

/**
 * Retrieve an array of episode data for a post.
 * Returns array with episode information including name, URL and type.
 *
 * @param int $post_id
 * @return array Array of episode data
 */
function mxseries_get_episodes( $post_id ) {
    // Get episodes data
    $episodes_data = get_post_meta( $post_id, '_mxseries_episodes_data', true );
    
    // Force array if not already
    if ( ! is_array( $episodes_data ) ) {
        $episodes_data = array();
    }
    
    // If we have data in new format, return it
    if ( ! empty( $episodes_data ) ) {
        // Ensure each episode has required keys
        $cleaned_episodes = array();
        foreach ( $episodes_data as $index => $ep ) {
            if ( isset( $ep['url'] ) && ! empty( $ep['url'] ) ) {
                $cleaned_episodes[] = array(
                    'name' => 'Episode ' . ( $index + 1 ),
                    'url'  => $ep['url'],
                    'type' => isset( $ep['type'] ) ? $ep['type'] : 'mp4'
                );
            }
        }
        return $cleaned_episodes;
    }
    
    // Fallback: check old format (plain URL list) for backwards compatibility
    $list = get_post_meta( $post_id, '_mxseries_episode_list', true );
    
    $episodes = array();
    if ( ! empty( $list ) && is_string( $list ) ) {
        $lines = explode( "\n", $list );
        foreach ( $lines as $index => $line ) {
            $url = trim( $line );
            if ( ! empty( $url ) ) {
                $episodes[] = array(
                    'name' => 'Episode ' . ( $index + 1 ),
                    'url'  => esc_url( $url ),
                    'type' => 'mp4'
                );
            }
        }
    }
    
    return $episodes;
}

/**
 * Retrieve the number of episodes for a given post.
 *
 * @param int $post_id
 * @return int Number of episodes
 */
function mxseries_get_episode_count( $post_id ) {
    // Check explicit episode count meta (auto-updated by save function)
    $count = get_post_meta( $post_id, '_mxseries_episode_count', true );
    if ( $count ) {
        return intval( $count );
    }
    
    // Fallback: count episodes from new data format
    $episodes_data = get_post_meta( $post_id, '_mxseries_episodes_data', true );
    if ( is_array( $episodes_data ) && ! empty( $episodes_data ) ) {
        return count( $episodes_data );
    }
    
    // Fallback: count number of lines in old episode list format
    $list = get_post_meta( $post_id, '_mxseries_episode_list', true );
    if ( $list ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", $list ) ) );
        if ( ! empty( $lines ) ) {
            return count( $lines );
        }
    }
    
    return 0;
}

/**
 * Retrieve associated model posts for a post.
 *
 * @param int $post_id
 * @return WP_Post[]
 */
function mxseries_get_associated_models( $post_id ) {
    /*
     * Retrieve associated model terms for a post. Returns an array of
     * WP_Term objects representing actors assigned to this post.
     */
    $terms = wp_get_post_terms( $post_id, 'model' );
    if ( is_wp_error( $terms ) ) {
        return array();
    }
    return $terms;
}

/**
 * Filter the_excerpt length to show shorter excerpt for cards.
 */
function mxseries_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'mxseries_excerpt_length' );

/**
 * Add classes to body element for easier styling.
 */
function mxseries_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'mxseries-front-page';
    }
    if ( is_singular( 'model' ) ) {
        $classes[] = 'mxseries-model-single';
    }
    return $classes;
}
add_filter( 'body_class', 'mxseries_body_classes' );

/**
 * Enqueue AOS library and initialize animation.
 */
function mxseries_enqueue_aos() {
    // AOS library for animations.
    wp_enqueue_style( 'aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css', array(), '2.3.4' );
    wp_enqueue_script( 'aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js', array(), '2.3.4', true );
    // Initialize AOS when the script is loaded.
    wp_add_inline_script( 'aos', 'AOS.init({ duration: 600, once: true });' );
}
add_action( 'wp_enqueue_scripts', 'mxseries_enqueue_aos' );

/**
 * Output custom scripts in the footer for search overlay toggle and off-canvas menu.
 */
function mxseries_custom_footer_scripts() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle search overlay visibility. Listen on all elements with the
        // data attribute or class so multiple triggers can exist (desktop and mobile).
        var overlay = document.getElementById('header-search-overlay');
        var searchButtons = document.querySelectorAll('[data-toggle="search-overlay"], .search-overlay-toggle');
        if (overlay && searchButtons.length) {
            searchButtons.forEach(function(btn) {
                btn.addEventListener('click', function(event) {
                    event.preventDefault();
                    if (overlay.style.display === 'block') {
                        overlay.style.display = 'none';
                    } else {
                        overlay.style.display = 'block';
                    }
                });
            });
        }
        // Off-canvas menu toggle
        var navButton = document.getElementById('nav-button');
        var offCanvas = document.getElementById('arch-menu');
        if (navButton && offCanvas) {
            navButton.addEventListener('click', function(event) {
                event.preventDefault();
                if (offCanvas.style.display === 'block') {
                    offCanvas.style.display = 'none';
                } else {
                    offCanvas.style.display = 'block';
                }
            });
        }

        // Hover trailer preview: play MP4 trailer on hover over cards.
        var cards = document.querySelectorAll('[data-trailer]');
        cards.forEach(function(card) {
            var video;
            card.addEventListener('mouseenter', function() {
                var url = card.getAttribute('data-trailer');
                if (!url) return;
                // Create video element if not exists.
                if (!video) {
                    video = document.createElement('video');
                    video.src = url;
                    video.muted = true;
                    video.loop = true;
                    video.playsInline = true;
                    video.style.position = 'absolute';
                    video.style.top = '0';
                    video.style.left = '0';
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.style.objectFit = 'cover';
                    video.style.zIndex = '1';
                    video.style.borderRadius = window.getComputedStyle(card).borderRadius;
                    video.style.pointerEvents = 'none';
                    card.style.position = 'relative';
                    card.appendChild(video);
                }
                video.style.display = 'block';
                video.play().catch(function(){});
            });
            card.addEventListener('mouseleave', function() {
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                    video.style.display = 'none';
                }
            });
        });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'mxseries_custom_footer_scripts' );

/**
 * Custom pagination displaying a limited number of page links with ellipses.
 *
 * @param WP_Query $query The query to paginate.
 */
function mxseries_custom_pagination( $query ) {
    if ( ! $query ) {
        global $wp_query;
        $query = $wp_query;
    }
    $total = $query->max_num_pages;
    if ( $total <= 1 ) {
        return '';
    }
    $current = max( 1, get_query_var( 'paged', 1 ) );
    $max_links = 6; // maximum number of numbered links to show
    $output = '<div id="pages"><div class="wpm-pagination justify-content-center"><ul id="wpm-pages">';
    // Previous link
    if ( $current > 1 ) {
        $output .= '<li id="prev"><a href="' . esc_url( get_pagenum_link( $current - 1 ) ) . '">≪</a></li>';
    }
    $pages = array();
    // Always include first page
    $pages[] = 1;
    // Calculate range around current page
    $range = floor( ( $max_links - 2 ) / 2 );
    $start = max( 2, $current - $range );
    $end   = min( $total - 1, $current + $range );
    // Add ellipsis if start is greater than 2
    if ( $start > 2 ) {
        $pages[] = '...';
    }
    for ( $i = $start; $i <= $end; $i++ ) {
        $pages[] = $i;
    }
    // Add ellipsis if end less than total - 1
    if ( $end < $total - 1 ) {
        $pages[] = '...';
    }
    // Always include last page if total > 1
    if ( $total > 1 ) {
        $pages[] = $total;
    }
    // Remove duplicates and keep order
    $pages = array_values( array_unique( $pages ) );
    foreach ( $pages as $page ) {
        if ( $page === '...' ) {
            $output .= '<li class="dots">…</li>';
        } else {
            $class = ( $page == $current ) ? ' class="active"' : '';
            $output .= '<li' . $class . '><a href="' . esc_url( get_pagenum_link( $page ) ) . '">' . $page . '</a></li>';
        }
    }
    // Next link
    if ( $current < $total ) {
        $output .= '<li id="next"><a href="' . esc_url( get_pagenum_link( $current + 1 ) ) . '">≫</a></li>';
    }
    $output .= '</ul></div></div>';
    return $output;
}

/**
 * Override the template used for certain top‑level slugs.
 *
 * Even after changing taxonomy slugs, WordPress may still treat `/model` or
 * `/ott` as non‑existent posts and return a 404. This filter checks the
 * current request path and, if it matches these slugs, loads the
 * corresponding page templates directly. This means you don’t need
 * to create pages in the admin; simply visiting `/model` or `/ott` will
 * display the models or OTT portfolio.
 *
 * @param string $template Path to the template file.
 * @return string Modified path if a special slug is detected.
 */
function mxseries_override_top_level_templates( $template ) {
    // Determine the current request path relative to the site URL.
    global $wp;
    if ( empty( $wp ) || ! isset( $wp->request ) ) {
        return $template;
    }
    $request = trim( $wp->request, '/' );
    // Match exactly 'model' or 'ott'. Do not override deeper paths.
    if ( 'model' === $request ) {
        $custom = locate_template( 'page-model.php' );
        if ( $custom ) {
            return $custom;
        }
    } elseif ( 'ott' === $request ) {
        $custom = locate_template( 'page-ott.php' );
        if ( $custom ) {
            return $custom;
        }
    }
    return $template;
}
add_filter( 'template_include', 'mxseries_override_top_level_templates' );
add_theme_support( 'custom-logo' );

register_nav_menus( array(
    'primary'   => __( 'Primary Menu', 'mxseries-html-theme' ),
    'secondary' => __( 'Secondary Menu', 'mxseries-html-theme' ),
) );

?>