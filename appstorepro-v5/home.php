<?php
// home.php — AppStore Pro V5
get_header();
?>
<main id="main" tabindex="-1" class="pb-8">
	<?php get_template_part( 'template-parts/home/hero' ); ?>
	<?php get_template_part( 'template-parts/home/categories-section' ); ?>
	<?php get_template_part( 'template-parts/home/featured-apps' ); ?>

	<!-- Top Games section -->
	<section class="py-8 bg-gray-50 dark:bg-gray-950">
		<div class="max-w-6xl mx-auto px-4">
			<div class="aspv5-reveal flex items-center justify-between mb-5">
				<div class="aspv5-section-title-bar">
					<div>
						<p class="text-xs font-bold uppercase tracking-widest text-primary mb-0.5"><?php esc_html_e( 'Popular', 'aspv5' ); ?></p>
						<h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php esc_html_e( 'Top Games', 'aspv5' ); ?></h2>
					</div>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>"
				   class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity flex items-center gap-1">
					<?php esc_html_e( 'See all', 'aspv5' ); ?>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-3.5 h-3.5"><polyline points="9 18 15 12 9 6"/></svg>
				</a>
			</div>
			<?php
			$games_query = new WP_Query( [
				'post_type'      => 'game',
				'posts_per_page' => 6,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			] );
			if ( $games_query->have_posts() ) : ?>
				<div class="flex flex-col gap-3 aspv5-reveal">
					<?php while ( $games_query->have_posts() ) : $games_query->the_post(); ?>
						<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'list' ] ); ?>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Latest Apps compact grid -->
	<section class="py-8 bg-white dark:bg-gray-900">
		<div class="max-w-6xl mx-auto px-4">
			<div class="aspv5-reveal flex items-center justify-between mb-5">
				<div class="aspv5-section-title-bar">
					<div>
						<p class="text-xs font-bold uppercase tracking-widest text-primary mb-0.5"><?php esc_html_e( 'Hot Picks', 'aspv5' ); ?></p>
						<h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php esc_html_e( 'Latest Apps', 'aspv5' ); ?></h2>
					</div>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
				   class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity flex items-center gap-1">
					<?php esc_html_e( 'See all', 'aspv5' ); ?>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-3.5 h-3.5"><polyline points="9 18 15 12 9 6"/></svg>
				</a>
			</div>
			<?php
			$apps_query = new WP_Query( [
				'post_type'      => 'app',
				'posts_per_page' => 12,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			] );
			if ( $apps_query->have_posts() ) :
			?>
				<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 aspv5-reveal">
					<?php while ( $apps_query->have_posts() ) : $apps_query->the_post(); ?>
						<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'compact' ] ); ?>
					<?php endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( is_active_sidebar( 'home-widgets' ) ) : ?>
	<!-- Home Page Widgets -->
	<section class="py-8 bg-gray-50 dark:bg-gray-950">
		<div class="max-w-6xl mx-auto px-4">
			<div class="aspv5-home-widgets grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php dynamic_sidebar( 'home-widgets' ); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

</main>
<?php get_footer(); ?>
