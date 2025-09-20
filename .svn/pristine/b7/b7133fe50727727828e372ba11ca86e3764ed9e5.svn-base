var SignUp = function()
{
	
	var initResetModal = function(validate_pass_length, pass_length, upper_length, digit_length, pass_err, repeat_pass)
	{
		password_constraints({
			pass_length : validate_pass_length,
			upper_length : upper_length,
			digit_length : digit_length,
			pass_err : pass_err,
			repeat_pass : repeat_pass,
			pass_same : 0
		});
	}
	
	var save = function()
	{
		$('#form_modal_sign_up').parsley();
		
		$('#form_modal_sign_up').off("submit.sign_up").on("submit.sign_up", function(e) {
			e.preventDefault();
			
			if ( $(this).parsley().isValid() ) {
			  var data = $(this).serialize();
				  
			  button_loader('create_account_btn', 1);
			  $.post($base_url + "sign_up/process/", data, function(result) {
				var opt = {
					size : 'large'
				};

				notification_msg(result.status, result.msg, false, opt);
				button_loader('create_account_btn', 0);				
				
				if(result.status == "success"){
				  $("#modal_sign_up").modal("close");
				}
				
			  }, 'json');       
			}
		});
	}

	return {
		initResetModal : function(validate_pass_length, pass_length, upper_length, digit_length, pass_err, repeat_pass)
		{
			initResetModal(validate_pass_length, pass_length, upper_length, digit_length, pass_err, repeat_pass);
		},
		save : function()
		{
			save();
		}
	}
}();