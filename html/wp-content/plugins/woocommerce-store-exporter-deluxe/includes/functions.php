<?php
include_once( WOO_CD_PATH . 'includes/product.php' );
include_once( WOO_CD_PATH . 'includes/category.php' );
include_once( WOO_CD_PATH . 'includes/tag.php' );
include_once( WOO_CD_PATH . 'includes/brand.php' );
include_once( WOO_CD_PATH . 'includes/order.php' );
include_once( WOO_CD_PATH . 'includes/customer.php' );
include_once( WOO_CD_PATH . 'includes/user.php' );
include_once( WOO_CD_PATH . 'includes/review.php' );
include_once( WOO_CD_PATH . 'includes/coupon.php' );
include_once( WOO_CD_PATH . 'includes/subscription.php' );
include_once( WOO_CD_PATH . 'includes/product_vendor.php' );
include_once( WOO_CD_PATH . 'includes/commission.php' );
include_once( WOO_CD_PATH . 'includes/shipping_class.php' );
include_once( WOO_CD_PATH . 'includes/ticket.php' );
include_once( WOO_CD_PATH . 'includes/cron.php' );

// Check if we are using PHP 5.3 and above
if( version_compare( phpversion(), '5.3' ) >= 0 )
	include_once( WOO_CD_PATH . 'includes/legacy.php' );
include_once( WOO_CD_PATH . 'includes/formatting.php' );

