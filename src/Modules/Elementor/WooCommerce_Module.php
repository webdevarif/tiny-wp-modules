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
class WooCommerce_Module extends Base_Module {

	/**
	 * Module type identifier
	 *
	 * @var string
	 */
	protected $module_type = 'woocommerce';

	/**
	 * Initialize the module
	 */
	public function __construct() {
		parent::__construct();
		
		// Hook into Elementor's loaded action to register WooCommerce widgets
		add_action( 'elementor/loaded', array( $this, 'register_woocommerce_widgets_with_elementor' ) );
	}

	/**
	 * Register all available WooCommerce widgets
	 */
	protected function register_items() {
		$this->items = array(
			'product_grid_widget' => array(
				'class' => 'Product_Grid_Widget',
				'file' => 'WooCommerce/Product_Grid_Widget.php'
			),
			'product_carousel_widget' => array(
				'class' => 'Product_Carousel_Widget',
				'file' => 'WooCommerce/Product_Carousel_Widget.php'
			),
			'category_showcase_widget' => array(
				'class' => 'Category_Showcase_Widget',
				'file' => 'WooCommerce/Category_Showcase_Widget.php'
			),
			'cart_summary_widget' => array(
				'class' => 'Cart_Summary_Widget',
				'file' => 'WooCommerce/Cart_Summary_Widget.php'
			),
			'wishlist_widget' => array(
				'class' => 'Wishlist_Widget',
				'file' => 'WooCommerce/Wishlist_Widget.php'
			),
			'product_comparison_widget' => array(
				'class' => 'Product_Comparison_Widget',
				'file' => 'WooCommerce/Product_Comparison_Widget.php'
			),
			'product_quick_view_widget' => array(
				'class' => 'Product_Quick_View_Widget',
				'file' => 'WooCommerce/Product_Quick_View_Widget.php'
			),
			'product_reviews_widget' => array(
				'class' => 'Product_Reviews_Widget',
				'file' => 'WooCommerce/Product_Reviews_Widget.php'
			),
			'related_products_widget' => array(
				'class' => 'Related_Products_Widget',
				'file' => 'WooCommerce/Related_Products_Widget.php'
			)
		);

		// Allow other plugins/themes to register additional WooCommerce widgets
		$this->items = apply_filters( 'tiny_wp_modules_elementor_woocommerce_registry', $this->items );
	}



	/**
	 * Initialize a specific WooCommerce widget
	 *
	 * @param string $item_id Item ID.
	 * @param array  $item_data Item configuration data.
	 */
	protected function init_item( $item_id, $item_data ) {
		// Load the widget file if it exists
		$this->load_woocommerce_widget_file( $item_data['file'] );
	}

	/**
	 * Check module dependencies
	 *
	 * @return bool True if all dependencies are met.
	 */
	public function check_dependencies() {
		return class_exists( '\Elementor\Plugin' ) && class_exists( 'WooCommerce' );
	}

	/**
	 * Get dependency warning message
	 *
	 * @return string Warning message.
	 */
	public function get_dependency_warning() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return __( 'Elementor plugin is required to use WooCommerce widgets.', 'tiny-wp-modules' );
		}
		return __( 'WooCommerce plugin is required to use WooCommerce widgets.', 'tiny-wp-modules' );
	}

	/**
	 * Register all enabled WooCommerce widgets with Elementor
	 */
	public function register_woocommerce_widgets_with_elementor() {
		// Check if Elementor is active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		foreach ( $this->items as $item_id => $item_data ) {
			if ( isset( $settings[ $item_id ] ) && $settings[ $item_id ] ) {
				// Load the widget file if it exists
				$this->load_woocommerce_widget_file( $item_data['file'] );
				
				// Register the widget with Elementor
				$class_name = $item_data['class'];
				if ( class_exists( $class_name ) ) {
					add_action( 'elementor/widgets/register', function( $widgets_manager ) use ( $class_name ) {
						$widgets_manager->register( new $class_name() );
					} );
				}
			}
		}
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
	 * Get registered WooCommerce widgets (alias for get_items for backward compatibility)
	 *
	 * @return array Array of registered WooCommerce widgets.
	 */
	public function get_woocommerce_widgets() {
		return $this->items;
	}
}
