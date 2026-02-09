/**
 * Main JavaScript file for mvdrive theme
 * Handles mobile menu, hero slider, notice close, and all interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // Mobile Menu Functionality
    // ========================================
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sideMenu = document.getElementById('sideMenu');
    const menuOverlay = document.getElementById('menuOverlay');
    const closeMenuBtn = document.getElementById('closeMenuBtn');
    
    if (hamburgerBtn && sideMenu && menuOverlay) {
        hamburgerBtn.addEventListener('click', function() {
            sideMenu.style.left = '0';
            menuOverlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
        
        function closeMenu() {
            sideMenu.style.left = '-100%';
            menuOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        if (closeMenuBtn) {
            closeMenuBtn.addEventListener('click', closeMenu);
        }
        
        menuOverlay.addEventListener('click', closeMenu);
    }
    
    // ========================================
    // Notice Close Button
    // ========================================
    const noticeClose = document.getElementById('noticeClose');
    if (noticeClose) {
        noticeClose.addEventListener('click', function() {
            const noticeSection = this.closest('.notice-section');
            if (noticeSection) {
                noticeSection.style.display = 'none';
                // Save to localStorage
                localStorage.setItem('mvdrive_notice_closed', 'true');
            }
        });
        
        // Check if notice was previously closed
        if (localStorage.getItem('mvdrive_notice_closed') === 'true') {
            const noticeSection = document.querySelector('.notice-section');
            if (noticeSection) {
                noticeSection.style.display = 'none';
            }
        }
    }
    
    // ========================================
    // Hero Slider - Thumbnail Auto Slide
    // ========================================
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        const slides = heroSlider.querySelectorAll('.hero-banner');
        
        if (slides.length > 0) {
            let currentIndex = 0;
            let autoSlideInterval;
            
            // Auto scroll function
            function autoScroll() {
                currentIndex++;
                if (currentIndex >= slides.length) {
                    currentIndex = 0;
                    heroSlider.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    const slideWidth = slides[0].offsetWidth;
                    const gap = 16; // 1rem gap
                    heroSlider.scrollTo({
                        left: (slideWidth + gap) * currentIndex,
                        behavior: 'smooth'
                    });
                }
            }
            
            // Start auto slide every 3 seconds
            autoSlideInterval = setInterval(autoScroll, 3000);
            
            // Pause on hover
            heroSlider.addEventListener('mouseenter', function() {
                clearInterval(autoSlideInterval);
            });
            
            // Resume on mouse leave
            heroSlider.addEventListener('mouseleave', function() {
                autoSlideInterval = setInterval(autoScroll, 3000);
            });
            
            // Pause on touch/scroll
            heroSlider.addEventListener('touchstart', function() {
                clearInterval(autoSlideInterval);
            });
            
            heroSlider.addEventListener('scroll', function() {
                clearInterval(autoSlideInterval);
                // Resume after 2 seconds of no scrolling
                setTimeout(function() {
                    autoSlideInterval = setInterval(autoScroll, 3000);
                }, 2000);
            });
        }
        
        // Remove dots and navigation if they exist
        const dotsContainer = document.querySelector('.hero-dots');
        if (dotsContainer) {
            dotsContainer.remove();
        }
        
        const prevBtn = heroSlider.querySelector('.hero-prev');
        const nextBtn = heroSlider.querySelector('.hero-next');
        if (prevBtn) prevBtn.remove();
        if (nextBtn) nextBtn.remove();
    }
    
    // ========================================
    // Submenu Toggle for Mobile
    // ========================================
    const hasSubmenuItems = document.querySelectorAll('.side-nav-item.has-submenu > .side-nav-link');
    hasSubmenuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const parent = this.parentElement;
                const submenu = parent.querySelector('.side-submenu');
                
                if (submenu) {
                    // Toggle submenu
                    const isOpen = submenu.style.display === 'block';
                    submenu.style.display = isOpen ? 'none' : 'block';
                    
                    // Rotate arrow icon
                    const arrow = this.querySelector('.dropdown-icon');
                    if (arrow) {
                        arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                }
            }
        });
    });
    
    // ========================================
    // Smooth Scroll for Anchor Links
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // ========================================
    // Lazy Loading Images Enhancement
    // ========================================
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.src;
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
        document.body.appendChild(script);
    }
    
    // ========================================
    // Back to Top Button
    // ========================================
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12L12 5L19 12" stroke="currentColor" stroke-width="2"/></svg>';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.setAttribute('aria-label', 'Back to top');
    document.body.appendChild(backToTopBtn);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // ========================================
    // Search Enhancement
    // ========================================
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    }
    
    // ========================================
    // Add Loading Animation
    // ========================================
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });
});
