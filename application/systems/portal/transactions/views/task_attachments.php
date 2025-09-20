<?php

    foreach($attachments as $task_attachment):

        $document_id            = ( ISSET($task_attachment['document_id'])) ? base64_url_encode($task_attachment['document_id']) : '';
        $reference              = ( ISSET($task_attachment['reference'])) ? base64_url_encode($task_attachment['reference']) : '';
        $document_type_code     = ( ISSET($task_attachment['document_type_code'])) ? base64_url_encode($task_attachment['document_type_code']) : '';
        $file_name              = ( ISSET($task_attachment['file_name']) ) ? ($task_attachment['file_name']) : '';
        $version              = ( ISSET($task_attachment['version']) ) ? ($task_attachment['version']) : '';
        $sys_file_name          = ( ISSET($task_attachment['sys_file_name']) ) ? ($task_attachment['sys_file_name']) : '';
        $created_date           = ( ISSET($task_attachment['modified_date']) ) ? std_db_datetime_format($task_attachment['modified_date']) : std_db_datetime_format($task_attachment['created_date']);

        if(!EMPTY($other_doc['modified_by'])){
            $user_info        = $this->users_model->get_user_details($task_attachment['modified_by']);
        }else{
            $user_info        = $this->users_model->get_user_details($task_attachment['created_by']);
        }   

        $created_by       = (ISSET($user_info['fname']) AND $user_info['lname']) ? $user_info['fname'].' '.$user_info['lname'] : '';

        //$created_by             = (ISSET($user_info['fname']) AND $user_info['lname']) ? $user_info['fname'].' '.$user_info['lname'] : '';
        $file_ext               = explode('.', $task_attachment['file_name']);

        $path = PATH_UPLOADED_FILES.$sys_file_name;
        $path = str_replace(array('\\','/'), array(DS,DS), $path);

        if( file_exists( $path ) )
        {
            $file_size          = file_size_convert( filesize( $path ) );
        }else{
            $file_size          = '0 KB';
        }


        $dl_path 		= base_url().'pria_file/download?file='.$sys_file_name;
        $vw_path 		= base_url().'pria_file/view?file='.$sys_file_name;
?>

    <div class="file-wrapper <?php echo end($file_ext) ?>" id="attachment-<?php echo $task_attachment['document_id']; ?>">
        <div class="type" data-file-type="<?php echo end($file_ext) ?>"></div>
        <div class="contents valign-top">
            <div class="filename truncate"><?php echo $file_name ?></div>
            <span class="red-text"><b><?php echo 'v'.$version ?></b></span><br>
            uploaded <?php echo $created_date; ?> by <?php echo $created_by; ?> <?php echo $file_size ?>
        </div>
        <div class="actions valign-middle right-align">
            
            <a target="_blank" href="<?php echo $vw_path; ?>" >
                <button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="View"><i class="material-icons valign-middle ">search</i></button>
            </a>

            <?php if($download_per): ?>
            <a target="_blank" href="<?php echo $dl_path; ?>" download>
                <button type="button" id="download" class="tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons valign-middle">file_download</i></button>
            </a>
            <?php endif; ?>

         
            <?php if($task_attachment['created_by'] == $this->session->user_id && $task_attachment['initial_upload'] == ENUM_YES && $hide == FALSE){ ?>

                <button type="button" id="delete" onclick="content_delete('attachment','<?php echo $document_id; ?>')" class='tooltipped' data-tooltip='Delete' data-position='top' data-delay='50'><i class="material-icons valign-middle">delete</i> </button>
           <?php } ?>

            <?php if($task_attachment['initial_upload'] == ENUM_NO){ ?>
                <button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="New Version" onclick="modal_file_version_init('<?php echo $reference; ?>/<?php echo $document_type_code; ?>/<?php echo $document_id; ?>', 'File Version')" data-target="modal_file_version"><i class="material-icons valign-middle">add</i></button>
            <?php } ?>                 
        </div>
    </div>

<?php endforeach; ?>

