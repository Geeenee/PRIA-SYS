var General = function(){

	var ajax_request = function(options){
		/* Sets of predefined config for ajax request */
		var predefined = {
				dataType   : "json",
				method     : "POST",
				beforeSend : function(){
					//no ui block in core General.showLoading();
					start_loading();
				},
				error : function(jqXHR, textStatus, errorThrown){
					console.log('Error : '+textStatus);
				},
				complete   : function(jqXHR, status){
					//no ui block in core  General.hideLoading();
					end_loading();
				}
		}
		/* Merge the two configs */
	   var final_option = $.extend(predefined, options);
		
	   return $.ajax(final_option);
	};
	
	var scroll_up = function(alert_id){
		$('.scrollable').animate({scrollTop:$('#'+alert_id).offset().top - 20}, "slow");
	};
	
	var bind_parsley = function($form, validate_visible_only, destroy){
		if(typeof $form == 'string'){
			$form = $('#'+$form);
		}else if(typeof $form == 'object' && $form instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}
		
		if(destroy)
			$form.parsley('destroy');
		
		/* Put the message below selectize */
		$parsley = $form.parsley({
			 errorsContainer (field) {

			 	if( $(field.$element[0]).is('select') && field.$element[0].selectize != undefined )
			 		return field.$element.next('.selectize-control');
			    
			  }	
		});

		/* Finds which tabs has parsley error */
		$parsley.off('form:error').on('form:error', function(ev){

			if(ev.$element.find('.tabs').length > 0)
			{ 	
	    		var tab_req = [];

	    		$.each(ev.fields, function(index, value){

	    			var validationResult 	= value.isValid();
	    			var tab_id 				= value.$element.closest('.tab-content').attr('id');
	    			var tab_val 			= $('ul.tabs').find('a[href="#'+tab_id+'"]').data('name');

	    			if( validationResult == false && $.inArray(tab_val, tab_req) == -1 && tab_val != undefined)  {
	    				tab_req.push(tab_val);
	    			}	

	    		});


	    		if(tab_req.length > 0)
	    		{
		    		var msg = 'Please fill up the required fields for the following tabs : ' + tab_req.join(', ');

					notification_msg($.CONSTANTS.WARNING, msg, false);
	    		}
    		}

		});
		
		/*if(validate_visible_only)
		{ 
			$parsley.on('field:validate', function(response){
				console.log(response);
			});
		}		*/	

		 $parsley.on('field:validate', function(response){
				//console.log(response.$element);
		 });
		
		
		return $parsley;
	};
	

	/*
	 * Handles the jquery upload
	 * @param1 tbl_body string : id of the table body
	 * @param2 tbl_row  string : id of the table row
	 * @param3 elements object : object with the details of the elements to be cloned.
	 * Note Example value of @param3 { field_id : {required:true, type: 'select2'} }. type may be omitted.
	 */
	var add_row = function(tbl_body, tbl_row, elements){
		/* Initiliaze params
		 * $row  	: jquery object for table row to cloned.
		 * $body    : jquery object for table body which the new row will be appended.
		 * $cloned  : the cloned row. *notice i removed the style so that the hidden css will be remove to. 
		 */
		var $row    = $('#' + tbl_row);
		var $body   = $('#' + tbl_body);
		var $cloned = $row.clone().removeAttr('id class');
		//console.log($cloned.find(':input, a'));
		/* Loop the elements inside the cloned row */
		$cloned.find(':input, a').each(function(){
			/* Gets the id of the current element in the loop */
			var id = $(this).attr('id');
			
			/* Assign a name for the element. *notice will just use the id of element and add [] */
			$(this).attr('name', id+'[]');
			
			/* Check if element_id is defined in the elements */
			if(elements.hasOwnProperty(id)){
			
				/* Get the requirement for this element */
				var requirements = elements[id];
				
				/* If element is required bind data-parsley-required */
				if(requirements.hasOwnProperty('required')){					
					$(this).attr('data-parsley-required', true);
				}
				
				/* If element has type property check what jquery plugin should be bind */
				if(requirements.hasOwnProperty('type')){
					switch(requirements.type)
					{
						case 'selectize' :
							//$(this).removeClass('selectize');
							//console.log($(this), $(this)[0]);
							//$(this)[0].selectize.destroy();
							$(this).selectize();
						break;
						case 'datepicker' :
							$(this).datepicker();
						break;
						case 'delete' :
							$(this).removeClass('hide');

							$(this).on('click',function(){
								$(this).closest("tr").remove();
							});
						break;
					}
				}
			}
		});
		
		/* Append the cloned table row to the table body */
		$body.append($cloned);
	};
	


	var is_empty  = function(value){
		var empty = (value) ? 0 : 1;
		
		if( ! empty && typeof(value) == 'string'){
			var lowered		= value.toLowerCase();
			var invalid_str = ['null', 'undefined', 'nan']; 
			
			empty       	= ($.inArray( lowered, invalid_str ) < 0) ? 0 : 1;
		}
		
		return empty;
	};

	var undisable_fields = function($form){
		if(typeof $form == 'string'){
			$form = $('#'+$form);
		}else if(typeof $form == 'object' && $form instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		
		$form.find('input,textarea').prop('disabled', false);
		$form.find('input,select,textarea').css('cssText', 'color: black; opacity: 0.8;');

		$form.find('select.selectize').each(function(index){

			if(this.selectize != undefined)  this.selectize.enable();
			
		});

		/*
		$readonly = $form.find('.readonly');
		$readonly.filter('.select2').select2('readonly', true);
		*/
	}

	var disable_fields = function($form){
		if(typeof $form == 'string'){
			$form = $('#'+$form);
		}else if(typeof $form == 'object' && $form instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		//$form.find('input,select,textarea').prop('disabled', true);
		$form.find('input,textarea').prop('disabled', true);
		$form.find('input,select,textarea').css('cssText', 'color: black; opacity: 0.8;');

		$form.find('select.selectize').each(function(index){

			if(this.selectize != undefined)  this.selectize.disable();
			
		});

		/*
		$readonly = $form.find('.readonly');
		$readonly.filter('.select2').select2('readonly', true);
		*/
	};


	var disable_field = function($form){
		if(typeof $form == 'string'){
			$form = $('#'+$form);
		}else if(typeof $form == 'object' && $form instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		$form.prop('disabled', true);
		//$form.css('cssText', 'color: black; opacity: 0.8;');
	};
	

	var undisable_field = function($form){
		if(typeof $form == 'string'){
			$form = $('#'+$form);
		}else if(typeof $form == 'object' && $form instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		$form.prop('disabled', false);
		//$form.css('cssText', 'color: black; opacity: 0.8;');
	};
	
	


	var close_modal  = function(){
		var modal_id = '#' + $('div.md-show').attr('id');
		
		$(modal_id).removeClass('md-show');
	}
	
	var show_elem	 = function($elem){
		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}
		
		$elem.removeClass('hide');
	}
	
	var hide_elem	 = function($elem){
		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}
		
		$elem.addClass('hide');
	}


	var set_dropdown_values_ajax = function(options)
	{
		if(options == 'undefined' || options == null || jQuery.isEmptyObject(options) == true){
			console.log('options must not be empty');
			return false;
		}

		$elem = options.elem;

		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		var ajax_options = {
            url     :  options.path,
            data    :  options.data,
            success :  function(response){
                var elem_select = $elem[0].selectize;

                elem_select.clear();
                elem_select.clearOptions();
                elem_select.load(function(callback){
                     callback(response);
                });
            }  
        };

      	 ajax_request(ajax_options);
	};

	var set_dropdown_values = function(options)
	{
		if(options == 'undefined' || options == null || jQuery.isEmptyObject(options) == true){
			console.log('options must not be empty');
			return false;
		}

		$elem = options.elem;

		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		
        var elem_select = $elem[0].selectize;

        elem_select.clear();
        elem_select.clearOptions();
        elem_select.load(function(callback){
             callback(options.list);
        });
           
	};

	var reset_fields = function($elem)
	{
		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		$elem.find(':input').val('');

		$elem.find('input:checked').each(function() {
            $(this).prop('checked', false);
		});

		$elem.find('select.selectize').each(function(){
			$(this)[0].selectize.clear();
		});

		$elem.find('select.sumoselect').each(function(){
			$(this)[0].sumo.unSelectAll();
		});

	}

	var tag_fields_required = function($elem)
	{
		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}


		$elem.find(':input').not('div.selectize-input :input').each(function(){
			$(this).attr('data-parsley-required', 'true');
		});	
	}

	var tag_fields_not_required = function($elem)
	{
		if(typeof $elem == 'string'){
			$elem = $('#'+$elem);
		}else if(typeof $elem == 'object' && $elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for form!');
			return false;
		}

		$elem.find(':input').not('div.selectize-input :input').each(function(){
			$(this).attr('data-parsley-required', 'false');
		});	
	}

	var close_curr_modal = function()
	{
		$('.modal:visible').modal('close');
	}

	var get_curr_date = function(datetime)
	{
		if(datetime)
			return dateFormat(new Date(), $.CONSTANTS.FORMAT_DATETIME);
		else
			return dateFormat(new Date(), $.CONSTANTS.FORMAT_DATE);
	}
	
	
	var remove_hide_required = function($form){
		if(typeof $form == 'string'){
			$form = $form;
		}else if(typeof $form == 'object' && $form instanceof jQuery){
			
		}else{
			console.log('wrong type passed for $form!');
			return false;
		}
		
		$form.on("change", function(){
			$(".hide input, .hide select").attr("data-parsley-required", false);
		});
	}
	
	var get_locations = function($elem, $sub_elem, $loc_type){
		if(typeof $sub_elem == 'string'){
			$sub_elem = $('#'+$sub_elem);
		}else if(typeof $sub_elem == 'object' && $sub_elem instanceof jQuery){
			
		}else{
			console.log('wrong type passed for $sub_elem!');
			return false;
		}

		var arrTypes = [$.CONSTANTS.LOCATION_REGION,$.CONSTANTS.LOCATION_PROVINCE,$.CONSTANTS.LOCATION_CITY];
		if($.inArray($loc_type, arrTypes) == -1)
			console.log('Does not match in available location types.');

		var code	= $elem.val();
		var url	 	= $base_url + 'locations/get_locations';
		var data 	= {location_code: code, location_type: $loc_type};

		$.post(url, data, function(options){
			$sub_elem[0].selectize.destroy();
			$sub_elem.html(options);
			$sub_elem.selectize();
		});
	};


	const request_fetch = function(options)
	{
		const successFunc  = options.successFunc;
		const path 		   = options.path;
		

		//Predefined options
		const predefined   = {
			method : 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
		};

		if( csrf_name )    
		{
			if( options.body )
				options.body  += '&'+csrf_name+'='+csrf_token;    
			else
				options.body   = csrf_name+'='+csrf_token;    
		}

		const blockUI 	   = options.blockUI;
		let   completeFunc = args => {
			if(blockUI)
				end_loading();
		};

		if(options.completeFunc)
		{
			completeFunc = options.completeFunc;

			delete options.completeFunc;	
		}	
		//remove the ff options because its not part of Request interface
		delete options.blockUI;
		delete options.successFunc;
		delete options.path;

		const finalOptions = Object.assign(predefined, options);

		if(blockUI)
			start_loading();

		fetch(path, finalOptions)
		.then(General.getResponseFetch)
		.then(successFunc)
		.then(completeFunc)
		.catch(General.catchFetch);
	}

	/*
	 * return obj
	 */
	return {
		ajax : function(options){
			return ajax_request(options);
		},
		bindParsley : function(form, visible_only, destroy){
			var validate_visible_only = (typeof visible_only != 'undefined') ? visible_only : true;
			var destroy = (typeof destroy  != 'undefined') ? destroy : false;
			
			return bind_parsley(form, validate_visible_only, destroy);
		},
		addRow : function(tbl_body, tbl_row, elements){
			add_row(tbl_body, tbl_row, elements);
		},
		isEmpty : function(value){
			return is_empty(value);
		},
		disableFields : function($form){
			disable_fields($form);
		},
		disableField : function($form){
			disable_field($form);
		},
		closeModal : function()
		{
			close_modal();
		},
		//ADDED NEW 
		showElem : function(elem)
		{
			show_elem(elem);
		},
		hideElem : function(elem)
		{
			hide_elem(elem);
		},
		setDropDownValuesAjax : function(options)
		{
			set_dropdown_values_ajax(options);
		},
		setDropdownValues : function(options)
		{
			set_dropdown_values(options);
		},
		resetFields : function(elem)
		{
			reset_fields(elem);
		},
		unDisableFields : function($form){
			undisable_fields($form);
		},
		unDisableField : function($form){
			undisable_field($form);
		},
		tagFieldsRequired : function($container){
			tag_fields_required($container);
		},
		tagFieldsNotRequired : function($container){
			tag_fields_not_required($container);
		},	
		closeCurrModal:function(){
			close_curr_modal();
		},
		getCurrDate:function(datetime){
			var datetimeonly = datetime != undefined ? datetime : false;

			return get_curr_date(datetimeonly);
		},
		removeHideRequired : function($form)
		{
			remove_hide_required($form);
		},
		getLocations : function(elem, sub_elem, loc_type)
		{
			get_locations(elem, sub_elem, loc_type);
		},
		getResponseFetch : function(response)  
		{
			if(response.ok)
				return response.json()
			else
				throw new Error(`Error : ${response.status}`);
		},
		catchFetch : function(err)
		{
			console.error(err);
		},
		Fetch : function(options)
		{
			request_fetch(options);
		},
		clearCkeditor : function(){
			if( typeof(CKEDITOR) !== 'undefined' && CKEDITOR !== undefined )
			{
				if( Object.keys(CKEDITOR.instances).length !== 0 )
				{
					for ( instance in CKEDITOR.instances ) 
					{ 
						CKEDITOR.instances[instance].setData('');
					}
				}
			}
		}
	};
}();



