</div><!-- #page-content -->

<!-- Footer -->
<footer class="site-footer mt-4 py-4" style="background: var(--card-bg); border-top: 1px solid var(--border-color);">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h4 style="font-size: 1.1rem; margin-bottom: 12px;"><?php bloginfo('name'); ?></h4>
                <p style="font-size: 13px; margin-bottom: 8px;"><?php bloginfo('description'); ?></p>
            </div>
            
            <div class="col-md-4 mb-3">
                <h5 style="font-size: 1rem; margin-bottom: 12px;">Quick Links</h5>
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container' => false,
                        'menu_class' => 'footer-menu list-unstyled',
                        'depth' => 1,
                        'fallback_cb' => false,
                    ));
                }
                ?>
            </div>
            
            <?php if (is_active_sidebar('footer-1')) : ?>
            <div class="col-md-4 mb-3">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
            <?php else : ?>
            <div class="col-md-4 mb-3">
                <h5 style="font-size: 1rem; margin-bottom: 12px;">Follow Us</h5>
                <div class="social-links">
                    <a href="#" class="me-2" style="font-size: 1.2rem;"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-2" style="font-size: 1.2rem;"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-2" style="font-size: 1.2rem;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="font-size: 1.2rem;"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <hr class="my-3" style="opacity: 0.1;">
        
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0" style="font-size: 12px;">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
