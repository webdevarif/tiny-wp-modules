/**
 * Admin JavaScript for Tiny WP Modules
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		
		// Initialize admin functionality
		initAdmin();
		
		// Global save button
		$('#save-all-settings').on('click', function(e) {
			e.preventDefault();
			saveAllSettings();
		});

		// Refresh status button
		$('#refresh-status').on('click', function(e) {
			e.preventDefault();
			refreshStatus();
		});

		// Check for updates button
		$('#check-for-updates').on('click', function(e) {
			e.preventDefault();
			checkForUpdates();
		});
		
	});

	/**
	 * Initialize admin functionality
	 */
	function initAdmin() {
		console.log('Tiny WP Modules Admin initialized');
		
		// Add loading states to buttons
		$('.button').on('click', function() {
			var $button = $(this);
			if (!$button.hasClass('disabled')) {
				$button.addClass('disabled').prop('disabled', true);
				setTimeout(function() {
					$button.removeClass('disabled').prop('disabled', false);
				}, 1000);
			}
		});
		
		// Add hover effects to cards
		$('.tiny-wp-modules-card').hover(
			function() {
				$(this).addClass('hover');
			},
			function() {
				$(this).removeClass('hover');
			}
		);

		// Handle tab navigation
		$('.nav-tab').on('click', function(e) {
			var $tab = $(this);
			var targetTab = $tab.attr('href').split('tab=')[1];
			
			// Update active tab
			$('.nav-tab').removeClass('nav-tab-active');
			$tab.addClass('nav-tab-active');
			
			// Show corresponding content
			$('.tab-content').hide();
			$('#' + targetTab + '-tab').show();
		});
	}

	/**
	 * Save all settings
	 */
	function saveAllSettings() {
		var $button = $('#save-all-settings');
		var originalText = $button.text();
		
		$button.text('Saving...').prop('disabled', true);
		
		// Submit the form
		$('#tiny-wp-modules-settings-form').submit();
		
		// Show success message
		setTimeout(function() {
			showNotification('Settings saved successfully!', 'success');
			$button.text('Saved!').removeClass('button-primary').addClass('button-success');
			
			setTimeout(function() {
				$button.text(originalText).removeClass('button-success').addClass('button-primary').prop('disabled', false);
			}, 2000);
		}, 500);
	}

	/**
	 * Refresh plugin status
	 */
	function refreshStatus() {
		var $button = $('#refresh-status');
		var originalText = $button.text();
		
		$button.text('Refreshing...').prop('disabled', true);
		
		// Simulate AJAX call
		setTimeout(function() {
			$button.text('Status Updated!').removeClass('button-secondary').addClass('button-primary');
			
			setTimeout(function() {
				$button.text(originalText).removeClass('button-primary').addClass('button-secondary').prop('disabled', false);
			}, 2000);
		}, 1000);
	}

	/**
	 * Show notification
	 */
	function showNotification(message, type) {
		var $notification = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
		$('.wrap h1').after($notification);
		
		// Auto dismiss after 5 seconds
		setTimeout(function() {
			$notification.fadeOut();
		}, 5000);
	}

	/**
	 * Check for updates
	 */
	function checkForUpdates() {
		var $button = $('#check-for-updates');
		var originalText = $button.text();
		
		$button.text('Checking...').prop('disabled', true);
		
		ajaxRequest('check_for_updates', {}, function(data) {
			if (data.has_update) {
				$button.text('Update Available!').removeClass('button-secondary').addClass('button-primary');
				showNotification(data.message, 'success');
			} else {
				$button.text('Up to Date').removeClass('button-secondary').addClass('button-success');
				showNotification(data.message, 'success');
			}
			
			setTimeout(function() {
				$button.text(originalText).removeClass('button-primary button-success').addClass('button-secondary').prop('disabled', false);
			}, 3000);
		});
	}

	/**
	 * AJAX helper function
	 */
	function ajaxRequest(action, data, callback) {
		data = data || {};
		data.action = action;
		data.nonce = tiny_wp_modules_ajax.nonce;
		
		$.post(tiny_wp_modules_ajax.ajax_url, data, function(response) {
			if (response.success) {
				if (callback) callback(response.data);
			} else {
				showNotification('Error: ' + response.data, 'error');
			}
		}).fail(function() {
			showNotification('Request failed. Please try again.', 'error');
		});
	}

})(jQuery); 