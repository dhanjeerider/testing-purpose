<?php
// search.php — AppStore Pro V5
get_header();
?>
<main id="main" class="max-w-6xl mx-auto px-4 py-8 min-h-screen" tabindex="-1">

	<header class="mb-6 aspv5-reveal">
		<p class="text-xs font-bold uppercase tracking-widest text-primary mb-1"><?php esc_html_e( 'Search Results', 'aspv5' ); ?></p>
		<h1 class="text-2xl font-bold text-gray-900 dark:text-white">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Results for: %s', 'aspv5' ),
				'<span class="text-primary">' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>
		<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
			<?php
			$total = $wp_query->found_posts;
			printf(
				/* translators: %d: number of results */
				esc_html( _n( '%d result found', '%d results found', $total, 'aspv5' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</p>
	</header>

	<!-- Search form -->
	<div class="mb-8 aspv5-reveal">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex gap-2">
			<input type="search" name="s"
			       value="<?php echo esc_attr( get_search_query() ); ?>"
			       placeholder="<?php esc_attr_e( 'Search apps & games...', 'aspv5' ); ?>"
			       class="flex-1 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/40">
			<button type="submit" class="px-5 py-3 rounded-2xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition-opacity">
				<?php esc_html_e( 'Search', 'aspv5' ); ?>
			</button>
		</form>
	</div>

	<?php if ( have_posts() ) : ?>
		<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				$post_type = get_post_type();
				if ( in_array( $post_type, [ 'app', 'game' ], true ) ) {
					get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] );
				} else {
					get_template_part( 'template-parts/content/content', 'post' );
				}
				?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( [ 'class' => 'aspv5-pagination flex justify-center gap-1 mt-10', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ] ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>

</main>
<?php get_footer(); ?>
