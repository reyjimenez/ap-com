<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_review_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_review_count' );
		if( $cached == false ) {
			$post_type = apply_filters( 'woo_ce_get_export_type_review_count_post_types', array( 'product', 'product_variation' ) );
			$args = array(
				'count' => true,
				'status' => 'all',
				'post_status' => 'publish',
				'post_type' => $post_type
			);
			$comments = get_comments( $args );
			$count = absint( $comments );
			set_transient( WOO_CD_PREFIX . '_product_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Review Sorting widget on Store Exporter screen
	function woo_ce_review_sorting() {

		$orderby = woo_ce_get_option( 'review_orderby', 'ID' );
		$order = woo_ce_get_option( 'review_order', 'ASC' );

		ob_start(); ?>
<p><label><?php _e( 'Review Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="review_orderby">
		<option value="ID"<?php selected( 'ID', $orderby ); ?>><?php _e( 'Review ID', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="review_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Reviews within the exported file. By default this is set to export Review by Review ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

}

// Returns a list of Review export columns
function woo_ce_get_review_fields( $format = 'full' ) {

	$export_type = 'review';

	$fields = array();
	$fields[] = array(
		'name' => 'comment_ID',
		'label' => __( 'Review ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_post_ID',
		'label' => __( 'Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_name',
		'label' => __( 'Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_author',
		'label' => __( 'Reviewer', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_author_email',
		'label' => __( 'E-mail', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_content',
		'label' => __( 'Content', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_date',
		'label' => __( 'Review Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'rating',
		'label' => __( 'Rating', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'verified',
		'label' => __( 'Verified', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_author_IP',
		'label' => __( 'IP Address', 'woocommerce-exporter' )
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

function woo_ce_override_review_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'review_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_review_fields', 'woo_ce_override_review_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_review_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_review_fields();
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

// Returns a list of WooCommerce Review IDs to export process
function woo_ce_get_reviews( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$orderby = 'ID';
	$order = 'ASC';
	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : false );
		if( isset( $args['review_orderby'] ) )
			$orderby = $args['review_orderby'];
		if( isset( $args['review_order'] ) )
			$order = $args['review_order'];
	}
	$post_type = apply_filters( 'woo_ce_get_reviews_post_type', array( 'product' ) );

	$args = array(
		'status' => 'all',
		'post_status' => 'publish',
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'fields' => 'ids'
	);

	$reviews = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_reviews_args', $args );
	$review_ids = new WP_Comment_Query( $args );
	if( $review_ids->comments ) {
		foreach( $review_ids->comments as $review_id ) {
			if( isset( $review_id ) )
				$reviews[] = $review_id;
		}
	}
	return $reviews;

}

function woo_ce_get_review_data( $review_id = 0, $args = array(), $fields = array() ) {

	$review = get_comment( $review_id );

	add_filter( 'the_title', 'woo_ce_get_product_title', 10, 2 );
	$review->product_name = woo_ce_format_post_title( get_the_title( $review->comment_post_ID ) );
	remove_filter( 'the_title', 'woo_ce_get_product_title' );
	$review->comment_content = woo_ce_format_description_excerpt( $review->comment_content );
	$review->comment_date = woo_ce_format_date( $review->comment_date );
	$review->rating = get_comment_meta( $review_id, 'rating', true );
	$review->verified = get_comment_meta( $review_id, 'verified', true );

	// Allow Plugin/Theme authors to add support for additional Review columns
	$review = apply_filters( 'woo_ce_review_item', $review, $review_id );

	// Trim back the Review just to requested export fields
	if( !empty( $fields ) ) {
		$fields = array_merge( $fields, array( 'id', 'ID', 'post_parent', 'filter' ) );
		if( !empty( $review ) ) {
			foreach( $review as $key => $data ) {
				if( !in_array( $key, $fields ) )
					unset( $review->$key );
			}
		}
	}

	return $review;

}
?>