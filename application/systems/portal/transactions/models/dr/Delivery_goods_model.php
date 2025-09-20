<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delivery_goods_model extends Portal_Model
{
    private $tbl_pria_delivery_goods;
    private $tbl_pria_delivery_goods_reference;
    private $tbl_delivery_goods_documents;
    private $tbl_core_users;
    private $tbl_core_user_roles;
    private $tbl_pria_references;
    private $tbl_transmittals;
    private $tbl_pria_workflow;
    private $tbl_tbl_pria_workflow_stages;
    private $tbl_tbl_pria_tasks;
    private $tbl_pria_task_forms;
    private $tbl_pria_task_roles;

	public function __construct()
	{
		parent::__construct();

        $this->tbl_pria_delivery_goods           = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
        $this->tbl_pria_delivery_goods_reference = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
        $this->tbl_internal_orders               = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
        $this->tbl_delivery_goods_documents      = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_DOCUMENTS;
        $this->tbl_core_users                    = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_core_user_roles               = Portal_Model::CORE_USER_ROLES;
        $this->tbl_transmittals                  = Portal_Model::PORTAL_TABLE_TRANSMITTALS;
        $this->tbl_pria_references               = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE;
        $this->tbl_param_dr_types                = Portal_Model::PORTAL_TABLE_PARAM_DR_TYPES;
        $this->tbl_pria_workflow                 = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_workflow_stages          = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_pria_tasks                    = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_pria_task_forms               = Portal_Model::PORTAL_TABLE_PRIA_TASK_FORMS;
        $this->tbl_pria_task_roles               = Portal_Model::PORTAL_TABLE_PRIA_TASK_ROLES;
        $this->tbl_pria_task_predecessors        = Portal_Model::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
	}

    /** SELECT FUNCTIONS */
    public function get_delivery_goods_receipt(array $where=[], array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_delivery_goods, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function check_amounts($drg_ids = '', $po_amount = '', $dr_amount = '')
    {
        try
        {
            $val    = [];
            $where  = '';

            if(!EMPTY($drg_ids)){
                $where = 'WHERE dr_gr_id IN ('.$drg_ids.')';
            }

            $query    =<<<EOS
                SELECT 
                   SUM(dr_amount) total_dr_amount
                FROM $this->tbl_pria_delivery_goods
                {$where}
                
EOS;
            return $this->query($query, $val, TRUE, FALSE);

        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_delivery_goods_receipt_ref($drg_type, $ref_num)
    {
        try
        {

            $query    =<<<EOS
                

                SELECT * 
                FROM $this->tbl_pria_workflow a
                    JOIN $this->tbl_pria_workflow_stages b 
                        ON a.pria_workflow_id = b.pria_workflow_id 
                    JOIN $this->tbl_pria_tasks c 
                        ON b.pria_stage_id = c.pria_stage_id 
                    JOIN $this->tbl_pria_delivery_goods d
                        ON c.pria_task_id = d.pria_task_id
                    JOIN $this->tbl_param_dr_types e
                        ON d.dr_type_code = e.dr_type_code  
                WHERE a.reference_num = ? and e.drg_code = ?
EOS;
            
            return $this->query($query, array($ref_num, $drg_type), TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
 
    public function get_delivery_goods_reference(array $where=[], array $fields=['*'], $multiple = FALSE)
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_delivery_goods_reference, $multiple, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_delivery_goods_receipt_io(array $where=[], $fields=' * ', $dr_type = NULL, $io_id = NULL)
    {
        try
        {   
            $and = '';
            $val = array(
                $dr_type
            );

            IF($io_id){
                $and = 'AND c.io_id = '.$io_id;
            }

            $query    =<<<EOS
                SELECT 
                   {$fields}
                FROM $this->tbl_pria_delivery_goods a 
                JOIN $this->tbl_pria_delivery_goods_reference b ON a.dr_gr_id = b.dr_gr_id
                JOIN $this->tbl_internal_orders c ON b.io_id = c.io_id
                WHERE a.dr_type_code = ? {$and}
EOS;
            
            return $this->query($query, $val, TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Christian Aquino
     * @Date: 2019-09-18 15:35:19 
     * @Referenced : Encode_clean_up.php, Ret_app_clean_up.php
     * @Desc:  specifically made for the controllers above
     */    
    public function get_gr_dr_nums($dr_type, $column, $io_id)
    {
        try
        {   
            /* 
            IF($io_id){
                $and = 'AND c.io_id = '.$io_id;
            }
            

            $query    =<<<EOS
                SELECT 
                    group_concat(' ',a.dr_num) dr_nums, 
                    group_concat(' ',a.gr_num) gr_nums, 
                    dr_type_code,
                    e.controller
                FROM $this->tbl_pria_delivery_goods a
                JOIN $this->tbl_pria_delivery_goods_reference b ON a.dr_gr_id = b.dr_gr_id
                JOIN $this->tbl_internal_orders c ON b.io_id = c.io_id
                JOIN $this->tbl_pria_tasks d ON a.pria_task_id = d.pria_task_id
                JOIN $this->tbl_pria_task_forms e ON a.pria_task_id = e.pria_task_id
                WHERE d.task_status_id != ? {$and} and a.dr_type_code = ?
                GROUP BY c.io_id
EOS;
            
            return $this->query($query, [TASK_STATUS_SKIPPED, $dr_type], TRUE, FALSE); */

            $query    =<<<EOS
                SELECT 
                    $column as ref_num,
                    dr_type_code,
                    e.controller,
                    d.pria_task_id
                FROM 
                    $this->tbl_pria_delivery_goods a
                JOIN 
                    $this->tbl_pria_delivery_goods_reference b ON a.dr_gr_id = b.dr_gr_id
                JOIN 
                    $this->tbl_internal_orders c ON b.io_id = c.io_id
                JOIN 
                    $this->tbl_pria_tasks d ON a.pria_task_id = d.pria_task_id
                JOIN 
                    $this->tbl_pria_task_forms e ON a.pria_task_id = e.pria_task_id
                WHERE 
                    d.task_status_id != ? 
                AND 
                    c.io_id = ?
                AND 
                    a.dr_type_code = ?
                GROUP BY 
                    c.io_id, a.dr_gr_id
EOS;
            return $this->query($query, [TASK_STATUS_SKIPPED, $io_id, $dr_type], TRUE, TRUE); 
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-06-06 16:11:32 
     * @Desc: Retrieves the if the LAST DR is a 
     * @ReferencedBy:  Task.php
     */    
    public function get_last_delivery($dr_type, $reference, $fields=['*'])
    {
        try
        {
            $fields = implode(',', $fields);

            switch($dr_type)
            {
                case DR_MEDVAC:
                case DR_DOCGR:
                case DR_DOCDR:
                    $column = 'b.io_id';
                break;

                case DR_PO:
                     $column = 'b.po_id';
                break;
            }

            if(EMPTY($column))
                throw new PDOException('DR type is required.');

            $query=<<<EOS
                SELECT
                    $fields
                FROM 
                    $this->tbl_pria_delivery_goods a 
                JOIN
                    $this->tbl_pria_delivery_goods_reference b ON a.dr_gr_id = b.dr_gr_id
                WHERE
                    $column        = ?
                AND 
                    a.dr_type_code = ?
                AND 
                    a.last_dr_flag = ?
EOS;
            
            return $this->query($query, [$reference, $dr_type, ENUM_YES], TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_total_delivery_amount_by_po_id($po_id)
    {
        try
        {
            $query =<<<EOS
                SELECT 
                    SUM(dr_amount) AS total_amount
                FROM 
                    $this->tbl_pria_delivery_goods_reference a
                JOIN 
                    $this->tbl_pria_delivery_goods b ON a.dr_gr_id =  b.dr_gr_id
                WHERE 
                    a.po_id = ?
EOS;
            $result = $this->query($query, [$po_id], TRUE, FALSE);

            return $result['total_amount'];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_delivery_goods_document($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_DOCUMENTS, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_dr_transmittal($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   

            return $this->select_data($fields, Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_REFERENCE, FALSE, $where, $order, $group, $limit);
        
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_transmittal(array $where=[], array $fields=['*'])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_transmittals, FALSE, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_dr_references(array $where=[], array $fields=['*'])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_references, FALSE, $where);
        }
        catch(PDOException $e) 
        {
            throw $e;
        }
    }

    public function get_dr_references_multiple(array $where=[], array $fields=['*'])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_references, TRUE, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    /** INSERT FUNCTIONS */
    public function insert_delivery_goods_receipt(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_delivery_goods, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_delivery_goods_reference(array $fields)
    {
        try
        {
            $this->insert_data($this->tbl_pria_delivery_goods_reference, $fields, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_delivery_goods_receipt_document(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_delivery_goods_documents, $fields, $return);
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

    public function insert_dr_transmittals(array $fields, $return=TRUE)
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
    public function update_delivery_goods_receipt(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_pria_delivery_goods, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_delivery_goods_receipt_document(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_delivery_goods_documents, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_dr_transmittals(array $where, array $fields)
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

    public function get_dr_recipients_by_org($org_code, $status=STATUS_ACTIVE)
    {
        try
        {
            $tbl_users 		= parent::CORE_TABLE_USERS;
            $tbl_user_roles = parent::CORE_USER_ROLES;
            $tbl_user_orgs  = parent::PORTAL_TABLE_USER_ORGS;
            $vendor = TASK_ROLE_VENDOR;

            $query 	 = <<<EOS
                SELECT
                    c.user_id, 
                    CONCAT(
                        AGDEC(c.fname), ' ',
                        IFNULL(AGDEC(c.mname),''), ' ',
                        IFNULL(AGDEC(c.lname),''),' ',
                        IFNULL(AGDEC(c.ext_name),'')
                    ) as fullname,
                    GROUP_CONCAT(a.role_code) as role_codex
                FROM
                    $tbl_users c 
                JOIN
                    $tbl_user_orgs b ON b.user_id = c.user_id
                JOIN
                    $tbl_user_roles a ON a.user_id = c.user_id
                WHERE
                    b.org_code  = ?
                AND 
                    c.status 	= ?
                GROUP BY
                    c.user_id
                HAVING
                    role_codex NOT LIKE '%$vendor%'
                ORDER BY fullname
EOS;

                return $this->query($query, [$org_code, $status], TRUE, TRUE);
            }
            catch(PDOException $e)
            {
                throw $e;
            }  
    }

    public function get_dr_doc_recipient()
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
                    -- B.role_code,
                    ' . $decrypt . '
                                        
                FROM ' . $this->tbl_core_users . ' A
                
                

            ';
            // JOIN ' . $this->tbl_core_user_roles . ' B ON A.user_id = B.user_id
            
            return $this->query($query, array(), TRUE, TRUE);
            
            
        }   
        catch (PDOException $e)
        {
            throw $e;
        }
    }

    public function update_task_roles(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_pria_task_roles, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_dependent_tasks($core_task_id, $pria_task_id)
    {
        try
        {
            $d_q_marks = $s_q_marks = "";

            $values = [];

            $dependent_core_tasks   = ["0"];
            $core_task_stages       = ["0"];

            if(in_array($core_task_id, [CORE_TASK_MED_VAC, CORE_TASK_MED_VAC_APPEND]))
            {
                $dependent_core_tasks   = [CORE_TASK_DOC_DR, CORE_TASK_DOC_DR_APPEND];
                $core_task_stages       = [CORE_WORKFLOW_STAGE_MEDVAC, CORE_WORKFLOW_STAGE_MEDVAC_APPEND];
            }
            else if(in_array($core_task_id, [CORE_TASK_DOC_DR, CORE_TASK_DOC_DR_APPEND]))
            {
                $dependent_core_tasks   = [CORE_TASK_CLEANUP];
                $core_task_stages       = [CORE_WORKFLOW_STAGE_DOC_DR, CORE_WORKFLOW_STAGE_DOC_DR_APPEND];
            }

            if(COUNT($dependent_core_tasks) > 0)
            {
                foreach($dependent_core_tasks AS $key => $dependent_core_task)
                {
                    $d_q_marks  .= (!EMPTY($d_q_marks))? ", ?": "?";
                    $values[]   = $dependent_core_task;
                }
            }

            $values[]   = $pria_task_id;

            if(COUNT($core_task_stages) > 0)
            {
                foreach($core_task_stages AS $key => $core_task_stage)
                {
                    $s_q_marks  .= (!EMPTY($s_q_marks))? ", ?": "?";
                    $values[]   = $core_task_stage;
                }
            }

            $query  =<<<EOS
                SELECT
                    A.pria_task_id,
                    A.user_id,
                    COUNT(IF(D.pria_task_id IS NOT NULL AND D.task_status_id IS NULL, D.pria_task_id, NULL)) pending_ongoing,
                    GROUP_CONCAT(F.pria_task_id) dependent_task
                FROM $this->tbl_pria_tasks A
                JOIN $this->tbl_pria_workflow_stages B ON A.pria_stage_id = B.pria_stage_id
                JOIN $this->tbl_pria_workflow_stages C ON B.pria_workflow_id = C.pria_workflow_id
                LEFT JOIN $this->tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id
                AND D.sequence_no = (
                    SELECT MAX(sequence_no)
                    FROM $this->tbl_pria_tasks
                    WHERE pria_stage_id = D.pria_stage_id
                )
                LEFT JOIN $this->tbl_pria_task_predecessors E
                ON D.pria_task_id = E.pre_pria_task_id
                LEFT JOIN $this->tbl_pria_tasks F ON E.pria_task_id = F.pria_task_id
                AND F.core_workflow_task_id IN ($d_q_marks) AND F.task_status_id IS NULL
                WHERE A.pria_task_id = ? AND C.core_workflow_stage_id IN ($s_q_marks)
                GROUP BY A.pria_task_id
EOS;
            return $this->query($query, $values, TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    // Change request 12.21.22 Starts Here
    public function get_gdr_task_ref($pria_workflow_id)
    {
        try
        {
            $query = "
                SELECT 
                    c.reference
                FROM $this->tbl_pria_workflow a
                JOIN $this->tbl_pria_workflow_stages b 
                    ON a.pria_workflow_id = b.pria_workflow_id 
                JOIN $this->tbl_pria_tasks c 
                    ON b.pria_stage_id = c.pria_stage_id 
                WHERE c.core_workflow_task_id = 3 AND a.pria_workflow_id = {$pria_workflow_id}
            ";
            return $this->query($query, [], TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    // Change request 12.21.22 Ends Here

}