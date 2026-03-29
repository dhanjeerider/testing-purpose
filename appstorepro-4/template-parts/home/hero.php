<?php
// template-parts/home/hero.php
$hero_title    = get_theme_mod( 'appstorepro_hero_title', 'Premium mods,' );
$hero_sub      = get_theme_mod( 'appstorepro_hero_subtitle', 'Ad-free apps, unlocked games & unlimited resources for Android.' );
$hero_badge    = get_theme_mod( 'appstorepro_hero_badge', '★ TRUSTED MOD STORE' );
?>
<section class="fp-hero pas-reveal">
	<div class="fp-hero-blob fp-hero-blob1" aria-hidden="true"></div>
	<div class="fp-hero-blob fp-hero-blob2" aria-hidden="true"></div>
	<div class="fp-badge"><?= esc_html( $hero_badge ); ?></div>
	<h1 class="fp-hero-title">
		<?= esc_html( $hero_title ); ?><br>
		<span class="fp-hero-hl"><?php esc_html_e( 'free forever', 'appstorepro' ); ?></span>
	</h1>
	<p class="fp-hero-sub"><?= esc_html( $hero_sub ); ?></p>
	<form class="fp-search-form" method="get" action="<?= esc_url( home_url( '/' ) ); ?>" role="search">
		<input type="hidden" name="post_type" value="app">
		<div class="fp-search-wrap">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search apps, games, mods...', 'appstorepro' ); ?>" autocomplete="off" aria-label="<?php esc_attr_e( 'Search apps', 'appstorepro' ); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'appstorepro' ); ?></button>
		</div>
	</form>
	<div class="fp-cats">
		<?php
		$cats = get_terms( [ 'taxonomy' => 'app-category', 'number' => 6, 'hide_empty' => true ] );
		if ( $cats && ! is_wp_error( $cats ) ) :
			foreach ( $cats as $cat ) :
				?>
				<a href="<?= esc_url( get_term_link( $cat ) ); ?>" class="fp-cat-chip"><?= esc_html( $cat->name ); ?></a>
				<?php
			endforeach;
		endif;
		?>
	</div>
</section>
