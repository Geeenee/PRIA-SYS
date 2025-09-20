var CDIPayment = (function($, document, window){
    const controller_path = $base_url + 'transactions/tabs/cdi_payments/',
          btnNominateId   = 'submit_modal_cdi_payment';

    var action = function()
    {
        $("#bom_id").off('change').on("change", function(e){
            e.preventDefault();

            var bom_id = $(this).val();

            $.post(controller_path + "/get_bom_details", {bom_id : bom_id}, function(result){
            
                $('#bom_approval_reference_no').text(result.bom_info.bom_num);
                $('#business_center').text(result.bom_info.org_name);
                $('#business_center_field').val(result.bom_info.org_code);

            }, 'json');
        });
    }

    var initModal = function(){
        const btnNominate = document.getElementById(btnNominateId);

        btnNominate.removeEventListener('click', save);
        btnNominate.addEventListener('click', save);
    }

    var save = function()
    {
        
        const $form     = $('#form_modal_cdi_payment');
        const $parsley  = $form.parsley();

        if($parsley.validate())
        {
            button_loader(btnNominateId, 1);
                
            const options = {
				body        : $form.serialize(),
				path        : controller_path + 'process',
				successFunc : function(response){
                    button_loader(btnNominateId, 0);

                    notification_msg(response.flag, response.msg);

                    if(response.flag == $.CONSTANTS.SUCCESS)
                    {
                        document.querySelector('a[href="#tab_payment"]').click();
                        General.closeCurrModal();
                    }   
                }    
			};

			General.Fetch(options);
        }   
        else
        {
            console.log('else');
        }
    }

    return {
        initModal,
        action
    };

}(jQuery, document, window));