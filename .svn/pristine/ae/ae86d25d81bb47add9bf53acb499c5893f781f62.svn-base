var Users = function()
{
	var $module = "user_management";
	var $base_url = $("#base_url").val();
	var selected_single;
	var on_c_sel 			= {};

	var initObj = function()
	{
		deleteObj = new handleData({ controller : 'users', method : 'delete_user', module: $module, fn: async () => {
			await $.get($base_url + $module + "/users/get_stats", function(res) {
				$('#link_active_btn span').text(res.active_count);
				$('#link_inactive_btn span').text(res.inactive_count);
				$('#link_blocked_btn span').text(res.blocked_count);
			}, 'json');
		}});
		
		$(".link-filter").click(function(){
			var id = $(this).prop('id');
			
			$("#" + id).addClass("active");
			$(".link-filter").not("#" + id).removeClass("active");
			
			// if(id == 'link_inactive_btn')
			// 	$("#users_table thead th:eq(4)").html("Last Logged In");
			// else
			// 	$("#users_table thead th:eq(4)").html("Roles");
		});
		
		$("#refresh_btn").click(function(){
			$(".link-filter").removeClass("active");
			$("#link_active_btn").addClass("active");
		});
	}

	var load_data_options = function(data, sel_id)
	{
		// console.log(data);
		// console.log(sel_id);
		$('#'+sel_id)[0].selectize.clear();
		$('#'+sel_id)[0].selectize.clearOptions();

		let elem_select_cc = $('#'+sel_id)[0].selectize;
		elem_select_cc.clearOptions();
		elem_select_cc.addOption(data);

		if (data.length == 1) {
			elem_select_cc.setValue(data[0].value);
		}
	};

	var main_role_dropdown 		= function( main_role, other_role, role_json, value_sel ) 
	{
		var json_dec 	= JSON.parse( $('#role_json').val() );

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
		var json_dec 	= JSON.parse( $('#role_json').val() );
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

	var handle_role_dropdown 	= function()
	{
		var role_json 	= $('#role_json').val();
		var other_role 	= $('#role')[0].selectize;
		
		var main_role 	= $('#main_role')[0].selectize;

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
	
	var initForm = function()
	{
		handle_role_dropdown();

		if($("#user_id").val() != '')
		{
			$('.input-field label').addClass('active');
			$('.labelauty').next().removeClass('active');
		}
		
		toggleContactFields($("input[name='contact_type']:checked").val());
		
		$(".contact_flag").off('change').on('change', function(){
			var value = $(this).val();
			toggleContactFields(value);
		});

		$("#main_role").off("change").on("change", function(){
			// console.log('here', $(this).val());

			if($(this).val() == 'VENDOR')
			{
				$("#vendor").closest('#div_vendor').removeClass('hide');
				$("#role").closest('#div_role').addClass('hide');
			}
			else
			{	
				$("#vendor").closest('#div_vendor').addClass('hide');
				$("#role").closest('#div_role').removeClass('hide');
			}
        })
        
        $('#org')[0].selectize.setValue($('#xorg').val().split(','));
        $('#main_role')[0].selectize.setValue($('#xmain_role').val().split(','));
        $('#role')[0].selectize.setValue($('#xother_role').val().split(','));
        $('#vendor')[0].selectize.setValue($('#xvendor_code').val().split(','));

		// $("#vendor").on("change", function(){
			
		// 	var data = $(this).serialize();

		// 	$.post($base_url + $module+'/users/get_user_org', data, function(response){
				
				// response 	= JSON.parse( result );
				// console.log(response.org_code.length);

				// for(var i = 0; i < response.org_code.length; i++)
			 //   	{
			 //   		console.log(response.vendor_code);
			 //    }

				// $select[0].selectize.setValue("US");

		// 	}, 'json');
			
		// })
	}

	var convertArrayToOptions = function(data)
	{
		console.log(data);

		return JSON.parse(data);
	}
	
	var toggleContactFields = function(value)
	{
		if(value == 1)
		{
			$("#username").attr("data-parsley-required", "false");
			$("#password").attr("data-parsley-required", "false");
			$("#confirm_password").attr("data-parsley-required", "false");
			
			$("#main_role").attr("data-parsley-required", "false");
			$("#user_cancel_email").prop("checked","checked");
			
			$("label[for='username'], label[for='password'], label[for='confirm_password'], label[for='main_role']").removeClass("required");

			
		}else{
			$("#username").attr("data-parsley-required", "true");
			
			$("#main_role").attr("data-parsley-required", "true");
			$("#user_send_email").prop("checked","checked");
			
			if($("#form_modal_user_mgmt #user_id").val() == '')
			{
				$("#password").attr("data-parsley-required", "true");
				$("#confirm_password").attr("data-parsley-required", "true");
				$("label[for='password'], label[for='confirm_password']").addClass("required");
			}
			
			$("label[for='username'],  label[for='main_role']").addClass("required");
		}
	}
	
	var successCallback 	= function(arr, data)
	{
		arr = JSON.parse(arr);

		if( CONSTANTS.CHECK_CUSTOM_UPLOAD_PATH )
		{
			var avatar = output_image(data[0], arr.path);
		}
		else
		{
			var avatar = $base_url + arr.path + data;
		}

		$("#" + arr.id + "_src").attr("src", avatar);
		
		$("#" + arr.id).val(data);
		$("#" + arr.id + "_upload").prev(".ajax-file-upload").hide();
		$("#" + arr.id + "_upload + div + div.ajax-file-upload-statusbar .ajax-file-upload-red").text("Delete");
	}

	var deleteCallback 	= function(arr)
	{
		arr = JSON.parse(arr);
		
		$("#" + arr.id + "_upload + div + div.ajax-file-upload-statusbar .ajax-file-upload-error").fadeOut();	
		$("#" + arr.id).val(''); 
		
		// for default image value sent from controller
		if(typeof(arr.default_image_preview) != "undefined" && arr.default_image_preview !== null) {
			var avatar = $base_url + arr.path_images + arr.default_image_preview;
			$("#" + arr.id + "_src").attr("src", avatar);
		}
			
		$("#" + arr.id + "_upload").prev(".ajax-file-upload").show();

		$.post( $base_url+'user_management/Users/check_if_created_user', { user_id : $( "#user_id" ).val() }, function( response ) {

			if( response.check == 1 ) 
			{
				var avatar = $base_url+ PATH_IMAGES + "avatar.jpg";
		  
				$("#top_bar_avatar").addClass("profile_avatar");
				$("#top_bar_avatar").attr("data-name", response.name);

				create_avatar($('.profile_avatar'), {width:80,height:80,fontSize:50});
			}

		}, 'json' );
	}

	var save = function()
	{
		$('#form_modal_user_mgmt').parsley();
		
		$('#form_modal_user_mgmt').off("submit").on("submit", function(e) {
		  e.preventDefault();
		  e.stopImmediatePropagation();

		  if ( $(this).parsley().isValid() ) {
			var data = $(this).serialize();
		  
			button_loader('submit_modal_user_mgmt', 1);
			$.post($base_url + $module + "/users/process", data, function(result) {
				
				notification_msg(result.status, result.msg);
				button_loader('submit_modal_user_mgmt', 0);
				
				if(result.status == "success")
				{
					$.get($base_url + $module + "/users/get_stats", function(res) {
						$('#link_active_btn span').text(res.active_count);
						$('#link_inactive_btn span').text(res.inactive_count);
						$('#link_blocked_btn span').text(res.blocked_count);
					}, 'json');

					$("#modal_user_mgmt").modal("close");
					load_datatable(result.datatable_options);
				}
				
			}, 'json');       
		  }
		});
	}
	
	var initProdModal = function()
	{
		$("#conent_products").off("click").on("click", ".change-assign-trigger", function(){
		 
		 	var id = $(this).closest('.toggle-products').attr('id');
			var id_val = id.split('toggle_product_')[1]; 
			
			toggle("change-assign-trigger", id_val, id);
		});
		
		$("#conent_products").off("click").on("click", ".save_people_product", function(){
			var $data = {data_val : $(this).data('value'), product_id : $(this).closest('.toggle-products').find('select').val(), selected_user : $("#selected_user").val()};
			$.post($base_url + $module+'/users/process_people_product', $data, function(result){
				if(result.status != "success")
				{
					notification_msg(result.status, result.msg);
				}
				else if(result.status == "success")
				{
					
					$("#conent_products").html(result.content);
				}

			}, 'json');

		});
		$("#form_modal_assign_product").find('.modal-footer').find('button').css('cssText', "display : none !important");
		$("#form_modal_assign_product").find('.modal-footer').find('.modal-close').html('Close');

	}
	
	var toggle = function(trigger_class, id, elem) {
		var toggle_class = $("." + trigger_class).data('toggle'),
			elem = "#" + elem || "";
			console.log($(elem + " #field_wrapper_"+ id ));
		$("#conent_products " + elem + " ." + toggle_class).toggle("slow", function(){
			var field_wrapper_visible = $("#conent_products " + elem + " #field_wrapper_"+ id ).is( ":visible" ),
				field_wrapper_hidden  = $("#conent_products " + elem + " #field_wrapper_"+ id ).is( ":hidden" ),
				
				text_wrapper_visible = $("#conent_products " + elem + " #assign_text_wrapper_"+ id ).is( ":visible" ),
				text_wrapper_hidden  = $("#conent_products " + elem + " #assign_text_wrapper_"+ id ).is( ":hidden" );
				
			if(field_wrapper_visible && text_wrapper_hidden)
			{	
				$(elem + " #action_save_" + id).show();
			}else{
				$(elem + " #action_save_" + id).hide();
			}
		});
	}

	var search_func 	= function(search_params)
	{
		var status;

		var id 	= $(".link-filter.active").attr('id');

		if( id == 'link_active_btn' )
		{
			status 	= 'STATUS_ACTIVE';
		}
		else if( id == 'link_inactive_btn' )
		{
			status 	= 'STATUS_INACTIVE';
		}
		else if( id == 'link_blocked_btn' )
		{
			status  = 'STATUS_BLOCKED';
		}

		search_params['status']	= status;
		
		return search_params
	}

	return {
		initObj : function()
		{
			initObj();
		},
		initForm : function()
		{
			initForm();
		},
		initProdModal : function()
		{
			console.log('test');
			initProdModal();
		},
		save : function()
		{
			save();
		},
		successCallback : function(arr, data)
		{
			successCallback(arr, data);
		},
		deleteCallback : function(arr)
		{
			deleteCallback(arr);
		},
		search_func : function(search_params)
		{
			return search_func(search_params);
		},
		load_data_options : function(data, sel_id)
		{
			load_data_options(data, sel_id);
		}
	}
}();