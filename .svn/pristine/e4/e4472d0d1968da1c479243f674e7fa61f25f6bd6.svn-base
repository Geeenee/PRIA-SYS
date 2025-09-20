<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_status_model extends Portal_Model{

	private $tbl_pria_workflow;
	private $tbl_payments;
	private $tbl_payment_apvs;
	private $tbl_soa;
	private $tbl_contracts;
	private $tbl_purchase_requisitions;
	private $tbl_purchase_orders;
	private $tbl_internal_orders;
	private $tbl_boq;
	private $tbl_delivery_goods_receipt;
	private $tbl_pria_references;

	public function __construct()
	{
		parent::__construct();
		$this->tbl_pria_workflow 				= Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
		$this->tbl_payments 					= Portal_Model::PORTAL_TABLE_PAYMENTS;
		$this->tbl_payment_apvs 				= Portal_Model::PORTAL_TABLE_PAYMENT_APVS;
		$this->tbl_soa 							= Portal_Model::PORTAL_TABLE_SOA;
		$this->tbl_contracts 					= Portal_Model::PORTAL_TABLE_CONTRACTS;
		$this->tbl_purchase_orders 				= Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
		$this->tbl_internal_orders 				= Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
		$this->tbl_boq 							= Portal_Model::PORTAL_TABLE_PRIA_BOQ;
		$this->tbl_delivery_goods_receipt  		= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
		$this->tbl_pria_references  			= Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
		// $this->tbl_user_orgs  				= Portal_Model::PORTAL_TABLE_USER_ORGS;
		$this->tbl_pria_tab_modules				= Portal_Model::PORTAL_TABLE_PRIA_TAB_MODULE;
		$this->tbl_purchase_requisitions		= Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
		$this->tbl_core_modules					= Portal_Model::CORE_MODULES;
		$this->tbl_organizations					= Portal_Model::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_param_account_groups					= Portal_Model::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
		$this->tbl_sites					= Portal_Model::PORTAL_TABLE_SITES;
		
	}

	public function get_payment_status_list($params = NULL, $searches = NULL, $having = "")
	{
	    try
	    {
	    	$and_where = $where = $order = $limit = $group_by = "";

	    	$import_reference_url	= get_link_url(MODULE_PORTAL_IMPORT_REFERENCE);

	    	$apv_num_url	= "IF(A.ref_b_apv IS NOT NULL, CONCAT('<a href=\"', '$import_reference_url', '/".REF_B_APV."/', A.ref_b_apv, '#tab_apv\">', A.ref_b_apv, '</a>'), NULL)";
	    	$cv_num_url		= "IF(A.ref_b_cv IS NOT NULL, CONCAT('<a href=\"', '$import_reference_url', '/".REF_B_CV."/', A.ref_b_cv, '#tab_apv\">', A.ref_b_cv, '</a>'), NULL)";
	    	$dr_num_url		= "IF(A.ref_b_dr IS NOT NULL, CONCAT('<a href=\"".base_url()."', A.trans_link, '?keyword=', A.ref_a_no, '#', A.link_tab, '\">', A.ref_b_dr, '</a>'), NULL)";

		  	$ref_b_no_select	= "CONCAT_WS(', <br/>',
		  		IFNULL($apv_num_url, NULL),
		  		IFNULL($cv_num_url, NULL),
		  		IFNULL($dr_num_url, NULL)
		  	)";
		  	$ref_b_date_select	= "CONCAT_WS(', <br/>',
		  		IFNULL(IF(A.ref_b_apv_date IS NOT NULL AND A.ref_b_apv_date != '0000-00-00', DATE_FORMAT(A.ref_b_apv_date, '%c/%e/%Y'), IF(A.ref_b_apv IS NOT NULL, '-', NULL)), NULL),
		  		IFNULL(IF(A.ref_b_cv IS NOT NULL, '-', NULL), NULL),
		  		IFNULL(IF(A.ref_b_dr_date IS NOT NULL AND A.ref_b_dr_date != '0000-00-00', DATE_FORMAT(A.ref_b_dr_date, '%c/%e/%Y'), IF(A.ref_b_dr IS NOT NULL, '-', NULL)), NULL))";

	    	if($searches['reference_b'])
	      	{
		    	switch ($searches['reference_b'])
		    	{
		      		case REF_B_APV:

		      			$ref_b_no_select	= "IFNULL($apv_num_url, '-')";
		      			$ref_b_date_select	= "IFNULL(IF(A.ref_b_apv_date IS NOT NULL AND A.ref_b_apv_date != '0000-00-00', DATE_FORMAT(A.ref_b_apv_date, '%c/%e/%Y'), IF(A.ref_b_apv IS NOT NULL, '-', NULL)) SEPARATOR ', <br/>'), '-')";

		      			break;

		      		case REF_B_CV:

		      			$ref_b_no_select	= "IFNULL($cv_num_url, '-')";
		      			$ref_b_date_select	= "IF(A.ref_b_cv IS NOT NULL, '-', NULL)";

		      			break;

		      		case REF_B_DR:

		      			$ref_b_no_select	= "IFNULL($dr_num_url, '-')";
		      			$ref_b_date_select	= "IFNULL(IF(A.ref_b_dr_date IS NOT NULL AND A.ref_b_dr_date != '0000-00-00', DATE_FORMAT(A.ref_b_dr_date, '%c/%e/%Y'), IF(A.ref_b_dr IS NOT NULL, '-', NULL)), '-')";

		      			break;
		      	}
	  		}

			$values	= [
				AG_CONTRACT_GROWERS,
				AG_CONTRACTORS,
				AG_GOODS_BFFI,
				AG_CONTRACT_GROWERS,
				AG_CONTRACTORS,
				AG_GOODS_BFFI,
				AG_CONTRACT_GROWERS,
				AG_CONTRACTORS,
				AG_GOODS_BFFI,
				AG_CONTRACT_GROWERS,
				AG_CONTRACT_GROWERS,
				APV_REF_TYPE_CODE_IO,
				MODULE_PORTAL_TRANSACTIONS,
				AG_GOODS_BFFI,
				APV_REF_TYPE_CODE_PO,
				MODULE_PORTAL_TRANSACTIONS,
				AG_CONTRACTORS,
				AG_CONTRACTORS,
				APV_REF_TYPE_CODE_PO,
				MODULE_PORTAL_TRANSACTIONS,
				APV_REF_TYPE_CODE_SOA,
				MODULE_PORTAL_TRANSACTIONS,
				MODULE_PORTAL_TRANSACTIONS,
				AG_GOODS_BFFI,
				AG_LESSORS,
				STATUS_COMPLETED,
				APV_REF_TYPE_CODE_CONTRACTS,
				CONTRACT_STATUS_NEW
			];

			//Account Group
			if($searches['account_group'])
			{
				$and_where .= " AND A.account_group_code = ?";
				$values[] = $searches["account_group"];
			}

			//Business Center
			if($searches['business_center'])
			{
				$and_where .= " AND A.org_code = ?";
				$values[] = $searches["business_center"];
			}

			if($params === NULL)
			{
				$select_fields	= 'COUNT(DISTINCT A.pria_workflow_id, A.account_group_code) total';
			}
			else
			{
		        $select_fields	= "
		          		SQL_CALC_FOUND_ROWS
		            	A.pria_workflow_id,
		            	A.account_group_code,
		            	A.org_code,
		            	A.ref_a_no,
		            	DATE_FORMAT(A.ref_a_date, '%c/%e/%Y') ref_a_date,
		            	$ref_b_no_select ref_b_no,
		            	$ref_b_date_select ref_b_date,
		            	A.trans_link,
		            	A.link_tab,
		            	B.name org_name,
		            	C.account_group_name,
		            	A.store_name
		        ";
		        
		        $filters		= array(
		            	"A.ref_a_no",
		            	"DATE_FORMAT(A.ref_a_date, '%c/%e/%Y') convert_to ref_a_date",
		            	"A.ref_b_dr",
		            	"DATE_FORMAT(A.ref_b_dr_date, '%c/%e/%Y') convert_to ref_b_dr_date",
		            	"A.ref_b_apv",
		            	"DATE_FORMAT(A.ref_b_apv_date, '%c/%e/%Y') convert_to ref_b_apv_date",
		            	"A.ref_b_cv",
		            	"B.name convert_to org_name",
		            	"C.account_group_name",
		            	"A.store_name"
		        );
		        
		        $orders = array(
		        			"org_name",
		        			"account_group_name",
		        			"store_name",
		            	"ref_a_no",
		            	"A.ref_a_date",
		            	"ref_b_no",
		            	"ref_b_date"
		        );

		        $filter		= $this->filtering($filters, $params, TRUE);
		        $order		= $this->ordering($orders, $params);
		        $limit		= $this->paging($params);

		        $where		= $filter["search_str"];
		        $values		= array_merge($values, $filter["search_params"]);
				// $group_by	= "GROUP BY A.pria_workflow_id, A.account_group_code";
		    }

		    $base_url		= base_url();

			$query = <<<EOS
				SELECT
				$select_fields
				FROM (
					SELECT
						a.pria_workflow_id,
						a.account_group_code,
						a.org_code,
						a.reference_num ref_a_no,
						'N/A' store_name,
						CASE
						WHEN (a.account_group_code IN (?)) THEN DATE_FORMAT(b.created_date, '%Y-%m-%d')
						WHEN (a.account_group_code IN (?)) THEN DATE_FORMAT(f.po_date, '%Y-%m-%d')
						WHEN (a.account_group_code IN (?)) THEN DATE_FORMAT(d.po_date, '%Y-%m-%d')
						ELSE DATE_FORMAT(g.soa_date, '%Y-%m-%d')
						END ref_a_date,
						i.dr_num ref_b_dr,
						i.dr_date ref_b_dr_date,
						k.apv_num ref_b_apv,
						k.apv_date ref_b_apv_date,
						k.cv_num ref_b_cv,
						CASE
						WHEN (a.account_group_code IN (?)) THEN bb.link
						WHEN (a.account_group_code IN (?)) THEN fb.link
						WHEN (a.account_group_code IN (?)) THEN db.link
						ELSE gb.link
						END trans_link,
						CASE
						WHEN (a.account_group_code IN (?)) THEN 'tab_internal_orders'
						WHEN (a.account_group_code IN (?)) THEN 'tab_purchase_orders'
						WHEN (a.account_group_code IN (?)) THEN 'tab_purchase_orders'
						ELSE 'tab_soa'
						END link_tab
				    FROM $this->tbl_pria_workflow a
				    LEFT JOIN $this->tbl_internal_orders b ON a.reference_id = b.io_id AND a.account_group_code = ?
				    AND a.org_code = b.org_code AND a.vendor_code = b.vendor_code
				    LEFT JOIN $this->tbl_pria_tab_modules ba ON ba.ag_code = ? AND ba.transaction_tab = ? AND ba.root_module = ?
				    AND a.core_workflow_id = ba.core_workflow_id
				    LEFT JOIN $this->tbl_core_modules bb ON ba.parent_module_code = bb.module_code
				    LEFT JOIN $this->tbl_purchase_orders d ON a.reference_id = d.po_id
				    LEFT JOIN $this->tbl_pria_references l ON d.po_id = l.po_id
				    AND a.org_code = d.org_code AND a.vendor_code = d.vendor_code AND l.po_id IS NOT NULL
				    LEFT JOIN $this->tbl_pria_tab_modules da ON da.ag_code = ? AND da.transaction_tab = ? AND da.root_module = ?
				    AND a.core_workflow_id = da.core_workflow_id
				    LEFT JOIN $this->tbl_core_modules db ON da.parent_module_code = db.module_code
				    LEFT JOIN $this->tbl_purchase_requisitions e ON l.pr_id = e.pr_id AND a.account_group_code = e.account_group_code AND l.pr_id IS NOT NULL
				    LEFT JOIN $this->tbl_purchase_orders f ON a.reference_id = f.po_id AND a.account_group_code = ?
				    AND a.org_code = f.org_code AND a.vendor_code = f.vendor_code
				    LEFT JOIN $this->tbl_pria_tab_modules fa ON fa.ag_code = ? AND fa.transaction_tab = ? AND fa.root_module = ?
				    AND a.core_workflow_id = fa.core_workflow_id
				    LEFT JOIN $this->tbl_core_modules fb ON fa.parent_module_code = fb.module_code
				    LEFT JOIN $this->tbl_soa g ON a.reference_id = g.soa_id AND a.account_group_code = g.account_group_code
				    AND a.org_code = g.org_code AND a.vendor_code = g.vendor_code
				    LEFT JOIN $this->tbl_pria_tab_modules ga ON ga.ag_code = g.account_group_code AND ga.transaction_tab = ? AND ga.root_module = ?
				    AND a.core_workflow_id = ga.core_workflow_id
				    LEFT JOIN $this->tbl_core_modules gb ON ga.parent_module_code = gb.module_code
				    LEFT JOIN $this->tbl_pria_references h ON d.po_id = h.po_id
				    LEFT JOIN $this->tbl_delivery_goods_receipt i ON h.dr_gr_id = i.dr_gr_id
				    LEFT JOIN $this->tbl_payments j on a.reference_id = j.reference_id AND a.account_group_code = j.account_group_code
				    AND a.org_code = j.org_code AND a.vendor_code = j.vendor_code AND a.core_workflow_id IN (
				    	SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
				    	WHERE ag_code = j.account_group_code AND transaction_tab = j.reference_type_code
				    	AND root_module = ?
				    )
				    LEFT JOIN $this->tbl_payment_apvs k on j.payment_id = k.payment_id
				    WHERE (b.io_id IS NOT NULL OR (e.account_group_code = ? AND d.po_id IS NOT NULL) OR f.po_id IS NOT NULL OR g.soa_id IS NOT NULL)
				    AND a.account_group_code != ? AND a.status_code = ?
				    UNION ALL
				    SELECT 
				    	a.contract_id pria_workflow_id,
				    	a.account_group_code,
							a.org_code,
				    	a.contract_code ref_a_no,
							d.official_store_name store_name,
				    	DATE_FORMAT(a.date_from, '%Y-%m-%d') ref_a_date,
							'-' ref_b_dr,
							'-' ref_b_dr_date,
							c.apv_num ref_b_apv,
							c.apv_date ref_b_apv_date,
							c.cv_num ref_b_cv,
							'transactions/lessors' trans_link,
							'tab_contracts' link_tab
				    FROM $this->tbl_contracts a
						JOIN $this->tbl_sites d on a.site_id = d.site_id
						LEFT JOIN $this->tbl_payments b ON a.contract_id = b.reference_id
						AND a.account_group_code = b.account_group_code AND b.reference_type_code = ?
				    LEFT JOIN $this->tbl_payment_apvs c on b.payment_id = c.payment_id
				    WHERE (a.contract_status_code = ? OR b.payment_id IS NOT NULL)
				) A
				JOIN $this->tbl_organizations B ON A.org_code = B.org_code
				JOIN $this->tbl_param_account_groups C ON A.account_group_code = C.account_group_code
				WHERE 1=1 $and_where
				$where
				$group_by
				$having
				$order
				$limit
EOS;
	    	
	      	if($params === NULL)
	      	{
	        	$total = $this->query($query, $values, TRUE, FALSE);
	        	return $total['total'];
	      	}
	      	else
	      	{
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

  public function _get_display_records()
  {
    try
    {
      $query = "SELECT FOUND_ROWS() cnt";

      $count = $this->query($query, NULL, TRUE, FALSE);

      return $count['cnt'];
    }
    catch (PDOException $e)
    {
      throw $e;
    }
  }
}