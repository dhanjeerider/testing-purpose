<aside id="secondary" class="widget-area">
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <?php dynamic_sidebar('sidebar-1'); ?>
    <?php else : ?>
        
        <!-- Search Widget -->
        <section class="widget widget_search">
            <?php get_search_form(); ?>
        </section>
        
        <!-- Recent Posts -->
        <section class="widget widget_recent_entries">
            <h2 class="widget-title">Recent Posts</h2>
            <ul>
                <?php
                $recent_posts = wp_get_recent_posts(array(
                    'numberposts' => 5,
                    'post_status' => 'publish'
                ));
                foreach ($recent_posts as $post) :
                ?>
                    <li>
                        <a href="<?php echo get_permalink($post['ID']); ?>" class="ajax-link">
                            <?php echo $post['post_title']; ?>
                        </a>
                    </li>
                <?php endforeach; wp_reset_query(); ?>
            </ul>
        </section>
        
        <!-- Categories -->
        <section class="widget widget_categories">
            <h2 class="widget-title">Categories</h2>
            <ul>
                <?php wp_list_categories(array('title_li' => '')); ?>
            </ul>
        </section>
        
        <!-- Tags Cloud -->
        <section class="widget widget_tag_cloud">
            <h2 class="widget-title">Tags</h2>
            <?php wp_tag_cloud(); ?>
        </section>
        
    <?php endif; ?>
</aside>
