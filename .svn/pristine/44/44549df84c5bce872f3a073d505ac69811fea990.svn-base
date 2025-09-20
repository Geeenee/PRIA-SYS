<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_account extends SYSAD_Controller 
{

	private $module;
	private $module_js;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->module 		= MODULE_PROFILE;
		$this->module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_USER_MANAGEMENT."/profile";
		
		$this->load->model('users_model', 'users', TRUE);
		$this->load->model(CORE_GROUPS.'/Groups_model', 'groups');
		$this->load->model(CORE_GROUPS.'/User_groups_model', 'user_groups');
	}
	
	public function index()
	{
		try
		{
			// $this->redirect_off_system($this->module);
			// $this->redirect_module_permission($this->module);
			
			$data = $resources = array();

			$all_groups 			= array();

			$user_groups 			= array();
			
			$id 			= $this->session->user_id;
			$user 			= $this->users->get_user_details($id);

			$user_g 		= $this->user_groups->get_user_groups_details( $id );

			/*if( !EMPTY( $user_g ) )
			{
				$user_groups = array_column( $user_g, 'group_id');
			}*/

			// $all_groups 	= $this->groups->get_all_groups();

			$data['user'] 	= $user;
			// $data['all_groups']		= $all_groups;
			$data['user_groups']	= $user_g;
			
			$resources['load_css']	= array(CSS_SELECTIZE);
			$resources['load_js'] 	= array(JS_SELECTIZE, $this->module_js);
			
			$resources['loaded_init'] = array(
				'Profile.initObj();',
				'Profile.save();'
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
		
		$this->load->view('tabs/profile_account', $data);
		$this->load_resources->get_resource($resources);
		
	}
}