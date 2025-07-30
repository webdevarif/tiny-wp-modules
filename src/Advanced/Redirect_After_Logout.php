<?php
/**
 * Redirect After Logout Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * Class for Redirect After Logout module
 */
class Redirect_After_Logout {

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
		
		// Only hook if redirect after logout is enabled
		if ( isset( $settings['enable_redirect_after_logout'] ) && $settings['enable_redirect_after_logout'] ) {
			add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );
		}
	}

	/**
	 * Redirect to custom internal URL after logout for user roles
	 *
	 * @param int $user_id The user ID
	 */
	public function redirect_after_logout( $user_id ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$this->redirect_to_single_url_after_logout( $user_id );
	}

	/**
	 * Redirect all applicable user roles to a single URL after logout
	 * 
	 * @param int $user_id The user ID
	 */
	public function redirect_to_single_url_after_logout( $user_id ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$redirect_after_logout_to_slug_raw = isset( $settings['redirect_after_logout_to_slug'] ) ? $settings['redirect_after_logout_to_slug'] : '';
		
		if ( !empty( $redirect_after_logout_to_slug_raw ) ) {
			$redirect_after_logout_to_slug = trim( trim( $redirect_after_logout_to_slug_raw ), '/' );
			if ( false !== strpos( $redirect_after_logout_to_slug, '#' ) || false !== strpos( $redirect_after_logout_to_slug, '.php' ) || false !== strpos( $redirect_after_logout_to_slug, '.html' ) ) {
				$relative_path = $redirect_after_logout_to_slug;
				// do not append slash at the end
			} else {
				$relative_path = $redirect_after_logout_to_slug . '/';
			}
		} else {
			$relative_path = '';
		}
		
		$redirect_url = get_site_url() . '/' . $relative_path;
		$redirect_after_logout_for = isset( $settings['redirect_after_logout_for'] ) ? $settings['redirect_after_logout_for'] : array();
		$user = get_userdata( $user_id );
		
		if ( isset( $redirect_after_logout_for ) && count( $redirect_after_logout_for ) > 0 ) {
			// Assemble single-dimensional array of roles for which custom URL redirection should happen
			$roles_for_custom_redirect = array();
			foreach ( $redirect_after_logout_for as $role_slug => $custom_redirect ) {
				if ( $custom_redirect ) {
					$roles_for_custom_redirect[] = $role_slug;
				}
			}
			
			// Does the user have roles data in array form?
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				$current_user_roles = $user->roles;
			}
			
			// Redirect for roles set in the settings. Otherwise, leave redirect URL to the default, i.e. admin dashboard.
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_for_custom_redirect ) ) {
					wp_safe_redirect( $redirect_url );
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
				$relative_path = $redirect_slug;
				// do not append slash at the end
			} else {
				$relative_path = $redirect_slug . '/';
			}
		} else {
			$relative_path = '';
		}
		return $relative_path;
	}
} 