<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quick_add extends Task_Controller
{
  	protected $controller;
	protected $module;
  	protected $module_folder;
  	protected $module_js;

  	protected $soa_day_from;
  	protected $soa_day_to;

  	protected $soa_file_extensions;
  	protected $po_file_extensions;

	protected $model_name = 'quick_add_model';

	const SUFFIX_FILE_EXT = '|';

	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('Excel_parser');
		$this->load->library('Pria_workflow');
		$this->load->library('Pria_overview');

		$this->controller 		= strtolower(__CLASS__);
		$this->module        	= MODULE_PORTAL_QUICK_ADD;
		$this->module_folder 	= PORTAL_COMMON;
		$this->module_js 		= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_QUICK_ADD."/".$this->controller;

		$this->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model'); 

		$this->load->model($this->module_folder. '/'.$this->model_name, $this->model_name);
		$this->task				= modules::load(PORTAL_TRANSACTIONS . '/Task');

		$soa_day_from_details	= get_sys_param_val(SYS_PARAM_SOA_PERIOD_DAY, SYS_PARAM_SOA_PERIOD_DAY_FROM);
		$soa_day_to_details		= get_sys_param_val(SYS_PARAM_SOA_PERIOD_DAY, SYS_PARAM_SOA_PERIOD_DAY_TO);

		$this->soa_day_from		= (ISSET($soa_day_from_details['sys_param_value']))? $soa_day_from_details['sys_param_value']: DEFAULT_SOA_PERIOD_DAY_FROM;
		$this->soa_day_to		= (ISSET($soa_day_to_details['sys_param_value']))? $soa_day_to_details['sys_param_value']: DEFAULT_SOA_PERIOD_DAY_TO;

		$this->exempt_scope_list	= array();
		$this->exempt_scope_batch	= array();

		$soa_extension_details		= $this->quick_add_model->get_record_details(array('allowed_extensions'), Portal_Model::PORTAL_TABLE_PARAM_DOCUMENT_TYPES, FALSE, array('document_type_code' => DOC_TYPE_SOA));
		$po_extension_details		= $this->quick_add_model->get_record_details(array('allowed_extensions'), Portal_Model::PORTAL_TABLE_PARAM_DOCUMENT_TYPES, FALSE, array('document_type_code' => DOC_TYPE_PO));

		$this->soa_file_extensions	= (ISSET($soa_extension_details['allowed_extensions']) AND !EMPTY($soa_extension_details['allowed_extensions']))? $soa_extension_details['allowed_extensions']: NULL;
		$this->po_file_extensions	= (ISSET($po_extension_details['allowed_extensions']) AND !EMPTY($po_extension_details['allowed_extensions']))? $po_extension_details['allowed_extensions']: NULL;
	}

	public function modal_quick_add($quick_add_val = NULL)
	{
		try 
		{

			$data = $resources = array();

			$multiple_flag  = FALSE;
			$allowed_types	= IMPORT_FILE_EXTENSION;

			$resources['load_css']		= array(CSS_UPLOAD);
			$resources['load_js']		= array(JS_UPLOAD);

			$resources['loaded_init']	= array();

			$upload_arr = json_encode(
					array(
							'id' 		=> 'file_import',
							"path" 		=> PATH_QA_IMPORT_FILE
					)
			);

			$upload_delete_arr = json_encode(
					array(
							'id' 		=> 'file_import'
					)
			);

			switch ($quick_add_val)
			{
				case PORTAL_TMP_QA_IO:
					
					$data['label_name'] = 'IO list';
					$data['file_name'] 	= TEMPLATE_QA_IO_LIST;
					$modal 				= "modals/import_file";
					break;
				
				case PORTAL_TMP_QA_PR:

					$data['label_name'] = 'PR list';
					$data['file_name'] 	= TEMPLATE_QA_PR_LIST;
					$modal 				= "modals/import_file";
					break;
				
				case PORTAL_TMP_QA_PO:

					$data['label_name'] = 'PO list';
					$data['file_name'] 	= TEMPLATE_QA_PO_LIST;
					$modal 				= "modals/import_file";
					break;
				
				case PORTAL_TMP_QA_PO_BATCH:

					$data['label_name'] = 'PO Batch list';
					$data['file_name'] 	= TEMPLATE_QA_PO_BATCH;
					$data['format'] 	= '[AG CODE]__[PR NO]__[PO NO]__[VENDOR CODE]__[BUSINESS CENTER CODE].pdf';
					$data['example']	= 'Example: <b>GBV__PR0001__PO0001__V001__BC0001.pdf</b>';
					$data['multiple_pr']	= '<span class="red-text">Multiple PR should be separated by ";".</span><br/>Example: <b>GBV__PR0001;PR0002__PO0001__V001__BC0001.pdf</b>';
					$modal 				= "modals/import_batch";
					$multiple_flag 		= TRUE;
					$allowed_types		= $this->po_file_extensions;
					break;
				
				case PORTAL_TMP_QA_SOA:

					$data['label_name'] = 'SOA list';
					$data['file_name'] 	= TEMPLATE_QA_SOA_LIST;
					$modal 				= "modals/import_file";
					break;
				
				case PORTAL_TMP_QA_SOA_BATCH:

					$data['label_name'] = 'SOA Batch list';
					$data['file_name'] 	= TEMPLATE_QA_SOA_BATCH;
					$data['format'] 	= '[AG CODE]__[SOA NO]__[VENDOR CODE]__[BUSINESS CENTER CODE].pdf';
					$data['example']	= 'Example: <b>IBC__SOA0001__V001__BC0001.pdf</b>';
					$modal 				= "modals/import_batch";
					$multiple_flag 		= TRUE;
					$allowed_types		= $this->soa_file_extensions;
					break;
				
				case PORTAL_TMP_QA_DR:

					$data['label_name'] = 'DR list';
					$data['file_name'] 	= TEMPLATE_QA_DR_LIST;
					$modal 				= "modals/import_file";
					break;
				
				case PORTAL_TMP_QA_GR:

					$data['label_name'] = 'GR list';
					$data['file_name'] 	= TEMPLATE_QA_GR_LIST;
					$modal 				= "modals/import_file";
					break;

				case PORTAL_TMP_QA_APV:
				
					$data['label_name'] = 'APV list';
					$data['file_name'] 	= TEMPLATE_QA_APV_LIST;
					$modal 				= "modals/import_file";
					break;
				
				default:
					throw new Exception($this->lang->line('err_unauthorized_access'));
					break;
			}

			$data['allowed_extensions']			= explode(',', $allowed_types);

			$resources['upload']				= array(
					'attachments'				=> array(
							'path' 				=> PATH_QA_IMPORT_FILE,
							'allowed_types' 	=> $allowed_types,
							'multiple' 			=> $multiple_flag,
							'max_file'			=> 999,
							'drag_drop' 		=> TRUE,
							'show_preview' 		=> TRUE,
							'successCallback' 	=> 'Quick_add.successCallBack(files, data, '.$upload_arr.','.$multiple_flag.');',
							'deleteCallback' 	=> 'Quick_add.deleteCallBack(data,'.$upload_delete_arr.');'
					)
			);
		}
		catch (PDOException $e)
		{
			$msg  = $this->get_user_message($e);

     		$this->error_modal( $msg );
		} 
		
		catch (Exception $e) 
		{
			 $msg  = $this->get_user_message($e);

     		 $this->error_modal( $msg );
		}

		$this->load->view($modal, $data);
		$this->load_resources->get_resource($resources);
	}

	/** 
	 * @Author: kevin villarojo 
	 * @Date: 2019-12-06 11:55:52 
	 * @Desc: Used as callback parameter of array_map in this class. 
	 */	
	public function add_suffix_ext($ext_name)
	{
		return $ext_name.self::SUFFIX_FILE_EXT;
	}

	public function import_quick_add($module_code = NULL)
	{
		try
		{
			$params 				= get_params();

			$flag 					= 0;
			$status 				= ERROR;
			$msg 					= '';
			$data 					= '';
			$table_name 			= NULL;
			$temp_ids 				= NULL;
			$batch_flag 			= FALSE;
			$multiple_import_flag	= FALSE;
			$import_type			= ISSET($params['tmp_file']) ? $params['tmp_file'] : '';
			$batch_value			= ISSET($params['batch_value']) ? $params['batch_value'] : '';
			$module_code			= (!EMPTY($module_code))? base64_url_decode($module_code): NULL;
			
			$values					= array(0);
			
			$err_msg				= "";
			$err_err = $err_warning = 0;
			
    		Portal_Model::beginTransaction();
			
			if($params['multiple_file_name'])
			{
				//For Batch uploading
				if(is_array($params['multiple_file_name']))
				{ 
					$multiple_import_flag	= TRUE;
					$batch_flag 			= TRUE;

					$qa_module_code			= NULL;

					//validate if file is in valid format
					//$params['multiple_file_name']

					if($batch_value	== TEMPLATE_QA_PO_BATCH)
					{
						$qa_module_code		= MODULE_PORTAL_QA_PO_BATCH;
					}
					else if($batch_value == TEMPLATE_QA_SOA_BATCH)
					{
						$qa_module_code		= MODULE_PORTAL_QA_SOA_BATCH;
					}

					$scope_details			= get_scope_details($qa_module_code);

					//loop for files uploaded
					foreach ($params['multiple_file_name'] as $key => $multiple_file_name)
					{
						//original file name
						$orig_file_name	= $params['file_orig'][$key];

						//system file name
						$sys_file_name	= $multiple_file_name;

						$fields			= array();
						$curr_err_msg	= NULL;
						$temp_reference_id	= NULL;

						if($batch_value == TEMPLATE_QA_PO_BATCH)
						{
							//$exploded_file		= explode("__", str_replace(preg_filter('/^/', '.', explode(',', $this->po_file_extensions)), '', $orig_file_name));

							$po_exts 			= array_map([$this, 'add_suffix_ext'], preg_filter('/^/', '.', explode(',', $this->po_file_extensions)));
							$exploded_file		= explode("__", str_replace($po_exts, '', $orig_file_name.self::SUFFIX_FILE_EXT));
						
							$ex_ag_code 		= (ISSET($exploded_file[0]))? $exploded_file[0]: NULL;
							$ex_pr_no 			= (ISSET($exploded_file[1]))? $exploded_file[1]: NULL;
							$ex_po_no 			= (ISSET($exploded_file[2]))? $exploded_file[2]: NULL;
							$ex_vendor 			= (ISSET($exploded_file[3]))? $exploded_file[3]: NULL;
							$ex_bc	 			= (ISSET($exploded_file[4]))? $exploded_file[4]: NULL;
							$ex_boq_no 			= (ISSET($exploded_file[5]))? $exploded_file[5]: NULL;

							$field_length_details	= $this->_validate_field_length('account_group_code', $ex_ag_code);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_ag_code			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'AG Code', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'AG Code', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('pr_num', $ex_pr_no);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_pr_no			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'PR Number', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'PR Number', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('po_num', $ex_po_no);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_po_no			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'PO Number', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'PO Number', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('vendor_code', $ex_vendor);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_vendor			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'Vendor Code', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'Vendor Code', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('org_code', $ex_bc);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_bc				= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'Business Center Code', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'Business Center Code', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$db_table_name 		= Portal_Model::PORTAL_TABLE_TEMP_POS;
							$actual_table_name  = Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
							$table_name			= PORTAL_TMP_QA_PO_BATCH;

							$validate_params	= array(
									'account_group_code'	=> $ex_ag_code,
									'org_code'				=> $ex_bc,
									'vendor_code'			=> $ex_vendor,
									'reference_num'			=> $ex_po_no,
									'file_name'				=> $orig_file_name,
									'pr_num'				=> $ex_pr_no,
									'boq_num'				=> $ex_boq_no
							);
							
							$fields['po_num']				= $ex_po_no;
							$fields['pr_num']				= $ex_pr_no;
							$fields['boq_num']				= $ex_boq_no;

							$temp_where						= array(
									'account_group_code'	=> $ex_ag_code,
									'org_code'				=> $ex_bc,
									'vendor_code'			=> $ex_vendor,
									'po_num'				=> $ex_po_no,
									'pr_num'				=> $ex_pr_no
							);

							$temp_exist_details				= $this->quick_add_model->get_record_details(array('temp_po_id'), Portal_Model::PORTAL_TABLE_TEMP_POS, FALSE, $temp_where);

							if(ISSET($temp_exist_details['temp_po_id']) AND !EMPTY($temp_exist_details['temp_po_id']))
							{
								$temp_reference_id			= $temp_exist_details['temp_po_id'];
							}
						}
						elseif($batch_value == TEMPLATE_QA_SOA_BATCH)
						{
							$soa_exts 			= array_map([$this, 'add_suffix_ext'], preg_filter('/^/', '.', explode(',', $this->soa_file_extensions)));
							$exploded_file		= explode("__", str_replace($soa_exts, '', $orig_file_name.self::SUFFIX_FILE_EXT));

							$ex_ag_code 		= (ISSET($exploded_file[0]))? $exploded_file[0]: NULL;
							$ex_soa_no 			= (ISSET($exploded_file[1]))? $exploded_file[1]: NULL;
							$ex_vendor 			= (ISSET($exploded_file[2]))? $exploded_file[2]: NULL;
							$ex_bc 				= (ISSET($exploded_file[3]))? $exploded_file[3]: NULL;

							$field_length_details	= $this->_validate_field_length('account_group_code', $ex_ag_code);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_ag_code			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'AG Code', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'AG Code', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('temp_soa_no', $ex_soa_no);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_soa_no			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'SOA Number', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'SOA Number', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('vendor_code', $ex_vendor);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_vendor			= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'Vendor Code', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'Vendor Code', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							$field_length_details	= $this->_validate_field_length('org_code', $ex_bc);

							if(ISSET($field_length_details['too_long']) AND $field_length_details['too_long'] == ENUM_YES)
							{
								$ex_bc				= $field_length_details['value'];

								$curr_err_msg		.= sprintf($this->lang->line('err_too_long'), 'Business Center Code', $field_length_details['max_length']) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), 'Business Center Code', $field_length_details['max_length'])) . " on file " . $orig_file_name . ".\r\n";
								$err_warning++;
							}

							//Get max batch number
							$db_table_name 		= Portal_Model::PORTAL_TABLE_TEMP_SOAS;
							$actual_table_name  = Portal_Model::PORTAL_TABLE_SOA;
							$table_name			= PORTAL_TMP_QA_SOA_BATCH;

							$validate_params	= array(
									'account_group_code'	=> $ex_ag_code,
									'org_code'				=> $ex_bc,
									'vendor_code'			=> $ex_vendor,
									'reference_num'			=> $ex_soa_no,
									'file_name'				=> $orig_file_name
							);

							$fields['temp_soa_no']			= $ex_soa_no;

							$temp_where						= array(
									'account_group_code'	=> $ex_ag_code,
									'org_code'				=> $ex_bc,
									'vendor_code'			=> $ex_vendor,
									'temp_soa_no'			=> $ex_soa_no
							);

							$temp_exist_details				= $this->quick_add_model->get_record_details(array('temp_soa_id'), Portal_Model::PORTAL_TABLE_TEMP_SOAS, FALSE, $temp_where);

							if(ISSET($temp_exist_details['temp_soa_id']) AND !EMPTY($temp_exist_details['temp_soa_id']))
							{
								$temp_reference_id			= $temp_exist_details['temp_soa_id'];
							}
						}
						else
						{
							throw new Exception($this->lang->line('err_valid_batch_upload'));
						}

						if(!in_array($batch_value, $this->exempt_scope_batch))
						{
							$valid_scope		= $this->_validate_scope($scope_details, $validate_params['org_code'], $validate_params['vendor_code']);

							if(!$valid_scope)
							{
								if($scope_details['scope'] == SCOPE_REGION AND ISSET($validate_params['org_code']))
								{
									$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Business Center Code', 'user account scope') . "<br/>";
									$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Business Center Code', 'user account scope')) . " on row " . $line_no . ".\r\n";
									$err_warning++;
								}
								else if($scope_details['scope'] == SCOPE_AGENCY AND ISSET($validate_params['vendor_code']))
								{
									$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'user account scope') . "<br/>";
									$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'user account scope')) . " on row " . $line_no . ".\r\n";
									$err_warning++;
								}
							}
						}

						$curr_err_msg						.= $this->_validate_batch_upload($batch_value, $qa_module_code, $validate_params, $err_msg, $err_warning, $err_err);

						$mx_temp_batch_no 					= $this->quick_add_model->get_max_batch($db_table_name);
						$mx_batch_no 						= EMPTY($mx_temp_batch_no) ? 1 : $mx_temp_batch_no['max_val'] + 1;

						$fields['account_group_code'] 		= $ex_ag_code;
						$fields['batch_num']				= $mx_batch_no;
						$fields['vendor_code']				= $ex_vendor;
						$fields['org_code']					= $ex_bc;
						$fields['file_name'] 				= $orig_file_name;
						$fields['sys_file_name']			= $sys_file_name;
						$fields['created_by']				= $this->session->user_id;
						$fields['created_date']				= date('Y-m-d H:i:s');
						$fields['error_msg']				= $curr_err_msg;
						$fields['temp_reference_id']		= $temp_reference_id;
						
						$temp_id 	= $this->quick_add_model->insert_temp_data($db_table_name, $fields);
						$values[] 	= $temp_id;
					}
				}
				else
				{
					$file 		= PATH_UPLOADS."portal/imported_files/".$params['multiple_file_name'];
					$data 		= $this->_read_excel($file, $import_type);
					
					$line_no	= 2;

					if(EMPTY($data['body']))
					{
						throw new Exception($this->lang->line('err_import_empty'));
					}
					else
					{
						$temp_field_key					= NULL;

						//Compare Sheet Name
						switch($data['table'])
						{
							case PORTAL_SHEET_QA_IO:

								if($import_type != TEMPLATE_QA_IO_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_IOS;
								$table_fields 		= array('account_group_code', 'org_code', 'vendor_code', 'site_code', 'cycle_num', 'temp_io_num');
								$required_fields 	= array('account_group_code', 'org_code', 'vendor_code', 'site_code', 'cycle_num', 'temp_io_num');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
								$temp_field_key   	= 'temp_io_num';
								$actual_field_key   = 'io_num';
								$tab_module 		= MODULE_PORTAL_QA_IO;
								break;

							case PORTAL_SHEET_QA_PR:

								if($import_type != TEMPLATE_QA_PR_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_PRS;
								$table_fields 		= array('account_group_code', 'purchasing_group_code', 'temp_pr_num', 'pr_item_type', 'cost_center_code', 'gl_account_code', 'requestor');
								$required_fields 	= array('account_group_code', 'purchasing_group_code', 'temp_pr_num', 'requestor');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
								$temp_field_key		= 'temp_pr_num';
								$actual_field_key   = 'pr_num';
								$tab_module 		= MODULE_PORTAL_QA_PR;
								break;

							case PORTAL_SHEET_QA_PO:

								if($import_type != TEMPLATE_QA_PO_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_POS;
								$table_fields 		= array('account_group_code', 'org_code', 'vendor_code'/*, 'boq_num'*/, 'pr_num', 'po_num', 'po_date', 'amount', 'released_date');
								$required_fields 	= array('account_group_code', 'org_code', 'vendor_code', 'pr_num', 'po_num', 'released_date');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
								$temp_field_key		= 'po_num';
								$actual_field_key   = 'po_num';
								$tab_module 		= MODULE_PORTAL_QA_PO;
								break;

							case PORTAL_SHEET_QA_SOA:

								if($import_type != TEMPLATE_QA_SOA_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_SOAS;
								$table_fields 		= array('account_group_code', 'org_code', 'vendor_code', 'temp_soa_no', 'soa_date', 'soa_amount', 'date_from', 'date_to');
								$required_fields 	= array('account_group_code', 'org_code', 'vendor_code', 'temp_soa_no', 'soa_date', 'soa_amount', 'date_from', 'date_to');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_SOA;
								$temp_field_key		= 'temp_soa_no';
								$actual_field_key   = 'soa_num';
								$tab_module 		= MODULE_PORTAL_QA_SOA;
								break;

							case PORTAL_SHEET_QA_DR:

								if($import_type != TEMPLATE_QA_DR_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_DRS;
								$table_fields 		= array('account_group_code', 'org_code', 'vendor_code', 'temp_dr_no', 'dr_date', 'cost_center', 'remarks');
								$required_fields 	= array('account_group_code', 'org_code', 'vendor_code', 'temp_dr_no', 'cost_center');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
								$temp_field_key		= 'temp_dr_no';
								$actual_field_key   = 'dr_num';
								$tab_module 		= MODULE_PORTAL_QA_DR;
								break;

							case PORTAL_SHEET_QA_GR:

								if($import_type != TEMPLATE_QA_GR_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_GRS;
								$table_fields 		= array('account_group_code', 'org_code', 'vendor_code', 'dr_number', 'temp_gr_no', 'gr_date');
								$required_fields 	= array('account_group_code', 'org_code', 'vendor_code', 'dr_number', 'temp_gr_no');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
								$temp_field_key		= 'temp_gr_no';
								$actual_field_key   = 'gr_num';
								$tab_module 		= MODULE_PORTAL_QA_GR;

								break;
							
							case PORTAL_SHEET_QA_APV:

								if($import_type != TEMPLATE_QA_APV_LIST)
								{
									throw new Exception($this->lang->line('err_mismatch_sheet'));
								}

								$table_name 		= Portal_Model::PORTAL_TABLE_TEMP_APVS;
								$table_fields 		= array('account_group_code', 'org_code', 'vendor_code', 'reference_num', 'apv_num', 'apv_date', 'apv_amount', 'cv_num', 'cv_amount', 'particulars', 'apv_status_code');
								$required_fields 	= array('account_group_code', 'org_code', 'vendor_code', 'reference_num', 'apv_status_code');
								$actual_table_name 	= Portal_Model::PORTAL_TABLE_PAYMENTS;
								$actual_field_key   = 'apv_num';
								$tab_module 		= MODULE_PORTAL_QA_APV;
								break;
							
							default:
								throw new Exception($this->lang->line('err_mismatch_sheet'));
								break;
						}

						$scope_details				= get_scope_details($tab_module);

						foreach($data['body'] as $key => $body)
						{
							$fields						= array();

							$curr_err_msg				= NULL;
							$temp_reference_id			= NULL;

							if(COUNT($body) != COUNT($table_fields))
							{
								throw new Exception($this->lang->line('err_incomplete_fields'));
							}

							//print_var_export($body); die;

							foreach ($body as $key2 => $value)
							{
								if($data['table'] == PORTAL_SHEET_QA_PO AND $table_fields[$key2] == "pr_num")
								{
									$pr_nums	= explode(',', $value);
									
									foreach($pr_nums AS $pr_key => $pr_num)
									{
										$field_length_details			= $this->_validate_field_length($table_fields[$key2], $pr_num);

										if($field_length_details['too_short'] == ENUM_YES)
										{
											$value						= $field_length_details['value'];

											$curr_err_msg				.= sprintf($this->lang->line('err_too_short'), $data['header'][$key2], $field_length_details['min_length']) . "<br/>";
											$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_short')), $data['header'][$key2], $field_length_details['min_length'])) . " on row " . $line_no . ".\r\n";
											$err_warning++;
										}

										if($field_length_details['too_long'] == ENUM_YES)
										{
											$value						= $field_length_details['value'];

											$curr_err_msg				.= sprintf($this->lang->line('err_too_long'), $data['header'][$key2], $field_length_details['max_length']) . "<br/>";
											$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), $data['header'][$key2], $field_length_details['max_length'])) . " on row " . $line_no . ".\r\n";
											$err_warning++;
										}
									}
								}
								else
								{
									$field_length_details			= $this->_validate_field_length($table_fields[$key2], $value);
	
									if($field_length_details['too_short'] == ENUM_YES)
									{
										$value						= $field_length_details['value'];
	
										$curr_err_msg				.= sprintf($this->lang->line('err_too_short'), $data['header'][$key2], $field_length_details['min_length']) . "<br/>";
										$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_short')), $data['header'][$key2], $field_length_details['min_length'])) . " on row " . $line_no . ".\r\n";
										$err_warning++;
									}
	
									if($field_length_details['too_long'] == ENUM_YES)
									{
										$value						= $field_length_details['value'];
	
										$curr_err_msg				.= sprintf($this->lang->line('err_too_long'), $data['header'][$key2], $field_length_details['max_length']) . "<br/>";
										$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('err_too_long')), $data['header'][$key2], $field_length_details['max_length'])) . " on row " . $line_no . ".\r\n";
										$err_warning++;
									}
								}

								if(in_array($table_fields[$key2], array('po_date', 'released_date', 'soa_date', 'date_from', 'date_to', 'dr_date', 'gr_date', 'apv_date')))
								{
									if(is_numeric($value) && (int)$value == $value)
									{
										$fields[$table_fields[$key2]]= gmdate("Y-m-d", ($value - 25569) * 86400);
									}
									else
									{
										if(!EMPTY($value))
										{
											$curr_err_msg		.= sprintf($this->lang->line('invalid_value'), $data['header'][$key2]) . "<br/>";
											$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('invalid_value')), $data['header'][$key2])) . " on row " . $line_no . ".\r\n";
											$err_warning++;
										}
									}
								}
								else if(in_array($table_fields[$key2], array('apv_date')))
								{
									if(is_numeric($value) && (int)$value == $value)
									{
										$fields[$table_fields[$key2]]= gmdate("Y-m-d H:i:s", ($value - 25569) * 86400);
									}
									else
									{
										if(!EMPTY($value))
										{
											$curr_err_msg		.= sprintf($this->lang->line('invalid_value'), $data['header'][$key2]) . "<br/>";
											$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('invalid_value')), $data['header'][$key2])) . " on row " . $line_no . ".\r\n";
											$err_warning++;
										}
									}
								}
								else if(in_array($table_fields[$key2], array('cycle_num')))
								{
									if(is_numeric($value) && (int)$value == $value)
									{
										$fields[$table_fields[$key2]]= str_pad($value, 2, "0", STR_PAD_LEFT);
									}
									else
									{
										$fields[$table_fields[$key2]]= (!EMPTY($value))? htmlentities($value): NULL;
									}
								}
								else
								{
									$fields[$table_fields[$key2]]= (!EMPTY($value))? (($table_fields[$key2] != 'reference_num')? htmlentities($value): $value): NULL;
								}

								/*if($table_fields[$key2] == "account_group_code")
								{
									if($data['table'] == PORTAL_SHEET_QA_SOA)
									{
										if($value == AG_FORWARDERS)
										{
											array_diff($required_fields, array('date_from', 'date_to'));
										}
										else
										{
											array_push($required_fields, 'date_from', 'date_to');
										}
									}
								}*/

								if(in_array($table_fields[$key2], $required_fields))
								{
									if(EMPTY($value))
									{
										$curr_err_msg			.= sprintf($this->lang->line('is_required'), $data['header'][$key2]) . "<br/>";
										$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('is_required')), $data['header'][$key2])) . " on row " . $line_no . ".\r\n";
										$err_warning++;
									}
								}
							}

							if($data['table'] == PORTAL_SHEET_QA_APV)
							{
								if(EMPTY($fields['apv_num']) AND EMPTY($fields['cv_num']))
								{
									$curr_err_msg				.= sprintf($this->lang->line('is_required'), "APV NUMBER or CV NUMBER") . "<br/>";
									$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf(str_replace('.', '', $this->lang->line('is_required')), "APV NUMBER or CV NUMBER")) . " on row " . $line_no . ".\r\n";
									$err_warning++;
								}

								$reference_details				= $this->_get_payment_reference_details($fields);
								$reference_type					= $this->_get_reference_type_details($fields['account_group_code']);

								unset($reference_type['trans_only']);

								$fields							= array_merge($fields, $reference_details, $reference_type);
							}

							if(COUNT(array_diff($fields, array(''))) > 0)
							{
								if(!in_array($data['table'], $this->exempt_scope_list))
								{
									$valid_scope	= $this->_validate_scope($scope_details, $fields['org_code'], $fields['vendor_code']);

									if(!$valid_scope)
									{
										if($scope_details['scope'] == SCOPE_REGION AND ISSET($fields['org_code']))
										{
											$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Business Center Code', 'user account scope') . "<br/>";
											$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Business Center Code', 'user account scope')) . " on row " . $line_no . ".\r\n";
											$err_warning++;
										}
										else if($scope_details['scope'] == SCOPE_AGENCY AND ISSET($fields['vendor_code']))
										{
											$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'user account scope') . "<br/>";
											$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'user account scope')) . " on row " . $line_no . ".\r\n";
											$err_warning++;
										}
									}
								}

								if(EMPTY($curr_err_msg))
								{
									$curr_err_msg					= $this->_validate_transactions($import_type, $tab_module, $fields, $line_no, TRUE, $err_msg, $err_warning, $err_err, $temp_field_key, $temp_reference_id, $values);
								}

								$mx_temp_batch_no 					= $this->quick_add_model->get_max_batch($table_name);
								$mx_batch_no 						= EMPTY($mx_temp_batch_no) ? 1 : $mx_temp_batch_no['max_val'] + 1;

								$fields['batch_num']				= $mx_batch_no;
								$fields['created_by']				= $this->session->user_id;
								$fields['created_date']				= date('Y-m-d H:i:s');
								$fields['error_msg']				= $curr_err_msg;
								$fields['temp_reference_id']		= $temp_reference_id;
								
								$temp_id 							= $this->quick_add_model->insert_temp_data($table_name, $fields);
								$values[] 							= $temp_id;

								$line_no++;
							}
						}
					}
				}

				$temp_ids 	= implode('/', $values);
				$status 	= SUCCESS;
				$msg 		= $this->lang->line('temp_success_import');
				$flag 		= 1;

				if($err_err > 0)
				{
					$msg			= ($batch_flag)? $this->lang->line('error_import_batch'): $this->lang->line('error_import');
					//throw new Exception($msg);
				}
				else if($err_warning > 0)
				{
					$status			= WARNING;
					$msg			= ($batch_flag)? $this->lang->line('warning_import_batch'): $this->lang->line('warning_import');
				}

				Portal_Model::commit();
			}
			else
			{
				throw new Exception($this->lang->line('err_import_empty'));
			}
		}
		catch(PDOException $e)
	    {
	    	$status = ERROR;
	    	$flag 	= 0;
	      	$msg  	= $this->get_user_message($e);
	      	Portal_Model::rollback();
	    }
	    catch(Exception $e)
	    {
	    	$status = ERROR;
	    	$flag 	= 0;
	      	$msg    = $this->rlog_error($e, TRUE);
	      	Portal_Model::rollback();
	    }

	    $response = array(
            	'status'    	=> $status,
            	'msg'       	=> $msg,
            	'err_msg'       => $err_msg,
            	"table_name"	=> base64_url_encode($table_name),
            	"batch_flag"	=> $batch_flag,
				'flag'			=> $flag,
				"temp_ids"		=> base64_url_encode($temp_ids),
				'date_time'		=> date('Y-m-d-H-i-s')
        );

	    echo json_encode($response);
	}

	private function _read_excel($file = NULL, $import_type = NULL)
	{
		try
		{
			$params	= get_params();
			$sheet	= NULL;

			switch($import_type)
			{
				case TEMPLATE_QA_IO_LIST:
					$sheet	= PORTAL_SHEET_QA_IO;
				break;
				case TEMPLATE_QA_PR_LIST:
					$sheet	= PORTAL_SHEET_QA_PR;
				break;
				case TEMPLATE_QA_SOA_LIST:
					$sheet	= PORTAL_SHEET_QA_SOA;
				break;
				case TEMPLATE_QA_DR_LIST:
					$sheet	= PORTAL_SHEET_QA_DR;
				break;
				case TEMPLATE_QA_PO_LIST:
					$sheet	= PORTAL_SHEET_QA_PO;
				break;
				case TEMPLATE_QA_GR_LIST:
					$sheet	= PORTAL_SHEET_QA_GR;
				break;
				case TEMPLATE_QA_APV_LIST:
					$sheet	= PORTAL_SHEET_QA_APV;
				break;
			}

			$data 	= $this->excel_parser->parse_file($file, $params, $sheet);

			return $data;
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

	public function modal_generated_file($table_name = NULL, $batch_flag = FALSE)
	{
		try
		{
			$params		= get_params();
			$temp_ids	= $params['ids'];
			
			$data = $resources = $params = array();
			$selected_fields = '';

			$resources['load_css']		= array();
			$resources['load_js']		= array($this->module_js);
			$resources['loaded_init']	= array();

			$decoded_tmp_ids 			= base64_url_decode($temp_ids);
			$decoded_table 				= base64_url_decode($table_name);
			$arr_ids 					= explode("/",$decoded_tmp_ids);

			switch ($decoded_table)
			{
				case PORTAL_TMP_QA_IO:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_IOS;
					$data['primary_field'] 	= 'temp_io_id';
					$data['table_header'] 	= array('AG CODE', 'BUSINESS CENTER CODE', 'VENDOR CODE', 'SLOC', 'CYCLE NUMBER', 'IO NUMBER', 'ERROR/ WARNING');
					$selected_fields 		= ' A.temp_io_id, A.account_group_code, A.org_code, A.vendor_code, A.site_code, A.cycle_num, A.temp_io_num, A.error_msg, A.temp_reference_id';
					break;
				
				case PORTAL_TMP_QA_PR:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_PRS;
					$data['primary_field'] 	= 'temp_pr_id';
					$data['table_header'] 	= array('AG CODE', 'PURCHASING GROUP', 'PR NUMBER', 'ITEM TYPE CODE', 'COST CENTER', 'GL ACCOUNT', 'REQUESTORS', 'ERROR/ WARNING');
					$selected_fields 		= ' A.temp_pr_id, A.account_group_code, A.purchasing_group_code, A.temp_pr_num, A.pr_item_type, A.cost_center_code, A.gl_account_code, A.requestor, A.error_msg, A.temp_reference_id';
					break;
				
				case PORTAL_TMP_QA_PO:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_POS;
					$data['primary_field'] 	= 'temp_po_id';
					$data['table_header'] 	= array('AG CODE', 'BUSINESS CENTER CODE', 'VENDOR CODE', 'PR NUMBER', 'PO NUMBER', 'PO DATE', 'AMOUNT', 'RELEASED DATE', 'ERROR/ WARNING');
					$selected_fields 		= " A.temp_po_id, A.account_group_code, A.org_code, A.vendor_code, A.pr_num, A.po_num, DATE_FORMAT(A.po_date, '%m/%d/%Y'), if(A.amount REGEXP '^[0-9]+\\.?[0-9]*$', FORMAT(A.amount, 2), A.amount), DATE_FORMAT(A.released_date, '%m/%d/%Y'), A.error_msg, A.temp_reference_id";
					break;

				case PORTAL_TMP_QA_PO_BATCH:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_POS;
					$data['primary_field'] 	= 'temp_po_id';
					$data['table_header'] 	= array('AG CODE', 'PR NUMBER', 'PO NUMBER', 'VENDOR CODE', 'BUSINESS CENTER CODE', 'FILE NAME', 'ERROR/ WARNING');
					$selected_fields 		= ' A.temp_po_id, A.account_group_code, A.pr_num, A.po_num, A.vendor_code, A.org_code, A.file_name, A.error_msg, A.temp_reference_id';
					break;

				case PORTAL_TMP_QA_SOA:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_SOAS;
					$data['primary_field'] 	= 'temp_soa_id';
					$data['table_header'] 	= array('AG CODE', 'BUSINESS CENTER CODE', 'VENDOR CODE', 'SOA NUMBER', 'SOA DATE', 'SOA AMOUNT', 'PERIOD COVERED FROM', 'PERIOD COVERED TO', 'ERROR/ WARNING');
					$selected_fields 		= " A.temp_soa_id, A.account_group_code, A.org_code, A.vendor_code, A.temp_soa_no, DATE_FORMAT(A.soa_date, '%m/%d/%Y'), if(A.soa_amount REGEXP '^[0-9]+\\.?[0-9]*$', FORMAT(A.soa_amount, 2), A.soa_amount), DATE_FORMAT(A.date_from, '%m/%d/%Y'), DATE_FORMAT(A.date_to, '%m/%d/%Y'), A.error_msg, A.temp_reference_id";
					break;

				case PORTAL_TMP_QA_SOA_BATCH:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_SOAS;
					$data['primary_field'] 	= 'temp_soa_id';
					$data['table_header'] 	= array('AG CODE', 'SOA NUMBER', 'VENDOR CODE', 'BUSINESS CENTER CODE', 'FILE NAME', 'ERROR/ WARNING');
					$selected_fields 		= ' A.temp_soa_id, A.account_group_code, A.temp_soa_no, A.vendor_code, A.org_code, A.file_name, A.error_msg, A.temp_reference_id';
					break;
				
				case PORTAL_TMP_QA_DR:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_DRS;
					$data['primary_field'] 	= 'temp_dr_id';
					$data['table_header'] 	= array('AG CODE', 'BUSINESS CENTER CODE', 'VENDOR CODE', 'DR NUMBER', 'DR DATE', 'COST CENTER CODE', 'REMARKS', 'ERROR/ WARNING');
					$selected_fields 		= " A.temp_dr_id, A.account_group_code, A.org_code, A.vendor_code, A.temp_dr_no, DATE_FORMAT(A.dr_date, '%m/%d/%Y'), A.cost_center, A.remarks, A.error_msg, A.temp_reference_id";
					break;

				case PORTAL_TMP_QA_GR:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_GRS;
					$data['primary_field'] 	= 'temp_gr_id';
					$data['table_header'] 	= array('AG CODE', 'BUSINESS CENTER CODE', 'VENDOR CODE', 'DR NUMBER', 'GR NUMBER',  'GR DATE', 'ERROR/ WARNING');
					$selected_fields 		= " A.temp_gr_id, A.account_group_code, A.org_code, A.vendor_code, A.dr_number, A.temp_gr_no, DATE_FORMAT(A.gr_date, '%m/%d/%Y'), A.error_msg, A.temp_reference_id";
					break;

				case PORTAL_TMP_QA_APV:
					$table_name 			= Portal_Model::PORTAL_TABLE_TEMP_APVS;
					$data['primary_field'] 	= 'temp_apv_id';
					$data['table_header'] 	= array('AG CODE', 'BUSINESS CENTER CODE', 'VENDOR CODE', 'REF NUMBER', 'APV NUMBER', 'APV DATE', 'APV AMOUNT', 'CV NUMBER' , 'CV AMOUNT', 'PARTICULARS', 'STATUS', 'ERROR/ WARNING');
					$selected_fields 		= " A.temp_apv_id, A.account_group_code, A.org_code, A.vendor_code, A.reference_num, A.apv_num, DATE_FORMAT(A.apv_date, '%m/%d/%Y'), if(A.apv_amount REGEXP '^[0-9]+\\.?[0-9]*$', FORMAT(A.apv_amount, 2), A.apv_amount), A.cv_num, if(A.cv_amount REGEXP '^[0-9]+\\.?[0-9]*$', FORMAT(A.cv_amount, 2), A.cv_amount), A.particulars, A.apv_status_code, A.error_msg, A.temp_reference_id";
					break;
				
				default:
					# code...
					break;
			}

			$user_id			= $this->session->user_id;
			$records			= $this->quick_add_model->get_tmp_records($selected_fields, $table_name, $arr_ids, $user_id);
			
			$data['records'] 	= $records;
			
			$data['table_name'] = $table_name;
			$data['batch_flag'] = $batch_flag;
			$modal 				= "modals/generated_file";

		}
		catch (PDOException $e)
		{
			$msg  = $this->get_user_message($e);

      		$this->error_index( $msg );
		} 
		
		catch (Exception $e) 
		{
			$msg  = $this->rlog_error($e, TRUE);	

      		$this->error_index( $msg );
		}
		 
		$this->load->view($modal, $data);
		$this->load_resources->get_resource($resources);
	}

    public function process_imported_file()
    {
    	try
    	{
    		set_time_limit(300);

    		$status 	= ERROR;
    		$flag 		= 0;
    		$msg 		= 'Error';
    		$data 		= '';
    		$params 	= get_params();

			$keyword 	= '';
    		$err_msg	= '';
    		$apv_actor 	= '';
    		$io_actor 	= '';

    		$err_err = $err_warning = 0;

    		//log to overview
			//starts

			$overview_type		   	= OVERVIEW_TYPE_ADD_TRANSACTION;
			//Get the transaction module code not the tab module code
			
			//Ends

    		Portal_Model::beginTransaction();

    		$import_actor	= $this->quick_add_model->get_user_fullname($this->session->user_id);
    		$import_actor	= ($import_actor)? " by <b>" . $import_actor . "</b>": "";

    		/*switch ($params['table_name']) {
    			case PORTAL_TMP_QA_IO:

    				$user_id			= $this->session->user_id;
    				
    				if($user_id){
    					$io_actor = $this->quick_add_model->get_user_fullname($user_id);
    				}

    				if($io_actor){
    					$io_actor = ' by <b>'.$io_actor.'</b>';
    				}

    				$roles 			= array(ROLE_BC_ADMIN); //specify role to be notified
					$module_code 	= ROOT_QA; 	//specify module code
					$message 		= ''; 		//notification for mobile
					$import_subject = 'Internal Orders';
					$notification 	= "<font color='#e23b3b'>".$import_subject."</font> <font color='#000000'> have been uploaded".$io_actor." </font>";

					$this->import_system_notification($roles, $module_code, $notification, $message);
    				break;
    			case PORTAL_TMP_QA_DR:

    				$user_id			= $this->session->user_id;
    				
    				if($user_id){
    					$dr_actor		= $this->quick_add_model->get_user_fullname($user_id);
    				}

    				if($dr_actor){
    					$dr_actor = ' by <b>'.$dr_actor.'</b>';
    				}

    				$roles 			= array(ROLE_LOG_PERS, ROLE_LOG_HEAD); //specify role to be notified
					$module_code 	= ROOT_QA; 	//specify module code
					$message 		= ''; 		//notification for mobile
					$import_subject = 'DRs';
					$notification 	= "<font color='#e23b3b'>".$import_subject."</font> <font color='#000000'> have been uploaded".$dr_actor." </font>";

					$this->import_system_notification($roles, $module_code, $notification, $message);
    				break;

    			case PORTAL_TMP_QA_APV:

    				$user_id			= $this->session->user_id;
    				
    				if($user_id){
    					$apv_actor = $this->quick_add_model->get_user_fullname($user_id);
    				}

    				if($apv_actor){
    					$apv_actor = ' by <b>'.$apv_actor.'</b>';
    				}

    				$roles 			= array(ROLE_SUPER_ADMIN, ROLE_SUP_CARE, ROLE_SALES_FINANCE, ROLE_ROTI_ADMIN, ROLE_ROH, ROLE_REGIONAL_HEAD, ROLE_RECIPIENT, ROLE_PURCH_PERS, ROLE_PURCH_HEAD, ROLE_PROJ_ENG, ROLE_PROD_FIN_PERS, ROLE_PRESIDENT, ROLE_PPEI, ROLE_PAY_FIN_PERS, ROLE_MDC, ROLE_MCS_ENG, ROLE_LOG_PERS, ROLE_ISSC, ROLE_HO_FINANCE, ROLE_FPA, ROLE_FM_HEAD, ROLE_FM_COOR, ROLE_FEEDS_FIN_PERS, ROLE_FCSS, ROLE_DPIM_HEAD, ROLE_DPIM, ROLE_DP_ASSIST, ROLE_DH, ROLE_CSS_HO, ROLE_CSS_HEAD, ROLE_CSS, ROLE_CG_SUP, ROLE_CG_LIQ_FIN_PERS, ROLE_BC_HEAD, ROLE_BC_FIN_PERS, ROLE_BC_ADMIN, ROLE_BA_UNIT
    				); //specify role to be notified

					$module_code 	= ROOT_QA; 	//specify module code
					$message 		= ''; 		//notification for mobile
					$import_subject = 'APVs';
					$notification 	= "<font color='#e23b3b'>".$import_subject."</font> <font color='#000000'> have been uploaded".$apv_actor." </font>";
					
					$this->import_system_notification($roles, $module_code, $notification, $message);
    				break;
    			
    			default:
    				# code...
    				break;

				
    		}*/


    		if(!EMPTY($params['table_name']) AND !EMPTY($params['import_checkbox']))
    		{
    			$table_name 		= $params['table_name'];
    			$import_checkbox 	= $params['import_checkbox'];
    			$field_names 		= ' * ';

				$user_id			= $this->session->user_id;
				$selected_records 	= $this->quick_add_model->get_tmp_records($field_names, $table_name, $import_checkbox, $user_id);

    			//Insert to actual table
    			$fields				= array();

    			$orgs				= array();

    			foreach($selected_records as $key => $values)
    			{
    				$date						= date('Y-m-d');
    				$date_time					= date('Y-m-d H:i:s');

    				$insert_data				= FALSE;
    				$update_data				= FALSE;
    				$insert_extend_data			= FALSE;
    				$insert_doc_data			= FALSE;
    				$create_workflow			= FALSE;
    				$email_notif				= FALSE;
    				$system_notif				= FALSE;
    				$continue					= TRUE;
    				$auto_complete_task			= FALSE;
    				$update_task				= FALSE;
    				$auto_assign				= FALSE;
    				$add_org					= FALSE;

    				$batch						= FALSE;

    				$insert_fields				= array();
    				$update_fields				= array();
    				$insert_extend_fields_cols	= array();
    				$insert_extend_fields_datas	= array();
    				$insert_doc_fields			= array();

    				$append_workflow_stages		= array();

    				$pria_workflow_id			= NULL;
    				$core_workflow_task_id		= NULL;
    				$pria_task_id				= NULL;
    				$org_code					= NULL;
    				$insert_extend_data_key		= NULL;

    				$trans_email				= NULL;
    				$trans_notif				= NULL;

    				$link						= NULL;

    				$account_group_tasks		= $this->check_account_group_tasks($values['account_group_code']);

    				switch($table_name)
    				{
    					case PORTAL_TMP_QA_IO:
    						$type										= TEMPLATE_QA_IO_LIST;
    						$module_code								= MODULE_PORTAL_QA_IO;
    						$actual_table_name							= Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
    						$reference_num								= $values['temp_io_num'];
    						$org_code									= $values['org_code'];

    						$validate_params							= array(
    								'account_group_code'				=> $values['account_group_code'],
    								'reference_num'						=> $reference_num,
									'vendor_code'						=> $values['vendor_code'],
									'org_code'							=> $values['org_code'],
									'site_code'							=> $values['site_code'],
									'cycle_num'							=> $values['cycle_num']
    						);

    						$line_no									= $key + 1;

    						$curr_err_msg								= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);

    						if(EMPTY($curr_err_msg))
	   						{
								$placement_year 						=  substr($reference_num, 4, 2);
	   							$io_details								= $this->quick_add_model->get_io_trans_rec($values['account_group_code'], $values['org_code'], $values['vendor_code'], $values['site_code'], $values['cycle_num'], $placement_year);
	   							
	   							if(ISSET($io_details['reference_id']) AND !EMPTY($io_details['reference_id']))
	   							{
	   								if($reference_num != $io_details['reference_num'])
	   								{
	   									$reference_id						= $io_details['reference_id'];
	   									$overview_msg						= sprintf($this->lang->line('update_transaction_io'), $io_details['reference_num']);

	   									$update_io_fields					= array('io_num' => $reference_num);
	   									$update_io_where					= array('io_id' => $reference_id);
	   									$this->quick_add_model->update_actual_data($actual_table_name, $update_io_fields, $update_io_where);

	   									$update_workflow_fields				= array('reference_num' => $reference_num);
	   									$update_workflow_where				= array('pria_workflow_id' => $io_details['pria_workflow_id']);
										$this->quick_add_model->update_actual_data(Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS, $update_workflow_fields, $update_workflow_where);

	   									$update_payment_fields				= array('reference_num' => $reference_num);
	   									$update_payment_where				= array('reference_id' => $reference_id, 'reference_type_code' => APV_REF_TYPE_CODE_IO);
	   									$this->quick_add_model->update_actual_data(Portal_Model::PORTAL_TABLE_PAYMENTS, $update_payment_fields, $update_payment_where);

										$message   =<<<EOS
											<font color='#e23b3b'>Internal Order</font> 
											<font color='#000000'>
												<b>{$io_details['reference_num']}</b> 
												has been updated to 
												<b>$reference_num</b> 
												$import_actor
											</font>
EOS;
										
										/* $roles 			= array(); //specify role to be notified
										$module_code 	= ROOT_QA; 	//specify module code
										$message 		= ''; 		//notification for mobile
										//$import_subject = 'Internal Order ';
										//$notification 	= "<font color='#e23b3b'>".$import_subject."</font> <font color='#000000'> has been updated by".$io_actor." </font> to <font color='#e23b3b'>'.$reference_num.'</font>";
										
					
										$this->import_system_notification($roles, $module_code, $notification, $message); */
	   								}
	   								else
	   								{
	   									$continue							= FALSE;
	   								}
	   							}
	   							else
	   							{
		   							$insert_data						= TRUE;

		   							$insert_fields						= array(
											'io_num'					=> $reference_num,
											'cycle_num'					=> $values['cycle_num'],
											'vendor_code'				=> $values['vendor_code'],
											'org_code'					=> $values['org_code'],
											'site_code'					=> $values['site_code'],
											'created_by'				=> $this->session->user_id,
											'created_date'				=> $date_time,
											'placement_year'			=> $placement_year
		   							);

		   							$tab_module_details					= $this->get_tab_module_details($values['account_group_code'], $module_code);
		   							$workflow_id						= (ISSET($tab_module_details['core_workflow_id']) AND !EMPTY($tab_module_details['core_workflow_id']))? $tab_module_details['core_workflow_id']: NULL;

		   							if(!EMPTY($workflow_id))
		   							{
			   							$create_workflow				= TRUE;

			   							$workflow_fields				= array(
												'account_group_code'	=> $values['account_group_code'],
												'reference_num'			=> $reference_num,
												'workflow_for_id'		=> $values['vendor_code'],
												'workflow_for_type'		=> WORKFLOW_FOR_VENDOR,
												'user_id'				=> $this->session->user_id,
												'org_code'				=> $values['org_code'],
												'vendor_code'			=> $values['vendor_code']
			   							);
		   							}

		   							$message   =<<<EOS
											<font color='#e23b3b'>Internal Order</font> 
											<font color='#000000'>
												<b>$reference_num</b> 
												has been uploaded 
												$import_actor 
											</font>
EOS;

		   							$auto_assign						= TRUE;

		   							$add_org							= TRUE;

	   								$overview_msg						= $this->lang->line('add_transaction_io');
	   							}

	   							$system_notif							= TRUE;
	   							$notif_roles							= array(ROLE_BC_ADMIN);

	   							$email_notif							= TRUE;
	   							$email_notif_type						= EMAIL_NOTIF_SUB_IO_UPLOAD_LIST;


	   							$trans_email							= ENUM_NO;
	   							$trans_notif							= ENUM_YES;
    						}
    						else
    						{
	   							$continue								= FALSE;
    						}

    					break;

    					case PORTAL_TMP_QA_PR:
    						$type										= TEMPLATE_QA_PR_LIST;
    						$module_code								= MODULE_PORTAL_QA_PR;
    						$actual_table_name							= Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
    						$reference_num								= $values['temp_pr_num'];
    						
    						$validate_params							= array(
    								'account_group_code'				=> $values['account_group_code'],
    								'reference_num'						=> $reference_num,
									'purchasing_group_code'				=> $values['purchasing_group_code'],
									'pr_item_type'						=> $values['pr_item_type'],
									'cost_center_code'					=> $values['cost_center_code'],
									'gl_account_code'					=> $values['gl_account_code']
    						);

    						$line_no									= $key + 1;

    						$curr_err_msg								= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);

    						if(EMPTY($curr_err_msg))
	   						{
	   							$pr_details								= $this->quick_add_model->get_pr_trans_rec($values['account_group_code'], $reference_num);
	   							
	   							if(ISSET($pr_details['reference_id']) AND !EMPTY($pr_details['reference_id']))
	   							{
	   								$continue							= FALSE;
	   							}
	   							else
	   							{
		   							$insert_data						= TRUE;

		   							$insert_fields						= array(
											'account_group_code'		=> $values['account_group_code'],
											'pr_num'					=> $reference_num,
											'pr_item_type' 				=> strtoupper($values['pr_item_type']),
											'purchasing_group_code'		=> $values['purchasing_group_code'],
											'gl_account_code'			=> $values['gl_account_code'],
											'requestor'					=> array(filter_var($values['requestor'], FILTER_SANITIZE_STRING), 'ENCRYPT'),
											'created_by'				=> $this->session->user_id,
											'created_date'				=> $date_time
									);

		   							$insert_extend_table_name			= Portal_Model::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;
									$insert_extend_data					= TRUE;

									$insert_extend_data_key				= 'pr_id';
									$insert_extend_fields_cols			= array('cost_center_code');

									$cost_centers						= explode(',', $values['cost_center_code']);

									if(COUNT($cost_centers) > 0)
									{
										foreach($cost_centers AS $cc_key => $cost_center)
										{
											$insert_extend_fields_datas[]	= array(
													'cost_center_code'		=> trim($cost_center)
											);
										}
									}

		   							$tab_module_details					= $this->get_tab_module_details($values['account_group_code'], $module_code);
		   							$workflow_id						= (ISSET($tab_module_details['core_workflow_id']) AND !EMPTY($tab_module_details['core_workflow_id']))? $tab_module_details['core_workflow_id']: NULL;

		   							if(!EMPTY($workflow_id))
		   							{
			   							$create_workflow				= TRUE;

			   							$workflow_fields				= array(
												'account_group_code'	=> $values['account_group_code'],
												'reference_num'			=> $reference_num,
												'user_id'				=> $this->session->user_id
			   							);
		   							}
		   							
		   							$update_task						= TRUE;

		   							if(in_array($values['account_group_code'], array(AG_GOODS_BFFI, AG_GOODS_MARINADES)))
		   							{
		   								$auto_complete_task				= TRUE;
		   							}

	   								$overview_msg						= $this->lang->line('add_transaction_pr');
	   							}
    						}
    						else
    						{
	   							$continue								= FALSE;
    						}

    					break;

    					case PORTAL_TMP_QA_PO:

    						if(EMPTY($values['sys_file_name']))
    						{
	    						$type										= TEMPLATE_QA_PO_LIST;
	    						$module_code								= MODULE_PORTAL_QA_PO;
	    						$actual_table_name							= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
	    						$reference_num								= $values['po_num'];

	    						$validate_params							= array(
	    								'account_group_code'				=> $values['account_group_code'],
	    								'reference_num'						=> $reference_num,
										'vendor_code'						=> $values['vendor_code'],
										'org_code'							=> $values['org_code'],
										'pr_num'							=> $values['pr_num'],
										'boq_num'							=> $values['boq_num'],
										'amount'							=> $values['amount']
	    						);

								$pr_nums									= explode(',', $values['pr_num']);
								$pr_ok										= TRUE;
								$pr_ids										= [];

	    						$line_no									= $key + 1;

	    						$curr_err_msg								= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);

	    						if(EMPTY($curr_err_msg))
		   						{
									foreach($pr_nums AS $pr_key => $pr_num)
									{
    									$po_details							= $this->quick_add_model->get_po_records($values['account_group_code'], $values['org_code'], $values['vendor_code'], $reference_num, $pr_num, $values['boq_num'], TRANS_TAB_PO, $account_group_tasks['po_released_core_task_id']);

										if(ISSET($po_details['reference_id']) AND !EMPTY($po_details['reference_id']))
										{
											if($values['account_group_code'] != AG_GOODS_BAVI)
											{
												$pr_ok							= FALSE;
											}
										}
									}
			   						
		   							if($values['account_group_code'] == AG_GOODS_BAVI)
		   							{ 
			   							if($pr_ok)
			   							{
			   								$reference_id					= $po_details['reference_id'];
			   								$pria_task_id					= $po_details['pria_task_id'];

			   								$update_data					= TRUE;

			   								$update_fields					= array(
			   										'po_date'				=> $values['po_date'],
			   										'po_amount'				=> $values['amount'],
			   										'po_released_date'		=> $values['released_date']
			   								);

			   								$update_where					= array(
			   										'po_id'					=> $reference_id,
			   										// 'pr_id'					=> $po_details['sub_reference_id']
			   								);

			   								$update_task					= TRUE;
			   								$auto_complete_task				= TRUE;

			   								$overview_msg					= $this->lang->line('update_transaction_po_released');
			   							}
			   							else
			   							{
			   								$continue							= FALSE;
			   							}
		   							}
		   							else
		   							{	   							
			   							if(!$pr_ok)
			   							{
			   								$continue							= FALSE;
			   							}
			   							else
			   							{
											foreach($pr_nums AS $pr_key => $pr_num)
											{
												$pr_details						= $this->quick_add_model->get_pr_trans_rec($values['account_group_code'], $pr_num);

												if(!ISSET($pr_details['reference_id']) OR EMPTY($pr_details['reference_id']))
												{
													$pr_ok						= FALSE;
												}
												else
												{
													$pr_ids[]					= $pr_details['reference_id'];
												}
											}

	    									if($pr_ok)
	    									{
					   							$insert_data					= TRUE;

					   							$insert_fields					= array(
														'po_num'				=> $reference_num,
														'vendor_code'			=> $values['vendor_code'],
														'org_code'				=> $values['org_code'],
														'po_status_code'		=> PO_ACTIVE,
														// 'pr_id'					=> $pr_details['reference_id'],
														'created_by'			=> $this->session->user_id,
														'created_date'			=> $date_time
					   							);

				   								$update_data					= TRUE;

				   								$update_fields					= array(
				   										'po_date'				=> $values['po_date'],
				   										'po_amount'				=> $values['amount'],
				   										'po_released_date'		=> $values['released_date']
				   								);

				   								$update_where					= array(
				   										// 'pr_id'					=> $pr_details['reference_id']
				   								);

				   								$update_key						= 'po_id';

					   							$tab_module_details				= $this->get_tab_module_details($values['account_group_code'], $module_code);
					   							$workflow_id					= (ISSET($tab_module_details['core_workflow_id']) AND !EMPTY($tab_module_details['core_workflow_id']))? $tab_module_details['core_workflow_id']: NULL;

					   							if(!EMPTY($workflow_id))
					   							{
						   							$create_workflow			= TRUE;

						   							$workflow_fields			= array(
															'account_group_code'=> $values['account_group_code'],
															'reference_num'		=> $reference_num,
															'user_id'			=> $this->session->user_id,
															'org_code'			=> $values['org_code'],
															'vendor_code'		=> $values['vendor_code']
						   							);
					   							}

					   							$append_workflow_stages			= $account_group_tasks['po_append_workflow_stage'];

					   							$update_task					= TRUE;
					   							$auto_complete_task				= TRUE;

				   								$overview_msg					= $this->lang->line('add_transaction_po');
	    									}
	    									else
	    									{
	    										$continue						= FALSE;
	    									}
			   							}
		   							}
	    						}
	    						else
	    						{
		   							$continue								= FALSE;
	    						}
    						}
    						else
    						{
    							$batch										= TRUE;
								$overview_type 								= OVERVIEW_TYPE_UPLOAD_BATCH_FILES;
    							$type										= TEMPLATE_QA_PO_BATCH;
	    						$module_code								= MODULE_PORTAL_QA_PO_BATCH;
    							$actual_table_name							= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
    							$reference_num								= $values['po_num'];

	    						$validate_params							= array(
	    								'account_group_code'				=> $values['account_group_code'],
	    								'reference_num'						=> $reference_num,
										'vendor_code'						=> $values['vendor_code'],
										'org_code'							=> $values['org_code'],
										'pr_num'							=> $values['pr_num'],
										'boq_num'							=> $values['boq_num'],
										'file_name'							=> $values['file_name']
	    						);

								$pr_nums									= explode(';', $values['pr_num']);
								$pr_ok										= TRUE;
								$pr_ids										= [];

    							$curr_err_msg								= $this->_validate_batch_upload($type, $module_code, $validate_params, $err_msg, $err_warning, $err_err);
    							
    							if(EMPTY($curr_err_msg))
    							{
									foreach($pr_nums AS $pr_key => $pr_num)
									{
    									$po_details							= $this->quick_add_model->get_po_records($values['account_group_code'], $values['org_code'], $values['vendor_code'], $reference_num, $pr_num, $values['boq_num'], TRANS_TAB_PO);

										if(ISSET($po_details['reference_id']) AND !EMPTY($po_details['reference_id']))
										{
											$pr_ok							= FALSE;
										}
									}

    								if(!$pr_ok)
    								{
    									$continue							= FALSE;
		   							}
    								else
    								{
										RLog::error("KJBP3");
										foreach($pr_nums AS $pr_key => $pr_num)
										{
											$pr_details						= $this->quick_add_model->get_pr_trans_rec($values['account_group_code'], $pr_num);
											RLog::error($pr_nums);

											if(!ISSET($pr_details['reference_id']) OR EMPTY($pr_details['reference_id']))
											{
												$pr_ok						= FALSE;
											}
											else
											{
												$pr_ids[]					= $pr_details['reference_id'];
											}
										}

    									if($pr_ok)
    									{
											RLog::error("KJBP4");
											RLog::error($pr_ids);
				   							$insert_data					= TRUE;

				   							$insert_fields					= array(
													'po_num'				=> $reference_num,
													'vendor_code'			=> $values['vendor_code'],
													'org_code'				=> $values['org_code'],
													'po_status_code'		=> PO_ACTIVE,
													// 'pr_id'					=> $pr_details['reference_id'],
													'created_by'			=> $this->session->user_id,
													'created_date'			=> $date_time
				   							);

				   							$insert_doc_data				= TRUE;

				   							$insert_doc_fields				= array(
				   									'document_type_code'	=> DOC_TYPE_PO,
				   									'file_name'				=> $values['file_name'],
				   									'sys_file_name'			=> $values['sys_file_name'],
				   									'version'				=> '1.0',
				   									'module_code'			=> $account_group_tasks['po_module_code'],
				   									'created_by'			=> $this->session->user_id,
				   									'created_date'			=> $date_time
				   							);

				   							$tab_module_details				= $this->get_tab_module_details($values['account_group_code'], $module_code);
				   							$workflow_id					= (ISSET($tab_module_details['core_workflow_id']) AND !EMPTY($tab_module_details['core_workflow_id']))? $tab_module_details['core_workflow_id']: NULL;

				   							if(!EMPTY($workflow_id))
				   							{
					   							$create_workflow			= TRUE;

					   							$workflow_fields			= array(
														'account_group_code'=> $values['account_group_code'],
														'reference_num'		=> $reference_num,
														'user_id'			=> $this->session->user_id,
														'org_code'			=> $values['org_code'],
														'vendor_code'		=> $values['vendor_code']
					   							);
				   							}

				   							$append_workflow_stages			= $account_group_tasks['po_append_workflow_stage'];

				   							$update_task					= TRUE;
				   							$auto_complete_task				= TRUE;

			   								$overview_msg					= $this->lang->line('add_transaction_po_batch');
    									}
    									else
    									{
    										$continue						= FALSE;
    									}
    								}
    							}
    							else
    							{
    								$continue								= FALSE;
    							}
    						}

    					break;

    					case PORTAL_TMP_QA_SOA:

    						if(EMPTY($values['sys_file_name']))
    						{
	    						$type										= TEMPLATE_QA_SOA_LIST;
	    						$module_code								= MODULE_PORTAL_QA_SOA;
	    						$actual_table_name							= Portal_Model::PORTAL_TABLE_SOA;
	    						$reference_num								= $values['temp_soa_no'];

	    						$validate_params							= array(
	    								'account_group_code'				=> $values['account_group_code'],
	    								'reference_num'						=> $reference_num,
										'vendor_code'						=> $values['vendor_code'],
										'org_code'							=> $values['org_code']
	    						);

	    						$line_no									= $key + 1;

	    						$curr_err_msg								= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);

	    						if(EMPTY($curr_err_msg))
		   						{
		   							$soa_details							= $this->quick_add_model->get_soa_trans_rec($values['account_group_code'], $values['org_code'], $values['vendor_code'], $reference_num);

		   							$soa_centralized						= array(AG_FEEDMILL, AG_INBOUND_CENTRAL, AG_MANPOWER, AG_TOLL_PARTNERS);
		   							
		   							if(ISSET($soa_details['reference_id']) AND !EMPTY($soa_details['reference_id']))
		   							{
		   								$continue							= FALSE;
		   							}
		   							else
		   							{
			   							$insert_data						= TRUE;

			   							$insert_fields						= array(
												'account_group_code'		=> $values['account_group_code'],
												'soa_num'					=> $reference_num,
												'soa_date'					=> $values['soa_date'],
												'soa_amount'				=> $values['soa_amount'],
												'vendor_code'				=> $values['vendor_code'],
												'org_code'					=> $values['org_code'],
												'date_from'					=> $values['date_from'],
												'date_to'					=> $values['date_to'],
												'soa_type'					=> ((in_array($values['account_group_code'], $soa_centralized))? SOA_CENTRAL: SOA_NORMAL),
												'created_by'				=> $this->session->user_id,
												'created_date'				=> $date_time
			   							);

			   							$tab_module_details					= $this->get_tab_module_details($values['account_group_code'], $module_code);
			   							$workflow_id						= (ISSET($tab_module_details['core_workflow_id']) AND !EMPTY($tab_module_details['core_workflow_id']))? $tab_module_details['core_workflow_id']: NULL;

			   							if(!EMPTY($workflow_id))
			   							{
				   							$create_workflow				= TRUE;

				   							$workflow_fields				= array(
													'account_group_code'	=> $values['account_group_code'],
													'reference_num'			=> $reference_num,
													'workflow_for_id'		=> $values['vendor_code'],
													'workflow_for_type'		=> WORKFLOW_FOR_VENDOR,
													'user_id'				=> $this->session->user_id,
													'org_code'				=> $values['org_code'],
													'vendor_code'			=> $values['vendor_code']
				   							);
			   							}

			   							$update_task						= TRUE;

		   								$overview_msg						= $this->lang->line('add_transaction_soa');
		   							}
	    						}
	    						else
	    						{
		   							$continue								= FALSE;
	    						}
    						}
    						else
    						{
    							$batch										= TRUE;
								$overview_type 								= OVERVIEW_TYPE_UPLOAD_BATCH_FILES;
    							$type										= TEMPLATE_QA_SOA_BATCH;
	    						$module_code								= MODULE_PORTAL_QA_SOA_BATCH;
	    						$actual_table_name							= Portal_Model::PORTAL_TABLE_SOA;
	    						$reference_num								= $values['temp_soa_no'];

    							$validate_params							= array(
										'account_group_code'				=> $values['account_group_code'],
										'reference_num'						=> $reference_num,
										'vendor_code'						=> $values['vendor_code'],
										'org_code'							=> $values['org_code'],
										'file_name'							=> $values['file_name']
    							);

    							$curr_err_msg								= $this->_validate_batch_upload($type, $module_code, $validate_params, $err_msg, $err_warning, $err_err);

    							if(EMPTY($curr_err_msg))
    							{
    								$soa_details							= $this->quick_add_model->get_soa_records($values['account_group_code'], $values['org_code'], $values['vendor_code'], $reference_num, TRANS_TAB_SOA, $account_group_tasks['soa_upload_core_task_id']);
    								$soa_doc_details						= $this->quick_add_model->get_soa_records($values['account_group_code'], $values['org_code'], $values['vendor_code'], $reference_num, TRANS_TAB_SOA, $account_group_tasks['soa_upload_core_task_id'], DOC_TYPE_SOA);

    								if(ISSET($soa_details['pria_task_id']) AND !EMPTY($soa_details['pria_task_id']))
    								{
    									if(ISSET($soa_doc_details['document_id']) AND !EMPTY($soa_doc_details['document_id']))
    									{
    										$continue						= FALSE;
    									}
    									else
    									{
	    									$pria_task_id					= $soa_details['pria_task_id'];
	    									$reference_id					= $soa_details['reference_id'];

	    									$update_data					= TRUE;

			   								$update_fields					= array(
			   										'submission_date'		=> $date_time
			   								);

			   								$update_where					= array(
			   										'soa_id'				=> $reference_id
			   								);

				   							$insert_doc_data				= TRUE;

				   							$insert_doc_fields				= array(
				   									'document_type_code'	=> DOC_TYPE_SOA,
				   									'file_name'				=> $values['file_name'],
				   									'sys_file_name'			=> $values['sys_file_name'],
				   									'version'				=> '1.0',
				   									'module_code'			=> $account_group_tasks['soa_module_code'],
				   									'created_by'			=> $this->session->user_id,
				   									'created_date'			=> $date_time
				   							);

	    									$auto_complete_task				= TRUE;

			   								$overview_msg					= $this->lang->line('add_transaction_soa_batch');
	    								}
    								}
	    							else
	    							{
	    								$continue							= FALSE;
	    							}
    							}
    							else
    							{
    								$continue								= FALSE;
    							}
    						}

    					break;

    					case PORTAL_TMP_QA_DR:

    						$type										= TEMPLATE_QA_DR_LIST;
    						$module_code								= MODULE_PORTAL_QA_DR;
    						$actual_table_name							= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
    						$reference_num								= $values['temp_dr_no'];
    						
    						$validate_params							= array(
    								'account_group_code'				=> $values['account_group_code'],
    								'reference_num'						=> $reference_num,
									'vendor_code'						=> $values['vendor_code'],
									'org_code'							=> $values['org_code'],
									'dr_date'							=> $values['dr_date'],
									'cost_center'						=> $values['cost_center']
    						);

    						$line_no									= $key + 1;

    						$curr_err_msg								= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);

    						if(EMPTY($curr_err_msg))
	   						{
	   							$dr_details								= $this->quick_add_model->get_dr_trans_rec($values['account_group_code'], $values['org_code'], $values['vendor_code'], $reference_num);

	   							if(ISSET($dr_details['reference_id']) AND !EMPTY($dr_details['reference_id']))
	   							{
	   								$continue							= FALSE;
	   							}
	   							else
	   							{
	   								$site_details						= $this->quick_add_model->get_record_details(array('site_id'), Portal_Model::PORTAL_TABLE_SITES, FALSE, array('cost_center_code' => $values['cost_center']));

	   								$site_id							= (ISSET($site_details['site_id']) AND !EMPTY($site_details['site_id']))? $site_details['site_id']: NULL;

		   							$insert_data						= TRUE;

		   							$insert_fields						= array(
											'account_group_code'		=> $values['account_group_code'],
											'dr_num'					=> $reference_num,
											'dr_date'					=> $values['dr_date'],
											'dr_remarks'				=> $values['remarks'],
											'vendor_code'				=> $values['vendor_code'],
											'org_code'					=> $values['org_code'],
											'site_id'					=> $site_id,
											'created_by'				=> $this->session->user_id,
											'created_date'				=> $date_time
									);

		   							$tab_module_details					= $this->get_tab_module_details($values['account_group_code'], $module_code);
		   							$workflow_id						= (ISSET($tab_module_details['core_workflow_id']) AND !EMPTY($tab_module_details['core_workflow_id']))? $tab_module_details['core_workflow_id']: NULL;

		   							if(!EMPTY($workflow_id))
		   							{
			   							$create_workflow				= TRUE;

			   							$workflow_fields				= array(
												'account_group_code'	=> $values['account_group_code'],
												'reference_num'			=> $reference_num,
												'user_id'				=> $this->session->user_id,
												'org_code'				=> $values['org_code'],
												'vendor_code'			=> $values['vendor_code']
			   							);
		   							}

		   							$message   =<<<EOS
										<font color='#e23b3b'>DR</font> 
										<font color='#000000'>
											<b>$reference_num</b> 
											has been uploaded 
											$import_actor 
										</font>
EOS;

		   							$system_notif						= TRUE;
		   							$notif_roles						= array(ROLE_LOG_PERS, ROLE_LOG_HEAD);

		   							$trans_notif						= TRUE;

	   								$overview_msg						= $this->lang->line('add_transaction_dr');
	   							}
    						}
    						else
    						{
	   							$continue								= FALSE;
    						}

    					break;

    					case PORTAL_TMP_QA_GR:

    						$import_subject 							= 'GR';
    						$type										= TEMPLATE_QA_GR_LIST;
    						$module_code								= MODULE_PORTAL_QA_GR;
    						$actual_table_name							= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
    						$reference_num								= $values['temp_gr_no'];
    						
    						$validate_params							= array(
    								'account_group_code'				=> $values['account_group_code'],
    								'reference_num'						=> $reference_num,
									'vendor_code'						=> $values['vendor_code'],
									'org_code'							=> $values['org_code'],
									'dr_num'							=> $values['dr_number'],
									'gr_date'							=> $values['gr_date']
    						);

    						$line_no									= $key + 1;

    						$curr_err_msg								= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);

    						if(EMPTY($curr_err_msg))
	   						{
	   							$dr_details								= $this->quick_add_model->get_dr_trans_rec($values['account_group_code'], $values['org_code'], $values['vendor_code'], $values['dr_number']);
	   							
	   							if(ISSET($dr_details['reference_id']) AND !EMPTY($dr_details['reference_id']))
	   							{
	   								$dr_complete_details				= $this->quick_add_model->get_dr_trans_rec($values['account_group_code'], $values['org_code'], $values['vendor_code'], $values['dr_number'], $account_group_tasks['dr_core_task_id'], $account_group_tasks['dr_approve_core_task_id'], ENUM_YES, $account_group_tasks['gr_core_task_id']);

	   								if(ISSET($dr_complete_details['reference_id']) AND !EMPTY($dr_complete_details['reference_id']))
	   								{
		   								$gr_details						= $this->quick_add_model->get_gr_trans_rec($dr_details['reference_id'], $reference_num);

		   								if(ISSET($gr_details['reference_id']) AND !EMPTY($gr_details['reference_id']))
		   								{
											$continue						= FALSE;
		   								}
		   								else
		   								{
				   							$update_data					= TRUE;

				   							$update_fields					= array(
													'gr_num'				=> $reference_num,
													'gr_date'				=> $values['gr_date'],
													'modified_by'			=> $this->session->user_id,
													'modified_date'			=> $date_time
											);

											$update_where					= array(
													'account_group_code'	=> $values['account_group_code'],
													'dr_gr_id'				=> $dr_details['reference_id']
											);

											$reference_id					= $dr_details['reference_id'];

											$pria_task_id					= $dr_complete_details['gr_pria_task_id'];

											$update_task					= TRUE;
											$auto_complete_task				= TRUE;

											$pria_references 				= $this->quick_add_model->get_pria_references(['dr_gr_id' => $dr_details['reference_id']], ['po_id']);

											$po_details 					= $this->quick_add_model->get_purchase_order(['po_id' => $pria_references['po_id']], ['po_num']);	

											$keyword 						= $po_details['po_num'];

			   								$overview_msg					= $this->lang->line('add_transaction_gr');
		   								}
	   								}
	   								else
	   								{
										$continue						= FALSE;
	   								}

	   							}
	   							else
	   							{
	   								$continue							= FALSE;
	   							}
    						}
    						else
    						{
	   							$continue								= FALSE;
    						}

    					break;

    					case PORTAL_TMP_QA_APV:

    						$import_subject 								= 'APV';
    						$type											= TEMPLATE_QA_APV_LIST;
    						$module_code									= MODULE_PORTAL_QA_APV;    					
							$actual_table_name 								= Portal_Model::PORTAL_TABLE_PAYMENTS;
							$actual_sub_table_name 							= Portal_Model::PORTAL_TABLE_PAYMENT_APVS;
    						$reference_num									= $values['reference_num'];
    						
    						$validate_params								= array(
    								'account_group_code'					=> $values['account_group_code'],
    								'reference_num'							=> $reference_num,
    								'reference_type_code'					=> $values['reference_type_code'],
									'vendor_code'							=> $values['vendor_code'],
									'org_code'								=> $values['org_code'],
									'apv_status_code'						=> $values['apv_status_code'],
									'apv_num'								=> $values['apv_num'],
									'apv_amount'							=> $values['apv_amount'],
									'cv_num'								=> $values['cv_num'],
									'cv_amount'								=> $values['cv_amount'],
									'particulars'							=> $values['particulars']
    						);

    						$line_no										= $key + 1;
							
    						$curr_err_msg									= $this->_validate_transactions($type, $module_code, $validate_params, $line_no, FALSE, $err_msg, $err_warning, $err_err);
							
    						if(EMPTY($curr_err_msg))
	   						{
								$reference_id								= $values['reference_id'];

								$apv_status_where							= array('apv_status_name' => $values['apv_status_code']);
								$apv_status_details							= $this->quick_add_model->get_record_details(array('apv_status_id'), Portal_Model::PORTAL_TABLE_PARAM_APV_STATUS, FALSE, $apv_status_where);

								if(ISSET($apv_status_details['apv_status_id']) AND !EMPTY($apv_status_details['apv_status_id']))
								{
		    						if(EMPTY($reference_id))
		    						{
		    							$reference_type							= $this->_get_reference_type_details($values['account_group_code'], $values['vendor_code'], $values['org_code'], $reference_num, TRUE);

		    							if(ISSET($reference_type['reference_id']) AND !EMPTY($reference_type['reference_id']))
		    							{
		    								$reference_id						= $reference_type['reference_id'];
		    							}
		    							else
		    							{
		    								$continue							= FALSE;
		    							}
									}
								}
								else
								{
									$continue									= FALSE;
								}
								
								if($continue)
								{
		    						if(!EMPTY($values['payment_id']))
		    						{
		    							$payment_id							= $values['payment_id'];
		    						}
		    						else
		    						{
		    							$payments_details					= $this->_get_payment_reference_details($values);

		    							if(ISSET($payments_details['payment_id']) AND !EMPTY($payments_details['payment_id']))
		    							{
		    								$payment_id						= $payments_details['payment_id'];
		    							}
		    							else
		    							{
			    							$payment_fields					= array(
			    									'vendor_code'			=> $values['vendor_code'],
			    									'org_code'				=> $values['org_code'],
			    									'account_group_code'	=> $values['account_group_code'],
			    									'account_group_code'	=> $values['account_group_code'],
			    									'reference_type_code'	=> $values['reference_type_code'],
			    									'reference_num'			=> $reference_num,
			    									'reference_id'			=> $reference_id,
			    									'created_by'			=> $this->session->user_id,
			    									'created_date'			=> $date_time
			    							);

			    							$payment_id						= $this->quick_add_model->insert_actual_data($actual_table_name, $payment_fields);
		    							}
		    						}

		    						$payment_apv_details					= $this->quick_add_model->get_exist_payment_apv($payment_id, $values['apv_num'], $values['cv_num'], $values['particulars']);

		    						$payment_apv_id							= (!EMPTY($payment_apv_details['cv_payment_apv']))? $payment_apv_details['cv_payment_apv']: ((!EMPTY($payment_apv_details['null_payment_apv']))? $payment_apv_details['null_payment_apv']: $payment_apv_details['null_payment_cv']);
									
									$payment_apv_fields						= array(
											'payment_id'					=> $payment_id,
											'apv_num'						=> $values['apv_num'],
											'apv_date'						=> $values['apv_date'],
											'apv_amount'					=> $values['apv_amount'],
											'cv_num'						=> $values['cv_num'],
											'cv_amount'						=> $values['cv_amount'],
											'particulars'					=> $values['particulars'],
											'apv_status_code'				=> $apv_status_details['apv_status_id']
									);
								
		    						if(!EMPTY($payment_apv_id))
		    						{
		    							$payment_apv_fields['modified_by']	= $this->session->user_id;
		    							$payment_apv_fields['modified_date']= $date_time;

		    							$payment_apv_where					= array('payment_apv_id' => $payment_apv_id);

		    							$this->quick_add_model->update_actual_data($actual_sub_table_name, $payment_apv_fields, $payment_apv_where);
		    						}
		    						else
		    						{
		    							$payment_apv_fields['created_by']	= $this->session->user_id;
		    							$payment_apv_fields['created_date']	= $date_time;

										$this->quick_add_model->insert_actual_data($actual_sub_table_name, $payment_apv_fields);
		    						}

		   							$message   =<<<EOS
										<font color='#e23b3b'>APV</font> 
										<font color='#8F44A9'>
											<b>$reference_num</b> 
										</font>
										<font color='#000000'>
											has been uploaded 
											$import_actor 
										</font>
EOS;

		   							$system_notif							= TRUE;
									$notif_roles							= $this->_insert_qa_system_notif($values['account_group_code'], $values['apv_num']);

		   							$trans_notif							= TRUE;

		   							$overview_msg							= $this->lang->line('add_transaction_apv');
								}
							}

							/*//Insert system notification
							$this->_insert_qa_system_notif($values['account_group_code'], $values['apv_num']);*/

    					break;
    				}
    				
    				if($continue)
    				{
    					if($add_org AND !EMPTY($org_code))
    					{
    						$orgs[]								= $org_code;
    					}

	    				if($insert_data)
	    				{
	    					$reference_id						= $this->quick_add_model->insert_actual_data($actual_table_name, $insert_fields);

							if(ISSET($pr_ids) AND is_array($pr_ids) AND count($pr_ids) > 0)
							{
								foreach($pr_ids AS $pr_id_key => $pr_id)
								{
									$this->quick_add_model->insert_actual_data(Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE, ['po_id' => $reference_id, 'pr_id' => $pr_id]);
								}									
							}
	    				}

	    				if($update_data)
	    				{
	    					if(!EMPTY($update_key))
	    					{
	    						$update_where[$update_key]		= $reference_id;
	    					}

	    					$this->quick_add_model->update_actual_data($actual_table_name, $update_fields, $update_where);
	    				}

	    				if($insert_extend_data)
	    				{
	    					if(COUNT($insert_extend_fields_datas) > 0)
	    					{
		    					foreach($insert_extend_fields_datas AS $ie_key => $insert_extend_field_data)
		    					{
		    						if(COUNT($insert_extend_fields_cols) > 0)
		    						{
		    							foreach ($insert_extend_fields_cols as $iec_key => $insert_extend_fields_col)
		    							{
		    								$insert_extend_fields[$insert_extend_fields_col]	= $insert_extend_field_data[$insert_extend_fields_col];
		    							}

		    							if(!EMPTY($insert_extend_data_key))
		    							{
		    								$insert_extend_fields[$insert_extend_data_key]	= $reference_id;
		    							}

		    							$this->quick_add_model->insert_actual_data($insert_extend_table_name, $insert_extend_fields);
		    						}
		    					}
	    					}
	    				}

	    				if($create_workflow)
	    				{
	    					$workflow_fields['reference_id']	= $reference_id;

	    					$workflow_details					= $this->pria_workflow->copy_worfklow($workflow_id, $workflow_fields);

	    					if(EMPTY($pria_workflow_id))
	    					{
	    						$pria_workflow_id				= (ISSET($workflow_details['workflow_id']) AND !EMPTY($workflow_details['workflow_id']))? $workflow_details['workflow_id']: NULL;
	    					}

	    					if(EMPTY($core_workflow_task_id))
	    					{
	    						$core_workflow_task_id			= (ISSET($workflow_details['core_workflow_task_id']) AND !EMPTY($workflow_details['core_workflow_task_id']))? $workflow_details['core_workflow_task_id']: NULL;
	    					}

	    					if(EMPTY($pria_task_id))
	    					{
	    						$pria_task_id					= (ISSET($workflow_details['task_id']) AND !EMPTY($workflow_details['task_id']))? $workflow_details['task_id']: NULL;
	    					}

	    					$workflows							= $this->quick_add_model->get_record_details(array('max(sequence_no) as curr_stage_seq_no'), Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES, FALSE, array('pria_workflow_id' => $pria_workflow_id));

	    					$curr_stage_seq_no					= $workflows['curr_stage_seq_no'];
	    				}

	    				if($insert_doc_data)
	    				{
	    					$insert_doc_fields['reference']		= $reference_id;
	    					$insert_doc_fields['pria_task_id']	= $pria_task_id;

	    					$this->quick_add_model->insert_actual_data(Portal_Model::PORTAL_TABLE_DOCUMENTS, $insert_doc_fields);

	    					if(file_exists(FCPATH . PATH_QA_IMPORT_FILE . $insert_doc_fields['sys_file_name']))
	    					{
	    						rename(FCPATH . PATH_QA_IMPORT_FILE . $insert_doc_fields['sys_file_name'], FCPATH . PATH_UPLOADED_FILES . $insert_doc_fields['sys_file_name']);
	    					}
	    				}

	    				if(COUNT($append_workflow_stages) > 0)
	    				{
	    					foreach ($append_workflow_stages as $aws_key => $append_workflow_stage)
	    					{
								$this->pria_workflow->append_stage($pria_workflow_id, $curr_stage_seq_no, $append_workflow_stage, $this->session->user_id, TRUE, TRUE);
								
	    						$workflows						= $this->quick_add_model->get_record_details(array('count(workflow_stage_id) as cnt_workflow_stage_id'), Portal_Model::CORE_WORKFLOW_STAGES, FALSE, array('workflow_id' => $append_workflow_stage));
	    						$curr_stage_seq_no				= $curr_stage_seq_no + $workflows['cnt_workflow_stage_id'];
	    					}
	    				}

	    				if($update_task)
	    				{
		    				if(!EMPTY($pria_task_id))
		    				{
								$skip_overview	= ($auto_complete_task)? TRUE: FALSE;
								$this->pria_workflow->tag_task($pria_task_id, TASK_STATUS_ONGOING, ['reference' => $reference_id, 'skip_overview' => $skip_overview]);
								//($pria_task_id, $task_status_id, $columns=[], $user_id=NULL)
		    				}
	    				}
	    				else
	    				{
	    					if($auto_assign)
	    					{
	    						$this->pria_workflow->_clear_n_assign_next_task($pria_task_id, $core_workflow_task_id, $org_code);
	    					}
	    				}

	    				if($auto_complete_task)
	    				{
	    					//$this->task->tag_complete($pria_task_id);
							//$this->task->tag_task($pria_task_id, TASK_STATUS_DONE, array('skip_overview' => TRUE));
							$this->pria_workflow->tag_task($pria_task_id, TASK_STATUS_DONE/* , array('skip_overview' => TRUE) */);
						}
						
						$account_group_code						= $values['account_group_code'];
						
						$parent_module_code						= $this->get_module_code_per_task_ag_code($account_group_code);
						
						$module_details 						= $this->get_tab_module_details($account_group_code, $module_code);

	    				$overview_details   					= array(
								'reference' 					=> $reference_id,
								'transaction_num' 				=> $reference_num,
								'transaction_msg' 				=> $overview_msg,
								'created_by' 					=> $this->session->user_id,
								'created_date' 					=> $date_time,
								'account_group_code'			=> $account_group_code,
								'tab_module_code'				=> $module_details['to_tab_module_code'],
								'parent_module_code'			=> $parent_module_code,
								'keyword'						=> ( ! EMPTY($keyword)) ? $keyword : $reference_num,
								'account_group_code'			=> $account_group_code
						);


						if( ! EMPTY($pria_task_id))
						{
							$pria_task_details = $this->tm_model->get_task_details($pria_task_id);

							$overview_details  = array_merge($overview_details, $pria_task_details);
						}	
						//print_var_export($overview_details, $module_code); die;
						//parent_module_code = contract_growers //$module_details = tab_internal_orders
						//print_var_export($module_details, $parent_module_code); die;

						$this->pria_overview->log_overview($parent_module_code, $overview_type, $overview_details);

						if($email_notif AND $trans_email == ENUM_YES)
						{
							$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, $email_notif_type);
						}

						if($system_notif AND $trans_notif == ENUM_YES)
						{
    						$org_code		= (ISSET($values['org_code']) AND !EMPTY($values['org_code']))? $values['org_code']: NULL;
    						$vendor_code	= (ISSET($values['vendor_code']) AND !EMPTY($values['vendor_code']))? $values['vendor_code']: NULL;

							$org_details	= $this->quick_add_model->get_record_details(array('org_type_code'), Portal_Model::PORTAL_TABLE_ORGANIZATIONS, FALSE, array('org_code' => $org_code));

							$notif_orgs		= array();

							if(!EMPTY($org_code))
							{
								$notif_orgs	= $this->pria_mailer_model->get_trans_orgs($org_code, $org_details['org_type_code']);
								$notif_orgs	= (COUNT($notif_orgs) > 0)? array_column($notif_orgs, 'org_code'): array();
							}

							$recipients		= $this->pria_notification->import_system_notification($notif_roles, ROOT_QA, $message, $link, $this->session->user_id, $notif_orgs, $vendor_code);
						}
    				}
    			}

				if($email_notif AND $trans_email == ENUM_NO)
				{
					$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, $email_notif_type, NULL, NULL, NULL, $orgs);
				}

    			$status = SUCCESS;
    			$flag 	= 1;
    			$msg 	= $this->lang->line('success_import');
    		}else{
    			$status = ERROR;
    			$flag 	= 0;
	      		$msg 	= $this->lang->line('err_check_record');
			}

    		Portal_Model::commit();
    	}
    	catch(PDOException $e)
	    {
	    	$status = ERROR;
    		$flag 	= 0;
	      	$msg  	= $this->get_user_message($e);

	      	Portal_Model::rollback();
	    }
	    catch(Exception $e)
	    {
	      	$msg    = $this->rlog_error($e, TRUE);  
	      	$status = ERROR;
    		$flag 	= 0;

	      	Portal_Model::rollback();
	    }

	    $response = array(
            'status'    	=> $status,
            'msg'       	=> $msg,
            'flag'			=> $flag
        );

	    echo json_encode($response);
    }


    public function clean_tmp_tables()
    {
    	try
    	{
    		$params		= get_params();
    		$where		= array('created_by' => $this->session->user_id);

    		switch($params['table_name'])
    		{
    			case PORTAL_TMP_QA_IO:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_IOS);
    			break;
    			case PORTAL_TMP_QA_PO:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_POS);
    				$where['sys_file_name']	= "IS NULL";

    				if($params['batch_flag'])
    				{
    					$where['sys_file_name']	= "IS NOT NULL";
    				}
    			break;
    			case PORTAL_TMP_QA_PO_BATCH:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_POS);
    				$where['sys_file_name']	= "IS NOT NULL";
    			break;
    			case PORTAL_TMP_QA_PR:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_PRS);
    			break;
    			case PORTAL_TMP_QA_SOA:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_SOAS);
    				$where['sys_file_name']	= "IS NULL";

    				if($params['batch_flag'])
    				{
    					$where['sys_file_name']	= "IS NOT NULL";
    				}
    			break;
    			case PORTAL_TMP_QA_SOA_BATCH:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_SOAS);
    				$where['sys_file_name']	= "IS NOT NULL";
    			break;
    			case PORTAL_TMP_QA_DR:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_DRS);
    			break;
    			case PORTAL_TMP_QA_GR:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_GRS);
    			break;
    			case PORTAL_TMP_QA_APV:
    				$table_names = array(Portal_Model::PORTAL_TABLE_TEMP_APVS);
    			break;
    			default:
    				$table_names = array();
    			break;
    		}

			foreach($table_names as $table_name)
			{
				$this->quick_add_model->delete_temp($table_name, $where);
			}

    	}catch(PDOException $e)
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

	public function _get_payment_reference_details($fields)
	{
		try
		{
			$reference_details				= array();

			$payments_where					= array(
					'vendor_code'			=> $fields['vendor_code'],
					'org_code'				=> $fields['org_code'],
					'account_group_code'	=> $fields['account_group_code'],
					'reference_num'			=> $fields['reference_num']
			);

			$payments_details				= $this->quick_add_model->get_record_details(array('reference_id', 'reference_type_code', 'payment_id'), Portal_Model::PORTAL_TABLE_PAYMENTS, FALSE, $payments_where);

			if(ISSET($payments_details['payment_id']) AND !EMPTY($payments_details['payment_id']))
			{
				$reference_details['payment_id']			= $payments_details['payment_id'];
				$reference_details['reference_id']			= $payments_details['reference_id'];
				$reference_details['reference_type_code']	= $payments_details['reference_type_code'];
			}

			return $reference_details;
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

	public function _get_reference_type_details($account_group_code, $vendor_code=NULL, $org_code=NULL, $reference_num=NULL, $full_flag = FALSE, $completed	= FALSE, $ag_last_core_task_ids = array())
	{
		try
		{
			$reference_details							= array();

			$reference_type_code						= NULL;
			$table										= NULL;
			$table_id									= NULL;
			$account_group_code							= strtoupper($account_group_code);

			$trans_only									= FALSE;
			$trans_where								= array();

			switch($account_group_code)
			{
				case AG_CONTRACT_GROWERS:
					$reference_type_code				= APV_REF_TYPE_CODE_IO;
					$table								= Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
					$table_id							= "io_id";
				break;
				case AG_CONTRACTORS:
					$reference_type_code				= APV_REF_TYPE_CODE_PO;
					$table								= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
					$table_id							= "po_id";
				break;
				case AG_FEEDMILL:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_FORWARDERS:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_GOODS_BAVI:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_GOODS_BFFI:
					$reference_type_code				= APV_REF_TYPE_CODE_PO;
					$table								= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
					$table_id							= "po_id";
				break;
				case AG_GOODS_MARINADES:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_INBOUND_CENTRAL:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_INBOUND_NORMAL:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_LESSORS:
					$reference_type_code				= APV_REF_TYPE_CODE_CONTRACTS;
					$table								= Portal_Model::PORTAL_TABLE_CONTRACTS;
					$table_id							= "contract_id";
					$trans_only							= TRUE;
					$trans_where						= array(
							'account_group_code'		=> $account_group_code,
							'org_code'					=> $org_code,
							'vendor_code'				=> $vendor_code,
							'contract_code'				=> $reference_num
					);

					if($completed)
					{
						$trans_where['contract_status_code']	= ['IN', [CONTRACT_NEW, CONTRACT_STATUS_FOR_RENEWAL, CONTRACT_STATUS_OVERDUE, CONTRACT_STATUS_DUE_RENEWAL]];
					}
				break;
				case AG_MANPOWER:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_OUTBOUND:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_TOLL_PARTNERS:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
				case AG_SOA_BASED:
					$reference_type_code				= APV_REF_TYPE_CODE_SOA;
					$table								= Portal_Model::PORTAL_TABLE_SOA;
					$table_id							= "soa_id";
				break;
			}

			if($full_flag AND !EMPTY($table))
			{
				//print_var_export('FUCK');
				$reference_details						= $this->quick_add_model->get_reference_details($table, $table_id, $account_group_code, $vendor_code, $org_code, $reference_num, $completed, $ag_last_core_task_ids, $trans_only, $trans_where);
				//print_var_export('Inside', $reference_details); 
			}
			
			$reference_details['reference_type_code']	= $reference_type_code;
			$reference_details['trans_only']			= $trans_only;

			return $reference_details;
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

	public function check_account_group_tasks($account_group_code)
	{
		try
		{
			$account_group_tasks['pr_upload_core_task_id']					= NULL;
			$account_group_tasks['po_upload_core_task_id']					= NULL;
			$account_group_tasks['po_upload_approved_core_task_id']			= NULL;
			$account_group_tasks['po_released_core_task_id']				= NULL;
			$account_group_tasks['soa_upload_core_task_id']					= NULL;
			$account_group_tasks['dr_core_task_id']							= NULL;
			$account_group_tasks['dr_approve_core_task_id']					= NULL;
			$account_group_tasks['gr_core_task_id']							= NULL;

			$account_group_tasks['po_module_code']							= NULL;
			$account_group_tasks['soa_module_code']							= NULL;

			$account_group_tasks['po_append_workflow_stage']				= array();

			switch($account_group_code)
			{
				case AG_CONTRACT_GROWERS:
				break;
				case AG_CONTRACTORS:
					$account_group_tasks['po_upload_core_task_id']			= CORE_TASK_PO_UPLOAD_APPROVAL;
					$account_group_tasks['po_upload_approved_core_task_id']	= CORE_TASK_PO_UPLOAD_APPROVED;
					$account_group_tasks['po_released_core_task_id']		= CORE_TASK_PO_UPLOAD_APPROVED;

					$account_group_tasks['po_module_code']					= MODULE_PORTAL_TRANS_CONTRACTORS_PO;
				break;
				case AG_FEEDMILL:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_TRUCKERS_FEEDMILL;

					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_SOA;
				break;
				case AG_FORWARDERS:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_FORWARDERS;

					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_FORWARDER_SOA;
				break;
				case AG_GOODS_BAVI:
					$account_group_tasks['pr_upload_core_task_id']			= CORE_TASK_PR_UPLOAD_BAVI;
					$account_group_tasks['po_upload_core_task_id']			= CORE_TASK_PO_UPLOAD_APPROVAL;
					$account_group_tasks['po_upload_approved_core_task_id']	= CORE_TASK_PO_UPLOAD_APPROVED;
					//$account_group_tasks['po_released_core_task_id']		= CORE_TASK_PO_UPLOAD_APPROVED;
					$account_group_tasks['po_released_core_task_id']		= CORE_TASK_PO_RELEASED;
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD;
					$account_group_tasks['dr_core_task_id']					= CORE_TASK_PO_DR_BAVI_MARINADES;
					$account_group_tasks['dr_approve_core_task_id']			= CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES;
					$account_group_tasks['gr_core_task_id']					= CORE_TASK_PO_GR_BAVI_MARINADES;

					$account_group_tasks['po_module_code']					= MODULE_PORTAL_TRANS_GOODS_G_PO;
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_GOODS_G_SOA;

					$account_group_tasks['po_append_workflow_stage']		= array(CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL);
				break;
				case AG_GOODS_BFFI:
					$account_group_tasks['pr_upload_core_task_id']			= CORE_TASK_PR_UPLOAD_BFFI_MARINADES;
					$account_group_tasks['po_upload_core_task_id']			= CORE_TASK_PO_UPLOAD_WO_APPROVAL;
					$account_group_tasks['po_released_core_task_id']		= CORE_TASK_PO_UPLOAD_WO_APPROVAL;
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD;
					$account_group_tasks['dr_core_task_id']					= CORE_TASK_PO_DR_BFFI;
					$account_group_tasks['dr_approve_core_task_id']			= CORE_TASK_PO_DR_APPROVE_BFFI;
					$account_group_tasks['gr_core_task_id']					= CORE_TASK_PO_GR_BFFI;

					$account_group_tasks['po_module_code']					= MODULE_PORTAL_TRANS_GOODS_G_PO;
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_GOODS_G_SOA;

					$account_group_tasks['po_append_workflow_stage']		= array(CORE_WORKFLOW_DELIVERY_W_TRANSMITTAL);
				break;
				case AG_GOODS_MARINADES:
					$account_group_tasks['pr_upload_core_task_id']			= CORE_TASK_PR_UPLOAD_BFFI_MARINADES;
					$account_group_tasks['po_upload_core_task_id']			= CORE_TASK_PO_UPLOAD_WO_APPROVAL;
					$account_group_tasks['po_released_core_task_id']		= CORE_TASK_PO_UPLOAD_WO_APPROVAL;
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD;
					$account_group_tasks['dr_core_task_id']					= CORE_TASK_PO_DR_BAVI_MARINADES;
					$account_group_tasks['dr_approve_core_task_id']			= CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES;
					$account_group_tasks['gr_core_task_id']					= CORE_TASK_PO_GR_BAVI_MARINADES;
					
					$account_group_tasks['po_module_code']					= MODULE_PORTAL_TRANS_GOODS_M_PO;
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_GOODS_M_SOA;

					$account_group_tasks['po_append_workflow_stage']		= array(CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL);
				break;
				case AG_INBOUND_CENTRAL:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED;
					
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA;
				break;
				case AG_INBOUND_NORMAL:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_TRUCKERS_NORMAL;
					
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA;
				break;
				case AG_LESSORS:
				break;
				case AG_MANPOWER:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_TRUCKERS_MANPOWER;

					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_MANPOWER_SOA;
				break;
				case AG_OUTBOUND:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_TRUCKERS_NORMAL;
					
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_SOA;
				break;
				case AG_TOLL_PARTNERS:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED;
					
					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA;
				break;
				case AG_SOA_BASED:
					$account_group_tasks['soa_upload_core_task_id']			= CORE_TASK_SOA_UPLOAD_SOA_BASED;

					$account_group_tasks['soa_module_code']					= MODULE_PORTAL_TRANS_SOA_BASED_SOA;
				break;
			}

			return $account_group_tasks;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function _validate_batch_upload($type, $module_code, $validate_params, &$err_msg, &$err_warning, &$err_err)
	{
		try
		{
			$curr_err_msg								= NULL;

			$account_group_code							= (ISSET($validate_params['account_group_code']) AND !EMPTY($validate_params['account_group_code']))? strtoupper($validate_params['account_group_code']): NULL;
			$org_code									= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
			$vendor_code								= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
			$reference_num								= (ISSET($validate_params['reference_num']) AND !EMPTY($validate_params['reference_num']))? $validate_params['reference_num']: NULL;
			$file_name									= (ISSET($validate_params['file_name']) AND !EMPTY($validate_params['file_name']))? $validate_params['file_name']: NULL;

			$continue									= TRUE;
			$validate_exist								= TRUE;

			$validate_ag_pr								= FALSE;
			$validate_ag_code							= FALSE;
			$validate_qa_ag								= FALSE;
			$validate_bc								= FALSE;
			$validate_vendor							= FALSE;
			$validate_vendor_ag							= FALSE;
			$validate_vendor_bc							= FALSE;
			
			$account_group_tasks						= $this->check_account_group_tasks($account_group_code);

			if(!EMPTY($file_name))
			{
				switch($type)
				{
					case TEMPLATE_QA_PO_BATCH:
						$pr_num							= (ISSET($validate_params['pr_num']) AND !EMPTY($validate_params['pr_num']))? $validate_params['pr_num']: NULL;
						$boq_num						= (ISSET($validate_params['boq_num']) AND !EMPTY($validate_params['boq_num']))? $validate_params['boq_num']: NULL;

						$pr_nums						= explode(';', $pr_num);
						$pr_ok							= TRUE;

						if(EMPTY($account_group_code) OR EMPTY($org_code) OR EMPTY($vendor_code) OR EMPTY($reference_num) OR EMPTY($file_name) OR EMPTY($pr_num)
							OR ($account_group_code == AG_CONTRACTORS AND EMPTY($boq_num)))
						{
							$curr_err_msg				.= sprintf($this->lang->line('invalid_upload_file'), $file_name) . "<br/>";
							$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_upload_file'), $file_name)) . "\r\n";
							$err_warning++;

							$continue					= FALSE;
						}
						else
						{
							$validate_exist				= FALSE;

							foreach($pr_nums AS $pr_key => $pr_num)
							{
								$exist_details			= $this->quick_add_model->get_po_records($account_group_code, $org_code, $vendor_code, $reference_num, $pr_num, $boq_num, TRANS_TAB_PO);

								if(ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))
								{
									$pr_ok				= FALSE;
								}
							}

							$validate_ag_pr				= TRUE;
							$validate_ag_code			= TRUE;
							$validate_qa_ag				= TRUE;
							$validate_bc				= TRUE;
							$validate_vendor			= TRUE;
							$validate_vendor_ag			= TRUE;
							$validate_vendor_bc			= TRUE;
						}

						break;
					case TEMPLATE_QA_SOA_BATCH:

						if(EMPTY($account_group_code) OR EMPTY($org_code) OR EMPTY($vendor_code) OR EMPTY($reference_num) OR EMPTY($file_name))
						{
							$curr_err_msg				.= sprintf($this->lang->line('invalid_upload_file'), $file_name) . "<br/>";
							$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_upload_file'), $file_name)) . "\r\n";
							$err_warning++;

							$continue					= FALSE;
						}
						else
						{
							$soa_upload_core_task_id	= $account_group_tasks['soa_upload_core_task_id'];

							$exist_details				= $this->quick_add_model->get_soa_records($account_group_code, $org_code, $vendor_code, $reference_num, TRANS_TAB_SOA, $soa_upload_core_task_id);
							$file_exist_details			= $this->quick_add_model->get_soa_records($account_group_code, $org_code, $vendor_code, $reference_num, TRANS_TAB_SOA, $soa_upload_core_task_id, DOC_TYPE_SOA);
						}

						break;
				}
				
				if($continue)
				{
					$exist_reference					= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

					if($validate_exist)
					{
						$exist_task						= (ISSET($exist_details['pria_task_id']) AND !EMPTY($exist_details['pria_task_id']))? TRUE: FALSE;
						$file_exist						= (ISSET($file_exist_details['document_id']) AND !EMPTY($file_exist_details['document_id']))? TRUE: FALSE;

						if(!$exist_task)
						{
							$curr_err_msg				.= sprintf($this->lang->line('invalid_transaction_file'), $reference_num, $file_name) . "<br/>";
							$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_transaction_file'), $reference_num, $file_name)) . "\r\n";
							$err_warning++;
						}
						else
						{
							if($file_exist)
							{
								$curr_err_msg			.= sprintf($this->lang->line('exist_transaction_file'), $reference_num, $file_name) . "<br/>";
								$err_msg				.= sprintf($this->lang->line('prep_warning'), sprintf($this->lang->line('exist_transaction_file'), $reference_num, $file_name)) . "\r\n";
								$err_warning++;
							}
						}
					}
					else
					{
						if(!$pr_ok)
						{
							$curr_err_msg				.= sprintf($this->lang->line('exist_file_transaction_for'), $reference_num) . "<br/>";
							$err_msg					.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_file_transaction_for'), $reference_num)) . "\r\n";
							$err_warning++;
						}
						else
						{
							if($validate_ag_pr)
							{
                				$pr_last_tasks			= ($account_group_code == AG_GOODS_BAVI)? [CORE_TASK_PR_UPLOAD_BAVI]: [CORE_TASK_PR_UPLOAD_BFFI_MARINADES];

								foreach($pr_nums AS $pr_key => $pr_num)
								{
									$valid_ag_pr			= $this->quick_add_model->get_pr_trans_rec($account_group_code, $pr_num, $pr_last_tasks);

									if(!$valid_ag_pr)
									{
										$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code') . "<br/>";
										$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code')) . " on file " . $file_name . ".\r\n";
										$err_warning++;

										$pr_ok				= FALSE;
									}
								}
								
								if($pr_ok)
								{
									if(!ISSET($valid_ag_pr['pria_task_id']) OR EMPTY($valid_ag_pr['pria_task_id']))
									{
										$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code, PR not yet completed.') . "<br/>";
										$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code')) . " on file " . $file_name . ".\r\n";
										$err_warning++;
									}
								}
							}

							if($validate_ag_code)
							{
								$valid_ag_code			= $this->_validate_ag_code($account_group_code);

								if(!$valid_ag_code)
								{
									$curr_err_msg		.= sprintf($this->lang->line('invalid_value'), 'AG Code') . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'AG Code')) . " on file " . $file_name . ".\r\n";
									$err_warning++;
								}
							}

							if($validate_qa_ag)
							{
								$valid_qa_ag			= $this->_validate_qa_ag($module_code, $account_group_code);

								if(!$valid_qa_ag)
								{
									$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), 'AG Code', 'current Import') . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'AG Code', 'current Import')) . " on file " . $file_name . ".\r\n";
									$err_warning++;
								}
							}

							if($validate_bc)
							{
								$valid_bc				= $this->_validate_bc($org_code);

								if(!$valid_bc)
								{
									$curr_err_msg		.= sprintf($this->lang->line('invalid_value'), 'Business Center Code') . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Business Center Code')) . " on file " . $file_name . ".\r\n";
									$err_warning++;
								}
							}

							if($validate_vendor)
							{
								$valid_vendor			= $this->_validate_vendor($vendor_code);

								if(!$valid_vendor)
								{
									$curr_err_msg		.= sprintf($this->lang->line('invalid_value'), 'Vendor Code') . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Vendor Code')) . " on file " . $file_name . ".\r\n";
									$err_warning++;
								}
							}

							if($validate_vendor_ag)
							{
								$valid_vendor_ag		= $this->_validate_vendor_ag($account_group_code, $vendor_code);

								if(!$valid_vendor_ag)
								{
									$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current AG Code') . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current AG Code')) . " on file " . $file_name . ".\r\n";
									$err_warning++;
								}
							}

							if($validate_vendor_bc)
							{
								$valid_vendor_bc		= $this->_validate_vendor_bc($vendor_code, $org_code);

								if(!$valid_vendor_bc)
								{
									$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current Business Center Code') . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current Business Center Code')) . " on file " . $file_name . ".\r\n";
									$err_warning++;
								}
							}
						}
					}
				}
			}

			return $curr_err_msg;
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

	public function _validate_transactions($type, $module_code, $validate_params, $line_no, $temp_flag, &$err_msg, &$err_warning, &$err_err, $temp_field_key = NULL, &$temp_reference_id = NULL, $temp_id_values = array())
	{
		try
		{
			$curr_err_msg						= NULL;
			$site_label							= NULL;

			$validate_params['reference_num']	= ($temp_flag AND !EMPTY($temp_field_key))? $validate_params[$temp_field_key]: $validate_params['reference_num'];

			$account_group_code					= (ISSET($validate_params['account_group_code']) AND !EMPTY($validate_params['account_group_code']))? strtoupper($validate_params['account_group_code']): NULL;
			$reference_num						= (ISSET($validate_params['reference_num']) AND !EMPTY($validate_params['reference_num']))? $validate_params['reference_num']: NULL;

			$vendor_code						= NULL;
			$org_code							= NULL;
			$site_code							= NULL;
			$purchasing_group_code				= NULL;
			$item_type_code						= NULL;
			$cost_center_code					= NULL;
			$gl_account_code					= NULL;

			$validate_dates						= array();
			$validate_numbers					= array();
			$validate_amounts					= array();

			$validate_ag_pr						= FALSE;
			$validate_ag_code					= FALSE;
			$validate_qa_ag						= FALSE;
			$validate_bc						= FALSE;
			$validate_vendor					= FALSE;
			$validate_vendor_ag					= FALSE;
			$validate_vendor_bc					= FALSE;
			$validate_vendor_site				= FALSE;
			$validate_site						= FALSE;
			$validate_item_type					= FALSE;
			$validate_pg						= FALSE;
			$validate_cost_center				= FALSE;
			$validate_cost_center_multiple		= FALSE;
			$validate_cost_center_bc			= FALSE;
			$validate_gl_account				= FALSE;
			$validate_apv_status				= FALSE;
			$validate_date						= FALSE;
			$validate_numeric					= FALSE;
			$validate_amount					= FALSE;

			$account_group_tasks				= $this->check_account_group_tasks($account_group_code);

			switch ($type)
			{
				case TEMPLATE_QA_IO_LIST:

					$site_label					= "SLOC";

					$vendor_code				= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
					$org_code					= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
					$site_code					= (ISSET($validate_params['site_code']) AND !EMPTY($validate_params['site_code']))? $validate_params['site_code']: NULL;
					$cycle_num					= (ISSET($validate_params['cycle_num']) AND !EMPTY($validate_params['cycle_num']))? $validate_params['cycle_num']: NULL;
					$placement_year 			=  substr($reference_num, 4, 2);
					$exist_details				= $this->quick_add_model->get_io_trans_rec($account_group_code, $org_code, $vendor_code, $site_code, $cycle_num, $placement_year);

					$exist						= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

					$ref_num_where				= array('io_num' => $reference_num);

					if($exist)
					{
						$ref_num_where['io_id']	= array('!=' => $exist_details['reference_id']);
					}

					$ref_num_details			= $this->quick_add_model->get_record_details(array('io_id'), Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS, FALSE, $ref_num_where);

					if(ISSET($ref_num_details['io_id']) AND !EMPTY($ref_num_details['io_id']))
					{
						$curr_err_msg			.= sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code') . "<br/>";
						$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}
					else
					{
						if(!$exist)
						{
							$validate_ag_code		= TRUE;
							$validate_qa_ag			= TRUE;
							$validate_bc			= TRUE;
							$validate_vendor		= TRUE;
							$validate_vendor_ag		= TRUE;
							$validate_vendor_bc		= TRUE;
							$validate_vendor_site	= TRUE;
							$validate_site			= TRUE;
							$validate_numeric		= TRUE;

							$validate_numbers		= array('Cycle Number' => $cycle_num);

							$temp_sloc				= substr($reference_num, 0, 4);
							$temp_year				= substr($reference_num, 4, 2);
							$temp_month				= substr($reference_num, 6, 2);
							$temp_cycle				= substr($reference_num, 8, 2);

							if($temp_sloc != $site_code)
							{
								$curr_err_msg		.= sprintf($this->lang->line('err_not_match'), 'IO Number', 'SLOC', $reference_num) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_not_match'), 'IO Number', 'SLOC', $reference_num)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}

							$temp_year_full			= DateTime::createFromFormat('y', $temp_year);
							
							if(!$temp_year_full)
							{
								$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), 'Year', 'IO Number ' . $reference_num) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Year', 'IO Number ' . $reference_num)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}

							$temp_month_full		= DateTime::createFromFormat('m', $temp_month);
							
							if(!$temp_month_full)
							{
								$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), 'Month', 'IO Number ' . $reference_num) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Month', 'IO Number ' . $reference_num)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}

							if($temp_cycle != $cycle_num)
							{
								$curr_err_msg		.= sprintf($this->lang->line('err_not_match'), 'IO Number', 'Cycle Number', $reference_num) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_not_match'), 'IO Number', 'Cycle Number', $reference_num)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}

							if($temp_flag)
							{
								if($cycle_num < IO_MIN_CYCLE_NUM OR $cycle_num > IO_MAX_CYCLE_NUM)
								{
									$curr_err_msg	.= $this->lang->line('err_io_cycle_num') . "<br/>";
									$err_msg		.= sprintf($this->lang->line('prep_error'), $this->lang->line('err_io_cycle_num')) . " on row " . $line_no . ".\r\n";
									$err_warning++;
								}

								$temp_where				= array(
										'account_group_code'	=> $account_group_code,
										'temp_io_num'			=> $reference_num,
										'vendor_code'			=> $vendor_code,
										'org_code'				=> $org_code,
										'cycle_num'				=> $cycle_num
								);

								if(COUNT($temp_id_values) > 0)
								{
									$temp_where['temp_io_id']	= array('IN' => $temp_id_values);
								}

								$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_io_id'), Portal_Model::PORTAL_TABLE_TEMP_IOS, FALSE, $temp_where);

								if(ISSET($temp_exist_details['temp_io_id']) AND !EMPTY($temp_exist_details['temp_io_id']))
								{
									$temp_reference_id	= $temp_exist_details['temp_io_id'];
								}
							}
						}
						else
						{
							if($reference_num == $exist_details['reference_num'])
							{
								$curr_err_msg	.= $this->lang->line('err_no_update') . "<br/>";
								$err_msg		.= sprintf($this->lang->line('prep_error'), $this->lang->line('err_no_update')) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}
						}
					}

					break;

				case TEMPLATE_QA_PR_LIST:

					$purchasing_group_code		= (ISSET($validate_params['purchasing_group_code']) AND !EMPTY($validate_params['purchasing_group_code']))? $validate_params['purchasing_group_code']: NULL;
					$item_type_code				= (ISSET($validate_params['pr_item_type']) AND !EMPTY($validate_params['pr_item_type']))? $validate_params['pr_item_type']: NULL;
					$cost_center_code			= (ISSET($validate_params['cost_center_code']) AND !EMPTY($validate_params['cost_center_code']))? $validate_params['cost_center_code']: NULL;
					$gl_account_code			= (ISSET($validate_params['gl_account_code']) AND !EMPTY($validate_params['gl_account_code']))? $validate_params['gl_account_code']: NULL;

					$exist_details				= $this->quick_add_model->get_pr_trans_rec($account_group_code, $reference_num);

					$exist						= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

					if(!$exist)
					{
						$validate_ag_code				= TRUE;
						$validate_qa_ag					= TRUE;
						$validate_pg					= TRUE;
						$validate_item_type				= TRUE;
						$validate_cost_center_multiple	= TRUE;
						$validate_gl_account			= (!EMPTY($gl_account_code))? TRUE: FALSE;

						if($temp_flag)
						{
							$temp_where				= array(
									'account_group_code'	=> $account_group_code,
									'temp_pr_num'			=> $reference_num
							);

							if(COUNT($temp_id_values) > 0)
							{
								$temp_where['temp_pr_id']	= array('IN' => $temp_id_values);
							}

							$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_pr_id'), Portal_Model::PORTAL_TABLE_TEMP_PRS, FALSE, $temp_where);

							if(ISSET($temp_exist_details['temp_pr_id']) AND !EMPTY($temp_exist_details['temp_pr_id']))
							{
								$temp_reference_id	= $temp_exist_details['temp_pr_id'];
							}
						}
					}
					else
					{
						$curr_err_msg			.= sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code') . "<br/>";
						$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}

					break;

				case TEMPLATE_QA_PO_LIST:

					$vendor_code				= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
					$org_code					= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
					$pr_num						= (ISSET($validate_params['pr_num']) AND !EMPTY($validate_params['pr_num']))? $validate_params['pr_num']: NULL;
					$po_date					= (ISSET($validate_params['po_date']) AND !EMPTY($validate_params['po_date']))? $validate_params['po_date']: NULL;
					$released_date				= (ISSET($validate_params['released_date']) AND !EMPTY($validate_params['released_date']))? $validate_params['released_date']: NULL;
					$po_amount					= (ISSET($validate_params['amount']) AND !EMPTY($validate_params['amount']))? $validate_params['amount']: NULL;

					$pr_nums					= explode(',', $pr_num);
					$pr_ok						= TRUE;

					if($account_group_code == AG_GOODS_BAVI)
					{
						foreach($pr_nums AS $pr_key => $pr_num)
						{
							$exist_details			= $this->quick_add_model->get_po_records($account_group_code, $org_code, $vendor_code, $reference_num, $pr_num, NULL, TRANS_TAB_PO, $account_group_tasks['po_released_core_task_id']);
	
							$exist					= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;
							
							if(!$exist)
							{
								$curr_err_msg		.= sprintf($this->lang->line('not_exist_transaction_for'), $reference_num, 'Business Center Code, Vendor Code of AG Code PR Number ' . $pr_num) . "<br/>";
								$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('not_exist_transaction_for'), $reference_num, 'Business Center Code, Vendor Code of AG Code, PR Number ' . $pr_num)) . " on row " . $line_no . ".\r\n";
								$err_warning++;

								$pr_ok			= FALSE;
							}
						}

						if($pr_ok)
						{
							foreach($pr_nums AS $pr_key => $pr_num)
							{
								$exist_completed_details= $this->quick_add_model->get_po_records($account_group_code, $org_code, $vendor_code, $reference_num, $pr_num, NULL, TRANS_TAB_PO, $account_group_tasks['po_upload_approved_core_task_id'], ENUM_YES, NULL, TRUE);

								$exist_completed	= (ISSET($exist_completed_details['reference_id']) AND !EMPTY($exist_completed_details['reference_id']))? TRUE: FALSE;
								
								if(!$exist_completed)
								{
									$pr_ok			= FALSE;
								}
							}
							
							if(!$pr_ok)
							{
								$curr_err_msg	.= sprintf($this->lang->line('exist_transaction_not_approved_for'), $reference_num, 'released') . "<br/>";
								$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_not_approved_for'), $reference_num, 'released')) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}
							else
							{
								$validate_amount		= TRUE;
								$validate_amounts		= array("Amount" => $po_amount);

								if($temp_flag)
								{
									$validate_date		= TRUE;

									$validate_dates		= array("PO Date" => $po_date, "Released Date" => $released_date);

									$temp_where				= array(
											'account_group_code'	=> $account_group_code,
											'po_num'				=> $reference_num,
											'vendor_code'			=> $vendor_code,
											'org_code'				=> $org_code,
											'pr_num'				=> $pr_num
									);

									if(COUNT($temp_id_values) > 0)
									{
										$temp_where['temp_po_id']	= array('IN' => $temp_id_values);
									}

									$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_po_id'), Portal_Model::PORTAL_TABLE_TEMP_POS, FALSE, $temp_where);

									if(ISSET($temp_exist_details['temp_po_id']) AND !EMPTY($temp_exist_details['temp_po_id']))
									{
										$temp_reference_id	= $temp_exist_details['temp_po_id'];
									}
								}
							}
						}
					}
					else
					{
						foreach($pr_nums AS $pr_key => $pr_num)
						{
							$exist_details				= $this->quick_add_model->get_po_records($account_group_code, $org_code, $vendor_code, $reference_num, $pr_num, NULL, TRANS_TAB_PO);

							$exist						= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

							if($exist)
							{
								$curr_err_msg			.= sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code') . "<br/>";
								$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code')) . " on row " . $line_no . ".\r\n";
								$err_warning++;

								$pr_ok					= FALSE;
							}
						}

						if($pr_ok)
						{
							$validate_ag_pr			= TRUE;
							$validate_ag_code		= TRUE;
							$validate_qa_ag			= TRUE;
							$validate_bc			= TRUE;
							$validate_vendor		= TRUE;
							$validate_vendor_ag		= TRUE;
							$validate_vendor_bc		= TRUE;
							$validate_amount		= TRUE;

							$validate_amounts		= array('Amount' => $po_amount);

							if($temp_flag)
							{
								$validate_date		= TRUE;
								$validate_dates		= array("PO Date" => $po_date, "Released Date" => $released_date);

								$temp_where			= array(
										'account_group_code'	=> $account_group_code,
										'po_num'				=> $reference_num,
										'vendor_code'			=> $vendor_code,
										'org_code'				=> $org_code,
										'pr_num'				=> $pr_num
								);

								if(COUNT($temp_id_values) > 0)
								{
									$temp_where['temp_po_id']	= array('IN' => $temp_id_values);
								}

								$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_po_id'), Portal_Model::PORTAL_TABLE_TEMP_POS, FALSE, $temp_where);

								if(ISSET($temp_exist_details['temp_po_id']) AND !EMPTY($temp_exist_details['temp_po_id']))
								{
									$temp_reference_id	= $temp_exist_details['temp_po_id'];
								}
							}
						}
					}

					break;

				case TEMPLATE_QA_SOA_LIST:

					$vendor_code				= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
					$org_code					= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
					$soa_date					= (ISSET($validate_params['soa_date']) AND !EMPTY($validate_params['soa_date']))? $validate_params['soa_date']: NULL;
					$soa_amount					= (ISSET($validate_params['soa_amount']) AND !EMPTY($validate_params['soa_amount']))? $validate_params['soa_amount']: NULL;
					$date_from					= (ISSET($validate_params['date_from']) AND !EMPTY($validate_params['date_from']))? $validate_params['date_from']: NULL;
					$date_to					= (ISSET($validate_params['date_to']) AND !EMPTY($validate_params['date_to']))? $validate_params['date_to']: NULL;

					$exist_details				= $this->quick_add_model->get_soa_trans_rec($account_group_code, $org_code, $vendor_code, $reference_num);

					$exist						= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

					if(!$exist)
					{
						$validate_ag_code		= TRUE;
						$validate_qa_ag			= TRUE;
						$validate_bc			= TRUE;
						$validate_vendor		= TRUE;
						$validate_vendor_ag		= TRUE;
						$validate_vendor_bc		= TRUE;
						$validate_amount		= TRUE;
						$validate_amounts		= array("SOA Amount" => $soa_amount);

						if($temp_flag)
						{
							$validate_date		= TRUE;
							$validate_dates		= array("SOA Date" => $soa_date, "Period Covered From" => $date_from, "Period Covered To" => $date_to);

							/*if($this->soa_day_from != date('l', strtotime($date_from)))
							{
								$curr_err_msg			.= sprintf($this->lang->line('invalid_value_against_value'), "Period Covered From", "Period Covered From", $this->soa_day_from) . "<br/>";
								$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value_against_value'), "Period Covered From", "Period Covered From", $this->soa_day_from)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}

							if($this->soa_day_to != date('l', strtotime($date_to)))
							{
								$curr_err_msg			.= sprintf($this->lang->line('invalid_value_against_value'), "Period Covered To", "Period Covered To", $this->soa_day_to) . "<br/>";
								$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value_against_value'), "Period Covered To", "Period Covered To", $this->soa_day_to)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}*/

							if($date_from > $date_to)
							{
								$curr_err_msg			.= sprintf($this->lang->line('err_date_range'), "Period Covered") . " " . sprintf($this->lang->line('err_date_order'), "Period Covered From", "Period Covered To") . "<br/>";
								$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_date_range'), "Period Covered") . " " . sprintf($this->lang->line('err_date_order'), "Period Covered From", "Period Covered To")) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}
							else
							{
								if($soa_date < $date_to)
								{
									$curr_err_msg		.= sprintf($this->lang->line('err_date_order'), "Period Covered", "SOA Date") . "<br/>";
									$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_date_order'), "Period Covered", "SOA Date")) . " on row " . $line_no . ".\r\n";
									$err_warning++;
								}
							}

							$temp_where				= array(
									'account_group_code'	=> $account_group_code,
									'temp_soa_no'			=> $reference_num,
									'vendor_code'			=> $vendor_code,
									'org_code'				=> $org_code
							);

							if(COUNT($temp_id_values) > 0)
							{
								$temp_where['temp_soa_id']	= array('IN' => $temp_id_values);
							}

							$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_soa_id'), Portal_Model::PORTAL_TABLE_TEMP_SOAS, FALSE, $temp_where);

							if(ISSET($temp_exist_details['temp_soa_id']) AND !EMPTY($temp_exist_details['temp_soa_id']))
							{
								$temp_reference_id	= $temp_exist_details['temp_soa_id'];
							}
						}
					}
					else
					{
						$curr_err_msg			.= sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code') . "<br/>";
						$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}

					break;

				case TEMPLATE_QA_DR_LIST:

					$vendor_code				= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
					$org_code					= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
					$dr_date					= (ISSET($validate_params['dr_date']) AND !EMPTY($validate_params['dr_date']))? $validate_params['dr_date']: NULL;
					$cost_center_code			= (ISSET($validate_params['cost_center']) AND !EMPTY($validate_params['cost_center']))? $validate_params['cost_center']: NULL;

					$exist_details				= $this->quick_add_model->get_dr_trans_rec($account_group_code, $org_code, $vendor_code, $reference_num);
					
					$exist						= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

					if(!$exist)
					{
						$validate_ag_code		 = TRUE;
						$validate_qa_ag			 = TRUE;
						$validate_bc			 = TRUE;
						$validate_vendor		 = TRUE;
						$validate_vendor_ag		 = TRUE;
						$validate_vendor_bc		 = TRUE;
						$validate_cost_center	 = TRUE;
						$validate_cost_center_bc = TRUE;

						if($temp_flag)
						{	
							$validate_date		= TRUE;
							$validate_dates		= array("DR Date" => $dr_date);

							$temp_where				= array(
									'account_group_code'	=> $account_group_code,
									'temp_dr_no'			=> $reference_num,
									'vendor_code'			=> $vendor_code,
									'org_code'				=> $org_code
							);

							if(COUNT($temp_id_values) > 0)
							{
								$temp_where['temp_dr_id']	= array('IN' => $temp_id_values);
							}

							$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_dr_id'), Portal_Model::PORTAL_TABLE_TEMP_DRS, FALSE, $temp_where);

							if(ISSET($temp_exist_details['temp_dr_id']) AND !EMPTY($temp_exist_details['temp_dr_id']))
							{
								$temp_reference_id	= $temp_exist_details['temp_dr_id'];
							}
						}

					}
					else
					{
						$curr_err_msg			.= sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code') . "<br/>";
						$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}

					break;

				case TEMPLATE_QA_GR_LIST:

					$dr_ref						= ($temp_flag)? "dr_number": "dr_num";

					$vendor_code				= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
					$org_code					= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
					$dr_num						= (ISSET($validate_params[$dr_ref]) AND !EMPTY($validate_params[$dr_ref]))? $validate_params[$dr_ref]: NULL;
					$gr_date					= (ISSET($validate_params['gr_date']) AND !EMPTY($validate_params['gr_date']))? $validate_params['gr_date']: NULL;

					$exist_dr_details			= $this->quick_add_model->get_dr_trans_rec($account_group_code, $org_code, $vendor_code, $dr_num);

					$exist_dr					= (ISSET($exist_dr_details['reference_id']) AND !EMPTY($exist_dr_details['reference_id']))? TRUE: FALSE;

					if($exist_dr)
					{
						$exist_dr_complete		= $this->quick_add_model->get_dr_trans_rec($account_group_code, $org_code, $vendor_code, $dr_num, $account_group_tasks['dr_core_task_id'], $account_group_tasks['dr_approve_core_task_id'], ENUM_YES, $account_group_tasks['gr_core_task_id']);
						$exist_dr_complete		= (ISSET($exist_dr_complete['reference_id']) AND !EMPTY($exist_dr_complete['reference_id']))? TRUE: FALSE;

						if(!$exist_dr_complete)
						{
							$curr_err_msg		.= sprintf($this->lang->line('exist_transaction_not_approved_for'), $reference_num, 'DR') . "<br/>";
							$err_msg			.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('exist_transaction_not_approved_for'), $reference_num, 'DR')) . " on row " . $line_no . ".\r\n";
							$err_warning++;
						}
						else
						{
							$exist_details		= $this->quick_add_model->get_gr_trans_rec($exist_dr_details['reference_id'], $reference_num);

							$exist				= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;

							if($exist)
							{
								$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), $reference_num, 'DR Number') . "<br/>";
								$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $reference_num, 'DR Number')) . " on row " . $line_no . ".\r\n";
								$err_warning++;
							}
							else
							{
								if($temp_flag)
								{	
									$validate_date			= TRUE;
									$validate_dates			= array("GR Date" => $gr_date);

									$temp_where				= array(
											'account_group_code'	=> $account_group_code,
											'temp_gr_no'			=> $reference_num,
											'vendor_code'			=> $vendor_code,
											'org_code'				=> $org_code,
											'dr_number'				=> $dr_num
									);

									if(COUNT($temp_id_values) > 0)
									{
										$temp_where['temp_gr_id']	= array('IN' => $temp_id_values);
									}

									$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_gr_id'), Portal_Model::PORTAL_TABLE_TEMP_GRS, FALSE, $temp_where);

									if(ISSET($temp_exist_details['temp_gr_id']) AND !EMPTY($temp_exist_details['temp_gr_id']))
									{
										$temp_reference_id	= $temp_exist_details['temp_gr_id'];
									}
								}
							}
						}
					}
					else
					{
						$curr_err_msg			.= sprintf($this->lang->line('invalid_data_for'), $dr_num, 'AG Code, Business Center Code, Vendor Code') . "<br/>";
						$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $dr_num, 'AG Code, Business Center Code, Vendor Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}

					break;

				case TEMPLATE_QA_APV_LIST:

					$vendor_code				= (ISSET($validate_params['vendor_code']) AND !EMPTY($validate_params['vendor_code']))? $validate_params['vendor_code']: NULL;
					$org_code					= (ISSET($validate_params['org_code']) AND !EMPTY($validate_params['org_code']))? $validate_params['org_code']: NULL;
					$apv_date					= (ISSET($validate_params['apv_date']) AND !EMPTY($validate_params['apv_date']))? $validate_params['apv_date']: NULL;
					$apv_status_code			= (ISSET($validate_params['apv_status_code']) AND !EMPTY($validate_params['apv_status_code']))? $validate_params['apv_status_code']: NULL;
					$apv_num					= (ISSET($validate_params['apv_num']) AND !EMPTY($validate_params['apv_num']))? $validate_params['apv_num']: NULL;
					$apv_amount					= (ISSET($validate_params['apv_amount']) AND !EMPTY($validate_params['apv_amount']))? $validate_params['apv_amount']: NULL;
					$cv_num						= (ISSET($validate_params['cv_num']) AND !EMPTY($validate_params['cv_num']))? $validate_params['cv_num']: NULL;
					$cv_amount					= (ISSET($validate_params['cv_amount']) AND !EMPTY($validate_params['cv_amount']))? $validate_params['cv_amount']: NULL;
					$particulars				= (ISSET($validate_params['particulars']) AND !EMPTY($validate_params['particulars']))? $validate_params['particulars']: NULL;
					$reference_type_code		= (ISSET($validate_params['reference_type_code']) AND !EMPTY($validate_params['reference_type_code']))? $validate_params['reference_type_code']: NULL;

					$exist_details				= $this->_get_reference_type_details($account_group_code, $vendor_code, $org_code, $reference_num, TRUE);

					$exist						= (ISSET($exist_details['reference_id']) AND !EMPTY($exist_details['reference_id']))? TRUE: FALSE;
					
					if(!$exist)
					{
						$curr_err_msg			.= sprintf($this->lang->line('invalid_data_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code') . "<br/>";
						$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $reference_num, 'AG Code, Business Center Code, Vendor Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}
					else
					{
						$exist_completed		= $this->_get_reference_type_details($account_group_code, $vendor_code, $org_code, $reference_num, TRUE, TRUE, $this->last_ag_tasks[$account_group_code]);

						$exempt_arr 			= [AG_CONTRACT_GROWERS];

						if(
								(
									ISSET($exist_completed['not_appendable_pria_task_id']) 
									AND !EMPTY($exist_completed['not_appendable_pria_task_id'])
								) 
							OR (
									ISSET($exist_completed['appendable_last_pria_task_id']) 
									AND !EMPTY($exist_completed['appendable_last_pria_task_id'])
								)
							OR
								in_array($account_group_code, $exempt_arr) == TRUE	

							OR	$exist_completed['trans_only'] == TRUE
						  )
						{
							$exist_payment_where			= array(
									'reference_type_code'	=> $reference_type_code,
									'reference_num'			=> $reference_num,
									'reference_id'			=> $exist_details['reference_id']
							);

							$exist_payment_details	= $this->quick_add_model->get_record_details(array('payment_id'), Portal_Model::PORTAL_TABLE_PAYMENTS, FALSE, $exist_payment_where);

							$trans_conflict		= FALSE;

							if($exist_completed['trans_only'] == TRUE AND EMPTY($exist_completed['reference_id']) AND EMPTY($exist_payment_details['payment_id']))
							{
								$curr_err_msg	.= sprintf($this->lang->line('err_trans_not_for_payment2'), $reference_num) . "<br/>";
								$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_trans_not_for_payment2'), $reference_num)) . " on row " . $line_no . ".\r\n";
								$err_warning++;
								$trans_conflict	= TRUE;
							}

							if(!$trans_conflict)
							{
								if(!EMPTY($apv_num))
								{
									$exist_apv_where			= array('apv_num' => $apv_num);

									if(ISSET($exist_payment_details['payment_id']) AND !EMPTY($exist_payment_details['payment_id']))
									{
										$exist_apv_where['payment_id']	= array('!=' => $exist_payment_details['payment_id']);
									}

									$exist_apv_details			= $this->quick_add_model->get_record_details(array('payment_id'), Portal_Model::PORTAL_TABLE_PAYMENT_APVS, FALSE, $exist_apv_where);
								}

								if(!EMPTY($cv_num))
								{
									$exist_cv_where				= array('cv_num' => $cv_num);

									if(ISSET($exist_payment_details['payment_id']) AND !EMPTY($exist_payment_details['payment_id']))
									{
										$exist_cv_where['payment_id']	= array('!=' => $exist_payment_details['payment_id']);
									}

									$exist_cv_details			= $this->quick_add_model->get_record_details(array('payment_id'), Portal_Model::PORTAL_TABLE_PAYMENT_APVS, FALSE, $exist_cv_where);
								}

								if((ISSET($exist_apv_details['payment_id']) AND !EMPTY($exist_apv_details['payment_id']))
								OR (ISSET($exist_cv_details['payment_id']) AND !EMPTY($exist_cv_details['payment_id'])))
								{
									if(ISSET($exist_apv_details['payment_id']) AND !EMPTY($exist_apv_details['payment_id']))
									{
										$curr_err_msg			.= sprintf($this->lang->line('err_record_exist'), 'APV Number', $apv_num) . "<br/>";
										$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_record_exist'), 'APV Number', $apv_num)) . " on row " . $line_no . ".\r\n";
										$err_warning++;
									}

									if(ISSET($exist_cv_details['payment_id']) AND !EMPTY($exist_cv_details['payment_id']))
									{
										$curr_err_msg			.= sprintf($this->lang->line('err_record_exist'), 'CV Number', $cv_num) . "<br/>";
										$err_msg				.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_record_exist'), 'CV Number', $cv_num)) . " on row " . $line_no . ".\r\n";
										$err_warning++;
									}
								}
								else
								{
									$apv_status_details			= $this->quick_add_model->get_record_details(array('apv_status_id'), Portal_Model::PORTAL_TABLE_PARAM_APV_STATUS, FALSE, array('apv_status_name' => $apv_status_code));

									$apv_status_id				= (ISSET($apv_status_details['apv_status_id']) AND !EMPTY($apv_status_details['apv_status_id']))? $apv_status_details['apv_status_id']: NULL;

									$apv_cv_where				= array(
											'particulars'		=> (!EMPTY($particulars))? $particulars: "IS NULL",
											'apv_status_code'	=> $apv_status_id
									);

									$apv_cv_where['apv_num']	= (!EMPTY($apv_num))? $apv_num: "IS NULL";
									$apv_cv_where['cv_num']		= (!EMPTY($cv_num))? $cv_num: "IS NULL";

									$apv_cv_details				= $this->quick_add_model->get_record_details(array('payment_apv_id'), Portal_Model::PORTAL_TABLE_PAYMENT_APVS, FALSE, $apv_cv_where);

									if(ISSET($apv_cv_details['payment_apv_id']) AND !EMPTY($apv_cv_details['payment_apv_id']))
									{
										$curr_err_msg			.= $this->lang->line('err_no_update') . "<br/>";
										$err_msg				.= sprintf($this->lang->line('prep_error'), $this->lang->line('err_no_update')) . " on row " . $line_no . ".\r\n";
										$err_warning++;
									}
									else
									{
										$validate_apv_status	= TRUE;
										$validate_numeric		= TRUE;
										$validate_amount		= TRUE;

										$validate_numbers		= array('APV Number' => $apv_num, 'CV Number' => $cv_num);
										$validate_amounts		= array('APV Amount' => $apv_amount, 'CV Amount' => $cv_amount);

										if($temp_flag)
										{	
											$validate_date		= TRUE;
											$validate_dates		= array("APV Date" => $apv_date);

											/* $temp_payment_where		= array(
													'account_group_code'	=> $account_group_code,
													'vendor_code'			=> $vendor_code,
													'org_code'				=> $org_code,
													'reference_num'			=> $reference_num,
													'reference_type_code'	=> $reference_type_code
											);

											$temp_payment_details	= $this->quick_add_model->get_record_details(array('temp_apv_id'), Portal_Model::PORTAL_TABLE_TEMP_APVS, FALSE, $temp_payment_where);

											if(!EMPTY($apv_num))
											{
												$temp_apv_where		= array('apv_num' => $apv_num, 'particulars' => (!EMPTY($particulars))? $particulars: "IS NULL");

												if(ISSET($temp_payment_details['temp_apv_id']) AND !EMPTY($temp_payment_details['temp_apv_id']))
												{
													$temp_apv_where['temp_apv_id']	= array('!=' => $temp_payment_details['temp_apv_id']);
												}

												if(COUNT($temp_id_values) > 0)
												{
													$temp_apv_where['temp_apv_id']	= array('IN' => $temp_id_values);
												}

												$temp_apv_details	= $this->quick_add_model->get_record_details(array('temp_apv_id'), Portal_Model::PORTAL_TABLE_TEMP_APVS, FALSE, $temp_apv_where);
											}

											if(!EMPTY($cv_num))
											{
												$temp_cv_where		= array('cv_num' => $cv_num, 'particulars' => (!EMPTY($particulars))? $particulars: "IS NULL");

												if(ISSET($temp_payment_details['temp_apv_id']) AND !EMPTY($temp_payment_details['temp_apv_id']))
												{
													$temp_cv_where['temp_apv_id']	= array('!=' => $temp_payment_details['temp_apv_id']);
												}

												if(COUNT($temp_id_values) > 0)
												{
													$temp_cv_where['temp_apv_id']	= array('IN' => $temp_id_values);
												}

												$temp_cv_details	= $this->quick_add_model->get_record_details(array('temp_apv_id'), Portal_Model::PORTAL_TABLE_TEMP_APVS, FALSE, $temp_cv_where);
											} */

											if((ISSET($temp_apv_details['temp_apv_id']) AND !EMPTY($temp_apv_details['temp_apv_id']))
											OR (ISSET($temp_cv_details['temp_apv_id']) AND !EMPTY($temp_cv_details['temp_apv_id'])))
											{
												if(ISSET($temp_apv_details['temp_apv_id']) AND !EMPTY($temp_apv_details['temp_apv_id']))
												{
													$curr_err_msg	.= sprintf($this->lang->line('err_record_exist_temp'), 'APV Number', $apv_num) . "<br/>";
													$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_record_exist_temp'), 'APV Number', $apv_num)) . " on row " . $line_no . ".\r\n";
													$err_warning++;
												}

												if(ISSET($temp_cv_details['temp_apv_id']) AND !EMPTY($temp_cv_details['temp_apv_id']))
												{
													$curr_err_msg	.= sprintf($this->lang->line('err_record_exist_temp'), 'CV Number', $cv_num) . "<br/>";
													$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_record_exist_temp'), 'CV Number', $apv_num)) . " on row " . $line_no . ".\r\n";
													$err_warning++;
												}
											}
											else
											{
												$temp_where			= array(
														'account_group_code'	=> $account_group_code,
														'vendor_code'			=> $vendor_code,
														'org_code'				=> $org_code,
														'reference_num'			=> $reference_num,
														'reference_type_code'	=> $reference_type_code,
														'apv_num'				=> ((!EMPTY($apv_num))? $apv_num: "IS NULL"),
														'cv_num'				=> ((!EMPTY($cv_num))? $cv_num: "IS NULL"),
														'particulars'			=> (!EMPTY($particulars))? $particulars: "IS NULL",
														'apv_status_code'		=> $apv_status_code
												);

												if(COUNT($temp_id_values) > 0)
												{
													$temp_where['temp_apv_id']	= array('IN' => $temp_id_values);
												}

												$temp_exist_details	= $this->quick_add_model->get_record_details(array('temp_apv_id'), Portal_Model::PORTAL_TABLE_TEMP_APVS, FALSE, $temp_where);

												if(ISSET($temp_exist_details['temp_apv_id']) AND !EMPTY($temp_exist_details['temp_apv_id']))
												{
													$temp_reference_id	= $temp_exist_details['temp_apv_id'];
												}
											}
										}
									}
								}
							}
						}
						else
						{
							$curr_err_msg	.= sprintf($this->lang->line('err_trans_not_for_payment'), $reference_num) . "<br/>";
							$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_trans_not_for_payment'), $reference_num)) . " on row " . $line_no . ".\r\n";
							$err_warning++;
						}
					}

					break;
			}

			if($validate_ag_pr)
			{
                $pr_last_tasks		= ($account_group_code == AG_GOODS_BAVI)? [CORE_TASK_PR_UPLOAD_BAVI]: [CORE_TASK_PR_UPLOAD_BFFI_MARINADES];

				foreach($pr_nums AS $pr_key => $pr_num)
				{
					$valid_ag_pr		= $this->quick_add_model->get_pr_trans_rec($account_group_code, $pr_num, $pr_last_tasks);

					if(!$valid_ag_pr)
					{
						$curr_err_msg		.= sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code') . "<br/>";
						$err_msg			= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code')) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}
					else
					{
						if(!ISSET($valid_ag_pr['pria_task_id']) OR EMPTY($valid_ag_pr['pria_task_id']))
						{
							$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code, PR not yet completed.') . "<br/>";
							$err_msg		= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $pr_num, 'AG Code')) . " on row " . $line_no . ".\r\n";
							$err_warning++;
						}
					}
				}				
			}

			if($validate_ag_code)
			{
				$valid_ag_code		= $this->_validate_ag_code($account_group_code);

				if(!$valid_ag_code)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'AG Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'AG Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_qa_ag)
			{
				$valid_qa_ag		= $this->_validate_qa_ag($module_code, $account_group_code);

				if(!$valid_qa_ag)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'AG Code', 'current Import') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'AG Code', 'current Import')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_bc)
			{
				$valid_bc			= $this->_validate_bc($org_code);

				if(!$valid_bc)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'Business Center Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Business Center Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_vendor)
			{
				$valid_vendor		= $this->_validate_vendor($vendor_code);

				if(!$valid_vendor)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'Vendor Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Vendor Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_vendor_ag)
			{
				$valid_vendor_ag	= $this->_validate_vendor_ag($account_group_code, $vendor_code);

				if(!$valid_vendor_ag)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current AG Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current AG Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_vendor_bc)
			{
				$valid_vendor_bc	= $this->_validate_vendor_bc($vendor_code, $org_code);

				if(!$valid_vendor_bc)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current Business Center Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'current Business Center Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_vendor_site)
			{
				$valid_vendor_bc	= $this->_validate_vendor_site($vendor_code, $site_code);

				if(!$valid_vendor_bc)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'SLOC') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Vendor Code', 'SLOC')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}
			
			if($validate_site)
			{
				$valid_site			= $this->_validate_site($site_code);

				$site_label			= (!EMPTY($site_label))? $site_label: "Site Code";

				if(!$valid_site)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), $site_label) . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), $site_label)) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_pg)
			{
				$valid_pg			= $this->_validate_pg($purchasing_group_code);

				if(!$valid_pg)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'Purchasing Group Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Purchasing Group Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_item_type)
			{
				$valid_item_type	= $this->_validate_item_type($item_type_code);

				if(!$valid_item_type)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'Item Type Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Item Type Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_cost_center)
			{
				$valid_cost_center	= $this->_validate_cost_center($cost_center_code);

				if(!$valid_cost_center)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'Cost Center Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Cost Center Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_cost_center_bc)
			{
				$valid_cost_center_bc	= $this->_validate_cost_center_bc($cost_center_code, $org_code);

				if(!$valid_cost_center_bc)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), 'Cost Center Code', 'current Business Center Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), 'Cost Center Code', 'current Business Center Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_cost_center_multiple)
			{
				$valid_cost_center_multiple	= $this->_validate_cost_center_multiple($cost_center_code);

				if(!$valid_cost_center_multiple['valid'])
				{
					$cost_centers	= implode(', ', $valid_cost_center_multiple['invalid_cost_center']);
					$curr_err_msg	.= sprintf($this->lang->line('invalid_data_for'), $cost_centers, 'Cost Center Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_data_for'), $cost_centers, 'Cost Center Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_gl_account)
			{
				$valid_gl_account	= $this->_validate_gl_account($gl_account_code);

				if(!$valid_gl_account)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'GL Account Code') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'GL Account Code')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_apv_status)
			{
				$valid_apv_status	= $this->_validate_apv_status($apv_status_code);

				if(!$valid_apv_status)
				{
					$curr_err_msg	.= sprintf($this->lang->line('invalid_value'), 'Status') . "<br/>";
					$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value'), 'Status')) . " on row " . $line_no . ".\r\n";
					$err_warning++;
				}
			}

			if($validate_date)
			{
				foreach($validate_dates AS $key => $valid_date)
				{
					$validated_date	= $this->_validate_date($valid_date);

					if(!$validated_date)
					{
						$curr_err_msg	.= sprintf($this->lang->line('err_future_date'), $key, $key) . "<br/>";
						$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('err_future_date'), $key, $key)) . " on row " . $line_no . ".\r\n";
						$err_warning++;
					}
				}
			}

			if($validate_numeric)
			{
				foreach($validate_numbers AS $key => $validate_number)
				{
					if(!EMPTY($validate_number))
					{
						$validated_number	= (is_numeric($validate_number) AND !strstr($validate_number, '.'))? TRUE: FALSE;

						if(!$validated_number)
						{
							$curr_err_msg	.= sprintf($this->lang->line('invalid_value_against_value'), $key, $key, 'number') . "<br/>";
							$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value_against_value'), $key, $key, 'number')) . " on row " . $line_no . ".\r\n";
							$err_warning++;
						}
					}
				}
			}

			if($validate_amount)
			{
				foreach($validate_amounts AS $key => $validate_amnt)
				{
					if(!EMPTY($validate_amnt))
					{
						$validated_amount	= (is_numeric($validate_amnt))? TRUE: FALSE;

						if(!$validated_amount)
						{
							$curr_err_msg	.= sprintf($this->lang->line('invalid_value_against_value'), $key, $key, 'valid amount') . "<br/>";
							$err_msg		.= sprintf($this->lang->line('prep_error'), sprintf($this->lang->line('invalid_value_against_value'), $key, $key, 'valid amount')) . " on row " . $line_no . ".\r\n";
							$err_warning++;
						}
					}
				}
			}

			return $curr_err_msg;
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

	public function _validate_ag_code($account_group_code)
	{
		try
		{
			$ag_where	= array('account_group_code' => $account_group_code);
			$ag_details	= $this->quick_add_model->get_record_details(array('account_group_code'), Portal_Model::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS, FALSE, $ag_where);
			$ag			= (ISSET($ag_details['account_group_code']) AND !EMPTY($ag_details['account_group_code']))? TRUE: FALSE;
			return $ag;
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

	public function _validate_qa_ag($module_code, $account_group_code)
	{
		try
		{
			$qa_ag_details	= $this->get_tab_module_details($account_group_code, $module_code);
			$qa_ag			= (ISSET($qa_ag_details['tab_module_code']) AND !EMPTY($qa_ag_details['tab_module_code']))? TRUE: FALSE;
			return $qa_ag;
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

	public function _validate_bc($org_code)
	{
		try
		{
			$bc_where	= array('org_code' => $org_code);
			$bc_details	= $this->quick_add_model->get_record_details(array('org_code'), Portal_Model::PORTAL_TABLE_ORGANIZATIONS, FALSE, $bc_where);
			$bc			= (ISSET($bc_details['org_code']) AND !EMPTY($bc_details['org_code']))? TRUE: FALSE;
			return $bc;
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

	public function _validate_vendor($vendor_code)
	{
		try
		{
			$vendor_where	= array('vendor_code' => $vendor_code);
			$vendor_details	= $this->quick_add_model->get_record_details(array('vendor_code'), Portal_Model::PORTAL_TABLE_VENDORS, FALSE, $vendor_where);
			$vendor			= (ISSET($vendor_details['vendor_code']) AND !EMPTY($vendor_details['vendor_code']))? TRUE: FALSE;
			return $vendor;
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

	public function _validate_vendor_ag($account_group_code, $vendor_code)
	{
		try
		{
			$vendor_ag_where	= array('account_group_code' => $account_group_code, 'vendor_code' => $vendor_code);
			$vendor_ag_details	= $this->quick_add_model->get_record_details(array('vendor_code'), Portal_Model::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP, FALSE, $vendor_ag_where);
			$vendor_ag			= (ISSET($vendor_ag_details['vendor_code']) AND !EMPTY($vendor_ag_details['vendor_code']))? TRUE: FALSE;
			return $vendor_ag;
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

	public function _validate_vendor_bc($vendor_code, $org_code)
	{
		try
		{
			$vendor_bc_where	= array('vendor_code' => $vendor_code, 'org_code' => $org_code);
			$vendor_bc_details	= $this->quick_add_model->get_record_details(array('vendor_code'), Portal_Model::PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS, FALSE, $vendor_bc_where);
			$vendor_bc			= (ISSET($vendor_bc_details['vendor_code']) AND !EMPTY($vendor_bc_details['vendor_code']))? TRUE: FALSE;
			return $vendor_bc;
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
	
	public function _validate_vendor_site($vendor_code, $site_code)
	{
		try
		{
			$vendor_bc_where	= array('vendor_code' => $vendor_code, 'site_code' => $site_code);
			$vendor_bc_details	= $this->quick_add_model->get_record_details(array('vendor_code'), Portal_Model::PORTAL_TABLE_VENDOR_SITES, FALSE, $vendor_bc_where);
			$vendor_bc			= (ISSET($vendor_bc_details['vendor_code']) AND !EMPTY($vendor_bc_details['vendor_code']))? TRUE: FALSE;
			return $vendor_bc;
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

	public function _validate_site($site_code)
	{
		try
		{
			$site_where		= array('site_code' => $site_code);
			$site_details	= $this->quick_add_model->get_record_details(array('site_code'), Portal_Model::PORTAL_TABLE_SITES, FALSE, $site_where);
			$site			= (ISSET($site_details['site_code']) AND !EMPTY($site_details['site_code']))? TRUE: FALSE;
			return $site;
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

	public function _validate_pg($purchasing_group_code)
	{
		try
		{
			$pg_where	= array('purchasing_group_code' => $purchasing_group_code);
			$pg_details	= $this->quick_add_model->get_record_details(array('purchasing_group_code'), Portal_Model::PORTAL_TABLE_PARAM_PURCHASING_GROUP, FALSE, $pg_where);
			$pg			= (ISSET($pg_details['purchasing_group_code']) AND !EMPTY($pg_details['purchasing_group_code']))? TRUE: FALSE;
			return $pg;
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

	public function _validate_item_type($item_type_code)
	{
		try
		{
			$item_type_where	= array('pr_item_code' => $item_type_code);
			$item_type_details	= $this->quick_add_model->get_record_details(array('pr_item_code'), Portal_Model::PORTAL_TABLE_PRIA_PARAM_PR_ITEM_TYPES, FALSE, $item_type_where);
			$item_type			= (ISSET($item_type_details['pr_item_code']) AND !EMPTY($item_type_details['pr_item_code']))? TRUE: FALSE;
			return $item_type;
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

	public function _validate_cost_center($cost_center_code)
	{
		try
		{
			$cost_center_where		= array('cost_center_code' => $cost_center_code);
			$cost_center_details	= $this->quick_add_model->get_record_details(array('cost_center_code'), Portal_Model::PORTAL_TABLE_SITES, FALSE, $cost_center_where);
			$cost_center			= (ISSET($cost_center_details['cost_center_code']) AND !EMPTY($cost_center_details['cost_center_code']))? TRUE: FALSE;
			return $cost_center;
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

	public function _validate_cost_center_bc($cost_center_code, $org_code)
	{
		try
		{
			$cost_center_where		= array('cost_center_code' => $cost_center_code,'org_code' => $org_code);
			$cost_center_details	= $this->quick_add_model->get_record_details(array('cost_center_code'), Portal_Model::PORTAL_TABLE_SITES, FALSE, $cost_center_where);
			$cost_center			= (ISSET($cost_center_details['cost_center_code']) AND !EMPTY($cost_center_details['cost_center_code']))? TRUE: FALSE;
			return $cost_center;
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


	public function _validate_cost_center_multiple($cost_center_code)
	{
		try
		{
			$valid_cost_center_multiple		= FALSE;
			$invalid_cost_center			= array();

			$cost_centers					= explode(',', $cost_center_code);

			foreach($cost_centers AS $key => $cost_center)
			{
				$cost_center				= trim($cost_center);

				$valid_cost_center			= $this->_validate_cost_center($cost_center);

				if(!$valid_cost_center)
				{
					$invalid_cost_center[]	= $cost_center;
				}
			}

			$valid_cost_center_multiple		= (COUNT($invalid_cost_center) > 0)? FALSE: TRUE;

			$cost_center_multiple			= array('valid' => $valid_cost_center_multiple, 'invalid_cost_center' => $invalid_cost_center);

			return $cost_center_multiple;
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

	public function _validate_gl_account($gl_account_code)
	{
		try
		{
			$gl_account_where	= array('gl_account_code' => $gl_account_code, 'deleted_flag' => INITIAL_NO);
			$gl_account_details	= $this->quick_add_model->get_record_details(array('gl_account_code'), Portal_Model::PORTAL_TABLE_PARAM_GL_ACCOUNTS, FALSE, $gl_account_where);
			$gl_account			= (ISSET($gl_account_details['gl_account_code']) AND !EMPTY($gl_account_details['gl_account_code']))? TRUE: FALSE;
			return $gl_account;
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

	public function _validate_scope($scope_details, $org_code = NULL, $vendor_code = NULL)
	{
		try
		{
			if(ISSET($scope_details['scope']))
			{
				switch($scope_details['scope'])
				{
					case SCOPE_SYSTEM:
						$scope	= TRUE;
					break;
					case SCOPE_REGION:
						$scope	= (ISSET($scope_details['orgs']) AND !EMPTY($org_code) AND in_array($org_code, $scope_details['orgs']))? TRUE: FALSE;
					break;
					case SCOPE_AGENCY:
						$scope	= (ISSET($scope_details['vendor_code']) AND !EMPTY($vendor_code) AND $vendor_code == $scope_details['vendor_code'])? TRUE: FALSE;
					break;
					default:
						$scope	= FALSE;
					break;
				}
			}
			else
			{
				$scope	= FALSE;
			}

			return $scope;
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

	public function _validate_apv_status($apv_status_code)
	{
		try
		{
			$apv_status_where	= array('apv_status_name' => $apv_status_code);
			$apv_status_details	= $this->quick_add_model->get_record_details(array('apv_status_id'), Portal_Model::PORTAL_TABLE_PARAM_APV_STATUS, FALSE, $apv_status_where);
			$apv_status			= (ISSET($apv_status_details['apv_status_id']) AND !EMPTY($apv_status_details['apv_status_id']))? TRUE: FALSE;
			return $apv_status;
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

	public function _validate_date($date)
	{
		try
		{
			return ($date > date('Y-m-d H:i:s'))? FALSE: TRUE;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function _validate_field_length($field, $value)
	{
		try
		{
			$field_length_details	= array();

			if(in_array($field, ['soa_amount', 'amount', 'apv_amount', 'cv_amount']))
			{
				$value				= (is_numeric($value))? number_format($value, 2): $value;
			}

			$too_short				= ENUM_NO;
			$too_long				= ENUM_NO;
			$value_length			= strlen($value);
			$min_length				= 0;
			$max_length				= 0;

			switch($field)
			{
				case 'account_group_code':
					$max_length		= 30;
				break;
				case 'org_code':
					$max_length		= 25;
				break;
				case 'vendor_code':
					$max_length		= 45;
				break;
				case 'temp_io_num':
					$min_length		= 10;
					$max_length		= 10;
				break;
				case 'temp_pr_num':
					$max_length		= 45;
				break;
				case 'pr_num':
					// $max_length		= 45;
				break;
				case 'po_num':
					$max_length		= 45;
				break;
				case 'temp_soa_no':
					$max_length		= 45;
				break;
				case 'temp_dr_no':
				case 'dr_number':
					$max_length		= 45;
				break;
				case 'temp_gr_no':
					$max_length		= 45;
				break;
				case 'apv_num':
					$max_length		= 10;
				break;
				case 'cv_num':
					$max_length		= 45;
				break;
				case 'site_code':
					$max_length		= 45;
				break;
				case 'cycle_num':
					$max_length		= 10;
				break;
				case 'purchasing_group_code':
					$max_length		= 45;
				break;
				case 'pr_item_type':
					$max_length		= 45;
				break;
				case 'cost_center':
					$max_length		= 45;
				case 'cost_center_code':
					$max_length		= 255;
				break;
				case 'gl_account_code':
					$max_length		= 20;
				break;
				case 'requestor':
					$max_length		= 100;
				break;
				/*case 'po_date':
				case 'released_date':
				case 'soa_date':
				case 'dr_date':
				case 'gr_date':
					$max_length		= 10;
				break;
				case 'apv_date':
					$max_length		= 19;
				break;*/
				case 'week_no':
					$max_length		= 45;
				break;
				case 'particulars':
					$max_length		= 255;
				break;
				case 'apv_status_code':
					$max_length		= 100;
				break;
				case 'soa_amount':
				case 'amount':
					$max_length		= 13;
				break;
				case 'apv_amount':
				case 'cv_amount':
					$max_length		= 20;
				break;
			}

			if($value_length > 0 AND $max_length > 0)
			{
				if($value_length > $max_length)
				{
					$too_long		= ENUM_YES;
					$value			= substr($value, 0, $max_length);
				}
				else
				{
					if($min_length > 0 AND $value_length < $min_length)
					{
						$too_short	= ENUM_YES;
					}
				}
			}

			$field_length_details['too_short']	= $too_short;
			$field_length_details['too_long']	= $too_long;
			$field_length_details['min_length']	= $min_length;
			$field_length_details['max_length']	= $max_length;
			$field_length_details['value']		= $value;

			return $field_length_details;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function get_tab_module_details($account_group_code, $module_code)
	{
		try
		{
			$tab_module_where	= array('tab_module_code' => $module_code, 'ag_code' => $account_group_code);
			return $this->quick_add_model->get_record_details(array('*'), Portal_Model::PORTAL_TABLE_PRIA_TAB_MODULE, FALSE, $tab_module_where);
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

	//private function for system notification for APV
	private function _insert_qa_system_notif($ag_code = NULL, $apv_num = NULL, $user_id=NULL)
	{
		try{

			$prev_users = array();
			$roles 		= array();

			switch ($ag_code) {
				case AG_CONTRACT_GROWERS:
					$roles = array(ROLE_BC_ADMIN);
					break;

				case AG_INBOUND_NORMAL:
				case AG_OUTBOUND:
					$roles = array(ROLE_BC_FIN_PERS);
					break;

				case AG_INBOUND_CENTRAL:
				case AG_TOLL_PARTNERS:
					$roles = array(ROLE_PROD_FIN_PERS);
					break;
				
				case AG_FEEDMILL:
					$roles = array(ROLE_FEEDS_FIN_PERS);
					break;

				case AG_CONTRACTORS:
					$roles = array(ROLE_PURCH_PERS,ROLE_PROJ_ENG);
					break;

				case AG_LESSORS:
					$roles = array(ROLE_BC_HEAD);
					break;

				case AG_MANPOWER:
				case AG_GOODS_BAVI:
				case AG_GOODS_BFFI:
				case AG_GOODS_MARINADES:
					$roles = array(ROLE_PAY_FIN_PERS);
					break;
				
				default:
					# code...
					break;
			}
                
                /*if(!EMPTY($roles)){
	                foreach ($roles as $ra ) {
	                
		                $where = array('role_code' => $ra);
		                $users = $this->tm_model->get_users_role($where);

		                if($users){
		                	
		                    foreach ($users as $user):
		                        
		                        if(!in_array($user['user_id'], $prev_users)){
		                            $notify_who = array(
		                                'notification_icon'         => 'speaker_notes',
		                                'notification_mobile'       => NULL,
		                                'notify_users'              => array($user['user_id']),
		                                'notify_orgs'               => array(), //no default data
		                                'notify_roles'              => array(),
		                                'module_code'               => MODULE_PORTAL_IMPORT_REFERENCE_APV,
		                                'displayed_socket_flag'     => NO_FLAG,
		                                'listed_flag'               => YES_FLAG
		                            );

		                            $notify_who['notification_html'] = '';//base_url().PORTAL_TRANSACTIONS.$encode_link;
		                            
		                            $notification 	= "<font color='#e23b3b'>APV</font><font color='#8F44A9'> ".$apv_num." </font><font color='#000000'> has been uploaded</font>";

		                            $this->notify->insert_notification($notification, $notify_who, $user_id);
		                        }
		                        $prev_users[] = $user['user_id'];
		                    endforeach;
		                }
	            	}
            	}*/

            return $roles;
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