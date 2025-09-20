var Tasks = (function($, document) 
{
	var $tasks			= "tasks/";
	var $module_task	= "task";
	
	var toggleFilter = function(id)
	{
		$( "#filter_task" ).click(function() {
			$("#" + id).toggleClass("none");
		  
			if ( $("#" + id).is( ".none" ) ) {
				var display = 0;
				$("#filter_task").removeClass("active");
			} else {
				var display = 1;
				$("#filter_task").addClass("active");
			}
			
			var data = "filter_display=" + display;
			
			$.post($base_url + $tasks + $module_task + "/set_filter_session/", data, function()
			{
					
			});
		});
	}

	var init = function(){
		$('.list-toggle').on('click', 'li', function(){

			if(this.classList.contains('active'))
			{
				const id 	  = $(this).data('id');
				const ag 	  = $(this).data('ag');
				const options = {
					method : 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body   : $.param({id:id, ag:ag}),
					blockUI:true
				};
				
				fetch($base_url + 'tasks/task/display_task_list', options)
				.then(General.getResponseFetch)
				.then(response => {

				})
				.catch(General.catchFetch);
			}
		});
	}

	return {
		init,
		toggleFilter : function(id)
		{
			toggleFilter(id);
		}
	}
}(jQuery, document));