<?php
function woo_ce_export_settings_quicklinks() {

	ob_start(); ?>
<li>| <a href="#xml-settings"><?php _e( 'XML Settings', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#rss-settings"><?php _e( 'RSS Settings', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#scheduled-exports"><?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#cron-exports"><?php _e( 'CRON Exports', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#orders-screen"><?php _e( 'Orders Screen', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#export-triggers"><?php _e( 'Export Triggers', 'woocommerce-exporter' ); ?></a></li>
<?php
	ob_end_flush();

}

function woo_ce_export_settings_csv() {

	$header_formatting = woo_ce_get_option( 'header_formatting', 1 );

	ob_start(); ?>
<tr>
	<th>
		<label for="header_formatting"><?php _e( 'Header formatting', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" name="header_formatting" value="1"<?php checked( $header_formatting, '1' ); ?> />&nbsp;<?php _e( 'Include export field column headers', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="radio" name="header_formatting" value="0"<?php checked( $header_formatting, '0' ); ?> />&nbsp;<?php _e( 'Do not include export field column headers', 'woocommerce-exporter' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Choose the header format that suits your spreadsheet software (e.g. Excel, OpenOffice, etc.). This rule applies to CSV, TSV, XLS and XLSX export types.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

// Returns the HTML template for the CRON, scheduled exports, Secret Export Key and Export Trigger options for the Settings screen
function woo_ce_export_settings_extend() {

	// XML settings
	$xml_attribute_url = woo_ce_get_option( 'xml_attribute_url', 1 );
	$xml_attribute_title = woo_ce_get_option( 'xml_attribute_title', 1 );
	$xml_attribute_date = woo_ce_get_option( 'xml_attribute_date', 1 );
	$xml_attribute_time = woo_ce_get_option( 'xml_attribute_time', 0 );
	$xml_attribute_export = woo_ce_get_option( 'xml_attribute_export', 1 );
	$xml_attribute_orderby = woo_ce_get_option( 'xml_attribute_orderby', 0 );
	$xml_attribute_order = woo_ce_get_option( 'xml_attribute_order', 0 );
	$xml_attribute_limit = woo_ce_get_option( 'xml_attribute_limit', 0 );
	$xml_attribute_offset = woo_ce_get_option( 'xml_attribute_offset', 0 );

	// RSS settings
	$rss_title = woo_ce_get_option( 'rss_title', '' );
	$rss_link = woo_ce_get_option( 'rss_link', '' );
	$rss_description = woo_ce_get_option( 'rss_description', '' );

	// Scheduled exports
	$enable_auto = woo_ce_get_option( 'enable_auto', 0 );

	// CRON exports
	$enable_cron = woo_ce_get_option( 'enable_cron', 0 );
	$secret_key = woo_ce_get_option( 'secret_key', '' );
	$cron_fields = woo_ce_get_option( 'cron_fields', 'all' );

	// Orders Screen
	$order_actions_csv = woo_ce_get_option( 'order_actions_csv', 1 );
	$order_actions_tsv = woo_ce_get_option( 'order_actions_tsv', 1 );
	$order_actions_xls = woo_ce_get_option( 'order_actions_xls', 1 );
	$order_actions_xlsx = woo_ce_get_option( 'order_actions_xlsx', 1 );
	$order_actions_xml = woo_ce_get_option( 'order_actions_xml', 0 );
	$order_actions_fields = woo_ce_get_option( 'order_actions_fields', 'all' );

	// Export Triggers
	$enable_trigger_new_order = woo_ce_get_option( 'enable_trigger_new_order', 0 );
	$trigger_new_order_format = woo_ce_get_option( 'trigger_new_order_format', 'csv' );
	$trigger_new_order_method = woo_ce_get_option( 'trigger_new_order_method', 'archive' );
	$trigger_new_order_method_email_to = woo_ce_get_option( 'trigger_new_order_method_email_to', '' );
	$trigger_new_order_method_email_subject = woo_ce_get_option( 'trigger_new_order_method_email_subject', '' );
	$trigger_new_order_fields = woo_ce_get_option( 'trigger_new_order_fields', 'all' );

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	ob_start(); ?>
<tr id="xml-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'XML Settings', 'woocommerce-exporter' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Attribute display', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="xml_attribute_url" value="1"<?php checked( $xml_attribute_url ); ?> /> <?php _e( 'Site Address', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_title" value="1"<?php checked( $xml_attribute_title ); ?> /> <?php _e( 'Site Title', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_date" value="1"<?php checked( $xml_attribute_date ); ?> /> <?php _e( 'Export Date', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_time" value="1"<?php checked( $xml_attribute_time ); ?> /> <?php _e( 'Export Time', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_export" value="1"<?php checked( $xml_attribute_export ); ?> /> <?php _e( 'Export Type', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_orderby" value="1"<?php checked( $xml_attribute_orderby ); ?> /> <?php _e( 'Export Order By', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_order" value="1"<?php checked( $xml_attribute_order ); ?> /> <?php _e( 'Export Order', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_limit" value="1"<?php checked( $xml_attribute_limit ); ?> /> <?php _e( 'Limit Volume', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_offset" value="1"<?php checked( $xml_attribute_offset ); ?> /> <?php _e( 'Volume Offset', 'woocommerce-exporter' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different attributes in the XML export.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #xml-settings -->

<tr id="rss-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'RSS Settings', 'woocommerce-exporter' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_title"><?php _e( 'Title element', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="rss_title" type="text" id="rss_title" value="<?php echo esc_attr( $rss_title ); ?>" class="large-text" />
		<p class="description"><?php _e( 'Defines the title of the data feed (e.g. Product export for WordPress Shop).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_link"><?php _e( 'Link element', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="rss_link" type="text" id="rss_link" value="<?php echo esc_attr( $rss_link ); ?>" class="large-text" />
		<p class="description"><?php _e( 'A link to your website, this doesn\'t have to be the location of the RSS feed.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_description"><?php _e( 'Description element', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="rss_description" type="text" id="rss_description" value="<?php echo esc_attr( $rss_description ); ?>" class="large-text" />
		<p class="description"><?php _e( 'A description of your data feed.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #rss-settings -->

<tr id="scheduled-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3>
			<div class="dashicons dashicons-calendar"></div>&nbsp;<?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?>
			<a href="<?php echo esc_url( admin_url( add_query_arg( 'post_type', 'scheduled_export', 'post-new.php' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'woocommerce-exporter' ); ?></a>
		</h3>
<?php if( $enable_auto == 1 ) { ?>
		<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php _e( 'Scheduled exports is enabled', 'woocommerce-exporter' ); ?></strong></p>
<?php } ?>
		<p class="description"><?php _e( 'Automatically generate exports and apply filters to export just what you need.<br />Adjusting options within the Scheduling sub-section will after clicking Save Changes refresh the scheduled export engine, editing filters, formats, methods, etc. will not affect the scheduling of the current scheduled export.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_auto"><?php _e( 'Enable scheduled exports', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<select id="enable_auto" name="enable_auto">
			<option value="1"<?php selected( $enable_auto, 1 ); ?>><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
			<option value="0"<?php selected( $enable_auto, 0 ); ?>><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
		</select>
<?php if( $enable_auto == 0 && woo_ce_get_option( 'hide_scheduled_exports_tab', 0 ) == 1 ) { ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'restore_scheduled_exports_tab', '_wpnonce' => wp_create_nonce( 'woo_ce_restore_scheduled_exports_tab' ) ) ) ); ?>"><?php _e( 'Restore Scheduled Exports tab', 'woocommerce-exporter' ); ?></a>
<?php } ?>
		<p class="description"><?php _e( 'Enabling Scheduled Exports will trigger automated exports at the intervals specified under Scheduling within each scheduled export. You can suspend individual scheduled exports by changing the Post Status.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>

<tr>
	<th>&nbsp;</th>
	<td>
		<p>
			<a href="<?php echo add_query_arg( array( 'tab' => 'scheduled_export' ) ); ?>"><?php _e( 'View Scheduled Exports', 'woocommerce-exporter' ); ?></a>
		</p>
	</td>
</tr>

<tr id="cron-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-clock"></div>&nbsp;<?php _e( 'CRON Exports', 'woocommerce-exporter' ); ?></h3>
<?php if( $enable_cron == 1 ) { ?>
		<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php _e( 'CRON Exports is enabled', 'woocommerce-exporter' ); ?></strong></p>
<?php } ?>
		<p class="description"><?php printf( __( 'Store Exporter Deluxe supports exporting via a command line request, to do this you need to prepare a specific URL and pass it the following required inline parameters. For sample CRON requests and supported arguments consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_cron"><?php _e( 'Enable CRON', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<select id="enable_cron" name="enable_cron">
			<option value="1"<?php selected( $enable_cron, 1 ); ?>><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
			<option value="0"<?php selected( $enable_cron, 0 ); ?>><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Enabling CRON allows developers to schedule automated exports and connect with Store Exporter Deluxe remotely.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="secret_key"><?php _e( 'Export secret key', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="secret_key" type="text" id="secret_key" value="<?php echo esc_attr( $secret_key ); ?>" class="large-text code" />
		<p class="description"><?php _e( 'This secret key (can be left empty to allow unrestricted access) limits access to authorised developers who provide a matching key when working with Store Exporter Deluxe.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="cron_fields"><?php _e( 'Export fields', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="cron_fields" name="cron_fields" value="all"<?php checked( $cron_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="radio" name="cron_fields" value="saved"<?php checked( $cron_fields, 'saved' ); ?> /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woocommerce-exporter' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #cron-exports -->

<tr id="orders-screen">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Orders Screen', 'woocommerce-exporter' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Actions display', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="order_actions_csv" value="1"<?php checked( $order_actions_csv ); ?> /> <?php _e( 'Export to CSV', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_tsv" value="1"<?php checked( $order_actions_tsv ); ?> /> <?php _e( 'Export to TSV', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_xls" value="1"<?php checked( $order_actions_xls ); ?> /> <?php _e( 'Export to XLS', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_xlsx" value="1"<?php checked( $order_actions_xlsx ); ?> /> <?php _e( 'Export to XLSX', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_xml" value="1"<?php checked( $order_actions_xml ); ?> /> <?php _e( 'Export to XML', 'woocommerce-exporter' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different Order actions on the WooCommerce &raquo; Orders screen.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="order_actions_fields"><?php _e( 'Export fields', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="order_actions_fields" name="order_actions_fields" value="all"<?php checked( $order_actions_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="radio" name="order_actions_fields" value="saved"<?php checked( $order_actions_fields, 'saved' ); ?> /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woocommerce-exporter' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #orders-screen -->

<tr id="export-triggers">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Export Triggers', 'woocommerce-exporter' ); ?></h3>
		<p class="description"><?php _e( 'Run exports on specific triggers within your WooCommerce store.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #export-triggers -->

<tr id="new-orders">
	<th>
		<label><?php _e( 'New Order', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
<?php if( $enable_trigger_new_order == 1 ) { ?>
		<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php _e( 'Export on New Order is enabled, this will run for each new Order received.', 'woocommerce-exporter' ); ?></strong></p>
<?php } ?>
		<p class="description"><?php _e( 'Trigger an export of each new Order that is generated after successful Checkout.', 'woocommerce-exporter' ); ?></p>
		<ul>

			<li>
				<p>
					<label for="enable_trigger_new_order"><?php _e( 'Enable trigger', 'woocommerce-exporter' ); ?></label><br />
					<select id="enable_trigger_new_order" name="enable_trigger_new_order">
						<option value="1"<?php selected( $enable_trigger_new_order, 1 ); ?>><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
						<option value="0"<?php selected( $enable_trigger_new_order, 0 ); ?>><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
					</select>
				</p>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export format', 'woocommerce-exporter' ); ?></label></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" name="trigger_new_order_format" value="csv"<?php checked( $trigger_new_order_format, 'csv' ); ?> /> <?php _e( 'CSV', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woocommerce-exporter' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="tsv"<?php checked( $trigger_new_order_format, 'tsv' ); ?> /> <?php _e( 'TSV', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Tab Separated Values)', 'woocommerce-exporter' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="xls"<?php checked( $trigger_new_order_format, 'xls' ); ?> /> <?php _e( 'Excel (XLS)', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woocommerce-exporter' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="xlsx"<?php checked( $trigger_new_order_format, 'xlsx' ); ?> /> <?php _e( 'Excel (XLSX)', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woocommerce-exporter' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="xml"<?php checked( $trigger_new_order_format, 'xml' ); ?> /> <?php _e( 'XML', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woocommerce-exporter' ); ?></span></label></li>
				</ul>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export method', 'woocommerce-exporter' ); ?></label></p>
				<select id="trigger_new_order_method" name="trigger_new_order_method">
					<option value="archive"<?php selected( $trigger_new_order_method, 'archive' ); ?>><?php echo woo_ce_format_export_method( 'archive' ); ?></option>
					<option value="email"<?php selected( $trigger_new_order_method, 'email' ); ?>><?php echo woo_ce_format_export_method( 'email' ); ?></option>
<!--
					<option value="post"<?php selected( $trigger_new_order_method, 'post' ); ?>><?php echo woo_ce_format_export_method( 'post' ); ?></option>
					<option value="ftp"<?php selected( $trigger_new_order_method, 'ftp' ); ?>><?php echo woo_ce_format_export_method( 'ftp' ); ?></option>
-->
				</select>
				<hr />
			</li>

			<li class="export_method_options">
				<p><label><?php _e( 'Export method options', 'woocommerce-exporter' ); ?></label></p>
				<div class="export-options email-options">
					<ul style="margin-top:0.2em;">
						<li><label><?php _e( 'E-mail recipient', 'woocommerce-exporter' ); ?> <input type="text" id="trigger_new_method_email_to" name="trigger_new_method_email_to" value="<?php echo $trigger_new_order_method_email_to; ?>" class="large-text" placeholder="big.bird@sesamestreet.org,oscar@sesamestreet.org"></label></li>
						<li><label><?php _e( 'E-mail subject', 'woocommerce-exporter' ); ?> <input type="text" id="trigger_new_method_email_subject" name="trigger_new_method_email_subject" value="<?php echo $trigger_new_order_method_email_subject; ?>" class="large-text" placeholder="<?php _e( 'Order export', 'woocommerce-exporter' ); ?>"></label></li>
					</ul>
				</div>
				<!-- .email-options -->
				<div class="export-options save-options">
					<ul style="margin-top:0.2em;">
						<li><label><?php _e( 'Filepath', 'woocommerce-exporter' ); ?> <input type="text" id="" name="" value="<?php echo 111; ?>" class="large-text" placeholder=""></label></li>
					</ul>
				</div>
				<!-- .save-options -->
				<div class="export-options archive-options">
					<p><?php _e( 'No export method options are available for this export method.', 'woocommerce-exporter' ); ?></p>
				</div>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export fields', 'woocommerce-exporter' ); ?></label></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" id="trigger_new_order_fields" name="trigger_new_order_fields" value="all"<?php checked( $trigger_new_order_fields, 'all' ); ?> /> <?php _e( 'Include all Order Fields', 'woocommerce-exporter' ); ?></label></li>
					<li><label><input type="radio" name="trigger_new_order_fields" value="saved"<?php checked( $trigger_new_order_fields, 'saved' ); ?> /> <?php _e( 'Use the saved Export Fields preference for Orders set on the Export screen', 'woocommerce-exporter' ); ?></label></li>
				</ul>
				<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for Orders. Default is to include all export fields.', 'woocommerce-exporter' ); ?></p>
			</li>

		</ul>
	</td>
</tr>
<!-- #new-orders -->

<?php
	ob_end_flush();

}

function woo_ce_export_settings_save() {

	// Strip file extension from export filename
	$export_filename = strip_tags( $_POST['export_filename'] );
	if( ( strpos( $export_filename, '.csv' ) !== false ) || ( strpos( $export_filename, '.xml' ) !== false ) || ( strpos( $export_filename, '.xls' ) !== false ) )
		$export_filename = str_replace( array( '.csv', '.xml', '.xls' ), '', $export_filename );
	woo_ce_update_option( 'export_filename', $export_filename );
	woo_ce_update_option( 'delete_file', absint( $_POST['delete_file'] ) );
	woo_ce_update_option( 'encoding', sanitize_text_field( $_POST['encoding'] ) );
	woo_ce_update_option( 'delimiter', sanitize_text_field( $_POST['delimiter'] ) );
	woo_ce_update_option( 'category_separator', sanitize_text_field( $_POST['category_separator'] ) );
	woo_ce_update_option( 'line_ending_formatting', sanitize_text_field( $_POST['line_ending'] ) );
	woo_ce_update_option( 'bom', absint( $_POST['bom'] ) );
	woo_ce_update_option( 'escape_formatting', sanitize_text_field( $_POST['escape_formatting'] ) );
	woo_ce_update_option( 'header_formatting', absint( $_POST['header_formatting'] ) );
	$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
	if( $_POST['date_format'] == 'custom' && !empty( $_POST['date_format_custom'] ) ) {
		if( $date_format <> $_POST['date_format'] )
			woo_ce_update_option( 'date_format', sanitize_text_field( $_POST['date_format_custom'] ) );
	} else if( $date_format <> $_POST['date_format'] ) {
		// Update the date format on scheduled exports
		if( $scheduled_exports = woo_ce_get_scheduled_exports() ) {
			foreach( $scheduled_exports as $scheduled_export ) {
				$order_dates_from = get_post_meta( $scheduled_export, '_filter_order_dates_from', true );
				$order_dates_to = get_post_meta( $scheduled_export, '_filter_order_dates_to', true );
				// Format date to new format
				if( !empty( $order_dates_from ) )
					update_post_meta( $scheduled_export, '_filter_order_dates_from', date( sanitize_text_field( $_POST['date_format'] ), strtotime( $order_dates_from ) ) );
				if( !empty( $order_dates_to ) )
				update_post_meta( $scheduled_export, '_filter_order_dates_to', date( sanitize_text_field( $_POST['date_format'] ), strtotime( $order_dates_to ) ) );
			}
		}
		woo_ce_update_option( 'date_format', sanitize_text_field( $_POST['date_format'] ) );
	}

	// XML settings
	woo_ce_update_option( 'xml_attribute_url', ( isset( $_POST['xml_attribute_url'] ) ? absint( $_POST['xml_attribute_url'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_title', ( isset( $_POST['xml_attribute_title'] ) ? absint( $_POST['xml_attribute_title'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_date', ( isset( $_POST['xml_attribute_date'] ) ? absint( $_POST['xml_attribute_date'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_time', ( isset( $_POST['xml_attribute_time'] ) ? absint( $_POST['xml_attribute_time'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_export', ( isset( $_POST['xml_attribute_export'] ) ? absint( $_POST['xml_attribute_export'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_orderby', ( isset( $_POST['xml_attribute_orderby'] ) ? absint( $_POST['xml_attribute_orderby'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_order', ( isset( $_POST['xml_attribute_order'] ) ? absint( $_POST['xml_attribute_order'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_limit', ( isset( $_POST['xml_attribute_limit'] ) ? absint( $_POST['xml_attribute_limit'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_offset', ( isset( $_POST['xml_attribute_offset'] ) ? absint( $_POST['xml_attribute_offset'] ) : 0 ) );

	// RSS settings
	woo_ce_update_option( 'rss_title', ( isset( $_POST['rss_title'] ) ? sanitize_text_field( $_POST['rss_title'] ) : '' ) );
	woo_ce_update_option( 'rss_link', ( isset( $_POST['rss_link'] ) ? esc_url_raw( $_POST['rss_link'] ) : '' ) );
	woo_ce_update_option( 'rss_description', ( isset( $_POST['rss_description'] ) ? sanitize_text_field( $_POST['rss_description'] ) : '' ) );

	// Scheduled export settings
	$enable_auto = absint( $_POST['enable_auto'] );
	if( 
		woo_ce_get_option( 'enable_auto', 0 ) <> $enable_auto
	) {
		// Save these fields before we re-load the WP-CRON schedule
		woo_ce_update_option( 'enable_auto', $enable_auto );
		if( $enable_auto == 0 ) {
			woo_ce_cron_activation( true );
		}
	}

	// CRON settings
	$enable_cron = absint( $_POST['enable_cron'] );
	// Display additional notice if Enabled CRON is enabled/disabled
	if( woo_ce_get_option( 'enable_cron', 0 ) <> $enable_cron ) {
		$message = sprintf( __( 'CRON support has been %s.', 'woocommerce-exporter' ), ( ( $enable_cron == 1 ) ? __( 'enabled', 'woocommerce-exporter' ) : __( 'disabled', 'woocommerce-exporter' ) ) );
		woo_cd_admin_notice( $message );
	}
	woo_ce_update_option( 'enable_cron', $enable_cron );
	woo_ce_update_option( 'secret_key', sanitize_text_field( $_POST['secret_key'] ) );
	woo_ce_update_option( 'cron_fields', sanitize_text_field( $_POST['cron_fields'] ) );

	// Orders Screen
	woo_ce_update_option( 'order_actions_csv', ( isset( $_POST['order_actions_csv'] ) ? absint( $_POST['order_actions_csv'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_tsv', ( isset( $_POST['order_actions_tsv'] ) ? absint( $_POST['order_actions_tsv'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_xls', ( isset( $_POST['order_actions_xls'] ) ? absint( $_POST['order_actions_xls'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_xlsx', ( isset( $_POST['order_actions_xlsx'] ) ? absint( $_POST['order_actions_xlsx'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_xml', ( isset( $_POST['order_actions_xml'] ) ? absint( $_POST['order_actions_xml'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_fields', sanitize_text_field( $_POST['order_actions_fields'] ) );

	// Export Triggers
	woo_ce_update_option( 'enable_trigger_new_order', ( isset( $_POST['enable_trigger_new_order'] ) ? absint( $_POST['enable_trigger_new_order'] ) : 0 ) );
	woo_ce_update_option( 'trigger_new_order_format', sanitize_text_field( $_POST['trigger_new_order_format'] ) );
	woo_ce_update_option( 'trigger_new_order_method', sanitize_text_field( $_POST['trigger_new_order_method'] ) );
	woo_ce_update_option( 'trigger_new_order_method_email_to', sanitize_text_field( $_POST['trigger_new_method_email_to'] ) );
	woo_ce_update_option( 'trigger_new_order_method_email_subject', sanitize_text_field( $_POST['trigger_new_method_email_subject'] ) );
	woo_ce_update_option( 'trigger_new_order_fields', sanitize_text_field( $_POST['trigger_new_order_fields'] ) );

	$message = __( 'Changes have been saved.', 'woocommerce-exporter' );
	woo_cd_admin_notice( $message );

}
?>