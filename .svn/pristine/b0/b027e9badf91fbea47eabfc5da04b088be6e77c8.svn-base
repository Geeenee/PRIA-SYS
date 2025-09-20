var Quick_add = function()
{
	var $common		= "common";
	var $module		= "quick_add";

	var import_qa	= function(module_code)
	{
		$("#submit_modal_quick_add").off("click").on("click", function(e)
		{
			e.preventDefault();

			button_loader('submit_modal_quick_add', 1);

			var data	= $("#form_modal_quick_add").serialize();

			$('#form_modal_quick_add').parsley().validate();

			module_code	= (module_code != '')? '/'+module_code: '';

			$.post($base_url + $common + "/" + $module + "/import_quick_add" + module_code, data, function(result)
			{
				if(result.flag == '1')
				{
					//notification_msg(result.status, result.msg);
					if(result.status == 'warning')
					{
						//notification_msg(result.status, result.msg);
					}

					var batch_flag	= (result.batch_flag == true)? 1: 0;

					$("#generated_file").attr("onclick", "modal_generated_file_init('" + result.table_name + "/" + batch_flag +"', this, 'Proceed to Import')");
					$("#generated_file").attr("data-modal_post", `{"ids":"${result.temp_ids}"}`);
					$('#generated_file').trigger( "click" );

					// if(result.batch_flag == '1'){
						// $("button[data-btn-action='Import_record']").addClass(' none ');

						//hiding checkbox
						// $('.class_to_hide').remove();
					// }
					
					//$('#form_modal_generated_file').find('#table_body').html('table achuchu');
				}
				else
				{
					notification_msg(result.status, result.msg);
					$('#form_modal_quick_add').find('#error_div').html(result.msg);
					$('#form_modal_quick_add').find('#error_div').removeClass('none');
				}

				if(result.err_msg != '')
				{
					//download_to_text('PRIA Import Log File '+ result.date_time +'.txt', result.err_msg);
				}

				button_loader('submit_modal_quick_add', 0);
			
		  	}, 'json');
		});
	}

	var selectAll = function(index)
	{
		$('input[class=ind_checkbox_'+ index +']:checkbox').not(':disabled').each(function(){ 
			if($('input[class=check_all_'+ index +']:checkbox:checked').length == 0){ 
				$(this).prop("checked", false);
			} else {
				$(this).prop("checked", true);
			} 
		});
	}

	var process_import = function()
	{
		$("#modal_generated_file").find("button[data-btn-action='Import_record']").off("click").on("click", function(e)
		{
			e.preventDefault();
			
			$("#modal_generated_file").find("button[data-btn-action='Upload']").removeAttr('id');

			button_loader('submit_modal_generated_file', 1);

			var data = $("#form_modal_generated_file").serialize();
			
			$('#form_modal_generated_file').parsley().validate();

			$.post($base_url + $common + "/" + $module + "/process_imported_file", data, function(result)
			{
				notification_msg(result.status, result.msg);

				button_loader('submit_modal_generated_file', 0);
				
				if(result.flag == '1')
				{
					$('#modal_generated_file').find('.modal-action.modal-close').trigger('click');
				
					start_loading();

					window.location.reload(true);
				}

		  	}, 'json');
		});
	}

	var close_import_modal = function()
	{
		$('#modal_generated_file').find('.modal-action.modal-close').off("click").on("click", function(e)
		{
			e.preventDefault();
			$("#modal_generated_file").find("button[data-btn-action='Upload']").trigger('click');
			$("#modal_quick_add").modal('close');
		});

		$("#modal_generated_file").find("button[data-btn-action='Upload']").off("click").on("click", function(e)
		{
			e.preventDefault();

			var data = $("#form_modal_generated_file").serialize();
			$('#modal_generated_file').modal('close');
			$.post($base_url + $common + "/" + $module + "/clean_tmp_tables", data, function(result)
			{
		  	}, 'json');
		});
	}

	//For file versioning
	var file_vesioning_func = function()
	{
		
		$('#modal_upload_file').find("button[data-btn-action='Upload']").off("click").on("click", function(e)
			{
			    e.preventDefault();

			    if($('#form_modal_upload_file').parsley({inputs: 'input, textarea, select, input[type=hidden], :hidden',}).validate())
				{
				    button_loader('submit_modal_upload_file', 1);

				    var data = $("#form_modal_upload_file").serialize();

				    //Reset file to empty
				    //Close modal
				    $.post($base_url + $common + "/" + 'File_version' + "/insert_file_version", data, function(result){
				    // $.post($base_url + $common + "/" + $module + "/process_imported_file", data, function(result)
				    // {
				        if(result.flag == '1')
				        {
				            notification_msg(result.status, result.msg);
				            $('#modal_upload_file .modal-close, .close').trigger('click');    
				            $('#modal_file_version .modal-close, .close').trigger('click');

				        	location.reload();
				        }
				        else
				        {
				            notification_msg(result.status, result.msg);
				        }

				        button_loader('submit_modal_upload_file', 0);

				        
				      }, 'json');
				    $('#modal_generated_file .modal-close, .close').trigger('click');
				}
			});
	}

	var attach_file = function()
	{
		$("button[data-btn-action='Attach']").off("click").on("click", function(e)
		{
			e.preventDefault();
			
			button_loader('submit_modal_task_upload', 1);

			var data = $("#form_modal_task_upload").serialize();
			
			//Reset file to empty
			//Close modal
			$.post($base_url + $common + "/" + 'Task_attachment' + "/process", data, function(result)
			{
				start_loading();
				if(result.flag == '1')
				{
					notification_msg(result.status, result.msg);
					$('#modal_task_upload .modal-close, .close').trigger('click');	
					location.reload();
				}
				else
				{
					notification_msg(result.status, result.msg);
				}
			
				button_loader('submit_modal_task_upload', 0);
				end_loading();
				
		 	 }, 'json');			
		});
	}

	var cancelCallback 	= function(files, pd)
	{
		console.log(files);
		console.log(pd);
	}

	var successCallBack = function(files, data, arr, multiple_flag)
	{
		//console(multiple_flag);
		// $(".ajax-upload-dragdrop").fadeOut();
		//hide system file name
        $('.ajax-file-upload-filename').hide();
        // $('.ajax-upload-dragdrop').hide();
        // if(multiple_flag == 1){
        	//$('.ajax-file-upload').hide();
    	// }
	}

	var deleteCallBack = function(data, arr)
	{
		// $(".ajax-file-upload-error").fadeOut();
        // if($('.ajax-file-upload-filename').length == 0)
        // $(".ajax-upload-dragdrop").fadeIn();
	}

	return {

		import_qa : function(module_code)
		{
			import_qa(module_code);
		},
		process_import : function()
		{
			process_import();
		},

		close_import_modal : function()
		{
			close_import_modal();
		},

		attach_file : function()
		{
			attach_file();
		},

		selectAll : function(index)
		{
			selectAll(index);
		},
		selectCallback : function(files)
		{
			selectCallback(files);
		},
		cancelCallback	: function(files, pd)
		{
			cancelCallback(files, pd);
		},
		successCallBack : function(files, data, arr)
		{
			successCallBack(files, data, arr);
		},
		deleteCallBack : function(data, arr)
		{
			deleteCallBack(data, arr);
		},
		file_vesioning_func : function()
		{
			file_vesioning_func();
		}
	}
}();