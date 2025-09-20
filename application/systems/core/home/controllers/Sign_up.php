<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sign_up extends SYSAD_Controller 
{
	
	private $controller;
	
	private $module_js;
	private $path;
	private $table_id;

	private $approve_per;

	private $status_drop 	= array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->controller 	= strtolower(__CLASS__);
		$this->module_code 	= MODULE_SIGN_UP_APPROVAL;
		$this->module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_DASHBOARD."/".$this->controller;
		$this->path 		= CORE_DASHBOARD."/".$this->controller."/get_user_list/0";
		$this->table_id 	= "user_approval_table";
		
		$this->load->model('Sign_up_model', 'sign_up');
		$this->load->model(CORE_USER_MANAGEMENT . '/users_model', 'users', TRUE);
		$this->load->model(CORE_USER_MANAGEMENT . '/roles_model', 'roles', TRUE);

		$this->approve_per 	= $this->permission->check_permission($this->module_code 	, ACTION_APPROVE);

		$this->status_drop 	= array(
			PENDING => "Pending", APPROVED => "Approved", DISAPPROVED => "Disapproved"
		);
	}

	public function index()
	{
		try
		{
			
			$this->redirect_module_permission($this->module_code);

			$data 			= array();
			$resources 		= array();

			$roles 			= $this->roles->get_roles();

			$data['roles'] 			= $roles;
			$data['statistics'] 	= $this->users->get_user_status_count();

			$role_json 				= json_encode( $roles );

			$data['role_json'] 		= $role_json;

			$resources['load_css'] 	= array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE);
			$resources['load_js'] 	= array(JS_DATATABLE, JS_DATATABLE_MATERIAL, $this->module_js);
			$resources['load_materialize_modal'] = array (
				'modal_user_details'=> array (
					'title' 		=> "User Details",
					'size' 			=> "xs",
					'module' 		=> CORE_DASHBOARD,
					'controller' 	=> __CLASS__
				)
			);

			$datatable_options 		= array(
				'table_id' 			=> $this->table_id, 
				'path' 				=> $this->path, 
				'advanced_filter'	=> true,
				'with_search'		=> true,
				'post_data' 		=> array(
					'status_sign_up'=> '0'
				),
				'search_func' 		=> 'Sign_up.search_func(search_params);'

			);

			$datatable_disappr 		= $datatable_options;
			$datatable_appr 		= $datatable_options;

			$datatable_disappr['post_data']	= array(
				'status_sign_up'		=> DISAPPROVED
			);

			$datatable_appr['post_data']	= array(
				'status_sign_up'		=> APPROVED
			);

			$resources['datatable'] = $datatable_options;

			$json_datatable_options 		= json_encode( $datatable_options );
			$json_datatable_appr_options 	= json_encode( $datatable_appr );
			$json_datatable_disappr_options = json_encode( $datatable_disappr );
			
			$resources['loaded_init'] = array(
				'materialize_select_init();',
				"Sign_up.initObj('".$json_datatable_options."');",
				"refresh_datatable('".$json_datatable_options."','#ctr_pending');",
				"refresh_datatable('".$json_datatable_disappr_options."','#ctr_disapproved');",
				"refresh_datatable('".$json_datatable_appr_options."','#ctr_approved');"
			);

			$data['status_arr'] 	= $this->status_drop;

			$this->template->load('sign_up', $data, $resources);
		}
		catch(PDOException $e)
		{			
			$msg = $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	
	public function get_user_list($filter_status = NULL)
	{
		
		// Variables needed for the datatable 		
		$total_records	= $display_records = $flag = 0;
		$table_data		= array();
		$msg			= $this->lang->line('err_page_500_heading');
		
		try 
		{
			$params	= get_params();

			if( ISSET( $params['search_status_sign_up'] ) )
			{
				$filter_status 	= $params['search_status_sign_up'];
			}
			
			if( ! is_int($filter_status) )
				$filter_status = $this->decrypt($filter_status);
				
			
			
			$total_records		= $this->users->get_user_list($filter_status);

			$records_info 		= $this->users->get_user_list($filter_status, $params);
			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];
			
			for($itr = 0; $itr < $display_records; $itr++)
			{
				$record = $records[$itr];
				$avatar = $this->_construct_avatar($record);
				$status	= $record["sys_param_code"];
								
				$account_status = ($status === STATUS_APPROVED) ? '<div class="mute m-t-xs"><em>-- Unauthenticated --</em></div>' : '';
				
				// Construct table actions
				$encrypt_id	= $this->encrypt($record["user_id"]);				
				$salt		= gen_salt();
				$actions	= '';				
	
				
				switch($status)
				{
					case STATUS_APPROVED:
					case STATUS_DISAPPROVED:					
						$actions.= "<div><a href='javascript:;' class='tooltipped' data-tooltip='Resend Email' data-position='bottom' data-delay='50' onclick=\"Sign_up.resendEmail('".$encrypt_id."','".base64_url_encode($status)."','".$record['email']."')\"><i class='material-icons'>markunread</i></a></div>";
					break;
					
					case STATUS_PENDING:					
						if($this->approve_per)
						{					
							$actions.= "<a href='javascript:;' class='waves-effect waves-light approve tooltipped popmodal-dropdown' data-id-selector='approve_id' data-ondocumentclick-close='false' data-ondocumentclick-close-prevent='e' data-id='".$encrypt_id."' data-placement='rightCenter' data-showclose-but='false' data-popmodal-bind='#approve_content' data-tooltip='Approve' data-position='bottom' data-delay='50'><i class='material-icons'>done</i></a>";
							$actions.= "<a href='javascript:;' class='waves-effect waves-light disapprove tooltipped popmodal-dropdown' data-id-selector='reject_id' data-id='".$encrypt_id."' data-ondocumentclick-close='false' data-ondocumentclick-close-prevent='e'  data-placement='rightCenter' data-showclose-but='false' data-popmodal-bind='#reject_content' data-tooltip='Disapprove' data-position='bottom' data-delay='50'><i class='material-icons'>clear</i></a>";
						}
					break;
				}
				
				if( ($itr + 1) == $display_records)
				{
					$resources['load_js'] 		= array(JS_POP_MODAL, $this->module_js);
					$resources['preload_modal'] = array("modal_user_details");
					$resources['loaded_doc_init'] 	= array(
						"selectize_init();",
						"Sign_up.initTable();"
					);
					$actions.= $this->load_resources->get_resource($resources, TRUE);
				}
				
					
				$table_data[] = array(
					$avatar . $record["fname"] . ' ' . $record["lname"],
					'<span class="font-semibold">' . $record['org_name'] . '</span>',
					'<em>' . $record['job_title'] . '</em>',					
					$record["email"],
					'<div class="center-align">' . $record['sys_param_name'] . $account_status . '</div>',
					'<div class="table-actions">' . $actions . '</div>'
				);
			}
			
			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
			
		}
		
		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}
	
		private function _construct_avatar($record)
	{
		try 
		{
			$photo_path = '';
			$img_src	= base_url().PATH_IMAGES . "avatar.jpg";
			if( ! empty($record['photo']) )
			{
				$root_path  = $this->get_root_path();
				$photo_path = $root_path . PATH_USER_UPLOADS . $record['photo'];
				$photo_path = str_replace(array('\\','/'), array(DS,DS), $photo_path);
				
				if( file_exists( $photo_path ) )
				{
					
					$check_upl = $this->check_custom_path();
					
					if( ! empty($check_upl) )
					{
						$img_src = output_image($record['photo'], PATH_USER_UPLOADS);
					}
					else
					{
						$img_src = base_url() . PATH_USER_UPLOADS . $record['photo'];
					}
				}				
			}
			
			$contact_flag = ($record['contact_flag'] == 1) ? "<i class='material-icons small'>fiber_manual_record</i>" : "";
			
			if( ! empty($photo_path) )
			{
				$img = '<img class="avatar" width="20" height="20" src="'.$img_src.'" /> ' . $contact_flag;
			}
			else
			{
				$img = '<img class="avatar default-avatar" data-name="'.$record['fname'].'" /> ' . $contact_flag;
			}
			
			return '<span class="table-avatar-wrapper">' . $img . '</span>';

		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
	
	public function modal($id = NULL, $salt = NULL, $token = NULL)
	{
		
		try
		{
			// $this->redirect_off_system($this->module);

			$data 	= array();
			
			$id 	= base64_url_decode($id);
				
			// CHECK IF THE SECURITY VARIABLES WERE CORRUPTED OR INTENTIONALLY EDITED BY THE USER
			check_salt($id, $salt, $token);
				
			$data["user"] = $this->users->get_user_details($id);
			
			$this->load->view("modals/dashboard_user_details", $data);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_modal( $msg );
		}
		catch(Exception $e)
		{
			$msg 	= $this->rlog_error( $e, TRUE );

			$this->error_modal( $msg );
		}	
	}

	private function _validate_update_user($params, $action = NULL)
	{
		$required 		= array();
		$constraints	= array();

		if( ISSET( $params['main_role'] ) )
		{

			$required['main_role']	= 'Main Role';

			if( !EMPTY( $params['role'] ) AND !EMPTY( $params['role'][0] ) )
			{
				if( in_array( $params['main_role'][0], $params['role'] ) )
				{
					throw new Exception('There may be a duplicate role in both main role and other roles.');
				}
			}
		}

		$this->check_required_fields( $params, $required );

		$this->validate_inputs( $params, $constraints );
	}
		
	public function update_user_status()
	{
		try
		{
			// $this->redirect_off_system($this->module);

			$status = ERROR;
			$params	= get_params();

			$this->_validate_update_user( $params );

			$permission 		= $this->approve_per;
			$per_msg 			= $this->lang->line( 'err_unauthorized_approve_disapprove_user' );

			if( !$permission )
			{
				throw new Exception( $per_msg );
			}
	
			// GET SECURITY VARIABLES
			$decode_id	= $this->decrypt($params['id']);
			$id			= filter_var($decode_id, FILTER_SANITIZE_NUMBER_INT);
			// BEGIN TRANSACTION			
			SYSAD_Model::beginTransaction();
			
			$audit_table[]	= SYSAD_Model::CORE_TABLE_USERS;
			$audit_schema[]	= DB_CORE;
			$audit_action[]	= AUDIT_UPDATE;
								
			$params['user_id'] 	= $decode_id;
			// GET THE DETAIL FIRST BEFORE UPDATING THE RECORD
			$prev_detail[] 		= $this->users->get_specific_user($id);
			
			$this->users->update_status($params);
			$msg 			= $this->lang->line('data_updated');
			
			// GET THE DETAIL AFTER UPDATING THE RECORD
			$curr_detail[] 	= $this->users->get_specific_user($id);
			
			// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
			$activity = "%s user account has been approved";
			$activity = sprintf($activity, $curr_detail[0][0]['fname'] . ' ' . $curr_detail[0][0]['lname']);				
			
			// LOG AUDIT TRAIL
			$this->audit_trail->log_audit_trail(
				$activity, 
				$this->module_code, 
				$prev_detail, 
				$curr_detail, 
				$audit_action, 
				$audit_table,
				$audit_schema
			);
									
			SYSAD_Model::commit();
			
			$mail_flag = $this->_send_email($id, $params['status_id']);
			$status = SUCCESS;
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
		
		$statistics = $this->users->get_user_status_count();
		
		$info = array(
			"status" 	=> $status,
			"msg" 		=> $msg,
			"id" 		=> $id,
			"pending" 	=> $statistics['pending_count'],
			"approved" 	=> $statistics['approved_count'],
			"disapproved" 	=> $statistics['disapproved_count'],
			"mail" 			=> $mail_flag,
			"datatable_options"	=> array('table_id' => $this->table_id, 'path' => $this->path, 'advanced_filter' => true)
		);
	
		echo json_encode($info);
	}

	public function resend_approval_email()
	{
		try
		{
			// $this->redirect_off_system($this->module);
			
			$status 		= ERROR;
			$params			= get_params();
			$decode_id		= base64_url_decode($params['id']);
			$id				= filter_var($decode_id, FILTER_SANITIZE_NUMBER_INT);
			$status_id		= base64_url_decode($params['status_id']);
			$status_code	= ($status_id == STATUS_APPROVED) ? APPROVED : DISAPPROVED;
			
			$user_detail 	= $this->users->get_user_details($id);
			
			$mail_flag 		= $this->_send_email($id, $status_code);
			$status 		= SUCCESS;
			$msg			= "Email was successfully resent to <strong>" . $user_detail['email'] . "</strong>";
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
			"id" 		=> $id,
			"mail" 		=> $mail_flag
		);
	
		echo json_encode($info);
	}
	
	private function _send_email($id, $status)
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
				
			//$this->email_template->send_email_template($email_data, "emails/account", $template_data);
			$this->email_template->send_email_template($email_data, "emails/account_approve", $template_data);
			//$flag = 1;
			$flag = $this->email->print_debugger();

			$errors 						= $this->email_template->get_email_errors();

			if( !EMPTY( $errors ) )
			{
				$str 						= var_export( $errors, TRUE );

				RLog::error( "Email Error" ."\n" . $str . "\n" );
			}
			
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
}
