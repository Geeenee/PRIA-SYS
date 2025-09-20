<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_nominations_model extends Portal_Model
{
	public $tbl_business_center;

	public $tbl_sites;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_business_center		= parent::PORTAL_TABLE_ORGANIZATIONS;

		$this->tbl_sites				= parent::PORTAL_TABLE_SITES;
		
		$this->tbl_pria_workflow		= parent::PORTAL_TABLE_PRIA_WORKFLOWS;

		$this->tbl_pria_stages			= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;

		$this->tbl_pria_tasks			= parent::PORTAL_TABLE_PRIA_TASKS;

		$this->tbl_param_contactor_process_categories	= parent::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES;
    }

    public function get_site_nominations_list($where, $list_flag=NULL, $having='')
    {
    	try
    	{
    		$values	 			= [TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED, ORG_TYPE_BUSINESS_CENTER, CORE_WORKFLOW_CONTRACTOR_SITE_NOMINATION, AG_CONTRACTORS, CORE_WORKFLOW_STAGE_SITE_NOMINATION_APPROVAL, CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION];
    		$values_filter		= [];
			$date_format	 	= FORMAT_DATE_DISPLAY_DB;

            $w_marks = $q_marks = $limit = $filter = "";

			$fields	 = [
					"A.site_id AS reference_id",
					"A.site_num AS display_num",
					"IF(A.official_store_name IS NULL, CONCAT('Suggested: ', A.suggested_store_name), CONCAT('Official: ', A.official_store_name) ) AS display_name",
					"CONCAT('Business Center: ', B.name, ', Date Approved: ', IF(g.task_status_id IN (?, ?) AND g.actual_end_date IS NOT NULL, DATE_FORMAT(g.actual_end_date, '$date_format'), IF(g.task_status_id IN (?), 'Disapproved', 'N/A')), '<br/>Nomination Type: ', h.category_name) AS display_extra",
					"e.pria_workflow_id",
					"e.org_code",
					"e.vendor_code"
			];
			
			$select_fields	= implode(', ', $fields);

            if(COUNT($where['filter']) > 0)
            {
                $filter         = $where['filter']['having'];
                $values_filter  = $where['filter']['values'];
            }
  
            if(ISSET($where['limit']))
            {
                $limit = 'LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }
			
    		$query	  =<<<EOS
					SELECT 
						$select_fields
					FROM 
						$this->tbl_sites A
					JOIN 
						$this->tbl_business_center B ON A.org_code = B.org_code AND B.org_type_code = ?
					JOIN 
                    	$this->tbl_pria_workflow e   ON A.site_id = e.reference_id
                    
						AND e.core_workflow_id   = ?

						AND e.account_group_code = ?
					JOIN
						$this->tbl_pria_stages f ON e.pria_workflow_id = f.pria_workflow_id AND f.core_workflow_stage_id = ?
					JOIN
						$this->tbl_pria_tasks g ON g.pria_stage_id = f.pria_stage_id AND g.core_workflow_task_id = ?
					JOIN $this->tbl_param_contactor_process_categories h ON A.nomination_type_code = h.category_code
					$filter
					$having
					ORDER BY 
						A.site_id DESC
                    $limit
EOS;

			$values			= array_merge($values, $values_filter);
			// print_var_export($query, $values); die;
			if($list_flag === NULL)
    		{
	    		return $this->query($query, $values);
    		}
    		else
    		{ 
				/*$next_records	= $this->query($query, $values, TRUE, FALSE);
				return $next_records['cnt'];*/
                return count($this->query($query, $values));
    		}
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
	}

	
/* 
	public function get_site(array $where=[], array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_sites, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	} */

	public function get_sites(array $where=[], array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_sites, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}
	
	public function get_site_details($site_id, array $fields=['*'])
	{
		try
		{
			$arr   = implode(',', $fields);
			$query =<<<EOS
				SELECT
					$arr
				FROM 
					$this->tbl_sites a
				JOIN
					$this->tbl_business_center b ON a.org_code = b.org_code AND b.org_type_code = ?
				WHERE 
					a.site_id = ?
EOS;
			return $this->query($query, [ORG_TYPE_BUSINESS_CENTER, $site_id], TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** INSERT FUNCTIONS */
	public function insert_site($data)
	{
		try
		{
			return $this->insert_data($this->tbl_sites, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** UPDATE FUNCTIONS */
	public function update_site($fields, $where)
	{
		try
		{
			$this->update_data($this->tbl_sites, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}