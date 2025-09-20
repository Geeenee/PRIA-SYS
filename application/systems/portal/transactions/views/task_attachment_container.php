<div class="row b-t-n <?php //echo ($hide) ? 'hide' : ''; ?>" id="task-attachments-section">
    <div class="col l1 m1 s1 hide-on-med-and-down"></div>

    <div class="col l11 m12 s12">
        <div class="row m-b-n m-t-md p-b-n">
            <div class="col s12 p-n">
                <h5>Additional Files</h5>
                <?php //if(in_array($task_status, [TASK_STATUS_ONGOING, TASK_STATUS_PENDING,TASK_STATUS_DONE]) && $has_approval == FALSE) {
                      if( $stage_status == PARAM_STATUS_ONGOING && $has_approval == FALSE) { ?>

                <div class="help-text">
                    <?php //if($task_status == TASK_STATUS_ONGOING OR $task_status == TASK_STATUS_DONE): 
                        if( $stage_status == PARAM_STATUS_ONGOING && $task_status != TASK_STATUS_PENDING ) :
                    ?>

                        <?php if(EMPTY($task_attachments)): ?>
                        No files are attached to this task -
                        <?php endif; ?>
                    <a class ="" href="javascript:;" onclick="modal_task_upload_init('<?php echo $pria_task_id ?>/<?php echo base64_url_encode(DOC_TYPE_TASK_ATTACHMENT) ?>','Attach File')" data-target="modal_task_upload" >Attach files to this task <!-- <i class="material-icons tiny">add</i> --> </a>
                    <?php else: ?>
                        <i>( Save task details first )</i>
                    <?php endif; ?>

                </div>

                <?php } ?>

                <?php
                   // if(EMPTY($task_attachments) && (in_array($task_status, [TASK_STATUS_RETURNED, TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED]) || $has_approval == TRUE) )
                    if(EMPTY($task_attachments) && ( $stage_status == PARAM_STATUS_COMPLETED || $has_approval == TRUE) )
                    {
                        echo '<div class="help-text"> No attached file available for this task </div>';
                    }
                    else
                    {
                        echo $task_attachments;
                    }
                ?>
            </div>
        </div>
    </div>
</div>