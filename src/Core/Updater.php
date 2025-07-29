<?php
/**
 * Plugin Updater
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Plugin Updater Class
 */
class Updater {

	/**
	 * GitHub repository information
	 */
	private $github_repo = 'webdevarif/tiny-wp-modules';
	private $github_api_url = 'https://api.github.com/repos/';
	private $github_download_url = 'https://github.com/';

	/**
	 * Plugin information
	 */
	private $plugin_slug;
	private $plugin_file;
	private $plugin_name;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_slug = 'tiny-wp-modules';
		$this->plugin_file = TINY_WP_MODULES_PLUGIN_FILE;
		$this->plugin_name = 'Tiny WP Modules';

		// Add update hooks
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		add_filter( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 3 );
		
		// Add plugin row meta links
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_plugin_row_scripts' ) );
		add_action( 'wp_ajax_check_plugin_updates', array( $this, 'ajax_check_updates' ) );
	}

	/**
	 * Check for updates from GitHub
	 *
	 * @param object $transient WordPress update transient.
	 * @return object Modified transient.
	 */
	public function check_for_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get current version
		$current_version = TINY_WP_MODULES_VERSION;

		// Get latest version from GitHub
		$latest_version = $this->get_latest_version();

		if ( $latest_version && version_compare( $current_version, $latest_version, '<' ) ) {
			$plugin_data = get_plugin_data( $this->plugin_file );

			$transient->response[ $this->plugin_slug . '/' . $this->plugin_slug . '.php' ] = (object) array(
				'slug'          => $this->plugin_slug,
				'new_version'   => $latest_version,
				'url'           => $this->github_download_url . $this->github_repo,
				'package'       => $this->get_download_url( $latest_version ),
				'requires'      => $plugin_data['RequiresWP'] ?? '5.0',
				'requires_php'  => $plugin_data['RequiresPHP'] ?? '7.4',
				'tested'        => $plugin_data['Tested up to'] ?? '6.4',
			);
		}

		return $transient;
	}

	/**
	 * Get plugin information for the WordPress update system
	 *
	 * @param object $result Plugin information.
	 * @param string $action Action being performed.
	 * @param object $args Additional arguments.
	 * @return object Plugin information.
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( $this->plugin_slug !== $args->slug ) {
			return $result;
		}

		// Get plugin information from GitHub
		$plugin_info = $this->get_plugin_info();

		if ( $plugin_info ) {
			$result = (object) array(
				'name'          => $this->plugin_name,
				'slug'          => $this->plugin_slug,
				'version'       => $plugin_info['version'],
				'author'        => $plugin_info['author'],
				'author_profile' => $plugin_info['author_profile'],
				'last_updated'  => $plugin_info['last_updated'],
				'homepage'      => $plugin_info['homepage'],
				'sections'      => array(
					'description' => $plugin_info['description'],
					'changelog'   => $plugin_info['changelog'],
				),
				'download_link' => $this->get_download_url( $plugin_info['version'] ),
				'requires'      => $plugin_info['requires'],
				'requires_php'  => $plugin_info['requires_php'],
				'tested'        => $plugin_info['tested'],
			);
		}

		return $result;
	}

	/**
	 * Get latest version from GitHub
	 *
	 * @return string|false Latest version or false on failure.
	 */
	public function get_latest_version() {
		$api_url = $this->github_api_url . $this->github_repo . '/releases/latest';
		$response = $this->make_github_request( $api_url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! empty( $data['tag_name'] ) ) {
			$version = ltrim( $data['tag_name'], 'v' );
			return $version;
		}

		return false;
	}

	/**
	 * Get plugin information from GitHub
	 *
	 * @return array|false Plugin information or false on failure.
	 */
	private function get_plugin_info() {
		$api_url = $this->github_api_url . $this->github_repo . '/releases/latest';
		$response = $this->make_github_request( $api_url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! empty( $data ) ) {
			$info = array(
				'version'        => ltrim( $data['tag_name'], 'v' ),
				'author'         => 'Digital Farmers',
				'author_profile' => 'https://digitalfarmers.com',
				'last_updated'   => $data['published_at'],
				'homepage'       => $this->github_download_url . $this->github_repo,
				'description'    => $data['body'] ?? 'A modular WordPress plugin with OOP architecture and Composer autoloading.',
				'changelog'      => $data['body'] ?? 'No changelog available.',
				'requires'       => '5.0',
				'requires_php'   => '7.4',
				'tested'         => '6.4',
			);

			return $info;
		}

		return false;
	}

	/**
	 * Get download URL for a specific version
	 *
	 * @param string $version Version to download.
	 * @return string Download URL.
	 */
	private function get_download_url( $version ) {
		return $this->github_download_url . $this->github_repo . '/archive/refs/tags/v' . $version . '.zip';
	}

	/**
	 * Make request to GitHub API
	 *
	 * @param string $url API URL.
	 * @return WP_Error|array Response or error.
	 */
	private function make_github_request( $url ) {
		$args = array(
			'headers' => array(
				'User-Agent' => 'Tiny-WP-Modules-Plugin',
			),
		);

		return wp_remote_get( $url, $args );
	}

	/**
	 * Handle post-installation tasks
	 *
	 * @param bool  $response Installation response.
	 * @param array $hook_extra Extra arguments.
	 * @param array $result Installation result.
	 * @return bool Response.
	 */
	public function upgrader_post_install( $response, $hook_extra, $result ) {
		if ( ! isset( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== $this->plugin_slug . '/' . $this->plugin_slug . '.php' ) {
			return $response;
		}

		return $response;
	}

	/**
	 * Add plugin row meta links
	 *
	 * @param array  $links Plugin row meta links.
	 * @param string $file  Plugin file.
	 * @return array Modified links.
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( $this->plugin_file ) === $file ) {
			$links[] = sprintf(
				'<a href="#" class="check-updates-link" data-plugin="%s" title="%s">%s</a>',
				esc_attr( $this->plugin_slug ),
				esc_attr__( 'Check for updates from GitHub repository', 'tiny-wp-modules' ),
				esc_html__( 'Check for Updates', 'tiny-wp-modules' )
			);
		}
		return $links;
	}

	/**
	 * Enqueue scripts for plugin row
	 */
	public function enqueue_plugin_row_scripts() {
		$screen = get_current_screen();
		if ( $screen && 'plugins' === $screen->id ) {
			wp_enqueue_script(
				'tiny-wp-modules-plugin-row',
				TINY_WP_MODULES_PLUGIN_URL . 'assets/js/plugin-row.js',
				array( 'jquery' ),
				TINY_WP_MODULES_VERSION,
				true
			);

			wp_localize_script(
				'tiny-wp-modules-plugin-row',
				'tiny_wp_modules_plugin_row',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'tiny_wp_modules_plugin_row_nonce' ),
					'loading'  => __( 'Checking...', 'tiny-wp-modules' ),
					'up_to_date' => __( 'Up to date', 'tiny-wp-modules' ),
					'update_available' => __( 'Update available', 'tiny-wp-modules' ),
					'error'    => __( 'Error checking updates', 'tiny-wp-modules' ),
				)
			);
		}
	}

	/**
	 * AJAX handler for checking updates from plugin row
	 */
	public function ajax_check_updates() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'tiny_wp_modules_plugin_row_nonce' ) ) {
			wp_die( __( 'Security check failed.', 'tiny-wp-modules' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'You do not have permission to update plugins.', 'tiny-wp-modules' ) );
		}

		// Get latest version
		$latest_version = $this->get_latest_version();
		$current_version = TINY_WP_MODULES_VERSION;

		if ( $latest_version && version_compare( $current_version, $latest_version, '<' ) ) {
			wp_send_json_success( array(
				'has_update' => true,
				'current_version' => $current_version,
				'latest_version' => $latest_version,
				'message' => sprintf(
					/* translators: %1$s: current version, %2$s: latest version */
					__( 'Update available! Current version: %1$s, Latest version: %2$s', 'tiny-wp-modules' ),
					$current_version,
					$latest_version
				),
			) );
		} else {
			wp_send_json_success( array(
				'has_update' => false,
				'current_version' => $current_version,
				'latest_version' => $latest_version ?: $current_version,
				'message' => __( 'Plugin is up to date.', 'tiny-wp-modules' ),
			) );
		}
	}
} 