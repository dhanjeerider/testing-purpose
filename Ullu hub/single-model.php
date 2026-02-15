<?php
/**
 * Single post template for series
 *
 * Displays video player, meta information, episodes navigation, models
 * associated, description, tags, related series and comments. The HTML
 * structure matches the original design provided by MXSeries.
 *
 * @package MXSeries HTML Theme
 */

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
    <?php
    $post_id      = get_the_ID();
    $poster       = get_the_post_thumbnail_url( $post_id, 'full' );
    $video_url    = get_post_meta( $post_id, '_mxseries_video_url', true );
    $view_count   = get_post_meta( $post_id, '_mxseries_view_count', true );
    $view_count   = $view_count ? intval( $view_count ) : 0;
    $likes_count  = get_post_meta( $post_id, '_mxseries_likes_count', true );
    $likes_count  = $likes_count ? intval( $likes_count ) : rand( 100, 500 );
    $dislikes_count = get_post_meta( $post_id, '_mxseries_dislikes_count', true );
    $dislikes_count = $dislikes_count ? intval( $dislikes_count ) : rand( 10, 50 );
    $ott_terms    = wp_get_post_terms( $post_id, 'ott' );
    $categories   = get_the_category();
    $associated_models = mxseries_get_associated_models( $post_id );
    
    // Detect video type
    $is_iframe = strpos( $video_url, '<iframe' ) !== false || strpos( $video_url, 'iframe' ) !== false;
    ?>
    <div class="content">
        <div class="stickyvideo">
            <div class="video-container" id="video-wrapper">
                <?php if ( ! empty( $video_url ) ) : ?>
                    <?php if ( $is_iframe ) : ?>
                        <!-- iFrame Video -->
                        <div id="iframe-container" class="iframe-video-wrapper">
                            <?php echo $video_url; ?>
                        </div>
                    <?php else : ?>
                        <!-- MP4 Video -->
                        <video id="my-video" width="100%" poster="<?php echo esc_url( $poster ); ?>" controls class="br-lazy entered br-loaded" data-ll-status="loaded">
                            <source id="video-source" data-breeze="<?php echo esc_url( $video_url ); ?>" type="video/mp4" src="<?php echo esc_url( $video_url ); ?>" />
                            <?php echo __( 'Your browser does not support HTML video.', 'mxseries-html-theme' ); ?>
                        </video>
                    <?php endif; ?>
                    <div id="svplay" class="svplay">
                        <!-- Play icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M21.409 9.353a2.998 2.998 0 0 1 0 5.294L8.597 21.614C6.534 22.737 4 21.277 4 18.968V5.033c0-2.31 2.534-3.769 4.597-2.648z"></path></svg>
                    </div>
                    <div id="related-posts" class="related-posts">
                        <div class="related-posts-inner">
                            <?php
                            // Display related posts from same categories (excluding current post).
                            $related_query = new WP_Query( array(
                                'post_type'      => 'post',
                                'posts_per_page' => 6,
                                'post__not_in'   => array( $post_id ),
                                'category__in'   => wp_list_pluck( $categories, 'term_id' ),
                            ) );
                            if ( $related_query->have_posts() ) {
                                while ( $related_query->have_posts() ) {
                                    $related_query->the_post();
                                    $time_ago = mxseries_relative_time( get_the_date( 'U' ) );
                                    echo '<div><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . ' <span class="clock">' . esc_html( $time_ago ) . '</span></a></div>';
                                }
                            }
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="no-video-message">
                        <p><?php _e( 'No video available for this series.', 'mxseries-html-theme' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Like/Dislike Buttons -->
            <?php if ( ! empty( $video_url ) ) : ?>
            <div class="like-dislike-section" style="padding: 20px; text-align: center; background: #f5f5f5; margin-top: 10px; border-radius: 8px;">
                <button id="like-btn" class="reaction-btn like-btn" data-post-id="<?php echo esc_attr( $post_id ); ?>" style="background: #4CAF50; color: white; border: none; padding: 12px 30px; margin: 0 10px; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold; transition: all 0.3s;">
                    👍 <span id="like-count"><?php echo esc_html( $likes_count ); ?></span>
                </button>
                <button id="dislike-btn" class="reaction-btn dislike-btn" data-post-id="<?php echo esc_attr( $post_id ); ?>" style="background: #f44336; color: white; border: none; padding: 12px 30px; margin: 0 10px; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold; transition: all 0.3s;">
                    👎 <span id="dislike-count"><?php echo esc_html( $dislikes_count ); ?></span>
                </button>
            </div>
            <style>
                .reaction-btn:hover {
                    transform: scale(1.1);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                }
                .reaction-btn:active {
                    transform: scale(0.95);
                }
            </style>
            <?php endif; ?>
        </div>
        <div class="des">
            <h1 class="title">
                <?php the_title(); ?> <?php _e( 'Web Series', 'mxseries-html-theme' ); ?>
            </h1>
            <span class="metad">
                <?php
                // OTT links.
                if ( ! empty( $ott_terms ) && ! is_wp_error( $ott_terms ) ) {
                    foreach ( $ott_terms as $term ) {
                        echo '<a class="srs" href="' . esc_url( get_term_link( $term ) ) . '" rel="Series"> ' . esc_html( $term->name ) . '</a>';
                    }
                }
                // Categories.
                foreach ( $categories as $cat ) {
                    echo '<a class="fold" href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
                }
                // Relative time.
                echo '<div class="clock">' . esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ) . '</div>';
                // View count.
                echo '<div class="eye">' . esc_html( $view_count ) . ' ' . __( 'Views', 'mxseries-html-theme' ) . '</div>';
                ?>
                <button id="shareBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="white" d="m21 12l-7-7v4C7 10 4 15 3 20c2.5-3.5 6-5.1 11-5.1V19z"></path></svg> <?php _e( 'Share', 'mxseries-html-theme' ); ?>
                </button>
            </span>

            <div class="models scrollx">
                <?php
                if ( ! empty( $associated_models ) ) {
                    foreach ( $associated_models as $term ) {
                        $image_url   = get_term_meta( $term->term_id, 'mxseries_model_image', true );
                        $series_cnt  = get_term_meta( $term->term_id, 'mxseries_series_count', true );
                        $series_cnt  = $series_cnt ? intval( $series_cnt ) : 0;
                        $model_views = get_term_meta( $term->term_id, 'mxseries_model_view_count', true );
                        $model_views = $model_views ? intval( $model_views ) : 0;
                        $style       = $image_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $image_url ) . "');" : '';
                        echo '<a style="' . esc_attr( $style ) . '" href="' . esc_url( get_term_link( $term ) ) . '" data-aos="zoom-in-up">';
                        echo '<div class="bgb">';
                        echo '<h3>' . esc_html( $term->name ) . '</h3>';
                        echo '<div class="mmeta">';
                        echo '<span class="MCount">';
                        // Icon for model count.
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.562 7a2.132 2.132 0 0 0-2.1-2.5H6.538a2.132 2.132 0 0 0-2.1 2.5M17.5 4.5c.028-.26.043-.389.043-.496a2 2 0 0 0-1.787-1.993C15.65 2 15.52 2 15.26 2H8.74c-.26 0-.391 0-.497.011a2 2 0 0 0-1.787 1.993c0 .107.014.237.043.496" opacity="0.5"></path><path d="M14.581 13.616c.559.346.559 1.242 0 1.588l-3.371 2.09c-.543.337-1.21-.1-1.21-.794v-4.18c0-.693.667-1.13 1.21-.794z"></path><path d="M2.384 13.793c-.447-3.164-.67-4.745.278-5.77C3.61 7 5.298 7 8.672 7h6.656c3.374 0 5.062 0 6.01 1.024c.947 1.024.724 2.605.278 5.769l-.422 3c-.35 2.48-.525 3.721-1.422 4.464c-.897.743-2.22.743-4.867.743h-5.81c-2.646 0-3.97 0-4.867-.743c-.897-.743-1.072-1.983-1.422-4.464z"></path></g></svg> ' . esc_html( $series_cnt ) . '</span>';
                        echo '<span class="CombinedViews eye">' . esc_html( $model_views ) . '</span>';
                        echo '</div>';
                        echo '</div>';
                        echo '</a>';
                    }
                }
                ?>
            </div>

            <hr />

            <details class="det">
                <summary><?php _e( 'Description', 'mxseries-html-theme' ); ?></summary>
                <?php the_content(); ?>
            </details>

            <div class="tags-container">
                <div class="tags" style="max-height: 30px; transition: max-height 0.5s; margin-right: 35px; overflow: hidden;">
                    <?php
                    $post_tags = get_the_tags();
                    if ( $post_tags ) {
                        foreach ( $post_tags as $tag ) {
                            echo '<a class="tag" id="' . esc_attr( $tag->term_id ) . '" href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a> ';
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="related-series" style="padding-left: 5px; max-width: 900px; margin: auto;">
                <h3 class="relt">Related Series</h3>
                <div class="vul-box">
                    <?php
                    // Display related series based on OTT platform or categories.
                    $related_series = new WP_Query( array(
                        'post_type'      => 'post',
                        'posts_per_page' => 4,
                        'post__not_in'   => array( $post_id ),
                        'tax_query'      => array(
                            'relation' => 'OR',
                            array(
                                'taxonomy' => 'ott',
                                'field'    => 'slug',
                                'terms'    => wp_list_pluck( $ott_terms, 'slug' ),
                            ),
                            array(
                                'taxonomy' => 'category',
                                'field'    => 'term_id',
                                'terms'    => wp_list_pluck( $categories, 'term_id' ),
                            ),
                        ),
                    ) );
                    if ( $related_series->have_posts() ) {
                        while ( $related_series->have_posts() ) {
                            $related_series->the_post();
                            $r_id      = get_the_ID();
                            $img_url   = get_the_post_thumbnail_url( $r_id, 'full' );
                            // Use helper to determine episode count for related post (supports embed code posts).
                            $ep_cnt    = mxseries_get_episode_count( $r_id );
                            $views_cnt = get_post_meta( $r_id, '_mxseries_view_count', true );
                            $views_cnt = $views_cnt ? intval( $views_cnt ) : 0;
                            $r_style   = $img_url ? "background-size: cover; background-position: top; background-image: url('" . esc_url( $img_url ) . "');" : '';
                            echo '<a class="godx-box vblock" style="' . esc_attr( $r_style ) . '" title="' . esc_attr( get_the_title() ) . '" href="' . esc_url( get_permalink() ) . '" data-aos="zoom-in-up">';
                            echo '<div class="time srs"> ' . esc_html( $ep_cnt ) . ' ' . __( 'Episodes', 'mxseries-html-theme' ) . '</div>';
                            echo '<div class="top-right eye">' . esc_html( $views_cnt ) . '</div>';
                            echo '<h2 class="vtitle">' . esc_html( get_the_title() ) . ' <span class="clock">' . esc_html( mxseries_relative_time( get_the_date( 'U' ) ) ) . '</span></h2>';
                            echo '</a>';
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>

            <?php
            // Comments
            if ( comments_open() || get_comments_number() ) {
                echo '<div id="comments" class="comments-area">';
                comments_template();
                echo '</div>';
            }
            ?>

        </div> <!-- .des -->
    </div> <!-- .content -->

<?php endwhile; ?>

<?php
/*
 * Pass episode URLs to JavaScript for switching via hash (#season_number).
 * Only output the script if there are episodes.
 */
$episode_js_array = isset( $episodes ) && is_array( $episodes ) ? $episodes : array();

// Force debug output
if ( isset( $_GET['debug'] ) ) {
    echo '<!-- DEBUG INFO -->';
    echo '<!-- Post ID: ' . $post_id . ' -->';
    echo '<!-- Episodes Count: ' . count( $episode_js_array ) . ' -->';
    echo '<!-- Episodes Data: ' . print_r( $episode_js_array, true ) . ' -->';
    echo '<!-- Raw Meta: ' . print_r( get_post_meta( $post_id, '_mxseries_episodes_data', true ), true ) . ' -->';
    echo '<!-- END DEBUG -->';
}
?>

<script>
console.log('PHP Episodes passed to JS:', <?php echo json_encode( $episode_js_array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>);
</script>

<script>
(function() {
    'use strict';
    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEpisodeSwitcher);
    } else {
        initEpisodeSwitcher();
    }
    
    function initEpisodeSwitcher() {
        // Episode data with name, URL and type
        var episodes = <?php echo json_encode( array_values( $episode_js_array ), JSON_UNESCAPED_SLASHES ); ?>;
        var videoWrapper = document.getElementById('video-wrapper');
        var pillButtons = document.querySelectorAll('.pill-btn');
        var prevButton = document.getElementById('prev-episode');
        var nextButton = document.getElementById('next-episode');
        var currentEpisodeIndex = 0;
        
        // Debug logging
        console.log('=== Episode Switcher Debug Info ===');
        console.log('Total episodes:', episodes.length);
        console.log('Episodes data:', episodes);
        console.log('Video wrapper:', videoWrapper ? 'Found' : 'NOT FOUND');
        console.log('Pill buttons:', pillButtons.length);
        
        // Exit early if required elements don't exist
        if (!videoWrapper || pillButtons.length === 0 || episodes.length === 0) {
            console.error('Episode navigation cannot be initialized:', {
                videoWrapper: !!videoWrapper,
                pillButtonsCount: pillButtons.length,
                episodeCount: episodes.length
            });
            return;
        }
        
        function updateEpisode(index) {
            // Validate index and episode exists
            if (typeof index !== 'number' || index < 0 || index >= episodes.length) {
                console.warn('Invalid episode index:', index);
                return;
            }
            
            var episode = episodes[index];
            if (!episode || !episode.url) {
                console.error('Episode data not found at index:', index);
                return;
            }
            
            console.log('Switching to episode', index + 1, ':', episode.name, '(', episode.type, ')');
            
            // Update pill buttons active state
            pillButtons.forEach(function(btn, idx) {
                if (idx === index) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            // Update current index
            currentEpisodeIndex = index;
            
            // Clear current content
            var container = videoWrapper.querySelector('.video-container') || videoWrapper;
            
            // Remove existing iframe or video
            var existingIframe = container.querySelector('#iframe-container');
            var existingVideo = container.querySelector('#my-video');
            
            if (episode.type === 'iframe') {
                // Show iframe
                if (existingVideo) {
                    existingVideo.style.display = 'none';
                }
                
                if (existingIframe) {
                    existingIframe.innerHTML = episode.url;
                    existingIframe.style.display = 'block';
                } else {
                    var iframeDiv = document.createElement('div');
                    iframeDiv.id = 'iframe-container';
                    iframeDiv.className = 'iframe-video-wrapper';
                    iframeDiv.innerHTML = episode.url;
                    container.insertBefore(iframeDiv, container.firstChild);
                }
            } else {
                // Show MP4 video
                if (existingIframe) {
                    existingIframe.style.display = 'none';
                }
                
                var video = container.querySelector('#my-video');
                var source = container.querySelector('#video-source');
                
                if (video && source) {
                    source.src = episode.url;
                    if (source.hasAttribute('data-breeze')) {
                        source.setAttribute('data-breeze', episode.url);
                    }
                    video.style.display = 'block';
                    video.load();
                } else {
                    console.error('Video elements not found for MP4 playback');
                }
            }
            
            // Update hash (1-based index)
            window.location.hash = '#' + (index + 1);
        }
        
        // On page load, check hash and select the corresponding episode
        var hash = window.location.hash.replace('#', '');
        var initialIndex = parseInt(hash, 10) - 1;
        if (!isNaN(initialIndex) && initialIndex >= 0 && initialIndex < episodes.length) {
            console.log('Loading episode from URL hash:', initialIndex + 1);
            updateEpisode(initialIndex);
        }
        
        // Click event for pill buttons
        pillButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var idx = parseInt(this.getAttribute('data-episode'), 10);
                updateEpisode(idx);
            });
        });
        
        // Prev/Next buttons
        if (prevButton) {
            prevButton.addEventListener('click', function() {
                var idx = currentEpisodeIndex - 1;
                if (idx < 0) idx = 0;
                updateEpisode(idx);
            });
        }
        
        if (nextButton) {
            nextButton.addEventListener('click', function() {
                var idx = currentEpisodeIndex + 1;
                if (idx >= episodes.length) idx = episodes.length - 1;
                updateEpisode(idx);
            });
        }
        
        // Share button: use Web Share API if available, fallback to copy link.
        var shareBtn = document.getElementById('shareBtn');
        if (shareBtn) {
            shareBtn.addEventListener('click', function() {
                var shareData = { title: document.title, url: window.location.href };
                if (navigator.share) {
                    navigator.share(shareData).catch(function(err) {
                        console.warn('Share failed', err);
                    });
                } else if (navigator.clipboard) {
                    navigator.clipboard.writeText(window.location.href).then(function() {
                        alert('Link copied to clipboard');
                    }).catch(function() {
                        // Fallback: prompt with link
                        prompt('Copy this link:', window.location.href);
                    });
                } else {
                    prompt('Copy this link:', window.location.href);
                }
            });
        }
        
        console.log('Episode switcher fully initialized');
    }
})();
</script>

<?php get_footer(); ?>