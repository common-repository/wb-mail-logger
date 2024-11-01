<?php

/**
 *
 * @link              https://profiles.wordpress.org/webbuilder143/
 * @since             1.0.0
 * @package           Wb_Mail_Logger
 *
 * @wordpress-plugin
 * Plugin Name:       Wb Mail Logger
 * Plugin URI:        https://wordpress.org/plugins/wb-mail-logger/
 * Description:       Wb Mail Logger will help to capture all WP emails. So this will help to debug email related issues. And also use full to store a copy of all sent emails.
 * Version:           1.0.5
 * Author:            Web Builder 143
 * Author URI:        https://profiles.wordpress.org/webbuilder143/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wb-mail-logger
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
define('WB_MAIL_LOGGER_VERSION', '1.0.5');
define('WB_MAIL_LOGGER_SETTINGS', 'WB_MAIL_LOGGER_SETTINGS');

define ('WB_MAIL_LOGGER_PLUGIN_FILENAME', __FILE__);
define ('WB_MAIL_LOGGER_PLUGIN_NAME', 'wb-mail-logger');
define ('WB_MAIL_LOGGER_PLUGIN_PATH', plugin_dir_path(WB_MAIL_LOGGER_PLUGIN_FILENAME));
define ('WB_MAIL_LOGGER_PLUGIN_URL', plugin_dir_url(WB_MAIL_LOGGER_PLUGIN_FILENAME));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wb-mail-logger-activator.php
 */
function activate_wb_mail_logger() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wb-mail-logger-activator.php';
	Wb_Mail_Logger_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wb-mail-logger-deactivator.php
 */
function deactivate_wb_mail_logger() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wb-mail-logger-deactivator.php';
	Wb_Mail_Logger_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wb_mail_logger' );
register_deactivation_hook( __FILE__, 'deactivate_wb_mail_logger' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wb-mail-logger.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wb_mail_logger() {

	$plugin = new Wb_Mail_Logger();
	$plugin->run();

}
run_wb_mail_logger();
