<?php
function woo_ce_admin_scheduled_export_widget() {

	if( $enable_auto = woo_ce_get_option( 'enable_auto', 0 ) ) {

		$next_export = '';
		$next_time = '';

		// Get widget options
		if( !$widget_options = woo_ce_get_option( 'scheduled_export_widget_options', array() ) ) {
			$widget_options = array(
				'number' => 5
			);
		}

		// Loop through each scheduled export, only show Published
		$args = array(
			'post_status' => 'publish'
		);
		if( $scheduled_exports = woo_ce_get_scheduled_exports( $args ) ) {
			$export_times = array();
			foreach( $scheduled_exports as $key => $scheduled_export ) {

				// Only display enabled scheduled exports
				if( get_post_status( $scheduled_export ) <> 'publish' ) {
					unset( $scheduled_exports[$key] );
					continue;
				}

				// Figure out which scheduled export will run next
				if( $next_time == '' ) {
					$next_export = $scheduled_export;
					$next_time = woo_ce_get_next_scheduled_export( $scheduled_export, 'timestamp' );
				} else {
					if( $next_time > woo_ce_get_next_scheduled_export( $scheduled_export, 'timestamp' ) ) {
						$next_export = $scheduled_export;
						$next_time = woo_ce_get_next_scheduled_export( $scheduled_export, 'timestamp' );
					}
				}
				$export_times[$scheduled_export] = $next_time;

			}

			// Sort the scheduled exports by the order of next run
			if( !empty(  $export_times ) ) {
				arsort( $export_times );
				$scheduled_exports = array_keys( $export_times );
			}

			// Check if we need to limit the number of scheduled exports
			$size = count( $scheduled_exports );
			if( $size > $widget_options['number'] ) {
				$i = $size;
				// Loop through the recent exports till we get it down to our limit
				for( $i; $i > $widget_options['number']; $i-- )
					array_pop( $scheduled_exports );
			}
			unset( $next_time );

		}

	}

	if( file_exists( WOO_CD_PATH . 'templates/admin/dashboard_widget-scheduled_export.php' ) ) {
		include_once( WOO_CD_PATH . 'templates/admin/dashboard_widget-scheduled_export.php' );
	} else {
		$message = sprintf( __( 'We couldn\'t load the widget template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-exporter' ), 'dashboard_widget-scheduled_export.php', WOO_CD_PATH . 'templates/admin/...' );

		ob_start(); ?>
<p><strong><?php echo $message; ?></strong></p>
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

function woo_ce_admin_recent_scheduled_export_widget() {

	$recent_exports = woo_ce_get_option( 'recent_scheduled_exports', array() );
	if( empty( $recent_exports ) )
		$recent_exports = array();
	$size = count( $recent_exports );
	$recent_exports = array_reverse( $recent_exports );

	// Get widget options
	if( !$widget_options = woo_ce_get_option( 'recent_scheduled_export_widget_options', array() ) ) {
		$widget_options = array(
			'number' => 5
		);
	}

	// Check if we need to limit the number of recent exports
	if( $size > $widget_options['number'] ) {
		$i = $size;
		// Loop through the recent exports till we get it down to our limit
		for( $i; $i >= $widget_options['number']; $i-- ) {
			unset( $recent_exports[$i] );
		}
		// Save the changes so we don't have to do this next screen refresh
		woo_ce_update_option( 'recent_scheduled_exports', $recent_exports );
	}

	if( file_exists( WOO_CD_PATH . 'templates/admin/dashboard_widget-recent_scheduled_export.php' ) ) {
		include_once( WOO_CD_PATH . 'templates/admin/dashboard_widget-recent_scheduled_export.php' );
	} else {
		$message = sprintf( __( 'We couldn\'t load the widget template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-exporter' ), 'dashboard_widget-recent_scheduled_export.php', WOO_CD_PATH . 'templates/admin/...' );

		ob_start(); ?>
<p><strong><?php echo $message; ?></strong></p>
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

function woo_ce_admin_scheduled_export_widget_configure() {

	// Get widget options
	if( !$widget_options = woo_ce_get_option( 'scheduled_export_widget_options', array() ) ) {
		$widget_options = array(
			'number' => 5
		);
	}

	// Update widget options
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['woo_ce_scheduled_export_widget_post'] ) ) {
		$widget_options = array_map( 'sanitize_text_field', $_POST['woo_ce_scheduled_export'] );
		if( empty( $widget_options['number'] ) )
			$widget_options['number'] = 5;
		update_option( 'woo_ce_scheduled_export_widget_options', $widget_options );
	} ?>
<div>
	<label for="woo_ce_scheduled_export-number"><?php _e( 'Number of scheduled exports', 'woocommerce-exporter' ); ?>:</label><br />
	<input type="text" id="woo_ce_scheduled_export-number" name="woo_ce_scheduled_export[number]" value="<?php echo $widget_options['number']; ?>" />
	<p class="description"><?php _e( 'Control the number of scheduled exports that are shown.', 'woocommerce-exporter' ); ?></p>
</div>
<input name="woo_ce_scheduled_export_widget_post" type="hidden" value="1" />
<?php

}

function woo_ce_admin_recent_scheduled_export_widget_configure() {

	// Get widget options
	if( !$widget_options = woo_ce_get_option( 'recent_scheduled_export_widget_options', array() ) ) {
		$widget_options = array(
			'number' => 5
		);
	}

	// Update widget options
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['woo_ce_recent_scheduled_export_widget_post'] ) ) {
		$widget_options = array_map( 'sanitize_text_field', $_POST['woo_ce_recent_scheduled_export'] );
		if( empty( $widget_options['number'] ) )
			$widget_options['number'] = 5;
		update_option( 'woo_ce_recent_scheduled_export_widget_options', $widget_options );
	} ?>
<div>
	<label for="woo_ce_recent_scheduled_export-number"><?php _e( 'Number of scheduled exports', 'woocommerce-exporter' ); ?>:</label><br />
	<input type="text" id="woo_ce_recent_scheduled_export-number" name="woo_ce_recent_scheduled_export[number]" value="<?php echo $widget_options['number']; ?>" />
	<p class="description"><?php _e( 'Control the number of recent scheduled exports that are shown.', 'woocommerce-exporter' ); ?></p>
</div>
<input name="woo_ce_recent_scheduled_export_widget_post" type="hidden" value="1" />
<?php

}

function woo_ce_scheduled_export_banner( $post ) {

	// Check the Post object exists
	if( isset( $post->post_type ) == false )
		return;

	// Limit to the scheduled_export Post Type
	$post_type = 'scheduled_export';
	if( $post->post_type <> $post_type )
		return;

	if( apply_filters( 'woo_ce_scheduled_export_banner_save_prompt', true ) )
		echo '<a href="' . esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'scheduled_export' ), 'admin.php' ) ) . '" class="button confirm-button" data-confirm="' . __( 'The changes you made will be lost if you navigate away from this page before saving.', 'woocommerce-exporter' ) . '">' . __( 'Return to Scheduled Exports', 'woocommerce-exporter' ) . '</a>';
	else
		echo '<a href="' . esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'scheduled_export' ), 'admin.php' ) ) . '" class="button">' . __( 'Return to Scheduled Exports', 'woocommerce-exporter' ) . '</a>';

}

