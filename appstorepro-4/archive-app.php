<?php
// archive-app.php — App CPT archive
get_header();
$current_term = get_queried_object();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<header class="page-header pas-reveal">
			<div class="section-eyebrow">Browse</div>
			<div class="section-title-row">
				<h1 class="section-title"><?php esc_html_e( 'All Apps', 'appstorepro' ); ?></h1>
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
