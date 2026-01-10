<?php get_header(); ?>

<main class="site-main">
    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <!-- Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail mb-4">
                                <?php the_post_thumbnail('large', array('class' => 'img-fluid rounded')); ?>
                            </div>
                        <?php endif; ?>
                        
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
                        
                        <!-- Post Content -->
                        <div class="entry-content">
                            <?php the_content(); ?>
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
                            <?php echo do_shortcode('[social_share]'); ?>
                        </div>
                        
                        <!-- Reaction Buttons -->
                        <div class="mt-4">
                            <?php echo do_shortcode('[reactions post_id="' . get_the_ID() . '"]'); ?>
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
