<?php
// template-parts/global/site-header.php — AppStore Pro V5
$color_themes = aspv5_get_color_themes();
?>
<header class="site-header sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 transition-shadow duration-300" id="aspv5-header">
	<div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between gap-3">

		<!-- Logo -->
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 flex-shrink-0 group" rel="home">
			<?php $logo = get_theme_mod( 'custom_logo' ); ?>
			<?php if ( $logo ) : ?>
				<img src="<?php echo esc_url( wp_get_attachment_image_url( $logo, 'full' ) ); ?>" class="w-8 h-8 rounded-lg" width="32" height="32" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php else : ?>
				<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/android-icon.svg" class="w-8 h-8 rounded-lg" width="32" height="32" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php endif; ?>
			<span class="font-display font-bold text-[15px] text-gray-900 dark:text-white tracking-tight group-hover:text-primary transition-colors">
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			</span>
		</a>

		<!-- Desktop Nav -->
		<nav class="hidden md:flex items-center gap-1" aria-label="<?php esc_attr_e( 'Primary navigation', 'aspv5' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-3 py-1.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
				<?php esc_html_e( 'Home', 'aspv5' ); ?>
			</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>" class="px-3 py-1.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
				<?php esc_html_e( 'Apps', 'aspv5' ); ?>
			</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>" class="px-3 py-1.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary transition-colors">
				<?php esc_html_e( 'Games', 'aspv5' ); ?>
			</a>
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'menu_class'     => 'flex items-center gap-1',
				'fallback_cb'    => false,
				'depth'          => 1,
				'walker'         => false,
				'items_wrap'     => '%3$s',
				'container'      => false,
			] );
			?>
		</nav>

		<!-- Header Actions -->
		<div class="flex items-center gap-2">

			<!-- Color Theme Dots (desktop) -->
			<div class="hidden sm:flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 rounded-full px-2 py-1.5">
				<?php foreach ( $color_themes as $ct ) : ?>
					<button class="aspv5-color-dot w-4 h-4 rounded-full border-2 border-transparent hover:scale-125 transition-transform focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1"
					        style="background:<?php echo esc_attr( $ct['primary'] ); ?>;"
					        data-primary="<?php echo esc_attr( $ct['primary'] ); ?>"
					        data-light="<?php echo esc_attr( $ct['light'] ); ?>"
					        data-bg="<?php echo esc_attr( $ct['bg'] ); ?>"
					        title="<?php echo esc_attr( $ct['name'] ); ?>"
					        aria-label="<?php echo esc_attr( sprintf( __( 'Color theme: %s', 'aspv5' ), $ct['name'] ) ); ?>">
					</button>
				<?php endforeach; ?>
			</div>

			<!-- Dark Mode Toggle -->
			<button class="aspv5-dm-toggle w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white transition-colors"
			        aria-label="<?php esc_attr_e( 'Toggle dark mode', 'aspv5' ); ?>"
			        aria-pressed="false">
				<svg class="ico-sun w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
					<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
					<line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
					<line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
				</svg>
				<svg class="ico-moon w-4 h-4" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
					<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
				</svg>
			</button>

			<!-- Search Toggle -->
			<button class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white transition-colors"
			        id="aspv5-search-toggle"
			        aria-label="<?php esc_attr_e( 'Toggle search', 'aspv5' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
					<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
				</svg>
			</button>

			<!-- Menu Toggle (mobile) -->
			<button class="md:hidden w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300"
			        id="aspv5-menu-toggle"
			        aria-label="<?php esc_attr_e( 'Open menu', 'aspv5' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
					<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
				</svg>
			</button>

		</div>
	</div>

	<!-- Search Dropdown -->
	<div class="aspv5-search-dropdown bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800" id="aspv5-search-dropdown">
		<div class="max-w-6xl mx-auto px-4 py-3">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex gap-2">
				<input type="search" name="s"
				       placeholder="<?php esc_attr_e( 'Search apps & games...', 'aspv5' ); ?>"
				       value="<?php echo esc_attr( get_search_query() ); ?>"
				       class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/40"
				       autocomplete="off">
				<button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition-opacity">
					<?php esc_html_e( 'Search', 'aspv5' ); ?>
				</button>
			</form>
		</div>
	</div>
</header>
<canvas id="aspv5-particles" aria-hidden="true"></canvas>
