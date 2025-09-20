<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Organizations extends SYSAD_Controller 
{

	const DEFAULT_ORG_LEVEL 	= 1;

	public $hierarchy_msg_map 	= array(); // Msg mag for hierarchy
	
	private $module;
	private $table_id;
	private $path;

	private $view_per 			= FALSE;
	private $edit_per 			= FALSE;
	private $add_per 			= FALSE;
	private $delete_per 		= FALSE;

	private $date_now;
	private $dt_options 		= array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->module 					= MODULE_ORGANIZATION;
		$this->table_id 				= 'organizations_table';
		$this->path 					= CORE_USER_MANAGEMENT.'/organizations/get_organization_list';
		
		$this->load->model('organizations_model', 'orgs');

		$this->hierarchy_msg_map 		= array(
			Organizations_model::DESCENDANTS 		=> $this->lang->line('is_descendants'),
			Organizations_model::ANCESTORS 			=> $this->lang->line('is_ancestor'),
			Organizations_model::SIBLINGS 			=> $this->lang->line('is_sibling'),
		); // Msg mag for hierarchy;

		$this->date_now 		= date('Y-m-d H:i:s');

		$this->view_per 		= $this->permission->check_permission( $this->module, ACTION_VIEW );
		$this->edit_per 		= $this->permission->check_permission( $this->module, ACTION_EDIT );
		$this->add_per 			= $this->permission->check_permission( $this->module, ACTION_ADD );
		$this->delete_per 		= $this->permission->check_permission( $this->module, ACTION_DELETE );

		$this->dt_options 	= array(
			'table_id' 	=> $this->table_id, 
			'path'	 	=> $this->path, 
			'advanced_filter'	=> true, 
			'with_search' => true
		);
	}
	
	public function index()
	{	
		$data 		= array();
		$resources 	= array();
		$module_js 	= HMVC_FOLDER."/".SYSTEM_CORE."/".CORE_USER_MANAGEMENT."/organizations";

		try
		{
			// $this->redirect_off_system($this->module);
			$this->redirect_module_permission($this->module);
			
			$resources['load_css'] 	= array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE);
			$resources['load_js'] 	= array(JS_DATATABLE, JS_DATATABLE_MATERIAL, $module_js);

			$datatable_options 		= $this->dt_options;
			$resources['datatable'] = $datatable_options;

			$resources['load_materialize_modal'] 	= array (
			    'modal_organizations' 				=> array (
					'title' 		=> "Create new organization",
					'size' 			=> "md xl-h",
					'module' 		=> CORE_USER_MANAGEMENT,
					'controller' 	=> __CLASS__
			    )
			);

			$data['add_per'] 		  = $this->add_per;

			$json_datatable_options   = json_encode( $datatable_options );

			$resources['loaded_init'] = array(
				'Organizations.init_obj();',
				"refresh_datatable('".$json_datatable_options."')"
			);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg 	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
		
		$this->template->load('organizations', $data, $resources);
	}

	public function get_organization_list($sector_code=NULL)
	{
		$rows 					= array();
		$flag 					= 0;
		$msg 					= '';
		$output  				= array();

		$resources 				= array();

		try
		{
			// $this->redirect_off_system($this->module);
			
			$params 			= get_params();
				
			$aColumns 			= array('A.org_code', 'd.name as parent_name', 'A.name', 'A.website', 'A.email');
			$bColumns 			= array('name', 'group_types','website', 'email');
				
			$organizations 		= $this->orgs->get_org_list($aColumns, $bColumns, $params, $sector_code);
			$iFilteredTotal 	= $this->orgs->filtered_length($aColumns, $bColumns, $params);
			$iTotal 			= $this->orgs->total_length();

			$output['aaData']				= array();
			$output['sEcho'] 				= intval($params['sEcho']);
			$output['iTotalRecords'] 		= $iTotal["cnt"];
			$output['iTotalDisplayRecords']	= $iFilteredTotal["cnt"];
			
			$keys 				= array_keys($organizations);
			$last_key 			= array_pop($keys);	

			$edit_per 			= $this->permission->check_permission($this->module, ACTION_EDIT);
			$delete_per 		= $this->permission->check_permission($this->module, ACTION_DELETE);

			if( !EMPTY( $organizations ) )
			{
			
				foreach($organizations as $key => $val)
				{
					$actions 		= '';
					$id  			= base64_url_encode($val['org_code']);
					$salt 			= gen_salt();
					$token 			= in_salt($val['org_code'], $salt);
							 
					$del_action 	= 'content_delete(\'Organization\', \''.$id.'\');';	
					$url 			= $id.'/'.$salt.'/'.$token;
					$actions 		.= '<div class="table-actions">';
					
					if( $edit_per )
					{
						$actions 	.= '<a href="#modal_organizations" class="modal_organizations_trigger tooltipped" data-tooltip="Edit" data-position="bottom" data-delay="50" onclick="modal_organizations_init(\''.$url.'\');" ><i class="material-icons">mode_edit</i></a>';
					}
					
					if( $delete_per )
					{
						$actions 	.= '<a href="javascript:;" onclick="'.$del_action.'" class="tooltipped" data-tooltip="Delete" data-position="bottom" data-delay="50"><i class="material-icons">delete</i></a>';
					}
					
					$actions 		.= '</div>';
					
					if($last_key == $key)
					{
						$resources['preload_modal'] = array("modal_organizations");
						$resources['loaded_init'] 	= array("selectize_init();");
						$actions 					.= $this->load_resources->get_resource($resources, TRUE);
					}

					$parent_search 	= array(
						'c.group_type_name', 'd.name'
					);

					$get_parents 	= $this->get_parents($val['org_code'], NULL, $parent_search, $params);

					$parent_str 	= '';

					if( !EMPTY( $get_parents ) )
					{
						$desc_arr 		= $this->process_descendants_arr($get_parents, 'group_type_name', 'parent_name');

						if( !EMPTY( $desc_arr ) )
						{
							foreach( $desc_arr as $grp => $desc )
							{
								$parent_str .= '<b>'.$grp.'</b>
								<br/>
									&nbsp;&nbsp;'.implode(', ', $desc).'
								<br/>
								<br/>
	';
							}
						}
					}
					
					$rows[] = array(
						$val['name'],
						$val['parent_name'],
						$val['website'],
						$val['email'],
						$actions
					);
				}
			}

			$flag 	= 1;
		}
		catch(PDOException $e)
		{			
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		$output['aaData']			= $rows;
		$output['flag']				= $flag;
		$output['msg']				= $msg;
			
		echo json_encode($output);
	}
	
	public function modal($id=NULL, $salt=NULL, $token=NULL)
	{
		try
		{
			// $this->redirect_off_system($this->module);

			$data 			= array();
			$resources 		= array();
			$org_code 		= 0;

			$org_parents 	= array();
			
			if(!EMPTY($id) && !EMPTY($salt) && !EMPTY($token))
			{
				
				$org_code 		= base64_url_decode($id);
				
				// CHECK IF THE SECURITY VARIABLES WERE CORRUPTED OR INTENTIONALLY EDITED BY THE USER
				check_salt($org_code, $salt, $token);
				
				$org_details 			= $this->orgs->get_org_details($org_code);
				$portal_org_details 		= $this->orgs->get_business_center_details($org_code);

				$data['id'] 			= $id;
				$data['salt'] 			= $salt;
				$data['token'] 			= $token;
				$data['org_details'] 	= $org_details;
				$data['portal_org_details'] 	= $portal_org_details;

				//$org_parents 			= $this->get_parents( $org_code );

				$result 	= $this->orgs->get_param_org_types(['org_type_code' => $portal_org_details['org_type_code']], ['parent_org_type_code']);

				$fields 	= [
					'org_code as value',
					'name as text'
				];

				$org_parents 		= $this->orgs->get_organizations(['org_type_code' => $result['parent_org_type_code']], $fields);
			}
			
			$data['other_orgs'] 		= $this->orgs->get_other_orgs($org_code);
			$data['org_parents'] 		= $org_parents;
			$data['org_type_code']		= $this->orgs->get_all_org_types();
			
			$resources['load_css'] 		= array(CSS_SELECTIZE, CSS_LABELAUTY);
			$resources['load_js'] 		= array(JS_SELECTIZE, JS_LABELAUTY, 'add_row');
			$resources['loaded_init'] 	= array(
				'Organizations.init_modal();',
				'Organizations.save();'
			);
			$resources['selectize'] 	= array (
				'selectize-orgs' 		=> array(
					'type' 				=> 'default'
				),
				'org_type_code'			=> array(
					'type'				=> 'default'
				)
			);

			$resources['load_delete']	= array(
				'org_parent'			=> array(
					'delete_cntrl'		=> 'Organizations',
					'delete_method'		=> 'delete_org_parent',
					'delete_module'		=> CORE_USER_MANAGEMENT
				)
			);

			$resources['upload'] 		= array(
				'org_logo'				=> array(
					'path'					=> PATH_ORGANIZATION_UPLOADS,
					'allowed_types' 		=> IMAGE_EXTENSIONS,
					'show_preview'			=> 1,
					'default_img_preview'	=> 'image_preview.png',
					'max_file' 				=> 1,
					'multiple' 				=> 0,
					'successCallback'		=> "Organizations.successCallback(files,data,xhr,pd);",
					'auto_submit'			=> false,
					// 'max_file_size'			=> '13107200',
					'multiple_obj'			=> true,
					'show_download'			=> true,
					'drag_drop' 			=> false,
					'dont_delete_in_server' => true
				)
			);


			$this->load->view("modals/organizations", $data);
			$this->load_resources->get_resource($resources);

		}
		catch(PDOException $e)
		{			
			$msg = $this->get_user_message($e);

			$this->error_modal( $msg );
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);

			$this->error_modal( $msg );
		}
	}
	
	/**
	 * Use This helper function to generate the query string for insert and values
	 * for both org_parents table and org_paths table
	 *
	 *
	 * @param  $params -- required. all the field in the form
	 * @param array $org_code -- required. organization code
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function process_org_parent( array $params, $org_code )
	{
		$org_par_query_str 			= '';
		$org_par_val 				= array();

		$org_path_query_str 		= '';
		$org_path_val 				= array();

		$org_group_type_arr 		= array();

		try
		{

			if( ISSET( $params['org_parents'] ) AND !EMPTY( $params['org_parents'] ) 
				AND ISSET( $params['org_parents'][0] ) AND !EMPTY( $params['org_parents'][0] )
			)
			{
				foreach( $params['org_parents'] as $key => $par_org_code )
				{

					$clean_par 			= filter_var( base64_url_decode( $par_org_code ), FILTER_SANITIZE_STRING );
					$clean_group 		= filter_var( base64_url_decode( $params['org_group_type'][ $key ] ), FILTER_SANITIZE_STRING );

					$org_group_type_arr[] = $clean_group;

					$this->validate_root( $org_code, $clean_par, $clean_group );

					$org_par_query_str .= '(?,?,?),';

					$org_par_val[] 		= $org_code;
					$org_par_val[] 		= $clean_par;
					$org_par_val[] 		= $clean_group;

					$org_parent_det 	= $this->orgs->check_parent_org_has_parent( $clean_par, $clean_group );

					if( !EMPTY( $org_parent_det ) )
					{
						foreach( $org_parent_det as $det_key => $org_par_det )
						{
							$org_path_query_str.= '(?,?,?,?,?),';
							$org_path_val[] 	= $org_code;
							$org_path_val[] 	= $clean_par;
							$org_path_val[] 	= $org_par_det['group_type'];
							$org_path_val[] 	= ( $org_par_det['org_level'] + 1 );
							$org_path_val[] 	= $org_par_det['org_root'];
						}
					}
					else
					{
						$org_path_query_str.= '(?,?,?,?,?),';

						$org_path_val[] 	= $org_code;
						$org_path_val[] 	= $clean_par;
						$org_path_val[] 	= $clean_group;
						$org_path_val[] 	= self::DEFAULT_ORG_LEVEL;
						$org_path_val[] 	= $clean_par;
					}
				}

				$org_par_query_str 		= rtrim( $org_par_query_str, ',' );
				$org_path_query_str 	= rtrim( $org_path_query_str, ',' );
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

		return array(
			'org_par_query_str'		=> $org_par_query_str,
			'org_par_val'			=> $org_par_val,
			'org_path_query_str' 	=> $org_path_query_str,
			'org_path_val' 			=> $org_path_val,
			'org_group_type_arr'	=> $org_group_type_arr
		);
	}
	
	/**
	 * Use This helper function to generate the query string for insert and values
	 * for org_paths table
	 *
	 *
	 * @param  $details -- required. detail of the organization, its parent and its org_paths 
	 * @param  $par_detail -- optional. additional value 
	 * 						if the following key or details is existing then it will override the details
	 * @param  $action -- required. default ACTION_ADD. what action
	 * @param  $check_lev_var -- required. default FALSE. if organization level is 1
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function process_org_path( array $details, array $par_detail = array(), $action = ACTION_ADD, $check_lev_var = FALSE )
	{
		$val 		= array();
		$str 		= '';

		if( !EMPTY( $details ) )
		{
			foreach( $details as $det )
			{
				$str 	.= '(?,?,?,?,?),';

				if( !EMPTY( $par_detail['org_code'] ) AND ISSET( $par_detail['org_code'] ) )
				{
					$val[] 	= $par_detail['org_code'];
				}
				else
				{
					$val[] 	= $det['org_code'];
				}

				if( !EMPTY( $par_detail['org_parent'] ) AND ISSET( $par_detail['org_parent'] ) )
				{
					$val[] 	= $par_detail['org_parent'];
				}
				else
				{
					$val[] 	= $det['org_parent'];
				}

				if( !EMPTY( $par_detail['group_type'] ) AND ISSET( $par_detail['group_type'] ) )
				{
					$val[] 	= $par_detail['group_type'];
				}
				else
				{
					$val[] 	= $det['group_type'];
				}

				if( $action != ACTION_ADD OR !EMPTY( $check_lev_var ) )
				{
					$val[] 	= ( $det['org_level'] - 1 );
				}
				else
				{
					$val[] 	= ( $det['org_level'] + 1 );
				}

				if( !EMPTY( $par_detail['org_root'] ) AND ISSET( $par_detail['org_root'] ) )
				{
					$val[] 	= $par_detail['org_root'];
				}
				else
				{
					$val[] 	= $det['org_root'];
				}
			}

			$str 		= rtrim( $str, ',' );
		}

		return array(
			'str'		=> $str,
			'val'		=> $val
		);
	}
	
	/**
	 * Use This helper function to insert the org paths per sub organization of the main organization selected
	 *
	 *
	 * @param  $details -- required. detail of the organization, its parent and its org_paths
	 * @param  $par_detail -- optional. additional value
	 * 						if the following key or details is existing then it will override the details
	 * @param  $action -- required. default ACTION_ADD. what action
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function do_process_insert_sub_org( array $details, array $audit_where, array $par_detail = array(), $action = ACTION_ADD )
	{
		$prev_detail 			= array();
		$curr_detail 			= array();
		$audit_table 			= array();
		$audit_action 			= array();
		$audit_schema 			= array();

		try
		{

			if( !EMPTY( $details ) )
			{
				foreach( $details as $det )
				{
					$str 		= '(?,?,?,?,?)';
					$val 		= array();

					if( !EMPTY( $par_detail['org_code'] ) AND ISSET( $par_detail['org_code'] ) )
					{
						$val[] 		= $par_detail['org_code'];
						$org_code 	= $par_detail['org_code'];
					}
					else
					{
						$val[] 		= $det['org_code'];
						$org_code 	= $det['org_code'];
					}

					if( !EMPTY( $par_detail['org_parent'] ) AND ISSET( $par_detail['org_parent'] ) )
					{
						$val[] 			= $par_detail['org_parent'];
						$par_org_code 	= $par_detail['org_parent'];
					}
					else
					{
						$val[] 			= $det['org_parent'];
						$par_org_code 	= $det['org_parent'];
					}

					if( !EMPTY( $par_detail['group_type'] ) AND ISSET( $par_detail['group_type'] ) )
					{
						$val[] 			= $par_detail['group_type'];
						$group_type 	= $par_detail['group_type'];
					}
					else
					{
						$val[] 			= $det['group_type'];
						$group_type 	= $det['group_type'];
					}

					if( $action != ACTION_ADD )
					{
						$val[] 		= ( $det['org_level'] - 1 );
						$org_level 	= ( $det['org_level'] - 1 );
					}
					else
					{
						$val[] 		= ( $det['org_level'] + 1 );
						$org_level 	= ( $det['org_level'] + 1 );
					}

					if( !EMPTY( $par_detail['org_root'] ) AND ISSET( $par_detail['org_root'] ) )
					{
						$val[] 		= $par_detail['org_root'];
						$org_root 	= $par_detail['org_root'];
					}
					else
					{
						$val[] 		= $det['org_root'];
						$org_root 	= $det['org_root'];
					}

					if( !EMPTY( $val ) )
					{
						$check_where 		= array(
							'org_code'		=> $org_code,
							'org_parent'	=> $par_org_code,
							'group_type'	=> $group_type,
							'org_level'		=> $org_level,
							'org_root'		=> $org_root
						);

						$check_exists 		= $this->orgs->check_org_path_exisits( $check_where );

						if( !EMPTY( $check_exists ) AND !EMPTY( $check_exists['check_exists'] ) )
						{
							continue;
						}

						$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
						$audit_schema[]		= DB_CORE;

						$prev_detail[] 		= array();
						$audit_action[] 	= AUDIT_INSERT;

						$this->orgs->insert_org_paths( $str, $val );

						$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $audit_where );
					}
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

		return array(
			'audit_schema'			=> $audit_schema,
			'audit_table' 			=> $audit_table,
			'audit_action' 			=> $audit_action,
			'prev_detail'			=> $prev_detail,
			'curr_detail' 			=> $curr_detail
		);
	}
	
	/**
	 * Use This helper function to insert the org paths of the org root then update the its child organization
	 * org path usually used when the org root will change per group type 
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $action -- required. default ACTION_ADD. what action
	 * @param  $org_code_for_del -- optional. default value NULL
	 * 						if the following details is not empty then it will override the org_code parameter
	 * @param  $group_type -- optional. default value NULL. what is the group type of the main organization selected
	 * @param  $check_lev_var -- optional. default value FALSE. if the main organization selected is level 1
	 * @param  $org_det -- optional. default value array. detail main organization selected 						
	 * 
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function process_org_root_del_ins( $org_code, $action = ACTION_ADD, $org_code_for_del = NULL, $group_type = NULL, $check_lev_var = FALSE, $org_det = array() )
	{
		$prev_detail 			= array();
		$curr_detail 			= array();
		$audit_table 			= array();
		$audit_action 			= array();
		$audit_schema 			= array();

		try
		{
			$orig_org_code 		= $org_code;

			if( !EMPTY( $check_lev_var ) )
			{
				if( !EMPTY( $org_det ) )
				{
					$org_code 	= $org_det['org_root'];
				}
			}

			$root_childrens 	= $this->orgs->get_root_children( $org_code, $group_type );

			if( !EMPTY( $root_childrens ) )
			{
				$root_where 	= array(
					'org_root'	=> $org_code
				);

				if( $action == ACTION_DELETE )
				{
					$root_code['org_root']	= $org_code_for_del;
				}
				else
				{
					$org_code_where 				= array();
					$org_code_where['a.org_code'] 	= $org_code;

					$root_code 				= $this->orgs->get_org_path_details_helper( $org_code_where );
				}

				if( !EMPTY( $check_lev_var ) )
				{
					if( !EMPTY( $org_det ) )
					{
						$root_code['org_root'] 		= $orig_org_code;
					}

				}

				$root_str 				= '';
				$root_val 				= array();

				if( !EMPTY( $root_code ) )
				{
					$audit_action[]		= AUDIT_DELETE;
					$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
					$audit_schema[]		= DB_CORE;

					$prev_detail[] 		= $this->orgs->get_org_root_audit( $org_code, $group_type );
					
					$this->orgs->delete_root_helper( $org_code, $group_type );
					
					$curr_detail[] 		= array();

					$root_ins_det 		= $this->process_org_path( $root_childrens, array( 'org_root' => $root_code['org_root']  ), $action, $check_lev_var );

					$root_str 			= $root_ins_det['str'];
					$root_val 			= $root_ins_det['val'];
					
					if( !EMPTY( $root_str ) )
					{
						$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
						$audit_schema[]		= DB_CORE;

						$prev_detail[] 		= array();
						$audit_action[] 	= AUDIT_INSERT;

						$this->orgs->insert_org_paths( $root_str, $root_val );

						$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $root_where );
					}
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

		return array(
			'audit_schema'			=> $audit_schema,
			'audit_table' 			=> $audit_table,
			'audit_action' 			=> $audit_action,
			'prev_detail'			=> $prev_detail,
			'curr_detail' 			=> $curr_detail
		);
	}

	/**
	 * Use This helper function to update the org paths of the child org of the main organization selected.
	 * Usually used when adding a parent to the main organization selected 
	 *
	 *
	 * @param  $params -- required. form fields
	 * @param  $org_code -- required. organization code
	 * @param  $action -- required. default ACTION_ADD. what action
	 * @param  $group_type -- optional. default value NULL. what is the group type of the main organization selected
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function process_sub_org_del_ins( array $params, $org_code, $action = ACTION_ADD, $group_type = NULL )
	{
		$prev_detail 			= array();
		$curr_detail 			= array();
		$audit_table 			= array();
		$audit_action 			= array();
		$audit_schema 			= array();

		try
		{
			$not_parent_str 	= '';
			$group_str 			= '';
			$root_str 			= '';

			$not_par_val 		= array();
			$group_val 			= array();
			$root_val 			= array();

			if( ISSET( $params['org_parents'] ) AND !EMPTY( $params['org_parents'] ) 
				AND ISSET( $params['org_parents'][0] ) AND !EMPTY( $params['org_parents'][0] )
			)
			{
				foreach( $params['org_parents'] as $key => $par_org_code )
				{
					$clean_par 			= filter_var( base64_url_decode( $par_org_code ), FILTER_SANITIZE_STRING );

					$not_parent_str 	.= '?,';
					$not_par_val[] 		= $clean_par;

					$per_org_det 		= $this->orgs->get_root_org_code( $clean_par, $group_type );
					

					if( !EMPTY( $per_org_det ) )
					{
						foreach( $per_org_det as $s_k => $det )
						{
							$group_str 	.= '?,';
							$root_str 	.= '?,';

							$group_val[] = $det['group_type'];
							$root_val[]  = $det['org_root'];
						}
					}
					else
					{
						$root_str 	.= '?,';
						$root_val[]  = $clean_par;

						if( !EMPTY(  $group_type ) )
						{
							if( is_array( $group_type ) )
							{
								$count_as_type  = count( $group_type );

								$group_str 		.= str_repeat( '?,', $count_as_type );

								$group_val 		= array_merge( $group_val, $group_type );
							}
							else
							{
								$group_str 	.= '?,';
								$group_val[] = $group_type;
							}
						}
					}
				}

				$not_parent_str 		= rtrim( $not_parent_str, ',' );
				$group_str 				= rtrim( $group_str, ',' );
				$root_str 				= rtrim( $root_str, ',' );

			}

			if( !EMPTY( $not_parent_str ) AND !EMPTY( $group_str ) AND !EMPTY( $root_str ) )
			{
				$descendants 			= $this->orgs->get_descendants_update( $org_code, $not_parent_str, $not_par_val, $root_str, $root_val, $group_str, $group_val );
				
				if( !EMPTY( $descendants ) )
				{

					foreach( $descendants as $d_k => $desc )
					{
						$desc_str  				= '';
						$desc_val  				= array();

						$desc_where 			= array(
							'org_code'		=> $desc['org_code'],
							'group_type'	=> $desc['group_type']
						);

						$org_parent_det 	= $this->orgs->check_parent_org_has_parent( $desc['org_parent'], $desc['group_type'] );
						// var_dump($org_parent_det);
						
						$audit_action[]		= AUDIT_DELETE;
						$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
						$audit_schema[]		= DB_CORE;

						$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $desc_where );

						$this->orgs->delete_helper( SYSAD_Model::CORE_TABLE_ORG_PATHS, $desc_where );

						$curr_detail[] 		= array();
						
						if( !EMPTY( $org_parent_det ) )
						{
							/*$ins_sub_org_det 	= $this->process_org_path( $org_parent_det, $desc );
							$desc_str 		 	= $ins_sub_org_det['str'];
							$desc_val 			= $ins_sub_org_det['val'];*/

							$ins_sub_org_real 	= $this->do_process_insert_sub_org( $org_parent_det, $desc_where, $desc, $action );

							$audit_schema 		= array_merge( $audit_schema, $ins_sub_org_real['audit_schema'] );
							$audit_table 		= array_merge( $audit_table, $ins_sub_org_real['audit_table'] );
							$audit_action 		= array_merge( $audit_action, $ins_sub_org_real['audit_action'] );
							$prev_detail 		= array_merge( $prev_detail, $ins_sub_org_real['prev_detail'] );
							$curr_detail 		= array_merge( $curr_detail, $ins_sub_org_real['curr_detail'] );
						}

					/*	if( !EMPTY( $desc_str ) )
						{
							$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
							$audit_schema[]		= DB_CORE;

							$prev_detail[] 		= array();
							$audit_action[] 	= AUDIT_INSERT;

							$this->orgs->insert_org_paths( $desc_str, $desc_val );

							$curr_detail[] 		= $this->orgs->select_helper(
								array('*'), SYSAD_Model::CORE_TABLE_ORG_PATHS, $desc_where, TRUE
							);
						}*/
					}
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

		return array(
			'audit_schema'			=> $audit_schema,
			'audit_table' 			=> $audit_table,
			'audit_action' 			=> $audit_action,
			'prev_detail'			=> $prev_detail,
			'curr_detail' 			=> $curr_detail
		);
	}
	
	public function process()
	{
		$org_code 	= NULL;
		$org_dec 	= NULL;

		$security_detail 	= array();

		$path 				= NULL;
		$sess_org_code 	 	= NULL;

		try
		{
			// $this->redirect_off_system($this->module);

			$status 	= ERROR;
			$params 	= get_params();
			//print_var_export($params); die;
			// SERVER VALIDATION
			$this->_validate($params);
			
			// GET SECURITY VARIABLES
			$id			= $params['id'];
			$salt 		= $params['salt'];
			$token 		= $params['token'];			
			
			// BEGIN TRANSACTION
			SYSAD_Model::beginTransaction();
			// print_r($params);
			$check_lev 	= array();
			
			if(!EMPTY($id) && !EMPTY($salt) && !EMPTY($token))
			{
				$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORGANIZATIONS;
				$audit_schema[]		= DB_CORE;

				$audit_action[] 	= AUDIT_UPDATE;
				
				$org_code 			= base64_url_decode($id);
					
				// CHECK IF THE SECURITY VARIABLES WERE CORRUPTED OR INTENTIONALLY EDITED BY THE USER
				check_salt($org_code, $salt, $token);
				
				// GET THE DETAIL FIRST BEFORE UPDATING THE RECORD
				$prev_org_upd 		= $this->orgs->get_org_details($org_code);
				$prev_detail[] 		= array($prev_org_upd);
				
				$this->orgs->update_org($params, $org_code);

				$msg = $this->lang->line('data_updated');
				
				// GET THE DETAIL AFTER UPDATING THE RECORD
				$curr_org_det 		= $this->orgs->get_org_details($org_code);
				$curr_detail[] 		= array($curr_org_det);

				if( EMPTY( $params['org_logo'] ) )
				{
					$main_where 		= array(
						'org_code'		=> $org_code
					);

					$audit_schema[] 	= DB_CORE;
					$audit_table[] 	 	= SYSAD_Model::CORE_TABLE_ORGANIZATIONS;
					$audit_action[] 	= AUDIT_UPDATE;
					$prev_detail[]  	= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORGANIZATIONS,
						$main_where
					);

					$upd_val['modified_by'] 	= $this->session->user_id;
					$upd_val['modified_date'] 	= $this->date_now;
					$upd_val['logo'] 			= NULL;
					$upd_val['logo_orig_name'] 	= NULL;

					$path 				= base_url().PATH_IMAGES.DEFAULT_ORG_LOGO;

					$this->orgs->update_helper( SYSAD_Model::CORE_TABLE_ORGANIZATIONS, $upd_val, $main_where );

					$fields	= array(
						"org_type_code"		=> $params['org_type_code'],
						"org_parent"		=> ( ! EMPTY($params['parent_org_code'])) ? $params['parent_org_code'] : NULL,
						"short_name" 		=> $params['org_short_name'],
						"name" 				=> $params['org_name'],
						"website" 			=> $params['website'],
						"email" 			=> $params['email'],
						"phone" 			=> $params['tel_no'],
						"fax" 				=> $params['fax_no'],
						"logo"				=> NULL,
						"logo_orig_name"	=> NULL,
						"modified_by"		=> $this->session->userdata('user_id'),
						"modified_date"		=> date('Y-m-d H:i:s')
					);

					$this->orgs->update_portal_organization($fields, $main_where);

					$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORGANIZATIONS,
						$main_where
					);
				}
				else
				{
					if( !EMPTY( $curr_org_det['logo'] ) )
					{
						$root_path 			= $this->get_root_path();
						$org_pic_path 		= $root_path. PATH_ORGANIZATION_UPLOADS . $curr_org_det['logo'];
						$org_pic_path 		= str_replace(array('\\','/'), array(DS,DS), $org_pic_path);
						
						if( file_exists( $org_pic_path ) )
						{
							$org_pic_src	= output_image( $curr_org_det['logo'], PATH_ORGANIZATION_UPLOADS );
							$path 	 	 	= $org_pic_src;
						}
					}
				}

				$sess_org_code 		= $this->session->org_code;
				$org_dec 			= $org_code;

				if( $curr_org_det['system_owner'] == ENUM_NO )
				{
					$sys_logo 		 = get_setting(GENERAL, "system_logo");

					if( !EMPTY( $sys_logo ) )
					{
						$root_path 			= $this->get_root_path();

						$sys_logo_path 		= $root_path. PATH_SETTINGS_UPLOADS . $sys_logo;
						$sys_logo_path 		= str_replace(array('\\','/'), array(DS,DS), $sys_logo_path);

						if( file_exists( $sys_logo_path ) )
						{

							//$system_logo_src = output_image( $system_logo, PATH_SETTINGS_UPLOADS ); Changed by kebs - kasi nag undefined error saken. Pakireplace if mali.
							$system_logo_src = output_image( $sys_logo, PATH_SETTINGS_UPLOADS );
							$system_logo_src = @getimagesize($sys_logo_path) ? $system_logo_src : base_url() . PATH_IMAGES . "logo_white.png";

							$path 			= $system_logo_src;
						}
						else
						{
							$path 			= base_url() . PATH_IMAGES . "logo_white.png";
						}
					}
				}

/* 				$par_org_code_val 	= $this->process_org_parent( $params, $org_code );
				
				$par_where 			= array();

				$par_where['org_code']	= $org_code;

				$check_org_code_lev 	= $this->get_paths( $org_code, $par_org_code_val['org_group_type_arr'] );
				
				$check_lev 				= array();
				
				if( !ISSET( $params['has_parent'] ) OR EMPTY( $params['has_parent'] ) )
				{
					if( !EMPTY( $check_org_code_lev ) )
					{
						foreach( $check_org_code_lev as $lev )
						{
							if( $lev['org_level'] != 1 )
							{
								$check_lev[] 	= 0;
							}
							else
							{
								$check_lev[] 	= 1;
							}
						}
					}

					$check_descendants 	= $this->orgs->get_descendants( $org_code, Organizations_model::DESCENDANTS );

					if( !EMPTY( $check_descendants ) AND in_array( 0, $check_lev ) )
					{
						throw new Exception( sprintf( $this->lang->line('multiple_root_organization'), '' ) );
					}
				}
				else
				{
					$check_lev[] 	= 0;
				}

				$org_det 			= $this->orgs->get_org_path_details($org_code);

				$audit_action[]		= AUDIT_DELETE;
				$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
				$audit_schema[]		= DB_CORE;

				$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $par_where );

				$this->orgs->delete_org_paths( $org_code );

				$curr_detail[] 		= array();

				$audit_action[]		= AUDIT_DELETE;
				$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PARENTS;
				$audit_schema[]		= DB_CORE;

				$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PARENTS, $par_where );

				$this->orgs->delete_org_parents( $par_where );

				$curr_detail[] 		= array();

				if( !EMPTY( $par_org_code_val['org_par_query_str'] ) AND ISSET( $params['has_parent'] ) )
				{
					$par_where 			= array();

					$par_where['org_code']	= $org_code;

					$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PARENTS;
					$audit_schema[]		= DB_CORE;

					$prev_detail[] 		= array();
					$audit_action[] 	= AUDIT_INSERT;

					$this->orgs->insert_org_parents( $par_org_code_val['org_par_query_str'], $par_org_code_val['org_par_val'] );

					$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PARENTS, $par_where );
				}

				if( !EMPTY( $par_org_code_val['org_path_query_str'] ) AND ISSET( $params['has_parent'] ) )
				{
					$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
					$audit_schema[]		= DB_CORE;

					$prev_detail[] 		= array();
					$audit_action[] 	= AUDIT_INSERT;

					$this->orgs->insert_org_paths( $par_org_code_val['org_path_query_str'], $par_org_code_val['org_path_val'] );

					$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $par_where );
				} */
				
				// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
				// $activity 			= $this->lang->line('audit_trail_update');
				$activity 				= "updated the details of organization ( %s ).";
				$activity 				= sprintf($activity, $params['org_name']);
			}
			else
			{
				$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORGANIZATIONS;
				$audit_schema[]		= DB_CORE;

				$prev_detail[] 		= array();
				$audit_action[] 	= AUDIT_INSERT;
				
				$fields	= array(
					"org_code"			=> $params['org_code'],
					"org_parent"		=> ( ! EMPTY($params['parent_org_code'])) ? $params['parent_org_code'] : NULL,
					"org_type_code"		=> $params['org_type_code'],
					"short_name" 		=> $params['org_short_name'],
					"name" 				=> $params['org_name'],
					"website" 			=> $params['website'],
					"email" 			=> $params['email'],
					"phone" 			=> $params['tel_no'],
					"fax" 				=> $params['fax_no'],
					"created_by"		=> $this->session->userdata('user_id'),
					"created_date"		=> date('Y-m-d H:i:s')
				);
				
				$this->orgs->insert_to_portal_organization($fields);
			
				$org_code 			= $this->orgs->insert_org($params);
				$msg 				= $this->lang->line('data_saved');

				$org_det 			= $this->orgs->get_org_path_details($org_code);
				
				// GET THE DETAIL AFTER INSERTING THE RECORD
				$curr_detail[] 		= array($this->orgs->get_org_details($org_code));

				// $change_level 		= 

			/* 	$par_org_code_val 	= $this->process_org_parent( $params, $org_code );

				if( !EMPTY( $par_org_code_val['org_par_query_str'] ) AND ISSET( $params['has_parent'] ) )
				{
					$par_where 			= array();

					$par_where['org_code']	= $org_code;

					$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PARENTS;
					$audit_schema[]		= DB_CORE;

					$prev_detail[] 		= array();
					$audit_action[] 	= AUDIT_INSERT;

					$this->orgs->insert_org_parents( $par_org_code_val['org_par_query_str'], $par_org_code_val['org_par_val'] );

					$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PARENTS, $par_where );
				}

				if( !EMPTY( $par_org_code_val['org_path_query_str'] ) AND ISSET( $params['has_parent'] ) )
				{
					$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
					$audit_schema[]		= DB_CORE;

					$prev_detail[] 		= array();
					$audit_action[] 	= AUDIT_INSERT;

					$this->orgs->insert_org_paths( $par_org_code_val['org_path_query_str'], $par_org_code_val['org_path_val'] );

					$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $par_where );
				} */

				// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
				$activity 				= "created a new organization ( %s ).";
				$activity 				= sprintf($activity, $params['org_name']);
			}

			$security_detail 		= $this->generate_salt_token_arr( $org_code );

		/* 	if( !EMPTY( $org_code ) )
			{
				$check_root 		= $this->orgs->check_root( $org_code );
				
				if( !EMPTY( $check_root ) AND !EMPTY( $check_root['check_root'] ) OR !in_array(0, $check_lev ) )
				{
					$check_lev_var 		= !in_array(0, $check_lev );

					$root_audit_details = $this->process_org_root_del_ins( $org_code, ACTION_ADD, NULL, $par_org_code_val['org_group_type_arr'], $check_lev_var, $org_det );

					$audit_schema 		= array_merge( $audit_schema, $root_audit_details['audit_schema'] );
					$audit_table 		= array_merge( $audit_table, $root_audit_details['audit_table'] );
					$audit_action 		= array_merge( $audit_action, $root_audit_details['audit_action'] );
					$prev_detail 		= array_merge( $prev_detail, $root_audit_details['prev_detail'] );
					$curr_detail 		= array_merge( $curr_detail, $root_audit_details['curr_detail'] );
				}
				
				$sub_org_audit 			= $this->process_sub_org_del_ins( $params, $org_code, ACTION_ADD, $par_org_code_val['org_group_type_arr'] );

				$audit_schema 			= array_merge( $audit_schema, $sub_org_audit['audit_schema'] );
				$audit_table 			= array_merge( $audit_table, $sub_org_audit['audit_table'] );
				$audit_action 			= array_merge( $audit_action, $sub_org_audit['audit_action'] );
				$prev_detail 			= array_merge( $prev_detail, $sub_org_audit['prev_detail'] );
				$curr_detail 			= array_merge( $curr_detail, $sub_org_audit['curr_detail'] );
			} */
			// throw new Exception("Error Processing Request");
			// throw new Exception('audit_trail_add');
			
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
			$status 	= SUCCESS;
			
		}
		catch(PDOException $e)
		{
			SYSAD_Model::rollback();
			$msg 		= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			SYSAD_Model::rollback();
			$msg 		= $this->rlog_error($e, TRUE);
		}

		$response 		= array(
			'status' 	=> $status,
			'msg' 		=> $msg,
			'datatable_options' => $this->dt_options,
			'sess_org_code'	=> $sess_org_code,
			'path' 			=> $path,
			'org_dec' 		=> $org_dec
		);

		if( !EMPTY( $security_detail ) )
		{
			$response['org_code'] 		= $security_detail['id_enc'];
			$response['org_salt'] 		= $security_detail['salt'];
			$response['org_token'] 		= $security_detail['token'];
		}
		
		echo json_encode( $response );
	}
	
	public function delete_organization()
	{
		try
		{
			// $this->redirect_off_system($this->module);

			$status 		= ERROR;
			$params 		= get_params();
			$org_code 		= base64_url_decode($params['param_1']);

			// BEGIN TRANSACTION
			SYSAD_Model::beginTransaction();

			$par_where 			= array();

			$par_where['org_code']	= $org_code;

			$audit_action[]		= AUDIT_DELETE;
			$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
			$audit_schema[]		= DB_CORE;

			$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $par_where );
			
			$this->orgs->delete_org_paths( $org_code );

			$curr_detail[] 		= array();

			$audit_action[]		= AUDIT_DELETE;
			$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PARENTS;
			$audit_schema[]		= DB_CORE;

			$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PARENTS, $par_where );

			$this->orgs->delete_org_parents( $par_where );

			$curr_detail[] 		= array();
			
			$audit_action[]		= AUDIT_DELETE;
			$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORGANIZATIONS;
			$audit_schema[]		= DB_CORE;
			
			// GET THE DETAIL FIRST BEFORE DELETING THE RECORD
			$prev_or 			= $this->orgs->get_org_details($org_code);
			$prev_detail[] 		= array($prev_or);
			
			$this->orgs->delete_org($org_code);
			$this->orgs->delete_portal_org($org_code);


			$msg 				= $this->lang->line('data_deleted');
			
			$curr_detail[] 		= array();
			// ACTIVITY TO BE LOGGED ON THE AUDIT TRAIL
			$activity 			= "deleted an organization ( %s ).";
			$activity 			= sprintf($activity, $prev_or['short_name']);
				
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

			$root_path = $this->get_root_path();
			$path_dir = $root_path. PATH_ORGANIZATION_UPLOADS. $prev_or['logo'];
			$path_dir = str_replace(array('\\','/'), array(DS,DS), $path_dir);

			if( !EMPTY( $path_dir ) )
			{
				$this->unlink_attachment( $path_dir );
			}
				
			SYSAD_Model::commit();
			$status 	= SUCCESS;
			
		}
		catch(PDOException $e)
		{
			SYSAD_Model::rollback();
			$msg 		= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			SYSAD_Model::rollback();
			$msg 		= $this->rlog_error($e, TRUE);
		}

		$response 		= array(
			'status' 	=> $status,
			'msg' 		=> $msg,
			'reload' 	=> 'datatable',
			'datatable_options' 	=> $this->dt_options
		);
	
		echo json_encode( $response );
	}
	
	/**
	 * Use This function to delete the org parent of the main organization selected
	 * Also automatically update the org path of the following organization affected
	 *
	 *
	 * @throws PDOException
	 * @throws Exception
	 */
	public function delete_org_parent()
	{
		$flag 		= 0;
		$msg 		= '';
		$status 	= ERROR;

		$org_code 		= NULL;
		$org_salt 		= NULL;
		$org_token  	= NULL;

		$par_org_code 	= NULL;
		$par_org_salt 	= NULL;
		$par_org_token  = NULL;

		$group_type 		= NULL;
		$group_salt 		= NULL;
		$group_token 		= NULL;

		$audit_schema 		= array();
		$audit_table 		= array();
		$audit_action 		= array();
		$prev_detail 		= array();
		$curr_detail 		= array();

		$get_paths 			= array();

		$orig_org_code 		= NULL;
		$orig_org_salt 		= NULL;
		$orig_org_token 	= NULL;

		$post_data 			= array();

		try
		{
			// $this->redirect_off_system($this->module);

			$params 		= get_params();
			
			$par_one_arr 	= explode('/', $params['param_1']);

			$org_code 			= filter_var( base64_url_decode( $par_one_arr[0] ), FILTER_SANITIZE_STRING );
			$org_salt 			= $par_one_arr[1];
			$org_token 			= $par_one_arr[2];

			$orig_org_code 		= $par_one_arr[0];
			$orig_org_salt 		= $par_one_arr[1];
			$orig_org_token 	= $par_one_arr[2];

			$post_data 			= array(
				'org_code' 		=> $orig_org_code,
				'salt' 			=> $orig_org_salt,
				'token' 		=> $orig_org_token
			);

			$par_org_code 		= filter_var( base64_url_decode( $par_one_arr[3] ), FILTER_SANITIZE_STRING );
			$par_org_salt 		= $par_one_arr[4];
			$par_org_token 		= $par_one_arr[5];

			$group_type 		= filter_var( base64_url_decode( $par_one_arr[6] ), FILTER_SANITIZE_STRING );
			$group_salt 		= $par_one_arr[7];
			$group_token 		= $par_one_arr[8];

			check_salt( $org_code, $org_salt, $org_token );
			check_salt( $par_org_code, $par_org_salt, $par_org_token );
			check_salt( $group_type, $group_salt, $group_token );

			SYSAD_Model::beginTransaction();

			$par_where 			= array();

			$par_where['org_code']		= $org_code;
			$par_where['org_parent']	= $par_org_code;
			$par_where['group_type']	= $group_type;

			$check_org_paths 			= $this->get_parents( $org_code, $group_type );

			$get_paths 					= $this->get_paths( $par_org_code, $group_type );

			$count_org_paths 			= count( $check_org_paths );

			if( $count_org_paths == 1 AND !EMPTY( $get_paths ) )
			{
				$get_descendants 		= $this->orgs->get_descendants( $org_code, Organizations_model::DESCENDANTS, $group_type );
				
				if( !EMPTY( $get_descendants ) )
				{
					$invalid_delete_msg = sprintf( $this->lang->line('multiple_root_organization'), 'Please delete its children first.' );

					throw new Exception($invalid_delete_msg);
				}
			}

			$pass_args 					= array();

			$pass_args['org_parents']	= array( $par_one_arr[3] );

			$audit_action[]		= AUDIT_DELETE;
			$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PATHS;
			$audit_schema[]		= DB_CORE;

			$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PATHS, $par_where );

			$this->orgs->delete_helper( SYSAD_Model::CORE_TABLE_ORG_PATHS, $par_where );

			$curr_detail[] 		= array();

			$audit_action[]		= AUDIT_DELETE;
			$audit_table[] 		= SYSAD_Model::CORE_TABLE_ORG_PARENTS;
			$audit_schema[]		= DB_CORE;

			$prev_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORG_PARENTS, $par_where );

			$this->orgs->delete_helper( SYSAD_Model::CORE_TABLE_ORG_PARENTS, $par_where );

			$curr_detail[] 		= array();

			if( !EMPTY( $org_code ) )
			{
				$check_root 		= $this->orgs->check_root( $par_org_code );

				$check_path 		= $this->get_paths( $org_code, $group_type );

				if( !EMPTY( $check_root ) AND !EMPTY( $check_root['check_root'] ) AND EMPTY( $check_path ) )
				{
					$root_audit_details = $this->process_org_root_del_ins( $par_org_code, ACTION_DELETE, $org_code, $group_type );

					$audit_schema 		= array_merge( $audit_schema, $root_audit_details['audit_schema'] );
					$audit_table 		= array_merge( $audit_table, $root_audit_details['audit_table'] );
					$audit_action 		= array_merge( $audit_action, $root_audit_details['audit_action'] );
					$prev_detail 		= array_merge( $prev_detail, $root_audit_details['prev_detail'] );
					$curr_detail 		= array_merge( $curr_detail, $root_audit_details['curr_detail'] );
				}
			}

			$sub_org_audit_detail 	= $this->process_sub_org_del_ins( $pass_args, $org_code, ACTION_ADD, $group_type );

			$audit_schema 			= array_merge( $audit_schema, $sub_org_audit_detail['audit_schema'] );
			$audit_table 			= array_merge( $audit_table, $sub_org_audit_detail['audit_table'] );
			$audit_action 			= array_merge( $audit_action, $sub_org_audit_detail['audit_action'] );
			$prev_detail 			= array_merge( $prev_detail, $sub_org_audit_detail['prev_detail'] );
			$curr_detail 			= array_merge( $curr_detail, $sub_org_audit_detail['curr_detail'] );

			$activity 			= sprintf($this->lang->line('audit_trail_delete'), 'Org Parent');

			$this->audit_trail->log_audit_trail(
				$activity, 
				$this->module, 
				$prev_detail, 
				$curr_detail, 
				$audit_action, 
				$audit_table,
				$audit_schema
			);
			// throw new Exception('a');
			SYSAD_Model::commit();

			$status 			= SUCCESS;
			$msg 				= $this->lang->line('data_deleted');
		}
		catch( PDOException $e )
		{
			SYSAD_Model::rollback();

			$this->rlog_error( $e );

			$msg 					= $this->get_user_message( $e );
		}
		catch( Exception $e )
		{
			SYSAD_Model::rollback();

			$this->rlog_error( $e );

			$msg 					= $e->getMessage();
		}

		$response 					= array(
			"flag" 					=> $flag,
			"msg" 					=> $msg,
			"status" 				=> $status,
			"reload"				=> "dynamic_table",
			"data"					=> json_encode( $post_data ),
			"path"					=> CORE_USER_MANAGEMENT.'/organizations/get_org_parents_table/',
			"wrapper"				=> '#tbl_org_parent tbody',
			"functions"				=> "Organizations.parent_toogle_click();Organizations.has_parent_toggle($('#has_parent'));Organizations.my_add_rows();"
		);

		echo json_encode( $response );

	}
	
	
	/**
	 * Use This helper function to get the parent of the main organization selected
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $group_type -- optional. default NULL. group type of the organization
	 * @param  $search -- optional. default value array. search parameters usually used in datatable
	 * @param  $params -- optional. default value array. search value of the parameters usually used in datatable
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function get_parents( $org_code, $group_type = NULL, array $search = array(), array $params = array() )
	{
		$result 		= array();

		try
		{
			$result 	= $this->orgs->get_parents( $org_code, $group_type, $search, $params );
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return $result;
	}
	
	/**
	 * Use This helper function to get the paths of the main organization selected
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $group_type -- optional. default NULL. group type of the organization
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function get_paths( $org_code, $group_type = NULL )
	{
		$result 		= array();

		try
		{
			$result 	= $this->orgs->get_paths( $org_code, $group_type );
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return $result;
	}

	/**
	 * Use This helper function to get the roots of the main organization selected
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $group_type -- optional. default NULL. group type of the organization
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function get_roots( $org_code, $group_type = NULL )
	{
		$result 		= array();

		try
		{
			$result 	= $this->orgs->get_roots( $org_code, $group_type );
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return $result;
	}

	/**
	 * Use This helper function to process the descendants of the main org selected
	 *
	 *
	 * @param  $descendants -- required. all the child of the main org selected.
	 * @param  $group_name -- optional. default NULL. group name
	 * @param  $org_name -- optional. default NULL. organization name
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function process_descendants_arr( array $descendants, $group_name = NULL, $org_name = NULL )
	{
		$descendants_arr 	= array();

		$org_key 			= 'org_code';
		$group_key 			= 'group_type';

		if( !EMPTY( $group_name ) )
		{
			$group_key 		= $group_name;
		}

		if( !EMPTY( $org_name ) )
		{
			$org_key 		= $org_name;
		}

		foreach( $descendants as $descendant )
		{
			$descendants_arr[ $descendant[ $group_key ] ][] 	= $descendant[ $org_key ];
		}

		return $descendants_arr;
	}
	
	/**
	 * Use This helper function to check if the main org selected is a descendant
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $parent_org -- required. its parent organization code
	 * @param  $group_type -- required. group type of the organization
	 * @param  $return -- optional. Default value TRUE. if False it will just throw an exception message
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function is_descendant( $org_code, $parent_org, $group_type, $return = TRUE )
	{
		$flag 				= '';
		$msg 				= TRUE;

		try
		{
			$check 			= $this->base_hierarchy_check( $org_code, $parent_org, $group_type, $return );

			$flag 			= ( !$check['flag'] );
			$msg 			= $check['msg'];
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return array(
			'flag'	=> $flag
		);
	}
	
	/**
	 * Use This helper function to check if the main org selected is an ancestor
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $parent_org -- required. its parent organization code
	 * @param  $group_type -- required. group type of the organization
	 * @param  $return -- optional. Default value TRUE. if False it will just throw an exception message
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function is_ancestor( $org_code, $parent_org, $group_type, $return = TRUE )
	{
		$flag 				= '';
		$msg 				= TRUE;

		try
		{
			$check 			= $this->base_hierarchy_check( $org_code, $parent_org, $group_type, $return, Organizations_model::ANCESTORS );

			$flag 			= ( !$check['flag'] );
			$msg 			= $check['msg'];
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return array(
			'flag'	=> $flag
		);
	}

	/**
	 * Use This helper function to check if the main org selected is a sibling
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $parent_org -- required. its parent organization code
	 * @param  $group_type -- required. group type of the organization
	 * @param  $return -- optional. Default value TRUE. if False it will just throw an exception message
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function is_sibling( $org_code, $parent_org, $group_type, $return = TRUE )
	{
		$flag 				= '';
		$msg 				= TRUE;

		try
		{
			$check 			= $this->base_hierarchy_check( $org_code, $parent_org, $group_type, $return, Organizations_model::SIBLINGS );

			$flag 			= ( !$check['flag'] );
			$msg 			= $check['msg'];
		}
		catch( PDOException $e )
		{
			throw $e;
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return array(
			'flag'	=> $flag
		);
	}
	

	/**
	 * Use This helper function to check if the main org selected is a descendant or ancestor or sibling
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $parent_org -- required. its parent organization code
	 * @param  $group_type -- required. group type of the organization
	 * @param  $return -- optional. Default value TRUE. if False it will just throw an exception message
	 * @param  $type -- optional. Default value Descendants.
	 * @throws PDOException
	 * @throws Exception
	 * @return array
	 */
	public function base_hierarchy_check( $org_code, $parent_org, $group_type, $return = FALSE, $type = Organizations_model::DESCENDANTS )
	{
		$msg 				= '';
		$flag 				= TRUE;

		try
		{
			$child_orgs 	= $this->orgs->get_descendants($org_code, $type);

			$desc_arr 		= $this->process_descendants_arr( $child_orgs );

			if( is_array( $parent_org ) AND is_array( $group_type ) )
			{
				foreach( $parent_org as $key => $par_org )
				{
					$clean_group 	= filter_var( base64_url_decode( $group_type[ $key ] ), FILTER_SANITIZE_STRING );
					$clean_par 		= filter_var( base64_url_decode( $par_org ), FILTER_SANITIZE_STRING );

					$real_par 		= ( !EMPTY( $clean_par ) ) ? $clean_par : $par_org;
					$real_group 	= ( !EMPTY( $clean_group ) ) ? $clean_group : $group_type[ $key ];
					
					if( !EMPTY( $desc_arr ) AND ISSET( $desc_arr[ $real_group ] ) AND !EMPTY( $desc_arr[ $real_group ] ) )
					{
						$valid_desc_arr 	= $desc_arr[ $real_group ];

						if( in_array( $real_par, $valid_desc_arr ) )
						{
							// $msg 			= 'Sorry, but this organization is already your child.';

							if( ISSET( $this->hierarchy_msg_map[ $type ] ) )
							{
								$msg 		= sprintf( $this->hierarchy_msg_map[ $type ], $real_par );
							}
							
							$flag 			= FALSE;

							if( !$return )
							{
								throw new Exception( $msg );
							}

							break;
						}
					}
				}					
			}
			else
			{
				$clean_group 	= filter_var( base64_url_decode( $parent_org ), FILTER_SANITIZE_STRING );
				$clean_par 		= filter_var( base64_url_decode( $group_type ), FILTER_SANITIZE_STRING );

				$real_par 		= ( !EMPTY( $clean_par ) ) ? $clean_par : $parent_org;
				$real_group 	= ( !EMPTY( $clean_group ) ) ? $clean_group : $group_type;

				if( !EMPTY( $desc_arr ) AND ISSET( $desc_arr[ $real_group ] ) AND !EMPTY( $desc_arr[ $real_group ] ) )
				{
					$valid_desc_arr 	= $desc_arr[ $real_group ];

					if( in_array( $real_par, $valid_desc_arr ) )
					{
						// $msg 			= 'Sorry, but this organization is already your child.';

						if( ISSET( $this->hierarchy_msg_map[ $type ] ) )
						{
							$msg 		= sprintf( $this->hierarchy_msg_map[ $type ], $real_par );
						}

						$flag 			= FALSE;

						if( !$return )
						{
							throw new Exception( $msg );
						}
					}
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

		return array(
			'msg'	=> $msg,
			'flag'	=> $flag
		);
	}
	
	private function _validate($params)
	{
		$required 		= array();
		$constraints 	= array();

		$required['org_name']				= 'Organization Name';

	/* 	if( ISSET( $params['has_parent'] ) )
		{
			$required['org_group_type'] 	= 'Group Type';
			$required['org_parents'] 		= 'Parent';
		} */

		$constraints['org_name']			= array(
		 	'data_type'     => 'string',
            'max_len'       => '255',
            'name'          => 'Organization Name'
		);

	
		$required['org_type_code']	 = 'Organization Type';

		$constraints['org_type_code'] = [
			'data_type'   => 'db_value',
			'name'        => 'Organization Type',
			'field'       => 'COUNT( 1 ) as check_row',
			'check_field' => 'check_row',
			'where'       => 'org_type_code',
			'table'       =>  SYSAD_Model::PORTAL_TABLE_ORGANIZATION_TYPES
		];
		

		if( ! EMPTY($constraints['parent_org_code']))
		{
			$required['parent_org_code'] = 'Parent Organization';

			$constraints['parent_org_code'] = [
				'data_type'   => 'db_value',
				'name'        => 'Parent Organization',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'org_code',
				'table'       =>  SYSAD_Model::PORTAL_TABLE_ORGANIZATIONS
			];
		}


/* 		if( ! ISSET($params['org_name'] ) OR EMPTY( $params['org_name'] ) )
		{
			$required['org_name']	= 'Organization name';
		}

		if( !EMPTY( $params['id'] ) )
		{
			$id 			= filter_var( base64_url_decode( $params['id'] ), FILTER_SANITIZE_STRING );

			if( ISSET( $params['org_parents'] ) AND !EMPTY( $params['org_parents'] ) 
				AND ISSET( $params['org_parents'][0] ) AND !EMPTY( $params['org_parents'][0] )
			)
			{
				$this->is_descendant( $id, $params['org_parents'], $params['org_group_type'], FALSE );
			}
		} */
		
		if(EMPTY($params['id']) AND EMPTY($params['salt']) AND EMPTY($params['token']))
		{

			if( ! ISSET( $params['org_code'] ) OR EMPTY($params['org_code'] ) )
			{
				$required['org_code']	= 'Organization code';
			}
			
			$org_details = $this->orgs->get_org_details($params['org_code']); 

			if(! EMPTY($org_details))
				throw new Exception(sprintf($this->lang->line('duplicate_data'), 'organization code')); 
			
		}

		$this->check_required_fields( $params, $required );

		if( ISSET( $params['email'] ) AND !EMPTY( $params['email'] ) )
		{
			$constraints['email']	= array(
				'data_type'     => 'email',
	            'name'          => 'Email'
			);
		}

		if( ISSET( $params['website'] ) AND !EMPTY( $params['website'] ) )
		{
			$constraints['website']	= array(
				'data_type'     => 'url',
	            'name'          => 'Website'
			);
		}

		$this->validate_inputs( $params, $constraints );
		

		/* if( ISSET( $params['has_parent'] ) )
		{
			if( ISSET( $params['org_parents'] ) AND !EMPTY( $params['org_parents'] ) 
				AND ISSET( $params['org_parents'][0] ) AND !EMPTY( $params['org_parents'][0] )
			)
			{
				foreach( $params['org_parents'] as $key => $org_par )
				{
					$clean_par 		= filter_var( base64_url_decode( $org_par ), FILTER_SANITIZE_STRING );
					//$clean_group 	= filter_var( base64_url_decode( $params['org_group_type'][ $key ] ), FILTER_SANITIZE_STRING );

					$org_where 				= array();
					$org_where['org_code']	= $clean_par;

					$group_where 				= array();
					$group_where['group_type']	= $clean_group;

					$check 		= $this->orgs->check_valid_org_code_helper( $org_where );

					//$check_grp 		= $this->orgs->check_valid_group_type_helper( $group_where );

					if( EMPTY( $check ) OR EMPTY( $check['check_org'] ) )
					{
						$invalid_org_msg 			= sprintf( $this->lang->line('invalid_multi'), 'Org', ( $key + 1 ) );

						throw new Exception($invalid_org_msg);
					}

					if( EMPTY( $check_grp ) OR EMPTY( $check_grp['check_grp'] ) )
					{
						$invalid_group_type_msg 	= sprintf( $this->lang->line('invalid_multi'), 'Group Type', ( $key + 1 ) );

						throw new Exception($invalid_group_type_msg);
					}
				}
			}
		} */
	}

	public function get_org_parents_table()
	{
		$flag 		= 0;

		$html 		= '';

		$org_code 	= 0;
		$params 	= get_params();
		$par_org_details 	= array();

		try
		{
			$data  			= array();
			$resources 		= array();

			$org_code_orig 	= NULL;
			$org_salt 		= NULL;
			$org_token 		= NULL;

			if( !EMPTY( $params['org_code'] ) )
			{
				$org_code 				= filter_var( base64_url_decode( $params['org_code'] ), FILTER_SANITIZE_STRING );

				$par_org_details 		= $this->orgs->get_root_paths_per_org_code( $org_code );

				check_salt( $org_code, $params['salt'], $params['token'] );

				$org_code_orig 			= $params['org_code'];
				$org_salt 				= $params['salt'];
				$org_token 				= $params['token'];
			}

			$resources['loaded_init']	= array('selectize_init();');

			$org_group_types 			= array();

			$org_group_types 			= $this->orgs->get_org_group_types();
			$other_orgs 				= $this->orgs->get_other_orgs( $org_code );

			$data['other_orgs'] 		= $other_orgs;
			$data['org_group_types']	= $org_group_types;
			$data['par_org_details']	= $par_org_details;
			$data['org_code']			= $org_code_orig;
			$data['org_salt']			= $org_salt;
			$data['org_token'] 			= $org_token;

			$html 			= $this->load->view('tables/org_parents_table', $data, TRUE);
			$html 		   .= $this->load_resources->get_resource($resources);
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message( $e );
		}
		catch( Exception $e )
		{
			$msg 	= $this->rlog_error($e, TRUE);
		}

		echo $html;
	}
	
	/**
	 * Use This helper function to check if the main org selected is the root organization
	 *
	 *
	 * @param  $org_code -- required. organization code
	 * @param  $org_parent -- required. its parent organization code
	 * @param  $group_type -- required. group type of the organization
	 * @param  $return -- optional. Default value TRUE. if False it will just throw an exception message
	 * @param  $type -- optional. Default value Descendants.
	 * @throws PDOException
	 * @throws Exception
	 */
	public function validate_root( $org_code, $org_parent, $group_type )
	{
		try
		{
			$check_has_already_parent 	= $this->orgs->check_has_already_parent_helper( $group_type );
			
			if( !EMPTY( $check_has_already_parent ) AND !EMPTY( $check_has_already_parent['check_has_already_parent'] ) )
			{
				if( $check_has_already_parent['org_root'] == $org_code )
				{
					return;
				}

				$check_if_root 			= $this->orgs->check_if_root_helper( $org_parent, $group_type );

				if( $check_has_already_parent['org_root'] != $org_parent )
				{
					if( EMPTY( $check_if_root ) OR EMPTY( $check_if_root['check_if_root'] ) )
					{
						throw new Exception( sprintf( $this->lang->line('multiple_root_organization'), '' ) );
					}
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

	public function update_logo()
	{
		$msg 					= "";
		$flag  					= 0;

		$orig_params 			= get_params();

		$audit_action 			= AUDIT_UPDATE;
		$update 				= TRUE;
		$action 				= ACTION_EDIT;

		$prev_detail 			= array();
		$curr_detail 			= array();
		$audit_table 			= array();
		$audit_action 			= array();
		$audit_schema 			= array();
		$audit_activity 		= '';

		$status 				= ERROR;

		$update 				= ( ISSET( $orig_params['id'] ) AND !EMPTY( $orig_params['id'] ) ) ? TRUE : FALSE;
		$action 				= ( ISSET( $orig_params['id'] ) AND !EMPTY( $orig_params['id'] ) ) ? ACTION_EDIT : ACTION_ADD;

		$main_where 			= array();

		$path 					= NULL;
		$org_code 				= NULL;
		$sess_org_code 			= NULL;

		try
		{
			// $this->redirect_off_system($this->module);
			
			$params 			= $this->set_filter( $orig_params )
									->filter_string('id', TRUE)
									->filter();

			check_salt($params['id'], $params['salt'], $params['token']);

			$permission 		= $this->edit_per;
			$per_msg 			= $this->lang->line( 'err_unauthorized_edit' );

			if( !$permission )
			{
				throw new Exception( $per_msg );
			}	

			$main_where 		= array(
				'org_code'		=> $params['id']
			);

			SYSAD_Model::beginTransaction();

			$audit_schema[] 	= DB_CORE;
			$audit_table[] 	 	= SYSAD_Model::CORE_TABLE_ORGANIZATIONS;
			$audit_action[] 	= AUDIT_UPDATE;
			$prev_detail[]  	= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORGANIZATIONS,
				$main_where
			);

			$upd_val['modified_by'] 	= $this->session->user_id;
			$upd_val['modified_date'] 	= $this->date_now;
			$upd_val['logo'] 			= ( !EMPTY( $params['org_logo'] ) ) ? $params['org_logo'] : NULL;
			$upd_val['logo_orig_name'] 	= ( !EMPTY( $params['org_logo_orig_filename'] ) ) ? $params['org_logo_orig_filename'] : NULL;

			$this->orgs->update_helper( SYSAD_Model::CORE_TABLE_ORGANIZATIONS, $upd_val, $main_where );

			$curr_detail[] 		= $this->orgs->get_details_for_audit( SYSAD_Model::CORE_TABLE_ORGANIZATIONS,
				$main_where
			);

			$audit_name 				= 'Organization Logo.';

			$audit_activity 			= sprintf($this->lang->line('audit_trail_update'), $audit_name);

			$this->audit_trail->log_audit_trail( $audit_activity, $this->module, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema );

			if( !EMPTY( $params['org_logo'] ) )
			{
				$root_path 			= $this->get_root_path();
				$org_pic_path 		= $root_path. PATH_ORGANIZATION_UPLOADS . $params['org_logo'];
				$org_pic_path 		= str_replace(array('\\','/'), array(DS,DS), $org_pic_path);
				
				if( file_exists( $org_pic_path ) )
				{
					$org_pic_src	= output_image($params['org_logo'], PATH_ORGANIZATION_UPLOADS);
					$path 	 	 	= $org_pic_src;
				}
			}
			else
			{
				$path 				= base_url().PATH_IMAGES.DEFAULT_ORG_LOGO;
			}

			$org_code 				= $params['id'];
			$sess_org_code 			= $this->session->org_code;

			SYSAD_Model::commit();

			$status 				= SUCCESS;
			$flag 					= 1;
			$msg 					= $this->lang->line( 'data_saved' );
		}
		catch( PDOException $e )
		{
			SYSAD_Model::rollback();

			$this->rlog_error( $e );

			$msg 					= $this->get_user_message( $e );
		}
		catch (Exception $e)
		{

			SYSAD_Model::rollback();

			$this->rlog_error( $e );

			$msg 					= $e->getMessage();
		}

		$response 					= array(
			'msg' 					=> $msg,
			'flag' 					=> $flag,
			'status'				=> $status,
			'table_id' 				=> $this->table_id,
			'org_code' 				=> $org_code,
			'sess_org_code'			=> $sess_org_code,
			'path' 					=> $path
		);

		echo json_encode( $response );
	}

	public function get_org_parents()
	{
		try
		{
			$flag 		= '';
			$msg 		= '';
			$orgs 		= [];
			$params 	= get_params();

			$required 	= ['org_type' => 'Organization Type'];

			$constraints['org_type'] = [
				'data_type'   => 'db_value',
				'name'        => 'Organization Type',
				'field'       => 'COUNT( 1 ) as check_row',
				'check_field' => 'check_row',
				'where'       => 'org_type_code',
				'table'       =>  SYSAD_Model::PORTAL_TABLE_ORGANIZATION_TYPES
			];
            
            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
			$data       = $this->validate_inputs($params, $constraints);
			
			$result 	= $this->orgs->get_param_org_types(['org_type_code' => $params['org_type']], ['parent_org_type_code']);

			$fields = [
				'org_code as value',
				'name as text'
			];

			$orgs 		= $this->orgs->get_organizations(['org_type_code' => $result['parent_org_type_code']], $fields);
		}
		catch(PDOException $e)
		{
			$msg 	= $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);
		}

		echo json_encode([
			'flag'  => $flag,
			'msg'   => $msg,
 			'orgs'  => json_encode($orgs)
		]);
	}
}

