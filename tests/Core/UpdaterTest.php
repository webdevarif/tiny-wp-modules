<?php
/**
 * Updater Test
 *
 * @package TinyWpModules\Tests\Core
 */

namespace TinyWpModules\Tests\Core;

use TinyWpModules\Core\Updater;
use WP_UnitTestCase;

/**
 * Updater Test Class
 */
class UpdaterTest extends WP_UnitTestCase {

	/**
	 * Updater instance
	 *
	 * @var Updater
	 */
	private $updater;

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		$this->updater = new Updater();
	}

	/**
	 * Test plugin row meta links
	 */
	public function test_plugin_row_meta() {
		$links = array( 'Visit plugin site' );
		$file = 'tiny-wp-modules/tiny-wp-modules.php';
		
		$result = $this->updater->plugin_row_meta( $links, $file );
		
		$this->assertCount( 2, $result );
		$this->assertContains( 'Visit plugin site', $result );
		$this->assertContains( 'Check for Updates', $result[1] );
	}

	/**
	 * Test plugin row meta for different plugin
	 */
	public function test_plugin_row_meta_different_plugin() {
		$links = array( 'Visit plugin site' );
		$file = 'other-plugin/other-plugin.php';
		
		$result = $this->updater->plugin_row_meta( $links, $file );
		
		$this->assertCount( 1, $result );
		$this->assertContains( 'Visit plugin site', $result );
	}

	/**
	 * Test get latest version method exists
	 */
	public function test_get_latest_version_method_exists() {
		$this->assertTrue( method_exists( $this->updater, 'get_latest_version' ) );
	}

	/**
	 * Test updater constructor
	 */
	public function test_updater_constructor() {
		$this->assertInstanceOf( Updater::class, $this->updater );
	}

	/**
	 * Test plugin row meta contains check updates link
	 */
	public function test_plugin_row_meta_contains_check_updates_link() {
		$links = array();
		$file = 'tiny-wp-modules/tiny-wp-modules.php';
		
		$result = $this->updater->plugin_row_meta( $links, $file );
		
		$this->assertCount( 1, $result );
		$this->assertContains( 'Check for Updates', $result[0] );
		$this->assertContains( 'check-updates-link', $result[0] );
	}
} 