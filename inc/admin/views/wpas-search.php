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

if ( ! defined( 'ABSPATH' ) && !current_user_can( 'manage_options' ) ) {
	exit; // Exit if accessed directly
}

$current_user = wp_get_current_user(); 
$opt = get_option('wp_advance_search_settings');

wp_register_script( 'advance-search-wpas-js', '');
wp_enqueue_script( 'advance-search-wpas-js');
wp_add_inline_script(
    'advance-search-wpas-js', " var vle_nonce = '". wp_create_nonce('verify-wpas-email')."'"
);
?>


<?php
global $wpdb;
$search_form_table = $wpdb->prefix."wpas_index";
$search_forms = $wpdb->get_results("SELECT id, name FROM $search_form_table");

$default_search_form_id = get_option($this->plugin_name.'_default_search');
$default_woo_search_form_id = get_option($this->plugin_name.'_default_woo_search');
?>
<div class="wrap">
<div class="advance_serach_sec">
<div class="search_box">
	<h3><?php echo esc_html( get_admin_page_title() ); ?></h3>
	<form class="form" action="<?php echo (count($search_forms) < 3) ? esc_url( admin_url( 'admin-post.php' ) ) : '#'; ?>" method="post" id="">
		<input type="hidden" name="action" value="<?php echo (count($search_forms) < 3) ? 'wpas_search_form_response' : '';?>">
		<input type="hidden" name="wpas-search" value="<?php echo (count($search_forms) < 3) ? wp_create_nonce($this->plugin_text_domain) : ''; ?>" />
		
        <div>
			<label for="<?php echo esc_attr($this->plugin_text_domain); ?>-search_form"> <?php _e('Shortcode Name:', 'advance-search'); ?> </label>
			<input required maxlength="20" id="<?php echo  esc_attr('advance-search'); ?>-search_form" type="text" name="<?php echo "wpas"; ?>[search_form_name]" value="" placeholder="<?php _e('Enter Shortcode Name', 'advance-search');?>"<?php echo (count($search_forms) < 3) ? '' : ' readonly="readonly"' ;?>/>
            <div class="submit"><input type="submit" name="submit" id="submit" class="<?php echo (count($search_forms) == 3) ? 'pointer-event-none' : '';?> btn-submit button button-primary" value="<?php _e('Create', 'advance-search'); ?>" <?php echo (count($search_forms) < 3) ? '' : ' ';?>/></div>
			<?php if(count($search_forms) >= 3){ ?>
			<p class="pro-info">* <?php _e( 'You have reached the maximum form limit of 3 forms. <a href="https://searchpro.ai/" target="_blank" class="go_pro_link">Buy Pro</a> for more search forms.', 'advance-search'); ?></p>
			<?php }?>
            <?php if(isset($_GET['name-already-exists']) ){ ?>
			<p class="pro-info">* <?php _e('This name is already exists.', 'advance-search'); ?></p>
			<?php }?>

            <?php if(isset($_GET['name-maxlength']) ){ ?>
			<p class="pro-info">* <?php _e('Shortcode name length must not exceed 20 character.', 'advance-search'); ?></p>
			<?php }?>
		</div>
	</form>
