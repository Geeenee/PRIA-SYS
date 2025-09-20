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

	public function file_page($enc_module, $encoded_tab_module, $page_num=1)
	{
		try
		{
			$module_code   	 = base64_url_decode($enc_module);
			$tab_module_code = base64_url_decode($encoded_tab_module);

			$from 		     = ($page_num - 1) * SYS_SETTING_DISPLAY_LIST_NO;

			$filter_params   = get_params(TRUE, TRUE);
			$filter_params   = $this->_explode_filter($filter_params);
			
			
			$scope_details  	= get_scope_details($tab_module_code);

			$where 			 = [
				'limit' 	=> ['from' => $from, 'to' => SYS_SETTING_DISPLAY_LIST_NO], 
				'module' 	=> $module_code,
				'having'	=> $scope_details['having']
			];

			$file_list = $this->files_model->get_file_list($where, $filter_params);

			//Get the next batch of files		
			$where 			 = [
				'limit' 	=> ['from' => $from - 2, 'to' => 1], 
				'module' 	=> $module_code,
				'having'	=> $scope_details['having']
			];

			$prev_list = $this->files_model->get_file_list($where, $filter_params);

			//Determines if this is the last page.
			$prev_date       = $prev_list[0]['created_date'];

			//Get the next batch of internal orders
			$where 			 = [
				'limit' 	=> ['from' => $from, 'to' => SYS_SETTING_DISPLAY_LIST_NO], 
				'module' 	=> $module_code,
				'having'	=> $scope_details['having']
			];

            $next_ovr        = $this->files_model->get_file_list($where, $filter_params);

			$last_page 	 	 = ( EMPTY($next_ovr)) ? TRUE : FALSE;

			$this->load->view(PORTAL_TRANSACTIONS.'/tasks/'.FOLDER_FILES.'/file_list', ['list' => $file_list, 'last_page' => $last_page, 'prev_date' => $prev_date, 'initial' => FALSE]);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function index($ag_code= NULL, $enc_tab_module=NULL)
	{
		try
		{
			
			$flag 				= ERROR;
			$footer 			= array();
			$display_scroll 	= FALSE;
			$module_code 		= decrypt_id($ag_code);
			$tab_module_code	= decrypt_id($enc_tab_module);
			/* echo 'TAB MODULE_CODE : '.$tab_module_code; die; */
			$scope_details  	= get_scope_details($tab_module_code);

			$params				= get_params();

			$filters 			= $params['filter_form'];

			$filter_param		= $this->_construct_url_filter($filters);
			

			//Load list of Internal Orders
			$where 			 = [
				'limit' 	=> ['from' => 0, 'to' => SYS_SETTING_DISPLAY_LIST_NO, ], 
				'module' 	=> $module_code,
				'having'	=> $scope_details['having']
			];

			$files 		= $this->files_model->get_file_list($where, $filters);

			$file_num 	= $this->files_model->get_files($filters, $where, ['1 as cnt']);

			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if(COUNT($file_num) > SYS_SETTING_DISPLAY_LIST_NO)
			{
				$footer 	 = [
					'container' => '.list-toggle',
					'path'		=>  base_url().'transactions/tabs/files/file_page/'.base64_url_encode($module_code).'/'.base64_url_encode($tab_module_code).'/',
					'append'	=> 'li',
					'last_page' => '#scroll-next-page',
					'get_params'=> $filter_param
				]; 

				$display_scroll = TRUE;
			}

			$display	   = ['display_scroll' => ($display_scroll) ? TRUE : FALSE];
            $footer 	   = array_merge($footer, $display); 

			$list          = $this->load->view(PORTAL_TRANSACTIONS.'/tasks/'.FOLDER_FILES.'/file_list', ['list' => $files], TRUE, TRUE);

			//Load more actions if it exists.
			$more_actions 	= $this->load->view(PORTAL_TRANSACTIONS.'/more_actions', [], TRUE);

            $this->load->view(PORTAL_TRANSACTIONS.'/transaction_tab', ['list' => $list, 'more_actions' => $more_actions]); 

			$this->load->view('common/tabs/tab_content_footer', $footer);


			$resources['loaded_init'] 	= array(
				'Task.toggleFilter("task_filter");',
				'Filter.init();'
			);
			
			$this->load_resources->get_resource($resources);

            $flag 	       = SUCCESS;
            $msg           = $this->lang->line('data_saved');
		
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