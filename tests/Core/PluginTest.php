<?php
/**
 * Plugin Test
 *
 * @package TinyWpModules\Tests\Core
 */

namespace TinyWpModules\Tests\Core;

use TinyWpModules\Core\Plugin;
use WP_UnitTestCase;

/**
 * Plugin Test Class
 */
class PluginTest extends WP_UnitTestCase {

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		$this->plugin = new Plugin();
	}

	/**
	 * Test plugin initialization
	 */
	public function test_plugin_initialization() {
		$this->assertInstanceOf( Plugin::class, $this->plugin );
	}

	/**
	 * Test plugin name getter
	 */
	public function test_get_plugin_name() {
		$this->assertEquals( 'tiny-wp-modules', $this->plugin->get_plugin_name() );
	}

	/**
	 * Test version getter
	 */
	public function test_get_version() {
		$this->assertEquals( TINY_WP_MODULES_VERSION, $this->plugin->get_version() );
	}

	/**
	 * Test loader getter
	 */
	public function test_get_loader() {
		$this->assertInstanceOf( \TinyWpModules\Core\Loader::class, $this->plugin->get_loader() );
	}

	/**
	 * Test plugin constants are defined
	 */
	public function test_plugin_constants() {
		$this->assertTrue( defined( 'TINY_WP_MODULES_VERSION' ) );
		$this->assertTrue( defined( 'TINY_WP_MODULES_PLUGIN_FILE' ) );
		$this->assertTrue( defined( 'TINY_WP_MODULES_PLUGIN_DIR' ) );
		$this->assertTrue( defined( 'TINY_WP_MODULES_PLUGIN_URL' ) );
		$this->assertTrue( defined( 'TINY_WP_MODULES_PLUGIN_BASENAME' ) );
	}
} 