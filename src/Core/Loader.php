<?php
/**
 * Hook Loader Class
 *
 * @package TinyWpModules\Core
 */

namespace TinyWpModules\Core;

/**
 * Register all hooks and filters
 */
class Loader {

	/**
	 * Array of actions to register
	 *
	 * @var array
	 */
	protected $actions;

	/**
	 * Array of filters to register
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection
	 *
	 * @param string   $hook          The name of the WordPress action.
	 * @param object   $component     A reference to the instance of the object on which the action is defined.
	 * @param string   $callback      The name of the function definition on the $component.
	 * @param int      $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection
	 *
	 * @param string   $hook          The name of the WordPress filter.
	 * @param object   $component     A reference to the instance of the object on which the filter is defined.
	 * @param string   $callback      The name of the function definition on the $component.
	 * @param int      $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @param array    $hooks         The collection of hooks that is being registered.
	 * @param string   $hook          The name of the WordPress filter that is being registered.
	 * @param object   $component     A reference to the instance of the object on which the filter is defined.
	 * @param string   $callback      The name of the function definition on the $component.
	 * @param int      $priority      The priority at which the function should be fired.
	 * @param int      $accepted_args The number of arguments that should be passed to the $callback.
	 * @return array The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}

	/**
	 * Get the number of actions
	 *
	 * @return int
	 */
	public function get_action_count() {
		return count( $this->actions );
	}

	/**
	 * Get the number of filters
	 *
	 * @return int
	 */
	public function get_filter_count() {
		return count( $this->filters );
	}
} 