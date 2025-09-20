var Pr = function() {
	
	var $transactions	 = "transactions";
	var $module			 = "pr";
	
	var acctGroupHandler = function(value){
		const purGroupElem  = document.getElementById('purchasing_group');
		const purGroupOpts  = purGroupElem.selectize.options;
		let   purGroupValue = 0;

		for(let i in purGroupOpts)
		{
			if(purGroupOpts[i].agCode == value)
				purGroupValue = purGroupOpts[i].value;
		}

		purGroupElem.selectize.setValue(purGroupValue);
	};

	var load_cc = function(data)
	{
		$('#cost_center')[0].selectize.clear();
		$('#cost_center')[0].selectize.clearOptions();

		let elem_select_cc = $('#cost_center')[0].selectize;
		elem_select_cc.clearOptions();
		elem_select_cc.addOption(data);

		if (data.length == 1) {
			elem_select_cc.setValue(data[0].value);
		}
	};

	var boq_proj_loader	= function ()
	{
		$('#additional_flag').off('change').on('change', (e) =>
		{
			if($('#additional_flag').val() != '' && $('#additional_flag').val() != '0')
			{
				$("#project_ref").attr('data-parsley-required', 'true');
				$("label[for='project_ref']").addClass('required');
			}
			else
			{
				$("#project_ref").attr('data-parsley-required', 'false');
				$("label[for='project_ref']").removeClass('required');
			}
		});

		$('#business_center').off('change').on('change', (e) =>
		{
			var data = {org_code : $('#business_center').val()};

			$('#boq_ref')[0].selectize.clear();
			$('#boq_ref')[0].selectize.clearOptions();

			$('#project_ref')[0].selectize.clear();
			$('#project_ref')[0].selectize.clearOptions();
			
			$.post($base_url + $transactions + "/" + $module + "/pr/get_bc_boq", data, function(result)
			{
				const elem_select = $('#boq_ref')[0].selectize;
				elem_select.clearOptions();
				elem_select.addOption(result.data);

				if (result.data.length == 1) {
					elem_select.setValue(result.data[0].value);
				}
			}, 'json');

			$.post($base_url + $transactions + "/" + $module + "/pr/get_bc_proj", data, function(result)
			{
				const elem_select = $('#project_ref')[0].selectize;
				elem_select.clearOptions();
				elem_select.addOption(result.data);

				if (result.data.length == 1) {
					elem_select.setValue(result.data[0].value);
				}
			}, 'json');
		});


		$('#boq_ref').off('change').on('change', (e) =>
		{
			var data = {boq_id : $('#boq_ref').val()};

			if($('#boq_ref').val() != '')
			{
				$.post($base_url + $transactions + "/" + $module + "/pr/get_boq_details", data, function(result)
				{
					$('div#store').html('<b>' + result.data.official_store_name + '</b>');
				}, 'json');
			}
			else
			{
				$('div#store').html('<b>' + 'N/A' + '</b>');
			}
		});
	}

	var save = function(with_init)
	{	
		if(with_init)
		{
			const acctGroupElem = document.getElementById('account_group');
			
			acctGroupElem.selectize.off('change');
			acctGroupElem.selectize.on('change', acctGroupHandler);
		}

		$("#submit_modal_add_pr").off('click').on("click", function(e){
			e.preventDefault();

			if($('#form_modal_add_pr').parsley().validate())
			{
				var data	= $("#form_modal_add_pr").serialize();

				button_loader('submit_modal_add_pr', 1);

				$.post($base_url + $transactions + "/" + $module + "/pr/process", data, function(result)
				{
					notification_msg(result.status, result.msg);
					// start_loading();
					if(result.flag == '1')
					{
						$("#modal_add_pr").modal("close");

						$('a[href="#tab_purchase_requests"]').click();
						window.location.reload(true);					}

					button_loader('submit_modal_add_pr', 0);

					//window.location.reload(true);
					//end_loading();	

			  	}, 'json');
			}
		});
	}
	
	return {		
		save : function(with_init)
		{
			save(with_init);
		},		
		boq_proj_loader : function()
		{
			boq_proj_loader();
		},
		load_cc : function(data)
		{
			load_cc(data);
		}
	}
}();