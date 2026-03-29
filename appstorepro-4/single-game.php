<?php
// single-game.php — Single game CPT reuses app layout
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/single/app-single' ); ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
