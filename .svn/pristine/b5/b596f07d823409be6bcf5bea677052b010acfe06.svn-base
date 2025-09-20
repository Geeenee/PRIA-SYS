<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Portal_Controller 
{

	private $module;
	private $module_folder;
	private $controller;

	public function __construct()
	{
		parent::__construct();

		$this->module			= MODULE_PORTAL_DASHBOARD;
		$this->module_folder	= PORTAL_DASHBOARD;
		$this->controller		= strtolower(__CLASS__);

		$this->permissions		= check_permission($this->module);
		$this->scope_details	= get_scope_details($this->module, NULL, TRUE);

		$this->module_js		= HMVC_FOLDER . "/" . SYSTEM_PORTAL . "/" . $this->module_folder . "/dashboard";

		$this->load->model('dashboard_model');

		$this->load->model(PORTAL_CODE_LIBRARIES. '/Vendor_model', 'vendor_model');

	}



	public function print_email($offset=0, $limit=200)
	{
		$this->load->model('Pria_mailer_model', 'pria_mailer_model');

		$emails = $this->pria_mailer_model->get_mail_notifications_by_last_hundred($offset, $limit);
	
		foreach($emails as $content) {
			print_r($content['message']);
			unset($content['message']);
			echo "<pre>";
			print_r($content);
			echo "</pre>";
		}

		exit;
	}

	public function check_email_notif($core_task_id, $pria_task_id, $task_action = NULL)
	{
		$core_task_id	= encrypt_id($core_task_id);
		$pria_task_id	= encrypt_id($pria_task_id);
		$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, $task_action, NULL, NULL, NULL, ['print_only' => TRUE]);
	}

	public function index()
	{
		try
		{

			$data									= array();
			$resources								= array();

			if(!$this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access')."".$this->module.json_encode($this->permissions));

			$common_resource						= $this->get_common_resources(MODULE_PORTAL_DASHBOARD);

			$resources['load_css']					= array_merge($common_resource['css'], array(CSS_LABELAUTY, CSS_SELECTIZE));
			$resources['load_js']					= array_merge($common_resource['js'], array(JS_LABELAUTY, JS_SELECTIZE, $this->module_js));
			$resources['loaded_init']				= array_merge($common_resource['init'], array("Dashboard.init();", "document.getElementById('content-wrapper').scrollIntoView();"));
			$resources['load_materialize_modal']	= $common_resource['modal'];

			// $data['user_notifications']				= $this->dashboard_model->get_user_notifications($this->session->user_id);

			$reminder_interval	= get_sys_param_val(SYS_PARAM_DASHBOARD_PARAM, SYS_PARAM_DASHBOARD_REMINDER_INTERVAL);
            $reminder_interval  = (ISSET($reminder_interval['sys_param_value']) AND !EMPTY($reminder_interval['sys_param_value']))? $reminder_interval['sys_param_value']: NULL;

			//Starts
			//$user_roles 							= $this->session->userdata('user_roles');
			$data['user_reminders']					= $this->dashboard_model->get_reminders($this->session->user_id, $reminder_interval);
			//Ends

			$data['user_tasks']						= $this->dashboard_model->get_user_tasks($this->session->user_id, $this->scope_details['having']);

			$account_groups							= $this->dashboard_model->get_all_account_groups(array('dashboard_flag' => ENUM_YES));
			
			if(COUNT($account_groups) > 0)
			{
				if(!in_array(TASK_ROLE_VENDOR, $this->session->userdata('user_roles')))
				{
					foreach($account_groups AS $key => $account_group)
					{
						switch($account_group['account_group_code'])
						{
							case AG_CONTRACT_GROWERS:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_CG_IO);
							break;
							case AG_CONTRACTORS:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_CONTRACTORS_PO);
							break;
							case AG_FORWARDERS:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_FORWARDER_SOA);
							break;
							case AG_GOODS_BAVI:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_GOODS_G_SOA);
							break;
							case AG_GOODS_BFFI:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_GOODS_G_PO);
							break;
							case AG_GOODS_MARINADES:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_GOODS_M_SOA);
							break;
							case AG_LESSORS:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_LESSORS_CONTRACTS);
							break;
							case AG_MANPOWER:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_MANPOWER_SOA);
							break;
							case AG_TOLL_PARTNERS:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA);
							break;
							case AG_FEEDMILL:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_SOA);
							break;
							case AG_INBOUND_NORMAL:
							case AG_INBOUND_CENTRAL:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA);
							break;
							case AG_OUTBOUND:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_SOA);
							break;
							case AG_SOA_BASED:
								$module_permissions	= check_permission(MODULE_PORTAL_TRANS_SOA_BASED_SOA);
							break;
						}

						if(COUNT(array_filter($module_permissions)) == 0)
						{
							unset($account_groups[$key]);
						}
					}

					$data['per_reminders'] = array_column($account_groups,'account_group_code');
				}
				else
				{
					$user 		= $this->dashboard_model->get_vendor_user(['user_id' => $this->session->userdata('user_id')], ['vendor_code']);

					$ag_codes 	= $this->vendor_model->get_vendor_account_groups(['vendor_code' => $user['vendor_code']]);
					$ag_codes 	= array_column($ag_codes, 'account_group_code');

					foreach($account_groups AS $key => $account_group)
					{
						if( ! in_array($account_group['account_group_code'], $ag_codes))
							unset($account_groups[$key]);
					}
				}
			}

			$data['account_groups']					= $account_groups;

			// PAGE TITLE
			$data['active_sub_menu']				= $this->module;
			$data['page_title']						= 'Dashboard';
			
			$data['body_color']						= 'grey lighten-4';

			$this->template->load('dashboard', $data, $resources, Portal_Controller::$system);
		}
		catch(PDOException $e)
		{
			$msg	= $this->get_user_message($e);
			$this->error_index($msg);
		}
		catch(Exception $e)
		{
			$msg	= $this->rlog_error($e, TRUE);
			$this->error_index($msg);
		}
	}

	public function generate_dashboard_payment($account_group_code)
	{
		try
		{
			$data = $resources = array();

			$resources['load_css']    	= array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATATABLE_BUTTONS);
			$resources['load_js']     	= array(JS_DATATABLE, JS_SELECTIZE, JS_DATATABLE_MATERIAL, JS_BUTTON_EXPORT_EXTENSION);

			$account_group_method		= "";
			$account_group_table_id		= "";
			$account_group_view			= "";
			$export_title				= "";
			$transaction_folder			= "";
			$transaction_tab			= "";

			$data['statuses'] 			= $this->dashboard_model->get_all_apv_status([], ['*'], ['apv_status_name' => 'ASC']);

			
			switch($account_group_code)
			{
				case AG_CONTRACT_GROWERS: 
					$account_group_method	= "get_payment_cg";
					$account_group_table_id	= "tbl_dashboard_cg";
					$account_group_view		= "contract_growers";
					$export_title			= "Contract Growers";
					$transaction_folder		= "contract_growers";
					$transaction_tab		= PORTAL_TAB_IO;
				break;
				case AG_CONTRACTORS:
					$account_group_method	= "get_payment_contractors";
					$account_group_table_id	= "tbl_dashboard_contractors";
					$account_group_view		= "contractors";
					$export_title			= "Contractors";
					$transaction_folder		= "contractors";
					$transaction_tab		= PORTAL_TAB_PURCHASE_ORDERS;
				break;
				case AG_FORWARDERS:
					$account_group_method	= "get_payment_forwarders";
					$account_group_table_id	= "tbl_dashboard_forwarders";
					$account_group_view		= "forwarders";
					$export_title			= "Forwarders";
					$transaction_folder		= "forwarders";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_GOODS_BAVI:
					$account_group_method	= "get_payment_goods_bavi";
					$account_group_table_id	= "tbl_dashboard_bavi";
					$account_group_view		= "goods_bavi";
					$export_title			= "Goods BAVI";
					$transaction_folder		= "goods";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_GOODS_BFFI:
					$account_group_method	= "get_payment_goods_bffi";
					$account_group_table_id	= "tbl_dashboard_bffi";
					$account_group_view		= "goods_bffi";
					$export_title			= "Goods BFFI";
					$transaction_folder		= "goods";
					$transaction_tab		= PORTAL_TAB_PURCHASE_ORDERS;
				break;
				case AG_GOODS_MARINADES:
					$account_group_method	= "get_payment_goods_marinades";
					$account_group_table_id	= "tbl_dashboard_marinades";
					$account_group_view		= "goods_marinades";
					$export_title			= "Goods Marinades";
					$transaction_folder		= "goods_marinades";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_LESSORS:
					$account_group_method	= "get_payment_lessors";
					$account_group_table_id	= "tbl_dashboard_lessors";
					$account_group_view		= "lessors";
					$export_title			= "Lessors";
					$transaction_folder		= "lessors";
					$transaction_tab		= PORTAL_TAB_CONTRACTS;
				break;
				case AG_MANPOWER:
					$account_group_method	= "get_payment_manpower";
					$account_group_table_id	= "tbl_dashboard_manpower";
					$account_group_view		= "manpower";
					$export_title			= "Manpower";
					$transaction_folder		= "manpower";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_TOLL_PARTNERS:
					$account_group_method	= "get_payment_toll_partners";
					$account_group_table_id	= "tbl_dashboard_toll_partners";
					$account_group_view		= "toll_partners";
					$export_title			= "Toll Partners";
					$transaction_folder		= "toll_partners";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_FEEDMILL:
					$account_group_method	= "get_payment_trucker_feedmills";
					$account_group_table_id	= "tbl_dashboard_feedmills";
					$account_group_view		= "trucker_feedmills";
					$export_title			= "Feedmill Toll";
					$transaction_folder		= "feedmill_truckers";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_INBOUND_NORMAL:
					$account_group_method	= "get_payment_trucker_inbound";
					$account_group_table_id	= "tbl_dashboard_inbound";
					$account_group_view		= "trucker_inbound";
					$export_title			= "Inbound Normal Trucker";
					$transaction_folder		= "inbound_truckers";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_INBOUND_CENTRAL:
					$account_group_method	= "get_payment_trucker_inbound_centralized";
					$account_group_table_id	= "tbl_dashboard_inbound_centralized";
					$account_group_view		= "trucker_inbound_centralized";
					$export_title			= "Inbound Centralized Trucker";
					$transaction_folder		= "inbound_truckers";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_OUTBOUND:
					$account_group_method	= "get_payment_trucker_outbound";
					$account_group_table_id	= "tbl_dashboard_outbound";
					$account_group_view		= "trucker_outbound";
					$export_title			= "Outbound Trucker";
					$transaction_folder		= "outbound_truckers";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
				case AG_SOA_BASED:
					$account_group_method	= "get_payment_soa_based";
					$account_group_table_id	= "tbl_dashboard_soa_based";
					$account_group_view		= "soa_based";
					$export_title			= "SOA-Based";
					$transaction_folder		= "soa_based";
					$transaction_tab		= PORTAL_TAB_SOA;
				break;
			}

			$resources['datatable']				= array(
					'path'						=> $this->module_folder . '/' . $this->controller . '/' . $account_group_method . '/' . $account_group_code,
					'table_id'					=> $account_group_table_id,
					'scrollX'					=> TRUE,
					'scrollY'					=> "300px",
					'advanced_filter'			=> TRUE,
					'buttons'					=> ['excel', 'colvis'],
					'export_title'				=> "Payments - " . $export_title,
					'export_file_name'			=> "Payments - " . $export_title . " " . date('Y-m-d-H-i-s'),
					'post_data'					=> array('transaction_folder' => $transaction_folder, 'transaction_tab' => $transaction_tab),
					'sort_order'				=> 'desc',
					'hidden_column'				=> 0
			);

			$this->load->view('tabs/'.$account_group_view, $data);
			$this->load_resources->get_resource($resources);
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

	public function get_payment_cg($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_cg_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_cg_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['cycle'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['reference_number']): $record['reference_number'],
						$record['clean_up_date'],
						/* $record['harvest_date'],
						$record['harvest_pic'],
						$record['harvest_approval_date'],
						$record['harvest_approval_pic'],
						$record['live_sales_upload_date'],
						$record['live_sales_report_date'],
						$record['live_sales_pic'], */
						$record['fhr_upload_date'],
						$record['fhr_submission_date'],
						$record['fhr_approval_date'],
						$record['fhr_number'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_contractors($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_contractors_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_contractors_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['po_number']): $record['po_number'],
						$record['po_approved_date'],
						$record['boq_approved_date'],
						$record['boq_progress_approved_date'],
						$record['project_completion_approved_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_forwarders($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_forwarders_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_forwarders_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['soa_date_bavi'],
						$record['soa_date_calamba'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_goods_bavi($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_goods_bavi_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_goods_bavi_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						$record['po_number'],
						$record['po_amount'],
						$record['dr_number'],
						$record['gr_number'],
						$record['invoice_date'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['invoice_number']): $record['invoice_number'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_goods_bffi($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_goods_bffi_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_goods_bffi_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['po_number']): $record['po_number'],
						$record['po_amount'],
						$record['dr_number'],
						$record['gr_number'],
						$record['invoice_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_goods_marinades($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_goods_marinades_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_goods_marinades_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						$record['po_number'],
						$record['dr_number'],
						$record['gr_number'],
						$record['invoice_date'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['invoice_number']): $record['invoice_number'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_lessors($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			// KPOYAOAN 2021-03-11
			// Changed to New Contracts only from contracts table
			// Lessors with Renewal Process
			/*$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_lessors_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_lessors_list($this->scope_details['having'], $core_task_params, $params);*/

			$total_records		= $this->dashboard_model->get_payment_lessors_list($this->scope_details['having']);
			$records_info 		= $this->dashboard_model->get_payment_lessors_list($this->scope_details['having'], $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['contract_number']): $record['contract_number'],
						$record['store_name'],
						$record['contract_upload_date'],
						$record['payment_term_name'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_manpower($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_manpower_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_manpower_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						$record['soa_upload_date'],
						$record['soa_period'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_manpower_adv($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_manpower_adv_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_manpower_adv_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['business_center_name'],
						$record['vendor_name'],
						$record['cms_number'],
						$record['soa_upload_date'],
						$record['soa_period'],
						$record['soa_number'],
						$record['acknowledgement_date'],
						$record['apv_date'],
						$record['apv_number'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status']
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_toll_partners($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_toll_partners_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_toll_partners_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['soa_period'],
						$record['soa_date'],
						$record['acknowledgement_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_trucker_feedmills($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_trucker_feedmills_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_trucker_feedmills_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['soa_period'],
						$record['soa_upload_date'],
						$record['soa_approval_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_trucker_inbound($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_trucker_inbound_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_trucker_inbound_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['soa_date'],
						$record['soa_approval_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_trucker_inbound_centralized($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_trucker_inbound_centralized_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_trucker_inbound_centralized_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['soa_period'],
						$record['soa_upload_date'],
						$record['acknowledgement_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_trucker_outbound($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_trucker_outbound_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_trucker_outbound_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['soa_date'],
						$record['acknowledgement_date'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function get_payment_soa_based($account_group_code)
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$params				= get_params();

			$core_task_params	= $this->generate_param_w_vals($this->last_ag_tasks[$account_group_code]);

			$total_records		= $this->dashboard_model->get_payment_soa_based_list($this->scope_details['having'], $core_task_params);
			$records_info 		= $this->dashboard_model->get_payment_soa_based_list($this->scope_details['having'], $core_task_params, $params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['created_date'],
						$record['business_center_name'],
						$record['vendor_name'],
						$record['soa_upload_date'],
						$record['soa_period'],
						(ISSET($params['transaction_folder']) AND !EMPTY($params['transaction_folder']))? $this->generate_transaction_link($params['transaction_folder'], $params['transaction_tab'], $record['soa_number']): $record['soa_number'],
						$record['apv_number'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['check_number'],
						$record['check_amount'],
						$record['apv_particular'],
						$record['apv_status'],
						"&nbsp;"
				);
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function generate_param_w_vals($param_array=array(0))
	{
		try
		{
			$param_w_vals	= array();

			$q_marks		= "";
			$q_values		= array();

			if(is_array($param_array) AND COUNT($param_array) > 0)
			{
				foreach($param_array AS $key => $param_val)
				{
					$q_marks	.= (!EMPTY($q_marks))? ", ?": "?";
					$q_values[]	= $param_val;
				}

				$param_w_vals['q_marks']	= $q_marks;
				$param_w_vals['q_values']	= $q_values;
			}

			return $param_w_vals;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function generate_transaction_link($transaction_folder, $transaction_tab, $reference_number)
	{
		try
		{
			return "<a href='" . base_url() . PORTAL_TRANSACTIONS . "/" . $transaction_folder . "?keyword=" . $reference_number . "#tab_" . $transaction_tab . "'>" . $reference_number . "</a>";
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function remove_reminder()
	{
		try{

			$status = ERROR;
			$params = get_params();
			
			Portal_Model::beginTransaction();
			
			$fields = array(
				'read_date' => date("Y-m-d H:i:s")
			);

			$where 	= array(
				'notification_id' => $params['rem_id']
			);

			$this->dashboard_model->update_reminder($fields, $where);

			Portal_Model::commit();

			$status = SUCCESS;
			$msg 	= $this->lang->line('notif_done');
		}
		catch(PDOException $e)
		{
			Portal_Model::rollback();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			Portal_Model::rollback();
			$msg = $this->rlog_error($e, TRUE);
		}

		$info = array(
				"status"			=> $status,
				"msg"				=> $msg
		);

		echo json_encode($info);
	}
}