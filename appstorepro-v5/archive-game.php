<?php
/**
 * archive-game.php — AppStore Pro V5
 * Game CPT archive with 4-layout switcher
 */
get_header();

$show_switcher  = get_theme_mod( 'aspv5_show_layout_switcher', '1' );
$default_layout = get_theme_mod( 'aspv5_default_layout', 'grid' );

$grid_classes = [
	'grid'    => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-4',
	'list'    => 'flex flex-col gap-3',
	'banner'  => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4',
	'compact' => 'grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3',
];
?>
<main id="main" class="pb-8 pt-4 min-h-screen" tabindex="-1">
	<div class="max-w-6xl mx-auto px-4">

		<!-- Page Header -->
		<header class="aspv5-reveal mb-6">
			<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
				<div>
					<p class="text-xs font-bold uppercase tracking-widest text-primary mb-1">
						<?php esc_html_e( 'Browse', 'aspv5' ); ?>
					</p>
					<h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
						<?php esc_html_e( 'All Games', 'aspv5' ); ?>
					</h1>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
						<?php
						$total = $wp_query->found_posts;
						printf(
							/* translators: %d: number of games */
							esc_html( _n( '%d game found', '%d games found', $total, 'aspv5' ) ),
							esc_html( number_format_i18n( $total ) )
						);
						?>
					</p>
				</div>

				<?php if ( $show_switcher ) : ?>
				<!-- Layout Switcher -->
				<div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-2xl p-1">
					<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors" data-layout="grid" title="<?php esc_attr_e( 'Grid view', 'aspv5' ); ?>" aria-label="<?php esc_attr_e( 'Switch to grid layout', 'aspv5' ); ?>">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
					</button>
					<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors" data-layout="list" title="<?php esc_attr_e( 'List view', 'aspv5' ); ?>" aria-label="<?php esc_attr_e( 'Switch to list layout', 'aspv5' ); ?>">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
					</button>
					<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors" data-layout="banner" title="<?php esc_attr_e( 'Poster view', 'aspv5' ); ?>" aria-label="<?php esc_attr_e( 'Switch to poster/banner layout', 'aspv5' ); ?>">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg>
					</button>
					<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors" data-layout="compact" title="<?php esc_attr_e( 'Compact view', 'aspv5' ); ?>" aria-label="<?php esc_attr_e( 'Switch to compact layout', 'aspv5' ); ?>">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4"><rect x="2" y="2" width="5" height="5" rx="1"/><rect x="9.5" y="2" width="5" height="5" rx="1"/><rect x="17" y="2" width="5" height="5" rx="1"/><rect x="2" y="9.5" width="5" height="5" rx="1"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/><rect x="17" y="9.5" width="5" height="5" rx="1"/><rect x="2" y="17" width="5" height="5" rx="1"/><rect x="9.5" y="17" width="5" height="5" rx="1"/><rect x="17" y="17" width="5" height="5" rx="1"/></svg>
					</button>
				</div>
				<?php endif; ?>
			</div>
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
