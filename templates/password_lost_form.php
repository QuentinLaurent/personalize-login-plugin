<div class="form-standard login-form-container col-12">
    <div class="row justify-content-center">
        <div class="login-form-area col-10">

            <form id="lost-password" action="lost-password" method="post">

				<?php wp_nonce_field( 'ajax-lost-password-nonce', 'security' ); ?>

                <!--FORM - FORM TITLE-->

                <div class="row">
                    <div class="col-12">
                        <h1 class="form-title"><?php _e( 'Lost your password ?', 'personalize-login' ); ?></h1>
                    </div>
                </div>

                <!--FORM - FORM DESCRIPTION-->

                <div class="row">
                    <div class="col-12">
                        <h1 class="form-description"><?php _e( 'Enter your email address and we\'ll send you a link you can use to pick a new password.', 'personalize-login' ); ?></h1>
                    </div>
                </div>

                <!--FORM - LOGIN FORM SECTION-->

                <div class="row form-section alternate">
                    <div class="col-12">
                        <div class="form-section-content row">
                            <div class="col-12">
                                <label for="username">
                                    <?php _e( 'E-mail address', 'personalize-login' ); ?>
                                    <sup>*</sup>
                                </label>
                                <div class="form-field-container">
                                    <i class="form-field-icon username"></i>
                                    <input id="username" type="text" name="username" value="" class="form-element">
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

                                <p class="status"></p>

                                <button class="submit_button button-primary is-disabled" name="submit">
                                    <span></span>
									<?php _e( 'New password', 'personalize-login' ); ?>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


            </form>

        </div>


    </div>

</div>