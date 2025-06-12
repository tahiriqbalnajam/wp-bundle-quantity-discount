(function( $ ) {
	'use strict';

	$(document).ready(function() {
		// Show/hide discount sections based on radio selection
		function toggleDiscountSections() {
			var selectedType = $('input[name="idl_discount_type"]:checked').val();
			
			if (selectedType === 'quantity') {
				$('#quantity_discount_section').show().css('display', 'block');
				$('#bundle_discount_section').hide().css('display', 'none');
				// Disable validation for hidden bundle fields
				$('#bundle_discount_section input, #bundle_discount_section textarea').prop('disabled', true);
				// Enable validation for visible quantity fields
				$('#quantity_discount_section input, #quantity_discount_section textarea').prop('disabled', false);
			} else if (selectedType === 'bundle') {
				$('#quantity_discount_section').hide().css('display', 'none');
				$('#bundle_discount_section').show().css('display', 'block');
				// Disable validation for hidden quantity fields
				$('#quantity_discount_section input, #quantity_discount_section textarea').prop('disabled', true);
				// Enable validation for visible bundle fields
				$('#bundle_discount_section input, #bundle_discount_section textarea').prop('disabled', false);
			} else {
				$('#quantity_discount_section, #bundle_discount_section').hide().css('display', 'none');
				// Disable validation for all hidden fields
				$('#quantity_discount_section input, #quantity_discount_section textarea, #bundle_discount_section input, #bundle_discount_section textarea').prop('disabled', true);
			}
		}

		// Initialize on page load
		toggleDiscountSections();

		// Handle radio button changes
		$(document).on('change', 'input[name="idl_discount_type"]', function() {
			toggleDiscountSections();
		});

		// Add new quantity rule
		var quantityRuleIndex = $('#quantity_rules .quantity_rule_row').length;
		$(document).on('click', '#add_quantity_rule', function(e) {
			e.preventDefault();
			var newRow = createQuantityRuleRow(quantityRuleIndex);
			$('#quantity_rules').append(newRow);
			quantityRuleIndex++;
		});

		// Add new bundle rule
		var bundleRuleIndex = $('#bundle_rules .bundle_rule_row').length;
		$(document).on('click', '#add_bundle_rule', function(e) {
			e.preventDefault();
			var newRow = createBundleRuleRow(bundleRuleIndex);
			$('#bundle_rules').append(newRow);
			bundleRuleIndex++;
		});

		// Remove rule
		$(document).on('click', '.remove_rule', function(e) {
			e.preventDefault();
			$(this).closest('.quantity_rule_row, .bundle_rule_row').remove();
		});

		// Product search for bundle
		$(document).on('keyup', '.product_search', function(e) {
			var $this = $(this);
			var searchTerm = $this.val();
			
			// Handle Enter key press
			if (e.keyCode === 13) {
				e.preventDefault();
				var $firstSuggestion = $this.siblings('.product-suggestions').find('.suggestion:first');
				if ($firstSuggestion.length > 0) {
					$firstSuggestion.click();
				}
				return;
			}
			
			if (searchTerm.length < 3) {
				$this.siblings('.product-suggestions').remove();
				return;
			}

			$.ajax({
				url: idl_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'search_products_for_bundle',
					term: searchTerm,
					nonce: idl_ajax.nonce
				},
				success: function(response) {
					$this.siblings('.product-suggestions').remove();
					
					if (response && response.length > 0) {
						var suggestions = '<div class="product-suggestions">';
						response.forEach(function(product) {
							suggestions += '<div class="suggestion" data-id="' + product.id + '" data-name="' + product.text + '">' + product.text + '</div>';
						});
						suggestions += '</div>';
						
						$this.after(suggestions);
					}
				}
			});
		});

		// Handle keydown for Enter key to prevent form submission
		$(document).on('keydown', '.product_search', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});

		// Select product from suggestions
		$(document).on('click', '.suggestion', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			var $suggestion = $(this);
			var productId = $suggestion.data('id');
			var productName = $suggestion.data('name');
			
			console.log('Suggestion clicked:', {
				id: productId,
				name: productName,
				element: $suggestion
			});
			
			// Find the bundle rule row that contains this suggestion
			var $bundleRow = $suggestion.closest('.bundle_rule_row');
			if ($bundleRow.length === 0) {
				// If not found, search by proximity to the search input
				var $searchInput = $('.product_search').filter(function() {
					return $(this).siblings('.product-suggestions').find($suggestion).length > 0;
				});
				if ($searchInput.length > 0) {
					$bundleRow = $searchInput.closest('.bundle_rule_row');
				}
			}
			
			console.log('Bundle row found:', $bundleRow.length > 0);
			
			if ($bundleRow.length === 0) {
				console.error('Could not find bundle rule row');
				return;
			}
			
			// Now find elements within this specific bundle row
			var $hiddenInput = $bundleRow.find('input[name*="[products]"]');
			var $display = $bundleRow.find('.selected-products-display');
			
			console.log('Elements found:', {
				hiddenInput: $hiddenInput.length,
				hiddenInputName: $hiddenInput.attr('name'),
				display: $display.length,
				bundleRowIndex: $bundleRow.data('index')
			});
			
			if ($hiddenInput.length === 0 || $display.length === 0) {
				console.error('Could not find required elements within bundle row');
				console.log('Bundle row HTML:', $bundleRow.html());
				return;
			}
			
			// Get current selected products
			var currentProducts = $hiddenInput.val() || '';
			var selectedIds = [];
			
			if (currentProducts) {
				var matches = currentProducts.match(/\(ID: (\d+)\)/g);
				if (matches) {
					selectedIds = matches.map(function(match) {
						return parseInt(match.match(/\d+/)[0]);
					});
				}
			}
			
			console.log('Current selected IDs:', selectedIds);
			
			// Check if product is already selected
			if (selectedIds.indexOf(parseInt(productId)) === -1) {
				// Clean product name (remove price info if present)
				var cleanProductName = productName.replace(/ - \$[\d,.]+/, '').replace(/ - €[\d,.]+/, '');
				
				// Add new product
				var newProductString = cleanProductName + ' (ID: ' + productId + ')';
				var updatedProducts = currentProducts ? currentProducts + ', ' + newProductString : newProductString;
				$hiddenInput.val(updatedProducts);
				
				console.log('Updated products string:', updatedProducts);
				
				// Add to display
				var productHtml = '<div class="selected-product-item" data-product-id="' + productId + '">';
				productHtml += '<span class="product-name">' + cleanProductName + '</span>';
				productHtml += '<button type="button" class="remove-product" data-product-id="' + productId + '">×</button>';
				productHtml += '</div>';
				
				console.log('Adding product HTML:', productHtml);
				$display.append(productHtml);
				
				// Verify it was added
				console.log('Display now contains:', $display.children().length, 'items');
			} else {
				console.log('Product already selected');
				alert('Product is already selected');
			}
			
			// Clear search and remove suggestions
			$bundleRow.find('.product_search').val('');
			$('.product-suggestions').remove();
		});

		// Remove selected product
		$(document).on('click', '.remove-product', function() {
			var $button = $(this);
			var productId = $button.data('product-id');
			var $formField = $button.closest('.form-field');
			var $hiddenInput = $formField.find('.selected_products');
			
			// Get current products value safely
			var currentProducts = $hiddenInput.val() || '';
			
			if (currentProducts) {
				// Remove from hidden input
				var regex = new RegExp('[^,]*\\(ID: ' + productId + '\\)[,\\s]*', 'g');
				var updatedProducts = currentProducts.replace(regex, '');
				
				// Clean up extra commas and spaces
				updatedProducts = updatedProducts.replace(/^[,\s]+|[,\s]+$/g, ''); // Remove leading/trailing commas and spaces
				updatedProducts = updatedProducts.replace(/[,\s]*,[,\s]*/g, ', '); // Clean up multiple commas
				
				$hiddenInput.val(updatedProducts);
			}
			
			// Remove from display
			$button.closest('.selected-product-item').remove();
		});

		function createQuantityRuleRow(index) {
			return `
				<div class="quantity_rule_row" data-index="${index}">
					<p class="form-field">
						<label>Quantity</label>
						<input type="number" name="quantity_rules[${index}][quantity]" value="" min="1" step="1" />
					</p>
					<p class="form-field">
						<label>Price</label>
						<input type="number" name="quantity_rules[${index}][price]" value="" step="0.01" min="0" />
					</p>
					<p class="form-field">
						<label>Label</label>
						<input type="text" name="quantity_rules[${index}][label]" value="" />
					</p>
					<p class="form-field">
						<label>Badge</label>
						<input type="text" name="quantity_rules[${index}][badge]" value="" />
					</p>
					<p class="form-field">
						<label>Description</label>
						<textarea name="quantity_rules[${index}][description]"></textarea>
					</p>
					<button type="button" class="button remove_rule">X</button>
				</div>
			`;
		}

		function createBundleRuleRow(index) {
			return `
				<div class="bundle_rule_row" data-index="${index}">
					<p class="form-field">
						<label>Bundle Products</label>
						<input type="text" class="product_search" name="bundle_rules[${index}][products_search]" value="" placeholder="Search and select products..." autocomplete="off" />
						<input type="hidden" name="bundle_rules[${index}][products]" value="" class="selected_products" />
						<div class="selected-products-display"></div>
					</p>
					<p class="form-field">
						<label>Bundle Price</label>
						<input type="number" name="bundle_rules[${index}][price]" value="" step="0.01" min="0" />
					</p>
					<p class="form-field">
						<label>Badge</label>
						<input type="text" name="bundle_rules[${index}][badge]" value="" />
					</p>
					<p class="form-field">
						<label>Description</label>
						<textarea name="bundle_rules[${index}][description]"></textarea>
					</p>
					<button type="button" class="button remove_rule">X</button>
				</div>
			`;
		}

		// Prevent form submission if there are validation errors in hidden sections
		$(document).on('submit', '#post', function(e) {
			var selectedType = $('input[name="idl_discount_type"]:checked').val();
			
			// Before submission, temporarily enable all fields to ensure data is saved
			$('#quantity_discount_section input, #quantity_discount_section textarea, #bundle_discount_section input, #bundle_discount_section textarea').prop('disabled', false);
			
			// But clear validation state for fields that shouldn't be validated
			if (selectedType !== 'quantity') {
				$('#quantity_discount_section input, #quantity_discount_section textarea').each(function() {
					if (this.setCustomValidity) {
						this.setCustomValidity('');
					}
				});
			}
			
			if (selectedType !== 'bundle') {
				$('#bundle_discount_section input, #bundle_discount_section textarea').each(function() {
					if (this.setCustomValidity) {
						this.setCustomValidity('');
					}
				});
			}
		});
	});

})( jQuery );
