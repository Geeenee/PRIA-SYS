<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks_model extends Portal_Model
{
    private $tbl_pria_tasks;
    private $tbl_core_tasks;
    private $tbl_core_roles;
    private $tbl_pria_task_actions;
    private $tbl_pria_task_roles;
    private $tbl_pria_task_predecessors;
    private $tbl_pria_task_appendable;
    private $tbl_pria_task_forms;
    private $tbl_pria_task_return;

	public function __construct()
	{
		parent::__construct();

        $this->tbl_pria_tasks             = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_core_tasks             = Portal_Model::CORE_WORKFLOW_STAGE_TASKS;
        $this->tbl_core_workflow          = Portal_Model::CORE_WORKFLOWS;
        $this->tbl_core_workflow_stages   = Portal_Model::CORE_WORKFLOW_STAGES;
        $this->tbl_core_roles             = Portal_Model::CORE_ROLES;
        $this->tbl_core_users             = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_pria_task_actions      = Portal_Model::PORTAL_TABLE_PRIA_TASK_ACTIONS;
        $this->tbl_pria_task_roles        = Portal_Model::PORTAL_TABLE_PRIA_TASK_ROLES;
        $this->tbl_pria_task_predecessors = Portal_Model::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
        $this->tbl_pria_task_appendable   = Portal_Model::PORTAL_TABLE_PRIA_TASK_APPENDABLE;
        $this->tbl_pria_task_forms        = Portal_Model::PORTAL_TABLE_PRIA_TASK_FORMS;
        $this->tbl_pria_workflows         = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_workflow_stages   = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_pria_task_return       = Portal_Model::PORTAL_TABLE_PRIA_TASK_RETURN;
        $this->tbl_pria_task_comments     = Portal_Model::PORTAL_TABLE_PRIA_TASK_COMMENTS;
        $this->tbl_soa                    = Portal_Model::PORTAL_TABLE_SOA;
	}

    /** SELECT FUNCTIONS */
    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-05-21 09:41:26 
     * @Desc:  Returns list of task indexed by stage id
     * @ReferencedBy:  Task.php
     */    
    public function get_stage_tasks_details(array $stages)
    {
        try
        {
            /*
                JOIN 
                $this->tbl_core_tasks b ON a.core_workflow_task_id  = b.workflow_task_id
            */
            $actor      = aes_crypt('a.actor_name', FALSE);
            $stage_ids  = implode("','", $stages);
            $query      =<<<EOS
            SELECT
                a.pria_stage_id, 
                a.pria_task_id, 
                a.sequence_no, 
                a.tat, 
                a.user_id, 
                $actor, 
                a.version_flag, 
                a.task_status_id, 
                a.core_workflow_task_id, 
                a.task_name,
                IFNULL(c.action_name, 'Pending') as task_status,
                c.pria_task_action_id as task_status_id,
                e.role_name,
                g.workflow_name as appendable_workflow_name,
                f.core_workflow_id as appendable_workflow_id,
                h.controller,
                a.end_date,
                d.role_code
            FROM
                $this->tbl_pria_tasks a    
            LEFT JOIN
                $this->tbl_pria_task_actions c ON a.pria_task_id    = c.pria_task_id AND a.task_status_id = c.pria_task_action_id
            JOIN 
                $this->tbl_pria_task_roles d ON a.pria_task_id = d.pria_task_id AND d.actor_flag = {$this->initial_yes}
            JOIN 
                $this->tbl_core_roles e ON d.role_code = e.role_code
            LEFT JOIN 
                $this->tbl_pria_task_appendable f ON a.pria_task_id = f.pria_task_id    
            LEFT JOIN 
                $this->tbl_core_workflow g ON f.core_workflow_id = g.workflow_id    
            LEFT JOIN
                $this->tbl_pria_task_forms h ON a.pria_task_id = h.pria_task_id
            WHERE 
                a.pria_stage_id IN ('$stage_ids')    
            ORDER BY 
                a.sequence_no
EOS;
            /* $db		= static::get_connection();						
            $stmt	= $db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC); */

            return $this->select_query_group($query);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_predecessors(array $tasks)
    {
        try
        {
            $task_ids  = implode("','", $tasks);
            $query      =<<<EOS
                SELECT 
                    a.pria_task_id, 
                    a.pre_pria_task_id, 
                    b.task_status_id 
                FROM 
                    $this->tbl_pria_task_predecessors a 
                JOIN 
                    $this->tbl_pria_tasks b ON a.pre_pria_task_id = b.pria_task_id
                WHERE 
                    a.pria_task_id IN ('$task_ids')
EOS;
            return $this->select_query_group($query);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-05-22 17:48:54 
     * @Desc:  
     * @ReferencedBy:  Transaction_Controller.php, Medvac.php, Task.php, Pria_overview.php
     */    
    public function get_task_details($task_id)
    {
      
        try
        {
            $actor      = aes_crypt('a.actor_name', FALSE, FALSE);
            $query      =<<<EOS
            SELECT
                a.pria_stage_id, 
                a.pria_task_id, 
                a.task_status_id, 
                a.task_name,
                b.stage_name,
                c.reference_id,
                c.pria_workflow_id,
                IF(a.actor_name IS NULL, e.role_name, $actor) as actor,
                IFNULL(f.action_name, 'Pending') as task_status,
                a.reference as task_reference_id,
                a.user_id,
                g.controller,
                d.role_code,
                c.account_group_code,
                c.reference_num
            FROM
                $this->tbl_pria_tasks a
            JOIN
                $this->tbl_pria_workflow_stages b ON a.pria_stage_id = b.pria_stage_id
            JOIN
                $this->tbl_pria_workflows c ON b.pria_workflow_id = c.pria_workflow_id
            JOIN 
                $this->tbl_pria_task_roles d ON a.pria_task_id = d.pria_task_id  AND d.actor_flag = {$this->initial_yes}
            JOIN 
                $this->tbl_core_roles e ON d.role_code = e.role_code    
            JOIN 
                $this->tbl_pria_task_forms g ON a.pria_task_id = g.pria_task_id  
            LEFT JOIN
                $this->tbl_pria_task_actions f ON a.pria_task_id = f.pria_task_id AND a.task_status_id = f.pria_task_action_id
            WHERE 
                a.pria_task_id = ?
EOS;
            return $this->query($query, [$task_id], TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_actions(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_task_actions, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-05-23 15:56:19 
     * @Desc: Gets the reference ID for the workflow where the task is under.
     * E.g. Gets the io_id for the task 
     * @ReferencedBy: Medvac.php
     * @Returns : the reference_id
     */    
    public function get_task_workflow_reference_id($task_id)
    {
      
        try
        {
            $query      =<<<EOS
            SELECT
                c.reference_id
            FROM
                $this->tbl_pria_tasks a
            JOIN
                $this->tbl_pria_workflow_stages b ON a.pria_stage_id = b.pria_stage_id
            JOIN
                $this->tbl_pria_workflows c ON b.pria_workflow_id = c.pria_workflow_id
            WHERE 
                a.pria_task_id = ?
EOS;
            return $this->query($query, [$task_id], TRUE, FALSE)['reference_id'];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_roles(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_task_roles, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-05-29 16:56:01 
     * @Desc:  Get all dependent tasks 
     * @ReferencedBy:  
     */    
    public function get_dependent_tasks($task_id)
    {
        try
        { 
            $query      =<<<EOS
                SELECT 
                    a.pria_task_id, 
                    b.task_status_id,
                    c.controller,
                    b.task_name 
                FROM 
                    $this->tbl_pria_task_predecessors a 
                JOIN 
                    $this->tbl_pria_tasks b ON a.pre_pria_task_id = b.pria_task_id
                JOIN 
                    $this->tbl_pria_task_forms c ON a.pria_task_id = c.pria_task_id
                WHERE 
                    a.pre_pria_task_id = ?
EOS;
        
            return $this->query($query, [$task_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_appendable_workflow_by_ag_reference_id($reference_id, $ag_code)
    {
        try
        {
            $query =<<<EOS
            SELECT 
                e.workflow_name as appendable_workflow_name, 
                e.workflow_id   as appendable_workflow_id,
                c.core_workflow_task_id,
                a.reference_id,
                a.core_workflow_id,
                b.core_workflow_stage_id
            FROM 
                $this->tbl_pria_workflows a
            JOIN 
                $this->tbl_pria_workflow_stages b ON a.pria_workflow_id = b.pria_workflow_id
            JOIN 
                $this->tbl_pria_tasks c ON b.pria_stage_id = c.pria_stage_id
            JOIN 
                $this->tbl_pria_task_appendable d ON c.pria_task_id = d.pria_task_id
            JOIN 
                $this->tbl_core_workflow e ON d.core_workflow_id = e.workflow_id
            WHERE 
                a.reference_id          = ? 
            AND
                a.account_group_code    = ?
EOS;
            
            return $this->query($query, [$reference_id, $ag_code], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_appendable_workflow_by_pria_workflow_id($pria_workflow_id)
    {
        try
        {
            $query =<<<EOS
            SELECT 
                e.workflow_name as appendable_workflow_name, 
                e.workflow_id   as appendable_workflow_id,
                c.core_workflow_task_id,
                a.pria_workflow_id,
                a.core_workflow_id,
                b.core_workflow_stage_id,
                a.reference_id
            FROM 
                $this->tbl_pria_workflows a
            JOIN 
                $this->tbl_pria_workflow_stages b ON a.pria_workflow_id = b.pria_workflow_id
            JOIN 
                $this->tbl_pria_tasks c ON b.pria_stage_id = c.pria_stage_id
            JOIN 
                $this->tbl_pria_task_appendable d ON c.pria_task_id = d.pria_task_id
            JOIN 
                $this->tbl_core_workflow e ON d.core_workflow_id = e.workflow_id
            WHERE 
                a.pria_workflow_id = ?
EOS;
            
            return $this->query($query, [$pria_workflow_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-06-06 17:23:33 
     * @Desc:  Gets the sequence where the stage will be appended 
     * @ReferencedBy:  Task.php
     */    
    public function get_stage_seq_where_to_append($core_workflow_id, $core_workflow_stage_id, $pria_workflow_id)
    {
        try
        {
            $query =<<<EOS
                SELECT 
                    a.sequence_no - 1 AS sequence_no
                FROM  
                    $this->tbl_pria_workflow_stages a
                WHERE 
                    a.core_workflow_stage_id = (
                    SELECT  
	                    workflow_stage_id
                    FROM 
                        $this->tbl_core_workflow a
                    JOIN 
                        $this->tbl_core_workflow_stages b ON a.workflow_id = b.workflow_id
                    WHERE 
                        a.workflow_id = ?
                    AND 
                        sequence_no = (
                                 SELECT 
                                    sequence_no + 1 
                                 FROM 
                                    $this->tbl_core_workflow_stages 
                                WHERE 
                                    workflow_stage_id = ? 
                        )
                ) 
                AND 
                    a.pria_workflow_id = ?
EOS;
            $result = $this->query($query, [$core_workflow_id, $core_workflow_stage_id, $pria_workflow_id], TRUE, FALSE);

            return $result['sequence_no'];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** 
     * @Author: Christian Aquino 
     * @Date: 2019-06-07 01:30:01 
     * @Desc:  Get all return task values 
     * @ReferencedBy:  
     */    
    public function get_return_tasks($task_id)
    {
        try
        {
            $query      =<<<EOS
                SELECT 
                    a.pria_task_id, 
                    a.ret_pria_task_id,
                    a.pria_stage_id,
                    b.task_name
                FROM 
                    $this->tbl_pria_task_return a
                JOIN $this->tbl_pria_tasks b ON a.ret_pria_task_id = b.pria_task_id
                WHERE 
                    a.pria_task_id = ?
EOS;
            return $this->query($query, [$task_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_workflow_involved_users($reference_id, $ag_code)
    {
        try
        {
            $query =<<<EOS
            SELECT 
                c.user_id
            FROM   
                pria_workflows a 
            JOIN   
                pria_workflow_stages b  ON a.pria_workflow_id = b.pria_workflow_id
            JOIN   
                pria_tasks c            ON b.pria_stage_id = c.pria_stage_id
            WHERE  
                a.reference_id 		 = ?
            AND   
                a.account_group_code = ?
            AND    
                c.user_id IS NOT NULL
            GROUP BY 
                c.user_id
EOS;
            return $this->query($query, [$reference_id, $ag_code], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    public function get_task_comment(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pria_task_comments, FALSE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

        /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-06-13 14:30:29 
     * @Desc:  Specifically made to retrieve comments for display inside task
     * @ReferencedBy:   Transaction_Controller.php
     */    
    public function get_task_comment_details_by_comment_id($pria_task_comment_id)
    {
        try
        {
            $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          //aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.lname', FALSE, FALSE).",''),'  ',".
                          "IFNULL(".aes_crypt('b.ext_name', FALSE, FALSE).",''))  as commented_by";
            $query      =<<<EOS
            SELECT 
                b.photo, 
                a.created_by, 
                a.created_date, 
                a.pria_task_comment,
                a.pria_task_comment_id,
                $fullname
            FROM   
                $this->tbl_pria_task_comments a
            JOIN   
                $this->tbl_core_users b ON a.created_by = b.user_id
            WHERE
                a.pria_task_comment_id = ?    
            ORDER BY 
                a.created_date DESC
EOS;

            return $this->query($query, [$pria_task_comment_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    /** 
     * @Author: Kevin Villarojo 
     * @Date: 2019-06-13 14:30:29 
     * @Desc:  Specifically made to retrieve comments for display inside task
     * @ReferencedBy:   Transaction_Controller.php
     */    
    public function get_task_comments_details($pria_task_id)
    {
        try
        {
            $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          //aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.lname', FALSE, FALSE).",''),'  ',".
                          "IFNULL(".aes_crypt('b.ext_name', FALSE, FALSE).",''))  as commented_by";
            $query      =<<<EOS
            SELECT 
                b.photo, 
                a.created_by, 
                a.created_date, 
                a.pria_task_comment,
                a.pria_task_comment_id,
                $fullname
            FROM   
                $this->tbl_pria_task_comments a
            JOIN   
                $this->tbl_core_users b ON a.created_by = b.user_id
            WHERE
                a.pria_task_id = ?    
            ORDER BY 
                a.created_date DESC
EOS;

            return $this->query($query, [$pria_task_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** INSERT FUNCTIONS */
    public function insert_task_comment($data)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_task_comments, $data, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

}