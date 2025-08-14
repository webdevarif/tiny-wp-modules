<?php
/**
 * Settings Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

use TinyWpModules\Admin\Settings_Config;

/**
 * Settings functionality
 */
class Settings {

	/**
	 * Option group name
	 *
	 * @var string
	 */
	private $option_group;

	/**
	 * Option name
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Initialize settings
	 */
	public function init() {
		// Get plugin slug
		global $tiny_wp_modules_plugin;
		$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';
		$this->option_group = $plugin_slug . '_options';
		$this->option_name = Settings_Config::OPTION_NAME;
		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		// Register the setting
		register_setting(
			$this->option_group,
			$this->option_name,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'type' => 'array',
				'default' => Settings_Config::get_default_values(),
			)
		);

		// Ensure the option exists
		if ( ! get_option( $this->option_name ) ) {
			add_option( $this->option_name, Settings_Config::get_default_values() );
		}
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Input data.
	 * @return array Sanitized data.
	 */
	public function sanitize_settings( $input ) {
		// Get existing settings to preserve values not in current form
		$existing_settings = get_option( $this->option_name, array() );
		$sanitized = array();



		// Use centralized sanitization for general settings
		$general_settings = Settings_Config::sanitize_settings( $input );
		$sanitized = array_merge( $sanitized, $general_settings );
		
		// Sanitize Elementor module settings - preserve existing values if not in current form
		$elementor_modules = array(
			'elementor_widgets',
			'elementor_tags',
			'elementor_woocommerce'
		);
		
		foreach ( $elementor_modules as $module ) {
			if ( isset( $input[ $module ] ) ) {
				$sanitized[ $module ] = '1';
			} else {
				$sanitized[ $module ] = isset( $existing_settings[ $module ] ) ? $existing_settings[ $module ] : '0';
			}
		}
		
		// Sanitize all other Elementor-related settings dynamically
		// This preserves existing values for any settings not in the current form
		foreach ( $existing_settings as $key => $value ) {
			// Skip settings we've already handled
			if ( in_array( $key, array( 'enable_faq', 'enable_elementor', 'faq_label', 'faq_slug' ) ) ) {
				continue;
			}
			
			// Skip the main module settings we handled above
			if ( in_array( $key, $elementor_modules ) ) {
				continue;
			}
			
			// For any other Elementor-related setting, preserve existing value if not in current form
			if ( ! isset( $input[ $key ] ) ) {
				$sanitized[ $key ] = $value;
			} else {
				$sanitized[ $key ] = '1';
			}
		}
		
		if ( isset( $input['faq_label'] ) ) {
			$sanitized['faq_label'] = sanitize_text_field( $input['faq_label'] );
		}
		
		if ( isset( $input['faq_slug'] ) ) {
			$sanitized['faq_slug'] = sanitize_title( $input['faq_slug'] );
		}

		// Check if FAQ settings changed and flush rewrite rules
		$old_settings = get_option( $this->option_name, array() );
		$faq_changed = false;
		
		if ( 
			! isset( $old_settings['enable_faq'] ) || 
			$old_settings['enable_faq'] !== $sanitized['enable_faq'] ||
			( isset( $old_settings['faq_slug'] ) && isset( $sanitized['faq_slug'] ) && $old_settings['faq_slug'] !== $sanitized['faq_slug'] )
		) {
			$faq_changed = true;
		}

		if ( $faq_changed ) {
			// Delete the flushed flag so rewrite rules will be flushed
			delete_option( 'tiny_wp_modules_faq_rewrite_flushed' );
		}

		return $sanitized;
	}

	/**
	 * Get setting value
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public function get_setting( $key, $default = null ) {
		$options = get_option( $this->option_name, array() );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}
} 