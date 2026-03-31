<?php
// index.php — AppStore Pro V5 (fallback)
get_header();
?>
<main id="main" class="max-w-6xl mx-auto px-4 py-8 min-h-screen" tabindex="-1">

	<?php if ( have_posts() ) : ?>
		<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( [ 'class' => 'aspv5-pagination flex justify-center gap-1 mt-10', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ] ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>

</main>
<?php get_footer(); ?>