include_once( WOO_CD_PATH . 'includes/export-csv.php' );
include_once( WOO_CD_PATH . 'includes/export-xml.php' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( WOO_CD_PATH . 'includes/admin.php' );
	include_once( WOO_CD_PATH . 'includes/scheduled_export.php' );
	include_once( WOO_CD_PATH . 'includes/archives.php' );
	include_once( WOO_CD_PATH . 'includes/settings.php' );

	// Displays a HTML notice when a WordPress or Store Exporter error is encountered
	function woo_ce_admin_fail_notices() {

		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

		// If the failed flag is set then prepare for an error notice
		if( isset( $_GET['failed'] ) ) {
			$message = '';
			if( isset( $_GET['message'] ) )
				$message = urldecode( $_GET['message'] );
			if( $message )
				$message = sprintf( __( 'A WordPress or server error caused the exporter to fail, the exporter was provided with a reason: <em>%s</em>', 'woocommerce-exporter' ), $message ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			else
				$message = __( 'A WordPress or server error caused the exporter to fail, no reason was provided, if this persists please get in touch so we can reproduce and resolve this with you.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			woo_cd_admin_notice_html( $message, 'error' );
		}

		// Displays a notice where the maximum execution time cannot be set
		if( !woo_ce_get_option( 'dismiss_execution_time_prompt', 0 ) ) {
			$max_execution_time = absint( ini_get( 'max_execution_time' ) );
			$response = @ini_set( 'max_execution_time', 120 );
			if( $response == false || ( $response != $max_execution_time ) ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_execution_time_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_execution_time_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'We could not override the PHP configuration option <code>max_execution_time</code>, this may limit the size of large exports. See: <a href="%s" target="_blank">Increasing PHP max_execution_time configuration option</a>', 'woocommerce-exporter' ), $troubleshooting_url );
				woo_cd_admin_notice_html( $message );
			}
		}

		// Displays a notice where the memory allocated to WordPress falls below 64MB
		if( !woo_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = absint( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_memory_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_memory_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'We recommend setting memory to at least %dMB, your site has only %dMB allocated to it. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'woocommerce-exporter' ), $minimum_memory_limit, $memory_limit, $troubleshooting_url );
				woo_cd_admin_notice_html( $message, 'error' );
			}
		}

		// Displays a notice where the PHP open_basedir restriction is enabled
		if( !woo_ce_get_option( 'dismiss_open_basedir_prompt', 0 ) ) {
			if( $open_basedir = ini_get( 'open_basedir' ) ) {
				$temp_dir = sys_get_temp_dir();
				$override_temp_dir = apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() );
				// Check if the woo_ce_sys_get_temp_dir WordPress Filter has been used
				$has_filter = false;
				$has_valid = false;
				if( $temp_dir <> $override_temp_dir ) {
					$has_filter = true;
					$temp_dir = $override_temp_dir;
				}
				// Check if the sys_get_temp_dir() is within the open_basedir list
				$open_basedir = explode( ':', $open_basedir );
				if( is_array( $open_basedir ) ) {
					if( !empty( $open_basedir ) ) {
						foreach( $open_basedir as $path ) {
							if( strstr( $temp_dir, $path ) ) {
								$has_valid = true;
								break;
							}
						}
						if( $has_valid ) {
							// Show a notice to confirm that the temporary path exists and is writable
							$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_open_basedir_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_open_basedir_prompt' ) ) ) );
							$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'The PHP open_basedir restriction is enabled for this WordPress site, ensure that you can save tempoary files to <code>%s</code>.<br /><br />If you experience corrupt exports read how to <a href="%s" target="_blank">override the default directory PHP stores temporary files for this Plugin</a>.', 'woocommerce-exporter' ), $temp_dir, $troubleshooting_url . '#General_troubleshooting' );
							woo_cd_admin_notice_html( $message );
						} else {
							// Show a notice explaining what PHP open_basedir is and how to resolve it
							$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_open_basedir_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_open_basedir_prompt' ) ) ) );
							$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'The PHP open_basedir restriction is enabled for this WordPress site but the <code>%s</code> directory is not in the allowed list of directories (<code>%s</code>).<br /><br />If you experience corrupt exports read how to <a href="%s" target="_blank">override the default directory PHP stores temporary files for this Plugin</a> or contact your hosting provider to resolve this.', 'woocommerce-exporter' ), $temp_dir, implode( ', ', $open_basedir ), $troubleshooting_url . '#General_troubleshooting' );
							woo_cd_admin_notice_html( $message, 'error' );
						}
					}
				}
				unset( $open_basedir, $override_temp_dir, $has_filter, $has_valid );
			}
		}

		// Displays a notice if PHP 5.2 or lower is installed
		if( version_compare( phpversion(), '5.3', '<' ) && !woo_ce_get_option( 'dismiss_php_legacy', 0 ) ) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_php_legacy', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_php_legacy' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'Your PHP version (%s) is not supported and is very much out of date, since 2010 all users are strongly encouraged to upgrade to PHP 5.3+ and above. Contact your hosting provider to make this happen. See: <a href="%s" target="_blank">Migrating from PHP 5.2 to 5.3</a>', 'woocommerce-exporter' ), phpversion(), $troubleshooting_url . '#General_troubleshooting' );
			woo_cd_admin_notice_html( $message, 'error' );
		}

		// Displays notice if there are more than 2500 Subscriptions
		if( !woo_ce_get_option( 'dismiss_subscription_prompt', 0 ) ) {
			if( class_exists( 'WC_Subscriptions' ) ) {
				$wcs_version = woo_ce_get_wc_subscriptions_version();
				if( version_compare( $wcs_version, '2.0.1', '<' ) ) {
					if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
						// Does this store have roughly more than 3000 Subscriptions
						if( WC_Subscriptions::is_large_site() ) {
							$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_subscription_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_subscription_prompt' ) ) ) );
							$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'We\'ve detected the <em>is_large_site</em> flag has been set within WooCommerce Subscriptions. Please get in touch if exports are incomplete as we need to spin up an alternative export process to export Subscriptions from large stores.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
							woo_cd_admin_notice_html( $message, 'error' );
						}
					}
				}
			}
		}

		// Displays notice if WooCommerce Checkout Add-Ons is installed
		if( !woo_ce_get_option( 'dismiss_checkout_addons_prompt', 0 ) ) {
			if( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_checkout_addons_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_checkout_addons_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like you have WooCommerce Checkout Add-Ons activated, to export the Checkout Add-on ID, Checkout Add-on Label and Checkout Add-on Value associated with Orders ensure the Fee Order Item Type is selected from Export Options.', 'woocommerce-exporter' );
				woo_cd_admin_notice_html( $message );
			}
		}

		// Display notice if Query Monitor is installed
		if( !woo_ce_get_option( 'dismiss_query_monitor_prompt', 0 ) ) {
			if( class_exists( 'QM_Plugin' ) ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_query_monitor_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_query_monitor_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like you have Query Monitor activated, just a heads up there may be a significant increase in memory usage and time to generate exports as Query Monitor logs all queries.', 'woocommerce-exporter' );
				woo_cd_admin_notice_html( $message );
			}
		}

		// If the export failed the WordPress Transient will still exist
		if( get_transient( WOO_CD_PREFIX . '_running' ) ) {
			$message = __( 'A WordPress or server error caused the exporter to fail with a blank screen, this is usually isolated to a memory or timeout issue, if this persists please get in touch so we can reproduce and resolve this.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			woo_cd_admin_notice_html( $message, 'error' );
			delete_transient( WOO_CD_PREFIX . '_running' );
		}

		// If the woo_cd_exported WordPress Option exists then an Order export failed and we should roll back changes
		if( woo_ce_get_option( 'exported', false ) ) {
			$orders = woo_ce_get_option( 'exported', false );
			if( !empty( $orders ) ) {
				foreach( $orders as $order_id ) {
					// Remove the export flag
					delete_post_meta( $order_id, '_woo_cd_exported' );
					if( woo_ce_get_option( 'order_flag_notes', 0 ) ) {
						// Add an additional Order Note
						$order = woo_ce_get_order_wc_data( $order_id );
						$note = __( 'Order export flag was cleared.', 'woocommerce-exporter' );
						$order->add_order_note( $note );
						unset( $order );
					}
				}
			}
			unset( $orders, $order_id );
			delete_option( WOO_CD_PREFIX . '_exported' );
			$message = __( 'It looks like a previous Orders export failed before it could complete, we have removed the exported flag assigned to those Orders so they are not excluded from your next export using <em>Filter Orders by Order Date</em> > <em>Since last export</em>.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			woo_cd_admin_notice_html( $message );
		}

		// Displays a HTML notice where we have detected the site has moved or this is staging site
		if(
			woo_ce_get_option( 'duplicate_site_prompt', 0 )
			&& ( !woo_ce_get_option( 'override_duplicate_site_prompt', 0 ) )
			&& ( !woo_ce_get_option( 'dismiss_duplicate_site_prompt', 0 ) )
		) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_duplicate_site_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_duplicate_site_prompt' ) ) ) );
			$override_url = esc_url( add_query_arg( array( 'action' => 'override_duplicate_site_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_override_duplicate_site_prompt' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like this site has moved or is a duplicate site. Store Exporter Deluxe has disabled scheduled exports on this site to prevent duplicate scheduled exports being generated from a staging or test environment. If this is in error click <em>Continue running scheduled exports</em> to re-enable scheduled exports.', 'woocommerce-exporter' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Continue running scheduled exports', 'woocommerce-exporter' ) . '</a>';
			woo_cd_admin_notice_html( $message, 'error' );
		}

		// Displays a HTML notice if Archives is disabled and the Archives tab is opened
		if(
			woo_ce_get_option( 'delete_file', '1' ) == 1
			&& ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '' ) == 'archive'
			&& ( !woo_ce_get_option( 'dismiss_archives_prompt', 0 ) )
		) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_archives_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_archives_prompt' ) ) ) );
			$override_url = esc_url( add_query_arg( array( 'action' => 'hide_archives_tab', '_wpnonce' => wp_create_nonce( 'woo_ce_hide_archives_tab' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like the saving of export archives is disabled from the Enabled Archives option on the Settings tab, would you like to hide the Archives tab aswell?', 'woocommerce-exporter' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Hide Archives tab', 'woocommerce-exporter' ) . '</a>';
			woo_cd_admin_notice_html( $message );
		}

		// Displays a HTML notice if Scheduled Exports are disabled and the Scheduled Exports tab is opened
		if(
			woo_ce_get_option( 'enable_auto', '0' ) == 0
			&& ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '' ) == 'scheduled_export'
			&& ( !woo_ce_get_option( 'dismiss_scheduled_exports_prompt', 0 ) )
		) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_scheduled_exports_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_scheduled_exports_prompt' ) ) ) );
			$override_url = esc_url( add_query_arg( array( 'action' => 'hide_scheduled_exports_tab', '_wpnonce' => wp_create_nonce( 'woo_ce_hide_scheduled_exports_tab' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like scheduled exports are disabled from the <em>Enable scheduled exports</em> option on the Settings tab, would you like to hide the Scheduled Exports tab aswell?', 'woocommerce-exporter' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Hide Scheduled Exports tab', 'woocommerce-exporter' ) . '</a>';
			woo_cd_admin_notice_html( $message );
		}

		// Displays a HTML notice if Archives are detected without a Post Status of private
		if( woo_ce_get_unprotected_archives( array( 'count' => true ) ) && !woo_ce_get_option( 'dismiss_archives_privacy_prompt', 0 ) ) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_archives_privacy_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_archives_privacy_prompt' ) ) ) );
			$override_url = esc_url( add_query_arg( array( 'action' => 'override_archives_privacy', '_wpnonce' => wp_create_nonce( 'woo_ce_override_archives_privacy' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like some archived exports require updating, would you like to hide these archived exports now?', 'woocommerce-exporter' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Update export archives', 'woocommerce-exporter' ) . '</a>';
			woo_cd_admin_notice_html( $message );
		}

/*
		// @mod - Will work on in the 2.1+ series, requires a mountain of testing before release
		// If the sed-exports folder within Uploads does not exist
		$upload_dir =  wp_upload_dir();
		if( !file_exists( $upload_dir['basedir'] . '/sed-exports/.htaccess' ) && woo_ce_get_option( 'dismiss_secure_archives_prompt', false ) == false ) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_secure_archives_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_secure_archives_prompt' ) ) ) );
			$action_url = esc_url( add_query_arg( array( 'action' => 'relocate_archived_exports', '_wpnonce' => wp_create_nonce( 'woo_ce_relocate_archived_exports' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'It looks like your exports are out in the open, let\'s move them to a secure location within the WordPres Uploads directory.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)' . '<br /><br /><a href="' . $action_url . '" class="button-primary">' . __( 'Re-locate archived exports', 'woocommerce-exporter' ) . '</a>';
			woo_cd_admin_notice_html( $message, 'error' );
		}
*/

	}

	// Saves the state of Export fields for next export
	function woo_ce_save_fields( $export_type = '', $fields = array(), $sorting = array() ) {

		// Default fields
		if( $fields == false && !is_array( $fields ) )
			$fields = array();
		$export_types = array_keys( woo_ce_get_export_types() );
		if( in_array( $export_type, $export_types ) && !empty( $fields ) ) {
			woo_ce_update_option( $export_type . '_fields', array_map( 'sanitize_text_field', $fields ) );
			woo_ce_update_option( $export_type . '_sorting', array_map( 'absint', $sorting ) );
		}

	}

	// Returns number of an Export type prior to export, used on Store Exporter screen
	function woo_ce_get_export_type_count( $export_type = '', $args = array() ) {

		global $wpdb;

		$count_sql = null;
		$woocommerce_version = woo_get_woo_version();

		switch( $export_type ) {

			case 'product':
				$count = woo_ce_get_export_type_product_count();
				break;

			case 'category':
				$count = woo_ce_get_export_type_category_count();
				break;

			case 'tag':
				$count = woo_ce_get_export_type_tag_count();
				break;

			case 'order':
				$count = woo_ce_get_export_type_order_count();
				break;

			case 'customer':
				$count = woo_ce_get_export_type_customer_count();
				break;

			case 'user':
				$count = woo_ce_get_export_type_user_count();
				break;

			case 'review':
				$count = woo_ce_get_export_type_review_count();
				break;

			case 'coupon':
				$count = woo_ce_get_export_type_coupon_count();
				break;

			case 'shipping_class':
				$count = woo_ce_get_export_type_shipping_class_count();
				break;

			case 'ticket':
				$count = woo_ce_get_export_type_ticket_count();
				break;

			case 'attribute':
				$count = woo_ce_get_export_type_attribute_count();
				break;

			// Allow Plugin/Theme authors to populate their own custom export type counts
			default:
				$count = 0;
				$count = apply_filters( 'woo_ce_get_export_type_count', $count, $export_type, $args );
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				if( is_object( $count ) ) {
					$count = (array)$count;
					$count = absint( array_sum( $count ) );
				}
				return $count;
			} else {
				if( $count_sql )
					$count = $wpdb->get_var( $count_sql );
				else
					$count = 0;
			}
			return $count;
		} else {
			return 0;
		}

	}

	// In-line display of export file and export details when viewed via WordPress Media screen
	function woo_ce_read_export_file( $post = false ) {

		if( empty( $post ) ) {
			if( isset( $_GET['post'] ) )
				$post = get_post( $_GET['post'] );
		}

		if( $post->post_type != 'attachment' )
			return;

		// Check if the Post matches one of our Post Mime Types
		if( !in_array( $post->post_mime_type, array_values( woo_ce_get_mime_types() ) ) )
			return;

		$filepath = get_attached_file( $post->ID );

		// We can only read CSV, TSV and XML file types, the others are encoded
		if( in_array( $post->post_mime_type, array( 'text/csv', 'text/tab-separated-values', 'application/xml', 'application/rss+xml' ) ) ) {

			$contents = __( 'No export entries were found, please try again with different export filters.', 'woocommerce-exporter' );
			if( file_exists( $filepath ) ) {
				$contents = file_get_contents( $filepath );
			} else {
				// This resets the _wp_attached_file Post meta key to the correct value
				update_attached_file( $post->ID, $post->guid );
				// Try grabbing the file contents again
				$filepath = get_attached_file( $post->ID );
				if( file_exists( $filepath ) ) {
					$handle = fopen( $filepath, "r" );
					$contents = stream_get_contents( $handle );
					fclose( $handle );
				}
			}
			if( !empty( $contents ) )
				include_once( WOO_CD_PATH . 'templates/admin/media-csv_file.php' );

		}

		// We can still show the Export Details for any supported Post Mime Type
		$export_type = get_post_meta( $post->ID, '_woo_export_type', true );
		$columns = get_post_meta( $post->ID, '_woo_columns', true );
		$rows = get_post_meta( $post->ID, '_woo_rows', true );
		$scheduled_id = get_post_meta( $post->ID, '_scheduled_id', true );
		$start_time = get_post_meta( $post->ID, '_woo_start_time', true );
		$end_time = get_post_meta( $post->ID, '_woo_end_time', true );
		$idle_memory_start = get_post_meta( $post->ID, '_woo_idle_memory_start', true );
		$data_memory_start = get_post_meta( $post->ID, '_woo_data_memory_start', true );
		$data_memory_end = get_post_meta( $post->ID, '_woo_data_memory_end', true );
		$idle_memory_end = get_post_meta( $post->ID, '_woo_idle_memory_end', true );

		include_once( WOO_CD_PATH . 'templates/admin/media-export_details.php' );

	}
	add_action( 'edit_form_after_editor', 'woo_ce_read_export_file' );

	// Returns label of Export type slug used on Store Exporter screen
	function woo_ce_export_type_label( $export_type = '', $echo = false ) {

		$output = '';
		if( !empty( $export_type ) ) {
			$export_types = woo_ce_get_export_types();
			if( array_key_exists( $export_type, $export_types ) )
				$output = $export_types[$export_type];
		}
		if( $echo )
			echo $output;
		else
			return $output;

	}

	// Returns a list of archived exports
	function woo_ce_get_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array_values( woo_ce_get_mime_types() ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1
		);
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( !empty( $filter ) )
				$args['meta_value'] = $filter;
		}
		$files = get_posts( $args );
		return $files;

	}

	function woo_ce_nuke_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array_values( woo_ce_get_mime_types() ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$post_query = new WP_Query( $args );
		if( !empty( $post_query->found_posts ) ) {
			foreach( $post_query->posts as $post )
				wp_delete_attachment( $post, true );
			return true;
		}

	}

	// Reset WP-CRON
	function woo_ce_nuke_cron() {

		if( update_option( 'cron', '' ) )
			return true;

	}

	// Delete all Scheduled Exports
	function woo_ce_nuke_scheduled_exports() {

		$scheduled_exports = woo_ce_get_scheduled_exports();
		if( !empty( $scheduled_exports ) ) {
			foreach( $scheduled_exports as $post_ID )
				wp_delete_post( $post_ID, true );
			return true;
		}

	}

	// Delete all WordPress Options generated by Store Exporter
	function woo_ce_nuke_options() {

		global $wpdb;

		$prefix = 'woo_ce_%';

		// Get a list of WordPress Options prefixed by woo_ce_
		$options_sql = $wpdb->prepare( "SELECT `option_name` FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE %s", $prefix );
		$options = $wpdb->get_col( $options_sql );
		if( !empty( $options ) ) {
			$count = 0;
			// Get a count of WordPress Options to be deleted
			$size = count( $options );
			foreach( $options as $option ) {
				// Get a count of deleted WordPress Options
				if( delete_option( $option ) )
					$count++;
			}
			// Compare the count of WordPress Options vs deleted WordPress Options
			if( $count == $size )
				return true;
		}

	}

	// Reset all dismissed notices within Store Exporter
	function woo_ce_nuke_dismissed_notices() {

		global $wpdb;

		$prefix = 'woo_ce_dismiss_%';

		// Get a list of WordPress Options prefixed by woo_ce_dismiss_
		$options_sql = $wpdb->prepare( "SELECT `option_name` FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE %s", $prefix );
		$options = $wpdb->get_col( $options_sql );
		if( !empty( $options ) ) {
			foreach( $options as $option )
				delete_option( $option );
		}

	}

	// Returns a list of Attachments which are exposed to the public
	function woo_ce_get_unprotected_archives( $postarr = array() ) {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array_values( woo_ce_get_mime_types() ),
			'meta_key' => $meta_key,
			'post_status' => 'inherit',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$args = wp_parse_args( $postarr, $args );
		$post_query = new WP_Query( $args );
		if( !empty( $post_query->found_posts ) ) {
			// Check if we are returning a count or list
			if( isset( $postarr['count'] ) ) {
				return $post_query->found_posts;
			}
			return $post_query->posts;
		}

	}

	function woo_ce_update_archives_privacy() {

		$attachments = woo_ce_get_unprotected_archives();
		if( !empty( $attachments ) ) {
			foreach( $attachments as $post_ID ) {
				$args = array(
					'ID' => $post_ID,
					'post_status' => 'private'
				);
				wp_update_post( $args );
			}
			return true;
		}

	}

	// Returns an archived export with additional details
	function woo_ce_get_archive_file( $file = '' ) {

		$upload_dir = wp_upload_dir();
		$file->export_type = get_post_meta( $file->ID, '_woo_export_type', true );
		$file->export_type_label = woo_ce_export_type_label( $file->export_type );
		if( empty( $file->export_type ) )
			$file->export_type = __( 'Unassigned', 'woocommerce-exporter' );
		if( empty( $file->guid ) )
			$file->guid = $upload_dir['url'] . '/' . basename( $file->post_title );
		$file->post_mime_type = get_post_mime_type( $file->ID );
		if( !$file->post_mime_type )
			$file->post_mime_type = __( 'N/A', 'woocommerce-exporter' );
		$file->media_icon = wp_get_attachment_image( $file->ID, array( 80, 60 ), true );
		if( $author_name = get_user_by( 'id', $file->post_author ) )
			$file->post_author_name = $author_name->display_name;
		$file->post_date = woo_ce_format_archive_date( $file->ID );
		unset( $author_name, $t_time, $time );
		return $file;

	}

	// HTML template for displaying the current export type filter on the Archives screen
	function woo_ce_archives_quicklink_current( $current = '' ) {

		$output = '';
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( $filter == $current )
				$output = ' class="current"';
		} else if( $current == 'all' ) {
			$output = ' class="current"';
		}
		echo $output;

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_archives_quicklink_count( $type = '' ) {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'post_status' => 'any',
			'fields' => 'ids'
		);
		if( !empty( $type ) )
			$args['meta_value'] = $type;
		$post_query = new WP_Query( $args );
		return absint( $post_query->found_posts );

	}

	/* End of: WordPress Administration */

}

// Export process for CSV file
function woo_ce_export_dataset( $export_type = null, &$output = null ) {

	global $export;

	$separator = $export->delimiter;
	$line_ending = woo_ce_get_line_ending();
	$export->columns = array();
	$export->total_rows = 0;
	$export->total_columns = 0;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	if( ( !$export->cron && !$export->scheduled_export ) )
		set_transient( WOO_CD_PREFIX . '_running', time(), woo_ce_get_option( 'timeout', HOUR_IN_SECONDS ) );

	// Load up the fatal error notice if we 500 Internal Server Error (memory), hit a server timeout or encounter a fatal PHP error
	add_action( 'shutdown', 'woo_ce_fatal_error' );

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );
	add_filter( 'attribute_escape', 'woo_ce_attribute_escape', 10, 2 );

	switch( $export_type ) {

		// Products
		case 'product':
			$fields = woo_ce_get_product_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_product_field( $key );
			}
			if( $export->gallery_unique ) {
				$export->fields = woo_ce_unique_product_gallery_fields( $export->fields );
				$export->columns = woo_ce_unique_product_gallery_columns( $export->columns, $export->fields );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $products = woo_ce_get_products( $export->args ) ) {
				$export->total_rows = count( $products );
				// XML export
				if( $export->export_format == 'xml' ) {
					if( !empty( $export->fields ) ) {
						foreach( $products as $product ) {
							$child = $output->addChild( apply_filters( 'woo_ce_export_xml_product_node', sanitize_key( $export_type ) ) );
							$product = woo_ce_get_product_data( $product, $export->args, array_keys( $export->fields ) );
							$child->addAttribute( 'id', ( isset( $product->product_id ) ? $product->product_id : '' ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $product->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $product->$field, $export_type, $field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( woo_ce_sanitize_xml_string( $product->$field ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $product->$field ) ) );
									}
								}
							}
						}
					}
				} else if( $export->export_format == 'rss' ) {
					// RSS export
					if( !empty( $export->fields ) ) {
						foreach( $products as $product ) {
							$child = $output->addChild( 'item' );
							$product = woo_ce_get_product_data( $product, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $field ) {
								if( isset( $product->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $product->$field ) )
											$child->addChild( sanitize_key( $field ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $product->$field ) ) );
										else
											$child->addChild( sanitize_key( $field ), esc_html( woo_ce_sanitize_xml_string( $product->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $products as $key => $product ) {
						$products[$key] = woo_ce_get_product_data( $product, $export->args, array_keys( $export->fields ) );
					}
					$output = $products;
				}
				unset( $products, $product );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Categories
		case 'category':
			$fields = woo_ce_get_category_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_category_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$category_args = array(
				'orderby' => ( isset( $export->args['category_orderby'] ) ? $export->args['category_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['category_order'] ) ? $export->args['category_order'] : 'ASC' ),
			);
			if( $categories = woo_ce_get_product_categories( $category_args ) ) {
				$export->total_rows = count( $categories );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $categories as $category ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_category_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $category->term_id ) ? $category->term_id : '' ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $category->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $category->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $category->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $category->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $categories;
				}
				unset( $categories, $category );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Tags
		case 'tag':
			$fields = woo_ce_get_tag_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_tag_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$tag_args = array(
				'orderby' => ( isset( $export->args['tag_orderby'] ) ? $export->args['tag_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['tag_order'] ) ? $export->args['tag_order'] : 'ASC' ),
			);
			if( $tags = woo_ce_get_product_tags( $tag_args ) ) {
				$export->total_rows = count( $tags );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $tags as $tag ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_tag_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $tag->term_id ) ? $tag->term_id : '' ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $tag->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $tag->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $tag->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $tag->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $tags;
				}
				unset( $tags, $tag );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Brands
		case 'brand':
			$fields = woo_ce_get_brand_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_brand_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$brand_args = array(
				'orderby' => ( isset( $export->args['brand_orderby'] ) ? $export->args['brand_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['brand_order'] ) ? $export->args['brand_order'] : 'ASC' ),
			);
			if( $brands = woo_ce_get_product_brands( $brand_args ) ) {
				$export->total_rows = count( $brands );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $brands as $brand ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_brand_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $brand->term_id ) ? $brand->term_id : '' ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $brand->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $brand->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $brand->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $brand->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $brands;
				}
				unset( $brands, $brand );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Orders
		case 'order':
			$fields = woo_ce_get_order_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				// Do not apply Field Editor changes to the unique Order Items Formatting rule
				if( $export->args['order_items'] == 'unique' )
					remove_filter( 'woo_ce_order_fields', 'woo_ce_override_order_field_labels', 11 );
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_order_field( $key );
				// Do not apply Field Editor changes to the unique Order Items Formatting rule
				if( $export->args['order_items'] == 'unique' )
					add_filter( 'woo_ce_order_fields', 'woo_ce_override_order_field_labels', 11 );
			}
			if( $export->args['order_items'] == 'unique' ) {
				$export->fields = woo_ce_unique_order_item_fields( $export->fields );
				$export->columns = woo_ce_unique_order_item_columns( $export->columns, $export->fields );
			}
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $orders = woo_ce_get_orders( 'order', $export->args ) ) {
				$export->total_columns = $size = count( $export->columns );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $orders as $order ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_order_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', $order );
							$order = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
							if( in_array( $export->args['order_items'], array( 'combined', 'unique' ) ) ) {
								// Order items formatting: SPECK-IPHONE|INCASE-NANO|-
								foreach( array_keys( $export->fields ) as $key => $field ) {
									if( isset( $order->$field ) && isset( $export->columns[$key] ) ) {
										if( !is_array( $field ) ) {
											if( woo_ce_is_xml_cdata( $order->$field ) )
												$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
											else
												$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
										}
									}
								}
							} else if( $export->args['order_items'] == 'individual' ) {
								// Order items formatting: SPECK-IPHONE<br />INCASE-NANO<br />-
								if( !empty( $order->order_items ) ) {
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
									foreach( $order->order_items as $order_item ) {
										// Add Order Item weight to Shipping Weight
										if( $order_item->total_weight != '' )
											$order->shipping_weight += $order_item->total_weight;
										$order->order_items_id = $order_item->id;
										$order->order_items_product_id = $order_item->product_id;
										$order->order_items_variation_id = $order_item->variation_id;
										if( empty( $order_item->sku ) )
											$order_item->sku = '';
										$order->order_items_sku = $order_item->sku;
										$order->order_items_name = $order_item->name;
										$order->order_items_variation = $order_item->variation;
										$order->order_items_description = woo_ce_format_description_excerpt( $order_item->description );
										$order->order_items_excerpt = woo_ce_format_description_excerpt( $order_item->excerpt );
										$order->order_items_tax_class = $order_item->tax_class;
										$order->total_quantity += $order_item->quantity;
										$order->order_items_quantity = $order_item->quantity;
										$order->order_items_total = $order_item->total;
										$order->order_items_subtotal = $order_item->subtotal;
										$order->order_items_rrp = $order_item->rrp;
										$order->order_items_stock = $order_item->stock;
										$order->order_items_tax = $order_item->tax;
										$order->order_items_tax_subtotal = $order_item->tax_subtotal;
										$order->order_items_refund_subtotal = $order_item->refund_subtotal;
										$order->order_items_refund_quantity = $order_item->refund_quantity;
										$order->order_items_type = $order_item->type;
										$order->order_items_type_id = $order_item->type_id;
										$order->order_items_category = $order_item->category;
										$order->order_items_tag = $order_item->tag;
										$order->order_items_total_sales = $order_item->total_sales;
										$order->order_items_weight = $order_item->weight;
										$order->order_items_height = $order_item->height;
										$order->order_items_width = $order_item->width;
										$order->order_items_length = $order_item->length;
										$order->order_items_total_weight = $order_item->total_weight;
										$order = apply_filters( 'woo_ce_order_items_individual', $order, $order_item );
										foreach( array_keys( $export->fields ) as $key => $field ) {
											if( isset( $order->$field ) && isset( $export->columns[$key] ) ) {
												if( !is_array( $field ) ) {
													if( woo_ce_is_xml_cdata( $order->$field ) )
														$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
													else
														$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
												}
											}
										}
									}
									unset( $order->order_items );
								}
							}
						}
					}
				} else {
					// PHPExcel export
					if( $export->args['order_items'] == 'individual' )
						$output = array();
					foreach( $orders as $order ) {
						if( in_array( $export->args['order_items'], array( 'combined', 'unique' ) ) ) {
							// Order items formatting: SPECK-IPHONE|INCASE-NANO|-
							$output[] = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
						} else if( $export->args['order_items'] == 'individual' ) {
							// Order items formatting: SPECK-IPHONE<br />INCASE-NANO<br />-
							$order = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
							if( !empty( $order->order_items ) ) {
								foreach( $order->order_items as $order_item ) {
									// Add Order Item weight to Shipping Weight
									if( $order_item->total_weight != '' )
										$order->shipping_weight += $order_item->total_weight;
									$order->order_items_id = $order_item->id;
									$order->order_items_product_id = $order_item->product_id;
									$order->order_items_variation_id = $order_item->variation_id;
									if( empty( $order_item->sku ) )
										$order_item->sku = '';
									$order->order_items_sku = $order_item->sku;
									$order->order_items_name = $order_item->name;
									$order->order_items_variation = $order_item->variation;
									$order->order_items_description = $order_item->description;
									$order->order_items_excerpt = $order_item->excerpt;
									$order->order_items_tax_class = $order_item->tax_class;
									$order->total_quantity += $order_item->quantity;
									$order->order_items_quantity = $order_item->quantity;
									$order->order_items_total = $order_item->total;
									$order->order_items_subtotal = $order_item->subtotal;
									$order->order_items_rrp = $order_item->rrp;
									$order->order_items_stock = $order_item->stock;
									$order->order_items_tax = $order_item->tax;
									$order->order_items_tax_subtotal = $order_item->tax_subtotal;
									$order->order_items_refund_subtotal = $order_item->refund_subtotal;
									$order->order_items_refund_quantity = $order_item->refund_quantity;
									$order->order_items_type = $order_item->type;
									$order->order_items_type_id = $order_item->type_id;
									$order->order_items_category = $order_item->category;
									$order->order_items_tag = $order_item->tag;
									$order->order_items_total_sales = $order_item->total_sales;
									$order->order_items_weight = $order_item->weight;
									$order->order_items_width = $order_item->width;
									$order->order_items_length = $order_item->length;
									$order->order_items_height = $order_item->height;
									$order->order_items_total_weight = $order_item->total_weight;
									$order = apply_filters( 'woo_ce_order_items_individual', $order, $order_item );
									// This fixes the Order Items for this Order Items Formatting rule
									$output[] = (object)(array)$order;
								}
							}
						}
					}
				}
				unset( $orders, $order );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Customers
		case 'customer':
			$fields = woo_ce_get_customer_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_customer_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $customers = woo_ce_get_orders( 'customer', $export->args ) ) {
				$export->total_rows = count( $customers );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $customers as $customer ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_customer_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $customer->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $customer->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $customer->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $customer->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $customers;
				}
				unset( $customers, $customer );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Users
		case 'user':
			$fields = woo_ce_get_user_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_user_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $users = woo_ce_get_users( $export->args ) ) {
				$export->total_rows = count( $users );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $users as $user ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_user_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $user->user_id ) ? $user->user_id : '' ) );
							$user = woo_ce_get_user_data( $user, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $user->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $user->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $user->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $user->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $users as $key => $user )
						$users[$key] = woo_ce_get_user_data( $user, $export->args, array_keys( $export->fields ) );
					$output = $users;
				}
				unset( $users, $user );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Reviews
		case 'review':
			$fields = woo_ce_get_review_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_review_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $reviews = woo_ce_get_reviews( $export->args ) ) {
				$export->total_rows = count( $reviews );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $reviews as $review ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_review_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $review->comment_id ) ? $review->comment_id : '' ) );
							$review = woo_ce_get_review_data( $review, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $review->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $review->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $review->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $review->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $reviews as $key => $review )
						$reviews[$key] = woo_ce_get_review_data( $review, $export->args, array_keys( $export->fields ) );
					$output = $reviews;
				}
				unset( $reviews, $review );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Coupons
		case 'coupon':
			$fields = woo_ce_get_coupon_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_coupon_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $coupons = woo_ce_get_coupons( $export->args ) ) {
				$export->total_rows = count( $coupons );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $coupons as $coupon ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_coupon_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $coupon ) ? $coupon : '' ) );
							$coupon = woo_ce_get_coupon_data( $coupon, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $coupon->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $coupon->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $coupon->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $coupon->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $coupons as $key => $coupon ) {
						$coupons[$key] = woo_ce_get_coupon_data( $coupon, $export->args, array_keys( $export->fields ) );
					}
					$output = $coupons;
				}
				unset( $coupons, $coupon );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Subscriptions
		case 'subscription':
			$fields = woo_ce_get_subscription_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_subscription_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $subscriptions = woo_ce_get_subscriptions( $export->args ) ) {
				$export->total_rows = count( $subscriptions );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $subscriptions as $subscription ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_subscription_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$subscription = woo_ce_get_subscription_data( $subscription, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $subscription->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $subscription->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $subscription->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $subscription->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $subscriptions as $key => $subscription ) {
						$subscriptions[$key] = woo_ce_get_subscription_data( $subscription, $export->args, array_keys( $export->fields ) );
					}
					$output = $subscriptions;
				}
				unset( $subscriptions, $subscription );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Product Vendors
		case 'product_vendor':
			$fields = woo_ce_get_product_vendor_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_product_vendor_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $product_vendors = woo_ce_get_product_vendors( $export->args ) ) {
				$export->total_rows = count( $product_vendors );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $product_vendors as $product_vendor ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_product_vendor_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $product_vendor ) ? $product_vendor : '' ) );
							$product_vendor = woo_ce_get_product_vendor_data( $product_vendor, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $product_vendor->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $product_vendor->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $product_vendor->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $product_vendor->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $product_vendors as $key => $product_vendor ) {
						$product_vendors[$key] = woo_ce_get_product_vendor_data( $product_vendor, $export->args, array_keys( $export->fields ) );
					}
					$output = $product_vendors;
				}
				unset( $product_vendors, $product_vendor );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Commissions
		case 'commission':
			$fields = woo_ce_get_commission_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_commission_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $commissions = woo_ce_get_commissions( $export->args ) ) {
				$export->total_rows = count( $commissions );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $commissions as $commission ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_commission_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $commission ) ? $commission : '' ) );
							$commission = woo_ce_get_commission_data( $commission, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $commission->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $commission->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $commission->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $commission->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $commissions as $key => $commission ) {
						$commissions[$key] = woo_ce_get_commission_data( $commission, $export->args, array_keys( $export->fields ) );
					}
					$output = $commissions;
				}
				unset( $commissions, $commission );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Shipping Classes
		case 'shipping_class':
			$fields = woo_ce_get_shipping_class_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_shipping_class_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $shipping_classes = woo_ce_get_shipping_classes( $export->args ) ) {
				$export->total_rows = count( $shipping_classes );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $shipping_classes as $shipping_class ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_shipping_class_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $shipping_class->term_id ) ? $shipping_class->term_id : '' ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $shipping_class->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $shipping_class->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $shipping_class->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $shipping_class->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $shipping_classes;
				}
				unset( $shipping_classes, $shipping_class );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Tickets
		case 'ticket':
			$fields = woo_ce_get_ticket_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_ticket_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $tickets = woo_ce_get_tickets( $export->args ) ) {
				$export->total_rows = count( $tickets );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $tickets as $ticket ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_ticket_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', ( isset( $ticket->comment_id ) ? $ticket->comment_id : '' ) );
							$ticket = woo_ce_get_ticket_data( $ticket, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $ticket->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $ticket->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $ticket->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $ticket->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $tickets as $key => $ticket )
						$tickets[$key] = woo_ce_get_ticket_data( $ticket, $export->args, array_keys( $export->fields ) );
					$output = $tickets;
				}
				unset( $tickets, $ticket );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

