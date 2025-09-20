var SiteNominations = (function($, document, window){
    const controller_path = $base_url + 'transactions/tabs/site_nominations/',
          btnNominateId   = 'submit_modal_site_nomination';

    var initModal = function(){
        const btnNominate = document.getElementById(btnNominateId);

        btnNominate.removeEventListener('click', save);
        btnNominate.addEventListener('click', save);
    }

    var save = function()
    {
        const $form     = $('#form_modal_site_nomination');
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
                        document.querySelector('a[href="#tab_site_nominations"]').click();
                        
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
        initModal
    };

}(jQuery, document, window));