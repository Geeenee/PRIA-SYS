<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Toll_partners_model extends Portal_Model
{
    private $tbl_internal_orders;
    private $tbl_soa_documents;
    private $tbl_vendors;
    private $tbl_business_center;
    private $tbl_sites;

    private $tbl_pria_delivery;
    private $tbl_pria_delivery_reference;

    private $tbl_soa;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_soa                          = Portal_Model::PORTAL_TABLE_SOA;
        $this->tbl_soa_documents                = Portal_Model::PORTAL_TABLE_SOA_DOCUMENTS;
        $this->tbl_vendors                      = Portal_Model::PORTAL_TABLE_VENDORS;
        $this->tbl_business_center              = Portal_Model::PORTAL_TABLE_BUSINESS_CENTER;
        $this->tbl_sites                        = Portal_Model::PORTAL_TABLE_SITES;

        $this->tbl_pria_delivery                = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
        $this->tbl_pria_delivery_reference      = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;    
    }

     /** 
     * @Author: Tristan Rosales
     * @Date: 2019-06-03 14:37:00 
     * @Desc: Made to fetch list of SOA in left sub navigation
     * @ReferencedBy: 
     */   

    public function get_soa($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            $condition = '';
            $account_group_code = '';
            $order_by = '';

            if( ISSET($where['account_group_code']) )
            {
                if($where['account_group_code'] == "INBOUND"){
                    $account_group_code = " account_group_code IN ('INBOUND_NORMAL', 'INBOUND_CENTRAL')";
                }else{
                    $account_group_code = " account_group_code = '" . $where['account_group_code'] . "' ";
                }

                $condition = ' WHERE ' . $account_group_code;
            }

            if( ISSET($where['created_date']) ){
                $order_by = ' ORDER BY created_date ASC';
            }

            $query    =<<<EOS
            SELECT 
                soa_num as searchable_value
            FROM  
                $this->tbl_soa 
            $condition    
            $order_by
            $limit
EOS;

            return $this->query($query);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_specific_soa($reference_id)
    {
        try
        {
            $query    =<<<EOS
            SELECT 
                    a.soa_id,
                    a.soa_num,
                    DATE_FORMAT(a.soa_date, "%m/%d/%Y") as soa_date,
                    a.soa_type,
                    a.submission_date,
                    a.account_group_code,
                    a.current_task_id,
                    b.vendor_name,
                    c.file_name,
                    c.sys_file_name,
                    c.version,
                    DATE_FORMAT(c.created_date, "%m/%d/%Y") as uploaded_date
            FROM  
                    $this->tbl_soa a 
            JOIN 
                    $this->tbl_vendors b ON a.vendor_code = b.vendor_code 
            JOIN 
                    $this->tbl_soa_documents c ON a.soa_id = c.soa_id 
            WHERE a.soa_id = $reference_id
EOS;

            return $this->query($query);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    /** 
     * @Author: Tristan Rosales 
     * @Date:   2019-06-03 17:30:00 
     * @Desc:   Made to fetch list of SOA in SOA tabs
     * @ReferencedBy:   
     */    
    public function get_soa_list($where=array())
    {
        try
        {
            $limit = '';
            $condition = '';
            $account_group_code = '';

            if( ISSET($where['account_group_code']) )
            {
                if($where['account_group_code'] == "INBOUND"){
                    $account_group_code = " a.account_group_code IN ('INBOUND_NORMAL', 'INBOUND_CENTRAL')";
                }else{
                    $account_group_code = " a.account_group_code = '" . $where['account_group_code'] . "' ";
                }

                $condition = ' WHERE ' . $account_group_code;
            }

            if( ISSET($where['limit']) )
            {
                $limit = ' LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

            $query    =<<<EOS
            SELECT 
                    a.soa_id as reference_id, a.soa_num as display_num, b.vendor_name as display_name
            FROM  
                    $this->tbl_soa a 
            JOIN 
                    $this->tbl_vendors b ON a.vendor_code = b.vendor_code 
            $condition
                    
            ORDER BY a.soa_id ASC

            $limit
EOS;

            return $this->query($query);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

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
            return $this->insert_data($this->tbl_soa_documents, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

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
}