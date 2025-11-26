<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
class Portal_Model extends Base_Model {

  	protected static $dsn		= DB_PORTAL;
	protected static $system    = SYSTEM_PORTAL;
	protected $initial_yes 		= INITIAL_YES;

	//PRIA CORE TABLES
	const CORE_TABLE_USERS 								= DB_CORE.'.users';
	const CORE_ROLES					            	= DB_CORE.'.roles';
	const CORE_USER_ROLES					            = DB_CORE.'.user_roles';
	const CORE_WORKFLOWS 					            = DB_CORE.'.workflows';
	const CORE_PARAM_TASK_ACTIONS 			     		= DB_CORE.'.param_task_actions';
	const CORE_WORKFLOW_STAGES 				       		= DB_CORE.'.workflow_stages';
	const CORE_WORKFLOW_STAGE_TASKS 		    		= DB_CORE.'.workflow_stage_tasks';
	const CORE_WORKFLOW_TASK_ACTIONS 		   			= DB_CORE.'.workflow_task_actions';
	const CORE_WORKFLOW_TASK_ROLES 			    		= DB_CORE.'.workflow_task_roles';
	const CORE_WORKFLOW_TASK_APPENDABLE		 			= DB_CORE.'.workflow_task_appendable';
	const CORE_WORKFLOW_TASK_PREDECESSORS 				= DB_CORE.'.workflow_task_predecessors';
	const CORE_WORKFLOW_TASK_WORKFLOW_RETURN			= DB_CORE.'.workflow_task_return';
	const CORE_MODULES									= DB_CORE.'.modules';
	const CORE_LOCATIONS                    			= DB_CORE.'.locations';
	const CORE_ORGANIZATIONS                    		= DB_CORE.'.organizations';
	const CORE_NOTIFICATIONS							= DB_CORE.'.notifications';
	const CORE_SYS_PARAM								= DB_CORE.'.sys_param';
	const CORE_TABLE_MODULE_SCOPE_ROLES					= DB_CORE.'.module_scope_roles';
	const CORE_MODULE_ACTION_ROLES                      = DB_CORE.'.module_action_roles';
	const CORE_MODULE_ACTIONS                           = DB_CORE.'.module_actions';
	const CORE_EMAIL_NOTIFICATION_QUEUES               	= DB_CORE.'.email_notification_queues';

	//PRIA TABLES
	const PORTAL_TABLE_BUSINESS_CENTER                  = 'business_center';
	const PORTAL_TABLE_CONTRACT_DOCUMENT_VERSIONS       = 'contract_document_versions';
	const PORTAL_TABLE_CONTRACT_DOCUMENTS               = 'contract_documents';
	const PORTAL_TABLE_CONTRACTS                        = 'contracts';
	const PORTAL_TABLE_CONTRACT_BILLING_DATES           = 'contract_billing_dates';
	const PORTAL_TABLE_DELIVERY_GOODS_DOCUMENT_VERSIONS = 'delivery_goods_document_versions';
	const PORTAL_TABLE_DELIVERY_GOODS_DOCUMENTS         = 'delivery_goods_documents';
	const PORTAL_TABLE_DELIVERY_GOODS_RECEIPT           = 'delivery_goods_receipt';
	const PORTAL_TABLE_DELIVERY_GOODS_REFERENCE         = 'pria_references';//'delivery_goods_reference';
	const PORTAL_TABLE_INTERNAL_ORDER_DOCUMENT_VERSIONS = 'internal_order_document_versions';
	const PORTAL_TABLE_INTERNAL_ORDER_DOCUMENTS         = 'internal_order_documents';
	const PORTAL_TABLE_INTERNAL_ORDERS                  = 'internal_orders';
	const PORTAL_TABLE_PARAM_ACCOUNT_GROUPS             = 'param_account_groups';
	const PORTAL_TABLE_PARAM_APV_STATUS                 = 'param_apv_status';
	const PORTAL_TABLE_PARAM_BARANGAYS                  = 'param_barangays';
	const PORTAL_TABLE_PARAM_CONTRACT_STATUS            = 'param_contract_status';
	const PORTAL_TABLE_PARAM_DELIVERY_STATUS            = 'param_delivery_status';
	const PORTAL_TABLE_PARAM_DOCUMENT_TYPES             = 'param_document_types';
	const PORTAL_TABLE_PARAM_DOCUMENT_TYPE_ACCOUNT_GROUP = 'param_document_type_account_group';
	const PORTAL_TABLE_PARAM_GL_ACCOUNTS                = 'param_gl_accounts';
	const PORTAL_TABLE_PARAM_MAIN_ACCOUNT_GROUPS        = 'param_main_account_groups';
	const PORTAL_TABLE_PARAM_MUNI_CITIES                = 'param_muni_cities';
	const PORTAL_TABLE_PARAM_PAYMENT_TERMS              = 'param_payment_terms';
	const PORTAL_TABLE_PARAM_PO_STATUS                  = 'param_po_status';
	const PORTAL_TABLE_PARAM_PROJECT_TYPES              = 'param_project_types';
	const PORTAL_TABLE_PARAM_PROVINCES                  = 'param_provinces';
	const PORTAL_TABLE_PARAM_PURCHASING_GROUP           = 'param_purchasing_group';
	const PORTAL_TABLE_PARAM_REGIONS                    = 'param_regions';
	const PORTAL_TABLE_PARAM_SITE_TYPES                 = 'param_site_types';
	const PORTAL_TABLE_PARAM_SOA_TYPES                  = 'param_soa_types';
	const PORTAL_TABLE_PARAM_TRANSMITTAL_STATUS         = 'param_transmittal_status';
	const PORTAL_TABLE_PARAM_DR_TYPES         			= 'param_dr_types';
	const PORTAL_TABLE_PAYMENTS							= 'payments';
	const PORTAL_TABLE_PAYMENT_APVS						= 'payment_apvs';
	const PORTAL_TABLE_PRIA_TASK_ACTIONS                = 'pria_task_actions';
	const PORTAL_TABLE_PRIA_TASK_ATTACHMENTS            = 'pria_task_attachments';
	const PORTAL_TABLE_PRIA_TASK_ATTACHMENT_VERSIONS    = 'pria_task_attachment_versions';
	const PORTAL_TABLE_PRIA_TASK_COMMENTS               = 'pria_task_comments';
	const PORTAL_TABLE_PRIA_TASK_EMAIL_LINKS			= 'pria_task_email_links';
	const PORTAL_TABLE_PRIA_TASK_NOTIFY                 = 'pria_task_notify';
	const PORTAL_TABLE_PRIA_TASK_PREDECESSORS           = 'pria_task_predecessors';
	const PORTAL_TABLE_PRIA_TASK_APPENDABLE          	= 'pria_task_appendable';
	const PORTAL_TABLE_PRIA_TASK_ROLES                  = 'pria_task_roles';
	const PORTAL_TABLE_PRIA_TASKS                       = 'pria_tasks';
	const PORTAL_TABLE_PRIA_WORKFLOW_STAGES             = 'pria_workflow_stages';
	const PORTAL_TABLE_PRIA_WORKFLOWS                   = 'pria_workflows';

