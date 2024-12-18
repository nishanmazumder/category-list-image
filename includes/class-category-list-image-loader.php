<?php
/**
 * Registers all actions and filters for the plugin.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    wordpress
 * @subpackage category-list-image
 */

namespace CLI\Core;

/**
 * Registers all actions and filters for the plugin.
 *
 * Keeps track of all hooks registered within the plugin,
 * and registers them with the WordPress API. Calls the
 * run function to execute the list of actions and filters.
 *
 * @since      1.0.0
 * @authored_by Nishan Mazumder <arosh019@gmail.com>
 */
class Category_List_Image_Loader {

	use \CLI\Trait\Singleton;

	/**
	 * The list of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    Actions registered with WordPress to execute when the plugin loads.
	 */
	protected $actions;

	/**
	 * The list of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    Filters registered with WordPress to trigger when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initializes the collections to manage actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Adds a new action to the collection for registration with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress action being registered.
	 * @param    object $component        A reference to the instance of the object where the action is defined.
	 * @param    string $callback         The name of the function defined on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be executed. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments to pass to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Adds a new filter to the collection for registration with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress filter being registered.
	 * @param    object $component        A reference to the instance of the object where the filter is defined.
	 * @param    string $callback         The name of the function defined on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be executed. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments to pass to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Utility function for registering actions and hooks into a single collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array  $hooks            Collection of hooks being registered (actions or filters).
	 * @param    string $hook             Name of the WordPress filter being registered.
	 * @param    object $component        Reference to the instance of the object defining the filter.
	 * @param    string $callback         Name of the function defined on the $component.
	 * @param    int    $priority         Priority at which the function should be executed.
	 * @param    int    $accepted_args    Number of arguments to pass to the $callback.
	 * @return   array                    Collection of actions and filters registered with WordPress.
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
	 * Registers the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}
