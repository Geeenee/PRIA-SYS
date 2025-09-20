var Dr = function(){
	var $transactions	= "transactions";
	var $module			= "dr";
	let cbCheck 		= {};
	
	var init = function(){
		$('#tbl_drs').on('draw.dt', function(){
			cbCheck	= {};
			for(let a of document.querySelectorAll('.ind_checkbox_dr'))
			{
				if(document.getElementById('check_all_dr').checked == true)
				{
					a.checked = true;
					cbCheck[a.value] = a.value;
				}
			}	
		});


		$('#tbl_drs').on('click', '.ind_checkbox_dr', (ev) => {
			const checked  = ev.target.checked;
			const checkBox = ev.target;

			if(cbCheck[checkBox.value])
				 delete cbCheck[checkBox.value];
			else
				cbCheck[checkBox.value] = checkBox.value;
		});

		/*$('#check_all_dr').on('click', (ev) =>{
			if( ! ev.target.checked)
				cbCheck = {};
		});*/
	};

	var save = function()
	{
		$("#submit_modal_cancel_dr").off('click').on("click", function(e){
			e.preventDefault();

			if($('#form_modal_cancel_dr').parsley().validate())
			{
				var data	= $("#form_modal_cancel_dr").serialize();

				button_loader('submit_modal_cancel_dr', 1);

				$.post($base_url + $transactions + "/" + $module + "/dr/process", data, function(result){
					
					if(result.flag == '1'){
						notification_msg(result.status, result.msg);

					}else{
						notification_msg(result.status, result.msg);
					}

					$("#modal_cancel_dr").modal("close");
					button_loader('submit_modal_cancel_dr', 0);

					window.location.reload(true);
				
			  	}, 'json');
			}
		});
	}

	var dr_approval = function()
	{
		$("#submit_modal_approval").off('click').on("click", function(e){
			e.preventDefault();

			if($('#form_modal_approval').parsley().validate())
			{
				var data	= $("#form_modal_approval").serialize();
				
				button_loader('submit_modal_approval', 1);

				$.post($base_url + $transactions + "/" + $module + "/dr/approve_dr_process", data, function(result){
					
					if(result.flag == '1'){
						notification_msg(result.status, result.msg);

					}else{
						notification_msg(result.status, result.msg);
					}

					$("#modal_approval").modal("close");
					button_loader('submit_modal_approval', 0);
					
					window.location.reload(true);
					
			  	}, 'json');
			}
		});
	}

	var dr_disapproval = function()
	{
		$("#submit_modal_disapproval").off('click').on("click", function(e){
			e.preventDefault();

			if($('#form_modal_disapproval').parsley().validate())
			{
				var data	= $("#form_modal_disapproval").serialize();

				button_loader('submit_modal_disapproval', 1);

				$.post($base_url + $transactions + "/" + $module + "/dr/disapprove_dr_process", data, function(result){
					
					if(result.flag == '1'){
						notification_msg(result.status, result.msg);

					}else{
						notification_msg(result.status, result.msg);
					}

					$("#modal_disapproval").modal("close");
					button_loader('submit_modal_disapproval', 0);

					window.location.reload(true);
				
			  	}, 'json');
			}
		});
	}
	
	var selectAll = function(index)
	{
		cbCheck	= {};
		$('input[class=ind_checkbox_'+ index +']:checkbox').each(function(){
			if($('input[class=check_all_'+ index +']:checkbox:checked').length == 0){ 
				$(this).prop("checked", false);
			} else {
				$(this).prop("checked", true);
				cbCheck[$(this).val()] = $(this).val();
			} 
		});
	}

	var cancel_dr = function(index)
	{
		var data = {
			checkboxes : cbCheck,
			checkAll   : false, //document.getElementById('check_all_dr').checked
		};

		if(document.getElementById('check_all_dr').checked == true || Object.keys(cbCheck).length !== 0)
		{
			$.post($base_url + $transactions + "/" + $module + "/dr/get_checked_drs", data, function(result){
				$('#trigger_btn').attr("data-modal_post", JSON.stringify(result));
				$('#trigger_btn').attr('onClick', `modal_cancel_dr_init('', this, 'Request Cancellation');`);
				$("#trigger_btn").click();
			}, 'json');
		}
		else
		{
			notification_msg($.CONSTANTS.ERROR, 'Please select a DR first');
		}
	}


	var add_soa = function(index)
	{
		var data = {
			checkboxes : cbCheck,
			checkAll   : false, //document.getElementById('check_all_dr').checked,
			same 	   : true,
		};

		if(document.getElementById('check_all_dr').checked == true || Object.keys(cbCheck).length !== 0)
		{
			$.post($base_url + $transactions + "/" + $module + "/dr/get_checked_drs", data, function(result){
				
				if(result.flag == $.CONSTANTS.SUCCESS)
				{
					$('#trigger_soa_btn').attr("data-modal_post", JSON.stringify(result));
					$('#trigger_soa_btn').attr('onClick', `modal_add_soa_init('${$.CONSTANTS.MODULE_TAB_FORWARDERS_DR}', this, 'Add SOA');`);
					$("#trigger_soa_btn").click();
				}
				else
				{
					notification_msg($.CONSTANTS.ERROR, result.msg);
				}
			}, 'json');
		}
		else
		{
			notification_msg($.CONSTANTS.ERROR, 'Please select a DR first');
		}
	}

	var dr_location = function()
	{
		
		$("#support_center").off('change').on('change', function(e)
		{
			e.preventDefault();

			var data	= $("#form-task").serialize();

			$('#location')[0].selectize.clear();
			$('#location')[0].selectize.clearOptions();

			$('#dr_document_recipient')[0].selectize.clear();
			$('#dr_document_recipient')[0].selectize.clearOptions();

			$.post($base_url + $transactions + "/" + $module + "/encode_delivery_receipt/get_locations", data, function(response)
			{
				const elem_select = $('#location')[0].selectize;
				elem_select.clearOptions();
				elem_select.addOption(response);

				if (response.length == 1) {
					elem_select.setValue(response[0].value);
				}
		  	}, 'json');

			$.post($base_url + $transactions + "/" + $module + "/encode_delivery_receipt/get_dr_recipients", data, function(response)
			{
				const elem_select = $('#dr_document_recipient')[0].selectize;
				elem_select.clearOptions();
				elem_select.addOption(response);

				if (response.length == 1) {
					elem_select.setValue(response[0].value);
				}
		  	}, 'json');
		});
	}

	return {
		save: function()
		{
			save();
		},
		dr_approval: function()
		{
			dr_approval();
		},
		dr_disapproval: function()
		{
			dr_disapproval();
		},
		selectAll: function(index)
		{
			selectAll(index);
		},
		cancel_dr: function(index)
		{
			cancel_dr(index);
		},
		add_soa: function()
		{
			add_soa();
		},
		dr_location: function()
		{
			dr_location();
		},
		init
	}
}();