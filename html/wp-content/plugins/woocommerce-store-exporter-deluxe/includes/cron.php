<?php
function woo_ce_cron_activation( $force_reload = false, $post_ID = 0 ) {

	if( $scheduled_exports = woo_ce_get_scheduled_exports() ) {

		// Check if we need to reload just a single scheduled export
		if( $force_reload ) {
			if( !empty( $post_ID ) ) {
				$args = array(
					'id' => $post_ID
				);
				wp_clear_scheduled_hook( 'woo_ce_auto_export_schedule_' . $post_ID, $args );
			} else {
				// Reset all scheduled exports
				foreach( $scheduled_exports as $scheduled_export ) {
					$args = array(
						'id' => $scheduled_export
					);
					wp_clear_scheduled_hook( 'woo_ce_auto_export_schedule_' . $scheduled_export, $args );
				}
			}
		}

		foreach( $scheduled_exports as $scheduled_export ) {
			$hook = 'woo_ce_auto_export_schedule_' . $scheduled_export;
			$args = array(
				'id' => $scheduled_export
			);
			// Check if this schedule already exists and that its post status is publish
			if( !wp_next_scheduled( $hook, $args ) && get_post_status( $scheduled_export ) == 'publish' ) {
				$auto_schedule = sanitize_text_field( get_post_meta( $scheduled_export, '_auto_schedule', true ) );
				$auto_commence = sanitize_text_field( get_post_meta( $scheduled_export, '_auto_commence', true ) );
				switch( $auto_schedule ) {

					case 'custom':
						$recurrence = sprintf( 'woo_ce_auto_interval_%d', $scheduled_export );
						break;

					default:
						$recurrence = $auto_schedule;
						break;

				}
				switch( $auto_commence ) {

					// Start initial export immediately
					case 'now':
					default:
						$time = current_time( 'timestamp', 1 );
						break;

					// Pass on a timestamp from the future
					case 'future':
						$commence_date = sanitize_text_field( get_post_meta( $scheduled_export, '_auto_commence_date', true ) );
						// Check if date is set
						if( !empty( $commence_date ) ) {
							$now = current_time( 'timestamp', 0 );
							$timezone = ( function_exists( 'wc_timezone_string' ) ? wc_timezone_string() : date_default_timezone_get() );
							$objTimeZone = new DateTimezone( $timezone );
							$objDateTo = new DateTime( woo_ce_format_order_date( $commence_date ), $objTimeZone );
							$commence_date = $objDateTo->format( 'U' );
							$time = $commence_date;
						} else {
							$time = $now;
						}
						break;

				}
				$args = array(
					'id' => $scheduled_export
				);
				if( $auto_schedule == 'one-time' ) {
					wp_schedule_single_event( $time, $hook, $args );
				} else {
					// Check if hook still exists (as WordPress tends to ignore us)
					if( !wp_next_scheduled( $hook, $args ) )
						wp_schedule_event( $time, $recurrence, $hook, $args );
					else
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Could not re-schedule scheduled export as WordPress has not cleared the existing WP-CRON task. We will try again on next screen refresh.', 'woocommerce-exporter' ) ) );
				}
			}
		}

	}

}

function woo_ce_cron_schedules() {

	$schedules = array();
	// Check if Weekly already exists
	if( !isset( $schedules['weekly'] ) ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'woocommerce-exporter' )
		);
	}
	// Check if Monthly already exists
	if( !isset( $schedules['monthly'] ) ) {
		$schedules['monthly'] = array(
			'interval' => ( date( 't' ) * 60 * 60 * 24 ),
			'display'  => __( 'Once Monthly', 'woocommerce-exporter' )
		);
	}
	$args = array(
		'post_status' => 'publish'
	);
	if( $scheduled_exports = woo_ce_get_scheduled_exports( $args ) ) {
		foreach( $scheduled_exports as $scheduled_export ) {
			$schedule = sanitize_text_field( get_post_meta( $scheduled_export, '_auto_schedule', true ) );
			switch( $schedule ) {

				case 'custom':
					$interval = absint( get_post_meta( $scheduled_export, '_auto_interval', true ) );
					if( $interval ) {
						$schedules[sprintf( 'woo_ce_auto_interval_%d', $scheduled_export )] = array(
							'interval' => $interval * 60,
							'display'  => sprintf( __( 'Every %d minutes', 'woocommerce-exporter' ), $interval )
						);
					}
					break;

			}
		}
	}
	return $schedules;

}

function woo_ce_auto_export( $args = array() ) {

	if( !empty( $args ) ) {

		$post_ID = absint( $args );

		// Check if a draft/trash scheduled export snuck through
		if( in_array( get_post_status( $post_ID ), array( 'draft', 'trash' ) ) ) {
			woo_ce_cron_activation( true, $post_ID );
			return;
		}

		// If our site hash check fails cease the export
		if( woo_ce_auto_check_site_hash() ) {
			if( woo_ce_get_option( 'dismiss_duplicate_site_prompt', 0 ) == 1 )
				$error = __( 'Site hash mis-match after notice dismissal, ceasing current scheduled export', 'woocommerce-exporter' );
			else
				$error = __( 'Site hash mis-match, disabling future scheduled exports and displaying duplicate site prompt', 'woocommerce-exporter' );
			woo_ce_add_recent_scheduled_export( $post_ID, '', 0, $error );
			return;
		}

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_scheduled_export_id', $post_ID, MINUTE_IN_SECONDS );

		$export_type = get_post_meta( $post_ID, '_export_type', true );
		// Check an export type has been set
		if( !empty( $export_type ) ) {
			$export_method = get_post_meta( $post_ID, '_export_method', true );
			if( in_array( $export_method, array( 'archive', 'save', 'email', 'post', 'ftp' ) ) )
				woo_ce_cron_export( $export_method, $export_type, true );
			else
				woo_ce_cron_export( '', $export_type, true );
		}

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_scheduled_export_id' );

	}

}

function woo_ce_auto_check_site_hash() {

	// Do we have an existing hash to check and has the hash override been enabled
	$site_hash = woo_ce_get_option( 'site_hash', false );
	if( empty( $site_hash ) ) {
		// Set a new site hash for future scheduled exports
		$site_hash = md5( get_option( 'siteurl' ) );
		woo_ce_update_option( 'site_hash', $site_hash );
		return;
	}

	// Proceed with scheduled export if override is set
	if( woo_ce_get_option( 'override_duplicate_site_prompt', 0 ) == 1 )
		return;

	// Cease scheduled export if dismiss is set
	if( woo_ce_get_option( 'dismiss_duplicate_site_prompt', 0 ) == 1 ) {
		woo_ce_error_log( sprintf( 'Error: %s', __( 'Site hash mis-match after notice dismissal, ceasing current scheduled export', 'woocommerce-exporter' ) ) );
		return true;
	}

	// Check the existing hash against the current hash
	if( $site_hash != md5( get_site_url() ) ) {
		// Show a notice and disable scheduled exports
		woo_ce_update_option( 'enable_auto', 0 );
		woo_ce_update_option( 'duplicate_site_prompt', 1 );
		woo_ce_error_log( sprintf( 'Error: %s', __( 'Site hash mis-match, disabling future scheduled exports and displaying duplicate site prompt.', 'woocommerce-exporter' ) ) );
		woo_ce_cron_activation( true );
		return true;
	}

}

