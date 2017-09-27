<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://jrgould.com/rdtp/
 * @since             0.1.0
 * @package           RDTP
 *
 * @wordpress-plugin
 * Plugin Name:       Rename DB Table Prefix
 * Plugin URI:        http://jrgould.com/rdtp/
 * Description:       Change the table prefix of your WordPress install.
 * Version:           0.1.0
 * Author:            Jeff Gould
 * Author URI:        http://jrgould.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rdtp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rdtp-activator.php
 */
function activate_rdtp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rdtp-activator.php';
	RDTP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rdtp-deactivator.php
 */
function deactivate_rdtp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rdtp-deactivator.php';
	RDTP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rdtp' );
register_deactivation_hook( __FILE__, 'deactivate_rdtp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rdtp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rdtp() {

	$plugin = new RDTP();
	$plugin->run();

}
run_rdtp();
