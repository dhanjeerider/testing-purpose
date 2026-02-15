<?php
/**
 * Archive template for Model post type
 *
 * Displays all models in a grid format matching the original design. It
 * preserves the HTML structure (classes and IDs) used on the MXSeries site.
 *
 * @package MXSeries HTML Theme
 */

get_header();

?>

<h1 class="relt">All Models/stars</h1>
<div class="models big">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            $model_id   = get_the_ID();
            $image_url  = get_the_post_thumbnail_url( $model_id, 'full' );
            $series_cnt = get_post_meta( $model_id, '_mxseries_series_count', true );
            $series_cnt = $series_cnt ? intval( $series_cnt ) : 0;
            $view_cnt   = get_post_meta( $model_id, '_mxseries_model_view_count', true );
            $view_cnt   = $view_cnt ? intval( $view_cnt ) : 0;
            $style      = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
            echo '<a style="' . esc_attr( $style ) . '" href="' . esc_url( get_permalink() ) . '" data-aos="zoom-in-up">';
            echo '<div class="bgb">';
            echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
            echo '<div class="mmeta">';
            echo '<span class="MCount">';
            // Reuse the same icon as model cards.
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" opacity="0.5"></path><path d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z"></path><path d="M2.384 13.793c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.947 1.024.724 2.605.278 5.769l-.422 3c-.35 2.48-.525 3.721-1.422 4.464c-.897.743-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743c-.897-.743-1.072-1.983-1.422-4.464z"></path></g></svg> ' . esc_html( $series_cnt ) . '</span>';
            echo '<span class="CombinedViews eye">' . esc_html( $view_cnt ) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
        }
    } else {
        echo '<p>' . __( 'No models found.', 'mxseries-html-theme' ) . '</p>';
    }
    ?>
</div>

<?php get_footer(); ?>