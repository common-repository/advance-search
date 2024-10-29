( function( $ ) {
  "use strict";
  
  /**
       * All of the code for your admin-facing JavaScript source
       * should reside in this file.
       *
       * Note: It has been assumed you will write jQuery code here, so the
       * $ function reference has been prepared for usage within the scope
       * of this function.
       *
       * This enables you to define handlers, for when the DOM is ready:
       *
       * $(function() {
       *
       * });
       *
       * When the window is loaded:
       *
       * $( window ).load(function() {
       *
       * });
       *
       * ...and/or other possibilities.
       *
       * Ideally, it is not considered best practise to attach more than a
       * single DOM-ready or window-load handler for a particular page.
       * Although scripts in the WordPress core, Plugins and Themes may be
       * practising this, we should strive to set a better example in our own work.
       *
       * The file is enqueued from inc/admin/class-admin.php.
       */
  
     jQuery(document).on('click', '#ad_dismiss', function(e){
      jQuery('.dismiss-icon').hide();
  })
    jQuery(document).ready(function () {
      var admin_page_url = 'admin.php?page=advance-search';
      window.history.replaceState({}, document.title, admin_page_url);
        if(js_params.has_msg != ''){
          window.history.replaceState({}, document.title, js_params.has_msg);
        }
       jQuery('.toggle').click(function(e) {
       e.preventDefault();
    
      var $this = jQuery(this);
    
      if ($this.next().hasClass('show')) {
          $this.next().removeClass('show');
          $this.next().slideUp(350);
          $this.removeClass('active');
      } else {
          $this.parent().parent().find('li .inner').removeClass('show');
          $this.parent().parent().find('li .inner').slideUp(350);
          $this.next().toggleClass('show');
          $this.next().slideToggle(350);
          jQuery('.toggle').removeClass('active');
          $this.addClass('active');
      }
  });
  
  
  });
  
  })( jQuery );
  
      // color picker
  
  jQuery(document).ready(function(){
    
    if(jQuery('#advance_search_posttype_chkbox .checkarea').is(":checked"))
    {
      jQuery('#postSearch').show();
    }else{
      jQuery('#postSearch').hide();
    }
  
    if(jQuery('#advance_search_taxonomy_chkbox .checkarea').is(":checked"))
    {
      jQuery('#taxonomySearch').show();
    }else{
      jQuery('#taxonomySearch').hide();
    }
  
    jQuery('#advance_search_posttype_chkbox .checkarea').click(function() {
      
      posttype_with_taxonomies(jQuery('#advance_search_posttype_chkbox .checkarea'), jQuery('#postSearch'),'subposting');
    });
    jQuery('#advance_search_taxonomy_chkbox .checkarea').click(function() {
      
      posttype_with_taxonomies(jQuery('#advance_search_taxonomy_chkbox .checkarea'), jQuery('#taxonomySearch'),'subtaxonoy');
    });
  
    function posttype_with_taxonomies(parentselector,hideselector,data_check){
      var advanced_search_check_posttype = [];
      parentselector.each(function(index, value) {
          if(jQuery(this).is(":checked")){
            advanced_search_check_posttype.push(value);
          }
        });
      if(advanced_search_check_posttype.length == 0){
        hideselector.hide();
        jQuery("[data-check="+data_check+"]").prop('checked', false);
      }
      else{
        hideselector.show();
      }
    }
  
  
      jQuery('.my-color-field').wpColorPicker();
      jQuery('.wpas_color_field').each(function(){
          jQuery(this).wpColorPicker();
      });
  });
  
  // clone and delete search
  
  jQuery(document).ready(function(){
  
      // clone popup
      jQuery('.aclone_search').click(function(e) {
        jQuery("div#ClonePopup").css({"visibility":"visible", "opacity":"1"});
        var scname = jQuery(this).attr('data-scname');
        var targetid = jQuery(this).data('id');
        jQuery("span.csname-heading").text(scname);
        jQuery("#advance-search-ajaxsearch_form").val(scname);
        jQuery(".aspopup-form-area #clone_search").attr('data-id',targetid);
      });
  
      jQuery(document).on('click', '.aspopup-header .close, .wpas-modal-btn',function(e) {
        jQuery("div#ClonePopup").removeAttr("style");
        jQuery('#ClonePopup .pro-info').hide();
     
      });
     
  
  
      // clone / delete setting 
      
  
      jQuery('.search_imp_ajax').click(function() {
        
          var search_id = jQuery(this).attr('data-id');
          var type = jQuery(this).attr('data-type');
          var nonce = jQuery("#extra_ajax_hidden").val();
          var clonenum = jQuery(this).attr('data-num');
         
          var shortcodeName = jQuery("#advance-search-ajaxsearch_form").val();
          var dataAjax = jQuery(this).attr('data-ajax');
          if (jQuery(this).hasClass('delete_search')){
            var del=confirm(js_params.delete_record_text);
            if(del==true){
              jQuery(".delete_search").attr("data-ajax", "Yes");
              dataAjax = "Yes"
              dataDeleteAjax = "Yes"
            }else{
              jQuery(".delete_search").attr("data-ajax", "No");
              dataAjax = "No"
            }
            
          }
          
         if(dataAjax == 'Yes'){          
            var test=0;
            jQuery.ajax({
                url: ajaxurl, // domain/wp-admin/admin-ajax.php
                type: "POST",
                dataType: "json",
                data: {
                    action: "WPAS_Advanced_Search_extra_ajax",
                    ajax_type: type,
                    security:nonce,
                    form_id : search_id,
                    'search_form_name': shortcodeName,
                    "cloneNum" : clonenum
            },
                success: function(data) {
                    if(data.astext == 'true' ){
                      jQuery('.as-alreadyexists').hide();
                      jQuery('.as-namelength').hide();
                      jQuery('.as-validname').hide();
                       jQuery('.as-success').show();         
                      setTimeout(function(){
                      var admin_url = jQuery('.aclone_search').attr('data_url');
                     window.location.href = admin_url;
                    }, 300);
                      
                    }
                    else if(data.astext == 'already-exists' ){
                      jQuery('.as-alreadyexists').show();
                      jQuery('.as-validname').hide(); 
                      jQuery('.as-namelength').hide(); 
                      return false;
                     }else if(data.astext == 'empty'){
                      jQuery('.as-validname').show();
                        jQuery('.as-alreadyexists').hide();
                        jQuery('.as-namelength').hide();
                        jQuery('.as-success').hide();
                     }else if(data.astext == 'name-length'){
                      jQuery('.as-validname').hide();
                        jQuery('.as-alreadyexists').hide();
                        jQuery('.as-namelength').show();
                        jQuery('.as-success').hide();
                     }
  
                    
                     else{
                      setTimeout(function(){
                      alert(js_params.something_wrong_text);
                           location.reload();
             }, 300);
                     }
  
          },
          error: function (data) {
  
          }
        });
      }
  
  });
    jQuery('.btn-submit').addClass('pointer-event-none');
    jQuery('#advance-search-search_form').on('keyup', function 
    () {
      var nameInput = jQuery(this).val().replace(/^\s+|\s+$/g, "").length != 0;
        let isValid = nameInput != "" && nameInput != null;
        if(nameInput == "" || nameInput == null){
          jQuery('.btn-submit').addClass('pointer-event-none');
        }
        else{
          jQuery('.btn-submit').removeClass('pointer-event-none');
        }
      });
   
    
   
     jQuery("input.restricted").keyup(function (e) {
         var str = jQuery(this).val();
         var string = str.replace("-", '');
         jQuery(this).val(string);
               });
  
  });
  
  
  /*********** verify email popup *************/
  
  jQuery(window).load(function (e) {
    jQuery('.wfmrs').delay(10000).slideDown('slow');
    jQuery('.lokhal_verify_email_popup').slideDown();
    jQuery('.lokhal_verify_email_popup_overlay').show();
  });
  
  jQuery(document).ready(function () {
  
  jQuery('.lokhal_cancel').click(function (e) {
      e.preventDefault();
      var email = jQuery('#verify_lokhal_email').val();
      var fname = jQuery('#verify_lokhal_fname').val();
      var lname = jQuery('#verify_lokhal_lname').val();
      jQuery('.lokhal_verify_email_popup').slideUp();
      jQuery('.lokhal_verify_email_popup_overlay').hide();
      send_ajax('cancel', email, fname, lname);
    });
    jQuery('.verify_local_email').click(function (e) {
      e.preventDefault();
      var email = jQuery('#verify_lokhal_email').val();
      var fname = jQuery('#verify_lokhal_fname').val();
      var lname = jQuery('#verify_lokhal_lname').val();
      var checkEmail = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
   
      var send_mail = true;
      jQuery('.error_msg').hide();
      if (fname == '') {
        jQuery('#fname_error').show();
        send_mail = false;
      }
      if (lname == '') {
        jQuery('#lname_error').show();
        send_mail = false;
      }
      if (email == '') {
        jQuery('#email_error').show();
        send_mail = false;
      } 
      else if (!checkEmail.test(email)){
        jQuery('#email_error_valid').show();
        jQuery('#email_error').hide();
        send_mail = false;
      }
      if (send_mail) {
        jQuery('.lokhal_verify_email_popup').slideUp();
        jQuery('.lokhal_verify_email_popup_overlay').hide();
        send_ajax('verify', email, fname, lname);
      }
    });
    // mac
    if (navigator.userAgent.indexOf('Mac OS X') != -1) {
      jQuery("body").addClass("mac");
    } else {
      jQuery("body").addClass("windows");
    }
  
    jQuery('.fm_close_msg').click(function (e) {
      jQuery('.fm_msg_popup').fadeOut();
    });
  
  });
  
  function send_ajax(todo, email, fname, lname) {
    jQuery.ajax({
      type: "post",
      url: ajaxurl,
      data: {
        action: "wpas_verify_email",
        'todo': todo,
        'vle_nonce': vle_nonce,
        'lokhal_email': email,
        'lokhal_fname': fname,
        'lokhal_lname': lname
      },
      success: function (response) {
        if (response == '1') {
          alert(js_params.confirmation_text);
        } else if (response == '2') {
        }
      }
    });
  }
  
  //characters only
  jQuery(document).ready(function () {
  
   /* open border width and color if selected value is not none start */
     var SelectedborderVal= jQuery('#InputLayoutBorder').val();
  
   if(SelectedborderVal != 'none'){
       jQuery('#InputBorder .hideBorder').show(500);
   }else{
       jQuery('#InputBorder .hideBorder').hide(500);
   }
   
   var SelectedborderVal= jQuery('#BoxLayoutBorder').val();
  
   if(SelectedborderVal != 'none'){
       jQuery('#InputBorderBox .hideBorder').show(500);
   }else{
       jQuery('#InputBorderBox .hideBorder').hide(500);
   }
   /* open border width and color if selected value is not none end */
   
    jQuery('.serach_input_style').keypress(function(e) {
      var inputValue = event.charCode;
        if(!(inputValue >= 65 && inputValue <= 120) && (inputValue != 32 && inputValue != 0)){
          event.preventDefault();
      }
  });
   
  jQuery('select.borderType').on('change', function() {
    var $this = jQuery(this);
    var borderVal= this.value;
    
    if (borderVal == 'none'){
   
      $this.parents('.searchMBox li').siblings().find('.hideBorder').hide(500);
      $this.parents('.searchMBox li').siblings().find('.input_style').val('0');
      $this.parents('.searchMBox li').siblings().find('.wp-picker-clear').trigger('click');
   
      }
  else{
      $this.parents('.searchMBox li').siblings().find('.hideBorder').show(500);
    }
  });
  jQuery('select.theme_replaced').on('change', function() {  
    var themeVal= jQuery(this).find(":selected").val()
    if (jQuery(themeVal) === ""){
      
    }
    else{
      jQuery('.none-option').val('0'); 
    }
  });
  
    });
  