<?php
/**
 * Template for OTT taxonomy terms
 *
 * Displays series posts associated with a particular OTT platform. Posts are
 * presented in the same card format as used on the home page. Pagination
 * is included when there are multiple pages of results.
 *
 * @package MXSeries HTML Theme
 */

get_header();

// Current OTT term.
$term = get_queried_object();

// Retrieve the thumbnail for this OTT term.
$thumbnail = '';
if ( $term && isset( $term->term_id ) ) {
    $thumbnail = get_term_meta( $term->term_id, 'mxseries_ott_thumbnail', true );
}
?>

<?php
$post_count = intval( $term->count );
?>

<div class="profile" style="height:300px; background-size:cover; background-position:center; background-image:url('<?php echo esc_url( $thumbnail ); ?>');">

    <div class="cpro">
        <h1><?php echo esc_html( $term->name ); ?></h1>
        <span class="vdo"><?php echo esc_html( $post_count ); ?> Series</span>
    </div>

</div>

<div class="vs">
    <div class="vul-box">
        <?php
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $posts_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 12,
            'paged'          => $paged,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'ott',
                    'field'    => 'slug',
                    'terms'    => $term->slug,
                ),
            ),
        ) );
        if ( $posts_query->have_posts() ) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                $post_id   = get_the_ID();
                $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
                // Retrieve the episode count using helper (supports legacy embed code).
                $episodes  = mxseries_get_episode_count( $post_id );
                $views     = get_post_meta( $post_id, '_mxseries_view_count', true );
                $views     = $views ? intval( $views ) : 0;
                $style     = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
                // Add trailer attribute if available to play preview on hover.
                $trailer = get_post_meta( $post_id, '_mxseries_trailer_url', true );
                $trailer_attr = $trailer ? ' data-trailer="' . esc_url( $trailer ) . '"' : '';
                echo '<a class="godx-box vblock" style="' . esc_attr( $style ) . '" title="' . esc_attr( get_the_title() ) . '" href="' . esc_url( get_permalink() ) . '" data-aos="zoom-in-up"' . $trailer_attr . '>';
                echo '<div class="time srs"> ' . esc_html( $episodes ) . ' ' . __( 'Episodes', 'mxseries-html-theme' ) . '</div>';
                echo '<div class="top-right eye">' . esc_html( $views ) . '</div>';
                echo '<h2 class="vtitle">' . esc_html( get_the_title() ) . ' <span class="clock">' . esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ) . '</span></h2>';
                echo '</a>';
            }
        } else {
            echo '<p>' . __( 'No series found for this OTT platform.', 'mxseries-html-theme' ) . '</p>';
        }
        wp_reset_postdata();
        ?>
    </div>
    <?php
    /*
     * Use the theme’s custom pagination helper so only a handful of numbered
     * pages are shown with ellipses. This keeps the markup consistent with
     * other archive pages and obeys the “show max 6” requirement.
     */
    echo mxseries_custom_pagination( $posts_query );
    ?>
</div>

<?php get_footer(); ?>