<?php
/**
 * Admin Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

use TinyWpModules\Admin\Settings;
use TinyWpModules\Admin\Elementor_Manager;
use TinyWpModules\Admin\Settings_Config;

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
		$this->settings    = new \TinyWpModules\Admin\Settings();
		$this->ajax_handler = new \TinyWpModules\Admin\Ajax_Handler();
		
		// Hook form submission handler - use admin_post for form submissions
		add_action( 'admin_post_tiny_wp_modules_save_settings', array( $this, 'handle_settings_submission' ) );
		add_action( 'admin_post_nopriv_tiny_wp_modules_save_settings', array( $this, 'handle_settings_submission' ) );
	}

	/**
	 * Register the stylesheets for the admin area
	 */
	public function enqueue_styles() {
		// Use constants for asset URLs
		$admin_css_url = TINY_WP_MODULES_PLUGIN_URL . 'assets/css/admin.css';
		$components_css_url = TINY_WP_MODULES_PLUGIN_URL . 'assets/css/components.css';
		
		wp_enqueue_style(
			$this->plugin_name . '-admin',
			$admin_css_url,
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name . '-components',
			$components_css_url,
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Get plugin menu configuration
	 *
	 * @return array Array of menu pages with their configurations
	 */
	private function get_plugin_menus() {
		// Get plugin slug from main plugin class
		global $tiny_wp_modules_plugin;
		$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';
		
		return array(
			'main' => array(
				'page_title' => $this->plugin_name,
				'menu_title' => '<img src="' . tiny_image( 'logo/logo.svg' ) . '" alt="Tiny WP Modules" style="width: 86px; height: 24px; vertical-align: middle; margin-right: 5px;" />' . __( '', 'tiny-wp-modules' ),
				'capability' => 'manage_options',
				'menu_slug' => $plugin_slug,
				'callback' => array( $this, 'admin_page' ),
				'icon' => tiny_image( 'logo/16x16.png' ),
				'position' => 30
			),
			'welcome' => array(
				'parent_slug' => $plugin_slug,
				'page_title' => __( 'Welcome', 'tiny-wp-modules' ),
				'menu_title' => __( 'Welcome', 'tiny-wp-modules' ),
				'capability' => 'manage_options',
				'menu_slug' => $plugin_slug,
				'callback' => array( $this, 'admin_page' )
			),
			'settings' => array(
				'parent_slug' => $plugin_slug,
				'page_title' => __( 'Settings', 'tiny-wp-modules' ),
				'menu_title' => __( 'Settings', 'tiny-wp-modules' ),
				'capability' => 'manage_options',
				'menu_slug' => $plugin_slug . '-settings',
				'callback' => array( $this, 'settings_page' )
			)
		);
	}

	/**
	 * Get plugin menu pages for script enqueuing
	 *
	 * @return array Array of screen IDs where scripts should be loaded
	 */
	private function get_plugin_menu_pages() {
		$menus = $this->get_plugin_menus();
		$plugin_slug = $menus['main']['menu_slug'];
		
		return array(
			'toplevel_page_' . $plugin_slug,
			$plugin_slug . '_page_' . $plugin_slug . '-settings'
		);
	}

	/**
	 * Check if current screen is a plugin page
	 *
	 * @return bool True if current screen is a plugin page
	 */
	private function is_plugin_page() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}
		
		$plugin_pages = $this->get_plugin_menu_pages();
		$is_plugin_page = in_array( $screen->id, $plugin_pages );
		
		return $is_plugin_page;
	}

	/**
	 * Register admin scripts
	 */
	public function register_scripts() {
		// Use constants for asset URLs
		$script_url = TINY_WP_MODULES_PLUGIN_URL . 'assets/js/admin.js';
		$style_url = TINY_WP_MODULES_PLUGIN_URL . 'assets/css/admin.css';
		
		// Register Alpine.js first
		wp_register_script(
			'alpinejs',
			'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
			array(),
			'3.0.0',
			false
		);

		// Register admin JavaScript
		$registered = wp_register_script(
			$this->plugin_name . '-admin',
			$script_url,
			array( 'jquery', 'alpinejs' ),
			$this->version,
			false
		);

		// Localize script for AJAX
		wp_localize_script(
			$this->plugin_name . '-admin',
			'tiny_wp_modules_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'tiny_wp_modules_ajax_nonce' ),
			)
		);
		

	}

	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		
		// Only load on plugin pages
		if ( ! $this->is_plugin_page() ) {
			return;
		}

		// Enqueue registered scripts
		wp_enqueue_script( $this->plugin_name . '-admin' );
	}

	/**
	 * Enqueue Elementor assets
	 */
	public function enqueue_elementor_assets() {
		// Only enqueue if Elementor is active
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		// Enqueue Elementor widget styles
		wp_enqueue_style(
			$this->plugin_name . '-elementor-widgets',
			TINY_WP_MODULES_PLUGIN_URL . 'assets/css/elementor-widgets.css',
			array(),
			$this->version,
			'all'
		);

		// Enqueue Elementor widget scripts
		wp_enqueue_script(
			$this->plugin_name . '-elementor-widgets',
			TINY_WP_MODULES_PLUGIN_URL . 'assets/js/elementor-widgets.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Localize script for AJAX
		wp_localize_script(
			$this->plugin_name . '-elementor-widgets',
			'tiny_faq_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'tiny_faq_ajax_nonce' ),
			)
		);
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		$menus = $this->get_plugin_menus();
		
		// Add main menu page
		$main_menu = $menus['main'];
		add_menu_page(
			$main_menu['page_title'],
			$main_menu['menu_title'],
			$main_menu['capability'],
			$main_menu['menu_slug'],
			$main_menu['callback'],
			$main_menu['icon'],
			$main_menu['position']
		);

		// Add submenu pages
		foreach ( array( 'welcome', 'settings' ) as $submenu_key ) {
			$submenu = $menus[$submenu_key];
			add_submenu_page(
				$submenu['parent_slug'],
				$submenu['page_title'],
				$submenu['menu_title'],
				$submenu['capability'],
				$submenu['menu_slug'],
				$submenu['callback']
			);
		}



		// Add FAQ submenu if enabled
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		if ( isset( $settings['enable_faq'] ) && $settings['enable_faq'] ) {
			$faq_label = isset( $settings['faq_label'] ) ? $settings['faq_label'] : 'FAQ';
			$faq_slug = isset( $settings['faq_slug'] ) ? $settings['faq_slug'] : 'faq';
			
			// Add FAQ submenu
			add_submenu_page(
				$menus['main']['menu_slug'],
				$faq_label,
				$faq_label,
				'manage_options',
				'edit.php?post_type=' . $faq_slug
			);
		}
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
		// Ensure Settings_Config class is loaded
		if ( ! class_exists( 'TinyWpModules\\Admin\\Settings_Config' ) ) {
			// This should not happen with proper autoloading, but just in case
			require_once TINY_WP_MODULES_PLUGIN_DIR . 'src/Admin/Settings_Config.php';
		}
		
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

	/**
	 * Process user roles from POST data
	 *
	 * @param array $posted_roles Posted roles data.
	 * @return array Processed roles with all roles included.
	 */
	private function process_user_roles( $posted_roles ) {
		// Get all available user roles dynamically
		$all_roles = $this->get_all_user_roles();
		$processed_roles = array();
		
		// Debug logging
		error_log( 'Tiny WP Modules: Processing user roles. Posted roles: ' . print_r( $posted_roles, true ) );
		error_log( 'Tiny WP Modules: All available roles: ' . print_r( $all_roles, true ) );
		
		// Process each role - if it's in POST data, it's checked (value = '1'), if not, it's unchecked
		foreach ( $all_roles as $role_slug => $role_name ) {
			if ( isset( $posted_roles[ $role_slug ] ) && $posted_roles[ $role_slug ] === '1' ) {
				$processed_roles[ $role_slug ] = '1';
			} else {
				$processed_roles[ $role_slug ] = '0';
			}
		}
		
		error_log( 'Tiny WP Modules: Processed roles result: ' . print_r( $processed_roles, true ) );
		
		return $processed_roles;
	}
	
	/**
	 * Get all available user roles dynamically
	 *
	 * @return array Array of role slugs => role names.
	 */
	private function get_all_user_roles() {
		// Get all WordPress user roles dynamically
		return wp_roles()->get_names();
	}

	/**
	 * Handle settings form submission
	 */
	public function handle_settings_submission() {
		
		if ( ! isset( $_POST['tiny_wp_modules_nonce'] ) || ! wp_verify_nonce( $_POST['tiny_wp_modules_nonce'], 'tiny_wp_modules_save_settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get current tab
		$current_tab = isset( $_POST['current_tab'] ) ? sanitize_text_field( $_POST['current_tab'] ) : 'general';

		// Get plugin slug
		global $tiny_wp_modules_plugin;
		$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';



		$settings = Settings_Config::get_all_settings();
		
		// Process settings based on current tab to avoid overwriting other tab data
		switch ( $current_tab ) {
			case 'general':
				// Process general tab settings using centralized configuration
				$post_settings = $_POST[ Settings_Config::OPTION_NAME ] ?? array();
				$general_settings = Settings_Config::sanitize_settings( $post_settings );
				$settings = array_merge( $settings, $general_settings );
				break;
				
			case 'advanced':
				// Advanced tab settings
				$settings['enable_change_login_url'] = isset( $_POST['tiny_wp_modules_settings']['enable_change_login_url'] ) ? '1' : '0';
				$settings['enable_redirect_after_login'] = isset( $_POST['tiny_wp_modules_settings']['enable_redirect_after_login'] ) ? '1' : '0';
				$settings['enable_redirect_after_logout'] = isset( $_POST['tiny_wp_modules_settings']['enable_redirect_after_logout'] ) ? '1' : '0';
				$settings['enable_redirect_404'] = isset( $_POST['tiny_wp_modules_settings']['enable_redirect_404'] ) ? '1' : '0';
				$settings['enable_password_protection'] = isset( $_POST['tiny_wp_modules_settings']['enable_password_protection'] ) ? '1' : '0';
				$settings['enable_svg_upload'] = isset( $_POST['tiny_wp_modules_settings']['enable_svg_upload'] ) ? '1' : '0';
				
				// Change Login URL settings
				if ( isset( $_POST['tiny_wp_modules_settings']['custom_login_slug'] ) ) {
					$settings['custom_login_slug'] = sanitize_title( $_POST['tiny_wp_modules_settings']['custom_login_slug'] );
				}
				
				if ( isset( $_POST['tiny_wp_modules_settings']['allowed_login_paths'] ) ) {
					$settings['allowed_login_paths'] = sanitize_textarea_field( $_POST['tiny_wp_modules_settings']['allowed_login_paths'] );
				}
				
				// Redirect After Login settings
				if ( isset( $_POST['tiny_wp_modules_settings']['redirect_after_login_to_slug'] ) ) {
					$settings['redirect_after_login_to_slug'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['redirect_after_login_to_slug'] );
				}
				
				// Process redirect after login roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['redirect_after_login_for'] ?? array();
				$settings['redirect_after_login_for'] = $this->process_user_roles( $posted_roles );
				
				// Redirect After Logout settings
				if ( isset( $_POST['tiny_wp_modules_settings']['redirect_after_logout_to_slug'] ) ) {
					$settings['redirect_after_logout_to_slug'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['redirect_after_logout_to_slug'] );
				}
				
				// Process redirect after logout roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['redirect_after_logout_for'] ?? array();
				$settings['redirect_after_logout_for'] = $this->process_user_roles( $posted_roles );
				
				// Password Protection settings
				if ( isset( $_POST['tiny_wp_modules_settings']['password_protection_password'] ) ) {
					$settings['password_protection_password'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['password_protection_password'] );
				}
				
				// SVG Upload settings
				$settings['enable_svg_upload'] = isset( $_POST['tiny_wp_modules_settings']['enable_svg_upload'] ) ? '1' : '0';
				
				// Process SVG upload roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['svg_upload_roles'] ?? array();
				$settings['svg_upload_roles'] = $this->process_user_roles( $posted_roles );
				
				// AVIF Upload settings
				$settings['enable_avif_upload'] = isset( $_POST['tiny_wp_modules_settings']['enable_avif_upload'] ) ? '1' : '0';
				
				// Process AVIF upload roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['avif_upload_roles'] ?? array();
				$settings['avif_upload_roles'] = $this->process_user_roles( $posted_roles );
				
				// Image Upload Control settings
				$settings['enable_image_upload_control'] = isset( $_POST['tiny_wp_modules_settings']['enable_image_upload_control'] ) ? '1' : '0';
				if ( isset( $_POST['tiny_wp_modules_settings']['image_max_width'] ) ) {
					$settings['image_max_width'] = intval( $_POST['tiny_wp_modules_settings']['image_max_width'] );
				}
				if ( isset( $_POST['tiny_wp_modules_settings']['image_max_height'] ) ) {
					$settings['image_max_height'] = intval( $_POST['tiny_wp_modules_settings']['image_max_height'] );
				}
				if ( isset( $_POST['tiny_wp_modules_settings']['image_conversion_quality'] ) ) {
					$settings['image_conversion_quality'] = intval( $_POST['tiny_wp_modules_settings']['image_conversion_quality'] );
				}
				
				// Login ID Type settings
				$settings['enable_login_id_type'] = isset( $_POST['tiny_wp_modules_settings']['enable_login_id_type'] ) ? '1' : '0';
				if ( isset( $_POST['tiny_wp_modules_settings']['login_id_type'] ) ) {
					$settings['login_id_type'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['login_id_type'] );
				}
				
				// Maintenance Mode settings
				$settings['enable_maintenance_mode'] = isset( $_POST['tiny_wp_modules_settings']['enable_maintenance_mode'] ) ? '1' : '0';
				if ( isset( $_POST['tiny_wp_modules_settings']['maintenance_page_heading'] ) ) {
					$settings['maintenance_page_heading'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['maintenance_page_heading'] );
				}
				if ( isset( $_POST['tiny_wp_modules_settings']['maintenance_page_description'] ) ) {
					$settings['maintenance_page_description'] = sanitize_textarea_field( $_POST['tiny_wp_modules_settings']['maintenance_page_description'] );
				}
				if ( isset( $_POST['tiny_wp_modules_settings']['maintenance_page_background'] ) ) {
					$settings['maintenance_page_background'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['maintenance_page_background'] );
				}
				if ( isset( $_POST['tiny_wp_modules_settings']['maintenance_bypass_key'] ) ) {
					$settings['maintenance_bypass_key'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['maintenance_bypass_key'] );
				}
				// Process maintenance mode allowed roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['maintenance_allowed_roles'] ?? array();
				$settings['maintenance_allowed_roles'] = $this->process_user_roles( $posted_roles );
				break;
				
			case 'updates':
				// Updates tab settings (if any)
				// Currently no specific settings for updates tab
				break;
				
			case 'elementor':
				// Elementor tab settings
				$post_settings = $_POST[ Settings_Config::OPTION_NAME ] ?? array();
				$elementor_settings = array();
				
				$elementor_settings['enable_elementor'] = isset( $post_settings['enable_elementor'] ) ? '1' : '0';
				
				// Elementor module settings
				$elementor_modules = array(
					'elementor_widgets',
					'elementor_tags',
					'elementor_woocommerce'
				);
				
				foreach ( $elementor_modules as $module ) {
					$elementor_settings[ $module ] = isset( $post_settings[ $module ] ) ? '1' : '0';
				}
				
				// Get all Elementor items dynamically from modules
				$module_types = array( 'widgets', 'tags', 'woocommerce' );
				foreach ( $module_types as $module_type ) {
					$items = Elementor_Manager::get_module_items( $module_type );
					foreach ( $items as $item ) {
						$item_id = $item['id'];
						$elementor_settings[ $item_id ] = isset( $post_settings[ $item_id ] ) ? '1' : '0';
					}
				}
				
				$settings = array_merge( $settings, $elementor_settings );
				break;
		}

		// Debug logging for settings being saved
		error_log( 'Tiny WP Modules: Settings being saved: ' . print_r( $settings, true ) );
		
		$result = Settings_Config::update_settings( $settings );
		
		// Debug logging for result
		error_log( 'Tiny WP Modules: Settings save result: ' . print_r( $result, true ) );
		
		// Redirect to prevent form resubmission, preserving the current tab
		// Since the form is submitted from the settings page, redirect back to settings page
		$redirect_url = add_query_arg( 
			array(
				'settings-updated' => 'true',
				'tab' => $current_tab
			), 
			admin_url( 'admin.php?page=' . $plugin_slug . '-settings' ) 
		);
		
		wp_redirect( $redirect_url );
		exit;
	}
	
} 