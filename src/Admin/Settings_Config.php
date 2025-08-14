<?php
/**
 * Settings Configuration
 *
 * Centralized configuration for all plugin settings
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

/**
 * Settings Configuration Class
 * 
 * This class provides a centralized way to manage all plugin settings,
 * making them easily reusable and maintainable.
 */
class Settings_Config {

	/**
	 * Option name for storing settings
	 */
	const OPTION_NAME = 'tiny_wp_modules_settings';

	/**
	 * Nonce action for saving settings
	 */
	const NONCE_ACTION = 'tiny_wp_modules_save_settings';

	/**
	 * AJAX nonce action
	 */
	const AJAX_NONCE_ACTION = 'tiny_wp_modules_ajax_nonce';

	/**
	 * All available settings with their configuration
	 *
	 * @return array Settings configuration
	 */
	public static function get_settings_config(): array {
		return array(
			// General Settings
			'enable_faq' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Enable FAQ', 'tiny-wp-modules' ),
				'description' => __( 'Enable FAQ functionality for your website.', 'tiny-wp-modules' ),
				'category' => 'general',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'enable_elementor' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Enable Elementor', 'tiny-wp-modules' ),
				'description' => __( 'Enable Elementor integration and widgets.', 'tiny-wp-modules' ),
				'category' => 'general',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'faq_label' => array(
				'type' => 'text',
				'default' => __( 'FAQ', 'tiny-wp-modules' ),
				'label' => __( 'FAQ Label', 'tiny-wp-modules' ),
				'description' => __( 'Label for FAQ functionality.', 'tiny-wp-modules' ),
				'category' => 'faq',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' => 'enable_faq',
			),
			'faq_slug' => array(
				'type' => 'text',
				'default' => 'faq',
				'label' => __( 'FAQ Slug', 'tiny-wp-modules' ),
				'description' => __( 'URL slug for FAQ pages.', 'tiny-wp-modules' ),
				'category' => 'faq',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_title',
				'dependency' => 'enable_faq',
			),
			
			// Advanced Settings
			'enable_change_login_url' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Change Login URL', 'tiny-wp-modules' ),
				'description' => __( 'Change the default WordPress login URL for enhanced security.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'custom_login_slug' => array(
				'type' => 'text',
				'default' => 'backend',
				'label' => __( 'Custom Login Slug', 'tiny-wp-modules' ),
				'description' => __( 'Custom slug for the login URL.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_title',
				'dependency' => 'enable_change_login_url',
			),
			'allowed_login_paths' => array(
				'type' => 'textarea',
				'default' => '',
				'label' => __( 'Allowed Login Paths', 'tiny-wp-modules' ),
				'description' => __( 'Paths that are exempted from login URL restrictions.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_textarea_field',
				'dependency' => 'enable_change_login_url',
			),
			'enable_redirect_after_login' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Redirect After Login', 'tiny-wp-modules' ),
				'description' => __( 'Configure custom redirect URL for user roles after login.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'redirect_after_login_to_slug' => array(
				'type' => 'text',
				'default' => '',
				'label' => __( 'Redirect After Login To', 'tiny-wp-modules' ),
				'description' => __( 'URL slug to redirect to after login.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' => 'enable_redirect_after_login',
			),
			'redirect_after_login_for' => array(
				'type' => 'array',
				'default' => array(),
				'label' => __( 'Redirect After Login For', 'tiny-wp-modules' ),
				'description' => __( 'User roles to apply this redirect.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_array',
				'dependency' => 'enable_redirect_after_login',
			),
			'enable_redirect_after_logout' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Redirect After Logout', 'tiny-wp-modules' ),
				'description' => __( 'Configure custom redirect URL for user roles after logout.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'redirect_after_logout_to_slug' => array(
				'type' => 'text',
				'default' => '',
				'label' => __( 'Redirect After Logout To', 'tiny-wp-modules' ),
				'description' => __( 'URL slug to redirect to after logout.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' => 'enable_redirect_after_logout',
			),
			'redirect_after_logout_for' => array(
				'type' => 'array',
				'default' => array(),
				'label' => __( 'Redirect After Logout For', 'tiny-wp-modules' ),
				'description' => __( 'User roles to apply this redirect.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_array',
				'dependency' => 'enable_redirect_after_logout',
			),
			'enable_redirect_404' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Redirect 404', 'tiny-wp-modules' ),
				'description' => __( 'Automatically redirect 404 pages to the homepage.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'enable_password_protection' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'Password Protection', 'tiny-wp-modules' ),
				'description' => __( 'Password-protect the entire site to hide content from public view.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'password_protection_password' => array(
				'type' => 'password',
				'default' => 'secret',
				'label' => __( 'Password Protection Password', 'tiny-wp-modules' ),
				'description' => __( 'Password to protect the site.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' => 'enable_password_protection',
			),
			'enable_svg_upload' => array(
				'type' => 'boolean',
				'default' => '0',
				'label' => __( 'SVG Upload', 'tiny-wp-modules' ),
				'description' => __( 'Allow SVG file uploads to the WordPress media library with security sanitization.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_boolean',
			),
			'svg_upload_roles' => array(
				'type' => 'array',
				'default' => array(),
				'label' => __( 'SVG Upload Roles', 'tiny-wp-modules' ),
				'description' => __( 'User roles that can upload SVG files.', 'tiny-wp-modules' ),
				'category' => 'advanced',
				'capability' => 'manage_options',
				'sanitize_callback' => 'sanitize_array',
				'dependency' => 'enable_svg_upload',
			),
		);
	}

	/**
	 * Get settings by category
	 *
	 * @param string $category Category name.
	 * @return array Settings for the specified category.
	 */
	public static function get_settings_by_category( string $category ): array {
		$config = self::get_settings_config();
		$category_settings = array();

		foreach ( $config as $key => $setting ) {
			if ( $setting['category'] === $category ) {
				$category_settings[ $key ] = $setting;
			}
		}

		return $category_settings;
	}

	/**
	 * Get all setting keys
	 *
	 * @return array Array of setting keys.
	 */
	public static function get_setting_keys(): array {
		return array_keys( self::get_settings_config() );
	}

	/**
	 * Get default values for all settings
	 *
	 * @return array Default values.
	 */
	public static function get_default_values(): array {
		$config = self::get_settings_config();
		$defaults = array();

		foreach ( $config as $key => $setting ) {
			$defaults[ $key ] = $setting['default'];
		}

		return $defaults;
	}

	/**
	 * Get setting value with fallback to default
	 *
	 * @param string $key Setting key.
	 * @param mixed  $default Default value if setting doesn't exist.
	 * @return mixed Setting value or default.
	 */
	public static function get_setting( string $key, $default = null ) {
		$settings = get_option( self::OPTION_NAME, array() );
		$config = self::get_settings_config();

		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		if ( $default !== null ) {
			return $default;
		}

		return $config[ $key ]['default'] ?? null;
	}

	/**
	 * Update a single setting
	 *
	 * @param string $key Setting key.
	 * @param mixed  $value Setting value.
	 * @return bool Success status.
	 */
	public static function update_setting( string $key, $value ): bool {
		$settings = get_option( self::OPTION_NAME, array() );
		$settings[ $key ] = $value;
		return update_option( self::OPTION_NAME, $settings );
	}

	/**
	 * Update multiple settings at once
	 *
	 * @param array $settings Array of settings to update.
	 * @return bool Success status.
	 */
	public static function update_settings( array $settings ): bool {
		$current_settings = get_option( self::OPTION_NAME, array() );
		$updated_settings = array_merge( $current_settings, $settings );
		return update_option( self::OPTION_NAME, $updated_settings );
	}

	/**
	 * Delete a setting
	 *
	 * @param string $key Setting key.
	 * @return bool Success status.
	 */
	public static function delete_setting( string $key ): bool {
		$settings = get_option( self::OPTION_NAME, array() );
		unset( $settings[ $key ] );
		return update_option( self::OPTION_NAME, $settings );
	}

	/**
	 * Get all current settings
	 *
	 * @return array All current settings.
	 */
	public static function get_all_settings(): array {
		return get_option( self::OPTION_NAME, self::get_default_values() );
	}

	/**
	 * Check if a setting is enabled
	 *
	 * @param string $key Setting key.
	 * @return bool True if enabled.
	 */
	public static function is_enabled( string $key ): bool {
		$value = self::get_setting( $key );
		return $value === '1' || $value === true || $value === 1;
	}

	/**
	 * Get setting configuration
	 *
	 * @param string $key Setting key.
	 * @return array|null Setting configuration or null if not found.
	 */
	public static function get_setting_config( string $key ) {
		$config = self::get_settings_config();
		return $config[ $key ] ?? null;
	}

	/**
	 * Sanitize all settings
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized settings.
	 */
	public static function sanitize_settings( array $input ): array {
		$config = self::get_settings_config();
		$sanitized = array();

		foreach ( $config as $key => $setting ) {
			if ( isset( $input[ $key ] ) ) {
				$sanitized[ $key ] = self::sanitize_setting( $key, $input[ $key ] );
			} else {
				// For boolean settings, set to '0' if not present
				if ( $setting['type'] === 'boolean' ) {
					$sanitized[ $key ] = '0';
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize a single setting
	 *
	 * @param string $key Setting key.
	 * @param mixed  $value Setting value.
	 * @return mixed Sanitized value.
	 */
	public static function sanitize_setting( string $key, $value ) {
		$config = self::get_setting_config( $key );
		if ( ! $config ) {
			return $value;
		}

		$callback = $config['sanitize_callback'] ?? 'sanitize_text_field';

		switch ( $callback ) {
			case 'sanitize_boolean':
				return $value ? '1' : '0';
			case 'sanitize_text_field':
				return sanitize_text_field( $value );
			case 'sanitize_title':
				return sanitize_title( $value );
			case 'sanitize_email':
				return sanitize_email( $value );
			case 'sanitize_url':
				return sanitize_url( $value );
			case 'sanitize_textarea_field':
				return sanitize_textarea_field( $value );
			case 'sanitize_array':
				if ( is_array( $value ) ) {
					return array_map( 'sanitize_text_field', $value );
				}
				return array();
			case 'sanitize_password':
				return sanitize_text_field( $value );
			default:
				if ( function_exists( $callback ) ) {
					return call_user_func( $callback, $value );
				}
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Get form field name for a setting
	 *
	 * @param string $key Setting key.
	 * @return string Form field name.
	 */
	public static function get_field_name( string $key ): string {
		return self::OPTION_NAME . '[' . $key . ']';
	}

	/**
	 * Get form field ID for a setting
	 *
	 * @param string $key Setting key.
	 * @return string Form field ID.
	 */
	public static function get_field_id( string $key ): string {
		return 'setting_' . $key;
	}

	/**
	 * Get nonce field HTML
	 *
	 * @return string Nonce field HTML.
	 */
	public static function get_nonce_field(): string {
		return wp_nonce_field( self::NONCE_ACTION, '_wpnonce', true, false );
	}

	/**
	 * Verify nonce
	 *
	 * @param string $nonce Nonce value.
	 * @return bool True if valid.
	 */
	public static function verify_nonce( string $nonce ): bool {
		return wp_verify_nonce( $nonce, self::NONCE_ACTION );
	}

	/**
	 * Get AJAX nonce
	 *
	 * @return string AJAX nonce.
	 */
	public static function get_ajax_nonce(): string {
		return wp_create_nonce( self::AJAX_NONCE_ACTION );
	}

	/**
	 * Verify AJAX nonce
	 *
	 * @param string $nonce Nonce value.
	 * @return bool True if valid.
	 */
	public static function verify_ajax_nonce( string $nonce ): bool {
		return wp_verify_nonce( $nonce, self::AJAX_NONCE_ACTION );
	}
}
