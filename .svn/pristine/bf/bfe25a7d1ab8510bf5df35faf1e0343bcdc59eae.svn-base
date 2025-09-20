<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import_reference extends Transaction_Controller 
{
	private $module_js;

	public function __construct()
	{
		parent::__construct();
		
		$this->module_code 	  		= MODULE_PORTAL_IMPORT_REFERENCE;
		$this->module_folder  		= PORTAL_IMPORT_REFERENCE;
		$this->controller 	  		= strtolower(__CLASS__);

		$this->permissions			= check_permission($this->module_code);
	}

	public function index($type = NULL, $reference_num = NULL)
	{
		try
		{
			if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

			$data 			= array();
			$resources		= array();

			$common_resource          				= $this->get_common_resources($this->module_code);

			$resources['load_css']    				= array_merge([CSS_LABELAUTY], $common_resource['css']);
			$resources['load_js']     				= array_merge([JS_LABELAUTY, $this->module_task_js], $common_resource['js']);
			$resources['loaded_init'] 				= $common_resource['init'];
			$resources['load_materialize_modal'] 	= $common_resource['modal'];

			$data['module']							= $this->module_folder;
			$data['resources']						= $resources;

			$tabs	= $this->_construct_module_tabs($this->module_code, $this->module_folder, $type, $reference_num);

			if( EMPTY($tabs) )	
				throw new Exception($this->lang->line('err_trans_no_access_tabs'));
			else
				$data['tabs']	= $tabs;

			$data['sub_nav_right'] 	 = ['sidebar_close' => TRUE];
			
			// PAGE TITLE
			//$data['active_sub_menu'] = MODULE_PORTAL_TRANSACTIONS;
			$data['page_title'] 	 = 'Import Reference';
			
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