var Contract = function() {
    
    var $transactions   = "transactions";
    var $module         = "contract";
    
    var save = function()
    {   
        $("#submit_modal_add_contract").off('click').on("click", function(e){
            e.preventDefault();

            if($('#form_modal_add_contract').parsley().validate())
            {
                var data    = $("#form_modal_add_contract").serialize();

                button_loader('submit_modal_add_contract', 1);

                $.post($base_url + $transactions + "/" + $module + "/contract/process", data, function(result)
                {
                    notification_msg(result.status, result.msg);

                    if(result.flag == '1'){
                        contract_file_uploadObj.startUpload();

                        $("#modal_add_contract").modal("close");
                        
                        $("#tbl_contracts").DataTable().ajax.reload();
                    }
                    button_loader('submit_modal_add_contract', 0);
                }, 'json');
            }
        });

        $("#submit_modal_edit_contract").off('click').on("click", function(e){
            e.preventDefault();

            if($('#form_modal_edit_contract').parsley().validate())
            {
                var data    = $("#form_modal_edit_contract").serialize();

                button_loader('submit_modal_edit_contract', 1);

                $.post($base_url + $transactions + "/" + $module + "/contract/process", data, function(result)
                {
                    notification_msg(result.status, result.msg);

                    if(result.flag == '1')
                    {
                        $("#modal_edit_contract").modal("close");
                        
                        $("#tbl_contracts").DataTable().ajax.reload();

                    }
                    
                    button_loader('submit_modal_edit_contract', 0);
                
                }, 'json');
            }
        });
    }

    var successCallback = function(files,data,xhr,pd)
    {
        var form;

        form    = $("#form_modal_add_contract");

        var post_data    = form.serialize();
            post_data   += '&upd_attach=1';
            post_data   += '&filename=' + files[0];
            post_data   += '&sysfilename=' + data[0];

        $.post($base_url+'transactions/contract/Contract/save_document', post_data, function(result){
                    
            response = JSON.parse( result );

           // console.log(response, response.status);

            notification_msg(response.status, response.msg);
            //if(response.status == $.CONSTANTS.SUCCESS)

        }, 'json');
    }
    
    return {        
        save : function()
        {
            save();
        },
        successCallback        : function(files,data,xhr,pd)
        {
            successCallback(files,data,xhr,pd);
        }
    }
}();