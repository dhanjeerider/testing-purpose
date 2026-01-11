</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <?php for ($i = 1; $i <= 4; $i++) {
                if (is_active_sidebar('footer-' . $i)) {
                    echo '<div class="footer-widget">';
                    dynamic_sidebar('footer-' . $i);
                    echo '</div>';
                }
            } ?>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
