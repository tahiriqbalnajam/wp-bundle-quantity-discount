<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://tinajam.wordpress.com/
 * @since      1.0.0
 *
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/includes
 * @author     tahir iqbal <tahiriqbal09@gmail.com>
 */
class Idl_Pricediscount_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'idl-pricediscount',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
