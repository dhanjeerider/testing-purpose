<?php
// template-parts/single/app-single.php
$post_id        = get_the_ID();
$icon_url       = appstorepro_get_app_meta( $post_id, '_app_icon_url' );
$hero_img       = appstorepro_get_app_meta( $post_id, '_app_hero_image_url' );
$version        = appstorepro_get_app_meta( $post_id, '_app_version' );
$size           = appstorepro_get_app_meta( $post_id, '_app_size' );
$developer      = appstorepro_get_app_meta( $post_id, '_app_developer' );
$rating_raw     = appstorepro_get_app_meta( $post_id, '_app_rating' );
$rating         = $rating_raw ? number_format( (float) $rating_raw, 1, '.', '' ) : '';
$android_ver    = appstorepro_get_app_meta( $post_id, '_app_android_version' );
$download_url   = appstorepro_get_app_meta( $post_id, '_app_download_url' );
$play_store_url = appstorepro_get_app_meta( $post_id, '_app_play_store_url' );
$telegram_url   = appstorepro_get_app_meta( $post_id, '_app_telegram_url' );
$tg_members     = appstorepro_get_app_meta( $post_id, '_app_telegram_members' );
$youtube_url    = appstorepro_get_app_meta( $post_id, '_app_youtube_url' );
$is_mod         = appstorepro_get_app_meta( $post_id, '_app_is_mod' );
$mod_info       = appstorepro_get_app_meta( $post_id, '_app_mod_info' );
$downloads_raw  = appstorepro_get_app_meta( $post_id, '_app_downloads' );
$downloads      = appstorepro_format_downloads( $downloads_raw );
$screenshots_raw = appstorepro_get_app_meta( $post_id, '_app_screenshots' );
$screenshots    = $screenshots_raw ? array_filter( array_map( 'trim', explode( "\n", $screenshots_raw ) ) ) : [];
$terms          = get_the_terms( $post_id, 'app-category' );
$cat_name       = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cat_link       = ( $terms && ! is_wp_error( $terms ) ) ? get_term_link( $terms[0] ) : '';

// Effective icon
$icon_src = $icon_url ?: ( has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'app-icon' ) : '' );
?>

<!-- Hero Banner -->
<div class="sa-hero">
	<?php if ( $hero_img || ( $screenshots && count( $screenshots ) > 0 ) ) :
		$bg = $hero_img ?: $screenshots[0];
		?>
		<div class="sa-hero-bg" style="background-image:url('<?= esc_url( $bg ); ?>')" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="sa-hero-overlay" aria-hidden="true"></div>
</div>

<!-- App Info -->
<div class="sa-info-wrap">
	<div class="sa-info container">
		<div class="sa-app-icon">
			<?php if ( $icon_src ) : ?>
				<img src="<?= esc_url( $icon_src ); ?>" alt="<?= esc_attr( get_the_title() ); ?>" width="80" height="80" loading="eager">
			<?php else : ?>
				<div class="app-icon-placeholder sa-icon-placeholder"><?= esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></div>
			<?php endif; ?>
		</div>
		<div class="sa-meta">
			<h1 class="sa-app-title"><?= esc_html( get_the_title() ); ?></h1>
			<?php if ( $developer ) : ?>
				<div class="sa-developer"><?= esc_html( $developer ); ?></div>
			<?php endif; ?>
			<div class="sa-pills">
				<?php if ( $rating ) : ?>
					<span class="sa-pill sa-pill-rating">
						<?= esc_html( $rating ); ?> ★
					</span>
				<?php endif; ?>
				<?php if ( $cat_name && $cat_link ) : ?>
					<a href="<?= esc_url( $cat_link ); ?>" class="sa-pill sa-pill-cat"><?= esc_html( $cat_name ); ?></a>
				<?php endif; ?>
				<?php if ( $size ) : ?>
					<span class="sa-pill sa-pill-size"><?= esc_html( $size ); ?></span>
				<?php endif; ?>
				<?php if ( $downloads ) : ?>
					<span class="sa-pill sa-pill-downloads"><i class="bx bx-download"></i> <?= esc_html( $downloads ); ?></span>
				<?php endif; ?>
				<?php if ( $is_mod ) : ?>
					<span class="sa-pill sa-pill-mod">MOD</span>
				<?php endif; ?>
				<?php if ( $android_ver ) : ?>
					<span class="sa-pill sa-pill-android">Android <?= esc_html( $android_ver ); ?>+</span>
				<?php endif; ?>
			</div>
			<div class="sa-actions">
				<?php if ( $download_url ) : ?>
					<a href="<?= esc_url( $download_url ); ?>" class="sa-btn-dl" rel="nofollow noopener" target="_blank">
						<i class="bx bxs-download"></i>
						<?php esc_html_e( 'Download APK', 'appstorepro' ); ?>
					</a>
				<?php endif; ?>
				<button class="sa-btn-share btn-icon" id="sa-share-btn" aria-label="<?php esc_attr_e( 'Share this app', 'appstorepro' ); ?>">
					<i class="bx bx-share-alt"></i>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Screenshots Carousel -->
