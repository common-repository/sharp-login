jQuery(document).ready(function() {
    var $ = jQuery;
    if ($('.set_custom_logo').length > 0) {
        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            $(document).on('click', '.set_custom_logo', function(e) {
                e.preventDefault();
                var button = $(this);
                wp.media.editor.open(button);
                var id = button.prev();
                wp.media.editor.send.attachment = function(props, attachment) {
                    id.val(attachment.url);
                };
                return false;
            });
        }
    }
});