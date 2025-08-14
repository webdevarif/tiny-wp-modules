<?php
/**
 * Tab Manager Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

/**
 * Centralized tab management for the plugin
 */
class Tab_Manager {

	/**
	 * Get all available tabs with their configurations
	 *
	 * @param array $settings Current plugin settings.
	 * @return array Array of tabs with their configurations.
	 */
	public static function get_tabs( $settings = array() ) {
		$tabs = array(
			'general' => array(
				'label' => __( 'General', 'tiny-wp-modules' ),
				'icon' => self::get_general_icon(),
				'icon_type' => 'inline_svg',
				'description' => __( 'Configure general plugin settings and module options', 'tiny-wp-modules' ),
				'always_visible' => true
			),

			'advanced' => array(
				'label' => __( 'Advanced', 'tiny-wp-modules' ),
				'icon' => self::get_advanced_icon(),
				'icon_type' => 'inline_svg',
				'description' => __( 'Advanced configuration and developer options', 'tiny-wp-modules' ),
				'always_visible' => true
			),
			'elementor' => array(
				'label' => __( 'Elementor', 'tiny-wp-modules' ),
				'icon' => self::get_elementor_icon(),
				'icon_type' => 'inline_svg',
				'description' => __( 'Manage Elementor widgets and functionality', 'tiny-wp-modules' ),
				'always_visible' => false,
				'requires_setting' => 'enable_elementor'
			)
		);

		// Filter tabs based on settings
		$filtered_tabs = array();
		foreach ( $tabs as $tab_key => $tab_data ) {
			if ( $tab_data['always_visible'] || 
				( isset( $tab_data['requires_setting'] ) && 
				  isset( $settings[ $tab_data['requires_setting'] ] ) && 
				  $settings[ $tab_data['requires_setting'] ] ) ) {
				$filtered_tabs[ $tab_key ] = $tab_data;
			}
		}

		return $filtered_tabs;
	}

