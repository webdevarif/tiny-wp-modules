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
		add_action( 'wp_ajax_save_elementor_setting', array( $this, 'save_elementor_setting' ) );
		add_action( 'wp_ajax_save_general_settings', array( $this, 'save_general_settings' ) );
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
	
	/**
	 * Save Elementor setting via AJAX
	 */
	public function save_elementor_setting() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'tiny_wp_modules_ajax_nonce' ) ) {
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

		// Get current settings
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Update the specific setting
		$settings[ $setting_id ] = $setting_value;
		
		// Save settings
		$result = update_option( 'tiny_wp_modules_settings', $settings );
		
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
		// Debug logging
		error_log('Tiny WP Modules: save_general_settings called');
		error_log('Tiny WP Modules: POST data: ' . print_r($_POST, true));
		error_log('Tiny WP Modules: Nonce received: ' . ($_POST['nonce'] ?? 'NOT SET'));
		error_log('Tiny WP Modules: Expected nonce action: tiny_wp_modules_save_settings');
		
		// Check nonce - use the form nonce since this is called via form submission
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'tiny_wp_modules_save_settings' ) ) {
			error_log('Tiny WP Modules: Nonce verification failed');
			wp_send_json_error( __( 'Security check failed.', 'tiny-wp-modules' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'tiny-wp-modules' ) );
		}

		// Get current settings
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Update general settings
		$settings['enable_modules'] = isset( $_POST['tiny_wp_modules_settings']['enable_modules'] ) ? '1' : '0';
		$settings['debug_mode'] = isset( $_POST['tiny_wp_modules_settings']['debug_mode'] ) ? '1' : '0';
		$settings['enable_faq'] = isset( $_POST['tiny_wp_modules_settings']['enable_faq'] ) ? '1' : '0';
		$settings['enable_elementor'] = isset( $_POST['tiny_wp_modules_settings']['enable_elementor'] ) ? '1' : '0';
		
		// FAQ settings (only if FAQ is enabled)
		if ( isset( $_POST['tiny_wp_modules_settings']['faq_label'] ) ) {
			$settings['faq_label'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['faq_label'] );
		}
		
		if ( isset( $_POST['tiny_wp_modules_settings']['faq_slug'] ) ) {
			$settings['faq_slug'] = sanitize_title( $_POST['tiny_wp_modules_settings']['faq_slug'] );
		}
		
		// Save settings
		$result = update_option( 'tiny_wp_modules_settings', $settings );
		
		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'General settings saved successfully.', 'tiny-wp-modules' ),
				'settings' => $settings
			) );
		} else {
			wp_send_json_error( __( 'Failed to save general settings.', 'tiny-wp-modules' ) );
		}
	}
} 