<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_order_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_order_count' );
		if( $cached == false ) {
			$post_type = 'shop_order';
			$woocommerce_version = woo_get_woo_version();
			// Check if this is a WooCommerce 2.2+ instance (new Post Status)
			if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
				$post_status = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_ce_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
			else
				$post_status = apply_filters( 'woo_ce_order_post_status', woo_ce_post_statuses() );
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => 1,
				'post_status' => $post_status,
				'fields' => 'ids'
			);
			$count_query = new WP_Query( $args );
			$count = $count_query->found_posts;
			set_transient( WOO_CD_PREFIX . '_order_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Filter Orders by Order Date widget on Store Exporter screen
	function woo_ce_orders_filter_by_date() {

		$today = date( 'l' );
		$yesterday = date( 'l', strtotime( '-1 days' ) );
		$current_month = date( 'F' );
		$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
		$order_dates_variable = '';
		$order_dates_variable_length = '';
		$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
		$order_dates_from = woo_ce_get_order_first_date( $date_format );
		$order_dates_to = date( $date_format );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-date" /> <?php _e( 'Filter Orders by Order Date', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="order_dates_filter" value="today" /> <?php _e( 'Today', 'woocommerce-exporter' ); ?> (<?php echo $today; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="yesterday" /> <?php _e( 'Yesterday', 'woocommerce-exporter' ); ?> (<?php echo $yesterday; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_week" /> <?php _e( 'Current week', 'woocommerce-exporter' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_week" /> <?php _e( 'Last week', 'woocommerce-exporter' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_month" /> <?php _e( 'Current month', 'woocommerce-exporter' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_month" /> <?php _e( 'Last month', 'woocommerce-exporter' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
<!--
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_quarter" /> <?php _e( 'Last quarter', 'woocommerce-exporter' ); ?> (Nov. - Jan.)</label>
		</li>
-->
		<li>
			<label><input type="radio" name="order_dates_filter" value="variable" /> <?php _e( 'Variable date', 'woocommerce-exporter' ); ?></label>
			<div style="margin-top:0.2em;">
				<?php _e( 'Last', 'woocommerce-exporter' ); ?>
				<input type="text" name="order_dates_filter_variable" class="text code" size="4" maxlength="4" value="<?php echo $order_dates_variable; ?>" />
				<select name="order_dates_filter_variable_length" style="vertical-align:top;">
					<option value=""<?php selected( $order_dates_variable_length, '' ); ?>>&nbsp;</option>
					<option value="second"<?php selected( $order_dates_variable_length, 'second' ); ?>><?php _e( 'second(s)', 'woocommerce-exporter' ); ?></option>
					<option value="minute"<?php selected( $order_dates_variable_length, 'minute' ); ?>><?php _e( 'minute(s)', 'woocommerce-exporter' ); ?></option>
					<option value="hour"<?php selected( $order_dates_variable_length, 'hour' ); ?>><?php _e( 'hour(s)', 'woocommerce-exporter' ); ?></option>
					<option value="day"<?php selected( $order_dates_variable_length, 'day' ); ?>><?php _e( 'day(s)', 'woocommerce-exporter' ); ?></option>
					<option value="week"<?php selected( $order_dates_variable_length, 'week' ); ?>><?php _e( 'week(s)', 'woocommerce-exporter' ); ?></option>
					<option value="month"<?php selected( $order_dates_variable_length, 'month' ); ?>><?php _e( 'month(s)', 'woocommerce-exporter' ); ?></option>
					<option value="year"<?php selected( $order_dates_variable_length, 'year' ); ?>><?php _e( 'year(s)', 'woocommerce-exporter' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="manual" /> <?php _e( 'Fixed date', 'woocommerce-exporter' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="order_dates_from" name="order_dates_from" value="<?php echo esc_attr( $order_dates_from ); ?>" class="text code datepicker order_export" /> to <input type="text" size="10" maxlength="10" id="order_dates_to" name="order_dates_to" value="<?php echo esc_attr( $order_dates_to ); ?>" class="text code datepicker order_export" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first Order to today in the date format <code>DD/MM/YYYY</code>.', 'woocommerce-exporter' ); ?></p>
			</div>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_export" /> <?php _e( 'Since last export', 'woocommerce-exporter' ); ?></label>
			<p class="description"><?php _e( 'Export Orders which have not previously been included in an export. Decided by whether the <code>_woo_cd_exported</code> custom Post meta key has not been assigned to an Order.', 'woocommerce-exporter' ); ?></p>
		</li>
	</ul>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	// Returns date of first Order received, any status
	function woo_ce_get_order_first_date( $date_format = 'd/m/Y' ) {

		$output = date( $date_format, mktime( 0, 0, 0, date( 'n' ), 1 ) );

		$post_type = 'shop_order';
		$args = array(
			'post_type' => $post_type,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'numberposts' => 1,
			'post_status' => 'any'
		);
		$orders = get_posts( $args );
		if( !empty( $orders ) ) {
			$output = date( $date_format, strtotime( $orders[0]->post_date ) );
			unset( $orders );
		}
		return $output;

	}

	// HTML template for Filter Orders by Customer widget on Store Exporter screen
	function woo_ce_orders_filter_by_customer() {

		$users = woo_ce_get_export_type_count( 'users' );
		if( $users < 1000 )
			$customers = woo_ce_get_customers_list();

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-customer" /> <?php _e( 'Filter Orders by Customer', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-customer" class="separator">
	<ul>
		<li>
<?php if( $users < 1000 ) { ?>
			<select id="order_customer" data-placeholder="<?php _e( 'Choose a Customer...', 'woocommerce-exporter' ); ?>" name="order_filter_customer[]" multiple class="chzn-select" style="width:95%;">
				<option value=""><?php _e( 'Show all customers', 'woocommerce-exporter' ); ?></option>
	<?php if( !empty( $customers ) ) { ?>
		<?php foreach( $customers as $customer ) { ?>
				<option value="<?php echo $customer->ID; ?>"><?php printf( '%s (#%s - %s)', $customer->display_name, $customer->ID, $customer->user_email ); ?></option>
		<?php } ?>
	<?php } ?>
			</select>
<?php } else { ?>
			<input type="text" id="order_customer" name="order_filter_customer" size="20" class="text" />
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Customer (unique e-mail address) to be included in the export.', 'woocommerce-exporter' ); ?><?php if( $users > 1000 ) { echo ' ' . __( 'Enter a list of User ID\'s separated by a comma character.', 'woocommerce-exporter' ); } ?> <?php _e( 'Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-customer -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Billing Country widget on Store Exporter screen
	function woo_ce_orders_filter_by_billing_country() {

		$countries = woo_ce_allowed_countries();
		$types = woo_ce_get_option( 'order_billing_country', array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-billing_country"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Orders by Billing Country', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-billing_country" class="separator">
	<ul>
		<li>
<?php if( !empty( $countries ) ) { ?>
			<select id="order_billing_country" data-placeholder="<?php _e( 'Choose a Billing Country...', 'woocommerce-exporter' ); ?>" name="order_filter_billing_country[]" multiple class="chzn-select" style="width:95%;">
				<option value=""><?php _e( 'Show all Countries', 'woocommerce-exporter' ); ?></option>
	<?php if( $countries ) { ?>
		<?php foreach( $countries as $country_prefix => $country ) { ?>
				<option value="<?php echo $country_prefix; ?>"<?php echo ( is_array( $types ) ? selected( in_array( $country_prefix, $types, false ), true ) : '' ); ?>><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
		<?php } ?>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Countries were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Billing Country to be included in the export. Default is to include all Countries.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-customer -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Shipping Country widget on Store Exporter screen
	function woo_ce_orders_filter_by_shipping_country() {

		$countries = woo_ce_allowed_countries();
		$types = woo_ce_get_option( 'order_shipping_country', array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-shipping_country"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Orders by Shipping Country', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-shipping_country" class="separator">
	<ul>
		<li>
<?php if( !empty( $countries ) ) { ?>
			<select id="order_shipping_country" data-placeholder="<?php _e( 'Choose a Shipping Country...', 'woocommerce-exporter' ); ?>" name="order_filter_shipping_country" multiple class="chzn-select" style="width:95%;">
				<option value=""><?php _e( 'Show all Countries', 'woocommerce-exporter' ); ?></option>
	<?php foreach( $countries as $country_prefix => $country ) { ?>
				<option value="<?php echo $country_prefix; ?>"<?php echo ( is_array( $types ) ? selected( in_array( $country_prefix, $types, false ), true ) : '' ); ?>><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Countries were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Shipping Country to be included in the export. Default is to include all Countries.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-customer -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by User Role widget on Store Exporter screen
	function woo_ce_orders_filter_by_user_role() {

		$user_roles = woo_ce_get_user_roles();
		$types = woo_ce_get_option( 'order_user_roles', array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-user_role"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Orders by User Role', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-user_role" class="separator">
	<ul>
		<li>
<?php if( !empty( $user_roles ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a User Role...', 'woocommerce-exporter' ); ?>" name="order_filter_user_role[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $user_roles as $key => $user_role ) { ?>
				<option value="<?php echo $key; ?>"<?php echo ( is_array( $types ) ? selected( in_array( $key, $types, false ), true ) : '' ); ?>><?php echo ucfirst( $user_role['name'] ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No User Roles were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the User Roles you want to filter exported Orders by. Default is to include all User Role options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-user_role -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Order ID widget on Store Exporter screen
	function woo_ce_orders_filter_by_order_id() {

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-id" /> <?php _e( 'Filter Orders by Order ID', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-id" class="separator">
	<ul>
		<li>
			<label for="order_filter_id"><?php _e( 'Order ID', 'woocommerce-exporter' ); ?></label>:<br />
			<input type="text" id="order_filter_id" name="order_filter_id" placeholder="1000,1001,1002" value="" class="text code" style="width:95%;" />
		</li>
	</ul>
	<p class="description"><?php _e( 'Enter the Order ID\'s you want to filter exported Orders by. Multiple Order ID\'s can be entered separated by the \',\' (comma) character. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-user_role -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Coupon Code widget on Store Exporter screen
	function woo_ce_orders_filter_by_coupon() {

		$args = array(
			'coupon_orderby' => 'ID',
			'coupon_order' => 'DESC'
		);
		$coupons = woo_ce_get_coupons( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-coupon" /> <?php _e( 'Filter Orders by Coupon Code', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-coupon" class="separator">
	<ul>
		<li>
<?php if( !empty( $coupons ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Coupon...', 'woocommerce-exporter' ); ?>" name="order_filter_coupon[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $coupons as $coupon ) { ?>
				<option value="<?php echo $coupon; ?>"<?php disabled( 0, woo_ce_get_coupon_code_usage( get_the_title( $coupon ) ) ); ?>><?php echo get_the_title( $coupon ); ?> (<?php echo woo_ce_get_coupon_code_usage( get_the_title( $coupon ) ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Coupons were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Coupon Codes you want to filter exported Orders by. Default is to include all Orders with and without assigned Coupon Codes.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-coupon -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Payment Gateway widget on Store Exporter screen
	function woo_ce_orders_filter_by_payment_gateway() {

		$payment_gateways = woo_ce_get_order_payment_gateways();

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-payment_gateway" /> <?php _e( 'Filter Orders by Payment Gateway', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-payment_gateway" class="separator">
	<ul>
		<li>
<?php if( !empty( $payment_gateways ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Payment Gateway...', 'woocommerce-exporter' ); ?>" name="order_filter_payment_gateway[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $payment_gateways as $payment_gateway ) { ?>
				<option value="<?php echo $payment_gateway->id; ?>"<?php disabled( 0, woo_ce_get_order_payment_gateway_usage( $payment_gateway->id ) ); ?>><?php echo woo_ce_format_order_payment_gateway( $payment_gateway->id ); ?> (<?php echo woo_ce_get_order_payment_gateway_usage( $payment_gateway->id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Payment Gateways were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Payment Gateways you want to filter exported Orders by. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-payment_gateway -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Payment Gateway widget on Store Exporter screen
	function woo_ce_orders_filter_by_shipping_method() {

		$shipping_methods = woo_ce_get_order_shipping_methods();

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-shipping_method" /> <?php _e( 'Filter Orders by Shipping Method', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-shipping_method" class="separator">
	<ul>
		<li>
<?php if( !empty( $shipping_methods ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Shipping Method...', 'woocommerce-exporter' ); ?>" name="order_filter_shipping_method[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $shipping_methods as $shipping_method ) { ?>
				<option value="<?php echo $shipping_method->id; ?>"><?php echo woo_ce_format_order_shipping_method( $shipping_method->id ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Shipping Methods were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Shipping Methods you want to filter exported Orders by. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-shipping_method -->
<?php
		ob_end_flush();

	}

	// HTML template for Order Items Formatting on Store Exporter screen
	function woo_ce_orders_items_formatting() {

		$order_items_formatting = woo_ce_get_option( 'order_items_formatting', 'unique' );

		ob_start(); ?>
<tr class="export-options order-options">
	<th><label for="order_items"><?php _e( 'Order items formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<ul>
			<li>
				<label><input type="radio" name="order_items" value="combined"<?php checked( $order_items_formatting, 'combined' ); ?> />&nbsp;<?php _e( 'Place Order Items within a grouped single Order row', 'woocommerce-exporter' ); ?></label>
				<p class="description"><?php _e( 'For example: <code>Order Items: SKU</code> cell might contain <code>SPECK-IPHONE|INCASE-NANO|-</code> for 3 Order items within an Order', 'woocommerce-exporter' ); ?></p>
			</li>
			<li>
				<label><input type="radio" name="order_items" value="unique"<?php checked( $order_items_formatting, 'unique' ); ?> />&nbsp;<?php _e( 'Place Order Items on individual cells within a single Order row', 'woocommerce-exporter' ); ?></label>
				<p class="description"><?php _e( 'For example: <code>Order Items: SKU</code> would become <code>Order Item #1: SKU</code> with <codeSPECK-IPHONE</code> for the first Order item within an Order', 'woocommerce-exporter' ); ?></p>
			</li>
			<li>
				<label><input type="radio" name="order_items" value="individual"<?php checked( $order_items_formatting, 'individual' ); ?> />&nbsp;<?php _e( 'Place each Order Item within their own Order row', 'woocommerce-exporter' ); ?></label>
				<p class="description"><?php _e( 'For example: An Order with 3 Order items will display a single Order item on each row', 'woocommerce-exporter' ); ?></p>
			</li>
		</ul>
		<p class="description"><?php _e( 'Choose how you would like Order Items to be presented within Orders.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	// HTML template for Max Order Items widget on Store Exporter screen
	function woo_ce_orders_max_order_items() {

		$max_size = woo_ce_get_option( 'max_order_items', 10 );

		ob_start(); ?>
<tr id="max_order_items_option" class="export-options order-options">
	<th>
		<label for="max_order_items"><?php _e( 'Max unique Order items', 'woocommerce-exporter' ); ?>: </label>
	</th>
	<td>
		<input type="text" id="max_order_items" name="max_order_items" size="3" class="text" value="<?php echo esc_attr( $max_size ); ?>" />
		<p class="description"><?php _e( 'Manage the number of Order Item colums displayed when the \'Place Order Items on individual cells within a single Order row\' Order items formatting option is selected.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	// HTML template for Order Items Types on Store Exporter screen
	function woo_ce_orders_items_types() {

		$types = woo_ce_get_order_items_types();
		$order_items_types = woo_ce_get_option( 'order_items_types', array() );

		// Default to Line Item if not set
		if( empty( $order_items_types ) ) {
			$order_items_types = array( 'line_item' );
			// Check if WooCommerce Checkout Add-ons is activated
			if( function_exists( 'init_woocommerce_checkout_add_ons' ) )
				$order_items_types = array( 'line_item', 'fee' );
		}

		ob_start(); ?>
<tr class="export-options order-options">
	<th><label><?php _e( 'Order item types', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<ul>
<?php foreach( $types as $key => $type ) { ?>
			<li><label><input type="checkbox" name="order_items_types[<?php echo $key; ?>]" value="<?php echo $key; ?>"<?php checked( in_array( $key, $order_items_types ), true ); ?> /> <?php echo ucfirst( $type ); ?></label></li>
<?php } ?>
		</ul>
		<p class="description"><?php _e( 'Choose what Order Item types are included within the Orders export. Default is to include all Order Item types.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	// HTML template for Add note for exported Order flag widget on Store Exporter screen
	function woo_ce_orders_flag_notes() {

		$order_flag_notes = woo_ce_get_option( 'order_flag_notes', 0 );

		ob_start(); ?>
<tr class="export-options order-options">
	<th><label><?php _e( 'Exported Order notes', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="order_flag_notes" value="0"<?php checked( $order_flag_notes, 0 ); ?>>&nbsp;<?php _e( 'Do not add private Order notes', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="order_flag_notes" value="1"<?php checked( $order_flag_notes, 1 ); ?>>&nbsp;<?php _e( 'Add private Order notes', 'woocommerce-exporter' ); ?></label>
		<p class="description"><?php _e( 'Choose whether Order notes - e.g. Order was exported successfully or Order export flag was cleared - are assigned to exported Orders when using the Since last export Order Filter. Default is not to add Order notes.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Order Status widget on Store Exporter screen
	function woo_ce_orders_filter_by_status() {

		$order_statuses = woo_ce_get_order_statuses();
		$types = woo_ce_get_option( 'order_status', array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-status"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Orders by Order Status', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $order_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Order Status...', 'woocommerce-exporter' ); ?>" name="order_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $order_statuses as $order_status ) { ?>
				<option value="<?php echo $order_status->slug; ?>"<?php echo ( is_array( $types ) ? selected( in_array( $order_status->slug, $types, false ), true ) : '' ); ?><?php disabled( 0, $order_status->count ); ?>><?php echo ucfirst( $order_status->name ); ?> (<?php echo $order_status->count; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Order Status\'s were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Product widget on Store Exporter screen
	function woo_ce_orders_filter_by_product() {

/*
		// @mod - Removed as the meta_query args are returning empty results
		$product_types = woo_ce_get_product_types();
		// Remove the Product Variation type
		unset( $product_types['variation'] );
		$args = array(
			'product_type' => array_keys( $product_types )
		);
*/
		$args = array();
		$products = woo_ce_get_products( $args );
		add_filter( 'the_title', 'woo_ce_get_product_title_sku', 10, 2 );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-product" /> <?php _e( 'Filter Orders by Product', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-product" class="separator">
	<ul>
		<li>
<?php if( wp_script_is( 'wc-enhanced-select', 'enqueued' ) ) { ?>
			<p><input type="hidden" id="order_filter_product" name="order_filter_product[]" class="multiselect wc-product-search" data-multiple="true" style="width:95;" data-placeholder="<?php _e( 'Search for a Product&hellip;', 'woocommerce-exporter' ); ?>" data-action="woocommerce_json_search_products_and_variations" /></p>
<?php } else { ?>
	<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product...', 'woocommerce-exporter' ); ?>" name="order_filter_product[]" multiple class="chzn-select" style="width:95%;">
		<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>"><?php echo woo_ce_format_post_title( get_the_title( $product ) ); ?></option>
		<?php } ?>
			</select>
	<?php } else { ?>
			<?php _e( 'No Products were found.', 'woocommerce-exporter' ); ?></li>
	<?php } ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Products you want to filter exported Orders by. Default is to include all Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-product -->
<?php
		ob_end_flush();
		remove_filter( 'the_title', 'woo_ce_get_product_title_sku' );

	}

	// HTML template for Filter Orders by Product Category widget on Store Exporter screen
	function woo_ce_orders_filter_by_product_category() {

		$args = array(
			'hide_empty' => 1
		);
		$product_categories = woo_ce_get_product_categories( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-category" /> <?php _e( 'Filter Orders by Product Category', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-category" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_categories ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woocommerce-exporter' ); ?>" name="order_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
				<option value="<?php echo $product_category->term_id; ?>"><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Categories were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Orders by. Product Categories not assigned to Products are hidden from view. Default is to include all Product Categories.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-category -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Product Tag widget on Store Exporter screen
	function woo_ce_orders_filter_by_product_tag() {

		$args = array(
			'hide_empty' => 1
		);
		$product_tags = woo_ce_get_product_tags( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-tag" /> <?php _e( 'Filter Orders by Product Tag', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-tag" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_tags ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woocommerce-exporter' ); ?>" name="order_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
				<option value="<?php echo $product_tag->term_id; ?>"><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Tags were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Orders by. Product Tags not assigned to Products are hidden from view. Default is to include all Product Tags.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-tag -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Orders by Brand widget on Store Exporter screen
	function woo_ce_orders_filter_by_product_brand() {

		// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
		// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
		if( woo_ce_detect_product_brands() == false )
			return;

		$args = array(
			'hide_empty' => 1
		);
		$product_brands = woo_ce_get_product_brands( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-brand" /> <?php _e( 'Filter Orders by Product Brand', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-brand" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_brands ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Brand...', 'woocommerce-exporter' ); ?>" name="order_filter_brand[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_brands as $product_brand ) { ?>
				<option value="<?php echo $product_brand->term_id; ?>"><?php echo woo_ce_format_product_category_label( $product_brand->name, $product_brand->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_brand->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Brands were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Brands you want to filter exported Orders by. Product Brands not assigned to Products are hidden from view. Default is to include all Product Brands.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-brand -->
<?php
		ob_end_flush();

	}

	// HTML template for Order Sorting widget on Store Exporter screen
	function woo_ce_order_sorting() {

		$orderby = woo_ce_get_option( 'order_orderby', 'ID' );
		$order = woo_ce_get_option( 'order_order', 'ASC' );

		ob_start(); ?>
<p><label><?php _e( 'Order Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="order_orderby">
		<option value="ID"<?php selected( 'ID', $orderby ); ?>><?php _e( 'Order ID', 'woocommerce-exporter' ); ?></option>
		<option value="title"<?php selected( 'title', $orderby ); ?>><?php _e( 'Order Name', 'woocommerce-exporter' ); ?></option>
		<option value="date"<?php selected( 'date', $orderby ); ?>><?php _e( 'Date Created', 'woocommerce-exporter' ); ?></option>
		<option value="modified"<?php selected( 'modified', $orderby ); ?>><?php _e( 'Date Modified', 'woocommerce-exporter' ); ?></option>
		<option value="rand"<?php selected( 'rand', $orderby ); ?>><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="order_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Orders within the exported file. By default this is set to export Orders by Product ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for jump link to Custom Order Fields within Order Options on Store Exporter screen
	function woo_ce_orders_custom_fields_link() {

		ob_start(); ?>
<div id="export-orders-custom-fields-link">
	<p><a href="#export-orders-custom-fields"><?php _e( 'Manage Custom Order Fields', 'woocommerce-exporter' ); ?></a></p>
</div>
<!-- #export-orders-custom-fields-link -->
<?php
		ob_end_flush();

	}

	// HTML template for Custom Orders widget on Store Exporter screen
	function woo_ce_orders_custom_fields() {

		if( $custom_orders = woo_ce_get_option( 'custom_orders', '' ) )
			$custom_orders = implode( "\n", $custom_orders );
		if( $custom_order_items = woo_ce_get_option( 'custom_order_items', '' ) )
			$custom_order_items = implode( "\n", $custom_order_items );
		if( $custom_order_products = woo_ce_get_option( 'custom_order_products', '' ) )
			$custom_order_products = implode( "\n", $custom_order_products );

		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

		ob_start(); ?>
<form method="post" id="export-orders-custom-fields" class="export-options order-options">
	<div id="poststuff">

		<div class="postbox" id="export-options">
			<h3 class="hndle"><?php _e( 'Custom Order Fields', 'woocommerce-exporter' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom Order, Order Item or Product meta associated to Order Items in the Export Orders table above fill the appropriate text box then click <em>Save Custom Fields</em>.', 'woocommerce-exporter' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'Order meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_orders" rows="5" cols="70"><?php echo esc_textarea( $custom_orders ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Order meta in your export file by adding each custom Order meta name to a new line above. This is case sensitive.<br />For example: <code>Customer UA</code> (new line) <code>Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e( 'Order Item meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_order_items" rows="5" cols="70"><?php echo esc_textarea( $custom_order_items ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Order Item meta in your export file by adding each custom Order Item meta name to a new line above. This is case sensitive.<br />For example: <code>Personalized Message</code> (new line) <code>_line_total</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e( 'Order Item Product meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_order_products" rows="5" cols="70"><?php echo esc_textarea( $custom_order_products ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Order Item Product meta in your export file by adding each custom Product meta name associated to Order Items to a new line above. This is case sensitive.<br />For example: <code>_sold_individually</code> (new line) <code>_manage_stock</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'woo_ce_orders_custom_fields' ); ?>

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Custom Fields', 'woocommerce-exporter' ); ?>" class="button" />
				</p>
				<p class="description"><?php printf( __( 'For more information on custom Order and Order Item meta consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-orders-custom-fields -->
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of Order export columns
function woo_ce_get_order_fields( $format = 'full' ) {

	$export_type = 'order';

	$fields = array();
	$fields[] = array(
		'name' => 'purchase_id',
		'label' => __( 'Order ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_id',
		'label' => __( 'Post ID', 'woocommerce-exporter' )
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
		'name' => 'order_currency',
		'label' => __( 'Order Currency', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_discount',
		'label' => __( 'Order Discount', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'coupon_code',
		'label' => __( 'Coupon Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'coupon_description',
		'label' => __( 'Coupon Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_total_tax',
		'label' => __( 'Order Total Tax', 'woocommerce-exporter' )
	);
/*
	$fields[] = array(
		'name' => 'order_incl_tax',
		'label' => __( 'Order Incl. Tax', 'woocommerce-exporter' )
	);
*/
	$fields[] = array(
		'name' => 'order_subtotal_excl_tax',
		'label' => __( 'Order Subtotal Excl. Tax', 'woocommerce-exporter' )
	);
/*
	$fields[] = array(
		'name' => 'order_tax_rate',
		'label' => __( 'Order Tax Rate', 'woocommerce-exporter' )
	);
*/
	$fields[] = array(
		'name' => 'order_sales_tax',
		'label' => __( 'Sales Tax Total', 'woocommerce-exporter' )
	);
	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			$fields[] = array(
				'name' => sprintf( 'purchase_total_tax_rate_%d', $tax_rate['rate_id'] ),
				'label' => sprintf( __( 'Order Total Tax: %s', 'woocommerce-exporter' ), $tax_rate['label'] )
			);
		}
	}
	$fields[] = array(
		'name' => 'order_shipping_tax',
		'label' => __( 'Shipping Tax Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_excl_tax',
		'label' => __( 'Shipping Excl. Tax', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'refund_total',
		'label' => __( 'Refund Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'refund_date',
		'label' => __( 'Refund Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_tax_percentage',
		'label' => __( 'Order Tax Percentage', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'payment_gateway_id',
		'label' => __( 'Payment Gateway ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'payment_gateway',
		'label' => __( 'Payment Gateway', 'woocommerce-exporter' )
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
		'name' => 'shipping_weight',
		'label' => __( 'Shipping Weight', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'payment_status',
		'label' => __( 'Order Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_status',
		'label' => __( 'Post Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_key',
		'label' => __( 'Order Key', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_date',
		'label' => __( 'Order Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_time',
		'label' => __( 'Order Time', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'customer_message',
		'label' => __( 'Customer Message', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'customer_notes',
		'label' => __( 'Customer Notes', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_notes',
		'label' => __( 'Order Notes', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'total_quantity',
		'label' => __( 'Total Quantity', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'total_order_items',
		'label' => __( 'Total Order Items', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Username', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_role',
		'label' => __( 'User Role', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'ip_address',
		'label' => __( 'Checkout IP Address', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'browser_agent',
		'label' => __( 'Checkout Browser Agent', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'has_downloads',
		'label' => __( 'Has Downloads', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'has_downloaded',
		'label' => __( 'Has Downloaded', 'woocommerce-exporter' )
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
		'name' => 'order_items_id',
		'label' => __( 'Order Items: ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_product_id',
		'label' => __( 'Order Items: Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation_id',
		'label' => __( 'Order Items: Variation ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_sku',
		'label' => __( 'Order Items: SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_name',
		'label' => __( 'Order Items: Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation',
		'label' => __( 'Order Items: Product Variation', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_description',
		'label' => __( 'Order Items: Product Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_excerpt',
		'label' => __( 'Order Items: Product Excerpt', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_tax_class',
		'label' => __( 'Order Items: Tax Class', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_quantity',
		'label' => __( 'Order Items: Quantity', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_total',
		'label' => __( 'Order Items: Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_subtotal',
		'label' => __( 'Order Items: Subtotal', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_rrp',
		'label' => __( 'Order Items: RRP', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_stock',
		'label' => __( 'Order Items: Stock', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_tax',
		'label' => __( 'Order Items: Tax', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_tax_subtotal',
		'label' => __( 'Order Items: Tax Subtotal', 'woocommerce-exporter' )
	);
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			$fields[] = array(
				'name' => sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] ),
				'label' => sprintf( __( 'Order Items: Tax Rate - %s', 'woocommerce-exporter' ), $tax_rate['label'] )
			);
		}
	}
	unset( $tax_rates, $tax_rate );
	$fields[] = array(
		'name' => 'order_items_refund_subtotal',
		'label' => __( 'Order Items: Refund Subtotal', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_refund_quantity',
		'label' => __( 'Order Items: Refund Quantity', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_type',
		'label' => __( 'Order Items: Type', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_type_id',
		'label' => __( 'Order Items: Type ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_category',
		'label' => __( 'Order Items: Category', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_tag',
		'label' => __( 'Order Items: Tag', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_total_sales',
		'label' => __( 'Order Items: Total Sales', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_weight',
		'label' => __( 'Order Items: Weight', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_height',
		'label' => __( 'Order Items: Height', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_width',
		'label' => __( 'Order Items: Width', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_length',
		'label' => __( 'Order Items: Length', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_total_weight',
		'label' => __( 'Order Items: Total Weight', 'woocommerce-exporter' )
	);

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional Order Item fields
	$fields = apply_filters( sprintf( WOO_CD_PREFIX . '_%s_fields', 'order_items' ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

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

	$remember = woo_ce_get_option( $export_type . '_fields', array() );
	$hidden = woo_ce_get_option( $export_type . '_hidden', array() );
	if( !empty( $remember ) ) {
		$remember = maybe_unserialize( $remember );
		$hidden = maybe_unserialize( $hidden );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['hidden'] = ( isset( $fields[$i]['hidden'] ) ? $fields[$i]['hidden'] : 0 );
			$fields[$i]['default'] = 1;
			if( isset( $fields[$i]['name'] ) ) {
				// If not found turn off default
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
				// Remove the field from exports if found
				if( array_key_exists( $fields[$i]['name'], $hidden ) )
					$fields[$i]['hidden'] = 1;
			}
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
				if( !isset( $fields[$i]['name'] ) ) {
					unset( $fields[$i] );
					continue;
				}
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

// Check if we should override field labels from the Field Editor
function woo_ce_override_order_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'order_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_order_fields', 'woo_ce_override_order_field_labels', 11 );
add_filter( 'woo_ce_order_items_fields', 'woo_ce_override_order_field_labels', 11 );

// Adds custom Order columns to the Order fields list
function woo_ce_extend_order_fields( $fields = array() ) {

	// Product Add-ons - http://www.woothemes.com/
	if( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) {
		$product_addons = woo_ce_get_product_addons();
		if( !empty( $product_addons ) ) {
			foreach( $product_addons as $product_addon ) {
				if( !empty( $product_addon ) ) {
					$fields[] = array(
						'name' => sprintf( 'order_items_product_addon_%s', $product_addon->post_name ),
						'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucfirst( $product_addon->post_title ) ),
						'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_product_addons', '%s: %s' ), __( 'Product Add-ons', 'woocommerce-exporter' ), $product_addon->form_title )
					);
				}
			}
		}
		unset( $product_addons, $product_addon );
	}

	// WooCommerce Print Invoice & Delivery Note - https://wordpress.org/plugins/woocommerce-delivery-notes/
	if( class_exists( 'WooCommerce_Delivery_Notes' ) ) {
		$fields[] = array(
			'name' => 'invoice_number',
			'label' => __( 'Invoice Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Print Invoice & Delivery Note', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'invoice_date',
			'label' => __( 'Invoice Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Print Invoice & Delivery Note', 'woocommerce-exporter' )
		);
	}

	// WooCommerce PDF Invoices & Packing Slips - http://www.wpovernight.com
	if( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
		$fields[] = array(
			'name' => 'pdf_invoice_number',
			'label' => __( 'PDF Invoice Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'pdf_invoice_date',
			'label' => __( 'PDF Invoice Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( class_exists( 'WooCommerce_HearAboutUs' ) ) {
		$fields[] = array(
			'name' => 'hear_about_us',
			'label' => __( 'Source', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Hear About Us', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Uploads - https://wpfortune.com/shop/plugins/woocommerce-uploads/
	if( class_exists( 'WPF_Uploads' ) ) {
		$fields[] = array(
			'name' => 'uploaded_files',
			'label' => __( 'Uploaded Files', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Uploads', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'uploaded_files_thumbnail',
			'label' => __( 'Uploaded Files (Thumbnail)', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Uploads', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Checkout Manager - http://wordpress.org/plugins/woocommerce-checkout-manager/
	// WooCommerce Checkout Manager Pro - http://www.trottyzone.com/product/woocommerce-checkout-manager-pro
	if( function_exists( 'wccs_install' ) || function_exists( 'wccs_install_pro' ) ) {

		// Checkout Manager Pro stores its settings in mulitple suffixed wccs_settings WordPress Options

		// Load generic settings
		$options = get_option( 'wccs_settings' );
		if( isset( $options['buttons'] ) ) {
			$buttons = $options['buttons'];
			if( !empty( $buttons ) ) {
				$header = ( $buttons[0]['type'] == 'heading' ? $buttons[0]['label'] : __( 'Additional', 'woocommerce-exporter' ) );
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					$label = ( !empty( $button['label'] ) ? $button['label'] : $button['cow'] );
					$fields[] = array(
						'name' => $button['cow'],
						'label' => ( !empty( $header ) ? sprintf( apply_filters( 'woo_ce_extend_order_fields_wccs', '%s: %s' ), ucfirst( $header ), ucfirst( $label ) ) : ucfirst( $label ) ),
						'hover' => ( function_exists( 'wccs_install_pro' ) ? __( 'WooCommerce Checkout Manager Pro', 'woocommerce-exporter' ) : __( 'WooCommerce Checkout Manager', 'woocommerce-exporter' ) )
					);
				}
				unset( $buttons, $button, $header, $label );
			}
		}
		unset( $options );
		// Load Shipping settings
		$options = get_option( 'wccs_settings2' );
		if( isset( $options['shipping_buttons'] ) ) {
			$buttons = $options['shipping_buttons'];
			if( !empty( $buttons ) ) {
				$header = ( $buttons[0]['type'] == 'heading' ? $buttons[0]['label'] : __( 'Shipping', 'woocommerce-exporter' ) );
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					$wccs_field_duplicate = false;
					// Check if this isn't a duplicate Checkout Manager Pro field
					foreach( $fields as $field ) {
						if( isset( $field['name'] ) && $field['name'] == sprintf( 'shipping_%s', $button['cow'] ) ) {
							// Duplicate exists
							$wccs_field_duplicate = true;
							break;
						}
					}
					// If it's not a duplicate go ahead and add it to the list
					if( $wccs_field_duplicate !== true ) {
						$label = ( !empty( $button['label'] ) ? $button['label'] : $button['cow'] );
						$fields[] = array(
							'name' => sprintf( 'shipping_%s', $button['cow'] ),
							'label' => ( !empty( $header ) ? sprintf( apply_filters( 'woo_ce_extend_order_fields_wccs', '%s: %s' ), ucfirst( $header ), ucfirst( $label ) ) : ucfirst( $label ) ),
							'hover' => ( function_exists( 'wccs_install_pro' ) ? __( 'WooCommerce Checkout Manager Pro', 'woocommerce-exporter' ) : __( 'WooCommerce Checkout Manager', 'woocommerce-exporter' ) )
						);
					}
					unset( $wccs_field_duplicate );
				}
				unset( $buttons, $button, $header, $label );
			}
		}
		unset( $options );
		// Load Billing settings
		$options = get_option( 'wccs_settings3' );
		if( isset( $options['billing_buttons'] ) ) {
			$buttons = $options['billing_buttons'];
			if( !empty( $buttons ) ) {
				$header = ( $buttons[0]['type'] == 'heading' ? $buttons[0]['label'] : __( 'Billing', 'woocommerce-exporter' ) );
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					$wccs_field_duplicate = false;
					// Check if this isn't a duplicate Checkout Manager Pro field
					foreach( $fields as $field ) {
						if( isset( $field['name'] ) && $field['name'] == sprintf( 'billing_%s', $button['cow'] ) ) {
							// Duplicate exists
							$wccs_field_duplicate = true;
							break;
						}
					}
					// If it's not a duplicate go ahead and add it to the list
					if( $wccs_field_duplicate !== true ) {
						$label = ( !empty( $button['label'] ) ? $button['label'] : $button['cow'] );
						$fields[] = array(
							'name' => sprintf( 'billing_%s', $button['cow'] ),
							'label' => ( !empty( $header ) ? sprintf( apply_filters( 'woo_ce_extend_order_fields_wccs', '%s: %s' ), ucfirst( $header ), ucfirst( $label ) ) : ucfirst( $label ) ),
							'hover' => ( function_exists( 'wccs_install_pro' ) ? __( 'WooCommerce Checkout Manager Pro', 'woocommerce-exporter' ) : __( 'WooCommerce Checkout Manager', 'woocommerce-exporter' ) )
						);
					}
					unset( $wccs_field_duplicate );
				}
				unset( $buttons, $button, $header, $label );
			}
		}
		unset( $options );
	}

	// Poor Guys Swiss Knife - http://wordpress.org/plugins/woocommerce-poor-guys-swiss-knife/
	if( function_exists( 'wcpgsk_init' ) ) {

		$options = get_option( 'wcpgsk_settings' );
		$billing_fields = ( isset( $options['woofields']['billing'] ) ? $options['woofields']['billing'] : array() );
		$shipping_fields = ( isset( $options['woofields']['shipping'] ) ? $options['woofields']['shipping'] : array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				$fields[] = array(
					'name' => $key,
					'label' => $options['woofields'][sprintf( 'label_%s', $key )],
					'hover' => __( 'Poor Guys Swiss Knife', 'woocommerce-exporter' )
				);
			}
			unset( $billing_fields, $billing_field );
		}

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				$fields[] = array(
					'name' => $key,
					'label' => $options['woofields'][sprintf( 'label_%s', $key )],
					'hover' => __( 'Poor Guys Swiss Knife', 'woocommerce-exporter' )
				);
			}
			unset( $shipping_fields, $shipping_field );
		}

		unset( $options );
	}

	// Checkout Field Editor - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_init_checkout_field_editor' ) ) {
		$billing_fields = get_option( 'wc_fields_billing', array() );
		$shipping_fields = get_option( 'wc_fields_shipping', array() );
		$additional_fields = get_option( 'wc_fields_additional', array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				// Only add non-default Checkout fields to export columns list
				if( isset( $billing_field['custom'] ) && $billing_field['custom'] == 1 ) {
					$fields[] = array(
						'name' => sprintf( 'wc_billing_%s', $key ),
						'label' => sprintf( __( 'Billing: %s', 'woocommerce-exporter' ), ucfirst( $billing_field['label'] ) ),
						'hover' => __( 'Checkout Field Editor', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $billing_fields, $billing_field );

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				// Only add non-default Checkout fields to export columns list
				if( isset( $shipping_field['custom'] ) && $shipping_field['custom'] == 1 ) {
					$fields[] = array(
						'name' => sprintf( 'wc_shipping_%s', $key ),
						'label' => sprintf( __( 'Shipping: %s', 'woocommerce-exporter' ), ucfirst( $shipping_field['label'] ) ),
						'hover' => __( 'Checkout Field Editor', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $shipping_fields, $shipping_field );

		// Additional fields
		if( !empty( $additional_fields ) ) {
			foreach( $additional_fields as $key => $additional_field ) {
				// Only add non-default Checkout fields to export columns list
				if( isset( $additional_field['custom'] ) && $additional_field['custom'] == 1 ) {
					$fields[] = array(
						'name' => sprintf( 'wc_additional_%s', $key ),
						'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ucfirst( $additional_field['label'] ) ),
						'hover' => __( 'Checkout Field Editor', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $additional_fields, $additional_field );
	}

	// Checkout Field Manager - http://61extensions.com
	if( function_exists( 'sod_woocommerce_checkout_manager_settings' ) ) {
		$billing_fields = get_option( 'woocommerce_checkout_billing_fields', array() );
		$shipping_fields = get_option( 'woocommerce_checkout_shipping_fields', array() );
		$custom_fields = get_option( 'woocommerce_checkout_additional_fields', array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $billing_field['default_field'] ) != 'on' ) {
					$fields[] = array(
						'name' => sprintf( 'sod_billing_%s', $billing_field['name'] ),
						'label' => sprintf( __( 'Billing: %s', 'woocommerce-exporter' ), ucfirst( $billing_field['label'] ) ),
						'hover' => __( 'Checkout Field Manager', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $billing_fields, $billing_field );

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $shipping_field['default_field'] ) != 'on' ) {
					$fields[] = array(
						'name' => sprintf( 'sod_shipping_%s', $shipping_field['name'] ),
						'label' => sprintf( __( 'Shipping: %s', 'woocommerce-exporter' ), ucfirst( $shipping_field['label'] ) ),
						'hover' => __( 'Checkout Field Manager', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $shipping_fields, $shipping_field );

		// Custom fields
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $key => $custom_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $custom_field['default_field'] ) != 'on' ) {
					$fields[] = array(
						'name' => sprintf( 'sod_additional_%s', $custom_field['name'] ),
						'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ucfirst( $custom_field['label'] ) ),
						'hover' => __( 'Checkout Field Manager', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $custom_fields, $custom_field );
	}

	// WooCommerce Extra Checkout Fields for Brazil - https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/
	if( class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
		$fields[] = array(
			'name' => 'billing_cpf',
			'label' => __( 'Billing: CPF', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_rg',
			'label' => __( 'Billing: RG', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_cnpj',
			'label' => __( 'Billing: CNPJ', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_ie',
			'label' => __( 'Billing: IE', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_birthdate',
			'label' => __( 'Billing: Birth date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_sex',
			'label' => __( 'Billing: Sex', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_number',
			'label' => __( 'Billing: Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_neighborhood',
			'label' => __( 'Billing: Neighborhood', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_cellphone',
			'label' => __( 'Billing: Cell phone', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'shipping_number',
			'label' => __( 'Shipping: Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'shipping_neighborhood',
			'label' => __( 'Shipping: Neighborhood', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Quick Donation - http://wordpress.org/plugins/woocommerce-quick-donation/
	if( class_exists( 'WooCommerce_Quick_Donation' ) ) {
		$fields[] = array(
			'name' => 'project_id',
			'label' => __( 'Project ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Quick Donation', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'project_name',
			'label' => __( 'Project Name', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Quick Donation', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Easy Checkout Fields Editor - http://codecanyon.net/item/woocommerce-easy-checkout-field-editor/9799777
	if( function_exists( 'pcmfe_admin_form_field' ) ) {
		$custom_fields = get_option( 'pcfme_additional_settings' );
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $key => $custom_field ) {
				$fields[] = array(
					'name' => $key,
					'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ucfirst( $custom_field['label'] ) ),
					'hover' => __( 'WooCommerce Easy Checkout Fields Editor', 'woocommerce-exporter' )
				);
			}
			unset( $custom_fields, $custom_field );
		}
	}

	// WooCommerce Events - http://www.woocommerceevents.com/
	if( class_exists( 'WooCommerce_Events' ) ) {
		$fields[] = array(
			'name' => 'tickets_purchased',
			'label' => __( 'Tickets Purchased', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Currency Switcher - http://dev.pathtoenlightenment.net/shop
	if( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {
		$fields[] = array(
			'name' => 'order_currency',
			'label' => __( 'Order Currency', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Currency Switcher', 'woocommerce-exporter' )
		);
	}

	// WooCommerce EU VAT Number - http://woothemes.com/woocommerce
	if( function_exists( '__wc_eu_vat_number_init' ) ) {
		$fields[] = array(
			'name' => 'eu_vat',
			'label' => __( 'VAT ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_validated',
			'label' => __( 'VAT ID Validated', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_b2b',
			'label' => __( 'VAT B2B Transaction', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' )
		);
	}

	// WooCommerce EU VAT Assistant - https://wordpress.org/plugins/woocommerce-eu-vat-assistant/
	if( class_exists( 'Aelia_WC_RequirementsChecks' ) ) {
		$fields[] = array(
			'name' => 'eu_vat',
			'label' => __( 'VAT ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_country',
			'label' => __( 'VAT ID Country', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_validated',
			'label' => __( 'VAT ID Validated', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Custom Admin Order Fields - http://www.woothemes.com/products/woocommerce-admin-custom-order-fields/
	if( function_exists( 'init_woocommerce_admin_custom_order_fields' ) ) {
		$ac_fields = get_option( 'wc_admin_custom_order_fields' );
		if( !empty( $ac_fields ) ) {
			foreach( $ac_fields as $ac_key => $ac_field ) {
				$fields[] = array(
					'name' => sprintf( 'wc_acof_%d', $ac_key ),
					'label' => sprintf( __( 'Admin Custom Order Field: %s', 'woocommerce-exporter' ), $ac_field['label'] )
				);
			}
		}
	}

	// Order Items go below this line

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		$fields[] = array(
			'name' => 'order_items_checkout_addon_id',
			'label' => __( 'Order Items: Checkout Add-ons ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_checkout_addon_label',
			'label' => __( 'Order Items: Checkout Add-ons Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_checkout_addon_value',
			'label' => __( 'Order Items: Checkout Add-ons Value', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() ) {
		$fields[] = array(
			'name' => 'order_items_brand',
			'label' => __( 'Order Items: Brand', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Brands', 'woocommerce-exporter' )
		);
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		$fields[] = array(
			'name' => 'order_items_vendor',
			'label' => __( 'Order Items: Product Vendor', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' )
		);
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$fields[] = array(
			'name' => 'cost_of_goods',
			'label' => __( 'Order Total Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_cost_of_goods',
			'label' => __( 'Order Items: Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_total_cost_of_goods',
			'label' => __( 'Order Items: Total Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$fields[] = array(
			'name' => 'order_items_msrp',
			'label' => __( 'Order Items: MSRP', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce MSRP Pricing', 'woocommerce-exporter' )
		);
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) ) {
		$fields[] = array(
			'name' => 'order_items_pickup_location',
			'label' => __( 'Order Items: Pickup Location', 'woocommerce-exporter' ),
			'hover' => __( 'Local Pickup Plus', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$fields[] = array(
			'name' => 'order_items_booking_id',
			'label' => __( 'Order Items: Booking ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_date',
			'label' => __( 'Order Items: Booking Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_type',
			'label' => __( 'Order Items: Booking Type', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_start_date',
			'label' => __( 'Order Items: Start Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_end_date',
			'label' => __( 'Order Items: End Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( class_exists( 'RGForms' ) && class_exists( 'woocommerce_gravityforms' ) ) {
		// Check if there are any Products linked to Gravity Forms
		if( $gf_fields = woo_ce_get_gravity_form_fields() ) {
			$fields[] = array(
				'name' => 'order_items_gf_form_id',
				'label' => __( 'Order Items: Gravity Form ID', 'woocommerce-exporter' ),
				'hover' => __( 'Gravity Forms', 'woocommerce-exporter' )
			);
			$fields[] = array(
				'name' => 'order_items_gf_form_label',
				'label' => __( 'Order Items: Gravity Form Label', 'woocommerce-exporter' ),
				'hover' => __( 'Gravity Forms', 'woocommerce-exporter' )
			);
			foreach( $gf_fields as $gf_field ) {
				$gf_field_duplicate = false;
				// Check if this isn't a duplicate Gravity Forms field
				foreach( $fields as $field ) {
					if( isset( $field['name'] ) && $field['name'] == sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] ) ) {
						// Duplicate exists
						$gf_field_duplicate = true;
						break;
					}
				}
				// If it's not a duplicate go ahead and add it to the list
				if( $gf_field_duplicate !== true ) {
					$fields[] = array(
						'name' => sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] ),
						'label' => sprintf( apply_filters( 'woo_ce_extend_order_fields_gf_label', __( 'Order Items: %s - %s', 'woocommerce-exporter' ) ), ucwords( strtolower( $gf_field['formTitle'] ) ), ucfirst( strtolower( $gf_field['label'] ) ) ),
						'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_gf_hover', '%s: %s (ID: %d)' ), __( 'Gravity Forms', 'woocommerce-exporter' ), ucwords( strtolower( $gf_field['formTitle'] ) ), $gf_field['formId'] )
					);
				}
			}
			unset( $gf_fields, $gf_field );
		}
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields() ) {
			foreach( $tm_fields as $tm_field ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), $tm_field['name'] ),
					'hover' => __( 'WooCommerce TM Extra Product Options', 'woocommerce-exporter' )
				);
			}
			unset( $tm_fields, $tm_field );
		}
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						$fields[] = array(
							'name' => sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) ),
							'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucfirst( $custom_field['label'] ) ),
							'hover' => sprintf( '%s: %s (ID: %s)', __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ), __( 'Product Field', 'woocommerce-exporter' ), sanitize_key( $custom_field['key'] ) )
						);
					}
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// WooCommerce Ship to Multiple Addresses - http://woothemes.com/woocommerce
	if( class_exists( 'WC_Ship_Multiple' ) ) {
		$fields[] = array(
			'name' => 'wcms_number_packages',
			'label' => __( 'Number of Packages', 'woocommerce-exporter' ),
			'hover' => __( 'Ship to Multiple Addresses', 'woocommerce-exporter' )
		);
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		$fields[] = array(
			'name' => 'order_items_barcode_type',
			'label' => __( 'Order Items: Barcode Type', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_barcode',
			'label' => __( 'Order Items: Barcode', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' )
		);
	}

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			$attribute->attribute_label = trim( $attribute->attribute_label );
			if( empty( $attribute->attribute_label ) )
				$attribute->attribute_label = $attribute->attribute_name;
			$fields[] = array(
				'name' => sprintf( 'order_items_attribute_%s', $attribute->attribute_name ),
				'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucwords( $attribute->attribute_label ) ),
				'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_attribute', '%s: %s (#%d)' ), __( 'Attribute', 'woocommerce-exporter' ), $attribute->attribute_name, $attribute->attribute_id )
			);
		}
		unset( $attributes, $attribute );
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => woo_ce_clean_export_label( $custom_user ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_user_hover', '%s: %s' ), __( 'Custom User', 'woocommerce-exporter' ), $custom_user )
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	// Custom Order fields
	$custom_orders = woo_ce_get_option( 'custom_orders', '' );
	if( !empty( $custom_orders ) ) {
		foreach( $custom_orders as $custom_order ) {
			if( !empty( $custom_order ) ) {
				$fields[] = array(
					'name' => $custom_order,
					'label' => woo_ce_clean_export_label( $custom_order ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_order_hover', '%s: %s' ), __( 'Custom Order', 'woocommerce-exporter' ), $custom_order )
				);
			}
		}
		unset( $custom_orders, $custom_order );
	}

	return $fields;

}
add_filter( 'woo_ce_order_fields', 'woo_ce_extend_order_fields' );

// Adds custom Order Item columns to the Order Items fields list
function woo_ce_extend_order_items_fields( $fields = array() ) {

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_%s', $custom_order_item ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_order_item ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_order_item_hover', '%s: %s' ), __( 'Custom Order Item', 'woocommerce-exporter' ), $custom_order_item )
				);
			}
		}
	}
	unset( $custom_order_items, $custom_order_item );

	// Custom Product fields
	$custom_product_fields = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_product_fields ) ) {
		foreach( $custom_product_fields as $custom_product_field ) {
			if( !empty( $custom_product_field ) ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_%s', $custom_product_field ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_product_field ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_product_hover', '%s: %s' ), __( 'Custom Product', 'woocommerce-exporter' ), $custom_product_field )
				);
			}
		}
	}
	unset( $custom_product_fields, $custom_product_field );

	return $fields;

}
add_filter( 'woo_ce_order_items_fields', 'woo_ce_extend_order_items_fields' );

// Returns the export column header label based on an export column slug
function woo_ce_get_order_field( $name = null, $format = 'name', $order_items = false ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_order_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						if( $order_items == 'unique' )
							$output = str_replace( __( 'Order Items: ', 'woocommerce-exporter' ), '', $output );
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

// Returns a list of Order IDs
function woo_ce_get_orders( $export_type = 'order', $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;

	if( $args ) {
		$order_ids = ( isset( $args['order_ids'] ) ? $args['order_ids'] : false );
		$payment = ( isset( $args['order_payment'] ) ? $args['order_payment'] : false );
		$shipping = ( isset( $args['order_shipping'] ) ? $args['order_shipping'] : false );
		$user_roles = ( isset( $args['order_user_roles'] ) ? $args['order_user_roles'] : false );
		$coupons = ( isset( $args['order_coupons'] ) ? $args['order_coupons'] : false );
		$product = ( isset( $args['order_product'] ) ? $args['order_product'] : false );
		$product_category = ( isset( $args['order_category'] ) ? $args['order_category'] : false );
		$product_tag = ( isset( $args['order_tag'] ) ? $args['order_tag'] : false );
		$product_brand = ( isset( $args['order_brand'] ) ? $args['order_brand'] : false );
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = $args['offset'];
		$orderby = ( isset( $args['order_orderby'] ) ? $args['order_orderby'] : 'ID' );
		$order = ( isset( $args['order_order'] ) ? $args['order_order'] : 'ASC' );
		$order_dates_filter = ( isset( $args['order_dates_filter'] ) ? $args['order_dates_filter'] : false );
		switch( $order_dates_filter ) {

			case 'today':
				$order_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );
				$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );
				break;

			case 'yesterday':
				$order_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '-2 days' ) ), date( 'd', strtotime( '-2 days' ) ) ) );
				$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '-1 days' ) ), date( 'd', strtotime( '-1 days' ) ) ) );
				break;

			case 'current_week':
				$order_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'this Monday' ) ), date( 'd', strtotime( 'this Monday' ) ) ) );
				$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'next Sunday' ) ), date( 'd', strtotime( 'next Sunday' ) ) ) );
				break;

			case 'last_week':
				$order_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'last Monday' ) ), date( 'd', strtotime( 'last Monday' ) ) ) );
				$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'last Sunday' ) ), date( 'd', strtotime( 'last Sunday' ) ) ) );
				break;

			case 'current_month':
				$order_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), 1 ) );
				$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '+1 month' ) ), 0 ) );
				break;

			case 'last_month':
				$order_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '-1 month' ) ), 1 ) );
				$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), 0 ) );
				break;

			case 'manual':
				$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );

				// Populate empty from or to dates
				if( !empty( $args['order_dates_from'] ) )
					$order_dates_from = woo_ce_format_order_date( $args['order_dates_from'] );
				else
					$order_dates_from = woo_ce_get_order_first_date( $date_format );
				if( !empty( $args['order_dates_to'] ) )
					$order_dates_to = woo_ce_format_order_date( $args['order_dates_to'] );
				else
					$order_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );

				// WP_Query only accepts D-m-Y so we must format dates to that
				if( $date_format <> 'd/m/Y' ) {
					$date_format = woo_ce_format_order_date( $date_format );
					if( function_exists( 'date_create_from_format' ) && function_exists( 'date_format' ) ) {
						if( $order_dates_from = date_create_from_format( $date_format, $order_dates_from ) )
							$order_dates_from = date_format( $order_dates_from, 'd-m-Y' );
						if( $order_dates_to = date_create_from_format( $date_format, $order_dates_to ) )
							$order_dates_to = date_format( $order_dates_to, 'd-m-Y' );
					}
				}
				break;

			case 'variable':
				$order_filter_date_variable = $args['order_dates_filter_variable'];
				$order_filter_date_variable_length = $args['order_dates_filter_variable_length'];
				if( $order_filter_date_variable !== false && $order_filter_date_variable_length !== false ) {
					$timestamp = strtotime( sprintf( '-%d %s', $order_filter_date_variable, $order_filter_date_variable_length ) );
					$order_dates_from = date( 'd-m-Y', mktime( date( 'H', $timestamp ), date( 'i', $timestamp ), date( 's', $timestamp ), date( 'n', $timestamp ), date( 'd', $timestamp ), date( 'Y', $timestamp ) ) );
					$order_dates_to = date( 'd-m-Y', time() );
					unset( $order_filter_date_variable, $order_filter_date_variable_length, $timestamp );
				}
				break;

			default:
				$order_dates_from = false;
				$order_dates_to = false;
				break;

		}
		if( !empty( $order_dates_from ) && !empty( $order_dates_to ) ) {
			$order_dates_from = explode( '-', $order_dates_from );
			// Check that a valid date was provided
			if( isset( $order_dates_from[0] ) && isset( $order_dates_from[1] ) && isset( $order_dates_from[2] ) ) {
				$order_dates_from = array(
					'year' => $order_dates_from[2],
					'month' => $order_dates_from[1],
					'day' => $order_dates_from[0],
					'hour' => 0,
					'minute' => 0,
					'second' => 0
				);
			} else {
				$order_dates_from = false;
			}
			$order_dates_to = explode( '-', $order_dates_to );
			// Check that a valid date was provided
			if( isset( $order_dates_to[0] ) && isset( $order_dates_to[1] ) && isset( $order_dates_to[2] ) ) {
				$order_dates_to = array(
					'year' => $order_dates_to[2],
					'month' => $order_dates_to[1],
					'day' => $order_dates_to[0],
					'hour' => 23,
					'minute' => 59,
					'second' => 59
				);
			} else {
				$order_dates_to = false;
			}
		}
		$order_status = ( isset( $args['order_status'] ) ? $args['order_status'] : array() );
		$user_ids = ( isset( $args['order_customer'] ) ? $args['order_customer'] : false );
		$billing_country = ( isset( $args['order_billing_country'] ) ? $args['order_billing_country'] : false );
		$shipping_country = ( isset( $args['order_shipping_country'] ) ? $args['order_shipping_country'] : false );
		$order_items = $args['order_items'];
	}
	$post_type = 'shop_order';
	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'fields' => 'ids',
		'suppress_filters' => false
	);
	$woocommerce_version = woo_get_woo_version();
	// Check if this is a pre-WooCommerce 2.2 instance
	if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
		$args['post_status'] = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_ce_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
	else
		$args['post_status'] = apply_filters( 'woo_ce_order_post_status', 'publish' );
	if( !empty( $order_ids ) ) {
		$order_ids = explode( ',', $order_ids );
		// Check if we're looking up a Sequential Order Number
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			$args['meta_query'][] = array(
				'key' => ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ),
				'value' => $order_ids
			);
		} else {
			$size = count( $order_ids );
			if( $size > 1 )
				$args['post__in'] = array_map( 'absint', $order_ids );
			else
				$args['p'] = absint( $order_ids[0] );
		}
	}
	if( $product ) {
		$order_ids = woo_ce_get_product_assoc_order_ids( $product );
		if( $order_ids ) {
			$size = count( $order_ids );
			if( $size > 1 )
				$args['post__in'] = array_map( 'absint', $order_ids );
			else
				$args['p'] = absint( $order_ids[0] );
		}
	}
	if( !empty( $payment ) ) {
		$args['meta_query'][] = array(
			'key' => '_payment_method',
			'value' => $payment
		);
	}
	if( !empty( $order_status ) ) {
		// Check if this is a WooCommerce 2.2+ instance (new Post Status)
		if( version_compare( $woocommerce_version, '2.2' ) >= 0 ) {
			$args['post_status'] = $order_status;
			if( $export->cron ) {
				// Something weird is going on so we'll override WordPress on this one
				$args['post_status'] = implode( ',', $order_status );
				$args['suppress_filters'] = false;
				add_filter( 'posts_where' , 'woo_ce_wp_query_order_where_override' );
			}
		} else {
			$term_taxonomy = 'shop_order_status';
			$args['tax_query'] = array(
				array(
					'taxonomy' => $term_taxonomy,
					'field' => 'slug',
					'terms' => $order_status
				)
			);
		}
	}
	if( !empty( $user_ids ) ) {
		// Check if we're dealing with a string or list of users
		if( is_string( $user_ids ) )
			$user_ids = explode( ',', $user_ids );
		$user_emails = array();
		foreach( $user_ids as $user_id ) {
			if( $user = get_userdata( $user_id ) )
				$user_emails[] = $user->user_email;
		}
		if( !empty( $user_emails ) ) {
			$args['meta_query'][] = array(
				'key' => '_billing_email',
				'value' => $user_emails
			);
		}
		unset( $user_id, $user_emails );
	}
	if( !empty( $billing_country ) ) {
		$args['meta_query'][] = array(
			'key' => '_billing_country',
			'value' => $billing_country
		);
	}
	if( !empty( $shipping_country ) ) {
		$args['meta_query'][] = array(
			'key' => '_shipping_country',
			'value' => $shipping_country
		);
	}
	// Filter Order dates
	if( !empty( $order_dates_from ) && !empty( $order_dates_to ) ) {
		$args['date_query'] = array(
			array(
				'column' => 'post_date',
				'before' => $order_dates_to,
				'after' => $order_dates_from,
				'inclusive' => true
			)
		);
	}
	if( $order_dates_filter == 'last_export' ) {
		$args['meta_query'][] = array(
			'key' => '_woo_cd_exported',
			'value' => 1,
			'compare' => 'NOT EXISTS'
		);
	}
	$orders = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_orders_args', $args );

	$order_ids = new WP_Query( $args );
	// Something weird is going on so we'll override WordPress on this one
	if( !empty( $order_status ) && $export->cron && version_compare( $woocommerce_version, '2.2' ) >= 0 )
		remove_filter( 'posts_where' , 'woo_ce_wp_query_order_where_override' );
	if( $order_ids->posts ) {
		foreach( $order_ids->posts as $order_id ) {

			// Get WooCommerce Order details
			$order = woo_ce_get_order_wc_data( $order_id );

			// Filter Orders by User Roles
			$order->user_id = get_post_meta( $order->id, '_customer_user', true );
			if( $user_roles ) {
				$user_ids = array();
				$size = count( $export->args['order_user_roles'] );
				for( $i = 0; $i < $size; $i++ ) {
					$args = array(
						'role' => $export->args['order_user_roles'][$i],
						'fields' => 'ID'
					);
					$user_id = get_users( $args );
					$user_ids = array_merge( $user_ids, $user_id );
				}
				if( !in_array( $order->user_id, $user_ids ) ) {
					unset( $order );
					continue;
				}
			}

			// Filter Orders by Coupons
			$order->coupon_code = woo_ce_get_order_assoc_coupon( $order->id );
			if( $coupons ) {
				$coupon_ids = array();
				$size = count( $export->args['order_coupons'] );
				for( $i = 0; $i < $size; $i++ )
					$coupon_ids[] = get_the_title( $coupons[$i] );
				if( !in_array( $order->coupon_code, $coupon_ids ) ) {
					unset( $order );
					continue;
				}
			}

			// Filter Orders by Product Category
			if( $product_category ) {
				if( $order_items = woo_ce_get_order_item_ids( $order->id ) ) {
					$term_taxonomy = 'product_cat';
					$args = array(
						'fields' => 'ids'
					);
					$category_ids = array();
					foreach( $order_items as $order_item ) {
						if( $product_categories = wp_get_post_terms( $order_item->product_id, $term_taxonomy, $args ) ) {
							$category_ids = array_merge( $category_ids, $product_categories );
							unset( $product_categories );
						}
					}
					if( count( array_intersect( $product_category, $category_ids ) ) == 0 ) {
						unset( $order );
						continue;
					}
					unset( $category_ids );
				} else {
					// If the Order has no Order Items assigned to it we can safely remove it from the export
					unset( $order );
					continue;
				}
				unset( $order_items );
			}

			// Filter Orders by Product Tag
			if( $product_tag ) {
				if( $order_items = woo_ce_get_order_item_ids( $order->id ) ) {
					$term_taxonomy = 'product_tag';
					$args = array(
						'fields' => 'ids'
					);
					$tag_ids = array();
					foreach( $order_items as $order_item ) {
						if( $product_tags = wp_get_post_terms( $order_item->product_id, $term_taxonomy, $args ) ) {
							$tag_ids = array_merge( $tag_ids, $product_tags );
							unset( $product_tags );
						}
					}
					if( empty( $tag_ids ) || count( array_intersect( $product_tag, $tag_ids ) ) == 0 ) {
						unset( $order );
						continue;
					}
					unset( $tag_ids );
				} else {
					// If the Order has no Order Items assigned to it we can safely remove it from the export
					unset( $order );
					continue;
				}
				unset( $order_items );
			}

			// Filter Orders by Product Brand
			if( $product_brand ) {
				if( $order_items = woo_ce_get_order_item_ids( $order->id ) ) {
					$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
					$args = array(
						'fields' => 'ids'
					);
					$brand_ids = array();
					foreach( $order_items as $order_item ) {
						if( $product_brands = wp_get_post_terms( $order_item->product_id, $term_taxonomy, $args ) ) {
							$brand_ids = array_merge( $brand_ids, $product_brands );
							unset( $product_brands );
						}
					}
					if( empty( $brand_ids ) || count( array_intersect( $product_brand, $brand_ids ) ) == 0 ) {
						unset( $order );
						continue;
					}
					unset( $brand_ids );
				} else {
					// If the Order has no Order Items assigned to it we can safely remove it from the export
					unset( $order );
					continue;
				}
				unset( $order_items );
			}

			// Filter Orders by Shipping Method
			if( $shipping ) {
				$shipping_id = woo_ce_get_order_assoc_shipping_method_id( $order->id );
				if( !in_array( $shipping_id, $shipping ) ) {
					unset( $order );
					continue;
				}
				unset( $shipping_id );
			}

			$order->id = apply_filters( 'woo_ce_get_order_id', $order->id );
			if( $order->id )
				$orders[] = $order->id;

			// Mark this Order as exported if Since last export Date filter is used
			if( $order_dates_filter == 'last_export' && $order->id ) {
				update_post_meta( $order->id, '_woo_cd_exported', 1 );
				if( woo_ce_get_option( 'order_flag_notes', 0 ) ) {
					// Add an Order Note
					$note = __( 'Order was exported successfully.', 'woocommerce-exporter' );
					if( method_exists( $order, 'add_order_note' ) )
						$order->add_order_note( $note );
					unset( $note );
				}
			}

		}
		// Only populate the $export Global if it is an export
		if( isset( $export ) ) {
			$export->total_rows = count( $orders );
			if( !empty( $order_ids ) ) {
				// Check if we're looking up a Sequential Order Number
				if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) )
					$export->order_ids_raw = $orders;
			}
		}
		unset( $order_ids, $order_id );
	}
	switch( $export_type ) {

		case 'order':
			if( WOO_CD_DEBUG !== true ) {
				if( $order_dates_filter == 'last_export' ) {
					// Save the Order ID's list to a WordPress Transient incase the export fails
					woo_ce_update_option( 'exported', $orders );
				}
			}
			return $orders;
			break;

		case 'customer':
			$customers = array();
			if( !empty( $orders ) ) {
				foreach( $orders as $order_id ) {
					$order = woo_ce_get_order_data( $order_id, 'customer', $export->args );
					if( $duplicate_key = woo_ce_is_duplicate_customer( $customers, $order ) ) {
						$customers[$duplicate_key]->total_spent = $customers[$duplicate_key]->total_spent + woo_ce_format_price( get_post_meta( $order_id, '_order_total', true ) );
						$customers[$duplicate_key]->total_orders++;
						if( strtolower( $order->payment_status ) == 'completed' )
							$customers[$duplicate_key]->completed_orders++;
					} else {
						$customers[$order_id] = $order;
						$customers[$order_id]->total_spent = woo_ce_format_price( get_post_meta( $order_id, '_order_total', true ) );
						$customers[$order_id]->completed_orders = 0;
						if( strtolower( $order->payment_status ) == 'completed' )
							$customers[$order_id]->completed_orders = 1;
						$customers[$order_id]->total_orders = 1;
					}
				}
			}
			return $customers;
			break;

	}

}

function woo_ce_wp_query_order_where_override( $where ) {

	global $export, $wpdb;

	$order_status = ( isset( $export->args['order_status'] ) ? $export->args['order_status'] : false );

	// Skip this if we're dealing with stock WordPress Post Status
	if( count( array_intersect( array( 'trash', 'publish' ), $order_status ) ) )
		return $where;

	// Let's add in our custom Post Status parameters
	if( !empty( $order_status ) ) {
		foreach( $order_status as $key => $status ) {
			if( empty( $status ) ) {
				unset( $order_status[$key] );
				continue;
			}
			$order_status[$key] = " " . $wpdb->posts . ".post_status = '$status'";
		}
		if( !empty( $order_status ) )
			$where .= " AND (" . join( ' OR ', $order_status ) . ")";
	}

	return $where;

}

// Returns WooCommerce Order data associated to a specific Order
function woo_ce_get_order_wc_data( $order_id = 0 ) {

	if( !empty( $order_id ) ) {
		$order = ( class_exists( 'WC_Order' ) ? new WC_Order( $order_id ) : get_post( $order_id ) );
		return $order;
	}

}

function woo_ce_get_order_data( $order_id = 0, $export_type = 'order', $args = array(), $fields = array() ) {

	global $export;

	// Check if this is a pre-WooCommerce 2.2 instance
	$woocommerce_version = woo_get_woo_version();

	$defaults = array(
		'order_items' => 'combined',
		'order_items_types' => array_keys( woo_ce_get_order_items_types() )
	);
	$args = wp_parse_args( $args, $defaults );

	// Get WooCommerce Order details
	$order = woo_ce_get_order_wc_data( $order_id );

	$order->ID = ( isset( $order->id ) ? $order->id : $order_id );
	$order->payment_status = $order->status;

	$order->post_status = woo_ce_format_post_status( $order->post_status );
	$order->user_id = get_post_meta( $order_id, '_customer_user', true );
	if( $order->user_id == 0 )
		$order->user_id = '';
	$order->user_name = woo_ce_get_username( $order->user_id );
	$order->user_role = woo_ce_format_user_role_label( woo_ce_get_user_role( $order->user_id ) );
	$order->purchase_total = get_post_meta( $order_id, '_order_total', true );
	$order->refund_total = ( method_exists( $order, 'get_total_refunded' ) ? $order->get_total_refunded() : '' );
	$order->refund_date = ( !empty( $order->refund_total ) ? woo_ce_get_order_assoc_refund_date( $order_id ) : '' );
	$order->order_currency = get_post_meta( $order_id, '_order_currency', true );

	$order->billing_first_name = get_post_meta( $order_id, '_billing_first_name', true );
	$order->billing_last_name = get_post_meta( $order_id, '_billing_last_name', true );
	if( empty( $order->billing_first_name ) && empty( $order->billing_first_name ) )
		$order->billing_full_name = '';
	else
		$order->billing_full_name = $order->billing_first_name . ' ' . $order->billing_last_name;
	$order->billing_company = get_post_meta( $order_id, '_billing_company', true );
	$order->billing_address = '';
	$order->billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
	$order->billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
	if( !empty( $order->billing_address_2 ) )
		$order->billing_address = sprintf( apply_filters( 'woo_ce_get_order_data_billing_address', '%s %s' ), $order->billing_address_1, $order->billing_address_2 );
	else
		$order->billing_address = $order->billing_address_1;
	$order->billing_city = get_post_meta( $order_id, '_billing_city', true );
	$order->billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
	$order->billing_state = get_post_meta( $order_id, '_billing_state', true );
	$order->billing_country = get_post_meta( $order_id, '_billing_country', true );
	$order->billing_state_full = woo_ce_expand_state_name( $order->billing_country, $order->billing_state );
	$order->billing_country_full = woo_ce_expand_country_name( $order->billing_country );
	$order->billing_phone = get_post_meta( $order_id, '_billing_phone', true );
	$order->billing_email = get_post_meta( $order_id, '_billing_email', true );
	// If the e-mail address is empty check if the Order has a User assigned to it
	if( empty( $order->billing_email ) ) {
		// Check if a User ID has been assigned
		if( !empty( $order->user_id ) ) {
			$user = woo_ce_get_user_data( $order->user_id );
			// Check if the User is valid and e-mail assigned to User
			if( isset( $user->email ) )
				$order->billing_email = $user->email;
			unset( $user );
		}
	}
	$order->shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
	$order->shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
	if( empty( $order->shipping_first_name ) && empty( $order->shipping_last_name ) )
		$order->shipping_full_name = '';
	else
		$order->shipping_full_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
	$order->shipping_company = get_post_meta( $order_id, '_shipping_company', true );
	$order->shipping_address = '';
	$order->shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
	$order->shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
	if( !empty( $order->billing_address_2 ) )
		$order->shipping_address = sprintf( apply_filters( 'woo_ce_get_order_data_shipping_address', '%s %s' ), $order->shipping_address_1, $order->shipping_address_2 );
	else
		$order->shipping_address = $order->shipping_address_1;
	$order->shipping_city = get_post_meta( $order_id, '_shipping_city', true );
	$order->shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
	$order->shipping_state = get_post_meta( $order_id, '_shipping_state', true );
	$order->shipping_country = get_post_meta( $order_id, '_shipping_country', true );
	$order->shipping_state_full = woo_ce_expand_state_name( $order->shipping_country, $order->shipping_state );
	$order->shipping_country_full = woo_ce_expand_country_name( $order->shipping_country );
	$order->shipping_phone = get_post_meta( $order_id, '_shipping_phone', true );

	if( $export_type == 'order' ) {

		$order->post_id = $order->purchase_id = $order_id;
		$order->order_discount = get_post_meta( $order_id, '_cart_discount', true );
		$order->coupon_code = woo_ce_get_order_assoc_coupon( $order_id );
		if( !empty( $order->coupon_code ) ) {
			$coupon = get_page_by_title( $order->coupon_code, OBJECT, 'shop_coupon' );
			if( $coupon !== null )
				$order->coupon_description = $coupon->post_excerpt;
			unset( $coupon );
		}
		$order->order_sales_tax = get_post_meta( $order_id, '_order_tax', true );
		$order->order_shipping_tax = get_post_meta( $order_id, '_order_shipping_tax', true );
		$order->shipping_cost = get_post_meta( $order_id, '_order_shipping', true );
		$order->shipping_excl_tax = ( $order->shipping_cost - $order->order_shipping_tax );
		$order->purchase_total_tax = ( $order->order_sales_tax + $order->order_shipping_tax );
		if( !empty( $order->purchase_total_tax ) ) {
			// Tax Rates
			$tax_rates = woo_ce_get_order_tax_rates();
			if( !empty( $tax_rates ) ) {
				foreach( $tax_rates as $tax_rate ) {
					$order->{sprintf( 'purchase_total_tax_rate_%d', $tax_rate['rate_id'] )} = woo_ce_format_price( woo_ce_get_order_assoc_tax_rate_total( $order_id, $tax_rate['rate_id'] ), $order->order_currency );
				}
			}
			unset( $tax_rates, $tax_rate );
		}
		$order->purchase_total = $order->purchase_total - $order->refund_total;
		$order->order_subtotal_excl_tax = ( $order->purchase_total - $order->purchase_total_tax );
		$order->purchase_subtotal = $order->order_subtotal_excl_tax - $order->shipping_cost;
		// Order Tax Percentage - Order Total - Total Tax / Total Tax
		if( !empty( $order->purchase_total_tax ) && !empty( $order->purchase_total ) )
			$order->order_tax_percentage = absint( ( $order->purchase_total - $order->purchase_total_tax ) / $order->purchase_total_tax ) . '%';
		$order->purchase_total = woo_ce_format_price( $order->purchase_total, $order->order_currency );
		$order->order_sales_tax = woo_ce_format_price( $order->order_sales_tax, $order->order_currency );
		$order->order_shipping_tax = woo_ce_format_price( $order->order_shipping_tax, $order->order_currency );
		$order->purchase_subtotal = woo_ce_format_price( $order->purchase_subtotal, $order->order_currency );
		$order->order_discount = woo_ce_format_price( $order->order_discount, $order->order_currency );
		$order->order_subtotal_excl_tax = woo_ce_format_price( $order->order_subtotal_excl_tax, $order->order_currency );
		$order->refund_total = woo_ce_format_price( $order->refund_total, $order->order_currency );
		$order->payment_status = woo_ce_format_order_status( $order->payment_status );
		$order->payment_gateway_id = get_post_meta( $order_id, '_payment_method', true );
		$order->payment_gateway = woo_ce_format_order_payment_gateway( $order->payment_gateway_id );
		// WooCommerce 2.1 stores the shipping method in cart items, includes fallback support
		if( method_exists( $order, 'get_shipping_method' ) ) {
			$order->shipping_method_id = woo_ce_get_order_assoc_shipping_method_id( $order_id );
			$order->shipping_method = $order->get_shipping_method();
		} else {
			$order->shipping_method_id = get_post_meta( $order_id, '_shipping_method', true );
			$order->shipping_method = '';
		}
		$order->shipping_cost = woo_ce_format_price( $order->shipping_cost, $order->order_currency );
		$order->shipping_excl_tax = woo_ce_format_price( $order->shipping_excl_tax, $order->order_currency );
		$order->purchase_total_tax = woo_ce_format_price( $order->purchase_total_tax, $order->order_currency );
		$order->shipping_weight = '';
		$order->order_key = get_post_meta( $order_id, '_order_key', true );
		$order->purchase_date = woo_ce_format_date( $order->order_date );
		$order->purchase_time = mysql2date( 'H:i:s', $order->order_date );
		$order->ip_address = woo_ce_format_ip_address( get_post_meta( $order_id, '_customer_ip_address', true ) );
		$order->browser_agent = get_post_meta( $order_id, '_customer_user_agent', true );
		$order->has_downloads = 0;
		$order->has_downloaded = 0;
		// Order Downloads
		if( $order_downloads = woo_ce_get_order_assoc_downloads( $order_id ) ) {
			$order->has_downloads = 1;
			foreach( $order_downloads as $order_download ) {
				// Check if any download permissions have counts against them
				if( $order_download->download_count > 0 ) {
					$order->has_downloaded = 1;
					break;
				}
			}
		}
		unset( $order_downloads, $order_download );
		$order->has_downloads = woo_ce_format_switch( $order->has_downloads );
		$order->has_downloaded = woo_ce_format_switch( $order->has_downloaded );
		$order->customer_notes = '';
		$order->order_notes = '';
		$order->total_quantity = 0;
		$order->total_order_items = 0;
		// Order Notes
		if( $order_notes = woo_ce_get_order_assoc_notes( $order_id ) ) {
			if( WOO_CD_DEBUG )
				$order->order_notes = implode( $export->category_separator, $order_notes );
			else
				$order->order_notes = implode( "\n", $order_notes );
			unset( $order_notes );
		}
		// Customer Notes
		if( $order_notes = woo_ce_get_order_assoc_notes( $order_id, 'customer_note' ) ) {
			if( WOO_CD_DEBUG )
				$order->customer_notes = implode( $export->category_separator, $order_notes );
			else
				$order->customer_notes = implode( "\n", $order_notes );
			unset( $order_notes );
		}
		if( $order->order_items = woo_ce_get_order_items( $order_id, $args['order_items_types'] ) ) {
			$order->total_order_items = count( $order->order_items );
			if( $args['order_items'] == 'combined' ) {
				$order->order_items_id = '';
				$order->order_items_product_id = '';
				$order->order_items_variation_id = '';
				$order->order_items_sku = '';
				$order->order_items_name = '';
				$order->order_items_variation = '';
				$order->order_items_description = '';
				$order->order_items_excerpt = '';
				$order->order_items_tax_class = '';
				$order->order_items_quantity = '';
				$order->order_items_total = '';
				$order->order_items_subtotal = '';
				$order->order_items_rrp = '';
				$order->order_items_stock = '';
				$order->order_items_tax = '';
				$order->order_items_tax_subtotal = '';
				$order->order_items_refund_subtotal = '';
				$order->order_items_refund_quantity = '';
				$order->order_items_type = '';
				$order->order_items_type_id = '';
				$order->order_items_category = '';
				$order->order_items_tag = '';
				$order->order_items_total_sales = '';
				$order->order_items_weight = '';
				$order->order_items_height = '';
				$order->order_items_width = '';
				$order->order_items_length = '';
				$order->order_items_total_weight = '';
				if( !empty( $order->order_items ) ) {
					foreach( $order->order_items as $order_item ) {
						if( empty( $order_item->sku ) )
							$order_item->sku = '';
						$order->order_items_id .= $order_item->id . $export->category_separator;
						$order->order_items_product_id .= $order_item->product_id . $export->category_separator;
						$order->order_items_variation_id .= $order_item->variation_id . $export->category_separator;
						$order->order_items_sku .= $order_item->sku . $export->category_separator;
						$order->order_items_name .= $order_item->name . $export->category_separator;
						$order->order_items_variation .= $order_item->variation . $export->category_separator;
						$order->order_items_description .= woo_ce_format_description_excerpt( $order_item->description ) . $export->category_separator;
						$order->order_items_excerpt .= woo_ce_format_description_excerpt( $order_item->excerpt ) . $export->category_separator;
						$order->order_items_tax_class .= $order_item->tax_class . $export->category_separator;
						$order->total_quantity += $order_item->quantity;
						if( empty( $order_item->quantity ) && '0' != $order_item->quantity )
							$order_item->quantity = '';
						$order->order_items_quantity .= $order_item->quantity . $export->category_separator;
						$order->order_items_total .= $order_item->total . $export->category_separator;
						$order->order_items_subtotal .= $order_item->subtotal . $export->category_separator;
						$order->order_items_rrp .= $order_item->rrp . $export->category_separator;
						$order->order_items_stock .= $order_item->stock . $export->category_separator;
						$order->order_items_tax .= $order_item->tax . $export->category_separator;
						$order->order_items_tax_subtotal .= $order_item->tax_subtotal . $export->category_separator;
						$order->order_items_refund_subtotal .= $order_item->refund_subtotal . $export->category_separator;
						$order->order_items_refund_quantity .= $order_item->refund_quantity . $export->category_separator;
						$order->order_items_type .= $order_item->type . $export->category_separator;
						$order->order_items_type_id .= $order_item->type_id . $export->category_separator;
						$order->order_items_category .= $order_item->category . $export->category_separator;
						$order->order_items_tag .= $order_item->tag . $export->category_separator;
						$order->order_items_total_sales .= $order_item->total_sales . $export->category_separator;
						$order->order_items_weight .= $order_item->weight . $export->category_separator;
						$order->order_items_height .= $order_item->height . $export->category_separator;
						$order->order_items_width .= $order_item->width . $export->category_separator;
						$order->order_items_length .= $order_item->length . $export->category_separator;
						$order->order_items_total_weight .= $order_item->total_weight . $export->category_separator;
						// Add Order Item weight to Shipping Weight
						if( $order_item->total_weight != '' )
							$order->shipping_weight += $order_item->total_weight;
					}
					$order->order_items_id = substr( $order->order_items_id, 0, -1 );
					$order->order_items_product_id = substr( $order->order_items_product_id, 0, -1 );
					$order->order_items_variation_id = substr( $order->order_items_variation_id, 0, -1 );
					$order->order_items_sku = substr( $order->order_items_sku, 0, -1 );
					$order->order_items_name = substr( $order->order_items_name, 0, -1 );
					$order->order_items_variation = substr( $order->order_items_variation, 0, -1 );
					$order->order_items_description = substr( $order->order_items_description, 0, -1 );
					$order->order_items_excerpt = substr( $order->order_items_excerpt, 0, -1 );
					$order->order_items_tax_class = substr( $order->order_items_tax_class, 0, -1 );
					$order->order_items_quantity = substr( $order->order_items_quantity, 0, -1 );
					$order->order_items_total = substr( $order->order_items_total, 0, -1 );
					$order->order_items_subtotal = substr( $order->order_items_subtotal, 0, -1 );
					$order->order_items_rrp = substr( $order->order_items_rrp, 0, -1 );
					$order->order_items_stock = substr( $order->order_items_stock, 0, -1 );
					$order->order_items_tax = substr( $order_item->tax, 0, -1 );
					$order->order_items_tax_subtotal = substr( $order_item->tax_subtotal, 0, -1 );
					$order->order_items_refund_subtotal = substr( $order_item->refund_subtotal, 0, -1 );
					$order->order_items_refund_quantity = substr( $order_item->refund_quantity, 0, -1 );
					$order->order_items_type = substr( $order->order_items_type, 0, -1 );
					$order->order_items_type_id = substr( $order->order_items_type_id, 0, -1 );
					$order->order_items_category = substr( $order->order_items_category, 0, -1 );
					$order->order_items_tag = substr( $order->order_items_tag, 0, -1 );
					$order->order_items_total_sales = substr( $order->order_items_total_sales, 0, -1 );
					$order->order_items_weight = substr( $order->order_items_weight, 0, -1 );
					$order->order_items_height = substr( $order->order_items_height, 0, -1 );
					$order->order_items_width = substr( $order->order_items_width, 0, -1 );
					$order->order_items_length = substr( $order->order_items_length, 0, -1 );
					$order->order_items_total_weight = substr( $order->order_items_total_weight, 0, -1 );
				}
				$order = apply_filters( 'woo_ce_order_items_combined', $order );
			} else if( $args['order_items'] == 'unique' ) {
				if( !empty( $order->order_items ) ) {
					$i = 1;
					foreach( $order->order_items as $order_item ) {
						$order->{sprintf( 'order_item_%d_id', $i )} = $order_item->id;
						$order->{sprintf( 'order_item_%d_product_id', $i )} = $order_item->product_id;
						$order->{sprintf( 'order_item_%d_variation_id', $i )} = $order_item->variation_id;
						$order->{sprintf( 'order_item_%d_sku', $i )} = ( empty( $order_item->sku ) == false ? $order_item->sku : '' );
						$order->{sprintf( 'order_item_%d_name', $i )} = $order_item->name;
						$order->{sprintf( 'order_item_%d_variation', $i )} = $order_item->variation;
						$order->{sprintf( 'order_item_%d_description', $i )} = $order_item->description;
						$order->{sprintf( 'order_item_%d_excerpt', $i )} = $order_item->excerpt;
						$order->{sprintf( 'order_item_%d_tax_class', $i )} = $order_item->tax_class;
						$order->total_quantity += $order_item->quantity;
						if( empty( $order_item->quantity ) && '0' != $order_item->quantity )
							$order_item->quantity = '';
						$order->{sprintf( 'order_item_%d_quantity', $i )} = $order_item->quantity;
						$order->{sprintf( 'order_item_%d_total', $i )} = $order_item->total;
						$order->{sprintf( 'order_item_%d_subtotal', $i )} = $order_item->subtotal;
						$order->{sprintf( 'order_item_%d_rrp', $i )} = $order_item->rrp;
						$order->{sprintf( 'order_item_%d_stock', $i )} = $order_item->stock;
						$order->{sprintf( 'order_item_%d_tax', $i )} = $order_item->tax;
						$order->{sprintf( 'order_item_%d_tax_subtotal', $i )} = $order_item->tax_subtotal;
						$order->{sprintf( 'order_item_%d_refund_subtotal', $i )} = $order_item->refund_subtotal;
						$order->{sprintf( 'order_item_%d_refund_quantity', $i )} = $order_item->refund_quantity;
						$order->{sprintf( 'order_item_%d_type', $i )} = $order_item->type;
						$order->{sprintf( 'order_item_%d_type_id', $i )} = $order_item->type_id;
						$order->{sprintf( 'order_item_%d_category', $i )} = $order_item->category;
						$order->{sprintf( 'order_item_%d_tag', $i )} = $order_item->tag;
						$order->{sprintf( 'order_item_%d_total_sales', $i )} = $order_item->total_sales;
						$order->{sprintf( 'order_item_%d_weight', $i )} = $order_item->weight;
						$order->{sprintf( 'order_item_%d_height', $i )} = $order_item->height;
						$order->{sprintf( 'order_item_%d_width', $i )} = $order_item->width;
						$order->{sprintf( 'order_item_%d_length', $i )} = $order_item->length;
						$order->{sprintf( 'order_item_%d_total_weight', $i )} = $order_item->total_weight;
						// Add Order Item weight to Shipping Weight
						if( $order_item->total_weight != '' )
							$order->shipping_weight += $order_item->total_weight;
						$order = apply_filters( 'woo_ce_order_items_unique', $order, $i, $order_item );
						$i++;
					}
				}
			}
		}

		// Custom Order fields
		$custom_orders = woo_ce_get_option( 'custom_orders', '' );
		if( !empty( $custom_orders ) ) {
			foreach( $custom_orders as $custom_order ) {
				if( !empty( $custom_order ) ) {
					$order->{$custom_order} = woo_ce_format_custom_meta( get_post_meta( $order_id, $custom_order, true ) );
				}
			}
		}

		// Check if the Order has a User assigned to it
		if( !empty( $order->user_id ) ) {
			// Custom User fields
			$custom_users = woo_ce_get_option( 'custom_users', '' );
			if( !empty( $custom_users ) ) {
				foreach( $custom_users as $custom_user ) {
					if( !empty( $custom_user ) && !isset( $order->{$custom_user} ) ) {
						$order->{$custom_user} = woo_ce_format_custom_meta( get_user_meta( $order->user_id, $custom_user, true ) );
					}
				}
			}
			unset( $custom_users, $custom_user );
		}

	} else if( $export_type = 'customer' ) {

		// Check if the Order has a User assigned to it
		if( !empty( $order->user_id ) ) {

			// Load up the User data as other Plugins will use it too
			$user = woo_ce_get_user_data( $order->user_id );

			// WooCommerce Follow-Up Emails - http://www.woothemes.com/products/follow-up-emails/
			if( class_exists( 'FollowUpEmails' ) ) {

				global $wpdb;

				if( isset( $user->email ) ) {
					$followup_optout_sql = $wpdb->prepare( "SELECT `id` FROM `" . $wpdb->prefix . "followup_email_excludes` WHERE `email` = %s LIMIT 1", $user->email );
					$order->followup_optout = $wpdb->get_var( $followup_optout_sql );
				}

			}

			// Custom User fields
			$custom_users = woo_ce_get_option( 'custom_users', '' );
			if( !empty( $custom_users ) ) {
				foreach( $custom_users as $custom_user ) {
					if( !empty( $custom_user ) && !isset( $order->{$custom_user} ) ) {
						$order->{$custom_user} = woo_ce_format_custom_meta( get_user_meta( $order->user_id, $custom_user, true ) );
					}
				}
			}
			unset( $custom_users, $custom_user );

			// Clean up
			unset( $user );

		}

		// Custom Customer fields
		$custom_customers = woo_ce_get_option( 'custom_customers', '' );
		if( !empty( $custom_customers ) ) {
			foreach( $custom_customers as $custom_customer ) {
				if( !empty( $custom_customer ) )
					$order->{$custom_customer} = esc_attr( get_user_meta( $order->user_id, $custom_customer, true ) );
			}
		}

	}

	// Allow Plugin/Theme authors to add support for additional Order columns
	$order = apply_filters( 'woo_ce_order', $order, $order_id );

	// Trim back the Order just to requested export fields
	if( !empty( $fields ) ) {
		$fields[] = 'id';
		if( $args['order_items'] == 'individual' )
			$fields[] = 'order_items';
		if( !empty( $order ) ) {
			foreach( $order as $key => $data ) {
				if( !in_array( $key, $fields ) )
					unset( $order->$key );
			}
		}
	}

	return $order;

}

// Returns a list of WooCommerce Tax Rates based on existing Orders
function woo_ce_get_order_tax_rates() {

	global $wpdb;

	$order_item_type = 'tax';
	$tax_rates_sql = $wpdb->prepare( "SELECT order_items.order_item_id as item_id FROM " . $wpdb->prefix . "woocommerce_order_items as order_items WHERE order_items.order_item_type = %s GROUP BY order_items.order_item_name", $order_item_type );
	$tax_rates = $wpdb->get_results( $tax_rates_sql, 'ARRAY_A' );
	if( !empty( $tax_rates ) ) {
		$meta_type = 'order_item';
		foreach( $tax_rates as $key => $tax_rate ) {
			$tax_rates[$key]['rate_id'] = get_metadata( $meta_type, $tax_rate['item_id'], 'rate_id', true );
			$tax_rates[$key]['label'] = get_metadata( $meta_type, $tax_rate['item_id'], 'label', true );
			if( !empty( $tax_rates[$key]['rate_id'] ) ) {
				$meta_sql = $wpdb->prepare( "SELECT `tax_rate_class` FROM `" . $wpdb->prefix . "woocommerce_tax_rates` WHERE `tax_rate_id` = %d LIMIT 1", $tax_rates[$key]['rate_id'] );
				$meta = $wpdb->get_var( $meta_sql );
				$tax_rates[$key]['class'] = $meta;
			}
		}
		return $tax_rates;
	}

}

function woo_ce_get_order_assoc_tax_rate_total( $order_id = 0, $tax_rate = 0 ) {

	global $wpdb;

	$order_item_type = 'tax';
	$meta_key = 'rate_id';
	$order_item_id_sql = $wpdb->prepare( "SELECT order_items.order_item_id FROM " . $wpdb->prefix . "woocommerce_order_items as order_items, " . $wpdb->prefix . "woocommerce_order_itemmeta as order_itemmeta WHERE order_items.order_item_id = order_itemmeta.order_item_id AND order_items.order_item_type = %s AND order_items.order_id = %d AND order_itemmeta.meta_key = %s AND order_itemmeta.meta_value = %d", $order_item_type, $order_id, $meta_key, $tax_rate );
	$order_item_id = $wpdb->get_var( $order_item_id_sql );
	if( !empty( $order_item_id ) ) {
		$amounts_sql = $wpdb->prepare( "SELECT SUM( meta_value ) FROM " . $wpdb->prefix . "woocommerce_order_itemmeta WHERE order_item_id = %d AND meta_key IN ( 'tax_amount', 'shipping_tax_amount' )", $order_item_id );
		$amounts = $wpdb->get_var( $amounts_sql );
		if( !empty( $amounts ) ) {
			return $amounts;
		}
	}

}

// Get the Order Item ID of refunded Order Items
function woo_ce_get_order_line_item_assoc_refunds( $line_item_id = 0 ) {

	global $wpdb;

	$order_item_type = 'line_item';
	$meta_key = '_refunded_item_id';
	$refund_items_sql = $wpdb->prepare( "SELECT order_itemmeta.`order_item_id` FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE order_items.`order_item_id` = order_itemmeta.`order_item_id` AND order_items.`order_item_type` = %s AND order_itemmeta.`meta_key` = %s AND order_itemmeta.`meta_value` = %d", $order_item_type, $meta_key, $line_item_id );
	$refund_items = $wpdb->get_col( $refund_items_sql );
	return $refund_items;

}

// Take our pretty slashed date format and make it play nice with strtotime() and date()
function woo_ce_format_order_date( $date = '' ) {

	$output = $date;
	if( !empty( $date ) )
		$output = str_replace( '/', '-', $date );
	return $output;

}

// Returns a list of WooCommerce Order statuses
function woo_ce_get_order_statuses() {

	$terms = false;
	// Check if this is a WooCommerce 2.2+ instance (new Post Status)
	$woocommerce_version = woo_get_woo_version();
	if( version_compare( $woocommerce_version, '2.2' ) >= 0 ) {
		// Convert Order Status array into our magic sauce
		$order_statuses = ( function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : false );
		if( !empty( $order_statuses ) ) {
			$terms = array();
			$post_type = 'shop_order';
			$posts_count = wp_count_posts( $post_type );
			foreach( $order_statuses as $key => $order_status ) {
				$terms[] = (object)array(
					'name' => $order_status,
					'slug' => $key,
					'count' => ( isset( $posts_count->$key ) ? $posts_count->$key : 0 )
				);
			}
		}
	} else {
		$args = array(
			'hide_empty' => false
		);
		$terms = get_terms( 'shop_order_status', $args );
		if( empty( $terms ) || ( is_wp_error( $terms ) == true ) )
			$terms = false;
	}
	return $terms;

}

// Returns the Shipping Method ID associated to a specific Order
function woo_ce_get_order_assoc_shipping_method_id( $order_id = 0 ) {

	global $export;

	if( class_exists( 'WC_Order' ) && !empty( $order_id ) ) {
		$output = '';
		$order = new WC_Order( $order_id );
		if( method_exists( 'WC_Order', 'get_shipping_methods' ) ) {
			if( $shipping_methods = $order->get_shipping_methods() ) {
				foreach( $shipping_methods as $shipping_item_id => $shipping_item ) {
					if( isset( $shipping_item['item_meta'] ) ) {
						$output = $shipping_item['item_meta']['method_id'];
						if( is_array( $output ) )
							$output = $output[0];
						break;
					}
				}
			}
			unset( $shipping_methods );
		}
		unset( $order );
		return apply_filters( 'woo_ce_get_order_assoc_shipping_method_id', $output );
	}

}

// Returns Download keys associated to a specified Order
function woo_ce_get_order_assoc_downloads( $order_id = 0 ) {

	global $wpdb;

	if( !empty( $order_id ) ) {
		$order_downloads_sql = $wpdb->prepare( "SELECT `download_id`, `download_count` FROM `" . $wpdb->prefix . "woocommerce_downloadable_product_permissions` WHERE `order_id` = %d", $order_id );
		$order_downloads = $wpdb->get_results( $order_downloads_sql );
		$output = array();
		if( !empty( $order_downloads ) ) {
			$output = $order_downloads;
		}
		unset( $order_downloads );
		return $output;
	}

}

// Returns Order Notes associated to a specific Order
function woo_ce_get_order_assoc_notes( $order_id = 0, $note_type = 'order_note' ) {

	global $wpdb;

	if( !empty( $order_id ) ) {
		$term_taxonomy = 'order_note';
		// @mod - The default get_comments() call is not working for returning Order Notes or Customer Notes, using database query
		$order_notes_sql = $wpdb->prepare( "SELECT `comment_ID`, `comment_date`, `comment_content` FROM `" . $wpdb->comments . "` WHERE `comment_type` = %s AND `comment_post_ID` = %d AND `comment_agent` = 'WooCommerce' AND `comment_approved` = 1", $term_taxonomy, $order_id );
		$order_notes = $wpdb->get_results( $order_notes_sql );
		$wpdb->flush();
		$output = array();
		if( !empty( $order_notes ) ) {
			foreach( $order_notes as $order_note ) {
				// Check if we are returning an order or customer note
				$order_note->comment_date = sprintf( apply_filters( 'woo_ce_get_order_assoc_notes_date', '%s %s' ), woo_ce_format_date( $order_note->comment_date ), mysql2date( 'H:i:s', $order_note->comment_date ) );
				if( $note_type == 'customer_note' ) {
					// Check if the order note is a customer one
					if( absint( get_comment_meta( $order_note->comment_ID, 'is_customer_note', true ) ) == 1 )
						$output[] = sprintf( apply_filters( 'woo_ce_get_order_assoc_notes_customer', '%s: %s' ), $order_note->comment_date, $order_note->comment_content );
				} else {
					// Check if the order note is a customer one
					if( absint( get_comment_meta( $order_note->comment_ID, 'is_customer_note', true ) ) == 0 )
						$output[] = sprintf( apply_filters( 'woo_ce_get_order_assoc_notes_order', '%s: %s' ), $order_note->comment_date, $order_note->comment_content );
				}
			}
		}
		return $output;
	}

}

function woo_ce_get_order_assoc_refund_date( $order_id = 0 ) {

	if( !empty( $order_id ) ) {
		$output = '';
		$post_type = 'shop_order_refund';
		$args = array(
			'post_type' => $post_type,
			'post_status' => 'wc-completed',
			'post_parent' => $order_id,
			'posts_per_page' => -1
		);
		$refunds = new WP_Query( $args );
		if( !empty( $refunds->posts ) ) {
			foreach( $refunds->posts as $refund ) {
				if( $refund->post_excerpt == __( 'Order Fully Refunded', 'woocommerce' ) ) {
					$output = woo_ce_format_date( $refund->post_date );
					break;
				}
			}
		}
		return $output;
	}

}

// Returns the Coupon Code associated to a specific Order
function woo_ce_get_order_assoc_coupon( $order_id = 0 ) {

	global $export;

	if( !empty( $order_id ) ) {
		$output = '';
		$order_item_type = 'coupon';
		if( class_exists( 'WC_Order' ) ) {
			$order = new WC_Order( $order_id );
			if( method_exists( $order, 'get_used_coupons' ) ) {
				if( $coupons = $order->get_used_coupons() ) {
					$size = count( $coupons );
					// If more than a single Coupon is assigned to this order then separate them
					if( $size > 1 )
						$output = implode( $export->category_separator, $coupons );
					else
						$output = $coupons[0];
				}
			}
		}
		return $output;
	}

}

function woo_ce_get_gravity_forms_products() {

	global $wpdb;

	$meta_key = '_gravity_form_data';
	$post_ids_sql = $wpdb->prepare( "SELECT `post_id`, `meta_value` FROM `$wpdb->postmeta` WHERE `meta_key` = %s GROUP BY `meta_value`", $meta_key );
	return $wpdb->get_results( $post_ids_sql );

}

function woo_ce_get_gravity_form_fields() {

	if( apply_filters( 'woo_ce_enable_addon_gravity_forms', true ) == false )
		return;

	if( $gf_products = woo_ce_get_gravity_forms_products() ) {
		$fields = array();
		foreach( $gf_products as $gf_product ) {
			if( $gf_product_data = maybe_unserialize( get_post_meta( $gf_product->post_id, '_gravity_form_data', true ) ) ) {
				// Check the class and method for Gravity Forms exists
				if( class_exists( 'RGFormsModel' ) && method_exists( 'RGFormsModel', 'get_form_meta' ) ) {
					// Check the form exists
					$gf_form_meta = RGFormsModel::get_form_meta( $gf_product_data['id'] );
					if( !empty( $gf_form_meta ) ) {
						// Check that the form has fields assigned to it
						if( !empty( $gf_form_meta['fields'] ) ) {
							foreach( $gf_form_meta['fields'] as $gf_form_field ) {
								// Check for duplicate Gravity Form fields
								$gf_form_field['formTitle'] = $gf_form_meta['title'];
								// Do not include page and section breaks, hidden as exportable fields
								if( !in_array( $gf_form_field['type'], array( 'page', 'section', 'hidden' ) ) )
									$fields[] = $gf_form_field;
							}
						}
					}
					unset( $gf_form_meta );
				}
			}
		}
		return $fields;
	}

}

function woo_ce_get_extra_product_option_fields( $order_item = 0 ) {

	global $wpdb, $export;

	// Check if we can use the existing data assigned to Order Items
	$meta_key = '_tmcartepo_data';
	$order_item_type = 'line_item';
	$tm_fields_sql = $wpdb->prepare( "SELECT order_itemmeta.`meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE order_items.`order_item_id` = order_itemmeta.`order_item_id` AND order_items.`order_item_type` = %s AND order_itemmeta.`meta_key` = %s", $order_item_type, $meta_key );

	// Limit scan to single Order Item if an Order Item ID is provided
	if( !empty( $order_item ) ) {
		$tm_fields_sql .= sprintf( " AND order_items.`order_item_id` = %d", $order_item );
	}

	// Limit scan of Order Items to Order IDs if provided
	if( !empty( $order_item ) && !empty( $export->order_ids ) ) {
		$order_ids = $export->order_ids;
		// Check if we're looking up a Sequential Order Number
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			if( isset( $export->order_ids_raw ) )
				$order_ids = $export->order_ids_raw;
		}
		// Check if it's an array
		if( is_array( $order_ids ) )
			$order_ids = implode( ',', $order_ids );
		$tm_fields_sql .= " AND order_items.`order_id` IN (" . $order_ids . ")";
		unset( $order_ids );
	}

	$tm_fields = $wpdb->get_col( $tm_fields_sql );
	if( !empty( $tm_fields ) ) {
		$fields = array();
		foreach( $tm_fields as $tm_field ) {
			$tm_field = maybe_unserialize( $tm_field );
			$size = count( $tm_field );
			for( $i = 0; $i < $size; $i++ ) {
				// Check that the name is set
				if( !empty( $tm_field[$i]['name'] ) ) {
					$tm_field[$i]['name'] = wp_specialchars_decode( $tm_field[$i]['name'], 'ENT_QUOTES' );
					// Check if we haven't already set this
					if( !array_key_exists( sanitize_key( $tm_field[$i]['name'] ), $fields ) )
						$fields[sanitize_key( $tm_field[$i]['name'] )] = $tm_field[$i];
				}
			}
		}
		return $fields;
	} else {
		// Fallback to scanning the individual Global Extra Product Options
		$post_type = 'tm_global_cp';
		$args = array(
			'post_type' => $post_type,
			'fields' => 'ids',
			'posts_per_page' => -1
		);
		$global_ids = new WP_Query( $args );
		if( !empty( $global_ids->posts ) ) {
			foreach( $global_ids->posts as $global_id )
				$meta = get_post_meta( $global_id, 'tm_meta', true );
		}
		unset( $global_ids, $global_id );
	}

}

function woo_ce_get_extra_product_option_value( $order_item = 0, $tm_field = array() ) {

	global $wpdb;

	$output = '';
	if( isset( $tm_field['name'] ) ) {
		$meta_sql = $wpdb->prepare( "SELECT `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `order_item_id` = %d AND `meta_key` = %s LIMIT 1", $order_item, $tm_field['name'] );
		$meta = $wpdb->get_var( $meta_sql );
		if( !empty( $meta ) ) {
			$output = $meta;
		} else {
			$output = $tm_field['value'];
		}
	}
	return $output;

}

function woo_ce_get_order_assoc_booking_id( $order_id ) {

	// Run a WP_Query to return the Post ID of the Booking
	$post_type = 'wc_booking';
	$args = array(
		'post_type' => $post_type,
		'post_parent' => $order_id,
		'fields' => 'ids',
		'posts_per_page' => 1
	);
	$booking_ids = new WP_Query( $args );
	if( !empty( $booking_ids->posts ) )
		return $booking_ids->posts[0];
	unset( $booking_ids );

}

function woo_ce_max_order_items( $orders = array() ) {

	$output = 0;
	if( $orders ) {
		foreach( $orders as $order ) {
			if( $order->order_items )
				$output = count( $order->order_items[0]->name );
		}
	}
	return $output;

}

// Returns a list of Order Item ID's with the order_item_type of 'line item' for a specified Order
function woo_ce_get_order_item_ids( $order_id = 0 ) {

	global $wpdb;

	if( !empty( $order_id ) ) {
		$order_item_type = 'line_item';
		$order_items_sql = $wpdb->prepare( "SELECT order_items.`order_item_id` as id, order_itemmeta.`meta_value` as product_id FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE order_items.`order_item_id` = order_itemmeta.`order_item_id` AND order_items.`order_id` = %d AND order_items.`order_item_type` = %s AND order_itemmeta.`meta_key` IN ('_product_id')", $order_id, $order_item_type );
		if( $order_items = $wpdb->get_results( $order_items_sql ) )
			return $order_items;
	}

}

// Returns a list of Order Items for a specified Order
function woo_ce_get_order_items( $order_id = 0, $order_items_types = array() ) {

	global $export, $wpdb;

	if( !empty( $order_id ) ) {
		$order_items_sql = $wpdb->prepare( "SELECT `order_item_id` as id, `order_item_name` as name, `order_item_type` as type FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id` = %d", $order_id );
		if( $order_items = $wpdb->get_results( $order_items_sql ) ) {
			$wpdb->flush();

			foreach( $order_items as $key => $order_item ) {

				// Default to Line Item for empty Order Item types
				if( empty( $order_items_types ) )
					$order_items_types = array( 'line_item' );

				// Filter Order Item types from Orders export
				if( !in_array( $order_item->type, $order_items_types ) ) {
					unset( $order_items[$key] );
					continue;
				}

				$order_item_meta_sql = $wpdb->prepare( "SELECT `meta_key`, `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `order_item_id` = %d ORDER BY meta_key ASC", $order_item->id );
				if( $order_item_meta = $wpdb->get_results( $order_item_meta_sql ) ) {
					$order_items[$key]->product_id = '';
					$order_items[$key]->variation_id = '';
					$order_items[$key]->sku = '';
					$order_items[$key]->description = '';
					$order_items[$key]->excerpt = '';
					$order_items[$key]->variation = '';
					$order_items[$key]->quantity = '';
					$order_items[$key]->total = '';
					$order_items[$key]->subtotal = '';
					$order_items[$key]->rrp = '';
					$order_items[$key]->stock = '';
					$order_items[$key]->tax = '';
					$order_items[$key]->tax_subtotal = '';
					$order_items[$key]->tax_class = '';
					$order_items[$key]->category = '';
					$order_items[$key]->tag = '';
					$order_items[$key]->total_sales = '';
					$order_items[$key]->weight = '';
					$order_items[$key]->height = '';
					$order_items[$key]->width = '';
					$order_items[$key]->length = '';
					$order_items[$key]->total_weight = '';
					$size = count( $order_item_meta );
					for( $i = 0; $i < $size; $i++ ) {

						// Go through each Order Item meta found
						switch( $order_item_meta[$i]->meta_key ) {

							case '_qty':
								$order_items[$key]->quantity = $order_item_meta[$i]->meta_value;
								break;

							case '_product_id':
								if( $order_items[$key]->product_id = $order_item_meta[$i]->meta_value ) {
									$product = get_post( $order_items[$key]->product_id );
									if( $product !== null ) {
										$order_items[$key]->description = woo_ce_format_description_excerpt( $product->post_content );
										$order_items[$key]->excerpt = woo_ce_format_description_excerpt( $product->post_excerpt );
									}
									unset( $product );
									$order_items[$key]->sku = get_post_meta( $order_items[$key]->product_id, '_sku', true );
									$order_items[$key]->category = woo_ce_get_product_assoc_categories( $order_items[$key]->product_id );
									$order_items[$key]->tag = woo_ce_get_product_assoc_tags( $order_items[$key]->product_id );
									$order_items[$key]->total_sales = get_post_meta( $order_items[$key]->product_id, 'total_sales', true );
									$order_items[$key]->weight = get_post_meta( $order_items[$key]->product_id, '_weight', true );
									$order_items[$key]->height = get_post_meta( $order_items[$key]->product_id, '_height', true );
									$order_items[$key]->width = get_post_meta( $order_items[$key]->product_id, '_width', true );
									$order_items[$key]->length = get_post_meta( $order_items[$key]->product_id, '_length', true );
									$order_items[$key]->rrp = get_post_meta( $order_items[$key]->product_id, '_price', true );
									if( isset( $order_items[$key]->rrp ) && $order_items[$key]->rrp != '' )
										$order_items[$key]->rrp = woo_ce_format_price( $order_items[$key]->rrp );
									$order_items[$key]->stock = get_post_meta( $order_items[$key]->product_id, '_stock', true );
									$order_items[$key]->stock = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $order_items[$key]->stock ) : $order_items[$key]->stock );
									// Override Variable with total stock quantity
									$term_taxonomy = 'product_type';
									if( has_term( 'variable', $term_taxonomy, $order_items[$key]->product_id ) ) {
										$_product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $order_items[$key]->product_id ) : false );
										$order_items[$key]->stock = ( method_exists( $_product, 'get_total_stock' ) ? $_product->get_total_stock() : $order_items[$key]->stock );
										unset( $_product );
									}
								}
								break;

							case '_variation_id':
								$order_items[$key]->variation = '';
								if( $order_items[$key]->variation_id = $order_item_meta[$i]->meta_value ) {
									// Check if the Variation SKU is set and default to the Product SKU if it is empty
									$variation_sku = get_post_meta( $order_items[$key]->variation_id, '_sku', true );
									if( !empty( $variation_sku ) )
										$order_items[$key]->sku = $variation_sku;
									unset( $variation_sku );
									$order_items[$key]->weight = get_post_meta( $order_items[$key]->variation_id, '_weight', true );
									$order_items[$key]->height = get_post_meta( $order_items[$key]->variation_id, '_height', true );
									$order_items[$key]->width = get_post_meta( $order_items[$key]->variation_id, '_width', true );
									$order_items[$key]->length = get_post_meta( $order_items[$key]->variation_id, '_length', true );
									$variations_sql = "SELECT `meta_key` FROM `" . $wpdb->postmeta . "` WHERE `post_id` = " . $order_items[$key]->variation_id . " AND `meta_key` LIKE 'attribute_pa_%' ORDER BY `meta_key` ASC";
									// Check if the variation has a taxonomy
									if( $variations = $wpdb->get_col( $variations_sql ) ) {
										$attributes = woo_ce_get_product_attributes();
										foreach( $variations as $variation ) {

											$variation = str_replace( 'attribute_pa_', '', $variation );
											foreach( $attributes as $attribute ) {
												if( $attribute->attribute_name == $variation ) {
													if( empty( $attribute->attribute_label ) )
														$attribute->attribute_label = $attribute->attribute_name;
													$variation_label = $attribute->attribute_label;
													break;
												}
											}
											$slug = get_post_meta( $order_items[$key]->variation_id, sprintf( 'attribute_pa_%s', $variation ), true );
											$term_taxonomy = sprintf( 'pa_%s', $variation );
											if( taxonomy_exists( $term_taxonomy ) ) {
												$term = get_term_by( 'slug', $slug, $term_taxonomy );
												if( $term && !is_wp_error( $term ) )
													$order_items[$key]->variation .= sprintf( apply_filters( 'woo_ce_get_order_items_variation_taxonomy', '%s: %s' ), $variation_label, $term->name ) . "|";
											}

										}
										unset( $variations, $variation, $variation_label, $slug, $term_taxonomy, $term );
										$order_items[$key]->variation = substr( $order_items[$key]->variation, 0, -1 );
									} else {
										// Check for per-Product variations that are not linked to a taxonomy
										$variations_sql = "SELECT `meta_key` FROM `" . $wpdb->postmeta . "` WHERE `post_id` = " . $order_items[$key]->variation_id . " AND `meta_key` LIKE 'attribute_%' ORDER BY `meta_key` ASC";
										if( $variations = $wpdb->get_col( $variations_sql ) ) {
											foreach( $variations as $variation ) {
												$variation = str_replace( 'attribute_', '', $variation );
												$attribute = get_post_meta( $order_items[$key]->product_id, '_product_attributes', true );
												$variation_label = '';
												if( !empty( $attribute ) ) {
													if( isset( $attribute[$variation] ) )
														$variation_label = $attribute[$variation]['name'];
												}
												$slug = get_post_meta( $order_items[$key]->variation_id, sprintf( 'attribute_%s', $variation ), true );
												if( !empty( $slug ) && !empty( $variation_label ) )
													$order_items[$key]->variation .= sprintf( apply_filters( 'woo_ce_get_order_items_variation_custom', '%s: %s' ), $variation_label, ucwords( $slug ) ) . "\n";
											}
											$order_items[$key]->variation = substr( $order_items[$key]->variation, 0, -1 );
											unset( $variations, $variation, $attribute, $slug );
										}
									}
								}
								break;

							case '_tax_class':
								$order_items[$key]->tax_class = woo_ce_format_order_item_tax_class( $order_item_meta[$i]->meta_value );
								break;

							case '_line_subtotal':
								$order_items[$key]->subtotal = woo_ce_format_price( $order_item_meta[$i]->meta_value );
								break;

							case '_line_subtotal_tax':
								$order_items[$key]->tax_subtotal = woo_ce_format_price( $order_item_meta[$i]->meta_value );
								break;

							case '_line_total':
								$order_items[$key]->total = woo_ce_format_price( $order_item_meta[$i]->meta_value );
								break;

							case '_line_tax':
								$order_items[$key]->tax = woo_ce_format_price( $order_item_meta[$i]->meta_value );
								break;

							// This is for Order Item tax meta, we can safely ignore
							case '_line_tax_data':
								continue;
								break;

							// This is for any custom Order Item meta
							default:
								$order_items[$key] = apply_filters( 'woo_ce_order_item_custom_meta', $order_items[$key], $order_item_meta[$i]->meta_key, $order_item_meta[$i]->meta_value );
								break;

						}
					}
				}
				unset( $order_item_meta );

				if( !empty( $order_items[$key]->tax_class ) ) {
					// Tax Rates
					$tax_rates = woo_ce_get_order_tax_rates();
					if( !empty( $tax_rates ) ) {
						foreach( $tax_rates as $tax_rate ) {
							if( $tax_rate['class'] == $order_items[$key]->tax_class ) {
								$order_items[$key]->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )} = woo_ce_format_price( $order_items[$key]->tax_subtotal );
								break;
							}
						}
					}
					unset( $tax_rates );
				}

				// Default the quantity to 1 for the Fee Order Item Type
				if( $order_items[$key]->type == 'fee' )
					$order_items[$key]->quantity = 1;

				$order_items[$key]->type_id = $order_items[$key]->type;
				$order_items[$key] = apply_filters( 'woo_ce_order_item', $order_items[$key], $order_id );
				$order_items[$key]->type = woo_ce_format_order_item_type( $order_items[$key]->type );
				$order_items[$key]->total_weight = ( $order_items[$key]->weight <> '' ? $order_items[$key]->weight * $order_items[$key]->quantity : '' );

			}

			return $order_items;

		}
	}

}

// Returns a list of WooCommerce Order Item Types
function woo_ce_get_order_items_types() {

	$order_item_types = array(
		'line_item' => __( 'Line Item', 'woocommerce-exporter' ),
		'coupon' => __( 'Coupon', 'woocommerce-exporter' ),
		'fee' => __( 'Fee', 'woocommerce-exporter' ),
		'tax' => __( 'Tax', 'woocommerce-exporter' ),
		'shipping' => __( 'Shipping', 'woocommerce-exporter' )
	);
	$order_item_types = apply_filters( 'woo_ce_order_item_types', $order_item_types );
	return $order_item_types;

}

// Populate Order details for export of 3rd party Plugins
function woo_ce_order_extend( $order, $order_id ) {

	// WooCommerce Sequential Order Numbers - http://www.skyverge.com/blog/woocommerce-sequential-order-numbers/
	if( class_exists( 'WC_Seq_Order_Number' ) ) {
		// Override the Purchase ID if this Plugin exists and Post meta isn't empty
		$order_number = get_post_meta( $order_id, '_order_number', true );
		if( !empty( $order_id ) )
			$order->purchase_id = $order_number;
		unset( $order_number );
	}

	// Sequential Order Numbers Pro - http://www.woothemes.com/products/sequential-order-numbers-pro/
	if( class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
		// Override the Purchase ID if this Plugin exists and Post meta isn't empty
		$order_number = get_post_meta( $order_id, '_order_number_formatted', true );
		if( !empty( $order_id ) )
			$order->purchase_id = $order_number;
		unset( $order_number );
	}

	// WooCommerce Jetpack - https://wordpress.org/plugins/woocommerce-jetpack/
	// WooCommerce Jetpack Plus - http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/
	if( class_exists( 'WC_Jetpack' ) || class_exists( 'WC_Jetpack_Plus' ) ) {
		// Use WooCommerce Jetpack Plus's display_order_number() to handle formatting
		if( class_exists( 'WCJ_Order_Numbers' ) ) {
			$order_numbers = new WCJ_Order_Numbers();
			$order->purchase_id = $order_numbers->display_order_number( $order_id, $order );
			unset( $order_numbers );
		} else {
			// Fall-back to old school get_post_meta()
			$order_number = get_post_meta( $order_id, '_wcj_order_number', true );
			// Override the Purchase ID if this Plugin exists and Post meta isn't empty
			if( !empty( $order_number ) && get_option( 'wcj_order_numbers_enabled', 'no' ) !== 'no' )
				$order->purchase_id = $order_number;
			unset( $order_number );
		}
	}

	// WooCommerce Basic Ordernumbers - http://open-tools.net/woocommerce/advanced-ordernumbers-for-woocommerce.html
	if( class_exists( 'OpenToolsOrdernumbersBasic' ) ) {
		$order_number = get_post_meta( $order_id, '_oton_number_ordernumber', true );
		// Override the Purchase ID if this Plugin exists and Post meta isn't empty
		if( !empty( $order_number ) && get_option( 'customize_ordernumber', 'no' ) !== 'no' )
			$order->purchase_id = $order_number;
		unset( $order_number );
	}

	// WooCommerce Checkout Manager - http://wordpress.org/plugins/woocommerce-checkout-manager/
	// WooCommerce Checkout Manager Pro - http://www.trottyzone.com/product/woocommerce-checkout-manager-pro
	if( function_exists( 'wccs_install' ) || function_exists( 'wccs_install_pro' ) ) {
		// Load generic settings
		$options = get_option( 'wccs_settings' );
		if( isset( $options['buttons'] ) ) {
			$buttons = $options['buttons'];
			if( !empty( $buttons ) ) {
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					// Check that we are not overriding an existing field
					if( isset( $button['cow'] ) && !isset( $order->{$button['cow']} ) )
						$order->{$button['cow']} = woo_ce_format_custom_meta( get_post_meta( $order_id, $button['cow'], true ) );
				}
				unset( $buttons, $button );
			}
		}
		unset( $options );
		// Load Shipping settings
		$options = get_option( 'wccs_settings2' );
		if( isset( $options['shipping_buttons'] ) ) {
			$buttons = $options['shipping_buttons'];
			if( !empty( $buttons ) ) {
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					// Check that we are not overriding an existing field
					if( isset( $button['cow'] ) && !isset( $order->{sprintf( 'shipping_%s', $button['cow'] )} ) )
						$order->{sprintf( 'shipping_%s', $button['cow'] )} = woo_ce_format_custom_meta( get_post_meta( $order_id, sprintf( '_shipping_%s', $button['cow'] ), true ) );
				}
				unset( $buttons, $button );
			}
		}
		unset( $options );
		// Load Billing settings
		$options = get_option( 'wccs_settings3' );
		if( isset( $options['billing_buttons'] ) ) {
			$buttons = $options['billing_buttons'];
			if( !empty( $buttons ) ) {
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					// Check that we are not overriding an existing field
					if( isset( $button['cow'] ) && !isset( $order->{sprintf( 'billing_%s', $button['cow'] )} ) )
						$order->{sprintf( 'billing_%s', $button['cow'] )} = woo_ce_format_custom_meta( get_post_meta( $order_id, sprintf( '_billing_%s', $button['cow'] ), true ) );
				}
				unset( $buttons, $button );
			}
		}
		unset( $options );
	}

	// Poor Guys Swiss Knife - http://wordpress.org/plugins/woocommerce-poor-guys-swiss-knife/
	if( function_exists( 'wcpgsk_init' ) ) {
		$options = get_option( 'wcpgsk_settings' );
		$billing_fields = ( isset( $options['woofields']['billing'] ) ? $options['woofields']['billing'] : array() );
		$shipping_fields = ( isset( $options['woofields']['shipping'] ) ? $options['woofields']['shipping'] : array() );
		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field )
				$order->$key = get_post_meta( $order_id, sprintf( '_%s', $key ), true );
			unset( $billing_fields, $billing_field );
		}
		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field )
				$order->$key = get_post_meta( $order_id, sprintf( '_%s', $key ), true );
			unset( $shipping_fields, $shipping_field );
		}
		unset( $options );
	}

	// Checkout Field Editor - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_init_checkout_field_editor' ) ) {
		$billing_fields = get_option( 'wc_fields_billing', array() );
		$shipping_fields = get_option( 'wc_fields_shipping', array() );
		$additional_fields = get_option( 'wc_fields_additional', array() );
		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				// Only add non-default Checkout fields to export columns list
				if( $billing_field['custom'] == 1 ) {
					$billing_field['value'] = get_post_meta( $order_id, $key, true );
					if( $billing_field['value'] != '' ) {
						if( $billing_field['type'] == 'checkbox' )
							$order->{sprintf( 'wc_billing_%s', $key )} = $billing_field['value'] == '1' ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' );
						else
							$order->{sprintf( 'wc_billing_%s', $key )} = $billing_field['value'];
					}
				}
			}
		}
		unset( $billing_fields, $billing_field );

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				// Only add non-default Checkout fields to export columns list
				if( $shipping_field['custom'] == 1 ) {
					$shipping_field['value'] = get_post_meta( $order_id, $key, true );
					if( $shipping_field['value'] != '' ) {
						if( $shipping_field['type'] == 'checkbox' )
							$order->{sprintf( 'wc_shipping_%s', $key )} = $shipping_field['value'] == '1' ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' );
						else
							$order->{sprintf( 'wc_shipping_%s', $key )} = $shipping_field['value'];
					}
				}
			}
		}
		unset( $shipping_fields, $shipping_field );

		// Additional fields
		if( !empty( $additional_fields ) ) {
			foreach( $additional_fields as $key => $additional_field ) {
				// Only add non-default Checkout fields to export columns list
				if( $additional_field['custom'] == 1 ) {
					$additional_field['value'] = get_post_meta( $order_id, $key, true );
					if( $additional_field['value'] != '' ) {
						if( $additional_field['type'] == 'checkbox' )
							$order->{sprintf( 'wc_additional_%s', $key )} = $additional_field['value'] == '1' ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' );
						else
							$order->{sprintf( 'wc_additional_%s', $key )} = $additional_field['value'];
					}
				}
			}
		}
		unset( $additional_fields, $additional_field );
	}

	// Checkout Field Manager - http://61extensions.com
	if( function_exists( 'sod_woocommerce_checkout_manager_settings' ) ) {
		// Custom billing fields
		$billing_fields = get_option( 'woocommerce_checkout_billing_fields', array() );
		$shipping_fields = get_option( 'woocommerce_checkout_shipping_fields', array() );
		$custom_fields = get_option( 'woocommerce_checkout_additional_fields', array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $billing_field['default_field'] ) != 'on' ) {
					$billing_field['value'] = get_post_meta( $order_id, sprintf( '_%s', $billing_field['name'] ), true );
					if( $billing_field['value'] != '' ) {
						// Override for the checkbox field type
						if( $billing_field['type'] == 'checkbox' )
							$order->{sprintf( 'sod_billing_%s', $billing_field['name'] )} = strtolower( $billing_field['value'] == 'on' ) ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' );
						else
							$order->{sprintf( 'sod_billing_%s', $billing_field['name'] )} = $billing_field['value'];
					}
				}
			}
		}
		unset( $billing_fields, $billing_field );

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $shipping_field['default_field'] ) != 'on' ) {
					$shipping_field['value'] = get_post_meta( $order_id, sprintf( '_%s', $shipping_field['name'] ), true );
					if( $shipping_field['value'] != '' ) {
						// Override for the checkbox field type
						if( $shipping_field['type'] == 'checkbox' )
							$order->{sprintf( 'sod_shipping_%s', $shipping_field['name'] )} = strtolower( $shipping_field['value'] == 'on' ) ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' );
						else
							$order->{sprintf( 'sod_shipping_%s', $shipping_field['name'] )} = $shipping_field['value'];
					}
				}
			}
		}
		unset( $shipping_fields, $shipping_field );

		// Custom fields
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $key => $custom_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $custom_field['default_field'] ) != 'on' ) {
					$custom_field['value'] = get_post_meta( $order_id, '_' . $custom_field['name'], true );
					if( $custom_field['value'] != '' ) {
						// Override for the checkbox field type
						if( $custom_field['type'] == 'checkbox' )
							$order->{sprintf( 'sod_additional_%s', $custom_field['name'] )} = strtolower( $custom_field['value'] == 'on' ) ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' );
						else
							$order->{sprintf( 'sod_additional_%s', $custom_field['name'] )} = $custom_field['value'];
					}
				}
			}
		}
		unset( $custom_fields, $custom_field );
	}

	// WooCommerce Print Invoice & Delivery Note - https://wordpress.org/plugins/woocommerce-delivery-notes/
	if( class_exists( 'WooCommerce_Delivery_Notes' ) ) {
		if( function_exists( 'wcdn_get_order_invoice_number' ) )
			$order->invoice_number = wcdn_get_order_invoice_number( $order_id );
		if( function_exists( 'wcdn_get_order_invoice_date' ) )
			$order->invoice_date = wcdn_get_order_invoice_date( $order_id );
	}

	// WooCommerce PDF Invoices & Packing Slips - http://www.wpovernight.com
	if( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
		// Check if the PDF Invoice has been generated
		$invoice_exists = get_post_meta( $order_id, '_wcpdf_invoice_exists', true );
		if( !empty( $invoice_exists ) ) {
			// Check if the Invoice Number formatting Class is available
			if( class_exists( 'WooCommerce_PDF_Invoices_Export' ) ) {
				$wcpdf = new WooCommerce_PDF_Invoices_Export();
				$order->pdf_invoice_number = $wcpdf->get_invoice_number( $order_id );
				unset( $wcpdf );
			} else {
				$order->pdf_invoice_number = get_post_meta( $order_id, '_wcpdf_invoice_number', true );
			}
			$invoice_date = get_post_meta( $order_id, '_wcpdf_invoice_date', true );
			$order->pdf_invoice_date = date_i18n( get_option( 'date_format' ), strtotime( $invoice_date ) );
		}
		unset( $invoice_exists, $invoice_date );
	}

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( class_exists( 'WooCommerce_HearAboutUs' ) ) {
		$source = get_post_meta( $order_id, 'source', true );
		if( $source == '' )
			$source = __( 'N/A', 'woocommerce-exporter' );
		$order->hear_about_us = $source;
		unset( $source );
	}

	// WooCommerce Uploads - https://wpfortune.com/shop/plugins/woocommerce-uploads/
	if( class_exists( 'WPF_Uploads' ) ) {
		$uploaded_files = get_post_meta( $order_id, '_wpf_umf_uploads', true );
		if( !empty( $uploaded_files ) ) {
			$order->uploaded_files = '';
			$order->uploaded_files_thumbnail = '';
			foreach( $uploaded_files as $uploaded_files_product_id ) {
				if( !empty( $uploaded_files_product_id ) ) {
					foreach( $uploaded_files_product_id as $uploaded_files_product_item_number ) {
						if( !empty( $uploaded_files_product_item_number ) ) {
							foreach( $uploaded_files_product_item_number as $uploaded_files_upload_type ) {
								if( !empty( $uploaded_files_upload_type ) ) {
									foreach( $uploaded_files_upload_type as $uploaded_files_file_number ) {
										if( !empty( $uploaded_files_file_number ) ) {

											// Check we have a path to work with
											if( !empty( $uploaded_files_file_number['path'] ) ) {
												// Check the path exists
												if( file_exists( $uploaded_files_file_number['path'] ) ) {
													// Convert the file path into a URL
													$uploaded_files_file_number['path'] = str_replace( ABSPATH, '', $uploaded_files_file_number['path'] );
													$uploaded_files_file_number['path'] = home_url( $uploaded_files_file_number['path'] );
													$order->uploaded_files .= $uploaded_files_file_number['path'] . "\n";
												}
											}

											// Check we have a thumbnail to work with
											if( !empty( $uploaded_files_file_number['thumb'] ) ) {
												// Check the path exists
												if( file_exists( $uploaded_files_file_number['thumb'] ) ) {
													// Convert the file path into a URL
													$uploaded_files_file_number['thumb'] = str_replace( ABSPATH, '', $uploaded_files_file_number['thumb'] );
													$uploaded_files_file_number['thumb'] = home_url( $uploaded_files_file_number['thumb'] );
													$order->uploaded_files_thumbnail .= $uploaded_files_file_number['thumb'] . "\n";
												}
											}

										}
									}
								}
							}
						}
					}
				}
			}
			unset( $uploaded_files_product_id, $uploaded_files_product_item_number, $uploaded_files_upload_type, $uploaded_files_file_number );
		}
		unset( $uploaded_files );
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$order->cost_of_goods = woo_ce_format_price( get_post_meta( $order_id, '_wc_cog_order_total_cost', true ), $order->order_currency );
	}

	// WooCommerce Ship to Multiple Addresses - http://woothemes.com/woocommerce
	if( class_exists( 'WC_Ship_Multiple' ) ) {
		$shipping_packages = get_post_meta( $order_id, '_wcms_packages', true );
		if( !empty( $shipping_packages ) ) {
			$order->wcms_number_packages = count( $shipping_packages );
		}
		unset( $shipping_packages );
	}

	// WooCommerce EU VAT Number - http://woothemes.com/woocommerce
	if( function_exists( '__wc_eu_vat_number_init' ) ) {
		$vat_id = get_post_meta( $order_id, '_vat_number', true );
		$order->eu_vat = $vat_id;
		$order->eu_vat_b2b = ( !empty( $vat_id ) ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' ) );
		if( !empty( $vat_id ) ) {
			if( get_post_meta( $order_id, '_vat_number_is_validated', true ) !== 'true' ) {
				$order->eu_vat_validated = __( 'Not possible', 'woocommerce-exporter' );
			} else {
				$order->eu_vat_validated = ( get_post_meta( $order_id, '_vat_number_is_valid', true ) === 'true' ? __( 'Yes', 'woocommerce-exporter' ) : __( 'No', 'woocommerce-exporter' ) );
			}
		}
		unset( $vat_id );
	}

	// WooCommerce EU VAT Assistant - https://wordpress.org/plugins/woocommerce-eu-vat-assistant/
	if( class_exists( 'Aelia_WC_RequirementsChecks' ) ) {
		$order->eu_vat = get_post_meta( $order_id, 'vat_number', true );
		$order->eu_vat_country = get_post_meta( $order_id, '_vat_country', true );
		$order->eu_vat_validated = get_post_meta( $order_id, '_vat_number_validated', true );
	}

	// WooCommerce Custom Admin Order Fields - http://www.woothemes.com/products/woocommerce-admin-custom-order-fields/
	if( function_exists( 'init_woocommerce_admin_custom_order_fields' ) ) {
		$ac_fields = get_option( 'wc_admin_custom_order_fields' );
		if( !empty( $ac_fields ) ) {
			foreach( $ac_fields as $ac_key => $ac_field ) {
				$order->{sprintf( 'wc_acof_%d', $ac_key )} = get_post_meta( $order_id, sprintf( '_wc_acof_%d', $ac_key ), true );
			}
		}
	}

	// WooCommerce Extra Checkout Fields for Brazil - https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/
	if( class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
		$order->billing_cpf = get_post_meta( $order_id, '_billing_cpf', true );
		$order->billing_rg = get_post_meta( $order_id, '_billing_rg', true );
		$order->billing_cnpj = get_post_meta( $order_id, '_billing_cnpj', true );
		$order->billing_ie = get_post_meta( $order_id, '_billing_ie', true );
		$order->billing_birthdate = get_post_meta( $order_id, '_billing_birthdate', true );
		$order->billing_sex = get_post_meta( $order_id, '_billing_sex', true );
		$order->billing_number = get_post_meta( $order_id, '_billing_number', true );
		$order->billing_neighborhood = get_post_meta( $order_id, '_billing_neighborhood', true );
		$order->billing_cellphone = get_post_meta( $order_id, '_billing_cellphone', true );
		$order->shipping_number = get_post_meta( $order_id, '_shipping_number', true );
		$order->shipping_neighborhood = get_post_meta( $order_id, '_shipping_neighborhood', true );
	}

	// WooCommerce Quick Donation - http://wordpress.org/plugins/woocommerce-quick-donation/
	if( class_exists( 'WooCommerce_Quick_Donation' ) ) {

		global $wpdb;

		// Check the wc_quick_donation table exists
		if( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->prefix . "wc_quick_donation'" ) ) {
			$project_id_sql = $wpdb->prepare( "SELECT `projectid` FROM `" . $wpdb->prefix . "wc_quick_donation` WHERE `donationid` = %d LIMIT 1", $order_id );
			$order->project_id = absint( $wpdb->get_var( $project_id_sql ) );
			$order->project_name = get_the_title( $order->project_id );
		}
	}

	// WooCommerce Easy Checkout Fields Editor - http://codecanyon.net/item/woocommerce-easy-checkout-field-editor/9799777
	if( function_exists( 'pcmfe_admin_form_field' ) ) {
		$custom_fields = get_option( 'pcfme_additional_settings' );
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $key => $custom_field ) {
				$order->{$key} = get_post_meta( $order_id, $key, true );
			}
		}
	}

	// WooCommerce Events - http://www.woocommerceevents.com/
	if( class_exists( 'WooCommerce_Events' ) ) {
		$count = false;
		$tickets_purchased = get_post_meta( $order_id, 'WooCommerceEventsTicketsPurchased', true );
		if( !empty( $tickets_purchased ) ) {
			$tickets_purchased = json_decode( $tickets_purchased );
			if( !empty( $tickets_purchased ) ) {
				foreach( $tickets_purchased as $ticket_product )
					$count += $ticket_product;
			}
		}
		$order->tickets_purchased = $count;
		unset( $tickets_purchased, $count );
	}

	return $order;

}
add_filter( 'woo_ce_order', 'woo_ce_order_extend', 10, 2 );

function woo_ce_extend_order_item_custom_meta( $order_item, $meta_key = '', $meta_value = '' ) {

	global $export;

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields( $order_item->id ) ) {
			foreach( $tm_fields as $tm_field )
				$order_item->{sprintf( 'tm_%s', sanitize_key( $tm_field['name'] ) )} = woo_ce_get_extra_product_option_value( $order_item->id, $tm_field );
		}
		unset( $tm_fields, $tm_field );
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( woo_ce_get_gravity_forms_products() ) {
		$meta_type = 'order_item';
		$gravity_forms_history = get_metadata( $meta_type, $order_item->id, '_gravity_forms_history', true );
		// Check that Gravity Forms Order item meta isn't empty
		if( !empty( $gravity_forms_history ) ) {
			if( isset( $gravity_forms_history['_gravity_form_data'] ) ) {
				$order_item->gf_form_id = ( isset( $gravity_forms_history['_gravity_form_data']['id'] ) ? $gravity_forms_history['_gravity_form_data']['id'] : 0 );
				if( $order_item->gf_form_id ) {
					$gravity_form = ( method_exists( 'RGFormsModel', 'get_form' ) ? RGFormsModel::get_form( $gravity_forms_history['_gravity_form_data']['id'] ) : array() );
					$order_item->gf_form_label = ( !empty( $gravity_form ) ? $gravity_form->title : '' );
				}
			}
		}
	}

	// Product Add-ons - http://www.woothemes.com/
	if( $product_addons = woo_ce_get_product_addons() ) {
		foreach( $product_addons as $product_addon ) {
			if( strpos( $meta_key, $product_addon->post_name ) !== false ) {
				// Check if this Product Addon has already been set
				if( isset( $order_item->product_addons[$product_addon->post_name] ) ) {
					// Append the new result to the existing value (likely a checkbox, multiple select, etc.)
					$order_item->product_addons[$product_addon->post_name] .= $export->category_separator . $meta_value;
					// Append the option price to the new value
					$order_item->product_addons[$product_addon->post_name] .= str_replace( $product_addon->post_name, '', $meta_key );
				} else {
					// Otherwise make a new one
					$order_item->product_addons[$product_addon->post_name] = $meta_value;
					// Append the option price to the value
					$order_item->product_addons[$product_addon->post_name] .= str_replace( $product_addon->post_name, '', $meta_key );
				}
			}
		}
	}

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		$meta_type = 'fee';
		if( in_array( $meta_key, array( '_wc_checkout_add_on_label', '_wc_checkout_add_on_value' ) ) )
			$meta_value = maybe_unserialize( $meta_value );
		if( $meta_key == '_wc_checkout_add_on_id' )
			$order_item->checkout_addon_id = absint( $meta_value );
		if( $meta_key == '_wc_checkout_add_on_label' )
			$order_item->checkout_addon_label = ( is_array( $meta_value ) ? implode( $export->category_separator, $meta_value ) : $meta_value );
		if( $meta_key == '_wc_checkout_add_on_value' ) {
			$order_item->checkout_addon_value = ( is_array( $meta_value ) ? implode( $export->category_separator, $meta_value ) : $meta_value );
		}
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) ) {
		$meta_type = 'order_item';
		if( $meta_key == 'Pickup Location' )
			$order_item->pickup_location = $meta_value;
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$meta_type = 'order_item';
		if( $meta_key == 'Booking Date' )
			$order_item->booking_date = $meta_value;
		if( $meta_key == 'Booking Type' )
			$order_item->booking_type = $meta_value;
	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $order_item;

}
add_filter( 'woo_ce_order_item_custom_meta', 'woo_ce_extend_order_item_custom_meta', 10, 3 );

function woo_ce_extend_order_item( $order_item = array(), $order_id = 0 ) {

	global $export;

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Check for the Refund Line Item
	$order_item->refund_subtotal = 0;
	$order_item->refund_quantity = 0;
	if( $refunds = woo_ce_get_order_line_item_assoc_refunds( $order_item->id ) ) {
		$refund_subtotal = 0;
		$refund_quantity = 0;
		foreach( $refunds as $refund ) {
			switch( $order_item->type_id ) {

				case 'shipping':
					$refund_subtotal += wc_get_order_item_meta( $refund, '_cost' );
					break;

				default:
					$refund_subtotal += wc_get_order_item_meta( $refund, '_line_total' );
					break;

			}
			$refund_quantity += wc_get_order_item_meta( $refund, '_qty' );
		}
		$order_item->refund_subtotal = woo_ce_format_price( $refund_subtotal );
		$order_item->refund_quantity = $refund_quantity;
		unset( $refund_subtotal, $refund_quantity, $refunds, $refund );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() )
		$order_item->brand = woo_ce_get_product_assoc_brands( $order_item->product_id );

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) )
		$order_item->vendor = woo_ce_get_product_assoc_product_vendors( $order_item->product_id );

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$meta_type = 'order_item';
		$order_item->cost_of_goods = woo_ce_format_price( get_metadata( $meta_type, $order_item->id, '_wc_cog_item_cost', true ) );
		$order_item->total_cost_of_goods = woo_ce_format_price( get_metadata( $meta_type, $order_item->id, '_wc_cog_item_total_cost', true ) );
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$order_item->msrp = woo_ce_format_price( get_post_meta( $order_item->product_id, '_msrp_price', true ) );
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields( $order_item->id ) ) {
			$meta_type = 'order_item';
			foreach( $tm_fields as $tm_field ) {
				// Check if we have already populated this
				if( isset( $order_item->{sprintf( 'tm_%s', sanitize_key( $tm_field['name'] ) )} ) )
					break;
				$order_item->{sprintf( 'tm_%s', sanitize_key( $tm_field['name'] ) )} = woo_ce_get_extra_product_option_value( $order_item->id, $tm_field );
			}
		}
	}
	unset( $tm_fields, $tm_field );

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$meta_type = 'order_item';
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						$meta_value = get_metadata( $meta_type, $order_item->id, sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) ), true );
						if( $meta_value !== false )
							$order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} = $meta_value;
					}
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		$order_item->order_items_barcode_type = get_post_meta( $order_item->product_id, '_barcode_type', true );
		$order_item->order_items_barcode = get_post_meta( $order_item->product_id, '_barcode', true );
	}

	// Attributes
	if( !empty( $order_item->variation_id ) ) {
		if( $attributes = woo_ce_get_product_attributes() ) {
			$meta_type = 'order_item';
			foreach( $attributes as $attribute ) {
				// Fetch the Taxonomy Attribute value
				$meta_value = get_metadata( $meta_type, $order_item->id, sprintf( 'pa_%s', $attribute->attribute_name ), true );
				if( $meta_value == false ) {
					// Fallback to non-Taxonomy Attribute value
					$meta_value = get_metadata( $meta_type, $order_item->id, $attribute->attribute_name, true );
					if( $meta_value !== false )
						$order_item->{'attribute_' . $attribute->attribute_name} = $meta_value;
				} else {
					$term_taxonomy = 'pa_' . $attribute->attribute_name;
					if( taxonomy_exists( $term_taxonomy ) ) {
						$term = get_term_by( 'slug', $meta_value, $term_taxonomy );
						if( $term && !is_wp_error( $term ) )
							$order_item->{'attribute_' . $attribute->attribute_name} = $term->name;
					}
				}
			}
		}
	}
	unset( $attributes, $attribute );

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		$meta_type = 'order_item';
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				// Check if this Custom Order Item has already been set
				if( isset( $order_item->{$custom_order_item} ) ) {
					// Append the new result to the existing value (likely a checkbox, multiple select, etc.)
					$order_item->{$custom_order_item} .= $export->category_separator . implode( $export->category_separator, (array)get_metadata( $meta_type, $order_item->id, $custom_order_item, true ) );
				} else {
					// Otherwise make a new one
					$order_item->{$custom_order_item} = woo_ce_format_custom_meta( get_metadata( $meta_type, $order_item->id, $custom_order_item, true ) );
				}
			}
		}
	}
	unset( $custom_order_items, $custom_order_item );

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		$meta_type = 'order_item';
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$order_item->{$custom_product} = woo_ce_format_custom_meta( get_post_meta( $order_item->product_id, $custom_product, true ) );
			}
		}
	}
	unset( $custom_products, $custom_product );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $order_item;

}
add_filter( 'woo_ce_order_item', 'woo_ce_extend_order_item', 10, 2 );

function woo_ce_extend_order_items_unique_fields_exclusion( $excluded_fields = array(), $fields = '' ) {

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Product Add-ons - http://www.woothemes.com/
	if( $product_addons = woo_ce_get_product_addons() ) {
		foreach( $product_addons as $product_addon ) {
			if( isset( $fields[sprintf( 'order_items_product_addon_%s', $product_addon->post_name )] ) )
				$excluded_fields[] = sprintf( 'order_items_product_addon_%s', $product_addon->post_name );
		}
		unset( $product_addons, $product_addon );
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( $gf_fields = woo_ce_get_gravity_form_fields() ) {
		if( isset( $fields['order_items_gf_form_id'] ) )
			$excluded_fields[] = 'order_items_gf_form_id';
		if( isset( $fields['order_items_gf_form_label'] ) )
			$excluded_fields[] = 'order_items_gf_form_label';
		foreach( $gf_fields as $gf_field ) {
			if( isset( $fields[sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )] ) )
				$excluded_fields[] = sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] );
		}
	}
	unset( $gf_fields, $gf_field );

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		if( isset( $fields['order_items_checkout_addon_id'] ) )
			$excluded_fields[] = 'order_items_checkout_addon_id';
		if( isset( $fields['order_items_checkout_addon_label'] ) )
			$excluded_fields[] = 'order_items_checkout_addon_label';
		if( isset( $fields['order_items_checkout_addon_value'] ) )
			$excluded_fields[] = 'order_items_checkout_addon_value';
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() ) {
		if( isset( $fields['order_items_brand'] ) )
			$excluded_fields[] = 'order_items_brand';
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		if( isset( $fields['order_items_vendor'] ) )
			$excluded_fields[] = 'order_items_vendor';
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		if( isset( $fields['order_items_cost_of_goods'] ) )
			$excluded_fields[] = 'order_items_cost_of_goods';
		if( isset( $fields['order_items_total_cost_of_goods'] ) )
			$excluded_fields[] = 'order_items_total_cost_of_goods';
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		if( isset( $fields['order_items_msrp'] ) )
			$excluded_fields[] = 'order_items_msrp';
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) ) {
		if( isset( $fields['order_items_pickup_location'] ) )
			$excluded_fields[] = 'order_items_pickup_location';
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		if( isset( $fields['order_items_booking_id'] ) )
			$excluded_fields[] = 'order_items_booking_id';
		if( isset( $fields['order_items_booking_date'] ) )
			$excluded_fields[] = 'order_items_booking_date';
		if( isset( $fields['order_items_booking_type'] ) )
			$excluded_fields[] = 'order_items_booking_type';
		if( isset( $fields['order_items_booking_start_date'] ) )
			$excluded_fields[] = 'order_items_booking_start_date';
		if( isset( $fields['order_items_booking_end_date'] ) )
			$excluded_fields[] = 'order_items_booking_end_date';
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields() ) {
			foreach( $tm_fields as $tm_field ) {
				if( isset( $fields[sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )] ) )
					$excluded_fields[] = sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) );
			}
		}
		unset( $tm_fields, $tm_field );
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						if( isset( $fields[sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )] ) )
							$excluded_fields[] = sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) );
					}
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		if( isset( $fields['order_items_barcode_type'] ) )
			$excluded_fields[] = 'order_items_barcode_type';
		if( isset( $fields['order_items_barcode'] ) )
			$excluded_fields[] = 'order_items_barcode';
	}

	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			if( isset( $fields[sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )] ) )
				$excluded_fields[] = sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] );
		}
	}
	unset( $tax_rates, $tax_rate );

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( isset( $fields[sprintf( 'order_items_attribute_%s', $attribute->attribute_name )] ) )
				$excluded_fields[] = sprintf( 'order_items_attribute_%s', $attribute->attribute_name );
		}
	}
	unset( $attributes, $attribute );

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				if( isset( $fields['order_items_' . $custom_order_item] ) )
					$excluded_fields[] = 'order_items_' . $custom_order_item;
			}
		}
	}
	unset( $custom_order_items, $custom_order_item );

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( isset( $fields['order_items_' . $custom_product] ) )
				$excluded_fields[] = 'order_items_' . $custom_product;
		}
	}
	unset( $custom_products, $custom_product );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $excluded_fields;

}
add_filter( 'woo_ce_add_unique_order_item_fields_exclusion', 'woo_ce_extend_order_items_unique_fields_exclusion', 10, 2 );

// Order items formatting: Combined
function woo_ce_extend_order_items_combined( $order ) {

	global $export;

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Product Add-ons - http://www.woothemes.com/
	$product_addons = woo_ce_get_product_addons();
	if( $product_addons && $order->order_items ) {
		foreach( $product_addons as $product_addon ) {
			foreach( $order->order_items as $order_item ) {
				if( isset( $order_item->product_addons[$product_addon->post_name] ) )
					$order->{'order_items_product_addon_' . $product_addon->post_name} .= $order_item->product_addons[$product_addon->post_name] . $export->category_separator;
			}
			if( isset( $order->{'order_items_product_addon_' . $product_addon->post_name} ) )
				$order->{'order_items_product_addon_' . $product_addon->post_name} = substr( $order->{'order_items_product_addon_' . $product_addon->post_name}, 0, -1 );
		}
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	$gf_fields = woo_ce_get_gravity_form_fields();
	if( $gf_fields && $order->order_items ) {
		$meta_type = 'order_item';
		$order->order_items_gf_form_id = '';
		$order->order_items_gf_form_label = '';
		foreach( $order->order_items as $order_item ) {
			$gravity_forms_history = get_metadata( $meta_type, $order_item->id, '_gravity_forms_history', true );
			// Check that Gravity Forms Order item meta isn't empty
			if( !empty( $gravity_forms_history ) ) {
				if( isset( $gravity_forms_history['_gravity_form_data'] ) ) {
					$order->order_items_gf_form_id .= $gravity_forms_history['_gravity_form_data']['id'] . $export->category_separator;
					$gravity_form = ( method_exists( 'RGFormsModel', 'get_form' ) ? RGFormsModel::get_form( $gravity_forms_history['_gravity_form_data']['id'] ) : array() );
					$order->order_items_gf_form_label .= ( !empty( $gravity_form ) ? $gravity_form->title : '' ) . $export->category_separator;
					unset( $gravity_form );
				}
			}
			foreach( $gf_fields as $gf_field ) {
				// Check that we only fill export fields for forms that are actually filled
				if( $gf_field['formId'] == $gravity_forms_history['_gravity_form_data']['id'] )
					$order->{sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )} .= get_metadata( $meta_type, $order_item->id, $gf_field['label'], true ) . $export->category_separator;
			}
			unset( $gf_fields, $gf_field, $gravity_forms_history );
		}
		if( isset( $order->order_items_gf_form_id ) )
			$order->order_items_gf_form_id = substr( $order->order_items_gf_form_id, 0, -1 );
		if( isset( $order->order_items_gf_form_label ) )
			$order->order_items_gf_form_label = substr( $order->order_items_gf_form_label, 0, -1 );
		if( isset( $order->{sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )} ) )
			$order->{sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )} = substr( $order->{sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )}, 0, -1 );
	}

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) && $order->order_items ) {
		$meta_type = 'order_item';
		foreach( $order->order_items as $order_item ) {
			$order->order_items_checkout_addon_id .= $order_item->checkout_addon_id . $export->category_separator;
			$order->order_items_checkout_addon_label .= $order_item->checkout_addon_label . $export->category_separator;
			$order->order_items_checkout_addon_value .= $order_item->checkout_addon_value . $export->category_separator;
		}
		if( isset( $order->order_items_checkout_addon_id ) )
			$order->order_items_checkout_addon_id = substr( $order->order_items_checkout_addon_id, 0, -1 );
		if( isset( $order->order_items_checkout_addon_label ) )
			$order->order_items_checkout_addon_label = substr( $order->order_items_checkout_addon_label, 0, -1 );
		if( isset( $order->order_items_checkout_addon_value ) )
			$order->order_items_checkout_addon_value = substr( $order->order_items_checkout_addon_value, 0, -1 );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() && $order->order_items ) {
		$meta_type = 'order_item';
		foreach( $order->order_items as $order_item )
			$order->order_items_brand .= woo_ce_get_product_assoc_brands( $order_item->product_id ) . $export->category_separator;
		if( isset( $order->order_items_brand ) )
			$order->order_items_brand = substr( $order->order_items_brand, 0, -1 );
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) && $order->order_items ) {
		$meta_type = 'order_item';
		foreach( $order->order_items as $order_item )
			$order->order_items_vendor = woo_ce_get_product_assoc_product_vendors( $order_item->product_id ) . $export->category_separator;
		if( isset( $order->order_items_vendor ) )
			$order->order_items_vendor = substr( $order->order_items_vendor, 0, -1 );
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) && $order->order_items ) {
		$meta_type = 'order_item';
		foreach( $order->order_items as $order_item ) {
			$order->order_items_cost_of_goods .= woo_ce_format_price( get_metadata( $meta_type, $order_item->id, '_wc_cog_item_cost', true ), $order->order_currency ) . $export->category_separator;
			$order->order_items_total_cost_of_goods .= woo_ce_format_price( get_metadata( $meta_type, $order_item->id, '_wc_cog_item_total_cost', true ), $order->order_currency ) . $export->category_separator;
		}
		if( isset( $order->order_items_cost_of_goods ) )
			$order->order_items_cost_of_goods = substr( $order->order_items_cost_of_goods, 0, -1 );
		if( isset( $order->order_items_total_cost_of_goods ) )
			$order->order_items_total_cost_of_goods = substr( $order->order_items_total_cost_of_goods, 0, -1 );
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) && $order->order_items ) {
		foreach( $order->order_items as $order_item ) {
			$order->order_items_msrp .= woo_ce_format_price( get_post_meta( $order_item->product_id, '_msrp_price', true ) ) . $export->category_separator;
		}
		if( isset( $order->order_items_msrp ) )
			$order->order_items_msrp = substr( $order->order_items_msrp, 0, -1 );
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) && $order->order_items ) {
		$meta_type = 'order_item';
		$order->order_items_pickup_location = '';
		foreach( $order->order_items as $order_item ) {
			$pickup_location = get_metadata( $meta_type, $order_item->id, 'Pickup Location', true );
			if( !empty( $pickup_location ) )
				$order->order_items_pickup_location .= get_metadata( $meta_type, $order_item->id, 'Pickup Location', true ) . $export->category_separator;
			unset( $pickup_location );
		}
		if( isset( $order->order_items_pickup_location ) )
			$order->order_items_pickup_location = substr( $order->order_items_pickup_location, 0, -1 );
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) && $order->order_items ) {
		$meta_type = 'order_item';
		$order->order_items_booking_id = '';
		$order->order_items_booking_date = '';
		$order->order_items_booking_type = '';
		$order->order_items_booking_start_date = '';
		$order->order_items_booking_end_date = '';
		foreach( $order->order_items as $order_item ) {
			$booking_id = woo_ce_get_order_assoc_booking_id( $order->id );
			if( !empty( $booking_id ) ) {
				$order->order_items_booking_id .= $booking_id . $export->category_separator;
				$booking_start_date = get_post_meta( $booking_id, '_booking_start', true );
				if( !empty( $booking_start_date ) )
					$order->order_items_start_date .= woo_ce_format_date( date( 'Y-m-d', strtotime( $booking_start_date ) ) ) . $export->category_separator;
				unset( $booking_start_date );
				$booking_end_date = get_post_meta( $booking_id, '_booking_end', true );
				if( !empty( $booking_end_date ) )
					$order->order_items_booking_end_date .= woo_ce_format_date( date( 'Y-m-d', strtotime( $booking_end_date ) ) ) . $export->category_separator;
				unset( $booking_end_date );
			}
			unset( $booking_id );
			$booking_date = get_metadata( $meta_type, $order_item->id, 'Booking Date', true );
			if( !empty( $booking_date ) )
				$order->order_items_booking_date .= get_metadata( $meta_type, $order_item->id, 'Booking Date', true ) . $export->category_separator;
			unset( $booking_date );
			$booking_type = get_metadata( $meta_type, $order_item->id, 'Booking Type', true );
			if( !empty( $booking_type ) )
				$order->order_items_booking_type .= get_metadata( $meta_type, $order_item->id, 'Booking Type', true ) . $export->category_separator;
			unset( $booking_type );
		}
		if( isset( $order->order_items_booking_id ) )
			$order->order_items_booking_id = substr( $order->order_items_booking_id, 0, -1 );
		if( isset( $order->order_items_booking_date ) )
			$order->order_items_booking_date = substr( $order->order_items_booking_date, 0, -1 );
		if( isset( $order->order_items_booking_type ) )
			$order->order_items_booking_type = substr( $order->order_items_booking_type, 0, -1 );
		if( isset( $order->order_items_booking_start_date ) )
			$order->order_items_booking_start_date = substr( $order->order_items_booking_start_date, 0, -1 );
		if( isset( $order->order_items_booking_end_date ) )
			$order->order_items_booking_end_date = substr( $order->order_items_booking_end_date, 0, -1 );
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) && $order->order_items ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields() ) {
			foreach( $tm_fields as $tm_field )
				$order->{'order_items_tm_' . sanitize_key( $tm_field['name'] )} = '';
		}
		foreach( $order->order_items as $order_item ) {
			$tm_fields = woo_ce_get_extra_product_option_fields( $order_item->id );
			foreach( $tm_fields as $tm_field ) {
				if( isset( $order_item->{'tm_' . sanitize_key( $tm_field['name'] )} ) )
					$order->{sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )} .= woo_ce_get_extra_product_option_value( $order_item->id, $tm_field ) . $export->category_separator;
			}
		}
		if( $tm_fields = woo_ce_get_extra_product_option_fields() ) {
			foreach( $tm_fields as $tm_field ) {
				if( isset( $order->{sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )} ) )
					$order->{sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )} = substr( $order->{sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )}, 0, -1 );
			}
		}
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$meta_type = 'order_item';
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						$order->{sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )} = '';
					}
					foreach( $order->order_items as $order_item ) {
						foreach( $custom_fields as $custom_field ) {
							if( isset( $order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} ) )
								$order->{sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )} .= $order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} . $export->category_separator;
						}
					}
					foreach( $custom_fields as $custom_field ) {
						if( isset( $order->{sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )} ) )
							$order->{sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )} = substr( $order->{sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )}, 0, -1 );
					}
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		$order->order_items_barcode_type = '';
		$order->order_items_barcode = '';
		foreach( $order->order_items as $order_item ) {
			$order->order_items_barcode_type .= get_post_meta( $order_item->product_id, '_barcode_type', true ) . $export->category_separator;
			$order->order_items_barcode .= get_post_meta( $order_item->product_id, '_barcode', true ) . $export->category_separator;
		}
		if( isset( $order->order_items_barcode_type ) )
			$order->order_items_barcode_type = substr( $order->order_items_barcode_type, 0, -1 );
		if( isset( $order->order_items_barcode ) )
			$order->order_items_barcode = substr( $order->order_items_barcode, 0, -1 );
	}

	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate )
			$order->{sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )} = '';
		foreach( $order->order_items as $order_item ) {
			foreach( $tax_rates as $tax_rate ) {
				if( isset( $order_item->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )} ) )
					$order->{sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )} = $order_item->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )};
			}
		}
		foreach( $tax_rates as $tax_rate ) {
			if( isset( $order->{sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )} ) )
				$order->{sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )} = substr( $order->{sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )}, 0, -1 );
		}
		unset( $tax_rates, $tax_rate );
	}

	// Attributes
	$attributes = woo_ce_get_product_attributes();
	if( $attributes && $order->order_items ) {
		foreach( $attributes as $attribute )
			$order->{'order_items_attribute_' . sanitize_key( $attribute->attribute_name )} = '';
		foreach( $order->order_items as $order_item ) {
			foreach( $attributes as $attribute ) {
				if( isset( $order_item->{'attribute_' . $attribute->attribute_name} ) )
					$order->{'order_items_attribute_' . sanitize_key( $attribute->attribute_name )} .= $order_item->{'attribute_' . $attribute->attribute_name} . $export->category_separator;
			}
		}
		foreach( $attributes as $attribute ) {
			if( isset( $order->{'order_items_attribute_' . sanitize_key( $attribute->attribute_name )} ) )
				$order->{'order_items_attribute_' . sanitize_key( $attribute->attribute_name )} = substr( $order->{'order_items_attribute_' . sanitize_key( $attribute->attribute_name )}, 0, -1 );
		}
		unset( $attributes, $attribute );
	}

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) && $order->order_items ) {
		foreach( $custom_order_items as $custom_order_item )
			$order->{'order_items_' . $custom_order_item} = '';
		foreach( $order->order_items as $order_item ) {
			foreach( $custom_order_items as $custom_order_item ) {
				if( !empty( $custom_order_item ) )
					$order->{'order_items_' . $custom_order_item} .= $order_item->{$custom_order_item} . $export->category_separator;
			}
		}
		foreach( $custom_order_items as $custom_order_item ) {
			if( isset( $order->{'order_items_' . $custom_order_item} ) )
				$order->{'order_items_' . $custom_order_item} = substr( $order->{'order_items_' . $custom_order_item}, 0, -1 );
		}
	}

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product )
			$order->{'order_items_' . $custom_product} = '';
		foreach( $order->order_items as $order_item ) {
			foreach( $custom_products as $custom_product ) {
				if( !empty( $custom_product ) )
					$order->{'order_items_' . $custom_product} .= $order_item->{$custom_product} . $export->category_separator;
			}
		}
		foreach( $custom_products as $custom_product ) {
			if( isset( $order->{'order_items_' . $custom_product} ) )
				$order->{'order_items_' . $custom_product} = substr( $order->{'order_items_' . $custom_product}, 0, -1 );
		}
	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $order;

}
add_filter( 'woo_ce_order_items_combined', 'woo_ce_extend_order_items_combined' );