function woo_ce_cron_export( $gui = '', $type = '', $is_scheduled = false ) {

	global $export;

	$export = new stdClass;
	$export->cron = ( $is_scheduled ? 0 : 1 );
	$export->scheduled_export = ( $is_scheduled ? 1 : 0 );
	$export->start_time = time();
	$export->idle_memory_start = woo_ce_current_memory_usage();
	$export->error = '';

	$bits = '';
	$type = ( isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : $type );
	if( empty( $type ) ) {
		if( $gui == 'gui' ) {
			$output = sprintf( '<p>%s</p>', __( 'No export type was provided.', 'woocommerce-exporter' ) );
		} else {
			woo_ce_error_log( sprintf( 'Error: %s', __( 'No export type was provided', 'woocommerce-exporter' ) ) );
			return;
		}
	} else {

		$export_types = apply_filters( 'woo_ce_cron_allowed_export_types', array_keys( woo_ce_get_export_types() ) );
		$export->type = $type;
		// Check that export is in the list of allowed exports
		if( !in_array( $export->type, $export_types ) ) {

			if( $gui == 'gui' ) {
				$output = '<p>' . __( 'An invalid export type was provided.', 'woocommerce-exporter' ) . '</p>';
			} else {
				woo_ce_error_log( sprintf( 'Error: %s', __( 'An invalid export type was provided', 'woocommerce-exporter' ) ) );
				return;
			}

		} else {

			$export->export_format = ( isset( $_GET['format'] ) ? sanitize_text_field( $_GET['format'] ) : 'csv' );

			// Load the Post ID for scheduled exports
			if( isset( $_GET['scheduled_export'] ) ) {
				// Override this CRON export as a scheduled export
				$export->scheduled_export = 1;
				$is_scheduled = 1;
				$scheduled_export = absint( $_GET['scheduled_export'] );
				$export_type = get_post_meta( $scheduled_export, '_export_type', true );
				$export->type = $export_type;
				$gui = $export_method = get_post_meta( $scheduled_export, '_export_method', true );
			} else {
				$scheduled_export = ( $export->scheduled_export ? absint( get_transient( WOO_CD_PREFIX . '_scheduled_export_id' ) ) : 0 );
			}

			// Override the export format if outputting to screen in friendly design
			if( $gui == 'gui' && in_array( $export->export_format, array( 'csv', 'tsv', 'xls', 'xlsx' ) ) )
				$export->export_format = 'csv';

			// Override the export format if this is a scheduled export
			if( $export->scheduled_export )
				$export->export_format = get_post_meta( $scheduled_export, '_export_format', true );

			// Override the export format if the single order Transient is set
			$single_export_format = get_transient( WOO_CD_PREFIX . '_single_export_format' );
			if( $single_export_format !== false )
				$export->export_format = $single_export_format;
			unset( $single_export_format );

			$export->order_items = ( isset( $_GET['order_items'] ) ? sanitize_text_field( $_GET['order_items'] ) : woo_ce_get_option( 'order_items_formatting', 'unique' ) );
			// Override order items formatting if the single order Transient is set
			$single_export_order_items_formatting = get_transient( WOO_CD_PREFIX . '_single_export_order_items_formatting' );
			if( $single_export_order_items_formatting !== false )
				$export->order_items = $single_export_order_items_formatting;
			unset( $single_export_order_items_formatting );

			$export->delimiter = ( isset( $_GET['delimiter'] ) ? sanitize_text_field( $_GET['delimiter'] ) : woo_ce_get_option( 'delimiter', ',' ) );
			if( $export->delimiter == '' || $export->delimiter == false ) {
				woo_ce_error_log( sprintf( 'Warning: %s', __( 'Delimiter export option was corrupted, defaulted to ,' ) ) );
				$export->delimiter = ',';
				woo_ce_update_option( 'delimiter', ',' );
			} else if( $export->delimiter == 'TAB' ) {
				$export->delimiter = "\t";
			}
			$export->category_separator = ( isset( $_GET['category_separator'] ) ? sanitize_text_field( $_GET['category_separator'] ) : woo_ce_get_option( 'category_separator', '|' ) );
			// Override for line break (LF) support in Category Separator
			if( $export->category_separator == 'LF' )
				$export->category_separator = "\n";
			$export->bom = ( isset( $_GET['bom'] ) ? absint( $_GET['bom'] ) : woo_ce_get_option( 'bom', 1 ) );
			$export->encoding = ( isset( $_GET['encoding'] ) ? sanitize_text_field( $_GET['encoding'] ) : woo_ce_get_option( 'encoding', 'UTF-8' ) );
			$export->timeout = woo_ce_get_option( 'timeout', 600 );
			$export->escape_formatting = ( isset( $_GET['escape_formatting'] ) ? sanitize_text_field( $_GET['escape_formatting'] ) : woo_ce_get_option( 'escape_formatting', 'all' ) );
			$export->header_formatting = ( isset( $_GET['header_formatting'] ) ? absint( $_GET['header_formatting'] ) : woo_ce_get_option( 'header_formatting', 1 ) );
			$export->upsell_formatting = woo_ce_get_option( 'upsell_formatting', 1 );
			$export->crosssell_formatting = woo_ce_get_option( 'crosssell_formatting', 1 );
			$export->gallery_formatting = woo_ce_get_option( 'gallery_formatting', 1 );
			$export->gallery_unique = woo_ce_get_option( 'gallery_unique', 0 );
			$export->max_product_gallery = woo_ce_get_option( 'max_product_gallery', 3 );
			$export->filename = woo_ce_generate_filename( $export->type );

			// Set the file extension and MIME type
			switch( $export->export_format ) {

				case 'csv':
					$php_excel_format = 'SED_CSV';
					$file_extension = 'csv';
					$post_mime_type = 'text/csv';
					break;

				case 'tsv':
					$php_excel_format = 'SED_CSV';
					$file_extension = 'tsv';
					$post_mime_type = 'text/tab-separated-values';
					break;

				case 'xls':
					$php_excel_format = 'Excel5';
					$file_extension = 'xls';
					$post_mime_type = 'application/vnd.ms-excel';
					break;

				case 'xlsx':
					$php_excel_format = 'Excel2007';
					$file_extension = 'xlsx';
					$post_mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					break;

				case 'xml':
					$file_extension = 'xml';
					$post_mime_type = 'application/xml';
					break;

				case 'rss':
					$file_extension = 'xml';
					$post_mime_type = 'application/rss+xml';
					break;

				default:
					if( $export->scheduled_export )
						woo_ce_error_log( sprintf( 'Error: %s', sprintf( __( 'An invalid export format - %s was provided by Scheduled Export #%d', 'woocommerce-exporter' ), $export->export_format, $scheduled_export ) ) );
					else
						woo_ce_error_log( sprintf( 'Error: %s', sprintf( __( 'An invalid export format - %s was provided', 'woocommerce-exporter' ), $export->export_format ) ) );
					return;
					break;

			}
			$export->filename = $export->filename . '.' . $file_extension;
			$export->limit_volume = ( isset( $_GET['limit'] ) ? absint( $_GET['limit'] ) : -1 );
			$export->offset = ( isset( $_GET['offset'] ) ? absint( $_GET['offset'] ) : 0 );
			// Select all export fields for CRON export
			$export->fields = woo_ce_cron_export_fields( $export->type, $export->scheduled_export, $scheduled_export );
			// Grab to value if response is e-mail or remote POST
			if( in_array( $gui, array( 'email', 'post' ) ) ) {
				if( $gui == 'email' ) {
					$export->to = ( isset( $_GET['to'] ) ? sanitize_email( $_GET['to'] ) : get_post_meta( $scheduled_export, '_method_email_to', true ) );

					// Override the e-mail recipient if the single order Transient is set
					$single_export_method_email_to = get_transient( WOO_CD_PREFIX . '_single_export_method_email_to' );
					if( $single_export_method_email_to !== false )
						$export->to = $single_export_method_email_to;
					unset( $single_export_method_email_to );

				} else if( $gui == 'post' ) {
					$export->to = ( isset( $_GET['to'] ) ? esc_url_raw( $_GET['to'] ) : get_post_meta( $scheduled_export, '_method_post_to', true ) );
				}
			}
			$export = woo_ce_check_cron_export_arguments( $export );

			$export->args = array(
				'limit_volume' => $export->limit_volume,
				'offset' => $export->offset,
				'encoding' => $export->encoding,
				'date_format' => woo_ce_get_option( 'date_format', 'd/m/Y' ),
				'order_items' => $export->order_items,
				'order_items_types' => ( isset( $_GET['order_items_types'] ) ? sanitize_text_field( $_GET['order_items_types'] ) : woo_ce_get_option( 'order_items_types', false ) )
			);

			$orderby = ( isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : null );
			$order = ( isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : null );
			switch( $export->type ) {

				case 'product':
					$export->args['product_orderby'] = $orderby;
					$export->args['product_order'] = $order;
					if( $export->scheduled_export ) {
						$product_filter_category = get_post_meta( $scheduled_export, '_filter_product_category', true );
						$product_filter_tag = get_post_meta( $scheduled_export, '_filter_product_tag', true );
						$product_filter_brand = get_post_meta( $scheduled_export, '_filter_product_brand', true );
						$product_filter_status = get_post_meta( $scheduled_export, '_filter_product_status', true );
						$product_filter_type = get_post_meta( $scheduled_export, '_filter_product_type', true );
						$product_filter_sku = get_post_meta( $scheduled_export, '_filter_product_sku', true );
						$product_filter_stock = get_post_meta( $scheduled_export, '_filter_product_stock', true );
						$product_filter_featured = get_post_meta( $scheduled_export, '_filter_product_featured', true );
						$product_filter_shipping_class = get_post_meta( $scheduled_export, '_filter_product_shipping_class', true );
						$export->args['product_categories'] = ( !empty( $product_filter_category ) ? $product_filter_category : false );
						$export->args['product_tags'] = ( !empty( $product_filter_tag ) ? $product_filter_tag : false );
						$export->args['product_brands'] = ( !empty( $product_filter_brand ) ? $product_filter_brand : false );
						$export->args['product_status'] = ( !empty( $product_filter_status ) ? $product_filter_status : false );
						$export->args['product_type'] = ( !empty( $product_filter_type ) ? $product_filter_type : false );
						$export->args['product_sku'] = ( !empty( $product_filter_sku ) ? (array)$product_filter_sku : array() );
						$export->args['product_stock'] = ( !empty( $product_filter_stock ) ? $product_filter_stock : false );
						$export->args['product_featured'] = ( !empty( $product_filter_featured ) ? $product_filter_featured : false );
						$export->args['product_shipping_class'] = ( !empty( $product_filter_shipping_class ) ? $product_filter_shipping_class : false );
					} else {
						if( isset( $_GET['product_category'] ) ) {
							$product_filter_category = sanitize_text_field( $_GET['product_category'] );
							$product_filter_category = explode( ',', $product_filter_category );
							$export->args['product_categories'] = array_map( 'absint', (array)$product_filter_category );
						}
						if( isset( $_GET['product_tag'] ) ) {
							$product_filter_tag = sanitize_text_field( $_GET['product_tag'] );
							$product_filter_tag = explode( ',', $product_filter_tag );
							$export->args['product_tags'] = array_map( 'absint', (array)$product_filter_tag );
						}
						if( isset( $_GET['product_brand'] ) ) {
							$product_filter_brand = sanitize_text_field( $_GET['product_brand'] );
							$product_filter_brand = explode( ',', $product_filter_brand );
							$export->args['product_brands'] = array_map( 'absint', (array)$product_filter_brand );
						}
						$export->args['product_status'] = ( isset( $_GET['product_status'] ) ? sanitize_text_field( $_GET['product_status'] ) : null );
						if( isset( $_GET['product_type'] ) ) {
							$product_filter_type = sanitize_text_field( $_GET['product_type'] );
							$product_filter_type = explode( ',', $product_filter_type );
							$export->args['product_type'] = $product_filter_type;
						}
						$export->args['product_stock'] = ( isset( $_GET['stock_status'] ) ? sanitize_text_field( $_GET['stock_status'] ) : null );
						$export->args['product_featured'] = ( isset( $_GET['product_featured'] ) ? sanitize_text_field( $_GET['product_featured'] ) : null );
						if( isset( $_GET['product_brand'] ) ) {
							$product_filter_brand = sanitize_text_field( $_GET['product_brand'] );
							$product_filter_brand = explode( ',', $product_filter_brand );
							$export->args['product_brands'] = array_map( 'absint', (array)$product_filter_brand );
						}
						if( isset( $_GET['product_vendor'] ) ) {
							$product_filter_vendor = sanitize_text_field( $_GET['product_vendor'] );
							$product_filter_vendor = explode( ',', $product_filter_vendor );
							$export->args['product_vendors'] = array_map( 'absint', (array)$product_filter_vendor );
						}
						if( isset( $_GET['shipping_class'] ) ) {
							$product_filter_shipping_class = sanitize_text_field( $_GET['shipping_class'] );
							$product_filter_shipping_class = explode( ',', $product_filter_shipping_class );
							$export->args['shipping_class'] = $product_filter_shipping_class;
						}
					}
					break;

				case 'category':
					$export->args['category_orderby'] = $orderby;
					$export->args['category_order'] = $order;
					break;

				case 'tag':
					$export->args['tag_orderby'] = $orderby;
					$export->args['tag_order'] = $order;
					break;

				case 'order':
					$export->args['order_orderby'] = $orderby;
					$export->args['order_order'] = $order;
					$export->args['order_ids'] = ( isset( $_GET['order_ids'] ) ? sanitize_text_field( $_GET['order_ids'] ) : null );

					// Override Filter Orders by Order ID if a single order transient is set
					$single_export_order_ids = get_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
					if( $single_export_order_ids !== false )
						$export->args['order_ids'] = sanitize_text_field( $single_export_order_ids );
					unset( $single_export_order_ids );

					if( $export->scheduled_export ) {

						// Scheduled export engine

						// Order Status
						$order_filter_status = get_post_meta( $scheduled_export, '_filter_order_status', true );
						$export->args['order_status'] = ( !empty( $order_filter_status ) ? (array)$order_filter_status : array() );
						// Order Date
						$order_dates_filter = get_post_meta( $scheduled_export, '_filter_order_date', true );
						if( $order_dates_filter ) {
							$export->args['order_dates_filter'] = $order_dates_filter;
							switch( $order_dates_filter ) {

								case 'manual':
									$order_filter_dates_from = get_post_meta( $scheduled_export, '_filter_order_dates_from', true );
									$order_filter_dates_to = get_post_meta( $scheduled_export, '_filter_order_dates_to', true );
									$export->args['order_dates_from'] = ( !empty( $order_filter_dates_from ) ? sanitize_text_field( $order_filter_dates_from ) : false );
									$export->args['order_dates_to'] = ( !empty( $order_filter_dates_to ) ? sanitize_text_field( $order_filter_dates_to ) : false );
									break;

								case 'variable':
									$order_filter_date_variable = get_post_meta( $scheduled_export, '_filter_order_date_variable', true );
									$order_filter_date_variable_length = get_post_meta( $scheduled_export, '_filter_order_date_variable_length', true );
									$export->args['order_dates_filter_variable'] = ( !empty( $order_filter_date_variable ) ? absint( $order_filter_date_variable ) : false );
									$export->args['order_dates_filter_variable_length'] = ( !empty( $order_filter_date_variable_length ) ? sanitize_text_field( $order_filter_date_variable_length ) : false );
									break;

							}
						}
						// Product
						$order_filter_product = get_post_meta( $scheduled_export, '_filter_order_product', true );
						$export->args['order_product'] = ( !empty( $order_filter_product ) ? (array)$order_filter_product : array() );
						// Billing Country
						$order_filter_billing_country = get_post_meta( $scheduled_export, '_filter_order_billing_country', true );
						$export->args['order_billing_country'] = ( !empty( $order_filter_billing_country ) ? array_map( 'sanitize_text_field', $order_filter_billing_country ) : false );
						// Shipping Country
						$order_filter_shipping_country = get_post_meta( $scheduled_export, '_filter_order_shipping_country', true );
						$export->args['order_shipping_country'] = ( !empty( $order_filter_shipping_country ) ? array_map( 'sanitize_text_field', $order_filter_shipping_country ) : false );
						// Payment Gateway
						$order_filter_payment = get_post_meta( $scheduled_export, '_filter_order_payment', true );
						$export->args['order_payment'] = ( !empty( $order_filter_payment ) ? array_map( 'sanitize_text_field', $order_filter_payment ) : false );
						// Shipping Method
						$order_filter_shipping = get_post_meta( $scheduled_export, '_filter_order_shipping', true );
						$export->args['order_shipping'] = ( !empty( $order_filter_shipping ) ? array_map( 'sanitize_text_field', $order_filter_shipping ) : false );
					} else {

						// CRON export engine

						// Order Status
						if( isset( $_GET['order_status'] ) ) {
							$order_filter_status = sanitize_text_field( $_GET['order_status'] );
							$order_filter_status = explode( ',', $order_filter_status );
							$export->args['order_status'] = $order_filter_status;
						}
						// Product
						if( isset( $_GET['order_product'] ) ) {
							$order_filter_product = sanitize_text_field( $_GET['order_product'] );
							$order_filter_product = explode( ',', $order_filter_product );
							$export->args['order_product'] = $order_filter_product;
						}
						// Order Date
						if( isset( $_GET['order_date_from'] ) && isset( $_GET['order_date_to'] ) ) {
							$order_filter_dates_from = $_GET['order_date_from'];
							$order_filter_dates_to = $_GET['order_date_to'];
							$export->args['order_dates_filter'] = 'manual';
							$export->args['order_dates_from'] = ( !empty( $order_filter_dates_from ) ? sanitize_text_field( $order_filter_dates_from ) : false );
							$export->args['order_dates_to'] = ( !empty( $order_filter_dates_to ) ? sanitize_text_field( $order_filter_dates_to ) : false );
						}
						// Billing Country
						if( isset( $_GET['billing_country'] ) ) {
							$order_filter_billing_country = sanitize_text_field( $_GET['billing_country'] );
							$order_filter_billing_country = explode( ',', $order_filter_billing_country );
							$export->args['order_billing_country'] = ( !empty( $order_filter_billing_country ) ? $order_filter_billing_country : false );
						}
						// Shipping Country
						if( isset( $_GET['shipping_country'] ) ) {
							$order_filter_shipping_country = sanitize_text_field( $_GET['shipping_country'] );
							$order_filter_shipping_country = explode( ',', $order_filter_shipping_country );
							$export->args['order_shipping_country'] = ( !empty( $order_filter_shipping_country ) ? $order_filter_shipping_country : false );
						}
						// Payment Gateway
						if( isset( $_GET['payment_gateway'] ) ) {
							$order_filter_payment = sanitize_text_field( $_GET['order_payment'] );
							$order_filter_payment = explode( ',', $order_filter_payment );
							$export->args['order_payment'] = ( !empty( $order_filter_payment ) ? $order_filter_payment : false );
						}
						// Shipping Method
						if( isset( $_GET['shipping_method'] ) ) {
							$order_filter_shipping = sanitize_text_field( $_GET['shipping_method'] );
							$order_filter_shipping = explode( ',', $order_filter_shipping );
							$export->args['order_shipping'] = ( !empty( $order_filter_shipping ) ? $order_filter_shipping : false );
						}
					}
					break;

				case 'subscription':
					$export->args['subscription_orderby'] = $orderby;
					$export->args['subscription_order'] = $order;
					break;

				case 'product_vendor':
					$export->args['product_vendor_orderby'] = $orderby;
					$export->args['product_vendor_order'] = $order;
					break;

				case 'user':
					if( $export->scheduled_export ) {

						// Scheduled export engine

						// User Role
						$user_filter_role = get_post_meta( $scheduled_export, '_filter_user_role', true );
						$export->args['user_roles'] = ( !empty( $user_filter_role ) ? array_map( 'sanitize_text_field', $user_filter_role ) : false );

					} else {

						// CRON export engine

						// User Role
						if( isset( $_GET['user_role'] ) ) {
							$user_filter_role = sanitize_text_field( $_GET['user_role'] );
							$user_filter_role = explode( ',', $user_filter_role );
							$export->args['user_roles'] = $user_filter_role;
						}
					}
					break;

				case 'review':
					$export->args['review_orderby'] = $orderby;
					$export->args['review_order'] = $order;
					break;

				case 'commission':
					// Commission Date
					$commission_dates_filter = get_post_meta( $scheduled_export, '_filter_commission_date', true );
					if( $commission_dates_filter ) {
						$export->args['commission_dates_filter'] = $commission_dates_filter;
						switch( $commission_dates_filter ) {

							case 'manual':
								$commission_filter_dates_from = get_post_meta( $scheduled_export, '_filter_commission_dates_from', true );
								$commission_filter_dates_to = get_post_meta( $scheduled_export, '_filter_commission_date_to', true );
								$export->args['commission_dates_from'] = ( !empty( $commission_filter_dates_from ) ? sanitize_text_field( $commission_filter_dates_from ) : false );
								$export->args['commission_dates_to'] = ( !empty( $commission_filter_dates_to ) ? sanitize_text_field( $commission_filter_dates_to ) : false );
								break;

							case 'variable':
								$commission_filter_date_variable = get_post_meta( $scheduled_export, '_filter_commission_date_variable', true );
								$commission_filter_date_variable_length = get_post_meta( $scheduled_export, '_filter_commission_date_variable_length', true );
								$export->args['commission_dates_filter_variable'] = ( !empty( $commission_filter_date_variable ) ? absint( $commission_filter_date_variable ) : false );
								$export->args['commission_dates_filter_variable_length'] = ( !empty( $commission_filter_date_variable_length ) ? sanitize_text_field( $commission_filter_date_variable_length ) : false );
								break;

						}
					}
					break;

				case 'shipping_class':
					$export->args['shipping_class_orderby'] = $orderby;
					$export->args['shipping_class_order'] = $order;
					break;

			}
			$export->filename = sprintf( '%s.%s', woo_ce_generate_filename( $export->type ), $file_extension );
			// Let's spin up PHPExcel for supported Export Types and Export Formats
			if( in_array( $export->export_format, array( 'csv', 'tsv', 'xls', 'xlsx' ) ) ) {

				// Check if we are using PHPExcel or not for supported Export Types
				$dataset = woo_ce_export_dataset( $export->type );
				if( !empty( $dataset ) ) {
					// Check that PHPExcel is where we think it is
					if( file_exists( WOO_CD_PATH . 'classes/PHPExcel.php' ) ) {
						// Check if PHPExcel has already been loaded
						if( !class_exists( 'PHPExcel' ) ) {
							include_once( WOO_CD_PATH . 'classes/PHPExcel.php' );
						} else {
							woo_ce_error_log( sprintf( '%s: Warning: %s', $export->filename, __( 'The PHPExcel library was already loaded by another WordPress Plugin, if there\'s issues with your export file you know where to look.', 'woocommerce-exporter' ) ) );
						}
						$excel = new PHPExcel();
						$excel->setActiveSheetIndex( 0 );
						$excel->getActiveSheet()->setTitle( ucfirst( $export->type ) );
						$row = 1;
						// Skip headers if Heading Formatting is turned off
						if( $export->header_formatting ) {
							$col = 0;
							foreach( $export->columns as $column ) {
								$excel->getActiveSheet()->setCellValueByColumnAndRow( $col, $row, wp_specialchars_decode( $column, 'ENT_QUOTES' ) );
								$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( true );
								$excel->getActiveSheet()->getColumnDimensionByColumn( $col )->setAutoSize( true );
								$col++;
							}
							$row = 2;
						}
						foreach( $dataset as $data ) {
							$col = 0;
							foreach( array_keys( $export->fields ) as $field ) {
								$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( false );
								if( $export->encoding == 'UTF-8' ) {
									if( woo_ce_detect_value_string( ( isset( $data->$field ) ? $data->$field : null ) ) ) {
										// Treat this cell as a string
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValueExplicit( ( isset( $data->$field ) ? wp_specialchars_decode( $data->$field, 'ENT_QUOTES' ) : '' ), PHPExcel_Cell_DataType::TYPE_STRING );
									} else {
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValue( ( isset( $data->$field ) ? wp_specialchars_decode( $data->$field, 'ENT_QUOTES' ) : '' ) );
									}
								} else {
									// PHPExcel only deals with UTF-8 regardless of encoding type
									if( woo_ce_detect_value_string( ( isset( $data->$field ) ? $data->$field : null ) ) ) {
										// Treat this cell as a string
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValueExplicit( ( isset( $data->$field ) ? utf8_encode( wp_specialchars_decode( $data->$field, 'ENT_QUOTES' ) ) : '' ), PHPExcel_Cell_DataType::TYPE_STRING );
									} else {
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValue( ( isset( $data->$field ) ? utf8_encode( wp_specialchars_decode( $data->$field, 'ENT_QUOTES' ) ) : '' ) );
									}
								}
								$col++;
							}
							$row++;
						}
						// Load our custom Writer for the CSV and TSV file types
						if( in_array( $export->export_format, array( 'csv', 'tsv' ) ) ) {
							// We need to load this after the PHPExcel Class has been created
							woo_cd_load_phpexcel_sed_csv_writer();
						}
						$objWriter = PHPExcel_IOFactory::createWriter( $excel, $php_excel_format );
						switch( $export->export_format ) {

							case 'csv':
								$objWriter->setUseBOM( true );
								// Check if we're using a non-standard delimiter
								if( $export->delimiter != ',' )
									$objWriter->setDelimiter( $export->delimiter );
								break;

							case 'tsv':
								$objWriter->setUseBOM( true );
								$objWriter->setDelimiter( "\t" );
								break;

							case 'xlsx':
								$objWriter->setPreCalculateFormulas( false );
								break;

						}
						if( in_array( $gui, array( 'raw' ) ) ) {
							$objWriter->save( 'php://output' );
						} else {
							// Save file to PHP tmp then pass to PHPExcel
							$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
							// Check if we were given a temporary filename
							if( $temp_filename == false ) {
								$export->error = sprintf( __( 'We could not create a temporary export file in %s, ensure that WordPress can read and write files here and try again.', 'woocommerce-exporter' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
								woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
							} else {
								$objWriter->save( $temp_filename );
								$bits = file_get_contents( $temp_filename );
							}
						}

						// Clean up PHPExcel
						$excel->disconnectWorksheets();
						unset( $objWriter, $excel );

					} else {
						$export->error = __( 'We couldn\'t load the PHPExcel library, this file should be present.', 'woocommerce-exporter' );
						woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
					}
				}
			// Run the default engine for the XML and RSS export formats
			} else if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
				// Check if SimpleXMLElement is present
				if( class_exists( 'SED_SimpleXMLElement' ) ) {
					if( $export->export_format == 'xml' ) {
						$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_xml_first_line', '<?xml version="1.0" encoding="%s"?><%s/>' ), esc_attr( $export->encoding ), esc_attr( apply_filters( 'woo_ce_export_xml_store_node', 'store' ) ) ) );
						if( woo_ce_get_option( 'xml_attribute_url', 1 ) )
							$xml->addAttribute( 'url', get_site_url() );
						if( woo_ce_get_option( 'xml_attribute_date', 1 ) )
							$xml->addAttribute( 'date', date( 'Y-m-d' ) );
						if( woo_ce_get_option( 'xml_attribute_time', 0 ) )
							$xml->addAttribute( 'time', date( 'H:i:s' ) );
						if( woo_ce_get_option( 'xml_attribute_title', 1 ) )
							$xml->addAttribute( 'name', htmlspecialchars( get_bloginfo( 'name' ) ) );
						if( woo_ce_get_option( 'xml_attribute_export', 1 ) )
							$xml->addAttribute( 'export', htmlspecialchars( $export->type ) );
						if( woo_ce_get_option( 'xml_attribute_orderby', 1 ) )
							$xml->addAttribute( 'orderby', $orderby );
						if( woo_ce_get_option( 'xml_attribute_order', 1 ) )
							$xml->addAttribute( 'order', $order );
						if( woo_ce_get_option( 'xml_attribute_limit', 1 ) )
							$xml->addAttribute( 'limit', $export->limit_volume );
						if( woo_ce_get_option( 'xml_attribute_offset', 1 ) )
							$xml->addAttribute( 'offset', $export->offset );
						$xml = apply_filters( 'woo_ce_export_xml_before_dataset', $xml );
						$xml = woo_ce_export_dataset( $export->type, $xml );
						$xml = apply_filters( 'woo_ce_export_xml_after_dataset', $xml );
					} else if( $export->export_format == 'rss' ) {
						$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_rss_first_line', '<?xml version="1.0" encoding="%s"?><rss version="2.0"%s/>' ), esc_attr( $export->encoding ), ' xmlns:g="http://base.google.com/ns/1.0"' ) );
						$child = $xml->addChild( apply_filters( 'woo_ce_export_rss_channel_node', 'channel' ) );
						$child->addChild( 'title', woo_ce_get_option( 'rss_title', '' ) );
						$child->addChild( 'link', woo_ce_get_option( 'rss_link', '' ) );
						$child->addChild( 'description', woo_ce_get_option( 'rss_description', '' ) );
						$xml = apply_filters( 'woo_ce_export_rss_before_dataset', $xml );
						$xml = woo_ce_export_dataset( $export->type, $child );
						$xml = apply_filters( 'woo_ce_export_rss_after_dataset', $xml );
					}
					$bits = woo_ce_format_xml( $xml );
					// Save file to PHP tmp
					$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
					// Check if we were given a temporary filename
					if( $temp_filename == false ) {
						$export->error = sprintf( __( 'We could not create a temporary export file in %s, ensure that WordPress can read and write files here and try again.', 'woo_ce' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
						woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
					} else {
						// Populate the temporary file
						$handle = fopen( $temp_filename, 'w' );
						fwrite( $handle, $bits );
						fclose( $handle );
						unset( $handle );
					}
				} else {
					$bits = false;
					$export->error = __( 'The SimpleXMLElement class does not exist for XML file generation', 'woocommerce-exporter' );
					woo_ce_error_log( sprintf( 'Error: %s', $export->error ) );
				}
			}
			if( !empty( $bits ) ) {
				$output = '<p>' . __( 'Export completed successfully.', 'woocommerce-exporter' ) . '</p>';
				if( $gui == 'gui' )
					$output .= '<textarea readonly="readonly">' . esc_textarea( str_replace( '<br />', "\n", $bits ) ) . '</textarea>';
			} else {
				if( $gui == 'gui' ) {
					$output = sprintf( '<p>%s</p>', __( 'No export entries were found.', 'woocommerce-exporter' ) );
				} else {
					if( $export->scheduled_export ) {
						$export->error = __( 'No export entries were found.', 'woocommerce-exporter' );
					} else {
						woo_ce_error_log( sprintf( '%s: Warning: %s', $export->filename, __( 'No export entries were found', 'woocommerce-exporter' ) ) );
						return;
					}
				}
			}
		}
	}

	// Return raw export to browser without file headers
	if( $gui == 'raw' ) {

		if( !empty( $bits ) )
			return $bits;

	// Return export as file download to browser
	} else if( $gui == 'download' ) {

		if( !empty( $bits ) ) {
			woo_ce_generate_file_headers( $post_mime_type );
			if( defined( 'DOING_AJAX' ) || get_transient( WOO_CD_PREFIX . '_single_export_format' ) !== false )
				echo $bits;
			else
				return $bits;
		}

	// HTTP Post export contents to remote URL
	} else if( $gui == 'post' ) {

		if( !empty( $bits ) ) {
			$args = apply_filters( 'woo_ce_cron_export_post_args', array(
				'method'      => 'POST',
				'timeout'     => 60,
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'headers'     => array(
					'accept'       => $post_mime_type,
					'content-type' => $post_mime_type
				),
				'body'        => $bits,
				'cookies'     => array(),
				'user-agent'  => sprintf( 'WordPress/%s', $GLOBALS['wp_version'] ),
			) );
			$response = wp_remote_post( $export->to, $args );
			if( is_wp_error( $response ) ) {
				$export->error = sprintf( __( 'Could not HTTP Post using wp_remote_post(), response: %s', 'woocommerce-exporter' ), $response->get_error_message() );
				woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
				if( !$export->scheduled_export )
					return;
			} else {
				woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'Remote POST sent to %s', 'woocommerce-exporter' ), $export->to ) ) );
			}
		}

	// Output to screen in friendly design with on-screen error responses
	} else if( $gui == 'gui' ) {

		if( file_exists( WOO_CD_PATH . 'templates/admin/cron.php' ) ) {
			include_once( WOO_CD_PATH . 'templates/admin/cron.php' );
		} else {
			$export->error = __( 'Could not load template file within /templates/admin/cron.php', 'woocommerce-exporter' );
			woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
		}
		if( isset( $output ) )
			echo $output;
		echo '
	</body>
</html>';

	// Save export file locally outside the WordPress Media
	} else if( $gui == 'save' ) {

		if( $export->filename && !empty( $bits ) ) {
			$path = get_post_meta( $scheduled_export, '_method_save_path', true );
			$filename = get_post_meta( $scheduled_export, '_method_save_filename', true );
			// Switch to fixed export filename if provided
			if( !empty( $filename ) )
				$export->filename = sprintf( '%s.%s', woo_ce_generate_filename( $export->type, $filename ), $file_extension );
			// Change directory if neccesary
			if( !empty( $path ) ) {
				if( is_dir( ABSPATH . $path ) ) {
					$directory_response = @chdir( ABSPATH . $path );
					if( $directory_response == false )
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Could not change the current directory on this server', 'woocommerce-exporter' ) ) );
				} else {
					// Attempt to create directory
					if( wp_mkdir_p( ABSPATH . $path ) ) {
						woo_ce_error_log( sprintf( 'Warning: %s', sprintf( __( 'Could not detect an existing directory from the given file path so we created it, %s', 'woocommerce-exporter' ), ABSPATH . $path ) ) );
					} else {
						$export->error = sprintf( __( 'Could not detect or generate a directory from the given file path, %s', 'woocommerce-exporter' ), ABSPATH . $path );
						woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
					}
				}
			}
			if( $handle = fopen( ABSPATH . $path . $export->filename, 'w' ) ) {
				if( fwrite( $handle, $bits ) == false ) {
					$export->error = sprintf( __( 'Could not write to the open file on this server at %s', 'woocommerce-exporter' ), ABSPATH . $path . $export->filename );
					woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
				}
				$connection_response = fclose( $handle );
				if( $connection_response == false ) {
					$export->error = sprintf( __( 'Could not close an open file pointer on this server at %s', 'woocommerce-exporter' ), ABSPATH . $path . $export->filename );
					woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
				}
			} else {
				$export->error = sprintf( __( 'Could not create or open a file on this server at %s', 'woocommerce-exporter' ), ABSPATH . $path . $export->filename );
				woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
			}
			unset( $handle );
		}

	// E-mail export file to preferred address or WordPress site owner address
	} else if( $gui == 'email' ) {

		if( !empty( $bits ) ) {

			global $woocommerce;

			// Check if the required filename already exists
			if( file_exists( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) . '/' . $export->filename ) )
				unlink( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) . '/' . $export->filename );
			$rename_response = @rename( $temp_filename, apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) . '/' . $export->filename );
			if( $rename_response == false ) {
				$export->error = sprintf( __( 'We could not rename the temporary export file in %s, ensure that WordPress can read and write files here and try again.', 'woocommerce-exporter' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
				woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
			} else {
				$temp_filename = apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) . '/' . $export->filename;
				$mailer = $woocommerce->mailer();
				$subject = woo_ce_cron_email_subject( $export->type, $export->filename );
				$attachment = $temp_filename;
				$email_heading = sprintf( __( 'Export: %s', 'woocommerce-exporter' ), ucwords( $export->type ) );
				$recipient_name = apply_filters( 'woo_ce_email_recipient_name', __( 'there', 'woocommerce-exporter' ) );
				$email_contents = woo_ce_cron_email_contents( $export->type, $export->filename );
				if( !empty( $export->to ) ) {
					// Check that the attachment is populated
					if( !empty( $attachment ) ) {

						// Buffer
						ob_start();

						// Get mail template, preference WordPress Theme, Plugin, fallback
						if( file_exists( get_stylesheet_directory() . '/woocommerce/emails/scheduled_export.php' ) ) {
							include_once( get_stylesheet_directory() . '/woocommerce/emails/scheduled_export.php' );
						} else if( file_exists( WOO_CD_PATH . 'templates/emails/scheduled_export.php' ) ) {
							include_once( WOO_CD_PATH . 'templates/emails/scheduled_export.php' );
						} else {
							echo wpautop( sprintf( __( 'Hi %s', 'woocommerce-exporter' ), $recipient_name ) );
							echo $email_contents;
							$export->error = sprintf( __( 'Could not load template file %s within %s, defaulted to hardcoded template.', 'woocommerce-exporter' ), 'scheduled_export.php', '/templates/emails/...' );
							woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
						}

						// Get contents
						$message = ob_get_clean();

						// Send the mail using WooCommerce mailer
						if( function_exists( 'woocommerce_mail' ) ) {
							woocommerce_mail( $export->to, $subject, $message, null, $attachment );
						} else {
							// Default to wp_mail()
							add_filter( 'wp_mail_content_type', 'woo_ce_set_html_content_type' );
							wp_mail( $export->to, $subject, $message, null, $attachment );
							remove_filter( 'wp_mail_content_type', 'woo_ce_set_html_content_type' );
						}
						woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'Scheduled export e-mail of %s sent to %s', 'woocommerce-exporter' ), $export->filename, $export->to ) ) );
					} else {
						$export->error = sprintf( __( 'Scheduled export e-mail of %s sent to %s failed due to no export entries were found', 'woocommerce-exporter' ), $export->filename, $export->to );
						woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
					}
				} else {
					$export->error = sprintf( __( 'Scheduled export e-mail of %s failed due to the e-mail recipient field being empty', 'woocommerce-exporter' ), $export->filename );
					woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
				}
				// Delete the export file regardless of whether e-mail was successful or not
				unlink( $temp_filename );
			}
			unset( $rename_response, $temp_filename );
		}

	// Save export file to WordPress Media before sending/saving/etc. action
	} else if( in_array( $gui, array( 'gui', 'archive', 'url', 'file', 'email', 'ftp' ) ) ) {

		$upload = false;
		if( $export->filename && !empty( $bits ) ) {
			$post_ID = woo_ce_save_file_attachment( $export->filename, $post_mime_type );
			$upload = wp_upload_bits( $export->filename, null, $bits );
			if( ( $post_ID == false ) || $upload['error'] ) {
				wp_delete_attachment( $post_ID, true );
				$export->error = sprintf( __( 'Could not upload file to WordPress Media: %s', 'woocommerce-exporter' ), $upload['error'] );
				woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
				if( !$export->scheduled_export )
					return;
			}
			if( $post_ID && file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
				wp_update_attachment_metadata( $post_ID, $attach_data );
				update_attached_file( $post_ID, $upload['file'] );
				if( !empty( $post_ID ) ) {
					woo_ce_save_file_guid( $post_ID, $export->type, $upload['url'] );
					woo_ce_save_file_details( $post_ID );
				}
			} else {
				woo_ce_error_log( sprintf( '%s: Warning: %s', $export->filename, __( 'Could not load image.php within /wp-admin/includes/image.php', 'woocommerce-exporter' ) ) );
			}
		}

		// Return URL to export file
		if( $gui == 'url' )
			return $upload['url'];

		// Return system path to export file
		if( $gui == 'file' )
			return $upload['file'];

		// Upload export file to FTP server
		if( $gui == 'ftp' ) {

			// Load up our FTP/SFTP resources
			$host = get_post_meta( $scheduled_export, '_method_ftp_host', true );
			if( !empty( $host ) )
				$host = woo_ce_format_ftp_host( $host );
			$port = get_post_meta( $scheduled_export, '_method_ftp_port', true );
			$port = ( !empty( $port ) ? absint( $port ) : false );
			$user = get_post_meta( $scheduled_export, '_method_ftp_user', true );
			$pass = get_post_meta( $scheduled_export, '_method_ftp_pass', true );
			$path = get_post_meta( $scheduled_export, '_method_ftp_path', true );
			$filename = get_post_meta( $scheduled_export, '_method_ftp_filename', true );
			// Switch to fixed export filename if provided
			if( !empty( $filename ) )
				$export->filename = woo_ce_generate_filename( $export->type, $filename ) . '.' . $file_extension;

			// Check what protocol are we using; FTP or SFTP?
			$protocol = get_post_meta( $scheduled_export, '_method_ftp_protocol', true );
			switch( $protocol ) {

				case 'ftp':
				default:
					// Check if ftp_connect() is available
					if( function_exists( 'ftp_connect' ) ) {
						$passive = get_post_meta( $scheduled_export, '_method_ftp_passive', true );
						$timeout = get_post_meta( $scheduled_export, '_method_ftp_timeout', true );
						if( $connection = @ftp_connect( $host, $port ) ) {
							// Update the FTP timeout if available and if a timeout was provided at export
							if( function_exists( 'ftp_get_option' ) && function_exists( 'ftp_set_option' ) ) {
								$remote_timeout = @ftp_get_option( $connection, FTP_TIMEOUT_SEC );
								$timeout = absint( $timeout );
								if( $remote_timeout !== false && !empty( $timeout ) ) {
									// Compare the server timeout and the timeout provided at export
									if( $remote_timeout <> $timeout ) {
										if( @ftp_set_option( $connection, FTP_TIMEOUT_SEC, $timeout ) == false )
											woo_ce_error_log( sprintf( '%s: Warning: %s', $export->filename, sprintf( __( 'Could not change the FTP server timeout on %s', 'woocommerce-exporter' ), $host ) ) );
									}
								}
								unset( $remote_timeout );
							} else {
								woo_ce_error_log( sprintf( '%s: Warning: %s', 'woocommerce-exporter', $export->filename, sprintf( __( 'We could not change the FTP server timeout on %s as the PHP functions ftp_get_option() and ftp_set_option() are unavailable to WordPress.', 'woocommerce-exporter' ), $host ) ) );
							}
							if( @ftp_login( $connection, $user, $pass ) ) {
								// Check if Transfer Mode is set to Auto/Pasive and if passive mode is available
								if( in_array( $passive, array( 'auto', 'passive' ) ) ) {
									$features = @ftp_raw( $connection, 'FEAT' );
									if( !empty( $features ) ) {
										if( in_array( 'PASV', $features ) ) {
											if( @ftp_pasv( $connection, true ) == false )
												woo_ce_error_log( sprintf( '%s: Warning: %s', 'woocommerce-exporter', $export->filename, sprintf( __( 'Could not switch to FTP passive mode on %s', 'woocommerce-exporter' ), $host ) ) );
										}
									}
									unset( $features );
								}
								unset( $passive );
								$directory_response = true;
								// Change directory if neccesary
								if( !empty( $path ) ) {
									$current_directory  = @ftp_pwd( $connection );
									if( $current_directory !== false ) {
										$directory_response = @ftp_chdir( $connection, $path );
										if( $directory_response == false ) {
											$export->error = sprintf( __( 'Could not change the current directory on the FTP server to %s', 'woocommerce-exporter' ), $path );
											woo_ce_error_log( sprintf( 'Error: %s', $export->error ) );
										}
									} else {
										$directory_response = false;
										$export->error = sprintf( __( 'Could not return the current directory name on the FTP server to %s', 'woocommerce-exporter' ), $path );
										woo_ce_error_log( sprintf( 'Error: %s', $export->error ) );
									}
									unset( $current_directory );
								}
								if( $directory_response ) {
									// Switch between ftp_put and ftp_fput
									$connection_response = 'ftp_put';
									switch( apply_filters( 'woo_ce_cron_export_ftp_switch', 'ftp_put' ) ) {
	
										default:
										case 'ftp_put':
											$connection_response = @ftp_put( $connection, $export->filename, $upload['file'], FTP_ASCII );
											break;
	
										case 'ftp_fput':
											$connection_response = @ftp_fput( $connection, $export->filename, $bits, FTP_ASCII );
											break;
	
									}
									if( $connection_response ) {
										if( !empty( $path ) )
											woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'Scheduled export of %s to %s via FTP uploaded', 'woocommerce-exporter' ), $export->filename, $path ) ) );
										else
											woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'Scheduled export of %s via FTP uploaded', 'woocommerce-exporter' ), $export->filename ) ) );
									} else {
										if( !empty( $path ) )
											$export->error = sprintf( __( 'There was a problem uploading %s to %s via FTP, response: %s', 'woocommerce-exporter' ), $export->filename, $path, woo_ce_error_get_last_message() );
										else
											$export->error = sprintf( __( 'There was a problem uploading %s via FTP, response: %s', 'woocommerce-exporter' ), $export->filename, woo_ce_error_get_last_message() );
										woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
										if( @ftp_delete( $connection, $export->filename ) == false ) {
											if( !empty( $path ) )
												woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, sprintf( __( 'Could not delete failed FTP upload of %s from %s', 'woocommerce-exporter' ), $export->filename, $path ) ) );
											else
												woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, sprintf( __( 'Could not delete failed FTP upload of %s', 'woocommerce-exporter' ), $export->filename ) ) );
										}
									}
									unset( $connection_response );
								}
							} else {
								$export->error = sprintf( __( 'Login incorrect for user %s on FTP server at %s, response: %s', 'woocommerce-exporter' ), $user, $host, woo_ce_error_get_last_message() );
								woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
							}
						} else {
							$export->error = sprintf( __( 'There was a problem connecting to %s via FTP', 'woocommerce-exporter' ), $host );
							woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
						}
					} else {
						$export->error = __( 'The function ftp_connect() is disabled within your WordPress site, cannot upload to FTP server', 'woocommerce-exporter' );
						woo_ce_error_log( __( '%s: Error: %s', 'woocommerce-exporter' ), $export->filename, $export->error );
					}
					break;

				case 'sftp':
					// Check if ssh2_connect() is available
					if( function_exists( 'ssh2_connect' ) ) {
						if( $connection = @ssh2_connect( $host, $port ) ) {
							if( @ssh2_auth_password( $connection, $user, $pass ) ) {
								// Initialize SFTP subsystem
								if( $session = @ssh2_sftp( $connection ) ) {
									if( $handle = fopen( sprintf( 'ssh2.sftp://%s/%s/%s', $session, $path, $export->filename ), 'w+' ) ) {
										if( !empty( $path ) )
											woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'Scheduled export of %s to %s via SFTP uploaded', 'woocommerce-exporter' ), $export->filename, $path ) ) );
										else
											woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'Scheduled export of %s via SFTP uploaded', 'woocommerce-exporter' ), $export->filename ) ) );
									} else {
										if( !empty( $path ) )
											$export->error = sprintf( __( 'There was a problem uploading %s to %s via SFTP', 'woocommerce-exporter' ), $export->filename, $path );
										else
											$export->error = sprintf( __( 'There was a problem uploading %s via SFTP', 'woocommerce-exporter' ), $export->filename );
										woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
									}
								} else {
									$export->error = sprintf( __( 'Could not initialize SFTP subsystem on SFTP server at %s', 'woocommerce-exporter' ), $host );
									woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
								}
							} else {
								$export->error = sprintf( __( 'Login incorrect for user %s on SFTP server at %s', 'woocommerce-exporter' ), $user, $host );
								woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
							}
						} else {
							$export->error = sprintf( __( 'There was a problem connecting to %s via SFTP', 'woocommerce-exporter' ), $host );
							woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, $export->error ) );
						}
					} else {
						$export->error = __( 'The function ssh2_connect() is disabled within your WordPress site, cannot upload to SFTP server', 'woocommerce-exporter' );
						woo_ce_error_log( sprintf( __( '%s: Error: %s', 'woocommerce-exporter' ), $export->filename, $export->error ) );
					}
					break;

			}
			// Delete the export file regardless of whether upload was successful or not
			wp_delete_attachment( $post_ID, true );
		}

	}

	// Only include scheduled exports to the Recent Scheduled Exports list
	if( $export->scheduled_export ) {

		if( !isset( $post_ID ) )
			$post_ID = 0;
		woo_ce_add_recent_scheduled_export( $scheduled_export, $gui, $post_ID );

		// Link the Attachment to the scheduled export
		if( !empty( $post_ID ) )
			update_post_meta( $post_ID, '_scheduled_id', $scheduled_export );

		// Increment the total_exports Post meta on the scheduled export
		$total_exports = absint( get_post_meta( $scheduled_export, '_total_exports', true ) );
		$total_exports++;
		update_post_meta( $scheduled_export, '_total_exports', $total_exports );
		$time = current_time( 'timestamp', 1 );
		update_post_meta( $scheduled_export, '_last_export', $time );

	}

	delete_option( WOO_CD_PREFIX . '_exported' );

	// If the CRON process gets this far, pass on the good news!
	return true;

}

