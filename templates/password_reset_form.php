<div class="form-standard login-form-container col-12">
    <div class="row justify-content-center">
        <div class="login-form-area col-10">

            <form id="reset-password" action="reset-password" method="post" autocomplete="off">

				<?php wp_nonce_field( 'ajax-reset-password-nonce', 'security' ); ?>

                <!--FORM - FORM TITLE-->

                <div class="row">
                    <div class="col-12">
                        <h1 class="form-title"><?php _e( 'Reset your password ?', 'personalize-login' ); ?></h1>
                    </div>
                </div>

                <!--FORM - FORM DESCRIPTION-->

                <div class="row">
                    <div class="col-12">
                        <h2 class="form-description">
                            <?php _e( 'In order to access your account you need a secure password. Fill in the 2 fields according to the rules below.', 'personalize-login' ); ?>
                            <ul class="error-list-checker">
                                <li id="check-length" class=""> <?php _e( '8 characters long', 'personalize-login' ); ?></li>
                                <li id="check-uppercase" class=""> <?php _e( 'Contains at least one uppercase', 'personalize-login' ); ?></li>
                                <li id="check-lowercase" class=""> <?php _e( 'Contains at least one lowercase', 'personalize-login' ); ?></li>
                                <li id="check-digit" class=""> <?php _e( 'Contains at least one digit', 'personalize-login' ); ?></li>
                                <li id="check-symbol" class=""> <?php _e( 'Contains at least one of theses symbols', 'personalize-login' ); ?> @#!"?$%^&)</li>
                            </ul>
                        </h2>
                    </div>
                </div>

                <!--FORM - LOGIN FORM SECTION-->

                <div class="row form-section alternate">
                    <div class="col-12">
                        <div class="form-section-content row">
                            <div class="col-12 col-md-6">
                                <label for="password1">
									<?php _e( 'Password', 'personalize-login' ); ?>
                                    <sup>*</sup>
                                </label>
                                <div class="form-field-container">
                                    <i class="form-field-icon password"></i>
                                    <input id="password1" type="text" name="password1" value="" class="form-element" autocomplete="off">
                                    <i class="form-field-status"></i>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password2">
			                        <?php _e( 'Repeat password', 'personalize-login' ); ?>
                                    <sup>*</sup>
                                </label>
                                <div class="form-field-container">
                                    <i class="form-field-icon password"></i>
                                    <input id="password2" type="text" name="password2" value="" class="form-element" autocomplete="off">
                                    <i class="form-field-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!--FORM - FORM CTA-->

                <div class="row cta-section">

                    <div class="col-12 text-center">

                        <div class="cta-section-content row">

                            <div class="col-12">

                                <?php //echo wp_get_password_hint(); ?>
                                <p class="status"></p>

                                <button class="submit_button button-primary is-disabled" name="submit">
                                    <span></span>
									<?php _e( 'Validate Password', 'personalize-login' ); ?>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


            </form>

        </div>


    </div>

</div>