<div class="secColInfo">
	<h3><?php _e('Replace search bar', 'advance-search'); ?></h3>
	
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="themeSearch_option">
		<label><?php _e('Replace default search bar with:', 'advance-search'); ?></label>
		<input type="hidden" name="action" value="wpas_default_search_form_response" />
		<input type="hidden" name="wpas-default-search-form" value="<?php echo wp_create_nonce('default_search_form'); ?>" />
		<input type="hidden" name="search_type" value="default_theme_search_form" />
     
       <div class="box_dropdown_advance">
		<select name="default_search_form_id" class="theme_replaced" required>
			<option class="none-option" value="" <?php if($default_search_form_id == '0') {echo 'selected="selected"'; } ?>><?php _e('None', 'advance-search'); ?></option>
			<?php
			if(!empty($search_forms)){
				foreach ($search_forms as $search_form_name) {
					?>
					<option value="<?php echo $search_form_name->id; ?>" <?php if($default_search_form_id == $search_form_name->id) {echo 'selected="selected"'; } ?>><?php echo esc_attr($search_form_name->name); ?></option>
					<?php
				}
			}
			?>
		</select>
        <?php if(isset($_GET['theme-search-replaced'])){ ?>
            <div class="updated notice is-dismissible dismiss-icon">
					<p><?php _e('Successfully Replaced.', 'advance-search'); ?><button type="button" id="ad_dismiss" class="notice-dismiss" data_url=""><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'advance-search'); ?></span></button></p>
				</div>
			
			<?php }?>
	</div>
		<p class="submit"><input type="submit" name="submit" id="replacesubmit" class="button button-primary" value="<?php _e('Save', 'advance-search'); ?>"></p>
		
	</form>


	<!-- replace woo search -->

	<?php
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	?>
	<div class="pro_feature buy_pro_wrapper">
		<div class="buy_pro">
			<a href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search' ); ?>
				<i class="fa fa-diamond" aria-hidden="true"></i>
			</a>
		</div>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<label><?php _e('Replace the default woocommerce search with:', 'advance-search'); ?></label>
       <div class="box_dropdown_advance">
		<select name="default_woo_search_form_id">
			<option value="0" <?php if($default_woo_search_form_id == '0') {echo 'selected="selected"'; } ?>><?php _e('None', 'advance-search'); ?></option>
			<?php
			if(!empty($search_forms)){
				foreach ($search_forms as $search_form_name) {
					?>
					<option value="<?php echo intval($search_form_name->id); ?>" <?php if($default_woo_search_form_id == $search_form_name->id) {echo 'selected="selected"'; } ?>><?php echo esc_attr($search_form_name->name); ?></option>
					<?php
				}
			}
			?>
		</select>
	</div>
		<p class="submit"><input type="submit" name="submit" id="prosubmit" class="btn-submit button button-primary" value="<?php _e('Save', 'advance-search'); ?>"></p>
	</form>
</div>

	<?php } // endif ?>

