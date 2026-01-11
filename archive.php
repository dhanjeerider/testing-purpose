<?php get_header(); ?>

<div class="container my-5">
    <div class="archive-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="archive-title">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    echo 'Posts by: ' . get_the_author();
                } elseif (is_date()) {
                    echo 'Archive: ' . get_the_date('F Y');
                } else {
                    echo 'Archives';
                }
                ?>
            </h1>
            
            <!-- Layout Switcher -->
            <div class="layout-switcher">
                <button class="layout-btn active" data-layout="grid" title="Grid View">
                    <i class="fas fa-th"></i>
                </button>
                <button class="layout-btn" data-layout="list" title="List View">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
        
        <?php if (is_category() && category_description()) : ?>
            <div class="archive-description mt-3">
                <?php echo category_description(); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="posts-container grid-layout">
        <div id="main-content" class="row">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-md-6 mb-4">
                        <article id="post-<?php the_ID(); ?>" <?php post_class('material-card h-100'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large', array('class' => 'img-fluid')); ?>
                                    </a>
                                    <?php if (get_the_category()) : ?>
                                        <span class="category-badge"><?php echo get_the_category()[0]->name; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-content p-4">
                                <h2 class="post-title h4">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="post-meta mb-3">
                                    <span class="meta-item"><i class="fas fa-calendar"></i> <?php echo get_the_date(); ?></span>
                                    <span class="meta-item"><i class="fas fa-user"></i> <?php the_author(); ?></span>
                                    <span class="meta-item"><i class="fas fa-comments"></i> <?php comments_number('0', '1', '%'); ?></span>
                                </div>
                                
                                <div class="post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <div class="post-footer mt-3 d-flex justify-content-between align-items-center">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">Read More <i class="fas fa-arrow-right"></i></a>
                                    
                                    <div class="quick-share">
                                        <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="share-icon telegram" title="Share on Telegram">
                                            <i class="fab fa-telegram"></i>
                                        </a>
                                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="share-icon whatsapp" title="Share on WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                
                <!-- Pagination -->
                <div class="col-12">
                    <nav class="pagination-wrapper mt-4">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                            'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                            'class' => 'pagination-custom'
                        ));
                        ?>
                    </nav>
                </div>
                
            <?php else : ?>
                <div class="col-12">
                    <div class="no-posts-found text-center py-5">
                        <i class="fas fa-inbox fa-4x mb-3 text-muted"></i>
                        <h3>No posts found</h3>
                        <p class="text-muted">Sorry, no posts were found in this archive.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
