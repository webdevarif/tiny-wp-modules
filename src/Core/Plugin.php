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
use TinyWpModules\Modules\FAQ_Module;
use TinyWpModules\Advanced\Change_Login_URL;
use TinyWpModules\Advanced\Redirect_After_Login;
use TinyWpModules\Advanced\Redirect_After_Logout;
use TinyWpModules\Advanced\Redirect_404;
use TinyWpModules\Advanced\Password_Protection;
use TinyWpModules\Advanced\SVG_Upload;
use TinyWpModules\Modules\Elementor\Widgets_Module;

/**
 * Main Plugin Class
 */
class Plugin {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin slug
	 *
	 * @var string
	 */
	private $plugin_slug = 'tiny-wp-modules';

	/**
	 * Plugin loader
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * Admin class
	 *
	 * @var Admin
	 */
	private $admin;

	/**
	 * Public class
	 *
	 * @var Public_Handler
	 */
	private $public;

	/**
	 * FAQ Module
	 *
	 * @var FAQ_Module
	 */
	private $faq_module;

	/**
	 * Change Login URL Module
	 *
	 * @var Change_Login_URL
	 */
	private $change_login_url;

	/**
	 * Redirect After Login Module
	 *
	 * @var Redirect_After_Login
	 */
	private $redirect_after_login;

	/**
	 * Redirect After Logout Module
	 *
	 * @var Redirect_After_Logout
	 */
	private $redirect_after_logout;

	/**
	 * Redirect 404 Module
	 *
	 * @var Redirect_404
	 */
	private $redirect_404;

	/**
	 * Password Protection Module
	 *
	 * @var Password_Protection
	 */
	private $password_protection;

	/**
	 * SVG Upload Module
	 *
	 * @var SVG_Upload
	 */
	private $svg_upload;

	/**
	 * Elementor Widget Manager
	 *
	 * @var Widgets_Module
	 */
	private $elementor_widget_manager;

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
	 * Load the required dependencies for this plugin
	 * Classes are now autoloaded via Composer PSR-4 autoloader
	 */
	private function load_dependencies() {
		// Initialize core components
		$this->loader = new Loader();
		$this->admin = new Admin( $this->get_plugin_name(), $this->get_version() );
		$this->public = new Public_Handler( $this->get_plugin_name(), $this->get_version() );
		
		// Initialize modules
		$this->faq_module = new FAQ_Module();
		$this->change_login_url = new Change_Login_URL();
		$this->redirect_after_login = new Redirect_After_Login();
		$this->redirect_after_logout = new Redirect_After_Logout();
		$this->redirect_404 = new Redirect_404();
		$this->password_protection = new Password_Protection();
		$this->svg_upload = new SVG_Upload();

		// Initialize Elementor integration
		$this->elementor_widget_manager = new Widgets_Module();
	}

	/**
	 * Set locale for internationalization
	 */
	private function set_locale() {
		$i18n = new \TinyWpModules\Core\I18n();
		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register admin hooks
	 */
	private function define_admin_hooks() {
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'init', $this->admin, 'register_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $this->admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $this->admin, 'init_settings' );
		$this->loader->add_action( 'admin_init', $this->admin, 'handle_settings_submission' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->admin, 'enqueue_elementor_assets' );
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

	/**
	 * Get plugin slug
	 *
	 * @return string
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Get asset URL
	 *
	 * @param string $path Asset path relative to assets directory.
	 * @return string Full asset URL.
	 */
	public function get_asset_url( $path ) {
		return TINY_WP_MODULES_PLUGIN_URL . 'assets/' . ltrim( $path, '/' );
	}

	/**
	 * Get image URL
	 *
	 * @param string $path Image path relative to assets/images directory.
	 * @return string Full image URL.
	 */
	public function get_image_url( $path ) {
		return $this->get_asset_url( 'images/' . ltrim( $path, '/' ) );
	}

	/**
	 * Get icon URL
	 *
	 * @param string $path Icon path relative to assets/icons directory.
	 * @return string Full icon URL.
	 */
	public function get_icon_url( $path ) {
		return $this->get_asset_url( 'icons/' . ltrim( $path, '/' ) );
	}
} 