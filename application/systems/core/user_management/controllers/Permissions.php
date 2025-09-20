<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permissions extends SYSAD_Controller 
{
	
	private $module;
	private $module_per;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->module 	= MODULE_PERMISSION;
		
		$this->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);
		$this->module_per = $this->permission->check_permission( $this->module );
	}
	
	public function index()
	{	
		try
		{
			// $this->redirect_off_system($this->module);
			$this->redirect_module_permission($this->module);

			$data 		= array();
			$resources 	= array();
			$module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_USER_MANAGEMENT."/permissions";
			
			$data['systems'] 			= $this->systems->get_systems();
			$resources['load_css'] 		= array(CSS_SELECTIZE);
			$resources['load_js'] 		= array(JS_SELECTIZE, $module_js);
			$resources['loaded_init'] 	= array(
				'Permissions.initForm();',
				'Permissions.initCheckbox();',
				'Permissions.save();'
			);

			$resources['selectize'] = array (
				'selectize-permissions' => array(
					'type' => 'default'
				)
			);

			$data['module_per']	= $this->module_per;
		}
		catch( PDOException $e )
		{
			$msg = $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg = $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
		
		$this->template->load('permissions', $data, $resources);
	}

	public function _construct_permission_tbody( $params = array(), $modules, $action, $scope, $parent = NULL, $curr_parent=0, $indent=0, $header='' )
	{
		$html 				= '';
		static $deep 		= 0;

		try
		{
			if( !EMPTY( $action['module_action_id'] ) )
			{
				$role_action = explode(',', $action['module_action_id']); 
			}
			else 
			{
				$role_action = array();
			}

			if( !EMPTY( $modules ) )
			{
				foreach( $modules as $key => $val )
				{
					if( !EMPTY( $exempt_modules ) AND in_array($val['module_code'], $exempt_modules) )
			    	{
			    		continue;
			    	}
					
					if( $val['parent_module'] == $parent )
		    		{

		    			$has_children 		= array();

						$has_children['id']	= FALSE;

						$has_children_check = $this->permissions_model->has_children( $val['module_code'] );

						if( $header != $val['system_name'] )
						{
							$html .= '<tr style="background: #6F7D95;">';
								$html .= '<td>';
									$html .= '<h5 class="white-text font-sm font-semibold text-uppercase m-n">'.$val['system_name'].'</h5>';
								$html .= '</td>';

								$html .= '<td>';
								$html .= '<h5 class="white-text font-sm font-semibold text-uppercase m-n"></h5>';
								$html .= '</td>';

								$html .= '<td>';
								$html .= '<h5 class="white-text font-sm font-semibold text-uppercase m-n">Action</h5>';
								$html .= '</td>';

								$html .= '<td>';
								$html .= '<h5 class="white-text font-sm font-semibold text-uppercase m-n">Scoping</h5>';
								$html .= '</td>';
							$html .= '</tr>';

							$header = $val['system_name'];
						}

						if( EMPTY($val['parent_module']) )
						{
							$indent 	 = 0;
							$curr_parent = $val['parent_module'];
							$style 		 = 'style="font-weight : bold;"';
						}
						else
						{
							$indent      = ($curr_parent != $val['parent_module'] AND $deep > 1 ) ? $indent + 30 : $indent;
							$curr_parent = ($curr_parent != $val['parent_module']) ? $val['parent_module'] : $curr_parent;

							if( !EMPTY( $indent ) )
							{
								if( !EMPTY( $has_children_check ) )
								{
									$style 		 = 'style="font-weight : bold;padding-left:'.$indent.'px;"';
								}
								else
								{
									$style 		 = 'style="padding-left:'.$indent.'px;"';
								}
							}
							else
							{
								if( !EMPTY( $has_children_check ) )
								{
									$style 		= 'style="font-weight : bold;"';
								}
								else
								{
									$style 		= '';
								}
							}
						}

						$avail_action_id   = explode(',', $val['available_action_per_module']);
						$avail_action_name = explode(',', $val['action_name']);
						$avail_scope_id    = explode(',', $val['available_scope_per_module']);
						$avail_scope_name  = explode(',', $val['scope_name']);

						$action_arr = $this->_construct_action($val['module_code'], $role_action, $avail_action_id, $avail_action_name);
						$scope_html = ( ! EMPTY($val['available_scope_per_module'])) ?  $this->_construct_scope($val['module_code'], $scope, $avail_scope_id, $avail_scope_name) : '<td></td>';
						$disabled   = ( ! $action_arr['checked']) ? 'disabled' : '';
						
						$checked    = ($action_arr['checked']) ? 'checked' : '';

						$hide 		= '';

						$checkbox = '<input type="checkbox" name="check['.$val['module_code'].']" id="check_'.$val['module_code'].'" class="ind_checkbox filled-in '.$hide.'" '.$checked.' data-checkbox="'.$val['parent_module'].'" />
									  <label class="'.$hide.'" for="check_'.$val['module_code'].'"></label>';

						$html .= '<tr class="'.$val['module_code'].'_tr">';
						$html .= '<td class="p-l-md valign-middle" >';
						$html .= '<input type="hidden" name="check_hide['.$val['module_code'].']" />';
							if( EMPTY($val['parent_module']) )
								$html .= $checkbox;
							else
								$html .= "&nbsp;";
						$html .= '</td>';

						$html .= '<td '.$style.' class="valign-middle">';
							if(!EMPTY($val['parent_module']) )
								$html .= $checkbox;
							
							$html .= str_replace("     ", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $val['module_name']);
						$html .= '</td>';

						$html .= '<td>';
							if( !EMPTY( $val['parent_module'] ) OR EMPTY( $has_children['id'] ) )
							{	
								$html .= '<div>';	
								$html .= '<select class="selectize-permissions" name="module_actions['.$val['module_code'].'][]" id="module_action_'.$val['module_code'].'" multiple '.$disabled.'>';
									$html .= $action_arr['html'];
								$html .= '</select>';
								$html .= '</div>';
							}

						$html .= '</td>';

						if( ! EMPTY($val['available_scope_per_module']) )
						{
							$html .= '<td>';
								if( !EMPTY( $val['parent_module'] ) OR EMPTY( $has_children['id'] ) )
								{
									// $html .= '<div>';
									$html .= '<select class="selectize-permissions" name="module_scopes['.$val['module_code'].'][]" id="module_scope_'.$val['module_code'].'" '.$disabled.' data-parsley-required="true" data-parsley-trigger="change">';
										$html .= $scope_html;
									$html .= '</select>';
									// $html .= '</div>';
								}
							$html .= '</td>';
						}

						$html .= '</tr>';

						++$deep;

						$html .= $this->_construct_permission_tbody( $params, $modules, $action, $scope, $val["module_code"], $curr_parent, $indent, $header );

						--$deep;

		    		}
				}
			}
		}
		catch( PDOException $e )
		{
			$this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$this->rlog_error($e);
		}

		return $html;
	}
	
	public function get_permission()
	{
		try
		{
			// $this->redirect_off_system($this->module);

			$html = '';
			$params = get_params();
			$role = $params['role'];
			$system = $params['system'];
			
			$modules = $this->permissions_model->get_module_action_scopes($system);
			/* GET ROLE ACTIONS */
			$action = $this->permissions_model->get_role_action($role, true);
			
			/* GET ROLE SCOPE */
			$scope = $this->permissions_model->get_role_scope($role, true);

			$html 	 = $this->_construct_permission_tbody( $params, $modules, $action, $scope );
		
			/*$role_action = explode(',', $action['module_action_id']); 
			
			$header = '';
			$indent = 0;
			$curr_parent = 0;
			
			foreach($modules as $key => $val):

				if($header != $val['system_name']):
					$html .= '<tr style="background: #6F7D95;">';
						$html .= '<td colspan="4">';
							$html .= '<p class="white-text font-sm font-semibold text-uppercase m-n">'.$val['system_name'].'</p>';
						$html .= '</td>';
						
						$header = $val['system_name'];
					$html .= '</tr>';
				endif;
				
				if( EMPTY($val['parent_module']) ):
					$indent = 0;
					$curr_parent = $val['parent_module'];
				else:
					$indent = ($curr_parent != $val['parent_module']) ? $indent + 10 : $indent;
					$curr_parent = ($curr_parent != $val['parent_module']) ? $val['parent_module'] : $curr_parent;
				endif;

				$avail_action_id = explode(',', $val['available_action_per_module']);
				$avail_action_name = explode(',', $val['action_name']);
				$avail_scope_id = explode(',', $val['available_scope_per_module']);
				$avail_scope_name = explode(',', $val['scope_name']);
				
				$action_arr = $this->_construct_action($val['module_code'], $role_action, $avail_action_id, $avail_action_name);
				$scope_html = (!EMPTY($val['available_scope_per_module']))?  $this->_construct_scope($val['module_code'], $scope, $avail_scope_id, $avail_scope_name) : '<td></td>';
				$disabled = (!$action_arr['checked'])? 'disabled' : '';
				
				$checked = ($action_arr['checked']) ? 'checked' : '';
				
				$html .= '<tr>';
				$html .= '<td class="p-l-md valign-middle">';
					if( EMPTY($val['parent_module']) ):
						$html .= '<input type="checkbox" name="check['.$val['module_code'].']" id="check_'.$val['module_code'].'" class="ind_checkbox filled-in" '.$checked.'/>
							  <label for="check_'.$val['module_code'].'"></label>';
					endif;
				$html .= '</td>';

				$html .= '<td class="valign-middle">';
					
					if( !EMPTY($val['parent_module']) ):
						$html .= '<div class="inline valign-top"><input type="checkbox" name="check['.$val['module_code'].']" id="check_'.$val['module_code'].'" class="ind_checkbox filled-in" '.$checked.'/>
							  <label for="check_'.$val['module_code'].'"></label></div>';
					endif;
					$html .= $val['module_name'];
				$html .= '</td>';
				
				$html .= '<td>';
					$html .= '<select class="selectize-permissions" name="module_actions['.$val['module_code'].'][]" id="module_action_'.$val['module_code'].'" multiple '.$disabled.'>';
						$html .= $action_arr['html'];
					$html .= '</select>';
				$html .= '</td>';
				
				if( ! EMPTY($val['available_scope_per_module']) ):
					$html .= '<td>';
						$html .= '<select class="selectize-permissions" name="module_scopes['.$val['module_code'].'][]" id="module_scope_'.$val['module_code'].'" '.$disabled.'>';
							$html .= $scope_html;
						$html .= '</select>';
					$html .= '</td>';
				endif;
				
				$html .= '</tr>';
			endforeach;*/
			
			echo $html;
			
		}
		catch(PDOException $e)
		{			
			echo $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			echo $this->rlog_error($e, TRUE);
		}
	}
	
	public function save()
	{
		$prev_detail 			= array();
		$curr_detail 			= array();
		$audit_table 			= array();
		$audit_action 			= array();
		$audit_schema 			= array();

		try
		{
			// $this->redirect_off_system($this->module);

			$msg 	= '';
			$status = ERROR;
			$params = get_params();

			$where_act_role 		= array();
			$where_scope_role 		= array();

			$act_role_arr 			= array();
			$scope_role_arr 		= array();
			
			// SERVER VALIDATION
			$this->_validate($params);
			
			// BEGIN TRANSACTION
			SYSAD_Model::beginTransaction();

			$where_act_role['role_code'] 	= $params['role_filter'];
			$where_scope_role['role_code']	= $params['role_filter'];

			if( ISSET( $params['check_hide'] ) AND !EMPTY( $params['check_hide'] ) )
			{
				$prev_module_act 			= $this->permissions_model->get_role_action_by_module_id(
												array(
													'where'	=> 'WHERE b.role_code = ?',
													'val'	=> array( $params['role_filter'] )
												),
												array_keys( $params['check_hide'] )
											);
			}

			if( !EMPTY( $prev_module_act ) )
			{
				foreach( $prev_module_act as $key => $module_act )
				{
					$act_role_arr[] 	= $module_act['module_action_id'];
				}

				$audit_schema[]	= DB_CORE;
				$audit_table[]	= SYSAD_Model::CORE_TABLE_MODULE_ACTION_ROLES;
				$audit_action[] = AUDIT_DELETE;

				$prev_detail[]	= $this->permissions_model->get_role_action_per( 
					array(  
						'where'	=> 'WHERE role_code = ?',
						'val'	=> array( $params['role_filter'] )
					), 
					$act_role_arr 

				);

				$this->permissions_model->delete_action_role_per( 
					array(  
						'where'	=> 'WHERE role_code = ?',
						'val'	=> array( $params['role_filter'] )
					), 
					$act_role_arr 

				);

				$curr_detail[] 	= array();

			}

			$audit_schema[]	= DB_CORE;
			$audit_table[]	= SYSAD_Model::CORE_TABLE_MODULE_ACTION_ROLES;
			$audit_action[] = AUDIT_INSERT;
			
			$prev_detail[]	= array();	

			$this->permissions_model->insert_action_roles($params['module_actions'], $params['role_filter']);

			$curr_detail[] 	= $this->permissions_model->get_role_action($params['role_filter']);

			if(! EMPTY($params['module_scopes']))
			{
				if( ISSET( $params['check_hide'] ) AND !EMPTY( $params['check_hide'] ) )
				{
					$prev_module_scope 	= $this->permissions_model->get_role_scope_per(
											array(
												'where'	=> 'WHERE role_code = ?',
												'val'	=> array( $params['role_filter'] )
											),
											array_keys( $params['check_hide'] )
										);
				}

				if( !EMPTY( $prev_module_scope ) )
				{
					foreach( $prev_module_scope as $key => $mod_scope ) 
					{
						$scope_role_arr[] 	= $mod_scope['module_code'];
					}

					$audit_schema[]	= DB_CORE;
					$audit_table[]	= SYSAD_Model::CORE_TABLE_MODULE_SCOPE_ROLES;
					$audit_action[] = AUDIT_DELETE;

					$prev_detail[]  = $this->permissions_model->get_role_scope_per( 
						array(  
							'where'	=> 'WHERE role_code = ?',
							'val'	=> array( $params['role_filter'] )
						),  
						$scope_role_arr
					);

					$this->permissions_model->delete_scope_role_per( 
						array(  
							'where'	=> 'WHERE role_code = ?',
							'val'	=> array( $params['role_filter'] )
						),  
						$scope_role_arr
					); 

					$curr_detail[] 	= array();
				}

				$audit_schema[]	= DB_CORE;
				$audit_table[]	= SYSAD_Model::CORE_TABLE_MODULE_SCOPE_ROLES;
				$audit_action[] = AUDIT_INSERT;

				// $prev_detail[]	= $this->pm->get_role_scope($params['role_filter']);
				$prev_detail[] 	= array();

				$this->permissions_model->insert_scope_roles($params['module_scopes'],  $params['role_filter']);

				$curr_detail[] 	= $this->permissions_model->get_role_scope($params['role_filter']);
			}
			
			/*$audit_schema[]	= DB_CORE;
			$audit_schema[]	= DB_CORE;
			
			$audit_table[] = SYSAD_Model::CORE_TABLE_MODULE_SCOPE_ROLES;
			$audit_table[] = SYSAD_Model::CORE_TABLE_MODULE_ACTION_ROLES;
			
			$audit_action[] = AUDIT_UPDATE;
			$audit_action[] = AUDIT_UPDATE;
			
			// GET THE DETAIL FIRST BEFORE UPDATING THE RECORD
			$prev_detail[] = $this->permissions_model->get_role_scope($params['role_filter']);
			$prev_detail[] = $this->permissions_model->get_role_action($params['role_filter']);
			
			$this->permissions_model->delete_action_roles($params['role_filter'], $params['system_filter']);
			$this->permissions_model->delete_scope_roles($params['role_filter'], $params['system_filter']); 
			
			$this->permissions_model->insert_action_roles($params['module_actions'], $params['role_filter']);
			if(! EMPTY($params['module_scopes']))
				$this->permissions_model->insert_scope_roles($params['module_scopes'],  $params['role_filter']);
			
			// GET THE DETAIL AFTER UPDATING THE RECORD
			$curr_detail[] = $this->permissions_model->get_role_scope($params['role_filter']);
			$curr_detail[] = $this->permissions_model->get_role_action($params['role_filter']);*/
			
			// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
			$activity = "updated permission of %s";
			$activity = sprintf($activity, $params['role_filter']);
			
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
			
			SYSAD_Model::commit();
			$status = SUCCESS;
			$msg 	= $this->lang->line('data_saved');
			
		}
		catch(PDOException $e)
		{
			SYSAD_Model::rollback();
			$msg 	= $this->get_user_message($e);
			
		}
		catch(Exception $e)
		{
			SYSAD_Model::rollback();
			$msg 	= $this->rlog_error($e, TRUE);
		}
		
		echo json_encode(array('msg' => $msg, 'status' => $status));
	}
	
	public function reset_options($system_code)
	{		
		// $this->redirect_off_system($this->module);
		
		$list = $this->permissions_model->get_system_roles($system_code);
		
		echo json_encode($list);
	}
	
	private function _construct_action($module_code, $role_action, $avail_action_id, $avail_action_name)
	{
		try
		{
			$html = '';
			$checked = false;
			$html .= '<option value=""></option>';
			foreach($avail_action_id as $key => $ai):
				$selected = (in_array($ai, $role_action)) ? 'selected' : '';
				$checked = (in_array($ai, $role_action)) ? true : $checked;
				
				if( ISSET( $avail_action_name[$key] ) )
				{
					$html .= '<option value="'.$ai.'" '.$selected.'>'.$avail_action_name[$key].'</option>';
				}
			endforeach;
			
			return array('html' => $html, 'checked' => $checked);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
	
	private function _construct_scope($module_code, $scope, $avail_scope_id, $avail_scope_name)
	{
		try
		{
			$html = '';
			$html .= '<option value="">Select Scope</option>';
			
			foreach($avail_scope_id as $key => $as):
					$selected = (ISSET($scope[$module_code]) && $scope[$module_code] == $as) ? 'selected' : '';
				$html .= '<option value="'.$as.'" '.$selected.'>'.$avail_scope_name[$key].'</option>';
			endforeach;
			
			return $html;
		}
		catch(Exception $e)
		{
			throw $e;
		}	
	}
	
	private function _validate($params)
	{

		if(!ISSET($params['role_filter']) || EMPTY($params['role_filter'])) 
			throw new Exception(sprintf($this->lang->line('is_required'), "Role"));
		
		if(!ISSET($params['module_actions']) || EMPTY($params['module_actions'])) 
			throw new Exception('No modules and actions defined.');
		
		if(ISSET($params['check'])){
			foreach($params['check'] as $key => $val):
				if(!ISSET($params['module_actions'][$key])){
					$modules = $this->permissions_model->get_modules($key);	
					throw new Exception(sprintf($this->lang->line('is_required'), "Actions for ".$modules['module_name']));
				}
				
				if(ISSET($params['module_scopes'][$key]) && EMPTY($params['module_scopes'][$key][0])){
					$modules = $this->permissions_model->get_modules($key);
					throw new Exception(sprintf($this->lang->line('is_required'), "Scope for ".$modules['module_name']));
				}
			endforeach;
		}
	}
	
}