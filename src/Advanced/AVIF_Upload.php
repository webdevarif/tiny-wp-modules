<?php
/**
 * AVIF Upload Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

/**
 * Class for AVIF Upload module
 *
 * @since 1.0.0
 */
class AVIF_Upload {

	/**
	 * Initialize the AVIF Upload module
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks() {
		add_filter( 'upload_mimes', array( $this, 'allow_avif_mime_type_upload' ) );
		add_filter( 'wp_get_mime_types', array( $this, 'add_avif_mime_type' ) );
		add_filter( 'mime_types', array( $this, 'add_avif_mime_type' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'handle_exif_and_fileinfo_fail' ), 10, 5 );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'add_avif_image_dimension' ), 10, 3 );
		add_filter( 'file_is_displayable_image', array( $this, 'make_avif_displayable' ), 10, 2 );
	}

	/**
	 * Add AVIF mime type to list of mime types
	 *
	 * @since 1.0.0
	 * @param array $wp_get_mime_types WordPress mime types.
	 * @return array Modified mime types.
	 */
	public function add_avif_mime_type( $wp_get_mime_types ) {
		$wp_get_mime_types['avif'] = 'image/avif';
		return $wp_get_mime_types;
	}

	/**
	 * Add AVIF mime type to allowed mime types
	 *
	 * @since 1.0.0
	 * @param array $mimes Allowed mime types.
	 * @return array Modified mime types.
	 */
	public function add_avif_mime_type_upload( $mimes ) {
		$mimes['avif'] = 'image/avif';
		return $mimes;
	}

	/**
	 * Add AVIF to mapping of mime types to their respective extensions
	 *
	 * @since 1.0.0
	 * @param array $mime_to_ext Mime type to extension mapping.
	 * @return array Modified mapping.
	 */
	public function add_avif_mime_type_to_exts( $mime_to_ext ) {
		$mime_to_ext['image/avif'] = 'avif';
		return $mime_to_ext;
	}
	
	/**
	 * Add correct dimension for AVIF images
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/avif-support/trunk/includes/AvifSupport.php#L104
	 * @since 1.0.0
	 * @param array  $metadata      Attachment metadata.
	 * @param int    $attachment_id Attachment ID.
	 * @param string $context       Context.
	 * @return array Modified metadata.
	 */
	public function add_avif_image_dimension( $metadata, $attachment_id, $context ) {
				
		if ( empty( $metadata ) ) {
			return $metadata;
		}
		
		$attachment_post = get_post( $attachment_id );
		
		if ( ! $attachment_post || is_wp_error( $attachment_post ) ) {
			return $metadata;
		}
		
		if ( 'image/avif' !== $attachment_post->post_mime_type ) {
			return $metadata;
		}
		
		// Fix width and height
		if ( 
			( ! empty( $metadata['width'] ) 
			  && ( 0 !== $metadata['width'] ) ) 
			  && ( ! empty( $metadata['height'] ) 
			  && 0 !== $metadata['height'] ) 
			) {
			return $metadata;
		}
		
		$file = get_attached_file( $attachment_id );
		
		if ( ! $file ) {
			return $metadata;   
		}
		
		if ( empty( $metadata['width'] ) ) {
			$metadata['width'] = 0;
		}

		if ( empty( $metadata['height'] ) ) {
			$metadata['height'] = 0;
		}
		
		if ( empty( $metadata['file'] ) ) {
			$metadata['file'] = _wp_relative_upload_path( $file );
		}
		
		if ( empty( $metadata['sizes'] ) ) {
			$metadata['sizes'] = array();
		}
		
		$img_size = wp_getimagesize( $file );

		// Legacy PHP Version, return false, fake it till manual.
		if ( empty( $img_size ) ) {
			$img_size = array(
				0      => 0,
				1      => 0,
				2      => 19,
				3      => 'width="0" height="0"',
				'mime' => 'image/avif',
			);
		}

		if ( is_array( $img_size ) && ( 0 !== $img_size[0] ) && ( 0 !== $img_size[1] ) ) {
			// Do nothing, we have what we need
		} else {
			
			// Manually get width and height
			$binary_string = file_get_contents( $file );
			$ispe_pos      = strpos( $binary_string, 'ispe' );

			if ( false === $ispe_pos ) {
				// Corrupted Image.
				return false;
			}

			$dim_start_pos = $ispe_pos + 8;
			$dim_bin       = substr( $binary_string, $dim_start_pos, 8 );
			$width         = hexdec( bin2hex( substr( $dim_bin, 0, 4 ) ) );
			$height        = hexdec( bin2hex( substr( $dim_bin, 4, 8 ) ) );

			if ( $width && $height && is_numeric( $width ) && is_numeric( $height ) ) {
				$img_size[0] = absint( $width );
				$img_size[1] = absint( $height );
			}
		}
		
		if ( ! $img_size ) {
			$avif_specs = false;
		} else {
			$file_size = filesize( $file );
			$avif_specs = array(
				'width'       => $img_size[0],
				'height'      => $img_size[1],
				'mime'        => $img_size['mime'],
				'dimension'   => $img_size[0] . 'x' . $img_size[1],
				'ext'         => str_replace( 'image/', '', $img_size['mime'] ),
				'size'        => $file_size,
				'size_format' => size_format( $file_size ),
			);
		}
		
		if ( is_wp_error( $avif_specs ) || ! $avif_specs ) {
			return $metadata;
		}
		
		$metadata['width'] = $avif_specs['width'];
		$metadata['height'] = $avif_specs['height'];
		
		return $metadata;
	}
	
