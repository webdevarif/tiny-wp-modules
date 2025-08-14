<?php
/**
 * User Info Tag
 *
 * @package TinyWpModules\Modules\Elementor\Tags
 */

namespace TinyWpModules\Modules\Elementor\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * User Info Tag
 */
class User_Info_Tag extends Tag {

	/**
	 * Get dynamic tag name.
	 *
	 * @return string Dynamic tag name.
	 */
	public function get_name() {
		return 'user_info_tag';
	}

	/**
	 * Get dynamic tag title.
	 *
	 * @return string Dynamic tag title.
	 */
	public function get_title() {
		return esc_html__( 'User Info', 'tiny-wp-modules' );
	}

	/**
	 * Get dynamic tag group.
	 *
	 * @return string Dynamic tag group.
	 */
	public function get_group() {
		return 'tiny-wp-modules';
	}

	/**
	 * Get dynamic tag categories.
	 *
	 * @return array Dynamic tag categories.
	 */
	public function get_categories() {
		return [ Module::TEXT_CATEGORY ];
	}

	/**
	 * Register dynamic tag controls.
	 */
	protected function register_controls() {
		$this->add_control(
			'user_info_type',
			[
				'label' => esc_html__( 'User Info Type', 'tiny-wp-modules' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'display_name',
				'options' => [
					'display_name' => esc_html__( 'Display Name', 'tiny-wp-modules' ),
					'user_email' => esc_html__( 'Email', 'tiny-wp-modules' ),
					'user_login' => esc_html__( 'Username', 'tiny-wp-modules' ),
					'first_name' => esc_html__( 'First Name', 'tiny-wp-modules' ),
					'last_name' => esc_html__( 'Last Name', 'tiny-wp-modules' ),
				],
			]
		);
	}

	/**
	 * Render tag output on the frontend.
	 */
	public function render() {
		$user_info_type = $this->get_settings( 'user_info_type' );
		
		if ( ! is_user_logged_in() ) {
			echo esc_html__( 'Not logged in', 'tiny-wp-modules' );
			return;
		}

		$current_user = wp_get_current_user();
		
		switch ( $user_info_type ) {
			case 'display_name':
				echo esc_html( $current_user->display_name );
				break;
			case 'user_email':
				echo esc_html( $current_user->user_email );
				break;
			case 'user_login':
				echo esc_html( $current_user->user_login );
				break;
			case 'first_name':
				echo esc_html( $current_user->first_name );
				break;
			case 'last_name':
				echo esc_html( $current_user->last_name );
				break;
			default:
				echo esc_html( $current_user->display_name );
		}
	}
}
