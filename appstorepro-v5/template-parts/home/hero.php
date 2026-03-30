<?php
// template-parts/home/hero.php — AppStore Pro V5
$hero_title    = get_theme_mod( 'aspv5_hero_title', 'Premium Mods,' );
$hero_subtitle = get_theme_mod( 'aspv5_hero_subtitle', 'Ad-free apps, unlocked games & unlimited resources for Android.' );
$hero_badge    = get_theme_mod( 'aspv5_hero_badge', '★ TRUSTED MOD STORE' );
?>
<section class="relative overflow-hidden py-14 pb-10">
	<!-- Background gradient -->
	<div class="absolute inset-0 bg-gradient-to-br from-[color:var(--asp-primary-bg)] via-white to-white dark:from-gray-900 dark:via-gray-950 dark:to-gray-950 -z-10"></div>
	<!-- Decorative circles -->
	<div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-[color:var(--asp-primary)] opacity-[0.06] -z-10"></div>
	<div class="absolute -bottom-10 -left-10 w-60 h-60 rounded-full bg-[color:var(--asp-primary)] opacity-[0.04] -z-10"></div>

	<div class="max-w-6xl mx-auto px-4 text-center">

		<!-- Badge -->
		<div class="aspv5-reveal inline-flex items-center gap-2 bg-[color:var(--asp-primary)]/10 text-[color:var(--asp-primary)] rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest mb-6">
			<?php echo esc_html( $hero_badge ); ?>
		</div>

		<!-- Headline -->
		<h1 class="aspv5-reveal text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-gray-900 dark:text-white leading-tight mb-4">
			<?php echo esc_html( $hero_title ); ?><br>
			<span class="text-[color:var(--asp-primary)]">
				<?php esc_html_e( 'Unlimited.', 'aspv5' ); ?>
			</span>
		</h1>

		<!-- Subtitle -->
		<p class="aspv5-reveal text-gray-600 dark:text-gray-400 text-base sm:text-lg max-w-2xl mx-auto mb-8">
			<?php echo esc_html( $hero_subtitle ); ?>
		</p>

		<!-- CTA Buttons -->
		<div class="aspv5-reveal flex flex-col sm:flex-row gap-3 justify-center items-center">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
			   class="inline-flex items-center gap-2 bg-[color:var(--asp-primary)] hover:opacity-90 text-white font-semibold px-6 py-3 rounded-2xl text-sm transition-opacity shadow-lg shadow-[color:var(--asp-primary)]/30">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
				<?php esc_html_e( 'Browse Apps', 'aspv5' ); ?>
			</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>"
			   class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 font-semibold px-6 py-3 rounded-2xl text-sm transition-colors border border-gray-200 dark:border-gray-700">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4"><rect x="2" y="7" width="20" height="13" rx="3"/><path d="M8 11h4m-2-2v4"/><circle cx="17" cy="13" r=".5" fill="currentColor"/><circle cx="15" cy="11" r=".5" fill="currentColor"/></svg>
				<?php esc_html_e( 'Browse Games', 'aspv5' ); ?>
			</a>
		</div>

		<!-- Stats row -->
		<div class="aspv5-reveal flex justify-center gap-6 sm:gap-10 mt-10">
			<?php
			$app_count  = wp_count_posts( 'app' )->publish;
			$game_count = wp_count_posts( 'game' )->publish;
			$cat_count  = wp_count_terms( [ 'taxonomy' => 'app-category' ] );
			?>
			<div class="text-center">
				<div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo esc_html( number_format_i18n( $app_count ) ); ?>+</div>
				<div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?php esc_html_e( 'Apps', 'aspv5' ); ?></div>
			</div>
			<div class="w-px bg-gray-200 dark:bg-gray-700"></div>
			<div class="text-center">
				<div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo esc_html( number_format_i18n( $game_count ) ); ?>+</div>
				<div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?php esc_html_e( 'Games', 'aspv5' ); ?></div>
			</div>
			<div class="w-px bg-gray-200 dark:bg-gray-700"></div>
			<div class="text-center">
				<div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo esc_html( is_int( $cat_count ) ? number_format_i18n( $cat_count ) : '0' ); ?>+</div>
				<div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?php esc_html_e( 'Categories', 'aspv5' ); ?></div>
			</div>
		</div>
	</div>
</section>
