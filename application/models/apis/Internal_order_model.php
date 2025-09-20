<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Internal_order_model extends Portal_Model
{
    private $tbl_internal_orders;
    private $tbl_internal_order_documents;
    private $tbl_vendors;
    private $tbl_business_center;
    private $tbl_organization_types;
    private $tbl_sites;

    private $tbl_pria_delivery;
    private $tbl_pria_delivery_reference;
    private $tbl_pria_workflow;

	public function __construct()
	{
		parent::__construct();

        $this->tbl_internal_orders              = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
        $this->tbl_internal_order_documents     = Portal_Model::PORTAL_TABLE_INTERNAL_ORDER_DOCUMENTS;
        $this->tbl_vendors                      = Portal_Model::PORTAL_TABLE_VENDORS;
        $this->tbl_business_center              = Portal_Model::PORTAL_TABLE_ORGANIZATIONS;
        $this->tbl_organization_types           = Portal_Model::PORTAL_TABLE_ORGANIZATION_TYPES;
        $this->tbl_sites                        = Portal_Model::PORTAL_TABLE_SITES;

        $this->tbl_pria_delivery                = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
        $this->tbl_pria_delivery_reference      = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
        $this->tbl_pria_workflow                = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
	}


    /** SELECT FUNCTIONS */
    public function get_internal_orders($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS, TRUE, $where, $order, $group, $limit);
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

    public function get_internal_order_document($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, Portal_Model::PORTAL_TABLE_INTERNAL_ORDER_DOCUMENTS, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    /** 
     * @Author: Kevin Villarojo 
     * @Date:   2019-05-16 13:16:51 
     * @Desc:   Made to fetch list of io in internal order tabs
     * @ReferencedBy:  Internal_orders.php, 
     */    
    public function get_io_list($where=array())
    {
        try
        {
            $limit = '';

            if( ISSET($where['limit']) )
            {
                $limit = ' LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

            $val = [
                ORG_TYPE_BUSINESS_CENTER,
                $where['core_workflow_id'],
                $where['account_group_code'],
            ];
            
            $query    =<<<EOS
            SELECT 
                    a.io_id as reference_id, 
                    a.io_num as display_num, 
                    b.vendor_name as display_name, 
                    CONCAT('Business Center : ', d.name) as display_extra,
                    e.pria_workflow_id
            FROM  
                    $this->tbl_internal_orders a 
            LEFT JOIN 
                    $this->tbl_vendors b ON a.vendor_code = b.vendor_code
            LEFT JOIN 
                    $this->tbl_sites c ON a.site_code = c.site_code
            JOIN 
                    $this->tbl_business_center d ON c.org_code = d.org_code AND d.org_type_code = ? 
            JOIN 
                    $this->tbl_pria_workflow e ON a.io_id = e.reference_id
                    
                    AND e.core_workflow_id   = ?

                    AND e.account_group_code = ?

            ORDER BY a.io_id ASC

            $limit
EOS;
            return $this->query($query, $val);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_deliveries_by_io($io_id, $type='', $fields='*')
    {
        try
        {   
            $where = '';
            $val   = [$io_id];

            if($type)
            {
                $where = ' AND a.dr_type_code = ?';
                $val[] = $type;
            }

            $query=<<<EOS
                SELECT
                    $fields
                FROM 
                    $this->tbl_pria_delivery a 
                JOIN
                    $this->tbl_pria_delivery_reference b ON a.dr_gr_id = b.dr_gr_id
                WHERE
                    b.io_id = ?
                $where    
EOS;

            return $this->query($query, $val, TRUE);
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