function woo_ce_set_html_content_type() {

	return 'text/html';

}

function woo_ce_check_cron_export_arguments( $args ) {

	$args->export_format = ( $args->export_format != '' ? $args->export_format : 'csv' );
	$args->limit_volume = ( $args->limit_volume != '' ? $args->limit_volume : -1 );
	$args->offset = ( $args->offset != '' ? $args->offset : 0 );
	if( isset( $args->date_format ) ) {
		$args->date_format = ( $args->date_format != '' ? $args->date_format : 'd/m/Y' );
		// Override for corrupt WordPress option 'date_format' from older releases
		if( $args->date_format == '1' || $args->date_format == '' || $args->date_format == false ) {
			woo_ce_error_log( sprintf( 'Warning: %s', __( 'Date Format export option was corrupted, defaulted to d/m/Y' ) ) );
			$args->date_format = 'd/m/Y';
		}
	}
	// Override for Order Item Types passed via CRON
	if( !empty( $args->order_items_types ) && !is_array( $args->order_items_types ) ) {
		$args->order_items_types = explode( ',', $args->order_items_types );
	} else if( empty( $args->order_items_types ) ) {
		// Override for empty Order Item Types
		$args->order_items_types = array( 'line_item' );
	}
	// Override for empty Export Fields
	if( empty( $args->fields ) ) {
		woo_ce_error_log( sprintf( 'Error: %s', __( 'No export fields were selected, defaulted to all', 'woocommerce-exporter' ) ) );
		if( function_exists( sprintf( 'woo_ce_get_%s_fields', $args->type ) ) )
			$args->fields = call_user_func_array( 'woo_ce_get_' . $args->type . '_fields', array( 'summary' ) );
	}
	return $args;

}

