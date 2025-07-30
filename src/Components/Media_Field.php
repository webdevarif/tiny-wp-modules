<?php
/**
 * Media Field Component
 *
 * @package TinyWpModules\Components
 */

namespace TinyWpModules\Components;

/**
 * Reusable Media Field Component
 */
class Media_Field {

	/**
	 * Enqueue media scripts
	 */
	public static function enqueue_scripts() {
		wp_enqueue_media();
	}

	/**
	 * Render a media upload field
	 *
	 * @param array $args Field arguments
	 */
	public static function render( $args = array() ) {
		$defaults = array(
			'id' => 'media_field',
			'name' => 'media_field',
			'value' => '',
			'label' => __( 'Media Field', 'tiny-wp-modules' ),
			'description' => __( 'Upload or select media.', 'tiny-wp-modules' ),
			'button_text' => __( 'Upload Media', 'tiny-wp-modules' ),
			'remove_text' => __( 'Remove Media', 'tiny-wp-modules' ),
			'preview_size' => 'thumbnail',
			'multiple' => false,
			'class' => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$field_id = $args['id'];
		$field_name = $args['name'];
		$field_value = $args['value'];
		$preview_size = $args['preview_size'];

		// Get preview URL
		$preview_url = '';
		if ( $field_value ) {
			$preview_url = wp_get_attachment_image_url( $field_value, $preview_size );
		}

		?>
		<div class="media-field-wrapper <?php echo esc_attr( $args['class'] ); ?>">
			<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
			<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $field_value ); ?>" />
			
			<div class="media-preview" id="<?php echo esc_attr( $field_id ); ?>_preview">
				<?php if ( $preview_url ) : ?>
					<img src="<?php echo esc_url( $preview_url ); ?>" style="max-width: 150px; height: auto; margin-bottom: 10px;" />
				<?php endif; ?>
			</div>
			
			<button type="button" class="button upload-media-button" data-field="<?php echo esc_attr( $field_id ); ?>" data-multiple="<?php echo esc_attr( $args['multiple'] ? 'true' : 'false' ); ?>">
				<?php echo esc_html( $args['button_text'] ); ?>
			</button>
			
			<button type="button" class="button remove-media-button" data-field="<?php echo esc_attr( $field_id ); ?>" <?php echo empty( $field_value ) ? 'style="display:none;"' : ''; ?>>
				<?php echo esc_html( $args['remove_text'] ); ?>
			</button>
			
			<?php if ( $args['description'] ) : ?>
				<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
			<?php endif; ?>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Check if wp.media is available
			if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
				console.warn('Tiny WP Modules: WordPress media library is not available');
				return;
			}
			
			var mediaUploader;
			
			// Upload button click
			$('.upload-media-button[data-field="<?php echo esc_js( $field_id ); ?>"]').on('click', function(e) {
				e.preventDefault();
				
				try {
					var fieldId = $(this).data('field');
					var isMultiple = $(this).data('multiple') === 'true';
					
					// If the uploader object has already been created, reopen the dialog
					if (mediaUploader) {
						mediaUploader.open();
						return;
					}
					
					// Create the media uploader
					mediaUploader = wp.media({
						title: 'Select Media',
						button: {
							text: 'Use this media'
						},
						multiple: isMultiple
					});
				
				// When media is selected, run a callback
				mediaUploader.on('select', function() {
					try {
						var attachment = mediaUploader.state().get('selection').first().toJSON();
						$('#' + fieldId).val(attachment.id);
						
						// Get preview URL with fallback
						var previewUrl = '';
						if (attachment.sizes && attachment.sizes.<?php echo esc_js( $preview_size ); ?> && attachment.sizes.<?php echo esc_js( $preview_size ); ?>.url) {
							previewUrl = attachment.sizes.<?php echo esc_js( $preview_size ); ?>.url;
						} else if (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
							previewUrl = attachment.sizes.thumbnail.url;
						} else if (attachment.url) {
							previewUrl = attachment.url;
						}
						
						if (previewUrl) {
							$('#' + fieldId + '_preview').html('<img src="' + previewUrl + '" style="max-width: 150px; height: auto; margin-bottom: 10px;" />');
						}
						$('.remove-media-button[data-field="' + fieldId + '"]').show();
					} catch (error) {
						console.error('Tiny WP Modules: Error processing media selection:', error);
					}
				});
				
				// Open the uploader dialog
				mediaUploader.open();
				} catch (error) {
					console.error('Tiny WP Modules: Error creating media uploader:', error);
				}
			});
			
			// Remove button click
			$('.remove-media-button[data-field="<?php echo esc_js( $field_id ); ?>"]').on('click', function(e) {
				e.preventDefault();
				
				var fieldId = $(this).data('field');
				$('#' + fieldId).val('');
				$('#' + fieldId + '_preview').empty();
				$(this).hide();
			});
		});
		</script>
		<?php
	}
} 