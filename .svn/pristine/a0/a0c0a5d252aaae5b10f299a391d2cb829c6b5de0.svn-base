<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Models::pria_cron_model
 * @author Asiagate Networks, Inc
 * @email support@asiagate.com
 *
 * Pria Cron Jobs 
 */

class Pria_cron_model extends Portal_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function delete_temp($table_name)
	{
		try
		{	$where = array("DATE(created_date)" => array("<" => date('Y-m-d')));

			return $this->delete_data($table_name, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


	//Check all renewal contracts
	public function check_contracts($cron_months)
	{
		try
		{
			$contracts 		= parent::PORTAL_TABLE_CONTRACTS;
			$val 			= array(CONTRACT_NEW, CONTRACT_EXPIRED);

			$query = <<<EOS
				SELECT * FROM
					$contracts
				WHERE expiration_date < date_sub(now(), interval -$cron_months month)
				AND contract_status_code = ? OR contract_status_code = ?
EOS;
			return $this->query($query, $val);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_all_contracts_for_update()
	{
		try
		{
			$contracts 		= parent::PORTAL_TABLE_CONTRACTS;
			$status 		= implode('\',\'', [CONTRACT_RENEWED, CONTRACT_EXPIRED, CONTRACT_FOR_RENEWAL]);
			$query  		=<<<EOS
				SELECT * 
				FROM $contracts
				WHERE saved_flag = 1 AND contract_status_code NOT IN ('%s')
EOS;
			$query  = sprintf($query, $status);	

			return $this->query($query);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function update_contract_status(array $where, array $fields)
    {
        try
        {	
        	$contracts 	= parent::PORTAL_TABLE_CONTRACTS;

            $this->update_data($contracts, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    //Check all due soas
	public function check_soas()
	{
		try
		{
            $val = array();
			$soa 		= parent::PORTAL_TABLE_SOA;
			$cron_days 	= CRON_SOA;
			// $val 	= array(CONTRACT_NEW, CONTRACT_EXPIRED);

			$query = <<<EOS
				SELECT *
					FROM $soa
				WHERE CURDATE() > ( soa_date + INTERVAL $cron_days DAY )
EOS;
			return $this->query($query, $val);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_soa_task_details($soa_num = NULL)
	{
		try
		{	
			$val = array($soa_num, TASK_STATUS_PENDING, TASK_STATUS_ONGOING, NO_FLAG);

			$soa 					= parent::PORTAL_TABLE_SOA;
			$pria_workflows 		= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
			$pria_workflow_stages 	= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
			$pria_tasks 			= parent::PORTAL_TABLE_PRIA_TASKS;
			
			// SELECT 
			// 		d.pria_task_id
			// 	FROM $soa a
			// 	JOIN $pria_workflows b ON a.soa_num = b.reference_num
			// 	JOIN $pria_workflow_stages c ON b.pria_workflow_id = c.pria_workflow_id
			// 	JOIN $pria_tasks d ON c.pria_stage_id = d.pria_stage_id
			// 	WHERE a.soa_num = ? and (d.task_status_id = ? OR d.task_status_id = ?)

			$query = <<<EOS
				SELECT
					d.pria_task_id
				FROM $soa a
				JOIN $pria_workflows b ON a.soa_num = b.reference_num
				JOIN $pria_workflow_stages c ON b.pria_workflow_id = c.pria_workflow_id
				JOIN $pria_tasks d ON c.pria_stage_id = d.pria_stage_id
				WHERE a.soa_num = ? AND d.task_status_id = ? and d.task_status_id = ? and d.apv_sent_flag = ?
EOS;
			
			return $this->query($query, $val);

		}catch(PDOException $e){
			throw $e;
		}
	}

	public function update_soa_task(array $where, array $fields)
    {
        try
        {	
        	$pria_tasks 	= parent::PORTAL_TABLE_PRIA_TASKS;

            $this->update_data($pria_tasks, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /**
     * Get Pending FHR with completed Clean up report 
     */

    public function get_pending_fhrs()
    {
    	try{
    		
    		$pria_tasks 			= parent::PORTAL_TABLE_PRIA_TASKS;
    		$pria_task_predecessors = parent::PORTAL_TABLE_PRIA_TASK_PREDECESSORS;
    		$pria_workflow_stages 	= parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES;
    		$pria_workflows 		= parent::PORTAL_TABLE_PRIA_WORKFLOWS;
    		$internal_orders 		= parent::PORTAL_TABLE_INTERNAL_ORDERS;

    		$approve_clean_up 	= CORE_TASK_APPROVE_CLEANUP;
    		$task_approved 		= TASK_STATUS_APPROVED; 

			$query = <<<EOS
				SELECT * 
					FROM $pria_tasks a
				JOIN $pria_task_predecessors b ON a.pria_task_id = b.pre_pria_task_id
				JOIN $pria_tasks c ON b.pria_task_id = c.pria_task_id
				JOIN $pria_workflow_stages d ON c.pria_stage_id = d.pria_stage_id
				JOIN $pria_workflows e ON d.pria_workflow_id = e.pria_workflow_id
				JOIN $internal_orders f ON e.reference_id = f.io_id AND e.reference_num = io_num
				WHERE a.core_workflow_task_id = ? AND a.task_status_id = ? AND c.task_status_id IS NULL
EOS;
			return $this->query($query, array($approve_clean_up, $task_approved));
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
    }


    public function get_cron_io_det($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {   
            return $this->select_data($fields, parent::PORTAL_TABLE_INTERNAL_ORDERS, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_vendor_approval_soas()
    {
    	try
    	{
    		$values	= array(TASK_STATUS_DONE, TASK_STATUS_APPROVED, CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED, CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL, CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER, TASK_STATUS_ONGOING, CRON_SOA);

    		$query	= "
				SELECT
					A.pria_task_id, F.reference_id
				FROM " . parent::PORTAL_TABLE_PRIA_TASKS . " A
				JOIN " . parent::PORTAL_TABLE_PRIA_WORKFLOW_STAGES . " E ON A.pria_stage_id = E.pria_stage_id
				JOIN " . parent::PORTAL_TABLE_PRIA_WORKFLOWS . " F ON E.pria_workflow_id = F.pria_workflow_id
				JOIN " . parent::PORTAL_TABLE_PRIA_TASK_PREDECESSORS . " B ON A.pria_task_id = B.pria_task_id
				JOIN " . parent::PORTAL_TABLE_PRIA_TASKS . " C ON A.pria_stage_id = C.pria_stage_id
				AND B.pre_pria_task_id = C.pria_task_id AND C.task_status_id IN (?, ?)
				WHERE A.core_workflow_task_id IN (?, ? ,?) AND (A.task_status_id IS NULL OR A.task_status_id IN (?))
				AND DATE_ADD(DATE(C.actual_end_date), INTERVAL ? DAY) <= CURDATE()
";
			return $this->query($query, $values);
    	}
    	catch(PDOException $e)
    	{
    		throw $e;
    	}
    }
}



/* End of file Pria_cron_model.php */
/*/application/models/Pria_cron_model.php*/