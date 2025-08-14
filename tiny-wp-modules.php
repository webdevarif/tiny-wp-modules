<?php
/**
 * Plugin Name: Tiny WP Modules
 * Plugin URI: https://github.com/webdevarif/tiny-wp-modules
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
} else {
	// If autoloader is missing, show admin notice
	add_action( 'admin_notices', function() {
		echo '<div class="notice notice-error"><p><strong>Tiny WP Modules:</strong> Composer autoloader is missing. Please run <code>composer install</code> in the plugin directory.</p></div>';
	});
	return; // Don't continue if autoloader is missing
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
 * Get plugin icon URL
 *
 * @param string $path Icon path relative to assets/icons directory.
 * @return string Full icon URL.
 */
function tiny_icon( $path ) {
	return tiny_asset( 'icons/' . ltrim( $path, '/' ) );
}

/**
 * Plugin activation hook
 */
function tiny_wp_modules_activate() {
	// Check if Composer autoloader is available
	if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		wp_die( 
			esc_html__( 'Tiny WP Modules requires Composer dependencies to be installed. Please run composer install in the plugin directory.', 'tiny-wp-modules' ),
			esc_html__( 'Plugin Activation Error', 'tiny-wp-modules' ),
			array( 'back_link' => true )
		);
	}

	// Include the autoloader
	require_once __DIR__ . '/vendor/autoload.php';

	// Check if Activator class exists after loading autoloader
	if ( ! class_exists( 'TinyWpModules\\Core\\Activator' ) ) {
		wp_die( 
			esc_html__( 'Tiny WP Modules could not load required classes. Please check if all files are properly uploaded.', 'tiny-wp-modules' ),
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

	// Initialize GitHub Updater for automatic updates
	tiny_wp_modules_init_github_updater();
}

/**
 * Initialize GitHub Updater
 */
function tiny_wp_modules_init_github_updater() {
	// Check if GitHubUpdater class exists
	if ( ! class_exists( 'TinyWpModules\\Admin\\GitHubUpdater' ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater Error: GitHubUpdater class not found' );
		}
		return;
	}

	// Create GitHub Updater instance
	$updater = new TinyWpModules\Admin\GitHubUpdater( __FILE__ );

	// Try to refresh GitHub information first
	$updater->refreshGitHubInfo();
	
	// If still not configured, set it manually as fallback
	if ( ! $updater->isGitHubConfigured() ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater: Plugin header parsing failed, using manual fallback' );
		}
		$updater->setGitHubRepository( 'webdevarif', 'tiny-wp-modules', 'main' );
	}

	// Optional: Set custom branch (defaults to 'main')
	// $updater->setBranch( 'master' );

	// Optional: Set access token for private repositories
	// $updater->setAccessToken( 'your_github_token_here' );

	// Optional: Set tested WordPress version
	$updater->setTestedWpVersion( '6.4' );

	// Debug: Log GitHub information and test connection
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$github_info = $updater->getGitHubInfo();
		error_log( 'GitHubUpdater Debug: GitHub Info: ' . print_r( $github_info, true ) );
		
		// Test GitHub connection and log results
		$connection_test = $updater->testGitHubConnection();
		error_log( 'GitHubUpdater Debug: Connection Test: ' . print_r( $connection_test, true ) );
		
		// Add admin notice for debugging
		add_action( 'admin_notices', function() use ( $updater ) {
			$connection_test = $updater->testGitHubConnection();
			if ( $connection_test['success'] ) {
				echo '<div class="notice notice-success"><p><strong>GitHubUpdater:</strong> Successfully connected to repository: <code>' . esc_html( $connection_test['repository_name'] ) . '</code></p></div>';
			} else {
				echo '<div class="notice notice-error"><p><strong>GitHubUpdater Error:</strong> ' . esc_html( $connection_test['error'] ) . '</p></div>';
			}
		});
	}

	// Add the updater to WordPress
	$updater->add();
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