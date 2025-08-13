<?php
/**
 * Elementor Widgets Module
 *
 * @package TinyWpModules\Modules\Elementor
 */

namespace TinyWpModules\Modules\Elementor;

/**
 * Handles Elementor widgets functionality
 */
class Widgets_Module {

	/**
	 * Widget registry
	 *
	 * @var array
	 */
	private $widgets = array();

	/**
	 * Initialize the module
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->register_widgets();
	}

	/**
	 * Register all available widgets
	 */
	private function register_widgets() {
		$this->widgets = array(
			'hero_section_widget' => array(
				'class' => 'Hero_Section_Widget',
				'file' => 'widgets/hero-section-widget.php'
			),
			'testimonials_widget' => array(
				'class' => 'Testimonials_Widget',
				'file' => 'widgets/testimonials-widget.php'
			),
			'pricing_table_widget' => array(
				'class' => 'Pricing_Table_Widget',
				'file' => 'widgets/pricing-table-widget.php'
			),
			'team_members_widget' => array(
				'class' => 'Team_Members_Widget',
				'file' => 'widgets/team-members-widget.php'
			),
			'countdown_timer_widget' => array(
				'class' => 'Countdown_Timer_Widget',
				'file' => 'widgets/countdown-timer-widget.php'
			),
			'progress_bars_widget' => array(
				'class' => 'Progress_Bars_Widget',
				'file' => 'widgets/progress-bars-widget.php'
			)
		);

		// Allow other plugins/themes to register additional widgets
		$this->widgets = apply_filters( 'tiny_wp_modules_elementor_widgets_registry', $this->widgets );
	}

	/**
	 * Initialize module functionality
	 */
	public function init() {
		// Check if Elementor is active and widgets are enabled
		if ( ! $this->is_enabled() ) {
			return;
		}

		// Initialize enabled widgets
		$this->init_enabled_widgets();
	}

	/**
	 * Check if module is enabled
	 *
	 * @return bool True if enabled.
	 */
	private function is_enabled() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings['enable_elementor'] ) && $settings['enable_elementor'] &&
			   isset( $settings['elementor_widgets'] ) && $settings['elementor_widgets'];
	}

	/**
	 * Initialize only the enabled widgets
	 */
	private function init_enabled_widgets() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );

		foreach ( $this->widgets as $widget_id => $widget_data ) {
			if ( isset( $settings[ $widget_id ] ) && $settings[ $widget_id ] ) {
				$this->init_widget( $widget_id, $widget_data );
			}
		}
	}

	/**
	 * Initialize a specific widget
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $widget_data Widget configuration data.
	 */
	private function init_widget( $widget_id, $widget_data ) {
		// Load the widget file if it exists
		$this->load_widget_file( $widget_data['file'] );
	}

	/**
	 * Load widget file if it exists
	 *
	 * @param string $file_path Relative path to widget file.
	 */
	private function load_widget_file( $file_path ) {
		$full_path = plugin_dir_path( __FILE__ ) . $file_path;
		if ( file_exists( $full_path ) ) {
			require_once $full_path;
		}
	}

	/**
	 * Get registered widgets
	 *
	 * @return array Array of registered widgets.
	 */
	public function get_widgets() {
		return $this->widgets;
	}

	/**
	 * Check if a specific widget is enabled
	 *
	 * @param string $widget_id Widget ID.
	 * @return bool True if enabled.
	 */
	public function is_widget_enabled( $widget_id ) {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings[ $widget_id ] ) && $settings[ $widget_id ];
	}
}
