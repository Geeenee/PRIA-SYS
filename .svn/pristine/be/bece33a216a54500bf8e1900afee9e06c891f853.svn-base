<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sign_up extends SYSAD_Controller 
{

	private $module;
	private $controller;
	private $module_js;
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->module 		= MODULE_USER;
		$this->controller 	= strtolower(__CLASS__);
		$this->module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_COMMON."/".$this->controller;
		
		$this->load->model(CORE_USER_MANAGEMENT . '/users_model', 'users', TRUE);
		$this->load->model(CORE_USER_MANAGEMENT . '/organizations_model', 'orgs', TRUE);
		$this->load->model(CORE_USER_MANAGEMENT . '/vendors_model', 'vendors', TRUE);
		$this->load->model('settings_model', 'settings', TRUE);
	}	
	
	public function modal()
	{
		try
		{
			$data 			= array();
			$resources 		= array();
			// $data['orgs'] 	= $this->orgs->get_orgs();
			$data['vendors'] 	= $this->vendors->get_vendor_details();

			$constraints 	= $this->settings->get_settings_value(PASSWORD_CONSTRAINTS);
			$pass_const 	= array();
			
			foreach ($constraints as $row)
			{
				$pass_const[$row['setting_name']] = $row['setting_value'];
			}
			
			$pass_err 			= $this->get_pass_error_msg();
			$data['pass_err'] 	= $pass_err;

			$pass_length 		= $pass_const[PASS_CONS_LENGTH];
			$upper_length 		= $pass_const[PASS_CONS_UPPERCASE];
			$digit_length 		= $pass_const[PASS_CONS_DIGIT];
			$repeat_pass 		= $pass_const[PASS_CONS_REPEATING];
			
			$validate_password_length 	= (intval($pass_length) > 0) ? true : false;
			
			$resources["load_css"] 		= array(CSS_LABELAUTY);
			$resources["load_js"] 		= array(JS_LABELAUTY, $this->module_js);
			
			$resources['loaded_init'] 	= array (
				'materialize_select_init();',
				'SignUp.initResetModal("'.$validate_password_length.'", "'.$pass_length.'", "'.$upper_length.'", "'.$digit_length.'", "'.$pass_err.'", "'.$repeat_pass.'");',
				'SignUp.save();'
			);
		}
		catch(PDOException $e)
		{			
			echo $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			echo $this->rlog_error($e, TRUE);
		}

		$this->load->view("modals/sign_up", $data);
		$this->load_resources->get_resource($resources);
	}
	
	public function process()
	{
		try
		{
			$status 			= ERROR;
			$mail_flag 			= 0;
			$params				= get_params();
			$params["status"] 	= PENDING;

			$account_creator 	= get_setting(ACCOUNT, "account_creator");

			if( $account_creator == VISITOR_NOT_APPROVAL )
			{
				$params["status"] 	= APPROVED;

				$params['role']		= array(
					AUTHENTICATED_USER_ROLE
				);
			}

			// SERVER VALIDATION
			$this->_validate($params);
			
			// GET SECURITY VARIABLES
			$salt 			= $params['salt'];
			$token 			= $params['token'];
			
			// CHECK IF THE SECURITY VARIABLES WERE CORRUPTED OR INTENTIONALLY EDITED BY THE USER
			check_salt(PROJECT_NAME, $salt, $token);

			$email_exist 		= $this->_validate_email($params['email']);
			$username_exist 	= $this->_validate_username($params['username']);
			
			if($email_exist)
			{
				throw new Exception($this->lang->line('email_exist'));
			}

			if($username_exist)
			{
				throw new Exception($this->lang->line('username_exist'));
			}

			// BEGIN TRANSACTION
			SYSAD_Model::beginTransaction();
		
			$audit_action[]	= AUDIT_INSERT;
			$audit_table[]	= SYSAD_Model::CORE_TABLE_USERS;
			$audit_schema[]	= DB_CORE;
			$prev_detail[]	= array();
			
			//get organization information
			if($params['vendor']){
				$where 		= array("deleted_flag" => MAINTAINER_NO, "vendor_code" => $params['vendor']);
				$vendors 	= $this->vendors->get_vendors($where);

				if (!empty($vendors['org_code'])) {
					$params['org'] = $vendors['org_code'];
				}
			}

			// print_r($params);
			// die();

			//Defined additional undefined fields
			$params['contact_type'] 				= 0;
			$password_creation 						= get_setting( PASSWORD_INITIAL_SET, 'password_creator' );
			$params['password_creation']			= SET_ADMINISTRATOR;
			$params['initial_flag'] 				= INITIAL_NO;

			$id 	= $this->users->insert_user($params);
			
			$this->session->set_userdata('user_id', $id);

			$curr_detail[] = $this->users->get_specific_user($id);
				
			$msg 	= $this->lang->line('signup_success');
				
			// GET THE DETAIL AFTER INSERTING THE RECORD
				
			// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
			$activity = "%s has signed up";
			$activity = sprintf($activity, $params['fname'] . ' ' . $params['lname']);

			$this->audit_trail->log_audit_trail(
				$activity,
				$this->module,
				$prev_detail,
				$curr_detail,
				$audit_action,
				$audit_table,
				$audit_schema
			);
		
			$this->session->unset_userdata('user_id');

			SYSAD_Model::commit();

			/*if( $account_creator == VISITOR_NOT_APPROVAL )
			{
				$mail_flag 	= $this->_send_approved_email( $id, APPROVED );
			
			}
			else
			{*/
				$mail_flag 	= $this->_send_sign_up_email($curr_detail);
			// }

			$status 	= SUCCESS;
		}
		catch(PDOException $e)
		{			
			SYSAD_Model::rollback();
			
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			SYSAD_Model::rollback();
			
			$msg = $this->rlog_error($e, TRUE);
		}
	
		$info = array(
			"status" 	=> $status,
			"msg" 		=> $msg,
			"mail_sent" => $mail_flag
		);
	
		echo json_encode($info);
	
	}
	
	private function _validate($params)
	{
		
		$required 		= array();
		$constraints 	= array();

		$required['lname']	= "Last Name";
		$required['fname']	= "First Name";
		$required['email']	= "Email";
		// $required['org']	= "Agency";
		$required['vendor']	= "Vendor";

		$this->check_required_fields( $params, $required );

	}
	
	private function _send_sign_up_email($user_details)
	{	
		try
		{
			$flag 			= 0;
			$email_data 	= array();
			$template_data 	= array();
	
			$salt 			= gen_salt(TRUE);
			$system_title 	= get_setting(GENERAL, "system_title");
			$system_email 	= get_setting(GENERAL, "system_email");
				
			// required parameters for the email template library
			$email_data["from_email"] 	= $system_email;
			$email_data["from_name"] 	= $system_title;
			$email_data["to_email"] 	= array($user_details[0][0]['email']);
			$email_data["subject"] 		= 'Your Pending Registration to ' . $system_title;
				
			// additional set of data that will be used by a specific template
			
			$sys_logo 		 			= get_setting(GENERAL, "system_logo");
			$system_logo_src 			= base_url() . PATH_IMAGES . "logo_white.png";

			if( !EMPTY( $sys_logo ) )
			{
				$root_path 			= $this->get_root_path();
				$sys_logo_path 		= $root_path. PATH_SETTINGS_UPLOADS . $sys_logo;
				$sys_logo_path 		= str_replace(array('\\','/'), array(DS,DS), $sys_logo_path);

				if( file_exists( $sys_logo_path ) )
				{
					$system_logo_src = output_image($sys_logo, PATH_SETTINGS_UPLOADS);
					$system_logo_src = getimagesize($sys_logo_path) ? $system_logo_src : base_url() . PATH_IMAGES . "logo_white.png";
				}
			}

			$template_data["logo"] 			= $system_logo_src;
			
			$template_data["email"] 		= $user_details[0][0]['email'];
			$template_data["system_name"] 	= $system_title;
			$template_data["name"] 			= $user_details[0][0]['fname'] . ' ' . $user_details[0][0]['lname'];
			
			$flag = $this->email_template->send_email_template($email_data, "emails/sign_up", $template_data);
			
			return $flag;
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

	private function _validate_email($email)
	{
		try
		{
			$exist_flag = $this->users->check_email_exist($email);
			
			return $exist_flag['email_exist'];
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

	private function _send_approved_email($id, $status)
	{	
		try
		{
			$user_detail 	= $this->users->get_user_details($id);
			
			$flag 			= 0;
			$email_data 	= array();
			$template_data 	= array();
	
			$system_title 	= get_setting(GENERAL, "system_title");
				
			// required parameters for the email template library
			$email_data["from_email"] 	= get_setting(GENERAL, "system_email");
			$email_data["from_name"] 	= $system_title;
			$email_data["to_email"] 	= array($user_detail['email']);
			$email_data["subject"] 		= ($status == APPROVED) ? 'Activate your Account' : 'Registration Denied';
				
			
			// additional set of data that will be used by a specific template
			$template_data["email"] 		= $user_detail['email'];
			$template_data["password"] 		= base64_url_encode($user_detail['password']);
			$template_data["reason"] 		= $user_detail['reason'];
			$template_data["name"] 			= $user_detail['fname'] . ' ' . $user_detail['lname'];
			$template_data["status"] 		= $status;
			$template_data["system_name"] 	= $system_title;
			$template_data["id"] 			= $id;
				
			$this->email_template->send_email_template($email_data, "emails/account", $template_data);

			$error 							= $this->email_template->get_email_errors();

			if( !EMPTY( $error ) )
			{
				RLog::error( "Email Error" ."\n" . var_export($error, TRUE) . "\n" );
			}

			//$flag = 1;
			$flag = $this->email->print_debugger();
			
			return $flag;
		}
		catch( PDOException $e )
		{
			echo $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			echo $this->rlog_error( $e, TRUE );
		}	
	
	}
		

	private function _validate_username($email)
	{
		try
		{
			$exist_flag = $this->users->check_username_exist($email);
			
			return $exist_flag['username_exist'];
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