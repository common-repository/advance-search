<?php

namespace Advance_Search\Inc\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @author     mndpsingh287
 */
class Internationalization_i18n {

	/**
	 * The text domain of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The text domain of the plugin.
	 */
	private $text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string $plugin_text_domain  The text domain of the plugin.
	 */
	public function __construct( $plugin_text_domain ) {

		$this->text_domain = $plugin_text_domain;

	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->text_domain,
			false,
			dirname(dirname( dirname( plugin_basename( __FILE__ ) ) ) ). '/languages'
		);
	}
}
