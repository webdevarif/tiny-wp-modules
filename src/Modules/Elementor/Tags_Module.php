<?php
/**
 * Elementor Tags Module
 *
 * @package TinyWpModules\Modules\Elementor
 */

namespace TinyWpModules\Modules\Elementor;

/**
 * Handles Elementor tags functionality
 */
class Tags_Module extends Base_Module {

	/**
	 * Module type identifier
	 *
	 * @var string
	 */
	protected $module_type = 'tags';

	/**
	 * Initialize the module
	 */
	public function __construct() {
		parent::__construct();
		
		// Hook into Elementor's loaded action to register tags
		add_action( 'elementor/loaded', array( $this, 'register_tags_with_elementor' ) );
	}

	/**
	 * Register all available tags
	 */
	protected function register_items() {
		$this->items = array(
			'user_info_tag' => array(
				'class' => 'User_Info_Tag',
				'file' => 'Tags/User_Info_Tag.php'
			),
			'post_meta_tag' => array(
				'class' => 'Post_Meta_Tag',
				'file' => 'Tags/Post_Meta_Tag.php'
			),
			'site_info_tag' => array(
				'class' => 'Site_Info_Tag',
				'file' => 'Tags/Site_Info_Tag.php'
			),
			'custom_fields_tag' => array(
				'class' => 'Custom_Fields_Tag',
				'file' => 'Tags/Custom_Fields_Tag.php'
			),
			'query_loop_tag' => array(
				'class' => 'Query_Loop_Tag',
				'file' => 'Tags/Query_Loop_Tag.php'
			),
			'product_price_tag' => array(
				'class' => 'Product_Price_Tag',
				'file' => 'Tags/Product_Price_Tag.php'
			),
			'product_rating_tag' => array(
				'class' => 'Product_Rating_Tag',
				'file' => 'Tags/Product_Rating_Tag.php'
			),
			'cart_total_tag' => array(
				'class' => 'Cart_Total_Tag',
				'file' => 'Tags/Cart_Total_Tag.php'
			)
		);

		// Allow other plugins/themes to register additional tags
		$this->items = apply_filters( 'tiny_wp_modules_elementor_tags_registry', $this->items );
	}



	/**
	 * Initialize a specific tag
	 *
	 * @param string $item_id Item ID.
	 * @param array  $item_data Item configuration data.
	 */
	protected function init_item( $item_id, $item_data ) {
		// Load the tag file if it exists
		$this->load_tag_file( $item_data['file'] );
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
		return __( 'Elementor plugin is required to use dynamic tags.', 'tiny-wp-modules' );
	}

	/**
	 * Register all enabled tags with Elementor
	 */
	public function register_tags_with_elementor() {
		// Check if Elementor is active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		foreach ( $this->items as $item_id => $item_data ) {
			if ( isset( $settings[ $item_id ] ) && $settings[ $item_id ] ) {
				// Load the tag file if it exists
				$this->load_tag_file( $item_data['file'] );
				
				// Register the tag with Elementor
				$class_name = $item_data['class'];
				if ( class_exists( $class_name ) ) {
					add_action( 'elementor/dynamic_tags/register', function( $dynamic_tags_manager ) use ( $class_name ) {
						$dynamic_tags_manager->register( new $class_name() );
					} );
				}
			}
		}
	}

	/**
	 * Load tag file if it exists
	 *
	 * @param string $file_path Relative path to tag file.
	 */
	private function load_tag_file( $file_path ) {
		$full_path = plugin_dir_path( __FILE__ ) . $file_path;
		if ( file_exists( $full_path ) ) {
			require_once $full_path;
		}
	}

	/**
	 * Get registered tags (alias for get_items for backward compatibility)
	 *
	 * @return array Array of registered tags.
	 */
	public function get_tags() {
		return $this->items;
	}
}
