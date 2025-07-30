<?php
/**
 * Redirect After Login Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * Class for Redirect After Login module
 */
class Redirect_After_Login {

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
		
		// Only hook if redirect after login is enabled
		if ( isset( $settings['enable_redirect_after_login'] ) && $settings['enable_redirect_after_login'] ) {
			add_action( 'wp_login', array( $this, 'redirect_after_login' ), 10, 2 );
		}
	}

	/**
	 * Redirect to custom internal URL after login for user roles
	 *
	 * @param string $username The username
	 * @param object $user The user object
	 */
	public function redirect_after_login( $username, $user ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$this->redirect_to_single_url_after_login( $username, $user );
	}

	/**
	 * Redirect all applicable user roles to a single URL after login
	 * 
	 * @param string $username The username
	 * @param object $user The user object
	 */
	public function redirect_to_single_url_after_login( $username, $user ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$redirect_after_login_to_slug_raw = isset( $settings['redirect_after_login_to_slug'] ) ? $settings['redirect_after_login_to_slug'] : '';
		$relative_path = $this->get_redirect_relative_path( $redirect_after_login_to_slug_raw );
		$redirect_after_login_for = isset( $settings['redirect_after_login_for'] ) ? $settings['redirect_after_login_for'] : array();
		
		if ( isset( $redirect_after_login_for ) && count( $redirect_after_login_for ) > 0 ) {
			// Assemble single-dimensional array of roles for which custom URL redirection should happen
			$roles_for_custom_redirect = array();
			foreach ( $redirect_after_login_for as $role_slug => $custom_redirect ) {
				if ( $custom_redirect ) {
					$roles_for_custom_redirect[] = $role_slug;
				}
			}
			
			// Does the user have roles data in array form?
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				$current_user_roles = $user->roles;
			}
			
			// Set custom redirect URL for roles set in the settings. Otherwise, leave redirect URL to the default, i.e. admin dashboard.
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_for_custom_redirect ) ) {
					wp_safe_redirect( home_url( $relative_path ) );
					exit;
				}
			}
		}
	}

	/**
	 * Get the relative path to redirect to based on the raw redirect slug
	 * 
	 * @param string $redirect_slug_raw The raw redirect slug
	 * @return string The processed relative path
	 */
	public function get_redirect_relative_path( $redirect_slug_raw ) {
		if ( !empty( $redirect_slug_raw ) ) {
			$redirect_slug = trim( trim( $redirect_slug_raw ), '/' );
			if ( false !== strpos( $redirect_slug, '#' ) || false !== strpos( $redirect_slug, '.php' ) || false !== strpos( $redirect_slug, '.html' ) ) {
				$slug_suffix = '';
			} else {
				$slug_suffix = '/';
			}
			$relative_path = $redirect_slug . $slug_suffix;
		} else {
			$relative_path = '';
		}
		return $relative_path;
	}
} 