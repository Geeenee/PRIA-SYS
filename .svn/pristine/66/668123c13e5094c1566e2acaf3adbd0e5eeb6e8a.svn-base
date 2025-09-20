<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_workflow_model extends Portal_Model {

	private $core_tbl_users					= Portal_Model::CORE_TABLE_USERS;
	private $core_tbl_workflow          	= Portal_Model::CORE_WORKFLOWS;
	private $core_tbl_stages            	= Portal_Model::CORE_WORKFLOW_STAGES;
	private $core_tbl_tasks             	= Portal_Model::CORE_WORKFLOW_STAGE_TASKS;
	private $core_tbl_task_roles        	= Portal_Model::CORE_WORKFLOW_TASK_ROLES;
	private $core_tbl_task_actions      	= Portal_Model::CORE_WORKFLOW_TASK_ACTIONS;
	private $core_tbl_task_predecessors 	= Portal_Model::CORE_WORKFLOW_TASK_PREDECESSORS;
	private $core_tbl_task_appendable		= Portal_Model::CORE_WORKFLOW_TASK_APPENDABLE;
	private $core_tbl_task_forms			= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS_TASK_FORMS;
	private $core_tbl_task_workflow_return	= Portal_Model::CORE_WORKFLOW_TASK_WORKFLOW_RETURN;
	private $core_tbl_task_workflow_document_types	= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS_TASK_DOCUMENT_TYPES;
	private $core_tbl_task_workflow_file_extensions	= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS_TASK_FILE_EXTENSIONS;


	private $to_tbl_workflow            = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
	private $to_tbl_stages              = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
	private $to_tbl_tasks               = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
	private $to_tbl_task_roles	        = Portal_Model::PORTAL_TABLE_PRIA_TASK_ROLES;
	private $to_tbl_task_actions        = Portal_Model::PORTAL_TABLE_PRIA_TASK_ACTIONS;
	private $to_tbl_predecessors        = Portal_Model::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
	private $to_tbl_task_appendable		= Portal_Model::PORTAL_TABLE_PRIA_TASK_APPENDABLE;
	private $to_tbl_task_forms			= Portal_Model::PORTAL_TABLE_PRIA_TASK_FORMS;
	private $to_tbl_task_return        	= Portal_Model::PORTAL_TABLE_PRIA_TASK_RETURN;
	private $to_tbl_task_document_types = Portal_Model::PORTAL_TABLE_PRIA_TASK_DOCUMENT_TYPES;
	private $to_tbl_task_file_extensions = Portal_Model::PORTAL_TABLE_PRIA_TASK_FILE_EXTENSIONS;

	public function __construct()
	{
		parent::__construct();
	}

	/** SELECT FUNCTIONS */
	public function get_stage($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_stages, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_stages($where, $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_stages, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_workflow($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_workflow, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_workflows($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_workflow, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_tasks($where, $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_tasks, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_task($where, $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_tasks, FALSE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_core_workflow($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->core_tbl_workflow, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_core_stages($where, $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, $this->core_tbl_stages, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_task_predecessors($where, $fields=array('*'), $order=array())
	{
		try
		{
			return $this->select_data($fields, $this->to_tbl_predecessors, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_stages_transaction_list($pria_workflow_id, $filter_params=array())
	{
		try
		{
			$filter	= "";
			$values	= array($pria_workflow_id);

			if(COUNT($filter_params) > 0)
            {
                $filter	= $filter_params['having'];
                $values	= array_merge($values, $filter_params['values']);
            }

			$query	=<<<EOS
					SELECT
						A.pria_workflow_id,
						A.pria_stage_id,
						A.stage_name,
						A.core_workflow_stage_id
					FROM $this->to_tbl_stages A
					WHERE A.pria_workflow_id = ?
                    $filter
					ORDER BY A.sequence_no ASC
EOS;
			//print_var_export($query, $values);
	    	return $this->query($query, $values);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_current_predecessor_pria_task_id($pria_workflow_id, $seq_no)
	{
		try
		{
			$query =<<<EOS
			SELECT
				a.pria_task_id
			FROM
				pria_tasks a
			JOIN
				pria_workflow_stages b ON a.pria_stage_id = b.pria_stage_id
			JOIN
				pria_workflows c ON b.pria_workflow_id = c.pria_workflow_id
			WHERE
				c.pria_workflow_id 	= ?
			AND
				b.sequence_no 		= ?
			ORDER BY
				a.sequence_no DESC
EOS;
			$result = $this->query($query, [$pria_workflow_id, $seq_no], TRUE, FALSE);

			return $result['pria_task_id'];
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** INSERT FUNCTIONS */
	public function insert_workflow($data)
	{
		try
		{
			return $this->insert_data($this->to_tbl_workflow, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_stage($fields)
	{
		try
		{
			return $this->insert_data($this->to_tbl_stages, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_stages($workflow_id, $new_workflow_id)
	{
		try
		{
			$query=<<<EOS
			INSERT INTO $this->to_tbl_stages
			(pria_workflow_id, stage_name, tat, skip_flag, core_workflow_stage_id, sequence_no)

			SELECT
			$new_workflow_id, stage_name, tat_in_days, skip_flag, workflow_stage_id, sequence_no
			FROM $this->core_tbl_stages
			WHERE
				workflow_id = ?
			AND
				active_flag = ?
EOS;
			$this->query($query, array($workflow_id, INITIAL_YES), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_stages_for_append($workflow_id, $new_workflow_id, $start_sequence)
	{
		try
		{
			$query=<<<EOS
			INSERT INTO $this->to_tbl_stages
			(pria_workflow_id, stage_name, tat, skip_flag, core_workflow_stage_id, sequence_no)

			SELECT
				$new_workflow_id, stage_name, tat_in_days, skip_flag, workflow_stage_id,  @rownum := @rownum + 1
			FROM
				 $this->core_tbl_stages
			CROOS JOIN
				(select @rownum := ?) r
			WHERE
				workflow_id = ?
EOS;
			$this->query($query, array($start_sequence, $workflow_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_tasks($stage_id, $new_stage_id, $created_by)
	{
		try
		{
			$query=<<<EOS
			INSERT INTO $this->to_tbl_tasks
			(pria_stage_id, task_name, sequence_no, tat, version_flag, get_flag, due_date_tag, system_based_tag, sys_notif_role, notif_flag, doc_name, core_workflow_task_id, created_by, created_date)

			SELECT
			$new_stage_id, task_name, sequence_no, tat_in_days, version_flag, get_flag, due_date_tag, system_based_tag, sys_notif_role, notif_flag, doc_name, workflow_task_id, $created_by, NOW()
			FROM $this->core_tbl_tasks WHERE workflow_stage_id = ?
EOS;

			$this->query($query, array($stage_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_appendable($task_id, $new_task_id)
	{
		try
		{
			$query=<<<EOS
				INSERT INTO $this->to_tbl_task_appendable
				(pria_task_id, core_workflow_id)

				SELECT
					$new_task_id, workflow_id
				FROM
					$this->core_tbl_task_appendable
				WHERE
					workflow_task_id = ?
EOS;
			$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_forms($task_id, $new_task_id)
	{
		try
		{
			$query=<<<EOS
				INSERT INTO $this->to_tbl_task_forms
					(pria_task_id, core_workflow_task_id, controller, btn_save_review_flag)

				SELECT
					$new_task_id, core_workflow_task_id, controller, btn_save_review_flag
				FROM
					$this->core_tbl_task_forms
				WHERE
					core_workflow_task_id = ?
EOS;

				$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_roles($task_id, $new_task_id)
	{
		try
		{
			$query=<<<EOS
			INSERT INTO $this->to_tbl_task_roles
			(pria_task_id, role_code, actor_flag)

			SELECT
			$new_task_id, actor_role_code, actor_flag
			FROM $this->core_tbl_task_roles
			WHERE workflow_task_id = ?
EOS;
			$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_actions($task_id, $new_task_id)
	{
		try
		{
			$query=<<<EOS
			INSERT INTO $this->to_tbl_task_actions

			SELECT
			$new_task_id, task_action_id, display_status, btn_label, seq_no
			FROM  $this->core_tbl_task_actions
			WHERE workflow_task_id = ?
EOS;

			$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_predecessors($task_id, $new_task_id, $new_stage_ids)
	{
		try
		{
			$str_stage_ids	= implode(',', $new_stage_ids);
			$query			=<<<EOS
			INSERT INTO $this->to_tbl_predecessors
			SELECT $new_task_id, pria_task_id
			FROM pria_tasks
			WHERE core_workflow_task_id IN
				(
					SELECT pre_workflow_task_id FROM $this->core_tbl_task_predecessors WHERE workflow_task_id = ?
				)
			AND  pria_stage_id IN (%s)
EOS;
			$query = sprintf($query, $str_stage_ids);

			$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_pria_task_predecessors($fields)
	{
		try
		{
			return $this->insert_data($this->to_tbl_predecessors, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

/* 	public function insert_task_document_types($task_id, $new_task_id, $new_stage_ids)
	{
		try
		{
			$str_stage_ids	= implode(',', $new_stage_ids);
			$query			=<<<EOS
			INSERT INTO $this->to_tbl_task_document_types
			SELECT $new_task_id, pria_task_id
			FROM pria_tasks
			WHERE core_workflow_task_id IN
				(
					SELECT core_workflow_task_id FROM $this->core_tbl_task_workflow_document_types WHERE workflow_task_id = ?
				)
			AND  pria_stage_id IN (%s)
EOS;
			$query = sprintf($query, $str_stage_ids);

			$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	} */


	public function insert_task_document_types($task_id, $new_task_id)
	{
		try
		{
			$query=<<<EOS
				INSERT INTO $this->to_tbl_task_document_types
					(pria_task_id, core_workflow_task_id, document_type_code, access)

				SELECT
					$new_task_id, core_workflow_task_id, document_type_code, access
				FROM
					$this->core_tbl_task_workflow_document_types
				WHERE
					core_workflow_task_id = ?
EOS;

				$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_file_extensions($task_id, $new_task_id)
	{
		try
		{
			$query=<<<EOS
				INSERT INTO $this->to_tbl_task_file_extensions
					(pria_task_id, core_workflow_task_id, file_extension)

				SELECT
					$new_task_id, core_workflow_task_id, file_extension
				FROM
					$this->core_tbl_task_workflow_file_extensions
				WHERE
					core_workflow_task_id = ?
EOS;

				$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_task_return($task_id, $new_task_id, $new_stage_ids)
	{
		try
		{
			$str_stage_ids	= implode(',', $new_stage_ids);
			$query			=<<<EOS
			INSERT INTO $this->to_tbl_task_return
			SELECT $new_task_id, pria_task_id, NULL
			FROM pria_tasks
			WHERE core_workflow_task_id IN
				(
					SELECT pria_task_id FROM $this->core_tbl_task_workflow_return WHERE workflow_task_id = ?
				)
			AND pria_stage_id IN (%s)
EOS;
			$query = sprintf($query, $str_stage_ids);
			$this->query($query, array($task_id), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** UPDATE  FUNCTIONS */
	public function update_task($fields, $where)
	{
		try
		{
			$this->update_data($this->to_tbl_tasks, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_stage($fields, $where)
	{
		try
		{
			$this->update_data($this->to_tbl_stages, $fields, $where);
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
			$this->update_data($this->to_tbl_workflow, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_stage_sequence($pria_workflow_id, $curr_sequence_no, $increment)
	{
		try
		{
			$query=<<<EOS
				UPDATE $this->to_tbl_stages
				SET
					sequence_no    	 = sequence_no + ?
				WHERE
					pria_workflow_id = ?
				AND
					sequence_no > ?
EOS;

			$this->query($query, array($increment, $pria_workflow_id, $curr_sequence_no), FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_task_predecessors($fields, $where)
	{
		try
		{
			$this->update_data($this->to_tbl_predecessors, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_dr($fields, $where, $core_task_id = NULL, $dependent_task = NULL)
	{
		try
		{
			$delivery_goods_tbl	= parent::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;

			$stage_where = $s_q_marks = "";
			$core_task_stages = [];

			$values	= [ENUM_NO, $where['dr_gr_id']];

			if(in_array($core_task_id, [CORE_TASK_MED_VAC, CORE_TASK_MED_VAC_APPEND]))
            {
                $core_task_stages       = [CORE_WORKFLOW_STAGE_MEDVAC, CORE_WORKFLOW_STAGE_MEDVAC_APPEND];
            }
            else if(in_array($core_task_id, [CORE_TASK_DOC_DR, CORE_TASK_DOC_DR_APPEND]))
            {
                $core_task_stages       = [CORE_WORKFLOW_STAGE_DOC_DR, CORE_WORKFLOW_STAGE_DOC_DR_APPEND];
            }

            if(COUNT($core_task_stages) > 0)
            {
                foreach($core_task_stages AS $key => $core_task_stage)
                {
                    $s_q_marks  .= (!EMPTY($s_q_marks))? ", ?": "?";
                    $values[]   = $core_task_stage;
                }

                $stage_where	= " AND C.core_workflow_stage_id IN ($s_q_marks)";
            }

			$query	=<<<EOS
				UPDATE $delivery_goods_tbl
				SET last_dr_flag = ?
				WHERE pria_task_id IN (
					SELECT pria_task_id FROM (
						SELECT D.pria_task_id
						FROM $delivery_goods_tbl Z
						JOIN $this->to_tbl_tasks A ON Z.pria_task_id = A.pria_task_id
						JOIN $this->to_tbl_stages B ON A.pria_stage_id = B.pria_stage_id
						JOIN $this->to_tbl_stages C ON B.pria_workflow_id = C.pria_workflow_id
						JOIN $this->to_tbl_tasks D ON C.pria_stage_id = D.pria_stage_id
						WHERE Z.dr_gr_id = ? $stage_where
					) Y
				)
EOS;
			$this->query($query, $values, FALSE);

			$this->update_data($delivery_goods_tbl, $fields, $where);

			if($fields['last_dr_flag'] == ENUM_YES AND !EMPTY($dependent_task))
			{
				$values	= [$where['dr_gr_id'], $dependent_task];

				$query	=<<<EOS
				UPDATE $this->to_tbl_predecessors
				SET pre_pria_task_id = (
					SELECT C.pria_task_id
					FROM $delivery_goods_tbl A
					JOIN $this->to_tbl_tasks B ON A.pria_task_id = B.pria_task_id
					JOIN $this->to_tbl_tasks C ON B.pria_stage_id = C.pria_stage_id
					WHERE dr_gr_id = ? AND C.sequence_no = (
						SELECT MAX(sequence_no)
						FROM $this->to_tbl_tasks
						WHERE pria_stage_id = B.pria_stage_id
					)
				)
				WHERE pria_task_id = ?
EOS;
				$this->query($query, $values, FALSE);
			}

		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_pr_uploaders_by_po_id($po_id) {
		try {
			$pr_bavi = CORE_TASK_PR_UPLOAD_BAVI;
			$pr_bffi = CORE_TASK_PR_UPLOAD_BFFI_MARINADES;
			$pr_contractors = CORE_TASK_PR_UPLOAD_CONTRACTORS;

			$query	=<<<EOS
			SELECT user_id 
			FROM   pria_references a 
			JOIN pria_tasks b 
			ON a.pr_id = b.reference 
			WHERE a.po_id = ? AND a.pr_id IS NOT NULL
			AND b.core_workflow_task_id IN ($pr_bavi, $pr_bffi, $pr_contractors) 
			GROUP BY b.user_id
EOS;
			
	    	return $this->query($query, [$po_id]);
		}
		catch(PDOException $e){
			throw $e;
		}
	}
}