<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_store_layout_plan extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
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

            $this->task_resources['load_js'][] = JS_NUMBER;

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details'] = $boq_details;

            $this->task_details['task_reference_id']    = $boq_id;

            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_page                  = '/upload_store_layout_plan';

            $this->_load_task_view();
        }
        catch( PDOException $e )
        {
            $msg    = $this->get_user_message($e);

            $this->error_page( $msg );
        }
        catch( Exception $e )
        {
            $msg    = $this->rlog_error($e, TRUE);  
            
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
            $additional_flag  = $prev_detail[0][0]['additional_flag'];
            $actvy_index  = EMPTY($task_details['task_reference_id']) ? 'audit_trail_add' : 'audit_trail_update';
            $activity     = sprintf($this->lang->line($actvy_index), ' Upload Store Lay-Out Plan');
            
            $fields       = [
                    'layout_specifications' => (ISSET($params['layout_specifications']) AND !EMPTY($params['layout_specifications']))? $params['layout_specifications']: NULL,
                    'modified_by'           => $this->session->user_id,
                    'modified_date'         => date(FORMAT_DB_DATE)
            ];

            $this->bq_model->update_boq($fields, $where);

            $curr_detail  = [ $this->bq_model->get_details_for_audit( $table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            $this->tag_task($task_id, $params['task_status'], ['reference' => $boq_id]);

            if($additional_flag){
                echo 'additional_flag';
                print_var_export($task_details);
				$this->pria_workflow->_skip_stage_tasks($task_details);
			}


            $doc_ref      = encrypt_id($boq_id);

            Portal_Model::commit();
            
            $flag         = SUCCESS;
            $msg          = $this->lang->line('data_saved');
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
            'flag'   => $flag,
            'msg'    => $msg,
            'task'   => $task,
            'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }
    
    private function _validate($params, $task_details)
    {
        $params = $this->set_filter( $params )
        ->filter_string('doc_layout_plan')
        ->filter_string('doc_supp_pics')
        ->filter_string('layout_specifications')
        ->filter();

        //Define the required fields.
        $required = [];

        $task_id       = decrypt_id($params['etd']);
        $task_document = $this->dm_model->get_document(['pria_task_id' => $task_id, 'document_type_code' => DOC_TYPE_LAYOUT_PLAN]);
         
        if(EMPTY($task_document))
        {
            $required['doc_layout_plan']    = 'Store Lay-Out Plan';

            $constraints['doc_layout_plan'] = [
                'data_type'                 => 'string',
                'name'                      => 'Store Lay-Out Plan'
            ];
        }

        $task_document = $this->dm_model->get_document(['pria_task_id' => $task_id, 'document_type_code' => DOC_TYPE_SUPP_PICS]);
         
        if(EMPTY($task_document))
        {
            if($params['task_status'] == TASK_STATUS_DONE)
            {
                $required['doc_supp_pics']    = 'Supporting Pictures';
            }

            $constraints['doc_supp_pics'] = [
                'data_type'               => 'string',
                'name'                    => 'Supporting Pictures'
            ];
        }

        if($params['task_status'] == TASK_STATUS_DONE)
        {
            $required['layout_specifications'] = 'Specifications/Remarks';
        }

        $constraints['layout_specifications'] = [
            'data_type'               => 'string',
            'name'                    => 'Specifications/Remarks'
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
         return $this->validate_inputs($params, $constraints);
    }
}