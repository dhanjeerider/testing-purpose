<?php
// inc/widgets.php — AppStore Pro V5 Widgets

/**
 * Widget: Apps / Games by Category
 *
 * Displays posts from a chosen app-category term in one of three card styles:
 *   backdrop  – hero/poster image as card background with gradient overlay
 *   clean     – white card with app icon + title + meta (clean card style)
 *   list      – compact horizontal list rows
 */
class ASPV5_Apps_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'aspv5_apps_widget',
			__( 'AppStore V5: Apps by Category', 'aspv5' ),
			[
				'description' => __( 'Display apps or games from a category with 3 card style options.', 'aspv5' ),
				'classname'   => 'aspv5-apps-widget',
			]
		);
	}

	// Widget front-end output
	public function widget( $args, $instance ) {
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$category  = ! empty( $instance['category'] ) ? $instance['category'] : '';
		$count     = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;
		$style     = ! empty( $instance['style'] ) ? $instance['style'] : 'clean';
		$post_type = ! empty( $instance['post_type'] ) ? $instance['post_type'] : 'app';
		$orderby   = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'date';

		$query_args = [
			'post_type'      => $post_type,
			'posts_per_page' => $count,
			'post_status'    => 'publish',
			'orderby'        => $orderby,
			'order'          => 'DESC',
			'no_found_rows'  => true,
		];

		if ( $category ) {
			$query_args['tax_query'] = [
				[
					'taxonomy' => 'app-category',
					'field'    => 'slug',
					'terms'    => $category,
				],
			];
		}

		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );

		if ( $title ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( apply_filters( 'widget_title', $title ) ) . wp_kses_post( $args['after_title'] );
		}

		if ( 'backdrop' === $style ) {
			$this->render_backdrop( $query );
		} elseif ( 'list' === $style ) {
			$this->render_list( $query );
		} else {
			$this->render_clean( $query );
		}

		wp_reset_postdata();

		echo wp_kses_post( $args['after_widget'] );
	}

	// ── Backdrop / Poster card layout ─────────────────────────────────────────
	private function render_backdrop( $query ) {
		echo '<div class="aspv5-widget-backdrop grid grid-cols-2 gap-3">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id  = get_the_ID();
			$hero     = aspv5_get_app_meta( $post_id, '_app_hero_image_url' );
			$icon_url = aspv5_get_app_meta( $post_id, '_app_icon_url' );
			$bg_url   = $hero ?: $icon_url ?: ( has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'app-hero' ) : '' );
			$rating   = aspv5_get_app_meta( $post_id, '_app_rating' );
			$rating   = $rating ? number_format( (float) $rating, 1 ) : '';
			$is_mod   = aspv5_get_app_meta( $post_id, '_app_is_mod' );
			?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>"
			   class="aspv5-backdrop-card relative block rounded-2xl overflow-hidden bg-gray-800 group"
			   style="aspect-ratio:3/4;"
			   aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<?php if ( $bg_url ) : ?>
					<img src="<?php echo esc_url( $bg_url ); ?>"
					     alt="<?php echo esc_attr( get_the_title() ); ?>"
					     class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
					     loading="lazy">
				<?php endif; ?>
				<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
				<?php if ( $is_mod ) : ?>
					<span class="absolute top-2 left-2 bg-[var(--asp-primary)] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">MOD</span>
				<?php endif; ?>
				<div class="absolute bottom-0 left-0 right-0 p-3">
					<h4 class="text-white text-sm font-semibold leading-tight line-clamp-2"><?php echo esc_html( get_the_title() ); ?></h4>
					<?php if ( $rating ) : ?>
						<div class="text-yellow-400 text-xs mt-1">★ <?php echo esc_html( $rating ); ?></div>
					<?php endif; ?>
				</div>
			</a>
			<?php
		}
		echo '</div>';
	}

	// ── Clean card layout ─────────────────────────────────────────────────────
	private function render_clean( $query ) {
		echo '<div class="aspv5-widget-clean grid grid-cols-3 gap-3">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id  = get_the_ID();
			$icon_url = aspv5_get_app_meta( $post_id, '_app_icon_url' );
			$rating   = aspv5_get_app_meta( $post_id, '_app_rating' );
			$rating   = $rating ? number_format( (float) $rating, 1 ) : '';
			$is_mod   = aspv5_get_app_meta( $post_id, '_app_is_mod' );
			?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>"
			   class="aspv5-clean-card flex flex-col items-center gap-1.5 p-2 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow group"
			   aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<div class="relative w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
					<?php if ( $icon_url ) : ?>
						<img src="<?php echo esc_url( $icon_url ); ?>"
						     alt="<?php echo esc_attr( get_the_title() ); ?>"
						     class="w-full h-full object-cover"
						     loading="lazy">
					<?php elseif ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'app-icon', [ 'class' => 'w-full h-full object-cover', 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
					<?php else : ?>
						<span class="flex items-center justify-center w-full h-full text-lg font-bold text-[var(--asp-primary)]">
							<?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?>
						</span>
					<?php endif; ?>
					<?php if ( $is_mod ) : ?>
						<span class="absolute bottom-0 right-0 bg-[var(--asp-primary)] text-white text-[8px] font-bold px-1 rounded-tl-md">MOD</span>
					<?php endif; ?>
				</div>
				<h4 class="text-xs font-semibold text-gray-800 dark:text-gray-100 text-center leading-tight line-clamp-2"><?php echo esc_html( get_the_title() ); ?></h4>
				<?php if ( $rating ) : ?>
					<div class="text-[10px] text-yellow-500 font-medium">★ <?php echo esc_html( $rating ); ?></div>
				<?php endif; ?>
			</a>
			<?php
		}
		echo '</div>';
	}

	// ── List row layout ───────────────────────────────────────────────────────
	private function render_list( $query ) {
		echo '<div class="aspv5-widget-list flex flex-col gap-2">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id   = get_the_ID();
			$icon_url  = aspv5_get_app_meta( $post_id, '_app_icon_url' );
			$developer = aspv5_get_app_meta( $post_id, '_app_developer' );
			$rating    = aspv5_get_app_meta( $post_id, '_app_rating' );
			$rating    = $rating ? number_format( (float) $rating, 1 ) : '';
			$is_mod    = aspv5_get_app_meta( $post_id, '_app_is_mod' );
			$terms     = get_the_terms( $post_id, 'app-category' );
			$cat_name  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
			?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>"
			   class="aspv5-list-row flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group"
			   aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
					<?php if ( $icon_url ) : ?>
						<img src="<?php echo esc_url( $icon_url ); ?>"
						     alt="<?php echo esc_attr( get_the_title() ); ?>"
						     class="w-full h-full object-cover"
						     loading="lazy">
					<?php elseif ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'app-icon', [ 'class' => 'w-full h-full object-cover', 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
					<?php else : ?>
						<span class="flex items-center justify-center w-full h-full text-base font-bold text-[var(--asp-primary)]">
							<?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="flex-1 min-w-0">
					<h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate"><?php echo esc_html( get_the_title() ); ?></h4>
					<p class="text-xs text-gray-500 dark:text-gray-400 truncate">
						<?php echo esc_html( $developer ?: $cat_name ); ?>
					</p>
					<?php if ( $rating ) : ?>
						<div class="text-xs text-yellow-500">★ <?php echo esc_html( $rating ); ?></div>
					<?php endif; ?>
				</div>
				<?php if ( $is_mod ) : ?>
					<span class="flex-shrink-0 bg-[var(--asp-primary)] text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">MOD</span>
				<?php endif; ?>
			</a>
			<?php
		}
		echo '</div>';
	}

	// Widget settings form
	public function form( $instance ) {
		$title     = $instance['title']     ?? __( 'Top Apps', 'aspv5' );
		$category  = $instance['category']  ?? '';
		$count     = $instance['count']     ?? 6;
		$style     = $instance['style']     ?? 'clean';
		$post_type = $instance['post_type'] ?? 'app';
		$orderby   = $instance['orderby']   ?? 'date';

		// Get all app-category terms for the dropdown
		$terms = get_terms( [ 'taxonomy' => 'app-category', 'hide_empty' => false ] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'aspv5' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Post Type:', 'aspv5' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>">
				<option value="app"  <?php selected( $post_type, 'app' ); ?>><?php esc_html_e( 'Apps', 'aspv5' ); ?></option>
				<option value="game" <?php selected( $post_type, 'game' ); ?>><?php esc_html_e( 'Games', 'aspv5' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Category:', 'aspv5' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
				<option value=""><?php esc_html_e( '— All Categories —', 'aspv5' ); ?></option>
				<?php if ( ! is_wp_error( $terms ) ) : ?>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $category, $term->slug ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of items:', 'aspv5' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
			       type="number" step="1" min="1" max="24" value="<?php echo esc_attr( $count ); ?>" size="3">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_html_e( 'Card Style:', 'aspv5' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
				<option value="clean"    <?php selected( $style, 'clean' ); ?>><?php esc_html_e( 'Clean Card (icon + info)', 'aspv5' ); ?></option>
				<option value="backdrop" <?php selected( $style, 'backdrop' ); ?>><?php esc_html_e( 'Backdrop / Poster (hero image)', 'aspv5' ); ?></option>
				<option value="list"     <?php selected( $style, 'list' ); ?>><?php esc_html_e( 'List Row (compact horizontal)', 'aspv5' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order by:', 'aspv5' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<option value="date"     <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Latest', 'aspv5' ); ?></option>
				<option value="modified" <?php selected( $orderby, 'modified' ); ?>><?php esc_html_e( 'Recently updated', 'aspv5' ); ?></option>
				<option value="rand"     <?php selected( $orderby, 'rand' ); ?>><?php esc_html_e( 'Random', 'aspv5' ); ?></option>
				<option value="title"    <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Title A–Z', 'aspv5' ); ?></option>
				<option value="comment_count" <?php selected( $orderby, 'comment_count' ); ?>><?php esc_html_e( 'Most commented', 'aspv5' ); ?></option>
			</select>
		</p>
		<?php
	}

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance              = [];
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['category']  = sanitize_text_field( $new_instance['category'] );
		$instance['count']     = absint( $new_instance['count'] );
		$instance['post_type'] = in_array( $new_instance['post_type'], [ 'app', 'game' ], true ) ? $new_instance['post_type'] : 'app';
		$instance['orderby']   = in_array( $new_instance['orderby'], [ 'date', 'modified', 'rand', 'title', 'comment_count' ], true ) ? $new_instance['orderby'] : 'date';
		$valid_styles          = [ 'clean', 'backdrop', 'list' ];
		$instance['style']     = in_array( $new_instance['style'], $valid_styles, true ) ? $new_instance['style'] : 'clean';
		return $instance;
	}
}

// Register widget
function aspv5_register_widgets() {
	register_widget( 'ASPV5_Apps_Widget' );
}
add_action( 'widgets_init', 'aspv5_register_widgets' );
