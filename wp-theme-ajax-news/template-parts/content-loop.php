<div class="col-md-6 mb-4">
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
            
            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm ajax-link mt-2">
                Read More <i class="fas fa-arrow-right"></i>
            </a>
            
            <!-- Reaction Buttons -->
            <?php echo do_shortcode('[reactions post_id="' . get_the_ID() . '"]'); ?>
        </div>
    </article>
</div>
