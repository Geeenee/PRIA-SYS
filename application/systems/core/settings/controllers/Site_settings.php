<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_settings extends SYSAD_Controller {

	private $module;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->module = MODULE_SITE_SETTINGS;
		
		$this->load->model('site_settings_model', 'settings', TRUE);
	}
	
	public function index()
	{
		try
		{
			// $this->redirect_off_system($this->module);
			$resources = array();
			
			$module_js = HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_SETTINGS."/settings";
			
			$sidebar_menu 	= get_setting(LAYOUT, "sidebar_menu");
			$skin 			= get_setting(THEME, "skins");
			$header 		= get_setting(LAYOUT, "header");
			$menu_position 	= get_setting(MENU_LAYOUT, "menu_position");
			$menu_display 	= get_setting(MENU_LAYOUT, "menu_child_display");
			$menu_type 		= get_setting(MENU_LAYOUT, "menu_type");
			
			$logo_arr = json_encode(array("id" =>"system_logo", "path" =>  str_replace('\\', '\\\\', PATH_SETTINGS_UPLOADS ) ) );
			$logo_delete_arr = json_encode(array("id" =>"system_logo", "path_images" =>  str_replace('\\', '\\\\', PATH_IMAGES ) , "default_image_preview" => "image_preview.png"));
			$favicon_arr = json_encode(array("id" =>"system_favicon", "path" =>  str_replace('\\', '\\\\', PATH_SETTINGS_UPLOADS ) ) );

			$term_cond_arr 	= json_encode(array("id" =>"terms_conditions", "path" => PATH_TERM_CONDITIONS_UPLOADS, "multiple" => true, 'special' => true));

			$resources['load_css'] = array(CSS_LABELAUTY, CSS_UPLOAD);
			$resources['load_js'] = array(JS_LABELAUTY, JS_UPLOAD, JS_EDITOR, $module_js); 
			$resources['upload'] = array(
				'system_logo' => array(
					'path' 					=> PATH_SETTINGS_UPLOADS, 
					'allowed_types' 		=> 'jpeg,jpg,png,gif',
					'default_img_preview' 	=> 'image_preview.png',
					'page' 					=> 'site_settings',
					// 'auto_submit'			=> false,
					'successCallback'		=> "Settings.successCallback('".$logo_arr."', data);",
					'deleteCallback'		=> "Settings.deleteCallback('".$logo_delete_arr."', data);"
				),
				'system_favicon' => array(
					'path' 					=> PATH_SETTINGS_UPLOADS, 
					'allowed_types' 		=> 'ico',
					'default_img_preview' 	=> 'default_favicon.png',
					'page' 					=> 'site_settings',
					//'auto_submit'			=> false
					'successCallback'		=> "Settings.successCallback('".$favicon_arr."', data);"
				),
				'terms_conditions' 			=> array(
					'path' 					=> PATH_TERM_CONDITIONS_UPLOADS, 
					'allowed_types' 		=> 'pdf,doc,docx,xls,xlsx,csv,ppt,pptx',
					'show_preview'			=> true,
					'max_file' 				=> 5,
					"multiple"  			=> true,
					// 'drag_drop'				=> true,
					'max_file_size'			=> '13107200',
					'successCallback'		=> 'Settings.successCallbackTerm('.$term_cond_arr.', data, files);',
					'deleteCallback'		=> 'Settings.deleteCallbackTerm('.$term_cond_arr.', data);',
					'special' 				=> true
				),
			);
			$resources['loaded_doc_init'] = array(
				'Settings.initSiteSettings("'.$sidebar_menu.'", "'.$skin.'", "'.$header.'", "'.$menu_position.'", "'.$menu_display.'", "'.$menu_type.'");',
				'Settings.saveSiteSettings()'
			);

			$permission 		= $this->permission->check_permission($this->module);

			$data 				= array(
				'permission'	=> $permission
			);
			
			$this->load->view('tabs/site_settings', $data);
			$this->load_resources->get_resource($resources);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index_tab($msg);
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index_tab($msg);
		}
	}

	private function _validate( array $params )
	{
		$required 				= array();
		$constraints 			= array();

		$constraints['system_description']	= array(
			'name'			=> 'Site Description',
			'data_type'		=> 'string',
			'max_len'		=> 255
		);

		$this->check_required_fields( $params, $required );

		$this->validate_inputs( $params, $constraints );
	}
	
	public function process()
	{
		try
		{
			// $this->redirect_off_system($this->module);
			
			$status = ERROR;
			$params	= get_params();
			
			// BEGIN TRANSACTION
			SYSAD_Model::beginTransaction();
			
			$fields = $this->settings->get_site_settings(SITE_APPEARANCE);

			$curr_detail 		= array();

			$this->_validate($params);

			if( !ISSET( $params['has_agreement_text'] ) )
			{
				if( !EMPTY( $params['agreement_uploads'] ) )
				{
					$files = explode('|', $params['agreement_uploads']);

					foreach( $files as $file )
					{
						$file_det = explode('=', $file);

						$path 	= FCPATH.PATH_TERM_CONDITIONS_UPLOADS.$file_det[0];
						$path 	= str_replace( array('/', '\\'), array( DS, DS ), $path );
						if( file_exists( $path ) )
						{
							$this->unlink_attachment($path);
						}
					}
				}
			}
			
			foreach($fields as $field):
			
				$audit_action[]	= AUDIT_UPDATE;
				$audit_table[]	= SYSAD_Model::CORE_TABLE_SITE_SETTINGS;
				$audit_schema[]	= DB_CORE;
				
				// GET THE DETAIL FIRST BEFORE UPDATING THE RECORD
				$prev_detail[] = array( $this->settings->get_site_settings(SITE_APPEARANCE, $field['setting_type'], $field['setting_name']) );
				$this->settings->update_settings($field['setting_type'], $params, $field['setting_name']);
					 
				// GET THE DETAIL AFTER UPDATING THE RECORD
				$curr_detail[] = array( $this->settings->get_site_settings(SITE_APPEARANCE, $field['setting_type'], $field['setting_name']) );			
			endforeach;
			
			// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
			$activity = "%s has been updated";
			$activity = sprintf($activity, "Site settings");
			
			// LOG AUDIT TRAIL
			$this->audit_trail->log_audit_trail(
				$activity,
				$this->module,
				$prev_detail,
				$curr_detail,
				$audit_action,
				$audit_table,
				$audit_schema
			);
				
			$msg = $this->lang->line('data_updated');
			
			SYSAD_Model::commit();
			$status = SUCCESS;
			
		}
		catch(PDOException $e)
		{
			SYSAD_Model::rollback();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			SYSAD_Model::rollback();
			$msg = $this->rlog_error($e, TRUE);
		}
	
		$info = array(
			"status" => $status,
			"msg" => $msg
		);
	
		echo json_encode($info);
	
	}
}