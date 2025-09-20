<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_Controller extends Transaction_Controller
{
	protected $tpl_task_container    		= PORTAL_COMMON.'/nav/nav_container';

	protected $tpl_task_comments     		= PORTAL_TRANSACTIONS.'/task_comments';
	protected $tpl_task_comment_container	= PORTAL_TRANSACTIONS.'/task_comment_container';

	protected $tpl_task_attachments     		= PORTAL_TRANSACTIONS.'/task_attachments';
	protected $tpl_task_attachment_container	= PORTAL_TRANSACTIONS.'/task_attachment_container';

	protected $module_task_comment_js		= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/task_comments";

    protected $data;
	protected $pria_task_id;
	protected $task_details;
	protected $task_access;
	protected $task_view_data;
    protected $task_btn_html;
    protected $task_actions;
	protected $task_resources;
	protected $task_page;

	protected $task_js	   		= [];
	protected $task_css	   		= [];
	protected $task_upload 		= [];

	protected $task_config = [
		'js' => ['hasUpload' => false]
	];

	private $module_task_attachment;

	private $permission_view;
  	private $permission_download;

	public function __construct()
	{
		parent::__construct();

		$this->load->model(PORTAL_TRANSACTIONS.'/documents_model', 'dm_model');
		$this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
		$this->load->model(PORTAL_TRANSACTIONS.'/task_comment_model', 'tcm_model');

		$this->load->library('pria_overview');

		$this->module_task_attachment = MODULE_PORTAL_TASK_ATTACHMENT;

		$this->permission_view			= $this->permission->check_permission($this->module_task_attachment, ACTION_VIEW);

		$this->permission_download		= $this->permission->check_permission($this->module_task_attachment, ACTION_DOWNLOAD);
	}

    /** Transaction related functions  */
    protected function _initialize_task($pria_task_id, array $task_config=[])
	{
		try
		{
			if( ! EMPTY($task_config))
				$this->task_config = array_merge($this->task_config, $task_config);

            $this->pria_task_id          = $pria_task_id;
            //Obviously gets the task details
			$this->task_details          = $this->tm_model->get_task_details($this->pria_task_id);
			//Detemines if the task is for editing or viewing of the logged in user, or if we will disable the form
			$this->task_access           = $this->_get_task_access($this->task_details);
			//Data pass to the view of the specific task
			$this->task_view_data 		 = ['view' => $this->task_access['view'], 'class_label' => $this->task_access['class_label']];
            //Initialize resources
			$portal_resource             = $this->get_common_resources($this->module_code);

			$this->_set_task_documents();

			$task_config_js				 = json_encode($this->task_config['js']);

            $this->task_resources        =  [
				'load_css'               => array_merge(
					[ CSS_LABELAUTY ],
					$portal_resource['css'],
					$this->task_css
				),
                'load_js'                => array_merge(
					[ JS_LABELAUTY, JS_EDITOR, $this->module_task_js],
					$portal_resource['js'],
					$this->task_js
				),
                'loaded_init'            => array_merge(
					[ 'Task.initPage("'.$this->task_details['controller'].'", '.$task_config_js.');' ],
					[ 'Task.removeAttachment();'],
					$portal_resource['init']
				),
                'load_materialize_modal' => $portal_resource['modal'],
                'upload'                 => array_merge($portal_resource['upload'], $this->task_upload)
            ];

			$this->data['task']		 	 = $this->task_details;
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

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-06-29 13:59:19
	 * @Desc: Sets the initialization of the upload
	 * @ReferencedBy:
	 */
	private function _set_task_documents()
	{
		try
		{
			//Sets if has upload will be true. Some task might only have viewing access so set hasUpload in config as false
			$has_upload			= FALSE;

			$this->task_details['has_upload'] = FALSE;

			$where              = ['pria_task_id' => $this->pria_task_id];
			// $documents  		= $this->tm_model->get_specific_task_document_type($where);

			$documents  		= $this->tm_model->get_task_doc_file_extension($this->pria_task_id);
			//print_var_export($documents); die;
			$pria_task_docs 	= $this->dm_model->get_documents($where, ['document_id', 'document_type_code']);
			$pria_task_docs		= array_column($pria_task_docs, 'document_type_code');

			//pria task id
			$pria_task_id = $this->pria_task_id;
			// print_var_export($documents, $pria_task_docs); die;
			foreach($documents as $d)
            {
				if( ! in_array($d['document_type_code'], $pria_task_docs) )
				{
					$document_type	   	= $d['document_type_code'];
					$document_type_idx 	= strtolower($d['document_type_code']);
					$module_code 		= $this->module_code;

					$allowed_ext = !EMPTY($d['allowed_ext']) ? $d['allowed_ext'] : '*';

					if($d['access'] == DOCUMENT_ACCESS_ADD)
					{
						$this->task_upload[$document_type_idx]  = [
							'path'                  => PATH_UPLOADED_FILES,
							//'path'                  => PATH_UPLOADED_TMP_FILES,
							'allowed_types'         => $allowed_ext,//PRIA_TASK_ALLOWED_FILES,
							'multiple'              => FALSE,
							'max_file'              => 1,
							'max_file_size'         => $d['file_bytes'],
							//'max_file_size'         => '1000',
							'drag_drop'             => FALSE,
							'show_preview'          => TRUE,
							'show_download'			=> TRUE,
							'show_progress'			=> TRUE,
							'auto_submit'           => TRUE,
							//'multiple_obj'          => TRUE,
							//'successCallback'       => "Documents.successCallback(files, data, xhr, pd, '$module_code', '$document_type','$pria_task_id');",
						];

						$has_upload = TRUE;
					}
				}
			}

			if($has_upload)
			{
				$this->task_config['js']['hasUpload'] = TRUE;

				$this->task_js[] = $this->module_js_path.'documents';

				$this->task_details['has_upload'] = TRUE;
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

	protected function _get_task_access($task_details)
	{
		try
		{
			$task_id 	      	= $task_details['pria_task_id'];
			$task_status_id		= $task_details['task_status_id'];
			/*
				1) need malaman kung may save or wala. If wala VIEW LAHAT.
					   - Paano pag hindi pa ongoing yung task? dapat ba pag bawal iclick?
				2) If meron, check kung may actor na.
					   - If yes, check kung siya otherwise view?
					   - If none, check kung may role siya. If yes, edit. If no, ?
				3)	If done na, view nalang
			*/
			//print_var_export($this->permissions); die;
			if($this->permissions[ACTION_EDIT])
			{

				switch($task_status_id)
				{
					case TASK_STATUS_DISAPPROVED:
					case TASK_STATUS_APPROVED:
					case TASK_STATUS_DONE:
					case TASK_STATUS_SKIPPED:
						$view 		= TRUE;
					break;
					case TASK_STATUS_RETURNED:
						$view 		= ($task_details['returned_flag'] == ENUM_YES) ? FALSE : TRUE;
					break;
					case TASK_STATUS_ONGOING:

						$view 		= ($task_details['user_id'] == $this->session->user_id) ? FALSE : TRUE;
					break;
					default :

						if(EMPTY($task_details['user_id']))
						{
							//If wala pang nakakaclaim ng task ( wala pang actor ), check if logged user has the role required for the task.
							$where 		 = ['pria_task_id' => $task_id, 'actor_flag' => INITIAL_YES];
							$task_roles  = $this->tm_model->get_task_roles($where, ['role_code']);
							$task_roles  = array_column($task_roles, 'role_code');

							//Here is where the checking is done
							if( ! EMPTY(array_intersect($this->session->user_roles, $task_roles)))
							{
								$view = FALSE;

								//Since wala pang user so we'll assume na igeget yung task. This will set the form to disable until it has an assigned user.
								$this->task_config['js']['disableForm'] = true;
							}
							else
							{
								throw new Exception($this->lang->line('invalid_action'));
							}
						}
						else
						{
							$view = FALSE;

							//If my nakaclaim na or assigned pero hindi siya yung user.
							if($task_details['user_id'] != $this->session->user_id)
									throw new Exception($this->lang->line('invalid_action'));
						}
				}
			}
			else
			{
				$view 		 = TRUE;
			}

			return [
				'view' 			=> $view,
				'hide_btn' 		=> ($view) ? true : false,
				'class_label' 	=> ($view) ? ' '  : 'required'
			];
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

	public function _construct_task_btn($task_id)
	{
		try
		{
			$html 			= '';
			$data 			= '';
			$task_actions  	= $this->tm_model->get_task_actions(
				['pria_task_id' => $task_id, 'btn_label' => 'IS NOT NULL'],
				['btn_label', 'pria_task_action_id'],
				['seq_no' => 'ASC']
			);

			//For GET/RETURN/APPROVE
			$no_btn = COUNT($task_actions);

			foreach($task_actions as $ta)
			{
				$btn_label 		= $ta['btn_label'];
				$task_action_id = $ta['pria_task_action_id'];

				//Settings per button
				switch($task_action_id)
				{
					case TASK_STATUS_ONGOING :
						$id 	= 'btn-save-task';
						$class 	= 'save-draft m-r-xs';
						$icon   = 'mode_edit';
						$data   = 'data-btn-action="Saving"';

						//This means that the task is not yet assigned to a user so display "Get task"
						if( EMPTY($this->task_details['task_status_id'])  && EMPTY($this->task_details['user_id']) )
						{
							$id 		= 'btn-get-task';
							$btn_label 	= 'Get task';
							$icon 		= 'mode_edit';
							$data   	= 'data-btn-action="Processing"';
						}

						if($no_btn == 3 && ! EMPTY($this->task_details['task_status_id']))
							continue 2;

						//If there's an assigned user already
						if($no_btn == 3  && ! EMPTY($this->task_details['user_id']))
							continue 2;

						//If there's an assigned user already
						if($no_btn == 4  && ! EMPTY($this->task_details['user_id']))
							continue 2;
					break;

					case TASK_STATUS_DONE :
						//This means if it is still pending, dont load approve btn
						if( EMPTY($this->task_details['task_status_id']) && EMPTY($this->task_details['user_id'])) continue 2;

						$id 	= 'btn-submit-task';
						$class 	= 'save-submit';
						$icon 	= 'send';
					break;


					case TASK_STATUS_APPROVED :
						//This means if it is still pending, dont load approve btn
						if( EMPTY($this->task_details['task_status_id']) && EMPTY($this->task_details['user_id']) ) continue 2;

						$id 	= 'btn-approve-task';
						$class 	= 'save-submit';
						$icon   = 'check';
					break;

					case TASK_STATUS_RETURNED :
						//This means if it is still pending, dont load return btn
						if( EMPTY($this->task_details['task_status_id']) && EMPTY($this->task_details['user_id']) ) continue 2;

						$id 	= 'btn-return-task';
						$class  = '';
						$icon   = 'replay';
					break;


					case TASK_STATUS_DISAPPROVED:
						$id 	= 'btn-disapprove-task';
						$class  = 'blue-grey lighten-1';
						$icon   = 'clear';
					break;

					default:
						$id     = '';
						$action = '';
						$class 	= '';
						$icon   = '';
				}

				$this->task_actions[$task_action_id] = $task_action_id;

				$html .=<<<EOS
					<button type="button" id="$id" class="btn m-r-xs m-t-xs m-b-sm $class" $data><i class="material-icons">$icon</i>$btn_label</button>
EOS;
			}

			return $html;
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

	public function _construct_task_btn_bkup($task_id)
	{
		try
		{
			$html 			= '';
			$data 			= '';
			$task_actions  	= $this->tm_model->get_task_actions(
				['pria_task_id' => $task_id, 'btn_label' => 'IS NOT NULL'],
				['btn_label', 'pria_task_action_id'],
				['seq_no' => 'ASC']
			);

			//Below is a special rule for task with 3 buttons. Assumed Save, Approve, Return
			$no_btn = COUNT($task_actions);
		/*
			Possible combinations.
			1 - GET/SAVE
			2 - APPROVE
			3 - RETURN

			1 - SAVE/GET

			1 - SAVE/GET
			2 - SUBMIT

			Display sequence.
			GET
			SAVE / SUBMIT FOR RECOMMENDATION / RETURN */
			foreach($task_actions as $ta)
			{
				$btn_label = $ta['btn_label'];

				//Settings per button
				switch($ta['pria_task_action_id'])
				{
					case TASK_STATUS_ONGOING :
						$id 	= 'btn-save-task';
						$class 	= 'purple lighten-1';
						$data   = 'data-btn-action="Saving"';

						//This means that the task is not yet assigned to a user so display "Get task"
						if( EMPTY($this->task_details['task_status_id']) )
						{
							$id 		= 'btn-get-task';
							$btn_label 	= 'Get task';
						}
						else
						{
							//If the task has 3 buttons available. ( Most likely get/save, return, approve )
							if($no_btn == 3)
							{
								//If this flag is not empty, meaning task has fields to fill up. Not just an ordinary return/approve
								//Else skip this button
								if( ! EMPTY($this->task_details['btn_save_review_flag']))
									$btn_label 	= 'Save';
								else
									continue 2;
							}
						}

						/* //This means if task has 3 buttons ( most likey return, approve, get ) and it has a status already dont display this button
						if($no_btn == 3 && ! EMPTY($this->task_details['task_status_id']) ) continue 2;

						//This means if task has 3 buttons ( most likey return, approve, get ) and it is pending change from save to get-task ( so that tagging of get can made in a common function )
						if($no_btn == 3 && EMPTY($this->task_details['task_status_id']) )
							$id = 'btn-get-task'; */
					break;

					case TASK_STATUS_DONE :
						//This means if it is still pending, dont load approve btn
						if( ($no_btn == 3 || $no_btn == 2) && EMPTY($this->task_details['task_status_id']) ) continue 2;

						if($no_btn == 3)
							$id = 'btn-approve-task';
						else
							$id = 'btn-submit-task';

						$class 	= 'purple lighten-1';
					break;

					case TASK_STATUS_RETURNED :
						//This means if it is still pending, dont load return btn
						if($no_btn == 3 && EMPTY($this->task_details['task_status_id']) ) continue 2;

						$id 	= 'btn-return-task';
						$class  = '';
					break;

					default:
						$id     = '';
						$action = '';
						$class 	= '';
				}

				//Removed data-task="$js_obj" not used.
				$html .=<<<EOS
					<button type="button" id="$id" class="btn $class" $data>$btn_label</button>
EOS;
			}
			//echo $html; die;
			return $html;
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

    //protected function _load_specific_task($data, $resources)
    protected function _load_task_view()
	{
		try
		{
			 $link		= $this->tm_model->get_module(['module_code' => $this->tab_module_code], ['link', 'module_name', 'parent_module']);
			 //Get parent module
			 $plink		= $this->tm_model->get_module(['module_code' => $link['parent_module']], ['link']);
			 $tab_name	= 'tab_'.strtolower(str_replace(' ', '_', $link['module_name']));

			 $this->data['page_referer']  		= base_url().$plink['link'].'?keyword='.$this->task_details['reference_num'].'#'.$tab_name;

			 //print_var_export($this->data['page_referer']); die;

			 //$this->data['page_referer']  = $_SERVER['HTTP_REFERER'];

			$this->_set_page_title( $this->module_code, $this->tab_module_code);

			//File Attachment Starts
			// $this->data['task_attachments'] = $task_attachments;
			//File Attachment Ends
			//print_var_export($this->task_access); die;
			 //Builds the html for task buttons
			 $this->data['task_btn_html'] 			= ( $this->task_access['hide_btn'] == FALSE ) ?  $this->_construct_task_btn($this->pria_task_id) : '';

			//To check if this task has approval
			 $task_actions  	= $this->tm_model->get_task_actions(
				['pria_task_id' => $this->pria_task_id, 'btn_label' => 'IS NOT NULL'],
				['btn_label', 'pria_task_action_id'],
				['seq_no' => 'ASC']
			 );

			 $setup_task_actions = array_column($task_actions, 'pria_task_action_id');
			 //If task has approve action
			 //$this->task_details['has_approval'] 	= ( ISSET($this->task_actions[TASK_STATUS_APPROVED]) ) ? TRUE : FALSE;
			 $this->task_details['has_approval'] 	= ( in_array(TASK_STATUS_APPROVED, $setup_task_actions) == TRUE ) ? TRUE : FALSE;

			 //If task is returned
			 $this->task_details['is_returned'] 	= ( ($this->task_details['returned_flag'] == ENUM_YES) ) ? TRUE : FALSE;

             //Retreive comments for this task
             //$this->data['task_comments'] 	= $this->_construct_task_comments($this->pria_task_id);
             $this->data['task_comments'] 			= $this->_construct_task_comments();

             //Retreive attachment for this task
             $this->data['task_attachments'] 		= $this->_construct_task_attachment($this->pria_task_id);

             //If not empty load js and init
             if( $this->data['task_comments'] )
             {
                 $this->task_resources['loaded_init'][] = 'TaskComments.init("'.$this->module_code.'");';
                 $this->task_resources['load_js'][]     = $this->module_task_comment_js;
			 }

			 //Task documents ( Main documents ). Creates the 'task_param_documents' variable
			 $this->task_view_data['task_documents']  	= $this->_construct_task_documents($this->pria_task_id, $this->task_details['task_reference_id'], $this->task_details['task_status_id'], $this->task_details['has_approval'], $this->task_details['is_returned']);

			//print_var_export($this->task_view_data); die;

			 if($this->task_details['has_upload'] === TRUE && $this->task_details['actual_docs_complete'] === FALSE && EMPTY($this->task_details['user_id']) === FALSE)
			 {
				 //die('asdf');
				//$this->pwm_model->update_task(['task_status_id' => TASK_STATUS_ONGOING], ['pria_task_id' => $this->task_details['pria_task_id']]);
			 }


			 $this->data['enc_task_id']     			= encrypt_id($this->pria_task_id);
			
			 $this->data['task']['content'] 		 	= $this->load->view($this->path_task_views.'/'.$this->task_page,  $this->task_view_data, TRUE);

			 $this->data['hide_clock']					= TRUE;
			//  print_var_export($this->task_resources);
			//  print_var_export($this->task_view_data);
			//  die;
		
             $this->template->load($this->tpl_task_container, $this->data, $this->task_resources, Portal_Controller::$system);

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


	private function _set_page_title($module_code, $tab_module_code=NULL)
	{
		switch($module_code)
		{
			case MODULE_PORTAL_TRANS_CONTRACTORS:
				switch($tab_module_code)
				{
					case MODULE_PORTAL_TRANS_CONTRACTORS_SN:
							$site_name = ISSET($this->task_view_data['site_details']['official_store_name'])
										? $this->task_view_data['site_details']['official_store_name']
										: $this->task_view_data['site_details']['suggested_store_name'];


							$this->data['page_title'] = $this->data['page_title'].' - '.$site_name;
					break;

					case MODULE_PORTAL_TRANS_CONTRACTORS_BOQ:
							$this->data['page_title'] = $this->data['page_title'].' - '.$this->task_view_data['boq_details']['official_store_name'];
					break;

					case MODULE_PORTAL_TRANS_CONTRACTORS_PROJECTS:
							$this->data['page_title'] = $this->data['page_title'].' - '.$this->task_view_data['boq_details']['official_store_name'];
					break;
				}
			break;
		}
	}

	//private function _construct_task_comments($pria_task_id)
	private function _construct_task_comments()
	{
		try
		{
			$html 			= '';

			$task_status 	=  $this->task_details['task_status_id'];

			//$task_comments 	= $this->tm_model->get_task_comments_details($pria_task_id);
			$task_comments 	= $this->tm_model->get_task_comments_details($this->task_details['pria_stage_id']);

			$comment_html   = ( ! EMPTY($task_comments)) ? $this->load->view($this->tpl_task_comments, ['comments' => $task_comments, 'task_status' => $task_status], TRUE) : '';

			$array 			= [
								'task_comments'   => $comment_html,
								'task_status' 	  => $task_status,
								'has_approval'    => $this->task_details['has_approval'],
								'stage_status' 	  => $this->task_details['stage_status_code'],
								'workflow_status' => $this->task_details['workflow_status_code'],
								'returned_flag'   => $this->task_details['returned_flag']
			];
			$html 			= $this->load->view($this->tpl_task_comment_container, $array, TRUE);

			return $html;
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

	private function _construct_task_attachment($pria_task_id)
	{
		try
		{

			//permission_view

			$data 			= array();
			$view_per		= FALSE;
			$download_per 	= FALSE;

			if($this->permission_view) {
				$view_per = TRUE;
			}

			if($this->permission_download) {
				$download_per = TRUE;
			}

			$html 				= '';

			$hide 				= ($this->task_details['task_status_id'] == TASK_STATUS_ONGOING OR $this->task_details['task_status_id'] == TASK_STATUS_RETURNED) ? false : true;

			$hide_delete 		= ($this->task_details['task_status_id'] == TASK_STATUS_DONE ) ? false : true;

			$is_returned 		= ($this->task_details['returned_flag'] == ENUM_YES ) ? true : false;

			$task_attachments 	= $this->tm_model->get_document_details($this->task_details['pria_stage_id']);

			// $task_attachments 	= $this->tm_model->get_document_details($pria_task_id);

			$attachment_html    = $this->load->view($this->tpl_task_attachments, ['attachments' => $task_attachments, 'view_per' => $view_per, 'download_per' => $download_per, 'hide' => $hide, 'hide_delete' => $hide_delete, 'is_returned' => $is_returned], TRUE);

			$doc_info 			= $this->tm_model->get_task_doc_type($pria_task_id);

			$extra 				=  [
				'task_attachments' 	=> $attachment_html,
				'hide'				=> $hide,
				'pria_task_id' 		=> base64_url_encode($pria_task_id),
				'doc_type' 			=> base64_url_encode($doc_info['document_type_code']),
				'has_approval'		=> $this->task_details['has_approval'],
				'stage_status'		=> $this->task_details['stage_status_code'],
				'workflow_status'  	=> $this->task_details['workflow_status_code'],
			];

			$html 		= $this->load->view($this->tpl_task_attachment_container, $extra, TRUE);

			return $html;
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

	protected function _construct_task_documents($task_id, $reference_id, $status_id = NULL, $has_approval = FALSE, $is_returned = FALSE)
	{
		try
		{
			$force_show_version = FALSE;
			$doc		    	= [];
			$task_documents 	= $this->tm_model->get_pria_task_documents($task_id, $reference_id);

			//Added by Christian Oct 21, 2019
			$documents  		= $this->tm_model->get_task_doc_file_extension($task_id);

			$this->task_view_data['task_param_documents'] = $task_documents;

			$no_req_documents   = COUNT($task_documents);
			$actual_upd_docs	= 0;

			foreach($task_documents as $key => $td)
			{
				$document_type 	   		= $td['document_type_code'];
				$document_type_idx 		= strtolower($document_type);
				$document_type_idx_org 	= strtolower($document_type).'_orig_filename';

				$allowed_file_ext = $documents[$key]['allowed_ext'];
				$html_allowed = '<div class="red-text font-md">Allowed file extensions: '.$allowed_file_ext.' </div>';

				$stand_alone			= FALSE;

				switch($task_documents[0]['core_workflow_task_id'])
				{
					case CORE_TASK_BOQ_MCS_APPROVED:
						$stand_alone		= (in_array(ROLE_MCS_ENG, $this->session->user_roles))? TRUE : FALSE;
					case CORE_TASK_PROJ_BOQ_PROGRESS_APPROVED:
						$force_show_version = (in_array(ROLE_MCS_ENG, $this->session->user_roles))? TRUE : FALSE;
					break;
					case CORE_TASK_BOQ_INDICATE:
					case CORE_TASK_BOQ_INDICATE_CONFIRM_CONTRACT:
						$stand_alone		= (in_array(ROLE_FPA, $this->session->user_roles) AND $document_type == DOC_TYPE_RFA)? TRUE : FALSE;
						$force_show_version	= (in_array(ROLE_FPA, $this->session->user_roles) AND $document_type == DOC_TYPE_RFA)? TRUE : FALSE;
					break;
					case CORE_TASK_SITES_RECOM_SITE_NOMINATION:
						$stand_alone		= (in_array(ROLE_ROH, $this->session->user_roles) AND $document_type == DOC_TYPE_SITE_FORM) ? TRUE : FALSE;
						$force_show_version	= (in_array(ROLE_ROH, $this->session->user_roles) AND $document_type == DOC_TYPE_SITE_FORM) ? TRUE : FALSE;
					break;
				}

				if((!EMPTY($status_id) AND !in_array($status_id, [TASK_STATUS_ONGOING, TASK_STATUS_RETURNED])) OR $td['user_id'] == "")
				{
					$stand_alone		= FALSE;
					$force_show_version	= FALSE;
				}

				if( EMPTY($td['document_id']) )
				{
					$html = <<<EOS
					<div class="input-field">
						<a href="#" id="{$document_type_idx}_upload" class="center m-r-sm file-task-attach">Attach</a>

						<input type="hidden" name="task_doc_type[]" value="$document_type_idx"/>
						<input type="hidden" name="$document_type_idx" id="$document_type_idx" value=""/>
						<input type="hidden" name="$document_type_idx_org" id="$document_type_idx_org" value=""/>
					</div>
EOS;
				}
				else
				{
					$actual_upd_docs++;

					$html = create_document_tag($td, TRUE, $status_id, $has_approval, $is_returned, $force_show_version, $stand_alone);
				}


				$doc[$document_type] = $html;
			}

			$this->task_details['actual_docs_complete'] = ($no_req_documents == $actual_upd_docs) ? TRUE : FALSE;

			return $doc;
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




/* 	private function _send_email_notifications($task_status_id, $task_details, $user_id, $extra)
	{
		try
		{
			$core_task_id	= (ISSET($task_details['core_workflow_task_id']) AND !EMPTY($task_details['core_workflow_task_id']))? encrypt_id($task_details['core_workflow_task_id']): NULL;

			$pria_task_id	= (ISSET($task_details['pria_task_id']) AND !EMPTY($task_details['pria_task_id']))? encrypt_id($task_details['pria_task_id']): NULL;

			switch($task_status_id)
			{
				case TASK_STATUS_DONE:

					$task_action		= (ISSET($task_details['returned_flag']) AND !EMPTY($task_details['returned_flag']) AND $task_details['returned_flag'] == ENUM_YES) ? TASK_ACTION_RESUBMITTED: NULL;

					$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, $task_action);
				break;

				case TASK_STATUS_APPROVED:
					$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, TASK_ACTION_APPROVED, NULL, $user_id);
				break;

				case TASK_STATUS_RETURNED:
					$return_pria_task_id = (!EMPTY($extra['task_return_id']))? encrypt_id($extra['task_return_id']): NULL;

					$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, TASK_ACTION_RETURNED, NULL, $user_id, $return_pria_task_id);
				break;
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

	private function _send_sys_notifications($task_status_id, $task_ret_details, $module_code, $user_id, $extra, $status_label = '', $task_orig_details = array())
	{
		try
		{
			$sys_notif_role     = [];

			if($task_orig_details['sys_notif_role']){

				//$str_arr = explode (",", $task_ret_details['sys_notif_role']);

				$sys_notif_role = array(array('role_code' => $task_orig_details['sys_notif_role']));
			}

			switch($task_status_id)
			{
				case TASK_STATUS_DISAPPROVED:
				case TASK_STATUS_APPROVED:
				case TASK_STATUS_DONE:
					$this->system_notification($task_ret_details['pria_task_id'], $task_orig_details, $module_code, $sys_notif_role, NULL, NULL, NULL, $status_label, $task_orig_details);
				break;

				case TASK_STATUS_RETURNED:
					$this->system_notification($task_ret_details['pria_task_id'], $task_ret_details, $module_code, $sys_notif_role, $extra['task_return_id'], $user_id, TRUE, $status_label, $task_orig_details);
				break;
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

	private function _set_tat_dates(&$fields, $task_id, $task_details)
	{
		try
		{
			//Added condition by Christian - if system_based_flag is yes system generated dates will be used in actual dates
			//July 23, 2019
			if($task_details['system_based_tag'] == YES_FLAG)
			{
				$fields['actual_end_date'] = date(FORMAT_DB_DATETIME);

				if(EMPTY($task_details['start_date']))
					$fields['start_date'] = date(FORMAT_DB_DATETIME);

				if(EMPTY($task_details['actual_start_date']))
					$fields['actual_start_date'] = date(FORMAT_DB_DATETIME);
			}

			//Added condition by Christian - if due_date_tag is yes get predecessors "actual_end_date"  = "expected_start_date" , "actual_end_date" + tat = expected_end_date"
			//July 23, 2019
			if($task_details['due_date_tag'] == YES_FLAG)
			{
				//get predecessors
				$where 	= array('pria_task_id' => $task_id);
				$pre 	= $this->tm_model->get_next_predecessors($where);

				//get predecesors details
				if($pre)
				{
					$pre_task_details 				= $this->tm_model->get_task_details($pre[0]['pre_pria_task_id']);

					$fields['expected_start_date'] 	= $pre_task_details['actual_end_date'];
					$fields['expected_end_date'] 	= date('Y-m-d H:i:s',strtotime($pre_task_details['actual_end_date']) + ((86400 * $task_details['tat'])) - 86400);
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

	//private function _update_data_upon_complete($reference_id, $core_workflow_task_id, $task_status_id)
	private function _update_data_upon_complete($core_workflow_task_id, $task_details)
	{
		try
		{
			$reference_id = $task_details['reference_id'];

			switch($core_workflow_task_id)
			{
				case CORE_TASK_ENCODE_PROFIT_COST:
					$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

					$this->sn_model->update_site(['status_code' => PARAM_STATUS_COMPLETED], ['site_id' => $reference_id]);
				break;

				case CORE_TASK_ENCODE_ASSET_CODE:
					$this->load->model(FOLDER_BOQ.'/boq_model', 'bq_model');

					$this->bq_model->update_boq(['status_code' => PARAM_STATUS_COMPLETED], ['boq_id' => $reference_id]);
				break;

				case CORE_TASK_OPENING_DATE:
					$this->load->model(FOLDER_PROJECTS.'/projects_model', 'pj_model');

					$this->pj_model->update_project(['status_code' => PARAM_STATUS_COMPLETED], ['project_id' => $reference_id]);
				break;

				case CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION:
					if($task_details['task_status_id'] == TASK_STATUS_DISAPPROVED)
					{
						$this->load->model(FOLDER_SITE_NOMINATIONS.'/site_nominations_model', 'sn_model');

						$this->sn_model->update_site(['status_code' => PARAM_STATUS_DISAPPROVED], ['site_id' => $reference_id]);
					}
				break;
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
	} */

	/* private function _update_dependent_tasks($task_id, $user_id, $task_details)
	{
		try
		{
			$user_roles 	  = $this->tm_model->get_users_role(['user_id' => $user_id], ['role_code']);

			$user_roles 	  = array_column($user_roles, 'role_code');

			$dependents 	  = $this->tm_model->get_dependent_tasks($task_id);

			$dependents_ids   = array_column($dependents, 'pria_task_id');

			//Get the predecessors of the dependent tasks
			$dependents_prede = $this->tm_model->get_task_predecessors($dependents_ids);

			foreach($dependents as $d)
			{
				//Checks if the dependent task is ready to be cleared...
				if( ! $this->_check_task_completion_rules($d['core_workflow_task_id'], $d['pria_workflow_reference_id']) )
					continue;

				$clear_task = FALSE;
				$dep_id   	= $d['pria_task_id'];

				//Check if dependent has other predecessors. If no, enable, else, check.
				if( ISSET($dependents_prede[$dep_id]) )
				{
					$result = $this->_check_dependent_predecessor($dependents_prede[$dep_id]);

					if($result)
						$clear_task = TRUE;
				}
				else
				{
					$clear_task = TRUE;
				}

				//Enables the task
				if($clear_task)
				{

					$this->_clear_n_assign_next_task($dep_id, $d['core_workflow_task_id'], $d['org_code'], $task_details, $d['vendor_code']);

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
	} */

	/**
	 * @Author: kevin villarojo
	 * @Date: 2019-09-12 11:54:51
	 * @Desc:  Updates the task assigned user and task status to pending
	 * @Reference by : Quick Add, Task_Controller
	 */
/* 	public function _clear_n_assign_next_task($dep_pria_task_id, $dep_core_task_id, $org_code, $task_details=[], $vendor_code='')
	{
		try
		{
			$user_id 	= '';
			//Set initial details to update
			$fields   	= array('task_status_id' => NULL);
			$where 	  	= array('pria_task_id' => $dep_pria_task_id);

			//Gets the next role
			$roles 	  	= $this->tm_model->get_task_roles(['pria_task_id' => $dep_pria_task_id, 'actor_flag' => INITIAL_YES], ['role_code']);
			$role_codes = array_column($roles, 'role_code');

			//Determines if next task will be auto_assigned
			if(in_array(TASK_ROLE_VENDOR, $role_codes))
			{
				//$users 			= $this->tm_model->get_vendor_users(['vendor_code' => $vendor_code], ['user_id']);
				$users 			= $this->tm_model->get_vendor_users_by_vendor_code_n_status($vendor_code);
			}
			else
			{
				$valid_orgs		= $this->_get_parent_orgs($org_code);
				$valid_orgs[]	= $org_code;

				$users   	 	= $this->tm_model->get_users_by_roles_n_org($role_codes, $valid_orgs);
			}

			switch($dep_core_task_id)
			{
				case CORE_TASK_RETURN_DELIVERY_RECEIPT:
					$this->load->model(FOLDER_DELIVERY_GOODS.'/Delivery_goods_model', 'dr_model');

					$recipient = $this->dr_model->get_delivery_goods_receipts(['dr_gr_id' => $task_details['task_reference_id']], ['dr_recipient_id']);

					$user_id   = $recipient['dr_recipient_id'];
				break;

				case CORE_TASK_ENCODE_GOOD_RECEIPT:
					$this->load->model(FOLDER_DELIVERY_GOODS.'/Delivery_goods_model', 'dr_model');

					//get pria_refereces
					$reference_info = $this->dr_model->get_dr_references(['po_id' => $task_details['reference_id']]);

					if(!EMPTY($reference_info['dr_gr_id'])){
						$recipient = $this->dr_model->get_delivery_goods_receipts(['dr_gr_id' => $reference_info['dr_gr_id']], ['dr_recipient_id']);

						$user_id   = $recipient['dr_recipient_id'];
					}
				break;


				//added by christian
				case CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES:
					$this->load->model(FOLDER_DELIVERY_GOODS.'/Delivery_goods_model', 'dr_model');

					//get pria_refereces
					$recipient = $this->dr_model->get_delivery_goods_receipts(['dr_gr_id' => $task_details['task_reference_id']], ['dr_recipient_id']);

					$user_id   = $recipient['dr_recipient_id'];
				break;
				//ends

				default:
					if(COUNT($users) == 1)
						$user_id = $users[0]['user_id'];
			}

			if( ! EMPTY($user_id))
			{
				$fields['user_id'] 		= $user_id;
				$actor 					= $this->_task_actor_name($user_id);
				$fields['actor_name'] 	= [$actor, 'ENCRYPT'];
			}

			$this->pwm_model->update_task($fields, $where);
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

	private function _get_parent_orgs($org_code, $parents=[])
	{
		try
		{
			$org_dets    = $this->tm_model->get_organization(['org_code' => $org_code], ['org_parent']);

			$parent_org  = $org_dets['org_parent'];

			if( ! EMPTY($parent_org))
			{
				$parents[] = $parent_org;

				$parents   = $this->_get_parent_orgs($parent_org, $parents);
			}

			return $parents;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} */

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-08-07 08:03:26
	 * @Desc: Defines special rules before taggging the next task as ongoing/cleared or the current task
	 * If used in current task, mostly used to determine if notification will be triggered
	 * @ReferencedBy:  Task.php
	 */
/* 	protected function _check_task_completion_rules($core_workflow_task_id, $reference_id)
	{
		try
		{
			switch($core_workflow_task_id)
			{

				//Codes below are used in next task ( _update_dependent_tasks ) function
				case CORE_TASK_DOC_DR:
					return $this->_check_w_last_dr(DR_MEDVAC, $reference_id);
				break;
				case CORE_TASK_CLEANUP:
				case CORE_TASK_HARVEST_REPORT:
				case CORE_TASK_LIVE_SALES_REPORT:
					return $this->_check_w_last_dr(DR_DOCDR, $reference_id);
				break;
			}

			return TRUE;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} */

	/**
	 * @Author: Kevin Villarojo
	 * @Date: 2019-08-07 08:03:26
	 * @Desc:
	 * @ReferencedBy:  Task.php
	 */
/* 	protected function _check_w_last_dr($dr_type, $reference_id)
	{
		try
		{
			$this->load->model(FOLDER_DELIVERY_GOODS.'/delivery_goods_model', 'dgm_model');

			$last_dr = $this->dgm_model->get_last_delivery($dr_type, $reference_id, ['a.last_dr_flag', 'a.dr_gr_id', 'a.gr_pria_task_id']);

			return ( ! EMPTY($last_dr) ) ? TRUE : FALSE;
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
 */


  /**
	 * @Author: Kevin Villarojo
	 * @Date:
	 * @Desc: Checks the predecessor task status of the dependent tasks
	 * @ReferencedBy:  Task.php
	 */
/*
	protected function _check_dependent_predecessor($predecessors)
	{
		try
		{
			$return = TRUE;

			foreach($predecessors as $p)
			{
				if(in_array($p['task_status_id'], [TASK_STATUS_ONGOING, TASK_STATUS_RETURNED]))
					$return = FALSE;
			}

			return $return;
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

   private function _open_prev_task($pria_task_id = NULL, $predecessor_ids = NULL)
    {
        try{

            //Chosen task to return to
            $return_ids[] = $predecessor_ids;

            $this->_get_return_records($predecessor_ids, $pria_task_id, $return_ids);

            $fields = array('task_status_id' => TASK_STATUS_RETURNED);
			$where  = array('pria_task_id' => ['IN' => $return_ids]);

			$this->pwm_model->update_task($fields, $where);

			$this->pwm_model->update_task(['returned_flag'   => YES_FLAG], ['pria_task_id' => $predecessor_ids]);
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

    private function _get_return_records($pria_task_id, $current_pria_task_id, &$return_ids)
    {
        try
        {
            $where      = ['pre_pria_task_id' =>  $pria_task_id];
            $records    = $this->pwm_model->get_task_predecessors($where);

            foreach($records as $r)
            {
                if($r['pria_task_id'] == $current_pria_task_id) return;

                $return_ids[] = $r['pria_task_id'];

                $this->_get_return_records($r['pria_task_id'], $current_pria_task_id, $return_ids);
            }

            return;
        }
        catch(PDOException $e)
        {
            throw $e;
        }
	}

	private function _insert_remarks_as_comment($pria_task_id, $remarks, $user_id)
	{
		try
		{
			//Insert remarks as as comment
			$comment = [
				'pria_task_id'		=> $pria_task_id,
				'pria_task_comment' => '<p>'.$remarks.'</p>',
				'created_by' 		=> $user_id,
				'created_date'  	=> date(FORMAT_DB_DATETIME)
			];

			$this->tcm_model->insert_task_comment($comment);
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

	private function _cancel_succeeding_tasks($task_details)
	{
		try
		{
			//Gets the current sequence and stage id
			$curr_pria_stage_id 	= $task_details['pria_stage_id'];
			$curr_pria_stage_seq	= $task_details['pria_stage_sequence_no'];
			$curr_pria_task_seq  	= $task_details['sequence_no'];

			$fields = ['task_status_id'	 => TASK_STATUS_CANCELLED];

			//Updates all task under the same stage to "Cancelled"
			$where  = ['sequence_no'	 => ['>' => $curr_pria_task_seq],  'pria_stage_id' => $curr_pria_stage_id];

			$this->pwm_model->update_task($fields, $where);

			//Gets the succeeding stages
			$where  	= array('pria_workflow_id' => $task_details['pria_workflow_id'], 'sequence_no'	 => ['>' => $curr_pria_stage_seq]);
            $fields_arr = array('pria_stage_id');
			$stages 	= $this->pwm_model->get_stages($where, $fields_arr);

			$stage_ids 	= array_column($stages, 'pria_stage_id');

			//Updates all task under the succeeding stage to "Cancelled"
			$where  	= ['pria_stage_id' => ['IN' => $stage_ids]];

			$this->pwm_model->update_task($fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} */

    protected function _task_actor_name($user_id)
    {
        try
        {
			$user_details = $this->tm_model->get_core_user(['user_id' => $user_id], [
				aes_crypt('fname', FALSE),
				aes_crypt('mname', FALSE),
				aes_crypt('lname', FALSE),
				aes_crypt('ext_name', FALSE)
			]);

			$actor  = trim(
				$user_details['fname'].' '.
				$user_details['mname'].' '.
				$user_details['lname'].' '.
				$user_details['ext_name']
			);

            return $actor;
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

	/**
	 * @Author: kevin villarojo
	 * @Date: 2019-10-10 17:07:30
	 * @Desc:
	 * @Referenced: SOA.php, PO.php
	 */
/* 	protected function get_organizations_by_org_type_w_scope($module, $org_type=ORG_TYPE_BUSINESS_CENTER)
	{

		$scope_details   = get_scope_details($module);
		$org_codes       = ['org_type_code' => $org_type];

		if( ! EMPTY($scope_details['orgs']) )
			$org_codes = ['org_code' => ['IN', $scope_details['orgs']] ];

		return $this->tm_model->get_organizations($org_codes, ['org_code', 'name']);
	}
 */
}