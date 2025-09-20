var $base_url	= $("#base_url").val();

var Datasheet = function()
{
	var checkbox_class = '.ind_checkbox',
		check_all = '.check_all';
		
	var init = function(arr)
	{
		$.each(arr, function (index, value) {
			var table_id = "#table_" + index;
				
			$(document).on("change", checkbox_class + "_" + index, function(){
				var element = $(this),
					checked = $(this).prop('checked');
				
				if(checked)
					checkSelected(index, table_id);
				else
					$(check_all + "_" + index).prop('checked', false);
			});
			
			var i=0;
			$(document).on('click', table_id + ' td', function(){
				var id = table_id,
					element = id + ' td',
					active_cell = $(this),
					cell_index = active_cell.index();
				
				$(element).not(active_cell).removeClass('focus');
				active_cell.not('.row-ctr, .row-action').addClass('focus');
				
				$(this).prevAll('td.row-ctr').toggleClass('focus');
				if(i != cell_index){
					$( id + ' th:not(:eq(' + cell_index + '))' ).removeClass('focus');
				}
				$( id + ' th:eq(' + cell_index + ')' ).not('.row-ctr, .row-action').addClass('focus');
				
				var i = cell_index;
			});
		});
	}
	
	var addRow = function(arr)
	{
		$.each(arr, function (index, value) {
			var i = 2,
				div_id = '#' + index,
				table_id = '#table_' + index,
				module = module,
				controller = controller,
				method = method;
				
			$(div_id + " .addmore").on('click',function(){
				if(typeof(value.scrollable) != "undefined" && value.scrollable == true) {
					var count = $(table_id + ' tr').length + 1;
				}else if(typeof(value.calculate_input) != "undefined" && value.calculate_input !== null) {
					var count = $(table_id + ' tr').length - 1;
				}else{
					var count = $(table_id + ' tr').length;
				}
				
				var data = "row_count=" + count + "&id_count=" + i;
				
				$(this).prop('disabled', true);
				$.post($base_url + value.module + "/" + value.controller + "/" + value.method, data, function(result){
					$(table_id + " tbody ").fadeIn().append(result);
					$(check_all + "_" + index).prop('checked', false);
					$(div_id + " .addmore").prop('disabled', false);
				});
				i++;
			});
		});
		
	}

	var deleteRow = function(arr)
	{
		$.each(arr, function (index, value) {
			var div_id = "#" + index,
				table_id = "#table_" + index;
			
			$(div_id + " .delete").on("click", function() {
				var tr_min_length = 0;
				$(checkbox_class + "_" + index + ":checkbox:checked").parents("tr").remove();
				$(check_all + "_" + index).prop("checked", false); 
				check(table_id);
				
				if(typeof(value.calculate_input) != "undefined" && value.calculate_input !== null) {
					
					if(typeof(value.scrollable) != "undefined" && value.scrollable == true) {
						var tr_min_length = 0;
					} else {
						var tr_min_length = 2;
					}
					
					$.each(value.calculate_input, function (index, value) {
						calculateSum(index, value.sum, value.type, value.decimal_places);
					});
				}
				console.log($(table_id + " tr").length);
				if($(table_id + " tr").length == tr_min_length)
				{
					$(div_id + " .addmore").trigger( "click" );
				}
			});
		});
	}
	
	var checkSelected = function(index, table_id)
	{
		// CHECK IF ALL CHECKBOXES ARE SELECTED
		if ($(checkbox_class + "_" + index + ':checked').length == $(checkbox_class + "_" + index).length) {
			// SELECT CHECK_ALL CHECKBOX
			$(check_all + "_" + index).prop("checked", true);
		}else{
			// DISSELECT CHECK_ALL CHECKBOX
			$(check_all + "_" + index).prop("checked", false);
		}
	}
	
	var selectAll = function(index)
	{
		$('input[class=ind_checkbox_'+ index +']:checkbox').each(function(){ 
			if($('input[class=check_all_'+ index +']:checkbox:checked').length == 0){ 
				$(this).prop("checked", false); 
			} else {
				$(this).prop("checked", true); 
			} 
		});
	}
	
	var check = function(table_id)
	{
		obj = $(table_id + ' tr').find('span');
		$.each( obj, function( key, value ) {
			id = value.id;
			$('#'+id).html(key+1);
		});
	}
	
	var inputCompute = function(arr)
	{
		$.each(arr, function (index, value) {
			$.each(value.calculate_input, function (index, value) {
				$(document).on('keydown keyup', "." + index, function(){
					calculateSum(index, value.sum, value.type, value.decimal_places);
					
					if(typeof(value.product) != "undefined" && value.calculate_input !== null)
					{
						var val = this.value.replace(/[^\d\.\-\ ]/g, ''),
							id = this.id;
							
						calculateProduct(val, id, value.multiplier, value.product);
					}
					if(typeof(value.product_total) != "undefined" && value.calculate_input !== null)
					{
						calculateSum(value.product, value.product_total, value.type, value.product_total_decimal);
					}
				});
			});
		});
	}
	
	var calculateProduct = function(val, id, multiplier_class, product_class)
	{
		var prod = 0,
			multiplier_id = $("#" + id).parent().parent().find("." + multiplier_class).attr("id"),
			multiplier_val = $("#" + multiplier_id).val().replace(/[^\d\.\-\ ]/g, ''),
			product_field = $("#" + id).parent().parent().find("." + product_class).attr("id");
			
			
		if (!isNaN(val) && val.length != 0) {
			prod = parseFloat(multiplier_val) * parseFloat(val);
		}
		$("#" + product_field).val(prod);
	}
	
	var calculateSum = function(input, sum_field, type, decimal_places)
	{
		var sum = 0,
			decimal_places = decimal_places || "";
		
		$("." + input).each(function() {
			var value = this.value.replace(/[^\d\.\-\ ]/g, '');
			
			if (!isNaN(value) && value.length != 0) {
				sum += parseFloat(value);
			}
		});
		
		switch(type)
		{
			case 'form-element':
				var method = 'val';
			break;
			
			case 'script-element':
				var method = 'html';
			break;
			
			case 'text':
				var method = 'text';
			break;
		}
		$("#" + sum_field)[method](sum.toFixed(decimal_places).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
	}
	
	var highlightOption = function(form, id)
	{
		$('tr', '#' + form).removeClass('row-active');
		$("#" + id).parent().parent().addClass('row-active');
	}
	
	return {
		init : function(arr)
		{
			init(arr);
		},
		addRow 	: function(arr)
		{
			addRow(arr);
		},
		deleteRow 	: function(arr)
		{
			deleteRow(arr);
		},
		checkSelected : function(index, table_id)
		{
			checkSelected(index, table_id);
		},
		selectAll : function(index)
		{
			selectAll(index);
		},
		check : function(table_id)
		{
			check(table_id);
		},
		inputCompute : function(arr)
		{
			inputCompute(arr);
		},
		calculateSum : function(input, sum_field, type, decimal_places)
		{
			calculateSum(input, sum_field, type, decimal_places);
		},
		calculateProduct : function(val, id, multiplier_class, product_class)
		{
			calculateProduct(val, id, multiplier_class, product_class);
		},
		highlightOption : function(form, id)
		{
			highlightOption(form, id);
		}
	}

}();