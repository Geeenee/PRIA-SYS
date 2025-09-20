<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage_settings extends SYSAD_Controller 
{

	private $module 		= MODULE_SETTINGS;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		try
		{
			// $this->redirect_off_system($this->module);
			$this->redirect_module_permission($this->module);

			$data 		= array();
			$resources 	= array();
			$module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_SETTINGS."/settings";
			
			$resources['load_js'] 		= array($module_js);
			$resources['loaded_init'] 	= array(
				'Settings.initForm();'
			);
			
			$this->template->load('manage_settings', $data, $resources);
		}
		catch(PDOException $e)
		{			
			$msg = $this->get_user_message($e);

			redirect(base_url() . 'errors/modal/500/'.base64_url_encode($msg) , 'location');
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);

			redirect(base_url() . 'errors/modal/500/'.base64_url_encode($msg) , 'location');
		}	
	}
}