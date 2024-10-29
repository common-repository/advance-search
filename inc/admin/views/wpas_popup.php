<?php

if ( ! defined( 'ABSPATH' ) && !current_user_can( 'manage_options' ) ) {
	exit; // Exit if accessed directly
}

$current_user = wp_get_current_user();
wp_register_script( 'advance-search-inlinepopup-js', '');
wp_enqueue_script( 'advance-search-inlinepopup-js' );
wp_add_inline_script(
    'advance-search-inlinepopup-js', 'var vle_nonce = "'. wp_create_nonce("verify-wpas-email").'"'
); ?>
<div class="popup_wrapper">
<?php 
    if (false === get_option('wpas_email_verified_'.$current_user->ID) && (false === (get_transient('wpas_cancel_lk_popup_'.$current_user->ID)))) {
        ?>
        <div id="lokhal_verify_email_popup" class="lokhal_verify_email_popup">
            <div class="lokhal_verify_email_popup_overlay"></div>
            <div class="lokhal_verify_email_popup_tbl">
                <div class="lokhal_verify_email_popup_cel">
                    <div class="lokhal_verify_email_popup_content">
                        <a href="javascript:void(0)" class="lokhal_cancel"> <img src="<?php echo esc_url(plugins_url('images/fm_close_icon.png', dirname(__FILE__))); ?>"
                                class="wp_fm_loader" /></a>
                        <div class="popup_inner_lokhal">
                            <h3>
                                <?php _e('Welcome to Advanced Search', 'advance-search'); ?>
                            </h3>
                            <p class="lokhal_desc">
                                <?php _e('We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.', 'advance-search'); ?>
                            </p>
                            <form>
                                <div class="form_grp">
                                    <div class="form_twocol">
                                        <input name="verify_lokhal_fname" id="verify_lokhal_fname" class="regular-text"
                                            type="text" value="<?php echo (null == get_option('verify_wpas_fname_'.$current_user->ID)) ? esc_attr($current_user->user_firstname) : get_option('verify_wpas_fname_'.$current_user->ID); ?>"
                                            placeholder="<?php _e('First Name', 'advance-search'); ?> " maxlength="20"/>
                                        <span id="fname_error" class="error_msg">
                                            <?php _e('Please Enter First Name.', 'advance-search'); ?></span>
                                    </div>
                                    <div class="form_twocol">
                                        <input name="verify_lokhal_lname" id="verify_lokhal_lname" maxlength="20" class="regular-text"
                                            type="text" value="<?php echo (null ==
            get_option('verify_wpas_lname_'.$current_user->ID)) ? esc_attr($current_user->user_lastname) : get_option('verify_wpas_lname_'.$current_user->ID); ?>"
                                            placeholder="<?php _e('Last Name', 'advance-search'); ?>" />
                                        <span id="lname_error" class="error_msg">
                                            <?php _e('Please Enter Last Name.', 'advance-search'); ?></span>
                                    </div>
                                </div>
                                <div class="form_grp">
                                    <div class="form_onecol">
                                        <input name="verify_lokhal_email" id="verify_lokhal_email" class="regular-text"
                                            type="email" value="<?php echo (null == get_option('wpas_email_address_'.$current_user->ID)) ? esc_attr($current_user->user_email) : get_option('wpas_email_address_'.$current_user->ID); ?>"
                                            placeholder="<?php _e('Email Address', 'advance-search'); ?>" />
                                        <span id="email_error" class="error_msg">
                                            <?php _e('Please Enter Email Address.', 'advance-search'); ?></span>
											<span id="email_error_valid" class="error_msg"><?php _e('Please Enter Valid Email Address.', 'advance-search'); ?></span>
                                    </div>
                                </div>
                                <div class="btn_dv">
                                    <button class="verify verify_local_email button button-primary "><span class="btn-text"><?php _e('Verify', 'advance-search'); ?>
                                        </span>
                                        <span class="btn-text-icon">
                                            <img src="<?php echo esc_attr(plugins_url('images/btn-arrow-icon.png', dirname(__FILE__))); ?>" />
                                        </span></button>
                                    <button class="lokhal_cancel button">
                                        <?php _e('No Thanks', 'advance-search'); ?></button>
                                </div>
                            </form>
                        </div>
                        <div class="fm_bot_links">
                            <a href="https://searchpro.ai/terms-condition/" target="_blank">
                                <?php _e('Terms of Service', 'advance-search'); ?></a> <a href="https://searchpro.ai/privacy-policy/"
                                target="_blank">
                                <?php _e('Privacy Policy', 'advance-search'); ?></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php
   } 
   ?>

</div>