	const PORTAL_TABLE_PRIA_WORKFLOWS_TASK_FORMS        = 'workflow_task_forms';
	const PORTAL_TABLE_PRIA_TASK_FORMS                  = 'pria_task_forms';

	const PORTAL_TABLE_PRIA_WORKFLOWS_TASK_DOCUMENT_TYPES = 'workflow_task_document_types';
	const PORTAL_TABLE_PRIA_WORKFLOWS_TASK_FILE_EXTENSIONS = 'workflow_task_file_extensions';
	const PORTAL_TABLE_PRIA_TASK_DOCUMENT_TYPES         = 'pria_task_document_types';
	const PORTAL_TABLE_PRIA_TASK_FILE_EXTENSIONS        = 'pria_task_file_extensions';

	const PORTAL_TABLE_PROJECT_DOCUMENT_VERSIONS        = 'project_document_versions';
	const PORTAL_TABLE_PROJECT_DOCUMENTS                = 'project_documents';
	const PORTAL_TABLE_PROJECTS                         = 'projects';
	const PORTAL_TABLE_PROJECT_TYPES                    = 'project_types';
	const PORTAL_TABLE_PURCHASE_ORDERS                  = 'purchase_orders';
	const PORTAL_TABLE_PURCHASE_ORDER_TYPES             = 'param_purchase_order_types';
	const PORTAL_TABLE_PURCHASE_REQUISITIONS            = 'purchase_requisitions';
	const PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS	= 'purchase_request_cost_centers';
	const PORTAL_TABLE_SITE_DOCUMENT_VERSIONS           = 'site_document_versions';
	const PORTAL_TABLE_SITE_DOCUMENTS                   = 'site_documents';
	const PORTAL_TABLE_SITES                            = 'sites';
	const PORTAL_TABLE_SOA                              = 'soa';
	const PORTAL_TABLE_SOA_DOCUMENT_VERSIONS            = 'soa_document_versions';
	const PORTAL_TABLE_SOA_DOCUMENTS                    = 'soa_documents';
	const PORTAL_TABLE_SOA_TRANSMITTAL                  = 'soa_transmittal';
	const PORTAL_TABLE_TEMP_APVS                        = 'temp_apvs';
	const PORTAL_TABLE_TEMP_DRS                         = 'temp_drs';
	const PORTAL_TABLE_TEMP_GRS                         = 'temp_grs';
	const PORTAL_TABLE_TEMP_IOS                         = 'temp_ios';
	const PORTAL_TABLE_TEMP_POS                         = 'temp_pos';
	const PORTAL_TABLE_TEMP_PRS                         = 'temp_prs';
	const PORTAL_TABLE_TEMP_SOAS                        = 'temp_soas';
	const PORTAL_TABLE_TRANSMITTALS                     = 'transmittals';
	const PORTAL_TABLE_DOCUMENT_TRANSMITTALS            = 'document_transmittals';
	const PORTAL_TABLE_VENDOR_ACCOUNT_GROUP             = 'vendor_account_group';
	const PORTAL_TABLE_COST_CENTERS             		= 'cost_centers';
	const PORTAL_TABLE_VENDORS                          = 'vendors';
	const PORTAL_TABLE_VENDOR_USERS                     = 'vendor_users';
	const PORTAL_TABLE_VENDOR_SITES                     = 'vendor_sites';
	const PORTAL_TABLE_USER_ORGS                      	= 'user_orgs';

