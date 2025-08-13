<?php
/**
 * Advanced Tab Content Template
 *
 * @package TinyWpModules\Templates\Admin\Tabs
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import classes
use TinyWpModules\Admin\Components;

// Get settings
$settings = get_option( 'tiny_wp_modules_settings', array() );
?>

<div class="tab-content" id="advanced-tab">
	<table class="settings-table">
		<tbody>
			<!-- Change Login URL -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Change Login URL', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_change_login_url',
						'name' => 'tiny_wp_modules_settings[enable_change_login_url]',
						'value' => '1',
						'checked' => isset( $settings['enable_change_login_url'] ) ? $settings['enable_change_login_url'] : 0,
						'label' => sprintf( __( 'Default is %s', 'tiny-wp-modules' ), home_url( '/wp-login.php' ) ),
						'class' => 'login-url-module',
						'data_toggle' => 'login-url-config',
						'config_fields' => array(
							array(
								'type' => 'group_header',
								'title' => __( 'Login URL Configuration', 'tiny-wp-modules' ),
								'description' => __( 'Configure custom login URL and allowed paths.', 'tiny-wp-modules' ),
								'class' => 'login-url-group-header'
							),
							array(
								'type' => 'text',
								'id' => 'custom_login_slug',
								'name' => 'tiny_wp_modules_settings[custom_login_slug]',
								'value' => isset( $settings['custom_login_slug'] ) ? $settings['custom_login_slug'] : 'backend',
								'placeholder' => __( 'e.g. backend', 'tiny-wp-modules' ),
								'class' => 'tiny-input',
								'label' => __( 'New login URL:', 'tiny-wp-modules' ),
								'base_url' => home_url() . '/'
							),
							array(
								'type' => 'textarea',
								'id' => 'allowed_login_paths',
								'name' => 'tiny_wp_modules_settings[allowed_login_paths]',
								'value' => '',
								'placeholder' => __( 'e.g. dashboard', 'tiny-wp-modules' ),
								'class' => 'tiny-input',
								'label' => sprintf( __( 'Allow login from: %s', 'tiny-wp-modules' ), home_url() ),
								'description' => __( 'Enter one path per line. These paths will be exempted from login URL restrictions.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'info',
								'content' => '<p><strong>"New login URL"</strong> only works for/with the default WordPress login page. If you have a login page you manually created with a page builder or with another plugin, please add them to the "Allow login from" section.</p>
									<p>This module is not yet compatible with two-factor authentication (2FA) methods. If you use a 2FA plugin, please use the change login URL feature bundled in that plugin, or use another plugin that is compatible with it.</p>
									<p>And obviously, to improve security, please use something other than \'login\' for the custom login slug.</p>'
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- Redirect After Login -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Redirect After Login', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_redirect_after_login',
						'name' => 'tiny_wp_modules_settings[enable_redirect_after_login]',
						'value' => '1',
						'checked' => isset( $settings['enable_redirect_after_login'] ) ? $settings['enable_redirect_after_login'] : 0,
						'label' => __( 'Enable redirect after login', 'tiny-wp-modules' ),
						'description' => __( 'Configure custom redirect URL for user roles after login.', 'tiny-wp-modules' ),
						'class' => 'redirect-after-login-module',
						'data_toggle' => 'redirect-after-login-config',
						'config_fields' => array(
							array(
								'type' => 'user_roles',
								'id' => 'redirect_after_login_for',
								'name' => 'tiny_wp_modules_settings[redirect_after_login_for]',
								'value' => isset( $settings['redirect_after_login_for'] ) ? $settings['redirect_after_login_for'] : array(),
								'url_value' => isset( $settings['redirect_after_login_to_slug'] ) ? $settings['redirect_after_login_to_slug'] : '',
								'url_name' => 'tiny_wp_modules_settings[redirect_after_login_to_slug]',
								'class' => 'tiny-input',
								'label' => __( 'for:', 'tiny-wp-modules' ),
								'description' => __( 'Select user roles to apply this redirect.', 'tiny-wp-modules' )
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- Redirect After Logout -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Redirect After Logout', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_redirect_after_logout',
						'name' => 'tiny_wp_modules_settings[enable_redirect_after_logout]',
						'value' => '1',
						'checked' => isset( $settings['enable_redirect_after_logout'] ) ? $settings['enable_redirect_after_logout'] : 0,
						'label' => __( 'Enable redirect after logout', 'tiny-wp-modules' ),
						'description' => __( 'Configure custom redirect URL for user roles after logout.', 'tiny-wp-modules' ),
						'class' => 'redirect-after-logout-module',
						'data_toggle' => 'redirect-after-logout-config',
						'config_fields' => array(
							array(
								'type' => 'user_roles',
								'id' => 'redirect_after_logout_for',
								'name' => 'tiny_wp_modules_settings[redirect_after_logout_for]',
								'value' => isset( $settings['redirect_after_logout_for'] ) ? $settings['redirect_after_logout_for'] : array(),
								'url_value' => isset( $settings['redirect_after_logout_to_slug'] ) ? $settings['redirect_after_logout_to_slug'] : '',
								'url_name' => 'tiny_wp_modules_settings[redirect_after_logout_to_slug]',
								'class' => 'tiny-input',
								'label' => __( 'for:', 'tiny-wp-modules' ),
								'description' => __( 'Select user roles to apply this redirect.', 'tiny-wp-modules' )
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- Redirect 404 -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Redirect 404', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_redirect_404',
						'name' => 'tiny_wp_modules_settings[enable_redirect_404]',
						'value' => '1',
						'checked' => isset( $settings['enable_redirect_404'] ) ? $settings['enable_redirect_404'] : 0,
						'label' => __( 'Enable 404 redirect', 'tiny-wp-modules' ),
						'description' => __( 'Automatically redirect 404 pages to the homepage.', 'tiny-wp-modules' ),
						'class' => 'redirect-404-module',
						'data_toggle' => 'redirect-404-config',
						'config_fields' => array(
							array(
								'type' => 'info',
								'content' => '<p><strong>Note:</strong> This feature automatically redirects all 404 (page not found) errors to your homepage with a 301 permanent redirect. This helps improve user experience and SEO by preventing visitors from seeing error pages.</p>
									<p>This redirect will not affect admin pages, cron jobs, or XML-RPC requests.</p>'
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- Password Protection -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Password Protection', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_password_protection',
						'name' => 'tiny_wp_modules_settings[enable_password_protection]',
						'value' => '1',
						'checked' => isset( $settings['enable_password_protection'] ) ? $settings['enable_password_protection'] : 0,
						'label' => __( 'Enable password protection', 'tiny-wp-modules' ),
						'description' => __( 'Password-protect the entire site to hide the content from public view and search engine bots / crawlers. Logged-in administrators can still access the site as usual.', 'tiny-wp-modules' ),
						'class' => 'password-protection-module',
						'data_toggle' => 'password-protection-config',
						'config_fields' => array(
							array(
								'type' => 'password',
								'id' => 'password_protection_password',
								'name' => 'tiny_wp_modules_settings[password_protection_password]',
								'value' => isset( $settings['password_protection_password'] ) ? $settings['password_protection_password'] : '',
								'placeholder' => __( 'Enter password', 'tiny-wp-modules' ),
								'class' => 'tiny-input',
								'label' => __( 'Set the password:', 'tiny-wp-modules' ),
								'description' => __( '(Default is "secret")', 'tiny-wp-modules' )
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- SVG Upload -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'SVG Upload', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_svg_upload',
						'name' => 'tiny_wp_modules_settings[enable_svg_upload]',
						'value' => '1',
						'checked' => isset( $settings['enable_svg_upload'] ) ? $settings['enable_svg_upload'] : 0,
						'label' => __( 'Enable SVG upload', 'tiny-wp-modules' ),
						'description' => __( 'Allow SVG file uploads to the WordPress media library with security sanitization.', 'tiny-wp-modules' ),
						'class' => 'svg-upload-module',
						'data_toggle' => 'svg-upload-config',
						'config_fields' => array(
							array(
								'type' => 'user_roles_simple',
								'id' => 'svg_upload_roles',
								'name' => 'tiny_wp_modules_settings[svg_upload_roles]',
								'value' => isset( $settings['svg_upload_roles'] ) ? $settings['svg_upload_roles'] : array(),
								'label' => __( 'Allow SVG upload for these user roles:', 'tiny-wp-modules' ),
								'description' => __( 'Select which user roles can upload SVG files.', 'tiny-wp-modules' )
							)
						)
					) );
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
