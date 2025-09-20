<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends Portal_Model
{
    public $tbl_pria_tasks;
    public $tbl_pria_workflow_stages;
    public $tbl_pria_workflows;
    public $tbl_core_tasks;
    public $tbl_core_roles;
    public $tbl_pria_task_actions;
    public $tbl_pria_task_roles;
    public $tbl_pria_task_predecessors;
    public $tbl_pria_task_appendable;
    public $tbl_pria_task_forms;
    public $tbl_pria_task_return;
    public $tbl_pria_task_attachments;
    public $tbl_module_account_group;

	public function __construct()
	{
        parent::__construct();

        $this->tbl_pria_tasks                  = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_core_tasks                  = Portal_Model::CORE_WORKFLOW_STAGE_TASKS;
        $this->tbl_core_workflow               = Portal_Model::CORE_WORKFLOWS;
        $this->tbl_core_workflow_stages        = Portal_Model::CORE_WORKFLOW_STAGES;
        $this->tbl_core_roles                  = Portal_Model::CORE_ROLES;
        $this->tbl_core_users                  = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_core_user_roles             = Portal_Model::CORE_USER_ROLES;
        $this->tbl_pria_task_actions           = Portal_Model::PORTAL_TABLE_PRIA_TASK_ACTIONS;
        $this->tbl_pria_task_email_links       = Portal_Model::PORTAL_TABLE_PRIA_TASK_EMAIL_LINKS;
        $this->tbl_pria_task_roles             = Portal_Model::PORTAL_TABLE_PRIA_TASK_ROLES;
        $this->tbl_pria_task_predecessors      = Portal_Model::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
        $this->tbl_pria_task_appendable        = Portal_Model::PORTAL_TABLE_PRIA_TASK_APPENDABLE;
        $this->tbl_pria_task_forms             = Portal_Model::PORTAL_TABLE_PRIA_TASK_FORMS;
        $this->tbl_pria_workflows              = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS;
        $this->tbl_pria_workflow_stages        = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
        $this->tbl_pria_task_return            = Portal_Model::PORTAL_TABLE_PRIA_TASK_RETURN;
        $this->tbl_pria_task_comments          = Portal_Model::PORTAL_TABLE_PRIA_TASK_COMMENTS;
        $this->tbl_pria_task_attachments       = Portal_Model::PORTAL_TABLE_PRIA_TASK_ATTACHMENTS;
        $this->tbl_pria_task_document_types    = Portal_Model::PORTAL_TABLE_PRIA_TASK_DOCUMENT_TYPES;

        $this->tbl_module_account_group        = Portal_Model::PORTAL_TABLE_MODULE_ACCOUNT_GROUPS;

        $this->tbl_pria_documents              = Portal_Model::PORTAL_TABLE_DOCUMENTS;

        $this->tbl_documents                   = Portal_Model::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_document_versions           = Portal_Model::PORTAL_TABLE_DOCUMENT_VERSIONS;

        $this->tbl_pria_workflow_task_doc_type = Portal_Model::PORTAL_TABLE_PRIA_WORKFLOWS_TASK_DOCUMENT_TYPES;
        $this->tbl_workflow_task_updates       = Portal_Model::PORTAL_TABLE_WORKFLOW_TASK_UPDATES;
        $this->tbl_pria_tab_modules            = Portal_Model::PORTAL_TABLE_PRIA_TAB_MODULE;

        $this->tbl_param_document_types        = Portal_Model::PORTAL_TABLE_PARAM_DOCUMENT_TYPES;
	}

    /** SELECT FUNCTIONS */
    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-05-21 09:41:26
     * @Desc:  Returns list of task indexed by stage id
     * @ReferencedBy:  Task.php
     */
    public function get_stage_tasks_details(array $stages, $filter_params=array())
    {
        try
        {
            $values     = array(ENUM_YES, TASK_STATUS_DONE);
            $filter     = "";

            /*
                JOIN
                $this->tbl_core_tasks b ON a.core_workflow_task_id  = b.workflow_task_id
            */
            $actor      = aes_crypt('a.actor_name', FALSE);
            $stage_ids  = implode("','", $stages);


            if(COUNT($filter_params) > 0)
            {
                $filter = $filter_params['having'];
                $values = array_merge($values, $filter_params['values']);
            }

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
                IF(c.action_name IS NULL, 'Pending', IF(a.returned_flag = ? AND a.task_status_id = ?, 'Resubmitted', c.action_name)) as task_status,
                c.pria_task_action_id as task_action_id,
                GROUP_CONCAT(e.role_name ORDER BY e.role_name ASC SEPARATOR ', ') as role_name,
                g.workflow_name as appendable_workflow_name,
                f.core_workflow_id as appendable_workflow_id,
                h.controller,
                a.end_date,
                a.actual_end_date,
                a.expected_end_date,
                GROUP_CONCAT(d.role_code ORDER BY d.role_code ASC) as role_codes,
                i.pria_workflow_id,
                a.reference task_reference_id,
                a.returned_flag
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
            LEFT JOIN
                $this->tbl_pria_workflow_stages i ON a.pria_stage_id = i.pria_stage_id
            WHERE
                a.pria_stage_id IN ('$stage_ids')
            GROUP BY
                a.pria_task_id
            $filter
            ORDER BY
                a.sequence_no
EOS;

            //print_var_export($query, $values); die;
            return $this->select_query_group($query, $values);
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

    public function get_all_task_actions(array $tasks)
    {
        try
        {
            $task_ids  = implode("','", $tasks);
            $query      =<<<EOS
                SELECT
                    a.pria_task_id,
                    a.pria_task_action_id
                FROM
                    $this->tbl_pria_task_actions a
                JOIN
                    $this->tbl_pria_tasks b ON a.pria_task_id = b.pria_task_id
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
           //-- IF(a.actor_name IS NULL, GROUP_CONCAT(e.role_name ORDER BY e.role_name SEPARATOR ', '), CONCAT(CAST($actor as CHAR), ' - ', GROUP_CONCAT(h1.role_name))) as actor,
            $query      =<<<EOS
            SELECT
                a.pria_stage_id,
                a.pria_task_id,
                a.task_status_id,
                b.status_code as stage_status_code,
                a.task_name,
                b.stage_name,
                a.sequence_no,
                c.reference_id,
                c.pria_workflow_id,
                b.sequence_no as pria_stage_sequence_no,
                CONCAT(CAST($actor as CHAR), ' - ', GROUP_CONCAT(DISTINCT h1.role_name SEPARATOR ', ')) as actor,
                GROUP_CONCAT(DISTINCT e.role_name SEPARATOR ', ') as task_role,
                IFNULL(f.action_name, 'Pending') as task_status,
                a.reference as task_reference_id,
                a.user_id,
                g.controller,
                d.role_code,
                c.account_group_code,
                c.reference_num,
                a.core_workflow_task_id,
                a.returned_flag,
                a.due_date_tag,
                a.system_based_tag,
                a.actual_start_date,
                a.actual_end_date,
                a.tat,
                a.notif_flag,
                g.btn_save_review_flag,
                a.saved_flag,
                a.sys_notif_role,
                a.remarks,
                a.doc_name,
                e.role_name,
                c.status_code as workflow_status_code,
                c.core_workflow_id,
                b.core_workflow_stage_id,
                CAST($actor as CHAR) as actor_name_str,
                a.additional_flag,
                if(a.end_date is not null and a.end_date != '0000-00-00 00:00:00', a.end_date, if(a.start_date is not null and a.start_date != '0000-00-00 00:00:00', a.start_date, 'N/A' )) modified_date
            FROM
                $this->tbl_pria_tasks a
            JOIN
                $this->tbl_pria_workflow_stages b ON a.pria_stage_id = b.pria_stage_id
            JOIN
                $this->tbl_pria_workflows c ON b.pria_workflow_id = c.pria_workflow_id
            JOIN
                $this->tbl_pria_task_roles d ON a.pria_task_id = d.pria_task_id AND d.actor_flag = {$this->initial_yes}
            JOIN
                $this->tbl_core_roles e ON d.role_code = e.role_code
            JOIN
                $this->tbl_pria_task_forms g ON a.pria_task_id = g.pria_task_id
            LEFT JOIN
                $this->tbl_pria_task_actions f ON a.pria_task_id = f.pria_task_id AND a.task_status_id = f.pria_task_action_id
            LEFT JOIN
                $this->tbl_core_user_roles h ON h.user_id = a.user_id
            LEFT JOIN
                $this->tbl_core_roles h1 ON h.role_code = h1.role_code
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
        {  //villarojo
            $query      =<<<EOS
                SELECT
                    a.pria_task_id,
                    b.task_status_id,
                    c.controller,
                    b.task_name ,
                    b.core_workflow_task_id,
                    e.reference_id pria_workflow_reference_id,
                    b.get_flag,
                    e.org_code,
                    e.vendor_code,
                    e.pria_workflow_id
                FROM
                    $this->tbl_pria_task_predecessors a
                JOIN
                    $this->tbl_pria_tasks b ON a.pria_task_id = b.pria_task_id
                JOIN
                    $this->tbl_pria_task_forms c ON a.pria_task_id     = c.pria_task_id
                JOIN
                    $this->tbl_pria_workflow_stages d ON d.pria_stage_id = b.pria_stage_id
                JOIN
                    $this->tbl_pria_workflows e ON e.pria_workflow_id = d.pria_workflow_id
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
          //  print_var_export($query, [$core_workflow_id, $core_workflow_stage_id, $pria_workflow_id]);
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
                    b.task_name,
                    AGDEC(b.actor_name) as actor_name
                FROM
                    $this->tbl_pria_task_return a
                JOIN
                    $this->tbl_pria_tasks b ON a.ret_pria_task_id = b.pria_task_id
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
    public function get_task_comments_details($pria_stage_id)
    {
        try
        {
          /*   $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
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
EOS; */
            $fullname   = " CONCAT (".
                        aes_crypt('d.fname', FALSE, FALSE)." ,'  ',".
                        "IFNULL(".aes_crypt('d.mname', FALSE, FALSE).",'') ,'  ',".
                        "IFNULL(".aes_crypt('d.lname', FALSE, FALSE).",''),'  ',".
                        "IFNULL(".aes_crypt('d.ext_name', FALSE, FALSE).",''))  as commented_by";

            $query      =<<<EOS
            SELECT
                d.photo,
                a.created_by,
                a.created_date,
                a.pria_task_comment,
                a.pria_task_comment_id,
                $fullname
            FROM
                $this->tbl_pria_task_comments  a
            JOIN
                $this->tbl_pria_tasks b ON a.pria_task_id = b.pria_task_id
            JOIN
                $this->tbl_pria_workflow_stages c  ON b.pria_stage_id = c.pria_stage_id
            JOIN
                $this->tbl_core_users d ON a.created_by = d.user_id
            WHERE
                c.pria_stage_id = ?
            ORDER BY
                a.created_date DESC
EOS;
           // print_var_export($query, [$pria_stage_id]);
            return $this->query($query, [$pria_stage_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /**
     * @Author: Christian Aquino
     * @Date: 2019-06-21 11:17:29
     * @Desc:  Specifically made to retrieve task attachments for display inside task
     * @ReferencedBy:   Transaction_Controller.php
     */
    public function get_task_attachments_details($pria_task_id)
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
                a.file_name,
                a.sys_file_name,
                a.pria_task_attachments_id,
                $fullname
            FROM
                $this->tbl_pria_task_attachments a
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

     /**
     * @Author: Christian Aquino
     * @Date: 2019-06-21 11:17:29
     * @Desc:  Specifically made to retrieve task documents for display inside task
     * @ReferencedBy:   Transaction_Controller.php
     */
    //public function get_document_details($pria_task_id)
    public function get_document_details($pria_stage_id)
    {
        try
        {
            $fullname   = " CONCAT (".
                          aes_crypt('b.fname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.mname', FALSE, FALSE).",'') ,'  ',".
                          //aes_crypt('b.lname', FALSE, FALSE)." ,'  ',".
                          "IFNULL(".aes_crypt('b.lname', FALSE, FALSE).",''),'  ',".
                          "IFNULL(".aes_crypt('b.ext_name', FALSE, FALSE).",''))  as commented_by";
//             $query      =<<<EOS
//             SELECT
//                 a.document_type_code,
//                 a.reference,
//                 a.created_by,
//                 a.created_date,
//                 a.file_name,
//                 a.sys_file_name,
//                 a.document_id,
//                 $fullname
//             FROM
//                 $this->tbl_documents a
//             JOIN
//                 $this->tbl_core_users b ON a.created_by = b.user_id
//             WHERE
//                 a.reference = ?
//             ORDER BY
//                 a.created_date DESC
// EOS;

            $query      =<<<EOS
            SELECT
                a.document_type_code,
                a.reference,
                a.created_by,
                a.created_date,
                a.modified_by,
                a.modified_date,
                a.file_name,
                a.sys_file_name,
                a.document_id,
                a.version,
                a.initial_upload,
                $fullname
            FROM
                $this->tbl_documents a
            JOIN
                $this->tbl_core_users b ON a.created_by = b.user_id
            JOIN
                $this->tbl_pria_tasks c ON c.pria_task_id = a.reference
            JOIN
                $this->tbl_pria_workflow_stages d  ON c.pria_stage_id = d.pria_stage_id
            WHERE
                d.pria_stage_id = ? AND a.document_type_code = ?
            ORDER BY
                a.created_date DESC
EOS;
            return $this->query($query, [$pria_stage_id, DOC_TYPE_TASK_ATTACHMENT], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_module_account_group(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_module_account_group, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-06-29 13:40:21
     * @Desc:
     * @ReferencedBy:  Task_Controller.php
     */
    public function get_pria_task_documents($pria_task_id, $reference_id)
    {
        try
        {
            $query=<<<EOS
                SELECT
                    b.document_id,
                    b.reference,
                    a.document_type_code,
                    b.file_name,
                    b.sys_file_name,
                    b.version,
                    b.created_by,
                    b.created_date,
                    b.modified_by,
                    b.modified_date,
                    a.access,
                    c.document_type_name,
                    d.core_workflow_task_id,
                    d.user_id
                FROM
                    $this->tbl_pria_task_document_types a
                JOIN
                    $this->tbl_param_document_types c ON a.document_type_code =  c.document_type_code
                JOIN
                    $this->tbl_pria_tasks d ON a.pria_task_id = d.pria_task_id
                JOIN
                    pria_workflow_stages pws ON d.pria_stage_id = pws.pria_stage_id
                LEFT JOIN
                    $this->tbl_pria_documents b ON  a.document_type_code = b.document_type_code  AND b.reference = ? AND IF(pws.core_workflow_stage_id = 39 OR pws.core_workflow_stage_id = 22, d.pria_stage_id = b.pria_stage_id, 1=1)
                WHERE
                    a.pria_task_id = ?

EOS;

            return $this->query($query, [$reference_id, $pria_task_id], TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_task_doc_type($task_id)
    {

        try
        {
            $query      =<<<EOS
            SELECT
                *
            FROM
                $this->tbl_pria_tasks a
            JOIN
                $this->tbl_pria_workflow_task_doc_type b

                ON a.core_workflow_task_id = b.core_workflow_task_id
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

    public function get_task_workflow($task_id)
    {

        try
        {
            $query      =<<<EOS
            SELECT
                *
            FROM

                $this->tbl_pria_tasks a
            JOIN
                $this->tbl_core_tasks b
                ON a.core_workflow_task_id = b.workflow_task_id
            JOIN
                $this->tbl_core_workflow_stages c
                ON b.workflow_stage_id = c.workflow_stage_id

            WHERE a.pria_task_id = ?
EOS;

            return $this->query($query, [$task_id], TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

	   /**
     * @Author: Kevin Villarojo
     * @Date: 2019-07-05 16:39:31
     * @Desc:
     * @ReferencedBy:  Upload_project_completion
     */

    public function get_pria_task_document_types($task_id)
    {

        try
        {
            $query      =<<<EOS
            SELECT
                b.*
            FROM
                $this->tbl_pria_tasks a
            JOIN
                $this->tbl_pria_task_document_types b ON a.pria_task_id = b.pria_task_id
            WHERE
                a.pria_task_id = ?
EOS;

            return $this->query($query, [$task_id], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    public function get_workflow_task_updates(array $where, array $fields=['*'], array $order=[])
    {
        try
        {
            return $this->select_data($fields, $this->tbl_workflow_task_updates, TRUE, $where, $order);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /**
     * @Author: Kevin Villarojo
     * @Date: 2019-07-23 16:00:02
     * @Desc:
     * @ReferencedBy:  Task.php
     */
    public function get_stages_with_appendable($pria_workflow_id)
    {
        try
        {
            $query=<<<EOS
            SELECT
                c.pria_stage_id,
                e.core_workflow_id,
                e.pria_workflow_id,
                c.core_workflow_stage_id,
                a.core_workflow_id as appendable_workflow_id,
                d.workflow_name as appendable_workflow_name,
                b.core_workflow_task_id,
                b.reference as task_reference_id,
                e.reference_id as workflow_reference_id,
                b.pria_task_id
            FROM
                $this->tbl_pria_task_appendable a
            JOIN
                $this->tbl_pria_tasks b ON b.pria_task_id = a.pria_task_id
            JOIN
                $this->tbl_pria_workflow_stages c ON c.pria_stage_id = b.pria_stage_id
            JOIN
                $this->tbl_core_workflow d  ON d.workflow_id = a.core_workflow_id
            JOIN
                $this->tbl_pria_workflows e ON c.pria_workflow_id = e.pria_workflow_id
            WHERE
                c.pria_workflow_id = ?
EOS;

            return $this->select_query_group($query, [$pria_workflow_id]);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }



    public function get_vendor_users_by_vendor_code_n_status($vendor_code, $status=STATUS_ACTIVE)
	{
		try
		{
			$tbl_users	        = self::CORE_TABLE_USERS;
			/* $tbl_user_roles     = self::CORE_USER_ROLES;
            $tbl_user_orgs      = self::PORTAL_TABLE_USER_ORGS; */
            $tbl_vendor_users   = self::PORTAL_TABLE_VENDOR_USERS;

			/* $roles_codes_str    = implode('\',\'', $role_codes);
 */
            $query 	 = <<<EOS
                SELECT
                    a.user_id
                FROM
                    $tbl_vendor_users a
                JOIN
                    $tbl_users b ON a.user_id = b.user_id
                WHERE
                    a.vendor_code = ?
                AND
                    b.status 	= ?
                GROUP BY
                    a.user_id
EOS;

            return $this->query($query, [$vendor_code, $status], TRUE, TRUE);

			/* $query 	 = <<<EOS
			SELECT
				a.user_id
			FROM
				$tbl_user_roles a
			JOIN
				$tbl_user_orgs b ON a.user_id = b.user_id
			JOIN
				$tbl_users c ON a.user_id = c.user_id
			WHERE
				a.role_code IN ('%s')
			AND
				c.status 	= ?
			AND
                b.org_code  = ?
            GROUP BY
                a.user_id
EOS;

			$query = sprintf($query, $roles_codes_str);

			return $this->query($query, [$status, $org_code], TRUE, TRUE); */
		}
		catch(PDOException $e)
		{
			throw $e;
		}
    }

    public function get_users_by_roles_n_org($role_codes=[], $org_codes=[], $status=STATUS_ACTIVE)
	{
		try
		{
			$tbl_users 		= self::CORE_TABLE_USERS;
			$tbl_user_roles = self::CORE_USER_ROLES;
			$tbl_user_orgs  = self::PORTAL_TABLE_USER_ORGS;

			$roles_codes_str = implode('\',\'', $role_codes);
			$org_codes_str   = implode('\',\'', $org_codes);

			$query 	 = <<<EOS
			SELECT
				a.user_id
			FROM
				$tbl_user_roles a
			JOIN
				$tbl_user_orgs b ON a.user_id = b.user_id
			JOIN
				$tbl_users c ON a.user_id = c.user_id
			WHERE
				a.role_code IN ('%s')
			AND
				c.status 	= ?
			AND
                b.org_code IN ('%s')
            GROUP BY
                a.user_id
EOS;

			$query = sprintf($query, $roles_codes_str, $org_codes_str);
            //print_var_export($query, $status); die;
			return $this->query($query, [$status], TRUE, TRUE);
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

    public function insert_task_role($data)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_task_roles, $data, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** UPDATE FUNCTIONS */
    public function update_tasks_updates($table, $fields, $where)
    {
        try
        {
            $this->update_data($table, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_task_email_link($where, $fields)
	{
		return $this->update_data($this->tbl_pria_task_email_links, $fields, $where);
    }

    /** DELETE FUNCTIONS */
    public function delete_task_role($where_arr)
    {
       try
       {
         $this->delete_data($this->tbl_pria_task_roles, $where_arr);
       }
       catch(PDOException $e)
       {
           throw $e;
       }
    }

    public function get_appendable_ongoing($pria_task_id, $core_task_ids)
    {
        try
        {
            $task_where = $task_q_mark = "";

            $values = array($pria_task_id);

            if(is_array($core_task_ids) AND count($core_task_ids) > 0)
            {
                foreach($core_task_ids AS $key => $core_task_id)
                {
                    $task_q_mark    .= (!EMPTY($task_q_mark))? ", ?": "?";
                    $values[]       = $core_task_id;
                }

                $task_where         = "AND D.core_workflow_task_id IN ($task_q_mark) AND (D.task_status_id IS NULL OR D.task_status_id = ?)";
                $values[]           = TASK_STATUS_ONGOING;
            }

            $query  = <<<EOS
                SELECT
                    D.pria_task_id
                FROM $this->tbl_pria_tasks A
                JOIN $this->tbl_pria_workflow_stages B ON A.pria_stage_id = B.pria_stage_id
                JOIN $this->tbl_pria_workflow_stages C ON B.pria_workflow_id = C.pria_workflow_id
                JOIN $this->tbl_pria_tasks D ON C.pria_stage_id = D.pria_stage_id
                WHERE A.pria_task_id = ? $task_where
EOS;
            return $this->query($query, $values, TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_from_table($fields_arr, $table, $multiple, $where_arr = array(), $order_arr = array(), $group_arr = array(), $limit = '')
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
}