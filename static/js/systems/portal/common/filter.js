var Filter = function()
{
	var $common		= "common";
	var $module		= "filter";

	var init		= function()
	{
		//var searchParams = new URLSearchParams(window.location.search);
		$('#filter_form').find('.filter-input').bind('keyup', function(e)
		{
			e.stopImmediatePropagation();

			var val	= $(this).val();

			if(e.keyCode == 13)
			{
				$('#filter_btn').trigger('click');
			}
		});

		$('#reset_btn').off('click').on('click', function()
		{
			General.resetFields('filter_form');
			$('ul.tabs').find('a.active').trigger('click');
		});

		$('#filter_btn').off('click').on('click', function()
		{
			$('ul.tabs').find('a.active').trigger('click');
		});		
	}

	var filter_tab_reset = function()
	{
		/*if($('#filter_task').length < 1)
		{
		};*/
		General.resetFields('filter_form');
		$('#task_filter').addClass('none');

		jsGoTo('', '', window.location.pathname+window.location.hash);
	}

	return {
		init : function()
		{
			init();
		},
		filter_tab_reset : function()
		{
			filter_tab_reset();
		}
	}
}();