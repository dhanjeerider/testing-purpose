/**
 * Header Enhancements - Search, Notifications, etc.
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Search Popup Toggle
        $('#searchToggle').on('click', function() {
            $('#searchPopup').fadeIn(300);
            $('.search-field-popup').focus();
        });
        
        $('#searchClose, #searchPopup').on('click', function(e) {
            if (e.target === this) {
                $('#searchPopup').fadeOut(300);
            }
        });
        
        // Live Search
        let searchTimeout;
        $('.search-field-popup').on('keyup', function() {
            const query = $(this).val();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 3) {
                $('#searchResults').html('');
                return;
            }
            
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: ajaxNewsTheme.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'live_search',
                        query: query,
                        nonce: ajaxNewsTheme.nonce
                    },
                    beforeSend: function() {
                        $('#searchResults').html('<div class="text-center p-3"><div class="spinner-border" role="status"></div></div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#searchResults').html(response.data.html);
                        }
                    }
                });
            }, 500);
        });
        
        // Notification Bell
        $('#notificationBell').on('click', function() {
            alert('Notifications feature - Coming soon!');
        });
        
        // Dark Mode Toggle (Updated)
        const $darkModeBtn = $('#darkModeToggle');
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        
        if (isDarkMode) {
            $('body').addClass('dark-mode');
            updateDarkModeIcon(true);
        }
        
        $darkModeBtn.on('click', function() {
            $('body').toggleClass('dark-mode');
            const isNowDark = $('body').hasClass('dark-mode');
            localStorage.setItem('darkMode', isNowDark);
            updateDarkModeIcon(isNowDark);
        });
        
        function updateDarkModeIcon(isDark) {
            const $icon = $darkModeBtn.find('i');
            if (isDark) {
                $icon.removeClass('fa-moon').addClass('fa-sun');
            } else {
                $icon.removeClass('fa-sun').addClass('fa-moon');
            }
        }
        
    });
    
})(jQuery);
