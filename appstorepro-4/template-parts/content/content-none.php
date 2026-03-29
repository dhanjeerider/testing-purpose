<?php
// template-parts/content/content-none.php
?>
<div class="no-results not-found">
	<div class="no-results-inner">
		<div class="no-results-icon">📭</div>
		<h2 class="no-results-title"><?php esc_html_e( 'Nothing here yet', 'appstorepro' ); ?></h2>
		<p class="no-results-message">
			<?php
			if ( is_search() ) {
				esc_html_e( 'No results found. Try a different search term.', 'appstorepro' );
			} else {
				esc_html_e( 'It seems we can\'t find what you\'re looking for. Browse our app store below.', 'appstorepro' );
			}
			?>
		</p>
		<a href="<?= esc_url( home_url( '/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Go Home', 'appstorepro' ); ?></a>
	</div>
</div>
