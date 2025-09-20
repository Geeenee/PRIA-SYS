<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Soa_model extends Portal_Model
{
    public $tbl_pria_workflows;
    public $tbl_soa;
    public $tbl_soa_documents;
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

        $this->tbl_pria_workflows	      = parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_tasks	          = parent::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_pria_stages	          = parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_soa				      = parent::PORTAL_TABLE_SOA;
        $this->tbl_soa_documents          = parent::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_vendors			      = parent::PORTAL_TABLE_VENDORS;
        $this->tbl_transmittals           = parent::PORTAL_TABLE_TRANSMITTALS;
        $this->tbl_soa_transmittals       = parent::PORTAL_TABLE_SOA_TRANSMITTAL;
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
    public function get_soas($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_soa, TRUE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Christian Aquino
     * @Date: 2019-06-26 13:52:05 
     * @Desc:  
     * @ReferencedBy: 
     */    
    public function get_soa($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_soa, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_soa_transmittal_by_soa_id($soa_id, $fields=array('*'), $order=array(), $group=array())
    {
        try
        {   
            $fields  = implode(',', $fields);
            $query  = <<<EOS
            
            SELECT             
                $fields
            FROM  
                    $this->tbl_pria_references a
            JOIN
                    $this->tbl_transmittals b ON a.transmittal_id = b.transmittal_id
            WHERE
                    a.soa_id = ?
EOS;

            return $this->query($query, [$soa_id], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_soa_drs_by_soa_id($soa_id, $fields=array('*'))
    {
        try
        {   
            $fields  = implode(',', $fields);
            $query   = <<<EOS
            SELECT 
                $fields            
            FROM  
                $this->tbl_pria_references a
            JOIN
                $this->tbl_delivery_goods_receipt b ON a.dr_gr_id = b.dr_gr_id
            WHERE
                a.soa_id = ?
EOS;
            return $this->query($query, [$soa_id], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    public function get_transmittals($where=array(), $fields=array('*'), $order=array(), $group=array())
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_transmittals, FALSE, $where, $order, $group);
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

    public function get_soa_document($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_documents, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_soa_list($where, $list_flag=NULL, $having='', $tab_module='')
    {
        try
        {
            $values_temp            = array();
            $values                 = array();
            $values_filter          = array();

            $w_marks = $q_marks = $limit = $filter = "";
           
            $join   = '';
            $fields = [
                    "A.soa_id AS reference_id",
                    "A.soa_num AS display_num",
                    "C.pria_workflow_id",
                    "CONCAT(D.name,': ', B.vendor_name, ' [', A.vendor_code, ']') as display_name",
                    "C.org_code",
                    "C.vendor_code",
                    "A.created_by",
                    "C.status_code"
            ];

            $date_format = FORMAT_DATE_DISPLAY_DB;
            $doc_type    = DOC_TYPE_SOA;

            $group_by    = "";

            $org_field  = "A.org_code";

            if($list_flag === NULL)
            {
                $group_by   = "GROUP BY A.soa_id";
            }
            
            $having = str_replace('org_code', 'A.org_code', $having);
            
            $having = str_replace('vendor_code', 'A.vendor_code', $having);

            switch($tab_module)
            {
                case MODULE_PORTAL_TRANS_GOODS_M_SOA:
                case MODULE_PORTAL_TRANS_GOODS_G_SOA:
                    $fields[] = <<<EOS
                        CONCAT('PO: ', GROUP_CONCAT(DISTINCT po.po_num SEPARATOR ', '), ', Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) as display_extra
EOS;
                    $join     = <<<EOS
                        JOIN 
                            $this->tbl_pria_references prf ON A.soa_id = prf.soa_id
                        JOIN 
                            $this->tbl_purchase_orders po ON prf.po_id = po.po_id
                        JOIN
                            $this->tbl_pria_references prf2 ON po.po_id = prf2.po_id AND prf2.pr_id IS NOT NULL
                        JOIN
                            $this->tbl_pr_cost_centers prcc ON prf2.pr_id = prcc.pr_id
                        JOIN
                            $this->tbl_sites s ON prcc.cost_center_code = s.cost_center_code
EOS;

                    $org_field  = "s.org_code";

                    if(!EMPTY($having))
                    {
                        $having = str_replace('A.org_code', 'D.org_code', $having);
                    }
                break;
                
                case MODULE_PORTAL_TRANS_FORWARDER_SOA:
                    $fields[] = <<<EOS
                        CONCAT('Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) as display_extra
EOS;
                break;

                case MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_SOA:
                    $fields[] = <<<EOS
                        CONCAT('Period Covered : ', DATE_FORMAT(A.date_from, '$date_format'), ' - ', DATE_FORMAT(A.date_to, '$date_format'), ', Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}')) AS display_extra
EOS;
                break;
                
                case MODULE_PORTAL_TRANS_MANPOWER_SOA:
                case MODULE_PORTAL_TRANS_SOA_BASED_SOA:
                case MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA:
                case MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_SOA:
                default:
                    $fields[] = <<<EOS
                        CONCAT('Period Covered : ', DATE_FORMAT(A.date_from, '$date_format'), ' - ', DATE_FORMAT(A.date_to, '$date_format'), ', Date Uploaded: ', IF(pt.actual_end_date IS NOT NULL, DATE_FORMAT(pt.actual_end_date, '$date_format'), 'N/a')) AS display_extra
EOS;
                    $join     = <<<EOS
                         LEFT JOIN
                            $this->tbl_pria_stages ps ON ps.pria_workflow_id = C.pria_workflow_id
                         LEFT JOIN 
                            $this->tbl_pria_tasks pt ON A.soa_id = pt.reference AND ps.pria_stage_id = pt.pria_stage_id
EOS;
                case MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA:
                    $ibc_code = AG_INBOUND_CENTRAL;
                    $fields[] = <<<EOS
                         CONCAT('Period Covered : ', DATE_FORMAT(A.date_from, '$date_format'), ' - ', DATE_FORMAT(A.date_to, '$date_format'), 
                            IF(A.account_group_code = '$ibc_code',    
                               CONCAT(', Date Uploaded: ', IF(pt.actual_end_date IS NOT NULL, DATE_FORMAT(pt.actual_end_date, '$date_format'), 'N/a')),
                               CONCAT(', Date Submitted : ', DATE_FORMAT(A.submission_date, '{$date_format}'))
                            )
                         ) AS display_extra
EOS;
                $join     = <<<EOS
                     LEFT JOIN
                        $this->tbl_pria_stages ps ON ps.pria_workflow_id = C.pria_workflow_id
                     LEFT JOIN 
                        $this->tbl_pria_tasks pt ON A.soa_id = pt.reference AND ps.pria_stage_id = pt.pria_stage_id
EOS;
                break;
            }


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
                        $this->tbl_soa A
                    LEFT JOIN 
                        $this->tbl_vendors B ON A.vendor_code = B.vendor_code
                    LEFT JOIN 
                        $this->tbl_pria_workflows C ON A.soa_id = C.reference_id
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
                    ORDER BY A.soa_id DESC
                    $limit
EOS;
            
            $values = array_merge($values, $values_temp, $values_filter);
            //print_var_export($query, $values); die;

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

    // public function get_acc_group($where=array(), $fields=array('*'), $order=array())
    // {
    //     try
    //     {
    //         return $this->select_data($fields, $this->tbl_param_account_groups, FALSE, $where, $order);
    //     }
    //     catch(PDOException $e)
    //     {
    //         throw $e;
    //     }
    // }

    // public function get_tab_module($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
    // {
    //     try
    //     {
    //         return $this->select_data($fields, $this->tbl_pria_tab_modules, $multiple, $where, $order);
    //     }
    //     catch(PDOException $e)
    //     {
    //         throw $e;
    //     }
    // }

    public function get_soa_doc_recipient()
    {

        try
        {
            
            $fields     = array(
                'A.fname', 'A.lname', 'A.email', 'A.mname', 'A.ext_name', 
                'A.username', 'A.nickname', 'A.job_title', 'A.contact_no', 'A.mobile_no'
            );
            
            $decrypt    = aes_crypt($fields, FALSE);
            
            $query = 
            '
                SELECT
                    A.user_id,
                    A.location_code,
                    A.org_code,
                    A.password,
                    A.salt,
                    A.reset_salt,
                    A.gender,
                    A.photo,
                    A.logged_in_flag,
                    A.mail_flag,
                    A.contact_flag,
                    A.status,
                    A.reason,
                    A.attempts,
                    A.initial_flag,
                    A.pw_email_flag,
                    A.last_logged_in_date,
                    A.built_in_flag,
                    B.role_code,
                    ' . $decrypt . '
                                        
                FROM ' . $this->tbl_core_users . ' A
                
                JOIN ' . $this->tbl_core_user_roles . ' B ON A.user_id = B.user_id

            ';
            
            return $this->query($query, array(), TRUE, TRUE);
            
            
        }   
        catch (PDOException $e)
        {
            throw $e;
        }
    }

    public function get_finance_recipient($org_code)
    {
        try{

            $fields     = array(
                'A.fname', 'A.lname', 'A.email', 'A.mname', 'A.ext_name', 
                'A.username', 'A.nickname', 'A.job_title', 'A.contact_no', 'A.mobile_no'
            );
            
            $decrypt    = aes_crypt($fields, FALSE);
            
            $query   = <<<EOS
                SELECT
                    A.user_id,
                    A.location_code,
                    A.org_code,
                    A.password,
                    A.salt,
                    A.reset_salt,
                    A.gender,
                    A.photo,
                    A.logged_in_flag,
                    A.mail_flag,
                    A.contact_flag,
                    A.status,
                    A.reason,
                    A.attempts,
                    A.initial_flag,
                    A.pw_email_flag,
                    A.last_logged_in_date,
                    A.built_in_flag,
                    C.finance_flag,
                    B.role_code,
                    GROUP_CONCAT(C.role_name SEPARATOR ', ') as role_names,
                    $decrypt,
                    CONCAT(
                        AGDEC(A.fname), ' ',
                        IFNULL(AGDEC(A.mname),''), ' ',
                        IFNULL(AGDEC(A.lname),''),' ',
                        IFNULL(AGDEC(A.ext_name),'')
                    ) as fullname
                                        
                FROM $this->tbl_core_users A
                
                JOIN $this->tbl_core_user_roles B ON A.user_id = B.user_id

                JOIN $this->tbl_core_roles C ON B.role_code = C.role_code

                JOIN $this->tbl_user_orgs  D ON A.user_id = D.user_id
                
                WHERE C.finance_flag = ?

                AND   D.org_code     = ?

                GROUP BY A.user_id

EOS;
            //kevin
            $fin_users = $this->query($query, array(FINANCE_YES_FLAG, $org_code), TRUE, TRUE);

            return $fin_users; 
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** INSERT FUNCTIONS */
    /** 
     * @Author: Christian Aquino 
     * @Date: 2019-06-29 14:13:05 
     * @Desc:  
     * @ReferencedBy:  Upload_soa.php
     */    
    public function insert_soa(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_soa, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_soa_document(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_documents, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_transmittals(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_transmittals, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_soa_transmittals(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_references, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** UPDATE FUNCTIONS */
    /** 
     * @Author: Christian Aquino 
     * @Date: 2019-05-29 14:13:05 
     * @Desc:  
     * @ReferencedBy:  Upload_soa.php
     */ 
    public function update_soa(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_soa, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_soa_transmittals(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_transmittals, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_soa_document(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_documents, $fields, $where);
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

    public function get_data($fields_arr, $table, $multiple, $where_arr = array(), $order_arr = array(), $group_arr = array(), $limit = '')
    {
        try
        {
            return $this->select_data($fields_arr, $table, $multiple, $where_arr, $order_arr, $group_arr, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_approved_released_po($tab_module, $ag_codes = array(), $vendor_codes = array(), $org_codes = array())
    {
        try
        {
            $ag_where = $ag_q_mark = $vendor_where = $v_q_mark = $org_where = $org_q_mark = "";
            $values = array(
                $tab_module,
                CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES,
                TASK_STATUS_DONE, TASK_STATUS_APPROVED
            );

            /*
                - Uncomment BFFI Values and add this to CASE Condition if BFFI will be included
                    WHEN B.account_group_code = ? THEN ?
            */

            if(is_array($ag_codes) AND count($ag_codes) > 0)
            {
                foreach($ag_codes AS $key => $ag_code)
                {
                    $ag_q_mark  .= (!EMPTY($ag_q_mark))? ", ?": "?";
                    $values[]   = $ag_code;
                }

                $ag_where       = "AND B.account_group_code IN ($ag_q_mark)";
            }

            if(is_array($vendor_codes) AND count($vendor_codes) > 0)
            {
                foreach($vendor_codes AS $key => $vendor_code)
                {
                    $v_q_mark  .= (!EMPTY($v_q_mark))? ", ?": "?";
                    $values[]   = $vendor_code;
                }

                $vendor_where   = "AND A.vendor_code IN ($v_q_mark)";
            }

            if(is_array($org_codes) AND count($org_codes) > 0)
            {
                foreach($org_codes AS $key => $org_code)
                {
                    $org_q_mark .= (!EMPTY($org_q_mark))? ", ?": "?";
                    $values[]   = $org_code;
                }

                $org_where      = "AND IF(H.org_code IS NOT NULL, H.org_code, A.org_code) IN ($org_q_mark)";
            }

            $query  = <<<EOS
                SELECT DISTINCT A.po_id, A.po_num
                FROM $this->tbl_purchase_orders A
                JOIN $this->tbl_pria_references F ON A.po_id = F.po_id
                JOIN $this->tbl_purchase_requisitions B ON F.pr_id = B.pr_id
                JOIN $this->tbl_pria_workflows C ON A.po_id = C.reference_id
                AND B.account_group_code = C.account_group_code AND C.core_workflow_id IN (
                    SELECT core_workflow_id FROM $this->tbl_pria_tab_modules
                    WHERE ag_code = B.account_group_code AND tab_module_code = ?
                )
                JOIN $this->tbl_pria_stages D ON C.pria_workflow_id = D.pria_workflow_id
                JOIN $this->tbl_pria_tasks E ON D.pria_stage_id = E.pria_stage_id
                LEFT JOIN $this->tbl_pr_cost_centers I ON B.pr_id = I.pr_id
                LEFT JOIN $this->tbl_sites G ON I.cost_center_code = G.cost_center_code
                LEFT JOIN $this->tbl_organizations H ON G.org_code = H.org_code
                AND E.core_workflow_task_id = ?
                AND E.task_status_id IN (?, ?)
                WHERE 1=1 $ag_where $vendor_where $org_where
EOS;
            return $this->query($query, $values);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_soa_drs(array $fields, array $where)
    {
        try
        {
            return $this->update_data($this->tbl_delivery_goods_receipt, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function delete_soa_drs(array $where)
    {
        try
        {
            return $this->delete_data($this->tbl_pria_references, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    public function get_cdi_finance_recipient($org_code)
    {
        try{

            $fields     = array(
                'A.fname', 'A.lname', 'A.email', 'A.mname', 'A.ext_name', 
                'A.username', 'A.nickname', 'A.job_title', 'A.contact_no', 'A.mobile_no'
            );
            
            $decrypt    = aes_crypt($fields, FALSE);
            
            $query   = <<<EOS
                SELECT
                    A.user_id,
                    A.location_code,
                    A.org_code,
                    A.password,
                    A.salt,
                    A.reset_salt,
                    A.gender,
                    A.photo,
                    A.logged_in_flag,
                    A.mail_flag,
                    A.contact_flag,
                    A.status,
                    A.reason,
                    A.attempts,
                    A.initial_flag,
                    A.pw_email_flag,
                    A.last_logged_in_date,
                    A.built_in_flag,
                    C.finance_flag,
                    B.role_code,
                    GROUP_CONCAT(C.role_name SEPARATOR ', ') as role_names,
                    $decrypt,
                    CONCAT(
                        AGDEC(A.fname), ' ',
                        IFNULL(AGDEC(A.mname),''), ' ',
                        IFNULL(AGDEC(A.lname),''),' ',
                        IFNULL(AGDEC(A.ext_name),'')
                    ) as fullname
                                        
                FROM $this->tbl_core_users A
                
                JOIN $this->tbl_core_user_roles B ON A.user_id = B.user_id

                JOIN $this->tbl_core_roles C ON B.role_code = C.role_code

                JOIN $this->tbl_user_orgs  D ON A.user_id = D.user_id
                
                WHERE D.org_code     = ?

                AND   B.role_code IN ('CDI_FIN_HEAD', 'CDI_FIN_PERS')

                GROUP BY A.user_id

EOS;
            //kevin
            // $fin_users = $this->query($query, array(FINANCE_YES_FLAG, $org_code), TRUE, TRUE);
            $fin_users = $this->query($query, array($org_code), TRUE, TRUE);

            return $fin_users; 
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}