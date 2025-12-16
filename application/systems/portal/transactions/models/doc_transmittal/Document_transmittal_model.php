<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document_transmittal_model extends Portal_model
{


    public $tbl_pria_workflows;
    public $tbl_transmittal_documents;
    // public $tbl_soa_documents;
    public $tbl_vendors;
    public $tbl_transmittals;
    public $tbl_soa_transmittals;
    public $tbl_param_account_groups;
    public $tbl_pria_tab_modules;
    public $tbl_documents;
    public $tbl_core_users;
    public $tbl_core_user_roles;
    public $tbl_pria_references;
    public $tbl_business_center;
    public $tbl_core_roles;
    public $tbl_organizations;
    public $tbl_purchase_orders;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_transmittals              = parent::PORTAL_TABLE_TRANSMITTALS;
        $this->tbl_document_transmittals      = parent::PORTAL_TABLE_DOCUMENT_TRANSMITTALS;

        $this->tbl_pria_workflows	      = parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_tasks	          = parent::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_pria_stages	          = parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        // $this->tbl_soa				      = parent::PORTAL_TABLE_SOA;
        // $this->tbl_soa_documents          = parent::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_vendors			      = parent::PORTAL_TABLE_VENDORS;
        // $this->tbl_transmittals           = parent::PORTAL_TABLE_TRANSMITTALS;
        // $this->tbl_soa_transmittals       = parent::PORTAL_TABLE_SOA_TRANSMITTAL;
        $this->tbl_param_account_groups   = parent::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
        $this->tbl_pria_tab_modules       = parent::PORTAL_TABLE_PRIA_TAB_MODULE;
        $this->tbl_documents              = parent::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_core_users             = parent::CORE_TABLE_USERS;
        $this->tbl_core_user_roles        = parent::CORE_USER_ROLES;
        $this->tbl_pria_references        = parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
        $this->tbl_business_center        = parent::PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS;
        $this->tbl_core_roles             = parent::CORE_ROLES;
        $this->tbl_organizations          = parent::PORTAL_TABLE_ORGANIZATIONS;
        $this->tbl_purchase_orders        = parent::PORTAL_TABLE_PURCHASE_ORDERS;
        $this->tbl_delivery_goods_receipt = parent::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
        $this->tbl_user_orgs              = parent::PORTAL_TABLE_USER_ORGS;
        $this->tbl_purchase_requisitions  = parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
        $this->tbl_pr_cost_centers        = parent::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;
        $this->tbl_sites                  = parent::PORTAL_TABLE_SITES;
    }

    /** SELECT FUNCTIONS */
    public function get_document_transmittals($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_document_transmittals, TRUE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_document_transmittal($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_document_transmittals, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function SF($where=array(), $fields=array('*'), $order=array(), $group=array())
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_document_transmittals, FALSE, $where, $order, $group);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

        public function get_pria_reference($where=array(), $fields=array('*'), $order=array(), $group=array())
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_pria_references, FALSE, $where, $order, $group);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_transmittal_list($where, $list_flag=NULL, $having='', $tab_module='')
    {
        try
        {
            $values_temp            = array();
            $values                 = array();
            $values_filter          = array();

            $w_marks = $q_marks = $limit = $filter = "";
           
            $join   = '';
            $fields = [
                    "A.document_transmittal_id AS reference_id",
                    "A.document_tracer_batch_number AS display_num",
                    "C.pria_workflow_id",
                    "CONCAT(D.name, IF(B.vendor_name IS NOT NULL AND TRIM(B.vendor_name) <> '', CONCAT(': ', B.vendor_name, ' [', A.vendor_code, ']'), '')) as display_name",
                    "C.org_code",
                    "C.vendor_code",
                    "A.created_by",
                    "C.status_code",
                    "'' as display_extra",
            ];

            $date_format = FORMAT_DATE_DISPLAY_DB;
            $doc_type    = DOC_TYPE_DOCUMENT_TRANSMITTAL;

            $group_by    = "";

            $org_field  = "A.org_code";

            if($list_flag === NULL)
            {
                $group_by   = "GROUP BY A.document_transmittal_id";
            }
            
            $having = str_replace('org_code', 'A.org_code', $having);
            
            $having = str_replace('vendor_code', 'A.vendor_code', $having);

//             $fields[] = <<<EOS
//             CONCAT('Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) as display_extra
// EOS;
            // die($tab_module . ' <<== tab module');

//             switch($tab_module)
//             {
//                 case MODULE_PORTAL_TRANS_GOODS_M_SOA:
//                 case MODULE_PORTAL_TRANS_GOODS_G_SOA:
//                     $fields[] = <<<EOS
//                         CONCAT('PO: ', GROUP_CONCAT(DISTINCT po.po_num SEPARATOR ', '), ', Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) as display_extra
// EOS;
//                     $join     = <<<EOS
//                         JOIN 
//                             $this->tbl_pria_references prf ON A.soa_id = prf.soa_id
//                         JOIN 
//                             $this->tbl_purchase_orders po ON prf.po_id = po.po_id
//                         JOIN
//                             $this->tbl_pria_references prf2 ON po.po_id = prf2.po_id AND prf2.pr_id IS NOT NULL
//                         JOIN
//                             $this->tbl_pr_cost_centers prcc ON prf2.pr_id = prcc.pr_id
//                         JOIN
//                             $this->tbl_sites s ON prcc.cost_center_code = s.cost_center_code
// EOS;

//                     $org_field  = "s.org_code";

//                     if(!EMPTY($having))
//                     {
//                         $having = str_replace('A.org_code', 'D.org_code', $having);
//                     }
//                 break;
                
//                 case MODULE_PORTAL_TRANS_FORWARDER_SOA:
//                     $fields[] = <<<EOS
//                         CONCAT('Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) as display_extra
// EOS;
//                 break;

//                 case MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_SOA:
//                     $fields[] = <<<EOS
//                         CONCAT('Period Covered : ', DATE_FORMAT(A.date_from, '$date_format'), ' - ', DATE_FORMAT(A.date_to, '$date_format'), ', Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) AS display_extra
// EOS;
//                 break;
                
//                 case MODULE_PORTAL_TRANS_MANPOWER_SOA:
//                 case MODULE_PORTAL_TRANS_SOA_BASED_SOA:
//                 case MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA:
//                 case MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_SOA:
//                 default:
//                     $fields[] = <<<EOS
//                         CONCAT('Period Covered : ', DATE_FORMAT(A.date_from, '$date_format'), ' - ', DATE_FORMAT(A.date_to, '$date_format'), ', Date Uploaded: ', IF(pt.actual_end_date IS NOT NULL, DATE_FORMAT(pt.actual_end_date, '$date_format'), 'N/a')) AS display_extra
// EOS;
//                     $join     = <<<EOS
//                          LEFT JOIN
//                             $this->tbl_pria_stages ps ON ps.pria_workflow_id = C.pria_workflow_id
//                          LEFT JOIN 
//                             $this->tbl_pria_tasks pt ON A.soa_id = pt.reference AND ps.pria_stage_id = pt.pria_stage_id
// EOS;
//                 case MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA:
//                     $ibc_code = AG_INBOUND_CENTRAL;
//                     $fields[] = <<<EOS
//                          CONCAT('Period Covered : ', DATE_FORMAT(A.date_from, '$date_format'), ' - ', DATE_FORMAT(A.date_to, '$date_format'), 
//                             IF(A.account_group_code = '$ibc_code',    
//                                CONCAT(', Date Uploaded: ', IF(pt.actual_end_date IS NOT NULL, DATE_FORMAT(pt.actual_end_date, '$date_format'), 'N/a')),
//                                CONCAT(', Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}'))
//                             )
//                          ) AS display_extra
// EOS;
//                 $join     = <<<EOS
//                      LEFT JOIN
//                         $this->tbl_pria_stages ps ON ps.pria_workflow_id = C.pria_workflow_id
//                      LEFT JOIN 
//                         $this->tbl_pria_tasks pt ON A.soa_id = pt.reference AND ps.pria_stage_id = pt.pria_stage_id
// EOS;
//                 break;
//             }


            $select_fields = implode(', ', $fields);
       
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
                    $q_marks        .= ($key==0)? "?": ", ?";
                    $values_temp[]  = $ag_code;
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
            
            $query                  =<<<EOS
                    SELECT $select_fields
                    FROM 
                        document_transmittals A
                    LEFT JOIN 
                        $this->tbl_vendors B ON A.vendor_code = B.vendor_code
                    LEFT JOIN 
                        $this->tbl_pria_workflows C ON A.document_transmittal_id = C.reference_id
                    AND 
                        A.account_group_code = C.account_group_code
                    AND 
                        C.core_workflow_id IN ($w_marks)
                    $join
                    JOIN 
                        $this->tbl_organizations D ON $org_field = D.org_code    
                    WHERE 
                        A.account_group_code IN ($q_marks)
                    $having
                    $group_by
                    $filter
                    ORDER BY A.document_transmittal_id DESC
                    $limit
EOS;
            $values = array_merge($values, $values_temp, $values_filter);
            // print_var_export($query, $values); die;

            if($list_flag === NULL)
            {
                return $this->query($query, $values);
            }
            else
            {
                /*$next_records     = $this->query($query, $values, TRUE, FALSE);
                return $next_records['cnt'];*/
                return count($this->query($query, $values));
            }
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_specific_vendor($where=array(), $fields=array('*'), $order=array())
    {
        try
        {   

            return $this->select_data($fields, $this->tbl_vendors, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_specific_org($where=array(), $fields=array('*'), $order=array())
    {
        try
        {   

            return $this->select_data($fields, $this->tbl_organizations, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_document_transmittal(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_document_transmittals, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_document_transmittal(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_document_transmittals, $fields, $where);
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


}