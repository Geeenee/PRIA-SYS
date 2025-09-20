<?php if (!defined('BASEPATH')) exit('No direct script access is allowed'); 

class Mail_action extends Portal_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('pria_mailer_model');
		$this->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');

		$this->module_js	= "mail_action";
		//$this->task_js		= HMVC_FOLDER . "/" . SYSTEM_PORTAL . "/mail_action";
	}

	//public function pria_validate($encrypt_user_id=NULL, $encrypt_id=NULL, $encoded_type=NULL)
	public function pria_validate($encoded_id)
	{
		try
		{
			$data						= array();
			$resources					= array();
			$task_details				= array();
			$task_actions				= array();
			
			$resources['load_css']		= array(CSS_SELECTIZE);
			$resources['load_js']		= array(JS_SELECTIZE, $this->module_js);
			$resources['loaded_init']	= array('MailAction.init();');

			$task_email_link_id 		= base64_url_decode($encoded_id);

			if(EMPTY($task_email_link_id))
				throw new Exception($this->lang->line('err_unauthorized_access'));

			//echo 'EMAIL LINK ID : '.$task_email_link_id; die;

			$page 		   = 'mail_used';

			$email_details = $this->pria_mailer_model->get_task_email_link_details(['task_email_link_id' => $task_email_link_id]);

			if($email_details['active_flag'])
			{
				$page 		   = 'mail_action';

				//$user_id				= $email_details['created_by'];
				$task_id				= $email_details['pria_task_id'];
				$type					= $email_details['email_notification_type'];

				$task_details			= $this->pria_mailer_model->get_task_details($task_id, $type);
				$task_actions			= $this->pria_mailer_model->get_task_actions($task_id);
				$user_id 				= $task_details['resource_id'];
				//print_var_export($task_details);
				$data['task_details']	= $task_details;
				$data['task_actions']	= $task_actions;
				$data['etd']			= encrypt_id($task_id);
				$data['euid']			= encrypt_id($user_id);

				$data['return_tasks']	= $this->tm_model->get_return_tasks($task_id);
			}



		/* 	if(EMPTY($encrypt_user_id) OR EMPTY($encrypt_id) OR EMPTY($encoded_type))
			{
				throw new Exception($this->lang->line('err_unauthorized_access'));
			}

			$user_id				= decrypt_id($encrypt_user_id);
			$task_id				= decrypt_id($encrypt_id);
			$type					= base64_url_decode($encoded_type);

			if(EMPTY($user_id) OR EMPTY($task_id) OR EMPTY($type))
			{
				throw new Exception($this->lang->line('err_unauthorized_access'));
			}

			$task_details			= $this->pria_mailer_model->get_task_details($task_id, $type);
			$task_actions			= $this->pria_mailer_model->get_task_actions($task_id);

			$data['task_details']	= $task_details;
			$data['task_actions']	= $task_actions;
			$data['etd']			= encrypt_id($task_id);
			$data['euid']			= encrypt_id($user_id); */
			
			//$page = ($task_details['task_status_id'] != TASK_STATUS_PENDING) ? 'mail_used' : 'mail_action';

			$data['view_page']		= $this->load->view($page, $data, TRUE);
			
			$this->load->view('mail_template', $data);
			$this->load_resources->get_resource($resources);
		}
		catch(PDOException $e)
		{
    		$msg	= $this->get_user_message($e);
     		show_error($msg);
		}
		catch(Exception $e)
		{
    		$msg	= $this->rlog_error($e, TRUE);
     		show_error($msg);
		}
	}

	public function tag_approve()
	{
		try
		{
			$params 	= get_params();

			$this->task	= modules::load(PORTAL_TRANSACTIONS . '/Task');

			$params 	= $this->_clean_remarks($params);

			$this->task->tag_approve($params['etd'], $params['euid'], $params['task_remarks']);
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

	public function tag_return()
	{
		try
		{
			$params 	= get_params();
		//	print_var_export($params, 'EUID : ',decrypt_id($params['euid'])); die;

			$this->task	= modules::load(PORTAL_TRANSACTIONS . '/Task');

			$params 	= $this->_clean_remarks($params);

			$task_id 	= decrypt_id($params['etd']);

			$return		= $this->tm_model->get_return_tasks($task_id);

			$this->task->tag_return($params['etd'], $params['euid'], $params['task_remarks'], $params['return_task_id']);
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

	public function tag_disapprove()
	{
		try
		{
			$params 	= get_params();

			$this->task	= modules::load(PORTAL_TRANSACTIONS . '/Task');

			$params 	= $this->_clean_remarks($params);

			$this->task->tag_disapprove($params['etd'], $params['euid'], $params['task_remarks']);
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

	private function _clean_remarks($params)
	{
		$params = $this->set_filter( $params )
            ->filter_string('task_remarks')
			->filter();
			
		return $params;	
	}
}