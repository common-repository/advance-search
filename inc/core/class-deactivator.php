<?php

namespace Advance_Search\Inc\Core;
use Advance_Search as WPAS;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @author     mndpsingh287
 */
class Deactivator {

	/**
	 * Deactivation Hook.
	 *
	 * @since    1.0
	 */
	public static function deactivate() {

		$plugin_name = WPAS\PLUGIN_NAME;
		
		global $wpdb;

		$wpas_post_table = $wpdb->prefix . 'posts';

        // change post table

        $wpdb->query("ALTER TABLE ".$wpas_post_table." DROP INDEX wpas_index_post_table");
        $wpdb->query("ALTER TABLE ".$wpas_post_table." DROP INDEX wpas_index_post_table_title");
        $wpdb->query("ALTER TABLE ".$wpas_post_table." DROP INDEX wpas_index_post_table_content");

	}

}
