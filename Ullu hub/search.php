<?php
/**
 * Search results template
 *
 * Displays posts matching the search query in the same card layout
 * used throughout the site. This file replaces the previous category
 * archive logic so that search results are not constrained to a
 * specific category and instead use the native WordPress search
 * query ($wp_query). Pagination is handled by the custom helper.
 *
 * @package MXSeries HTML Theme
 */

get_header();
?>

<h1 class="relt">
    <?php
    // Always show the search query in the heading.
    /* translators: %s: search query */
    printf( __( 'Search Results for: %s', 'mxseries-html-theme' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
    ?>
</h1>

<div class="vs">
    <div class="vul-box">
        <?php
        /*
         * Loop through posts matching the search. We use the global
         * $wp_query, which is already populated with the search
         * results. Each post is displayed using the same card
         * structure as category and archive templates. If no posts
         * match, output a message. The helper function
         * mxseries_get_episode_count() provides correct episode
         * counts for posts with embed codes.
         */
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                $post_id   = get_the_ID();
                $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
                $episodes  = mxseries_get_episode_count( $post_id );
                $views     = get_post_meta( $post_id, '_mxseries_view_count', true );
                $views     = $views ? intval( $views ) : 0;
                $style     = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
                $trailer   = get_post_meta( $post_id, '_mxseries_trailer_url', true );
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
            echo '<p>' . __( 'No results found for your search query.', 'mxseries-html-theme' ) . '</p>';
        }
        ?>
    </div>
    <?php
    // Use the custom pagination helper on the global query.
    global $wp_query;
    echo mxseries_custom_pagination( $wp_query );
    ?>
</div>

<?php get_footer(); ?>