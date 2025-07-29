<?php
/**
 * Admin Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

use TinyWpModules\Admin\Settings;

/**
 * Admin functionality
 */
class Admin {

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
	 * Settings instance
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = new Settings();
		$this->ajax_handler = new Ajax_Handler();
	}

	/**
	 * Register the stylesheets for the admin area
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			TINY_WP_MODULES_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$this->version,
			'all'
		);

		// Enqueue components CSS
		wp_enqueue_style(
			$this->plugin_name . '-components',
			TINY_WP_MODULES_PLUGIN_URL . 'assets/css/components.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			TINY_WP_MODULES_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		wp_localize_script(
			$this->plugin_name,
			'tiny_wp_modules_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'tiny_wp_modules_ajax_nonce' ),
			)
		);
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Tiny WP Modules', 'tiny-wp-modules' ),
			__( 'Tiny Modules', 'tiny-wp-modules' ),
			'manage_options',
			'tiny-wp-modules',
			array( $this, 'admin_page' ),
			'dashicons-admin-generic',
			30
		);

		add_submenu_page(
			'tiny-wp-modules',
			__( 'Welcome', 'tiny-wp-modules' ),
			__( 'Welcome', 'tiny-wp-modules' ),
			'manage_options',
			'tiny-wp-modules',
			array( $this, 'admin_page' )
		);

		add_submenu_page(
			'tiny-wp-modules',
			__( 'Settings', 'tiny-wp-modules' ),
			__( 'Settings', 'tiny-wp-modules' ),
			'manage_options',
			'tiny-wp-modules-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Admin page callback
	 */
	public function admin_page() {
		include TINY_WP_MODULES_PLUGIN_DIR . 'templates/admin/admin-page.php';
	}

	/**
	 * Settings page callback
	 */
	public function settings_page() {
		// Get current tab
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		
		include TINY_WP_MODULES_PLUGIN_DIR . 'templates/admin/settings-page.php';
	}

	/**
	 * Initialize settings
	 */
	public function init_settings() {
		$this->settings->init();
	}
} 