<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_approve_po extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        // $this->module_code      = MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA;
        $this->folder           = FOLDER_PURCHASE_ORDERS;
        
        $this->load->model($this->folder.'/Po_model', 'po_model'); 
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');

        // $this->permissions      = check_permission($this->module_code);
        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{
            //if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params    = get_params(TRUE, TRUE);
            $task_id                = base64_url_decode($params['t'] );

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

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;
            $resources['load_css'][]    = CSS_DATETIMEPICKER;

            // $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];
            
            //Get PO details
            $fields         = ['*'];
            $where          = ['po_num' => $this->task_details['reference_num']];
            $po_details    = $this->po_model->get_po($where, $fields);

            $po_id = $po_details['po_id'];

            $this->task_details['task_reference_id'] = $po_details['po_id'];

            if( ! EMPTY($po_details['created_date']))
            {
                //Get Task Document Type
                $field_select       = ['*'];
                $where              = ['pria_task_id' => $task_id];
                $document_type_det  = $this->po_model->get_specific_task_document_type($where, $field_select, array(), FALSE);
                $document_type_code = $document_type_det['document_type_code'];

                if($po_details['vendor_code']){
                    $fields                                 = ['*'];
                    $where                                  = ['vendor_code' => $po_details['vendor_code']];  
                    $this->task_view_data['vendor_details'] = $this->po_model->get_all_vendors($where , array('*'), array(), FALSE);
                }

                $fields  = ['*'];
                $this->task_view_data['po_details'] = $this->po_model->get_po(['po_num' => $this->task_details['reference_num']], $fields);
                
                if($po_details['created_by'])
                    $this->task_view_data['user_info'] = $this->users_model->get_user_details($po_details['created_by']);        
            }

            //Load the content of the task
            $this->data['page_title']       = 'PO Number: '.$po_details['po_num'];
            $this->task_page                = '/return_approve_po';

            $this->_load_task_view();
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_page( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	
            
			$this->error_page( $msg );
		}
	}
}