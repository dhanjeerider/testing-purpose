/**
 * Layout Switcher - Grid view only (simplified)
 * Normal page navigation (no AJAX)
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Grid layout is default and only option
        const defaultLayout = 'grid';
        
        // Apply grid layout
        applyGridLayout();
        
        function applyGridLayout() {
            const $container = $('.posts-container');
            const $mainContent = $('#main-content, .posts-container .row');
            
            // Ensure grid layout
            $container.addClass('grid-layout');
            $mainContent.addClass('row');
            
            // Ensure proper column classes
            $mainContent.find('.col-12').each(function() {
                if (!$(this).find('.pagination-wrapper').length && 
                    !$(this).find('.no-posts-found').length &&
                    !$(this).find('.no-results').length) {
                    $(this).removeClass('col-12').addClass('col-md-6');
                }
            });
            
            // Ensure material cards are flex columns
            $('.material-card').each(function() {
                $(this).addClass('d-flex flex-column');
            });
        }
        
        // Like Button Functionality
        $(document).on('click', '.like-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const postId = $btn.data('post-id');
            const $count = $btn.find('.like-count');
            const $icon = $btn.find('i');
            
            // Toggle liked state
            if ($btn.hasClass('liked')) {
                $btn.removeClass('liked');
                $icon.removeClass('fas').addClass('far');
                let currentCount = parseInt($count.text()) || 0;
                $count.text(Math.max(0, currentCount - 1));
                
                // Save to localStorage
                let likes = JSON.parse(localStorage.getItem('likedPosts') || '[]');
                likes = likes.filter(id => id !== postId);
                localStorage.setItem('likedPosts', JSON.stringify(likes));
            } else {
                $btn.addClass('liked');
                $icon.removeClass('far').addClass('fas');
                let currentCount = parseInt($count.text()) || 0;
                $count.text(currentCount + 1);
                
                // Save to localStorage
                let likes = JSON.parse(localStorage.getItem('likedPosts') || '[]');
                if (!likes.includes(postId)) {
                    likes.push(postId);
                }
                localStorage.setItem('likedPosts', JSON.stringify(likes));
                
                // Animation
                $btn.addClass('pulse');
                setTimeout(() => $btn.removeClass('pulse'), 300);
            }
        });
        
        // Load saved likes on page load
        function loadSavedLikes() {
            const likedPosts = JSON.parse(localStorage.getItem('likedPosts') || '[]');
            likedPosts.forEach(postId => {
                const $btn = $(`.like-btn[data-post-id="${postId}"]`);
                if ($btn.length) {
                    $btn.addClass('liked');
                    $btn.find('i').removeClass('far').addClass('fas');
                    const $count = $btn.find('.like-count');
                    let currentCount = parseInt($count.text()) || 0;
                    if (!$btn.data('loaded')) {
                        $count.text(currentCount + 1);
                        $btn.data('loaded', true);
                    }
                }
            });
        }
        
        loadSavedLikes();
        
        // Smooth scroll for anchor links only
        $('a[href^="#"]').on('click', function(e) {
            const href = $(this).attr('href');
            if (href !== '#' && $(href).length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $(href).offset().top - 100
                }, 600);
            }
        });
        
    });
    
})(jQuery);