	const PORTAL_TABLE_PRIA_TASK_RETURN					= 'pria_task_return';

	const PORTAL_TABLE_PRIA_OVERVIEW					= 'overview';
	const PORTAL_TABLE_PRIA_MAIL_NOTIFICATIONS			= 'pria_mail_notifications';
	const PORTAL_TABLE_PRIA_MAIL_NOTIFICATION_RECIPIENTS	= 'pria_mail_notification_recipients';
	const PORTAL_TABLE_PRIA_EMAIL_TEMPLATES				= 'pria_email_templates';

	const PORTAL_TABLE_DOCUMENT_VERSIONS       			= 'document_versions';
	const PORTAL_TABLE_DOCUMENTS               			= 'documents';

	const PORTAL_TABLE_ORGANIZATIONS               		= 'organizations';
	const PORTAL_TABLE_ORGANIZATION_TYPES               = 'param_organization_types';
	const PORTAL_TABLE_MODULE_ACCOUNT_GROUPS 			= 'module_account_group';
	const PORTAL_TABLE_PRIA_TAB_MODULE 					= 'pria_tab_modules';

	const PORTAL_TABLE_PRIA_BOQ 						= 'boq';
	const PORTAL_TABLE_PRIA_BOQ_PR 						= 'boq_pr';
	const PORTAL_TABLE_PRIA_BOQ_ASSET 					= 'boq_asset_codes';
	const PORTAL_TABLE_PRIA_PARAM_PR_ITEM_TYPES 		= 'param_pr_item_types';
	const PORTAL_TABLE_PURCHASE_REQUISITION_PROJECTS	= 'purchase_requisition_projects';

	const PORTAL_TABLE_WORKFLOW_TASK_UPDATES 			= 'workflow_task_updates';

	const PORTAL_TABLE_PRIA_PARAM_STATUSES 				= 'param_statuses';

	const PORTAL_TABLE_PRIA_PARAM_PROCESS_TYPES 					= 'param_process_types';
	const PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES 	= 'param_contactor_process_categories';
	const PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORY_MAP 	= 'param_contactor_process_category_map';
	const PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_CATEGORY_FILES 		= 'param_contractor_category_files';
	const PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS 					= 'vendor_business_centers';
	const PORTAL_TABLE_PRIA_VENDOR_BUSINESS_CENTERS 				= 'vendor_business_centers';

	const PORTAL_TABLE_PARAM_ACCOUNT_GROUP_MODULES					= 'param_account_group_modules';

