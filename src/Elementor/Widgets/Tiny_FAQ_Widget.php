<?php
/**
 * Tiny FAQ Elementor Widget
 *
 * @package TinyWpModules\Elementor\Widgets
 */

namespace TinyWpModules\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tiny FAQ Widget Class
 */
class Tiny_FAQ_Widget extends Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'tiny-faq';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Tiny FAQ', 'tiny-wp-modules' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-help-o';
	}

	/**
	 * Get widget categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'tiny-wp-modules' );
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'faq', 'accordion', 'questions', 'answers', 'tiny' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'tiny-wp-modules' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'tiny-wp-modules' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Frequently Asked Questions', 'tiny-wp-modules' ),
				'placeholder' => __( 'Enter your title', 'tiny-wp-modules' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'tiny-wp-modules' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Find answers to common questions about our services.', 'tiny-wp-modules' ),
				'placeholder' => __( 'Enter your description', 'tiny-wp-modules' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'       => __( 'Number of FAQs per Category', 'tiny-wp-modules' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 10,
				'min'         => 1,
				'max'         => 50,
				'step'        => 1,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => __( 'Order By', 'tiny-wp-modules' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'     => __( 'Date', 'tiny-wp-modules' ),
					'title'    => __( 'Title', 'tiny-wp-modules' ),
					'menu_order' => __( 'Menu Order', 'tiny-wp-modules' ),
					'rand'     => __( 'Random', 'tiny-wp-modules' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'tiny-wp-modules' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'ASC'  => __( 'Ascending', 'tiny-wp-modules' ),
					'DESC' => __( 'Descending', 'tiny-wp-modules' ),
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Title
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'tiny-wp-modules' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .tiny-faq-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'tiny-wp-modules' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tiny-faq-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'tiny-wp-modules' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tiny-faq-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - FAQ Items
		$this->start_controls_section(
			'section_faq_style',
			array(
				'label' => __( 'FAQ Items', 'tiny-wp-modules' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'faq_border',
				'selector' => '{{WRAPPER}} .tiny-faq-item',
			)
		);

		$this->add_control(
			'faq_border_radius',
			array(
				'label'      => __( 'Border Radius', 'tiny-wp-modules' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .tiny-faq-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'faq_box_shadow',
				'selector' => '{{WRAPPER}} .tiny-faq-item',
			)
		);

		$this->end_controls_section();

		// Style Section - Questions
		$this->start_controls_section(
			'section_question_style',
			array(
				'label' => __( 'Questions', 'tiny-wp-modules' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'question_typography',
				'selector' => '{{WRAPPER}} .tiny-faq-question',
			)
		);

		$this->add_control(
			'question_color',
			array(
				'label'     => __( 'Color', 'tiny-wp-modules' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tiny-faq-question' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'question_background',
			array(
				'label'     => __( 'Background Color', 'tiny-wp-modules' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tiny-faq-question' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Answers
		$this->start_controls_section(
			'section_answer_style',
			array(
				'label' => __( 'Answers', 'tiny-wp-modules' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'answer_typography',
				'selector' => '{{WRAPPER}} .tiny-faq-answer',
			)
		);

		$this->add_control(
			'answer_color',
			array(
				'label'     => __( 'Color', 'tiny-wp-modules' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tiny-faq-answer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'answer_background',
			array(
				'label'     => __( 'Background Color', 'tiny-wp-modules' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tiny-faq-answer' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
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

	/**
	 * Get FAQ categories with thumbnails
	 *
	 * @return array
	 */
	private function get_faq_categories_data() {
		$faq_post_type = $this->get_faq_post_type();
		$categories = get_terms( array(
			'taxonomy'   => $faq_post_type . '_category',
			'hide_empty' => true,
		) );

		if ( is_wp_error( $categories ) ) {
			return array();
		}

		$categories_data = array();
		foreach ( $categories as $category ) {
			$category->thumbnail = $this->get_category_thumbnail( $category );
			$categories_data[] = $category;
		}

		return $categories_data;
	}

	/**
	 * Get FAQ posts
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function get_faq_posts( $settings ) {
		$faq_post_type = $this->get_faq_post_type();
		
		$args = array(
			'post_type'      => $faq_post_type,
			'post_status'    => 'publish',
			'posts_per_page' => isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 10,
			'orderby'        => isset( $settings['order_by'] ) ? $settings['order_by'] : 'date',
			'order'          => isset( $settings['order'] ) ? $settings['order'] : 'DESC',
		);

		// Add category filter
		if ( ! empty( $settings['category_filter'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $faq_post_type . '_category',
					'field'    => 'term_id',
					'terms'    => $settings['category_filter'],
				),
			);
		}

		return get_posts( $args );
	}

	/**
	 * Get category thumbnail
	 *
	 * @param object $category Category object.
	 * @return string
	 */
	private function get_category_thumbnail( $category ) {
		$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
			return $image ? $image[0] : '';
		}
		return '';
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$faq_categories = $this->get_faq_categories_data();

		$this->add_render_attribute( 'wrapper', 'class', 'tiny-faq-widget' );
		?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h2 class="tiny-faq-title"><?php echo esc_html( $settings['title'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="tiny-faq-description"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $faq_categories ) ) : ?>
				<!-- Categories Grid -->
				<div class="tiny-faq-categories">
					<div class="categories-grid">
						<?php foreach ( $faq_categories as $category ) : ?>
							<div class="category-item" data-category-id="<?php echo esc_attr( $category->term_id ); ?>">
								<div class="category-thumbnail">
									<?php if ( ! empty( $category->thumbnail ) ) : ?>
										<img src="<?php echo esc_url( $category->thumbnail ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" />
									<?php else : ?>
										<div class="category-placeholder">
											<span class="placeholder-icon">?</span>
										</div>
									<?php endif; ?>
								</div>
								<div class="category-info">
									<h3 class="category-name"><?php echo esc_html( $category->name ); ?></h3>
									<span class="category-count"><?php echo esc_html( $category->count ); ?> <?php echo esc_html( _n( 'FAQ', 'FAQs', $category->count, 'tiny-wp-modules' ) ); ?></span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- FAQ List Container -->
				<div class="tiny-faq-list-container">
					<div class="faq-list-header">
						<h3 class="selected-category-title"><?php esc_html_e( 'Select a category above', 'tiny-wp-modules' ); ?></h3>
					</div>
					<div class="tiny-faq-list" id="tiny-faq-list">
						<div class="tiny-faq-empty">
							<p><?php esc_html_e( 'Please select a category to view FAQs.', 'tiny-wp-modules' ); ?></p>
						</div>
					</div>
				</div>
			<?php else : ?>
				<div class="tiny-faq-empty">
					<p><?php esc_html_e( 'No FAQ categories found.', 'tiny-wp-modules' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<?php
	}
} 