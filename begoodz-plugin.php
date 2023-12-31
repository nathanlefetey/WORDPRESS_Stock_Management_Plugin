<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           begoodz_plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Begoodz Plugin
 * Plugin URI:        https://ingenius.agency/
 * Description:       This plugin provides global inventory management for woocommerce products.
 * Version:           2.0.0
 * Author:            Ingenius
 * Author URI:        https://ingenius.agency/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       happy-larry-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Begoodz_Plugin_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-happy-larry-plugin-activator.php
 */
function activate_begoodz_plugin() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-begoodz-plugin-activator.php';
    Begoodz_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-happy-larry-plugin-deactivator.php
 */
function deactivate_begoodz_plugin() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-begoodz-plugin-deactivator.php';
    Begoodz_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_begoodz_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_begoodz_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-begoodz-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_begoodz_plugin() {

	$plugin = new Begoodz_Plugin();
	$plugin->run();

}
run_begoodz_plugin();
