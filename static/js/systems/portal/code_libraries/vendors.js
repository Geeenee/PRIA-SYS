var Vendors = function() {
	
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
		 deleteObj = new handleData({ module: $module, controller : 'vendors', method : 'delete_vendor'  });	
	}
	
	var save = function()
	{		
		
		var form_add = $('#form_modal_add_vendor'); 		
		form_add.parsley();
		
		var form_edit = $('#form_modal_edit_vendor'); 		
		form_edit.parsley();
				
		$('#save_add_modal_add_vendor').off("click.save_add_vendor").on('click.save_add_vendor', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save_add');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_add_vendor').off('click.add_vendor').on('click.add_vendor', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_add.trigger('submit');
		});
		
		$('#submit_modal_edit_vendor').off('click.edit_vendor').on('click.edit_vendor', function(e){
			e.preventDefault();
			
			$('input[name="btn_action"]', form_add).val('save');
			
			form_edit.trigger('submit');
		});
			
		form_add.off("submit.add_vendor").on("submit.add_vendor", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				
				var btn_save_add = $('input[name="btn_action"]', form_add).val();
				
				process(data, 'modal_add_vendor', btn_save_add);
			}
		});
		
		form_edit.off("submit.edit_vendor").on("submit.edit_vendor", function(e) {
			
			e.preventDefault();			
			
			if ( $(this).parsley().isValid() ) {

				var data = $(this).serialize();
				process(data, 'modal_edit_vendor');
			}
		});
	}
	
	var process = function(data, label, btn_save_add)
	{
		var url		= $base_url +  $module + '/vendors/process'; 
		var btn_label	= btn_save_add == "save_add" ?  'save_add_modal_add_vendor': 'submit_' + label;
		
		button_loader(btn_label, 1);
		
		$.post(url , data, function(result){
			
			button_loader(btn_label, 0);
			
			notification_msg(result.status, result.msg);
			
			if(result.status == 'success'){
				if(result.action == 'save'){
					$("#" + label).modal("close");
				}else{
					$('input[name="vendor_code"]').val('');
					$('input[name="vendor_name"]').val('');
					$('input[name="description"]').val('');
					$('input[name="district_code"]').val('');
					$('input[name="bldg_st"]').val('');
					
					var $cost_center_code = $('#cost_center_code').selectize();
					var control_cost_center_code = $cost_center_code[0].selectize;
					control_cost_center_code.clear();

					var $account_groups = $('#account_groups').selectize();
					var control_account_groups = $account_groups[0].selectize;
					control_account_groups.clear();

					var $region_code = $('#region_code').selectize();
					var control_region_code = $region_code[0].selectize;
					control_region_code.clear();

					var $province_code = $('#province_code').selectize();
					var control_province_code = $province_code[0].selectize;
					control_province_code.clear();

					var $muni_city_code = $('#muni_city_code').selectize();
					var control_muni_city_code = $muni_city_code[0].selectize;
					control_muni_city_code.clear();

					var $barangay_code = $('#barangay_code').selectize();
					var control_barangay_code = $barangay_code[0].selectize;
					control_barangay_code.clear();
				}
				
				var opt = JSON.parse(result.datatable);
				load_datatable(opt);
			}
			
			
		}, 'json');
	}

	var location = function()
	{
		$('#region_code')[0].selectize.on('change', function(value){
			var option = this.options[value];
			var $elem  = $('#province_code');
			
			set_list_value(value, 'province_code', $elem);
		});

		$('#province_code')[0].selectize.on('change', function(value){
			var option = this.options[value];
			var $elem  = $('#muni_city_code');
			
			set_list_value(value, 'muni_city_code', $elem);
		});

		$('#muni_city_code')[0].selectize.on('change', function(value){
			var option = this.options[value];
			var $elem  = $('#barangay_code');
			

			set_list_value(value, 'barangay_code', $elem);
		});
	}

	var set_list_value = function(id, type, elem)
	{
	    var ajax_url		= $base_url +  $module + '/vendors/get_options';

	    var options = {
            path    :  ajax_url,
            data    :  {'id' : id, 'type' : type},
            elem    :  elem, 
        };

        set_dropdown_values_ajax(options);   
    };

    var set_dropdown_values_ajax = function(options)
	{
		if(options == 'undefined' || options == null || jQuery.isEmptyObject(options) == true){
			console.log('options must not be empty');
			return false;
		}

		$elem = options.elem;

		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		var ajax_options = {
            url     :  options.path,
            data    :  options.data,
            success :  function(response){
                var elem_select = $elem[0].selectize;

                elem_select.clear();
                elem_select.clearOptions();
                elem_select.load(function(callback){
                     callback(response);
                });
            }
        };

      	ajax_request(ajax_options);
	};

	return {
		
		initialize : function(options)
		{
			loadTable(options);
			remove();
		},
		
		save : function()
		{
			save();
		},
		
		location : function()
		{
			location();
		},

		setDropDownValuesAjax : function(options)
		{
			set_dropdown_values_ajax(options);
		}
	
	}
	

}();