=== Plugin Name ===
Contributors: terrytsang
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=terry@terrytsang.com&item_name=Donation+for+TerryTsang+Wordpress+WebDev
Plugin Name: WooCommerce Extra Fee Option
Plugin URI:  http://terrytsang.com/shop/shop/woocommerce-extra-fee-option/
Tags: woocommerce, extra fee, minimum order, service charge, e-commerce, payment, shipping, product, category
Requires at least: 3.6.1
Tested up to: 4.0.1
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WooCommerce plugin that add an extra fee to customer order based on conditions.

== Description ==

A WooCommerce plugin that allow user to add an extra fee for any order with the minimum amount option. If you set minimum order amount, then it will compare with current cart/order total. Or else, it just add extra fee to all orders.

In WooCommerce Settings Panel, there will be a new submenu link called 'Extra Fee Option' where you can:

*   Enabled / Disabled the extra fee option
*   Change "Label" to any text
*   Change "Amount" to total extra fee you want to apply
*   Choose "Type" to Fixed Fee or Cart Percentage (%)
*   Choose "Taxable"
*   Change "Minimum Order" to any amount

= Features =

*   Implement an extra fee for any order less or equal than the minimum order amount
*   2 languages available : English UK (en_GB) and Chinese (zh_CN)

= IMPORTANT NOTES =
*   Do use POEdit and open 'wc-extra-fee-option.pot' file and save the file as wc-extra-fee-option-[language code].po, then put that into languages folder for this plugin.

= Featured Plugins by Terry Tsang =
*   [WooCommerce Direct Checkout](http://wordpress.org/plugins/woocommerce-direct-checkout/)
*   [WooCommerce Custom Checkout Options](http://terrytsang.com/shop/shop/woocommerce-custom-checkout-options/)
*   [WooCommerce Social Buttons PRO](http://terrytsang.com/shop/shop/woocommerce-social-buttons-pro/)


= GET PRO VERSION =
[WooCommerce Extra Fee Options PRO](http://terrytsang.com/shop/shop/woocommerce-extra-fee-option-pro/)


== Installation ==

1. Upload the entire *woocommerce-extra-fee-option* folder to the */wp-content/plugins/* directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce Settings panel at left sidebar menu and update the options at Tab *Extra Fee Option* there.
4. That's it. You're ready to go and cheers!

== Screenshots ==

1. [screenhot-1.png] Screenshot Admin WooCommerce Settings - Extra Fee Option
2. [screenhot-2.png] Screenshot Frontend WooCommerce - Catalog page
3. [screenhot-3.png] Screenshot Frontend WooCommerce - Product page

== Changelog ==

= 1.0.7 =
* Updated Minimum Order condition checking

= 1.0.6 =
* Fixed PayPal problem due to percentage calculation

= 1.0.5 =
* Fixed layout problem

= 1.0.4 =
* Added "Type" option to let user choose fixed fee or cart percentage
* Updated compatibility for WordPress 4.0 and latest WooCommerce 2.2.x version.

= 1.0.3 =
* Updated css and add pro version link

= 1.0.2 =
* Updated add fee function

= 1.0.1 =
* Updated readme file
* Updated minimum order amount as optional in order to add extra fee

= 1.0.0 =
* Initial Release
* Allow user to add an extra fee for any order less or equal than the minimum amount