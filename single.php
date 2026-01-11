<?php get_header(); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <?php while (have_posts()) : the_post(); ?>
                <article class="single-post">
                    <header class="post-header">
                        <?php if (get_the_category()) : ?>
                            <div class="post-categories">
                                <?php foreach (get_the_category() as $cat) : ?>
                                    <a href="<?php echo get_category_link($cat->term_id); ?>" class="category-link"><?php echo $cat->name; ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        
                        <div class="post-meta">
                            <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                            <span><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                            <span><i class="far fa-comments"></i> <?php comments_number(); ?></span>
                        </div>
                    </header>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-featured-image">
                            <?php the_post_thumbnail('news-large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="share-buttons">
                        <h6>Share:</h6>
                        <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-telegram btn-sm">
                            <i class="fab fa-telegram"></i> Telegram
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="btn btn-whatsapp btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-facebook btn-sm">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                    </div>
                    
                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <?php if (has_tag()) : ?>
                        <div class="post-tags">
                            <?php the_tags('Tags: ', ', ', ''); ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
