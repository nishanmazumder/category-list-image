<?php
/**
 * Class Category_List_Image_Notice
 *
 * Handles notices via the WP_Error class and displays them as admin notices.
 *
 * @link       https://github.com/#
 * @since      1.0.0
 *
 * @package    wordpress
 * @subpackage category-list-image
 */

namespace CLI\Core;

/**
 * Class Category_List_Image_Notice
 *
 * Handles notices using the WP_Error class and displays them as admin notices.
 */
class Category_List_Image_Notice {
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $instance The instance of this class.
	 */
	private static $instance = null;

	/**
	 * Notice Object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $notices WP_Error Object.
	 */
	private $notices;

	/**
	 * Notice Type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $type Available types: 'success', 'info', 'warning', 'error'.
	 */
	private $type;

	/**
	 * Constructor.
	 *
	 * Sets up the WP_Error instance for storing notices.
	 */
	private function __construct() {
		$this->notices = new \WP_Error();
	}

	/**
	 * Retrieves the singleton instance of the class.
	 *
	 * @return Category_List_Image_Notice The singleton instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Sets a notice message.
	 *
	 * @param string $code     The code for the notice.
	 * @param string $message  The notice message.
	 * @param string $type     The notice type. Available types: 'success', 'info', 'warning', 'error'.
	 */
	public function set_notice( $code, $message, $type = 'success' ) {
		$this->notices->add( $code, $message );
		$this->type = $type;
	}

	/**
	 * Retrieves all notice messages.
	 *
	 * @return \WP_Error The WP_Error instance containing notices.
	 */
	public function get_notices() {
		return $this->notices;
	}

	/**
	 * Clears all notice messages.
	 *
	 * Resets the \WP_Error instance.
	 */
	public function clear_notices() {
		$this->notices = new \WP_Error();
	}

	/**
	 * Display notice messages as admin notices.
	 *
	 * Outputs the notices and clears them after display.
	 */
	public function display_notices() {
		if ( ! empty( $this->notices->get_error_messages() ) ) {
			foreach ( $this->notices->get_error_messages() as $notice ) {
				printf(
					'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
					esc_html( $this->type ),
					esc_html( $notice )
				);
			}
			// Clear messages after displaying.
			$this->clear_notices();
		}
	}
}
