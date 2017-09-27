<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    RDTP
 * @subpackage RDTP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    RDTP
 * @subpackage RDTP/admin
 * @author     Jeff Gould <jrgould@gmail.com>
 */
class RDTP_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $rdtp    The ID of this plugin.
	 */
	private $rdtp;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The user capability required to use this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cap    The user capability required to use this plugin.
	 */
	private $cap;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string    $rdtp      The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      string    $cap        The user capability required to use this plugin.
	 */
	public function __construct( $rdtp, $version, $cap = 'import' ) {

		$this->rdtp = $rdtp;
		$this->version = $version;
		$this->cap = $cap;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in RDTP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The RDTP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->rdtp, plugin_dir_url( __FILE__ ) . 'dist/rdtp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in RDTP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The RDTP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->rdtp, plugin_dir_url( __FILE__ ) . 'dist/rdtp-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_menu() {
		$hook_suffix = add_management_page( 'Rename DB Table Prefix',
			'Rename DB Table Prefix',
			$this->cap,
			'rdtp',
			array( $this, 'options_page' ) );
	}

	public function options_page() {
		if( ! current_user_can( $this->cap ) ) {
			die( 'You must have the "' . $this->cap . '" capability to access this page' );
		}
		require_once( plugin_dir_path( __FILE__ ) . 'partials/rdtp-admin-display.php' );
	}

	public function ajax_rename_db_table_prefix() {
		if ( ! check_ajax_referer( 'rdtp-rename-db-table-prefix', 'rdtp-rename-db-table-prefix', false ) ) {
			wp_die( sprintf( __( 'Invalid nonce for "%s"', 'rdtp' ), 'rdtp-rename-db-table-prefix' ) );
		}

		$newPrefix = $_POST['new-prefix'];

		$newPrefixSanitized = preg_replace( '/[^a-zA-Z0-9\_\-]/', '', $newPrefix );

		if( $newPrefix != $newPrefixSanitized ) {
			wp_die( __( 'Error: invalid prefix.', 'rdtp' ) );
		}

		$updater = new RDTP_Prefix_Updater( $newPrefixSanitized );

		$result = $updater->run();
		if( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
	    } else {
			wp_send_json( array( 'success' => true ) );
		}


	}

	public function ajax_backup_wp_config() {
		if ( ! check_ajax_referer( 'rdtp-backup-wp-config', 'rdtp-backup-wp-config', false ) ) {
			wp_die( sprintf( __( 'Invalid nonce for "%s"', 'rdtp' ), 'rdtp-backup-wp-config' ) );
		}


			$wp_config = RDTP_Prefix_Updater::locate_wp_config();
		if ( $wp_config && is_readable( $wp_config ) ) {
			$backup_name = 'wp-config.php.' . date( 'Ymd_Gis' ) . '.bak';
			$backup_path = dirname( $wp_config ) . '/' . $backup_name;
			if ( copy( $wp_config, $backup_path ) ) {
				wp_die( json_encode( array( 'success' => sprintf( __( 'Backup created: `%s`'), $backup_path ) ) ) );
			} else {
				wp_die( json_encode( array( 'error' => sprintf( __( 'Error: Unable to write backup file to `%s`', 'rdtp' ), dirname( $wp_config ) ) ) ) );
			}
		}
		wp_die( json_encode( array( 'error' => sprintf( __( 'Error: Unable to read wp-config.php file located at `%s`', 'rdtp' ), $wp_config ) ) ) ) ;
	}

}
