<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/martinkrcho/
 * @since             1.0.0
 * @package           iFocus_Link_Nest
 *
 * @wordpress-plugin
 * Plugin Name:       iFOCUS.sk Link Nest Lite
 * Plugin URI:        https://www.ifocus.sk/
 * Description:       Plugin allows the site owner to automate linking selected keywords in the site content.
 * Version:           1.0.0
 * Author:            Martin Krcho
 * Author URI:        https://www.linkedin.com/in/martinkrcho/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ifocus-link-nest
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
define( 'IFOCUS_LINK_NEST_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-ifocus-link-nest-activator.php
 */
function activate_ifocus_link_nest() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ifocus-link-nest-activator.php';
	iFocus_Link_Nest_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * This action is documented in includes/class-ifocus-link-nest-deactivator.php
 */
function deactivate_ifocus_link_nest() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ifocus-link-nest-deactivator.php';
	iFocus_Link_Nest_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ifocus_link_nest' );
register_deactivation_hook( __FILE__, 'deactivate_ifocus_link_nest' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ifocus-link-nest.php';

// Require Composer autoloader if it exists.
if ( file_exists( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
}

require_once plugin_dir_path( __FILE__ ) . '/includes/vendor/wpwhitesecurity/select2-wpwhitesecurity/load.php';
if ( class_exists( '\S24WP' ) ) {
	\S24WP::init( plugin_dir_url( __FILE__ ) . '/includes/vendor/wpwhitesecurity/select2-wpwhitesecurity' );
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ifocus_link_nest() {
	$plugin = new iFocus_Link_Nest();
	$plugin->run();
}
run_ifocus_link_nest();
