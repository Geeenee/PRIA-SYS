<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Boq extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module_code      	= MODULE_PORTAL_TRANS_CONTRACTORS;
		$this->tab_module_code      = MODULE_PORTAL_TRANS_CONTRACTORS_BOQ;
		$this->controller 	  		= strtolower(__CLASS__);
		$this->module_js			= $this->module_js_path.strtolower(__CLASS__);
		
		$this->load->model(FOLDER_BOQ.'/boq_model');
		$this->load->model(PORTAL_CODE_LIBRARIES.'/vendor_model', 'vm_model');
		$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

		$this->load->library('pria_workflow');
		$this->load->library('pria_overview');
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
				'modal_boq' 	=> array(
				  'size' 					=> 'sm-w md-h',
				  'custom_title' 			=> 'Add BOQ', 
				  'post'					=> true,
				  'module' 					=> PORTAL_TRANSACTIONS,
				  'method' 					=> 'modal_boq',
				  'controller' 				=> 'tabs/boq',
				  'custom_button'	=> array(
							'Add BOQ' => array(
								'type' 		=> 'button',
								'action' 	=> 'Add BOQ'
							)
						)
				)		
			];

			$resources['load_js']	= [$this->module_js, JS_LABELAUTY];
			$resources['load_css']  = [CSS_SELECTIZE];
			
			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if($list['list']['next_records'] > 0)
			{
				$footer					= array(
						'container'		=> '.list-toggle',
						'path'			=>  base_url().PORTAL_TRANSACTIONS.'/tabs/'.PORTAL_TAB_BOQ.'/page/'.$encoded_module_code.'/'.$encoded_tab_module.'/0/',
						'append'		=> 'li',
						'last_page'		=> '#scroll-next-page',
						'get_params'	=> $filter_param
				); 
			}
			
			//Set up additional actions in tab
			if(check_permission($tab_module, ACTION_ADD))
			{
				$buttons[]				= array(
						'label'			=> 'Add BOQ',
						'class'			=> 'purple darken-1',
						'id'			=> 'add_boq',
						'target'		=> 'modal_boq',
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
			
			$rmv_having_str = ! EMPTY( ($where['filter']['having']) ) ? TRUE : FALSE;

			$scope_details 	= get_scope_details($tab_module, '', $rmv_having_str);

			$boqs						= $this->boq_model->get_boqs_list($where, NULL, $scope_details['having']);

			//Get the next batch of internal orders
			$where['limit']['from']		= $page_num * SYS_SETTING_DISPLAY_LIST_NO;
			$next_records	 			= $this->boq_model->get_boqs_list($where, TRUE, $scope_details['having']);
		
			//Determines if this is the last page.
			$last_page 	 	 			= ($next_records > 0)? FALSE : TRUE;

			$data 						= array(
				'list'					=> array(
					'list' 				=> $boqs, 
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

	public function modal_boq()
	{
		try
		{
			$data = $resources = [];
			
			$resources['loaded_init'] 		= ['selectize_init();', 'labelauty_init();', 'Boq.initModal();'];

			$scope_details 					= get_scope_details($this->tab_module_code);

			$where_sites 					= ['status_code' => PARAM_STATUS_COMPLETED, 'site_type_code' => SITE_TYPE_STORE];

			if( ! EMPTY($scope_details['orgs']))
				$where_sites['org_code'] 	=  ['IN', $scope_details['orgs']];
			
			$data['sites'] 					= $this->sn_model->get_sites($where_sites, ['site_id', 'official_store_name'], ['official_store_name' => 'ASC']);
		
			$data['contractors'] 			= $this->vm_model->get_vendor_by_ag_code_n_org_code([AG_CONTRACTORS], $scope_details['orgs']);

			$this->load->view('modals/add_boq', $data);
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
		
			$params = $this->_validate();

			Portal_Model::beginTransaction();

			$fields = [
				'site_id' 	           => $params['site'],
				'status_code'	  	   => PARAM_STATUS_ONGOING,
				'inhouse' 	           => (ISSET($params['third_party']) && $params['third_party'] == INITIAL_YES) ? ENUM_NO: ENUM_YES,
				'additional_flag'	   => (ISSET($params['additional_flag']) && !EMPTY($params['additional_flag'])) ? INITIAL_YES: INITIAL_NO,
				'created_by'		   => $this->session->user_id,
				'created_date'		   => date(FORMAT_DB_DATETIME)
			];

			if(ISSET($params['third_party']) && $params['third_party'] == INITIAL_YES)
			{
				if($params['recommended_contractor'] != 'new')
					$fields['recommended_vendor_code'] = $params['recommended_contractor'];
				else
					$fields['new_contractor'] 		   = $params['new_contractor'];
			}	

			$site_details 	= $this->boq_model->get_site(['site_id' => $params['site']], ['nomination_type_code', 'org_code']);

		//	$fields['boq_category_code'] = $site_details['nomination_type_code'];
			
			$boq_id 		= $this->boq_model->insert_boq($fields);

			//$boq_number 	= 'BOQ '.str_pad($boq_id, BOQ_CODE_LENGTH, 0, STR_PAD_LEFT);

			$where 			= ['boq_id'   => $boq_id];
			/* $fields 		= ['boq_code' => $boq_number];

			$this->boq_model->update_boq($fields, $where); */

			$audit_curr		= [$this->sn_model->get_details_for_audit(Portal_Model::PORTAL_TABLE_PRIA_BOQ, $where)];

			$boq_number     = $audit_curr[0][0]['boq_code'];

			$workflow 		= [
				'user_id' 				=> $this->session->user_id,
				'account_group_code' 	=> AG_CONTRACTORS,
				'workflow_for_type'		=> WORKFLOW_FOR_BAVI,
				'reference_num'			=> $boq_number,
				'reference_id'			=> $boq_id,
				'org_code'				=> $site_details['org_code']
			];
			 
			$workflow_dets 		= $this->pria_workflow->copy_worfklow(CORE_WORKFLOW_CONTRACTOR_BOQ, $workflow);

			$pria_task_id 		= $workflow_dets['task_id'];
		
			$this->tag_task($pria_task_id, TASK_STATUS_ONGOING, ['reference' => $boq_id, 'manual_get' => ENUM_YES]);

			$overview_details   = [
				'transaction_num' 		=> $boq_number,
				'transaction_msg'   	=> $this->lang->line('add_transaction_boq'),
				//'reference' 			=> ISSET($id) ? $id : NULL,
				'account_group_code' 	=> AG_CONTRACTORS,
				'reference' 			=> $boq_id,
				'created_by' 			=> $this->session->userdata('user_id'),
				'created_date' 			=> date('Y-m-d H:i:s'),
				'keyword'           	=> $boq_number,
				'tab_module_code'	 	=> $this->tab_module_code,
				'parent_module_code' 	=> $this->module_code,
				'core_workflow_id'  	=> CORE_WORKFLOW_CONTRACTOR_BOQ
			];

			if((ISSET($params['additional_flag']) && !EMPTY($params['additional_flag']))){
				$this->pria_workflow->_skip_stage_tasks( $this->tm_model->get_task_details($pria_task_id));
			}

			$this->pria_overview->log_overview($this->module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

			$activity 		= sprintf($this->lang->line('audit_trail_add'), 'BOQ');
		
			$audit_prev 	= [[]];
			$audit_action 	= [AUDIT_INSERT];
			$audit_table 	= [Portal_Model::PORTAL_TABLE_PRIA_BOQ];
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
		
			$msg    = 'Your BOQ # is '.$boq_number.	 'pria task id: '. $pria_task_id;;
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

		$params = get_params();

		$params  = $this->set_filter( $params )
		->filter_number('site')
		->filter_number('third_party')
		->filter_string('recommended_contractor')
		->filter_string('new_contractor')
		->filter_number('additional_flag')
		->filter();

		$required = ['site' => 'Official Store Name'];

		if(ISSET($params['third_party']) && $params['third_party'] == INITIAL_YES)
		{
			$required['recommended_contractor'] = 'Recommended Contractor';

			if($params['recommended_contractor'] != 'new')
			{
				$constraints['recommended_contractor']  = [
					'data_type'   => 'db_value',
					'name'        => 'Recommended Contractor',
					'field'       => 'COUNT( 1 ) as check_row',
					'check_field' => 'check_row',
					'where'       => 'vendor_code',
					'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_VENDORS
				];
			}
			else
			{
				$required['new_contractor'] 	= 'New Contractor';	
			
				$constraints['new_contractor'] 	= array(
					'data_type' => 'string',
					'name'		=> 'New Contractor',				
				);

				$constraints['recommended_contractor']  = [
					'data_type'			=> 'enum',
					'name'				=> 'Recommended Contractor',
					'allowed_values' 	=> array('new')
				];
			}	

			$constraints['third_party']	= array(
				'data_type'			=> 'enum',
				'name'				=> 'Third Party',
				'allowed_values' 	=> $this->enum_yes_num
			);
		}	

		/* Validate constraints */			
		$constraints['site']  = [
			'data_type'   => 'db_value',
			'name'        => 'Official Store Name',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'site_id',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_SITES
		];

		$constraints['additional_flag']	= array(
			'data_type'			=> 'enum',
			'name'				=> 'Project Type',
			'allowed_values' 	=> [INITIAL_YES, INITIAL_NO]
		);
	
		$data   = $this->validate_inputs($params, $constraints);	

		$this->check_required_fields($data, $required);

		return $data;
	}
}