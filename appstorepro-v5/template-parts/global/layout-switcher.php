<?php
// template-parts/global/layout-switcher.php — AppStore Pro V5
// Reusable layout switcher bar (4 buttons)
?>
<div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-2xl p-1" role="group" aria-label="<?php esc_attr_e( 'Layout options', 'aspv5' ); ?>">
	<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors"
	        data-layout="grid"
	        title="<?php esc_attr_e( 'Grid view', 'aspv5' ); ?>"
	        aria-label="<?php esc_attr_e( 'Grid view', 'aspv5' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4">
			<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
		</svg>
	</button>
	<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors"
	        data-layout="list"
	        title="<?php esc_attr_e( 'List view', 'aspv5' ); ?>"
	        aria-label="<?php esc_attr_e( 'List view', 'aspv5' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4">
			<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
			<line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
		</svg>
	</button>
	<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors"
	        data-layout="banner"
	        title="<?php esc_attr_e( 'Poster view', 'aspv5' ); ?>"
	        aria-label="<?php esc_attr_e( 'Poster view', 'aspv5' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4">
			<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/>
		</svg>
	</button>
	<button class="aspv5-layout-btn flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 dark:text-gray-400 transition-colors"
	        data-layout="compact"
	        title="<?php esc_attr_e( 'Compact view', 'aspv5' ); ?>"
	        aria-label="<?php esc_attr_e( 'Compact view', 'aspv5' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4">
			<rect x="2" y="2" width="5" height="5" rx="1"/><rect x="9.5" y="2" width="5" height="5" rx="1"/><rect x="17" y="2" width="5" height="5" rx="1"/>
			<rect x="2" y="9.5" width="5" height="5" rx="1"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/><rect x="17" y="9.5" width="5" height="5" rx="1"/>
			<rect x="2" y="17" width="5" height="5" rx="1"/><rect x="9.5" y="17" width="5" height="5" rx="1"/><rect x="17" y="17" width="5" height="5" rx="1"/>
		</svg>
	</button>
</div>
