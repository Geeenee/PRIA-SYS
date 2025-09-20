<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
		//$this->module_code 	  	= PORTAL_CONTRACT_GROWER_FILES;
		$this->module_folder  	= PORTAL_TRANSACTIONS;
		$this->controller 	  	= strtolower(__CLASS__);


		//$this->permissions		= check_permission($this->module_code);
		
		$this->load->model('files_model', 'files_model');
	}

	// public function index()
	// {
	// 	$footer 					= array();
	// 	$data 			            = array();
	// 	$resources		           	= array();
	// 	$actions 					= array();
	// 	$imports 					= array();
	// 	$buttons 					= array();
		
	// 	//Load list of Internal Orders
	// 	$where 			 = [ 
	// 		'limit' 			 => ['from' => 0, 'to' => SYS_SETTING_DISPLAY_LIST_NO], 
	// 		'core_workflow_id' 	 => CORE_WORKFLOW_INTERNAL_ORDER,
	// 		'account_group_code' => $this->ag_code
	// 	];	

	// 	$internal_orders = $this->cg_model->get_io_list($where);
		
	// 	//Count total number of IO's. If less than SYS_SETTING_DISPLAY_LIST_NO don't load infinite scroll;
	// 	$ios_num 		 = $this->cg_model->get_internal_orders([], ['1 as cnt']);
	// 	$ios_num 		 = COUNT($ios_num);
	// 	$footer			 = FALSE;

	// 	//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
	// 	if($ios_num > SYS_SETTING_DISPLAY_LIST_NO)
	// 	{
	// 		$footer 	 = [
	// 			'container' => '.list-toggle',
	// 			'path'		=>  base_url().'transactions/files/file_page/',
	// 			'append'	=> 'li',
	// 			'last_page' => '#scroll-next-page'
	// 		]; 
	// 	}
		
	// 	//Set up additional actions in tab
	// 	if(check_permission(MODULE_PORTAL_TRANS_CG_IO, ACTION_ADD))
	// 	{
	// 		$imports[] = [
	// 			'target'  => 'modal_quick_add', 
	// 			'icon'	  => 'unarchive',
	// 			'label'	  => 'Import IO',
	// 			'onclick' => 'modal_quick_add_init(\'temp_ios\')'
	// 		];
	// 	}

	// 	//Consolidate data to be passed in views.
	// 	$data    = [
	// 		'list'   	=> [
	// 			'list' 		=> $internal_orders, 
	// 			'ag_code' 	=> $this->ag_code
	// 		],
	// 		'footer' 	=> $footer,
	// 		'resources' => $resources,
	// 		'actions'	=> [
	// 			'imports' => $imports,
	// 			'buttons' => $buttons
	// 		]
	// 	];
	// }

	public function file_page($page_num=1)
	{
		try
		{
			$from 			 = ($page_num - 1) * SYS_SETTING_DISPLAY_LIST_NO;
			$where 			 = [ 'limit' => ['from' => $from, 'to' => SYS_SETTING_DISPLAY_LIST_NO] ];

			$files = $this->files_model->get_file_list($where);

			//Get the next batch of files	
			$where['limit']['from'] = $page_num * SYS_SETTING_DISPLAY_LIST_NO;	
			$next_files = $this->files_model->get_file_list($where);
			//Determines if this is the last page.

			$last_page 	 	 		= ( EMPTY($next_files)) ? TRUE : FALSE;
			
			$data = [
				'list' => [
					'list' 		=> $files,
					//'ag_code' 	=> $this->ag_code, 
					'last_page' => $last_page,
					'counter'	=> $from + 1
				]
			];

		//	$data['resources']['loaded_init'] = ['Task.initDropdown();'];
			
			$this->_load_file_list($data, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function files($ag_code= NULL)
	{
		try
		{

			$footer 					= array();
			$data 			            = array();
			$resources		           	= array();
			$actions 					= array();
			$imports 					= array();
			$buttons 					= array();
			
			//Load list of Internal Orders
			$where 			 = [ 
				'limit' 			 => ['from' => 0, 'to' => SYS_SETTING_DISPLAY_LIST_NO], 
			];	

			$files = $this->files_model->get_file_list($where);
			
			//Count total number of IO's. If less than SYS_SETTING_DISPLAY_LIST_NO don't load infinite scroll;
			$file_num 		 = $this->files_model->get_files([], ['1 as cnt']);
			$file_num 		 = COUNT($file_num);
			$footer			 = FALSE;

			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if($file_num > SYS_SETTING_DISPLAY_LIST_NO)
			{
				$footer 	 = [
					'container' => '.list-toggle',
					'path'		=>  base_url().'transactions/files/file_page/',
					'append'	=> 'li',
					'last_page' => '#scroll-next-page'
				]; 
			}

			//Set up additional actions in tab
			// if(check_permission(MODULE_PORTAL_TRANS_CG_IO, ACTION_ADD))
			// {
			// 	$imports[] = [
			// 		'target'  => 'modal_quick_add', 
			// 		'icon'	  => 'unarchive',
			// 		'label'	  => 'Import IO',
			// 		'onclick' => 'modal_quick_add_init(\'temp_ios\')'
			// 	];
			// }
			
			$data    = [
				'list'   	=> [
					'list' 		=> $files, 
					//'ag_code' 	=> $this->ag_code
				],
				'footer' 	=> $footer,
				'resources' => $resources,
				'actions'	=> [
					'imports' => $imports,
					'buttons' => $buttons
				]
			];

			$this->_load_file_list($data, TRUE);		
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}

		// $this->load->view('tasks/files/files', $data);
	}
}