/*
		// Attributes
		case 'attribute':
			$fields = woo_ce_get_attribute_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_attribute_field( $key );
			}
			$export->total_columns = $size = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $attributes = woo_ce_get_attributes( $export->args ) ) {
				$export->total_rows = count( $attributes );
				// Generate the export headers
				if( $export->header_formatting && in_array( $export->export_format, array( 'csv', 'xls' ) ) ) {
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $line_ending;
						else
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
					}
				}
				if( !empty( $export->fields ) ) {
					foreach( $atributes as $attribute ) {

						if( $export->export_format == 'xml' )
							$child = $output->addChild( $export->type, 0, -1 );

					}
				}
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			unset( $export->fields );
			break;
*/

	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );
	remove_filter( 'attribute_escape', 'woo_ce_attribute_escape' );

	// Remove our fatal error notice so not to conflict with the CRON or scheduled export engine	
	remove_action( 'shutdown', 'woo_ce_fatal_error' );

	// Export completed successfully
	if( ( !$export->cron && !$export->scheduled_export ) )
		delete_transient( WOO_CD_PREFIX . '_running' );

	// Check if we're using PHPExcel or generic export engine
	if( WOO_CD_DEBUG || in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {

		// Check that the export file is populated, export columns have been assigned and rows counted
		if( !empty( $output ) && $export->total_rows && $export->total_columns ) {
			if( WOO_CD_DEBUG && !in_array( $export->export_format, array( 'csv', 'tsv', 'xls', 'xlsx' ) ) && ( !$export->cron && !$export->scheduled_export ) ) {
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) )
					$output = woo_ce_format_xml( $output );
				$response = set_transient( WOO_CD_PREFIX . '_debug_log', base64_encode( $output ), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
				if( $response !== true ) {
					$message = __( 'The export contents were too large to store in a single WordPress transient, use the Volume offset / Limit volume options to reduce the size of your export and try again.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
					if( function_exists( 'woo_cd_admin_notice' ) )
						woo_cd_admin_notice( $message, 'error' );
					else
						woo_ce_error_log( sprintf( 'woo_ce_export_dataset() - %s', $message ) );
					return;
				} else {
					return true;
				}
			} else {
				return $output;
			}
		}

	} else {
		return $output;
	}

}

