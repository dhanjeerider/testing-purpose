<?php
/**
 * template-parts/content/content-app.php — AppStore Pro V5
 *
 * Supports 4 layout modes via $args['layout']:
 *   grid    — 2–3 col cards (default)
 *   list    — horizontal rows
 *   banner  — backdrop/poster cards (hero image background)
 *   compact — dense 3–4 col mini-cards
 *
 * Rendered via data-layout attribute on the parent container; individual cards
 * only need to output the correct markup for their layout slot.
 */

$layout    = $args['layout'] ?? 'grid';
$post_id   = get_the_ID();
$icon_url  = aspv5_get_app_meta( $post_id, '_app_icon_url' );
$rating    = aspv5_get_app_meta( $post_id, '_app_rating' );
$rating    = $rating ? number_format( (float) $rating, 1, '.', '' ) : '';
$size      = aspv5_get_app_meta( $post_id, '_app_size' );
$version   = aspv5_get_app_meta( $post_id, '_app_version' );
$is_mod    = aspv5_get_app_meta( $post_id, '_app_is_mod' );
$developer = aspv5_get_app_meta( $post_id, '_app_developer' );
$downloads = aspv5_format_downloads( aspv5_get_app_meta( $post_id, '_app_downloads' ) );
$terms     = get_the_terms( $post_id, 'app-category' );
$cat_name  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cat_link  = ( $terms && ! is_wp_error( $terms ) ) ? get_term_link( $terms[0] ) : '';

/* ──────────────────────────────────────────────────────────────────────────
   BANNER / BACKDROP layout — hero image as full card background
   ────────────────────────────────────────────────────────────────────────── */
if ( 'banner' === $layout ) :
	$hero_url = aspv5_get_app_meta( $post_id, '_app_hero_image_url' );
	if ( ! $hero_url ) {
		$hero_url = $icon_url;
	}
