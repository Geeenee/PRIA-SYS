<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File_list_model extends Portal_Model
{
    private $tbl_pria_internal_orders;
    private $tbl_pria_internal_order_documents;
    private $tbl_pria_internal_order_document_versions;

    private $tbl_documents;
    private $tbl_document_versions;
    private $tbl_users;
    
	public function __construct()
	{
		parent::__construct();

        $this->tbl_pria_internal_orders                     = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
        $this->tbl_pria_internal_order_documents            = Portal_Model::PORTAL_TABLE_INTERNAL_ORDER_DOCUMENTS;
        $this->tbl_pria_internal_order_document_versions    = Portal_Model::PORTAL_TABLE_INTERNAL_ORDER_DOCUMENT_VERSIONS;

        $this->tbl_documents            = Portal_Model::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_document_versions    = Portal_Model::PORTAL_TABLE_DOCUMENT_VERSIONS;
        $this->tbl_users                = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_modules              = Portal_Model::CORE_MODULES;
	}

    /** SELECT FUNCTIONS */
    /** 
     * @Author: Christian Aquino
     * @Date: 2019-06-20 11:27:26
     * @Desc:  Returns list of task indexed by stage id
     * @ReferencedBy:  contract_growers.php
     */

    public function get_file_list($where=array(), $having = '')
    {
        try
        {
            $limit  = '';
            $val    = [];

            if( ISSET($where['limit']) )
            {
                $limit = ' LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

            if( ISSET($where['module']) )
            {
                $val[] = $where['module'];
                $val[] = $where['module'];
            }

            $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          //aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.lname', FALSE, FALSE).",''),'  ',".
                          "IFNULL(".aes_crypt('b.ext_name', FALSE, FALSE).",''))  as created_by";

            $query    =<<<EOS
            SELECT 
                a.document_id,
                a.reference,
                a.document_type_code, 
                a.sys_file_name,
                a.file_name,
                a.version,
                a.module_code,
                a.created_date,
                a.modified_by,
                a.modified_date,
                $fullname
            FROM $this->tbl_documents a
            JOIN $this->tbl_users b 
                ON a.created_by = b.user_id
            JOIN $this->tbl_modules c
                ON a.module_code = c.module_code
            WHERE
                c.module_code = ? OR c.parent_module = ?
            $having
            ORDER BY document_id ASC
            $limit
EOS;
            
            return $this->query($query, $val);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** SELECT FUNCTIONS */
    public function get_files($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='', $having= '')
    {
        try
        {
            $limit  = '';
            $val    = [];

            if( ISSET($where['limit']) )
            {
                $limit = ' LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

            if( ISSET($where['module']) )
            {
                $val[] = $where['module'];
                $val[] = $where['module'];
            }

            $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          //aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.lname', FALSE, FALSE).",''),'  ',".
                          "IFNULL(".aes_crypt('b.ext_name', FALSE, FALSE).",''))  as created_by";

            $query    =<<<EOS
            SELECT 
                a.document_id,
                a.reference,
                a.document_type_code, 
                a.sys_file_name,
                a.file_name,
                a.version,
                a.module_code,
                a.created_date,
                a.modified_by,
                a.modified_date,
                $fullname
            FROM $this->tbl_documents a
            JOIN $this->tbl_users b 
                ON a.created_by = b.user_id
            JOIN $this->tbl_modules c
                ON a.module_code = c.module_code
            WHERE
                c.module_code = ? OR c.parent_module = ?
            ORDER BY document_id ASC

            $limit
EOS;
            return $this->query($query, $val);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

}