function woo_ce_cron_export_fields( $export_type = '', $is_scheduled = 0, $scheduled_export = 0 ) {

	global $export;

	$fields = array();

	$export_fields = 'all';
	// Override the export fields if the single order Transient is set
	$single_export_fields = get_transient( WOO_CD_PREFIX . '_single_export_fields' );
	if( $single_export_fields !== false ) {
		$export_fields = $single_export_fields;
	} else {
		if( $is_scheduled == '0' )
			$export_fields = woo_ce_get_option( 'cron_fields', 'all' );
		else if( $is_scheduled == '1' )
			$export_fields = get_post_meta( $scheduled_export, '_export_fields', true );
	}
	unset( $single_export_fields );

	if( $export_fields == 'all' ) {
		// Check that the fields list exists for that export type
		if( function_exists( sprintf( 'woo_ce_get_%s_fields', $export_type ) ) )
			$fields = call_user_func_array( 'woo_ce_get_' . $export_type . '_fields', array( 'summary' ) );
	} else if( $export_fields == 'saved' ) {
		// Fall back to default of stored export fields for that export type
		$fields = woo_ce_get_option( $export_type . '_fields', array() );
	}
	return $fields;

}

function woo_ce_cron_email_subject( $type = '', $filename = '' ) {

	global $export;

	$scheduled_export = ( $export->scheduled_export ? absint( get_transient( WOO_CD_PREFIX . '_scheduled_export_id' ) ) : 0 );

	$subject = '';
	if( !empty( $scheduled_export ) ) {
		$subject = get_post_meta( $scheduled_export, '_method_email_subject', true );
		// Default subject
		if( empty( $subject ) )
			$subject = apply_filters( 'woo_ce_default_email_subject', __( '[%store_name%] Export: %export_type% (%export_filename%)', 'woocommerce-exporter' ), $scheduled_export );
	} else {
		// Override the e-mail subject if the single order Transient is set
		$single_export_method_email_subject = get_transient( WOO_CD_PREFIX . '_single_export_method_email_subject' );
		if( $single_export_method_email_subject !== false )
			$subject = $single_export_method_email_subject;
		unset( $single_export_method_email_subject );
		// Default subject
		if( empty( $subject ) )
			$subject = apply_filters( 'woo_ce_default_email_subject', __( '[%store_name%] Export: %export_type% (%export_filename%)', 'woocommerce-exporter' ) );
	}
	$subject = str_replace( '%store_name%', sanitize_title( get_bloginfo( 'name' ) ), $subject );
	$subject = str_replace( '%export_type%', ucwords( $type ), $subject );
	$subject = str_replace( '%export_filename%', $filename, $subject );
	return $subject;

}

