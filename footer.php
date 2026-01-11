</main>
        
        <!-- Footer -->
        <footer class="site-footer">
            <?php if (is_active_sidebar('footer-widget-1') || is_active_sidebar('footer-widget-2') || is_active_sidebar('footer-widget-3') || is_active_sidebar('footer-widget-4')) : ?>
                <div class="footer-widgets">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-4">
                                <?php if (is_active_sidebar('footer-widget-1')) : ?>
                                    <?php dynamic_sidebar('footer-widget-1'); ?>
                                <?php else : ?>
                                    <div class="widget">
                                        <h4 class="widget-title">About Us</h4>
                                        <p><?php bloginfo('description'); ?></p>
                                        <div class="social-links">
                                            <a href="#" class="social-link telegram"><i class="fab fa-telegram"></i></a>
                                            <a href="#" class="social-link whatsapp"><i class="fab fa-whatsapp"></i></a>
                                            <a href="#" class="social-link facebook"><i class="fab fa-facebook"></i></a>
                                            <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-4">
                                <?php if (is_active_sidebar('footer-widget-2')) : ?>
                                    <?php dynamic_sidebar('footer-widget-2'); ?>
                                <?php else : ?>
                                    <div class="widget">
                                        <h4 class="widget-title">Categories</h4>
                                        <ul class="footer-category-list">
                                            <?php
                                            $categories = get_categories(array('number' => 6));
                                            foreach ($categories as $cat) :
                                            ?>
                                                <li>
                                                    <a href="<?php echo get_category_link($cat->term_id); ?>">
                                                        <i class="fas fa-angle-right"></i> <?php echo $cat->name; ?>
                                                        <span class="count">(<?php echo $cat->count; ?>)</span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-4">
                                <?php if (is_active_sidebar('footer-widget-3')) : ?>
                                    <?php dynamic_sidebar('footer-widget-3'); ?>
                                <?php else : ?>
                                    <div class="widget">
                                        <h4 class="widget-title">Quick Links</h4>
                                        <?php
                                        wp_nav_menu(array(
                                            'theme_location' => 'footer',
                                            'container' => false,
                                            'menu_class' => 'footer-menu-list',
                                            'fallback_cb' => 'footer_fallback_menu'
                                        ));
                                        
                                        function footer_fallback_menu() {
                                            echo '<ul class="footer-menu-list">';
                                            echo '<li><a href="' . home_url('/') . '"><i class="fas fa-angle-right"></i> Home</a></li>';
                                            echo '<li><a href="' . home_url('/about') . '"><i class="fas fa-angle-right"></i> About</a></li>';
                                            echo '<li><a href="' . home_url('/contact') . '"><i class="fas fa-angle-right"></i> Contact</a></li>';
                                            echo '<li><a href="' . home_url('/privacy-policy') . '"><i class="fas fa-angle-right"></i> Privacy Policy</a></li>';
                                            echo '</ul>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-4">
                                <?php if (is_active_sidebar('footer-widget-4')) : ?>
                                    <?php dynamic_sidebar('footer-widget-4'); ?>
                                <?php else : ?>
                                    <div class="widget">
                                        <h4 class="widget-title">Share & Subscribe</h4>
                                        <p class="mb-3">Stay updated with our latest news</p>
                                        
                                        <div class="footer-share-buttons">
                                            <a href="#" target="_blank" class="footer-share-btn telegram">
                                                <i class="fab fa-telegram"></i> Telegram Channel
                                            </a>
                                            <a href="#" target="_blank" class="footer-share-btn whatsapp">
                                                <i class="fab fa-whatsapp"></i> WhatsApp Group
                                            </a>
                                            <a href="#" target="_blank" class="footer-share-btn google">
                                                <i class="fab fa-google"></i> Google News
                                            </a>
                                            <a href="#" target="_blank" class="footer-share-btn youtube">
                                                <i class="fab fa-youtube"></i> YouTube Channel
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="footer-bottom">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-start">
                            <p class="copyright mb-0">
                                &copy; <?php echo date('Y'); ?> <strong><?php bloginfo('name'); ?></strong>. All rights reserved.
                            </p>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <p class="credits mb-0">
                                Designed with <i class="fas fa-heart text-danger"></i> for News
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        
        <?php wp_footer(); ?>
    </body>
</html>
