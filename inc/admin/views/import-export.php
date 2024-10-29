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
   global $wpdb;
   $search_form_table = $wpdb->prefix."wpas_index";
   $search_forms = $wpdb->get_results("SELECT * FROM $search_form_table");
   ?>

<?php
    include_once('wpas_popup.php');
?>


<div class="wrap">
  <div class="accordion_section advance_serach_sec wpas_search">
    <h3 class="mb_0"><?php echo esc_attr( get_admin_page_title()); ?></h3>
    <br/>
    <div class="imp_link">
      <a class="back" href="<?php echo esc_url(admin_url().'admin.php?page='.$this->plugin_name); ?>"><?php _e('Search list', 'advance-search'); ?></a>
      <a class="statistics" href="<?php echo esc_url(admin_url().'admin.php?page=wpas-statistics');?>"><?php _e('Search Statistics', 'advance-search'); ?></a>
      <a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search'); ?> <i class="fa fa-diamond" aria-hidden="true"></i></a>
    </div>
  </div>
   <div class="advance_serach_sec">
      <div class="wrap_section">
         <div class="imprtExportSec pro_version">
          <div class="buy_pro_wrapper">
            <h4 class="title_heading">
              <?php _e('* This feature only for pro version', 'advance-search' ); ?><a href="https://searchpro.ai/" target="_blank"><?php _e('Buy Now', 'advance-search' ); ?></a>
            </h4>
        </div>
            <img class="import_export_demo" src="<?php echo esc_url(plugins_url().'/'.$this->plugin_name); ?>/inc/admin/images/import-export.png">
         </div>
      </div>
   </div>
<div class="sideBar_Section">
   <div class="secColStyle">
      <h3><?php _e('Advanced Search', 'advance-search'); ?></h3>
      <div class="starts">
         <span class="fa fa-star checked"></span>
         <span class="fa fa-star checked"></span>
         <span class="fa fa-star checked"></span>
         <span class="fa fa-star checked"></span>
         <span class="fa fa-star checked"></span>
      </div>
      <p><?php _e('We love and care about you. Our team is putting maximum efforts to provide you the best functionalities. It would be highly appreciable if you could spend a couple of seconds to give a Nice Review to the plugin to appreciate our efforts. So we can work hard to provide new features regularly.','advance-search'); ?>
      </p>
      <button class="button button-primary"><a href="https://wordpress.org/plugins/advance-search#reviews" target="_blank"><?php _e('Rate Us', 'advance-search'); ?></a></button>
   </div>
   <div class="secColStyle pro_sec">
      <h3><?php _e('Go Pro', 'advance-search'); ?></h3>
      <p><?php _e('Even more features available in Advanced Search Pro.', 'advance-search'); ?></p>
      <div class="btn"><a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro ', 'advance-search'); ?><i class="fa fa-diamond" aria-hidden="true"></i></a></div>
   </div>
</div>
</div>