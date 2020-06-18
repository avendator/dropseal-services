<?php

/**
 * Fired during plugin activation
 *
 * @link       https://upsite.top/
 * @since      1.0.0
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/includes
 * @author     UPsite <info@upsite.top>
 */
class Dropseal_Services_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::dss_create_tables();
	}


	/**
	 * Create tables
	 */
	public static function dss_create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name = 'dss_sms_data';
		if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
			
			// Create dss_sms_data
			$sql = "CREATE TABLE $table_name (
				id INT NOT NULL AUTO_INCREMENT,
				order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
				customer_id INT,
				process_date INT,
				status VARCHAR(20),
				reminder INT,
				link VARCHAR(50),
				hide_to_admin INT,
				sms_limit INT,
				UNIQUE KEY id (id)
			);";
			dbDelta( $sql );		
		}

		$table_name = 'dss_sms_meta';
		if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
			// Create dss_sms_meta
			$sql = "CREATE TABLE $table_name (
				meta_id INT NOT NULL AUTO_INCREMENT,
				sms_id INT,
				meta_key VARCHAR(50),
				meta_value TEXT,
				UNIQUE KEY meta_id (meta_id)
			);";
			dbDelta( $sql );
		}
		
		$table_name = 'dss_crs_data';
		if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
			
			// Create dss_request_data
			$sql = "CREATE TABLE $table_name (
				id INT NOT NULL AUTO_INCREMENT,
				order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
				customer_id INT,
				process_date INT,
				status VARCHAR(20),
				reminder INT,
				link VARCHAR(50),
				hide_to_admin INT,
				UNIQUE KEY id (id)
			);";
			dbDelta( $sql );		
		}

		$table_name = 'dss_crs_meta';
		if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
			// Create dss_request_meta
			$sql = "CREATE TABLE $table_name (
				meta_id INT NOT NULL AUTO_INCREMENT,
				crs_id INT,
				meta_key VARCHAR(50),
				meta_value TEXT,
				UNIQUE KEY meta_id (meta_id)
			);";
			dbDelta( $sql );
		}	

	}
	
}