// Order items formatting: Unique
function woo_ce_extend_order_items_unique( $order, $i = 0, $order_item = array() ) {

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Product Add-ons - http://www.woothemes.com/
	if( $product_addons = woo_ce_get_product_addons() ) {
		foreach( $product_addons as $product_addon ) {
			if( isset( $order_item->product_addons[$product_addon->post_name] ) )
				$order->{sprintf( 'order_item_%d_product_addon_%s', $i, $product_addon->post_name )} = $order_item->product_addons[$product_addon->post_name];
		}
		unset( $product_addons, $product_addon );
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( $gf_fields = woo_ce_get_gravity_form_fields() ) {
		if( $order->order_items ) {
			$meta_type = 'order_item';
			foreach( $order->order_items as $order_item ) {
				$order->{sprintf( 'order_item_%d_gf_form_id', $i )} = ( isset( $order_item->gf_form_id ) ? $order_item->gf_form_id : false );
				$order->{sprintf( 'order_item_%d_gf_form_label', $i )} = ( isset( $order_item->gf_form_label ) ? $order_item->gf_form_label : false );
				foreach( $gf_fields as $gf_field ) {
					// Check that we only fill export fields for forms that are actually filled
					if( isset( $order_item->gf_form_id ) ) {
						if( $gf_field['formId'] == $order_item->gf_form_id )
							$order->{sprintf( 'order_item_%d_gf_%d_%s', $i, $gf_field['formId'], $gf_field['id'] )} = get_metadata( $meta_type, $order_item->id, $gf_field['label'], true );
					}
				}
			}
		}
		unset( $gf_fields, $gf_field );
	}

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		$order->{sprintf( 'order_item_%d_checkout_addon_id', $i )} = ( isset( $order_item->checkout_addon_id ) ? $order_item->checkout_addon_id : false );
		$order->{sprintf( 'order_item_%d_checkout_addon_label', $i )} = ( isset( $order_item->checkout_addon_label ) ? $order_item->checkout_addon_label : false );
		$order->{sprintf( 'order_item_%d_checkout_addon_value', $i )} = ( isset( $order_item->checkout_addon_value ) ? $order_item->checkout_addon_value : false );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() )
		$order->{sprintf( 'order_item_%d_brand', $i )} = $order_item->brand;

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) )
		$order->{sprintf( 'order_item_%d_vendor', $i )} = $order_item->vendor;

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$order->{sprintf( 'order_item_%d_cost_of_goods', $i )} = $order_item->cost_of_goods;
		$order->{sprintf( 'order_item_%d_total_cost_of_goods', $i )} = $order_item->total_cost_of_goods;
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$order->{sprintf( 'order_item_%d_msrp', $i )} = $order_item->msrp;
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) )
		$order->{sprintf( 'order_item_%d_pickup_location', $i )} = $order_item->pickup_location;

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$order->{sprintf( 'order_item_%d_booking_id', $i )} = $order_item->booking_id;
		$order->{sprintf( 'order_item_%d_booking_date', $i )} = $order_item->booking_date;
		$order->{sprintf( 'order_item_%d_booking_type', $i )} = $order_item->booking_type;
		$order->{sprintf( 'order_item_%d_booking_start_date', $i )} = $order_item->booking_start_date;
		$order->{sprintf( 'order_item_%d_booking_end_date', $i )} = $order_item->booking_end_date;
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields( $order_item->id ) ) {
			foreach( $tm_fields as $tm_field ) {
				if( isset( $order_item->{sprintf( 'tm_%s', sanitize_key( $tm_field['name'] ) )} ) )
					$order->{sprintf( 'order_item_%d_tm_%s', $i, sanitize_key( $tm_field['name'] ) )} = woo_ce_get_extra_product_option_value( $order_item->id, $tm_field );
			}
		}
		unset( $tm_fields, $tm_field );
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field )
						$order->{sprintf( 'order_item_%d_wccf_%s', $i, sanitize_key( $custom_field['key'] ) )} = ( isset( $order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} ) ? $order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} : false );
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		$order->{sprintf( 'order_item_%d_barcode_type', $i )} = $order_item->barcode_type;
		$order->{sprintf( 'order_item_%d_barcode', $i )} = $order_item->barcode;
	}

	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			if( isset( $order_item->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )} ) )
				$order->{sprintf( 'order_item_%d_tax_rate_%d', $i, $tax_rate['rate_id'] )} = $order_item->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )};
		}
		unset( $tax_rates, $tax_rate );
	}

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( isset( $order_item->{'attribute_' . sanitize_key( $attribute->attribute_name )} ) )
				$order->{sprintf( 'order_item_%d_attribute_%s', $i, sanitize_key( $attribute->attribute_name ) )} = $order_item->{'attribute_' . sanitize_key( $attribute->attribute_name )};
		}
		unset( $attributes, $attribute );
	}

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				if( isset( $order_item->{$custom_order_item} ) )
					$order->{sprintf( 'order_item_%d_%s', $i, $custom_order_item )} = $order_item->{$custom_order_item};
			}
		}
	}

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				if( isset( $order_item->{$custom_product} ) )
					$order->{sprintf( 'order_item_%d_%s', $i, $custom_product )} = $order_item->{$custom_product};
			}
		}
	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $order;

}
add_filter( 'woo_ce_order_items_unique', 'woo_ce_extend_order_items_unique', 10, 3 );

