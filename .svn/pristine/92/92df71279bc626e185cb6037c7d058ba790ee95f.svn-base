<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Soa extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module_code		= MODULE_PORTAL_IMPORT_REFERENCE_SOA;
		$this->module_folder	= PORTAL_IMPORT_REFERENCE;
		$this->controller		= strtolower(__CLASS__);
		
		$this->load->model('import_reference_model');
	}

	public function index()
	{
		try
		{
			$data = $resources = $buttons = array();

			$common_resource          		= $this->get_common_resources($this->module_code);

			$resources['load_css']    		= array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER));
			$resources['load_js']     		= array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER));

			$resources['loaded_init']		= array_merge($common_resource['init'], array("materialize_select_init();"));

			$resources['datatable']			= array(
					'path'					=> $this->module_folder .'/tabs/'. $this->controller .'/get_soas',
					'table_id'				=> 'tbl_soa',
					'advanced_filter'		=> TRUE
			);

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
			$this->load->view('tabs/soa', $data);
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

	public function get_soas()
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$wheres				= array();
			$params				= get_params();

			$total_records		= $this->import_reference_model->get_soas_list();
			$records_info 		= $this->import_reference_model->get_soas_list($params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				$encoded_id		= base64_url_encode($record['pria_task_id']);

				$actions		= "<div class='table-actions'>";

				$file_link		= base_url() . PATH_UPLOADED_FILES . $record['sys_file_name'];
				$task_link		= base_url() . PORTAL_TRANSACTIONS . '/' . $record['task_controller'] . '/?t=' . $encoded_id;

	

				if(ISSET($record['pria_task_id']) AND !EMPTY($record['pria_task_id']))
				{
					$actions		.=<<<EOS
						<a href='$task_link' class='tooltipped' data-tooltip='View Task' data-position='bottom' data-delay='50' target='_blank'>
							<i class='material-icons'>search</i>
						</a>
EOS;
				}


				if(ISSET($record['sys_file_name']) AND !EMPTY($record['sys_file_name']))
				{
					$actions		.=<<<EOS
						<a href='$file_link' class='tooltipped' data-tooltip='View File' data-position='bottom' data-delay='50' target='_blank'>
							<i class='material-icons'>attach_file</i>
						</a>
EOS;
				}

				$actions		.= "</div>";
				
				$table_data[]	= array(
						$record['account_group_code'],
						$record['soa_num'],
						$record['date_from'],
						$record['date_to'],
						$record['vendor'],
						$actions
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