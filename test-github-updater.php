<?php
/**
 * Test script for GitHubUpdater
 * Run this in your WordPress environment to test the GitHubUpdater functionality
 */

// Include WordPress
require_once( dirname( __FILE__ ) . '/../../../wp-load.php' );

// Check if GitHubUpdater class exists
if ( ! class_exists( 'TinyWpModules\\Admin\\GitHubUpdater' ) ) {
    echo "❌ GitHubUpdater class not found!\n";
    exit;
}

echo "🧪 Testing GitHubUpdater...\n\n";

// Create GitHubUpdater instance
$updater = new TinyWpModules\Admin\GitHubUpdater( __FILE__ );

// Get GitHub information
$github_info = $updater->getGitHubInfo();

echo "📋 GitHub Information:\n";
echo "Plugin URL: " . ($github_info['plugin_url'] ?: '❌ EMPTY') . "\n";
echo "GitHub URL: " . ($github_info['url'] ?: '❌ EMPTY') . "\n";
echo "GitHub Path: " . ($github_info['path'] ?: '❌ EMPTY') . "\n";
echo "GitHub Org: " . ($github_info['org'] ?: '❌ EMPTY') . "\n";
echo "GitHub Repo: " . ($github_info['repo'] ?: '❌ EMPTY') . "\n";
echo "GitHub Branch: " . ($github_info['branch'] ?: '❌ EMPTY') . "\n";
echo "Plugin Version: " . ($github_info['plugin_version'] ?: '❌ EMPTY') . "\n\n";

// Check if configured
if ( $updater->isGitHubConfigured() ) {
    echo "✅ GitHub is properly configured!\n";
} else {
    echo "❌ GitHub is NOT configured!\n";
}

// Test GitHub connection
echo "\n🔗 Testing GitHub API connection...\n";
$connection_test = $updater->testGitHubConnection();

if ( $connection_test['success'] ) {
    echo "✅ GitHub connection successful!\n";
    echo "Repository: " . $connection_test['repository_name'] . "\n";
    echo "Description: " . $connection_test['description'] . "\n";
    echo "URL: " . $connection_test['html_url'] . "\n";
} else {
    echo "❌ GitHub connection failed!\n";
    echo "Error: " . $connection_test['error'] . "\n";
}

echo "\n🎯 Test completed!\n";
