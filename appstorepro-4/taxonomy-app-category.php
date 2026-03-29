<?php
// taxonomy-app-category.php — App category archive
get_header();
$term = get_queried_object();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<header class="page-header pas-reveal">
			<div class="section-eyebrow"><?php esc_html_e( 'Category', 'appstorepro' ); ?></div>
			<div class="section-title-row">
				<h1 class="section-title"><?= esc_html( $term->name ); ?></h1>
				<?php if ( $term->description ) : ?>
					<p class="section-desc"><?= esc_html( $term->description ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="app-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] ); ?>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( [ 'class' => 'pas-pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ] ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
