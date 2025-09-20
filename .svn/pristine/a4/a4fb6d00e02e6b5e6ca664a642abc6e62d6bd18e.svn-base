<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vendors_model extends SYSAD_Model 
{
	
	private $vendors;
	private $vendor_users;
	private $vendor_business_centers;
	
	public function __construct()
	{	
		parent::__construct();
		
		$this->vendors 					= parent::PORTAL_TABLE_VENDORS;
		$this->vendor_users 			= parent::PORTAL_TABLE_VENDOR_USERS;
		$this->vendor_business_centers 	= parent::PORTAL_TABLE_VENDOR_BUSINESS_CENTERS;
	}
    
	public function get_vendor_details()
	{
		$result 		= array();

		try
		{
			$fields 	= array("*");
			$where 		= array("deleted_flag" => MAINTAINER_NO);
				
			$result 	= $this->select_data($fields, $this->vendors, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}	

	public function get_vendor_select()
	{
		$result 		= array();

		try
		{
			$fields 	= array("vendor_name", "vendor_code");
			$where 		= array("deleted_flag" => MAINTAINER_NO);
				
			$result 	= $this->select_data($fields, $this->vendors, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}	

	public function get_vendors($where, $fields=['*'])
	{
		$result 		= array();

		try
		{
			//$fields 	= array("*");
			// $where 		= array("deleted_flag" => MAINTAINER_NO);
				
			$result 	= $this->select_data($fields, $this->vendors, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}

	public function get_vendor_business_centers($vendor)
	{
		$result 		= array();

		try
		{
			$fields 	= array("org_code", "vendor_code");
			$where 		= array("vendor_code" => $vendor);
				
			$result 	= $this->select_data($fields, $this->vendors, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}	

	public function delete_vendor_users( array $where )
	{
		try
		{

			$this->delete_data( $this->vendor_users, $where );
		}
		catch( PDOException $e )
		{
			throw $e;
		}
	}

	public function insert_vendor_users( array $val )
	{
		try
		{
			$this->insert_data( $this->vendor_users, $val );
		}
		catch( PDOException $e )
		{
			throw $e;
		}
	}
}
