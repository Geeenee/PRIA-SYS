const Apv = function () {
	const init_apv = (type, ref_num) => {

		if(type == $.CONSTANTS.REF_B_APV)
		{
			$('#tbl_apvs').find('input[type=text][name=apv_num]').val(ref_num);
		}
		else if(type == $.CONSTANTS.REF_B_CV)
		{
			$('#tbl_apvs').find('input[type=text][name=cv_num]').val(ref_num);
		}

		setTimeout(() => {$('#tbl_apvs').find('.table-actions').find('.filter-submit').trigger('click');}, 300);
	}

	return {		
		init : (type, ref_num) => {
			init_apv(type, ref_num);
		}
	}
}();