/**
 * Plugin Row JavaScript for Tiny WP Modules
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		
		// Initialize plugin row functionality
		initPluginRow();
		
	});

	/**
	 * Initialize plugin row functionality
	 */
	function initPluginRow() {
		
		// Handle "Check for Updates" link clicks
		$(document).on('click', '.check-updates-link', function(e) {
			e.preventDefault();
			
			var $link = $(this);
			var $row = $link.closest('tr');
			var $pluginTitle = $row.find('.plugin-title');
			var $description = $row.find('.description');
			var originalText = $link.text();
			
			// Remove any existing update messages first
			$row.find('.update-message').remove();
			
			// Show loading state
			$link.text(tiny_wp_modules_plugin_row.loading)
				.addClass('updating-message')
				.prop('disabled', true);
			
			// Add loading spinner after description
			$description.after(
				'<div class="update-message notice inline notice-info notice-alt">' +
				'<p><span class="spinner is-active"></span> ' + tiny_wp_modules_plugin_row.loading + '</p>' +
				'</div>'
			);
			
			// Make AJAX request
			$.post(tiny_wp_modules_plugin_row.ajax_url, {
				action: 'check_plugin_updates',
				nonce: tiny_wp_modules_plugin_row.nonce
			}, function(response) {
				
				// Remove loading spinner
				$row.find('.update-message').remove();
				
				if (response.success) {
					if (response.data.has_update) {
						// Show update available message
						$description.after(
							'<div class="update-message notice inline notice-warning notice-alt">' +
							'<p><strong>' + tiny_wp_modules_plugin_row.update_available + '</strong> ' +
							response.data.message + '</p>' +
							'</div>'
						);
						
						// Update link text
						$link.text(tiny_wp_modules_plugin_row.update_available)
							.removeClass('updating-message')
							.addClass('update-available');
					} else {
						// Show up to date message
						$description.after(
							'<div class="update-message notice inline notice-success notice-alt">' +
							'<p><strong>' + tiny_wp_modules_plugin_row.up_to_date + '</strong> ' +
							response.data.message + '</p>' +
							'</div>'
						);
						
						// Update link text
						$link.text(tiny_wp_modules_plugin_row.up_to_date)
							.removeClass('updating-message')
							.addClass('up-to-date');
					}
				} else {
					// Show error message
					$description.after(
						'<div class="update-message notice inline notice-error notice-alt">' +
						'<p><strong>' + tiny_wp_modules_plugin_row.error + '</strong></p>' +
						'</div>'
					);
					
					// Reset link
					$link.text(originalText)
						.removeClass('updating-message')
						.prop('disabled', false);
				}
				
			}).fail(function() {
				// Remove loading spinner
				$row.find('.update-message').remove();
				
				// Show error message
				$description.after(
					'<div class="update-message notice inline notice-error notice-alt">' +
					'<p><strong>' + tiny_wp_modules_plugin_row.error + '</strong></p>' +
					'</div>'
				);
				
				// Reset link
				$link.text(originalText)
					.removeClass('updating-message')
					.prop('disabled', false);
			});
			
		});
		
		// Remove the conflicting hover event handlers since we're using CSS :hover
		// $(document).on('mouseenter', '.check-updates-link', function() {
		// 	$(this).addClass('hover');
		// }).on('mouseleave', '.check-updates-link', function() {
		// 	$(this).removeClass('hover');
		// });
		
		// Auto-hide update messages after 5 seconds
		$(document).on('click', '.check-updates-link', function() {
			var $row = $(this).closest('tr');
			setTimeout(function() {
				$row.find('.update-message').fadeOut(300, function() {
					$(this).remove();
				});
			}, 5000);
		});
		
	}

})(jQuery); 