<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files_model extends Portal_Model
{
    private $tbl_documents;
    private $tbl_document_versions;
    private $tbl_users;
    private $tbl_pria_workflows;
    private $tbl_pria_tasks;
    private $tbl_pria_workflow_stages;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_documents            = Portal_Model::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_document_versions    = Portal_Model::PORTAL_TABLE_DOCUMENT_VERSIONS;
        $this->tbl_users                = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_modules              = Portal_Model::CORE_MODULES;
        $this->tbl_pria_workflows       = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_workflow_stages = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_pria_tasks           = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_pria_contracts       = Portal_Model::PORTAL_TABLE_CONTRACTS;
    }

    /** SELECT FUNCTIONS */
    /** 
     * @Author: Christian Aquino
     * @Date: 2019-07-01 11:27:26
     * @Desc:  Returns list of Files
     * @ReferencedBy:  Files.php
     */

    public function get_file_list($where=array(), $filters=array())
    {
        try
        {
            $limit   = '';
            $val     = [];
            $having  = [];
            $filter  = ''; 

            if( ISSET($where['limit']) )
            {
                $limit = ' LIMIT '.$where['limit']['from'].','.$where['limit']['to'];
            }

            if( ISSET($where['module']) )
            {
                $val[] = $where['module'];
                $val[] = $where['module'];
            }

            if( ISSET($where['having']) )
            {
                $having[] = $where['having'];
            }
            
            if( ! EMPTY($filters['filter-keyword']))
            {
                //$filter  .= ' AND a.file_name LIKE ?';
                $having[] = 'a.file_name LIKE ?';
                $val[]    =  "%".strtolower(filter_var($filters['filter-keyword'], FILTER_SANITIZE_STRING))."%";
            }

            if( ! EMPTY($filters['filter-assign-to']))
            {
                //$filter .= ' AND a.created_by IN ('.implode(',', $filters['filter-assign-to']).')';
                $having[] = 'a.created_by IN ('.implode(',', $filters['filter-assign-to']).')';
            }

            $having = implode('AND ', $having);

            /*
            else
            {
                $having = 'HAVING '.$filter;
            } */
 
            $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          //aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.lname', FALSE, FALSE).",''),'  ',".
                          "IFNULL(".aes_crypt('b.ext_name', FALSE, FALSE).",''))  as created_by";
            
            $org_code = 'f.org_code';
            $join     = '';
               
            if($where['module'] == MODULE_PORTAL_TRANS_LESSORS)
            {
                $org_code = 'd1.org_code'; 

                $join     =<<<EOS
                        JOIN $this->tbl_pria_contracts d1 ON d1.contract_id = a.reference
EOS;
            }
               


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
                $fullname,
                f.vendor_code,
                $org_code
            FROM $this->tbl_documents a
            JOIN $this->tbl_users b 
                ON a.created_by = b.user_id
            LEFT JOIN $this->tbl_modules c
                ON a.module_code = c.module_code
            LEFT JOIN $this->tbl_pria_tasks d ON a.pria_task_id = d.pria_task_id
            LEFT JOIN $this->tbl_pria_workflow_stages e ON d.pria_stage_id = e.pria_stage_id
            LEFT JOIN $this->tbl_pria_workflows f ON e.pria_workflow_id = f.pria_workflow_id
            $join
            WHERE
                c.module_code = ? OR c.parent_module = ?
            $having
            ORDER BY document_id DESC
            $limit
EOS;
          //  print_var_export($query, $val); die;

            return $this->query($query, $val);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** SELECT FUNCTIONS */
    public function get_files($filters=array(),$where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            $val    = [];
            $having = '';
            $filter = '';

            if( ISSET($where['module']) )
            {
                $val[] = $where['module'];
                $val[] = $where['module'];
            }

            if( ! EMPTY($filters['filter-keyword']))
            {
                $filter .= ' AND a.file_name LIKE ?';
                $val[]   =  "%".strtolower(filter_var($filters['filter-keyword'], FILTER_SANITIZE_STRING))."%";
            }

            if( ! EMPTY($filters['filter-assign-to']))
            {
                $filter .= ' AND a.created_by IN ('.implode(',', $filters['filter-assign-to']).')';
            }

            if( ISSET($where['having']) )
            {
                $having = $where['having'].$filter;
            }
            else
            {
                $having = 'HAVING '.$filter;
            }
            

            $query    =<<<EOS
            SELECT 
                a.document_id,
                a.reference,
                a.document_type_code,
                a.sys_file_name ,
                a.file_name,
                a.version,
                a.created_by,
                a.created_date,
                a.modified_by,
                a.modified_date,
                f.vendor_code,
                f.org_code
            FROM $this->tbl_documents a
            JOIN $this->tbl_users b 
                ON a.created_by = b.user_id
            JOIN $this->tbl_modules c
                ON a.module_code = c.module_code
            LEFT JOIN $this->tbl_pria_tasks d ON a.pria_task_id = d.pria_task_id
            LEFT JOIN $this->tbl_pria_workflow_stages e ON d.pria_stage_id = e.pria_stage_id
            LEFT JOIN $this->tbl_pria_workflows f ON e.pria_workflow_id = f.pria_workflow_id
            WHERE
                c.module_code = ? OR c.parent_module = ?
            $having
            ORDER BY document_id DESC
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