<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
		$this->module_code      	= MODULE_PORTAL_TRANS_CONTRACTORS;
		$this->tab_module_code      = MODULE_PORTAL_TRANS_CONTRACTORS_PROJECTS;
		$this->controller 	  		= strtolower(__CLASS__);
		$this->module_js			= $this->module_js_path.strtolower(__CLASS__);
		
		$this->load->model(FOLDER_PROJECTS.'/projects_model', 'proj_model');
		$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');
		$this->load->model(FOLDER_BOQ.'/boq_model', 'bq_model');

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

			$resources['load_js']	= [$this->module_js];
			$resources['load_css']  = [CSS_SELECTIZE];


			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if($list['list']['next_records'] > 0)
			{
				$footer					= array(
						'container'		=> '.list-toggle',
						'path'			=>  base_url().PORTAL_TRANSACTIONS.'/tabs/'.PORTAL_TAB_PROJECTS.'/page/'.$encoded_module_code.'/'.$encoded_tab_module.'/0/',
						'append'		=> 'li',
						'last_page'		=> '#scroll-next-page',
						'get_params'	=> $filter_param
				); 
			}

			$resources['load_materialize_modal']	=  [
				'modal_project' 	=> array(
				  'size' 					=> 'sm-w sm-h',
				  'custom_title' 			=> 'Add Project', 
				  'post'					=> true,
				  'module' 					=> PORTAL_TRANSACTIONS,
				  'method' 					=> 'modal_project',
				  'controller' 				=> 'tabs/projects',
				  'custom_button'	=> array(
							'Add Project' => array(
								'type' 		=> 'button',
								'action' 	=> 'Add Project'
							)
						)
				)		
			];


			
			//Set up additional actions in tab
			if(check_permission($tab_module, ACTION_ADD))
			{
				$buttons[]				= array(
						'label'			=> 'Add Project',
						'class'			=> 'purple darken-1',
						'id'			=> 'add_proj',
						'target'		=> 'modal_project',
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

			$projects					= $this->proj_model->get_projects_list($where, NULL, $scope_details['having']);

			//Get the next batch of internal orders
			$where['limit']['from']		= $page_num * SYS_SETTING_DISPLAY_LIST_NO;
			$next_records	 			= $this->proj_model->get_projects_list($where, TRUE, $scope_details['having']);

			//Determines if this is the last page.
			$last_page 	 	 			= ($next_records > 0)? FALSE : TRUE;

			$data 						= array(
				'list'					=> array(
					'list' 				=> $projects, 
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

	public function modal_project()
	{
		try
		{
			$data = $resources = [];
			
			$resources['loaded_init'] 	= ['selectize_init();',  'Projects.initModal();'];

			$data['boqs']				= [];

			$scope_details 					= get_scope_details($this->tab_module_code);

			$where_sites 					= ['status_code' => PARAM_STATUS_COMPLETED];

			if( ! EMPTY($scope_details['orgs']))
				$where_sites['org_code'] 	=  ['IN', $scope_details['orgs']];
			
			$data['sites'] 					= $this->sn_model->get_sites($where_sites, ['site_id', 'official_store_name'], ['official_store_name' => 'ASC']);
			
			$this->load->view('modals/add_project', $data);
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

	public function get_boqs()
	{
		try
		{
			$boqs   = [];
			$flag   = ERROR;
			$msg    = '';
			$params = get_params();
			
			Portal_Model::beginTransaction();

			$constraints['site']  = [
				'data_type'   => 'db_value',
				'name'        => 'Site',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'site_id',
				'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_SITES
			];
		
		/* 	$constraints['project_type']  = [
				'data_type'   => 'db_value',
				'name'        => 'Project Type',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'category_code',
				'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES
			]; */
		
			$data   = $this->validate_inputs($params, $constraints);	

			$where  = [
				'site_id' 			=> $data['site'],
				'status_code'		=> PARAM_STATUS_COMPLETED,
				//'boq_category_code' => $data['project_type'],
			];
			
			$fields = [
				'boq_id as value',
				'boq_code as text'
			];

			$boqs  = $this->bq_model->get_boqs($where, $fields);		
		
			Portal_Model::commit();
		
			$flag 	= SUCCESS;
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
			'msg'   => $msg,
 			'boqs'  => json_encode($boqs)
		]);
	}


	public function get_boq_project_types()
	{
		try
		{
			$projs  = [];
			$flag   = ERROR;
			$msg    = '';
			$params = get_params();
			
			Portal_Model::beginTransaction();

			$constraints['boq']  = [
				'data_type'   => 'db_value',
				'name'        => 'Boq',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'boq_id',
				'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_BOQ
			];
	
		
			$data   = $this->validate_inputs($params, $constraints);	
/* 
			$where  = [
				'site_id' 			=> $data['site'],
				'status_code'		=> PARAM_STATUS_COMPLETED,
			];
			
			$fields = [
				'boq_id as value',
				'boq_code as text'
			]; */

			//$boqs  = $this->bq_model->get_boqs($where, $fields);		
			$list  = $this->bq_model->get_boq_asset_codes_joined_to_params($data['boq']);		

			foreach($list as $l)
				$projs[] = ['text' => $l['category_name'], 'value' => $l['asset_type']];

		
			Portal_Model::commit();
		
			$flag 	= SUCCESS;
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
			'flag'   => $flag,
			'msg'    => $msg,
 			'projs'  => json_encode($projs)
		]);
	}

	public function process()
	{
		try
		{
			$flag   = ERROR;
			$params = $this->_validate();
			//print_var_export($params); die;
			Portal_Model::beginTransaction();
			
			$fields = [
				'site_id' 	           => $params['site'],
				'boq_id'			   => $params['boq'],
				//'project_type_code'	   => $params['project_type'],
				'status_code'  		   => PROJECT_STATUS_ONGOING,
				'created_by'		   => $this->session->user_id,
				'created_date'		   => date(FORMAT_DB_DATETIME)
			];

			$project_id 	= $this->proj_model->insert_project($fields);

			$proj_number 	= str_pad($project_id, PROJ_CODE_LENGTH, 0, STR_PAD_LEFT);

			$where 			= ['project_id'  	=> $project_id];
			$fields 		= ['project_code' 	=> $proj_number];

			$this->proj_model->update_project($fields, $where);


			foreach($params['project_types'] as $key => $value)
			{
				$param = ['project_type' => $value];

				$constraints['project_type']  = [
					'data_type'   => 'db_value',
					'name'        => 'Project Type',
					'field'       => 'COUNT( 1 ) as check_row',
					'check_field' => 'check_row',
					'where'       => 'category_code',
					'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES
				];

				$data 	= $this->validate_inputs($param, $constraints);	

				$fields = [
					'project_id' 		=> $project_id,
					'project_type_code'	=> $data['project_type']
				];

				$this->proj_model->insert_project_types($fields);
			}


			$workflow_id 	= $this->get_workflow_id_by_tab_module($this->tab_module_code);
			
			$site_details 	= $this->proj_model->get_site(['site_id' => $params['site']], ['org_code']);

			$workflow 		= [
				'user_id' 				=> $this->session->user_id,
				'account_group_code' 	=> AG_CONTRACTORS,
				'workflow_for_type'		=> WORKFLOW_FOR_BAVI,
				'reference_num'			=> $proj_number,
				'reference_id'			=> $project_id,
				'org_code'				=> $site_details['org_code']
			];
			 
			$workflow_dets 		= $this->pria_workflow->copy_worfklow($workflow_id, $workflow);
			
			$this->tag_task($workflow_dets['task_id'], TASK_STATUS_ONGOING, ['reference' => $project_id, 'manual_get' => ENUM_YES]);

			$overview_details   = [
				'transaction_num' 	 	=> $proj_number,
				'transaction_msg'   	=> $this->lang->line('add_transaction_project'),
				'reference' 		 	=> $project_id,
				'created_by' 		 	=> $this->session->userdata('user_id'),
				'created_date' 		 	=> date('Y-m-d H:i:s'),
				'keyword'            	=> $proj_number,
				'account_group_code' 	=> AG_CONTRACTORS,
				'tab_module_code'	 	=> $this->tab_module_code,
				'parent_module_code' 	=> $this->module_code,
				'core_workflow_id'  	=> $workflow_id
			];

			$this->pria_overview->log_overview($this->module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

			$audit_curr		= [$this->proj_model->get_details_for_audit(Portal_Model::PORTAL_TABLE_PROJECTS, $where)];

			$activity 		= sprintf($this->lang->line('audit_trail_add'), 'Project');
		
			$audit_prev 	= [[]];
			$audit_action 	= [AUDIT_INSERT];
			$audit_table 	= [Portal_Model::PORTAL_TABLE_PROJECTS];
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
		
			$msg    = 'Your Project # is '.$proj_number;
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
		$params = get_params();

		if( ! check_permission($this->tab_module_code, ACTION_ADD)) throw new Exception($this->lang->line('invalid_action'));

		$params = get_params();

		$params  = $this->set_filter( $params )
		->filter_number('site')
		->filter_number('boq')
	//	->filter_string('project_type')
		->filter();

		$required = [
			'site'         => 'Site',
		//	'project_type' => 'Project Type',
			'boq'          => 'BOQ'
		];

		/* Validate constraints */			
		$constraints['site']  = [
			'data_type'   => 'db_value',
			'name'        => 'Site',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'site_id',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_SITES
		];

	/* 	$constraints['project_type']  = [
			'data_type'   => 'db_value',
			'name'        => 'Project Type',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'category_code',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES
		]; */

		$constraints['boq']  = [
			'data_type'   => 'db_value',
			'name'        => 'BOQ',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'boq_id',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_BOQ
		];
	
		$data   = $this->validate_inputs($params, $constraints);	

		$this->check_required_fields($params, $required);

		$data['project_types'] = $params['project_type'];

		return $data;
	}
}