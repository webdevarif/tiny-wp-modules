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
use TinyWpModules\Admin\Tab_Manager;

// Get plugin slug
global $tiny_wp_modules_plugin;
$plugin_slug = $tiny_wp_modules_plugin ? $tiny_wp_modules_plugin->get_plugin_slug() : 'tiny-wp-modules';

// Ensure settings are registered with WordPress
if ( ! get_option( 'tiny_wp_modules_settings' ) ) {
	add_option( 'tiny_wp_modules_settings', array(
		'enable_faq' => '0',
		'enable_elementor' => '0',
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
				$sanitized['enable_faq'] = isset( $input['enable_faq'] ) ? '1' : '0';
				$sanitized['enable_elementor'] = isset( $input['enable_elementor'] ) ? '1' : '0';
				
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

// Get settings
$settings = get_option( 'tiny_wp_modules_settings', array() );

// Get tabs using Tab_Manager
$tabs = Tab_Manager::get_tabs( $settings );
?>

<div class="wrap" x-data="{ bannerVisible: true }">
	<!-- Full Card Container -->
	<div class="tiny-wp-modules-card-container">
		<!-- Top Banner with Notifications -->
		<div class="tiny-wp-modules-banner" id="tiny-wp-modules-banner" x-show="bannerVisible">
			<div class="banner-background">
				<div class="banner-pattern"></div>
				<div class="banner-shapes">
					<div class="shape shape-1"></div>
					<div class="shape shape-2"></div>
					<div class="shape shape-3"></div>
				</div>
			</div>
			<div class="banner-content">
				<div class="banner-left">
					<div class="banner-icon">
						<div class="icon-container">
							<img src="<?php echo esc_url( tiny_image( 'logo/32x32.png' ) ); ?>" alt="Tiny WP Modules Logo" class="logo-image">
							<div class="icon-glow"></div>
							<div class="icon-particles"></div>
						</div>
					</div>
					<div class="banner-text-content">
						<h3 class="banner-title"><?php esc_html_e( 'Welcome to Tiny WP Modules!', 'tiny-wp-modules' ); ?></h3>
						<p class="banner-text"><?php esc_html_e( 'Your WordPress site is ready to be enhanced with powerful modules and features.', 'tiny-wp-modules' ); ?></p>
					</div>
				</div>
				<button class="banner-close" type="button" aria-label="<?php esc_attr_e( 'Close', 'tiny-wp-modules' ); ?>" id="banner-close-btn" @click="bannerVisible = false; $el.closest('.tiny-wp-modules-banner').style.transition = 'opacity 0.3s ease'; $el.closest('.tiny-wp-modules-banner').style.opacity = '0'; setTimeout(() => $el.closest('.tiny-wp-modules-banner').remove(), 300)">
					<img src="<?php echo esc_url( tiny_icon( 'close.svg' ) ); ?>" alt="<?php esc_attr_e( 'Close', 'tiny-wp-modules' ); ?>" class="close-icon">
				</button>
			</div>
		</div>
		
		<!-- Success Notification -->
		<?php 
		// Debug: Check if settings-updated parameter is received
		error_log( 'Tiny WP Modules Template: $_GET contents: ' . print_r( $_GET, true ) );
		error_log( 'Tiny WP Modules Template: settings-updated value: ' . ( $_GET['settings-updated'] ?? 'not set' ) );
		?>
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
			<?php echo Tab_Manager::render_navigation( $tabs, $current_tab ); ?>

			<!-- Right Content Panel -->
			<div class="tiny-wp-modules-content-panel">
				<!-- Header -->
				<?php echo Tab_Manager::render_header( $tabs, $current_tab ); ?>

				<!-- Settings Table -->
				<div class="settings-table-container">
					<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" id="tiny-wp-modules-settings-form">
						<input type="hidden" name="action" value="tiny_wp_modules_save_settings">
						<?php wp_nonce_field( 'tiny_wp_modules_save_settings', 'tiny_wp_modules_nonce' ); ?>
						<input type="hidden" name="current_tab" value="<?php echo esc_attr( $current_tab ); ?>">
						
						<!-- Debug: Show form action -->
						<div style="background: #f0f0f0; padding: 5px; margin: 5px 0; font-size: 12px; border: 1px solid #ccc;">
							<strong>Debug:</strong> Form action: <?php echo admin_url( 'admin-post.php' ); ?>
						</div>
						
						<!-- Critical Settings - Always Include -->
						<input type="hidden" name="tiny_wp_modules_settings[enable_elementor]" value="<?php echo isset( $settings['enable_elementor'] ) ? esc_attr( $settings['enable_elementor'] ) : '0'; ?>">
						
						<!-- Tab Content -->
												<?php 
						// Include the appropriate tab template
						$tab_template_path = Tab_Manager::get_tab_template_path( $current_tab );
						if ( ! empty( $tab_template_path ) && file_exists( $tab_template_path ) ) {
							include $tab_template_path;
						} else {
							// Fallback for missing templates
							echo '<div class="tab-content" id="' . esc_attr( $current_tab ) . '-tab">';
							echo '<div class="notice notice-warning"><p>' . esc_html__( 'Tab content template not found.', 'tiny-wp-modules' ) . '</p></div>';
							echo '</div>';
						}
						?>
						
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