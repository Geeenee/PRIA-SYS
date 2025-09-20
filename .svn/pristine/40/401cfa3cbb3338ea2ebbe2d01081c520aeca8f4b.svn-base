<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authenticate 
{

	protected $check_maintenance = FALSE;
	protected $login_url 		  	= array(
		"auth"
	);

	protected $exemption_dir 		= array();
	protected $sub_dir_login        = array();

	protected $exemption 			= array("css", "common", "sign_up", "forgot_password","account_cron","unauthorized", 'errors_public', 'mail_action', 'mobile_upload', 'params', 'main', 'tasks', 'dropdowns', 'pria_file');

	public function __construct()
	{
		$this->CI =& get_instance();
		
		$this->check_maintenance 	= $this->check_maintenance_mode();
		$exempt_cur_dir 			= $this->process_directory();
		$fetch_dir 					= $this->CI->router->fetch_directory();
		
		if( !$this->check_maintenance )
		{
			if(
				(
					( $this->CI->router->fetch_class() == 'sign_up' AND !EMPTY( $fetch_dir ) )
					OR
					!in_array($this->CI->router->fetch_class(), $this->exemption) 
				)
				AND !$exempt_cur_dir
			)
			{
				$this->check_user();
			}
		}

		
	}

	private function process_dir_uri( $uri )
	{
		$arr 			= array();
		$clean_string 	= '';

		if( !EMPTY( $uri ) )
		{
			$clean_string 	= rtrim(str_replace('../', '', $uri), '/');
			$arr 			= explode( '/', $clean_string );
		}

		return array(
			'dir_arr'		=> $arr,
			'clean_string'	=> $clean_string
		);
	}

	private function process_directory()
	{
		$current_dir 	= '';
		$fetch_dir 		= $this->CI->router->fetch_directory();

		if( !EMPTY( $fetch_dir ) )
		{
			$fetch_dir_det  = $this->process_dir_uri( $fetch_dir );

			$fetch_dir 		= $fetch_dir_det['clean_string'];
			$fetch_dir_arr 	= $fetch_dir_det['dir_arr'];
			
			if( !EMPTY( $fetch_dir_arr ) )
			{
				foreach( $fetch_dir_arr as $dir )
				{
					if( in_array( $dir, $this->exemption_dir ) )
					{
						if( !EMPTY( $this->sub_dir_login ) )
                    	{
                    		foreach( $this->sub_dir_login as $sub_dir )
                        	{
                        		$exempt_arr     = explode('/', $sub_dir);

                        		if( $dir == $exempt_arr[0] )
	                            {
	                                if( in_array( $exempt_arr[1], $fetch_dir_arr ) )
	                                {
	                                    return FALSE;
	                                }
	                                else
	                                {
	                                    return TRUE;
	                                }
	                            }
                        	}
                    	}
                    	else
	                    {
	                        return TRUE;
	                    }
					}
				}
			}
		}

		return FALSE;
	}
	
	public function check_maintenance_mode()
	{
		$maintenance_mode 				= get_setting(GENERAL, "maintenance_mode");

		$check 							= FALSE;
		$maintainer 					= FALSE;

		if( !EMPTY( $maintenance_mode ) )
		{

			$check 						= TRUE;

			$authenticated 				= ($this->CI->session->has_userdata('user_id') == TRUE)? $this->CI->session->user_id : 0;
			
			if( $this->CI->router->fetch_class() != "maintenance"
				AND !in_array( $this->CI->router->fetch_class(), $this->login_url )
			)
			{
				$maintainer = $this->check_maintenance_maintainer( $authenticated, $check, TRUE );

				if( !$authenticated OR ( !EMPTY( $authenticated ) AND !$maintainer )  )
				{
					header('Location:'.base_url().'maintenance');
				}

				if( $authenticated AND !$maintainer )
				{
					$this->CI->session->sess_destroy();
					delete_cookie('autologin');
				}

			}
		}
		else
		{
			if( $this->CI->router->fetch_class() == "maintenance" )
			{
				header('Location:'.base_url());
			}
		}

		return $check;
	}
	
	public function check_user()
	{		
		// CHECK IF SESSION EXISTS
		$authenticated 					= ($this->CI->session->has_userdata('user_id') == TRUE)? 1 : 0;
		$initial_flag 					= ($this->CI->session->has_userdata('initial_flag') == TRUE) ? $this->CI->session->userdata( "initial_flag" ) : 0;
		$change_password_initial_login 	= get_setting(LOGIN, "change_password_initial_login");
		$session_username 				= $this->CI->session->userdata('username');
		$session_username 				= base64_url_encode( $this->CI->session->userdata('username') );
		$salt 							= $this->CI->session->userdata('salt');

		$account_create 				= get_setting(ACCOUNT, 'account_creator');
		
		if($this->CI->router->fetch_class() != "auth")
		{	
			if(!$authenticated)	
			{
				if( $this->CI->router->fetch_class() != 'unauthorized' AND 
					$this->CI->router->fetch_method() != 'session_expired_modal' AND
					$this->CI->input->is_ajax_request() != TRUE
				)
				{
					header('Location:'.base_url());
				}
			}
			else
			{ 
				$fetch_dir 		= $this->CI->router->fetch_directory();

				$fetch_dir_det  = $this->process_dir_uri( $fetch_dir );

				$fetch_dir 		= $fetch_dir_det['clean_string'];
				$fetch_dir_arr 	= $fetch_dir_det['dir_arr'];

				if( !EMPTY( $fetch_dir ) )
				{
					if( ISSET( $fetch_dir_arr[1] ) )	
					{
						$this->CI->load->model(CORE_SYSTEMS.'/Systems_application_model', 'sys_app_auth_mod');

						$check_sys_dir 	= $this->CI->sys_app_auth_mod->check_system_redirection( $fetch_dir_arr[1] );

						if( !EMPTY( $check_sys_dir ) )
						{
							if( EMPTY( $check_sys_dir['check_system_redirection'] ) )
							{
								show_404();
							}
							else
							{
								$img_src 		= "";
	
								if( !EMPTY( $check_sys_dir["logo"] ) )
								{
									$root_path 	= get_root_path();
					
									$photo_path = $root_path.PATH_SYSTEMS_UPLOADS.$check_sys_dir["logo"];
									$photo_path = str_replace(array('\\','/'), array(DS,DS), $photo_path);
					
									if( file_exists( $photo_path ) )
									{
										$img_src = output_image($check_sys_dir["logo"], PATH_SYSTEMS_UPLOADS);
									}
								}

								$this->CI->session->set_userdata('current_system', $check_sys_dir['system_code']);
								$this->CI->session->set_userdata('current_system_logo', $img_src);
								
							}
						}
					}

				}
				
			}	

			// AND $account_create == ADMINISTRATOR
			if( !EMPTY( $change_password_initial_login ) )
			{
				if( $this->CI->router->fetch_class() != "reset_password" )
				{

					if( !EMPTY( $initial_flag ) AND $this->CI->router->fetch_method() != 'update_password' )
					{
						header('location: '.base_url().'reset_password/initial_logged_in/'.$session_username.'/'.$salt.'/'.INITIAL_YES.'/' );	
					}
				}
				else
				{
					if( EMPTY( $initial_flag ) )
					{
						$user_systems = $this->CI->session->user_systems;

						if(!in_array(SYSAD, $user_systems))
						{
							header('Location:'.base_url().CORE_HOME_PAGE);
						}
						else
						{
							header('Location:'.base_url().CORE_HOME_PAGE);
						}
					}
				}
			}
		}
		else
		{		
			if($authenticated AND $this->CI->router->fetch_method() != "sign_out")
			{

				if( !EMPTY( $initial_flag ) )
				{
					// AND $account_create == ADMINISTRATOR
					if( !EMPTY( $change_password_initial_login ) )
					{
						if( $this->CI->router->fetch_method() != 'update_password' )
						{
							header('location: '.base_url().'reset_password/initial_logged_in/'.$session_username.'/'.$salt.'/'.INITIAL_YES.'/');	
						}
						
					}
				}
				else
				{
				
					$user_systems = $this->CI->session->user_systems;

					$landing_page = $this->CI->session->redirect_page;

					try
					{
						$query_string = get_params(TRUE, TRUE);
					}
					catch(Exception $e)
					{
						$query_string = [];
					}

					//Added by kebs
					if(ISSET($query_string['redirect']) && ! EMPTY($query_string['redirect']))
					{
						header('Location:'.base_url().$query_string['redirect']);
					}
					else
					{
						if(!EMPTY($landing_page))
						{
							header('Location:'.base_url().$landing_page);
						}
						else
						{
							header('Location:'.base_url().CORE_HOME_PAGE);
						}
					}
				}
			}
		}
		
		$auto_log_inactivity 		= get_setting( LOGIN, 'auto_log_inactivity' );
		$auto_log_inactivity_dur 	= get_setting( LOGIN, 'auto_log_inactivity_duration' );

		if( !EMPTY( $auto_log_inactivity ) AND !EMPTY( $auto_log_inactivity_dur ) )
		{
			
			if( $authenticated )
			{
				$active_time 		= $this->CI->session->userdata( "active_time" );
							
				/*if( time() - $active_time >= $auto_log_inactivity_dur ) //subtract new timestamp from the old one
				{ 
				    $this->sign_out();
				    header('location: '.base_url());
				} 
				else 
				{
				    $this->CI->session->set_userdata( "active_time", time() ); //set new timestamp
				}*/

			}

		}						
	}

	protected function check_maintenance_maintainer( $user_id, $check_maintenance = FALSE, $return = FALSE )	
	{

		$check 	= FALSE;

		if( $check_maintenance )
		{
			$this->CI->load->model('Auth_model', 'authy');
			
			$maintainer_flags 		= $this->CI->authy->check_user_maintainer( $user_id );

			if( !EMPTY( $maintainer_flags ) )
			{
				$maintainer_flags 	= array_column($maintainer_flags, 'maintainer_flag');

				if( !in_array(MAINTAINER_YES, $maintainer_flags) )
				{
					
					if( !$return )
					{
						throw new Exception($this->CI->lang->line('maintenance_mode'));
					}
				}
				else
				{
					$check = TRUE;
				}
			}
		}

		return $check;
	}
	
	public function sign_in($username, $password, $salted , $verify_pass_only = FALSE)
	{
		try 
		{
			$flag = 0;
			
			$user_info = $this->CI->auth_model->get_active_user($username);
			
			$maintainer_flags 		= array();

			if( !EMPTY( $user_info ) )
			{
				$this->check_maintenance_maintainer($user_info['user_id'], $this->check_maintenance);
			}
			
			$allowed_val 		= array(BLOCKED, ACTIVE, EXPIRED);
			$not_allowed_val 	= array(DELETED, INACTIVE, PENDING, DISAPPROVED, DRAFT);
			
			$sys_param = get_sys_param_val(SYS_PARAM_STATUS, $user_info['status']);
			
			if(EMPTY($user_info) && !in_array($sys_param["sys_param_value"], $allowed_val))
			{
				throw new Exception($this->CI->lang->line('invalid_login'));
			}

			if(in_array($sys_param["sys_param_value"], $not_allowed_val))
			{
				throw new Exception($this->CI->lang->line('invalid_login'));
			}
			
			if($sys_param["sys_param_value"] == BLOCKED)
				throw new Exception($this->CI->lang->line('account_blocked'));
			
			if($sys_param["sys_param_value"] == EXPIRED)
				throw new Exception($this->CI->lang->line('account_expired'));

			$check_single_session 	= get_setting(LOGIN, "single_session");

			if( !EMPTY( $check_single_session ) )
			{
				if($user_info['logged_in_flag'] == 1)
				{
					throw new Exception($this->CI->lang->line('multiple_login')); 
				}
			}
			
			// ENCRYPT THE PASSWORD 
			$password = ($salted)? $password : in_salt($password, $user_info["salt"], TRUE);

			if($password != $user_info['password'])
			{
				$this->CI->auth_model->update_attempts($user_info["user_id"], $user_info["attempts"]);
				$e_message = ($verify_pass_only) ? 'Incorrect Password.' : $this->CI->lang->line('invalid_login');
				throw new Exception($e_message);
			}else{
				$this->CI->auth_model->update_attempts($user_info["user_id"]);
			}
			
			if($verify_pass_only === TRUE) return TRUE;
			
			if($sys_param["sys_param_value"] == PENDING)
				throw new Exception($this->CI->lang->line('pending_account'));

			// GET AND CHECK USER ROLES	
			$user_roles		= $this->CI->auth_model->get_user_roles($user_info["user_id"], $user_info["attempts"]);
			$user_main_role	= $this->CI->auth_model->get_user_main_role($user_info["user_id"]);

			//Added by Kebs
			$user_orgs_raw	= $this->CI->auth_model->get_user_orgs(['user_id' => $user_info["user_id"]], ['org_code']);
			$user_orgs 		= array_column($user_orgs_raw, 'org_code');
			
			if(EMPTY($user_roles))
				throw new Exception($this->CI->lang->line('contact_admin'));
				
			// SET THE USER INFO IN SESSION VARIABLES
			$arr = array(
				"user_id" 			=> $user_info["user_id"],	
				"username" 			=> $user_info["username"],
				"user_email" 		=> $user_info["email"],
				"photo" 			=> $user_info["photo"],
				"name" 				=> $user_info["name"],
				"job_title" 		=> $user_info["job_title"],
				"location_code" 	=> $user_info["location_code"],
				"org_code" 			=> $user_info["org_code"],
				"active_time"		=> time(),
				'salt'				=> $user_info['salt'],
				"initial_flag" 		=> $user_info['initial_flag'],
				"user_main_role" 	=> $user_main_role,
				"user_orgs"			=> $user_orgs
			);


			$this->CI->session->set_userdata($arr);
			
			// SET USER ROLES IN SESSION VARIABLES
			$roles 				= array();
			$default_sys 		= array();
			
			foreach($user_roles as $role):
				$roles[] 		= $role['role_code'];
				if( !EMPTY( $default_sys ) )
				{
					$default_sys[] 	= $role['default_system'];
				}
			endforeach;
 			
			$this->CI->session->set_userdata('user_roles', $roles);

			$user_systems = $this->CI->auth_model->get_user_system($roles);

			// SET USER SYSTEMS
			$systems = array();
			foreach($user_systems as $user_system):
				$systems[] = $user_system['system_code'];
			endforeach;

			$this->CI->session->set_userdata('user_systems', $systems);

			$landing_pages 	= array();

			$system_pass 	= $systems;

			if( !EMPTY( $default_sys ) )
			{
				$system_pass = $default_sys;
			}
			
			if( !EMPTY( $systems ) )
			{
				$landing_pages 	= $this->CI->auth_model->get_landing_pages( $system_pass );
			}
			
			// SETS THE LANDING PAGE AFTER LOGIN
			if(!EMPTY($landing_pages))
			{
				if( !EMPTY( $landing_pages[0]['link'] ) )
				{
					$landing_details 	= $this->_process_landing_page( $landing_pages );

					$has_access 		= $landing_details['has_access'];
					
					if( EMPTY( $has_access ) )
					{
						$this->_next_landing_page( $roles, $default_sys );
					}
					
				}
			}
			else
			{
				// $this->CI->session->set_userdata('redirect_page', CORE_HOME_PAGE);
				$this->_next_landing_page( $roles, $default_sys );
			}

			// CHECK IF SESSION EXISTS
			if($this->CI->session->has_userdata('user_id') === FALSE)
				throw new Exception($this->CI->lang->line('system_error'));

			/*if( !EMPTY( $check_single_session ) )
			{*/
				$this->CI->auth_model->update_log($user_info['user_id'], LOGGED_IN_FLAG_YES);
			// }
							
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

	private function _process_landing_page( array $landing_pages )
	{
		$has_access = FALSE;
		$link_p 	= "";

		try
		{
			if( count( $landing_pages ) > 1 )
			{
				foreach( $landing_pages as $l_p )
				{
					$has_access 	= $this->CI->permission->check_permission($l_p['module_code']);

					if( !EMPTY( $has_access ) )
					{
						$link_p 	= $l_p['link'];

						$this->CI->session->set_userdata('redirect_page', $l_p['link']);

						break;
					}
				}
			}
			else
			{
				$has_access 	= $this->CI->permission->check_permission($landing_pages[0]['module_code']);

				if( !EMPTY( $has_access ) )
				{
					$link_p 	= $landing_pages[0]['link'];

					$this->CI->session->set_userdata('redirect_page', $landing_pages[0]['link']);
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

		return array(
			'has_access'	=> $has_access,
			'link_p'		=> $link_p
		);
	}

	private function _next_landing_page( array $roles, array $default_sys )
	{
		try
		{
			$next_landing_page 	= $this->CI->auth_model->get_modules_for_landing_page( $roles, $default_sys );

			$has_access_sub_arr = array();

			$main_link 			= NULL;

			if( !EMPTY( $next_landing_page ) )
			{
				foreach( $next_landing_page as $page )
				{
					$modules 								= $this->CI->auth_model->get_modules_by_link($page['link']);

					foreach( $modules as $mod )
					{
						$has_access_sub 					= $this->CI->permission->check_permission($mod['module_code']);

						$has_access_sub_arr[ $mod['link'] ][] 	= $has_access_sub;
					}
				}

				if( !EMPTY( $has_access_sub_arr ) )
				{
					foreach( $has_access_sub_arr as $link => $permissions )
					{
						if( !in_array(0, $permissions ) )
						{
							$main_link 	= $link;

							break;
						}
					}
				}
				
				if( !EMPTY( $main_link ) )
				{
					$this->CI->session->set_userdata('redirect_page', $main_link);
				}
				
			}
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}
	}
	
	public function sign_out($user_id)
	{
		try 
		{

			$this->CI->auth_model->update_log($user_id, LOGGED_IN_FLAG_NO);
			

			// DESTROY ALL SESSIONS
			$this->CI->session->sess_destroy();
			
			// CHECK IF SESSION_ID WAS DESTROYED		
			if($this->CI->session->has_userdata('user_id') === FALSE)
				throw new Exception($this->CI->lang->line('system_error'));									
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
	}
	
}