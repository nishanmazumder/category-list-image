<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    WordPress
 * @subpackage category-list-image
 */

namespace CLI\Admin;

use CLI\Core\Category_List_Image_Notice;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WordPress
 * @subpackage category-list-image
 * @author     arosh019
 */
class Category_List_Image_Admin {
	use \CLI\Trait\Singleton;

	/**
	 * The ID of this plugin.
	 * Used on slug of plugin menu.
	 * Used on Root Div ID for React too.
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
	 * Notice
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $notice WP_Error object.
	 */
	private static $notice;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = CATEGORY_LIST_IMAGE_PLUGIN_NAME;
		$this->version     = CATEGORY_LIST_IMAGE_VERSION;
		self::$notice      = Category_List_Image_Notice::get_instance();

		// Admin menu added.
		add_action( 'admin_menu', array( $this, 'add_wpfmm_admin_menu' ) );

		// event category list.
		add_shortcode( 'category_list_image', array( $this, 'category_list_image_func' ) );
		// add term image.
		add_action( 'category_add_form_fields', array( $this, 'add_categor_image_field' ), 10, 2 );
		// add term image on edit.
		add_action( 'category_edit_form_fields', array( $this, 'edit_category_image_form_fields' ), 10, 2 );
		// save term image.
		add_action( 'created_category', array( $this, 'save_category_image_term_field' ), 10, 2 );
		// save term image on edit.
		add_action( 'edited_category', array( $this, 'update_image_upload' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'image_uploader_enqueue' ) );
	}

	/**
	 * Add Admin Page Menu page.
	 *
	 * @since    1.0.0
	 */
	public function add_wpfmm_admin_menu() {
		add_menu_page(
			esc_html__( 'Category Image', 'category-list-image' ),
			esc_html__( 'Category Image', 'category-list-image' ),
			'manage_options',
			$this->plugin_name,
			function () {
				// Show form list.
				echo 'done ' . esc_html( $this->plugin_name );
			},
			'dashicons-list-view'
		);
	}

	public function category_list_image_func() {

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,

			)
		);

		$taxonomy_list = '';

		if ( isset( $terms ) && ! empty( $terms ) ) {

			foreach ( $terms as $term ) {

				$upload_image = get_term_meta( $term->term_id, 'term_image', true );
				$image_url    = esc_url( $upload_image );
				$link_url     = esc_url( get_term_link( $term ) );
				$sector       = esc_html( $term->name );

				$taxonomy_list .= '<div class="gt-banner-box" style="background-image: url(\'' . $image_url . '\');">
                <a href="' . $link_url . '" target="_parent">
                    <div class="gt-content">
                    <span class="primary">' . $sector . '</span>
                    </div>
                </a>
            </div>';

			}
		} else {
			$taxonomy_list = 'No data found!';
		}

		return '<div class="bsw-event-category-grid">' . $taxonomy_list . '</div>';
	}


	public function add_categor_image_field( $taxonomy ) {
		?>
	<div class="bsw-term-image-field-container-side">
		<label for="">Upload and Image</label>
		<input type="text" name="txt_upload_image" id="txt_upload_image" value="" style="width: 77%">
		<input type="button" id="upload_image_btn" class="button" value="Upload an Image" />
	</div>
		<?php
	}

	public function save_category_image_term_field( $term_id, $tt_id ) {
		if ( isset( $_POST['txt_upload_image'] ) && '' !== $_POST['txt_upload_image'] ) {
			$group = esc_url( $_POST['txt_upload_image'] );
			add_term_meta( $term_id, 'term_image', $group, true );
		}
	}

	public function edit_category_image_form_fields( $term, $taxonomy ) {
		// get current group
		$txt_upload_image = get_term_meta( $term->term_id, 'term_image', true );
		?>
	<div class="bsw-term-image-field-container">
		<label for="">Upload and Image</label>
		<input type="text" name="txt_upload_image" id="txt_upload_image" value="<?php echo $txt_upload_image; ?>" style="width: 77%">
		<input type="button" id="upload_image_btn" class="button" value="Upload an Image" />
	</div>
		<?php
	}

	public function update_image_upload( $term_id, $tt_id ) {
		if ( isset( $_POST['txt_upload_image'] ) && '' !== $_POST['txt_upload_image'] ) {
			$group = esc_url( $_POST['txt_upload_image'] );
			update_term_meta( $term_id, 'term_image', $group );
		}
	}

	// enque and localize script
	public function image_uploader_enqueue( $hook_suffix ) {
		// global $typenow;
		if ( $hook_suffix === 'edit-tags.php' || $hook_suffix === 'term.php' ) {
			wp_enqueue_media();

			wp_register_script( 'meta-image', CATEGORY_LIST_IMAGE_URL . '/assets/admin/js/media-uploader.js', array( 'jquery' ) );

			wp_localize_script(
				'meta-image',
				'meta_image',
				array(
					'title'  => 'Upload an Image',
					'button' => 'Use this Image',
				)
			);

			wp_enqueue_script( 'meta-image' );
		}
	}
}
