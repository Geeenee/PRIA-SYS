var Sites = function() {
	
	var $module		= "code_libraries";

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
	
	var loadTable = function(options)
	{
		$("#refresh_btn").on("click", function(){			
			var opt = JSON.parse(options);
			load_datatable(opt);
		});
	}
	
	var remove = function()
	{
		 deleteObj = new handleData({ module: $module, controller : 'sites', method : 'delete_site'  });	
	}
	
	var save = function()
	{		
		var form_add = $('#form_modal_add_site'); 		
		form_add.parsley();
		
		var form_edit = $('#form_modal_edit_site'); 		
		form_edit.parsley();
				
		$('#save_add_modal_add_site').off("click.save_add_site").on('click.save_add_site', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save_add');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_add_site').off('click.add_site').on('click.add_site', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_edit_site').off('click.edit_site').on('click.edit_site', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_edit.trigger('submit');
		});
			
		form_add.off("submit.add_site").on("submit.add_site", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				
				var btn_save_add = $('input[name="btn_action"]', form_add).val();
				
				process(data, 'modal_add_site', btn_save_add);
			}
		});
		
		form_edit.off("submit.edit_site").on("submit.edit_site", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				process(data, 'modal_edit_site');
			}
		});

		var selBc = document.getElementById('business_center');
		var selVn = document.getElementById('vendor_code');
		var selSt = document.getElementById('site_type_code');
		var selCc = document.getElementById('cost_center_code');

		selSt.selectize.off('change');
		selSt.selectize.on('change', (val) => {

			const vnClass = document.querySelector('label[for="vendor_code"]').classList;
			const ccClass = document.querySelector('label[for="cost_center_code"]').classList;

			selVn.dataset.parsleyRequired = true;
			vnClass.add('required');
			selVn.closest('div.col').classList.remove('hide');

			if(val == $.CONSTANTS.SITE_TYPE_COST_CENTER)
			{
				/* selBc.closest('div.row.m-b-lg').classList.add('hide');
				selBc.dataset.parsleyRequired = false; */
				selVn.closest('div.col').classList.add('hide');
				selVn.dataset.parsleyRequired = false;
			}
			else
			{
				/* selBc.closest('div.row.m-b-lg').classList.remove('hide');
				selBc.dataset.parsleyRequired = true; */
				selVn.closest('div.col').classList.remove('hide');
				selVn.dataset.parsleyRequired = true;

				if(val == $.CONSTANTS.SITE_TYPE_OFFICE)
				{
					selVn.dataset.parsleyRequired = false;
					vnClass.remove('required');
					selVn.closest('div.col').classList.add('hide');
				}
			}
			

			if(val != $.CONSTANTS.SITE_TYPE_FARM)
			{
				selCc.dataset.parsleyRequired = true;
				ccClass.add('required');

				if(val == $.CONSTANTS.SITE_TYPE_OFFICE)
				{
					selVn.dataset.parsleyRequired = false;
					vnClass.remove('required');
					selVn.closest('div.col').classList.add('hide');
				}
			}
			else
			{
				selCc.dataset.parsleyRequired = false;
				ccClass.remove('required');
			}
			

			form_add.parsley().refresh();
			form_add.parsley().validate();
		});	

		selBc.selectize.off('change');
		selBc.selectize.on('change', (val) => {
			site_type_code = $('#site_type_code').val();

			if(site_type_code)
			{
				var options 		= {
					blockUI    : true,
					body 	   : 'org_code=' + val + '&site_type_code=' + site_type_code,
					path       : $base_url + 'code_libraries/sites/get_site_vendors/',
					successFunc: function(response){
						if(response.flag == $.CONSTANTS.ERROR)
						{
							notification_msg(response.flag, response.msg);
						}	
						else	
						{
							selVn.selectize.clearOptions();
							selVn.selectize.addOption(response.options);
							selVn.selectize.refreshOptions();
						}	
					}
				};
				
				General.Fetch(options); 	
			}
			else
			{
				selBc.selectize.clear(true);

				notification_msg($.CONSTANTS.ERROR, 'Please select a site type first.');
			}
		});
	}
	
	var process = function(data, label, btn_save_add)
	{
		var url		= $base_url +  $module + '/sites/process'; 
		var options = {
			blockUI    : true,
			body 	   : data,
			path       : url,
			successFunc: function(result){
				notification_msg(result.status, result.msg);	

				if(result.status == $.CONSTANTS.SUCCESS)
				{
					if(result.action == 'save')
						$("#" + label).modal("close");

					var opt = JSON.parse(result.datatable);
					load_datatable(opt);
				}	
			}
		};
		
		General.Fetch(options); 	

/* 
	
		//var btn_label	= btn_save_add == "save" ? 'submit_' + label : 'save_modal_add_site';
		var btn_label = 'submit_modal_edit_site';

	
		
		button_loader(btn_label, 1);
		
		$.post(url , data, function(result){
			
			button_loader(btn_label, 0);
			
			notification_msg(result.status, result.msg);
			
			if(result.status == 'success'){
				if(result.action == 'save'){
					$("#" + label).modal("close");
				}else{
					// $('input[name="vendor_code"]').val('');
					// $('input[name="vendor_name"]').val('');
					// $('input[name="description"]').val('');
					// $('input[name="district_code"]').val('');
					// $('input[name="bldg_st"]').val('');
					
					// var $cost_center_code = $('#cost_center_code').selectize();
					// var control_cost_center_code = $cost_center_code[0].selectize;
					// control_cost_center_code.clear();

					// var $account_groups = $('#account_groups').selectize();
					// var control_account_groups = $account_groups[0].selectize;
					// control_account_groups.clear();

					// var $region_code = $('#region_code').selectize();
					// var control_region_code = $region_code[0].selectize;
					// control_region_code.clear();

					// var $province_code = $('#province_code').selectize();
					// var control_province_code = $province_code[0].selectize;
					// control_province_code.clear();

					// var $muni_city_code = $('#muni_city_code').selectize();
					// var control_muni_city_code = $muni_city_code[0].selectize;
					// control_muni_city_code.clear();

					// var $barangay_code = $('#barangay_code').selectize();
					// var control_barangay_code = $barangay_code[0].selectize;
					// control_barangay_code.clear();
				}
				
				var opt = JSON.parse(result.datatable);
				load_datatable(opt);
			}
			
			
		}, 'json'); */
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