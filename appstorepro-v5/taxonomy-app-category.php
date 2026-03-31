<?php
/**
 * taxonomy-app-category.php — AppStore Pro V5
 * App/Game category taxonomy archive with 4-layout switcher
 */
get_header();

$term           = get_queried_object();
$show_switcher  = get_theme_mod( 'aspv5_show_layout_switcher', '1' );
$default_layout = get_theme_mod( 'aspv5_default_layout', 'grid' );
$cat_image_id   = $term ? get_term_meta( $term->term_id, '_aspv5_category_image', true ) : '';
$cat_image_url  = $cat_image_id ? wp_get_attachment_image_url( $cat_image_id, 'app-hero' ) : '';

$grid_classes = [
	'grid'    => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-4',
	'list'    => 'flex flex-col gap-3',
	'banner'  => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4',
	'compact' => 'grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3',
];
?>
<main id="main" class="pb-8 pt-4 min-h-screen" tabindex="-1">
	<div class="max-w-6xl mx-auto px-4">

		<!-- Category Header -->
		<header class="aspv5-reveal mb-6">
			<?php if ( $cat_image_url ) : ?>
				<div class="relative rounded-2xl overflow-hidden mb-5 h-32 sm:h-44">
					<img src="<?php echo esc_url( $cat_image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" class="absolute inset-0 w-full h-full object-cover" loading="eager">
					<div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
					<div class="absolute bottom-4 left-5">
						<h1 class="text-white text-2xl sm:text-3xl font-bold"><?php echo esc_html( $term->name ); ?></h1>
					</div>
				</div>
			<?php else : ?>
				<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
					<div>
						<p class="text-xs font-bold uppercase tracking-widest text-primary mb-1">
							<?php echo esc_html( aspv5_get_category_icon_svg( $term ? $term->slug : '' ) . ' ' ); ?><?php esc_html_e( 'Category', 'aspv5' ); ?>
						</p>
						<h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
							<?php echo esc_html( $term ? $term->name : '' ); ?>
						</h1>
						<?php if ( $term && $term->description ) : ?>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo esc_html( $term->description ); ?></p>
						<?php endif; ?>
						<p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
							<?php
							$total = $wp_query->found_posts;
							printf(
								/* translators: %d: number of items */
								esc_html( _n( '%d item', '%d items', $total, 'aspv5' ) ),
								esc_html( number_format_i18n( $total ) )
							);
							?>
						</p>
					</div>
					<?php if ( $show_switcher ) : ?>
					<?php get_template_part( 'template-parts/global/layout-switcher' ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $cat_image_url && $show_switcher ) : ?>
				<div class="flex justify-end mt-3">
					<?php get_template_part( 'template-parts/global/layout-switcher' ); ?>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="<?php echo esc_attr( $grid_classes[ $default_layout ] ); ?>"
			     data-layout-container
			     data-default-layout="<?php echo esc_attr( $default_layout ); ?>"
			     id="aspv5-posts-container"
			     data-grid-classes="<?php echo esc_attr( wp_json_encode( $grid_classes ) ); ?>">

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => $default_layout ] ); ?>
				<?php endwhile; ?>

			</div>

			<?php the_posts_pagination( [
				'class'     => 'aspv5-pagination flex flex-wrap justify-center gap-1.5 mt-10',
				'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-4 h-4"><polyline points="15 18 9 12 15 6"/></svg>',
				'next_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-4 h-4"><polyline points="9 18 15 12 9 6"/></svg>',
			] ); ?>

		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>

	</div>
</main>

<?php
// Layout switcher inline script — same as archive pages
?>
<script>
(function() {
	var container  = document.getElementById('aspv5-posts-container');
	var layoutBtns = document.querySelectorAll('.aspv5-layout-btn');
	var gridClasses = JSON.parse(container ? container.dataset.gridClasses || '{}' : '{}');
	var LAYOUT_KEY  = 'aspv5_layout';

	function applyLayout(layout) {
		if (!container || !gridClasses[layout]) return;
		Object.values(gridClasses).forEach(function(cls) {
			cls.split(' ').forEach(function(c) { container.classList.remove(c); });
		});
		gridClasses[layout].split(' ').forEach(function(c) { if(c) container.classList.add(c); });
		container.setAttribute('data-layout', layout);
		localStorage.setItem(LAYOUT_KEY, layout);
		layoutBtns.forEach(function(btn) {
			btn.classList.toggle('active', btn.dataset.layout === layout);
		});
	}

	layoutBtns.forEach(function(btn) {
		btn.addEventListener('click', function() { applyLayout(btn.dataset.layout); });
	});

	var saved = localStorage.getItem(LAYOUT_KEY) || (container ? container.dataset.defaultLayout : 'grid');
	applyLayout(saved);
})();
</script>

<?php get_footer(); ?>
