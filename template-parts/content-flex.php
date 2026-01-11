<!-- Flex Layout (Horizontal card) -->
<article class="news-card layout-flex mb-3">
    <div class="row g-0 align-items-center" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
        <?php if (has_post_thumbnail()) : ?>
            <div class="col-4 col-md-3">
                <a href="<?php the_permalink(); ?>" class="ajax-link">
                    <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid w-100', 'style' => 'height: 120px; object-fit: cover;')); ?>
                </a>
            </div>
        <?php endif; ?>
        <div class="<?php echo has_post_thumbnail() ? 'col-8 col-md-9' : 'col-12'; ?>">
            <div class="p-3">
                <h5 class="mb-2" style="font-size: 1rem; line-height: 1.4;">
                    <a href="<?php the_permalink(); ?>" class="ajax-link text-decoration-none" style="color: var(--text-color);">
                        <?php the_title(); ?>
                    </a>
                </h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="meta text-muted small">
                        <i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-secondary reaction-btn" data-post-id="<?php the_ID(); ?>" data-reaction="like" style="padding: 2px 8px; font-size: 0.75rem;">
                            <i class="fas fa-thumbs-up"></i> <span class="count"><?php echo get_post_meta(get_the_ID(), 'reaction_likes', true) ?: 0; ?></span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary reaction-btn" data-post-id="<?php the_ID(); ?>" data-reaction="save" style="padding: 2px 8px; font-size: 0.75rem;">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
