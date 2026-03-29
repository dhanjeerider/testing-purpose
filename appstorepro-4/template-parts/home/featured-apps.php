<?php
// template-parts/home/featured-apps.php

// Featured Apps — horizontal scroll
$featured_query = new WP_Query( [
	'post_type'      => 'app',
	'posts_per_page' => 10,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_status'    => 'publish',
] );
?>
<?php if ( $featured_query->have_posts() ) : ?>
<section class="pas-section pas-reveal">
	<div class="container">
		<div class="section-title-row">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'Fresh Picks', 'appstorepro' ); ?></div>
				<h2 class="section-title"><?php esc_html_e( 'New Apps', 'appstorepro' ); ?></h2>
			</div>
			<a href="<?= esc_url( home_url( '/apps/' ) ); ?>" class="section-view-all"><?php esc_html_e( 'See all', 'appstorepro' ); ?> &rarr;</a>
		</div>
		<div class="h-scroll-wrap">
			<div class="h-scroll">
				<?php
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
					$post_id   = get_the_ID();
					$icon_url  = appstorepro_get_app_meta( $post_id, '_app_icon_url' );
					$rating    = appstorepro_get_app_meta( $post_id, '_app_rating' );
					$size      = appstorepro_get_app_meta( $post_id, '_app_size' );
					$is_mod    = appstorepro_get_app_meta( $post_id, '_app_is_mod' );
					$terms     = get_the_terms( $post_id, 'app-category' );
					$cat_name  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
					?>
					<a href="<?= esc_url( get_the_permalink() ); ?>" class="app-card-h">
						<div class="app-card-h-icon">
							<?php if ( $icon_url ) : ?>
								<img src="<?= esc_url( $icon_url ); ?>" alt="<?= esc_attr( get_the_title() ); ?>" loading="lazy" width="64" height="64">
							<?php elseif ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'app-icon', [ 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
							<?php else : ?>
								<div class="app-icon-placeholder"><?= esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></div>
							<?php endif; ?>
							<?php if ( $is_mod ) : ?>
								<span class="badge-mod">MOD</span>
							<?php endif; ?>
						</div>
						<div class="app-card-h-info">
							<div class="app-card-h-title"><?= esc_html( get_the_title() ); ?></div>
							<?php if ( $cat_name ) : ?>
								<div class="app-card-h-cat"><?= esc_html( $cat_name ); ?></div>
							<?php endif; ?>
							<?php if ( $rating ) : ?>
								<div class="app-card-h-rating"><?= esc_html( $rating ); ?> ★</div>
							<?php endif; ?>
						</div>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
// New Games section
$games_term = get_term_by( 'slug', 'games', 'app-category' );
$games_query = new WP_Query( [
	'post_type'      => 'app',
	'posts_per_page' => 6,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_status'    => 'publish',
	'tax_query'      => $games_term ? [
		[
			'taxonomy' => 'app-category',
			'field'    => 'slug',
			'terms'    => 'games',
		],
	] : [],
] );
?>
<?php if ( $games_query->have_posts() ) : ?>
<section class="pas-section pas-reveal">
	<div class="container">
		<div class="section-title-row">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'Play Now', 'appstorepro' ); ?></div>
				<h2 class="section-title"><?php esc_html_e( 'New Games', 'appstorepro' ); ?></h2>
			</div>
			<a href="<?= esc_url( home_url( '/games/' ) ); ?>" class="section-view-all"><?php esc_html_e( 'See all', 'appstorepro' ); ?> &rarr;</a>
		</div>
		<div class="app-grid">
			<?php
			while ( $games_query->have_posts() ) :
				$games_query->the_post();
				get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
// All Recent Apps grid
$recent_query = new WP_Query( [
	'post_type'      => 'app',
	'posts_per_page' => 12,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_status'    => 'publish',
	'offset'         => 10,
] );
?>
<?php if ( $recent_query->have_posts() ) : ?>
<section class="pas-section pas-reveal">
	<div class="container">
		<div class="section-title-row">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'Explore', 'appstorepro' ); ?></div>
				<h2 class="section-title"><?php esc_html_e( 'All Apps', 'appstorepro' ); ?></h2>
			</div>
			<a href="<?= esc_url( home_url( '/apps/' ) ); ?>" class="section-view-all"><?php esc_html_e( 'See all', 'appstorepro' ); ?> &rarr;</a>
		</div>
		<div class="app-grid">
			<?php
			while ( $recent_query->have_posts() ) :
				$recent_query->the_post();
				get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>
