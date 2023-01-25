var keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
    e = e || window.event;
    if (e.preventDefault)
        e.preventDefault();
    e.returnValue = false;
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

function disableScroll() {
    if (window.addEventListener) // older FF
        window.addEventListener('DOMMouseScroll', preventDefault, false);
    window.onwheel = preventDefault; // modern standard
    window.onmousewheel = document.onmousewheel = preventDefault; // older browsers, IE
    window.ontouchmove = preventDefault; // mobile
    document.onkeydown = preventDefaultForScrollKeys;
}

function enableScroll() {
    if (window.removeEventListener)
        window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.onmousewheel = document.onmousewheel = null;
    window.onwheel = null;
    window.ontouchmove = null;
    document.onkeydown = null;
}

// Map variables
let map_register,
    theMarker = {},
    iconBaseUri = libraryRootUrl + '/library/images/map/',
    original_lat = 46.85,
    original_lon = 2.3518,
    original_zoom = 6,
    oldLayer,
    icons = {
        'iconEvent': L.icon({iconUrl: iconBaseUri + "collecte.png", iconSize: [47, 56], iconAnchor: [23, 56]}),
    };


/**
 *
 * @param target
 */
scrollToAnchor = function (target, delta = 0) {

    var scroll_duration = 500;
    disableScroll();

    setTimeout(function () {
        $('html,body').animate({
                scrollTop: $('#' + target).offset().top - delta
            }, scroll_duration,
            function () {
                enableScroll();
            });
    }, 100);

};

