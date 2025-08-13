/**
 * Tiny WP Modules Admin JavaScript
 * Minimal JavaScript - using Alpine.js directives in HTML instead
 */

// Initialize Alpine.js when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Alpine.js
    if (typeof Alpine !== 'undefined') {
        Alpine.start();
    }
});

/**
 * Save Elementor setting via AJAX
 */
function saveElementorSetting(enabled) {
    // Create form data
    const formData = new FormData();
    formData.append('action', 'save_elementor_setting');
    formData.append('nonce', tiny_wp_modules_ajax.nonce);
    formData.append('setting_id', 'enable_elementor');
    formData.append('setting_value', enabled ? '1' : '0');
    
    // Send AJAX request
    fetch(tiny_wp_modules_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Success - update the UI without page reload
            if (enabled) {
                // Show success message
                showNotification('Elementor support enabled successfully!', 'success');
                
                // Add Elementor tab to sidebar if it doesn't exist
                addElementorTab();
                
                // Switch to Elementor tab
                setTimeout(() => {
                    switchToTab('elementor');
                }, 500);
            } else {
                showNotification('Elementor support disabled.', 'info');
                
                // Remove Elementor tab from sidebar
                removeElementorTab();
            }
        } else {
            // Error - revert the switch
            const switchElement = document.querySelector('#enable_elementor');
            if (switchElement) {
                switchElement.checked = !enabled;
            }
            showNotification(result.data || 'Failed to save setting. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
        
        // Revert the switch on error
        const switchElement = document.querySelector('#enable_elementor');
        if (switchElement) {
            switchElement.checked = !enabled;
        }
        showNotification('Failed to save setting. Please try again.', 'error');
    });
}

/**
 * Add Elementor tab to sidebar
 */
function addElementorTab() {
    const sidebar = document.querySelector('.nav-menu');
    if (!sidebar) return;
    
    // Check if Elementor tab already exists
    if (document.querySelector('#elementor-tab-nav')) return;
    
    // Create Elementor tab
    const elementorTab = document.createElement('li');
    elementorTab.className = 'nav-menu-item';
    elementorTab.id = 'elementor-tab-nav';
    
    elementorTab.innerHTML = `
        <a href="#elementor" class="nav-menu-link" data-tab="elementor">
            <span class="nav-menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="nav-menu-label">Elementor</span>
        </a>
    `;
    
    // Add click event
    elementorTab.querySelector('.nav-menu-link').addEventListener('click', function(e) {
        e.preventDefault();
        switchToTab('elementor');
    });
    
    // Insert after General tab
    const generalTab = sidebar.querySelector('[data-tab="general"]');
    if (generalTab) {
        generalTab.parentNode.insertBefore(elementorTab, generalTab.nextSibling);
    } else {
        sidebar.appendChild(elementorTab);
    }
}

/**
 * Remove Elementor tab from sidebar
 */
function removeElementorTab() {
    const elementorTab = document.querySelector('#elementor-tab-nav');
    if (elementorTab) {
        elementorTab.remove();
    }
    
    // Switch to general tab if currently on elementor tab
    const currentTab = document.querySelector('.tab-content.active');
    if (currentTab && currentTab.id === 'elementor-tab') {
        switchToTab('general');
    }
}

/**
 * Switch to specific tab
 */
function switchToTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all nav items
    document.querySelectorAll('.nav-menu-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Show target tab content
    const targetTab = document.querySelector(`#${tabName}-tab`);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Activate target nav item
    const targetNav = document.querySelector(`[data-tab="${tabName}"]`);
    if (targetNav) {
        targetNav.parentNode.classList.add('active');
    }
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notice notice-${type} is-dismissible`;
    notification.innerHTML = `
        <p>${message}</p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    `;
    
    // Add dismiss functionality
    notification.querySelector('.notice-dismiss').addEventListener('click', function() {
        notification.remove();
    });
    
    // Insert at top of page
    const firstElement = document.querySelector('.wrap h1');
    if (firstElement) {
        firstElement.parentNode.insertBefore(notification, firstElement.nextSibling);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
} 