<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission {
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	/**
	 * $module_code - Code of the module being accessed
	 * 
	 * $button_action - Actions that a user can use depending on its access level
	 *  
	 * $redirect - redirect to unauthorized access error page if necessary
	 *     
	 */
	
	public function check_permission($module_code, $button_action = NULL, $redirect = FALSE){
	
		try
		{
			$permissions = $this->CI->permissions_model->get_permission_access($module_code, $button_action);
		
			$has_access	= FALSE;
			
			if(!EMPTY($permissions))
			{
				$user_roles = $this->CI->session->userdata('user_roles');
		
				if(!EMPTY($user_roles)){
		
					foreach($permissions as $permission):
						$role_code = $permission['role_code'];
							
						if(in_array($role_code, $user_roles))
							$has_access	= TRUE;
					endforeach;
				}
			}
		
			if($has_access){
				if(!$redirect)
					return TRUE;
			} else {
				if($redirect){
					redirect(base_url() . 'unauthorized' , 'location');
				} else {
					return FALSE;
				}
			}
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

	public function get_scope($module_code) 
	{
		$scope 			= NULL;

		try
		{
			$scope_system = get_sys_param_code(SYS_PARAM_SCOPES, SCOPE_SYSTEM);
			$scope_region = get_sys_param_code(SYS_PARAM_SCOPES, SCOPE_REGION);
			$scope_agency = get_sys_param_code(SYS_PARAM_SCOPES, SCOPE_AGENCY);
			$scope_direct = get_sys_param_code(SYS_PARAM_SCOPES, SCOPE_DIRECT_NODES);

			$scope_system = $scope_system['sys_param_code'];
			$scope_region = $scope_region['sys_param_code'];
			$scope_agency = $scope_agency['sys_param_code'];
			$scope_direct = $scope_direct['sys_param_code'];

			$user_roles 	= $this->CI->session->userdata('user_roles');

			if( ! ISSET($user_roles) || EMPTY($user_roles))
			{
				return NULL;
			}

			$priority_heirarchy = array(
				$scope_system,
				$scope_region,
				$scope_direct,
				$scope_agency
			);

			$curr_scope 		= count( $priority_heirarchy );

			if($user_roles)
			{
				foreach ($user_roles as $user_role) 
				{
					$result		= $this->CI->permissions_model->get_scope($module_code, $user_role);

					if(ISSET( $result ) AND ISSET( $result['scope'] ) )
					{
						if( !EMPTY( $result['scope'] ) )
						{
							$heirarchy 		= array_search($result['scope'], $priority_heirarchy);
							
							if($heirarchy < $curr_scope)
							{
								$curr_scope = $heirarchy;

								break;
							}
						}
					}
				}

				$curr_scope = $priority_heirarchy[$curr_scope];
				
				if($curr_scope == $scope_system )
					$curr_scope = SCOPE_SYSTEM;

				if($curr_scope == $scope_region)
					$curr_scope = SCOPE_REGION;

				if($curr_scope == $scope_agency)
					$curr_scope = SCOPE_AGENCY;

				if($curr_scope == $scope_direct)
					$curr_scope = SCOPE_DIRECT_NODES;
			}
			else
			{
				$curr_scope = null;
			}

			$scope 	= $curr_scope;
		}	
		catch(PDOException $e) 
		{
			throw $e;
		} 
		catch(Exception $e) 
		{
			throw $e;
		}

		return $scope;
	}

	public function get_orgs_by_scope( $module_code )
	{
		$orgs 		= array();

		try
		{
			$this->CI->load->model(CORE_USER_MANAGEMENT.'/Organizations_model', 'org_mod');

			$scope 	= $this->get_scope($module_code);

			$current_org_code 	= $this->CI->session->org_code;

			$org_codes 			= ( !is_array( $current_org_code ) ) ? array( $current_org_code ) : $current_org_code;

			if( !EMPTY( $scope ) )
			{
				if( $scope == SCOPE_REGION
					OR $scope == SCOPE_DIRECT_NODES
			 	)
				{
					foreach( $org_codes as $o_c )
					{
						$check_root 	= FALSE;

						$check_root_det = $this->CI->org_mod->check_root($o_c);

						if( !EMPTY( $check_root_det ) AND !EMPTY( $check_root_det['check_root'] ) )
						{
							$check_root = TRUE;
						}

						$org_childs 	= $this->CI->org_mod->get_descendants($o_c, Organizations_model::DESCENDANTS, NULL, $check_root);

						if( !EMPTY( $org_childs ) )
						{
							$org_c 		= array_column($org_childs, 'org_code');

							if( $scope == SCOPE_REGION )
							{
								$orgs 	= array_merge( $orgs, $org_c );
							}
							else if( $scope == SCOPE_DIRECT_NODES )
							{
								$orgs[] = $org_c[0];
							}
						}

						if( $check_root )
						{
							$orgs[] 	= $o_c;
						}
					}

					$orgs 				= array_unique( $orgs );

					if( EMPTY( $orgs ) )
					{
						$orgs 			= $org_codes;
					}
				}
				else if( $scope == SCOPE_AGENCY )
				{
					$orgs			= $org_codes;
				}
				else if( $scope == SCOPE_SYSTEM )
				{
					$orgs 			= array();
				}
			}
			else
			{
				$orgs 				= FALSE;
			}
		}
		catch(PDOException $e) 
		{
			throw $e;
		} 
		catch(Exception $e) 
		{
			throw $e;
		}

		return $orgs;
	}

	public function check_session(){
	
		try
		{
			if (isset($_SESSION['user_id'])) {
				$session = $this->CI->permissions_model->check_session($_SESSION['user_id']);

				if ($session['logged_in_flag'] == 0) {
					$this->CI->authenticate->sign_out($_SESSION['user_id']);
					delete_cookie('autologin');
					redirect(base_url());
				}
			}
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