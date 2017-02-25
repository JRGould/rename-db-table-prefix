<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://jrgould.com/wpctp/
 * @since             1.0.0
 * @package           WPCTP
 *
 * @wordpress-plugin
 * Plugin Name:       WP Change Table Prefix
 * Plugin URI:        http://jrgould.com/wpctp/
 * Description:       Change the table prefix of your WordPress install.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://jrgoulde.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpctp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpctp-activator.php
 */
function activate_wpctp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpctp-activator.php';
	WPCTP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpctp-deactivator.php
 */
function deactivate_wpctp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpctp-deactivator.php';
	WPCTP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpctp' );
register_deactivation_hook( __FILE__, 'deactivate_wpctp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpctp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpctp() {

	$plugin = new WPCTP();
	$plugin->run();

}
run_wpctp();
