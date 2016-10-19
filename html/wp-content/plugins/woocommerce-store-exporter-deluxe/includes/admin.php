<?php
// Display admin notice on screen load
function woo_cd_admin_notice( $message = '', $priority = 'updated', $screen = '' ) {

	if( $priority == false || $priority == '' )
		$priority = 'updated';
	if( $message <> '' ) {
		ob_start();
		woo_cd_admin_notice_html( $message, $priority, $screen );
		$output = ob_get_contents();
		ob_end_clean();
		// Check if an existing notice is already in queue
		$existing_notice = get_transient( WOO_CD_PREFIX . '_notice' );
		if( $existing_notice !== false ) {
			$existing_notice = base64_decode( $existing_notice );
			$output = $existing_notice . $output;
		}
		set_transient( WOO_CD_PREFIX . '_notice', base64_encode( $output ), MINUTE_IN_SECONDS );
		add_action( 'admin_notices', 'woo_cd_admin_notice_print' );
	}

}

// HTML template for admin notice
function woo_cd_admin_notice_html( $message = '', $priority = 'updated', $screen = '' ) {

	// Display admin notice on specific screen
	if( !empty( $screen ) ) {

		global $pagenow;

		if( is_array( $screen ) ) {
			if( in_array( $pagenow, $screen ) == false )
				return;
		} else {
			if( $pagenow <> $screen )
				return;
		}

	} ?>
<div id="message" class="<?php echo $priority; ?>">
	<p><?php echo $message; ?></p>
</div>
<?php

}

// Grabs the WordPress transient that holds the admin notice and prints it
function woo_cd_admin_notice_print() {

	$output = get_transient( WOO_CD_PREFIX . '_notice' );
	if( $output !== false ) {
		delete_transient( WOO_CD_PREFIX . '_notice' );
		$output = base64_decode( $output );
		echo $output;
	}

}

// HTML template header on Store Exporter screen
function woo_cd_template_header( $title = '', $icon = 'woocommerce' ) {

	if( $title )
		$output = $title;
	else
		$output = __( 'Store Export', 'woocommerce-exporter' ); ?>
<div id="woo-ce" class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-woocommerce-importer"><br /></div>
	<h2>
		<?php echo $output; ?>
	</h2>
<?php

}

// HTML template footer on Store Exporter screen
function woo_cd_template_footer() { ?>
</div>
<!-- .wrap -->
<?php

}

function woo_cd_template_header_title() {

	return __( 'Store Exporter Deluxe', 'woocommerce-exporter' );

}
add_filter( 'woo_ce_template_header', 'woo_cd_template_header_title' );

