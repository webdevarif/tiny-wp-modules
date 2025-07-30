<?php
/**
 * Taxonomy Manager Class
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Reusable Taxonomy Manager
 */
class Taxonomy_Manager {

	/**
	 * Register a taxonomy with dynamic settings
	 *
	 * @param string $taxonomy_slug The taxonomy slug
	 * @param array  $post_types    Array of post type slugs
	 * @param array  $settings      Taxonomy settings
	 * @param bool   $enabled       Whether the taxonomy is enabled
	 */
	public static function register_taxonomy( $taxonomy_slug, $post_types = array(), $settings = array(), $enabled = true ) {
		if ( ! $enabled ) {
			return;
		}

		$label = isset( $settings['label'] ) ? sanitize_text_field( $settings['label'] ) : ucfirst( $taxonomy_slug );
		$slug = isset( $settings['slug'] ) ? sanitize_title( $settings['slug'] ) : $taxonomy_slug;

		$labels = array(
			'name'                       => sprintf( __( '%s Categories', 'tiny-wp-modules' ), $label ),
			'singular_name'              => sprintf( __( '%s Category', 'tiny-wp-modules' ), $label ),
			'menu_name'                  => sprintf( __( '%s Categories', 'tiny-wp-modules' ), $label ),
			'all_items'                  => sprintf( __( 'All %s Categories', 'tiny-wp-modules' ), $label ),
			'parent_item'                => sprintf( __( 'Parent %s Category', 'tiny-wp-modules' ), $label ),
			'parent_item_colon'          => sprintf( __( 'Parent %s Category:', 'tiny-wp-modules' ), $label ),
			'new_item_name'              => sprintf( __( 'New %s Category Name', 'tiny-wp-modules' ), $label ),
			'add_new_item'               => sprintf( __( 'Add New %s Category', 'tiny-wp-modules' ), $label ),
			'edit_item'                  => sprintf( __( 'Edit %s Category', 'tiny-wp-modules' ), $label ),
			'update_item'                => sprintf( __( 'Update %s Category', 'tiny-wp-modules' ), $label ),
			'view_item'                  => sprintf( __( 'View %s Category', 'tiny-wp-modules' ), $label ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s categories with commas', 'tiny-wp-modules' ), strtolower( $label ) ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s categories', 'tiny-wp-modules' ), strtolower( $label ) ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s categories', 'tiny-wp-modules' ), strtolower( $label ) ),
			'popular_items'              => sprintf( __( 'Popular %s Categories', 'tiny-wp-modules' ), $label ),
			'search_items'               => sprintf( __( 'Search %s Categories', 'tiny-wp-modules' ), $label ),
			'not_found'                  => sprintf( __( 'No %s categories found', 'tiny-wp-modules' ), strtolower( $label ) ),
			'no_terms'                   => sprintf( __( 'No %s categories', 'tiny-wp-modules' ), strtolower( $label ) ),
			'items_list_navigation'      => sprintf( __( '%s categories list navigation', 'tiny-wp-modules' ), $label ),
			'items_list'                 => sprintf( __( '%s categories list', 'tiny-wp-modules' ), $label ),
		);

		$default_args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $slug ),
			'capabilities'      => array(
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_posts',
			),
		);

		$args = wp_parse_args( $settings['args'] ?? array(), $default_args );

		register_taxonomy( $slug, $post_types, $args );

		// Add thumbnail support if enabled
		if ( isset( $settings['thumbnail'] ) && $settings['thumbnail'] ) {
			self::add_thumbnail_support( $slug, $settings );
		}
	}

	/**
	 * Add thumbnail support to taxonomy
	 *
	 * @param string $taxonomy_slug The taxonomy slug
	 * @param array  $settings      Taxonomy settings
	 */
	private static function add_thumbnail_support( $taxonomy_slug, $settings ) {
		// Add hooks for thumbnail functionality
		add_action( 'admin_enqueue_scripts', function( $hook ) use ( $taxonomy_slug ) {
			if ( in_array( $hook, array( 'edit-tags.php', 'term.php' ) ) && isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] === $taxonomy_slug ) {
				wp_enqueue_media();
			}
		});

		add_action( 'created_' . $taxonomy_slug, array( __CLASS__, 'save_taxonomy_thumbnail' ), 10, 2 );
		add_action( 'edited_' . $taxonomy_slug, array( __CLASS__, 'save_taxonomy_thumbnail' ), 10, 2 );
		add_action( $taxonomy_slug . '_add_form_fields', array( __CLASS__, 'add_taxonomy_thumbnail_field' ) );
		add_action( $taxonomy_slug . '_edit_form_fields', array( __CLASS__, 'edit_taxonomy_thumbnail_field' ), 10, 2 );
	}

	/**
	 * Add thumbnail field to taxonomy add form
	 */
	public static function add_taxonomy_thumbnail_field() {
		// Use the reusable Media Field component
		\TinyWpModules\Components\Media_Field::render( array(
			'id' => 'taxonomy_thumbnail_id',
			'name' => 'taxonomy_thumbnail_id',
			'value' => '',
			'label' => __( 'Category Thumbnail', 'tiny-wp-modules' ),
			'description' => __( 'Upload an image for this category.', 'tiny-wp-modules' ),
			'button_text' => __( 'Upload Image', 'tiny-wp-modules' ),
			'remove_text' => __( 'Remove Image', 'tiny-wp-modules' ),
			'preview_size' => 'thumbnail',
			'multiple' => false,
			'class' => 'taxonomy-thumbnail-field'
		) );
	}

	/**
	 * Add thumbnail field to taxonomy edit form
	 */
	public static function edit_taxonomy_thumbnail_field( $term, $taxonomy ) {
		$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		
		// Use the reusable Media Field component
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="taxonomy_thumbnail"><?php _e( 'Category Thumbnail', 'tiny-wp-modules' ); ?></label>
			</th>
			<td>
				<?php
				\TinyWpModules\Components\Media_Field::render( array(
					'id' => 'taxonomy_thumbnail_id',
					'name' => 'taxonomy_thumbnail_id',
					'value' => $thumbnail_id,
					'label' => '', // No label needed in table layout
					'description' => __( 'Upload an image for this category.', 'tiny-wp-modules' ),
					'button_text' => __( 'Upload Image', 'tiny-wp-modules' ),
					'remove_text' => __( 'Remove Image', 'tiny-wp-modules' ),
					'preview_size' => 'thumbnail',
					'multiple' => false,
					'class' => 'taxonomy-thumbnail-field'
				) );
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save taxonomy thumbnail
	 */
	public static function save_taxonomy_thumbnail( $term_id, $tt_id ) {
		if ( isset( $_POST['taxonomy_thumbnail_id'] ) ) {
			$thumbnail_id = intval( $_POST['taxonomy_thumbnail_id'] );
			update_term_meta( $term_id, 'thumbnail_id', $thumbnail_id );
		}
	}
} 