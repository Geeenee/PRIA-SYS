<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Internal_order_model extends Portal_Model
{
    public $tbl_internal_orders;
    public $tbl_organizations;
    public $tbl_pria_workflows;
    public $tbl_sites;
    public $tbl_vendors;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_internal_orders          = parent::PORTAL_TABLE_INTERNAL_ORDERS;
        $this->tbl_organizations            = parent::PORTAL_TABLE_ORGANIZATIONS;
        $this->tbl_pria_workflows           = parent::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_sites                    = parent::PORTAL_TABLE_SITES;
        $this->tbl_vendors                  = parent::PORTAL_TABLE_VENDORS;
    }

    public function get_internal_orders_list($where, $list_flag=NULL, $having='')
    {
        try
        {
            $values_temp            = array();
            $values                 = array(ORG_TYPE_BUSINESS_CENTER);
            $values_filter          = array();

            $w_marks = $q_marks = $limit = $filter = "";

            /*if($list_flag === NULL)
            {*/
                $fields             = array(
                        "A.io_id AS reference_id",
                        "A.io_num AS display_num",
                        "B.vendor_name AS display_name",
                        "CONCAT('Business Center: ', D.name,', Date: ', DATE_FORMAT(IFNULL(A.modified_date, A.created_date), '".FORMAT_DATE_DISPLAY_DB."')) AS display_extra",
                        "E.pria_workflow_id",
                        "E.org_code",
                        "E.vendor_code"
                );

                $select_fields      = implode(', ', $fields);
            /*}
            else
            {
                $select_fields      = "COUNT(A.pr_id) AS cnt";
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
                    FROM $this->tbl_internal_orders A
                    LEFT JOIN $this->tbl_vendors B ON A.vendor_code = B.vendor_code
                    LEFT JOIN $this->tbl_sites C ON A.site_code = C.site_code
                    LEFT JOIN $this->tbl_organizations D ON C.org_code = D.org_code AND D.org_type_code = ?
                    LEFT JOIN $this->tbl_pria_workflows E ON A.io_id = E.reference_id
                    AND E.core_workflow_id IN ($w_marks)
                    AND E.account_group_code IN ($q_marks)
                    $filter
                    $having
                    ORDER BY A.io_id DESC
                    $limit
EOS;

            //      print_var_export($query); die;
            $values                 = array_merge($values, $values_temp, $values_filter);
       
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

    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-05-23 15:52:05 
     * @Desc:  
     * @ReferencedBy:  MedVac.php
     */    
    public function get_internal_order($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }



    
    /** INSERT FUNCTIONS */
    /** 
     * @Author: Christian Aquino 
     * @Date: 2019-05-29 14:13:05 
     * @Desc:  
     * @ReferencedBy:  Clean_up.php
     */    
    public function insert_internal_order(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_internal_orders, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_internal_order_document(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_internal_order_documents, $fields, $return);
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
     * @ReferencedBy:  Clean_up.php, Encode_audit_score.php
     */ 
    public function update_internal_order(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_internal_orders, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_internal_order_document(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_internal_order_documents, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}