</div>
</div>
<div class="search_box_list">
	<h3><?php _e('List of shortcodes', 'advance-search'); ?></h3>
    
    <ul>
	<?php
		if(!empty($search_forms)){
			?>
			<input type="hidden" value="<?php echo wp_create_nonce('extra_ajax_nonce'); ?>" id="extra_ajax_hidden" />
			<?php
        $i = 1;
            
			foreach ($search_forms as $search_form_name) {
			?>
			<li>
                <span class="num_style"><?php echo $i;?>.</span> <span class="content_style">
                <?php echo esc_attr($search_form_name->name);?></span><input type="text" class="quick_shortcode" value="[wpas id=<?php echo intval($search_form_name->id); ?>]" readonly="readonly"> 
				<span class="icons_sec">
				   <a href="<?php echo esc_url(admin_url().'admin.php?page='.$this->plugin_name.'&wpas_id='.$search_form_name->id); ?>" title="<?php _e('Edit Settings', 'advance-search'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> | <a href="javascript:void(0)" data-targent="ClonePopup" data-id="<?php echo intval($search_form_name->id); ?>" data-scname="<?php echo esc_attr($search_form_name->name); ?>" data-type="clone_search" class="asearch_imp_ajax aclone_search" id="aclonesearch" data-ajax='Yes' title="<?php _e('Clone Settings','advance-search'); ?>" data_url="<?php echo esc_url(admin_url('admin.php?page=advance-search'))?>"><i class="fa fa-clone" aria-hidden="true"></i></a>| <a href="javascript:void(0)" data-id="<?php echo intval($search_form_name->id); ?>" data-type="delete_search" data-ajax='No' class="search_imp_ajax delete_search" title="<?php _e('Delete Search','advance-search'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
			   </span> 
		   </li>
			<?php
			$i++;
			}
		}
		else {
            $html = "<p class='pl-10'>";
            $html .= esc_attr__('Oops! Shortcode(s) not found.', 'advance-search');
            $html .= "</p>";
            echo apply_filters('the_content',$html);
		}
	?>
</ul>
</div>
</div>

<div class="sideBar_Section" style="margin-top:20px;">
<div class="secColStyle">
<h3><?php _e('Advanced Search', 'advance-search'); ?></h3>
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
<p><?php _e('Even more features available in Advanced Search Pro.', 'advance-search'); ?></p>
<div class="btn"><a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search'); ?> <i class="fa fa-diamond" aria-hidden="true"></i></a></div>
</div>
</div>

</div>
<div class="wpas_loader"><img src="<?php echo esc_url(plugins_url().'/'.$this->plugin_name); ?>/inc/admin/images/loader3.gif" /></div>
<?php
    include_once('wpas_popup.php');
?>
<div class="fm_msg_popup">
    <div class="fm_msg_popup_tbl">
        <div class="fm_msg_popup_cell">
            <div class="fm_msg_popup_inner">
                <div class="fm_msg_text">
                    <?php _e('Saving...', 'advance-search'); ?>
                </div>
                <div class="fm_msg_btn_dv"><a href="javascript:void(0)" class="fm_close_msg button button-primary"><?php _e('OK', 'advance-search'); ?></a></div>
            </div>
        </div>
    </div>
</div>

<div id="ClonePopup" class="asoverlay">
	<div class="aspopup">
        <div class="aspopup-header">
            <h2><?php _e( 'Clone Shortcode:', 'advance-search'); ?> <span class="csname-heading"></span></h2>
    		<a class="close" href="javascript:void(0)">&times;</a>
        </div>
		<div class="content aspopup-content">
        <form class="form" action="" method="post" >
            <input type="hidden" name="action"  value="<?php echo (count($search_forms) < 3) ? 'wpas_search_form_response' : '';?>">
             <span class="icons_sec">
            <input type="hidden" name="wpas-search" id="wpas-search" value="<?php echo (count($search_forms) < 3) ? wp_create_nonce($this->plugin_text_domain) : ''; ?>" />
            <div>
				
				 <?php if(count($search_forms) >= 3){ ?>
                
                <p class="pro-infomax pt-0">* <?php _e( 'You have reached the maximum form limit of 3 forms. <a href="https://searchpro.ai/" target="_blank" class="go_pro_link">Buy Pro</a> for more search forms.', 'advance-search'); ?>
				</p>
				 <?php } else{ ?>
				 <div class="aspopup-form-area">
                
                <input required maxlength="20" id="<?php echo esc_attr($this->plugin_text_domain); ?>-ajaxsearch_form" type="text" name="<?php echo "wpas"; ?>[search_form_name]" value="" placeholder="<?php _e('Enter Shortcode Name', 'advance-search');?>"<?php echo (count($search_forms) < 3) ? '' : ' readonly="readonly"' ;?>/>
                
                <input type="button" name="submit" id="clone_search" data-ajax='Yes' title="<?php _e('Clone Settings', 'advance-search'); ?>" data_url="<?php echo esc_url(admin_url('admin.php?page=advance-search'))?>" data-id="" data-type="clone_search" class="search_imp_ajax clone_search" value="<?php _e('Clone', 'advance-search'); ?>" <?php echo (count($search_forms) < 3) ? '' : ' ';?>/>
				</div>

               <?php }?>
                <p class="pro-info as-alreadyexists" style="display:none">* <?php _e('This name is already exists.', 'advance-search'); ?></p>
                <p class="pro-info as-namelength" style="display:none">* <?php _e('Name must not exceed 20 character..', 'advance-search'); ?></p>
                <p class="pro-info as-validname" style="display:none">* <?php _e('Please enter Shortcode Name.', 'advance-search'); ?></p>
                <p class="pro-infoq as-success" style="display:none"><?php _e('Shortcode Successfully created.', 'advance-search'); ?></p>
            </div>
	    </form>
		</div>
	</div>
</div>