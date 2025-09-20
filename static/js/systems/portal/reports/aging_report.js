var Aging_report = function() {
	
var $module		= "reports";
	
	var loadTable = function(options)
	{
		$("#refresh_btn").off("click").on("click", function()
		{
			$('#account_group')[0].selectize.setValue('_ALL_');			
			var opt = JSON.parse(options);
			load_datatable(opt);
		});
	}

	var searchFilter = function(options)
	{
		$("#search").off("click").on("click", function()
		{
			$('#aging_report_form').parsley();
			$('#aging_report_form').parsley().validate();
			
			if($('#aging_report_form').parsley().isValid())
			{
				var $date_to 			= 0;
				var $date_from 			= 0;
				var	$account_group 		= $("#account_group").val();
				var	$business_center 	= $("#business_center").val();

				if ($('#date_to').val() != ''){
					var	$date_to = $("#date_to").val();
					var $date_to = $date_to.replace(/\//g, "-");
				}

				if ($('#date_from').val() != ''){
					var	$date_from = $("#date_from").val();
					var $date_from = $date_from.replace(/\//g, "-");
				}
				
				var $apv_status 		= $("#apv_status").val();

				var options = {
					'table_id' 			: 'tbl_aging_report',
					'path' 				: $module + "/aging_report/get_aging_report_list/"+ $account_group + "/" + $business_center + "/" + $date_from + "/" + $date_to + "/" + $apv_status,
					'advanced_filter' 	: true,
					'with_search'		: true,
			        'buttons'       	: ['excel', 'colvis'],
			        'export_title'      : "Aging Report",
			        'export_file_name'  : "Aging Report"
				}
				
				load_datatable(options);
			}
		}); 

		$('#account_group').off('change').on('change', function(){
			
			var data	= $("#aging_report_form").serialize();

			const business_center_select = $('#business_center')[0].selectize;

        	business_center_select.clear();
        	business_center_select.clearOptions();

			$.post($base_url + $module + "/aging_report/get_business_centers", data, function(response)
			{
				business_center_select.load(function(callback){
					callback(response.business_centers);
					business_center_select.setValue(response.business_centers[0].value);
				});
		  	}, 'json');
		});
	}

	return {
		
		initialize : function(options)
		{
			loadTable(options);

			// getBusinessCenter();

			searchFilter(options);

		}
	}
}();