function woo_ce_cron_email_contents( $type = '', $filename = '' ) {

	global $export;

	// Set the default e-mail contents
	$contents = '';

	$scheduled_export = ( $export->scheduled_export ? absint( get_transient( WOO_CD_PREFIX . '_scheduled_export_id' ) ) : 0 );
	if( $scheduled_export ) {
		$contents = get_post_meta( $scheduled_export, '_method_email_contents', true );
		// Default e-mail contents
		if( empty( $contents ) )
			$contents = apply_filters( 'woo_ce_default_email_contents', wpautop( __( 'Please find attached your export ready to review.', 'woocommerce-exporter' ) ), $scheduled_export );
	} else {
		// Override the e-mail contents if the single order Transient is set
		$single_export_method_email_contents = get_transient( WOO_CD_PREFIX . '_single_export_method_email_contents' );
		if( $single_export_method_email_contents !== false )
			$contents = $single_export_method_email_contents;
		unset( $single_export_method_email_contents );
		// Default e-mail contents
		if( empty( $contents ) )
			$contents = apply_filters( 'woo_ce_default_email_contents', wpautop( __( 'Please find attached your export ready to review.', 'woocommerce-exporter' ) ) );
	}
	$contents = str_replace( '%store_name%', sanitize_title( get_bloginfo( 'name' ) ), $contents );
	$contents = str_replace( '%export_type%', ucwords( $type ), $contents );
	$contents = str_replace( '%export_filename%', $filename, $contents );
	$contents = apply_filters( 'woo_ce_email_contents', $contents );
	return $contents;

}

