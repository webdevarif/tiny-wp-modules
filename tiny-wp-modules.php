<?php
/**
 * Plugin Name: Tiny WP Modules
 * Plugin URI: https://digitalfarmers.com/tiny-wp-modules
 * Description: A modular WordPress plugin with OOP architecture and Composer autoloading
 * Version: 1.0.0
 * Author: Digital Farmers
 * Author URI: https://digitalfarmers.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tiny-wp-modules
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 *
 * @package TinyWpModules
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'TINY_WP_MODULES_VERSION', '1.0.0' );
define( 'TINY_WP_MODULES_PLUGIN_FILE', __FILE__ );
define( 'TINY_WP_MODULES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TINY_WP_MODULES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TINY_WP_MODULES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Autoloader
if ( file_exists( TINY_WP_MODULES_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once TINY_WP_MODULES_PLUGIN_DIR . 'vendor/autoload.php';
}

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'tiny_wp_modules_activate' );
register_deactivation_hook( __FILE__, 'tiny_wp_modules_deactivate' );

// Initialize the plugin
add_action( 'plugins_loaded', 'tiny_wp_modules_init' );

/**
 * Get plugin asset URL
 *
 * @param string $path Asset path relative to assets directory.
 * @return string Full asset URL.
 */
function tiny_asset( $path ) {
	return TINY_WP_MODULES_PLUGIN_URL . 'assets/' . ltrim( $path, '/' );
}

/**
 * Get plugin image URL
 *
 * @param string $path Image path relative to assets/images directory.
 * @return string Full image URL.
 */
function tiny_image( $path ) {
	return tiny_asset( 'images/' . ltrim( $path, '/' ) );
}

/**
 * Plugin activation hook
 */
function tiny_wp_modules_activate() {
	// Check if Composer autoloader is available
	if ( ! class_exists( 'TinyWpModules\\Core\\Activator' ) ) {
		wp_die( 
			esc_html__( 'Tiny WP Modules requires Composer dependencies to be installed. Please run composer install in the plugin directory.', 'tiny-wp-modules' ),
			esc_html__( 'Plugin Activation Error', 'tiny-wp-modules' ),
			array( 'back_link' => true )
		);
	}

	// Run activation
	$activator = new TinyWpModules\Core\Activator();
	$activator->activate();
}

/**
 * Plugin deactivation hook
 */
function tiny_wp_modules_deactivate() {
	// Check if Composer autoloader is available
	if ( ! class_exists( 'TinyWpModules\\Core\\Deactivator' ) ) {
		return;
	}

	// Run deactivation
	$deactivator = new TinyWpModules\Core\Deactivator();
	$deactivator->deactivate();
}

/**
 * Initialize the plugin
 */
function tiny_wp_modules_init() {
	global $tiny_wp_modules_plugin;
	
	if ( ! class_exists( 'TinyWpModules\\Core\\Plugin' ) ) {
		add_action( 'admin_notices', 'tiny_wp_modules_autoloader_missing_notice' );
		return;
	}

	// Initialize the main plugin class
	$tiny_wp_modules_plugin = new TinyWpModules\Core\Plugin();
	$tiny_wp_modules_plugin->init();
}

/**
 * Display notice if autoloader is missing
 */
function tiny_wp_modules_autoloader_missing_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: composer install command */
				esc_html__( 'Tiny WP Modules requires Composer dependencies to be installed. Please run %s in the plugin directory.', 'tiny-wp-modules' ),
				'<code>composer install</code>'
			);
			?>
		</p>
	</div>
	<?php
} 