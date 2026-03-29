<?php
// template-parts/content/content-app.php
// Determine layout: 'grid' by default; can be 'list' or 'banner'.
$layout      = $args['layout'] ?? 'grid';
$post_id     = get_the_ID();
$icon_url    = appstorepro_get_app_meta( $post_id, '_app_icon_url' );
$rating      = appstorepro_get_app_meta( $post_id, '_app_rating' );
$rating      = $rating ? number_format( (float) $rating, 1, '.', '' ) : '';
$size        = appstorepro_get_app_meta( $post_id, '_app_size' );
$version     = appstorepro_get_app_meta( $post_id, '_app_version' );
$is_mod      = appstorepro_get_app_meta( $post_id, '_app_is_mod' );
$developer   = appstorepro_get_app_meta( $post_id, '_app_developer' );
$downloads   = appstorepro_format_downloads( appstorepro_get_app_meta( $post_id, '_app_downloads' ) );
$terms       = get_the_terms( $post_id, 'app-category' );
$cat_name    = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cat_link    = ( $terms && ! is_wp_error( $terms ) ) ? get_term_link( $terms[0] ) : '';

// Banner layout
if ( $layout === 'banner' ) {
    // Determine the hero image: use `_app_hero_image_url` if set, otherwise fall back to the icon.
    $hero_url = appstorepro_get_app_meta( $post_id, '_app_hero_image_url' );
    if ( ! $hero_url ) {
        $hero_url = $icon_url;
    }
    ?>
    <article class="app-card-banner pas-reveal" id="post-<?php the_ID(); ?>" <?php post_class( 'app-card-banner' ); ?>>
        <a href="<?= esc_url( get_the_permalink() ); ?>" class="app-banner-link" aria-label="<?= esc_attr( get_the_title() ); ?>">
            <?php if ( $hero_url ) : ?>
                <div class="app-banner-image">
                    <img src="<?= esc_url( $hero_url ); ?>" alt="<?= esc_attr( get_the_title() ); ?>" loading="lazy" width="320" height="180" />
                </div>
            <?php endif; ?>
            <div class="app-banner-content">
                <h3 class="app-banner-title"><?= esc_html( get_the_title() ); ?></h3>
                <?php if ( $developer ) : ?>
                    <div class="app-banner-dev"><?= esc_html( $developer ); ?></div>
                <?php elseif ( $cat_name && $cat_link ) : ?>
                    <div class="app-banner-dev"><a href="<?= esc_url( $cat_link ); ?>"><?= esc_html( $cat_name ); ?></a></div>
                <?php endif; ?>
                <div class="app-banner-meta">
                    <?php if ( $rating ) : ?>
                        <span class="app-banner-rating"><?= esc_html( $rating ); ?>&#9733;</span>
                    <?php endif; ?>
                    <?php if ( $size ) : ?>
                        <span class="app-banner-size"><?= esc_html( $size ); ?></span>
                    <?php endif; ?>
                    <?php if ( $downloads ) : ?>
                        <span class="app-banner-downloads"><i class="bx bx-download"></i> <?= esc_html( $downloads ); ?></span>
                    <?php endif; ?>
                    <?php if ( $is_mod ) : ?>
                        <span class="app-banner-mod">MOD</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </article>
    <?php
    // End banner layout

// List layout (row)
} elseif ( $layout === 'list' ) {
    ?>
    <article class="app-row pas-reveal" id="post-<?php the_ID(); ?>" <?php post_class( 'app-row' ); ?>>
        <a href="<?= esc_url( get_the_permalink() ); ?>" class="app-row-link" aria-label="<?= esc_attr( get_the_title() ); ?>">
            <div class="app-row-icon">
                <?php if ( $icon_url ) : ?>
                    <img src="<?= esc_url( $icon_url ); ?>" alt="<?= esc_attr( get_the_title() ); ?>" loading="lazy" width="56" height="56" />
                <?php elseif ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'app-icon', [ 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
                <?php else : ?>
                    <div class="app-icon-placeholder"><?= esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></div>
                <?php endif; ?>
            </div>
            <div class="app-row-info">
                <h3 class="app-row-title"><?= esc_html( get_the_title() ); ?></h3>
                <?php if ( $developer ) : ?>
                    <div class="app-row-dev"><?= esc_html( $developer ); ?></div>
                <?php endif; ?>
                <div class="app-row-meta">
                    <?php if ( is_sticky() ) : ?>
                        <span class="badge-hot"><?php esc_html_e( 'Hot', 'appstorepro' ); ?></span>
                    <?php endif; ?>
                    <?php if ( $rating ) : ?>
                        <span class="app-row-rating"><?= esc_html( $rating ); ?> &#9733;</span>
                    <?php endif; ?>
                    <?php if ( $size ) : ?>
                        <span class="app-row-size"><?= esc_html( $size ); ?></span>
                    <?php endif; ?>
                    <?php if ( $downloads ) : ?>
                        <span class="app-row-downloads"><i class="bx bx-download"></i> <?= esc_html( $downloads ); ?></span>
                    <?php endif; ?>
                    <?php if ( $is_mod ) : ?>
                        <span class="badge-mod">MOD</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </article>
    <?php
    // End list layout

// Default grid card layout
} else {
    ?>
    <article class="app-card pas-reveal" id="post-<?php the_ID(); ?>" <?php post_class( 'app-card' ); ?>>
        <a href="<?= esc_url( get_the_permalink() ); ?>" class="app-card-link" aria-label="<?= esc_attr( get_the_title() ); ?>">
            <div class="app-card-icon">
                <?php if ( $icon_url ) : ?>
                    <img src="<?= esc_url( $icon_url ); ?>" alt="<?= esc_attr( get_the_title() ); ?>" loading="lazy" width="72" height="72" />
                <?php elseif ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'app-icon', [ 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
                <?php else : ?>
                    <div class="app-icon-placeholder"><?= esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></div>
                <?php endif; ?>
                <div class="app-card-badges">
                    <?php if ( is_sticky() ) : ?>
                        <span class="badge-hot"><?php esc_html_e( 'Hot', 'appstorepro' ); ?></span>
                    <?php endif; ?>
                    <?php if ( $is_mod ) : ?>
                        <span class="badge-mod app-card-mod-badge">MOD</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="app-card-info">
                <h3 class="app-card-title"><?= esc_html( get_the_title() ); ?></h3>
                <?php if ( $cat_name && $cat_link ) : ?>
                    <div class="app-card-cat"><?= esc_html( $cat_name ); ?></div>
                <?php elseif ( $developer ) : ?>
                    <div class="app-card-cat"><?= esc_html( $developer ); ?></div>
                <?php endif; ?>
                <div class="app-card-meta">
                    <?php if ( $rating ) : ?>
                        <span class="app-card-rating"><span class="star-val"><?= esc_html( $rating ); ?></span><span class="star-icon">&#9733;</span></span>
                    <?php endif; ?>
                    <?php if ( $size ) : ?>
                        <span class="app-card-size"><?= esc_html( $size ); ?></span>
                    <?php endif; ?>
                    <?php if ( $downloads ) : ?>
                        <span class="app-card-downloads"><i class="bx bx-download"></i> <?= esc_html( $downloads ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </article>
    <?php
}
?>
