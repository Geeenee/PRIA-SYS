var DocumentTransmittal = function() {
	var $transactions	= "transactions";
	var $module			= "doc_transmittal";

	var save = function(e)
	{	
		console.log($base_url + $transactions + "/" + $module + "/document_transmittal_modal/process");
		// $("#submit_modal_add_document_transmittal").off('click').on("click", function(e){
		// 	e.preventDefault();

			// if($('#form_modal_add_document_transmittal').parsley().validate())
			// {
				// var data	= $("#form_modal_add_document_transmittal").serialize();

				// button_loader('submit_modal_add_document_transmittal', 1);

				// $.post($base_url + $transactions + "/" + $module + "/document_transmittal_modal/process", data, function(result){
				// 	notification_msg(result.status, result.msg);

				// 	if(result.flag == '1')
				// 	{
				// 		$("#modal_add_document_transmittal").modal("close");

				// 		$('a[href="#tab_transmittal"]').trigger('click');
				// 	}

				// 	button_loader('submit_modal_add_document_transmittal', 0);
				
			  	// }, 'json');
			// }
		// });
		
		if($('#business_center').length > 0)
		{
			document.getElementById('business_center').selectize.on('change', function(val)
			{
				// const data	  = $("#form_modal_add_document_transmittal").serialize();
				
				$('#vendor')[0].selectize.clear();
				$('#vendor')[0].selectize.clearOptions();

				if(!val) return;
				const options = {
					blockUI    : true,
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					body: new URLSearchParams({
						business_center: val ? val : NULL,
						tab_module: document.getElementById('tab_module').value,
						ag_code: document.getElementById('ag_code').value
					}).toString(),
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
			});
		} 
	}


	return {		
		save : function()
		{
			save();
		}
	}
}();