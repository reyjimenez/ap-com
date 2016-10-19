<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_subscription_count( $count = 0, $export_type = '', $args ) {

		if( $export_type <> 'subscription' )
			return $count;

		$count = 0;
		// Check that WooCommerce Subscriptions exists
		if( class_exists( 'WC_Subscriptions' ) ) {
			$count = woo_ce_get_subscription_count();
		}
		return $count;

	}
	add_filter( 'woo_ce_get_export_type_count', 'woo_ce_get_export_type_subscription_count', 10, 3 );

	function woo_ce_get_subscription_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_subscription_count' );
		if( $cached == false ) {
			$wcs_version = woo_ce_get_wc_subscriptions_version();
			if( version_compare( $wcs_version, '2.0.1', '<' ) ) {
				if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
					// Does this store have roughly more than 3000 Subscriptions
					if( false === WC_Subscriptions::is_large_site() ) {
						if( class_exists( 'WC_Subscriptions_Manager' ) ) {
							// Check that the get_all_users_subscriptions() function exists
							if( method_exists( 'WC_Subscriptions_Manager', 'get_all_users_subscriptions' ) ) {
								if( $subscriptions = WC_Subscriptions_Manager::get_all_users_subscriptions() ) {
									if( version_compare( $wcs_version, '2.0.1', '<' ) ) {
										foreach( $subscriptions as $key => $user_subscription ) {
											if( !empty( $user_subscription ) ) {
												foreach( $user_subscription as $subscription )
													$count++;
											}
										}
										unset( $subscriptions, $subscription, $user_subscription );
									}
								}
							}
						}
					} else {
						if( method_exists( 'WC_Subscriptions', 'get_total_subscription_count' ) )
							$count = WC_Subscriptions::get_total_subscription_count();
						else
							$count = "~2500";
					}
				} else {
					if( method_exists( 'WC_Subscriptions', 'get_subscription_count' ) )
						$count = WC_Subscriptions::get_subscription_count();
				}
			} else {
				if( function_exists( 'wcs_get_subscriptions' ) ) {
					$args = array(
						'subscriptions_per_page' => -1,
						'subscription_status' => 'trash'
					);
					$count += count( wcs_get_subscriptions( $args ) );
					$args['subscription_status'] = 'any';
					$count += count( wcs_get_subscriptions( $args ) );
				}
			}
			set_transient( WOO_CD_PREFIX . '_subscription_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Filter Subscriptions by Subscription Status widget on Store Exporter screen
	function woo_ce_subscriptions_filter_by_subscription_status() {

		$subscription_statuses = woo_ce_get_subscription_statuses();

		ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-status" /> <?php _e( 'Filter Subscriptions by Subscription Status', 'woocommerce-exporter' ); ?></label></p>
<div id="export-subscriptions-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $subscription_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Subscription Status...', 'woocommerce-exporter' ); ?>" name="subscription_filter_status[]" class="chzn-select" style="width:95%;">
				<option value=""></option>
	<?php foreach( $subscription_statuses as $key => $subscription_status ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $subscription_status; ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Subscription Status\'s have been found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Status options you want to filter exported Subscriptions by. Due to a limitation in WooCommerce Subscriptions you can only filter by a single Subscription Status. Default is to include all Subscription Status options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-subscriptions-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Subscriptions by Subscription Product widget on Store Exporter screen
	function woo_ce_subscriptions_filter_by_subscription_product() {

		$products = woo_ce_get_subscription_products();

		ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-product" /> <?php _e( 'Filter Subscriptions by Subscription Product', 'woocommerce-exporter' ); ?></label></p>
<div id="export-subscriptions-filters-product" class="separator">
	<ul>
		<li>
<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Subscription Product...', 'woocommerce-exporter' ); ?>" name="subscription_filter_product[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>"><?php echo woo_ce_format_post_title( get_the_title( $product ) ); ?> (<?php printf( __( 'SKU: %s', 'woocommerce-exporter' ), get_post_meta( $product, '_sku', true ) ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Subscription Products were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Product you want to filter exported Subscriptions by. Default is to include all Subscription Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-subscriptions-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Subscriptions by Customer widget on Store Exporter screen
	function woo_ce_subscriptions_filter_by_customer() {

		$users = woo_ce_get_export_type_count( 'users' );
		if( $users < 1000 )
			$customers = woo_ce_get_customers_list();

		ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-customer" /> <?php _e( 'Filter Subscriptions by Customer', 'woocommerce-exporter' ); ?></label></p>
<div id="export-subscriptions-filters-customer" class="separator">
	<ul>
		<li>
<?php if( $users < 1000 ) { ?>
			<select id="subscription_customer" data-placeholder="<?php _e( 'Choose a Customer...', 'woocommerce-exporter' ); ?>" name="subscription_filter_customer[]" multiple class="chzn-select" style="width:95%;">
				<option value=""></option>
	<?php if( !empty( $customers ) ) { ?>
		<?php foreach( $customers as $customer ) { ?>
				<option value="<?php echo $customer->ID; ?>"><?php printf( '%s (#%s - %s)', $customer->display_name, $customer->ID, $customer->user_email ); ?></option>
		<?php } ?>
	<?php } ?>
			</select>
<?php } else { ?>
			<input type="text" id="subscription_customer" name="subscription_filter_customer" size="20" class="text" />
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Subscriptions by Customer (unique e-mail address) to be included in the export.', 'woocommerce-exporter' ); ?><?php if( $users > 1000 ) { echo ' ' . __( 'Enter a list of User ID\'s separated by a comma character.', 'woocommerce-exporter' ); } ?> <?php _e( 'Default is to include all Subscriptions.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-subscriptions-filters-customer -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Subscriptions by Source widget on Store Exporter screen
	function woo_ce_subscriptions_filter_by_source() {

		$types = false;

		ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-source" /> <?php _e( 'Filter Subscriptions by Source', 'woocommerce-exporter' ); ?></label></p>
<div id="export-subscriptions-filters-source" class="separator">
	<ul>
		<li value=""><label><input type="radio" name="subscription_filter_source" value=""<?php checked( $types, false ); ?> /><?php _e( 'Include both', 'woocommerce-exporter' ); ?></label></li>
		<li value="customer"><label><input type="radio" name="subscription_filter_source" value="customer" /><?php _e( 'Customer Subscriptions', 'woocommerce-exporter' ); ?></label></li>
		<li value="manual"><label><input type="radio" name="subscription_filter_source" value="manual" /><?php _e( 'Added via WordPress Administration', 'woocommerce-exporter' ); ?></label></li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Source you want to filter exported Subscriptions by. Default is to include all Subscription Sources.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-subscriptions-filters-source -->
<?php
		ob_end_flush();

	}

	// HTML template for Subscription Sorting widget on Store Exporter screen
	function woo_ce_subscription_sorting() {

		$orderby = woo_ce_get_option( 'subscription_orderby', 'start_date' );
		$order = woo_ce_get_option( 'subscription_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Subscription Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="subscription_orderby">
		<option value="start_date"<?php selected( 'start_date', $orderby ); ?>><?php _e( 'Start date', 'woocommerce-exporter' ); ?></option>
		<option value="expiry_date"<?php selected( 'expiry_date', $orderby ); ?>><?php _e( 'Expiry date', 'woocommerce-exporter' ); ?></option>
		<option value="end_date"<?php selected( 'end_date', $orderby ); ?>><?php _e( 'End date', 'woocommerce-exporter' ); ?></option>
		<option value="status"<?php selected( 'status', $orderby ); ?>><?php _e( 'Status', 'woocommerce-exporter' ); ?></option>
		<option value="name"<?php selected( 'name', $orderby ); ?>><?php _e( 'Name', 'woocommerce-exporter' ); ?></option>
		<option value="order_id"<?php selected( 'order_id', $orderby ); ?>><?php _e( 'Order ID', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="subscription_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Subscriptions within the exported file. By default this is set to export Subscriptions by Start date in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

function woo_ce_get_subscription_fields( $format = 'full' ) {

	$export_type = 'subscription';

	$fields = array();
	$fields[] = array(
		'name' => 'subscription_id',
		'label' => __( 'Subscription ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_id',
		'label' => __( 'Order ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'status',
		'label' => __( 'Subscription Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'recurring',
		'label' => __( 'Recurring', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user',
		'label' => __( 'User', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_status',
		'label' => __( 'Order Status', 'woocommerce-exporter' )
	);
	// Check if this is a pre-WooCommerce 2.2 instance
	$woocommerce_version = woo_get_woo_version();
	if( version_compare( $woocommerce_version, '2.2', '<' ) ) {
		$fields[] = array(
			'name' => 'post_status',
			'label' => __( 'Post Status', 'woocommerce-exporter' )
		);
	}
	$fields[] = array(
		'name' => 'start_date',
		'label' => __( 'Start Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'end_date',
		'label' => __( 'End Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'trial_end_date',
		'label' => __( 'Trial End Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'last_payment',
		'label' => __( 'Last Payment', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'next_payment',
		'label' => __( 'Next Payment', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'payment_method',
		'label' => __( 'Payment Method', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_total',
		'label' => __( 'Order Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_subtotal',
		'label' => __( 'Order Subtotal', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'recurring_total',
		'label' => __( 'Recurring Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_method_id',
		'label' => __( 'Shipping Method ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_method',
		'label' => __( 'Shipping Method', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_cost',
		'label' => __( 'Shipping Cost', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sign_up_fee',
		'label' => __( 'Sign-up Fee', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'trial_length',
		'label' => __( 'Trial Length', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'trial_period',
		'label' => __( 'Trial Period', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'coupon',
		'label' => __( 'Coupon Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'related_orders',
		'label' => __( 'Related Orders', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_full_name',
		'label' => __( 'Billing: Full Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_first_name',
		'label' => __( 'Billing: First Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_last_name',
		'label' => __( 'Billing: Last Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_company',
		'label' => __( 'Billing: Company', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_address',
		'label' => __( 'Billing: Street Address (Full)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_address_1',
		'label' => __( 'Billing: Street Address 1', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_address_2',
		'label' => __( 'Billing: Street Address 2', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_city',
		'label' => __( 'Billing: City', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_postcode',
		'label' => __( 'Billing: ZIP Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_state',
		'label' => __( 'Billing: State (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_state_full',
		'label' => __( 'Billing: State', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_country',
		'label' => __( 'Billing: Country (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_country_full',
		'label' => __( 'Billing: Country', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_phone',
		'label' => __( 'Billing: Phone Number', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_email',
		'label' => __( 'Billing: E-mail Address', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_full_name',
		'label' => __( 'Shipping: Full Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_first_name',
		'label' => __( 'Shipping: First Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_last_name',
		'label' => __( 'Shipping: Last Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_company',
		'label' => __( 'Shipping: Company', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_address',
		'label' => __( 'Shipping: Street Address (Full)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_address_1',
		'label' => __( 'Shipping: Street Address 1', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_address_2',
		'label' => __( 'Shipping: Street Address 2', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_city',
		'label' => __( 'Shipping: City', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_postcode',
		'label' => __( 'Shipping: ZIP Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_state',
		'label' => __( 'Shipping: State (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_state_full',
		'label' => __( 'Shipping: State', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_country',
		'label' => __( 'Shipping: Country (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_country_full',
		'label' => __( 'Shipping: Country', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_product_id',
		'label' => __( 'Subscription Items: Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation_id',
		'label' => __( 'Subscription Items: Variation ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_sku',
		'label' => __( 'Subscription Items: Product SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_name',
		'label' => __( 'Subscription Items: Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation',
		'label' => __( 'Subscription Items: Product Variation', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_quantity',
		'label' => __( 'Subscription Items: Quantity', 'woocommerce-exporter' )
	);
/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( sprintf( WOO_CD_PREFIX . '_%s_fields', $export_type ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	if( $remember = woo_ce_get_option( $export_type . '_fields', array() ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['default'] = 1;
			if( !array_key_exists( $fields[$i]['name'], $remember ) )
				$fields[$i]['default'] = 0;
		}
	}

	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) )
					$output[$fields[$i]['name']] = 'on';
			}
			return $output;
			break;

		case 'full':
		default:
			$sorting = woo_ce_get_option( $export_type . '_sorting', array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				$fields[$i]['reset'] = $i;
				$fields[$i]['order'] = ( isset( $sorting[$fields[$i]['name']] ) ? $sorting[$fields[$i]['name']] : $i );
			}
			// Check if we are using PHP 5.3 and above
			if( version_compare( phpversion(), '5.3' ) >= 0 )
				usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_override_subscription_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'subscription_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_subscription_fields', 'woo_ce_override_subscription_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_subscription_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_subscription_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						break;

					case 'full':
						$output = $fields[$i];
						break;

				}
				$i = $size;
			}
		}
	}
	return $output;

}

// Adds custom Subscription columns to the Subscription fields list
function woo_ce_extend_subscription_fields( $fields = array() ) {

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			$attribute->attribute_label = trim( $attribute->attribute_label );
			if( empty( $attribute->attribute_label ) )
				$attribute->attribute_label = $attribute->attribute_name;
			$fields[] = array(
				'name' => sprintf( 'order_items_attribute_%s', $attribute->attribute_name ),
				'label' => sprintf( __( 'Subscription Items: %s', 'woocommerce-exporter' ), ucwords( $attribute->attribute_label ) ),
				'hover' => sprintf( apply_filters( 'woo_ce_extend_subscription_fields_attribute', '%s: %s (#%d)' ), __( 'Attribute', 'woocommerce-exporter' ), $attribute->attribute_name, $attribute->attribute_id )
			);
		}
		unset( $attributes, $attribute );
	}

/*
	// @mod - Commented out as it overrides the Order details
	// WooCommerce User Profile fields
	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( method_exists( 'WC_Admin_Profile', 'get_customer_meta_fields' ) ) {
			$show_fields = $admin_profile->get_customer_meta_fields();
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field ) {
					$fields[] = array(
						'name' => $key,
						'label' => sprintf( apply_filters( 'woo_ce_extend_subscription_fields_wc', '%s: %s' ), $fieldset['title'], esc_html( $field['label'] ) )
					);
				}
			}
			unset( $show_fields, $fieldset, $field );
		}
	}
*/

	// Custom Order fields
	$custom_orders = woo_ce_get_option( 'custom_orders', '' );
	if( !empty( $custom_orders ) ) {
		foreach( $custom_orders as $custom_order ) {
			if( !empty( $custom_order ) ) {
				$fields[] = array(
					'name' => $custom_order,
					'label' => woo_ce_clean_export_label( $custom_order )
				);
			}
		}
		unset( $custom_orders, $custom_order );
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => woo_ce_clean_export_label( $custom_user ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_subscription_fields_custom_user_hover', '%s: %s' ), __( 'Custom User', 'woocommerce-exporter' ), $custom_user )
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $fields;

}
add_filter( 'woo_ce_subscription_fields', 'woo_ce_extend_subscription_fields' );

// Returns a list of Subscription IDs
function woo_ce_get_subscriptions( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$subscription_status = false;
	$subscription_product = false;
	$orderby = 'start_date';
	$order = 'DESC';
	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : -1 );
		$offset = $args['offset'];
		$orderby = ( isset( $args['subscription_orderby'] ) ? $args['subscription_orderby'] : 'start_date' );
		$order = ( isset( $args['subscription_order'] ) ? $args['subscription_order'] : 'DESC' );
		$subscription_status = ( isset( $args['subscription_status'] ) ? $args['subscription_status'] : array() );
		$subscription_product = ( isset( $args['subscription_product'] ) ? $args['subscription_product'] : array() );
		$user_ids = ( isset( $args['subscription_customer'] ) ? $args['subscription_customer'] : false );
		$source = ( isset( $args['subscription_source'] ) ? $args['subscription_source'] : false );
	}

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	$output = array();

	// Check that WooCommerce Subscriptions exists
	if( !class_exists( 'WC_Subscriptions' ) || !class_exists( 'WC_Subscriptions_Manager' ) ) {
		$message = __( 'The WooCommerce Subscriptions class <code>WC_Subscriptions</code> or <code>WC_Subscriptions_Manager</code> could not be found, this is required to export Subscriptions.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
		woo_cd_admin_notice( $message, 'error' );
		return;
	} else {
		// Check that the get_all_users_subscriptions() function exists
		if( !method_exists( 'WC_Subscriptions_Manager', 'get_all_users_subscriptions' ) ) {
			$message = __( 'The WooCommerce Subscriptions method <code>WC_Subscriptions_Manager->get_all_users_subscriptions()</code> could not be found, this is required to export Subscriptions.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			woo_cd_admin_notice( $message, 'error' );
			return;
		}
	}

	if( class_exists( 'WC_Subscriptions' ) ) {
		if( function_exists( 'wcs_get_subscriptions' ) ) {

			$args = array(
				'subscriptions_per_page' => $limit_volume,
				'offset' => $offset,
				'orderby' => $orderby,
				'order' => $order
			);

			// Filter Subscriptions by Subscription Status
			if( $subscription_status ) {
				if( count( $subscription_status ) == 1 ) {
					$args['subscription_status'] = $subscription_status[0];
				} else {
					$args['subscription_status'] = $subscription_status;
				}
			}
			// Filter Subscriptions by Customer
			if( !empty( $user_ids ) ) {
				// Check if we're dealing with a string or list of users
				if( is_string( $user_ids ) )
				$user_ids = explode( ',', $user_ids );
			}

			// Allow other developers to bake in their own filters
			$args = apply_filters( 'woo_ce_get_subscriptions_args', $args );

			add_filter( 'woocommerce_got_subscriptions', 'woo_ce_woocommerce_got_subscriptions' );
			$subscription_ids = wcs_get_subscriptions( $args );
			remove_filter( 'woocommerce_got_subscriptions', 'woo_ce_woocommerce_got_subscriptions' );
			$subscriptions = array();

			if( !empty( $subscription_ids ) ) {
				foreach( $subscription_ids as $subscription_id ) {
					// Filter Subscriptions by Subscription Product
					if( $subscription_product ) {
						$order_id = wp_get_post_parent_id( $subscription_id );
						if( !empty( $order_id ) ) {
							$order_ids = woo_ce_get_product_assoc_order_ids( $subscription_product );
							if( in_array( $order_id, $order_ids ) == false ) {
								unset( $subscription_id );
							}
							unset( $order_ids );
						}
						unset( $order_id );
					}
					// Filter Subscriptions by Customer
					if( !empty( $user_ids ) ) {
						$user_id = get_post_meta( $subscription_id, '_customer_user', true );
						if( !in_array( $user_id, $user_ids ) ) {
							unset( $subscription_id );
						}
					}
					// Filter Subscriptions by Source
					if( !empty( $source ) ) {
						$order_id = wp_get_post_parent_id( $subscription_id );
						switch( $source ) {

							case 'customer':
								if( empty( $order_id ) )
									unset( $subscription_id );
								break;

							case 'manual':
								if( !empty( $order_id ) )
									unset( $subscription_id );
								break;

						}
						unset( $order_id );
					}

					if( isset( $subscription_id ) )
						$subscriptions[] = $subscription_id;

				}
				unset( $subscription_ids, $subscription_id );
			}

		}
	}
	return $subscriptions;

}

// Override wcs_get_subscriptions() to only return the Subscription Post ID
function woo_ce_woocommerce_got_subscriptions( $subscriptions ) {

	foreach( $subscriptions as $key => $subscription ) {
		$subscriptions[$key] = $key;
	}
	return $subscriptions;

}

function woo_ce_get_subscription_data( $subscription_id, $args = array(), $fields = array() ) {

	$subscription_statuses = woo_ce_get_subscription_statuses();

	$post = get_post( $subscription_id );
	$wcs_subscription = wcs_get_subscription( $subscription_id );
	$order_status = false;
	if( !empty( $wcs_subscription ) ) {
		// Check if an Order has been assigned to this Subscription
		if( !empty( $wcs_subscription->order ) )
			$order_status = $wcs_subscription->order->post_status;
	} else {
		$order = get_post( $post->post_parent );
		$order_status = $order->post_status;
		unset( $order );
	}

	$subscription = new stdClass();
	$subscription->order_id = $post->post_parent;
	$subscription->subscription_id = $subscription_id;
	if( function_exists( 'wcs_get_subscription_status_name' ) )
		$subscription->status = wcs_get_subscription_status_name( $wcs_subscription->get_status() );
	else
		$subscription->status = ( isset( $subscription_statuses[$post->post_status] ) ? $subscription_statuses[$post->post_status] : false );
	$subscription->user_id = get_post_meta( $subscription_id, '_customer_user', true );
	$subscription->user = woo_ce_get_username( $subscription->user_id );
	$subscription->order_status = ( !empty( $order_status ) ? woo_ce_format_order_status( $order_status ) : '-' );
	$subscription->coupon = woo_ce_get_order_assoc_coupon( $subscription->order_id );
	$subscription->payment_method = ( method_exists( $wcs_subscription, 'get_payment_method_to_display' ) ? $wcs_subscription->get_payment_method_to_display() : false );
	$subscription->recurring = sprintf( '%s %s', wcs_get_subscription_period_interval_strings( $wcs_subscription->billing_interval ), wcs_get_subscription_period_strings( 1, $wcs_subscription->billing_period ) );
	$subscription->start_date = ( ( 0 < $wcs_subscription->get_time( 'start' ) ) ? woo_ce_format_date( $wcs_subscription->get_date( 'start' ) ) : '-' );
	$subscription->end_date = ( ( 0 < $wcs_subscription->get_time( 'end' ) ) ? woo_ce_format_date( $wcs_subscription->get_date( 'end', 'site' ) ) : '-' );
	$subscription->trial_end_date = ( ( 0 < $wcs_subscription->get_time( 'trial_end' ) ) ? woo_ce_format_date( $wcs_subscription->get_date( 'trial_end' ) ) : '-' );
	$subscription->next_payment = ( ( 0 < $wcs_subscription->get_time( 'next_payment' ) ) ? woo_ce_format_date( $wcs_subscription->get_date( 'next_payment' ) ) : '-' );
	$subscription->last_payment = ( ( 0 < $wcs_subscription->get_time( 'last_payment' ) ) ? woo_ce_format_date( $wcs_subscription->get_date( 'next_payment' ) ) : '-' );
	$subscription->related_orders = ( method_exists( $wcs_subscription, 'get_related_orders' ) ? count( $wcs_subscription->get_related_orders() ) : 0 );

	add_filter( 'wc_price', 'woo_ce_filter_wc_price', 10, 3 );
	add_filter( 'formatted_woocommerce_price', 'woo_ce_formatted_woocommerce_price', 10, 5 );
	add_filter( 'woocommerce_currency_symbol', 'woo_ce_woocommerce_currency_symbol', 10, 2 );
	$subscription->recurring_total = $wcs_subscription->get_formatted_order_total();
	$subscription->recurring_total = str_replace( array( '<span class="amount">', '</span>' ), '', $subscription->recurring_total );
	remove_filter( 'formatted_woocommerce_price', 'woo_ce_formatted_woocommerce_price' );
	remove_filter( 'wc_price', 'woo_ce_filter_wc_price' );
	remove_filter( 'woocommerce_currency_symbol', 'woo_ce_woocommerce_currency_symbol' );

	$order = woo_ce_get_order_data( $subscription_id, 'order', false, false );
	$subscription = (object)array_merge( (array) $subscription, (array)$order );

/*
	$order = woo_ce_get_order_wc_data( $subscription['order_id'] );
	$order_item = woo_ce_get_subscription_order_item( $subscription['order_id'], $subscription['product_id'] );
	$product = woo_ce_get_subscription_product( $order, $order_item );
	$subscription['key'] = woo_ce_get_subscription_key( $subscription['order_id'], $subscription['product_id'] );
	$subscription['name'] = $order_item['name'];
	if( isset( $product->variation_data ) )
		$subscription['name'] = ( function_exists( 'woocommerce_get_formatted_variation' ) ? woocommerce_get_formatted_variation( $product->variation_data, true ) : $subscription['name'] );
	$subscription['variation_id'] = ( !empty( $order_item['variation_id'] ) ? $order_item['variation_id'] : '' );
	$subscription['quantity'] = ( !empty( $order_item['qty'] ) ? $order_item['qty'] : '' );
	$subscription['recurring'] = ( !empty( $subscription['interval'] ) ? sprintf( '%s %s', woo_ce_format_product_subscription_period_interval( $subscription['interval'] ), $subscription['period'] ) : '' );
	$subscription['order_status'] = woo_ce_format_order_status( $order->status );
	$subscription['post_status'] = ucwords( $order->post_status );
	$user = woo_ce_get_user_data( $subscription['user_id'] );
	$subscription['email'] = ( isset( $user->email ) ? $user->email : '' );
	unset( $user );
	$subscription['status'] = ( isset( $subscription_statuses[$subscription['status']] ) ? $subscription_statuses[$subscription['status']] : $subscription['status'] );
	$subscription['start_date'] = ( isset( $order_item['subscription_start_date'] ) ? date_i18n( woocommerce_date_format(), strtotime( $order_item['subscription_start_date'] ) ) : '' );
	$subscription['expiration'] = ( !empty( $subscription['expiry_date'] ) ? woo_ce_format_subscription_date( $subscription['expiry_date'] ) : __( 'Never', 'woocommerce-subscriptions' ) );
	$subscription['end_date'] = ( !empty( $order_item['subscription_expiry_date'] ) ? date_i18n( woocommerce_date_format(), strtotime( $order_item['subscription_expiry_date'] ) ) : __( 'Not yet ended', 'woocommerce-subscriptions' ) );
	$subscription['trial_end_date'] = ( !empty( $order_item['subscription_trial_expiry_date'] ) ? date_i18n( woocommerce_date_format(), strtotime( $order_item['subscription_trial_expiry_date'] ) ) : '-' );
	$subscription['last_payment'] = ( !empty( $subscription['last_payment_date'] ) ? woo_ce_format_subscription_date( $subscription['last_payment_date'] ) : '-' );
	$subscription['next_payment'] = woo_ce_get_subscription_next_payment( $subscription['key'], $subscription['user_id'] );
	$subscription['renewals'] = woo_ce_get_subscription_renewals( $subscription['order_id'] );
	if( method_exists( $product, 'get_sku' ) )
		$subscription['product_sku'] = $product->get_sku();
	$subscription['sign_up_fee'] = get_post_meta( $subscription['product_id'], '_subscription_sign_up_fee', true );
	$subscription['trial_length'] = get_post_meta( $subscription['product_id'], '_subscription_trial_length', true );
	$subscription['trial_period'] = get_post_meta( $subscription['product_id'], '_subscription_trial_period', true );
*/

	// Trim back the Subscription just to requested export fields
	if( !empty( $fields ) ) {
		$fields = array_merge( $fields, array( 'id', 'ID', 'post_parent', 'filter' ) );
		if( !empty( $subscription ) ) {
			foreach( $subscription as $key => $data ) {
				if( !in_array( $key, $fields ) )
					unset( $subscription->$key );
			}
		}
	}

	return $subscription;

}

// Populate Subscription details for export of 3rd party Plugins
function woo_ce_subscription_extend( $subscription ) {

/*
	// @mod - Commented out as it overrides the Order details
	// WooCommerce User Profile fields
	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( $show_fields = $admin_profile->get_customer_meta_fields() ) {
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field )
					$subscription->{$key} = esc_attr( get_user_meta( $subscription->user_id, $key, true ) );
			}
		}
		unset( $show_fields, $fieldset, $field );
	}
*/

	// Custom Order fields
	$custom_orders = woo_ce_get_option( 'custom_orders', '' );
	if( !empty( $custom_orders ) ) {
		foreach( $custom_orders as $custom_order ) {
			if( !empty( $custom_order ) && !isset( $subscription->{$custom_order} ) )
				$subscription->{$custom_order} = esc_attr( get_post_meta( $subscription->order_id, $custom_order, true ) );
		}
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) && !isset( $subscription->{$custom_user} ) ) {
				$subscription->{$custom_user} = woo_ce_format_custom_meta( get_user_meta( $subscription->user_id, $custom_user, true ) );
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $subscription;

}
add_filter( 'woo_ce_subscription', 'woo_ce_subscription_extend' );

function woo_ce_get_subscription_statuses() {

	if( function_exists( 'wcs_get_subscription_statuses' ) ) {
		$subscription_statuses = wcs_get_subscription_statuses();
	} else {
		$subscription_statuses = array(
			'active'    => __( 'Active', 'woocommerce-subscriptions' ),
			'cancelled' => __( 'Cancelled', 'woocommerce-subscriptions' ),
			'suspended' => __( 'Suspended', 'woocommerce-subscriptions' ),
			'expired'   => __( 'Expired', 'woocommerce-subscriptions' ),
			'pending'   => __( 'Pending', 'woocommerce-subscriptions' ),
			'failed'    => __( 'Failed', 'woocommerce-subscriptions' ),
			'on-hold'   => __( 'On-hold', 'woocommerce-subscriptions' ),
			'trash'     => __( 'Deleted', 'woocommerce-exporter' ),
		);
	}
	return apply_filters( 'woo_ce_subscription_statuses', $subscription_statuses );

}

function woo_ce_get_wc_subscriptions_version() {

	if( class_exists( 'WC_Subscriptions' ) ) {
		return WC_Subscriptions::$version;
	}

}

function woo_ce_get_subscription_order_item( $order_id = 0, $product_id = 0 ) {

	if( method_exists( 'WC_Subscriptions_Order', 'get_item_by_product_id' ) )
		$order_item = WC_Subscriptions_Order::get_item_by_product_id( $order_id, $product_id );
	return $order_item;

}

function woo_ce_get_subscription_product( $order = false, $order_item = false ) {

	// Check that get_product_from_item() exists within the WC_Order class
	if( method_exists( 'WC_Order', 'get_product_from_item' ) ) {
		// Check that $order and $order_item aren't empty
		if( !empty( $order ) && !empty( $order_item ) )
			$product = $order->get_product_from_item( $order_item );
	}
	return $product;

}

function woo_ce_format_subscription_date( $end_date = '' ) {

	// Date formatting is provided by WooCommerce Subscriptions
	$current_gmt_time = gmdate( 'U' );
	$end_date_timestamp = strtotime( $end_date );
	$time_diff = $current_gmt_time - $end_date_timestamp;
	if ( $time_diff > 0 && $time_diff < 7 * 24 * 60 * 60 )
		$end_date = sprintf( __( '%s ago', 'woocommerce-subscriptions' ), human_time_diff( $end_date_timestamp, $current_gmt_time ) );
	else
		$end_date = date_i18n( woocommerce_date_format(), $end_date_timestamp + get_option( 'gmt_offset' ) * 3600 );
	return $end_date;

}

function woo_ce_get_subscription_products() {

	$term_taxonomy = 'product_type';
	$args = array(
		'post_type' => array( 'product', 'product_variation' ),
		'posts_per_page' => -1,
		'fields' => 'ids',
		'suppress_filters' => false,
		'tax_query' => array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'slug',
				'terms' => array( 'subscription', 'variable-subscription' )
			)
		)
	);
	$products = array();
	$product_ids = new WP_Query( $args );
	if( $product_ids->posts ) {
		foreach( $product_ids->posts as $product_id )
			$products[] = $product_id;
	}
	return $products;

}

function woo_ce_format_product_subscription_period_interval( $interval ) {

	$output = $interval;
	if( !empty( $interval ) ) {
		switch( $interval ) {

			case '1':
				$output = __( 'per', 'woocommerce-exporter' );
				break;

			case '2':
				$output = __( 'every 2nd', 'woocommerce-exporter' );
				break;

			case '3':
				$output = __( 'every 3rd', 'woocommerce-exporter' );
				break;

			case '4':
				$output = __( 'every 4th', 'woocommerce-exporter' );
				break;

			case '5':
				$output = __( 'every 5th', 'woocommerce-exporter' );
				break;

			case '6':
				$output = __( 'every 6th', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_subscripion_length( $length, $period = '' ) {

	$output = $length;
	if( $length == '0' ) {
		$output = __( 'all time', 'woocommerce-exporter' );
	}
	return $output;

}

function woo_ce_format_product_subscription_limit( $limit ) {

	$output = $limit;
	if( !empty( $limit ) ) {
		$limit = strtolower( $limit );
		switch( $limit ) {

			case 'active':
				$output = __( 'Active Subscription', 'woocommerce-exporter' );
				break;

			case 'any':
				$output = __( 'Any Subscription', 'woocommerce-exporter' );
				break;

			case 'no':
				$output = __( 'Do not limit', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}
?>