<?php
/**
 * Custom template tags for this theme
 */

// Display post entry with image and border
function news_post_entry() {
    ?>
    <div class="post-entry-item">
        <?php if (has_post_thumbnail()) : ?>
            <div class="post-entry-thumb">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('news-thumb'); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="post-entry-content">
            <h3 class="post-entry-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <div class="post-entry-meta">
                <i class="far fa-calendar"></i> <?php echo get_the_date(); ?>
                <span class="mx-2">•</span>
                <i class="far fa-user"></i> <?php the_author(); ?>
            </div>
        </div>
    </div>
    <?php
}

// Display post navigation
function news_post_navigation() {
    the_post_navigation(array(
        'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'news-theme') . '</span> <span class="nav-title">%title</span>',
        'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'news-theme') . '</span> <span class="nav-title">%title</span>',
    ));
}
