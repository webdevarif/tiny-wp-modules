/**
 * Elementor Widgets JavaScript
 *
 * @package TinyWpModules
 */

(function($) {
    'use strict';

    /**
     * Tiny FAQ Widget Handler
     */
    var TinyFAQWidget = {
        
        /**
         * Initialize FAQ functionality
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Category selection
            $(document).on('click', '.category-item', function(e) {
                e.preventDefault();
                TinyFAQWidget.selectCategory($(this));
            });

            // FAQ toggle
            $(document).on('click', '.tiny-faq-question', function(e) {
                e.preventDefault();
                TinyFAQWidget.toggleFAQ($(this));
            });

            // Keyboard navigation for categories
            $(document).on('keydown', '.category-item', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    TinyFAQWidget.selectCategory($(this));
                }
            });

            // Keyboard navigation for FAQs
            $(document).on('keydown', '.tiny-faq-question', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    TinyFAQWidget.toggleFAQ($(this));
                }
            });
        },

        /**
         * Select category and load FAQs
         *
         * @param {jQuery} categoryElement Category element
         */
        selectCategory: function(categoryElement) {
            var categoryId = categoryElement.data('category-id');
            var categoryName = categoryElement.find('.category-name').text();
            var widgetContainer = categoryElement.closest('.tiny-faq-widget');

            // Update active state
            widgetContainer.find('.category-item').removeClass('active');
            categoryElement.addClass('active');

            // Update category title
            widgetContainer.find('.selected-category-title').text(categoryName);

            // Show loading state
            var faqList = widgetContainer.find('#tiny-faq-list');
            faqList.html('<div class="faq-loading"><p>Loading FAQs...</p></div>');

            // Load FAQs via AJAX
            this.loadFAQs(categoryId, faqList);
        },

        /**
         * Load FAQs for selected category
         *
         * @param {string} categoryId Category ID
         * @param {jQuery} container Container element
         */
        loadFAQs: function(categoryId, container) {
            $.ajax({
                url: tiny_faq_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_faqs_by_category',
                    category_id: categoryId,
                    nonce: tiny_faq_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        container.html(response.data.html);
                    } else {
                        container.html('<div class="tiny-faq-empty"><p>' + (response.data.message || 'No FAQs found.') + '</p></div>');
                    }
                },
                error: function() {
                    container.html('<div class="tiny-faq-empty"><p>Error loading FAQs. Please try again.</p></div>');
                }
            });
        },

        /**
         * Toggle FAQ item
         *
         * @param {jQuery} questionElement Question element
         */
        toggleFAQ: function(questionElement) {
            var faqItem = questionElement.closest('.tiny-faq-item');
            var answerElement = faqItem.find('.tiny-faq-answer');
            var toggleIcon = questionElement.find('.toggle-icon');
            var isActive = questionElement.hasClass('active');

            // Close all other FAQ items
            $('.tiny-faq-question').not(questionElement).removeClass('active');
            $('.tiny-faq-answer').not(answerElement).removeClass('active');

            // Toggle current item
            if (isActive) {
                questionElement.removeClass('active');
                answerElement.removeClass('active');
            } else {
                questionElement.addClass('active');
                answerElement.addClass('active');
            }

            // Trigger custom event for Elementor
            $(document).trigger('tiny_faq_toggled', [faqItem, isActive]);
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        TinyFAQWidget.init();
    });

    /**
     * Elementor Frontend Integration
     */
    if (typeof elementorFrontend !== 'undefined') {
        elementorFrontend.hooks.addAction('frontend/element_ready/tiny-faq.default', function($scope) {
            // Re-initialize FAQ functionality for Elementor widgets
            setTimeout(function() {
                TinyFAQWidget.init();
            }, 100);
        });
    }

})(jQuery); 