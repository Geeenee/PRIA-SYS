<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vendor_model extends Portal_Model{

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


		$this->tbl_vendor_sites 			= parent::PORTAL_TABLE_VENDOR_SITES;
		
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

	public function get_vendor_by_ag_code_n_org_code(array $ag_code=[], array $org_code=[])
	{
		try
		{
			$and 		= '';
			$ag_codes 	= implode("','", $ag_code);

			if( ! EMPTY($org_code))
			{
				$org_codes 	= implode("','", $org_code);
				
				$and 		=<<<EOS
					AND
					c.org_code IN ('$org_codes')
EOS;
			}
			
			$query 		= <<<EOS
				SELECT
					a.vendor_code,
					a.vendor_name
				FROM 
					$this->tbl_vendors a
				JOIN 
					$this->tbl_vendor_account_groups b ON a.vendor_code = b.vendor_code
				JOIN 
					$this->tbl_vendor_business_centers c ON a.vendor_code = c.vendor_code
				WHERE
					b.account_group_code IN ('%s')
				$and
				ORDER BY
					a.vendor_name ASC
EOS;
			$query = sprintf($query, $ag_codes);
			
			return $this->query($query, [], TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_specific_vendor($where)
	{
		try
		{
			$cont_fname 	= aes_crypt('cont_fname', FALSE);
			$cont_lname 	= aes_crypt('cont_lname', FALSE);
			$cont_mname 	= aes_crypt('cont_mname', FALSE);
			$cont_email 	= aes_crypt('cont_email', FALSE);
			$cont_mobile 	= aes_crypt('cont_mobile', FALSE);

			$fields = array(
				"vendor_code",
				"vendor_name",
				"vendor_type",
				"org_code",
				"description",
				"bldg_st",
				"region_code",
				"province_code",
				"muni_city_code",
				"district_code",
				"barangay_code",
				"deleted_flag",
				"created_date",
				$cont_fname,
				$cont_lname,
				$cont_mname,
				$cont_email,
				$cont_mobile,
				"modified_by",
				"modified_date"
			);


			return $this->select_data($fields, $this->tbl_vendors, $multiple=FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendor_list($params=NULL)
	{
		try
		{
			$val = $filters = array();
      		$where = $order = $group_by = $limit = "";

			if($params === NULL)
			{
				$select_fields	= 'COUNT(DISTINCT A.vendor_code) total';
			}
			else
			{
				
				$select_fields = "
					SQL_CALC_FOUND_ROWS
						A.vendor_code,
						A.vendor_name,
						GROUP_CONCAT(DISTINCT F.account_group_name SEPARATOR ', ') account_group,
						GROUP_CONCAT(DISTINCT D.name SEPARATOR ', ') business_center,
						A.description,
						A.created_date
				";
				
				$filters	= array(
						"A.vendor_code convert_to vendor_code",
						"A.vendor_name convert_to vendor_name",
						"F.account_group_name convert_to account_group",
						"D.name convert_to business_center",
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
						"vendor_code",
						"vendor_name",
						"account_group",
						"business_center",
						"A.created_date"
				);

				$filter	= $this->filtering($filters, $params, TRUE);
				
				$order	= $this->ordering($orders, $params);
				$limit	= $this->paging($params);

				$where	= $filter["search_str"];
				$val	= $filter["search_params"];

				$group_by	= "GROUP BY A.vendor_code";

			}

			$query = <<<EOS
				SELECT
					$select_fields
				FROM $this->tbl_vendors A
				LEFT JOIN $this->tbl_users B ON A.created_by = B.user_id
				LEFT JOIN $this->tbl_vendor_business_centers C ON A.vendor_code = C.vendor_code
				LEFT JOIN $this->tbl_organizations D ON C.org_code = D.org_code
				LEFT JOIN $this->tbl_vendor_account_groups E ON A.vendor_code = E.vendor_code
				LEFT JOIN $this->tbl_account_groups F ON E.account_group_code = F.account_group_code
				WHERE A.deleted_flag = 0
				$where 
				$group_by
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

	public function insert_vendor($fields)
	{

		try
		{
			return $this->insert_data($this->tbl_vendors, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}


	public function insert_vendor_sites($fields)
	{

		try
		{
			return $this->insert_data($this->tbl_vendor_sites, $fields, TRUE);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}


	public function update_vendor($fields, $where)
	{

		try
		{
			return $this->update_data($this->tbl_vendors, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function update_vendor_sites($fields, $where)
	{

		try
		{
			return $this->update_data($this->tbl_vendor_sites, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function delete_vendor_sites($where)
	{

		try
		{
			return $this->delete_data($this->tbl_vendor_sites, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}


	public function delete_vendor_users($vendor_code)
	{

		try
		{
			return $this->delete_data($this->tbl_vendor_users, array('vendor_code' => $vendor_code));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}


	public function delete_vendor($vendor_code)
	{

		try
		{
			
			return $this->delete_data($this->tbl_vendors, array('vendor_code' => $vendor_code));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function delete_vendor_account_group($vendor_code)
	{

		try
		{
			
			return $this->delete_data($this->tbl_vendor_account_groups, array('vendor_code' => $vendor_code));
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
			   	'suggested_store_name',
				'site_type_code',
				'site_id',
				'site_code',
				'official_store_name',
				'modified_date',
				'modified_by',	
				'ifs_code',
				'deleted_flag',
				'created_date',
				'created_by',
				'cost_center_code',
				'org_code'
			);

			return $this->select_data($fields, $this->tbl_sites, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendor_user(array $where=[],array $fields=['vendor_code', 'user_id'],array $order=[])
	{
		try
		{

			return $this->select_data($fields, $this->tbl_vendor_users, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_account_groups($where = NULL)
	{
		try
		{
			$fields = array(
			   	'account_group_code',
				'account_group_name',
				'core_workflow_id'
			);

			return $this->select_data($fields, $this->tbl_account_groups, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_business_centers($where = NULL)
	{
		try
		{
			$fields = array(
			   	'name',
				'org_code'
			);

			return $this->select_data($fields, $this->tbl_organizations, TRUE, $where, ['name' => 'ASC']);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendors_with_inbound()
	{
		try
		{
            $query    =<<<EOS
            SELECT 
            	A.vendor_code, A.vendor_name 
        	FROM 
        		$this->tbl_vendors A 
			JOIN 
				$this->tbl_vendor_account_groups B ON A.vendor_code = B.vendor_code 
			WHERE 
				B.account_group_code IN ('INBOUND', 'INBOUND_NORMAL', 'INBOUND_CENTRAL') AND A.deleted_flag=0
EOS;
			return $this->query($query);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	// public function get_all_vendors($where = NULL)
	// {
	// 	try
	// 	{
	// 		$fields = array(
	// 		   	'vendor_code',
	// 			'vendor_name'
	// 		);

	// 		return $this->select_data($fields, $this->tbl_vendors, TRUE, $where);
	// 	}
	// 	catch(PDOException $e)
	// 	{
	// 		throw $e;
	// 	}
	// }

	public function get_account_group_code_of_selected_vendor($parent, $vendor_code)
    {
        try
        {
        	$val = array($parent, $vendor_code);

            $query    =<<<EOS
            SELECT 
                    B.account_group_code
            FROM  
                    $this->tbl_account_groups A 
            LEFT JOIN 
                    $this->tbl_vendor_account_groups B ON A.account_group_code = B.account_group_code
            WHERE A.parent_account_group_code = ? and B.vendor_code = ?
EOS;

            $return_val =  $this->query($query, $val);

			return $return_val[0]['account_group_code'];
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

	public function get_vendor_account_groups($where = NULL, $fields=['vendor_code', 'account_group_code'])
	{
		try
		{
			/* $fields = array(
			   	'vendor_code',
				'account_group_code'
			); */

			return $this->select_data($fields, $this->tbl_vendor_account_groups, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_vendor_business_centers($where = NULL)
	{
		try
		{
			$fields = array(
			   	'vendor_code',
				'org_code'
			);

			return $this->select_data($fields, $this->tbl_business_centers, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_cost_center_groups($where = NULL)
	{
		try
		{
			$fields = array(
			   	'vendor_code',
				'cost_center_code'
			);

			return $this->select_data($fields, $this->tbl_cost_centers, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function delete_account_groups($vendor_code)
	{

		try
		{
			return $this->delete_data($this->tbl_vendor_account_groups, array('vendor_code' => $vendor_code));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function delete_cost_centers($vendor_code)
	{

		try
		{
			return $this->delete_data($this->tbl_cost_centers, array('vendor_code' => $vendor_code));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function delete_business_centers($vendor_code)
	{

		try
		{
			return $this->delete_data($this->tbl_business_centers, array('vendor_code' => $vendor_code));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function delete_user_orgs($user_id)
	{

		try
		{
			return $this->delete_data($this->tbl_user_orgs, array('user_id' => $user_id));
		}
		catch(PDOException $e)
		{
			throw $e;
		}

	}

	public function insert_account_groups($fields)
	{
		try{

			return $this->insert_data($this->tbl_vendor_account_groups, $fields, TRUE);

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}

	public function insert_cost_centers($fields)
	{
		try{

			return $this->insert_data($this->tbl_cost_centers, $fields, TRUE);

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}

	public function insert_business_centers($fields)
	{
		try{

			return $this->insert_data($this->tbl_business_centers, $fields, TRUE);

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}

	public function insert_user_orgs($fields)
	{
		try{

			return $this->insert_data($this->tbl_user_orgs, $fields, TRUE);

		}catch(PDOException $e){
			throw $e;
		}catch(Exception $e){
			throw $e;
		}
	}
}