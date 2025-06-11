<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://tinajam.wordpress.com/
 * @since      1.0.0
 *
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Idl_Pricediscount
 * @subpackage Idl_Pricediscount/public
 * @author     tahir iqbal <tahiriqbal09@gmail.com>
 */
class Idl_Pricediscount_Public {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/idl-pricediscount-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/idl-pricediscount-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'idl_public_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'idl_public_nonce' )
		));
	}

	public function display_discount_options() {
		global $product;
		
		if ( ! $product || ! is_product() ) {
			return;
		}

		$discount_type = get_post_meta( $product->get_id(), '_idl_discount_type', true );
		
		if ( ! $discount_type ) {
			return;
		}

		// Hide only the default quantity selector when discounts are available
		echo '<style>.quantity { display: none !important; }</style>';
		
		echo '<div id="idl-discount-options" data-product-id="' . $product->get_id() . '">';
		
		if ( $discount_type === 'quantity' ) {
			$this->display_quantity_discount_options( $product );
		} elseif ( $discount_type === 'bundle' ) {
			$this->display_bundle_discount_options( $product );
		}
		
		echo '</div>';
	}

	private function display_quantity_discount_options( $product ) {
		$quantity_rules = get_post_meta( $product->get_id(), '_idl_quantity_rules', true );
		
		if ( ! $quantity_rules ) {
			return;
		}

		echo '<div class="idl-quantity-discounts">';
		echo '<h4>' . __( 'Choose Your Quantity & Price', 'idl-pricediscount' ) . '</h4>';
		
		foreach ( $quantity_rules as $index => $rule ) {
			if ( empty( $rule['quantity'] ) || empty( $rule['price'] ) ) {
				continue;
			}
			
			$savings = $product->get_price() - $rule['price'];
			$savings_percent = ( $savings / $product->get_price() ) * 100;
			
			echo '<div class="discount-option" data-type="quantity" data-index="' . $index . '" data-product-id="' . $product->get_id() . '">';
			echo '<label>';
			echo '<input type="radio" name="idl_discount_option" value="quantity_' . $index . '" data-quantity="' . $rule['quantity'] . '" data-price="' . $rule['price'] . '">';
			echo '<div class="discount-info">';
			echo '<span class="quantity-price-info">';
			echo '<strong>' . sprintf( __( '%d items for %s', 'idl-pricediscount' ), $rule['quantity'], wc_price( $rule['price'] * $rule['quantity'] ) ) . '</strong>';
			echo ' <small>(' . wc_price( $rule['price'] ) . ' ' . __( 'each', 'idl-pricediscount' ) . ')</small>';
			echo '</span>';
			
			if ( ! empty( $rule['badge'] ) ) {
				echo '<span class="discount-badge">' . esc_html( $rule['badge'] ) . '</span>';
			}
			
			if ( $savings > 0 ) {
				echo '<span class="savings">' . sprintf( __( 'Save %s (%.1f%%)', 'idl-pricediscount' ), wc_price( $savings * $rule['quantity'] ), $savings_percent ) . '</span>';
			}
			
			if ( ! empty( $rule['description'] ) ) {
				echo '<p class="discount-description">' . esc_html( $rule['description'] ) . '</p>';
			}
			
			echo '</div>';
			echo '</label>';
			echo '<button type="button" class="button alt idl-add-to-cart-btn" data-type="quantity" data-index="' . $index . '" data-quantity="' . $rule['quantity'] . '" data-product-id="' . $product->get_id() . '" style="display:none;">';
			echo __( 'Add to Cart', 'idl-pricediscount' );
			echo '</button>';
			echo '</div>';
		}
		
		echo '</div>';
	}

	private function display_bundle_discount_options( $product ) {
		$bundle_rules = get_post_meta( $product->get_id(), '_idl_bundle_rules', true );
		
		if ( ! $bundle_rules ) {
			return;
		}

		echo '<div class="idl-bundle-discounts">';
		echo '<h4>' . __( 'Bundle Deals Available', 'idl-pricediscount' ) . '</h4>';
		
		foreach ( $bundle_rules as $index => $rule ) {
			if ( empty( $rule['products'] ) || empty( $rule['price'] ) ) {
				continue;
			}
			
			$bundle_products = $this->parse_bundle_products( $rule['products'] );
			$total_regular_price = $product->get_price();
			
			foreach ( $bundle_products as $bundle_product_id ) {
				$bundle_product = wc_get_product( $bundle_product_id );
				if ( $bundle_product ) {
					$total_regular_price += $bundle_product->get_price();
				}
			}
			
			$savings = $total_regular_price - $rule['price'];
			
			echo '<div class="discount-option bundle-option" data-type="bundle" data-index="' . $index . '" data-product-id="' . $product->get_id() . '">';
			echo '<label>';
			echo '<input type="radio" name="idl_discount_option" value="bundle_' . $index . '" data-price="' . $rule['price'] . '" data-products="' . esc_attr( $rule['products'] ) . '">';
			echo '<div class="bundle-info">';
			echo '<span class="bundle-price">' . wc_price( $rule['price'] ) . '</span>';
			
			if ( ! empty( $rule['badge'] ) ) {
				echo '<span class="discount-badge">' . esc_html( $rule['badge'] ) . '</span>';
			}
			
			echo '<div class="bundle-products">';
			echo '<strong>' . __( 'This bundle includes:', 'idl-pricediscount' ) . '</strong><br>';
			echo '<div class="bundle-product-list">';
			echo '• ' . $product->get_name() . ' - ' . wc_price( $product->get_price() ) . '<br>';
			
			foreach ( $bundle_products as $bundle_product_id ) {
				$bundle_product = wc_get_product( $bundle_product_id );
				if ( $bundle_product ) {
					echo '• ' . $bundle_product->get_name() . ' - ' . wc_price( $bundle_product->get_price() ) . '<br>';
				}
			}
			echo '</div>';
			echo '<div class="bundle-total">';
			echo '<strong>' . __( 'Regular Total: ', 'idl-pricediscount' ) . wc_price( $total_regular_price ) . '</strong><br>';
			echo '<strong class="bundle-deal-price">' . __( 'Bundle Price: ', 'idl-pricediscount' ) . wc_price( $rule['price'] ) . '</strong>';
			echo '</div>';
			echo '</div>';
			
			if ( $savings > 0 ) {
				echo '<span class="savings">' . sprintf( __( 'You Save %s!', 'idl-pricediscount' ), wc_price( $savings ) ) . '</span>';
			}
			
			if ( ! empty( $rule['description'] ) ) {
				echo '<p class="discount-description">' . esc_html( $rule['description'] ) . '</p>';
			}
			
			echo '</div>';
			echo '</label>';
			echo '<button type="button" class="button alt idl-add-to-cart-btn" data-type="bundle" data-index="' . $index . '" data-product-id="' . $product->get_id() . '" style="display:none;">';
			echo __( 'Add Bundle to Cart', 'idl-pricediscount' );
			echo '</button>';
			echo '</div>';
		}
		
		echo '</div>';
	}

	private function parse_bundle_products( $products_string ) {
		preg_match_all('/\(ID: (\d+)\)/', $products_string, $matches);
		return array_map( 'intval', $matches[1] );
	}

	public function add_discount_to_cart() {
		check_ajax_referer( 'idl_public_nonce', 'nonce' );
		
		$product_id = intval( $_POST['product_id'] );
		$discount_type = sanitize_text_field( $_POST['discount_type'] );
		$discount_index = intval( $_POST['discount_index'] );
		$selected_quantity = intval( $_POST['quantity'] );
		
		if ( $discount_type === 'quantity' ) {
			$this->add_quantity_discount_to_cart( $product_id, $discount_index, $selected_quantity );
		} elseif ( $discount_type === 'bundle' ) {
			$this->add_bundle_discount_to_cart( $product_id, $discount_index );
		}
	}

	private function add_quantity_discount_to_cart( $product_id, $discount_index, $quantity ) {
		$quantity_rules = get_post_meta( $product_id, '_idl_quantity_rules', true );
		
		if ( ! isset( $quantity_rules[ $discount_index ] ) ) {
			wp_send_json_error( __( 'Invalid discount rule', 'idl-pricediscount' ) );
		}
		
		$rule = $quantity_rules[ $discount_index ];
		
		$cart_item_data = array(
			'idl_discount_type' => 'quantity',
			'idl_discount_index' => $discount_index,
			'idl_discount_price' => $rule['price'],
			'idl_discount_original_price' => wc_get_product( $product_id )->get_price(),
			'idl_discount_label' => $rule['label'],
			'idl_discount_badge' => $rule['badge'],
			'idl_discount_quantity' => $rule['quantity']
		);
		
		WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );
		
		wp_send_json_success( array( 'redirect' => wc_get_cart_url() ) );
	}

	private function add_bundle_discount_to_cart( $product_id, $discount_index ) {
		$bundle_rules = get_post_meta( $product_id, '_idl_bundle_rules', true );
		
		if ( ! isset( $bundle_rules[ $discount_index ] ) ) {
			wp_send_json_error( __( 'Invalid bundle rule', 'idl-pricediscount' ) );
		}
		
		$rule = $bundle_rules[ $discount_index ];
		$bundle_products = $this->parse_bundle_products( $rule['products'] );
		
		// Clear cart for bundle deals
		WC()->cart->empty_cart();
		
		$bundle_id = 'bundle_' . time() . '_' . $product_id;
		
		// Add main product
		$main_cart_item_data = array(
			'idl_discount_type' => 'bundle',
			'idl_discount_index' => $discount_index,
			'idl_discount_price' => $rule['price'],
			'idl_discount_badge' => $rule['badge'],
			'idl_bundle_id' => $bundle_id,
			'idl_bundle_main' => true
		);
		
		WC()->cart->add_to_cart( $product_id, 1, 0, array(), $main_cart_item_data );
		
		// Add bundle products
		foreach ( $bundle_products as $bundle_product_id ) {
			$bundle_cart_item_data = array(
				'idl_discount_type' => 'bundle',
				'idl_bundle_id' => $bundle_id,
				'idl_bundle_main' => false,
				'idl_discount_price' => 0 // Bundle products are free
			);
			
			WC()->cart->add_to_cart( $bundle_product_id, 1, 0, array(), $bundle_cart_item_data );
		}
		
		wp_send_json_success( array( 'redirect' => wc_get_checkout_url() ) );
	}

	public function add_discount_data_to_cart( $cart_item_data, $product_id, $variation_id ) {
		// This hook is used by our AJAX handler, so we just return the data as-is
		return $cart_item_data;
	}

	public function get_cart_item_discount_data_from_session( $cart_item, $values ) {
		if ( isset( $values['idl_discount_type'] ) ) {
			$cart_item['idl_discount_type'] = $values['idl_discount_type'];
			$cart_item['idl_discount_index'] = $values['idl_discount_index'];
			$cart_item['idl_discount_price'] = $values['idl_discount_price'];
			$cart_item['idl_discount_original_price'] = $values['idl_discount_original_price'] ?? '';
			$cart_item['idl_discount_label'] = $values['idl_discount_label'] ?? '';
			$cart_item['idl_discount_badge'] = $values['idl_discount_badge'] ?? '';
			$cart_item['idl_discount_quantity'] = $values['idl_discount_quantity'] ?? '';
			$cart_item['idl_bundle_id'] = $values['idl_bundle_id'] ?? '';
			$cart_item['idl_bundle_main'] = $values['idl_bundle_main'] ?? false;
		}
		
		return $cart_item;
	}

	public function update_cart_item_prices( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['idl_discount_price'] ) ) {
				$cart_item['data']->set_price( $cart_item['idl_discount_price'] );
			}
		}
	}

	public function display_cart_item_price( $price, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['idl_discount_price'] ) ) {
			return wc_price( $cart_item['idl_discount_price'] );
		}
		
		return $price;
	}

	public function display_discount_data_in_cart( $item_data, $cart_item ) {
		if ( isset( $cart_item['idl_discount_type'] ) ) {
			if ( $cart_item['idl_discount_type'] === 'quantity' ) {
				// Show original price vs discount price
				if ( ! empty( $cart_item['idl_discount_original_price'] ) ) {
					$item_data[] = array(
						'key' => __( 'Regular Price', 'idl-pricediscount' ),
						'value' => wc_price( $cart_item['idl_discount_original_price'] ) . ' ' . __( 'each', 'idl-pricediscount' ),
						'display' => ''
					);
				}
				
				$item_data[] = array(
					'key' => __( 'Discount Price', 'idl-pricediscount' ),
					'value' => wc_price( $cart_item['idl_discount_price'] ) . ' ' . __( 'each', 'idl-pricediscount' ),
					'display' => ''
				);
				
				if ( ! empty( $cart_item['idl_discount_original_price'] ) && ! empty( $cart_item['idl_discount_price'] ) ) {
					$savings_per_item = $cart_item['idl_discount_original_price'] - $cart_item['idl_discount_price'];
					$total_savings = $savings_per_item * $cart_item['quantity'];
					
					$item_data[] = array(
						'key' => __( 'You Save', 'idl-pricediscount' ),
						'value' => wc_price( $total_savings ) . ' ' . __( 'total', 'idl-pricediscount' ),
						'display' => ''
					);
				}
				
				if ( ! empty( $cart_item['idl_discount_label'] ) ) {
					$item_data[] = array(
						'key' => __( 'Discount Type', 'idl-pricediscount' ),
						'value' => $cart_item['idl_discount_label'],
						'display' => ''
					);
				}
			}
			
			if ( $cart_item['idl_discount_type'] === 'bundle' ) {
				if ( isset( $cart_item['idl_bundle_main'] ) && $cart_item['idl_bundle_main'] ) {
					$item_data[] = array(
						'key' => __( 'Bundle Deal', 'idl-pricediscount' ),
						'value' => ! empty( $cart_item['idl_discount_badge'] ) ? $cart_item['idl_discount_badge'] : __( 'Main Bundle Item', 'idl-pricediscount' ),
						'display' => ''
					);
				} else {
					$item_data[] = array(
						'key' => __( 'Bundle Item', 'idl-pricediscount' ),
						'value' => __( 'Included in bundle (Free)', 'idl-pricediscount' ),
						'display' => ''
					);
				}
			}
		}
		
		return $item_data;
	}

	public function update_cart_item_price( $price, $cart_item, $cart_item_key ) {
		// This is deprecated in favor of update_cart_item_prices
		return $price;
	}

	public function save_discount_data_to_order_items( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['idl_discount_type'] ) ) {
			$item->add_meta_data( '_idl_discount_type', $values['idl_discount_type'] );
			$item->add_meta_data( '_idl_discount_index', $values['idl_discount_index'] );
			$item->add_meta_data( '_idl_discount_price', $values['idl_discount_price'] );
			
			if ( ! empty( $values['idl_discount_original_price'] ) ) {
				$item->add_meta_data( '_idl_discount_original_price', $values['idl_discount_original_price'] );
			}
			
			if ( ! empty( $values['idl_discount_label'] ) ) {
				$item->add_meta_data( '_idl_discount_label', $values['idl_discount_label'] );
			}
			
			if ( ! empty( $values['idl_bundle_id'] ) ) {
				$item->add_meta_data( '_idl_bundle_id', $values['idl_bundle_id'] );
				$item->add_meta_data( '_idl_bundle_main', $values['idl_bundle_main'] );
			}
		}
	}

	public function modify_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		// Check if this item has discount data
		if ( isset( $cart_item['idl_discount_type'] ) ) {
			if ( $cart_item['idl_discount_type'] === 'quantity' ) {
				// For quantity discounts, show quantity as read-only text
				$quantity = $cart_item['quantity'];
				$product_quantity = '<div class="quantity">';
				$product_quantity .= '<span class="qty-text">' . sprintf( __( 'Qty: %d', 'idl-pricediscount' ), $quantity ) . '</span>';
				$product_quantity .= '<input type="hidden" name="cart[' . $cart_item_key . '][qty]" value="' . $quantity . '" />';
				$product_quantity .= '<small class="qty-note">' . __( '(Fixed quantity for discount)', 'idl-pricediscount' ) . '</small>';
				$product_quantity .= '</div>';
			} elseif ( $cart_item['idl_discount_type'] === 'bundle' ) {
				// For bundle items, also show as read-only
				$quantity = $cart_item['quantity'];
				$product_quantity = '<div class="quantity">';
				$product_quantity .= '<span class="qty-text">' . sprintf( __( 'Qty: %d', 'idl-pricediscount' ), $quantity ) . '</span>';
				$product_quantity .= '<input type="hidden" name="cart[' . $cart_item_key . '][qty]" value="' . $quantity . '" />';
				$product_quantity .= '<small class="qty-note">' . __( '(Bundle item)', 'idl-pricediscount' ) . '</small>';
				$product_quantity .= '</div>';
			}
		}
		
		return $product_quantity;
	}

	public function prevent_quantity_change( $cart_item_key, $cart ) {
		$cart_item = $cart->get_cart_item( $cart_item_key );
		
		if ( isset( $cart_item['idl_discount_type'] ) ) {
			// If someone tries to change quantity via URL or other means, reset it
			if ( $cart_item['idl_discount_type'] === 'quantity' && isset( $cart_item['idl_discount_quantity'] ) ) {
				$cart->set_quantity( $cart_item_key, $cart_item['idl_discount_quantity'] );
			} elseif ( $cart_item['idl_discount_type'] === 'bundle' ) {
				$cart->set_quantity( $cart_item_key, 1 );
			}
		}
	}

	public function disable_bundle_item_removal( $remove_link, $cart_item_key ) {
		$cart_item = WC()->cart->get_cart_item( $cart_item_key );
		
		if ( isset( $cart_item['idl_discount_type'] ) && $cart_item['idl_discount_type'] === 'bundle' ) {
			return '<span class="bundle-item-locked">' . __( 'Bundle Item', 'idl-pricediscount' ) . '</span>';
		}
		
		// Also disable removal for quantity discount items
		if ( isset( $cart_item['idl_discount_type'] ) && $cart_item['idl_discount_type'] === 'quantity' ) {
			return '<span class="discount-item-locked">' . __( 'Discount Item', 'idl-pricediscount' ) . '</span>';
		}
		
		return $remove_link;
	}

	public function redirect_bundle_to_checkout( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		if ( isset( $cart_item_data['idl_discount_type'] ) && $cart_item_data['idl_discount_type'] === 'bundle' ) {
			if ( ! wp_doing_ajax() ) {
				wp_redirect( wc_get_checkout_url() );
				exit;
			}
		}
	}
}
