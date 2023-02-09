<div id="register-form" class="form-standard login-form-container col-12">
    <div class="row justify-content-center">
        <div class="login-form-area col-10">

            <form id="register" action="register" method="post">

				<?php wp_nonce_field( 'ajax-register-nonce', 'register-security' ); ?>

                <!--FORM - FORM TITLE-->

                <div class="row">
                    <div class="col">
                        <h1 class="form-title"><?php _e( 'Register', 'personalize-login' ); ?></h1>
                    </div>
                    <div class="col-auto">
                        <h1 class="form-step-counter">
                            <span class="counter-currrent"></span>
                            <span class="counter-separator">/</span>
                            <span class="counter-total"></span>
                        </h1>
                    </div>
                </div>


                <!--FORM - REGISTER FORM SECTION-->

                <div id="register-form-container" class="row">

                    <div id="register-loader" class="loader-container">
                        <div class="wave"></div>
                    </div>


                    <div id="form-section-slider" style="width: 100%">

                        <!--REGISTER - STEP 1-->

                        <div id="step-1" class="form-section-step">

                            <div class="row form-section alternate">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-6 form-field-container">
                                            <label for="organization_type">
												<?php _e( 'Organization type', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>
                                            <div class="form-field-container">
                                                <i class="form-field-icon organization-type"></i>
                                                <select name="organization_type" id="organization_type"
                                                        style="width: 100%" class="organization-type">
                                                    <option value="association">Association</option>
                                                    <option value="institution-scolaire">Institution scolaire</option>
                                                    <option value="collectivite">Collectivité</option>
                                                    <option value="entreprise">Entreprise</option>
                                                    <option value="autre">Autre</option>
                                                </select>
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>

                                        <div class="col-12 col-md-6 form-field-container">

                                            <label for="organization_name">
												<?php _e( 'Organization name', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon organization-name"></i>
                                                <input type="text" name="organization_name" id="organization_name"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>

                                        <div class="mt-5 w-100">

                                            <div class="form-section-content row">

                                                <div class="col-12 col-md-6 form-field-container">
                                                    <label for="upcycling_switch">
                                                        Êtes-vous dans le domaine du Upcycling ?
                                                        <sup>*</sup>
                                                    </label>

                                                </div>

                                                <div class="col-12 col-md-6 form-field-container">

                                                    <div class="radiobutton">
                                                        <input type="radio" name="upcycling_switch"
                                                               id="upcycling_yes"
                                                               class="form-element"
                                                               value=" <?php _e( 'Yes', 'personalize-login' ); ?>">
                                                        <label for="upcycling_yes"><?php _e( 'Yes', 'personalize-login' ); ?></label>
                                                    </div>

                                                    <div class="radiobutton">
                                                        <input type="radio" name="upcycling_switch"
                                                               id="upcycling_no"
                                                               class="form-element"
                                                               value=" <?php _e( 'No', 'personalize-login' ); ?>" checked>
                                                        <label for="upcycling_no"><?php _e( 'No', 'personalize-login' ); ?></label>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row form-section">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-6 form-field-container">
                                            <label for="general_interest_switch">
												<?php _e( 'General Interest', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-infos">
	                                            <?php _e( '(?) To be eligible for a funding application, you must be an association of general interest.', 'personalize-login' ); ?>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 form-field-container">

                                            <div class="radiobutton">
                                                <input type="radio" name="general_interest_switch"
                                                       id="general_interest_yes"
                                                       class="form-element"
                                                       value=" <?php _e( 'Yes', 'personalize-login' ); ?>" checked>
                                                <label for="general_interest_yes"><?php _e( 'Yes', 'personalize-login' ); ?></label>
                                            </div>

                                            <div class="radiobutton">
                                                <input type="radio" name="general_interest_switch"
                                                       id="general_interest_no"
                                                       class="form-element"
                                                       value=" <?php _e( 'No', 'personalize-login' ); ?>">
                                                <label for="general_interest_no"><?php _e( 'No', 'personalize-login' ); ?></label>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row form-section alternate">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 form-field-container">
                                            <label for="caption_page">
												<?php _e( 'Description', 'personalize-login' ); ?>
                                                <sup>*</sup>
												<?php _e( '(this text will be visible on your dedicated page)', 'personalize-login' ); ?>
                                            </label>

                                            <div class="form-field-container multiline">
                                                <i class="form-field-icon text"></i>
                                                <textarea name="caption_page" id="caption_page"
                                                          class="form-element"></textarea>
                                                <i class="form-field-status"></i>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row cta-section">

                                <div class="col-12 text-center">

                                    <div class="cta-section-content row">

                                        <div class="col-12">

                                            <div class="required-fields">
                                                * <?php _e( 'Required fields', 'personalize-login' ); ?>
                                            </div>

                                            <button class="next-step button-primary" value="Next" name="next">
                                                <span></span>
												<?php _e( 'Continue', 'personalize-login' ); ?>
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!--REGISTER - STEP 2-->

                        <div id="step-2" class="form-section-step">

                            <div class="row form-section alternate">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-6 form-field-container">
                                            <label for="contact_firstname">
												<?php _e( 'Dedicated contact first name', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon username"></i>
                                                <input type="text" name="contact_firstname" id="contact_firstname"
                                                       value="" class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 form-field-container">

                                            <label for="contact_lastname">
												<?php _e( 'Dedicated contact last name', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon username"></i>
                                                <input type="text" name="contact_lastname" id="contact_lastname"
                                                       value="" class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                            <input type="text" name="contact_nickname" id="contact_nickname"
                                                   style="display: none"
                                                   value="">

                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="row form-section">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-6 form-field-container">
                                            <label for="contact_email">
												<?php _e( 'E-mail', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon email"></i>
                                                <input type="text" name="contact_email" id="contact_email"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>

                                        <div class="col-12 col-md-6 form-field-container">

                                            <label for="contact_phone">
												<?php _e( 'Phone', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon phone"></i>
                                                <input type="text" name="contact_phone" id="contact_phone"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row form-section alternate">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 form-field-container">

                                            <label for="organization_address">
												<?php _e( 'Address', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon location"></i>
                                                <input type="text" name="organization_address" id="organization_address"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row form-section">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-4 form-field-container">
                                            <label for="organization_zipcode">
												<?php _e( 'Zipcode', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon location"></i>
                                                <input type="text" name="organization_zipcode" id="organization_zipcode"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-8 form-field-container">

                                            <label for="organization_city">
												<?php _e( 'City', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon location"></i>
                                                <input type="text" name="organization_city" id="organization_city"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row form-section">

                                        <div class="col-12">

                                            <div class="form-section-content row" style="max-width: 100%">
                                                <div class="col-12 form-field-container">

                                                    <div class="form-field-container">
                                                        <div id="map_register" class="map_register"></div>
                                                        <div class="form-field-infos">
                                                            <strong>
                                                                (*)<?php _e( 'GPS coordinates are required to obtain the precise location of your association on our map.', 'personalize-login' ); ?>
                                                            </strong>
                                                            <br>
                                                            (?)
							                                <?php _e( 'In order to get the precise GPS coordinates, you can add a point on the map by clicking on it.', 'personalize-login' ); ?>
                                                            <br>
							                                <?php _e( 'You can also move the marker with your mouse.', 'personalize-login' ); ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="row form-section">

                                        <div class="col-12">

                                            <div class="form-section-content row">
                                                <div class="col-12 col-md-6 form-field-container">

                                                    <label for="organization_address_lat">
						                                <?php _e( 'Latitude', 'fdm-theme' ); ?>
                                                        <sup>*</sup>
                                                    </label>

                                                    <div class="form-field-container">
                                                        <i class="form-field-icon gps"></i>
                                                        <input type="text" name="organization_address_lat" id="organization_address_lat"
                                                               value=""
                                                               class="form-element">
                                                        <i class="form-field-status"></i>
                                                    </div>

                                                </div>

                                                <div class="col-12 col-md-6 form-field-container">

                                                    <label for="organization_address_lng">
						                                <?php _e( 'Longitude', 'fdm-theme' ); ?>
                                                        <sup>*</sup>
                                                    </label>

                                                    <div class="form-field-container">
                                                        <i class="form-field-icon gps"></i>
                                                        <input type="text" name="organization_address_lng" id="organization_address_lng"
                                                               value=""
                                                               class="form-element">
                                                        <i class="form-field-status"></i>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="row cta-section alternate">

                                <div class="col-12 text-center">

                                    <div class="cta-section-content row">

                                        <div class="col-12">

                                            <div class="required-fields">
                                                * <?php _e( 'Required fields', 'personalize-login' ); ?>
                                            </div>

                                            <button class="next-step button-primary" value="Next" name="next">
                                                <span></span>
												<?php _e( 'Continue', 'personalize-login' ); ?>
                                            </button>
                                        </div>

                                        <div class="col-12">
                                            <button class="previous-step button-text back" value="Previous"
                                                    name="previous">
												<?php _e( 'Back to previous step', 'personalize-login' ); ?>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                        <!--REGISTER - STEP 3-->

                        <div id="step-3" class="form-section-step">

                            <div class="row form-section alternate">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-6 form-field-container">
                                            <label for="organization_website">
												<?php _e( 'Website', 'personalize-login' ); ?>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon website"></i>
                                                <input type="text" name="organization_website" id="organization_website"
                                                       placeholder="http://monsite.fr"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 form-field-container">

                                            <label for="organization_instagram">
												<?php _e( 'Instagram', 'personalize-login' ); ?>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon instagram"></i>
                                                <input type="text" name="organization_instagram"
                                                       id="organization_instagram"
                                                       placeholder="http://instagram.com/username"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row form-section">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <div class="col-12 col-md-6 form-field-container">
                                            <label for="organization_twitter">
												<?php _e( 'Twitter', 'personalize-login' ); ?>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon twitter"></i>
                                                <input type="text" name="organization_twitter" id="organization_twitter"
                                                       placeholder="http://twitter.com/username"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 form-field-container">

                                            <label for="organization_facebook">
												<?php _e( 'Facebook', 'personalize-login' ); ?>
                                            </label>

                                            <div class="form-field-container">
                                                <i class="form-field-icon facebook"></i>
                                                <input type="text" name="organization_facebook"
                                                       id="organization_facebook"
                                                       placeholder="http://facebook.com/username"
                                                       value=""
                                                       class="form-element">
                                                <i class="form-field-status"></i>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row form-section alternate">

                                <div class="col-12">

                                    <div class="form-section-content row">

                                        <script type="text/javascript">
                                            var plugin_image_url_success = '<?php echo plugins_url( 'personalize-login' ) . '/library/images/uploader-success.svg' ?>';
                                            var plugin_image_url_error = '<?php echo plugins_url( 'personalize-login' ) . '/library/images/uploader-error.svg' ?>';
                                        </script>

                                        <!--FILE - UPLOADER LOGO-->
                                        <div class="col-12 col-md-6">

                                            <label for="form_image_input_file_logo">
												<?php _e( 'Organization logo', 'personalize-login' ); ?>
                                                <sup>*</sup>
                                            </label>

                                            <div class="upload-image-container form-field-container">

                                                <form id="form-register-image-logo">
													<?php $backgroundUploader = plugins_url( 'personalize-login' ) . '/library/images/uploader-waiting.svg'; ?>
													<?php $backgroundUploaderFile = plugins_url( 'personalize-login' ) . '/library/images/uploader-file.svg'; ?>
                                                    <div class="img_contain">
                                                        <img id="organization_logo"
                                                             class="img-container"
                                                             src="<?php echo $backgroundUploader; ?>"
                                                             alt="<?php _e( 'Uploader pending icon', 'personalize-login' ); ?>"
                                                             title=''/>
                                                    </div>
                                                    <div class="input-group">

                                                        <div class="upload-status-error"></div>

                                                        <div class="custom-file form-field-container">
                                                            <input type="file" id="form_image_input_file_logo"
                                                                   name="form_image_input_file_logo"
                                                                   class="imgInp custom-file-input form-element"
                                                                   aria-describedby="form_image_input_file_logo_addon"
                                                                   max-file-size="500000"
                                                                   min-file-width="500"
                                                                   min-file-height="500">
                                                            <i class="form-field-status"></i>
                                                            <label class="button-file"
                                                                   for="form_image_input_file_logo">
																<?php _e( 'Choose a file', 'personalize-login' ); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="file_name">
                                                        <i class="file_name_icon"
                                                           style="background-image: url('<?php echo $backgroundUploaderFile; ?>')"></i>
                                                        <span class="filename_informations">
														    <?php _e( 'Format jpg/png', 'personalize-login' ); ?><br>
                                                        </span>
                                                    </div>
                                                </form>

                                            </div>

                                            <div class="upload-image-rules">
												<?php _e( 'Dimension : 500 x 500 px', 'personalize-login' ); ?><br>
												<?php _e( 'Max weight : 500Ko', 'personalize-login' ); ?>
                                            </div>

                                        </div>

                                        <!--FILE - UPLOADER COVER-->
                                        <div class="col-12 col-md-6">

                                            <label for="form_image_input_file_cover">
												<?php _e( 'Organization cover', 'personalize-login' ); ?>
                                            </label>

                                            <div class="upload-image-container form-field-container">

												<?php $backgroundUploader = plugins_url( 'personalize-login' ) . '/library/images/uploader-waiting.svg'; ?>
												<?php $backgroundUploaderFile = plugins_url( 'personalize-login' ) . '/library/images/uploader-file.svg'; ?>

                                                <form id="form-register-image-cover">
                                                    <div class="img_contain">
                                                        <img id="organization_cover"
                                                             class="img-container"
                                                             src="<?php echo $backgroundUploader; ?>"
                                                             alt="<?php _e( 'Uploader pending icon', 'personalize-login' ); ?>"
                                                             title=''/>
                                                    </div>

                                                    <div class="input-group">

                                                        <div class="upload-status-error"></div>

                                                        <div class="custom-file form-field-container">
                                                            <input type="file" id="form_image_input_file_cover"
                                                                   name="form_image_input_file_cover"
                                                                   class="imgInp custom-file-input form-element"
                                                                   aria-describedby="form_image_input_file_cover_addon"
                                                                   max-file-size="800000"
                                                                   min-file-width="890"
                                                                   min-file-height="470">
                                                            <i class="form-field-status"></i>
                                                            <label class="button-file"
                                                                   for="form_image_input_file_cover">
																<?php _e( 'Choose a file', 'personalize-login' ); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="file_name">
                                                        <i class="file_name_icon"
                                                           style="background-image: url('<?php echo $backgroundUploaderFile; ?>')"></i>
                                                        <span class="filename_informations">
														    <?php _e( 'Format jpg/png', 'personalize-login' ); ?><br>

                                                        </span>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="upload-image-rules">
												<?php _e( 'Dimension : 890 x 470 px', 'personalize-login' ); ?><br>
												<?php _e( 'Max weight : 800Ko', 'personalize-login' ); ?>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row cta-section">

                                <div class="col-12 text-center">

                                    <div class="cta-section-content row">


                                        <div class="col-12">

                                            <p class="status"></p>
                                            <div id="summary" class="error-list">
                                                <ul></ul>
                                            </div>
                                            <div class="col-12">

                                                <!--<div class="required-fields">
                                                    * <?php /*_e( 'Required fields', 'personalize-login' ); */ ?>
                                                </div>-->

                                                <button class="submit_button button-primary" value="Register"
                                                        name="submit">
                                                    <span></span>
													<?php _e( 'Valid register', 'personalize-login' ); ?>
                                                </button>
                                            </div>

                                            <div class="col-12">

                                                <button class="previous-step button-text back" value="Previous"
                                                        name="previous">
													<?php _e( 'Back to previous step', 'personalize-login' ); ?>
                                                </button>


                                            </div>


                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

            </form>

        </div>
    </div>
</div>
