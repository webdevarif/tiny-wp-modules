<?php
/**
 * Elementor Manager Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

/**
 * Manages Elementor functionality with dynamic grid system
 */
class Elementor_Manager {

	/**
	 * Get all Elementor modules
	 *
	 * @return array Array of Elementor modules.
	 */
	public static function get_modules() {
		return array(
					'widgets' => array(
			'id' => 'elementor_widgets',
			'label' => __( 'Elementor Widgets', 'tiny-wp-modules' ),
			'description' => __( 'Enable custom Elementor widgets for enhanced page building', 'tiny-wp-modules' ),
			'icon' => 'widgets',
			'class' => 'Widgets_Module',
			'file' => 'src/Modules/Elementor/Widgets_Module.php'
		),
		'tags' => array(
			'id' => 'elementor_tags',
			'label' => __( 'Elementor Tags', 'tiny-wp-modules' ),
			'description' => __( 'Add custom Elementor tags and shortcodes', 'tiny-wp-modules' ),
			'icon' => 'tags',
			'class' => 'Tags_Module',
			'file' => 'src/Modules/Elementor/Tags_Module.php'
		),
		'woocommerce' => array(
			'id' => 'elementor_woocommerce',
			'label' => __( 'WooCommerce', 'tiny-wp-modules' ),
			'description' => __( 'Enhanced WooCommerce integration with Elementor', 'tiny-wp-modules' ),
			'icon' => 'woocommerce',
			'class' => 'WooCommerce_Module',
			'file' => 'src/Modules/Elementor/WooCommerce_Module.php'
		)
		);
	}

	/**
	 * Get module items dynamically from module classes
	 *
	 * @param string $module_type Module type (widgets, tags, woocommerce).
	 * @return array Array of module items.
	 */
	public static function get_module_items( $module_type ) {
		$items = array();

		switch ( $module_type ) {
			case 'widgets':
				$items = self::get_widget_items();
				break;
			case 'tags':
				$items = self::get_tag_items();
				break;
			case 'woocommerce':
				$items = self::get_woocommerce_items();
				break;
		}

		return $items;
	}

	/**
	 * Get widget items dynamically
	 *
	 * @return array Array of widget items.
	 */
	private static function get_widget_items() {
		// Get widgets from the Widgets_Module
		$widgets_module = new \TinyWpModules\Modules\Elementor\Widgets_Module();
		return $widgets_module->get_enabled_items_for_settings();
	}
	
	/**
	 * Get item label by item ID
	 *
	 * @param string $item_id Item ID.
	 * @return string Item label.
	 */
	private static function get_item_label( $item_id ) {
		// Convert item ID to readable label
		$label = str_replace( '_', ' ', $item_id );
		$label = str_replace( ' widget', '', $label );
		$label = str_replace( ' tag', '', $label );
		return ucwords( $label );
	}

	/**
	 * Get item category - all items belong to the same category
	 *
	 * @param string $item_id Item ID.
	 * @return string Item category.
	 */
	private static function get_item_category( $item_id ) {
		return self::get_plugin_category();
	}



	/**
	 * Get tag items dynamically
	 *
	 * @return array Array of tag items.
	 */
	private static function get_tag_items() {
		// Get tags from the Tags_Module
		$tags_module = new \TinyWpModules\Modules\Elementor\Tags_Module();
		return $tags_module->get_enabled_items_for_settings();
	}

	/**
	 * Get WooCommerce items dynamically
	 *
	 * @return array Array of WooCommerce items.
	 */
	private static function get_woocommerce_items() {
		// Get WooCommerce widgets from the WooCommerce_Module
		$woocommerce_module = new \TinyWpModules\Modules\Elementor\WooCommerce_Module();
		return $woocommerce_module->get_enabled_items_for_settings();
	}