<?php if ( $screenshots ) : ?>
<section class="sa-sc-section container pas-reveal">
	<h2 class="sa-section-title"><?php esc_html_e( 'Screenshots', 'appstorepro' ); ?></h2>
	<div class="sa-sc-wrap">
		<div class="sa-sc-scroll" id="sa-sc-scroll">
			<?php foreach ( $screenshots as $i => $sc_url ) : ?>
				<div class="sa-sc-item">
					<img src="<?= esc_url( $sc_url ); ?>" alt="<?php printf( esc_attr__( '%s screenshot %d', 'appstorepro' ), esc_attr( get_the_title() ), $i + 1 ); ?>" loading="lazy">
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $screenshots ) > 1 ) : ?>
			<div class="sa-sc-dots" id="sa-sc-dots">
				<?php foreach ( $screenshots as $i => $sc_url ) : ?>
					<button class="sa-sc-dot<?= $i === 0 ? ' active' : ''; ?>" data-index="<?= esc_attr( $i ); ?>" aria-label="<?php printf( esc_attr__( 'Screenshot %d', 'appstorepro' ), $i + 1 ); ?>"></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<!-- Telegram Join Card -->
<?php if ( $telegram_url ) : ?>
<section class="sa-tg-section container pas-reveal">
	<div class="sa-tg-card">
		<div class="sa-tg-icon">
			<i class="bx bxl-telegram"></i>
		</div>
		<div class="sa-tg-info">
			<div class="sa-tg-title"><?php esc_html_e( 'Join our Telegram', 'appstorepro' ); ?></div>
			<?php if ( $tg_members ) : ?>
				<div class="sa-tg-members"><?= esc_html( $tg_members ); ?> <?php esc_html_e( 'members', 'appstorepro' ); ?></div>
			<?php endif; ?>
		</div>
		<a href="<?= esc_url( $telegram_url ); ?>" class="sa-tg-btn" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Join', 'appstorepro' ); ?>
		</a>
	</div>
</section>
<?php endif; ?>

<!-- Download Section -->
<?php if ( $download_url ) : ?>
<section class="sa-dl-section container pas-reveal">
	<a href="<?= esc_url( $download_url ); ?>" class="sa-btn-dl sa-btn-dl-lg" rel="nofollow noopener" target="_blank">
		<i class="bx bxs-download"></i>
		<?php esc_html_e( 'Download APK', 'appstorepro' ); ?>
		<?php if ( $size ) : ?>
			<span class="sa-btn-dl-size"><?= esc_html( $size ); ?></span>
		<?php endif; ?>
	</a>
	<?php if ( $play_store_url ) : ?>
		<a href="<?= esc_url( $play_store_url ); ?>" class="sa-btn-playstore" target="_blank" rel="noopener noreferrer">
			<i class="bx bxl-play-store"></i>
			<?php esc_html_e( 'Get on Play Store', 'appstorepro' ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $is_mod && $mod_info ) : ?>
		<div class="sa-mod-info">
			<span class="badge-mod">MOD</span>
			<?= esc_html( $mod_info ); ?>
		</div>
	<?php endif; ?>
