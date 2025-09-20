<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Boq_model extends Portal_Model
{
	protected $tbl_projects;

    public function __construct()
    {
		parent::__construct();

		$this->tbl_projects 		    = Portal_Model::PORTAL_TABLE_PROJECTS;
		$this->tbl_sites 			    = Portal_Model::PORTAL_TABLE_SITES;
		$this->tbl_vendors 			    = Portal_Model::PORTAL_TABLE_VENDORS;
		$this->tbl_vendor_account_group	= Portal_Model::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP;

		$this->tbl_pria_workflows 	    = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
		$this->tbl_pria_workflow_stages = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
		$this->tbl_pria_tasks 			= Portal_Model::PORTAL_TABLE_PRIA_TASKS;

		$this->tbl_boq 				    = Portal_Model::PORTAL_TABLE_PRIA_BOQ;
		$this->tbl_boq_asset_codes 		= Portal_Model::PORTAL_TABLE_PRIA_BOQ_ASSET;
		$this->tbl_param_boq_categories = Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES;

		$this->tbl_boq_pr 				= Portal_Model::PORTAL_TABLE_PRIA_BOQ_PR;
		$this->tbl_organizations 		= Portal_Model::PORTAL_TABLE_ORGANIZATIONS;

		$this->tbl_vendor_business_centers	= Portal_Model::PORTAL_TABLE_PRIA_VENDOR_BUSINESS_CENTERS;

    }

    public function get_boqs_list($where, $list_flag=NULL, $having='')
    {
    	try
    	{
    		$values	 		= [TASK_STATUS_DONE, TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED, CORE_TASK_BOQ_PRES_APPROVED, CORE_WORKFLOW_CONTRACTOR_BOQ, AG_CONTRACTORS];
			$values_filter	= [];

            $w_marks = $q_marks = "";

			$fields	 = [
				"a.boq_id AS reference_id",
				"a.boq_code AS display_num",
				//"CONCAT(IF(a.inhouse = '".ENUM_NO."', IFNULL(a.new_contractor, d.vendor_name), 'Inhouse'), ' ( ',b.official_store_name,' )')  AS display_name",
				"CONCAT(IF(a.additional_flag = 1, 'Additional Works', 'New Project'), ': ', IF(a.inhouse = '".ENUM_NO."', IFNULL(a.new_contractor, IFNULL(GROUP_CONCAT(DISTINCT h.vendor_name SEPARATOR ', '), d.vendor_name)), 'Inhouse')) AS display_name",
				"CONCAT('Store Name: ', b.official_store_name, '<br/>Business Center : ', f.name, ' Date Approved: ', IF(j.task_status_id IN (?, ?), DATE_FORMAT(j.actual_end_date, '%m/%e/%Y'), IF(j.task_status_id IN (?), 'Disapproved', 'N/A'))) AS display_extra",
				"e.pria_workflow_id",
				"e.org_code",
				"IFNULL(g.confirmed_contractor, e.vendor_code) vendor_code"
			];

			$select_fields	= implode(', ', $fields);

			if(COUNT($where['filter']) > 0)
            {
                $filter         = $where['filter']['having'];
                $values_filter  = $where['filter']['values'];
            }

            if(ISSET($where['limit']))
            	$limit = 'LIMIT '.$where['limit']['from'].','.$where['limit']['to'];

            $group_by	= ($list_flag === NULL)? "GROUP BY a.boq_id": "";

    		$query				=<<<EOS
			SELECT
				$select_fields
			FROM
				$this->tbl_boq a
			JOIN
				$this->tbl_sites    b ON a.site_id = b.site_id
			JOIN
				$this->tbl_pria_workflows e ON a.boq_id = e.reference_id
			LEFT JOIN
				$this->tbl_vendors 	d ON a.recommended_vendor_code = d.vendor_code
			JOIN
				$this->tbl_organizations f ON f.org_code = e.org_code
			LEFT JOIN
				$this->tbl_boq_asset_codes g ON  a.boq_id = g.boq_id
			LEFT JOIN
				$this->tbl_vendors h ON g.confirmed_contractor = h.vendor_code
			LEFT JOIN
				$this->tbl_pria_workflow_stages i ON e.pria_workflow_id = i.pria_workflow_id
			LEFT JOIN
				$this->tbl_pria_tasks j ON i.pria_stage_id = j.pria_stage_id AND j.core_workflow_task_id = ?
			WHERE
				e.core_workflow_id = ?
			AND
				e.account_group_code = ?
			GROUP BY a.boq_id
			$filter
			$having
			ORDER BY
				a.boq_id DESC
			$limit
EOS;

			$values			= array_merge($values, $values_filter);

			if($list_flag === NULL)
	    		return $this->query($query, $values);
    		else
                return count($this->query($query, $values));
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
	}

	public function get_boq_details($boq_id)
	{
		try
		{
			$enum_no	= ENUM_NO;

			$site_url	= base_url().PORTAL_TRANSACTIONS.'/'.PORTAL_CONTRACTOR.'?keyword=%s#tab_site_nominations';

			$query 		= <<<EOS
			SELECT
				a.boq_code,
				b.site_code,
				b.site_num,
				b.official_store_name,
				IF(a.inhouse = '{$enum_no}', IFNULL(a.new_contractor, c.vendor_name), 'Inhouse') AS recommended_contractor,
				a.recommended_amount,
				a.final_amount,
				a.internal_order,
				a.boq_category_code,
				e.category_name,
				a.budgeted_flag,
				b.org_code,
				b1.name,
				c.vendor_code,
				a.inhouse,
				a.layout_specifications,
				IF(b.suggested_store_name IS NOT NULL AND b.suggested_store_name != '', CONCAT('<a href=', REPLACE('$site_url', '%s', b.site_num), '>', b.site_num, ' - ', b.official_store_name, '</a>'), 'N/A') site_nomination,
				a.additional_flag,
				a.reco_amount_civil_works,
				a.reco_amount_signage,
				a.budget_amount_civil_works,
				a.budget_amount_signage,
				a.final_amount_civil_works,
				a.final_amount_signage,
				a.boq_recommendation,
				a.boq_recommendation_bh,
				a.boq_recommendation_rh,
				a.boq_justification,
				a.boq_reco_approver
			FROM
				$this->tbl_boq a
			JOIN
				$this->tbl_sites   b  ON a.site_id = b.site_id
			JOIN
				$this->tbl_organizations b1 ON b1.org_code = b.org_code
			LEFT JOIN
				$this->tbl_vendors c  ON a.recommended_vendor_code 	= c.vendor_code
			LEFT JOIN
				$this->tbl_param_boq_categories e ON e.category_code = a.boq_category_code
			WHERE
				a.boq_id = ?
EOS;
		 return $this->query($query, [$boq_id], TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_boqs(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_boq, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}


	public function get_boq_asset_codes(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_boq_asset_codes, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function get_contractors()
	{
		try
		{
			$query =<<<EOS
			SELECT
				a.vendor_code,
				a.vendor_name
			FROM
				$this->tbl_vendors a
			JOIN
				$this->tbl_vendor_account_group b ON a.vendor_code = b.vendor_code
			WHERE
				b.account_group_code = ?
EOS;
			return $this->query($query, [AG_CONTRACTORS], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	public function get_contractors_by_org_code($org_code)
	{
		try
		{
			$query =<<<EOS
			SELECT
				a.vendor_code,
				a.vendor_name
			FROM
				$this->tbl_vendors a
			JOIN
				$this->tbl_vendor_account_group b ON a.vendor_code = b.vendor_code
			JOIN
				$this->tbl_vendor_business_centers c ON b.vendor_code = c.vendor_code
			WHERE
				b.account_group_code = ?
			AND
				c.org_code 			 =  ?
EOS;
			return $this->query($query, [AG_CONTRACTORS, $org_code], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/**
	 * @Author: kevin villarojo
	 * @Date: 2019-10-08 11:45:44
	 * @Desc:
	 * @Referenced : Po.php
	 */
	public function get_contractors_by_boq_id($boq_id)
	{
		try
		{
			$query =<<<EOS
			SELECT
				b.vendor_code,
				b.vendor_name
			FROM
				$this->tbl_boq_asset_codes a
			JOIN
				$this->tbl_vendors b ON a.confirmed_contractor = b.vendor_code
			WHERE
				a.boq_id = ?
			GROUP BY
				b.vendor_code
EOS;
			return $this->query($query, [$boq_id], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/**
	 * @Author: kevin villarojo
	 * @Date: 2019-09-20 15:47:34
	 * @Desc:
	 * @referenced by : Indicate_contractor.php
	 */
	public function get_boq_asset_codes_grouped_by_contractor($boq_id)
	{
		try
		{
			$query =<<<EOS
			SELECT
				GROUP_CONCAT(a.asset_type) AS asset_types,
				a.confirmed_contractor,
				b.vendor_name,
				GROUP_CONCAT(c.category_name ORDER BY c.category_name SEPARATOR '<br/>') AS asset_type_names
			FROM
				$this->tbl_boq_asset_codes a
			JOIN
				$this->tbl_vendors b ON a.confirmed_contractor = b.vendor_code
			JOIN
				$this->tbl_param_boq_categories c ON a.asset_type = category_code
			WHERE
				a.boq_id = ?
			GROUP BY
				a.confirmed_contractor
			ORDER BY
				a.seq_no
EOS;
			return $this->query($query, [$boq_id], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/**
	 * @Author: kevin villarojo
	 * @Date: 2019-09-20 15:47:34
	 * @Desc:
	 * @referenced by : Encode_asset.php
	 */
	public function get_boq_asset_codes_joined_to_params($boq_id)
	{
		try
		{
			$query =<<<EOS
			SELECT
				a.asset_type,
				c.category_name,
				a.asset_code,
				a.internal_order
			FROM
				$this->tbl_boq_asset_codes a
			JOIN
				$this->tbl_param_boq_categories c ON a.asset_type = category_code
			WHERE
				a.boq_id = ?
			ORDER BY
				a.seq_no, c.category_name
EOS;
			return $this->query($query, [$boq_id], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** INSERT FUNCTIONS */
	public function insert_boq($data)
	{
		try
		{
			return $this->insert_data($this->tbl_boq, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_boq_asset($data)
	{
		try
		{
			return $this->insert_data($this->tbl_boq_asset_codes, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_boq_pr($data)
	{
		try
		{
			return $this->insert_data($this->tbl_boq_pr, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** UPDATE FUNCTIONS */
	public function update_boq($fields, $where)
	{
		try
		{
			$this->update_data($this->tbl_boq, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_boq_asset($fields, $where)
	{
		try
		{
			$this->update_data($this->tbl_boq_asset_codes, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** DELETE FUNCTIONS */
	public function delete_boq_asset($where)
	{
		try
		{
			$this->delete_data($this->tbl_boq_asset_codes, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}