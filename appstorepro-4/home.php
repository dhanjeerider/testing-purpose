<?php
// home.php — Homepage template
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php get_template_part( 'template-parts/home/hero' ); ?>
	<?php get_template_part( 'template-parts/home/categories-section' ); ?>
	<?php get_template_part( 'template-parts/home/featured-apps' ); ?>
</main>
<?php
get_footer();
