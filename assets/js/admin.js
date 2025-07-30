/**
 * Tiny WP Modules Admin JavaScript
 * Organized with proper initialization pattern
 */

(function($) {
    'use strict';

    console.log('Tiny WP Modules: admin.js loaded successfully');

    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.warn('Tiny WP Modules: jQuery is not available');
        return;
    }

    /**
     * Tiny WP Modules Admin Class
     */
    var TinyWpModulesAdmin = {
        
        /**
         * Initialize the admin functionality
         */
        init: function() {
            this.hideAllConfigSections();
            this.initModuleToggles();
            this.bindEvents();
            this.handleBannerClose();
        },

        /**
         * Hide all config sections by default
         */
        hideAllConfigSections: function() {
            $('.config-fields-section').hide();
        },

        /**
         * Initialize all module toggles
         */
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

        /**
         * Bind all event listeners
         */
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
        },

        /**
         * Generic function to handle module configuration visibility
         */
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

        /**
         * Generic function to handle all module toggles
         */
        handleModuleToggle: function(checkboxId) {
            var checkbox = $('#' + checkboxId);
            var expandableModule = checkbox.closest('[data-expandable-module]');
            var dataToggle = expandableModule.attr('data-expandable-module');
            
            if (dataToggle) {
                this.toggleModuleConfig(checkboxId, dataToggle);
            } else {
                console.warn('Tiny WP Modules: No data-expandable-module found for checkbox:', checkboxId);
            }
        },

        /**
         * Handle collapse/expand toggle clicks
         */
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

        /**
         * Handle banner close button
         */
        handleBannerClose: function() {
            $(document).on('click', '.banner-close, #banner-close-btn', function() {
                var banner = $('#tiny-wp-modules-banner');
                banner.fadeOut(300, function() {
                    banner.remove();
                });
            });
        }
    };

            /**
         * Initialize when document is ready
         */
        $(document).ready(function() {
            TinyWpModulesAdmin.init();
        });

})(jQuery); 