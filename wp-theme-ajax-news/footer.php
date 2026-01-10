</div><!-- #page-content -->

<!-- Footer -->
<footer class="site-footer mt-5 py-5" style="background: var(--card-bg); border-top: 1px solid var(--border-color);">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h3><?php bloginfo('name'); ?></h3>
                <p><?php bloginfo('description'); ?></p>
            </div>
            
            <?php if (is_active_sidebar('footer-1')) : ?>
            <div class="col-md-4">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
            <?php endif; ?>
            
            <div class="col-md-4">
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="#" class="me-3"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#"><i class="fab fa-youtube fa-2x"></i></a>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
