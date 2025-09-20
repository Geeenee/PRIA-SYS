<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_sales extends Task_Controller 
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
        $this->load->model('Documents_model', 'document_model'); 
        
        $this->permissions      = check_permission($this->tab_module_code);

        $this->path_task_views .= $this->folder;
    }
    
    public function index()
    {
        try
        {
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params    = get_params(TRUE, TRUE);

            $task_id   = base64_url_decode($params['t'] );

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;

            //Get IO details
            $fields     = ['*'];
            $where      = ['io_id' => $this->task_details['reference_id']];                      
            $io         = $this->io_model->get_internal_order($where, $fields);


            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $io_id              = $this->task_details['task_reference_id'];

            
            if( ! EMPTY($io['live_sales_submit_date']))
            {   
                $fields  = ['live_sales_submit_date'];

                $this->task_view_data['live_sales_details'] = $this->io_model->get_internal_order(['io_id' => $io_id], $fields);
            }

            //Load the content of the task
            $this->data['page_title']         = 'Internal Order: '.$io['io_num'];      
            $this->task_page                = '/live_sales';

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
            $status       = '';
            $response     = [];
            $doc_ref      = '';
            $data         = $this->_validate();
            
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

            $task_details = $this->tm_model->get_task_details($task_id);

            $where          = ['document_type_code' => DOC_TYPE_LIVESALES, 'reference' => $task_details['reference_id']];
            $live_sales_form   = $this->document_model->get_document($where);

            //Get IO details
            $fields     = ['*'];
            $where      = ['io_id' => $task_details['reference_id']];
            $io         = $this->io_model->get_internal_order($where, $fields);

            //Set initial fields that is present for both insert and update action
            $fields       = [
                'live_sales_submit_date'   => std_db_date_format($data['date_submitted'])
            ];

            //Start the db transaction                
            Portal_Model::beginTransaction();   
            //If reference id is empty action will be insert

            if( EMPTY($io['live_sales_submit_date']))
            {

                //validate harvest file
                if(EMPTY($live_sales_form)){
                    if(EMPTY($data['doc_livesales'])){
                        throw new Exception('Live Sales File is required.');
                    }
                }

                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT];
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Live sales report');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);
                $io_details   = $this->io_model->get_internal_order(['io_id' => $io_id], ['vendor_code']);

                //Update internal orders
                $where = array('io_id' => $io_id);

                $this->io_model->update_internal_order($where, $fields); 

                $where             = ['io_id'   => $io_id];
                $curr_detail       = [ $this->io_model->get_details_for_audit($table, $where) ];
                
                // $ongoing           = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                //     'reference'         => $io_id,
                //     'start_date'        => date(FORMAT_DB_DATETIME),
                //     'actual_start_date' => date(FORMAT_DB_DATETIME)
                // ]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $io_id,
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => date(FORMAT_DB_DATETIME)
                ]);

                $doc_ref           = encrypt_id($io_id);

                // $response['actor'] = $ongoing['actor_name'];
                // $status            = TASK_STATUS_ONGOING;
            }
            else
            {   
                $audit_action = [AUDIT_UPDATE];
                
                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);
                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Harvest report');

                //validate harvest file
                if(EMPTY($live_sales_form)){
                    if(EMPTY($data['doc_livesales'])){
                        throw new Exception('Live Sales File is required.');
                    }
                }

                // $ongoing      = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $io_id]);
                
                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $io_id
                ]);

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);                

                //Update internal orders
                $where  = array('io_id' => $io_id);
                $update = array(
                    'live_sales_submit_date'   => std_db_date_format($data['date_submitted'])
                );

                $this->io_model->update_internal_order($where, $update);

                $where              = ['io_id' => $io_id];
                $curr_detail        = [ $this->io_model->get_details_for_audit( $table, $where) ];

                $doc_ref            = encrypt_id($io_id);

                // $response['actor']  = $ongoing['actor_name'];   
                // $status             = TASK_STATUS_ONGOING;
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
            'flag'      => $flag,
            'msg'       => $msg,
            // 'task'      => $response,
            // 'status'    => $status,
            'doc_ref'   => $doc_ref
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
            ->filter_date('date_submitted')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'date_submitted' => 'Date Submitted'
                // 'doc_livesales'  => 'Live Sales File'
            ];

            $constraints['date_submitted'] = [
                'data_type'         => 'date',
                'name'              => 'Date Submitted'
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
            
            $constraints['doc_livesales'] = [
                'data_type'         => 'string',
                'name'              => 'Live Sales File'
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            
            $date_submitted   = strtotime($data['date_submitted']);
            $curr_date        = strtotime(date(FORMAT_DB_DATE));

            if($date_submitted > $curr_date)
                throw new Exception('Live sales report date must not be greater than current date.');

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