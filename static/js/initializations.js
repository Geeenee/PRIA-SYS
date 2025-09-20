/*
|--------------------------------------------------------------------------
| INITIALIZATION
|--------------------------------------------------------------------------
|
| These functions are used when initializing plugins in modals, tabs, etc.
| Instead of reloading the plugin scripts.
| s
| To be placed inside 
| 	$resources['loaded_init'] = array();
|	$this->load_resources->get_resource($resources);
*/

function materialize_select_init(id)
{
	var id = id || ".material-select";
	$(id).material_select();
}
function number_init(class_id, decimal_places)
{
	var class_id = class_id || ".number",
		decimal_places = ( decimal_places !== undefined ) ? decimal_places : 2;
	
	$('input' + class_id).number(true, decimal_places);
}

function sumo_select( class_id, settings )
{
	var default_setting 	= $.extend({
        placeholder: 'Select Here',   // Dont change it here.
        csvDispCount: 3,              // display no. of items in multiselect. 0 to display all.
        captionFormat:'{0} Selected', // format of caption text. you can set your locale.
        captionFormatAllSelected:'{0} all selected', // format of caption text when all elements are selected. set null to use captionFormat. It will not work if there are disabled elements in select.
        floatWidth: 400,              // Screen width of device at which the list is rendered in floating popup fashion.
        forceCustomRendering: false,  // force the custom modal on all devices below floatWidth resolution.
        nativeOnDevice: ['Android', 'BlackBerry', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile', 'Silk'], //
        outputAsCSV: false,           // true to POST data as csv ( false for Html control array ie. default select )
        csvSepChar: ',',              // separation char in csv mode
        okCancelInMulti: false,       // display ok cancel buttons in desktop mode multiselect also.
        isClickAwayOk: false,         // for okCancelInMulti=true. sets whether click outside will trigger Ok or Cancel (default is cancel).
        triggerChangeCombined: true,  // im multi select mode whether to trigger change event on individual selection or combined selection.
        selectAll: false,             // to display select all button in multiselect mode.|| also select all will not be available on mobile devices.

        search: false,                // to display input for filtering content. selectAlltext will be input text placeholder
        searchText: 'Search...',      // placeholder for search input
        noMatch: 'No matches for "{0}"',
        prefix: '',                   // some prefix usually the field name. eg. '<b>Hello</b>'
        locale: ['OK', 'Cancel', 'Select All'],  // all text that is used. don't change the index.
        up: false,                    // set true to open upside.
        showTitle: true,               // set to false to prevent title (tooltip) from appearing
        modal_id : ''
    }, settings);

    $(class_id).SumoSelect(default_setting);

    if( default_setting.modal_id != '' )
	{

	    var api_orig    =  $('#'+default_setting.modal_id).find('.scroll-pane').data('jsp');

	    if( api_orig !== undefined )
	    {
	        api_orig.destroy();
	    }

	    var container     = $('#'+default_setting.modal_id).find('.scroll-pane').jScrollPane({autoReinitialise: true, contentWidth: '0px'});

	    var api = $('#'+default_setting.modal_id).find('.scroll-pane').data('jsp');  

	    api.reinitialise();  
	}
}

function selectize_init(class_id, type, max_item)
{
	var type = type || '',
		class_id = class_id || '',
		max_item = max_item || 3;
		
		default_class = 'selectize',
		tagging_class = 'tagging',
		max_item_class = 'tagging-max';

	try
	{
		if(type != '' && class_id != '')
		{
			switch(type)
			{
				case 'default' :
					var default_class = class_id;
				break;
				
				case 'tagging' :
					var tagging_class = class_id;
				break;
				
				case 'max-items' :
					var max_item_class = class_id;
				break;
			}
		}
		
		if(  $( 'select.' + default_class ).length !== 0 )
		{
			$( 'select.' + default_class ).each( function() {

				var sel_opt		= {
					plugins 		: ['remove_button'],
					onInitialize 	: function()
					{
						var s = this;
						
						this.revertSettings.$children.each(function() 
						{
							$.extend(s.options[this.value], $(this).data());
						});
					}
				}

				if($(this).attr('data-extra_opt_function'))
				{
					var sel_check_opt 		= eval($(this).attr('data-extra_opt_function'));

					if( sel_check_opt )
					{
						$.extend( sel_opt, sel_check_opt );
					}
				}
				
				if( !$(this).hasClass( 'selectized' ) )
				{
					$(this).selectize(sel_opt);
				}
			} )
		}

		if( $( 'select.selectize-tagging' ).length !== 0 )
		{
			$( 'select.selectize-tagging' ).each( function() {

				var tag_opt 	= {
					plugins: ['remove_button'],
					createOnBlur: true,
				    create: true,
				    delimiter: ',',
				    persist: false
				}

				if($(this).attr('data-extra_opt_function'))
				{
					var tag_check_opt 		= eval($(this).attr('data-extra_opt_function'));

					if( tag_check_opt )
					{
						$.extend( tag_opt, tag_check_opt );
					}
				}

				if( !$(this).hasClass( 'selectized' ) )
				{
					$(this).selectize(tag_opt);
				}

			} );
		}

		if( $( 'input.' + tagging_class ).length !== 0 )
		{
			$( 'input.' + tagging_class ).each( function() {

				var inp_tag_opt 	= {
					plugins: ['remove_button'],
					createOnBlur: true,
				    create: true,
				    delimiter: ',',
				    persist: false
				};

				if($(this).attr('data-extra_opt_function'))
				{
					var inp_tag_check_opt 		= eval($(this).attr('data-extra_opt_function'));

					if( inp_tag_check_opt )
					{
						$.extend( inp_tag_opt, inp_tag_check_opt );
					}
				}

				if( !$(this).hasClass( 'selectized' ) )
				{
					$(this).selectize(inp_tag_opt);
				}

			} );
		}

		if( $( 'select.' + max_item_class ).length  !== 0 )
		{
			$(  'select.' + max_item_class ).each( function() {
				if( !$(this).hasClass( 'selectized' ) )
				{
					$(this).selectize({ maxItems: max_item });
				}
			} )
		}

		if( $( 'input.selectize-custom-input' ).length !== 0 )
		{
			$( 'input.selectize-custom-input' ).each( function() {

				var select_custom_opt 	= {
					plugins: ['remove_button'],
				 	delimiter: ';',
				    persist: false,
				    create: function(input) {
				        return {
				            value: input,
				            text: input
				        }
				    }
				}

				if($(this).attr('data-extra_opt_function'))
				{
					var select_check_custom_opt 		= eval($(this).attr('data-extra_opt_function'));

					if( select_check_custom_opt )
					{
						$.extend( select_custom_opt, select_check_custom_opt );
					}
				}

				if( !$(this).hasClass( 'selectized' ) )
				{
					$(this).selectize(select_custom_opt);
				}

			} );
			   
		}

		if( $('select.lazy-selectize').length !== 0 )
		{
			$( 'select.lazy-selectize' ).each( function() {
				var options 	= {
					data 	: {},
					convertArrayToOptions : function(data)
					{
						if( data )
						{	
							return JSON.parse(data);
						}
					},
					plugins : ['remove_button'],
					onInitialize 	: function()
					{
						var s = this;
						
						this.revertSettings.$children.each(function() 
						{
							$.extend(s.options[this.value], $(this).data());
						});
					}
				}

				if( $(this).attr('multiple') )
				{
					options.mode 		= 'multi';
					options.maxItems 	= null;
				}
				
				if(!$(this).attr('data-loadItemsUrl'))
				{
					throw new Error('attribute data-loadItemsUrl is required. for select '+$(this).attr('id'));
				}

				options.loadItemsUrl = $(this).attr('data-loadItemsUrl');

				if($(this).attr('data-extra_opt_function'))
				{
					var check_opt 		= eval($(this).attr('data-extra_opt_function'));

					if( check_opt )
					{
						$.extend( options, check_opt );
					}
				}
				
				if( !$(this).hasClass( 'selectized' ) )
				{
					$(this).lazySelectize(options);
				}
			} )
		}
	}
	catch(main_err) 
 	{
 		notification_msg('error', main_err);
		console.log(main_err);
	}
}

function datepicker_init()
{
	/* $('.datepicker').datetimepicker({
		timepicker:false,
		scrollInput: false,
		format:'m/d/Y',
		formatDate:'m/d/Y',
	  }); */

	$('.datepicker').each(function(index, elem){
		var data 	= $(this).data(),
		    options = {
				timepicker:false,
				scrollInput: false,
				format:'m/d/Y',
				formatDate:'m/d/Y',
			};

		if(typeof data.maxDate !== "undefined")	options.maxDate = data.maxDate;

		$(this).datetimepicker(options);
	});

		
	$('.timepicker').datetimepicker({
		datepicker:false,
		format:'h:i A'
	});


	
	$('.datepicker_start').each(function(index, elem){
		var data 	= $(this).data(),
		    options = {
				format:'m/d/Y',
				formatDate:'m/d/Y',
				scrollInput: false,
				onShow:function( ct ){
					if(typeof data.maxDate === "undefined")
					{
						this.setOptions({
							maxDate:jQuery('.datepicker_end').val()?jQuery('.datepicker_end').val():false
						});
					}
				},
				timepicker:false
			};

		if(typeof data.maxDate !== "undefined")	options.maxDate = data.maxDate;

		if(typeof data.minDate !== "undefined")	options.minDate = data.minDate;
			
		$(this).datetimepicker(options);
	});
		
/* 	$('.datepicker_start').datetimepicker({
		format:'m/d/Y',
		formatDate:'m/d/Y',
		scrollInput: false,
		onShow:function( ct ){
		  this.setOptions({
			maxDate:jQuery('.datepicker_end').val()?jQuery('.datepicker_end').val():false
		  })
		},
		timepicker:false
	}); */
		
	$('.datepicker_end').each(function(index, elem){
		var data 	= $(this).data(),
		    options = {
				format:'m/d/Y',
				formatDate:'m/d/Y',
				scrollInput: false,
				onShow:function( ct ){
					this.setOptions({
						minDate:jQuery('.datepicker_start').val()?jQuery('.datepicker_start').val():false
					})
				},
				timepicker:false
			};

		if(typeof data.maxDate !== "undefined")	options.maxDate = data.maxDate;

		if(typeof data.minDate !== "undefined")	options.minDate = data.minDate;
			
		$(this).datetimepicker(options);
	});

/* 	$('.datepicker_end').datetimepicker({
		format:'m/d/Y',
		formatDate:'m/d/Y',
		scrollInput: false,
		onShow:function( ct ){
			this.setOptions({
				minDate:jQuery('.datepicker_start').val()?jQuery('.datepicker_start').val():false
			})
		},
		timepicker:false
	});
 */
	$('.datetimepicker_start').datetimepicker({
		format:'m/d/Y h:i A',
		formatDate:'m/d/Y h:i A',
		scrollInput: false,
		onShow:function( ct ){
		  this.setOptions({
			maxDate:jQuery('.datetimepicker_end').val()?jQuery('.datetimepicker_end').val():false
		  })
		},
		onClose: function(ct, $i){
				$i.parsley().validate();
		},
		timepicker:true
	});
		
	$('.datetimepicker_end').datetimepicker({
		format:'m/d/Y h:i A',
		formatDate:'m/d/Y h:i A',
		scrollInput: false,
		onShow:function( ct ){
			this.setOptions({
				minDate:jQuery('.datetimepicker_start').val()?jQuery('.datetimepicker_start').val():false
			})
		},
		onClose: function(ct, $i){
				$i.parsley().validate();
		},
		timepicker:true
	});

	if($('.datepicker,.datepicker_start,.datepicker_end,.timepicker').length === 0)
	$('.datepicker,.datepicker_start,.datepicker_end,.timepicker').datetimepicker('destroy');
}

function labelauty_init()
{
	if( $('.labelauty').length !== 0 )
	{
		
		$(".labelauty").not('.labelauty-initialized').labelauty({
			checked_label: "",
			unchecked_label: "",
			class: "labelauty-initialized"
		});
	}
}

function collapsible_init()
{
	$('.collapsible').collapsible({
		accordion : false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
	});
}

function scrollspy_init()
{
	$('.scrollspy').scrollSpy();
}

function dropdown_button_init(class_id)
{
	var elem 	= (  class_id !== undefined ) ? '.'+class_id : '.dropdown-button';
	
	$(elem).dropdown();
}

function colorpicker_init( selector )
{

	var selector;

	if( selector !== undefined )
	{
		if( typeof( selector ) === 'string' )
		{
			selector 	= $( selector );
		}
		else if( selector instanceof jQuery )
		{
			selector 	= selector
		}
	}
	else
	{
		selector 		= $('.color-picker');
	}

	if( selector.val() != '' )
	{
		selector.css('backgroundColor', '#' + selector.val());
	}

	selector.ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			selector.val('#'+hex);
			selector.css('backgroundColor', '#' + hex);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
}

function load_datatable(custom_settings)
{

	var default_setting 	= $.extend({
		path 					: "",
		table_id 				: "",
		scrollX 				: "",
		scrollY 				: "",
		modal 					: '',
		highlight 				: false,
		group_column 			: 0,
		colspan 				: 0,
		order 					: 0,
		sort					: true,
		sort_order 				: "asc",
		hidden_column			: "",
		cols 					: [],
		sortable_index 			: "_all",
		advanced_filter 		: false,
		display_length 			: 10,
		length_change			: true,
		custom_option_callback 	: "",
		func_callback 			: "",
		succ_callback 			: "",
		post_data 				: {},
		with_search 			: true,
		info		 			: true,
		dom 					: "<'row m-n dataTable-filter p-t-md' <'col l6 m12 s12 p-l-n'B> <'col l3 m4 s12 right-align p-n'l> <'col l3 m8 s12 p-n'f> > <'row dt-table m-n'tr> <'row dataTable-footer m-n' <'col s4 p-n'i> <'col s8 p-n'p> > ",
		buttons 				: [],
		export_file_name 		: '',
		export_title 			: '',
		delete_remove_filter	: false,
		select 					: false,
		state_save 				: false,
		page_length 			: 10,
		jump_to_page 			: true,
		post_data_func 			: '',
		search_func 			: ''

	}, custom_settings);

	var default_buttons 	= [
		'excel', 'pdf', 'print', 'colvis'
	],
	buttons,
	button_i 				= 0,
	button_len,
	button_opt 				= [];

	try
	{
		if( default_setting.buttons.length !== 0 )
		{
			buttons 				= default_setting.buttons;
		}
		else
		{
			buttons 				= default_buttons;
		}

		if( default_setting.buttons === false )
		{
			buttons 				= [];
		}

		button_len 					= buttons.length;

		if( button_len !== 0 )
		{
			for( ; button_i < button_len; button_i++ )
			{
				var b_opt 				= {};

				if( buttons[ button_i ] != 'colvis' )
				{
					if( buttons[ button_i ] != 'print' )
					{
						b_opt.extend 			= buttons[ button_i ]+'Html5';
					}
					else
					{
						b_opt.extend 			= buttons[ button_i ];
					}

					if( buttons[ button_i ] == 'pdf' )
					{
						b_opt.download 			= 'open';
					}

					b_opt.exportOptions 		= {
						columns : ':visible:not(.col-actions)',
						format: {
							body: function(data, row, column, node) {
								data = $("<p>" + data + "</p>").text();
								return "\0" + data;
							}
						}
					};

					if( default_setting.export_file_name != '' )
					{
						b_opt.filename 			= default_setting.export_file_name;
					}

					if( default_setting.export_title != '' )
					{
						b_opt.title 			= default_setting.export_title;
					}
				}
				else
				{
					b_opt.extend 				= buttons[ button_i ];
				}

				button_opt.push( b_opt );
			}
		}
		
		if( default_setting.table_id == "" )	
		{
			throw new Error("Table id is required.");
		}

		if( default_setting.path == "" )	
		{
			throw new Error("path is required.");
		}

		var scrollX 				= default_setting.scrollX,
			scrollY 				= default_setting.scrollY,
			modal 					= default_setting.modal,
			highlight 				= default_setting.highlight,
			group_column 			= default_setting.group_column,
			colspan 				= default_setting.colspan,
			order 					= default_setting.order,
			sort 					= default_setting.sort,
			sort_order 				= default_setting.sort_order,
			hidden_column			= default_setting.hidden_column,
			cols 					= default_setting.cols,
			page_length 			= default_setting.page_length,
			length_change			= default_setting.length_change,
			custom_option_callback 	= default_setting.custom_option_callback,
			func_callback 			= default_setting.func_callback,
			post_data 				= default_setting.post_data,
			with_search 			= default_setting.with_search,
			info		 			= default_setting.info,
			delete_remove_filter 	= default_setting.delete_remove_filter,
			options 		 		= {},
			table_obj 				= $("#"+default_setting.table_id),
			select 					= default_setting.select,
			state_save 				= default_setting.state_save,
			display_length 			= default_setting.display_length,
			search_params 			= {},
			search_func 			= default_setting.search_func;;
			// JEFF'S CODE array object of sortable fields
			sortable_index	 		= default_setting.sortable_index || "_all";
			sortable_index 			= (sortable_index != '_all') ? (( typeof( sortable_index ) === 'string' ) ? JSON.parse(sortable_index) : sortable_index ) : sortable_index;

		var tableD_obj;

		// for pdf only
		button_opt[1].orientation = 'landscape';
		button_opt[1].customize = function (doc) {
			doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
		}
		
		options 					= {
			"bDestroy": true,
			"bProcessing": true,
			"bServerSide": true,
			"scrollX": scrollX,
			"scrollY": scrollY,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"sAjaxSource": $base_url + default_setting.path, 
			"order": [[ order, sort_order ]],
			"bLengthChange" : length_change,
			"pageLength" : page_length,
			"bSort": sort,
			"searching" : with_search,
			"jump_to_page" : default_setting.jump_to_page,
			"info" : info,
			"iDisplayLength" : display_length,
			stateSave : state_save,
			select : select,
			dom : default_setting.dom,
	        buttons: button_opt,
			"rowCallback": function( row, data ) {
				if(highlight === true) {
					if(row._DT_RowIndex == 0)
						$(row).addClass('highlightRow');
				}
	        },
			"fnServerData": function ( sSource, aoData, fnCallback ) {
				if( Object.keys(search_params).length !== 0 )
				{
					for( var key in search_params )
					{  
						aoData.push( { 'name' : key, 'value' : search_params[ key ] } );
					}
					
				}

				if( post_data !== undefined && post_data != '' )
				{
					if( typeof( post_data ) !== 'object' )
					{
						post_data 	= JSON.parse( post_data );
					}
					if( post_data instanceof Array )
					{
						$.extend(aoData, post_data);
					}
					else
					{
						if( Object.keys(post_data).length !== 0 )
						{
							
							for( var key in post_data )
							{ 
								if( post_data[ key ] instanceof Array )
								{
									var len_data 	= post_data[ key ].length,
										data_cnt 	= 0;
		
									for( ; data_cnt < len_data; data_cnt++ )
									{
										aoData.push( { 'name' : key+'[]', 'value' : post_data[ key ][ data_cnt ] } );	
									}
								}
								else
								{
									aoData.push( { 'name' : key, 'value' : post_data[ key ] } );	
								}
								
							}
						}
					}
				}

				if( default_setting.post_data_func != '' )
				{
					var check_p = eval(default_setting.post_data_func);

					if( check_p )
					{
						aoData = check_p
					}
				}

				$.ajax( {
					"dataType": 'json', 
					"type": "POST", 
					"url": sSource, 
					"data": aoData, 
					"success": function( result ) {
						
						if( result.flag !== undefined )
						{
							if( result.flag == 0 )
							{
								notification_msg('error', result.msg);
							}
						}

						fnCallback( result );

						if( default_setting.succ_callback != '' )
						{
							eval( default_setting.succ_callback );

						}

						if( modal )
						{
						  	$(modal).find('div.modal-content').jScrollPane({autoReinitialise: true, contentWidth: '0px'});
						}
					} 
				} );	
			}
		};
			
		if(group_column > 0)
		{
			for(cnt = 0; cnt < group_column; cnt++)
			{
				cols.push(cnt);
			}	

			options['drawCallback'] 	= function ( settings ) {
				var api = this.api();
				var rows = api.rows( {page:'current'} ).nodes();
				var last=null;
				var td_class = "";
				var td_colspan = "";
				
				$.each(cols, function( index, value ) {
					
					td_class = " class='yellow lighten-3 font-semibold'";
					if (group_column === value+1){
						if(colspan > 0)	
							td_colspan = "colspan='"+colspan+"'";
					}	
						
					api.column(value, {page:'current'} ).data().each( function ( group, i ) {
						if ( last !== group && group.length > 0) {
							$(rows).eq( i ).before(
								'<td '+td_colspan + td_class+'>' + group + '</td>'
							);
		 
							last = group;
						}
					} );
				});
				
				/* To automatically activate tooltips */
				if( $( '.tooltipped' ).length !== 0 )
				{
					$('.tooltipped').tooltip({delay: 50});
				}
				
				/* To automatically activate modal */
				if( ModalEffects !== undefined && $( '.md-trigger' ).length !== 0 )
				{
					ModalEffects.re_init();
				}

				if( $('.labelauty').length !== 0 )
				{					
					labelauty_init();					
				}

				if( $('.datepicker').length !== 0 )
				{
					
						datepicker_init();
					
				}

				if( $('.selectize').length !== 0 )
				{
					if( typeof( $.fn.selectize ) === 'undefined' )
					{
						$.getScript( $base_url+'static/js/selectize.js' );
					}

					selectize_init();
				}

				if( $('.default-avatar').length !== 0 && typeof( $.fn.initial ) !== 'undefined'  )
				{
					create_avatar($('.default-avatar'), {width:80,height:80,fontSize:30});
				}

				if( typeof($.fn.dropdown) !== "undefined" && $('.dropdown-button').length !== 0 )
				{
					dropdown_button_init();
				}
			};

			options['columnDefs'] 	= [
				{ orderable: false, targets: -1 },
				{ targets: sortable_index, orderable: true},
		        (sortable_index !== '_all') ? { targets: '_all', orderable: false }  : '',
				{ visible: false, targets: cols }
			];
				
		} else {	

			options['drawCallback'] 	= function() {
				if( $( '.tooltipped' ).length !== 0 )
				{
					$('.tooltipped').tooltip({delay: 50});
				}

				if( $('.datepicker').length !== 0 )
				{
					
					datepicker_init();
					
				}

				if( $('.labelauty').length !== 0 )
				{					
					labelauty_init();					
				}
				
				/* To automatically activate modal */
				if( ModalEffects !== undefined && $( '.md-trigger' ).length !== 0 )
				{
					ModalEffects.re_init();
				}

				if( $('.selectize').length !== 0 )
				{
					if( typeof( $.fn.selectize ) === 'undefined' )
					{
						$.getScript( $base_url+'static/js/selectize.js' );
					}

					selectize_init();
				}

				if( $('.default-avatar').length !== 0 && typeof( $.fn.initial ) !== 'undefined'  )
				{
					create_avatar($('.default-avatar'), {width:80,height:80,fontSize:30});
				}

				if( typeof($.fn.dropdown) !== "undefined" && $('.dropdown-button').length !== 0 )
				{
					dropdown_button_init();
				}
			}
			
			options['columnDefs'] 	=  [
				{ orderable: false, targets: -1 },
				{ targets: sortable_index, orderable: true},
				(hidden_column !== '') ? { targets: hidden_column, visible: false, searchable: false } : '',
		        (sortable_index !== '_all') ? { targets: '_all', orderable: false }  : ''
			];
		}	

		if( default_setting.advanced_filter )
		{
			options["orderCellsTop"] = true;

			if( with_search )
			{
				options['searching'] 	 = true;
			}
			else
			{
				options['searching'] 	 = false;
			}

		}

		if( custom_option_callback != '' )
		{
			var check_options 	= eval(custom_option_callback);

			if( check_options !== undefined )
			{
				options 		= check_options;
			}

		}

	 	if( default_setting.scrollX )
        {
			options['initComplete']	= function(settings)
			{
				var my_table 			= this;
				
			 	var main_obj 			= $('#'+default_setting.table_id+'_wrapper').find('.dataTables_scroll').find('.dataTables_scrollHead').find('table')	
			 	var filter_submit 		= main_obj.find('.filter-submit');
			 	var filter_cancel 		= main_obj.find('.filter-cancel');

				main_obj.find('.form-filter').bind('keyup', function(e) {
	             	e.stopImmediatePropagation();
	             	var val 	= $(this).val();
					if(e.keyCode == 13)
					{
						filter_submit.trigger('click');
					}
				});
					 
				filter_submit.on('click', function(e)
				{
				  	e.stopImmediatePropagation();
					search_params['action'] 	= 'filter';

					$('textarea.form-filter-outside, select.form-filter-outside, input.form-filter-outside:not([type="radio"],[type="checkbox"])').each(function() {
					    search_params[$(this).attr("name")] = $(this).val();
					});

					$('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', main_obj).each(function() {
						search_params[$(this).attr("name")] = $(this).val();
					});

					  // get all checkboxes
					$('input.form-filter[type="checkbox"]:checked', main_obj).each(function() {
					    search_params[$(this).attr("name")] = $(this).val();
					});

					$('input.form-filter-outside[type="checkbox"]:checked').each(function() {
					    search_params[$(this).attr("name")] = $(this).val();
					});

					// get all radio buttons
					$('input.form-filter[type="radio"]:checked', main_obj).each(function() {
					    search_params[$(this).attr("name")] = $(this).val();
					});

					 $('input.form-filter-outside[type="radio"]:checked').each(function() {
					    search_params[$(this).attr("name")] = $(this).val();
					});

				 	if( search_func )
		            {
		            	search_params 	= eval(search_func);
		            }

		             if( tableD_obj )
			        {

			        	tableD_obj.state.clear();
			        }

					my_table.DataTable().ajax.reload();

					$('.tooltipped').tooltip('remove');

				});

				filter_cancel.on('click', function(e)
				{
					e.stopImmediatePropagation();

					search_params['action'] 	= 'filter_cancel';

				 	$('textarea.form-filter, select.form-filter, input.form-filter', main_obj).each(function() {
					    $(this).val("");
					});

					$('input.form-filter[type="checkbox"]', main_obj).each(function() {
					    $(this).attr("checked", false);
					});

						$('select.form-filter.material-select', main_obj).each(function(){
						$(this).prop('selectedIndex', 0);
						$(this).material_select();   
					});
					
					$('select.form-filter.selectize', main_obj).each(function(){
						var selectize = $(this)[0].selectize;
						selectize.clear();   
						$(this).val('');
					});

					search_params 				= {};

					if( search_func )
		            {
		            	search_params 	= eval(search_func);
		            }

		             if( tableD_obj )
			        {

			        	tableD_obj.state.clear();
			        }

					my_table.DataTable().ajax.reload();

					$('.tooltipped').tooltip('remove');
				});
			}
		}

		var table 	= {
			table_id : function()
			{
				return table_obj.DataTable( options );
			}
		}

		tableD_obj 	= table.table_id();

		if( func_callback != "" )
		{
			eval( func_callback );
		}

		if( default_setting.advanced_filter )
		{
			/*
			 * FILTERING THRU PRESSING ENTER ON FILTER INPUTS
			 * ADDED BY JAB
			 */
			table_obj.find('.form-filter').bind('keyup', function(e)
			{
				if(e.keyCode == 13)
				{
					table_obj.find('.filter-submit').trigger('click');
				}
			});

		 	table_obj.on('click', '.filter-submit', function(e) {
		        e.stopImmediatePropagation();
		        search_params['action'] 	= 'filter';

		         $('textarea.form-filter-outside, select.form-filter-outside, input.form-filter-outside:not([type="radio"],[type="checkbox"])').each(function() {
	                search_params[$(this).attr("name")] = $(this).val();
	            });

	        	$('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', table_obj).each(function() {
		        	search_params[$(this).attr("name")] = $(this).val();
		        });

	        	  // get all checkboxes
	            $('input.form-filter[type="checkbox"]:checked', table_obj).each(function() {
	                search_params[$(this).attr("name")] = $(this).val();
	            });

	            $('input.form-filter-outside[type="checkbox"]:checked').each(function() {
	                search_params[$(this).attr("name")] = $(this).val();
	            });

	            // get all radio buttons
	            $('input.form-filter[type="radio"]:checked', table_obj).each(function() {
	                search_params[$(this).attr("name")] = $(this).val();
	            });

	             $('input.form-filter-outside[type="radio"]:checked').each(function() {
	                search_params[$(this).attr("name")] = $(this).val();
	            });

	            if( search_func )
	            {
	            	search_params 	= eval(search_func);
	            }

	            if( tableD_obj )
		        {

		        	tableD_obj.state.clear();
		        }

		        table.table_id();

		        $('.tooltipped').tooltip('remove');
		    });

	     	table_obj.on('click', '.filter-cancel', function(e) {
	            
	            e.stopImmediatePropagation();

	         	search_params['action'] 	= 'filter_cancel';
	            
	     	 	$('textarea.form-filter, select.form-filter, input.form-filter', table_obj).each(function() {
	                $(this).val("");
	            });

	            $('input.form-filter[type="checkbox"]', table_obj).each(function() {
	                $(this).attr("checked", false);
	            });

             	$('select.form-filter.material-select', table_obj).each(function(){
	            	$(this).prop('selectedIndex', 0);
	    			$(this).material_select();   
        		});

        		$('select.form-filter.selectize', table_obj).each(function(){
	            	var selectize = $(this)[0].selectize;
	    			selectize.clear();   
	    			$(this).val('');
        		});

	            search_params 				= {};

	            if( search_func )
	            {
	            	search_params 	= eval(search_func);
	            }

             	if( tableD_obj )
		        {

		        	tableD_obj.state.clear();
		        }

	            table.table_id();

	            $('.tooltipped').tooltip('remove');
	        });
	 	}

	 	if( delete_remove_filter )
	 	{
 			$('textarea.form-filter, select.form-filter, input.form-filter', table_obj).each(function() {
                $(this).val("");
            });

          	$('input.form-filter[type="checkbox"]', table_obj).each(function() {
                $(this).attr("checked", false);
            });

         	$('select.form-filter.material-select', table_obj).each(function(){
            	$(this).prop('selectedIndex', 0);
    			$(this).material_select();   
    		});

    		$('select.form-filter.selectize', table_obj).each(function(){
            	var selectize = $(this)[0].selectize;
    			selectize.clear();   
    			$(this).val('');
    		});
	 	}

 	}
 	catch(main_err) 
 	{
 		notification_msg('error', main_err);
		console.log(main_err);
	}
}

function create_avatar( obj, settings )
{
	var my_setting = $.extend( {
		name: 'Name',
        seed: 0,
        charCount: 1,
        textColor: '#ffffff',
        height: 100,
        width: 100,
        fontSize: 60,
        fontWeight: 400,
        fontFamily: 'HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial,Lucida Grande, sans-serif',
        radius: 0
	}, settings );

	if( obj.initial !== undefined )
	{
		obj.initial( my_setting );
	}
}

function parsley_listener_duplicate()
{
	window.Parsley.on('field:validated', function(ParsleyForm) 
	{
		var field_ins 	= [];
		var field_check = [];

		if( ParsleyForm.parent !== undefined )
		{
 
			for(var field in ParsleyForm.parent.fields)
			{
				var fieldInstance = ParsleyForm.parent.fields[field];

				if( fieldInstance.constraintsByName !== undefined && 
	        		Object.keys( fieldInstance.constraintsByName ).length !== 0 && 
	        		"unique" in fieldInstance.constraintsByName &&
	        		fieldInstance.$element !== undefined
	        	)
	    		{
	    			field_check.push( fieldInstance.isValid() );
	    			field_ins.push( fieldInstance );
	    			
	    		}
			}
            
			if( field_check.length !== 0  
				&& field_check.indexOf(false) === -1
				&& field_ins.length !== 0
			)
			{
				var f_len 	= field_ins.length,
					f_i 	= 0; 

				for( ; f_i < f_len; f_i++ )
				{
					field_ins[ f_i ].reset();
				}
			}
		}
            
	});
}