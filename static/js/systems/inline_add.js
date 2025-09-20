var Inline_add 	= function()
{
	var init 	= function(post_url)
	{
		$(".add-subtask").on("click", function(e){
			var elem  = $(this).closest('li');
			var data = '';
			$(this).hide();
			start_loading();

			$.post(post_url, data, function( response )
			{
				elem.before('<li>'+ response.html +'</li>');
				end_loading();
				$(".sub-tabs > li:first a").trigger("click");
			},'json');
		});
		$(".close-subtask").on("click", function(e){
			var elem  		= $(this).closest('li'),
				add_elem  	= $(this).closest('.add-subtask');
			
			elem.prev().remove();
			elem.find('.add-subtask').show();
		});
	}

	return {
		init : function(post_url)
		{
			init(post_url);
		}
	};

}();