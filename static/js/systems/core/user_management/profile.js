var Profile = function()
{
	var $module = "user_management";
		$path_uploads = $("#user_upload_path").val();
		$path_images = "static/images/";
		
	var initForm = function()
	{
		set_active_tab($module);
	}
	
	var initObj = function()
	{
		var form_data = {"dir" : $path_uploads};
		
		if( csrf_name )
		{
			form_data[csrf_name] 	= csrf_token;
		}

		toggle_fields('PROFILE');
		uploadObj = $("#profile_photo").uploadFile({
			url: $base_url + "upload/",
			fileName: "file",
			allowedTypes:"jpeg,jpg,png,gif",
			acceptFiles:"*",	
			dragDrop:false,
			multiple: false,
			maxFileCount: 1,
			allowDuplicates: true,
			duplicateStrict: false,
			showDone: false,
			showAbort: false,
			showProgress: false,
			showPreview: false,
			returnType:"json",	
		//	formData: {"dir" : $path_uploads},		
			formData: form_data,		
			uploadFolder:$base_url + $path_uploads,
			onSelect: function(files){
				$(".avatar-wrapper .ajax-file-upload").hide();
			},
			onSuccess:function(files,data,xhr){ 
				if( CONSTANTS.CHECK_CUSTOM_UPLOAD_PATH )
				{
					var avatar = output_image(data[0], $path_uploads);
				}
				else
				{
					var avatar = $base_url + $path_uploads + data;
				}
				
				$("#profile_img").attr("src", avatar);
				
				$('#user_image').val((data));
				$('.avatar-wrapper .ajax-file-upload-progress').hide();
				$(".avatar-wrapper .ajax-file-upload-red").html("<i class='flaticon-recycle69'></i>");
			},
			showDelete:true,
			deleteCallback: function(data,pd)
			{
				for(var i=0;i<data.length;i++)
				{
					$.post($base_url + "upload/delete/", {op : "delete", name : data[i], dir : $path_uploads},
					function(resp, textStatus, jqXHR)
					{ 
						$(".avatar-wrapper .ajax-file-upload-error").fadeOut();	
						$('#user_image').val(''); 
						
						var avatar = $base_url + $path_images + "avatar.jpg";
						$("#profile_img").attr("src", avatar);
					});
				 }      
				pd.statusbar.hide();
				$(".avatar-wrapper .ajax-file-upload").css("display","");
			},
			onLoad:function(obj)
			{
				$.ajax({
					cache: true,
					url: $base_url + "upload/existing_files/",
					dataType: "json",
					data: { dir: $path_uploads, file: $('#user_image').val()} ,
					success: function(data) 
					{
						for(var i=0;i<data.length;i++)
						{
							obj.createProgress(data[i]);
						}	
						
						if(data.length > 0){
						  $(".avatar-wrapper .ajax-file-upload").hide();
						  $('.avatar-wrapper .ajax-file-upload-progress').hide();
						  $(".avatar-wrapper .ajax-file-upload-red").html("<i class='material-icons'>delete_forever</i>");
						}else{
						  var avatar = $base_url + $path_images + "avatar.jpg";
						  $("#profile_img").attr("src", avatar);
						}
					}
				});
			}
		});
	}
	
	var initModal = function($pass_length, $upper_length, $digit_length, $pass_err)
	{
		$('#current_password').blur(function() {
			var password = $(this).val();
			
			if(password != ''){
			  $(this).addClass('loading');
			  
				$.post($base_url + $module + "/profile/validate_password_prof/", { password : password },function(result){
					if(result.flag == 0){
					  $("#new_password, #confirm_password").closest(".col").addClass("disabled");
					  $('#new_password, #confirm_password, #submit_profile_pass').prop('disabled',true);
					  $('#current_password').addClass('error-loading').removeClass('loading success-loading');
					  
					  $("#new_password, #confirm_password").attr("data-parsley-required", "false");
					} else {
					  $("#new_password, #confirm_password").closest(".col").removeClass("disabled");
					  $('#new_password, #confirm_password, #submit_profile_pass').prop('disabled',false);
					  $('#current_password').addClass('success-loading').removeClass('loading error-loading');
					  
					  $("#new_password, #confirm_password").attr("data-parsley-required", "true");
					}
				}, 'json');
			} else {
				$(this).removeClass('loading error-loading success-loading');
				$('#new_password, #confirm_password, #submit_profile_pass').prop('disabled',true);
				$("#new_password, #confirm_password").attr("data-parsley-required", "false");
			}
			
		});

		/*window.ParsleyValidator.addValidator('pass', function (input, data_val) {
			var input_copy = input;
			var input_count = input.length;
			var upper_count = input_copy.replace(/[^A-Z]/g, "").length;
			var digit_count = input_copy.replace(/[^0-9]/g, "").length;
				
			if($pass_length > 0){

				if(input_count < parseInt($pass_length) && parseInt($pass_length) != 0)
					return false;

				if(upper_count < parseInt($upper_length) && parseInt($upper_length) != 0)
					return false;

				if(digit_count < parseInt($digit_length) && parseInt($digit_length) != 0)
					return false;
				
			}
			
			return true;
			
		}).addMessage('en', 'pass', $pass_err);*/
	}	
	
	var save = function()
	{
		$('#form_profile_account').parsley();
		$("#submit_profile_account").on("click", function(){

			  if ( $("#form_profile_account").parsley().isValid() ) {
				  var data = $("#form_profile_account").serialize();
				  
				  button_loader('submit_profile_account', 1);
				  
				  $.post($base_url + $module + "/profile/process", data, function(result) {
					  notification_msg(result.status, result.msg);
					  button_loader('submit_profile_account', 0);
					  
					  if(result.status == "success")
					  {
						  $("#top_bar_account_name").text(result.name);

						  if( result.image != "" )
						  {

					  		if( CONSTANTS.CHECK_CUSTOM_UPLOAD_PATH )
							{
								var avatar = output_image(result.image, $path_uploads);
							}
							else
							{
								var avatar = $base_url + $path_uploads + result.image;
							}
						 }
						 else
						 {
						 	var avatar 	= $base_url + $path_images + "avatar.jpg";
						 }
						  
						  $("#top_bar_avatar").attr("src", avatar);
					  }
						
				  }, 'json');
			  }
		});
	}
	
	var savePassword = function()
	{
		$('#form_profile_pass').parsley();
		$("#submit_profile_pass").on("click", function(){

			  if ( $("#form_profile_pass").parsley().isValid() ) {
				  var data = $("#form_profile_pass").serialize();
				  
				  button_loader('submit_profile_pass', 1);
				  
				  $.post($base_url + $module + "/profile/process/1", data, function(result) {
					  notification_msg(result.status, result.msg);
					  button_loader('submit_profile_pass', 0);
					  
					  if(result.status == "success")
					  {
						  $('#current_password').val("").removeClass('loading error-loading success-loading');
						  $('#new_password, #confirm_password').prop('disabled',true);
						  $("#new_password, #confirm_password").val("").attr("data-parsley-required", "false");
					  }
						
				  }, 'json');
			  }
		});
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
		initModal : function($pass_length, $upper_length, $digit_length, $pass_err)
		{
			initModal($pass_length, $upper_length, $digit_length, $pass_err);
		},
		save : function()
		{
			save();
		},
		savePassword : function()
		{
			savePassword();
		}
	}
}();