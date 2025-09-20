var Settings = function()
{
	var $module = "settings";
	var terms_upl 	= [];
	var terms_file 	= {};
	
	var init = function()
	{
	}
	
	var initForm = function()
	{
		set_active_tab($module);
	}
	
	var initSiteSettings = function($sidebar_menu, $skin, $header, $menu_position, $menu_display, $menu_type)
	{
		$("#site_settings_form #site-info input").each(function(){
			if( $(this).val().length > 0 ) {
				var id = $(this).attr("id");
				$("label[for='"+ id +"']").addClass("active");
			}
		});
		
		if($sidebar_menu == 'cd-nav-compact'){ 
		  $("#layout_collapsed").prop("checked", true);
		} else {
		  $("#layout_expanded").prop("checked", true);
		}

		if($("#terms_conditions").val() != '' )
		{


			var terms_upl_det 	= $("#terms_conditions").val().split('|'),
				t_i 			= 0,
				t_l 			= terms_upl_det.length;

			for( ; t_i < t_l; t_i++ )
			{
				var t_d 		= terms_upl_det[ t_i ].split('=');

				terms_upl.push( t_d[0] );
				terms_file[ t_d[0] ] = t_d[1];
			}
		}

		load_editor("agremment_text", true);

		toggle('has_agreement_text', 'has_agreement_text_value');
		
		$("#skin_" + $skin).prop("checked", true);
		$("#header_" + $header).prop("checked", true);
		$("#menu_" + $menu_position).prop("checked", true);
		$("#display_" + $menu_display).prop("checked", true);
		$("#type_" + $menu_type).prop("checked", true);

		showHideElem($menu_position);

		$('input[name="menu_position"]').on('change', function(e) {
			var val = $(this).val();
			
			showHideElem(val);
		});
	}
	
	var showHideElem = function(val)
	{
		if(val == 'TOP_NAV')
		{
			$("#top_nav_elem").show();
			$("#side_nav_elem").hide();
		}else {
			$("#top_nav_elem").hide();
			$("#side_nav_elem").show();
		}
	}

	var initAccountSettings = function($account_creator, $login_via, $password_expiry, $password_initial_set)
	{
		$("#account_" + $account_creator).prop("checked", true);
		$("#login_via_" + $login_via).prop("checked", true);
		$("#set_" + $password_initial_set).prop("checked", true);

		if($password_expiry == 1){
			$("#password_expiry").prop("checked", true);
		}
		
		toggle('password_expiry', 'password_expiry_duration');
		toggle('log_in_deactivation', 'log_in_deactivation_duration');
		toggle('auto_log_inactivity', 'auto_log_inactivity_duration');
		toggle('apply_username_constraints', 'apply_username_constraints_div');
	}

	var init_media_setting 	= function()
	{
		toggle('change_upload_path', 'custom_upload_path_div', custom_path_toggle);
	}
	
	var saveSiteSettings = function()
	{
		$("#save_site_settings").on("click", function(){

		  update_editor();
		  var data = $("#site_settings_form").serialize();
		  
		  button_loader('save_site_settings', 1);
		  
		  $.post($base_url + $module + "/site_settings/process", data, function(result){
			  notification_msg(result.status, result.msg);
			  button_loader('save_site_settings', 0);
			  
			  if(result.status == "success")
				location.reload();
		  }, 'json');
		});
	}
	
	var saveAccountSettings = function()
	{
		$('#account_settings_form').parsley();
	
		$('#account_settings_form').submit(function(e) {
			e.preventDefault();
			
			if ($(this).parsley().isValid()) {
			  var data = $(this).serialize();
		  
			  button_loader('save_account_settings', 1);
			  
			  $.post($base_url + $module + "/account_settings/process", data, function(result){
				  notification_msg(result.status, result.msg);
				  button_loader('save_account_settings', 0);
				  
				  if(result.status == "success")
					load_index('tab_account_settings', 'account_settings', $module)
			  }, 'json');       
			}
		});
	}

	var save_media_settting  	= function()
	{
		$('#media_settings_form').parsley();
	
		$('#media_settings_form').submit(function(e) {
			e.preventDefault();
			
			if ($(this).parsley().isValid()) {
		 		var data = $(this).serialize();

		  		button_loader('save_media_settings', 1);

	  		 	$.post($base_url + $module + "/Media_settings/process", data, function(result){

	 		  		notification_msg(result.status, result.msg);
			  		button_loader('save_media_settings', 0);

		  		 	if(result.status == "success")
						load_index('tab_media_settings', 'media_settings', $module)
	  		 	}, 'json');
			}

		});
	}
	
	var successCallback 	= function(arr, data)
	{
		arr 		= JSON.parse(arr);

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
		arr 		= JSON.parse(arr);

		$("#" + arr.id + "_upload + div + div.ajax-file-upload-statusbar .ajax-file-upload-error").fadeOut();	
		$("#" + arr.id).val(''); 
			
		if(typeof(arr.default_image_preview) != "undefined" && arr.default_image_preview !== null) {
			var avatar = $base_url + arr.path_images + arr.default_image_preview;
			$("#" + arr.id + "_src").attr("src", avatar);
		}
			
		$("#" + arr.id + "_upload").prev(".ajax-file-upload").show();
	}

	var custom_path_toggle = function(id, content_id)
	{
		if($("#" + id).is(':checked'))
		{
			$('#new_upload_path').attr('data-parsley-required', 'true');
		}
		else
		{
			$('#new_upload_path').removeAttr('data-parsley-required');
			$('#new_upload_path').val('');
		}
		
	}

	var successCallbackTerm 	= function(arr, data, files)
	{
	/*	$("#" + arr.id).val(data);
		$("#" + arr.id + "_upload").prev(".ajax-file-upload").hide();
		$("#" + arr.id + "_upload + div + div.ajax-file-upload-statusbar .ajax-file-upload-red").text("Delete");*/

		if( arr.multiple !== undefined && arr.multiple === true )
		{
			var i 		= 0,
				len 	= data.length,
				val_obj = [],
				str 	= "";

			terms_upl.push( data[0] );
			terms_file[data[0]] = files[0];
			
			if( terms_upl.length !== 0 )
			{
				var j 	= 0,
					t_l = terms_upl.length;

				for( ;j < t_l; j++ )
				{
					str += terms_upl[ j ]+"="+terms_file[ terms_upl[ j ] ]+"|";
				}

				str 	= str.substring(0, str.length - 1);

				$("#" + arr.id).val(str); 
			}
			
		}
		else
		{
			var avatar = $base_url + arr.path + data;

			$("#" + arr.id + "_src").attr("src", avatar);
		
			$("#" + arr.id).val(data);
			$("#" + arr.id + "_upload").prev(".ajax-file-upload").hide();
			$("#" + arr.id + "_upload + div + div.ajax-file-upload-statusbar .ajax-file-upload-red").text("Delete");
		}

	}

	var deleteCallbackTerm 	= function(arr, data)
	{
		if( arr.multiple !== undefined && arr.multiple === true )
		{
			terms_upl.splice( terms_upl.indexOf( data[0] ), 1 );
			
			delete terms_file[ data[0] ];

			if( terms_upl.length !== 0 )
			{
				var j 	= 0,
					t_l = terms_upl.length,
					str = "";

				for( ;j < t_l; j++ )
				{
					str += terms_upl[ j ]+"="+terms_file[ terms_upl[ j ] ]+"|";
				}

				str 	= str.substring(0, str.length - 1);

				$("#" + arr.id).val(str); 
			}
			else
			{
				$("#" + arr.id).val(''); 
			}
		}
		else
		{

			$("#" + arr.id + "_upload + div + div.ajax-file-upload-statusbar .ajax-file-upload-error").fadeOut();	
			$("#" + arr.id).val(''); 

			if(typeof(arr.default_image_preview) != "undefined" && arr.default_image_preview !== null) {
				var avatar = $base_url + arr.path_images + arr.default_image_preview;
				$("#" + arr.id + "_src").attr("src", avatar);
			}
				
			$("#" + arr.id + "_upload").prev(".ajax-file-upload").show();

		}
	}
	
	return {
		initForm : function()
		{
			initForm();
		},
		initSiteSettings : function($sidebar_menu, $skin, $header, $menu_position, $menu_display, $menu_type)
		{
			initSiteSettings($sidebar_menu, $skin, $header, $menu_position, $menu_display, $menu_type);
		},
		saveSiteSettings : function()
		{
			saveSiteSettings();
		},
		initAccountSettings : function($account_creator, $login_via, $password_expiry, $password_initial_set)
		{
			initAccountSettings($account_creator, $login_via, $password_expiry, $password_initial_set);
		},
		saveAccountSettings : function()
		{
			saveAccountSettings();
		},
		successCallback : function(arr, data)
		{
			successCallback(arr, data);
		},
		deleteCallback : function(arr)
		{
			deleteCallback(arr);
		},
		init_media_setting : function()
		{
			init_media_setting();
		},
		save_media_setting : function()
		{
			save_media_settting();
		},
		custom_path_toggle : function(id, content_id)
		{
			custom_path_toggle(id, content_id);
		},
		init : function()
		{
			init();
		},
		successCallbackTerm( arr, data, files )
		{
			successCallbackTerm( arr, data, files );
		},
		deleteCallbackTerm( arr, data )
		{
			deleteCallbackTerm( arr, data );
		}
	}
}();