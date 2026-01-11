<?php get_header(); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('material-card'); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-content p-4">
                        <h1 class="post-title mb-3"><?php the_title(); ?></h1>
                        
                        <div class="post-meta mb-4">
                            <span class="meta-item"><i class="fas fa-calendar"></i> <?php echo get_the_date(); ?></span>
                            <span class="meta-item"><i class="fas fa-user"></i> <?php the_author(); ?></span>
                        </div>
                        
                        <!-- Social Share Buttons -->
                        <div class="social-share-buttons mb-4">
                            <h6 class="share-title">Share This:</h6>
                            <div class="share-buttons-wrapper">
                                <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-telegram" title="Share on Telegram">
                                    <i class="fab fa-telegram"></i> Telegram
                                </a>
                                <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="btn btn-whatsapp" title="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-facebook" title="Share on Facebook">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-twitter" title="Share on Twitter">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                                <a href="https://news.google.com/publications/CAAqBwgKMKHL9QowkqbaAg" target="_blank" class="btn btn-google-news" title="Follow on Google News">
                                    <i class="fab fa-google"></i> Google News
                                </a>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Social Share Buttons Bottom -->
                        <div class="social-share-buttons mt-4 pt-4 border-top">
                            <h6 class="share-title">Share This:</h6>
                            <div class="share-buttons-wrapper">
                                <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-telegram">
                                    <i class="fab fa-telegram"></i> Telegram
                                </a>
                                <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="btn btn-whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-facebook">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-twitter">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
