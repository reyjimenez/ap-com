<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_user_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_user_count' );
		if( $cached == false ) {
			if( $users = count_users() )
				$count = ( isset( $users['total_users'] ) ? $users['total_users'] : 0 );
			set_transient( WOO_CD_PREFIX . '_user_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Filter Users by User Role widget on Store Exporter screen
	function woo_ce_users_filter_by_user_role() {

		$user_roles = woo_ce_get_user_roles();

		ob_start(); ?>
<p><label><input type="checkbox" id="users-filters-user_role" /> <?php _e( 'Filter Users by User Role', 'woocommerce-exporter' ); ?></label></p>
<div id="export-users-filters-user_role" class="separator">
	<ul>
		<li>
<?php if( !empty( $user_roles ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a User Role...', 'woocommerce-exporter' ); ?>" name="user_filter_user_role[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $user_roles as $key => $user_role ) { ?>
				<option value="<?php echo $key; ?>"><?php echo ucfirst( $user_role['name'] ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No User Roles were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the User Roles you want to filter exported Users by. Default is to include all User Role options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-users-filters-user_role -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Users by Date Registered widget on Store Exporter screen
	function woo_ce_users_filter_by_date_registered() {

		$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
		$user_dates_from = woo_ce_get_user_first_date( $date_format );
		$user_dates_to = date( $date_format );

		ob_start(); ?>
<p><label><input type="checkbox" id="users-filters-date_registered" /> <?php _e( 'Filter Users by Date Registered', 'woocommerce-exporter' ); ?></label></p>
<div id="export-users-filters-date_registered" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="user_dates_filter" value="manual" /> <?php _e( 'Fixed date', 'woocommerce-exporter' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="user_dates_from" name="user_dates_from" value="<?php echo esc_attr( $user_dates_from ); ?>" class="text code datepicker user_export" /> to <input type="text" size="10" maxlength="10" id="user_dates_to" name="user_dates_to" value="<?php echo esc_attr( $user_dates_to ); ?>" class="text code datepicker user_export" />
				<p class="description"><?php _e( 'Filter the dates of Users to be included in the export. Default is the date of the first User registered to today in the date format <code>DD/MM/YYYY</code>.', 'woocommerce-exporter' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-users-filters-date_registered -->
<?php
		ob_end_flush();

	}

	// HTML template for jump lin[...]Store Exporter screen
	function woo_ce_users_custom_fidate_registered() {

		ob_start(); ?>
<d
iv id="export-users-custom-fields-link">
	<p><a href="#export-users-custom-fields"><?php _e( 'Manage Custom User Fields', 'woocommerce-exporter' ); ?></a></p>
</div>
<!-- #export-users-custom-fields-link -->
<?php
		ob_end_flush();

	}

	// HTML template for User Sorting widget on Store Exporter screen
	function woo_ce_user_sorting() {

		$orderby = woo_ce_get_option( 'user_orderby', 'ID' );
		$order = woo_ce_get_option( 'user_order', 'ASC' );

		ob_start(); ?>
<p><label><?php _e( 'User Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="user_orderby">
		<option value="ID"<?php selected( 'ID', $orderby ); ?>><?php _e( 'User ID', 'woocommerce-exporter' ); ?></option>
		<option value="display_name"<?php selected( 'display_name', $orderby ); ?>><?php _e( 'Display Name', 'woocommerce-exporter' ); ?></option>
		<option value="user_name"<?php selected( 'user_name', $orderby ); ?>><?php _e( 'Name', 'woocommerce-exporter' ); ?></option>
		<option value="user_login"<?php selected( 'user_login', $orderby ); ?>><?php _e( 'Username', 'woocommerce-exporter' ); ?></option>
		<option value="nicename"<?php selected( 'nicename', $orderby ); ?>><?php _e( 'Nickname', 'woocommerce-exporter' ); ?></option>
		<option value="email"<?php selected( 'email', $orderby ); ?>><?php _e( 'E-mail', 'woocommerce-exporter' ); ?></option>
		<option value="url"<?php selected( 'url', $orderby ); ?>><?php _e( 'Website', 'woocommerce-exporter' ); ?></option>
		<option value="registered"<?php selected( 'registered', $orderby ); ?>><?php _e( 'Date Registered', 'woocommerce-exporter' ); ?></option>
		<option value="rand"<?php selected( 'rand', $orderby ); ?>><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="user_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Users within the exported file. By default this is set to export User by User ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for Custom Users widget on Store Exporter screen
	function woo_ce_users_custom_fields() {

		if( $custom_users = woo_ce_get_option( 'custom_users', '' ) )
			$custom_users = implode( "\n", $custom_users );

		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

		ob_start(); ?>
<form method="post" id="export-users-custom-fields" class="export-options user-options">
	<div id="poststuff">

		<div class="postbox" id="export-options user-options">
			<h3 class="hndle"><?php _e( 'Custom User Fields', 'woocommerce-exporter' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom User meta in the Export Users table above fill the Users text box then click Save Custom Fields.', 'woocommerce-exporter' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'User meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_users" rows="5" cols="70"><?php echo esc_textarea( $custom_users ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom User meta in your export file by adding each custom User meta name to a new line above.<br />For example: <code>Customer UA (new line) Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Custom Fields', 'woocommerce-exporter' ); ?>" class="button" />
				</p>
				<p class="description"><?php printf( __( 'For more information on custom User meta consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-users-custom-fields -->
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of User export columns
function woo_ce_get_user_fields( $format = 'full' ) {

	$export_type = 'user';

	$fields = array();
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
		'name' => 'first_name',
		'label' => __( 'First Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'last_name',
		'label' => __( 'Last Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'full_name',
		'label' => __( 'Full Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'nick_name',
		'label' => __( 'Nickname', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'email',
		'label' => __( 'E-mail', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'orders',
		'label' => __( 'Orders', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'money_spent',
		'label' => __( 'Money Spent', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'url',
		'label' => __( 'Website', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'date_registered',
		'label' => __( 'Date Registered', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Biographical Info', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'aim',
		'label' => __( 'AIM', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'yim',
		'label' => __( 'Yahoo IM', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'jabber',
		'label' => __( 'Jabber / Google Talk', 'woocommerce-exporter' )
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

function woo_ce_override_user_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'user_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_user_fields', 'woo_ce_override_user_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_user_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_user_fields();
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

// Adds custom User columns to the User fields list
function woo_ce_extend_user_fields( $fields = array() ) {

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( class_exists( 'WooCommerce_HearAboutUs' ) ) {
		$fields[] = array(
			'name' => 'hear_about_us',
			'label' => __( 'Source', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Hear About Us', 'woocommerce-exporter' )
		);
	}

	// WooCommerce User fields
	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( method_exists( 'WC_Admin_Profile', 'get_customer_meta_fields' ) ) {
			$show_fields = $admin_profile->get_customer_meta_fields();
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field ) {
					$fields[] = array(
						'name' => $key,
						'label' => sprintf( apply_filters( 'woo_ce_extend_user_fields_wc', '%s: %s' ), $fieldset['title'], esc_html( $field['label'] ) )
					);
				}
			}
			unset( $show_fields, $fieldset, $field );
		}
	}

	// WC Vendors - http://wcvendors.com
	if( class_exists( 'WC_Vendors' ) ) {
		$fields[] = array(
			'name' => 'shop_name',
			'label' => __( 'Shop Name' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'shop_slug',
			'label' => __( 'Shop Slug' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'paypal_email',
			'label' => __( 'PayPal E-mail' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'commission_rate',
			'label' => __( 'Commission Rate (%)' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'seller_info',
			'label' => __( 'Seller Info' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'shop_description',
			'label' => __( 'Shop Description' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		$fields[] = array(
			'name' => 'active_subscriber',
			'label' => __( 'Active Subscriber' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
	}

	// Custom User meta
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => woo_ce_clean_export_label( $custom_user ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_user_fields_custom_user_hover', '%s: %s' ), __( 'Custom User', 'woocommerce-exporter' ), $custom_user )
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $fields;

}
add_filter( 'woo_ce_user_fields', 'woo_ce_extend_user_fields' );

// Returns a list of User IDs
function woo_ce_get_users( $args = array() ) {

	global $wpdb, $export;

	$limit_volume = 0;
	$offset = 0;
	$orderby = 'login';
	$order = 'ASC';
	$user_roles = false;

	if( $args ) {
		$user_roles = ( isset( $args['user_roles'] ) ? $args['user_roles'] : 0 );
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : 0 );
		if( $limit_volume == -1 )
			$limit_volume = 0;
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : 0 );
		$orderby = ( isset( $args['user_orderby'] ) ? $args['user_orderby'] : 'login' );
		$order = ( isset( $args['user_order'] ) ? $args['user_order'] : 'ASC' );
		$user_dates_filter = ( isset( $args['user_dates_filter'] ) ? $args['user_dates_filter'] : false );
		switch( $user_dates_filter ) {

			case 'manual':
				$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );

				$user_dates_from = woo_ce_format_order_date( $args['user_dates_from'] );
				$user_dates_to = woo_ce_format_order_date( $args['user_dates_to'] );

				// WP_User_Query only accepts YY-m-D so we must format dates to that
				if( $date_format <> 'Y/m/d' ) {
					$date_format = woo_ce_format_order_date( $date_format );
					if( function_exists( 'date_create_from_format' ) && function_exists( 'date_format' ) ) {
						if( $user_dates_from = date_create_from_format( $date_format, $user_dates_from ) )
							$user_dates_from = date_format( $user_dates_from, 'Y-m-d 00:00:00' );
						if( $user_dates_to = date_create_from_format( $date_format, $user_dates_to ) )
							$user_dates_to = date_format( $user_dates_to, 'Y-m-d 23:59:59' );
					}
				}
				
				break;

			default:
				$user_dates_from = false;
				$user_dates_to = false;
				break;

		}
	}
	$args = array(
		'offset' => $offset,
		'number' => $limit_volume,
		'order' => $order,
		'offset' => $offset,
		'fields' => 'ids'
	);

	// Filter Order dates
	if( !empty( $user_dates_from ) && !empty( $user_dates_to ) ) {
		$args['date_query'] = array(
			array(
				'before' => $user_dates_to,
				'after' => $user_dates_from,
				'inclusive' => true
			)
		);
	}

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_users_args', $args );

	if( $user_ids = new WP_User_Query( $args ) ) {
		$users = array();
		foreach( $user_ids->results as $user_id ) {

			$user = new WP_User( $user_id );

			if( $user_roles ) {
				if( count( array_intersect( $user_roles, $user->roles ) ) == 0 ) {
					unset( $user, $user_id );
					continue;
				}
			}

			if( isset( $user_id ) )
				$users[] = $user_id;

		}
		// Only populate the $export Global if it is an export
		if( isset( $export ) )
			$export->total_rows = count( $users );
		return $users;
	}

}

function woo_ce_get_user_data( $user_id = 0, $args = array() ) {

	$defaults = array();
	$args = wp_parse_args( $args, $defaults );

	// Get User details
	$user_data = get_userdata( $user_id );

	$user = new stdClass;
	if( $user_data !== false ) {
		$user->ID = $user_data->ID;
		$user->user_id = $user_data->ID;
		$user->user_name = $user_data->user_login;
		$user->user_role = ( isset( $user_data->roles[0] ) ? $user_data->roles[0] : false );
		$user->first_name = $user_data->first_name;
		$user->last_name = $user_data->last_name;
		$user->full_name = sprintf( apply_filters( 'woo_ce_get_user_data_full_name', '%s %s' ), $user->first_name, $user->last_name );
		$user->nick_name = $user_data->user_nicename;
		$user->email = $user_data->user_email;
		$user->orders = ( function_exists( 'wc_get_customer_order_count' ) ? wc_get_customer_order_count( $user->ID ) : 0 );
		$user->money_spent = ( function_exists( 'wc_get_customer_total_spent' ) ? woo_ce_format_price( wc_get_customer_total_spent( $user->ID ) ) : 0 );
		$user->url = $user_data->user_url;
		$user->date_registered = $user_data->user_registered;
		$user->description = $user_data->description;
		$user->aim = $user_data->aim;
		$user->yim = $user_data->yim;
		$user->jabber = $user_data->jabber;
	}

	// Allow Plugin/Theme authors to add support for additional User columns
	return apply_filters( 'woo_ce_user', $user );
	
}

// Populate User details for export of 3rd party Plugins
function woo_ce_user_extend( $user ) {

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( class_exists( 'WooCommerce_HearAboutUs' ) ) {
		$source = get_user_meta( $user->ID, '_wchau_source', true );
		if( $source == '' )
			$source = __( 'N/A', 'woocommerce-exporter' );
		$user->hear_about_us = $source;
		unset( $source );
	}

	// WooCommerce User Profile fields
	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( $show_fields = $admin_profile->get_customer_meta_fields() ) {
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field )
					$user->{$key} = esc_attr( get_user_meta( $user->ID, $key, true ) );
			}
		}
		unset( $show_fields, $fieldset, $field );
	}

	// WC Vendors - http://wcvendors.com
	if( class_exists( 'WC_Vendors' ) ) {
		$user->shop_name = get_user_meta( $user->ID, 'pv_shop_name', true );
		$user->shop_slug = get_user_meta( $user->ID, 'pv_shop_slug', true );
		$user->paypal_email = get_user_meta( $user->ID, 'pv_paypal', true );
		$user->commission_rate = get_user_meta( $user->ID, 'pv_custom_commission_rate', true );
		$user->seller_info = get_user_meta( $user->ID, 'pv_seller_info', true );
		$user->shop_description = get_user_meta( $user->ID, 'pv_shop_description', true );
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		if( function_exists( 'wcs_user_has_subscription' ) ) {
			$user->active_subscriber = woo_ce_format_switch( wcs_user_has_subscription( $user->ID, '', 'active' ) );
		}
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			// Check that the custom User name is filled and it hasn't previously been set
			if( !empty( $custom_user ) && !isset( $user->{$custom_user} ) ) {
				$user->{$custom_user} = woo_ce_format_custom_meta( get_user_meta( $user->ID, $custom_user, true ) );
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $user;

}
add_filter( 'woo_ce_user', 'woo_ce_user_extend' );

// Returns a list of WordPress User Roles
function woo_ce_get_user_roles() {

	global $wp_roles;

	$user_roles = $wp_roles->roles;
	if( $users = count_users() ) {
		foreach( $user_roles as $key => $user_role ) {
			$user_roles[$key]['count'] = ( isset( $users['avail_roles'][$key] ) ? $users['avail_roles'][$key] : 0 );
		}
		unset( $user_role, $users );
	}
	return $user_roles;

}

// Returns the Username of a User
function woo_ce_get_username( $user_id = 0 ) {

	$output = '';
	if( $user_id ) {
		if( $user = get_userdata( $user_id ) )
			$output = $user->user_login;
		unset( $user );
	}
	return $output;

}

// Returns the User Role of a User
function woo_ce_get_user_role( $user_id = 0 ) {

	$output = '';
	if( $user_id ) {
		$user = get_userdata( $user_id );
		if( $user ) {
			$user_role = $user->roles[0];
			if( !empty( $user_role ) )
				$output = $user_role;
		}
		unset( $user );
	}
	return $output;

}

function woo_ce_format_user_role_label( $user_role = '' ) {

	global $wp_roles;

	$output = $user_role;
	if( $user_role ) {
		$user_roles = woo_ce_get_user_roles();
		if( isset( $user_roles[$user_role] ) )
			$output = ucfirst( $user_roles[$user_role]['name'] );
		unset( $user_roles );
	}
	return $output;

}

// Returns date of first User registered, any status
function woo_ce_get_user_first_date( $date_format = 'd/m/Y' ) {

	$output = date( $date_format, mktime( 0, 0, 0, date( 'n' ), 1 ) );

	$args = array(
		'limit_volume' => 1,
		'orderby' => 'registered',
		'order' => 'ASC'
	);
	if( $user_ids = woo_ce_get_users( $args ) ) {
		foreach( $user_ids as $user_id ) {
			$user = new WP_User( $user_id );
			if( !empty( $user ) )
				$output = date( $date_format, strtotime( $user->data->user_registered ) );
		}
	}
	return $output;

}
?>