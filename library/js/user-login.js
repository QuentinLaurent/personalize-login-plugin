jQuery(document).ready(function ($) {

    let redirect_time_out_ID;

    $('#go-to-register-form').on('click',function(evt){

        evt.preventDefault();

        $target = $(this).attr('href');
        $target = $target.substring(1,$target.length);
        scrollToAnchor($target,250);
    });
    //scrollToAnchor();

    $('.submit_button').removeClass('is-disabled');

    /*jQuery.validator.methods.email = function( value, element ) {
        return this.optional( element ) || /[a-z0-9]+@[a-z0-9-]+\.[a-z]+/.test( value );
    };*/

    jQuery.validator.addMethod("emailCustom", function(value, element) {
        return this.optional( element ) || /[a-z0-9]+@[a-z0-9-]+\.[a-z]+/.test( value );
    }, 'Veuillez renseigner un format email valides.');

    $("form#login").validate({
        errorPlacement: function(error, element) { return false;  },
        debug: false,
        errorClass : 'field-error',
        validClass : 'field-success',
        rules: {
            "username": {
                required: true,
                emailCustom : true
            },
            "password": {
                required: true
            },
        },
        submitHandler: function(form) {
            console.info('The login form has been submitted');

            /**
             * Perform AJAX Login
             */

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_login_object.ajaxurl,
                data: {
                    'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                    'username': $('form#login #username').val(),
                    'password': $('form#login #password').val(),
                    'security': $('form#login #security').val()
                },
                beforeSend : function(){
                    $('form#login button.submit_button').addClass('load is-disabled');
                },
                success: function(data){

                    $('form#login button.submit_button').removeClass('load is-disabled');
                    //$('form#login p.status').html(data.message);

                    let alert_data = {type: data.alert_type, message: data.message};
                    manageAlerts(alert_data);

                    if (data.action !== 'fail') {

                        redirect_time_out_ID = setTimeout(function () {

                            document.location.href = data.redirect_url;

                            window.clearTimeout(redirect_time_out_ID);

                        }, 4000);

                    }
                }
            });

        }

    });

});