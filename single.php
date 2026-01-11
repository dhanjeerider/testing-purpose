<?php get_header(); ?>

<main class="site-main">
    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>     
                        <!-- Post Header -->
                        <header class="entry-header mb-4">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                            
                            <div class="entry-meta text-muted">
                                <span class="me-3">
                                    <i class="far fa-calendar"></i> 
                                    <?php echo get_the_date(); ?>
                                </span>
                                <span class="me-3">
                                    <i class="far fa-user"></i> 
                                    <?php the_author(); ?>
                                </span>
                                <span class="me-3">
                                    <i class="far fa-folder"></i> 
                                    <?php the_category(', '); ?>
                                </span>
                                <span>
                                    <i class="far fa-comments"></i> 
                                    <?php comments_number('0 Comments', '1 Comment', '% Comments'); ?>
                                </span>
                            </div>
                        </header>
                        
                        <!-- Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail mb-4">
                                <?php the_post_thumbnail('large', array('class' => 'img-fluid rounded')); ?>
                            </div>
                        <?php endif; ?>
                   
                        <!-- Post Content -->
                        <div class="entry-content" style="font-size: 16px; line-height: 1.8; color: var(--text-color);">
                            <?php 
                            the_content();
                            
                            wp_link_pages(array(
                                'before' => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'ajax-news-theme') . '</span>',
                                'after'  => '</div>',
                                'link_before' => '<span>',
                                'link_after'  => '</span>',
                            ));
                            ?>
                        </div>
                        
                        <!-- Tags -->
                        <?php if (has_tag()) : ?>
                            <div class="post-tags mt-4">
                                <i class="fas fa-tags"></i> 
                                <?php the_tags('', ', ', ''); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- External Share Buttons -->
                        <div class="mt-4">
                            <h5 style="font-size: 1rem; margin-bottom: 15px; color: var(--text-color);">Share this article:</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-success btn-sm">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-info btn-sm">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                                <a href="https://t.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                    <i class="fab fa-telegram-plane"></i> Telegram
                                </a>
                            </div>
                        </div>
                    </article>
                    
                    <!-- Related Posts -->
                    <?php
                    $categories = wp_get_post_categories(get_the_ID());
                    if ($categories) :
                        $related_args = array(
                            'category__in' => $categories,
                            'post__not_in' => array(get_the_ID()),
                            'posts_per_page' => 6,
                            'orderby' => 'rand',
                        );
                        $related_query = new WP_Query($related_args);
                        
                        if ($related_query->have_posts()) :
                    ?>
                    <div class="related-posts mt-5">
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; color: var(--text-color);">Related Articles</h3>
                        <div class="posts-grid" id="related-posts-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                            <?php 
                            $count = 0;
                            while ($related_query->have_posts()) : 
                                $related_query->the_post(); 
                                if ($count < 3) : // Show only 3 initially
                                    get_template_part('template-parts/content', 'loop');
                                endif;
                                $count++;
                            endwhile; 
                            ?>
                        </div>
                        
                        <?php if ($related_query->found_posts > 3) : ?>
                        <div class="text-center mt-3">
                            <button id="loadMoreRelated" class="btn btn-primary" data-shown="3" data-total="<?php echo $related_query->found_posts; ?>">
                                Load More Related Posts <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        
                        <!-- Hidden Posts for Load More -->
                        <div id="hidden-related-posts" style="display: none;">
                            <?php 
                            $related_query->rewind_posts();
                            $count = 0;
                            while ($related_query->have_posts()) : 
                                $related_query->the_post(); 
                                if ($count >= 3) : // Hide posts after first 3
                                    echo '<div class="hidden-post">';
                                    get_template_part('template-parts/content', 'loop');
                                    echo '</div>';
                                endif;
                                $count++;
                            endwhile; 
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php 
                        endif;
                        wp_reset_postdata();
                    endif;
                    ?>
                    
                    <!-- Comments -->
                    <?php
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>
                <?php endwhile; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
