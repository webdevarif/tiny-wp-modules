<?php
/**
 * Uninstall Tiny WP Modules
 *
 * @package TinyWpModules
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if user has permission to uninstall
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}

// Include WordPress database functions
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Remove all plugin data
 */
function tiny_wp_modules_remove_all_data() {
	global $wpdb;

	// Remove all plugin options
	$options_to_delete = array(
		'tiny_wp_modules_log_level',
		'tiny_wp_modules_activated',
		'tiny_wp_modules_activation_time',
		'tiny_wp_modules_deactivated',
		'tiny_wp_modules_deactivation_time',
	);

	foreach ( $options_to_delete as $option ) {
		delete_option( $option );
	}

	// Remove all plugin transients
	$transients_to_delete = array(
		'tiny_wp_modules_status',
		'tiny_wp_modules_modules_list',
		'tiny_wp_modules_settings',
	);

	foreach ( $transients_to_delete as $transient ) {
		delete_transient( $transient );
	}

	// Drop plugin tables
	$tables_to_drop = array(
		$wpdb->prefix . 'tiny_wp_modules',
	);

	foreach ( $tables_to_drop as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS $table" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	// Clear any cached data
	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}

	// Remove any scheduled hooks
	wp_clear_scheduled_hook( 'tiny_wp_modules_cleanup' );
	wp_clear_scheduled_hook( 'tiny_wp_modules_maintenance' );
}

// Run the cleanup
tiny_wp_modules_remove_all_data(); 