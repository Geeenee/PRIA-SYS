<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_nominations_model extends Portal_Model
{
	public $tbl_business_center;

	public $tbl_sites;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_business_center		= parent::PORTAL_TABLE_BUSINESS_CENTER;

		$this->tbl_sites				= parent::PORTAL_TABLE_SITES;
		$this->tbl_pria_workflows 		= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
    }

    public function get_site_nominations_list($where, $list_flag=NULL, $having='')
    {
    	try
    	{
            $values_temp            = array();
    		$values				    = array();
            $values_filter          = array();

            $w_marks = $q_marks = $limit = $filter = "";

    		/*if($list_flag === NULL)
    		{*/
	    		$fields			= array(
	    				"A.site_id AS reference_id",
						"A.site_code AS display_num",
						"CONCAT('Suggested: ', A.suggested_store_name) AS display_name",
						"CONCAT('Business Center: ', B.bc_name) AS display_extra",
						"0 AS pria_workflow_id",
						"C.org_code",
						"C.vendor_code"
	    		);

    			$select_fields	= implode(', ', $fields);
    		/*}
    		else
    		{
    			$select_fields	= "COUNT(A.site_id) AS cnt";
    		}*/

            /*if(COUNT($where['where']['workflow_ids']) > 0)
            {
                foreach($where['where']['workflow_ids'] AS $key => $workflow_id)
                {
                    $w_marks        .= ($key==0)? "?": ", ?";
                    $values[]       = $workflow_id;
                }
            }*/

    		if(COUNT($where['where']['ag_codes']) > 0)
    		{
    			foreach($where['where']['ag_codes'] AS $key => $ag_code)
    			{
    				$q_marks	.= ($key==0)? "?": ", ?";
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

			$con_ag_code 		= AG_CONTRACTORS;
    		$query				=<<<EOS
					SELECT $select_fields
					FROM $this->tbl_sites A
					LEFT JOIN $this->tbl_business_center B ON A.bc_code = B.bc_code
					JOIN $this->tbl_pria_workflow C ON A.site_id = C.reference_id AND C.account_group_code = '$con_ag_code'
					$filter
					$having
					ORDER BY A.site_id DESC
                    $limit
EOS;
            $values                 = array_merge(/*$values, $values_temp, */$values_filter);

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
}