<div class="row p-n b-t-n <?php //echo ($hide) ? 'hide' : ''; ?>" id="task-comments-section">
    <div class="col l1 m1 s1 hide-on-med-and-down"></div>
    <div class="col l11 m12 s12 b-t b-dashed p-t-md" style="border-color:#e5e5e5!important">
        <div class="input-field">
                <h5>Comments </h5>

            <?php if( 
                        //( in_array($task_status, [TASK_STATUS_PENDING])  == FALSE || $task_status == TASK_STATUS_RETURNED && $returned_flag == ENUM_YES ) && $has_approval == FALSE
                          $workflow_status == PARAM_STATUS_ONGOING  && $has_approval == FALSE
                   ){ 
            ?>
                <div id="task-comment-form" class="row">
                    <div class="comment-section">                   
                        <div class="comment-avatar">
                            <?php
                                //$img_path = PATH_USER_UPLOADS.$this->session->photo;
                                $img_path = PATH_USER_UPLOADS;
                        
                                $options  = ['class' => 'responsive-img circle ', 'style' => 'width:55px; height:55px;'];

                                create_img_tag($this->session->photo, $img_path, $options, FALSE);
                            ?>
                        </div>
                        
                        <div class="comment-contents">
                            <textarea id="task-comment" class="materialize-textarea" name="task-comment" placeholder="Enter Comments"></textarea>

                            <div class="right-align m-t-sm">
                                <button type="button" class="btn black-text grey lighten-2 hide" id="btn-cancel-comment" >Cancel</button>
                                <button type="button" class="btn save-submit" id="btn-save-comment" data-btn-action="Saving" <?php echo $task_status == TASK_STATUS_PENDING ?  'disabled' : ''; //echo ($task_status == TASK_STATUS_ONGOING) ? '' : 'disabled'; ?>>Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        
                <div id="task-comments-container">
                    <?php //echo ( ! EMPTY($task_comments)) ? $task_comments : 'No comments available for this task.'; 
                        if(  EMPTY($task_comments) 
                             && ( 
                                    //in_array($task_status, [TASK_STATUS_RETURNED, TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_SKIPPED, TASK_STATUS_DISAPPROVED]) || 
                                  /*   in_array($task_status, [TASK_STATUS_PENDING]) ||  */
                                    $workflow_status == PARAM_STATUS_COMPLETED || 
                                    $has_approval == TRUE 
                                ) 
                          )
                        {
                            echo '<div class="help-text">No comments available for this task</div>';
                        }
                        else
                        {
                            echo $task_comments;
                        }
                    ?>
                </div>
        </div>
    </div>
</div>
                    
