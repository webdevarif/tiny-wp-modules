<?php
/**
 * Elementor Tab Content Template
 *
 * @package TinyWpModules\Templates\Admin\Tabs
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import classes
use TinyWpModules\Admin\Components;
use TinyWpModules\Admin\Elementor_Manager;

// Render the Elementor tab content using Elementor_Manager
echo Elementor_Manager::render_tab_content();
?>
