<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Po_model extends Portal_Model
{
    public $tbl_pria_workflows;
    public $tbl_po;
    public $tbl_pria_tab_modules;
    public $tbl_core_users;
    public $tbl_core_user_roles;
    public $tbl_boqs;
    public $tbl_boq_pr;
    public $tbl_pr;
    public $tbl_sites;

    public $tbl_pria_references;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_pria_workflows         = parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_po                     = parent::PORTAL_TABLE_PURCHASE_ORDERS;
        $this->tbl_pria_tab_modules       = parent::PORTAL_TABLE_PRIA_TAB_MODULE;
        $this->tbl_core_users             = parent::CORE_TABLE_USERS;
        $this->tbl_core_user_roles        = parent::CORE_USER_ROLES;
        $this->tbl_boqs                   = parent::PORTAL_TABLE_PRIA_BOQ;
        $this->tbl_boq_pr                 = parent::PORTAL_TABLE_PRIA_BOQ_PR;
        $this->tbl_pr                     = parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;
        $this->tbl_pr_cost_centers        = parent::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;
        $this->tbl_sites                  = parent::PORTAL_TABLE_SITES;

        $this->tbl_pria_references        = parent::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;

        $this->tbl_param_po_types         = parent::PORTAL_TABLE_PURCHASE_ORDER_TYPES;

        $this->tbl_organizations          = parent::PORTAL_TABLE_ORGANIZATIONS;
    }

    /** SELECT FUNCTIONS */
    public function get_pos($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_po, TRUE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_all_boqs($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_boqs, TRUE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_po($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_po, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_po_details($po_id){
        try{

            $boq_url   = base_url().PORTAL_TRANSACTIONS.'/'.PORTAL_CONTRACTOR.'?keyword=%s#tab_boq';

            $query =<<<EOS
            SELECT
                po.po_id,
                po.po_num,
                po.po_date,
                po.po_amount,
                po.remaining_amount,
                po.po_status_code,
                po.po_released_date,
                po.po_approved_by,
                po.vendor_code,
                po.org_code,
                po.mobilization_date,
                po.created_by,
                po.created_date,
                po.modified_by,
                po.modified_date,
                po.po_type_code,
                pr.pr_id,
                GROUP_CONCAT(DISTINCT pr.pr_num SEPARATOR ', ') as pr_num,
                CONCAT('<a href=', REPLACE('$boq_url', '%s', b.boq_code), '>', b.boq_code, '</a>') boq_code,
                IFNULL(s.official_store_name, s.suggested_store_name) as site_name,
                IF(po.additional_flag = 1, 'Additional Works', 'New Project') as po_type_name,
                po.receiving_report_num
            FROM $this->tbl_po po
            JOIN $this->tbl_pria_references pr2 ON po.po_id = pr2.po_id
            JOIN  $this->tbl_pr  pr ON pr2.pr_id  = pr.pr_id
            LEFT JOIN $this->tbl_boq_pr bp ON pr.pr_id  = bp.pr_id
            LEFT JOIN $this->tbl_boqs b ON b.boq_id  = bp.boq_id
            LEFT JOIN $this->tbl_sites s ON b.site_id = s.site_id
            WHERE po.po_id = ?
            GROUP BY po.po_id
EOS;
            return $this->query($query, [$po_id], TRUE, FALSE);
        }catch(PDOException $e){
            throw $e;
        }
    }

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


    /** INSERT FUNCTIONS */
    /**
     * @Author: Christian Aquino
     * @Date: 2019-06-29 14:13:05
     * @Desc:
     * @ReferencedBy:
     */
    public function insert_po(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_po, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_po_pr(array $fields)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_references, $fields);
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
     * @ReferencedBy:
     */
    public function update_po(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_po, $fields, $where);
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

    public function get_pr_orgs($po_id)
    {
        try
        {
            $values = [$po_id];

            $query  =<<<EOS
                SELECT
                    F.org_code,
                    F.name,
                    F.org_code value,
                    F.name text
                FROM $this->tbl_po A
                JOIN $this->tbl_pria_references B ON A.po_id = B.po_id AND B.pr_id IS NOT NULL
                JOIN $this->tbl_pr C ON B.pr_id = C.pr_id
                JOIN $this->tbl_pr_cost_centers D ON C.pr_id = D.pr_id
                JOIN $this->tbl_sites E ON D.cost_center_code = E.cost_center_code AND E.cost_center_code IS NOT NULL
                JOIN $this->tbl_organizations F ON E.org_code = F.org_code
                WHERE A.po_id = ?
EOS;
            return $this->query($query, $values);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}