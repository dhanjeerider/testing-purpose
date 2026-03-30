/**
 * AppStore Pro V5 — main.js
 * Handles: dark mode, color themes, layout switcher, layout persistence,
 *          scroll progress bar, header search, slide menu, reveal animations,
 *          app single page behaviors.
 */
( function () {
	'use strict';

	/* ── Helpers ──────────────────────────────────────────────────────────── */
	const $ = ( sel, ctx = document ) => ctx.querySelector( sel );
	const $$ = ( sel, ctx = document ) => ctx.querySelectorAll( sel );
	const html = document.documentElement;
	const body = document.body;

	/* ══════════════════════════════════════════════════════════════════════
	   1. DARK MODE
	   ════════════════════════════════════════════════════════════════════ */
	const DARK_KEY = 'aspv5_theme';

	function isDark() {
		return html.classList.contains( 'dark' );
	}

	function setDark( dark ) {
		html.classList.toggle( 'dark', dark );
		localStorage.setItem( DARK_KEY, dark ? 'dark' : 'light' );
		updateDarkIcons();
	}

	function updateDarkIcons() {
		const dark = isDark();
		$$( '.aspv5-dm-toggle' ).forEach( btn => {
			btn.setAttribute( 'aria-pressed', dark );
			const sunIco = $( '.ico-sun', btn );
			const moonIco = $( '.ico-moon', btn );
			if ( sunIco ) sunIco.style.display = dark ? 'none' : '';
			if ( moonIco ) moonIco.style.display = dark ? '' : 'none';
		} );
	}

	$$( '.aspv5-dm-toggle' ).forEach( btn => {
		btn.addEventListener( 'click', () => setDark( !isDark() ) );
	} );

	updateDarkIcons();

	/* ══════════════════════════════════════════════════════════════════════
	   2. COLOR THEMES
	   ════════════════════════════════════════════════════════════════════ */
	const COLOR_KEY = 'aspv5_color';

	function applyColor( theme ) {
		html.style.setProperty( '--asp-primary', theme.primary );
		html.style.setProperty( '--asp-primary-light', theme.light || theme.primary );
		html.style.setProperty( '--asp-primary-bg', theme.bg || '#FFF4EC' );
		// Update RGB for shadow calculations
		const r = parseInt( theme.primary.slice( 1, 3 ), 16 );
		const g = parseInt( theme.primary.slice( 3, 5 ), 16 );
		const b = parseInt( theme.primary.slice( 5, 7 ), 16 );
		html.style.setProperty( '--asp-primary-rgb', r + ',' + g + ',' + b );
		localStorage.setItem( COLOR_KEY, JSON.stringify( theme ) );
		updateActiveDots( theme.primary );
	}

	function updateActiveDots( primary ) {
		$$( '.aspv5-color-dot' ).forEach( dot => {
			dot.classList.toggle( 'active', dot.dataset.primary === primary );
		} );
	}

	$$( '.aspv5-color-dot' ).forEach( dot => {
		dot.addEventListener( 'click', () => {
			applyColor( {
				primary: dot.dataset.primary,
				light:   dot.dataset.light || dot.dataset.primary,
				bg:      dot.dataset.bg || '#FFF4EC',
			} );
		} );
	} );

	// Restore saved color
	try {
		const saved = JSON.parse( localStorage.getItem( COLOR_KEY ) );
		if ( saved && /^#[0-9A-Fa-f]{6}$/.test( saved.primary ) ) {
			applyColor( saved );
		}
	} catch ( e ) {}

	/* ══════════════════════════════════════════════════════════════════════
	   3. LAYOUT SWITCHER
	   ════════════════════════════════════════════════════════════════════ */
	const LAYOUT_KEY = 'aspv5_layout';
	const layoutContainer = $( '[data-layout-container]' );
	const layoutBtns      = $$( '.aspv5-layout-btn' );

	function getStoredLayout() {
		return localStorage.getItem( LAYOUT_KEY ) ||
			( typeof AspV5 !== 'undefined' ? AspV5.defaultLayout : 'grid' );
	}

	function applyLayout( layout ) {
		if ( ! layoutContainer ) return;
		const validLayouts = [ 'grid', 'list', 'banner', 'compact' ];
		if ( ! validLayouts.includes( layout ) ) layout = 'grid';

		layoutContainer.setAttribute( 'data-layout', layout );
		localStorage.setItem( LAYOUT_KEY, layout );

		// Update button active state
		layoutBtns.forEach( btn => {
			btn.classList.toggle( 'active', btn.dataset.layout === layout );
		} );

		// Apply grid class based on layout
		layoutContainer.classList.remove( 'layout-grid', 'layout-list', 'layout-banner', 'layout-compact' );
		layoutContainer.classList.add( 'layout-' + layout );
	}

	if ( layoutBtns.length ) {
		layoutBtns.forEach( btn => {
			btn.addEventListener( 'click', () => applyLayout( btn.dataset.layout ) );
		} );
		// Apply saved or default layout
		applyLayout( getStoredLayout() );
	}

	/* ══════════════════════════════════════════════════════════════════════
	   4. SCROLL PROGRESS BAR
	   ════════════════════════════════════════════════════════════════════ */
	const scrollBar = $( '#aspv5-scroll-bar' );
	if ( scrollBar ) {
		document.addEventListener( 'scroll', () => {
			const total = document.documentElement.scrollHeight - window.innerHeight;
			const pct   = total > 0 ? ( window.scrollY / total ) * 100 : 0;
			scrollBar.style.width = pct + '%';
		}, { passive: true } );
	}

	/* ══════════════════════════════════════════════════════════════════════
	   5. STICKY HEADER SHADOW
	   ════════════════════════════════════════════════════════════════════ */
	const siteHeader = $( '#aspv5-header' );
	if ( siteHeader ) {
		document.addEventListener( 'scroll', () => {
			siteHeader.classList.toggle( 'scrolled', window.scrollY > 10 );
		}, { passive: true } );
	}

	/* ══════════════════════════════════════════════════════════════════════
	   6. HEADER SEARCH TOGGLE
	   ════════════════════════════════════════════════════════════════════ */
	const searchToggle   = $( '#aspv5-search-toggle' );
	const searchDropdown = $( '#aspv5-search-dropdown' );
	const searchInput    = searchDropdown ? $( 'input[type=search]', searchDropdown ) : null;

	if ( searchToggle && searchDropdown ) {
		searchToggle.addEventListener( 'click', () => {
			const open = searchDropdown.classList.toggle( 'open' );
			if ( open && searchInput ) {
				setTimeout( () => searchInput.focus(), 50 );
			}
		} );
		document.addEventListener( 'click', e => {
			if ( ! searchToggle.contains( e.target ) && ! searchDropdown.contains( e.target ) ) {
				searchDropdown.classList.remove( 'open' );
			}
		} );
		document.addEventListener( 'keydown', e => {
			if ( e.key === 'Escape' ) searchDropdown.classList.remove( 'open' );
		} );
	}

	/* ══════════════════════════════════════════════════════════════════════
	   7. SLIDE MENU
	   ════════════════════════════════════════════════════════════════════ */
	const menuToggle  = $( '#aspv5-menu-toggle' );
	const closeMenu   = $( '#aspv5-menu-close' );
	const slideMenu   = $( '#aspv5-slide-menu' );
	const menuOverlay = $( '#aspv5-menu-overlay' );

	function openMenu() {
		if ( ! slideMenu ) return;
		slideMenu.classList.add( 'open' );
		if ( menuOverlay ) menuOverlay.classList.remove( 'hidden' );
		document.body.style.overflow = 'hidden';
	}
	function closeMenuFn() {
		if ( ! slideMenu ) return;
		slideMenu.classList.remove( 'open' );
		if ( menuOverlay ) menuOverlay.classList.add( 'hidden' );
		document.body.style.overflow = '';
	}

	if ( menuToggle ) menuToggle.addEventListener( 'click', openMenu );
	if ( closeMenu )  closeMenu.addEventListener( 'click', closeMenuFn );
	if ( menuOverlay ) menuOverlay.addEventListener( 'click', closeMenuFn );

	/* ══════════════════════════════════════════════════════════════════════
	   8. INTERSECTION OBSERVER — reveal animations
	   ════════════════════════════════════════════════════════════════════ */
	if ( 'IntersectionObserver' in window ) {
		const observer = new IntersectionObserver( entries => {
			entries.forEach( entry => {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'visible' );
					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.08 } );
		$$( '.aspv5-reveal' ).forEach( el => observer.observe( el ) );
	} else {
		$$( '.aspv5-reveal' ).forEach( el => el.classList.add( 'visible' ) );
	}

	/* ══════════════════════════════════════════════════════════════════════
	   9. SINGLE APP PAGE
	   ════════════════════════════════════════════════════════════════════ */

	// Share button
	const shareBtn = $( '#sa-share-btn' );
	if ( shareBtn ) {
		shareBtn.addEventListener( 'click', async () => {
			if ( navigator.share ) {
				try {
					await navigator.share( {
						title: document.title,
						url:   location.href,
					} );
				} catch ( e ) {}
			} else {
				await navigator.clipboard.writeText( location.href ).catch( () => {} );
				shareBtn.title = 'Link copied!';
			}
		} );
	}

	// Sticky bottom bar on single
	const saSticky = $( '#sa-sticky' );
	if ( saSticky ) {
		const threshold = 300;
		document.addEventListener( 'scroll', () => {
			saSticky.classList.toggle( 'visible', window.scrollY > threshold );
		}, { passive: true } );
	}

	// Show more / less app content
	const contentEl = $( '#sa-entry-content' );
	const showMoreBtn = $( '#sa-show-more' );
	if ( contentEl && showMoreBtn ) {
		contentEl.classList.add( 'collapsed' );
		showMoreBtn.addEventListener( 'click', () => {
			const collapsed = contentEl.classList.toggle( 'collapsed' );
			showMoreBtn.textContent = collapsed
				? ( showMoreBtn.dataset.showLabel || 'Show more' )
				: ( showMoreBtn.dataset.hideLabel || 'Show less' );
		} );
	}

	// YouTube tutorial
	$$( '.sa-tut-play' ).forEach( btn => {
		btn.addEventListener( 'click', () => {
			const ytid = btn.dataset.ytid;
			if ( ! ytid ) return;
			const thumb = btn.closest( '.sa-tut-thumb' );
			if ( ! thumb ) return;
			const iframe = document.createElement( 'iframe' );
			iframe.src         = 'https://www.youtube-nocookie.com/embed/' + ytid + '?autoplay=1&rel=0';
			iframe.allow       = 'autoplay; fullscreen';
			iframe.allowFullscreen = true;
			iframe.className   = 'w-full rounded-xl';
			iframe.style.aspectRatio = '16/9';
			thumb.replaceWith( iframe );
		} );
	} );

	// YouTube tutorial accordion
	$$( '.sa-tut-tog' ).forEach( tog => {
		tog.addEventListener( 'click', () => {
			const body = tog.nextElementSibling;
			if ( ! body ) return;
			const expanded = tog.getAttribute( 'aria-expanded' ) === 'true';
			tog.setAttribute( 'aria-expanded', !expanded );
			body.style.display = expanded ? 'none' : 'block';
		} );
	} );

	// Screenshot carousel dots
	const scScroll = $( '#sa-sc-scroll' );
	const scDots   = $$( '#sa-sc-dots .sa-sc-dot' );
	if ( scScroll && scDots.length ) {
		scDots.forEach( ( dot, i ) => {
			dot.addEventListener( 'click', () => {
				const items = $$( '.sa-sc-item', scScroll );
				if ( items[ i ] ) {
					scScroll.scrollTo( { left: items[ i ].offsetLeft, behavior: 'smooth' } );
				}
				scDots.forEach( d => d.classList.remove( 'active' ) );
				dot.classList.add( 'active' );
			} );
		} );
	}

} )();
