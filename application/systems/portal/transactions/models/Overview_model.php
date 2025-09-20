<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overview_model extends Portal_Model
{
    private $tbl_core_users;
    private $tbl_overview_table;
    private $tbl_pria_workflows;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_core_users                       = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_overview_table                   = Portal_Model::PORTAL_TABLE_PRIA_OVERVIEW;
        $this->tbl_pria_workflows                   = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_contracts                   = Portal_Model::PORTAL_TABLE_CONTRACTS;
        $this->tbl_delivery_goods_receipt           = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
        $this->tbl_purchase_requisitions            = Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
        $this->tbl_purchase_request_cost_centers    = Portal_Model::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;
        $this->tbl_sites                            = Portal_Model::PORTAL_TABLE_SITES;
    }

    public function get_overview_list($module_code, $from, $to, $having='', $created_from = NULL, $check_only = FALSE, $count = FALSE)
    {
        try
        {
            $join_tbl   = "";
            $values     = [$module_code];

            $scope_where    = str_replace('org_code', 'A.org_code', $having);

            $order_by   = "";
            $limit      = "";

            if($count)
            {
                $select_fields  = "COUNT(overview_id) AS count";
            }
            else
            {
                $select_fields  = "*";
                $order_by       = "ORDER BY A.created_date DESC, A.reference DESC";
                $limit          = "LIMIT $from, $to";
            }

            $created_from   = (!EMPTY($created_from))? str_replace('%20', ' ', $created_from): date(FORMAT_DB_DATETIME);
            $multiple       = ($check_only OR $count)? FALSE: TRUE;
            
            $values[]       = $created_from;

            $union_statement    =<<<EOS
                    UNION
                    SELECT
                        a.overview_id,
                        a.overview_html,
                        a.reference,
                        a.created_by,
                        a.created_date,
                        a.module_code,
                        b.photo,
                        AGDEC(b.fname) as fname,
                        c.org_code,
                        c.vendor_code
                    FROM $this->tbl_overview_table a
                    JOIN $this->tbl_core_users b ON a.created_by = b.user_id
EOS;

            if($module_code == MODULE_PORTAL_TRANS_LESSORS)
            {
                $join_tbl   = $union_statement;
                $join_tbl   .=<<<EOS
                        JOIN $this->tbl_pria_contracts c ON a.reference = c.contract_id
                        WHERE a.core_workflow_id IS NULL
                        AND a.module_code = ? AND a.overview_html NOT LIKE '%added an APV%' AND a.created_date <= ?
EOS;
                $values[]   = $module_code;
                $values[]   = $created_from;
            }

            if($module_code == MODULE_PORTAL_TRANS_FORWARDERS)
            {
                $join_tbl   = $union_statement;
                $join_tbl   .=<<<EOS
                        JOIN $this->tbl_delivery_goods_receipt c ON a.reference = c.dr_gr_id
                        WHERE a.core_workflow_id IS NULL
                        AND a.module_code = ? AND a.overview_html NOT LIKE '%added an APV%' AND a.created_date <= ?
EOS;
                $values[]   = $module_code;
                $values[]   = $created_from;
            }

            if(in_array($module_code, [MODULE_PORTAL_TRANS_GOODS_GOODS, MODULE_PORTAL_TRANS_GOODS_MARINADES]))
            {
                $join_tbl   = $union_statement;
                $join_tbl   = str_replace('c.org_code', 'f.org_code', $join_tbl);
                $join_tbl   .=<<<EOS
                        JOIN $this->tbl_pria_workflows c ON a.reference = c.reference_id
                        AND a.account_group_code = c.account_group_code AND a.core_workflow_id = c.core_workflow_id AND c.org_code IS NULL
                        JOIN $this->tbl_purchase_requisitions d ON c.reference_id = d.pr_id AND c.account_group_code = d.account_group_code
                        JOIN $this->tbl_purchase_request_cost_centers e ON d.pr_id = e.pr_id
                        JOIN $this->tbl_sites f ON e.cost_center_code = f.cost_center_code AND f.cost_center_code IS NOT NULL
                        WHERE a.module_code = ? AND a.overview_html NOT LIKE '%added an APV%' AND a.created_date <= ?
EOS;
                $values[]   = $module_code;
                $values[]   = $created_from;
            }

            $query =<<<EOS
            SELECT
                $select_fields
            FROM
            (
                SELECT
                    a.overview_id,
                    a.overview_html,
                    a.reference,
                    a.created_by,
                    a.created_date,
                    a.module_code,
                    b.photo,
                    AGDEC(b.fname) as fname,
                    c.org_code,
                    c.vendor_code
                FROM $this->tbl_overview_table a
                JOIN $this->tbl_core_users b ON a.created_by = b.user_id    
                JOIN $this->tbl_pria_workflows c ON a.reference = c.reference_id
                AND a.account_group_code = c.account_group_code AND a.core_workflow_id = c.core_workflow_id AND c.org_code IS NOT NULL
                WHERE a.module_code = ? AND a.overview_html NOT LIKE '%added an APV%' AND a.created_date <= ?
                $join_tbl 
            ) A
            WHERE 1=1
            $scope_where         
            $order_by
            $limit                   
EOS;
            /* print_var_export($query, $module_code); die; */
            return $this->query($query, $values, TRUE, $multiple);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    
    public function get_overviews($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, $this->tbl_overview_table, TRUE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_overview_scope($module_code, $having='', $created_from = NULL)
    {
        try
        {
            $created_from    = (!EMPTY($created_from))? str_replace('%20', ' ', $created_from): date(FORMAT_DB_DATETIME);

            $query =<<<EOS
            SELECT
                    a.overview_html,
                    a.created_by,
                    a.created_date,
                    b.photo,
                    AGDEC(b.fname) as fname,
                    c.org_code,
                    c.vendor_code
            FROM    
                    {$this->tbl_overview_table} a
            JOIN    
                    $this->tbl_core_users b ON a.created_by = b.user_id    
            JOIN
                    $this->tbl_pria_workflows c ON a.reference = c.reference_id AND a.account_group_code = c.account_group_code
            WHERE
                    a.module_code = ? AND a.overview_html NOT LIKE '%added an APV%' 
                    AND a.created_date <= ?
            $having          
            ORDER BY
                    a.created_date DESC     
EOS;
            
            return $this->query($query, [$module_code, $created_from]);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


}