<?php
/**
 * Plugin Name: Category List Image
 * Plugin URI: https://github.com/#
 * Description: Category List Image
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Nishan
 * Author URI: https://github.com/#
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: category-list-image
 * Domain Path: /languages
 *
 * @package category-list-image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define Plugins Constants
 *
 * @since 1.0.0
 */

define( 'CATEGORY_LIST_IMAGE_PATH', plugin_dir_path( __FILE__ ) );
define( 'CATEGORY_LIST_IMAGE_URL', plugin_dir_url( __FILE__ ) );
define( 'CATEGORY_LIST_IMAGE_PLUGIN_NAME', 'category-list-image' );
define( 'CATEGORY_LIST_IMAGE_VERSION', '1.0.0' );

register_activation_hook( __FILE__, 'activate_category_list_image' );
register_deactivation_hook( __FILE__, 'deactivate_category_list_image' );
// Hook to display admin notices.
add_action( 'admin_notices', 'category_list_image_notice' );
add_action( 'wp_ajax_cli_review_dismiss', 'cli_review_dismiss_action' );

/**
 * Display an admin notice for the welcome message.
 *
 * @since 1.0.0
 */
function category_list_image_notice() {

	if ( get_transient( 'category_list_image_notice' ) ) {
		printf(
			'<div class="notice notice-success is-dismissible cli-after-install-notice"><p>%1$s <b><a href="#">Category List Image</a></b>! 🎉 %2$s <a href="%6$s">%3$s &#8594;</a> %4$s <a class="cli-rating" href="#">★★★★★</a>. %5$s 😎</p></div>',
			/*1*/ esc_html__( 'Welcome to', 'category-list-image' ),
			/*2*/ esc_html__( 'Visit', 'category-list-image' ),
			/*3*/ esc_html__( 'Dashboard', 'category-list-image' ),
			/*4*/ esc_html__( 'If you find our plugin helpful, we would greatly appreciate it if you could leave us a', 'category-list-image' ),
			/*5*/ esc_html__( 'Thank you in advance!', 'category-list-image' ),
			/*6*/ esc_url( admin_url( 'admin.php?page=category-list-image' ) ),
		);

		// Delete the transient to prevent the notice from showing again.
		delete_transient( 'category_list_image_notice' );
	}

	// Plugin review notice (7 days).
	$time_unix = (int) get_user_meta( get_current_user_id(), 'category_list_image_install_time', true );

	if ( ( time() - $time_unix ) >= ( 7 * 24 * 60 * 60 ) ) {
		printf(
		// Translators: %2$s is the name of plugin.
			esc_html__( '%1$sYou have been using %2$s for a while now, and we hope you\'re enjoying it! If you do, please take a moment to leave us a %3$s rating to keep us motivated. If you\'d like to support us further, consider a cup of fuel ☕ to help us continue offering free features! %4$s Thank you! %5$s %6$s %7$s %8$s %9$s', 'category-list-image' ),
			/*1*/ '<div class="notice notice-info cli-notice-review is-dismissible"><p>',
			/*2*/ '<b><a href="#" target="_blank">Category List Image</a></b>',
			/*3*/ '<a href"#">★★★★★</a>',
			/*4*/ '&#x1F60E;',
			/*5*/ '</p>',
			/*6*/ '<p><a href="#" class="cli-review-write-btn" target="_blank">&#x1F600;&nbsp;Yes, you deserve it</a> &nbsp;',
			/*7*/ '<a href="#" class="cli-review-done-btn" data-clir="1">&#128151;&nbsp;Yes, I did.</a> &nbsp;',
			/*8*/ '<a href="#" class="cli-review-dismiss-btn" target="_blank">&#x1F614;&nbsp;Maybe later</a> &nbsp;',
			/*9*/ '<a href="#" target="_blank" class="cli-review-coffee-btn">☕&nbsp;Help keep the code running</a> </p></div>',
		);
	}
}

/**
 * Review reshedule.
 *
 * @since 1.0.0
 */
function cli_review_dismiss_action() {
	$is_review_done  = (int) filter_input( INPUT_POST, 'done', FILTER_DEFAULT );
	$time_reschedule = $is_review_done ? strtotime( '+1 month' ) : time();

	update_user_meta( get_current_user_id(), 'category_list_image_install_time', $time_reschedule );
	wp_die();
}

/**
 * Check screen.
 *
 * @since 1.0.0
 */
function check_category_list_image_page() {
	$screen = get_current_screen();
	if ( 'toplevel_page_category-list-image' === $screen->id ) {
		add_filter( 'admin_footer_text', 'category_list_image_footer_notice', 99 );
	}
}
add_action( 'current_screen', 'check_category_list_image_page' );

/**
 * Admin footer notice
 *
 * @since 1.0.0
 */
