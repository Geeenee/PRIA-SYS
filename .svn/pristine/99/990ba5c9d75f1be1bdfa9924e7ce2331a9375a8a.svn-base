<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_approve_boq extends Task_Controller 
{ 
        public function __construct()
        {
                parent::__construct();
                
                $this->controller 		= strtolower(__CLASS__);
                $this->module_code      = MODULE_PORTAL_TRANS_CONTRACTORS;
                $this->tab_module_code  = MODULE_PORTAL_TRANS_CONTRACTORS_PROJECTS;
                $this->folder           = FOLDER_PROJECTS;

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

                        $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
                        $this->task_resources['load_css'][] = JS_DATETIMEPICKER;

                        $proj_id         = $this->task_details['reference_id'];

                        $proj_details   = $this->pj_model->get_project(['project_id' => $proj_id], ['boq_id', 'project_code']);

                        $boq_details    = $this->bq_model->get_boq_details($proj_details['boq_id']);

                        $proj_vendor     = $this->pj_model->get_project_vendors_by_project_id($proj_id);
                        
                        $vendors         = implode(', ', array_unique(array_column($proj_vendor, 'vendor_name')));

                        $boq_details['awarded_contractor_name'] = $vendors;


                        $this->task_view_data['boq_details']     = $boq_details;
                        $this->task_view_data['proj_details']    = $proj_details;

                        $this->task_details['task_reference_id'] = $proj_id;

                        //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
                //         $sub_nav_left_config	 = [
                                        // 'title'  		=> 'IO Number',
                                        // 'placeholder' 	=> 'IO #',
                                        // 'data' 		 	=> []
                //         ];
                        
                        // $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config); 

                        //Load the content of the task
                        $this->data['page_title']         = 'Project: '.$proj_details['project_code'];    
                        $this->task_page                  = '/return_approve_boq';
                        
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