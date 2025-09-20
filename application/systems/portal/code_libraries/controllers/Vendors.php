<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vendors extends Portal_Controller 
{
	private $module;
	private $module_folder;
	private $controller;
	private $module_js;

	private $permission_view;
	private $permission_add;
	private $permission_edit;
	private $permission_delete;

	protected $model_name = 'Vendor_model';
	
	public function __construct()
	{
		parent::__construct();

		$this->module 	  	 = MODULE_PORTAL_VENDORS;
		$this->module_folder = PORTAL_CODE_LIBRARIES;
		$this->controller 	 = strtolower(__CLASS__);
		
		$this->load->model($this->module_folder. '/Vendor_model', 'vendor_model');

		try{
			$this->permission_view		= $this->permission->check_permission($this->module, ACTION_VIEW);
			$this->permission_add		= $this->permission->check_permission($this->module, ACTION_ADD);
			$this->permission_edit		= $this->permission->check_permission($this->module, ACTION_EDIT);
			$this->permission_delete	= $this->permission->check_permission($this->module, ACTION_DELETE);
		}
		catch (PDOException $e)
		{
			$this->is_construct_error	= TRUE;
			$this->construct_error_msg	= $this->get_user_message($e);
		}
		catch (Exception $e)
		{
			$this->is_construct_error	= TRUE;
			$this->construct_error_msg	= $e->getMessage();
		}

		$hash_module = $this->hash($this->module);

		$this->security_action_add		= $hash_module . $this->hash(ACTION_ADD);
		$this->security_action_edit		= $hash_module . $this->hash(ACTION_EDIT);
		$this->security_action_delete	= $hash_module . $this->hash(ACTION_DELETE);
		$this->security_action_view		= $hash_module . $this->hash(ACTION_VIEW);

	}

	public function index()
	{
		try
		{

			$data 			= array();
			$resources		= array();

			$common_resource          				= $this->get_common_resources(MODULE_PORTAL_VENDORS);	

			$this->module_js  	 					= HMVC_FOLDER."/".SYSTEM_PORTAL."/".$this->module_folder."/vendors";

			$resources['load_css']    				= array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER));
			$resources['load_js']     				= array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js));
			$resources['loaded_init'] 				= $common_resource['init'];
			
			$modal = array(
						'modal_add_vendor' => array(
							'title'			=> 'Add Vendor',
							'size'			=> 'lg-h md-w',
							'module'		=> PORTAL_CODE_LIBRARIES,
							'controller' 	=> PORTAL_VENDORS,
							'method'		=> 'modal_add_vendor',
							'multi_save'	=> TRUE
						),
					   'modal_edit_vendor' => array(
								'title'			=> 'Edit Vendor',
								'size'			=> 'lg-h md-w',
								'module'		=> PORTAL_CODE_LIBRARIES,
								'controller' 	=> PORTAL_VENDORS,
								'method'		=> 'modal_edit_vendor',
						)
					);

			$resources['load_materialize_modal'] 	= array_merge($common_resource['modal'], $modal);

			$table_options = array(
					'table_id'		  => 'tbl_vendors',
					'path'			  => $this->module_folder.'/vendors/get_vendor_list',
					'advanced_filter' => TRUE
			);

			$resources['datatable']	= $table_options;


			$options_encoded 			= json_encode($table_options);
			$resources['loaded_init'] 	= array_merge($common_resource['init'], array("Vendors.initialize('".$options_encoded."')"));

			$hash_id	= $this->hash(0);
			$salt		= gen_salt();
			$token		= in_salt($hash_id . '/' . $this->security_action_add, $salt);

			$security	= $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_add;
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}

		$data['security']		= $security;
		$data['permission_add']	= $this->permission_add;

		$this->template->load('vendor', $data, $resources, Portal_Controller::$system);
	}

	public function modal_add_vendor($hash_id, $salt, $token, $security_action)
	{
		$this->_modal($hash_id, $salt, $token, $security_action);
	}

	public function modal_edit_vendor($hash_id, $salt, $token, $security_action)
	{
		$this->_modal($hash_id, $salt, $token, $security_action);
	}

	private function _modal($hash_id, $salt, $token, $security_action)
	{
		try
		{
			$data 		= array();
			$resources 	= array();

			$security 		= "";
			$active			= "checked";
			$key 			= $this->get_hash_key('vendor_code');
			$where			= array();
			$where[$key]	= $hash_id;

			$info 			= $this->vendor_model->get_specific_vendor($where);
			
			$vendor_code	= $info['vendor_code'];

			switch($security_action)
			{
				case $this->security_action_add:
					if( ! empty($vendor_code) )
						throw new Exception($this->lang->line('err_unauthorized_add'));

					if($this->permission_add === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_add'));
				
				break;

				case $this->security_action_edit:

					if(empty($vendor_code) )
						throw new Exception($this->lang->line('err_unauthorized_edit'));

					if($this->permission_edit === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_edit'));

						$data['info']	= $info;
						$active			= !empty($info['deleted_flag']) ? "" : "checked";

					$where = array('vendor_code' => $vendor_code);
					$vendor_account_group = $this->vendor_model->get_vendor_account_groups($where);
					$data['vendor_account_group'] = array_column($vendor_account_group, 'account_group_code');

					$vendor_business_centers = $this->vendor_model->get_vendor_business_centers($where);
					$data['vendor_business_centers'] = array_column($vendor_business_centers, 'org_code');
					
					// $cost_center_group = $this->vendor_model->get_cost_center_groups($where);
					// $data['cost_center_group'] = array_column($cost_center_group, 'cost_center_code');

				break;

				default:
					throw new Exception($this->lang->line('err_unauthorized_access'));
				break;
			}

			$this->module_js  	 	= HMVC_FOLDER."/".SYSTEM_PORTAL."/".$this->module_folder."/vendors";
			$resources['load_css']	= array(CSS_SELECTIZE);
			$resources['load_js']	= array(JS_SELECTIZE, $this->module_js);
			$resources['loaded_init'] = array(
					"Vendors.save();",
					"Vendors.location();",
					"Vendors.setDropDownValuesAjax();"
			);

			$data['regions'] 		= $this->vendor_model->get_param_regions();
			$data['orgs']			= $this->vendor_model->get_all_organizations();

			$where = array('dashboard_flag' => ENUM_YES);
			$data['account_groups']	= $this->vendor_model->get_account_groups($where);

			$where = array('org_type_code' => ORG_TYPE_BUSINESS_CENTER);
			$data['business_centers']	= $this->vendor_model->get_business_centers($where);
			 
			/* Get provinces base on the region picked */
			$where = array('region_code' => $info['region_code']);
			$data['provinces']		 = $this->vendor_model->get_param_provinces($where);

			/* Get municities base on the province picked */
			$where  = array('province_code' => $info['province_code']);
		
			$fields = array('CONCAT(region_code, "-",province_code, "-", district_code, "-", muni_city_code) as id', 'muni_city_name as name' );
			$data['municities'] = $this->vendor_model->get_param_muni_cities($where, $fields);
		
			/* Get barangays base on the municity picked */
			$where = array
			(
				'CONCAT(region_code, "-",province_code, "-", district_code, "-", muni_city_code)' => $info['muni_city_code']
			);

			$data['barangays']		 = $this->vendor_model->get_param_barangays($where);
			
			$salt		= gen_salt();
			$token		= in_salt($hash_id . '/' . $security_action, $salt);

			$security	= $hash_id . '/' . $salt . '/' . $token . '/' . $security_action;

			$modal_page	= 'modals/vendors';
		}
		catch(PDOException $e)
		{
			$msg = $this->get_user_message($e);

			$this->error_modal($msg);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);

			$this->error_modal($msg);
		}

		$data['active']		= $active;
		$data['security']	= $security;

		$this->load->view($modal_page, $data);
		$this->load_resources->get_resource($resources);
	}

	public function get_vendor_list()
	{
		$flag 		= $total_records = $display_records = 0;
		$table_data = array();

		try
		{
			$params	= get_params();

			$total_records		= $this->vendor_model->get_vendor_list();
			$records_info 		= $this->vendor_model->get_vendor_list($params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];
			
			foreach($records as $records)
			{
				$hash_id	= $this->hash($records['vendor_code']);
				$salt		= gen_salt();

				$actions	= "";
				if($this->permission_edit)
				{
					$token	= in_salt($hash_id . '/' . $this->security_action_edit, $salt);
					$url	= $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_edit;

					$actions.= "<a class='tooltipped' data-tooltip='Edit' href='#modal_edit_vendor' onclick=\"modal_edit_vendor_init('".$url."')\"><i class='material-icons'>mode_edit</i></a>";
				}

				if($this->permission_delete)
				{
					$token	= in_salt($hash_id . '/' . $this->security_action_delete, $salt);
					$url	= $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_delete;

					$onclick = 'content_delete("vendor", "'.$url.'")';
					$actions.= "<a href='javascript:;' onclick='".$onclick."' class='tooltipped' data-tooltip='Delete' data-position='bottom' data-delay='50'><i class='material-icons'>delete</i></a>";
				}
					
				$table_data[] = array(
						$records['vendor_code'],
						$records['vendor_name'],
						$records['account_group'],
						$records['business_center'],
						date('M d, Y', strtotime($records['created_date'])),
						"<div class='table-actions'>" . $actions . "</div>"
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
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
				)
			);

	}

	public function process()
	{
		try 
		{
			$status 			= ERROR;
			$msg				= "";
			$flag 				= 0;
			$options_encoded 	= "";

			$params	= get_params();

			$validated_data = $this->_validate_form($params);
			
			$key 			= $this->get_hash_key('vendor_code');

			$where			= array();
			$where[$key]	= $this->hash($params['vendor_code']);

			$old_info 		= $this->vendor_model->get_specific_vendor($where);
			$vendor_code	= $old_info['vendor_code'];

			if(!EMPTY($vendor_code) AND !EMPTY($params['account_groups']))
				$this->_save_account_groups($vendor_code, $params['account_groups']);

			// if(!EMPTY($vendor_code) AND !EMPTY($params['cost_center_code']))
			// 	$this->_save_cost_center($vendor_code, $params['cost_center_code']);

			Portal_Model::beginTransaction();
			
			switch($params['security_action'])
			{
				case $this->security_action_add:
				
				if($this->permission_add === FALSE)
					throw new Exception($this->lang->line('err_unauthorized_add'));

					$this->_check_unique($validated_data["vendor_name"]);

					$fields	= array(
						"vendor_code"		=> $validated_data["vendor_code"],
						"vendor_name" 		=> $validated_data["vendor_name"],
						//"vendor_type" 		=> $validated_data["vendor_type"],
						//"org_code" 			=> $validated_data["business_center"],
						"description" 		=> ISSET($validated_data["description"]) ? $validated_data["description"] : NULL ,
						"bldg_st" 			=> (ISSET($validated_data["bldg_st"]) AND !EMPTY($validated_data["bldg_st"]))? $validated_data["bldg_st"]: NULL,
						"region_code" 		=> (ISSET($validated_data["region_code"]) AND !EMPTY($validated_data["region_code"]))? $validated_data["region_code"]: NULL,
						"province_code" 	=> (ISSET($validated_data["province_code"]) AND !EMPTY($validated_data["province_code"]))? $validated_data["province_code"]: NULL,
						"muni_city_code" 	=> (ISSET($validated_data["muni_city_code"]) AND !EMPTY($validated_data["muni_city_code"]))? $validated_data["muni_city_code"]: NULL,
						"district_code" 	=> (ISSET($validated_data['district_code']) AND !EMPTY($validated_data['district_code']))? $validated_data['district_code']: NULL,
						"barangay_code" 	=> (ISSET($validated_data["barangay_code"]) AND !EMPTY($validated_data["barangay_code"]))? $validated_data["barangay_code"]: NULL,
						
						//Starts
						"cont_lname" 		=> array(filter_var($validated_data["contact_last_name"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_fname" 		=> array(filter_var($validated_data["contact_first_name"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_mname" 		=> array(filter_var($validated_data["contact_middle_initial"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_mobile" 		=> array(filter_var($validated_data["contact_mobile_number"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_email" 		=> array(filter_var($validated_data["contact_email"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						//Ends

						"created_by"		=> $this->session->userdata('user_id'),
						"created_date"		=> date('Y-m-d H:i:s')
					);

					$vendor_code = $this->vendor_model->insert_vendor($fields);

					$this->_save_account_groups($validated_data["vendor_code"], $params['account_groups']);

					//$this->_save_cost_center($validated_data["vendor_code"], $params['cost_center_code']);
					$this->_save_business_center($validated_data["vendor_code"], $params['business_center']);

					$audit_action[]	= AUDIT_INSERT;
					$audit_table[]	= Portal_Model::PORTAL_TABLE_VENDORS;
					$audit_schema[]	= DB_PORTAL;
					$prev_detail[]	= array();
					$curr_detail[]	= array($fields);
					$activity		= $validated_data["vendor_name"] . " has been added in the system.";

					$msg  = $this->lang->line('data_saved');
					break;

				case $this->security_action_edit:

					if($this->permission_edit === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_add'));

					if($validated_data["vendor_name"] != $old_info['vendor_name'])
						$this->_check_unique($validated_data["vendor_name"]);

					$fields	= array(
						"vendor_code"		=> $validated_data["vendor_code"],
						"vendor_name" 		=> $validated_data["vendor_name"],
					//	"vendor_type" 		=> $validated_data["vendor_type"],
						//"org_code" 			=> $validated_data["business_center"],
						"description" 		=> ISSET($validated_data["description"]) ? $validated_data["description"] : NULL ,
						"bldg_st" 			=> (ISSET($validated_data["bldg_st"]) AND !EMPTY($validated_data["bldg_st"]))? $validated_data["bldg_st"]: NULL,
						"region_code" 		=> (ISSET($validated_data["region_code"]) AND !EMPTY($validated_data["region_code"]))? $validated_data["region_code"]: NULL,
						"province_code" 	=> (ISSET($validated_data["province_code"]) AND !EMPTY($validated_data["province_code"]))? $validated_data["province_code"]: NULL,
						"muni_city_code" 	=> (ISSET($validated_data["muni_city_code"]) AND !EMPTY($validated_data["muni_city_code"]))? $validated_data["muni_city_code"]: NULL,
						"district_code" 	=> (ISSET($validated_data['district_code']) AND !EMPTY($validated_data['district_code']))? $validated_data['district_code']: NULL,
						"barangay_code" 	=> (ISSET($validated_data["barangay_code"]) AND !EMPTY($validated_data["barangay_code"]))? $validated_data["barangay_code"]: NULL,

						//Starts
						"cont_lname" 		=> array(filter_var($validated_data["contact_last_name"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_fname" 		=> array(filter_var($validated_data["contact_first_name"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_mname" 		=> array(filter_var($validated_data["contact_middle_initial"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_mobile" 		=> array(filter_var($validated_data["contact_mobile_number"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						"cont_email" 		=> array(filter_var($validated_data["contact_email"], FILTER_SANITIZE_STRING), 'ENCRYPT'),
						//Ends

						"modified_by"		=> $this->session->userdata('user_id'),
						"modified_date"		=> date('Y-m-d H:i:s')
					);

					$this->vendor_model->update_vendor($fields, array('vendor_code' => $vendor_code));

					$this->_save_account_groups($validated_data["vendor_code"], $params['account_groups']);

					//$this->_save_cost_center($validated_data["vendor_code"], $params['cost_center_code']);
					$this->_save_business_center($validated_data["vendor_code"], $params['business_center']);

					$audit_action[]	= AUDIT_UPDATE;
					$audit_table[]	= Portal_Model::PORTAL_TABLE_VENDORS;
					$audit_schema[]	= DB_PORTAL;
					$prev_detail[]	= array($old_info);
					$curr_detail[]	= array($fields); 
					$activity		= $validated_data["vendor_name"] . " has been updated in the system.";
					$msg  = $this->lang->line('data_updated');
					break;

			default:
					throw new Exception($this->lang->line('err_unauthorized_access'));
					break;
			}

			//Added by Christian Sept 02,2019 
			//Starts
			$where = array(
				'vendor_code' => $validated_data['vendor_code']
			);

			$vendor_bc_info = $this->vendor_model->get_vendor_business_centers($where);
			$org_codes 		= array_column($vendor_bc_info,'org_code');
			
			$vendor_users_info 	= $this->vendor_model->get_vendor_user($where);
			$user_ids 			= array_column($vendor_users_info,'user_id');
			
			IF(!EMPTY($user_ids)){
				foreach ($user_ids as $user_id)
				{
					$this->_save_user_orgs($user_id, $org_codes);					
				}
			}
			//Ends

			$this->audit_trail->log_audit_trail($activity, $this->module, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

			$flag 	= 1;
			$status = SUCCESS;

			$table_options = array(
					'table_id'		  => 'tbl_vendors',
					'path'			  => $this->module_folder.'/vendors/get_vendor_list',
					'advanced_filter' => TRUE
			);
			$options_encoded = json_encode($table_options);


			Portal_Model::commit();
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

		echo json_encode(
			array(
				'status'	=> $status,
				'msg'		=> $msg,
				'action'	=> $params['btn_action'],
				'datatable'	=> $options_encoded
			)
		);
	}

	private function _validate_form(&$params)
	{
		try{

			$this->validate_security($params);

			$fields								= array();
			$fields['vendor_code'] 				= "Vendor Code";
			$fields['vendor_name'] 				= "Vendor Name";
			$fields['business_center'] 			= "Business Center";

			// $fields['bldg_st'] 					= $params['bldg_st'];
			// $fields['region_code'] 				= $params['region_code'];
			// $fields['province_code'] 			= $params['province_code'];
			// $fields['muni_city_code'] 			= $params['muni_city_code'];

			//Starts
			// $fields['contact_last_name'] 		= $params['contact_last_name'];
			// $fields['contact_first_name'] 		= $params['contact_first_name'];
			// $fields['contact_middle_initial'] 	= $params['contact_middle_initial'];
			// $fields['contact_mobile_number'] 	= $params['contact_mobile_number'];
			// $fields['contact_email'] 			= $params['contact_email'];
			//Ends

			/*if(!empty($params['district_code'])){
				$fields['district_code'] 		= $params['district_code'];
			}*/

			// $fields['barangay_code'] 			= $params['barangay_code'];

			$this->check_required_fields($params, $fields);
					
			return $this->_validate_inputs($params);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private function _validate_inputs($params)
	{
		try
		{
			/* Validate constraints */
			$validation	= array();

			$validation['vendor_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Vendor Code',
				'min_len'	=> 1,
				'max_len' 	=> 45
			);

			$validation['vendor_name'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Vendor Name',
				'min_len'	=> 1,
				'max_len' 	=> 100
			);

			$validation['business_center'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Business Center',
				'min_len'	=> 1,
				'max_len' 	=> 100
			);

		/* 	$validation['vendor_type'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Vendor Type',
				'min_len'	=> 1,
				'max_len' 	=> 100
			);
 */
			/*$validation['cost_center_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Cost Center',
				'min_len'	=> 1,
				'max_len' 	=> 45
			);*/

			$validation['business_center_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Business Center',
				'min_len'	=> 1,
				'max_len' 	=> 500
			);

			if(!empty($params['description'])){
				$validation['description'] 	= array(
					'data_type'	=> 'string',
					'name'		=> 'Description',
					'min_len'	=> 1,
					'max_len' 	=> 100
				);
			}

			$validation['bldg_st'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Building Street',
				// 'min_len'	=> 1,
				'max_len' 	=> 100
			);

			$validation['region_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Region',
				// 'min_len'	=> 1,
				'max_len' 	=> 20
			);

			$validation['province_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Province',
				// 'min_len'	=> 1,
				'max_len' 	=> 20
			);

			$validation['muni_city_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Municipality/City',
				// 'min_len'	=> 1,
				'max_len' 	=> 20
			);

			if(!empty($params['district_code'])){
				$validation['district_code'] 	= array(
					'data_type'	=> 'string',
					'name'		=> 'District',
					// 'min_len'	=> 1,
					'max_len' 	=> 20
				);
			}

			$validation['barangay_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Barangay',
				// 'min_len'	=> 1,
				'max_len' 	=> 20
			);

			$validation['contact_last_name'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Contact Last Name'
			);

			$validation['contact_first_name'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Contact First Name'
			);

			$validation['contact_middle_initial'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Contact Middle Name'
			);

			$validation['contact_mobile_number'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Contact Mobile Number'
			);

			$validation['contact_email'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Contact Email'
			);

			return $this->validate_inputs($params, $validation);
		}
		catch ( Exception $e )
		{
			throw $e;
		}
	}

	public function delete_vendor()
	{
		try
		{
			$status 	= ERROR;
			$params		= get_params();

			$params['security']	= $params['param_1'];

			$this->validate_security($params);
			
			$key 			= $this->get_hash_key('vendor_code');

			$where			= array();
			$where[$key]	= $params['hash_id'];
			
			$old_info 		= $this->vendor_model->get_specific_vendor($where);

			$vendor_code	= $old_info['vendor_code'];

			IF($this->permission_delete === TRUE){
				if(empty($vendor_code))
					throw new Exception($this->lang->line('err_unauthorized_access'));

				Portal_Model::beginTransaction();

				$fields = array(
		          "deleted_flag" => ACTIVE_FLAG
		        );

				//$this->vendor_model->delete_vendor_account_group($vendor_code);

				// 2021-04-13 KPOYAOAN Remove delete flag implementation
        		// $this->vendor_model->update_vendor($fields, array('vendor_code' => $vendor_code));

        		// 2021-04-13 KPOYAOAN Permanently delete Vendor
        		$this->vendor_model->delete_vendor_sites(['vendor_code' => $vendor_code]);
        		$this->vendor_model->delete_vendor_account_group($vendor_code);
        		$this->vendor_model->delete_business_centers($vendor_code);
        		$this->vendor_model->delete_vendor_users($vendor_code);
        		$this->vendor_model->delete_vendor($vendor_code);

				$audit_action[]	= AUDIT_DELETE;
				$audit_table[]	= Portal_Model::PORTAL_TABLE_VENDORS;
				$audit_schema[]	= DB_PORTAL;
				$prev_detail[]	= array($old_info);
				$curr_detail[]	= array();
				$activity		= $old_info['vendor_name'] . " has been deleted.";

				$this->audit_trail->log_audit_trail($activity, $this->module, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

				Portal_Model::commit();

				$status = SUCCESS;
				$msg 	= $this->lang->line('data_deleted');
			}
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

		$table_options = array(
				'table_id'		  => 'tbl_vendors',
				'path'			  => $this->module_folder.'/vendors/get_vendor_list',
				'advanced_filter' => TRUE
		);

		$info = array(
				"status"			=> $status,
				"msg"				=> $msg,
				"reload"			=> 'datatable',
				"datatable_options" => $table_options
		);

		echo json_encode($info);
	}

	private function _check_unique($value)
	{

		$info = $this->vendor_model->get_specific_vendor(array('TRIM(UPPER(vendor_name))' => trim(strtoupper($value))));
			
		if( ! empty($info['vendor_name']))
			throw new Exception(sprintf($this->lang->line('duplicate_data'), $value));

	}

	public function get_options()
	{
		try
		{
			$options = array();
			$rows 	 = $this->_get_list_values();

			foreach($rows as $val)
			{
				$options[] = array('value' => $val['id'],  'text' => htmlspecialchars_decode($val['name'], ENT_QUOTES));	
			}
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);
		}

		echo json_encode($options, JSON_HEX_APOS | JSON_HEX_QUOT);
	}

	private function _get_list_values()
	{
		try
		{
			$params = get_params();
			
			$id 	= $params['id'];
			$type 	= $params['type'];

			/* Settings for each type */
			switch($type)
			{
				case 'province_code' 	:
						$func   = 'get_param_provinces';
						$where  = array('region_code' => $id);
						$fields = array('province_code as id', 'province_name as name' );
					break;
				case 'muni_city_code'	:
						$func   = 'get_param_muni_cities';
						$where  = array('province_code' => $id);
						$fields = array('CONCAT(region_code, "-",province_code, "-", district_code, "-", muni_city_code) as id', 'muni_city_name as name' );
					break;
				case 'barangay_code'	:
						
						$func   = 'get_param_barangays';
						$where  = array('CONCAT(region_code, "-",province_code, "-", district_code, "-", muni_city_code)' => $id);
						$fields = array('barangay_code as id', 'barangay_name as name' );
					break;
			}

			return $this->vendor_model->$func($where, $fields, ['name' => 'ASC']);
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


	private function _save_account_groups($vendor_code, $account_groups)
	{
		try{

			$this->vendor_model->delete_account_groups($vendor_code);

			foreach ($account_groups as $val) {
					
				$fields	= array(
					"vendor_code"			=> $vendor_code,
					"account_group_code" 	=> $val
				);

				$this->vendor_model->insert_account_groups($fields);
			}	

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}

	private function _save_cost_center($vendor_code, $cost_center_codes)
	{
		try{

			$this->vendor_model->delete_cost_centers($vendor_code);

			foreach ($cost_center_codes as $val) {
					
				$fields	= array(
					"vendor_code"			=> $vendor_code,
					"cost_center_code" 		=> $val
				);

				$this->vendor_model->insert_cost_centers($fields);
			}	

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}

	private function _save_business_center($vendor_code, $business_center_codes)
	{
		try{

			$this->vendor_model->delete_business_centers($vendor_code);

			foreach ($business_center_codes as $val) {
					
				$fields	= array(
					"vendor_code"	=> $vendor_code,
					"org_code" 		=> $val
				);

				$this->vendor_model->insert_business_centers($fields);
			}	

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}

	private function _save_user_orgs($user_id, $org_codes)
	{
		try{

			$this->vendor_model->delete_user_orgs($user_id);

			foreach ($org_codes as $val) {
					
				$fields	= array(
					"user_id"	=> $user_id,
					"org_code" 	=> $val
				);

				$this->vendor_model->insert_user_orgs($fields);
			}	

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}
}