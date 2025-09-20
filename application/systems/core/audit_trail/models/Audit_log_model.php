<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_log_model extends SYSAD_Model {
	
	private $audit_trail;
	private $audit_trail_detail;
	private $users;
	private $modules;
                
	public function __construct()
	{
		parent::__construct();
		
		$this->audit_trail 			= parent::CORE_TABLE_AUDIT_TRAIL;
		$this->audit_trail_detail 	= parent::CORE_TABLE_AUDIT_TRAIL_DETAIL;
		$this->users 				= parent::CORE_TABLE_USERS;
		$this->modules 				= parent::CORE_TABLE_MODULES;
	}			
	
	public function get_audit_log($audit_log_id)
	{
		$result 	= array();

		try
		{
			$query = <<<EOS
				SELECT A.*, DATE_FORMAT(activity_date,'%m/%d/%Y %r') activity_date, CONCAT(B.fname, ' ', B.lname) name, C.module_name
				FROM $this->audit_trail A, $this->users B, $this->modules C
				WHERE A.user_id = B.user_id
				AND A.module_code = C.module_code
				AND A.audit_trail_id = ?
EOS;
			$stmt 	= $this->query($query, array($audit_log_id), TRUE, FALSE);
			
			$result = $stmt;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		

		return $result;
	}	
	
	public function get_audit_log_details($audit_log_id)
	{
		$result 	= array();

		try
		{
			$fields = array("*");
			$where 	= array("audit_trail_id" => $audit_log_id);
				
			$result = $this->select_data($fields, $this->audit_trail_detail, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;	
	}	
	
	public function get_audit_log_list($aColumns, $bColumns, $params)
	{
		$result 		= array();

		$add_where 		= "";
		$extra_val 		= array();

		try
		{
			$fname_w 	= "CAST( ".aes_crypt('B.fname', FALSE, FALSE)." as char(100) ) ";
			$lname_w 	= "CAST( ".aes_crypt('B.lname', FALSE, FALSE)." as char(100) ) ";

			$cColumns 	= array("CONCAT(".$fname_w.",' ',".$lname_w.") convert_to fullname", "C-module_name", "A-activity", "DATE_FORMAT(activity_date,'%m/%d/%Y %T') convert_to activity_date", "A-ip_address");
			$fields 	= str_replace(" , ", " ", implode(", ", $aColumns));
		
			$sWhere = $this->filtering($cColumns, $params, TRUE);
			$sOrder = $this->ordering($bColumns, $params);
			$sLimit = $this->paging($params);
			
			$filter_str 	= $sWhere["search_str"];
			$filter_params 	= $sWhere["search_params"];
			
			if(ISSET($params['system_code']) AND $params['system_code'] != "0"  )
			{
				$val = array($params['system_code']);
				$val = array_merge($val,$filter_params);
				$filter_sys_code 	= " AND C.system_code = ? ";
			} else {
				$filter_sys_code 	= "";
				$val 			 	= $filter_params;
			}

			if( ISSET( $params['date_from'] ) AND !EMPTY( $params['date_from'] ) 
				AND ( !ISSET( $params['date_to'] ) OR EMPTY( $params['date_to'] ) )
			)
			{
				$add_where .= " AND DATE(A.activity_date) = ? ";	

				$extra_val[]= $params['date_from'];
			}

			if( ISSET( $params['date_to'] ) AND !EMPTY( $params['date_to'] ) 
				AND ( !ISSET( $params['date_from'] ) OR EMPTY( $params['date_from'] ) )
			)
			{

				$add_where .= " AND DATE(A.activity_date) = ? ";	

				$extra_val[]= $params['date_to'];
			}

			if( 
				( ISSET( $params['date_from'] ) AND !EMPTY( $params['date_from'] ) ) AND
				( ISSET( $params['date_to'] ) AND !EMPTY( $params['date_to'] ) )
			)
			{
				$add_where .= " AND DATE(A.activity_date) BETWEEN ? AND ? ";	

				$extra_val[]= $params['date_from'];
				$extra_val[]= $params['date_to'];
			}
			
			$query = <<<EOS
				SELECT SQL_CALC_FOUND_ROWS $fields
				FROM $this->audit_trail A, $this->users B, $this->modules C
				WHERE A.user_id = B.user_id
				AND A.module_code = C.module_code
				$filter_sys_code
				$filter_str
				$add_where
	        	$sOrder
	        	$sLimit
EOS;

			$val 	= array_merge( $val, $extra_val );

			$stmt 	= $this->query($query, $val);
			
			$result = $stmt;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;	
	}
	
	public function filtered_length($aColumns, $bColumns, $params)
	{
		$result 	= array();

		try
		{
			$this->get_audit_log_list($aColumns, $bColumns, $params);
		
			$query 	= <<<EOS
				SELECT FOUND_ROWS() cnt
EOS;
			$result = $this->query($query, NULL, TRUE, FALSE);
			
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function total_length()
	{
		$result 	= array();

		try
		{
			$fields = array("COUNT(audit_trail_id) cnt");
			
			$result = $this->select_data($fields, $this->audit_trail, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
}