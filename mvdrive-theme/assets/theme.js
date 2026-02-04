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
    // Hero Slider Functionality
    // ========================================
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        const prevBtn = heroSlider.querySelector('.hero-prev');
        const nextBtn = heroSlider.querySelector('.hero-next');
        const slides = heroSlider.querySelectorAll('.hero-banner');
        const dots = document.querySelectorAll('.hero-dots .dot');
        let currentSlide = 0;
        const totalSlides = slides.length;
        
        function showSlide(index) {
            // Remove active class from all slides and dots
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            // Add active class to current slide and dot
            if (slides[index]) {
                slides[index].classList.add('active');
            }
            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }
        
        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', prevSlide);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', nextSlide);
        }
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
        
        // Auto-play slider every 5 seconds
        let autoplayInterval = setInterval(nextSlide, 5000);
        
        // Pause autoplay on hover
        heroSlider.addEventListener('mouseenter', function() {
            clearInterval(autoplayInterval);
        });
        
        heroSlider.addEventListener('mouseleave', function() {
            autoplayInterval = setInterval(nextSlide, 5000);
        });
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
