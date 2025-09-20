<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_nominations extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module_code      	= MODULE_PORTAL_TRANS_CONTRACTORS;
		$this->tab_module_code      = MODULE_PORTAL_TRANS_CONTRACTORS_SN;
		$this->controller 	  		= strtolower(__CLASS__);
		$this->module_js			= $this->module_js_path.strtolower(__CLASS__);

		$this->load->library('pria_workflow');
		$this->load->library('pria_overview');

		

		$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');
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

			$module_code				= decrypt_id($encoded_module_code);
			$tab_module					= decrypt_id($encoded_tab_module);

			$ag_codes					= $this->get_ag_code_per_module($module_code);
			$workflow_ids				= $this->get_workflow_per_module_tab($module_code, $tab_module);

			$params						= get_params();

			$filter_param				= $this->_construct_url_filter($params['filter_form']);

			$list						= $this->page($encoded_module_code, $encoded_tab_module);

			$resources['load_materialize_modal']	=  [
				'modal_site_nomination' 	=> array(
				  'size' 					=> 'sm-w sm-h',
				  'custom_title' 			=> 'Nominate Site', 
				  'post'					=> true,
				  'module' 					=> PORTAL_TRANSACTIONS,
				  'method' 					=> 'modal_site_nomination',
				  'controller' 				=> 'tabs/site_nominations',
				  'custom_button'	=> array(
							'Nominate' => array(
								'type' 		=> 'button',
								'action' 	=> 'Nominate'
							)
						)
				)		
			];

			$resources['load_js']	= [$this->module_js];
			
			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if($list['list']['next_records'] > 0)
			{
				$footer					= array(
						'container'		=> '.list-toggle',
						'path'			=>  base_url().PORTAL_TRANSACTIONS.'/tabs/'.PORTAL_TAB_SITE_NOMINATIONS.'/page/'.$encoded_module_code.'/'.$encoded_tab_module.'/0/',
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
						'label'			=> 'Import SN',
						'onclick'		=> 'modal_quick_add_init(\'temp_sns\')'
				);

				$buttons[]				= array(
						'label'			=> 'Nominate Site',
						'class'			=> 'purple darken-1',
						'id'			=> 'add_sn',
						'target'		=> 'modal_site_nomination',
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
			$scope_details 				= get_scope_details($tab_module, '', $rmv_having_str);
		
			$sns						= $this->sn_model->get_site_nominations_list($where, NULL, $scope_details['having']);

			//Get the next batch of internal orders
			$where['limit']['from']		= $page_num * SYS_SETTING_DISPLAY_LIST_NO;
			$next_records	 			= $this->sn_model->get_site_nominations_list($where, TRUE, $scope_details['having']);

			//Determines if this is the last page.
			$last_page 	 	 			= ($next_records > 0)? FALSE : TRUE;

			$data 						= array(
				'list'					=> array(
					'list' 				=> $sns, 
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

	public function modal_site_nomination()
	{
		try
		{
			$data = $resources = [];

			$resources['loaded_init'] = ['selectize_init();', 'SiteNominations.initModal();'];
			
			$data['business_centers'] = $this->sn_model->get_organizations(['org_type_code' => ORG_TYPE_BUSINESS_CENTER, 'org_code' => ['IN', $this->session->userdata('user_orgs')]]);
			$data['nomination_types'] = $this->sn_model->get_param_contractor_process_categories_by_process_type(CONTRACTOR_PROCESS_SITE);

			$this->load->view('modals/nominate_site', $data);
			$this->load_resources->get_resource($resources);
		}
		catch(PDOException $e)
		{
			$msg  = $this->get_user_message($e);

     		 $this->error_modal( $msg );
		}
		catch(Exception $e)
		{
			$msg  = $this->get_user_message($e);

			$this->error_modal( $msg );
		}
	}

	public function process()
	{
		try
		{
			$flag   = ERROR;
			$fields = $this->_validate();
			
			Portal_Model::beginTransaction();
			
			$fields['created_by'] 	= $this->session->user_id;
			$fields['created_date'] = date(FORMAT_DB_DATETIME);

			$site_id 		= $this->sn_model->insert_site($fields);

			$where 			= ['site_id' 	=> $site_id];
			/* $sn_number      = 'SN '.str_pad($site_id, SITE_CODE_LENGTH, 0, STR_PAD_LEFT);

			$fields 		= ['site_code' 	=> $sn_number];

			$this->sn_model->update_site($fields, $where); */

			$audit_curr		= [$this->sn_model->get_details_for_audit(Portal_Model::PORTAL_TABLE_SITES, $where)];
			$sn_number 		= $audit_curr[0][0]['site_num'];

			$workflow 		= [
				'user_id' 				=> $this->session->user_id,
				'account_group_code' 	=> AG_CONTRACTORS,
				'workflow_for_type'		=> WORKFLOW_FOR_BAVI,
				'reference_num'			=> $sn_number,
				'reference_id'			=> $site_id,
				'org_code'				=> $fields['org_code']
			];
			 
			$workflow_det = $this->pria_workflow->copy_worfklow(CORE_WORKFLOW_CONTRACTOR_SITE_NOMINATION, $workflow);
			
			
			$this->tag_task($workflow_det['task_id'], TASK_STATUS_ONGOING, array('manual_get' => ENUM_YES));

			$overview_details   = [
				'transaction_num' 		=> $sn_number,
				'transaction_msg'   	=> $this->lang->line('add_transaction_site_nomination'),
				//'reference' 		=> ISSET($id) ? $id : NULL,
				'reference' 			=> $site_id,
				'created_by' 			=> $this->session->userdata('user_id'),
				'created_date' 			=> date('Y-m-d H:i:s'),
				'keyword'               => $sn_number,
				'account_group_code' 	=> AG_CONTRACTORS,
				'tab_module_code'	 	=> $this->tab_module_code,
				'parent_module_code' 	=> $this->module_code,
				'core_workflow_id'  	=> CORE_WORKFLOW_CONTRACTOR_SITE_NOMINATION
			];
			
			$this->pria_overview->log_overview($this->module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

			

			$activity 		= sprintf($this->lang->line('audit_trail_add'), 'Site Nomination');
		
			$audit_prev 	= [[]];
			$audit_action 	= [AUDIT_INSERT];
			$audit_table 	= [Portal_Model::PORTAL_TABLE_SITES];
			$audit_schema   = [DB_PORTAL];

			$this->audit_trail->log_audit_trail(
				$activity, 
				$this->module_code, 
				$audit_prev, 
				$audit_curr, 
				$audit_action, 
				$audit_table, 
				$audit_schema
			);
			
			Portal_Model::commit();
		
			$flag 	= SUCCESS;
		
			$msg    = 'Your SN # is '.$sn_number;
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);
		
			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);
		
			Portal_Model::rollback();
		}
		
		echo json_encode([
			'flag'  => $flag,
			'msg'   => $msg
		]);
	}

	private function _validate()
	{
		if( ! check_permission($this->tab_module_code, ACTION_ADD)) throw new Exception($this->lang->line('invalid_action'));

		$params  = get_params();
		
		$params  = $this->set_filter( $params )
            ->filter_string('business_center')
            ->filter_string('suggested_name')
			->filter();

		$required = ['business_center' => 'Business Center', 'suggested_name' => 'Suggested Name'];

		$this->check_required_fields($params, $required);
		
		/* Validate constraints */			
		$constraints['business_center']  = [
			'data_type'   => 'db_value',
			'name'        => 'Business Center',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'org_code',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_ORGANIZATIONS
		];

		$constraints['nomination_type']  = [
			'data_type'   => 'db_value',
			'name'        => 'Nomination Type',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'category_code',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES
		];

		$constraints['suggested_name'] = array(
			'data_type' => 'string',
			'name'		=> 'Suggested Name',				
		);
			
		$data   = $this->validate_inputs($params, $constraints);

		return [
			'org_code' 				=> $data['business_center'],
			'suggested_store_name'  => $data['suggested_name'],
			'nomination_type_code'  => $data['nomination_type'],
			'status_code'			=> PARAM_STATUS_ONGOING,
			'site_type_code'		=> SITE_TYPE_STORE
		];
	}
}