function woo_ce_fatal_error() {

	global $export;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	$error = error_get_last();
	if( $error !== null ) {
		$message = '';
		$notice = sprintf( __( 'Refer to the following error and if you continue to have problems see our <a href="%s" target="_blank">Usage</a> document or contact us on <a href="http://www.visser.com.au/premium-support/" target="_blank">Support</a> for further assistance.<br /><br /><code>%s in %s on line %d</code>', 'woocommerce-exporter' ), $troubleshooting_url, $error['message'], $error['file'], $error['line'] );
		if ( substr( $error['message'], 0, 22 ) === 'Maximum execution time' ) {
			$message = __( 'The server\'s maximum execution time is too low to complete this export, use our batch export function - Limit Volume and Volume Offset under Export Options - to create smaller exports. This is commonly due to a low timeout limit set by your hosting provider or PHP Safe Mode being enabled. Consider increasing the timeout limit or reducing the size of your export.', 'woocommerce-exporter' );
		} elseif ( substr( $error['message'], 0, 19 ) === 'Allowed memory size' ) {
			$message = __( 'The server\'s maximum memory size is too low to complete this export, use our batch export function - Limit Volume and Volume Offset under Export Options - to create smaller exports. Consider increasing available memory to WordPress or reducing the size of your export.', 'woocommerce-exporter' );
		} else if( $error['type'] === E_ERROR ) {
			// Test if it's WP All Import conflicting with the PHPExcel library
			if( substr( $error['message'], 0, 33 ) == "Class 'PHPExcel_Writer_Excel2007'" && ( strstr( $error['file'], 'wp-all-import' ) !== false ) ) {
				$message = __( 'A fatal PHP error was encountered during the export process, this was due to the Plugin WP All Import pre-loading the PHPExcel library. Contact the Plugin author of WP All Import - Soflyy - for more information.', 'woocommerce-exporter' );
			} else {
				$message = __( 'A fatal PHP error was encountered during the export process, we couldn\'t detect or diagnose it further.', 'woocommerce-exporter' );
			}
		}
		if( !empty( $message ) ) {

			// Save a record to the PHP error log
			woo_ce_error_log( sprintf( __( 'Fatal error: %s - PHP response: %s in %s on line %s', 'woocommerce-exporter' ), $message, $error['message'], $error['file'], $error['line'] ) );
			error_log( sprintf( __( 'Fatal error: %s - PHP response: %s in %s on line %s', 'woocommerce-exporter' ), $message, $error['message'], $error['file'], $error['line'] ) );

			// Only display the message if this is a manual export
			if( ( !$export->cron && !$export->scheduled_export ) ) {
				$output = '<div id="message" class="error"><p>' . sprintf( __( '<strong>[store-exporter-deluxe]</strong> An unexpected error occurred. %s', 'woocommerce-exporter' ), $message . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)' ) . '</p><p>' . $notice . '</p></div>';
				echo $output;
			}

		}
	}

}

