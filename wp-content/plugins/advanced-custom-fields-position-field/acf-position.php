<?php

/**
 * Plugin Name: Advanced Custom Fields: Position Field
 * Plugin URI: https://wordpress.org/plugins/advanced-custom-fields-position-field/
 * Description: Страна/Регион/Город
 * Version: 1.0.8.2
 * Author: Mikhail Kobzarev
 * Author URI: https://www.kobzarev.com/
 * License: GPL
 */
class acf_field_position_plugin {

	public function __construct() {
		$domain = "acf-position";
		$mofile = trailingslashit( dirname( __FILE__ ) ) . 'lang/' . $domain . '-' . get_locale() . '.mo';
		load_textdomain( $domain, $mofile );

		add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) );

		//register_activation_hook( __FILE__, array($this, 'populate_db') );
		//register_deactivation_hook( __FILE__, array($this, 'depopulate_db') );
	}

	public function include_field_types() {
		include_once 'register-fields.php';
	}

//	public function populate_db() {
//		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//
//		ob_start();
//		//require_once "lib/install-data.php";
//		//$sql = ob_get_clean();
//		//dbDelta( $sql );
//	}
//
//	public function depopulate_db() {
//		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//
//		ob_start();
//		//require_once "lib/drop-tables.php";
//		//$sql = ob_get_clean();
//		//dbDelta( $sql );
//	}

}

new acf_field_position_plugin();
