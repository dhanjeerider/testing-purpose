<?php
// template-parts/global/slide-menu.php
?>
<div class="slide-menu" id="slide-menu" aria-hidden="true">
	<div class="slide-menu-overlay" id="slide-menu-overlay"></div>
	<div class="slide-menu-panel" role="dialog" aria-label="<?php esc_attr_e( 'Navigation menu', 'appstorepro' ); ?>">
		<div class="slide-menu-header">
			<span class="slide-menu-title"><?php esc_html_e( 'Menu', 'appstorepro' ); ?></span>
			<button class="slide-menu-close" id="slide-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'appstorepro' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
					<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
				</svg>
			</button>
		</div>

		<div class="sm-section">
			<div class="sm-section-label"><?php esc_html_e( 'Appearance', 'appstorepro' ); ?></div>
			<div class="sm-theme-btns">
				<button class="sm-theme-btn" id="sm-theme-light" aria-label="<?php esc_attr_e( 'Light mode', 'appstorepro' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
						<circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
						<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
						<line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
					</svg>
					<?php esc_html_e( 'Light', 'appstorepro' ); ?>
				</button>
				<button class="sm-theme-btn" id="sm-theme-dark" aria-label="<?php esc_attr_e( 'Dark mode', 'appstorepro' ); ?>">
					<svg viewBox="0 0 24 24" fill="currentColor" stroke="none" width="16" height="16">
						<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
					</svg>
					<?php esc_html_e( 'Dark', 'appstorepro' ); ?>
				</button>
				<button class="sm-theme-btn" id="sm-theme-system" aria-label="<?php esc_attr_e( 'System theme', 'appstorepro' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
						<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
					</svg>
					<?php esc_html_e( 'System', 'appstorepro' ); ?>
				</button>
			</div>
			<div class="theme-color-picker sm-theme-colors" role="group" aria-label="<?php esc_attr_e( 'Choose theme colour', 'appstorepro' ); ?>">
				<?php foreach ( appstorepro_get_color_themes() as $theme ) : ?>
					<button class="theme-dot<?= ( $theme['name'] === 'Orange' ) ? ' active' : ''; ?>"
						data-primary="<?= esc_attr( $theme['primary'] ); ?>"
						data-light="<?= esc_attr( $theme['light'] ); ?>"
						data-bg="<?= esc_attr( $theme['bg'] ); ?>"
						style="background:<?= esc_attr( $theme['primary'] ); ?>;"
						title="<?= esc_attr( $theme['name'] ); ?>"
						aria-label="<?= esc_attr( $theme['name'] ); ?> theme"></button>
				<?php endforeach; ?>
			</div>
			<div class="sm-section-label sm-subdued"><?php esc_html_e( 'Background animation', 'appstorepro' ); ?></div>
			<button class="btn-icon sm-bg-toggle" id="particle-toggle" aria-label="<?php esc_attr_e( 'Toggle background animation', 'appstorepro' ); ?>">
				<svg id="particle-icon-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<circle cx="12" cy="12" r="2" fill="currentColor" stroke="none"/>
					<circle cx="5" cy="5" r="1.5" fill="currentColor" stroke="none" opacity="0.6"/>
					<circle cx="19" cy="5" r="1" fill="currentColor" stroke="none" opacity="0.4"/>
					<circle cx="5" cy="19" r="1" fill="currentColor" stroke="none" opacity="0.4"/>
					<circle cx="19" cy="19" r="1.5" fill="currentColor" stroke="none" opacity="0.6"/>
					<circle cx="12" cy="4" r="1" fill="currentColor" stroke="none" opacity="0.5"/>
					<circle cx="20" cy="12" r="1" fill="currentColor" stroke="none" opacity="0.5"/>
				</svg>
				<svg id="particle-icon-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
					<circle cx="12" cy="12" r="2" fill="currentColor" stroke="none" opacity="0.3"/>
					<line x1="4" y1="4" x2="20" y2="20" stroke-width="2"/>
				</svg>
				<span class="sm-bg-toggle-label"><?php esc_html_e( 'Toggle background animation', 'appstorepro' ); ?></span>
			</button>
		</div>

		<div class="sm-divider"></div>

		<div class="sm-section">
			<div class="sm-section-label"><?php esc_html_e( 'Navigate', 'appstorepro' ); ?></div>
			<nav class="sm-nav" aria-label="<?php esc_attr_e( 'Slide menu navigation', 'appstorepro' ); ?>">
				<a href="<?= esc_url( home_url( '/' ) ); ?>" class="sm-nav-item">
					<span class="sm-nav-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
							<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
						</svg>
					</span>
					<?php esc_html_e( 'Home', 'appstorepro' ); ?>
				</a>
				<a href="<?= esc_url( home_url( '/apps/' ) ); ?>" class="sm-nav-item">
					<span class="sm-nav-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
							<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
						</svg>
					</span>
					<?php esc_html_e( 'All Apps', 'appstorepro' ); ?>
				</a>
				<a href="<?= esc_url( home_url( '/games/' ) ); ?>" class="sm-nav-item">
					<span class="sm-nav-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
							<rect x="2" y="7" width="20" height="13" rx="3"/><path d="M8 11h4m-2-2v4"/><circle cx="17" cy="13" r=".5" fill="currentColor"/><circle cx="15" cy="11" r=".5" fill="currentColor"/>
						</svg>
					</span>
					<?php esc_html_e( 'Games', 'appstorepro' ); ?>
				</a>
				<a href="<?= esc_url( home_url( '/app-category/' ) ); ?>" class="sm-nav-item">
					<span class="sm-nav-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
							<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
						</svg>
					</span>
					<?php esc_html_e( 'Categories', 'appstorepro' ); ?>
				</a>
			</nav>
		</div>

		<?php if ( has_nav_menu( 'slide-menu' ) ) : ?>
			<div class="sm-divider"></div>
			<div class="sm-section">
				<div class="sm-section-label"><?php esc_html_e( 'Pages', 'appstorepro' ); ?></div>
				<?php
				wp_nav_menu( [
					'theme_location' => 'slide-menu',
					'menu_class'     => 'sm-nav',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
					'link_before'    => '',
					'link_after'     => '',
					'item_spacing'   => 'discard',
					'walker'         => null,
				] );
				?>
			</div>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'legal-menu' ) ) : ?>
			<div class="sm-divider"></div>
			<nav class="sm-nav sm-nav-legal" aria-label="<?php esc_attr_e( 'Legal links', 'appstorepro' ); ?>">
				<?php
				wp_nav_menu( [
					'theme_location' => 'legal-menu',
					'menu_class'     => 'sm-nav-legal-list',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
				] );
				?>
			</nav>
		<?php else : ?>
			<div class="sm-divider"></div>
			<nav class="sm-nav sm-nav-legal" aria-label="<?php esc_attr_e( 'Legal links', 'appstorepro' ); ?>">
				<a href="<?= esc_url( home_url( '/dmca/' ) ); ?>" class="sm-nav-item"><?php esc_html_e( 'DMCA', 'appstorepro' ); ?></a>
				<a href="<?= esc_url( home_url( '/disclaimer/' ) ); ?>" class="sm-nav-item"><?php esc_html_e( 'Disclaimer', 'appstorepro' ); ?></a>
				<a href="<?= esc_url( home_url( '/privacy-policy/' ) ); ?>" class="sm-nav-item"><?php esc_html_e( 'Privacy Policy', 'appstorepro' ); ?></a>
				<a href="<?= esc_url( home_url( '/terms/' ) ); ?>" class="sm-nav-item"><?php esc_html_e( 'Terms', 'appstorepro' ); ?></a>
			</nav>
		<?php endif; ?>
	</div>
</div>