// List of Export types used on Store Exporter screen
function woo_ce_get_export_types() {

	$export_types = array(
		'product' => __( 'Products', 'woocommerce-exporter' ),
		'category' => __( 'Categories', 'woocommerce-exporter' ),
		'tag' => __( 'Tags', 'woocommerce-exporter' ),
		'brand' => __( 'Brands', 'woocommerce-exporter' ),
		'order' => __( 'Orders', 'woocommerce-exporter' ),
		'customer' => __( 'Customers', 'woocommerce-exporter' ),
		'user' => __( 'Users', 'woocommerce-exporter' ),
		'review' => __( 'Reviews', 'woocommerce-exporter' ),
		'coupon' => __( 'Coupons', 'woocommerce-exporter' ),
		'subscription' => __( 'Subscriptions', 'woocommerce-exporter' ),
		'product_vendor' => __( 'Product Vendors', 'woocommerce-exporter' ),
		'commission' => __( 'Commission', 'woocommerce-exporter' ),
		'shipping_class' => __( 'Shipping Classes', 'woocommerce-exporter' ),
		'ticket' => __( 'Tickets', 'woocommerce-exporter' )
		// 'attribute' => __( 'Attributes', 'woocommerce-exporter' )
	);
	$export_types = apply_filters( 'woo_ce_export_types', $export_types );
	return $export_types;

}

