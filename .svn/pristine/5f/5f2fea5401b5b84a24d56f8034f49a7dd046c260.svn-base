<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_overview_model extends Portal_Model {
	
	private $core_tbl_users					= Portal_Model::CORE_TABLE_USERS;
	private $core_tbl_workflow          	= Portal_Model::CORE_WORKFLOWS;
	private $tbl_overview 				    = Portal_Model::PORTAL_TABLE_PRIA_OVERVIEW;

	
	public function __construct()
	{
		parent::__construct();
	}


	/** INSERT FUNCTIONS */
	public function insert_overview($ag_code, $data)
	{
		try
		{
			return $this->insert_data($this->tbl_overview, $data, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** DELETE FUNCTIONS */
	public function delete_overview($where)
	{
		try
		{
			$this->delete_data($this->tbl_overview, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}


}