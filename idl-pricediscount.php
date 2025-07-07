<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tinajam.wordpress.com/
 * @since             1.0.0
 * @package           Idl_Pricediscount
 *
 * @wordpress-plugin
 * Plugin Name:       Price Discount
 * Plugin URI:        https://tinajam.wordpress.com/
 * Description:       This plugin is for discount on different satuations.
 * Version:           1.0.0
 * Author:            tahir iqbal
 * Author URI:        https://tinajam.wordpress.com//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       idl-pricediscount
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'IDL_PRICEDISCOUNT_VERSION', '1.1.871' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-idl-pricediscount-activator.php
 */
function activate_idl_pricediscount() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-idl-pricediscount-activator.php';
	Idl_Pricediscount_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-idl-pricediscount-deactivator.php
 */
function deactivate_idl_pricediscount() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-idl-pricediscount-deactivator.php';
	Idl_Pricediscount_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_idl_pricediscount' );
register_deactivation_hook( __FILE__, 'deactivate_idl_pricediscount' );

/**
 * Check if WooCommerce is active
 */
function idl_pricediscount_check_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'idl_pricediscount_woocommerce_missing_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
		return false;
	}
	return true;
}

/**
 * Display notice if WooCommerce is not active
 */
function idl_pricediscount_woocommerce_missing_notice() {
	echo '<div class="error"><p>' . __( 'Price Discount plugin requires WooCommerce to be installed and active.', 'idl-pricediscount' ) . '</p></div>';
}

// Check WooCommerce dependency before loading the plugin
add_action( 'plugins_loaded', function() {
	if ( idl_pricediscount_check_woocommerce() ) {
		run_idl_pricediscount();
	}
}, 20 );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-idl-pricediscount.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_idl_pricediscount() {

	$plugin = new Idl_Pricediscount();
	$plugin->run();

}
