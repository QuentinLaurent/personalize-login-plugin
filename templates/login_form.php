<?php
// Test if the query exists at the URL
$_login_result          = $_GET['login'];
$_login_result_password = $_GET['password'];

if ( $_login_result ) :

	$_message = __( 'The URL used to reset the password is no longer valid.', 'personalize-login' );

	if ( $_login_result === 'invalidkey' || $_login_result === 'expiredkey' ) : ?>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                let alert_data = {
                    type: 'danger',
                    message: '<?php echo esc_attr( $_message ); ?>'
                };
                manageAlerts(alert_data);
            });
        </script>

	<?php endif; ?>



<?php endif; ?>

<?php

if ( $_login_result_password ) :

	$_message = __( 'Password successfully changed, you can now use it to login', 'personalize-login' );

	if ( $_login_result_password === 'changed' ) : ?>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                let alert_data = {
                    type: 'danger',
                    message: '<?php echo esc_attr( $_message ); ?>'
                };
                manageAlerts(alert_data);
            });
        </script>

	<?php endif; ?>



<?php endif; ?>



<div class="form-standard login-form-container col-12">
    <div class="row justify-content-center">
        <div class="login-form-area col-10">

            <form id="login" action="login" method="post">

				<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>


                <!--FORM - FORM TITLE-->

                <div class="row align-items-baseline justify-content-between">
                    <div class="col-12 col-lg-auto">
                        <h1 class="form-title"><?php _e( 'Login', 'personalize-login' ); ?></h1>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <a id="go-to-register-form" class="link-text"
                           href="#register-form"><?php _e( 'Create my account', 'personalize-login' ) ?></a>
                    </div>
                </div>

                <!--FORM - LOGIN FORM SECTION-->

                <div class="row form-section alternate">
                    <div class="col-12">
                        <div class="form-section-content row">
                            <div class="col-12 col-md-6">
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
                            <div class="col-12 col-md-6">
                                <label for="password">
									<?php _e( 'Password', 'personalize-login' ); ?>
                                    <sup>*</sup>
                                </label>
                                <div class="form-field-container">
                                    <i class="form-field-icon password"></i>
                                    <input id="password" type="password" name="password" value="" class="form-element">
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

                                <button class="submit_button button-primary is-disabled" value="Login" name="submit">
                                    <span></span>
									<?php _e( 'Login', 'personalize-login' ); ?>
                                </button>

                            </div>

                            <div class="col-12">
                                <a class="button-text"
                                   href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'Lost your password ?', 'personalize-login' ); ?></a>
                            </div>

                        </div>

                    </div>

                </div>


            </form>

        </div>


    </div>

</div>