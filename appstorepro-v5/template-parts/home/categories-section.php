<?php
// template-parts/home/categories-section.php — AppStore Pro V5
$categories = get_terms( [
	'taxonomy'   => 'app-category',
	'hide_empty' => true,
	'number'     => 12,
	'orderby'    => 'count',
	'order'      => 'DESC',
] );

if ( is_wp_error( $categories ) || empty( $categories ) ) return;
?>
<section class="py-8 bg-white dark:bg-gray-900">
	<div class="max-w-6xl mx-auto px-4">

		<div class="aspv5-reveal flex items-center justify-between mb-5">
			<div>
				<p class="text-xs font-bold uppercase tracking-widest text-primary mb-0.5"><?php esc_html_e( 'Explore', 'aspv5' ); ?></p>
				<h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php esc_html_e( 'Categories', 'aspv5' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( home_url( '/app-category/' ) ); ?>"
			   class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity flex items-center gap-1">
				<?php esc_html_e( 'All', 'aspv5' ); ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-3.5 h-3.5"><polyline points="9 18 15 12 9 6"/></svg>
			</a>
		</div>

		<div class="aspv5-hscroll aspv5-reveal">
			<?php foreach ( $categories as $cat ) :
				$cat_image_id  = get_term_meta( $cat->term_id, '_appstorepro_category_image', true );
				$cat_image_url = $cat_image_id ? wp_get_attachment_image_url( $cat_image_id, 'thumbnail' ) : '';
				$cat_link      = get_term_link( $cat );
				?>
				<a href="<?php echo esc_url( $cat_link ); ?>"
				   class="flex flex-col items-center gap-2 flex-shrink-0 w-20 group"
				   title="<?php echo esc_attr( $cat->name ); ?>">
					<div class="w-16 h-16 rounded-2xl overflow-hidden bg-[color:var(--asp-primary)]/10 dark:bg-gray-800 flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow group-hover:bg-[color:var(--asp-primary)]/20">
						<?php if ( $cat_image_url ) : ?>
							<img src="<?php echo esc_url( $cat_image_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" class="w-full h-full object-cover" loading="lazy">
						<?php else : ?>
							<span class="text-[color:var(--asp-primary)] text-2xl">
								<?php echo wp_kses( aspv5_get_category_icon_svg( $cat->slug ), aspv5_allowed_svg_kses() ); ?>
							</span>
						<?php endif; ?>
					</div>
					<span class="text-[11px] font-medium text-gray-700 dark:text-gray-300 text-center line-clamp-2 w-full leading-tight">
						<?php echo esc_html( $cat->name ); ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

	</div>
</section>
