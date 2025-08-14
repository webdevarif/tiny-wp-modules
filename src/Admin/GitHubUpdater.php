<?php
/**
 * GitHub Updater Class
 *
 * Enables WordPress to automatically update this plugin from GitHub
 * Based on implementation by Ryan Sechrest
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

/**
 * GitHub Updater Class
 * 
 * This class integrates with WordPress's built-in update system to enable
 * automatic updates from GitHub repositories.
 */
class GitHubUpdater {

	/**
	 * Plugin file path
	 *
	 * @var string
	 */
	private string $file = '';

	/**
	 * GitHub repository information
	 *
	 * @var string
	 */
	private string $gitHubUrl = '';
	private string $gitHubPath = '';
	private string $gitHubOrg = '';
	private string $gitHubRepo = '';
	private string $gitHubBranch = 'main';
	private string $gitHubAccessToken = '';

	/**
	 * Plugin information
	 *
	 * @var string
	 */
	private string $pluginFile = '';
	private string $pluginDir = '';
	private string $pluginFilename = '';
	private string $pluginSlug = '';
	private string $pluginUrl = '';
	private string $pluginVersion = '';

	/**
	 * WordPress compatibility
	 *
	 * @var string
	 */
	private string $testedWpVersion = '';

	/**
	 * Constructor
	 *
	 * @param string $file Absolute path to the root plugin file.
	 */
	public function __construct( string $file ) {
		$this->file = $file;
		$this->loadPluginData();
		$this->parseGitHubUrl();
		
		// Log initial state for debugging
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater Constructor: Initialized with file: ' . $file );
			error_log( 'GitHubUpdater Constructor: Plugin URL from header: ' . $this->pluginUrl );
			error_log( 'GitHubUpdater Constructor: GitHub configured: ' . ( $this->isGitHubConfigured() ? 'YES' : 'NO' ) );
		}
	}

	/**
	 * Load plugin data from plugin header
	 */
	private function loadPluginData(): void {
		$pluginData = get_plugin_data( $this->file );
		
		$this->pluginFile = plugin_basename( $this->file );
		$this->pluginDir = dirname( $this->pluginFile );
		$this->pluginFilename = basename( $this->file );
		$this->pluginSlug = $this->pluginDir;
		$this->pluginUrl = $pluginData['PluginURI'] ?? '';
		$this->pluginVersion = $pluginData['Version'] ?? '';
		$this->testedWpVersion = $pluginData['Tested up to'] ?? '';
	}

	/**
	 * Parse GitHub URL from plugin header
	 */
	private function parseGitHubUrl(): void {
		if ( empty( $this->pluginUrl ) || strpos( $this->pluginUrl, 'github.com' ) === false ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'GitHubUpdater Error: Plugin URL is empty or not a GitHub URL: ' . $this->pluginUrl );
			}
			return;
		}

		$this->gitHubUrl = $this->pluginUrl;
		
		// Parse GitHub URL: https://github.com/username/repository
		$path = parse_url( $this->gitHubUrl, PHP_URL_PATH );
		if ( $path ) {
			$pathParts = explode( '/', trim( $path, '/' ) );
			if ( count( $pathParts ) >= 2 ) {
				$this->gitHubOrg = $pathParts[0];
				$this->gitHubRepo = $pathParts[1];
				$this->gitHubPath = $this->gitHubOrg . '/' . $this->gitHubRepo;
				
				// Validate that we have valid GitHub information
				if ( ! empty( $this->gitHubOrg ) && ! empty( $this->gitHubRepo ) ) {
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( 'GitHubUpdater Success: Parsed GitHub URL successfully' );
						error_log( 'GitHubUpdater Debug: Plugin URL: ' . $this->pluginUrl );
						error_log( 'GitHubUpdater Debug: GitHub Path: ' . $this->gitHubPath );
						error_log( 'GitHubUpdater Debug: GitHub Org: ' . $this->gitHubOrg );
						error_log( 'GitHubUpdater Debug: GitHub Repo: ' . $this->gitHubRepo );
					}
				} else {
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( 'GitHubUpdater Error: Failed to parse GitHub organization or repository from URL' );
					}
				}
			} else {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'GitHubUpdater Error: GitHub URL format invalid. Expected: https://github.com/username/repository' );
				}
			}
		} else {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'GitHubUpdater Error: Could not parse URL path from: ' . $this->gitHubUrl );
			}
		}
	}

	/**
	 * Set GitHub branch
	 *
	 * @param string $branch Branch name.
	 * @return self
	 */
	public function setBranch( string $branch ): self {
		$this->gitHubBranch = $branch;
		return $this;
	}

	/**
	 * Set GitHub access token for private repositories
	 *
	 * @param string $accessToken GitHub personal access token.
	 * @return self
	 */
	public function setAccessToken( string $accessToken ): self {
		$this->gitHubAccessToken = $accessToken;
		return $this;
	}

	/**
	 * Set tested WordPress version
	 *
	 * @param string $version WordPress version.
	 * @return self
	 */
	public function setTestedWpVersion( string $version ): self {
		$this->testedWpVersion = $version;
		return $this;
	}

	/**
	 * Manually set GitHub repository information
	 *
	 * @param string $organization GitHub organization/username.
	 * @param string $repository GitHub repository name.
	 * @param string $branch GitHub branch (defaults to 'main').
	 * @return self
	 */
	public function setGitHubRepository( string $organization, string $repository, string $branch = 'main' ): self {
		$this->gitHubOrg = $organization;
		$this->gitHubRepo = $repository;
		$this->gitHubPath = $organization . '/' . $repository;
		$this->gitHubBranch = $branch;
		$this->gitHubUrl = 'https://github.com/' . $this->gitHubPath;
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater: Manually set GitHub repository to: ' . $this->gitHubPath );
		}
		
		return $this;
	}

	/**
	 * Check if GitHub information is properly configured
	 *
	 * @return bool True if properly configured.
	 */
	public function isGitHubConfigured(): bool {
		return ! empty( $this->gitHubPath ) && ! empty( $this->gitHubOrg ) && ! empty( $this->gitHubRepo );
	}

	/**
	 * Refresh GitHub information from plugin header
	 *
	 * @return self
	 */
	public function refreshGitHubInfo(): self {
		$this->loadPluginData();
		$this->parseGitHubUrl();
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater: Refreshed GitHub information' );
			error_log( 'GitHubUpdater Debug: Refreshed GitHub Info: ' . print_r( $this->getGitHubInfo(), true ) );
		}
		
		return $this;
	}

	/**
	 * Get current GitHub information for debugging
	 *
	 * @return array GitHub information.
	 */
	public function getGitHubInfo(): array {
		return array(
			'url' => $this->gitHubUrl,
			'path' => $this->gitHubPath,
			'org' => $this->gitHubOrg,
			'repo' => $this->gitHubRepo,
			'branch' => $this->gitHubBranch,
			'plugin_url' => $this->pluginUrl,
			'plugin_version' => $this->pluginVersion,
		);
	}

	/**
	 * Check for available updates
	 *
	 * @return array Update check results.
	 */
	public function checkForUpdates(): array {
		$latestVersion = $this->getLatestVersion();
		$currentVersion = $this->pluginVersion;
		
		if ( ! $latestVersion ) {
			return array(
				'success' => false,
				'error' => 'Could not fetch latest version from GitHub',
				'current_version' => $currentVersion,
				'latest_version' => null,
				'update_available' => false
			);
		}
		
		$updateAvailable = version_compare( $currentVersion, $latestVersion, '<' );
		
		return array(
			'success' => true,
			'current_version' => $currentVersion,
			'latest_version' => $latestVersion,
			'update_available' => $updateAvailable,
			'version_comparison' => version_compare( $currentVersion, $latestVersion, '>=' )
		);
	}

	/**
	 * Test GitHub API connection
	 *
	 * @return array Test results.
	 */
	public function testGitHubConnection(): array {
		if ( empty( $this->gitHubPath ) ) {
			return array(
				'success' => false,
				'error' => 'GitHub path not configured',
				'github_info' => $this->getGitHubInfo()
			);
		}

		$apiUrl = sprintf(
			'https://api.github.com/repos/%s',
			$this->gitHubPath
		);

		$response = wp_remote_get( $apiUrl );
		
		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'error' => 'HTTP request failed: ' . $response->get_error_message(),
				'github_info' => $this->getGitHubInfo()
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( $status_code === 200 && isset( $data['name'] ) ) {
			return array(
				'success' => true,
				'repository_name' => $data['name'],
				'description' => $data['description'] ?? 'No description',
				'html_url' => $data['html_url'],
				'github_info' => $this->getGitHubInfo()
			);
		} else {
			return array(
				'success' => false,
				'error' => 'API response error. Status: ' . $status_code . ', Response: ' . $body,
				'github_info' => $this->getGitHubInfo()
			);
		}
	}

	/**
	 * Manually trigger update check
	 */
	public function forceUpdateCheck(): void {
		// Clear any cached update data
		delete_site_transient( 'update_plugins' );
		
		// Force WordPress to check for updates
		wp_update_plugins();
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater: Forced update check triggered' );
		}
	}

	/**
	 * Add the updater to WordPress
	 */
	public function add(): void {
		// Final check - if still not configured, try to set it manually
		if ( ! $this->isGitHubConfigured() ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'GitHubUpdater Error: GitHub not configured after initialization. Plugin URL: ' . $this->pluginUrl );
				error_log( 'GitHubUpdater Debug: Current GitHub Info: ' . print_r( $this->getGitHubInfo(), true ) );
			}
			return;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater Success: Initializing updater for ' . $this->gitHubPath );
			error_log( 'GitHubUpdater Debug: Final GitHub Info: ' . print_r( $this->getGitHubInfo(), true ) );
		}

		$this->prepareAdminNotices();
		$this->preparePluginDetailLinks();
		$this->prepareUpdateResponse();
		$this->prepareHttpRequestArgs();
		$this->moveUpdatedPlugin();
	}

	/**
	 * Prepare admin notices for missing plugin header fields
	 */
	private function prepareAdminNotices(): void {
		add_action( 'admin_notices', array( $this, '_adminNotices' ) );
	}

	/**
	 * Display admin notice if required plugin header fields are missing
	 */
	public function _adminNotices(): void {
		$plugin_name = get_plugin_data( $this->file )['Name'] ?? 'This plugin';
		
		// Check if Plugin URI is missing or not a GitHub URL
		if ( empty( $this->pluginUrl ) ) {
			$message = sprintf(
				'<strong>%s</strong> requires a <code>Plugin URI</code> header field pointing to a GitHub repository to enable automatic updates.',
				esc_html( $plugin_name )
			);
			echo '<div class="notice notice-error"><p>' . wp_kses_post( $message ) . '</p></div>';
			return;
		}

		// Check if it's a GitHub URL
		if ( strpos( $this->pluginUrl, 'github.com' ) === false ) {
			$message = sprintf(
				'<strong>%s</strong> requires the <code>Plugin URI</code> header field to point to a GitHub repository (e.g., https://github.com/username/repository).',
				esc_html( $plugin_name )
			);
			echo '<div class="notice notice-error"><p>' . wp_kses_post( $message ) . '</p></div>';
			return;
		}

		// Check if Version is missing
		if ( empty( $this->pluginVersion ) ) {
			$message = sprintf(
				'<strong>%s</strong> requires a <code>Version</code> header field to enable automatic updates from GitHub.',
				esc_html( $plugin_name )
			);
			echo '<div class="notice notice-error"><p>' . wp_kses_post( $message ) . '</p></div>';
			return;
		}

		// Check if GitHub information was parsed correctly
		if ( empty( $this->gitHubPath ) ) {
			$message = sprintf(
				'<strong>%s</strong> GitHub repository information could not be parsed from the Plugin URI. Please check the format: https://github.com/username/repository',
				esc_html( $plugin_name )
			);
			echo '<div class="notice notice-error"><p>' . wp_kses_post( $message ) . '</p></div>';
			return;
		}
	}

	/**
	 * Prepare plugin detail links to point to GitHub
	 */
	private function preparePluginDetailLinks(): void {
		add_filter( 'admin_url', array( $this, '_adminUrl' ), 10, 2 );
	}

	/**
	 * Replace plugin detail links with GitHub links
	 *
	 * @param string $url Admin URL.
	 * @param string $path Admin path.
	 * @return string Modified admin URL.
	 */
	public function _adminUrl( string $url, string $path ): string {
		if ( $path !== 'plugin-install.php?tab=plugin-information&plugin=' . $this->pluginSlug ) {
			return $url;
		}

		return $this->gitHubUrl;
	}

	/**
	 * Prepare update response for WordPress
	 */
	private function prepareUpdateResponse(): void {
		add_filter( 'update_plugins_' . parse_url( $this->gitHubUrl, PHP_URL_HOST ), array( $this, '_updateResponse' ) );
	}

	/**
	 * Build update response for WordPress
	 *
	 * @param mixed $response WordPress response.
	 * @return array Update response.
	 */
	public function _updateResponse( $response ): array {
		if ( ! empty( $response ) ) {
			return $response;
		}

		$latestVersion = $this->getLatestVersion();
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater Debug: Current plugin version: ' . $this->pluginVersion );
			error_log( 'GitHubUpdater Debug: Latest GitHub version: ' . $latestVersion );
			error_log( 'GitHubUpdater Debug: Version comparison result: ' . version_compare( $this->pluginVersion, $latestVersion, '>=' ) );
		}
		
		if ( ! $latestVersion || version_compare( $this->pluginVersion, $latestVersion, '>=' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'GitHubUpdater Debug: No update needed or version comparison failed' );
			}
			return array();
		}

		return array(
			$this->pluginFile => (object) array(
				'id'          => $this->gitHubUrl,
				'slug'        => $this->pluginSlug,
				'plugin'      => $this->pluginFile,
				'version'     => $latestVersion,
				'url'         => $this->gitHubUrl,
				'package'     => $this->getPrivateRemotePluginZipFile(),
				'icons'       => array(),
				'tested'      => $this->testedWpVersion,
				'new_version' => $latestVersion,
			),
		);
	}

	/**
	 * Get latest version from GitHub
	 *
	 * @return string|false Latest version or false on failure.
	 */
	private function getLatestVersion() {
		$apiUrl = sprintf(
			'https://api.github.com/repos/%s/releases/latest',
			$this->gitHubPath
		);

		$response = wp_remote_get( $apiUrl );
		if ( is_wp_error( $response ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'GitHubUpdater Error: Failed to fetch latest version: ' . $response->get_error_message() );
			}
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'GitHubUpdater Debug: API Response: ' . print_r( $data, true ) );
		}

		$tag_name = $data['tag_name'] ?? false;
		
		if ( $tag_name ) {
			// Clean the tag name to extract a valid version
			$clean_version = $this->cleanVersionFromTag( $tag_name );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'GitHubUpdater Debug: Original tag: ' . $tag_name . ', Cleaned version: ' . $clean_version );
			}
			return $clean_version;
		}

		return false;
	}

	/**
	 * Clean version string from GitHub tag
	 *
	 * @param string $tag_name GitHub tag name.
	 * @return string Cleaned version string.
	 */
	private function cleanVersionFromTag( string $tag_name ): string {
		// Remove 'v' prefix if present
		$version = ltrim( $tag_name, 'v' );
		
		// Remove common suffixes like -tag, -release, -beta, etc.
		$version = preg_replace( '/-(tag|release|beta|alpha|rc|dev).*$/i', '', $version );
		
		// Ensure it's a valid semantic version format
		if ( preg_match( '/^\d+\.\d+(\.\d+)?/', $version ) ) {
			return $version;
		}
		
		// If still not valid, try to extract just the numbers
		if ( preg_match( '/(\d+)\.(\d+)(?:\.(\d+))?/', $version, $matches ) ) {
			$major = $matches[1];
			$minor = $matches[2];
			$patch = isset( $matches[3] ) ? $matches[3] : '0';
			return $major . '.' . $minor . '.' . $patch;
		}
		
		// Fallback: return original tag name
		return $tag_name;
	}

	/**
	 * Get ZIP file URL for GitHub repository
	 *
	 * @return string ZIP file URL.
	 */
	private function getPrivateRemotePluginZipFile(): string {
		return sprintf(
			'https://api.github.com/repos/%s/zipball/%s',
			$this->gitHubPath,
			$this->gitHubBranch
		);
	}

	/**
	 * Prepare HTTP request arguments for GitHub API
	 */
	private function prepareHttpRequestArgs(): void {
		if ( empty( $this->gitHubAccessToken ) ) {
			return;
		}

		add_filter( 'http_request_args', array( $this, '_prepareHttpRequestArgs' ), 10, 2 );
	}

	/**
	 * Add authorization header for GitHub API requests
	 *
	 * @param array  $args HTTP request arguments.
	 * @param string $url Request URL.
	 * @return array Modified HTTP request arguments.
	 */
	public function _prepareHttpRequestArgs( array $args, string $url ): array {
		if ( $url !== $this->getPrivateRemotePluginZipFile() ) {
			return $args;
		}

		$args['headers']['Authorization'] = 'Bearer ' . $this->gitHubAccessToken;
		$args['headers']['Accept'] = 'application/vnd.github+json';

		return $args;
	}

	/**
	 * Move updated plugin to correct location
	 */
	private function moveUpdatedPlugin(): void {
		add_filter( 'upgrader_install_package_result', array( $this, '_moveUpdatedPlugin' ) );
	}

	/**
	 * Move new plugin to where old plugin was located
	 *
	 * @param array $result Installation result.
	 * @return array Modified installation result.
	 */
	public function _moveUpdatedPlugin( array $result ): array {
		$newPluginPath = $result['destination'] ?? '';

		if ( ! $newPluginPath ) {
			return $result;
		}

		$pluginRootPath = $result['local_destination'] ?? WP_PLUGIN_DIR;
		$oldPluginPath = $pluginRootPath . '/' . $this->pluginDir;

		// Move the new plugin to the old plugin location
		if ( function_exists( 'move_dir' ) ) {
			move_dir( $newPluginPath, $oldPluginPath );
		} else {
			// Fallback for older WordPress versions
			rename( $newPluginPath, $oldPluginPath );
		}

		$result['destination'] = $oldPluginPath;
		$result['destination_name'] = $this->pluginDir;
		$result['remote_destination'] = $oldPluginPath;

		return $result;
	}
}