function woo_ce_trigger_new_order_export( $order_id = 0 ) {

	global $export;

	if( !empty( $order_id ) ) {
		$export_format = woo_ce_get_option( 'trigger_new_order_format', 'csv' );
		$export_fields = woo_ce_get_option( 'trigger_new_order_fields', 'all' );
		$export_method = woo_ce_get_option( 'trigger_new_order_method', 'archive' );
		$order_items_formatting = apply_filters( 'woo_ce_trigger_new_order_items_formatting', false );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', absint( $order_id ), MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_format', sanitize_text_field( $export_format ), MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_fields', sanitize_text_field( $export_fields ), MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_method', sanitize_text_field( $export_method ), MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_items_formatting', sanitize_text_field( $order_items_formatting ), MINUTE_IN_SECONDS );
		switch( $export_method ) {

			case 'email':
				$export_method_email_to = woo_ce_get_option( 'trigger_new_order_method_email_to', 'archive' );
				$export_method_email_subject = woo_ce_get_option( 'trigger_new_order_method_email_subject', 'archive' );
				set_transient( WOO_CD_PREFIX . '_single_export_method_email_to', sanitize_text_field( $export_method_email_to ), MINUTE_IN_SECONDS );
				set_transient( WOO_CD_PREFIX . '_single_export_method_email_subject', sanitize_text_field( $export_method_email_subject ), MINUTE_IN_SECONDS );
				break;

		}
		$export_type = 'order';
		if( woo_ce_cron_export( $export_method, $export_type ) ) {
			switch( $export_method ) {

				case 'archive':
					woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'New Order #%d export saved to WordPress Media', 'woocommerce-exporter' ), $order_id ) ) );
					break;

				case 'email':
					woo_ce_error_log( sprintf( '%s: Success: %s', $export->filename, sprintf( __( 'New Order #%d export sent via e-mail', 'woocommerce-exporter' ), $order_id ) ) );
					break;

			}
		}
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_method' );
		delete_transient( WOO_CD_PREFIX . '_single_export_method_email_to' );
		delete_transient( WOO_CD_PREFIX . '_single_export_method_email_subjec' );
		delete_transient( WOO_CD_PREFIX . '_single_export_fields' );
		unset( $export_method, $export_type, $export_method_email_to, $export_method_email_subject );
	}

}
?>