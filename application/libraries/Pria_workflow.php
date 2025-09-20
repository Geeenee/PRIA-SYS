<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_workflow {

    protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();

        $this->CI->load->library('pria_overview');
        $this->CI->load->library('audit_trail');
        $this->CI->load->library('pria_mailer');
        $this->CI->load->library('pria_notification');

        $this->CI->load->model('pria_workflow_model', 'pwm');
        $this->CI->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');
		$this->CI->load->model(PORTAL_TRANSACTIONS.'/task_comment_model', 'tcm_model');
		$this->CI->load->model(PORTAL_TRANSACTIONS.'/dr/delivery_goods_model', 'dgm_model');
		$this->CI->load->model(PORTAL_TRANSACTIONS.'/documents_model', 'dm_model');
        $this->CI->load->model(PORTAL_TRANSACTIONS.'/soa/soa_model', 'soa_model');
        $this->CI->load->model(PORTAL_TRANSACTIONS.'/projects/projects_model', 'projects_model');
    }

    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-05-16 15:40:59
     * @Desc:
     * @ReferencedBy:
     * @Returns :
     *      workflow_id : the workflow id that is inserted
     *      task_id     : the current task id
     */
    public function copy_worfklow($workflow_id, $extra_data=array())
    {
        try
        {
            //I NEED THE ACCOUNT GROUP CODE AND REFERNCE ID
            if( ! ISSET($extra_data['user_id'])) throw new Exception('User id is not defined');

            $user_id         = $extra_data['user_id'];

            unset($extra_data['user_id']);

            //Added by Christian Nov 7, 2019
            //Starts
               $w_flow = $this->CI->pwm->get_core_workflow(['workflow_id' => $workflow_id]);
            //Ends

            $data            = array_merge( $extra_data, ['core_workflow_id' => $workflow_id, 'tat' => $w_flow['tat_in_days'] ] );
            //Copy workflow from core tables
            $new_workflow_id = $this->CI->pwm->insert_workflow($data);
            //Copy stages from core tables
            $this->CI->pwm->insert_stages($workflow_id, $new_workflow_id);
            //Get the inserted stages
            $where  = array('pria_workflow_id' => $new_workflow_id);
            $fields = array('pria_stage_id', 'stage_name', 'core_workflow_stage_id');
			$stages = $this->CI->pwm->get_stages($where, $fields, ['sequence_no' => 'ASC']);

            //Insert tasks per stage

            foreach($stages as $val)
            {
                $this->CI->pwm->insert_tasks($val['core_workflow_stage_id'], $val['pria_stage_id'], $user_id);
            }

            //Get inserted tasks
            $new_stage_ids = array_column($stages, 'pria_stage_id');

            //return $this->_copy_tasks($new_stage_ids);

            $result = $this->_copy_tasks($new_stage_ids);

            return [
                'workflow_id'           => $new_workflow_id,
                'task_id'               => $result['pria_task_id'],
                'core_workflow_task_id' => $result['core_workflow_task_id']
            ];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-05-09 10:27:05
     * @Desc:
     * @ReferencedBy:
     * @Params:
     *    $pria_workflow_id    - This id refers to the current pria_workflow_id
     *    $curr_sequence_no    - This refers to the sequence where the append will happen.
     *    $core_workflow_id    - This id refers to the workflow that will be appended.
     */
    //public function append_stage($pria_stage_id, $core_workflow_id, $user_id)
    public function append_stage($pria_workflow_id, $curr_sequence_no, $core_workflow_id, $user_id, $upd_pre_first_task=FALSE, $with_appendable=FALSE, $upd_first_task_role=FALSE)
    {
        try
        {
            //Get the pria workflow id where the append will happen
            /* $pria_stage_details = $this->CI->pwm->get_stage( array('pria_stage_id' => $pria_stage_id), array('pria_workflow_id', 'sequence_no') );
            $pria_workflow_id   = $pria_stage_details['pria_workflow_id'];
            $curr_sequence_no   = $pria_stage_details['sequence_no']; */
            $orig_curr_seq_no   = $curr_sequence_no;
            //Get stages that will be appended
            $fields             = ['stage_name', 'tat_in_days', 'skip_flag', 'workflow_stage_id', 'workflow_stage_id'];
            $core_stages        = $this->CI->pwm->get_core_stages( array('workflow_id' => $core_workflow_id), $fields, ['sequence_no' => 'ASC']);
            //Count
            $increment          = COUNT($core_stages);
            //Update the sequence number of the stages following the workflow that will be appended
            $this->CI->pwm->update_stage_sequence($pria_workflow_id, $curr_sequence_no, $increment);
            //Append the stages
            $pria_stage_ids     = [];
            foreach($core_stages as $val)
            {
                $curr_sequence_no++;

                $fields         = [
                    'pria_workflow_id'       => $pria_workflow_id,
                    'stage_name'             => $val['stage_name'],
                    'tat'                    => $val['tat_in_days'],
                    'skip_flag'              => $val['skip_flag'],
                    'core_workflow_stage_id' => $val['workflow_stage_id'],
                    'sequence_no'            => $curr_sequence_no,
                ];

                $pria_stage_id    = $this->CI->pwm->insert_stage($fields);

                $pria_stage_ids[] = $pria_stage_id;

                $this->CI->pwm->insert_tasks($val['workflow_stage_id'], $pria_stage_id, $user_id);
            }

            //return $this->_copy_tasks($pria_stage_ids, $orig_curr_seq_no, $pria_workflow_id);
			$upd_first_task_dets = [];

			if($upd_first_task_role)
			{
				$upd_first_task_dets = [
						'user_id' 		=> $user_id,
						'actor_name' 	=>[$this->_task_actor_name($user_id), 'ENCRYPT']
				];
			}



            $result =  $this->_copy_tasks($pria_stage_ids, $orig_curr_seq_no, $pria_workflow_id, $with_appendable, $upd_pre_first_task, $upd_first_task_dets);

            return $result['pria_task_id'];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }


    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-05-09 13:22:26
     * @Desc: Inserts the tables connect to tasks
     * @ReferencedBy:
     *
     * $curr_sequence_no
     */
    private function _copy_tasks($stage_ids, $curr_sequence_no=0, $pria_workflow_id=0, $with_appendable=TRUE, $upd_pre_first_task=FALSE, $upd_first_task_dets=[])
    {
        try
        {
          /*   $where         = array('pria_stage_id' => array('IN', $stage_ids));
            $fields        = array('pria_task_id', 'core_workflow_task_id');

			$tasks         = $this->CI->pwm->get_tasks($where, $fields);

            $last_ptid     = 0;
            $first_ptid    = 0;
            foreach($tasks as $val)
            {
                $task_id     = $val['core_workflow_task_id'];
                $new_task_id = $val['pria_task_id'];
                $last_ptd    = $new_task_id;

                if(EMPTY($first_ptid))
                    $first_ptid = $new_task_id;

                $this->CI->pwm->insert_task_roles($task_id, $new_task_id);

                $this->CI->pwm->insert_task_actions($task_id, $new_task_id);

				if($with_appendable)
                	$this->CI->pwm->insert_task_appendable($task_id, $new_task_id);

                $this->CI->pwm->insert_task_forms($task_id, $new_task_id);

                $this->CI->pwm->insert_task_predecessors($task_id, $new_task_id, $stage_ids);

                $this->CI->pwm->insert_task_document_types($task_id, $new_task_id);

                //$this->CI->pwm->insert_task_file_extensions($task_id, $new_task_id);

                $this->CI->pwm->insert_task_return($task_id, $new_task_id, $stage_ids);
			} */

			$first_ptid    = 0;
			$first_cwtid   = 0;
			$last_ptid     = 0;

			foreach($stage_ids as $stage_id)
			{
				$where         = array('pria_stage_id' => $stage_id);
				$fields        = array('pria_task_id', 'core_workflow_task_id');

				$tasks         = $this->CI->pwm->get_tasks($where, $fields, ['sequence_no' => 'ASC']);

				foreach($tasks as $val)
				{
					$task_id     = $val['core_workflow_task_id'];
					$new_task_id = $val['pria_task_id'];
					$last_ptd    = $new_task_id;

					if(EMPTY($first_ptid))
						$first_ptid = $new_task_id;

					if(EMPTY($first_cwtid))
						$first_cwtid = $task_id;

					$this->CI->pwm->insert_task_roles($task_id, $new_task_id);

					$this->CI->pwm->insert_task_actions($task_id, $new_task_id);

					if($with_appendable)
						$this->CI->pwm->insert_task_appendable($task_id, $new_task_id);

					$this->CI->pwm->insert_task_forms($task_id, $new_task_id);

					$this->CI->pwm->insert_task_predecessors($task_id, $new_task_id, $stage_ids);

					$this->CI->pwm->insert_task_document_types($task_id, $new_task_id);

					//$this->CI->pwm->insert_task_file_extensions($task_id, $new_task_id);

					$this->CI->pwm->insert_task_return($task_id, $new_task_id, $stage_ids);
				}
			}

            //If set, update the predecessors of the dependents tasks..
            if( ! EMPTY($curr_sequence_no) && ! EMPTY($pria_workflow_id))
            {
                $curr_pred_pria_task_id = $this->CI->pwm->get_current_predecessor_pria_task_id($pria_workflow_id, $curr_sequence_no);

                //Updates the predecessor of the task
                $this->CI->pwm->update_task_predecessors(['pre_pria_task_id' => $last_ptd], ['pre_pria_task_id' => $curr_pred_pria_task_id]);

                //Inserts the predecessor of the first task of the appended workflow ( optional yung sa DOC DR hindi needed to )
                if($upd_pre_first_task)
                    $this->CI->pwm->insert_pria_task_predecessors(['pre_pria_task_id' => $curr_pred_pria_task_id, 'pria_task_id' => $first_ptid]);
			}

			if( ! EMPTY($upd_first_task_dets))
			{
				//print_var_export($upd_first_task_dets); die;
				$first_ptid = $this->CI->pwm->update_task($upd_first_task_dets, ['pria_task_id' => $first_ptid]);
			}
            //Return the first task id
            //return $tasks[0]['pria_task_id'];
            return [
                'pria_task_id'          => $first_ptid,
                'core_workflow_task_id' => $first_cwtid
            ];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }


    private function _task_actor_name($user_id=NULL)
    {
        try
        {
             $user_id       = (EMPTY($user_id)) ?  $this->CI->session->user_id : $user_id;

             //Get the name of the current user
             $user_details  = $this->CI->pwm->get_core_user(['user_id' => $user_id], [
                aes_crypt('fname', FALSE),
                aes_crypt('mname', FALSE),
                aes_crypt('lname', FALSE),
                aes_crypt('ext_name', FALSE)
            ]);

            $actor  = trim(
                $user_details['fname'].' '.
                $user_details['mname'].' '.
                $user_details['lname'].' '.
                $user_details['ext_name']
            );

            return $actor;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

 /*    public function tag_task_completed($pria_task_id, $end_date = '', $add_where = array())
    {
        try
        {
            $remarks = filter_var($remarks, FILTER_SANITIZE_STRING);

            $fields = [
                'end_date'          => (EMPTY($end_date)) ? date(FORMAT_DB_DATETIME) : $end_date,
                'task_status_id'    => TASK_STATUS_DONE,
            ];

            //added by christian
            $fields = array_merge($fields, $add_where);
            $this->CI->pwm->update_task($fields, ['pria_task_id' => $pria_task_id]);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    } */

     public function tag_task_status($pria_task_id, $task_status_id, $columns=[], $user_id = NULL, $predecessor_ids = NULL)
    {
        try
        {
            $return = [];
            $session_user_id = '';

            //$actor  = $this->_task_actor_name();
            if(empty($user_id) || is_null($user_id)){
                $session_user_id = $this->CI->session->user_id;
            }else{
                $session_user_id = $user_id;
            }

            $actor  = $this->CI->pwm->get_user_fullname($session_user_id);

            $fields = [
                'user_id'           => $session_user_id,
                // 'start_date'        => date(FORMAT_DB_DATETIME),
                'actor_name'        => [$actor, 'ENCRYPT']
            ];

            $curr_datetime  = date(FORMAT_DB_DATETIME);
            $task_details   = $this->CI->pwm->get_task(['pria_task_id' => $pria_task_id], ['start_date']);

            switch($task_status_id)
            {
                case TASK_STATUS_APPROVED:
                case TASK_STATUS_DONE:
                    $fields['end_date']    = $curr_datetime;
                    $return['actor_name']  = $actor;

                    //Pag save and submit agad siya..
                   /*  if(EMPTY($task_details['actual_start_date']))
                        $fields['actual_start_date'] = $curr_datetime; */
                break;

                case TASK_STATUS_RETURNED:
                    $fields['end_date']    = $curr_datetime;

                    $this->_open_prev_task($pria_task_id, $predecessor_ids);

                    $return['actor_name']  = $actor;
                break;

                case TASK_STATUS_ONGOING:
                    $fields['start_date']  = $curr_datetime;
                    $return['actor_name']  = $actor;

                    //Set the saved_flag to Y, Unless it is defined by the user
                    $columns['saved_flag'] = (ISSET($columns['saved_flag'])) ? $columns['saved_flag'] : ENUM_YES;
                break;

                case TASK_STATUS_SKIPPED:
                    $fields['end_date']    = $curr_datetime;
                break;

                default:
                    throw new Exception($this->CI->lang->line('invalid_action'));
            }

            $fields['task_status_id'] = $task_status_id;

            $fields = array_merge($fields, $columns);

            $this->CI->pwm->update_task($fields, ['pria_task_id' => $pria_task_id]);

            if($return)
                return $return;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
/*
    private function _open_prev_task($pria_task_id = NULL, $predecessor_ids = NULL)
    {
        try{
            //Chosen task to return to
            $return_ids[] = $predecessor_ids;

            $this->_get_return_records($predecessor_ids, $pria_task_id, $return_ids);

            //$fields = array('task_status_id' => NULL, 'returned_flag'   => YES_FLAG);
            $fields = array('task_status_id' => TASK_STATUS_RETURNED, 'returned_flag'   => YES_FLAG);
            $where  = array('pria_task_id' => ['IN' => $return_ids]);

            $this->CI->pwm->update_task($fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    private function _get_return_records($pria_task_id, $current_pria_task_id, &$return_ids)
    {
        try
        {
            $where      = ['pre_pria_task_id' =>  $pria_task_id];
            $records    = $this->CI->pwm->get_task_predecessors($where);

            foreach($records as $r)
            {
                if($r['pria_task_id'] == $current_pria_task_id) return;

                $return_ids[] = $r['pria_task_id'];

                $this->_get_return_records($r['pria_task_id'], $current_pria_task_id, $return_ids);
            }

            return;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    } */
    //kebsvillarojo
    public function tag_task($pria_task_id, $task_status_id, $columns=[], $user_id=NULL, $recipient_id = NULL, $cmd = FALSE)
	{
		try
		{
            //print_var_export('INSIDE TAG TASK', get_params()); die;
            //This is used for task attachments...
            if(!$cmd)
                $params = get_params();

			//Other data/ids need by other functions will be defined here. CI->
			$extra			= [];
			$curr_datetime  = date(FORMAT_DB_DATETIME);

			if(EMPTY($user_id))
				$user_id = $this->CI->session->user_id;

			//Setup default fields to update
			$fields = [
				'user_id'           => $user_id,
				'task_status_id'	=> $task_status_id
			];

			//Get task details
			$task_details 		= $this->CI->tm_model->get_task_details($pria_task_id);
			/* pria_stage_id pria_workflow_id */

            if(EMPTY($task_details['user_id']) OR (!EMPTY($task_details['user_id']) AND EMPTY($task_details['actor_name_str'])) OR ($task_details['user_id'] != $user_id))
            {
                $actor                  = $this->CI->pwm->get_user_fullname($user_id);
                $fields['actor_name']   = [$actor, 'ENCRYPT'];
            }

			$pria_stage_id 		= $task_details['pria_stage_id'];
			$pria_workflow_id 	= $task_details['pria_workflow_id'];

			//Setup audit trail details
			$prev_detail  		= [[$task_details]];
			$audit_action 		= [AUDIT_UPDATE];
			$audit_schema 		= [DB_PORTAL];
			$audit_table  		= [Portal_Model::PORTAL_TABLE_PRIA_TASKS];
			$module_code  		= $this->_get_module_code_per_task_ag_code($task_details['account_group_code']);

			$update_dependent	= FALSE;
			$auto_assign_nxt_tk = FALSE;
			$skip_overview 		= '';

			if(ISSET($columns['skip_overview']))
			{
				$skip_overview = $columns['skip_overview'];

				unset($columns['skip_overview']);
			}
			//$skip_overview 		= (ISSET($columns['skip_overview'])) ? $columns['skip_overview'] : '';

			$this->_set_tat_dates($fields, $task_details, $task_status_id, $curr_datetime);

			switch($task_status_id)
			{
				case TASK_STATUS_ONGOING:
                    // $update_dependent 		= TRUE;
					//If there's a value just retain it.
					//$fields['start_date']	= ( ! EMPTY($task_details['start_date'])) ? $task_details['start_date']: $curr_datetime;

					$audit_activity 		= 'audit_trail_task_status_assigned';
				break;

				case TASK_STATUS_DONE:
					$update_dependent 		= TRUE;
					//$fields['end_date']		= $curr_datetime;

					//$this->_set_tat_dates($fields, $pria_task_id, $task_details);

					$audit_activity 		= 'audit_trail_task_status_complete';

                    if(ISSET($columns['skip_reminder']) AND !EMPTY($columns['skip_reminder']))
                    {
                        unset($columns['skip_reminder']);
                    }
                    else
                    {
                        $this->_insert_reminder($task_details, $module_code, '', $user_id);
                    }

				break;

				case TASK_STATUS_APPROVED:
					$update_dependent 		= TRUE;

					//If there's a value just retain it. Possible that start date is already updated upon tasks with "Get" action.
					//$fields['start_date']	= ( ! EMPTY($task_details['start_date'])) ? $task_details['start_date']: $curr_datetime;
					//$fields['end_date']		= $curr_datetime;

					//$this->_set_tat_dates($fields, $pria_task_id, $task_details);

					if( ! EMPTY($columns['remarks']))
						$this->_insert_remarks_as_comment($pria_task_id, $columns['remarks'], $user_id);

					$audit_activity 		= 'audit_trail_task_status_approve';

                    //For Reminders
                    $reminder_actor    = $this->CI->pwm->get_user_fullname($user_id);


                    if(ISSET($columns['skip_reminder']) AND !EMPTY($columns['skip_reminder']))
                    {
                        unset($columns['skip_reminder']);
                    }
                    else
                    {
                        $this->_insert_reminder($task_details, $module_code, $reminder_actor, $user_id);
                    }

				break;


				case TASK_STATUS_DISAPPROVED:
					//If there's a value just retain it. Possible that start date is already updated upon tasks with "Get" action.
					//$fields['start_date']	= ( ! EMPTY($task_details['start_date'])) ? $task_details['start_date']: $curr_datetime;
					//$fields['end_date']		= $curr_datetime;

					//Updates the status of all succeeding tasks to "Cancelled"
					$this->_cancel_succeeding_tasks($task_details);

					$this->_insert_remarks_as_comment($pria_task_id, $columns['remarks'], $user_id);

					$audit_activity 		= 'audit_trail_task_status_disapprove';
				break;

				case TASK_STATUS_RETURNED:

					$extra['task_return_id']	= $columns['task_return_id'];
					$columns['returned_flag']	= ENUM_NO;

					$this->_open_prev_task($pria_task_id,  $extra['task_return_id']);

					$this->_insert_remarks_as_comment($pria_task_id, $columns['remarks'], $user_id);

					$audit_activity 			= 'audit_trail_task_status_return';
					break;

				case TASK_STATUS_SKIPPED:
					//$fields['end_date']    		= $curr_datetime;

                    if(in_array($task_details['core_workflow_stage_id'], array(CORE_WORKFLOW_STAGE_DOC_DR, CORE_WORKFLOW_STAGE_DOC_DR_APPEND)))
                    {
                        //Updates the status of all succeeding tasks to "Skipped"
                        $this->_skip_succeeding_stage_tasks($task_details);
                    }

                    if(in_array($task_details['core_workflow_task_id'], array(CORE_TASK_SOA_TRANSMIT_CALAMBA)))
                    {
                        $update_dependent       = TRUE;
                    }

					$audit_activity 			= 'audit_trail_task_status_skipped';
				break;
			}

			//Unset this key because it is not included in pria_tasks table.
			if(ISSET($columns['task_return_id']))
				unset($columns['task_return_id']);

			$last_dr_flag = ISSET($columns['last_dr_flag']) ? $columns['last_dr_flag'] : NO_FLAG;

			if(ISSET($columns['last_dr_flag']))
				unset($columns['last_dr_flag']);

            if(ISSET($columns['manual_get']) AND $columns['manual_get'] == ENUM_YES)
            {
                unset($fields['task_status_id']);
                unset($columns['manual_get']);
            }

			//Updates task status
			$fields = array_merge($fields, $columns);
			$this->CI->pwm->update_task($fields, ['pria_task_id' => $pria_task_id]);

			//Update stage status
			//Get last task of the current stage
			$stage_tasks 		= $this->CI->pwm->get_tasks(['pria_stage_id' => $pria_stage_id], ['pria_task_id', 'task_status_id', 'core_workflow_task_id'], ['sequence_no' => 'DESC']);

			//print_var_export($stage_tasks); die;

			if(
				$stage_tasks[0]['pria_task_id'] == $pria_task_id &&
				in_array($stage_tasks[0]['task_status_id'], [TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_SKIPPED]) == TRUE
			  )
			{
				$this->CI->pwm->update_stage(['status_code' => STATUS_COMPLETED], ['pria_stage_id' => $task_details['pria_stage_id']]);
			}

			//die('ay');
			//Updates the status of the dependent tasks to "Pending"
			if($update_dependent)
				$this->_update_dependent_tasks($pria_task_id, $user_id, $task_details, $recipient_id);

			//Get update task details for overview and audit trail
            $task_details = $this->CI->tm_model->get_task_details($pria_task_id);

            //Updates something base on the core_workflow_task_id ( optional ), Here is where the updating of disapproved transaction also happen ( Note: main table only e.g sites )
			//$this->_update_data_upon_complete($task_details['task_reference_id'], $task_details['core_workflow_task_id'], $task_status_id);
			if($task_status_id != TASK_STATUS_ONGOING)
				$this->_update_data_upon_complete($task_details['core_workflow_task_id'], $task_details, $params);

            //Get orgs by task ID
            $ref_orgs = $this->_get_org_ref($task_details);

			//Updates the workflow as completed
			$workflow_last_task = $this->CI->pwm->get_last_task_per_workflow($pria_workflow_id, ['c.pria_task_id']);
			if(
				$workflow_last_task['pria_task_id'] == $pria_task_id &&
				in_array($task_details['task_status_id'], [TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_SKIPPED]) == TRUE
			)
			{
                //Added by Christian 'end_date' if completed task.
				$this->CI->pwm->update_workflow(['status_code' => STATUS_COMPLETED, 'end_date' => $curr_datetime ], ['pria_workflow_id' => $pria_workflow_id]);
			}

			//Update the email for this task as inactive
			$this->CI->tm_model->update_task_email_link(['pria_task_id' => $pria_task_id], ['active_flag' =>  INITIAL_NO]);

			if( ! $skip_overview )
			{
				//Setup and log overview
				//die('andito naman');
				$overview_details   = [
					'reference' 			=> $task_details['reference_id'],
					'created_by' 			=> $user_id,
					'created_date' 			=> $curr_datetime,
					'account_group_code' 	=> $task_details['account_group_code']
				];

				$overview_details 	= array_merge($task_details, $overview_details);

				$this->CI->pria_overview->log_overview($module_code, OVERVIEW_TYPE_CHANGE_TASK_STATUS, $overview_details);
			}
			//die('it\'s living');
			//Log audit trail
			$curr_detail  = [[$task_details]];
            $activity     = sprintf($this->CI->lang->line($audit_activity), $task_details['task_name']);

			$this->CI->audit_trail->log_audit_trail($activity, $module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema, $user_id);

			//Get configure documents per task
			$documents  = $this->CI->dm_model->get_task_doc_file_extension($pria_task_id);
			//
			$docs_w_add = in_array(DOCUMENT_ACCESS_ADD, array_column($documents, 'access'));


            if (! empty($params['task_doc_type']) && $docs_w_add > 0) {
                foreach ($params['task_doc_type'] as $document_type_code) {
                    if (!empty($params[$document_type_code.'_orig_filename'])) {
                        $fields = [
                            'reference'          => $task_details['task_reference_id'],
                            'pria_task_id'       => $pria_task_id,
                            'pria_stage_id'      => $pria_stage_id,
                            'document_type_code' => strtoupper($document_type_code),
                            'file_name'          => $params[$document_type_code.'_orig_filename'],
                            'sys_file_name'      => $params[$document_type_code],
                            'module_code'        => $module_code,
                            'created_by'         => $user_id,
                            'created_date'       => $curr_datetime,
                            'account_group_code' => $task_details['account_group_code'],
                            'version'            => 1,
                            'initial_upload'     => ENUM_YES,
                        ];

                        $this->CI->dm_model->insert_document($fields);
                    }
                }
            }

            //$task_docs 	= $this->CI->dm_model->get_documents(['pria_task_id' => $pria_task_id]);

			//print_var_export($documents, 'DOCS W ADD : '.$docs_w_add, 'TASK DOCS', $task_docs); die;
            //Updates all documents in the task that it is not initial upload already ( meaning wala ng delete after ma complete tong function na to)
            $this->CI->dm_model->update_document(['initial_upload' => ENUM_NO], ['pria_task_id' => $pria_task_id]);

            //print_var_export($documents, $docs_w_add, $task_docs); die;
			//Email notifications
			/* if($docs_w_add == 0 || ($docs_w_add > 0 && ! EMPTY($task_docs))){
                die('asdf'); */
				$this->_send_email_notifications($task_status_id, $task_details, $user_id, $extra, $cmd);
            //}

			//System notifications
			if($task_details['notif_flag'] == YES_FLAG OR $last_dr_flag == YES_FLAG OR in_array($task_status_id, array(TASK_STATUS_RETURNED, TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED)))
			{
				if($task_status_id == TASK_STATUS_RETURNED)
				{
					//Get update task details for overview and audit trail
					$task_ret_details = $this->CI->tm_model->get_task_details($extra['task_return_id']);
                    $status_label = ' returned';

					$this->_send_sys_notifications($task_status_id, $task_ret_details, $module_code, $user_id, $extra, $status_label, $task_details, $ref_orgs);
				}else{
					switch ($task_status_id) {
						case TASK_STATUS_DISAPPROVED:
							$status_label = ' disapproved';
							break;

						case TASK_STATUS_APPROVED:
							$status_label = ' approved';
							break;

						case TASK_STATUS_DONE:
							if($task_details['returned_flag'] == YES_FLAG){
								$status_label = ' resubmitted';
							}else{
								$status_label = ' submitted';
							}

							break;

						default:
							$status_label ='';
							break;
					}

					$this->_send_sys_notifications($task_status_id, $task_details, $module_code, $user_id, $extra, $status_label, $task_details, $ref_orgs);
				}
            }
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }


    private function _get_module_code_per_task_ag_code($ag_code)
	{
		try
		{
		  $row = $this->CI->tm_model->get_module_account_group(['account_group_code' => $ag_code]);

		  return $row[0]['module_code'];
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

	private function _set_tat_dates(&$fields, $task_details, $task_status_id, $curr_datetime)
	{
		$task_id 		= $task_details['pria_task_id'];

		if($task_details['due_date_tag'] == YES_FLAG)
		{
			//get predecessors
			$where 	= array('pria_task_id' => $task_id);
			$pre 	= $this->CI->tm_model->get_next_predecessors($where);

			//get predecesors details
			if(is_array($pre) AND count($pre) > 0)
			{
				$pre_task_details 			    = $this->CI->tm_model->get_task_details($pre[0]['pre_pria_task_id']);

				$fields['expected_start_date']  = $pre_task_details['actual_end_date'];
				$fields['expected_end_date'] 	= date(FORMAT_DB_DATETIME, strtotime($pre_task_details['actual_end_date'].' + '.floor($task_details['tat']).' days'));
			}
		}

		switch($task_status_id)
		{
			case TASK_STATUS_ONGOING:
				$start_date = ( ! EMPTY($task_details['start_date'])) ? $task_details['start_date']: $curr_datetime;

				$fields['start_date'] = $fields['actual_start_date'] = $start_date;
			break;

			case TASK_STATUS_DISAPPROVED:
			case TASK_STATUS_APPROVED:
			case TASK_STATUS_DONE:
				$start_date = ( ! EMPTY($task_details['start_date'])) ? $task_details['start_date']: $curr_datetime;

				$fields['start_date'] = $fields['actual_start_date'] = $start_date;

				$fields['end_date']   = $fields['actual_end_date'] 	 = $curr_datetime;
			break;

			case TASK_STATUS_RETURNED:
				$fields['start_date']   = ( ! EMPTY($task_details['start_date'])) ? $task_details['start_date']: $curr_datetime;
			break;

			case TASK_STATUS_SKIPPED:
				$fields['end_date']   	= $fields['actual_end_date'] 	= $curr_datetime;
				$fields['start_date']   = $fields['actual_start_date']	= $curr_datetime;
			break;
		}
	}


/*     private function _set_tat_dates_old(&$fields, $task_id, $task_details)
	{
		try
		{
			//Added condition by Christian - if system_based_flag is yes system generated dates will be used in actual dates
			//July 23, 2019
			if($task_details['system_based_tag'] == YES_FLAG)
			{
				$fields['actual_end_date'] = date(FORMAT_DB_DATETIME);

				if(EMPTY($task_details['start_date']))
					$fields['start_date'] = date(FORMAT_DB_DATETIME);

				if(EMPTY($task_details['actual_start_date']))
					$fields['actual_start_date'] = date(FORMAT_DB_DATETIME);
			}

			//Added condition by Christian - if due_date_tag is yes get predecessors "actual_end_date"  = "expected_start_date" , "actual_end_date" + tat = expected_end_date"
			//July 23, 2019
			if($task_details['due_date_tag'] == YES_FLAG)
			{
				//get predecessors
				$where 	= array('pria_task_id' => $task_id);
				$pre 	= $this->CI->tm_model->get_next_predecessors($where);

				//get predecesors details
				if($pre)
				{
					$pre_task_details 				= $this->CI->tm_model->get_task_details($pre[0]['pre_pria_task_id']);

					$fields['expected_start_date'] 	= $pre_task_details['actual_end_date'];
					$fields['expected_end_date'] 	= date('Y-m-d H:i:s',strtotime($pre_task_details['actual_end_date']) + ((86400 * $task_details['tat'])) - 86400);
				}
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    } */

    private function _insert_remarks_as_comment($pria_task_id, $remarks, $user_id)
	{
		try
		{
			//Insert remarks as as comment
			$comment = [
				'pria_task_id'		=> $pria_task_id,
				'pria_task_comment' => '<p>'.$remarks.'</p>',
				'created_by' 		=> $user_id,
				'created_date'  	=> date(FORMAT_DB_DATETIME)
			];

			$this->CI->tcm_model->insert_task_comment($comment);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }


    private function _cancel_succeeding_tasks($task_details)
	{
		try
		{
			//Gets the current sequence and stage id
			$curr_pria_stage_id 	= $task_details['pria_stage_id'];
			$curr_pria_stage_seq	= $task_details['pria_stage_sequence_no'];
			$curr_pria_task_seq  	= $task_details['sequence_no'];

			$fields = ['task_status_id'	 => TASK_STATUS_CANCELLED];

			//Updates all task under the same stage to "Cancelled"
			$where  = ['sequence_no'	 => ['>' => $curr_pria_task_seq],  'pria_stage_id' => $curr_pria_stage_id];
          //  print_var_export($where);
			$this->CI->pwm->update_task($fields, $where);

			//Gets the succeeding stages
			$where  	= array('pria_workflow_id' => $task_details['pria_workflow_id'], 'sequence_no'	 => ['>' => $curr_pria_stage_seq]);
            $fields_arr = array('pria_stage_id');
			$stages 	= $this->CI->pwm->get_stages($where, $fields_arr);

			$stage_ids 	= array_column($stages, 'pria_stage_id');

			//Updates all task under the succeeding stage to "Cancelled"
			$where  	= ['pria_stage_id' => ['IN' => $stage_ids]];
           // print_var_export($where); die;
			$this->CI->pwm->update_task($fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    public function _skip_succeeding_stage_tasks($task_details)
    {
        try
        {
            //Gets the current sequence and stage id
            $curr_pria_stage_id     = $task_details['pria_stage_id'];
            $curr_pria_stage_seq    = $task_details['pria_stage_sequence_no'];
            $curr_pria_task_seq     = $task_details['sequence_no'];

            $fields = ['task_status_id'  => TASK_STATUS_SKIPPED];

            //Updates all task under the same stage to "Skipped"
            $where  = ['sequence_no'     => ['>' => $curr_pria_task_seq],  'pria_stage_id' => $curr_pria_stage_id];

            $this->CI->pwm->update_task($fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    
    public function _skip_stage_tasks($task_details)
    {
        try
        {
            //Gets the current sequence and stage id
            $curr_pria_stage_id     = $task_details['pria_stage_id'];
            $curr_pria_stage_seq    = $task_details['pria_stage_sequence_no'];
            $curr_pria_task_seq     = $task_details['sequence_no'];

            $fields = ['task_status_id'  => TASK_STATUS_SKIPPED];

            //Updates all task under the same stage to "Skipped"
            $where  = ['pria_stage_id' => $curr_pria_stage_id];

            $this->CI->pwm->update_task($fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }


    private function _open_prev_task($pria_task_id = NULL, $predecessor_ids = NULL)
    {
        try{

            //Chosen task to return to
            $return_ids[] = $predecessor_ids;

            $this->_get_return_records($predecessor_ids, $pria_task_id, $return_ids);

            $fields = array('task_status_id' => TASK_STATUS_RETURNED, 'returned_flag' => NO_FLAG);
			$where  = array('pria_task_id' => ['IN' => $return_ids]);

			$this->CI->pwm->update_task($fields, $where);

			$this->CI->pwm->update_task(['returned_flag'   => YES_FLAG], ['pria_task_id' => $predecessor_ids]);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    private function _get_return_records($pria_task_id, $current_pria_task_id, &$return_ids)
    {
        try
        {
            $where      = ['pre_pria_task_id' =>  $pria_task_id];
            $records    = $this->CI->pwm->get_task_predecessors($where);

            foreach($records as $r)
            {
                if($r['pria_task_id'] == $current_pria_task_id) return;

                $return_ids[] = $r['pria_task_id'];

                $this->_get_return_records($r['pria_task_id'], $current_pria_task_id, $return_ids);
            }

            return;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}


    private function _update_data_upon_complete($core_workflow_task_id, $task_details, $params = [])
	{
		try
		{
			$reference_id = $task_details['reference_id'];

			switch($core_workflow_task_id)
			{
				case CORE_TASK_ENCODE_PROFIT_COST:
					$this->CI->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

					$this->CI->sn_model->update_site(['status_code' => PARAM_STATUS_COMPLETED], ['site_id' => $reference_id]);
				break;

				case CORE_TASK_ENCODE_ASSET_CODE:
					$this->CI->load->model(FOLDER_BOQ.'/boq_model', 'bq_model');

					$this->CI->bq_model->update_boq(['status_code' => PARAM_STATUS_COMPLETED], ['boq_id' => $reference_id]);
				break;

				case CORE_TASK_OPENING_DATE:
					$this->CI->load->model(FOLDER_PROJECTS.'/projects_model', 'pj_model');

					$this->CI->pj_model->update_project(['status_code' => PARAM_STATUS_COMPLETED], ['project_id' => $reference_id]);
				break;

				case CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION:
					if($task_details['task_status_id'] == TASK_STATUS_APPROVED)
					{
						$this->CI->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

						$this->CI->sn_model->update_site(['status_code' => PARAM_STATUS_COMPLETED], ['site_id' => $reference_id]);
					}
                    else if($task_details['task_status_id'] == TASK_STATUS_DISAPPROVED)
                    {
                        $this->CI->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

                        $this->CI->sn_model->update_site(['status_code' => PARAM_STATUS_DISAPPROVED], ['site_id' => $reference_id]);
                    }
                break;

                case CORE_TASK_BOQ_PRES_APPROVED:
                    if($task_details['task_status_id'] == TASK_STATUS_APPROVED)
                    {
                        $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_PRIA_BOQ, ['status_code' => PARAM_STATUS_COMPLETED], ['boq_id' => $reference_id]);
                    }
                break;

                case CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL:
                case CORE_TASK_PO_UPLOAD_WO_APPROVAL:
                case CORE_TASK_PO_RELEASED:
                    $this->CI->load->model(PORTAL_TRANSACTIONS . '/po/po_model', 'po_model');

                    $this->CI->po_model->update_po(['po_id' => $reference_id], ['po_status_code' => PO_RELEASED]);
                break;

                case CORE_TASK_SOA_ACCEPT_CALAMBA:
                    if($task_details['task_status_id'] == TASK_STATUS_APPROVED)
					{
                        $drs = $this->CI->dgm_model->get_dr_references_multiple(['soa_id' => $reference_id, 'dr_gr_id' => 'IS NOT NULL']);

                        foreach($drs as $val)
                            $this->CI->dgm_model->update_delivery_goods_receipt(['dr_gr_id' => $val['dr_gr_id']], ['dr_status' => DR_SOA_APPROVED]);
                    }
                break;

                case CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL:
                    $cn_recommendation_bh   = (ISSET($params['recommendation_bh']) AND !EMPTY($params['recommendation_bh']))? $params['recommendation_bh']: NULL;
                    $cn_recommendation_bh   = (ISSET($params['task_remark']) AND !EMPTY($params['task_remark']))? $params['task_remark']: $cn_recommendation_bh;
                    $cn_recommendation_bh   = (ISSET($params['task_remarks']) AND !EMPTY($params['task_remarks']))? $params['task_remarks']: $cn_recommendation_bh;
                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_CONTRACTS, ['cn_recommendation_bh' => $cn_recommendation_bh], ['contract_id' => $reference_id]);
                break;

                case CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL:
                    $cn_recommendation_rh   = (ISSET($params['recommendation_rh']) AND !EMPTY($params['recommendation_rh']))? $params['recommendation_rh']: NULL;
                    $cn_recommendation_rh   = (ISSET($params['task_remark']) AND !EMPTY($params['task_remark']))? $params['task_remark']: $cn_recommendation_rh;
                    $cn_recommendation_rh   = (ISSET($params['task_remarks']) AND !EMPTY($params['task_remarks']))? $params['task_remarks']: $cn_recommendation_rh;
                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_CONTRACTS, ['cn_recommendation_rh' => $cn_recommendation_rh], ['contract_id' => $reference_id]);
                break;

                case CORE_TASK_PRES_APPROVE_CONTRACT_RENEWAL:
                    $old_contract   = $this->CI->tm_model->get_from_table(["*"], Portal_Model::PORTAL_TABLE_CONTRACTS, FALSE, ['contract_id' => $reference_id]);

                    $old_reference_id   = (ISSET($old_contract['reference_contract_id']) AND !EMPTY($old_contract['reference_contract_id']))? $old_contract['reference_contract_id']: NULL;

                    $update_old = array(
                        'contract_status_code'  => CONTRACT_RENEWED,
                        'modified_by'           => $this->CI->session->userdata('user_id'),
                        'modified_date'         => date(FORMAT_DB_DATETIME)
                    );

                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_CONTRACTS, $update_old, ['contract_id' => $old_reference_id]);

                    $update = array(
                        'contract_status_code'  => CONTRACT_NEW,
                        'modified_by'           => $this->CI->session->userdata('user_id'),
                        'modified_date'         => date(FORMAT_DB_DATETIME)
                    );

                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_CONTRACTS, $update, ['contract_id' => $reference_id]);
                break;

                case CORE_TASK_SITES_RECOM_SITE_REGIONAL:
                    $sn_recommendation_bh   = (ISSET($params['recommendation_bh']) AND !EMPTY($params['recommendation_bh']))? $params['recommendation_bh']: NULL;
                    $sn_recommendation_bh   = (ISSET($params['task_remark']) AND !EMPTY($params['task_remark']))? $params['task_remark']: $sn_recommendation_bh;
                    $sn_recommendation_bh   = (ISSET($params['task_remarks']) AND !EMPTY($params['task_remarks']))? $params['task_remarks']: $sn_recommendation_bh;
                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_SITES, ['sn_recommendation_bh' => $sn_recommendation_bh], ['site_id' => $reference_id]);
                break;

                case CORE_TASK_SITES_RECOM_SITE_PRES:
                    $sn_recommendation_rh   = (ISSET($params['recommendation_rh']) AND !EMPTY($params['recommendation_rh']))? $params['recommendation_rh']: NULL;
                    $sn_recommendation_rh   = (ISSET($params['task_remark']) AND !EMPTY($params['task_remark']))? $params['task_remark']: $sn_recommendation_rh;
                    $sn_recommendation_rh   = (ISSET($params['task_remarks']) AND !EMPTY($params['task_remarks']))? $params['task_remarks']: $sn_recommendation_rh;
                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_SITES, ['sn_recommendation_rh' => $sn_recommendation_rh], ['site_id' => $reference_id]);
                break;

                case CORE_TASK_BOQ_MCS_ENGINEERING:
                    $boq_recommendation_bh   = (ISSET($params['recommendation_bh']) AND !EMPTY($params['recommendation_bh']))? $params['recommendation_bh']: NULL;
                    $boq_recommendation_bh   = (ISSET($params['task_remark']) AND !EMPTY($params['task_remark']))? $params['task_remark']: $boq_recommendation_bh;
                    $boq_recommendation_bh   = (ISSET($params['task_remarks']) AND !EMPTY($params['task_remarks']))? $params['task_remarks']: $boq_recommendation_bh;
                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_PRIA_BOQ, ['boq_recommendation_bh' => $boq_recommendation_bh], ['boq_id' => $reference_id]);
                break;

                /*case CORE_TASK_BOQ_MCS_APPROVED:
                    $boq_recommendation_rh   = (ISSET($params['recommendation_rh']) AND !EMPTY($params['recommendation_rh']))? $params['recommendation_rh']: NULL;
                    $boq_recommendation_rh   = (ISSET($params['task_remark']) AND !EMPTY($params['task_remark']))? $params['task_remark']: $boq_recommendation_rh;
                    $boq_recommendation_rh   = (ISSET($params['task_remarks']) AND !EMPTY($params['task_remarks']))? $params['task_remarks']: $boq_recommendation_rh;
                    $this->CI->tm_model->update_tasks_updates(Portal_Model::PORTAL_TABLE_PRIA_BOQ, ['boq_recommendation_rh' => $boq_recommendation_rh], ['boq_id' => $reference_id]);
                break;*/
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    private function _update_dependent_tasks($task_id, $user_id, $task_details, $recipient_id = NULL)
	{
		try
		{
			$user_roles 	  = $this->CI->tm_model->get_users_role(['user_id' => $user_id], ['role_code']);

			$user_roles 	  = array_column($user_roles, 'role_code');

			$dependents 	  = $this->CI->tm_model->get_dependent_tasks($task_id);

			$dependents_ids   = array_column($dependents, 'pria_task_id');

			//Get the predecessors of the dependent tasks
			$dependents_prede = $this->CI->tm_model->get_task_predecessors($dependents_ids);

           // print_var_export($dependents_prede, $dependents); die;
			foreach($dependents as $d)
			{
				//Checks if the dependent task is ready to be cleared...
				if( ! $this->_check_task_completion_rules($d['core_workflow_task_id'], $d['pria_workflow_reference_id']) )
					continue;

				$clear_task = FALSE;
				$dep_id   	= $d['pria_task_id'];

				//Check if dependent has other predecessors. If no, enable, else, check.
				if( ISSET($dependents_prede[$dep_id]) )
				{
					$result = $this->_check_dependent_predecessor($dependents_prede[$dep_id]);

					if($result)
						$clear_task = TRUE;
				}
				else
				{
					$clear_task = TRUE;
				}

                if($d['core_workflow_task_id'] == CORE_TASK_UPLOAD_BOQ)
                    $recipient_id   = $user_id;

				//Enables the task
				if($clear_task)
					$this->_clear_n_assign_next_task($dep_id, $d['core_workflow_task_id'], $d['org_code'], $task_details, $d['vendor_code'], $recipient_id);
			}
			//die('asd');
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    /**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-08-07 08:03:26
	 * @Desc: Defines special rules before taggging the next task as ongoing/cleared or the current task
	 * If used in current task, mostly used to determine if notification will be triggered
	 * @ReferencedBy:  Task.php
	 */
    public function _check_task_completion_rules($core_workflow_task_id, $reference_id)
	{
		try
		{
			switch($core_workflow_task_id)
			{

				//Codes below are used in next task ( _update_dependent_tasks ) function
				case CORE_TASK_DOC_DR:
					return $this->_check_w_last_dr(DR_MEDVAC, $reference_id);
				break;
				case CORE_TASK_CLEANUP:
				case CORE_TASK_HARVEST_REPORT:
				case CORE_TASK_LIVE_SALES_REPORT:
					return $this->_check_w_last_dr(DR_DOCDR, $reference_id);
				break;
			}

			return TRUE;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    /**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-08-07 08:03:26
	 * @Desc:
	 * @ReferencedBy:  Task.php
	 */
    public function _check_w_last_dr($dr_type, $reference_id)
	{
		try
		{
			//$this->CI->load->model(FOLDER_DELIVERY_GOODS.'/delivery_goods_model', 'dgm_model');
			//$this->CI->load->model(PORTAL_TRANSACTIONS.'/delivery_goods_model', 'dgm_model');

			$last_dr = $this->CI->dgm_model->get_last_delivery($dr_type, $reference_id, ['a.last_dr_flag', 'a.dr_gr_id', 'a.gr_pria_task_id']);

			return ( ! EMPTY($last_dr) ) ? TRUE : FALSE;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

     /**
	 * @Author: Kevin Villarojo
	 * @Date:
	 * @Desc: Checks the predecessor task status of the dependent tasks
	 * @ReferencedBy:  Task.php
	 */
    protected function _check_dependent_predecessor($predecessors)
	{
		try
		{
			$return = TRUE;

			foreach($predecessors as $p)
			{
				if(in_array($p['task_status_id'], [TASK_STATUS_ONGOING, TASK_STATUS_RETURNED]))
					$return = FALSE;
			}

			return $return;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    /**
	 * @Author: kevin villarojo
	 * @Date: 2019-09-12 11:54:51
	 * @Desc:  Updates the task assigned user and task status to pending
	 * @Reference by : Quick Add, Task_Controller
	 */
    public function _clear_n_assign_next_task($dep_pria_task_id, $dep_core_task_id, $org_code, $task_details=[], $vendor_code='', $recipient_id = NULL)
	{
		try
		{
			$user_id 	   = '';
			//Set initial details to update
			$fields   	  = array('task_status_id' => NULL);
			$assign_tasks = [$dep_pria_task_id];
           // print_var_export($task_details); die;
			//Gets the next role
			$roles 	  	  = $this->CI->tm_model->get_task_roles(['pria_task_id' => $dep_pria_task_id, 'actor_flag' => INITIAL_YES], ['role_code']);
			$role_codes   = array_column($roles, 'role_code');

			//Determines if next task will be auto_assigned
			if(in_array(TASK_ROLE_VENDOR, $role_codes))
			{
				//$users 			= $this->tm_model->get_vendor_users(['vendor_code' => $vendor_code], ['user_id']);
				$users 			= $this->CI->tm_model->get_vendor_users_by_vendor_code_n_status($vendor_code);
                $users          = ($users AND COUNT($users) > 0)? array_column($users, 'user_id'): array();
                $users          = array_unique($users);
				//print_var_export($users); die;
			}
			else
			{
				$valid_orgs		= $this->_get_parent_orgs($org_code);

				array_unshift($valid_orgs, $org_code);

                $users          = $this->CI->pria_mailer_model->get_users_per($role_codes, $valid_orgs);
                $users          = ($users AND COUNT($users) > 0)? array_column($users, 'user_id'): array();
                $users          = array_unique($users);

				/*foreach($valid_orgs as $org_code)
				{
					$users  = $this->CI->tm_model->get_users_by_roles_n_org($role_codes, [$org_code]);

					if( ! EMPTY($users))
						break;
				}*/
			}

			switch($dep_core_task_id)
			{
                case CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL:
                case CORE_TASK_SOA_ACCEPT_SOA_BASED:
                case CORE_TASK_SOA_APPROVE:
                case CORE_TASK_SOA_ACCEPT_CALAMBA:
                case CORE_TASK_BOQ_APPROVAL_STORE_MOCKUP_DESIGN:
                // case CORE_TASK_UPLOAD_BOQ:
                    $user_id = $recipient_id;
                break;

                case CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES:
				case CORE_TASK_RETURN_DELIVERY_RECEIPT:
                    $user_id  = $recipient_id;

                    $ctask_id = $dep_core_task_id == CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES ? CORE_TASK_PO_GR_BAVI_MARINADES : CORE_TASK_PO_GR_BFFI;

                    $gr_task  = $this->CI->pwm->get_task(['pria_stage_id' => $task_details['pria_stage_id'], 'core_workflow_task_id' => $ctask_id], ['pria_task_id']);

                    $assign_tasks[] = $gr_task['pria_task_id'];
				break;

                case CORE_TASK_PO_GR_BFFI:
                case CORE_TASK_PO_GR_BAVI_MARINADES:
                    $user_id  = NULL;
                // Change request 12.21.22 Starts Here
                case CORE_TASK_FHR: // contact_Growers/Internal Orders/encode flock history report 
                    $user_id  = NULL;
                // Change request 12.21.22 Ends Here
                break;
				default:
					if(COUNT($users) == 1)
						$user_id = $users[0];
			}

			if( ! EMPTY($user_id))
			{
				$fields['user_id'] 		= $user_id;
				$actor 					= $this->_task_actor_name($user_id);
				$fields['actor_name'] 	= [$actor, 'ENCRYPT'];
			}

            $where  = array('pria_task_id' => ['IN', $assign_tasks]);

			$this->CI->pwm->update_task($fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	private function _get_parent_orgs($org_code, $parents=[])
	{
		try
		{
			$org_dets    = $this->CI->tm_model->get_organization(['org_code' => $org_code], ['org_parent']);

			$parent_org  = $org_dets['org_parent'];

			if( ! EMPTY($parent_org))
			{
				$parents[] = $parent_org;

				$parents   = $this->_get_parent_orgs($parent_org, $parents);
			}

			return $parents;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }


    public function _send_email_notifications($task_status_id, $task_details, $user_id, $extra=[], $cmd)
	{
		try
		{
			$core_task_id	= (ISSET($task_details['core_workflow_task_id']) AND !EMPTY($task_details['core_workflow_task_id']))? encrypt_id($task_details['core_workflow_task_id']): NULL;

			$pria_task_id	= (ISSET($task_details['pria_task_id']) AND !EMPTY($task_details['pria_task_id']))? encrypt_id($task_details['pria_task_id']): NULL;

            if($cmd)
            {
                $base_url_cron      = get_sys_param_val(SYS_PARAM_CRON_SETTINGS, SYS_PARAM_BASE_URL_CRON);
                $base_url_cron      = (ISSET($base_url_cron['sys_param_value']) AND !EMPTY($base_url_cron['sys_param_value']))? $base_url_cron['sys_param_value']: NULL;

                $transaction_params = ['base_url_cron' => $base_url_cron];
            }

			switch($task_status_id)
			{
				case TASK_STATUS_DONE:
                    
					$task_action		= (ISSET($task_details['returned_flag']) AND !EMPTY($task_details['returned_flag']) AND $task_details['returned_flag'] == ENUM_YES) ? TASK_ACTION_RESUBMITTED: NULL;
                   
					$this->CI->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, $task_action, NULL, NULL, NULL, $transaction_params);
				break;

				case TASK_STATUS_APPROVED:
					$this->CI->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, TASK_ACTION_APPROVED, NULL, $user_id, NULL, $transaction_params);
				break;

                case TASK_STATUS_RETURNED:
                    $return_pria_task_id = (!EMPTY($extra['task_return_id']))? encrypt_id($extra['task_return_id']): NULL;

                    $this->CI->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, TASK_ACTION_RETURNED, NULL, $user_id, $return_pria_task_id, $transaction_params);
                    break;
				case TASK_STATUS_DISAPPROVED:
					$this->CI->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, TASK_ACTION_DISAPPROVED, NULL, $user_id, $return_pria_task_id, $transaction_params);
				break;
			}
            
            $this->_send_email_to_specific_user($task_details, $task_status_id);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    private function _send_email_to_specific_user($task_details, $task_status_id){
        try
        {
          
            switch($task_details['core_workflow_task_id']){
                case CORE_TASK_PO_RELEASED:
                case CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL:
                case CORE_TASK_PO_UPLOAD_WO_APPROVAL:
                    if($task_status_id == TASK_STATUS_DONE){
                        
                        $to_users = $this->CI->pwm->get_pr_uploaders_by_po_id($task_details['task_reference_id']);

                        $transaction_params = array(
                            'to_user_ids'			=> array_column($to_users, 'user_id')
                        );
                        
                        $this->CI->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_PO_RELEASED, encrypt_id($task_details['core_workflow_task_id']), encrypt_id($task_details['pria_task_id']), NULL, NULL, $user_id, NULL, $transaction_params);
                    } 
                break;
            }
        }
        catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    private function _send_sys_notifications($task_status_id, $task_ret_details, $module_code, $user_id, $extra, $status_label = '', $task_orig_details = array() , $ref_orgs = array())
	{
		try
		{
			$sys_notif_role     = [];

            $vendor_details     = $this->_get_task_details_detailed($task_orig_details['pria_task_id'], $task_orig_details['core_workflow_task_id']);

            $vendor_code        = (ISSET($vendor_details['vendor_code']) AND !EMPTY($vendor_details['vendor_code']))? $vendor_details['vendor_code']: NULL;

			if($task_orig_details['sys_notif_role'])
            {
				$sys_notif_role = array(array('role_code' => $task_orig_details['sys_notif_role']));
			}

			switch($task_status_id)
			{
				case TASK_STATUS_DISAPPROVED:
				case TASK_STATUS_APPROVED:
				case TASK_STATUS_DONE:
					$this->CI->pria_notification->system_notification($task_ret_details['pria_task_id'], $task_orig_details, $module_code, $sys_notif_role, NULL, $user_id, NULL, $status_label, $task_orig_details, $ref_orgs, $vendor_code);
				break;

				case TASK_STATUS_RETURNED:
					$this->CI->pria_notification->system_notification($task_ret_details['pria_task_id'], $task_ret_details, $module_code, $sys_notif_role, $extra['task_return_id'], $user_id, TRUE, $status_label, $task_orig_details, $ref_orgs, $vendor_code);
				break;
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}


    public function update_task_assignment($pria_task_id = NULL, $user_id = NULL)
    {
        try{

            $where      = array('pria_task_id' => $pria_task_id);

            if( ! EMPTY($user_id))
            {
                $fields['user_id']      = $user_id;
                $actor                  = $this->_task_actor_name($user_id);
                $fields['actor_name']   = [$actor, 'ENCRYPT'];
            }

            $this->CI->pwm->update_task($fields, $where);

        }catch(PDOException $e){
            throw $e;
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * @Author: Christian Aquino
     * @Date: 2019-10-23 05:11:51
     * @Desc:  Insert reminder for user, it appears in dashboard
     * @Reference by : Task_Controller
     */
    private function _insert_reminder($task_details, $module_code, $actor = '', $user_id = NULL)
    {
        try
        {
            $roles              = array(); //specify role to be notified
            $orgs               = array(); //specify role to be notified
            $rem_task_details   = NULL;
            $multiple           = FALSE;

            switch ($task_details['core_workflow_task_id']) {
                case CORE_TASK_SOA_APPROVE:

                    $tab = '#tab_soa';
                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles = array(ROLE_BC_FIN_PERS, ROLE_HO_PAY_FIN, ROLE_PAY_FIN_PERS); //specify role to be notified

                    /*if(!EMPTY($task_details['account_group_code']) AND in_array($task_details['account_group_code'], array(AG_GOODS_BFFI, AG_GOODS_MARINADES)))
                    {
                        $roles[]    = ROLE_PAY_FIN_PERS;
                    }*/

                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a></font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font> <font color='#000000'>and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message, $orgs);

                    break;

                case CORE_TASK_SOA_ACCEPT_CALAMBA:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $tab = '#tab_soa';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PAY_FIN_PERS, ROLE_HO_PAY_FIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_SOA_BASED:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $tab = '#tab_soa';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_BC_FIN_PERS, ROLE_HO_PAY_FIN, ROLE_PAY_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'> <a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $tab = '#tab_soa';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PAY_FIN_PERS, ROLE_HO_PAY_FIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $tab = '#tab_soa';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_BC_FIN_PERS, ROLE_BC_FIN_HEAD); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $tab = '#tab_soa';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PAY_FIN_PERS, ROLE_HO_PAY_FIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $tab = '#tab_soa';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_FEEDS_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'> <a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved by </font><font color='#354575'><b>".$actor."</b></font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_FHR_FINAL_APPROVAL:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_IO_TASK_COMPLETED
                    );

                    $tab = '#tab_internal_orders';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_CG_LIQ_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>FHR</b></font><font color='#000000'> with FHR </font> <font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font> <font color='#000000'> has been approved. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_PR_UPLOAD_BAVI_APPROVAL:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED
                    );

                    $tab = '#tab_purchase_requests';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PURCH_PERS,ROLE_PURCH_HEAD); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>PR</b> </font> <font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font> <font color='#000000'> has been approved. You may now create </font><font color='#354575'><b>PO</b></font><font color='#000000'> and upload to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_PR_UPLOAD_CON_APPROVAL:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED
                    );

                    $tab = '#tab_purchase_requests';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PURCH_PERS_CON,ROLE_PURCH_HEAD); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>PR</b> </font> <font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font> <font color='#000000'> has been approved. You may now create </font><font color='#354575'><b>PO</b></font><font color='#000000'> and upload to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_ENCODE_ASSET_CODE:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                    );

                    $tab = '#tab_boq';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PROJ_ENG,ROLE_BC_ADMIN,ROLE_ROTI_ADMIN,ROLE_PURCH_PERS_CON); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>Asset Code</b> and <b>IO</b> </font> <font color='#000000'> have been encoded for </font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'>. You may now create</font> <font color='#354575'><b>Purchase Request</b></font><font color='#000000'> and upload to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);https://pria.dev.asiagate.com/dashboard/dashboard
                    break;

                case CORE_TASK_PROJ_BOQ_PROGRESS_APPROVED:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                    );

                    $tab = '#tab_projects';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PAY_FIN_CON,ROLE_BC_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>BOQ Progress</b></font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved. You may now release the 30% check payment.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_RENEWED_CONTRACT:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SRR_TASK_COMPLETED
                    );

                    $tab = '#tab_contracts';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_BC_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>Contract</b></font> <font color='#000000'> with CN </font> <font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been uploaded. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);
                    break;

                case CORE_TASK_PROJ_COMPLETION_APPROVED:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                    );

                    $tab = '#tab_projects';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array(ROLE_PAY_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = "<font color='#354575'> <b>Project Completion Files</b></font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> have been approved. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL:

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED
                    );

                    $tab = '#tab_purchase_orders';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array([ROLE_PROJ_ENG], [ROLE_PAY_FIN_PERS]); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = ["<font color='#354575'> <b>Released Purchase Order </b></font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been uploaded. You may now add </font><font color='#354575'><b>Project</b></font><font color='#000000'>.</font>", "<font color='#354575'> <b>Released Purchase Order </b></font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been uploaded. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'>.</font>"];

                    $multiple       = TRUE;
                    break;

                case CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION;

                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SN_TASK_SIMPLE
                    );

                    $tab = '#tab_site_nominations';
                    $link = get_link_url($module_code, $task_details['reference_num'] , $tab);

                    $roles          = array([ROLE_PROJ_ENG], [ROLE_ROTI_ADMIN]); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $notification   = ["<font color='#354575'> <b>Site Nomination </b></font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved. You may now add </font><font color='#354575'><b>BOQ</b></font><font color='#000000'>.</font>", "<font color='#354575'> <b>Site Nomination </b></font><font color='#e23b3b'><a href='".$link."'> ".$ref_number." </a> </font><font color='#000000'> has been approved. You may now add </font><font color='#354575'><b>Contract</b></font><font color='#000000'>.</font>"];

                    $multiple       = TRUE;
                    break;

                case CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION:
                case CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION_APPEND:
                    $rem_task_details = $this->CI->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                    );
                    // $proj_details = $this->_check_project($task_details['reference_num']);
                    // $additional_flag = $proj_details['additional_flag'];
                    if($task_details['additional_flag'])
                    {
                        $tab = '#tab_projects';
                        $link = get_link_url($module_code, $task_details['reference_num'] , $tab);
                        $roles          = array(ROLE_PROJ_ENG, ROLE_ROTI_ADMIN, ROLE_BC_ADMIN); //specify role to be notified
                        $message        = '';       //notification for mobile
                        $ref_number     = $task_details['reference_num'];
                        $notification   =<<<EOS
                            <font color='#354575'>
                                <b>Projects</b>
                            </font>
                            <font color='#e23b3b'>
                                <a href='$link'>$ref_number</a>
                            </font>
                            <font color='#000000'> requires additional BOQ, PR and PO. You may now add</font>
                            <font color='#354575'>
                                <b>BOQ</b>
                            </font>
                            <font color='#000000'> for additional works.</font>";
EOS;
                    }
                    break;
                default:
                    # code...
                    break;
            }

            if(!EMPTY($roles))
            {
                if(ISSET($rem_task_details['org_code']) AND !EMPTY($rem_task_details['org_code']))
                {
                    $orgs_raw       = $this->CI->pria_mailer_model->get_trans_orgs($rem_task_details['org_code'], $rem_task_details['org_type_code']);

                    $orgs           = (COUNT($orgs_raw) > 0) ? array_column($orgs_raw, 'org_code'): array();
                }

                if($multiple AND is_array($notification))
                {
                    foreach ($notification as $key => $value)
                    {
                        if(!EMPTY($roles[$key]))
                        {
                            $this->CI->pria_notification->import_reminder($roles[$key], $module_code, $value, $message, $orgs, $user_id, $ref_number);
                        }
                    }
                }
                else
                {
                    $this->CI->pria_notification->import_reminder($roles, $module_code, $notification, $message, $orgs, $user_id, $ref_number);
                }
            }

        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Get all org code base on reference number
     * @Return: org_code
     * List all tasks based on category
     */
    private function _get_org_ref($task_details = array())
    {
        try{
                switch ($task_details['core_workflow_task_id']) {

                    //Internal Orders
                    case CORE_TASK_MED_VAC:
                    case CORE_TASK_DOC_DR:
                    case CORE_TASK_DOC_GR:
                    case CORE_TASK_CLEANUP:
                    case CORE_TASK_HARVEST_REPORT:
                    case CORE_TASK_HARVEST_REPORT_APPROVAL:
                    case CORE_TASK_LIVE_SALES_REPORT:
                    case CORE_TASK_FHR:
                    case CORE_TASK_FHR_FINAL_APPROVAL:
                    case CORE_TASK_FHR_AUDIT_SCORE:
                    case CORE_TASK_FHR_APPROVE:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_IO_TASK_COMPLETED
                        );

                    break;

                    //SOA
                    case CORE_TASK_SOA_APPROVE:
                    case CORE_TASK_SOA_TRANSMIT_CALAMBA:
                    case CORE_TASK_SOA_ACCEPT_CALAMBA:
                    case CORE_TASK_SOA_UPLOAD:
                    case CORE_TASK_SOA_APPROVE:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_NORMAL:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_FEEDMILL:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_MANPOWER:
                    case CORE_TASK_SOA_UPLOAD_SOA_BASED:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER:
                    case CORE_TASK_SOA_UPLOAD_FORWARDERS:
                    case CORE_TASK_SOA_ACCEPT_SOA_BASED:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                        );

                    break;

                    //PO
                    case CORE_TASK_PO_DR_BAVI_MARINADES;
                    case CORE_TASK_PO_DR_BFFI;
                    case CORE_TASK_PO_GR_BAVI_MARINADES;
                    case CORE_TASK_PO_TRANSMIT_BFFI:
                    case CORE_TASK_PO_GR_BFFI:
                    case CORE_TASK_PO_DR_BAVI:
                    case CORE_TASK_PO_UPLOAD_APPROVAL;
                    case CORE_TASK_PO_UPLOAD_WO_APPROVAL;
                    case CORE_TASK_PO_UPLOAD_APPROVED;
                    case CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES;
                    case CORE_TASK_PO_DR_APPROVE_BFFI;
                    case CORE_TASK_PO_RELEASED;

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_PO_TASK_COMPLETED
                        );

                    break;

                    //PR
                    case CORE_TASK_PR_UPLOAD_BAVI:
                    case CORE_TASK_PR_UPLOAD_BFFI_MARINADES:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_PR_TASK_COMPLETED
                        );

                    break;

                    //BOQ
                    case CORE_TASK_UPLOAD_BOQ:
                    case CORE_TASK_BOQ_REGIONAL_HEAD_APPROVED:
                    case CORE_TASK_BOQ_MCS_ENGINEERING:
                    case CORE_TASK_BOQ_INDICATE:
                    case CORE_TASK_BOQ_MCS_APPROVED:
                    case CORE_TASK_BOQ_PRES_APPROVED:
                    case CORE_TASK_BOQ_INDICATE_CONFIRM_CONTRACT:
                    case CORE_TASK_ENCODE_ASSET_CODE:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED
                        );

                    break;

                    //PROJECTS
                    case CORE_TASK_PROJECT_PROJECT_PLAN:
                    case CORE_TASK_ENCODE_BOQ_PROGRESS:
                    case CORE_TASK_PROJ_BOQ_PROGRESS_APPROVED:
                    case CORE_TASK_PROJECT_TURNOVER_DATE:
                    case CORE_TASK_PROJECT_COMPLETION_FILE:
                    case CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION:
                    case CORE_TASK_PROJ_COMPLETION_APPROVED:
                    //case CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL:
                    case CORE_TASK_OPENING_DATE:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                        );

                    break;

                    //SITE NOMINATION
                    case CORE_TASK_UPLOAD_SITE_NOMINATION:
                    case CORE_TASK_STORE_OPENING:
                    case CORE_TASK_UPLOAD_IVIEW_MAP:
                    case CORE_TASK_SITES_RECOM_SITE_NOMINATION:
                    case CORE_TASK_SITES_RECOM_SITE_REGIONAL:
                    case CORE_TASK_SITES_RECOM_SITE_PRES:
                    case CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION:
                    case CORE_TASK_ENCODE_PROFIT_COST:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_SN_TASK_COMPLETED
                        );

                    break;

                    //SITE RENEWAL
                    case CORE_TASK_CONTRACTS_UPLOAD:
                    case CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL:
                    case CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL:
                    case CORE_TASK_PRES_APPROVE_CONTRACT_RENEWAL:
                    case CORE_TASK_RENEWED_CONTRACT:

                        $notif_task_details = $this->CI->pria_mailer_model->get_task_details(
                            $task_details['pria_task_id'],
                            EMAIL_NOTIF_SUB_SRR_TASK_COMPLETED
                        );

                    break;

                    default:
                        break;
                }

                if(ISSET($notif_task_details['org_code']) AND !EMPTY($notif_task_details['org_code']))
                {
                    $orgs_raw       = $this->CI->pria_mailer_model->get_trans_orgs($notif_task_details['org_code'], $notif_task_details['org_type_code']);
                    $orgs           = (COUNT($orgs_raw) > 0) ? array_column($orgs_raw, 'org_code'): array();
                }

                return $orgs;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Get detailed task [org and vendor and other details included]
     */
    private function _get_task_details_detailed($pria_task_id, $core_task_id)
    {
        try{
                switch ($core_task_id) {

                    //Internal Orders
                    case CORE_TASK_MED_VAC:
                    case CORE_TASK_DOC_DR:
                    case CORE_TASK_DOC_GR:
                    case CORE_TASK_CLEANUP:
                    case CORE_TASK_HARVEST_REPORT:
                    case CORE_TASK_HARVEST_REPORT_APPROVAL:
                    case CORE_TASK_LIVE_SALES_REPORT:
                    case CORE_TASK_FHR:
                    case CORE_TASK_FHR_FINAL_APPROVAL:
                    case CORE_TASK_FHR_AUDIT_SCORE:
                    case CORE_TASK_FHR_APPROVE:

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_IO_TASK_COMPLETED
                        );

                    break;

                    //SOA
                    case CORE_TASK_SOA_APPROVE:
                    case CORE_TASK_SOA_TRANSMIT_CALAMBA:
                    case CORE_TASK_SOA_ACCEPT_CALAMBA:
                    case CORE_TASK_SOA_UPLOAD:
                    case CORE_TASK_SOA_APPROVE:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_NORMAL:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_FEEDMILL:
                    case CORE_TASK_SOA_UPLOAD_TRUCKERS_MANPOWER:
                    case CORE_TASK_SOA_UPLOAD_SOA_BASED:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL:
                    case CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER:
                    case CORE_TASK_SOA_UPLOAD_FORWARDERS:
                    case CORE_TASK_SOA_ACCEPT_SOA_BASED:

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                        );

                    break;

                    //PO
                    case CORE_TASK_PO_DR_BAVI_MARINADES;
                    case CORE_TASK_PO_DR_BFFI;
                    case CORE_TASK_PO_GR_BAVI_MARINADES;
                    case CORE_TASK_PO_TRANSMIT_BFFI:
                    case CORE_TASK_PO_GR_BFFI:
                    case CORE_TASK_PO_DR_BAVI:
                    case CORE_TASK_PO_UPLOAD_APPROVAL;
                    case CORE_TASK_PO_UPLOAD_WO_APPROVAL;
                    case CORE_TASK_PO_UPLOAD_APPROVED;
                    case CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES;
                    case CORE_TASK_PO_DR_APPROVE_BFFI;
                    case CORE_TASK_PO_RELEASED;

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_PO_TASK_COMPLETED
                        );

                    break;

                    //PR
                    case CORE_TASK_PR_UPLOAD_BAVI:
                    case CORE_TASK_PR_UPLOAD_BFFI_MARINADES:

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_PR_TASK_COMPLETED
                        );

                    break;

                    //BOQ
                    case CORE_TASK_UPLOAD_BOQ:
                    case CORE_TASK_BOQ_REGIONAL_HEAD_APPROVED:
                    case CORE_TASK_BOQ_MCS_ENGINEERING:
                    case CORE_TASK_BOQ_INDICATE:
                    case CORE_TASK_BOQ_MCS_APPROVED:
                    case CORE_TASK_BOQ_PRES_APPROVED:
                    case CORE_TASK_BOQ_INDICATE_CONFIRM_CONTRACT:
                    case CORE_TASK_ENCODE_ASSET_CODE:

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED
                        );

                    break;

                    //PROJECTS
                    case CORE_TASK_PROJECT_PROJECT_PLAN:
                    case CORE_TASK_ENCODE_BOQ_PROGRESS:
                    case CORE_TASK_PROJ_BOQ_PROGRESS_APPROVED:
                    case CORE_TASK_PROJECT_TURNOVER_DATE:
                    case CORE_TASK_PROJECT_COMPLETION_FILE:
                    case CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION:
                    case CORE_TASK_PROJ_COMPLETION_APPROVED:
                    //case CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL:
                    case CORE_TASK_OPENING_DATE:

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                        );

                    break;

                    //SITE NOMINATION
                    case CORE_TASK_UPLOAD_SITE_NOMINATION:
                    case CORE_TASK_STORE_OPENING:
                    case CORE_TASK_UPLOAD_IVIEW_MAP:
                    case CORE_TASK_SITES_RECOM_SITE_NOMINATION:
                    case CORE_TASK_SITES_RECOM_SITE_REGIONAL:
                    case CORE_TASK_SITES_RECOM_SITE_PRES:
                    case CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION:
                    case CORE_TASK_ENCODE_PROFIT_COST:

                        $task_details = $this->CI->pria_mailer_model->get_task_details(
                            $pria_task_id,
                            EMAIL_NOTIF_SUB_SN_TASK_COMPLETED
                        );

                    break;

                    default:
                        break;
                }

                return $task_details;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }


    public function _check_project($reference_id)
	{
		try
		{
			$project = $this->CI->projects_model->get_project(['project_id' => $reference_id], ['additional_flag']);
			return $project;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

    public function _upd_project($reference_id, $additional_flag)
	{
		try
		{
			// $project = $this->CI->projects_model->get_project(['project_id' => $reference_id], ['additional_flag']);

			$this->CI->projects_model->update_project(
                    ['additional_flag' => $additional_flag],
                    ['project_id' => $reference_id]
            );
			// return $project;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}


}