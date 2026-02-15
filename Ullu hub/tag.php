<?php
/**
 * Tag archive template
 *
 * Shows posts belonging to a WordPress tag
 *
 * @package MXSeries HTML Theme
 */

get_header();

// Get current tag object
$tag = get_queried_object();

// Total posts in this tag
$post_count = 0;
if ( $tag && isset( $tag->count ) ) {
    $post_count = intval( $tag->count );
}
?>

<h1 class="relt">
    <?php echo esc_html( single_tag_title('', false) ); ?>
    <?php if ( $post_count > 0 ) : ?>
        <span style="font-weight: normal; font-size: 0.8em;">
            (<?php echo esc_html( $post_count ); ?> <?php _e( 'Videos', 'mxseries-html-theme' ); ?>)
        </span>
    <?php endif; ?>
</h1>

<div class="vs">
    <div class="vul-box">
        <?php
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        // 🔥 TAG QUERY
        $tag_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 12,
            'paged'          => $paged,
            'tag_id'         => $tag ? $tag->term_id : 0,
        ) );

        if ( $tag_query->have_posts() ) :
            while ( $tag_query->have_posts() ) : $tag_query->the_post();

                $post_id   = get_the_ID();
                $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
                // Determine the number of episodes using helper (supports legacy embed code posts).
                $episodes  = mxseries_get_episode_count( $post_id );
                $views     = (int) get_post_meta( $post_id, '_mxseries_view_count', true );
                $trailer   = get_post_meta( $post_id, '_mxseries_trailer_url', true );

                $style = $image_url
                    ? "background-size:cover;background-position:top;background-image:url('" . esc_url( $image_url ) . "');"
                    : '';

                $trailer_attr = $trailer ? ' data-trailer="' . esc_url( $trailer ) . '"' : '';
                ?>

                <a class="godx-box vblock"
                   style="<?php echo esc_attr( $style ); ?>"
                   title="<?php echo esc_attr( get_the_title() ); ?>"
                   href="<?php echo esc_url( get_permalink() ); ?>"
                   data-aos="zoom-in-up"<?php echo $trailer_attr; ?>>

                    <div class="time srs">
                        <?php echo esc_html( $episodes ); ?> <?php _e( 'Episodes', 'mxseries-html-theme' ); ?>
                    </div>

                    <div class="top-right eye"><?php echo esc_html( $views ); ?></div>

                    <h2 class="vtitle">
                        <?php echo esc_html( get_the_title() ); ?>
                        <span class="clock">
                            <?php echo esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ); ?>
                        </span>
                    </h2>
                </a>

            <?php endwhile;
        else :
            echo '<p>' . __( 'No posts found for this tag.', 'mxseries-html-theme' ) . '</p>';
        endif;

        wp_reset_postdata();
        ?>
    </div>

    <?php
    echo mxseries_custom_pagination( $tag_query );
    ?>
</div>

<?php get_footer(); ?>