<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_site_renewal extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_RENEWAL;
        
        $this->load->model($this->folder.'/Renewal_model', 'renewal_model'); 
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
            //$tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC]);

            //get tab module details
            //$tab_module_details = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module_code]);
            
            //$tab_module_code = $tab_module_details['tab_module_code'];
          //  print_var_export($tab_module_details); die;

            $tab_module_code        = MODULE_PORTAL_TRANS_LESSORS_RENEWAL;
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

            //Get SOA details
            $fields                 = ['*'];
            $where                  = ['contract_id' => $this->task_details['reference_id']];
            $contract_details       = $this->renewal_model->get_contract($where, $fields);
            
            /*  print_var_export($common); die; */

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;
 
            $resources['load_css'][]    = CSS_DATETIMEPICKER;
            $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];

            //Contract Details
            $this->task_view_data['contract_det'] = $contract_details;

            //Store Infomation
            IF($contract_details['site_id']){
                $where = array(
                    'site_id' => $contract_details['site_id']
                );
                $this->task_view_data['site_det'] = $this->renewal_model->get_site($where);
            }

            //Lessor Information
            IF($contract_details['vendor_code']){
                $where = array(
                    'vendor_code' => $contract_details['vendor_code']
                );
                $this->task_view_data['vendor_det'] = $this->renewal_model->get_vendor($where);
            }

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];
            $document_type_det  = $this->renewal_model->get_specific_task_document_type($where, $field_select, array(), FALSE);

            $this->task_view_data['contract_details'] = $contract_details;            
            
            //Load the content of the task
            $this->data['page_title']       = 'Contract number: '.$contract_details['contract_code'];
            $this->task_page                = '/upload_site_renewal';
        
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
            $response     = [];
            $doc_ref      = '';
            $status       = TASK_STATUS_ONGOING;
            $now          = date(FORMAT_DB_DATE);
            $data         = $this->_validate();

            //Start the db transaction                
            Portal_Model::beginTransaction();

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_CONTRACTS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];
            $task_details = $this->tm_model->get_task_details($task_id);

            //Get module code
            $module_code = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];

            $document_type_det  = $this->renewal_model->get_specific_task_document_type($where, $field_select,array(), FALSE);
            $document_type_code = $document_type_det['document_type_code'];

            $contract_id        = $task_details['reference_id'];

            //Get SOA details   
            $fields             = array('*');
            $where              = array('contract_id' => $contract_id);
            $contract_details   = $this->renewal_model->get_contract($where, $fields);

            $where          = ['document_type_code' => DOC_TYPE_RENEWAL, 'reference' => $contract_id];
            $site_renewal_form        = $this->document_model->get_document($where);

            if(EMPTY($site_renewal_form)){
                if(empty($data['doc_renewal'])){
                    throw new Exception('Site Renewal file is required.');
                }
                $doc_ref             = encrypt_id($contract_id);  
            } 
      
            $this->renewal_model->update_contract(['contract_id' => $contract_id], ['cn_recommendation' => $data['recommendation']]);
             
            $this->tag_task($task_id, $task_status_id, [
                'reference'         => $contract_details['contract_id'],
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            ]);

            $msg                 = $this->lang->line('data_saved');

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
            // 'task'   => $response,
            // 'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            $params     = get_params(TRUE, TRUE);
         
            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            // ->filter_string('doc_renewal')
            ->filter_string('recommendation')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            if(ISSET($params['task_status']) AND $params['task_status'] == TASK_STATUS_DONE)
            {
                $required = [
                    'recommendation' => 'Recommendation',
                    //'doc_renewal'  => 'Renewal Contract File'
                ];
            }

            $constraints['recommendation'] = [
                'data_type'			=> 'string',
                'name'				=> 'Recommendation'
            ];


            $constraints['doc_renewal']    = [
                'data_type'         => 'string',
                'name'              => 'Renewal Contract File'
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