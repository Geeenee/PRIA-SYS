var Outbound_truckers = function() {
	
	var $module		= "transactions/outbound_truckers";

	var init = function() {
		var forms = $('#smart-wizard');
		forms.smartWizard({
            anchorSettings: {
                anchorClickable: true,
                enableAllAnchors: true,
                markDoneStep: false,
                enableAnchorOnDoneStep: true
            }
		});
		
		forms.closest('.right-side').css({ 'padding': 0 });
	}

	var ajax_request = function(options){
		/* Sets of predefined config for ajax request */
		var predefined = {
				dataType   : "json",
				method     : "POST",
				beforeSend : function(){
					//no ui block in core General.showLoading();
					start_loading();
				},
				error : function(jqXHR, textStatus, errorThrown){
					console.log('Error : '+textStatus);
				},
				complete   : function(jqXHR, status){
					//no ui block in core  General.hideLoading();
					end_loading();
				}
		}
		/* Merge the two configs */
	   var final_option = $.extend(predefined, options);
		
	   return $.ajax(final_option);
	}
	
	var save = function()
	{		
		
		var form_add = $('#form_modal_add_soa'); 		
		form_add.parsley();
				
		$('#save_add_modal_add_soa').off("click.save_add_soa").on('click.save_add_soa', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save_add');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_add_soa').off('click.add_soa').on('click.add_soa', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_add.trigger('submit');
		});
		
			
		form_add.off("submit.add_soa").on("submit.add_soa", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				
				var btn_save_add = $('input[name="btn_action"]', form_add).val();
				
				process(data, 'modal_add_soa', btn_save_add);
			}
		});
	}
	
	var process = function(data, label, btn_save_add)
	{
		var url		= $base_url +  $module + '/add_soa'; 
		
		var btn_label	= btn_save_add == "save" ? 'submit_' + label : 'save_add_modal_add_soa';

		button_loader(btn_label, 1);
		
		$.post(url , data, function(result){			
			button_loader(btn_label, 0);
			
			notification_msg(result.status, result.msg);
			
			if(result.status == 'success')
			{	
				if(result.action == 'save'){
					$("#" + label).modal("close");
					load_index('tab_soa', 'outbound_truckers/soa', 'transactions');
				}
			}
		}, 'json');
	}

	return {
		save : function(){
			save();
		}
	}
}();