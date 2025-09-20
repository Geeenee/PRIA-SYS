<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Renew_contract extends Task_Controller 
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
            $tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC, 'transaction_tab' =>  TRANS_TAB_RENEWALS]);

            //get tab module details
            //$tab_module_details = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module_code]);
            
            $tab_module_code = $tab_module_details['tab_module_code'];
            
            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];

            $this->tab_module_code  = $tab_module_code;

            $this->permissions  = check_permission($tab_module_code);
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_css'][] = CSS_SELECTIZE;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_SELECTIZE;

            //Get SOA details
            $fields                     = ['*'];
            $where                      = ['contract_id' => $this->task_details['reference_id']];
            $renewal_contract_details   = $this->renewal_model->get_contract($where, $fields);

            //Get SOA details
            $fields                 = ['*'];
            $where                  = ['contract_id' => $renewal_contract_details['reference_contract_id']];
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

            //Renewed Contract Details
            $this->task_view_data['renewal_contract_details'] = $renewal_contract_details;

            //Store Infomation
            IF($contract_details['site_id']){
                $where = array(
                    'site_id' => $renewal_contract_details['site_id']
                );
                $this->task_view_data['site_det'] = $this->renewal_model->get_site($where);
            }

            //Lessor Information
            IF($contract_details['vendor_code']){
                $where = array(
                    'vendor_code' => $renewal_contract_details['vendor_code']
                );
                $this->task_view_data['vendor_det'] = $this->renewal_model->get_vendor($where);
            }

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];
            $document_type_det  = $this->renewal_model->get_specific_task_document_type($where, $field_select, array(), FALSE);

            $this->task_view_data['contract_details'] = $contract_details;            

            //Payment Terms
            $this->task_view_data['payment_terms'] = $this->renewal_model->get_all_payment_terms();

            //Load the content of the task
            $this->data['page_title']       = 'Contract number: '.$renewal_contract_details['contract_code'];
            $this->task_page                = '/renew_contract';
        
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
            $doc_ref      = '';
            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATETIME);
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
            /* print_var_export($task_details); die; */
            //Get module code
            $module_code = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];


            $document_type_det  = $this->renewal_model->get_specific_task_document_type($where, $field_select,array(), FALSE);
            $document_type_code = $document_type_det['document_type_code'];

            //Get SOA details   
            $fields             = array('*');
            $where              = array('contract_id' => $task_details['reference_id']);
            $contract_details   = $this->renewal_model->get_contract($where, $fields);

            $where          = ['document_type_code' => DOC_TYPE_SIGNED_RENEWAL, 'reference' => $task_details['reference_id']];
            $contract_form        = $this->document_model->get_document($where);

            if(EMPTY($contract_form)){
                if(empty($data['doc_signed_renewal'])){
                    throw new Exception('Signed contract file is required.');
                }
                $doc_ref             = encrypt_id($task_details['reference_id']);
            }

            //Update Contract
            // KPOYAOAN 2021-11-03 Moved to President Approval
            /*$update = array(
                'contract_status_code'  => CONTRACT_RENEWED,
                'modified_by'           => $this->session->userdata('user_id'),
                'modified_date'         => $now
            );

            $where              = array('contract_id' => $contract_details['reference_contract_id']);
            $this->renewal_model->update_contract($where, $update);*/

            //Get Contract details
            $fields             = array('*');
            $where              = array('contract_id' => $task_details['reference_id']);
            // $contract_details   = $this->renewal_model->get_contract($where, $fields);

            $update = array(
                'date_from'             => $data['contract_period_from'],
                'date_to'               => $data['contract_period_to'],
                'payment_term_code'     => $data['payment_terms'],
                // 'contract_status_code'  => CONTRACT_NEW,//CONTRACT_RENEWED, // KPOYAOAN 2021-11-03 Moved to President Approval
                'modified_by'           => $this->session->userdata('user_id'),
                'modified_date'         => $now,
                'saved_flag'            => MAINTAINER_YES
            );
            
            $this->renewal_model->update_contract($where, $update);
            //Ends

            //Update the reference of the task and status ( w/other details )
            // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
            //     'reference'         => $contract_details['contract_id'],
            //     'start_date'        => date(FORMAT_DB_DATETIME),
            //     'actual_start_date' => date(FORMAT_DB_DATETIME)
            // ]);

            $this->tag_task($task_id, $task_status_id, [
                'reference'         => $contract_details['contract_id'],
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            ]);

            // $response['actor']   = $ongoing['actor_name'];                
            // $status              = TASK_STATUS_ONGOING;
            $msg                 = $this->lang->line('data_saved');

            // $doc_ref             = encrypt_id($task_details['reference_id']);

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
            $required   = array();

            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_string('doc_signed_renewal')
            ->filter_date('contract_period_from')
            ->filter_date('contract_period_to')
            ->filter_string('payment_terms')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                //'doc_renewal'        => 'Renewal Contract File'
                'payment_terms'             => 'Payment Term',
                'contract_period_from'      => 'Contract Period from',
                'contract_period_to'        => 'Contract Period to',
            ];

            $constraints['doc_signed_renewal']    = [
                'data_type'         => 'string',
                'name'              => 'Renewal Contract File'
            ];

            $constraints['payment_terms']    = [
                'data_type'         => 'string',
                'name'              => 'Payment Term'
            ];

            $constraints['contract_period_from']    = [
                'data_type'         => 'string',
                'name'              => 'Contract Period From'
            ];

            $constraints['contract_period_to']    = [
                'data_type'         => 'string',
                'name'              => 'Contract Period To'
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