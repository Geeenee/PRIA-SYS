<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends SYSAD_Controller 
{
	
	private $controller;
	private $module;
	private $module_js;
	private $path;
	private $table_id;

	private $approve_per;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->controller 	= strtolower(__CLASS__);
		$this->module 		= MODULE_DASHBOARD;
		$this->module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_DASHBOARD."/".$this->controller;
		$this->path 		= CORE_DASHBOARD."/".$this->controller."/get_user_list/0";
		$this->table_id 	= "user_approval_table";
		
		$this->load->model('dashboard_model', 'dashboard', TRUE);
		$this->load->model(CORE_USER_MANAGEMENT . '/users_model', 'users', TRUE);
		$this->load->model(CORE_USER_MANAGEMENT . '/roles_model', 'roles', TRUE);
		$this->load->model(CORE_DASHBOARD. '/widgets_model', 'widgets', TRUE);

		// $this->approve_per 	= $this->permission->check_permission($this->module, ACTION_APPROVE);
	}


	public function index()
	{
		try
		{

			// $this->redirect_off_system($this->module);
			$this->redirect_module_permission($this->module);

			$data 			= array();
			$resources 		= array();
			
			// GET LAYOUT OF THE AVAILABLE REGIONS IN DASHBOARD
			$data['area_layout'] 	= $this->widgets->get_area_layout();
			
			$resources['load_css'] 	= array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE);
			$resources['load_js'] 	= array(JS_DATATABLE, JS_DATATABLE_MATERIAL, $this->module_js);
		
			$this->template->load('dashboard', $data, $resources);
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
}


/* End of file Dashboard.php */
/* Location: ./application/modules/budget/controllers/Dashboard.php */
