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

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_UPLOAD;

            //Get Document Transmittal details
            // $fields         = ['*'];
            $where         = ['document_transmittal_id' => $this->task_details['reference_id']];
            // print_var_export($where); die();
            $dt_details    = $this->dt_model->get_document_transmittal($where);
            // print_var_export($dt_details); die();

            $where         = ['document_type_code' => DOC_TYPE_DOCUMENT_TRANSMITTAL, 'reference' => $this->task_details['reference_id']];
            $dt_form       = $this->dm_model->get_document($where);
            
            // if( ! EMPTY($soa_form))
            //     $this->task_access['hide_btn'] = TRUE;


            /*  print_var_export($common); die; */

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;
 
            $resources['load_css'][]    = CSS_DATETIMEPICKER;
            $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];
            
            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];
            $document_type_det  = $this->dt_model->get_specific_task_document_type($where, $field_select, array(), FALSE);
            
            //Prepare data for the view
            if(!empty($dt_details['vendor_code'])){
                $fields                  = ['vendor_name'];
                $where                   = ['vendor_code' => $dt_details['vendor_code']];  
                $this->task_view_data['vendor_details'] = $this->dt_model->get_specific_vendor($where, $fields);
            }
            if(!empty($dt_details['org_code'])){
                $fields                  = ['name'];
                $where                   = ['org_code' => $dt_details['org_code']];  
                $this->task_view_data['org_details'] = $this->dt_model->get_specific_org($where, $fields);
            }
            $this->task_view_data['dt_details'] = $dt_details;
            // print_var_export($dt_details); die();

            // Finance In Charge is removed from the workflow
            // if(ISSET($this->task_view_data['dt_details']['recipient_id'])) 
            //     $this->task_view_data['recipient_info'] = $this->users_model->get_user_details($this->task_view_data['dt_details']['recipient_id']);    

            //initialization for showing fields inside the form
            // $this->task_view_data['show_po']                = FALSE;
            // $this->task_view_data['show_fowarders']         = FALSE;
            // $this->task_view_data['show_week_period']       = TRUE;
            // $this->task_view_data['show_receipt']           = TRUE;
            // $this->task_view_data['show_document_receipt']  = TRUE;

            // 
            // switch ($tab_module_details['ag_code']) {
            //     case AG_GOODS_BAVI:
            //     case AG_GOODS_BFFI:
            //     case AG_GOODS_MARINADES:

            //         //get po reference
            //         $where          = array('soa_id' => $soa_details['soa_id']);
            //         $reference_info = $this->soa_model->get_pria_references($where, ['*'], [], TRUE);
            //         $po_ids         = (is_array($reference_info) AND count($reference_info) > 0)? array_column($reference_info, 'po_id'): [0];

            //         //get po reference info
            //         $where          = array('po_id' => ['IN', $po_ids]);
            //         $po_ref_info    = $this->soa_model->get_purchase_order($where, ['*'], [], TRUE);
                    
            //         $this->task_view_data['po_ref_info'] = $po_ref_info;

            //         $this->task_view_data['show_po'] = TRUE;
            //         // $this->task_view_data['show_receipt'] = TRUE;
            //         break;
            //     case AG_FORWARDERS:
            //         //$this->task_view_data['show_receipt'] = TRUE;
            //             $fields = ['dr_num'];
            //             $soa_drs = $this->soa_model->get_soa_drs_by_soa_id($this->task_details['reference_id'], $fields);

            //             $this->task_view_data['show_fowarders'] = TRUE;
            //             $this->task_view_data['forwarders_dr']  = implode(', ', array_column($soa_drs, 'dr_num'));
            //         break;
            //     case AG_TOLL_PARTNERS:
            //     case AG_INBOUND_CENTRAL:
            //     case AG_FEEDMILL:
            //     case AG_MANPOWER:
            //         $this->task_view_data['show_receipt']           = FALSE;
            //         $this->task_view_data['show_document_receipt']  = FALSE;
            //         break;
            //     default:
            //         # code...
            //         break;
            // }

            // if($soa_details['soa_type'] == SOA_CENTRAL){
            //     $this->task_view_data['show_week_period']   = TRUE;
            // }

            //Load the content of the task
            $this->data['page_title']       = 'Document Tracer Transmittal Batch Number: '.$dt_details['document_tracer_batch_number'];
            $this->task_page                = '/Upload_transmittal_document_view';
            // print_var_export($this); die();
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
            $response       = [];
            $now            = date(FORMAT_DB_DATE);
            $doc_ref        = '';
            $status         = TASK_STATUS_ONGOING;
            $data           = $this->_validate();

            //Start the db transaction                
            Portal_Model::beginTransaction();
            
            //Initial audit trail config
            $table          = Portal_Model::PORTAL_TABLE_SOA;
            $audit_schema   = [DB_PORTAL];
            $audit_table    = [$table];

            $task_id        = $data['task_id'];
            $task_status_id = $data['task_status'];
            
            $task_details   = $this->tm_model->get_task_details($task_id);

            //Get module code
            // $module_code    = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

            //Get Task Document Type
            // $field_select       = ['*'];
            // $where              = ['pria_task_id' => $task_id];

            // $document_type_det  = $this->soa_model->get_specific_task_document_type($where, $field_select,array(), FALSE);
            // $document_type_code = $document_type_det['document_type_code'];

            //Get Document transmittal details   
            $fields         = array('*');
            $where          = array('document_transmittal_id' => $task_details['reference_id']);
            $dt_details    = $this->dt_model->get_document_transmittal($where, $fields);

            // if($task_status_id == TASK_STATUS_DONE AND EMPTY($dt_details['document_transmittal_date']))
            // {
            //     $sub_val = array('document_transmittal_date' => date(FORMAT_DB_DATETIME));
            //     $this->dt_model->update_document_transmittal($where, $sub_val);
            // }

            // $where          = ['document_type_code' => DOC_TYPE_SOA, 'reference' => $task_details['reference_id']];
            // $soa_form      = $this->document_model->get_document($where);

            //validate soa file
            // if(EMPTY($soa_form) AND EMPTY($data['doc_soa'])){
            //     throw new Exception('SOA File is required.');
            // }

            // if(EMPTY($soa_form)){
            //     if(empty($data['doc_soa'])){
            //         throw new Exception('SOA File is required.');
            //     }
            //     $doc_ref             = encrypt_id($task_details['reference_id']);
            // }       

            //Update the reference of the task and status ( w/other details )
            /*$ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                'reference'         => $soa_details['soa_id'],
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            ]);
            
*/

            $update_values = [
                'remarks' => $data['remarks']
            ];

            $this->dt_model->update_document_transmittal($where, $update_values);


            $val = array(
                'reference'         => $dt_details['document_transmittal_id'],
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            );

            /*if($task_status_id == TASK_STATUS_DONE AND EMPTY($dt_details['submission_date']))
            {
                $val = array_merge(array('submission_date' => date(FORMAT_DB_DATETIME)),$val);
            } */

            $this->tag_task($task_id, $task_status_id, $val, NULL, $dt_details['recipient_id']);

            //$response['actor']   = $ongoing['actor_name'];                
            // $status              = TASK_STATUS_ONGOING;
            // $msg                 = $this->lang->line('data_saved');

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

            /*
            Required fields are not needed when saving as draft
            */
            if($task_status == TASK_STATUS_DONE) // 1 = save as draft, 2 = submit
            {   
                #For the input fields
                $required = [
                    // 'remarks'
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