// Order items formatting: Individual
function woo_ce_extend_order_items_individual( $order, $order_item ) {

	global $export;

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Product Add-ons - http://www.woothemes.com/
	if( $product_addons = woo_ce_get_product_addons() ) {
		foreach( $product_addons as $product_addon ) {
			if( isset( $order_item->product_addons[$product_addon->post_name] ) )
				$order->{'order_items_product_addon_' . $product_addon->post_name} = $order_item->product_addons[$product_addon->post_name];
		}
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( $gf_fields = woo_ce_get_gravity_form_fields() ) {
		if( $order->order_items ) {
			$order->order_items_gf_form_id = ( isset( $order_item->gf_form_id ) ? $order_item->gf_form_id : false ); 
			$order->order_items_gf_form_label = ( isset( $order_item->gf_form_label ) ? $order_item->gf_form_label : false );
			$meta_type = 'order_item';
			foreach( $gf_fields as $gf_field ) {
				// Check that we only fill export fields for forms that are actually filled
				if( isset( $order_item->gf_form_id ) ) {
					if( $gf_field['formId'] == $order_item->gf_form_id )
						$order->{sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )} = get_metadata( $meta_type, $order_item->id, $gf_field['label'], true );
				}
			}
			unset( $gf_fields, $gf_field );
		}
	}

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		$order->order_items_checkout_addon_id = ( isset( $order_item->checkout_addon_id ) ? $order_item->checkout_addon_id : false );
		$order->order_items_checkout_addon_label = ( isset( $order_item->checkout_addon_label ) ? $order_item->checkout_addon_label : false );
		$order->order_items_checkout_addon_value = ( isset( $order_item->checkout_addon_value ) ? $order_item->checkout_addon_value : false );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() )
		$order->order_items_brand = $order_item->brand;

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) )
		$order->order_items_vendor = $order_item->vendor;

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$order->order_items_cost_of_goods = $order_item->cost_of_goods;
		$order->order_items_total_cost_of_goods = $order_item->total_cost_of_goods;
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) )
		$order->order_items_msrp = $order_item->msrp;

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) ) {
		$meta_type = 'order_item';
		$pickup_location = get_metadata( $meta_type, $order_item->id, 'Pickup Location', true );
		if( !empty( $pickup_location ) )
			$order->order_items_pickup_location = get_metadata( $meta_type, $order_item->id, 'Pickup Location', true );
		unset( $pickup_location );
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$booking_id = woo_ce_get_order_assoc_booking_id( $order->id );
		if( !empty( $booking_id ) ) {
			$order->order_items_booking_id = $booking_id;
			$booking_start_date = get_post_meta( $booking_id, '_booking_start', true );
			if( !empty( $booking_start_date ) )
				$order->order_items_booking_start_date = woo_ce_format_date( date( 'Y-m-d', strtotime( $booking_start_date ) ) );
			unset( $booking_start_date );
			$booking_end_date = get_post_meta( $booking_id, '_booking_end', true );
			if( !empty( $booking_end_date ) )
				$order->order_items_booking_end_date = woo_ce_format_date( date( 'Y-m-d', strtotime( $booking_end_date ) ) );
			unset( $booking_end_date );
		}
		unset( $booking_id );
		$meta_type = 'order_item';
		$booking_date = get_metadata( $meta_type, $order_item->id, 'Booking Date', true );
		if( !empty( $booking_date ) )
			$order->order_items_booking_date = get_metadata( $meta_type, $order_item->id, 'Booking Date', true );
		unset( $booking_date );
		$booking_type = get_metadata( $meta_type, $order_item->id, 'Booking Type', true );
		if( !empty( $booking_type ) )
			$order->order_items_booking_type = get_metadata( $meta_type, $order_item->id, 'Booking Type', true );
		unset( $booking_type );
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields( $order_item->id ) ) {
			foreach( $tm_fields as $tm_field ) {
				if( isset( $order_item->{sprintf( 'tm_%s', sanitize_key( $tm_field['name'] ) )} ) )
					$order->{sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )} = woo_ce_get_extra_product_option_value( $order_item->id, $tm_field );
			}
		}
		unset( $tm_fields, $tm_field );
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field )
						$order->{sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )} = ( isset( $order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} ) ? $order_item->{sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) )} : false );
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			if( isset( $order_item->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )} ) )
				$order->{sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )} = $order_item->{sprintf( 'tax_rate_%d', $tax_rate['rate_id'] )};
		}
		unset( $tax_rates, $tax_rate );
	}

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( isset( $order_item->{'attribute_' . sanitize_key( $attribute->attribute_name )} ) )
				$order->{'order_items_attribute_' . sanitize_key( $attribute->attribute_name )} = $order_item->{'attribute_' . sanitize_key( $attribute->attribute_name )};
		}
		unset( $attributes, $attribute );
	}

	// WooCommerce Ship to Multiple Addresses - http://woothemes.com/woocommerce
	if( class_exists( 'WC_Ship_Multiple' ) ) {
		$shipping_packages = get_post_meta( $order->ID, '_wcms_packages', true );
		if( !empty( $shipping_packages ) ) {

			// Override the Shipping address
			$order->shipping_first_name = '';
			$order->shipping_last_name = '';
			if( empty( $order->shipping_first_name ) && empty( $order->shipping_first_name ) )
				$order->shipping_full_name = '';
			else
				$order->shipping_full_name = '';
			$order->shipping_company = '';
			$order->shipping_address = '';
			$order->shipping_address_1 = '';
			$order->shipping_address_2 = '';
			$order->shipping_city = '';
			$order->shipping_postcode = '';
			$order->shipping_state = '';
			$order->shipping_country = '';
			$order->shipping_state_full = '';
			$order->shipping_country_full = '';

			// Override the shipping method

			foreach( $shipping_packages as $shipping_package ) {
				$contents = $shipping_package['contents'];
				if( !empty( $contents ) ) {
					foreach( $contents as $content ) {
						if( $content['product_id'] == $order_item->product_id ) {
							$order->shipping_first_name = $shipping_package['full_address']['first_name'];
							$order->shipping_last_name = $shipping_package['full_address']['last_name'];
							if( empty( $order->shipping_first_name ) && empty( $order->shipping_last_name ) )
								$order->shipping_full_name = '';
							else
								$order->shipping_full_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
							$order->shipping_company = $shipping_package['full_address']['company'];
							$order->shipping_address = '';
							$order->shipping_address_1 = $shipping_package['full_address']['address_1'];
							$order->shipping_address_2 = $shipping_package['full_address']['address_2'];
							if( !empty( $order->billing_address_2 ) )
								$order->shipping_address = sprintf( apply_filters( 'woo_ce_get_order_data_shipping_address', '%s %s' ), $order->shipping_address_1, $order->shipping_address_2 );
							else
								$order->shipping_address = $order->shipping_address_1;
							$order->shipping_city = $shipping_package['full_address']['city'];
							$order->shipping_postcode = $shipping_package['full_address']['postcode'];
							$order->shipping_state = $shipping_package['full_address']['state'];
							$order->shipping_country = $shipping_package['full_address']['country'];
							$order->shipping_state_full = woo_ce_expand_state_name( $order->shipping_country, $order->shipping_state );
							$order->shipping_country_full = woo_ce_expand_country_name( $order->shipping_country );
							break;
							break;
						}
					}
				}
				unset( $contents );
			}

		}
		unset( $shipping_packages );
	}

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) )
				$order->{'order_items_' . $custom_order_item} = $order_item->{$custom_order_item};
		}
	}
	unset( $custom_order_items, $custom_order_item );

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) )
				$order->{'order_items_' . $custom_product} = $order_item->{$custom_product};
		}
	}
	unset( $custom_products, $custom_product );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $order;

}
add_filter( 'woo_ce_order_items_individual', 'woo_ce_extend_order_items_individual', 10, 2 );

