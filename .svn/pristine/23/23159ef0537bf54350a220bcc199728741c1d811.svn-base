<?php
  $state                = '';
  $skip                 = '';
  $anchor               = '';
  $line_through         = '';
  $completed_by         = '';
  $pria_task_id         = $task['pria_task_id'];
  $task_status          = strtolower($task['task_status']);
  $task_status_class    = strtolower($task['task_status']);
  $task_name            = $task['task_name'];
  $assigned_to          = ( ! EMPTY($task['actor_name'])) ? $task['actor_name'] :  $task['role_name'];
  // $assigned_to          = ( ! EMPTY($task['actor_name'])) ? $task['actor_name'] : ($finance_in_charge ? $finance_in_charge : $task['role_name']);
  $etd                  = encrypt_id($pria_task_id);

  //$path                 = base_url().PORTAL_TRANSACTIONS.'/'.$task['controller'].'?t='.base64_url_encode($pria_task_id).'&mid='.base64_url_encode($mid);

  $path                 = base_url().PORTAL_TRANSACTIONS.'/'.$task['controller'].'?t='.base64_url_encode($pria_task_id);
  $tooltip_class        = '';
  $tooltip_html         = '';
  $delay_color          = '';
  $indicator            = '';

  $task_roles           = explode(',', $task['role_codes']);

  if($has_skip OR $has_skip_prev)
  {
    $skip = <<<EOS
        <i class="material-icons task-skip">forward</i>
EOS;
  } 
  
  switch($task['task_status_id'])
  {
    case TASK_STATUS_ONGOING:
        //$class_checkbox     = ( $task['user_id'] != $this->session->user_id ) ? 'disabled cursor-default' :'tag-task-done';
        $class_checkbox     = ( $task['user_id'] != $this->session->user_id ) ? 'disabled cursor-default' :'tag-task-done';
        $state              = ( $task['user_id'] != $this->session->user_id ) ? '' :'';
        $anchor             = ( $task['user_id'] != $this->session->user_id ) ? $task_name : '';
        $skip               = ( $task['user_id'] != $this->session->user_id ) ? '' : $skip;
        $prev_skip          = '';
        //$completed_by   = 'by '.$task['actor_name'];

        //Indicator
        IF(!EMPTY($task['expected_end_date'])){

          $actual_date = date("Y-m-d");
          $expect_date = date("Y-m-d",strtotime($task['expected_end_date']));

          $delay_color = (strtotime($actual_date) > strtotime($expect_date)) ? " style='color:#e32012 !important;' " : "" ;
          $completed_by       = std_date_format($task['expected_end_date']);   
        }
        //Ends
    break;

    //Represents completed and approved
    case TASK_STATUS_APPROVED:
        $task_status_class  = 'approved';
    case TASK_STATUS_DONE:
        $class_checkbox     = 'disabled cursor-default';
        $completed_by       = std_date_format($task['actual_end_date']);    
        $state              = 'checked';
        $line_through       = 'text-line-through';
        $skip               = '';
        $prev_skip          = '';

        //$task_status_class = 'green-text';

        //Indicator
        $actual_date = date("Y-m-d",strtotime($task['actual_end_date']));
        $expect_date = date("Y-m-d",strtotime($task['expected_end_date']));

        IF(!EMPTY($task['actual_end_date']) AND !EMPTY($task['expected_end_date']))
        {

          if(strtotime($actual_date) > strtotime($expect_date)){
            $indicator  = ' returned ';
            $lens       = ' <i class="material-icons">lens</i> ';
          }else{
            $indicator  = ' approved ';
            $lens       = ' <i class="material-icons">lens</i> ';
          }

          /* $delay_color = (strtotime($task['actual_end_date']) > strtotime($task['expected_end_date'])) ? " style='color:#e32012 !important;' " : "" ; */
        }
        //Ends
    break;

    case TASK_STATUS_DISAPPROVED:
        $class_checkbox     = 'disabled cursor-default';
        $skip               = '';
        $prev_skip          = '';
      //  $task_status_class  = 'red-text';
        $state              = 'disabled';
    break;

    case TASK_STATUS_RETURNED:
        $class_checkbox     = 'disabled cursor-default';
        $skip               = '';
        $prev_skip          = '';
        $task_status_class  = 'red-text';
        //Means eto yung task na "ni return mismo".    
        $state              = ($task['returned_flag'] == ENUM_YES) ? '' : 'disabled';

        $anchor             = $task_name;

        //if( ($task['returned_flag'] == ENUM_YES && $task['user_id'] == $this->session->user_id) || $task['user_id'] == $this->session->user_id )
        if($task['user_id'] == $this->session->user_id)
            $anchor = '';
    break;

    case TASK_STATUS_SKIPPED:
        $class_checkbox     = 'disabled cursor-default';
        $completed_by       = std_date_format($task['actual_end_date']);    
        $state              = 'disabled';
        $line_through       = 'text-line-through';
        $skip               = '';
      //  $task_status_class  = 'blue-text';

        /* IF($task['actual_end_date'] != NULL AND $task['expected_end_date'] != NULL){
          $delay_color = (strtotime($task['actual_end_date']) > strtotime($task['expected_end_date'])) ? " style='color:#e32012 !important;' " : "" ;
        } */
    break;


    case TASK_STATUS_CANCELLED:
        $class_checkbox     = 'disabled cursor-default';
        $skip               = '';
        $prev_skip          = '';
      //  $task_status_class  = 'red-text';
        $anchor             = $task_name;  
        $state              = 'disabled';
        $tooltip            = 'Task has been cancelled';
    break;

    default:
        $class_checkbox = 'disabled cursor-default';
        $state          = ($has_pending) ? 'disabled' : '';
        //If true, hindi niya pwede iclick
        //$anchor         = ($has_pending == TRUE || ! in_array($task['role_code'], $this->session->user_roles) || ( ! EMPTY($task['user_id']) && $task['user_id'] != $this->session->user_id) ) ? $task_name : '';
        //$skip           = ($has_pending == TRUE || ! in_array($task['role_code'], $this->session->user_roles)) ? '' : $skip;
        
        $anchor         = ($has_pending == TRUE || ( ! EMPTY($task['user_id']) && $task['user_id'] != $this->session->user_id)  || ( EMPTY($task['user_id']) && array_intersect($task_roles, $this->session->user_roles) == false ) ) ? $task_name : '';
        $prev_skip      = ($has_skip_prev) ? $skip : '';
        $skip           = ($has_pending == TRUE || array_intersect($task_roles, $this->session->user_roles) == false) ? '' : $skip;
        
        $tooltip        = ($has_pending == TRUE) ? 'Task has dependency and cannot be started' : '';
        
        //Indicator
        IF(!EMPTY($task['expected_end_date'])){
          $actual_date = date("Y-m-d",strtotime($task['actual_end_date']));
          $expect_date = date("Y-m-d",strtotime($task['expected_end_date']));

          $delay_color  = (strtotime($actual_date) > strtotime($expect_date) ) ? " style='color:#e32012 !important;' " : "" ;
          $completed_by = std_date_format($task['expected_end_date']);   
        }
        //Ends
  }  
  
  //print_var_export($task['role_code'], $this->session->user_roles);
  //If task is completed, returned, ongoing or pending without prerequisite. Allow task to be clickable (link). Else display it as text
  if(EMPTY($anchor))
  {
    $anchor =<<<EOS
        <a class="$line_through task-name" href="$path">
            $task_name
        </a>
EOS;
 }
 else
 {
  if($has_skip_prev)
  {
    $anchor =<<<EOS
        <a class="$line_through task-name" href="javascript:void(0);">
            $task_name
        </a>
EOS;
  }
 }

 if( ! EMPTY($tooltip))
 {
    $tooltip_class = 'tooltipped';
    $tooltip_html  =<<<EOS
        data-tooltip="$tooltip" data-position="right"
EOS;
 }

 $divider = !EMPTY($completed_by) ? "&bull;" : "";
