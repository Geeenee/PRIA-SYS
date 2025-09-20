<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Soa_model extends Portal_Model
{
    public $tbl_pria_workflows;
    public $tbl_soa;
    public $tbl_vendors;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_pria_workflows	= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_soa				= parent::PORTAL_TABLE_SOA;
        $this->tbl_vendors			= parent::PORTAL_TABLE_VENDORS;
    }

    public function get_soa_list($where, $list_flag=NULL)
    {
    	try
    	{
            $values_temp			= array();
    		$values					= array();

            $w_marks = $q_marks     = "";

    		/*if($list_flag === NULL)
    		{*/
	    		$fields				= array(
	    				"A.soa_id AS reference_id",
						"A.soa_num AS display_num",
						"AGDEC(B.vendor_name) AS display_name",
						"CONCAT('Date Created: ', DATE_FORMAT(A.created_date, '%d %M %Y')) AS display_extra",
						"C.pria_workflow_id"
	    		);

    			$select_fields		= implode(', ', $fields);
    		/*}
    		else
    		{
    			$select_fields		= "COUNT(A.soa_id) AS cnt";
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

            if(ISSET($where['limit']))
            {
                $limit = 'LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

    		$query					=<<<EOS
					SELECT $select_fields
					FROM $this->tbl_soa A
                    LEFT JOIN $this->tbl_vendors B ON A.vendor_code = B.vendor_code
                    LEFT JOIN $this->tbl_pria_workflows C ON A.soa_id = C.reference_id
					AND C.core_workflow_id IN ($w_marks)
                    AND C.account_group_code IN ($q_marks)
					WHERE A.account_group_code IN ($q_marks)
					ORDER BY A.soa_id ASC
                    $limit
EOS;

			$values					= array_merge($values, $values_temp, $values_temp);

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