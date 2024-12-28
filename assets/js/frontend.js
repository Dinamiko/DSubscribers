document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-validation-unsubscribe');

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        const data = new FormData();
        data.append('action', 'dsubscribers_ajax');
        data.append('dsubscribers_action', document.getElementById('dsubscribers_action').value);
        data.append('dsubscribers_email', document.getElementById('dsubscribers_email').value);
        data.append('dsubscribers_nonce', document.getElementById('dsubscribers_form_nonce').value);

        fetch(dsubscribers_data.ajax_url, {
            method: 'POST',
            body: data
        }).then((response) => {
            return response.json();
        }).then((data) => {
            document.getElementById('dsubscribers_msg').innerHTML = data.msg;
            document.getElementById('dsubscribers_email').value = '';
        })
    })
});

/*
jQuery(document).ready(function ($) {

    $("#form-validation").submit(function (e) {

        e.preventDefault();

        var dsubscribers_action = $(this).find('#dsubscribers_action').val();
        var dsubscribers_email = $(this).find('#dsubscribers_email').val();
        var dsubscribers_nonce = $('#dsubscribers_email').val();

        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'dsubscribers_ajax',
                dsubscribers_action: dsubscribers_action,
                dsubscribers_email: dsubscribers_email,
                dsubscribers_nonce: dsubscribers_nonce
            },

            success: function (response) {

                if (response.type == 'success') {

                    $('#dsubscribers_msg').html(response.msg);
                    $('#dsubscribers_email').val('');


                } else {

                    $('#dsubscribers_msg').html(response.msg);
                    $('#dsubscribers_email').val('');

                }

            }

        });


    });

    $("#form-validation-widget").submit(function (e) {

        e.preventDefault();


        //var ajaxurl = $(this).attr('action');
        var dsubscribers_action = $(this).find('#dsubscribers_action').val();
        var dsubscribers_email = $(this).find('#dsubscribers_email').val();
        //var dsubscribers_nonce = $(this).find('#dsubscribers_nonce').val();
        var dsubscribers_nonce = $('#dsubscribers_form_nonce').val();

        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'dsubscribers_ajax',
                dsubscribers_action: dsubscribers_action,
                dsubscribers_email: dsubscribers_email,
                dsubscribers_nonce: dsubscribers_nonce
            },

            success: function (response) {

                if (response.type == 'success') {

                    $('#dsubscribers_msg_widget').html(response.msg);
                    $('#dsubscribers_email_widget').val('');


                } else {

                    $('#dsubscribers_msg_widget').html(response.msg);
                    $('#dsubscribers_email_widget').val('');

                }

            }

        });


    });


    $("#form-validation-unsubscribe").submit(function (e) {

        e.preventDefault();

        var dsubscribers_action = $(this).find('#dsubscribers_action').val();
        var dsubscribers_email = $(this).find('#dsubscribers_email').val();
        var dsubscribers_nonce = $('#dsubscribers_form_nonce').val();

        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'dsubscribers_ajax',
                dsubscribers_action: dsubscribers_action,
                dsubscribers_email: dsubscribers_email,
                dsubscribers_nonce: dsubscribers_nonce
            },

            success: function (response) {

                if (response.type == 'success') {

                    $('#dsubscribers_unsubscribe_msg').html(response.msg);
                    $('#dsubscribers_email').val('');


                } else {

                    $('#dsubscribers_unsubscribe_msg').html(response.msg);
                    $('#dsubscribers_email').val('');

                }

            }

        });


    });


});
*/
