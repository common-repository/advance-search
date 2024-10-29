<?php
/**
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/mndpsingh287
 * @since             1.0
 * @package           Advance_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Search
 * Plugin URI:        https://wordpress.org/plugins/advance-search
 * Description:       Advanced Search Includes Search Form Customizer, WooCommerce Search, AJAX Search & Voice Search support! Advanced Search plugin adds to default WordPress search engine the ability to search by content, post types, attachments from selected fields and ACF plugin fields support.
 * Version:           1.1.6
 * Author:            mndpsingh287
 * Author URI:        https://profiles.wordpress.org/mndpsingh287/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advance-search
 * Domain Path:       /languages
 */

namespace Advance_Search;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$plugin_name = 'advance-search';
$plugin_text_domain = 'advance-search';
$plugin_version = '1.1.6';

/**
 * Define Constants
 */
define( __NAMESPACE__ . '\WPAS', __NAMESPACE__ . '\\' );

define( WPAS . 'PLUGIN_NAME', $plugin_name );

define( WPAS . 'PLUGIN_VERSION', $plugin_version );

define( WPAS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( WPAS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( WPAS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( WPAS . 'PLUGIN_TEXT_DOMAIN', $plugin_text_domain );

/**
 * Autoload Classes
 */

require_once( PLUGIN_NAME_DIR . 'inc/libraries/autoloader.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook( __FILE__, array( WPAS . 'Inc\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( WPAS . 'Inc\Core\Deactivator', 'deactivate' ) );

/**
 * Maintains a single copy of the plugin app object
 *
 * @since    1.0
 */
class Advance_Search {

	/**
	 * The instance of the plugin.
	 *
	 * @since    1.0
	 * @var      Init $init Instance of the plugin.
	 */
	static $init;
	/**
	 * Loads the plugin
	 *
	 * @access public
	 */
	public static function init() {

		if ( null === self::$init ) {
			self::$init = new Inc\Core\Init();
			self::$init->run();
		}

		return self::$init;
	}
}

$min_php = '5.6.0';

/**
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 **/
function Advance_Search_init() {
		return Advance_Search::init();
}

/**
 * Show admin notice if php version is lower than required version.
 */
function upgrade_php_version() {
	$html = '<div class="error">';
	$html .= '<p>' . sprintf( __('Advanced Search plugin requires a minmum PHP Version of %s. You have to upgrade your php version to enjoy Advanced Search.', 'advance-search'), $min_php) . '</p>';
	$html .= '</div>';
	echo apply_filters('the_content',$html);	
}


// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
		Advance_Search_init();
}
else {
	add_action('admin_notices', 'upgrade_php_version');
}