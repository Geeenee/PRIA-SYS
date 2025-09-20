var Dashboard = function()
{
	var $controller		= "dashboard";
	var $module			= "dashboard";

	var init = function()
	{
		var ag_code =  $('#payment_ag').val();

		// $('#payment_empty').addClass('none');
		// $('#payment_content').removeClass('none');
		// load_index('payment_content', $controller + '/generate_dashboard_payment/'+ag_code, $module);
		$('#payment_panel').css('max-width', ($(window).width() - 76) + 'px');

		$('#payment_ag').off('change').on('change',function()
		{
			var ag		= $(this).val();

			if(ag != '')
			{
				$('#payment_empty').addClass('none');
				$('#payment_content').removeClass('none');
				load_index('payment_content', $controller + '/generate_dashboard_payment/'+ag, $module);
				$('#payment_panel').css('max-width', ($(window).width() - 76) + 'px');
			}
			else
			{
				$('#payment_empty').removeClass('none');
				$('#payment_content').addClass('none');
			}
		});
	}

	return {
		init : function()
		{
			init();
		},
		scroll_expand : function(options)
		{
			options.scrollCollapse = true;
			return options;
		}
	}
}();