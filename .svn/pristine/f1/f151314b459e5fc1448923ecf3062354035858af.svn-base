<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends Task_Controller
{
    protected $module_code;

	public function __construct()
	{
		parent::__construct();

        $this->load->library('pria_workflow');
        $this->load->model('documents_model', 'dm_model');
        $this->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');
	}

	public function process()
	{
		try
        {
            $flag   = ERROR;
            $fields = $this->_validate();

            $fields['version']          = 1;
            $fields['created_date']     = date(FORMAT_DB_DATETIME);
            $fields['created_by']       = $this->session->user_id;

            $last_upload                = $fields['last_upload'];

            unset($fields['last_upload']);

            Portal_Model::beginTransaction();

            $document_type = $this->dm_model->get_param_document_types(['document_type_code' => $fields['document_type_code']], ['document_type_name']);

            $audit_action  = [AUDIT_INSERT];
            $audit_table   = [Portal_Model::PORTAL_TABLE_DOCUMENTS];
            $audit_schema  = [DB_PORTAL];
            $prev_detail   = [array()];
            $activity      = sprintf($this->lang->line('audit_trail_add'), $document_type[0]['document_type_name']);

            $task_details  = $this->tm_model->get_task_details($fields['pria_task_id']);

            $fields['pria_stage_id'] = $task_details['pria_stage_id'];

            $document_id   = $this->dm_model->insert_document($fields);

            if($last_upload)
                $this->pria_workflow->_send_email_notifications($task_details['task_status_id'], $task_details, $this->session->user_id);

            $curr_detail   = [ $this->dm_model->get_details_for_audit( Portal_Model::PORTAL_TABLE_DOCUMENTS, ['document_id' => $document_id]) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag 	= SUCCESS;

            $msg   = $this->lang->line('data_saved');
        }
        catch(PDOException $e)
        {
            $msg 	= $this->get_user_message($e);

            Portal_Model::rollback();
        }
        catch(Exception $e)
        {
            $msg  	= $this->rlog_error($e, TRUE);

            Portal_Model::rollback();
        }

        echo json_encode([
            'flag'  => $flag,
            'msg'   => $msg
        ]);
    }

    private function _validate()
    {
        try
        {
            $params = get_params();

            //Filters the data inputted by the user
			$params = $this->set_filter( $params )
            ->filter_string('filename')
            ->filter_string('sysfilename')
            ->filter_string('module_code')
            ->filter_string('task_id')
            ->filter_string('lastUpload')
            ->filter();


            //Define the required fields.
            $required = [
                'filename'       => 'File',
                'sysfilename'    => 'File',
                'doctype'        => 'Document Type',
                'docref'         => 'Document Reference',
                'module_code'    => 'Module Code',
                'task_id'        => 'Task Id'
            ];

            $constraints['filename']	    = [
                'data_type'			=> 'string',
                'name'				=> 'File'
            ];

            $constraints['sysfilename']	    = [
                'data_type'			=> 'string',
                'name'				=> 'File'
            ];

            $constraints['doctype']	    = [
                'data_type'   => 'db_value',
                'name'        => 'Document Type',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'document_type_code',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PARAM_DOCUMENT_TYPES
            ];

            $constraints['module_code']	= [
                'data_type'   => 'db_value',
                'name'        => 'Module Code',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'module_code',
                'table'       =>  Portal_Model::CORE_MODULES
            ];

            $constraints['task_id']     = [
                'data_type'         => 'string',
                'name'              => 'Task ID'
            ];

            $constraints['lastUpload']	= array(
                'data_type'			=> 'enum',
                'name'				=> 'Last Upload',
                'allowed_values' 	=> [TRUE, FALSE]
            );

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            $reference = decrypt_id($params['docref']);

            /* Validate constraints */
            $data = $this->validate_inputs($params, $constraints);

            $this->module_code = $data['module_code'];

            return [
                'reference'          => $reference,
                'document_type_code' => $data['doctype'],
                'file_name'          => $data['filename'],
                'sys_file_name'      => $data['sysfilename'],
                'module_code'        => $data['module_code'],
                'pria_task_id'       => $data['task_id'],
                'last_upload'        => $data['lastUpload']
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
}