<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_password extends SYSAD_Controller 
{

	private $module;
	private $module_js;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->module 		= MODULE_PROFILE;
		$this->module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_USER_MANAGEMENT."/profile";
		
		$this->load->model('users_model', 'users', TRUE);
	}
	
	public function index()
	{
		try
		{
			// $this->redirect_off_system($this->module);
			// $this->redirect_module_permission($this->module);
			
			$data = $resources = array();
			
			$id 			= $this->session->user_id;
			$user 			= $this->users->get_user_details($id);
			$data['user'] 	= $user;
			
			$resources['load_js'] 	= array($this->module_js);
			
			$pass_const 	= $this->users->get_settings_arr(PASSWORD_CONSTRAINTS);
			
			$pass_err 		= $this->get_pass_error_msg();
			$pass_length 	= $pass_const[PASS_CONS_LENGTH];
			$upper_length 	= $pass_const[PASS_CONS_UPPERCASE];
			$digit_length 	= $pass_const[PASS_CONS_DIGIT];
			$repeat_pass 	= $pass_const[PASS_CONS_REPEATING];
			
			$cons_array = array(
					'pass_err'		=> $pass_err,
					'pass_length'	=> $pass_length,
					'upper_length'	=> $upper_length,
					'digit_length'	=> $digit_length,
					'repeat_pass'	=> $repeat_pass,
					'pass_same'		=> 0
			);
			
			$cons_array 		= json_encode( $cons_array );
			
			$resources['loaded_init'] = array(
				'password_constraints( '.$cons_array.' );',
				'Profile.initModal("'.$pass_length.'", "'.$upper_length.'", "'.$digit_length.'", "'.$pass_err.'");',
				'Profile.savePassword();'
			);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);
			
			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg 	= $this->rlog_error($e, TRUE);
			
			$this->error_index( $msg );
		}
		
		$this->load->view('tabs/profile_password', $data);
		$this->load_resources->get_resource($resources);
		
	}
}