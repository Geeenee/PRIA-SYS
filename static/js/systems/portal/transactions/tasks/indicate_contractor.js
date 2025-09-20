var IndicateContractor = (function($, window, document){

    var init    = function()
    {  
        var selectedValue = [];
        var options = {
            'btn_id'            :   'btn-add-contractor',
            'tbl_id'            :   'tbl-contractor',
            'before_copy_row'   :   function(row_index,self,tbl,tbl_copy){
				var rows = $('#tbl-contractor').find('select.selectize'),
				i = 0,
				len;
				if(rows.length !== 0)
				{
					len = rows.length;

					for(i = 0;i < len;i++){

						var select_id = ($(rows[i]).attr('id') === undefined)? i:$(rows[i]).attr('id'),
							parent = i;

						selectedValue[parent+'_'+select_id] = rows[i].selectize.getValue();
						
						rows[i].selectize.destroy();
						
						$(rows[i]).val('');
					}
				}
				
				var tbl_row = $('#tbl-contractor').find('tr');
            },
            'elem_to_mod'       : ['input', 'select', 'a'],
            'each_elem_mod'     : function( obj, row_index, remove_func, tbl_id, args )
			{
				var tbl_row = $('#tbl-contractor').find('tr');

				row_index = tbl_row.length;

                if( obj.hasClass('hide') )
	            {
	                obj.removeClass('hide');
                }
                
				if(obj.hasClass('category'))
				{
					obj.attr('name', 'boq_category_'+row_index+'[]');
					obj.attr('id', 'boq_category_'+row_index);
					obj.attr('data-parsley-multiple', 'boq_category_'+row_index+'[]');
				}

                if(obj.hasClass('vendor'))
                    obj.attr('name', 'vendor_'+row_index+'[]');    
            },
            'after_copy_row' : function(row_index,par_btn,_for_remove,tbl_id,args){
				var rows = $('#tbl-contractor').find('select.selectize').selectize({
					onInitialize 	: function()
					{
						var s = this;
						
						this.revertSettings.$children.each(function() 
						{
							$.extend(s.options[this.value], $(this).data());
						});
					}
				}),
				i = 0,
				len = rows.length, 
				j = 0;

				var something_sel 	= {};
				
				for(i = 0;i < len;i++)
				{
					if( rows[i].selectize !== undefined)
					{
						var select_id = ( $(rows[i]).attr('id') === undefined)? i: $(rows[i]).attr('id'),
							parent_id = i;

						cache_sel 	= true;

						rows[i].selectize.setValue( selectedValue[parent_id +'_'+ select_id]);

						$(rows[i]).val(selectedValue[parent_id +'_'+ select_id]);
					}
				}
			}
        };

        add_rows(options);

        const btnDeleteRow = document.querySelector('#tbody-contractor');

        btnDeleteRow.addEventListener('click', function(event){
			const targetElem = event.target;
			const parentElem = targetElem.parentElement;

			if(parentElem.classList.contains('delete'))
				parentElem.closest('tr').remove();
		});

    };


    return {
        init
    };

}(jQuery, window, document));