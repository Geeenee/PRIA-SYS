<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_attachment extends Task_Controller
{
	private $module_code;
  	protected $module_folder;
  	protected $module_transactions;
  	protected $controller;

  	protected $permission_view;
  	protected $permission_add;

	protected $model_name = 'Task_attachment_model';

	protected $module_task_js		= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/task";

	public function __construct()
	{
		parent::__construct();

		$this->controller 			= strtolower(__CLASS__);
		$this->module_code      	= MODULE_PORTAL_TASK_ATTACHMENT;
		$this->module_folder 		= PORTAL_COMMON;
		$this->module_transactions 	= PORTAL_TRANSACTIONS;

		$this->load->model($this->module_folder. '/'.$this->model_name, 'task_attachment_model');
		$this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
		$this->load->model($this->module_transactions.'/documents_model', 'documents_model');
		$this->load->library('pria_overview');

		$this->permission_view		= $this->permission->check_permission($this->module_code, ACTION_VIEW);
		$this->permission_add		= $this->permission->check_permission($this->module_code, ACTION_ADD);
	}

	public function modal_task_upload($version_id = NULL, $doc_type = NULL)
	{
		try
		{
			$data 		= array();
			$resources 	= array();

			$resources['load_js'] 	= array(JS_UPLOAD, $this->module_task_js);
			$resources['load_css'] 	= array(CSS_UPLOAD);

			//DOC_TYPE_TASK_ATTACHMENT;
			$where = array(
				'document_type_code' => DOC_TYPE_TASK_ATTACHMENT
			);

			$doc_type = $this->documents_model->get_param_document_types($where);
			$task_attachment_files = $doc_type[0]['allowed_extensions'];

			$resources['upload'] = array(
				'attachments' => array(
					'path' 					=> PATH_UPLOADED_FILES,
					'allowed_types' 		=> $task_attachment_files,
					'multiple' 				=> TRUE,
					'max_file'				=> 999,
					'drag_drop' 			=> TRUE,
					'show_preview' 			=> TRUE,
					'successCallback'       => "Task.successCallback();"
				)
			);

			$data['prim_id']	= $version_id;
			$data['doc_type']	= $doc_type[0]['document_type_code'];

			$modal = "modals/attach_file";
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


	public function process()
	{
		try{
			$flag 			= 0;
			$status 		= ERROR;
			$params 		= get_params();

			$multiple_file_name = $params['multiple_file_name'];
			$file_orig          = $params['file_orig'];
			$primary_id 	    = base64_url_decode($params['prim_id']);
			$doc_type 		    = $params['doc_type'];

			if(EMPTY($multiple_file_name[0]) || EMPTY($file_orig[0]))
				throw new Exception($this->lang->line('err_required_attachment_file'));

			//Start the db transaction
            Portal_Model::beginTransaction();

        	//fetching CURRENT document version info
			$table_version 	= Portal_Model::PORTAL_TABLE_DOCUMENT_VERSIONS;
			$table 			= Portal_Model::PORTAL_TABLE_DOCUMENTS;

			//Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $primary_id];

            $document_type_det  = $this->task_attachment_model->get_specific_task_document_type($where, $field_select, [], FALSE);

            $document_type_code = $document_type_det['document_type_code'];

            //log to overview
			//Starts
			$task_details 		= $this->tm_model->get_task_details($primary_id);

			$parent_module_code	= $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

			foreach($file_orig as $key => $value)
			{
				$fields = array(
					'reference'				=> $primary_id,
					'pria_task_id'			=> $primary_id,
					'pria_stage_id'			=> $task_details['pria_stage_id'],
					'document_type_code'	=> $doc_type,
					'file_name'				=> $value,
					'sys_file_name'			=> $multiple_file_name[$key],
					'version'				=> 1,
					'module_code' 			=> $parent_module_code,
					'created_by'			=> $this->session->userdata('user_id'),
					'created_date'			=> date('Y-m-d H:i:s')
				);

				$id = $this->task_attachment_model->insert_doc_info($table, $fields, TRUE );

				$overview_type		   	= OVERVIEW_TYPE_ADD_TASK_ATTACHMENT;
				//Get the transaction module code not the tab module code

				$overview_details   	= [
					'filename' 					=> $value,
					'reference' 				=> $task_details['reference_id'],
					'created_by' 				=> $this->session->userdata('user_id'),
					'created_date' 				=> date('Y-m-d H:i:s'),
					'account_group_code' 		=> $task_details['account_group_code'],
					'pria_task_attachment_id'  	=> $id,
				];

				$overview_details 	= array_merge($task_details, $overview_details);

				$this->pria_overview->log_overview($parent_module_code, $overview_type, $overview_details);
			}

			//Ends

			$activity     	= sprintf($this->lang->line('audit_trail_add'), ' Task file');
			$prev_detail 	= array();
			$curr_detail 	= $task_details;
			$audit_action 	= [AUDIT_INSERT];
			$audit_table  	= [$table];
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

	public function delete_attachment()
	{
		try
		{
			$status 	= ERROR;
			$params		= get_params();

			$document_id = base64_url_decode($params['param_1']);

			$where			= array('document_id' => $document_id);

			$old_info 		= $this->documents_model->get_document($where);

			Portal_Model::beginTransaction();

			$where			= array('document_id' => $document_id);
			$this->documents_model->delete_document($where);

			$audit_action[]	= AUDIT_DELETE;
			$audit_table[]	= Portal_Model::PORTAL_TABLE_DOCUMENTS;
			$audit_schema[]	= DB_PORTAL;
			$prev_detail[]	= array($old_info);
			$curr_detail[]	= array();
			$activity		= $old_info['file_name'] . " has been deleted.";

			$this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

			Portal_Model::commit();

			$status = SUCCESS;
			$msg 	= $this->lang->line('data_deleted');

		}
		catch(PDOException $e)
		{
			Portal_Model::rollback();

			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			Portal_Model::rollback();

			$msg = $this->rlog_error($e, TRUE);
		}

		$info = array(
				"status"			=> $status,
				"msg"				=> $msg
		);

		echo json_encode($info);
	}
}