	public function get_param_regions($where=array(), $fields=array(), $order=array())
	{
		try
		{
			$order  = EMPTY($order) ? array('region_sort' => 'ASC') : $order;
			$where  = array_merge( array('active_flag' => ACTIVE_FLAG), $where);

			$fields = EMPTY($fields) ? array('region_code', 'region_name', 'abbreviation') : $fields;


			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_REGIONS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_provinces($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			$order  = EMPTY($order) ? array('province_name' => 'ASC') : $order;

			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_PROVINCES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_muni_cities($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_MUNI_CITIES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_barangays($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_BARANGAYS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_max_batch($table_name = NULL)
	{
		try{

			$val = NULL;

			$query = <<<EOS
				SELECT
					MAX(batch_num) max_val
				FROM
					{$table_name}

EOS;
			$max_val = $this->query($query, $val, TRUE, FALSE);
			return $max_val;
		}catch(PDOException $e){
			throw $e;
		}
	}

	public function get_all_account_groups($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_payment_terms($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_PAYMENT_TERMS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_tab_module($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	{
		try
		{	
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_TAB_MODULE, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-05-21 09:50:00
	 * @Desc: Override the select_data in Base_Model.php, Added the $limit option.
	 * 		  Created for the infinite scroll
	 * @ReferencedBy:
	 */
	protected function select_data($fields_arr, $table, $multiple, $where_arr = array(), $order_arr = array(), $group_arr = array(), $limit = '')
	{
		try
		{
			$values 	= array();
			$where 		= '';
			$order_by 	= '';
			$group_by 	= '';


			$fields 	= implode(',' , $fields_arr);

			if( ! empty($where_arr))
			{
				// Construct where condition
				list($where_str, $where_val)	= $this->_construct_where_statement($where_arr);

				$where	= ' WHERE ' . $where_str;
				$values	= array_merge($values, $where_val);

			}

			if( ! empty($order_arr))
			{
				$order_by_arr = array();
				foreach ($order_arr as $a => $b)
					$order_by_arr[] = $a . ' ' . $b;

				$order_by = 'ORDER BY  '. implode(',',$order_by_arr);
			}

			if( ! empty($group_arr))
			{
				$group_by_arr = array();
				foreach ($group_arr as $grp)
					$group_by_arr[] = $grp;

				$group_by = 'GROUP BY  ' . implode(',', $group_by_arr);
			}



			$query =
			'
				SELECT ' . $fields . '
				FROM ' . $table . ' ' .
				$where . ' ' .
				$group_by . ' ' .
				$order_by . ' ' .
				$limit;


			RLog::info('FUNCTION select_data()');
			RLog::info('QUERY: ' . $query);
			RLog::info('VALUE: ' . var_export($values, TRUE));

			return $this->query($query, $values, TRUE, $multiple);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-05-21 09:52:01
	 * @Desc: Created for column indexed associative arrays
	 * @ReferencedBy:
	 */
	protected function select_query_group($query,array $val=array())
	{
		try
		{
			$db		= static::get_connection();
			$stmt	= $db->prepare($query);
			$stmt->execute($val);

			return $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_all_vendors($where=array(), $fields=array('*'), $order=array(), $multiple = TRUE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_VENDORS, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_vend($where=array(), $fields=array('*'), $order=array(), $multiple = TRUE)
	{
		try
		{

			$tbl_vendor 		= self::PORTAL_TABLE_VENDORS;
			$tbl_vendor_sites 	= self::PORTAL_TABLE_VENDOR_SITES;

			$query  =<<<EOS
				SELECT
					*
				FROM
					$tbl_vendor a
				JOIN
					$tbl_vendor_sites b ON a.vendor_code = b.vendor_code
				WHERE
					a.vendor_type = ? AND a.deleted_flag = ?
EOS;

			return $this->query($query, [VT_LESSOR, NOT_DEL_FLAG], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendor_by_org_code_arr($org_code, $vendor_code='', $field_arr=[])
	{
		try
		{
			$and 						= '';
			$values 				 	= [$org_code];
			$tbl_vendor 				= self::PORTAL_TABLE_VENDORS;
			$tbl_vendor_business_center = self::PORTAL_TABLE_PRIA_VENDOR_BUSINESS_CENTERS;

			if( EMPTY($field_arr) )
				$field_arr = ['a.vendor_code', 'a.vendor_name'];


			if( ! EMPTY($vendor_code))
			{
				$and 	  = <<<EOS
					AND a.vendor_code = ?
EOS;
				$values[] = $vendor_code;
			}

			$fields = implode(',', $field_arr);
			$query  =<<<EOS
				SELECT
					$fields
				FROM
					$tbl_vendor a
				JOIN
					$tbl_vendor_business_center b ON a.vendor_code = b.vendor_code
				WHERE
					b.org_code = ?
				$and
EOS;

			return $this->query($query, $values, TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_vendor_by_org_code_arr_and_ag_arr($ag_code, $org_code, $vendor_code='', $field_arr=[])
	{
		try
		{
			$and 						= '';
			$values 				 	= [$org_code];
			$tbl_vendor 				= self::PORTAL_TABLE_VENDORS;
			$tbl_vendor_business_center = self::PORTAL_TABLE_PRIA_VENDOR_BUSINESS_CENTERS;
			$tbl_vendor_account_group 	= self::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP;

			if( EMPTY($field_arr) )
				$field_arr = ['a.vendor_code', 'a.vendor_name'];


			$ag_codes 	 = implode('\',\'', $ag_code);


			if( ! EMPTY($vendor_code))
			{
				$and 	  = <<<EOS
					AND a.vendor_code = ?
EOS;
				$values[] = $vendor_code;
			}

			$fields = implode(',', $field_arr);
			$query  =<<<EOS
				SELECT
					$fields
				FROM
					$tbl_vendor a
				JOIN
					$tbl_vendor_business_center b ON a.vendor_code = b.vendor_code
				JOIN
					$tbl_vendor_account_group c ON a.vendor_code = c.vendor_code
				WHERE
					b.org_code = ?
				AND
					c.account_group_code IN ('$ag_codes')
				$and
				ORDER BY a.vendor_name
EOS;
			// return print_var_export($query, $values);
			return $this->query($query, $values, TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_all_vendor_ag($ag_code=NULL)
	{
		try
		{
			$and 	= '';
			$where 	= '';
			$tbl_vendors 				= self::PORTAL_TABLE_VENDORS;
			$tbl_vendor_account_group 	= self::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP;

			if($ag_code == AG_GOODS_BFFI OR $ag_code == AG_GOODS_BAVI OR $ag_code == AG_GOODS){
				$where 	= " WHERE b.account_group_code = '".AG_GOODS_BFFI."' OR b.account_group_code = '".AG_GOODS_BAVI."' OR b.account_group_code = '".AG_GOODS."'";
				$ag_code = array();
			}else{
				$where 	= " WHERE b.account_group_code = ? ";
				$ag_code = array($ag_code);
			}

			$query      =<<<EOS
                SELECT *
                	FROM $tbl_vendors a
				JOIN $tbl_vendor_account_group b
					ON a.vendor_code = b.vendor_code
				$where
EOS;


            return $this->query($query, $ag_code, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_business_centers($vendor_code = NULL, $ag_code = NULL, $org_type = NULL, $having = "")
	{
		try
		{
			$tbl_bc 					= self::PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS;
			$tbl_organization 			= self::PORTAL_TABLE_ORGANIZATIONS;
			$tbl_vendor_account_group	= self::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP;

			$where	= "";
			$values	= [];

			if(!EMPTY($vendor_code))
			{
				$where		.= " AND a.vendor_code = ?";
				$values[]	= $vendor_code;
			}

			if(!EMPTY($ag_code))
			{
				$where		.= " AND a.vendor_code IN (SELECT vendor_code FROM $tbl_vendor_account_group WHERE account_group_code = ?)";
				$values[]	= $ag_code;
			}

			if(!EMPTY($org_type))
			{
				$where		.= " AND b.org_type_code = ?";
				$values[]	= $org_type;
			}

			$query      =<<<EOS
                SELECT
                    a.org_code,
                    a.vendor_code,
                    a.org_code value,
                    b.name text
                FROM
                    $tbl_bc a
                JOIN $tbl_organization b ON a.org_code = b.org_code
                WHERE 1=1
					$where
				$having
				ORDER BY
					b.name ASC
EOS;

            return $this->query($query, $values, TRUE);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_pr_ref($ag_codes, $ag_last_core_task_ids = [])
	{
		try
		{
			$values = array();

            $pr							= self::PORTAL_TABLE_PURCHASE_REQUISITIONS;

            $tbl_pria_workflows			= self::PORTAL_TABLE_PRIA_WORKFLOWS;
            $tbl_pria_workflow_stages	= self::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
            $tbl_pria_tasks				= self::PORTAL_TABLE_PRIA_TASKS;

            $where = '';

            if(is_array($ag_last_core_task_ids) AND count($ag_last_core_task_ids) > 0)
			{
				$q_mark			= "";

				foreach($ag_last_core_task_ids AS $key => $ag_last_core_task_id)
				{
					$q_mark		.= (!EMPTY($q_mark))? ", ": "";
					$q_mark		.= $ag_last_core_task_id;
				}

				$join			.= "
					JOIN $tbl_pria_workflows B ON A.pr_id = B.reference_id
					AND A.account_group_code = B.account_group_code
					AND B.org_code IS NULL AND B.vendor_code IS NULL
					JOIN $tbl_pria_workflow_stages C ON B.pria_workflow_id = C.pria_workflow_id
					JOIN $tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id AND D.core_workflow_task_id IN ($q_mark)
					AND D.task_status_id IN (?, ?)";

				$values	= array_merge([TASK_STATUS_DONE, TASK_STATUS_APPROVED], $values);
			}

			$values[]	= $ag_codes;

			$query		= <<<EOS
				SELECT
					A.account_group_code,
					A.pr_id value,
					A.pr_num text
				FROM
					$pr A
				$join
				WHERE A.account_group_code = ?
				ORDER BY text
EOS;
			return $this->query($query, $values, TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendor($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_VENDORS, FALSE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendors($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_VENDORS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_purchase_orders($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PURCHASE_ORDERS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_purchase_order($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PURCHASE_ORDERS, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_delivery_goods_receipts($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_fwd_delivery_goods_receipts($account_group_code = AG_FORWARDERS)
	{
		try
		{
			$tbl_delivery_goods_receipt = self::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;

			$dr_cancelled 			= DR_CANCELLED;
			$dr_for_cancellation 	= DR_FOR_CANCELLATION;

			$query 	 = <<<EOS
				SELECT *
					FROM $tbl_delivery_goods_receipt
				WHERE account_group_code = ?
					AND (dr_status NOT IN (?, ?) OR dr_status IS NULL)
				GROUP BY dr_gr_id DESC
EOS;

			return $this->query($query, [$account_group_code, $dr_cancelled, $dr_for_cancellation], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_delivery_goods_receipts($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT, FALSE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_sites($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_SITES . " a", TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/**
	 * @Author: kevin villarojo
	 * @Date: 2019-10-09 16:55:30
	 * @Desc:
	 * @Referenced: Encode_delivery_receipt.php
	 */
	public function get_site_selection($ag_code = NULL, $org_code = NULL)
	{
		try
		{
			$tbl_sites 				= self::PORTAL_TABLE_SITES;
			$site_status_completed 	= SITE_STATUS_COMPLETED;

			$and = "";

			if($ag_code){
				switch ($ag_code) {
	                case AG_GOODS_MARINADES:

	                	$dressing_plant 	=  SITE_TYPE_DRESSING_PLANT;
                        $warehouse 			=  SITE_TYPE_WAREHOUSE;
                        $office             =  SITE_TYPE_OFFICE;

	                    $and = " AND (site_type_code = '".$dressing_plant."' OR site_type_code = '".$warehouse."' OR site_type_code = '".$office."')";
	                break;

	                default:
	                    $and = "";
	                break;
	            }
			}

			if( ! EMPTY($org_code))
			{
				$and .=<<<EOS
					AND org_code = $org_code
EOS;
			}


			$query 	 = <<<EOS
				SELECT *
					FROM $tbl_sites
				WHERE status_code = '$site_status_completed'
				{$and}
				ORDER BY official_store_name ASC
EOS;

			return $this->query($query, array(), TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_overdue_site_contracts()
	{
		try
		{
			$tbl_sites 			= self::PORTAL_TABLE_SITES;
			$tbl_contracts 		= self::PORTAL_TABLE_CONTRACTS;

			$status_completed 	= SITE_STATUS_COMPLETED;
			$contract_status 	= CONTRACT_OVERDUE;

			$query 	 = <<<EOS
				SELECT *
					FROM $tbl_sites a
				JOIN $tbl_contracts b
					ON a.site_id = b.site_id
				WHERE a.status_code = ? AND b.contract_status_code = ?
EOS;

			return $this->query($query, [$status_completed, $contract_status], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_site($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_SITES, FALSE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_site_types($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_SITE_TYPES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_organizations($where=array(), $fields=array('*'), $order=array('name' => 'ASC'))
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_ORGANIZATIONS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_purchasing_group($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_PURCHASING_GROUP, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_pr_item_types($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_PARAM_PR_ITEM_TYPES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_purchase_requisitions($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PURCHASE_REQUISITIONS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_purch_requisitions($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PURCHASE_REQUISITIONS, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_purch_requisitions_multiple($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PURCHASE_REQUISITIONS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

// 	public function get_all_pr_ref($ag_codes)
// 	{
// 		try
// 		{
// 			$val = array();
//             $ag_arr_code  = implode("','", $ag_codes);


//             $pr = self::PORTAL_TABLE_PURCHASE_REQUISITIONS;

//             $where = '';

//             if($ag_codes)
//             	$where = "WHERE a.account_group_code IN ('$ag_arr_code')";

// 			$query 	 = <<<EOS
// 				SELECT
// 					*
// 				FROM
// 					$pr a
// 				$where
// EOS;

// 			return $this->query($query, [], TRUE, TRUE);
// 		}
// 		catch(PDOException $e)
// 		{
// 			throw $e;
// 		}
// 	}

	public function get_all_gl_accounts($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			if( EMPTY($where))
				$where = ['deleted_flag' => INITIAL_NO];

			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_GL_ACCOUNTS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_apv_status($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_APV_STATUS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_delivery_status($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_DELIVERY_STATUS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_projects($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PROJECTS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_core_user(array $where=[], array $fields=['*'], $multiple = FALSE, $order = array())
	{
		try
		{
			return $this->select_data($fields, self::CORE_TABLE_USERS, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-06-20 13:35:23
	 * @Desc:
	 * @ReferencedBy:  Pria_overview.php, Pria_workflow.php
	 */
	public function get_user_fullname($user_id)
	{
		try
		{
			//Get the name of the current user
			$user_details = $this->get_core_user(['user_id' => $user_id], [
                aes_crypt('fname', FALSE),
                aes_crypt('mname', FALSE),
                aes_crypt('lname', FALSE),
                aes_crypt('ext_name', FALSE)
            ]);

            $actor  = trim(
                $user_details['fname'].' '.
                $user_details['mname'].' '.
                $user_details['lname'].' '.
                $user_details['ext_name']
            );

            return $actor;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_module(array $where=[], array $fields=['*'])
	{
		try
		{
			return $this->select_data($fields, self::CORE_MODULES, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_specific_task_document_type($where=array(), $fields=array('*'), $order=array(), $multiple_flag = TRUE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_TASK_DOCUMENT_TYPES, $multiple_flag, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_spec_site($where=array(), $fields=array('*'), $order=array(), $multiple_flag = TRUE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_SITES, $multiple_flag, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_task_doc_file_extension($pria_task_id = NULL)
	{
		try
		{
			$tbl_task_doc_types 	= self::PORTAL_TABLE_PRIA_TASK_DOCUMENT_TYPES;
			$tbl_document_types 	= self::PORTAL_TABLE_PARAM_DOCUMENT_TYPES;
//kevin
			$query      =<<<EOS
                SELECT
                    a.pria_task_id,
					a.core_workflow_task_id,
					a.document_type_code,
					a.access,
					group_concat(b.allowed_extensions) allowed_ext,
					b.file_bytes
                FROM
                    $tbl_task_doc_types a
                JOIN $tbl_document_types b on a.document_type_code = b.document_type_code

                WHERE
                    a.pria_task_id = ?
                GROUP BY a.document_type_code
EOS;
            return $this->query($query, [$pria_task_id], TRUE);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function _get_display_records()
	{
		try
		{
			$query = 'SELECT FOUND_ROWS() cnt';
			$count = $this->query($query, NULL, TRUE, FALSE);

			return $count['cnt'];
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_business_centers($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			$where_arr = ['deleted_flag' => INITIAL_NO];

			if( ! EMPTY($where))
				$where_arr = array_merge($where_arr, $where);

			return $this->select_data($fields, self::PORTAL_TABLE_BUSINESS_CENTER, TRUE, $where_arr, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_param_document_types($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PARAM_DOCUMENT_TYPES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_organizations($where=array(), $fields=array('*'), $order=array('name' => 'ASC'))
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_ORGANIZATIONS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_organization($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_ORGANIZATIONS, FALSE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_pria_workflow_details($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_WORKFLOWS, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_pria_workflow_stage_details($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_WORKFLOW_STAGES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_param_boq_categories($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_next_predecessors($where, $fields=array('*'), $order=array())
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_PRIA_TASK_PREDECESSORS, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_roles(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_PRIA_TASK_ROLES, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_ref(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_PRIA_TASKS, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_notif_info(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_PRIA_TASKS, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_users_role(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::CORE_USER_ROLES, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_sys_notif_users_role($role_code = NULL, $module_code = NULL)
	{
		try
		{
			$tbl_user_roles 			= self::CORE_USER_ROLES;
			$tbl_vendor_users 			= self::PORTAL_TABLE_VENDOR_USERS;
			$tbl_vendor_account_group 	= self::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP;

//christian
			$query      =<<<EOS
                SELECT *
                FROM $tbl_user_roles a
			JOIN $tbl_vendor_users b on a.user_id = b.user_id
			JOIN $tbl_vendor_account_group c on b.vendor_code = c.vendor_code
			WHERE a.role_code = ? AND c.account_group_code = ?
EOS;

            return $this->query($query, [$role_code, $module_code], TRUE);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_contractor_process_categories($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_PARAM_PR_CONTRACTOR_PROCESS_CATEGORIES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_param_contractor_process_category_map($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_PARAM_PR_CONTRACTOR_PROCESS_CATEGORY_MAP, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

/* 	public function get_param_contractor_process_category_map($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::CORE_USER_ROLES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	} */



	public function get_param_contractor_process_categories_by_process_type($process_type_code)
	{
		try
		{
			$tbl_map = self::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORY_MAP;
			$tbl_cat = self::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES;
			$query 	 = <<<EOS
				SELECT
					a.category_code, b.category_name
				FROM
					$tbl_map a
				JOIN
					$tbl_cat b ON a.category_code = b.category_code
				WHERE
					a.process_type_code = ?
EOS;
			return $this->query($query, [$process_type_code], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_users_by_role($role_code, $status=STATUS_ACTIVE)
	{
		try
		{
			$tbl_users 		= self::CORE_TABLE_USERS;
			$tbl_user_roles = self::CORE_USER_ROLES;
			$tbl_user_orgs  = self::PORTAL_TABLE_USER_ORGS;

			$query 	 = <<<EOS
			SELECT
				a.user_id, GROUP_CONCAT(b.org_code) as user_orgs
			FROM
				$tbl_user_roles a
			JOIN
				$tbl_user_orgs b ON a.user_id = b.user_id
			JOIN
				$tbl_users c ON a.user_id = c.user_id
			WHERE
				a.role_code = ?
			AND
				c.status 	= ?
EOS;

			return $this->query($query, [$role_code, $status], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_users_by_org($org_code, $status=STATUS_ACTIVE)
	{
		try
		{
			$tbl_users 		= self::CORE_TABLE_USERS;
			$tbl_user_roles = self::CORE_USER_ROLES;
			$tbl_user_orgs  = self::PORTAL_TABLE_USER_ORGS;

			$query 	 = <<<EOS
			SELECT
				c.user_id,
				CONCAT(
					AGDEC(c.fname), ' ',
					IFNULL(AGDEC(c.mname),''), ' ',
					IFNULL(AGDEC(c.lname),''),' ',
					IFNULL(AGDEC(c.ext_name),'')
				) as fullname
			FROM
				 $tbl_users c
			JOIN
				$tbl_user_orgs b ON b.user_id = c.user_id
			WHERE
				b.org_code  = ?
			AND
				c.status 	= ?
EOS;

			return $this->query($query, [$org_code, $status], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_users_by_role_n_org($role_code, $org_code, $status=STATUS_ACTIVE)
	{
		try
		{
			$tbl_users 		= self::CORE_TABLE_USERS;
			$tbl_user_roles = self::CORE_USER_ROLES;
			$tbl_user_orgs  = self::PORTAL_TABLE_USER_ORGS;

			$query 	 = <<<EOS
			SELECT
				a.user_id, GROUP_CONCAT(b.org_code) as user_orgs
			FROM
				$tbl_user_roles a
			JOIN
				$tbl_user_orgs b ON a.user_id = b.user_id
			JOIN
				$tbl_users c ON a.user_id = c.user_id
			WHERE
				a.role_code = ?
			AND
				b.org_code  = ?
			AND
				c.status 	= ?
			GROUP BY
				a.user_id
EOS;

			return $this->query($query, [$role_code, $org_code, $status], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}



	public function get_vendor_user(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_VENDOR_USERS, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_vendor_site(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_VENDOR_SITES, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_vendor_sites(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_VENDOR_SITES, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_vendor_users(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_VENDOR_USERS, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_pria_references(array $where, array $fields=['*'], array $order=[], $multiple = FALSE)
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE, $multiple, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_account_group_modules(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_PARAM_ACCOUNT_GROUP_MODULES, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}



	public function get_bavi_recipients_by_org($org_code, $status=STATUS_ACTIVE)
    {
        try
        {
            $tbl_users 		= self::CORE_TABLE_USERS;
            $tbl_user_roles = self::CORE_USER_ROLES;
            $tbl_user_orgs  = self::PORTAL_TABLE_USER_ORGS;

            $query 	 = <<<EOS
            SELECT
                c.user_id,
                CONCAT(
                    AGDEC(c.fname), ' ',
                    IFNULL(AGDEC(c.mname),''), ' ',
                    IFNULL(AGDEC(c.lname),''),' ',
                    IFNULL(AGDEC(c.ext_name),'')
                ) as fullname,
                a.role_code
            FROM
                $tbl_users c
            JOIN
                $tbl_user_orgs b ON b.user_id = c.user_id
            JOIN
                $tbl_user_roles a ON a.user_id = c.user_id
            WHERE
                b.org_code  = ?
            AND
                c.status 	= ?
            GROUP BY
                c.user_id
            HAVING
                role_code <> ?
			ORDER BY fullname
EOS;
                return $this->query($query, [$org_code, $status, TASK_ROLE_VENDOR], TRUE, TRUE);
            }
            catch(PDOException $e)
            {
                throw $e;
            }
	}



	public function get_last_task_per_workflow($pria_workflow_id, $fields=['*'])
	{
		try
		{
			$fields = implode(',', $fields);
			$query  =<<<EOS
			SELECT
				$fields
			FROM
				%s a
			JOIN
				%s b on a.pria_workflow_id = b.pria_workflow_id
			JOIN
				%s c on b.pria_stage_id = c.pria_stage_id
			WHERE
				a.pria_workflow_id = ?
			ORDER BY
				b.sequence_no DESC,
				c.sequence_no DESC
			LIMIT 1
EOS;

			$query = sprintf($query, self::PORTAL_TABLE_PRIA_WORKFLOWS, self::PORTAL_TABLE_PRIA_WORKFLOW_STAGES, self::PORTAL_TABLE_PRIA_TASKS);

			return $this->query($query, [$pria_workflow_id], TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_param_contractor_category_files($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_CATEGORY_FILES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function ordering_pria($aColumns, $params, $with_order_by=TRUE)
	{
		$result = $this->ordering($aColumns, $params);

		return ($with_order_by) ? $result : str_replace("ORDER BY", ",", $result);
	}

	public function get_used_dr(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, self::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_param_purchase_order_types($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, self::PORTAL_TABLE_PURCHASE_ORDER_TYPES, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}