<?php
// template-parts/content/content-none.php — AppStore Pro V5
?>
<div class="flex flex-col items-center justify-center py-20 text-center">
	<div class="text-6xl mb-4">📭</div>
	<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
		<?php esc_html_e( 'Nothing found', 'aspv5' ); ?>
	</h2>
	<p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
		<?php esc_html_e( 'No apps or games match your query. Try searching for something else.', 'aspv5' ); ?>
	</p>
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex gap-2 w-full max-w-sm">
		<input type="search" name="s"
		       value="<?php echo esc_attr( get_search_query() ); ?>"
		       placeholder="<?php esc_attr_e( 'Search...', 'aspv5' ); ?>"
		       class="flex-1 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/40">
		<button type="submit" class="px-4 py-2.5 rounded-2xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition-opacity">
			<?php esc_html_e( 'Search', 'aspv5' ); ?>
		</button>
	</form>
</div>
