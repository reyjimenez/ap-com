<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_shipping_class_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_shipping_class_count' );
		if( $cached == false ) {
			$term_taxonomy = 'product_shipping_class';
			if( taxonomy_exists( $term_taxonomy ) )
				$count = wp_count_terms( $term_taxonomy );
			set_transient( WOO_CD_PREFIX . '_shipping_class_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Shipping Class Sorting widget on Store Exporter screen
	function woo_ce_shipping_class_sorting() {

		$shipping_class_orderby = woo_ce_get_option( 'shipping_class_orderby', 'ID' );
		$shipping_class_order = woo_ce_get_option( 'shipping_class_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Shipping Class Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="shipping_class_orderby">
		<option value="id"<?php selected( 'id', $shipping_class_orderby ); ?>><?php _e( 'Term ID', 'woocommerce-exporter' ); ?></option>
		<option value="name"<?php selected( 'name', $shipping_class_orderby ); ?>><?php _e( 'Shipping Class Name', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="shipping_class_order">
		<option value="ASC"<?php selected( 'ASC', $shipping_class_order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $shipping_class_order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Shipping Classes within the exported file. By default this is set to export Shipping Classes by Term ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of Shipping Classes export columns
function woo_ce_get_shipping_class_fields( $format = 'full' ) {

	$export_type = 'shipping_class';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woocommerce-exporter' )
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

function woo_ce_override_shipping_class_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'shipping_class_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_shipping_class_fields', 'woo_ce_override_shipping_class_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_shipping_class_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_shipping_class_fields();
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

// Returns a list of WooCommerce Shipping Classes to export process
function woo_ce_get_shipping_classes( $args = array() ) {

	$term_taxonomy = 'product_shipping_class';
	$defaults = array(
		'orderby' => 'ID',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_shipping_clases_args', $args );

	$shipping_classes = get_terms( $term_taxonomy, $args );
	if( !empty( $shipping_classes ) && is_wp_error( $shipping_classes ) == false ) {
		$size = count( $shipping_classes );
		for( $i = 0; $i < $size; $i++ ) {
			$shipping_classes[$i]->disabled = 0;
			if( $shipping_classes[$i]->count == 0 )
				$shipping_classes[$i]->disabled = 1;
		}
		return $shipping_classes;
	}

}
?>