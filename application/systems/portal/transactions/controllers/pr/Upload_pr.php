<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_pr extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_PURCHASE_REQUESTS;
        
        $this->load->model($this->folder.'/Pr_model', 'pr_model'); 
        $this->load->model(FOLDER_BOQ.'/boq_model', 'bq_model'); 
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
            $tab_module_details = $this->tm_model->get_tab_module(
                ['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC]
            );

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

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;
 
            $resources['load_css'][]    = CSS_DATETIMEPICKER;
            $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];
            
            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];
            $document_type_det  = $this->pr_model->get_specific_task_document_type($where, $field_select, array(), FALSE);

            //Get PR details
            $pr_details    = $this->pr_model->get_pr_details($this->task_details['reference_id'], $ag_code);

            $this->task_view_data['pr_details']     = $pr_details;

            if($tab_module_code == MODULE_PORTAL_TRANS_CONTRACTORS_PR)
            {
                $boq_id     = (ISSET($pr_details['boq_id']) AND !EMPTY($pr_details['boq_id']))? $pr_details['boq_id']: NULL;
                $org_code   = (ISSET($pr_details['org_code']) AND !EMPTY($pr_details['org_code']))? $pr_details['org_code']: NULL;

                $this->task_view_data['categories']             = $this->bq_model->get_param_contractor_process_categories_by_process_type(CONTRACTOR_PROCESS_BOQ);
                
                $this->task_view_data['boq_asset_codes']        = $this->bq_model->get_boq_asset_codes_joined_to_params($boq_id);

                $task_view_data                                 = array_merge($this->task_view_data, ['view' => TRUE]);
                
                $this->task_view_data['boq_asset_codes_view']   = $this->load->view(PORTAL_TASK.'/'.FOLDER_BOQ.'/boq_asset_codes', $task_view_data, TRUE);

                $this->task_view_data['boq_asset_codes']        = $this->bq_model->get_boq_asset_codes_grouped_by_contractor($boq_id);
                $this->task_view_data['contractors']            = $this->bq_model->get_contractors_by_org_code($org_code);

                $task_view_data                                 = array_merge($this->task_view_data, ['view' => TRUE]);

                $this->task_view_data['contractor_view']        = $this->load->view(PORTAL_TASK.'/'.FOLDER_BOQ.'/boq_indicate_categories', $task_view_data, TRUE);

                $where          = ['document_type_code' => DOC_TYPE_RFA, 'reference' => $boq_id, 'module_code' => MODULE_PORTAL_TRANS_CONTRACTORS];
                $rfa_form       = $this->dm_model->get_document($where);

                $this->task_view_data['rfa_form']               = create_document_tag($rfa_form);

                $task_page  = '/upload_pr_contractor';
            }
            else
            {
                $pr_cc_details  = $this->pr_model->get_pr_cost_centers($this->task_details['reference_id']);
                $this->task_view_data['pr_cc_details']          = $pr_cc_details;

                $task_page  = '/upload_pr';
            }

            //Load the content of the task
            $this->data['page_title']       = 'PR number: '.$pr_details['pr_num'];
            $this->task_page                = $task_page;
        
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

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            $doc_ref      = '';
            $data         = $this->_validate();
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];
            
            $task_details = $this->tm_model->get_task_details($task_id);

            //Get module code
            $module_code = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];

            $document_type_det  = $this->tm_model->get_specific_task_document_type($where, $field_select,array(), FALSE);
            $document_type_code = $document_type_det['document_type_code'];

            //Get SOA details   
            $fields         = array('*');
            $where          = array('pr_id' => $task_details['reference_id']);
            $pr_details     = $this->pr_model->get_purch_requisitions($where, $fields);

            $where          = ['document_type_code' => DOC_TYPE_PR, 'reference' => $task_details['reference_id']];
            $pr_form        = $this->document_model->get_document($where);

            if(EMPTY($pr_form)){
                if(empty($data['doc_pr'])){
                    throw new Exception('PR Document file is required.');   
                }
                $doc_ref             = encrypt_id($task_details['reference_id']);  
            }   

            //Start the db transaction                
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            $pr_id               = $this->tm_model->get_task_workflow_reference_id($task_id);


            // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
            //     'reference'         => $pr_id,
            //     'start_date'        => date(FORMAT_DB_DATETIME),
            //     'actual_start_date' => date(FORMAT_DB_DATETIME)
            // ]);

            $this->tag_task($task_id, $task_status_id, [
                'reference'         => $pr_id,
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            ]);

            // $response['actor']   = $ongoing['actor_name'];
            $status              = TASK_STATUS_ONGOING;
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
            $params = get_params();
            
            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_string('doc_pr')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                //'doc_pr'        => 'DOC PR'
            ];

            $constraints['doc_pr']    = [
                'data_type'         => 'string',
                'name'              => 'DOC PR'
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