<?php if (!defined('BASEPATH')) exit('No direct script access is allowed');

class Dashboard_model extends Portal_Model
{
	public $tbl_core_notifications;

	public $tbl_param_apv_status;
	public $tbl_param_payment_terms;

	public $tbl_boq;
	public $tbl_boq_pr;
	public $tbl_contracts;
	public $tbl_delivery_goods_receipt;
	public $tbl_internal_orders;
	public $tbl_projects;
	public $tbl_purchase_orders;
	public $tbl_purchase_requisitions;
	public $tbl_soa;
	public $tbl_transmittals;

	public $tbl_payments;
	public $tbl_payment_apvs;

	public $tbl_pria_task_predecessors;
	public $tbl_pria_references;
	public $tbl_pria_tasks;
	public $tbl_pria_task_actions;
	public $tbl_pria_task_appendable;
	public $tbl_pria_task_forms;
	public $tbl_pria_task_roles;
	public $tbl_pria_workflow_stages;
	public $tbl_pria_workflows;

	public $tbl_workflow_stages;
	public $tbl_workflow_stage_tasks;

	public $tbl_pria_tab_modules;
	public $tbl_organizations;
	public $tbl_sites;
	public $tbl_vendors;
	public $tbl_vendor_business_centers;

	public $tbl_users;
	public $tbl_user_roles;

	public function __construct()
	{
		parent::__construct();

		$this->tbl_core_notifications				= parent::CORE_NOTIFICATIONS;

		$this->tbl_param_apv_status					= parent::PORTAL_TABLE_PARAM_APV_STATUS;
		$this->tbl_param_payment_terms				= parent::PORTAL_TABLE_PARAM_PAYMENT_TERMS;

		$this->tbl_boq								= parent::PORTAL_TABLE_PRIA_BOQ;
		$this->tbl_boq_pr							= parent::PORTAL_TABLE_PRIA_BOQ_PR;
		$this->tbl_contracts						= parent::PORTAL_TABLE_CONTRACTS;
		$this->tbl_delivery_goods_receipt			= parent::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
		$this->tbl_internal_orders					= parent::PORTAL_TABLE_INTERNAL_ORDERS;
		$this->tbl_projects							= parent::PORTAL_TABLE_PROJECTS;
		$this->tbl_purchase_orders					= parent::PORTAL_TABLE_PURCHASE_ORDERS;
		$this->tbl_purchase_requisitions			= parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
		$this->tbl_purchase_request_cost_centers	= parent::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;
		$this->tbl_soa								= parent::PORTAL_TABLE_SOA;
		$this->tbl_transmittals						= parent::PORTAL_TABLE_TRANSMITTALS;

		$this->tbl_payments							= parent::PORTAL_TABLE_PAYMENTS;
		$this->tbl_payment_apvs						= parent::PORTAL_TABLE_PAYMENT_APVS;

		$this->tbl_pria_task_predecessors			= parent::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
		$this->tbl_pria_references					= parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
		$this->tbl_pria_tasks						= parent::PORTAL_TABLE_PRIA_TASKS;
		$this->tbl_pria_task_actions				= parent::PORTAL_TABLE_PRIA_TASK_ACTIONS;
		$this->tbl_pria_task_appendable				= parent::PORTAL_TABLE_PRIA_TASK_APPENDABLE;
		$this->tbl_pria_task_forms					= parent::PORTAL_TABLE_PRIA_TASK_FORMS;
		$this->tbl_pria_task_roles					= parent::PORTAL_TABLE_PRIA_TASK_ROLES;
		$this->tbl_pria_workflow_stages				= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
		$this->tbl_pria_workflows					= parent::PORTAL_TABLE_PRIA_WORKFLOWS;

		$this->tbl_workflow_stages					= parent::CORE_WORKFLOW_STAGES;
		$this->tbl_workflow_stage_tasks				= parent::CORE_WORKFLOW_STAGE_TASKS;

		$this->tbl_pria_tab_modules					= parent::PORTAL_TABLE_PRIA_TAB_MODULE;
		$this->tbl_organizations					= parent::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_sites							= parent::PORTAL_TABLE_SITES;
		$this->tbl_vendors							= parent::PORTAL_TABLE_VENDORS;
		$this->tbl_vendor_business_centers			= parent::PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS;

		$this->tbl_users							= parent::CORE_TABLE_USERS;
		$this->tbl_user_roles						= parent::CORE_USER_ROLES;

		$this->tbl_boq_asset_codes					= parent::PORTAL_TABLE_PRIA_BOQ_ASSET;
		$this->tbl_boq 								= parent::PORTAL_TABLE_PRIA_BOQ;

		$this->tbl_documents						= parent::PORTAL_TABLE_DOCUMENTS;
	}

