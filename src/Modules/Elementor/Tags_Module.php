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
class Tags_Module {

	/**
	 * Tags registry
	 *
	 * @var array
	 */
	private $tags = array();

	/**
	 * Initialize the module
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->register_tags();
	}

	/**
	 * Register all available tags
	 */
	private function register_tags() {
		$this->tags = array(
			'user_info_tag' => array(
				'class' => 'User_Info_Tag',
				'file' => 'tags/user-info-tag.php'
			),
			'post_meta_tag' => array(
				'class' => 'Post_Meta_Tag',
				'file' => 'tags/post-meta-tag.php'
			),
			'site_info_tag' => array(
				'class' => 'Site_Info_Tag',
				'file' => 'tags/site-info-tag.php'
			),
			'custom_fields_tag' => array(
				'class' => 'Custom_Fields_Tag',
				'file' => 'tags/custom-fields-tag.php'
			),
			'query_loop_tag' => array(
				'class' => 'Query_Loop_Tag',
				'file' => 'tags/query-loop-tag.php'
			)
		);

		// Allow other plugins/themes to register additional tags
		$this->tags = apply_filters( 'tiny_wp_modules_elementor_tags_registry', $this->tags );
	}

	/**
	 * Initialize module functionality
	 */
	public function init() {
		// Check if Elementor is active and tags are enabled
		if ( ! $this->is_enabled() ) {
			return;
		}

		// Initialize enabled tags
		$this->init_enabled_tags();
	}

	/**
	 * Check if module is enabled
	 *
	 * @return bool True if enabled.
	 */
	private function is_enabled() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings['enable_elementor'] ) && $settings['enable_elementor'] &&
			   isset( $settings['elementor_tags'] ) && $settings['elementor_tags'];
	}

	/**
	 * Initialize only the enabled tags
	 */
	private function init_enabled_tags() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );

		foreach ( $this->tags as $tag_id => $tag_data ) {
			if ( isset( $settings[ $tag_id ] ) && $settings[ $tag_id ] ) {
				$this->init_tag( $tag_id, $tag_data );
			}
		}
	}

	/**
	 * Initialize a specific tag
	 *
	 * @param string $tag_id Tag ID.
	 * @param array  $tag_data Tag configuration data.
	 */
	private function init_tag( $tag_id, $tag_data ) {
		// Load the tag file if it exists
		$this->load_tag_file( $tag_data['file'] );
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
	 * Get registered tags
	 *
	 * @return array Array of registered tags.
	 */
	public function get_tags() {
		return $this->tags;
	}

	/**
	 * Check if a specific tag is enabled
	 *
	 * @param string $tag_id Tag ID.
	 * @return bool True if enabled.
	 */
	public function is_tag_enabled( $tag_id ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings[ $tag_id ] ) && $settings[ $tag_id ];
	}
}
