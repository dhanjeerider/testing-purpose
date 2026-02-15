(function($) {
    $(document).ready(function() {
        /**
         * Generic media uploader helper. When the user clicks the upload button,
         * open the WordPress media frame and set the selected image URL on the
         * associated input field.
         *
         * @param {string} buttonSelector Selector for the button triggering the media modal.
         * @param {string} inputSelector  Selector for the input field to receive the URL.
         */
        function addMediaHandler(buttonSelector, inputSelector) {
            $(document).on('click', buttonSelector, function(e) {
                e.preventDefault();
                // Attempt to locate the associated input field in a robust manner.
                var parent = $(this).closest('.form-field, tr');
                var inputField = parent.find(inputSelector);
                if (!inputField.length) {
                    // Fallback: look among siblings.
                    inputField = $(this).siblings(inputSelector);
                }
                var frame = wp.media({
                    title: 'Select or Upload Image',
                    button: {
                        text: 'Use this image'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                });
                frame.open();
            });
        }
        // Attach handlers for model and ott image fields.
        addMediaHandler('.mxseries-upload-model-image', '#mxseries_model_image');
        addMediaHandler('.mxseries-upload-ott-image', '#mxseries_ott_thumbnail');
    });
})(jQuery);