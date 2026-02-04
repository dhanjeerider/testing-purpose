<?php
/**
 * Template part for displaying a movie card in the grid.
 *
 * This template expects to be used inside The Loop.  It wraps each post
 * inside an anchor linking to the single page and displays the featured
 * image, a quality badge (if available), and the title.  The class names
 * are kept identical to those in the original HTML provided by the user so
 * that the existing CSS continues to apply.
 */

// Determine quality from custom field; fallback to a default label.
$quality = get_post_meta( get_the_ID(), 'quality', true );
if ( empty( $quality ) ) {
    $quality = 'HD';
}

?>
<a href="<?php the_permalink(); ?>">
    <div class="poster-card">
        <div class="poster-inner">
            <div class="poster-image">
                <?php if ( has_post_thumbnail() ) : ?>
                    <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                <?php else : ?>
                    <!-- Placeholder image if no featured image is set.  The user can supply CSS to style this element. -->
                    <div class="no-image" style="width:100%;height:100%;background:#ccc;"></div>
                <?php endif; ?>
                <span class="poster-quality"><?php echo esc_html( $quality ); ?></span>
            </div>
            <div class="poster-info">
                <p class="poster-title"><?php the_title(); ?></p>
            </div>
        </div>
    </div>
</a>