function woo_ce_export_options_export_format() {

	$export_format = woo_ce_get_option( 'export_format', 'csv' );

	ob_start(); ?>
<tr>
	<th>
		<label><?php _e( 'Export format', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<label><input type="radio" name="export_format" value="csv"<?php checked( $export_format, 'csv' ); ?> /> <?php _e( 'CSV', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="tsv"<?php checked( $export_format, 'tsv' ); ?> /> <?php _e( 'TSV', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Tab Separated Values)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xls"<?php checked( $export_format, 'xls' ); ?> /> <?php _e( 'Excel (XLS)', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xlsx"<?php checked( $export_format, 'xlsx' ); ?> /> <?php _e( 'Excel (XLSX)', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xml"<?php checked( $export_format, 'xml' ); ?> /> <?php _e( 'XML', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="rss"<?php checked( $export_format, 'rss' ); ?> /> <?php _e( 'RSS 2.0', 'woocommerce-exporter' ); ?> <span class="description"><?php printf( __( '(<attr title="%s">XML</attr> feed in RSS 2.0 format)', 'woocommerce-exporter' ), __( 'EXtensible Markup Language', 'woocommerce-exporter' ) ); ?></span></label>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

// Add Export, Docs and Support links to the Plugins screen
function woo_cd_add_settings_link( $links, $file ) {

	// Manually force slug
	$this_plugin = WOO_CD_RELPATH;

	if( $file == $this_plugin ) {
		$support_url = 'http://www.visser.com.au/premium-support/';
		$support_link = sprintf( '<a href="%s" target="_blank">' . __( 'Support', 'woocommerce-exporter' ) . '</a>', $support_url );
		$docs_url = 'http://www.visser.com.au/docs/';
		$docs_link = sprintf( '<a href="%s" target="_blank">' . __( 'Docs', 'woocommerce-exporter' ) . '</a>', $docs_url );
		$export_link = sprintf( '<a href="%s">' . __( 'Export', 'woocommerce-exporter' ) . '</a>', esc_url( add_query_arg( 'page', 'woo_ce', 'admin.php' ) ) );
		array_unshift( $links, $support_link );
		array_unshift( $links, $docs_link );
		array_unshift( $links, $export_link );
	}
	return $links;

}
add_filter( 'plugin_action_links', 'woo_cd_add_settings_link', 10, 2 );

function woo_ce_admin_order_column_headers( $columns ) {

	// Check if another Plugin has registered this column
	if( !isset( $columns['woo_ce_export_status'] ) ) {
		$pos = array_search( 'order_title', array_keys( $columns ) );
		$columns = array_merge(
			array_slice( $columns, 0, $pos ),
			array( 'woo_ce_export_status' => __( 'Export Status', 'woocommerce-exporter' ) ),
			array_slice( $columns, $pos )
		);
	}
	// $columns['woo_ce_export_status'] = __( 'Export Status', 'woocommerce-exporter' );
	return $columns;

}

function woo_ce_admin_order_column_content( $column ) {

	global $post;

	if( $column == 'woo_ce_export_status' ) {
		if( $is_exported = ( get_post_meta( $post->ID, '_woo_cd_exported', true ) ? true : false ) ) {
			printf( '<mark title="%s" class="%s">%s</mark>', __( 'This Order has been exported and will not be included in future exports filtered by \'Since last export\'.', 'woocommerce-exporter' ), 'csv_exported', __( 'Exported', 'woocommerce-exporter' ) );
		} else {
			printf( '<mark title="%s" class="%s">%s</mark>', __( 'This Order has not yet been exported using the \'Since last export\' Order Date filter.', 'woocommerce-exporter' ), 'csv_not_exported', __( 'Not Exported', 'woocommerce-exporter' ) );
		}

		// Allow Plugin/Theme authors to add their own content within this column
		do_action( 'woo_ce_admin_order_column_content', $post->ID );

	}

}

// Display the bulk actions for Orders on the Orders screen
function woo_ce_admin_order_bulk_actions() {

	global $post_type;

	// Check if this is the Orders screen
	if( $post_type != 'shop_order' )
		return;

	// In-line javascript
	ob_start(); ?>
<script type="text/javascript">
jQuery(function() {
	jQuery('<option>').val('download_csv').text('<?php _e( 'Download as CSV', 'woocommerce-exporter' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_csv').text('<?php _e( 'Download as CSV', 'woocommerce-exporter' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_tsv').text('<?php _e( 'Download as TSV', 'woocommerce-exporter' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_tsv').text('<?php _e( 'Download as TSV', 'woocommerce-exporter' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_xls').text('<?php _e( 'Download as XLS', 'woocommerce-exporter' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_xls').text('<?php _e( 'Download as XLS', 'woocommerce-exporter' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_xlsx').text('<?php _e( 'Download as XLSX', 'woocommerce-exporter' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_xlsx').text('<?php _e( 'Download as XLSX', 'woocommerce-exporter' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_xml').text('<?php _e( 'Download as XML', 'woocommerce-exporter' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_xml').text('<?php _e( 'Download as XML', 'woocommerce-exporter' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('unflag_export').text('<?php _e( 'Remove export flag', 'woocommerce-exporter' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('unflag_export').text('<?php _e( 'Remove export flag', 'woocommerce-exporter' )?>').appendTo("select[name='action2']");
});
</script>
<?php
	ob_end_flush();

}

// Process the bulk action for Orders on the Orders screen
function woo_ce_admin_order_process_bulk_action() {

	$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
	$action = $wp_list_table->current_action();
	$export_format = false;
	switch( $action ) {

		case 'download_csv':
			$export_format = 'csv';
			break;

		case 'download_tsv':
			$export_format = 'tsv';
			break;

		case 'download_xls':
			$export_format = 'xls';
			break;

		case 'download_xlsx':
			$export_format = 'xlsx';
			break;

		case 'download_xml':
			$export_format = 'xml';
			break;

		case 'unflag_export':
			if( isset( $_REQUEST['post'] ) ) {
				$post_ids = array_map( 'absint', (array)$_REQUEST['post'] );
				if( !empty( $post_ids ) ) {
					foreach( $post_ids as $post_id ) {
						// Remove exported flag from Order
						delete_post_meta( $post_id, '_woo_cd_exported' );
						if( woo_ce_get_option( 'order_flag_notes', 0 ) ) {
							// Add an additional Order Note
							$order = woo_ce_get_order_wc_data( $post_id );
							$note = __( 'Order export flag was cleared.', 'woocommerce-exporter' );
							$order->add_order_note( $note );
							unset( $order );
						}
					}
				}
				unset( $post_ids );
			}
			return;
			break;

		default:
			return;
			break;

	}
	if( !empty( $export_format ) ) {
		if( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'absint', (array)$_REQUEST['post'] );

			// Replace Order ID with Sequential Order ID if available
			if( !empty( $post_ids ) && ( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) ) {
				$size = count( $post_ids );
				for( $i = 0; $i < $size; $i++ ) {
					$post_ids[$i] = get_post_meta( $post_ids[$i], ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
				}
			}

			set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
			set_transient( WOO_CD_PREFIX . '_single_export_order_ids', implode( ',', $post_ids ), MINUTE_IN_SECONDS );
			unset( $post_ids );
			$gui = 'download';
			$export_type = 'order';
			woo_ce_cron_export( $gui, $export_type );
			delete_transient( WOO_CD_PREFIX . '_single_export_format' );
			delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
			unset( $gui, $export_type );
			exit();
		} else {
			woo_ce_error_log( __( '$_REQUEST[\'post\'] was empty so we could not run woo_ce_admin_order_process_bulk_action()', 'woocommerce-exporter' ) );
			return;
		}
	}

}

// Add Download as... buttons to Actions column on Orders screen
function woo_ce_admin_order_actions( $actions = array(), $order = false ) {

	// Replace Order ID with Sequential Order ID if available
	$order_id = ( isset( $order->id ) ? $order->id : 0 );
	if( !empty( $order ) && ( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) ) {
		$order_id = get_post_meta( $order->id, ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
	}

	if( woo_ce_get_option( 'order_actions_csv', 1 ) ) {
		$export_format = 'csv';
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => $export_format, 'order_ids' => $order_id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as CSV', 'woocommerce-exporter' ),
			'action' => 'download_csv'
		);
	}
	if( woo_ce_get_option( 'order_actions_tsv', 1 ) ) {
		$export_format = 'tsv';
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => $export_format, 'order_ids' => $order_id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as TSV', 'woocommerce-exporter' ),
			'action' => 'download_tsv'
		);
	}
	if( woo_ce_get_option( 'order_actions_xls', 1 ) ) {
		$export_format = 'xls';
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => $export_format, 'order_ids' => $order_id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as XLS', 'woocommerce-exporter' ),
			'action' => 'download_xls'
		);
	}
	if( woo_ce_get_option( 'order_actions_xlsx', 1 ) ) {
		$export_format = 'xlsx';
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => $export_format, 'order_ids' => $order_id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as XLSX', 'woocommerce-exporter' ),
			'action' => 'download_xlsx'
		);
	}
	if( woo_ce_get_option( 'order_actions_xml', 0 ) ) {
		$export_format = 'xml';
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => $export_format, 'order_ids' => $order_id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as XML', 'woocommerce-exporter' ),
			'action' => 'download_xml'
		);
	}

	$actions = apply_filters( 'woo_ce_admin_order_actions', $actions, $order );

	return $actions;

}

// Generate exports for Download as... button clicks
function woo_ce_ajax_export_order() {

	if( check_admin_referer( 'woo_ce_export_order' ) ) {
		$gui = 'download';
		$export_type = 'order';
		$order_ids = ( isset( $_GET['order_ids'] ) ? sanitize_text_field( $_GET['order_ids'] ) : false );
		if( $order_ids ) {
			woo_ce_cron_export( $gui, $export_type );
			exit();
		}
	}

}

function woo_ce_admin_order_single_export_csv( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_format = 'csv';

		// Replace Order ID with Sequential Order ID if available
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			$order->id = get_post_meta( $order->id, ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
		}

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_export_tsv( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_format = 'tsv';

		// Replace Order ID with Sequential Order ID if available
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			$order->id = get_post_meta( $order->id, ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
		}

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_export_xls( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_type = 'xls';

		// Replace Order ID with Sequential Order ID if available
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			$order->id = get_post_meta( $order->id, ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
		}

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_type, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_export_xlsx( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_type = 'xlsx';

		// Replace Order ID with Sequential Order ID if available
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			$order->id = get_post_meta( $order->id, ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
		}

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_type, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_export_xml( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_format = 'xml';

		// Replace Order ID with Sequential Order ID if available
		if( class_exists( 'WC_Seq_Order_Number' ) || class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			$order->id = get_post_meta( $order->id, ( class_exists( 'WC_Seq_Order_Number_Pro' ) ? '_order_number_formatted' : '_order_number' ), true );
		}

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_export_unflag( $order = false ) {

	if( $order !== false ) {
		// Remove exported flag from Order
		delete_post_meta( $order->id, '_woo_cd_exported' );
		if( woo_ce_get_option( 'order_flag_notes', 0 ) ) {
			// Add an additional Order Note
			$order_data = woo_ce_get_order_wc_data( $order->id );
			$note = __( 'Order export flag was cleared.', 'woocommerce-exporter' );
			$order_data->add_order_note( $note );
			unset( $order_data );
		}
	}

}

function woo_ce_admin_order_single_actions( $actions ) {

	$actions['woo_ce_export_order_csv'] = __( 'Download as CSV', 'woocommerce-exporter' );
	$actions['woo_ce_export_order_tsv'] = __( 'Download as TSV', 'woocommerce-exporter' );
	$actions['woo_ce_export_order_xml'] = __( 'Download as XML', 'woocommerce-exporter' );
	$actions['woo_ce_export_order_xls'] = __( 'Download as XLS', 'woocommerce-exporter' );
	$actions['woo_ce_export_order_xlsx'] = __( 'Download as XLSX', 'woocommerce-exporter' );
	$actions['woo_ce_export_order_unflag'] = __( 'Remove export flag', 'woocommerce-exporter' );
	return $actions;

}

// Add Store Export page to WooCommerce screen IDs
function woo_ce_wc_screen_ids( $screen_ids = array() ) {

	$screen_ids[] = 'woocommerce_page_woo_ce';
	return $screen_ids;

}
add_filter( 'woocommerce_screen_ids', 'woo_ce_wc_screen_ids', 10, 1 );

// Add Store Export to WordPress Administration menu
function woo_ce_admin_menu() {

	$hook = add_submenu_page( 'woocommerce', __( 'Store Exporter Deluxe', 'woocommerce-exporter' ), __( 'Store Export', 'woocommerce-exporter' ), 'view_woocommerce_reports', 'woo_ce', 'woo_cd_html_page' );
	// Load scripts and styling just for this Screen
	add_action( 'admin_print_styles-' . $hook, 'woo_ce_enqueue_scripts' );
	$tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '' );
	if( $tab == 'archive' )
		add_action( 'load-' . $hook, 'woo_ce_archives_add_options' );
	add_action( 'current_screen', 'woo_ce_add_help_tab' );

}
add_action( 'admin_menu', 'woo_ce_admin_menu', 11 );

function woo_ce_admin_enqueue_scripts( $hook = '' ) {

	global $post, $pagenow;

	if( $post ) {
		$post_type = 'scheduled_export';
		// Check if this is the Scheduled Export Edit screen
		if( get_post_type( $post->ID ) == $post_type && ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
			// Load up default WooCommerce resources
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'wc-admin-meta-boxes' );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_style( 'woocommerce_admin_styles' );
			// Load up default exporter resources
			woo_ce_enqueue_scripts();
			// Time Picker Addon
			wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( '/js/jquery.timepicker.js', WOO_CD_RELPATH ) );
			wp_enqueue_style( 'jquery-ui-timepicker', plugins_url( '/templates/admin/jquery-ui-timepicker.css', WOO_CD_RELPATH ) );
			// Hide the Pending Review Post Status
			add_action( 'admin_footer', 'woo_ce_admin_scheduled_export_post_status' );
		}
	}

}
add_action( 'admin_enqueue_scripts', 'woo_ce_admin_enqueue_scripts', 11 );

// Load CSS and jQuery scripts for Store Exporter Deluxe screen
function woo_ce_enqueue_scripts() {

	// Simple check that WooCommerce is activated
	if( class_exists( 'WooCommerce' ) ) {

		global $woocommerce;

		// Load WooCommerce default Admin styling
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );

	}

	// Date Picker Addon
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/templates/admin/jquery-ui-datepicker.css', WOO_CD_RELPATH ) );

	// Chosen
	wp_enqueue_style( 'jquery-chosen', plugins_url( '/templates/admin/chosen.css', WOO_CD_RELPATH ) );
	wp_enqueue_script( 'jquery-chosen', plugins_url( '/js/jquery.chosen.js', WOO_CD_RELPATH ), array( 'jquery' ) );
	wp_enqueue_script( 'ajax-chosen', plugins_url( '/js/ajax-chosen.js', WOO_CD_RELPATH ), array( 'jquery', 'jquery-chosen' ) );

	// Common
	wp_enqueue_style( 'woo_ce_styles', plugins_url( '/templates/admin/export.css', WOO_CD_RELPATH ) );
	wp_enqueue_script( 'woo_ce_scripts', plugins_url( '/templates/admin/export.js', WOO_CD_RELPATH ), array( 'jquery', 'jquery-ui-sortable' ) );
	add_action( 'admin_footer', 'woo_ce_datepicker_format' );
	wp_enqueue_style( 'dashicons' );

	if( WOO_CD_DEBUG ) {
		wp_enqueue_style( 'jquery-csvToTable', plugins_url( '/templates/admin/jquery-csvtable.css', WOO_CD_RELPATH ) );
		wp_enqueue_script( 'jquery-csvToTable', plugins_url( '/js/jquery.csvToTable.js', WOO_CD_RELPATH ), array( 'jquery' ) );
	}
	wp_enqueue_style( 'woo_vm_styles', plugins_url( '/templates/admin/woocommerce-admin_dashboard_vm-plugins.css', WOO_CD_RELPATH ) );

/*
	// @mod - We'll do this once 2.1 goes out
	// Check for WordPress 3.3+
	if( get_bloginfo( 'version' ) < '3.3' )
		return;

	// Get the screen ID
	$screen = get_current_screen();
	$screen_id = $screen->id;

	// Get pointers for this screen
	$pointers = apply_filters( 'woo_ce_admin_pointers-' . $screen_id, array() );
	if( !$pointers || !is_array( $pointers ) )
		return;

	// Get dismissed pointers
	$dismissed = explode( ',', (string)get_user_meta( get_current_user_id(), WOO_CD_PREFIX . '_dismissed_pointers', true ) );
	$valid_pointers = array();

	// Check pointers and remove dismissed ones.
	foreach( $pointers as $pointer_id => $pointer ) {

		// Sanity check
		if( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
			continue;

		$pointer['pointer_id'] = $pointer_id;

		// Add the pointer to $valid_pointers array
		$valid_pointers['pointers'][] =  $pointer;

	}

	// No valid pointers? Stop here.
	if( empty( $valid_pointers ) )
		return;

	// Add pointers style to queue.
	wp_enqueue_style( 'wp-pointer' );

	// Add pointers script to queue. Add custom script.
	wp_enqueue_script( 'woo_ce_pointer', plugins_url( '/templates/admin/pointer.js', WOO_CD_RELPATH ), array( 'wp-pointer' ) );

	// Add pointer options to script.
	wp_localize_script( 'woo_ce_pointer', 'woo_ce_pointers', $valid_pointers );
*/

}

function woo_ce_ajax_dismiss_pointer() {

	if( current_user_can( 'manage_options' ) ) {
		$pointer_id = ( isset( $_POST['pointer'] ) ? sanitize_text_field( $_POST['pointer'] ) : false );
		$user_id = get_current_user_id();

		if( empty( $user_id ) )
			return;

		// Get existing dismissed pointers
		$pointers = get_user_meta( $user_id, WOO_CD_PREFIX . '_dismissed_pointers', true );
		if( $pointers == false )
			$pointers = array();

		if( in_array( $pointer_id, $pointers ) == false )
			$pointers[] = $pointer_id;

		$pointers = implode( ',', $pointers );

		// Save the updated dismissed pointers
		// update_user_meta( $user_id, WOO_CD_PREFIX . '_dismissed_pointers', $pointers );

	}

}
// @mod - We'll do this once 2.1 goes out
// add_action( 'wp_ajax_woo_ce_dismiss_pointer', 'woo_ce_ajax_dismiss_pointer' );

function woo_ce_admin_register_pointer_testing( $pointers = array() ) {

	$pointers['xyz140'] = array(
		'target' => '#product',
		'options' => array(
			'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
				__( 'Title' ,'plugindomain'),
				__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.','plugindomain')
			),
			'position' => array( 'edge' => 'top', 'align' => 'left' )
		)
	);
	return $pointers;

}
// @mod - We'll do this once 2.1 goes out
// add_filter( 'woo_ce_admin_pointers-woocommerce_page_woo_ce', 'woo_ce_admin_register_pointer_testing' );

function woo_ce_add_help_tab() {

	$screen = get_current_screen();
	if( $screen->id <> 'woocommerce_page_woo_ce' )
		return;

	$screen->add_help_tab( array(
		'id' => 'woo_ce',
		'title' => __( 'Store Exporter Deluxe', 'woocommerce-exporter' ),
		'content' => 
			'<p>' . __( 'Thank you for using Store Exporter Deluxe :) Should you need help using this Plugin please read the documentation, if an issue persists get in touch with us on Support.', 'woocommerce-exporter' ) . '</p>' .
			'<p><a href="' . 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/' . '" target="_blank" class="button button-primary">' . __( 'Documentation', 'woocommerce-exporter' ) . '</a> <a href="' . 'http://www.visser.com.au/premium-support/' . '" target="_blank" class="button">' . __( 'Support', 'woocommerce-exporter' ) . '</a></p>'
	) );

}

function woo_ce_admin_plugin_row() {

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

	// Detect if another e-Commerce platform is activated
	if( !woo_is_woo_activated() && ( woo_is_jigo_activated() || woo_is_wpsc_activated() ) ) {
		$message = sprintf( __( 'We have detected another e-Commerce Plugin than WooCommerce activated, please check that you are using Store Exporter Deluxe for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
		echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">' . $message . '</div></td></tr>';
	} else if( !woo_is_woo_activated() ) {
		$message = sprintf( __( 'We have been unable to detect the WooCommerce Plugin activated on this WordPress site, please check that you are using Store Exporter Deluxe for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
		echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">' . $message . '</div></td></tr>';
	}

}
 
function woo_ce_admin_override_scheduled_export_notice() {

	global $post_type, $pagenow;

	$page = ( isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '' );

	if( $pagenow == 'admin.php' && $page == 'woo_ce' && isset( $_REQUEST['scheduled'] ) && (int)$_REQUEST['scheduled'] ) {
		$message = __( 'The requested scheduled export will run momentarily.', 'woocommerce-exporter' );
		woo_cd_admin_notice_html( $message );
	}

}
add_action( 'admin_notices', 'woo_ce_admin_override_scheduled_export_notice' );

// HTML active class for the currently selected tab on the Store Exporter screen
function woo_cd_admin_active_tab( $tab_name = null, $tab = null ) {

	if( isset( $_GET['tab'] ) && !$tab )
		$tab = $_GET['tab'];
	else if( !isset( $_GET['tab'] ) && woo_ce_get_option( 'skip_overview', false ) )
		$tab = 'export';
	else
		$tab = 'overview';

	$output = '';
	if( isset( $tab_name ) && $tab_name ) {
		if( $tab_name == $tab )
			$output = ' nav-tab-active';
	}
	echo $output;

}

// HTML template for each tab on the Store Exporter screen
function woo_cd_tab_template( $tab = '' ) {

	if( !$tab )
		$tab = 'overview';

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

	switch( $tab ) {

		case 'overview':
			$skip_overview = woo_ce_get_option( 'skip_overview', false );
			break;

		case 'export':
			$export_type = sanitize_text_field( ( isset( $_POST['dataset'] ) ? $_POST['dataset'] : woo_ce_get_option( 'last_export', 'product' ) ) );
			$export_types = array_keys( woo_ce_get_export_types() );

			// Check if the default export type exists
			if( !in_array( $export_type, $export_types ) )
				$export_type = 'product';

			$product = woo_ce_get_export_type_count( 'product' );
			$category = woo_ce_get_export_type_count( 'category' );
			$tag = woo_ce_get_export_type_count( 'tag' );
			$brand = woo_ce_get_export_type_count( 'brand' );
			$order = woo_ce_get_export_type_count( 'order' );
			$customer = woo_ce_get_export_type_count( 'customer' );
			$user = woo_ce_get_export_type_count( 'user' );
			$review = woo_ce_get_export_type_count( 'review' );
			$coupon = woo_ce_get_export_type_count( 'coupon' );
			$attribute = woo_ce_get_export_type_count( 'attribute' );
			$subscription = woo_ce_get_export_type_count( 'subscription' );
			$product_vendor = woo_ce_get_export_type_count( 'product_vendor' );
			$commission = woo_ce_get_export_type_count( 'commission' );
			$shipping_class = woo_ce_get_export_type_count( 'shipping_class' );
			$ticket = woo_ce_get_export_type_count( 'ticket' );

			add_action( 'woo_ce_export_options', 'woo_ce_export_options_export_format' );
			if( $product_fields = woo_ce_get_product_fields() ) {
				foreach( $product_fields as $key => $product_field )
					$product_fields[$key]['disabled'] = ( isset( $product_field['disabled'] ) ? $product_field['disabled'] : 0 );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_category' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_tag' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_brand' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_vendor' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_status' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_type' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_sku' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_stock_status' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_featured' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_shipping_class' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_language' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_date_modified' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_custom_fields_link' );
				add_action( 'woo_ce_export_product_options_after_table', 'woo_ce_product_sorting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_upsells_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_crosssells_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_variation_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_description_excerpt_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_export_options_gallery_format' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_products_custom_fields' );
				add_action( 'woo_ce_products_custom_fields', 'woo_ce_products_custom_fields_tab_manager' );
				add_action( 'woo_ce_products_custom_fields', 'woo_ce_products_custom_fields_product_addons' );
			}
			if( $category_fields = woo_ce_get_category_fields() ) {
				foreach( $category_fields as $key => $category_field )
					$category_fields[$key]['disabled'] = ( isset( $category_field['disabled'] ) ? $category_field['disabled'] : 0 );
				add_action( 'woo_ce_export_category_options_before_table', 'woo_ce_categories_filter_by_language' );
				add_action( 'woo_ce_export_category_options_after_table', 'woo_ce_category_sorting' );
			}
			if( $tag_fields = woo_ce_get_tag_fields() ) {
				foreach( $tag_fields as $key => $tag_field )
					$tag_fields[$key]['disabled'] = ( isset( $tag_field['disabled'] ) ? $tag_field['disabled'] : 0 );
				add_action( 'woo_ce_export_tag_options_before_table', 'woo_ce_tags_filter_by_language' );
				add_action( 'woo_ce_export_tag_options_after_table', 'woo_ce_tag_sorting' );
			}
			if( $brand_fields = woo_ce_get_brand_fields() ) {
				foreach( $brand_fields as $key => $brand_field )
					$brand_fields[$key]['disabled'] = ( isset( $brand_field['disabled'] ) ? $brand_field['disabled'] : 0 );
				add_action( 'woo_ce_export_brand_options_before_table', 'woo_ce_brand_sorting' );
			}
			if( $order_fields = woo_ce_get_order_fields() ) {
				foreach( $order_fields as $key => $order_field ) {
					$order_fields[$key]['disabled'] = ( isset( $order_field['disabled'] ) ? $order_field['disabled'] : 0 );
					if( isset( $order_field['hidden'] ) && $order_field['hidden'] )
						unset( $order_fields[$key] );
				}
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_date' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_status' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_customer' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_billing_country' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_shipping_country' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_user_role' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_coupon' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_category' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_tag' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_brand' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_order_id' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_payment_gateway' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_shipping_method' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_custom_fields_link' );
				add_action( 'woo_ce_export_order_options_after_table', 'woo_ce_order_sorting' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_items_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_max_order_items' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_items_types' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_flag_notes' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_orders_custom_fields' );
			}
			if( $customer_fields = woo_ce_get_customer_fields() ) {
				foreach( $customer_fields as $key => $customer_field )
					$customer_fields[$key]['disabled'] = ( isset( $customer_field['disabled'] ) ? $customer_field['disabled'] : 0 );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_filter_by_status' );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_filter_by_user_role' );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_custom_fields_link' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_customers_custom_fields' );
			}
			if( $user_fields = woo_ce_get_user_fields() ) {
				foreach( $user_fields as $key => $user_field )
					$user_fields[$key]['disabled'] = ( isset( $user_field['disabled'] ) ? $user_field['disabled'] : 0 );
				add_action( 'woo_ce_export_user_options_before_table', 'woo_ce_users_filter_by_user_role' );
				add_action( 'woo_ce_export_user_options_before_table', 'woo_ce_users_filter_by_date_registered' );
				add_action( 'woo_ce_export_user_options_after_table', 'woo_ce_user_sorting' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_users_custom_fields' );
			}
			if( $review_fields = woo_ce_get_review_fields() ) {
				foreach( $review_fields as $key => $review_field )
					$review_fields[$key]['disabled'] = ( isset( $review_field['disabled'] ) ? $review_field['disabled'] : 0 );
				add_action( 'woo_ce_export_review_options_after_table', 'woo_ce_review_sorting' );
			}
			if( $coupon_fields = woo_ce_get_coupon_fields() ) {
				foreach( $coupon_fields as $key => $coupon_field )
					$coupon_fields[$key]['disabled'] = ( isset( $coupon_field['disabled'] ) ? $coupon_field['disabled'] : 0 );
				add_action( 'woo_ce_export_coupon_options_before_table', 'woo_ce_coupons_filter_by_discount_type' );
				add_action( 'woo_ce_export_coupon_options_before_table', 'woo_ce_coupon_sorting' );
			}
			if( $subscription_fields = woo_ce_get_subscription_fields() ) {
				foreach( $subscription_fields as $key => $subscription_field )
					$subscription_fields[$key]['disabled'] = ( isset( $subscription_field['disabled'] ) ? $subscription_field['disabled'] : 0 );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_subscription_status' );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_subscription_product' );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_customer' );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_source' );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscription_sorting' );
			}
			if( $product_vendor_fields = woo_ce_get_product_vendor_fields() ) {
				foreach( $product_vendor_fields as $key => $product_vendor_field )
					$product_vendor_fields[$key]['disabled'] = ( isset( $product_vendor_field['disabled'] ) ? $product_vendor_field['disabled'] : 0 );
			}
			if( $commission_fields = woo_ce_get_commission_fields() ) {
				foreach( $commission_fields as $key => $commission_field )
					$commission_fields[$key]['disabled'] = ( isset( $commission_field['disabled'] ) ? $commission_field['disabled'] : 0 );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_date' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_product_vendor' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_commission_status' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commission_sorting' );
			}
			if( $shipping_class_fields = woo_ce_get_shipping_class_fields() ) {
				foreach( $shipping_class_fields as $key => $shipping_class_field )
					$shipping_class_fields[$key]['disabled'] = ( isset( $shipping_class_field['disabled'] ) ? $shipping_class_field['disabled'] : 0 );
				add_action( 'woo_ce_export_shipping_class_options_after_table', 'woo_ce_shipping_class_sorting' );
			}
			if( $ticket_fields = woo_ce_get_ticket_fields() ) {
				foreach( $ticket_fields as $key => $ticket_field )
					$ticket_fields[$key]['disabled'] = ( isset( $ticket_field['disabled'] ) ? $ticket_field['disabled'] : 0 );
			}
			// $attribute_fields = woo_ce_get_attribute_fields();
			if( $attribute_fields = false ) {
				foreach( $attribute_fields as $key => $attribute_field )
					$attribute_fields[$key]['disabled'] = ( isset( $attribute_field['disabled'] ) ? $attribute_field['disabled'] : 0 );
			}

			// Export options
			$limit_volume = woo_ce_get_option( 'limit_volume' );
			$offset = woo_ce_get_option( 'offset' );
			break;

		case 'fields':
			$export_type = ( isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '' );
			$export_types = array_keys( woo_ce_get_export_types() );
			$fields = array();
			if( in_array( $export_type, $export_types ) ) {
				if( has_filter( 'woo_ce_' . $export_type . '_fields', 'woo_ce_override_' . $export_type . '_field_labels' ) )
					remove_filter( 'woo_ce_' . $export_type . '_fields', 'woo_ce_override_' . $export_type . '_field_labels', 11 );
				if( function_exists( sprintf( 'woo_ce_get_%s_fields', $export_type ) ) )
					$fields = call_user_func( 'woo_ce_get_' . $export_type . '_fields' );
				$labels = woo_ce_get_option( $export_type . '_labels', array() );
			}
			break;

		case 'scheduled_export':
			$enable_auto = woo_ce_get_option( 'enable_auto', 0 );
			if( !$enable_auto ) {
				$override_url = esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'scheduled_export', 'action' => 'enable_scheduled_exports', '_wpnonce' => wp_create_nonce( 'woo_ce_enable_scheduled_exports' ) ), 'admin.php' ) );
				$message = sprintf( __( 'Scheduled exports are turned off from the <em>Enable scheduled exports</em> option on the Settings tab, to enable scheduled exports globally <a href="%s">click here</a>.', 'woocommerce-exporter' ), $override_url );
				woo_cd_admin_notice_html( $message, 'error' );
			}
			$scheduled_exports = woo_ce_get_scheduled_exports();

			break;

		case 'archive':
			if( isset( $_POST['archive'] ) || isset( $_GET['trashed'] ) ) {
				if( isset( $_POST['archive'] ) ) {
					$post_ID = count( $_POST['archive'] );
				} else if( isset( $_GET['trashed'] ) ) {
					$post_ID = count( $_GET['ids'] );
				}
				$message = _n( 'Archived export has been deleted.', 'Archived exports has been deleted.', $post_ID, 'woocommerce-exporter' );
				woo_cd_admin_notice_html( $message );
			}

			if( woo_ce_get_option( 'delete_file', '1' ) ) {
				$override_url = esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'archive', 'action' => 'enable_archives', '_wpnonce' => wp_create_nonce( 'woo_ce_enable_archives' ) ), 'admin.php' ) );
				$message = sprintf( __( 'New exports will not be archived here as the saving of export archives is disabled from the <em>Enable archives</em> option on the Settings tab, to enable the archives globally <a href="%s">click here</a>.', 'woocommerce-exporter' ), $override_url );
				woo_cd_admin_notice_html( $message, 'error' );
			}

			global $archives_table;

			$archives_table->prepare_items();

			$count = woo_ce_archives_quicklink_count();

			break;

		case 'settings':
			$export_filename = woo_ce_get_option( 'export_filename', '' );
			// Strip file extension from export filename
			if( ( strpos( $export_filename, '.csv' ) !== false ) || ( strpos( $export_filename, '.xml' ) !== false ) || ( strpos( $export_filename, '.xls' ) !== false ) )
				$export_filename = str_replace( array( '.csv', '.xml', '.xls' ), '', $export_filename );
			// Default export filename
			if( $export_filename == false )
				$export_filename = '%store_name%-export_%dataset%-%date%-%time%-%random%';
			$delete_file = woo_ce_get_option( 'delete_file', 1 );
			$timeout = woo_ce_get_option( 'timeout', 0 );
			$encoding = woo_ce_get_option( 'encoding', 'UTF-8' );
			$bom = woo_ce_get_option( 'bom', 1 );
			$delimiter = woo_ce_get_option( 'delimiter', ',' );
			$category_separator = woo_ce_get_option( 'category_separator', '|' );
			$line_ending_formatting = woo_ce_get_option( 'line_ending_formatting', 'windows' );
			$escape_formatting = woo_ce_get_option( 'escape_formatting', 'all' );
			$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
			// Reset the Date Format if corrupted
			if( $date_format == '1' || $date_format == '' || $date_format == false )
				$date_format = 'd/m/Y';
			$file_encodings = ( function_exists( 'mb_list_encodings' ) ? mb_list_encodings() : false );
			add_action( 'woo_ce_export_settings_top', 'woo_ce_export_settings_quicklinks' );
			add_action( 'woo_ce_export_settings_after', 'woo_ce_export_settings_csv' );
			add_action( 'woo_ce_export_settings_after', 'woo_ce_export_settings_extend' );
			break;

		case 'tools':
			// Product Importer Deluxe
			$woo_pd_url = 'http://www.visser.com.au/woocommerce/plugins/product-importer-deluxe/';
			$woo_pd_target = ' target="_blank"';
			if( function_exists( 'woo_pd_init' ) ) {
				$woo_pd_url = esc_url( add_query_arg( array( 'page' => 'woo_pd', 'tab' => null ) ) );
				$woo_pd_target = false;
			}

			// Store Toolkit
			$woo_st_url = 'http://www.visser.com.au/woocommerce/plugins/store-toolkit/';
			$woo_st_target = ' target="_blank"';
			if( function_exists( 'woo_st_admin_init' ) ) {
				$woo_st_url = esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => null ) ) );
				$woo_st_target = false;
			}

			// Export modules
			$module_status = ( isset( $_GET['module_status'] ) ? sanitize_text_field( $_GET['module_status'] ) : false );
			$modules = woo_ce_admin_modules_list( $module_status );
			$modules_all = get_transient( WOO_CD_PREFIX . '_modules_all_count' );
			$modules_active = get_transient( WOO_CD_PREFIX . '_modules_active_count' );
			$modules_inactive = get_transient( WOO_CD_PREFIX . '_modules_inactive_count' );
			break;

	}
	if( $tab ) {
		if( file_exists( WOO_CD_PATH . 'templates/admin/tabs-' . $tab . '.php' ) ) {
			include_once( WOO_CD_PATH . 'templates/admin/tabs-' . $tab . '.php' );
		} else {
			$message = sprintf( __( 'We couldn\'t load the export template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-exporter' ), 'tabs-' . $tab . '.php', WOO_CD_PATH . 'templates/admin/...' );
			woo_cd_admin_notice_html( $message, 'error' );
			ob_start(); ?>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woocommerce-exporter' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woocommerce-exporter' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woocommerce-exporter' ); ?></p>
<?php
			ob_end_flush();
		}
	}

}