// Append this in $anchor for debugging purposes -$pria_task_id-{$task['core_workflow_task_id']}
  echo<<<EOS
            <li class="task-{$pria_task_id}">
                <div class="table-display m-b-n">
                    <div class="table-cell l3 m3 s2 valign-middle">
                        $prev_skip $skip
                        <input type="checkbox" class="labelauty rounded $class_checkbox" name="dr_chk[]" value="$etd" $state /> 
                        <div class="assignment valign-middle truncate tooltipped hide-on-small-only" data-tooltip="$assigned_to" data-position="top">$assigned_to</div>
                    </div>

                    <div class="table-cell l7 m8 s10 valign-middle">
                        <span class="$tooltip_class task-name-div" $tooltip_html>
                        $anchor-$pria_task_id-{$task['core_workflow_task_id']}
                        </span>
                        <span class="completed-by hide-on-small-only" $delay_color >$completed_by</span>
                        <div class="hide-on-med-and-up assigned-to">assigned to <b>$assigned_to</b> $divider $completed_by</div>
                        <div class="hide-on-med-and-up status m-t-sm {$task_status_class}">{$task_status}</div>
                    </div>

                    <div class="table-cell s1 hide-on-small-only valign-middle status right-align p-r-md {$task_status_class}">
                      <div class="status icon $indicator">
                        $lens
                      </div>
                    </div>

                    <div class="table-cell s1 hide-on-small-only valign-middle status {$task_status_class}">{$task_status}</div>
                </div>
            </li>
EOS;

/* if( ! EMPTY($task['appendable_workflow_name']) && $extra['display_appendable'] == TRUE)
{
    $awd = encrypt_id($task['appendable_workflow_id']);
    echo<<<EOS
        <li>
            <div class="actions">
                <a href="javascript:;" class="append-task" data-awd="{$awd}"><i class="material-icons valign-middle font-md">add</i> Add {$task['appendable_workflow_name']}</a>
            </div>
        </li>
EOS;
} */
?>