?>
<article class="aspv5-reveal aspv5-card-lift" id="post-<?php the_ID(); ?>" <?php post_class( 'aspv5-app-banner' ); ?>>
	<a href="<?php echo esc_url( get_the_permalink() ); ?>"
	   class="relative flex flex-col rounded-2xl overflow-hidden bg-gray-800 cursor-pointer group"
	   style="aspect-ratio:3/4;"
	   aria-label="<?php echo esc_attr( get_the_title() ); ?>">

		<?php if ( $hero_url ) : ?>
			<img src="<?php echo esc_url( $hero_url ); ?>"
			     alt="<?php echo esc_attr( get_the_title() ); ?>"
			     class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
			     loading="lazy">
		<?php else : ?>
			<div class="absolute inset-0 bg-gradient-to-br from-primary to-orange-500 opacity-70"></div>
		<?php endif; ?>

		<!-- Dark gradient overlay -->
		<div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

		<!-- Badges top-left -->
		<div class="absolute top-2.5 left-2.5 flex gap-1">
			<?php if ( is_sticky() ) : ?>
				<span class="badge-hot px-2 py-0.5 rounded-full text-[9px] font-bold text-white bg-red-500"><?php esc_html_e( 'Hot', 'aspv5' ); ?></span>
			<?php endif; ?>
			<?php if ( $is_mod ) : ?>
				<span class="badge-mod px-2 py-0.5 rounded-full text-[9px] font-bold text-white"><?php esc_html_e( 'MOD', 'aspv5' ); ?></span>
			<?php endif; ?>
		</div>

		<!-- Bottom info overlay -->
		<div class="absolute bottom-0 left-0 right-0 p-3">
			<?php if ( $icon_url ) : ?>
				<img src="<?php echo esc_url( $icon_url ); ?>"
				     alt=""
				     class="w-10 h-10 rounded-xl mb-2 shadow-md"
				     loading="lazy">
			<?php endif; ?>
			<h3 class="text-white text-sm font-bold leading-tight line-clamp-2 mb-1">
				<?php echo esc_html( get_the_title() ); ?>
			</h3>
			<?php if ( $developer ) : ?>
				<p class="text-white/70 text-[11px] truncate mb-1.5"><?php echo esc_html( $developer ); ?></p>
			<?php elseif ( $cat_name ) : ?>
				<p class="text-white/70 text-[11px] truncate mb-1.5"><?php echo esc_html( $cat_name ); ?></p>
			<?php endif; ?>
			<div class="flex items-center gap-2 flex-wrap">
				<?php if ( $rating ) : ?>
					<span class="text-yellow-400 text-xs font-semibold">★ <?php echo esc_html( $rating ); ?></span>
				<?php endif; ?>
				<?php if ( $size ) : ?>
					<span class="text-white/60 text-[10px]"><?php echo esc_html( $size ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</a>
</article>

<?php
/* ──────────────────────────────────────────────────────────────────────────
   LIST layout — horizontal row
   ────────────────────────────────────────────────────────────────────────── */
elseif ( 'list' === $layout ) :
?>
<article class="aspv5-reveal" id="post-<?php the_ID(); ?>" <?php post_class( 'aspv5-app-row' ); ?>>
	<a href="<?php echo esc_url( get_the_permalink() ); ?>"
	   class="flex items-center gap-3 p-3 rounded-2xl bg-white dark:bg-gray-800 hover:bg-primary/5 dark:hover:bg-primary/10 border border-gray-100 dark:border-gray-700/60 hover:border-primary/20 dark:hover:border-primary/30 transition-all group"
	   aria-label="<?php echo esc_attr( get_the_title() ); ?>">

		<!-- App icon -->
		<div class="relative w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0 shadow-sm">
			<?php if ( $icon_url ) : ?>
				<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-full h-full object-cover" loading="lazy">
			<?php elseif ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'app-icon', [ 'class' => 'w-full h-full object-cover', 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
			<?php else : ?>
				<div class="app-icon-placeholder"><span><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span></div>
			<?php endif; ?>
		</div>

		<!-- Info -->
		<div class="flex-1 min-w-0">
			<h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate group-hover:text-primary transition-colors">
				<?php echo esc_html( get_the_title() ); ?>
			</h3>
			<?php if ( $developer ) : ?>
				<p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5"><?php echo esc_html( $developer ); ?></p>
			<?php elseif ( $cat_name ) : ?>
				<p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5"><?php echo esc_html( $cat_name ); ?></p>
			<?php endif; ?>
			<div class="flex items-center gap-2 mt-1 flex-wrap">
				<?php if ( is_sticky() ) : ?>
					<span class="badge-hot px-1.5 py-0.5 rounded-full text-[9px] font-bold text-white"><?php esc_html_e( 'Hot', 'aspv5' ); ?></span>
				<?php endif; ?>
				<?php if ( $is_mod ) : ?>
					<span class="badge-mod px-1.5 py-0.5 rounded-full text-[9px] font-bold text-white"><?php esc_html_e( 'MOD', 'aspv5' ); ?></span>
				<?php endif; ?>
				<?php if ( $rating ) : ?>
					<span class="text-xs text-yellow-500 font-medium">★ <?php echo esc_html( $rating ); ?></span>
				<?php endif; ?>
				<?php if ( $size ) : ?>
					<span class="text-[10px] text-gray-400"><?php echo esc_html( $size ); ?></span>
				<?php endif; ?>
				<?php if ( $downloads ) : ?>
					<span class="text-[10px] text-gray-400"><?php echo esc_html( $downloads ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<!-- Download arrow -->
		<div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700 group-hover:bg-primary transition-colors">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
			     class="w-3.5 h-3.5 text-gray-400 group-hover:text-white transition-colors">
				<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
			</svg>
		</div>

	</a>
</article>

<?php
/* ──────────────────────────────────────────────────────────────────────────
   COMPACT layout — small, dense grid cards
   ────────────────────────────────────────────────────────────────────────── */
elseif ( 'compact' === $layout ) :
?>
<article class="aspv5-reveal aspv5-card-lift" id="post-<?php the_ID(); ?>" <?php post_class( 'aspv5-app-compact' ); ?>>
	<a href="<?php echo esc_url( get_the_permalink() ); ?>"
	   class="flex flex-col items-center gap-1.5 p-2.5 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 hover:border-primary/40 dark:hover:border-primary/40 transition-all group text-center"
	   aria-label="<?php echo esc_attr( get_the_title() ); ?>">

		<div class="relative w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 shadow-sm">
			<?php if ( $icon_url ) : ?>
				<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-full h-full object-cover" loading="lazy">
			<?php elseif ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'app-icon', [ 'class' => 'w-full h-full object-cover', 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
			<?php else : ?>
				<div class="app-icon-placeholder"><span><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span></div>
			<?php endif; ?>
			<?php if ( $is_mod ) : ?>
				<span class="absolute bottom-0 right-0 badge-mod px-1 text-[8px] font-bold text-white rounded-tl-md rounded-br-2xl">M</span>
			<?php endif; ?>
		</div>
		<h3 class="text-[11px] font-semibold text-gray-800 dark:text-gray-100 line-clamp-2 leading-tight w-full">
			<?php echo esc_html( get_the_title() ); ?>
		</h3>
		<?php if ( $rating ) : ?>
			<span class="text-[10px] text-yellow-500 font-medium -mt-0.5">★ <?php echo esc_html( $rating ); ?></span>
		<?php elseif ( $cat_name ) : ?>
			<span class="text-[10px] text-gray-400 truncate w-full"><?php echo esc_html( $cat_name ); ?></span>
		<?php endif; ?>

	</a>
</article>

<?php
/* ──────────────────────────────────────────────────────────────────────────
   GRID layout (default) — standard card with icon + info
   ────────────────────────────────────────────────────────────────────────── */
else :
?>
<article class="aspv5-reveal aspv5-card-lift" id="post-<?php the_ID(); ?>" <?php post_class( 'aspv5-app-card' ); ?>>
	<a href="<?php echo esc_url( get_the_permalink() ); ?>"
	   class="flex flex-col gap-2.5 p-3.5 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 hover:border-primary/30 dark:hover:border-primary/30 hover:shadow-lg transition-all group h-full"
	   aria-label="<?php echo esc_attr( get_the_title() ); ?>">

		<!-- Icon row with badges -->
		<div class="relative flex items-start gap-3">
			<div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0 shadow-sm">
				<?php if ( $icon_url ) : ?>
					<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-full h-full object-cover" loading="lazy">
				<?php elseif ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'app-icon', [ 'class' => 'w-full h-full object-cover', 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
				<?php else : ?>
					<div class="app-icon-placeholder"><span><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span></div>
				<?php endif; ?>
			</div>
			<div class="flex-1 min-w-0 pt-0.5">
				<div class="flex gap-1 mb-1 flex-wrap">
					<?php if ( is_sticky() ) : ?>
						<span class="badge-hot text-[9px] font-bold text-white px-1.5 py-0.5 rounded-full"><?php esc_html_e( 'Hot', 'aspv5' ); ?></span>
					<?php endif; ?>
					<?php if ( $is_mod ) : ?>
						<span class="badge-mod text-[9px] font-bold text-white px-1.5 py-0.5 rounded-full"><?php esc_html_e( 'MOD', 'aspv5' ); ?></span>
					<?php endif; ?>
				</div>
				<h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
					<?php echo esc_html( get_the_title() ); ?>
				</h3>
				<!-- Category / Developer -->
				<?php if ( $cat_name ) : ?>
					<div class="text-[11px] text-gray-400 dark:text-gray-500 truncate mt-0.5"><?php echo esc_html( $cat_name ); ?></div>
				<?php elseif ( $developer ) : ?>
					<div class="text-[11px] text-gray-400 dark:text-gray-500 truncate mt-0.5"><?php echo esc_html( $developer ); ?></div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Meta pills -->
		<div class="flex items-center justify-between mt-auto pt-1 border-t border-gray-50 dark:border-gray-700/40">
			<div class="flex items-center gap-2 flex-wrap">
				<?php if ( $rating ) : ?>
					<span class="inline-flex items-center gap-0.5 text-xs font-semibold text-yellow-500">
						<span>★</span><span><?php echo esc_html( $rating ); ?></span>
					</span>
				<?php endif; ?>
				<?php if ( $size ) : ?>
					<span class="text-[10px] text-gray-400 dark:text-gray-500"><?php echo esc_html( $size ); ?></span>
				<?php endif; ?>
				<?php if ( $downloads ) : ?>
					<span class="text-[10px] text-gray-400 dark:text-gray-500"><?php echo esc_html( $downloads ); ?></span>
				<?php endif; ?>
			</div>
			<span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-xl bg-primary/10 group-hover:bg-primary transition-colors">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
				     class="w-3.5 h-3.5 text-primary group-hover:text-white transition-colors">
					<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
				</svg>
			</span>
		</div>

	</a>
</article>

<?php endif; ?>
