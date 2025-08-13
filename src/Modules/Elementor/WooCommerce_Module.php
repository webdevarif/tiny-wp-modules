<?php
/**
 * Elementor WooCommerce Module
 *
 * @package TinyWpModules\Modules\Elementor
 */

namespace TinyWpModules\Modules\Elementor;

/**
 * Handles Elementor WooCommerce functionality
 */
class WooCommerce_Module {

	/**
	 * WooCommerce widgets registry
	 *
	 * @var array
	 */
	private $woocommerce_widgets = array();

	/**
	 * Initialize the module
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->register_woocommerce_widgets();
	}

	/**
	 * Register all available WooCommerce widgets
	 */
	private function register_woocommerce_widgets() {
		$this->woocommerce_widgets = array(
			'product_grid_widget' => array(
				'class' => 'Product_Grid_Widget',
				'file' => 'woocommerce/product-grid-widget.php'
			),
			'product_carousel_widget' => array(
				'class' => 'Product_Carousel_Widget',
				'file' => 'woocommerce/product-carousel-widget.php'
			),
			'category_showcase_widget' => array(
				'class' => 'Category_Showcase_Widget',
				'file' => 'woocommerce/category-showcase-widget.php'
			),
			'cart_summary_widget' => array(
				'class' => 'Cart_Summary_Widget',
				'file' => 'woocommerce/cart-summary-widget.php'
			),
			'wishlist_widget' => array(
				'class' => 'Wishlist_Widget',
				'file' => 'woocommerce/wishlist-widget.php'
			),
			'product_comparison_widget' => array(
				'class' => 'Product_Comparison_Widget',
				'file' => 'woocommerce/product-comparison-widget.php'
			)
		);

		// Allow other plugins/themes to register additional WooCommerce widgets
		$this->woocommerce_widgets = apply_filters( 'tiny_wp_modules_elementor_woocommerce_registry', $this->woocommerce_widgets );
	}

	/**
	 * Initialize module functionality
	 */
	public function init() {
		// Check if Elementor is active and WooCommerce is enabled
		if ( ! $this->is_enabled() ) {
			return;
		}

		// Initialize enabled WooCommerce widgets
		$this->init_enabled_woocommerce_widgets();
	}

	/**
	 * Check if module is enabled
	 *
	 * @return bool True if enabled.
	 */
	private function is_enabled() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings['enable_elementor'] ) && $settings['enable_elementor'] &&
			   isset( $settings['elementor_woocommerce'] ) && $settings['elementor_woocommerce'];
	}

	/**
	 * Initialize only the enabled WooCommerce widgets
	 */
	private function init_enabled_woocommerce_widgets() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );

		foreach ( $this->woocommerce_widgets as $widget_id => $widget_data ) {
			if ( isset( $settings[ $widget_id ] ) && $settings[ $widget_id ] ) {
				$this->init_woocommerce_widget( $widget_id, $widget_data );
			}
		}
	}

	/**
	 * Initialize a specific WooCommerce widget
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $widget_data Widget configuration data.
	 */
	private function init_woocommerce_widget( $widget_id, $widget_data ) {
		// Load the widget file if it exists
		$this->load_woocommerce_widget_file( $widget_data['file'] );
	}

	/**
	 * Load WooCommerce widget file if it exists
	 *
	 * @param string $file_path Relative path to widget file.
	 */
	private function load_woocommerce_widget_file( $file_path ) {
		$full_path = plugin_dir_path( __FILE__ ) . $file_path;
		if ( file_exists( $full_path ) ) {
			require_once $full_path;
		}
	}

	/**
	 * Get registered WooCommerce widgets
	 *
	 * @return array Array of registered WooCommerce widgets.
	 */
	public function get_woocommerce_widgets() {
		return $this->woocommerce_widgets;
	}

	/**
	 * Check if a specific WooCommerce widget is enabled
	 *
	 * @param string $widget_id Widget ID.
	 * @return bool True if enabled.
	 */
	public function is_woocommerce_widget_enabled( $widget_id ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings[ $widget_id ] ) && $settings[ $widget_id ];
	}
}
