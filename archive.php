<?php get_header(); ?>

<?php if (is_active_sidebar('header-widget-area')) : ?>
    <div class="header-widget-area">
        <div class="container">
            <?php dynamic_sidebar('header-widget-area'); ?>
        </div>
    </div>
<?php endif; ?>

<div class="container my-5">
    
    <!-- Archive Title Section -->
    <div class="archive-title-section mb-5">
        <div class="archive-title-wrapper">
            <h1 class="archive-main-title">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    echo 'Tag: ';
                    single_tag_title();
                } elseif (is_author()) {
                    echo 'Author: ' . get_the_author();
                } elseif (is_date()) {
                    if (is_day()) {
                        echo 'Daily Archives: ' . get_the_date('F j, Y');
                    } elseif (is_month()) {
                        echo 'Monthly Archives: ' . get_the_date('F Y');
                    } elseif (is_year()) {
                        echo 'Yearly Archives: ' . get_the_date('Y');
                    }
                } else {
                    echo 'Archives';
                }
                ?>
            </h1>
            
            <?php if (is_category() && category_description()) : ?>
                <div class="archive-description">
                    <?php echo category_description(); ?>
                </div>
            <?php endif; ?>
            
            <?php if (is_tag() && tag_description()) : ?>
                <div class="archive-description">
                    <?php echo tag_description(); ?>
                </div>
            <?php endif; ?>
            
            <div class="archive-meta">
                <?php if (have_posts()) : ?>
                    <span class="post-count">
                        <i class="fas fa-file-alt"></i> <?php echo $wp_query->found_posts; ?> Posts Found
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (is_active_sidebar('before-content')) : ?>
        <div class="before-content-area mb-5">
            <?php dynamic_sidebar('before-content'); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="posts-container">
                <div class="row">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <div class="col-md-6 mb-4">
                                <article id="post-<?php the_ID(); ?>" <?php post_class('material-card h-100'); ?>>
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
                                    
                                    <div class="card-content p-4">
                                        <h2 class="post-title h5">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="post-meta mb-3">
                                            <span class="meta-item"><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                                            <span class="meta-item"><i class="far fa-user"></i> <?php the_author(); ?></span>
                                        </div>
                                        
                                        <div class="post-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>
                                        
                                        <div class="post-footer mt-3 d-flex justify-content-between align-items-center">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                                Read More <i class="fas fa-arrow-right"></i>
                                            </a>
                                            
                                            <div class="post-actions">
                                                <button class="action-btn like-btn" data-post-id="<?php the_ID(); ?>" title="Like">
                                                    <i class="far fa-heart"></i>
                                                    <span class="like-count">0</span>
                                                </button>
                                                <div class="quick-share">
                                                    <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="share-icon telegram" title="Telegram">
                                                        <i class="fab fa-telegram"></i>
                                                    </a>
                                                    <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="share-icon whatsapp" title="WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endwhile; ?>
                        
                        <div class="col-12">
                            <nav class="pagination-wrapper mt-4">
                                <?php
                                the_posts_pagination(array(
                                    'mid_size' => 2,
                                    'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                                    'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
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
        
        <div class="col-lg-4">
            <aside class="main-sidebar">
                <?php if (is_active_sidebar('main-sidebar')) : ?>
                    <?php dynamic_sidebar('main-sidebar'); ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>

<?php get_footer(); ?>