// Order items formatting: Unique
function woo_ce_unique_order_item_fields( $fields = array() ) {

	$max_size = woo_ce_get_option( 'max_order_items', 10 );
	if( !empty( $fields ) ) {
		// Tack on a extra digit to max_size so we get the correct number of columns
		$max_size++;
		for( $i = 1; $i < $max_size; $i++ ) {
			if( isset( $fields['order_items_id'] ) )
				$fields[sprintf( 'order_item_%d_id', $i )] = 'on';
			if( isset( $fields['order_items_product_id'] ) )
				$fields[sprintf( 'order_item_%d_product_id', $i )] = 'on';
			if( isset( $fields['order_items_variation_id'] ) )
				$fields[sprintf( 'order_item_%d_variation_id', $i )] = 'on';
			if( isset( $fields['order_items_sku'] ) )
				$fields[sprintf( 'order_item_%d_sku', $i )] = 'on';
			if( isset( $fields['order_items_name'] ) )
				$fields[sprintf( 'order_item_%d_name', $i )] = 'on';
			if( isset( $fields['order_items_variation'] ) )
				$fields[sprintf( 'order_item_%d_variation', $i )] = 'on';
			if( isset( $fields['order_items_description'] ) )
				$fields[sprintf( 'order_item_%d_description', $i )] = 'on';
			if( isset( $fields['order_items_excerpt'] ) )
				$fields[sprintf( 'order_item_%d_excerpt', $i )] = 'on';
			if( isset( $fields['order_items_tax_class'] ) )
				$fields[sprintf( 'order_item_%d_tax_class', $i )] = 'on';
			if( isset( $fields['order_items_quantity'] ) )
				$fields[sprintf( 'order_item_%d_quantity', $i )] = 'on';
			if( isset( $fields['order_items_total'] ) )
				$fields[sprintf( 'order_item_%d_total', $i )] = 'on';
			if( isset( $fields['order_items_subtotal'] ) )
				$fields[sprintf( 'order_item_%d_subtotal', $i )] = 'on';
			if( isset( $fields['order_items_rrp'] ) )
				$fields[sprintf( 'order_item_%d_rrp', $i )] = 'on';
			if( isset( $fields['order_items_stock'] ) )
				$fields[sprintf( 'order_item_%d_stock', $i )] = 'on';
			if( isset( $fields['order_items_tax'] ) )
				$fields[sprintf( 'order_item_%d_tax', $i )] = 'on';
			if( isset( $fields['order_items_tax_subtotal'] ) )
				$fields[sprintf( 'order_item_%d_tax_subtotal', $i )] = 'on';
			if( isset( $fields['order_items_refund_subtotal'] ) )
				$fields[sprintf( 'order_item_%d_refund_subtotal', $i )] = 'on';
			if( isset( $fields['order_items_refund_quantity'] ) )
				$fields[sprintf( 'order_item_%d_refund_quantity', $i )] = 'on';
			if( isset( $fields['order_items_type'] ) )
				$fields[sprintf( 'order_item_%d_type', $i )] = 'on';
			if( isset( $fields['order_items_type_id'] ) )
				$fields[sprintf( 'order_item_%d_type_id', $i )] = 'on';
			if( isset( $fields['order_items_category'] ) )
				$fields[sprintf( 'order_item_%d_category', $i )] = 'on';
			if( isset( $fields['order_items_tag'] ) )
				$fields[sprintf( 'order_item_%d_tag', $i )] = 'on';
			if( isset( $fields['order_items_total_sales'] ) )
				$fields[sprintf( 'order_item_%d_total_sales', $i )] = 'on';
			if( isset( $fields['order_items_weight'] ) )
				$fields[sprintf( 'order_item_%d_weight', $i )] = 'on';
			if( isset( $fields['order_items_height'] ) )
				$fields[sprintf( 'order_item_%d_height', $i )] = 'on';
			if( isset( $fields['order_items_width'] ) )
				$fields[sprintf( 'order_item_%d_width', $i )] = 'on';
			if( isset( $fields['order_items_length'] ) )
				$fields[sprintf( 'order_item_%d_length', $i )] = 'on';
			if( isset( $fields['order_items_total_weight'] ) )
				$fields[sprintf( 'order_item_%d_total_weight', $i )] = 'on';
			$fields = apply_filters( 'woo_ce_add_unique_order_item_fields_on', $fields, $i );
		}
		foreach( $fields as $key => $field ) {
			$excluded_fields = apply_filters( 'woo_ce_add_unique_order_item_fields_exclusion', array(
				'order_items_id',
				'order_items_product_id',
				'order_items_variation_id',
				'order_items_sku',
				'order_items_name',
				'order_items_variation',
				'order_items_description',
				'order_items_excerpt',
				'order_items_tax_class',
				'order_items_quantity',
				'order_items_total',
				'order_items_subtotal',
				'order_items_rrp',
				'order_items_stock',
				'order_items_tax',
				'order_items_tax_subtotal',
				'order_items_refund_subtotal',
				'order_items_refund_quantity',
				'order_items_type',
				'order_items_type_id',
				'order_items_category',
				'order_items_tag',
				'order_items_total_sales',
				'order_items_weight',
				'order_items_height',
				'order_items_width',
				'order_items_length',
				'order_items_total_weight'
			), $fields );
			if( in_array( $key, $excluded_fields ) || strpos( $field, 'order_items_' ) === true )
				unset( $fields[$key] );
		}
	}
	return $fields;

}

