<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends Portal_Model
{
    private $tbl_pria_tasks;
    private $tbl_core_tasks;

	public function __construct()
	{
		parent::__construct();

        $this->tbl_pria_tasks = Portal_Model::PORTAL_TABLE_PRIA_TASKS;
        $this->tbl_core_tasks = Portal_Model::CORE_WORKFLOW_STAGE_TASKS;
     
	}

    public function get_stage_tasks(array $stages)
    {
        try
        {

            $stage_ids  = implode("','", $stages);
            $query      =<<<EOS
            SELECT
                a.pria_stage_id, a.pria_task_id, a.sequence_no, a.tat, a.user_id, a.actor_name, 
                a.version_flag, a.task_status_id, a.core_workflow_task_id, b.task_name
            FROM
                $this->tbl_pria_tasks a
            JOIN 
                $this->tbl_core_tasks b ON a.core_workflow_task_id  = b.workflow_task_id
            WHERE 
                a.pria_stage_id IN ('$stage_ids')    
EOS;
            $db		= static::get_connection();						
            $stmt	= $db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}

