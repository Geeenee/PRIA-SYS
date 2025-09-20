var uploadProjCompletion = (function(window, document, $){
    let initFileList;

    var initTask = function()
    {
        const elemFileList      = document.getElementById('completion_files');

        const selectizeFileList = elemFileList?.selectize;

        if(selectizeFileList){
            initFileList    = autoSubObj;
            autoSubObj      = [];

            selectizeFileList.on('item_add', _triggerItemAdd);
            selectizeFileList.on('item_remove', _triggerItemRemove);
        }
    }

    var _triggerItemAdd = function(val)
    {
        name = val.toLowerCase();

        if(document.getElementById(name) != null)
            autoSubObj.push(name + '_uploadObj');

        document.getElementById(name + '_container').classList.remove('hide');
    }

    var _triggerItemRemove = function(val)
    {
        const name      = val.toLowerCase();
        const updName   = name + '_uploadObj';
        const index     = autoSubObj.indexOf(updName);
        const btnCancel = document.querySelector('#' + name + ' .ajax-file-upload-cancel');

        if(index > -1 && document.getElementById(name) != null)
           autoSubObj.splice(index, 1);

        if(btnCancel)
            btnCancel.click();

        document.getElementById(name + '_container').classList.add('hide');
    }

    return {
        initTask
    };

}(window, document, jQuery));