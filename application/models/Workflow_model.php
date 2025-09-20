<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workflow_model extends SYSAD_Model {
	
	private $process;
	private $process_stages;
	private $process_steps;
	private $process_actions;
	private $process_stage_roles;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->process = parent::CORE_TABLE_PROCESS;
		$this->process_stages = parent::CORE_TABLE_PROCESS_STAGES;
		$this->process_steps = parent::CORE_TABLE_PROCESS_STEPS;
		$this->process_actions = parent::CORE_TABLE_PROCESS_ACTIONS;
		$this->process_stage_roles = parent::CORE_TABLE_PROCESS_STAGE_ROLES;
	}
    
	// check if current step is the same as the available step
	public function check_process($process_id, $user_roles, $db, $table, $where)
	{
		try
		{
			$info = array();
			$fields = array();
			
			$current_step = $this->_check_current_step($db, $table, $where);
			$process_step = $this->_get_process_stage_step($process_id, $user_roles);
			
			$info["valid"] = ($current_step === $process_step[0]["process_step_id"])? TRUE : FALSE;
			
			if(count($process_step) > 1){
				$info["actions"] = $process_step;
			} else {
				$info["tooltip"] = $process_step[0]["name"];
				$info["is_return"] = $process_step[0]["is_return"];
				
				foreach ($user_roles as $role):
					$fields[] = $role;
				endforeach;
			
				$info["proceeding_step"] = ($this->_check_table($db, $table, $fields, $where))? $process_step[0]["proceeding_step"] : $process_step[0]["process_step_id"];
			}	
			
			return $info;
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
	
	// execute the process workflow
	public function do_process($process_id, $user_roles, $db, $table, $val, $where)
	{
		try
		{
			$done_parallel = TRUE; // initial value is true so that it will proceed even the process is not parallel
			$notify = FALSE; // notify only if a stage process has been successfully completed
			$proceeding_step = $val["proceeding_step"];
			$is_return = $val["is_return"];
			$result = array();
			
			// remove elements in $val array that is not needed for updating/does not exists in the module table
			unset($val["proceeding_step"]);
			unset($val["is_return"]);
			
			$process = $this->_get_process($process_id, $proceeding_step, $is_return);
			
			// get roles involed
			$fields = explode(",", strtolower($process["roles"]));
			
			// if process is return
			if($is_return){
				// special case, roles involved in mid parallel return
				$arr = (!EMPTY($process["arr"]))? explode(",", strtolower($process["arr"])) : array();
				
				$fields = array_merge($fields, $arr);
				
				$val = array("status_id" => $process["status_id"], "step_id" => $proceeding_step);
				foreach ($fields as $role):
					$val[$role] = 0;
				endforeach;
				
				$this->_update_table($db, $table, $val, $where);
				
				$roles = $process["roles"];
				$notify = TRUE;
			} else {
				$val["status_id"] = $process["status_id"];
				$this->_update_table($db, $table, $val, $where);
				
				// count roles involved, if more than 1 it means a parallel process is in effect
				if(count($fields) > 1)
					$done_parallel = $this->_check_table($db, $table, $fields, $where);
				
				// if parallel process is done that is the only time the status will be updated
				if($done_parallel){
					$val = array("step_id" => $process["proceeding_step"]);
					$this->_update_table($db, $table, $val, $where);
					$roles = $process["next_roles"];
					$notify = TRUE;
				}
			}
			
			$result["msg"] = $process["message"];
			$result["roles"] = $roles;
			$result["notify"] = $notify;
			return $result;
				
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
	
	// Get current step of the process
	private function _check_current_step($db, $table, $where)
	{
		try
		{
			$result = $this->select_data(array("step_id"), $db.".".$table, FALSE, $where);
			
			return $result["step_id"];
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
	
	// Get available process stage and step depending on the role of the user logged in.
	private function _get_process_stage_step($process_id, $user_roles)
	{
		try
		{
			$val = array($process_id, $process_id);
			$str = " AND D.role_code IN (";
			foreach ($user_roles as $role):
				$str .= "?, ";
				$val[] = $role;
			endforeach;
			$str = rtrim($str, ', ').")";
			
			$query = <<<EOS
				SELECT C.process_stage_id, A.process_step_id, B.name, B.proceeding_step, B.is_return,
				IF((B.is_return = 1), (SELECT status_id FROM $this->process_steps WHERE process_step_id = B.proceeding_step AND process_id = ?), A.status_id) status_id
				FROM $this->process_steps A JOIN $this->process_actions B ON A.process_step_id = B.process_step_id AND A.process_id = B.process_id
				JOIN $this->process_stages C ON A.process_stage_id = C.process_stage_id AND A.process_id = C.process_id
				JOIN $this->process_stage_roles D ON C.process_stage_id = D.process_stage_id AND A.process_id = D.process_id
				WHERE A.process_id = ? $str
EOS;

			$stmt = $this->query($query, $val);
			
			return $stmt;
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
	
	// get all the details of the available process
	private function _get_process($process_id, $proceeding_step, $is_return)
	{
		try
		{
			$val = array();
			$fields_arr = array("E.process_action_id", "E.name action", "E.proceeding_step", "E.message", "E.is_return");
			$clear_roles = "";
			$next_role = "";
			
			if($is_return){
				$fields_arr = array();
				$val = array($proceeding_step, $process_id, $proceeding_step, $process_id, $proceeding_step, $process_id, $proceeding_step, $process_id, $proceeding_step, $process_id);
				$fields_arr[] = "(SELECT process_action_id FROM $this->process_actions WHERE proceeding_step = ? AND process_id = ? AND is_return = 1) process_action_id";
				$fields_arr[] = "(SELECT name FROM $this->process_actions WHERE proceeding_step = ? AND process_id = ? AND is_return = 1) action";
				$fields_arr[] = "(SELECT proceeding_step action FROM $this->process_actions WHERE proceeding_step = ? AND process_id = ? AND is_return = 1) proceeding_step";
				$fields_arr[] = "(SELECT message FROM $this->process_actions WHERE proceeding_step = ? AND process_id = ? AND is_return = 1) message";
				$fields_arr[] = "(SELECT is_return FROM $this->process_actions WHERE proceeding_step = ? AND process_id = ? AND is_return = 1) is_return";
				
				$clear_roles = ", (SELECT GROUP_CONCAT(DISTINCT role_code) FROM $this->process_stage_roles A1 JOIN $this->process_steps B1 ON A1.process_stage_id = B1.process_stage_id AND A1.process_id = B1.process_id WHERE A1.process_stage_id != B.process_stage_id AND A1.process_id = ? AND B1.process_step_id = E.proceeding_step) arr";
				$val[] = $process_id;
			} else {
				$next_role = ", (SELECT GROUP_CONCAT(DISTINCT A1.role_code) FROM $this->process_stage_roles A1 JOIN $this->process_steps B1 ON A1.process_stage_id = B1.process_stage_id AND A1.process_id = B1.process_id WHERE IF(ISNULL(E.proceeding_step), 1, B1.process_step_id = E.proceeding_step) AND A1.process_id = ?) next_roles";
				$val[] = $process_id;
			}
			
			$fields = implode(",", $fields_arr);
			
			$val[] = $process_id;
			$val[] = $proceeding_step;
			
			$query = <<<EOS
				SELECT A.process_id, A.description, B.process_stage_id, B.name stage, GROUP_CONCAT(DISTINCT(C.role_code)) roles, 
				D.process_step_id, D.name step, D.status_id, $fields $clear_roles $next_role
				FROM $this->process A JOIN $this->process_stages B ON A.process_id = B.process_id 
				JOIN $this->process_stage_roles C ON B.process_stage_id = C.process_stage_id AND A.process_id = C.process_id
				JOIN $this->process_steps D ON B.process_stage_id = D.process_stage_id AND A.process_id = D.process_id
				JOIN $this->process_actions E ON D.process_step_id = E.process_step_id AND A.process_id = E.process_id
				WHERE A.process_id = ? AND E.process_step_id = ?
EOS;

			$stmt = $this->query($query, $val, TRUE, FALSE);
			
			return $stmt;
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
	
	// this will be used by the parallel process to check if all proper conditions were met before proceeding to the next process step
	private function _check_table($db, $table, $fields, $where)
	{
		try
		{
			$users = array();
			
			$result = $this->select_data($fields, $db.".".$table, FALSE, $where);
			
			foreach ($fields as $field):
				$users[] = $result[$field];
			endforeach;
			
			return (in_array('0', $users, true))? 0 : 1;
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
	
	// update the necessary fields
	private function _update_table($db, $table, $val, $where)
	{
		try
		{
			$this->update_data($db.".".$table, $val, $where);
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
	
}
