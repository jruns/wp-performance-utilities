<?php

/**
 *
 * @link              https://github.com/jruns
 * @since             0.1
 * @package           Performance_Utilities
 *
 * @wordpress-plugin
 * Plugin Name:       Performance Utilities 
 * Plugin URI:        https://github.com/jruns/wp-performance-utilities
 * Description:       Utilities to improve the performance of your WordPress site.
 * Version:           0.8
 * Author:            Jason Schramm
 * Author URI:        https://github.com/jruns
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       performance-utilities
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PERFORMANCE_UTILITIES_VERSION', '0.8' );
define( 'PERFORMANCE_UTILITIES_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-performance-utilities-activator.php
 */
function activate_performance_utilities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-performance-utilities-activator.php';
	Performance_Utilities_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-performance-utilities-deactivator.php
 */
function deactivate_performance_utilities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-performance-utilities-deactivator.php';
	Performance_Utilities_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_performance_utilities' );
register_deactivation_hook( __FILE__, 'deactivate_performance_utilities' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-performance-utilities.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_performance_utilities() {

	$plugin = new Performance_Utilities();
	$plugin->run();

}
run_performance_utilities();
