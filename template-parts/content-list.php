<!-- List Layout (Full width with thumbnail on left) -->
<div class="col-12 mb-3">
    <article class="news-card layout-list">
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
                    <!-- Author Meta -->
                    <div class="post-meta-author mb-2">
                        <?php echo get_avatar(get_the_author_meta('ID'), 44, '', '', array('class' => 'author-avatar')); ?>
                        <div class="author-info">
                            <strong><?php the_author(); ?></strong>
                            <div class="meta-details">
                                <span class="post-date"><i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                <span class="post-category"><i class="fas fa-tag"></i> <?php the_category(', '); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="news-card-title">
                        <a href="<?php the_permalink(); ?>" class="ajax-link"><?php the_title(); ?></a>
                    </h3>
                    
                    <!-- Reactions Row -->
                    <div class="reactions-card mt-2">
                        <button class="btn-reaction like-btn" data-post-id="<?php the_ID(); ?>" title="Like">
                            <i class="far fa-heart"></i> <span class="count"><?php echo get_post_meta(get_the_ID(), '_likes', true) ?: 0; ?></span>
                        </button>
                        <button class="btn-reaction save-btn" data-post-id="<?php the_ID(); ?>" title="Save">
                            <i class="far fa-bookmark"></i>
                        </button>
                        <button class="btn-reaction share-btn" data-post-id="<?php the_ID(); ?>" title="Share">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>
