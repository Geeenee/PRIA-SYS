<?php

    $html = '';
    $cnt  = ( ISSET($counter) ) ?  $counter : 0;
    $with_datatable = ISSET($with_datatable) ? $with_datatable : FALSE;
   //$cnt = 0;
   //Remove in .task-append data-sec="$hashed_sec"
  /*
   <a class="more-task-actions dropdown-trigger" data-activates="task-actions-$cnt">
                            <i class=" material-icons">more_vert</i>
                        </a>

     <ul id="task-actions-$cnt" class="dropdown-content">
                            <li><a class="task-append" data-target="modal_append_task"><i class="material-icons">note_add</i>Add Task</a></li>
                        </ul> 
  */
    
    if(!EMPTY($list)){
        
	foreach($list as $val)
	{
        $hashed_id  = encrypt_id($val['reference_id']);
        $hashed_mid = ISSET($mod_code) ? encrypt_id($mod_code) : '';
        $hashed_pwi = encrypt_id($val['pria_workflow_id']);

        $padding    = "";
            $btns   = "";

        if(is_array($addtl_actions) AND count($addtl_actions) > 0)
        {
            foreach($addtl_actions AS $aa_key => $aa)
            {
                $label      = (ISSET($aa['label']) && !EMPTY($aa['label']))? $aa['label'] : '';
                $icon       = (ISSET($aa['icon']) && !EMPTY($aa['icon']))? $aa['icon'] : 'add';
                $class      = (ISSET($aa['class']) && !EMPTY($aa['class']))? $aa['class'] : '';
                $target     = (ISSET($aa['target']) && !EMPTY($aa['target']))? 'data-target="'.$aa['target'].'"': '';
                $onclick    = (ISSET($aa['onclick']) && !EMPTY($aa['onclick']))? 'onclick="'.$aa['onclick'].'"'   : '';
                $onclick    = sprintf($onclick, $hashed_id);

                $created_by     = (ISSET($val['created_by']) AND !EMPTY($val['created_by']))? $val['created_by']: NULL;
                $status_code    = (ISSET($val['status_code']) AND !EMPTY($val['status_code']))? $val['status_code']: NULL;

                if($created_by == $this->session->user_id AND $status_code != STATUS_COMPLETED)
                {
                    $btns   .=<<<EOS
                        <a href="javascript:void(0);" {$target} data-tooltip="{$label}" class="{$class} pull-right purple-text tooltipped" {$onclick}>
                            <i class="material-icons">{$icon}</i>
                        </a>
EOS;
                }
            }

            $padding    = "p-r-lg";
        }

        $html      .=<<<EOS
        <li class="toggle-task" data-id="$hashed_id" data-pwi="$hashed_pwi" data-mid="$hashed_mid" >
            <div class="collapsible-header">
                <div class="row m-b-n">
                    <div class="col l2 m2 s1 toggle-ref-num">
                        <span class="toggle tooltipped" data-tooltip="Expand List"></span>
                        
                        <!--a class="more-task-actions" data-activates="task-actions-$cnt">
                            <i class=" material-icons">more_vert</i>
                        </a-->
                        
                       <!-- add task here -->
                        
                        <span class="hide-on-small-only" id="transaction_ref_num">
                            {$val['display_num']}
                        </span>    
                    </div>
                    <div class="col l6 m6 s8 toggle-task-header">
                        <div class="hide-on-med-and-up toggle-ref-num" id="transaction_ref_num">{$val['display_num']}</div>
                        {$val['display_name']}
                        <div class="hide-on-large-only toggle-timestamp">{$val['display_extra']}</div>
                    </div>
                    <div class="col l4 m4 s3 toggle-timestamp hide-on-med-and-down {$padding}">{$val['display_extra']}</div>
                </div>
            </div>
            {$btns}
            <div class="collapsible-body">
                <div class="row p-b-n">
                    <div class="col s4 offset-s4 center">
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>   
                    </div>
                </div>
            </div>
        </li>
EOS;

        $cnt++;
    }	
    
        if( ISSET($last_page) && $last_page === FALSE)
            $html .= '<span id="scroll-next-page"></span>';

        echo $html;  
    }else{

        IF($with_datatable == FALSE){
        $html = '
            <div id="notfound">
                <div class="notfound">
                    <div class="notfound-404">
                        <h1>No data available</h1>
                    </div>
                    <h2>Oops! Nothing was found</h2>
                </div>
            </div>';
        echo $html;
        }
    }  
?>