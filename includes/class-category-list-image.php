<?php
/**
 * Defines the core plugin class.
 *
 * This class includes attributes and functions used both on the
 * public-facing side of the site and in the admin area.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    wordpress
 * @subpackage category-list-image
 */

namespace CLI\Core;

use CLI\Core\Category_List_Image_I18n;
use CLI\Public\Category_List_Image_Action;
use CLI\Admin\Category_List_Image_Admin;

/**
 * The core plugin class.
 *
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the plugin's unique identifier and current version.
 *
 * @since      1.0.0
 * @author Nishan Mazumder <arosh019@gmail.com>
 */
class Category_List_Image {

	/**
	 * The loader responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Category_List_Image_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * Sets the plugin name and version for use throughout the plugin.
	 * Loads dependencies, defines the locale, and sets hooks for both
	 * the admin area and the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = CATEGORY_LIST_IMAGE_PLUGIN_NAME;
		$this->version     = CATEGORY_LIST_IMAGE_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->plugin_action();
	}

	/**
	 * Loads the required dependencies for this plugin.
	 *
	 * Includes the following files that comprise the plugin:
	 *
	 * - Category_List_Image_Loader: Manages the hooks of the plugin.
	 * - Category_List_Image_I18n: Provides internationalization functionality.
	 *
	 * Creates an instance of the loader, which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * Manage admin notcie.
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'includes/class-category-list-image-notice.php';

		/**
		 * Traits - Singleton
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'includes/traits/trait-singleton.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'includes/class-category-list-image-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'includes/class-category-list-image-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the site.
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'includes/class-category-list-image-action.php';

		/**
		 * The class responsible for defining all actions that occur in the admin.
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'admin/class-category-list-image-admin.php';

		/**
		 * Outdoor functions (request)
		 */
		require_once CATEGORY_LIST_IMAGE_PATH . 'includes/functions.php';

		/**
		 * Instance dependency for this class.
		 */
		$this->loader = Category_List_Image_Loader::get_instance();
	}

	/**
	 * Defines the locale for this plugin for internationalization.
	 *
	 * Utilizes the Category_List_Image_I18n class to set the domain and register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = Category_List_Image_I18n::get_instance();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Registers all functionality related to hooks within the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function plugin_action() {
		$notice = Category_List_Image_Notice::get_instance();
		$this->loader->add_action( 'admin_notices', $notice, 'display_notices' );

		$plugin_action = Category_List_Image_Action::get_instance( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_action, 'enqueue_admin_resources' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_action, 'enqueue_public_resources' );

		$admin_action = Category_List_Image_Admin::get_instance();

		// $this->loader->add_filter( 'manage_category_columns', $admin_action, 'cli_set_columns_image', 4 );


	}

	/**
	 * Executes the loader to run all hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Provides the plugin's name, uniquely identifying it within WordPress and defining internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Reference to the class that orchestrates the hooks within the plugin.
	 *
	 * @since     1.0.0
	 * @return    Category_List_Image_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieves the plugin's version number.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
