<?php
/**
 * Index template
 *
 * WordPress requires an index.php file in every theme. This minimal
 * template is used as a fallback for all queries that do not have a more
 * specific template. It loops over posts and displays their content in a
 * simple format. Feel free to customize this file further to match your
 * design, or rely on specific templates such as front-page.php for the
 * homepage.
 *
 * @package MXSeries HTML Theme
 */

get_header();

?>
<main class="site-main">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php if ( is_singular() ) : ?>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                <?php else : ?>
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                <?php endif; ?>
                <div class="entry-content">
                    <?php
                    if ( is_singular() ) {
                        the_content();
                    } else {
                        the_excerpt();
                    }
                    ?>
                </div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p><?php _e( 'No posts found.', 'mxseries-html-theme' ); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>