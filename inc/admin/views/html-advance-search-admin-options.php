<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @package    WPAS_Advance_Search
 * @subpackage WPAS_Advance_Search/inc/admin/views
 */

if ( ! defined( 'ABSPATH' ) && !current_user_can( 'manage_options' )) {
	exit; // Exit if accessed directly
}


$search_form_id = isset($_GET['wpas_id']) ? intval($_GET['wpas_id']) : 0;

global $wpdb;
$search_form_table = $wpdb->prefix."wpas_index";
$search_form_setting = $wpdb->get_row($wpdb->prepare("SELECT * FROM $search_form_table where id=%d", $search_form_id));

if($search_form_id == 0 || $wpdb->num_rows == 0) {
	_e("Search form not found !", 'advance-search');
	exit;
}

$form_name = $search_form_setting->name;

if($wpdb->num_rows > 0) {

$settings = json_decode($search_form_setting->data, true);

$args = array(
	'public' => true,
	
);
$advance_search_excludeTaxonomy = array('product_shipping_class');
// taxonomies args
$taxonomies_args = array(
  'public'   => true,
  
); 

// append posts and pages to the post types.
$post_types = get_post_types( $args, 'objects' );

unset($post_types['attachment']); // exclude attachment from loop

$output = 'names'; // or objects
$operator = 'and'; // 'and' or 'or'
$taxonomies = get_taxonomies( $taxonomies_args, $output, $operator );

unset($taxonomies['post_format']);

?>
<?php
    include_once('wpas_popup.php');
?>

<div class="wrap">
<div class="accordion_section advance_serach_sec">
	<div class="wpas_search">

	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="">
		<input type="hidden" name="action" value="wpas_search_form_settings">
		<input type="hidden" name="wpas-search_setting" value="<?php echo wp_create_nonce('search_form_settings'); ?>" />
		<input type="hidden" name="search_form_setting[form_id]" value="<?php echo intval($search_form_id); ?>">

	<div class="heading">
		<h3><?php echo esc_html( get_admin_page_title() ); ?></h3>
		<div class="imp_link">
			<a class="back" href="<?php echo esc_url(admin_url().'admin.php?page='.$this->plugin_name); ?>"><?php _e('Back to the search list', 'advance-search'); ?></a>
			<a class="statistics" href="<?php echo esc_url(admin_url().'admin.php?page=wpas-statistics'); ?>"><?php _e('Search Statistics', 'advance-search'); ?></a>
			<a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro ', 'advance-search'); ?> <i class="fa fa-diamond" aria-hidden="true"></i></a>
		</div>
	</div>

	<div class="wpas_search_left">

	<?php 
	if(isset($_GET["msg"])){
		$msg = intval($_GET["msg"]);
		$wpas_id = intval($_GET["wpas_id"] );
		switch($msg){
			case 0:
				?>
				<div class="error notice is-dismissible">
					<p><?php _e( 'You haven\'t made any changes in settings to be saved.', 'advance-search' ); ?><button type="button" id="ad_dismiss" class="notice-dismiss" data_url="<?php echo esc_url(admin_url('admin.php?page=advance-search')."&wpas_id=".$wpas_id);?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'advance-search' ); ?></span></button></p>
				</div>
				<?php
			break;
			case 1:
				?>
				<div class="updated notice is-dismissible">
					<p><?php _e( 'Settings updated successfully.', 'advance-search' ); ?><button type="button" id="ad_dismiss" class="notice-dismiss" data_url="<?php echo esc_url(admin_url('admin.php?page=advance-search')."&wpas_id=".$wpas_id);?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'advance-search' ); ?></span></button></p>
				</div>
				<?php
			break;
			case 2:
				?>
				<div class="updated notice is-dismissible">
					<p><?php _e( 'Settings has been restored successfully.', 'advance-search' ); ?><button type="button" id="ad_dismiss" class="notice-dismiss" data_url="<?php echo esc_url(admin_url('admin.php?page=advance-search')."&wpas_id=".$wpas_id);?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'advance-search' ); ?></span></button></p>
				</div>
				<?php
			break;
		}
	}
	?>
		<ul class="accordion">
		  <li>
		    <a class="toggle" href="#"><i class="fa fa-cogs" aria-hidden="true"></i> <?php _e('Shortcode for ', 'advance-search'); ?><b><?php echo esc_attr($form_name); ?></b></a>
		    <div class="inner shortcode_inputSec">
		    	<h4 class="title_heading">
					<?php _e( 'Simple Shortcode', 'advance-search' ); ?>
				</h4>
		    	<?php
		    		$simple_shortcode_template = '<?php echo do_shortcode("[wpas id='.intval($search_form_id).' title=\''.esc_attr($form_name).'\']"); ?>';
		    	?>
              <div class="shortCol">
				<label><?php _e( 'Search Shortcode:', 'advance-search' ); ?></label>
				<input type="text" value="[wpas id=<?php echo intval($search_form_id); ?>]" readonly="readonly">
		        </div>
                 <div class="shortCol">
		     	<label><?php _e( 'Add title for Search:', 'advance-search' ); ?></label><br/>
		     	<input type="text" value="[wpas id=<?php echo intval($search_form_id); ?> title='<?php echo esc_attr($form_name); ?>']" readonly="readonly">
				</div>	
				<h4><?php _e( 'Extra for php template use', 'advance-search' ); ?></h4>
				<?php highlight_string($simple_shortcode_template); ?>
		    </div>
		  </li>
		  
		  <li id="advance_search_posttype_chkbox">
		    <a class="toggle" href="#"><i class="fa fa-list" aria-hidden="true"></i> <?php _e( 'Post Types', 'advance-search' ); ?></a>
		    <div class="inner">
		    <h4 class="title_heading">
				<?php _e( 'Select Post Types to Include in the Advanced Search', 'advance-search' ); ?>
			</h4>
		
			<?php
				foreach ( $post_types  as $post_type ) {
					$the_post_type = $post_type->name;
					$the_post_type_label = $post_type->label;
					?>
					<fieldset class="checkbox_toggleSec">
						<legend class="screen-reader-text"><span><?php _e( 'Setting for ', 'advance-search' ) . $the_post_type_label; ?></span></legend>

						<label for="<?php echo esc_attr( $this->plugin_name . '_' . $the_post_type ); ?>">
							<div>
								
					        <label class="el-switch el-switch-blue">
						        <input type="checkbox" data-ptag="postSearch" class="checkarea" id="<?php echo esc_attr( $this->plugin_name . '-' . $the_post_type ); ?>" class="postCheckbox" name="search_form_setting[post_types][post_types][<?php echo esc_attr( $the_post_type ); ?>]" value="<?php echo esc_attr( $the_post_type ); ?>" <?php if(array_key_exists('post_types', $settings['post_types'])) { if (in_array($the_post_type, $settings['post_types']['post_types'])) { echo 'checked="checked"'; } } ?> />
						        <span class="el-switch-style"></span>
					        </label>
							<span class="title"><?php echo esc_attr( $the_post_type_label ); ?>
							</span>
						 </div></label> 
					</fieldset>
				<?php
				} // post type foreach end
			?>

		<hr class="section_seprate search_box_expand" />
		
		<div class="search_areas sep_section search_box_expand" id="postSearch">
			<h4 class="title_heading">
				<?php _e('Search Area', 'advance-search' ); ?>
			</h4>

			<p class="content_Style"><?php _e('* By enable specific area of search, search result relative from specific area i.e title, description etc.', 'advance-search' ); ?></p>
			<fieldset class="checkbox_toggleSec">
				<label for="<?php echo esc_attr( $this->plugin_name . '_post_search_title' ); ?>">
					<div>
					  <label class="el-switch el-switch-blue">
						  <input type="checkbox" class="subcheckbox subposting" data-check="subposting" id="<?php echo esc_attr( $this->plugin_name . '-_post_search_title' ); ?>" name="search_form_setting[post_types][search_areas][]" value="<?php echo esc_attr('post_title'); ?>" <?php if(array_key_exists('search_areas', $settings['post_types'])) { if (in_array('post_title', $settings['post_types']['search_areas'])) { echo 'checked="checked"'; } } ?> />
						  <span class="el-switch-style"></span>
					  </label>
					<span class="title"><?php _e( 'Search in Title', 'advance-search' ); ?></span>
				</div>
				</label>
			</fieldset>

			<fieldset class="checkbox_toggleSec">
				<label for="<?php echo esc_attr( $this->plugin_name . '_post_search_desc' ); ?>">
				<div>
				 <label class="el-switch el-switch-blue">
					<input type="checkbox" class="subcheckbox subposting" data-check="subposting" id="<?php echo esc_attr( $this->plugin_name . '-_post_search_desc' ); ?>" name="search_form_setting[post_types][search_areas][]" value="<?php echo esc_attr('post_content'); ?>" <?php if(array_key_exists('search_areas', $settings['post_types'])) { if (in_array('post_content', $settings['post_types']['search_areas'])) { echo 'checked="checked"'; }} ?> />
					 <span class="el-switch-style"></span>
				 </label>
					<span class="title"><?php _e( 'Search in Content', 'advance-search' ); ?></span>
				</div>
				</label>
			</fieldset>
			<hr class="section_seprate" />
		</div>

		
		
		<div class="post_meta_keyvalue_section sep_section">
			<h4 class="title_heading">
				<?php _e('Search by Post Meta key and Value', 'advance-search' ); ?>
			</h4>

			<p class="content_Style warning"><?php _e('* Search may slower by post meta key search.', 'advance-search' ); ?></p>
			
			<label><?php _e('List of Post Meta Keys', 'advance-search' ); ?></label>
				<input type="text" name="search_form_setting[post_types][meta_keys][]" value="<?php if (array_key_exists('meta_keys', $settings['post_types'])) { echo $settings['post_types']['meta_keys'][0]; } ?>" />
			<p class="content_Style"><?php _e('* Comma "," separated list of post meta keys i.e Metakey1,Metakey2 etc.', 'advance-search' ); ?></p>


		</div>

		<hr class="section_seprate" />
      
        <div class="ex_postSec buy_pro_wrapper">
			<h4 class="title_heading">
				<?php _e('Exclude post by ID', 'advance-search' ); ?>
				<span><?php _e('* This feature is available in pro version.', 'advance-search' ); ?><span>
			</h4>
				<div class="buy_pro"><a href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search' ); ?><i class="fa fa-diamond" aria-hidden="true"></i></a></div>
        	<div class="disable_section">
				<div class="element">
					<label for="<?php echo esc_attr( $this->plugin_name . '_post_search_exclude' ); ?>">
						<?php _e('Exclude Post ids', 'advance-search' ); ?> 
					</label>
					<textarea disable></textarea>
						<span class="message_info"><?php _e( 'Comma "," separated list of post IDs', 'advance-search' ); ?></span>
				</div>
				</div>
		</div>

		    </div>
		  </li>
		  
		  <li id="advance_search_taxonomy_chkbox">
		    <a class="toggle" href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> <?php _e( 'Taxonomies', 'advance-search' ); ?></a>
		    <div class="inner">
		
			<h4 class="title_heading">
				<?php _e('Select Taxonomies to Include in the Advanced Search', 'advance-search' ); ?>
			</h4>

			<?php

			if( !empty($taxonomies) ) {

				foreach ($taxonomies as $taxonomy) {
					
					if( in_array( $taxonomy, $advance_search_excludeTaxonomy ) ) {
							continue;
						}
					?>
					<fieldset class="checkbox_toggleSec">
						<legend class="screen-reader-text"><span><?php _e( 'Setting for ', 'advance-search' ) . $the_post_type_label; ?></span></legend>
						<label for="<?php echo esc_attr( $this->plugin_name . '_' . $taxonomy ); ?>">
							<div>
				            <label class="el-switch el-switch-blue">
								<input type="checkbox" data-ptag="taxonomySearch" class="checkarea" id="<?php echo esc_attr( $this->plugin_name . '-' . $taxonomy ); ?>" name="search_form_setting[taxonomies][taxonomies][<?php echo esc_attr( $taxonomy ); ?>]" value="<?php echo esc_attr( $taxonomy ); ?>" 
								<?php 
								if(!empty($settings['taxonomies']) && array_key_exists('taxonomies', $settings)) {
									 if (!empty($settings['taxonomies']['taxonomies']) &&  in_array($taxonomy, $settings['taxonomies']['taxonomies'], true)) { echo 'checked="checked"'; 
								} 
								} ?> class="taxonomyCheck" />
								 <span class="el-switch-style"></span>
				            </label>
							<span class="title"><?php
							$taxonomy = ucwords(str_replace('_', ' ', $taxonomy));
							echo esc_attr($taxonomy); ?></span>
						</div>
						</label>
					</fieldset>
					<?php
				}
			}

			?>

<hr class="section_seprate search_box_expand" />
		
		<div class="sep_section searchTaxonomy search_box_expand" id="taxonomySearch">
			<h4 class="title_heading">
				<?php _e('Search Area', 'advance-search' ); ?>
			</h4>

			<p class="content_Style"><?php _e('* By enable specific area of search, search result relative from specific area i.e title, description etc.', 'advance-search' ); ?></p>

			<fieldset class="checkbox_toggleSec">
				<label for="<?php echo esc_attr( $this->plugin_name . '_post_search_title' ); ?>">
				  <div>
				    <label class="el-switch el-switch-blue">
					<input type="checkbox" data-check="subtaxonoy" class="subcheckbox" id="<?php echo esc_attr( $this->plugin_name . '-_post_search_title' ); ?>" name="search_form_setting[taxonomies][search_areas][title]" value="<?php echo esc_attr('title'); ?>" 
					
					<?php
					
					if (!empty($settings['taxonomies']) && array_key_exists('taxonomies', (array)$settings)) { 
						if (!empty($settings['taxonomies']['search_areas']) && in_array('title', $settings['taxonomies']['search_areas'], true)) { echo 'checked="checked"';
						 } 
						} ?> />
					     <span class="el-switch-style"></span>
				    </label>
					<span class="title"><?php _e( 'Search in Title', 'advance-search' ); ?></span>
				</div>
				</label>
			</fieldset>

			<fieldset class="checkbox_toggleSec">
				<label for="<?php echo esc_attr( $this->plugin_name . '_post_search_desc' ); ?>">
				<div>
				    <label class="el-switch el-switch-blue">
					<input type="checkbox" class="subcheckbox" data-check="subtaxonoy" id="<?php echo esc_attr( $this->plugin_name . '-_post_search_desc' ); ?>" name="search_form_setting[taxonomies][search_areas][content]" value="<?php echo esc_attr('content'); ?>" <?php 
					if (!empty($settings['taxonomies']) && array_key_exists('taxonomies', (array)$settings)) { 
						if (!empty($settings['taxonomies']['search_areas']) && in_array('content', $settings['taxonomies']['search_areas'], TRUE)) { echo 'checked="checked"'; } 
					} 
					?> />
					     <span class="el-switch-style"></span>
				    </label>
					<span class="title"><?php _e( 'Search in Content', 'advance-search' ); ?></span>
				</div>
				</label>
			</fieldset>

			<hr class="section_seprate" />
		</div>

		
		
		<div class="sep_section buy_pro_wrapper">
			<h4 class="title_heading">
				<?php _e('Show/Hide empty taxonomies', 'advance-search' ); ?>
				<span><?php _e('* This feature is available in pro version.', 'advance-search' ); ?><span>
			</h4>

			<div class="buy_pro"><a href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search' ); ?><i class="fa fa-diamond" aria-hidden="true"></i></a></div>

			<p class="content_Style"><?php _e('* By enable this option empty taxonomies show in search results.', 'advance-search' ); ?></p>
		<div class="disable_section">
			<fieldset class="checkbox_toggleSec show_hide_empty_taxonomies">
				<label for="<?php echo esc_attr( $this->plugin_name . '_show_hide_empty_taxonomies' ); ?>">
				  <div>
				    <label class="el-switch el-switch-blue">
					<input type="checkbox" id="<?php echo esc_attr( $this->plugin_name . '-_show_hide_empty_taxonomies' ); ?>" disabled />
					     <span class="el-switch-style"></span>
				    </label>
					<span class="title"><?php _e( 'Show Empty Taxonomies', 'advance-search' ); ?></span>
				</div>
				</label>
			</fieldset>
		</div>
		<br/><br/>
		</div>
		    </div>
		  </li>
		  
		  <li>
		    <a class="toggle" href="#"><i class="fa fa-file-image-o" aria-hidden="true"></i> <?php _e( 'Attachments', 'advance-search' ); ?></a>
		    <div class="inner">
		    	<div class="buy_pro_wrapper">
		    <h4 class="title_heading">
				<?php _e('Select Attachment type to Include in the Advanced Search', 'advance-search' ); ?>
				<span><?php _e('* Only Image name search available in free version.', 'advance-search' ); ?><span>
			</h4>

			<p class="content_Style"><?php _e('* Select attachment type to include in search result.', 'advance-search' ); ?></p>
			<p class="content_Style"><?php _e('* By enable png, jpg, gif, zip attachment search only by title.', 'advance-search' ); ?></p>

			<?php

			$attachments = array(
				'image/jpeg'=>'Jpeg',
				'image/gif'=>'Gif',
				'image/png'=>'Png',
			);

			foreach ($attachments as $key => $value) {
				?>
				<fieldset class="checkbox_toggleSec">
					<label for="<?php echo esc_attr( $this->plugin_name . '_' . $key ); ?>">
					<div>
				    <label class="el-switch el-switch-blue">
					    <input type="checkbox" id="<?php echo esc_attr( $this->plugin_name . '-' . $key ); ?>" name="search_form_setting[attachments][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $key ); ?>" <?php if (array_key_exists('attachments', $settings)) { if (in_array($key, $settings['attachments'])) { echo 'checked="checked"'; } } ?> />
					     <span class="el-switch-style"></span>
				    </label>
						<span class="title"><?php echo esc_attr($value); ?></span>
					</div>
					</label>
				</fieldset>
				<?php
			}
			?>

			<div class="pro_version" style="position: relative;">
				
				<div class="buy_pro" style="height:100%;"><a href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search' ); ?><i class="fa fa-diamond" aria-hidden="true"></i></a></div>

				<?php
				$pro_attachments = array(
					'application/pdf'=>'PDF',
					'application/msword'=>'Word',
					'application/vnd.ms-excel'=>'Excel',
					'text/csv'=>'Csv',
					'application/zip'=>'Zip'
				);

				foreach ($pro_attachments as $key => $value) {
					?>
					<fieldset class="checkbox_toggleSec">
						<label for="<?php echo esc_attr( $this->plugin_name . '_' . $key ); ?>">
						<div>
					    <label class="el-switch el-switch-blue">
						    <input type="checkbox" disabled />
						     <span class="el-switch-style"></span>
					    </label>
							<span class="title"><?php echo esc_attr($value); ?></span>
						</div>
						</label>
					</fieldset>
					<?php
				}
				?>
			</div>
		</div>
		    </div>
		  </li>

		   <li>
		    <a class="toggle" href="#"><i class="fa fa-user" aria-hidden="true"></i> <?php _e( 'User Search', 'advance-search' ); ?> <span>* This feature is available in pro version.</span></a>

			    <div class="inner">
			    	<div class="buy_pro_wrapper">

			    <div class="buy_pro" style="height:100%;"><a href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search' ); ?><i class="fa fa-diamond" aria-hidden="true"></i></a></div>

			    <h4 class="title_heading">
					<?php _e('Enable User Search By Specific Column', 'advance-search' ); ?>
				</h4>
			     
			     <!-- <p class="content_Style">* .</p> -->

				<fieldset class="checkbox_toggleSec toggle2">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_fname' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
							<input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php _e( 'By First Name', 'advance-search' ); ?></span>
					</div>
					</label>
				</fieldset>

				<fieldset class="checkbox_toggleSec toggle2">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_lname' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
					     	<input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php _e( 'By Last Name', 'advance-search' ); ?></span>
					</div>
					</label>
				</fieldset>

				<fieldset class="checkbox_toggleSec toggle2">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_login_name' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
					    <input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php _e( 'By Login Name', 'advance-search' ); ?></span>
					</div>
					</label>
				</fieldset>

				<fieldset class="checkbox_toggleSec toggle2">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_display_name' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
					    <input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php _e( 'By Display Name', 'advance-search' ); ?></span>
					</div>
					</label>
				</fieldset>

				<fieldset class="checkbox_toggleSec toggle2">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_email' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
					         <input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php _e( 'By Email', 'advance-search' ); ?></span>
					</div>
					</label>
				</fieldset>

				<fieldset class="checkbox_toggleSec toggle2">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_user_bio' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
					    	<input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php _e( 'By User Bio', 'advance-search' ); ?></span>
					</div>
					</label>
				</fieldset>

				<hr class="section_seprate" />
		
		<div class="sep_section">
			<h4 class="title_heading">
				<?php _e('Search by Specific Role', 'advance-search' ); ?>
			</h4>

				<?php
				$editable_roles = get_editable_roles();
			    foreach ($editable_roles as $role => $details) {
			    	?>
			    <fieldset class="checkbox_toggleSec">
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_by_role' ); ?>">
						<div>
				          <label class="el-switch el-switch-blue">
					    	<input type="checkbox" disabled />
					           <span class="el-switch-style"></span>
				          </label>
						<span class="title"><?php echo esc_attr($details['name']); ?></span>
					</div>
					</label>
				</fieldset>
			    <?php
			    } // end user role foreach
				?>
			</div>

		<hr class="section_seprate" />
		
		<div class="sep_section exclude_Sec">
			<h4 class="title_heading">
				<?php _e('Exclude user By ID', 'advance-search' ); ?>
			</h4>
					<label for="<?php echo esc_attr( $this->plugin_name . '_user_search_exclude' ); ?>"></label>
						<input type="text" disabled />
						<p class="content_Style"><?php _e( 'Comma "," separated list of user IDs', 'advance-search' ); ?>
						</p>
				</div>
	</div>
			    </div>
		  	</li>

		  <li>
		    <a class="toggle" href="#"><i class="fa fa-microphone" aria-hidden="true"></i> <?php _e( 'Voice Search', 'advance-search' ); ?> <span>* <?php _e( 'This feature is available in pro version.', 'advance-search' ); ?> </span></a>
		    <div class="inner">

		    	<div class="buy_pro_wrapper">

		    <h4 class="title_heading">
				<?php _e('Enable Voice Search', 'advance-search' ); ?>
			</h4>

			<div class="buy_pro" style=""><a href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search' ); ?><i class="fa fa-diamond" aria-hidden="true"></i></a></div>


			<p class="content_Style"><?php _e('* By enable voice option user can search by voice from frontend.', 'advance-search' ); ?></p>

			<p class="content_Style warning"><?php _e('* Voice search support only working on those web browsers which supports webkitSpeechRecognition API..', 'advance-search' ); ?></p>

			<fieldset class="checkbox_toggleSec toggle2">
				<label for="<?php echo esc_attr( $this->plugin_name . '_voice_search' ); ?>">
				<div>
				 <label class="el-switch el-switch-blue">
					<input type="checkbox" disabled />
					  <span class="el-switch-style"></span>
				 </label>
					<span class="title"><?php _e( 'Enable Voice Search ', 'advance-search' ); ?></span>
				</div>
				</label>
			</fieldset>
			</div>
		    </div>
		  </li>

		  <li>
		    <a class="toggle" href="#"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e( 'Theme & Styling', 'advance-search' ); ?></a>	    
		    <div class="inner">
		    <?php
		    	$border_type = array('none','hidden', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset');
		    ?>
              <div class="themeStyleSec">
		      
		      <h4 class="title_heading alert"><?php _e('Style is not implemented if this search is set as default search.', 'advance-search' ); ?></h4>

		      <h4 class="title_heading"><?php _e( 'Overall Box layout', 'advance-search' ); ?></h4>

		      <div class="search_box_layout" style="float: left;">
		      	<div class="search_box_width searchBox_info">
		      		<h5><?php _e( 'Search Box Widths', 'advance-search' ); ?></h5>
		      		<ul>
					
		      		<li>
		      		<div class="col_liStle"><span class="tooltip" title="Desktop"><i class="fa fa-desktop" aria-hidden="true"></i></span>
					<input type="text" name="search_form_setting[styling][search_box_outer][width][desktop]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['width']['desktop']); ?>" class="restricted"/></div>
					</li>
					
		      		<li><div class="col_liStle"><span class="tooltip" title="Tablet"><i class="fa fa-tablet" aria-hidden="true"></i></span><input type="text" name="search_form_setting[styling][search_box_outer][width][tablet]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['width']['tablet']); ?>" class="restricted"/></div></li>
					
		      		<li><div class="col_liStle"><span class="tooltip" title="Mobile"><i class="fa fa-mobile" aria-hidden="true"></i></span><input type="text" name="search_form_setting[styling][search_box_outer][width][mobile]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['width']['mobile']); ?>" class="restricted"/></div></li>
					
		      		</ul>
		      	</div>
				
				<div class="search_box_height searchBox_info">
		      	<h5><?php _e( 'Search Box Height', 'advance-search' ); ?></h5>
				  	<ul class="searchMBox">
		      			<li>  
							<div class="col_liStle">
							<input type="text" minlength="2" name="search_form_setting[styling][search_box_outer][height]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['height']); ?>" class="restricted"/><span class="pxValue"><?php _e('px', 'advance-search'); ?></span>
							</div>
						</li>	
					</ul>
		      	</div>

		      	<div class="search_box_margin searchBox_info">
		      	<h5><?php _e( 'Search Box Margin', 'advance-search' ); ?></h5>
		      	<ul class="searchMBox">
		      		<li>
		      			<div class="col_liStle"><i class="fa fa-arrow-up" aria-hidden="true" title="Top"></i>
		      				<input type="number" name="search_form_setting[styling][search_box_outer][margin][top]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['margin']['top']); ?>" /><span class="pxValue"><?php _e('px', 'advance-search'); ?></span></div>
		      	    </li>
                    
		       	</ul>
		      	</div>

		      	<div class="search_box_bg_color searchBox_info">
		      		<h5><?php _e( 'BackGround Color', 'advance-search' ); ?></h5>
					<ul class="searchMBox">
		      			<li>  
							<input type="text" class="wpas_color_field" name="search_form_setting[styling][search_box_outer][bg_color]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['bg_color']); ?>" />
						</li>
					</ul>
				</div>

		      	<div class="search_box_border searchBox_info">
                  <h5><?php _e( 'Border Type', 'advance-search' ); ?></h5>
		      	<ul class="searchMBox" id="InputBorderBox">
		      		<li>
		      		<div class="col_liStle">
		      		<div class="box_dropdown_advance">
		      			<select name="search_form_setting[styling][search_box_outer][border_type]" id="BoxLayoutBorder" class="smaller _xx_style_xx_ borderType">
		      				<?php
		      				foreach ($border_type as $key => $value) {
		      					if($value == $settings['styling']['search_box_outer']['border_type']) {
		      				?>
		      					<option value="<?php echo esc_attr($value); ?>" selected="selected"><?php echo esc_attr($value); ?></option>
		      				<?php
		      					}
		      					else {
		      						?>
		      					<option value="<?php echo esc_attr($value); ?>"><?php echo  esc_attr($value); ?></option>	
		      						<?php
		      					}
		      				}
		      				?>
                        </select>
		      		</div>
		      	</div>
		      	</li>
                 <li>
                 	<div class="col_liStle hideBorder">
		      		<label><?php _e( 'Width:', 'advance-search' ); ?></label>
		      		 <input class="input_style" type="number" name="search_form_setting[styling][search_box_outer][border_px]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['border_px']); ?>"><span class="pxValue nwPxValue"><?php _e('px', 'advance-search'); ?></span>
		      	</div>
		      	</li>
                  
                  <li>
                  	<div class="col_liStle hideBorder">
		      		<label><?php _e( 'Border Color:', 'advance-search' ); ?></label>
		      			<input type="text" name="search_form_setting[styling][search_box_outer][border_color]" class="wpas_color_field" value="<?php echo esc_attr($settings['styling']['search_box_outer']['border_color']); ?>">
		      		</div>
					  </li>
		      		</ul>
		      		<h5><?php _e( 'Border Radius ', 'advance-search' ); ?></h5>
		      		 <ul class="searchMBox newBoxSearch">
		      		 	<li>
		      			<div class="col_liStle"><i class="fa fa-arrow-up" title="Top" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_box_outer][border_radius][top]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['border_radius']['top']); ?>" class="restricted"><span class="pxValue"><?php _e('px', 'advance-search'); ?></span></div>
		      		</li>
		      		<li>
		      			<div  class="col_liStle"><i class="fa fa-arrow-right" title="Right" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_box_outer][border_radius][right]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['border_radius']['right']); ?>" class="restricted"><span class="pxValue"><?php _e('px', 'advance-search'); ?></span></div>
		      		</li>
		      		<li>
		      			<div  class="col_liStle"><i class="fa fa-arrow-down" title="Bottom" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_box_outer][border_radius][bottom]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['border_radius']['bottom']); ?>" class="restricted"><span class="pxValue"><?php _e('px', 'advance-search'); ?></span></div>
		      		</li>
		      		<li>
		      		<div class="col_liStle"><i class="fa fa-arrow-left" title="Left" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_box_outer][border_radius][left]" value="<?php echo esc_attr($settings['styling']['search_box_outer']['border_radius']['left']); ?>" class="restricted"><span class="pxValue">px</span></div>
		      		</li>
		      		</ul>

		      	</div>
		      </div>
		      
		      <hr class="section_seprate" />

		      <div class="search_input_layout search_box_layout" style="float: left;">

               <h4 class="title_heading"><?php _e( 'Search Input Design', 'advance-search' ); ?></h4>

              <div class="searchBox_info">
               <ul class="searchMBox">
               	<li>
		      <div class="col_liStle">
		      	<label><?php _e( 'Search Input Background Color:', 'advance-search' ); ?></label>
		      	<input type="text" name="search_form_setting[styling][search_input][bg_color]" class="wpas_color_field" value="<?php echo esc_attr($settings['styling']['search_input']['bg_color']); ?>">
		      </div>
		    </li>
		   </ul>
		  </div>

		  <div class="searchBox_info">
		      <h5><?php _e( 'Input Font Style', 'advance-search' ); ?></h5>
		      <ul class="searchMBox">
		      <li>
		      <div  class="col_liStle"><label><?php _e( 'Search Input Font Color :', 'advance-search' ); ?></label> 
		      	<input type="text" name="search_form_setting[styling][search_input][font_color]" class="wpas_color_field" value="<?php echo esc_attr($settings['styling']['search_input']['font_color']); ?>"></div>
		      </li>
              <li>
		      <div  class="col_liStle"><label><?php _e( 'Font size:', 'advance-search' ); ?></label> <input class="restricted input_style" type="text" name="search_form_setting[styling][search_input][font_size]" value="<?php echo esc_attr($settings['styling']['search_input']['font_size']); ?>" min="0"><span class="pxValue nwPxValue"><?php _e('px', 'advance-search'); ?></span></div>
		     </li>
		     <li>
		      <div class="col_liStle"><label><?php _e( 'Line Height:', 'advance-search' ); ?></label> <input class="restricted input_style" type="text" name="search_form_setting[styling][search_input][line_height]" value="<?php echo esc_attr($settings['styling']['search_input']['line_height']); ?>" min="0" ><span class="pxValue nwPxValue"><?php _e('px', 'advance-search'); ?></span></div>
		    </li>
		     </ul>
             </div>


 <div class="searchBox_info">
   <h5><?php _e( 'Input Border', 'advance-search' ); ?></h5>
          <ul class="searchMBox" id="InputBorder">
          	<li>
          		<div class="col_liStle">
		      	<label><?php _e( 'Border Type:', 'advance-search' ); ?></label>
		      	 <div class="box_dropdown_advance">
		      			<select name="search_form_setting[styling][search_input][border_type]" id="InputLayoutBorder" class="smaller _xx_style_xx_ borderType">
                            <?php
		      				foreach ($border_type as $key => $value) {
		      					if($value == $settings['styling']['search_input']['border_type']) {
		      				?>
		      					<option value="<?php echo esc_attr($value); ?>" selected="selected"><?php echo esc_attr($value); ?></option>
		      				<?php
		      					}
		      					else {
		      						?>
		      					<option value="<?php echo esc_attr($value); ?>"><?php echo esc_attr($value); ?></option>	
		      						<?php
		      					}
		      				}
		      				?>
                        </select>
		      		</div>
		      	</div>
		      	</li>
                 
                <li>
                	<div class="col_liStle hideBorder">
		      		<label><?php _e( 'Width: ', 'advance-search' ); ?></label>
		      			<input type="number" class="input_style" name="search_form_setting[styling][search_input][border_px]" value="<?php echo esc_attr($settings['styling']['search_input']['border_px']); ?>"><span class="pxValue nwPxValue"><?php _e('px', 'advance-search'); ?></span>
		      		</div>
		      		</li>

		      		<li>
		      		<div class="col_liStle hideBorder">
		      		<label><?php _e( 'Border Color:', 'advance-search' ); ?></label><input type="text" name="search_form_setting[styling][search_input][border_color]" class="wpas_color_field" value="<?php echo esc_attr($settings['styling']['search_input']['border_color']); ?>">
		      	     </div>
		      	    </li>
		      	</ul>
                 
                 <ul class="searchMBox newBoxSearch">
		      	    <li>
		      	    <div class="col_liStle">
		      		<label><?php _e( 'Border Radius:', 'advance-search' ); ?></label>
		      		<i class="fa fa-arrow-up" title="Top" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_input][border_radius][top]" value="<?php echo esc_attr($settings['styling']['search_input']['border_radius']['top']); ?>" class="restricted"><span class="pxValue">px</span>
		      		</div>
		      		</li>
		      		<li>
		      		<div class="col_liStle"><i class="fa fa-arrow-right" title="Right" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_input][border_radius][right]" value="<?php echo esc_attr($settings['styling']['search_input']['border_radius']['right']); ?>" class="restricted"><span class="pxValue">px</span>
		      		</div>
		      		</li>
		      		<li>
		      		<div class="col_liStle">
		      			<i class="fa fa-arrow-down" title="Bottom" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_input][border_radius][bottom]" value="<?php echo esc_attr($settings['styling']['search_input']['border_radius']['bottom']); ?>" class="restricted"><span class="pxValue">px</span>
		      		</div>
		      		</li>
		      		<li>
		      		<div class="col_liStle">
		      		<i class="fa fa-arrow-left" title="Left" aria-hidden="true"></i><input type="text" name="search_form_setting[styling][search_input][border_radius][left]" value="<?php echo esc_attr($settings['styling']['search_input']['border_radius']['left']); ?>" class="restricted"><span class="pxValue">px</span>
		      	    </div>
		      		</li>
                  </ul>
				</div>
			</div>

