/**
 * Load More Posts
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        let currentPage = 1;
        let isLoading = false;
        
        // Load More Button Click
        $(document).on('click', '#loadMoreBtn', function(e) {
            e.preventDefault();
            
            if (isLoading) return;
            
            const $btn = $(this);
            const container = $btn.data('container') || '#main-content';
            
            currentPage++;
            isLoading = true;
            
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Loading...');
            
            $.ajax({
                url: ajaxNewsTheme.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_posts',
                    paged: currentPage,
                    nonce: ajaxNewsTheme.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $(container).append(response.data.content);
                        
                        if (!response.data.has_more) {
                            $btn.fadeOut();
                        } else {
                            $btn.prop('disabled', false).html('Load More Posts <i class="fas fa-chevron-down"></i>');
                        }
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('Load More Posts <i class="fas fa-chevron-down"></i>');
                    alert('Error loading posts. Please try again.');
                },
                complete: function() {
                    isLoading = false;
                }
            });
        });
        
        // Infinite Scroll (Optional)
        if ($('.infinite-scroll').length) {
            $(window).on('scroll', function() {
                if (isLoading) return;
                
                const scrollTop = $(window).scrollTop();
                const windowHeight = $(window).height();
                const documentHeight = $(document).height();
                
                if (scrollTop + windowHeight >= documentHeight - 500) {
                    $('#loadMoreBtn').trigger('click');
                }
            });
        }
        
        // Load More Related Posts
        $(document).on('click', '#loadMoreRelated', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const shown = parseInt($btn.data('shown'));
            const total = parseInt($btn.data('total'));
            const toShow = 3; // Show 3 more each time
            
            // Get hidden posts
            const $hiddenPosts = $('#hidden-related-posts .hidden-post');
            const newShown = Math.min(shown + toShow, total);
            
            // Show next batch
            $hiddenPosts.slice(0, toShow).each(function() {
                $('#related-posts-container').append($(this).html());
                $(this).remove();
            });
            
            // Update counter
            $btn.data('shown', newShown);
            
            // Hide button if all posts shown
            if (newShown >= total) {
                $btn.fadeOut();
            } else {
                $btn.html(`Load More Related Posts (${total - newShown} remaining) <i class="fas fa-chevron-down"></i>`);
            }
        });
        
    });
    
})(jQuery);
