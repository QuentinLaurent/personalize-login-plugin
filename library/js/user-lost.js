jQuery(document).ready(function ($) {

    let redirect_time_out_ID;

    $('.submit_button').removeClass('is-disabled');

    $("form#lost-password").validate({
        errorPlacement: function(error, element) { return false;  },
        debug: false,
        errorClass : 'field-error',
        validClass : 'field-success',
        rules: {
            "username": {
                required: true,
                email : true
            }
        },
        submitHandler: function(form) {
            console.info('The lost password form has been submited');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_lost_object.ajaxurl,
                data: {
                    'action': 'ajaxlost', //calls wp_ajax_nopriv_ajaxlogin
                    'username': $('form#lost-password #username').val(),
                    'security': $('form#lost-password #security').val()
                },
                beforeSend : function(){
                    $('form#lost-password button.submit_button').addClass('load is-disabled');
                },
                success: function(data){

                    $('form#lost-password button.submit_button').removeClass('load is-disabled');

                    let alert_data = {type: data.alert_type, message: data.message};
                    manageAlerts(alert_data);

                    if (data.action !== 'fail') {

                        redirect_time_out_ID = setTimeout(function () {
                            document.location.href = data.redirect_url;
                            window.clearTimeout(redirect_time_out_ID);
                        }, 4000);

                    }

                    /*$('form#lost-password button.submit_button').removeClass('is-disabled');
                    $('form#lost-password p.status').html(data.message);

                    if (data.loggedin === true){
                        document.location.href = data.redirect_url;
                    }*/
                }
            });

        }
    });

});