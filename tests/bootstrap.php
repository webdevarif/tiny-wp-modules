<?php
/**
 * Test Bootstrap
 *
 * @package TinyWpModules\Tests
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/../' );
}

// Load Composer autoloader
require_once dirname( __FILE__ ) . '/../vendor/autoload.php';

// Load WordPress test environment
if ( ! defined( 'WP_TESTS_DIR' ) ) {
	define( 'WP_TESTS_DIR', getenv( 'WP_TESTS_DIR' ) ?: dirname( __FILE__ ) . '/wordpress-tests-lib' );
}

// Load WordPress test functions
if ( file_exists( WP_TESTS_DIR . '/includes/functions.php' ) ) {
	require_once WP_TESTS_DIR . '/includes/functions.php';
}

// Load WordPress test bootstrap
if ( file_exists( WP_TESTS_DIR . '/includes/bootstrap.php' ) ) {
	require_once WP_TESTS_DIR . '/includes/bootstrap.php';
}

// Load plugin
require_once dirname( __FILE__ ) . '/../tiny-wp-modules.php'; 