<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_comment_model extends Portal_Model
{
    private $tbl_pria_tasks;
    private $tbl_core_users;
    private $tbl_pria_task_comments;
    
	public function __construct()
	{
		parent::__construct();

        $this->tbl_pria_tasks             = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_core_users             = Portal_Model::CORE_TABLE_USERS;
        $this->tbl_pria_task_comments     = Portal_Model::PORTAL_TABLE_PRIA_TASK_COMMENTS;
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
     * @ReferencedBy:   Task_Controller.php
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
                $fullname,
                c.task_name,
                c.task_status_id,
                c.pria_task_id
            FROM   
                $this->tbl_pria_task_comments a
            JOIN   
                $this->tbl_core_users b ON a.created_by   = b.user_id
            JOIN
                $this->tbl_pria_tasks c ON a.pria_task_id = c.pria_task_id
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
     * @ReferencedBy:   Task_Controller.php
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

    /** UPDATE FUNCTIONS */
    public function update_task_comment(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_pria_task_comments, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** DELETE FUNCTIONS */
    public function delete_task_comment($where)
    {
        try
        {
            $this->delete_data($this->tbl_pria_task_comments, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

}