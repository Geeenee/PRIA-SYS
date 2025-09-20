<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Release_po extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_PURCHASE_ORDERS;
        
        $this->load->model($this->folder.'/Po_model', 'po_model'); 
        $this->load->model('Documents_model', 'document_model'); 
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');

        // $this->permissions      = check_permission($this->module_code);
        $this->path_task_views .= $this->folder;
    }
    
    public function index()
    {
        try
        {
            $params = get_params(TRUE, TRUE);

            $task_id = base64_url_decode($params['t'] );
            
            //get task details
            $task = $this->tm_model->get_task_details($task_id);
            
            //get tab module code
            //$tab_module_code    = base64_url_decode($params['mid']);

            $task_workflow = $this->tm_model->get_task_workflow($task_id);

            //get account group
            $ag_code = $task['account_group_code'];
            
            //get core workflow id
            $workflow_id = $task_workflow['workflow_id'];

            //get tab module details
            $tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC]);

            $tab_module_code = $tab_module_details['tab_module_code'];

            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];

            $this->tab_module_code  = $tab_module_code;

            $this->permissions  = check_permission($tab_module_code);
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_UPLOAD;

            //Get PO details
            $fields         = ['*'];
            $where          = ['po_id' => $this->task_details['reference_id']];
            $po_details    = $this->po_model->get_po($where, $fields);

            $this->task_details['task_reference_id'] = $po_details['po_id'];

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;
 
            $resources['load_css'][]    = CSS_DATETIMEPICKER;
            $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];
            
            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];
            $document_type_det  = $this->po_model->get_specific_task_document_type($where, $field_select, array(), FALSE);

            $this->task_view_data['po_details'] = $po_details;

            //get vendor info
            $ven_info = $this->po_model->get_all_vendors([ 'vendor_code' => $po_details['vendor_code']], array('*'), array(), FALSE );
            $this->task_view_data['vendor_info'] = $ven_info;

            //Load the content of the task
            $this->data['page_title']       = 'PO number: '.$po_details['po_num'];
            $this->task_page                = 'release_po';
        
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
            
            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            $doc_ref        = '';
            $data         = $this->_validate();
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];
            $task_details = $this->tm_model->get_task_details($task_id);

            //Get module code
            $module_code = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

            //Start the db transaction                
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            $po_id               = $this->tm_model->get_task_workflow_reference_id($task_id);

            // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
            //     'reference'         => $po_id,
            //     'start_date'        => date(FORMAT_DB_DATETIME),
            //     'actual_start_date' => date(FORMAT_DB_DATETIME)
            // ]);

            $this->tag_task($task_id, $task_status_id, [
                'reference'         => $po_id,
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            ]);

            $fields = array('po_released_date' => $data['po_released_date']);
            $where  = array('po_id' => $po_id);
            $this->po_model->update_po($where, $fields);

            // $response['actor']   = $ongoing['actor_name'];
            $status              = TASK_STATUS_ONGOING;
            // $msg                 = $this->lang->line('data_saved');

            // $doc_ref             = encrypt_id($po_id);  
            

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
            /*'task'   => $response,
            'status' => $status, 
            'doc_ref'=> $doc_ref */
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            $params = get_params();
            
            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_string('po_released_date')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'po_released_date'        => 'Released Date'
            ];

            $constraints['po_released_date']    = [
                'data_type'         => 'date',
                'name'              => 'DOC PO'
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

            $end_date   = strtotime($params['po_released_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('PO released date must not be greater than current date.');

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