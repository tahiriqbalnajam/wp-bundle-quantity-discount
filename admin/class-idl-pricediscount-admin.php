<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tinajam.wordpress.com/
 * @since      1.0.0
 *
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/admin
 * @author     tahir iqbal <tahiriqbal09@gmail.com>
 */
class Idl_Pricediscount_Admin {

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
		 * defined in Idl_Pricediscount_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Idl_Pricediscount_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/idl-pricediscount-admin.css', array(), $this->version, 'all' );

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
		 * defined in Idl_Pricediscount_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Idl_Pricediscount_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/idl-pricediscount-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'idl_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'idl_discount_nonce' )
		));

	}

	public function add_discount_tab( $tabs ) {
		$tabs['idl_discount'] = array(
			'label'  => __( 'Discount', 'idl-pricediscount' ),
			'target' => 'idl_discount_data',
			'class'  => array( 'show_if_simple', 'show_if_variable' ),
		);
		return $tabs;
	}

	public function add_discount_tab_content() {
		global $post;
		?>
		<div id="idl_discount_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field">
					<label><input type="radio" name="idl_discount_type" value="quantity" <?php checked( get_post_meta( $post->ID, '_idl_discount_type', true ), 'quantity' ); ?>> <?php _e( 'Quantity Discount', 'idl-pricediscount' ); ?></label>
				</p>
				<p class="form-field">
					<label><input type="radio" name="idl_discount_type" value="bundle" <?php checked( get_post_meta( $post->ID, '_idl_discount_type', true ), 'bundle' ); ?>> <?php _e( 'Bundle Discount', 'idl-pricediscount' ); ?></label>
				</p>
			</div>

			<!-- Quantity Discount Section -->
			<div id="quantity_discount_section" class="discount_section" style="display: none;">
				<h4><?php _e( 'Quantity Discount Rules', 'idl-pricediscount' ); ?></h4>
				<div id="quantity_rules">
					<?php
					$quantity_rules = get_post_meta( $post->ID, '_idl_quantity_rules', true );
					if ( $quantity_rules ) {
						foreach ( $quantity_rules as $index => $rule ) {
							$this->render_quantity_rule_row( $index, $rule );
						}
					} else {
						$this->render_quantity_rule_row( 0 );
					}
					?>
				</div>
				<button type="button" id="add_quantity_rule" class="button"><?php _e( 'Add Rule', 'idl-pricediscount' ); ?></button>
			</div>

			<!-- Bundle Discount Section -->
			<div id="bundle_discount_section" class="discount_section" style="display: none;">
				<h4><?php _e( 'Bundle Discount Rules', 'idl-pricediscount' ); ?></h4>
				<div id="bundle_rules">
					<?php
					$bundle_rules = get_post_meta( $post->ID, '_idl_bundle_rules', true );
					if ( $bundle_rules ) {
						foreach ( $bundle_rules as $index => $rule ) {
							$this->render_bundle_rule_row( $index, $rule );
						}
					} else {
						$this->render_bundle_rule_row( 0 );
					}
					?>
				</div>
				<button type="button" id="add_bundle_rule" class="button"><?php _e( 'Add Bundle Rule', 'idl-pricediscount' ); ?></button>
			</div>
		</div>
		<?php
	}

	private function render_quantity_rule_row( $index, $rule = array() ) {
		?>
		<div class="quantity_rule_row" data-index="<?php echo $index; ?>">
			<p class="form-field">
				<label><?php _e( 'Quantity', 'idl-pricediscount' ); ?></label>
				<input type="number" name="quantity_rules[<?php echo $index; ?>][quantity]" value="<?php echo isset( $rule['quantity'] ) ? $rule['quantity'] : ''; ?>" min="1" step="1" />
			</p>
			<p class="form-field">
				<label><?php _e( 'Price', 'idl-pricediscount' ); ?></label>
				<input type="number" name="quantity_rules[<?php echo $index; ?>][price]" value="<?php echo isset( $rule['price'] ) ? $rule['price'] : ''; ?>" step="0.01" min="0" />
			</p>
			<p class="form-field">
				<label><?php _e( 'Label', 'idl-pricediscount' ); ?></label>
				<input type="text" name="quantity_rules[<?php echo $index; ?>][label]" value="<?php echo isset( $rule['label'] ) ? esc_attr( $rule['label'] ) : ''; ?>" />
			</p>
			<p class="form-field">
				<label><?php _e( 'Badge', 'idl-pricediscount' ); ?></label>
				<input type="text" name="quantity_rules[<?php echo $index; ?>][badge]" value="<?php echo isset( $rule['badge'] ) ? esc_attr( $rule['badge'] ) : ''; ?>" />
			</p>
			<p class="form-field">
				<label><?php _e( 'Description', 'idl-pricediscount' ); ?></label>
				<textarea name="quantity_rules[<?php echo $index; ?>][description]"><?php echo isset( $rule['description'] ) ? esc_textarea( $rule['description'] ) : ''; ?></textarea>
			</p>
			<button type="button" class="button remove_rule"><?php _e( 'X', 'idl-pricediscount' ); ?></button>
		</div>
		<?php
	}

	private function render_bundle_rule_row( $index, $rule = array() ) {
		?>
		<div class="bundle_rule_row" data-index="<?php echo $index; ?>">
			<p class="form-field">
				<label><?php _e( 'Bundle Products', 'idl-pricediscount' ); ?></label>
				<input type="text" class="product_search" name="bundle_rules[<?php echo $index; ?>][products_search]" value="" placeholder="<?php _e( 'Search and select products...', 'idl-pricediscount' ); ?>" />
				<input type="hidden" name="bundle_rules[<?php echo $index; ?>][products]" value="<?php echo isset( $rule['products'] ) ? esc_attr( $rule['products'] ) : ''; ?>" class="selected_products" />
				<div class="selected-products-display">
					<?php
					if ( ! empty( $rule['products'] ) ) {
						$this->display_selected_products( $rule['products'] );
					}
					?>
				</div>
			</p>
			<p class="form-field">
				<label><?php _e( 'Bundle Price', 'idl-pricediscount' ); ?></label>
				<input type="number" name="bundle_rules[<?php echo $index; ?>][price]" value="<?php echo isset( $rule['price'] ) ? $rule['price'] : ''; ?>" step="0.01" min="0" />
			</p>
			<p class="form-field">
				<label><?php _e( 'Badge', 'idl-pricediscount' ); ?></label>
				<input type="text" name="bundle_rules[<?php echo $index; ?>][badge]" value="<?php echo isset( $rule['badge'] ) ? esc_attr( $rule['badge'] ) : ''; ?>" />
			</p>
			<p class="form-field">
				<label><?php _e( 'Title', 'idl-pricediscount' ); ?></label>
				<textarea name="bundle_rules[<?php echo $index; ?>][description]"><?php echo isset( $rule['description'] ) ? esc_textarea( $rule['description'] ) : ''; ?></textarea>
			</p>
			<button type="button" class="button remove_rule"><?php _e( 'X', 'idl-pricediscount' ); ?></button>
		</div>
		<?php
	}

	private function display_selected_products( $products_string ) {
		if ( empty( $products_string ) ) {
			return;
		}

		$product_ids = $this->parse_bundle_products( $products_string );
		
		if ( empty( $product_ids ) ) {
			// Handle old format where product names were stored
			echo '<div class="selected-product-item">' . esc_html( $products_string ) . '</div>';
			return;
		}

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				echo '<div class="selected-product-item" data-product-id="' . $product_id . '">';
				echo '<span class="product-name">' . esc_html( $product->get_name() ) . '</span>';
				echo '<span class="product-price">(' . wc_price( $product->get_price() ) . ')</span>';
				echo '<button type="button" class="remove-product" data-product-id="' . $product_id . '">×</button>';
				echo '</div>';
			}
		}
	}

	private function parse_bundle_products( $products_string ) {
		// Try to extract product IDs from the string
		preg_match_all('/\(ID: (\d+)\)/', $products_string, $matches);
		if ( ! empty( $matches[1] ) ) {
			return array_map( 'intval', $matches[1] );
		}

		// If no IDs found, try comma-separated product IDs
		$ids = explode( ',', $products_string );
		$product_ids = array();
		foreach ( $ids as $id ) {
			$id = intval( trim( $id ) );
			if ( $id > 0 ) {
				$product_ids[] = $id;
			}
		}

		return $product_ids;
	}

	public function save_discount_data( $post_id ) {
		if ( isset( $_POST['idl_discount_type'] ) ) {
			update_post_meta( $post_id, '_idl_discount_type', sanitize_text_field( $_POST['idl_discount_type'] ) );
		}

		if ( isset( $_POST['quantity_rules'] ) ) {
			$quantity_rules = array();
			foreach ( $_POST['quantity_rules'] as $rule ) {
				$quantity_rules[] = array(
					'quantity' => intval( $rule['quantity'] ),
					'price' => floatval( $rule['price'] ),
					'label' => sanitize_text_field( $rule['label'] ),
					'badge' => sanitize_text_field( $rule['badge'] ),
					'description' => sanitize_textarea_field( $rule['description'] )
				);
			}
			update_post_meta( $post_id, '_idl_quantity_rules', $quantity_rules );
		}

		if ( isset( $_POST['bundle_rules'] ) ) {
			$bundle_rules = array();
			foreach ( $_POST['bundle_rules'] as $rule ) {
				$bundle_rules[] = array(
					'products' => sanitize_text_field( $rule['products'] ),
					'price' => floatval( $rule['price'] ),
					'badge' => sanitize_text_field( $rule['badge'] ),
					'description' => sanitize_textarea_field( $rule['description'] )
				);
			}
			update_post_meta( $post_id, '_idl_bundle_rules', $bundle_rules );
		}
	}

	public function search_products_for_bundle() {
		check_ajax_referer( 'idl_discount_nonce', 'nonce' );
		
		$search_term = sanitize_text_field( $_POST['term'] );
		
		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			's' => $search_term,
			'posts_per_page' => 10,
			'meta_query' => array(
				array(
					'key' => '_stock_status',
					'value' => 'instock',
					'compare' => '='
				)
			)
		);
		
		$products = get_posts( $args );

		$results = array();
		foreach ( $products as $product ) {
			$product_obj = wc_get_product( $product->ID );
			if ( $product_obj ) {
				$results[] = array(
					'id' => $product->ID,
					'text' => $product->post_title . ' - ' . wc_price( $product_obj->get_price() )
				);
			}
		}

		wp_send_json( $results );
	}
}
