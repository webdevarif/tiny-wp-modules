<?php
/**
 * Elementor Widget Manager
 *
 * @package TinyWpModules\Elementor
 */

namespace TinyWpModules\Elementor;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget Manager Class
 */
class Widget_Manager {

	/**
	 * Widget Manager instance
	 *
	 * @var Widget_Manager
	 */
	private static $instance = null;

	/**
	 * Widgets array
	 *
	 * @var array
	 */
	private $widgets = array();

	/**
	 * Get singleton instance
	 *
	 * @return Widget_Manager
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_widget_categories' ) );
		add_action( 'wp_ajax_load_faqs_by_category', array( $this, 'load_faqs_by_category' ) );
		add_action( 'wp_ajax_nopriv_load_faqs_by_category', array( $this, 'load_faqs_by_category' ) );
	}

	/**
	 * Register all widgets
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager instance.
	 */
	public function register_widgets( $widgets_manager ) {
		// Check if Elementor is active
		if ( ! $this->is_elementor_active() ) {
			return;
		}

		// Register widgets
		$this->register_widget( $widgets_manager, 'Tiny_FAQ_Widget' );
	}

	/**
	 * Register a single widget
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager instance.
	 * @param string                     $widget_class    Widget class name.
	 */
	private function register_widget( $widgets_manager, $widget_class ) {
		$class_name = "TinyWpModules\\Elementor\\Widgets\\{$widget_class}";
		
		if ( class_exists( $class_name ) ) {
			$widgets_manager->register( new $class_name() );
		}
	}

	/**
	 * Add widget categories
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager instance.
	 */
	public function add_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'tiny-wp-modules',
			array(
				'title' => __( 'Tiny WP Modules', 'tiny-wp-modules' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Check if Elementor is active
	 *
	 * @return bool
	 */
	private function is_elementor_active() {
		return defined( 'ELEMENTOR_VERSION' ) && class_exists( 'Elementor\Plugin' );
	}

	/**
	 * Get widgets directory path
	 *
	 * @return string
	 */
	public function get_widgets_path() {
		return TINY_WP_MODULES_PLUGIN_DIR . 'src/Elementor/Widgets/';
	}

	/**
	 * Get widgets URL
	 *
	 * @return string
	 */
	public function get_widgets_url() {
		return TINY_WP_MODULES_PLUGIN_URL . 'src/Elementor/Widgets/';
	}

	/**
	 * Load FAQs by category via AJAX
	 */
	public function load_faqs_by_category() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'tiny_faq_ajax_nonce' ) ) {
			wp_die( 'Security check failed' );
		}

		$category_id = intval( $_POST['category_id'] );
		$faq_post_type = $this->get_faq_post_type();

		$args = array(
			'post_type'      => $faq_post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'tax_query'      => array(
				array(
					'taxonomy' => $faq_post_type . '_category',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
		);

		$faq_posts = get_posts( $args );
		$html = '';

		if ( ! empty( $faq_posts ) ) {
			$html .= '<div class="tiny-faq-container">';
			foreach ( $faq_posts as $index => $post ) {
				$html .= '<div class="tiny-faq-item" data-index="' . esc_attr( $index ) . '">';
				$html .= '<div class="tiny-faq-question" role="button" tabindex="0">';
				$html .= '<span class="question-text">' . esc_html( $post->post_title ) . '</span>';
				$html .= '<span class="toggle-icon">+</span>';
				$html .= '</div>';
				$html .= '<div class="tiny-faq-answer">';
				$html .= '<div class="answer-content">';
				$html .= wp_kses_post( $post->post_content );
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}
			$html .= '</div>';
		} else {
			$html = '<div class="tiny-faq-empty"><p>' . esc_html__( 'No FAQs found in this category.', 'tiny-wp-modules' ) . '</p></div>';
		}

		wp_send_json_success( array(
			'html' => $html,
		) );
	}

	/**
	 * Get FAQ post type slug
	 *
	 * @return string
	 */
	private function get_faq_post_type() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		return isset( $settings['faq_slug'] ) ? $settings['faq_slug'] : 'faq';
	}
} 