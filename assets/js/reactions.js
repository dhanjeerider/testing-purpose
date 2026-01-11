/**
 * Reactions - Like, Save functionality with localStorage
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Initialize saved posts from localStorage
        let savedPosts = JSON.parse(localStorage.getItem('savedPosts') || '[]');
        
        // Update UI for saved posts
        updateSavedButtonsUI();
        
        // Handle Like button clicks
        $(document).on('click', '.like-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const postId = $btn.data('post-id');
            
            // Toggle active state
            $btn.toggleClass('active');
            
            // Update count
            let count = parseInt($btn.find('.count').text() || 0);
            if ($btn.hasClass('active')) {
                count++;
                $btn.find('i').removeClass('far').addClass('fas');
            } else {
                count = Math.max(0, count - 1);
                $btn.find('i').removeClass('fas').addClass('far');
            }
            $btn.find('.count').text(count);
            
            // Send AJAX to update server
            $.ajax({
                url: ajaxNewsTheme.ajax_url,
                type: 'POST',
                data: {
                    action: 'handle_like',
                    post_id: postId,
                    nonce: ajaxNewsTheme.nonce
                }
            });
            
            showNotification('❤️ ' + ($btn.hasClass('active') ? 'Liked!' : 'Like removed'));
        });
        
        // Handle Save button clicks
        $(document).on('click', '.save-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const postId = $btn.data('post-id');
            const $card = $btn.closest('.news-card');
            const postTitle = $card.find('.news-card-title a').text().trim();
            const postUrl = $card.find('.news-card-title a').attr('href');
            const postImage = $card.find('img').first().attr('src') || '';
            
            // Check if already saved
            const index = savedPosts.findIndex(p => p.id == postId);
            
            if (index > -1) {
                // Remove from saved
                savedPosts.splice(index, 1);
                $btn.removeClass('active');
                $btn.find('i').removeClass('fas').addClass('far');
                showNotification('📌 Removed from saved posts');
            } else {
                // Add to saved
                savedPosts.push({
                    id: postId,
                    title: postTitle,
                    url: postUrl,
                    image: postImage,
                    date: new Date().toISOString()
                });
                $btn.addClass('active');
                $btn.find('i').removeClass('far').addClass('fas');
                showNotification('📌 Post saved!');
            }
            
            // Update localStorage
            localStorage.setItem('savedPosts', JSON.stringify(savedPosts));
            updateSavedButtonsUI();
        });
        
        // Handle Share button clicks
        $(document).on('click', '.share-btn', function(e) {
            e.preventDefault();
            
            const $card = $(this).closest('.news-card');
            const postTitle = $card.find('.news-card-title a').text().trim();
            const postUrl = $card.find('.news-card-title a').attr('href');
            
            // Check if Web Share API is available
            if (navigator.share) {
                navigator.share({
                    title: postTitle,
                    url: postUrl
                }).catch(() => {});
            } else {
                // Fallback: Copy to clipboard
                const tempInput = document.createElement('input');
                tempInput.value = postUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                showNotification('🔗 Link copied to clipboard!');
            }
        });
        
        // Show Saved Posts Popup
        $('#savedPostsLink').on('click', function(e) {
            e.preventDefault();
            showSavedPostsPopup();
        });
        
        // Close Saved Posts Popup
        $('#savedPostsClose, #savedPostsPopup').on('click', function(e) {
            if (e.target === this) {
                $('#savedPostsPopup').fadeOut(300);
            }
        });
        
        // Remove saved post from popup
        $(document).on('click', '.remove-saved', function() {
            const postId = $(this).data('post-id');
            const index = savedPosts.findIndex(p => p.id == postId);
            
            if (index > -1) {
                savedPosts.splice(index, 1);
                localStorage.setItem('savedPosts', JSON.stringify(savedPosts));
                updateSavedButtonsUI();
                showSavedPostsPopup(); // Refresh popup
                showNotification('📌 Post removed from saved');
            }
        });
        
        // Update saved buttons UI
        function updateSavedButtonsUI() {
            $('.save-btn').each(function() {
                const postId = $(this).data('post-id');
                const isSaved = savedPosts.some(p => p.id == postId);
                
                if (isSaved) {
                    $(this).addClass('active');
                    $(this).find('i').removeClass('far').addClass('fas');
                } else {
                    $(this).removeClass('active');
                    $(this).find('i').removeClass('fas').addClass('far');
                }
            });
        }
        
        // Show saved posts popup
        function showSavedPostsPopup() {
            const $list = $('#savedPostsList');
            $list.empty();
            
            if (savedPosts.length === 0) {
                $list.html(`
                    <div class="no-saved-posts">
                        <i class="far fa-bookmark"></i>
                        <p>No saved posts yet.<br>Start saving posts to read later!</p>
                    </div>
                `);
            } else {
                savedPosts.reverse().forEach(post => {
                    $list.append(`
                        <div class="saved-post-item">
                            ${post.image ? `<img src="${post.image}" class="saved-post-thumb" alt="${post.title}">` : ''}
                            <div class="saved-post-info">
                                <h4><a href="${post.url}" class="ajax-link">${post.title}</a></h4>
                                <p><i class="far fa-clock"></i> Saved ${getTimeAgo(post.date)}</p>
                            </div>
                            <button class="remove-saved" data-post-id="${post.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                });
            }
            
            $('#savedPostsPopup').fadeIn(300);
        }
        
        // Get time ago
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) return 'just now';
            if (diffMins < 60) return `${diffMins} min ago`;
            
            const diffHours = Math.floor(diffMins / 60);
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            
            const diffDays = Math.floor(diffHours / 24);
            return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        }
        
        // Show notification
        function showNotification(message) {
            const $notification = $('<div class="reaction-notification"></div>')
                .text(message)
                .css({
                    position: 'fixed',
                    bottom: '30px',
                    right: '30px',
                    padding: '15px 25px',
                    background: 'var(--primary-color)',
                    color: 'white',
                    borderRadius: '8px',
                    zIndex: 9999,
                    boxShadow: '0 5px 20px rgba(0,0,0,0.3)',
                    fontSize: '14px',
                    fontWeight: '500'
                });
            
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 2500);
        }
        
    });
    
})(jQuery);
