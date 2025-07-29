<?php
/**
 * Public Handler Class
 *
 * @package TinyWpModules\Public
 */

namespace TinyWpModules\Public;

/**
 * Public functionality
 */
class Public_Handler {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			TINY_WP_MODULES_PLUGIN_URL . 'assets/css/public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			TINY_WP_MODULES_PLUGIN_URL . 'assets/js/public.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		wp_localize_script(
			$this->plugin_name,
			'tiny_wp_modules_public',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'tiny_wp_modules_public_nonce' ),
			)
		);
	}

	/**
	 * Add meta tags to head
	 */
	public function add_meta_tags() {
		// Add custom meta tags if needed
		echo '<meta name="tiny-wp-modules" content="' . esc_attr( $this->version ) . '" />' . "\n";
	}

	/**
	 * Get plugin name
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
} 