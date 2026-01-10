<?php
/**
 * Reactions Functions - Like, Dislike, Save
 */

// Handle reaction AJAX request
function ajax_news_handle_reaction() {
    check_ajax_referer('reactions_nonce', 'nonce');
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $reaction = isset($_POST['reaction']) ? sanitize_text_field($_POST['reaction']) : '';
    
    if (!$post_id || !in_array($reaction, array('like', 'dislike', 'save'))) {
        wp_send_json_error('Invalid data');
    }
    
    $user_id = get_current_user_id();
    $meta_key = 'reaction_' . $reaction . 's';
    $current_count = get_post_meta($post_id, $meta_key, true) ?: 0;
    
    // Check if user already reacted
    $user_reacted = false;
    
    if ($user_id) {
        // For logged-in users, use user meta
        $user_reactions = get_user_meta($user_id, 'post_reactions', true) ?: array();
        
        if (!isset($user_reactions[$post_id])) {
            $user_reactions[$post_id] = array();
        }
        
        if (in_array($reaction, $user_reactions[$post_id])) {
            // Remove reaction
            $user_reactions[$post_id] = array_diff($user_reactions[$post_id], array($reaction));
            $current_count = max(0, $current_count - 1);
            $user_reacted = false;
            $message = ucfirst($reaction) . ' removed';
        } else {
            // Add reaction
            if (in_array($reaction, array('like', 'dislike'))) {
                // Remove opposite reaction
                $opposite = $reaction === 'like' ? 'dislike' : 'like';
                if (in_array($opposite, $user_reactions[$post_id])) {
                    $user_reactions[$post_id] = array_diff($user_reactions[$post_id], array($opposite));
                    $opposite_count = get_post_meta($post_id, 'reaction_' . $opposite . 's', true) ?: 0;
                    update_post_meta($post_id, 'reaction_' . $opposite . 's', max(0, $opposite_count - 1));
                }
            }
            
            $user_reactions[$post_id][] = $reaction;
            $current_count++;
            $user_reacted = true;
            $message = ucfirst($reaction) . 'd successfully';
        }
        
        update_user_meta($user_id, 'post_reactions', $user_reactions);
    } else {
        // For non-logged users, use cookies
        $cookie_name = 'reaction_' . $post_id . '_' . $reaction;
        
        if (isset($_COOKIE[$cookie_name])) {
            // Remove reaction
            setcookie($cookie_name, '', time() - 3600, '/');
            $current_count = max(0, $current_count - 1);
            $user_reacted = false;
            $message = ucfirst($reaction) . ' removed';
        } else {
            // Add reaction
            if (in_array($reaction, array('like', 'dislike'))) {
                // Remove opposite reaction cookie
                $opposite = $reaction === 'like' ? 'dislike' : 'like';
                $opposite_cookie = 'reaction_' . $post_id . '_' . $opposite;
                if (isset($_COOKIE[$opposite_cookie])) {
                    setcookie($opposite_cookie, '', time() - 3600, '/');
                    $opposite_count = get_post_meta($post_id, 'reaction_' . $opposite . 's', true) ?: 0;
                    update_post_meta($post_id, 'reaction_' . $opposite . 's', max(0, $opposite_count - 1));
                }
            }
            
            setcookie($cookie_name, '1', time() + (86400 * 365), '/');
            $current_count++;
            $user_reacted = true;
            $message = ucfirst($reaction) . 'd successfully';
        }
    }
    
    // Update post meta
    update_post_meta($post_id, $meta_key, $current_count);
    
    wp_send_json_success(array(
        'count' => $current_count,
        'user_reacted' => $user_reacted,
        'message' => $message,
    ));
}
add_action('wp_ajax_handle_reaction', 'ajax_news_handle_reaction');
add_action('wp_ajax_nopriv_handle_reaction', 'ajax_news_handle_reaction');

// Get user's saved posts
function ajax_news_get_saved_posts($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return array();
    }
    
    $user_reactions = get_user_meta($user_id, 'post_reactions', true) ?: array();
    $saved_posts = array();
    
    foreach ($user_reactions as $post_id => $reactions) {
        if (in_array('save', $reactions)) {
            $saved_posts[] = $post_id;
        }
    }
    
    return $saved_posts;
}

// Shortcode to display saved posts
function ajax_news_saved_posts_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '<p>Please login to view your saved posts.</p>';
    }
    
    $saved_posts = ajax_news_get_saved_posts();
    
    if (empty($saved_posts)) {
        return '<p>You have no saved posts yet.</p>';
    }
    
    $args = array(
        'post__in' => $saved_posts,
        'posts_per_page' => -1,
    );
    
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<p>No saved posts found.</p>';
    }
    
    $output = '<div class="saved-posts row">';
    
    while ($query->have_posts()) {
        $query->the_post();
        
        $output .= '<div class="col-md-4">';
        $output .= '<article class="news-card">';
        
        if (has_post_thumbnail()) {
            $output .= '<a href="' . get_permalink() . '" class="ajax-link">';
            $output .= get_the_post_thumbnail(get_the_ID(), 'news-thumb');
            $output .= '</a>';
        }
        
        $output .= '<div class="news-card-body">';
        $output .= '<h3 class="news-card-title"><a href="' . get_permalink() . '" class="ajax-link">' . get_the_title() . '</a></h3>';
        $output .= '<p>' . get_the_excerpt() . '</p>';
        $output .= '</div>';
        $output .= '</article>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('saved_posts', 'ajax_news_saved_posts_shortcode');
