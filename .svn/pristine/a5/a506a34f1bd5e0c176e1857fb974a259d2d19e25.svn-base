<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_approve_pr extends Task_Controller 
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

            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_UPLOAD;

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
 
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

            $this->task_details['task_reference_id']            = $pr_details['pr_id'];

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
}