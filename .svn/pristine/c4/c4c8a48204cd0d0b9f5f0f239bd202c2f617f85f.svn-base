var ForgotPw = function()
{
	
	var initResetModal = function(validate_pass_length, pass_length, upper_length, digit_length, pass_err)
	{
		/*window.ParsleyValidator.addValidator('pass', 
			function (input, data_val) {
			var input_copy = input;
			var input_count = input.length;
			var upper_count = input_copy.replace(/[^A-Z]/g, "").length;
			var digit_count = input_copy.replace(/[^0-9]/g, "").length;
			if(validate_pass_length)
			{
				if(input_count < parseInt(pass_length) || upper_count < parseInt(upper_length) || digit_count < parseInt(digit_length)){
					return false;
				}
				return true;
			}
		}).addMessage('en', 'pass', pass_err);*/
	}
	
	var save = function()
	{
		$('#forgot_password_form').parsley();
		
		$('#forgot_password_form').off("submit.forgot_password").on("submit.forgot_password", function(e) {
			e.preventDefault();
			
			if ( $(this).parsley().isValid() ) {
			  var data = $(this).serialize();
				  
			  button_loader('forgot_password_btn', 1);
			  $.post($base_url + "forgot_password/request_reset/", data, function(result) {
				
				notification_msg(result.status, result.msg);
				button_loader('forgot_password_btn', 0);				
				
				if(result.status == "success"){
				  $("#modal_forgot_pw").modal("close");
				}
				
			  }, 'json');       
			}
		});
	}
	
	var saveReset = function()
	{
		$('#form_modal_reset_pw').parsley();
		
		$('#form_modal_reset_pw').off("submit.reset_password").on("submit.reset_password", function(e) {
			e.preventDefault();
			
			if ( $(this).parsley().isValid() ) {
			  var data = $(this).serialize();
				  
			  button_loader('reset_password_btn', 1);
			  $.post($base_url + "forgot_password/update/", data, function(result) {
				
				notification_msg(result.status, result.msg);
				button_loader('reset_password_btn', 0);				
				
				if(result.status == "success"){
					setTimeout(function(){ window.location = $base_url; }, 5000);
				}
				
			  }, 'json');       
			}
		});
	}
	
	var saveUser = function()
	{
		$('#form_modal_verify_account').parsley();
		
		$('#form_modal_verify_account').off("submit").on("submit", function(e) {
			e.preventDefault();
			
			if ( $(this).parsley().isValid() ) {
			  var data = $(this).serialize();
				  
			  button_loader('continue_btn', 1);
			  $.post($base_url + "auth/update_user_account/", data, function(result) {
				button_loader('continue_btn', 0);				
				
				if(result.status == "success"){
					var data = $('#form_modal_verify_account').serialize();
					$.post($base_url + "auth/sign_in/", data, function(x) {
					
						if(x.flag == 0){			
							button_loader('submit_login', 0);
							
						} else {
							if( x.redirect_page !== undefined && x.redirect_page != '' )
							{
								window.location = $base_url + x.redirect_page;
							}
							else
							{
								window.location = $base_url;
							}
						}
					}, 'json');
				}else{
					notification_msg(result.status, result.msg);
				}
				
			  }, 'json');       
			}
		});
	}

	return {
		initResetModal : function(validate_pass_length, pass_length, upper_length, digit_length, pass_err)
		{
			initResetModal(validate_pass_length, pass_length, upper_length, digit_length, pass_err);
		},
		save : function()
		{
			save();
		},
		saveReset : function()
		{
			saveReset();
		},
		saveUser : function()
		{
			saveUser();
		}
	}
}();