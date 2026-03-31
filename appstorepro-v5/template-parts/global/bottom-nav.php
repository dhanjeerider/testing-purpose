<?php
// template-parts/global/bottom-nav.php — AppStore Pro V5
?>
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-800 flex items-center h-16 px-2"
     aria-label="<?php esc_attr_e( 'Mobile navigation', 'aspv5' ); ?>">

	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
	   class="flex-1 flex flex-col items-center gap-0.5 py-2 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
			<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
		</svg>
		<span class="text-[10px] font-medium"><?php esc_html_e( 'Home', 'aspv5' ); ?></span>
	</a>

	<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
	   class="flex-1 flex flex-col items-center gap-0.5 py-2 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
			<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
		</svg>
		<span class="text-[10px] font-medium"><?php esc_html_e( 'Apps', 'aspv5' ); ?></span>
	</a>

	<a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>"
	   class="flex-1 flex flex-col items-center gap-0.5 py-2 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
			<rect x="2" y="7" width="20" height="13" rx="3"/><path d="M8 11h4m-2-2v4"/><circle cx="17" cy="13" r=".5" fill="currentColor"/><circle cx="15" cy="11" r=".5" fill="currentColor"/>
		</svg>
		<span class="text-[10px] font-medium"><?php esc_html_e( 'Games', 'aspv5' ); ?></span>
	</a>

	<a href="<?php echo esc_url( home_url( '/app-category/' ) ); ?>"
	   class="flex-1 flex flex-col items-center gap-0.5 py-2 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
			<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
		</svg>
		<span class="text-[10px] font-medium"><?php esc_html_e( 'Categories', 'aspv5' ); ?></span>
	</a>

	<button id="aspv5-menu-toggle"
	        class="flex-1 flex flex-col items-center gap-0.5 py-2 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"
	        aria-label="<?php esc_attr_e( 'Open menu', 'aspv5' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
			<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
		</svg>
		<span class="text-[10px] font-medium"><?php esc_html_e( 'Menu', 'aspv5' ); ?></span>
	</button>

</nav>