// This prepares the Order columns for the 'unique' Order Item formatting selection
function woo_ce_unique_order_item_fields_on( $fields = array(), $i = 0 ) {

	// Product Add-ons - http://www.woothemes.com/
	if( $product_addons = woo_ce_get_product_addons() ) {
		foreach( $product_addons as $product_addon ) {
			if( isset( $fields[sprintf( 'order_items_product_addon_%s', $product_addon->post_name )] ) )
				$fields[sprintf( 'order_item_%d_product_addon_%s', $i, $product_addon->post_name )] = 'on';
		}
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( class_exists( 'RGForms' ) && class_exists( 'woocommerce_gravityforms' ) ) {
		// Check if there are any Products linked to Gravity Forms
		if( isset( $fields['order_items_gf_form_id'] ) )
			$fields[sprintf( 'order_item_%d_gf_form_id', $i )] = 'on';
		if( isset( $fields['order_items_gf_form_label'] ) )
			$fields[sprintf( 'order_item_%d_gf_form_label', $i )] = 'on';
		if( $gf_fields = woo_ce_get_gravity_form_fields() ) {
			foreach( $gf_fields as $key => $gf_field ) {
				if( isset( $fields[sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] )] ) )
					$fields[sprintf( 'order_item_%d_gf_%d_%s', $i, $gf_field['formId'], $gf_field['id'] )] = 'on';
			}
			unset( $gf_fields, $gf_field );
		}
	}

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		if( isset( $fields['order_items_checkout_addon_id'] ) )
			$fields[sprintf( 'order_item_%d_checkout_addon_id', $i )] = 'on';
		if( isset( $fields['order_items_checkout_addon_label'] ) )
			$fields[sprintf( 'order_item_%d_checkout_addon_label', $i )] = 'on';
		if( isset( $fields['order_items_checkout_addon_value'] ) )
			$fields[sprintf( 'order_item_%d_checkout_addon_value', $i )] = 'on';
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() ) {
		if( isset( $fields['order_items_brand'] ) )
			$fields[sprintf( 'order_item_%d_brand', $i )] = 'on';
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		if( isset( $fields['order_items_vendor'] ) )
			$fields[sprintf( 'order_item_%d_vendor', $i )] = 'on';
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		if( isset( $fields['order_items_cost_of_goods'] ) )
			$fields[sprintf( 'order_item_%d_cost_of_goods', $i )] = 'on';
		if( isset( $fields['order_items_total_cost_of_goods'] ) )
			$fields[sprintf( 'order_item_%d_total_cost_of_goods', $i )] = 'on';
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		if( isset( $fields['order_items_msrp'] ) )
			$fields[sprintf( 'order_item_%d_msrp', $i )] = 'on';
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) ) {
		if( isset( $fields['order_items_pickup_location'] ) )
			$fields[sprintf( 'order_item_%d_pickup_location', $i )] = 'on';
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		if( isset( $fields['order_items_booking_id'] ) )
			$fields[sprintf( 'order_item_%d_booking_id', $i )] = 'on';
		if( isset( $fields['order_items_booking_date'] ) )
			$fields[sprintf( 'order_item_%d_booking_date', $i )] = 'on';
		if( isset( $fields['order_items_booking_type'] ) )
			$fields[sprintf( 'order_item_%d_booking_type', $i )] = 'on';
		if( isset( $fields['order_items_booking_start_date'] ) )
			$fields[sprintf( 'order_item_%d_booking_start_date', $i )] = 'on';
		if( isset( $fields['order_items_booking_start_date'] ) )
			$fields[sprintf( 'order_item_%d_booking_start_date', $i )] = 'on';
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields() ) {
			foreach( $tm_fields as $tm_field ) {
				if( isset( $fields[sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) )] ) )
					$fields[sprintf( 'order_item_%d_tm_%s', $i, sanitize_key( $tm_field['name'] ) )] = 'on';
			}
		}
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$meta_type = 'order_item';
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						if( isset( $fields[sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) )] ) )
							$fields[sprintf( 'order_item_%d_wccf_%s', $i, sanitize_key( $custom_field['key'] ) )] = 'on';
					}
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			if( isset( $fields[sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] )] ) )
				$fields[sprintf( 'order_item_%d_tax_rate_%d', $i, $tax_rate['rate_id'] )] = 'on';
		}
	}
	unset( $tax_rates, $tax_rate );

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( isset( $fields[sprintf( 'order_items_attribute_%s', sanitize_key( $attribute->attribute_name ) )] ) )
				$fields[sprintf( 'order_item_%d_attribute_%s', $i, sanitize_key( $attribute->attribute_name ) )] = 'on';
		}
	}

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				if( isset( $fields['order_items_' . $custom_order_item] ) )
					$fields[sprintf( 'order_item_%d_%s', $i, $custom_order_item )] = 'on';
			}
		}
	}

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				if( isset( $fields['order_items_' . $custom_product] ) )
					$fields[sprintf( 'order_item_%d_%s', $i, $custom_product )] = 'on';
			}
		}
	}

	return $fields;

}
add_filter( 'woo_ce_add_unique_order_item_fields_on', 'woo_ce_unique_order_item_fields_on', 10, 2 );

