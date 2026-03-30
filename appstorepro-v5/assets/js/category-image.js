/* Category Image Uploader */
(function ($) {
  'use strict';

  var mediaFrame;

  $(document).on('click', '#appstorepro-category-image-btn', function (e) {
    e.preventDefault();

    // Create media frame if it doesn't exist
    if (!mediaFrame) {
      mediaFrame = wp.media({
        title: 'Select Category Image',
        button: {
          text: 'Use This Image'
        },
        multiple: false
      });

      mediaFrame.on('select', function () {
        var attachment = mediaFrame.state().get('selection').first().toJSON();
        $('#appstorepro-category-image').val(attachment.id);
        $('#appstorepro-category-image-preview')
          .attr('src', attachment.sizes.thumbnail.url)
          .show();
        $('#appstorepro-category-image-remove').show();
      });
    }

    mediaFrame.open();
  });

  $(document).on('click', '#appstorepro-category-image-remove', function (e) {
    e.preventDefault();
    $('#appstorepro-category-image').val('');
    $('#appstorepro-category-image-preview').hide();
    $(this).hide();
  });
})(jQuery);
