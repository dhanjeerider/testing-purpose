<?php
/**
 * Shortcodes for AJAX News Theme - Cleaned Up
 */

// Social Share Shortcode
function ajax_news_social_share_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => get_the_title(),
        'url' => get_permalink(),
    ), $atts);
    
    $title = urlencode($atts['title']);
    $url = urlencode($atts['url']);
    
    ob_start();
    ?>
    <div class="social-share">
        <h4>Share this article:</h4>
        
   <div class="d-flex flex-wrap"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" class="social-share-btn facebook">
            <i class="fab fa-facebook-f"></i> Facebook
        </a>
        
        <a href="https://twitter.com/intent/tweet?text=<?php echo $title; ?>&url=<?php echo $url; ?>" target="_blank" class="social-share-btn twitter">
            <i class="fab fa-twitter"></i> Twitter
        </a>
        
        <a href="https://api.whatsapp.com/send?text=<?php echo $title; ?> <?php echo $url; ?>" target="_blank" class="social-share-btn whatsapp">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" target="_blank" class="social-share-btn linkedin">
            <i class="fab fa-linkedin-in"></i> LinkedIn
        </a>
        
        <a href="https://t.me/share/url?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" class="social-share-btn telegram">
            <i class="fab fa-telegram-plane"></i> Telegram
        </a>
    </div> </div>
    <?php
    return ob_get_clean();
}
add_shortcode('social_share', 'ajax_news_social_share_shortcode');

// Related Posts Shortcode
function ajax_news_related_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 3,
        'post_id' => get_the_ID(),
    ), $atts);
    
    $post_id = $atts['post_id'];
    $categories = wp_get_post_categories($post_id);
    
    $args = array(
        'category__in' => $categories,
        'post__not_in' => array($post_id),
        'posts_per_page' => $atts['count'],
        'orderby' => 'rand',
    );
    
    $related_query = new WP_Query($args);
    
    if (!$related_query->have_posts()) {
        return '';
    }
    
    ob_start();
    echo '<div class="related-posts my-5">';
    echo '<h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 20px; color: var(--text-color);">Related Articles</h3>';
    echo '<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">';
    
    while ($related_query->have_posts()) {
        $related_query->the_post();
        get_template_part('template-parts/content', 'loop');
    }
    
    echo '</div>';
    echo '</div>';
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('related_posts', 'ajax_news_related_posts_shortcode');

// Also Read Section Shortcode
function ajax_news_also_read_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
        'count' => 5,
    ), $atts);
    
    $post_id = $atts['post_id'];
    $categories = wp_get_post_categories($post_id);
    
    $args = array(
        'category__in' => $categories,
        'post__not_in' => array($post_id),
        'posts_per_page' => $atts['count'],
        'orderby' => 'date',
    );
    
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '';
    }
    
    $output = '<div class="also-read-section">';
    $output .= '<h4><i class="fas fa-bookmark"></i> Also Read</h4>';
    $output .= '<ul class="also-read-list">';
    
    while ($query->have_posts()) {
        $query->the_post();
        $output .= '<li><a href="' . get_permalink() . '" class="ajax-link">' . get_the_title() . '</a></li>';
    }
    
    $output .= '</ul>';
    $output .= '</div>';
    
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('also_read', 'ajax_news_also_read_shortcode');
