<?php
/**
 * Password Protection Module Class
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

use WP_Error;

/**
 * Password Protection functionality
 */
class Password_Protection {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Check if Password Protection is enabled
		if ( ! isset( $settings['enable_password_protection'] ) || ! $settings['enable_password_protection'] ) {
			return;
		}

		// Add hooks for password protection
		add_action( 'init', array( $this, 'maybe_process_login' ) );
		add_action( 'template_redirect', array( $this, 'maybe_show_login_form' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'add_password_protection_admin_bar_item' ) );
		add_action( 'admin_head', array( $this, 'add_password_protection_admin_bar_item_styles' ) );
		add_action( 'wp_head', array( $this, 'add_password_protection_admin_bar_item_styles' ) );
		add_action( 'wp_head', array( $this, 'maybe_disable_page_caching' ) );
		
		// Hook error messages
		$this->hook_error_messages();
	}

	/**
	 * Show Password Protection admin bar status icon
	 */
	public function show_password_protection_admin_bar_icon() {
		add_action( 'wp_before_admin_bar_render', array( $this, 'add_password_protection_admin_bar_item' ) );
		add_action( 'admin_head', array( $this, 'add_password_protection_admin_bar_item_styles' ) );
		add_action( 'wp_head', array( $this, 'add_password_protection_admin_bar_item_styles' ) );
	}

	/**
	 * Add WP Admin Bar item
	 */
	public function add_password_protection_admin_bar_item() {
		global $wp_admin_bar;
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( array(
					'id'    => 'password_protection',
					'title' => '',
					'href'  => admin_url( 'admin.php?page=tiny-wp-modules-settings' ),
					'meta'  => array(
						'title' => __( 'Password protection is currently enabled for this site.', 'tiny-wp-modules' ),
					),
				) );
			}
		}
	}

	/**
	 * Add icon and CSS for admin bar item
	 */
	public function add_password_protection_admin_bar_item_styles() {
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				?>
				<style>
					#wp-admin-bar-password_protection { 
						background-color: #c32121 !important;
						transition: .25s;
					}
					#wp-admin-bar-password_protection > .ab-item { 
						color: #fff !important;  
					}
					#wp-admin-bar-password_protection > .ab-item:before { 
						content: "\f160"; 
						top: 2px; 
						color: #fff !important; 
						margin-right: 0px; 
					}
					#wp-admin-bar-password_protection:hover > .ab-item { 
						background-color: #af1d1d !important; 
						color: #fff; 
					}
				</style>
				<?php 
			}
		}
	}

	/**
	 * Disable page caching
	 */
	public function maybe_disable_page_caching() {
		if ( !defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
	}

	/**
	 * Maybe show login form
	 */
	public function maybe_show_login_form() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		$stored_password = isset( $settings['password_protection_password'] ) ? $settings['password_protection_password'] : '';
		
		// When user is logged-in as an administrator
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				return;
				// Do not load login form or perform redirection to the login form
			}
		}
		
		// When site visitor has entered correct password, get the auth cookie
		$auth_cookie = ( isset( $_COOKIE['tiny_wp_modules_password_protection'] ) ? $_COOKIE['tiny_wp_modules_password_protection'] : '' );
		
		// Compare $auth_cookie against hashed string set in maybe_process_login()
		if ( true === wp_check_password( $_SERVER['HTTP_HOST'] . '__' . $stored_password, $auth_cookie ) ) {
			return;
			// Do not load login form or perform redirection to the login form
		}
		
		if ( isset( $_REQUEST['protected-page'] ) && 'view' == $_REQUEST['protected-page'] ) {
			// Show login form
			$this->render_password_protection_login_form();
			exit;
		} else {
			// Redirect from current URL to login form
			$current_url = (( is_ssl() ? 'https://' : 'http://' )) . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );
			$args = array(
				'protected-page' => 'view',
				'source'         => urlencode( $current_url ),
			);
			$pwd_protect_login_url = add_query_arg( $args, home_url( '/' ) );
			nocache_headers();
			wp_safe_redirect( $pwd_protect_login_url );
			exit;
		}
	}

	/**
	 * Render password protection login form
	 */
	private function render_password_protection_login_form() {
		global $password_protected_errors, $error, $is_iphone;
		
		$password_field_label = __( 'Password', 'tiny-wp-modules' );
		$button_label = __( 'View Content', 'tiny-wp-modules' );
		
		nocache_headers();
		header( 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );
		
		// Maybe show error message above login form
		$shake_error_codes = array('empty_password', 'incorrect_password');
		if ( $password_protected_errors->get_error_code() && in_array( $password_protected_errors->get_error_code(), $shake_error_codes ) ) {
			add_action( 'tiny_wp_modules_password_protection_login_head', array( $this, 'wp_shake_js' ), 12 );
		}
		?>
		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
			<meta name="viewport" content="width=device-width" />
			<meta name="robots" content="noindex">
			<title><?php bloginfo( 'name' ); ?></title>
			<?php 
			wp_admin_css( 'login', true );
			do_action( 'tiny_wp_modules_password_protection_login_head' );
			?>
			<style type="text/css" id="protected-page-login-style">
				#login_error {
					box-sizing: border-box;
					width: 287px;
					border-left: 4px solid #d63638;
					padding: 12px;
					margin-top: 20px;
					margin-bottom: 0;
					background-color: #fff;
					box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
					word-wrap: break-word;
					color: #3c434a;
				}
			</style>
		</head>
		<body class="login protected-page-login wp-core-ui">

		<div id="login">
			<?php do_action( 'tiny_wp_modules_password_protection_error_messages' ); ?>
			<form name="loginform" id="loginform" action="<?php echo esc_url( add_query_arg( 'protected-page', 'view', home_url( '/' ) ) ); ?>" method="post">
				<label for="protected_page_pwd"><?php echo esc_html( $password_field_label ); ?></label>
				<input type="password" name="protected_page_pwd" id="protected_page_pwd" class="input" value="" size="20" />
				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo esc_attr( $button_label ); ?>" />
					<input type="hidden" name="protected-page" value="view" />
					<input type="hidden" name="source" value="<?php echo esc_attr( ( !empty( $_REQUEST['source'] ) ? $_REQUEST['source'] : '' ) ); ?>" />
				</p>
			</form>
		</div>

		<?php do_action( 'login_footer' ); ?>

		</body>
		</html>
		<?php
	}

	/**
	 * Maybe process login to access protected page content
	 */
	public function maybe_process_login() {
		global $password_protected_errors;
		$password_protected_errors = new WP_Error();
		
		if ( isset( $_REQUEST['protected_page_pwd'] ) ) {
			$password_input = sanitize_text_field( $_REQUEST['protected_page_pwd'] );
			$settings = get_option( 'tiny_wp_modules_settings', array() );
			$stored_password = isset( $settings['password_protection_password'] ) ? $settings['password_protection_password'] : '';
			
			if ( !empty( $password_input ) ) {
				if ( $password_input == $stored_password ) {
					// Password is correct
					// Set auth cookie
					$expiration = 0; // by the end of browsing session
					$hashed_cookie_value = wp_hash_password( $_SERVER['HTTP_HOST'] . '__' . $stored_password );
					setcookie(
						'tiny_wp_modules_password_protection',
						$hashed_cookie_value,
						$expiration,
						COOKIEPATH,
						COOKIE_DOMAIN,
						false,
						true
					);
					
					// Redirect
					$redirect_to_url = ( isset( $_REQUEST['source'] ) ? sanitize_url( $_REQUEST['source'] ) : '' );
					wp_safe_redirect( $redirect_to_url );
					exit;
				} else {
					// Password is incorrect
					$password_protected_errors->add( 'incorrect_password', __( 'Incorrect password.', 'tiny-wp-modules' ) );
				}
			} else {
				// Password input is empty
				$password_protected_errors->add( 'empty_password', __( 'Password can not be empty.', 'tiny-wp-modules' ) );
			}
		}
	}

	/**
	 * Add custom login error messages
	 */
	public function add_login_error_messages() {
		global $password_protected_errors;
		
		if ( $password_protected_errors->get_error_code() ) {
			$messages = '';
			$errors = '';
			
			// Extract the error message
			foreach ( $password_protected_errors->get_error_codes() as $code ) {
				$severity = $password_protected_errors->get_error_data( $code );
				foreach ( $password_protected_errors->get_error_messages( $code ) as $error ) {
					if ( 'message' == $severity ) {
						$messages .= $error . '<br />';
					} else {
						$errors .= $error . '<br />';
					}
				}
			}
			
			// Output the error message
			if ( !empty( $messages ) ) {
				echo '<p class="message">' . wp_kses_post( $messages ) . '</p>';
			}
			if ( !empty( $errors ) ) {
				echo '<div id="login_error">' . wp_kses_post( $errors ) . '</div>';
			}
		}
	}
	
	/**
	 * Hook error messages to WordPress login form
	 */
	public function hook_error_messages() {
		add_action( 'tiny_wp_modules_password_protection_error_messages', array( $this, 'add_login_error_messages' ) );
	}
	
	/**
	 * WP Shake JS for error animation
	 */
	public function wp_shake_js() {
		global $is_iphone;
		if ( isset( $is_iphone ) ) {
			if ( $is_iphone ) {
				return;
			}
		}
		?>
		<script>
		addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
		function s(id,pos){g(id).left=pos+'px';}
		function g(id){return document.getElementById(id).style;}
		function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
		addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
		</script>
		<?php 
	}
} 