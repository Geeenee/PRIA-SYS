<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_orders_model extends Portal_Model
{
    public $tbl_pria_workflows;
    public $tbl_purchase_requisitions;
    public $tbl_purchase_orders;
    public $tbl_vendors;

    public $tbl_pria_references;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_pria_workflows					= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_purchase_requisitions			= parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
        $this->tbl_purchase_orders					= parent::PORTAL_TABLE_PURCHASE_ORDERS;
		$this->tbl_vendors							= parent::PORTAL_TABLE_VENDORS;
		$this->tbl_purchase_request_cost_centers	= parent::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;

        $this->tbl_boq_pr							= parent::PORTAL_TABLE_PRIA_BOQ_PR;
		$this->tbl_boq								= parent::PORTAL_TABLE_PRIA_BOQ;

		$this->tbl_sites							= parent::PORTAL_TABLE_SITES;

		$this->tbl_organizations 					= parent::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_param_purchasing_group   		= parent::PORTAL_TABLE_PARAM_PURCHASING_GROUP;

		$this->tbl_pria_references					= parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;

		$this->tbl_param_purchase_order_types		= parent::PORTAL_TABLE_PURCHASE_ORDER_TYPES;
    }

    public function get_purchase_orders_list($where, $list_flag=NULL, $having='', $tab_module_code)
    {
    	try
    	{
    		$values_temp			= array();
            $values                 = array();
    		$values_filter			= array();

			$w_marks = $q_marks = $limit = $filter = "";

			$join 					= '';
			$dis_name 				= "'PR :'";
			$display_extra 			= "GROUP_CONCAT(DISTINCT B.pr_num SEPARATOR ', ')";
			$date_format	 		= FORMAT_DATE_DISPLAY_DB;

			$emp_pur_group 			= '"N/A"';

			$group_by		= "";

			if($list_flag === NULL)
    		{
				$group_by	= "GROUP BY A.po_id";
    		}


			$group_by	= "GROUP BY A.po_id";

    		if(!EMPTY($having))
    		{
    			$having	= str_replace('org_code', 'X.org_code', $having);
    		}

    		$having	= str_replace('vendor_code', 'A.vendor_code', $having);

			if($tab_module_code == MODULE_PORTAL_TRANS_CONTRACTORS_PO)
			{
				$dis_name 		= "'BOQ : '";
				$display_extra 	= "b2.boq_code";
				$emp_pur_group 	= 'b3.official_store_name';
				$join 			=<<<EOS
					JOIN $this->tbl_boq_pr b1 ON B.pr_id = b1.pr_id
					JOIN $this->tbl_boq b2 ON b1.boq_id = b2.boq_id
					JOIN $this->tbl_sites b3 ON b2.site_id = b3.site_id
					JOIN $this->tbl_organizations X ON b3.org_code = X.org_code
EOS;
				$display_name	= "CONCAT(GROUP_CONCAT(DISTINCT $emp_pur_group SEPARATOR ', '), ' : ', C.vendor_name) AS display_name";
			}
			else
			{
				$join			=<<<EOS
					JOIN $this->tbl_purchase_request_cost_centers Z ON B.pr_id = Z.pr_id
					JOIN $this->tbl_sites Y ON Z.cost_center_code = Y.cost_center_code
					JOIN $this->tbl_organizations X ON Y.org_code = X.org_code
					LEFT JOIN $this->tbl_param_purchasing_group C1 ON B.purchasing_group_code = C1.purchasing_group_code
EOS;
				$display_name	= "CONCAT(GROUP_CONCAT(DISTINCT C1.purchasing_group_name SEPARATOR ', '), ' : ', C.vendor_name) AS display_name";

			}

			$fields				= array(
					"A.po_id AS reference_id",
					"A.po_num AS display_num",
					$display_name,
					"CONCAT($dis_name, $display_extra, ', Business Center : ', GROUP_CONCAT(DISTINCT X.name SEPARATOR ', '),',<br/> ', IF(B.account_group_code = '".AG_CONTRACTORS."', CONCAT('Project Type : ', IF(A.additional_flag = 1, 'Additional Works', 'New Project'), ', '), ''), 'PO Date : ', IF(A.po_date IS NOT NULL, DATE_FORMAT(A.po_date, '$date_format'), 'N/A')) AS display_extra",
					"D.pria_workflow_id",
					"X.org_code",
					"D.vendor_code",
			);

			$select_fields		= implode(', ', $fields);


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
                $limit  = 'LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

    		$query					=<<<EOS
					SELECT $select_fields
					FROM
						$this->tbl_purchase_orders A
					JOIN
						$this->tbl_pria_references F ON A.po_id = F.po_id
					JOIN
						$this->tbl_purchase_requisitions B ON F.pr_id = B.pr_id
					$join
					LEFT JOIN
						$this->tbl_vendors C ON A.vendor_code = C.vendor_code
					LEFT JOIN
						$this->tbl_pria_workflows D ON A.po_id = D.reference_id
						AND B.account_group_code = D.account_group_code
						AND D.core_workflow_id IN ($w_marks)
					WHERE
						B.account_group_code IN ($q_marks)
                    $having
					$group_by
                    $filter
					ORDER BY A.po_id DESC
                    $limit
EOS;
			
			$values					= array_merge($values, $values_temp, $values_filter);
			if($list_flag === NULL)
    		{
	    		return $this->query($query, $values);
    		}
    		else
    		{
                return count($this->query($query, $values));
    		}
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
    }
}