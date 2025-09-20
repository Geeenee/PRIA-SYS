<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apv extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module_code		= MODULE_PORTAL_IMPORT_REFERENCE_APV;
		$this->module_folder	= PORTAL_IMPORT_REFERENCE;
		$this->controller		= strtolower(__CLASS__);

		$this->module_js		= HMVC_FOLDER . '/' . SYSTEM_PORTAL . '/' . $this->module_folder . '/' . $this->controller;
		
		$this->load->model('import_reference_model');
	}

	public function index($encoded_module_code, $encoded_tab_module, $type = NULL, $reference_num = NULL)
	{
		try
		{
			$data = $resources = $buttons = array();

			$common_resource          		= $this->get_common_resources($this->module_code);

			$resources['load_css']    		= array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER));
			$resources['load_js']     		= array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js));

			$resources['loaded_init']		= array_merge($common_resource['init'], array("materialize_select_init();"));

			$resources['datatable']			= array(
					'path'					=> $this->module_folder .'/tabs/'. $this->controller .'/get_payments',
					'table_id'				=> 'tbl_apvs',
					'advanced_filter'		=> TRUE,
					'order' 				=> 4,
					'sort_order' 			=> 'DESC'
			);

			if($type !== NULL AND !EMPTY($type) AND $reference_num !== NULL AND !EMPTY($reference_num))
			{
				$resources['datatable']['func_callback']	= "Apv.init(`$type`, `$reference_num`);";
			}

			$data['param_apv_status']		= $this->import_reference_model->get_all_apv_status(array(), array('*'), array('apv_status_name' => 'ASC'));

			$list['list']['list'] 			= array();
			$list['list']['with_datatable'] = TRUE;

			$data							=  array_merge($data, $list, array(
					'footer'				=> NULL,
					'resources'				=> $resources,
					'actions'				=> array(
							'imports'		=> [],
							'buttons'		=> $buttons,
							'hide_filter'	=> TRUE
					)
			));

			$this->_load_transaction_list($data);
			$this->load->view('tabs/apv', $data);
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);
			$this->error_index($msg);
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);	
			$this->error_index($msg);
		}
	}

	public function get_payments()
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$wheres				= array();
			$params				= get_params();

			$total_records		= $this->import_reference_model->get_payments_list();
			$records_info 		= $this->import_reference_model->get_payments_list($params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$actions		= "";
				
				$table_data[]	= array(
						$record['account_group_code'],
						$record['vendor_name'],
						$record['reference_num'],
						$record['apv_num'],
						$record['apv_date'],
						$record['apv_amount'],
						$record['cv_num'],
						$record['cv_amount'],
						$record['particulars'],
						$record['apv_status_name'],
						""
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
}