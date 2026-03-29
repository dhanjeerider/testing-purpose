<?php
// template-parts/global/bottom-nav.php
?>
<nav class="bottom-nav" id="bottom-nav" aria-label="<?php esc_attr_e( 'Main mobile navigation', 'appstorepro' ); ?>">
	<a href="<?= esc_url( home_url( '/' ) ); ?>" class="nav-item" data-nav="home">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
		</svg>
		<span><?php esc_html_e( 'Home', 'appstorepro' ); ?></span>
	</a>
	<a href="<?= esc_url( home_url( '/app-category/' ) ); ?>" class="nav-item" data-nav="category">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
		</svg>
		<span><?php esc_html_e( 'Category', 'appstorepro' ); ?></span>
	</a>
	<a href="<?= esc_url( home_url( '/apps/' ) ); ?>" class="nav-item" data-nav="apps">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
		</svg>
		<span><?php esc_html_e( 'Apps', 'appstorepro' ); ?></span>
	</a>
	<a href="<?= esc_url( home_url( '/games/' ) ); ?>" class="nav-item" data-nav="games">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<rect x="2" y="7" width="20" height="13" rx="3"/><path d="M8 11h4m-2-2v4"/><circle cx="17" cy="13" r=".5" fill="currentColor"/><circle cx="15" cy="11" r=".5" fill="currentColor"/>
		</svg>
		<span><?php esc_html_e( 'Games', 'appstorepro' ); ?></span>
	</a>
	<button class="nav-item" id="menu-toggle-btn" aria-label="<?php esc_attr_e( 'Open menu', 'appstorepro' ); ?>" data-nav="menu">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
		</svg>
		<span><?php esc_html_e( 'Menu', 'appstorepro' ); ?></span>
	</button>
</nav>
