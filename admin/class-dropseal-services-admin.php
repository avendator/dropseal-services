<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://upsite.top/
 * @since      1.0.0
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/admin
 * @author     UPsite <info@upsite.top>
 */

require_once( plugin_dir_path( __FILE__ ) .'/../lib/twilio/src/Twilio/autoload.php' );

use Twilio\Rest\Client;

class Dropseal_Services_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dropseal_Services_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dropseal_Services_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dropseal-services-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dropseal_Services_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dropseal_Services_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dropseal-services-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add admin menu
	 */
	public function dss_add_admin_menu() {
		add_menu_page( 'Dropseal services', 'Dropseal services', 'manage_options', 'dropseal-services', array($this, 'dss_settings_display'), '', 30 );
		add_submenu_page( 'dropseal-services', 'Settings', 'Settings', 'manage_options', 'dropseal-services' );
		add_submenu_page( 'dropseal-services', 'SMS Orders', 'SMS Orders', 'manage_options', 'sms-services', array( $this, 'dss_sms_display' ) );
		add_submenu_page( null, 'New sms order', 'New sms order', 'manage_options', 'sms-new-order', array( $this, 'dss_sms_new_order_display' ) );
		add_submenu_page( null, 'Edit sms order', 'Edit sms order', 'manage_options', 'sms-edit-order', array( $this, 'dss_sms_edit_order_display' ) );
		add_submenu_page( 'dropseal-services', 'Special request', 'Special request', 'manage_options', 'special-request', array( $this, 'dss_special_request_display' ) );
		add_submenu_page( null, 'Edit crs order', 'Edit crs order', 'manage_options', 'crs-edit-order', array( $this, 'dss_crs_edit_order_display' ) );
		add_submenu_page( null, 'Order info', 'Order info', 'manage_options', 'order-info', array( $this, 'dss_order_info_display' ) );
		add_submenu_page( 'dropseal-services', 'Messages', 'Messages', 'manage_options', 'messages-settings', array( $this, 'dss_messages_display' ) );
		add_submenu_page( 'dropseal-services', 'Twilio', 'Twilio', 'manage_options', 'twilio-settings', array( $this, 'dss_twilio_display' ) );
		add_submenu_page( 'dropseal-services', 'Mobile Carriers', 'Mobile carriers', 'manage_options', 'mobile-carrier', array( $this, 'dss_mobile_carriers_display' ) );
	}

	/**
	 * Render settings admin menu
	 */
	public function dss_settings_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-settings-display.php';
	}

	/**
	 * Render SMS admin menu
	 */
	public function dss_sms_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-sms-display.php';
	}

	/**
	 * Render SMS New order admin menu
	 */
	public function dss_sms_new_order_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-sms-new-order-display.php';
	}

	/**
	 * Render SMS edit order admin menu
	 */
	public function dss_sms_edit_order_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-sms-edit-order-display.php';
	}

	/**
	 * Render CRS admin menu
	 */
	public function dss_special_request_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-crs-display.php';
	}

	/**
	 * Render CRS edit order admin menu
	 */
	public function dss_crs_edit_order_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-crs-edit-order-display.php';
	}

	/**
	 * Render order info display admin menu
	 */
	public function dss_order_info_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-order-info-display.php';
	}

	/**
	 * Render messages admin menu
	 */
	public function dss_messages_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-messages-display.php';
	}

	/**
	 * Render twilio admin menu
	 */
	public function dss_twilio_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-twilio-display.php';
	}

	/**
	 * Render mobile carrier  admin menu
	 */
	public function dss_mobile_carriers_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dropseal-services-admin-mobile-carriers-display.php';
	}

	/**
	* Function responsible for processing and sending user data 
	*/
	public function dss_send_message() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/dropseal-services-send-message.php';
	}

	/**
	 * Show custom profile fields
	 */
	public function dss_show_profile_fields( $user ) { 
		$mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) ); ?>

		<h3>Phone and mobile carrier</h3>
		<table class="form-table">
			<tr>
				<th><label for="dss_phone">Phone</label></th>			
				<td><input type="text" name="dss_phone" id="dss_phone" value="<?= esc_attr( get_the_author_meta( 'dss_phone', $user->ID ) ); ?>" class="regular-text"></td>
			</tr>	
			<tr>
				<th><label for="dss_mobile_carrier">Mobile carrier</label></th>			
				<td>
					<select name="dss_mobile_carrier" id="dss_mobile_carrier" class="regular-text">
						<option value="">Mobile carrier no choice</option>

						<?php foreach ( $mobile_carriers as $mobile_carrier ) : ?>

							<?php if ( $mobile_carrier->checked ) : ?>  
								<option value="<?= $mobile_carrier->sms ?>" 
											
									<?php if ( esc_attr( get_the_author_meta( 'dss_mobile_carrier', $user->ID ) ) == $mobile_carrier->sms ) echo 'selected'; ?>
								
								><?= $mobile_carrier->name ?></option>
							<?php endif; ?> 

						<?php endforeach; ?> 

					</select>
				</td>
			</tr>
		</table>

		<?php
	}

	/**
	 * Save custom profile fields
	 */
	public function dss_save_profile_fields( $user_id  ) {
		update_usermeta( $user_id, 'dss_phone', $_POST['dss_phone'] );
		update_usermeta( $user_id, 'dss_mobile_carrier', $_POST['dss_mobile_carrier'] );
	}

	/**
	 * Handlers method
	 */
	public function dss_handlers() {
		global $wpdb;
		switch ( $_POST['request'] ) {

			case 'get_mobile_carriers':
				echo get_option( 'dss_mobile_carriers' );
				break;

			case 'delete_file':	
				$service = $_POST['service'];
				$order_id = $_POST['id'];
				self::delete_file( $order_id, $service );		
				break;
			
			case 'delete_order':
				$service = $_POST['service'];
				$order_id = $_POST['id'];
				$status = self::get_order_status( $order_id, $service );

				if( $status == 'pending' || $status == 'processing' ) self::change_status( $order_id, $service, 'cancelled' );				
				$wpdb->update( 'dss_'.$service.'_data', ['hide_to_admin' => '1'], ['id' => $order_id] );
				// self::delete_file( $order_id, $service );			
				break;

			case 'add_lock':
				$type = $_POST['type'];
				$val = $_POST['val'];
				$blocked = get_option( 'dss_blocked_'.$type );
				if ( !$blocked ) $blocked = [];

				if ( in_array( $val, $blocked ) ) {
					echo 'exists';
				} else {
					$blocked[] = $val;
					update_option( 'dss_blocked_'.$type, $blocked );
					echo '<div>
						<span class="dss_btn dss_delete_button dss_delete_lock" data-type="'.$type.'" data-val="'.$val.'" title="Delete">&#10007;</span> 
						<span>'.$val.'</span>
					</div>';
				}
				
				break;

			case 'delete_lock':
				$type = $_POST['type'];
				$val = $_POST['val'];
				$blocked = get_option( 'dss_blocked_'.$type );
				$key = array_search( $val, $blocked );
				unset( $blocked[$key] );		
				update_option( 'dss_blocked_'.$type, array_values( $blocked ) );
				break;

			case 'check_locks':				
				echo json_encode( self::check_locks( $_POST['numbers'], $_POST['emails'] ) );
		
				break;
		}	
		wp_die();		
	}

	/**
	 * File upload
	 */
	public static function file_upload( $files ) {

		if( $_FILES['file']['error'] ) return;
		$file_size = get_option( 'dss_upload_file_size' ) * 1024 * 1024;
	
		if ( $files['file']['size'] > $file_size )  wp_die( 'File was not uploaded!' );
		$directory = $_SERVER['DOCUMENT_ROOT'] . 'wp-content/plugins/dropseal-services/files/attachements/'.time();
	
		if ( !mkdir( $directory ) )  wp_die( 'File was not uploaded!' );
		$file_path = $directory.'/'.$files['file']['name'];
		
		if ( move_uploaded_file( $files['file']['tmp_name'], $file_path ) ) return $file_path;
	}

	/**
	 * Delete file
	 */
	public static function delete_file( $order_id, $service ) {
		global $wpdb;
		$file_path = $wpdb->get_var( "SELECT meta_value FROM dss_{$service}_meta WHERE {$service}_id = $order_id AND meta_key = 'file_path'" );

		if( $file_path ) self::delete_directory( dirname( $file_path ) );
		$wpdb->update( 'dss_'.$service.'_meta', ['meta_value' => ''], [$service.'_id' => $order_id, 'meta_key' => 'file_path'] );
	}

	/**
	 * Delete directory
	 */
	private function delete_directory( $dir ) {
		$files = array_diff( scandir( $dir ), ['.','..'] );

		foreach ( $files as $file ) {
			( is_dir( $dir.'/'.$file ) ) ? delete_directory( $dir.'/'.$file ) : unlink ($dir.'/'.$file );
		}
		return rmdir( $dir );
	}

	/**
	 * Get statuses
	 */
	public static function get_statuses() {
		$statuses = ['pending', 'processing', 'completed', 'cancelled'];
		return $statuses;
	}

	/**
	 * Get order status
	 */
	private function get_order_status( $order_id, $service ) {
		global $wpdb;
		$status = $wpdb->get_var( "SELECT status FROM dss_{$service}_data WHERE id = $order_id" );
		return $status;
	}

	/**
	 * Change status
	 */
	private function change_status( $order_id, $service, $status ) {
		global $wpdb;
		$wpdb->update( 'dss_'.$service.'_data', ['status' => $status], ['id' => $order_id] );
	}

	/**
	 * Check locks
	 */
	private function check_locks( $numbers, $emails ) {
		$blocked_numbers = get_option( 'dss_blocked_numbers' );
		$blocked_emails  = get_option( 'dss_blocked_emails' );
		$blocked_domains = get_option( 'dss_blocked_domains' );

		foreach ( $numbers as $number ) {

			if ( in_array( $number['val'], $blocked_numbers ) ) {
				$data['data'][] = [
					'name'  => $number['name'],
					'val'   => $number['val'],
				];
				$data['Error'] = true;
			}
		}

		foreach ( $emails as $email ) {

			if ( in_array( $emails['val'], $blocked_emails ) ) {
				$data['data'][] = [
					'name'  => $email['name'],
					'val'   => $email['val'],
				];
				$data['Error'] = true;
			}
		}

		foreach ( $emails as $email ) {

			if ( in_array( stristr( $email['val'], '@'), $blocked_domains ) ) {
				$data['data'][] = [
					'name'  => $email['name'],
					'val'   => $email['val'],
				];
				$data['Error'] = true;
			}
		}
	
		if ( $data['Error'] ) $data['Error'] = '<div class="notice notice-error is-dismissible"><p>Data is in the list of blocked!</p></div>';
		
		return $data;
	}

	/**
	 * Get template placeholders
	 */
	public static function get_template_placeholders() {
		$template_placeholders = ['[userName]', '[deliveryTime]', '[orderID]', '[orderPrice]', '[moneyAmount]', '[paymentEmail]', '[linkID]'];
		return $template_placeholders;
	}

	/**
	 * Get data by order
	 */
	public static function get_data( $order_id, $service ) {
		global $wpdb;
		$data = $wpdb->get_row( "SELECT * FROM dss_{$service}_data WHERE id = $order_id" );
		return $data;
	}

	/**
	 * Get meta by order
	 */
	public static function get_meta( $order_id, $service ) {
		global $wpdb;
		$meta_db = $wpdb->get_results( "SELECT meta_key, meta_value FROM dss_{$service}_meta WHERE {$service}_id = $order_id" ); 

		foreach ( $meta_db as $key ) {
			$meta[$key->meta_key] = is_array( json_decode( $key->meta_value ) ) || is_object( json_decode( $key->meta_value ) ) ? json_decode( $key->meta_value ) : $key->meta_value;  
		}
		return $meta;
	}

	/**
	 * Delete selected orders
	 */
	public static function delete_selected_orders( $service ) {
		global $wpdb;

		if( isset( $_POST['dss_checked_orders'] ) ) { 
			
			foreach( $_POST['dss_checked_orders'] as $order_id ) {
				if( $status == 'pending' || $status == 'processing' ) self::change_status( $order_id, $service, 'cancelled' );				
				$wpdb->update( 'dss_'.$service.'_data', ['hide_to_admin' => '1'], ['id' => $order_id] );	
			}
		}
	}

	/**
	 * Send message
	 */
	public static function send_message( $message, $order_id, $service ) {
		global $wpdb;

		$order = self::get_data( $order_id, $service );
		
		if( $order->reminder ) {
			$phone = get_user_meta( $order->customer_id, 'dss_phone', true );
			$mobile_carrier = get_user_meta( $order->customer_id, 'dss_mobile_carrier', true );

			if ( $mobile_carrier ) {
				//send sms to customer
				wp_mail( 
					$phone . $mobile_carrier, 
					'Dropseal services', 
					self::replace_template_placeholders( $message, $order->id, $service ) 
				);

			} else {
				//sending Twilio, if not a mobile carrier
				$send_message = self::send_message_Twilio( $phone, $message, $order->id );

				//if Twilio error
				if( $send_message['Error'] )  {

					//send email to customer
					wp_mail( 
						get_userdata( $order->customer_id )->user_email, 
						'Dropseal services', 
						self::replace_template_placeholders( $message, $order->id, $service ) 
					);
				}
			}
			//send email to admin
			if( $send_message['Error'] )  {
                wp_mail( 
                    get_option( 'admin_email' ), 
                    'Dropseal services', 
                    Dropseal_Services_Admin::replace_template_placeholders( get_option( 'dss_template_cancelled' ), $order->id, 'sms' ) 
                );				
			} else {
				wp_mail( 
					get_option( 'admin_email' ), 
					'Dropseal services', 
					self::replace_template_placeholders( $message, $order->id, $service ) 
				);				
			}
		}
	}

	/**
	 * Twilio send message
	 */
	public static function send_message_Twilio( $to, $message, $order_id = 0, $service = 'sms' ) {
		global $wpdb;
	 
		$TWILIO_SID   = get_option( 'dss_twilio_api_sid' );
		$TWILIO_TOKEN = get_option( 'dss_twilio_api_token' );
		$sender_phone = get_option( 'dss_twilio_api_phone' );
		
		if( $order_id ) $message = self::replace_template_placeholders( $message, $order_id, $service );

		try {
			$client = new Client( $TWILIO_SID, $TWILIO_TOKEN );
			$response = $client->messages->create( '+1'.$to, ['from' => $sender_phone, 'body' => $message ] );
		} catch ( Exception $e ) {
			$errors['Error'] = $e->getMessage();
			return $errors;
		}
		
		return;
	}

	/**
	 * Replace template placeholders
	 */
	public static function replace_template_placeholders( $message, $order_id, $service ) {
		global $wpdb;

		$data = self::get_data( $order_id, $service );
		$meta = self::get_meta( $order_id, $service );
		$template_placeholders = self::get_template_placeholders();

		$replace = [
			get_userdata( $data->customer_id )->display_name, 
			date('F j, Y, g:i A', $data->process_date ), 
			$order_id, 
			$meta['order_price'],
			$meta['money_amount'],
			$meta['payment_email'],
			get_permalink( get_page_by_path ( 'content' ) ) . '?id=' . $data->link_id,
		];
		return str_replace( $template_placeholders, $replace, $message );		
	}

}