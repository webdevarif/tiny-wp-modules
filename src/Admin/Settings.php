<?php
/**
 * Settings Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

/**
 * Settings functionality
 */
class Settings {

	/**
	 * Option group name
	 *
	 * @var string
	 */
	private $option_group = 'tiny_wp_modules_options';

	/**
	 * Option name
	 *
	 * @var string
	 */
	private $option_name = 'tiny_wp_modules_settings';

	/**
	 * Initialize settings
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting(
			$this->option_group,
			$this->option_name,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Input array.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		if ( isset( $input['enable_modules'] ) ) {
			$sanitized['enable_modules'] = 1;
		} else {
			$sanitized['enable_modules'] = 0;
		}

		if ( isset( $input['debug_mode'] ) ) {
			$sanitized['debug_mode'] = 1;
		} else {
			$sanitized['debug_mode'] = 0;
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