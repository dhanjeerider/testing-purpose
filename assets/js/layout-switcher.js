/**
 * Layout Switcher - Grid/List view toggle
 * Works with archive.php, search.php and other listing pages
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Get saved layout preference
        const savedLayout = localStorage.getItem('postLayout') || 'grid';
        
        // Apply saved layout
        applyLayout(savedLayout);
        
        // Handle layout button clicks
        $('.layout-btn').on('click', function() {
            const layout = $(this).data('layout');
            
            // Update button states
            $('.layout-btn').removeClass('active');
            $(this).addClass('active');
            
            // Apply layout
            applyLayout(layout);
            
            // Save preference
            localStorage.setItem('postLayout', layout);
            
            // Smooth transition
            $('.posts-container').css('opacity', '0.5');
            setTimeout(function() {
                $('.posts-container').css('opacity', '1');
            }, 300);
        });
        
        function applyLayout(layout) {
            const $container = $('.posts-container');
            const $mainContent = $('#main-content');
            
            // Update button state
            $('.layout-btn').removeClass('active');
            $(`.layout-btn[data-layout="${layout}"]`).addClass('active');
            
            if (layout === 'list') {
                // List layout
                $container.removeClass('grid-layout').addClass('list-layout');
                $mainContent.removeClass('row');
                
                // Convert grid items to list
                $mainContent.find('.col-md-6').each(function() {
                    $(this).removeClass('col-md-6').addClass('col-12');
                });
                
                // Adjust card structure for list view
                $mainContent.find('.material-card').each(function() {
                    const $card = $(this);
                    $card.addClass('d-flex flex-row');
                    
                    const $thumbnail = $card.find('.post-thumbnail');
                    if ($thumbnail.length) {
                        $thumbnail.css({
                            'flex': '0 0 300px',
                            'max-width': '300px'
                        });
                    }
                });
                
            } else {
                // Grid layout
                $container.removeClass('list-layout').addClass('grid-layout');
                $mainContent.addClass('row');
                
                // Convert list items to grid
                $mainContent.find('.col-12').each(function() {
                    if (!$(this).find('.pagination-wrapper').length && 
                        !$(this).find('.no-posts-found').length &&
                        !$(this).find('.no-results').length) {
                        $(this).removeClass('col-12').addClass('col-md-6');
                    }
                });
                
                // Reset card structure for grid view
                $mainContent.find('.material-card').each(function() {
                    const $card = $(this);
                    $card.removeClass('d-flex flex-row');
                    $card.addClass('d-flex flex-column');
                    
                    const $thumbnail = $card.find('.post-thumbnail');
                    if ($thumbnail.length) {
                        $thumbnail.css({
                            'flex': '',
                            'max-width': ''
                        });
                    }
                });
            }
        }
        
        // Smooth scroll for read more buttons
        $('.btn-primary').on('click', function(e) {
            if ($(this).attr('href').indexOf('#') === 0) {
                e.preventDefault();
                const target = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, 500);
            }
        });
        
        // Add loading animation for social share buttons
        $('.social-share-buttons a, .quick-share a').on('click', function() {
            const $btn = $(this);
            $btn.addClass('sharing');
            setTimeout(function() {
                $btn.removeClass('sharing');
            }, 1000);
        });
        
    });
    
})(jQuery);
