<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File_version extends Portal_Controller
{
	private $module_code;
  	private $module_folder;
  	private $controller;
  	private $module_js;

  	private $permission_upload;
  	private $permission_download;

	protected $model_name = 'File_version_model';

	protected $module_task_comment_js		= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/task";

	public function __construct()
	{
		parent::__construct();

		$this->controller 		= strtolower(__CLASS__);
		$this->module_code      = MODULE_PORTAL_FILE_VERSION;
		$this->module_folder 	= PORTAL_COMMON;
		$this->module_js 		= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_FILE_VERSION."/".$this->controller;

		$this->load->model($this->module_folder. '/'.$this->model_name, 'file_version_model');
		$this->load->model(PORTAL_TRANSACTIONS.'/documents_model', 'documents_model');
		$this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
		
		$this->permission_view		= $this->permission->check_permission($this->module_code, ACTION_VIEW);
		// $this->permission_add		= $this->permission->check_permission($this->module_code, ACTION_ADD);
		$this->permission_upload	= $this->permission->check_permission($this->module_code, ACTION_UPLOAD);
		$this->permission_download	= $this->permission->check_permission($this->module_code, ACTION_DOWNLOAD);
	}

	public function modal_file_version($reference = NULL, $table_type = NULL, $document_id = FALSE)
	{
		try
		{
			$data 					= array();
			$data['view_per'] 		= FALSE;
			$data['upload_per'] 	= FALSE;
			$data['download_per'] 	= FALSE;

			if($this->permission_view) {
				$data['view_per'] = TRUE;
			}

			if($this->permission_download) {
				$data['download_per'] = TRUE;
			}

			if($this->permission_upload) {
				$data['upload_per'] = TRUE;
			}
			
			if( ! $this->permission_view){
				throw new Exception($this->lang->line('err_unauthorized_access'));
			}
			
			//primary ID of document
			$reference = base64_url_decode($reference);
			$table_type = base64_url_decode($table_type);
			$document_id = base64_url_decode($document_id);

			if($reference){

				$fields = array('*');
				$where 	= array(
					'reference' => $reference,
					'document_type_code' => $table_type,
					'document_id' => $document_id
				);

				$current_doc_info 			= $this->documents_model->get_document($where, $fields );
				$data['document_id'] 		= base64_url_encode($current_doc_info['document_id']);

				$data['current_doc_info'] 	= $current_doc_info;

				if($current_doc_info['created_by']){
					if(!EMPTY($current_doc_info['modified_by'])){
						$data['current_user_info'] = $this->users_model->get_user_details($current_doc_info['modified_by']);
					}else{
						$data['current_user_info'] = $this->users_model->get_user_details($current_doc_info['created_by']);
					}   
				}

	            //for fetching OTHER document version info
				$fields = array('*');
				$where 	= array(
					'reference' => $reference,
					'document_type_code' => $table_type,
					'document_id' => $document_id
				);

				$other_doc_info 			= $this->documents_model->get_document_versions($where, $fields );
				$data['other_doc_info'] 	= $other_doc_info;
			}
			
			$modal 					= "modals/portal_file_version";
		} 
		catch (PDOException $e)
		{
			$msg  = $this->get_user_message($e);

     		 $this->error_modal( $msg );
		} 
		
		catch (Exception $e) 
		{
			 $msg  = $this->get_user_message($e);

     		 $this->error_modal( $msg );
		}

		$this->load->view($modal, $data);
	}

	public function modal_upload_file($document_id = NULL, $doc_type = NULL, $fa_flag = FALSE)
	{
		try 
		{
			$data 		= array();
			$resources 	= array();

			$resources['load_js'] 	= array(JS_UPLOAD, $this->module_task_comment_js);
			$resources['load_css'] 	= array(CSS_UPLOAD);

			$resources['upload'] = array(
				'file_version' => array(
					'path' 					=> PATH_UPLOADED_FILES,
					'allowed_types' 		=> '*',
					'multiple' 				=> FALSE,
					'max_file'				=> 999,
					'drag_drop' 			=> TRUE,
					'show_preview' 			=> TRUE,
					'successCallback'       => "Task.successCallback();"
				)
			);

			$data['document_id']		= $document_id;
			$data['doc_type']			= $doc_type;

			$data['from_file_attach']	= $fa_flag;

			$modal = "modals/upload_file";
		} 
		catch (PDOException $e)
		{
			$msg  = $this->get_user_message($e);

     		 $this->error_modal( $msg );
		} 
		
		catch (Exception $e) 
		{
			 $msg  = $this->get_user_message($e);

     		 $this->error_modal( $msg );
		}

		$this->load->view($modal, $data);
		$this->load_resources->get_resource($resources);
	}


	public function insert_file_version()
	{
		try{
			$flag 			= 0;
			$status 		= ERROR;
			$params 		= get_params();

			$file_name 		= $params['version_file'];
			$sys_file_name 	= $params['version_sysfile'];
			$document_id 	= base64_url_decode($params['prim_id']);
			$doc_type 		= base64_url_decode($params['doc_type']);

			//Start the db transaction                
            Portal_Model::beginTransaction();

			$fields = array('MAX(version) max_version');
			$where 	= array(
				'document_id' 			=> $document_id
			);

			$version 		= $this->documents_model->get_max_version($where, $fields);
			//$version 		= $this->_get_max_version($table, $where);

			// insert to document_version from document
			// update document
			if(EMPTY($file_name)){
				throw new Exception('File is required.');
			}

			$update_fields = array(
				'file_name' 	=> $file_name,
				'sys_file_name' => $sys_file_name,
				'version' 		=> $version,
				'modified_by' 	=> $this->session->userdata('user_id'),
				'modified_date' => date('Y-m-d H:i:s')
			);

			$doc_version_det = $this->_copy_document_version($document_id, $update_fields);

			$activity     	= sprintf($this->lang->line('audit_trail_add'), ' File');
			$prev_detail 	= array();
			$curr_detail 	= $doc_version_det;
			$audit_action 	= [AUDIT_INSERT];
			$audit_table  	= [Portal_Model::PORTAL_TABLE_DOCUMENT_VERSIONS];
			$audit_schema 	= [DB_PORTAL];

			$this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

			Portal_Model::commit();

			$flag 	= 1;
			$msg 	= $this->lang->line('succ_file_uploaded');
			$status = SUCCESS;
		}
        catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);

            Portal_Model::rollback();
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);  

            Portal_Model::rollback();
        }

        echo json_encode([
            'flag'  => $flag,
            'msg'   => $msg,
            'status' => $status
        ]);
	}

	private function _get_max_version($table_name = NULL, $where = array())
	{
		try{

			$max_version = $this->file_version_model->get_max_version($table_name, $where);
			
			return $max_version;
		}
		catch(PDOException $e)
	    {
	    	$msg  = $this->get_user_message($e);

     		$this->error_modal( $msg );
	    }
	    catch(Exception $e)
	    {
	      	$msg  = $this->get_user_message($e);

     		$this->error_modal( $msg );
	    }
	}

	private function _copy_document_version($document_id = NULL, $update_fields = array())
	{
		try{

			//get docuemnt version details
			$where 			= array('document_id' => $document_id);
			$fields 		= array('*');
			$document_info 	= $this->documents_model->get_document($where, $fields);

			$fields = array(
				'document_id' 			=> $document_info['document_id'],
				'reference'				=> $document_info['reference'],
				'document_type_code' 	=> $document_info['document_type_code'],
				'file_name' 			=> $document_info['file_name'],
				'sys_file_name' 		=> $document_info['sys_file_name'],
				'version' 				=> $document_info['version'],
				'created_by'			=> $document_info['created_by'],
				'created_date'			=> $document_info['created_date'],
				'modified_by'			=> $document_info['modified_by'],
				'modified_date'			=> $document_info['modified_date'],
			);

			//if exist, insert record from document table to version table
			if($document_info){
				$this->documents_model->insert_document_versions($fields);

				//$this->pria_overview->log_overview($parent_module_code, $overview_type, $overview_details);
			}

			//update doc info table
			$new_doc_details = $this->documents_model->update_document($update_fields, $where);

			return $new_doc_details;
		}
		catch(PDOException $e)
	    {
	    	$msg  = $this->get_user_message($e);

     		$this->error_modal( $msg );
	    }
	    catch(Exception $e)
	    {
	      	$msg  = $this->get_user_message($e);

     		$this->error_modal( $msg );
	    }
	}
	
}