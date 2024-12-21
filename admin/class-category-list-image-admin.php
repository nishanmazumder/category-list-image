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

	private $term_image;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		self::$notice      = Category_List_Image_Notice::get_instance();

		$this->placeholder = CATEGORY_LIST_IMAGE_URL . '/assets/admin/img/cli-upload-placeholder.png';

		$this->term_image = filter_input( INPUT_POST, 'cli_term_image', FILTER_DEFAULT );

		// Admin menu added.
		add_action( 'admin_menu', array( $this, 'add_wpfmm_admin_menu' ) );


		add_action( 'init', array( $this, 'category_list_image_int' ), 99 );

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
		// add column name.
		add_filter( 'manage_edit-category_columns', array( $this, 'add_category_list_image_column' ) );
		// display column value.
		add_filter( 'manage_category_custom_column', array( $this, 'display_category_list_image_column' ), 10, 3 );
		// add script for image upload.
		add_action( 'admin_enqueue_scripts', array( $this, 'term_image_uploader_enqueue' ) );
	}


	public function category_list_image_int() {
		echo '<pre>';
		print_r("test");
		echo '</pre>';
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
			'category-list-image',
			function () {
				// Show form list.
				echo 'category-list-image ';
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

				$upload_image = get_term_meta( $term->term_id, 'cli_term_image', true );
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
		<img id="cli_upload_image_btn" src="<?php echo esc_attr( $this->placeholder ); ?>" alt="Category list image placeholder">
		<input type="hidden" name="cli_term_image" id="cli_upload_image_url" value="">
		<div class="cli_image_remove">&#x1F5D9;</div>
	</div>

		<?php
	}

	public function add_term_image_field_on_create( $term_id, $taxonomy_id ) {
		if ( isset( $this->term_image ) && null !== $this->term_image ) {
			$image_url = sanitize_url( $this->term_image );
			add_term_meta( $term_id, 'cli_term_image', $image_url, true );
		}
	}

	public function add_term_image_field_on_edit( $term, $taxonomy ) {
		$term_image = get_term_meta( $term->term_id, 'cli_term_image', true );
		?>
		<tr class="form-field form-required term-name-wrap cli-uploader-wrapper">
			<th scope="row"><label for="name">Upload an image</label></th>
			<td>
				<img id="cli_upload_image_btn" src="<?php echo esc_attr( $term_image ? $term_image : $this->placeholder ); ?>" alt="Category list image placeholder">
				<input type="hidden" name="cli_term_image" id="cli_upload_image_url" value="">
				<div class="cli_image_remove">&#x1F5D9;</div>
				<p class="description" id="name-description">The image is how it appears on your category image.</p>
			</td>
		</tr>
		<?php
	}

	public function update_term_image_field_on_edit( $term_id, $taxonomy_id ) {
		if ( isset( $this->term_image ) && null !== $this->term_image ) {
			update_term_meta( $term_id, 'cli_term_image', sanitize_url( $this->term_image ) );
		}
	}

	public function add_category_list_image_column( $columns ) {
		$new_columns = array();

		$i = 0;
		foreach ( $columns as $key => $value ) {
			if ( 1 === $i++ ) {
				$new_columns['cli_image'] = __( 'Image', 'category-list-image' );
			}

			$new_columns[ $key ] = $value;
		}

		return $new_columns;
	}

	public function display_category_list_image_column( $value, $column_name, $term_id ) {

		if ( 'cli_image' == $column_name ) {
			$image_url = get_term_meta( $term_id, 'cli_term_image', true );
			$value     = '<img class="category-image-thumb" src="' . esc_attr( $image_url ? $image_url : $this->placeholder ) . '" >';
		}

		return $value;
	}

	// enque and localize script
	public function term_image_uploader_enqueue( $hook_suffix ) {
		if ( $hook_suffix === 'edit-tags.php' || $hook_suffix === 'term.php' ) {
			wp_enqueue_media();

			wp_register_script( 'meta-image', CATEGORY_LIST_IMAGE_URL . '/assets/admin/js/media-uploader.js', array( 'jquery' ) );

			wp_localize_script(
				'meta-image',
				'meta_image',
				array(
					'title'       => 'Upload an Image',
					'button'      => 'Use this Image',
					'placeholder' => $this->placeholder,
				)
			);

			wp_enqueue_script( 'meta-image' );
		}
	}
}
