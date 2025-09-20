var Renewal = function() {
    
    var $transactions   = "transactions";
    var $module         = "renewal";
    
    var save = function()
    {
        $("#submit_modal_add_renewal").off('click').on("click", function(e){
            e.preventDefault();

            button_loader('submit_modal_add_renewal', 1);
            
            if($('#form_modal_add_renewal').parsley().validate())
            {
                var data    = $("#form_modal_add_renewal").serialize();

                $.post($base_url + $transactions + "/" + $module + "/renewal/process", data, function(result){
                    
                    if(result.status == $.CONSTANTS.SUCCESS){
                        notification_msg(result.status, result.msg);

                        window.location.reload(true);

                        $("#modal_add_renewal").modal("close");
                    }else{
                        notification_msg(result.status, result.msg);
                    }
                    
                    button_loader('submit_modal_add_renewal', 0);
                }, 'json');
            }
            else
            {
                console.log('else');
                button_loader('submit_modal_add_renewal', 0);
            }
        });
    }

    var store = function()
    {
        $("#store_name").off('change').on("change", function(e){
            e.preventDefault();

            var store_id = $(this).val();

            $.post($base_url + $transactions + "/" + $module + "/renewal/get_store_details", {store : store_id}, function(result){
            
                console.log(result.store_info);
                //Show Contract Number
                $('#contract_reference').html(result.store_info.contract_code);

                $('#reference_contract').html(result.store_info.new_contract);

                //Show Lessor
                $('#lessor').html(result.store_info.vendor_name);

                //Show Contract ID
                $('#contract_id').val(result.store_info.contract_id);
            }, 'json');
        });
    }
    
    return {
        save : function()
        {
            save();
        },
        store : function()
        {
            store();
        }
    }
}();