function category_list_image_footer_notice() {
	return __( 'Enjoying <b><a href="#" target="_blank">Category List Image</a></b>? <a href="#">Leave a ★★★★★ rating </a>! We\'re listening to your <a href="#">suggestions</a>. <a href="#">Fuel our code with a ☕</a>. Thank you very much! 😎', 'category-list-image' );
}

/**
 * The code that runs during plugin activation.
 *
 * @since 1.0.0
 */
function activate_category_list_image() {
	// check installation.
	if ( ! function_exists( 'wpforms' ) ) {
		set_transient( 'category_list_image_dependency', true, 5 );
	}

	// Set plugin install date.
	update_user_meta( get_current_user_id(), 'category_list_image_install_time', time() );

	// Set a transient to display the welcome message.
	set_transient( 'category_list_image_notice', true, 5 );
}

/**
 * The code that runs during plugin deactivation.
 *
 * @since 1.0.0
 */
function deactivate_category_list_image() {}

/**
 * Initiates the plugin's execution.
 *
 * Because all components of the plugin are hooked into the system,
 * starting the plugin at this point in the file does not impact
 * the overall page life cycle.
 *
 * @since    1.0.0
 */
function run_category_list_image() {
	require CATEGORY_LIST_IMAGE_PATH . 'includes/class-category-list-image.php';
	$plugin = new \CLI\Core\Category_List_Image();
	$plugin->run();
}

add_action( 'init', 'run_category_list_image' );

/**
 * Core plugin action links.
 * Includes interface, documentation, and settings.
 *
 * @param string[] $actions An array containing the plugin's action links.
 * @param string   $plugin_file The path to the plugin file relative to the plugins directory.
 *
 * @since 1.0.0
 */
function category_list_image_action_links( $actions, $plugin_file ) {
	static $plugin;

	if ( ! isset( $plugin ) ) {
		$plugin = plugin_basename( __FILE__ );
	}

	if ( $plugin === $plugin_file ) {
		$dashboard = array( 'dashboard' => '<a href="' . admin_url( 'options-general.php?page=category-list-image' ) . '">' . __( 'Dashboard', 'category-list-image' ) . '</a>' );
		// $doc_link     = array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=category-list-image-settings' ) . '">' . __( 'Settings', 'category-list-image' ) . '</a>' );
		// $support_link = array( 'support' => '<a href="' . admin_url( 'options-general.php?page=category-list-image-support' ) . '">' . __( 'Support', 'category-list-image' ) . '</a>' );

		// $actions = array_merge( $support_link, $actions );
		// $actions = array_merge( $doc_link, $actions );
		$actions = array_merge( $dashboard, $actions );
	}

	return $actions;
}

add_filter( 'plugin_action_links', 'category_list_image_action_links', 10, 5 );



/**
 * admin menu without using register_setting.
 */


// Add the custom admin menu
function custom_admin_menu() {
	add_menu_page(
		'Category List Image Settings', // Page title
		'Category List Image',          // Menu title
		'manage_options',               // Capability
		'category-list-image',          // Menu slug
		'category_list_image_page'      // Callback function
	);
}
add_action( 'admin_menu', 'custom_admin_menu' );

// Callback function to display settings page
function category_list_image_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Category List Image Settings', 'plugin-text-domain' ); ?></h1>
		<form method="post" action="">
			<?php
				// Display the fields
				$category_list_image = get_option( 'category_list_image', '' );
			?>
			<input type="text" name="category_list_image" value="<?php echo esc_attr( $category_list_image ); ?>" />
			<input type="submit" name="save_settings" value="<?php esc_attr_e( 'Save Settings', 'plugin-text-domain' ); ?>" />
		</form>
	</div>
	<?php
	// If settings are saved
	if ( isset( $_POST['save_settings'] ) ) {
		// Sanitize and save the input value manually
		$new_value = sanitize_text_field( $_POST['category_list_image'] );
		update_option( 'category_list_image', $new_value );
	}
}


function custom_api_settings_endpoint() {
	register_rest_route(
		'wp/v2',
		'/settings',
		array(
			'methods'             => 'POST',
			'callback'            => 'save_custom_settings',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'custom_api_settings_endpoint' );

function save_custom_settings( WP_REST_Request $request ) {
	// Get the value from the request
	$category_list_image = sanitize_text_field( $request->get_param( 'category_list_image' ) );

	// Save the setting using update_option()
	update_option( 'category_list_image', $category_list_image );

	return new WP_REST_Response( 'Settings saved successfully', 200 );
}


function enqueue_admin_scripts() {
	wp_enqueue_script(
		'category-list-image-settings',
		plugin_dir_url( __FILE__ ) . 'build/settings-page.js',
		array( 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'build/settings-page.js' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'enqueue_admin_scripts' );
