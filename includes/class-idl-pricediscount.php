<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tinajam.wordpress.com/
 * @since      1.0.0
 *
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/includes
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
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/includes
 * @author     tahir iqbal <tahiriqbal09@gmail.com>
 */
class Idl_Pricediscount {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Idl_Pricediscount_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'IDL_PRICEDISCOUNT_VERSION' ) ) {
			$this->version = IDL_PRICEDISCOUNT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'idl-pricediscount';

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
	 * - Idl_Pricediscount_Loader. Orchestrates the hooks of the plugin.
	 * - Idl_Pricediscount_i18n. Defines internationalization functionality.
	 * - Idl_Pricediscount_Admin. Defines all hooks for the admin area.
	 * - Idl_Pricediscount_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-idl-pricediscount-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-idl-pricediscount-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-idl-pricediscount-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-idl-pricediscount-public.php';

		$this->loader = new Idl_Pricediscount_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Idl_Pricediscount_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Idl_Pricediscount_i18n();

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

		$plugin_admin = new Idl_Pricediscount_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Add product discount tab
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'add_discount_tab' );
		$this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'add_discount_tab_content' );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'save_discount_data' );
		
		// AJAX handlers
		$this->loader->add_action( 'wp_ajax_search_products_for_bundle', $plugin_admin, 'search_products_for_bundle' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Idl_Pricediscount_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// Product page hooks
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'display_discount_options', 25 );
		
		// Cart hooks
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'add_discount_data_to_cart', 10, 3 );
		$this->loader->add_filter( 'woocommerce_get_cart_item_from_session', $plugin_public, 'get_cart_item_discount_data_from_session', 10, 2 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'display_discount_data_in_cart', 10, 2 );
		
		// Price calculation hooks
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'update_cart_item_prices', 10, 1 );
		$this->loader->add_filter( 'woocommerce_cart_item_price', $plugin_public, 'display_cart_item_price', 10, 3 );
		
		// Checkout hooks
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'save_discount_data_to_order_items', 10, 4 );
		
		// Cart quantity control hooks
		$this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_public, 'modify_cart_item_quantity', 10, 3 );
		$this->loader->add_filter( 'woocommerce_cart_item_remove_link', $plugin_public, 'disable_bundle_item_removal', 10, 2 );
		$this->loader->add_action( 'woocommerce_cart_item_restored', $plugin_public, 'prevent_quantity_change', 10, 2 );
		
		// Bundle cart protection
		$this->loader->add_filter( 'woocommerce_cart_item_remove_link', $plugin_public, 'disable_bundle_item_removal', 10, 2 );
		$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'redirect_bundle_to_checkout', 10, 6 );
		
		// AJAX handlers
		$this->loader->add_action( 'wp_ajax_add_discount_to_cart', $plugin_public, 'add_discount_to_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_add_discount_to_cart', $plugin_public, 'add_discount_to_cart' );
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
	 * @return    Idl_Pricediscount_Loader    Orchestrates the hooks of the plugin.
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
