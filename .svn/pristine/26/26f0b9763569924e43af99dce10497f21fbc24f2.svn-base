<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sites extends Portal_Controller 
{
	private $module;
	private $module_folder;
	private $controller;
	private $module_js;

	private $permission_view;
	private $permission_add;
	private $permission_edit;
	private $permission_delete;

	protected $model_name = 'Site_model';

	private $site_types_req_cost_center = [SITE_TYPE_WAREHOUSE, SITE_TYPE_COST_CENTER, SITE_TYPE_STORE, SITE_TYPE_DRESSING_PLANT];
	
	public function __construct()
	{
		parent::__construct();

		$this->module 	  	 = MODULE_PORTAL_SITES;
		$this->module_folder = PORTAL_CODE_LIBRARIES;
		$this->controller 	 = strtolower(__CLASS__);
		
		$this->load->model($this->module_folder. '/Site_model', 'site_model');
		$this->load->model($this->module_folder. '/vendor_model', 'vm_model');

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

			$common_resource          				= $this->get_common_resources(MODULE_PORTAL_SITES);	

			$this->module_js  	 					= HMVC_FOLDER."/".SYSTEM_PORTAL."/".$this->module_folder."/sites";

			$resources['load_css']    				= array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER));
			$resources['load_js']     				= array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js));
			$resources['loaded_init'] 				= $common_resource['init'];
			
			$modal = array(
				'modal_add_site' => array(
					'title'			=> 'Add Site',
					'size'			=> 'md-h sm-w',
					'module'		=> PORTAL_CODE_LIBRARIES,
					'controller' 	=> PORTAL_SITES,
					'method'		=> 'modal_add_site',
					//'multi_save'	=> TRUE
				),
			   'modal_edit_site' => array(
						'title'			=> 'Edit Site',
						'size'			=> 'md-h sm-w',
						'module'		=> PORTAL_CODE_LIBRARIES,
						'controller' 	=> PORTAL_SITES,
						'method'		=> 'modal_edit_site'
				)
			);

			$resources['load_materialize_modal'] 	= array_merge($common_resource['modal'], $modal);

			$table_options = array(
					'table_id'		  => 'tbl_sites',
					'path'			  => $this->module_folder.'/sites/get_site_list',
					'advanced_filter' => TRUE
			);

			$resources['datatable']	= $table_options;

			$options_encoded 			= json_encode($table_options);
			$resources['loaded_init'] 	= array_merge($common_resource['init'], array("Sites.initialize('".$options_encoded."')"));

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

		$this->template->load('site', $data, $resources, Portal_Controller::$system);
	}

	public function modal_add_site($hash_id, $salt, $token, $security_action)
	{
		$this->_modal($hash_id, $salt, $token, $security_action);
	}


	public function modal_edit_site($hash_id, $salt, $token, $security_action)
	{
		$this->_modal($hash_id, $salt, $token, $security_action);
	}

	private function _modal($hash_id, $salt, $token, $security_action)
	{
		try
		{
			$data 			 = array();
			$resources 		 = array();
			$vendors 		 = array();
			$security 		 = "";
			$active			 = "checked";
			$where_site_type = ['display_in_code_lib' => INITIAL_YES];
			
			
			
			$data['business_centers'] = get_organizations_by_org_type_w_scope($this->module_code);

			$data['business_centers'] 	= $this->site_model->get_organizations(['org_type_code' => ORG_TYPE_BUSINESS_CENTER]);
			
			switch($security_action)
			{
				case $this->security_action_add:
					if($this->permission_add === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_add'));

					$where_site_type['display_in_add_code_lib'] = INITIAL_YES;
				break;

				case $this->security_action_edit:
					if($this->permission_edit === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_edit'));

					$key 			= $this->get_hash_key('site_id');
					$where			= array();
					$where[$key]	= $hash_id;

					$info 			= $this->site_model->get_specific_site($where);

					$site_id		= $info['site_id'];

					if(empty($site_id) )
						throw new Exception($this->lang->line('err_unauthorized_edit'));

					$site_details 	= $this->site_model->get_site_details_by_site_id($site_id);
					$data['site_details']  = $site_details;
					
					$ag_code 		= [];

					if($site_details['site_type_code'] == SITE_TYPE_FARM)
						$ag_code[] = AG_CONTRACT_GROWERS;	
					else
						$ag_code[] = AG_LESSORS;	

					$vendors  		= $this->vm_model->get_vendor_by_ag_code_n_org_code($ag_code, [$site_details['org_code']]);

					$data['info']	= $info;
					$active			= !empty($info['deleted_flag']) ? "" : "checked";
				break;

				default:
					throw new Exception($this->lang->line('err_unauthorized_access'));
				break;
			}

			$this->module_js  	 	    = HMVC_FOLDER."/".SYSTEM_PORTAL."/".$this->module_folder."/sites";

			$resources['load_css']	   	= array(CSS_SELECTIZE);
			$resources['load_js']	    = array(JS_SELECTIZE, $this->module_js);
			$resources['loaded_init'] 	= array("Sites.save();");

			$salt		      			= gen_salt();
			$token		      			= in_salt($hash_id . '/' . $security_action, $salt);
			$security	      			= $hash_id . '/' . $salt . '/' . $token . '/' . $security_action;

			$data['site_types'] 		= $this->site_model->get_param_site_types($where_site_type);
			$data['vendors']  			= $vendors; 
			$data['active']	  			= $active;
			$data['security'] 			= $security;

			$this->load->view('modals/sites', $data);
			$this->load_resources->get_resource($resources);
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

		
	}

	public function get_site_list()
	{
		$flag 		= $total_records = $display_records = 0;
		$table_data = array();

		try
		{
			$params	= get_params();

			$total_records		= $this->site_model->get_site_list();
			$records_info 		= $this->site_model->get_site_list($params);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];
			
			foreach($records as $records)
			{
				$hash_id	= $this->hash($records['site_id']);
				$salt		= gen_salt();

				$actions	= "";
				if(!EMPTY($records['site_code']) AND $this->permission_edit)
				{
					$token	= in_salt($hash_id . '/' . $this->security_action_edit, $salt);
					$url	= $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_edit;

					$actions.= "<a class='tooltipped' data-tooltip='Edit' href='#modal_edit_site' onclick=\"modal_edit_site_init('".$url."')\"><i class='material-icons'>mode_edit</i></a>";
				}

				if(!EMPTY($records['site_code']) AND $this->permission_delete)
				{
					$token	= in_salt($hash_id . '/' . $this->security_action_delete, $salt);
					$url	= $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_delete;

					$onclick = 'content_delete("site", "'.$url.'")';
					$actions.= "<a href='javascript:;' onclick='".$onclick."' class='tooltipped' data-tooltip='Delete' data-position='bottom' data-delay='50'><i class='material-icons'>delete</i></a>";
				}
					
				$table_data[] = array(
						$records['site_code'],
						$records['site_type_name'],
						$records['official_store_name'],						
						$records['cost_center_code'],						
						date('M d, Y', strtotime($records['created_date'])),
						// $records['status'],
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

			$params				= get_params();

			$validated_data 	= $this->_validate_form($params);

			$where				= array('site_id' => base64_url_decode($params['site_id']));

			$old_info 			= $this->site_model->get_specific_site($where);

			$site_id			= $old_info['site_id'];
			$curr_datetime  	= date(FORMAT_DB_DATETIME);

			Portal_Model::beginTransaction();
			
			switch($params['security_action'])
			{
				case $this->security_action_add:
			
					if($this->permission_add === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_add'));

					$fields = [
						'site_code'           => $validated_data['site_code'],
						'site_type_code'      => $validated_data['site_type_code'],
						'org_code'     		  => ( ! EMPTY($validated_data['business_center']) ) ? $validated_data['business_center'] : NULL,
						'status_code'		  => PARAM_STATUS_COMPLETED,
						'official_store_name' => $validated_data['official_store_name'],
						'cost_center_code' 	  => ( ! EMPTY($validated_data['cost_center_code']) ) ? $validated_data['cost_center_code'] : NULL,
						"created_by"		  => $this->session->userdata('user_id'),
						"created_date"		  => $curr_datetime
					];

					$site_id = $this->site_model->insert_site($fields);

					if($validated_data['site_type_code'] != SITE_TYPE_COST_CENTER)
					{
						$this->_insert_vendor_sites($validated_data['vendor_code'], $validated_data['site_code']);
					}	

					$audit_action[]	= AUDIT_INSERT;
					$audit_table[]	= Portal_Model::PORTAL_TABLE_SITES;
					$audit_schema[]	= DB_PORTAL;
					$prev_detail[]	= array();
					$curr_detail[]	= array($fields);
					$activity		= $validated_data["official_store_name"] . " has been added in the system.";

					$msg  = $this->lang->line('data_saved');
				break;

				case $this->security_action_edit:

					if($this->permission_edit === FALSE)
						throw new Exception($this->lang->line('err_unauthorized_add'));

					$fields = [
						'site_code'           => $validated_data['site_code'],
						'site_type_code'      => $validated_data['site_type_code'],
						'org_code'     		  => ( ! EMPTY($validated_data['business_center']) ) ? $validated_data['business_center'] : NULL,
						'status_code'		  => PARAM_STATUS_COMPLETED,
						'official_store_name' => $validated_data['official_store_name'],
						'cost_center_code' 	  => ( ! EMPTY($validated_data['cost_center_code']) ) ? $validated_data['cost_center_code'] : NULL,
						"modified_by"		  => $this->session->userdata('user_id'),
						"modified_date"		  => $curr_datetime
					];
					
					$this->site_model->update_site($fields, array('site_id' => $site_id));

					if($old_info['site_type_code'] != SITE_TYPE_COST_CENTER)
						$this->vm_model->delete_vendor_sites(['site_code' => $validated_data['site_code']]);

					if($validated_data['site_type_code'] != SITE_TYPE_COST_CENTER)
						$this->_insert_vendor_sites($validated_data['vendor_code'], $validated_data['site_code']);
					
					//$this->vm_model->update_vendor_sites(['vendor_code' => $validated_data['vendor_code']], ['site_code' => $validated_data['site_code']]);

					$audit_action[]	= AUDIT_UPDATE;
					$audit_table[]	= Portal_Model::PORTAL_TABLE_SITES;
					$audit_schema[]	= DB_PORTAL;
					$prev_detail[]	= array($old_info);
					$curr_detail[]	= array($fields); 
					$activity		= $validated_data["official_store_name"] . " has been updated in the system.";
					$msg  = $this->lang->line('data_updated');
				break;

				default:
					throw new Exception($this->lang->line('err_unauthorized_access'));
				break;
			}

			$this->audit_trail->log_audit_trail($activity, $this->module, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

			$flag 	= 1;
			$status = SUCCESS;

			$table_options = array(
					'table_id'		  => 'tbl_sites',
					'path'			  => $this->module_folder.'/sites/get_site_list',
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


	private function _insert_vendor_sites($vendors, $site_code)
	{
		try
		{
			foreach($vendors as $v)
			{	
				$params = ['vendor_code' => $v];

				$validation['vendor_code']  = [
					'data_type'   => 'db_value',
					'name'        => 'Vendor',
					'field'       => 'COUNT( 1 ) as check_row',
					'check_field' => 'check_row',
					'where'       => 'vendor_code',
					'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_VENDORS
				];

				$valid_data =  $this->validate_inputs($params, $validation);

				$this->vm_model->insert_vendor_sites([
					'site_code' 	=> $site_code,
					'vendor_code'	=> $v
				]);		
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
	
	

	private function _validate_form(&$params)
	{
		try
		{
			$this->validate_security($params);

			$required = [
				'site_code' 			=> 'Site Code',
				'site_type_code' 		=> 'Site Type',
				'business_center' 		=> 'Business Center',
				'vendor_code'			=> 'Vendor',
				'official_store_name' 	=> 'Official Store Name'
			];
			
			if( in_array($params['site_type_code'], $this->site_types_req_cost_center))
				$required['cost_center_code'] = 'Cost Center Code';

			if($params['site_type_code'] == SITE_TYPE_COST_CENTER)
			{
				// unset($required['business_center']);
				
				unset($required['vendor_code']);
			}

			if($params['site_type_code'] == SITE_TYPE_OFFICE)
			{
				unset($required['vendor_code']);
			}

			$this->check_required_fields($params, $required);
					
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
			$valid_data = array();

			if($params['site_type_code'] != SITE_TYPE_COST_CENTER)
			{
				/* $validation['vendor_code']  = [
					'data_type'   => 'db_value',
					'name'        => 'Vendor',
					'field'       => 'COUNT( 1 ) as check_row',
					'check_field' => 'check_row',
					'where'       => 'vendor_code',
					'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_VENDORS
				]; */

				$valid_data['vendor_code'] 		= $params['vendor_code'];
			}

			$validation['business_center']  = [
				'data_type'   => 'db_value',
				'name'        => 'Recommended Contractor',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'org_code',
				'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_ORGANIZATIONS
			];

			$validation['site_type_code']  = [
				'data_type'   => 'db_value',
				'name'        => 'Site Type',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'site_type_code',
				'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PARAM_SITE_TYPES
			];

			$validation['official_store_name'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Site  Name',
			);

			$validation['site_code'] 	= array(
				'data_type'	=> 'string',
				'name'		=> 'Site Code',
			);

			if( ! EMPTY($params['cost_center_code']))
			{
				$validation['cost_center_code']  = [
					'data_type'	=> 'string',
					'name'		=> 'Cost Center Code',
				];
			}
			
			$res =  $this->validate_inputs($params, $validation);

			return array_merge($valid_data, $res);
		}
		catch ( Exception $e )
		{
			throw $e;
		}
	}

	public function delete_site()
	{
		try
		{
			$status 	= ERROR;
			$params		= get_params();

			$params['security']	= $params['param_1'];

			$this->validate_security($params);
			
			$key 			= $this->get_hash_key('site_id');

			$where			= array();
			$where[$key]	= $params['hash_id'];
			
			$old_info 		= $this->site_model->get_specific_site($where);

			$site_id		= $old_info['site_id'];

			IF($this->permission_delete === TRUE){
				if(empty($site_id))
					throw new Exception($this->lang->line('err_unauthorized_access'));

				Portal_Model::beginTransaction();

				// KPOYAOAN 2021-05-04
				// change to permanent delete
				/*$fields = array(
		          "deleted_flag" 	=> ACTIVE_FLAG,
		          "status_code" 	=> SITE_STATUS_DELETED
		        );

				//$this->vendor_model->delete_vendor_account_group($vendor_code);
        		$this->site_model->update_site($fields, array('site_id' => $site_id));*/

        		// KPOYAOAN 2021-05-04 Delete permanently
        		$this->vm_model->delete_vendor_sites(['site_code' => $old_info['site_code']]);
        		$this->site_model->delete_site($site_id);

				$audit_action[]	= AUDIT_DELETE;
				$audit_table[]	= Portal_Model::PORTAL_TABLE_SITES;
				$audit_schema[]	= DB_PORTAL;
				$prev_detail[]	= array($old_info);
				$curr_detail[]	= array();
				$activity		= $old_info['official_store_name'] . " has been deleted.";

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
				'table_id'		  => 'tbl_sites',
				'path'			  => $this->module_folder.'/sites/get_site_list',
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

		$info = $this->site_model->get_specific_site(array('TRIM(UPPER(official_store_name))' => trim(strtoupper($value))));
			
		if( ! empty($info['official_store_name']))
			throw new Exception(sprintf($this->lang->line('duplicate_data'), $value));

	}

	public function get_site_vendors()
	{
		try
		{
			$flag 	= ERROR;
			$params = get_params();
			$items  = [];

			if($params['site_type_code'] == SITE_TYPE_FARM)
				$ag_code[] = AG_CONTRACT_GROWERS;	
			else
				$ag_code[] = AG_LESSORS;	

			/* $orgs 	   = get_organizations_by_org_type_w_scope($this->module_code);
			$org_codes = array_column($orgs, 'org_code');
 */
			$vendors  = $this->vm_model->get_vendor_by_ag_code_n_org_code($ag_code, [$params['org_code']]);

			foreach($vendors as $val)
				$items[] = ['text' => $val['vendor_name'], 'value' => $val['vendor_code']];

			$flag = SUCCESS;	
		}
		catch(PDOException $e)
		{
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode([
			'flag'		=> $flag,
			'msg' 		=> $msg,
			'options' 	=> $items
		]);
	}
}