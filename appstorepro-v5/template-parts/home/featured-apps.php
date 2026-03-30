<?php
// template-parts/home/featured-apps.php — AppStore Pro V5
$featured_layout = get_theme_mod( 'aspv5_featured_layout', 'banner' );

$args = [
	'post_type'      => [ 'app', 'game' ],
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
];

$query = new WP_Query( $args );
if ( ! $query->have_posts() ) return;
?>
<section class="py-8">
	<div class="max-w-6xl mx-auto px-4">

		<!-- Section header -->
		<div class="aspv5-reveal flex items-center justify-between mb-5">
			<div>
				<p class="text-xs font-bold uppercase tracking-widest text-primary mb-0.5"><?php esc_html_e( 'Latest', 'aspv5' ); ?></p>
				<h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php esc_html_e( 'New Arrivals', 'aspv5' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
			   class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity flex items-center gap-1">
				<?php esc_html_e( 'See all', 'aspv5' ); ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-3.5 h-3.5"><polyline points="9 18 15 12 9 6"/></svg>
			</a>
		</div>

		<?php if ( 'banner' === $featured_layout ) : ?>
		<!-- Banner / poster scroll row -->
		<div class="aspv5-hscroll aspv5-reveal">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'banner' ] ); ?>
			<?php endwhile; ?>
		</div>

		<?php elseif ( 'compact' === $featured_layout ) : ?>
		<!-- Compact grid -->
		<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 aspv5-reveal">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'compact' ] ); ?>
			<?php endwhile; ?>
		</div>

		<?php else : ?>
		<!-- Standard grid -->
		<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 aspv5-reveal">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] ); ?>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>

	</div>
</section>
<?php wp_reset_postdata(); ?>
