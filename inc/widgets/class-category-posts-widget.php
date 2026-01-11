<?php
/**
 * Category Posts Widget
 */

class News_Category_Posts_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'news_category_posts',
            'News: Category Posts',
            array('description' => 'Display posts from specific category')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $number = (!empty($instance['number'])) ? absint($instance['number']) : 4;
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'grid';
        
        $query_args = array(
            'posts_per_page' => $number,
            'post_status' => 'publish'
        );
        
        if ($category) {
            $query_args['cat'] = $category;
        }
        
        $category_posts = new WP_Query($query_args);
        
        if ($category_posts->have_posts()) :
            echo '<div class="category-posts-widget layout-' . esc_attr($layout) . '">';
            echo '<div class="row">';
            
            while ($category_posts->have_posts()) : $category_posts->the_post();
                $col_class = ($layout === 'grid') ? 'col-md-6' : 'col-12';
        ?>
                <div class="<?php echo $col_class; ?> mb-4">
                    <article class="widget-post-card material-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('news-medium'); ?>
                                </a>
                                <?php if (get_the_category()) : ?>
                                    <span class="category-badge"><?php echo get_the_category()[0]->name; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-content p-3">
                            <h3 class="post-title h5">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="post-meta">
                                <span class="meta-item"><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                            </div>
                        </div>
                    </article>
                </div>
        <?php
            endwhile;
            echo '</div></div>';
            wp_reset_postdata();
        endif;
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $number = !empty($instance['number']) ? absint($instance['number']) : 4;
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'grid';
        
        $categories = get_categories();
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>">Category:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo $cat->term_id; ?>" <?php selected($category, $cat->term_id); ?>>
                        <?php echo $cat->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">Number of posts:</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('layout'); ?>">Layout:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>">
                <option value="grid" <?php selected($layout, 'grid'); ?>>Grid</option>
                <option value="list" <?php selected($layout, 'list'); ?>>List</option>
            </select>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['category'] = (!empty($new_instance['category'])) ? absint($new_instance['category']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 4;
        $instance['layout'] = (!empty($new_instance['layout'])) ? $new_instance['layout'] : 'grid';
        return $instance;
    }
}
