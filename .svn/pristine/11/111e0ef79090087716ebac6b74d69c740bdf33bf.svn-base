<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_boq_progress extends Task_Controller 
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

            $proj_details    = $this->pj_model->get_project(['project_id' => $proj_id], ['boq_id', 'project_code']);

            $boq_details     = $this->bq_model->get_boq_details($proj_details['boq_id']);

            $proj_vendor     = $this->pj_model->get_project_vendors_by_project_id($proj_id);
            
            $vendors         = implode(', ', array_unique(array_column($proj_vendor, 'vendor_name')));

            $boq_details['awarded_contractor_name'] = $vendors;

            $this->task_view_data['boq_details']    = $boq_details;
            $this->task_view_data['proj_details']   = $proj_details;


            
            $where          = ['document_type_code' => DOC_TYPE_BOQ_PROGRESS, 'reference' => $proj_id];
            $boq_progress   = $this->dm_model->get_document($where);

            $this->task_details['task_reference_id'] = $proj_id;

            //If There's an uploaded file hide save because edit will be in versioning
        /*     if( ! EMPTY($boq_progress))
                $this->task_access['hide_btn'] = TRUE;
 */
            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
       /*      $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config); 
 */
            //Load the content of the task
            $this->data['page_title']         = 'Project: '.$proj_details['project_code'];    
            $this->task_page                  = '/upload_boq_progress';
            
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
            $params       = get_params();
            $flag         = ERROR;
            $status       = '';
            $msg          = '';
            $task         = '';
            $doc_ref      = '';    
            $required     = [];
            $task_id      = decrypt_id($params['etd']);

         /*    $required = ['doc_boq_progress'  => 'BOQ File'];

            $constraints['doc_boq_progress'] = [
                'data_type'			=> 'string',
                'name'				=> 'BOQ File'
            ];
 */
            $task_details = $this->tm_model->get_task_details($task_id);

            $constraints['task_status'] = [
                'data_type'   => 'db_value',
                'name'        => 'Task Status',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'action_id',
                'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
            ];

            $where       = ['document_type_code' => DOC_TYPE_BOQ_PROGRESS, 'reference' => $task_details['reference_id']];
            $soa_form   = $this->dm_model->get_document($where);
    

            if(EMPTY($soa_form))
            {
                $required['doc_boq_progress']      = 'BOQ File';
    
                $constraints['doc_boq_progress']   = [
                    'data_type'			=> 'string',
                    'name'				=> 'BOQ File'
                ];
            }

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $this->validate_inputs($params, $constraints);

         

            $proj_id      = $task_details['reference_id'];

            //Update the reference of the task and status ( w/other details )
           // $ongoing      = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $proj_id]);

            $this->tag_task($task_id, $params['task_status'], ['reference' => $proj_id]);


            $doc_ref      = encrypt_id($proj_id);
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
    
    private function _validate($params, $task_details)
    {
      //  $insert = EMPTY($task_details['task_reference_id']) ? true : false;

        $where       = ['document_type_code' => DOC_TYPE_BOQ_PROGRESS, 'reference' => $task_details['reference_id']];
        $soa_form   = $this->document_model->get_document($where);


        //Filters the data inputted by the user 
        $params = $this->set_filter( $params )
        ->filter_date('mobilization_date')
        ->filter_string('doc_gantt')
        ->filter();

        //$params['task_id'] = decrypt_id($params['etd']);

        //Define the required fields.
        $required = [
            'mobilization_date'  => 'Mobilization Date',
        ];

        if(EMPTY($soa_form))
        {
            $required['doc_gantt']      = 'Gantt Chart';

            $constraints['doc_gantt']   = [
                'data_type'			=> 'string',
                'name'				=> 'Gantt Chart'
            ];
        }

        $constraints['mobilization_date'] = [
            'data_type'			=> 'date',
            'name'				=> 'Mobilization Date'
        ];

        $constraints['task_id'] = [
            'data_type'   => 'db_value',
            'name'        => 'ETD',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'pria_task_id',
            'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
        ];


        $constraints['task_status'] = [
            'data_type'   => 'db_value',
            'name'        => 'Task Status',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'action_id',
            'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
        ];

         /* Validate the required fields */
         $this->check_required_fields($params, $required);

         /* Validate constraints */
         $data =  $this->validate_inputs($params, $constraints);
         
         $mobilization_date = strtotime($data['mobilization_date']);
         $curr_date         = strtotime(date(FORMAT_DB_DATE));

         if($mobilization_date > $curr_date)
             throw new Exception('Mobilization date must not be greater than current date.');

         return $data;
    }
}