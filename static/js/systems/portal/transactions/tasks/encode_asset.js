var EncodeAsset = (function($, window, document){

    var init    = function()
    {
		var $selectize    = $('select.selectize').selectize();

		$selectize.each(function(index, value){
			//console.log(this);

			this.selectize.disable();
		});
		//$selectize[0].selectize.disable();
		//console.log($selectize);

        var selectedValue = [];
        var options = {
            'btn_id'            :   'btn-add-category',
            'tbl_id'            :   'tbl-category',
            'before_copy_row'   :   function(row_index,self,tbl,tbl_copy){
				var rows = $('#tbl-category').find('select.selectize'),
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
				
				var tbl_row = $('#tbl-category').find('tr');
            },
            'elem_to_mod'       : ['input', 'select', 'a'],
            'each_elem_mod'     : function( obj, row_index, remove_func, tbl_id, args )
			{
                if( obj.hasClass('hide') )
	            {
	                obj.removeClass('hide');
				}
				
				if( obj.attr('id') == 'asset_code' || obj.attr('id') == 'internal_order' )
					obj.val('');
            },
            'after_copy_row' : function(row_index,par_btn,_for_remove,tbl_id,args){
				var rows = $('#tbl-category').find('select.selectize').selectize({
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

        const btnDeleteRow = document.querySelector('#tbody-category');

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