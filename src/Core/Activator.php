<?php
/**
 * Plugin Activation Handler
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Plugin Activation Handler
 */
class Activator {

	/**
	 * Plugin activation tasks
	 */
	public function activate() {
		// Set default options
		$this->set_default_options();

		// Create database tables if needed
		$this->create_tables();

		// Flush rewrite rules
		flush_rewrite_rules();

		// Set activation flag
		update_option( 'tiny_wp_modules_activated', true );
		update_option( 'tiny_wp_modules_activation_time', current_time( 'timestamp' ) );

		// Clear any existing caches
		$this->clear_caches();
	}

	/**
	 * Set default plugin options
	 */
	private function set_default_options() {
		$default_options = array(
			'enable_modules' => '1',
			'debug_mode'     => '0',
			'log_level'      => 'info',
		);

		// Only set options if they don't exist
		foreach ( $default_options as $option_name => $default_value ) {
			if ( false === get_option( 'tiny_wp_modules_' . $option_name ) ) {
				add_option( 'tiny_wp_modules_' . $option_name, $default_value );
			}
		}
	}

	/**
	 * Create database tables if needed
	 */
	private function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Example table for modules (you can modify this based on your needs)
		$table_name = $wpdb->prefix . 'tiny_wp_modules';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			module_name varchar(100) NOT NULL,
			module_status varchar(20) DEFAULT 'active',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY module_name (module_name)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Clear any existing caches
	 */
	private function clear_caches() {
		// Clear object cache if available
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		// Clear transients
		delete_transient( 'tiny_wp_modules_status' );
		delete_transient( 'tiny_wp_modules_modules_list' );
	}
} 