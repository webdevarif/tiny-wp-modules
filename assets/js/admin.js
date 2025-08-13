/**
 * Tiny WP Modules Admin JavaScript
 * Organized with proper initialization pattern
 */

(function($) {
    'use strict';



    /**
     * Tiny WP Modules Admin Class
     */
    var TinyWpModulesAdmin = {
        
            init: function() {
        this.hideAllConfigSections();
        this.initModuleToggles();
        this.bindEvents();
        this.handleBannerClose();
    },

            hideAllConfigSections: function() {
            $('.config-fields-section').hide();
        },

        initModuleToggles: function() {
            var self = this;
            setTimeout(function() {
                var checkboxes = $('[data-expandable-module] input[type="checkbox"]');
                checkboxes.each(function() {
                    var checkboxId = $(this).attr('id');
                    self.handleModuleToggle(checkboxId);
                });
            }, 100);
        },

        bindEvents: function() {
            var self = this;
            
            // Handle only main module toggle checkboxes (not child checkboxes)
            $(document).on('change', '[data-expandable-module] input[type="checkbox"]', function() {
                var checkboxId = $(this).attr('id');
                self.handleModuleToggle(checkboxId);
            });

            // Handle collapse/expand link clicks
            $(document).on('click', '.collapse-toggle', function(e) {
                e.preventDefault();
                self.handleCollapseToggle($(this));
            });

            			// Handle Elementor checkbox change to refresh page for tab visibility
			$(document).on('change', '#enable_elementor', function() {
				if ($(this).is(':checked')) {
					// Show a message that the page will refresh
					if (confirm('Elementor support has been enabled. The page will refresh to show the new Elementor tab.')) {
						// Save the setting first before refreshing
						var form = $(this).closest('form');
						if (form.length) {
							// Create a hidden input for the current tab if it doesn't exist
							if (!form.find('input[name="current_tab"]').length) {
								form.append('<input type="hidden" name="current_tab" value="general">');
							}
							
							// Submit the form via AJAX to save the setting
							var formData = new FormData(form[0]);
							formData.append('action', 'save_general_settings');
							// Use the form nonce instead of AJAX nonce
							formData.append('nonce', form.find('input[name="tiny_wp_modules_nonce"]').val());
							
							// Debug logging
							console.log('Form data being sent:');
							for (var pair of formData.entries()) {
								console.log(pair[0] + ': ' + pair[1]);
							}
							console.log('Nonce being sent:', tiny_wp_modules_ajax.nonce);
							console.log('AJAX URL:', tiny_wp_modules_ajax.ajax_url);
							
							$.ajax({
								url: tiny_wp_modules_ajax.ajax_url,
								type: 'POST',
								data: formData,
								processData: false,
								contentType: false,
								success: function(response) {
									if (response.success) {
										// Setting saved successfully, now refresh the page
										window.location.reload();
									} else {
										// Save failed, show error
										alert('Failed to save setting. Please try again.');
										// Uncheck the checkbox
										$('#enable_elementor').prop('checked', false);
									}
								},
								error: function() {
									// AJAX failed, show error
									alert('Failed to save setting. Please try again.');
									// Uncheck the checkbox
									$('#enable_elementor').prop('checked', false);
								}
							});
						} else {
							// No form found, just refresh (fallback)
							window.location.reload();
						}
					} else {
						// Uncheck if user cancels
						$(this).prop('checked', false);
					}
				}
			});

			// Handle Elementor module toggles (show/hide items only)
			$(document).on('change', '.module-switch', function() {
				var moduleId = $(this).attr('id');
				var isEnabled = $(this).is(':checked');
				var moduleItems = $('#' + moduleId + '-items');
				
				// Show/hide items only
				if (isEnabled) {
					moduleItems.slideDown(200);
				} else {
					moduleItems.slideUp(200);
				}
			});
        },

        toggleModuleConfig: function(checkboxId, dataToggleTarget) {
            var isEnabled = $('#' + checkboxId).is(':checked');
            var configSection = $('#config-' + dataToggleTarget);
            var collapseLink = $('#collapse-' + dataToggleTarget);
            
            if (isEnabled) {
                configSection.show().addClass('show').addClass('collapsed');
                collapseLink.show();
                collapseLink.find('.collapse-text').text('EXPAND');
                collapseLink.find('.collapse-icon').text('▼');
            } else {
                configSection.hide().removeClass('show').removeClass('collapsed');
                collapseLink.hide();
            }
        },

        handleModuleToggle: function(checkboxId) {
            var checkbox = $('#' + checkboxId);
            var expandableModule = checkbox.closest('[data-expandable-module]');
            var dataToggle = expandableModule.attr('data-expandable-module');
            
            if (dataToggle) {
                this.toggleModuleConfig(checkboxId, dataToggle);
            }
        },
        


        handleCollapseToggle: function(toggleElement) {
            var toggleId = toggleElement.attr('data-toggle');
            var configSection = $('#config-' + toggleId);
            var collapseText = toggleElement.find('.collapse-text');
            var collapseIcon = toggleElement.find('.collapse-icon');
            
            if (configSection.hasClass('collapsed')) {
                // Expand
                configSection.removeClass('collapsed');
                collapseText.text('COLLAPSE');
                collapseIcon.text('▲');
            } else {
                // Collapse
                configSection.addClass('collapsed');
                collapseText.text('EXPAND');
                collapseIcon.text('▼');
            }
        },

        handleBannerClose: function() {
            $(document).on('click', '.banner-close, #banner-close-btn', function() {
                var banner = $('#tiny-wp-modules-banner');
                banner.fadeOut(300, function() {
                    banner.remove();
                });
            });
        }
    };

    $(document).ready(function() {
        TinyWpModulesAdmin.init();
    });

})(jQuery); 