<?php
// template-parts/global/slide-menu.php — AppStore Pro V5
$color_themes = aspv5_get_color_themes();
?>
<!-- Slide Menu Overlay -->
<div class="fixed inset-0 bg-black/50 z-[60] hidden" id="aspv5-menu-overlay" aria-hidden="true"></div>

<!-- Slide Menu Panel -->
<div class="fixed top-0 right-0 bottom-0 w-72 max-w-full bg-white dark:bg-gray-900 z-[70] flex flex-col shadow-2xl"
     id="aspv5-slide-menu"
     role="dialog"
     aria-modal="true"
     aria-label="<?php esc_attr_e( 'Navigation menu', 'aspv5' ); ?>">

	<!-- Menu Header -->
	<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
		<div class="flex items-center gap-2">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/android-icon.svg" class="w-7 h-7 rounded-md" width="28" height="28" alt="">
			<span class="font-bold text-gray-900 dark:text-white"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
		</div>
		<button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-red-100 hover:text-red-500 transition-colors"
		        id="aspv5-menu-close"
		        aria-label="<?php esc_attr_e( 'Close menu', 'aspv5' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="w-4 h-4">
				<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
			</svg>
		</button>
	</div>

	<!-- Menu Content -->
	<div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-5 h-5 flex-shrink-0"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
			<span class="font-medium text-sm"><?php esc_html_e( 'Home', 'aspv5' ); ?></span>
		</a>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-5 h-5 flex-shrink-0"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
			<span class="font-medium text-sm"><?php esc_html_e( 'All Apps', 'aspv5' ); ?></span>
		</a>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-5 h-5 flex-shrink-0"><rect x="2" y="7" width="20" height="13" rx="3"/><path d="M8 11h4m-2-2v4"/><circle cx="17" cy="13" r=".5" fill="currentColor"/><circle cx="15" cy="11" r=".5" fill="currentColor"/></svg>
			<span class="font-medium text-sm"><?php esc_html_e( 'Games', 'aspv5' ); ?></span>
		</a>
		<a href="<?php echo esc_url( home_url( '/app-category/' ) ); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-5 h-5 flex-shrink-0"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
			<span class="font-medium text-sm"><?php esc_html_e( 'Categories', 'aspv5' ); ?></span>
		</a>

		<!-- Divider -->
		<div class="border-t border-gray-100 dark:border-gray-800 my-2"></div>

		<!-- Color Theme -->
		<div class="px-3 py-2">
			<p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3"><?php esc_html_e( 'Color Theme', 'aspv5' ); ?></p>
			<div class="flex flex-wrap gap-2.5">
				<?php foreach ( $color_themes as $ct ) : ?>
					<button class="aspv5-color-dot w-7 h-7 rounded-full border-2 border-transparent hover:scale-110 transition-transform focus:outline-none focus-visible:ring-2"
					        style="background:<?php echo esc_attr( $ct['primary'] ); ?>;"
					        data-primary="<?php echo esc_attr( $ct['primary'] ); ?>"
					        data-light="<?php echo esc_attr( $ct['light'] ); ?>"
					        data-bg="<?php echo esc_attr( $ct['bg'] ); ?>"
					        title="<?php echo esc_attr( $ct['name'] ); ?>"
					        aria-label="<?php echo esc_attr( $ct['name'] ); ?>">
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Dark Mode -->
		<div class="px-3 py-2 flex items-center justify-between">
			<span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php esc_html_e( 'Dark Mode', 'aspv5' ); ?></span>
			<button class="aspv5-dm-toggle relative w-11 h-6 rounded-full bg-gray-200 dark:bg-primary transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
			        aria-label="<?php esc_attr_e( 'Toggle dark mode', 'aspv5' ); ?>"
			        aria-pressed="false">
				<span class="absolute top-0.5 left-0.5 dark:left-[unset] dark:right-0.5 w-5 h-5 rounded-full bg-white shadow transition-all flex items-center justify-center">
					<svg class="ico-sun w-3 h-3 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/><line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/></svg>
					<svg class="ico-moon w-3 h-3 text-primary" viewBox="0 0 24 24" fill="currentColor" style="display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
				</span>
			</button>
		</div>

	</div>
</div>
