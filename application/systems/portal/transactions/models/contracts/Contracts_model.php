<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts_model extends Portal_Model
{
	public $tbl_param_contract_status;
	public $tbl_param_payment_terms;

	public $tbl_contracts;
	public $tbl_pria_workflows;
	public $tbl_sites;
	public $tbl_vendors;
	public $tbl_organizations;

    public function __construct()
    {
        parent::__construct();

		$this->tbl_param_contract_status	= parent::PORTAL_TABLE_PARAM_CONTRACT_STATUS;
		$this->tbl_param_payment_terms		= parent::PORTAL_TABLE_PARAM_PAYMENT_TERMS;

        $this->tbl_contracts				= parent::PORTAL_TABLE_CONTRACTS;
        $this->tbl_pria_workflows			= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_sites					= parent::PORTAL_TABLE_SITES;
		$this->tbl_vendors					= parent::PORTAL_TABLE_VENDORS;
		$this->tbl_vendor_sites 			= parent::PORTAL_TABLE_VENDOR_SITES;
		$this->tbl_organizations 			= parent::PORTAL_TABLE_ORGANIZATIONS;

		$this->tbl_contract_billing_dates	= parent::PORTAL_TABLE_CONTRACT_BILLING_DATES;
    }

    public function get_contracts_list($wheres, $params=NULL, $having = '')
    {
    	try
    	{
			// Initialize variables
			$where = $join = $filter = $order = $limit = $w_marks = $q_marks = "";
			$values = $filter_val = $values_temp = array();

			$having	= str_replace('org_code', 'A.org_code', $having);
			
			if(COUNT($wheres['workflow_ids']) > 0)
            {
                foreach($wheres['workflow_ids'] AS $key => $workflow_id)
                {
                    $w_marks        .= ($key==0)? "?": ", ?";
                    // $values[]       = $workflow_id;
                }
            }

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
					"A.contract_id",
					"A.contract_code AS ref_no",
					"B.official_store_name AS store_name",
					"C.vendor_name AS lessor",
					"IF(A.saved_flag = ".MAINTAINER_YES.", D.payment_term_name, I.payment_term_name) AS payment_terms",
					"DATE_FORMAT(IF(A.saved_flag = ".MAINTAINER_YES.", A.date_to, A.recommended_date_to), '%m/%d/%Y') AS exp_date",
					"E.contract_code AS ref_contract",
					"F.contract_status_name AS contract_status",
					"A.contract_status_code",
					"A.org_code",
					"A.vendor_code",
					"F.contract_status_id",
					"H.name AS business_center_name"
			);

			$filters			= array(
					"A.contract_code convert_to ref_no",
					"B.official_store_name convert_to store_name",
					"C.vendor_name convert_to lessor",
					"IF(A.saved_flag = ".MAINTAINER_YES.", D.payment_term_name, I.payment_term_name) convert_to payment_terms",
					"DATE_FORMAT(IF(A.saved_flag = ".MAINTAINER_YES.", A.date_to, A.recommended_date_to), '%m/%d/%Y') convert_to exp_date",
					"E.contract_code convert_to ref_contract",
					//(!EMPTY($params['contract_status'])? "A.contract_status_code convert_to contract_status": "F.contract_status_name convert_to contract_status"),
					"F-contract_status_id",
					"A.org_code",
                    "A.vendor_code",
                    "H.name convert_to business_center_name"
			);

			$filters_ordering	= array(
					"ref_no",
					"business_center_name",
					"store_name",
					"lessor",
					"payment_terms",
					"A.date_to",
					"ref_contract",
					"contract_status"
			);

			// If params variable is null, get the total count of records
			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.contract_id) total, A.vendor_code, A.org_code';
			}
			else
			{
				$select_fields	= "SQL_CALC_FOUND_ROWS " . implode(', ', $fields);
				$filter			= $this->filtering($filters, $params, TRUE);
				$order			= $this->ordering($filters_ordering, $params);
				$limit			= $this->paging($params);


				$where			.= $filter["search_str"];
				$values			= array_merge($values, $filter["search_params"]);

				$order			.= ", A.date_to DESC, B.official_store_name ASC, C.vendor_name ASC";

				//echo $order; die;
			}

			$query =<<<EOS
				SELECT
					$select_fields
					FROM $this->tbl_contracts A
					LEFT JOIN $this->tbl_sites B ON A.site_id = B.site_id
					LEFT JOIN $this->tbl_vendors C ON A.vendor_code = C.vendor_code
					LEFT JOIN $this->tbl_param_payment_terms D ON A.payment_term_code = D.payment_term_code
					LEFT JOIN $this->tbl_contracts E ON A.reference_contract_id = E.contract_id
					LEFT JOIN $this->tbl_param_contract_status F ON A.contract_status_code = F.contract_status_code
					LEFT JOIN $this->tbl_organizations H ON A.org_code = H.org_code
					LEFT JOIN $this->tbl_param_payment_terms I ON A.recommended_payment_term_code = I.payment_term_code
				WHERE A.account_group_code IN ($q_marks) AND A.contract_status_code IS NOT NULL
				$where
				$having
				$order
				$limit
