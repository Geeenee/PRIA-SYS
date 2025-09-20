<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends Task_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->controller 		= strtolower(__CLASS__);
		$this->module_folder 	= PORTAL_TASK;

		$this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
	}



	public function set_filter_session()
	{
		$params = get_params();

		try
		{
			$display = $params['filter_display'];

			if($display)
				$this->session->set_userdata('show_task_filter', $display);
			else
				unset($_SESSION['show_task_filter']);


		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-06-07 14:26:21
	 * @Desc:
	 * @ReferencedBy:
	 * @Params:
     *    $return    - If true return the html to the function caller.
	 */
	//public function display_task_list($return=FALSE, $enc_ag_code='', $enc_ref_id='')
	public function display_task_list($return=FALSE, $pria_workflow_id='')
	{
		try
		{
			$params 			 = get_params();
			$success  		 	 = FALSE;
			$html 				 = '';
			$msg 				 = '';
			$ag_codes	 		= array();

			/* Comment by kebs
			if( ! $return)
			{
				$pria_workflow_id    	= decrypt_id($params['pwi']);

				$pria_workflow_details	= $this->pwm_model->get_pria_workflow_details(array('pria_workflow_id' => $pria_workflow_id), array('DISTINCT account_group_code, core_workflow_id'));
				$ag_codes				= (ISSET($pria_workflow_details) AND COUNT($pria_workflow_details) > 0)? array_column($pria_workflow_details, 'account_group_code'): array();
				$core_workflow_ids		= (ISSET($pria_workflow_details) AND COUNT($pria_workflow_details) > 0)? array_column($pria_workflow_details, 'core_workflow_id'): array();

				$module_details			= $this->pwm_model->get_tab_module(array('core_workflow_id' => $core_workflow_ids[0], 'ag_code' => $ag_codes[0], 'root_module' => ROOT_TRANSAC ));

				$module_code			= (ISSET($module_details['tab_module_code']) AND !EMPTY($module_details['tab_module_code']))? $module_details['tab_module_code']: NULL;
				//ends
			}
			*/

			$pria_workflow_id    	= ( ! $return) ? decrypt_id($params['pwi']) : $pria_workflow_id;

			$pria_workflow_details	= $this->pwm_model->get_pria_workflow_details(array('pria_workflow_id' => $pria_workflow_id), array('DISTINCT account_group_code, core_workflow_id' , 'reference_id as workflow_reference_id'));

			$ag_codes				= (ISSET($pria_workflow_details) AND COUNT($pria_workflow_details) > 0)? array_column($pria_workflow_details, 'account_group_code'): array();
			$core_workflow_ids		= (ISSET($pria_workflow_details) AND COUNT($pria_workflow_details) > 0)? array_column($pria_workflow_details, 'core_workflow_id'): array();

			$module_details			= $this->pwm_model->get_tab_module(array('core_workflow_id' => $core_workflow_ids[0], 'ag_code' => $ag_codes[0], 'root_module' => ROOT_TRANSAC ));

			$module_code			= (ISSET($module_details['tab_module_code']) AND !EMPTY($module_details['tab_module_code']))? $module_details['tab_module_code']: NULL;

			$workflow_reference_id  = $pria_workflow_details[0]['workflow_reference_id'];

			//If return = TRUE the value of filter_form will be from the function where it is called.
			$filter_params		 	= $params['filter_form'];

			unset($filter_params['filter-keyword']);

			$filter		 		 = $this->_construct_transactions_having($filter_params, $ag_codes);
			$filter_task		 = $this->_construct_tasks_having($filter_params);

			$stages 			 = $this->pwm_model->get_stages_transaction_list($pria_workflow_id, $filter);

			$stage_ids 		 	 = array_column($stages, 'pria_stage_id');

			$tasks 				 = $this->tm_model->get_stage_tasks_details($stage_ids, $filter_task);

			$stages_w_appendable = $this->tm_model->get_stages_with_appendable($pria_workflow_id);

			$task_ids 		     = array_column_recursive($tasks, 'pria_task_id');

			$task_predecessors 	 = $this->tm_model->get_task_predecessors($task_ids);

			$task_actions		 = $this->tm_model->get_all_task_actions($task_ids);

			//Constructs the html for stages and task
			foreach($stages as $s)
			{
					//This is where the additional message is place. E.g "No last medvac delivery yet"
					$append_note = '';

					//Extra msgs
					//$append_note = $this->_get_stage_additional_append_msg($s['core_workflow_stage_id'], $workflow_reference_id);

					//If stage has a task that has an appendable, merge details with the current stage details
					if( ISSET($stages_w_appendable[$s['pria_stage_id']]) )
					{
						$curr_stage 		= $stages_w_appendable[$s['pria_stage_id']][0];

						$display_appendable = $this->_check_workflow_if_appendable($curr_stage['pria_task_id'], $curr_stage['core_workflow_task_id'], $curr_stage['workflow_reference_id']);

						//Added the "EMPTY($append_note)" condition just to check if all conditions is satisfied before display of append
						//if($display_appendable && EMPTY($append_note))
						if($display_appendable)
						{
							$s = array_merge($s, $curr_stage);

							$append_note = $this->_get_stage_additional_append_msg($s['core_workflow_stage_id']);
						}
					}

					$task_html		= $this->_construct_task_list($tasks[$s['pria_stage_id']], $task_predecessors, $task_actions, $module_code, $workflow_reference_id);

					$html 	 	   .= $this->load->view('stage_list', ['stage' => $s, 'tasks' => $task_html, 'append_note' => $append_note], TRUE);
			}

			$success  		 = TRUE;
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);
		}

		if( ! $return)
		{
			echo json_encode([
					'success'  => $success,
					'msg' 		 => $msg,
					'html'		 => $html,
			]);
		}
		else
		{
			if( ! $success)
				throw new Exception($msg);
			else
				return $html;
		}
	}

	//private function _get_stage_additional_append_msg($core_stage_id, $reference_id)
	private function _get_stage_additional_append_msg($core_stage_id)
	{
		try
		{
			$msg = '';

			switch($core_stage_id)
			{
				case CORE_WORKFLOW_STAGE_MEDVAC:
					$msg = 'Last delivery yet to be declared';
				break;

				case CORE_WORKFLOW_STAGE_DOC_DR:
					$msg = 'Last delivery yet to be declared';
				break;
			}

			return $msg;
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

//	private function _construct_task_list($tasks, $predecessors, $reference_id)
	private function _construct_task_list($tasks, $predecessors, $actions, $mod_code = NULL, $pria_workflow_ref_id)
	{
		try
		{
			$html 	 		= '';

			foreach($tasks as $tkey => $t)
			{
					$task_id 				= $t['pria_task_id'];
					$pending_predecessors 	= [];

					if(ISSET($predecessors[$task_id]))
					{
						$task_predecessor_status = array_column($predecessors[$task_id], 'task_status_id');
						//Determines if task has a pending predecessor. If yes, pass setting that will disable the checkbox.
						$pending_predecessors    = array_filter($task_predecessor_status, function($val){
									//return ( EMPTY($val) || $val != TASK_STATUS_DONE ) ? TRUE : FALSE;
									return ( EMPTY($val) || ! in_array($val, [TASK_STATUS_DONE, TASK_STATUS_SKIPPED, TASK_STATUS_APPROVED]) ) ? TRUE : FALSE;
						});
					}

					$special_rule = $this->pria_workflow->_check_task_completion_rules($t['core_workflow_task_id'], $pria_workflow_ref_id);

					$has_pending  = ( ! EMPTY($pending_predecessors) || $special_rule == FALSE) ? TRUE : FALSE;

					//$has_pending  = ( ! EMPTY($pending_predecessors) ) ? TRUE : FALSE;

					$task_actions = array_column($actions[$task_id], 'pria_task_action_id');

					$has_skip 	  = (in_array(TASK_STATUS_SKIPPED, $task_actions)) ? TRUE : FALSE;

					$prev_actor	  = (ISSET($tasks[$tkey-1]['user_id']) AND !EMPTY($tasks[$tkey-1]['user_id']))? $tasks[$tkey-1]['user_id']: NULL;

					$has_skip_prev	= (!$has_pending AND $prev_actor == $this->session->user_id AND ($tkey+1) != count($tasks))? $has_skip: FALSE;
					//Determines/checks extra settings for each task
					//$extra = $this->_extra_task_display_settings($t, $reference_id);

					$html .= $this->load->view('task_list', ['task' => $t, 'has_pending' => $has_pending, 'has_skip' => $has_skip, 'has_skip_prev' => $has_skip_prev, 'mid' => $mod_code], TRUE);
			}

			return $html;
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

	public function tag_skip()
	{
		try
		{
			$flag  		  = ERROR;
			$params 	  = get_params();
			$completed_by = '';
			$cleared_task = [];

			Portal_Model::beginTransaction();

			$task_id 	  = decrypt_id($params['etd']);

			$task_details = $this->tm_model->get_task_details($task_id);

			if( ! in_array($task_details['task_status_id'], [TASK_STATUS_ONGOING, TASK_STATUS_PENDING]))
				throw new Exception('Sorry, status has already been changed to '.strtolower($task_details['task_status']).'. Your not allowed to skip this task.');

			$recipient_id	= NULL;

			if($task_details['account_group_code'] == AG_FORWARDERS AND $task_details['core_workflow_task_id'] == CORE_TASK_SOA_TRANSMIT_CALAMBA)
			{
				$soa	= $this->tm_model->get_from_table(['*'], Portal_Model::PORTAL_TABLE_SOA, FALSE, ['soa_id' => $task_details['reference_id']]);
				$recipient_id	= $soa['recipient_id'];
			}

			$this->tag_task($task_id, TASK_STATUS_SKIPPED, [], $this->session->user_id, $recipient_id);

			$html	= $this->display_task_list(TRUE, $task_details['pria_workflow_id']);

			Portal_Model::commit();

			$flag 	= SUCCESS;

			$msg   = $this->lang->line('data_saved');
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}

		echo json_encode([
			'flag'  		=> $flag,
			'msg'			=> $msg,
			'completed_by'  => $completed_by,
			'cleared_task'	=> $cleared_task,
			'html'			=> $html
		]);
	}

	public function tag_get()
	{
		try
		{
			$flag  			= ERROR;
			$params    	 	= get_params();
			$pria_task_id 	= decrypt_id($params['etd']);

			$task_details 	= $this->tm_model->get_task_details($pria_task_id);

			if( ! EMPTY($task_details['user_id']))
				throw new Exception('Task is already assigned. Please refresh the page.');

			Portal_Model::beginTransaction();

			$this->tag_task($pria_task_id, TASK_STATUS_ONGOING, array('manual_get' => ENUM_YES));

			Portal_Model::commit();

			$flag  		= SUCCESS;

			$msg   		= $this->lang->line('succ_tagging_task_get');
		}
		catch(PDOException $e)
		{
			$msg     = $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg      = $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}

		echo json_encode([
			'flag'  => $flag,
			'msg'   => $msg
		]);
	}

/* 	public function tag_get()
	{
		try
		{
			$flag  		 	= ERROR;
			$params 		= get_params();

			$task_id 	  	= decrypt_id($params['etd']);
			$task_details 	= $this->tm_model->get_task_details($task_id);

			Portal_Model::beginTransaction();

			$prev_detail  	= [[$task_details]];
			$audit_action 	= [AUDIT_UPDATE];
			$audit_schema 	= [DB_PORTAL];
			$audit_table  	= [Portal_Model::PORTAL_TABLE_PRIA_TASKS];
			$module_code  	= $this->get_module_code_per_task_ag_code($task_details['account_group_code']);
			$activity     	= sprintf($this->lang->line('audit_trail_task_status_complete'), $task_details['task_name']);

			$this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['saved_flag' => ENUM_NO], $this->session->user_id, NULL);

			$task_details = $this->tm_model->get_task_details($task_id);

			$curr_detail  = [[$task_details]];

			$this->audit_trail->log_audit_trail($activity, $module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

			Portal_Model::commit();

			$flag 	= SUCCESS;

			$msg   = $this->lang->line('succ_tagging_task_get');
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}


		echo json_encode([
			'flag'  		=> $flag,
			'msg'			=> $msg
		]);
	} */

	/**
	 * @Author		: Kevin Villarojo
	 * @Date		: 2019-05-30 10:28:12
	 * @Desc		: Used for tagging the task as complete.
	 * 				  $param_task_id is passed from quick_add.php
	 *  			  Echo of data is optional because quick add doesn't need it
	 * @ReferencedBy: Quick_Add.php
	 *
	 */
	public function tag_complete($param_task_id = NULL)
	{
		try
		{
			$flag   	  	= ERROR;
			$completed_by 	= '';
			$doc_type 	  	= '';
			$cleared_task 	= [];
			$add_where 	  	= [];
			$due_add_where 	= [];
			$sys_add_where 	= [];
			$sys_notif_role = [];

			Portal_Model::beginTransaction();

			$this->tag_task($param_task_id, TASK_STATUS_DONE, []);

			Portal_Model::commit();

		/* 	$flag 	= SUCCESS;

			$msg   = $this->lang->line('succ_tagging_task_complete'); */
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}

	/* 	if(EMPTY($param_task_id))
		{
			echo json_encode([
				'flag'  		=> $flag,
				'msg'			=> $msg,
				'completed_by'  => $completed_by,
				'cleared_task'	=> $cleared_task
			]);
		} */
	}


	/**
	 * @Author: Kebs Villarojo
	 * @Date: 2019-06-03 1:09:12
	 * @Desc:  Used for disapproving the task and cancelling the succeeding task
	 * @ReferencedBy:  Mail_Action.php
	 */

	public function tag_disapprove($etd=NULL, $euid=NULL, $task_remarks=NULL)
	{
		try
		{
			$flag  				= ERROR;
			$params 			= get_params();

			Portal_Model::beginTransaction();

			$task_id 			= ( ! EMPTY($etd))  ? decrypt_id($etd) : decrypt_id($params['etd']);
			$user_id 			= ( ! EMPTY($euid)) ? decrypt_id($euid) : $this->session->user_id;
			$task_remarks 		= ( ! EMPTY($task_remarks)) ? $task_remarks : $params['task_remark'];

			//Remarks required
			if(EMPTY($task_remarks))
				throw new Exception($this->lang->line('err_required_remarks'));

			$this->tag_task($task_id, TASK_STATUS_DISAPPROVED, [
				'remarks' 			=> $task_remarks,
			], $user_id);

			Portal_Model::commit();

			$flag     = SUCCESS;

			$msg   = $this->lang->line('succ_tagging_task_disapproved');
		}
		catch(PDOException $e)
		{
			$msg     = $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg      = $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}

		echo json_encode([
			'flag'  => $flag,
			'msg'   => $msg
		]);
	}


	/**
	 * @Author		: Christian Aquino
	 * @Date		: 2019-06-03 1:09:12
	 * @Desc		: Used for tagging the task as approve/submit.
	 * 				  Arguments are passed if this function is called by Mail_Action.php
	 * @ReferencedBy:  Mail_Action.php
	 * @Arguments 	:
	 */
	public function tag_approve($etd=NULL, $euid=NULL, $task_remarks=NULL)
	{
		try
		{
			$flag   			= ERROR;
			$params 			= get_params();

			$returned_by 		= '';
			$cleared_task 		= [];
			$add_where 			= [];
			$sys_add_where 		= [];
			$due_add_where 		= [];

			Portal_Model::beginTransaction();

			$task_id 			= ( ! EMPTY($etd))  ? decrypt_id($etd) : decrypt_id($params['etd']);
			$user_id 			= ( ! EMPTY($euid)) ? decrypt_id($euid) : $this->session->user_id;
			$task_remarks 		= ( ! EMPTY($task_remarks)) ? $task_remarks : $params['task_remark'];

			$pria_task_dets = $this->tm_model->get_task_details($task_id);

			$project_completion_task_ids = [CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION,CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION_APPEND];
			if(in_array($pria_task_dets['core_workflow_task_id'], $project_completion_task_ids)) {
				$additional_flag = ( ISSET($params['additional_flag']) && $params['additional_flag'] == ENUM_YES) ? INITIAL_YES : INITIAL_NO;

				$this->pria_workflow->_upd_project($pria_task_dets['reference_id'], $additional_flag);

				$this->pwm_model->update_task(['additional_flag' => $additional_flag], ['pria_task_id' => $task_id]);
			}
			$this->tag_task($task_id, TASK_STATUS_APPROVED, ['remarks' => $task_remarks], $user_id);

			Portal_Model::commit();

			$flag 	= SUCCESS;
			$msg   	= $this->lang->line('succ_tagging_task_approved');
		}
		catch(PDOException $e)
			{
				$msg 	= $this->get_user_message($e);

				Portal_Model::rollback();
			}
			catch(Exception $e)
			{
				$msg  	= $this->rlog_error($e, TRUE);

				Portal_Model::rollback();
			}

			echo json_encode([
				'flag'			=> $flag,
				'msg'			=> $msg,
				'cleared_task'	=> $cleared_task
			]);
	}

	/**
	 * @Author: Christian Aquino
	 * @Date: 2019-06-03 1:09:12
	 * @Desc:  Used for returning the task
	 * @ReferencedBy:  Mail_Action.php
	 */
	public function tag_return($etd=NULL, $euid=NULL, $task_remarks=NULL, $task_return_id=NULL)
	{
		try
		{
			$flag   			= ERROR;
			$params 			= get_params();

			$returned_by 		= '';
			$cleared_task 		= [];
			$return_val 		= '';
			$fields 			= array();
			$sys_notif_role 	= array();

			Portal_Model::beginTransaction();

			$task_id 			= ( ! EMPTY($etd))  ? decrypt_id($etd) : decrypt_id($params['etd']);
			$user_id 			= ( ! EMPTY($euid)) ? decrypt_id($euid) : $this->session->user_id;
			$task_remarks 		= ( ! EMPTY($task_remarks)) ? $task_remarks : $params['task_remark'];
			$task_return_id     = ( ! EMPTY($task_return_id)) ? $task_return_id :  $params['task_return'];


			//Remarks required
			if(EMPTY($task_return_id))
				throw new Exception('Return to is required.');

			//Remarks required
			if(EMPTY($task_remarks))
				throw new Exception($this->lang->line('err_required_remarks'));


			$this->tag_task($task_id, TASK_STATUS_RETURNED, [
				'remarks' 			=> $task_remarks,
				'task_return_id'	=> $task_return_id
			], $user_id);


			Portal_Model::commit();

			$flag 	= SUCCESS;
			$msg   	= $this->lang->line('succ_tagging_task_returned');
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}

		echo json_encode([
			'flag'			=> $flag,
			'msg'			=> $msg,
			'cleared_task'	=> $cleared_task,
			'return_val'	=> $return_val
		]);
	}


/* 	private function _insert_remarks_as_comment($pria_task_id, $remarks, $user_id)
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

			$this->tcm_model->insert_task_comment($comment);
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

	/**
	 * @Author: Kevin Villarojo
	 * @Date:
	 * @Desc: Checks the dependent tasks and its predecessors
	 * @ReferencedBy:
	 * @Return:
	 */
	private function _check_dependent_tasks($task_id, $user_id)
	{
		try
		{
			$user_roles 	  = $this->tm_model->get_users_role(['user_id' => $user_id], ['role_code']);

			$user_roles 	  = array_column($user_roles, 'role_code');

			$cleared_task 	  = [];
			$dependents 	  = $this->tm_model->get_dependent_tasks($task_id);

			$dependents_ids   = array_column($dependents, 'pria_task_id');
			//Get the predecessors of the dependent tasks
			$dependents_prede = $this->tm_model->get_task_predecessors($dependents_ids);

			$actions		  = $this->tm_model->get_all_task_actions($dependents_ids);

			foreach($dependents as $d)
			{
				//Checks if the dependent task is ready to be cleared...
				if( ! $this->pria_workflow->_check_task_completion_rules($d['core_workflow_task_id'], $d['pria_workflow_reference_id']) )
					continue;

				$dep_id   = $d['pria_task_id'];
				$path     = base_url().PORTAL_TRANSACTIONS.'/'.$d['controller'].'?t='.base64_url_encode($dep_id);

				//Clear next task status ( to PENDING) Added by Christian june 6, 2019
				//update returned_flag to Y
				$fields   = array('task_status_id' => NULL);
				$where 	  = array('pria_task_id' => $dep_id);

				$this->pwm_model->update_task($fields, $where);
				//Ends

				$task_actions = array_column($actions[$dep_id], 'pria_task_action_id');

				$has_skip 	  = (in_array(TASK_STATUS_SKIPPED, $task_actions)) ? TRUE : FALSE;

				$skip 		  = $has_skip ? '<i class="material-icons task-skip">forward</i>' : '';

				$task_roles   = $this->tm_model->get_task_roles(['pria_task_id' => $dep_id], ['role_code']);
				$task_roles   = array_column($task_roles, 'role_code');

				if( ! EMPTY(array_intersect($user_roles, $task_roles)))
				{
					$is_role 	= TRUE;
					$anchor 	= '<a href="'.$path.'" class="task-name">'.$d['task_name'].'</a> <span class="completed-by"></span>';
				}
				else
				{
					$is_role 	= FALSE;
					$anchor 	= $d['task_name'];
				}

				//Set the response per task cleared. E.g. path and num id to be used in javascript
				$response = [
					'num' 		=> $dep_id,
					'skip'		=> $skip,
					'anchor'	=> $anchor,
					'is_role'	=> $is_role
				];

				//Check if dependent has other predecessors. If no, enable, else, check.
				if( ISSET($dependents_prede[$dep_id]) )
				{
					$result = $this->_check_dependent_predecessor($dependents_prede[$dep_id]);

					if($result)
						$cleared_task[] = $response;
				}
				else
				{
					$cleared_task[] = $response;
				}
			}

			return $cleared_task;
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
	 * @ReferencedBy:
	 */
	/* private function _check_dependent_predecessor($predecessors)
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
	} */


	/**
	 * @Author: Christian Aquino
	 * @Date:
	 * @Desc: Checks the return task details of specific task
	 * @ReferencedBy:
	 */
	private function _check_return_values($task_id)
	{
		try
		{
			$return = TRUE;
			$return = $this->tm_model->get_return_tasks($task_id);

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

/* 	private function _update_data_upon_complete($reference_id, $core_workflow_task_id)
	{
		try
		{
			switch($core_workflow_task_id)
			{
				case CORE_TASK_ENCODE_PROFIT_COST:
					$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

					$this->sn_model->update_site(['status_code' => STATUS_COMPLETED], ['site_id' => $reference_id]);
				break;

				case CORE_TASK_ENCODE_ASSET_CODE:
					$this->load->model(FOLDER_BOQ.'/boq_model', 'bq_model');

					$this->bq_model->update_boq(['status_code' => STATUS_COMPLETED], ['boq_id' => $reference_id]);
				break;

				case CORE_TASK_OPENING_DATE:
					$this->load->model(FOLDER_PROJECTS.'/projects_model', 'pj_model');

					$this->pj_model->update_project(['status_code' => STATUS_COMPLETED], ['project_id' => $reference_id]);
				break;


				// case CORE_TASK_ENCODE_PROFIT_COST:
				// 	$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

				// 	$this->sn_model->update_site(['status_code' => STATUS_COMPLETED], ['site_id' => $reference_id]);
				// break;
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


	public function append_stage()
	{
		try
		{
			$flag                    = ERROR;
			$params                  = get_params();
			$html 				     = '';
			$msg 					 = '';

			//Refers to the current (task list) core workflow id
			$core_workflow_id 		 = base64_url_decode($params['wid']);

			$core_workflow_stage_id  = base64_url_decode($params['wsid']);

			$appendable_workflow_id  = base64_url_decode($params['awid']);

			$pria_workflow_id		 = base64_url_decode($params['pwid']);

			$pria_task_id			 = base64_url_decode($params['tid']);

			$task_details 			 = $this->tm_model->get_task_details($pria_task_id);

			$appendable   			 = $this->_check_workflow_if_appendable($pria_task_id, $task_details['core_workflow_task_id'], $task_details['reference_id']);

			$append_workflow_details = $this->pwm->get_core_workflow(['workflow_id' => $appendable_workflow_id]);

			if( ! $appendable)
				throw new Exception('Sorry, but you cannot add another <b>'.$append_workflow_details['workflow_name'].'</b> anymore.');

			$sequence_no 	  		=  $this->tm_model->get_stage_seq_where_to_append($core_workflow_id, $core_workflow_stage_id, $pria_workflow_id);


			if(EMPTY($sequence_no))
			{
				$max_stage 			= $this->tm_model->get_pria_workflow_stage_details(['pria_workflow_id' => $pria_workflow_id], ['MAX(sequence_no) as sequence_no']);
				$sequence_no		= $max_stage[0]['sequence_no'];
			}

			//echo 'Seq #'.$sequence_no; die;
			Portal_Model::beginTransaction();

			$this->load->library('pria_workflow');

			$this->pria_workflow->append_stage($pria_workflow_id, $sequence_no, $appendable_workflow_id, $this->session->user_id, FALSE, FALSE, TRUE);

			//throw new Exception('OOPSS NAGLOGOUT');

			$html  = $this->display_task_list(TRUE, $pria_workflow_id);

			Portal_Model::commit();

			$flag 	= SUCCESS;
			$msg   	= 'Task successfully added.';
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);

			Portal_Model::rollback();
		}

		echo json_encode([
			'flag'  => $flag,
			'msg'   => $msg,
			'html'	=> $html
		]);
	}

	public function modal_append_task()
	{
		try
		{
			$params       		= get_params();
			/* $ag_code      = decrypt_id($params['ag']);
			$reference_id = decrypt_id($params['id']);	 */
			$pria_workflow_id 	= decrypt_id($params['pwi']);
			$trans_ref 	  		= $params['ref_num'];

			$resources	  = [
				'loaded_init' => ['selectize_init();', 'Task.appendTask();']
			];

			//$workflows 	  = $this->tm_model->get_appendable_workflow_by_ag_reference_id($reference_id, $ag_code);
			$workflows 	  = $this->tm_model->get_appendable_workflow_by_pria_workflow_id($pria_workflow_id);
			$task_options = [];

			foreach($workflows as $w)
			{
				$reference_id 		= $w['reference_id'];
				$display_appendable = $this->_check_workflow_if_appendable($w['core_workflow_task_id'], $reference_id);

				if($display_appendable)
				{
					//$pria_workflow_id = $w['pria_workflow_id'];
					$sequence_no 	  =  $this->tm_model->get_stage_seq_where_to_append($w['core_workflow_id'], $w['core_workflow_stage_id'], $pria_workflow_id);

					$task_options[] = [
						'workflow_name' => $w['appendable_workflow_name'],
						'value'			=> encrypt_id($w['appendable_workflow_id'].'/'.$sequence_no.'/'.$pria_workflow_id)
					];
				}
			}

			//$this->load->view(PORTAL_TRANSACTIONS.'/task_append', ['trans_ref' => $trans_ref, 'options' => $task_options, 'ref_id' => encrypt_id($reference_id), 'ag_code' => encrypt_id($ag_code)]);
			$this->load->view(PORTAL_TRANSACTIONS.'/task_append', ['trans_ref' => $trans_ref, 'options' => $task_options, 'pria_workflow_id' => encrypt_id($pria_workflow_id)]);

			$this->load_resources->get_resource($resources);
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			$this->error_modal($msg);
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_modal($msg);
		}
	}

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-08-07 08:03:26
	 * @Desc: Checks if the workflow specified can still be appended
	 * $pria_task_id - refers to the task kung saan nakaset up na may appendable
	 * @ReferencedBy:
	 */
	private function _check_workflow_if_appendable($pria_task_id, $core_task_id, $reference_id, $user_id=NULL)
	{
		try
		{
			$display_appendable = TRUE;

			if(EMPTY($user_id))
				$user_id = $this->session->userdata('user_id');

			//1st layer - checks if the user has the role indicated in the first task
			$task_roles = $this->tm_model->get_task_roles(['pria_task_id' => $pria_task_id, 'actor_flag' => INITIAL_YES], ['role_code']);
			$task_roles = array_column($task_roles, 'role_code');

			$user_roles = $this->tm_model->get_users_role(['user_id' => $user_id], ['role_code']);
			$user_roles = array_column($user_roles, 'role_code');
			//print_var_export($user_roles, $task_roles, $pria_task_id); die;
			if( ! array_intersect($task_roles, $user_roles))
				return FALSE;

			//2nd layer - Checks if status of predecessor is done or approve. If no, throw error
			$predecessors = $this->tm_model->get_next_predecessors(['pria_task_id' => $pria_task_id], ['pre_pria_task_id']);

			if( ! EMPTY($predecessors))
			{
				foreach($predecessors as $key => $val)
				{
					$task_details = $this->tm_model->get_task_details($val['pre_pria_task_id']);

					if( ! in_array($task_details['task_status_id'], [TASK_STATUS_DONE, TASK_STATUS_APPROVED]) )
						$display_appendable = FALSE;
				}
			}

			if( ! $display_appendable)
				return $display_appendable;

			$dr_tasks		= array();
			$with_ongoing	= FALSE;

			switch($core_task_id)
			{
				case CORE_TASK_MED_VAC:
					$display_appendable = ( $this->pria_workflow->_check_w_last_dr(DR_MEDVAC, $reference_id) ) ? FALSE : TRUE;
					$dr_tasks			= array(CORE_TASK_MED_VAC, CORE_TASK_MED_VAC_APPEND);
				break;

				case CORE_TASK_DOC_DR:
				//case CORE_TASK_DOC_GR:
					//$display_appendable = ( $this->pria_workflow->_check_w_last_dr(DR_DOCDR, $reference_id) ) ? FALSE : TRUE;
					//print_var_export($this->pria_workflow->_check_w_last_dr(DR_MEDVAC, $reference_id), $reference_id);

					$medvac 			= $this->pria_workflow->_check_w_last_dr(DR_MEDVAC, $reference_id);

					$display_appendable = ( $this->pria_workflow->_check_w_last_dr(DR_DOCDR, $reference_id) || $medvac == FALSE ) ? FALSE : TRUE;
					$dr_tasks			= array(CORE_TASK_DOC_DR, CORE_TASK_DOC_DR_APPEND);
				break;
				//For the meantime lang to
				case CORE_TASK_PO_DR_BAVI:
				case CORE_TASK_PO_DR_BAVI_MARINADES:
					/*
						$this->load->model(PORTAL_TRANSACTIONS.'/po/po_model', 'po_model');

						$po_details 		= $this->po_model->get_po(['po_id' => $reference_id], ['po_amount', 'remaining_amount']);

						if( EMPTY(round($po_details['remaining_amount'])) )
							return FALSE;
					*/
				case CORE_TASK_PO_DR_BFFI:
				//case CORE_TASK_PO_GR_BAVI_MARINADES:
					$display_appendable = ( $this->pria_workflow->_check_w_last_dr(DR_PO, $reference_id) ) ? FALSE : TRUE;

					if($core_task_id == CORE_TASK_PO_DR_BFFI)
					{
						$dr_tasks		= array(CORE_TASK_PO_DR_BFFI);
					}
					else
					{
						$dr_tasks		= array(CORE_TASK_PO_DR_BAVI);
					}
				break;

				case CORE_TASK_PO_TRANSMIT_BFFI:
					$display_appendable = TRUE;
				break;

				case CORE_TASK_PROJECT_COMPLETION_FILE:
					$task_details = $this->tm_model->get_task_details($pria_task_id);

					$display_appendable = $task_details['task_status_id'] == TASK_STATUS_DONE ? TRUE : FALSE;
				break;


				default:
					throw new Exception('No appendable checking set');
			}

			if($display_appendable AND count($dr_tasks) > 0)
			{
				$with_ongoing_dr	= $this->tm_model->get_appendable_ongoing($pria_task_id, $dr_tasks);

				if(ISSET($with_ongoing_dr['pria_task_id']) AND !EMPTY($with_ongoing_dr['pria_task_id']))
				{
					$display_appendable	= FALSE;
					$with_ongoing		= TRUE;
				}
			}

			return $display_appendable;
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



	public function get_return_values()
	{
		try
		{

			$params 	= get_params();
			$task_id  	= decrypt_id($params['etd']);

			$return_val = TRUE;
			$return_val = $this->tm_model->get_return_tasks($task_id);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}

		echo json_encode([
            'return_val'   	=> $return_val
        ]);
	}

	public function hello()
	{
		echo 'Hello world!';
	}

}