</section>
<?php endif; ?>

<!-- App Details Table -->
<section class="sa-details-section container pas-reveal">
	<h2 class="sa-section-title"><?php esc_html_e( 'App Details', 'appstorepro' ); ?></h2>
	<div class="sa-details-table">
		<?php if ( $version ) : ?>
			<div class="sa-details-row">
				<span class="sa-details-key"><?php esc_html_e( 'Version', 'appstorepro' ); ?></span>
				<span class="sa-details-val"><?= esc_html( $version ); ?></span>
			</div>
		<?php endif; ?>
		<?php if ( $size ) : ?>
			<div class="sa-details-row">
				<span class="sa-details-key"><?php esc_html_e( 'Size', 'appstorepro' ); ?></span>
				<span class="sa-details-val"><?= esc_html( $size ); ?></span>
			</div>
		<?php endif; ?>
		<?php if ( $developer ) : ?>
			<div class="sa-details-row">
				<span class="sa-details-key"><?php esc_html_e( 'Developer', 'appstorepro' ); ?></span>
				<span class="sa-details-val"><?= esc_html( $developer ); ?></span>
			</div>
		<?php endif; ?>
		<?php if ( $downloads ) : ?>
			<div class="sa-details-row">
				<span class="sa-details-key"><?php esc_html_e( 'Downloads', 'appstorepro' ); ?></span>
				<span class="sa-details-val"><?= esc_html( $downloads ); ?></span>
			</div>
		<?php endif; ?>
		<?php if ( $android_ver ) : ?>
			<div class="sa-details-row">
				<span class="sa-details-key"><?php esc_html_e( 'Requires Android', 'appstorepro' ); ?></span>
				<span class="sa-details-val"><?= esc_html( $android_ver ); ?>+</span>
			</div>
		<?php endif; ?>
		<?php if ( $cat_name ) : ?>
			<div class="sa-details-row">
				<span class="sa-details-key"><?php esc_html_e( 'Category', 'appstorepro' ); ?></span>
				<span class="sa-details-val">
					<?php if ( $cat_link ) : ?>
						<a href="<?= esc_url( $cat_link ); ?>"><?= esc_html( $cat_name ); ?></a>
					<?php else : ?>
						<?= esc_html( $cat_name ); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>
		<div class="sa-details-row">
			<span class="sa-details-key"><?php esc_html_e( 'Updated', 'appstorepro' ); ?></span>
			<span class="sa-details-val"><?= esc_html( get_the_modified_date() ); ?></span>
		</div>
	</div>
</section>

