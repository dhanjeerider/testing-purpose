<?php
// 404.php — AppStore Pro V5
get_header();
?>
<main id="main" class="max-w-2xl mx-auto px-4 py-16 min-h-screen flex flex-col items-center justify-center text-center" tabindex="-1">

	<div class="text-8xl font-black text-gray-100 dark:text-gray-800 mb-4 select-none">404</div>
	<div class="text-5xl mb-6">🤔</div>
	<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
		<?php esc_html_e( 'Page Not Found', 'aspv5' ); ?>
	</h1>
	<p class="text-gray-500 dark:text-gray-400 mb-8 text-sm max-w-sm">
		<?php esc_html_e( "The page you're looking for doesn't exist. Try searching for apps or games.", 'aspv5' ); ?>
	</p>

	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-full max-w-sm flex gap-2 mb-8">
		<input type="search" name="s"
		       placeholder="<?php esc_attr_e( 'Search...', 'aspv5' ); ?>"
		       class="flex-1 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/40">
		<button type="submit" class="px-5 py-3 rounded-2xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition-opacity">
			<?php esc_html_e( 'Go', 'aspv5' ); ?>
		</button>
	</form>

	<div class="flex gap-3 flex-wrap justify-center">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-5 py-2.5 rounded-2xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition-opacity">
			<?php esc_html_e( 'Go Home', 'aspv5' ); ?>
		</a>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>" class="px-5 py-2.5 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold hover:border-primary dark:hover:border-primary transition-colors">
			<?php esc_html_e( 'Browse Apps', 'aspv5' ); ?>
		</a>
	</div>

</main>
<?php get_footer(); ?>
