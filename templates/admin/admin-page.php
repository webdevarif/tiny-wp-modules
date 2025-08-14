<?php
/**
 * Admin Page Template
 *
 * @package TinyWpModules\Templates\Admin
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import classes
use TinyWpModules\Admin\Components;
?>

<div class="wrap">
	<div class="tiny-wp-modules-dashboard">
		<div class="tiny-wp-modules-header">
			<div class="tiny-wp-modules-header-content">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<p><?php esc_html_e( 'Welcome to Tiny WP Modules. Manage your modules and settings here.', 'tiny-wp-modules' ); ?></p>
			</div>
		</div>

		<div class="tiny-wp-modules-content">
			<div class="tiny-wp-modules-grid">
				<?php echo Components::render_card( array(
					'title' => __( 'Plugin Status', 'tiny-wp-modules' ),
					'icon' => 'admin-tools',
					'content' => '<ul class="status-list">
						<li>
							<span class="status-label">' . __( 'Version:', 'tiny-wp-modules' ) . '</span>
							<span class="status-value">' . TINY_WP_MODULES_VERSION . '</span>
						</li>
						<li>
							<span class="status-label">' . __( 'PHP Version:', 'tiny-wp-modules' ) . '</span>
							<span class="status-value">' . PHP_VERSION . '</span>
						</li>
						<li>
							<span class="status-label">' . __( 'WordPress Version:', 'tiny-wp-modules' ) . '</span>
							<span class="status-value">' . get_bloginfo( 'version' ) . '</span>
						</li>
					</ul>',
					'actions' => array(
						array(
							'text' => __( 'Refresh Status', 'tiny-wp-modules' ),
							'id' => 'refresh-status',
							'class' => 'tiny-btn tiny-btn-secondary',
							'icon' => 'update'
						)
					)
				) ); ?>



				<?php echo Components::render_card( array(
					'title' => __( 'Module Information', 'tiny-wp-modules' ),
					'icon' => 'admin-plugins',
					'content' => '<ul>
						<li>' . __( 'Hook loader class', 'tiny-wp-modules' ) . '</li>
						<li>' . __( 'Admin interface', 'tiny-wp-modules' ) . '</li>
						<li>' . __( 'Public handler', 'tiny-wp-modules' ) . '</li>
						<li>' . __( 'Update system', 'tiny-wp-modules' ) . '</li>
						<li>' . __( 'FAQ module', 'tiny-wp-modules' ) . '</li>
					</ul>',
					'actions' => array(
						array(
							'text' => __( 'View Details', 'tiny-wp-modules' ),
							'url' => '#',
							'class' => 'tiny-btn tiny-btn-secondary',
							'icon' => 'visibility'
						)
					)
				) ); ?>
			</div>
		</div>
	</div>
</div>

<style>
.tiny-wp-modules-dashboard {
	margin-top: 20px;
}

.tiny-wp-modules-header {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 30px;
	border-radius: 8px;
	margin-bottom: 30px;
}

.tiny-wp-modules-header h2 {
	margin: 0 0 10px 0;
	font-size: 24px;
}

.tiny-wp-modules-header p {
	margin: 0;
	opacity: 0.9;
}

.status-list {
	list-style: none;
	margin: 0;
	padding: 0;
}

.status-list li {
	display: flex;
	justify-content: space-between;
	padding: 8px 0;
	border-bottom: 1px solid #eee;
}

.status-list li:last-child {
	border-bottom: none;
}

.status-label {
	font-weight: 600;
	color: #666;
}

.status-value {
	color: #333;
	font-family: "Courier New", monospace;
	background: #f8f9fa;
	padding: 2px 6px;
	border-radius: 3px;
}

.module-info ul {
	margin: 10px 0 0 0;
	padding-left: 20px;
}

.module-info li {
	margin-bottom: 5px;
}
</style> 