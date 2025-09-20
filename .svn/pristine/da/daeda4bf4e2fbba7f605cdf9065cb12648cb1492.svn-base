<?php

$extra_msg      = '';
$append_anchor  = '';

if(ISSET($stage['appendable_workflow_id']))
{
    $pwid                   = base64_url_encode($stage['pria_workflow_id']);
    $tid                    = base64_url_encode($stage['pria_task_id']);
    $core_workflow_id       = base64_url_encode($stage['core_workflow_id']);
    $core_workflow_stage_id = base64_url_encode($stage['core_workflow_stage_id']);
    $append_workflow_id     = base64_url_encode($stage['appendable_workflow_id']);
    $append_workflow_name   = $stage['appendable_workflow_name'];
    
    $append_anchor          = <<<EOS
        <a href="javascript:;" data-tid="$tid" data-pwid="$pwid" data-wid="$core_workflow_id" data-wsid="$core_workflow_stage_id" data-awi="$append_workflow_id" data-wname="$append_workflow_name" class="a-subtask add-subtask"><i class="material-icons valign-middle font-md">add</i> Add $append_workflow_name</a>
EOS;
}

if( ! EMPTY($append_note))
{
    $extra_msg = '<span class="m-l-sm font-sm red-text task-note">( '.$append_note.' )</span>';
}

echo <<<EOS
        <ul class="list-nested m-l-md m-r-md">
            <li class="title p-b-md p-t-md">
                <h5>
                    <div class="stage-title inline">
                        {$stage['stage_name']} $extra_msg
                    </div>

                    <div class="actions right-align inline add-action-link">
                        $append_anchor       
                    </div> 
                </h5>

                <ul class="list-task">  
                    {$tasks}
                    
                </ul>
            </li>
        </ul>
EOS;
?>


