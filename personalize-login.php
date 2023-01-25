<?php
/**
 * Plugin Name:       Personalize Login
 * Description:       A plugin that replaces the WordPress login flow with a custom page.
 * Version:           1.0.0
 * Author:            Florent Desmis
 * License:           GPL-2.0+
 * Text Domain:       personalize-login
 * Domain Path:       /languages
 */


//https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-3-password-reset--cms-23811
class Personalize_Login_Plugin {

	/**
	 * Initializes the plugin.
	 *
	 * To keep the initialization fast, only add filter and action
	 * hooks in the constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'notification_bubble_user_in_admin_menu' ) );
		add_action( 'init', array( $this, 'create_custom_user_role' ) );

		add_action( 'after_setup_theme', array( $this, 'register_image_size' ) );
		add_action( 'init', array( $this, 'add_plugin_dependies' ) );

		add_action( 'plugins_loaded', array( $this, 'personalize_login_load_textdomain' ) );

		add_action( 'init', 'blockusers_init' ); 
		function blockusers_init() { 
			if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_redirect( home_url() ); exit;
			}
		}


		/**
		 * SHORTCODE DECLARATIONS
		 */
		add_shortcode( 'custom-login-form', array( $this, 'render_login_form' ) );
		add_shortcode( 'custom-register-form', array( $this, 'render_register_form' ) );
		add_shortcode( 'custom-password-lost-form', array( $this, 'render_password_lost_form' ) );
		add_shortcode( 'custom-password-reset-form', array( $this, 'render_password_reset_form' ) );

		/**
		 * REDIRECT TO CUSTOM PAGES
		 */

		add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
		add_action( 'login_form_lostpassword', array( $this, 'redirect_to_custom_lostpassword' ) );

