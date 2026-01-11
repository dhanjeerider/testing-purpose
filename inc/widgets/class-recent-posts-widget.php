<?php
/**
 * Recent Posts Widget
 */

class News_Recent_Posts_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'news_recent_posts',
            'News: Recent Posts',
            array('description' => 'Display recent posts with thumbnails')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;
        
        $recent_posts = new WP_Query(array(
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        ));
        
        if ($recent_posts->have_posts()) :
            echo '<div class="recent-posts-widget">';
            while ($recent_posts->have_posts()) : $recent_posts->the_post();
        ?>
                <div class="widget-post-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="widget-post-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('news-thumb'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="widget-post-content">
                        <h4 class="widget-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="widget-post-meta">
                            <span><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
            echo '</div>';
            wp_reset_postdata();
        endif;
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Recent Posts';
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">Number of posts:</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        return $instance;
    }
}
