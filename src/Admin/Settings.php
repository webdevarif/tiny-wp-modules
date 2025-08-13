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
	private $option_group;

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
		// Get plugin slug
		global $tiny_wp_modules_plugin;
		$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';
		$this->option_group = $plugin_slug . '_options';
		
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
				'default' => array(
					'enable_modules' => '1',
					'debug_mode' => '0',
					'enable_faq' => '0',
					'enable_elementor' => '0',
				),
			)
		);

		// Ensure the option exists
		if ( ! get_option( $this->option_name ) ) {
			add_option( $this->option_name, array(
				'enable_modules' => '1',
				'debug_mode' => '0',
				'enable_faq' => '0',
				'enable_elementor' => '0',
			) );
		}
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Input data.
	 * @return array Sanitized data.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Sanitize enable modules
		$sanitized['enable_modules'] = isset( $input['enable_modules'] ) ? '1' : '0';

		// Sanitize debug mode
		$sanitized['debug_mode'] = isset( $input['debug_mode'] ) ? '1' : '0';

		// Sanitize FAQ settings
		$sanitized['enable_faq'] = isset( $input['enable_faq'] ) ? '1' : '0';

		// Sanitize Elementor settings
		$sanitized['enable_elementor'] = isset( $input['enable_elementor'] ) ? '1' : '0';
		
		// Sanitize Elementor module settings
		$elementor_modules = array(
			'elementor_widgets',
			'elementor_tags',
			'elementor_woocommerce'
		);
		
		foreach ( $elementor_modules as $module ) {
			$sanitized[ $module ] = isset( $input[ $module ] ) ? '1' : '0';
		}
		
		// Sanitize Elementor widget settings
		$widget_items = array(
			'hero_section_widget',
			'testimonials_widget',
			'pricing_table_widget',
			'team_members_widget',
			'countdown_timer_widget',
			'progress_bars_widget'
		);
		
		foreach ( $widget_items as $widget ) {
			$sanitized[ $widget ] = isset( $input[ $widget ] ) ? '1' : '0';
		}
		
		// Sanitize Elementor tag settings
		$tag_items = array(
			'user_info_tag',
			'post_meta_tag',
			'site_info_tag',
			'custom_fields_tag',
			'query_loop_tag'
		);
		
		foreach ( $tag_items as $tag ) {
			$sanitized[ $tag ] = isset( $input[ $tag ] ) ? '1' : '0';
		}
		
		// Sanitize Elementor WooCommerce settings
		$woocommerce_items = array(
			'product_grid_widget',
			'product_carousel_widget',
			'category_showcase_widget',
			'cart_summary_widget',
			'wishlist_widget',
			'product_comparison_widget'
		);
		
		foreach ( $woocommerce_items as $item ) {
			$sanitized[ $item ] = isset( $input[ $item ] ) ? '1' : '0';
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