<?php
/**
 * Shortcodes for AJAX News Theme
 */

// Social Share Shortcode
function ajax_news_social_share_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => get_the_title(),
        'url' => get_permalink(),
    ), $atts);
    
    $title = urlencode($atts['title']);
    $url = urlencode($atts['url']);
    
    $output = '<div class="social-share">';
    $output .= '<h4>Share this article:</h4>';
    
    // Facebook
    $output .= '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" target="_blank" class="social-share-btn facebook">';
    $output .= '<i class="fab fa-facebook-f"></i> Facebook';
    $output .= '</a>';
    
    // Twitter
    $output .= '<a href="https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url . '" target="_blank" class="social-share-btn twitter">';
    $output .= '<i class="fab fa-twitter"></i> Twitter';
    $output .= '</a>';
    
    // WhatsApp
    $output .= '<a href="https://api.whatsapp.com/send?text=' . $title . ' ' . $url . '" target="_blank" class="social-share-btn whatsapp">';
    $output .= '<i class="fab fa-whatsapp"></i> WhatsApp';
    $output .= '</a>';
    
    // LinkedIn
    $output .= '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '" target="_blank" class="social-share-btn linkedin">';
    $output .= '<i class="fab fa-linkedin-in"></i> LinkedIn';
    $output .= '</a>';
    
    // Telegram
    $output .= '<a href="https://t.me/share/url?url=' . $url . '&text=' . $title . '" target="_blank" class="social-share-btn telegram">';
    $output .= '<i class="fab fa-telegram-plane"></i> Telegram';
    $output .= '</a>';
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('social_share', 'ajax_news_social_share_shortcode');

// Reactions Shortcode
function ajax_news_reactions_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
    ), $atts);
    
    $post_id = $atts['post_id'];
    
    // Get reaction counts
    $likes = get_post_meta($post_id, 'reaction_likes', true) ?: 0;
    $dislikes = get_post_meta($post_id, 'reaction_dislikes', true) ?: 0;
    $saves = get_post_meta($post_id, 'reaction_saves', true) ?: 0;
    
    // Check if user has reacted (using cookies for non-logged users)
    $user_id = get_current_user_id();
    $cookie_prefix = 'reaction_' . $post_id . '_';
    
    $user_liked = false;
    $user_disliked = false;
    $user_saved = false;
    
    if ($user_id) {
        $user_reactions = get_user_meta($user_id, 'post_reactions', true) ?: array();
        if (isset($user_reactions[$post_id])) {
            $user_liked = in_array('like', $user_reactions[$post_id]);
            $user_disliked = in_array('dislike', $user_reactions[$post_id]);
            $user_saved = in_array('save', $user_reactions[$post_id]);
        }
    } else {
        $user_liked = isset($_COOKIE[$cookie_prefix . 'like']);
        $user_disliked = isset($_COOKIE[$cookie_prefix . 'dislike']);
        $user_saved = isset($_COOKIE[$cookie_prefix . 'save']);
    }
    
    $output = '<div class="reaction-buttons">';
    
    // Like Button
    $like_class = $user_liked ? 'reaction-btn liked active' : 'reaction-btn';
    $output .= '<button class="' . $like_class . '" data-post-id="' . $post_id . '" data-reaction="like">';
    $output .= '<i class="fas fa-thumbs-up"></i> Like <span class="count">' . $likes . '</span>';
    $output .= '</button>';
    
    // Dislike Button
    $dislike_class = $user_disliked ? 'reaction-btn disliked active' : 'reaction-btn';
    $output .= '<button class="' . $dislike_class . '" data-post-id="' . $post_id . '" data-reaction="dislike">';
    $output .= '<i class="fas fa-thumbs-down"></i> Dislike <span class="count">' . $dislikes . '</span>';
    $output .= '</button>';
    
    // Save Button
    $save_class = $user_saved ? 'reaction-btn saved active' : 'reaction-btn';
    $output .= '<button class="' . $save_class . '" data-post-id="' . $post_id . '" data-reaction="save">';
    $output .= '<i class="fas fa-bookmark"></i> Save <span class="count">' . $saves . '</span>';
    $output .= '</button>';
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('reactions', 'ajax_news_reactions_shortcode');

// Video Player Shortcode
function ajax_news_video_player_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'type' => 'youtube', // youtube, vimeo, mp4
        'poster' => '',
    ), $atts);
    
    $output = '<div class="video-player-container">';
    
    if ($atts['type'] === 'youtube') {
        // Extract YouTube ID
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $atts['url'], $matches);
        $youtube_id = $matches[1] ?? '';
        
        if ($youtube_id) {
            $output .= '<iframe src="https://www.youtube.com/embed/' . $youtube_id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }
    } elseif ($atts['type'] === 'vimeo') {
        // Extract Vimeo ID
        preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $atts['url'], $matches);
        $vimeo_id = $matches[3] ?? '';
        
        if ($vimeo_id) {
            $output .= '<iframe src="https://player.vimeo.com/video/' . $vimeo_id . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
    } else {
        // HTML5 Video
        $output .= '<video class="plyr-video" controls';
        if ($atts['poster']) {
            $output .= ' poster="' . esc_url($atts['poster']) . '"';
        }
        $output .= '>';
        $output .= '<source src="' . esc_url($atts['url']) . '" type="video/mp4">';
        $output .= 'Your browser does not support the video tag.';
        $output .= '</video>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('video_player', 'ajax_news_video_player_shortcode');

// Newsletter Subscription Shortcode
function ajax_news_newsletter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Subscribe to our Newsletter',
        'placeholder' => 'Enter your email',
    ), $atts);
    
    $output = '<div class="newsletter-box p-4 my-4" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px;">';
    $output .= '<h3>' . esc_html($atts['title']) . '</h3>';
    $output .= '<form class="newsletter-form" method="post" action="' . admin_url('admin-ajax.php') . '">';
    $output .= '<div class="input-group">';
    $output .= '<input type="email" name="email" class="form-control" placeholder="' . esc_attr($atts['placeholder']) . '" required>';
    $output .= '<button type="submit" class="btn btn-primary">Subscribe</button>';
    $output .= '</div>';
    $output .= '<input type="hidden" name="action" value="newsletter_subscribe">';
    $output .= wp_nonce_field('newsletter_nonce', 'nonce', true, false);
    $output .= '</form>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('newsletter', 'ajax_news_newsletter_shortcode');

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
    
    $output = '<div class="related-posts my-5">';
    $output .= '<h3>Related Articles</h3>';
    $output .= '<div class="row">';
    
    while ($related_query->have_posts()) {
        $related_query->the_post();
        
        $output .= '<div class="col-md-4">';
        $output .= '<article class="news-card">';
        
        if (has_post_thumbnail()) {
            $output .= '<a href="' . get_permalink() . '" class="ajax-link">';
            $output .= get_the_post_thumbnail(get_the_ID(), 'news-thumb');
            $output .= '</a>';
        }
        
        $output .= '<div class="news-card-body">';
        $output .= '<h4 class="news-card-title"><a href="' . get_permalink() . '" class="ajax-link">' . get_the_title() . '</a></h4>';
        $output .= '<p>' . wp_trim_words(get_the_excerpt(), 15) . '</p>';
        $output .= '</div>';
        $output .= '</article>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    $output .= '</div>';
    
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('related_posts', 'ajax_news_related_posts_shortcode');
