<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_orders extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->controller 	  		= strtolower(__CLASS__);
		
		$this->load->model(FOLDER_PURCHASE_ORDERS.'/purchase_orders_model', 'po_model');
		
	}

	public function index($encoded_module_code, $encoded_tab_module)
	{
		try
		{
			$footer 					= array();
			$data 			            = array();
			$resources		           	= array();
			$actions 					= array();
			$imports 					= array();
			$buttons 					= array();
			$data 						= array();

		/* 	$resources['load_css']  	= array(CSS_UPLOAD, CSS_DATETIMEPICKER, CSS_SELECTIZE);
			$resources['load_js']  		= array(JS_UPLOAD, JS_DATETIMEPICKER, JS_SELECTIZE); */
			
			$module_code				= decrypt_id($encoded_module_code);
			$tab_module					= decrypt_id($encoded_tab_module);

			$ag_codes					= $this->get_ag_code_per_module($module_code);
			$workflow_ids				= $this->get_workflow_per_module_tab($module_code, $tab_module);

			$params						= get_params();

			$filter_param				= $this->_construct_url_filter($params['filter_form']);

			$list						= $this->page($encoded_module_code, $encoded_tab_module);

			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if($list['list']['next_records'] > 0)
			{
				$footer					= array(
						'container'		=> '.list-toggle',
						'path'			=>  base_url().PORTAL_TRANSACTIONS.'/tabs/'.PORTAL_TAB_PURCHASE_ORDERS.'/page/'.$encoded_module_code.'/'.$encoded_tab_module.'/0/',
						'append'		=> 'li',
						'last_page'		=> '#scroll-next-page',
						'get_params'	=> $filter_param
				); 
			}
			
			//Set up additional actions in tab
			if(check_permission($tab_module, ACTION_ADD))
			{
				$imports[]				= array(
						'target'		=> 'modal_quick_add', 
						'icon'			=> 'unarchive',
						'label'			=> 'Import PO List',
						'onclick'		=> 'modal_quick_add_init(\'temp_pos\',\'Import PO List\')'
				);

				$imports[]				= array(
						'target'		=> 'modal_quick_add',
						'icon'			=> 'unarchive',
						'label'			=> 'Import PO Files',
						'onclick'		=> 'modal_quick_add_init(\'temp_po_batch\',\'Import PO Files\')'
				);
				
				$buttons[]				= array(
						'target'		=> 'modal_add_po',
						'label'			=> 'Add Purchase Order',
						'class'			=> 'purple darken-1',
						'id'			=> 'add_po',
						'onclick'		=> 'modal_add_po_init(\''.$tab_module.'\',\'Add PO\')'
				);
			}

			//Consolidate data to be passed in views.
			$data						= array_merge($list, array(
					'footer'			=> $footer,
					'resources'			=> $resources,
					'actions'			=> array(
							'imports'	=> $imports,
							'buttons'	=> $buttons
					)
			));

			$this->_load_transaction_list($data);
			
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function page($encoded_module_code, $encoded_tab_module, $initial=TRUE, $page_num=1)
	{
		try
		{
			if($initial == TRUE)
			{
				$params					= get_params();
				$filter_params			= $params['filter_form'];
			}
			else
			{
				$filter_params			= get_params(TRUE, TRUE);
				$filter_params			= $this->_explode_filter($filter_params);		
			}

			$module_code				= decrypt_id($encoded_module_code);
			$tab_module					= decrypt_id($encoded_tab_module);
		
			$ag_codes					= $this->get_ag_code_per_module($module_code);
			$workflow_ids				= $this->get_workflow_per_module_tab($module_code, $tab_module);
			$from 			 			= ($page_num - 1) * SYS_SETTING_DISPLAY_LIST_NO;

			$where						= array(
					'where'				=> array(
						'ag_codes'		=> $ag_codes,
						'workflow_ids'	=> $workflow_ids
					),
					'filter'			=> $this->_construct_transactions_having($filter_params, $ag_codes),
					'limit'				=> array(
						'from'			=> $from,
						'to'			=> SYS_SETTING_DISPLAY_LIST_NO
					)
			);

			$rmv_having_str 			= ! EMPTY( ($where['filter']['having']) ) ? TRUE : FALSE;

			$scope_details 				= get_scope_details($tab_module, '', TRUE);

			$pos						= $this->po_model->get_purchase_orders_list($where, NULL, $scope_details['having'], $tab_module);

			//Get the next batch of internal orders
			$where['limit']['from']		= $page_num * SYS_SETTING_DISPLAY_LIST_NO;
			$next_records 		 		= $this->po_model->get_purchase_orders_list($where, TRUE, $scope_details['having'], $tab_module);

			//Determines if this is the last page.
			$last_page 	 	 			= ($next_records > 0)? FALSE : TRUE;

			$data 						= array(
				'list'					=> array(
					'list' 				=> $pos, 
					'ag_code' 			=> $ag_codes,
					'next_records'		=> $next_records
				)
			);
			
			if($initial == TRUE)
			{
				return $data;
			}
			else
			{
				$data['list']['last_page']	= $last_page;
				$data['list']['counter']	= $from + 1;

				$this->_load_transaction_list($data, FALSE);
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function process($module_code)
	{
		try
		{

		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

}