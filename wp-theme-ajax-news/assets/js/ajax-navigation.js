/**
 * AJAX Navigation - Page switching without reload
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Handle all internal links
        $(document).on('click', 'a.ajax-link, .navbar-nav a, .pagination a', function(e) {
            const $link = $(this);
            const url = $link.attr('href');
            
            // Check if it's an internal link
            if (!url || url.indexOf('#') === 0 || url.indexOf('http') === 0 && url.indexOf(ajaxNewsTheme.home_url) !== 0) {
                return;
            }
            
            e.preventDefault();
            
            // Show loader
            $('#ajaxLoader').addClass('loading');
            $('#page-content').removeClass('loaded');
            
            // Update URL without reload
            history.pushState({url: url}, '', url);
            
            // Load content via AJAX
            loadContent(url);
            
            // Scroll to top
            $('html, body').animate({scrollTop: 0}, 400);
        });
        
        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.url) {
                loadContent(e.state.url);
            }
        });
        
        // Initial state
        history.replaceState({url: window.location.href}, '', window.location.href);
        $('#page-content').addClass('loaded');
        
    });
    
    /**
     * Load content via AJAX
     */
    function loadContent(url) {
        $.ajax({
            url: ajaxNewsTheme.ajax_url,
            type: 'POST',
            data: {
                action: 'load_content',
                url: url,
                nonce: ajaxNewsTheme.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update page content
                    $('#main-content').html(response.data.content);
                    
                    // Update page title
                    if (response.data.title) {
                        document.title = response.data.title + ' - ' + $('meta[property="og:site_name"]').attr('content');
                    }
                    
                    // Hide loader
                    $('#ajaxLoader').removeClass('loading');
                    $('#page-content').addClass('loaded');
                    
                    // Reinitialize video players
                    initVideoPlayers();
                    
                    // Trigger custom event
                    $(document).trigger('contentLoaded');
                }
            },
            error: function() {
                // On error, do normal page load
                window.location.href = url;
            }
        });
    }
    
    /**
     * Initialize video players
     */
    function initVideoPlayers() {
        if (typeof Plyr !== 'undefined') {
            const players = Array.from(document.querySelectorAll('.plyr-video')).map(p => new Plyr(p));
        }
    }
    
    // Initialize on page load
    initVideoPlayers();
    
})(jQuery);
