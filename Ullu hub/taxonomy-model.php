<?php
/**
 * Template for Model taxonomy terms
 *
 * Displays a model profile (term) with its image, name, series count and
 * view count. Below the profile it lists all series posts associated
 * with this model. The layout closely follows the original single-model
 * template but works for taxonomy terms instead of a custom post type.
 *
 * @package MXSeries HTML Theme
 */

get_header();

// Current model term.
$term = get_queried_object();
if ( ! $term || ! isset( $term->term_id ) ) {
    get_footer();
    return;
}

// Retrieve metadata for the model.
$image_url   = get_term_meta( $term->term_id, 'mxseries_model_image', true );
$series_cnt  = get_term_meta( $term->term_id, 'mxseries_series_count', true );
$series_cnt  = $series_cnt ? intval( $series_cnt ) : 0;
$view_count  = get_term_meta( $term->term_id, 'mxseries_model_view_count', true );
$view_count  = $view_count ? intval( $view_count ) : 0;

// Profile background style.
$profile_style = $image_url ? "background-image: url('" . esc_url( $image_url ) . "');" : '';
?>

<div class="profile" style="<?php echo esc_attr( $profile_style ); ?>">
    <div class="profile2">
        <?php if ( $image_url ) : ?>
            <img loading="lazy" class="dp" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" />
        <?php endif; ?>
        <h1><?php echo esc_html( $term->name ); ?></h1>
        <div class="pmeta">
            <span class="vdo"><?php echo esc_html( $series_cnt ); ?> <?php _e( 'Series', 'mxseries-html-theme' ); ?></span>
            <span class="eye"><?php echo esc_html( $view_count ); ?></span>
        </div>
    </div>
</div>

<div class="vs">
    <div class="cdes">
        <h1 class="relt"><?php echo esc_html( $term->name ); ?> Web Series</h1>
        <?php
        // Output the term description, if present.
        if ( ! empty( $term->description ) ) {
            echo '<p>' . esc_html( $term->description ) . '</p>';
        }
        ?>
    </div>
    <ul class="vul-box">
        <?php
        // Query series posts associated with this model term.
        $series_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'model',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ),
            ),
        ) );
        if ( $series_query->have_posts() ) {
            while ( $series_query->have_posts() ) {
                $series_query->the_post();
                $post_id   = get_the_ID();
                $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
                // Use helper to determine episode count. Supports legacy embed code posts.
                $episodes  = mxseries_get_episode_count( $post_id );
                $views     = get_post_meta( $post_id, '_mxseries_view_count', true );
                $views     = $views ? intval( $views ) : 0;
                $style     = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
                ?>
                <a class="godx-box vblock" style="<?php echo esc_attr( $style ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" href="<?php echo esc_url( get_permalink() ); ?>" data-aos="zoom-in-up">
                    <div class="time srs"> <?php echo esc_html( $episodes ); ?> <?php _e( 'Episodes', 'mxseries-html-theme' ); ?></div>
                    <div class="top-right eye"><?php echo esc_html( $views ); ?></div>
                    <h2 class="vtitle"><?php echo esc_html( get_the_title() ); ?> <span class="clock"><?php echo esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ); ?></span></h2>
                </a>
                <?php
            }
        }
        wp_reset_postdata();
        ?>
    </ul>
    <div class="cdes">
        <br />
        <?php
        // Additional description or tags: you can list categories or leave a default message.
        echo '<p>' . __( 'Explore more web series featuring this model.', 'mxseries-html-theme' ) . '</p>';
        ?>
    </div>
</div>

<?php get_footer(); ?>