<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
		$this->controller 		= strtolower(__CLASS__);
		$this->module_folder 	= PORTAL_TASK;

		$this->load->model('pria_workflow_model', 'pw_model');
		//$this->load->model('task_model', 'tm_model');
	}
	
	public function index()
	{
		$data 			= array();
		$resources		= array();
		$params 		= get_params();
		
		try
		{
			$resources['load_css']			= array(CSS_DATETIMEPICKER, CSS_LABELAUTY, CSS_UPLOAD, CSS_SELECTIZE);
			$resources['load_js']			= array(JS_DATETIMEPICKER, JS_LABELAUTY, JS_UPLOAD, JS_SELECTIZE);
			
			$resources['upload'] = array(
				'file' => array(
					'path' 					=> PATH_SETTINGS_UPLOADS, 
					'allowed_types' 		=> '*',
					'drag_drop'				=> true,
					'multiple'				=> true
				)
			);
			
			$resources['selectize']		= array(
				"select-tag" => array(
					"type"	=> "default"
				),
				"select-privacy" => array(
					"type"	=> "default"
				)
			);

			$resources['load_materialize_modal'] = array(
		        'modal_task_reminders' 	=> array(
		          'size' 					=> 'sm-w md-h',
		          'title' 					=> 'Predecessors',
		          'module' 					=> PORTAL_TASK,
		          'method' 					=> 'modal_task_reminders',
		          'controller' 				=> $this->controller
		        )
		    );

			$content_left					= array();
			$content_left['id']				= 'sb_pr_list';
			$content_left['data']			= $this->construct_lists();
			$content_left['sidebar_toggle']	= TRUE;
			$content_left['sidebar_close']	= TRUE;
			$content_left['position']		= SB_LEFT; // SB_LEFT, SB_RIGHT
			$content_left['theme']			= SB_SKIN_LIGHT; // SB_SKIN_DARK, SB_SKIN_LIGHT
			$data['sub_nav_left'] 			= $this->construct_sub_nav($content_left);
			
			// PAGE TITLE
			$data['active_sub_menu'] = MODULE_PORTAL_TASK;
			$data['page_title'] 	 = 'Task';

			// START: BREADCRUMBS
			$breadcrumbs 		= array();
			$key				= 'Task'; 
			$breadcrumbs[$key]	= '';

			set_breadcrumbs($breadcrumbs, TRUE);
			// END: BREADCRUMBS
			
			$this->template->load('task_details', $data, $resources, Portal_Controller::$system);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function modal_task_reminders($hash_id = NULL, $salt = NULL, $token = NULL, $security_action = NULL) 
	{
		try 
		{
			$data = $resources = array();
			$modal = "modals/reminders";

			$resources['load_css']		= array(CSS_SELECTIZE);
			$resources['load_js']		= array(JS_SELECTIZE);
			$resources['selectize']		= array(
				"select-reminder" => array(
					"type"	=> "default"
				)
			);
		} 
		catch (PDOException $e) 
		{
			$data['exception'] = $e;
			$data['message'] = $this->get_user_message($e);

			$modal = CMS_ERR_PAGE_MODAL;
		} 
		
		catch (Exception $e) 
		{
			$data['exception'] = $e;
			$data['message'] = $this->rlog_error($e, TRUE);

			$modal = CMS_ERR_PAGE_MODAL;
		}

		$this->load->view($modal, $data);
		$this->load_resources->get_resource($resources);
	}

	public function view_page($form_id = NULL, $type = NULL, $security_action = NULL)
	{
		$data 						= array();
		$params 					= get_params();
		$resources 					= array();
		$resources['load_css']		= array(CSS_UPLOAD, CSS_DATETIMEPICKER, CSS_LABELAUTY);
		$resources['load_js']		= array(JS_UPLOAD, JS_DATETIMEPICKER, JS_LABELAUTY);

		$resources['upload'] = array(
			'file' => array(
				'path' 					=> PATH_SETTINGS_UPLOADS, 
				'allowed_types' 		=> '*',
				'multiple'				=> true
			)
		);
		
		//Sample Parameter
		$form_id 			= ISSET($form_id) ? $form_id : FORM_INTERNAL_ORDERS;
		$type 				= ISSET($type) ? $type : TYPE_FHR;
		$security_action 	= ISSET($security_action) ? $security_action : PORTAL_REVIEW;
		//Ends
		
		$content_left					= array();
		$content_left['id']				= 'sb_pr_list';
		$content_left['data']			= $this->construct_lists();

		$content_left['sidebar_toggle']	= TRUE;
		$content_left['sidebar_close']	= FALSE;
		$content_left['position']		= SB_LEFT; // SB_LEFT, SB_RIGHT
		$content_left['theme']			= SB_SKIN_LIGHT; // SB_SKIN_DARK, SB_SKIN_LIGHT

		$data['sub_nav_left'] 			= $this->construct_sub_nav($content_left);
		
		
		// PAGE TITLE
		$data['page_title'] 	= 'Internal Order: IO 0001 1901 01';
		$data['page_referer']	= base_url() . 'dashboard/dashboard/overview#tab_internal_orders';
		
		try
		{
			//Starts
			$content_form					= array();
			$content_form['id']				= 'nav_container';

			switch ($security_action) {
				case PORTAL_ENCODE:
					$content_form['data'] = $this->_encode_task($type, $data);
					break;

				case PORTAL_UPLOAD:
					$content_form['data'] = $this->_upload_task($type, $data);
					break;

				case PORTAL_REVIEW:
					$content_form['data'] = $this->_review_task($type, $data);
					break;

				case PORTAL_REVIEW_APPROVE:
					$content_form['data'] = $this->_review_approve_task($type, $data);
					break;	
					
				case PORTAL_REVIEW_ENCODE:
					$content_form['data'] = $this->_encode_review_task($type, $data);
					break;

				case PORTAL_APPROVE:
					$content_form['data'] = $this->_approve_task($type, $data);
					break;
				
				default:
					# code...
					break;
			}

			$data['subsa_nav_main'] 	= $this->construct_main_container($content_form, '100%');
			//Ends

			$this->template->load(PORTAL_COMMON.'/nav/nav_container', $data, $resources, Portal_Controller::$system);

		}catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function _encode_task($type = NULL, $data = NULL)
	{
		try{
			$resources 				= array();
			$resources['load_css']	= array(CSS_DATETIMEPICKER, CSS_LABELAUTY, CSS_UPLOAD, CSS_SELECTIZE);
			$resources['load_js']	= array(JS_DATETIMEPICKER, JS_LABELAUTY, JS_UPLOAD, JS_SELECTIZE);
			$resources['upload'] = array(
				'file' => array(
					'path' 					=> PATH_SETTINGS_UPLOADS, 
					'allowed_types' 		=> '*',
					'multiple'				=> true
				)
			);
			
			$html = $this->load->view('forms/'.$type.'/encode_'.$type, $data, TRUE);
			$html .= $this->load_resources->get_resource($resources, TRUE);
			
			return $html;

		}catch( PDOException $e ){
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}catch( Exception $e ){
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function _upload_task($type = NULL, $data = NULL)
	{
		try{
			$resources 				= array();
			$resources['load_css']	= array(CSS_LABELAUTY, CSS_DATETIMEPICKER);
			$resources['load_js']	= array(JS_LABELAUTY, JS_DATETIMEPICKER);
			
			$html = $this->load->view('forms/'.$type.'/upload_'.$type, $data, TRUE);
			$html .= $this->load_resources->get_resource($resources, TRUE);
			
			return $html;

		}catch( PDOException $e ){
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}catch( Exception $e ){
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function _review_task($type = NULL, $data = NULL)
	{
		try{
			$resources 				= array();
			$resources['load_css']	= array();
			$resources['load_js']	= array();

			return $this->load->view('forms/'.$type.'/review_'.$type, $data, $resources, Portal_Controller::$system);
		}catch( PDOException $e ){
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}catch( Exception $e ){
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function _review_approve_task($type = NULL, $data = NULL)
	{
		try{
			$resources 				= array();
			$resources['load_css']	= array();
			$resources['load_js']	= array();

			return $this->load->view('forms/'.$type.'/review_approve_'.$type, $data, $resources, Portal_Controller::$system);
		}catch( PDOException $e ){
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}catch( Exception $e ){
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function _encode_review_task($type = NULL, $data = NULL)
	{
		try{
			$resources 				= array();
			$resources['load_css']	= array();
			$resources['load_js']	= array();

			return $this->load->view('forms/'.$type.'/encode_review_'.$type, $data, $resources, Portal_Controller::$system);
		}catch( PDOException $e ){
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}catch( Exception $e ){
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	public function _approve_task($type = NULL, $data = NULL)
	{
		try{
			
			$resources 				= array();
			$resources['load_css']	= array();
			$resources['load_js']	= array();

			return $this->load->view('forms/'.$type.'approve_'.$type, $data, $resources, Portal_Controller::$system);
		}catch( PDOException $e ){
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}catch( Exception $e ){
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	public function set_filter_session()
	{
		$params = get_params(); 
		
		try
		{
			$display = $params['filter_display'];
			
			if($display)
				$this->session->set_userdata('show_task_filter', $display);
			else
				unset($_SESSION['show_task_filter']);

		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );

		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}


	public function display_task_list()
	{
		try
		{
			$params 			 = get_params();
			
			$success  		 = FALSE;
			/* $json 				 = file_get_contents('php://input');
			$params 			 = json_decode($json, TRUE); */

			$reference_id  	= decrypt_id($params['id']);
			$ag_code 			 = decrypt_id($params['ag']);

			$where  			 = ['reference_id' => $reference_id, 'account_group_code' => $ag_code];
			$workflow 		 = $this->pw_model->get_workflow($where, ['pria_workflow_id']);

			$where 				 = ['pria_workflow_id' => $workflow['pria_workflow_id']];
			$stages 			 = $this->pw_model->get_stages($where, ['pria_stage_id', 'stage_name'], ['sequence_no' => 'ASC']);	

			$stage_ids 		 = array_column($stages, 'pria_stage_id');

			$tasks = $this->tm_model->get_stage_tasks($stage_ids);

			$success  = TRUE;
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
				'success'  => $success,
				'msg' 		 => $msg
		]);
	}

	public function update_last_dr_flag()
	{
		$msg	= "";
		$status	= ERROR;

		try
		{
			$params			= get_params();

			$dr_gr_id		= (ISSET($params['dr_gr_id']) AND !EMPTY($params['dr_gr_id']))? $params['dr_gr_id']: "0";
			$last_dr_flag	= (ISSET($params['last_dr_flag']) AND !EMPTY($params['last_dr_flag']))? $params['last_dr_flag']: ENUM_NO;
			$core_task_id	= (ISSET($params['core_task_id']) AND !EMPTY($params['core_task_id']))? $params['core_task_id']: NULL;
			$dependent_task	= (ISSET($params['dependent_task']) AND !EMPTY($params['dependent_task']))? $params['dependent_task']: "0";

			Portal_Model::beginTransaction();
			
			$update_dr	= FALSE;

			if(!EMPTY($dependent_task))
			{
				$task_details	= $this->pw_model->get_task(['pria_task_id' => $dependent_task], ['pria_task_id', 'task_status_id']);

				if(ISSET($task_details['pria_task_id']) AND !EMPTY($task_details['pria_task_id'])
					AND EMPTY($task_details['task_status_id']))
				{
					$update_dr	= TRUE;
				}
				else
				{
					throw new Exception("Cannot update Last DR flag, dependent task not Pending.");
				}
			}
			else
			{
				$update_dr	= TRUE;
			}

			if($update_dr == TRUE)
			{
				$this->pw_model->update_dr(['last_dr_flag' => $last_dr_flag], ['dr_gr_id' => $dr_gr_id], $core_task_id, $dependent_task);
			}

			$msg	= $this->lang->line('data_saved');
			$status	= SUCCESS;

			Portal_Model::commit();
		}
		catch(PDOException $e)
		{
			$msg	= $this->get_user_message($e);
			Portal_Model::rollback();
		}
		catch(Exception $e)
		{
			$msg	= $this->rlog_error($e, TRUE);
			Portal_Model::rollback();
		}

		echo json_encode([
				'msg'		=> $msg,
				'status'	=> $status
		], JSON_HEX_APOS | JSON_HEX_QUOT);
	}
}