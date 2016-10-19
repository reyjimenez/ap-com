<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_ticket_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_ticket_count' );
		if( $cached == false ) {
			$post_type = 'event_magic_tickets';
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => 1,
				'fields' => 'ids',
				'suppress_filters' => 1
			);
			$count_query = new WP_Query( $args );
			$count = $count_query->found_posts;
			set_transient( WOO_CD_PREFIX . '_ticket_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	/* End of: WordPress Administration */

}

// Returns a list of Ticket export columns
function woo_ce_get_ticket_fields( $format = 'full' ) {

	$export_type = 'ticket';

	$fields = array();
	$fields[] = array(
		'name' => 'post_id',
		'label' => __( 'Post ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'ticket_id',
		'label' => __( 'Ticket ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'status',
		'label' => __( 'Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'barcode',
		'label' => __( 'Barcode', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_id',
		'label' => __( 'Order ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woocommerce-exporter' )
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

function woo_ce_override_ticket_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'ticket_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_ticket_fields', 'woo_ce_override_ticket_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_ticket_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_ticket_fields();
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

// Returns a list of WooCommerce Ticket IDs to export process
function woo_ce_get_tickets( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$orderby = 'ID';
	$order = 'ASC';
	if( $args ) {
		// Do something
	}
	$post_type = 'event_magic_tickets';
	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'fields' => 'ids',
		'suppress_filters' => false
	);
	$tickets = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_tickets_args', $args );

	$ticket_ids = new WP_Query( $args );
	if( $ticket_ids->posts ) {
		foreach( $ticket_ids->posts as $ticket_id ) {

			if( isset( $ticket_id ) )
				$tickets[] = $ticket_id;

		}
		// Only populate the $export Global if it is an export
		if( isset( $export ) )
			$export->total_rows = count( $tickets );
		unset( $ticket_ids, $ticket_id );
	}
	return $tickets;

}

function woo_ce_get_ticket_data( $ticket_id = 0, $args = array(), $fields = array() ) {

	$ticket = get_post( $ticket_id );

	// Allow Plugin/Theme authors to add support for additional Ticket columns
	$ticket = apply_filters( 'woo_ce_ticket_item', $ticket, $ticket_id );

	$ticket->post_id = $ticket->ID;
	$ticket->user_id = get_post_meta( $ticket->ID, 'WooCommerceEventsCustomerID', true );
	$ticket->ticket_id = get_post_meta( $ticket->ID, 'WooCommerceEventsTicketID', true );
	$ticket->status = get_post_meta( $ticket->ID, 'WooCommerceEventsStatus', true );
	$ticket->order_id = get_post_meta( $ticket->ID, 'WooCommerceEventsOrderID', true );
	$ticket->product_id = get_post_meta( $ticket->ID, 'WooCommerceEventsProductID', true );
	$barcode_path = false;
	if( class_exists( 'WooCommerce_Events_Config' ) ) {
		$ticket_config = new WooCommerce_Events_Config();
		if( !empty( $ticket_config ) ) {
			$barcode_path = ( isset( $ticket_config->barcodePath ) ? sanitize_text_field( $ticket_config->barcodePath ) : false );
		}
		unset( $ticket_config );
	}
	$ticket->barcode = ( !empty( $barcode_path ) ? $barcode_path . $ticket->ticket_id . '.png' : $ticket->ticket_id );

	// Trim back the Ticket just to requested export fields
	if( !empty( $fields ) ) {
		$fields = array_merge( $fields, array( 'id', 'ID', 'post_parent', 'filter' ) );
		if( !empty( $ticket ) ) {
			foreach( $ticket as $key => $data ) {
				if( !in_array( $key, $fields ) )
					unset( $ticket->$key );
			}
		}
	}

	return $ticket;

}
?>