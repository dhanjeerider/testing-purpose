<!-- List Layout (Full width with thumbnail on left) -->
<article class="news-card layout-list mb-4">
    <div class="row g-0">
        <?php if (has_post_thumbnail()) : ?>
            <div class="col-md-4">
                <a href="<?php the_permalink(); ?>" class="ajax-link">
                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid h-100 w-100', 'style' => 'object-fit: cover;')); ?>
                </a>
            </div>
        <?php endif; ?>
        <div class="<?php echo has_post_thumbnail() ? 'col-md-8' : 'col-md-12'; ?>">
            <div class="news-card-body p-3">
                <div class="meta mb-2">
                    <span class="badge bg-primary"><?php the_category(', '); ?></span>
                    <span class="text-muted ms-2">
                        <i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                    </span>
                </div>
                <h3 class="news-card-title">
                    <a href="<?php the_permalink(); ?>" class="ajax-link"><?php the_title(); ?></a>
                </h3>
                <div class="d-flex gap-2 mt-3">
                    <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-primary ajax-link">
                        <i class="fas fa-book-open"></i> Read
                    </a>
                    <?php echo do_shortcode('[reactions post_id="' . get_the_ID() . '"]'); ?>
                </div>
            </div>
        </div>
    </div>
</article>
