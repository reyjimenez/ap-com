<?php
/*
Plugin Name: WooCommerce - Store Exporter Deluxe
Plugin URI: http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/
Description: Unlocks business focused e-commerce features within Store Exporter for WooCommerce. This Pro ugprade will de-activate the basic Store Exporter Plugin on activation.
Version: 2.1.1
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
Text Domain: woocommerce-exporter
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'WOO_CD_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'WOO_CD_RELPATH', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'WOO_CD_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_CD_PREFIX', 'woo_ce' );

// Turn this on to enable additional debugging options at export time
define( 'WOO_CD_DEBUG', false );

if( !function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Check if multiple instances of SED are installed and activated
if( is_plugin_active( WOO_CD_DIRNAME . '/exporter-deluxe.php' ) && function_exists( 'woo_cd_i18n' ) ) {

	function woo_ce_admin_duplicate_plugin() {

		ob_start(); ?>
<div class="error">
	<p><?php printf( __( 'Please de-activate any other instances of <em>WooCommerce - Store Exporter Deluxe</em> before re-activating this Plugin.', 'woocommerce-exporter' ) ); ?></p>
</div>
<?php
		ob_end_flush();

		deactivate_plugins( str_replace( '\\', '/', dirname( __FILE__ ) ) . '/exporter-deluxe.php' );

	}
	add_action( 'admin_notices', 'woo_ce_admin_duplicate_plugin' );

} else {

	// Disable basic Store Exporter if it is activated
	include_once( WOO_CD_PATH . 'common/common.php' );
	if( defined( 'WOO_CE_PREFIX' ) == true ) {
		// Detect Store Exporter and other platform versions
		include_once( WOO_CD_PATH . 'includes/install.php' );
		woo_cd_detect_ce();
	} else {
		include_once( WOO_CD_PATH . 'includes/functions.php' );
	}

	function woo_cd_i18n() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-exporter' );
		load_plugin_textdomain( 'woocommerce-exporter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	}
	add_action( 'init', 'woo_cd_i18n' );

	if( is_admin() ) {

		/* Start of: WordPress Administration */

		// Register our install script for first time install
		include_once( WOO_CD_PATH . 'includes/install.php' );
		register_activation_hook( __FILE__, 'woo_cd_install' );
		register_deactivation_hook( __FILE__, 'woo_cd_uninstall' );

		// Initial scripts and export process
		function woo_cd_admin_init() {

			global $export, $wp_roles;

			$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

			// Check the User has the activate_plugins capability
			if( current_user_can( 'activate_plugins' ) ) {
				// Time to tell the store owner if we were unable to disable the basic Store Exporter
				if( defined( 'WOO_CE_PREFIX' ) ) {
					// Display notice if we were unable to de-activate basic Store Exporter
					if( ( is_plugin_active( 'woocommerce-exporter/exporter.php' ) || is_plugin_active( 'woocommerce-store-exporter/exporter.php' ) ) ) {
						$plugins_url = esc_url( add_query_arg( '', '', 'plugins.php' ) );
						$message = sprintf( __( 'We did our best to de-activate Store Exporter for you but may have failed, please check that the basic Store Exporter is de-activated from the <a href="%s">Plugins screen</a>.', 'woocommerce-exporter' ), $plugins_url );
						woo_cd_admin_notice( $message, 'error', array( 'plugins.php', 'update-core.php' ) );
					}
				}

				// Detect if another e-Commerce platform is activated
				if( !woo_is_woo_activated() && ( woo_is_jigo_activated() || woo_is_wpsc_activated() ) ) {
					$message = sprintf( __( 'We have detected another e-Commerce Plugin than WooCommerce activated, please check that you are using Store Exporter Deluxe for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
					woo_cd_admin_notice( $message, 'error', 'plugins.php' );
				} else if( !woo_is_woo_activated() ) {
					$message = sprintf( __( 'We have been unable to detect the WooCommerce Plugin activated on this WordPress site, please check that you are using Store Exporter Deluxe for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
					woo_cd_admin_notice( $message, 'error', 'plugins.php' );
				}

				// Detect if any known conflict Plugins are activated

				// WooCommerce Subscriptions Exporter - http://codecanyon.net/item/woocommerce-subscription-exporter/6569668
				if( function_exists( 'wc_subs_exporter_admin_init' ) ) {
					$message = sprintf( __( 'We have detected an activated Plugin for WooCommerce that is known to conflict with Store Exporter Deluxe, please de-activate WooCommerce Subscriptions Exporter to resolve export issues within Store Exporter Deluxe. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
					woo_cd_admin_notice( $message, 'error', array( 'plugins.php', 'admin.php' ) );
				}

				add_action( 'after_plugin_row_' . WOO_CD_RELPATH, 'woo_ce_admin_plugin_row' );

			}

			// Check the User has the view_woocommerce_reports capability
			if( current_user_can( 'view_woocommerce_reports' ) == false )
				return;

			// Migrate scheduled export to CPT
			if( woo_ce_get_option( 'auto_format', false ) !== false ) {
				if( woo_ce_legacy_scheduled_export() ) {
					$message = __( 'We have detected a legacy scheduled export and updated it to work with the new multiple scheduled export engine in Store Exporter Deluxe. Please open WooCommerce &raquo; Store Export &raquo; Settings &raquo; Scheduled Exports to see what\'s available.', 'woocommerce-exporter' );
					woo_cd_admin_notice( $message );
				}
			}

			// Load Dashboard widget for Scheduled Exports
			add_action( 'wp_dashboard_setup', 'woo_ce_admin_dashboard_setup' );
			// Add Export Status to Orders screen
			add_filter( 'manage_edit-shop_order_columns', 'woo_ce_admin_order_column_headers', 20 );
			add_action( 'manage_shop_order_posts_custom_column', 'woo_ce_admin_order_column_content' );
			// Load Download buttons for Orders screen
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'woo_ce_styles', plugins_url( '/templates/admin/export.css', WOO_CD_RELPATH ) );
			// Add our export to CSV, XML, XLS, XLSX action buttons
			add_filter( 'woocommerce_admin_order_actions', 'woo_ce_admin_order_actions', 10, 2 );
			add_action( 'wp_ajax_woo_ce_export_order', 'woo_ce_ajax_export_order' );
			// Add Download as... options to Orders Bulk
			add_action( 'admin_footer', 'woo_ce_admin_order_bulk_actions' );
			add_action( 'load-edit.php', 'woo_ce_admin_order_process_bulk_action' );
			// Add Download as... options to Edit Order Actions
			add_action( 'woocommerce_order_actions', 'woo_ce_admin_order_single_actions' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_csv', 'woo_ce_admin_order_single_export_csv' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_tsv', 'woo_ce_admin_order_single_export_tsv' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_xls', 'woo_ce_admin_order_single_export_xls' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_xlsx', 'woo_ce_admin_order_single_export_xlsx' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_xml', 'woo_ce_admin_order_single_export_xml' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_unflag', 'woo_ce_admin_order_single_export_unflag' );
			// Add memory usage to screen footer
			add_filter( 'admin_footer_text', 'woo_ce_admin_footer_text' );

			// Load up meta boxes for the Scheduled Export screen
			$post_type = 'scheduled_export';
			add_action( 'edit_form_top', 'woo_ce_scheduled_export_banner' );
			add_meta_box( 'woocommerce-coupon-data', __( 'Export Filters', 'woocommerce-exporter' ), 'woo_ce_scheduled_export_filters_meta_box', $post_type, 'normal', 'high' );
			add_meta_box( 'woo_ce-scheduled_exports-export_details', __( 'Export Details', 'woocommerce-exporter' ), 'woo_ce_scheduled_export_details_meta_box', $post_type, 'normal', 'default' );
			add_action( 'pre_post_update', 'woo_ce_scheduled_export_update', 10, 2 );
			add_action( 'save_post_scheduled_export', 'woo_ce_scheduled_export_save' );

			// Check that we are on the Store Exporter screen
			$page = ( isset($_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : false );
			if( $page != strtolower( WOO_CD_PREFIX ) )
				return;

			// Process any pre-export notice confirmations
			$action = ( function_exists( 'woo_get_action' ) ? woo_get_action() : false );
			switch( $action ) {

				// Reset all dismissed notices within Store Exporter Deluxe
				case 'nuke_notices':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_notices' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_nuke_dismissed_notices();
						$message = __( 'All dimissed notices within Store Exporter Deluxe have been restored.', 'woocommerce-exporter' );
						woo_cd_admin_notice( $message );
					}
					break;

				// Delete all WordPress Options associated with Store Exporter Deluxe
				case 'nuke_options':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_options' ) ) {
						// Delete WordPress Options used by Store Exporter Deluxe (Uninstall)
						if( woo_ce_nuke_options() ) {
							$message = __( 'All Store Exporter Deluxe WordPress Options have been deleted from your WordPress site, you can now de-activate and delete Store Exporter Deluxe.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message );
						} else {
							$message = __( 'Not all Store Exporter Deluxe WordPress Options could be deleted from your WordPress site, please see the WordPress Options table for Options prefixed by <code>woo_ce_</code>.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
						}
					}
					break;

				// Delete all Archives
				case 'nuke_archives':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_archives' ) ) {
						// Delete saved exports
						if( woo_ce_nuke_archive_files() ) {
							$message = __( 'All existing Archives and their export files have been deleted from your WordPress site.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message );
						} else {
							$message = __( 'There were no existing Archives to be deleted from your WordPress site.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
						}
					}
					break;

				// Reset WP-CRON
				case 'nuke_cron':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_cron' ) ) {
						// Reset WP-CRON
						if( woo_ce_nuke_cron() ) {
							$message = __( 'The WordPress Option \'cron\' has been reset, it will be re-populated on the next screen load.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message );
						} else {
							$message = __( ' WordPress Option \'cron\' could not be reset.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
						}
					}
					break;

				// Delete all Scheduled Exports
				case 'nuke_scheduled_exports':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_archives' ) ) {
						// Delete Scheduled Exports
						if( woo_ce_nuke_scheduled_exports() ) {
							$message = __( 'All existing Scheduled Exports have been deleted from your WordPress site.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message );
						} else {
							$message = __( 'There were no existing Scheduled Exports to be deleted from your WordPress site.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
						}
					}
					break;

				case 'dismiss_archives_privacy_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_archives_privacy_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_archives_privacy_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'override_archives_privacy':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_override_archives_privacy' ) ) {
						// Update Post Status of unsecured saved exports
						if( woo_ce_update_archives_privacy() ) {
							$message = __( 'All archived exports have been updated.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message );
						} else {
							$message = __( 'There were no existing archived exports to be updated.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
						}
					}
					break;

				// Prompt on Export screen when insufficient memory (less than 64M is allocated)
				case 'dismiss_memory_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_memory_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_memory_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				// Prompt on Export screen when open_basedir is enabled and the PHP temp directory is not in the exception list
				case 'dismiss_open_basedir_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_open_basedir_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_open_basedir_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				// Prompt on Export screen when PHP configuration option max_execution_time cannot be increased
				case 'dismiss_execution_time_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_execution_time_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_execution_time_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				// Prompt on Export screen when PHP 5.2 or lower is installed
				case 'dismiss_php_legacy':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_php_legacy' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_php_legacy', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_subscription_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_subscription_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_subscription_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_checkout_addons_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_checkout_addons_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_checkout_addons_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_query_monitor_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_query_monitor_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_query_monitor_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_secure_archives_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_secure_archives_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_secure_archives_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'enable_archives':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_enable_archives' ) ) {
						woo_ce_update_option( 'delete_file', 0 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_archives_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_archives_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_archives_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'hide_archives_tab':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_hide_archives_tab' ) ) {
						// Remember to hide the Archives tab
						woo_ce_update_option( 'hide_archives_tab', 1 );
						$url = add_query_arg( array( 'tab' => 'export', 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'restore_archives_tab':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_restore_archives_tab' ) ) {
						// Remember to show the Archives tab
						woo_ce_update_option( 'hide_archives_tab', 0 );
						$url = add_query_arg( array( 'tab' => 'archive', 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_scheduled_exports_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_scheduled_exports_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_scheduled_exports_prompt', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'hide_scheduled_exports_tab':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_hide_scheduled_exports_tab' ) ) {
						// Remember to hide the Scheduled Exports tab
						woo_ce_update_option( 'hide_scheduled_exports_tab', 1 );
						$url = add_query_arg( array( 'tab' => 'export', 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'restore_scheduled_exports_tab':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_restore_scheduled_exports_tab' ) ) {
						// Remember to show the Scheduled Exports tab
						woo_ce_update_option( 'hide_scheduled_exports_tab', 0 );
						$url = add_query_arg( array( 'tab' => 'scheduled_export', 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'dismiss_duplicate_site_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_duplicate_site_prompt' ) ) {
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'dismiss_duplicate_site_prompt', 1 );
						$message = __( 'Scheduled exports will remain disabled but future duplicate site prompts we will supressed from showing, to re-start scheduled exports open the Settings tab and change the <em>Enable Scheduled Exports</em> option.', 'woocommerce-exporter' );
						woo_cd_admin_notice_html( $message );
					}
					break;

				case 'override_duplicate_site_prompt':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_override_duplicate_site_prompt' ) ) {
						// Remember that we've applied the notice override
						woo_ce_update_option( 'override_duplicate_site_prompt', 1 );
						// Update the site URL hash
						$site_hash = md5( get_option( 'siteurl' ) );
						woo_ce_update_option( 'site_hash', $site_hash );
						// Enable Scheduled Exports
						woo_ce_update_option( 'enable_auto', 1 );
						woo_ce_cron_activation();
						$message = __( 'We\'ve turned scheduled exports back on and will supress future duplicate site prompts for this site.', 'woocommerce-exporter' );
						woo_cd_admin_notice_html( $message );
					}
					break;

				case 'enable_scheduled_exports':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_enable_scheduled_exports' ) ) {
						woo_ce_update_option( 'enable_auto', 1 );
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				// Process scheduled export on next screen load
				case 'override_scheduled_export':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_override_scheduled_export' ) ) {
						// Get the Scheduled Export Post ID
						$scheduled_export = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0 );
						if( !empty( $scheduled_export ) ) {
							// Create a single WP-CRON event that runs immediately
							$time = current_time( 'timestamp', 1 );
							$hook = sprintf( 'woo_ce_auto_export_schedule_%d', $scheduled_export );
							$args = array(
								'id' => $scheduled_export
							);
							wp_schedule_single_event( $time, $hook, $args );
							$url = add_query_arg( array( 'tab' => 'scheduled_export', 'action' => null, 'post' => null, 'scheduled' => 1, '_wpnonce' => null ) );
							wp_redirect( $url );
							exit();
						}
					}
					break;

				// Reset the Transient counters for all export types
				case 'refresh_export_type_counts':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_refresh_export_type_counts' ) ) {
						$transients = array(
							'product',
							'category',
							'tag',
							'brand',
							'order',
							'customer',
							'user',
							'review',
							'coupon',
							'attribute',
							'subscription',
							'product_vendor',
							'commission',
							'shipping_class',
							'ticket'
						);
						foreach( $transients as $transient ) {
							// Delete the existing count Transients
							delete_transient( WOO_CD_PREFIX . '_' . $transient . '_count' );
							// Refresh the count Transients
							woo_ce_get_export_type_count( $transient );
						}
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				case 'refresh_module_counts':
					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_refresh_module_counts' ) ) {
						// Delete the existing count Transients
						delete_transient( WOO_CD_PREFIX . '_modules_all_count' );
						delete_transient( WOO_CD_PREFIX . '_modules_active_count' );
						delete_transient( WOO_CD_PREFIX . '_modules_inactive_count' );
						// Refresh the count Transients
						woo_ce_admin_modules_list();
						$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
					break;

				// Move legacy archives exports to the sed-exports directory within Uploads
				case 'relocate_archived_exports':

					// We need to verify the nonce.
					if( !empty( $_GET ) && check_admin_referer( 'woo_ce_relocate_archived_exports' ) ) {

						// Create the sed-exports directory if it hasn't been
						woo_cd_create_secure_archives_dir();

						$updated = 0;
						if( $files = woo_ce_get_archive_files() ) {
							foreach( $files as $key => $file ) {
								$filepath = get_attached_file( $file->ID );
								// Check for archived exports that have not been moved to sed-exports
								if( strpos( $filepath, 'sed-exports' ) == false ) {
									// Move the export

									// Update the Post meta key _wp_attached_file
									$attached_file = get_post_meta( $file->ID, '_wp_attached_file', true );
									if( !empty( $attached_file ) )
										$attached_file = trailingslashit( 'sed-exports' ) . basename( $attached_file );
									$updated++;
								}
							}
						}

						// Show the response
						$message = sprintf( __( 'That\'s sorted, we\'ve relocated %d export files to the newly created <code>sed-exports</code> folder within the WordPress Uploads directory. Happy exporting!', 'woocommerce-exporter' ), $updated );
						woo_cd_admin_notice_html( $message );
					}
					break;

				// Save skip overview preference
				case 'skip_overview':
					// We need to verify the nonce.
					if( !empty( $_POST ) && check_admin_referer( 'skip_overview', 'woo_ce_skip_overview' ) ) {
						$skip_overview = false;
						if( isset( $_POST['skip_overview'] ) )
							$skip_overview = 1;
						// Remember that we've dismissed this notice
						woo_ce_update_option( 'skip_overview', $skip_overview );

						if( $skip_overview == 1 ) {
							$url = add_query_arg( array( 'tab' => 'export', '_wpnonce' => null ) );
							wp_redirect( $url );
							exit();
						}
					}
					break;

				// This is where the magic happens
				case 'export':

					// Make sure we play nice with other WooCommerce and WordPress exporters
					if( !isset( $_POST['woo_ce_export'] ) )
						return;

					check_admin_referer( 'manual_export', 'woo_ce_export' );

					// Set up the basic export options
					$export = new stdClass();
					$export->cron = 0;
					$export->scheduled_export = 0;
					$export->start_time = time();
					$export->idle_memory_start = woo_ce_current_memory_usage();
					$export->encoding = woo_ce_get_option( 'encoding', get_option( 'blog_charset', 'UTF-8' ) );
					// Reset the Encoding if corrupted
					if( $export->encoding == '' || $export->encoding == false || $export->encoding == 'System default' ) {
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Encoding export option was corrupted, defaulted to UTF-8', 'woocommerce-exporter' ) ) );
						$export->encoding = 'UTF-8';
						woo_ce_update_option( 'encoding', 'UTF-8' );
					}
					$export->delimiter = woo_ce_get_option( 'delimiter', ',' );
					// Reset the Delimiter if corrupted
					if( $export->delimiter == '' || $export->delimiter == false ) {
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Delimiter export option was corrupted, defaulted to ,', 'woocommerce-exporter' ) ) );
						$export->delimiter = ',';
						woo_ce_update_option( 'delimiter', ',' );
					} else if( $export->delimiter == 'TAB' ) {
						$export->delimiter = "\t";
					}
					$export->category_separator = woo_ce_get_option( 'category_separator', '|' );
					// Reset the Category Separator if corrupted
					if( $export->category_separator == '' || $export->category_separator == false ) {
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Category Separator export option was corrupted, defaulted to |', 'woocommerce-exporter' ) ) );
						$export->category_separator = '|';
						woo_ce_update_option( 'category_separator', '|' );
					}
					// Override for line break (LF) support in Category Separator
					if( $export->category_separator == 'LF' )
						$export->category_separator = "\n";
					$export->bom = woo_ce_get_option( 'bom', 1 );
					$export->escape_formatting = woo_ce_get_option( 'escape_formatting', 'all' );
					// Reset the Escape Formatting if corrupted
					if( $export->escape_formatting == '' || $export->escape_formatting == false ) {
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Escape Formatting export option was corrupted, defaulted to all', 'woocommerce-exporter' ) ) );
						$export->escape_formatting = 'all';
						woo_ce_update_option( 'escape_formatting', 'all' );
					}
					$export->header_formatting = woo_ce_get_option( 'header_formatting', 1 );
					$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
					// Reset the Date Format if corrupted
					if( $date_format == '1' || $date_format == '' || $date_format == false ) {
						woo_ce_error_log( sprintf( 'Warning: %s', __( 'Date Format export option was corrupted, defaulted to d/m/Y', 'woocommerce-exporter' ) ) );
						$date_format = 'd/m/Y';
						woo_ce_update_option( 'date_format', $date_format );
					}

					// Save export option changes made on the Export screen
					$export->limit_volume = ( isset( $_POST['limit_volume'] ) ? sanitize_text_field( $_POST['limit_volume'] ) : '' );
					woo_ce_update_option( 'limit_volume', $export->limit_volume );
					if( $export->limit_volume == '' )
						$export->limit_volume = -1;
					$export->offset = ( isset( $_POST['offset'] ) ? sanitize_text_field( $_POST['offset'] ) : '' );
					woo_ce_update_option( 'offset', $export->offset );
					if( $export->offset == '' )
						$export->offset = 0;
					$export->type = ( isset( $_POST['dataset'] ) ? sanitize_text_field( $_POST['dataset'] ) : false );
					if( in_array( $export->type, array( 'product', 'category', 'tag', 'brand', 'order' ) ) ) {
						$export->description_excerpt_formatting = ( isset( $_POST['description_excerpt_formatting'] ) ? absint( $_POST['description_excerpt_formatting'] ) : false );
						if( $export->description_excerpt_formatting <> woo_ce_get_option( 'description_excerpt_formatting' ) )
							woo_ce_update_option( 'description_excerpt_formatting', $export->description_excerpt_formatting );
					}
					if( isset( $_POST['export_format'] ) )
						woo_ce_update_option( 'export_format', sanitize_text_field( $_POST['export_format'] ) );

					// Set default values for all export options to be later passed onto the export process
					$export->fields = array();
					$export->fields_order = false;
					$export->export_format = woo_ce_get_option( 'export_format', 'csv' );
	
					// Product sorting
					$export->gallery_formatting = false;
					$export->gallery_unique = false;
					$export->max_product_gallery = false;
					$export->upsell_formatting = false;
					$export->crosssell_formatting = false;
					$export->variation_formatting = false;
	
					// Order sorting
					if( !empty( $export->type ) ) {
						$export->fields = ( isset( $_POST[$export->type . '_fields'] ) ? array_map( 'sanitize_text_field', $_POST[$export->type . '_fields'] ) : false );
						$export->fields_order = ( isset( $_POST[$export->type . '_fields_order'] ) ? array_map( 'absint', $_POST[$export->type . '_fields_order'] ) : false );
						woo_ce_update_option( 'last_export', $export->type );
					}
					switch( $export->type ) {

						case 'product':
							// Set up dataset specific options
							$export->gallery_formatting = ( isset( $_POST['product_gallery_formatting'] ) ? absint( $_POST['product_gallery_formatting'] ) : false );
							$export->gallery_unique = ( isset( $_POST['product_gallery_unique'] ) ? absint( $_POST['product_gallery_unique'] ) : false );
							$export->upsell_formatting = ( isset( $_POST['product_upsell_formatting'] ) ? absint( $_POST['product_upsell_formatting'] ) : false );
							$export->crosssell_formatting = ( isset( $_POST['product_crosssell_formatting'] ) ? absint( $_POST['product_crosssell_formatting'] ) : false );
							$export->variation_formatting = ( isset( $_POST['variation_formatting'] ) ? absint( $_POST['variation_formatting'] ) : false );
							if( isset( $_POST['max_product_gallery'] ) ) {
								$export->max_product_gallery = absint( $_POST['max_product_gallery'] );
								if( $export->max_product_gallery <> woo_ce_get_option( 'max_product_gallery' ) )
									woo_ce_update_option( 'max_product_gallery', $export->max_product_gallery );
							}
							break;

					}
					$export = apply_filters( 'woo_ce_setup_dataset_options', $export );
					if( !empty( $export->type ) ) {

						$timeout = 600;
						if( isset( $_POST['timeout'] ) ) {
							$timeout = absint( $_POST['timeout'] );
							if( $timeout <> woo_ce_get_option( 'timeout' ) )
								woo_ce_update_option( 'timeout', $timeout );
						}
						if( !ini_get( 'safe_mode' ) ) {
							@set_time_limit( $timeout );
							@ini_set( 'max_execution_time', $timeout );
						}
						@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

						$export->args = array(
							'limit_volume' => $export->limit_volume,
							'offset' => $export->offset,
							'encoding' => $export->encoding,
							'date_format' => $date_format,
							'product_categories' => ( isset( $_POST['product_filter_category'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_category'] ) ) : false ),
							'product_tags' => ( isset( $_POST['product_filter_tag'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_tag'] ) ) : false ),
							'product_brands' => ( isset( $_POST['product_filter_brand'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_brand'] ) ) : false ),
							'product_vendors' => ( isset( $_POST['product_filter_vendor'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_vendor'] ) ) : false ),
							'product_status' => ( isset( $_POST['product_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['product_filter_status'] ) ) : false ),
							'product_type' => ( isset( $_POST['product_filter_type'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['product_filter_type'] ) ) : false ),
							'product_sku' => ( isset( $_POST['product_filter_sku'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['product_filter_sku'] ) ) : false ),
							'product_stock' => ( isset( $_POST['product_filter_stock'] ) ? sanitize_text_field( $_POST['product_filter_stock'] ) : false ),
							'product_featured' => ( isset( $_POST['product_filter_featured'] ) ? sanitize_text_field( $_POST['product_filter_featured'] ) : false ),
							'product_shipping_class' => ( isset( $_POST['product_filter_shipping_class'] ) ? woo_ce_format_product_filters( $_POST['product_filter_shipping_class'] ) : false ),
							'product_language' => ( isset( $_POST['product_filter_language'] ) ? array_map( 'sanitize_text_field', $_POST['product_filter_language'] ) : false ),
							'product_dates_filter' => ( isset( $_POST['product_dates_filter'] ) ? sanitize_text_field( $_POST['product_dates_filter'] ) : false ),
							'product_dates_from' => ( isset( $_POST['product_dates_from'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['product_dates_from'] ) ) : '' ),
							'product_dates_to' => ( isset( $_POST['product_dates_to'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['product_dates_to'] ) ) : '' ),
							'product_orderby' => ( isset( $_POST['product_orderby'] ) ? sanitize_text_field( $_POST['product_orderby'] ) : false ),
							'product_order' => ( isset( $_POST['product_order'] ) ? sanitize_text_field( $_POST['product_order'] ) : false ),
							'category_language' => ( isset( $_POST['category_filter_language'] ) ? array_map( 'sanitize_text_field', $_POST['category_filter_language'] ) : false ),
							'category_orderby' => ( isset( $_POST['category_orderby'] ) ? sanitize_text_field( $_POST['category_orderby'] ) : false ),
							'category_order' => ( isset( $_POST['category_order'] ) ? sanitize_text_field( $_POST['category_order'] ) : false ),
							'tag_language' => ( isset( $_POST['tag_filter_language'] ) ? array_map( 'sanitize_text_field', $_POST['tag_filter_language'] ) : false ),
							'tag_orderby' => ( isset( $_POST['tag_orderby'] ) ? sanitize_text_field( $_POST['tag_orderby'] ) : false ),
							'tag_order' => ( isset( $_POST['tag_order'] ) ? sanitize_text_field( $_POST['tag_order'] ) : false ),
							'brand_orderby' => ( isset( $_POST['brand_orderby'] ) ? sanitize_text_field( $_POST['brand_orderby'] ) : false ),
							'brand_order' => ( isset( $_POST['brand_order'] ) ? sanitize_text_field( $_POST['brand_order'] ) : false ),
							'order_status' => ( isset( $_POST['order_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['order_filter_status'] ) ) : false ),
							'order_dates_filter' => ( isset( $_POST['order_dates_filter'] ) ? sanitize_text_field( $_POST['order_dates_filter'] ) : false ),
							'order_dates_from' => ( isset( $_POST['order_dates_from'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['order_dates_from'] ) ) : '' ),
							'order_dates_to' => ( isset( $_POST['order_dates_to'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['order_dates_to'] ) ) : '' ),
							'order_dates_filter_variable' => ( isset( $_POST['order_dates_filter_variable'] ) ? absint( $_POST['order_dates_filter_variable'] ) : false ),
							'order_dates_filter_variable_length' => ( isset( $_POST['order_dates_filter_variable_length'] ) ? sanitize_text_field( $_POST['order_dates_filter_variable_length'] ) : false ),
							'order_customer' => ( isset( $_POST['order_filter_customer'] ) ? array_map( 'absint', $_POST['order_filter_customer'] ) : false ),
							'order_billing_country' => ( isset( $_POST['order_filter_billing_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_billing_country'] ) : false ),
							'order_shipping_country' => ( isset( $_POST['order_filter_shipping_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping_country'] ) : false ),
							'order_user_roles' => ( isset( $_POST['order_filter_user_role'] ) ? woo_ce_format_user_role_filters( array_map( 'sanitize_text_field', $_POST['order_filter_user_role'] ) ) : false ),
							'order_coupons' => ( isset( $_POST['order_filter_coupon'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_coupon'] ) ) : false ),
							'order_product' => ( isset( $_POST['order_filter_product'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['order_filter_product'] ) ) : false ),
							'order_category' => ( isset( $_POST['order_filter_category'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_category'] ) ) : false ),
							'order_tag' => ( isset( $_POST['order_filter_tag'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_tag'] ) ) : false ),
							'order_brand' => ( isset( $_POST['order_filter_brand'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_brand'] ) ) : false ),
							'order_ids' => ( isset( $_POST['order_filter_id'] ) ? sanitize_text_field( $_POST['order_filter_id'] ) : false ),
							'order_payment' => ( isset( $_POST['order_filter_payment_gateway'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_payment_gateway'] ) : false ),
							'order_shipping' => ( isset( $_POST['order_filter_shipping_method'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping_method'] ) : false ),
							'order_items' => ( isset( $_POST['order_items'] ) ? sanitize_text_field( $_POST['order_items'] ) : false ),
							'order_items_types' => ( isset( $_POST['order_items_types'] ) ? array_map( 'sanitize_text_field', $_POST['order_items_types'] ) : false ),
							'order_flag_notes' => ( isset( $_POST['order_flag_notes'] ) ? absint( $_POST['order_flag_notes'] ) : false ),
							'order_orderby' => ( isset( $_POST['order_orderby'] ) ? sanitize_text_field( $_POST['order_orderby'] ) : false ),
							'order_order' => ( isset( $_POST['order_order'] ) ? sanitize_text_field( $_POST['order_order'] ) : false ),
							'user_roles' => ( isset( $_POST['user_filter_user_role'] ) ? woo_ce_format_user_role_filters( array_map( 'sanitize_text_field', $_POST['user_filter_user_role'] ) ) : false ),
							'user_dates_filter' => ( isset( $_POST['user_dates_filter'] ) ? sanitize_text_field( $_POST['user_dates_filter'] ) : false ),
							'user_dates_from' => ( isset( $_POST['user_dates_from'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['user_dates_from'] ) ) : '' ),
							'user_dates_to' => ( isset( $_POST['user_dates_to'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['user_dates_to'] ) ) : '' ),
							'user_orderby' => ( isset( $_POST['user_orderby'] ) ? sanitize_text_field( $_POST['user_orderby'] ) : false ),
							'user_order' => ( isset( $_POST['user_order'] ) ? sanitize_text_field( $_POST['user_order'] ) : false ),
							'review_orderby' => ( isset( $_POST['review_orderby'] ) ? sanitize_text_field( $_POST['review_orderby'] ) : false ),
							'review_order' => ( isset( $_POST['review_order'] ) ? sanitize_text_field( $_POST['review_order'] ) : false ),
							'coupon_discount_types' => ( isset( $_POST['coupon_filter_discount_type'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['coupon_filter_discount_type'] ) ) : false ),
							'coupon_orderby' => ( isset( $_POST['coupon_orderby'] ) ? sanitize_text_field( $_POST['coupon_orderby'] ) : false ),
							'coupon_order' => ( isset( $_POST['coupon_order'] ) ? sanitize_text_field( $_POST['coupon_order'] ) : false ),
							'subscription_status' => ( isset( $_POST['subscription_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['subscription_filter_status'] ) ) : false ),
							'subscription_product' => ( isset( $_POST['subscription_filter_product'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['subscription_filter_product'] ) ) : false ),
							'subscription_customer' => ( isset( $_POST['subscription_filter_customer'] ) ? array_map( 'absint', $_POST['subscription_filter_customer'] ) : false ),
							'subscription_source' => ( isset( $_POST['subscription_filter_source'] ) ? sanitize_text_field( $_POST['subscription_filter_source'] ) : false ),
							'subscription_orderby' => ( isset( $_POST['subscription_orderby'] ) ? sanitize_text_field( $_POST['subscription_orderby'] ) : false ),
							'subscription_order' => ( isset( $_POST['subscription_order'] ) ? sanitize_text_field( $_POST['subscription_order'] ) : false ),
							'commission_dates_filter' => ( isset( $_POST['commission_dates_filter'] ) ? sanitize_text_field( $_POST['commission_dates_filter'] ) : false ),
							'commission_dates_from' => ( isset( $_POST['commission_dates_from'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['commission_dates_from'] ) ) : '' ),
							'commission_dates_to' => ( isset( $_POST['commission_dates_to'] ) ? woo_ce_format_order_date( sanitize_text_field( $_POST['commission_dates_to'] ) ) : '' ),
							'commission_dates_filter_variable' => ( isset( $_POST['commission_dates_filter_variable'] ) ? absint( $_POST['commission_dates_filter_variable'] ) : false ),
							'commission_dates_filter_variable_length' => ( isset( $_POST['commission_dates_filter_variable_length'] ) ? sanitize_text_field( $_POST['commission_dates_filter_variable_length'] ) : false ),
							'commission_product_vendors' => ( isset( $_POST['commission_filter_product_vendor'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['commission_filter_product_vendor'] ) ) : false ),
							'commission_status' => ( isset( $_POST['commission_filter_commission_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['commission_filter_commission_status'] ) ) : false ),
							'commission_orderby' => ( isset( $_POST['commission_orderby'] ) ? sanitize_text_field( $_POST['commission_orderby'] ) : false ),
							'commission_order' => ( isset( $_POST['commission_order'] ) ? sanitize_text_field( $_POST['commission_order'] ) : false ),
							'shipping_class_orderby' => ( isset( $_POST['shipping_class_orderby'] ) ? sanitize_text_field( $_POST['shipping_class_orderby'] ) : false ),
							'shipping_class_order' => ( isset( $_POST['shipping_class_order'] ) ? sanitize_text_field( $_POST['shipping_class_order'] ) : false )
						);

						if( empty( $export->fields ) ) {
							$message = __( 'No export fields were selected, please try again with at least a single export field.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
							return;
						}
						woo_ce_save_fields( $export->type, $export->fields, $export->fields_order );
						unset( $export->fields_order );

						switch( $export->type ) {

							case 'product':
								// Save dataset export specific options
								if( $export->args['product_status'] <> woo_ce_get_option( 'product_status' ) )
									woo_ce_update_option( 'product_status', $export->args['product_status'] );
								if( $export->args['product_type'] <> woo_ce_get_option( 'product_type' ) )
									woo_ce_update_option( 'product_type', $export->args['product_type'] );
								if( $export->args['product_stock'] <> woo_ce_get_option( 'product_stock' ) )
									woo_ce_update_option( 'product_stock', $export->args['product_stock'] );
								if( $export->args['product_featured'] <> woo_ce_get_option( 'product_featured' ) )
									woo_ce_update_option( 'product_featured', $export->args['product_featured'] );
								if( $export->args['product_orderby'] <> woo_ce_get_option( 'product_orderby' ) )
									woo_ce_update_option( 'product_orderby', $export->args['product_orderby'] );
								if( $export->args['product_order'] <> woo_ce_get_option( 'product_order' ) )
									woo_ce_update_option( 'product_order', $export->args['product_order'] );
								if( $export->gallery_formatting <> woo_ce_get_option( 'gallery_formatting' ) )
									woo_ce_update_option( 'gallery_formatting', $export->gallery_formatting );
								if( $export->gallery_unique <> woo_ce_get_option( 'gallery_unique' ) )
									woo_ce_update_option( 'gallery_unique', $export->gallery_unique );
								if( $export->upsell_formatting <> woo_ce_get_option( 'upsell_formatting' ) )
									woo_ce_update_option( 'upsell_formatting', $export->upsell_formatting );
								if( $export->crosssell_formatting <> woo_ce_get_option( 'crosssell_formatting' ) )
									woo_ce_update_option( 'crosssell_formatting', $export->crosssell_formatting );
								if( $export->variation_formatting <> woo_ce_get_option( 'variation_formatting' ) )
									woo_ce_update_option( 'variation_formatting', $export->variation_formatting );
								break;

							case 'category':
								// Save dataset export specific options
								if( $export->args['category_orderby'] <> woo_ce_get_option( 'category_orderby' ) )
									woo_ce_update_option( 'category_orderby', $export->args['category_orderby'] );
								if( $export->args['category_order'] <> woo_ce_get_option( 'category_order' ) )
									woo_ce_update_option( 'category_order', $export->args['category_order'] );
								break;

							case 'tag':
								// Save dataset export specific options
								if( $export->args['tag_orderby'] <> woo_ce_get_option( 'tag_orderby' ) )
									woo_ce_update_option( 'tag_orderby', $export->args['tag_orderby'] );
								if( $export->args['tag_order'] <> woo_ce_get_option( 'tag_order' ) )
									woo_ce_update_option( 'tag_order', $export->args['tag_order'] );
								break;

							case 'brand':
								// Save dataset export specific options
								if( $export->args['brand_orderby'] <> woo_ce_get_option( 'brand_orderby' ) )
									woo_ce_update_option( 'brand_orderby', $export->args['brand_orderby'] );
								if( $export->args['brand_order'] <> woo_ce_get_option( 'brand_order' ) )
									woo_ce_update_option( 'brand_order', $export->args['brand_order'] );
								break;

							case 'order':
								// Save dataset export specific options
								if( $export->args['order_status'] <> woo_ce_get_option( 'order_status' ) )
									woo_ce_update_option( 'order_status', $export->args['order_status'] );
								if( $export->args['order_user_roles'] <> woo_ce_get_option( 'order_user_roles' ) )
									woo_ce_update_option( 'order_user_roles', $export->args['order_user_roles'] );
								if( $export->args['order_billing_country'] <> woo_ce_get_option( 'order_billing_country' ) )
									woo_ce_update_option( 'order_billing_country', $export->args['order_billing_country'] );
								if( $export->args['order_shipping_country'] <> woo_ce_get_option( 'order_shipping_country' ) )
									woo_ce_update_option( 'order_shipping_country', $export->args['order_shipping_country'] );
								if( $export->args['order_orderby'] <> woo_ce_get_option( 'order_orderby' ) )
									woo_ce_update_option( 'order_orderby', $export->args['order_orderby'] );
								if( $export->args['order_order'] <> woo_ce_get_option( 'order_order' ) )
									woo_ce_update_option( 'order_order', $export->args['order_order'] );

								// These are not persistant arguments
								if( $export->args['order_items'] <> woo_ce_get_option( 'order_items_formatting' ) )
									woo_ce_update_option( 'order_items_formatting', $export->args['order_items'] );
								if( $export->args['order_items_types'] <> woo_ce_get_option( 'order_items_types' ) )
									woo_ce_update_option( 'order_items_types', $export->args['order_items_types'] );
								$export->args['max_order_items'] = ( isset( $_POST['max_order_items'] ) ? sanitize_text_field( $_POST['max_order_items'] ) : false );
								if( $export->args['max_order_items'] <> woo_ce_get_option( 'max_order_items' ) )
									woo_ce_update_option( 'max_order_items', $export->args['max_order_items'] );
								if( $export->args['order_flag_notes'] <> woo_ce_get_option( 'order_flag_notes' ) )
									woo_ce_update_option( 'order_flag_notes', $export->args['order_flag_notes'] );
								break;

							case 'customer':
								// Override some Order details for this export type
								$export->args['order_status'] = ( isset( $_POST['customer_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['customer_filter_status'] ) ) : false );
								$export->args['order_user_roles'] = ( isset( $_POST['customer_filter_user_role'] ) ? woo_ce_format_user_role_filters( array_map( 'sanitize_text_field', $_POST['customer_filter_user_role'] ) ) : false );
								break;

							case 'user':
								// Save dataset export specific options
								if( $export->args['user_orderby'] <> woo_ce_get_option( 'user_orderby' ) )
									woo_ce_update_option( 'user_orderby', $export->args['user_orderby'] );
								if( $export->args['user_order'] <> woo_ce_get_option( 'user_order' ) )
									woo_ce_update_option( 'user_order', $export->args['user_order'] );
								break;

							case 'review':
								// Save dataset export specific options
								if( $export->args['review_orderby'] <> woo_ce_get_option( 'review_orderby' ) )
									woo_ce_update_option( 'review_orderby', $export->args['review_orderby'] );
								if( $export->args['review_order'] <> woo_ce_get_option( 'review_order' ) )
									woo_ce_update_option( 'review_order', $export->args['review_order'] );
								break;

							case 'coupon':
								// Save dataset export specific options
								if( $export->args['coupon_orderby'] <> woo_ce_get_option( 'coupon_orderby' ) )
									woo_ce_update_option( 'coupon_orderby', $export->args['coupon_orderby'] );
								if( $export->args['coupon_order'] <> woo_ce_get_option( 'coupon_order' ) )
									woo_ce_update_option( 'coupon_order', $export->args['coupon_order'] );
								break;

							case 'subscription':
								// Save dataset export specific options
								if( $export->args['subscription_orderby'] <> woo_ce_get_option( 'subscription_orderby' ) )
									woo_ce_update_option( 'subscription_orderby', $export->args['subscription_orderby'] );
								if( $export->args['subscription_order'] <> woo_ce_get_option( 'subscription_order' ) )
									woo_ce_update_option( 'subscription_order', $export->args['subscription_order'] );
								break;

							case 'commission':
								// Save dataset export specific options
								if( $export->args['commission_orderby'] <> woo_ce_get_option( 'commission_orderby' ) )
									woo_ce_update_option( 'commission_orderby', $export->args['commission_orderby'] );
								if( $export->args['commission_order'] <> woo_ce_get_option( 'commission_order' ) )
									woo_ce_update_option( 'commission_order', $export->args['commission_order'] );
								break;

							case 'shipping_class':
								// Save dataset export specific options
								if( $export->args['shipping_class_orderby'] <> woo_ce_get_option( 'shipping_class_orderby' ) )
									woo_ce_update_option( 'shipping_class_orderby', $export->args['shipping_class_orderby'] );
								if( $export->args['shipping_class_order'] <> woo_ce_get_option( 'shipping_class_order' ) )
									woo_ce_update_option( 'shipping_class_order', $export->args['shipping_class_order'] );
								break;

						}

						$export->filename = woo_ce_generate_filename( $export->type );

						$export->idle_memory_end = woo_ce_current_memory_usage();
						$export->end_time = time();

						// Let's spin up PHPExcel for supported export types and formats
						if( in_array( $export->export_format, array( 'csv', 'tsv', 'xls', 'xlsx' ) ) ) {

							$dataset = woo_ce_export_dataset( $export->type );

							// Check if we have data to export
							if( empty( $dataset ) ) {
								$message = __( 'No export entries were found, please try again with different export filters.', 'woocommerce-exporter' );
								if( $export->offset )
									$message .= ' ' . __( 'Try clearing the value set for the Volume Offset under Export Options.', 'woocommerce-exporter' );
								woo_cd_admin_notice( $message, 'error' );
								// Reset the count Transient for this export type in case it is out of date
								delete_transient( WOO_CD_PREFIX . '_' . $export->type . '_count' );
								return;
							}

							// Load up the fatal error notice if we 500, timeout or encounter a fatal PHP error
							add_action( 'shutdown', 'woo_ce_fatal_error' );

							// Check that PHPExcel is where we think it is
							if( file_exists( WOO_CD_PATH . 'classes/PHPExcel.php' ) ) {
								// Check if PHPExcel has already been loaded
								if( !class_exists( 'PHPExcel' ) ) {
									include_once( WOO_CD_PATH . 'classes/PHPExcel.php' );
								} else {
									// Let's try to locate the filepath of the already registered PHPExcel Class
									if( class_exists( 'ReflectionClass' ) ) {
										$reflector = new ReflectionClass( 'PHPExcel' );
										$message = sprintf( __( 'The required PHPExcel library was already loaded by another WordPress Plugin located at %s. If there\'s issues with your export file contact the Plugin author of the mentioned Plugin. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $reflector->getFileName(), $troubleshooting_url );
										woo_cd_admin_notice( $message, 'error' );
										unset( $reflector );
									// Nope, we couldn't detect the filepath so display default notice
									} else {
										$message = sprintf( __( 'The required PHPExcel library was already loaded by another WordPress Plugin, unfortunately however we cannot automatically detect which Plugin. If there\'s issues with your export file you now know where to start looking. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
										woo_cd_admin_notice( $message, 'error' );
									}
								}
							} else {
								$message = sprintf( __( 'We couldn\'t load the PHPExcel library <code>%s</code> within <code>%s</code>, this file should be present. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), 'PHPExcel.php', WOO_CD_PATH . 'classes/...', $troubleshooting_url );
								woo_cd_admin_notice( $message, 'error' );
								return;
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
							$col = 0;
							// Start iterating through the export data
							foreach( $dataset as $data ) {
								$col = 0;
								foreach( array_keys( $export->fields ) as $field ) {
									$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( false );
									// Experimental support for embedding Product images as thumbnails within the XLSX export type
									// @mod - It works but is very memory intensive so is opt-in
									if( apply_filters( 'woo_ce_enable_product_image_embed', false ) ) {
										if( $export->export_format == 'xlsx' && $field == 'image_embed' ) {
											if( !empty( $data->$field ) ) {

												$image_path = $data->$field;
												if( $image_path == false ) {
													$col++;
													continue;
												}

												$objDrawing = new PHPExcel_Worksheet_Drawing();
												$objDrawing->setName( 'Sample image' );
												$objDrawing->setDescription( 'Sample image' );
												$objDrawing->setPath( $image_path );
												$objDrawing->setCoordinates( PHPExcel_Cell::stringFromColumnIndex( $col ) . $row );
												$thumbnail_size = 'shop_thumbnail';
												$shop_thumbnail = ( function_exists( 'wc_get_image_size' ) ? wc_get_image_size( $thumbnail_size ) : array( 'height' => 100 ) );
												$objDrawing->setHeight( ( isset( $shop_thumbnail['height'] ) ? $shop_thumbnail['height'] : 100 ) );
												$objDrawing->setWorksheet( $excel->getActiveSheet() );
												$excel->getActiveSheet()->getRowDimension( $row )->setRowHeight( ( isset( $shop_thumbnail['height'] ) ? $shop_thumbnail['height'] : 100 ) );
												unset( $objDrawing );

												$col++;
												continue;
											}
										}
									}
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

							// Override the export format to CSV if debug mode is enabled
							if( WOO_CD_DEBUG )
								$export->export_format = 'csv';

							// Load our custom Writer for the CSV and TSV file types
							if( in_array( $export->export_format, array( 'csv', 'tsv' ) ) ) {
								// We need to load this after the PHPExcel Class has been created
								woo_cd_load_phpexcel_sed_csv_writer();
							}

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

							}

							// Tack on the file extension
							$export->filename = $export->filename . '.' . $file_extension;

							// Send the export to the factory
							$objWriter = PHPExcel_IOFactory::createWriter( $excel, $php_excel_format );

							// Only write headers if we're not in debug mode
							if( WOO_CD_DEBUG !== true ) {

								// Print to browser
								woo_ce_generate_file_headers( $post_mime_type );
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
								// Print directly to browser, do not save to the WordPress Media
								if( woo_ce_get_option( 'delete_file', 1 ) ) {

									// The end memory usage and time is collected at the very last opportunity prior to the file header being rendered to the screen
									delete_option( WOO_CD_PREFIX . '_exported' );
									$objWriter->save( 'php://output' );

								} else {

									// Save to file and insert to WordPress Media
									$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
									// Check if we were given a temporary filename
									if( $temp_filename == false ) {
										$message = sprintf( __( 'We could not create a temporary export file in <code>%s</code>, ensure that WordPress can read and write files to this directory and try again.', 'woocommerce-exporter' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
										woo_cd_admin_notice( $message, 'error' );
										$url = add_query_arg( array( 'failed' => true, 'message' => urlencode( $message ) ) );
										wp_redirect( $url );
										exit();
									} else {
										$objWriter->save( $temp_filename );
										$bits = file_get_contents( $temp_filename );
									}
									unlink( $temp_filename );

									$post_ID = woo_ce_save_file_attachment( $export->filename, $post_mime_type );
									$upload = wp_upload_bits( $export->filename, null, $bits );
									// Check if the upload succeeded otherwise delete Post and return error notice
									if( ( $post_ID == false ) || $upload['error'] ) {
										wp_delete_attachment( $post_ID, true );
										if( isset( $upload['error'] ) ) {
											$url = add_query_arg( array( 'failed' => true, 'message' => urlencode( $upload['error'] ) ) );
											wp_redirect( $url );
										} else {
											$url = add_query_arg( array( 'failed' => true ) );
											wp_redirect( $url );
										}
										return;
									}

									// Load the WordPress Media API resources
									if( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
										$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
										wp_update_attachment_metadata( $post_ID, $attach_data );
										update_attached_file( $post_ID, $upload['file'] );
										if( !empty( $post_ID ) ) {
											woo_ce_save_file_guid( $post_ID, $export->type, $upload['url'] );
											woo_ce_save_file_details( $post_ID );
										}
									} else {
										woo_ce_error_log( sprintf( '%s: Error: %s', $export->filename, __( 'Could not load image.php within /wp-admin/includes/image.php', 'woocommerce-exporter' ) ) );
									}

									// The end memory usage and time is collected at the very last opportunity prior to the file header being rendered to the screen
									woo_ce_update_file_detail( $post_ID, '_woo_idle_memory_end', woo_ce_current_memory_usage() );
									woo_ce_update_file_detail( $post_ID, '_woo_end_time', time() );
									delete_option( WOO_CD_PREFIX . '_exported' );
									$objWriter->save( 'php://output' );

								}

								// Clean up PHPExcel
								$excel->disconnectWorksheets();
								unset( $objWriter, $excel );
								exit();

							} else {

								// Save to temporary file then dump into export log screen
								$objWriter->setUseBOM( true );
								$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
								// Check if we were given a temporary filename
								if( $temp_filename == false ) {
									$message = sprintf( __( 'We could not create a temporary export file in <code>%s</code>, ensure that WordPress can read and write files to this directory and try again.', 'woocommerce-exporter' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
									woo_cd_admin_notice( $message, 'error' );
								} else {
									$objWriter->save( $temp_filename );
									$bits = file_get_contents( $temp_filename );
								}
								unlink( $temp_filename );

								// Clean up PHPExcel
								$excel->disconnectWorksheets();
								unset( $objWriter, $excel );

								// Save the export contents to the WordPress Transient, base64 encode it to get around Transient storage formatting issues 
								$response = set_transient( WOO_CD_PREFIX . '_debug_log', base64_encode( $bits ), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
								if( $response !== true ) {
									$message = __( 'The export contents were too large to store in a single WordPress transient, use the Volume offset / Limit volume options to reduce the size of your export and try again.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
									woo_cd_admin_notice( $message, 'error' );
								}

							}

							// Remove our fatal error notice to play nice with other Plugins
							remove_action( 'shutdown', 'woo_ce_fatal_error' );

						// Run the default engine for the XML and RSS export formats
						} else if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {

							// Check if SimpleXMLElement is present
							if( !class_exists( 'SED_SimpleXMLElement' ) ) {
								$message = sprintf( __( 'We couldn\'t load the SimpleXMLElement class, the SimpleXMLElement class is required for XML and RSS feed generation. <a href="%s" target="_blank">Need help?</a>', 'woocommerce-exporter' ), $troubleshooting_url );
								woo_cd_admin_notice( $message, 'error' );
								return;
							}

							// Set the file extension and MIME type
							switch( $export->export_format ) {

								case 'xml':
									$file_extension = 'xml';
									$post_mime_type = 'application/xml';
									break;

								case 'rss':
									$file_extension = 'xml';
									$post_mime_type = 'application/rss+xml';
									break;

							}

							// Tack on the file extension
							$export->filename = $export->filename . '.' . $file_extension;

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
								if( woo_ce_get_option( 'xml_attribute_orderby', 1 ) && isset( $export->{$export->type . '_orderby'} ) )
									$xml->addAttribute( 'orderby', $export->{$export->type . '_orderby'} );
								if( woo_ce_get_option( 'xml_attribute_order', 1 ) && isset( $export->{$export->type . '_order'} ) )
									$xml->addAttribute( 'order', $export->{$export->type . '_order'} );
								if( woo_ce_get_option( 'xml_attribute_limit', 1 ) )
									$xml->addAttribute( 'limit', $export->limit_volume );
								if( woo_ce_get_option( 'xml_attribute_offset', 1 ) )
									$xml->addAttribute( 'offset', $export->offset );
								$xml = apply_filters( 'woo_ce_export_xml_before_dataset', $xml );
								$bits = woo_ce_export_dataset( $export->type, $xml );
								$bits = apply_filters( 'woo_ce_export_xml_after_dataset', $bits );
							} else if( $export->export_format == 'rss' ) {
								$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_rss_first_line', '<?xml version="1.0" encoding="%s"?><rss version="2.0"%s/>' ), esc_attr( $export->encoding ), ' xmlns:g="http://base.google.com/ns/1.0"' ) );
								$child = $xml->addChild( apply_filters( 'woo_ce_export_rss_channel_node', 'channel' ) );
								$child->addChild( 'title', woo_ce_get_option( 'rss_title', '' ) );
								$child->addChild( 'link', woo_ce_get_option( 'rss_link', '' ) );
								$child->addChild( 'description', woo_ce_get_option( 'rss_description', '' ) );
								$xml = apply_filters( 'woo_ce_export_rss_before_dataset', $xml );
								$bits = woo_ce_export_dataset( $export->type, $child );
								$bits = apply_filters( 'woo_ce_export_rss_after_dataset', $bits );
							}

							// Check if we have data to export
							if( empty( $bits ) ) {
								$message = __( 'No export entries were found, please try again with different export filters.', 'woocommerce-exporter' );
								woo_cd_admin_notice( $message, 'error' );
								return;
							}

							if( WOO_CD_DEBUG !== true ) {
								// Print directly to browser, do not save to the WordPress Media
								if( woo_ce_get_option( 'delete_file', 1 ) ) {

									// Print directly to browser
									woo_ce_generate_file_headers( $post_mime_type );
									if( $bits = woo_ce_format_xml( $bits ) )
										echo $bits;
									exit();

								} else {

									// Save to file and insert to WordPress Media
									if( $export->filename && $bits ) {
										$post_ID = woo_ce_save_file_attachment( $export->filename, $post_mime_type );
										$bits = woo_ce_format_xml( $bits );
										$upload = wp_upload_bits( $export->filename, null, $bits );
										// Check for issues saving to WordPress Media
										if( ( $post_ID == false ) || !empty( $upload['error'] ) ) {
											wp_delete_attachment( $post_ID, true );
											if( isset( $upload['error'] ) ) {
												$url = add_query_arg( array( 'failed' => true, 'message' => urlencode( $upload['error'] ) ) );
												wp_redirect( $url );
											} else {
												$url = add_query_arg( array( 'failed' => true ) );
												wp_redirect( $url );
											}
											return;
										}
										$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
										wp_update_attachment_metadata( $post_ID, $attach_data );
										update_attached_file( $post_ID, $upload['file'] );
										if( $post_ID ) {
											woo_ce_save_file_guid( $post_ID, $export->type, $upload['url'] );
											woo_ce_save_file_details( $post_ID );
										}
										$export_type = $export->type;
										unset( $export );
	
										// The end memory usage and time is collected at the very last opportunity prior to the XML header being rendered to the screen
										woo_ce_update_file_detail( $post_ID, '_woo_idle_memory_end', woo_ce_current_memory_usage() );
										woo_ce_update_file_detail( $post_ID, '_woo_end_time', time() );
										delete_option( WOO_CD_PREFIX . '_exported' );
	
										// Generate XML header
										woo_ce_generate_file_headers( $post_mime_type );
										unset( $export_type );
	
										// Print file contents to screen
										if( !empty( $upload['file'] ) ) {
											// Check if readfile() is disabled on this host
											$disabled = explode( ',', ini_get( 'disable_functions' ) );
											if( !in_array( 'readfile', $disabled ) ) {
												readfile( $upload['file'] );
											} else {
												// Workaround for disabled readfile on some hosts
												$fp = fopen( $upload['file'], 'rb' );
												fpassthru( $fp );
												fclose( $fp );
												unset( $fp );
											}
											unset( $disabled );
										} else {
											$url = add_query_arg( 'failed', true );
											wp_redirect( $url );
										}
										unset( $upload );
									} else {
										$url = add_query_arg( 'failed', true );
										wp_redirect( $url );
									}
									exit();

								}
							}

						}

					}
					break;

				// Save changes on Settings screen
				case 'save-settings':
					// We need to verify the nonce.
					if( !empty( $_POST ) && check_admin_referer( 'save_settings', 'woo_ce_save_settings' ) ) {
						woo_ce_export_settings_save();
					}
					break;

				// Save changes on Field Editor screen
				case 'save-fields':
					// We need to verify the nonce.
					if( !empty( $_POST ) && check_admin_referer( 'save_fields', 'woo_ce_save_fields' ) ) {
						$fields = ( isset( $_POST['fields'] ) ? array_filter( $_POST['fields'] ) : array() );
						$hidden = ( isset( $_POST['hidden'] ) ? array_filter( $_POST['hidden'] ) : array() );
						$export_type = ( isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '' );
						$export_types = array_keys( woo_ce_get_export_types() );
						// Check we are saving against a valid export type
						if( in_array( $export_type, $export_types ) ) {
							woo_ce_update_option( $export_type . '_labels', $fields );
							woo_ce_update_option( $export_type . '_hidden', $hidden );
							$message = __( 'Field labels have been saved.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message );
						} else {
							$message = __( 'Changes could not be saved as we could not detect a valid export type. Raise this as a Support issue and include what export type you were editing.', 'woocommerce-exporter' );
							woo_cd_admin_notice( $message, 'error' );
						}
					}
					break;

			}

		}
		add_action( 'admin_init', 'woo_cd_admin_init', 11 );

		// HTML templates and form processor for Store Exporter Deluxe screen
		function woo_cd_html_page() {

			// Check the User has the view_woocommerce_reports capability
			if( current_user_can( 'view_woocommerce_reports' ) == false )
				return;

			global $wpdb, $export;

			$title = apply_filters( 'woo_ce_template_header', __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) );
			woo_cd_template_header( $title );
			$action = ( function_exists( 'woo_get_action' ) ? woo_get_action() : false );
			switch( $action ) {

				case 'export':
					if( WOO_CD_DEBUG ) {
						if( false === ( $export_log = get_transient( WOO_CD_PREFIX . '_debug_log' ) ) ) {
							$export_log = __( 'No export entries were found within the debug Transient, please try again with different export filters.', 'woocommerce-exporter' );
						} else {
							// We take the contents of our WordPress Transient and de-base64 it back to CSV format
							$export_log = base64_decode( $export_log );
						}
						delete_transient( WOO_CD_PREFIX . '_debug_log' );
						$output = '
	<h3>' . sprintf( __( 'Export Details: %s', 'woocommerce-exporter' ), esc_attr( $export->filename ) ) . '</h3>
	<p>' . __( 'This prints the $export global that contains the different export options and filters to help reproduce this on another instance of WordPress. Very useful for debugging blank or unexpected exports.', 'woocommerce-exporter' ) . '</p>
	<textarea id="export_log">' . esc_textarea( print_r( $export, true ) ) . '</textarea>
	<hr />';
						if( in_array( $export->export_format, array( 'csv', 'tsv', 'xls' ) ) ) {
							$output .= '
	<script type="text/javascript">
		$j(function() {
			$j(\'#export_sheet\').CSVToTable(\'\', {
				startLine: 0';
							if( in_array( $export->export_format, array( 'tsv', 'xls', 'xlsx' ) ) ) {
								$output .= ',
				separator: "\t"';
							}
							$output .= '
			});
		});
	</script>
	<h3>' . __( 'Export', 'woocommerce-exporter' ) . '</h3>
	<p>' . __( 'We use the <a href="http://code.google.com/p/jquerycsvtotable/" target="_blank"><em>CSV to Table plugin</em></a> to see first hand formatting errors or unexpected values within the export file.', 'woocommerce-exporter' ) . '</p>
	<div id="export_sheet">' . esc_textarea( $export_log ) . '</div>
	<p class="description">' . __( 'This jQuery plugin can fail with <code>\'Item count (#) does not match header count\'</code> notices which simply mean the number of headers detected does not match the number of cell contents.', 'woocommerce-exporter' ) . '</p>
	<hr />';
						}
						$output .= '
	<h3>' . __( 'Export Log', 'woocommerce-exporter' ) . '</h3>
	<p>' . __( 'This prints the raw export contents and is helpful when the jQuery plugin above fails due to major formatting errors.', 'woocommerce-exporter' ) . '</p>
	<textarea id="export_log" wrap="off">' . esc_textarea( $export_log ) . '</textarea>
	<hr />
	';
						echo $output;
					}

					woo_cd_manage_form();
					break;

				case 'update':
					// Save Custom Product Meta
					if( isset( $_POST['custom_products'] ) ) {
						$custom_products = $_POST['custom_products'];
						$custom_products = explode( "\n", trim( $custom_products ) );
						if( !empty( $custom_products ) ) {
							$size = count( $custom_products );
							if( !empty( $size ) ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_products[$i] = sanitize_text_field( trim( stripslashes( $custom_products[$i] ) ) );
								woo_ce_update_option( 'custom_products', $custom_products );
							}
						} else {
							woo_ce_update_option( 'custom_products', '' );
						}
						unset( $custom_products );
					}
					// Save Custom Attributes
					if( isset( $_POST['custom_attributes'] ) ) {
						$custom_attributes = $_POST['custom_attributes'];
						$custom_attributes = explode( "\n", trim( $custom_attributes ) );
						if( !empty( $custom_attributes ) ) {
							$size = count( $custom_attributes );
							if( !empty( $size ) ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_attributes[$i] = sanitize_text_field( trim( stripslashes( $custom_attributes[$i] ) ) );
								woo_ce_update_option( 'custom_attributes', $custom_attributes );
							}
						} else {
							woo_ce_update_option( 'custom_attributes', '' );
						}
					}
					// Save Custom Product Add-ons
					if( isset( $_POST['custom_product_addons'] ) ) {
						$custom_product_addons = $_POST['custom_product_addons'];
						$custom_product_addons = explode( "\n", trim( $custom_product_addons ) );
						if( !empty( $custom_product_addons ) ) {
							$size = count( $custom_product_addons );
							if( !empty( $size ) ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_product_addons[$i] = sanitize_text_field( trim( stripslashes( $custom_product_addons[$i] ) ) );
								woo_ce_update_option( 'custom_product_addons', $custom_product_addons );
							}
						} else {
							woo_ce_update_option( 'custom_product_addons', '' );
						}
						unset( $custom_product_addons );
					}
					// Save Custom Product Tabs
					if( isset( $_POST['custom_product_tabs'] ) ) {
						$custom_product_tabs = $_POST['custom_product_tabs'];
						$custom_product_tabs = explode( "\n", trim( $custom_product_tabs ) );
						if( !empty( $custom_product_tabs ) ) {
							$size = count( $custom_product_tabs );
							if( !empty( $size ) ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_product_tabs[$i] = sanitize_text_field( trim( stripslashes( $custom_product_tabs[$i] ) ) );
								woo_ce_update_option( 'custom_product_tabs', $custom_product_tabs );
							}
						} else {
							woo_ce_update_option( 'custom_product_tabs', '' );
						}
						unset( $custom_product_tabs );
					}
					// Save Custom Order meta
					if( isset( $_POST['custom_orders'] ) ) {
						$custom_orders = $_POST['custom_orders'];
						$custom_orders = explode( "\n", trim( $custom_orders ) );
						if( !empty( $custom_orders ) ) {
							$size = count( $custom_orders );
							if( $size ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_orders[$i] = sanitize_text_field( trim( stripslashes( $custom_orders[$i] ) ) );
								woo_ce_update_option( 'custom_orders', $custom_orders );
							}
						} else {
							woo_ce_update_option( 'custom_orders', '' );
						}
						unset( $custom_orders );
					}
					// Save Custom Order Item meta
					if( isset( $_POST['custom_order_items'] ) ) {
						$custom_order_items = $_POST['custom_order_items'];
						if( !empty( $custom_order_items ) ) {
							$custom_order_items = explode( "\n", trim( $custom_order_items ) );
							$size = count( $custom_order_items );
							if( $size ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_order_items[$i] = sanitize_text_field( trim( stripslashes( $custom_order_items[$i] ) ) );
								woo_ce_update_option( 'custom_order_items', $custom_order_items );
							}
						} else {
							woo_ce_update_option( 'custom_order_items', '' );
						}
						unset( $custom_order_items );
					}
					// Save Custom Product Order Item meta
					if( isset( $_POST['custom_order_products'] ) ) {
						$custom_order_products = $_POST['custom_order_products'];
						if( !empty( $custom_order_products ) ) {
							$custom_order_products = explode( "\n", trim( $custom_order_products ) );
							$size = count( $custom_order_products );
							if( $size ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_order_products[$i] = sanitize_text_field( trim( stripslashes( $custom_order_products[$i] ) ) );
								woo_ce_update_option( 'custom_order_products', $custom_order_products );
							}
						} else {
							woo_ce_update_option( 'custom_order_products', '' );
						}
						unset( $custom_order_products );
					}
					// Save Custom User meta
					if( isset( $_POST['custom_users'] ) ) {
						$custom_users = $_POST['custom_users'];
						$custom_users = explode( "\n", trim( $custom_users ) );
						if( !empty( $custom_order_products ) ) {
							$size = count( $custom_users );
							if( $size ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_users[$i] = sanitize_text_field( trim( stripslashes( $custom_users[$i] ) ) );
								woo_ce_update_option( 'custom_users', $custom_users );
							}
						} else {
							woo_ce_update_option( 'custom_users', '' );
						}
						unset( $custom_users );
					}
					// Save Custom Customer meta
					if( isset( $_POST['custom_customers'] ) ) {
						$custom_customers = $_POST['custom_customers'];
						$custom_customers = explode( "\n", trim( $custom_customers ) );
						if( !empty( $custom_customers ) ) {
							$size = count( $custom_customers );
							if( $size ) {
								for( $i = 0; $i < $size; $i++ )
									$custom_customers[$i] = sanitize_text_field( trim( stripslashes( $custom_customers[$i] ) ) );
								woo_ce_update_option( 'custom_customers', $custom_customers );
							}
						} else {
							woo_ce_update_option( 'custom_customers', '' );
						}
						unset( $custom_customers );
					}

					$message = __( 'Custom Fields saved. You can now select those additional fields from the Export Fields list.', 'woocommerce-exporter' );
					woo_cd_admin_notice_html( $message );
					woo_cd_manage_form();
					break;

				default:
					woo_cd_manage_form();
					break;

			}
			woo_cd_template_footer();

		}

		// HTML template for Export screen
		function woo_cd_manage_form() {

			$tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false );
			// If Skip Overview is set then jump to Export screen
			if( $tab == false && woo_ce_get_option( 'skip_overview', false ) )
				$tab = 'export';

			woo_ce_admin_fail_notices();

			include_once( WOO_CD_PATH . 'templates/admin/tabs.php' );

		}

		/* End of: WordPress Administration */

	} else {

		/* Start of: Storefront */

		function woo_ce_cron() {

			$action = ( function_exists( 'woo_get_action' ) ? woo_get_action() : false );
			// This is where the CRON export magic happens
			if( $action == 'woo_ce-cron' ) {
	
				// Check that Store Exporter is installed and activated or jump out
				if( !function_exists( 'woo_ce_get_option' ) )
					return;

				// Return silent response and record to error log if CRON support is disabled, bad secret key provided or IP whitelist is in effect
				if( woo_ce_get_option( 'enable_cron', 0 ) == 0 ) {
					woo_ce_error_log( sprintf( 'Error: %s', __( 'Failed CRON access, CRON is disabled', 'woocommerce-exporter' ) ) );
					return;
				}
				$key = ( isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '' );
				if( $key <> woo_ce_get_option( 'secret_key', '' ) ) {
					$ip_address = woo_ce_get_visitor_ip_address();
					woo_ce_error_log( sprintf( 'Error: %s', sprintf( __( 'Failed CRON attempt from %s, incorrect secret key', 'woocommerce-exporter' ), $ip_address ) ) );
					return;
				}
				if( $ip_whitelist = apply_filters( 'woo_ce_cron_ip_whitelist', false ) ) {
					$ip_address = woo_ce_get_visitor_ip_address();
					if( !in_array( $ip_address, $ip_whitelist ) ) {
						woo_ce_error_log( sprintf( 'Error: %s', sprintf( __( 'Failed CRON attempt from %s, did not match IP whitelist', 'woocommerce-exporter' ), $ip_address ) ) );
						return;
					}
					unset( $ip_whitelist );
				}

				$gui = ( isset( $_GET['gui'] ) ? absint( $_GET['gui'] ) : 0 );
				$response = ( isset( $_GET['response'] ) ? sanitize_text_field( $_GET['response'] ) : '' );
				// Output to screen in friendly design with on-screen error responses
				if( $gui == 1 ) {
					woo_ce_cron_export( 'gui' );
				// Return export download to browser in different expected formats, uses error_log for error responses
				} else if( $gui == 0 && in_array( $response, array( 'download', 'raw', 'url', 'file', 'email', 'post', 'ftp' ) ) ) {
					switch( $response ) {

						case 'download':
						case 'raw':
						case 'url':
						case 'file':
						case 'email':
						case 'post':
						case 'ftp':
							echo woo_ce_cron_export( $response );
							break;
	
					}
				} else {
					// Return simple binary response
					echo absint( woo_ce_cron_export() );
				}
				exit();

			}

		}
		add_action( 'init', 'woo_ce_cron' );	

		/* End of: Storefront */

	}

	// Run this function within the WordPress Administration and storefront to ensure scheduled exports happen
	function woo_ce_init() {

		include_once( WOO_CD_PATH . 'includes/functions.php' );
		if( function_exists( 'woo_ce_register_scheduled_export_cpt' ) )
			woo_ce_register_scheduled_export_cpt();

		// Check that Store Exporter Deluxe is installed and activated or jump out
		if( !function_exists( 'woo_ce_get_option' ) )
			return;

		// Check that WooCommerce is installed and activated or jump out
		if( !woo_is_woo_activated() )
			return;

		// Check if scheduled exports is enabled
		if( woo_ce_get_option( 'enable_auto', 0 ) == 1 ) {

			// Add custom schedule for automated exports
			add_filter( 'cron_schedules', 'woo_ce_cron_schedules' );

			if( function_exists( 'woo_ce_cron_activation' ) )
				woo_ce_cron_activation();

		}

		// Check if trigger export on New Order is enabled
		if( woo_ce_get_option( 'enable_trigger_new_order', 0 ) == 1 ) {

			// There are other WordPress Actions (woocommerce_new_order, woocommerce_api_create_order) we can hook into but woocommerce_checkout_update_order_meta is the only one where we can access the Order Items
			add_action( 'woocommerce_checkout_update_order_meta', 'woo_ce_trigger_new_order_export', 10, 1 );

		}

		// Every x minutes WP-CRON will run the automated export
		// Check for the legacy as well as new scheduled exports
		if( $scheduled_exports = woo_ce_get_scheduled_exports() ) {
			foreach( $scheduled_exports as $scheduled_export )
				add_action( 'woo_ce_auto_export_schedule_' . $scheduled_export, 'woo_ce_auto_export', 10, 1 );
		}

	}
	add_action( 'init', 'woo_ce_init', 11 );

}
?>