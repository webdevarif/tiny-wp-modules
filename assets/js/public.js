/**
 * Tiny WP Modules Public JavaScript
 * Vanilla JavaScript with OOP style for reusability
 */

(function() {
	'use strict';

    /**
     * Base Component Class
     */
    class Component {
        constructor(selector) {
            this.element = document.querySelector(selector);
            this.elements = document.querySelectorAll(selector);
        }

        exists() {
            return this.element !== null;
        }

        addEvent(event, callback) {
            if (this.element) {
                this.element.addEventListener(event, callback);
            }
        }

        addEvents(events) {
            if (this.element) {
                events.forEach(({ event, callback }) => {
                    this.element.addEventListener(event, callback);
                });
            }
        }
    }

    /**
     * FAQ Module for Frontend
     */
    class FAQModule extends Component {
        constructor(selector) {
            super(selector);
            this.init();
        }

        init() {
            if (!this.exists()) return;

            this.setupAccordion();
            this.setupSearch();
        }

        setupAccordion() {
            const faqItems = this.element.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                
                if (question && answer) {
                    question.addEventListener('click', () => {
                        this.toggleAnswer(item, answer);
                    });
                }
            });
        }

        toggleAnswer(item, answer) {
            const isOpen = item.classList.contains('active');
            
            // Close all other items
            this.element.querySelectorAll('.faq-item').forEach(faqItem => {
                faqItem.classList.remove('active');
                const faqAnswer = faqItem.querySelector('.faq-answer');
                if (faqAnswer) {
                    faqAnswer.style.maxHeight = '0';
                }
            });

            // Toggle current item
            if (!isOpen) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        }

        setupSearch() {
            const searchInput = this.element.querySelector('.faq-search');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.filterFAQs(e.target.value);
                });
            }
        }

        filterFAQs(searchTerm) {
            const faqItems = this.element.querySelectorAll('.faq-item');
            const searchLower = searchTerm.toLowerCase();

            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                
                if (question && answer) {
                    const questionText = question.textContent.toLowerCase();
                    const answerText = answer.textContent.toLowerCase();
                    
                    const matches = questionText.includes(searchLower) || answerText.includes(searchLower);
                    
                    item.style.display = matches ? 'block' : 'none';
                }
            });
        }
    }

    /**
     * Modal Component
     */
    class Modal extends Component {
        constructor(selector) {
            super(selector);
            this.isOpen = false;
            this.init();
        }

        init() {
            if (!this.exists()) return;

            this.setupEventListeners();
        }

        setupEventListeners() {
            // Open modal triggers
            document.addEventListener('click', (e) => {
                if (e.target.matches('[data-modal-target]')) {
			e.preventDefault();
                    const targetId = e.target.getAttribute('data-modal-target');
                    this.openModal(targetId);
                }
            });

            // Close modal triggers
            document.addEventListener('click', (e) => {
                if (e.target.matches('[data-modal-close]') || e.target.classList.contains('modal-overlay')) {
                    e.preventDefault();
                    this.closeModal();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.closeModal();
                }
            });
        }

        openModal(targetId) {
            const modal = document.getElementById(targetId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                this.isOpen = true;
            }
        }

        closeModal() {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                activeModal.classList.remove('active');
                document.body.style.overflow = '';
                this.isOpen = false;
            }
        }
    }

    /**
     * Notification Manager for Frontend
     */
    class NotificationManager {
        constructor() {
            this.notifications = [];
        }

        show(message, type = 'info', duration = 5000) {
            const notification = this.createNotification(message, type);
            document.body.appendChild(notification);

            // Auto remove after duration
            setTimeout(() => {
                this.remove(notification);
            }, duration);

            this.notifications.push(notification);
            return notification;
        }

        createNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `tiny-notification tiny-notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-message">${message}</span>
                    <button type="button" class="notification-close">&times;</button>
                </div>
            `;

            // Add dismiss functionality
            const dismissBtn = notification.querySelector('.notification-close');
            if (dismissBtn) {
                dismissBtn.addEventListener('click', () => this.remove(notification));
            }

            return notification;
        }

        remove(notification) {
            if (notification && notification.parentNode) {
                notification.parentNode.removeChild(notification);
                this.notifications = this.notifications.filter(n => n !== notification);
            }
        }

        clearAll() {
            this.notifications.forEach(notification => this.remove(notification));
        }
    }

    /**
     * Utility Functions
     */
    class Utils {
        static debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        static throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }

        static isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
    }

    /**
     * Main Public App
     */
    class PublicApp {
        constructor() {
            this.components = new Map();
            this.notificationManager = new NotificationManager();
            this.init();
        }

        init() {
            this.initializeComponents();
            this.setupGlobalEventListeners();
        }

        initializeComponents() {
            // Initialize FAQ Module
            if (document.querySelector('.tiny-faq-module')) {
                this.components.set('faq', new FAQModule('.tiny-faq-module'));
            }

            // Initialize Modal System
            if (document.querySelector('[data-modal-target]')) {
                this.components.set('modal', new Modal('.modal'));
            }
        }

        setupGlobalEventListeners() {
            // Handle window resize
            window.addEventListener('resize', Utils.debounce(() => {
                this.handleResize();
            }, 250));

            // Handle scroll events
            window.addEventListener('scroll', Utils.throttle(() => {
                this.handleScroll();
            }, 100));

            // Handle form submissions
            document.addEventListener('submit', (e) => {
                if (e.target.matches('.tiny-form')) {
                    this.handleFormSubmission(e);
                }
            });
        }

        handleResize() {
            // Handle responsive behavior
            const isMobile = window.innerWidth < 768;
            document.body.classList.toggle('mobile-view', isMobile);
        }

        handleScroll() {
            // Handle scroll-based animations
            const elements = document.querySelectorAll('[data-scroll-animate]');
            elements.forEach(element => {
                if (Utils.isElementInViewport(element)) {
                    element.classList.add('animate-in');
                }
            });
        }

        async handleFormSubmission(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    this.notificationManager.show('Form submitted successfully!', 'success');
                    form.reset();
                } else {
                    this.notificationManager.show('Error submitting form. Please try again.', 'error');
                }
            } catch (error) {
                this.notificationManager.show('Error submitting form. Please try again.', 'error');
            }
        }

        getComponent(name) {
            return this.components.get(name);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.tinyWpModulesPublic = new PublicApp();
        });
    } else {
        window.tinyWpModulesPublic = new PublicApp();
    }

})(); 