function woo_ce_scheduled_export_filters_meta_box() {

	global $post;

	$post_ID = ( $post ? $post->ID : 0 );

	// Check if the Enabled scheduled export option is disabled
	if( !woo_ce_get_option( 'enable_auto', 0 ) ) {
		$override_url = esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'scheduled_export', 'action' => 'enable_scheduled_exports', '_wpnonce' => wp_create_nonce( 'woo_ce_enable_scheduled_exports' ) ), 'admin.php' ) );
		$message = sprintf( __( 'Scheduled exports are turned off from the <em>Enable scheduled exports</em> option on the Settings tab, to enable scheduled exports globally <a href="%s">click here</a>.', 'woocommerce-exporter' ), $override_url );
		woo_cd_admin_notice_html( $message, 'error' );
	}

	// General
	add_action( 'woo_ce_before_scheduled_export_general_options', 'woo_ce_scheduled_export_general_export_type' );
	add_action( 'woo_ce_before_scheduled_export_general_options', 'woo_ce_scheduled_export_general_export_format' );
	add_action( 'woo_ce_before_scheduled_export_general_options', 'woo_ce_scheduled_export_general_export_method' );
	add_action( 'woo_ce_before_scheduled_export_general_options', 'woo_ce_scheduled_export_general_export_fields' );
	// Filters
	add_action( 'woo_ce_before_scheduled_export_type_options', 'woo_ce_scheduled_export_filters_product' );
	add_action( 'woo_ce_before_scheduled_export_type_options', 'woo_ce_scheduled_export_filters_order' );
	add_action( 'woo_ce_before_scheduled_export_type_options', 'woo_ce_scheduled_export_filters_user' );
	// Method
	add_action( 'woo_ce_before_scheduled_export_method_options', 'woo_ce_scheduled_export_method_save' );
	add_action( 'woo_ce_before_scheduled_export_method_options', 'woo_ce_scheduled_export_method_email' );
	add_action( 'woo_ce_before_scheduled_export_method_options', 'woo_ce_scheduled_export_method_post' );
	add_action( 'woo_ce_before_scheduled_export_method_options', 'woo_ce_scheduled_export_method_ftp' );
	// Scheduling
	add_action( 'woo_ce_before_scheduled_export_frequency_options', 'woo_ce_scheduled_export_frequency_schedule' );
	add_action( 'woo_ce_before_scheduled_export_frequency_options', 'woo_ce_scheduled_export_frequency_commence' );

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

?>
<div id="scheduled_export_options" class="panel-wrap scheduled_export_data">
	<div class="wc-tabs-back"></div>
	<ul class="coupon_data_tabs wc-tabs" style="display:none;">
<?php
	$coupon_data_tabs = apply_filters( 'woo_ce_scheduled_export_data_tabs', array(
		'general' => array(
			'label'  => __( 'General', 'woocommerce' ),
			'target' => 'general_coupon_data',
			'class'  => 'general_coupon_data',
		),
		'filters' => array(
			'label'  => __( 'Filters', 'woocommerce' ),
			'target' => 'usage_restriction_coupon_data',
			'class'  => '',
		),
		'method' => array(
			'label'  => __( 'Method', 'woocommerce' ),
			'target' => 'method_coupon_data',
			'class'  => '',
		),
		'scheduling' => array(
			'label'  => __( 'Scheduling', 'woocommerce' ),
			'target' => 'scheduling_coupon_data',
			'class'  => '',
		)
	) );

	foreach ( $coupon_data_tabs as $key => $tab ) { ?>
		<li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
			<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
		</li><?php
	} ?>
	</ul>
	<?php do_action( 'woo_ce_before_scheduled_export_options', $post_ID ); ?>
	<div id="general_coupon_data" class="panel woocommerce_options_panel export_general_options">
		<?php do_action( 'woo_ce_before_scheduled_export_general_options', $post_ID ); ?>
		<?php do_action( 'woo_ce_after_scheduled_export_general_options', $post_ID ); ?>
	</div>
	<!-- #general_coupon_data -->

	<div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel export_type_options">
		<?php do_action( 'woo_ce_before_scheduled_export_type_options', $post_ID ); ?>
		<div class="export-options category-options tag-options brand-options customer-options review-options coupon-options subscription-options product_vendor-options commission-options shipping_class-options">
			<p><?php _e( 'No filter options are available for this export type.', 'woocommerce-exporter' ); ?></p>
		</div>
		<?php do_action( 'woo_ce_after_scheduled_export_type_options', $post_ID ); ?>
	</div>
	<!-- #usage_restriction_coupon_data -->

	<div id="method_coupon_data" class="panel woocommerce_options_panel export_method_options">
		<?php do_action( 'woo_ce_before_scheduled_export_method_options', $post_ID ); ?>
		<div class="export-options archive-options">
			<p><?php _e( 'No export method options are available for this export method.', 'woocommerce-exporter' ); ?></p>
		</div>
		<?php do_action( 'woo_ce_after_scheduled_export_method_options', $post_ID ); ?>
	</div>
	<!-- #method_coupon_data -->

	<div id="scheduling_coupon_data" class="panel woocommerce_options_panel export_frequency_options">
		<?php do_action( 'woo_ce_before_scheduled_export_frequency_options', $post_ID ); ?>
		<?php do_action( 'woo_ce_after_scheduled_export_frequency_options', $post_ID ); ?>
	</div>
	<!-- #scheduling_coupon_data -->
	<?php do_action( 'woo_ce_after_scheduled_export_options', $post_ID ); ?>
	<div class="clear"></div>
</div>
<!-- #scheduled_export_options -->
<?php
	wp_nonce_field( 'scheduled_export', 'woo_ce_export' );

}

