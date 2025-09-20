<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* @Author      : Jhun Baria
* @Date        : 2023-08-11 22: 00: 00 
* @Desc        : The Following Controller is Made for the purpose of CDI Store Renovation Transaction
* @ReferencedBy: 
*/

class Cdi_payments_model extends Portal_Model
{
	public $tbl_business_center;

	public $tbl_cdi_payments;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_business_center		= parent::PORTAL_TABLE_ORGANIZATIONS;

		$this->tbl_cdi_payments			= PORTAL_TABLE_CDI_PAYMENTS;

		$this->tbl_cdi_boms    			= PORTAL_TABLE_CDI_BOMS;

        $this->tbl_core_users           = parent::CORE_TABLE_USERS;
		
		$this->tbl_pria_workflow		= parent::PORTAL_TABLE_PRIA_WORKFLOWS;

		$this->tbl_pria_stages			= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;

		$this->tbl_pria_tasks			= parent::PORTAL_TABLE_PRIA_TASKS;

		$this->tbl_param_contactor_process_categories	= parent::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES;
    }

    public function get_payments_list($where, $list_flag=NULL, $having='')
    {

    	try
    	{
    		$values	 			= [
				ORG_TYPE_BUSINESS_CENTER, 
				CORE_WORKFLOW_STRRNV_PAYMENTS_SEC_DEP_RENT_ADV,
				CORE_WORKFLOW_STRRNV_PAYMENTS_MALL_CHARGE,
				AG_STORE_RENOVATION, 
			];
    		$values_filter		= [];
			$date_format	 	= FORMAT_DATE_DISPLAY_DB;

            $w_marks = $q_marks = $limit = $filter = "";

			$fields	 = [
					"A.payment_id AS reference_id",
					"A.soa_num AS display_num",
					"f.official_store_name AS display_name",
					"CONCAT(
						'Business Center: ', B.name, '&nbsp;&nbsp;&nbsp;&nbsp;',
						'BOM No:', f.bom_num, '<br>',
						IF(a.rfp_no IS NULL, '', CONCAT('RFP No:', a.rfp_no, '&nbsp;&nbsp;&nbsp;&nbsp;')),
						'SOA Date:', a.soa_document_date
						) AS display_extra",
					"e.pria_workflow_id",
					"e.org_code",
					// "e.vendor_code"
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
						$this->tbl_cdi_payments A
					JOIN 
						$this->tbl_business_center B ON A.org_code = B.org_code AND B.org_type_code = ?
					JOIN 
                    	$this->tbl_cdi_boms f   	 ON f.bom_id = A.bom_id
					JOIN 
                    	$this->tbl_pria_workflow e   ON A.payment_id = e.reference_id
                    
						AND (e.core_workflow_id   = ? OR e.core_workflow_id   = ?)

						AND e.account_group_code = ?
					$filter
					$having
					ORDER BY 
						A.payment_id DESC
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

	public function get_payments(array $where=[], array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_cdi_payments, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}
	
	public function get_payment_details($payment_id, array $fields=['*'])
	{
		try
		{
			$arr   = implode(',', $fields);
			$query =<<<EOS
				SELECT
					$arr
				FROM 
					$this->tbl_cdi_payments a
				JOIN
					$this->tbl_business_center b ON a.org_code = b.org_code AND b.org_type_code = ?
				JOIN 
					$this->tbl_cdi_boms c ON c.bom_id = A.bom_id
                LEFT JOIN 
					$this->tbl_core_users d ON d.user_id = a.finance_in_charge
				WHERE 
					a.payment_id = ?
EOS;
			return $this->query($query, [ORG_TYPE_BUSINESS_CENTER, $payment_id], TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** INSERT FUNCTIONS */
	public function insert_payment($data)
	{
		try
		{

			return $this->insert_data($this->tbl_cdi_payments, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** UPDATE FUNCTIONS */
	public function update_payment($fields, $where)
	{
		try
		{
			$this->update_data($this->tbl_cdi_payments, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

}