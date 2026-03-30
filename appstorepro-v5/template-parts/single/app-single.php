<?php
// template-parts/single/app-single.php — AppStore Pro V5
$post_id         = get_the_ID();
$icon_url        = aspv5_get_app_meta( $post_id, '_app_icon_url' );
$hero_img        = aspv5_get_app_meta( $post_id, '_app_hero_image_url' );
$version         = aspv5_get_app_meta( $post_id, '_app_version' );
$size            = aspv5_get_app_meta( $post_id, '_app_size' );
$developer       = aspv5_get_app_meta( $post_id, '_app_developer' );
$rating_raw      = aspv5_get_app_meta( $post_id, '_app_rating' );
$rating          = $rating_raw ? number_format( (float) $rating_raw, 1, '.', '' ) : '';
$android_ver     = aspv5_get_app_meta( $post_id, '_app_android_version' );
$download_url    = aspv5_get_app_meta( $post_id, '_app_download_url' );
$play_store_url  = aspv5_get_app_meta( $post_id, '_app_play_store_url' );
$telegram_url    = aspv5_get_app_meta( $post_id, '_app_telegram_url' );
$tg_members      = aspv5_get_app_meta( $post_id, '_app_telegram_members' );
$youtube_url     = aspv5_get_app_meta( $post_id, '_app_youtube_url' );
$is_mod          = aspv5_get_app_meta( $post_id, '_app_is_mod' );
$mod_info        = aspv5_get_app_meta( $post_id, '_app_mod_info' );
$downloads_raw   = aspv5_get_app_meta( $post_id, '_app_downloads' );
$downloads       = aspv5_format_downloads( $downloads_raw );
$screenshots_raw = aspv5_get_app_meta( $post_id, '_app_screenshots' );
$screenshots     = $screenshots_raw ? array_filter( array_map( 'trim', explode( "\n", $screenshots_raw ) ) ) : [];
$terms           = get_the_terms( $post_id, 'app-category' );
$cat_name        = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cat_link        = ( $terms && ! is_wp_error( $terms ) ) ? get_term_link( $terms[0] ) : '';
$icon_src        = $icon_url ?: ( has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'app-icon' ) : '' );
$post_type_label = get_post_type() === 'game' ? __( 'Game', 'aspv5' ) : __( 'App', 'aspv5' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'pb-20' ); ?>>

	<!-- ── Hero Banner ── -->
	<div class="relative bg-gray-900 overflow-hidden"
	     style="height: clamp(160px, 30vw, 320px);">
		<?php if ( $hero_img ) : ?>
			<img src="<?php echo esc_url( $hero_img ); ?>"
			     alt="<?php echo esc_attr( get_the_title() ); ?>"
			     class="absolute inset-0 w-full h-full object-cover opacity-60"
			     loading="eager">
		<?php elseif ( $screenshots ) : ?>
			<img src="<?php echo esc_url( reset( $screenshots ) ); ?>"
			     alt="<?php echo esc_attr( get_the_title() ); ?>"
			     class="absolute inset-0 w-full h-full object-cover opacity-50"
			     loading="eager">
		<?php else : ?>
			<div class="absolute inset-0 bg-gradient-to-br from-[color:var(--asp-primary)] to-orange-600 opacity-80"></div>
		<?php endif; ?>
		<div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/60"></div>
	</div>

	<!-- ── App Info Card ── -->
	<div class="max-w-6xl mx-auto px-4">
		<div class="relative -mt-16 mb-6">
			<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-5 flex items-start gap-4">

				<!-- Icon -->
				<div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 flex-shrink-0 shadow-md -mt-10 border-4 border-white dark:border-gray-900">
					<?php if ( $icon_src ) : ?>
						<img src="<?php echo esc_url( $icon_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-full h-full object-cover" loading="eager">
					<?php else : ?>
						<div class="app-icon-placeholder h-full"><span><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span></div>
					<?php endif; ?>
				</div>

				<!-- Meta -->
				<div class="flex-1 min-w-0">
					<div class="flex gap-1 mb-1 flex-wrap">
						<?php if ( $is_mod ) : ?>
							<span class="badge-mod px-2 py-0.5 rounded-full text-[10px] font-bold text-white"><?php esc_html_e( 'MOD', 'aspv5' ); ?></span>
						<?php endif; ?>
						<span class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-[10px] font-semibold px-2 py-0.5 rounded-full"><?php echo esc_html( $post_type_label ); ?></span>
					</div>
					<h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight mb-1">
						<?php echo esc_html( get_the_title() ); ?>
					</h1>
					<?php if ( $developer ) : ?>
						<p class="text-sm text-gray-500 dark:text-gray-400"><?php echo esc_html( $developer ); ?></p>
					<?php endif; ?>

					<!-- Pills -->
					<div class="flex flex-wrap gap-2 mt-2">
						<?php if ( $rating ) : ?>
							<span class="inline-flex items-center gap-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 text-xs font-bold px-2.5 py-1 rounded-full">
								★ <?php echo esc_html( $rating ); ?>
							</span>
						<?php endif; ?>
						<?php if ( $cat_name && $cat_link ) : ?>
							<a href="<?php echo esc_url( $cat_link ); ?>" class="inline-flex items-center bg-[color:var(--asp-primary)]/10 text-[color:var(--asp-primary)] text-xs font-semibold px-2.5 py-1 rounded-full hover:bg-[color:var(--asp-primary)]/20 transition-colors">
								<?php echo esc_html( $cat_name ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $size ) : ?>
							<span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium px-2.5 py-1 rounded-full"><?php echo esc_html( $size ); ?></span>
						<?php endif; ?>
						<?php if ( $downloads ) : ?>
							<span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
								<i class="bx bx-download text-sm"></i><?php echo esc_html( $downloads ); ?>
							</span>
						<?php endif; ?>
						<?php if ( $android_ver ) : ?>
							<span class="bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-medium px-2.5 py-1 rounded-full">
								Android <?php echo esc_html( $android_ver ); ?>+
							</span>
						<?php endif; ?>
					</div>

					<!-- CTA Buttons -->
					<div class="flex gap-3 mt-4 flex-wrap">
						<?php if ( $download_url ) : ?>
							<a href="<?php echo esc_url( $download_url ); ?>"
							   class="flex-1 min-w-[140px] inline-flex items-center justify-center gap-2 bg-[color:var(--asp-primary)] hover:opacity-90 text-white font-bold px-5 py-3 rounded-2xl text-sm transition-opacity shadow-lg shadow-[color:var(--asp-primary)]/30"
							   rel="nofollow noopener" target="_blank">
								<i class="bx bxs-download text-base"></i>
								<?php esc_html_e( 'Download APK', 'aspv5' ); ?>
								<?php if ( $size ) : ?>
									<span class="opacity-75 text-xs font-normal"><?php echo esc_html( $size ); ?></span>
								<?php endif; ?>
							</a>
						<?php endif; ?>
						<button class="w-10 h-10 flex items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-primary hover:text-primary dark:hover:border-primary dark:hover:text-primary transition-colors"
						        id="sa-share-btn"
						        aria-label="<?php esc_attr_e( 'Share', 'aspv5' ); ?>">
							<i class="bx bx-share-alt text-lg"></i>
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

	<!-- ── Screenshots ── -->
	<?php if ( $screenshots ) : ?>
	<section class="mb-6">
		<div class="max-w-6xl mx-auto px-4">
			<h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-3"><?php esc_html_e( 'Screenshots', 'aspv5' ); ?></h2>
			<div class="aspv5-hscroll">
				<?php foreach ( $screenshots as $i => $sc_url ) : ?>
					<div class="sa-sc-item w-36 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 flex-shrink-0 shadow-sm">
						<img src="<?php echo esc_url( $sc_url ); ?>"
						     alt="<?php printf( esc_attr__( 'Screenshot %d', 'aspv5' ), $i + 1 ); ?>"
						     class="w-full h-auto"
						     loading="lazy">
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( count( $screenshots ) > 1 ) : ?>
				<div class="flex justify-center gap-1.5 mt-3" id="sa-sc-dots">
					<?php foreach ( $screenshots as $i => $sc_url ) : ?>
						<button class="sa-sc-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-index="<?php echo esc_attr( $i ); ?>" aria-label="<?php printf( esc_attr__( 'Screenshot %d', 'aspv5' ), $i + 1 ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<!-- ── Telegram Card ── -->
	<?php if ( $telegram_url ) : ?>
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<div class="flex items-center gap-4 bg-[#0088cc]/10 dark:bg-[#0088cc]/10 border border-[#0088cc]/20 rounded-2xl p-4">
				<div class="w-12 h-12 rounded-full bg-[#0088cc]/20 flex items-center justify-center flex-shrink-0 text-[#0088cc]">
					<i class="bx bxl-telegram text-2xl"></i>
				</div>
				<div class="flex-1 min-w-0">
					<h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?php esc_html_e( 'Join our Telegram', 'aspv5' ); ?></h3>
					<?php if ( $tg_members ) : ?>
						<p class="text-xs text-gray-500 dark:text-gray-400"><?php echo esc_html( $tg_members ); ?> <?php esc_html_e( 'members', 'aspv5' ); ?></p>
					<?php endif; ?>
				</div>
				<a href="<?php echo esc_url( $telegram_url ); ?>"
				   class="flex-shrink-0 bg-[#0088cc] hover:bg-[#006da8] text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors"
				   target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Join', 'aspv5' ); ?>
				</a>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ── App Details Table ── -->
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-3"><?php esc_html_e( 'App Details', 'aspv5' ); ?></h2>
			<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 divide-y divide-gray-100 dark:divide-gray-800">
				<?php
				$details = array_filter( [
					__( 'Version', 'aspv5' )          => $version,
					__( 'Size', 'aspv5' )             => $size,
					__( 'Developer', 'aspv5' )        => $developer,
					__( 'Downloads', 'aspv5' )        => $downloads,
					__( 'Requires Android', 'aspv5' ) => $android_ver ? $android_ver . '+' : '',
					__( 'Category', 'aspv5' )         => $cat_name,
					__( 'Updated', 'aspv5' )          => get_the_modified_date(),
				] );
				foreach ( $details as $key => $value ) : ?>
					<div class="flex items-center justify-between px-4 py-3">
						<span class="text-xs text-gray-500 dark:text-gray-400 font-medium"><?php echo esc_html( $key ); ?></span>
						<span class="text-sm text-gray-800 dark:text-gray-200 font-semibold text-right">
							<?php if ( $key === __( 'Category', 'aspv5' ) && $cat_link ) : ?>
								<a href="<?php echo esc_url( $cat_link ); ?>" class="text-primary hover:underline"><?php echo esc_html( $value ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $value ); ?>
							<?php endif; ?>
						</span>
					</div>
				<?php endforeach; ?>

				<?php if ( $is_mod && $mod_info ) : ?>
					<div class="flex items-center justify-between px-4 py-3">
						<span class="text-xs text-gray-500 dark:text-gray-400 font-medium"><?php esc_html_e( 'MOD Features', 'aspv5' ); ?></span>
						<span class="text-sm text-[color:var(--asp-primary)] font-semibold text-right"><?php echo esc_html( $mod_info ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- ── YouTube Tutorial ── -->
	<?php if ( $youtube_url ) :
		$yt_id = '';
		if ( preg_match( '/[?&]v=([a-zA-Z0-9_-]{11})/', $youtube_url, $m ) ) { $yt_id = $m[1]; }
		elseif ( preg_match( '/youtu\.be\/([a-zA-Z0-9_-]{11})/', $youtube_url, $m ) ) { $yt_id = $m[1]; }
	?>
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<button class="w-full flex items-center justify-between bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl px-4 py-3 sa-tut-tog group"
			        aria-expanded="false">
				<span class="flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
					<svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-red-500"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
					<?php esc_html_e( 'Watch Tutorial', 'aspv5' ); ?>
				</span>
				<svg class="sa-tut-arrow w-4 h-4 text-gray-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
			</button>
			<div class="sa-tut-body" style="display:none;">
				<div class="mt-2 rounded-2xl overflow-hidden">
					<?php if ( $yt_id ) : ?>
						<div class="sa-tut-thumb relative cursor-pointer bg-black rounded-2xl overflow-hidden" style="aspect-ratio:16/9;">
							<img src="https://img.youtube.com/vi/<?php echo esc_attr( $yt_id ); ?>/hqdefault.jpg" alt="<?php esc_attr_e( 'Tutorial thumbnail', 'aspv5' ); ?>" class="w-full h-full object-cover" loading="lazy">
							<button class="sa-tut-play absolute inset-0 flex items-center justify-center bg-black/30 hover:bg-black/40 transition-colors"
							        data-ytid="<?php echo esc_attr( $yt_id ); ?>"
							        aria-label="<?php esc_attr_e( 'Play tutorial', 'aspv5' ); ?>">
								<div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center shadow-xl">
									<svg viewBox="0 0 24 24" fill="white" class="w-7 h-7 ml-1"><path d="M8 5v14l11-7z"/></svg>
								</div>
							</button>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( $youtube_url ); ?>" target="_blank" rel="noopener noreferrer" class="block p-4 text-primary text-sm font-semibold hover:underline">
							<?php esc_html_e( 'Watch on YouTube →', 'aspv5' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ── App Content ── -->
	<?php if ( get_the_content() ) : ?>
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-3"><?php esc_html_e( 'About', 'aspv5' ); ?></h2>
			<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-5">
				<div class="entry-content text-sm leading-relaxed text-gray-700 dark:text-gray-300" id="sa-entry-content">
					<?php the_content(); ?>
				</div>
				<button class="mt-4 w-full text-center text-sm font-semibold text-primary hover:opacity-80 transition-opacity"
				        id="sa-show-more"
				        type="button"
				        data-show-label="<?php esc_attr_e( 'Show more', 'aspv5' ); ?>"
				        data-hide-label="<?php esc_attr_e( 'Show less', 'aspv5' ); ?>">
					<?php esc_html_e( 'Show more', 'aspv5' ); ?>
				</button>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ── Play Store Link ── -->
	<?php if ( $play_store_url ) : ?>
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<a href="<?php echo esc_url( $play_store_url ); ?>"
			   class="flex items-center gap-3 p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-green-300 dark:hover:border-green-800 transition-colors group"
			   target="_blank" rel="noopener noreferrer">
				<div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
					<i class="bx bxl-play-store text-xl"></i>
				</div>
				<div class="flex-1">
					<div class="text-sm font-semibold text-gray-900 dark:text-gray-100"><?php esc_html_e( 'Get on Play Store', 'aspv5' ); ?></div>
					<div class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e( 'Official version', 'aspv5' ); ?></div>
				</div>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
			</a>
		</div>
	</section>
	<?php endif; ?>

	<!-- ── Author Card ── -->
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<div class="flex items-center gap-3 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-4">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 44, '', get_the_author(), [ 'class' => 'w-11 h-11 rounded-full flex-shrink-0' ] ); ?>
				<div class="flex-1 min-w-0">
					<div class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php esc_html_e( 'Published by', 'aspv5' ); ?></div>
					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="text-sm font-semibold text-gray-900 dark:text-gray-100 hover:text-primary transition-colors"><?php echo esc_html( get_the_author() ); ?></a>
				</div>
				<div class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"><?php echo esc_html( get_the_date() ); ?></div>
			</div>
		</div>
	</section>

	<!-- ── Related Apps ── -->
	<?php
	$related_args = [
		'post_type'      => get_post_type(),
		'posts_per_page' => 4,
		'post__not_in'   => [ $post_id ],
		'orderby'        => 'rand',
		'post_status'    => 'publish',
	];
	if ( $terms && ! is_wp_error( $terms ) ) {
		$related_args['tax_query'] = [ [
			'taxonomy' => 'app-category',
			'field'    => 'term_id',
			'terms'    => wp_list_pluck( $terms, 'term_id' ),
		] ];
	}
	$related = new WP_Query( $related_args );
	?>
	<?php if ( $related->have_posts() ) : ?>
	<section class="mb-6 aspv5-reveal">
		<div class="max-w-6xl mx-auto px-4">
			<h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-3"><?php esc_html_e( 'Related', 'aspv5' ); ?></h2>
			<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
				<?php while ( $related->have_posts() ) : $related->the_post(); ?>
					<?php get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] ); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

</article>

<!-- ── Sticky Download Bar ── -->
<?php if ( $download_url ) : ?>
<div class="sa-sticky bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-800 shadow-xl" id="sa-sticky">
	<div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-3">
		<div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 flex-shrink-0">
			<?php if ( $icon_src ) : ?>
				<img src="<?php echo esc_url( $icon_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-full h-full object-cover" loading="lazy">
			<?php endif; ?>
		</div>
		<div class="flex-1 min-w-0">
			<div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate"><?php echo esc_html( get_the_title() ); ?></div>
			<?php if ( $version ) : ?>
				<div class="text-xs text-gray-500 dark:text-gray-400">v<?php echo esc_html( $version ); ?></div>
			<?php endif; ?>
		</div>
		<a href="<?php echo esc_url( $download_url ); ?>"
		   class="flex-shrink-0 bg-[color:var(--asp-primary)] hover:opacity-90 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-opacity"
		   rel="nofollow noopener" target="_blank">
			<?php esc_html_e( 'GET', 'aspv5' ); ?>
		</a>
	</div>
</div>
<?php endif; ?>
