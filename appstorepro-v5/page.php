<?php
// page.php — AppStore Pro V5
get_header();
?>
<main id="main" class="max-w-4xl mx-auto px-4 py-8 min-h-screen" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6' ); ?>>
			<header class="mb-6">
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content text-sm leading-relaxed text-gray-700 dark:text-gray-300">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
