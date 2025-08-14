<?php
/**
 * Image Upload Control Module
 *
 * @package TinyWpModules\Advanced
 */

namespace TinyWpModules\Advanced;

use Imagick;

/**
 * Class for Image Upload Control module
 *
 * @since 1.0.0
 */
class Image_Upload_Control {
    
    /**
     * Whether PNG has transparency
     *
     * @var bool
     */
    public $png_is_transparent;

    /**
     * Array storing the file names that were processed, as keys.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $orientation_fixed;

    /**
     * Array storing the meta data of original files in case it
     * needs to be restored later.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $previous_meta;

    /**
     * Constructor
     * @since 1.0.0
     */
    public function __construct() {
        $this->png_is_transparent = false;
        $this->orientation_fixed = array();
        $this->previous_meta = array();
        
        // Defer hook initialization until after all classes are loaded
        add_action( 'init', array( $this, 'init_hooks' ) );
    }

    /**
     * Initialize WordPress hooks
     */
    public function init_hooks() {
        // Check if Settings_Config class exists before using it
        if ( ! class_exists( 'TinyWpModules\\Admin\\Settings_Config' ) ) {
            return;
        }
        
        add_filter( 'wp_handle_upload', array( $this, 'image_upload_handler' ), 10, 1 );
        add_filter( 'wp_handle_upload_prefilter', array( $this, 'prefilter_maybe_fix_image_orientation' ), 10, 1 );
        add_filter( 'wp_handle_upload', array( $this, 'maybe_fix_image_orientation' ), 10, 1 );
    }

    /**
     * Handler for image uploads. Convert and resize images.
     *
     * @since 1.0.0
     * @param array $upload Upload data array.
     * @return array Modified upload data.
     */
    public function image_upload_handler( $upload ) {
        // Check if image upload control is enabled
        if ( ! \TinyWpModules\Admin\Settings_Config::is_enabled( 'enable_image_upload_control' ) ) {
            return $upload;
        }

        $applicable_mime_types = array(
            'image/bmp',
            'image/x-ms-bmp',
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/webp'
        );
        
        if ( in_array( $upload['type'], $applicable_mime_types ) ) {
            // Exclude from conversion and resizing images with filenames ending with '-nr', e.g. birds-nr.png
            if ( false !== strpos( $upload['file'], '-nr.' ) ) {
                return $upload;
            }
            
            // Convert BMP and non-transparent PNG to JPEG
            if ( 'image/bmp' === $upload['type'] || 'image/x-ms-bmp' === $upload['type'] ) {
                $upload = $this->maybe_convert_image( 'bmp', $upload );
            }
            if ( 'image/png' === $upload['type'] ) {
                $upload = $this->maybe_convert_image( 'png', $upload );
            }
            
            // At this point, BMPs and non-transparent PNGs are already converted to JPGs, unless excluded with '-nr' suffix.
            // Let's perform resize operation as needed, i.e. if image dimension is larger than specified
            $mime_types_to_resize = array(
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp'
            );
            
            if ( !is_wp_error( $upload ) && in_array( $upload['type'], $mime_types_to_resize ) && filesize( $upload['file'] ) > 0 ) {
                $wp_image_editor = wp_get_image_editor( $upload['file'] );
                if ( !is_wp_error( $wp_image_editor ) ) {
                    $image_size = $wp_image_editor->get_size();
                    $max_width = \TinyWpModules\Admin\Settings_Config::get_setting( 'image_max_width', 1920 );
                    $max_height = \TinyWpModules\Admin\Settings_Config::get_setting( 'image_max_height', 1080 );
                    $convert_to_jpg_quality = \TinyWpModules\Admin\Settings_Config::get_setting( 'image_conversion_quality', 82 );
                    
                    // Check upload image's dimension and only resize if larger than the defined max dimension
                    if ( isset( $image_size['width'] ) && $image_size['width'] > $max_width || isset( $image_size['height'] ) && $image_size['height'] > $max_height ) {
                        $wp_image_editor->resize( $max_width, $max_height, false );
                        // false is for no cropping
                    }
                    
                    // Save
                    if ( 'image/jpg' === $upload['type'] || 'image/jpeg' === $upload['type'] ) {
                        $wp_image_editor->set_quality( $convert_to_jpg_quality );
                    }
                    $wp_image_editor->save( $upload['file'] );
                }
            }
        }
        
        return $upload;
    }

