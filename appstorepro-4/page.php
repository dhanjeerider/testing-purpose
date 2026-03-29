<?php
// page.php — Default page template
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-article' ); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
					<?php
					wp_link_pages( [
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'appstorepro' ),
						'after'  => '</div>',
					] );
					?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
