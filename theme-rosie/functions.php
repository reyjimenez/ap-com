<?php

/* custom PHP functions below this line */

add_filter( 'woocommerce_order_number', 'webendev_woocommerce_order_number', 1, 2 );
/**
 * Add Prefix to WooCommerce Order Number
 * 
 */
function webendev_woocommerce_order_number( $oldnumber, $order ) {
  return 'REC' . $order->id;
}

function wc_xml_export_suite_export_order_on_payment( $order_id ) {

$export = new WC_Customer_Order_XML_Export_Suite_Handler( $order_id );

// for FTP
$export->upload();

// uncomment for HTTP POST
// $export->http_post();
}
add_action( 'woocommerce_payment_complete', 'wc_xml_export_suite_export_order_on_payment' );

// Remove all currency symbols
function sww_remove_wc_currency_symbols( $currency_symbol, $currency ) {
     $currency_symbol = '';
     return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'sww_remove_wc_currency_symbols', 10, 2);


/**
 * Maxlength billing address 25
 */

add_action("wp_footer", "cod_set_max_length");

function cod_set_max_length(){
if( !is_checkout())
   return;
?>
<script>
jQuery(document).ready(function($){
      $("#billing_company").attr('maxlength','35');
      $("#billing_address_1").attr('maxlength','25');
      $("#billing_address_2").attr('maxlength','25');
      $("#shipping_company").attr('maxlength','35');
      $("#shipping_address_1").attr('maxlength','25');
      $("#shipping_address_2").attr('maxlength','25');

});
</script>
<?php
}

/**
 * Deny PO BOX
 */

add_action('woocommerce_after_checkout_validation', 'deny_pobox_postcode');

function deny_pobox_postcode( $posted ) {
  global $woocommerce;
  
  $address  = ( isset( $posted['shipping_address_1'] ) ) ? $posted['shipping_address_1'] : $posted['billing_address_1'];
  $postcode = ( isset( $posted['shipping_postcode'] ) ) ? $posted['shipping_postcode'] : $posted['billing_postcode'];
  
  $replace  = array(" ", ".", ",");
  $address  = strtolower( str_replace( $replace, '', $address ) );
  $postcode = strtolower( str_replace( $replace, '', $postcode ) );

  if ( strstr( $address, 'pobox' ) || strstr( $postcode, 'pobox' ) ) {
    $woocommerce->add_error( "Sorry, we don't ship to PO Boxes." );
  }
}

add_filter( 'woocommerce_package_rates', 'hide_standard_shipping_in_state' , 10, 2 );

/**
* Hide free shipping if shipping state is AK, HI
*
* @param array $available_methods
*/
function hide_standard_shipping_in_state( $rates, $package ) {

  $excluded_states = array( 'AK','HI' );

  if( isset( $rates['free_shipping'] ) AND in_array( WC()->customer->get_shipping_state(), $excluded_states ) ) {

    // remove free shipping option
    unset( $rates['free_shipping'] );
  }

  return $rates;
}
?>