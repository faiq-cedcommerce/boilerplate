<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cedcoss.com/
 * @since      1.0.0
 *
 * @package    Test_Wc
 * @subpackage Test_Wc/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Test_Wc
 * @subpackage Test_Wc/includes
 * @author     Faiq Masood <faiqmasood@cedcoss.com>
 */
class Test_Wc_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$error_message = 'Please firstly activate the WooCommerce Plugin';
			die( $error_message ); 
		  }
	}

}
