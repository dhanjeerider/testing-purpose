<article id="post-<?php the_ID(); ?>" <?php post_class('news-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="ajax-link">
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
            <a href="<?php the_permalink(); ?>" class="ajax-link">
                <?php the_title(); ?>
            </a>
        </h2>
        
        <!-- External Share Button -->
        <div class="share-action mt-2">
            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-success">
                <i class="fab fa-whatsapp"></i> Share
            </a>
        </div>
    </div>
</article>
