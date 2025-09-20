var Permissions = function()
{
	var $module = "user_management";
	
	var initForm = function()
	{
		$('#system_filter').on('change', function(){
			$('#role_filter').selectize();
			var system_val = $(this).val();
				role = $('#role_filter')[0].selectize;
			
			role.disable();
			
			$.post($base_url + $module + "/permissions/reset_options/" + system_val, function(result){
				role.clearOptions();
				role.load(function(callback) {
					callback(result);
					role.enable();
				});
			}, 'json');
		});
		
		$('#role_filter').on('change', function(){
			var val = $(this).val();
			
			if(val === ""){
				$("#programs_tbody").html("<tr><td colspan='4' style='text-align:center;'>Please select system and role first.</td></tr>");
				$("#save_permission").prop("disabled", true);
			} else {
			  loadPermission();
			}
		});
		
		if( $('#system_filter').val() != '' )
		{
			$('#system_filter').trigger('change');
		}

		$('#check_all').on('change', function(){
			var checked = $(this).prop("checked");
			
			$("input:checkbox").prop('checked', checked);

			var selects = $('select.selectize-permissions').not('.filter');
			
			selects.each(function( index ) {
				if(checked){
					$(this)[0].selectize.enable();
				} else {
					$(this)[0].selectize.disable();
				}
			}); 
			
		});
		
	}
	
	var initCheckbox = function()
	{
		$(document).on('change', '.ind_checkbox', function(){
			var element = $(this);
			var checked = $(this).prop('checked');
			var selects = $(this).closest('tr').find('select');
			var action_sel = selects[0].selectize;
			
			var scope_sel = (selects.hasOwnProperty(1)) ? selects[1].selectize : 0;

			var self 		= this;
			
			if(checked)
			{
				par_check_proc( self );

				action_sel.enable();
				
				if(scope_sel !== 0)
					scope_sel.enable();
				
				checkSelected();
			}
			else
			{
				if(action_sel.getValue() != ""){
					$('#confirm_modal').confirmModal({
						topOffset : 0,
						onOkBut : function() {

							par_check_proc( self );
							action_sel.clear();
							action_sel.disable();
							
							if(scope_sel !== 0){
								scope_sel.clear();
								scope_sel.disable();
							}
							
							$('#check_all').prop("checked", false);
						},
						onCancelBut : function() {
							element.prop("checked", true);
						},
						onLoad : function() {
							$('.confirmModal_content h4').html('Are you sure you want to remove this module permission?');	
							$('.confirmModal_content p').html('This action will clear all selected module actions and scope and cannot be undone.');
						},
						onClose : function() {}
					});
				} else {
					par_check_proc( self );

					action_sel.disable();
					
					if(scope_sel !== 0){
						scope_sel.disable();
					}
				
					$('#check_all').prop("checked", false);
				}
			}
		});
	}

	var par_check_proc = function( obj )
	{
		var parent_code = $(obj).attr('data-checkbox');

		var check_ch 	= $(obj).parents('table').find('input[type="checkbox"][data-checkbox="'+parent_code+'"]:checked');

		var par_tr 		= $(obj).parents('table').find('tr.'+parent_code+'_tr');

		if( par_tr.length !== 0 )
		{

			var sel_par 	= par_tr.find('td select.selectize-permissions');
			var par_check 	= par_tr.find('td input[type="checkbox"]');

			if( check_ch.length !== 0 )
			{
				// console.log(sel_opt);
				sel_par[0].selectize.enable();
				sel_par[0].selectize.destroy();
				var sel_opt 	= sel_par.find('option');
				
				var par_val;

				if( sel_opt.length !== 0 )
				{

					var i 	= 0,
						len = sel_opt.length;
						
					for( ; i < len; i++ )
					{
						if( $(sel_opt[i]).val() != '' )
						{
							par_val = $(sel_opt[i]).val();

							break;
						}
					}
				}

				sel_par.selectize({plugins: ['remove_button']});
				sel_par[0].selectize.addItem(par_val);
				par_check.prop('checked', true);
			}
			else
			{
				sel_par.val('');

				if( sel_par[0].selectize === undefined )
				{
					sel_par.selectize({plugins: ['remove_button']});
				}
				
				sel_par[0].selectize.clear();
				sel_par[0].selectize.disable();
				par_check.prop('checked', false);
			}

		
			par_check_proc(par_tr.find('.ind_checkbox'));
		}
	}
	
	var save = function()
	{
		$('#permission_form').on('submit', function(e){
			e.preventDefault();

			var data = $(this).serialize();

			button_loader("save_permission", 1);
			
			$.ajax({
				url : $base_url + $module + '/permissions/save',
				data : data,		
				dataType : 'json',
				method : 'POST',
				success : function(response){
					notification_msg(response.status, response.msg);
					button_loader("save_permission", 0);

					$("html, body").animate({ scrollTop: 0 }, "slow");
				},
				error : function(jqXHR, textStatus, errorThrown){
					console.log('Error : '+textStatus);
				}
			});
			
		});	
	}
	
	var loadPermission = function()
	{
		//$('#programs_tbody').isLoading();
		start_loading();
		var form_data = { 'system' :  $('#system_filter').val(), 'role' : $('#role_filter').val() };
		$.ajax({
			url : $base_url + $module + '/permissions/get_permission',
			method : 'POST',
			dataType : 'html',
			data : form_data,
			success : function(response){
				$('#programs_tbody').isLoading("hide").html(response);
				$('#programs_tfoot').show();
				$('select.selectize-permissions').not('.filter').selectize({
					plugins: ['remove_button']
				});
				checkSelected();
				
				$("#save_permission").prop("disabled", false);
				end_loading();
			},
			error : function(jqXHR, textStatus, errorThrown){
				end_loading();
				console.log('Error : '+textStatus);
				$("#save_permission").prop("disabled", true);
			}
		});	
	}
	
	var checkSelected = function()
	{
		if($('.ind_checkbox:checked').length == $('.ind_checkbox').length){
			$('#check_all').prop("checked", true);
		} else {
			$('#check_all').prop("checked", false);
		}
	}

	return {
		initForm : function()
		{
			initForm();
		},
		initCheckbox : function()
		{
			initCheckbox();
		},
		save : function()
		{
			save();
		}
	}
}();