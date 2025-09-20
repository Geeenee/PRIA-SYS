<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_settings_model extends SYSAD_Model {
	
	private $site_settings;
	
	public function __construct()
	{	
		parent::__construct();
		
		$this->site_settings = parent::CORE_TABLE_SITE_SETTINGS;
	}
                
	public function get_site_settings($setting_location = NULL, $setting_type = NULL, $setting_name = NULL)
	{
		try
		{	
			$where = array();
			
			$fields = array("*");
			$multiple = TRUE;
			
			if(!IS_NULL($setting_location))
				$where['setting_location'] = $setting_location;
			
			if(!IS_NULL($setting_type))
				$where['setting_type'] = $setting_type;
			
			if(!IS_NULL($setting_name)){
				$where['setting_name'] = $setting_name;
				$multiple = FALSE;
			}
			
			return $this->select_data($fields, $this->site_settings, $multiple, $where);
			
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
	
	public function update_settings($setting_type, $params, $field)
	{
		try
		{
			$val = array();
			$where = array();
			
			switch($field){
				case 'system_logo':
				  $value = (@getimagesize(base_url(). PATH_SETTINGS_UPLOADS . $params[$field])) ? $params[$field] : "";
				break;
				
				case 'password_expiry':
				  $value = ISSET($params['password_expiry']) ? $params[$field] : 0;
				break;

				case 'change_password_initial_login':
					$value = ISSET($params['change_password_initial_login']) ? 1 : 0;
				break;

				case 'constraint_repeating_characters':
					$value = ISSET($params['constraint_repeating_characters']) ? 1 : 0;
				break;

				case 'log_in_deactivation':
					$value = ISSET($params['log_in_deactivation']) ? 1 : 0;
				break;

				case 'log_in_deactivation_duration':
					$value = EMPTY($params['log_in_deactivation_duration']) ? 0 : $params['log_in_deactivation_duration'];
				break;

				case 'auto_log_inactivity':
					$value = ISSET($params['auto_log_inactivity']) ? 1 : 0;
				break;

				case 'auto_log_inactivity_duration':
					$value = EMPTY($params['auto_log_inactivity_duration']) ? 0 : $params['auto_log_inactivity_duration'];
				break;

				case 'apply_username_constraints':
					$value = ISSET($params['apply_username_constraints']) ? 1 : 0;
				break;

				case 'constraint_pass_diff_username':
					$value = ISSET($params['constraint_pass_diff_username']) ? 1 : 0;
				break;

				case USERNAME_MIN_LENGTH :
					$value = EMPTY($params[USERNAME_MIN_LENGTH]) ? 0 : $params[USERNAME_MIN_LENGTH];
				break;

				case USERNAME_MAX_LENGTH :
					$value = EMPTY($params[USERNAME_MAX_LENGTH]) ? 0 : $params[USERNAME_MAX_LENGTH];
				break;

				case USERNAME_DIGIT :
					$value = EMPTY($params[USERNAME_DIGIT]) ? 0 : $params[USERNAME_DIGIT];
				break;

				case 'username_case_sensitivity' :
					$value = ISSET($params['username_case_sensitivity']) ? 1 : 0;
				break;

				case 'maintenance_mode' :
					$value = ISSET($params['maintenance_mode']) ? 1 : 0;
				break;
				
				case 'password_duration':
				case 'password_reminder':
				  $value = ISSET($params['password_expiry']) ? $params[$field] : "";
				break;

				case 'single_session':
				  $value = ISSET($params['single_session']) ? 1 : 0;
				break;

				case 'sess_expiration_warning' :
					$value = ISSET($params['sess_expiration_warning']) ? 1 : 0;
				break;
				
				case 'password_creator':
					$value = ISSET($params['password_creator']) ? $params[$field] : 0;
					break;
				case 'stages_flag' :
					$value = ISSET($params['stages_flag']) ? 1 : 0;
				break;
				case 'process_flag' :
					$value = 1;
				break;
				case 'steps_flag' :
					$value = 1;
				break;
				case 'prerequisites_flag' :
					$value = 1;
				break;

				case 'stages' :
					$value = ( ISSET($params['stages'] ) AND !EMPTY( $params['stages'] ) ) ? $params[$field] : NULL;
				break;
				case 'process' :
					$value = ( ISSET($params['process']) AND !EMPTY( $params['process'] ) ) ? $params[$field] : NULL;
				break;
				case 'steps' :
					$value = ( ISSET($params['steps'] ) AND !EMPTY($params['steps']) ) ? $params[$field] : NULL;
				break;
				case 'prerequisites' :
					$value = ( ISSET($params['prerequisites']) AND !EMPTY($params['steps']) ) ? $params[$field] : NULL;
				break;

				case 'stages_description' :
					$value = ( ISSET($params['stages_description'] ) AND !EMPTY($params['stages_description']) ) ? $params[$field] : NULL;
				break;
				case 'process_description' :
					$value = ( ISSET($params['process_description']) AND !EMPTY($params['process_description'] ) ) ? $params[$field] : NULL;
				break;
				case 'steps_description' :
					$value = ( ISSET($params['steps_description']) AND !EMPTY($params['steps_description'] ) ) ? $params[$field] : NULL;
				break;
				case 'prerequisites_description' :
					$value = ( ISSET($params['prerequisites_description']) AND !EMPTY($params['prerequisites_description'] ) ) ? $params[$field] : NULL;
				break;

				case 'show_title_on_login' :
					$value = ISSET($params['show_title_on_login']) ? 1 : 0;
				break;
				case 'show_tagline_on_login' :
					$value = ISSET($params['show_tagline_on_login']) ? 1 : 0;
				break;
				case 'change_upload_path' :
					$value = ISSET($params['change_upload_path']) ? 1 : 0;
				break;

				case 'new_upload_path' :
					$value = ( ISSET($params['new_upload_path']) AND !EMPTY($params['new_upload_path']) ) ? $params[$field] : NULL;
				break;

				case 'has_agreement_text':
					$value = ISSET($params['has_agreement_text']) ? 1 : 0;
				break;

				case 'agremment_text':
					$post 	= $this->input->post();

					$value 	= EMPTY($post['agremment_text']) ? '' : htmlentities( $post['agremment_text'] );

					if( !ISSET( $params['has_agreement_text'] ) )
					{
						$value = '';
					}

				break;

				case 'agreement_uploads':

					$value 	= EMPTY($params['agreement_uploads']) ? '' : $params['agreement_uploads'];

					if( !ISSET( $params['has_agreement_text'] ) )
					{	
						$value = '';
					}

				break;
				
				default:
					if( ISSET( $params[$field] ) )
					{
				  		$value = $params[$field];				
				  	}
				  	else
				  	{
				  		$value = '';
				  	}
			}

			$val['setting_value'] = filter_var($value, FILTER_SANITIZE_STRING);
			$where['setting_name'] = $field;
			$where['setting_type'] = $setting_type;
			
			$this->update_data($this->site_settings, $val, $where);
			
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