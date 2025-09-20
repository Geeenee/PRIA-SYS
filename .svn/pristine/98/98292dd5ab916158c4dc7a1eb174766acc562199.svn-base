<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dr_model extends Portal_Model
{
	public $tbl_delivery_goods_receipt;
	public $tbl_pria_workflows;
	public $tbl_vendors;
	public $tbl_delivery_status;
	public $tbl_sites;
	public $tbl_pria_references;
	public $tbl_soa;
	public $tbl_param_account_groups;
	public $tbl_organizations;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_delivery_goods_receipt	= parent::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
        $this->tbl_pria_workflows			= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_workflow_stages		= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_pria_tasks				= parent::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_vendors					= parent::PORTAL_TABLE_VENDORS;
        $this->tbl_delivery_status			= parent::PORTAL_TABLE_PARAM_DELIVERY_STATUS;
        $this->tbl_sites					= parent::PORTAL_TABLE_SITES;
        $this->tbl_pria_references			= parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
        $this->tbl_soa						= parent::PORTAL_TABLE_SOA;
		$this->tbl_organizations			= parent::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_param_account_groups 	= parent::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
    }

    public function get_dr_list($wheres, $params=NULL, $having = '')
    {
    	try
    	{
			// Initialize variables
			$where = $join = $filter = $order = $limit = $q_marks = "";
			$values = $filter_val = $values_temp = array();

			$having	= str_replace('org_code', 'A.org_code', $having);

			$values	= [TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_RETURNED, CORE_WORKFLOW_STAGE_SOA_FWD];

    		if(COUNT($wheres['ag_codes']) > 0)
    		{
    			foreach($wheres['ag_codes'] AS $key => $ag_code)
    			{
    				$q_marks		.= ($key==0)? "?": ", ?";
    				$values_temp[]	= $ag_code;
    			}
    		}

			$values				= array_merge($values, $values_temp);

			$fields				=  array(
					"A.dr_gr_id",
					"DATE_FORMAT(A.dr_date, '%m/%d/%Y') AS date",
					"A.dr_num AS ref_no",
					"CONCAT('[', A.vendor_code, '] ', B.vendor_name) AS vendor",
					"E.official_store_name AS official_store_name",
					"GROUP_CONCAT(G.soa_num SEPARATOR ', <br>') AS soa_num",
					"GROUP_CONCAT(IF(G.soa_num IS NULL, 'N/A', IF(J.task_status_id IS NULL,
						'Pending',
						IF(J.task_status_id = ".TASK_STATUS_RETURNED.",
						'Returned',
						CASE
							WHEN J.sequence_no = 1 THEN 'Uploaded'
							WHEN J.sequence_no = 2 THEN 'Transmitted'
							WHEN J.sequence_no = 3 THEN 'Approved'
							ELSE 'Pending'
						END
					))) SEPARATOR ', <br>') AS soa_status",
					"A.dr_remarks AS dr_remarks",
					"A.dr_status AS dr_status",
					"D.dr_status_name AS dr_status_name",
					"A.org_code",
					"A.vendor_code",
					"I.name as business_center"
			);

			$filters			= array(
					"DATE_FORMAT(A.dr_date, '%m/%d/%Y') convert_to date",
					"I.name convert_to business_center",
					"A.dr_num convert_to ref_no",
					"CONCAT('[', A.vendor_code, '] ', B.vendor_name) convert_to vendor",
					"E.official_store_name convert_to official_store_name",
					"G.soa_num convert_to soa_num",
					"IF(G.soa_num IS NULL, 'N/A', IF(J.task_status_id IS NULL,
						'Pending',
						IF(J.task_status_id = ".TASK_STATUS_RETURNED.",
						'Returned',
						CASE
							WHEN J.sequence_no = 1 THEN 'Uploaded'
							WHEN J.sequence_no = 2 THEN 'Transmitted'
							WHEN J.sequence_no = 3 THEN 'Approved'
							ELSE 'Pending'
						END
					))) convert_to soa_status",
					"A.dr_remarks convert_to dr_remarks",
					(!EMPTY($params['sSearch'])? "D.dr_status_name convert_to dr_status_name": "A.dr_status convert_to dr_status_name"),
					"A.org_code",
                    "A.vendor_code"
			);

			$filters_ordering	= array(
					"dr_gr_id",
					"A.dr_date",
					"business_center",
					"ref_no",
					"vendor",
					"official_store_name",
					"soa_num",
					"soa_status",
					"dr_remarks",
					"dr_status_name"
			);

			$group_by		= "";

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(DISTINCT A.dr_gr_id) total, A.vendor_code, A.org_code';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);

				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);

				$order			.= ", date";
				$group_by		= "GROUP BY A.dr_gr_id";
			}

			$query =<<<EOS
				SELECT
					$select_fields
					FROM $this->tbl_delivery_goods_receipt A
					JOIN $this->tbl_organizations I ON I.org_code = A.org_code
					JOIN $this->tbl_vendors B ON A.vendor_code = B.vendor_code
					LEFT JOIN $this->tbl_sites E ON A.site_id = E.site_id
					LEFT JOIN $this->tbl_delivery_status D ON A.dr_status = D.dr_status_code
					LEFT JOIN $this->tbl_pria_references F ON A.dr_gr_id = F.dr_gr_id
					LEFT JOIN $this->tbl_soa G ON F.soa_id = G.soa_id
					LEFT JOIN $this->tbl_pria_workflows H ON G.soa_id = H.reference_id
    				AND G.account_group_code = H.account_group_code
    				LEFT JOIN (
    					SELECT a.pria_workflow_id, c.pria_task_id, c.core_workflow_task_id, c.sequence_no, c.task_status_id
    					FROM $this->tbl_pria_workflow_stages a
    					JOIN (
							SELECT MAX(sequence_no) seq_no, pria_stage_id
						    FROM $this->tbl_pria_tasks
						    WHERE task_status_id IN (?, ?, ?)
						    GROUP BY pria_stage_id
						) b ON a.pria_stage_id = b.pria_stage_id
    					JOIN $this->tbl_pria_tasks c ON b.pria_stage_id = c.pria_stage_id AND b.seq_no = c.sequence_no
    					WHERE a.core_workflow_stage_id = ?
    				) J ON H.pria_workflow_id = J.pria_workflow_id
				WHERE A.account_group_code IN ($q_marks)
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


    public function get_checked_drs_records($dr_arr = array(), $fields=['*'])
    {
        try
        {   
        	$val 	= [];
        	$where 	= 'WHERE 1 = 2';

        	if(!EMPTY($dr_arr[0])){
        		$dr_arr = implode(',', $dr_arr);
        		$where = 'WHERE dr_gr_id IN ('.$dr_arr.')';
			}
			
			$fields_str = implode(',', $fields);

            $query    	=<<<EOS
                SELECT 
                   $fields_str
				FROM 
					$this->tbl_delivery_goods_receipt
                {$where}
                
EOS;
            return $this->query($query, $val, TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    public function update_canceled_delivery_goods_receipt(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_delivery_goods_receipt, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

	public function get_dr_gr_details($dr_gr_id)
	{
		try
		{
			$values	= array($dr_gr_id);
			$query	= <<<EOS
				SELECT A.dr_gr_id, A.dr_cancellation_requestor, A.dr_num, A.org_code, B.account_group_name
				FROM $this->tbl_delivery_goods_receipt A
				LEFT JOIN $this->tbl_param_account_groups B ON A.account_group_code = B.account_group_code
				WHERE A.dr_gr_id = ?
EOS;

			return $this->query($query, $values, TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_dr_gr_details_for_email_request($dr_gr_id_arr)
	{
		try
		{
			$values	= implode('\',\'', $dr_gr_id_arr);
			$query	= <<<EOS
				SELECT A.dr_gr_id, A.dr_cancellation_requestor, GROUP_CONCAT(A.dr_num SEPARATOR ', ') as dr_num, A.org_code, B.account_group_name
				FROM $this->tbl_delivery_goods_receipt A
				LEFT JOIN $this->tbl_param_account_groups B ON A.account_group_code = B.account_group_code
				WHERE A.dr_gr_id IN ('%s')
				GROUP BY A.org_code
EOS;
			$query  = sprintf($query, $values);

			return $this->query($query, [], TRUE, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_drs($where=[], $fields=['*'], $order=[])
	{
		try
		{
			return $this->select_data($fields, $this->tbl_delivery_goods_receipt, TRUE, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

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
}