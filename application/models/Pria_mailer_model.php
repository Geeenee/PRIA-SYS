<?php if (!defined('BASEPATH')) exit('No direct script access is allowed');

class Pria_mailer_model extends Portal_Model
{
	public $tbl_param_account_groups;
	public $tbl_param_document_types;

	public $tbl_pria_email_templates;
	public $tbl_pria_mail_notifications;
	public $tbl_pria_mail_notification_recipients;

	public $tbl_boq;
	public $tbl_boq_asset_codes;
	public $tbl_boq_pr;
	public $tbl_contracts;
	public $tbl_delivery_goods_receipt;
	public $tbl_internal_orders;
	public $tbl_projects;
	public $tbl_project_types;
	public $tbl_purchase_orders;
	public $tbl_purchase_requisitions;
	public $tbl_soa;
	public $tbl_document_transmittals;

	public $tbl_pria_references;
	public $tbl_pria_task_comments;
	public $tbl_pria_task_actions;
	public $tbl_pria_task_document_types;
	public $tbl_pria_task_email_links;
	public $tbl_pria_task_predecessors;
	public $tbl_pria_task_roles;
	public $tbl_pria_tasks;
	public $tbl_pria_workflow_stages;
	public $tbl_pria_workflows;

	public $tbl_pria_organizations;
	public $tbl_documents;
	public $tbl_sites;
	public $tbl_vendors;
	public $tbl_vendor_users;

	public $tbl_users;
	public $tbl_user_orgs;
	public $tbl_user_roles;
	public $tbl_roles;

	public $tbl_sys_param;
	public $tbl_workflows;
	public $tbl_workflow_stages;
	public $tbl_workflow_stage_tasks;

	public $tbl_workflow_task_predecessors;
	public $tbl_workflow_task_roles;

	public function __construct()
	{
		parent::__construct();

		$this->tbl_param_account_groups					= parent::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
		$this->tbl_param_document_types					= parent::PORTAL_TABLE_PARAM_DOCUMENT_TYPES;

		$this->tbl_pria_email_templates					= parent::PORTAL_TABLE_PRIA_EMAIL_TEMPLATES;
		$this->tbl_pria_mail_notifications				= parent::PORTAL_TABLE_PRIA_MAIL_NOTIFICATIONS;
		$this->tbl_pria_mail_notification_recipients	= parent::PORTAL_TABLE_PRIA_MAIL_NOTIFICATION_RECIPIENTS;

		$this->tbl_boq									= parent::PORTAL_TABLE_PRIA_BOQ;
		$this->tbl_boq_asset_codes						= parent::PORTAL_TABLE_PRIA_BOQ_ASSET;
		$this->tbl_boq_pr								= parent::PORTAL_TABLE_PRIA_BOQ_PR;
		$this->tbl_contracts							= parent::PORTAL_TABLE_CONTRACTS;
		$this->tbl_delivery_goods_receipt				= parent::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
		$this->tbl_internal_orders						= parent::PORTAL_TABLE_INTERNAL_ORDERS;
		$this->tbl_projects								= parent::PORTAL_TABLE_PROJECTS;
		$this->tbl_project_types						= parent::PORTAL_TABLE_PROJECT_TYPES;
		$this->tbl_purchase_orders						= parent::PORTAL_TABLE_PURCHASE_ORDERS;
		$this->tbl_purchase_requisitions				= parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
		$this->tbl_soa									= parent::PORTAL_TABLE_SOA;
		$this->tbl_document_transmittals				= parent::PORTAL_TABLE_DOCUMENT_TRANSMITTALS;

		$this->tbl_pria_references						= parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
		$this->tbl_pria_task_comments					= parent::PORTAL_TABLE_PRIA_TASK_COMMENTS;
		$this->tbl_pria_task_actions					= parent::PORTAL_TABLE_PRIA_TASK_ACTIONS;
		$this->tbl_pria_task_document_types 			= parent::PORTAL_TABLE_PRIA_TASK_DOCUMENT_TYPES;
		$this->tbl_pria_task_email_links 				= parent::PORTAL_TABLE_PRIA_TASK_EMAIL_LINKS;
		$this->tbl_pria_task_predecessors				= parent::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
		$this->tbl_pria_task_roles						= parent::PORTAL_TABLE_PRIA_TASK_ROLES;
		$this->tbl_pria_task_forms						= parent::PORTAL_TABLE_PRIA_TASK_FORMS;
		$this->tbl_pria_tasks							= parent::PORTAL_TABLE_PRIA_TASKS;
		$this->tbl_pria_workflow_stages					= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
		$this->tbl_pria_workflows						= parent::PORTAL_TABLE_PRIA_WORKFLOWS;

		$this->tbl_pria_organizations					= parent::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_documents							= parent::PORTAL_TABLE_DOCUMENTS;
		$this->tbl_sites								= parent::PORTAL_TABLE_SITES;
		$this->tbl_vendors								= parent::PORTAL_TABLE_VENDORS;
		$this->tbl_vendor_users							= parent::PORTAL_TABLE_VENDOR_USERS;

		$this->tbl_users								= parent::CORE_TABLE_USERS;
		$this->tbl_user_orgs							= parent::PORTAL_TABLE_USER_ORGS;
		$this->tbl_user_roles							= parent::CORE_USER_ROLES;
		$this->tbl_roles								= parent::CORE_ROLES;

		$this->tbl_sys_param							= parent::CORE_SYS_PARAM;
		$this->tbl_workflows							= parent::CORE_WORKFLOWS;
		$this->tbl_workflow_stages						= parent::CORE_WORKFLOW_STAGES;
		$this->tbl_workflow_stage_tasks					= parent::CORE_WORKFLOW_STAGE_TASKS;

		$this->tbl_workflow_task_predecessors			= parent::CORE_WORKFLOW_TASK_PREDECESSORS;
		$this->tbl_workflow_task_roles					= parent::CORE_WORKFLOW_TASK_ROLES;

		$this->tbl_email_notification_queues			= parent::CORE_EMAIL_NOTIFICATION_QUEUES;

	}