		add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
		add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );


		add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );


		/**
		 * WP ADMIN STATES
		 */
		add_filter( 'display_post_states', array( $this, 'plugin_activated_post_state' ), 10, 2 );

		//add_filter('authenticate', array($this, 'maybe_redirect_at_authenticate'), 101, 3);
		//add_filter('login_redirect', array($this, 'redirect_after_login'), 10, 3);

		/**
		 * AJAX LOGIN VALIDATION
		 */
		add_action( 'init', array( $this, 'ajax_login_init' ) );
		add_action( 'wp_ajax_nopriv_ajaxlogin', array( $this, 'ajax_login' ) );

		/**
		 * AJAX REGISTER VALIDATION
		 */

		add_action( 'init', array( $this, 'ajax_register_init' ) );
		add_action( 'wp_ajax_nopriv_ajaxregister', array( $this, 'ajax_register' ) );

		/**
		 * AJAX LOST PASSWORD VALIDATION
		 */
		add_action( 'init', array( $this, 'ajax_lost_init' ) );
		add_action( 'wp_ajax_nopriv_ajaxlost', array( $this, 'ajax_lost' ) );

		/**
		 * AJAX LOST PASSWORD VALIDATION
		 */
		add_action( 'init', array( $this, 'ajax_reset_init' ) );
		add_action( 'wp_ajax_nopriv_ajaxreset', array( $this, 'ajax_reset' ) );

	}

	/**
	 * LOGIN
	 */
	public function ajax_login_init() {

		wp_register_script( 'pl-user-login', plugin_dir_url( __FILE__ ) . '/library/js/user-login.js', array( 'jquery' ) );
		wp_register_style( 'pl-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/personalize-login.css', '', '1.0.0', 'all' );

		wp_enqueue_style( 'pl-stylesheet' );

		wp_localize_script( 'pl-user-login', 'ajax_login_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
	}

	function ajax_login() {

		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ajax-login-nonce', 'security' );

		// Nonce is checked, get the POST data and sign user on
		$info                  = array();
		$info['user_login']    = $_POST['username'];
		$info['user_password'] = $_POST['password'];
		$info['remember']      = true;

		$user    = get_user_by_email( trim( $info['user_login'] ) );
		$user_ID = $user->ID;

		// Check the role
		if ( in_array( "association_role", $user->roles ) ) {

			//Check  the account status
			$user_status = get_field( 'user_status', 'user_' . $user_ID );

			if ( $user_status == "pending" ) {

				echo json_encode( array(
						'action'     => 'fail',
						'alert_type' => 'danger',
						'message'    => __( 'Your account has not been approved yet. Please, try again later.', 'personalize-login' )
					)
				);

				die();

			} elseif ( $user_status == "blacklisted" ) {

				echo json_encode( array(
						'action'     => 'fail',
						'alert_type' => 'danger',
						'message'    => __( 'You do not have the necessary access to connect. Please contact us !', 'personalize-login' )
					)
				);

				die();

			} elseif ( $user_status == "registred" ) {

				echo json_encode( array(
						'action'     => 'fail',
						'alert_type' => 'danger',
						'message'    => __( 'Your account is under registration. Please, try again later.', 'personalize-login' )
					)
				);

				die();

			}

		}

		$user_signon = wp_signon( $info, true );

		if ( is_wp_error( $user_signon ) ) {

			$error_code = $user_signon->get_error_code();
			$message    = $this->get_error_message( $error_code );

			echo json_encode( array(
					'action'     => 'fail',
					'alert_type' => 'danger',
					'message'    => $message
				)
			);

		} else {

			$redirect_to = '';

			if ( user_can( $user_signon, 'manage_options' ) ) {
				$redirect_to = admin_url();
			} else {
				$redirect_to = home_url( 'edit-profile' );
			}

			echo json_encode( array(
					'action'       => 'success',
					'alert_type'   => 'success',
					'redirect_url' => $redirect_to,
					'message'      => __( 'Login successful, redirecting...', 'personalize-login' )
				)
			);

			/*echo json_encode( array(
				'loggedin'     => true,
				'redirect_url' => $redirect_to,
				'message'      => __( 'Login successful, redirecting...', 'personalize-login' )
			) );*/
		}

		die();
	}

	/**
	 * REGISTER
	 */
	public function ajax_register_init() {

		wp_register_script( 'pl-user-register', plugin_dir_url( __FILE__ ) . '/library/js/user-register.js', array( 'jquery' ), '1.0.0', true );

		wp_enqueue_script('leaflet-api', plugin_dir_url( __FILE__ ) . '/library/js/libs/leaflet.js', array(), '1.5.1', false);
		wp_enqueue_style('leaflet-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/leaflet.css', false);

		wp_enqueue_script('gesture-api', plugin_dir_url( __FILE__ ) . '/library/js/libs/leaflet-gesture-handling.min.js', array(), '1.1.8', false);
		wp_enqueue_style('gesture-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/leaflet-gesture-handling.min.css', false);

		wp_enqueue_script('cluster-api', plugin_dir_url( __FILE__ ) . '/library/js/libs/leaflet.markercluster.js', array(), '1.4.1', false);
		wp_enqueue_style('cluster-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/MarkerCluster.css', false);
		wp_enqueue_style('cluster-default-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/MarkerCluster.Default.css', false);


		wp_localize_script( 'pl-user-register', 'ajax_register_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );


	}

	function ajax_register() {

		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ajax-register-nonce', 'security' );

		// Nonce is checked, get the POST data and register user on
		if ( ! get_option( 'users_can_register' ) ) {
			// Registration closed, Redirect to home page
			$redirect_url = add_query_arg( 'registration-errors', 'closed', home_url() );
			wp_redirect( $redirect_url );
			exit;

		} else {
			$email      = $_POST['email'];
			$first_name = sanitize_text_field( $_POST['firstname'] );
			$last_name  = sanitize_text_field( $_POST['lastname'] );
			$nickname   = sanitize_text_field( $_POST['nickname'] );

			$_organization_type      = $_POST['organization_type'];
			$_organization_name      = $_POST['organization_name'];
			$_organization_interest  = $_POST['organization_interest'];
			$_upcycling  = $_POST['upcycling'];
			$_organization_caption   = $_POST['organization_caption'];
			$_contact_phone          = $_POST['contact_phone'];
			$_organization_address   = $_POST['organization_address'];
			$_organization_zipcode   = $_POST['organization_zipcode'];
			$_organization_city      = $_POST['organization_city'];
			$_organization_lat      = $_POST['organization_lat'];
			$_organization_lng      = $_POST['organization_lng'];
			$_organization_website   = $_POST['organization_website'];
			$_organization_instagram = $_POST['organization_instagram'];
			$_organization_twitter   = $_POST['organization_twitter'];
			$_organization_facebook  = $_POST['organization_facebook'];

			$_organization_logo  = $_POST['organization_logo'];
			$_organization_cover = $_POST['organization_cover'];

			$result = $this->register_user( $email, $first_name, $last_name, $nickname );

			if ( is_wp_error( $result ) ) {

				$error_code   = $result->get_error_code();
				$message      = $this->get_error_message( $error_code );
				$redirect_url = add_query_arg( 'registration-errors', $error_code, home_url() );

				/*echo json_encode( array(
					'registered'   => false,
					'redirect_url' => $redirect_url,
					'message'      => $message
				) );*/

				echo json_encode( array(
						'action'       => 'fail',
						'alert_type'   => 'danger',
						'redirect_url' => $redirect_url,
						'message'      => $message
					)
				);
				die();

			} else {

				$postData = array(
					'title'   => $_organization_name,
					'content' => $_organization_caption
				);

				$address_data = array(
					'address' => $_organization_address,
					'zipcode' => $_organization_zipcode,
					'city'    => $_organization_city,
					'lat'     => $_organization_lat,
					'lng'     => $_organization_lng,
				);

				$contact_data = array(
					'user_firstname' => $first_name,
					'user_lastname'  => $last_name,
					'user_email'     => $email,
					'user_phone'     => $_contact_phone,
					'user_id'        => $result
				);

				$association_type_data = array(
					'type'     => $_organization_type,
					'interest' => $_organization_interest,
					'upcycling' => $_upcycling
				);

				$social_data = array(
					'website'   => $_organization_website,
					'instagram' => $_organization_instagram,
					'twitter'   => $_organization_twitter,
					'facebook'  => $_organization_facebook
				);

				if ( $_organization_cover == null ) {
					$image_data = array(
						'logo' => $_organization_logo
					);
				} else {
					$image_data = array(
						'logo'  => $_organization_logo,
						'cover' => $_organization_cover
					);
				}


				$this->create_association_page( $postData, $address_data, $contact_data, $association_type_data, $social_data, $image_data, $result );


				$redirect_url = add_query_arg( 'registration-success', 'pending', home_url() );

				echo json_encode( array(
						'action'       => 'success',
						'alert_type'   => 'success',
						'redirect_url' => $redirect_url,
						'message'      => __( 'Registration successful, redirecting...', 'personalize-login' )
					)
				);


				die();

			}
		}

	}

	/**
	 * LOST PASSWORD
	 */
	function ajax_lost_init() {
		wp_register_script( 'pl-user-lost', plugin_dir_url( __FILE__ ) . '/library/js/user-lost.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'pl-user-lost' );

		wp_localize_script( 'pl-user-lost', 'ajax_lost_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
	}

	function ajax_lost() {

		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ajax-lost-password-nonce', 'security' );

		// Nonce is checked, get the POST data and sign user on
		$user_email = $_POST['username'];

		// Check if email is in wp users
		$user = email_exists( $user_email );

		if ( $user ) {

			$user_info = get_userdata( $user );
			$user_id   = ( $user_info->ID );
			$user      = new WP_User( (int) $user_id );

			$key = get_password_reset_key( $user );

			update_user_meta( $user, 'reset_password', $key );

			// Send email to user
			$to = $user_email;

			$subject =  __( 'Your account - Reset password', 'personalize-login' ) . ' -  #Ungestepourlamer';

			$msg = __( 'Hello', 'personalize-login' ) . ",\r\n\r\n";
			$msg .= __( 'You have requested a new password for your account.', 'personalize-login' ) . "\r\n";
			$msg .= __( 'You can generate your password by clicking the button below.', 'personalize-login' ) . "\r\n\r\n";
			$msg .= sprintf( '<a href="%s" style="border-radius: 0;cursor: pointer;display: inline-block;background-color: #FFBA00; border: 1px solid #FFBA00; outline: none; color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-weight: 400; font-size: 16px; text-decoration: none; text-transform: initial; text-align: center; line-height: normal; padding: 8px 15px; margin: 15px 0;"  target="_blank">%s</a>', site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_info->user_login ), 'login' ), __( 'Get my password', 'personalize-login' ) ) . "\r\n";
			$msg .= __( 'If the button does not work, you can copy / paste the link below into your browser.', 'personalize-login' ) . "\r\n\r\n";
			$msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_info->user_login ), 'login' ) . "\r\n\r\n";
			$msg .= __( 'Thank you for your trust,', 'personalize-login' ) . "\r\n";
			$msg .= __( 'The team of Ungestepourlamer', 'personalize-login' ) . "\r\n";

			wp_mail( $to, $subject, $msg );


			echo json_encode( array(
					'action'       => 'success',
					'alert_type'   => 'success',
					'redirect_url' => home_url( 'login' ),
					'message'      => __( 'An email with the password creation link has been sent.', 'personalize-login' )
				)
			);

			die();


		} else {
			$message = $this->get_error_message( 'invalid_email' );

			echo json_encode( array(
					'action'     => 'fail',
					'alert_type' => 'danger',
					'message'    => $message
				)
			);

		}


		wp_die();
	}

	/**
	 * RESET PASSWORD
	 */
	function ajax_reset_init() {
		wp_register_script( 'pl-user-reset', plugin_dir_url( __FILE__ ) . '/library/js/user-reset.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'pl-user-reset' );

		wp_localize_script( 'pl-user-reset', 'ajax_reset_object', array(
			'ajaxurl'      => admin_url( 'admin-ajax.php' ),
			'redirect_url' => home_url( 'login?password=changed' )
		) );
	}

	function ajax_reset() {

		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ajax-reset-password-nonce', 'security' );

		$rp_key      = $_POST['rp_key'];
		$rp_login    = $_POST['rp_login'];
		$rp_password = $_POST['password'];

		$user = check_password_reset_key( $rp_key, $rp_login );

		if ( ! $user || is_wp_error( $user ) ) {
			if ( $user && $user->get_error_code() === 'expired_key' ) {

				$message = $this->get_error_message( 'expired_key' );

				echo json_encode( array(
						'action'       => 'fail',
						'alert_type'   => 'danger',
						'rp_status' => 'expired_key',
						'redirect_url' => home_url( 'password-lost' ),
						'message'   => $message
					)
				);

			} else {

				$message = $this->get_error_message( 'invalidkey' );

				echo json_encode( array(
						'action'       => 'fail',
						'alert_type'   => 'danger',
						'rp_status' => 'invalidkey',
						'redirect_url' => home_url( 'password-lost' ),
						'message'   => $message
					)
				);

			}
			exit;
		} else {

			reset_password( $user, $rp_password );

			echo json_encode( array(
					'action'       => 'success',
					'alert_type'   => 'success',
					'redirect_url' => home_url( 'login' ),
					'message'      => __( 'Password successfully changed, you can now use it to login', 'personalize-login' )
				)
			);

			die();

		}


		die();

	}

	public function personalize_login_load_textdomain() {
		load_plugin_textdomain( 'personalize-login', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Plugin dependencies
	 */
	public function add_plugin_dependies() {

		wp_register_style( 'pl-select2-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/select2.min.css', '', '4.0.6-rc.0', 'all' );
		wp_enqueue_style( 'pl-select2-stylesheet' );

		wp_register_script( 'pl-select2', plugin_dir_url( __FILE__ ) . '/library/js/libs/select2.min.js', array( 'jquery' ), '4.0.6-rc.0', true );
		wp_enqueue_script( 'pl-select2' );

		wp_register_style( 'pl-slick-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/slick.css', '', '1.8.1', 'all' );
		wp_enqueue_style( 'pl-slick-stylesheet' );

		wp_register_script( 'pl-slick', plugin_dir_url( __FILE__ ) . '/library/js/libs/slick.min.js', array( 'jquery' ), '1.8.1', true );
		wp_enqueue_script( 'pl-slick' );

		/*wp_register_style( 'pl-flickity-stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/libs/flickity.css', '', '1.8.1', 'all' );
		wp_enqueue_style( 'pl-flickity-stylesheet' );

		wp_register_script( 'pl-flickity', plugin_dir_url( __FILE__ ) . '/library/js/libs/flickity.min.js', array( 'jquery' ), '1.8.1', true );
		wp_enqueue_script( 'pl-flickity' );*/


		wp_register_script( 'validate', plugin_dir_url( __FILE__ ) . '/library/js/libs/jquery.validate.min.js', array( 'jquery' ), '1.19.1' );
		wp_enqueue_script( 'validate' );

		wp_register_script( 'validate-additionnal', plugin_dir_url( __FILE__ ) . '/library/js/libs/additional-methods.min.js', array( 'jquery' ), '1.19.1' );
		wp_enqueue_script( 'validate-additionnal' );

	}

	/**
	 * Plugin activation hook.
	 *
	 * Creates all WordPress pages needed by the plugin.
	 */
	public static function plugin_activated() {
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			'edit-profile'   => array(
				'title'   => __( 'Edit profile', 'personalize-login' ),
				'content' => ''
			),
			'login'          => array(
				'title'   => __( 'Login', 'personalize-login' ),
				'content' => '[custom-login-form]'
			),
			'password-lost'  => array(
				'title'   => __( 'Password lost ?', 'personalize-login' ),
				'content' => '[custom-password-lost-form]'
			),
			'password-reset' => array(
				'title'   => __( 'Reset password', 'personalize-login' ),
				'content' => '[custom-password-reset-form]'
			)
		);

		foreach ( $page_definitions as $slug => $page ) {
			// Check that the page doesn't exist already
			$query = new WP_Query( 'pagename=' . $slug );
			if ( ! $query->have_posts() ) {
				// Add the page using the data from the array above
				wp_insert_post(
					array(
						'post_content'   => $page['content'],
						'post_name'      => $slug,
						'post_title'     => $page['title'],
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'ping_status'    => 'closed',
						'comment_status' => 'closed',
					)
				);
			}
		}


	}

	/**
	 * Add post state to the projects page
	 *
	 * @param $post_states
	 * @param $post
	 *
	 * @return array
	 */
	function plugin_activated_post_state( $post_states, $post ) {


		if ( $post->post_name == 'edit-profile' ) {
			$post_states[] = 'Profile - Edit page';
		}

		if ( $post->post_name == 'login' ) {
			$post_states[] = 'Profile - Login page';
		}

		if ( $post->post_name == 'password-lost' ) {
			$post_states[] = 'Profile - Forgot password';
		}

		if ( $post->post_name == 'password-reset' ) {
			$post_states[] = 'Profile - Reset password';
		}

		return $post_states;
	}

	/**
	 * REGISTER IMAGE SIZES FOR THE PLUGIN
	 */
	public function register_image_size() {
		add_image_size( 'organization_logo', 200, 200, false );
		add_image_size( 'organization_logo_small', 80, 80, false );

		add_image_size( 'organization_cover', 890, 470, true );
		add_image_size( 'organization_cover_small', 445, 235, true );
	}

	/**
	 * ASSIGN BASE 64 IMAGE TO A POST
	 *
	 * @param $base64_img
	 * @param $post_id
	 * @param $post_title
	 * @param $destination
	 */
	public function save_image( $base64_img, $post_id, $post_title, $destination ) {

		// Upload dir.
		$upload_dir     = wp_upload_dir();
		$upload_path    = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
		$file_extension = "";
		$file_type      = "";

		/**
		 * CHECK FORMAT EXTENSION
		 *
		 *
		 */

		if ( strpos( $base64_img, 'image/png' ) ) {

			$file_extension = ".png";
			$file_type      = "image/png";

		} elseif ( strpos( $base64_img, 'image/jpeg' ) || strpos( $base64_img, 'image/jpeg' ) ) {
			$file_extension = ".jpg";
			$file_type      = "image/jpeg";
		}

		//$img             = str_replace( 'data:image/jpeg;base64,', '', $base64_img );
		$img      = str_replace( 'data:' . $file_type . ';base64,', '', $base64_img );
		$img      = str_replace( ' ', '+', $img );
		$decoded  = base64_decode( $img );
		$filename = $post_title . $file_extension;
		//$hashed_filename = md5( $filename . microtime() ) . '_' . $filename;
		//$hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

		if ( $destination == "logo" ) {
			$hashed_filename = 'organization_logo_' . time() . '_' . $filename;
		} elseif ( $destination == "cover" ) {
			$hashed_filename = 'organization_cover_' . time() . '_' . $filename;
		}


		// Save the image in the uploads directory.
		$upload_file = file_put_contents( $upload_path . $hashed_filename, $decoded );

		$attachment = array(
			'post_mime_type' => $file_type,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $hashed_filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => $upload_dir['url'] . '/' . basename( $hashed_filename )
		);

		$attach_id = wp_insert_attachment( $attachment, $upload_dir['path'] . '/' . $hashed_filename, $post_id );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_path . $hashed_filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );


		if ( $destination == "logo" ) {
			set_post_thumbnail( $post_id, $attach_id );
		} elseif ( $destination == "cover" ) {
			update_field( 'image_de_cover', $attach_id, $post_id );
		}

	}

	/**
	 * NEW ASSOCIATION PAGE CREATION
	 *
	 * @param $postData
	 * @param $address_data
	 * @param $contact_data
	 * @param $association_type_data
	 * @param $social_data
	 * @param $image_data
	 * @param $user_id
	 */
	public function create_association_page( $postData, $address_data, $contact_data, $association_type_data, $social_data, $image_data, $user_id ) {

		$new_post = array(
			'post_type'      => 'associations',
			'post_title'     => $postData['title'],
			'post_content'   => $postData['content'],
			'post_status'    => 'draft',
			'post_date'      => date( 'Y-m-d H:i:s' ),
			'post_author'    => $user_id,
			'comment_status' => 'closed'
		);

		if ( $post_id = wp_insert_post( $new_post ) ) {

			// POST CREATION - SUCCESS
			$this->update_localization( $address_data, $post_id );
			$this->update_contact( $contact_data, $post_id );
			$this->update_association_type( $association_type_data, $post_id );
			$this->update_social( $social_data, $post_id );
			$this->update_image( $image_data, $post_id, sanitize_title( $postData['title'] ) );

		} else {

			// POST CREATION - ERROR

		}

	}

	/**
	 * ASSOCIATION - Update Address
	 *
	 * @param $address_data
	 * @param $post_id
	 */
	public function update_localization( $address_data, $post_id ) {

		update_field( "adresse", $address_data['address'], $post_id );
		update_field( "code_postal", $address_data['zipcode'], $post_id );
		update_field( "ville", $address_data['city'], $post_id );
		update_field( "association_lat_coordonnees_gps", $address_data['lat'], $post_id );
		update_field( "association_lng_coordonnees_gps", $address_data['lng'], $post_id );

	}

	/**
	 * ASSOCIATION - Update contact informations
	 *
	 * @param $contact_data
	 * @param $post_id
	 */
	public function update_contact( $contact_data, $post_id ) {

		update_field( "prenom", $contact_data['user_firstname'], $post_id );
		update_field( "nom", $contact_data['user_lastname'], $post_id );
		update_field( "email", $contact_data['user_email'], $post_id );
		update_field( "telephone", $contact_data['user_phone'], $post_id );
		update_field( "contact_wp", $contact_data['user_id'], $post_id );

	}

	/**
	 * ASSOCIATION - Update type
	 *
	 * @param $association_type_data
	 * @param $post_id
	 */
	public function update_association_type( $association_type_data, $post_id ) {

		update_field( "type", $association_type_data['type'], $post_id );
		update_field( "organisation_interet_general", trim( $association_type_data['interest'] ), $post_id );
		update_field( "upcycling", trim( $association_type_data['upcycling'] ), $post_id );

	}

	/**
	 * ASSOCIATION - Update Social informations
	 *
	 * @param $social_data
	 * @param $post_id
	 */
	public function update_social( $social_data, $post_id ) {

		update_field( "website", $social_data['website'], $post_id );
		update_field( "instagram", $social_data['instagram'], $post_id );
		update_field( "twitter", $social_data['twitter'], $post_id );
		update_field( "facebook", $social_data['facebook'], $post_id );

	}

	/**
	 * ASSOCIATION - Update images data
	 *
	 * @param $image_data
	 * @param $post_id
	 * @param $post_title
	 */
	public function update_image( $image_data, $post_id, $post_title ) {

		foreach ( $image_data as $key => $image ) {
			$this->save_image( $image, $post_id, $post_title, $key );
		}
	}

	/**
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array $attributes The PHP variables for the template
	 *
	 * @return string               The contents of the template.
	 */
	private function get_template_html( $template_name, $attributes = null ) {
		if ( ! $attributes ) {
			$attributes = array();
		}

		ob_start();

		do_action( 'personalize_login_before_' . $template_name );

		require( 'templates/' . $template_name . '.php' );

		do_action( 'personalize_login_after_' . $template_name );

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}


	public function notification_bubble_user_in_admin_menu() {
		global $menu;
		$newitem     = $this->get_number_of_new_user();
		$menu[70][0] .= $newitem ? "<span class='update-plugins fdm count-1'><span class='update-count'>$newitem </span></span>" : '';
	}

	public function get_number_of_new_user() {
		global $wpdb;

		$sql    = "SELECT COUNT(*) FROM  wp_usermeta";
		$sql    .= " WHERE meta_key= 'user_status'";
		$sql    .= " AND meta_value= 'pending'";
		$result = $wpdb->get_var( $sql );

		return $result;
	}

	public function create_custom_user_role() {

		add_role(
			'association_role',
			__( 'Association' ),
			array(
				'read'       => true,  // true allows this capability
				'edit_posts' => true,
			)
		);

	}

	/*******************************************************************************************************************/
	/*******************************************************************************************************************/
	/********************************************* LOGGEDIN PART *******************************************************/
	/*******************************************************************************************************************/
	/*******************************************************************************************************************/

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $content The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_login_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array(
			'show_title' => true
		);
		$attributes         = shortcode_atts( $default_attributes, $attributes );


		if ( is_user_logged_in() ) {
			wp_redirect( home_url( 'edit-profile' ) );
		}

		wp_enqueue_script( 'pl-user-login' );


		// Render the login form using an external template
		return $this->get_template_html( 'login_form', $attributes );
	}


	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 */
	function redirect_to_custom_login() {

		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;

			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user( $redirect_to );
				exit;
			}

			// The rest are redirected to the login page
			$login_url = home_url( 'login' );

			if ( ! empty( $redirect_to ) ) {
				$login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
			}

			wp_redirect( $login_url );
			exit;
		}
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {

		$user = wp_get_current_user();
		if ( user_can( $user, 'manage_options' ) ) {
			if ( $redirect_to ) {
				wp_safe_redirect( $redirect_to );
			} else {
				wp_redirect( admin_url() );
			}
		} else {
			wp_redirect( home_url( 'edit-profile' ) );
		}
	}

	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error $user The signed in user, or the errors that have occurred during login.
	 * @param string $username The user name used to log in.
	 * @param string $password The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 */
	function maybe_redirect_at_authenticate( $user, $username, $password ) {
		// Check if the earlier authenticate filter (most likely,
		// the default WordPress authentication) functions have found errors
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			if ( is_wp_error( $user ) ) {
				$error_codes = join( ',', $user->get_error_codes() );

				$login_url = home_url( 'login' );
				$login_url = add_query_arg( 'login', $error_codes, $login_url );

				wp_redirect( $login_url );
				exit;
			}
		}

		return $user;
	}

	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {

		switch ( $error_code ) {
			case 'empty_username':
				return __( 'Please enter your e-mail address.', 'personalize-login' );

			case 'empty_password':
				return __( 'Please enter your password.', 'personalize-login' );

			case 'invalid_username':
				return __(
					"We can not find your e-mail, maybe you have another one when you register?",
					'personalize-login'
				);
			case 'invalid_email':
				return __(
					"We can not find your e-mail, maybe you have another one when you register?",
					'personalize-login'
				);

			case 'incorrect_password':
				$err = __(
					"Bad password. <a href='%s'>Forgot your password ?</a>",
					'personalize-login'
				);

				return sprintf( $err, wp_lostpassword_url() );

			// Registration errors

			case 'email':
				return __( 'Invalid E-mail address.', 'personalize-login' );

			case 'email_exists':
				return __( 'This email address is already in use.', 'personalize-login' );

			case 'closed':
				return __( 'You can not create an account at this time.', 'personalize-login' );

			// Lost password

			case 'invalid_email':
			case 'invalidcombo':
				return __( 'No users found with this email address.', 'personalize-login' );

			// Reset password

			case 'expiredkey':
			case 'invalidkey':
				return __( 'The URL used to reset the password is no longer valid.', 'personalize-login' );

			case 'password_reset_mismatch':
				return __( "The fields \"Passwords\" must be identical", 'personalize-login' );

			case 'password_reset_empty':
				return __( "Please fill in the fields \"Password\"", 'personalize-login' );

			default:
				break;
		}

		return __( 'A technical error has occurred. Please try again later.', 'personalize-login' );
	}

	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string $redirect_to The redirect destination URL.
	 * @param string $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {

		$redirect_url = home_url();

		if ( ! isset( $user->ID ) ) {
			return $redirect_url;
		}

		if ( user_can( $user, 'manage_options' ) ) {
			// Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
			if ( $requested_redirect_to == '' ) {
				$redirect_url = admin_url();
			} else {
				$redirect_url = $requested_redirect_to;
			}
		} else {
			// Non-admin users always go to their account page after login
			$redirect_url = home_url();
		}

		return wp_validate_redirect( $redirect_url, home_url() );
	}

	/**
	 * Redirect to custom login page after the user has been logged out.
	 */
	public function redirect_after_logout() {
		$redirect_url = home_url( '?logged_out=true' );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/*******************************************************************************************************************/
	/*******************************************************************************************************************/
	/********************************************* REGISTER PART *******************************************************/
	/*******************************************************************************************************************/
	/*******************************************************************************************************************/


	/**
	 * A shortcode for rendering the new user registration form.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $content The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_register_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		if ( ! is_user_logged_in() ) {
			wp_enqueue_script( 'pl-user-register' );

			return $this->get_template_html( 'register_form', $attributes );
		} else {
			return '';
		}

	}

	/**
	 * Redirects the user to the custom registration page instead
	 * of wp-login.php?action=register.
	 */
	public function redirect_to_custom_register() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
			} else {
				wp_redirect( home_url( 'registration' ) );
			}
			exit;
		}
	}

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email The new user's email address
	 * @param string $first_name The new user's first name
	 * @param string $last_name The new user's last name
	 * @param string $nickname The new user's nickname
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
	private function register_user( $email, $first_name, $last_name, $nickname ) {
		$errors = new WP_Error();

		// Email address is used as both username and email. It is also the only
		// parameter we need to validate


		if ( ! is_email( $email ) ) {

			$message = $this->get_error_message( 'email' );

			echo json_encode( array(
					'action'     => 'fail',
					'alert_type' => 'danger',
					'message'    => $message
				)
			);

			/*echo json_encode( array(
				'registered' => false,
				'message'    => $message
			) );*/

			die();


		}

		if ( username_exists( $email ) || email_exists( $email ) ) {

			$message = $this->get_error_message( 'email_exists' );

			echo json_encode( array(
					'action'     => 'fail',
					'alert_type' => 'danger',
					'message'    => $message
				)
			);

			/*echo json_encode( array(
				'registered' => false,
				'message'    => $message
			) );*/

			die();

		}

		// Generate the password so that the subscriber will have to check email...
		$password = wp_generate_password( 12, false );

		$user_data = array(
			'user_login' => strtolower( $first_name ) . '.' . strtolower( $last_name ) . time(),
			'user_email' => $email,
			'user_pass'  => $password,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'nickname'   => $nickname,
			'role'       => 'association_role'
		);

		$user_id = wp_insert_user( $user_data );

		if ( ! is_wp_error( $user_id ) ) {
			// SEND EMAIL NOTIFICATION TO USER
			//wp_new_user_notification( $user_id, $password );

			// UPDATE THE PENDING STATUS FOR THE GIVEN USER
			update_field( 'user_status', 'pending', 'user_' . $user_id . '' );
		}

		return $user_id;

	}

	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
	/*public function do_register_user() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$redirect_url = home_url( 'registration' );

			if ( ! get_option( 'users_can_register' ) ) {
				// Registration closed, display error
				$redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
			} else {
				$email      = $_POST['email'];
				$first_name = sanitize_text_field( $_POST['first_name'] );
				$last_name  = sanitize_text_field( $_POST['last_name'] );
				$nickname   = sanitize_text_field( $_POST['nickname'] );

				$result = $this->register_user( $email, $first_name, $last_name, $nickname );

				if ( is_wp_error( $result ) ) {
					// Parse errors into a string and append as parameter to redirect
					$errors       = join( ',', $result->get_error_codes() );
					$redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				} else {
					// Success, redirect to login page.
					$redirect_url = home_url( 'login' );
					$redirect_url = add_query_arg( 'registered', $email, $redirect_url );
				}
			}

			wp_redirect( $redirect_url );
			exit;
		}
	}*/


	/*******************************************************************************************************************/
	/*******************************************************************************************************************/
	/********************************************* PASSWORD LOST *******************************************************/
	/*******************************************************************************************************************/
	/*******************************************************************************************************************/

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of
	 * wp-login.php?action=lostpassword.
	 */
	public function redirect_to_custom_lostpassword() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}

			wp_redirect( home_url( 'password-lost' ) );
			exit;
		}
	}

	/**
	 * A shortcode for rendering the form used to initiate the password reset.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $content The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_password_lost_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		return $this->get_template_html( 'password_lost_form', $attributes );

	}

	/**
	 * Initiates password reset.
	 */
	/*public function do_password_lost() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = home_url( 'password-lost' );
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = home_url( 'login' );
				$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
			}

			wp_redirect( $redirect_url );
			exit;
		}
	}*/


	/*******************************************************************************************************************/
	/*******************************************************************************************************************/
	/******************************************** PASSWORD RESET *******************************************************/
	/*******************************************************************************************************************/
	/*******************************************************************************************************************/


	/**
	 * Redirects to the custom password reset page, or the login page
	 * if there are errors.
	 */
	public function redirect_to_custom_password_reset() {

		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			// Verify key / login combo
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );

			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( 'login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( 'login?login=invalidkey' ) );
				}
				exit;
			}

			$redirect_url = home_url( 'password-reset' );
			$redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
			$redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * A shortcode for rendering the form used to reset a user's password.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $content The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_password_reset_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already logged in.', 'personalize-login' );
		} else {
			if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
				$attributes['login'] = $_REQUEST['login'];
				$attributes['key']   = $_REQUEST['key'];

				// Error messages
				$errors = array();
				if ( isset( $_REQUEST['error'] ) ) {
					$error_codes = explode( ',', $_REQUEST['error'] );

					foreach ( $error_codes as $code ) {
						$errors [] = $this->get_error_message( $code );
					}
				}
				$attributes['errors'] = $errors;

				return $this->get_template_html( 'password_reset_form', $attributes );
			} else {
				return __( 'Invalid password renewal URL.', 'personalize-login' );
			}
		}
	}

	/**
	 * Resets the user's password if the password reset form was submitted.
	 */
	public function do_password_reset() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$rp_key   = $_REQUEST['rp_key'];
			$rp_login = $_REQUEST['rp_login'];

			$user = check_password_reset_key( $rp_key, $rp_login );

			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( 'login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( 'login?login=invalidkey' ) );
				}
				exit;
			}

			if ( isset( $_POST['pass1'] ) ) {
				if ( $_POST['pass1'] != $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = home_url( 'password-reset' );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

					wp_redirect( $redirect_url );
					exit;
				}

				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = home_url( 'password-reset' );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

					wp_redirect( $redirect_url );
					exit;
				}

				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				wp_redirect( home_url( 'login?password=changed' ) );
			} else {
				echo "Invalid request.";
			}

			exit;
		}
	}

}

// Initialize the plugin
$personalize_login_pages_plugin = new Personalize_Login_Plugin();

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'Personalize_Login_Plugin', 'plugin_activated' ) );