	public function get_user_notifications($user_id, $module_scope=NULL)
	{
		try
		{
			$values	= array($user_id, ENUM_NO);

			$in_array = " AND modules_code IN ('".$module_scope."')";

			$query	= <<<EOS
				SELECT
				A.notification, A.notification_html, A.notification_icon, A.notification_date
				FROM $this->tbl_core_notifications A
				WHERE A.notify_users = ? AND A.reminder_flag = ?
				ORDER BY A.notification_date DESC
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	//Added by Christian
	//Oct 23, 2019 3:38pm
	public function get_reminders($user_id, $reminder_interval = NULL)
	{
		try
		{
			$reminder_interval	= (!EMPTY($reminder_interval))? $reminder_interval: 30;

			$values		= array($user_id, YES_FLAG, $reminder_interval);
			$in_array 	= " AND modules_code IN ('".$module_scope."')";

			$query	= <<<EOS
				SELECT
					A.notification_id,
					A.notification,
					A.notification_html,
					A.notification_icon,
					A.notification_date,
					A.read_date
				FROM $this->tbl_core_notifications A
				WHERE A.notify_users = ? AND A.reminder_flag = ?
				AND A.read_date IS NULL AND DATE(A.notification_date) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
				ORDER BY A.notification_date DESC
EOS;
			//B.ag_code
			//JOIN $this->tbl_pria_tab_modules B ON A.module_code = B.parent_module_code
			// echo "<pre>";
			// print_r ($query);
			// print_r ($values);
			// echo "</pre>";
			// die;

			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_user_tasks($user_id, $having=NULL)
	{
		try
		{
			$having	= str_replace('org_code', 'IF(N.po_id IS NOT NULL, N.org_code, IF(M.pr_id IS NOT NULL, M.org_code, IF(L.pr_id IS NOT NULL, L.org_code, C.org_code)))', str_replace('vendor_code', 'C.vendor_code', $having));

			$values	= array(
					ENUM_YES, MAIN_ROLE_FLAG,
					TASK_STATUS_SKIPPED, TASK_STATUS_DONE, TASK_STATUS_APPROVED,
					$user_id, $user_id,
					TASK_STATUS_SKIPPED, TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED,
					CORE_TASK_MED_VAC, CORE_TASK_MED_VAC_APPEND,
					CORE_TASK_DOC_GR, CORE_TASK_DOC_GR_APPEND, CORE_TASK_PO_TRANSMIT_BFFI, CORE_TASK_PO_GR_BAVI_MARINADES
					//ENUM_YES
			);
			//LEFT JOIN $this->tbl_vendors J ON C.vendor_code = J.vendor_code ORIGINAL REPLACED BY II, III AND J
			//REMOVE THIS BEFORE HAVING "AND IF(K.dr_gr_id IS NOT NULL, IF(K.last_dr_flag = ?, TRUE, FALSE), TRUE) = TRUE", IF YOU'RE GONNA ADD IT AGAIN, ADD "ENUM_YES" IN $values
			$query	= <<<EOS
				(SELECT
				A.pria_task_id, A.task_name, IF(AGDEC(A.actor_name) = '' OR AGDEC(A.actor_name) IS NULL, 'N/A', AGDEC(A.actor_name)) AS actor_name, C.reference_num, IF(A.task_status_id IS NULL, IF(A.returned_flag = ?, 'Returned', 'Pending'), D.action_name) AS action_name, IFNULL(IFNULL(IFNULL(A.end_date, A.start_date), H.end_date), A.created_date) AS task_date, E.controller,
				IFNULL(IF(N.po_id IS NOT NULL, N.name, IF(M.pr_id IS NOT NULL, M.name, IF(L.pr_id IS NOT NULL, GROUP_CONCAT(L.name ORDER BY L.name SEPARATOR ', '), I.name))), 'N/A') as org_name,  IFNULL(GROUP_CONCAT(DISTINCT J.vendor_name SEPARATOR ', '), 'N/A') as vendor_name, C.org_code
				FROM $this->tbl_pria_tasks A
				LEFT JOIN $this->tbl_pria_workflow_stages B ON A.pria_stage_id = B.pria_stage_id
				LEFT JOIN $this->tbl_pria_workflows C ON B.pria_workflow_id = C.pria_workflow_id
				LEFT JOIN $this->tbl_pria_task_actions D ON A.pria_task_id = D.pria_task_id AND A.task_status_id = D.pria_task_action_id
				LEFT JOIN $this->tbl_pria_task_forms E ON A.pria_task_id = E.pria_task_id
				LEFT JOIN $this->tbl_pria_task_roles F ON A.pria_task_id = F.pria_task_id AND F.actor_flag = ?
				LEFT JOIN $this->tbl_pria_task_predecessors G ON A.pria_task_id = G.pria_task_id
				LEFT JOIN $this->tbl_pria_tasks H ON G.pre_pria_task_id = H.pria_task_id
				LEFT JOIN $this->tbl_organizations I ON C.org_code = I.org_code
				LEFT JOIN $this->tbl_projects PI ON C.reference_num = PI.project_code
				LEFT JOIN $this->tbl_boq II ON IF(PI.boq_id IS NOT NULL, PI.boq_id, C.reference_num ) = IF(PI.boq_id IS NOT NULL, II.boq_id, II.boq_code)
				LEFT JOIN $this->tbl_boq_asset_codes III ON  III.boq_id = II.boq_id
				LEFT JOIN $this->tbl_vendors J ON J.vendor_code = IF(II.boq_id IS NOT NULL, (IF(III.confirmed_contractor IS NOT NULL, III.confirmed_contractor, II.recommended_vendor_code)), C.vendor_code)
				LEFT JOIN $this->tbl_delivery_goods_receipt K ON H.reference = K.dr_gr_id AND C.account_group_code = K.account_group_code
				LEFT JOIN (
					SELECT
					A.pr_id, A.pr_num, A.account_group_code, C.org_code, D.name
					FROM $this->tbl_purchase_requisitions A
					LEFT JOIN $this->tbl_purchase_request_cost_centers B ON A.pr_id = B.pr_id
					LEFT JOIN $this->tbl_sites C ON B.cost_center_code = C.cost_center_code
					LEFT JOIN $this->tbl_organizations D ON C.org_code = D.org_code
					WHERE C.org_code IS NOT NULL
				) L ON C.reference_num = L.pr_num AND C.account_group_code = L.account_group_code
				LEFT JOIN (
					SELECT
					A.pr_id, A.pr_num, A.account_group_code, C.org_code, D.name
					FROM $this->tbl_purchase_requisitions A
					LEFT JOIN $this->tbl_boq_pr E ON A.pr_id = E.pr_id
					LEFT JOIN $this->tbl_boq F ON E.boq_id = F.boq_id
					LEFT JOIN $this->tbl_sites C ON F.site_id = C.site_id
					LEFT JOIN $this->tbl_organizations D ON C.org_code = D.org_code
					WHERE C.org_code IS NOT NULL
				) M ON C.reference_num = M.pr_num AND C.account_group_code = M.account_group_code
				LEFT JOIN (
					SELECT
					DISTINCT A.po_id, A.po_num, C.account_group_code, E.org_code, F.name
					FROM $this->tbl_purchase_orders A
					JOIN $this->tbl_pria_references B ON A.po_id = B.po_id AND B.pr_id IS NOT NULL
					JOIN $this->tbl_purchase_requisitions C ON B.pr_id = C.pr_id
					JOIN $this->tbl_purchase_request_cost_centers D ON C.pr_id = D.pr_id
					JOIN $this->tbl_sites E ON D.cost_center_code = E.cost_center_code
					JOIN $this->tbl_organizations F ON E.org_code = F.org_code
				) N ON C.reference_num = N.po_num AND C.account_group_code = N.account_group_code
				WHERE
				(
					(H.pria_task_id IS NOT NULL AND H.task_status_id IN (?, ?, ?)) OR H.pria_task_id IS NULL
				)
				AND
				(
					((A.user_id IS NULL AND F.role_code IN (SELECT role_code FROM $this->tbl_user_roles WHERE user_id = ?))
					OR
					(A.user_id = ?))
					AND (A.task_status_id NOT IN (?, ?, ?, ?) OR A.task_status_id IS NULL)
				)
				AND 1 = (CASE
				WHEN H.core_workflow_task_id IN (?, ?) THEN
					IF((SELECT last_dr_flag FROM $this->tbl_delivery_goods_receipt WHERE pria_task_id = H.pria_task_id) = 'Y', 1, 0)
				WHEN H.core_workflow_task_id IN (?, ?, ?, ?) THEN
					IF((SELECT last_dr_flag FROM $this->tbl_delivery_goods_receipt WHERE pria_task_id IN (
						SELECT CC.pria_task_id
						FROM $this->tbl_pria_tasks AA
						JOIN $this->tbl_pria_workflow_stages BB ON AA.pria_stage_id = BB.pria_stage_id
						JOIN $this->tbl_pria_tasks CC ON BB.pria_stage_id = CC.pria_stage_id
						WHERE AA.pria_task_id = H.pria_task_id
					) LIMIT 1) = 'Y', 1, 0)
				ELSE 1 END)
				$having
				GROUP BY A.pria_task_id
				)
				ORDER BY task_date DESC
EOS;
			// print_var_export($query, $values); die;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_permissioned_ag($roles)
	{
		try
		{
			$values	=array();

			$query	=<<<EOS

EOS;

			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

    public function get_payment_cg_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(AG_CONTRACT_GROWERS, TRANS_TAB_IO);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_IO));
			$values		= array_merge($values, array(CORE_TASK_HARVEST_REPORT, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_HARVEST_REPORT_APPROVAL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_LIVE_SALES_REPORT, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_FHR, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_FHR_FINAL_APPROVAL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));

			$fields				=  array(
					"A.cycle_num AS cycle",
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.io_num AS reference_number",
					"IF(A.actual_clean_up_date IS NOT NULL, DATE_FORMAT(A.actual_clean_up_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') AS clean_up_date",
					"A.actual_clean_up_date",
					"DATE_FORMAT(A.harvest_rep_submit_date, '%d-%b-%Y') AS harvest_date",
					"A.harvest_rep_submit_date",
					"I.harvest_pics AS harvest_pic",
					"J.harvest_approval_dates AS harvest_approval_date",
					"J.harvest_approval_pics AS harvest_approval_pic",
					"K.live_sales_upload_dates AS live_sales_upload_date",
					"DATE_FORMAT(A.live_sales_submit_date, '%d-%b-%Y') AS live_sales_report_date",
					"K.live_sales_pics AS live_sales_pic",
					"IFNULL(L.fhr_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') AS fhr_upload_date",
					"IF(A.fhr_submit_date IS NOT NULL, DATE_FORMAT(A.fhr_submit_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."')  AS fhr_submission_date",
					"IFNULL(M.fhr_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') AS fhr_approval_date",
					"IFNULL(A.fhr_document_num, '".PAYMENT_EMPTY_INDICATOR."') AS fhr_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"A.cycle_num convert_to cycle",
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.io_num convert_to reference_number",
					"IF(A.actual_clean_up_date IS NOT NULL, DATE_FORMAT(A.actual_clean_up_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') convert_to clean_up_date",
					/*"DATE_FORMAT(A.harvest_rep_submit_date, '%d-%b-%Y') convert_to harvest_date",
					"I.harvest_pics convert_to harvest_pic",*/
					"J.harvest_approval_dates convert_to harvest_approval_date",
					/*"J.harvest_approval_pics convert_to harvest_approval_pic",*/
					"K.live_sales_upload_dates convert_to live_sales_upload_date",
					/*"DATE_FORMAT(A.live_sales_submit_date, '%d-%b-%Y') convert_to live_sales_report_date",
					"K.live_sales_pics convert_to live_sales_pic",*/
					"IFNULL(L.fhr_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to fhr_upload_date",
					"IF(A.fhr_submit_date IS NOT NULL, DATE_FORMAT(A.fhr_submit_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') convert_to fhr_submission_date",
					"IFNULL(M.fhr_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to fhr_approval_date",
					"IFNULL(A.fhr_document_num, '".PAYMENT_EMPTY_INDICATOR."') convert_to fhr_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					//"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) convert_to apv_status",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"cycle",
					"business_center_name",
					"vendor_name",
					"reference_number",
					"A.actual_clean_up_date",
					"L.fhr_upload_dates_raw",
					"A.fhr_submit_date",
					"M.fhr_approval_dates_raw",
					"fhr_number",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					//"apv_status"
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.io_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				//$order			= $this->ordering($filters_ordering, $params);
				$order			= $this->ordering_pria($filters_ordering, $params, TRUE);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_internal_orders A
				LEFT JOIN $this->tbl_pria_workflows B ON A.io_id = B.reference_id
				AND B.account_group_code = ? AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.io_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(CONCAT_WS(' ', AGDEC(AC.fname), AGDEC(AC.mname), AGDEC(AC.lname), AGDEC(AC.ext_name)) SEPARATOR ',<br/>') AS harvest_pics
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(CONCAT_WS(' ', AGDEC(AC.fname), AGDEC(AC.mname), AGDEC(AC.lname), AGDEC(AC.ext_name)) SEPARATOR ',<br/>') AS harvest_approval_pics,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS harvest_approval_dates,
						GROUP_CONCAT(AA.actual_end_date SEPARATOR ',<br/>') AS harvest_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) J ON B.pria_workflow_id = J.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(CONCAT_WS(' ', AGDEC(AC.fname), AGDEC(AC.mname), AGDEC(AC.lname), AGDEC(AC.ext_name)) SEPARATOR ',<br/>') AS live_sales_pics,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS live_sales_upload_dates,
						GROUP_CONCAT(AA.actual_end_date SEPARATOR ',<br/>') AS live_sales_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) K ON B.pria_workflow_id = K.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS fhr_upload_dates,
						GROUP_CONCAT(AA.actual_end_date SEPARATOR ',<br/>') AS fhr_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) L ON B.pria_workflow_id = L.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS fhr_approval_dates,
						GROUP_CONCAT(AA.actual_end_date SEPARATOR ',<br/>') AS fhr_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) M ON B.pria_workflow_id = M.pria_workflow_id
				WHERE (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
				$order
				$limit
EOS;

			if($params === NULL)
			{
				$total	= $this->query($query, $values, TRUE, FALSE);
				return $total['total'];
			}
			else
			{  /* print_var_export($query, $values); die; */
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

    public function get_payment_contractors_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$groupBy = $where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_PO);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_PO));
			$values		= array_merge($values, array(CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(TRANS_TAB_BOQ));
			$values		= array_merge($values, array(CORE_TASK_BOQ_PRES_APPROVED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_CONTRACTORS, TRANS_TAB_PROJ));
			$values		= array_merge($values, array(CORE_TASK_PROJ_BOQ_PROGRESS_APPROVED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_PROJ_COMPLETION_APPROVED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_CONTRACTORS));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"IFNULL(A.po_num, '".PAYMENT_EMPTY_INDICATOR."') AS po_number",
					"IFNULL(I.po_approved_dates, '".PAYMENT_EMPTY_INDICATOR."') AS po_approved_date",
					"IFNULL(M.boq_approved_dates, '".PAYMENT_EMPTY_INDICATOR."') AS boq_approved_date",
					"IFNULL(N.boq_progress_approved_dates, '".PAYMENT_EMPTY_INDICATOR."')  AS boq_progress_approved_date",
					"IFNULL(N.project_completion_approved_dates, '".PAYMENT_EMPTY_INDICATOR."')  AS project_completion_approved_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"IFNULL(A.po_num, '".PAYMENT_EMPTY_INDICATOR."') convert_to po_number",
					"IFNULL(I.po_approved_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to po_approved_date",
					"IFNULL(M.boq_approved_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to boq_approved_date",
					"IFNULL(N.boq_progress_approved_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to boq_progress_approved_date",
					"IFNULL(N.project_completion_approved_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to project_completion_approved_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					//"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) convert_to apv_status"
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"po_number",
					"I.po_approved_dates_raw",
					"M.boq_approved_dates_raw",
					"N.boq_progress_approved_dates_raw",
					"N.project_completion_approved_dates_raw",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_status",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.po_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);
				$groupBy        = 'GROUP BY A.po_id';
				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_purchase_orders A
				JOIN $this->tbl_pria_references O ON A.po_id = O.po_id
				LEFT JOIN $this->tbl_pria_workflows B ON A.po_id = B.reference_id
				AND B.account_group_code IN (
					SELECT account_group_code FROM $this->tbl_purchase_requisitions
					WHERE pr_id = O.pr_id
				) AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.po_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS po_approved_dates,
						GROUP_CONCAT(AA.actual_end_date) AS po_approved_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				LEFT JOIN $this->tbl_boq_pr J ON O.pr_id = J.pr_id
				LEFT JOIN $this->tbl_boq K ON J.boq_id = K.boq_id
				LEFT JOIN $this->tbl_pria_workflows L ON K.boq_id = L.reference_id
				AND B.account_group_code = L.account_group_code AND L.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = L.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS boq_approved_dates,
						GROUP_CONCAT(AA.actual_end_date) AS boq_approved_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) M ON L.pria_workflow_id = M.pria_workflow_id
				LEFT JOIN (
					SELECT
						AA.boq_id,
						GROUP_CONCAT(DATE_FORMAT(AD.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS boq_progress_approved_dates,
						GROUP_CONCAT(AD.actual_end_date) AS boq_progress_approved_dates_raw,
						GROUP_CONCAT(DATE_FORMAT(AF.actual_end_date, '%d-%b-%Y') SEPARATOR ',<br/>') AS project_completion_approved_dates,
						GROUP_CONCAT(AF.actual_end_date) AS project_completion_approved_dates_raw
					FROM $this->tbl_projects AA
					LEFT JOIN $this->tbl_pria_workflows AB ON AA.project_id = AB.reference_id
					AND AB.account_group_code = ? AND AB.core_workflow_id IN (
						SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
						WHERE ag_code = AB.account_group_code AND transaction_tab = ?
					)
					LEFT JOIN $this->tbl_pria_workflow_stages AC ON AB.pria_workflow_id = AC.pria_workflow_id
					LEFT JOIN $this->tbl_pria_tasks AD ON AC.pria_stage_id = AD.pria_stage_id
					AND AD.core_workflow_task_id = ? AND AD.task_status_id IN (?, ?)
					LEFT JOIN $this->tbl_users AE ON AD.user_id = AE.user_id
					LEFT JOIN $this->tbl_pria_tasks AF ON AC.pria_stage_id = AF.pria_stage_id
					AND AF.core_workflow_task_id = ? AND AF.task_status_id IN (?, ?)
					LEFT JOIN $this->tbl_users AG ON AF.user_id = AG.user_id
					GROUP BY AA.boq_id
				) N ON K.boq_id = N.boq_id
				WHERE B.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
				$groupBy
				$order
				$limit
EOS;

			if($params === NULL)
			{
				$total	= $this->query($query, $values, TRUE, FALSE);
				return $total['total'];
			}
			else
			{ // print_var_export($query, $values);
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

    public function get_payment_forwarders_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(TASK_STATUS_SKIPPED, TASK_STATUS_SKIPPED));
			$values		= array_merge($values, array(CORE_TASK_SOA_TRANSMIT_CALAMBA, TASK_STATUS_SKIPPED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_FORWARDERS));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"IFNULL(A.soa_num, '".PAYMENT_EMPTY_INDICATOR."') AS soa_number",
					"IF(A.submission_date IS NOT NULL, DATE_FORMAT(A.submission_date, '%d-%b-%Y'),  '".PAYMENT_EMPTY_INDICATOR."') AS soa_date_bavi",
					"IF(A.submission_date IS NOT NULL, DATE_FORMAT(A.submission_date, '%d-%b-%Y'),  '".PAYMENT_EMPTY_INDICATOR."')  AS submission_date",
					"IFNULL(I.soa_date_calambas, '".PAYMENT_EMPTY_INDICATOR."') AS soa_date_calamba",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"IFNULL(A.soa_num, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_number",
					"IF(A.submission_date IS NOT NULL, DATE_FORMAT(A.submission_date, '%d-%b-%Y'),  '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_date_bavi",
					"IFNULL(I.soa_date_calambas, '".PAYMENT_EMPTY_INDICATOR."')  convert_to soa_date_calamba",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"soa_number",
					"A.submission_date",
					"soa_date_calambas_raw",
					"F.apv_dates_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
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
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						AF.reference_id,
						GROUP_CONCAT(IF(AA.task_status_id = ?, 'N/A', DATE_FORMAT(AE.transmittal_date, '%d-%b-%Y')) SEPARATOR ',<br/>') AS soa_date_calambas,
						GROUP_CONCAT(IF(AA.task_status_id = ?, 'N/A', AE.transmittal_date)) AS soa_date_calambas_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_pria_workflows AF ON AB.pria_workflow_id = AF.pria_workflow_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_pria_references AD ON AF.reference_id = AD.soa_id AND AD.transmittal_id IS NOT NULL
					LEFT JOIN $this->tbl_transmittals AE ON AD.transmittal_id = AE.transmittal_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id AND A.soa_id = I.reference_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_goods_bavi_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(TRANS_TAB_PO));
			$values		= array_merge($values, array(CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES, CORE_TASK_PO_DR_BAVI_MARINADES, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_PO_GR_BAVI_MARINADES, CORE_TASK_PO_DR_BAVI_MARINADES, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_GOODS_BAVI));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"J.po_num AS po_number",
					"FORMAT(J.po_amount, 2) AS po_amount",
					"IFNULL(L.dr_nums, '".PAYMENT_EMPTY_INDICATOR."') AS dr_number",
					"IFNULL(M.gr_nums, '".PAYMENT_EMPTY_INDICATOR."') AS gr_number",
					"IF(A.soa_date IS NOT NULL, DATE_FORMAT(A.soa_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') AS invoice_date",
					"IFNULL(A.soa_date, '".PAYMENT_EMPTY_INDICATOR."') AS invoice_date_order",
					"IFNULL(A.soa_num, '".PAYMENT_EMPTY_INDICATOR."')  AS invoice_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"J.po_num convert_to po_number",
					"FORMAT(J.po_amount, 2) convert_to po_amount",
					"IFNULL(L.dr_nums, '".PAYMENT_EMPTY_INDICATOR."') convert_to dr_number",
					"IFNULL(M.gr_nums, '".PAYMENT_EMPTY_INDICATOR."') convert_to gr_number",
					"IF(A.soa_date IS NOT NULL, DATE_FORMAT(A.soa_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') convert_to invoice_date",
					"IFNULL(A.soa_num, '".PAYMENT_EMPTY_INDICATOR."') convert_to invoice_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"po_number",
					"po_amount",
					"dr_number",
					"gr_number",
					"A.soa_date",
					"invoice_number",
					"F.apv_dates_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			$group_by	= "";

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
				// $group_by		= 'GROUP BY E.payment_id, A.soa_id';
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN $this->tbl_pria_references I ON A.soa_id = I.soa_id
				LEFT JOIN $this->tbl_purchase_orders J ON I.po_id = J.po_id
				LEFT JOIN $this->tbl_pria_workflows K ON J.po_id = K.reference_id
				AND A.account_group_code = K.account_group_code AND K.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = K.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(AD.dr_num SEPARATOR ', ') AS dr_nums
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) L ON K.pria_workflow_id = L.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(AD.gr_num SEPARATOR ', ') AS gr_nums
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) M ON K.pria_workflow_id = M.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$group_by
				$having
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

    public function get_payment_goods_bffi_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_PO);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_PO));
			$values		= array_merge($values, array(CORE_TASK_PO_DR_APPROVE_BFFI, CORE_TASK_PO_DR_BFFI, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_PO_GR_BFFI, CORE_TASK_PO_DR_BFFI, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_PO_TRANSMIT_BFFI, CORE_TASK_PO_DR_BFFI, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_GOODS_BFFI));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.po_num AS po_number",
					"FORMAT(A.po_amount, 2) AS po_amount",
					"IFNULL(I.dr_nums, '".PAYMENT_EMPTY_INDICATOR."') AS dr_number",
					"IFNULL(J.gr_nums, '".PAYMENT_EMPTY_INDICATOR."') AS gr_number",
					"IFNULL(K.transmittal_dates, '".PAYMENT_EMPTY_INDICATOR."')  AS invoice_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"E.name convert_to business_center_name",
					"D.vendor_name convert_to vendor_name",
					"A.po_num convert_to po_number",
					"FORMAT(A.po_amount, 2) convert_to po_amount",
					"IFNULL(I.dr_nums, '".PAYMENT_EMPTY_INDICATOR."') convert_to dr_number",
					"IFNULL(J.gr_nums, '".PAYMENT_EMPTY_INDICATOR."') convert_to gr_number",
					"IFNULL(K.transmittal_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to invoice_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"po_number",
					"po_amount",
					"dr_number",
					"gr_number",
					"K.transmittal_dates_raw",
					"F.apv_dates_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.po_id) total';
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
				FROM $this->tbl_purchase_orders A
				JOIN $this->tbl_pria_references L ON A.po_id = L.po_id
				LEFT JOIN $this->tbl_pria_workflows B ON A.po_id = B.reference_id
				AND B.account_group_code IN (
					SELECT account_group_code FROM $this->tbl_purchase_requisitions
					WHERE pr_id = L.pr_id
				) AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.po_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(AD.dr_num SEPARATOR ', ') AS dr_nums
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(AD.gr_num SEPARATOR ', ') AS gr_nums
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) J ON B.pria_workflow_id = J.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AG.transmittal_date, '%d-%b-%Y') SEPARATOR ', ') AS transmittal_dates,
						GROUP_CONCAT(AG.transmittal_date SEPARATOR ', ') AS transmittal_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					LEFT JOIN $this->tbl_pria_references AF ON AD.dr_gr_id = AF.dr_gr_id
					LEFT JOIN $this->tbl_transmittals AG ON AF.transmittal_id = AG.transmittal_id
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) K ON B.pria_workflow_id = K.pria_workflow_id
				WHERE B.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_goods_marinades_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(TRANS_TAB_PO));
			$values		= array_merge($values, array(CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES, CORE_TASK_PO_DR_BAVI_MARINADES, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_PO_GR_BAVI_MARINADES, CORE_TASK_PO_DR_BAVI_MARINADES, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_GOODS_MARINADES));


			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"J.po_num AS po_number",
					"FORMAT(J.po_amount, 2) AS po_amount",
					"IFNULL(L.dr_nums, '".PAYMENT_EMPTY_INDICATOR."') AS dr_number",
					"IFNULL(M.gr_nums, '".PAYMENT_EMPTY_INDICATOR."') AS gr_number",
					"IF(A.soa_date IS NOT NULL, DATE_FORMAT(A.soa_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') AS invoice_date",
					"IFNULL(A.soa_date, '".PAYMENT_EMPTY_INDICATOR."') AS invoice_date_order",
					"IFNULL(A.soa_num, '".PAYMENT_EMPTY_INDICATOR."') AS invoice_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"J.po_num convert_to po_number",
					//"FORMAT(J.po_amount, 2) convert_to po_amount",
					"IFNULL(L.dr_nums, '".PAYMENT_EMPTY_INDICATOR."') convert_to dr_number",
					"IFNULL(M.gr_nums, '".PAYMENT_EMPTY_INDICATOR."') convert_to gr_number",
					"IF(A.soa_date IS NOT NULL, DATE_FORMAT(A.soa_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."') convert_to invoice_date",
					"IFNULL(A.soa_num, '".PAYMENT_EMPTY_INDICATOR."') convert_to invoice_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"po_number",
					//"po_amount",
					"dr_number",
					"gr_number",
					"A.soa_date",
					"invoice_number",
					"F.apv_dates_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			$group_by	= "";

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);

				// $group_by		= "GROUP BY F.payment_id, A.soa_id";
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN $this->tbl_pria_references I ON A.soa_id = I.soa_id
				LEFT JOIN $this->tbl_purchase_orders J ON I.po_id = J.po_id
				LEFT JOIN $this->tbl_pria_workflows K ON J.po_id = K.reference_id
				AND A.account_group_code = K.account_group_code AND K.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = K.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(AD.dr_num SEPARATOR ', ') AS dr_nums
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) L ON K.pria_workflow_id = L.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(AD.gr_num SEPARATOR ', ') AS gr_nums
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AD ON AA.pria_task_id = AD.pria_task_id
					LEFT JOIN $this->tbl_pria_tasks AE ON AA.pria_stage_id = AE.pria_stage_id AND AE.core_workflow_task_id = ?
					WHERE AA.core_workflow_task_id = ? AND AE.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) M ON K.pria_workflow_id = M.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$group_by
				$having
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

	// KPOYAOAN 2021-03-11
	// Changed to New Contracts only from contracts table
	// Lessors with Renewal Process
    /*public function get_payment_lessors_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(AG_LESSORS, TRANS_TAB_CONTRACTS);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_CONTRACTS));
			$values		= array_merge($values, array(CORE_TASK_CONTRACTS_UPLOAD, TASK_STATUS_DONE, TASK_STATUS_APPROVED));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.contract_code AS contract_number",
					"I.official_store_name AS store_name",
					"IFNULL(J.contract_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') AS contract_upload_date",
					"IFNULL(K.payment_term_name, '".PAYMENT_EMPTY_INDICATOR."')  AS payment_term_name",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.contract_code convert_to contract_number",
					"I.official_store_name convert_to store_name",
					"IFNULL(J.contract_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to contract_upload_date",
					"IFNULL(K.payment_term_name, '".PAYMENT_EMPTY_INDICATOR."') convert_to payment_term_name",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"contract_number",
					"store_name",
					"J.contract_upload_dates_raw",
					"payment_term_name",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.contract_id) total';
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
				FROM $this->tbl_contracts A
				LEFT JOIN $this->tbl_pria_workflows B ON A.contract_id = B.reference_id
				AND B.account_group_code = ? AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.contract_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN $this->tbl_sites I ON A.site_id = I.site_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS contract_upload_dates,
						GROUP_CONCAT(AA.actual_end_date) AS contract_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) J ON B.pria_workflow_id = J.pria_workflow_id
				LEFT JOIN $this->tbl_param_payment_terms K ON A.payment_term_code = K.payment_term_code
				WHERE (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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
	}*/

	public function get_payment_lessors_list($having, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_CONTRACTS, DOC_TYPE_SIGNED_RENEWAL, CONTRACT_STATUS_NEW, CONTRACT_STATUS_FOR_RENEWAL, CONTRACT_STATUS_OVERDUE, CONTRACT_STATUS_DUE_RENEWAL);

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.contract_code AS contract_number",
					"I.official_store_name AS store_name",
					"IF(A.original_contract_id IS NULL OR A.original_contract_id = '', A.created_date, L.created_date) AS contract_upload_date_raw",
					"IF(A.original_contract_id IS NULL OR A.original_contract_id = '', DATE_FORMAT(A.created_date, '%d-%b-%Y'), IF(L.created_date IS NOT NULL, DATE_FORMAT(L.created_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."')) AS contract_upload_date",
					"IFNULL(K.payment_term_name, '".PAYMENT_EMPTY_INDICATOR."')  AS payment_term_name",
					"IF(E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF(E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF(E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF(E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF(E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF(E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF(E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.contract_code convert_to contract_number",
					"I.official_store_name convert_to store_name",
					"IF(A.original_contract_id IS NULL OR A.original_contract_id = '', DATE_FORMAT(A.created_date, '%d-%b-%Y'), IF(L.created_date IS NOT NULL, DATE_FORMAT(L.created_date, '%d-%b-%Y'), '".PAYMENT_EMPTY_INDICATOR."')) convert_to contract_upload_date",
					"IFNULL(K.payment_term_name, '".PAYMENT_EMPTY_INDICATOR."') convert_to payment_term_name",
					"IF(E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF(E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF(E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF(E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF(E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF(E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF(E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"contract_number",
					"store_name",
					"contract_upload_date_raw",
					"payment_term_name",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.contract_id) total';
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
				FROM $this->tbl_contracts A
				LEFT JOIN $this->tbl_payments E ON A.contract_id = E.reference_id
				AND A.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN $this->tbl_sites I ON A.site_id = I.site_id
				LEFT JOIN $this->tbl_param_payment_terms K ON A.payment_term_code = K.payment_term_code
				LEFT JOIN $this->tbl_documents L ON A.contract_id = L.reference
				AND A.account_group_code = L.account_group_code AND L.document_type_code = ?
				WHERE (A.contract_status_code IN (?, ?, ?, ?) OR F.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_manpower_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_UPLOAD_TRUCKERS_MANPOWER, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_MANPOWER));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') AS soa_upload_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) AS soa_period",
					"A.date_from AS soa_period_order",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) AS soa_period",
					"A.soa_num AS soa_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_upload_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) convert_to soa_period",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) convert_to soa_period",
					"A.soa_num convert_to soa_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"I.soa_upload_dates_raw",
					"A.date_from",
					//"soa_period",
					"soa_number",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$order			.= ", soa_period_order";
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_upload_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_manpower_adv_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$having	= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values	= array(
					CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED, TASK_STATUS_DONE, TASK_STATUS_APPROVED,
					CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED, TASK_STATUS_DONE, TASK_STATUS_APPROVED,
					AG_MANPOWER, APV_REF_TYPE_CODE_SOA
			);

			$fields				=  array(
					"NULL AS business_center_name",
					"F.vendor_name AS vendor_name",
					"NULL AS cms_number",
					"H.soa_upload_dates AS soa_upload_date",
					"CONCAT_WS(' - ', DATE_FORMAT(C.date_from, '%d-%b-%Y'), DATE_FORMAT(C.date_to, '%d-%b-%Y')) AS soa_period",
					"C.soa_num AS soa_number",
					"I.soa_approval_dates AS acknowledgement_date",
					"B.apv_nums AS apv_number",
					"B.apv_dates AS apv_date",
					"B.apv_amounts AS apv_amount",
					"B.cv_nums AS check_number",
					"B.cv_amounts AS check_amount",
					"B.apv_particulars AS apv_particular",
					"B.apv_statuses AS apv_status"
			);

			$filters			= array(
					"NULL convert_to business_center_name",
					"F.vendor_name convert_to vendor_name",
					"NULL convert_to cms_number",
					"H.soa_upload_dates convert_to soa_upload_date",
					"CONCAT_WS(' - ', DATE_FORMAT(C.date_from, '%d-%b-%Y'), DATE_FORMAT(C.date_to, '%d-%b-%Y')) convert_to soa_period",
					"C.soa_num convert_to soa_number",
					"I.soa_approval_dates convert_to acknowledgement_date",
					"B.apv_nums convert_to apv_number",
					"B.apv_dates convert_to apv_date",
					"B.apv_amounts convert_to apv_amount",
					"B.cv_nums convert_to check_number",
					"B.cv_amounts convert_to check_amount",
					"B.apv_particulars convert_to apv_particular",
					"B.apv_statuses convert_to apv_status"
			);

			$filters_ordering	= array(
					"business_center_name",
					"vendor_name",
					"cms_number",
					"soa_upload_date",
					"soa_period",
					"soa_number",
					"acknowledgement_date",
					"apv_date",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_status"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.payment_id) total';
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
				FROM $this->tbl_payments A
				LEFT JOIN (
					SELECT
						A.payment_id,
						GROUP_CONCAT(IFNULL(A.apv_num, '---') SEPARATOR '<hr/>') AS apv_nums,
						GROUP_CONCAT(IFNULL(DATE_FORMAT(A.apv_date, '%d-%b-%Y'), '---') SEPARATOR '<hr/>') AS apv_dates,
						GROUP_CONCAT(IFNULL(FORMAT(A.apv_amount, 2), '---') SEPARATOR '<hr/>') AS apv_amounts,
						GROUP_CONCAT(IFNULL(A.cv_num, '---') SEPARATOR '<hr/>') AS cv_nums,
						GROUP_CONCAT(IFNULL(FORMAT(A.cv_amount, 2), '---') SEPARATOR '<hr/>') AS cv_amounts,
						GROUP_CONCAT(IFNULL(A.particulars, '---') SEPARATOR '<hr/>') AS apv_particulars,
						GROUP_CONCAT(IFNULL(B.apv_status_name, '---') SEPARATOR '<hr/>') AS apv_statuses
					FROM $this->tbl_payment_apvs A
					LEFT JOIN $this->tbl_param_apv_status B ON A.apv_status_code = B.apv_status_id
					GROUP BY A.payment_id
				) B ON A.payment_id = B.payment_id
				LEFT JOIN $this->tbl_soa C ON A.reference_id = C.soa_id
                -- LEFT JOIN $this->tbl_sites D ON C.site_code = D.site_code
                -- LEFT JOIN $this->tbl_organizations E ON D.org_code = E.org_code AND E.org_type_code = ?
                LEFT JOIN $this->tbl_vendors F ON C.vendor_code = F.vendor_code
				LEFT JOIN $this->tbl_pria_workflows G ON A.account_group_code = G.account_group_code AND C.soa_id = G.reference_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_upload_dates
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) H ON G.pria_workflow_id = H.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_approval_dates
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON G.pria_workflow_id = I.pria_workflow_id
				WHERE A.account_group_code = ? AND A.reference_type_code IN (?)
				$where
				$having
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

    public function get_payment_toll_partners_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_TOLL_PARTNERS));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.soa_num AS soa_number",
					//"A.week_num AS week_num",
					"DATE_FORMAT(A.submission_date, '%d-%b-%Y') AS soa_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) AS soa_period",
					"A.date_from AS soa_period_order",
					"I.soa_approval_dates AS acknowledgement_date",
					"A.submission_date AS soa_date_order",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.soa_num convert_to soa_number",
					//"A.week_num convert_to week_num",
					"DATE_FORMAT(A.submission_date, '%d-%b-%Y') convert_to soa_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) convert_to soa_period",
					"I.soa_approval_dates convert_to acknowledgement_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"soa_number",
					//"week_num",
					"soa_date",
					"A.date_from",
					"I.soa_approval_dates_raw",
					"F.apv_dates_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$order			.= ", soa_period_order";
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_approval_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_trucker_feedmills_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_UPLOAD_TRUCKERS_FEEDMILL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_FEEDMILL));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.soa_num AS soa_number",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) AS soa_period",
					"A.date_from AS soa_period_order",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) AS soa_period",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') AS soa_upload_date",
					"IFNULL(J.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."')  AS soa_approval_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.soa_num convert_to soa_number",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) convert_to soa_period",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) convert_to soa_period",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_upload_date",
					"IFNULL(J.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_approval_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"soa_number",
					"soa_period_order",
					"I.soa_upload_dates_raw",
					"J.soa_approval_dates_raw",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$order			.= ", soa_period_order";
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_upload_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_approval_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) J ON B.pria_workflow_id = J.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_trucker_inbound_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_INBOUND_NORMAL));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.soa_num AS soa_number",
					"DATE_FORMAT(A.soa_date, '%d-%b-%Y') AS soa_date",
					"IFNULL(A.soa_date, '".PAYMENT_EMPTY_INDICATOR."') AS soa_date_order",
					"IFNULL(I.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') AS soa_approval_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.soa_num convert_to soa_number",
					"DATE_FORMAT(A.soa_date, '%d-%b-%Y') convert_to soa_date",
					"IFNULL(I.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_approval_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"soa_number",
					"A.soa_date",
					"I.soa_approval_dates_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
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
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_approval_dates,
						AA.actual_end_date AS soa_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_trucker_inbound_centralized_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_INBOUND_CENTRAL));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.soa_num AS soa_number",
					"DATE_FORMAT(A.soa_date, '%d-%b-%Y') AS soa_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) AS soa_period",
					"A.date_from AS soa_period_order",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) AS soa_period",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') AS soa_upload_date",
					"IFNULL(J.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') AS acknowledgement_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.soa_num convert_to soa_number",
					"DATE_FORMAT(A.soa_date, '%d-%b-%Y') convert_to soa_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) convert_to soa_period",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) convert_to soa_period",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_upload_date",
					"IFNULL(J.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to acknowledgement_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"soa_number",
					"soa_period_order",
					//"soa_period",
					"I.soa_upload_dates_raw",
					"J.soa_approval_dates_raw",
					"F.apv_date_raw",
					"apv_number",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$order			.= ", soa_period_order";
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_upload_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_approval_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) J ON B.pria_workflow_id = J.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_trucker_outbound_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_OUTBOUND));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"A.soa_num AS soa_number",
					"DATE_FORMAT(A.soa_date, '%d-%b-%Y') AS soa_date",
					"A.soa_date AS soa_date_order",
					"IFNULL(I.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') AS acknowledgement_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"A.soa_num convert_to soa_number",
					"DATE_FORMAT(A.soa_date, '%d-%b-%Y') convert_to soa_date",
					"IFNULL(I.soa_approval_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to acknowledgement_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"soa_number",
					"soa_date_order",
					"I.soa_approval_dates_raw",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
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
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_approval_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_approval_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

    public function get_payment_soa_based_list($having, $core_task_params, $params=NULL)
    {
		try
		{
			// Initialize variables
			$where = $filter = $order = $limit = "";

			$q_marks	= (ISSET($core_task_params['q_marks']) AND !EMPTY($core_task_params['q_marks']))? $core_task_params['q_marks']: "?";
			$q_values	= (ISSET($core_task_params['q_values']) AND !EMPTY($core_task_params['q_values']))? $core_task_params['q_values']: array(0);

			$having		= str_replace('org_code', 'A.org_code', str_replace('vendor_code', 'A.vendor_code', $having));

			$values		= array(TRANS_TAB_SOA);
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(ENUM_YES));
			$values		= array_merge($values, $q_values);
			$values		= array_merge($values, array(TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(APV_REF_TYPE_CODE_SOA));
			$values		= array_merge($values, array(CORE_TASK_SOA_UPLOAD_SOA_BASED, TASK_STATUS_DONE, TASK_STATUS_APPROVED));
			$values		= array_merge($values, array(AG_SOA_BASED));

			$fields				=  array(
					"G.name AS business_center_name",
					"H.vendor_name AS vendor_name",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') AS soa_upload_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) AS soa_period",
					"A.date_from AS soa_period_order",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) AS soa_period",
					"A.soa_num AS soa_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) AS apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) AS apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) AS apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) AS check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) AS check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) AS apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'For Payment Processing', F.apv_statuses) AS apv_status",
					"E.created_date"
			);

			$filters			= array(
					"G.name convert_to business_center_name",
					"H.vendor_name convert_to vendor_name",
					"IFNULL(I.soa_upload_dates, '".PAYMENT_EMPTY_INDICATOR."') convert_to soa_upload_date",
					"CONCAT_WS(' - ', DATE_FORMAT(A.date_from, '%d-%b-%Y'), DATE_FORMAT(A.date_to, '%d-%b-%Y')) convert_to soa_period",
					//"FLOOR(DATEDIFF(A.date_to, A.date_from)/7) convert_to soa_period",
					"A.soa_num convert_to soa_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_nums) convert_to apv_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_dates) convert_to apv_date",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_amounts) convert_to apv_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_nums) convert_to check_number",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.cv_amounts) convert_to check_amount",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, '---', F.apv_particulars) convert_to apv_particular",
					"IF((C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL) AND E.payment_id IS NULL, 'NULL', F.apv_statuses_id) convert_to apv_statuses_id"
			);

			$filters_ordering	= array(
					"E.created_date",
					"business_center_name",
					"vendor_name",
					"I.soa_upload_dates_raw",
					"soa_period_order",
					//"soa_period",
					"soa_number",
					"apv_number",
					"F.apv_dates_raw",
					"apv_amount",
					"check_number",
					"check_amount",
					"apv_particular",
					"apv_statuses_id"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.soa_id) total';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				//$order			.= ", soa_period_order";
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);
			}

			$query =<<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					AND AY.core_workflow_task_id IN ($q_marks) AND AY.task_status_id IN (?, ?)
					WHERE AY.pria_task_id IS NOT NULL
				) C ON B.pria_workflow_id = C.pria_workflow_id
				AND C.core_workflow_stage_id NOT IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
					)
				)
				LEFT JOIN (
					SELECT AZ.pria_workflow_id, AZ.pria_stage_id, AZ.core_workflow_stage_id
					FROM $this->tbl_pria_workflow_stages AZ
					LEFT JOIN $this->tbl_pria_tasks AY ON AZ.pria_stage_id = AY.pria_stage_id
					LEFT JOIN $this->tbl_delivery_goods_receipt AX ON AY.pria_task_id = AX.pria_task_id and AX.last_dr_flag = ?
					LEFT JOIN $this->tbl_pria_tasks AW ON AY.pria_stage_id = AW.pria_stage_id
					AND AW.core_workflow_task_id IN ($q_marks) AND AW.task_status_id IN (?, ?)
					WHERE AW.pria_task_id IS NOT NULL
				) D ON B.pria_workflow_id = D.pria_workflow_id
				AND D.core_workflow_stage_id IN (
					SELECT CC.workflow_stage_id FROM $this->tbl_pria_task_appendable AA
					JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
					JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
					WHERE AA.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
					UNION
					SELECT EE.workflow_stage_id FROM $this->tbl_pria_task_appendable DD
					JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
					WHERE DD.pria_task_id IN (
						SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = D.pria_stage_id
					)
				)
				LEFT JOIN $this->tbl_payments E ON A.soa_id = E.reference_id AND B.account_group_code = E.account_group_code AND E.reference_type_code = ?
				LEFT JOIN (
					SELECT
						FF.payment_id,
						IFNULL(FF.apv_num, '---') AS apv_nums,
						IFNULL(DATE_FORMAT(FF.apv_date, '%d-%b-%Y'), '---') AS apv_dates,
						FF.apv_date AS apv_dates_raw,
						IFNULL(FORMAT(FF.apv_amount, 2), '---') AS apv_amounts,
						IFNULL(FF.cv_num, '---') AS cv_nums,
						IFNULL(FORMAT(FF.cv_amount, 2), '---') AS cv_amounts,
						IFNULL(FF.particulars, '---') AS apv_particulars,
						IFNULL(GG.apv_status_name, '---') AS apv_statuses,
						GG.apv_status_id AS apv_statuses_id
					FROM $this->tbl_payment_apvs FF
					LEFT JOIN $this->tbl_param_apv_status GG ON FF.apv_status_code = GG.apv_status_id
				) F ON E.payment_id = F.payment_id
				LEFT JOIN $this->tbl_organizations G ON A.org_code = G.org_code
				LEFT JOIN $this->tbl_vendors H ON A.vendor_code = H.vendor_code
				LEFT JOIN (
					SELECT
						AB.pria_workflow_id,
						GROUP_CONCAT(DATE_FORMAT(AA.actual_end_date, '%d-%b-%Y') SEPARATOR ', ') AS soa_upload_dates,
						GROUP_CONCAT(AA.actual_end_date) AS soa_upload_dates_raw
					FROM $this->tbl_pria_tasks AA
					LEFT JOIN $this->tbl_pria_workflow_stages AB ON AA.pria_stage_id = AB.pria_stage_id
					LEFT JOIN $this->tbl_users AC ON AA.user_id = AC.user_id
					WHERE AA.core_workflow_task_id = ? AND AA.task_status_id IN (?, ?)
					GROUP BY AB.pria_workflow_id
				) I ON B.pria_workflow_id = I.pria_workflow_id
				WHERE A.account_group_code = ? AND (C.pria_stage_id IS NOT NULL OR D.pria_stage_id IS NOT NULL OR E.payment_id IS NOT NULL)
				$where
				$having
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

	public function update_reminder($fields, $where)
	{
		try
		{
			return $this->update_data($this->tbl_core_notifications, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}