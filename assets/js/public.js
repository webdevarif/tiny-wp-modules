/**
 * Public JavaScript for Tiny WP Modules
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		
		// Initialize public functionality
		initPublic();
		
	});

	/**
	 * Initialize public functionality
	 */
	function initPublic() {
		console.log('Tiny WP Modules Public initialized');
		
		// Add smooth scrolling to anchor links
		$('a[href^="#"]').on('click', function(e) {
			e.preventDefault();
			var target = $(this.getAttribute('href'));
			if (target.length) {
				$('html, body').animate({
					scrollTop: target.offset().top - 50
				}, 500);
			}
		});
		
		// Add loading states to buttons
		$('.tiny-wp-modules-button').on('click', function() {
			var $button = $(this);
			if (!$button.hasClass('loading')) {
				$button.addClass('loading').text('Loading...');
				setTimeout(function() {
					$button.removeClass('loading');
				}, 2000);
			}
		});
		
		// Add hover effects to widgets
		$('.tiny-wp-modules-widget').hover(
			function() {
				$(this).addClass('hover');
			},
			function() {
				$(this).removeClass('hover');
			}
		);
	}

	/**
	 * AJAX helper function for public requests
	 */
	function publicAjaxRequest(action, data, callback) {
		data = data || {};
		data.action = action;
		data.nonce = tiny_wp_modules_public.nonce;
		
		$.post(tiny_wp_modules_public.ajax_url, data, function(response) {
			if (response.success) {
				if (callback) callback(response.data);
			} else {
				console.error('Public AJAX Error:', response.data);
			}
		}).fail(function() {
			console.error('Public AJAX Request failed');
		});
	}

	/**
	 * Show public notification
	 */
	function showPublicNotification(message, type) {
		var $notification = $('<div class="tiny-wp-modules-notification ' + type + '">' + message + '</div>');
		$('body').append($notification);
		
		// Auto dismiss after 3 seconds
		setTimeout(function() {
			$notification.fadeOut(function() {
				$(this).remove();
			});
		}, 3000);
	}

})(jQuery); 