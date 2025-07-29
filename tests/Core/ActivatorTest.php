<?php
/**
 * Activator Test
 *
 * @package TinyWpModules\Tests\Core
 */

namespace TinyWpModules\Tests\Core;

use TinyWpModules\Core\Activator;
use WP_UnitTestCase;

/**
 * Activator Test Class
 */
class ActivatorTest extends WP_UnitTestCase {

	/**
	 * Test activation sets default options
	 */
	public function test_activation_sets_default_options() {
		$activator = new Activator();
		$activator->activate();

		// Check if default options are set
		$this->assertEquals( '1', get_option( 'tiny_wp_modules_enable_modules' ) );
		$this->assertEquals( '0', get_option( 'tiny_wp_modules_debug_mode' ) );
		$this->assertEquals( 'info', get_option( 'tiny_wp_modules_log_level' ) );
	}

	/**
	 * Test activation sets activation flags
	 */
	public function test_activation_sets_flags() {
		$activator = new Activator();
		$activator->activate();

		// Check if activation flags are set
		$this->assertTrue( get_option( 'tiny_wp_modules_activated' ) );
		$this->assertNotEmpty( get_option( 'tiny_wp_modules_activation_time' ) );
	}

	/**
	 * Test activation creates database table
	 */
	public function test_activation_creates_table() {
		global $wpdb;

		$activator = new Activator();
		$activator->activate();

		$table_name = $wpdb->prefix . 'tiny_wp_modules';
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name;

		$this->assertTrue( $table_exists );
	}

	/**
	 * Test activation clears caches
	 */
	public function test_activation_clears_caches() {
		// Set some transients first
		set_transient( 'tiny_wp_modules_status', 'test', 3600 );
		set_transient( 'tiny_wp_modules_modules_list', array( 'test' ), 3600 );

		$activator = new Activator();
		$activator->activate();

		// Check if transients are cleared
		$this->assertFalse( get_transient( 'tiny_wp_modules_status' ) );
		$this->assertFalse( get_transient( 'tiny_wp_modules_modules_list' ) );
	}
} 