function woo_ce_unique_order_item_columns( $columns = array(), $fields = array() ) {

	$max_size = woo_ce_get_option( 'max_order_items', 10 );
	if( !empty( $columns ) ) {
		// Strip out any remaining Order Items columns
		foreach( $columns as $key => $column ) {
			if( strpos( $column, 'Order Items: ' ) !== false )
				unset( $columns[$key] );
		}
		// Tack on a extra digit to max_size so we get the correct number of columns
		$max_size++;
		// Replace the removed columns with new ones
		for( $i = 1; $i < $max_size; $i++ ) {
			if( isset( $fields[sprintf( 'order_item_%d_id', $i )] ) )
				$columns[] = sprintf( apply_filters( 'woo_ce_unique_order_item_column_id', __( 'Order Item #%d: %s', 'woocommerce-exporter' ) ), $i, woo_ce_get_order_field( 'order_items_id', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_product_id', $i )] ) )
				$columns[] = sprintf( apply_filters( 'woo_ce_unique_order_item_column_product_id', __( 'Order Item #%d: %s', 'woocommerce-exporter' ) ), $i, woo_ce_get_order_field( 'order_items_product_id', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_variation_id', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_variation_id', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_sku', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_sku', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_name', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_name', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_variation', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_variation', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_description', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_description', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_excerpt', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_excerpt', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_tax_class', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_tax_class', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_quantity', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_quantity', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_total', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_total', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_subtotal', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_subtotal', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_rrp', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_rrp', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_stock', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_stock', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_tax', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_tax', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_tax_subtotal', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_tax_subtotal', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_refund_subtotal', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_refund_subtotal', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_refund_quantity', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_refund_quantity', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_type', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_type', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_type_id', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_type_id', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_category', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_category', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_tag', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_tag', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_total_sales', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_total_sales', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_weight', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_weight', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_height', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_height', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_width', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_width', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_length', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_length', 'name', 'unique' ) );
			if( isset( $fields[sprintf( 'order_item_%d_total_weight', $i )] ) )
				$columns[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_total_weight', 'name', 'unique' ) );
			$columns = apply_filters( 'woo_ce_unique_order_item_columns', $columns, $i, $fields );
		}
	}
	return $columns;

}

function woo_ce_extend_order_items_unique_columns( $fields = array(), $i = 0, $original_columns = array() ) {

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Product Add-ons - http://www.woothemes.com/
	if( $product_addons = woo_ce_get_product_addons() ) {
		foreach( $product_addons as $product_addon ) {
			if( isset( $original_columns[sprintf( 'order_item_%d_product_addon_%s', $i, $product_addon->post_name )] ) )
				$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, $product_addon->post_title );
		}
	}

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_checkout_addon_id', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_checkout_addon_id', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_checkout_addon_label', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_checkout_addon_label', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_checkout_addon_value', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_checkout_addon_value', 'name', 'unique' ) );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_brand', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_brand', 'name', 'unique' ) );
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_vendor', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_vendor', 'name', 'unique' ) );
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_cost_of_goods', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_cost_of_goods', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_total_cost_of_goods', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_total_cost_of_goods', 'name', 'unique' ) );
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_msrp', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_msrp', 'name', 'unique' ) );
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( class_exists( 'RGForms' ) && class_exists( 'woocommerce_gravityforms' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_gf_form_id', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_gf_form_id', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_gf_form_label', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_gf_form_label', 'name', 'unique' ) );
		// Check if there are any Products linked to Gravity Forms
		if( $gf_fields = woo_ce_get_gravity_form_fields() ) {
			foreach( $gf_fields as $key => $gf_field ) {
				if( isset( $original_columns[sprintf( 'order_item_%d_gf_%d_%s', $i, $gf_field['formId'], $gf_field['id'] )] ) )
					$fields[] = sprintf( apply_filters( 'woo_ce_extend_order_items_unique_columns_gf_fields', __( 'Order Item #%d: %s - %s', 'woocommerce-exporter' ) ), $i, $gf_field['formTitle'], $gf_field['label'] );
			}
			unset( $gf_fields, $gf_field );
		}
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( class_exists( 'WC_Local_Pickup_Plus' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_pickup_location', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_pickup_location', 'name', 'unique' ) );
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		if( isset( $original_columns[sprintf( 'order_item_%d_booking_id', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_booking_id', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_booking_date', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_booking_date', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_booking_type', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_booking_type', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_booking_start_date', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_booking_start_date', 'name', 'unique' ) );
		if( isset( $original_columns[sprintf( 'order_item_%d_booking_end_date', $i )] ) )
			$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, woo_ce_get_order_field( 'order_items_booking_end_date', 'name', 'unique' ) );
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( class_exists( 'TM_Extra_Product_Options' ) ) {
		if( $tm_fields = woo_ce_get_extra_product_option_fields() ) {
			foreach( $tm_fields as $tm_field ) {
				if( isset( $original_columns[sprintf( 'order_item_%d_tm_%s', $i, sanitize_key( $tm_field['name'] ) )] ) )
					$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, $tm_field['name'] );
			}
		}
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$meta_type = 'order_item';
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$options = ( isset( $options[1] ) ? $options[1] : false );
			if( !empty( $options ) ) {
				// Product Fields
				$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						if( isset( $original_columns[sprintf( 'order_item_%d_wccf_%s', $i, sanitize_key( $custom_field['key'] ) )] ) )
							$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, ucfirst( $custom_field['label'] ) );
					}
					unset( $custom_fields, $custom_field );
				}
			}
			unset( $options );
		}
	}

	// Tax Rates
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			if( isset( $original_columns[sprintf( 'order_item_%d_tax_rate_%d', $i, $tax_rate['rate_id'] )] ) )
				$fields[] = sprintf( __( 'Order Item #%d: Tax Rate - %s', 'woocommerce-exporter' ), $i, $tax_rate['label'] );
		}
	}
	unset( $tax_rates, $tax_rate );

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( isset( $original_columns[sprintf( 'order_item_%d_attribute_%s', $i, sanitize_key( $attribute->attribute_name ) )] ) ) {
				if( empty( $attribute->attribute_label ) )
					$attribute->attribute_label = $attribute->attribute_name;
				$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, $attribute->attribute_label );
			}
		}
	}

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				if( isset( $original_columns[sprintf( 'order_item_%d_%s', $i, $custom_order_item )] ) )
					$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, $custom_order_item );
			}
		}
	}

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				if( isset( $original_columns[sprintf( 'order_item_%d_%s', $i, $custom_product )] ) )
					$fields[] = sprintf( __( 'Order Item #%d: %s', 'woocommerce-exporter' ), $i, $custom_product );
			}
		}
	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	return $fields;

}
add_filter( 'woo_ce_unique_order_item_columns', 'woo_ce_extend_order_items_unique_columns', 10, 3 );

