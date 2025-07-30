<?php
/**
 * FAQ Module Class
 *
 * @package TinyWpModules\Modules
 */

namespace TinyWpModules\Modules;

use TinyWpModules\Core\Post_Type_Manager;
use TinyWpModules\Core\Taxonomy_Manager;

/**
 * FAQ Module functionality
 */
class FAQ_Module {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_faq_post_type' ) );
		add_action( 'init', array( $this, 'register_faq_taxonomy' ) );
		add_action( 'init', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'admin_head', array( $this, 'add_faq_categories_button' ) );
	}

	/**
	 * Register FAQ custom post type
	 */
	public function register_faq_post_type() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Check if FAQ is enabled
		if ( ! isset( $settings['enable_faq'] ) || ! $settings['enable_faq'] ) {
			return;
		}

		$label = isset( $settings['faq_label'] ) ? sanitize_text_field( $settings['faq_label'] ) : 'FAQ';
		$slug = isset( $settings['faq_slug'] ) ? sanitize_title( $settings['faq_slug'] ) : 'faq';

		$post_type_settings = array(
			'label' => $label,
			'slug' => $slug,
			'args' => array(
				'menu_icon' => 'dashicons-format-chat',
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			)
		);

		// Use reusable Post Type Manager
		Post_Type_Manager::register_post_type( 'faq', $post_type_settings, $settings['enable_faq'] );
	}

	/**
	 * Register FAQ categories taxonomy
	 */
	public function register_faq_taxonomy() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Check if FAQ is enabled
		if ( ! isset( $settings['enable_faq'] ) || ! $settings['enable_faq'] ) {
			return;
		}

		$label = isset( $settings['faq_label'] ) ? sanitize_text_field( $settings['faq_label'] ) : 'FAQ';
		$slug = isset( $settings['faq_slug'] ) ? sanitize_title( $settings['faq_slug'] ) : 'faq';
		$taxonomy_slug = $slug . '_categories';

		$taxonomy_settings = array(
			'label' => $label,
			'slug' => $taxonomy_slug,
			'thumbnail' => true, // Enable thumbnail support
		);

		// Use reusable Taxonomy Manager
		Taxonomy_Manager::register_taxonomy( $taxonomy_slug, array( $slug ), $taxonomy_settings, $settings['enable_faq'] );
	}



	/**
	 * Add FAQ categories button to admin page header
	 */
	public function add_faq_categories_button() {
		global $pagenow, $post_type;
		
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Check if FAQ is enabled
		if ( ! isset( $settings['enable_faq'] ) || ! $settings['enable_faq'] ) {
			return;
		}

		$slug = isset( $settings['faq_slug'] ) ? sanitize_title( $settings['faq_slug'] ) : 'faq';
		$label = isset( $settings['faq_label'] ) ? sanitize_text_field( $settings['faq_label'] ) : 'FAQ';
		$taxonomy_slug = $slug . '_categories';

		// Only show on FAQ post type pages
		if ( $pagenow === 'edit.php' && $post_type === $slug ) {
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Add categories button to the page title area
				$('.wp-heading-inline').after(
					'<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=' . $taxonomy_slug . '&post_type=' . $slug ) ); ?>" class="page-title-action"><?php echo esc_html( sprintf( __( 'Categories', 'tiny-wp-modules' )) ); ?></a>'
				);
			});
			</script>
			<?php
		}
	}

	/**
	 * Flush rewrite rules when FAQ settings are saved
	 */
	public function flush_rewrite_rules() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Check if FAQ is enabled and if we need to flush rewrite rules
		if ( isset( $settings['enable_faq'] ) && $settings['enable_faq'] ) {
			$flushed = get_option( 'tiny_wp_modules_faq_rewrite_flushed', false );
			
			if ( ! $flushed ) {
				flush_rewrite_rules();
				update_option( 'tiny_wp_modules_faq_rewrite_flushed', true );
			}
		}
	}
} 