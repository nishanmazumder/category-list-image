jQuery(document).ready(function($){

    var cli_media_frame;

    if(! $('#cli_upload_image_url').val()){
        $('.cli_image_remove').hide();
    }

    $('#cli_upload_image_btn').click(function(){
        // e.preventDefault();

        if ( cli_media_frame ) {
            cli_media_frame.open();
            return;
        }

        cli_media_frame = wp.media.frames.cli_media_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });

        cli_media_frame.on('select', function(){
            var media_attachment = cli_media_frame.state().get('selection').first().toJSON();
            $('#cli_upload_image_url').val(media_attachment.url);
            $('#cli_upload_image_btn').attr('src', media_attachment.url);
            $('.cli_image_remove').show();
        });

        cli_media_frame.open();
    });

    $('.cli_image_remove').on('click', function(){
        $('#cli_upload_image_btn').attr('src', meta_image.placeholder);
    })

});