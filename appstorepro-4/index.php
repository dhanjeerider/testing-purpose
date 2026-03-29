<?php
// index.php
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Latest Posts', 'appstorepro' ); ?></h1>
			</header>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', get_post_type() );
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination( [
				'class'     => 'pas-pagination',
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
			] );
			?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
