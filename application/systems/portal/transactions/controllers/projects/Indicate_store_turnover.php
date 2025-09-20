<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Indicate_store_turnover extends Task_Controller 
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

            $proj_details   = $this->pj_model->get_project(['project_id' => $proj_id], ['boq_id', 'turnover_date', 'project_code']);

            $boq_details    = $this->bq_model->get_boq_details($proj_details['boq_id']);

            $this->task_view_data['boq_details']    = $boq_details;
            $this->task_view_data['proj_details']   = $proj_details;


            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
    //         $sub_nav_left_config	 = [
				// 'title'  		=> 'IO Number',
				// 'placeholder' 	=> 'IO #',
				// 'data' 		 	=> []
    //         ];
            
            // $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config); 

            //Load the content of the task
            $this->data['page_title']         = 'Project: '.$proj_details['project_code'];    
            $this->task_page                  = '/indicate_store_turnover';
            
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
            $flag         = ERROR;
            $status       = '';
            $msg          = '';
            $task         = '';
            $doc_ref      = '';    

            $params       = $this->_validate();
            
            $task_id      = $params['task_id'];

            $task_details = $this->tm_model->get_task_details($task_id);

            //Start the db transaction                
            Portal_Model::beginTransaction();

            $proj_id      = $task_details['reference_id'];

            $table        = Portal_Model::PORTAL_TABLE_PROJECTS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $where        = ['project_id' => $proj_id];    

            $audit_action = [AUDIT_UPDATE];	
            $prev_detail  = [$this->pj_model->get_details_for_audit($table, $where)];
            $actvy_index  = EMPTY($task_details['task_reference_id']) ? 'audit_trail_add' : 'audit_trail_update';
            $activity     = sprintf($this->lang->line($actvy_index), ' Indicate Store Turnover Date');

           $fields        = [
                'turnover_date'     => std_db_date_format($params['turnover_date']),
                'modified_by'       => $this->session->user_id,
                'modified_date'     => date(FORMAT_DB_DATE)
            ];     
            
            $this->pj_model->update_project($fields, $where);

            $curr_detail  = [ $this->pj_model->get_details_for_audit($table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
        /*     if(EMPTY($task_details['task_reference_id']))
                $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $proj_id]);
 */

            $this->tag_task($task_id, $params['task_status'], ['reference' => $proj_id]);


            Portal_Model::commit();
            
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
        $params = get_params();

        //Filters the data inputted by the user 
        $params = $this->set_filter( $params )
        ->filter_date('turnover_date')
        ->filter();

        $params['task_id'] = decrypt_id($params['etd']);

        //Define the required fields.
        $required = [
            'turnover_date'  => 'Turnover Date',
        ];

        $constraints['task_status'] = [
            'data_type'   => 'db_value',
            'name'        => 'Task Status',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'action_id',
            'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
        ];

        $constraints['turnover_date'] = [
            'data_type'			=> 'date',
            'name'				=> 'Turnover Date'
        ];

        $constraints['task_id'] = [
            'data_type'   => 'db_value',
            'name'        => 'ETD',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'pria_task_id',
            'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
        ];

         /* Validate the required fields */
         $this->check_required_fields($params, $required);

         /* Validate constraints */
         $data =  $this->validate_inputs($params, $constraints);
         
         $turnover_date = strtotime($data['turnover_date']);
         $curr_date     = strtotime(date(FORMAT_DB_DATE));

         if($turnover_date < $curr_date)
             throw new Exception('Turnover date must not be less than current date.');

         return $data;
    }
}