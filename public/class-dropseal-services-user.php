<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://upsite.top/
 * @since      1.0.0
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Includes methods for processing and validating data from new and existing users.
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/public
 * @author     UPsite <info@upsite.top>
 */
class Dropseal_Services_User {

	/**
	 * @param string $phone
	 * @return user login or bool(false)
	 */
	public function get_user_login_by_phone( $phone ) {
		global $wpdb;

		$phones = $wpdb->get_results( "SELECT user_id, meta_value FROM wp_usermeta WHERE meta_key = 'dss_phone'", ARRAY_A );

		foreach ( $phones as $v ) {

			if ( in_array($phone, $v) ) {
				$user_id = $v['user_id'];
			}
		}

		if ( $user_id ) {
			$user = get_user_by( 'id', $user_id );
			$user_login = $user->user_login;
			return $user_login;
		}
		return false;	
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @return bool
	 */
	public function dss_change_user_password() {
		if ( isset($_POST['update_pass']) ) {
			if ( $user = get_user_by('email', ($_POST['email'])) ) {
				$hash = $user->data->user_pass;
			}
			if ( !wp_check_password( $_POST['old_password'], $hash ) ) {
				$_POST['dss_change_pswd'] = 'existing_pswd';

				return;
			}
			if ( mb_strlen($_POST['new_password']) < 6 ) {
				$_POST['dss_change_pswd'] = 'is_too_short_pswd';

				return;
			}
			wp_set_password( $_POST['new_password'], get_current_user_id() );
			$_POST['dss_change_pswd'] = 'success';
		}
		return;
	}

	/**
	 * Checking the availability of a mobile number or a mobile operator with a user
	 * @return bool
	 */
	public function isset_phone_or_carrier() {

		$userid = get_current_user_id();
		$phone = get_user_meta($userid, 'dss_phone', true);
		$mobile_carrier = get_user_meta($userid, 'dss_mobile_carrier', true);

		if ( !$phone || !$mobile_carrier ) {
			return false;
		}
		return $mobile_carrier;		
	}

	/*
	 * Manipulation after clicking on the link from the email to reset the password
	 * login_form_rp & login_form_resetpass actions handler
	 */
	public function dss_to_custom_password_reset(){
	 
		$key = $_REQUEST['key'];
		$login = $_REQUEST['login'];
		$forgot_pass_page_slug = '/forgot-password';
		$login_page_slug = '/login';

		if ( ( 'GET' == $_SERVER['REQUEST_METHOD'] || 'POST' == $_SERVER['REQUEST_METHOD'] )
			&& isset( $key ) && isset( $login ) ) {
	 
			$user = check_password_reset_key( $key, $login );
	 
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( site_url( $login_page_slug . '?errno=expiredkey' ) );
				} else {
					wp_redirect( site_url( $login_page_slug . '?errno=invalidkey' ) );
				}
				exit;
			}	 
		}	 
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	 
			$to = site_url( $forgot_pass_page_slug );
			$to = add_query_arg( 'login', esc_attr( $login ), $to );
			$to = add_query_arg( 'key', esc_attr( $key ), $to );
	 
