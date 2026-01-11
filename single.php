<?php get_header(); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>
                    
                    <!-- Post Header -->
                    <header class="post-header mb-4">
                        <div class="post-categories mb-3">
                            <?php
                            $categories = get_the_category();
                            if ($categories) {
                                foreach ($categories as $category) {
                                    echo '<a href="' . get_category_link($category->term_id) . '" class="category-link">' . $category->name . '</a>';
                                }
                            }
                            ?>
                        </div>
                        
                        <h1 class="post-title-single"><?php the_title(); ?></h1>
                        
                        <div class="post-meta-single">
                            <span class="meta-item">
                                <i class="far fa-user"></i> 
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>
                            </span>
                            <span class="meta-item">
                                <i class="far fa-calendar"></i> <?php echo get_the_date('F j, Y'); ?>
                            </span>
                            <span class="meta-item">
                                <i class="far fa-clock"></i> <?php echo get_the_date('g:i a'); ?>
                            </span>
                            <span class="meta-item">
                                <i class="far fa-comments"></i> <?php comments_number('No Comments', '1 Comment', '% Comments'); ?>
                            </span>
                        </div>
                    </header>
                    
                    <!-- Featured Image -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail-single mb-4">
                            <?php the_post_thumbnail('news-large', array('class' => 'img-fluid rounded')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Share Top -->
                    <div class="social-share-buttons mb-4">
                        <h6 class="share-title"><i class="fas fa-share-alt"></i> Share This Article:</h6>
                        <div class="share-buttons-wrapper">
                            <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-telegram btn-sm">
                                <i class="fab fa-telegram"></i> Telegram
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="btn btn-whatsapp btn-sm">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-facebook btn-sm">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-twitter btn-sm">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        </div>
                    </div>
                    
                    <!-- Post Content -->
                    <div class="post-content-single">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Post Tags -->
                    <?php if (has_tag()) : ?>
                        <div class="post-tags mt-4">
                            <i class="fas fa-tags"></i> 
                            <?php the_tags('', ', ', ''); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Share Bottom -->
                    <div class="social-share-buttons mt-5 pt-4 border-top">
                        <h6 class="share-title"><i class="fas fa-share-alt"></i> Share This Article:</h6>
                        <div class="share-buttons-wrapper">
                            <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-telegram btn-sm">
                                <i class="fab fa-telegram"></i> Telegram
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="btn btn-whatsapp btn-sm">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-facebook btn-sm">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-twitter btn-sm">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        </div>
                    </div>
                    
                    <!-- Author Box -->
                    <div class="author-box mt-5">
                        <div class="author-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                        </div>
                        <div class="author-info">
                            <h5 class="author-name"><?php the_author(); ?></h5>
                            <p class="author-bio"><?php echo get_the_author_meta('description') ? get_the_author_meta('description') : 'No bio available.'; ?></p>
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="author-link">View all posts by <?php the_author(); ?> <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    
                </article>
                
                <!-- Related Posts -->
                <?php
                $categories = get_the_category();
                if ($categories) {
                    $category_ids = array();
                    foreach ($categories as $category) {
                        $category_ids[] = $category->term_id;
                    }
                    
                    $related_args = array(
                        'category__in' => $category_ids,
                        'post__not_in' => array(get_the_ID()),
                        'posts_per_page' => 3,
                        'ignore_sticky_posts' => 1
                    );
                    
                    $related_query = new WP_Query($related_args);
                    
                    if ($related_query->have_posts()) :
                ?>
                    <div class="related-posts mt-5">
                        <h3 class="section-title">You May Also Like</h3>
                        <div class="row">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <div class="col-md-4 mb-4">
                                    <article class="related-post-item">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="related-post-thumb">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('news-small'); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="related-post-content">
                                            <h5 class="related-post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h5>
                                            <div class="related-post-meta">
                                                <i class="far fa-calendar"></i> <?php echo get_the_date(); ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php
                    endif;
                    wp_reset_postdata();
                }
                ?>
                
                <!-- Comments -->
                <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
                
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
