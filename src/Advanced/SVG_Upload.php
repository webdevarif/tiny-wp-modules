<?php
/**
 * SVG Upload Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * SVG Upload functionality
 */
class SVG_Upload {

	/**
	 * Constructor
	 */
	public function __construct() {
		$settings = get_option( 'tiny_wp_modules_settings', array() );
		
		// Initialize global variable to empty array by default
		global $roles_svg_upload_enabled;
		$GLOBALS['roles_svg_upload_enabled'] = array();
		
		// Check if SVG upload is enabled
		if ( ! isset( $settings['enable_svg_upload'] ) || ! $settings['enable_svg_upload'] ) {
			return;
		}

		// Get SVG upload roles from settings
		$svg_upload_roles = isset( $settings['svg_upload_roles'] ) ? $settings['svg_upload_roles'] : array();
		
		// Build array of enabled roles
		$roles_svg_upload_enabled = array();
		if ( is_array( $svg_upload_roles ) ) {
			foreach ( $svg_upload_roles as $role_slug => $enabled ) {
				if ( $enabled === '1' || $enabled === true || $enabled === 1 ) {
					$roles_svg_upload_enabled[] = $role_slug;
				}
			}
		}
		
		// Store in global variable for compatibility
		$GLOBALS['roles_svg_upload_enabled'] = $roles_svg_upload_enabled;

		// Add hooks for SVG upload functionality (matching working plugin)
		add_filter( 'upload_mimes', array( $this, 'add_svg_mime' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'confirm_file_type_is_svg' ), 10, 4 );
		add_filter( 'wp_handle_sideload_prefilter', array( $this, 'sanitize_and_maybe_allow_svg_upload' ) );
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_and_maybe_allow_svg_upload' ) );
		add_action( 'rest_insert_attachment', array( $this, 'sanitize_after_upload' ), 10, 3 );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_svg_metadata' ), 10, 3 );
		add_filter( 'wp_calculate_image_srcset', array( $this, 'disable_svg_srcset' ) );
		add_filter( 'wp_get_attachment_image_sizes', array( $this, 'remove_svg_responsive_image_attr' ), 10, 3 );
		add_action( 'wp_ajax_svg_get_attachment_url', array( $this, 'get_svg_attachment_url' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'get_svg_url_in_media_library' ) );
	}

