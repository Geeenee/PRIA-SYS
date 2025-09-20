<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* @Author      : Jhun Baria
* @Date        : 2023-08-11 22: 00: 00 
* @Desc        : The Following Controller is Made for the purpose of CDI Payments Transaction
* @ReferencedBy: 
*/

class Cdi_payments extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module_code 	  		= MODULE_PORTAL_TRANS_STORE_RENOVATION;
		$this->tab_module_code      = MODULE_PORTAL_TRANS_STORE_RENOVATION_PAYMENTS;
		$this->controller 	  		= strtolower(__CLASS__);
		$this->module_js			= $this->module_js_path.strtolower(__CLASS__);
        // $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

		$this->load->library('pria_workflow');
		$this->load->library('pria_overview');

		$this->load->model('task_model', 'tm_model');
		$this->load->model('workflow_model', 'wm_model');

		$this->load->model(FOLDER_CDI.'/cdi_bom_approvals_model', 'cba_model');
		$this->load->model(FOLDER_CDI.'/cdi_payments_model', 'cp_model');
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
				'modal_cdi_payment' 	=> array(
				  'size' 					=> 'sm-w sm-h',
				  'custom_title' 			=> 'Payment', 
				  'post'					=> true,
				  'module' 					=> PORTAL_TRANSACTIONS,
				  'method' 					=> 'modal_cdi_payment',
				  'controller' 				=> 'tabs/cdi_payments',
				)		
			];

			$resources['load_js']  = [$this->module_js];
			
			//If more than SYS_SETTING_DISPLAY_LIST_NO, display and apply config for infinite scroll
			if($list['list']['next_records'] > 0)
			{
				$footer					= array(
						'container'		=> '.list-toggle',
						'path'			=>  base_url().PORTAL_TRANSACTIONS.'/tabs/'.PORTAL_TAB_STORE_PAYMENTS.'/page/'.$encoded_module_code.'/'.$encoded_tab_module.'/0/',
						'append'		=> 'li',
						'last_page'		=> '#scroll-next-page',
						'get_params'	=> $filter_param
				); 
			}
			
			//Set up additional actions in tab

			if(check_permission($tab_module, ACTION_ADD))
			{
				$buttons[]				= array(
						'label'			=> 'Add Payment',
						'class'			=> 'purple darken-1',
						'id'			=> 'add_payment',
						'target'		=> 'modal_cdi_payment',
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
						// 'workflow_ids'	=> $workflow_ids
					),
					'filter'			=> $this->_construct_transactions_having($filter_params, $ag_codes),
					'limit'				=> array(
						'from'			=> $from,
						'to'			=> SYS_SETTING_DISPLAY_LIST_NO
					)
			);

			$rmv_having_str 			= ! EMPTY( ($where['filter']['having']) ) ? TRUE : FALSE;
			$scope_details 				= get_scope_details($tab_module, '', $rmv_having_str);
		
			$payments = $this->cp_model->get_payments_list($where, NULL, $scope_details['having']);

			//Get the next batch of internal orders
			$where['limit']['from']		= $page_num * SYS_SETTING_DISPLAY_LIST_NO;
			$next_records	 			= $this->cp_model->get_payments_list($where, TRUE, $scope_details['having']);

			//Determines if this is the last page.
			$last_page 	 	 			= ($next_records > 0)? FALSE : TRUE;

			$data 						= array(
				'list'					=> array(
					'list' 				=> $payments, 
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

	public function modal_cdi_payment()
	{
		try
		{
			$data = $resources = [];

            $resources['load_js'] = array($this->module_js);

			$resources['loaded_init'] = [
				'selectize_init();',  
				'datepicker_init();',
				'CDIPayment.initModal();',
				'CDIPayment.action();'
			];
			$where                    = [ 'status_code'  => PARAM_STATUS_COMPLETED ];
			$select                   = ['bom_id', 'official_store_name' ];

			$data['workflows'] = [
				[
					'workflow_name' => 'Security Deposit and Rent Advances',
					'workflow_id'   => CORE_WORKFLOW_STRRNV_PAYMENTS_SEC_DEP_RENT_ADV
				],
				[
					'workflow_name' => 'Mall Charges During Construction',
					'workflow_id'   => CORE_WORKFLOW_STRRNV_PAYMENTS_MALL_CHARGE
				]
			];

			$data['boms']             = $this->cba_model->get_boms($where, $select, ['official_store_name' => 'ASC']);
			$data['business_centers'] = $this->sn_model->get_organizations(['org_type_code' => ORG_TYPE_BUSINESS_CENTER, 'org_code' => ['IN', $this->session->userdata('user_orgs')]]);

			$this->load->view('modals/cdi_payments', $data);
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
			
			$fields['created_by']   = $this->session->user_id;
			$fields['created_date'] = date(FORMAT_DB_DATETIME);


			$payment_id = $this->cp_model->insert_payment($fields);

			$where      = ['payment_id' => $payment_id];
			$audit_curr = [$this->cp_model->get_details_for_audit( PORTAL_TABLE_CDI_PAYMENTS, $where)];
			$payments_number  = $audit_curr[0][0]['soa_num'];

			$workflow 		= [
				'user_id' 				=> $this->session->user_id,
				'account_group_code' 	=> AG_STORE_RENOVATION,
				'workflow_for_type'		=> WORKFLOW_FOR_BAVI,
				'reference_num'			=> $payments_number,
				'reference_id'			=> $payment_id,
				'org_code'				=> $fields['org_code']
			];
			 
			$workflow_det = $this->pria_workflow->copy_worfklow($fields['core_workflow_id'], $workflow);
			
			// $this->tag_task($workflow_det['task_id'], TASK_STATUS_ONGOING, array('manual_get' => ENUM_YES));

			$overview_details   = [
				'transaction_num' 		=> $payments_number,
				'transaction_msg'   	=> $this->lang->line('add_transaction_payment'),
				//'reference' 		=> ISSET($id) ? $id : NULL,
				'reference' 			=> $payment_id,
				'created_by' 			=> $this->session->userdata('user_id'),
				'created_date' 			=> date('Y-m-d H:i:s'),
				'keyword'               => $payments_number,
				'account_group_code' 	=> AG_STORE_RENOVATION,
				'tab_module_code'	 	=> $this->tab_module_code,
				'parent_module_code' 	=> $this->module_code,
				'core_workflow_id'  	=> $fields['core_workflow_id']
			];

			$this->pria_overview->log_overview($this->module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

			$activity 		= sprintf($this->lang->line('audit_trail_add'), 'Payment');
		
			$audit_prev 	= [[]];
			$audit_action 	= [AUDIT_INSERT];
			$audit_table 	= [PORTAL_TABLE_CDI_PAYMENTS];
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
		
			$msg    = 'Your Payment # is '.$payments_number;
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
			->filter_number('workflow')
            ->filter_string('business_center')
            ->filter_string('bom_id')
            ->filter_string('soa_num')
            ->filter_date('soa_document_date')
            ->filter_date('soa_submission_date')
			->filter();

		$required = [
			'workflow'            => 'Workflow',
			'business_center'     => 'Business Center',
			'bom_id'              => 'Store Name',
			'soa_num'             => 'SOA Num',
			'soa_document_date'   => 'SOA Document Date',
			'soa_submission_date' => 'SOA Submission Date'
		];

		$this->check_required_fields($params, $required);

		$constraints['soa_document_date']    = [
			'data_type'         => 'date',
			'name'              => 'SOA Document Date'
		];

		$constraints['soa_submission_date']    = [
			'data_type'         => 'date',
			'name'              => 'SOA Submission Date'
		];

		$constraints['soa_num']    = [
			'data_type'         => 'string',
			'name'              => 'SOA Number'
		];

		$constraints['workflow']  = [
			'data_type'   => 'db_value',
			'name'        => 'Workflow',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'workflow_id',
			'table'       =>  Portal_Model::CORE_WORKFLOWS
		];
		
		/* Validate constraints */			
		$constraints['business_center']  = [
			'data_type'   => 'db_value',
			'name'        => 'Business Center',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'org_code',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_ORGANIZATIONS
		];

		$constraints['bom_id']  = [
			'data_type'   => 'db_value',
			'name'        => 'Store',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'bom_id',
			'table'       =>  DB_PORTAL.'.'.PORTAL_TABLE_CDI_BOMS
		];

		$constraints['business_center']  = [
			'data_type'   => 'db_value',
			'name'        => 'Business Center',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'org_code',
			'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_ORGANIZATIONS
		];

		$data   = $this->validate_inputs($params, $constraints);

		return [
			'org_code'            => $data['business_center'],
			'bom_id'              => $data['bom_id'],
			'status_code'         => PARAM_STATUS_ONGOING,
			'core_workflow_id'    => $data['workflow'],
			'soa_num'             => $data['soa_num'],
			'soa_document_date'   => $data['soa_document_date'],
			'soa_submission_date' => $data['soa_submission_date']

		];
	}

    public function get_bom_details()
    {
        try
        {
			$params                    = get_params();
			$bom                       = $this->cba_model->get_bom_details($params['bom_id']);
			$bom_approval_date_task_id = PRES_BOM_APP_DATE_PRIA_TASK_ID;
			$where                     = "c.reference_id = {$bom['bom_id']} AND a.core_workflow_task_id = {$bom_approval_date_task_id}";
			$task_details              = $this->tm_model->get_task_details_custom_where($where);
            $bom_info      = [
                'bom_num'  => $bom['bom_num'],
                'org_name' => $bom['name'],
                'org_code' => $bom['org_code']
            ];  
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }

        echo json_encode(
            array(
                'bom_info' => $bom_info
            )
        );
    }

}