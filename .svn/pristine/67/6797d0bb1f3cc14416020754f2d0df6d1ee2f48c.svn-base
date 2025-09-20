var Payment_status = function() {
	
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
			$('#payment_status_form').parsley();
			$('#payment_status_form').parsley().validate();
			
			if($('#payment_status_form').parsley().isValid())
			{
				var $date_to 			= 0;
				var $date_from 			= 0;
				var	$account_group 		= $("#account_group").val();
				var	$business_center 	= $("#business_center").val();
				var	$reference_a 		= $("#reference_a").val();
				var	$reference_b 		= $("#reference_b").val();

				var $reference_a_disp = $reference_a.replace(/_/g, " ");
				var $reference_b_disp = $reference_b.replace(/_/g, " ");

				$('#ref_a_no').text($reference_a_disp.toUpperCase());
				
				$reference_a_disp = $reference_a_disp + ' DATE';
				$('#ref_a_date').text($reference_a_disp.toUpperCase());

				$('#ref_b_no').text($reference_b_disp.toUpperCase());
				
				$reference_b_disp = $reference_b_disp + ' DATE';
				$('#ref_b_date').text($reference_b_disp.toUpperCase());

				//Setting up header names
				/*if($reference_a != '_ALL_' || $reference_a == '' ){
					var $reference_a = $reference_a.replace(/_/g, " ");
					$('#ref_a_no').text($reference_a.toUpperCase());
				}
				
				if($reference_a){
					$reference_a = $reference_a + ' DATE';
					$('#ref_a_date').text($reference_a.toUpperCase());	
				}

				if($reference_b != '_ALL_' || $reference_b == ''){
					var $reference_b = $reference_b.replace(/_/g, " ");
					$('#ref_b_no').text($reference_b.toUpperCase());
				}

				if($reference_b){
					$reference_b = $reference_b + ' DATE';
					$('#ref_b_date').text($reference_b.toUpperCase());	
				}*/
				//Ends

				var options = {
					'table_id' 			: 'tbl_payment_report',
					'path' 				: $module + "/Payment_status/get_payment_status_list/"+ $account_group + "/" + $business_center + "/" + $reference_a + "/" + $reference_b,
					'advanced_filter' 	: true,
					'hidden_column'		: 7,
					'buttons'		    : ['excel', 'colvis'],
					'export_title'	    : "Payment Status",
					'export_file_name'  : "Payment Status"

				}
				
				load_datatable(options);
			}
		}); 


		$('#account_group').off('change').on('change', function(){
			
			var data	= $("#payment_status_form").serialize();

			const reference_a_select = $('#reference_a')[0].selectize;
			const reference_b_select = $('#reference_b')[0].selectize;
			const business_center_select = $('#business_center')[0].selectize;

        	business_center_select.clear();
        	business_center_select.clearOptions();
			reference_a_select.clear();
        	reference_a_select.clearOptions();
        	reference_b_select.clear();
        	reference_b_select.clearOptions();

			$.post($base_url + $module + "/payment_status/get_reference_a", data, function(response)
			{
				business_center_select.load(function(callback){
					callback(response.business_centers);
					business_center_select.setValue(response.business_centers[0].value);
				});

				reference_a_select.load(function(callback){
					callback(response.reference_a);
					reference_a_select.setValue(response.reference_a[0].value);
				});

				reference_b_select.load(function(callback){
					callback(response.reference_b);
					reference_b_select.setValue(response.reference_b[0].value);
				});

				/*var len1 = response.reference_a['value'].length;
				var len2 = response.reference_b['value'].length;

                for( var i = 0; i<len1; i++){

                    var id1 = response.reference_a['value'][i];
                    var name1 = response.reference_a['text'][i];

                    var $select1 	= $('#reference_a').selectize();
					var selectize1 = $select1[0].selectize;
					selectize1.addOption({value: id1, text: name1});
					selectize1.addItem(id1);
                }

                for( var i = 0; i<len2; i++){

                    var id2 = response.reference_b['value'][i];
                    var name2 = response.reference_b['text'][i];

                    var $select2 	= $('#reference_b').selectize();
					var selectize2 = $select2[0].selectize;
					selectize2.addOption({value: id2, text: name2});
					selectize2.addItem(id2);
                }*/

		  	}, 'json');
		});
	}

	return {
		
		initialize : function(options)
		{
			loadTable(options);

			searchFilter(options);
		}
	}
}();