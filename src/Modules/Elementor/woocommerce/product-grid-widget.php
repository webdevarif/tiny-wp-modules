<?php
/**
 * Product Grid Widget
 *
 * @package TinyWpModules\Modules\Elementor\WooCommerce
 */

namespace TinyWpModules\Modules\Elementor\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Product Grid Widget
 */
class Product_Grid_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'product_grid_widget';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Product Grid', 'tiny-wp-modules' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-products-grid';
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
			'products_per_page',
			[
				'label' => esc_html__( 'Products Per Page', 'tiny-wp-modules' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'default' => 12,
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'tiny-wp-modules' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
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

		$args = array(
			'post_type' => 'product',
			'posts_per_page' => $settings['products_per_page'],
			'post_status' => 'publish',
		);

		$products = new \WP_Query( $args );

		if ( $products->have_posts() ) {
			echo '<div class="product-grid-widget" style="display: grid; grid-template-columns: repeat(' . esc_attr( $settings['columns'] ) . ', 1fr); gap: 20px;">';
			
			while ( $products->have_posts() ) {
				$products->the_post();
				global $product;
				
				if ( $product ) {
					echo '<div class="product-item">';
					echo '<a href="' . esc_url( get_permalink() ) . '">';
					echo '<div class="product-image">' . woocommerce_get_product_thumbnail() . '</div>';
					echo '<h3 class="product-title">' . esc_html( get_the_title() ) . '</h3>';
					echo '<div class="product-price">' . $product->get_price_html() . '</div>';
					echo '</a>';
					echo '</div>';
				}
			}
			
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__( 'No products found.', 'tiny-wp-modules' ) . '</p>';
		}
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<div class="product-grid-widget" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
			<div class="product-item">
				<div class="product-image">📦</div>
				<h3 class="product-title">Sample Product</h3>
				<div class="product-price">$19.99</div>
			</div>
			<div class="product-item">
				<div class="product-image">📦</div>
				<h3 class="product-title">Sample Product</h3>
				<div class="product-price">$29.99</div>
			</div>
			<div class="product-item">
				<div class="product-image">📦</div>
				<h3 class="product-title">Sample Product</h3>
				<div class="product-price">$39.99</div>
			</div>
		</div>
		<?php
	}
}
