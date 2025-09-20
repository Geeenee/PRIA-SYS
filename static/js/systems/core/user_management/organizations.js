var Organizations = function()
{
	var $module = "user_management";
	
	var init_obj = function()
	{
		deleteObj = new handleData({ controller  : 'organizations', method : 'delete_organization', module: $module });
	}
	
	var init_modal = function()
	{
		if($("#id_organizations").val() != '')
		{
			$('.input-field label:not([for="has_parent"],[for="system_owner"])').addClass('active');
		}

		var container = $('#tbl_org_parent tbody')[0];
		var rows = container.children;

		/*var drago = {
			moves: function (el, container, handle) {
                return $(handle).hasClass('handle')
            },
            revertOnSpill: true
		};

		var drake = dragula([container], drago);*/


	//load_org_parent_table();
		
		$('#org_type_code').on('change', function(){
			var orgType 	= $(this).val(),
				label   	= $('label[for="parent_org_code"]'),
				elemParent  = document.getElementById('parent_org_code'),
				elemParent  = elemParent.selectize;

				elemParent.clear();    
				elemParent.clearOptions();    

			if(orgType != $.CONSTANTS.ORG_TYPE_ORGANIZATION)
			{
				label.addClass('required');
			}
			else
			{

				label.removeClass('required');
			}

			$.post( $base_url+ $module + '/organizations/get_org_parents', {org_type: orgType}  ).promise().done( function( response )
			{	
				result = JSON.parse(response);
				
				elemParent.addOption(JSON.parse(result.orgs));
				
				elemParent.refreshOptions(false);
			});
		});
	}

	var parent_toogle_click 	= function()
	{
		$('#has_parent').on('click', function( e ){
			e.stopImmediatePropagation();

			has_parent_toggle( $(this) );
		});
	}

	var has_parent_toggle 		= function( obj )
	{
		if( obj.is(':checked') )
		{
			$('.par_div_tbl').removeAttr('style');
		}
		else
		{
			$('.par_div_tbl').attr('style', 'display : none !important');
		}

		add_validation_org_parent(obj.is(':checked'));
	}

	var add_validation_org_parent 	= function( checked )
	{
		var rows 			= $('#tbl_org_parent').find('tbody tr'),
			i 				= 0,
			len;

		if( rows.length !== 0 )
		{
			len 		= rows.length;

			for( ; i < len; i++ )
			{
				var row_obj	= $( rows[ i ] ).find('select');

				if( checked )
				{
					row_obj.attr('data-parsley-required', 'true');
					row_obj.attr('data-parsley-trigger', 'change');
				}
				else
				{
					row_obj.removeAttr('data-parsley-required');
					row_obj.removeAttr('data-parsley-trigger');
				}

				/*row_obj.each( function(){

					if( !checked )
					{
						$(this).val('').change();
						$(this)[0].selectize.clear();
					}
				} );*/
			}
		}
	}

	var load_org_parent_table = function()
	{
		var data 	= {};

		data['org_code']	= $('#id_organizations').val();
		data['salt']		= $('#salt').val();
		data['token']		= $('#token').val();

		$.post( $base_url+ $module + '/organizations/get_org_parents_table', data  ).promise().done( function( response )
		{
			$('#tbl_org_parent').find('tbody').html(response);

			parent_toogle_click();

			has_parent_toggle($('#has_parent'));

			my_add_rows();

		});
	}

	var save = function()
	{
		$('#form_modal_organizations').parsley();
		
		$('#form_modal_organizations').off("submit.organizations").on("submit.organizations", function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			
			if ( $(this).parsley().isValid() ) {
			  var data = $(this).serialize();
				 
			  button_loader('submit_modal_organizations', 1);
			  $.post($base_url + $module + "/organizations/process", data, function(result) {
				
				notification_msg(result.status, result.msg);
				button_loader('submit_modal_organizations', 0);
				  
				if(result.status == "success"){

					if( result.org_code )
					{
						$('#id_organizations').val( result.org_code );
						$('#salt').val( result.org_salt );
						$('#token').val( result.org_token );

						if( result.org_dec && result.sess_org_code && result.path )
						{
							if( result.org_dec == result.sess_org_code )
							{
								$('.org_logo_img').attr('src', result.path);
							}
						}

						org_logo_uploadObj.startUpload();
					}


				  $("#modal_organizations").modal("close");
				  load_datatable(result.datatable_options);
				}
			  }, 'json');
			}
		});
	}

	var my_add_rows 	= function()
	{
		var selectedValue 	= {};
		var table 			= '#tbl_org_parent';
		var options 		= {
			btn_id 	: 'add_parent',
			tbl_id	: 'tbl_org_parent',
			before_copy_row : function( row_index, self, tbl, tbl_copy )
			{
				var rows 	= $( table + ' > tbody' ).find( 'select.selectize' ),
					i 		= 0,
					len;

				if( rows.length !== 0 )
				{
					len = rows.length;

					for( ; i < len; i++ )
					{
						if( rows[i].selectize !== undefined )
						{
							var select_id 	= ( $(rows[i]).attr('id') === undefined ) ? i : $(rows[i]).attr('id'),
								parent 		= $(rows[i]).parents(table + ' > tbody tr').attr('id');

							if( parent === undefined )
							{
								parent = $(table).attr('id');
							}

							selectedValue[ parent+'_'+select_id ] = rows[i].selectize.getValue()
							
							rows[i].selectize.destroy();

							$(rows[i]).val('');
						}
					}
				}
			},
			elem_to_mod : [ 'input', 'a', 'select', 'label', 'div' ],
			each_elem_mod  : function( obj, row_index, remove_func, tbl_id, args ) 
			{
				var ul_parsley 	= obj.parent().find('ul');

				obj.val('');

				if( obj.attr('value') !== undefined )
				{
					obj.removeAttr('value');
				}

				if( ul_parsley !== undefined )
				{
					ul_parsley.remove();
				}

				if( obj.hasClass('parsley-error') )
				{
					obj.removeClass('parsley-error');
				}

				if( obj.hasClass('parsley-success' ) )
				{
					obj.removeClass('parsley-success');
				}
			},
			after_remove_row : function( that, row_index )
			{
				$('.tooltipped').tooltip('remove');
				that.closest("tr").remove();
			},	
			after_copy_row : function()
			{
				$('.tooltipped').tooltip({delay: 50});
				$('#form_modal_organizations').parsley().reset();

				var rows 	= $( table + ' > tbody' ).find('select.selectize'),
					i 		= 0,
					len;

				if( rows.length !== 0 )
				{
					len = rows.length;

					$( table + ' > tbody' ).find('select.selectize').selectize({
						plugins : {
							'remove_button' : {
								className : 'remove_single'
							}
						}
					});

					for( ; i < len; i++ )
					{
						if( rows[i].selectize !== undefined )
						{
							var parent_id 	= $( rows[i] ).parents( table + ' > tbody tr' ).attr('id'),
							select_id  	=  ( $(rows[i]).attr('id') === undefined ) ? i : $(rows[i]).attr('id');

							if( parent_id === undefined )
							{
								parent_id  	= $(table).attr('id');
							}

							rows[i].selectize.setValue( selectedValue[ parent_id + '_' + select_id ] );
						}
					}
				}

				parent_toogle_click();

				has_parent_toggle($('#has_parent'));
			}
		};

		add_rows( options );
	}

	var successCallback = function(files,data,xhr,pd)
	{
		var form;

		form 		= $('#form_modal_organizations');

		var post_data 		= form.serialize();

		post_data 			+= '&upd_attach=1';

		$.post( $base_url+$module+'/Organizations/update_logo', post_data ).promise().done( function( response )
		{
			response 		= JSON.parse( response );

			if( response.flag )
			{
				reload_datatable('#'+response.table_id);

				if( response.org_code && response.sess_org_code && response.path )
				{
					if( response.org_code == response.sess_org_code )
					{
						$('.org_logo_img').attr('src', response.path);
					}
				}

			}
		} );
		
	}


	return {
		init_obj : function()
		{
			init_obj();
		},
		init_modal : function()
		{
			init_modal();
		},
		save : function()
		{
			save();
		},
		load_org_parent_table : function()
		{
			load_org_parent_table9();
		},
		parent_toogle_click 	: function()
		{
			parent_toogle_click();
		},
		my_add_rows 			: function()
		{
			my_add_rows();
		},
		has_parent_toggle 		: function( obj )
		{
			has_parent_toggle( obj );
		},
		successCallback 		: function(files,data,xhr,pd)
		{
			successCallback(files,data,xhr,pd);
		}
	}
}();