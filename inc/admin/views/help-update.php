<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/mndpsingh287
 * @since      1.0
 *
 * @package    Advance_Search
 * @subpackage Advance_Search/inc/admin/views
 */
if ( ! defined( 'ABSPATH' ) && !current_user_can( 'manage_options' )) {
  exit; // Exit if accessed directly
}
    include_once('wpas_popup.php');
?>
<div class="wrap">
 <div class="accordion_section advance_serach_sec wpas_search">
    <h3 class="mb_0"><?php echo esc_html( get_admin_page_title() ); ?></h3>
    <br/>
    <div class="imp_link">
      <a class="back" href="<?php echo esc_url( admin_url().'admin.php?page='.$this->plugin_name) ; ?>"><?php _e('Search list', 'advance-search'); ?></a>
      <a class="statistics" href="<?php echo esc_url(admin_url().'admin.php?page=wpas-statistics'); ?>"><?php _e('Search Statistics', 'advance-search'); ?></a>
      <a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search');?> <i class="fa fa-diamond" aria-hidden="true"></i></a>
    </div>

  </div>
  <div class="advance_serach_sec accordion_section">
    <div class="help_section commanDiv">
      <div class="left">
        
        <div class="column">
    <h4><?php _e('Support', 'advance-search');?></h4>
    <p><?php _e('If you didn\'t find the answer from the FAQ, or if you are having other issues, feel free to ', 'advance-search'); ?><a href="https://wordpress.org/support/plugin/advance-search/" target="_blank"><?php _e('open a support ticket.', 'advance-search'); ?></a></p>
    </div>
   
    <div class="column">
    <h4><?php _e('FAQ', 'advance-search'); ?></h4>
    
    <div class="faq_wrapper">
      <ul class="accordion">
        <li>
          <a class="toggle" href="#"><?php _e('Does the plugin provide search shortcodes ?', 'advance-search'); ?></a>
          <div class="inner">
      <p><?php _e('Yes, Advance Search provides a shortcode for each search form which you can use to embed it on your WordPress site using our powerful and easy to use shortcode editing tool. We provide integration with Gutenburg and classic editor.', 'advance-search'); ?></p>
      <p><?php _e('For integration with most widely used editors like Visual Composer, Elementor and BB builder, you have to upgrade to our', 'advance-search'); ?> <a href="https://searchpro.ai/" target="_blank"> <?php _e('pro version.', 'advance-search'); ?></a></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('Is the plugin compatible with WooCommerce ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('Yes, Advance Search plugin cater you integration with WooCommerce to provide a powerful and advanced woocommerce search. Not only you can use Fuzzy searching, you can exclude/include specific WooCommerce products from search and much more.', 'advance-search'); ?></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('Does the plugin provide search widgets ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('No. Search widgets is a premium feature. You have to upgrade to our ', 'advance-search');?>
            <a href="https://searchpro.ai/" target="_blank"><?php _e('Pro Version ', 'advance-search');?></a><?php _e('to enable this feature.', 'advance-search'); ?></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('Will the advance search work with my theme ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('Yes. Advance Search, has been tested and works perfectly with a range of themes, including popular themes like Divi, Avada, Impreza, OceanWP and many more.', 'advance-search'); ?></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('Does plugin provide voice search ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('No. Voice search is a premium feature. You have to upgrade to our', 'advance-search'); ?> <a href="https://searchpro.ai/" target="_blank"> <?php _e('Pro Version', 'advance-search'); ?></a> <?php _e('to enable this feature.', 'advance-search'); ?></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('When I type in something, the search wheel is spinning, but nothing happen ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('It is most likely, that another plugin or the template is throwing errors while the ajax request is generated. Try disabling all the plugins one by one can help you figure out which plugin is creating the issue.', 'advance-search'); ?></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('I disabled all the plugins but the search wheel is still spinning to infinity, nothing happens ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('You should contact us on the support forum with your website url. We will check your website and will let you know what to do.', 'advance-search'); ?></p>
          </div>
        </li>

        <li>
          <a class="toggle" href="#"><?php _e('Do You Offer Customization Support ?', 'advance-search'); ?></a>
          <div class="inner">
            <p><?php _e('Yes, we offer free/premium customization to our customers.', 'advance-search'); ?><a href="https://searchpro.ai/contact" target="_blank"> <?php _e('Contact us ', 'advance-search'); ?></a> <?php _e('now.', 'advance-search');?></p>
          </div>
        </li>
       
      </ul>
    </div>

   </div>

      </div>
      <div class="right">
    <div class="column">
    <h4><?php _e('Checkout Pro features', 'advance-search');?></h4>
    <p><a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search');?> <i class="fa fa-diamond" aria-hidden="true"></i></a></p>
   </div>
    
  </div>
    </div>

</div>
</div>


<div class="sideBar_Section" style="display:none;">
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
<button class="button button-primary">
  <a href="https://wordpress.org/plugins/advance-search/#review" target="_blank"><?php _e('Rate Us', 'advance-search');?></a>
</button>
</div>
<div class="secColStyle pro_sec">
<h3><?php _e('Go Pro', 'advance-search');?></h3>
<p><?php _e('Even more features available in Advanced Search Pro.', 'advance-search');?></p>
<div class="btn"><a class="go_pro_button" href="https://searchpro.ai/" target="_blank"><?php _e('Go Pro', 'advance-search'); ?> <i class="fa fa-diamond" aria-hidden="true"></i></a></div>
</div>
</div>