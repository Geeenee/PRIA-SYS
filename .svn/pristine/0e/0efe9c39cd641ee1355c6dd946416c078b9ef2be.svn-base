var Sign_up = function()
{
	var $module = "home";
	
	var initObj = function( datatable_options )
	{
		var options 		= JSON.parse( datatable_options );

		$(".link-filter").click(function(){
			var id = $(this).prop('id');
			
			$("#" + id).addClass("active");
			$(".link-filter").not("#" + id).removeClass("active");
			
			/*if(id == 'link_inactive_btn')
				$("#users_table thead th:eq(4)").html("Last Logged In");
			else
				$("#users_table thead th:eq(4)").html("Roles");*/
		});

		$("#filter_user_status").on("change", function(){
			var status 		= $(this).val();

			options.path 	= $module + "/Sign_up/get_user_list/" + status;
			
			load_datatable(options);
		});
	}

	var main_role_dropdown 		= function( main_role, other_role, role_json, value_sel ) 
	{
		var json_dec 	= JSON.parse( $('#sign_up_role_json').val() );

		if( json_dec[ value_sel ] !== undefined )
		{
			delete json_dec[ value_sel ];
		}

		if( Object.keys(json_dec).length !== 0 )
		{
			other_role.removeOption( value_sel );

			for( var value in json_dec )
			{
				other_role.addOption( { value : value, text : json_dec[ value ] } );
			}
		}
	}

	var other_role_dropdown 	= function( main_role, other_role, role_json, value_sel )
	{
		var json_dec 	= JSON.parse( $('#sign_up_role_json').val() );
		var val 		= value_sel,
			len 		= val.length,
			i 			= 0;

		if( len !== 0 )
		{
			for( ; i < len; i++ )
			{
				main_role.removeOption( val[ i ] );

				if( json_dec[ val[ i ] ] !== undefined )
				{
					delete json_dec[ val[ i ] ];
				}
			}
		}

		if( Object.keys(json_dec).length !== 0 )
		{
			// 

			for( var value in json_dec )
			{

				main_role.addOption( { value : value, text : json_dec[ value ] } );
			}
		}
	}

	var handle_role_dropdown 	= function( parent )
	{
		var role_json 	= $('#sign_up_role_json').val();
		var other_role 	= parent.find('select.other_role')[0].selectize;
		
		var main_role 	= parent.find('select.main_role')[0].selectize;

		if( main_role.getValue() != '' )
		{
			main_role_dropdown( main_role, other_role, role_json, main_role.getValue() );
		}

		if( other_role.getValue().length !== 0 )
		{
			other_role_dropdown( main_role, other_role, role_json, other_role.getValue() );
		}

		if( role_json != '' )
		{
			var role_json_dec = JSON.parse( role_json );

			main_role.on('change', function( e )
			{
				main_role_dropdown( main_role, other_role, role_json, this.getValue() );

				// 
			});

			other_role.on('change', function( e )
			{
				other_role_dropdown( main_role, other_role, role_json, this.getValue() );
				
			});
		}
	}
	
	var initTable = function()
	{
		$('.popmodal-dropdown').click(function()
		{
			var data 		= $(this).data();
			
			var parents 	= $(this).parents('.table-actions').find('.popModal');
			var appr_cont 	= parents.find('.approve_content');

			$("#" + data.idSelector).val(data.id);
			
			if(data.idSelector === 'approve_id')
			{
				handle_role_dropdown(appr_cont);
				$("#approve_user_roles")[0].selectize.clear();
				$("#main_role")[0].selectize.clear();
			}
			else
			{
				$("#reject_reason").val("");
			}
		});
	}
	
	var updateStatus = function(form_id, status_id)
	{
		var data = $("#" + form_id).serialize() + "&status_id=" + status_id;
	
		$.blockUI({ 
            message: '<img src="'+$base_url+'static/images/loading.gif">'
        });
		$.post($base_url + $module + "/Sign_up/update_user_status/", data, function(result){
			notification_msg(result.status, result.msg);
			
			if(result.status == "success"){
				
				$("#ctr_pending").html('new <span>'+result.pending+'</span>');
				$("#ctr_disapproved").html('rejected <span>'+result.disapproved+'</span>');
				$("#ctr_approved").html('approved <span>'+result.approved+'</span>');
				
				var status_id = $("#filter_user_status").val();


				
				load_datatable(result.datatable_options);
			}
			$.unblockUI();
		}, 'json');	
	}
	
	var resendEmail = function(id, status, email)
	{
		$('#confirm_modal').confirmModal({
			topOffset : 0,
			onOkBut : function() {
				var data = "id=" + id+ "&status_id=" + status;
				$.blockUI({ 
		            message: '<img src="'+$base_url+'static/images/loading.gif">'
		        });
				$.post($base_url + $module + "/Sign_up/resend_approval_email/", data, function(result){
					
					$.unblockUI();
					notification_msg(result.status, result.msg);
				}, 'json');
			},
			onCancelBut : function() {},
			onLoad : function() {
				$('.confirmModal_content h4').html('Are you sure you want to resend the email?');	
				$('.confirmModal_content p').html('This will send another copy of the message to <strong>' + email + '</strong>');
			},
			onClose : function() {}
		});
	}

	var search_func 	= function(search_params)
	{
		var id 	= $(".link-filter.active").attr('id');
		
		if( id == 'ctr_pending' )
		{
			search_params['search_status_sign_up'] 	= 0;
		}
		else if( id == 'ctr_disapproved' )
		{
			search_params['search_status_sign_up'] 	= CONSTANTS.DISAPPROVED;
		}
		else if( id == 'ctr_approved' )
		{
			search_params['search_status_sign_up'] 	= CONSTANTS.APPROVED;
		}

		return search_params;
	}
	
	return {
		initObj : function( datatable_options )
		{
			initObj( datatable_options );
		},
		initTable : function()
		{
			initTable();
		},
		updateStatus : function(form_id, status)
		{
			updateStatus(form_id, status);
		},
		resendEmail : function(id, status, email)
		{
			resendEmail(id, status, email);
		},
		search_func : function(search_params)
		{
			return search_func(search_params);
		}

	}
}();