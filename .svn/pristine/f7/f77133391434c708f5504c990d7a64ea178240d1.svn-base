var Boq = (function($, document, window){
    let elemRecomContractor, elemNewContractor, $form, $parsley, btnSubmit;

    const controller_path = $base_url + 'transactions/tabs/boq/';

    var initModal = function(){
        const elemThirdParty        = document.getElementById('third_party');
              elemRecomContractor   = document.getElementById('recommended_contractor');
              elemNewContractor     = document.getElementById('new_contractor');
              btnSubmit             = document.getElementById('submit_modal_boq');
              $form                 = $('#form_modal_boq');
              $parsley              = $form.parsley();

        elemRecomContractor.selectize.disable();

        elemThirdParty.removeEventListener('click', _triggerThirdParty);
        elemThirdParty.addEventListener('click', _triggerThirdParty);

        elemRecomContractor.selectize.off('change', _triggerContractor);
        elemRecomContractor.selectize.on('change', _triggerContractor);

        btnSubmit.removeEventListener('click', _save);
        btnSubmit.addEventListener('click', _save);
    };

    var _save = function()
    {
        if($parsley.validate())
        {
            button_loader('submit_modal_boq', 1);

            const options = {
				body        : $form.serialize(),
				path        : controller_path + 'process',
				successFunc : function(response){

                    button_loader('submit_modal_boq', 0);

                    notification_msg(response.flag, response.msg);

                    if(response.flag == $.CONSTANTS.SUCCESS)
                    {
                        document.querySelector('a[href="#tab_boq"]').click();
                        
                        General.closeCurrModal();
                    }   
                }    
			};

            General.Fetch(options);
        }
    }

    var _triggerThirdParty = function(event)
    {
        const isChecked = event.target.checked;
        
        if(isChecked)
        {
            elemRecomContractor.selectize.enable();
            elemRecomContractor.dataset.parsleyRequired = true;
        }    
        else
        {
            elemRecomContractor.selectize.disable();    
            elemRecomContractor.dataset.parsleyRequired = false;
        }    

        $(elemRecomContractor).parsley().reset();
    }

    var _triggerContractor = function(value)
    {
        if(value == 'new')
        {
            elemNewContractor.disabled = false;
            elemNewContractor.dataset.parsleyRequired = true;
        }  
        else    
        {
            elemNewContractor.disabled = true;
            elemNewContractor.dataset.parsleyRequired = false;
        }    

        $(elemNewContractor).parsley().reset();
    }

    return {
        initModal
    };

}(jQuery, document, window));