<?php
// inc/shortcodes.php — Browse, Category List, and Home Page Shortcodes

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════════
// BROWSE PAGE SHORTCODE
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Browse Apps & Games Shortcode
 * 
 * Displays a filterable grid of apps and games with advanced search.
 * 
 * Usage: [aspv5_browse type="both" per_page="12"]
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML
 */
function aspv5_shortcode_browse( $atts ) {
	$atts = shortcode_atts( [
		'type'     => 'both', // 'both', 'app', 'game'
		'per_page' => 12,
		'orderby'  => 'date',
		'order'    => 'DESC',
	], $atts, 'aspv5_browse' );

	ob_start();
	?>
	<div class="aspv5-browse-wrapper">
		<!-- Hero Section -->
		<section class="browse-hero py-12 bg-gradient-to-b from-blue-50 to-white mb-10">
			<div class="max-w-6xl mx-auto px-4">
				<div class="text-center mb-8">
					<p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-orange mb-4">
						<span class="w-2 h-2 rounded-full bg-orange"></span>
						Download Free Apps & Games
					</p>
					<h1 class="text-4xl font-bold text-gray-900 mb-4">
						Explore Your Favorite <span class="text-orange">MOD APK</span> Games & Apps
					</h1>
					<p class="text-gray-600 max-w-2xl mx-auto">
						Search, filter, and download thousands of applications and games for Android.
					</p>
				</div>

				<!-- Search Form -->
				<form id="aspv5-browse-form" class="bg-white rounded-2xl shadow-lg p-8">
					<!-- Search Bar -->
					<div class="mb-6">
						<div class="relative">
							<input 
								type="text" 
								id="browse-search" 
								placeholder="Search for apps, games..." 
								class="w-full rounded-full border border-gray-200 px-6 py-4 text-gray-900 outline-none focus:ring-2 focus:ring-orange focus:border-transparent"
							>
							<button type="submit" class="absolute inset-y-0 right-2 flex items-center pr-4 text-orange hover:text-orange-hover">
								<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M765-144 526-383q-30 22-65.79 34.5-35.79 12.5-76.18 12.5Q284-336 214-406t-70-170q0-100 70-170t170-70q100 0 170 70t70 170.03q0 40.39-12.5 76.18Q599-464 577-434l239 239-51 51ZM384-408q70 0 119-49t49-119q0-70-49-119t-119-49q-70 0-119 49t-49 119q0 70 49 119t119 49Z"></path></svg>
							</button>
						</div>
					</div>

					<!-- Filters Grid -->
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<!-- Category Filter -->
						<div>
							<label class="block text-sm font-semibold text-gray-900 mb-2">Category</label>
							<select id="browse-category" class="w-full rounded-lg border border-gray-200 px-4 py-2">
								<option value="">All Categories</option>
								<?php
								$categories = get_terms( [
									'taxonomy'   => 'app-category',
									'hide_empty' => false,
									'number'     => 100,
								] );
								foreach ( $categories as $cat ) {
									echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>';
								}
								?>
							</select>
						</div>

						<!-- App Type Filter -->
						<div>
							<label class="block text-sm font-semibold text-gray-900 mb-2">App Type</label>
							<select id="browse-type" class="w-full rounded-lg border border-gray-200 px-4 py-2">
								<option value="">All Types</option>
								<option value="mod">MOD APK</option>
								<option value="premium">Premium</option>
								<option value="free">Free</option>
								<option value="paid">Paid</option>
								<option value="original">Original</option>
							</select>
						</div>

						<!-- Sort Filter -->
						<div>
							<label class="block text-sm font-semibold text-gray-900 mb-2">Sort By</label>
							<select id="browse-sort" class="w-full rounded-lg border border-gray-200 px-4 py-2">
								<option value="date-desc">Newest First</option>
								<option value="date-asc">Oldest First</option>
								<option value="title-asc">A to Z</option>
								<option value="rating-desc">Highest Rated</option>
							</select>
						</div>
					</div>
				</form>
			</div>
		</section>

		<!-- Results Grid -->
		<section class="max-w-6xl mx-auto px-4 pb-12">
			<div id="browse-results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php echo aspv5_render_browse_results( $atts ); ?>
			</div>

			<!-- Pagination -->
			<div id="browse-pagination" class="mt-8 text-center"></div>
		</section>
	</div>

	<style>
		.browse-hero {
			background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
		}
		
		.aspv5-browse-wrapper {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
		}

		.game-card {
			background: white;
			border-radius: 16px;
			overflow: hidden;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
		}

		.game-card:hover {
			box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
			transform: translateY(-4px);
		}

		.game-thumb {
			aspect-ratio: 1;
			overflow: hidden;
			background: #f3f4f6;
		}

		.game-thumb img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			transition: transform 0.3s ease;
		}

		.game-card:hover .game-thumb img {
			transform: scale(1.05);
		}

		.game-content {
			padding: 16px;
		}

		.game-title {
			font-weight: 600;
			color: #1f2937;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}

		.game-meta {
			font-size: 12px;
			color: #6b7280;
			margin-top: 8px;
		}

		.game-rating {
			color: #f59e0b;
			font-weight: 600;
		}

		.game-badge {
			display: inline-block;
			background: #ff6b35;
			color: white;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 10px;
			font-weight: 700;
			margin-right: 8px;
		}

		.game-download-btn {
			display: block;
			width: 100%;
			padding: 12px;
			margin-top: 12px;
			background: linear-gradient(135deg, #ff6b35 0%, #f27c2f 100%);
			color: white;
			border: none;
			border-radius: 10px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			text-align: center;
			text-decoration: none;
		}

		.game-download-btn:hover {
			background: linear-gradient(135deg, #f27c2f 0%, #e66f28 100%);
			transform: scale(1.02);
		}
	</style>

	<script>
		(function ($) {
			'use strict';

			$('#aspv5-browse-form').on('submit', function (e) {
				e.preventDefault();
				aspv5_load_browse_results();
			});

			$('#browse-category, #browse-type, #browse-sort').on('change', function () {
				aspv5_load_browse_results();
			});

			$('#browse-search').on('keyup', function () {
				clearTimeout(this.searchTimeout);
				this.searchTimeout = setTimeout(function () {
					aspv5_load_browse_results();
				}, 500);
			});

			function aspv5_load_browse_results() {
				var data = {
					action: 'aspv5_browse_results',
					search: $('#browse-search').val(),
					category: $('#browse-category').val(),
					type: $('#browse-type').val(),
					sort: $('#browse-sort').val(),
					paged: 1
				};

				$.post(aspv5.ajaxUrl, data, function (response) {
					$('#browse-results').html(response);
				});
			}

			// Load initial results
			aspv5_load_browse_results();
		})(jQuery);
	</script>

	<?php
	return ob_get_clean();
}
add_shortcode( 'aspv5_browse', 'aspv5_shortcode_browse' );

// ── Render browse results via AJAX ────────────────────────────────────────────
function aspv5_render_browse_results( $args = [] ) {
	$defaults = [
		'search'   => isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '',
		'category' => isset( $_POST['category'] ) ? absint( wp_unslash( $_POST['category'] ) ) : 0,
		'type'     => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
		'sort'     => isset( $_POST['sort'] ) ? sanitize_text_field( wp_unslash( $_POST['sort'] ) ) : 'date-desc',
		'per_page' => 12,
		'paged'    => max( 1, isset( $_POST['paged'] ) ? absint( wp_unslash( $_POST['paged'] ) ) : 1 ),
	];
	$args = wp_parse_args( $args, $defaults );

	// Build query args
	$query_args = [
		'post_type'      => [ 'app', 'game' ],
		'posts_per_page' => $args['per_page'],
		'paged'          => $args['paged'],
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	// Search
	if ( $args['search'] ) {
		$query_args['s'] = $args['search'];
	}

	// Category filter
	if ( $args['category'] ) {
		$query_args['tax_query'] = [
			[
				'taxonomy' => 'app-category',
				'field'    => 'term_id',
				'terms'    => $args['category'],
			],
		];
	}

	// Type filter (MOD info meta)
	if ( $args['type'] ) {
		$query_args['meta_query'] = [
			[
				'key'   => '_app_mod_info',
				'value' => '',
				'compare' => $args['type'] === 'mod' ? '!=' : '=',
			],
		];
	}

	// Sorting
	if ( 'title-asc' === $args['sort'] ) {
		$query_args['orderby'] = 'title';
		$query_args['order']   = 'ASC';
	} elseif ( 'rating-desc' === $args['sort'] ) {
		$query_args['orderby'] = 'meta_value_num';
		$query_args['meta_key'] = '_app_rating';
		$query_args['order']   = 'DESC';
	}

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			aspv5_render_app_card();
		}
		wp_reset_postdata();
	} else {
		echo '<div class="col-span-full text-center py-12"><p class="text-gray-500">No apps found. Try different filters.</p></div>';
	}
}

// ── Render single app card ────────────────────────────────────────────────────
function aspv5_render_app_card() {
	$icon     = get_post_meta( get_the_ID(), '_app_icon_url', true );
	$rating   = get_post_meta( get_the_ID(), '_app_rating', true );
	$mod_info = get_post_meta( get_the_ID(), '_app_mod_info', true );
	$version  = get_post_meta( get_the_ID(), '_app_version', true );
	$size     = get_post_meta( get_the_ID(), '_app_size', true );
	$categories = wp_get_post_terms( get_the_ID(), 'app-category' );

	// Get featured image fallback
	if ( ! $icon && has_post_thumbnail() ) {
		$icon = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
	}

	?>
	<article class="game-card">
		<?php if ( $mod_info ) : ?>
			<div style="position: absolute; top: 12px; left: 12px; z-index: 1;">
				<span class="game-badge">MOD</span>
			</div>
		<?php endif; ?>

		<a href="<?php the_permalink(); ?>" class="game-thumb">
			<?php if ( $icon ) : ?>
				<img src="<?php echo esc_url( $icon ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
			<?php else : ?>
				<div style="display: flex; align-items: center; justify-content: center; background: #e5e7eb; color: #6b7280; font-size: 48px; font-weight: bold;">
					<?php echo strtoupper( substr( get_the_title(), 0, 1 ) ); ?>
				</div>
			<?php endif; ?>
		</a>

		<div class="game-content">
			<a href="<?php the_permalink(); ?>" class="game-title"><?php the_title(); ?></a>

			<div class="game-meta">
				<?php if ( $version || $size ) : ?>
					<div>
						<?php if ( $version ) echo esc_html( $version ); ?>
						<?php if ( $version && $size ) echo ' • '; ?>
						<?php if ( $size ) echo esc_html( $size ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div style="display: flex; align-items: center; gap: 8px; margin-top: 12px;">
				<?php if ( $rating ) : ?>
					<span class="game-rating">★ <?php echo esc_html( $rating ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $categories ) ) : ?>
					<span style="font-size: 12px; color: #6b7280;">
						<?php echo esc_html( $categories[0]->name ); ?>
					</span>
				<?php endif; ?>
			</div>

			<a href="<?php the_permalink(); ?>" class="game-download-btn">
				<svg class="w-5 h-5" style="display: inline; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 -960 960 960"><path d="M480-336 288-528l51-51 105 105v-342h72v342l105-105 51 51-192 192ZM263.72-192Q234-192 213-213.15T192-264v-72h72v72h432v-72h72v72q0 29.7-21.16 50.85Q725.68-192 695.96-192H263.72Z"></path></svg>
				Download
			</a>
		</div>
	</article>
	<?php
}

// ── AJAX handler for browse results ───────────────────────────────────────────
function aspv5_ajax_browse_results() {
	check_ajax_referer( 'aspv5_nonce', 'security' );
	aspv5_render_browse_results();
	wp_die();
}
add_action( 'wp_ajax_aspv5_browse_results', 'aspv5_ajax_browse_results' );
add_action( 'wp_ajax_nopriv_aspv5_browse_results', 'aspv5_ajax_browse_results' );

// ═══════════════════════════════════════════════════════════════════════════════
// CATEGORIES LIST SHORTCODE
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Categories List Shortcode
 * 
 * Displays all app/game categories in a grid with images.
 * 
 * Usage: [aspv5_categories columns="3" show_count="yes"]
 */
function aspv5_shortcode_categories( $atts ) {
	$atts = shortcode_atts( [
		'columns'   => 3,
		'show_count' => 'yes',
	], $atts, 'aspv5_categories' );

	ob_start();

	$categories = get_terms( [
		'taxonomy'   => 'app-category',
		'hide_empty' => false,
		'number'     => 100,
	] );

	if ( empty( $categories ) || is_wp_error( $categories ) ) {
		return '<p>No categories found.</p>';
	}

	?>
	<div class="aspv5-categories-wrapper">
		<div class="categories-grid" style="display: grid; grid-template-columns: repeat(<?php echo (int) $atts['columns']; ?>, 1fr); gap: 24px; margin: 40px 0;">
			<?php foreach ( $categories as $category ) : ?>
				<?php
				$image_url = aspv5_get_category_image( $category->term_id, 'medium' );
				$link      = get_term_link( $category );
				$count     = $category->count;
				?>
				<a href="<?php echo esc_url( $link ); ?>" class="category-card">
					<div class="category-image-wrap">
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" loading="lazy">
						<?php else : ?>
							<div class="category-placeholder">
								<svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M480-336 288-528l51-51 105 105v-342h72v342l105-105 51 51-192 192ZM263.72-192Q234-192 213-213.15T192-264v-72h72v72h432v-72h72v72q0 29.7-21.16 50.85Q725.68-192 695.96-192H263.72Z"></path></svg>
							</div>
						<?php endif; ?>
					</div>
					<div class="category-info">
						<h3><?php echo esc_html( $category->name ); ?></h3>
						<?php if ( 'yes' === $atts['show_count'] ) : ?>
							<p><?php printf( _n( '%d app', '%d apps', $count, 'aspv5' ), $count ); ?></p>
						<?php endif; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<style>
		.category-card {
			display: block;
			text-decoration: none;
			border-radius: 16px;
			overflow: hidden;
			background: white;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
			height: 100%;
		}

		.category-card:hover {
			box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
			transform: translateY(-4px);
		}

		.category-image-wrap {
			position: relative;
			width: 100%;
			padding-bottom: 100%;
			overflow: hidden;
			background: #f3f4f6;
		}

		.category-image-wrap img {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
			transition: transform 0.3s ease;
		}

		.category-card:hover .category-image-wrap img {
			transform: scale(1.1);
		}

		.category-placeholder {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
			color: #9ca3af;
		}

		.category-info {
			padding: 20px;
			text-align: center;
		}

		.category-info h3 {
			margin: 0 0 8px 0;
			font-size: 18px;
			font-weight: 600;
			color: #1f2937;
		}

		.category-info p {
			margin: 0;
			font-size: 14px;
			color: #6b7280;
		}

		@media (max-width: 768px) {
			.categories-grid {
				grid-template-columns: repeat(2, 1fr);
				gap: 16px;
			}
		}

		@media (max-width: 480px) {
			.categories-grid {
				grid-template-columns: 1fr;
			}
		}
	</style>

	<?php
	return ob_get_clean();
}
add_shortcode( 'aspv5_categories', 'aspv5_shortcode_categories' );

// ═══════════════════════════════════════════════════════════════════════════════
// HOME PAGE SHORTCODE
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Home Page Hero Shortcode
 * 
 * Displays featured apps and game collections.
 * 
 * Usage: [aspv5_home_hero]
 */
function aspv5_shortcode_home_hero( $atts ) {
	ob_start();

	// Get featured apps
	$featured_apps = new WP_Query( [
		'post_type'      => [ 'app', 'game' ],
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );

	?>
	<section class="aspv5-home-hero py-12 bg-gradient-to-b from-blue-50 to-transparent">
		<div class="max-w-6xl mx-auto px-4">
			<!-- Hero Title -->
			<div class="text-center mb-12">
				<p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-orange mb-4">
					<span class="w-2 h-2 rounded-full bg-orange"></span>
					Discover Amazing Apps
				</p>
				<h1 class="text-5xl font-bold text-gray-900 mb-4">
					Download Premium <span class="text-orange">MOD APK</span> Apps
				</h1>
				<p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
					Get free access to premium apps and games with all features unlocked.
				</p>
				<a href="#browse" class="inline-block px-8 py-4 bg-orange text-white rounded-full font-semibold hover:bg-orange-hover transition-colors">
					Explore Now
				</a>
			</div>

			<!-- Featured Apps Carousel -->
			<?php if ( $featured_apps->have_posts() ) : ?>
				<div class="featured-carousel mb-16">
					<h2 class="text-2xl font-bold mb-6">Featured Applications</h2>
					<div class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-4">
						<?php while ( $featured_apps->have_posts() ) : ?>
							<?php $featured_apps->the_post(); ?>
							<?php
							$icon = get_post_meta( get_the_ID(), '_app_icon_url', true );
							if ( ! $icon && has_post_thumbnail() ) {
								$icon = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
							}
							$category = wp_get_post_terms( get_the_ID(), 'app-category' );
							?>
							<a href="<?php the_permalink(); ?>" class="snap-start shrink-0 w-[85%] sm:w-[60%] md:w-[45%] lg:w-[30%] rounded-2xl overflow-hidden no-underline text-dark bg-white block relative group">
								<div class="relative w-full aspect-video overflow-hidden">
									<?php if ( $icon ) : ?>
										<img src="<?php echo esc_url( $icon ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
									<?php else : ?>
										<div style="width: 100%; height: 100%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; color: #9ca3af;">
											<?php echo strtoupper( substr( get_the_title(), 0, 1 ) ); ?>
										</div>
									<?php endif; ?>
								</div>
								<div class="p-4 flex items-center gap-3 bg-white/50 backdrop-blur-sm absolute bottom-0 left-0 right-0">
									<?php if ( $icon ) : ?>
										<img src="<?php echo esc_url( $icon ); ?>" class="w-10 h-10 rounded-xl shadow-sm" alt="<?php the_title_attribute(); ?>">
									<?php endif; ?>
									<div>
										<h3 class="font-semibold text-dark text-md m-0 leading-tight"><?php the_title(); ?></h3>
										<?php if ( ! empty( $category ) ) : ?>
											<p class="text-xs font-semibold text-orange mt-0.5"><?php echo esc_html( $category[0]->name ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
						<?php endwhile; ?>
					</div>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>

	<style>
		.featured-carousel {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
		}

		.featured-carousel h2 {
			margin: 0;
		}

		.featured-carousel .snap-x {
			scroll-behavior: smooth;
		}

		/* Hide scrollbar for Chrome, Safari and Opera */
		.featured-carousel .overflow-x-auto::-webkit-scrollbar {
			display: none;
		}

		/* Hide scrollbar for IE, Edge and Firefox */
		.featured-carousel .overflow-x-auto {
			-ms-overflow-style: none;
			scrollbar-width: none;
		}
	</style>

	<?php
	return ob_get_clean();
}
add_shortcode( 'aspv5_home_hero', 'aspv5_shortcode_home_hero' );

/**
 * Home Page Collections Shortcode
 * 
 * Displays app collections/categories in grid.
 * 
 * Usage: [aspv5_home_collections]
 */
function aspv5_shortcode_home_collections( $atts ) {
	ob_start();

	$collections = [
		[
			'title' => 'Must Have Collection',
			'icon'  => 'settings',
		],
		[
			'title' => 'Gaming Paradise',
			'icon'  => 'gamepad',
		],
		[
			'title' => 'Creative Tools',
			'icon'  => 'palette',
		],
	];

	?>
	<section class="py-12">
		<div class="max-w-6xl mx-auto px-4">
			<h2 class="text-3xl font-bold mb-8">Collections & Curated Lists</h2>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<?php
				$categories = get_terms( [
					'taxonomy'   => 'app-category',
					'hide_empty' => false,
					'number'     => 3,
				] );

				foreach ( $categories as $category ) :
					$image_url = aspv5_get_category_image( $category->term_id, 'large' );
					$link      = get_term_link( $category );
					?>
					<a href="<?php echo esc_url( $link ); ?>" class="collection-card relative rounded-2xl overflow-hidden aspect-square group cursor-pointer no-underline text-dark">
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500">
						<?php else : ?>
							<div class="absolute inset-0 w-full h-full bg-gradient-to-br from-orange-100 to-blue-100"></div>
						<?php endif; ?>

						<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent transition-opacity duration-300 group-hover:opacity-50"></div>

						<div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 z-[2]">
							<h2 class="text-white font-bold text-lg sm:text-xl leading-snug drop-shadow">
								<?php echo esc_html( $category->name ); ?>
							</h2>
							<p class="text-white/80 text-sm mt-2 drop-shadow">
								<?php printf( _n( '%d app', '%d apps', $category->count, 'aspv5' ), $category->count ); ?>
							</p>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<style>
		.collection-card {
			display: block;
			text-decoration: none;
		}

		.collection-card:hover {
			text-decoration: none;
		}
	</style>

	<?php
	return ob_get_clean();
}
add_shortcode( 'aspv5_home_collections', 'aspv5_shortcode_home_collections' );
