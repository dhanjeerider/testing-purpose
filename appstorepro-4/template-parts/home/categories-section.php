<?php
// template-parts/home/categories-section.php
$cats = get_terms( [
	'taxonomy'   => 'app-category',
	'hide_empty' => true,
	'number'     => 12,
	'orderby'    => 'count',
	'order'      => 'DESC',
] );
?>
<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
<section class="tc-page pas-reveal">
	<div class="container">
		<div class="section-title-row">
			<div>
				<div class="tc-eyebrow"><?php esc_html_e( 'Browse', 'appstorepro' ); ?></div>
				<h2 class="tc-title"><?php esc_html_e( 'Categories', 'appstorepro' ); ?></h2>
			</div>
			<a href="<?= esc_url( home_url( '/app-category/' ) ); ?>" class="section-view-all"><?php esc_html_e( 'All categories', 'appstorepro' ); ?> &rarr;</a>
		</div>
		<div class="tc-grid">
			<?php foreach ( $cats as $cat ) :
				$icon_slug = $cat->slug;
				$svg       = appstorepro_get_category_icon_svg( $icon_slug );
				?>
				<a href="<?= esc_url( get_term_link( $cat ) ); ?>" class="tc-card">
					<div class="tc-card-icon">
						<?= wp_kses( $svg, appstorepro_allowed_svg_kses() ); // Boxicons markup from template-tags.php ?>
					</div>
					<div class="tc-card-info">
						<div class="tc-card-name"><?= esc_html( $cat->name ); ?></div>
						<div class="tc-card-count"><?= esc_html( $cat->count ); ?> <?php esc_html_e( 'apps', 'appstorepro' ); ?></div>
					</div>
					<span class="tc-card-arrow">
						<i class="bx bx-chevron-right"></i>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>
