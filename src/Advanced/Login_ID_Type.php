<?php
/**
 * Login ID Type Class
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

use WP_Error;

/**
 * Handles login ID type functionality
 */
class Login_ID_Type {

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
		
		if ( \TinyWpModules\Admin\Settings_Config::is_enabled( 'enable_login_id_type' ) ) {
			add_filter( 'login_form_defaults', array( $this, 'change_login_form_defaults' ), 10, 1 );
			add_filter( 'gettext', array( $this, 'gettext_login_id_username' ), 10, 3 );
			add_filter( 'gettext', array( $this, 'gettext_login_id_email' ), 10, 3 );
			add_filter( 'authenticate', array( $this, 'authenticate_email' ), 10, 2 );
		}
	}

	/**
	 * Change login form defaults
	 *
	 * @param array $defaults Login form defaults.
	 * @return array Modified defaults.
	 */
	public function change_login_form_defaults( $defaults ) {
		$login_id_type = \TinyWpModules\Admin\Settings_Config::get_setting( 'login_id_type', 'username' );
		if ( 'username' === $login_id_type ) {
			$defaults['label_username'] = __( 'Username', 'tiny-wp-modules' );
		} elseif ( 'email' === $login_id_type ) {
			$defaults['label_username'] = __( 'Email', 'tiny-wp-modules' );
		}
		return $defaults;
	}

	/**
	 * Filter gettext for username only mode
	 *
	 * @param string $translation Translated text.
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return string Modified translation.
	 */
	public function gettext_login_id_username( $translation, $text, $domain ) {
		$login_id_type = \TinyWpModules\Admin\Settings_Config::get_setting( 'login_id_type', 'username' );
		if ( 'username' === $login_id_type && 'default' === $domain ) {
			global $pagenow;
			if ( 'wp-login.php' === $pagenow && 'Username or Email Address' === $text ) {
				$translation = __( 'Username', 'tiny-wp-modules' );
			}
		}
		return $translation;
	}

	/**
	 * Authenticate email for email only mode
	 *
	 * @param WP_User|WP_Error|null $user User object or error.
	 * @param string $username Username or email entered.
	 * @return WP_User|WP_Error|null User object or error.
	 */
	public function authenticate_email( $user, $username ) {
		$login_id_type = \TinyWpModules\Admin\Settings_Config::get_setting( 'login_id_type', 'username' );
		if ( 'email' === $login_id_type && null !== $user && ! is_wp_error( $user ) ) {
			if ( strtolower( $user->user_email ) !== strtolower( $username ) ) {
				$user = new WP_Error( 'invalid_username', __( '<strong>Error:</strong> Invalid email or incorrect password.', 'tiny-wp-modules' ) );
			}
		}
		return $user;
	}

	/**
	 * Filter gettext for email only mode
	 *
	 * @param string $translation Translated text.
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return string Modified translation.
	 */
	public function gettext_login_id_email( $translation, $text, $domain ) {
		$login_id_type = \TinyWpModules\Admin\Settings_Config::get_setting( 'login_id_type', 'username' );
		if ( 'email' === $login_id_type && 'default' === $domain ) {
			global $pagenow;
			if ( 'wp-login.php' === $pagenow && 'Username or Email Address' === $text ) {
				$translation = __( 'Email', 'tiny-wp-modules' );
			}
		}
		return $translation;
	}
}
