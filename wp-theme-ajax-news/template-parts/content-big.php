<!-- Big Featured Layout (Full width hero) -->
<article class="news-card layout-big mb-4">
    <?php if (has_post_thumbnail()) : ?>
        <div class="position-relative">
            <a href="<?php the_permalink(); ?>" class="ajax-link">
                <?php the_post_thumbnail('large', array('class' => 'img-fluid w-100', 'style' => 'max-height: 500px; object-fit: cover;')); ?>
            </a>
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                <div class="text-white">
                    <span class="badge bg-danger mb-2">Featured</span>
                    <h2 class="text-white mb-2">
                        <a href="<?php the_permalink(); ?>" class="ajax-link text-white text-decoration-none">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <div class="meta text-white-50">
                        <i class="far fa-user"></i> <?php the_author(); ?> • 
                        <i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</article>
