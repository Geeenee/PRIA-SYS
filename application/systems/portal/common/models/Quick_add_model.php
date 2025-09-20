<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quick_add_model extends Portal_Model
{
	private $tbl_temp_ios;
	private $tbl_temp_pos;
	private $tbl_temp_drs;
	private $tbl_temp_prs;
	private $tbl_temp_soas;
	private $tbl_sites;
	private $tbl_business_center;
	private $tbl_vendors;
	private $tbl_param_gl_accounts;
	private $tbl_param_purchasing_group;
	private $tbl_projects;
	private $tbl_purchase_requisitions;
	private $tbl_param_apv_status;
	private $tbl_pria_workflows;
	private $tbl_pria_workflow_stages;
	private $tbl_pria_tasks;
	private $tbl_purchase_orders;
	private $tbl_soa;
	private $tbl_pria_tab_modules;
	private $tbl_boq;
	private $tbl_boq_pr;
	private $tbl_documents;
	private $tbl_internal_orders;
	private $tbl_workflow_task_appendable;
	private $tbl_workflow_stage_tasks;
	private $tbl_workflow_stages;

	private $tbl_payments;
	private $tbl_payment_apvs;

	private $tbl_pria_references;
	
	public function __construct()
	{
		parent::__construct();
		$this->tbl_temp_ios 				= Portal_Model::PORTAL_TABLE_TEMP_IOS;
		$this->tbl_temp_pos 				= Portal_Model::PORTAL_TABLE_TEMP_POS;
		$this->tbl_temp_drs 				= Portal_Model::PORTAL_TABLE_TEMP_DRS;
		$this->tbl_temp_prs 				= Portal_Model::PORTAL_TABLE_TEMP_PRS;
		$this->tbl_temp_soas 				= Portal_Model::PORTAL_TABLE_TEMP_SOAS;
		$this->tbl_sites 					= Portal_Model::PORTAL_TABLE_SITES;
		$this->tbl_business_center 			= Portal_Model::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_vendors					= Portal_Model::PORTAL_TABLE_VENDORS;
		$this->tbl_param_gl_accounts 		= Portal_Model::PORTAL_TABLE_PARAM_GL_ACCOUNTS;
		$this->tbl_param_purchasing_group 	= Portal_Model::PORTAL_TABLE_PARAM_PURCHASING_GROUP;
		$this->tbl_projects					= Portal_Model::PORTAL_TABLE_PROJECTS;
		$this->tbl_purchase_requisitions 	= Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
		$this->tbl_param_apv_status 		= Portal_Model::PORTAL_TABLE_PARAM_APV_STATUS;
		$this->tbl_param_account_groups 	= Portal_Model::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
		$this->tbl_delivery_goods_receipt 	= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
		$this->tbl_pria_workflows 			= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
		$this->tbl_pria_workflow_stages		= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
		$this->tbl_pria_tasks 				= Portal_Model::PORTAL_TABLE_PRIA_TASKS;
		$this->tbl_purchase_orders			= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
		$this->tbl_soa						= Portal_Model::PORTAL_TABLE_SOA;
		$this->tbl_pria_tab_modules 		= Portal_Model::PORTAL_TABLE_PRIA_TAB_MODULE;
		$this->tbl_boq						= Portal_Model::PORTAL_TABLE_PRIA_BOQ;
		$this->tbl_boq_pr					= Portal_Model::PORTAL_TABLE_PRIA_BOQ_PR;
		$this->tbl_documents				= Portal_Model::PORTAL_TABLE_DOCUMENTS;
		$this->tbl_internal_orders			= Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
		$this->tbl_workflow_task_appendable	= Portal_Model::PORTAL_TABLE_PRIA_TASK_APPENDABLE;
		$this->tbl_workflow_stage_tasks		= Portal_Model::CORE_WORKFLOW_STAGE_TASKS;
		$this->tbl_workflow_stages			= Portal_Model::CORE_WORKFLOW_STAGES;

		$this->tbl_payments 				= Portal_Model::PORTAL_TABLE_PAYMENTS;
		$this->tbl_payment_apvs 			= Portal_Model::PORTAL_TABLE_PAYMENT_APVS;

		$this->tbl_pria_references 			= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
	}

	public function insert_record_data($table_name, $fields)
	{
		try
		{
			return $this->insert_data($table_name, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_record_data($table_name, $fields, $where)
	{
		try
		{
			return $this->update_data($table_name, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function delete_record_data($table_name, $where)
	{
		try
		{
			return $this->delete_data($table_name, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_temp_data($table_name, $fields)
	{
		try
		{
			return $this->insert_data($table_name, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_temp_data($table_name, $fields, $where)
	{
		try
		{
			return $this->update_data($table_name, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function delete_temp_table($table_name, $where)
	{
		try
		{
			return $this->delete_data($table_name, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_actual_data($table_name, $fields)
	{
		try
		{
			return $this->insert_data($table_name, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_actual_data($table_name, $fields, $where)
	{
		try
		{
			return $this->update_data($table_name, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_tmp_records($field_names, $tbl_name, $arr_ids, $user_id)
	{
		$result 	= array();
		
		try
		{
			$val 		= array($user_id);
			$table_ids 	= implode(',', $arr_ids);
			$extra_join = '';
			$order_by 	= '';

			switch ($tbl_name) {
				case PORTAL_TMP_QA_IO:
					/*$extra_join = ' LEFT JOIN '.$this->tbl_sites.' B
										ON A.site_code = B.site_code
									LEFT JOIN '.$this->tbl_business_center.' C
										ON B.org_code = C.org_code AND C.org_type_code = \'' . ORG_TYPE_BUSINESS_CENTER . '\'
									LEFT JOIN '.$this->tbl_vendors.' D
										ON A.vendor_code = D.vendor_code ';*/

					$extra_and  = ' AND A.temp_io_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_io_id ASC ';
					break;
				
				case PORTAL_TMP_QA_PR:
					/*$extra_join = ' LEFT JOIN '.$this->tbl_sites.' B 
										ON A.cost_center_code = B.cost_center_code
									LEFT JOIN '.$this->tbl_param_gl_accounts.' C 
										ON A.gl_account_code = C.gl_account_code
									LEFT JOIN '.$this->tbl_param_purchasing_group.' D 
										ON A.purchasing_group_code = D.purchasing_group_code ';*/

					$extra_and  = ' AND A.temp_pr_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_pr_id  ASC ';
					break;
				
				case PORTAL_TMP_QA_PO:

					/*$extra_join = ' LEFT JOIN '.$this->tbl_projects.' B 
										ON A.boq_num = B.initial_boq_num
									LEFT JOIN '.$this->tbl_purchase_requisitions.' C 
										ON A.pr_num = C.pr_num
									LEFT JOIN '.$this->tbl_vendors.' D 
										ON A.vendor_code = D.vendor_code ';*/

					$extra_and  = ' AND A.temp_po_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_po_id  ASC ';
					break;
				
				case PORTAL_TMP_QA_SOA:
					/*$extra_join = ' LEFT JOIN '.$this->tbl_vendors.' B
										ON A.vendor_code = B.vendor_code ';*/
					
					$extra_and  = ' AND A.temp_soa_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_soa_id  ASC ';
					break;
				
				case PORTAL_TMP_QA_DR:
					/*$extra_join = ' LEFT JOIN '.$this->tbl_sites.' B 
										ON A.cost_center = B.cost_center_code
									LEFT JOIN '.$this->tbl_vendors.' C 
										ON A.vendor_code = C.vendor_code ';*/

					$extra_and  = ' AND A.temp_dr_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_dr_id  ASC ';
					break;

				case PORTAL_TMP_QA_GR:

					/*$extra_join = ' LEFT JOIN '.$this->tbl_delivery_goods_receipt.' B on A.dr_number = B.dr_num ';*/

					$extra_and  = ' AND A.temp_gr_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_gr_id  ASC ';
					break;

				case PORTAL_TMP_QA_APV:
					/*$extra_join = ' LEFT JOIN '.$this->tbl_vendors.' B
										ON A.vendor_code = B.vendor_code 
									LEFT JOIN '.$this->tbl_param_apv_status.' C 
										ON A.apv_status_code = C.apv_status_id ';*/
					
					$extra_and  = ' AND A.temp_apv_id IN ('.$table_ids.') ';
					$order_by 	= ' ORDER BY temp_apv_id  ASC ';
					break;
				
				default:
					break;
			}

			$query 	= <<<EOS
				SELECT 
					{$field_names}
				FROM {$tbl_name} A
					{$extra_join}
				WHERE A.created_by = ? {$extra_and}
				{$order_by}
EOS;
			$result = $this->query($query, $val, TRUE, TRUE);
		
			return $result;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_acc_group($where=array(), $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, $this->tbl_param_account_groups, FALSE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_workflow($fields, $where)
	{
		try
		{
			return $this->update_data($this->tbl_pria_workflows, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_existing_records($table_name, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $table_name, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_reference_record($table, $table_id, $account_group_code, $vendor_code, $reference_num, $core_task_id = NULL)
	{
		try
		{
			$join_select = $join_sql = "";

			$join_values		= array();

			if(!EMPTY($core_task_id))
			{
				$join_select	= ", D.pria_task_id";
				$join_sql		=<<<EOS
					LEFT JOIN $this->tbl_pria_workflow_stages C ON B.pria_workflow_id = C.pria_workflow_id
					LEFT JOIN $this->tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id AND D.core_workflow_task_id = ?
EOS;
				$join_values	= array($core_task_id);
			}

			$table_id			= "A.".$table_id;

			$query				=<<<EOS
				SELECT $table_id AS tbl_reference_id $join_select
				FROM $table A
				LEFT JOIN $this->tbl_pria_workflows B ON $table_id = B.reference_id
				$join_sql
				WHERE A.vendor_code = ? AND B.reference_num = ? AND B.account_group_code = ?
EOS;
			$values				= array_merge($join_values, array($vendor_code, $reference_num, $account_group_code));

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function delete_temp($table_name, $where)
	{
		try
		{
			return $this->delete_data($table_name, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	// public function get_tab_modules($where = array(), $fields=array('*'), $order=array(), $multiple = FALSE)
	// {
	// 	try
	// 	{
	// 		return $this->select_data($fields, $this->tbl_pria_tab_modules, $multiple, $where);
	// 	}
	// 	catch(PDOException $e)
	// 	{
	// 		throw $e;
	// 	}
	// }

	public function get_record_details($fields_arr, $table, $multiple, $where_arr = array())
	{
		try
		{
			return $this->select_data($fields_arr, $table, $multiple, $where_arr);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_reference_details($table, $table_id, $account_group_code, $vendor_code, $org_code, $reference_num, $completed = FALSE, $ag_last_core_task_ids = array(), $trans_only = FALSE, $trans_where = array())
	{
		try
		{
			$table_id	= "B." . $table_id;

			$q_mark			= "";
			
			$join_select	= "";
			$join_sql		= "";
			$join_where		= "";
			$join_values	= array();

			if(!$trans_only AND $completed)
			{
				if(COUNT($ag_last_core_task_ids) > 0)
				{
					$join_values	= array(TASK_STATUS_DONE, TASK_STATUS_APPROVED, ENUM_YES, TASK_STATUS_DONE, TASK_STATUS_APPROVED);

					foreach($ag_last_core_task_ids AS $key => $ag_last_core_task_id)
					{
						$q_mark		.= (!EMPTY($q_mark))? ", ": "";
						$q_mark		.= $ag_last_core_task_id;
					}

					$join_select	=<<<EOS
							, D.pria_task_id AS not_appendable_pria_task_id
							, H.pria_task_id AS appendable_last_pria_task_id
EOS;

					$join_sql		=<<<EOS
							LEFT JOIN $this->tbl_pria_workflow_stages C ON A.pria_workflow_id = C.pria_workflow_id
							AND C.core_workflow_stage_id NOT IN (
								SELECT CC.workflow_stage_id FROM $this->tbl_workflow_task_appendable AA
								JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
								JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
								WHERE AA.pria_task_id IN (
									SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
								)
								UNION
								SELECT EE.workflow_stage_id FROM $this->tbl_workflow_task_appendable DD
								JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
								WHERE DD.pria_task_id IN (
									SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = C.pria_stage_id
								)
							)
							LEFT JOIN $this->tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id
							AND D.core_workflow_task_id IN ($q_mark) AND D.task_status_id IN (?, ?)
							LEFT JOIN $this->tbl_pria_workflow_stages E ON A.pria_workflow_id = E.pria_workflow_id
							AND E.core_workflow_stage_id IN (
								SELECT CC.workflow_stage_id FROM $this->tbl_workflow_task_appendable AA
								JOIN $this->tbl_pria_tasks BB ON AA.pria_task_id = BB.pria_task_id
								JOIN $this->tbl_workflow_stage_tasks CC ON BB.core_workflow_task_id = CC.workflow_task_id
								WHERE AA.pria_task_id IN (
									SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = E.pria_stage_id
								)
								UNION
								SELECT EE.workflow_stage_id FROM $this->tbl_workflow_task_appendable DD
								JOIN $this->tbl_workflow_stages EE ON DD.core_workflow_id = EE.workflow_id
								WHERE DD.pria_task_id IN (
									SELECT pria_task_id FROM $this->tbl_pria_tasks WHERE pria_stage_id = E.pria_stage_id
								)
							)
							LEFT JOIN $this->tbl_pria_tasks F ON E.pria_stage_id = F.pria_stage_id
							LEFT JOIN $this->tbl_delivery_goods_receipt G ON F.pria_task_id = G.pria_task_id and G.last_dr_flag = ?
							LEFT JOIN $this->tbl_pria_tasks H ON F.pria_stage_id = H.pria_stage_id
							AND H.core_workflow_task_id IN ($q_mark) AND H.task_status_id IN (?, ?)
EOS;
					$join_where		= "AND (D.pria_task_id IS NOT NULL OR H.pria_task_id IS NOT NULL)";
				}
			}

			if(!$trans_only)
			{
				$query					=<<<EOS
						SELECT A.reference_id
						$join_select
						FROM $this->tbl_pria_workflows A
						LEFT JOIN $table B ON A.reference_id = $table_id
						$join_sql
						WHERE A.account_group_code = ? AND B.vendor_code = ? AND B.org_code = ? AND A.reference_num = ?
						$join_where
EOS;
				$values		= array_merge($join_values, array($account_group_code, $vendor_code, $org_code, $reference_num));

				return $this->query($query, $values, TRUE, FALSE);
			}
			else
			{
				return $this->select_data(array($table_id . " reference_id"), $table . " B", FALSE, $trans_where);
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_exist_payment_apv($payment_id, $apv_num, $cv_num, $particulars)
	{
		try
		{
			$particulars_condition	= (EMPTY($particulars))? "IS NULL": "= ?";
			$apv_array				= array($apv_num);
			$cv_array				= array($cv_num);
			$particulars_array		= (EMPTY($particulars))? array(): array($particulars);

			$query					=<<<EOS
				SELECT B.payment_apv_id AS cv_payment_apv, C.payment_apv_id AS null_payment_apv, D.payment_apv_id AS null_payment_cv
				FROM $this->tbl_payments A
				LEFT JOIN $this->tbl_payment_apvs B ON A.payment_id = B.payment_id AND B.apv_num = ? AND B.cv_num = ? AND B.particulars $particulars_condition
				LEFT JOIN $this->tbl_payment_apvs C ON A.payment_id = C.payment_id AND C.apv_num = ? AND C.cv_num IS NULL AND C.particulars $particulars_condition
				LEFT JOIN $this->tbl_payment_apvs D ON A.payment_id = D.payment_id AND D.cv_num = ? AND D.apv_num IS NULL AND D.particulars $particulars_condition
				WHERE A.payment_id = ?
EOS;
			$values	= array_merge($apv_array, $cv_array);
			$values	= array_merge($values, $particulars_array);
			$values	= array_merge($values, $apv_array);
			$values	= array_merge($values, $particulars_array);
			$values	= array_merge($values, $cv_array);
			$values	= array_merge($values, $particulars_array);
			$values	= array_merge($values, array($payment_id));

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_po_records($account_group_code, $org_code, $vendor_code, $reference_num, $pr_num, $boq_num, $transaction_tab, $core_task_id = NULL, $completed = ENUM_NO, $doc_type = NULL, $print=FALSE)
	{
		try
		{
			$join_select = $join_sql = "";
			$sub_join_select = $sub_join_sql = "";
			$where = "";

			$join_values			= array();
			$sub_join_values		= array();
			$values					= array();

			if(!EMPTY($boq_num))
			{
				$sub_join_select	= ", D.boq_id";
				$sub_join_sql		=<<<EOS
					LEFT JOIN $this->tbl_boq_pr C ON I.pr_id = C.pr_id
					LEFT JOIN $this->tbl_boq D ON C.boq_id = D.boq_id AND D.boq_code = ?
EOS;
				$sub_join_values	= array($boq_num);
			}

			if(!EMPTY($core_task_id))
			{
				$join_select		.= ", G.pria_task_id";
				$join_sql			.=<<<EOS
					LEFT JOIN $this->tbl_pria_workflow_stages F ON E.pria_workflow_id = F.pria_workflow_id
					LEFT JOIN $this->tbl_pria_tasks G ON F.pria_stage_id = G.pria_stage_id AND G.core_workflow_task_id = ?
EOS;
				$join_values[]	= $core_task_id;

				if($completed == ENUM_YES)
				{
					$where				.= "AND G.task_status_id IN (?, ?)";
					$values[]			= TASK_STATUS_DONE;
					$values[]			= TASK_STATUS_APPROVED;
				}

				if(!EMPTY($doc_type))
				{
					$join_select	.= ", H.document_id";
					$join_sql		.=<<<EOS
						LEFT JOIN $this->tbl_documents H ON A.po_id = H.reference AND G.pria_task_id = H.pria_task_id AND H.document_type_code = ?
EOS;
					$join_values[]	= $doc_type;
				}
			}

			$query					=<<<EOS
				SELECT A.po_id AS reference_id, B.pr_id AS sub_reference_id $sub_join_select $join_select
				FROM $this->tbl_purchase_orders A
				JOIN $this->tbl_pria_references I ON A.po_id = I.po_id
				LEFT JOIN $this->tbl_purchase_requisitions B ON I.pr_id = B.pr_id AND B.pr_num = ?
				$sub_join_sql
				LEFT JOIN $this->tbl_pria_workflows E ON A.po_id = E.reference_id
				AND B.account_group_code = E.account_group_code AND E.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				$join_sql
				WHERE B.account_group_code = ? AND A.org_code = ? AND A.vendor_code = ? AND A.po_num = ?
				$where
EOS;
			$values					= array_merge(
					array($pr_num),
					$sub_join_values,
					array($transaction_tab),
					$join_values,
					array($account_group_code, $org_code, $vendor_code, $reference_num),
					$values
			);

			//print_var_export($query, $values);
			/* if($print)
				print_var_export($query, $values); die; */

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_soa_records($account_group_code, $org_code, $vendor_code, $reference_num, $transaction_tab, $core_task_id, $doc_type = NULL)
	{
		try
		{
			$join_select = $join_sql = "";
			$q_mark = "";

			$join_values			= array();

			if(!EMPTY($doc_type))
			{
				$join_select		= ", E.document_id";
				$join_sql			=<<<EOS
					LEFT JOIN $this->tbl_documents E ON A.soa_id = E.reference AND D.pria_task_id = E.pria_task_id AND E.document_type_code = ?
EOS;
				$join_values		= array($doc_type);
			}

			$query					=<<<EOS
				SELECT A.soa_id AS reference_id, D.pria_task_id $join_select
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = A.account_group_code AND transaction_tab = ?
				)
				LEFT JOIN $this->tbl_pria_workflow_stages C ON B.pria_workflow_id = C.pria_workflow_id
				LEFT JOIN $this->tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id AND D.core_workflow_task_id = ?
				$join_sql
				WHERE A.account_group_code = ? AND A.org_code = ? AND A.vendor_code = ? AND A.soa_num = ?
EOS;
			$values					= array_merge(
					array($transaction_tab, $core_task_id),
					$join_values,
					array($account_group_code, $org_code, $vendor_code, $reference_num)
			);

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_io_trans_rec($account_group_code, $org_code, $vendor_code, $site_code, $cycle_num, $placement_year)
	{
		try
		{
			$values				= array(
					$account_group_code,
					TRANS_TAB_IO,
					$org_code,
					$vendor_code,
					$site_code,
					$cycle_num,
					$placement_year
			);

			$query				=<<<EOS
				SELECT
					A.io_id AS reference_id, A.io_num AS reference_num, B.pria_workflow_id
				FROM $this->tbl_internal_orders A
				LEFT JOIN $this->tbl_pria_workflows B ON A.io_id = B.reference_id
				AND B.account_group_code = ? AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = B.account_group_code AND transaction_tab = ?
				)
				WHERE A.org_code = ? AND A.vendor_code = ? AND A.site_code = ? AND A.cycle_num = ? AND A.placement_year = ?
EOS;
			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_pr_trans_rec($account_group_code, $reference_num, $ag_last_core_task_ids = [])
	{
		try
		{
			$join_select = $join = "";
			$values		= array($account_group_code, $reference_num);

			if(is_array($ag_last_core_task_ids) AND count($ag_last_core_task_ids) > 0)
			{
				foreach($ag_last_core_task_ids AS $key => $ag_last_core_task_id)
				{
					$q_mark		.= (!EMPTY($q_mark))? ", ": "";
					$q_mark		.= $ag_last_core_task_id;
				}

				$join_select	.= ", D.pria_task_id";

				$join			.= "
					LEFT JOIN $this->tbl_pria_workflows B ON A.pr_id = B.reference_id
					AND A.account_group_code = B.account_group_code
					AND B.org_code IS NULL AND B.vendor_code IS NULL
					LEFT JOIN $this->tbl_pria_workflow_stages C ON B.pria_workflow_id = C.pria_workflow_id
					LEFT JOIN $this->tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id AND D.core_workflow_task_id IN ($q_mark)
					AND D.task_status_id IN (?, ?)";
				$values	= array_merge([TASK_STATUS_DONE, TASK_STATUS_APPROVED], $values);
			}

			$query		=<<<EOS
				SELECT
					A.pr_id AS reference_id
					$join_select
				FROM $this->tbl_purchase_requisitions A
				$join
				WHERE A.account_group_code = ? AND A.pr_num = ?
EOS;
			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_soa_trans_rec($account_group_code, $org_code, $vendor_code, $reference_num)
	{
		try
		{
			$values				= array(
					TRANS_TAB_SOA,
					$account_group_code,
					$org_code,
					$vendor_code,
					$reference_num
			);

			$query				=<<<EOS
				SELECT
					A.soa_id AS reference_id
				FROM $this->tbl_soa A
				LEFT JOIN $this->tbl_pria_workflows B ON A.soa_id = B.reference_id
				AND A.account_group_code = B.account_group_code AND B.core_workflow_id IN (
					SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
					WHERE ag_code = A.account_group_code AND transaction_tab = ?
				)
				WHERE A.account_group_code = ? AND A.org_code = ? AND A.vendor_code = ? AND A.soa_num = ?
EOS;

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_dr_trans_rec($account_group_code, $org_code, $vendor_code, $reference_num, $core_task_id = NULL, $approve_core_task_id = NULL, $approve_completed = ENUM_NO, $gr_core_task_id = NULL)
	{
		try
		{
			$join_select = $join_sql = "";
			$join_values		= array();
			$where	= "";

			$values				= array(
					$account_group_code,
					$org_code,
					$vendor_code,
					$reference_num
			);

			if(!EMPTY($core_task_id))
			{
				$join_select	.= ", B.pria_task_id";
				$join_sql		.=<<<EOS
					LEFT JOIN $this->tbl_pria_tasks B ON A.pria_task_id = B.pria_task_id AND B.core_workflow_task_id = ?
EOS;
				$join_values[]	= $core_task_id;

				if(!EMPTY($approve_core_task_id))
				{
					$join_select	.= ", C.pria_task_id AS dr_approve_pria_task_id";
					$join_sql		.=<<<EOS
						LEFT JOIN $this->tbl_pria_tasks C ON B.pria_stage_id = C.pria_stage_id AND C.core_workflow_task_id = ?
EOS;
					$join_values[]	= $approve_core_task_id;

					if($approve_completed == ENUM_YES)
					{
						$where		.= "AND C.task_status_id IN (?, ?)";
						$values[]	= TASK_STATUS_DONE;
						$values[]	= TASK_STATUS_APPROVED;
					}
				}

				if(!EMPTY($gr_core_task_id))
				{
					$join_select	.= ", D.pria_task_id AS gr_pria_task_id";
					$join_sql		.=<<<EOS
					LEFT JOIN $this->tbl_pria_tasks D ON B.pria_stage_id = D.pria_stage_id AND D.core_workflow_task_id = ?
EOS;
					$join_values[]	= $gr_core_task_id;
				}
			}

			$query				=<<<EOS
				SELECT
					A.dr_gr_id AS reference_id $join_select
				FROM $this->tbl_delivery_goods_receipt A
				$join_sql
				WHERE A.account_group_code = ? AND A.org_code = ? AND A.vendor_code = ? AND A.dr_num = ?
				$where
EOS;
			$values		= array_merge($join_values, $values);
			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_gr_trans_rec($dr_id, $reference_num)
	{
		try
		{
			$values				= array(
					$dr_id,
					$reference_num
			);

			$query				=<<<EOS
				SELECT
					A.dr_gr_id AS reference_id
				FROM $this->tbl_delivery_goods_receipt A
				WHERE A.dr_gr_id = ? AND A.gr_num = ?
EOS;

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}