// Return the Order Status for a specified Order
function woo_ce_get_order_status( $order_id = 0 ) {

	global $export;

	$output = '';
	// Check if this is a WooCommerce 2.2+ instance (new Post Status)
	$woocommerce_version = woo_get_woo_version();
	if( version_compare( $woocommerce_version, '2.2' ) >= 0 ) {
		$output = get_post_status( $order_id );
		$terms = ( function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array() );
		if( isset( $terms[$output] ) )
			$output = $terms[$output];
	} else {
		$term_taxonomy = 'shop_order_status';
		$status = wp_get_object_terms( $order_id, $term_taxonomy );
		if( !empty( $status ) && is_wp_error( $status ) == false ) {
			$size = count( $status );
			for( $i = 0; $i < $size; $i++ ) {
				if( $term = get_term( $status[$i]->term_id, $term_taxonomy ) ) {
					$output .= $term->name . $export->category_separator;
					unset( $term );
				}
			}
			$output = substr( $output, 0, -1 );
		}
	}
	return $output;

}

function woo_ce_get_order_payment_gateways() {

	global $woocommerce;

	$output = false;

	// Test that payment gateways exist with WooCommerce 1.6 compatibility
	if( version_compare( $woocommerce->version, '2.0.0', '<' ) ) {
		if( $woocommerce->payment_gateways )
			$output = $woocommerce->payment_gateways->payment_gateways;
	} else {
		if( $woocommerce->payment_gateways() )
			$output = $woocommerce->payment_gateways()->payment_gateways();
	}
	return $output;

}

