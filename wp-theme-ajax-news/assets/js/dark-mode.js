/**
 * Dark Mode Toggle
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Check for saved dark mode preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        
        if (isDarkMode) {
            $('body').addClass('dark-mode');
            updateIcon(true);
        }
        
        // Toggle dark mode
        $('#darkModeToggle').on('click', function() {
            $('body').toggleClass('dark-mode');
            
            const isNowDark = $('body').hasClass('dark-mode');
            localStorage.setItem('darkMode', isNowDark);
            updateIcon(isNowDark);
        });
        
        // Update icon
        function updateIcon(isDark) {
            const $icon = $('#darkModeToggle i');
            if (isDark) {
                $icon.removeClass('fa-moon').addClass('fa-sun');
            } else {
                $icon.removeClass('fa-sun').addClass('fa-moon');
            }
        }
        
    });
    
})(jQuery);
