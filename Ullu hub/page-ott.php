<?php
/**
 * Template Name: OTT Platforms Index
 *
 * This template displays a paginated list of all OTT platform terms
 * using the same card layout as the taxonomy archives. Because the
 * OTT taxonomy slug has been updated to `ott-platforms`, the root
 * `/ott` URL can safely point to a static page. Assign this template
 * to a page with the slug `ott` to create a portfolio of all OTT
 * platforms.
 *
 * @package MXSeries HTML Theme
 */

get_header();

// Current page for pagination.
$paged    = max( 1, get_query_var( 'paged', 1 ) );
$per_page = 12;
$offset   = ( $paged - 1 ) * $per_page;

// Count total OTT terms.
$total_terms = wp_count_terms( 'ott', array( 'hide_empty' => false ) );
if ( is_wp_error( $total_terms ) ) {
    $total_terms = 0;
}

// Retrieve OTT terms for this page.
$otts = get_terms( array(
    'taxonomy'   => 'ott',
    'hide_empty' => false,
    'number'     => $per_page,
    'offset'     => $offset,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

?>

<h1 class="relt"><?php esc_html_e( 'All Series/Channel', 'mxseries-html-theme' ); ?></h1>
<div class="series" style="justify-content: center;">
    <?php
    if ( ! empty( $otts ) && ! is_wp_error( $otts ) ) {
        foreach ( $otts as $ott ) {
            // Thumbnail image from term meta.
            $image_url = get_term_meta( $ott->term_id, 'mxseries_ott_thumbnail', true );
            $style     = $image_url ? "background-size: cover; background-position: center; background-image: url('" . esc_url( $image_url ) . "');" : '';
            // Count how many posts are assigned to this OTT term using the term object’s count property.
            $post_count = isset( $ott->count ) ? intval( $ott->count ) : 0;
            echo '<a style="' . esc_attr( $style ) . '" href="' . esc_url( get_term_link( $ott ) ) . '" data-aos="zoom-in-up">';
            echo '<span class="post-count">';
            // SVG icon reused from the original OTT archive design.
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" opacity="0.5"></path><path d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z"></path><path d="M2.384 13.793c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.947 1.024.724 2.605.278 5.769l-.422 3c-.35 2.48-.525 3.721-1.422 4.464c-.897.743-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743c-.897-.743-1.072-1.983-1.422-4.464z"></path></g></svg>';
            echo ' ' . esc_html( $post_count );
            echo '</span>';
            echo '</a>';
        }
    } else {
        echo '<p>' . esc_html__( 'No OTT platforms found.', 'mxseries-html-theme' ) . '</p>';
    }
    ?>
</div>

<?php
// Pagination for OTT terms.
$total_pages = ( $per_page > 0 ) ? ceil( $total_terms / $per_page ) : 1;
if ( $total_pages > 1 ) {
    $fake_query = new stdClass();
    $fake_query->max_num_pages = $total_pages;
    echo mxseries_custom_pagination( $fake_query );
}

get_footer();