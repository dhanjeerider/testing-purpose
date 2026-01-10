<!-- Grid Layout (2 columns) -->
<div class="col-md-6 mb-4">
    <article class="news-card layout-grid">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" class="ajax-link">
                <?php the_post_thumbnail('medium_large'); ?>
            </a>
        <?php endif; ?>
        <div class="news-card-body">
            <div class="meta mb-2">
                <span class="badge bg-primary"><?php the_category(', '); ?></span>
                <span class="text-muted ms-2">
                    <i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                </span>
            </div>
            <h3 class="news-card-title">
                <a href="<?php the_permalink(); ?>" class="ajax-link"><?php the_title(); ?></a>
            </h3>
            <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-primary ajax-link">Read More</a>
        </div>
    </article>
</div>
