var Documents = (function($, document, window){
    let checkDocFiles = [],
        docRef        = '';

    var successCallback = function(files, data, xhr, pd, moduleCode, dtype, taskid, uid)
    {
        //hide system file name
        $('.ajax-file-upload-filename').hide();
        
        checkDocFiles.push(files[0]);

        form_data  += '&filename='+files;
        form_data  += '&sysfilename='+data;

        lastUpload  = checkDocFiles.length == autoSubObj.length ? true : false;
        
        //Damage control still can't find why files is uploaded multiple
        if(checkDocFiles.length > autoSubObj.length)
            return;

        const options = {
            body       : $.param({filename: files[0], sysfilename: data[0], doctype: dtype, docref: docRef, module_code:moduleCode, task_id:taskid, lastUpload:lastUpload}),
            path       : $base_url + 'transactions/documents/process',
            successFunc: function(response){
               
                //if(checkDocFiles.length == autoSubObj.length)
                if(lastUpload == true)
                {
                    checkDocFiles = [];

                    if(response.flag == $.CONSTANTS.SUCCESS)
                    {
                       location.reload();
                    }
                    else
                    {
                        end_loading();

                        notification_msg(response.flag, response.msg);
                    }
                }    
            }
        };
        
        General.Fetch(options);
    };


    return {
        successCallback,
        set : (ref) => docRef = ref
    };

}(jQuery, document, window));