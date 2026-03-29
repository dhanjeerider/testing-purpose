<?php
// 404.php
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<section class="error-404 not-found">
			<div class="error-404-inner">
				<div class="error-code">404</div>
				<h1 class="error-title"><?php esc_html_e( 'Page not found', 'appstorepro' ); ?></h1>
				<p class="error-message"><?php esc_html_e( "Looks like this page doesn't exist. It may have been moved or deleted.", 'appstorepro' ); ?></p>
				<div class="error-actions">
					<a href="<?= esc_url( home_url( '/' ) ); ?>" class="btn-primary">
						<?php esc_html_e( 'Go Home', 'appstorepro' ); ?>
					</a>
					<a href="<?= esc_url( home_url( '/apps/' ) ); ?>" class="btn-secondary">
						<?php esc_html_e( 'Browse Apps', 'appstorepro' ); ?>
					</a>
				</div>
				<?php get_search_form(); ?>
			</div>
		</section>
	</div>
</main>
<?php get_footer(); ?>
