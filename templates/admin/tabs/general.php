<?php
/**
 * General Tab Content Template
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
								'value' => isset( $settings['faq_label'] ) ? $settings['faq_label'] : 'faq',
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

			<tr class="setting-row">
				<td class="setting-label">
					<strong><?php esc_html_e( 'Enable Elementor', 'tiny-wp-modules' ); ?></strong>
				</td>
				<td class="setting-control">
					<?php echo Components::render_switch( array(
						'id' => 'enable_elementor',
						'name' => 'tiny_wp_modules_settings[enable_elementor]',
						'value' => '1',
						'checked' => isset( $settings['enable_elementor'] ) ? $settings['enable_elementor'] : 0,
						'label' => __( 'Enable Elementor readymade widgets', 'tiny-wp-modules' )
					) ); ?>
					<div class="setting-description">
						<?php esc_html_e( 'Enable Elementor support for readymade widgets and enhanced functionality.', 'tiny-wp-modules' ); ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
