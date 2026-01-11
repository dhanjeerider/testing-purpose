<?php get_header(); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="posts-grid">
                <div class="row">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <div class="col-md-6 mb-4">
                                <article class="post-card">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('news-medium'); ?>
                                            </a>
                                            <?php if (get_the_category()) : ?>
                                                <span class="category-badge"><?php echo get_the_category()[0]->name; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content">
                                        <h2 class="post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="post-meta">
                                            <span><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                                            <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                                        </div>
                                        
                                        <div class="post-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                        </div>
                                        
                                        <div class="post-footer">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">Read More</a>
                                            
                                            <div class="post-actions">
                                                <button class="like-btn" data-post-id="<?php the_ID(); ?>">
                                                    <i class="far fa-heart"></i>
                                                    <span class="like-count">0</span>
                                                </button>
                                                <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="share-btn">
                                                    <i class="fab fa-telegram"></i>
                                                </a>
                                                <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="share-btn">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endwhile; ?>
                        
                        <div class="col-12">
                            <?php
                            the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                                'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                            ));
                            ?>
                        </div>
                    <?php else : ?>
                        <div class="col-12">
                            <p>No posts found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

