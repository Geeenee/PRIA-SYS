<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template {
	
	var $template_data = array();

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Notifications_model', 'nm');
		$this->CI->load->library('Permission');
	}

	/*
	 * @param	string	$value	Value to test for serialized form
	 * @param	mixed	$result	Result of unserialize() of the $value
	 * @return	boolean	True if $value is serialized data, otherwise false
	 */
	public function is_serialized($value, &$result = null)
	{
		// Bit of a give away this one
		if (!is_string($value))
		{
			return false;
		}
		// Serialized false, return true. unserialize() returns false on an
		// invalid string or it could return false if the string is serialized
		// false, eliminate that possibility.
		if ($value === 'b:0;')
		{
			$result = false;
			return true;
		}
		$length	= strlen($value);
		$end	= '';
		if( ISSET($value[0]) )
		{
			switch ($value[0])
			{
				case 's':
					if ($value[$length - 2] !== '"')
					{
						return false;
					}
				case 'b':
				case 'i':
				case 'd':
					// This looks odd but it is quicker than isset()ing
					$end .= ';';
				case 'a':
				case 'O':
					$end .= '}';
					if ($value[1] !== ':')
					{
						return false;
					}
					switch ($value[2])
					{
						case 0:
						case 1:
						case 2:
						case 3:
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
						break;
						default:
							return false;
					}
				case 'N':
					$end .= ';';
					if ($value[$length - 1] !== $end[0])
					{
						return false;
					}
				break;
				default:
					return false;
			}
		}
		if (($result = @unserialize($value)) === false)
		{
			$result = null;
			return false;
		}
		return true;
	}
	
	private function _set($name, $value)
	{
		if( $name == 'resources' )
		{
			$real_v 	= array();

			if( is_array( $value ) )
			{

				foreach( $value as $key => $val )
				{
					if( is_array( $val ) )
					{
						foreach( $val as $k => $v )
						{
							$check_serial 		= $this->is_serialized($v);

							if( $check_serial )
							{
								$uns_v 			= unserialize($v);
								$real_v[$key] 	= array_merge( $real_v[$key], $uns_v );
							}
							else
							{
								if( is_numeric( $k ) )
								{
									$real_v[$key][]		= $v;
								}
								else
								{
									$real_v[$key][$k]	= $v;
								}
							}
						}
					}
					else
					{
						$check_serial_s 	= $this->is_serialized($val);

						if( $check_serial_s )
						{
							$uns_v_s 		= unserialize($val);
							$real_v 		= $uns_v_s;
						}
						else
						{
							$real_v[$key]	= $val;
						}
					}
					
				}

				$this->template_data[$name] = $real_v;
			}
			else
			{
				$this->template_data[$name] = $value;
			}
			
		}
		else
		{
			$this->template_data[$name] = $value;
		}
	}
	
	public function load($view = '' , $data = array(), $resources = array(), $system = SYSTEM_CORE, $override_system_folder = NULL)
	{   
		try 
		{
			$user_id 	= $this->CI->session->user_id;
			$user_roles = $this->CI->session->user_roles;
			$user_orgs 	= $this->CI->session->org_code;

			$contents 	= $this->CI->load->view($view, $data, TRUE);
			$this->_set('contents', $contents);			

			$notifications = $this->CI->nm->get_notifications($user_id, $user_roles, $user_orgs);

			if( !EMPTY( $notifications ) )
			{
				$this->_construct_notification_list( $notifications ); 
			}

			if(!EMPTY($resources))
			{
				$this->_set('resources', $resources);
			}

			$this->CI->load->model(CORE_USER_MANAGEMENT.'/Organizations_model', 'org_temp_mod', TRUE);

			$org_detail 	= $this->CI->org_temp_mod->get_system_owner();

			if( !EMPTY( $org_detail ) )
			{
				$this->template_data['org_sys_owner']	 		= $org_detail['system_owner'];
				$this->template_data['org_template_logo']		= $org_detail['logo'];
				
			}

			$current_system 	= $this->CI->session->current_system;
			$curr_system 		= SYSAD;

			if( !EMPTY( $current_system ) )
			{
				$curr_system 	= $current_system;
			}

			if( !EMPTY( $override_system_folder ) )
			{
				$curr_system 	= $override_system_folder;
			}

			$this->template_data['curr_system']		= $curr_system;
			$this->template_data['ROOT_PATH'] 		= get_root_path();
			
			//permission for quick add
			$this->template_data['per_io_list'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_IO);
			$this->template_data['per_po_list'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_PO);
			$this->template_data['per_po_batch'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_PO_BATCH);
			$this->template_data['per_pr_list'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_PR);
			$this->template_data['per_soa_list'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_SOA);
			$this->template_data['per_soa_batch'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_SOA_BATCH);
			$this->template_data['per_gr_list'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_GR);
			$this->template_data['per_apv_list'] 	= $this->CI->permission->check_permission(MODULE_PORTAL_QA_APV);

			$this->template_data['quick_add_permissions'] = array_filter(array(
					$this->template_data['per_io_list'],
					$this->template_data['per_po_list'],
					$this->template_data['per_po_batch'],
					$this->template_data['per_pr_list'],
					$this->template_data['per_soa_list'],
					$this->template_data['per_soa_batch'],
					$this->template_data['per_gr_list'],
					$this->template_data['per_apv_list']
			));

			$this->CI->load->view('template_'.$system, $this->template_data);
		}
		catch( PDOException $e )
		{
			$message = $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();;
			
			RLog::error($message);

			throw $e;
		}
		catch(Exception $e)
		{
			$message = $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();;
			
			RLog::error($message);

			throw $e;
		}	
	}	

	private function _construct_notification_list( array $notifications )
	{
		$html    = '';
		$unread  = 0;

		try
		{
			/* For notifications */
			
			if( ! EMPTY($notifications['aaData']))
			{
				foreach($notifications['aaData'] as $val)
				{
					//Added by Christian to remove reminders from notification Tab
					//October 24, 2019 9:24am
					//Starts
					if ($val['reminder_flag'] == YES_FLAG) continue;
					//Ends

					//COUNT UNREAD NOTIFICATIONS
					if( EMPTY($val['read_date'] ) ) 
					{
						$unread++;
					}
					

					$msg		= (ISSET($val['notification_html']) AND !EMPTY($val['notification_html']))? $val['notification_html']: "#";
					$time  		= findTimeAgo($val['notification_date']);
					$date  		= $val['notification_date'];
					
					$class = 'title';

					$extra  = '';

					if( EMPTY( $link ) )
					{
						$href  = '';
					}

					$html .= '<li data-notif="'.$val['notification_id'].'" class="collection-item avatar" onclick="Socket_notification.read_notification(this);">';
					//$html .= $msg;

					$html .= '<a href="'.$msg.'">
                                <i class="material-icons circle light-green darken-1">speaker_notes</i>
                                <p>'.$val['notification'].' by '.$val['notified_by_name'].'</p>
                            </a>';
					$html .= '<span class="mute font-xs">'.$time.'</span>';
					$html .= '</li>';
				}
			}
			
			$this->template_data['notif_list']	 = $html;
			$this->template_data['unread_notif'] = $unread;
		}
		catch( PDOException $e )
		{
			$message = $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();;
			
			RLog::error($message);

			throw $e;
		}
		catch(Exception $e)
		{
			$message = $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();;
			
			RLog::error($message);

			throw $e;
		}	

		return array(
			'html' 		=> $html,
			'unread' 	=> $unread
		);

	}

	public function get_notifications_scroll( $page = 0 )
	{
		$html    = '';
		$unread  = 0;

		try
		{
			$user_id 	= $this->CI->session->user_id;
			$user_roles = $this->CI->session->user_roles;
			$user_orgs 	= $this->CI->session->current_org_code;

			$notifications = $this->CI->nm->get_notifications($user_id, $user_roles, $user_orgs, $page);

			$details 		= $this->_construct_notification_list($notifications);

			$html 			= $details['html'];
			$unread 		= $details['unread'];
		}
		catch( PDOException $e )
		{
			$message = $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();;
			
			RLog::error($message);

			throw $e;
		}
		catch(Exception $e)
		{
			$message = $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();;
			
			RLog::error($message);

			throw $e;
		}

		return array(
			'html' 		=> $html,
			'unread' 	=> $unread
		);
	}
		
}