<?php
/**
 * Main Plugin Class
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

use TinyWpModules\Admin\Admin;
use TinyWpModules\Public\Public_Handler;
use TinyWpModules\Core\Loader;
use TinyWpModules\Core\I18n;

/**
 * Main Plugin Class
 */
class Plugin {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Loader instance
	 *
	 * @var Loader
	 */
	protected $loader;

	/**
	 * Admin instance
	 *
	 * @var Admin
	 */
	protected $admin;

	/**
	 * Public handler instance
	 *
	 * @var Public_Handler
	 */
	protected $public;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->version     = TINY_WP_MODULES_VERSION;
		$this->plugin_name = 'tiny-wp-modules';
		$this->loader      = new Loader();
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->run();
	}

	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies() {
		// Initialize admin
		$this->admin = new Admin( $this->get_plugin_name(), $this->get_version() );

		// Initialize public handler
		$this->public = new Public_Handler( $this->get_plugin_name(), $this->get_version() );

		// Initialize updater
		$this->updater = new Updater();
	}

	/**
	 * Set locale for internationalization
	 */
	private function set_locale() {
		$i18n = new I18n();
		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register admin hooks
	 */
	private function define_admin_hooks() {
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $this->admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $this->admin, 'init_settings' );
	}

	/**
	 * Register public hooks
	 */
	private function define_public_hooks() {
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $this->public, 'add_meta_tags' );
	}

	/**
	 * Run the loader
	 */
	public function run() {
		$this->loader->run();
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
	 * Get loader
	 *
	 * @return Loader
	 */
	public function get_loader() {
		return $this->loader;
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