<!-- YouTube Tutorial Accordion -->
<?php if ( $youtube_url ) : ?>
<section class="sa-tut-section container pas-reveal">
	<div class="sa-tut-accordion">
		<button class="sa-tut-tog" aria-expanded="false">
			<span>
				<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
				<?php esc_html_e( 'Watch Tutorial', 'appstorepro' ); ?>
			</span>
			<svg class="sa-tut-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
				<polyline points="6 9 12 15 18 9"/>
			</svg>
		</button>
		<div class="sa-tut-body" id="sa-tut-body">
			<div class="sa-tut-embed" data-url="<?= esc_attr( $youtube_url ); ?>">
				<?php
				// Extract YouTube video ID
				$yt_id = '';
				if ( preg_match( '/[?&]v=([a-zA-Z0-9_-]{11})/', $youtube_url, $m ) ) {
					$yt_id = $m[1];
				} elseif ( preg_match( '/youtu\.be\/([a-zA-Z0-9_-]{11})/', $youtube_url, $m ) ) {
					$yt_id = $m[1];
				}
				?>
				<?php if ( $yt_id ) : ?>
					<div class="sa-tut-thumb" id="sa-tut-thumb">
						<img src="https://img.youtube.com/vi/<?= esc_attr( $yt_id ); ?>/hqdefault.jpg" alt="<?php esc_attr_e( 'Tutorial thumbnail', 'appstorepro' ); ?>" loading="lazy" width="480" height="270">
						<button class="sa-tut-play" data-ytid="<?= esc_attr( $yt_id ); ?>" aria-label="<?php esc_attr_e( 'Play tutorial', 'appstorepro' ); ?>">
							<svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm-2 16.5v-9l7 4.5-7 4.5z"/></svg>
						</button>
					</div>
				<?php else : ?>
					<a href="<?= esc_url( $youtube_url ); ?>" target="_blank" rel="noopener noreferrer" class="sa-tut-external">
						<?php esc_html_e( 'Watch on YouTube', 'appstorepro' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- App Content -->
<?php if ( get_the_content() ) : ?>
<section class="sa-content container pas-reveal">
	<h2 class="sa-section-title"><?php esc_html_e( 'About', 'appstorepro' ); ?></h2>
	<div class="entry-content" id="sa-entry-content">
		<?php the_content(); ?>
	</div>
	<button class="sa-show-more" id="sa-show-more" type="button" data-show-label="<?php esc_attr_e( 'Show more', 'appstorepro' ); ?>" data-hide-label="<?php esc_attr_e( 'Show less', 'appstorepro' ); ?>">
		<?php esc_html_e( 'Show more', 'appstorepro' ); ?>
	</button>
</section>
<?php endif; ?>

<!-- Author Card -->
<section class="sa-author-section container pas-reveal">
	<div class="sa-author-card">
		<div class="sa-author-avatar">
			<?= get_avatar( get_the_author_meta( 'ID' ), 48, '', get_the_author(), [ 'class' => 'sa-author-img' ] ); ?>
		</div>
		<div class="sa-author-info">
			<div class="sa-author-label"><?php esc_html_e( 'Published by', 'appstorepro' ); ?></div>
			<a href="<?= esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="sa-author-name"><?= esc_html( get_the_author() ); ?></a>
		</div>
		<div class="sa-author-date"><?= esc_html( get_the_date() ); ?></div>
	</div>
</section>

<!-- Related Apps -->
<?php
$related_args = [
	'post_type'      => 'app',
	'posts_per_page' => 4,
	'post__not_in'   => [ $post_id ],
	'orderby'        => 'rand',
	'post_status'    => 'publish',
];
if ( $terms && ! is_wp_error( $terms ) ) {
	$related_args['tax_query'] = [
		[
			'taxonomy' => 'app-category',
			'field'    => 'term_id',
			'terms'    => wp_list_pluck( $terms, 'term_id' ),
		],
	];
}
$related_query = new WP_Query( $related_args );
?>
<?php if ( $related_query->have_posts() ) : ?>
<section class="sa-related container pas-reveal">
	<h2 class="sa-section-title"><?php esc_html_e( 'Related Apps', 'appstorepro' ); ?></h2>
	<div class="app-grid">
		<?php
		while ( $related_query->have_posts() ) :
			$related_query->the_post();
			get_template_part( 'template-parts/content/content', 'app', [ 'layout' => 'grid' ] );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
<?php endif; ?>

<!-- Sticky Bottom Bar -->
<div class="sa-sticky" id="sa-sticky">
	<div class="sa-sticky-inner container">
		<div class="sa-sticky-icon">
			<?php if ( $icon_src ) : ?>
				<img src="<?= esc_url( $icon_src ); ?>" alt="<?= esc_attr( get_the_title() ); ?>" width="40" height="40" loading="lazy">
			<?php endif; ?>
		</div>
		<div class="sa-sticky-info">
			<div class="sa-sticky-name"><?= esc_html( get_the_title() ); ?></div>
			<?php if ( $version ) : ?>
				<div class="sa-sticky-ver">v<?= esc_html( $version ); ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $download_url ) : ?>
			<a href="<?= esc_url( $download_url ); ?>" class="sa-sticky-btn" rel="nofollow noopener" target="_blank">
				<?php esc_html_e( 'GET', 'appstorepro' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
