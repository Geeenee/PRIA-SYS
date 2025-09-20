<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permissions_model extends SYSAD_Model {
	
	private $modules;
	private $module_actions;
	private $module_scopes;
	private $module_action_roles;
	private $module_scope_roles;
	private $sys_param;
	private $systems;
	private $system_roles;
	private $roles;

	public function __construct()
	{
		parent::__construct();
		
		$this->modules 				= parent::CORE_TABLE_MODULES;
		$this->module_actions 		= parent::CORE_TABLE_MODULE_ACTIONS;
		$this->module_scopes 		= parent::CORE_TABLE_MODULE_SCOPES;
		$this->module_action_roles 	= parent::CORE_TABLE_MODULE_ACTION_ROLES;
		$this->module_scope_roles 	= parent::CORE_TABLE_MODULE_SCOPE_ROLES;
		$this->sys_param 			= parent::CORE_TABLE_SYS_PARAM;
		$this->systems 				= parent::CORE_TABLE_SYSTEMS;
		$this->system_roles 		= parent::CORE_TABLE_SYSTEM_ROLES;
		$this->roles 				= parent::CORE_TABLE_ROLES;
		$this->users				= parent::CORE_TABLE_USERS;
	}
	
	public function get_module_action_scopes($app_id = NULL)
	{
		$stmt 			= array();

		try 
		{
			$where 		= "";
			$val		= array(SYS_PARAM_ACTIONS, SYS_PARAM_SCOPES);
			
			if(!EMPTY($app_id) && $app_id != 'all')
			{
				$where 	= ' AND A.system_code = ?'; 
				$val[] 	= filter_var($app_id, FILTER_SANITIZE_STRING);
			}

			$query1  	= "
				SET SESSION group_concat_max_len = 1000000
";
			$this->query( $query1, array(), FALSE );
			
			$query = <<<EOS
					SELECT A.sort_order, F.system_name, A.parent_module, A.module_code, A.system_code, 
					IF(A.parent_module IS NOT NULL, LPAD(A.module_name, CHAR_LENGTH(A.module_name) + 5, SPACE(5)), A.module_name) module_name,
					GROUP_CONCAT(DISTINCT B.module_action_id ORDER BY B.module_action_id) as available_action_per_module, GROUP_CONCAT(DISTINCT C.sys_param_name ORDER BY B.module_action_id) as action_name,
					GROUP_CONCAT(DISTINCT D.scope ORDER BY E.sys_param_value) as available_scope_per_module, GROUP_CONCAT(DISTINCT E.sys_param_name ORDER BY E.sys_param_value) as scope_name
					FROM $this->modules A
					/* JOIN TO GET THE AVAILABLE ACTION PER MODULE */
					LEFT JOIN $this->module_actions B
					ON A.module_code = B.module_code
					LEFT JOIN $this->sys_param C
					ON C.sys_param_type = ? AND B.action = C.sys_param_code
					/* JOIN TO GET THE AVAILABLE ACTION PER MODULE */
					LEFT JOIN $this->module_scopes D
					ON A.module_code = D.module_code
					LEFT JOIN $this->sys_param E
					ON E.sys_param_type = ? AND D.scope = E.sys_param_code
					/* JOIN TO GET SYSTEM NAME */
					JOIN $this->systems F
					ON A.system_code = F.system_code
				WHERE A.sort_order <> 0
				$where										
			GROUP BY A.module_code
			ORDER BY A.system_code ASC, A.sort_order ASC
EOS;
			$stmt = $this->query($query, $val);
				
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	
		return $stmt;
	}
		
	public function get_modules($module_code = NULL, $app_code = NULL, $parent_module = NULL, $where = array())
	{
		$result 			= array();

		try
		{
			$multiple 		= TRUE;
			$order 			= array("sort_order" => "ASC");
			$fields 		= array("module_code", "system_code", "module_name", "icon", "link", "hide_flag", "description");
			
			if(!IS_NULL($module_code))
			{
				$multiple 	= FALSE;
				$order 		= array();
				$where['module_code']  	= filter_var($module_code, FILTER_SANITIZE_STRING);
			}
			
			if(!IS_NULL($app_code))
			{
				$where['system_code'] 	= filter_var($app_code, FILTER_SANITIZE_STRING);
			}
			
			if(!IS_NULL($parent_module))
			{
				$where['parent_module'] = filter_var($parent_module, FILTER_SANITIZE_STRING);
			}
			
			$result 					= $this->select_data($fields, $this->modules, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function get_role_action($role_code, $group_concat = FALSE)
	{
		$result 		= array();

		try
		{
			$role_code 	= filter_var($role_code, FILTER_SANITIZE_STRING);;
			
			$fields 	= ($group_concat)? array("GROUP_CONCAT(module_action_id) module_action_id, role_code") : array("*");
			$where 		= array("role_code" => $role_code);
			
			$multiple 	= ($group_concat)? FALSE : TRUE;
			$order 		= array(
				'module_action_id'	=> 'ASC'
			);
			
			$result 	= $this->select_data($fields, $this->module_action_roles, $multiple, $where, $order);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function get_role_scope($role_code, $group_concat=FALSE)
	{
		$result 		= array();

		try
		{
			$role_code 	= filter_var($role_code, FILTER_SANITIZE_STRING);
			
			$fields 	= ($group_concat)? array("scope, role_code, module_code") : array("*");
			$where 		= array("role_code" => $role_code);
			
			$group_by 	= array("module_code");
			
			$stmt 		= $this->select_data($fields, $this->module_scope_roles, TRUE, $where, array(), $group_by);
			
			if($group_concat)
			{
				$return = array();
				
				foreach($stmt as $key => $val):
					$return[$val['module_code']] = $val['scope'];
				endforeach;
				
				$result = $return;
			} 
			else 
			{
				$result = $stmt;
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function get_system_roles($system_code)
	{
		$result 	= array();

		try
		{
			$val 	= array($system_code);
			
			$query 	= <<<EOS
				SELECT A.role_code value, B.role_name text
				FROM $this->system_roles A, $this->roles B
				WHERE A.role_code = B.role_code
				AND A.system_code = ?
				ORDER BY A.role_code ASC
EOS;
			$stmt 	= $this->query($query, $val);
				
			$result = $stmt;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function get_permission_access($module_code, $button_action = NULL)
	{
		$result 		= array();

		try
		{
			$where 		= "";
			$val 		= array(SYS_PARAM_ACTIONS, $module_code);
			
			if(!IS_NULL($button_action))
			{
				$where 	= " AND C.sys_param_value = ?";
				$val[] 	= $button_action;
			}
				
			$query 		= <<<EOS
				SELECT A.role_code 
				FROM $this->module_action_roles A, $this->module_actions B, $this->sys_param C 
				WHERE A.module_action_id = B.module_action_id 
				AND B.action = C.sys_param_code AND C.sys_param_type = ?
				AND B.module_code = ? 
				$where
EOS;

			$stmt 	= $this->query($query, $val);
			
			$result = $stmt;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function get_scope($module_code, $role)
	{
		$result 	= array();

		try
		{
			$val 	= array($module_code, $role);
			
			$query 	= <<<EOS
				SELECT * FROM $this->module_scope_roles
				WHERE module_code = ? AND role_code = ? 
EOS;
		
			$stmt 	= $this->query($query, $val, TRUE, FALSE);
			
			$result = $stmt;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}
	
	public function get_module_scope_roles($module_code)
	{
		$result 	= array();

		try
		{
			$val 	= array($module_code);
				
			$query 	= <<<EOS
				SELECT GROUP_CONCAT(role_code) role_code 
				FROM $this->module_scope_roles
				WHERE module_code = ?
EOS;
			$stmt 	= $this->query($query, $val, TRUE, FALSE);
			
			$result = $stmt;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		return $result;
	}		
	
	public function delete_action_roles($role_code, $system_code)
	{
		try
		{			
			$where 	= "";
			$val 	= array();
			$val[] 	= $role_code;
			
			if($system_code != 'all')
			{
				$where = " AND C.system_code = ? ";
				$val[] = $system_code;
			}
			
			$query = <<<EOS
				DELETE A FROM $this->module_action_roles A 
				JOIN $this->module_actions B ON A.module_action_id = B.module_action_id
				JOIN $this->modules C ON B.module_code = C.module_code
				WHERE A.role_code = ?
				$where
EOS;
			
			$this->query($query, $val, FALSE);
			
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
	
	public function delete_scope_roles($role_code, $system_code)
	{
		try
		{	
			$where 	= "";
			$val 	= array();
			$val[] 	= $role_code;
			
			if($system_code != 'all')
			{
				$where = " AND B.system_code = ? ";
				$val[] = $system_code;
			}
			
			$query 		= <<<EOS
				DELETE A FROM $this->module_scope_roles A
				JOIN $this->modules B ON A.module_code = B.module_code
				WHERE A.role_code = ?
				$where
EOS;
			$this->query($query, $val, FALSE);
			
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
	
	public function insert_action_roles($module_actions, $role_code)
	{
		try 
		{
			$fields = '(module_action_id, role_code)';
			$values = '';
			$val 	= array();
			
			foreach($module_actions as $key => $mod_val):
				foreach($mod_val as $k => $v):
					$values .= '(?, ?), ';
					$val[] = $v;
					$val[] = $role_code;
				endforeach;
			endforeach;
			
			$values = rtrim($values, ', ');
			$query 	= <<<EOS
					INSERT INTO $this->module_action_roles
					$fields
					VALUES
					$values
EOS;
			$this->query($query, $val, FALSE);
			
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
	
	public function insert_scope_roles($module_scopes, $role_code)
	{
		try 
		{
			$fields = '(module_code, scope, role_code)';
			$values = '';
			$val 	= array();
				
			foreach($module_scopes as $key => $mod_val):
				foreach($mod_val as $k => $v):
					$values .= '(?, ?, ?), ';
					$val[]   = $key;
					$val[]   = $v;
					$val[]   = $role_code;
				endforeach;
			endforeach;
			
			$values = rtrim($values, ', ');
			$query = <<<EOS
					INSERT INTO $this->module_scope_roles
					$fields
					VALUES
					$values
EOS;
			$this->query($query, $val, FALSE);
			
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function has_children( $module_id )
	{
		$result 		= array();

		$id 			= 0;

		try
		{
			$field 		= array( 'COUNT( module_code ) id' );
			$where 		= array( 'parent_module' => $module_id );

			$result 	= $this->select_data( $field, SYSAD_Model::CORE_TABLE_MODULES, FALSE, $where );

			if( !EMPTY( $result ) )
			{
				$id 	= $result['id'];
			}

		}
		catch( PDOException $e )
		{
			throw $e;
		}	

		return $id;
	}

	public function get_role_action_by_module_id( array $where = array(), array $modules = array() )
	{
		$result 			= array();

		$where_str 			= '';
		$where_val 			= array();

		$extra_where 		= '';
		$extra_val 			= array();

		try
		{
			if( ISSET( $where['where'] ) )
			{
				$where_str 	= $where['where'];
			}
			else
			{
				return array();
			}

			if( ISSET( $where['val'] ) )
			{
				$where_val 	= $where['val'];
			}
			else
			{
				return array();
			}

			if( !EMPTY( $modules ) )
			{
				$count_modules			= count( $modules );
				$placeholder_modules 	= str_repeat( '?,', $count_modules );
				$placeholder_modules 	= rtrim( $placeholder_modules, ',' );

				$extra_where 			= " AND a.module_code IN ( $placeholder_modules ) ";

				$extra_val 				= array_merge( $extra_val, $modules );
			}
			else
			{
				return array();
			}

			$query 			= "
				SELECT  b.module_action_id
				FROM 	%s a 
				JOIN 	%s b 
				ON 		a.module_action_id = b.module_action_id
				$where_str
				$extra_where
";
			$query 			= sprintf( $query, SYSAD_Model::CORE_TABLE_MODULE_ACTIONS, SYSAD_Model::CORE_TABLE_MODULE_ACTION_ROLES );

			if( !EMPTY( $extra_val ) )
			{
				$where_val 	= array_merge( $where_val, $extra_val );
			}

			$result 		= $this->query( $query, $where_val );

		}
		catch( PDOException $e )
		{
			throw $e;
		}

		return $result;
	}

	public function get_role_action_per( array $where = array(), array $modules = array() )
	{
		$result 				= array();

		$extra_where 			= '';
		$extra_val 				= array();

		$where_str 				= '';
		$where_val 				= array();

		try
		{

			if( ISSET( $where['where'] ) )
			{
				$where_str 	= $where['where'];
			}
			else
			{
				return array();
			}

			if( ISSET( $where['val'] ) )
			{
				$where_val 	= $where['val'];
			}
			else
			{
				return array();
			}

			if( !EMPTY( $modules ) )
			{
				$count_modules			= count( $modules );
				$placeholder_modules 	= str_repeat( '?,', $count_modules );
				$placeholder_modules 	= rtrim( $placeholder_modules, ',' );

				$extra_where 			= " AND module_action_id IN ( $placeholder_modules ) ";

				$extra_val 				= array_merge( $extra_val, $modules );
			}

			if( !EMPTY( $modules ) )
			{

				$query 				= "
					SELECT 	* 
					FROM 	%s 
					$where_str
					$extra_where
";

				$query 				= sprintf( $query, SYSAD_Model::CORE_TABLE_MODULE_ACTION_ROLES );

				if( !EMPTY( $extra_val ) )
				{
					$where_val 	= array_merge( $where_val, $extra_val );
				}

				$result 			= $this->query( $query, $where_val);

			}

		}
		catch( PDOException $e )
		{
			throw $e;
		}

		return $result;
	}

	public function delete_action_role_per( array $where = array(), array $modules = array() )
	{
		$result 				= array();

		$extra_where 			= '';
		$extra_val 				= array();

		$where_str 				= '';
		$where_val 				= array();

		try
		{
			if( ISSET( $where['where'] ) )
			{
				$where_str 	= $where['where'];
			}
			else
			{
				return array();
			}

			if( ISSET( $where['val'] ) )
			{
				$where_val 	= $where['val'];
			}
			else
			{
				return array();
			}

			if( !EMPTY( $modules ) )
			{
				$count_modules			= count( $modules );
				$placeholder_modules 	= str_repeat( '?,', $count_modules );
				$placeholder_modules 	= rtrim( $placeholder_modules, ',' );

				$extra_where 			= " AND module_action_id IN ( $placeholder_modules ) ";

				$extra_val 				= array_merge( $extra_val, $modules );
			}

			if( !EMPTY( $modules ) )
			{

				$query 				= "
					DELETE 	FROM %s
					$where_str
					$extra_where
";

				$query 				= sprintf( $query, SYSAD_Model::CORE_TABLE_MODULE_ACTION_ROLES );
				
				if( !EMPTY( $extra_val ) )
				{
					$where_val 	= array_merge( $where_val, $extra_val );
				}

				$result 			= $this->query( $query, $where_val, FALSE );

			}
		}
		catch( PDOException $e )
		{
			throw $e;
		}
	}
	
	public function get_role_scope_per( array $where = array(), array $modules = array() )
	{
		$result 			= array();

		$where_str 			= '';
		$where_val 			= array();

		$extra_where 		= '';
		$extra_val 			= array();

		try
		{
			if( ISSET( $where['where'] ) )
			{
				$where_str 	= $where['where'];
			}
			else
			{
				return array();
			}

			if( ISSET( $where['val'] ) )
			{
				$where_val 	= $where['val'];
			}
			else
			{
				return array();
			}

			if( !EMPTY( $modules ) )
			{
				$count_modules			= count( $modules );
				$placeholder_modules 	= str_repeat( '?,', $count_modules );
				$placeholder_modules 	= rtrim( $placeholder_modules, ',' );

				$extra_where 			= " AND module_code IN ( $placeholder_modules ) ";

				$extra_val 				= array_merge( $extra_val, $modules );
			}
			else
			{
				return array();
			}

			$query 			= "
				SELECT  *
				FROM 	%s
				$where_str
				$extra_where
";
			$query 			= sprintf( $query, SYSAD_Model::CORE_TABLE_MODULE_SCOPE_ROLES );

			if( !EMPTY( $extra_val ) )
			{
				$where_val 	= array_merge( $where_val, $extra_val );
			}

		
			$result 		= $this->query( $query, $where_val );

		}
		catch( PDOException $e )
		{
			throw $e;
		}

		return $result;
	}

	public function delete_scope_role_per( array $where = array(), array $modules = array() )
	{
		$result 				= array();

		$extra_where 			= '';
		$extra_val 				= array();

		$where_str 				= '';
		$where_val 				= array();

		try
		{
			if( ISSET( $where['where'] ) )
			{
				$where_str 	= $where['where'];
			}
			else
			{
				return array();
			}

			if( ISSET( $where['val'] ) )
			{
				$where_val 	= $where['val'];
			}
			else
			{
				return array();
			}

			if( !EMPTY( $modules ) )
			{
				$count_modules			= count( $modules );
				$placeholder_modules 	= str_repeat( '?,', $count_modules );
				$placeholder_modules 	= rtrim( $placeholder_modules, ',' );

				$extra_where 			= " AND module_code IN ( $placeholder_modules ) ";

				$extra_val 				= array_merge( $extra_val, $modules );
			}

			if( !EMPTY( $modules ) )
			{

				$query 				= "
					DELETE 	FROM %s
					$where_str
					$extra_where
";

				$query 				= sprintf( $query, SYSAD_Model::CORE_TABLE_MODULE_SCOPE_ROLES );

				if( !EMPTY( $extra_val ) )
				{
					$where_val 	= array_merge( $where_val, $extra_val );
				}

				$result 			= $this->query( $query, $where_val, FALSE );

			}
		}
		catch( PDOException $e )
		{
			throw $e;
		}
	}


	//Added by kebs
	// public function update_task_roles(array $where, array $fields)
 //    {
 //        try
 //        {
 //            $this->update_data(self::PORTAL_TABLE_PRIA_TASK_ROLES, $fields, $where);
 //        }
 //        catch(PDOException $e)
 //        {
 //            throw $e;
 //        }
	// }
	
	public function get_module_scopes_by_role_codes_arr($module_code, $role_arr)
	{
		try
		{
			$roles = implode('\',\'', $role_arr);
			$query =<<<EOS
				SELECT
					b.sys_param_value
				FROM
					$this->module_scope_roles a
				JOIN
					$this->sys_param b ON a.scope = b.sys_param_code
				WHERE
					a.module_code = ?
				AND
					a.role_code IN ('%s')
				GROUP BY 
					a.scope
				ORDER BY 
					b.sys_param_value ASC
EOS;

			$query  = sprintf($query, $roles);
			//print_var_export($query, $module_code); die;
			$result =  array_column($this->query($query, [$module_code], TRUE, TRUE), 'sys_param_value');

			return $result;
			//print_var_export($result); die;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function check_session($user_id) {
		try {
			return $this->select_data(array(
				'logged_in_flag'
			), $this->users, FALSE, array(
				'user_id' => $user_id
			));
		} catch (PDOException $e) {
			throw $e;
		}
	}

}