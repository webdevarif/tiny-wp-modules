<?php
/**
 * Deactivator Test
 *
 * @package TinyWpModules\Tests\Core
 */

namespace TinyWpModules\Tests\Core;

use TinyWpModules\Core\Deactivator;
use WP_UnitTestCase;

/**
 * Deactivator Test Class
 */
class DeactivatorTest extends WP_UnitTestCase {

	/**
	 * Test deactivation sets deactivation flags
	 */
	public function test_deactivation_sets_flags() {
		$deactivator = new Deactivator();
		$deactivator->deactivate();

		// Check if deactivation flags are set
		$this->assertTrue( get_option( 'tiny_wp_modules_deactivated' ) );
		$this->assertNotEmpty( get_option( 'tiny_wp_modules_deactivation_time' ) );
	}

	/**
	 * Test deactivation clears caches
	 */
	public function test_deactivation_clears_caches() {
		// Set some transients first
		set_transient( 'tiny_wp_modules_status', 'test', 3600 );
		set_transient( 'tiny_wp_modules_modules_list', array( 'test' ), 3600 );
		set_transient( 'tiny_wp_modules_settings', array( 'setting' => 'value' ), 3600 );

		$deactivator = new Deactivator();
		$deactivator->deactivate();

		// Check if transients are cleared
		$this->assertFalse( get_transient( 'tiny_wp_modules_status' ) );
		$this->assertFalse( get_transient( 'tiny_wp_modules_modules_list' ) );
		$this->assertFalse( get_transient( 'tiny_wp_modules_settings' ) );
	}

	/**
	 * Test deactivation preserves options
	 */
	public function test_deactivation_preserves_options() {
		// Set some options first
		update_option( 'tiny_wp_modules_enable_modules', '1' );
		update_option( 'tiny_wp_modules_debug_mode', '0' );

		$deactivator = new Deactivator();
		$deactivator->deactivate();

		// Check if options are preserved (not deleted)
		$this->assertEquals( '1', get_option( 'tiny_wp_modules_enable_modules' ) );
		$this->assertEquals( '0', get_option( 'tiny_wp_modules_debug_mode' ) );
	}
} 