	/**
	 * Get the plugin category name dynamically
	 *
	 * @return string Plugin category name.
	 */
	private static function get_plugin_category() {
		// Get plugin name from WordPress
		$plugin_data = get_plugin_data( TINY_WP_MODULES_PLUGIN_FILE );
		$plugin_name = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : 'Tiny WP Modules';
		
		// Extract the main name part (remove "Tiny WP" if present)
		if ( strpos( $plugin_name, 'Tiny WP' ) === 0 ) {
			$plugin_name = trim( str_replace( 'Tiny WP', '', $plugin_name ) );
		}
		
		// If empty after removal, use default
		if ( empty( $plugin_name ) ) {
			$plugin_name = 'Module';
		}
		
		return 'Tiny ' . $plugin_name;
	}

	/**
	 * Render the Elementor tab content
	 *
	 * @return string HTML output for Elementor tab.
	 */
	public static function render_tab_content() {
		$modules = self::get_modules();
		$settings = get_option( 'tiny_wp_modules_settings', array() );

		ob_start();
		?>
		<div class="tab-content" id="elementor-tab" x-data="{}">
			<div class="elementor-vertical-groups">
				<?php foreach ( $modules as $module_key => $module_data ) : ?>
					<?php 
					// Check dependencies for this module
					$module_class = 'TinyWpModules\\Modules\\Elementor\\' . $module_data['class'];
					$module_instance = new $module_class();
					$dependencies_met = $module_instance->are_dependencies_met();
					$dependency_warning = $module_instance->get_dependency_warning();
					
					$module_enabled = isset( $settings[ $module_data['id'] ] ) && $settings[ $module_data['id'] ] ? true : false;
					// Only allow enabling if dependencies are met
					$can_enable = $dependencies_met;
					?>
					<div class="elementor-group" x-data="{ 
						groupEnabled: <?php echo $module_enabled ? 'true' : 'false'; ?>
					}">
						
						<!-- Group Header with Switch -->
						<div class="group-header" :class="{ 'enabled': groupEnabled, 'disabled': !groupEnabled }">
							<div class="group-switch">
								<?php
								echo Components::render_switch( array(
									'id' => $module_data['id'],
									'name' => 'tiny_wp_modules_settings[' . $module_data['id'] . ']',
									'value' => '1',
									'checked' => $module_enabled,
									'label' => '',
									'class' => 'group-switch-toggle',
									'x_on_change' => 'groupEnabled = $event.target.checked',
									'disabled' => !$can_enable
								) );
								?>
							</div>
							
							<div class="group-info">
								<div class="group-details">
									<h3><?php echo esc_html( $module_data['label'] ); ?></h3>
									<p><?php echo esc_html( $module_data['description'] ); ?></p>
									<?php if ( !$dependencies_met && $module_enabled && !empty( $dependency_warning ) ) : ?>
										<div class="dependency-warning">
											<span class="warning-icon">⚠️</span>
											<span class="warning-text"><?php echo esc_html( $dependency_warning ); ?></span>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						
						<!-- Group Content -->
						<div class="group-content" 
							 x-show="groupEnabled"
							 x-transition:enter="transition ease-out duration-300"
							 x-transition:enter-start="opacity-0 transform -translate-y-4"
							 x-transition:enter-end="opacity-100 transform translate-y-0"
							 x-transition:leave="transition ease-in duration-200"
							 x-transition:leave-start="opacity-100 transform translate-y-0"
							 x-transition:leave-end="opacity-0 transform -translate-y-4">
							<div class="group-items">
								<?php
								$items = self::get_module_items( $module_key );
								foreach ( $items as $item_key => $item_data ) :
									// Check if this item can be enabled based on dependencies
									$item_can_be_enabled = $module_instance->can_item_be_enabled( $item_data['id'] );
								?>
									<div class="group-item">
										<div class="item-info">
											<h4><?php echo esc_html( $item_data['label'] ); ?></h4>
											<span class="item-category"><?php echo esc_html( $item_data['category'] ); ?></span>
										</div>
										<div class="item-toggle">
											<?php
											$item_enabled = isset( $settings[ $item_data['id'] ] ) ? $settings[ $item_data['id'] ] : '0';
											echo Components::render_switch( array(
												'id' => $item_data['id'],
												'name' => 'tiny_wp_modules_settings[' . $item_data['id'] . ']',
												'value' => '1',
												'checked' => $item_enabled,
												'label' => '',
												'class' => 'item-switch',
												'disabled' => !$item_can_be_enabled
											) );
											?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
