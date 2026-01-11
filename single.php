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
                        
                        <!-- Social Share -->
                        <div class="mt-4">
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-success">
                                <i class="fab fa-whatsapp"></i> Share on WhatsApp
                            </a>
                        </div>
                    </article>
                    
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