	/**
	 * Add SVG mime type for media library uploads
	 *
	 * @link https://developer.wordpress.org/reference/hooks/upload_mimes/
	 * @since 2.6.0
	 */
	public function add_svg_mime( $mimes ) {
		global $roles_svg_upload_enabled;

		// Check if global variable is set and is an array
		if ( ! isset( $roles_svg_upload_enabled ) || ! is_array( $roles_svg_upload_enabled ) ) {
			return $mimes;
		}

		$current_user = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

		if ( count( $roles_svg_upload_enabled ) > 0 ) {

			// Add mime type for user roles set to enable SVG upload
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_svg_upload_enabled ) ) {
					$mimes['svg'] = 'image/svg+xml';
				}
			}   

		}

		return $mimes;
	}

	/**
	 * Check and confirm if the real file type is indeed SVG
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_check_filetype_and_ext/
	 * @since 2.6.0
	 */
	public function confirm_file_type_is_svg( $filetypes_extensions, $file, $filename, $mimes ) {
		global $roles_svg_upload_enabled;

		// Check if global variable is set and is an array
		if ( ! isset( $roles_svg_upload_enabled ) || ! is_array( $roles_svg_upload_enabled ) ) {
			return $filetypes_extensions;
		}

		$current_user = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

		if ( count( $roles_svg_upload_enabled ) > 0 ) {

			// Check file extension
			if ( substr( $filename, -4 ) == '.svg' ) {

				// Add mime type for user roles set to enable SVG upload
				foreach ( $current_user_roles as $role ) {
					if ( in_array( $role, $roles_svg_upload_enabled ) ) {
						$filetypes_extensions['type'] = 'image/svg+xml';
						$filetypes_extensions['ext'] = 'svg';
					}
				}   

			}

		}

		return $filetypes_extensions;
	}

	/** 
	 * Sanitize the SVG file and maybe allow upload based on the result
	 *
	 * @since 2.6.0
	 */
	public function sanitize_and_maybe_allow_svg_upload( $file ) {        
		if ( ! isset( $file['tmp_name'] ) ) {
			return $file;
		}

		$file_tmp_name = $file['tmp_name']; // full path
		$file_name = isset( $file['name'] ) ? $file['name'] : '';
		$file_type_ext = wp_check_filetype_and_ext( $file_tmp_name, $file_name );
		$file_type = ! empty( $file_type_ext['type'] ) ? $file_type_ext['type'] : '';

		if ( 'image/svg+xml' === $file_type ) {
			$original_svg = file_get_contents( $file_tmp_name );

			$sanitizer = $this->get_svg_sanitizer();
			$sanitized_svg = $sanitizer->sanitize( $original_svg ); // boolean

			if ( false === $sanitized_svg ) {
				$file['error'] = 'This SVG file could not be sanitized, so, was not uploaded for security reasons.';
			}

			file_put_contents( $file_tmp_name, $sanitized_svg );
		}

		return $file;
	}
	
	/**
	 * Sanitize a file after it is added to the media library, e.g. via REST API POST request
	 * 
	 * @since 7.5.2
	 */
	public function sanitize_after_upload( $attachment, $request, $creating ) {
		// Let's sanitize SVG upon creation/insertion in the media library.
		if ( $creating ) {
			if ( $attachment instanceof WP_Post ) {
				$file_path = get_attached_file( $attachment->ID );
				$original_svg = file_get_contents( $file_path );

				$sanitizer = $this->get_svg_sanitizer();                
				$sanitized_svg = $sanitizer->sanitize( $original_svg ); // boolean
				
				if ( false !== $sanitized_svg ) {
					// Sanitization was a success, let's write the result back to the file
					file_put_contents( $file_path, $sanitized_svg );
				}
			}
		}
	}
	
	/**
	 * Get sanitizer object
	 * 
	 * @since 7.5.2
	 */
	public function get_svg_sanitizer() {
		if ( ! class_exists( '\enshrined\svgSanitize\Sanitizer' ) ) {
			// For now, we'll use a simple sanitizer approach
			// In a full implementation, you would include the SVG sanitizer library
			return new class {
				public function sanitize( $svg ) {
					// Basic SVG sanitization - remove script tags and dangerous attributes
					$svg = preg_replace( '/<script[^>]*>.*?<\/script>/is', '', $svg );
					$svg = preg_replace( '/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $svg );
					$svg = preg_replace( '/javascript:/i', '', $svg );
					return $svg;
				}
			};
		}

		$sanitizer = new \enshrined\svgSanitize\Sanitizer();
		
		return $sanitizer;        
	}

	/**
	 * Generate metadata for the svg attachment
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_generate_attachment_metadata/
	 * @since 2.6.0
	 */
	public function generate_svg_metadata( $metadata, $attachment_id, $context ) {

		if ( get_post_mime_type( $attachment_id ) == 'image/svg+xml' ) {

			// Get SVG dimensions
			$svg_path = get_attached_file( $attachment_id );
			$svg = simplexml_load_file( $svg_path );
			$width = 0;
			$height = 0;

			if ( $svg ) {

				$attributes = $svg->attributes();
				if ( isset( $attributes->width, $attributes->height ) ) {
					$width = intval( floatval( $attributes->width ) );
					$height = intval( floatval( $attributes->height ) );
				} elseif ( isset( $attributes->viewBox ) ) {
					$sizes = explode( ' ', $attributes->viewBox );
					if ( isset( $sizes[2], $sizes[3] ) ) {
						$width = intval( floatval( $sizes[2] ) );
						$height = intval( floatval( $sizes[3] ) );
					}
				}

			}

			$metadata['width'] = $width;
			$metadata['height'] = $height;

			// Get SVG filename
			$svg_url = wp_get_original_image_url( $attachment_id );
			$svg_url_path = str_replace( wp_upload_dir()['baseurl'] .'/' , '', $svg_url );
			$metadata['file'] = $svg_url_path;

		}

		return $metadata;
	}

	/**
	 * Remove responsive image attributes, i.e. srcset attributes, from SVG images HTML
	 * This helps ensure SVGs are displayed properly on the frontend
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/svg-support/tags/2.5.7/functions/attachment.php#L282
	 * @since 7.3.0
	 */
	public function disable_svg_srcset( $sources ) {
		$first_element = reset( $sources );

		if ( isset( $first_element ) && ! empty( $first_element['url'] ) ) {
			$extension = pathinfo( reset($sources)['url'], PATHINFO_EXTENSION );

			if ( 'svg' === $extension ) {
				$sources = array(); // return empty array
				return $sources;
			} else {
				return $sources;
			}
		} else {
			return $sources;
		}
	}
		
	/**
	 * Remove responsive image attributes, i.e. srcset attributes, from SVG images HTML
	 * This helps ensure SVGs are displayed properly on the frontend
	 * 
	 * @link https://gist.github.com/ericvalois/5b1e161c127632a1ace7d65ce1363e69
	 * @since 7.3.0
	 */
	public function remove_svg_responsive_image_attr( string $sizes, $size, $image_src = null ) {
		$explode = explode( '.', $image_src );
		$extension = end( $explode );
		
		if( 'svg' === $extension ){
			$sizes = '';
		}

		return $sizes;
	}

	/**
	 * Return svg file URL to show preview in media library
	 *
	 * @link https://developer.wordpress.org/reference/hooks/wp_ajax_action/
	 * @link https://developer.wordpress.org/reference/functions/wp_get_attachment_url/
	 * @since 2.6.0
	 */
	public function get_svg_attachment_url() {

		$attachment_url = '';
		$attachment_id = isset( $_REQUEST['attachmentID'] ) ? $_REQUEST['attachmentID'] : '';

		// Check response mime type
		if ( $attachment_id ) {

			echo esc_url( wp_get_attachment_url( $attachment_id ) );

			die();

		}

	}

	/**
	 * Return svg file URL to show preview in media library
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_prepare_attachment_for_js/
	 * @since 2.6.0
	 */
	public function get_svg_url_in_media_library( $response ) {

		// Check response mime type
		if ( $response['mime'] === 'image/svg+xml' ) {

			$response['image'] = array(
				'src'   => $response['url'],
			);

		}

		return $response;

	}
} 