	public function get_notification_type()
	{
		try
		{

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_notification_setups($setup_params)
	{
		try
		{
			$where					= "";
			$values					= array($setup_params['trigger'], $setup_params['sched'], ENUM_YES);

			if(ISSET($setup_params['type']) AND !EMPTY($setup_params['type']))
			{
				$where				.= " AND A.email_notification_type = ?";
				$values[]			= $setup_params['type'];
			}

			if(ISSET($setup_params['core_task_id']) AND !EMPTY($setup_params['core_task_id']))
			{
				$where				.= " AND A.core_workflow_task_id = ?";
				$values[]			= $setup_params['core_task_id'];
			}

			if(ISSET($setup_params['task_action']) AND !EMPTY($setup_params['task_action']))
			{
				$where				.= " AND A.email_notification_task_action = ?";
				$values[]			= $setup_params['task_action'];
			}
			else
			{
				$where				.= " AND A.email_notification_task_action IS NULL";
			}

			$query	= <<<EOS
				SELECT
				A.email_notification_id, A.email_notification_trigger, A.email_notification_sched, A.email_notification_type, A.core_workflow_task_id, A.email_notification_task_action, A.email_notification_title, A.email_notification_subject, A.email_template_id, A.email_detail_template_id, A.email_detail_extend_template_id, A.email_notification_attachment, A.email_notification_send_to, A.email_notification_filtered
				FROM $this->tbl_pria_mail_notifications A
				WHERE A.email_notification_trigger = ? AND A.email_notification_sched = ? AND A.email_notification_active = ?
				$where
EOS;

			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_notification_template($template_params)
	{
		try
		{
			$where_core_task		= "";
			$values					= array($template_params['trigger'], $template_params['type'], $template_params['sched']);

			if(ISSET($template_params['core_task_id']) AND !EMPTY($template_params['core_task_id']))
			{
				$where_core_task	= "AND A.core_workflow_task_id = ?";
				$values[]			= $template_params['core_task_id'];
			}

			$query	= <<<EOS
				SELECT
				A.email_notification_id, A.email_notification_subject, A.email_template_id, A.email_notification_title, A.email_detail_template_id, A.email_detail_extend_template_id, A.email_notification_attachment, A.email_notification_send_to
				FROM $this->tbl_pria_mail_notifications A
				WHERE A.email_notification_trigger = ? AND A.email_notification_type = ? AND A.email_notification_sched = ?
				$where_core_task
EOS;

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_email_template_details($email_template_id)
	{
		try
		{
			$values	= array($email_template_id);

			$query	= <<<EOS
				SELECT
				A.email_template_id, A.email_template_subject, A.email_template_content, A.email_template_parameter, A.email_template_type
				FROM $this->tbl_pria_email_templates A
				WHERE A.email_template_id = ?
EOS;

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_users_per_task($pria_task_id, $main_role=NULL, $org_codes=array())
	{
		try
		{
			$o_marks	= "";
			$where_role	= "";
			$where_org	= "";
			$join_sql	= "";
			$values		= array($pria_task_id, TASK_ROLE_VENDOR);

			if($main_role !== NULL)
			{
				$where_role	= " AND AA.actor_flag = ?";
				$values[]	= $main_role;
			}

			$values[]	= STATUS_ACTIVE;

			if(COUNT($org_codes) > 0)
			{
				foreach($org_codes AS $key => $org_code)
				{
					$o_marks	.= (!EMPTY($o_marks))? ", ?": "?";
					$values[]	= $org_code;
				}

				$join_sql		=<<<EOS
						JOIN $this->tbl_user_orgs C ON B.user_id = C.user_id
EOS;

				$where_org		= " AND C.org_code IN ($o_marks)";
			}

			$query	= <<<EOS
				SELECT
				A.user_id
				FROM $this->tbl_user_roles A
				JOIN $this->tbl_users B ON A.user_id = B.user_id
				$join_sql
				WHERE A.role_code IN (
					SELECT AA.role_code
					FROM $this->tbl_pria_task_roles AA
					WHERE AA.pria_task_id = ? AND AA.role_code != ? $where_role
				) AND B.status = ? $where_org
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendor_users_per_task($pria_task_id, $vendor_code, $main_role=NULL)
	{
		try
		{
			$where_role	= "";
			$values		= array($vendor_code, $pria_task_id, TASK_ROLE_VENDOR);

			if($main_role !== NULL)
			{
				$where_role	= " AND AA.actor_flag = ?";
				$values[]	= $main_role;
			}

			$values[]	= STATUS_ACTIVE;

			$query	= <<<EOS
				SELECT
				A.user_id
				FROM $this->tbl_vendor_users A
				JOIN $this->tbl_users B ON A.user_id = B.user_id
				WHERE A.vendor_code = ? AND 1 = (
					SELECT if(AA.pria_task_id IS NULL, 0, 1)
					FROM $this->tbl_pria_task_roles AA
					WHERE AA.pria_task_id = ? AND AA.role_code = ? $where_role
				) AND B.status = ?
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_task_details($pria_task_id, $template_type=NULL, $print=FALSE)
	{
		try
		{
			$join_fields	= "";
			$join_sql		= "";
			$join_where		= "";

			$values			= array(1);

			if(!EMPTY($template_type))
			{
				switch($template_type)
				{	
					case EMAIL_NOTIF_SUB_DTR_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_DTR_TASK_RETURNED:
					case EMAIL_NOTIF_SUB_DTR_FOR_APPROVAL_REVIEW_DETAILED_RESUBMIT:
					case EMAIL_NOTIF_SUB_DTR_FOR_APPROVAL_REVIEW_DETAILED:
						$join_fields	=<<<EOS
								, K.org_code
								, L.org_type_code
								, K.document_tracer_batch_number
								, L.name AS business_center_name
								, K.vendor_code
								, M.vendor_name
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
								, IFNULL(DATE_FORMAT(K.transmittal_date, '%M %d, %Y'), 'N/A') AS transmittal_date
								, IFNULL(DATE_FORMAT(K.release_date, '%M %d, %Y'), 'N/A') AS release_date
								, K.transmittal_document_sender
								, K.courier_tracking_number
								, P.pria_task_comment AS return_remarks
EOS;

						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_document_transmittals K ON C.reference_id = K.document_transmittal_id
								LEFT JOIN $this->tbl_pria_organizations L ON K.org_code = L.org_code
								LEFT JOIN $this->tbl_vendors M ON K.vendor_code = M.vendor_code
								LEFT JOIN $this->tbl_documents N ON K.document_transmittal_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
								LEFT JOIN $this->tbl_pria_task_comments P ON A.pria_task_id = P.pria_task_id AND P.created_date = (
									SELECT created_date
									FROM $this->tbl_pria_task_comments
									WHERE pria_task_id = A.pria_task_id
									ORDER BY created_date DESC
									LIMIT 1
								)
EOS;
					break;
					case EMAIL_NOTIF_SUB_IO_TASK_COMPLETED:
						/*
							ORIGINAL CODE IN JOIN_SQL

								LEFT JOIN $this->tbl_documents N ON J.io_id = N.reference

							BUT CHANGED IT TO

								LEFT JOIN $this->tbl_documents N ON A.pria_task_id = N.pria_task_id

							BECAUSE WALANG NAKUKUHA PAG DOC DR
						*/

						$join_fields	=<<<EOS
								, J.org_code
								, L.org_type_code
								, L.name AS business_center_name
								, J.vendor_code AS vendor_code
								, M.vendor_name
								, GROUP_CONCAT(DISTINCT IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(DISTINCT IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(DISTINCT IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_internal_orders J ON C.reference_id = J.io_id
								LEFT JOIN $this->tbl_sites K ON J.site_code = K.site_code
								LEFT JOIN $this->tbl_pria_organizations L ON J.org_code = L.org_code
								LEFT JOIN $this->tbl_vendors M ON J.vendor_code = M.vendor_code
								LEFT JOIN $this->tbl_documents N ON A.pria_task_id = N.pria_task_id
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;

					case EMAIL_NOTIF_SUB_SN_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_SN_FOR_APPROVAL_REVIEW_DETAILED:
					case EMAIL_NOTIF_SUB_SN_FOR_APPROVAL_W_ACTION:
					case EMAIL_NOTIF_SUB_SN_FOR_APPROVAL_FINAL_W_ACTION:
					case EMAIL_NOTIF_SUB_SN_TASK_SIMPLE:

						if($template_type != EMAIL_NOTIF_SUB_SN_TASK_SIMPLE)
						{
							$join_fields	=<<<EOS
								, K.sn_recommendation AS recommendation
								, K.sn_recommendation
								, K.sn_recommendation_bh
								, K.sn_recommendation_rh
EOS;
						}

						$join_fields	.=<<<EOS
								, K.org_code
								, L.org_type_code
								, L.name AS business_center_name
								, K.suggested_store_name
								, K.official_store_name
								, K.site_code
								, K.cost_center_code
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;

						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_sites K ON C.reference_id = K.site_id
								LEFT JOIN $this->tbl_pria_organizations L ON K.org_code = L.org_code
								LEFT JOIN $this->tbl_documents N ON K.site_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;
					case EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_SOA_FOR_APPROVAL_REVIEW_DETAILED:
						$join_fields	=<<<EOS
								, K.org_code
								, L.org_type_code
								, K.soa_num
								, L.name AS business_center_name
								, K.vendor_code
								, M.vendor_name
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
								, DATE_FORMAT(K.soa_date, '%M %d, %Y') AS soa_date
								, IFNULL(FORMAT(K.soa_amount, 2), 'N/A') as soa_amount
EOS;

						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_soa K ON C.reference_id = K.soa_id
								LEFT JOIN $this->tbl_pria_organizations L ON K.org_code = L.org_code
								LEFT JOIN $this->tbl_vendors M ON K.vendor_code = M.vendor_code
								LEFT JOIN $this->tbl_documents N ON K.soa_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;

					case EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_REVIEW_DETAILED:
					case EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_W_ACTION:
					case EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_FINAL_W_ACTION:
						$join_fields	=<<<EOS
								, K.org_code
								, L.org_type_code
								, K1.boq_code
								, L.name AS business_center_name
								, K.official_store_name
								, IFNULL(K1.new_contractor, K2.vendor_name) as recommended_vendor
								, FORMAT(K1.recommended_amount, 2) AS recommended_amount
								, FORMAT(K1.final_amount, 2) AS final_amount
								, M.vendor_name AS awarded_contractor
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(N.document_type_code, '') SEPARATOR ',') AS document_type_codes
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
								, K1.budgeted_flag
								, K1.additional_flag
								, FORMAT(K1.reco_amount_civil_works, 2) AS reco_amount_civil_works
								, FORMAT(K1.reco_amount_signage, 2) AS reco_amount_signage
								, IFNULL(K1.budget_amount_civil_works, 'N/A') budget_amount_civil_works
								, IFNULL(K1.budget_amount_signage, 'N/A') budget_amount_signage
								, FORMAT(K1.final_amount_civil_works, 2) AS final_amount_civil_works
								, FORMAT(K1.final_amount_signage, 2) AS final_amount_signage
								, K1.boq_recommendation
								, K1.boq_recommendation_bh
								, K1.boq_recommendation_rh
								, K1.boq_justification
								, K1.layout_specifications
EOS;

						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_boq K1 ON C.reference_id = K1.boq_id
								LEFT JOIN $this->tbl_vendors K2 ON K1.recommended_vendor_code = K2.vendor_code
								LEFT JOIN $this->tbl_sites K ON K1.site_id = K.site_id
								LEFT JOIN $this->tbl_pria_organizations L ON K.org_code = L.org_code
								LEFT JOIN $this->tbl_documents N ON K1.boq_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
								LEFT JOIN (
									SELECT AA.boq_id, GROUP_CONCAT(BB.vendor_name SEPARATOR ', ') AS vendor_name
									FROM $this->tbl_boq_asset_codes AA
									LEFT JOIN $this->tbl_vendors BB ON AA.confirmed_contractor = BB.vendor_code
									GROUP BY AA.boq_id
								) M ON K1.boq_id = M.boq_id
EOS;
					break;

					case EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED:
						$join_fields	=<<<EOS
								, K.org_code
								, L.org_type_code
								, L.name AS business_center_name
								, M.vendor_name AS vendor_name
								, K.official_store_name
								, DATE_FORMAT(K1.mobilization_date, '%M %d, %Y') AS mobilization_date
								, DATE_FORMAT(K1.turnover_date, '%M %d, %Y') AS turnover_date
								, DATE_FORMAT(K1.opening_date, '%M %d, %Y') AS opening_date
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;

						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_projects K1 ON C.reference_id = K1.project_id
								LEFT JOIN $this->tbl_sites K ON K1.site_id = K.site_id
								LEFT JOIN $this->tbl_pria_organizations L ON K.org_code = L.org_code
								LEFT JOIN (
									SELECT AA.project_id, GROUP_CONCAT(DD.vendor_name SEPARATOR ', ') AS vendor_name
									FROM $this->tbl_projects AA
									LEFT JOIN $this->tbl_project_types BB ON AA.project_id = BB.project_id
									LEFT JOIN $this->tbl_boq_asset_codes CC ON AA.boq_id = CC.boq_id AND BB.project_type_code = CC.asset_type
									LEFT JOIN $this->tbl_vendors DD ON CC.confirmed_contractor = DD.vendor_code
									GROUP BY AA.project_id
								) M ON K1.project_id = M.project_id
								LEFT JOIN $this->tbl_documents N ON K1.project_id = N.reference AND N.pria_stage_id = A.pria_stage_id
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;

					case EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED_W_FINAL_AMOUNT:
					case EMAIL_NOTIF_SUB_BOQ_W_FINAL_AMOUNT:
						$join_fields	=<<<EOS
								, K.org_code
								, L.org_type_code
								, K1.boq_code
								, L.name AS business_center_name
								, K.official_store_name
								, M.file_name as boq_file
								, M.sys_file_name as boq_sys_file
								, IFNULL(K1.new_contractor, K2.vendor_name) as recommended_vendor
								, K1.final_amount
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_boq K1 ON C.reference_id = K1.boq_id
								LEFT JOIN $this->tbl_vendors K2 ON K1.recommended_vendor_code = K2.vendor_code
								LEFT JOIN $this->tbl_sites K ON K1.site_id = K.site_id
								LEFT JOIN $this->tbl_pria_organizations L ON K.org_code = L.org_code
								LEFT JOIN $this->tbl_documents M ON K1.boq_id = M.reference AND M.document_type_code = ?
EOS;
							$values[]		= DOC_TYPE_BOQ;
					break;

					case EMAIL_NOTIF_SUB_SRR_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_SRR_FOR_APPROVAL_REVIEW_DETAILED:
					case EMAIL_NOTIF_SUB_SRR_FOR_APPROVAL_W_ACTION:
					case EMAIL_NOTIF_SUB_SRR_FOR_APPROVAL_FINAL_W_ACTION:
					case EMAIL_NOTIF_SUB_SRR_FINAL_APPROVED_DETAILED:
						$join_fields	=<<<EOS
								, J.org_code
								, J.cn_recommendation
								, J.cn_recommendation_bh
								, J.cn_recommendation_rh
								, L.org_type_code
								, L.name AS business_center_name
								, K.official_store_name
								, M.vendor_name
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_contracts J ON C.reference_id = J.contract_id
								LEFT JOIN $this->tbl_sites K ON J.site_id = K.site_id
								LEFT JOIN $this->tbl_pria_organizations L ON J.org_code = L.org_code
								LEFT JOIN $this->tbl_vendors M ON J.vendor_code = M.vendor_code
								LEFT JOIN $this->tbl_documents N ON J.contract_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;
					case EMAIL_NOTIF_SUB_PO_RELEASED:
					case EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_PO_TASK_COMPLETED:
					case EMAIL_NOTIF_SUB_PO_FOR_APPROVAL_REVIEW_DETAILED:
						$join_fields	=<<<EOS
								, J.org_code
								, K.org_type_code
								, K.name AS business_center_name
								, J.vendor_code
								, L.vendor_name
								, DATE_FORMAT(J.po_date, '%M %d, %Y') AS po_date
								, FORMAT(J.po_amount, 2) AS po_amount
								, DATE_FORMAT(J.po_released_date, '%M %d, %Y') AS po_released_date
								, GROUP_CONCAT(DISTINCT IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(DISTINCT IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(DISTINCT IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_purchase_orders J ON C.reference_id = J.po_id
								LEFT JOIN $this->tbl_pria_organizations K ON J.org_code = K.org_code
								LEFT JOIN $this->tbl_vendors L ON J.vendor_code = L.vendor_code
								LEFT JOIN $this->tbl_documents N ON J.po_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;

						if($template_type == EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED)
						{
							$join_fields	.=<<<EOS
								, R.boq_code AS boq_num
EOS;
							$join_sql		.=<<<EOS
								LEFT JOIN $this->tbl_pria_references S ON J.po_id = S.po_id
								LEFT JOIN $this->tbl_purchase_requisitions P ON S.pr_id = P.pr_id
								LEFT JOIN $this->tbl_boq_pr Q ON P.pr_id = Q.pr_id
								LEFT JOIN $this->tbl_boq R ON Q.boq_id = R.boq_id
EOS;
						}
					break;
					case EMAIL_NOTIF_SUB_DR_TASK_COMPLETED:
						$join_fields	=<<<EOS
								, L.org_code
								, M.org_type_code
								, M.name AS business_center_name
								, L.vendor_code
								, P.vendor_name
								, L.dr_num
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_purchase_orders J ON C.reference_id = J.po_id
								LEFT JOIN $this->tbl_pria_references K ON J.po_id = K.po_id
								LEFT JOIN $this->tbl_delivery_goods_receipt L ON K.dr_gr_id = L.dr_gr_id
								AND A.pria_task_id = L.pria_task_id
								LEFT JOIN $this->tbl_pria_organizations M ON L.org_code = M.org_code
								LEFT JOIN $this->tbl_vendors P ON L.vendor_code = P.vendor_code
								LEFT JOIN $this->tbl_documents N ON L.dr_gr_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;
					case EMAIL_NOTIF_SUB_GR_TASK_COMPLETED:
						$join_fields	=<<<EOS
								, L.org_code
								, M.org_type_code
								, M.name AS business_center_name
								, L.vendor_code
								, P.vendor_name
								, L.dr_num
								, L.gr_num
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_purchase_orders J ON C.reference_id = J.po_id
								LEFT JOIN $this->tbl_pria_references K ON J.po_id = K.po_id
								LEFT JOIN $this->tbl_delivery_goods_receipt L ON K.dr_gr_id = L.dr_gr_id
								AND L.pria_task_id IN (
									SELECT pria_task_id FROM $this->tbl_pria_tasks
									WHERE pria_stage_id = A.pria_stage_id
								)
								LEFT JOIN $this->tbl_pria_organizations M ON L.org_code = M.org_code
								LEFT JOIN $this->tbl_vendors P ON L.vendor_code = P.vendor_code
								LEFT JOIN $this->tbl_documents N ON L.dr_gr_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
					break;
					case EMAIL_NOTIF_SUB_PR_TASK_COMPLETED:
						$join_fields	=<<<EOS
								, M2.name AS business_center_name
								, IF(J.additional_flag = 1, 'Additional Works', 'New Project') project_type
								, IF(J.requestor IS NOT NULL AND AGDEC(J.requestor) != '', AGDEC(J.requestor), 'N/A') requestor_name
								, GROUP_CONCAT(IFNULL(P.file_name, '') SEPARATOR ',') AS rfa_document_name
								, GROUP_CONCAT(IFNULL(P.sys_file_name, '') SEPARATOR ',') AS rfa_document_path
								, GROUP_CONCAT(IFNULL(N.file_name, '') SEPARATOR ',') AS main_document_name
								, GROUP_CONCAT(IFNULL(N.sys_file_name, '') SEPARATOR ',') AS main_document_path
								, GROUP_CONCAT(IFNULL(O.document_type_name, '') SEPARATOR ',') AS document_type_names
EOS;
						$join_sql		=<<<EOS
								LEFT JOIN $this->tbl_purchase_requisitions J ON C.reference_id = J.pr_id
								LEFT JOIN $this->tbl_boq_pr K ON J.pr_id = K.pr_id
								LEFT JOIN $this->tbl_boq L ON K.boq_id = L.boq_id
								LEFT JOIN $this->tbl_sites M ON L.site_id = M.site_id
								LEFT JOIN $this->tbl_pria_organizations M2 ON M.org_code = M2.org_code
								LEFT JOIN $this->tbl_documents P ON K.boq_id = P.reference AND P.document_type_code IN (?)
								LEFT JOIN $this->tbl_documents N ON J.pr_id = N.reference
								AND N.document_type_code IN (
									SELECT DISTINCT document_type_code FROM $this->tbl_pria_task_document_types
									WHERE pria_task_id = A.pria_task_id
								)
								LEFT JOIN $this->tbl_param_document_types O ON N.document_type_code = O.document_type_code
EOS;
						$values[]	= DOC_TYPE_RFA;
					break;
				}
			}

			$values[]	= $pria_task_id;

			$query	= <<<EOS
				SELECT
				A.pria_task_id, B.pria_stage_id, C.pria_workflow_id, A.tat, A.remarks, I.controller, A.task_status_id,
				A.task_name, IF(A.expected_end_date IS NOT NULL, CONCAT_WS(' ', DATE_FORMAT(A.expected_end_date, '%m/%d/%Y'), IF(A.actual_end_date IS NOT NULL, IF(DATE(A.actual_end_date) > DATE(A.expected_end_date), '(DELAYED)', IF(A.actual_start_date IS NOT NULL, IF(DATE(A.actual_start_date) > DATE(A.expected_end_date) OR DATE(A.actual_start_date) > DATE(A.expected_start_date), '(DELAYED)', NULL), NULL)), NULL)), NULL) AS due_date_status, C.account_group_code, CONCAT_WS(' ', AGDEC(G.fname), AGDEC(G.mname), AGDEC(G.lname), AGDEC(G.ext_name)) AS full_name, H.account_group_name AS ag_name, C.reference_num, A.returned_flag, A.user_id AS resource_id, A.core_workflow_task_id, TA.action_name AS task_action_name, GROUP_CONCAT(TRR.role_name SEPARATOR ', ') as task_roles
					$join_fields
				FROM $this->tbl_pria_tasks A
				LEFT JOIN $this->tbl_pria_workflow_stages B ON A.pria_stage_id = B.pria_stage_id
				LEFT JOIN $this->tbl_pria_workflows C ON B.pria_workflow_id = C.pria_workflow_id
				LEFT JOIN $this->tbl_workflow_stage_tasks D ON A.core_workflow_task_id = D.workflow_task_id
				LEFT JOIN $this->tbl_workflow_stages E ON B.core_workflow_stage_id = E.workflow_stage_id
				LEFT JOIN $this->tbl_workflows F ON C.core_Workflow_id = F.workflow_id
				LEFT JOIN $this->tbl_users G ON A.user_id = G.user_id
				LEFT JOIN $this->tbl_param_account_groups H ON C.account_group_code = H.account_group_code
				LEFT JOIN $this->tbl_pria_task_forms I ON A.pria_task_id = I.pria_task_id
				LEFT JOIN $this->tbl_pria_task_actions TA ON A.pria_task_id = TA.pria_task_id and A.task_status_id = TA.pria_task_action_id
				LEFT JOIN $this->tbl_pria_task_roles TR ON A.pria_task_id = TR.pria_task_id AND TR.actor_flag = ?
				LEFT JOIN $this->tbl_roles TRR ON TR.role_code = TRR.role_code
				$join_sql
				WHERE A.pria_task_id = ? $join_where
				GROUP BY A.pria_task_id
EOS;

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_user_details($user_id)
	{
		try
		{
			return $this->select_data(array("AGDEC(fname) AS first_name", "AGDEC(mname) AS mid_name", "AGDEC(lname) AS last_name", "AGDEC(ext_name) AS ext_name", "CONCAT_WS(' ', AGDEC(fname), AGDEC(mname), AGDEC(lname), AGDEC(ext_name)) AS full_name", "email", "role_code", "gender"), $this->tbl_users . " a LEFT JOIN " . $this->tbl_user_roles . " b ON a.user_id = b.user_id AND b.main_role_flag = 1", FALSE, array('a.user_id' => $user_id));
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_recipient_type($email_notification_id)
	{
		try
		{
			return $this->select_data(array("recipient_code"), $this->tbl_pria_mail_notification_recipients, TRUE, array('email_notification_id' => $email_notification_id));
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_users_per($role_codes=array(), $org_codes=array())
	{
		try
		{
			$r_marks	= "";
			$o_marks	= "";
			$join_sql	= "";
			$where_role	= "";
			$where_org	= "";

			$values		= array(STATUS_ACTIVE);

			if(COUNT($role_codes) > 0)
			{
				foreach($role_codes AS $key => $role_code)
				{
					$r_marks	.= (!EMPTY($r_marks))? ", ?": "?";
					$values[]	= $role_code;
				}

				$where_role		= " AND A.role_code IN ($r_marks)"; // AND A.main_role_flag = ?
				//$values[]		= 1;
			}

			if(COUNT($org_codes) > 0)
			{
				foreach($org_codes AS $key => $org_code)
				{
					$o_marks	.= (!EMPTY($o_marks))? ", ?": "?";
					$values[]	= $org_code;
				}

				$join_sql		=<<<EOS
						JOIN $this->tbl_user_orgs C ON B.user_id = C.user_id
EOS;

				$where_org		= " AND C.org_code IN ($o_marks)";
			}

			$query	= <<<EOS
				SELECT
				A.user_id
				FROM $this->tbl_user_roles A
				JOIN $this->tbl_users B ON A.user_id = B.user_id
				$join_sql
				WHERE B.status = ? $where_role $where_org
EOS;
			/* print_var_export($query, $values); die; */
			return $this->query($query, $values);
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

	public function get_vendor_users_by_vendor_code($vendor_code)
	{
		try
		{
			$values		= array(STATUS_ACTIVE, $vendor_code);

			$query	= <<<EOS
				SELECT
				A.user_id
				FROM $this->tbl_vendor_users A
				JOIN $this->tbl_users B ON A.user_id = B.user_id
				WHERE B.status = ? AND A.vendor_code = ?
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_prev_next_task_details($pria_task_id, $next_flag)
	{
		try
		{
			$values	= array($pria_task_id);

			$where	= ($next_flag == ENUM_YES)? "A.pre_pria_task_id = ?": "A.pria_task_id = ?";

			$query	= <<<EOS
				SELECT
				A.pria_task_id, A.pre_pria_task_id, B.controller
				FROM $this->tbl_pria_task_predecessors A
				LEFT JOIN $this->tbl_pria_task_forms B ON A.pria_task_id = B.pria_task_id
				WHERE $where
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_users_from_next_core_task($core_task_id, $exempt_core_task_ids = array(), $main_role=NULL, $org_codes=array())
	{
		try
		{
			$exempt_mark		= "";
			$o_marks			= "";
			$join_sql			= "";
			$where_exempt		= "";
			$where_role			= "";
			$where_org			= "";
			$values				= array(TASK_ROLE_VENDOR, $core_task_id);

			if(COUNT($exempt_core_task_ids) > 0)
			{
				foreach($exempt_core_task_ids AS $key => $exempt_core_task_id)
				{
					$exempt_mark.= (!EMPTY($exempt_mark))? ", ?": "?";
					$values[]	= $exempt_core_task_id;
				}

				$where_exempt	= " AND AB.workflow_task_id NOT IN ($exempt_mark)";
			}

			if($main_role !== NULL)
			{
				$where_role	= " AND AA.actor_flag = ?";
				$values[]	= $main_role;
			}

			$values[]			= STATUS_ACTIVE;

			if(COUNT($org_codes) > 0)
			{
				foreach($org_codes AS $key => $org_code)
				{
					$o_marks	.= (!EMPTY($o_marks))? ", ?": "?";
					$values[]	= $org_code;
				}

				$join_sql		=<<<EOS
						JOIN $this->tbl_user_orgs C ON B.user_id = C.user_id
EOS;

				$where_org		= " AND C.org_code IN ($o_marks)";
			}

			$query	= <<<EOS
				SELECT A.user_id
				FROM $this->tbl_user_roles A
				JOIN $this->tbl_users B ON A.user_id = B.user_id
				$join_sql
				WHERE A.role_code IN (
					SELECT AA.actor_role_code
					FROM $this->tbl_workflow_task_roles AA
					WHERE AA.actor_role_code != ? AND AA.workflow_task_id IN (
						SELECT AB.workflow_task_id
						FROM $this->tbl_workflow_task_predecessors AB
						WHERE AB.pre_workflow_task_id = ? $where_exempt
					) $where_role
				) AND B.status = ? $where_org
EOS;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_task_actions($pria_task_id)
	{
		try
		{
			$values	= array(TASK_STATUS_ONGOING, $pria_task_id);

			$query	= <<<EOS
				SELECT
				A.pria_task_action_id, A.action_name, A.btn_label, A.seq_no
				FROM $this->tbl_pria_task_actions A
				WHERE A.btn_label IS NOT NULL AND A.pria_task_action_id != ? AND A.pria_task_id = ?
				ORDER BY A.seq_no
EOS;
			return $this->query($query, $values);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_filtered_role()
	{
		try
		{
			return $this->select_data(array("sys_param_value AS filtered_role"), $this->tbl_sys_param, TRUE, array('sys_param_type' => SYS_PARAM_MAIL_FILTERED));
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_pria_task_range($pria_task_id, $return_pria_task_id)
	{
		try
		{
			$values	= array($pria_task_id, $return_pria_task_id, $pria_task_id, $pria_task_id);

			$query	= <<<EOS
				SELECT
				A.pria_task_id
				FROM $this->tbl_pria_tasks A
				WHERE A.pria_stage_id = (
					SELECT pria_stage_id FROM $this->tbl_pria_tasks WHERE pria_task_id = ?
				)
				AND A.sequence_no BETWEEN (
					SELECT sequence_no FROM $this->tbl_pria_tasks WHERE pria_task_id = ?
				) AND (
					SELECT sequence_no FROM $this->tbl_pria_tasks WHERE pria_task_id = ?
				) AND A.pria_task_id != ?
EOS;
			return $this->query($query, $values);

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_email_link($fields)
	{
		return $this->insert_data($this->tbl_pria_task_email_links, $fields, TRUE);
	}


	public function get_task_email_link_details($where=[], $fields=['*'])
	{
		try
		{
			return $this->select_data($fields, $this->tbl_pria_task_email_links, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_pria_task_stage_users($pria_task_id)
	{
		try
		{
			$values	= array($pria_task_id, $pria_task_id);

			$query	= <<<EOS
				SELECT
				A.user_id
				FROM $this->tbl_pria_tasks A
				WHERE A.pria_stage_id = (
					SELECT pria_stage_id FROM $this->tbl_pria_tasks WHERE pria_task_id = ?
				) AND A.user_id IS NOT NULL AND A.sequence_no < (
					SELECT sequence_no FROM $this->tbl_pria_tasks WHERE pria_task_id = ?
				)
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_trans_orgs($org_code, $org_type)
	{
		try
		{
			$org_statement	= array();
			$values			= array();

			switch($org_type)
			{
				case ORG_TYPE_COST_CENTER:
					$org_statement[]	=<<<EOS
							SELECT D.org_code
							FROM $this->tbl_pria_organizations D
							WHERE D.org_type_code = ? AND D.org_code = ?
EOS;
					$values[]			= ORG_TYPE_COST_CENTER;
					$values[]			= $org_code;
				case ORG_TYPE_BUSINESS_CENTER:
					$org_statement[]	=<<<EOS
							SELECT A.org_code
							FROM $this->tbl_pria_organizations A
							WHERE A.org_type_code = ? AND A.org_code = ?
EOS;
					$values[]			= ORG_TYPE_BUSINESS_CENTER;
					$values[]			= $org_code;
				case ORG_TYPE_REGION:
					$org_statement[]	=<<<EOS
							SELECT B.org_code
							FROM $this->tbl_pria_organizations A
							LEFT JOIN $this->tbl_pria_organizations B ON A.org_parent = B.org_code AND B.org_type_code = ?
							WHERE A.org_type_code = ? AND A.org_code = ?
EOS;
					$values[]			= ORG_TYPE_REGION;
					$values[]			= ORG_TYPE_BUSINESS_CENTER;
					$values[]			= $org_code;
				case ORG_TYPE_ORGANIZATION:
					$org_statement[]	=<<<EOS
							SELECT C.org_code
							FROM $this->tbl_pria_organizations A
							LEFT JOIN $this->tbl_pria_organizations B ON A.org_parent = B.org_code AND B.org_type_code = ?
							LEFT JOIN $this->tbl_pria_organizations C ON B.org_parent = C.org_code
							WHERE A.org_type_code = ? AND A.org_code = ?
EOS;
					$values[]			= ORG_TYPE_REGION;
					$values[]			= ORG_TYPE_BUSINESS_CENTER;
					$values[]			= $org_code;
				break;
			}

			$implode_query	= implode(" UNION", $org_statement);

			$query			=<<<EOS
					SELECT DISTINCT(Z.org_code)
					FROM (
						$implode_query
					) Z
					WHERE Z.org_code IS NOT NULL
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_mail_notifications($where=array(), $fields=array('*'), $order=array(), $multiple = TRUE)
	{
		try
		{
			return $this->select_data($fields, $this->tbl_pria_mail_notifications, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_mail_notifications_by_last_hundred($offset=0, $limit=100)
	{
		try
		{
			$query =<<<EOS
				SELECT *
				FROM   $this->tbl_email_notification_queues
				ORDER BY email_notification_queue_id DESC
				LIMIT $offset, $limit
EOS;
			return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_email_templates($where=array(), $fields=array('*'), $order=array(), $multiple = TRUE)
	{
		try
		{
			return $this->select_data($fields, $this->tbl_pria_email_templates, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}