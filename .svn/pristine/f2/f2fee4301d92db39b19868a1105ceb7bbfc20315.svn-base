var Projects = (function($, document, window){
    let btnSubmitId = 'submit_modal_project',
        btnSubmit   = document.getElementById(btnSubmitId);

    const controller_path = $base_url + 'transactions/tabs/projects/';

    var initModal = function(){
       /*  const elemProjectType = document.getElementById('project_type'),
              selProjectType  = elemProjectType.selectize;

        selProjectType.on('change', _triggerProjectType); */

        const elemSite = document.getElementById('site'),
        selSite  = elemSite.selectize;

        selSite.on('change', _triggerBoq);

        const elemBoq = document.getElementById('boq'),
        selBoq  = elemBoq.selectize;

        selBoq.on('change', _triggerProjectTypes);

        btnSubmit.removeEventListener('click', _save);
        btnSubmit.addEventListener('click', _save);
    };

    var _save = function(){
        const form      = document.getElementById('form_modal_project');
        const $parsley  = $(form).parsley();

        if($parsley.validate())
        {   
            button_loader('submit_modal_project', 1);
            
            const data = $(form).serialize();
            options    = {
                body        : data,
                path        : controller_path + 'process',
                successFunc : function(response){
                    button_loader('submit_modal_project', 0);

                    notification_msg(response.flag, response.msg);

                    if(response.flag == $.CONSTANTS.SUCCESS)
                    {
                        document.querySelector('a[href="#tab_projects"]').click();
                        
                        General.closeCurrModal();
                    }   
                }    
            };

            General.Fetch(options);
        }
    };

 /*    var _triggerProjectType = function(val){
        const elemBoq   = document.getElementById('boq'),
              selBoq    = elemBoq.selectize;

              selBoq.clear();    
              selBoq.clearOptions();    

             options    = {
                    body        : $.param({project_type: val, site: $('#site').val()}),
                    path        : controller_path + 'get_boqs',
                    successFunc : function(response){
                        selBoq.addOption(JSON.parse(response.boqs));
                        
                        selBoq.refreshOptions(false);
                    }    
            };
        
        if(val)   
            General.Fetch(options);
    } */

    var _triggerProjectTypes = function(val){
        console.log(val, 'EY');
        const elemProj   = document.getElementById('project_type'),
              selProj    = elemProj.selectize;

              selProj.clear();    
              selProj.clearOptions();    

             options    = {
                    body        : $.param({boq: val}),
                    path        : controller_path + 'get_boq_project_types',
                    successFunc : function(response){
                        selProj.addOption(JSON.parse(response.projs));
                        
                        selProj.refreshOptions(false);
                    }    
            };
        
        if(val)   
            General.Fetch(options);
    }

    var _triggerBoq = function(val){
        console.log('adfasdfasdfasdfasdf', val);
        const elemBoq   = document.getElementById('boq'),
              selBoq    = elemBoq.selectize;

              selBoq.clear();    
              selBoq.clearOptions();    

             options    = {
                    body        : $.param({site: val}),
                    path        : controller_path + 'get_boqs',
                    successFunc : function(response){
                        selBoq.addOption(JSON.parse(response.boqs));
                        
                        selBoq.refreshOptions(false);
                    }    
            };
        
        if(val)   
            General.Fetch(options);
    }

    return {
        initModal
    };

}(jQuery, document, window));