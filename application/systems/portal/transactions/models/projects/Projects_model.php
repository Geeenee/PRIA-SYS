<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects_model extends Portal_Model
{
    private $tbl_pria_workflows;
    private $tbl_projects;
	private $tbl_sites;
	private $tbl_project_types;
	private $tbl_boq_asset_codes;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_projects	                	= parent::PORTAL_TABLE_PROJECTS;
        $this->tbl_sites			        	= parent::PORTAL_TABLE_SITES;
		$this->tbl_vendors			        	= parent::PORTAL_TABLE_VENDORS;
		$this->tbl_project_types				= parent::PORTAL_TABLE_PROJECT_TYPES;
		$this->tbl_boq_asset_codes 				= Portal_Model::PORTAL_TABLE_PRIA_BOQ_ASSET;

		$this->tbl_pria_workflows				= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_workflow_stages        	= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_pria_tasks                  	= Portal_Model::PORTAL_TABLE_PRIA_TASKS;
		$this->tbl_pria_task_predecessors     	= Portal_Model::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
		$this->tbl_organizations 				= Portal_Model::PORTAL_TABLE_ORGANIZATIONS;

		$this->tbl_boq 				    		= Portal_Model::PORTAL_TABLE_PRIA_BOQ;

		$this->tbl_param_contractor_cat_files  	= Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_CATEGORY_FILES;
    }

    public function get_projects_list($where, $list_flag=NULL, $having='')
    {
    	try
    	{
    		$values					= array(CORE_WORKFLOW_CONTRACTOR_PROJECT, AG_CONTRACTORS);
            $values_filter          = array();

            $w_marks = $q_marks = $limit = $filter = "";

			$fields				= array(
					"A.project_id AS reference_id",
					"A.project_code AS display_num",
					"B.official_store_name AS display_name",
					"CONCAT('BOQ: ', D.boq_code, ', Business Center : ', E.name) AS display_extra",
					"C.pria_workflow_id AS pria_workflow_id",
					"C.org_code",
					"C.vendor_code"
			);

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

    		$query					=<<<EOS
					SELECT
						$select_fields
					FROM
						$this->tbl_projects A
					JOIN
						$this->tbl_sites 	B ON A.site_id = B.site_id
					JOIN
						$this->tbl_pria_workflows C ON A.project_id = C.reference_id
					AND
						C.core_workflow_id = ?
					AND
						C.account_group_code = ?
					JOIN
						$this->tbl_boq 	D ON D.boq_id = A.boq_id
					JOIN
						$this->tbl_organizations E ON C.org_code = E.org_code
					$filter
					$having
					ORDER BY A.project_id DESC
                    $limit
EOS;

			$values	= array_merge($values, $values_filter);

			if($list_flag === NULL)
    		{
	    		return $this->query($query, $values);
    		}
    		else
    		{
				/*$next_records		= $this->query($query, $values, TRUE, FALSE);
				return $next_records['cnt'];*/
                return count($this->query($query, $values));
    		}
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
	}

	public function get_project_vendors_by_project_id($project_id)
	{
		try
		{
			$query =<<<EOS
				SELECT
					e.vendor_name
				FROM
					$this->tbl_projects a
				JOIN
					$this->tbl_project_types b
				JOIN
					$this->tbl_boq_asset_codes c ON a.boq_id = c.boq_id AND b.project_type_code = c.asset_type
				JOIN
					$this->tbl_vendors e 		 ON c.confirmed_contractor = e.vendor_code
				WHERE
					a.project_id = ?
EOS;

			return $this->query($query, [$project_id], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_project(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_projects, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-07-23 16:00:02
     * @Desc:
     * @ReferencedBy:  Projects.php
     */
    public function get_initial_project_tasks($pria_workflow_id)
    {
        try
        {
            $query=<<<EOS
			SELECT
				c.pria_task_id
			FROM
				$this->tbl_pria_workflows a
			JOIN
				$this->tbl_pria_workflow_stages b ON a.pria_workflow_id = b.pria_workflow_id
			JOIN
				$this->tbl_pria_tasks c ON c.pria_stage_id = b.pria_stage_id
			LEFT JOIN
				$this->tbl_pria_task_predecessors d ON c.pria_task_id = d.pria_task_id
			WHERE
		   		a.pria_workflow_id = ?
			AND
				d.pre_pria_task_id IS NULL
EOS;

	        return $this->query($query, [$pria_workflow_id], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

	public function get_projects(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_projects, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_project_types(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_project_types, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** INSERT FUNCTIONS */
	public function insert_project($data)
	{
		try
		{
			return $this->insert_data($this->tbl_projects, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_project_types($data)
	{
		try
		{
			return $this->insert_data($this->tbl_project_types, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
    }

    /** UPDATE FUNCTIONS */
	public function update_project($fields, $where)
	{
		try
		{
			$this->update_data($this->tbl_projects, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

}