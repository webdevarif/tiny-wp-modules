<?php
/**
 * Elementor Widgets Module
 *
 * @package TinyWpModules\Modules\Elementor
 */

namespace TinyWpModules\Modules\Elementor;

/**
 * Handles Elementor widgets functionality
 */
class Widgets_Module extends Base_Module {

	/**
	 * Module type identifier
	 *
	 * @var string
	 */
	protected $module_type = 'widgets';

	/**
	 * Initialize the module
	 */
	public function __construct() {
		parent::__construct();
		
		// Hook into Elementor's loaded action to register widgets
		add_action( 'elementor/loaded', array( $this, 'register_widgets_with_elementor' ) );
	}

	/**
	 * Register all available widgets
	 */
	protected function register_items() {
		$this->items = array(
			'cart_icon_widget' => array(
				'class' => 'Cart_Icon_Widget',
				'file' => 'Widgets/Cart_Icon_Widget.php'
			),
			'cart_widget' => array(
				'class' => 'Cart_Widget',
				'file' => 'Widgets/Cart_Widget.php'
			),
			'add_to_cart_widget' => array(
				'class' => 'Add_To_Cart_Widget',
				'file' => 'Widgets/Add_To_Cart_Widget.php'
			),
			'add_to_cart_button_widget' => array(
				'class' => 'Add_To_Cart_Button_Widget',
				'file' => 'Widgets/Add_To_Cart_Button_Widget.php'
			),
			'faq_widget' => array(
				'class' => 'FAQ_Widget',
				'file' => 'Widgets/FAQ_Widget.php'
			),
			'form_check_options_widget' => array(
				'class' => 'Form_Check_Options_Widget',
				'file' => 'Widgets/Form_Check_Options_Widget.php'
			),
			'product_filters_widget' => array(
				'class' => 'Product_Filters_Widget',
				'file' => 'Widgets/Product_Filters_Widget.php'
			),
			'shop_cart_combined_widget' => array(
				'class' => 'Shop_Cart_Combined_Widget',
				'file' => 'Widgets/Shop_Cart_Combined_Widget.php'
			),
			'best_seller_carousel_widget' => array(
				'class' => 'Best_Seller_Carousel_Widget',
				'file' => 'Widgets/Best_Seller_Carousel_Widget.php'
			)
		);

		// Allow other plugins/themes to register additional widgets
		$this->items = apply_filters( 'tiny_wp_modules_elementor_widgets_registry', $this->items );
	}



	/**
	 * Initialize a specific widget
	 *
	 * @param string $item_id Item ID.
	 * @param array  $item_data Item configuration data.
	 */
	protected function init_item( $item_id, $item_data ) {
		// Load the widget file if it exists
		$this->load_widget_file( $item_data['file'] );
	}

	/**
	 * Check module dependencies
	 *
	 * @return bool True if all dependencies are met.
	 */
	public function check_dependencies() {
		return class_exists( '\Elementor\Plugin' );
	}

	/**
	 * Get dependency warning message
	 *
	 * @return string Warning message.
	 */
	public function get_dependency_warning() {
		return __( 'Elementor plugin is required to use widgets.', 'tiny-wp-modules' );
	}

	/**
	 * Register all enabled widgets with Elementor
	 */
	public function register_widgets_with_elementor() {
		// Check if Elementor is active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		foreach ( $this->items as $item_id => $item_data ) {
			if ( isset( $settings[ $item_id ] ) && $settings[ $item_id ] ) {
				// Load the widget file if it exists
				$this->load_widget_file( $item_data['file'] );
				
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
	 * Load widget file if it exists
	 *
	 * @param string $file_path Relative path to widget file.
	 */
	private function load_widget_file( $file_path ) {
		$full_path = plugin_dir_path( __FILE__ ) . $file_path;
		if ( file_exists( $full_path ) ) {
			require_once $full_path;
		}
	}

	/**
	 * Get registered widgets (alias for get_items for backward compatibility)
	 *
	 * @return array Array of registered widgets.
	 */
	public function get_widgets() {
		return $this->items;
	}
}
