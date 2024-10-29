<?php

namespace Advance_Search\Inc\Core;
use Advance_Search as WPAS;
use Advance_Search\Inc\Admin as Admin;
use Advance_Search\Inc\Common as Common;
use Advance_Search\Inc\Frontend as Frontend;
use Advance_Search\Inc\Admin\Vc_Element as Vc_Element;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @author     mndpsingh287
 */
class Init {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $plugin_base_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_basename;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The text domain of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The text domain of the plugin.
	 */
	protected $plugin_text_domain;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->plugin_name = WPAS\PLUGIN_NAME;
		$this->plugin_basename = WPAS\PLUGIN_BASENAME;
		$this->version = WPAS\PLUGIN_VERSION;
		$this->plugin_text_domain = WPAS\PLUGIN_TEXT_DOMAIN;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_common_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Loads the following required dependencies for this plugin.
	 *
	 * - Loader - Orchestrates the hooks of the plugin.
	 * - Internationalization_i18n - Defines internationalization functionality.
	 * - Admin - Defines all hooks for the admin area.
	 * - Frontend - Defines all hooks for the public side of the site.
	 *
	 * @access    private
	 */
	private function load_dependencies() {
		$this->loader = new Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Internationalization_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access    private
	 */
	private function set_locale() {

		$plugin_i18n = new Internationalization_i18n( $this->plugin_text_domain );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access    private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin\Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Save/Update plugin options.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'update_plugin_options' );

		// Admin menu for the plugin.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// search form created with default settings
		$this->loader->add_action( 'admin_post_wpas_search_form_response', $plugin_admin, 'wpas_search_form');

		//when a form setting is submitted
		$this->loader->add_action( 'admin_post_wpas_search_form_settings', $plugin_admin, 'wpas_search_form_settings');

		// default search form setting
		$this->loader->add_action( 'admin_post_wpas_default_search_form_response', $plugin_admin, 'wpas_default_search_form_settings');

		// Editor Element addon

		$this->loader->add_action('init', $plugin_admin, 'editor_element_init_actions');

		// default editor custom tinymce buttons

		$this->loader->add_action('admin_head', $plugin_admin, 'wpas_add_mce_button');
		$this->loader->add_action('admin_head', $plugin_admin, 'wpas_mce_generate_variable');

		//$vc_element = new Vc_Element\Vc_Element( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

		// add custom setting links
		$this->loader->add_filter( 'plugin_action_links_'.$this->plugin_name.'/'.$this->plugin_name.'.php', $plugin_admin, 'wpas_plugin_link', 10, 2 );

		// extra ajax

		$this->loader->add_action( 'wp_ajax_WPAS_Advanced_Search_extra_ajax', $plugin_admin, 'wpas_search_extra_ajax' );

		/*
            Lokhal Verify Email
        */
        $this->loader->add_action('wp_ajax_wpas_verify_email', $plugin_admin, 'advance_search_verify_email_callback');
        $this->loader->add_action('wp_ajax_verify_wpas_email', $plugin_admin, 'verify_wpas_email_callback');
	
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access    private
	 */
	private function define_public_hooks() {

		$plugin_public = new Frontend\Frontend( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the common functionality of the plugin.
	 *
	 * The search form logic is written here.
	 *
	 * @access    private
	 */
	private function define_common_hooks() {

		$plugin_common = new Common\Common( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_common, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_common, 'enqueue_scripts' );

		/*
		 * To completely replace searchform.php with a custom form uncomment the line
		 * below, and delete the apply_filter and remove_filter statements in the
		 * method shortcode_WPAS_Advanced_Search of common/class-common.php
		 */

		// action to add the [wpas-advanced-search] shortcode that loads the custom search form.
		$this->loader->add_action( 'init', $plugin_common, 'register_shortcodes' );

		// ajax handler for loading the search auto-suggest.
		$this->loader->add_action( 'wp_ajax_WPAS_Advanced_Search_autosuggest', $plugin_common, 'advanced_search_autosuggest_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_WPAS_Advanced_Search_autosuggest', $plugin_common, 'advanced_search_autosuggest_handler' );

		// check default search
		$this->loader->add_action( 'init', $plugin_common, 'wpas_replace_default_search');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the text domain of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The text domain of the plugin.
	 */
	public function get_plugin_text_domain() {
		return $this->plugin_text_domain;
	}
}
