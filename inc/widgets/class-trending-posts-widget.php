<?php
/**
 * Trending Posts Widget
 */

class News_Trending_Posts_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'news_trending_posts',
            'News: Trending Posts',
            array('description' => 'Display trending/popular posts')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;
        
        $trending_posts = new WP_Query(array(
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'orderby' => 'comment_count',
            'order' => 'DESC'
        ));
        
        if ($trending_posts->have_posts()) :
            echo '<div class="trending-posts-widget">';
            $counter = 1;
            while ($trending_posts->have_posts()) : $trending_posts->the_post();
        ?>
                <div class="trending-post-item">
                    <span class="trending-number"><?php echo $counter; ?></span>
                    <div class="trending-post-content">
                        <h4 class="trending-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="trending-post-meta">
                            <span><i class="far fa-eye"></i> <?php echo get_comments_number(); ?> views</span>
                            <span><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                </div>
        <?php
                $counter++;
            endwhile;
            echo '</div>';
            wp_reset_postdata();
        endif;
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Trending Now';
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
