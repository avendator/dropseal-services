<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://upsite.top/
 * @since      1.0.0
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/includes
 * @author     UPsite <info@upsite.top>
 */
class Dropseal_Services {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Dropseal_Services_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'DROPSEAL_SERVICES_VERSION' ) ) {
			$this->version = DROPSEAL_SERVICES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'dropseal-services';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Dropseal_Services_Loader. Orchestrates the hooks of the plugin.
	 * - Dropseal_Services_i18n. Defines internationalization functionality.
	 * - Dropseal_Services_Admin. Defines all hooks for the admin area.
	 * - Dropseal_Services_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dropseal-services-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dropseal-services-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dropseal-services-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dropseal-services-public.php';

		/**
		 * The class responsible for processing and validating data from new and existing users in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dropseal-services-user.php';

		$this->loader = new Dropseal_Services_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dropseal_Services_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Dropseal_Services_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Dropseal_Services_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//Add setting menu item 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'dss_add_admin_menu' );

		//AJAX handlers 
		$this->loader->add_action( 'wp_ajax_dss_handlers', $plugin_admin, 'dss_handlers' );

		//Custom profile fields
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'dss_show_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'dss_show_profile_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'dss_save_profile_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'dss_save_profile_fields' );

		//Send message
		add_shortcode( 'dss-send-message',  array( $plugin_admin, 'dss_send_message' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Dropseal_Services_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// AJAX handlers
		$this->loader->add_action( 'wp_ajax_'.'save_sms_service_data', $plugin_public, 'save_sms_service_data' );
		$this->loader->add_action( 'wp_ajax_'.'save_crs_service_data', $plugin_public, 'save_crs_service_data' );
		$this->loader->add_action( 'wp_ajax_'.'dss_update_sms_data', $plugin_public, 'update_sms_data' );
		$this->loader->add_action( 'wp_ajax_'.'dss_update_crs_data', $plugin_public, 'update_crs_data' );
		// set of AJAX handlers via switch case
		$this->loader->add_action( 'wp_ajax_'.'dss_public_handlers', $plugin_public, 'dss_public_handlers' );
		// saving data during payment through paypal
		$this->loader->add_action( 'wp_paypal_ipn_processed', $plugin_public, 'dss_paypal_order_processed' );
		$this->loader->add_action( 'wpstripecheckout_payment_completed', $plugin_public, 'dss_apple_pay_order_processed' );
		//Change cookie logout
		$this->loader->add_filter( 'auth_cookie_expiration', $plugin_public, 'dss_change_cookie_logout',  10, 3 );

		//Change paypal amount
		// $this->loader->add_filter( 'wppaypal_buynow_custom_amount', $plugin_public, 'dss_change_paypal_amount', 10, 3 );

		add_shortcode( 'dss-sms-form',  array( $plugin_public, 'dss_sms_service_form' ) );
		add_shortcode( 'dss-crs-form',  array( $plugin_public, 'dss_crs_form' ) );
		add_shortcode( 'dss-edit-profile', array($plugin_public, 'dss_edit_profile') );
		add_shortcode( 'dss-my-payment', array($plugin_public, 'dss_my_payment') );
		add_shortcode( 'dss-edit-sms',  array( $plugin_public, 'dss_edit_sms_data' ) );
		add_shortcode( 'dss-edit-crs',  array( $plugin_public, 'dss_edit_crs_data' ) );
		add_shortcode( 'dss-registration-form',  array( $plugin_public, 'dss_registration_form' ) );
		add_shortcode( 'dss-login-form',  array( $plugin_public, 'dss_login_form' ) );
		add_shortcode( 'dss-pass-reset',  array( $plugin_public, 'dss_render_pass_reset_form' ) );
		add_shortcode( 'dss-crs-orders', array( $plugin_public, 'dss_crs_orders_display' ) );
		add_shortcode( 'dss-sms-orders', array( $plugin_public, 'dss_sms_orders_display' ) );
		add_shortcode( 'dss-recipient-content', array( $plugin_public, 'dss_recipient_content' ) );
		add_shortcode( 'dss-after-payment', array( $plugin_public, 'dss_after_payment_handler') );

		$user = new Dropseal_Services_User();

		$this->loader->add_action( 'plugins_loaded', $user, 'dss_change_user_password' );
		// Reset password handlers
		$this->loader->add_action( 'login_form_lostpassword', $user, 'dss_pass_reset_redir' );
		$this->loader->add_action( 'login_form_rp', $user, 'dss_to_custom_password_reset' );
		$this->loader->add_action( 'login_form_resetpass', $user, 'dss_to_custom_password_reset' );
		$this->loader->add_action( 'admin_init', $user, 'dss_only_admin', 1 );
		// User authorization
		$this->loader->add_action( 'wp_loaded', $user, 'dss_user_login' );
		$this->loader->add_action( 'wp_loaded', $user, 'dss_register_user' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Dropseal_Services_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
