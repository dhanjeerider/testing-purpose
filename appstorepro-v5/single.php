<?php
// single.php — AppStore Pro V5
get_header();
?>
<main id="main" class="max-w-4xl mx-auto px-4 py-8 min-h-screen" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6' ); ?>>
			<header class="mb-6">
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php the_title(); ?></h1>
				<div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
					<?php aspv5_posted_on(); ?>
					<span>·</span>
					<?php aspv5_posted_by(); ?>
				</div>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="mb-6 rounded-xl overflow-hidden">
					<?php the_post_thumbnail( 'large', [ 'class' => 'w-full h-auto' ] ); ?>
				</div>
			<?php endif; ?>
			<div class="entry-content prose dark:prose-invert max-w-none text-sm leading-relaxed text-gray-700 dark:text-gray-300">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
