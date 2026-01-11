(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Like Button
        $(document).on('click', '.like-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const postId = $btn.data('post-id');
            const $count = $btn.find('.like-count');
            const $icon = $btn.find('i');
            
            if ($btn.hasClass('liked')) {
                $btn.removeClass('liked');
                $icon.removeClass('fas').addClass('far');
                let count = parseInt($count.text()) || 0;
                $count.text(Math.max(0, count - 1));
                
                let likes = JSON.parse(localStorage.getItem('likedPosts') || '[]');
                likes = likes.filter(id => id != postId);
                localStorage.setItem('likedPosts', JSON.stringify(likes));
            } else {
                $btn.addClass('liked');
                $icon.removeClass('far').addClass('fas');
                let count = parseInt($count.text()) || 0;
                $count.text(count + 1);
                
                let likes = JSON.parse(localStorage.getItem('likedPosts') || '[]');
                if (!likes.includes(postId)) {
                    likes.push(postId);
                }
                localStorage.setItem('likedPosts', JSON.stringify(likes));
            }
        });
        
        // Load saved likes
        const likedPosts = JSON.parse(localStorage.getItem('likedPosts') || '[]');
        likedPosts.forEach(postId => {
            const $btn = $('.like-btn[data-post-id="' + postId + '"]');
            if ($btn.length && !$btn.data('loaded')) {
                $btn.addClass('liked');
                $btn.find('i').removeClass('far').addClass('fas');
                const $count = $btn.find('.like-count');
                $count.text(parseInt($count.text()) + 1);
                $btn.data('loaded', true);
            }
        });
        
    });
    
})(jQuery);
