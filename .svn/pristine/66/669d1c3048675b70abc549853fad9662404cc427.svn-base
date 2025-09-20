<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_project_completion extends Task_Controller
{
	public function __construct()
	{
		parent::__construct();

        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACTORS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CONTRACTORS_PROJECTS;
        $this->folder           = FOLDER_PROJECTS;
        $this->module_js		= $this->system_js_path.$this->module_folder.DS.strtolower(__CLASS__);

        $this->load->model(FOLDER_BOQ.'/boq_model', 'bq_model');
        $this->load->model($this->folder.'/projects_model', 'pj_model');

        $this->permissions      = check_permission($this->tab_module_code);

        $this->path_task_views .= $this->folder;
	}

	public function index()
	{
		try
		{
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params         = get_params(TRUE, TRUE);

            $task_id        = base64_url_decode($params['t']);

            $this->_initialize_task($task_id);

            $this->task_resources['load_js'][]      = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]      = JS_SELECTIZE;
            $this->task_resources['load_js'][]      = $this->module_js;
            $this->task_resources['load_css'][]     = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][]     = CSS_SELECTIZE;
            $this->task_resources['loaded_init'][]  = 'uploadProjCompletion.initTask();';

            $proj_id        = $this->task_details['reference_id'];

            $proj_details   = $this->pj_model->get_project(['project_id' => $proj_id], ['boq_id', 'project_code']);

            $boq_details    = $this->bq_model->get_boq_details($proj_details['boq_id']);

            $this->task_view_data['boq_details']    = $boq_details;
            $this->task_view_data['proj_details']   = $proj_details;

            $this->task_view_data['file_list']      = $this->tm_model->get_pria_task_documents($task_id, $proj_id);

           /*  $where          = ['document_type_code' => DOC_TYPE_BOQ_PROGRESS, 'reference' => $proj_id];
            $boq_progress   = $this->dm_model->get_document($where); */

            $this->task_details['task_reference_id'] = $proj_id;

            //If There's an uploaded file hide save because edit will be in versioning
            if( ! EMPTY($boq_progress))
                $this->task_access['hide_btn'] = TRUE;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.
         /*    $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];

            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */
            //print_var_export($this->task_upload, $this->task_resources); die;
            //Load the content of the task
            $this->data['page_title']         = 'Project: '.$proj_details['project_code'];
            $this->task_page                  = '/upload_project_completion';

            $this->_load_task_view();
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_page( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_page( $msg );
		}
	}

    public function process()
    {
        try
        {

            $flag          = ERROR;
            $status        = '';
            $msg           = '';
            $task          = '';
            $doc_ref       = '';
            $params        = $this->_validate();
            /* print_var_export($params); die; */
            $data          = $params['params'];
            $task_id       = $params['task_id'];
            $task_status   = $params['task_status'];
            $proj_id       = $params['proj_id'];
            $file_list     = $params['file_list'];
            $task_details  = $params['task_details'];
            $completion    = $params['completion_files'];

            //Start the db transaction
            Portal_Model::beginTransaction();

            foreach($file_list as $file)
            {
                //Delete the record in documents tables. If it has been removed in the selectbox
                if( ! EMPTY($file['sys_file_name']) && in_array($file['document_type_code'], $completion) == FALSE )
                {
                    $this->dm_model->delete_document(['document_id' => $file['document_id']]);

                    unlink(FCPATH.PATH_UPLOADED_FILES.$file['sys_file_name']);
                }
            }

            //Update the reference of the task and status ( w/other details )
           // $ongoing      = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $proj_id]);
            $this->tag_task($task_id, $task_status, ['reference' => $proj_id]);

            Portal_Model::commit();

            $doc_ref      = ( ! EMPTY($data)) ? encrypt_id($proj_id) :  '';
            //$doc_ref      = encrypt_id($proj_id);
            $flag         = SUCCESS;
            $msg          = $this->lang->line('data_saved');
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
            'flag'   => $flag,
            'msg'    => $msg,
            'task'   => $task,
            'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }


    private function _validate()
    {
        $params         = get_params();
        //print_var_export($params); die;
        $required       = $constraints  = [];

        $task_id        = decrypt_id($params['etd']);
        $task_details   = $this->tm_model->get_task_details($task_id);
        $proj_id        = $task_details['reference_id'];

        $file_list      = $this->tm_model->get_pria_task_documents($task_id, $proj_id);

        $project_types  = $this->pj_model->get_project_types(['project_id' => $proj_id], ['project_type_code']);
        $project_types  = array_column($project_types, 'project_type_code');

        //Get the required documents based on the project type
        $req_doc_types  = $this->pj_model->get_param_contractor_category_files(['category_code' =>  ['IN', $project_types]], ['DISTINCT document_type_code']);
        $req_doc_types  = array_column($req_doc_types, 'document_type_code');
        $no_req_doc     = count($req_doc_types);
        $req_doc_names  = array_column(
            array_filter($file_list, function($val) use($req_doc_types){
                return in_array($val['document_type_code'], $req_doc_types);
        }), 'document_type_name');


        if(EMPTY($params['completion_files']))
            throw new Exception('You are required to upload atleast one file.');

        //$difference   = array_intersect($req_doc_types, $params['completion_files']);
        $difference = array_diff($req_doc_types, $params['completion_files']);

        //currently uploaded
        $curr_proj_attachments = array_column($this->dm_model->get_documents(['reference' => $proj_id, 'account_group_code' => AG_CONTRACTORS], ['document_type_code']), 'document_type_code');

        $has_req_in_curr_attachments = array_intersect($req_doc_types, $curr_proj_attachments);
        $has_req_in_curr_upload = array_intersect($req_doc_types, $params['completion_files']);

        // If he has no attached required documents ( during first upload ) and no attached in current upload action, required to upload atleast one.
        if(count($has_req_in_curr_attachments) < 1 && count($has_req_in_curr_upload) <  1){
            throw new Exception('Please upload atleast one of the following documents: '.implode(',', $req_doc_names));
        }
    
        
       // print_var_export($file_list, $has_req_in_curr_attachments, $curr_proj_attachments, $req_doc_types); die;
/* 
        if(!EMPTY($difference))
        {
            $req_doc_names =  array_column(
                array_filter($file_list,
                    function($val) use($difference){
                            return in_array($val['document_type_code'], $difference) ? TRUE : FALSE;
                    }),
            'document_type_name');

            throw new Exception('The following documents are required for this project : '.implode(',', $req_doc_names));
        }
 */

        
        foreach($params['completion_files'] as $file)
        {
            $index      = strtolower($file);
            $key        = array_search($file, array_column($file_list, 'document_type_code'));
            $doc_name   = $file_list[$key]['document_type_name'];

            //If this file is already upload, don't validate
            if( ! EMPTY($file_list[$key]['sys_file_name'])) continue;

            $required[$index]       = $doc_name;
            $constraints[$index]    = [
                'data_type'			=> 'string',
                'name'				=> $doc_name
            ];
        }

        $const['task_id'] = [
            'data_type'   => 'db_value',
            'name'        => 'ETD',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'pria_task_id',
            'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
        ];

        $const['task_status'] = [
            'data_type'   => 'db_value',
            'name'        => 'Task Status',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'action_id',
            'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
        ];

        /* Separated the check of task id. Why? used the $data below in the function process. (see for yourself) */
        $this->validate_inputs(['task_id' => $task_id, 'task_status' => $params['task_status']], $const);

        /* Validate the required fields */
        $this->check_required_fields($params, $required);

        /* Validate constraints */
        $data =  $this->validate_inputs($params, $constraints);

        return [
            'params'           => $data,
            'task_id'          => $task_id,
            'proj_id'          => $proj_id,
            'file_list'        => $file_list,
            'task_details'     => $task_details,
            'completion_files' => $params['completion_files'],
            'task_status'      => $params['task_status']
        ];
    }

}