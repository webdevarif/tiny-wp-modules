<?php
/**
 * Maintenance Mode Class
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * Handles maintenance mode functionality
 */
class Maintenance_Mode {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Defer hook initialization until after all classes are loaded
		add_action( 'init', array( $this, 'init_hooks' ) );
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		// Check if Settings_Config class exists before using it
		if ( ! class_exists( 'TinyWpModules\\Admin\\Settings_Config' ) ) {
			return;
		}
		
		if ( \TinyWpModules\Admin\Settings_Config::is_enabled( 'enable_maintenance_mode' ) ) {
			add_action( 'template_redirect', array( $this, 'maintenance_mode_redirect' ), 1 );
			add_action( 'wp_before_admin_bar_render', array( $this, 'add_maintenance_mode_admin_bar_item' ) );
			add_action( 'admin_head', array( $this, 'add_maintenance_mode_admin_bar_item_styles' ) );
			add_action( 'wp_head', array( $this, 'add_maintenance_mode_admin_bar_item_styles' ) );
		}
	}

	/**
	 * Redirect for when maintenance mode is enabled
	 */
	public function maintenance_mode_redirect() {
		// Skip if user is allowed frontend access
		if ( $this->is_user_allowed_frontend_access() ) {
			return;
		}

		// Skip if it's admin area or login page
		if ( is_admin() || is_login() ) {
			return;
		}

		// Skip if bypass key is provided
		$bypass_key = \TinyWpModules\Admin\Settings_Config::get_setting( 'maintenance_bypass_key', '' );
		if ( ! empty( $bypass_key ) && isset( $_GET['bypass'] ) && $bypass_key === sanitize_text_field( $_GET['bypass'] ) ) {
			return;
		}

		// Show maintenance page
		$this->show_maintenance_page();
		exit;
	}

	/**
	 * Show maintenance page
	 */
	private function show_maintenance_page() {
		$heading = \TinyWpModules\Admin\Settings_Config::get_setting( 'maintenance_page_heading', __( 'Site Under Maintenance', 'tiny-wp-modules' ) );
		$description = \TinyWpModules\Admin\Settings_Config::get_setting( 'maintenance_page_description', __( 'We are currently performing scheduled maintenance. We will be back online shortly!', 'tiny-wp-modules' ) );
		$background = \TinyWpModules\Admin\Settings_Config::get_setting( 'maintenance_page_background', 'stripes' );
		
		// Set HTTP headers
		header( 'HTTP/1.1 503 Service Unavailable', true, 503 );
		header( 'Status: 503 Service Unavailable' );
		header( 'Retry-After: 3600' );
		
		// Get background style
		$background_style = $this->get_background_style( $background );
		
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo esc_html( get_bloginfo( 'name' ) . ' - ' . __( 'Maintenance', 'tiny-wp-modules' ) ); ?></title>
			<?php wp_site_icon(); ?>
			<style>
				body {
					margin: 0;
					padding: 0;
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
					<?php echo wp_kses_post( $background_style ); ?>
					background-size: cover;
					background-position: center center;
					min-height: 100vh;
					display: flex;
					align-items: center;
					justify-content: center;
				}
				.page-wrapper {
					text-align: center;
					max-width: 600px;
					padding: 40px 20px;
					background: rgba(255, 255, 255, 0.95);
					border-radius: 12px;
					box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
					backdrop-filter: blur(10px);
				}
				.message-box h1 {
					font-size: 2.5em;
					margin: 0 0 20px 0;
					color: #333;
					font-weight: 600;
				}
				.description {
					font-size: 1.2em;
					line-height: 1.6;
					color: #666;
					margin: 0;
				}
				@media (max-width: 768px) {
					.page-wrapper {
						margin: 20px;
						padding: 30px 20px;
					}
					.message-box h1 {
						font-size: 2em;
					}
					.description {
						font-size: 1.1em;
					}
				}
			</style>
		</head>
		<body>
			<div class="page-wrapper">
				<div class="message-box">
					<h1><?php echo wp_kses_post( $heading ); ?></h1>
					<div class="description"><?php echo wp_kses_post( $description ); ?></div>
				</div>
			</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Get background style based on selection
	 */
	private function get_background_style( $background ) {
		switch ( $background ) {
			case 'lines':
				return 'background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'1920\' height=\'1280\' viewBox=\'0 0 1920 1280\'%3e%3cpath d=\'M2294.46 927.36C2128.65 934.22 2078.52 1270.56 1693.36 1208.96 1308.19 1147.36 1373.24 145.96 1092.25-67.11\' stroke=\'rgba(158,160,161,0.57)\' stroke-width=\'2\'/%3e%3c/svg%3e");';
			case 'stripes':
				return 'background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'2560\' height=\'2560\' viewBox=\'0 0 2560 2560\'%3e%3cpath d=\'M0 0L524.59 0L0 986.23z\' fill=\'rgba(255,255,255,0.1)\'/%3e%3cpath d=\'M2560 2560L1477.86 2560L2560 2129.39z\' fill=\'rgba(0,0,0,0.1)\'/%3e%3c/svg%3e");';
			case 'curves':
				return 'background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'100%25\' viewBox=\'0 0 1600 800\'%3E%3Cpath fill=\'%23e0e0e0\' d=\'M486 705.8c-109.3-21.8-223.4-32.2-335.3-19.4C99.5 692.1 49 703 0 719.8V800h843.8c-115.9-33.2-230.8-68.1-347.6-92.2C492.8 707.1 489.4 706.5 486 705.8z\'/%3E%3C/svg%3E");';
			case 'solid_color':
				return 'background-color: #f5f5f5;';
			default:
				return 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);';
		}
	}

	/**
	 * Add WP Admin Bar item
	 */
	public function add_maintenance_mode_admin_bar_item() {
		global $wp_admin_bar;
		
		if ( is_user_logged_in() && $this->is_user_allowed_frontend_access() ) {
			$wp_admin_bar->add_menu( array(
				'id'    => 'maintenance_mode',
				'title' => '',
				'href'  => admin_url( 'admin.php?page=tiny-wp-modules#advanced' ),
				'meta'  => array(
					'title' => __( 'Maintenance mode is currently enabled for this site.', 'tiny-wp-modules' ),
				),
			) );
		}
	}

	/**
	 * Add icon and CSS for admin bar item
	 */
	public function add_maintenance_mode_admin_bar_item_styles() {
		if ( is_user_logged_in() && $this->is_user_allowed_frontend_access() ) {
			?>
			<style>
				#wp-admin-bar-maintenance_mode { 
					background-color: #ff800c !important;
					transition: .25s;
				}
				#wp-admin-bar-maintenance_mode > .ab-item { 
					color: #fff !important;  
				}
				#wp-admin-bar-maintenance_mode > .ab-item:before { 
					content: "\f308"; 
					top: 2px; 
					color: #fff !important; 
					margin-right: 0px; 
				}
				#wp-admin-bar-maintenance_mode:hover > .ab-item { 
					background-color: #e5730a !important; 
					color: #fff; 
				}
			</style>
			<?php
		}
	}

	/**
	 * Check if a user is allowed to access the frontend
	 */
	private function is_user_allowed_frontend_access() {
		$allowed_roles = \TinyWpModules\Admin\Settings_Config::get_setting( 'maintenance_allowed_roles', array() );
		
		if ( empty( $allowed_roles ) ) {
			// Default: allow administrators and editors
			return current_user_can( 'edit_posts' );
		}
		
		$user = wp_get_current_user();
		$user_roles = $user->roles;
		
		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $allowed_roles ) ) {
				return true;
			}
		}
		
		return false;
	}
}