			wp_redirect( $to );
			exit;
	 
		} elseif ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	 
			if ( isset( $_POST['pass1'] ) ) {
	 
	 			if ( $_POST['pass1'] != $_POST['pass2'] ) {

					$to = site_url( $forgot_pass_page_slug );
	 
					$to = add_query_arg( 'key', esc_attr( $key ), $to );
					$to = add_query_arg( 'login', esc_attr( $login ), $to );
					$to = add_query_arg( 'errno', 'password_reset_mismatch', $to );
	 
					wp_redirect( $to );
					exit;
				}	 
				if ( empty( $_POST['pass1'] ) ) {

	 				$to = site_url( $forgot_pass_page_slug );
	 
					$to = add_query_arg( 'key', esc_attr( $key ), $to );
					$to = add_query_arg( 'login', esc_attr( $login ), $to );
					$to = add_query_arg( 'errno', 'password_reset_empty', $to );
	 
					wp_redirect( $to );
					exit;
				}	 
				// here you can set additional password settings
				reset_password( $user, $_POST['pass1'] );
				wp_redirect( site_url( $login_page_slug . '?errno=changed' ) );
	 
			} else {
				echo 'Something went wrong.';
			}	 
			exit;	 
		}	 
	}

	/**
	 * redirect the standard password reset form
	 * login_form_lostpassword action handler
	 */
	public function dss_pass_reset_redir() {

		$forgot_pass_page_slug = '/forgot-password';

		$login_page_slug = '/login';
		// if someone went to the password reset page
		// (!) It’s passed, but not sent by the form,
		// then redirect to our custom password reset page
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] && !is_user_logged_in() ) {
			wp_redirect( site_url( $forgot_pass_page_slug ) );
			exit;
		} elseif ( 'POST' == $_SERVER['REQUEST_METHOD'] ) { // if on the contrary, the form was submitted

			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
            		// if errors occur, return the user back to the form
            		$to = site_url( $forgot_pass_page_slug );
            		$to = add_query_arg( 'errno', join( ',', $errors->get_error_codes() ), $to );
	        	} else {
            		// if there were no errors, redirect to the login with a success message
            		$to = site_url( $login_page_slug );
            		$to = add_query_arg( 'errno', 'confirm', $to );
	        	}

	        	wp_redirect( $to );
	        	exit;
		}
	}

	/**
	 * admin_init action handler
	 * block access to WordPress admin panel to everyone except admin
	 */
	public function dss_only_admin() {
		if ( ! current_user_can( 'manage_options' ) && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] ) {
            wp_redirect( site_url() );
	    }
	}
	
	/**
	 * User registration
	 * @return error-mesage or window.location
	 */
	public function dss_register_user() {
		if ( $this->get_user_login_by_phone($_POST['phone']) ) {
			$_POST['dss_error'] = 'Password length not less than 6 characters.';
			return;
		}
		if ( mb_strlen( $_POST['phone'] ) != 10 ) {
			$_POST['dss_error'] = 'Password length not less than 6 characters.';
			return;
		}
		if ( mb_strlen( $_POST['password'] ) < 6 ) {
			$_POST['dss_error'] = 'Password length not less than 6 characters.';
			return;
		}

		$userdata = array(
			'user_pass'    => $_POST['password'],
			'user_email'   => $_POST['email'],
			'first_name'   => $_POST['user_name'],
			'user_login'   => $_POST['user_name'],
			'role' 		   => 'subscriber',
			'show_admin_bar_front' => 'false'
	 	);

	 	$user_id = wp_insert_user( $userdata );

		if( is_wp_error( $user_id ) ) {
			$_POST['dss_error'] = $user_id->get_error_message();
			return;
		}
        wp_clear_auth_cookie();
        wp_set_current_user( $user_id ); // Set the current user detail
		wp_set_auth_cookie( $user_id );
		
		$qty_messages = get_option( 'dss_max_pending_messages' );
		update_user_meta( $user_id, 'dss_qty_messages', $qty_messages );
		update_user_meta( $user_id, 'dss_phone', $_POST['phone'] );
		update_user_meta( $user_id, 'dss_mobile_carrier', $_POST['mobile_carrier'] );

		$url = home_url() . '/my-account?success-registration';
		wp_redirect( $url );
	}

	/** 
	 * wp_loaded action handler
	 * @return error-mesage or wp_redirect()
	 */
	public function dss_user_login() {
		if ( isset($_POST['dss_login']) ) {

			if ( !$login = $this->get_user_login_by_phone($_POST['login']) ) {
				$login = $_POST['login'];
			}
			$user = wp_authenticate( $login, $_POST['password'] );

			if ( is_wp_error( $user ) ) {
				$error_string = $user->get_error_message();
				echo '<div class="dss-login-error">' . $error_string . '</div>';
			}
			else {
		        wp_clear_auth_cookie();
		        wp_set_current_user( $user->ID ); // Set the current user detail
				wp_set_auth_cookie( $user->ID );

				$time = strtotime( wp_date( 'j F Y H:i:s' ) );
				update_user_meta(  $user->ID, 'wp-last-login', $time );
				wp_redirect( site_url() );
			}
		}
	}
}