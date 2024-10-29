<?php
namespace Advance_Search\Inc\Core;
use Advance_Search as WPAS;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @author     mndpsingh287
 */
class Activator {

	/**
	 * Activation Hook.
	 *
	 * @since    1.0
	 */
	public static function activate() {

		$min_php = '5.6.0';
		$plugin_name = WPAS\PLUGIN_NAME;
        $wpas_search_field = '_shortcode_number';
      	$wpas_shortcode_number = 3;

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare(PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			
			wp_die('<p><strong>' . __('Advanced Search', 'advance-search').'</strong> '.  __('plugin requires a minmum PHP Version of ', 'advance-search').$min_php.'. '.__('You have to upgrade your php version to enjoy Advanced Search.', 'advance-search') . '</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
		
		}

		update_option( $plugin_name.'_default_search', '0' ); // default search
		update_option( $plugin_name.'_default_woo_search', '0' ); // default woo search
		update_option( $plugin_name. $wpas_search_field,$wpas_shortcode_number);
      
		global $wpdb;
        $wpas_index_table = $wpdb->prefix . 'wpas_index';
        $wpas_post_table = $wpdb->prefix . 'posts';

		$count_indexes  = $wpdb->query("SHOW KEYS FROM $wpas_post_table WHERE Key_name = 'wpas_index_post_table' OR Key_name = 'wpas_index_post_table_title' OR Key_name = 'wpas_index_post_table_content' ");
		if($count_indexes == 0){
			// change post table
			$wpdb->query("ALTER TABLE ".$wpas_post_table." ADD FULLTEXT wpas_index_post_table (post_title, post_content)");
			$wpdb->query("ALTER TABLE ".$wpas_post_table." ADD FULLTEXT wpas_index_post_table_title (post_title)");
			$wpdb->query("ALTER TABLE ".$wpas_post_table." ADD FULLTEXT wpas_index_post_table_content (post_content)");
		}
	

        if($wpdb->get_var("SHOW TABLES LIKE '$wpas_index_table'") != $wpas_index_table) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE ".$wpas_index_table." (
			    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  	name text NOT NULL,
			  	data longtext NOT NULL,
			  	PRIMARY KEY `id`(`id`)
            ) ENGINE = INNODB DEFAULT CHARSET = utf8";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

	}
}