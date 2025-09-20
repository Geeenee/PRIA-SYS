<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Indicate_budget extends Task_Controller 
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

            $this->task_resources['load_js'][]  = JS_NUMBER;
            $this->task_resources['load_css'][] = CSS_LABELAUTY;
            $this->task_resources['load_js'][]  = JS_LABELAUTY;

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details']        = $boq_details;

            $this->task_details['task_reference_id']    = $boq_id;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
     /*        $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */

            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_page                  = '/indicate_budget';
            
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
            if( ! check_permission($this->tab_module_code, ACTION_ADD)) throw new Exception($this->lang->line('invalid_action'));

            $params             = get_params();
            $flag               = ERROR;
            $status             = '';
            $msg                = '';
            $task               = '';
            
            $task_id            = decrypt_id($params['etd']);

            $task_details = $this->tm_model->get_task_details($task_id);

            $data         = $this->_validate($params, $task_details);	

            //Start the db transaction                
            Portal_Model::beginTransaction();

            $boq_id       = $task_details['reference_id'];

            $table        = Portal_Model::PORTAL_TABLE_PRIA_BOQ;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $where        = ['boq_id' => $boq_id];    

            $audit_action = [AUDIT_UPDATE];	
            $prev_detail  = [$this->bq_model->get_details_for_audit( $table, $where)];
            $actvy_index  = EMPTY($task_details['task_reference_id']) ? 'audit_trail_add' : 'audit_trail_update';
            $activity     = sprintf($this->lang->line($actvy_index), $task_details['task_name']);

            $fields        = [
                'budget_amount_civil_works' => $params['budget_amount_civil_works'],
                'budget_amount_signage'     => $params['budget_amount_signage'],
                'budgeted_flag'             => ISSET($params['budgeted']) ? $params['budgeted'] : INITIAL_NO,
                'modified_by'               => $this->session->user_id,
                'modified_date'             => date(FORMAT_DB_DATE)
            ];

            $this->bq_model->update_boq($fields, $where);

            $curr_detail  = [ $this->bq_model->get_details_for_audit($table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
           /*  if(EMPTY($task_details['task_reference_id']))
                $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $boq_id]);

 */
            $this->tag_task($task_id, $params['task_status'], ['reference' => $boq_id]);

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
            'status' => $status
        ]);
    }


    private function _validate($params, $task_details)
	{
        $params = $this->set_filter( $params )
        ->filter_string('budget_amount_civil_works')
        ->filter_string('budget_amount_signage')
        ->filter();

        //Define the required fields.
        $required = [
            'budget_amount_civil_works'   => 'Budget Amount for Civil Works',
            'budget_amount_signage'       => 'Budget Amount for Signage',
        ];

        $constraints['budget_amount_civil_works'] = [
            'data_type'         => 'string',
            'name'              => 'Budget Amount for Civil Works'
        ];

        $constraints['budget_amount_signage'] = [
            'data_type'         => 'string',
            'name'              => 'Budget Amount for Signage'
        ];

        if(ISSET($params['budgeted']) )
        {
            $constraints['budgeted']    = array(
                'data_type'         => 'enum',
                'name'              => 'Budgeted',
                'allowed_values'    => $this->enum_yes_num
            );
        }

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
         return $this->validate_inputs($params, $constraints);

		return $data;
	}

}