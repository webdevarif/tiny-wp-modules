<?php
/**
 * AJAX Handler
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

use TinyWpModules\Core\Updater;

/**
 * AJAX functionality
 */
class Ajax_Handler {

	/**
	 * Initialize AJAX handlers
	 */
	public function __construct() {
		add_action( 'wp_ajax_check_for_updates', array( $this, 'check_for_updates' ) );
	}

	/**
	 * Check for updates via AJAX
	 */
	public function check_for_updates() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'tiny_wp_modules_ajax_nonce' ) ) {
			wp_die( __( 'Security check failed.', 'tiny-wp-modules' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have permission to perform this action.', 'tiny-wp-modules' ) );
		}

		$updater = new Updater();

		// Get latest version
		$latest_version = $updater->get_latest_version();
		$current_version = TINY_WP_MODULES_VERSION;

		if ( $latest_version && version_compare( $current_version, $latest_version, '<' ) ) {
			wp_send_json_success( array(
				'has_update' => true,
				'current_version' => $current_version,
				'latest_version' => $latest_version,
				'message' => sprintf(
					/* translators: %1$s: current version, %2$s: latest version */
					__( 'Update available! Current version: %1$s, Latest version: %2$s', 'tiny-wp-modules' ),
					$current_version,
					$latest_version
				),
			) );
		} else {
			wp_send_json_success( array(
				'has_update' => false,
				'current_version' => $current_version,
				'latest_version' => $latest_version ?: $current_version,
				'message' => __( 'Plugin is up to date.', 'tiny-wp-modules' ),
			) );
		}
	}
} 