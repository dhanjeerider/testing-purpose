/**
 * Reactions - Like, Dislike, Save functionality
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Handle reaction button clicks
        $(document).on('click', '.reaction-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const postId = $btn.data('post-id');
            const reaction = $btn.data('reaction');
            
            // Disable button temporarily
            $btn.prop('disabled', true);
            
            // Send AJAX request
            $.ajax({
                url: reactionsData.ajax_url,
                type: 'POST',
                data: {
                    action: 'handle_reaction',
                    post_id: postId,
                    reaction: reaction,
                    nonce: reactionsData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update button state
                        const $container = $btn.parent();
                        
                        if (reaction === 'like' || reaction === 'dislike') {
                            // Remove active class from both like and dislike
                            $container.find('[data-reaction="like"], [data-reaction="dislike"]').removeClass('active liked disliked');
                        }
                        
                        // Update counts
                        $btn.find('.count').text(response.data.count);
                        
                        // Add active class if user reacted
                        if (response.data.user_reacted) {
                            $btn.addClass('active');
                            if (reaction === 'like') {
                                $btn.addClass('liked');
                            } else if (reaction === 'dislike') {
                                $btn.addClass('disliked');
                            } else if (reaction === 'save') {
                                $btn.addClass('saved');
                            }
                        }
                        
                        // Show notification
                        showNotification(response.data.message);
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        
        // Show notification
        function showNotification(message, type = 'success') {
            const $notification = $('<div class="reaction-notification"></div>')
                .addClass(type)
                .text(message)
                .css({
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    padding: '15px 20px',
                    background: type === 'success' ? '#28a745' : '#dc3545',
                    color: 'white',
                    borderRadius: '5px',
                    zIndex: 9999,
                    boxShadow: '0 5px 15px rgba(0,0,0,0.3)'
                });
            
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
    });
    
})(jQuery);
