/**
 * Dark Mode Toggle - Fixed and Optimized
 */

(function($) {
    'use strict';
    
    // Apply dark mode immediately to prevent flash
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
    }
    
    $(document).ready(function() {
        
        // Update icon based on current state
        updateIcon(isDarkMode);
        
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
                $('#darkModeToggle').attr('title', 'Switch to Light Mode');
            } else {
                $icon.removeClass('fa-sun').addClass('fa-moon');
                $('#darkModeToggle').attr('title', 'Switch to Dark Mode');
            }
        }
        
    });
    
})(jQuery);
