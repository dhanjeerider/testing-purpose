<?php
/**
 * Template Name: All Tags Page
 *
 * Shows all WordPress tags in simple link format
 */

get_header();
?>

<h1 class="relt">All Tags/Topics</h1>

<div class="tags">

<?php
$tags = get_tags( array(
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

if ( ! empty( $tags ) ) :

    foreach ( $tags as $tag ) : ?>

        <a id="<?php echo esc_attr( $tag->term_id ); ?>"
           href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
            <?php echo esc_html( $tag->name ); ?>
        </a>

    <?php endforeach;

else :
    echo '<p>No tags found.</p>';
endif;
?>

</div>

<?php get_footer(); ?>