function woo_ce_get_export_type_label( $export_type = '' ) {

	$output = $export_type;
	if( !empty( $export_type ) ) {
		$export_types = woo_ce_get_export_types();
		// Check our export type exists
		$output = ( isset( $export_types[$export_type] ) ? $export_types[$export_type] : $output );
	}
	return $output;

}

function woo_ce_generate_file_headers( $post_mime_type = 'text/csv' ) {

	global $export;

	header( sprintf( 'Content-Type: %s; charset=%s', esc_attr( $post_mime_type ), esc_attr( $export->encoding ) ) );
	header( sprintf( 'Content-Disposition: attachment; filename="%s"', $export->filename ) );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );
	header( sprintf( 'Content-Encoding: %s', esc_attr( $export->encoding ) ) );

}

// Function to generate filename of export file based on the Export type
function woo_ce_generate_filename( $export_type = '', $override = '' ) {

	// Check if a fixed filename hasn't been provided
	if( !empty( $override ) ) {
		$filename = $override;
	} else {
		// Get the filename from WordPress options
		$filename = woo_ce_get_option( 'export_filename', '%store_name%-export_%dataset%-%date%-%time%-%random%' );
		// Check for empty filename
		if( empty( $filename ) )
			$filename = '%store_name%-export_%dataset%-%date%-%time%-%random%';

		// Strip file extensions if present
		$filename = str_replace( array( '.xml', '.xls', '.csv' ), '', $filename );
		if( ( strpos( $filename, '.xml' ) !== false ) || ( strpos( $filename, '.xls' ) !== false ) || ( strpos( $filename, '.csv' ) !== false ) )
			$filename = str_replace( array( '.xml', '.xls', '.csv' ), '', $filename );

	}

	// Populate the available tags
	$date = date( 'Y_m_d' );
	$time = date( 'H_i_s' );
	$random = mt_rand( 10000000, 99999999 );
	$store_name = sanitize_title( get_bloginfo( 'name' ) );

	// Switch out the tags for filled values
	$filename = str_replace( '%dataset%', $export_type, $filename );
	$filename = str_replace( '%date%', $date, $filename );
	$filename = str_replace( '%time%', $time, $filename );
	$filename = str_replace( '%random%', $random, $filename );
	$filename = str_replace( '%store_name%', $store_name, $filename );

	return $filename;

}

// Returns the Post object of the export file saved as an attachment to the WordPress Media library
function woo_ce_save_file_attachment( $filename = '', $post_mime_type = 'text/csv' ) {

	if( !empty( $filename ) ) {
		$post_type = 'woo-export';
		$args = array(
			'post_status' => 'private',
			'post_title' => $filename,
			'post_type' => $post_type,
			'post_mime_type' => $post_mime_type
		);
		$post_ID = wp_insert_attachment( $args, $filename );
		if( is_wp_error( $post_ID ) )
			woo_ce_error_log( sprintf( 'save_file_attachment() - $s: %s', $filename, $result->get_error_message() ) );
		else
			return $post_ID;
	}

}

// Updates the GUID of the export file attachment to match the correct file URL
function woo_ce_save_file_guid( $post_ID, $export_type, $upload_url = '' ) {

	add_post_meta( $post_ID, '_woo_export_type', $export_type );
	if( !empty( $upload_url ) ) {
		$args = array(
			'ID' => $post_ID,
			'guid' => $upload_url
		);
		wp_update_post( $args );
	}

}

// Save critical export details against the archived export
function woo_ce_save_file_details( $post_ID ) {

	global $export;

	add_post_meta( $post_ID, '_woo_start_time', $export->start_time );
	add_post_meta( $post_ID, '_woo_idle_memory_start', $export->idle_memory_start );
	add_post_meta( $post_ID, '_woo_columns', $export->total_columns );
	// Check if column headers are included
	if( $export->header_formatting && in_array( $export->export_format, array( 'csv', 'tsv', 'xls', 'xlsx' ) ) )
		$export->total_rows++;
	add_post_meta( $post_ID, '_woo_rows', $export->total_rows );
	add_post_meta( $post_ID, '_woo_data_memory_start', $export->data_memory_start );
	add_post_meta( $post_ID, '_woo_data_memory_end', $export->data_memory_end );

}

// Update detail of existing archived export
function woo_ce_update_file_detail( $post_ID, $detail, $value ) {

	if( strstr( $detail, '_woo_' ) !== false )
		update_post_meta( $post_ID, $detail, $value );

}

// Returns a list of allowed Export type statuses, can be overridden on a per-Export type basis
function woo_ce_post_statuses( $extra_status = array(), $override = false ) {

	$output = array(
		'publish',
		'pending',
		'draft',
		'future',
		'private',
		'trash'
	);
	if( $override ) {
		$output = $extra_status;
	} else {
		if( $extra_status )
			$output = array_merge( $output, $extra_status );
	}
	return $output;

}

function woo_ce_get_mime_types() {

	$mime_types = array(
		'csv' => 'text/csv',
		'tsv' => 'text/tab-separated-values',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xml' => 'application/xml',
		'rss' => 'application/rss+xml'
	);
	return $mime_types;

}

function woo_ce_get_mime_type_extension( $mime_type, $search_by = 'extension' ) {

	$mime_types = woo_ce_get_mime_types();
	if( $search_by == 'extension' ) {
		if( isset( $mime_types[$mime_type] ) )
			return $mime_types[$mime_type];
	} else if( $search_by == 'mime_type' ) {
		if( $key = array_search( $mime_type, $mime_types ) )
			return strtoupper( $key );
	}

}

function woo_ce_add_missing_mime_type( $mime_types = array() ) {

	// Add CSV mime type if it has been removed
	if( !isset( $mime_types['csv'] ) )
		$mime_types['csv'] = 'text/csv';
	// Add TSV mime type if it has been removed
	if( !isset( $mime_types['tsv'] ) )
		$mime_types['tsv'] = 'text/tab-separated-values';
	// Add XLS mime type if it has been removed
	if( !isset( $mime_types['xls'] ) )
		$mime_types['xls'] = 'application/vnd.ms-excel';
	// Add XLSX mime type if it has been removed
	if( !isset( $mime_types['xlsx'] ) )
		$mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	// Add XML mime type if it has been removed
	if( !isset( $mime_types['xml'] ) )
		$mime_types['xml'] = 'application/xml';
	// Add RSS mime type if it has been removed
	if( !isset( $mime_types['rss'] ) )
		$mime_types['rss'] = 'application/rss+xml';
	return $mime_types;

}
add_filter( 'upload_mimes', 'woo_ce_add_missing_mime_type' );

if( !function_exists( 'woo_ce_sort_fields' ) ) {
	function woo_ce_sort_fields( $key ) {

		return $key;

	}
}

