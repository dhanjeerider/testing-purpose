<?php
/**
 * Category archive template
 *
 * Shows series posts belonging to a WordPress category using the same
 * card layout as the home page and OTT archives. The category title
 * is displayed along with the number of posts available in that
 * category. Pagination is provided via the custom helper so only
 * six numbered links are shown at a time.
 *
 * @package MXSeries HTML Theme
 */

get_header();

// Get the current category object.
$category = get_queried_object();

// Determine total number of posts in the category for the header.
$post_count = 0;
if ( $category && isset( $category->count ) ) {
    $post_count = intval( $category->count );
}
?>

<h1 class="relt">
    <?php echo esc_html( $category->name ); ?>
    <?php if ( $post_count > 0 ) : ?>
        <span style="font-weight: normal; font-size: 0.8em;"> (<?php echo esc_html( $post_count ); ?> <?php _e( 'Videos', 'mxseries-html-theme' ); ?>)</span>
    <?php endif; ?>
</h1>
<div class="vs">
    <div class="vul-box">
        <?php
        // Set up pagination variables.
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        // Query posts from the current category.
        $cat_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 12,
            'paged'          => $paged,
            'cat'            => $category ? $category->term_id : 0,
        ) );

        if ( $cat_query->have_posts() ) {
            while ( $cat_query->have_posts() ) {
                $cat_query->the_post();
                $post_id   = get_the_ID();
                $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
                // Determine number of episodes using helper. Supports posts with embed codes.
                $episodes  = mxseries_get_episode_count( $post_id );
                $views     = get_post_meta( $post_id, '_mxseries_view_count', true );
                $views     = $views ? intval( $views ) : 0;
                $style     = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
                // Trailer attribute for hover preview.
                $trailer = get_post_meta( $post_id, '_mxseries_trailer_url', true );
                $trailer_attr = $trailer ? ' data-trailer="' . esc_url( $trailer ) . '"' : '';
                ?>
                <a class="godx-box vblock" style="<?php echo esc_attr( $style ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" href="<?php echo esc_url( get_permalink() ); ?>" data-aos="zoom-in-up"<?php echo $trailer_attr; ?>>
                    <div class="time srs"> <?php echo esc_html( $episodes ); ?> <?php _e( 'Episodes', 'mxseries-html-theme' ); ?></div>
                    <div class="top-right eye"><?php echo esc_html( $views ); ?></div>
                    <h2 class="vtitle"><?php echo esc_html( get_the_title() ); ?> <span class="clock"><?php echo esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ); ?></span></h2>
                </a>
                <?php
            }
        } else {
            echo '<p>' . __( 'No series found in this category.', 'mxseries-html-theme' ) . '</p>';
        }
        // Reset post data.
        wp_reset_postdata();
        ?>
    </div>
    <?php
    // Output custom pagination.
    echo mxseries_custom_pagination( $cat_query );
    ?>
</div>

<?php get_footer(); ?>