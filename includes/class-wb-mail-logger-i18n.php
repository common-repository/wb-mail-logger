<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://profiles.wordpress.org/webbuilder143/
 * @since      1.0.0
 *
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/includes
 * @author     Web Builder 143 <webbuilder143@gmail.com>
 */
class Wb_Mail_Logger_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wb-mail-logger',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
