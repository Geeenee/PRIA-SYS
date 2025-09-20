<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recommend_project_completion extends Task_Controller
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

            $proj_id        = $this->task_details['reference_id'];

            $proj_details   = $this->pj_model->get_project(['project_id' => $proj_id], ['boq_id', 'project_code', 'additional_flag']);

            $boq_details    = $this->bq_model->get_boq_details($proj_details['boq_id']);
           // print_var_export($this->task_details); die;

            $core_task_id = $this->task_details['core_workflow_task_id'];
            $additional_flag = $this->task_details['additional_flag'];

            if(in_array($core_task_id, [CORE_TASK_PROJ_COMPLETION_APPROVED, CORE_TASK_PROJ_COMPLETION_APPROVED_APPEND])){
                  $predecessor = $this->tm_model->get_task_predecessors([$task_id]);
                  $pre_task_id = $predecessor[$task_id][0]['pre_pria_task_id'];
                  $pre_task_details = $this->tm_model->get_task_details($pre_task_id);
                  $additional_flag = $pre_task_details['additional_flag'];
            }
            

            $this->task_view_data['boq_details']    = $boq_details;
            $this->task_view_data['proj_details']   = $proj_details;
            
            $this->task_view_data['additional_flag']   = $additional_flag;
            $this->task_view_data['core_task_id']   = $core_task_id;
           
            $this->task_view_data['file_list']      = $this->tm_model->get_pria_task_documents($task_id, $proj_id);

            $this->task_details['task_reference_id'] = $proj_id;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.
            $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];

            //$this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);
            //print_var_export($this->task_upload, $this->task_resources); die;
            //Load the content of the task
            $this->data['page_title']         = 'Project: '.$proj_details['project_code'];


            $this->task_page                  = '/recommend_project_completion';

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


}