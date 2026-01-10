<?php get_header(); ?>

<!-- Hero Section with Search -->
<?php if (get_theme_mod('show_hero', true) && (is_home() || is_front_page())) : ?>
<section class="hero-section" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%); padding: 40px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="hero-title text-white mb-2" style="font-size: 2rem; font-weight: 700;">
                    <?php echo esc_html(get_theme_mod('hero_title', 'Breaking News & Latest Updates')); ?>
                </h1>
                <p class="hero-subtitle text-white mb-4" style="font-size: 1rem; opacity: 0.9;">
                    <?php echo esc_html(get_theme_mod('hero_subtitle', 'Stay informed with real-time news coverage')); ?>
                </p>
                
                <!-- Hero Search Box -->
                <div class="hero-search-box">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-group" style="max-width: 600px; margin: 0 auto;">
                            <input type="search" class="form-control" placeholder="Search news, articles, topics..." name="s" style="padding: 12px 20px; font-size: 14px; border: none; border-radius: 50px 0 0 50px;">
                            <button type="submit" class="btn btn-light" style="padding: 12px 30px; font-size: 14px; border-radius: 0 50px 50px 0; font-weight: 600;">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<main class="site-main">
    <div class="container py-3">
        
        <!-- Story Posts Section -->
        <div class="story-posts-section mb-3">
            <div class="story-posts-container" style="gap: 12px; padding: 15px 0;">
                <?php
                $story_posts = new WP_Query(array(
                    'posts_per_page' => 10,
                    'orderby' => 'date',
                ));
                if ($story_posts->have_posts()) :
                    while ($story_posts->have_posts()) : $story_posts->the_post();
                ?>
                    <a href="<?php the_permalink(); ?>" class="story-item ajax-link">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail', array('class' => 'story-avatar')); ?>
                        <?php else : ?>
                            <img src="https://via.placeholder.com/80" class="story-avatar" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                        <div class="story-title"><?php echo wp_trim_words(get_the_title(), 3); ?></div>
                    </a>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div id="posts-container">
                    <div class="row" id="main-content">
                        <?php if (have_posts()) : $counter = 0; ?>
                            <?php while (have_posts()) : the_post(); $counter++; ?>
                                <?php
                                // First post - Big featured layout
                                if ($counter === 1) {
                                    get_template_part('template-parts/content', 'big');
                                }
                                // Posts 2-5 - Grid layout
                                elseif ($counter <= 5) {
                                    get_template_part('template-parts/content', 'grid');
                                }
                                // Posts 6-10 - List layout
                                elseif ($counter <= 10) {
                                    echo '<div class="col-12">';
                                    get_template_part('template-parts/content', 'list');
                                    echo '</div>';
                                }
                                // Remaining - Flex layout
                                else {
                                    echo '<div class="col-12">';
                                    get_template_part('template-parts/content', 'flex');
                                    echo '</div>';
                                }
                                ?>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="col-12">
                                <p>No posts found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Load More Button -->
                <?php if (have_posts()) : ?>
                    <button id="loadMoreBtn" class="btn btn-primary" data-layout="grid" data-container="#main-content">
                        Load More <i class="fas fa-chevron-down"></i>
                    </button>
                <?php endif; ?>
                
                <!-- Recent Posts Section -->
                <div class="recent-posts-section mt-4">
                    <h3 class="mb-3" style="font-size: 1.2rem;">Recent Posts</h3>
                    <div class="recent-posts-widget">
                        <?php
                        $recent = new WP_Query(array(
                            'posts_per_page' => 5,
                            'orderby' => 'date',
                            'post__not_in' => array(get_the_ID()),
                        ));
                        if ($recent->have_posts()) :
                            while ($recent->have_posts()) : $recent->the_post();
                        ?>
                            <div class="recent-post-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="ajax-link">
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'recent-post-thumb')); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="recent-post-content flex-grow-1">
                                    <h6>
                                        <a href="<?php the_permalink(); ?>" class="ajax-link text-decoration-none" style="color: var(--text-color);">
                                            <?php the_title(); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                                    </small>
                                </div>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Trending Posts -->
                <div class="trending-section mb-3">
                    <h3 class="mb-3" style="font-size: 1.2rem;">Trending Now</h3>
                    <?php
                    $trending = new WP_Query(array(
                        'posts_per_page' => 5,
                        'orderby' => 'rand',
                    ));
                    if ($trending->have_posts()) :
                        global $trending_counter;
                        $trending_counter = 1;
                        while ($trending->have_posts()) : $trending->the_post();
                            get_template_part('template-parts/content', 'trending');
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
                
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>

