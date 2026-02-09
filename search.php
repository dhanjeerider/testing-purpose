<?php
/**
 * Template for displaying search results.
 *
 * This template mirrors the archive and index layouts but includes a
 * heading that displays the search query.  Results are displayed in the
 * movie grid, and pagination is provided when necessary.
 */

get_header();

mvdrive_display_notice();
?>

<section class="content-section">
    <h2 class="section-title">
        <i class="material-icons"></i>
        <span class="material-text">
            <?php printf( __( 'Search results for: %s', 'mvdrive' ), '<em>' . esc_html( get_search_query() ) . '</em>' ); ?>
        </span>
    </h2>
    <div class="movies-grid" id="moviesGridMain">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/card', 'movie' ); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="no-results">
                <?php _e( 'Sorry, no results found.', 'mvdrive' ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php
// Pagination for search results.
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
        $prev_disabled = $current_page <= 1;
        echo '<button class="page-btn prev-btn' . ( $prev_disabled ? ' disabled' : '' ) . '">';
        echo '<svg viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"></path></svg>';
        echo '</button>';
        echo '<div class="page-numbers">';
        foreach ( $links as $link ) {
            if ( strpos( $link, 'current' ) !== false ) {
                if ( preg_match( '/>([^<]+)<\/a>/', $link, $matches ) ) {
                    $page_num = $matches[1];
                } else {
                    $page_num = strip_tags( $link );
                }
                echo '<span aria-current="page" class="page-num active">' . esc_html( $page_num ) . '</span>';
            } else {
                if ( preg_match( '/href="([^"]+)"[^>]*>([^<]+)<\/a>/', $link, $matches ) ) {
                    $href     = $matches[1];
                    $page_num = $matches[2];
                    echo '<a class="page-num" href="' . esc_url( $href ) . '">' . esc_html( $page_num ) . '</a>';
                }
            }
        }
        echo '</div>';
        // Next button
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