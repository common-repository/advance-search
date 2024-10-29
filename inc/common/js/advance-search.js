( function( $ ) {
	"use strict";

	var ajaxReq = 'ToCancelPrevReq'; // you can have it's value anything you like
    var typingTimer;                //timer identifier
	var doneTypingInterval = 300;  //time in ms, 5 second for example
    
    $(document).on('keyup', ".wpas_search_input", function(e) {
    
    	clearTimeout(typingTimer);
		var searchbox = this;
        var queryString = $(searchbox).val();
        var form_id = $(searchbox).attr('data-formid');
		$(searchbox).parent().find('.wpas_search_close').hide().css({'display':'none'});
        $(searchbox).parent().find('.wpas_search_loader_icon').show().css({'display':'block'});
        $(searchbox).parent().find('.wpas_search_result').hide().css({'display':'none'});
        if(queryString == '' || queryString.length < 1){
            $(searchbox).closest('form').find('.wpas_search_result').hide().css({'display':'none'});
			$(searchbox).parent().find('.wpas_search_loader_icon').hide().css({'display':'none'});
			$(searchbox).parent().find('.wpas_search_close').hide().css({'display':'none'});
            return; //You can always alter this condition to a better one that suits you.

        } 
 		else {
            typingTimer = setTimeout(function(){
            ajaxReq = $.ajax({
                    url: params.ajaxurl, // domain/wp-admin/admin-ajax.php
					type: "POST",
					dataType: "json",
					data: {
						action: "WPAS_Advanced_Search_autosuggest",
						ajaxRequest: "yes",
                        security : params.nonce,
						term: queryString,
						form_id : form_id
					},
                    beforeSend : function() {
                            if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 2) {
                                ajaxReq.abort();
                                $(".search_form_"+form_id).find('.wpas_search_result').hide().css({'display':'none'});
                            }
                    },
                    success: function(json) {
						$(searchbox).closest('form').find('.wpas_search_loader_icon').hide().css({'display':'none'});
						$(searchbox).closest('form').find('.wpas_search_result').show().css({'display':'block'});
                        $(searchbox).closest('form').find('.wpas_search_result').html('');
						$(searchbox).closest('form').find('.wpas_search_result').html(json['html']);
						$(searchbox).closest('form').find('.wpas_search_close').show().css({'display':'block'});
                       
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                            if(thrownError == 'abort' || thrownError == 'undefined') return;
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                  }); //end ajaxReq
     	}, doneTypingInterval);
        
        }

    }); //end keyup


	$(document).on('click', '.wpas_search_close', function(){
		var $form = $(this).closest('form');
		var btn_close = $(this);
		$form[0].reset();
		$form.find('.wpas_search_result').hide().css({'display':'none'});
		btn_close.hide().css({'display':'none'});
	});
	
	$(document).click(function (e)
	{
		var container = $(".wpas_search_input");
		if (!container.is(e.target))
		{
			$('.wpas_search_close').hide().css({'display':'none'});
			$('.wpas_search_result').hide().css({'display':'none'});
			$('.wpas_search_loader_icon').hide().css({'display':'none'});
		}
		else{
			if(e.target.className == "wpas_search_input"){
				var targetForm = e.target.form;
				var keyword = e.target.value;
				if(keyword != "" && keyword.length > 2){
					$(targetForm).find('.wpas_search_close').show().css({'display':'block'});
					$(targetForm).find('.wpas_search_result').show().css({'display':'block'});
				}
			}
		}
	});


$(".wpas_search_input").on("keypress", function(e) {
		if (e.which === 32 && !this.value.length)
			e.preventDefault();
});


})( jQuery );