	/**
	 * Make sure AVIF files are displayable in the browser
	 * 
	 * @since 1.0.0
	 * @param bool   $result Whether the file is displayable.
	 * @param string $path   File path.
	 * @return bool Modified result.
	 */
	public function make_avif_displayable( $result, $path ) {
		if ( str_ends_with( $path, '.avif' ) ) {
			return true;
		}
		
		return $result;
	}

	/**
	 * Handle rare scenarios where exif and fileinfo fail to detect AVIF
	 * 
	 * @since 1.0.0
	 * @param array  $wp_check_filetype_and_ext File type check result.
	 * @param string $file                      File path.
	 * @param string $filename                  Filename.
	 * @param array  $mimes                     Allowed mime types.
	 * @param string $real_mime                 Real mime type.
	 * @return array Modified file type check result.
	 */
	public function handle_exif_and_fileinfo_fail( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {

		// AVIF is properly handled, no need to do anything else
		if ( $wp_check_filetype_and_ext['ext'] && $wp_check_filetype_and_ext['type'] ) {
			return $wp_check_filetype_and_ext;
		}

		// Not an .avif file, no need to do anything else
		if ( ! str_ends_with( $filename, '.avif' ) ) {
			return $wp_check_filetype_and_ext;
		} else {
			$binary_string = file_get_contents( $file );
			$ispe_pos      = strpos( $binary_string, 'ispe' );

			if ( false === $ispe_pos ) {
				// Corrupted Image.
				return false;
			}

			$dim_start_pos = $ispe_pos + 8;
			$dim_bin       = substr( $binary_string, $dim_start_pos, 8 );
			$width         = hexdec( bin2hex( substr( $dim_bin, 0, 4 ) ) );
			$height        = hexdec( bin2hex( substr( $dim_bin, 4, 8 ) ) );

			// If this is a valid image with proper width and height, set filetype and ext to AVIF
			if ( $width && $height && is_numeric( $width ) && is_numeric( $height ) ) {
				$wp_check_filetype_and_ext['type'] = 'image/avif';
				$wp_check_filetype_and_ext['ext']  = 'avif';                
			}
			
			return $wp_check_filetype_and_ext;
		}
	}
}
