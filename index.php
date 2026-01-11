<?php get_header(); ?>

<div class="container">
    <div class="posts-grid">
        <?php if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <article class="post-card">
                    <div class="post-image">
                        <?php if (has_post_thumbnail()) { the_post_thumbnail('post-thumb'); } ?>
                        <?php if (get_the_category()) { ?>
                            <span class="post-label"><?php echo get_the_category()[0]->name; ?></span>
                        <?php } ?>
                    </div>
                    <div class="post-info">
                        <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="post-meta">
                            <span><i class="far fa-calendar"></i> <?php echo get_the_date('M d'); ?></span>
                            <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                        </div>
                        <div class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></div>
                        <div class="post-footer">
                            <button class="read-btn" onclick="window.location='<?php the_permalink(); ?>'">Read More</button>
                            <div class="post-actions">
                                <button class="like-btn" data-post-id="<?php the_ID(); ?>"><i class="far fa-heart"></i></button>
                                <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>" class="share-btn telegram" target="_blank"><i class="fab fa-telegram"></i></a>
                                <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" class="share-btn whatsapp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php }
        } ?>
    </div>
    
    <?php the_posts_pagination(array('mid_size' => 2)); ?>
</div>

<?php get_footer(); ?>

