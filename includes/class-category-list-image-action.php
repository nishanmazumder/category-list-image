<?php
/**
 * Handles the action functionality of the plugin.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    wordpress
 * @subpackage category-list-image
 */

namespace CLI\Public;

/**
 * Handles the plugin's action functionality.
 *
 * Specifies the plugin name, version, and provides examples for
 * enqueueing public/admin-facing stylesheets and JavaScript files.
 *
 * @since      1.0.0
 * @author     arosh019
 */
class Category_List_Image_Action {

	use \CLI\Trait\Singleton;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Unique ID for this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $id    The ID of this class.
	 */
	private $id;

	/**
	 * Initializes the class and sets its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name  The name of the plugin.
	 * @param    string $version      The plugin's version.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->id          = $this->plugin_name . '-src';
	}

	/**
	 * Registers JavaScript and stylesheets for the admin section of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_admin_resources() {
		/**
		 * This function serves as a demonstration.
		 *
		 * An instance of this class should be passed to the run() function
		 * in Category_List_Image_Loader, where all hooks are defined.
		 *
		 * Category_List_Image_Loader will then link the defined hooks
		 * with the functions in this class.
		 */

		wp_enqueue_style( $this->id, CATEGORY_LIST_IMAGE_URL . 'assets/admin/css/category-list-image-admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->id, CATEGORY_LIST_IMAGE_URL . 'assets/admin/js/category-list-image-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->id,
			'cli_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'cli_nonce' ),
			)
		);
	}

	/**
	 * Registers JavaScript and stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_public_resources() {
		/**
		 * This function is solely for demonstration purposes.
		 *
		 * An instance of this class should be passed to the run() function
		 * in Category_List_Image_Loader, where all hooks are defined.
		 *
		 * Category_List_Image_Loader will then establish the relationship
		 * between the defined hooks and the functions in this class.
		 */

		wp_enqueue_style( $this->id, CATEGORY_LIST_IMAGE_URL . 'assets/public/css/category-list-image-public.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->id, CATEGORY_LIST_IMAGE_URL . 'assets/public/js/category-list-image-public.js', array( 'jquery' ), $this->version, false );
	}
}
