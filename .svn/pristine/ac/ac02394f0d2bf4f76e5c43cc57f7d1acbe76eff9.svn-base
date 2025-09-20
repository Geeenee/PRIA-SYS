<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_status extends Portal_Controller 
{
	private $module;
	private $module_folder;
	private $controller;
	private $module_js;

	private $permission_view;
	private $permission_add;
	private $permission_edit;
	private $permission_delete;

	protected $model_name = 'payment_status_model';

	public function __construct()
	{
		parent::__construct();

		$this->module        = MODULE_PORTAL_REPORT_PAYMENT_STATUS;
		$this->module_folder = MODULE_PORTAL_REPORTS;
		$this->controller    = strtolower(__CLASS__);

		$this->load->model(PORTAL_REPORTS.'/Payment_status_model', 'payment_status_model');

		try
		{
			$this->permission_view    = $this->permission->check_permission($this->module, ACTION_VIEW);
			$this->permission_add     = $this->permission->check_permission($this->module, ACTION_ADD);
			$this->permission_edit    = $this->permission->check_permission($this->module, ACTION_EDIT);
			$this->permission_delete  = $this->permission->check_permission($this->module, ACTION_DELETE);
		}
		catch (PDOException $e)
		{
			$this->is_construct_error = TRUE;
			$this->construct_error_msg  = $this->get_user_message($e);
		}
		catch (Exception $e)
		{
			$this->is_construct_error = TRUE;
			$this->construct_error_msg  = $e->getMessage();
		}

		$hash_module = $this->hash($this->module);

		$this->security_action_add    = $hash_module . $this->hash(ACTION_ADD);
		$this->security_action_edit   = $hash_module . $this->hash(ACTION_EDIT);
		$this->security_action_delete = $hash_module . $this->hash(ACTION_DELETE);
		$this->security_action_view   = $hash_module . $this->hash(ACTION_VIEW);

		$this->reference_titles   = array(
			AG_CONTRACT_GROWERS   => array('Internal Order'),
			AG_GOODS_BAVI         => array('Purchase Order'),
			AG_GOODS_BFFI         => array('Contractors'),
			AG_INBOUND_NORMAL     => array('Lessors'),
			AG_OUTBOUND_NORMAL    => array('Truckers')
		);

		$this->reference_b    = array(
			REF_B_APV         => array('APV and APV Date'),
			REF_B_CV          => array('CV and CV Date'),
			REF_B_DR          => array('DR and DR Date')
		);
	}

	public function index()
	{
		try
		{

			$data         = array();
			$resources    = array();

			$common_resource          = $this->get_common_resources(MODULE_PORTAL_REPORT_PAYMENT_STATUS);

			$this->module_js          = HMVC_FOLDER."/".SYSTEM_PORTAL."/reports/payment_status";

			$resources['load_css']    = array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER, CSS_DATATABLE_BUTTONS));
			$resources['load_js']     = array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js, JS_BUTTON_EXPORT_EXTENSION));

			$data['reference_titles'] = $this->reference_titles;
			$data['reference_b']      = $this->reference_b;

			$table_options = array(
				'table_id'          => 'tbl_payment_report',
				'path'              => 'reports/payment_status/get_payment_status_list',
				'advanced_filter'   => TRUE,
				'hidden_column'	    => 7,
				'buttons'		    => ['excel', 'colvis'],
				'export_title'	    => "Payment Status",
				'export_file_name'  => "Payment Status " . date('Y-m-d-H-i-s')
			);

			//Account Groups
			$where                    = array('dashboard_flag' => YES_FLAG);
			$data['account_groups']   = $this->payment_status_model->get_all_account_groups($where);

			//Business Centers
			$data['organizations']    = $this->get_scoped_org_by_ag($this->module, NULL, NULL, ORG_TYPE_BUSINESS_CENTER);

			$resources['datatable']   = $table_options;
			$options_encoded          = json_encode($table_options);
			$resources['loaded_init'] = array_merge($common_resource['init'],
				array(
					"Payment_status.initialize('".$options_encoded."')"
				)
			);
		}
		catch( PDOException $e )
		{
			$msg  = $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg    = $this->rlog_error($e, TRUE);  

			$this->error_index( $msg );
		}

		$this->template->load('payment_status', $data, $resources, Portal_Controller::$system);
	}

	public function get_payment_status_list($account_group = NULL, $business_center = NULL, $reference_a = NULL, $reference_b = NULL)
	{
		$flag     = $total_records = $display_records = 0;
		$table_data = array();

		try
		{
			$params = get_params();
			$searches   = array();

			//Account Group
			if(!EMPTY($account_group) AND $account_group != SELECT_ALL)
				$searches['account_group'] = $account_group;

			//Business Center
			if(!EMPTY($business_center) AND $business_center != SELECT_ALL)
				$searches['business_center'] = $business_center;

			//Reference A
			if(!EMPTY($reference_a) AND $reference_a != SELECT_ALL)
				$searches['reference_a'] = $reference_a;

			//Reference B
			if(!EMPTY($reference_b) AND $reference_b != SELECT_ALL)
				$searches['reference_b'] = $reference_b;

    		$orgs = get_scope_details($this->module);
    		$orgs_having = (ISSET($orgs['having']))? $orgs['having']: "";

			$total_records    = $this->payment_status_model->get_payment_status_list(NULL, $searches, $orgs_having);
			$records_info     = $this->payment_status_model->get_payment_status_list($params, $searches, $orgs_having);

			$records          = $records_info['records'];
			$display_records  = $records_info['display_records'];

			foreach($records as $records)
			{
				$table_data[] = array(
					$records["org_name"],
					$records["account_group_name"],
					$records["store_name"],
					"<a href=\"".base_url().$records["trans_link"]."?keyword=".$records["ref_a_no"]."#".$records["link_tab"]."\">".$records["ref_a_no"]."</a>",
					$records["ref_a_date"],
					!EMPTY($records["ref_b_no"]) ? $records["ref_b_no"] : '-',
					!EMPTY($records["ref_b_date"]) ? $records["ref_b_date"] : '-',
					''
				);
			}

			$flag = 1;
			$msg  = "";
		}
		catch(PDOException $e)
		{
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'                => $table_data,
				'sEcho'                 => intval($params['sEcho']),
				'iTotalRecords'         => $total_records,
				'iTotalDisplayRecords'  => $display_records,
				'flag'                  => $flag,
				'msg'                   => $msg
			)
		);
	}


	public function get_reference_a()
	{
		try
		{ 
			$flag         = ERROR;
			$params       = get_params();
			$reference_a  = array();
			$reference_b  = array();

			$account_group    = (ISSET($params['account_group']))? $params['account_group']: NULL;

			if($account_group)
			{
				switch ($account_group)
				{
					case AG_CONTRACT_GROWERS:

						$reference_a	= [['value' => PORTAL_TAB_IO, 'text' => "INTERNAL ORDER"]];
						$reference_b	= [['value' => REF_B_APV, 'text' => "APV"], ['value' => REF_B_CV, 'text' => "CV"]];

					break;

					case AG_CONTRACTORS:

						$reference_a	= [['value' => PORTAL_TAB_PURCHASE_ORDERS, 'text' => "PURCHASE ORDER"]];
						$reference_b	= [['value' => REF_B_APV, 'text' => "APV"], ['value' => REF_B_CV, 'text' => "CV"]];

					break;

					case AG_GOODS_BFFI:

						$reference_a	= [['value' => PORTAL_TAB_PURCHASE_ORDERS, 'text' => "PURCHASE ORDER"]];
						$reference_b	= [['value' => REF_B_APV, 'text' => "APV"], ['value' => REF_B_CV, 'text' => "CV"], ['value' => REF_B_DR, 'text' => "DR"]];

					break;

					case AG_GOODS_BAVI:
					case AG_GOODS_MARINADES:
					case AG_TOLL_PARTNERS:
					case AG_FORWARDERS:
					case AG_INBOUND_CENTRAL:
					case AG_INBOUND_NORMAL:
					case AG_OUTBOUND:
					case AG_FEEDMILL:
					case AG_MANPOWER:
					case AG_SOA_BASED:

						$reference_a	= [['value' => PORTAL_TAB_SOA, 'text' => "SOA"]];
						$reference_b	= [['value' => REF_B_APV, 'text' => "APV"], ['value' => REF_B_CV, 'text' => "CV"]];

					break;

					case AG_LESSORS:

						$reference_a	= [['value' => PORTAL_TAB_CONTRACTS, 'text' => "CONTRACTS"]];
						$reference_b	= [['value' => REF_B_APV, 'text' => "APV"], ['value' => REF_B_CV, 'text' => "CV"]];

					break;

					default:

						$reference_a	= [['value' => SELECT_ALL, 'text' => "All"]];
						$reference_b	= [['value' => SELECT_ALL, 'text' => "All"]];

						$account_group	= ($account_group == SELECT_ALL)? NULL: $account_group;

					break;
				}

				// $reference_a	= array_merge([['value' => SELECT_ALL, 'text' => "All"]], $reference_a);
				// $reference_b	= array_merge([['value' => SELECT_ALL, 'text' => "All"]], $reference_b);

				$business_centers	= $this->get_scoped_org_by_ag($this->module, NULL, $account_group, ORG_TYPE_BUSINESS_CENTER);
				$business_centers	= array_merge([['value' => SELECT_ALL, 'text' => "All"]], $business_centers);
			}

			$flag   = SUCCESS;

			$msg   = $this->lang->line('data_saved');
		}
		catch(PDOException $e)
		{
			$msg  = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg    = $this->rlog_error($e, TRUE);
		}

		echo json_encode([
			'flag'          	=> $flag,
			'msg'           	=> $msg,
			'reference_a'   	=> $reference_a,
			'reference_b'   	=> $reference_b,
			'business_centers'	=> $business_centers
		]);
	}
}