	/**
	 * Render tab navigation
	 *
	 * @param array $tabs Array of tabs.
	 * @param string $current_tab Current active tab.
	 * @return string HTML output for tab navigation.
	 */
	public static function render_navigation( $tabs, $current_tab ) {
		ob_start();
		?>
		<div class="tiny-wp-modules-nav-menu">
			<?php foreach ( $tabs as $tab_key => $tab_data ) : ?>
				<a href="?page=tiny-wp-modules-settings&tab=<?php echo esc_attr( $tab_key ); ?>" 
				   class="nav-menu-item <?php echo $current_tab === $tab_key ? 'active' : ''; ?>"
				   data-tab="<?php echo esc_attr( $tab_key ); ?>">
					<?php if ( isset( $tab_data['icon_type'] ) && $tab_data['icon_type'] === 'inline_svg' ) : ?>
						<div class="nav-menu-icon">
							<?php echo $tab_data['icon']; ?>
						</div>
					<?php else : ?>
						<span class="dashicons dashicons-<?php echo esc_attr( $tab_data['icon'] ); ?>"></span>
					<?php endif; ?>
					<span class="nav-menu-label"><?php echo esc_html( $tab_data['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render tab header
	 *
	 * @param array $tabs Array of tabs.
	 * @param string $current_tab Current active tab.
	 * @return string HTML output for tab header.
	 */
	public static function render_header( $tabs, $current_tab ) {
		if ( ! isset( $tabs[ $current_tab ] ) ) {
			return '';
		}

		$tab_data = $tabs[ $current_tab ];
		ob_start();
		?>
		<div class="content-header">
			<div class="header-content">
				<div class="header-icon">
					<?php if ( isset( $tab_data['icon_type'] ) && $tab_data['icon_type'] === 'inline_svg' ) : ?>
						<div class="header-icon-img">
							<?php echo $tab_data['icon']; ?>
						</div>
					<?php else : ?>
						<span class="dashicons dashicons-<?php echo esc_attr( $tab_data['icon'] ); ?>"></span>
					<?php endif; ?>
				</div>
				<div class="header-text">
					<h2><?php echo esc_html( $tab_data['label'] ); ?></h2>
					<p><?php echo esc_html( $tab_data['description'] ); ?></p>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get tab content template path
	 *
	 * @param string $tab_key Tab key.
	 * @return string Template path.
	 */
	public static function get_tab_template_path( $tab_key ) {
		$template_path = TINY_WP_MODULES_PLUGIN_DIR . 'templates/admin/tabs/' . $tab_key . '.php';
		
		// Check if tab-specific template exists
		if ( file_exists( $template_path ) ) {
			return $template_path;
		}
		
		// Return empty string if template doesn't exist
		return '';
	}

	/**
	 * Check if tab should be visible
	 *
	 * @param string $tab_key Tab key.
	 * @param array $settings Current plugin settings.
	 * @return bool True if tab should be visible.
	 */
	public static function is_tab_visible( $tab_key, $settings = array() ) {
		$tabs = self::get_tabs( $settings );
		return isset( $tabs[ $tab_key ] );
	}

	/**
	 * Get General tab icon SVG
	 *
	 * @return string SVG icon HTML.
	 */
	private static function get_general_icon() {
		return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="tab-icon-svg">
			<path opacity="0.4" d="M17 15V17M17.009 19H17M22 17C22 19.7614 19.7614 22 17 22C14.2386 22 12 19.7614 12 17C12 14.2386 14.2386 12 17 12C19.7614 12 22 14.2386 22 17Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			<path d="M20.2282 10.054C20.7082 9.51405 21.5362 8.69805 21.492 8.36573C21.526 8.04251 21.351 7.73888 21.0011 7.13163L20.5076 6.27503C20.1343 5.6272 19.9476 5.30328 19.63 5.17412C19.3125 5.04495 18.9533 5.14688 18.235 5.35072L17.0147 5.69442C16.5561 5.80017 16.075 5.74018 15.6562 5.52503L15.3193 5.33066C14.9603 5.10067 14.6841 4.76157 14.5312 4.36298L14.1972 3.36559C13.9777 2.70558 13.8679 2.37557 13.6065 2.18681C13.3451 1.99805 12.9979 1.99805 12.3036 1.99805H11.1888C10.4944 1.99805 10.1472 1.99805 9.88588 2.18681C9.62448 2.37557 9.51468 2.70558 9.29508 3.36559L8.96118 4.36298C8.80828 4.76157 8.53208 5.10067 8.17298 5.33066L7.83608 5.52503C7.41738 5.74018 6.93618 5.80017 6.47758 5.69442L5.25739 5.35072C4.53909 5.14688 4.17989 5.04495 3.86229 5.17412C3.54469 5.30328 3.35809 5.6272 2.98479 6.27503L2.49119 7.13163C2.1413 7.73888 1.9664 8.04251 2.0003 8.36573C2.0343 8.68895 2.2685 8.94942 2.73689 9.47036L3.76799 10.623C4.01989 10.942 4.19889 11.498 4.19889 11.9978C4.19889 12.498 4.01999 13.0538 3.76799 13.3729L2.73689 14.5256C2.2685 15.0465 2.0343 15.307 2.0003 15.6302C1.9664 15.9535 2.1413 16.2571 2.49119 16.8643L2.98479 17.7209C3.35809 18.3687 3.54469 18.6927 3.86229 18.8218C4.17989 18.951 4.53909 18.8491 5.25739 18.6452L6.47758 18.3015C6.93628 18.1957 7.41748 18.2558 7.83628 18.471L8.17308 18.6654C8.53218 18.8954 8.80828 19.2344 8.96108 19.633L9.29508 20.6305C9.51468 21.2905 9.63608 21.6336 9.83615 21.778C9.89615 21.8214 10.1362 22.018 10.7242 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			<path d="M14.1561 9.44605C13.3161 8.76205 12.6561 8.49805 11.6961 8.49805C9.89601 8.52205 8.25201 10.009 8.25201 11.942C8.25201 13.0077 8.57601 13.682 9.18801 14.39" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
		</svg>';
	}



	/**
	 * Get Advanced tab icon SVG
	 *
	 * @return string SVG icon HTML.
	 */
	private static function get_advanced_icon() {
		return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="tab-icon-svg">
			<path d="M20.3584 13.3567C19.1689 14.546 16.9308 14.4998 13.4992 14.4998C11.2914 14.4998 9.50138 12.7071 9.50024 10.4993C9.50024 7.07001 9.454 4.83065 10.6435 3.64138C11.8329 2.45212 12.3583 2.50027 17.6274 2.50027C18.1366 2.49809 18.3929 3.11389 18.0329 3.47394L15.3199 6.18714C14.6313 6.87582 14.6294 7.99233 15.3181 8.68092C16.0068 9.36952 17.1234 9.36959 17.8122 8.68109L20.5259 5.96855C20.886 5.60859 21.5019 5.86483 21.4997 6.37395C21.4997 11.6422 21.5479 12.1675 20.3584 13.3567Z" stroke="currentColor" stroke-width="1.5"/>
			<path opacity="0.4" d="M13.5 14.5L7.32842 20.6716C6.22386 21.7761 4.433 21.7761 3.32843 20.6716C2.22386 19.567 2.22386 17.7761 3.32843 16.6716L9.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			<path opacity="0.4" d="M5.50896 18.5H5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>';
	}

	/**
	 * Get Elementor tab icon SVG
	 *
	 * @return string SVG icon HTML.
	 */
	private static function get_elementor_icon() {
		return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="tab-icon-svg">
			<path d="M11.5 6C7.02166 6 4.78249 6 3.39124 7.17157C2 8.34315 2 10.2288 2 14C2 17.7712 2 19.6569 3.39124 20.8284C4.78249 22 7.02166 22 11.5 22C15.9783 22 18.2175 22 19.6088 20.8284C21 19.6569 21 17.7712 21 14C21 12.8302 21 11.8419 20.9585 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			<path opacity="0.4" d="M18.5 2L18.7579 2.69703C19.0961 3.61102 19.2652 4.06802 19.5986 4.40139C19.932 4.73477 20.389 4.90387 21.303 5.24208L22 5.5L21.303 5.75792C20.389 6.09613 19.932 6.26524 19.5986 6.59861C19.2652 6.93198 19.0961 7.38898 18.7579 8.30297L18.5 9L18.2421 8.30297C17.9039 7.38898 17.7348 6.93198 17.4014 6.59861C17.068 6.26524 16.611 6.09613 15.697 5.75792L15 5.5L15.697 5.24208C16.611 4.90387 17.068 4.73477 17.4014 4.40139C17.7348 4.06802 17.9039 3.61102 18.2421 2.69703L18.5 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
			<path d="M15.5 12L16.7265 13.0572C17.2422 13.5016 17.5 13.7239 17.5 14C17.5 14.2761 17.2422 14.4984 16.7265 14.9428L15.5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M7.5 12L6.27346 13.0572C5.75782 13.5016 5.5 13.7239 5.5 14C5.5 14.2761 5.75782 14.4984 6.27346 14.9428L7.5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M12.5 11L10.5 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>';
	}
}
