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
		
		error_log('Tiny WP Modules: Admin constructor called, form handler hooked to admin_post');
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
			error_log( 'Tiny WP Modules: No screen found' );
			return false;
		}
		
		$plugin_pages = $this->get_plugin_menu_pages();
		$is_plugin_page = in_array( $screen->id, $plugin_pages );
		
		error_log( 'Tiny WP Modules: Screen ID: ' . $screen->id );
		error_log( 'Tiny WP Modules: Plugin pages: ' . implode( ', ', $plugin_pages ) );
		error_log( 'Tiny WP Modules: Is plugin page: ' . ( $is_plugin_page ? 'YES' : 'NO' ) );
		
		return $is_plugin_page;
	}

	/**
	 * Register admin scripts
	 */
	public function register_scripts() {
		// Use constants for asset URLs
		$script_url = TINY_WP_MODULES_PLUGIN_URL . 'assets/js/admin.js';
		$style_url = TINY_WP_MODULES_PLUGIN_URL . 'assets/css/admin.css';
		
		error_log( 'Tiny WP Modules: Registering script - ' . $script_url );
		error_log( 'Tiny WP Modules: Plugin URL constant: ' . TINY_WP_MODULES_PLUGIN_URL );
		
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
		
		// Debug registration
		error_log( 'Tiny WP Modules: Script registration result: ' . ( $registered ? 'SUCCESS' : 'FAILED' ) );
	}

	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		
		// Debug enqueue attempt
		error_log( 'Tiny WP Modules: Enqueue attempt - Screen: ' . ( $screen ? $screen->id : 'null' ) );
		
		// Temporarily disable screen check for debugging
		/*
		// Only load on plugin pages
		if ( ! $this->is_plugin_page() ) {
			error_log( 'Tiny WP Modules: Script not enqueued - not a plugin page' );
			return;
		}
		*/

		// Enqueue registered scripts
		wp_enqueue_script( $this->plugin_name . '-admin' );
		error_log( 'Tiny WP Modules: Script enqueued successfully' );
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
		error_log('Tiny WP Modules: admin_page() method called');
		include TINY_WP_MODULES_PLUGIN_DIR . 'templates/admin/admin-page.php';
	}

	/**
	 * Settings page callback
	 */
	public function settings_page() {
		error_log('Tiny WP Modules: settings_page() method called');
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
	 * Handle settings form submission
	 */
	public function handle_settings_submission() {
		error_log('Tiny WP Modules: ==========================================');
		error_log('Tiny WP Modules: handle_settings_submission method called');
		error_log('Tiny WP Modules: REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
		error_log('Tiny WP Modules: POST data count: ' . count( $_POST ));
		error_log('Tiny WP Modules: Action field: ' . ( $_POST['action'] ?? 'NOT SET' ) );
		error_log('Tiny WP Modules: Nonce field: ' . ( $_POST['tiny_wp_modules_nonce'] ?? 'NOT SET' ) );
		error_log('Tiny WP Modules: Current tab: ' . ( $_POST['current_tab'] ?? 'NOT SET' ) );
		error_log('Tiny WP Modules: ==========================================');
		
		if ( ! isset( $_POST['tiny_wp_modules_nonce'] ) || ! wp_verify_nonce( $_POST['tiny_wp_modules_nonce'], 'tiny_wp_modules_save_settings' ) ) {
			error_log('Tiny WP Modules: Nonce verification failed');
			error_log('Tiny WP Modules: Nonce field present: ' . ( isset( $_POST['tiny_wp_modules_nonce'] ) ? 'YES' : 'NO' ) );
			if ( isset( $_POST['tiny_wp_modules_nonce'] ) ) {
				error_log('Tiny WP Modules: Nonce value: ' . $_POST['tiny_wp_modules_nonce'] );
			}
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			error_log('Tiny WP Modules: User does not have manage_options capability');
			return;
		}

		// Get current tab
		$current_tab = isset( $_POST['current_tab'] ) ? sanitize_text_field( $_POST['current_tab'] ) : 'general';

		// Get plugin slug
		global $tiny_wp_modules_plugin;
		$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';

		// Debug: Log what's being submitted
		error_log( 'Tiny WP Modules: POST data received: ' . print_r( $_POST, true ) );
		error_log( 'Tiny WP Modules: Current tab: ' . $current_tab );

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
				
				// Process redirect after login roles - handle both checked and unchecked states
				$settings['redirect_after_login_for'] = array();
				$all_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'shop_manager', 'customer' );
				
				// Get the POST data for roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['redirect_after_login_for'] ?? array();
				
				// Process each role - if it's in POST data, it's checked (value = '1'), if not, it's unchecked
				foreach ( $all_roles as $role ) {
					if ( isset( $posted_roles[ $role ] ) && $posted_roles[ $role ] === '1' ) {
						$settings['redirect_after_login_for'][ $role ] = '1';
					} else {
						$settings['redirect_after_login_for'][ $role ] = '0';
					}
				}
				
				// Redirect After Logout settings
				if ( isset( $_POST['tiny_wp_modules_settings']['redirect_after_logout_to_slug'] ) ) {
					$settings['redirect_after_logout_to_slug'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['redirect_after_logout_to_slug'] );
				}
				
				// Process redirect after logout roles - handle both checked and unchecked states
				$settings['redirect_after_logout_for'] = array();
				$all_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'shop_manager', 'customer' );
				
				// Get the POST data for roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['redirect_after_logout_for'] ?? array();
				
				// Process each role - if it's in POST data, it's checked (value = '1'), if not, it's unchecked
				foreach ( $all_roles as $role ) {
					if ( isset( $posted_roles[ $role ] ) && $posted_roles[ $role ] === '1' ) {
						$settings['redirect_after_logout_for'][ $role ] = '1';
					} else {
						$settings['redirect_after_logout_for'][ $role ] = '0';
					}
				}
				
				// Password Protection settings
				if ( isset( $_POST['tiny_wp_modules_settings']['password_protection_password'] ) ) {
					$settings['password_protection_password'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['password_protection_password'] );
				}
				
				// SVG Upload settings
				$settings['enable_svg_upload'] = isset( $_POST['tiny_wp_modules_settings']['enable_svg_upload'] ) ? '1' : '0';
				error_log('Tiny WP Modules: SVG upload enabled: ' . $settings['enable_svg_upload']);
				
				// Process SVG upload roles - handle both checked and unchecked states
				$settings['svg_upload_roles'] = array();
				$all_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'shop_manager', 'customer' );
				
				// Get the POST data for roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['svg_upload_roles'] ?? array();
				error_log('Tiny WP Modules: SVG upload roles POST data: ' . print_r( $posted_roles, true ));
				
				// Process each role - if it's in POST data, it's checked (value = '1'), if not, it's unchecked
				foreach ( $all_roles as $role ) {
					if ( isset( $posted_roles[ $role ] ) && $posted_roles[ $role ] === '1' ) {
						$settings['svg_upload_roles'][ $role ] = '1';
					} else {
						$settings['svg_upload_roles'][ $role ] = '0';
					}
				}
				
				error_log('Tiny WP Modules: SVG upload roles processed: ' . print_r( $settings['svg_upload_roles'], true ));
				
				// AVIF Upload settings
				$settings['enable_avif_upload'] = isset( $_POST['tiny_wp_modules_settings']['enable_avif_upload'] ) ? '1' : '0';
				
				// Process AVIF upload roles - handle both checked and unchecked states
				$settings['avif_upload_roles'] = array();
				$all_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'shop_manager', 'customer' );
				
				// Get the POST data for roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['avif_upload_roles'] ?? array();
				
				// Process each role - if it's in POST data, it's checked (value = '1'), if not, it's unchecked
				foreach ( $all_roles as $role ) {
					if ( isset( $posted_roles[ $role ] ) && $posted_roles[ $role ] === '1' ) {
						$settings['avif_upload_roles'][ $role ] = '1';
					} else {
						$settings['avif_upload_roles'][ $role ] = '0';
					}
				}
				
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
				// Process maintenance mode allowed roles - handle both checked and unchecked states
				$settings['maintenance_allowed_roles'] = array();
				$all_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'shop_manager', 'customer' );
				
				// Get the POST data for roles
				$posted_roles = $_POST['tiny_wp_modules_settings']['maintenance_allowed_roles'] ?? array();
				
				// Process each role - if it's in POST data, it's checked (value = '1'), if not, it's unchecked
				foreach ( $all_roles as $role ) {
					if ( isset( $posted_roles[ $role ] ) && $posted_roles[ $role ] === '1' ) {
						$settings['maintenance_allowed_roles'][ $role ] = '1';
					} else {
						$settings['maintenance_allowed_roles'][ $role ] = '0';
					}
				}
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

		// Debug: Log what settings are being saved
		error_log( 'Tiny WP Modules: Final settings to save: ' . print_r( $settings, true ) );
		
		$result = Settings_Config::update_settings( $settings );
		
		// Debug logging
		error_log( 'Tiny WP Modules: Settings saved successfully. Result: ' . ( $result ? 'true' : 'false' ) );
		error_log( 'Tiny WP Modules: Current tab: ' . $current_tab );
		error_log( 'Tiny WP Modules: Plugin slug: ' . $plugin_slug );
		
		// Verify the settings were actually saved
		$saved_settings = get_option( 'tiny_wp_modules_settings' );
		error_log( 'Tiny WP Modules: Settings after save: ' . print_r( $saved_settings, true ) );
		
		// Redirect to prevent form resubmission, preserving the current tab
		$redirect_url = add_query_arg( 
			array(
				'settings-updated' => 'true',
				'tab' => $current_tab
			), 
			admin_url( 'admin.php?page=' . $plugin_slug ) 
		);
		
		error_log( 'Tiny WP Modules: Redirecting to: ' . $redirect_url );
		error_log( 'Tiny WP Modules: Final redirect URL: ' . $redirect_url );
		
		wp_redirect( $redirect_url );
		exit;
	}
	
} 