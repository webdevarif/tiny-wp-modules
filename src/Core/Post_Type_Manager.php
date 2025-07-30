<?php
/**
 * Post Type Manager Class
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Reusable Post Type Manager
 */
class Post_Type_Manager {

	/**
	 * Register a custom post type with dynamic settings
	 *
	 * @param string $post_type_slug The post type slug
	 * @param array  $settings       Post type settings
	 * @param bool   $enabled        Whether the post type is enabled
	 */
	public static function register_post_type( $post_type_slug, $settings = array(), $enabled = true ) {
		if ( ! $enabled ) {
			return;
		}

		$label = isset( $settings['label'] ) ? sanitize_text_field( $settings['label'] ) : ucfirst( $post_type_slug );
		$slug = isset( $settings['slug'] ) ? sanitize_title( $settings['slug'] ) : $post_type_slug;

		$labels = array(
			'name'                  => $label,
			'singular_name'         => $label,
			'menu_name'             => $label,
			'name_admin_bar'        => $label,
			'add_new'               => __( 'Add New', 'tiny-wp-modules' ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'tiny-wp-modules' ), $label ),
			'new_item'              => sprintf( __( 'New %s', 'tiny-wp-modules' ), $label ),
			'edit_item'             => sprintf( __( 'Edit %s', 'tiny-wp-modules' ), $label ),
			'view_item'             => sprintf( __( 'View %s', 'tiny-wp-modules' ), $label ),
			'all_items'             => sprintf( __( 'All %s', 'tiny-wp-modules' ), $label ),
			'search_items'          => sprintf( __( 'Search %s', 'tiny-wp-modules' ), $label ),
			'parent_item_colon'     => sprintf( __( 'Parent %s:', 'tiny-wp-modules' ), $label ),
			'not_found'             => sprintf( __( 'No %s found.', 'tiny-wp-modules' ), strtolower( $label ) ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.', 'tiny-wp-modules' ), strtolower( $label ) ),
		);

		$default_args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => $slug ),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-admin-post',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		);

		$args = wp_parse_args( $settings['args'] ?? array(), $default_args );

		register_post_type( $slug, $args );
	}
} 