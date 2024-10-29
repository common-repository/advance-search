<?php
namespace Advance_Search\Inc\Common;
use Advance_Search as WPAS;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the Shortcode, Search Form, Hooks and
 * the public-facing stylesheet and JavaScript.
 *
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @author    mndpsingh287
 */
class Common {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param string $plugin_text_domain The text domain of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
		
		// add dyanamic style

		add_action('wp_footer', array($this, 'wpas_dyanamic_css'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome.min', plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/font-awesome.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/advance-search-common.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

		$params = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('wpas_ajax_search'),
		);
		wp_enqueue_script( 'Advance_Search', plugin_dir_url( __FILE__ ) . 'js/advance-search.js', array( 'jquery', 'jquery-ui-autocomplete' ), $this->version, true );
		wp_localize_script( 'Advance_Search', 'params', $params );

	}

	/**
	 * AJAX handler for the auto-suggest.
	 *
	 * Callback for the "wp_ajax_WPAS_Advanced_Search_autosuggest" and
	 * "wp_ajax_nopriv_WPAS_Advanced_Search_autosuggest" hooks in "class-init.php"
	 *
	 * @since    1.0
	 */
	public function advanced_search_autosuggest_handler() {

		if(isset($_POST)) {

			$requested_data = sanitize_post($_POST);
			
			$nonce = sanitize_text_field(htmlentities($requested_data['security']));

			if ( ! wp_verify_nonce( $nonce, 'wpas_ajax_search' )  ) {
				die ( 'Unauthorized !');
			}

			$form_id = intval($requested_data['form_id']);
			global $wpdb;
			
			$wp_posts_table = $wpdb->prefix."posts";
			$postmeta_table = $wpdb->prefix."postmeta";
			$search_form_table = $wpdb->prefix."wpas_index";

			$search_form_settings = $wpdb->get_row($wpdb->prepare("SELECT data FROM $search_form_table where id=%d", $form_id));
			
			if($wpdb->num_rows > 0) {

				$search_form_settings = json_decode($search_form_settings->data, true);
				$t_data = array();

				if(isset($search_form_settings['enable_special_character']) && $search_form_settings['enable_special_character'] == 'yes'){
					$s = sanitize_text_field($requested_data['term']);
				}else{
					$s = sanitize_text_field(htmlentities($requested_data['term']));

				}
				$s = preg_replace("/^[A-Za-z0-9-!?\\\\.‘'` À-ÖØ-öø-ÿĀ-ſ',_&]$/", '', $s);

				$search_type = $search_form_settings['search_type'];
				// custom post type search

				if(array_key_exists('post_types', $search_form_settings['post_types'])) {


					$post_types = '"'.implode('","', $search_form_settings['post_types']['post_types']).'"';
					// check if enable any specific title or content in search areas

					if(array_key_exists('search_areas', $search_form_settings['post_types']) && !empty($search_form_settings['post_types']['search_areas'])) {
						$search_columns = implode(',', $search_form_settings['post_types']['search_areas']);

						$search_areas = $search_form_settings['post_types']['search_areas'];
							$count = count($search_areas);
							if($count > 1) {
								$like = "post_title like '%$s%' OR post_content like '%$s%'";
							}
							else {
								$like = isset($search_areas[0]) ? "$search_areas[0] like '%$s%'" : "post_title like '%$s%' OR post_content like '%$s%'";
							}
					}
					else {
						$search_columns = 'post_title,post_content';
						$like = "post_title like '%$s%' OR post_content like '%$s%'";
					}

					// check if meta keys added
					$join = '';
					$where = "AND post_status='publish'";
					$post_meta_keys = isset($search_form_settings['post_types']['meta_keys']['0']) ? $search_form_settings['post_types']['meta_keys']['0'] : '';
					if(!empty($post_meta_keys)) {
						
						$post_meta_keys_array = explode(',',$post_meta_keys);

						if(!empty($post_meta_keys_array)) {
							$m = 1;
							foreach($post_meta_keys_array as $meta_key) {

								$join .= 'INNER JOIN '.$postmeta_table.' m'.$m.' ON ('.$wp_posts_table.'.ID = m'.$m.'.post_id AND '.$wp_posts_table.'.post_status="publish") ';
								$where .= " OR ( m".$m.".meta_key = '$meta_key' AND m".$m.".meta_value like '%$s%') ";

								$m++;
							}
						}

					}

					// check search type
					
					if($search_type == 'full_word' || empty($search_type) ) {
						$post_page_query = "SELECT ID, post_title, post_type, post_content, post_status FROM $wp_posts_table $join WHERE post_type IN($post_types) AND MATCH ($search_columns) AGAINST ('$s' IN BOOLEAN MODE) $where  GROUP BY ID";
					
					}
					else {
						$post_page_query = "SELECT ID, post_title, post_type, post_content, post_status FROM $wp_posts_table $join WHERE post_type IN($post_types) AND ($like) $where  GROUP By ID";	
					}

					$posts_pages = $wpdb->get_results($post_page_query);
				}
				
				$t_data = $posts_pages;
				$html = '';
				
				if(!empty($t_data)) {
					foreach ($t_data as $final) {
						if($final->post_type == 'attachment' || $final->post_type == 'revision' || $final->post_status == 'revision') {
						}else {
							$html .= '<div class="page_post"><a href="'.esc_url(get_the_permalink($final->ID)).'">'.esc_attr($final->post_title).' ('.$final->post_type.')</a> </div>';
						}
					}
				}

				// custom taxonomies search

				if(isset($search_form_settings['taxonomies']) && array_key_exists('taxonomies', $search_form_settings['taxonomies']) && !empty($search_form_settings['taxonomies']['taxonomies']  )) {
				
					$args = array(
						'taxonomy'      => $search_form_settings['taxonomies']['taxonomies'], // taxonomy name
						'orderby'       => 'id', 
						'order'         => 'ASC',
						'fields'        => 'all',
						'name__like'    => $s
					);

					// description search
					$args_description = array(
						'taxonomy'      => $search_form_settings['taxonomies']['taxonomies'], // taxonomy name
						'orderby'       => 'id', 
						'order'         => 'ASC',
						'fields'        => 'all',
						'description__like'	=> $s
					);

					$terms_result = array();

					// check if enable any specific title or content in search areas

					if(array_key_exists('search_areas', $search_form_settings['taxonomies']) && (isset($search_form_settings['taxonomies']['search_areas']['content']) || isset($search_form_settings['taxonomies']['search_areas']['title']) )) {
					
						if(array_key_exists('title', $search_form_settings['taxonomies']['search_areas']) && !isset($search_form_settings['taxonomies']['search_areas']['content'])) {
							$terms_result = get_terms( $args );

						} if(array_key_exists('content', $search_form_settings['taxonomies']['search_areas']) && !isset($search_form_settings['taxonomies']['search_areas']['title'])) {
							$terms_result = get_terms( $args_description );

						} if(isset($search_form_settings['taxonomies']['search_areas']['title']) && isset($search_form_settings['taxonomies']['search_areas']['content'])) {
							$terms = get_terms( $args );
							$terms1 = get_terms( $args_description );
							$terms_m = array_merge($terms, $terms1);
							$terms_result = array_unique($terms_m, SORT_REGULAR);
						}

					} else {
					
						$terms = get_terms( $args );
						$terms1 = get_terms( $args_description);
						$terms_merge = array_merge($terms, $terms1);
						$terms_result = array_unique($terms_merge, SORT_REGULAR);

					}
				
					$count = count($terms_result);
			
					if($count > 0){
						foreach ($terms_result as $term) {
							$html .= "<div><a href='".esc_url(get_term_link( $term ))."'>".esc_attr($term->name). " (".$term->taxonomy.")</a></div>";
						}
					}

				}

				// attachments search

				if(array_key_exists('attachments', $search_form_settings) && !empty($search_form_settings['attachments'])) {
					$attachments_mime_type = '"'.implode('","', $search_form_settings['attachments']).'"';
					$wp_post_attachments_data = array();
					if(in_array('image/jpeg', $search_form_settings['attachments']) || in_array('image/gif', $search_form_settings['attachments']) || in_array('image/png', $search_form_settings['attachments'])) {

					// check search type
					
					if( empty($search_type) || $search_type == 'full_word') {
						$wp_post_attachment_query = "SELECT ID, post_title, post_content, post_type, guid, post_mime_type FROM $wp_posts_table WHERE post_mime_type IN ($attachments_mime_type) AND MATCH (post_title, post_content) AGAINST ('$s' IN NATURAL LANGUAGE MODE) GROUP BY ID";
					}
					else {
						$wp_post_attachment_query = "SELECT ID, post_title, post_content, post_type, guid, post_mime_type FROM $wp_posts_table WHERE post_type = 'attachment' AND post_mime_type IN ($attachments_mime_type) AND post_title Like '%$s%' GROUP BY ID";
					}

					$wp_post_attachments_data = $wpdb->get_results($wp_post_attachment_query);

					}

					$attachments_data = $wp_post_attachments_data;
					if(!empty($attachments_data)) {
						foreach ($attachments_data as $attach_final) {
							if(isset($attach_final->post_title) && !empty($attach_final->post_title)) {
								if(isset($attach_final->guid)) {
									$link = $attach_final->guid;
								}
								else {
									$link = $attach_final->post_guid;
								}
								$html .= '<div class="attachments"><a href="'.esc_url($link).'" target="_blank">'.esc_attr($attach_final->post_title).' ('.$attach_final->post_type.')</a> </div>';
							}
						}
					}

				}
				
				// check if result not found

				if($html == '') {
					$html .= '<div class="not_found">'.esc_attr__('No results found.', 'advance-search').'</div>';
				}

				$final_data = array();
				$final_data['form_id'] = $form_id;
				$final_data['html'] = $html;
				// Echo the response to the AJAX request.
				wp_send_json($final_data);
				
			}
		}

	}

