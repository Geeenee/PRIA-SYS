<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recommend_approval_eng extends Task_Controller 
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

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details']        = $boq_details;

            $this->task_details['task_reference_id']    = $boq_id;
            
            $this->task_view_data['core_task_id']       = $this->task_details['core_workflow_task_id'];

            $this->task_view_data['w_edit_recom_bh']    =  $this->task_details['core_workflow_task_id'] == CORE_TASK_BOQ_MCS_ENGINEERING ? true : false;
            $this->task_view_data['w_edit_recom_rh']    =  $this->task_details['core_workflow_task_id'] == CORE_TASK_BOQ_MCS_APPROVED ? true : false;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
            /*   $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */

            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_page                  = '/recommend_approval_eng';
            
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

            $task_id      = decrypt_id($params['etd']);

            $task_details = $this->tm_model->get_task_details($task_id);

            $params       = $this->_validate($params, $task_details);

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
            $activity     = sprintf($this->lang->line($actvy_index), ' Upload BOQ Recommendation');

            $fields        = [
                'final_amount_civil_works'  => $params['final_amount_civil_works'],
                'final_amount_signage'      => (ISSET($params['final_amount_signage']) AND !EMPTY($params['final_amount_signage']))? $params['final_amount_signage']: "0.00",
                'boq_justification'         => (ISSET($params['justification']) AND !EMPTY($params['justification']))? $params['justification']: NULL,
                'modified_by'               => $this->session->user_id,
                'modified_date'             => date(FORMAT_DB_DATE)
            ];   

            $this->bq_model->update_boq($fields, $where);

            $curr_detail  = [ $this->bq_model->get_details_for_audit( $table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
          /*   if(EMPTY($task_details['task_reference_id']))
            {
                $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $boq_id]);

                $doc_ref      = encrypt_id($boq_id);
            }     */

            $this->tag_task($task_id, $params['task_status'], ['reference' => $boq_id]);

            $doc_ref      = encrypt_id($boq_id);

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
    
    private function _validate($params, $task_details)
    {
        //Filters the data inputted by the user 
        $params = $this->set_filter( $params )
        ->filter_float('final_amount_civil_works')
        ->filter_float('final_amount_signage')
        ->filter_string('justification')
        ->filter();

        //Define the required fields.
        $required = [
            'final_amount_civil_works'   => 'Final Amount for Civil Works'
        ];

        if(EMPTY($params['additional_flag']))
        {
            $required['final_amount_signage']    = 'Final Amount for Signage';
        }

        if(ISSET($params['task_status']) AND $params['task_status'] == TASK_STATUS_DONE)
        {
            $required['justification']      = 'Justification for Approval';
            // $required['recommendation_rh']  = 'Remarks on Recommendation';
        }

        $constraints['final_amount_civil_works'] = [
            'data_type'         => 'amount',
            'name'              => 'Final Amount for Civil Works'
        ];

        $constraints['final_amount_signage'] = [
            'data_type'         => 'amount',
            'name'              => 'Final Amount for Signage'
        ];

        $constraints['justification'] = [
            'data_type'         => 'string',
            'name'              => 'Justification for Approval'
        ];

        /*$constraints['recommendation_rh'] = [
            'data_type'         => 'string',
            'name'              => 'Remarks on Recommendation'
        ];*/

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
    }
}