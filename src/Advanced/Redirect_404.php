<?php
/**
 * Redirect 404 Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * Class for Redirect 404 module
 */
class Redirect_404 {

	/**
	 * Initialize the module
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Only hook if redirect 404 is enabled
		if ( isset( $settings['enable_redirect_404'] ) && $settings['enable_redirect_404'] ) {
			add_action( 'template_redirect', array( $this, 'redirect_404' ) );
		}
	}

	/**
	 * Redirect 404 to homepage
	 *
	 * @since 1.7.0
	 */
	public function redirect_404() {
		if ( !is_404() || is_admin() || defined( 'DOING_CRON' ) && DOING_CRON || defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return;
		} elseif ( is_404() ) {
			$redirect_url = site_url();
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( 'Location: ' . sanitize_url( $redirect_url ) );
			exit;
		} else {
		}
	}
} 