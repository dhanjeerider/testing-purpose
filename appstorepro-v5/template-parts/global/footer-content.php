<?php
// template-parts/global/footer-content.php — AppStore Pro V5
$footer_text  = get_theme_mod( 'aspv5_footer_text', '&copy; ' . gmdate( 'Y' ) . ' AppStore Pro V5. All rights reserved.' );
$telegram_url = get_theme_mod( 'aspv5_social_telegram', '' );
$youtube_url  = get_theme_mod( 'aspv5_social_youtube', '' );
$tg_members   = get_theme_mod( 'aspv5_footer_telegram_members', '' );
?>
<footer class="bg-gray-900 dark:bg-black text-gray-300 mt-16 pb-20 md:pb-0">
	<div class="max-w-6xl mx-auto px-4 pt-12 pb-8">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-10">

			<!-- Brand -->
			<div class="space-y-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/android-icon.svg" width="32" height="32" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="w-8 h-8 rounded-lg">
					<span class="font-bold text-white text-lg"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
				</a>
				<p class="text-sm text-gray-400 leading-relaxed">
					<?php esc_html_e( 'Premium MOD APKs, unlocked games &amp; unlimited resources for Android. Free. Always.', 'aspv5' ); ?>
				</p>
				<!-- Social Links -->
				<div class="flex gap-3">
					<?php if ( $telegram_url ) : ?>
						<a href="<?php echo esc_url( $telegram_url ); ?>"
						   class="flex items-center gap-1.5 bg-[#0088cc]/20 hover:bg-[#0088cc]/40 text-[#0088cc] px-3 py-1.5 rounded-full text-xs font-semibold transition-colors"
						   target="_blank" rel="noopener noreferrer">
							<svg viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.025 9.546c-.152.667-.548.833-1.112.518l-3-2.21-1.447 1.393c-.16.16-.295.295-.604.295l.215-3.053 5.56-5.023c.242-.215-.052-.334-.373-.119L6.74 14.377 3.8 13.46c-.66-.205-.673-.66.138-.978l10.89-4.197c.548-.198 1.028.134.734.963z"/></svg>
							<?php echo $tg_members ? esc_html( $tg_members ) . ' ' . esc_html__( 'members', 'aspv5' ) : esc_html__( 'Telegram', 'aspv5' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $youtube_url ) : ?>
						<a href="<?php echo esc_url( $youtube_url ); ?>"
						   class="flex items-center gap-1.5 bg-red-500/20 hover:bg-red-500/40 text-red-400 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors"
						   target="_blank" rel="noopener noreferrer">
							<svg viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
							<?php esc_html_e( 'YouTube', 'aspv5' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Browse Links -->
			<div>
				<h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4"><?php esc_html_e( 'Browse', 'aspv5' ); ?></h4>
				<ul class="space-y-2">
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'All Apps', 'aspv5' ); ?></a></li>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'Games', 'aspv5' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/app-category/' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'Categories', 'aspv5' ); ?></a></li>
				</ul>
			</div>

			<!-- Legal Links -->
			<div>
				<h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4"><?php esc_html_e( 'Legal', 'aspv5' ); ?></h4>
				<ul class="space-y-2">
					<li><a href="<?php echo esc_url( home_url( '/dmca/' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'DMCA', 'aspv5' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'Disclaimer', 'aspv5' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'Privacy Policy', 'aspv5' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" class="text-sm text-gray-400 hover:text-white transition-colors"><?php esc_html_e( 'Terms', 'aspv5' ); ?></a></li>
				</ul>
			</div>

		</div>

		<!-- Bottom bar -->
		<div class="border-t border-gray-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
			<p><?php echo wp_kses_post( $footer_text ); ?></p>
			<?php
			wp_nav_menu( [
				'theme_location' => 'legal-menu',
				'menu_class'     => 'flex gap-4',
				'fallback_cb'    => false,
				'depth'          => 1,
				'container'      => false,
				'items_wrap'     => '<ul class="flex gap-4">%3$s</ul>',
			] );
			?>
		</div>
	</div>
</footer>
