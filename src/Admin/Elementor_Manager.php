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
				'class' => 'Elementor_Widgets_Module',
				'file' => 'src/Modules/Elementor/Widgets_Module.php'
			),
			'tags' => array(
				'id' => 'elementor_tags',
				'label' => __( 'Elementor Tags', 'tiny-wp-modules' ),
				'description' => __( 'Add custom Elementor tags and shortcodes', 'tiny-wp-modules' ),
				'icon' => 'tags',
				'class' => 'Elementor_Tags_Module',
				'file' => 'src/Modules/Elementor/Tags_Module.php'
			),
			'woocommerce' => array(
				'id' => 'elementor_woocommerce',
				'label' => __( 'Elementor WooCommerce', 'tiny-wp-modules' ),
				'description' => __( 'Enhanced WooCommerce integration with Elementor', 'tiny-wp-modules' ),
				'icon' => 'woocommerce',
				'class' => 'Elementor_WooCommerce_Module',
				'file' => 'src/Modules/Elementor/WooCommerce_Module.php'
			)
		);
	}

	/**
	 * Get module items based on module type
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
	 * Get widget items
	 *
	 * @return array Array of widget items.
	 */
	private static function get_widget_items() {
		return array(
			'hero_section' => array(
				'id' => 'hero_section_widget',
				'label' => __( 'Hero Section', 'tiny-wp-modules' ),
				'description' => __( 'Full-width hero section with background image and text overlay', 'tiny-wp-modules' ),
				'category' => 'content'
			),
			'testimonials' => array(
				'id' => 'testimonials_widget',
				'label' => __( 'Testimonials', 'tiny-wp-modules' ),
				'description' => __( 'Customer testimonials with star ratings and quotes', 'tiny-wp-modules' ),
				'category' => 'content'
			),
			'pricing_table' => array(
				'id' => 'pricing_table_widget',
				'label' => __( 'Pricing Table', 'tiny-wp-modules' ),
				'description' => __( 'Professional pricing tables with features and CTAs', 'tiny-wp-modules' ),
				'category' => 'business'
			),
			'team_members' => array(
				'id' => 'team_members_widget',
				'label' => __( 'Team Members', 'tiny-wp-modules' ),
				'description' => __( 'Team member cards with photos, names, and social links', 'tiny-wp-modules' ),
				'category' => 'business'
			),
			'countdown_timer' => array(
				'id' => 'countdown_timer_widget',
				'label' => __( 'Countdown Timer', 'tiny-wp-modules' ),
				'description' => __( 'Animated countdown timer for events and promotions', 'tiny-wp-modules' ),
				'category' => 'marketing'
			),
			'progress_bars' => array(
				'id' => 'progress_bars_widget',
				'label' => __( 'Progress Bars', 'tiny-wp-modules' ),
				'description' => __( 'Animated progress bars for skills and achievements', 'tiny-wp-modules' ),
				'category' => 'content'
			)
		);
	}

	/**
	 * Get tag items
	 *
	 * @return array Array of tag items.
	 */
	private static function get_tag_items() {
		return array(
			'user_info' => array(
				'id' => 'user_info_tag',
				'label' => __( 'User Info', 'tiny-wp-modules' ),
				'description' => __( 'Display current user information and profile data', 'tiny-wp-modules' ),
				'category' => 'user'
			),
			'post_meta' => array(
				'id' => 'post_meta_tag',
				'label' => __( 'Post Meta', 'tiny-wp-modules' ),
				'description' => __( 'Show post metadata like author, date, categories', 'tiny-wp-modules' ),
				'category' => 'content'
			),
			'site_info' => array(
				'id' => 'site_info_tag',
				'label' => __( 'Site Info', 'tiny-wp-modules' ),
				'description' => __( 'Display site title, description, and other site data', 'tiny-wp-modules' ),
				'category' => 'site'
			),
			'custom_fields' => array(
				'id' => 'custom_fields_tag',
				'label' => __( 'Custom Fields', 'tiny-wp-modules' ),
				'description' => __( 'Show custom field values from posts and pages', 'tiny-wp-modules' ),
				'category' => 'content'
			),
			'query_loop' => array(
				'id' => 'query_loop_tag',
				'label' => __( 'Query Loop', 'tiny-wp-modules' ),
				'description' => __( 'Advanced query loop for custom post types and taxonomies', 'tiny-wp-modules' ),
				'category' => 'content'
			)
		);
	}

	/**
	 * Get WooCommerce items
	 *
	 * @return array Array of WooCommerce items.
	 */
	private static function get_woocommerce_items() {
		return array(
			'product_grid' => array(
				'id' => 'product_grid_widget',
				'label' => __( 'Product Grid', 'tiny-wp-modules' ),
				'description' => __( 'Customizable product grid with filtering options', 'tiny-wp-modules' ),
				'category' => 'products'
			),
			'product_carousel' => array(
				'id' => 'product_carousel_widget',
				'label' => __( 'Product Carousel', 'tiny-wp-modules' ),
				'description' => __( 'Swipeable product carousel for featured products', 'tiny-wp-modules' ),
				'category' => 'products'
			),
			'category_showcase' => array(
				'id' => 'category_showcase_widget',
				'label' => __( 'Category Showcase', 'tiny-wp-modules' ),
				'description' => __( 'Display product categories with images and descriptions', 'tiny-wp-modules' ),
				'category' => 'categories'
			),
			'cart_summary' => array(
				'id' => 'cart_summary_widget',
				'label' => __( 'Cart Summary', 'tiny-wp-modules' ),
				'description' => __( 'Mini cart summary with product count and total', 'tiny-wp-modules' ),
				'category' => 'cart'
			),
			'wishlist' => array(
				'id' => 'wishlist_widget',
				'label' => __( 'Wishlist', 'tiny-wp-modules' ),
				'description' => __( 'User wishlist display with add to cart functionality', 'tiny-wp-modules' ),
				'category' => 'user'
			),
			'product_comparison' => array(
				'id' => 'product_comparison_widget',
				'label' => __( 'Product Comparison', 'tiny-wp-modules' ),
				'description' => __( 'Compare multiple products side by side', 'tiny-wp-modules' ),
				'category' => 'products'
			)
		);
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
		<div class="tab-content" id="elementor-tab">
			<form method="post" action="">
				<?php wp_nonce_field( 'tiny_wp_modules_save_settings', 'tiny_wp_modules_nonce' ); ?>
				<input type="hidden" name="current_tab" value="elementor">
				
				<div class="elementor-modules-grid">
					<?php foreach ( $modules as $module_key => $module_data ) : ?>
						<div class="elementor-module-card">
							<div class="module-header">
								<div class="module-icon">
									<span class="dashicons dashicons-<?php echo esc_attr( $module_data['icon'] ); ?>"></span>
								</div>
								<div class="module-info">
									<h3><?php echo esc_html( $module_data['label'] ); ?></h3>
									<p><?php echo esc_html( $module_data['description'] ); ?></p>
								</div>
								<div class="module-toggle">
									<?php
									$module_enabled = isset( $settings[ $module_data['id'] ] ) ? $settings[ $module_data['id'] ] : '0';
									echo Components::render_switch( array(
										'id' => $module_data['id'],
										'name' => 'tiny_wp_modules_settings[' . $module_data['id'] . ']',
										'value' => '1',
										'checked' => $module_enabled,
										'label' => '',
										'class' => 'module-switch'
									) );
									?>
								</div>
							</div>
							
							<?php if ( $module_enabled ) : ?>
								<div class="module-items" id="<?php echo esc_attr( $module_data['id'] ); ?>-items">
									<div class="items-grid">
										<?php
										$items = self::get_module_items( $module_key );
										foreach ( $items as $item_key => $item_data ) :
										?>
											<div class="item-card">
												<div class="item-header">
													<h4><?php echo esc_html( $item_data['label'] ); ?></h4>
													<span class="item-category"><?php echo esc_html( $item_data['category'] ); ?></span>
												</div>
												<p class="item-description"><?php echo esc_html( $item_data['description'] ); ?></p>
												<div class="item-toggle">
													<?php
													$item_enabled = isset( $settings[ $item_data['id'] ] ) ? $settings[ $item_data['id'] ] : '0';
													echo Components::render_switch( array(
														'id' => $item_data['id'],
														'name' => 'tiny_wp_modules_settings[' . $item_data['id'] . ']',
														'value' => '1',
														'checked' => $item_enabled,
														'label' => '',
														'class' => 'item-switch'
													) );
													?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
				
				<div class="elementor-save-section">
					<button type="submit" class="button button-primary elementor-save-button">
						<span class="dashicons dashicons-yes"></span>
						<?php esc_html_e( 'Save Changes', 'tiny-wp-modules' ); ?>
					</button>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}
