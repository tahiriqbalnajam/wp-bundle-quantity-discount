<div class="idl-bundle-discounts">
		<h4><?php _e( 'Bundle Deals Available', 'idl-pricediscount' ); ?> </h4>
		<?php
		// Add default single product option
		$regular_price = $product->get_regular_price();
		$sale_price = $product->get_sale_price();
		$current_price = $product->get_price();
		
		// Check if any bundle is marked as default
		$has_default_bundle = false;
		foreach ( $bundle_rules as $rule ) {
			if ( isset( $rule['is_default'] ) && $rule['is_default'] ) {
				$has_default_bundle = true;
				break;
			}
		}
		?>
		<div class="discount-option bundle-option" data-type="bundle" data-index="default" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
		    <label>
		        <div class="bundle-info-main">
			        <div class="bundle-info">
			            <div class="bundle-left-content">
			                <div class="discount_option">
                                <input type="radio" name="idl_discount_option" value="bundle_default" data-price="<?php echo esc_attr( $current_price ); ?>" data-products="" <?php echo $has_default_bundle ? '' : 'checked'; ?>>
                            </div>
			                <div class="discount-description">1 Set</div>
			            </div>
			            <div class="bundle-price">
					        <div class="discountprice"><?php echo wc_price( $current_price ); ?></div>
					        <?php if ( $sale_price && $regular_price > $sale_price ) : ?>
					            <div class="regularprice"><?php echo wc_price( $regular_price ); ?></div>
					        <?php endif; ?>
				        </div>
			        </div>
		
		        <div class="bundle-products">
		            <div class="bundle-product-list"><ul>
		                <li>
		                    <?php
		                    $main_image_id = $product->get_image_id();
		                    if ( $main_image_id ) {
			                    $main_image_url = wp_get_attachment_image_src( $main_image_id, 'thumbnail' );
			                    if ( $main_image_url ) {
				                    echo '<img src="' . esc_url( $main_image_url[0] ) . '" alt="' . esc_attr( $product->get_name() ) . '" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; vertical-align: middle;">';
			                    }
		                    }
		                    echo esc_html( $product->get_name() );
		                    ?>
		                </li>
		            </ul></div>
		        </div>
		
		        </div>
		    </label>
		    <button type="button" class="button alt idl-add-to-cart-btn" data-type="bundle" data-index="default" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" style="display:none;">
		        <?php _e( 'Add Bundle to Cart', 'idl-pricediscount' ); ?>
		    </button>
		</div>
		
		<?php
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
			$is_default = isset( $rule['is_default'] ) && $rule['is_default'];
			?>
			<div class="discount-option bundle-option" data-type="bundle" data-index="<?php echo esc_attr( $index ); ?>" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
			    <label>
			        <div class="bundle-info-main">
				        <div class="bundle-info">
				            <div class="bundle-left-content">
				                <div class="discount_option">
                                    <input type="radio" name="idl_discount_option" value="bundle_<?php echo esc_attr( $index ); ?>" data-price="<?php echo esc_attr( $rule['price'] ); ?>" data-products="<?php echo esc_attr( $rule['products'] ); ?>" <?php echo $is_default ? 'checked' : ''; ?>>
                                </div>
				                <?php
				                if ( ! empty( $rule['description'] ) ) {
					                echo '<div class="discount-description">' . esc_html( $rule['description'] ) . '</div>';
				                }
				                if ( ! empty( $rule['badge'] ) ) {
					                echo '<div class="discount-badge">' . esc_html( $rule['badge'] ) . '</div>';
				                }
				                ?>
				            </div>
				            <div class="bundle-price">
						        <div class="discountprice"><?php echo wc_price( $rule['price'] ); ?></div>
						        <div class="regularprice"><?php echo wc_price( $total_regular_price ); ?></div>
					        </div>
				        </div>
			
			
			
			        <div class="bundle-products">
			            <div class="bundle-product-list"><ul>
			            <?php
			            
			            foreach ( $bundle_products as $bundle_product_id ) {
				            $bundle_product = wc_get_product( $bundle_product_id );
				            if ( $bundle_product ) {
					            echo '<li>';
					            $bundle_image_id = $bundle_product->get_image_id();
					            if ( $bundle_image_id ) {
						            $bundle_image_url = wp_get_attachment_image_src( $bundle_image_id, 'thumbnail' );
						            if ( $bundle_image_url ) {
							            echo '<img src="' . esc_url( $bundle_image_url[0] ) . '" alt="' . esc_attr( $bundle_product->get_name() ) . '" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; vertical-align: middle;">';
						            }
					            }
					            echo '+ FREE &nbsp;'.$bundle_product->get_name() .'</li>';
				            }
			            }
			            ?>
			            </ul></div>
			        </div>
			
			        </div>
			    </label>
			    <button type="button" class="button alt idl-add-to-cart-btn" data-type="bundle" data-index="<?php echo esc_attr( $index ); ?>" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" style="display:none;">
			        <?php _e( 'Add Bundle to Cart', 'idl-pricediscount' ); ?>
			    </button>
			</div>
		<?php
		}?>
	</div>