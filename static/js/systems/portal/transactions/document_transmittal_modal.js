var DocumentTransmittal = function() {
	var $transactions	= "transactions";
	var $module			= "doc_transmittal";

	var save = function(e)
	{	
		console.log($base_url + $transactions + "/" + $module + "/document_transmittal_modal/process");
		$("#submit_modal_add_document_transmittal").off('click').on("click", function(e){
			e.preventDefault();

			if($('#form_modal_add_document_transmittal').parsley().validate())
			{
				var data	= $("#form_modal_add_document_transmittal").serialize();

				button_loader('submit_modal_add_document_transmittal', 1);

				$.post($base_url + $transactions + "/" + $module + "/document_transmittal_modal/process", data, function(result){
					notification_msg(result.status, result.msg);

					if(result.flag == '1')
					{
						$("#modal_add_document_transmittal").modal("close");

						$('a[href="#tab_document_transmittal"]').trigger('click');
					}

					button_loader('submit_modal_add_document_transmittal', 0);
				
			  	}, 'json');
			}
		});
		
		if($('#business_center').length > 0)
		{
			document.getElementById('business_center').selectize.on('change', function(val)
			{
				const data	  = $("#form_modal_add_document_transmittal").serialize();

				$('#vendor')[0].selectize.clear();
				$('#vendor')[0].selectize.clearOptions();
				
				const options = {
					blockUI    : true,
					body 	   : data,
					path       : $base_url + $transactions + "/" + $module + "/document_transmittal_modal/get_vendors",
					successFunc: function(response){
						var len 		= response.length;
						var $select 	= $('#vendor').selectize();
						var selectize 	= $select[0].selectize;

						for( var i = 0; i<len; i++){
							var id 			= response[i]['value'];
							var name 		= response[i]['text'];

							selectize.addOption({value: id, text: name});

							if(len == 1 && i == 0)
								selectize.addItem(id);
						}
					}	
				};

				General.Fetch(options);

				// var options1 = {
				// 	blockUI    : true,
				// 	body 	   : $.param({'org_code' : val}),
				// 	path       : $base_url + $transactions + "/" + $module + "/document_transmittal/get_recipients",
				// 	successFunc: function(response){
				// 		var len 		= response.length;
				// 		var $select 	= $('#document_transmittal_document_recipient').selectize();
				// 		var selectize 	= $select[0].selectize;

				// 		for( var i = 0; i<len; i++){
				// 			var id 			= response[i]['value'];
				// 			var name 		= response[i]['text'];

				// 			selectize.addOption({value: id, text: name});

				// 			if(len == 1 && i == 0)
				// 				selectize.addItem(id);
				// 		}
				// 	}	
				// };

				// General.Fetch(options1);
			});
		}

		/* $("#vendor").on('change', function(e){
			e.preventDefault();

			var data	= $("#form_modal_add_soa").serialize();

            $('#business_center')[0].selectize.clear();
            $('#business_center')[0].selectize.clearOptions();

			$.post($base_url + $transactions + "/" + $module + "/soa/get_business_center", data, function(response){
					
				var len = response.length;
				
                for( var i = 0; i<len; i++){
                    var id = response[i]['value'];
                    var name = response[i]['text'];

                    var $select 	= $('#business_center').selectize();
					var selectize = $select[0].selectize;
					selectize.addOption({value: id, text: name});
					selectize.addItem(id);

                }
		  	}, 'json');
		}); */

		//  $("#vendor").off('change').on('change', function(e)
		//  {
		//  	if($('#delivery_receipt_number').length > 0)
		//  	{
		// 		$('#delivery_receipt_number')[0].selectize.clear();
		// 		$('#delivery_receipt_number')[0].selectize.clearOptions();
				
		// 		var options1 = {
		// 			blockUI    : true,
		// 			body 	   : $.param({'org_code' : $('#business_center').val(), 'vendor_code' : $(this).val(), 'ag_code' : $('#ag_code').val()}),
		// 			path       : $base_url + $transactions + "/" + $module + "/soa/get_drs",
		// 			successFunc: function(response){
		// 				var len 		= response.length;
		// 				var $select 	= $('#delivery_receipt_number').selectize();
		// 				var selectize 	= $select[0].selectize;

		// 				for( var i = 0; i<len; i++){
		// 					var id 			= response[i]['value'];
		// 					var name 		= response[i]['text'];

		// 					selectize.addOption({value: id, text: name});

		// 					if(len == 1 && i == 0)
		// 						selectize.addItem(id);
		// 				}
		// 			}	
		// 		};

		// 		General.Fetch(options1);
		//  	}

		 	// if($('#po_reference_number').length > 0)
		 	// {
			// 	$('#po_reference_number')[0].selectize.clear();
			// 	$('#po_reference_number')[0].selectize.clearOptions();
				
			// 	var options1 = {
			// 		blockUI    : true,
			// 		body 	   : $.param({'org_code' : $('#business_center').val(), 'vendor_code' : $(this).val(), 'ag_code' : $('#ag_code').val(), 'tab_module' : $('#tab_module').val()}),
			// 		path       : $base_url + $transactions + "/" + $module + "/soa/get_pos",
			// 		successFunc: function(response){
			// 			var len 		= response.length;
			// 			var $select 	= $('#po_reference_number').selectize();
			// 			var selectize 	= $select[0].selectize;

			// 			for( var i = 0; i<len; i++){
			// 				var id 			= response[i]['value'];
			// 				var name 		= response[i]['text'];

			// 				selectize.addOption({value: id, text: name});

			// 				if(len == 1 && i == 0)
			// 					selectize.addItem(id);
			// 			}
			// 		}	
			// 	};

			// 	General.Fetch(options1);
		 	// }
		// }); 
	}


	return {		
		save : function()
		{
			save();
		}
	}
}();