EOS;
			
		
			//Added AND A.contract_status_code IS NOT NULL
			if($params === NULL)
			{
				$total	= $this->query($query, $values, TRUE, FALSE);
				return $total['total'];
			}
			else
			{
				/*  print_var_export($query, $values); */
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

    public function get_param_contract_status()
    {
    	try
    	{
    		return $this->select_data(array('contract_status_code', 'contract_status_name', 'contract_status_id'), $this->tbl_param_contract_status, TRUE);
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
    }

    public function get_max_contract()
    {
    	try
    	{
    		return $this->select_data(array('MAX(contract_code) as max_num'), $this->tbl_contracts, FALSE);
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
    }

    public function get_specific_contract($contract_id = NULL)
    {
    	try
    	{

    		$query 	 = <<<EOS
				SELECT
					a.*,
					b.*,
					c.*,
					d.*,
					e.payment_term_name as recommended_payment_term_name
				FROM
					$this->tbl_contracts a
				LEFT JOIN $this->tbl_sites b ON a.site_id = b.site_id
				LEFT JOIN $this->tbl_param_contract_status c ON a.contract_status_code = c.contract_status_code
				LEFT JOIN $this->tbl_param_payment_terms d ON a.payment_term_code = d.payment_term_code
				LEFT JOIN $this->tbl_param_payment_terms e ON a.recommended_payment_term_code = e.payment_term_code
				WHERE a.contract_id = ?
EOS;

			return $this->query($query, [$contract_id], TRUE, FALSE);
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
	}
	
	public function get_available_sites($orgs=[])
	{
		try
		{
			$values 	= [SITE_TYPE_STORE, STATUS_COMPLETED, CONTRACT_EXPIRED];
			$and_orgs 	= '';
			
			if( ! EMPTY($orgs))
			{
				$and_orgs = ' AND a.org_code IN (\''.implode('\',\'', $orgs).'\')';
			}

			$query 		=<<<EOS
				SELECT 
					a.org_code, 
					a.official_store_name,
					a.status_code,
					a.site_id
				FROM 
					$this->tbl_sites a 
				LEFT JOIN 
					$this->tbl_contracts b ON a.site_id = b.site_id 
				WHERE
					a.site_type_code = ?
				AND
					a.status_code  	 = ?
				AND 
					a.official_store_name IS NOT NULL
				AND
				(
					contract_id IS NULL
					OR
					contract_status_code = ?
				)	
				$and_orgs
				ORDER BY
					a.official_store_name ASC
EOS;
				
			return $this->query($query, $values, TRUE, TRUE);		
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** 
	 * @Author: kevin villarojo 
	 * @Date: 2020-03-19 14:06:21 
	 * @Desc: 
	 * @Referenced : Renewal_contract.php 
	 */
	public function get_contracts_for_payment_processing()
	{
		try
		{
			$query =<<<EOS
			SELECT 
				a.contract_id, 
				a.contract_code, 
				a.contract_status_code,
				a.org_code,
				b.billing_date, 
				b.reminder_date, 
				b.notified 
			FROM 	
				$this->tbl_contracts a 
			JOIN
				$this->tbl_contract_billing_dates b ON a.contract_id = b.contract_id
			WHERE 
				a.contract_status_code NOT IN (?, ?, ?)
			AND
				b.reminder_date <= CURDATE()
			AND
				b.notified = ?
			AND
				b.billing_date IS NOT NULL AND b.reminder_date IS NOT NULL
			AND a.saved_flag = ?
EOS;
			return $this->query($query, [CONTRACT_STATUS_EXPIRED, CONTRACT_STATUS_FOR_RENEWAL, CONTRACT_STATUS_RENEWED, ENUM_NO, MAINTAINER_YES], TRUE, TRUE);	
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

    /** INSERT FUNCTIONS */
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

    /** UPDATE FUNCTIONS */
    public function update_contract(array $fields, array $where)
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


	public function insert_contract_billing_dates(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_contract_billing_dates, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	public function update_contract_billing($raw_fields, $where_arr)
	{
		try
        {
            return $this->update_data($this->tbl_contract_billing_dates, $raw_fields, $where_arr);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}


	public function delete_contract_billing_dates(array $where)
    {
        try
        {
            return $this->delete_data($this->tbl_contract_billing_dates, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}
}