function woo_ce_format_order_payment_gateway( $payment_id = '' ) {

	$output = $payment_id;
	$payment_gateways = woo_ce_get_order_payment_gateways();
	if( !empty( $payment_gateways ) ) {
		foreach( $payment_gateways as $payment_gateway ) {
			if( $payment_gateway->id == $payment_id ) {
				if( method_exists( $payment_gateway, 'get_title' ) )
					$output = $payment_gateway->get_title();
				else
					$output = $payment_id;
				break;
			}
		}
		unset( $payment_gateways, $payment_gateway );
	}
	if( empty( $payment_id ) )
		$output = __( 'N/A', 'woocommerce-exporter' );
	return $output;

}

function woo_ce_get_order_payment_gateway_usage( $payment_id = '' ) {

	$output = 0;
	if( !empty( $payment_id ) ) {
		$post_type = 'shop_order';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => 1,
			'post_status' => 'any',
			'meta_query' => array(
				array(
					'key' => '_payment_method',
					'value' => $payment_id
				)
			),
			'fields' => 'ids'
		);
		$order_ids = new WP_Query( $args );
		$output = absint( $order_ids->found_posts );
		unset( $order_ids );
	}
	return $output;

}

function woo_ce_get_order_shipping_methods() {

	global $woocommerce;

	$output = false;

	// Test that payment gateways exist with WooCommerce 1.6 compatibility
	if( version_compare( $woocommerce->version, '2.0.0', '<' ) ) {
		if( $woocommerce->shipping )
			$output = $woocommerce->shipping->shipping_methods;
	} else {
		if( $woocommerce->shipping() )
			$output = $woocommerce->shipping->load_shipping_methods();
	}
	$output = apply_filters( 'woo_ce_get_order_shipping_methods', $output );
	return $output;

}

function woo_ce_extend_get_order_shipping_methods( $output ) {

	// WooCommerce Table Rate Shipping Plus - http://mangohour.com/plugins/woocommerce-table-rate-shipping
	if( function_exists( 'mh_wc_table_rate_plus_init' ) ) {
		$shipping_methods = get_option( 'mh_wc_table_rate_plus_services' );
		if( !empty( $shipping_methods ) ) {
			foreach( $shipping_methods as $shipping_method ) {
				$output[sprintf( 'mh_wc_table_rate_plus_%d', $shipping_method['id'] )] = (object)array(
					'id' => sprintf( 'mh_wc_table_rate_plus_%d', $shipping_method['id'] ),
					'title' => $shipping_method['name'],
					'method_title' => $shipping_method['name']
				);
			}
		}
	}
	// WooCommerce Table Rate Shipping Plus - http://mangohour.com/plugins/woocommerce-table-rate-shipping
	if( isset( $output['mh_wc_table_rate_plus'] ) ) {
		unset( $output['mh_wc_table_rate_plus'] );
	}
	return $output;

}
add_filter( 'woo_ce_get_order_shipping_methods', 'woo_ce_extend_get_order_shipping_methods' );

function woo_ce_format_order_shipping_method( $shipping_id = '' ) {

	global $woocommerce;

	$output = $shipping_id;
	$shipping_methods = woo_ce_get_order_shipping_methods();
	if( !empty( $shipping_methods ) ) {
		foreach( $shipping_methods as $shipping_method ) {
			if( $shipping_method->id == $shipping_id ) {
				if( method_exists( $shipping_method, 'get_title' ) )
					$output = $shipping_method->get_title();
				else if( isset( $shipping_method->title ) )
					$output = $shipping_method->title;
				else
					$output = $shipping_id;
				break;
			}
		}
		unset( $shipping_methods );
	}
	if( empty( $shipping_id ) )
		$output = __( 'N/A', 'woocommerce-exporter' );
	return $output;

}

function woo_ce_format_order_item_type( $line_type = '' ) {

	$output = $line_type;
	switch( $line_type ) {

		case 'line_item':
			$output = __( 'Product', 'woocommerce-exporter' );
			break;

		case 'fee':
			$output = __( 'Fee', 'woocommerce-exporter' );
			break;

		case 'shipping':
			$output = __( 'Shipping', 'woocommerce-exporter' );
			break;

		case 'tax':
			$output = __( 'Tax', 'woocommerce-exporter' );
			break;

		case 'coupon':
			$output = __( 'Coupon', 'woocommerce-exporter' );
			break;

	}
	return $output;

}

function woo_ce_format_order_item_tax_class( $tax_class = '' ) {

	$output = $tax_class;
	switch( $tax_class ) {

		case 'zero-rate':
			$output = __( 'Zero Rate', 'woocommerce-exporter' );
			break;

		case 'reduced-rate':
			$output = __( 'Reduced Rate', 'woocommerce-exporter' );
			break;

		case '':
			$output = __( 'Standard', 'woocommerce-exporter' );
			break;

		case '0':
			$output = __( 'N/A', 'woocommerce-exporter' );
			break;

	}
	return $output;

}

function woo_ce_format_order_status( $status_id = '' ) {

	$output = $status_id;
	// Check if an empty Order Status has been provided
	if( empty( $status_id ) )
		return $output;

	$order_statuses = woo_ce_get_order_statuses();
	if( !empty( $order_statuses ) ) {
		foreach( $order_statuses as $order_status ) {
			if( $order_status->slug == $status_id || strtolower( $order_status->name ) == $status_id || strpos( $order_status->slug, $status_id ) !== false ) {
				$output = ucfirst( $order_status->name );
				break;
			}
		}
	}
	return $output;

}
?>