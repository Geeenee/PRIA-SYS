<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Renewal_model extends Portal_Model
{
	public $tbl_contracts;
    public $tbl_pria_workflows;
	public $tbl_vendors;
    public $tbl_sites;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_contracts            = parent::PORTAL_TABLE_CONTRACTS;
        $this->tbl_pria_workflows       = parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_vendors				= parent::PORTAL_TABLE_VENDORS;
        $this->tbl_sites                = parent::PORTAL_TABLE_SITES;
        $this->tbl_organizations        = parent::PORTAL_TABLE_ORGANIZATIONS;
    }

    public function get_contract_renewal_list($where, $list_flag=NULL, $having='')
    {
    	try
    	{
            $values_temp            = array();
    		$values				    = array();
            $values_filter          = array();

            $w_marks = $q_marks = $limit = $filter = "";
 
            $date_format = FORMAT_DATE_DISPLAY_DB;
             
            $fields		 = array(
                    "A.contract_id AS reference_id",
                    "A.contract_code AS display_num",
                    "CONCAT('Lessor: ', B.vendor_name, '<br/>Store: ', D.official_store_name) AS display_name",
                    "CONCAT('Expiration Date: ', DATE_FORMAT(IF(A.saved_flag = ".MAINTAINER_YES.", A.date_to, A.recommended_date_to), '{$date_format}'), ' Ref. Contract #: ', F.contract_code, '<br/>Business Center: ', E.name) AS display_extra",
                    "C.pria_workflow_id",
                    "A.org_code",
                    "A.vendor_code",
                    "A.created_by",
                    "C.status_code"
            );

            $select_fields	= implode(', ', $fields);

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

    		$query				=<<<EOS
					SELECT $select_fields
					FROM $this->tbl_contracts A
                    LEFT JOIN $this->tbl_vendors B ON A.vendor_code = B.vendor_code
                    JOIN $this->tbl_pria_workflows C ON A.contract_id = C.reference_id
                    AND A.account_group_code = C.account_group_code
                    AND C.core_workflow_id IN ($w_marks)
                    LEFT JOIN $this->tbl_sites D ON A.site_id = D.site_id
                    LEFT JOIN $this->tbl_organizations E ON D.org_code = E.org_code
                    LEFT JOIN $this->tbl_contracts F ON A.reference_contract_id = F.contract_id
					WHERE A.account_group_code IN ($q_marks)
                    $filter
                    $having
					ORDER BY C.pria_workflow_id DESC
                    $limit
EOS;

            $values = array_merge($values, $values_temp, array(), $values_filter);
            
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

    public function get_contract($where=array(), $fields=array('*'), $order=array())
    {
        try
        {
            return $this->select_data($fields, $this->tbl_contracts, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function check_contracts($contract_code = NULL)
    {
        try
        {
            $query  = <<<EOS
                    SELECT COUNT(*) as cnt
                        FROM $this->tbl_contracts 
                    WHERE ( contract_code = ? and contract_status_code = ? ) or ( contract_code = ? and contract_status_code = ? )
EOS;

            return $this->query($query, array($contract_code, CONTRACT_NEW, $contract_code, CONTRACT_RENEWED), TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_contract(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_contracts, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public function update_contract(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_contracts, $fields, $where);
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

    public function get_site_contract($store, $fields)
    {
        try
        {
            $query  = <<<EOS
                SELECT A.*, B.*, C.vendor_code, C.vendor_name
                FROM $this->tbl_sites A
                LEFT JOIN $this->tbl_contracts B 
                    ON A.site_id = B.site_id
                JOIN $this->tbl_vendors C 
                    ON B.vendor_code = C.vendor_code
                WHERE A.site_id = ?
                ORDER BY B.contract_id
                    DESC limit 1
EOS;
          
            return $this->query($query, array($store), TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_site_with_contracts($status_code=SITE_STATUS_COMPLETED, $contract_status_arr=[], $org_codes=[], $with_expired=FALSE)
    {
        try
        {
          //  $contract_status    = CONTRACT_OVERDUE;
            $and = '';

            if( ! EMPTY($contract_status_arr))
            {
                $expired_where  = ($with_expired)? " OR (B.contract_status_code = '".CONTRACT_EXPIRED."' AND DATE_ADD(B.expiration_date, INTERVAL 1 YEAR) >= CURDATE())": "";

                $csa  = implode('\',\'', $contract_status_arr);
                $and .=<<<EOS
                AND 
                    (B.contract_status_code IN ('$csa') $expired_where)
EOS;
            }

            if( ! EMPTY($org_codes))
            {
                $orc  = implode('\',\'', $org_codes);
                $and .=<<<EOS
                AND 
                    A.org_code IN ('$orc')
EOS;
            }

            $query  = <<<EOS
                SELECT * 
                FROM 
                    $this->tbl_sites A 
                JOIN 
                    $this->tbl_contracts B ON A.site_id = B.site_id 
                WHERE 
                    A.status_code = ? 
                $and
                GROUP BY 
                    A.site_id

EOS;
            return $this->query($query, array($status_code), TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_latest_contract_no($two_dig_year)
    {
        try
        {
            $query=<<<EOS
                SELECT 
                    contract_code
                FROM 
                    contracts 
                WHERE 
                    SUBSTR(contract_code, 5, 2) = ? 
                ORDER BY 
                    CAST(SUBSTR(contract_code, 8, 5) AS UNSIGNED) DESC 
                LIMIT 1
EOS;

            $result = $this->query($query, [$two_dig_year], TRUE, FALSE);

            return $result['contract_code'];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}