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
                <?php 
                $thumbnail_url = '';
                if ( has_post_thumbnail() ) {
                    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                } else {
                    // Get first valid image from post content (not YouTube/embed URLs)
                    $content = get_post_field( 'post_content', get_the_ID() );
                    // Match only <img> tags specifically
                    preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches );
                    if ( ! empty( $matches[1] ) ) {
                        // Filter out YouTube, embed, and data URLs
                        foreach ( $matches[1] as $img_url ) {
                            if ( strpos( $img_url, 'youtube.com' ) === false && 
                                 strpos( $img_url, 'youtu.be' ) === false &&
                                 strpos( $img_url, 'data:image' ) === false &&
                                 strpos( $img_url, '/embed/' ) === false ) {
                                $thumbnail_url = $img_url;
                                break;
                            }
                        }
                    }
                }
                
                if ( $thumbnail_url ) : ?>
                    <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                <?php else : ?>
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