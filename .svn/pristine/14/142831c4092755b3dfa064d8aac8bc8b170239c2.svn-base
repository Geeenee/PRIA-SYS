<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import_reference_model extends Portal_Model
{
	public $tbl_param_account_groups;
	public $tbl_param_apv_status;
	public $tbl_pria_tab_modules;
	public $tbl_pria_task_forms;

	public $tbl_payments;
	public $tbl_payment_apvs;

	public $tbl_documents;
	public $tbl_purchase_orders;
	public $tbl_purchase_requisitions;
	public $tbl_soa;
	public $tbl_vendors;

	public $tbl_pria_workflows;
	public $tbl_pria_workflow_stages;
	public $tbl_pria_tasks;

	public $tbl_pria_references;

	public function __construct()
	{
		parent::__construct();

		$this->tbl_param_account_groups		= parent::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
		$this->tbl_param_apv_status			= parent::PORTAL_TABLE_PARAM_APV_STATUS;
		$this->tbl_pria_tab_modules			= parent::PORTAL_TABLE_PRIA_TAB_MODULE;
		$this->tbl_pria_task_forms			= parent::PORTAL_TABLE_PRIA_TASK_FORMS;

		$this->tbl_payments					= parent::PORTAL_TABLE_PAYMENTS;
		$this->tbl_payment_apvs				= parent::PORTAL_TABLE_PAYMENT_APVS;

		$this->tbl_documents				= parent::PORTAL_TABLE_DOCUMENTS;
		$this->tbl_purchase_orders			= parent::PORTAL_TABLE_PURCHASE_ORDERS;
		$this->tbl_purchase_requisitions	= parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
		$this->tbl_soa						= parent::PORTAL_TABLE_SOA;
		$this->tbl_vendors					= parent::PORTAL_TABLE_VENDORS;

		$this->tbl_pria_workflows			= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
		$this->tbl_pria_workflow_stages		= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
		$this->tbl_pria_tasks				= parent::PORTAL_TABLE_PRIA_TASKS;

		$this->tbl_pria_references			= parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
	}

	public function get_payments_list($params=NULL)
	{
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$values	= array();

			$fields				=  array(
					"A.payment_apv_id",
					"B.account_group_code AS account_group_code",
					"D.vendor_name AS vendor_name",
					"B.reference_num AS reference_num",
					"A.apv_num AS apv_num",
					"DATE_FORMAT(A.apv_date, '%m/%d/%Y') AS apv_date",
					"FORMAT(A.apv_amount, 2) AS apv_amount",
					"A.cv_num AS cv_num",
					"FORMAT(A.cv_amount, 2) AS cv_amount",
					"A.particulars AS particulars",
					"C.apv_status_name AS apv_status_name"
			);

			$filters			= array(
					"B.account_group_code convert_to account_group_code",
					"D.vendor_name convert_to vendor_name",
					"B.reference_num convert_to reference_num",
					"A.apv_num convert_to apv_num",
					"DATE_FORMAT(A.apv_date, '%m/%d/%Y') convert_to apv_date",
					"FORMAT(A.apv_amount, 2) convert_to apv_amount",
					"A.cv_num convert_to cv_num",
					"FORMAT(A.cv_amount, 2) convert_to cv_amount",
					"A.particulars convert_to particulars",
					(!EMPTY($params['sSearch'])? "C.apv_status_name": "A.apv_status_code") . " convert_to apv_status_name"
			);

			$filters_ordering	= array(
					"account_group_code",
					"vendor_name",
					"reference_num",
					"apv_num",
					"apv_date",
					"apv_amount",
					"cv_num",
					"cv_amount",
					"particulars",
					"apv_status_name"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.payment_apv_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
					FROM $this->tbl_payment_apvs A
					LEFT JOIN $this->tbl_payments B ON A.payment_id = B.payment_id
					LEFT JOIN $this->tbl_param_apv_status C ON A.apv_status_code = C.apv_status_id
					LEFT JOIN $this->tbl_vendors D ON B.vendor_code = D.vendor_code
				WHERE 1=1
				$where
				$order
				$limit
EOS;
			if($params === NULL)
			{
				$total	= $this->query($query, $values, TRUE, FALSE);
				return $total['total'];
			}
			else
			{
				return array(
						'records'			=> $this->query($query, $values),
						'display_records'	=> $this->_get_display_records()
				);
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_purchase_orders_list($params=NULL)
	{
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = $q_mark = "";

			$values	= array(TRANS_TAB_PO);

			$core_task_ids		= array(CORE_TASK_PO_UPLOAD_APPROVAL, CORE_TASK_PO_UPLOAD_WO_APPROVAL);

			foreach($core_task_ids AS $key => $core_task_id)
			{
				$q_mark			.= (!EMPTY($q_mark))? ", ?": "?";
				$values[]		= $core_task_id;
			}

			$values[]			= DOC_TYPE_PO;

			$fields				=  array(
					"A.po_id",
					"A.po_num AS po_num",
					"DATE_FORMAT(A.po_date, '%m/%d/%Y') AS po_date",
					"FORMAT(A.po_amount, 2) AS po_amount",
					"GROUP_CONCAT(DISTINCT B.pr_num SEPARATOR '<br/>') AS pr_num",
					"C.vendor_name AS vendor",
					"G.sys_file_name AS sys_file_name",
					"F.pria_task_id AS pria_task_id",
					"B.account_group_code AS account_group_code",
					"H.controller AS task_controller"
			);

			$filters			= array(
					"B.account_group_code convert_to account_group_code",
					"A.po_num convert_to po_num",
					"DATE_FORMAT(A.po_date, '%m/%d/%Y') convert_to po_date",
					"FORMAT(A.po_amount, 2) convert_to po_amount",
					"B.pr_num convert_to pr_num",
					"C.vendor_name convert_to vendor"
			);

			$filters_ordering	= array(
					"account_group_code",
					"po_num",
					"po_date",
					"po_amount",
					"pr_num",
					"vendor"
			);

			$group_by	= "";

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(DISTINCT A.po_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);

				$group_by		= "GROUP BY A.po_id";
			}
	
			$query =<<<EOS
				SELECT
					$select_fields
					FROM $this->tbl_purchase_orders A
					JOIN $this->tbl_pria_references I ON A.po_id = I.po_id
					LEFT JOIN $this->tbl_purchase_requisitions B ON I.pr_id = B.pr_id
					LEFT JOIN $this->tbl_vendors C ON A.vendor_code = C.vendor_code
					LEFT JOIN $this->tbl_pria_workflows D ON A.po_id = D.reference_id
					AND B.account_group_code = D.account_group_code AND D.core_workflow_id IN (
						SELECT AA.core_workflow_id FROM $this->tbl_pria_tab_modules AA
						WHERE AA.ag_code = B.account_group_code AND  AA.transaction_tab = ?
					)
					LEFT JOIN $this->tbl_pria_workflow_stages E ON D.pria_workflow_id = E.pria_workflow_id
					LEFT JOIN $this->tbl_pria_tasks F ON E.pria_stage_id = F.pria_stage_id AND F.core_workflow_task_id IN ($q_mark)
					LEFT JOIN $this->tbl_documents G ON A.po_id = G.reference AND F.pria_task_id = G.pria_task_id AND G.document_type_code = ?
					LEFT JOIN $this->tbl_pria_task_forms H ON F.pria_task_id = H.pria_task_id
				WHERE 1=1
				$where
				$group_by
				$order
				$limit
EOS;
		
			if($params === NULL)
			{
				$total	= $this->query($query, $values, TRUE, FALSE);
				return $total['total'];
			}
			else
			{
				//print_r($query.var_export($values, TRUE));
				return array(
						'records'			=> $this->query($query, $values),
						'display_records'	=> $this->_get_display_records()
				);
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_soas_list($params=NULL)
	{
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = $q_mark = "";

			$values	= array(TRANS_TAB_SOA);

			$core_task_ids		= array(CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED, CORE_TASK_SOA_UPLOAD_TRUCKERS_NORMAL, CORE_TASK_SOA_UPLOAD_TRUCKERS_FEEDMILL, CORE_TASK_SOA_UPLOAD_TRUCKERS_MANPOWER, CORE_TASK_SOA_UPLOAD_FORWARDERS, CORE_TASK_SOA_UPLOAD);

			foreach($core_task_ids AS $key => $core_task_id)
			{
				$q_mark			.= (!EMPTY($q_mark))? ", ?": "?";
				$values[]		= $core_task_id;
			}

			$values[]			= DOC_TYPE_SOA;

			$fields				=  array(
					"A.soa_id",
					"A.soa_num AS soa_num",
					"DATE_FORMAT(A.date_from, '%m/%d/%Y') date_from",
					"DATE_FORMAT(A.date_to, '%m/%d/%Y') date_to",
					"B.vendor_name AS vendor",
					"F.sys_file_name AS sys_file_name",
					"E.pria_task_id AS pria_task_id",
					"A.account_group_code AS account_group_code",
					"G.controller AS task_controller"
			);

			$filters			= array(
					"A.account_group_code convert_to account_group_code",
					"A.soa_num convert_to soa_num",
					"DATE(A.date_from) convert_to date_from",
					"DATE(A.date_to) convert_to date_to",
					"B.vendor_name convert_to vendor"
			);

			$filters_ordering	= array(
					"account_group_code",
					"soa_num",
					"A.date_from",
					"A.date_to",
					"vendor"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				if(ISSET($params['period_from']) AND !EMPTY($params['period_from']))
					$params['date_from']	= date(FORMAT_DB_DATE, strtotime($params['period_from']));

				if(ISSET($params['period_to']) AND !EMPTY($params['period_to']))
					$params['date_to']		= date(FORMAT_DB_DATE, strtotime($params['period_to']));

				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
					FROM $this->tbl_soa A
					LEFT JOIN $this->tbl_vendors B ON A.vendor_code = B.vendor_code
					LEFT JOIN $this->tbl_pria_workflows C ON A.soa_id = C.reference_id
					AND A.account_group_code = C.account_group_code AND C.core_workflow_id IN (
						SELECT AA.core_workflow_id FROM $this->tbl_pria_tab_modules AA
						WHERE AA.ag_code = A.account_group_code AND AA.transaction_tab = ?
					)
					LEFT JOIN $this->tbl_pria_workflow_stages D ON C.pria_workflow_id = D.pria_workflow_id
					LEFT JOIN $this->tbl_pria_tasks E ON D.pria_stage_id = E.pria_stage_id AND E.core_workflow_task_id IN ($q_mark)
					LEFT JOIN $this->tbl_documents F ON A.soa_id = F.reference AND E.pria_task_id = F.pria_task_id AND F.document_type_code = ?
					LEFT JOIN $this->tbl_pria_task_forms G ON E.pria_task_id = G.pria_task_id
				WHERE 1=1
				$where
				$order
				$limit
EOS;
			if($params === NULL)
			{
				$total	= $this->query($query, $values, TRUE, FALSE);
				return $total['total'];
			}
			else
			{
				return array(
						'records'			=> $this->query($query, $values),
						'display_records'	=> $this->_get_display_records()
				);
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}