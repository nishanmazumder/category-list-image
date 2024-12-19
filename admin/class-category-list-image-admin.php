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

	private $placeholder;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = CATEGORY_LIST_IMAGE_PLUGIN_NAME;
		$this->version     = CATEGORY_LIST_IMAGE_VERSION;
		self::$notice      = Category_List_Image_Notice::get_instance();

		$this->placeholder = CATEGORY_LIST_IMAGE_URL . '/assets/admin/img/cli-upload-placeholder.png';

		// Admin menu added.
		add_action( 'admin_menu', array( $this, 'add_wpfmm_admin_menu' ) );

		// Add shortcode [category_list_image].
		add_shortcode( 'category_list_image', array( $this, 'category_list_image_func' ) );

		// add term image field on top level page.
		add_action( 'category_add_form_fields', array( $this, 'add_term_image_field' ), 10, 2 );
		// add term image field on edit page.
		add_action( 'category_edit_form_fields', array( $this, 'add_term_image_field_on_edit' ), 10, 2 );
		// add term image on category create.
		add_action( 'created_category', array( $this, 'add_term_image_field_on_create' ), 10, 2 );
		// save term image on edit page.
		add_action( 'edited_category', array( $this, 'update_term_image_field_on_edit' ), 10, 2 );
		// add script for image upload.
		add_action( 'admin_enqueue_scripts', array( $this, 'term_image_uploader_enqueue' ) );
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

		$term_list = '';

		if ( isset( $terms ) && ! empty( $terms ) ) {

			foreach ( $terms as $term ) {

				$upload_image = get_term_meta( $term->term_id, 'term_image', true );
				$image_url    = esc_url( $upload_image );
				$link_url     = esc_url( get_term_link( $term ) );
				$sector       = esc_html( $term->name );

				$term_list .= '<div class="gt-banner-box" style="background-image: url(\'' . $image_url . '\');">
                <a href="' . $link_url . '" target="_parent">
                    <div class="gt-content">
                    <span class="primary">' . $sector . '</span>
                    </div>
                </a>
            </div>';

			}
		} else {
			$term_list = 'No data found!';
		}

		return '<div class="bsw-event-category-grid">' . $term_list . '</div>';
	}


	public function add_term_image_field( $taxonomy ) {
		?>
		

	<div class="form-field term-description-wrap cli-uploader-wrapper">
		<label for="tag-description">Upload an image</label>
		<img id="cli_upload_image_btn" src="<?php echo esc_url( $this->placeholder ); ?>" alt="">
		<input type="hidden" name="txt_upload_image" id="cli_upload_image_url" value="">
		<!-- <input type="button" id="cli_upload_image_btn" class="button" value="Upload Image" /> -->
		 <div class="cli_image_remove">&#x1F5D9;</div>
	</div>

		<?php
	}

	public function add_term_image_field_on_create( $term_id, $tt_id ) {
		if ( isset( $_POST['txt_upload_image'] ) && '' !== $_POST['txt_upload_image'] ) {
			$group = esc_url( $_POST['txt_upload_image'] );
			add_term_meta( $term_id, 'term_image', $group, true );
		}
	}

	public function add_term_image_field_on_edit( $term, $taxonomy ) {
		// get current group
		$txt_upload_image = get_term_meta( $term->term_id, 'term_image', true );
		?>
	<div class="bsw-term-image-field-container">
		<label for="">Upload and Image</label>
		<input type="text" name="txt_upload_image" id="txt_upload_image" value="<?php echo $txt_upload_image; ?>" style="width: 77%">
		<input type="button" id="cli_upload_image_btn" class="button" value="Upload an Image" />
	</div>
		<?php
	}

	public function update_term_image_field_on_edit( $term_id, $tt_id ) {
		if ( isset( $_POST['txt_upload_image'] ) && '' !== $_POST['txt_upload_image'] ) {
			$group = esc_url( $_POST['txt_upload_image'] );
			update_term_meta( $term_id, 'term_image', $group );
		}
	}

	// enque and localize script
	public function term_image_uploader_enqueue( $hook_suffix ) {
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
					'placeholder' => $this->placeholder,
				)
			);

			wp_enqueue_script( 'meta-image' );
		}
	}
}
