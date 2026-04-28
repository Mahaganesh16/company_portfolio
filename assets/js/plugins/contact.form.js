/**
 *
 * -----------------------------------------------------------------------------
 *
 * Template : Reeni Personal Portfolio HTML Template
 * Author : themes-park
 * Author URI : https://themes-park.com/ 
 *
 * -----------------------------------------------------------------------------
 *
 **/

(function ($) {
    'use strict';

    var form = $('#contact-form');
    var formMessages = $('#form-messages');

    $(form).submit(function (e) {
        e.preventDefault();

        // Form data serialize + phone field à¦¯à§à¦•à§à¦¤ à¦•à¦°à¦¾
        var formData = $(form).serialize() + "&phone=" + $('#contact-phone').val();

        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
        })
        .done(function (response) {
            $(formMessages).removeClass('error').addClass('success').text(response);

            // à¦‡à¦¨à¦ªà§à¦Ÿ à¦«à¦¿à¦²à§à¦¡ à¦•à§à¦²à¦¿à§Ÿà¦¾à¦° à¦•à¦°à¦¾
            $('#contact-name, #contact-email, #subject, #contact-message, #contact-phone').val('');
        })
        .fail(function (data) {
            $(formMessages).removeClass('success').addClass('error');

            if (data.responseText !== '') {
                $(formMessages).text(data.responseText);
            } else {
                $(formMessages).text('Oops! An error occurred and your message could not be sent.');
            }
        });
    });

})(jQuery);