<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_requests_model extends Portal_Model
{
	public $tbl_purchase_requisitions;
	public $tbl_pria_workflows;

    public $tbl_param_purchasing_group;

    public $tbl_users;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_purchase_requisitions	= parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
        $this->tbl_pria_workflows			= parent::PORTAL_TABLE_PRIA_WORKFLOWS;

		$this->tbl_pr_cost_centers			= parent::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;
		$this->tbl_param_purchasing_group   = parent::PORTAL_TABLE_PARAM_PURCHASING_GROUP;

		$this->tbl_boq						= parent::PORTAL_TABLE_PRIA_BOQ;
		$this->tbl_boq_pr					= parent::PORTAL_TABLE_PRIA_BOQ_PR;

        $this->tbl_sites                    = parent::PORTAL_TABLE_SITES;
		$this->tbl_organizations 			= parent::PORTAL_TABLE_ORGANIZATIONS;

        $this->tbl_users                    = parent::CORE_TABLE_USERS;
    }

    public function get_purchase_requests_list($where, $list_flag=NULL, $having='')
    {
    	try
    	{
            $values_temp            = array();
    		$values					= array();
            $values_filter          = array();

    		$w_marks = $q_marks	= $limit = $filter = "";

    		/*if($list_flag === NULL)
    		{*/
	    		$fields				= array(
	    				"A.pr_id AS reference_id",
						"A.pr_num AS display_num",
						"B.pria_workflow_id",
                        "E.org_code",
                        "B.vendor_code"
	    		);

	    		if(array_intersect([AG_CONTRACTORS], $where['where']['ag_codes']))
	    		{
	    			$fields			= array_merge($fields, [
						"CONCAT('Requestor: ', IF(AGDEC(A.requestor) IS NULL OR AGDEC(A.requestor) = '', 'N/A', AGDEC(A.requestor)), '<br/>', 'Site/s: ', GROUP_CONCAT(DISTINCT E.official_store_name SEPARATOR ', ')) AS display_name",
						"CONCAT('BOQ: ', G.boq_code, ', Business Center: ', GROUP_CONCAT(DISTINCT F.name SEPARATOR ', '), '<br/>Project Type: ', IF(A.additional_flag = 1, 'Additional Works', 'New Project')) AS display_extra",
	    			]);

	    			$join			=<<<EOS
						JOIN $this->tbl_boq_pr D 
							ON A.pr_id = D.pr_id
						JOIN $this->tbl_boq G
							ON D.boq_id = G.boq_id
						JOIN
							$this->tbl_sites E
							ON G.site_id = E.site_id
						JOIN 
							$this->tbl_organizations F 
							ON E.org_code = F.org_code
EOS;
	    		}
	    		else
	    		{
	    			$fields			= array_merge($fields, [
						"CONCAT('Requestor: ', IF(AGDEC(A.requestor) IS NULL OR AGDEC(A.requestor) = '', 'N/A', AGDEC(A.requestor)), '<br/>', 'Cost Center/s: ', GROUP_CONCAT(DISTINCT E.official_store_name SEPARATOR ', ')) AS display_name",
						"CONCAT('Business/Support Center: ', GROUP_CONCAT(DISTINCT F.name SEPARATOR ', '), ', Purchasing Group: ', IF(C.purchasing_group_name IS NULL OR C.purchasing_group_name = '', 'N/A', C.purchasing_group_name)) AS display_extra",
	    			]);

	    			$join			=<<<EOS
						JOIN $this->tbl_pr_cost_centers D 
							ON A.pr_id = D.pr_id 
						JOIN
							$this->tbl_sites E
							ON D.cost_center_code = E.cost_center_code
						JOIN 
							$this->tbl_organizations F 
							ON E.org_code = F.org_code
						LEFT JOIN $this->tbl_param_purchasing_group C 
							ON A.purchasing_group_code = C.purchasing_group_code
EOS;
	    		}

	    		$fields				= 

    			$select_fields		= implode(', ', $fields);
    		/*}
    		else
    		{
    			$select_fields		= "COUNT(A.pr_id) AS cnt";
    		}*/

            if(COUNT($where['where']['workflow_ids']) > 0)
            {
                foreach($where['where']['workflow_ids'] AS $key => $workflow_id)
                {
                    $w_marks        .= ($key==0)? "?": ", ?";
                    $values[]       = $workflow_id;
                }
            }

    		if(COUNT($where['where']['ag_codes']) > 0)
    		{
    			foreach($where['where']['ag_codes'] AS $key => $ag_code)
    			{
    				$q_marks		.= ($key==0)? "?": ", ?";
    				$values_temp[]	= $ag_code;
    			}
    		}

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
						$this->tbl_purchase_requisitions A
					LEFT JOIN $this->tbl_pria_workflows B 
						ON A.pr_id = B.reference_id
                    	AND A.account_group_code = B.account_group_code
						AND B.core_workflow_id IN ($w_marks)
					$join
					WHERE A.account_group_code IN ($q_marks)
					GROUP BY A.pr_id
					$filter
                    $having
					ORDER BY A.pr_id DESC
                    $limit
EOS;
			
			$values					= array_merge($values, $values_temp, $values_filter);
			// var_dump('<pre>');
			// var_dump($values);
			// var_dump($query);
			//print_var_export($query, $values); die;
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
}