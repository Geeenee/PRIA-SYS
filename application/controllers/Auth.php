<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends SYSAD_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		
		$this->load->model(CORE_USER_MANAGEMENT . '/users_model', 'users', TRUE);
	}
		
	public function index( $logout_inactivity = NULL )
	{	
		$data 		= array();
		$check_has_agreement_text 	= 0;

		try
		{
			$resources['load_materialize_modal'] 	= array (
			    'modal_forgot_pw' 	=> array (
					'title' 		=> "Forgot Password",
					'size' 			=> "sm-w md-h",
					'controller' 	=> "forgot_password",
					'fixed_header' 	=> false,
					'modal_footer' 	=> false,
					'footer_div_none'	=> true
			    ),
			    'modal_sign_up' 	=> array (
					'fixed_header' 	=> false,
					'size' 			=> "md-w lg-h",
					'controller' 	=> "sign_up",
					'modal_footer' 	=> false,
					'footer_div_none'	=> true
			    ),
			    'modal_term_condition'  	=> array(
			    	'title' => "Terms and condition",
					'size' => "lg-w lg-h",
					'controller' => "auth",
					'method' => "modal_term_condition",
					'fixed_header' => false,
					'modal_footer' => false,
					'footer_div_none'	=> true,
					'post'			=> true
		    	)
			);

			$check_has_agreement_text	= (int) get_setting( AGREEMENT, 'has_agreement_text' );


			$data['check_has_agreement_text']	= $check_has_agreement_text;
			$data['logout_inactivity']			= $logout_inactivity;
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);
		}
		catch( Exception $e )
		{
			$msg 	= $this->rlog_error($e, TRUE);	
		}

		$this->load->view('login_pria', $data);
		$this->load_resources->get_resource($resources);
	}	
	
	public function sign_in($username = NULL, $password = NULL) 
	{ 
		$initial_flag 		= 0;
		$redirect_page 		= "";

		$check_has_agreement_text 	= 0;
		$user_agreed 				= 0;

		try 
		{
			$flag 			= 0;
			$msg 			= "";
			$salted 		= FALSE;
			
			if(!IS_NULL($username) AND !IS_NULL($password))
			{
				$username = filter_var($username, FILTER_SANITIZE_STRING);
				$username = base64_url_decode($username);
				$password = filter_var(base64_url_decode($password), FILTER_SANITIZE_STRING);
				
				$this->auth_model->update_status($username);
				
				$salted = TRUE;
			} 
			else 
			{
				$params 	= get_params();
				
				$username 	= filter_var($params['username'], FILTER_SANITIZE_STRING);
				$password 	= filter_var($params['password'], FILTER_SANITIZE_STRING);
			}	
		
			if(EMPTY($username)) throw new Exception($this->lang->line('username_required'));
			if(EMPTY($password)) throw new Exception($this->lang->line('password_required'));

			$check_has_agreement_text	= (int) get_setting( AGREEMENT, 'has_agreement_text' );

			if( !EMPTY( $check_has_agreement_text ) )
			{
				$this->authenticate->sign_in($username, $password, $salted, TRUE);

				$user_details				= $this->auth_model->get_active_user($username, 'username', TRUE);

				$user_agreed_details 		= $this->auth_model->get_user_agreement($user_details['user_id']);

				if( !EMPTY($user_agreed_details))
				{
					if( !EMPTY( $user_agreed_details['agreement_flag'] ) )
					{
						$this->users->update_last_logged_in( $username );

						$this->authenticate->sign_in($username, $password, $salted);

						if( !EMPTY( $change_password_initial_login ) )
						{

							$initial_flag 				= ($this->session->has_userdata('initial_flag') == TRUE) ? $this->session->userdata( "initial_flag" ) : 0;
						}
					}

					$user_agreed 			= $user_agreed_details['agreement_flag'];
				}
			}
			else
			{
			
				$this->authenticate->sign_in($username, $password, $salted);

				$this->users->update_last_logged_in( $username );

				$change_password_initial_login 	= get_setting(LOGIN, "change_password_initial_login");

				if( !EMPTY( $change_password_initial_login ) )
				{

					$initial_flag 				= ($this->session->has_userdata('initial_flag') == TRUE) ? $this->session->userdata( "initial_flag" ) : 0;
				}
			}

			$user_info 	= $this->auth_model->get_active_user($username);

			$activity 	= 'logged in '.$user_info['name'].'.';

			/*$this->audit_trail->log_audit_trail(
				$activity, 
				MODULE_USER
			);
			*/
			
			$flag 			= 1;
			$redirect_page 	= (($this->session->has_userdata('redirect_page')) === TRUE ) ? $this->session->redirect_page : '';

			/** Added by kebs for redirect after login */
			if(ISSET($params['redirect']) && ! EMPTY($params['redirect']))
				$redirect_page  = $params['redirect'];
		}
		catch(PDOException $e)
		{
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}
		
		$result = array(
			"flag" 			=> $flag,
			"msg" 			=> $msg,
			"redirect_page" => $redirect_page,
			'initial_flag'	=> $initial_flag,
			"check_has_agreement_text"	=> $check_has_agreement_text,
			"user_agreed"	=> $user_agreed
		);

		if( !EMPTY( $initial_flag ) )
		{
			$result["username"] 	= base64_url_encode( $this->session->userdata('username') );
			$result['salt'] 		= $this->session->userdata('salt');
		}
		
		if($salted)
		{
			$this->authenticate->check_user();	
		} 
		else 
		{
			echo json_encode($result);
		}	
		
	}

	public function get_term_condition_file()
	{

		$flag 				= 0;

		try
		{
			$params 		= get_params();
			
			if( ISSET( $params['file'] ) )
			{
				$path 		= FCPATH.PATH_TERM_CONDITIONS_UPLOADS.$params['file'];
				$path 		= str_replace(array('\\','/'), array(DS,DS), $path);

				if( file_exists( $path ) )
				{
					$ext 		= pathinfo( $path, PATHINFO_EXTENSION );

					if( strtolower( $ext ) == 'pdf' )
					{
						$pdf 	= file_get_contents( $path );

						header("Content-type: application/pdf");
						header("Content-Disposition: inline; filename=".$params['file']."");

						readfile( $path );

					}
					else 
					{
						$this->load->helper('download');

						force_download( $path, NULL );
					}

					$flag 		= 1;
				}
				else
				{
					throw new Exception('File not found.');
				}
			}
		}
		catch( PDOException $e )
		{
			$this->rlog_error( $e );
		}
		catch(Exception $e)
		{
			$this->rlog_error( $e );
		}

		if( !$flag )
		{
			redirect(base_url().'Errors/index/402/');
		}
		
	}
	
	public function sign_out($user_id = NULL)
	{
		try
		{
			$flag 	= 0;
			$msg 	= "";

			$id 		= $this->session->user_id;

			if( !EMPTY( $user_id ) )
			{
				$id 	= $user_id;
			}
		
			$this->authenticate->sign_out($id);

			$user_info 	= $this->users->get_user_details($id);

			$activity 	= 'logged out '.$user_info['fname'].' '.$user_info['lname'].'.';

			$this->audit_trail->log_audit_trail(
				$activity, 
				MODULE_USER
			);
			
			// Unset autologin variable
			delete_cookie('autologin');
			$flag 	= 1;							
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg 	= $this->rlog_error($e, TRUE);
		}
		
		$result		= array(
			"flag" 	=> $flag,
			"msg" 	=> $msg
		); 
												
		echo json_encode($result);
			
	}
	
	public function verify($id, $email)
	{
		$msg 		= "";
		$data 		= array();
		$resources 	= array();
	
		try
		{
			if(EMPTY($id) OR EMPTY($email))
			{
				throw new Exception($this->lang->line('invalid_action'));
			}
			
			$is_verified = $this->auth_model->check_user_status(base64_url_decode($id), TRUE);
			
			if(EMPTY($is_verified))
			{
				header('Location:'.base_url().'unauthorized/invalid_link');
			}
			
			$resources['load_materialize_modal'] = array (
				'modal_verify_account' 	=> array (
					'fixed_header' 		=> false,
					'size' 				=> "sm-w lg-h",
					'controller' 		=> "auth",
					'modal_footer' 		=> false,
					'method' 			=> "modal_verify_account/" . $id . "/" . $email,
					'modal_type' 		=> "open",
					'footer_div_none'	=> true
				)
			);
			
			$this->load->view('login', $data);
			$this->load_resources->get_resource($resources);
	
		}
		catch(PDOException $e)
		{			
			echo $this->get_user_message($e);

			// redirect(base_url() . 'errors/index/500/'.base64_url_encode($msg) , 'location');
		}
		catch(Exception $e)
		{
			echo $this->rlog_error($e, TRUE);

			// redirect(base_url() . 'errors/index/500/'.base64_url_encode($msg) , 'location');
		}
	}
	
	public function modal_verify_account($id, $email)
	{
		try
		{
			$data 		= array();
			$resources 	= array();
			
			$data['id'] 	= $id;
			$data['email'] 	= $email;
			
			$module_js 		= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_COMMON."/forgot_password";
			
			$pass_const 		= $this->users->get_settings_arr(PASSWORD_CONSTRAINTS);
			$user_const 		= $this->users->get_settings_arr(USERNAME_CONSTRAINTS);

			$user_has_cons 		= get_setting( USERNAME, 'apply_username_constraints' );
			
			$pass_err 			= $this->get_pass_error_msg();
			$pass_length 		= $pass_const[PASS_CONS_LENGTH];
			$upper_length 		= $pass_const[PASS_CONS_UPPERCASE];
			$digit_length 		= $pass_const[PASS_CONS_DIGIT];
			$repeat_pass 		= $pass_const[PASS_CONS_REPEATING];

			$user_err 			= $this->get_username_error_msg();

			$user_min 			= $user_const[USERNAME_MIN_LENGTH];
			$user_max 			= $user_const[USERNAME_MAX_LENGTH];
			$user_dig 			= $user_const[USERNAME_DIGIT];

			$user_cons_arr 		= array(
				'user_has_cons'		=> $user_has_cons,
				'user_min_length' 	=> $user_min,
				'user_max_length'	=> $user_max,
				'user_digit_length'	=> $user_dig,
				'user_err'			=> $user_err
			);

			$user_cons_arr 		= json_encode( $user_cons_arr );

			$cons_array = array(
				'pass_err'		=> $pass_err,
				'pass_length'	=> $pass_length,
				'upper_length'	=> $upper_length,
				'digit_length'	=> $digit_length,
				'repeat_pass'	=> $repeat_pass,
				'pass_same'		=> 0
			);

			$cons_array 		= json_encode( $cons_array );
			
			$resources['load_js'] 	= array($module_js);
			$resources['loaded_init'] = array(
				'password_constraints( '.$cons_array.' );',
				'username_constraints( '.$user_cons_arr.' );',
				'ForgotPw.saveUser();'
			);
			
			$this->load->view("modals/verify_account", $data);
			$this->load_resources->get_resource($resources);
		}
		catch(PDOException $e)
		{			
			echo $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			echo $this->rlog_error($e, TRUE);
		}
			
	}

	private function _validate_user_account( array $params )
	{
		if(!EMPTY($params['password']))
		{
			$this->users->check_password_history($params['user_id'], $params['password']);

			if( EMPTY( preg_match('/^[a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\s]+$/', $params['password'] ) ) )
			{
				throw new Exception("Password contains an illegal character.");
			}
		}
		else
		{
			throw new Exception(sprintf($this->lang->line('is_required'), "Password"));
		}

		if( EMPTY( $params['username'] ) )
		{
			throw new Exception(sprintf($this->lang->line('is_required'), "Username"));
		}

		if( !EMPTY( $params['password'] ) )
		{
						
			$check_password = $this->validate_password( $params['password'], $params['username'] );

			if( $check_password !== TRUE )
			{
				throw new Exception($check_password);
			}
		}
	}
	
	public function update_user_account()
	{
		$redirect_page 		= "";

		try
		{
			$status = ERROR;
			$params	= get_params();

		
			$decode_id	= base64_url_decode($params['user_id']);
			$id			= filter_var($decode_id, FILTER_SANITIZE_NUMBER_INT);

			$params['user_id'] 			= $decode_id;

			$this->_validate_user_account( $params );
		
			// GET SECURITY VARIABLES
			
			// BEGIN TRANSACTION			
			SYSAD_Model::beginTransaction();
								
			
			$params['verified_account'] = TRUE;
			
			$this->users->update_status($params);
			$msg 		= $this->lang->line('data_updated');

			$redirect_page 	= (($this->session->has_userdata('redirect_page')) === TRUE ) ? $this->session->redirect_page : '';
			
			SYSAD_Model::commit();
			$status 	= SUCCESS;
		}
		catch(PDOException $e)
		{
			SYSAD_Model::rollback();

			$msg = $this->get_user_message($e, array(), array(1062 => 'Sorry, The username '.$params['username'].' already exists.'));
		
			// $msg = $this->rlog_error($e, TRUE);
		}
		catch(Exception $e)
		{
			SYSAD_Model::rollback();
				
			$msg = $this->rlog_error($e, TRUE);
		}
		
		$info 			= array(
			"status" 	=> $status,
			"msg" 		=> $msg,
			"redirect_page" => $redirect_page
		);
	
		echo json_encode($info);
	}

	public function change_password_owner( $username, $salt, $initial_flag, $to_sign_in )
	{
		$this->reset_password_form( $username, $salt, $initial_flag, $to_sign_in );
	}

	public function reset_password_form($username, $reset_salt, $initial_flag = INITIAL_NO, $to_sign_in = FALSE )
	{		
		try 
		{
			if(empty($username) OR empty($reset_salt)) throw new Exception("Invalid request.");
			
			$username 				= base64_url_decode($username);
			
			// GET USER INFO USING USER ID AND RESET SALT
			$info = $this->auth_model->get_user_by_id_reset_salt( $username, $reset_salt, $initial_flag );
			
			if(empty($info)) throw new Exception("Invalid request.");
			
			$data					= array();
			$data['id']				= in_salt($username, $reset_salt);
			$data['key']			= $reset_salt;
			$data['initial_flag'] 	= $initial_flag;
			$data['to_sign_in'] 	= $to_sign_in;
			$data['username']		= $username;

			$pass_const 			= $this->users->get_settings_arr(PASSWORD_CONSTRAINTS);
			
			$pass_err 				= $this->get_pass_error_msg();
			$pass_length 			= $pass_const[PASS_CONS_LENGTH];
			$upper_length 			= $pass_const[PASS_CONS_UPPERCASE];
			$digit_length 			= $pass_const[PASS_CONS_DIGIT];
			$repeat_pass 			= $pass_const[PASS_CONS_REPEATING];

			$cons_array = array(
				'pass_err'		=> $pass_err,
				'pass_length'	=> $pass_length,
				'upper_length'	=> $upper_length,
				'digit_length'	=> $digit_length,
				'repeat_pass'	=> $repeat_pass,
				'pass_same' 	=> 0
			);

			$cons_array 		= json_encode( $cons_array );

			$resources['loaded_init']	= array(
				'password_constraints( '.$cons_array.' );'
			);
			
			$this->load->view('forms/reset_password_form', $data);
			$this->load_resources->get_resource($resources);
			
		}
		catch(PDOException $e)
		{
			echo $this->get_user_message( $e );
		}
		catch(Exception $e)
		{
			echo $this->rlog_error( $e, TRUE );
		}			
	}

	private function _check_reset_fields($params, $username)
	{

		$required 	= array();

		$required["password"]			= "Password";
		$required["retype_password"]	= "Confirm Password";

		if(!ISSET($params["id"]) OR EMPTY($params["id"])) throw new Exception($this->lang->line('err_invalid_data'));
		if(!ISSET($params["key"]) OR EMPTY($params["key"])) throw new Exception($this->lang->line('err_invalid_data'));

		$this->check_required_fields( $params, $required );
		
		if($params["password"] != $params["retype_password"]) throw new Exception('Confirm Password is invalid');

		if( !EMPTY( $params['password'] ) )
		{
			$check_password = $this->validate_password( $params['password'], $username );

			if( $check_password !== TRUE )
			{
				throw new Exception($check_password);
			}

			// $this->users->check_password_history($this->session->user_id, $params['confirm_password']);

			if( EMPTY( preg_match('/^[a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\s]+$/', $params['password'] ) ) )
			{
				throw new Exception("Password contains an illegal character.");
			}
		}

	}

	public function update_password()
	{	
		$flag 			= 0;
		$msg 			= "";
		$initial_flag 	= 0;

		try
		{
			$params 		= get_params();

			$id				= $params["id"];
			$key 			= $params["key"];
			$password 		= $params["password"];
			$initial_flag 	= $params['initial_flag'];

			if( $initial_flag )
			{	
				$info 		= $this->auth_model->get_active_user_for_reset( $key, BY_RESET_SALT );
			}
			else 
			{
				$info 		= $this->auth_model->get_active_user_for_reset($key, BY_SALT, INACTIVE);
			}
			
			$this->_check_reset_fields($params, $info['username']);

			$prev_detail 	= array();
			$curr_detail 	= array();

			//CHECK IF THE PASSWORD CONTAINS REPEATED CHARACTERS
		/*	if( max( array_count_values( str_split( $password ) ) )>1 )
				throw new Exception( $this->lang->line( 'no_repeat_char') );*/


			// BEGIN TRANSACTION
			SYSAD_Model::beginTransaction();
			// CHECKS IF THIS IS THE INITIAL LOG IN OF THE USER 

			if(EMPTY($info)) throw new Exception($this->lang->line('err_unauthorized_access'));

			$this->users->check_password_history( $info['user_id'], $password );
	
			$username 		= $info["username"];
	
			if( $id != in_salt( $username, $key ) ) 
				throw new Exception($this->lang->line('err_unauthorized_access'));
			
			$password_salt 	= in_salt($password, $info['salt']);
			//echo 'PASSWORD  SALT' . $password_salt . '\n';
			//echo 'SALT  ' . $info['salt'] . '\n';

			
			/*$password_hist 	= $this->user->check_password_hist( $info['user_id'], $password_salt );

			if( $info['password'] == $password_salt ) 
				throw new Exception('Error. Reusing password is not allowed');

			if(! EMPTY($password_hist))
				throw new Exception('Error. Reusing password is not allowed');*/

			// SAVE IN PASSWORD HISTORY

			$audit_table[] 	= SYSAD_Model::CORE_TABLE_USERS;
			$audit_schema[]	= DB_CORE;
			$audit_action[]	= AUDIT_UPDATE;

			$prev_detail[] 	= array( $this->users->get_user_details( $info['user_id'] ) );

			$password 		= preg_replace('/\s+/', '', $password);

			$this->auth_model->update_password($info['email'], $password);

			$curr_detail[] 	= array( $this->users->get_user_details( $info['user_id'] ) );

			$activity 		= "%s's password has been updated";
			$activity 		= sprintf($activity, $username);

			if( !EMPTY( $initial_flag ) )
			{
				$this->audit_trail->log_audit_trail(
					$activity, 
					MODULE_USER, 
					$prev_detail, 
					$curr_detail, 
					$audit_action, 
					$audit_table,
					$audit_schema
				);
				$this->session->set_userdata( "initial_flag", 0 );
			}

			SYSAD_Model::commit();
	
			$msg 			= "Password has been reset. You can now login using your new password.";
			$flag 			= 1;
	
		}
		catch(PDOException $e)
		{
			SYSAD_Model::rollback();

			$msg 			= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			// IF THE TRANSACTION IS NOT SUCCESSFUL, ROLLBACK ALL CHANGES
			SYSAD_Model::rollback();

			$msg 			= $this->rlog_error( $e, TRUE );
		}
	
		$result = array(
			"msg" 			=> $msg,
			"flag" 			=> $flag,
			"initial_flag" 	=> $this->session->userdata( "initial_flag" )
		);
	
		echo json_encode($result);
	}

	public function update_user_agreement()
	{
		$msg 		= '';

		$status = ERROR;
		$params	= get_params();
		$flag 	= 0;		

		$redirect_page = '';

		$sign_up 		= 0;

		try
		{
			if( EMPTY( $params['sign_up_check'] ) )
			{
				$this->_validate_user_agreement( $params );
			}

			SYSAD_Model::rollback();

			if( !EMPTY( $params['sign_up_check'] ) )
			{
				$sign_up 	= $params['sign_up_check'];
			}
			else
			{
				$user_info 	= $this->auth_model->get_active_user( $params['username'], 'username', TRUE );

				$this->auth_model->update_user_agreement( $user_info['user_id'] );

				$this->authenticate->sign_in($params['username'], $params['password'], FALSE);

			}

			$flag 		= 1;

			$redirect_page 	= (($this->session->has_userdata('redirect_page')) === TRUE ) ? $this->session->redirect_page : '';

			SYSAD_Model::commit();
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

		$result 	= array(
			"flag" 	=> $flag,
			"msg" 	=> $msg,
			'redirect_page' => $redirect_page,
			'sign_up'		=> $sign_up
		);

		echo json_encode( $result );
	}

	private function _validate_user_agreement( array $params )
	{
		if( EMPTY( $params['agreement'] ) )
		{
			throw new Exception('You must agree first before proceeding.');
		}

		$required['username'] 		= 'Username';
		$required['password'] 		= 'Password';		

		$this->check_required_fields( $params, $required );

		$this->authenticate->sign_in( $params['username'], $params['password'], FALSE, TRUE );
	}

	public function modal_term_condition()
	{
		$data 							= array();
		$resources 						= array();
		$params 						= get_params();
		$sign_up 						= 0;

		try
		{
			$module_js 					= HMVC_FOLDER."/terms";

			$resources['load_js']		= array($module_js);
			$resources['loaded_init']	= array('Terms.init();', 'Terms.proceed();');

			$get_agremment_text 		= get_setting( AGREEMENT, 'agremment_text' );
			$agreement_uploads 			= get_setting( AGREEMENT, 'agreement_uploads' );

			if( ISSET( $params['sign_up'] ) AND !EMPTY( $params['sign_up'] ) )
			{
				$sign_up 				= 1;
			}

			$data['aggreement_text'] 	= ( !EMPTY( $get_agremment_text ) ) ? html_entity_decode( $get_agremment_text ) : $get_agremment_text;

			$data['agreement_uploads']	= ( !EMPTY( $agreement_uploads ) ) ? explode('|', $agreement_uploads) : array();

			$data['sign_up'] 			= $sign_up;
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message( $e );
		}
		catch(Exception $e)
		{
			$msg 	= $this->rlog_error( $e, TRUE );
		}

		$this->load->view("modals/term_condition", $data);
		$this->load_resources->get_resource($resources);
	}
}