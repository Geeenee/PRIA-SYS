<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Submitted_document extends Portal_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module 		= PORTAL_SUBMITTED_DOCUMENT;
		$this->module_js 	= HMVC_FOLDER."/".SYSTEM_PORTAL."/submitted_document";
		$this->table_id 	= 'submitted_document';
		$this->path 		= 'submitted_document/get_submitted_document_list';
		$this->controller 	= strtolower(__CLASS__);
	}

	public function index()
	{
		$data 			= array();
		$resources		= array();

		try
		{
			$resources['load_css']			= array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE);
			$resources['load_js']			= array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE);
			
			$resources['load_materialize_modal'] = array(
		        'modal_submitted_document' 	=> array(
		          'size' 					=> 'sm-w md-h',
		          'title' 					=> 'Submitted Document',
		          'module' 					=> PORTAL_SUBMITTED_DOCUMENT,
		          'method' 					=> 'modal_submitted_document',
		          'controller' 				=> $this->controller
		        )
		    );

			$resources['datatable'] = array(
			 		'table_id' 			=> $this->table_id, 
			 		'path' 				=> $this->path,
			 		'advanced_filter'	=> true
			);

			$content_left					= array();
			$content_left['id']				= 'sb_pr_list';
			// $content_left['data']			= $this->construct_lists();
			$content_left['sidebar_toggle']	= TRUE;
			$content_left['sidebar_close']	= TRUE;
			$content_left['position']		= SB_LEFT; // SB_LEFT, SB_RIGHT
			$content_left['theme']			= SB_SKIN_DARK; // SB_SKIN_DARK, SB_SKIN_LIGHT
			
			// $data['sub_nav_left'] 			= $this->construct_sub_nav($content_left);

			// PAGE TITLE
			$data['active_sub_menu'] = MODULE_PORTAL_TASK;
			$data['page_title'] 	 = 'Submit Document';

			// START: BREADCRUMBS
			$breadcrumbs 		= array();
			$key				= 'Submit Document';
			$breadcrumbs[$key]	= '';

			set_breadcrumbs($breadcrumbs, TRUE);
			// END: BREADCRUMBS
			

			$this->template->load('submitted_document', $data, $resources, Portal_Controller::$system);
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

	public function modal_submitted_document($hash_id = NULL, $salt = NULL, $token = NULL, $security_action = NULL) 
	{
		try 
		{
			$data = $resources = array();
			$modal = "modals/submitted_document";

			$resources['load_css']		= array(CSS_SELECTIZE, CSS_UPLOAD);
			$resources['load_js']		= array(JS_SELECTIZE, JS_UPLOAD);

			$resources['upload'] = array(
				'file' => array(
					'path' 					=> PATH_SETTINGS_UPLOADS, 
					'allowed_types' 		=> '*',
					'drag_drop'				=> true,
					'multiple'				=> true
				)
			);

			$resources['selectize']		= array(
				"select-submitted_document" => array(
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

	public function get_submitted_document_list()
	{
		try{

			$submitted_document = array(
				array(
					'account_group' => 'Account Group 1',
					'document_type' => 'Document Type 1',
					'reference_no' 	=> 'Reference No 1',
					'main_document' => 'Main docuemnt 1'
				),
				array(
					'account_group' => 'Account Group 2',
					'document_type' => 'Document Type 2',
					'reference_no' 	=> 'Reference No 2',
					'main_document' => 'Main docuemnt 2'
				)
			);

			$iTotal 		= 0;
			$iFilteredTotal = 0;

			$output = array(
					"sEcho" => intval($_POST['sEcho']),
					"iTotalRecords" => 1,
					"iTotalDisplayRecords" => 1,
					"aaData" => array()
			);
			
			$cnt 			= 0;
			$edit_action 	= '';
			$delete_action 	= '';

			foreach ($submitted_document as $aRow):
				$cnt++;
				$row = array();
				$action = "";
				$action     		= "<div class='table-actions'>";
				
				$action             .= "<a href='#modal_submitted_document' class='tooltipped modal_submitted_document_trigger' data-tooltip='Edit' data-position='bottom' data-delay='50' onclick=\"". $edit_action ."\"><i class='grey-text material-icons'>edit</i></a>";
			
				$action             .= "<a href='javascript:;' onclick='".$delete_action."' class='tooltipped' data-tooltip='Delete' data-position='bottom' data-delay='50'><i class='grey-text material-icons'>delete</i></a>";
				
				$action             .= "</div>";
					
				//$resources['preload_modal'] = array("modal_designation");
				// $resources['loaded_init'] = array("selectize_init();");
				// $action .= $this->load_resources->get_resource($resources, TRUE);
										
				$row                = array();
				$row[]              = $aRow['account_group'];
				$row[]              = $aRow['document_type'];
				$row[]              = $aRow['reference_no'];
				$row[]              = $aRow['main_document'];
				$row[]              = $action;
				
				$output['aaData'][] = $row;
			endforeach;

			echo json_encode( $output );
		}
		catch(PDOException $e){
			echo $e;
		}
		catch(Exception $e){
			echo $e;
		}
	}
}