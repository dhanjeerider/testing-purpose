<?php get_header(); ?>

<main class="site-main">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <!-- 404 Error -->
                <div class="error-404" style="padding: 40px 20px;">
                    <h1 class="error-title" style="font-size: 6rem; font-weight: 900; color: var(--primary-color); margin-bottom: 20px;">
                        404
                    </h1>
                    <h2 class="error-subtitle" style="font-size: 1.5rem; font-weight: 600; color: var(--text-color); margin-bottom: 15px;">
                        Page Not Found
                    </h2>
                    <p class="error-message" style="font-size: 1rem; color: var(--secondary-color); margin-bottom: 30px;">
                        Sorry, the page you are looking for doesn't exist or has been moved.
                    </p>
                    
                    <!-- Search Box -->
                    <div class="error-search mb-4">
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="input-group" style="max-width: 500px; margin: 0 auto;">
                                <input type="search" class="form-control" placeholder="Search..." name="s" style="padding: 12px 20px; font-size: 14px;">
                                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 14px;">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="error-actions">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg" style="margin: 10px;">
                            <i class="fas fa-home"></i> Go to Homepage
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg" style="margin: 10px;">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="recent-posts-404 mt-5 text-start">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 20px; color: var(--text-color);">
                        Recent Posts
                    </h3>
                    <div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        <?php
                        $recent_posts = new WP_Query(array(
                            'posts_per_page' => 3,
                            'orderby' => 'date',
                        ));
                        if ($recent_posts->have_posts()) :
                            while ($recent_posts->have_posts()) : $recent_posts->the_post();
                                get_template_part('template-parts/content', 'loop');
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
