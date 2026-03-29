<?php
// archive.php — Blog archive
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<header class="page-header">
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/content/content', 'post' ); ?>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( [ 'class' => 'pas-pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ] ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
