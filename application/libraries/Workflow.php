<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workflow {
	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('workflow_model');
	}
	
	// use this to check if the button for processing the approval is available on a specific role and return an array of process info
	public function check_process($process_id, $user_roles, $db, $table, $where){
	
		try
		{
			// check if currently logged in user has the corresponding role to do the process
			$roles = array_intersect($user_roles, $this->CI->session->user_roles);
			
			// check if a workflow process exists with the assigned role
			$process = $this->CI->workflow_model->check_process($process_id, $user_roles, $db, $table, $where);
			
			$process["valid"] = ($process["valid"] AND !EMPTY($roles))? TRUE : FALSE;
			
			return $process;
		}
		catch(PDOException $e)
		{			
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	// this will update the status and insert the id of the user doing the process
	public function do_process($process_id, $user_roles, $db, $table, $val, $where){
	
		try
		{
			return $this->CI->workflow_model->do_process($process_id, $user_roles, $db, $table, $val, $where);
		}
		catch(PDOException $e)
		{			
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}	
	}
	
	// usage sample
	/*
	* role_code in core.roles table should be equivalent to the field (in lowercase) in a specific table.
	* ex: role_code in core.roles is PLANNING_OFFICER, existing field in table aip should be named planning_officer, this field is where the user_id of the one who processed the data will be inserted
	* $process_id - ex: AIP_APPROVAL is a constant having the value of the process_id in core.process table
	* $user_roles - role_code of the current user doing the approval process
	* $db - schema name where the table exists
	* $table - table where the user_id and status of the process will be updated
	* $val - fields that needs to be updated (ex: $val = array("planning_officer" => $this->session->user_id); )
	* $where - condition used for updating the specified table (ex: $where = array("aip_id" => $this->session->aip_id, "location_code" => $this->session->location_code, "cy" => $this->session->budget_year); )
	
	$this->workflow->do_process(AIP_APPROVAL, $this->session->user_roles, BUDGET_DB, 'aip', $val, $where);
	
	*/
	
}