function woo_ce_datepicker_format() {

	$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );

	// Check if we need to run date formatting for DatePicker
	if( $date_format <> 'd/m/Y' ) {

		// Convert the PHP date format to be DatePicker compatible
		$php_date_formats = array( 'Y', 'm', 'd' );
		$js_date_formats = array( 'yy', 'mm', 'dd' );

		// Exception for 'F j, Y'
		if( $date_format == 'F j, Y' )
			$date_format = 'd/m/Y';

		$date_format = str_replace( $php_date_formats, $js_date_formats, $date_format );

	} else {
		$date_format = 'dd/mm/yy';
	}

	// In-line javascript
	ob_start(); ?>
<script type="text/javascript">
jQuery(document).ready( function($) {
	var $j = jQuery.noConflict();
	// Date Picker
	if( $j.isFunction($j.fn.datepicker) ) {
		$j('.datepicker').datepicker({
			dateFormat: '<?php echo $date_format; ?>'
		}).on('change', function() {
			if( $j(this).hasClass('product_export') )
				$j('input:radio[name="product_dates_filter"][value="manual"]').prop( 'checked', true );
			if( $j(this).hasClass('user_export') )
				$j('input:radio[name="user_dates_filter"][value="manual"]').prop( 'checked', true );
			if( $j(this).hasClass('order_export') )
				$j('input:radio[name="order_dates_filter"][value="manual"]').prop( 'checked', true );
		});
	}
});
</script>
<?php
	ob_end_flush();

}