    /**
     * Convert BMP or PNG without transparency into JPG
     *
     * @since 1.0.0
     * @param string $file_extension File extension.
     * @param array  $upload Upload data array.
     * @return array Modified upload data.
     */
    public function maybe_convert_image( $file_extension, $upload ) {
        $image_object = null;
        
        // Get image object from uploaded BMP/PNG
        if ( 'bmp' === $file_extension ) {
            if ( is_file( $upload['file'] ) ) {
                // Generate image object from BMP for conversion to JPG later
                if ( function_exists( 'imagecreatefrombmp' ) ) {
                    // PHP >= v7.2
                    $image_object = imagecreatefrombmp( $upload['file'] );
                } else {
                    // PHP < v7.2 - fallback to Imagick
                    if ( class_exists( 'Imagick' ) ) {
                        $imagick = new Imagick();
                        $imagick->readImage( $upload['file'] );
                        $image_object = $imagick;
                    }
                }
            }
        }
        
        if ( 'png' === $file_extension ) {
            // Detect alpha/transparency in PNG
            $this->png_is_transparent = false;
            if ( is_file( $upload['file'] ) ) {
                if ( function_exists( 'imagecreatefrompng' ) ) {
                    // GD library is present, so 'imagecreatefrompng' function is available
                    // Generate image object from PNG for potential conversion to JPG later.
                    $image_object = imagecreatefrompng( $upload['file'] );
                    // Get image dimension
                    list( $width, $height ) = getimagesize( $upload['file'] );
                    // Run through pixels until transparent pixel is found
                    for ($x = 0; $x < $width; $x++) {
                        for ($y = 0; $y < $height; $y++) {
                            $pixel_color_index = imagecolorat( $image_object, $x, $y );
                            $pixel_rgba = imagecolorsforindex( $image_object, $pixel_color_index );
                            // array of red, green, blue and alpha values
                            if ( $pixel_rgba['alpha'] > 0 ) {
                                // a pixel with alpha/transparency has been found
                                // alpha value range from 0 (completely opaque) to 127 (fully transparent).
                                $this->png_is_transparent = true;
                                break 2;
                                // Break both 'for' loops
                            }
                        }
                    }
                } else {
                    if ( class_exists( 'Imagick' ) ) {
                        $imagick = new Imagick();
                        $imagick->readImage( $upload['file'] );
                        // If the channel is defined, and has any transparent areas across any frame, then maxima will always be greater then minima.
                        $alpha_range = $imagick->getImageChannelRange( Imagick::CHANNEL_ALPHA );
                        $this->png_is_transparent = $alpha_range['minima'] < $alpha_range['maxima'];
                        $image_object = $imagick;
                    }
                }
            }
            
            // Do not convert PNG with alpha/transparency
            if ( $this->png_is_transparent ) {
                return $upload;
            }
        }
        
        // Let's convert BMP and non-transparent PNG into JPG
        $converted_to_jpg = false;
        if ( is_object( $image_object ) || class_exists( 'Imagick' ) ) {
            $wp_uploads = wp_upload_dir();
            $old_filename = wp_basename( $upload['file'] );
            // Assign new, unique file name for the converted image
            $new_filename = str_ireplace( '.' . $file_extension, '.jpg', $old_filename );
            $new_filename = wp_unique_filename( dirname( $upload['file'] ), $new_filename );
            $converted_to_jpg = false;
        }
        
        if ( is_object( $image_object ) && !( $image_object instanceof Imagick ) ) {
            // When image object creation is successful using GD
            // When conversion from BMP/PNG to JPG is successful using GD. Last parameter is JPG quality (0-100).
            if ( imagejpeg( $image_object, $wp_uploads['path'] . '/' . $new_filename, 90 ) ) {
                $converted_to_jpg = true;
            }
        } else {
            // When image object creation with GD is not successful, we use Imagick to convert from BMP and non-transparent PNG to JPG.
            if ( class_exists( 'Imagick' ) ) {
                $imagick = new Imagick();
                $imagick->readImage( $upload['file'] );
                $imagick->setImageCompressionQuality( 90 );
                $imagick->setImageFormat( 'jpg' );
                
                if ( $imagick->writeImage( $wp_uploads['path'] . '/' . $new_filename ) ) {
                    $converted_to_jpg = true;
                }
                // Clear the Imagick object
                $imagick->clear();
                $imagick->destroy();
            }
        }
        
        if ( $converted_to_jpg ) {
            // Delete original BMP / PNG
            unlink( $upload['file'] );
            // Add converted JPG info into $upload
            $upload['file'] = $wp_uploads['path'] . '/' . $new_filename;
            $upload['url'] = $wp_uploads['url'] . '/' . $new_filename;
            $upload['type'] = 'image/jpeg';
        }
        
        return $upload;
    }

    /**
     * Generate image object from PNG/JPG with GD library
     * 
     * @since 1.0.0
     * @param string $file File path.
     * @param string $file_extension File extension.
     * @param string $webp_path WebP output path.
     * @param int    $webp_conversion_quality WebP conversion quality.
     */
    public function gd_generate_webp( $file, $file_extension, $webp_path, $webp_conversion_quality ) {
        if ( 'png' == $file_extension ) {
            $image_object = imagecreatefrompng( $file );
            if ( $this->png_is_transparent ) {
                imagepalettetotruecolor( $image_object );
            }
        }
        if ( 'jpg' == $file_extension || 'jpeg' == $file_extension ) {
            $image_object = imagecreatefromjpeg( $file );
        }
        
        // When creation of image object from PNG/JPG is successful. let's generate WebP image
        // Second parameter is file path, last parameter is WebP quality (0-100).
        if ( !is_null( $image_object ) && is_object( $image_object ) ) {
            imagewebp( $image_object, $webp_path, $webp_conversion_quality );
        }
    }

