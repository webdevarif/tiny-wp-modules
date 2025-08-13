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
		$this->settings    = new \TinyWpModules\Admin\Settings();
		$this->ajax_handler = new \TinyWpModules\Admin\Ajax_Handler();
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
		
		// Register admin JavaScript
		$registered = wp_register_script(
			$this->plugin_name . '-admin',
			$script_url,
			array( 'jquery' ),
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

	/**
	 * Handle settings form submission
	 */
	public function handle_settings_submission() {
		if ( ! isset( $_POST['tiny_wp_modules_nonce'] ) || ! wp_verify_nonce( $_POST['tiny_wp_modules_nonce'], 'tiny_wp_modules_save_settings' ) ) {
			error_log('Tiny WP Modules: Nonce verification failed');
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

		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Process settings based on current tab to avoid overwriting other tab data
		switch ( $current_tab ) {
			case 'general':
				// General tab settings
				$settings['enable_modules'] = isset( $_POST['tiny_wp_modules_settings']['enable_modules'] ) ? '1' : '0';
				$settings['debug_mode'] = isset( $_POST['tiny_wp_modules_settings']['debug_mode'] ) ? '1' : '0';
				$settings['enable_faq'] = isset( $_POST['tiny_wp_modules_settings']['enable_faq'] ) ? '1' : '0';
				
				// FAQ settings (only if FAQ is enabled)
				if ( isset( $_POST['tiny_wp_modules_settings']['faq_label'] ) ) {
					$settings['faq_label'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['faq_label'] );
				}
				
				if ( isset( $_POST['tiny_wp_modules_settings']['faq_slug'] ) ) {
					$settings['faq_slug'] = sanitize_title( $_POST['tiny_wp_modules_settings']['faq_slug'] );
				}
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
				
				if ( isset( $_POST['tiny_wp_modules_settings']['redirect_after_login_for'] ) ) {
					$settings['redirect_after_login_for'] = array_map( 'sanitize_text_field', $_POST['tiny_wp_modules_settings']['redirect_after_login_for'] );
				}
				
				// Redirect After Logout settings
				if ( isset( $_POST['tiny_wp_modules_settings']['redirect_after_logout_to_slug'] ) ) {
					$settings['redirect_after_logout_to_slug'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['redirect_after_logout_to_slug'] );
				}
				
				if ( isset( $_POST['tiny_wp_modules_settings']['redirect_after_logout_for'] ) ) {
					$settings['redirect_after_logout_for'] = array_map( 'sanitize_text_field', $_POST['tiny_wp_modules_settings']['redirect_after_logout_for'] );
				}
				
				// Password Protection settings
				if ( isset( $_POST['tiny_wp_modules_settings']['password_protection_password'] ) ) {
					$settings['password_protection_password'] = sanitize_text_field( $_POST['tiny_wp_modules_settings']['password_protection_password'] );
				}
				
				// SVG Upload settings
				if ( isset( $_POST['tiny_wp_modules_settings']['svg_upload_roles'] ) ) {
					$settings['svg_upload_roles'] = array_map( 'sanitize_text_field', $_POST['tiny_wp_modules_settings']['svg_upload_roles'] );
				}
				break;
				
			case 'updates':
				// Updates tab settings (if any)
				// Currently no specific settings for updates tab
				break;
		}

		$result = update_option( 'tiny_wp_modules_settings', $settings );
		
		// Redirect to prevent form resubmission, preserving the current tab
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