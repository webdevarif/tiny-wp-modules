<?php
/**
 * Updates Tab Content Template
 *
 * @package TinyWpModules\Templates\Admin\Tabs
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import classes
use TinyWpModules\Admin\Components;
?>

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
