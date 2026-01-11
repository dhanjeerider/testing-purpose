<?php get_header(); ?>

<div class="container my-5">
    <div class="error-404-page text-center py-5">
        <div class="error-content">
            <div class="error-code mb-4">
                <h1 class="display-1 fw-bold">404</h1>
            </div>
            
            <div class="error-message mb-4">
                <h2 class="h3 mb-3">Oops! Page Not Found</h2>
                <p class="lead text-muted">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            </div>
            
            <!-- Search Form -->
            <div class="error-search mx-auto mb-5" style="max-width: 600px;">
                <h5 class="mb-3">Try searching for what you need:</h5>
                <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                    <div class="input-group input-group-lg">
                        <input type="search" class="form-control" placeholder="Search..." name="s" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Quick Links -->
            <div class="error-actions mb-5">
                <a href="<?php echo home_url('/'); ?>" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-home"></i> Go to Homepage
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
            
            <!-- Recent Posts -->
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 3,
                'post_status' => 'publish'
            ));
            if ($recent_posts) :
            ?>
                <div class="recent-posts-section">
                    <h4 class="mb-4">Or check out our recent posts:</h4>
                    <div class="row justify-content-center">
                        <?php foreach ($recent_posts as $post) : ?>
                            <div class="col-md-4 mb-3">
                                <div class="material-card h-100">
                                    <?php if (has_post_thumbnail($post['ID'])) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php echo get_permalink($post['ID']); ?>">
                                                <?php echo get_the_post_thumbnail($post['ID'], 'medium', array('class' => 'img-fluid')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-content p-3">
                                        <h5 class="post-title h6">
                                            <a href="<?php echo get_permalink($post['ID']); ?>"><?php echo $post['post_title']; ?></a>
                                        </h5>
                                        <div class="post-meta small text-muted">
                                            <i class="fas fa-calendar"></i> <?php echo get_the_date('', $post['ID']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php wp_reset_query(); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Social Follow -->
            <div class="social-follow mt-5">
                <h5 class="mb-3">Stay Connected:</h5>
                <div class="social-buttons">
                    <a href="#" class="btn btn-telegram me-2" target="_blank">
                        <i class="fab fa-telegram"></i> Telegram
                    </a>
                    <a href="#" class="btn btn-whatsapp me-2" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="#" class="btn btn-facebook me-2" target="_blank">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="#" class="btn btn-google-news" target="_blank">
                        <i class="fab fa-google"></i> Google News
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
