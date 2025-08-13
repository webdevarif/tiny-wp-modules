<?php
/**
 * Base Widget Class for Elementor
 *
 * @package TinyWpModules\Modules\Elementor
 */

namespace TinyWpModules\Modules\Elementor;

/**
 * Base class for all Elementor widgets
 */
abstract class Base_Widget {

	/**
	 * Widget ID
	 *
	 * @var string
	 */
	protected $widget_id;

	/**
	 * Widget title
	 *
	 * @var string
	 */
	protected $widget_title;

	/**
	 * Widget icon
	 *
	 * @var string
	 */
	protected $widget_icon;

	/**
	 * Widget categories
	 *
	 * @var array
	 */
	protected $widget_categories;

	/**
	 * Widget keywords
	 *
	 * @var array
	 */
	protected $widget_keywords;

	/**
	 * Constructor
	 *
	 * @param string $widget_id Widget ID.
	 * @param string $widget_title Widget title.
	 * @param string $widget_icon Widget icon.
	 * @param array  $widget_categories Widget categories.
	 * @param array  $widget_keywords Widget keywords.
	 */
	public function __construct( $widget_id, $widget_title, $widget_icon = 'eicon-elementor', $widget_categories = array( 'general' ), $widget_keywords = array() ) {
		$this->widget_id = $widget_id;
		$this->widget_title = $widget_title;
		$this->widget_icon = $widget_icon;
		$this->widget_categories = $widget_categories;
		$this->widget_keywords = $widget_keywords;

		$this->init();
	}

	/**
	 * Initialize the widget
	 */
	protected function init() {
		// Check if Elementor is active
		if ( ! $this->is_elementor_active() ) {
			return;
		}

		// Register the widget
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
	}

	/**
	 * Check if Elementor is active
	 *
	 * @return bool True if Elementor is active.
	 */
	protected function is_elementor_active() {
		return defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::$instance->editor;
	}

	/**
	 * Check if widget is enabled in settings
	 *
	 * @return bool True if enabled.
	 */
	protected function is_enabled() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings[ $this->widget_id ] ) && $settings[ $this->widget_id ];
	}

	/**
	 * Register the widget with Elementor
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widget( $widgets_manager ) {
		if ( $this->is_enabled() ) {
			$widgets_manager->register( new $this->get_widget_class() );
		}
	}

	/**
	 * Get the widget class name
	 *
	 * @return string Widget class name.
	 */
	abstract protected function get_widget_class();

	/**
	 * Get widget ID
	 *
	 * @return string Widget ID.
	 */
	public function get_widget_id() {
		return $this->widget_id;
	}

	/**
	 * Get widget title
	 *
	 * @return string Widget title.
	 */
	public function get_widget_title() {
		return $this->widget_title;
	}

	/**
	 * Get widget icon
	 *
	 * @return string Widget icon.
	 */
	public function get_widget_icon() {
		return $this->widget_icon;
	}

	/**
	 * Get widget categories
	 *
	 * @return array Widget categories.
	 */
	public function get_widget_categories() {
		return $this->widget_categories;
	}

	/**
	 * Get widget keywords
	 *
	 * @return array Widget keywords.
	 */
	public function get_widget_keywords() {
		return $this->widget_keywords;
	}
}
