<?php
/**
 * Hero Section Widget for Elementor
 *
 * @package TinyWpModules\Modules\Elementor\Widgets
 */

namespace TinyWpModules\Modules\Elementor\Widgets;

use TinyWpModules\Modules\Elementor\Base_Widget;

/**
 * Hero Section Widget Class
 */
class Hero_Section_Widget extends Base_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'hero_section_widget',
			__( 'Hero Section', 'tiny-wp-modules' ),
			'eicon-banner',
			array( 'content', 'hero' ),
			array( 'hero', 'banner', 'section', 'header' )
		);
	}

	/**
	 * Get the widget class name
	 *
	 * @return string Widget class name.
	 */
	protected function get_widget_class() {
		return __CLASS__;
	}
}

/**
 * Elementor Widget Implementation
 */
class Hero_Section_Elementor_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tiny_wp_hero_section';
	}

	/**
	 * Get widget title
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Hero Section', 'tiny-wp-modules' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-banner';
	}

	/**
	 * Get widget categories
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'content', 'hero' );
	}

	/**
	 * Get widget keywords
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'hero', 'banner', 'section', 'header' );
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
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'tiny-wp-modules' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Welcome to Our Site', 'tiny-wp-modules' ),
				'placeholder' => __( 'Enter your title', 'tiny-wp-modules' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'tiny-wp-modules' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => __( 'This is a beautiful hero section with background image and text overlay.', 'tiny-wp-modules' ),
				'placeholder' => __( 'Enter your description', 'tiny-wp-modules' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'tiny-wp-modules' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Learn More', 'tiny-wp-modules' ),
				'placeholder' => __( 'Enter button text', 'tiny-wp-modules' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => __( 'Button Link', 'tiny-wp-modules' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'tiny-wp-modules' ),
				'default'     => array(
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				),
			)
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'tiny-wp-modules' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'tiny-wp-modules' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hero-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .hero-title',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="tiny-wp-hero-section">
			<div class="hero-content">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h1 class="hero-title"><?php echo esc_html( $settings['title'] ); ?></h1>
				<?php endif; ?>

				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="hero-description"><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $settings['button_text'] ) && ! empty( $settings['button_link']['url'] ) ) : ?>
					<a href="<?php echo esc_url( $settings['button_link']['url'] ); ?>" 
					   class="hero-button"
					   <?php echo $settings['button_link']['is_external'] ? 'target="_blank"' : ''; ?>
					   <?php echo $settings['button_link']['nofollow'] ? 'rel="nofollow"' : ''; ?>>
						<?php echo esc_html( $settings['button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<div class="tiny-wp-hero-section">
			<div class="hero-content">
				<# if ( settings.title ) { #>
					<h1 class="hero-title">{{{ settings.title }}}</h1>
				<# } #>

				<# if ( settings.description ) { #>
					<p class="hero-description">{{{ settings.description }}}</p>
				<# } #>

				<# if ( settings.button_text && settings.button_link.url ) { #>
					<a href="{{ settings.button_link.url }}" 
					   class="hero-button"
					   <# if ( settings.button_link.is_external ) { #>target="_blank"<# } #>
					   <# if ( settings.button_link.nofollow ) { #>rel="nofollow"<# } #>>
						{{{ settings.button_text }}}
					</a>
				<# } #>
			</div>
		</div>
		<?php
	}
}
