<?php
/**
 * AJAX Handler
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

use TinyWpModules\Admin\Settings_Config;

/**
 * AJAX functionality
 */
class Ajax_Handler {

	/**
	 * Initialize AJAX handlers
	 */
	public function __construct() {
		add_action( 'wp_ajax_save_elementor_setting', array( $this, 'save_elementor_setting' ) );
		add_action( 'wp_ajax_save_general_settings', array( $this, 'save_general_settings' ) );
		add_action( 'wp_ajax_force_update_check', array( $this, 'force_update_check' ) );
	}


	/**
	 * Save Elementor setting via AJAX
	 */
	public function save_elementor_setting() {
		// Check nonce
		if ( ! Settings_Config::verify_ajax_nonce( $_POST['nonce'] ?? '' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'tiny-wp-modules' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'tiny-wp-modules' ) );
		}

		// Get setting data
		$setting_id = sanitize_text_field( $_POST['setting_id'] ?? '' );
		$setting_value = sanitize_text_field( $_POST['setting_value'] ?? '0' );

		if ( empty( $setting_id ) ) {
			wp_send_json_error( __( 'Setting ID is required.', 'tiny-wp-modules' ) );
		}

		// Update the specific setting
		$result = Settings_Config::update_setting( $setting_id, $setting_value );
		
		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'Setting saved successfully.', 'tiny-wp-modules' ),
				'setting_id' => $setting_id,
				'setting_value' => $setting_value
			) );
		} else {
			wp_send_json_error( __( 'Failed to save setting.', 'tiny-wp-modules' ) );
		}
	}
	
	/**
	 * Save general settings via AJAX
	 */
	public function save_general_settings() {
		// Check nonce
		if ( ! Settings_Config::verify_nonce( $_POST['nonce'] ?? '' ) ) {
			error_log('Tiny WP Modules: Nonce verification failed');
			wp_send_json_error( __( 'Security check failed.', 'tiny-wp-modules' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'tiny-wp-modules' ) );
		}

		// Get settings from POST data
		$post_settings = $_POST[ Settings_Config::OPTION_NAME ] ?? array();
		
		// Sanitize and save all settings
		$sanitized_settings = Settings_Config::sanitize_settings( $post_settings );
		
		// Save settings
		$result = Settings_Config::update_settings( $sanitized_settings );
		
		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'General settings saved successfully.', 'tiny-wp-modules' ),
				'settings' => $sanitized_settings
			) );
		} else {
			wp_send_json_error( __( 'Failed to save general settings.', 'tiny-wp-modules' ) );
		}
	}
	
	/**
	 * Force update check via AJAX
	 */
	public function force_update_check() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'force_update_check' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'tiny-wp-modules' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'tiny-wp-modules' ) );
		}

		// Force WordPress to check for updates
		delete_site_transient( 'update_plugins' );
		wp_update_plugins();
		
		wp_send_json_success( array(
			'message' => __( 'Update check completed successfully.', 'tiny-wp-modules' )
		) );
	}
} 