<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aging_report extends Portal_Controller 
{
  private $module;
  private $module_folder;
  private $controller;
  private $module_js;

  private $permission_view;
  private $permission_add;
  private $permission_edit;
  private $permission_delete;

  protected $model_name = 'payment_report_model';
  
  public function __construct()
  {
    parent::__construct();

    $this->module        = MODULE_PORTAL_REPORT_AGING_REPORT;
    $this->module_folder = MODULE_PORTAL_REPORTS;
    $this->controller    = strtolower(__CLASS__);
    
    $this->load->model(PORTAL_REPORTS.'/Aging_report_model', 'aging_report_model');
    $this->load->model(PORTAL_TRANSACTIONS.'/'.FOLDER_SOA.'/Soa_model', 'soa_model');

    try{
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

    $this->ag_dues  = array(
        AG_CONTRACT_GROWERS   => REPORT_CG_DUE_DATE,
        AG_GOODS_BAVI         => REPORT_GBV_DUE_DATE,
        AG_GOODS_BFFI         => REPORT_GBF_DUE_DATE,
        AG_GOODS_MARINADES    => REPORT_MAR_DUE_DATE,
        AG_TOLL_PARTNERS      => REPORT_TP_DUE_DATE,
        AG_CONTRACTORS        => REPORT_CON_DUE_DATE,
        AG_FORWARDERS         => REPORT_FOR_DUE_DATE,
        AG_INBOUND_CENTRAL    => REPORT_INC_DUE_DATE,
        AG_INBOUND_NORMAL     => REPORT_INN_DUE_DATE,
        AG_OUTBOUND           => REPORT_OUT_DUE_DATE,
        AG_FEEDMILL           => REPORT_FEED_DUE_DATE,
        AG_LESSORS            => REPORT_LESS_DUE_DATE,
        AG_MANPOWER           => REPORT_MAN_DUE_DATE,
        AG_SOA_BASED          => REPORT_SOA_DUE_DATE
    );

  }

  public function index()
  {
    try
    {
      	$data       	= array();
      	$resources    = array();
        $counter      = 0;
        
      	$common_resource			= $this->get_common_resources(MODULE_PORTAL_REPORT_AGING_REPORT);

      	$this->module_js			= HMVC_FOLDER."/".SYSTEM_PORTAL."/reports/aging_report";

      	$resources['load_css']		= array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER, CSS_DATATABLE_BUTTONS));
      	$resources['load_js']		= array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js, JS_BUTTON_EXPORT_EXTENSION));
      
      	$table_options				= array(
  				'table_id'			=> 'tbl_aging_report',
  				'path'				=> 'reports/Aging_report/get_aging_report_list',
  				'advanced_filter'	=> TRUE,
				'with_search'		=> TRUE,
        'buttons'       => ['excel', 'colvis'],
        'export_title'      => "Aging Report",
        'export_file_name'  => "Aging Report " . date('Y-m-d-H-i-s')
    	);

        //Account Groups
        $where					= array('dashboard_flag' => YES_FLAG);
        $data['account_groups']	= $this->aging_report_model->get_all_account_groups($where);

        //Business Centers
        $data['organizations']	= $this->get_scoped_org_by_ag($this->module, NULL, NULL, ORG_TYPE_BUSINESS_CENTER);

        //APV Status
        $data['apv_statuses']	= $this->aging_report_model->get_all_apv_status();

        $scope_details			= get_scope_details($this->module);
        $data['counter']   = $this->aging_report_model->get_total_delayed_apvs($scope_details);

	  	  $resources['datatable']   = $table_options;
	  	  $options_encoded          = json_encode($table_options);
	  	  $resources['loaded_init'] = array_merge($common_resource['init'],
          array(
            "Aging_report.initialize('".$options_encoded."')"
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

    $this->template->load('aging_report', $data, $resources, Portal_Controller::$system);
  }

  public function get_aging_report_list($account_group = NULL, $business_center = NULL, $date_from = NULL, $date_to = NULL, $apv_status = NULL)
	{
		$flag 		  = $total_records = $display_records = 0;
		$table_data = array();
    $now        = time();

		try
		{
			$params	    = get_params();
      $searches   = array();

      $data['organizations']  = get_organizations_by_org_type_w_scope(MODULE_PORTAL_REPORT_AGING_REPORT);
      $organizations          = $data['organizations'];
      $scope_details          = get_scope_details(MODULE_PORTAL_REPORT_AGING_REPORT);

      //Account Group
      if(!EMPTY($account_group) AND $account_group != SELECT_ALL)
        $searches['account_group'] = $account_group;

      //Business Center
      if(!EMPTY($business_center) AND $business_center != SELECT_ALL)
        $searches['business_center'] = $business_center;

      //Date from
      if(!EMPTY($date_from))
        $searches['date_from'] = date(FORMAT_DB_DATE,strtotime(str_replace('-', '/', $date_from)));

      //Date to
      if(!EMPTY($date_to))
        $searches['date_to'] = date(FORMAT_DB_DATE,strtotime(str_replace('-', '/', $date_to)));

      //APV status
      if(!EMPTY($apv_status) AND $apv_status != SELECT_ALL)
        $searches['apv_status'] = $apv_status;

			$total_records		= $this->aging_report_model->get_aging_report_list($searches, $scope_details, NULL);
			$records_info 		= $this->aging_report_model->get_aging_report_list($searches, $scope_details, $params);

			$records          = $records_info['records'];
      $display_records  = $records_info['display_records'];

			foreach($records as $key => $records)
			{
				$table_data[] = array(
          $records["account_group_name"],
          $records["org_name"],
          $records["vendor_name"],
          $records["reference_num"],
          $records["reference_date"],
          $records["apv_num"],
          $records["apv_date"],
          $records["cv_num"],
          $records["due_date"],
          $records["days_delayed_convert"],
          $records["apv_status_name"],
          $records["timeliness"],
          ''
				);
			}

			$flag	= 1;
			$msg	= "";
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
				'aaData'				        => $table_data,
				'sEcho'					        => intval($params['sEcho']),
				'iTotalRecords'			    => $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					        => $flag,
				'msg'					          => $msg
				)
			);
	}

	public function get_business_centers()
	{
		try
		{ 
			$flag         = ERROR;
			$params       = get_params();

			$account_group   = (ISSET($params['account_group']))? $params['account_group']: NULL;

			if($account_group)
			{
        $account_group    = ($account_group == SELECT_ALL)? NULL: $account_group;
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
			'business_centers'	=> $business_centers
		]);
	}
}