jQuery(document).ready(function ($) {

    let redirect_time_out_ID;


    // https://codepen.io/gab5a430/pen/jPNXeX
    $(".imgInp").change(function (event) {
        //RecurFadeIn();
        readURL(this, $(this));
    });

    $(".imgInp").on('click', function (event) {
        //RecurFadeIn();
    });

    function FileConvertSize(aSize) {
        aSize = Math.abs(parseInt(aSize, 10));
        var def = [[1, 'octets'], [1024, 'ko'], [1024 * 1024, 'Mo'], [1024 * 1024 * 1024, 'Go'], [1024 * 1024 * 1024 * 1024, 'To']];
        for (var i = 0; i < def.length; i++) {
            if (aSize < def[i][0]) return (aSize / def[i - 1][0]).toFixed(2) + ' ' + def[i - 1][1];
        }
    }

    function readURL(input, inputElem) {

        if (input.files && input.files[0]) {

            var reader = new FileReader(),
                filename = inputElem.val(),
                maxFileSize = inputElem.attr('max-file-size'),
                minFileWidth = inputElem.attr('min-file-width'),
                minFileHeight = inputElem.attr('min-file-height'),
                filetype = input.files[0].type,
                filesize = input.files[0].size,
                filesizeConverted = FileConvertSize(input.files[0].size),
                filenameText = filename.substring(filename.lastIndexOf('\\') + 1),
                uploadedImageObj = URL.createObjectURL(input.files[0]),
                uploadedImage = new Image,
                fileWidth,
                fileHeight;


            reader.onload = function (e) {

                $_target = inputElem.parent().parent().parent().find('.img-container');
                $_target_label = inputElem.parent().parent().parent().find('.file_name');

                //$_target.attr('src', e.target.result);
                $_target.attr('data-src', e.target.result);
                $_target.attr('src', plugin_image_url_success);
                $_target.hide();
                $_target.fadeIn(500);

                $_target_label.find('.filename_informations').text(filenameText);
                $_target_label.find('.file_name_icon').addClass('is-visible');

                uploadedImage.onload = function () {
                    fileWidth = uploadedImage.width;
                    fileHeight = uploadedImage.height;

                    if ((fileWidth < minFileWidth) || (fileHeight < minFileHeight)) {

                        $_target = inputElem.parent().parent().parent().find('.img-container');
                        $_target_error = inputElem.parent().parent().find('.upload-status-error');
                        $_target_error.text('Dimensions min : ' + minFileWidth+' x ' + minFileHeight+'px');
                        $_target.attr('src', plugin_image_url_error);
                        inputElem.addClass('field-error');

                    }
                };

                uploadedImage.src = uploadedImageObj;
            };

            if (filetype.indexOf("jpeg") !== -1 || filetype.indexOf("png") !== -1 || filetype.indexOf("jpg") !== -1) {

                /**
                 * CHECK IF IMAGE IS GREATER THAN THE MAX FILE SIZE
                 */
                if (filesize < maxFileSize) {

                    $_target_error = inputElem.parent().parent().find('.upload-status-error');
                    $_target_error.text('');
                    reader.readAsDataURL(input.files[0]);
                    inputElem.removeClass('field-error');

                } else {

                    $_target = inputElem.parent().parent().parent().find('.img-container');
                    $_target_error = inputElem.parent().parent().find('.upload-status-error');

                    $_target_error.text('Image trop lourde');

                    $_target.attr('src', plugin_image_url_error);

                }

            } else {

                $_target_error = inputElem.parent().parent().find('.upload-status-error');
                $_target_error.text('Formats autorisés : jpg / png');
                inputElem.addClass('field-error');

            }

        }

    }

    /**
     * SELECT 2 - INIT
     */
    $('#organization_type').select2({
        allowClear: false, // Disabling clear button
        minimumResultsForSearch: -1,
        closeOnSelect: true,
        placeholder: 'Choisir'
    });

    /**
     * SLICK - INIT
     * @type {*|jQuery|HTMLElement}
     */
    var slickElement = $('#form-section-slider');
    var slidesToShow = 1;
    var slidesToScroll = 1;

    var slickOpts = {
        infinite: false,
        arrows: false,
        swipe: false,
        slidesToShow: slidesToShow,
        slidesToScroll: slidesToScroll,
        cssEase: 'ease-in-out',
        useTransform: true,
        mobileFirst: true,
        adaptiveHeight: true
    };


    $('#register-loader').fadeOut();
    $('#form-section-slider').fadeIn();

    slickElement.on('init', function (event, slick) {

        changeStepIndicator(slick.slideCount, 1);
        $('#register-loader').fadeOut();
        $('#form-section-slider').fadeIn();

        // Init the map inside the form
        setTimeout(function () {
            initRegisterMap();
        }, 250);


    });

    // INIT - INSTANCE OF THE SLICK OBJECT
    slickElement.slick(slickOpts);

    // NAVIGATION - CUSTOM ARROWS
    $('.previous-step').on('click', function (evt) {
        evt.preventDefault();
        slickElement.slick('slickPrev');
    });

    $('.next-step').on('click', function (evt) {
        evt.preventDefault();
        slickElement.slick('slickNext');
    });

    slickElement.on('afterChange', function (event, slick, slide, currentSlide, index) {
        //var currentSlideObj = slickElement.find('.slick-current');
        changeStepIndicator(slick.slideCount, slide + 1);
        scrollToAnchor('register-form-container', 300);
        //slickElement.find(".slick-list").height("auto");

    });

    /**
     * Perform AJAX registration
     */

    $("#contact_firstname,#contact_lastname").on('keyup change', function () {

        $_nicknamePart1 = $("#contact_firstname").val();
        $_nicknamePart2 = $("#contact_lastname").val();

        $_nickname = slugify($_nicknamePart1) + '.' + slugify($_nicknamePart2);

        $("#contact_nickname").val($_nickname);

    });

    jQuery.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            if (regexp.constructor != RegExp)
                regexp = new RegExp(regexp);
            else if (regexp.global)
                regexp.lastIndex = 0;
            return this.optional(element) || regexp.test(value);
        }, "Test Regex failed"
    );

    jQuery.validator.addMethod('social_url', function (value, element, param) {
        if ($.trim(value).length !== 0) {
            return RegExp('\\b' + param + '\\b', 'i').test(value);
        }
    }, "Must contain social network name");

    jQuery.validator.addMethod("digitsAndDotComma", function(value, element) {
        // allow any non-whitespace characters as the host part
        return this.optional( element ) || /^[0-9.-]+$/.test( value );
    }, 'Veuillez renseigner des coordonnées GPS valides.');

    /*jQuery.validator.methods.email = function( value, element ) {
        return this.optional( element ) || /[a-z0-9]+@[a-z]+\.[a-z]+/.test( value );
    };*/

    jQuery.validator.addMethod("emailCustom", function(value, element) {
        return this.optional( element ) || /[a-z0-9]+@[a-z0-9-]+\.[a-z]+/.test( value );
    }, 'Veuillez renseigner un format email valides.');

    jQuery.extend(jQuery.validator.messages, {
        required: "Ce champ est obligatoire",
        remote: "votre message",
        email: "Veuillez renseigner un email valide",
        url: "Format URL non reconnu",
        date: "votre message",
        dateISO: "votre message",
        number: "Veuillez n'utiliser que des chiffres",
        digits: "votre message",
        creditcard: "votre message",
        equalTo: "votre message",
        accept: "Veuillez vérifier le format de votre  fichier",
        maxlength: jQuery.validator.format("votre message {0} caractéres."),
        minlength: jQuery.validator.format("votre message {0} caractéres."),
        rangelength: jQuery.validator.format("votre message  entre {0} et {1} caractéres."),
        range: jQuery.validator.format("votre message  entre {0} et {1}."),
        max: jQuery.validator.format("votre message  inférieur ou égal à {0}."),
        min: jQuery.validator.format("votre message  supérieur ou égal à {0}."),
        regex: jQuery.validator.format("Ce champ doit être composé de 5 chiffres"),
        lettersonly: jQuery.validator.format("Ce champ doit être composé lettres uniquement"),
    });

    $('form#register').validate({
        debug: false,
        errorPlacement: function (error, element) {
            return false;
        },
        invalidHandler: function (event, validator) {
            //list of invalid fields
            //console.debug('invalidHandler', validator.invalid);
            var errorList = validator.invalid;

            $("form#register #summary ul").empty();

            $.each(errorList, function (fieldKey, errorMessage) {

                var getStepErrorId = $("#" + fieldKey).closest('.form-section-step').attr('id');
                var getStepError = getStepErrorId.split('-');
                var getStepErrorNumber = getStepError.pop();
                var fiedKeyName = $.trim($("label[for='" + fieldKey + "']").text());
                if(fieldKey !== 'organization_website' && fieldKey !== 'organization_instagram' && fieldKey !== 'organization_twitter' &&  fieldKey !== 'organization_facebook' ){
                    fiedKeyName = fiedKeyName.substring(0, fiedKeyName.length - 1);
                }else{
                    fiedKeyName = fiedKeyName;
                }

                if (fieldKey === "form_image_input_file_logo") {
                    fiedKeyName = "Logo de l'organisation";
                }

                $("form#register #summary ul").append('<li>Étape'
                    + ' ' + getStepErrorNumber + ': <b>' + fiedKeyName + '</b> ' + errorMessage
                    + '</li>');

            });

            // 'this' refers to the form
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                    ? 'il y a 1 champ en erreur. Ce champ est  en surbrillance'
                    : 'il y a ' + errors + ' champs en erreur. Il sont en surbrillance';
                $("form#register .status").html(message);
                $("form#register .status").show();
            } else {
                $("form#register .status").hide();
            }

            slickElement.find(".slick-list").height("auto");

        },
        errorClass: 'field-error',
        validClass: 'field-success',
        rules: {
            "organization_name": {
                required: true
            },
            "general_interest_switch": {
                required: true
            },
            "caption_page": {
                required: true
            },
            "contact_firstname": {
                required: true,
            },
            "contact_lastname": {
                required: true,
            },
            "contact_email": {
                required: true,
                emailCustom: true
            },
            "contact_phone": {
                required: true,
                number: true
            },
            "organization_address": {
                required: true
            },
            "organization_zipcode": {
                required: true,
                number: true,
                minlength: 2
            },
            "organization_city": {
                required: true,
            },
            "organization_address_lat": {
                required: true,
                digitsAndDotComma : true
            },
            "organization_address_lng": {
                required: true,
                digitsAndDotComma : true
            },
            "form_image_input_file_logo": {
                required: true,
                accept: "image/jpeg, image/png"
            },
            "organization_website" : {
                required: false,
                url: true
            },
            "organization_instagram" : {
                required: false,
                url: true
            },
            "organization_twitter" : {
                required: false,
                url: true
            },
            "organization_facebook" : {
                required: false,
                url: true
            },
        },
        submitHandler: function (form) {
            console.info('The Register form has been submited');

            // STEP 1
            $_organization_type = $('form#register #organization_type').select2('data')[0]['id'];
            $_organization_name = $('form#register #organization_name').val();
            $_organization_interest = $('form#register input[name=general_interest_switch]:checked').val();
            $_organization_caption = $('form#register #caption_page').val();

            // STEP 2
            $_contact_firstname = $('form#register #contact_firstname').val();
            $_contact_lastname = $('form#register #contact_lastname').val();
            $_contact_email = $('form#register #contact_email').val();
            $_contact_nickname = $('form#register #contact_nickname').val();
            $_contact_phone = $('form#register #contact_phone').val();
            $_organization_address = $('form#register #organization_address').val();
            $_organization_zipcode = $('form#register #organization_zipcode').val();
            $_organization_city = $('form#register #organization_city').val();
            $_organization_lat = $('form#register #organization_address_lat').val();
            $_organization_lng = $('form#register #organization_address_lng').val();

            // STEP 3
            $_organization_website = $('form#register #organization_website').val();
            $_organization_instagram = $('form#register #organization_instagram').val();
            $_organization_twitter = $('form#register #organization_twitter').val();
            $_organization_facebook = $('form#register #organization_facebook').val();

            $_organization_logo = $('form#register #organization_logo').attr('data-src');
            $_organization_cover = $('form#register #organization_cover').attr('data-src');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_login_object.ajaxurl,
                data: {
                    'action': 'ajaxregister', //calls wp_ajax_nopriv_ajaxlogin
                    'email': $_contact_email,
                    'firstname': $_contact_firstname,
                    'lastname': $_contact_lastname,
                    'nickname': $_contact_nickname,
                    'organization_type': $_organization_type,
                    'organization_name': $_organization_name,
                    'organization_interest': $_organization_interest,
                    'organization_caption': $_organization_caption,
                    'contact_phone': $_contact_phone,
                    'organization_address': $_organization_address,
                    'organization_zipcode': $_organization_zipcode,
                    'organization_city': $_organization_city,
                    'organization_lat': $_organization_lat,
                    'organization_lng': $_organization_lng,
                    'organization_website': $_organization_website,
                    'organization_instagram': $_organization_instagram,
                    'organization_twitter': $_organization_twitter,
                    'organization_facebook': $_organization_facebook,
                    'organization_logo': $_organization_logo,
                    'organization_cover': $_organization_cover,
                    'security': $('form#register #register-security').val()
                },
                beforeSend: function () {
                    $('form#register button.submit_button').addClass('load is-disabled');
                },
                success: function (data) {

                    //console.debug(data);

                    $('form#register button.submit_button').removeClass('load is-disabled');
                    //$('form#register p.status').html(data.message);

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


/**
 * SET THE STEP'S COUNTER AFTER SLIDE EVENT
 * @param totalSlides
 * @param currentSlide
 */
var changeStepIndicator = function (totalSlides, currentSlide) {
    $_el = $("#register");
    $_el.find('.form-step-counter').children('.counter-currrent').text(currentSlide);
    $_el.find('.form-step-counter').children('.counter-total').text(totalSlides);
};

/**
 * HELPERS - Slugify method
 * @param str
 * @returns {string}
 */
function slugify(str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to = "aaaaeeeeiiiioooouuuunc------";
    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
}

/**********************************************************************************************************************/
/******************************************** MAP - CREATION & UPDATES ************************************************/
/**********************************************************************************************************************/

/**
 * MAP - INIT
 * Instantiate the map container and functionality
 */

let initRegisterMap = function () {

    map_register = new L.map('map_register', {
        center: new L.LatLng(original_lat, original_lon),
        zoom: original_zoom,
        minZoom: 1,
        maxZoom: 20,
        zoomControl: true,
        gestureHandling: true,
        layers: new L.TileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png')
    });

    map_register.zoomControl.setPosition('bottomright');
    map_register.scrollWheelZoom.disable();

    map_register.on('click', function (e) {

        lat = e.latlng.lat;
        lon = e.latlng.lng;

        $('#organization_address_lat').val(lat);
        $('#organization_address_lng').val(lon);

        //Clear existing marker

        if (theMarker != undefined) {
            map_register.removeLayer(theMarker);
        }

        //Add a marker to show where you clicked.
        theMarker = L.marker(e.latlng, {
            icon: icons['iconEvent'],
            draggable: true,
        }).addTo(map_register);

        theMarker.on('dragend', function (e) {
            $('#organization_address_lat').val(e.target._latlng.lat);
            $('#organization_address_lng').val(e.target._latlng.lng);
        });


    });


};


/**
 * MAP - UPDATE
 * Update the map when a marker already exist. Remove the older and add a new one
 * @param is_editable
 * @param data
 */
let updateAddEventMap = function (is_editable, data) {

    if (is_editable && data) {

        theMarker = L.marker([data['lat'], data['lng']], {
            icon: icons['iconEvent'],
            draggable: true,
        }).addTo(map_register);

        var currentZoom = map_register.getZoom();

        map_register.flyTo([data['lat'], data['lng']], 13);

        theMarker.on('dragend', function (e) {
            $('#organization_address_lat').val(e.target._latlng.lat);
            $('#organization_address_lng').val(e.target._latlng.lng);
        });

    }

};
