<?php
/**
 * Front page template
 *
 * This template replicates the MXSeries home page layout with dynamic
 * data. It lists OTT platforms, top models, latest series posts and
 * provides pagination and quick links. All classes and IDs mirror the
 * original HTML structure so your own CSS can target elements reliably.
 *
 * @package MXSeries HTML Theme
 */

get_header();

?>
<div class="scrollx series">
    <?php
    // Query OTT terms.
    $ott_terms = get_terms( array(
        'taxonomy'   => 'ott',
        'hide_empty' => false,
    ) );
    if ( ! empty( $ott_terms ) && ! is_wp_error( $ott_terms ) ) {
        foreach ( $ott_terms as $term ) {
            $thumbnail = get_term_meta( $term->term_id, 'mxseries_ott_thumbnail', true );
            $style     = $thumbnail ? "background-size: cover; background-position: center; background-image: url('" . esc_url( $thumbnail ) . "');" : '';
            $count     = intval( $term->count );
            echo '<a style="' . esc_attr( $style ) . '" href="' . esc_url( get_term_link( $term ) ) . '" data-aos="zoom-in-up">';
            echo '<span class="post-count">';
            // Include icon inline to match original markup.
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" opacity="0.5"></path><path d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z"></path><path d="M2.384 13.793c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.947 1.024.724 2.605.278 5.769l-.422 3c-.35 2.48-.525 3.721-1.422 4.464c-.897.743-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743c-.897-.743-1.072-1.983-1.422-4.464z"></path></g></svg> ' . esc_html( $count ) . '</span>';
            echo '</a>';
        }
        // Link to a page listing all OTT platforms. Users should create a page with slug "ott" if desired.
        echo '<a class="viewall" href="' . esc_url( home_url( '/ott/' ) ) . '" data-aos="zoom-in-up">≫</a>';
    }
    ?>
</div>

<h3 class="relt">Top 20 dancestars</h3>
<div class="models scrollx">
    <?php
    /*
     * Query top models as taxonomy terms. Terms are ordered by the
     * custom meta key mxseries_series_count to list actors with the most
     * series first. Because get_terms() does not natively support
     * numeric meta sorting, we use WP_Term_Query.
     */
    $model_query = new WP_Term_Query( array(
        'taxonomy'   => 'model',
        'number'     => 20,
        'meta_key'   => 'mxseries_series_count',
        'orderby'    => 'meta_value_num',
        'order'      => 'DESC',
        'hide_empty' => false,
    ) );
    $model_terms = $model_query->get_terms();
    if ( ! empty( $model_terms ) && ! is_wp_error( $model_terms ) ) {
        foreach ( $model_terms as $term ) {
            $image_url   = get_term_meta( $term->term_id, 'mxseries_model_image', true );
            $series_cnt  = get_term_meta( $term->term_id, 'mxseries_series_count', true );
            $model_views = get_term_meta( $term->term_id, 'mxseries_model_view_count', true );
            $series_cnt  = $series_cnt ? intval( $series_cnt ) : 0;
            $model_views = $model_views ? intval( $model_views ) : 0;
            $style = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
            echo '<a style="' . esc_attr( $style ) . '" href="' . esc_url( get_term_link( $term ) ) . '" data-aos="zoom-in-up">';
            echo '<div class="bgb">';
            echo '<h3>' . esc_html( $term->name ) . '</h3>';
            echo '<div class="mmeta">';
            echo '<span class="MCount">';
            // Include same SVG icon used for MCount.
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" opacity="0.5"></path><path d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z"></path><path d="M2.384 13.793c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.947 1.024.724 2.605.278 5.769l-.422 3c-.35 2.48-.525 3.721-1.422 4.464c-.897.743-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743c-.897-.743-1.072-1.983-1.422-4.464z"></path></g></svg> ' . esc_html( $series_cnt ) . '</span>';
            echo '<span class="CombinedViews eye">' . esc_html( $model_views ) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
        }
        // Link to a page listing all models. This uses the taxonomy base slug.
        echo '<a class="viewall" href="' . esc_url( home_url( '/model/' ) ) . '" data-aos="zoom-in-up">≫</a>';
    }
    ?>
</div>

<h3 class="relt"> Latest dance Videos </h3>
<div class="vs">
    <div class="vul-box">
        <?php
        // Query latest posts.
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $videos_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 10,
            'paged'          => $paged,
        ) );
        if ( $videos_query->have_posts() ) {
            while ( $videos_query->have_posts() ) {
                $videos_query->the_post();
                $post_id    = get_the_ID();
                $image_url  = get_the_post_thumbnail_url( $post_id, 'full' );
                // Use helper to get episode count. This falls back to 1 when
                // using an embedded video via the legacy video_embed_code field.
                $episode_count = mxseries_get_episode_count( $post_id );
                $view_count = get_post_meta( $post_id, '_mxseries_view_count', true );
                $view_count = $view_count ? intval( $view_count ) : 0;
                $style = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
                // Retrieve optional trailer URL for hover preview.
                $trailer = get_post_meta( $post_id, '_mxseries_trailer_url', true );
                $trailer_attr = $trailer ? ' data-trailer="' . esc_url( $trailer ) . '"' : '';
                echo '<a class="godx-box vblock" style="' . esc_attr( $style ) . '" title="' . esc_attr( get_the_title() ) . '" href="' . esc_url( get_permalink() ) . '" data-aos="zoom-in-up"' . $trailer_attr . '>';                
                echo '<div class="time srs"> ' . esc_html( $episode_count ) . ' Episodes</div>';
                echo '<div class="top-right eye">' . esc_html( $view_count ) . '</div>';
                echo '<h2 class="vtitle">' . esc_html( get_the_title() ) . ' <span class="clock">' . esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ) . '</span></h2>';
                echo '</a>';
            }
        }
        wp_reset_postdata();
        ?>
    </div>
    <?php
    /*
     * Use the custom pagination helper defined in functions.php. This helper limits
     * the number of numbered links to six and inserts ellipses when there are
     * additional pages. It keeps the same markup as the original site
     * (list items inside a container) so your CSS continues to work.
     */
    echo mxseries_custom_pagination( $videos_query );
    ?>
</div>

<div class="ffm">
    <a class="srs" href="<?php echo esc_url( site_url( '/ott/' ) ); ?>">OTT</a>
    <a class="user" href="<?php echo esc_url( home_url( '/model/' ) ); ?>">Actress</a>
    <a class="tagg" href="<?php echo esc_url( site_url( '/tag/' ) ); ?>">Tags</a>
    <?php
    // Display top 3 categories as languages.
    $categories = get_categories( array(
        'number'     => 3,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'hide_empty' => true,
    ) );
    foreach ( $categories as $cat ) {
        echo '<a class="fold" href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
    }
    ?>
</div>

<?php get_footer(); ?>