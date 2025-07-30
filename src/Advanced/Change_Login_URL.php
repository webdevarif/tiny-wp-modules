<?php
/**
 * Change Login URL Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * Class for Change Login URL module
 */
class Change_Login_URL {

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
		
		if ( isset( $settings['enable_change_login_url'] ) && $settings['enable_change_login_url'] ) {
			add_action( 'init', array( $this, 'redirect_on_custom_login_url' ) );
			add_action( 'init', array( $this, 'redirect_on_default_login_urls' ) );
			add_filter( 'login_url', array( $this, 'customize_login_url' ), 10, 3 );
			add_filter( 'lostpassword_url', array( $this, 'customize_lost_password_url' ) );
			add_filter( 'register_url', array( $this, 'customize_register_url' ) );
			add_filter( 'logout_url', array( $this, 'customize_logout_url' ), 10, 2 );
			add_action( 'wp_login_failed', array( $this, 'redirect_to_custom_login_url_on_login_fail' ) );
			add_action( 'wp_logout', array( $this, 'redirect_to_custom_login_url_on_logout_success' ) );
			add_filter( 'login_errors', array( $this, 'add_failed_login_message' ) );
		}
	}

	/**
	 * Redirect to valid login URL when custom login slug is part of the request URL
	 */
	public function redirect_on_custom_login_url() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return;
		}

		$url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );
		
		// Check if current URL is in the allowed login paths whitelist
		if ( $this->is_url_in_allowed_paths( $url_input ) ) {
			return; // Allow access to whitelisted paths
		}
		
		// Make sure $url_input ends with /
		if ( false !== strpos( $url_input, $custom_login_slug ) ) {
			if ( substr( $url_input, -1 ) != '/' ) {
				$url_input = $url_input . '/';
			}
		}
		
		// If URL contains the custom login slug, redirect to the dashboard
		if ( false !== strpos( $url_input, '/' . $custom_login_slug . '/' ) ) {
			if ( is_user_logged_in() ) {
				wp_safe_redirect( get_admin_url() );
				exit;
			} else {
				// Redirect to the login URL with custom login slug in the query parameters
				wp_safe_redirect( site_url( '/wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
				exit;
			}
		}
	}

	/**
	 * Customize login URL returned when calling wp_login_url()
	 */
	public function customize_login_url( $login_url, $redirect, $force_reauth ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return $login_url;
		}

		$login_url = home_url( '/' . $custom_login_slug . '/' );
		
		if ( !empty( $redirect ) ) {
			$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
		}
		
		if ( $force_reauth ) {
			$login_url = add_query_arg( 'reauth', '1', $login_url );
		}
		
		return $login_url;
	}

	/**
	 * Customize lost password URL
	 */
	public function customize_lost_password_url( $lostpassword_url ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return $lostpassword_url;
		}

		return $lostpassword_url . '&' . $custom_login_slug;
	}

	/**
	 * Customize registration URL
	 */
	public function customize_register_url( $registration_url ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return $registration_url;
		}

		return $registration_url . '&' . $custom_login_slug;
	}

		/**
	 * Redirect to /not_found when login URL does not contain the custom login slug
	 */
	public function redirect_on_default_login_urls() {
		global $interim_login;
		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return;
		}

		$url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );
		$url_input_parts = explode( '/', $url_input );
		$redirect_slug = 'not_found';

		// Check if current URL is in the allowed login paths whitelist
		if ( $this->is_url_in_allowed_paths( $url_input ) ) {
			return; // Allow access to whitelisted paths
		}

		// Handle login POST requests
		if ( isset( $_POST['log'] ) && !empty( $_POST['log'] ) && isset( $_POST['pwd'] ) && !empty( $_POST['pwd'] ) ) {
			$http_referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_url( $_SERVER['HTTP_REFERER'] ) : '' );
			
			if ( !empty( $http_referrer ) && false === strpos( $http_referrer, $custom_login_slug ) ) {
				wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
				exit;
			}
		} elseif ( is_user_logged_in() ) {
			// Redirect to /wp-admin/ (Dashboard) when accessing /wp-login.php without any $_POST data
			if ( isset( $url_input_parts[1] ) && 'wp-login.php' == $url_input_parts[1] && empty( $_POST ) ) {
				wp_safe_redirect( admin_url(), 302 );
				exit;
			}
		} elseif ( !is_user_logged_in() ) {
			// Redirect default login URLs to not_found
			if ( isset( $url_input_parts[1] ) && in_array( $url_input_parts[1], array(
				'admin',
				'wp-admin',
				'login',
				'wp-login',
				'wp-login.php',
				'login.php'
			) ) && (!isset( $url_input_parts[2] ) || isset( $url_input_parts[2] ) && empty( $url_input_parts[2] ) || isset( $url_input_parts[2] ) && false !== strpos( $url_input_parts[2], '.php' )) ) {
				wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
				exit;
			} elseif ( false !== strpos( $url_input, 'wp-login.php' ) ) {
				if ( isset( $_GET['action'] ) && ('logout' == $_GET['action'] || 'rp' == $_GET['action'] || 'resetpass' == $_GET['action']) || isset( $_GET['checkemail'] ) && ('confirm' == $_GET['checkemail'] || 'registered' == $_GET['checkemail']) || isset( $_GET['interim-login'] ) && '1' == $_GET['interim-login'] || 'success' == $interim_login || isset( $_GET['redirect_to'] ) && isset( $_GET['reauth'] ) && false !== strpos( $url_input, 'comment' ) ) {
					// Allow these specific actions
					return;
				} elseif ( false === strpos( $url_input, $custom_login_slug ) ) {
					// Redirect to /not_found/
					wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
					exit;
				}
			}
		}
	}

	/**
	 * Redirect to custom login URL on failed login
	 */
	public function redirect_to_custom_login_url_on_login_fail() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return;
		}

		$should_redirect = true;
		
		// Check if the login attempt came from an allowed path
		$http_referrer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_url( $_SERVER['HTTP_REFERER'] ) : '';
		if ( ! empty( $http_referrer ) ) {
			$referrer_path = parse_url( $http_referrer, PHP_URL_PATH );
			if ( $this->is_url_in_allowed_paths( $referrer_path ) ) {
				$should_redirect = false; // Don't redirect if login came from allowed path
			}
		}
		
		// Prevent redirection if the login process is initiated by a custom login form
		if ( !isset( $_POST['log'] ) && !isset( $_POST['pwd'] ) && !isset( $_POST['wp-submit'] ) && !isset( $_POST['testcookie'] ) ) {
			$should_redirect = false;
		}
		
		if ( $should_redirect ) {
			wp_safe_redirect( site_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false&failed_login=true' ) );
			exit;
		}
	}

	/**
	 * Add login error message on top of the login form
	 */
	public function add_failed_login_message( $message ) {
		if ( isset( $_REQUEST['failed_login'] ) && $_REQUEST['failed_login'] == 'true' ) {
			$message = '<div id="login_error" class="notice notice-error"><b>' . __( 'Error:', 'tiny-wp-modules' ) . '</b> ' . __( 'Invalid username/email or incorrect password.', 'tiny-wp-modules' ) . '</div>';
		}
		return $message;
	}

	/**
	 * Redirect to custom login URL on successful logout
	 */
	public function redirect_to_custom_login_url_on_logout_success() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return;
		}

		wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
		exit;
	}

	/**
	 * Customize logout URL by adding the custom login slug to it
	 */
	public function customize_logout_url( $logout_url, $redirect ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$custom_login_slug = isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : '';
		
		if ( empty( $custom_login_slug ) ) {
			return $logout_url;
		}

		if ( !empty( $redirect ) ) {
			$logout_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $logout_url );
		}
		
		$logout_url .= '&' . $custom_login_slug;
		return $logout_url;
	}

	/**
	 * Check if the current URL is in the allowed login paths whitelist
	 *
	 * @param string $url_input The current URL path
	 * @return bool True if URL is in allowed paths, false otherwise
	 */
	private function is_url_in_allowed_paths( $url_input ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$allowed_login_paths = isset( $settings['allowed_login_paths'] ) ? $settings['allowed_login_paths'] : '';
		
		if ( empty( $allowed_login_paths ) ) {
			return false;
		}

		// Convert textarea content to array of paths
		$allowed_paths = array_filter( array_map( 'trim', explode( "\n", $allowed_login_paths ) ) );
		
		if ( empty( $allowed_paths ) ) {
			return false;
		}

		// Clean the URL input for comparison
		$url_input = trim( $url_input, '/' );
		
		// Debug logging (only if debug mode is enabled)
		$debug_mode = isset( $settings['debug_mode'] ) ? $settings['debug_mode'] : '0';
		if ( $debug_mode ) {
			error_log( 'Tiny WP Modules - URL Check: ' . $url_input . ' | Allowed paths: ' . implode( ', ', $allowed_paths ) );
		}
		
		// Check if any of the allowed paths match the current URL
		foreach ( $allowed_paths as $allowed_path ) {
			$allowed_path = trim( $allowed_path, '/' );
			
			// Exact match
			if ( $url_input === $allowed_path ) {
				if ( $debug_mode ) {
					error_log( 'Tiny WP Modules - Exact match found: ' . $allowed_path );
				}
				return true;
			}
			
			// Check if URL starts with the allowed path (for sub-paths)
			if ( ! empty( $allowed_path ) && strpos( $url_input, $allowed_path . '/' ) === 0 ) {
				if ( $debug_mode ) {
					error_log( 'Tiny WP Modules - Sub-path match found: ' . $allowed_path );
				}
				return true;
			}
		}

		return false;
	}
} 