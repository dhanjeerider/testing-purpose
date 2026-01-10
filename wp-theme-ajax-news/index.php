<?php get_header(); ?>

<main class="site-main">
    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div id="main-content">
                    <?php if (have_posts()) : ?>
                        <div class="row">
                            <?php while (have_posts()) : the_post(); ?>
                                <div class="col-md-6">
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('news-card'); ?>>
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>" class="ajax-link">
                                                <?php the_post_thumbnail('medium_large'); ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <div class="news-card-body">
                                            <div class="meta mb-2">
                                                <span class="text-muted">
                                                    <i class="far fa-calendar"></i> 
                                                    <?php echo get_the_date(); ?>
                                                </span>
                                                <span class="text-muted ms-3">
                                                    <i class="far fa-user"></i> 
                                                    <?php the_author(); ?>
                                                </span>
                                            </div>
                                            
                                            <h2 class="news-card-title">
                                                <a href="<?php the_permalink(); ?>" class="ajax-link">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h2>
                                            
                                            <div class="excerpt">
                                                <?php the_excerpt(); ?>
                                            </div>
                                            
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm ajax-link">
                                                Read More <i class="fas fa-arrow-right"></i>
                                            </a>
                                            
                                            <!-- Reaction Buttons -->
                                            <?php echo do_shortcode('[reactions post_id="' . get_the_ID() . '"]'); ?>
                                        </div>
                                    </article>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="pagination-wrapper mt-4">
                            <?php
                            the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                                'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                            ));
                            ?>
                        </div>
                    <?php else : ?>
                        <p>No posts found.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
