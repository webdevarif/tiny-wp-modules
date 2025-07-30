<?php
/**
 * Settings Page Template
 *
 * @package TinyWpModules\Templates\Admin
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import classes
use TinyWpModules\Admin\Components;

// Get plugin slug
global $tiny_wp_modules_plugin;
$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';

// Ensure settings are registered with WordPress
if ( ! get_option( 'tiny_wp_modules_settings' ) ) {
	add_option( 'tiny_wp_modules_settings', array(
		'enable_modules' => '1',
		'debug_mode' => '0',
		'enable_faq' => '0',
	) );
}

// Register the settings with WordPress if not already registered
if ( ! has_action( 'admin_init', array( 'TinyWpModules\Admin\Settings', 'register_settings' ) ) ) {
	register_setting(
		$plugin_slug . '_options',
		'tiny_wp_modules_settings',
		array(
			'sanitize_callback' => function( $input ) {
				$sanitized = array();
				$sanitized['enable_modules'] = isset( $input['enable_modules'] ) ? '1' : '0';
				$sanitized['debug_mode'] = isset( $input['debug_mode'] ) ? '1' : '0';
				$sanitized['enable_faq'] = isset( $input['enable_faq'] ) ? '1' : '0';
				
				if ( isset( $input['faq_label'] ) ) {
					$sanitized['faq_label'] = sanitize_text_field( $input['faq_label'] );
				}
				
				if ( isset( $input['faq_slug'] ) ) {
					$sanitized['faq_slug'] = sanitize_title( $input['faq_slug'] );
				}
				
				return $sanitized;
			},
			'type' => 'array',
		)
	);
}

// Get current tab
$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';

// Define tabs with icons
$tabs = array(
	'general' => array(
		'label' => __( 'General', 'tiny-wp-modules' ),
		'icon' => 'admin-generic'
	),
	'updates' => array(
		'label' => __( 'Updates', 'tiny-wp-modules' ),
		'icon' => 'update'
	),
	'advanced' => array(
		'label' => __( 'Advanced', 'tiny-wp-modules' ),
		'icon' => 'admin-tools'
	),
);

// Get settings
$settings = get_option( 'tiny_wp_modules_settings', array() );

// Debug: Check what value is stored for allowed_login_paths
if ( isset( $settings['allowed_login_paths'] ) ) {
	error_log( 'Tiny WP Modules: allowed_login_paths value: ' . $settings['allowed_login_paths'] );
}
?>

<div class="wrap">
	<!-- Full Card Container -->
	<div class="tiny-wp-modules-card-container">
		<!-- Top Banner with Notifications -->
		<div class="tiny-wp-modules-banner">
			<div class="banner-content">
				<span class="banner-text"><?php esc_html_e( 'Tiny WP Modules is ready to enhance your WordPress site', 'tiny-wp-modules' ); ?></span>
				<button class="banner-close" type="button" aria-label="<?php esc_attr_e( 'Close', 'tiny-wp-modules' ); ?>">
					<span class="dashicons dashicons-no-alt"></span>
				</button>
	</div>
		</div>
		
		<!-- Success Notification -->
		<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) : ?>
			<div class="tiny-wp-modules-notification success">
				<div class="notification-content">
					<span class="notification-text"><?php esc_html_e( 'Settings saved successfully!', 'tiny-wp-modules' ); ?></span>
					<button class="notification-close" type="button" aria-label="<?php esc_attr_e( 'Close', 'tiny-wp-modules' ); ?>">
						<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>
			</div>
		<?php endif; ?>

		<!-- Main Content Area -->
		<div class="tiny-wp-modules-main-area">
			<!-- Left Navigation Menu -->
			<div class="tiny-wp-modules-nav-menu">
				<?php foreach ( $tabs as $tab_key => $tab_data ) : ?>
					<a href="?page=tiny-wp-modules-settings&tab=<?php echo esc_attr( $tab_key ); ?>" 
					   class="nav-menu-item <?php echo $current_tab === $tab_key ? 'active' : ''; ?>">
						<span class="dashicons dashicons-<?php echo esc_attr( $tab_data['icon'] ); ?>"></span>
						<span class="nav-menu-label"><?php echo esc_html( $tab_data['label'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>

			<!-- Right Content Panel -->
			<div class="tiny-wp-modules-content-panel">
				<!-- Header -->
				<div class="content-header">
					<div class="header-content">
						<div class="header-icon">
							<span class="dashicons dashicons-<?php echo esc_attr( $tabs[$current_tab]['icon'] ); ?>"></span>
						</div>
						<div class="header-text">
							<h2><?php echo esc_html( $tabs[$current_tab]['label'] ); ?></h2>
							<p>
								<?php 
								$tab_descriptions = array(
									'general' => __( 'Configure general plugin settings and module options', 'tiny-wp-modules' ),
									'updates' => __( 'Manage plugin updates and version information', 'tiny-wp-modules' ),
									'advanced' => __( 'Advanced configuration and developer options', 'tiny-wp-modules' ),
								);
								echo esc_html( $tab_descriptions[$current_tab] ?? __( 'Configure plugin settings', 'tiny-wp-modules' ) );
								?>
							</p>
		</div>
	</div>
</div>

				<!-- Settings Table -->
				<div class="settings-table-container">
					<form method="post" action="" id="tiny-wp-modules-settings-form">
						<?php wp_nonce_field( 'tiny_wp_modules_save_settings', 'tiny_wp_modules_nonce' ); ?>
						<input type="hidden" name="current_tab" value="<?php echo esc_attr( $current_tab ); ?>">
						
						<?php if ( 'general' === $current_tab ) : ?>
							<div class="tab-content" id="general-tab">
								<table class="settings-table">
									<tbody>
										<tr class="setting-row">
											<td class="setting-label">
												<strong><?php esc_html_e( 'Enable Modules', 'tiny-wp-modules' ); ?></strong>
											</td>
											<td class="setting-control">
												<?php echo Components::render_switch( array(
													'id' => 'enable_modules',
													'name' => 'tiny_wp_modules_settings[enable_modules]',
													'value' => '1',
													'checked' => isset( $settings['enable_modules'] ) ? $settings['enable_modules'] : 1,
													'label' => __( 'Enable all modules by default', 'tiny-wp-modules' )
												) ); ?>
												<div class="setting-description">
													<?php esc_html_e( 'Enable all modules by default for enhanced functionality.', 'tiny-wp-modules' ); ?>
												</div>
											</td>
										</tr>

										<tr class="setting-row">
											<td class="setting-label">
												<strong><?php esc_html_e( 'Debug Mode', 'tiny-wp-modules' ); ?></strong>
											</td>
											<td class="setting-control">
												<?php echo Components::render_switch( array(
													'id' => 'debug_mode',
													'name' => 'tiny_wp_modules_settings[debug_mode]',
													'value' => '1',
													'checked' => isset( $settings['debug_mode'] ) ? $settings['debug_mode'] : 0,
													'label' => __( 'Enable debug mode (for development)', 'tiny-wp-modules' )
												) ); ?>
												<div class="setting-description">
													<?php esc_html_e( 'Enable debug mode for development and troubleshooting purposes.', 'tiny-wp-modules' ); ?>
												</div>
											</td>
										</tr>

										<tr class="setting-row">
											<td class="setting-label">
												<strong><?php esc_html_e( 'Enable FAQ', 'tiny-wp-modules' ); ?></strong>
											</td>
											<td class="setting-control">
												<?php 
												$faq_enabled = isset( $settings['enable_faq'] ) && ( $settings['enable_faq'] === '1' || $settings['enable_faq'] === 1 || $settings['enable_faq'] === true || $settings['enable_faq'] === 'on' );
												
												echo Components::render_expandable_module( array(
													'id' => 'enable_faq',
													'name' => 'tiny_wp_modules_settings[enable_faq]',
													'value' => '1',
													'checked' => isset( $settings['enable_faq'] ) ? $settings['enable_faq'] : 0,
													'label' => __( 'Enable FAQ custom post type', 'tiny-wp-modules' ),
													'description' => __( 'Enable FAQ custom post type with custom labels and slug.', 'tiny-wp-modules' ),
													'class' => 'faq-module',
													'data_toggle' => 'faq-config',
													'config_fields' => array(
														array(
															'type' => 'group_header',
															'title' => __( 'FAQ Post Type Settings', 'tiny-wp-modules' ),
															'description' => __( 'Configure the custom post type for FAQ entries.', 'tiny-wp-modules' ),
															'class' => 'faq-group-header'
														),
														array(
															'type' => 'text',
															'id' => 'faq_label',
															'name' => 'tiny_wp_modules_settings[faq_label]',
															'value' => isset( $settings['faq_label'] ) ? $settings['faq_label'] : 'FAQ',
															'placeholder' => __( 'Enter FAQ post type label', 'tiny-wp-modules' ),
															'class' => 'tiny-input',
															'label' => __( 'Post Type Label', 'tiny-wp-modules' ),
															'description' => __( 'Label for the FAQ custom post type (e.g., FAQ, Questions, Help)', 'tiny-wp-modules' )
														),
														array(
															'type' => 'text',
															'id' => 'faq_slug',
															'name' => 'tiny_wp_modules_settings[faq_slug]',
															'value' => isset( $settings['faq_slug'] ) ? $settings['faq_slug'] : 'faq',
															'placeholder' => __( 'Enter FAQ post type slug', 'tiny-wp-modules' ),
															'class' => 'tiny-input',
															'label' => __( 'Post Type Slug', 'tiny-wp-modules' ),
															'description' => __( 'URL slug for the FAQ custom post type (e.g., faq, questions, help)', 'tiny-wp-modules' )
														)
													)
												) );
												?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>

						<?php elseif ( 'updates' === $current_tab ) : ?>
							<div class="tab-content" id="updates-tab">
								<table class="settings-table">
									<tbody>
										<tr class="setting-row">
											<td class="setting-label">
												<strong><?php esc_html_e( 'Check for Updates', 'tiny-wp-modules' ); ?></strong>
											</td>
											<td class="setting-control">
												<?php echo Components::render_button( array(
													'type' => 'button',
													'text' => __( 'Check for Updates', 'tiny-wp-modules' ),
													'id' => 'check-for-updates',
													'class' => 'tiny-btn tiny-btn-primary',
													'icon' => 'update'
												) ); ?>
												<div class="setting-description">
													<?php esc_html_e( 'Manually check for updates from your GitHub repository.', 'tiny-wp-modules' ); ?>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>

						<?php elseif ( 'advanced' === $current_tab ) : ?>
							<div class="tab-content" id="advanced-tab">
								<table class="settings-table">
									<tbody>
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
													// 'description' => '',
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
															// 'description' => '',
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
						<?php endif; ?>
						
						<!-- Submit Button -->
						<div class="form-submit-section">
							<?php echo Components::render_button( array(
								'type' => 'submit',
								'text' => __( 'Save Changes', 'tiny-wp-modules' ),
								'id' => 'submit',
								'class' => 'tiny-btn tiny-btn-primary',
								'icon' => 'yes-alt'
							) ); ?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div> 