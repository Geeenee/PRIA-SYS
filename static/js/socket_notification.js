var Socket_notification 	= function()
{
	var $base_url 	= ci_details.ci_base_url;
	var $user_id 	= ci_details.ci_user_id;
	var $org_code 	= ci_details.ci_org_code;
	var $user_roles = ci_details.ci_user_roles;
	var $host 		= ci_details.ci_nodejs_server;
	var n_cnt 		= ci_details.notif_cnt;

	var address 	= $host + 'alerts';
	var notif_addr 	= $host + 'my_notifications';
	var details 	= {
	    resource: 'socket.io'
	};

	var $noti_circle = $('#noti_red');
	var $noti_count  = $('#notif_cnt');

	var $dropdown    = $('.notif');
	var interval_id  = 0;

	// var alerts 		= io.connect(address, details);
	var notifs 			= io.connect(notif_addr, {'secure': true});
	var request_check 	= false;

	notifs.on('connect', function()
	{
		notifs.emit('set_user_notif', {'user_id' : $user_id});
	});
	
	notifs.on('notify_users', function(data)
	{
		$noti_circle.show();
		toast_notification(data);
		append_notification(data);
	});

	notifs.on('notify_users_auto', function(data)
	{
		$noti_circle.show();
		toast_notification(data);
		append_notification(data);
	});

	notifs.on('check_user', function( data )
	{
		if(data.length !== 0)
		{
			$.post($base_url + "auth/sign_out/" + data.user_id, function(result){
				if(result.flag == 1){
					window.location = $base_url;
				}
			},'json');

		}
	});

	/*notifs.on('check_online', function(data)
	{
		console.log(data);
	});

	notifs.on('server_online', function(online)
	{
		if( !online )
		{
			modal_offline().trigger("openModal");
		}
		else
		{
			modal_offline().trigger("closeModal");
		}
		
	});*/

	notifs.on('inactive_user', function( data )
	{	
		if(data.changedRows !== 0)
		{
	 		$.post($base_url + "user_management/users/refresh_list", {user_id : $user_id}, function(result){
	 			$("#active_users_list").html(result);
	 			create_avatar($('.default-avatar'), {width:80,height:80,fontSize:30});
	 			$('.tooltipped').tooltip('remove');
	 			$('.tooltipped').tooltip({delay: 50});
		  	});
		}
	});

	notifs.on('logout', function( data )
	{
		if(data.length !== 0)
		{
			$.each( data, function(index, logout) 
			{
				var $user_ids = (logout.user_id)? logout.user_id.split(',') : [];

				if(($.inArray($user_id, $user_ids) !== -1))
				{
					$.post($base_url + "auth/sign_out/" + $user_id, function(result){
						if(result.flag == 1){
							window.location = $base_url;
						}
					},'json');
				}

			});

		}
	});

	var check_user 	= function()
	{
		notifs.emit('check_user');
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

	var logout 		= function()
	{
		notifs.emit('logout');
	/*	alerts.on('logout', function (data) 
		{
			if(data.logoutArray.length !== 0)
			{
				$.each(data.logoutArray, function(index, logout){
					var $user_ids = (logout.user_id)? logout.user_id.split(',') : [];
					
					if(($.inArray($user_id, $user_ids) !== -1)){
						$.post($base_url + "auth/sign_out/" + $user_id, function(result){
							if(result.flag == 1){
								window.location = $base_url;
							}
						},'json');
					}
					
		        });
			}
		});	*/
	}

	var inactive_user 	= function()
	{
		notifs.emit('inactive_user');
	}

	var append_notification = function( data )
	{
		var html 		= '';
		
		if( data.listed_flag == 'N' )
		{

			html 	+= '<li data-notif="'+data.notification_id+'" class="collection-item avatar" onclick="Socket_notification.read_notification(this);">';
			html 	+= data.notification;
			html 	+= '<span class="mute font-xs">'+moment(notification.date).fromNow()+'</span>';
			html 	+= '</li>';

			n_cnt+=1;	
			
			$('.collection-notif').prepend(html);
		}

		$noti_circle.show();
		$noti_count.html(n_cnt);
		$noti_count.show();

	}

	var toast_notification  = function(data)
	{
		var $toast_link = $('<div id="notif_id_'+data.notification_id+'" class="row m-n p-n"><div/>').html( data.notification );

		//var $toast_link = $('<div id="notif_id_'+data.notification_id+'" class="row m-n p-n"><div/>').html('<a href="'+ data.notification_html +'">' + data.notification + '</a>');

		// var $toast_link 	=  $('<div id="notif_id_'+data.notification_id+'" class="row m-n p-n"><div/>').html("<i class='material-icons circle blue darken-2'>add_alert</i><p><a class='' style='color:white !important; cursor: pointer !important;' data-timestamp='2017-12-05 10:46:38' href='#'  data-modal_post=''>News and Announcements : Non Working Holiday</a></p>");

		$('body').append($toast_link);
		
		var parent_div 	= $('#notif_id_'+data.notification_id+'');

		var icon 		= parent_div.find('i');
		var link 		= parent_div.find('p a').unwrap();

		icon.wrap('<div class="col s2 p-n m-n"></div>');
		link.wrap('<div class="col s10 font-sm p-xs p-t-n"></div>');

		parent_div.parents('div#toast-container').attr('style', 'width: 400px !important;');
		
		Materialize.toast($toast_link, 100000, 'notification grey lighten-4', function()
		{
			$('body').find(parent_div).remove();
		});
		
		parent_div.parents('div#toast-container').attr('style', 'width: 400px !important;');
	}

	var updateNotification = function()
	{
		var auto_user 	= {
			user_id 	: ci_details.ci_user_id,
			org_code 	: ci_details.ci_org_code,
			user_roles  : ci_details.ci_user_roles.split(','),
			datetime : moment().format('YYYY-MM-DD HH:MM:SS')
		};

		notifs.emit('updateNotification', auto_user);

		$.post( $base_url+'Notifications/update_notification', auto_user ).promise().done(function( response )
		{

		});
		
		n_cnt = 0;
	};

	var refreshNotiTime = function(){
		$('.collection-notif li').each(function(index){
			var anchor	  = $(this).find('a');
			var timestamp = moment(anchor.data('timestamp')).fromNow(); 
			
			$(this).find('p.timestamp').html(timestamp);
		});
	};

	$dropdown.on('click', function()
	{
		if( $(this).hasClass('selected') )
		{
			refreshNotiTime();
			updateNotification();
			interval_id = setInterval(refreshNotiTime, 3000);

			infinite_scroll();
		}
		else
		{
			clearInterval(interval_id);
			
			$noti_circle.attr('style', 'display : none !important;');
			$noti_count.hide();
		}
	});

	$(document).on('click', function(event)
	{
		if( !$(event.target).is('.has-children a') ) 
		{
			if( $dropdown.hasClass('selected') )
			{
				clearInterval(interval_id);
				
				$noti_circle.attr('style', 'display : none !important;');
				$noti_count.hide();
			}
		}
	} );

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
		  		if(response)
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
		  		/*if( response. != '' )
	  			{
	  				$('.notification-title').next().find('.scroll-pane').find('ul').append(response);
	  			}*/
		  	}
		};

		var yofinity = $('.notification-title').next().find('.scroll-pane').yofinity( options );
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

	var read_notification = function(obj)
	{
		var notif_id 	= $(obj).attr('data-notif');

		if( notif_id )
		{
			$.post( $base_url+'Notifications/update_click_notification', { notif_id : notif_id } ).promise().done( function( response ) {

				response = JSON.parse( response );

				if( response.flag )
				{
					$(obj).remove();
				}

			} );
		}
	}

	return {
		force_logout : function()
		{
			logout();
		},
		notifyUsers : function(notification_ids){
			notifs.emit('notify_users', notification_ids);
		},
		notify_user_auto : function(data)
		{
			notifs.emit('notify_users_auto', data);
		},
		check_online : function()
		{
			notifs.emit('check_online');
		},
		server_online : function()
		{
			notifs.emit('server_online');
		},
		check_session : function()
		{
			check_session();
		},
		inactive_user : function()
		{
			inactive_user();
		},
		check_user 	: function()
		{
			check_user();
		}
	}
}();

$(function()
{
	/*Push.create('Hello World', {
		body : "How's it hanging?",
		icon : $base_url + PATH_IMAGES + 'logo_orange.png',
		 onClick: function () {
           window.location = $base_url;
        }
	});*/

	var auto_user 	= {
		user_id 	: ci_details.ci_user_id,
		org_code 	: ci_details.ci_org_code,
		user_roles  : ci_details.ci_user_roles.split(',')
	};

	Socket_notification.force_logout();
	Socket_notification.notify_user_auto(auto_user);
	Socket_notification.check_online();
	Socket_notification.server_online();
	setInterval(Socket_notification.inactive_user, 7000);
	Socket_notification.check_user();

	// setInterval(Socket_notification.check_session, ci_details.ci_sess_expiration * 1000);
	// setInterval(Socket_notification.check_session, 10000);
	if( check_session )
	{
		setInterval(Socket_notification.check_session, 600000);
	}
});