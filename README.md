# Price Discount Plugin for WooCommerce

A comprehensive WordPress plugin that adds advanced discount functionality to WooCommerce products, supporting both quantity-based discounts and product bundle deals.

## Features

### 🎯 Quantity Discounts
- Set up tiered pricing based on purchase quantities
- Bulk discount pricing (e.g., buy 5+ items at $10 each instead of $15)
- Custom labels and badges for each discount tier
- Automatic savings calculation and display
- Fixed quantity enforcement in cart (prevents quantity manipulation)

### 📦 Bundle Discounts
- Create product bundles with special pricing
- Multi-product selection with search functionality
- Bundle price vs. individual product pricing comparison
- Automatic bundle cart management
- Direct checkout redirection for bundle purchases
- Bundle item protection (prevents individual item removal)

### 🛒 Cart & Checkout Integration
- Display original vs. discount pricing in cart
- Show savings amount for each item
- Fixed quantities for discount items (non-editable)
- Bundle items marked as "included" with special pricing
- Complete order tracking with discount metadata

### 🔧 Admin Features
- **Product-level Configuration**: Add discount options directly to product edit pages
- **Dynamic Rule Management**: Add/remove discount rules with intuitive interface
- **Product Search**: Real-time product search for bundle creation
- **Visual Product Selection**: Selected products displayed as removable tags
- **WooCommerce Integration**: Seamless integration with existing WooCommerce workflow

## Installation

1. **Upload Plugin Files**
   ```
   /wp-content/plugins/idl-pricediscount/
   ```

2. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Price Discount" and click "Activate"

3. **Verify Requirements**
   - WooCommerce must be installed and active
   - Plugin will deactivate automatically if WooCommerce is not available

## Usage

### Setting Up Quantity Discounts

1. **Edit a Product**
   - Go to Products → Edit Product
   - Click on the "Discount" tab

2. **Configure Quantity Rules**
   - Select "Quantity Discount" radio button
   - Add rules with:
     - **Quantity**: Minimum quantity required
     - **Price**: Price per item at this quantity
     - **Label**: Display label for the discount
     - **Badge**: Special badge text (e.g., "BULK SAVE")
     - **Description**: Additional details about the discount

3. **Example Configuration**
   ```
   Rule 1: Qty 1-4   → $15.00 each
   Rule 2: Qty 5-9   → $12.00 each (Save 20%)
   Rule 3: Qty 10+   → $10.00 each (Save 33%)
   ```

### Setting Up Bundle Discounts

1. **Edit a Product**
   - Go to Products → Edit Product
   - Click on the "Discount" tab

2. **Configure Bundle Rules**
   - Select "Bundle Discount" radio button
   - Search and select products to include in bundle
   - Set bundle total price
   - Add badge and description

3. **Example Bundle**
   ```
   Main Product: T-Shirt ($25)
   Bundle Products: 
   - Cap ($15)
   - Mug ($10)
   
   Regular Total: $50
   Bundle Price: $40
   Savings: $10
   ```

### Frontend Experience

#### For Customers - Quantity Discounts
- View available quantity tiers on product page
- See pricing breakdown and savings
- Select desired quantity option
- Add to cart with fixed quantity and pricing

#### For Customers - Bundle Discounts
- View complete bundle contents and pricing
- See individual vs. bundle pricing comparison
- Add entire bundle to cart
- Automatic redirect to checkout
- Bundle items cannot be removed individually

## Technical Details

### File Structure
```
idl-pricediscount/
├── idl-pricediscount.php          # Main plugin file
├── includes/
│   ├── class-idl-pricediscount.php        # Core plugin class
│   ├── class-idl-pricediscount-loader.php # Hook loader
│   ├── class-idl-pricediscount-i18n.php   # Internationalization
│   ├── class-idl-pricediscount-activator.php
│   └── class-idl-pricediscount-deactivator.php
├── admin/
│   ├── class-idl-pricediscount-admin.php  # Admin functionality
│   ├── css/idl-pricediscount-admin.css    # Admin styles
│   └── js/idl-pricediscount-admin.js      # Admin scripts
├── public/
│   ├── class-idl-pricediscount-public.php # Frontend functionality
│   ├── css/idl-pricediscount-public.css   # Frontend styles
│   └── js/idl-pricediscount-public.js     # Frontend scripts
└── README.md                              # This file
```

### Database Storage
- Discount rules stored as product meta data
- Cart items include discount metadata
- Order items preserve discount information for reporting

### Hooks and Filters
- `woocommerce_product_data_tabs` - Adds discount tab
- `woocommerce_product_data_panels` - Discount tab content
- `woocommerce_before_calculate_totals` - Price calculations
- `woocommerce_cart_item_quantity` - Quantity control
- `woocommerce_cart_item_remove_link` - Removal protection

## Requirements

- **WordPress**: 5.0 or higher
- **WooCommerce**: 4.0 or higher
- **PHP**: 7.4 or higher
- **Browser**: Modern browsers with JavaScript enabled

## Compatibility

- ✅ WooCommerce 4.0+
- ✅ WordPress 5.0+
- ✅ PHP 7.4+
- ✅ Most WooCommerce themes
- ✅ WooCommerce cart and checkout pages
- ✅ Mobile responsive design

## Support

For support and feature requests, please contact the plugin developer.

## Changelog

### Version 1.0.0
- Initial release
- Quantity discount functionality
- Bundle discount functionality
- WooCommerce integration
- Cart and checkout integration
- Admin interface
- Frontend display
- Mobile responsiveness

## License

This plugin is licensed under GPL-2.0+. See the license file for more details.
