jQuery(document).ready(function ($) {

    let redirect_time_out_ID;

    $('.submit_button').removeClass('is-disabled');

    $('form#reset-password #password2').on("cut copy paste", function (e) {
        e.preventDefault();
    });

    $('form#reset-password #password2').on("contextmenu", function () {
        return false;
    });


    $('form#reset-password #password1').on("keyup", function () {

        var password = $(this).val();
        var targetContainer = $('.error-list-checker');

        if (/^(?=.*[A-Z]{1})/.test(password)) {
            targetContainer.find('#check-uppercase').addClass('check');
        } else {
            targetContainer.find('#check-uppercase').removeClass('check');
        }

        if (/^(?=.*[a-z]{1})/.test(password)) {
            targetContainer.find('#check-lowercase').addClass('check');
        } else {
            targetContainer.find('#check-lowercase').removeClass('check');
        }

        if (/^(?=.*[0-9]{1})/.test(password)) {
            targetContainer.find('#check-digit').addClass('check');
        } else {
            targetContainer.find('#check-digit').removeClass('check');
        }

        if (/^(?=.*[@#!"?$%^&)]{1})/.test(password)) {
            targetContainer.find('#check-symbol').addClass('check');
        } else {
            targetContainer.find('#check-symbol').removeClass('check');
        }

        if (password.length >= 8) {
            targetContainer.find('#check-length').addClass('check');
        } else {
            targetContainer.find('#check-length').removeClass('check');
        }


    });

    /**
     * JQUERY VALIDATE METHODS
     */

    $.validator.addMethod("password_check", function (value) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#!"?$%^&)])(?=.{8,})/.test(value);
    });


    $("form#reset-password").validate({
        errorPlacement: function (error, element) {
            return false;
        },
        debug: false,
        errorClass: 'field-error',
        validClass: 'field-success',
        rules: {
            "password1": {
                required: true,
                //minlength: 8,
                password_check: true,
            },
            "password2": {
                required: true,
                //minlength: 12,
                equalTo: "#password1"
            }
        },
        submitHandler: function (form) {
            console.info('The reset password form has been submited');

            var currentUrl = new URL(window.location.href );

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_reset_object.ajaxurl,
                data: {
                    'action': 'ajaxreset', //calls wp_ajax_nopriv_ajaxlogin
                    'rp_key': currentUrl.searchParams.get("key"),
                    'rp_login': currentUrl.searchParams.get("login"),
                    'password': $('form#reset-password #password1').val(),
                    'security': $('form#reset-password #security').val()
                },
                beforeSend : function(){
                    $('form#reset-password button.submit_button').addClass('load is-disabled');
                },
                success: function(data){

                    console.debug(data)

                    $('form#reset-password button.submit_button').removeClass('load is-disabled');

                    let alert_data = {type: data.alert_type, message: data.message};
                    manageAlerts(alert_data);

                    redirect_time_out_ID = setTimeout(function () {
                        document.location.href = data.redirect_url;
                        window.clearTimeout(redirect_time_out_ID);

                    }, 4000);

                    /*if (data.action !== 'fail') {


                    }else if (data.action === 'fail') {

                    }*/
                }
            });

        }
    });

});