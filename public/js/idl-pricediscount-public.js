(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function() {
		// Show add to cart button for default selected option on page load
		function showDefaultAddToCartButton() {
			var $defaultChecked = $('input[name="idl_discount_option"]:checked');
			if ($defaultChecked.length > 0) {
				$('.idl-add-to-cart-btn').hide();
				$defaultChecked.closest('.discount-option').find('.idl-add-to-cart-btn').show();
			}
		}
		
		// Initialize on page load
		showDefaultAddToCartButton();
		
		// Handle discount option selection
		$(document).on('change', 'input[name="idl_discount_option"]', function() {
			var $selectedOption = $(this);
			var $parentOption = $selectedOption.closest('.discount-option');
			var discountType = $parentOption.data('type');
			var discountIndex = $parentOption.data('index');
			
			// Hide all add to cart buttons first
			$('.idl-add-to-cart-btn').hide();
			
			// Show the add to cart button for the selected option
			$parentOption.find('.idl-add-to-cart-btn').show();
			
			// Scroll to the add to cart button
			setTimeout(function() {
				$('html, body').animate({
					scrollTop: $parentOption.find('.idl-add-to-cart-btn').offset().top - 100
				}, 500);
			}, 100);
			
			// For quantity discounts, we don't need to update anything else
			// as the quantity is fixed per option
		});

		// Handle add to cart with discount
		$(document).on('click', '.idl-add-to-cart-btn', function(e) {
			e.preventDefault();
			
			var $button = $(this);
			var discountType = $button.data('type');
			var discountIndex = $button.data('index');
			var selectedQuantity = $button.data('quantity') || 1;
			
			// Get product ID from button data attribute first
			var productId = $button.data('product-id');
			
			// Fallback methods if not found
			if (!productId) {
				productId = $('#idl-discount-options').data('product-id');
			}
			
			if (!productId) {
				productId = $('input[name="add-to-cart"]').val();
			}
			
			if (!productId) {
				var bodyClasses = $('body').attr('class');
				var match = bodyClasses.match(/postid-(\d+)/);
				if (match) {
					productId = match[1];
				}
			}
			
			console.log('Product ID found:', productId);
			
			if (!productId) {
				alert('Product ID not found. Please refresh the page and try again.');
				return;
			}
			
			$button.addClass('loading').prop('disabled', true);
			
			$.ajax({
				url: idl_public_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'add_discount_to_cart',
					product_id: productId,
					discount_type: discountType,
					discount_index: discountIndex,
					quantity: selectedQuantity,
					nonce: idl_public_ajax.nonce
				},
				success: function(response) {
					if (response.success) {
						if (response.data.redirect) {
							window.location.href = response.data.redirect;
						}
					} else {
						alert(response.data || 'Error adding to cart');
					}
				},
				error: function(xhr, status, error) {
					alert('Error adding to cart: ' + error);
				},
				complete: function() {
					$button.removeClass('loading').prop('disabled', false);
				}
			});
		});

		// Remove the updatePriceDisplay function and related code since we're not changing the main price
		
		// Prevent manual quantity changes for bundle items in cart
		$(document).on('change', '.cart .qty', function() {
			var $row = $(this).closest('tr');
			if ($row.find('.bundle-item-locked').length > 0) {
				$(this).val(1);
				alert('Bundle item quantities cannot be changed.');
			}
		});
		
		// Prevent manual quantity changes for discount items in cart
		$(document).on('change keyup', '.cart .qty', function() {
			var $row = $(this).closest('tr');
			if ($row.find('.bundle-item-locked, .discount-item-locked').length > 0) {
				var originalValue = $(this).data('original-value') || $(this).attr('min') || 1;
				$(this).val(originalValue);
				
				// Show warning message
				if (!$('.qty-warning').length) {
					$row.find('.quantity').append('<div class="qty-warning" style="color: #d32f2f; font-size: 11px; margin-top: 5px;">Quantity cannot be changed for discount items</div>');
					setTimeout(function() {
						$('.qty-warning').fadeOut(function() {
							$(this).remove();
						});
					}, 3000);
				}
				
				return false;
			}
		});

		// Prevent increment/decrement buttons from working on discount items
		$(document).on('click', '.cart .quantity .plus, .cart .quantity .minus', function(e) {
			var $row = $(this).closest('tr');
			if ($row.find('.bundle-item-locked, .discount-item-locked').length > 0) {
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		});

		// Store original values for discount items
		$(document).on('focus', '.cart .qty', function() {
			$(this).data('original-value', $(this).val());
		});

		// Disable update cart button if discount items are present
		if ($('.bundle-item-locked, .discount-item-locked').length > 0) {
			$('button[name="update_cart"]').prop('disabled', true).addClass('disabled');
			$('button[name="update_cart"]').after('<small style="display: block; color: #666; margin-top: 5px;">Cart contains discount items with fixed quantities</small>');
		}
	});

})( jQuery );
