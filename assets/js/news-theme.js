/**
 * News Theme JavaScript
 * Grid layout only with smooth interactions
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Lazy loading for images
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.src = img.dataset.src;
            });
        }
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 600);
            }
        });
        
        // Social share animation
        $('.social-share-buttons a, .quick-share a').on('click', function() {
            const $btn = $(this);
            $btn.addClass('sharing');
            setTimeout(function() {
                $btn.removeClass('sharing');
            }, 1000);
        });
        
        // Widget animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.widget, .material-card').forEach(el => {
            observer.observe(el);
        });
        
        // Sticky sidebar
        const sidebar = $('.main-sidebar');
        if (sidebar.length) {
            const sidebarTop = sidebar.offset().top;
            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop();
                if (scrollTop > sidebarTop - 20) {
                    sidebar.addClass('sticky');
                } else {
                    sidebar.removeClass('sticky');
                }
            });
        }
        
        // Read more button smooth scroll
        $('.btn-primary').on('click', function(e) {
            if ($(this).attr('href').indexOf('#') === 0) {
                e.preventDefault();
                const target = $(this).attr('href');
                if ($(target).length) {
                    $('html, body').animate({
                        scrollTop: $(target).offset().top - 100
                    }, 500);
                }
            }
        });
        
    });
    
})(jQuery);
