<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_approve extends Task_Controller
{
	public function __construct()
	{
		parent::__construct();

        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACTORS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CONTRACTORS_BOQ;
        $this->folder           = FOLDER_BOQ;

        $this->load->model($this->folder.'/boq_model', 'bq_model');

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

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details']        = $boq_details;

            $this->task_details['task_reference_id']    = $boq_id;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.
            /*$sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];

            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);*/

            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];
            $this->task_page                  = '/return_approve';

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