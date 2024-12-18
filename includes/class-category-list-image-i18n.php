<?php
/**
 * Implements internationalization functionality.
 *
 * Loads and sets up the internationalization files for this plugin,
 * making it translation-ready.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    wordpress
 * @subpackage category-list-image
 */

namespace CLI\Core;

/**
 * Implements the internationalization functionality.
 *
 * Loads and configures the internationalization files for this plugin,
 * preparing it for translation.
 *
 * @since      1.0.0
 * @package    wordpress
 * @subpackage category-list-image
 * @author arosh019
 */
class Category_List_Image_I18n {

	use \CLI\Trait\Singleton;

	/**
	 * Initialize the class
	 *
	 * @since    1.0.0
	 */
	public function __construct() {}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'category-list-image',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
