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
            const layout = $btn.data('layout') || 'grid';
            const container = $btn.data('container') || '#posts-container';
            
            currentPage++;
            isLoading = true;
            
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Loading...');
            
            $.ajax({
                url: ajaxNewsTheme.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_posts',
                    paged: currentPage,
                    layout: layout,
                    nonce: ajaxNewsTheme.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $(container).append(response.data.content);
                        
                        if (!response.data.has_more) {
                            $btn.remove();
                        } else {
                            $btn.prop('disabled', false).html('Load More <i class="fas fa-chevron-down"></i>');
                        }
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('Load More <i class="fas fa-chevron-down"></i>');
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
        
    });
    
})(jQuery);
