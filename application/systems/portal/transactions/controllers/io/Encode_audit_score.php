<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encode_audit_score extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACT_GROWERS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CG_IO;
        $this->folder           = FOLDER_INTERNAL_ORDER;
        
        $this->load->model($this->folder.'/internal_order_model', 'io_model'); 
        $this->load->model(FOLDER_DELIVERY_GOODS.'/delivery_goods_model', 'dgr_model');
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        
        $this->permissions      = check_permission($this->tab_module_code);

        $this->path_task_views .= $this->folder;
    }
    
    public function index()
    {
        try
        {
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params    = get_params(TRUE, TRUE);

            $task_id   = base64_url_decode($params['t']);

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;

            //Get IO details
            $fields     = ['*'];
            $where      = ['io_id' => $this->task_details['reference_id']];                      
            $io         = $this->io_model->get_internal_order($where, $fields);

       

            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $io_id  = $this->task_details['reference_id'];

            $this->task_details['task_reference_id'] = $io_id;

            if( ! EMPTY($io['fhr_document_num']))
            {
                $fields  = ['fhr_document_num', 'fhr_submit_date', 'pre_placement', 'brooding_audit', 'biosecurity_audit', 'io_id'];
                
                $this->task_view_data['fhr_details'] = $this->io_model->get_internal_order(['io_id' => $io_id], $fields);
            }      

            //Load the content of the task
            $this->data['page_title']   = 'Internal Order: '.$io['io_num'];

            $this->task_page            = '/encode_audit_score';

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
            $flag         = ERROR;
            $data         = $this->_validate();

            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

            $task_details = $this->tm_model->get_task_details($task_id);

            //Get IO details
            $fields     = ['*'];
            $where      = ['io_id' => $task_details['reference_id']];
            $io         = $this->io_model->get_internal_order($where, $fields);

            //Set initial fields that is present for both insert and update action
            $fields       = [
                'pre_placement'         => ISSET($data['pre_placement']) ? $data['pre_placement'] : NULL,
                'brooding_audit'        => ISSET($data['brooding_audit']) ? $data['brooding_audit'] : NULL,
                'biosecurity_audit'     => ISSET($data['biosecurity_audit']) ? $data['biosecurity_audit'] : NULL
            ];
            
            //Start the db transaction                
            Portal_Model::beginTransaction();
            //If reference id is empty action will be insert

            //Set audit trail config for insert
            IF(!EMPTY($data['pre_placement']) AND !EMPTY($data['brooding_audit']) AND !EMPTY($data['biosecurity_audit'])){
                $audit_action = [AUDIT_INSERT];
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Audit Score');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);

                //Update internal orders
                $where = array('io_id' => $io_id);
                $this->io_model->update_internal_order($where, $fields);  
                
                $where          = ['io_id' => $io_id];
                $curr_detail    = [ $this->io_model->get_details_for_audit($table, $where) ];
                
                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING,[
                //     'start_date'        => date(FORMAT_DB_DATETIME),
                //     'actual_start_date' => date(FORMAT_DB_DATETIME)
                // ]);

                $this->tag_task($task_id, $task_status_id, [
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => date(FORMAT_DB_DATETIME)
                ]);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;
            }else{
                
                $audit_action = [AUDIT_UPDATE];
                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Audit Score');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);

                //Update internal orders
                $where = array('io_id' => $io_id);
                $this->io_model->update_internal_order($where, $fields);                
                $curr_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING);
                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;

                $this->tag_task($task_id, $task_status_id, [
                ]);
            }
            

            $this->audit_trail->log_audit_trail($activity, $this->tab_module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg  = $this->lang->line('data_saved');
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
            'flag'  => $flag,
            'msg'   => $msg
            // 'task'   => ISSET($response) ? $response : NULL,
            // 'status' => ISSET($status) ? $status : NULL 
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            if( ! $this->permissions[ACTION_EDIT]) throw new Exception($this->lang->line('err_unauthorized_access'));
            
            $params = get_params();

            //Filters the data inputted by the user 
            $params = $this->set_filter( $params )
            ->filter_float('pre_placement')
            ->filter_float('brooding_audit')
            ->filter_float('biosecurity_audit')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            $acceptable = FALSE;
            
            IF(!EMPTY($params['pre_placement'])){

                $acceptable = TRUE;

                $constraints['pre_placement'] = [
                    'data_type'         => 'amount',
                    'name'              => 'Pre placement'
                ];
            }

            IF(!EMPTY($params['brooding_audit'])){

                $acceptable = TRUE;

                $constraints['brooding_audit'] = [
                    'data_type'         => 'amount',
                    'name'              => 'Brooding audit'
                ];
            }

            IF(!EMPTY($params['biosecurity_audit'])){

                $acceptable = TRUE;

                $constraints['biosecurity_audit'] = [
                    'data_type'         => 'amount',
                    'name'              => 'Biosecurity Audit'
                ];
            }

            IF($acceptable == FALSE)
                throw new Exception($this->lang->line('err_audit_score_required'));

            //Define the required fields.
            $required = []; 

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
            $data       = $this->validate_inputs($params, $constraints);

            return $data;
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