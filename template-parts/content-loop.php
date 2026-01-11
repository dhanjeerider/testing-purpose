<article id="post-<?php the_ID(); ?>" <?php post_class('news-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('medium_large'); ?>
        </a>
    <?php endif; ?>
    
    <div class="news-card-body">
        <div class="meta mb-2">
            <span class="text-muted">
                <i class="far fa-calendar"></i> 
                <?php echo get_the_date(); ?>
            </span>
            <span class="text-muted ms-3">
                <i class="far fa-user"></i> 
                <?php the_author(); ?>
            </span>
        </div>
        
        <h2 class="news-card-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>
        
        <!-- External Share Buttons -->
        <div class="share-action mt-2">
            <div class="d-flex flex-wrap gap-1">
                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-success btn-sm" style="font-size: 11px; padding: 4px 8px;">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="font-size: 11px; padding: 4px 8px;">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-info btn-sm" style="font-size: 11px; padding: 4px 8px;">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://t.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="font-size: 11px; padding: 4px 8px;">
                    <i class="fab fa-telegram-plane"></i>
                </a>
            </div>
        </div>
    </div>
</article>