function woo_ce_register_scheduled_export_cpt() {

	$labels = array(
		'name'               => __( 'Scheduled Exports', 'woocommerce-exporter' ),
		'singular_name'      => __( 'Scheduled Export', 'woocommerce-exporter' ),
		'add_new'            => __( 'Add Scheduled Export', 'woocommerce-exporter' ),
		'add_new_item'       => __( 'Add New Scheduled Export', 'woocommerce-exporter' ),
		'edit'               => __( 'Edit', 'woocommerce-exporter' ),
		'edit_item'          => __( 'Edit Scheduled Export', 'woocommerce-exporter' ),
		'new_item'           => __( 'New Scheduled Export', 'woocommerce-exporter' ),
		'view'               => __( 'View Scheduled Export', 'woocommerce-exporter' ),
		'view_item'          => __( 'View Scheduled Export', 'woocommerce-exporter' ),
		'search_items'       => __( 'Search Scheduled Exports', 'woocommerce-exporter' ),
		'not_found'          => __( 'No Scheduled exports found', 'woocommerce-exporter' ),
		'not_found_in_trash' => __( 'No Scheduled exports found in trash', 'woocommerce-exporter' ),
		'parent'             => __( 'Parent Scheduled Exports', 'woocommerce-exporter' ),
		'menu_name'          => _x( 'Scheduled Exports', 'Scheduled Export', 'woocommerce-exporter' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'This is where Scheduled exports for Store Exporter Deluxe are managed.', 'woocommerce-exporter' ),
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => null,
		'supports'            => array( 'title', 'excerpt' )
	);
	$post_type = 'scheduled_export';
	register_post_type( $post_type, $args );

}

function woo_ce_get_scheduled_exports( $args = array() ) {

	$post_type = 'scheduled_export';
	$defaults = array(
		'post_type' => $post_type,
		'posts_per_page' => -1,
		'fields' => 'ids',
		'suppress_filters' => 1
	);
	$args = wp_parse_args( $args, $defaults );

	$exports_query = new WP_Query( $args );
	if( $exports_query->posts ) {
		return $exports_query->posts;
	}

}

function woo_ce_get_next_scheduled_export( $scheduled_export = 0, $format = 'human' ) {

	// Check that WordPress has set up a wp_cron task for our export

	$args = array(
		'id' => $scheduled_export
	);
	$time = wp_next_scheduled( 'woo_ce_auto_export_schedule_' . $scheduled_export, $args );
	if( $time !== false ) {
		if( $format == 'human' )
			return human_time_diff( current_time( 'timestamp', 1 ), $time );
		else
			return $time;
	}

/*
	if( wp_next_scheduled( 'woo_ce_auto_export_schedule' ) ) {
		$cron = ( function_exists( '_get_cron_array' ) ? _get_cron_array() : array() );
		if( !empty( $cron ) ) {
			foreach( $cron as $timestamp => $cronhooks ) {
				foreach ( (array) $cronhooks as $hook => $events ) {
					if( $hook == 'woo_ce_auto_export_schedule' )
						return human_time_diff( current_time( 'timestamp', 1 ), $timestamp );
				}
			}
		}
		unset( $cron );
	}
*/

}

function woo_ce_add_recent_scheduled_export( $scheduled_export = 0, $gui = '', $post_ID = 0, $error = '' ) {

	global $export;

	// Get the list of existing recent scheduled exports
	$recent_exports = woo_ce_get_option( 'recent_scheduled_exports', array() );
	if( empty( $recent_exports ) )
		$recent_exports = array();
	$size = count( $recent_exports );
	// Get the limit from the WordPress Dashboard widget
	if( !$widget_options = woo_ce_get_option( 'recent_scheduled_export_widget_options', array() ) ) {
		$widget_options = array(
			'number' => 5
		);
	}

	// Check if we have maxed out our recent scheduled exports
	if( $size >= $widget_options['number'] )
		array_shift( $recent_exports );
	$post_ID = ( isset( $post_ID ) ? $post_ID : 0 );
	$time = time();
	$recent_exports[] = array(
		'post_id' => ( empty( $export->error ) ? $post_ID : 0 ),
		'name' => $export->filename,
		'date' => $time,
		'method' => $gui,
		'error' => ( !empty( $error ) ? $error : $export->error ),
		'scheduled_id' => $scheduled_export
	);
	woo_ce_update_option( 'recent_scheduled_exports', $recent_exports );

}

// Convert the legacy scheduled export WordPress Options to CPT
function woo_ce_legacy_scheduled_export() {

	global $user_ID;

	$post_type = 'scheduled_export';
	$args = array(
		'post_type' => $post_type,
		'post_date' => current_time( 'mysql' ),
		'post_date_gmt' => current_time( 'mysql', 1 ),
		'post_title' => __( 'My scheduled export', 'woocommerce-exporter' ),
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_content' => '',
		'post_excerpt' => ''
	);
	$post_ID = wp_insert_post( $args );
	if( is_wp_error( $post_ID ) !== true ) {
		// Load WordPress Options for migration

		// General
		$export_type = woo_ce_get_option( 'auto_type', 'product' );
		update_post_meta( $post_ID, '_export_type', $export_type );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_type' );
		$export_format = woo_ce_get_option( 'auto_format', 'csv' );
		update_post_meta( $post_ID, '_export_format', $export_format );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_format' );
		$export_method = woo_ce_get_option( 'auto_method', 'archive' );
		update_post_meta( $post_ID, '_export_method', $export_method );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_method' );
		$export_fields = woo_ce_get_option( 'export_fields', 'all' );
		update_post_meta( $post_ID, '_export_fields', $export_fields );
		delete_option( WOO_CD_PREFIX . '_' . 'export_fields' );

		// Filters
		$product_filter_category = woo_ce_get_option( 'auto_product_category', false );
		update_post_meta( $post_ID, '_filter_product_category', $product_filter_category );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_category' );
		$product_filter_tag = woo_ce_get_option( 'auto_product_tag', false );
		update_post_meta( $post_ID, '_filter_product_tag', $product_filter_tag );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_tag' );
		$product_filter_status = woo_ce_get_option( 'auto_product_status', false );
		update_post_meta( $post_ID, '_filter_product_status', $product_filter_status );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_status' );
		$product_filter_type = woo_ce_get_option( 'auto_product_type', false );
		update_post_meta( $post_ID, '_filter_product_type', $product_filter_type );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_type' );
		$product_filter_stock = woo_ce_get_option( 'auto_product_stock', false );
		update_post_meta( $post_ID, '_filter_product_stock', $product_filter_stock );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_stock' );
		$product_filter_featured = woo_ce_get_option( 'auto_product_featured', false );
		update_post_meta( $post_ID, '_filter_product_featured', $product_filter_featured );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_featured' );
		$product_filter_shipping_class = woo_ce_get_option( 'auto_product_shipping_class', false );
		update_post_meta( $post_ID, '_filter_product_shipping_class', $product_filter_shipping_class );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_product_shipping_class' );

		$order_filter_date = woo_ce_get_option( 'auto_order_date', false );
		update_post_meta( $post_ID, '_filter_order_date', $order_filter_date );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_date' );
		$order_filter_date_variable = woo_ce_get_option( 'auto_order_date_variable', false );
		update_post_meta( $post_ID, '_filter_order_date_variable', $order_filter_date_variable );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_date_variable' );
		$order_filter_date_variable_length = woo_ce_get_option( 'auto_order_date_variable_length', false );
		update_post_meta( $post_ID, '_filter_order_date_variable_length', $order_filter_date_variable_length );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_date_variable_length' );
		$order_filter_date_from = woo_ce_get_option( 'auto_order_dates_from', false );
		update_post_meta( $post_ID, '_filter_order_dates_from', $order_filter_date_from );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_dates_from' );
		$order_filter_date_to = woo_ce_get_option( 'auto_order_dates_to', false );
		update_post_meta( $post_ID, '_filter_order_dates_to', $order_filter_date_to );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_dates_to' );
		$order_filter_status = woo_ce_get_option( 'auto_order_status', false );
		update_post_meta( $post_ID, '_filter_order_status', $order_filter_status );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_status' );
		$order_filter_product = woo_ce_get_option( 'auto_order_product', false );
		update_post_meta( $post_ID, '_filter_order_product', $order_filter_product );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_product' );
		$order_filter_billing_country = woo_ce_get_option( 'auto_order_billing_country', false );
		update_post_meta( $post_ID, '_filter_order_billing_country', $order_filter_billing_country );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_billing_country' );
		$order_filter_shipping_country = woo_ce_get_option( 'auto_order_shipping_country', false );
		update_post_meta( $post_ID, '_filter_order_shipping_country', $order_filter_shipping_country );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_shipping_country' );
		$order_filter_payment = woo_ce_get_option( 'auto_order_payment', false );
		update_post_meta( $post_ID, '_filter_order_payment', $order_filter_payment );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_payment' );
		$order_filter_shipping = woo_ce_get_option( 'auto_order_shipping', false );
		update_post_meta( $post_ID, '_filter_order_shipping', $order_filter_shipping );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_order_shipping' );

		// Method
		$email_to = woo_ce_get_option( 'email_to', false );
		update_post_meta( $post_ID, '_method_email_to', $email_to );
		delete_option( WOO_CD_PREFIX . '_' . 'email_to' );
		$email_subject = woo_ce_get_option( 'email_subject', false );
		update_post_meta( $post_ID, '_method_email_subject', $email_subject );
		delete_option( WOO_CD_PREFIX . '_' . 'email_subject' );

		$post_to = woo_ce_get_option( 'post_to', false );
		update_post_meta( $post_ID, '_method_post_to', $post_to );
		delete_option( WOO_CD_PREFIX . '_' . 'post_to' );

		$ftp_host = woo_ce_get_option( 'auto_ftp_method_host', false );
		update_post_meta( $post_ID, '_method_ftp_host', $ftp_host );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_host' );
		$ftp_port = woo_ce_get_option( 'auto_ftp_method_port', false );
		update_post_meta( $post_ID, '_method_ftp_port', $ftp_port );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_port' );
		$ftp_protocol = woo_ce_get_option( 'auto_ftp_method_protocol', false );
		update_post_meta( $post_ID, '_method_ftp_protocol', $ftp_protocol );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_protocol' );
		$ftp_user = woo_ce_get_option( 'auto_ftp_method_user', false );
		update_post_meta( $post_ID, '_method_ftp_user', $ftp_user );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_user' );
		$ftp_pass = woo_ce_get_option( 'auto_ftp_method_pass', false );
		update_post_meta( $post_ID, '_method_ftp_pass', $ftp_pass );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_pass' );
		$ftp_path = woo_ce_get_option( 'auto_ftp_method_path', false );
		update_post_meta( $post_ID, '_method_ftp_path', $ftp_path );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_path' );
		$ftp_filename = woo_ce_get_option( 'auto_ftp_method_filename', false );
		update_post_meta( $post_ID, '_method_ftp_filename', $ftp_filename );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_filename' );
		$ftp_passive = woo_ce_get_option( 'auto_ftp_method_passive', false );
		update_post_meta( $post_ID, '_method_ftp_passive', $ftp_passive );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_passive' );
		$ftp_timeout = woo_ce_get_option( 'auto_ftp_method_timeout', false );
		update_post_meta( $post_ID, '_method_ftp_timeout', $ftp_timeout );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_ftp_method_timeout' );

		// Scheduling
		$auto_schedule = woo_ce_get_option( 'auto_schedule', false );
		if( $auto_schedule == false )
			$auto_schedule = 'monthly';
		update_post_meta( $post_ID, '_auto_schedule', $auto_schedule );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_schedule' );
		$auto_interval = woo_ce_get_option( 'auto_interval', false );
		update_post_meta( $post_ID, '_auto_interval', $auto_interval );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_interval' );
		$auto_commence = woo_ce_get_option( 'auto_commence', false );
		update_post_meta( $post_ID, '_auto_commence', $auto_commence );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_commence' );
		$auto_commence_date = woo_ce_get_option( 'auto_commence_date', false );
		update_post_meta( $post_ID, '_auto_commence_date', $auto_commence_date );
		delete_option( WOO_CD_PREFIX . '_' . 'auto_commence_date' );

		// Delete the legacy WP-CRON
		wp_clear_scheduled_hook( 'woo_ce_auto_export_schedule' );

		return true;

	} else {
		wp_delete_post( $post_ID, true );
	}

}

