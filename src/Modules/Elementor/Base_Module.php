<?php
/**
 * Base Elementor Module Class
 *
 * @package TinyWpModules\Modules\Elementor
 */

namespace TinyWpModules\Modules\Elementor;

/**
 * Abstract base class for Elementor modules
 */
abstract class Base_Module {

	/**
	 * Items registry
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Module type identifier
	 *
	 * @var string
	 */
	protected $module_type;

	/**
	 * Initialize the module
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->register_items();
	}

	/**
	 * Register all available items - must be implemented by child classes
	 */
	abstract protected function register_items();

	/**
	 * Initialize module functionality
	 */
	public function init() {
		// Check if Elementor is active and module is enabled
		if ( ! $this->is_enabled() ) {
			return;
		}

		// Initialize enabled items
		$this->init_enabled_items();
	}

	/**
	 * Check if module is enabled
	 *
	 * @return bool True if enabled.
	 */
	protected function is_enabled() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// First check if Elementor is enabled globally
		if ( ! isset( $settings['enable_elementor'] ) || ! $settings['enable_elementor'] ) {
			return false;
		}
		
		// Then check if this specific module type is enabled
		if ( ! isset( $settings[ 'elementor_' . $this->module_type ] ) || ! $settings[ 'elementor_' . $this->module_type ] ) {
			return false;
		}
		
		// Finally check if all dependencies are met
		return $this->check_dependencies();
	}

	/**
	 * Check module dependencies - must be implemented by child classes
	 *
	 * @return bool True if all dependencies are met.
	 */
	abstract public function check_dependencies();

	/**
	 * Get dependency warning message - must be implemented by child classes
	 *
	 * @return string Warning message.
	 */
	abstract public function get_dependency_warning();

	/**
	 * Check if dependencies are met for settings display
	 *
	 * @return bool True if dependencies are met.
	 */
	public function are_dependencies_met() {
		return $this->check_dependencies();
	}

	/**
	 * Initialize only the enabled items
	 */
	protected function init_enabled_items() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );

		foreach ( $this->items as $item_id => $item_data ) {
			if ( isset( $settings[ $item_id ] ) && $settings[ $item_id ] ) {
				$this->init_item( $item_id, $item_data );
			}
		}
	}

	/**
	 * Get enabled items for settings display
	 *
	 * @return array Array of enabled items with their data.
	 */
	public function get_enabled_items_for_settings() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$enabled_items = array();

		foreach ( $this->items as $item_id => $item_data ) {
			$enabled_items[ $item_id ] = array(
				'id' => $item_id,
				'label' => $this->get_item_label( $item_id ),
				'category' => $this->get_plugin_category(),
				'class' => $item_data['class'],
				'enabled' => isset( $settings[ $item_id ] ) && $settings[ $item_id ]
			);
		}

		return $enabled_items;
	}

	/**
	 * Initialize a specific item - must be implemented by child classes
	 *
	 * @param string $item_id Item ID.
	 * @param array  $item_data Item data.
	 */
	abstract protected function init_item( $item_id, $item_data );

	/**
	 * Get all registered items
	 *
	 * @return array Array of registered items.
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Check if a specific item is enabled
	 *
	 * @param string $item_id Item ID.
	 * @return bool True if enabled.
	 */
	public function is_item_enabled( $item_id ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings[ $item_id ] ) && $settings[ $item_id ];
	}

	/**
	 * Check if a specific item can be enabled (dependencies met)
	 *
	 * @param string $item_id Item ID.
	 * @return bool True if item can be enabled.
	 */
	public function can_item_be_enabled( $item_id ) {
		// Check if the module itself is enabled
		if ( ! $this->is_enabled() ) {
			return false;
		}
		
		// Check if dependencies are met
		return $this->check_dependencies();
	}

	/**
	 * Get item label by item ID
	 *
	 * @param string $item_id Item ID.
	 * @return string Item label.
	 */
	protected function get_item_label( $item_id ) {
		// Convert item ID to readable label
		$label = str_replace( '_', ' ', $item_id );
		$label = str_replace( ' widget', '', $label );
		$label = str_replace( ' tag', '', $label );
		return ucwords( $label );
	}

	/**
	 * Get plugin category name dynamically
	 *
	 * @return string Plugin category name.
	 */
	protected function get_plugin_category() {
		// Get plugin name from WordPress
		$plugin_data = get_plugin_data( TINY_WP_MODULES_PLUGIN_FILE );
		$plugin_name = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : 'Tiny WP Modules';
		
		// Extract the main name part (remove "Tiny WP" if present)
		if ( strpos( $plugin_name, 'Tiny WP' ) === 0 ) {
			$plugin_name = trim( str_replace( 'Tiny WP', '', $plugin_name ) );
		}
		
		// If empty after removal, use default
		if ( empty( $plugin_name ) ) {
			$plugin_name = 'Module';
		}
		
		return 'Tiny ' . $plugin_name;
	}
}
