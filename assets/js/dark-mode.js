/**
 * Dark Mode Toggle - Simple & Fast
 */

// Apply dark mode immediately (before page load to prevent flash)
(function() {
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
        document.documentElement.classList.add('dark-mode');
        if (document.body) {
            document.body.classList.add('dark-mode');
        }
    }
})();

// Toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('darkModeToggle');
    if (!toggleBtn) return;
    
    // Update icon based on current state
    updateIcon();
    
    // Toggle dark mode on click
    toggleBtn.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const isNowDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isNowDark);
        updateIcon();
    });
    
    function updateIcon() {
        const isDark = document.body.classList.contains('dark-mode');
        const icon = toggleBtn.querySelector('i');
        if (!icon) return;
        
        if (isDark) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            toggleBtn.setAttribute('title', 'Light Mode');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            toggleBtn.setAttribute('title', 'Dark Mode');
        }
    }
});
