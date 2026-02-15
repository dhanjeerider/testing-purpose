<?php
/**
 * Footer template
 *
 * Closes the main page structure and outputs site footer. Classes and IDs
 * are kept simple; you can add your own markup or widget areas here as
 * needed. The theme leaves styling to your custom CSS.
 *
 * @package MXSeries HTML Theme
 */
?>

<footer id="colophon" class="site-footer">
    <div class="site-info">
        &copy; <?php echo date_i18n( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php _e( 'All rights reserved.', 'mxseries-html-theme' ); ?>
    </div><!-- .site-info -->

  <?php
// Footer menu output.
if ( has_nav_menu( 'footer' ) ) {
    echo '<nav class="footer-nav" style="text-align:center; margin-bottom:10px;">';
    wp_nav_menu( array(
        'theme_location' => 'footer',
        'container'      => false,
        'menu_class'     => 'footer-menu',
    ) );
    echo '</nav>';
}
?>

</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>