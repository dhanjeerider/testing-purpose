<?php
/**
 * The footer for the mvdrive theme.
 *
 * This template closes the main content area and outputs the footer markup.
 * The year and site name are generated dynamically.  As with the header,
 * class names mirror those of the original MoviesDrive design so that
 * existing CSS continues to apply.  The domain is replaced with a call
 * to home_url() to avoid hard‑coding external site names.
 */
?>

<footer class="footer">
    <div class="footer-content">
        <p style="text-align: center; padding: 15px 0;">
            <?php echo date( 'Y' ); ?> ©
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
            </a>
            <?php _e( '| All Rights Reserved.', 'mvdrive' ); ?>
        </p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>