    /**
     * Checks the filename before it is uploaded to WordPress and
     * runs the fix_image_orientation function in case its needed.
     *
     * @since 1.0.0
     * @param array $file An array of data for a single file.
     * @return array An array of data for a single file.
     */
    public function prefilter_maybe_fix_image_orientation( $file ) {
        // Get the file extension
        $suffix = pathinfo( $file['name'], PATHINFO_EXTENSION );
        if ( in_array( strtolower( $suffix ), array('jpg', 'jpeg', 'tiff'), true ) ) {
            $this->fix_image_orientation( $file['tmp_name'] );
        }
        return $file;
    }

    /**
     * Checks the filename before it is uploaded to WordPress and
     * runs the fix_image_orientation function in case its needed.
     *
     * @since 1.0.0
     * @param array $file Array of upload data.
     * @return array Array of upload data.
     */
    public function maybe_fix_image_orientation( $file ) {
        $suffix = substr( $file['file'], strrpos( $file['file'], '.', -1 ) + 1 );
        if ( in_array( strtolower( $suffix ), array('jpg', 'jpeg', 'tiff'), true ) ) {
            $this->fix_image_orientation( $file['file'] );
        }
        return $file;
    }

    /**
     * Fixes the orientation of the image based on exif data
     *
     * @since 1.0.0
     * @param string $file Path of the file.
     */
    public function fix_image_orientation( $file ) {
        if ( !isset( $this->orientation_fixed[$file] ) ) {
            $exif = @exif_read_data( $file );
            if ( isset( $exif ) && isset( $exif['Orientation'] ) && $exif['Orientation'] > 1 ) {
                // Calculate the operations we need to perform on the image.
                $operations = $this->calculate_flip_and_rotate( $file, $exif );
                if ( false !== $operations ) {
                    // Lets flip flop and rotate the image as needed.
                    $this->do_flip_and_rotate( $file, $operations );
                }
            }
        }
    }

    /**
     * Calculate the flips and rotations image will need to do to fix its orientation.
     *
     * @since 1.0.0
     * @param string $file Path of the file.
     * @param array  $exif Exif data of the image.
     * @return array|bool Array of operations to be performed on the image, false if no operations are needed.
     */
    private function calculate_flip_and_rotate( $file, $exif ) {
        $rotator = false;
        $flipper = false;
        $orientation = 0;
        
        // Lets switch to the orientation defined in the exif data.
        switch ( $exif['Orientation'] ) {
            case 1:
                // We don't want to fix an already correct image :).
                $this->orientation_fixed[$file] = true;
                return false;
            case 2:
                $flipper = array(false, true);
                break;
            case 3:
                $orientation = -180;
                $rotator = true;
                break;
            case 4:
                $flipper = array(true, false);
                break;
            case 5:
                $orientation = -90;
                $rotator = true;
                $flipper = array(false, true);
                break;
            case 6:
                $orientation = -90;
                $rotator = true;
                break;
            case 7:
                $orientation = -270;
                $rotator = true;
                $flipper = array(false, true);
                break;
            case 8:
            case 9:
                $orientation = -270;
                $rotator = true;
                break;
            default:
                $orientation = 0;
                $rotator = true;
                break;
        }
        
        return compact( 'orientation', 'rotator', 'flipper' );
    }

    /**
     * Flips and rotates the image based on the parameters provided.
     *
     * @since 1.0.0
     * @param string $file Path of the file.
     * @param array  $operations Array of operations to be performed on the image.
     * @return bool Returns true if operations were successful, false otherwise.
     */
    private function do_flip_and_rotate( $file, $operations ) {
        $editor = wp_get_image_editor( $file );
        
        // If GD Library is being used, then we need to store metadata to restore later.
        if ( 'WP_Image_Editor_GD' === get_class( $editor ) ) {
            include_once ABSPATH . 'wp-admin/includes/image.php';
            $this->previous_meta[$file] = wp_read_image_metadata( $file );
        }
        
        if ( !is_wp_error( $editor ) ) {
            // Lets rotate and flip the image based on exif orientation.
            if ( true === $operations['rotator'] ) {
                $editor->rotate( $operations['orientation'] );
            }
            if ( false !== $operations['flipper'] ) {
                $editor->flip( $operations['flipper'][0], $operations['flipper'][1] );
            }
            $editor->save( $file );
            $this->orientation_fixed[$file] = true;
            add_filter(
                'wp_read_image_metadata',
                array($this, 'restore_meta_data'),
                10,
                2
            );
            return true;
        }
        
        return false;
    }

    /**
     * Restores the meta data of the image after being processed.
     *
     * WordPress' Imagick Library does not need this, but GD library
     * removes metadata from the image upon rotation or flip so this
     * method restores those values.
     *
     * @since 1.0.0
     * @param array  $meta Image meta data.
     * @param string $file Path to image file.
     * @return array Image meta data.
     */
    public function restore_meta_data( $meta, $file ) {
        if ( isset( $this->previous_meta[$file] ) ) {
            $meta = $this->previous_meta[$file];
            // Setting the Orientation meta to the new value after fixing the rotation.
            $meta['orientation'] = 1;
            return $meta;
        }
        return $meta;
    }
}
