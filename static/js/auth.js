$(function(){
	var $base_url = $("#base_url").val();
		$home_page = $("#home_page").val();
		$login = $("#login_form");
		$logout = $("#logout"),
		submit_btn 	= $('#submit_login,#login_form');		

	// $login.submit(function(event){
	submit_btn.on('click keypress', function(event){
		var data = $("#login_form").serialize();
		// event.preventDefault();
	
		var subm = false;

		if( $(this).attr('id') != 'submit_login' )
		{
			if( event.keyCode == 13 )
			{
				subm = true;
			}
		}
		else
		{
			if( event.type == 'click' )
			{
				subm = true;
			}
		}

		if( subm )
		{
			button_loader('submit_login', 1);

			var redirect = getParameterByName('redirect', window.location.href);
			if(redirect)
				data += '&redirect=' + redirect;

			$.post($base_url + "auth/sign_in/", data, function(result) {
				
				if(result.flag == 0){			
					button_loader('submit_login', 0);
					$(".notify.error p").html(result.msg);
					$(".notify.error").notifyModal({
						duration : -1
					});
				} else {

					if( result.check_has_agreement_text == 1 && result.user_agreed == 0 )
					{
						var modal_obj = $('#modal_term_condition').modal({
							dismissible: false, // Modal can be dismissed by clicking outside of the modal
							opacity: .5, // Opacity of modal background
							in_duration: 300, // Transition in duration
							out_duration: 200, // Transition out duration
							ready: function() {
								$("#modal_term_condition .modal-content #content").load($base_url + 'auth/modal_term_condition/');
								$( "body" ).find(".modal").first().before("<div class='triggered-modal'></div>")
								$('.triggered-modal').css({
									'position'	: 'fixed',
									'width' 	: '100%',
									'height' 	: '100%',
									'top' 		: '0',
									'left' 		: '0',
									'z-index' 	: '1000',
									'opacity' 	: '0',
									'background' : 'rgba(0,0,0,0.8) !important',
									'-webkit-transition' : 'all 0.3s',
									'-moz-transition' : 'all 0.3s',
									'transition' : 'all 0.3s'
								});
								$( "body" ).removeAttr('style');
							}, // Callback for Modal open
							complete: function() { 
						  		$( "body" ).find('.triggered-modal').remove();
							} // Callback for Modal close


						});

						modal_obj.trigger('openModal');

						button_loader('submit_login', 0);
					}
					else
					{
						
						if( result.initial_flag == 0 ) 
						{
							if( result.redirect_page !== undefined )
							{
								window.location = $base_url + result.redirect_page;
							}
							else
							{
								window.location = $base_url + $home_page;
							}					
						}
						else 
						{
							window.location = $base_url + "reset_password/initial_logged_in/"+result.username+"/"+result.salt+"/"+result.initial_flag;	
						}
					}
				}			  						
			}, 'json');	
		}
	});
	
	$logout.on("click", function(){
		$.post($base_url + "auth/sign_out/", function(result) {
			if(result.flag == 0){
				Materialize.toast(result.msg, 3000);
			}
			else
			{
				window.location = $base_url;
			}	
		}, 'json');
	});
	

	function getParameterByName(name, url) {
		if (!url) url = window.location.href;
		name = name.replace(/[\[\]]/g, '\\$&');
		var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}
});