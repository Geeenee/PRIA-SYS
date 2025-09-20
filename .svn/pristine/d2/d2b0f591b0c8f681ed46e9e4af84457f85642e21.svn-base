<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contractors extends Transaction_Controller 
{
	private $module_js;

	public function __construct()
	{
		parent::__construct();
		
		$this->module_code 	  		= MODULE_PORTAL_TRANS_CONTRACTORS;
		$this->module_folder  		= PORTAL_TRANSACTIONS;
		$this->controller 	  		= strtolower(__CLASS__);

		$this->permissions			= check_permission($this->module_code);
		$this->module_js			= $this->system_js_path.$this->module_folder.DS.$this->controller;
	}

	public function index()
	{
		try
		{
			if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

			$data 			= array();
			$resources		= array();

			$common_resource          				= $this->get_common_resources($this->module_code);

			$resources['load_css']    				= array_merge([CSS_SELECTIZE, CSS_DATETIMEPICKER, CSS_UPLOAD, CSS_LABELAUTY], $common_resource['css']);
			$resources['load_js']     				= array_merge([JS_SELECTIZE, JS_DATETIMEPICKER, JS_UPLOAD, JS_LABELAUTY, $this->module_task_js], $common_resource['js']);
			$resources['loaded_init'] 				= $common_resource['init'];
			$resources['load_materialize_modal'] 	= $common_resource['modal'];

			$data['module']							= $this->module_folder;
			$data['resources']						= $resources;

			$tabs	= $this->_construct_module_tabs($this->module_code, $this->module_folder);

			if( EMPTY($tabs) )	
				throw new Exception($this->lang->line('err_trans_no_access_tabs'));
			else
				$data['tabs']	= $tabs;

			$data['sub_nav_right'] 	 = ['sidebar_close' => TRUE];
			
			// PAGE TITLE
			$data['active_sub_menu'] = MODULE_PORTAL_TRANSACTIONS;
			$data['page_title'] 	 = 'Contractors';
			
			$this->construct_ajax_tabs($data);
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
}