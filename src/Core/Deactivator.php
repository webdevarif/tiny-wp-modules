<?php
/**
 * Plugin Deactivation Handler
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Plugin Deactivation Handler
 */
class Deactivator {

	/**
	 * Plugin deactivation tasks
	 */
	public function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Clear caches
		$this->clear_caches();

		// Set deactivation flag
		update_option( 'tiny_wp_modules_deactivated', true );
		update_option( 'tiny_wp_modules_deactivation_time', current_time( 'timestamp' ) );

		// Note: We don't delete options or tables on deactivation
		// This allows users to reactivate without losing their settings
		// Use uninstall.php for complete cleanup if needed
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
		delete_transient( 'tiny_wp_modules_settings' );
	}
} 