// Add Store Export to filter types on the WordPress Media screen
function woo_ce_add_post_mime_type( $post_mime_types = array() ) {

	$post_mime_types['text/csv'] = array( __( 'Store Exports (CSV)', 'woocommerce-exporter' ), __( 'Manage Store Exports (CSV)', 'woocommerce-exporter' ), _n_noop( 'Store Export - CSV <span class="count">(%s)</span>', 'Store Exports - CSV <span class="count">(%s)</span>' ) );
	$post_mime_types['application/vnd.ms-excel'] = array( __( 'Store Exports (Excel 2003)', 'woocommerce-exporter' ), __( 'Manage Store Exports (Excel 2003)', 'woocommerce-exporter' ), _n_noop( 'Store Export - Excel 2003 <span class="count">(%s)</span>', 'Store Exports - Excel 2003 <span class="count">(%s)</span>' ) );
	$post_mime_types['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] = array( __( 'Store Exports (Excel 2007)', 'woocommerce-exporter' ), __( 'Manage Store Exports (Excel 2007)', 'woocommerce-exporter' ), _n_noop( 'Store Export - Excel 2007 <span class="count">(%s)</span>', 'Store Exports - Excel 2007 <span class="count">(%s)</span>' ) );
	$post_mime_types['application/xml'] = array( __( 'Store Exports (XML)', 'woocommerce-exporter' ), __( 'Manage Store Exports (XML)', 'woocommerce-exporter' ), _n_noop( 'Store Export - XML <span class="count">(%s)</span>', 'Store Exports - XML <span class="count">(%s)</span>' ) );
	$post_mime_types['application/rss+xml'] = array( __( 'Store Exports (RSS)', 'woocommerce-exporter' ), __( 'Manage Store Exports (RSS)', 'woocommerce-exporter' ), _n_noop( 'Store Export - RSS <span class="count">(%s)</span>', 'Store Exports - RSS <span class="count">(%s)</span>' ) );
	return $post_mime_types;

}
add_filter( 'post_mime_types', 'woo_ce_add_post_mime_type' );

function woo_ce_current_memory_usage() {

	$output = '';
	if( function_exists( 'memory_get_usage' ) )
		$output = round( memory_get_usage( true ) / 1024 / 1024, 2 );
	return $output;

}

function woo_ce_get_start_of_week_day() {

	global $wp_locale;

	$output = 'Monday';
	$start_of_week = get_option( 'start_of_week', 0 );
	for( $day_index = 0; $day_index <= 6; $day_index++ ) {
		if( $start_of_week == $day_index ) {
			$output = $wp_locale->get_weekday( $day_index );
			break;
		}
	}
	return $output;

}

// Provided by Pippin Williamson, mentioned on WP Beginner (http://www.wpbeginner.com/wp-tutorials/how-to-display-a-users-ip-address-in-wordpress/)
function woo_ce_get_visitor_ip_address() {

	if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters( 'woo_ce_get_visitor_ip_address', $ip );

}

function woo_ce_format_ip_address( $ip = '' ) {

	// Check if the IP Address is just a loopback
	if( in_array( $ip, array( '::1', '127.0.0.1', 'localhost' ) ) )
		$ip = '';
	return $ip;

}

function woo_ce_get_line_ending() {

	$output = PHP_EOL;
	$line_ending_formatting = woo_ce_get_option( 'line_ending_formatting', 'windows' );
	if( $line_ending_formatting == false || $line_ending_formatting == '' ) {
		woo_ce_error_log( __( 'Line ending formatting export option was corrupted, defaulted to windows', 'woocommerce-exporter' ) );
		$line_ending_formatting = 'windows';
		woo_ce_update_option( 'line_ending_formatting', 'windows' );
	}
	switch( $line_ending_formatting ) {

		case 'windows':
			$output = "\r\n";
			break;

		case 'mac':
			$output = "\r";
			break;

		case 'unix':
			$output = "\n";
			break;

	}
	$output = apply_filters( 'woo_ce_get_line_ending', $output, $line_ending_formatting );
	return $output;

}

function woo_ce_detect_wpml() {

	if( defined( 'ICL_LANGUAGE_CODE' ) )
		return true;

}

function woo_ce_detect_product_brands() {

	if( class_exists( 'WC_Brands' ) || class_exists( 'woo_brands' ) || taxonomy_exists( apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' ) ) )
		return true;

}

function woo_ce_error_log( $message = '' ) {

	if( $message == '' )
		return;

	if( class_exists( 'WC_Logger' ) ) {
		$logger = new WC_Logger();
		$logger->add( WOO_CD_PREFIX, $message );
		return true;
	} else {
		// Fallback where the WooCommerce logging engine is unavailable
		error_log( sprintf( '[store-exporter-deluxe] %s', $message ) );
	}

}

function woo_ce_error_get_last_message() {

	$output = '-';
	if( function_exists( 'error_get_last' ) ) {
		$last_error = error_get_last();
		if( isset( $last_error ) && isset( $last_error['message'] ) ) {
			$output = $last_error['message'];
		}
		unset( $last_error );
	}
	return $output;

}

function woo_ce_get_option( $option = null, $default = false, $allow_empty = false ) {

	$output = false;
	if( $option !== null ) {
		$separator = '_';
		$output = get_option( WOO_CD_PREFIX . $separator . $option, $default );
		if( $allow_empty == false && $output != 0 && ( $output == false || $output == '' ) )
			$output = $default;
	}
	return $output;

}

function woo_ce_update_option( $option = null, $value = null ) {

	$output = false;
	if( $option !== null && $value !== null ) {
		$separator = '_';
		$output = update_option( WOO_CD_PREFIX . $separator . $option, $value );
	}
	return $output;

}
?>