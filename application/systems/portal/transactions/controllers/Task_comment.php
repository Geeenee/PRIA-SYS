<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_comment extends Task_Controller 
{
	
	//Audit trail variables ( as of now optional )
	protected $audit_table	  = '';
	protected $audit_activity = '';
	protected $audit_schema   = [];
	protected $audit_action   = [];
	protected $audit_prev     = [];
	protected $audit_curr     = [];

	public function __construct()
	{
		parent::__construct();
		
		$this->controller 		= strtolower(__CLASS__);
		$this->module_folder 	= PORTAL_TASK;

		$this->load->model('task_comment_model', 'tcm_model');
		$this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
		$this->load->library('pria_overview');

		//Initial audit trail setup
		$this->con_table 		= Portal_Model::PORTAL_TABLE_PRIA_TASK_COMMENTS;
		$this->audit_table 		= [$this->con_table];
		$this->audit_schema 	= [DB_PORTAL];
	}


	public function save_comment()
	{
		try
		{ 
			$flag   = ERROR;
			$html   = '';
			
			$params = $this->_validate();
			
			Portal_Model::beginTransaction();
		
			$module_code			= $params['module_code'];
			$pria_task_id			= $params['pria_task_id'];
			$pria_task_comment_id 	= $params['pria_task_comment_id'];
			$user_id	 			= $this->session->user_id;
			$date	 	 			= date(FORMAT_DB_DATETIME);
			$comment 				= $params['comment'];
			$message 				= '';

			//Added by Christian
			//Starts
			$task_details   = $this->tm_model->get_task_details($pria_task_id);
			$user_info 		= $this->users_model->get_user_details($user_id);
			$user 			= $user_info['fname'].' '.$user_info['lname'].'';
			$task_name 		= "<font color='8F44A9'> ".$task_details['doc_name']." </font>";
			//Ends

			$fields  = [
				'pria_task_id' 		=> $pria_task_id,
				'pria_task_comment' => $comment
			];
			
			$where 	 			= ['pria_task_comment_id' => $pria_task_comment_id];

			if( ! $pria_task_comment_id)
			{
				$this->audit_activity 	= 'audit_trail_task_comment_add';
				$this->audit_prev		= [array()];

				$fields['created_by'] 	= $user_id;
				$fields['created_date'] = $date;

				$pria_task_comment_id 	= $this->tcm_model->insert_task_comment($fields);	

				$where 	 				= ['pria_task_comment_id' => $pria_task_comment_id];

				$overview_type		   	= OVERVIEW_TYPE_ADD_TASK_COMMENT;

				$this->audit_action 	= [AUDIT_INSERT];


				//starts of system notification
				$notification 	= "<font color='#e23b3b'>".$user."</font> <font color='#000000'> commented on</font>".$task_name."<font color='#e23b3b'>(List: ".$task_details['reference_num'].")</font>";
				//Ends

				
			}	
			else
			{  
				$this->audit_prev		= [$this->tcm_model->get_details_for_audit($this->con_table, $where)];

				//Check if the logged in user is the same as the one who created the comment
				if($this->audit_prev[0][0]['created_by'] != $user_id)
					throw new Exception($this->lang->line('invalid_action'));

				$this->audit_activity 	= 'audit_trail_task_comment_update';

				$fields['modified_by'] 	 = $user_id;
				$fields['modified_date'] = $date;
				
				$this->tcm_model->update_task_comment($where, $fields);

				$overview_type		   = OVERVIEW_TYPE_EDIT_TASK_COMMENT;
		
				$this->audit_action 	= [AUDIT_UPDATE];

				//starts of system notification
				$notification 	= "<font color='#e23b3b'>".$user."</font> <font color='#000000'> edited a comment on</font>".$task_name."<font color='#e23b3b'>(List: ".$task_details['reference_num'].")</font>";
				//ends
			}

			//Starts
			$encode_link    = '/'.$task_details['controller'].'?t='.base64_url_encode($pria_task_id);
        	$notify_who = array(
                'notification_icon'         => 'speaker_notes',
                'notification_mobile'       => NULL,
                'notify_users'              => array($task_details['user_id']),
                'notify_orgs'               => array(), //no default data
                'notify_roles'              => array(),
                'module_code'               => $module_code,
                'displayed_socket_flag'     => NO_FLAG,
                'listed_flag'               => YES_FLAG
            );
            $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;
			$this->notify->insert_notification($notification, $notify_who, $user_id);
			//Ends

			$overview_details   	= [
				'comment' 				=> $comment, 
				'reference' 		 	=> $task_details['reference_id'], 
				'created_by' 		 	=> $user_id,
				'created_date' 		 	=> $date,
				'account_group_code' 	=> $task_details['account_group_code'],
				'pria_task_comment_id'	=> $pria_task_comment_id
			];

			$overview_details 		= array_merge($task_details, $overview_details);
			
			//Get the transaction module code not the tab module code
			$parent_module_code 	= $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

			$this->pria_overview->log_overview($parent_module_code, $overview_type, $overview_details);

			$this->audit_curr	= [$this->tcm_model->get_details_for_audit($this->con_table, $where)];

			$comments 			= $this->tcm_model->get_task_comment_details_by_comment_id($pria_task_comment_id);

			//If save is called but task is not ongoing.
			if($comments[0]['task_status_id'] == TASK_STATUS_PENDING) 
					throw new Exception($this->lang->line('invalid_action'));

			$activity 			= sprintf($this->lang->line($this->audit_activity), $comments[0]['task_name']);
		
			$html    			= $this->load->view($this->tpl_task_comments, ['comments' => $comments, 'task_status' => $task_details['task_status_id']], TRUE);
			$this->audit_trail->log_audit_trail(
				$activity, 
				$module_code, 
				$this->audit_prev, 
				$this->audit_curr, 
				$this->audit_action, 
				$this->audit_table, 
				$this->audit_schema
			);
            
			
			Portal_Model::commit();
		
			$flag 	 = SUCCESS;

			$msg    = sprintf($this->lang->line('data_spec_saved'), 'Comment');
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
			'html'  => $html
		]);
	}
    
	private function _validate()
	{
		try
		{
			$params = get_params();

			$params = $this->set_filter( $params )
            ->filter_string('tid')
            ->filter_string('comment')
			->filter();
			
			$pria_task_comment_id = '';
						
			$pria_task_id 		  = decrypt_id($params['tid']);
			
			if( ! EMPTY($params['tci']))
				$pria_task_comment_id = decrypt_id($params['tci']);

			$params['comment']	= $this->input->post('comment');

			/* Validate the required fields */
			$required 	  = ['comment' => 'Comment'];

			$this->check_required_fields($params, $required);
		
			/* Validate constraints */			
            $constraints['module']  = [
                'data_type'   => 'db_value',
                'name'        => 'Module',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'module_code',
                'table'       =>  Portal_Model::CORE_MODULES
            ];
			
			$data        = $this->validate_inputs($params, $constraints);
			
			return [
				'comment' 				=> $params['comment'],
				'pria_task_id' 			=> $pria_task_id,
				'pria_task_comment_id' 	=> $pria_task_comment_id,
				'module_code'			=> $data['module']
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
	
    public function delete_comment()
    {
        try
        {
            $flag    				= ERROR;
			$params  				= get_params();
			
			$pria_task_comment_id 	= decrypt_id($params['tci']);

			$module_code 			= $params['module'];

			$constraints['module']  = [
                'data_type'   => 'db_value',
                'name'        => 'Module',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'module_code',
                'table'       =>  Portal_Model::CORE_MODULES
            ];
			
			$this->validate_inputs($params, $constraints);

			Portal_Model::beginTransaction();
			
			$where 		 			= ['pria_task_comment_id' => $pria_task_comment_id];
			$comment 				= $this->tcm_model->get_task_comment_details_by_comment_id($pria_task_comment_id);
			$pria_task_id			= $comment[0]['pria_task_id'];

			$this->audit_action 	= [AUDIT_DELETE];
			$this->audit_activity 	= sprintf($this->lang->line('audit_trail_task_comment_delete'), $comment[0]['task_name']);
			$this->audit_prev		= [$this->tcm_model->get_details_for_audit($this->con_table, $where)];
			$this->audit_curr		= [array()];

			//Check if the logged in user is the same as the one who created the comment
			if($this->audit_prev[0][0]['created_by'] != $this->session->user_id)
				throw new Exception($this->lang->line('invalid_action'));

			$this->tcm_model->delete_task_comment($where);
			
			$task_details  	 	= $this->tm_model->get_task_details($pria_task_id);
			//Get the transaction module code not the tab module code
			$parent_module_code = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

			$this->pria_overview->delete_log(['module_code' => $parent_module_code, 'reference' => $pria_task_comment_id, 'type' => ['IN', [OVERVIEW_TYPE_ADD_TASK_COMMENT, OVERVIEW_TYPE_EDIT_TASK_COMMENT]]]);

			$this->audit_trail->log_audit_trail(
				$this->audit_activity, 
				$module_code,
				$this->audit_prev, 
				$this->audit_curr, 
				$this->audit_action, 
				$this->audit_table, 
				$this->audit_schema
			);
            
            Portal_Model::commit();
        
            $flag 	= SUCCESS;
            $msg    = sprintf($this->lang->line('data_spec_deleted'), 'Comment');
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
            'msg'   => $msg
        ]);
    }
}