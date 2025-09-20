var Terms 	= function()
{
	var init 	= function()
	{
		$("#terms_checkbox").on("click", function(e){
			if( $(this).is(":checked") )
			{
				$("#terms_btn").removeAttr("style");
			}
			else
			{
				$("#terms_btn").attr("style", "display: none !important;");
			}	
		});
	}

	var proceed 	= function()
	{
		$("#terms_btn").on("click", function( e )
		{
			var data 	= {};
			
			e.stopImmediatePropagation();

			if( $("#terms_checkbox").is(":checked") )
			{
				data["agreement"]	= 1;
			}
			else
			{
				data["agreement"]	= 0;
			}

			data['sign_up_check']	= $('#sign_up_check').val();
			data["username"] 		= $("#icon_username").val();
			data["password"] 		= $("#icon_password").val();

		/*	$( "body" ).isLoading({
	        	text:       "<div class='loader'></div>", 
	        	position:   "overlay"
	  		});*/
	  		start_loading();

			$.post($base_url + "auth/update_user_agreement", data ).promise().done(function( response ){

				response = JSON.parse( response );

				if(response.flag == 0)
				{	
					$(".notify.error p").html(response.msg);
					$(".notify.error").notifyModal({
						duration : -1
					});
				}
				else
				{
					if( response.sign_up )
					{
						$('#modal_term_condition').modal('close');
						$('#modal_sign_up_link').click();
					}
					else
					{
						var $home_page 	= $("#home_page").val();

						if( response.redirect_page != '' )
						{
							window.location = $base_url + response.redirect_page;
						}
						else
						{
							window.location = $base_url;
						}		
					}
				}
				
				// $("body").isLoading("hide");		  	
				end_loading();
			});

		});
	}

	return {
		init : function()
		{
			init();
		},
		proceed : function()
		{
			proceed();
		}
	};

}();