<?php
/**
 * Singleton
 *
 * This trait used to ensure a class has only one instance
 * and provides a global access point to it.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    wordpress
 * @subpackage category-list-image
 */

namespace CLI\Trait;

/**
 * Singleton Trait
 *
 * @return object
 */
trait Singleton {
	/**
	 * The instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $instance Instance of this class.
	 */
	private static $instance = null;

	/**
	 * Static method to get the instance of the called class.
	 */
	public static function get_instance() {
		$args = func_get_args();
		if ( null === self::$instance ) {
			$reflection     = new \ReflectionClass( __CLASS__ );
			self::$instance = $reflection->newInstanceArgs( $args );
		}
		return self::$instance;
	}
}
