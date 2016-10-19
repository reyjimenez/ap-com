<?php
/*
Plugin Name: Visser Labs Updater
Plugin URI: http://www.visser.com.au/
Description: Receive Plugin update notifications to keep your Visser Labs Plugins up to date.
Version: 1.2
Author: Visser Labs
Author URI: http://www.visser.com.au/
License: GPL2
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vl_updater_i18n() {

	load_plugin_textdomain( 'vl-updater', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}
add_action( 'init', 'vl_updater_i18n', 0 );

function vl_get_plugins( $show_inactive = false ) {

	if( !function_exists( 'get_plugins' ) )
		require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );

	$all_plugins = false;
	$vl_plugins = false;
	$cache_key = 'vl_plugins';
	$vl_plugins = get_transient( $cache_key );

	if( !$vl_plugins ) {
		$all_plugins = get_plugins();
		foreach( $all_plugins as $plugin_basename => $plugin_data ) {
			if( $plugin_data['AuthorName'] == 'Visser Labs' )
				$vl_plugins[$plugin_basename] = dirname( $plugin_basename );
		}
		if ( $vl_plugins ) set_transient( $cache_key, $vl_plugins, 3600 );
	}
	return $vl_plugins;

}

function vl_updater_check_for_plugin_update( $checked_data ) {

	if( empty( $checked_data->checked ) )
		return $checked_data;
	
	$args = new stdClass();

	$updatable_plugins = array();
	$plugin_list = vl_updater_make_request( 'get_plugin_list', $args );

	if( is_wp_error( $plugin_list ) )
		return;

	foreach( $plugin_list as $plugin_slug => $plugin ) {

		if( !isset( $plugin['plugin_basename'] ) )
			continue;
		$plugin_basename = $plugin['plugin_basename'];

		if( file_exists( WP_PLUGIN_DIR . '/' . $plugin_basename ) && isset( $checked_data->checked[$plugin_basename] ) ) {
			if( version_compare( $checked_data->checked[$plugin_basename], $plugin['version'], '<' ) ) {
				$checked_data->response[$plugin_basename] = (object)array(
					'version' => $plugin['version'],
					'new_version' => $plugin['version'],
					'slug' => $plugin_slug,
					'date' => $plugin['date'],
					'package' => $plugin['package'],
					'file_name' => $plugin['file_name'],
					'author' => $plugin['author'],
					'url' => $plugin['url'],
					'requires' => $plugin['requires'],
					'tested' => $plugin['tested']
				);
			}
		}

	}
	return $checked_data;

}
add_filter( 'pre_set_site_transient_update_plugins', 'vl_updater_check_for_plugin_update', 20 );

function vl_updater_plugin_api_call( $def, $action, $args ) {

	if( !isset( $args->slug ) )
		return false;

	$vl_plugins = vl_get_plugins();
	$plugin_basename = array_search( $args->slug, $vl_plugins );

	if( !$plugin_basename )
		return false;

	// Get the current version
	$plugin_info = get_site_transient( 'update_plugins' );
	$current_version = $plugin_info->checked[$plugin_basename];
	$args->version = $current_version;

	// Start checking for an update
	$response = vl_updater_make_request( $action, $args );
	return $response;

}
add_filter( 'plugins_api', 'vl_updater_plugin_api_call', 20, 3 );

function vl_updater_make_request( $action, $args ) {

	global $wp_version;

	$update_uri = 'http://updates.visser.com.au/index.php';
	$request_string = array(
		'body' => array(
			'action' => $action, 
			'request' => serialize( $args ),
			'api-key' => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);
	$request = wp_remote_post( $update_uri, $request_string );

	if( is_wp_error( $request ) )
		$response = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'vl-updater' ), $request->get_error_message() );
	else
		$response = unserialize( $request['body'] );

	if ( $response === false )
		$response = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred.', 'vl-updater' ), $request['body'] );
	return $response;

}

if ( !class_exists( 'VL_Updater' ) ) {
	class VL_Updater {

		function init() {}

	}
	$vl_updater = new VL_Updater();
}
?>