	/**
	 * Register shortcodes.
	 *
	 * @since    1.0
	 */
	public function register_shortcodes() {

		add_shortcode( 'wpas', array( $this, 'shortcode_WPAS_Advanced_Search' ) );

	}

	/**
	 * Shortcode to add the advanced search form.
	 *
	 * Loads the custom search form added via the get_search_form filter hook.
	 * The custom search form is retrieved from the "advanced_search_form_markup" method.
	 * Returns the custom search form, and if the form was submitted the Search Results as well.
	 *
	 * @since    1.0
	 *
	 * @param  mixed  $atts an associative array of attributes, or an empty string if no attributes are given.
	 * @param  string $content the enclosed content.
	 */
	
	public function shortcode_WPAS_Advanced_Search( $atts, $content = null ) {
		
		/*
		 * Hook in a custom search form to override searchform.php in the theme or the
		 * default search form using the "get_search_form" filter hook.
		 *
		 * https://developer.wordpress.org/reference/functions/get_search_form/
		 *
		 * Note: I am adding and removing the "get_search_form" filter as I want my
		 * advanced form to load only when I invoke it using the plugin shortcode.
		 * This will ensure that any form defined in the theme's searchform.php is not
		 * overwritten.
		 *
		 * To completely override searchform.php detele the add_filter and remove_filter
		 * lines below and uncomment line 172 in the method "define_common_hooks" of
		 * inc/core/class-init.php.
		 */

		global $wpdb;
		$search_form_table = $wpdb->prefix."wpas_index";
		$search_form_id = $wpdb->get_results("SELECT id FROM $search_form_table limit 0,1");
		$f_id = $search_form_id[0]->id;

		$setting = shortcode_atts( array(
	        'id' => $f_id,
	        'title'=> '',
	    ), $atts );

		$form = '';
		ob_start();
		$form_id = intval($setting['id']);
		$custom_title = sanitize_text_field(htmlentities($setting['title']));
		
		$search_forms = $wpdb->get_row($wpdb->prepare("SELECT id, name, data FROM $search_form_table where id=%d",$form_id));

		if($wpdb->num_rows > 0) {
			$form_title = $search_forms->name;
			$form_setting = json_decode($search_forms->data, true);
			$in_name = esc_attr( $this->plugin_name ).'_'.$form_id;
			$loader_icon = 'sbl-circ';
			$magnifire_icon = 'search';
			$button_icon_position = sanitize_text_field($form_setting['styling']['magnifire']['position']);
			$button_text = sanitize_text_field($form_setting['styling']['search_button']['text']);

			$show_submit_button_text = sanitize_text_field($form_setting['styling']['search_button']['show_search_text']);
			$show_magnifire_icon = sanitize_text_field($form_setting['styling']['search_button']['show_maginfier_icon']);
			$submit_button_text = '';
			if(isset($show_submit_button_text) && !empty($show_submit_button_text) ) {
				$submit_button_text = '<div class="button_text_holder">'.esc_attr($button_text).'</div>';
			}

			$magnifire_icon_show = '';
			if(isset($show_magnifire_icon) && !empty($show_magnifire_icon )) {
				$magnifire_icon_show = '<i class="fa fa-'.$magnifire_icon.'"></i>';
			}
			
			$title = '';

			if( !empty($custom_title )) {
				$title = '<h3>'.esc_attr($custom_title).'</h3>';
			}

			$form .= '<div class="wpas-advanced-search-form-container wpas_form_container_'.$form_id.'">
			'.$title.'
			<div class="wpas_wrapper">
		<form id="wpas-advanced-search-form_'.$form_id.'" role="search" method="POST" autocomplete="off" class="wpas_search_form search_form_'.$form_id.'" action="" onkeydown="return event.key != \'Enter\';">
		<input type="hidden" name="form_id" value="'.$form_id.'" />
		<div class="input_cont '.$button_icon_position.'">
			<div class="wpas_input_container" id="wpas_input_container_'.$form_id.'">
				<div class="input_box"><input required class="wpas_search_input" id="wpas_search_input_'.$form_id.'" type="search" placeholder="'. __("Search here...", 'advance-search' ).'" name="'.$in_name.'" data-formid="'.$form_id.'" autocomplete="off" />
				</label>
				<div class="wpas_search_loader_icon" style="display:none;"><div class="'.$loader_icon.'"></div></div>
	<div class="wpas_search_close">
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 612 612" enable-background="new 0 0 612 612" xml:space="preserve">
				<polygon id="x-mark-icon" points="438.393,374.595 319.757,255.977 438.378,137.348 374.595,73.607 255.995,192.225 137.375,73.622 73.607,137.352 192.246,255.983 73.622,374.625 137.352,438.393 256.002,319.734 374.652,438.378 "></polygon>
				</svg>
			</div>
				</div>
				</div>
				<div class="wpas_submit_wrapper"><div class="wpas_margnifire_icon">'.$magnifire_icon_show.' '.$submit_button_text.'<div class="new_icon_div"></div></div>
			</div> <!-- wpas-input-container -->
	</div>
			<div class="wpas_search_result" style="display:none;"></div>
		</form> <!-- wpas-advanced-search-for -->
		</div>
	</div> <!-- wpas-advanced-search-form-container -->';

		}else {
			$form .= "<p class='alert alert-danger'>". esc_attr__('Search form not found !', 'advance-search')."</p>";
		}

		ob_end_clean();

		return $form;

	}

