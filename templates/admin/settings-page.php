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

// Get current tab
$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';

// Define tabs
$tabs = array(
	'general' => __( 'General', 'tiny-wp-modules' ),
	'updates' => __( 'Updates', 'tiny-wp-modules' ),
	'advanced' => __( 'Advanced', 'tiny-wp-modules' ),
);

// Get settings
$settings = get_option( 'tiny_wp_modules_settings', array() );
?>

<div class="wrap">
	<div class="tiny-wp-modules-settings-header">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<div class="tiny-wp-modules-save-button">
			<?php echo \TinyWpModules\Admin\Components::render_button( array(
				'type' => 'button',
				'text' => __( 'Save Changes', 'tiny-wp-modules' ),
				'id' => 'save-all-settings',
				'class' => 'button button-primary',
				'icon' => 'yes-alt'
			) ); ?>
		</div>
	</div>

	<nav class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
			<a href="?page=tiny-wp-modules-settings&tab=<?php echo esc_attr( $tab_key ); ?>" 
			   class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $tab_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="tiny-wp-modules-settings-content">
		<form method="post" action="options.php" id="tiny-wp-modules-settings-form">
			<?php settings_fields( 'tiny_wp_modules_options' ); ?>
			
			<?php if ( 'general' === $current_tab ) : ?>
				<div class="tab-content" id="general-tab">
					<div class="settings-section">
						<h2><?php esc_html_e( 'General Settings', 'tiny-wp-modules' ); ?></h2>
						<table class="form-table">
							<?php echo \TinyWpModules\Admin\Components::render_form_row( array(
								'label' => __( 'Enable Modules', 'tiny-wp-modules' ),
								'content' => \TinyWpModules\Admin\Components::render_switch( array(
									'id' => 'enable_modules',
									'name' => 'tiny_wp_modules_settings[enable_modules]',
									'value' => '1',
									'checked' => isset( $settings['enable_modules'] ) ? $settings['enable_modules'] : 1,
									'label' => __( 'Enable all modules by default', 'tiny-wp-modules' )
								) ),
								'description' => __( 'Enable all modules by default', 'tiny-wp-modules' )
							) ); ?>
							
							<?php echo \TinyWpModules\Admin\Components::render_form_row( array(
								'label' => __( 'Debug Mode', 'tiny-wp-modules' ),
								'content' => \TinyWpModules\Admin\Components::render_switch( array(
									'id' => 'debug_mode',
									'name' => 'tiny_wp_modules_settings[debug_mode]',
									'value' => '1',
									'checked' => isset( $settings['debug_mode'] ) ? $settings['debug_mode'] : 0,
									'label' => __( 'Enable debug mode (for development)', 'tiny-wp-modules' )
								) ),
								'description' => __( 'Enable debug mode (for development)', 'tiny-wp-modules' )
							) ); ?>
						</table>
					</div>
				</div>

			<?php elseif ( 'updates' === $current_tab ) : ?>
				<div class="tab-content" id="updates-tab">
					<div class="settings-section">
						<h2><?php esc_html_e( 'GitHub Updates', 'tiny-wp-modules' ); ?></h2>
						<p class="description">
							<?php esc_html_e( 'Check for updates from your public GitHub repository. Updates are checked manually.', 'tiny-wp-modules' ); ?>
						</p>
						
						<table class="form-table">
							<?php echo \TinyWpModules\Admin\Components::render_form_row( array(
								'label' => __( 'Repository', 'tiny-wp-modules' ),
								'content' => '<strong>webdevarif/tiny-wp-modules</strong>',
								'description' => __( 'Your public GitHub repository for manual updates.', 'tiny-wp-modules' )
							) ); ?>
							
							<?php echo \TinyWpModules\Admin\Components::render_form_row( array(
								'label' => __( 'Current Version', 'tiny-wp-modules' ),
								'content' => '<strong>' . TINY_WP_MODULES_VERSION . '</strong>',
								'description' => __( 'Currently installed plugin version.', 'tiny-wp-modules' )
							) ); ?>
							
							<?php echo \TinyWpModules\Admin\Components::render_form_row( array(
								'label' => __( 'Check for Updates', 'tiny-wp-modules' ),
								'content' => \TinyWpModules\Admin\Components::render_button( array(
									'type' => 'button',
									'text' => __( 'Check for Updates', 'tiny-wp-modules' ),
									'id' => 'check-for-updates',
									'class' => 'button button-secondary',
									'icon' => 'update'
								) ),
								'description' => __( 'Manually check for updates from your GitHub repository.', 'tiny-wp-modules' )
							) ); ?>
						</table>
					</div>
				</div>

			<?php elseif ( 'advanced' === $current_tab ) : ?>
				<div class="tab-content" id="advanced-tab">
					<div class="settings-section">
						<h2><?php esc_html_e( 'Advanced Settings', 'tiny-wp-modules' ); ?></h2>
						<p class="description">
							<?php esc_html_e( 'Advanced configuration options for developers and power users.', 'tiny-wp-modules' ); ?>
						</p>
						
						<table class="form-table">
							<tr>
								<th scope="row">
									<?php esc_html_e( 'No Advanced Settings', 'tiny-wp-modules' ); ?>
								</th>
								<td>
									<p class="description">
										<?php esc_html_e( 'No advanced settings are currently available.', 'tiny-wp-modules' ); ?>
									</p>
								</td>
							</tr>
						</table>
					</div>
				</div>
			<?php endif; ?>
		</form>
	</div>
</div>

<style>
.tiny-wp-modules-settings-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}

.tiny-wp-modules-settings-header h1 {
	margin: 0;
}

.tiny-wp-modules-save-button {
	margin-left: auto;
}

.tiny-wp-modules-settings-content {
	background: white;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 20px;
	margin-top: 20px;
}

.settings-section {
	margin-bottom: 30px;
}

.settings-section h2 {
	margin-top: 0;
	padding-bottom: 10px;
	border-bottom: 1px solid #eee;
}

.nav-tab-wrapper {
	margin-bottom: 0;
}

.tab-content {
	padding-top: 20px;
}

.form-table th {
	width: 200px;
}
</style> 