<hr class="section_seprate" />

				 <div class="loading_icon search_box_layout" style="float: left; width: 100%;">
				 <div class="searchBox_info buy_pro_wrapper">
				  <h4 class="title_heading"><?php _e( 'Icons', 'advance-search' ); ?>
				  	<span><?php _e('* Only 1 magnifier and loading icon available in free version.', 'advance-search' ); ?><span>
				  </h4>
				  <div class="col_liStle">
				  <label class="lable_magn"><?php _e( 'Magnifier Icons:', 'advance-search' ); ?></label>
				  <ul class="magnifier_icon_design">
				  <li class="active" data-icon="search"><i class="fa fa-search" aria-hidden="true"></i></li>
				</ul>
			    </div>
               
				<div class="searchBox_info magnifier_sec">
               <ul class="searchMBox">
		      <li>
		      <div class="col_liStle">
               	<label><?php _e( 'Magnifier Icon Color:', 'advance-search' ); ?></label>
               	<input type="text" name="search_form_setting[styling][magnifire][color]" value="<?php echo ($settings['styling']['magnifire']['color']) ? esc_attr($settings['styling']['magnifire']['color']) : '#ffffff'; ?>" class="wpas_color_field">
               
               </div>
		      </li>
              <li>
				<div class="col_liStle">
					<label><?php _e( 'Magnifier Background Color:', 'advance-search' ); ?></label>
					<input type="text" name="search_form_setting[styling][magnifire][bg_color]" value="<?php echo ($settings['styling']['magnifire']['bg_color']) ? esc_attr($settings['styling']['magnifire']['bg_color'] ): '#cccccc'; ?>" class="wpas_color_field">
               </div>
		     </li>
		     <li>
		      <div class="col_liStle">
                 <fieldset>
					<label><?php _e( 'Button Position:', 'advance-search' ); ?> </label>
						<div class="box_dropdown_advance">
						<select name="search_form_setting[styling][magnifire][position]">
							<option value="right" <?php echo ($settings['styling']['magnifire']['position'] == 'right') ?  'selected="selected"' : ''; ?>><?php _e( 'Right To input', 'advance-search' ); ?></option>
							<option value="left" <?php echo ($settings['styling']['magnifire']['position'] == 'left') ?  'selected="selected"' : ''; ?>><?php _e( 'Left To input', 'advance-search' ); ?></option>
						</select>
					</div>
				</fieldset>
                </div>
		    </li>
		     </ul>
            </div>

				</div>



 <div class="searchBox_info">
  <h5><?php _e( 'Loading Icons', 'advance-search' ); ?></h5>
