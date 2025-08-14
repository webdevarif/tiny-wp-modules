<?php
/**
 * Cart Icon Widget
 *
 * @package TinyWpModules\Modules\Elementor\Widgets
 */

namespace TinyWpModules\Modules\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Cart Icon Widget
 */
class Cart_Icon_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cart_icon_widget';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Cart Icon', 'tiny-wp-modules' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-cart';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'tiny-wp-modules' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'tiny-wp-modules' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'tiny-wp-modules' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 16,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .cart-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce is not active.', 'tiny-wp-modules' ) . '</p>';
			return;
		}

		$cart_count = WC()->cart->get_cart_contents_count();
		?>
		<div class="cart-icon-widget">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-icon">
				<span class="cart-icon-symbol">🛒</span>
				<?php if ( $cart_count > 0 ) : ?>
					<span class="cart-count"><?php echo esc_html( $cart_count ); ?></span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<div class="cart-icon-widget">
			<a href="#" class="cart-icon">
				<span class="cart-icon-symbol">🛒</span>
				<span class="cart-count">0</span>
			</a>
		</div>
		<?php
	}
}
