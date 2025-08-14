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
			<!-- Redirect Section Heading -->
			<?php echo \TinyWpModules\Admin\Settings_Config::get_section_heading( 
				__( 'Redirect', 'tiny-wp-modules' ),
				__( 'Configure custom redirects for various user actions and error pages.', 'tiny-wp-modules' )
			); ?>

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

			<!-- Protection Section Heading -->
			<?php echo \TinyWpModules\Admin\Settings_Config::get_section_heading( 
				__( 'Protection', 'tiny-wp-modules' ),
				__( 'Security and access control features to protect your website.', 'tiny-wp-modules' )
			); ?>
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

			<!-- Login ID Type -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Login ID Type', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_login_id_type',
						'name' => 'tiny_wp_modules_settings[enable_login_id_type]',
						'value' => '1',
						'checked' => isset( $settings['enable_login_id_type'] ) ? $settings['enable_login_id_type'] : 0,
						'label' => __( 'Change login form ID type', 'tiny-wp-modules' ),
						'description' => __( 'Change the login form to accept only usernames or only emails instead of both.', 'tiny-wp-modules' ),
						'class' => 'login-id-type-module',
						'data_toggle' => 'login-id-type-config',
						'config_fields' => array(
							array(
								'type' => 'radio',
								'id' => 'login_id_type',
								'name' => 'tiny_wp_modules_settings[login_id_type]',
								'value' => isset( $settings['login_id_type'] ) ? $settings['login_id_type'] : 'username',
								'class' => 'login-id-type-radio',
								'options' => array(
									'username' => __( 'Username only', 'tiny-wp-modules' ),
									'email' => __( 'Email address only', 'tiny-wp-modules' ),
								),
								'description' => __( 'Choose what type of ID users can use to login.', 'tiny-wp-modules' )
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

			<!-- Maintenance Mode -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Maintenance Mode', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_maintenance_mode',
						'name' => 'tiny_wp_modules_settings[enable_maintenance_mode]',
						'value' => '1',
						'checked' => isset( $settings['enable_maintenance_mode'] ) ? $settings['enable_maintenance_mode'] : 0,
						'label' => __( 'Enable maintenance mode', 'tiny-wp-modules' ),
						'description' => __( 'Show a custom maintenance page to visitors while keeping the site accessible to administrators and selected user roles.', 'tiny-wp-modules' ),
						'class' => 'maintenance-mode-module',
						'data_toggle' => 'maintenance-mode-config',
						'config_fields' => array(
							array(
								'type' => 'text',
								'id' => 'maintenance_page_heading',
								'name' => 'tiny_wp_modules_settings[maintenance_page_heading]',
								'value' => isset( $settings['maintenance_page_heading'] ) ? $settings['maintenance_page_heading'] : __( 'Site Under Maintenance', 'tiny-wp-modules' ),
								'placeholder' => __( 'Site Under Maintenance', 'tiny-wp-modules' ),
								'class' => 'tiny-input',
								'label' => __( 'Page Heading:', 'tiny-wp-modules' ),
								'description' => __( 'Heading text displayed on the maintenance page.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'textarea',
								'id' => 'maintenance_page_description',
								'name' => 'tiny_wp_modules_settings[maintenance_page_description]',
								'value' => isset( $settings['maintenance_page_description'] ) ? $settings['maintenance_page_description'] : __( 'We are currently performing scheduled maintenance. We will be back online shortly!', 'tiny-wp-modules' ),
								'placeholder' => __( 'We are currently performing scheduled maintenance. We will be back online shortly!', 'tiny-wp-modules' ),
								'class' => 'tiny-input',
								'label' => __( 'Page Description:', 'tiny-wp-modules' ),
								'description' => __( 'Description text displayed on the maintenance page.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'select',
								'id' => 'maintenance_page_background',
								'name' => 'tiny_wp_modules_settings[maintenance_page_background]',
								'value' => isset( $settings['maintenance_page_background'] ) ? $settings['maintenance_page_background'] : 'stripes',
								'class' => 'tiny-input',
								'label' => __( 'Background Style:', 'tiny-wp-modules' ),
								'description' => __( 'Choose the background style for the maintenance page.', 'tiny-wp-modules' ),
								'options' => array(
									'stripes' => __( 'Stripes', 'tiny-wp-modules' ),
									'lines' => __( 'Lines', 'tiny-wp-modules' ),
									'curves' => __( 'Curves', 'tiny-wp-modules' ),
									'solid_color' => __( 'Solid Color', 'tiny-wp-modules' ),
									'gradient' => __( 'Gradient', 'tiny-wp-modules' ),
								)
							),
							array(
								'type' => 'text',
								'id' => 'maintenance_bypass_key',
								'name' => 'tiny_wp_modules_settings[maintenance_bypass_key]',
								'value' => isset( $settings['maintenance_bypass_key'] ) ? $settings['maintenance_bypass_key'] : '',
								'placeholder' => __( 'Enter secret key', 'tiny-wp-modules' ),
								'class' => 'tiny-input',
								'label' => __( 'Bypass Key:', 'tiny-wp-modules' ),
								'description' => __( 'Secret key to bypass maintenance mode. Add ?bypass=YOUR_KEY to any URL to access the site.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'user_roles_simple',
								'id' => 'maintenance_allowed_roles',
								'name' => 'tiny_wp_modules_settings[maintenance_allowed_roles]',
								'value' => isset( $settings['maintenance_allowed_roles'] ) ? $settings['maintenance_allowed_roles'] : array(),
								'label' => __( 'Allow frontend access for these user roles:', 'tiny-wp-modules' ),
								'description' => __( 'Select which user roles can access the frontend during maintenance mode.', 'tiny-wp-modules' )
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- Safe Media Section Heading -->
			<?php echo \TinyWpModules\Admin\Settings_Config::get_section_heading( 
				__( 'Safe Media', 'tiny-wp-modules' ),
				sprintf( __( 'Secure media handling and file upload features for %s.', 'tiny-wp-modules' ), __( 'WordPress', 'tiny-wp-modules' ) )
			); ?>

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

			<!-- AVIF Upload -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'AVIF Upload', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_avif_upload',
						'name' => 'tiny_wp_modules_settings[enable_avif_upload]',
						'value' => '1',
						'checked' => isset( $settings['enable_avif_upload'] ) ? $settings['enable_avif_upload'] : 0,
						'label' => __( 'Enable AVIF upload', 'tiny-wp-modules' ),
						'description' => __( 'Allow AVIF file uploads to the WordPress media library with proper mime type support.', 'tiny-wp-modules' ),
						'class' => 'avif-upload-module',
						'data_toggle' => 'avif-upload-config',
						'config_fields' => array(
							array(
								'type' => 'user_roles_simple',
								'id' => 'avif_upload_roles',
								'name' => 'tiny_wp_modules_settings[avif_upload_roles]',
								'value' => isset( $settings['avif_upload_roles'] ) ? $settings['avif_upload_roles'] : array(),
								'label' => __( 'Allow AVIF upload for these user roles:', 'tiny-wp-modules' ),
								'description' => __( 'Select which user roles can upload AVIF files.', 'tiny-wp-modules' )
							)
						)
					) );
					?>
				</td>
			</tr>

			<!-- Image Upload Control -->
			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Image Upload Control', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php 
					echo Components::render_expandable_module( array(
						'id' => 'enable_image_upload_control',
						'name' => 'tiny_wp_modules_settings[enable_image_upload_control]',
						'value' => '1',
						'checked' => isset( $settings['enable_image_upload_control'] ) ? $settings['enable_image_upload_control'] : 0,
						'label' => __( 'Enable image upload control', 'tiny-wp-modules' ),
						'description' => __( 'Control image uploads with automatic conversion, resizing, and orientation fixing.', 'tiny-wp-modules' ),
						'class' => 'image-upload-control-module',
						'data_toggle' => 'image-upload-control-config',
						'config_fields' => array(
							array(
								'type' => 'number',
								'id' => 'image_max_width',
								'name' => 'tiny_wp_modules_settings[image_max_width]',
								'value' => isset( $settings['image_max_width'] ) ? $settings['image_max_width'] : 1920,
								'placeholder' => '1920',
								'class' => 'tiny-input',
								'label' => __( 'Maximum width (pixels):', 'tiny-wp-modules' ),
								'description' => __( 'Images larger than this will be automatically resized.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'number',
								'id' => 'image_max_height',
								'name' => 'tiny_wp_modules_settings[image_max_height]',
								'value' => isset( $settings['image_max_height'] ) ? $settings['image_max_height'] : 1080,
								'placeholder' => '1080',
								'class' => 'tiny-input',
								'label' => __( 'Maximum height (pixels):', 'tiny-wp-modules' ),
								'description' => __( 'Images larger than this will be automatically resized.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'number',
								'id' => 'image_conversion_quality',
								'name' => 'tiny_wp_modules_settings[image_conversion_quality]',
								'value' => isset( $settings['image_conversion_quality'] ) ? $settings['image_conversion_quality'] : 82,
								'placeholder' => '82',
								'class' => 'tiny-input',
								'label' => __( 'JPEG quality (0-100):', 'tiny-wp-modules' ),
								'description' => __( 'Quality for JPEG conversion. Higher values mean better quality but larger file sizes.', 'tiny-wp-modules' )
							),
							array(
								'type' => 'info',
								'content' => '<p><strong>Features:</strong></p>
									<ul>
										<li>• Automatically converts BMP and non-transparent PNG to JPEG</li>
										<li>• Resizes images to specified maximum dimensions</li>
										<li>• Fixes image orientation based on EXIF data</li>
										<li>• Excludes images with "-nr" suffix from processing</li>
									</ul>'
							)
						)
					) );
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