<div class="col_liStle">
<ul class="loader_lists">
<li class="active" data-icon="sbl-circ"><div class="sbl-circ"></div></li>
</ul>
</div>
<label><?php _e( 'Loading Icon Color:', 'advance-search' ); ?></label> <input type="text" name="search_form_setting[styling][loader][color]" value="<?php echo ($settings['styling']['loader']['color']) ? esc_attr($settings['styling']['loader']['color']) : '#ffffff'; ?>" class="wpas_color_field">
</div>

	

</div>
<hr class="section_seprate" />

			<div class="search_box_layout search_button" style="float: left; width: 100%;">
             <div class="searchBox_info">
			 <h4 class="title_heading"><?php _e( 'Search Button', 'advance-search' ); ?></h4>
              <div class="col_liStle">
              	<ul>
              	<li>
				<fieldset>
				<label><?php _e( 'Search Button Text:', 'advance-search' ); ?></label>
				 <input class="serach_input_style" type="text" name="search_form_setting[styling][search_button][text]" value="<?php echo ($settings['styling']['search_button']['text']) ? esc_attr($settings['styling']['search_button']['text']) : 'Search'; ?>" />
				</fieldset>
			    </li>
                <li>
				<label><?php _e( 'Search Text Color:', 'advance-search' ); ?></label> 
				<input type="text" name="search_form_setting[styling][search_button][font_color]" class="wpas_color_field" value="<?php echo ($settings['styling']['search_button']['font_color']) ? esc_attr($settings['styling']['search_button']['font_color']) : '#000000'; ?>">
			     </li>
			     </ul>
			    </div>

                <div class="col_liStle">
                <ul>
				<li>
				<fieldset>
				<label><input type="checkbox" name="search_form_setting[styling][search_button][show_search_text]" value="show_search_text" <?php if (in_array('show_search_text', $settings['styling']['search_button'])) { echo 'checked="checked"'; } ?> />
						<span><?php _e( 'Show Search Button Text', 'advance-search' ); ?></span>	
					</label>
				</fieldset>
                </li>
                <li>
                <fieldset>
				<label><input type="checkbox" name="search_form_setting[styling][search_button][show_maginfier_icon]" value="show_maginfier_icon" <?php if (in_array('show_maginfier_icon', $settings['styling']['search_button'])) { echo 'checked="checked"'; } ?> />
						<span><?php _e( 'Show Maginfier Icon', 'advance-search' ); ?></span>
					</label>
				</fieldset>

			   </li>
			   </ul>
               </div>
               
               <div class="col_liStle">
               	<ul class="searchMBox">
               	<li>
		    	<fieldset>
				<label><?php _e( 'Search Text Font Size:', 'advance-search' ); ?></label>
				<input class="restricted input_style" type="text" name="search_form_setting[styling][search_button][font_size]" value="<?php echo esc_attr( $settings['styling']['search_button']['font_size']); ?>" />
				<span class="pxValue nwPxValue"> <?php _e('px', 'advance-search'); ?></span>
				</fieldset>
			</li>
			</ul>
			</div>

            </div>

			</div>

		    </div>
