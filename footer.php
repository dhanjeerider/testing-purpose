</main>

<footer class="site-footer">
    <div class="footer-widgets">
        <div class="container">
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <div class="col-md-3">
                        <?php if (is_active_sidebar('footer-widget-' . $i)) : ?>
                            <?php dynamic_sidebar('footer-widget-' . $i); ?>
                        <?php else : ?>
                            <div class="widget">
                                <?php if ($i == 1) : ?>
                                    <h4 class="widget-title">About</h4>
                                    <p><?php bloginfo('description'); ?></p>
                                <?php elseif ($i == 2) : ?>
                                    <h4 class="widget-title">Categories</h4>
                                    <ul class="footer-links">
                                        <?php
                                        $cats = get_categories(array('number' => 5));
                                        foreach ($cats as $cat) {
                                            echo '<li><a href="' . get_category_link($cat->term_id) . '">' . $cat->name . '</a></li>';
                                        }
                                        ?>
                                    </ul>
                                <?php elseif ($i == 3) : ?>
                                    <h4 class="widget-title">Quick Links</h4>
                                    <ul class="footer-links">
                                        <li><a href="<?php echo home_url('/'); ?>">Home</a></li>
                                        <li><a href="<?php echo home_url('/about'); ?>">About</a></li>
                                        <li><a href="<?php echo home_url('/contact'); ?>">Contact</a></li>
                                    </ul>
                                <?php else : ?>
                                    <h4 class="widget-title">Follow Us</h4>
                                    <div class="social-links">
                                        <a href="#" class="social-link telegram"><i class="fab fa-telegram"></i></a>
                                        <a href="#" class="social-link whatsapp"><i class="fab fa-whatsapp"></i></a>
                                        <a href="#" class="social-link facebook"><i class="fab fa-facebook"></i></a>
                                        <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
