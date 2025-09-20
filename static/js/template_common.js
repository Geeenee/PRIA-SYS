var Template_common 	= function()
{
	var interval_id  = 0;

	var $noti_circle = $('#noti_red');
	var $noti_count  = $('#notif_cnt');

	var $dropdown    = $('.notif');
	var request_check 	= false;

	// $dropdown.on('click', function()
	// {
	// 	if( $(this).hasClass('selected') )
	// 	{
	// 		if( typeof( Socket_notification ) !== 'undefined' )
	// 		{
	// 			Socket_notification.refreshNotiTime();
	// 			Socket_notification.updateNotification();

	// 			interval_id = setInterval(Socket_notification.refreshNotiTime, 3000);
	// 		}

	// 		infinite_scroll();
	// 	}
	// 	else
	// 	{
	// 		clearInterval(interval_id);
			
	// 		$noti_circle.attr('style', 'display : none !important;');
	// 		$noti_count.hide();
	// 	}
	// });

	// $(document).on('click', function(event)
	// {
	// 	if( !$(event.target).is('.has-children a') ) 
	// 	{
	// 		if( $dropdown.hasClass('selected') )
	// 		{
	// 			clearInterval(interval_id);
				
	// 			$noti_circle.attr('style', 'display : none !important;');
	// 			$noti_count.hide();
	// 		}
	// 	}
	// } );	

	var infinite_scroll 	= function()
	{
		var api_orig    =  $('.notification-title').next().find('.scroll-pane').data('jsp');

		if( api_orig !== undefined )
		{
			api_orig.destroy();
		}

		var container 	= $('.notification-title').next().find('.scroll-pane').jScrollPane({autoReinitialise: true, contentWidth: '0px'});

		var api = $('.notification-title').next().find('.scroll-pane').data('jsp');  

	    api.reinitialise();  

		yofinity_helper.reset_iterator();

		var options 		= {
			ajaxUrl : $base_url+'Notifications/get_notifications',
		 	buffer 	: $('.notification-title').next().find('.scroll-pane').height(),
		 	debug 	: false,
		 	navSelector: 'a[rel="next"]',
		 	moreButton : $('#more_button'),
		 	context  :  $('.notification-title').next().find('.scroll-pane').find('.collection-notif'),
		 	loading  : function()
			{

			},
			scroll 	: {
		 		plugin 		: 'jScrollPane',
		 		container	: container
		 		// top 		: true
		 	},
		 	type 	: 'post',
		 	success : function( response )
		  	{
		  		if( response )
	  			{
	  				response 	= JSON.parse( response );

	  				if( response.html != '' )
	  				{
	  					$('.notification-title').next().find('.scroll-pane').find('ul').append(response.html);
	  				}

	  				if( response.unread )   
	  				{
	  					var unread 	= parseInt($('#notif_cnt').text());

	  					if( !isNaN( unread ) )
	  					{
	  						$('#notif_cnt').text( unread + response.unread );
	  					}
	  				}
	  			}
		  	}
		};

		var yofinity = $('.notification-title').next().find('.scroll-pane').yofinity( options );
	}

	var modal_sess_expired_log_in 	= function()
	{
		var modal_obj = $('#modal_sess_expired_log_in').modal({
			dismissible: false,
			opacity: .5, // Opacity of modal background
			in_duration: 300, // Transition in duration
			out_duration: 200, // Transition out duration
			ready: function() {
				$("#modal_sess_expired_log_in .modal-content #content").load($base_url+'Unauthorized/session_expired_modal/');
			}, // Callback for Modal open
			complete: function() { 
			
			} // Callback for Modal close
		});

		return modal_obj;
	}


	var check_session 	= function()
	{
		if( request_check == true )
		{
			return;	
		}
		
		$.ajax({
			url:$base_url+'Unauthorized/check_session',
			beforeSend : function()
			{
				request_check = true;
			},
			success: function(session_expired)
			{
				var check_json 	= is_json( session_expired )

				request_check 	= false;

				if( check_json )
				{
					var json_obj = JSON.parse( session_expired );
					
					if( !json_obj.check )
					{
						if( !$('#modal_sess_expired_log_in').hasClass('open') 
							&& !$('#modal_warning_expired_log_in').hasClass('open')
						)
						{
							modal_sess_expired_log_in().trigger('openModal');
						}
					}
					else
					{
						modal_sess_expired_log_in().trigger('closeModal');
					}
				}
				else
				{
					if( !$('#modal_sess_expired_log_in').hasClass('open') 
						&& !$('#modal_warning_expired_log_in').hasClass('open')
					)
					{
						modal_sess_expired_log_in().trigger('openModal');
					}
				}
			}
		});
	}

	return {
		check_session : function()
		{
			check_session();
		}
	}
}();

$(function()
{
	if( typeof( check_session ) === 'undefined' || check_session )
	{
		setInterval(Socket_notification.check_session, 50000);
	}
});