</div>
		  </li>

		   <li>
		    <a class="toggle" href="#"><i class="fa fa-tasks" aria-hidden="true"></i> <?php _e( 'Fuzzy Matching', 'advance-search' ); ?></a>
		    <div class="inner">
			
			<h4 class="title_heading alert"><?php _e("'Full/Exact Word' search will work for post types only.", 'advance-search' ); ?></h4>
		      
			<h4 class="title_heading">
				<?php _e('Fuzzy Matching', 'advance-search' ); ?>
			</h4>
		      <div>
		      	<p class="content_Style"><?php _e('* This additional option is for more efficient search results.', 'advance-search' ); ?></p>
		      	
		      	<label>
		      	<input type="radio" name="search_form_setting[search_type]" value="partial_word" <?php echo ($settings['search_type'] == 'partial_word') ? 'checked="checked"': ''; ?> />
		      	<?php _e('Partial Word', 'advance-search' ); ?>
				</label>

		      	<label>
		      	<input type="radio" name="search_form_setting[search_type]" value="full_word" <?php echo ($settings['search_type'] == 'full_word' || empty($settings['search_type']) ) ? 'checked="checked"': ''; ?> />
		      	<?php _e('Full/Exact  Word', 'advance-search' ); ?>
				</label>

		      </div>

		      <p class="content_Style warning"><?php _e('* Search may slower by choosing Partial Word Search.', 'advance-search' ); ?></p>

		    </div>
		  </li> 


		  <li>
		    <a class="toggle" href="#"><i class="fa fa-language" aria-hidden="true"></i> <?php _e( 'Enable Special Character in Search', 'advance-search' ); ?></a>
		    <div class="inner">
		      <div>
		      	<h4 class="title_heading"><?php _e('This additional option is for enabling the special character in search results.', 'advance-search' ); ?></h4>
		      	
		      	<label>
		      	<input type="checkbox" name="search_form_setting[enable_special_character]" value="yes" <?php echo isset($settings['enable_special_character']) ? ( ($settings['enable_special_character'] == 'yes') ? 'checked="checked"': '') : ''; ?> />
		      	<?php _e('Enable Special Character', 'advance-search' ); ?>
				</label>
		      </div>
		    </div>
		  </li> 
		  
		</ul>


	<div class="wpas_search_right">
		<button class="button button-primary" type="submit"><?php _e('Save all changes', 'advance-search'); ?></button>
		
		</form> 

		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="">
			<input type="hidden" name="action" value="wpas_search_form_settings">
			<input type="hidden" name="search_setting_reset" value="<?php echo wp_create_nonce('reset_form_settings'); ?>" />
			<input type="hidden" name="search_form_id" value="<?php echo intval($search_form_id); ?>">
			<button class="button button-secondary" type="submit"><?php _e('Restore defaults', 'advance-search'); ?></button>
		</form>

	</div>

	</div>


<div class="sideBar_Section">
<div class="secColStyle">
<h3><?php _e('Advanced Search', 'advance-search'); ?> </h3>
<div class="starts">
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
</div>
<p><?php _e('We love and care about you. Our team is putting maximum efforts to provide you the best functionalities. It would be highly appreciable if you could spend a couple of seconds to give a Nice Review to the plugin to appreciate our efforts. So we can work hard to provide new features regularly.', 'advance-search'); ?></p>
<button class="button button-primary"><a href="https://wordpress.org/plugins/advance-search#reviews" target="_blank"><?php _e('Rate Us', 'advance-search'); ?></a></button>
</div>
<div class="secColStyle pro_sec">
<h3><?php _e('Go Pro', 'advance-search'); ?></h3>
<p><?php _e('Even more features available in Advanced Search Pro.','advance-search'); ?></p>
<div class="btn"><a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search'); ?> <i class="fa fa-diamond" aria-hidden="true"></i></a></div>
</div>
</div>

</div>
</div>
</div>
<?php 
} // end check num rows
?>