<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_transmittal_document extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_TRANSMITTAL;

        $this->load->model($this->folder.'/document_transmittal_model', 'dt_model'); 
        $this->load->model('Documents_model', 'document_model'); 
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');

        $this->path_task_views .= $this->folder;
    }
    
    /* Notes By: Gene | On : 2025-11-07
    |------------------------------------------------------------------------------------------
    | The index function loads gathers the resources needed for the task page and loads the view
    | 
    | 
    |------------------------------------------------------------------------------------------
    |
    |------------------------------------------------------------------------------------------
    */
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
            $tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC, 'transaction_tab' => TRANS_TAB_TRANSMITTAL] );
            
            // print_var_export($tab_module_details); die('end');
            //get tab module details
            //$tab_module_details = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module_code]);
            
            $tab_module_code        = $tab_module_details['tab_module_code'];

            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];

            $this->tab_module_code  = $tab_module_code;

            $this->permissions  = check_permission($tab_module_code);

            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            #Resources
            #CSS
            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_css'][] = CSS_SELECTIZE;
            #JS
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_SELECTIZE;
            
            //Get Document Transmittal details
            $where         = ['document_transmittal_id' => $this->task_details['reference_id']];
            $dt_details    = $this->dt_model->get_document_transmittal($where);
            
            //Prepare data for the view
            if(!empty($dt_details['vendor_code'])){
                $fields                  = ['vendor_name'];
                $where                   = ['vendor_code' => $dt_details['vendor_code']];  
                $this->task_view_data['vendor_details'] = $this->dt_model->get_specific_vendor($where, $fields);
            }
            if(!empty($dt_details['org_code'])){
                $fields                  = ['name', 'org_code'];
                $where                   = ['org_code' => $dt_details['org_code']];  
                $this->task_view_data['org_details'] = $this->dt_model->get_specific_org($where, $fields);
            }
            $this->task_view_data['dt_details'] = $dt_details;

            #Special Condition for loading Input fields being Editable if the Task is Returned
            if($task['returned_flag'] == ENUM_YES){
                $this->task_view_data['is_returned'] = TRUE;

                if($task['task_status_id'] != TASK_STATUS_DONE){
                    $this->task_view_data['edit_task'] = TRUE;
                    $this->task_view_data['organizations']  = get_organizations_by_org_type_w_scope('PORTAL_DOCUMENT_TRANSMITTAL_TRANSMITTAL');      
                }
            }
            
            //Load the content of the task
            $this->data['page_title']       = 'Document Tracer Transmittal Batch Number: '.$dt_details['document_tracer_batch_number'];
            $this->task_page                = '/Upload_transmittal_document_view';

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
    
    public function process() //task status 1 = save as draft, 2 = submit
    {
        try
        {
            $flag           = ERROR;
            // $response       = [];
            // $now            = date(FORMAT_DB_DATE);
            // $doc_ref        = '';
            // $status         = TASK_STATUS_ONGOING;
            $data           = $this->_validate();

            //Start the db transaction                
            Portal_Model::beginTransaction();

            $task_id        = $data['task_id'];
            $task_status_id = $data['task_status'];
            
            $task_details   = $this->tm_model->get_task_details($task_id);

            $where          = array('document_transmittal_id' => $task_details['reference_id']);
            $dt_details    = $this->dt_model->get_document_transmittal($where);

            #Update Document Transmittal
            $update_values = [
                'remarks'                        => $data['remarks']
            ];
            if($task_details['returned_flag'] == ENUM_YES){
                $update_values += [
                    'transmittal_date'               => $data['transmittal_date'],
                    'document_tracer_batch_number'   => $data['document_tracer_batch_number'],
                    'org_code'                       => $data['business_center'],
                    'courier_tracking_number'        => $data['courier_tracking_number'],
                    'transmittal_document_sender'    => $data['transmittal_document_sender'],
                    'release_date'                   => $data['release_date'] !== '' ? $data['release_date'] : NULL,
                ];
            }
            $this->dt_model->update_document_transmittal($where, $update_values);

            #Update Task
            $task_values = array(
                'reference'         => $dt_details['document_transmittal_id'],
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            );
            $this->tag_task($task_id, $task_status_id, $task_values, NULL, $dt_details['recipient_id']);

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
            'msg'   => $msg,
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            $params = get_params(TRUE, TRUE);
            
            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);
            $task_status = $params['task_status']; 

            #Required fields are not needed when saving as draft
            if($task_status == TASK_STATUS_DONE) #1 = save as draft, 2 = submit
            {   
                #For the input fields
                $required = [
                    'remarks' => 'Remarks'
                ];

                $document_type_details = $this->document_model->get_document(['pria_task_id' => decrypt_id($params['etd'])], ['document_id']);
                #For the upload field
                # When the task is returned, the document is already uploaded.
                # When the document is already uploaded, the upload form won't have the doc_transmittal in the payload
                # So we need to check if the document is already uploaded in the database
                if(empty($document_type_details)){
                    if(empty($params['doc_transmittal'])){
                        throw new Exception('Transmittal Document is required.');
                    }
                }
            }
            
            $constraints['transmittal_date']    = [
                'data_type'         => 'date',
                'name'              => 'Transmittal Date'
            ];

            $constraints['document_tracer_batch_number']    = [
                'data_type'         => 'string',
                'name'              => 'Document Tracer Batch Number'
            ];

            $constraints['business_center']    = [
                'data_type'         => 'string',
                'name'              => 'Business Center'
            ];

            $constraints['courier_tracking_number']    = [
                'data_type'         => 'string',
                'name'              => 'Courier/Tracking Number',
            ];

            $constraints['transmittal_document_sender']    = [
                'data_type'         => 'string',
                'name'              => 'Document Sender'
            ];

            $constraints['release_date'] = [
                'data_type' => 'date',
                'name'      => 'Date Release',
            ];

            $constraints['remarks'] = [
                'data_type' => 'string',
                'name'      => 'Remarks',
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
            $data = $this->validate_inputs($params, $constraints);

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