function woo_ce_scheduled_export_general_export_type( $post_ID = 0 ) {

	$export_type = get_post_meta( $post_ID, '_export_type', true );
	$export_types = woo_ce_get_export_types();

	ob_start(); ?>
<div class="options_group">
	<p class="form-field discount_type_field ">
		<label for="export_type"><?php _e( 'Export type', 'woocommerce-exporter' ); ?> </label>
<?php if( !empty( $export_types ) ) { ?>
		<select id="export_type" name="export_type" class="select short">
	<?php foreach( $export_types as $key => $type ) { ?>
			<option value="<?php echo $key; ?>"<?php selected( $export_type, $key ); ?>><?php echo $type; ?></option>
	<?php } ?>
		</select>
		<img class="help_tip" data-tip="<?php _e( 'Select the export type you want to export.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
		<?php _e( 'No export types were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
	</p>
</div>
<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_general_export_format( $post_ID = 0 ) {

	$export_format = get_post_meta( $post_ID, '_export_format', true );

	ob_start(); ?>
<div class="options_group">
	<p class="form-field discount_type_field ">
		<label for="export_format"><?php _e( 'Export format', 'woocommerce-exporter' ); ?> </label>
		<select id="export_format" name="export_format" class="select short">
			<option value="csv"<?php selected( $export_format, 'csv' ); ?>><?php _e( 'CSV', 'woocommerce-exporter' ); ?> - <?php _e( 'Comma Separated Values', 'woocommerce-exporter' ); ?></label></li>
			<option value="tsv"<?php selected( $export_format, 'tsv' ); ?>><?php _e( 'TSV', 'woocommerce-exporter' ); ?> - <?php _e( 'Tab Separated Values', 'woocommerce-exporter' ); ?></label></li>
			<option value="xls"<?php selected( $export_format, 'xls' ); ?>><?php _e( 'Excel XLS', 'woocommerce-exporter' ); ?> - <?php _e( 'Excel 97-2003', 'woocommerce-exporter' ); ?></label></li>
			<option value="xlsx"<?php selected( $export_format, 'xlsx' ); ?>><?php _e( 'Excel XLSX', 'woocommerce-exporter' ); ?> - <?php _e( 'Excel 2007-2013', 'woocommerce-exporter' ); ?></label></li>
			<option value="xml"<?php selected( $export_format, 'xml' ); ?>><?php _e( 'XML', 'woocommerce-exporter' ); ?> - <?php _e( 'EXtensible Markup Language', 'woocommerce-exporter' ); ?></label></li>
			<option value="rss"<?php selected( $export_format, 'rss' ); ?>><?php _e( 'RSS', 'woocommerce-exporter' ); ?> - <?php _e( 'RSS 2.0 format', 'woocommerce-exporter' ); ?></label></li>
		</select>
		<img class="help_tip" data-tip="<?php _e( 'Adjust the export format to generate different export file formats. Default is CSV.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
	</p>
</div>

<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_general_export_method( $post_ID = 0 ) {

	$export_method = get_post_meta( $post_ID, '_export_method', true );

	ob_start(); ?>
<div class="options_group">
	<p class="form-field discount_type_field ">
		<label for="export_method"><?php _e( 'Export method', 'woocommerce-exporter' ); ?> </label>
		<select id="export_method" name="export_method" class="select short">
			<option value="archive"<?php selected( $export_method, 'archive' ); ?>><?php echo woo_ce_format_export_method( 'archive' ); ?></option>
			<option value="save"<?php selected( $export_method, 'save' ); ?>><?php echo woo_ce_format_export_method( 'save' ); ?></option>
			<option value="email"<?php selected( $export_method, 'email' ); ?>><?php echo woo_ce_format_export_method( 'email' ); ?></option>
			<option value="post"<?php selected( $export_method, 'post' ); ?>><?php echo woo_ce_format_export_method( 'post' ); ?></option>
			<option value="ftp"<?php selected( $export_method, 'ftp' ); ?>><?php echo woo_ce_format_export_method( 'ftp' 	); ?></option>
		</select>
		<img class="help_tip" data-tip="<?php _e( 'Choose what Store Exporter Deluxe does with the generated export. Default is to archive the export to the WordPress Media for archival purposes.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
	</p>
</div>

<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_general_export_fields( $post_ID = 0 ) {

	$export_fields = get_post_meta( $post_ID, '_export_fields', true );

	ob_start(); ?>
<div class="options_group">
	<p class="form-field discount_type_field">
		<label for="export_fields"><?php _e( 'Export fields', 'woocommerce-exporter' ); ?></label>
		<input type="radio" name="export_fields" value="all"<?php checked( in_array( $export_fields, array( false, 'all' ) ), true ); ?> />&nbsp;<?php _e( 'Include all Export Fields for the requested Export Type', 'woocommerce-exporter' ); ?><br />
		<input type="radio" name="export_fields" value="saved"<?php checked( $export_fields, 'saved' ); ?> />&nbsp;<?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woocommerce-exporter' ); ?>
	</p>
	<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woocommerce-exporter' ); ?></p>
</div>

<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_filters_product( $post_ID = 0 ) {

	$args = array(
		'hide_empty' => 1
	);
	$product_categories = woo_ce_get_product_categories( $args );
	$product_filter_category = get_post_meta( $post_ID, '_filter_product_category', true );
	$product_tags = woo_ce_get_product_tags( $args );
	$product_filter_tag = get_post_meta( $post_ID, '_filter_product_tag', true );
	// Check if the Product Brands taxonomy exists
	if( woo_ce_detect_product_brands() ) {
		$args = array(
			'hide_empty' => 1,
			'orderby' => 'term_group'
		);
		$product_brands = woo_ce_get_product_brands( $args );
		$product_filter_brand = get_post_meta( $post_ID, '_filter_product_brand', true );
	}
	$product_statuses = get_post_statuses();
	// Add Trash if it not included in the list
	if( !isset( $product_statuses['trash'] ) )
		$product_statuses['trash'] = __( 'Trash', 'woocommerce-exporter' );
	$product_filter_status = get_post_meta( $post_ID, '_filter_product_status', true );
	$product_types = woo_ce_get_product_types();
	$product_filter_type = get_post_meta( $post_ID, '_filter_product_type', true );
	$products = woo_ce_get_products();
	$product_filter_sku = get_post_meta( $post_ID, '_filter_product_sku', true );
	$shipping_classes = woo_ce_get_shipping_classes();
	$product_filter_shipping_class = get_post_meta( $post_ID, '_filter_product_shipping_class', true );
	$product_filter_stock = get_post_meta( $post_ID, '_filter_product_stock', true );
	$product_filter_featured = get_post_meta( $post_ID, '_filter_product_featured', true );

	ob_start(); ?>
		<div class="export-options product-options">

			<div class="options_group">
				<p class="form-field discount_type_field">
					<label for="product_filter_category"><?php _e( 'Product category', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $product_categories ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woocommerce-exporter' ); ?>" name="product_filter_category[]" multiple class="chzn-select">
	<?php foreach( $product_categories as $product_category ) { ?>
						<option value="<?php echo $product_category->term_id; ?>"<?php selected( ( !empty( $product_filter_category ) ? in_array( $product_category->term_id, $product_filter_category ) : false ), true ); ?><?php disabled( $product_category->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
					</select>
					<img class="help_tip" data-tip="<?php _e( 'Select the Product Category\'s you want to filter exported Products by. Default is to include all Product Categories.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
					<?php _e( 'No Product Categories were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
				</p>

				<p class="form-field discount_type_field">
					<label for="product_filter_tag"><?php _e( 'Product tag', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $product_tags ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woocommerce-exporter' ); ?>" name="product_filter_tag[]" multiple class="chzn-select select short" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
						<option value="<?php echo $product_tag->term_id; ?>"<?php selected( ( !empty( $product_filter_tag ) ? in_array( $product_tag->term_id, $product_filter_tag ) : false ), true ); ?><?php disabled( $product_category->count, 0 ); ?>><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
					</select>
					<img class="help_tip" data-tip="<?php _e( 'Select the Product Tag\'s you want to filter exported Products by. Default is to include all Product Tags.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
					<?php _e( 'No Product Tags were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
				</p>

<?php if( woo_ce_detect_product_brands() ) { ?>
				<p class="form-field discount_type_field">
					<label for="product_filter_brand"><?php _e( 'Product brand', 'woocommerce-exporter' ); ?></label>
	<?php if( !empty( $product_brands ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Product Brand...', 'woocommerce-exporter' ); ?>" name="product_filter_brand[]" multiple class="chzn-select select short" style="width:95%;">
		<?php foreach( $product_brands as $product_brand ) { ?>
						<option value="<?php echo $product_brand->term_id; ?>"<?php selected( ( !empty( $product_filter_brand ) ? in_array( $product_brand->term_id, $product_filter_brand ) : false ), true ); ?><?php disabled( $product_category->count, 0 ); ?>><?php echo $product_brand->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_brand->term_id ); ?>)</option>
		<?php } ?>
					</select>
					<img class="help_tip" data-tip="<?php _e( 'Select the Product Brand\'s you want to filter exported Products by. Default is to include all Product Brands.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
	<?php } else { ?>
					<?php _e( 'No Product Brands were found.', 'woocommerce-exporter' ); ?>
	<?php } ?>
				</p>
<?php } ?>

				<p class="form-field discount_type_field">
					<label for="product_filter_status"><?php _e( 'Product status', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $product_statuses ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Product Status...', 'woocommerce-exporter' ); ?>" name="product_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_statuses as $key => $product_status ) { ?>
						<option value="<?php echo $key; ?>"<?php selected( ( !empty( $product_filter_status ) ? in_array( $key, $product_filter_status ) : false ), true ); ?>><?php echo $product_status; ?></option>
	<?php } ?>
					</select>
					<img class="help_tip" data-tip="<?php _e( 'Select the Product Status\'s you want to filter exported Products by. Default is to include all Product Status\'s.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No Product Status were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
				</p>

				<p class="form-field discount_type_field">
					<label for="product_filter_type"><?php _e( 'Product type', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $product_types ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Product Type...', 'woocommerce-exporter' ); ?>" name="product_filter_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
						<option value="<?php echo $key; ?>"<?php selected( ( !empty( $product_filter_type ) ? in_array( $key, $product_filter_type ) : false ), true ); ?>><?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)</option>
	<?php } ?>
					</select>
					<img class="help_tip" data-tip="<?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types except Variations.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
					<?php _e( 'No Product Types were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
				</p>

				<p class="form-field discount_type_field">
					<label for="product_filter_sku"><?php _e( 'Product', 'woocommerce-exporter' ); ?></label>
<?php if( wp_script_is( 'wc-enhanced-select', 'enqueued' ) ) { ?>
					<input type="hidden" id="product_filter_sku" name="product_filter_sku[]" class="multiselect wc-product-search" data-multiple="true" style="width:95%;" data-placeholder="<?php _e( 'Search for a Product&hellip;', 'woocommerce-exporter' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
<?php
	$json_ids = array();
?>
<?php if( !empty( $product_filter_sku ) ) { ?>
<?php
	foreach( $product_filter_sku as $product_id ) {
		$product = wc_get_product( $product_id );
		if( is_object( $product ) ) {
			$json_ids[$product_id] = wp_kses_post( $product->get_formatted_name() );
		}
	}
	echo esc_attr( json_encode( $json_ids ) ); ?>
<?php } ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
<?php } else { ?>
<?php
	add_filter( 'the_title', 'woo_ce_get_product_title_sku', 10, 2 );
?>
	<?php if( !empty( $products ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Product...', 'woocommerce-exporter' ); ?>" name="product_filter_sku[]" multiple class="chzn-select" style="width:95%;">
		<?php foreach( $products as $product ) { ?>
						<option value="<?php echo $product; ?>"<?php selected( ( !empty( $product_filter_sku ) ? in_array( $product, $product_filter_sku ) : false ), true ); ?>><?php echo woo_ce_format_post_title( get_the_title( $product ) ); ?></option>
		<?php } ?>
					</select>
	<?php } else { ?>
					<?php _e( 'No Products were found.', 'woocommerce-exporter' ); ?>
	<?php } ?>
<?php
	remove_filter( 'the_title', 'woo_ce_get_product_title_sku' );
?>
<?php } ?>
					<img class="help_tip" data-tip="<?php _e( 'Select the Product\'s you want to filter exported Products by. Default is to include all Products.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
				</p>

				<p class="form-field discount_type_field">
					<label for="product_filter_shipping_class"><?php _e( 'Shipping class', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $shipping_classes ) ) { ?>
					<select data-placeholder="<?php _e( 'Choose a Shipping Class...', 'woocommerce-exporter' ); ?>" name="product_filter_shipping_class[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $shipping_classes as $shipping_class ) { ?>
						<option value="<?php echo $shipping_class->term_id; ?>"<?php selected( ( !empty( $product_filter_shipping_class ) ? in_array( $shipping_class->term_id, $product_filter_shipping_class ) : false ), true ); ?><?php disabled( $shipping_class->count, 0 ); ?>><?php echo $shipping_class->name; ?> (<?php echo $shipping_class->count; ?>)</option>
	<?php } ?>
					</select>
					<img class="help_tip" data-tip="<?php _e( 'Select the Shipping Class\'s you want to filter exported Products by. Default is to include all Shipping Classes.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
					<?php _e( 'No Shipping Classes were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
				</p>
			</div>

			<div class="options_group">
				<p class="form-field discount_type_field">
					<label for="product_filter_stock"><?php _e( 'Stock status', 'woocommerce-exporter' ); ?></label>
					<input type="radio" name="product_filter_stock" value=""<?php checked( $product_filter_stock, false ); ?> />&nbsp;<?php _e( 'Include both', 'woocommerce-exporter' ); ?><br />
					<input type="radio" name="product_filter_stock" value="instock"<?php checked( $product_filter_stock, 'instock' ); ?> />&nbsp;<?php _e( 'In stock', 'woocommerce-exporter' ); ?><br />
					<input type="radio" name="product_filter_stock" value="outofstock"<?php checked( $product_filter_stock, 'outofstock' ); ?> />&nbsp;<?php _e( 'Out of stock', 'woocommerce-exporter' ); ?>
				</p>
			</div>

			<div class="options_group">
				<p class="form-field discount_type_field">
					<label for="product_filter_featured"><?php _e( 'Featured', 'woocommerce-exporter' ); ?></label>
					<input type="radio" name="product_filter_featured" value=""<?php checked( $product_filter_featured, false ); ?> />&nbsp;<?php _e( 'Include both', 'woocommerce-exporter' ); ?><br />
					<input type="radio" name="product_filter_featured" value="yes"<?php checked( $product_filter_featured, 'yes' ); ?> />&nbsp;<?php _e( 'Featured', 'woocommerce-exporter' ); ?><br />
					<input type="radio" name="product_filter_featured" value="no"<?php checked( $product_filter_featured, 'no' ); ?> />&nbsp;<?php _e( 'Un-featured', 'woocommerce-exporter' ); ?>
				</p>
			</div>

		</div>
		<!-- .product-options -->

<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_filters_order( $post_ID = 0 ) {

	$order_statuses = woo_ce_get_order_statuses();
	$order_filter_status = get_post_meta( $post_ID, '_filter_order_status', true );
	if( empty( $order_filter_status ) )
		$order_filter_status = array();
	$products = woo_ce_get_products();
	$order_filter_product = get_post_meta( $post_ID, '_filter_order_product', true );
	$countries = woo_ce_allowed_countries();
	$order_filter_billing_country = get_post_meta( $post_ID, '_filter_order_billing_country', true );
	$order_filter_shipping_country = get_post_meta( $post_ID, '_filter_order_shipping_country', true );
	$payment_gateways = woo_ce_get_order_payment_gateways();
	$order_filter_payment = get_post_meta( $post_ID, '_filter_order_payment', true );
	$shipping_methods = woo_ce_get_order_shipping_methods();
	$order_filter_shipping = get_post_meta( $post_ID, '_filter_order_shipping', true );
	$order_filter_date = get_post_meta( $post_ID, '_filter_order_date', true );
	$order_filter_dates_from = get_post_meta( $post_ID, '_filter_order_dates_from', true );
	$order_filter_dates_to = get_post_meta( $post_ID, '_filter_order_dates_to', true );
	$order_filter_date_variable = get_post_meta( $post_ID, '_filter_order_date_variable', true );
	$order_filter_date_variable_length = get_post_meta( $post_ID, '_filter_order_date_variable_length', true );

	ob_start(); ?>
<div class="export-options order-options">

	<div class="options_group">
		<p class="form-field discount_type_field">
			<label for="order_filter_status"><?php _e( 'Order status', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $order_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Order Status...', 'woocommerce-exporter' ); ?>" name="order_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $order_statuses as $order_status ) { ?>
				<option value="<?php echo $order_status->slug; ?>"<?php selected( ( !empty( $order_filter_status ) ? in_array( $order_status->slug, $order_filter_status ) : false ), true ); ?>><?php echo ucfirst( $order_status->name ); ?> (<?php echo $order_status->count; ?>)</option>
	<?php } ?>
			</select>
			<img class="help_tip" data-tip="<?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No Order Status were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</p>

		<p class="form-field discount_type_field">
			<label for="order_filter_product"><?php _e( 'Product', 'woocommerce-exporter' ); ?></label>
<?php if( wp_script_is( 'wc-enhanced-select', 'enqueued' ) ) { ?>
			<input type="hidden" id="order_filter_product" name="order_filter_product[]" class="multiselect wc-product-search" data-multiple="true" style="width:95%;" data-placeholder="<?php _e( 'Search for a Product&hellip;', 'woocommerce-exporter' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
<?php
	$json_ids = array();
?>
<?php if( !empty( $order_filter_product ) ) { ?>
<?php
	foreach( $order_filter_product as $product_id ) {
		$product = wc_get_product( $product_id );
		if( is_object( $product ) ) {
			$json_ids[$product_id] = wp_kses_post( $product->get_formatted_name() );
		}
	}
	echo esc_attr( json_encode( $json_ids ) ); ?>
<?php } ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
<?php } else { ?>
<?php
	add_filter( 'the_title', 'woo_ce_get_product_title_sku', 10, 2 );
?>
	<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product...', 'woocommerce-exporter' ); ?>" name="order_filter_product[]" multiple class="chzn-select" style="width:95%;">
		<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>"<?php selected( ( !empty( $order_filter_product ) ? in_array( $product, $order_filter_product ) : false ), true ); ?>><?php echo woo_ce_format_post_title( get_the_title( $product ) ); ?></option>
		<?php } ?>
			</select>
	<?php } else { ?>
			<?php _e( 'No Products were found.', 'woocommerce-exporter' ); ?>
	<?php } ?>
<?php
	remove_filter( 'the_title', 'woo_ce_get_product_title_sku' );
?>
<?php } ?>
			<img class="help_tip" data-tip="<?php _e( 'Select the Products you want to filter exported Orders by. Default is to include all Products.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</p>

		<p class="form-field discount_type_field">
			<label for="order_filter_billing_country"><?php _e( 'Billing country', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $countries ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Billing Country...', 'woocommerce-exporter' ); ?>" id="order_filter_billing_country" name="order_filter_billing_country[]" multiple class="chzn-select" style="width:95%;">
				<option value=""><?php _e( 'Show all Countries', 'woocommerce-exporter' ); ?></option>
	<?php foreach( $countries as $country_prefix => $country ) { ?>
				<option value="<?php echo $country_prefix; ?>"<?php selected( ( !empty( $order_filter_billing_country ) ? in_array( $country_prefix, $order_filter_billing_country ) : false ), true ); ?>><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
	<?php } ?>
			</select>
			<img class="help_tip" data-tip="<?php _e( 'Filter Orders by Billing Country to be included in the export. Default is to include all Countries.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No Countries were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</p>

		<p class="form-field discount_type_field">
			<label for="order_filter_shipping_country"><?php _e( 'Shipping country', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $countries ) ) { ?>
			<select id="order_filter_shipping_country" data-placeholder="<?php _e( 'Choose a Shipping Country...', 'woocommerce-exporter' ); ?>" name="order_filter_shipping_country[]" multiple class="chzn-select" style="width:95%;">
				<option value=""><?php _e( 'Show all Countries', 'woocommerce-exporter' ); ?></option>
	<?php foreach( $countries as $country_prefix => $country ) { ?>
				<option value="<?php echo $country_prefix; ?>"<?php selected( ( !empty( $order_filter_shipping_country ) ? in_array( $country_prefix, $order_filter_shipping_country ) : false ), true ); ?>><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
	<?php } ?>
			</select>
			<img class="help_tip" data-tip="<?php _e( 'Filter Orders by Shipping Country to be included in the export. Default is to include all Countries.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No Countries were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</p>

		<p class="form-field discount_type_field">
			<label for="order_filter_payment"><?php _e( 'Payment gateway', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $payment_gateways ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Payment Gateway...', 'woocommerce-exporter' ); ?>" name="order_filter_payment[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $payment_gateways as $payment_gateway ) { ?>
				<option value="<?php echo $payment_gateway->id; ?>"<?php selected( ( !empty( $order_filter_payment ) ? in_array( $payment_gateway->id, $order_filter_payment ) : false ), true ); ?>><?php echo ucfirst( woo_ce_format_order_payment_gateway( $payment_gateway->id ) ); ?></option>
	<?php } ?>
			</select>
			<img class="help_tip" data-tip="<?php _e( 'Select the Payment Gateways you want to filter exported Orders by. Default is to include all Payment Gateways.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No Payment Gateways were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</p>

		<p class="form-field discount_type_field">
			<label for="product_filter_shipping"><?php _e( 'Shipping method', 'woocommerce-exporter' ); ?></label>
<?php if( !empty( $shipping_methods ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Shipping Method...', 'woocommerce-exporter' ); ?>" name="order_filter_shipping[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $shipping_methods as $shipping_method ) { ?>
				<option value="<?php echo $shipping_method->id; ?>"<?php selected( ( !empty( $order_filter_shipping ) ? in_array( $shipping_method->id, $order_filter_shipping ) : false ), true ); ?>><?php echo ucfirst( woo_ce_format_order_shipping_method( $shipping_method->id ) ); ?></option>
	<?php } ?>
			</select>
			<img class="help_tip" data-tip="<?php _e( 'Select the Shipping Methods you want to filter exported Orders by. Default is to include all Shipping Methods.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No Shipping Methods were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</p>
	</div>

	<div class="options_group">
		<p class="form-field discount_type_field">
			<label for="order_dates_filter"><?php _e( 'Order date', 'woocommerce-exporter' ); ?></label>
			<input type="radio" name="order_dates_filter" value=""<?php checked( $order_filter_date, false ); ?> />&nbsp;<?php _e( 'All', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="today"<?php checked( $order_filter_date, 'today' ); ?> />&nbsp;<?php _e( 'Today', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="yesterday"<?php checked( $order_filter_date, 'yesterday' ); ?> />&nbsp;<?php _e( 'Yesterday', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="current_week"<?php checked( $order_filter_date, 'current_week' ); ?> />&nbsp;<?php _e( 'Current week', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="last_week"<?php checked( $order_filter_date, 'last_week' ); ?> />&nbsp;<?php _e( 'Last week', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="current_month"<?php checked( $order_filter_date, 'current_month' ); ?> />&nbsp;<?php _e( 'Current month', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="last_month"<?php checked( $order_filter_date, 'last_month' ); ?> />&nbsp;<?php _e( 'Last month', 'woocommerce-exporter' ); ?><br />
			<input type="radio" name="order_dates_filter" value="variable"<?php checked( $order_filter_date, 'variable' ); ?> />&nbsp;<?php _e( 'Variable date', 'woocommerce-exporter' ); ?><br />
			<span style="float:left; margin-right:6px;"><?php _e( 'Last', 'woocommerce-exporter' ); ?></span>
			<input type="text" name="order_dates_filter_variable" class="sized" size="4" value="<?php echo $order_filter_date_variable; ?>" />
			<select name="order_dates_filter_variable_length">
				<option value=""<?php selected( $order_filter_date_variable_length, '' ); ?>>&nbsp;</option>
				<option value="second"<?php selected( $order_filter_date_variable_length, 'second' ); ?>><?php _e( 'second(s)', 'woocommerce-exporter' ); ?></option>
				<option value="minute"<?php selected( $order_filter_date_variable_length, 'minute' ); ?>><?php _e( 'minute(s)', 'woocommerce-exporter' ); ?></option>
				<option value="hour"<?php selected( $order_filter_date_variable_length, 'hour' ); ?>><?php _e( 'hour(s)', 'woocommerce-exporter' ); ?></option>
				<option value="day"<?php selected( $order_filter_date_variable_length, 'day' ); ?>><?php _e( 'day(s)', 'woocommerce-exporter' ); ?></option>
				<option value="week"<?php selected( $order_filter_date_variable_length, 'week' ); ?>><?php _e( 'week(s)', 'woocommerce-exporter' ); ?></option>
				<option value="month"<?php selected( $order_filter_date_variable_length, 'month' ); ?>><?php _e( 'month(s)', 'woocommerce-exporter' ); ?></option>
				<option value="year"<?php selected( $order_filter_date_variable_length, 'year' ); ?>><?php _e( 'year(s)', 'woocommerce-exporter' ); ?></option>
			</select><br class="clear" />
			<input type="radio" name="order_dates_filter" value="manual"<?php checked( $order_filter_date, 'manual' ); ?> />&nbsp;<?php _e( 'Fixed date', 'woocommerce-exporter' ); ?><br />
			<input type="text" name="order_dates_from" value="<?php echo $order_filter_dates_from; ?>" size="10" maxlength="10" class="sized datepicker" /> <span style="float:left; margin-right:6px;"><?php _e( 'to', 'woocommerce-exporter' ); ?></span> <input type="text" name="order_dates_to" value="<?php echo $order_filter_dates_to; ?>" size="10" maxlength="10" class="sized datepicker" /><br class="clear" />
			<input type="radio" name="order_dates_filter" value="last_export"<?php checked( $order_filter_date, 'last_export' ); ?> />&nbsp;<?php _e( 'Since last export', 'woocommerce-exporter' ); ?>
			<img class="help_tip" data-tip="<?php _e( 'Export Orders which have not previously been included in an export. Decided by whether the <code>_woo_cd_exported</code> custom Post meta key has not been assigned to an Order.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</p>
	</div>

</div>
<!-- .order-options -->

<?php
	ob_end_flush();

}

function woo_ce_scheduled_export_filters_user( $post_ID = 0 ) {

	$user_roles = woo_ce_get_user_roles();
	$user_filter_role = get_post_meta( $post_ID, '_filter_user_role', true );

	ob_start(); ?>
<div class="export-options user-options">

	<div class="options_group">
		<p class="form-field discount_type_field">
			<label for="order_filter_payment"><?php _e( 'User role', 'woocommerce-exporter' ); ?></label>

<?php if( !empty( $user_roles ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a User Role...', 'woocommerce-exporter' ); ?>" name="user_filter_role[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $user_roles as $key => $user_role ) { ?>
				<option value="<?php echo $key; ?>"<?php selected( ( !empty( $user_filter_role ) ? in_array( $key, $user_filter_role ) : false ), true ); ?>><?php echo ucfirst( $user_role['name'] ); ?> (<?php echo $user_role['count']; ?>)</option>
	<?php } ?>
			</select>
			<img class="help_tip" data-tip="<?php _e( 'Select the User Roles you want to filter exported Users by. Default is to include all User Roles.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } else { ?>
			<?php _e( 'No User Roles were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</p>
	</div>

</div>
<!-- .user-options -->

<?php
	ob_end_flush();

}

function woo_ce_scheduled_export_method_save( $post_ID = 0 ) {

	$save_path = get_post_meta( $post_ID, '_method_save_path', true );
	$save_filename = get_post_meta( $post_ID, '_method_save_filename', true );

	ob_start(); ?>
<div class="export-options save-options">

	<div class="options_group">
		<p class="form-field discount_type_field">
			<label for="save_method_file_path"><?php _e( 'File path', 'woocommerce-exporter' ); ?></label> <code><?php echo get_home_path(); ?></code> <input type="text" id="save_method_file_path" name="save_method_path" size="25" class="short code" value="<?php echo sanitize_text_field( $save_path ); ?>" style="float:none;" />
			<img class="help_tip" data-tip="<?php _e( 'Do not provide the filename within File path as it will be generated for you or rely on the fixed filename entered below.<br /><br />For file path example: <code>wp-content/uploads/exports/</code>', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</p>
	</div>
	<div class="options_group">
		<p class="form-field discount_type_field">
			<label for="save_method_filename"><?php _e( 'Fixed filename', 'woocommerce-exporter' ); ?></label> <input type="text" id="save_method_filename" name="save_method_filename" size="25" class="short code" value="<?php echo esc_attr( $save_filename ); ?>" />
			<img class="help_tip" data-tip="<?php _e( 'The export filename can be set within the Fixed filename field otherwise it defaults to the Export filename provided within General Settings above.<br /><br />Tags can be used: ', 'woocommerce-exporter' ); ?> <code>%dataset%</code>, <code>%date%</code>, <code>%time%</code>, <code>%random</code>, <code>%store_name%</code>." src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</p>
	</div>

</div>
<!-- .save-options -->

<?php
	ob_end_flush();

}

function woo_ce_scheduled_export_method_email( $post_ID = 0 ) {

	ob_start(); ?>
<div class="export-options email-options">

<?php
echo '<div class="options_group">';
woocommerce_wp_text_input(
	array(
		'id' => '_method_email_to', 
		'label' => __( 'E-mail recipient', 'woocommerce' ), 
		'desc_tip' => 'true', 
		'description' => __( 'Set the recipient of scheduled export e-mails, multiple recipients can be added using the <code><attr title="comma">,</attr></code> separator.<br /><br />Default is the Blog Administrator e-mail address set on the WordPress &raquo; Settings screen.', 'woocommerce-exporter' ), 
		'placeholder' => 'big.bird@sesamestreet.org,oscar@sesamestreet.org' 
	) 
);
echo '</div><div class="options_group">';
woocommerce_wp_text_input( 
	array( 
		'id' => '_method_email_subject', 
		'label' => __( 'E-mail subject', 'woocommerce' ), 
		'desc_tip' => 'true', 
		'description' => __( 'Set the subject of scheduled export e-mails.<br /><br />Tags can be used: <code>%store_name%</code>, <code>%export_type%</code>, <code>%export_filename%</code>.', 'woocommerce-exporter' ), 
		'placeholder' => __( 'Daily Product stock levels', 'woocommerce-exporter' ) 
	) 
);
echo '</div><div class="options_group">';
woocommerce_wp_textarea_input( 
	array( 
		'id' => '_method_email_contents', 
		'label' => __( 'E-mail contents', 'woocommerce-exporter' ), 
		'desc_tip' => 'true', 
		'description' => __( 'Set the e-mail contents of scheduled export e-mails.<br /><br />Tags can be used: <code>%store_name%</code>, <code>%export_type%</code>, <code>%export_filename%</code>.', 'woocommerce-exporter' ), 
		'placeholder' => __( 'Please find attached your export ready to review.', 'woocommerce-exporter' ), 
		'style' => apply_filters( 'woo_ce_scheduled_export_method_email_contents_style', 'height:10em;' ) 
	) 
);
echo '</div>';
?>

</div>
<!-- .email-options -->

<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_method_post( $post_ID = 0 ) {

	ob_start(); ?>
<div class="export-options post-options">

<?php
echo '<div class="options_group">';
woocommerce_wp_text_input( 
	array( 
		'id' => '_method_post_to', 
		'label' => __( 'Remote POST URL', 'woocommerce' ), 
		'desc_tip' => 'true', 
		'description' => __( 'Set the remote POST address for scheduled exports, this is for integration with web applications that accept a remote form POST. Default is empty.', 'woocommerce-exporter' ) 
	) 
);
echo '</div>';
?>

</div>
<!-- .post-options -->
<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_method_ftp( $post_ID = 0 ) {

	$ftp_host = get_post_meta( $post_ID, '_method_ftp_host', true );
	$ftp_port = get_post_meta( $post_ID, '_method_ftp_port', true );
	$ftp_protocol = get_post_meta( $post_ID, '_method_ftp_protocol', true );
	$ftp_user = get_post_meta( $post_ID, '_method_ftp_user', true );
	$ftp_pass = get_post_meta( $post_ID, '_method_ftp_pass', true );
	$ftp_path = get_post_meta( $post_ID, '_method_ftp_path', true );
	$ftp_filename = get_post_meta( $post_ID, '_method_ftp_filename', true );
	$ftp_passive = get_post_meta( $post_ID, '_method_ftp_passive', true );
	$ftp_timeout = get_post_meta( $post_ID, '_method_ftp_timeout', true );

	ob_start(); ?>
<div class="export-options ftp-options">

	<div class="options_group">
		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_host"><?php _e( 'Host', 'woocommerce-exporter' ); ?></label>
			<input type="text" id="ftp_method_host" name="ftp_method_host" size="15" class="short code" value="<?php echo sanitize_text_field( $ftp_host ); ?>" style="margin-right:6px;" />
			<img class="help_tip" data-tip="<?php _e( 'Enter the Host minus <code>ftp://</code> or <code>ftps://</code>', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
			<span style="float:left; margin-right:6px;"><?php _e( 'Port', 'woocommerce-exporter' ); ?></span>
			<input type="text" id="ftp_method_port" name="ftp_method_port" size="5" class="short code sized" value="<?php echo sanitize_text_field( $ftp_port ); ?>" maxlength="5" />
		</p>

		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_protocol"><?php _e( 'Protocol', 'woocommerce-exporter' ); ?></label>
			<select name="ftp_method_protocol" class="select short">
				<option value="ftp"<?php selected( $ftp_protocol, 'ftp' ); ?>><?php _e( 'FTP - File Transfer Protocol', 'woocommerce-exporter' ); ?></option>
				<option value="sftp"<?php selected( $ftp_protocol, 'sftp' ); ?><?php disabled( ( !function_exists( 'ssh2_connect' ) ? true : false ), true ); ?>><?php _e( 'SFTP - SSH File Transfer Protocol', 'woocommerce-exporter' ); ?></option>
			</select>
<?php if( !function_exists( 'ssh2_connect' ) ) { ?>
			<img class="help_tip" data-tip="<?php _e( 'The SFTP - SSH File Transfer Protocol option is not available as the required function ssh2_connect() is disabled within your WordPress site.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
<?php } ?>
		</p>

		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_user"><?php _e( 'Username', 'woocommerce-exporter' ); ?></label>
			<input type="text" id="ftp_method_user" name="ftp_method_user" size="15" class="short code" value="<?php echo sanitize_text_field( $ftp_user ); ?>" />
		</p>

		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_pass"><?php _e( 'Password', 'woocommerce-exporter' ); ?></label> <input type="password" id="ftp_method_pass" name="ftp_method_pass" size="15" class="short code" value="" placeholder="<?php echo str_repeat( '*', strlen( $ftp_pass ) ); ?>" /><?php if( !empty( $ftp_pass ) ) { echo ' ' . __( '(password is saved, fill this field to change it)', 'woocommerce-exporter' ); } ?><br />
		</p>

		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_file_path"><?php _e( 'File path', 'woocommerce-exporter' ); ?></label> <input type="text" id="ftp_method_file_path" name="ftp_method_path" size="25" class="short code" value="<?php echo sanitize_text_field( $ftp_path ); ?>" />
			<img class="help_tip" data-tip="<?php _e( 'Do not provide the filename within File path as it will be generated for you or rely on the fixed filename entered below.<br /><br />For file path example: <code>wp-content/uploads/exports/</code>', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</p>

		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_filename"><?php _e( 'Fixed filename', 'woocommerce-exporter' ); ?></label> <input type="text" id="ftp_method_filename" name="ftp_method_filename" size="25" class="short code" value="<?php echo sanitize_file_name( $ftp_filename ); ?>" />
			<img class="help_tip" data-tip="<?php _e( 'The export filename can be set within the Fixed filename field otherwise it defaults to the Export filename provided within General Settings above.<br /><br />Tags can be used: ', 'woocommerce-exporter' ); ?> <code>%dataset%</code>, <code>%date%</code>, <code>%time%</code>, <code>%random</code>, <code>%store_name%</code>." src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</p>

	</div>

	<div class="options_group">
		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_passive"><?php _e( 'Transfer mode', 'woocommerce-exporter' ); ?></label> 
			<select id="ftp_method_passive" name="ftp_method_passive" class="select short">
				<option value="auto"<?php selected( $ftp_passive, '' ); ?>><?php _e( 'Auto', 'woocommerce-exporter' ); ?></option>
				<option value="active"<?php selected( $ftp_passive, 'active' ); ?>><?php _e( 'Active', 'woocommerce-exporter' ); ?></option>
				<option value="passive"<?php selected( $ftp_passive, 'passive' ); ?>><?php _e( 'Passive', 'woocommerce-exporter' ); ?></option>
			</select>
		</p>

		<p class="form-field coupon_amount_field ">
			<label for="ftp_method_timeout"><?php _e( 'Timeout', 'woocommerce-exporter' ); ?></label> <input type="text" id="ftp_method_timeout" name="ftp_method_timeout" size="5" class="sized code" value="<?php echo sanitize_text_field( $ftp_timeout ); ?>" />
		</p>
	</div>

</div>
<!-- .ftp-options -->
<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_frequency_schedule( $post_ID = 0 ) {

	$auto_schedule = get_post_meta( $post_ID, '_auto_schedule', true );
	$auto_interval = get_post_meta( $post_ID, '_auto_interval', true );

	ob_start(); ?>
<div class="options_group">
	<p class="form-field coupon_amount_field ">
		<label for="auto_schedule"><?php _e( 'Frequency', 'woocommerce-exporter' ); ?></label>
		<input type="radio" name="auto_schedule" value="hourly"<?php checked( $auto_schedule, 'hourly' ); ?> /> <?php _e( 'Hourly', 'woocommerce-exporter' ); ?><br />
		<input type="radio" name="auto_schedule" value="daily"<?php checked( ( $auto_schedule == false ? 'daily' : $auto_schedule ), 'daily' ); ?> /> <?php _e( 'Daily', 'woocommerce-exporter' ); ?><br />
		<input type="radio" name="auto_schedule" value="twicedaily"<?php checked( $auto_schedule, 'twicedaily' ); ?> /> <?php _e( 'Twice Daily', 'woocommerce-exporter' ); ?><br />
		<input type="radio" name="auto_schedule" value="weekly"<?php checked( $auto_schedule, 'weekly' ); ?> /> <?php _e( 'Weekly', 'woocommerce-exporter' ); ?><br />
		<input type="radio" name="auto_schedule" value="monthly"<?php checked( $auto_schedule, 'monthly' ); ?> /> <?php _e( 'Monthly', 'woocommerce-exporter' ); ?><br />
		<span style="float:left; margin-right:6px;"><input type="radio" name="auto_schedule" value="custom"<?php checked( $auto_schedule, 'custom' ); ?> />&nbsp;<?php _e( 'Every ', 'woocommerce-exporter' ); ?></span>
		<input name="auto_interval" type="text" id="auto_interval" value="<?php echo esc_attr( $auto_interval ); ?>" size="6" maxlength="6" class="text sized" />
		<span style="float:left; margin-right:6px;"><?php _e( 'minutes', 'woocommerce-exporter' ); ?></span><br class="clear" />
		<input type="radio" name="auto_schedule" value="one-time" /> <?php _e( 'One time', 'woocommerce-exporter' ); ?>
	</p>
</div>
<!-- .options_group -->
<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_frequency_commence( $post_ID = 0 ) {

	$auto_commence = get_post_meta( $post_ID, '_auto_commence', true );
	$auto_commence_date = get_post_meta( $post_ID, '_auto_commence_date', true );
	$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );

	ob_start(); ?>
<div class="options_group">
	<p class="form-field coupon_amount_field ">
		<label for="auto_commence"><?php _e( 'Commence', 'woocommerce-exporter' ); ?></label>
		<input type="radio" name="auto_commence" value="now"<?php checked( ( $auto_commence == false ? 'now' : $auto_commence ), 'now' ); ?> /> <?php _e( 'From now', 'woocommerce-exporter' ); ?><br />
		<span style="float:left; margin-right:6px;"><input type="radio" name="auto_commence" value="future"<?php checked( $auto_commence, 'future' ); ?> /> <?php _e( 'From', 'woocommerce-exporter' ); ?></span><input type="text" name="auto_commence_date" size="20" maxlength="20" class="sized datetimepicker" value="<?php echo $auto_commence_date; ?>" /><!--, <?php _e( 'at this time', 'woocommerce-exporter' ); ?>: <input type="text" name="auto_interval_time" size="10" maxlength="10" class="text timepicker" />-->
		<span style="float:left; margin-right:6px;"><?php printf( __( 'Local time is: <code>%s</code>', 'woocommerce-exporter' ), date_i18n( $timezone_format ) ); ?></span>
	</p>
</div>
<!-- .options_group -->
<?php
		ob_end_flush();

}

function woo_ce_scheduled_export_details_meta_box() {

	global $post;

	$post_ID = ( $post ? $post->ID : 0 );

	$exports = get_post_meta( $post_ID, '_total_exports', true );
	$exports = absint( $exports );
	$last_export = get_post_meta( $post_ID, '_last_export', true );
	$last_export = ( $last_export == false ? 'No exports yet' : woo_ce_format_archive_date( 0, $last_export ) );

	include_once( WOO_CD_PATH . 'templates/admin/scheduled_export-export_details.php' );

}

function woo_ce_admin_scheduled_export_post_status() {

	// In-line javascript
	ob_start(); ?>
<script type="text/javascript">
jQuery(document).ready( function($) {
	/* Hide Post Status */
	jQuery('#post_status option[value="pending"]').remove();
	jQuery('#post_status option[value="private"]').remove();
	jQuery('.misc-pub-curtime').hide();
});
</script>
<?php
	ob_end_flush();

}

function woo_ce_scheduled_export_update( $post_ID = '', $post = array() ) {

	$post_type = 'scheduled_export';
	if( $post['post_type'] <> $post_type )
		return;

	if( ( get_post_status( $post_ID ) == 'publish' && $post['post_status'] == 'draft' ) || $post['post_status'] == 'trash' ) {
		woo_ce_cron_activation( true, $post_ID );
	}

}

function woo_ce_scheduled_export_save( $post_ID = 0 ) {

	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	// Make sure we play nice with other WooCommerce and WordPress exporters
	if( !isset( $_POST['woo_ce_export'] ) )
		return;

	$post_type = 'scheduled_export';
	check_admin_referer( $post_type, 'woo_ce_export' );

	// General
	update_post_meta( $post_ID, '_export_type', sanitize_text_field( $_POST['export_type'] ) );
	update_post_meta( $post_ID, '_export_format', sanitize_text_field( $_POST['export_format'] ) );
	update_post_meta( $post_ID, '_export_method', sanitize_text_field( $_POST['export_method'] ) );
	update_post_meta( $post_ID, '_export_fields', sanitize_text_field( $_POST['export_fields'] ) );

	// Filters
	update_post_meta( $post_ID, '_filter_product_category', ( isset( $_POST['product_filter_category'] ) ? array_map( 'absint', $_POST['product_filter_category'] ) : false ) );
	update_post_meta( $post_ID, '_filter_product_tag', ( isset( $_POST['product_filter_tag'] ) ? array_map( 'absint', $_POST['product_filter_tag'] ) : false ) );
	update_post_meta( $post_ID, '_filter_product_brand', ( isset( $_POST['product_filter_brand'] ) ? array_map( 'absint', $_POST['product_filter_brand'] ) : false ) );
	update_post_meta( $post_ID, '_filter_product_status', ( isset( $_POST['product_filter_status'] ) ? array_map( 'sanitize_text_field', $_POST['product_filter_status'] ) : false ) );
	update_post_meta( $post_ID, '_filter_product_type', ( isset( $_POST['product_filter_type'] ) ? array_map( 'sanitize_text_field', $_POST['product_filter_type'] ) : false ) );
	update_post_meta( $post_ID, '_filter_product_stock', sanitize_text_field( $_POST['product_filter_stock'] ) );
	update_post_meta( $post_ID, '_filter_product_featured', sanitize_text_field( $_POST['product_filter_featured'] ) );
	update_post_meta( $post_ID, '_filter_product_shipping_class', ( isset( $_POST['product_filter_shipping_class'] ) ? array_map( 'absint', $_POST['product_filter_shipping_class'] ) : false ) );
	$auto_product_sku = ( isset( $_POST['product_filter_sku'] ) ? $_POST['product_filter_sku'] : false );
	// Select2 passes us a string whereas Chosen gives us an array
	if( is_array( $auto_product_sku ) && count( $auto_product_sku ) == 1 )
		$auto_product_sku = explode( ',', $auto_product_sku[0] );
	update_post_meta( $post_ID, '_filter_product_sku', ( !empty( $auto_product_sku ) ? woo_ce_format_product_filters( array_map( 'absint', $auto_product_sku ) ) : false ) );

	$auto_order_date = sanitize_text_field( $_POST['order_dates_filter'] );
	$auto_order_dates_from = false;
	$auto_order_dates_to = false;
	$auto_order_date_variable = false;
	$auto_order_date_variable_length = false;
	if( $auto_order_date == 'variable' ) {
		$auto_order_date_variable = sanitize_text_field( $_POST['order_dates_filter_variable'] );
		$auto_order_date_variable_length = sanitize_text_field( $_POST['order_dates_filter_variable_length'] );
	} else if( $auto_order_date == 'manual' ) {
		$auto_order_dates_from = sanitize_text_field( $_POST['order_dates_from'] );
		$auto_order_dates_to = sanitize_text_field( $_POST['order_dates_to'] );
	}
	update_post_meta( $post_ID, '_filter_order_date', $auto_order_date );
	update_post_meta( $post_ID, '_filter_order_dates_from', $auto_order_dates_from );
	update_post_meta( $post_ID, '_filter_order_dates_to', $auto_order_dates_to );
	update_post_meta( $post_ID, '_filter_order_date_variable', $auto_order_date_variable );
	update_post_meta( $post_ID, '_filter_order_date_variable_length', $auto_order_date_variable_length );
	update_post_meta( $post_ID, '_filter_order_status', ( isset( $_POST['order_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['order_filter_status'] ) ) : false ) );
	$auto_order_product = ( isset( $_POST['order_filter_product'] ) ? $_POST['order_filter_product'] : false );
	// Select2 passes us a string whereas Chosen gives us an array
	if( is_array( $auto_order_product ) && count( $auto_order_product ) == 1 )
		$auto_order_product = explode( ',', $auto_order_product[0] );
	update_post_meta( $post_ID, '_filter_order_product', ( !empty( $auto_order_product ) ? woo_ce_format_product_filters( array_map( 'absint', $auto_order_product ) ) : false ) );
	update_post_meta( $post_ID, '_filter_order_billing_country', ( isset( $_POST['order_filter_billing_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_billing_country'] ) : false ) );
	update_post_meta( $post_ID, '_filter_order_shipping_country', ( isset( $_POST['order_filter_shipping_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping_country'] ) : false ) );
	update_post_meta( $post_ID, '_filter_order_payment', ( isset( $_POST['order_filter_payment'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_payment'] ) : false ) );
	update_post_meta( $post_ID, '_filter_order_shipping', ( isset( $_POST['order_filter_shipping'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping'] ) : false ) );

	update_post_meta( $post_ID, '_filter_user_role', ( isset( $_POST['user_filter_role'] ) ? array_map( 'sanitize_text_field', $_POST['user_filter_role'] ) : false ) );
	
	// Method
	update_post_meta( $post_ID, '_method_save_path', sanitize_text_field( $_POST['save_method_path'] ) );
	update_post_meta( $post_ID, '_method_email_to', sanitize_text_field( $_POST['_method_email_to'] ) );
	update_post_meta( $post_ID, '_method_email_subject', sanitize_text_field( $_POST['_method_email_subject'] ) );
	update_post_meta( $post_ID, '_method_email_contents', wp_kses( $_POST['_method_email_contents'], woo_ce_format_email_contents_allowed_html(), woo_ce_format_email_contents_allowed_protocols() ) );
	update_post_meta( $post_ID, '_method_post_to', sanitize_text_field( $_POST['_method_post_to'] ) );
	update_post_meta( $post_ID, '_method_ftp_host', ( isset( $_POST['ftp_method_host'] ) ? woo_ce_format_ftp_host( sanitize_text_field( $_POST['ftp_method_host'] ) ) : '' ) );
	update_post_meta( $post_ID, '_method_ftp_user', sanitize_text_field( $_POST['ftp_method_user'] ) );
	// Update FTP password only if it is filled in
	if( !empty( $_POST['ftp_method_pass'] ) )
		update_post_meta( $post_ID, '_method_ftp_pass', sanitize_text_field( $_POST['ftp_method_pass'] ) );
	update_post_meta( $post_ID, '_method_ftp_port', sanitize_text_field( $_POST['ftp_method_port'] ) );
	update_post_meta( $post_ID, '_method_ftp_protocol', sanitize_text_field( $_POST['ftp_method_protocol'] ) );
	update_post_meta( $post_ID, '_method_ftp_path', sanitize_text_field( $_POST['ftp_method_path'] ) );
	update_post_meta( $post_ID, '_method_ftp_passive', sanitize_text_field( $_POST['ftp_method_passive'] ) );
	update_post_meta( $post_ID, '_method_ftp_timeout', sanitize_text_field( $_POST['ftp_method_timeout'] ) );
	// Strip file extension from export filename
	$ftp_filename = ( isset( $_POST['ftp_method_filename'] ) ? strip_tags( $_POST['ftp_method_filename'] ) : '' );
	if( ( strpos( $ftp_filename, '.csv' ) !== false ) || ( strpos( $ftp_filename, '.tsv' ) !== false ) || ( strpos( $ftp_filename, '.xml' ) !== false ) || ( strpos( $ftp_filename, '.xls' ) !== false ) || ( strpos( $ftp_filename, '.xlsx' ) !== false ) )
		$ftp_filename = str_replace( array( '.csv', '.tsv', '.xml', '.xls', '.xlsx' ), '', $ftp_filename );
	update_post_meta( $post_ID, '_method_ftp_filename', $ftp_filename );
	unset( $ftp_filename );
	$save_filename = ( isset( $_POST['save_method_filename'] ) ? strip_tags( $_POST['save_method_filename'] ) : '' );
	if( ( strpos( $save_filename, '.csv' ) !== false ) || ( strpos( $save_filename, '.tsv' ) !== false ) || ( strpos( $save_filename, '.xml' ) !== false ) || ( strpos( $save_filename, '.xls' ) !== false ) || ( strpos( $save_filename, '.xlsx' ) !== false ) )
		$save_filename = str_replace( array( '.csv', '.tsv', '.xml', '.xls', '.xlsx' ), '', $save_filename );
	update_post_meta( $post_ID, '_method_save_filename', $save_filename );
	unset( $save_filename );

	// Scheduling
	$auto_schedule = sanitize_text_field( $_POST['auto_schedule'] );
	$auto_interval = ( $auto_schedule == 'custom' ? absint( $_POST['auto_interval'] ) : false );
	$auto_commence = sanitize_text_field( $_POST['auto_commence'] );
	$auto_commence_date = ( $auto_commence == 'future' ? sanitize_text_field( $_POST['auto_commence_date'] ) : false );
	$post_status = ( isset( $_POST['post_status'] ) ? $_POST['post_status'] : '123' );

	// Check if scheduling options have been modified
	if( 
		get_post_meta( $post_ID, '_auto_schedule', true ) <> $auto_schedule || 
		get_post_meta( $post_ID, '_auto_interval', true ) <> $auto_interval || 
		get_post_meta( $post_ID, '_auto_commence', true ) <> $auto_commence || 
		get_post_meta( $post_ID, '_auto_commence_date', true ) <> $auto_commence_date ||
		$post_status <> get_post_status( $post_ID )
	) {
		update_post_meta( $post_ID, '_auto_schedule', $auto_schedule );
		update_post_meta( $post_ID, '_auto_interval', $auto_interval );
		update_post_meta( $post_ID, '_auto_commence', $auto_commence );
		update_post_meta( $post_ID, '_auto_commence_date', $auto_commence_date );
		woo_ce_cron_activation( true, $post_ID );
	}

}
?>