// Display the memory usage in the screen footer
function woo_ce_admin_footer_text( $footer_text = '' ) {

	$current_screen = get_current_screen();
	$pages = array(
		'woocommerce_page_woo_ce'
	);
	// Check to make sure we're on the Export screen
	if ( isset( $current_screen->id ) && apply_filters( 'woo_ce_display_admin_footer_text', in_array( $current_screen->id, $pages ) ) ) {
		$memory_usage = woo_ce_current_memory_usage( false );
		$memory_limit = absint( ini_get( 'memory_limit' ) );
		$memory_percent = absint( $memory_usage / $memory_limit * 100 );
		$memory_color = 'font-weight:normal;';
		if( $memory_percent > 75 )
			$memory_color = 'font-weight:bold; color:orange;';
		if( $memory_percent > 90 )
			$memory_color = 'font-weight:bold; color:red;';
		$footer_text .= ' | ' . sprintf( __( 'Memory: %s of %s MB (%s)', 'woocommerce-exporter' ), $memory_usage, $memory_limit, sprintf( '<span style="%s">%s</span>', $memory_color, $memory_percent . '%' ) );
	}
	return $footer_text;

}

// List of WordPress Plugins that Store Exporter integrates with
function woo_ce_admin_modules_list( $module_status = false ) {

	$modules = array();
	$modules[] = array(
		'name' => 'aioseop',
		'title' => __( 'All in One SEO Pack', 'woocommerce-exporter' ),
		'description' => __( 'Optimize your WooCommerce Products for Search Engines. Requires Store Toolkit for All in One SEO Pack integration.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/all-in-one-seo-pack/',
		'slug' => 'all-in-one-seo-pack',
		'function' => 'aioseop_activate'
	);
	$modules[] = array(
		'name' => 'store_toolkit',
		'title' => __( 'Store Toolkit', 'woocommerce-exporter' ),
		'description' => __( 'Store Toolkit includes a growing set of commonly-used WooCommerce administration tools aimed at web developers and store maintainers.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/woocommerce-store-toolkit/',
		'slug' => 'woocommerce-store-toolkit',
		'function' => 'woo_st_admin_init'
	);
	$modules[] = array(
		'name' => 'ultimate_seo',
		'title' => __( 'SEO Ultimate', 'woocommerce-exporter' ),
		'description' => __( 'This all-in-one SEO plugin gives you control over Product details.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/seo-ultimate/',
		'slug' => 'seo-ultimate',
		'function' => 'su_wp_incompat_notice'
	);
	$modules[] = array(
		'name' => 'gpf',
		'title' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' ),
		'description' => __( 'Easily configure data to be added to your Google Merchant Centre feed.', 'woocommerce-exporter' ),
		'url' => 'http://www.leewillis.co.uk/wordpress-plugins/',
		'function' => 'woocommerce_gpf_install'
	);
	$modules[] = array(
		'name' => 'wpseo',
		'title' => __( 'WordPress SEO by Yoast', 'woocommerce-exporter' ),
		'description' => __( 'The first true all-in-one SEO solution for WordPress.', 'woocommerce-exporter' ),
		'url' => 'http://yoast.com/wordpress/seo/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpseoplugin',
		'slug' => 'wordpress-seo',
		'function' => 'wpseo_admin_init'
	);
	$modules[] = array(
		'name' => 'msrp',
		'title' => __( 'WooCommerce MSRP Pricing', 'woocommerce-exporter' ),
		'description' => __( 'Define and display MSRP prices (Manufacturer\'s suggested retail price) to your customers.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/msrp-pricing/',
		'function' => 'woocommerce_msrp_activate'
	);
	$modules[] = array(
		'name' => 'wc_brands',
		'title' => __( 'WooCommerce Brands Addon', 'woocommerce-exporter' ),
		'description' => __( 'Create, assign and list brands for products, and allow customers to filter by brand.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/brands/',
		'class' => 'WC_Brands'
	);
	$modules[] = array(
		'name' => 'wc_cog',
		'title' => __( 'Cost of Goods', 'woocommerce-exporter' ),
		'description' => __( 'Easily track total profit and cost of goods by adding a Cost of Good field to simple and variable products.', 'woocommerce-exporter' ),
		'url' => 'http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/',
		'class' => 'WC_COG'
	);
	$modules[] = array(
		'name' => 'per_product_shipping',
		'title' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
		'description' => __( 'Define separate shipping costs per product which are combined at checkout to provide a total shipping cost.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/per-product-shipping/',
		'function' => 'woocommerce_per_product_shipping_init'
	);
	$modules[] = array(
		'name' => 'vendors',
		'title' => __( 'Product Vendors', 'woocommerce-exporter' ),
		'description' => __( 'Turn your store into a multi-vendor marketplace (such as Etsy or Creative Market).', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/product-vendors/',
		'class' => 'WooCommerce_Product_Vendors'
	);
	$modules[] = array(
		'name' => 'wc_vendors',
		'title' => __( 'WC Vendors', 'woocommerce-exporter' ),
		'description' => __( 'Allow vendors to sell their own products and receive a commission for each sale.', 'woocommerce-exporter' ),
		'url' => 'http://wcvendors.com',
		'class' => 'WC_Vendors'
	);
	$modules[] = array(
		'name' => 'acf',
		'title' => __( 'Advanced Custom Fields', 'woocommerce-exporter' ),
		'description' => __( 'Powerful fields for WordPress developers.', 'woocommerce-exporter' ),
		'url' => 'http://www.advancedcustomfields.com',
		'class' => 'acf'
	);
	$modules[] = array(
		'name' => 'product_addons',
		'title' => __( 'Product Add-ons', 'woocommerce-exporter' ),
		'description' => __( 'Allow your customers to customise your products by adding input boxes, dropdowns or a field set of checkboxes.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/product-add-ons/',
		'class' => 'Product_Addon_Admin'
	);
	$modules[] = array(
		'name' => 'seq',
		'title' => __( 'WooCommerce Sequential Order Numbers', 'woocommerce-exporter' ),
		'description' => __( 'This plugin extends the WooCommerce e-commerce plugin by setting sequential order numbers for new orders.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-sequential-order-numbers/',
		'slug' => 'woocommerce-sequential-order-numbers',
		'class' => 'WC_Seq_Order_Number'
	);
	$modules[] = array(
		'name' => 'seq_pro',
		'title' => __( 'WooCommerce Sequential Order Numbers Pro', 'woocommerce-exporter' ),
		'description' => __( 'Tame your WooCommerce Order Numbers.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/sequential-order-numbers-pro/',
		'class' => 'WC_Seq_Order_Number_Pro'
	);
	$modules[] = array(
		'name' => 'print_invoice_delivery_note',
		'title' => __( 'WooCommerce Print Invoice & Delivery Note', 'woocommerce-exporter' ),
		'description' => __( 'Print invoices and delivery notes for WooCommerce orders.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-delivery-notes/',
		'slug' => 'woocommerce-delivery-notes',
		'class' => 'WooCommerce_Delivery_Notes'
	);
	$modules[] = array(
		'name' => 'pdf_invoices_packing_slips',
		'title' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woocommerce-exporter' ),
		'description' => __( 'Create, print & automatically email PDF invoices & packing slips for WooCommerce orders.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/',
		'slug' => 'woocommerce-pdf-invoices-packing-slips',
		'class' => 'WooCommerce_PDF_Invoices'
	);
	$modules[] = array(
		'name' => 'checkout_manager',
		'title' => __( 'WooCommerce Checkout Manager & WooCommerce Checkout Manager Pro', 'woocommerce-exporter' ),
		'description' => __( 'Manages the WooCommerce Checkout page and WooCommerce Checkout processes.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-checkout-manager/',
		'slug' => 'woocommerce-checkout-manager',
		'function' => array( 'wccs_install', 'wccs_install_pro' )
	);
	$modules[] = array(
		'name' => 'pgsk',
		'title' => __( 'Poor Guys Swiss Knife', 'woocommerce-exporter' ),
		'description' => __( 'A Swiss Knife for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-poor-guys-swiss-knife/',
		'slug' => 'woocommerce-poor-guys-swiss-knife',
		'function' => 'wcpgsk_init'
	);
	$modules[] = array(
		'name' => 'checkout_field_editor',
		'title' => __( 'Checkout Field Editor', 'woocommerce-exporter' ),
		'description' => __( 'Add, edit and remove fields shown on your WooCommerce checkout page.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-checkout-field-editor/',
		'function' => 'woocommerce_init_checkout_field_editor'
	);
	$modules[] = array(
		'name' => 'checkout_field_manager',
		'title' => __( 'Checkout Field Manager', 'woocommerce-exporter' ),
		'description' => __( 'Quickly and effortlessly add, remove and re-orders fields in the checkout process.', 'woocommerce-exporter' ),
		'url' => 'http://61extensions.com/shop/woocommerce-checkout-field-manager/',
		'function' => 'sod_woocommerce_checkout_manager_settings'
	);
	$modules[] = array(
		'name' => 'checkout_addons',
		'title' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' ),
		'description' => __( 'Add fields at checkout for add-on products and services while optionally setting a cost for each add-on.', 'woocommerce-exporter' ),
		'url' => 'http://www.skyverge.com/product/woocommerce-checkout-add-ons/',
		'function' => 'init_woocommerce_checkout_add_ons'
	);
	$modules[] = array(
		'name' => 'local_pickup_plus',
		'title' => __( 'Local Pickup Plus', 'woocommerce-exporter' ),
		'description' => __( 'Let customers pick up products from specific locations.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/local-pickup-plus/',
		'class' => 'WC_Local_Pickup_Plus'
	);
	$modules[] = array(
		'name' => 'gravity_forms',
		'title' => __( 'Gravity Forms', 'woocommerce-exporter' ),
		'description' => __( 'Gravity Forms is hands down the best contact form plugin for WordPress powered websites.', 'woocommerce-exporter' ),
		'url' => 'http://www.gravityforms.com/',
		'class' => 'RGForms'
	);
	$modules[] = array(
		'name' => 'currency_switcher',
		'title' => __( 'WooCommerce Currency Switcher', 'woocommerce-exporter' ),
		'description' => __( 'Currency Switcher for WooCommerce allows your shop to display prices and accept payments in multiple currencies.', 'woocommerce-exporter' ),
		'url' => 'http://aelia.co/shop/currency-switcher-woocommerce/',
		'class' => 'WC_Aelia_CurrencySwitcher'
	);
	$modules[] = array(
		'name' => 'subscriptions',
		'title' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
		'description' => __( 'WC Subscriptions makes it easy to create and manage products with recurring payments.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-subscriptions/',
		'class' => 'WC_Subscriptions_Manager'
	);
	$modules[] = array(
		'name' => 'extra_product_options',
		'title' => __( 'Extra Product Options', 'woocommerce-exporter' ),
		'description' => __( 'Create extra price fields globally or per-Product', 'woocommerce-exporter' ),
		'url' => 'http://codecanyon.net/item/woocommerce-extra-product-options/7908619',
		'class' => 'TM_Extra_Product_Options'
	);
	$modules[] = array(
		'name' => 'woocommerce_jetpack',
		'title' => __( 'Booster for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Supercharge your WooCommerce site with these awesome powerful features (formally WooCommerce Jetpack).', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-jetpack/',
		'slug' => 'woocommerce-jetpack',
		'class' => 'WC_Jetpack'
	);
	$modules[] = array(
		'name' => 'woocommerce_jetpack_plus',
		'title' => __( 'Booster Plus', 'woocommerce-exporter' ),
		'description' => __( 'Unlock all WooCommerce Booster features and supercharge your WordPress WooCommerce site even more (formally WooCommerce Jetpack Plus).', 'woocommerce-exporter' ),
		'url' => 'http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/',
		'class' => 'WC_Jetpack_Plus'
	);
	$modules[] = array(
		'name' => 'woocommerce_brands',
		'title' => __( 'WooCommerce Brands', 'woocommerce-exporter' ),
		'description' => __( 'Woocommerce Brands Plugin. After Install and active this plugin you\'ll have some shortcode and some widget for display your brands in fornt-end website.', 'woocommerce-exporter' ),
		'url' => 'http://proword.net/Woocommerce_Brands/',
		'class' => 'woo_brands'
	);
	$modules[] = array(
		'name' => 'woocommerce_bookings',
		'title' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
		'description' => __( 'Setup bookable products such as for reservations, services and hires.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-bookings/',
		'class' => 'WC_Bookings'
	);
	$modules[] = array(
		'name' => 'eu_vat',
		'title' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' ),
		'description' => __( 'The EU VAT Number extension lets you collect and validate EU VAT numbers during checkout to identify B2B transactions verses B2C.', 'woocommerce-exporter' ),
		'url' => 'https://www.woothemes.com/products/eu-vat-number/',
		'function' => '__wc_eu_vat_number_init'
	);
	$modules[] = array(
		'name' => 'aelia_eu_vat',
		'title' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' ),
		'description' => __( 'Assists with EU VAT compliance, for the new VAT regime beginning 1st January 2015.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-eu-vat-assistant/',
		'slug' => 'woocommerce-eu-vat-assistant',
		'class' => 'Aelia_WC_RequirementsChecks'
	);
	$modules[] = array(
		'name' => 'hear_about_us',
		'title' => __( 'WooCommerce Hear About Us', 'woocommerce-exporter' ),
		'description' => __( 'Ask where your new customers come from at Checkout.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-hear-about-us/',
		'slug' => 'woocommerce-hear-about-us', // Define this if the Plugin is hosted on the WordPress repo
		'class' => 'WooCommerce_HearAboutUs'
	);
	$modules[] = array(
		'name' => 'wholesale_pricing',
		'title' => __( 'WooCommerce Wholesale Pricing', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to set wholesale prices for products and variations.', 'woocommerce-exporter' ),
		'url' => 'http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/',
		'class' => 'woocommerce_wholesale_pricing'
	);
	$modules[] = array(
		'name' => 'woocommerce_barcodes',
		'title' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to add GTIN (former EAN) codes natively to your products.', 'woocommerce-exporter' ),
		'url' => 'http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/',
		'function' => 'wpps_requirements_met'
	);
	$modules[] = array(
		'name' => 'woocommerce_smart_coupons',
		'title' => __( 'WooCommerce Smart Coupons', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Smart Coupons lets customers buy gift certificates, store credits or coupons easily.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/smart-coupons/',
		'class' => 'WC_Smart_Coupons'
	);
	$modules[] = array(
		'name' => 'woocommerce_preorders',
		'title' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' ),
		'description' => __( 'Sell pre-orders for products in your WooCommerce store.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-pre-orders/',
		'class' => 'WC_Pre_Orders'
	);
	$modules[] = array(
		'name' => 'order_numbers_basic',
		'title' => __( 'WooCommerce Basic Ordernumbers', 'woocommerce-exporter' ),
		'description' => __( 'Lets the user freely configure the order numbers in WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://open-tools.net/woocommerce/advanced-ordernumbers-for-woocommerce.html',
		'class' => 'OpenToolsOrdernumbersBasic'
	);
	$modules[] = array(
		'name' => 'admin_custom_order_fields',
		'title' => __( 'WooCommerce Admin Custom Order Fields', 'woocommerce-exporter' ),
		'description' => __( 'Easily add custom fields to your WooCommerce orders and display them in the Orders admin, the My Orders section and order emails.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-admin-custom-order-fields/',
		'function' => 'init_woocommerce_admin_custom_order_fields'
	);
	$modules[] = array(
		'name' => 'table_rate_shipping_plus',
		'title' => __( 'WooCommerce Table Rate Shipping Plus', 'woocommerce-exporter' ),
		'description' => __( 'Calculate shipping costs based on destination, weight and price.', 'woocommerce-exporter' ),
		'url' => 'http://mangohour.com/plugins/woocommerce-table-rate-shipping',
		'function' => 'mh_wc_table_rate_plus_init'
	);
	$modules[] = array(
		'name' => 'woocommerce-extra-checkout-fields-for-brazil',
		'title' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' ),
		'description' => __( 'Adds Brazilian checkout fields in WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/',
		'slug' => 'woocommerce-extra-checkout-fields-for-brazil',
		'class' => 'Extra_Checkout_Fields_For_Brazil'
	);
	$modules[] = array(
		'name' => 'woocommerce_gravityforms',
		'title' => __( 'WooCommerce Gravity Forms Product Add-Ons', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to use Gravity Forms on individual WooCommerce products.', 'woocommerce-exporter' ),
		'url' => 'https://www.woothemes.com/products/gravity-forms-add-ons/',
		'class' => 'woocommerce_gravityforms'
	);
	$modules[] = array(
		'name' => 'woocommerce_quickdonation',
		'title' => __( 'WooCommerce Quick Donation', 'woocommerce-exporter' ),
		'description' => __( 'Turns WooCommerce into online donation.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-quick-donation/',
		'slug' => 'woocommerce-quick-donation',
		'class' => 'WooCommerce_Quick_Donation'
	);
	$modules[] = array(
		'name' => 'woocommerce_easycheckout',
		'title' => __( 'Easy Checkout Fields Editor', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Easy Checkout Fields Editor', 'woocommerce-exporter' ),
		'url' => 'http://codecanyon.net/item/woocommerce-easy-checkout-field-editor/9799777',
		'function' => 'pcmfe_admin_form_field'
	);
	$modules[] = array(
		'name' => 'woocommerce_productfees',
		'title' => __( 'Product Fees', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Easy Checkout Fields Editor', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-product-fees/',
		'slug' => 'woocommerce-product-fees',
		'class' => 'WooCommerce_Product_Fees'
	);
	$modules[] = array(
		'name' => 'woocommerce_events',
		'title' => __( 'Events', 'woocommerce-exporter' ),
		'description' => __( 'Adds event and ticketing features to WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.woocommerceevents.com/',
		'class' => 'WooCommerce_Events'
	);
	$modules[] = array(
		'name' => 'woocommerce_tabmanager',
		'title' => __( 'Tab Manager', 'woocommerce-exporter' ),
		'description' => __( 'A product tab manager for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-tab-manager/',
		'class' => 'WC_Tab_Manager'
	);
	$modules[] = array(
		'name' => 'woocommerce_customfields',
		'title' => __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ),
		'description' => __( 'Create custom fields for WooCommerce product and checkout pages.', 'woocommerce-exporter' ),
		'url' => 'http://www.rightpress.net/woocommerce-custom-fields',
		'class' => 'RP_WCCF'
	);
	$modules[] = array(
		'name' => 'barcode_isbn',
		'title' => __( 'WooCommerce Barcode & ISBN', 'woocommerce-exporter' ),
		'description' => __( 'A plugin to add a barcode & ISBN to WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-barcode-isbn/',
		'slug' => 'woocommerce-barcode-isbn',
		'function' => 'woo_add_barcode'
	);
	$modules[] = array(
		'name' => 'video_product_tab',
		'title' => __( 'WooCommerce Video Product Tab', 'woocommerce-exporter' ),
		'description' => __( 'Extends WooCommerce to allow you to add a Video to the Product page.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-video-product-tab/',
		'slug' => 'woocommerce-video-product-tab',
		'class' => 'WooCommerce_Video_Product_Tab'
	);
	$modules[] = array(
		'name' => 'external_featured_image',
		'title' => __( 'Nelio External Featured Image', 'woocommerce-exporter' ),
		'description' => __( 'Use external images from anywhere as the featured image of your pages and posts.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/external-featured-image/',
		'slug' => 'external-featured-image', // Define this if the Plugin is hosted on the WordPress repo
		'function' => '_nelioefi_url'
	);
	$modules[] = array(
		'name' => 'variation_swatches_photos',
		'title' => __( 'WooCommerce Variation Swatches and Photos', 'woocommerce-exporter' ),
		'description' => __( 'Configure colors and photos for shoppers on your site to use when picking variations.', 'woocommerce-exporter' ),
		'url' => 'https://www.woothemes.com/products/variation-swatches-and-photos/',
		'class' => 'WC_SwatchesPlugin'
	);
	$modules[] = array(
		'name' => 'uploads',
		'title' => __( 'WooCommerce Uploads', 'woocommerce-exporter' ),
		'description' => __( 'Upload files in WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wpfortune.com/shop/plugins/woocommerce-uploads/',
		'class' => 'WPF_Uploads'
	);

/*
	$modules[] = array(
		'name' => '',
		'title' => __( '', 'woocommerce-exporter' ),
		'description' => __( '', 'woocommerce-exporter' ),
		'url' => '',
		'slug' => '', // Define this if the Plugin is hosted on the WordPress repo
		'function' => '' // Define this for function detection, if Class rename attribute to class
	);
*/

	$modules = apply_filters( 'woo_ce_modules_addons', $modules );

	// Check if the existing Transient exists
	$modules_all = count( $modules );
	$cached = get_transient( WOO_CD_PREFIX . '_modules_all_count' );
	if( $cached == false ) {
		set_transient( WOO_CD_PREFIX . '_modules_all_count', $modules_all, DAY_IN_SECONDS );
	}

	$modules_active = 0;
	$modules_inactive = 0;

	if( !empty( $modules ) ) {
		foreach( $modules as $key => $module ) {
			$modules[$key]['status'] = 'inactive';
			// Check if each module is activated
			if( isset( $module['function'] ) ) {
				if( is_array( $module['function'] ) ) {
					$size = count( $module['function'] );
					for( $i = 0; $i < $size; $i++ ) {
						if( function_exists( $module['function'][$i] ) ) {
							$modules[$key]['status'] = 'active';
							$modules_active++;
							break;
						}
					}
				} else {
					if( function_exists( $module['function'] ) ) {
						$modules[$key]['status'] = 'active';
						$modules_active++;
					}
				}
			} else if( isset( $module['class'] ) ) {
				if( is_array( $module['class'] ) ) {
					$size = count( $module['class'] );
					for( $i = 0; $i < $size; $i++ ) {
						if( function_exists( $module['class'][$i] ) ) {
							$modules[$key]['status'] = 'active';
							$modules_active++;
							break;
						}
					}
				} else {
					if( class_exists( $module['class'] ) ) {
						$modules[$key]['status'] = 'active';
						$modules_active++;
					}
				}
			}
			// Filter Modules by Module Status
			if( !empty( $module_status ) ) {
				switch( $module_status ) {

					case 'active':
						if( $modules[$key]['status'] == 'inactive' )
							unset( $modules[$key] );
						break;

					case 'inactive':
						if( $modules[$key]['status'] == 'active' )
							unset( $modules[$key] );
						break;

				}
			}
			if( isset( $modules[$key] ) ) {
				// Check if the Plugin has a slug and if current user can install Plugins
				if( current_user_can( 'install_plugins' ) && isset( $module['slug'] ) )
					$modules[$key]['url'] = admin_url( sprintf( 'plugin-install.php?tab=search&type=term&s=%s', $module['slug'] ) );
			}
		}
	}

	// Check if the existing Transient exists
	$cached = get_transient( WOO_CD_PREFIX . '_modules_active_count' );
	if( $cached == false ) {
		set_transient( WOO_CD_PREFIX . '_modules_active_count', $modules_active, DAY_IN_SECONDS );
	}

	// Check if the existing Transient exists
	$cached = get_transient( WOO_CD_PREFIX . '_modules_inactive_count' );
	if( $cached == false ) {
		$modules_inactive = $modules_all - $modules_active;
		set_transient( WOO_CD_PREFIX . '_modules_inactive_count', $modules_inactive, DAY_IN_SECONDS );
	}

	return $modules;

}

function woo_ce_modules_status_class( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = 'green';
			break;

		case 'inactive':
			$output = 'yellow';
			break;

	}
	echo $output;

}

function woo_ce_modules_status_label( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = __( 'OK', 'woocommerce-exporter' );
			break;

		case 'inactive':
			$output = __( 'Install', 'woocommerce-exporter' );
			break;

	}
	echo $output;

}

function woo_ce_admin_dashboard_setup() {

	// Check that the User has permission
	if( current_user_can( 'manage_options' ) ) {
		wp_add_dashboard_widget( 'woo_ce_scheduled_export_widget', __( 'Scheduled Exports', 'woocommerce-exporter' ), 'woo_ce_admin_scheduled_export_widget', 'woo_ce_admin_scheduled_export_widget_configure' );
		wp_add_dashboard_widget( 'woo_ce_recent_scheduled_export_widget', __( 'Recent Scheduled Exports', 'woocommerce-exporter' ), 'woo_ce_admin_recent_scheduled_export_widget', 'woo_ce_admin_recent_scheduled_export_widget_configure' );
	}

}
?>