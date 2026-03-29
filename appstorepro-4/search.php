<?php
// search.php — Search results
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<header class="page-header pas-reveal">
			<div class="section-eyebrow"><?php esc_html_e( 'Search', 'appstorepro' ); ?></div>
			<div class="section-title-row">
				<h1 class="section-title">
					<?php
					printf(
						/* translators: %s: search query */
						esc_html__( 'Results for: %s', 'appstorepro' ),
						'<span class="search-term">' . esc_html( get_search_query() ) . '</span>'
					);
					?>
				</h1>
			</div>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="app-grid search-results-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( get_post_type() === 'app' ) :
						get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] );
					else :
						get_template_part( 'template-parts/content/content', 'post' );
					endif;
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( [ 'class' => 'pas-pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ] ); ?>
		<?php else : ?>
			<div class="search-no-results">
				<div class="search-no-icon">🔍</div>
				<h2><?php esc_html_e( 'Nothing found', 'appstorepro' ); ?></h2>
				<p><?php esc_html_e( 'Try different keywords or check spelling.', 'appstorepro' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