	// daynamic style

	public function wpas_dyanamic_css() {
		global $wpdb;
		$search_form_table = $wpdb->prefix."wpas_index";
		$search_forms = $wpdb->get_results("SELECT id, name,data FROM $search_form_table");
		
		if(!empty($search_forms)) {
			$i = 0;
			foreach ($search_forms as $search_form) {
				$form_setting = json_decode($search_forms[$i]->data, true);
				$search_box_settings = $form_setting['styling']['search_box_outer'];
				$search_input_settings = $form_setting['styling']['search_input'];
				$magnifire_settings = $form_setting['styling']['magnifire'];
				$loader_settings = $form_setting['styling']['loader'];
				$search_button_settings = $form_setting['styling']['search_button'];

				// desktop
				$desktop_box_width = ($search_box_settings['width']['desktop']) ? $search_box_settings['width']['desktop'] : '100%';
				// tablet
				$tablet_box_width = ($search_box_settings['width']['tablet']) ? $search_box_settings['width']['tablet'] : '100%';
				// mobile
				$mobile_box_width = ($search_box_settings['width']['mobile']) ? $search_box_settings['width']['mobile'] : '100%';

				// outer box

				$search_box_height = ($search_box_settings['height']) ? $search_box_settings['height'] : 'auto';
				$search_box_bg_color = ($search_box_settings['bg_color']) ? $search_box_settings['bg_color'] : '';
				$search_box_padding = ($search_box_settings['margin']['top']) ? $search_box_settings['margin']['top'].'px' : 'auto';
				
				$search_box_border_type = ($search_box_settings['border_type']) ? $search_box_settings['border_type'] : 'solid';
				$search_box_border_color = ($search_box_settings['border_color']) ? $search_box_settings['border_color'] : '';

				$search_box_border = ($search_box_settings['border_px']) ? $search_box_settings['border_px'].'px '.$search_box_border_type.' '.$search_box_border_color : '0';

				$search_box_border_radius_top = ($search_box_settings['border_radius']) ? $search_box_settings['border_radius']['top'].'px' : '0px';

				$search_box_border_radius_right = ($search_box_settings['border_radius']) ? $search_box_settings['border_radius']['right'].'px' : '0px';

				$search_box_border_radius_bottom = ($search_box_settings['border_radius']) ? $search_box_settings['border_radius']['bottom'].'px' : '0px';

				$search_box_border_radius_left = ($search_box_settings['border_radius']) ? $search_box_settings['border_radius']['left'].'px' : '0px';

				$search_box_border_radius = $search_box_border_radius_top.' '.$search_box_border_radius_right.' '.$search_box_border_radius_bottom.' '.$search_box_border_radius_left;


				// search input

				$search_input_font_size = ($search_input_settings['font_size']) ? $search_input_settings['font_size'].'px' : '12px';
				$search_input_line_height = ($search_input_settings['line_height']) ? $search_input_settings['line_height'].'px' : '12px';
				$search_input_font_color = ($search_input_settings['font_color']) ? $search_input_settings['font_color'] : '#000000';
				$search_input_bg_color = ($search_input_settings['bg_color']) ? $search_input_settings['bg_color'] : 'transparent';

				$search_input_border_type = ($search_input_settings['border_type']) ? $search_input_settings['border_type'] : 'solid';
				$search_input_border_color = ($search_input_settings['border_color']) ? $search_input_settings['border_color'] : '';

				$search_input_border = ($search_input_settings['border_px']) ? $search_input_settings['border_px'].'px '.$search_input_border_type.' '.$search_input_border_color : '0';

				$search_input_border_radius_top = ($search_input_settings['border_radius']) ? $search_input_settings['border_radius']['top'].'px' : '0px';

				$search_input_border_radius_right = ($search_input_settings['border_radius']) ? $search_input_settings['border_radius']['right'].'px' : '0px';

				$search_input_border_radius_bottom = ($search_input_settings['border_radius']) ? $search_input_settings['border_radius']['bottom'].'px' : '0px';

				$search_input_border_radius_left = ($search_input_settings['border_radius']) ? $search_input_settings['border_radius']['left'].'px' : '0px';

				$search_input_border_radius = $search_input_border_radius_top.' '.$search_input_border_radius_right.' '.$search_input_border_radius_bottom.' '.$search_input_border_radius_left;


				// loader icon

				$loader_icon_color = $loader_settings['color'];

				// magnifire icon

				$magnifire_icon_color = $magnifire_settings['color'];
				$magnifire_icon_bg_color = $magnifire_settings['bg_color'];
				$magnifire_icon_position = $magnifire_settings['position'];

				// search button

				$search_button_font_size = $search_button_settings['font_size'].'px';
				$search_button_font_color = $search_button_settings['font_color'];
				
			wp_register_style( 'advance-search-commonform-css', false );
			wp_enqueue_style( 'advance-search-commonform-css' );
			wp_add_inline_style(
				'advance-search-commonform-css',
           '.wpas_form_container_'.esc_attr($search_form->id).' {
           		width: '.esc_attr($desktop_box_width).';
           		max-width: '.esc_attr($desktop_box_width).';
           		background-color: '.esc_attr($search_box_bg_color).';
           		padding: '.esc_attr($search_box_padding).';
           		border: '. esc_attr($search_box_border).';
           		border-radius: '.esc_attr($search_box_border_radius).';
           		
           }
           .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_input_container{
           		width: '.esc_attr($desktop_box_width).';
           		max-width: '. esc_attr($desktop_box_width).';
           		height: '.esc_attr( $search_box_height).'px;
           		overflow:hidden;
           	    position: relative;
           }
           .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper form {
           	margin: 0px;
           	height:100%;
           	position: relative;
           	display: block;
           }
           .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper form .input_cont {
	          width: 100%;
	          display: flex;
           }
           .wpas_form_container_'. esc_attr($search_form->id).' .wpas_wrapper form .input_cont.left{
           	flex-direction: row-reverse;
           }
           .wpas_search_result{
			    width: 100%;
			    font-size: 14px;
			    color: #444;
			    max-height: 300px;
			    overflow: auto;
			    border: 2px solid #ccc;
			    border-radius: 4px;
			    margin-top: 0px;
			    display: none;
			    background-color: #fff;
			    z-index: 9999999;
			}
           
           	.wpas_search_result > div{
           	  	padding: 5px 12px;
           	}
            .wpas_search_result > div a{
           		color:#444;
           		text-decoration: none;
           	}
           	.wpas_form_container_'.esc_attr( $search_form->id).' .wpas_wrapper .input_box{
           		width:100%;
           		float: left;
           		position: relative;
           	}
           .wpas_form_container_'.esc_attr( $search_form->id).' .wpas_wrapper input[type=search] {
           	font-size:'.esc_attr($search_input_font_size).';
           	padding: 5px;
           	height: '.esc_attr($search_box_height).'px;
           	border: '.esc_attr($search_input_border).';
           	border-radius: '.esc_attr($search_input_border_radius).';
           	line-height: '.esc_attr($search_input_line_height).';
           	background-color: '.esc_attr($search_input_bg_color).';
           	color: '.esc_attr($search_input_font_color).' !important;
           	float: left;
           	width:100%;
           	padding-left: 12px;
           	margin-top: 0px;
           	
           }
		   .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper input[type=search]:focus {
				outline: none !important;
			}
           .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper input[type=search]::-webkit-input-placeholder {
           	color: '.esc_attr($search_input_font_color).' !important;
           }

            .wpas_form_container_'.esc_attr($search_form->id).' .voice-search-wrapper.wpas_voice_search .wpas_search_loader_icon, .wpas_form_container_'. esc_attr($search_form->id).' .voice-search-wrapper.wpas_voice_search .wpas_search_close {
			    right: 35px;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_input_container .input_box .voice-search-button {
				right: 0px !important;
			}

          
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_submit_wrapper {
			    width:auto;
			    height:100%;
			    float: right;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_submit_wrapper .wpas_margnifire_icon{
			    background:'.esc_attr($magnifire_icon_bg_color).';
			    height: '.esc_attr($search_box_height).'px;
			    transform: translate(0px, 0px);
			    line-height: '.esc_attr($search_box_height).'px;
			    text-align: center;
			    padding: 0 12px;
                color: #fff;
                font-size: 14px;
                font-family: arial;
                display: block ruby;
                white-space: nowrap;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_submit_wrapper .wpas_margnifire_icon span.text_search{
				padding-right: 5px;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_submit_wrapper .wpas_margnifire_icon .new_icon_div{
				 position: absolute;
				 top:50%;
				 transform: translate(0,-50%);
				 width: 100%;
				 text-align:center;
				 left:auto;
				 right:auto;
				  padding:0px 10px;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_submit_wrapper .wpas_margnifire_icon i.fa {
				color: '.esc_attr($magnifire_icon_color).';
			    font-size:'.esc_attr($search_button_font_size).';
			    display: inline-block;
			}

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_submit_wrapper .button_text_holder {
				color:'.esc_attr($search_button_font_color).';
			    font-size:'.esc_attr($search_button_font_size).';
			    margin-left: 5px;
			    display: inline-block;
                line-height: normal;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper 
			.wpas_search_loader_icon {
			    float: left;
			    z-index: 99999;
			    position: absolute;
			    top: 50%;
                right:10px;
                transform: translate(0,-50%);			
            }


			/*********** loader color and size ***********/

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper 
			.wpas_search_loader_icon .lds-hourglass {
			  display: inline-block;
			  position: relative;
			  width: 30px;
			  height: 30px;
			  margin-right: 10px;
			}
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper 
			.wpas_search_loader_icon .lds-hourglass:after {
			  content: " ";
			  display: block;
			  border-radius: 50%;
			  width: 0;
			  height: 0;
			  box-sizing: border-box;
			  border: 18px solid '.esc_attr($loader_icon_color).';
			  border-color: '.esc_attr($loader_icon_color).' transparent '.esc_attr($loader_icon_color).' transparent;
			  animation: lds-hourglass 1.2s infinite;
			}
			@keyframes lds-hourglass {
			  0% {
			    transform: rotate(0);
			    animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
			  }
			  50% {
			    transform: rotate(900deg);
			    animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
			  }
			  100% {
			    transform: rotate(1800deg);
			  }
			}

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper 
			.wpas_search_loader_icon .sbl-circ {
			  height: 25px;
			  width: 25px;
			  color: '.esc_attr($loader_icon_color).';
			  position: relative;
			  display: inline-block;
			  border: 3px solid;
			  border-radius: 50%;
			  border-top-color: transparent;
			  animation: rotate 1s linear infinite; float: right;}

			@keyframes rotate {
			  0% {
			    transform: rotate(0); }
			  100% {
			    transform: rotate(360deg); } }


			/**********************/

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper 
			.wpas_search_loader_icon .sbl-circ-path {
			  height: 30px;
			  width: 30px;
			  color: rgba(90, 90, 90, 0.2);
			  position: relative;
			  display: inline-block;
			  border: 4px solid;
			  border-radius: 50%;
			  border-right-color: '.esc_attr($loader_icon_color).';
			  animation: rotate 1s linear infinite; }

			@keyframes rotate {
			  0% {
			    transform: rotate(0); }
			  100% {
			    transform: rotate(360deg); } }


			/**************/

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper 
			.wpas_search_loader_icon .sbl-sticks-spin {
			  height: 30px;
			  width: 3px;
			  background: '.esc_attr($loader_icon_color).';
			  position: relative;
			  display: inline-block;
			  border-radius: 5px;
			  animation: animateSticks1 3s ease infinite; }
			  .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .sbl-sticks-spin::before, .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .sbl-sticks-spin::after {
			    height: inherit;
			    width: inherit;
			    content: "";
			    display: block;
			    background: inherit;
			    position: absolute;
			    border-radius: 4px; }
			  .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .sbl-sticks-spin::before {
			    left: 0;
			    animation: animateSticks2 1s .5s ease infinite;
			 }
			  .wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .sbl-sticks-spin::after {
			    right: 0;
			    animation: animateSticks3 1s 1s ease infinite; }

			@keyframes animateSticks1 {
			  0% {
			    transform: rotate(0deg); }
			  25% {
			    transform: rotate(-90deg); }
			  50% {
			    transform: rotate(180deg); }
			  75% {
			    transform: rotate(90deg); }
			  100% {
			    transform: rotate(0); } }

			@keyframes animateSticks2 {
			  0% {
			    transform: rotate(0deg); }
			  50%, 100% {
			    transform: rotate(55deg); } }

			@keyframes animateSticks3 {
			  0% {
			    transform: rotate(0deg); }
			  50%, 100% {
			    transform: rotate(115deg); } }

			@keyframes rotate {
			  0% {
			    transform: rotate(0); }
			  100% {
			    transform: rotate(360deg); } }

			/**********************/

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .loader04 {
			  width: 30px;
			  height: 30px;
			  border: 2px solid '.esc_attr($loader_icon_color).';
			  border-radius: 50%;
			  position: relative;
			  animation: loader-rotate 1s ease-in-out infinite;
			 }
			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .loader04::after {
			    content: " ";
			    width: 10px;
			    height: 10px;
			    border-radius: 50%;
			    background: '.esc_attr($loader_icon_color).';
			    position: absolute;
			    top: -6px;
			    left: 50%;
			    margin-left: -5px; }

			@keyframes loader-rotate {
			  0% {
			    transform: rotate(0); }
			  100% {
			    transform: rotate(360deg); } }

			.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_search_loader_icon .loader05 {
			  width: 35px;
			  height: 35px;
			  border: 4px solid '.esc_attr($loader_icon_color).';
			  border-radius: 50%;
			  position: relative;
			  animation: loader-scale 1s ease-out infinite;
			 }

			 /* clears the ‘X’ InternetExplore */
			 .wpas-advanced-search-form-container .input_box input[type=search]::-ms-clear { display: none; width : 0; height: 0; }
			.wpas-advanced-search-form-container .input_box input[type=search]::-ms-reveal { display: none; width : 0; height: 0; }

			/* clears the ‘X’ from Chrome */
			.wpas-advanced-search-form-container .input_box input[type="search"]::-webkit-search-decoration,
			.wpas-advanced-search-form-container .input_box input[type="search"]::-webkit-search-cancel-button,
			.wpas-advanced-search-form-container .input_box input[type="search"]::-webkit-search-results-button,
			.wpas-advanced-search-form-container .input_box input[type="search"]::-webkit-search-results-decoration { display: none; }
			@keyframes loader-scale {
			  0% {
			    transform: scale(0);
			    opacity: 0; }
			  50% {
			    opacity: 1; }
			  100% {
			    transform: scale(1);
			    opacity: 0; } }
				
			/* Ipad view  */
			@media(min-width:768px) and (max-width:1024px){
				.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_input_container{
					width: '.esc_attr($tablet_box_width).';
					max-width: '.esc_attr($tablet_box_width).';
				}
			}
			/* Mobile view  */
			@media(max-width:767px){
				.wpas_form_container_'.esc_attr($search_form->id).' .wpas_wrapper .wpas_input_container{
					width: '.esc_attr($mobile_box_width).';
					max-width: '.esc_attr($mobile_box_width).';
				}
			}'

		);
		$i++;
		} // end foreach
	}
    
    }
 
	// replace default wp search

	public function wpas_replace_default_search() {
		$theme_search_form_key = $this->plugin_name.'_default_search';
		$woo_form_key = $this->plugin_name.'_default_woo_search';
		$default_search_form_id = get_option($theme_search_form_key);
		// if default form selected

		if($default_search_form_id > 0 ) {
			add_action('wp_footer', array($this, 'default_theme_search_form_setting'));
		}

	}

	public function default_theme_search_form_setting() {
		$s_form_key = $this->plugin_name.'_default_search';
		$default_search_form_id = get_option($s_form_key);

		if($default_search_form_id > 0) {

		global $wpdb;
		$search_form_table = $wpdb->prefix."wpas_index";
		$search_form_setting = $wpdb->get_row($wpdb->prepare("SELECT * FROM $search_form_table where id=%d",$default_search_form_id));
		$settings = json_decode($search_form_setting->data, true);

		wp_register_script( 'advance-search-formcommon-js', '');
		wp_enqueue_script( 'advance-search-formcommon-js' );
		wp_add_inline_script(
			'advance-search-formcommon-js',
	
			"jQuery(document).ready(function() {
				var speechInputWrappers = document.querySelectorAll('form.search-form:not(.wpas_search_form)');
				
				if(speechInputWrappers.length == 0){

				 speechInputWrappers = document.querySelectorAll('form.wp-block-search:not(.wpas_search_form)');
				}
				[].forEach.call(speechInputWrappers, function (speechInputWrapper) {
					// Try to show the form temporarily so we can calculate the sizes
					var speechInputWrapperStyle = speechInputWrapper.getAttribute('style');
					speechInputWrapper.setAttribute('style', speechInputWrapperStyle);

					// Find the search input
					var inputEl = speechInputWrapper.querySelector('input[name=s]');
					var inputSubmit = speechInputWrapper.querySelector('input[type=submit]');
					if(inputSubmit == null || inputSubmit == ''){

						 inputSubmit = speechInputWrapper.querySelector('button[type=submit]');
					}
					if (null === inputEl) {
						inputEl = speechInputWrapper.querySelector('input[name=search]');
					}
					if (null === inputEl) {
						// Reset form style again
						speechInputWrapper.setAttribute('style', speechInputWrapperStyle);

						return;
					}
					inputEl.classList.add('wpas_search_input');
					inputEl.classList.add('search_form_added');
					inputEl.setAttribute('data-formid', ". intval($default_search_form_id). ");
					inputEl.setAttribute('style', 'margin-bottom: 0;');
					inputEl.setAttribute('autocomplete', 'off');
					inputSubmit.classList.add('wpas_search_submit');
					inputSubmit.setAttribute('style', 'margin-bottom: 0;pointer-events: none;');
					var classes = speechInputWrapper.getAttribute('class');					
					speechInputWrapper.classList.add('search_form_". intval($default_search_form_id)."');
					var innerDiv = document.createElement('div');
					innerDiv.className = 'wpas_search_result';
					speechInputWrapper.appendChild(innerDiv);
				});
			});
            jQuery(window).ready(function() { 
              jQuery('.search_form_added').on('keypress', function (event) { 
                  var keyPressed = event.keyCode || event.which; 
                  if (keyPressed === 13) { 
                      event.preventDefault(); 
                      return false; 
                  } 
              	}); 
              })"
			);
		}
	}

	// return dyanamic form

	public function WPAS_Advanced_Search_Form() {

		$s_form_key = $this->plugin_name.'_default_search';
		$default_search_form_id = get_option($s_form_key);
		$default_search_form_id = intval($default_search_form_id);
		if($default_search_form_id > 0 ) {
			$search_form = do_shortcode("[wpas id=".$default_search_form_id."]");
			return $search_form;
		}

	}
}