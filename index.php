<?php
/**
 * The main template file for mvdrive.
 *
 * This file is used to display a page when no more specific template matches
 * a query.  It outputs a section for the notice, a heading for the latest
 * releases, a grid of movie cards, and a pagination control.  The HTML
 * structure and class names are preserved from the original site so that
 * existing CSS continues to apply.  See the WordPress Loop documentation
 * for details on how posts are queried and displayed【708533204091608†L105-L112】.
 */

get_header();
// Display hero slider on the front page (only on the first page of the main query).
if ( ! is_paged() ) {
    // Query posts for the thumbnail slider
    $hero_query = new WP_Query( array(
        'posts_per_page'      => 10,
        'ignore_sticky_posts' => true,
    ) );

    if ( $hero_query->have_posts() ) {
        echo '<section class="hero-section">';
        echo '<div class="hero-slider">';

        while ( $hero_query->have_posts() ) {
            $hero_query->the_post();
            echo '<a href="' . esc_url( get_permalink() ) . '" class="hero-banner">';
            echo '<div class="hero-image">';
            if ( has_post_thumbnail() ) {
                echo '<img src="' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ) . '" alt="' . esc_attr( get_the_title() ) . '">';
            }
            echo '</div>';
            echo '<div class="hero-overlay"></div>';
            echo '<div class="hero-content">';
            echo '<h1 class="hero-title">' . esc_html( get_the_title() ) . '</h1>';
            echo '</div>'; // hero-content
            echo '</a>'; // hero-banner
        }
        
        echo '</div>'; // hero-slider
        echo '</section>';

        wp_reset_postdata();
    }
}

// Display the site‑wide notice defined in the Customizer.
mvdrive_display_notice();

?>
<section class="content-section">
    <h2 class="section-title">

        <span class="material-text"><?php _e( 'Latest Releases', 'mvdrive' ); ?></span>
    </h2>
    <div class="movies-grid" id="moviesGridMain">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/card', 'movie' ); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </div>
</section>

<?php
// Pagination section.
global $wp_query;
$total_pages = $wp_query->max_num_pages;
if ( $total_pages > 1 ) {
    $current_page = max( 1, get_query_var( 'paged' ) );
    $links = paginate_links( array(
        'current'  => $current_page,
        'total'    => $total_pages,
        'type'     => 'array',
        'prev_next' => true,
        'prev_text' => '',
        'next_text' => '',
    ) );
    ?>
    <nav class="pagination">
        <?php
        // Previous button
        $prev_link = get_previous_posts_link( '' );
        $prev_disabled = $current_page <= 1;
        echo '<button class="page-btn prev-btn' . ( $prev_disabled ? ' disabled' : '' ) . '">';
        echo '<svg viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"></path></svg>';
        echo '</button>';

        echo '<div class="page-numbers">';
        foreach ( $links as $link ) {
            // $link is HTML anchor tag or span; extract class to apply our classes.
            if ( strpos( $link, 'current' ) !== false ) {
                // Current page
                // Remove anchor to wrap with span and active class.
                if ( preg_match( '/>([^<]+)<\/a>/', $link, $matches ) ) {
                    $page_num = $matches[1];
                } else {
                    $page_num = strip_tags( $link );
                }
                echo '<span aria-current="page" class="page-num active">' . esc_html( $page_num ) . '</span>';
            } else {
                // Normal page link
                // Extract href and page number.
                if ( preg_match( '/href="([^"]+)"[^>]*>([^<]+)<\/a>/', $link, $matches ) ) {
                    $href     = $matches[1];
                    $page_num = $matches[2];
                    echo '<a class="page-num" href="' . esc_url( $href ) . '">' . esc_html( $page_num ) . '</a>';
                }
            }
        }
        echo '</div>';

        // Next button
        $next_link = get_next_posts_link( '' );
        $next_disabled = $current_page >= $total_pages;
        $next_href = get_pagenum_link( $current_page + 1 );
        echo '<a href="' . ( $next_disabled ? '#' : esc_url( $next_href ) ) . '" class="page-btn next-btn' . ( $next_disabled ? ' disabled' : '' ) . '">';
        echo '<svg viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"></path></svg>';
        echo '</a>';
        ?>
    </nav>
    <?php
}

get_footer();