<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_model extends Portal_Model{

	private $tbl_vendors;
	private $tbl_users;
	private $tbl_sites;
	private $tbl_account_groups;
	private $tbl_vendor_account_groups;
	private $tbl_organizations;
	private $tbl_business_centers;
	private $tbl_vendor_business_centers;
	private $tbl_vendor_users;
	private $tbl_user_orgs;

	public function __construct()
	{
		parent::__construct();
		$this->tbl_vendors 					= Portal_Model::PORTAL_TABLE_VENDORS;
		$this->tbl_users 					= Portal_Model::CORE_TABLE_USERS;
		$this->tbl_sites 					= Portal_Model::PORTAL_TABLE_SITES;
		$this->tbl_account_groups 			= Portal_Model::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
		$this->tbl_vendor_account_groups 	= Portal_Model::PORTAL_TABLE_VENDOR_ACCOUNT_GROUP;
		$this->tbl_cost_centers 			= Portal_Model::PORTAL_TABLE_COST_CENTERS;
		$this->tbl_organizations 			= Portal_Model::PORTAL_TABLE_ORGANIZATIONS;
		$this->tbl_business_centers 		= Portal_Model::PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS;
		$this->tbl_vendor_business_centers  = Portal_Model::PORTAL_TABLE_PRIA_PARAM_BUSINESS_CENTERS;
		$this->tbl_vendor_users  			= Portal_Model::PORTAL_TABLE_VENDOR_USERS;
		$this->tbl_user_orgs  				= Portal_Model::PORTAL_TABLE_USER_ORGS;
		$this->tbl_param_site_types			= Portal_Model::PORTAL_TABLE_PARAM_SITE_TYPES;
		$this->tbl_vendor_sites				= Portal_Model::PORTAL_TABLE_VENDOR_SITES;
	}

	public function get_vendor_by_ag_code(array $ag_code=[])
	{
		try
		{
			$ag_codes 	= implode("','", $ag_code);

			$query 		= <<<EOS
				SELECT
					a.vendor_code,
					a.vendor_name
				FROM 
					$this->tbl_vendors a
				JOIN 
					$this->tbl_vendor_account_groups b ON a.vendor_code = b.vendor_code
				WHERE
					b.account_group_code IN ('%s')
EOS;
			$query = sprintf($query, $ag_codes);

			return $this->query($query, [], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	/** 
	 * @Author: kevin villarojo 
	 * @Date: 2019-11-21 13:09:46 
	 * @Desc:  
	 * Referenced by : Sites.php
	 */	
	public function get_site_details_by_site_id($site_id)
	{
		try
		{
			$ag_codes 	= implode("','", $ag_code);

			$query 		= <<<EOS
				SELECT
					a.site_code,
					a.official_store_name,
					b.site_type_name,
					GROUP_CONCAT(d.vendor_name) as vendor_name,
					a.org_code,
					a.cost_center_code,
					GROUP_CONCAT(d.vendor_code) as vendor_codes,
					b.site_type_code
				FROM 
					$this->tbl_sites a
				JOIN 
					$this->tbl_param_site_types b ON a.site_type_code = b.site_type_code
				LEFT JOIN
					$this->tbl_vendor_sites c ON a.site_code = c.site_code
				LEFT JOIN
					$this->tbl_vendors d ON d.vendor_code = c.vendor_code
				WHERE
					a.site_id = ?
				GROUP BY
					a.site_id

EOS;
			return $this->query($query, [$site_id], TRUE, FALSE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_specific_site($where, $fields=['*'])
	{
		try
		{

			return $this->select_data($fields, $this->tbl_sites, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_site_list($params=NULL)
	{
		try
		{
			$val = $filters = array();
      		$where = $order = $limit = "";

			if($params === NULL)
			{
				$select_fields	= 'COUNT(A.site_id) total';
			}
			else
			{
				
				$select_fields = "
					SQL_CALC_FOUND_ROWS
						A.site_id,
						A.site_num,
						A.site_code,
						B.site_type_name,
						A.official_store_name,
						A.cost_center_code,
						A.created_date
				";
				
				$filters	= array(
						"A-site_code",
						"B-site_type_name",
						"A-official_store_name",
						"A-cost_center_code",
						"created_date_start" => function() use( &$params )
					    {
					      $date_from  = date_format( date_create( $params['created_date_start'] ), 'Y-m-d' );
					      $date_to  = date_format( date_create( $params['created_date_end'] ) , 'Y-m-d' );
					      $str    = " DATE(A.created_date) BETWEEN '".$date_from."' AND '".$date_to."' ";
					      
					      return array(
					          'where' => $str
					      );
					      },
					    "created_date_end" => function() use( &$params )
					    {
					      $date_to  = date_format( date_create( $params['created_date_end'] ) , 'Y-m-d' );
					      $str    = " DATE(A.created_date) <= '".$date_to."' ";
					      
					      return array(
					          'where' => $str
					      );
					    }
						);
				
				$orders	= array(
						"A.site_code",
						"B.site_type_name",
						"A.official_store_name",
						"A.cost_center_code",
						"A.created_date"
				);

				$filter	= $this->filtering($filters, $params, TRUE);
				
				$order	= $this->ordering($orders, $params);
				$limit	= $this->paging($params);

				$where	= $filter["search_str"];
				$val	= $filter["search_params"];
			}

			$this->site_completed 	= SITE_STATUS_COMPLETED;
			$this->not_deleted 		= NOT_DEL_FLAG;

			$query = <<<EOS
				SELECT
					$select_fields
				FROM 
					$this->tbl_sites A
				JOIN 
					$this->tbl_param_site_types B ON A.site_type_code = B.site_type_code
				WHERE 
					A.deleted_flag = $this->not_deleted 
				AND 
					A.status_code = '$this->site_completed'
				$where
	        	$order
	        	$limit
EOS;
			

			if(empty($params))
			{
				$total = $this->query($query, $val, TRUE, FALSE);

				return $total['total'];
			}
			else
			{
				return array(
						'records'			=> $this->query($query, $val),
						'display_records'	=> $this->_get_display_records()
				);
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function _get_display_records()
	{
		try
		{
			$query = "SELECT FOUND_ROWS() cnt";

			$count = $this->query($query, NULL, TRUE, FALSE);

			return $count['cnt'];
		}
		catch (PDOException $e)
		{
			throw $e;
		}
	}

	public function insert_site($fields)
	{

		try
		{
			return $this->insert_data($this->tbl_sites, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}
	public function update_site($fields, $where)
	{

		try
		{
			return $this->update_data($this->tbl_sites, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function delete_site($site_id)
	{

		try
		{
			
			return $this->delete_data($this->tbl_sites, array('site_id' => $site_id));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function get_sites($where = NULL)
	{
		try
		{
			$fields = array(
				'site_id',
			   	'site_num',
			   	'site_code',
				'site_type_code',
				'nomination_type_code',
				'status_code',
				'org_code',
				'ifs_code',
				'cost_center_code',
				'suggested_store_name',
				'official_store_name',
				'sn_recommendation',
				'deleted_flag',
				'created_by',
				'created_by',	
				'modified_by',
				'modified_date'
			);

			return $this->select_data($fields, $this->tbl_sites, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}