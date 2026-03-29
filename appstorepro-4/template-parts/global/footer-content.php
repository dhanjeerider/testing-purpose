<?php
// template-parts/global/footer-content.php
$footer_text     = get_theme_mod( 'appstorepro_footer_text', '&copy; ' . gmdate( 'Y' ) . ' AppStore Pro. All rights reserved.' );
$telegram_url    = get_theme_mod( 'appstorepro_social_telegram', '' );
$youtube_url     = get_theme_mod( 'appstorepro_social_youtube', '' );
$tg_members      = get_theme_mod( 'appstorepro_footer_telegram_members', '' );
?>
<footer class="site-footer">
	<div class="container">
		<div class="footer-inner">
			<div class="footer-brand">
				<a href="<?= esc_url( home_url( '/' ) ); ?>" class="footer-logo">
					<img src="<?= esc_url( get_template_directory_uri() ); ?>/assets/images/android-icon.svg" width="32" height="32" alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>">
					<span><?= esc_html( get_bloginfo( 'name' ) ); ?></span>
				</a>
				<p class="footer-desc"><?php esc_html_e( 'Premium MOD APKs, unlocked games & unlimited resources for Android. Free. Always.', 'appstorepro' ); ?></p>
				<div class="footer-social">
					<?php if ( $telegram_url ) : ?>
						<a href="<?= esc_url( $telegram_url ); ?>" class="footer-social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Telegram', 'appstorepro' ); ?>">
							<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.025 9.546c-.152.667-.548.833-1.112.518l-3-2.21-1.447 1.393c-.16.16-.295.295-.604.295l.215-3.053 5.56-5.023c.242-.215-.052-.334-.373-.119L6.74 14.377 3.8 13.46c-.66-.205-.673-.66.138-.978l10.89-4.197c.548-.198 1.028.134.734.963z"/></svg>
							<?php if ( $tg_members ) : ?>
								<span class="footer-social-count"><?= esc_html( $tg_members ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					<?php if ( $youtube_url ) : ?>
						<a href="<?= esc_url( $youtube_url ); ?>" class="footer-social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'YouTube', 'appstorepro' ); ?>">
							<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="footer-links">
				<div class="footer-col">
					<h4><?php esc_html_e( 'Browse', 'appstorepro' ); ?></h4>
					<ul>
						<li><a href="<?= esc_url( home_url( '/apps/' ) ); ?>"><?php esc_html_e( 'All Apps', 'appstorepro' ); ?></a></li>
						<li><a href="<?= esc_url( home_url( '/games/' ) ); ?>"><?php esc_html_e( 'Games', 'appstorepro' ); ?></a></li>
						<li><a href="<?= esc_url( home_url( '/app-category/' ) ); ?>"><?php esc_html_e( 'Categories', 'appstorepro' ); ?></a></li>
					</ul>
				</div>
				<div class="footer-col">
					<h4><?php esc_html_e( 'Legal', 'appstorepro' ); ?></h4>
					<ul>
						<li><a href="<?= esc_url( home_url( '/dmca/' ) ); ?>"><?php esc_html_e( 'DMCA', 'appstorepro' ); ?></a></li>
						<li><a href="<?= esc_url( home_url( '/disclaimer/' ) ); ?>"><?php esc_html_e( 'Disclaimer', 'appstorepro' ); ?></a></li>
						<li><a href="<?= esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'appstorepro' ); ?></a></li>
						<li><a href="<?= esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'appstorepro' ); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<p><?php echo wp_kses_post( $footer_text ); ?></p>
		</div>
	</div>
</footer>
