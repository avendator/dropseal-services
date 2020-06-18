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
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * Connects pages with HTML parts & ajax handlers
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/public
 * @author     UPsite <info@upsite.top>
 */
use Twilio\Rest\Client;

class Dropseal_Services_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class passed to the run() function
		 * defined in Dropseal_Services_Loader
		 *
		 * The Dropseal_Services_Loader will then create the relationship
		 * between the defined here hooks and the functions
		 */
		if ( is_page( array('sms-service', 'custom-service', 'my-account', 'edit-sms', 'edit-crs', 'edit-profile', 'crs-orders', 'my-payment') ) ) {
			wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.13.0/css/all.css' );				
		}
		if ( is_page( array('sms-service', 'edit-sms', 'custom-service') ) ) {
			wp_enqueue_style('air-datepicker', dirname(plugin_dir_url( __FILE__ )) . '/lib/air-datepicker/datepicker.min.css');
		}
		if ( is_page( array('sms-service', 'edit-sms') ) ) {
			wp_enqueue_style( 'dropseal-services-sms', plugin_dir_url( __FILE__ ) . 'css/dropseal-services-sms.css', array('responsive-style', 'child-style'), $this->version, 'all' );			
		}
		if ( is_page( array('custom-service', 'edit-crs') ) ) {
			wp_enqueue_style( 'dropseal-services-crs', plugin_dir_url( __FILE__ ) . 'css/dropseal-services-crs.css', array('responsive-style', 'child-style'), $this->version, 'all' );	
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dropseal-services-public.css', array('responsive-style', 'child-style'), $this->version, 'all' );	
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Dropseal_Services_Loader
		 *
		 * The Dropseal_Services_Loader will then create the relationship
		 * between the defined hooks and the functions
		 */

		wp_deregister_script( 'jquery-core' );
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', false, null, true );
		wp_enqueue_script( 'jquery' );

		if ( is_page( array('sms-service', 'custom-service') ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dropseal-services-public.js', array( 'jquery' ), $this->version, true );	
			wp_enqueue_script( 'dropseal-services-ajax', plugin_dir_url( __FILE__ ) . 'js/dropseal-services-ajax.js', array( 'jquery' ), $this->version, true );
		}
		if ( is_page( array('edit-sms', 'edit-crs') ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dropdeal-services-order-edit.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'dropseal-services-ajax', plugin_dir_url( __FILE__ ) . 'js/dropseal-services-ajax.js', array( 'jquery' ), $this->version, true );
		}
		if ( is_page('register') || is_page('edit-profile') ) {
			wp_enqueue_script( 'dropseal-services-registration', plugin_dir_url( __FILE__ ) . 'js/dropseal-services-registration.js', array( 'jquery' ), $this->version, true );
		}
		if ( is_page( array('my-account', 'crs-orders', 'edit-profile', 'my-payment') ) ) {
			wp_enqueue_script( 'dropseal-services-account', plugin_dir_url( __FILE__ ) .'js/dropseal-services-account.js', array( 'jquery' ), $this->version, true );
		}
		if ( is_page( array('sms-service', 'edit-sms', 'custom-service') ) ) {
			wp_enqueue_script( 'datepicker-settings', plugin_dir_url( __FILE__ ) . 'js/datepicker-settings.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script('air-datepicker-js', dirname(plugin_dir_url( __FILE__ )) . '/lib/air-datepicker/datepicker.min.js', array( 'jquery' ), null, true );
		}
	}

	/**
	 * Change the time to automatically log out of account
	 */
	public function dss_change_cookie_logout( $expiration, $user_id, $remember ) {
		$log_out_period = get_option( 'dss_log_out_period' );

		if ( $log_out_period ) {
			$expiration = $log_out_period * 60;
		}
  
  		return $expiration;
	}

	/**
	 * triggered by the wp_paypal_order_processed hook
	 */
	public function dss_paypal_order_processed( $ipn_response ) {
		global $wpdb;

		$wpdb->insert( 'dss_'.$ipn_response['custom'].'_meta', [
			$ipn_response['custom'].'_id' => $ipn_response['item_number'], 
			'meta_key'					  => 'paypal_id', 
			'meta_value' 				  => $ipn_response['order_id']
		] );

		$status = 'pending';
		if ( get_option('dss_auto_confirm') ) {
			$status = 'processing';
		}
		if ( $ipn_response['custom'] == 'crs' ) {
			$status = 'processing';
		}
		$wpdb->update( 'dss_'.$ipn_response['custom'].'_data',
			['status' => $status],
			['id'	  => $ipn_response['item_number']]
		);
		if ( $ipn_response['custom'] == 'sms' ) {
			$order_id = $ipn_response['item_number'];
			$data = $wpdb->get_row( "SELECT customer_id, sms_limit FROM dss_sms_data WHERE id = '$order_id'" );
			$sms_meta = $this->get_sms_meta( $order_id );
			$qty_recipients = count( (array)$sms_meta['recipients'] );
			$this->check_and_write_sms_limit( $data->sms_limit, $data->customer_id, $qty_recipients );
		}
	}
	
	/**
	 * triggered by the wp_apple_pay_order_processed hook
	 */
	public function dss_apple_pay_order_processed( $ipn_response ) {
		global $wpdb;

		$wpdb->insert( 'dss_'.$ipn_response['product_description'].'_meta', [
			$ipn_response['product_description'].'_id' => $ipn_response['product_name'], 
			'meta_key'					 			   => 'apple_pay_id', 
			'meta_value' 				  			   => $ipn_response['order_id']
		] );

		$status = 'pending';
		if ( get_option('dss_auto_confirm') ) {
			$status = 'processing';
		}
		if ( $ipn_response['product_description'] == 'crs' ) {
			$status = 'processing';
		}
		$wpdb->update( 'dss_'.$ipn_response['product_description'].'_data',
			['status' => $status],
			['id' 	  => $ipn_response['product_name']]
		);
		if ( $ipn_response['product_description'] == 'sms' ) {
			$order_id = $ipn_response['product_name'];
			$data = $wpdb->get_row( "SELECT customer_id, sms_limit FROM dss_sms_data WHERE id = '$order_id'" );
			$sms_meta = $this->get_sms_meta( $order_id );
			$qty_recipients = count( (array)$sms_meta['recipients'] );
			$this->check_and_write_sms_limit( $data->sms_limit, $data->customer_id, $qty_recipients );
		}
	}

	/**
	 * Registration form
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 */
	public function dss_registration_form() {

  		$mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );

  		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-registration-form.php';
	}

	/**
	 * Login form
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 */
	public function dss_login_form() {

  		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-login-form.php';
	}

	/**
	 * Reset password form
	 * this is a shortcode handler function that is added to the define_public_hooks
	 * password reset process is declared in the class Dropseal_Services_User
	 * @return html
	 */
	public function dss_render_pass_reset_form() {

  		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-forgot-password.php';
	}

	/**
	 * SMS-service form
	 * Output html
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 * of the Dropseal_Services class
	 */
	public function dss_sms_service_form() {

		$user_id = get_current_user_id();
		$mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );
		$package_sms_cost = get_option( 'dss_sms_cost' );
		$qty_recipients = 2;
		$mobile_carrier_cost = get_option( 'dss_mobile_carrier_cost' );
		$percentage = get_option( 'dss_transfer_percentage' );
		$total = (int)$sms_cost + (int)$mobile_carrier_cost;
		$sms_qty_paid = get_option( 'dss_qty_paid_messages', true );
		$sms_limit = get_user_meta( $user_id, 'dss_qty_messages', true );
		if ( !(int)$sms_limit ) {
			$sms_limit = get_user_meta( $user_id, 'dss_qty_paid_messages', true );
		}
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-sms-service-form.php';
	}

	/**
	 * CRS-service form
	 * Output html
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 * of the Dropseal_Services class
	 */
	public function dss_crs_form() {

		if ( !get_option('dss_special_request') ) : ?>
			<div class="dss-notification-container">
				Service is temporarily unavailable. We apologize.
			</div>

			<?php return; ?>
		<?php endif;

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-crs-form.php';
	}

	/**
	 * Output html
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 * of the Dropseal_Services class
	 */
	public function dss_recipient_content() {

		if ( empty($_GET['id']) ) return;
		global $wpdb;

		$link = $_GET['id'];
		$sms_id = $wpdb->get_var( "SELECT id FROM dss_sms_data WHERE link = '$link'" );

		if ( !$sms_id ) return;

		$meta = $this->get_sms_meta( $sms_id );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-recipient-content.php';
	}

	/**
	 * Profile Edit Page
	 */
	public function dss_edit_profile() {

		$this->dss_is_user_logged_in();

		$current_user = wp_get_current_user();
		$mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-user-profile-edit.php';
	}

	/**
	 * My Payment Page
	 */
	public function dss_my_payment() {

		$this->dss_is_user_logged_in();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-user-payment-page.php';
	}

	/**
	 * CRS orders list
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 * of the Dropseal_Services class
	 */
	public function dss_crs_orders_display() {

		$this->dss_is_user_logged_in();
		// get crs orders
		$crs_data = $this->get_service_data('crs');

		$crs_ids = self::get_ids_list($crs_data);

		$crs_meta = $this->get_service_meta( 'crs', $crs_ids, 'content');

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-crs-orders-display.php';
	}

	/**
	 * SMS messages list
	 * this is a shortcode handler function that is added to the define_public_hooks 
	 * of the Dropseal_Services class
	 */
	public function dss_sms_orders_display() {

		$this->dss_is_user_logged_in();
		// get crs orders
		$sms_data = $this->get_service_data('sms');

		$sms_ids = self::get_ids_list($sms_data);

		$sms_meta = $this->get_service_meta( 'sms', $sms_ids, 'recipients');

		$sms_arr = [];
		$recipients = [];
		foreach ( $sms_meta as $meta ) {

			$sms = is_array( json_decode( $meta->meta_value ) ) || is_object( json_decode( $meta->meta_value ) ) ? json_decode( $meta->meta_value ) : $meta->meta_value;

			$sms_arr[] = $sms;

			$i = 1;
			foreach ( $sms as $value ) {

				if( $value->recipient_.$i ) {
					$recipient = $i;		
				}
		
				$i++;				
			}
			$recipients[] = $recipient;				
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-sms-messages-display.php';
	}

	/**
	 * message editing
	 * output sms-service form with data from DB
	 */
	public function dss_edit_sms_data() {

		if ( empty($_GET['id']) || !is_user_logged_in() ) return;

		global $wpdb;

		$user_id = get_current_user_id();
		$sms_id = $_GET['id'];

		// get sms message
		$delivery_data = $wpdb->get_row( "SELECT * FROM dss_sms_data WHERE id = '$sms_id' AND customer_id = $user_id" );

		if ( !$delivery_data->process_date ) return;

		$mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );
		$package_sms_cost = get_option( 'dss_sms_cost' );
		$mobile_carrier_cost = get_option( 'dss_mobile_carrier_cost' );
		$percentage = get_option( 'dss_transfer_percentage' );
		$total = (int)$sms_cost + (int)$mobile_carrier_cost;
		$mobile_carrier_tooltip = get_option( 'dss_mobile_carrier_tooltip' );
		$sms_limit = get_user_meta( $user_id, 'dss_qty_messages', true );
		if ( !(int)$sms_limit ) {
			$sms_limit = get_user_meta( $user_id, 'dss_qty_paid_messages', true );
			if( !(int)$sms_limit ) {
				$payment_notice = '<div class="message-error">You have no messages to send! Please pay for the new package!</div>';
			}
		}
    	$meta = $this->get_sms_meta( $sms_id );

		$qty_recipients = 3;
	    $editable_qty_recipients = count( (array)$meta['recipients'] );

    	$users = get_users( ['role' => 'subscriber', 'fields' => ['ID', 'display_name']] );

		$check = '';
		if( $meta['payment_email'] ) {
			$check = ' checked';
		}
		$reminder = '';
		if( $delivery_data->reminder ) {
			$reminder = ' checked';
		}

		$recipients = (array)$meta['recipients'];
		$recipients = array_keys($recipients);
		$qty_recipients = substr( end($recipients), 10 );
		
		if ( $delivery_data->status == 'completed' || $delivery_data->status == 'cancelled' ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-sms-order-info-display.php';
		}
		else {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-edit-sms-service-form.php';
		}
	}

	/**
	 * message editing
	 * output crs form with data from DB
	 */
	public function dss_edit_crs_data() {

		if ( empty($_GET['id']) || !is_user_logged_in() ) return;

		global $wpdb;

		$user_id = get_current_user_id();
		$crs_id = $_GET['id'];

		// get crs message
		$delivery_data = $wpdb->get_row( "SELECT * FROM dss_crs_data WHERE id = '$crs_id' AND customer_id = $user_id" );

		if ( !$delivery_data->process_date ) return;

		$crs_meta = $wpdb->get_results( "SELECT meta_key, meta_value FROM dss_crs_meta WHERE crs_id = '$crs_id'" );

    	foreach ( $crs_meta as $key ) {
    		$meta[$key->meta_key] = is_array( json_decode( $key->meta_value ) ) || is_object( json_decode( $key->meta_value ) ) ? json_decode( $key->meta_value ) : $key->meta_value; 
    	}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/dss-crs-order-info-display.php';
	}

	/**
	 * ajax - handler
	 * insert data from sms-service form
	 * @return string "success" or if writing to the database was successful
	 */
	public function save_sms_service_data() {

		if ( $_POST['action'] != 'save_sms_service_data' ) {
			return;
		}
		global $wpdb;

		$qty_recipients = 3;
		$user_id = get_current_user_id();
		// first - blacklist check
		$this->check_black_list( $_POST['pay_email'] );
		// second - create meta ( there is a blacklist check )
		$meta = $this->dss_create_sms_meta( $qty_recipients );
	    // third - file upload			
		$file_path = $this->insert_file( $_FILES );
		// check file path
		$this->is_file_path( $file_path );
		$meta['file_path'] = $file_path ?: '';
		$status = $_POST['payment_status'] ?? 'pending';
		$status = 'pending';

		if ( get_option('dss_auto_confirm') ) {
			$status = 'processing';
		}
		$status = $_POST['payment_status'] ?? $status;

		$new_order_data = [
			'customer_id'  => $user_id,
			'process_date' => strtotime( $_POST['datetime'] ),
			'reminder'	   => $_POST['reminders'] ? 1 : 0,
			'status'       => $status,
		];

		//fourth step - write data in db
		$wpdb->insert( 'dss_sms_data', $new_order_data );	
		$sms_id = $wpdb->insert_id;
		// recipient content link
		$wpdb->update( 'dss_sms_data', ['link' => wp_hash_password($sms_id)], ['id' => $sms_id] );

		foreach ( $meta as $k => $v ) {
			$new_order_meta = [
				'sms_id'     => $sms_id,
				'meta_key'   => $k,
				'meta_value' => is_array( $v ) ? json_encode( $v ) : $v,
			];
			// fifth step - write meta in db
			$wpdb->insert( 'dss_sms_meta', $new_order_meta );
		}

		$sms_limit = $_POST['sms_limit'];
		$wpdb->update( 'dss_sms_data', ['sms_limit' => $sms_limit], ['id' => $sms_id], '%d');

		if( $_POST['payment_status'] ) {		
			$data['order_id'] = $sms_id;
		    echo json_encode($data);
			wp_die();
		}
		else {
			$qty_recipients = count( $meta['recipients'] );
			$this->check_and_write_sms_limit( $sms_limit, $user_id, $qty_recipients );
		}

		$data['url'] = home_url() . '/thank-you-page?id='.$sms_id;
	    echo json_encode($data);
		wp_die();
	}

	/**
	 * ajax - handler
	 * insert data from crs-service form
	 * @return string "success" if writing to the database was successful or...
	 */
	public function save_crs_service_data() {
		if ( $_POST['action'] != 'save_crs_service_data' ) {
			return;
		}
		global $wpdb;

	    // file upload			
		$file_path = $this->insert_file( $_FILES );
		// check file path
		$this->is_file_path( $file_path );

		$user_id = get_current_user_id();

		$new_order_data = [
			'customer_id'  => $user_id,
			'process_date' => strtotime( $_POST['datetime'] ),
			'status'       => 'pending',
		];
		// write data in db
		$wpdb->insert( 'dss_crs_data', $new_order_data );
		$crs_id = $wpdb->insert_id;
		
		$meta = [
			'content'   	=> $_POST['sms_text'] ?? '',
			'links'     	=> $_POST['links'] ?? '',
			'order_price'   => '',
			'file_path' 	=> $file_path ?: '',
		];

		foreach ( $meta as $k => $v ) {
			$new_order_meta = [
				'crs_id'     => $crs_id,
				'meta_key'   => $k,
				'meta_value' => is_array( $v ) ? json_encode( $v ) : $v,
			];
			// write meta in db
			$wpdb->insert( 'dss_crs_meta', $new_order_meta );
		}

	    $template = get_option( 'dss_template_success' );
		Dropseal_Services_Admin::send_message( $template, $wpdb->insert_id, 'crs' );

		$data['url'] = home_url() . '/thank-you-page?crs-id='.$crs_id;
	    echo json_encode($data);
		wp_die();
	}

	/**
	 * ajax - handler
	 * message update after of editing
	 * @return recording status message
	 */
	public function update_sms_data() {

		if ( $_POST['action'] != 'dss_update_sms_data' ) {
			return;
		}
		global $wpdb;

	    $id = $_POST['id'];
	    $qty_recipients = 3;
	    $user_id = get_current_user_id();
		$status = $this->get_servise_status($id, 'sms');

		// create meta ( there is a blacklist check )
		$meta = [
			'content'       => $_POST['sms_text'] ?? '',
			'links'         => $_POST['links'] ?? '',
		];

		$meta['recipients'] = $this->create_recipients_data( $qty_recipients );
		
		$file_path = $this->insert_file( $_FILES );
		// check file path
		if ( $this->is_file_path( $file_path ) ) {
			$meta['file_path'] = $file_path;
		}

	    $new_order_data = ['reminder' => $_POST['reminders'] ? 1 : 0];	

	    if ( $_POST['datetime'] ) {
	    	$new_order_data['process_date'] = strtotime( $_POST['datetime'] );	
	    }

	    $res = $wpdb->update( 'dss_sms_data', $new_order_data, ['id' => $id] );

	    // update meta in db
	    foreach ( $meta as $k => $v ) {
	        $wpdb->update( 'dss_sms_meta', ['meta_value' => is_array( $v ) ? json_encode( $v ) : $v], ['sms_id' => $id, 'meta_key' => $k] );
	    }

	    $template = get_option( 'dss_template_success' );
		Dropseal_Services_Admin::send_message( $template, $id, 'sms' );

		if( $res !== false ) {
			$data['url'] = home_url() . '/thank-you-page?update-sms-id='.$id;
		    echo json_encode($data);
		}
	    wp_die();
	}

	/**
	 * shortkode - handler
	 * redirect to Thank You Page
	 * update message / order status after payment
	 * @return void
	 */
	public function dss_after_payment_handler() {

		if ( !is_user_logged_in() ) return;

		global $wpdb;
		?>
		<br>
		<div class="dss-notification-container">

		<?php $user_id = get_current_user_id(); 
		?>

		<?php if ( isset($_GET['crs-id']) && $_GET['crs-id'] != '' ) : ?>
			<div>Your Order ID#<?php echo $_GET['crs-id']; ?> successfully created!</div>
			<div>The details can be tracked in your <a href="/crs-orders/">account</a></div>
		<?php endif; ?>

		<?php if ( isset($_GET['payment-crs-id']) && $_GET['payment-crs-id'] != '' ) : ?>
			<div>Your Order ID#<?php echo $_GET['payment-crs-id']; ?> successfully paid!</div>
		<?php endif; ?>

		<?php //sms order ?>
		<?php if ( isset($_GET['id']) && $_GET['id'] != '' ) : ?>
			<?php 
			$sms_id = $_GET['id'];
			$sms_limit = $wpdb->get_var( "SELECT sms_limit FROM dss_sms_data WHERE id = '$sms_id' AND customer_id = '$user_id'" );

		    $template = get_option( 'dss_template_success' );
			Dropseal_Services_Admin::send_message( $template, $_GET['id'], 'sms' );
			?>
			<div>Your SMS ID#<?php echo $_GET['id']; ?> successfully created!</div>
			<div>Your SMS Limit: <?php echo $sms_limit; ?></div>
			<div>Status and shipping details can be tracked in your <a href="/my-account/">account</a></div>
		<?php endif; ?>

		<?php if ( isset($_GET['update-sms-id']) && $_GET['update-sms-id'] != '' ) : ?>
			<div>Your SMS ID#<?php echo $_GET['update-sms-id']; ?> successfully updated!</div>
			<div>The details can be tracked in your <a href="/my-account/">account</a></div>
		<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Ajax handlers method
	 */
	public function dss_public_handlers() {
		global $wpdb;

		switch ( $_POST['request'] ) {
			case 'dss_change_order_status':
				$service = $_POST['service'];
				$order_id = $_POST['id'];

				// check for void
				$this->get_servise_status($order_id, $service);

				$status = $_POST['status'];

				$wpdb->update( 'dss_'.$service.'_data', ['status' => $status], ['id' => $order_id] );
				Dropseal_Services_Admin::delete_file( $service, $order_id );

      			if ( $status == 'cancelled' ) {
           			$template = get_option( 'dss_template_cancelled' );
					Dropseal_Services_Admin::send_message( $template, $order_id, $service );
        		}

				$notice = 'Order';
				if ( $service == 'sms' ) { 
					$notice = 'Message'; 
				}
				echo json_encode($notice);
				break;
			case 'dss_get_mobile_carriers':
				$mobile_carriers = get_option( 'dss_mobile_carriers' );
				$tooltip = get_option( 'dss_mobile_carrier_tooltip' );
				$mobile_carriers = [$mobile_carriers, $tooltip];

				echo json_encode($mobile_carriers);
				break;		
		}	
		wp_die();		
	}

	/**
	 * @param string $status
	 * @return string
	 */
	public static function get_status_color( $status ) {
		switch ( $status ) {
			case 'completed':
				$color = '#0CAC5F';
				break;
			case 'processing':
			case 'pending':
				$color = '#FFA500';
				break; 
			case 'cancelled':
				$color = '#FF0000';
				break; 		
			default:
				$color = '#808080';
				break;
		}

		return $color;
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public static function get_ids_list( $data ) {
		$ids = [];

		foreach ( $data as $value ) {
			$ids[] = $value['id'];
		}
		$ids = implode( ', ', $ids );

		return $ids;	
	}

	/**
	 * @param string $service
	 * @param string $payment_system_id
	 *
	 * @return array / null
	 */
	public static function get_payment_orders_by_user( $service, $payment_system_id ) {
		global $wpdb;

		$user_id = get_current_user_id();
		$payment_data = $wpdb->get_results( "SELECT id FROM dss_{$service}_data WHERE customer_id = '$user_id'", ARRAY_A );
		$payment_ids = self::get_ids_list( $payment_data );
		$payment_meta = $wpdb->get_results( "SELECT meta_value FROM dss_{$service}_meta WHERE {$service}_id IN ($payment_ids) AND meta_key = '$payment_system_id' ORDER BY {$service}_id DESC", ARRAY_A );

		return $payment_meta;
	}

	/**
	 * insert attachement file from service forms
	 * @return string $file_name or array $error
	 */
	private function insert_file( $files ) {

		if( $_FILES['file']['error'] ) {
			$error = ['error' => 'File was not uploaded!'];
			return $error;
		}

		$file_size = get_option( 'dss_upload_file_size' );
		$size = $file_size * 1024 * 1024;
	    if ( $files['file']['size'] > $size ) {
        	$error = ['error' => 'File size must not exceed '.$file_size.'MB. File was not uploaded!'];
        	return $error;
   		}
   		if ( $files['file']['size'] == 0 ) {
   			return;
   		}
		$directory = $_SERVER['DOCUMENT_ROOT'] . 'wp-content/plugins/dropseal-services/files/attachements/'.time();
		if ( !mkdir( $directory ) ) {
        	$error = ['error' => 'File was not uploaded!'];
        	return $error;
        }
		$file_path = $directory.'/'.$files['file']['name'];

        if (move_uploaded_file($files['file']['tmp_name'], $file_path)) return $file_path;
	}

	/**
	 * check file path
	 * @param mixed string or array
	 * @return bool(true) or wp_die()
	 */
	private function is_file_path( $file_path ) {

		if ( is_array($file_path) ) {
			$notice = '<div class="message-error">'.$file_path['error'].'</div>';
			echo json_encode($notice);

        	wp_die();
		}
		if ( $file_path ) return true;
		return false;
	}

	/**
	 * check servise status
	 * @param string $id
	 * @param string $service
	 * 
	 * @return string $status or wp_die()
	 */
	private function get_servise_status( $id, $service ) {
		global $wpdb;

		$table = 'dss_'.$service.'_data';
		$user_id = get_current_user_id();
		$status = $wpdb->get_var( "SELECT status FROM $table WHERE customer_id = '$user_id' AND id = '$id'" );

		if ( !$status ) {
			wp_die();
		}

		return $status;
	}

	/**
	 * Blacklist Check (emails, phones, domains)
	 * @param string $email
	 * @param string $phone
	 * @param string $domain
	 * 
	 * @return bool(true) or wp_die()
	 */
	private function check_black_list( $email = 0, $phone = 0 ) {

		$notice = ' is on the black list!</div>';

		if ( $email ) {
			$blocked_emails = get_option('dss_blocked_emails');
			if ( in_array($email, (array)$blocked_emails) ) {
				$notice = '<div class="message-error">The email '.$email.$notice;
				echo json_encode($notice);
				wp_die();
			}
			$domain = explode('@', $email);
			$domain = '@'.$domain[1];
			$blocked_domains = get_option('dss_blocked_domains');
			if ( in_array($domain, (array)$blocked_domains) ) {
				$notice = '<div class="message-error">The domain '.$domain.$notice;
				echo json_encode($notice);
				wp_die();
			}					
		}
		if ( $phone ) {
			$blocked_numbers = get_option('dss_blocked_numbers');
			if ( in_array($phone, (array)$blocked_numbers) ) {
				$notice = '<div class="message-error">The number '.$phone.$notice;
				echo json_encode($notice);
				wp_die();
			}			
		}
		return true;
	}

	/**
	 * @param string $service
	 * @return array / null
	 */
	private function get_service_data( $service ) {
		global $wpdb;

		$user_id = get_current_user_id();
		$table = 'dss_'.$service.'_data';
		$data = $wpdb->get_results( "SELECT * FROM $table WHERE customer_id = '$user_id' AND status != 'trash' AND status != 'on_hold' ORDER BY id DESC", ARRAY_A );

		return $data;
	}

	/**
	 * @param string $service
	 * @param string $ids
	 * @param string $key
	 *
	 * @return object / null
	 */
	private function get_service_meta( $service, $ids, $key ) {
		global $wpdb;

		$user_id = get_current_user_id();
		$table = 'dss_'.$service.'_meta';
		$sort = $service.'_id';
		$meta = $wpdb->get_results( "SELECT meta_value FROM $table WHERE $sort IN ($ids) AND meta_key = '$key' ORDER BY $sort DESC" );

		return $meta;
	}

	/**
	 * @param string $id
	 *
	 * @return array
	 */
	private function get_sms_meta( $sms_id ) {
		global $wpdb;

		$sms_meta = $wpdb->get_results( "SELECT meta_key, meta_value FROM dss_sms_meta WHERE sms_id = '$sms_id'" );

    	foreach ( $sms_meta as $key ) {
        	$meta[$key->meta_key] = is_array( json_decode( $key->meta_value ) ) || is_object( json_decode( $key->meta_value ) ) ? json_decode( $key->meta_value ) : $key->meta_value; 
    	}

		return $meta;
	}

 	/**
	 * @return bool(true) / window.location
	 */
	private function dss_is_user_logged_in() {

		if ( !is_user_logged_in() ) {
			$url = home_url() . '/login';
			echo "<script>window.location.href = '{$url}'; </script>";
		}
		return true;
	}

	/**
	 * @param int $recipients
	 * @return array
	 */
	private function create_recipients_data( $qty_recipients ) {

	    for ( $i = 1; $i <= $qty_recipients; $i++ ) {        
	        if ( isset( $_POST['name_'.$i] ) ) {

	        	$this->check_black_list( $_POST['email_'.$i], $_POST['phone_'.$i] );

	            $data['recipient_'.$i] = [
	                    'name'           => $_POST['name_'.$i],
	                    'email'          => $_POST['email_'.$i] ?? '',
	                    'phone'          => $_POST['phone_'.$i],
	                    'mobile_carrier' => $_POST['mobile_carrier_'.$i] ?? '',
	            ];
	        }
	    }
		return $data;
	}

	/**
	 * @param int $qty_recipients
	 * @return array
	 */
	private function dss_create_sms_meta( $qty_recipients ) {

		$meta = [
			'content'       => $_POST['sms_text'] ?? '',
			'links'         => $_POST['links'] ?? '',
			'payment_email' => $_POST['pay_email'] ?? '',
			'money_amount'  => $_POST['money'] ?? '',
			'total_paid'	=> $_POST['total_paid']  ?? '',
		];

		$meta['recipients'] = $this->create_recipients_data( $qty_recipients );

		return $meta;
	}

	/**
	 * @param int $sms_qty
	 * @param int $user_id
	 *
	 * @return void
	 */
	private function check_and_write_sms_limit( $sms_qty, $user_id, $qty_recipients ) {
		$sms_limit_free = (int)get_user_meta( $user_id, 'dss_qty_messages', true );

		if ( $sms_limit_free ) {
			if ( $sms_limit_free < $sms_qty) {
				// free sms over - zero user_meta
				update_user_meta( $user_id, 'dss_qty_messages', 0 );
				update_user_meta( $user_id, 'dss_qty_paid_messages', $sms_qty );
			} else {
				$sms_qty = $sms_limit_free - $qty_recipients;
				update_user_meta( $user_id, 'dss_qty_messages', $sms_qty );
			}
			return;
		}
		else {
			update_user_meta( $user_id, 'dss_qty_paid_messages', $sms_qty );
		}
		return;
	}

	// /**
	//  * @param string $template
	//  * @param int $order_id
	//  * @param string $service
	//  * @param int $user_id
	//  *
	//  * @return void
	//  */
	// public static function send_email_to_customer( $template, $order_id, $service, $user_id ) {
 //        //send email to customer
 //        wp_mail( 
 //            get_userdata( $user_id )->user_email, 
 //            'Dropseal services', 
 //            Dropseal_Services_Admin::replace_template_placeholders( $template, $order_id, $service ) 
 //        );

 //        //send email to admin
 //        wp_mail( 
 //            get_option( 'admin_email' ), 
 //            'Dropseal services', 
 //            Dropseal_Services_Admin::replace_template_placeholders( $template, $order_id, $service ) 
 //        );		
	// }
}