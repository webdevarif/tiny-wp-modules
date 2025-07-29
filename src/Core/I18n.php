<?php
/**
 * Internationalization Class
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Define the internationalization functionality
 */
class I18n {

	/**
	 * Load the plugin text domain for translation
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'tiny-wp-modules',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
} 