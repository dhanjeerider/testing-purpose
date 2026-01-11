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
            
            let likes = JSON.parse(localStorage.getItem('likes') || '{}');
            
            if (likes[postId]) {
                delete likes[postId];
                $btn.removeClass('liked');
                $icon.removeClass('fas').addClass('far');
                let count = parseInt($count.text()) || 0;
                $count.text(Math.max(0, count - 1));
            } else {
                likes[postId] = true;
                $btn.addClass('liked');
                $icon.removeClass('far').addClass('fas');
                let count = parseInt($count.text()) || 0;
                $count.text(count + 1);
            }
            
            localStorage.setItem('likes', JSON.stringify(likes));
        });
        
        // Load saved likes
        const likes = JSON.parse(localStorage.getItem('likes') || '{}');
        Object.keys(likes).forEach(postId => {
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
