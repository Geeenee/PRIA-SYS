var Business_centers = function() {
	
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
	
var $module		= "code_libraries";
	
	var loadTable = function(options)
	{
		$("#refresh_btn").on("click", function(){			
			var opt = JSON.parse(options);
			load_datatable(opt);
		});
	}
	
	var remove = function()
	{
		 deleteObj = new handleData({ module: $module, controller : 'business_centers', method : 'delete_business_center'  });	
	}
	
	var save = function()
	{		
		
		var form_add = $('#form_modal_add_business_center'); 		
		form_add.parsley();
		
		var form_edit = $('#form_modal_edit_business_center'); 		
		form_edit.parsley();
				
		$('#save_add_modal_add_business_center').off("click.save_add_business_center").on('click.save_add_business_center', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save_add');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_add_business_center').off('click.add_business_center').on('click.add_business_center', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_edit_business_center').off('click.edit_business_center').on('click.edit_business_center', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_edit.trigger('submit');
		});
			
		form_add.off("submit.add_business_center").on("submit.add_business_center", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				
				var btn_save_add = $('input[name="btn_action"]', form_add).val();
				
				process(data, 'modal_add_business_center', btn_save_add);
			}
		});
		
		form_edit.off("submit.edit_business_center").on("submit.edit_business_center", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				process(data, 'modal_edit_business_center');
			}
		});
	}
	
	var process = function(data, label, btn_save_add)
	{
		var url		= $base_url +  $module + '/business_centers/process'; 
		
		var btn_label	= btn_save_add == "save" ? 'submit_' + label : 'save_add_modal_add_business_center';
		
		

		button_loader(btn_label, 1);
		
		$.post(url , data, function(result){
			
			button_loader(btn_label, 0);
			
			notification_msg(result.status, result.msg);		
			
			if(result.status == 'success')
			{
				
				if(result.action == 'save')					
					$("#" + label).modal("close");
				else
				{
					$('input[name="bc_name"]').val('');
					$('input[name="bc_code"]').val('');
				}
				
				
				var opt = JSON.parse(result.datatable);
				load_datatable(opt);
			}
			
			
		}, 'json');
	}

	return {
		
		initialize : function(options)
		{
			loadTable(options);
			remove();
		},
		
		save : function()
		{
			save();
		}
		
	
	}
	

}();