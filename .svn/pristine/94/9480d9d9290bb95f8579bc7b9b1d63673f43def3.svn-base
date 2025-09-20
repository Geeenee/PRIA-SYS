<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_approve_dr extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        // $this->module_code      = MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA;
        $this->folder           = FOLDER_DELIVERY_GOODS;
        $this->po_folder        = FOLDER_PURCHASE_ORDERS;
        
        $this->load->model($this->folder.'/delivery_goods_model', 'dr_model');
        $this->load->model($this->po_folder.'/po_model', 'po_model');

        // $this->permissions      = check_permission($this->module_code);
        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{
            // if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params    = get_params(TRUE, TRUE);
            $task_id   = base64_url_decode($params['t'] );

            //get task details
            $task = $this->tm_model->get_task_details($task_id);
            
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

            $this->_initialize_task($task_id);

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;
            $resources['load_css'][]    = CSS_DATETIMEPICKER;

            // $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];

            //Get PO details
            $fields             = ['*'];
            $where              = ['po_id' => $this->task_details['reference_id']];
            $po_details         = $this->po_model->get_po($where, $fields);

            $this->task_view_data['po_details'] = $po_details;

            $dgr_id             = $this->task_details['task_reference_id'];

            //Get PO details
            if($dgr_id){
                $fields             = ['*'];
                $where              = ['dgr_id' => $this->task_details['task_reference_id']];
                $dgr_details        = $this->dr_model->get_delivery_goods_receipt($where, $fields);

                $this->task_view_data['dgr_details'] = $dgr_details;
            }

            // $this->task_view_data['delivery_receipt_details'] = $dgr_details;

            //Load the content of the task
            $this->data['page_title']       = 'SOA Number: '.$po_details['po_num'];
            $this->task_page                = 'return_approve_dr';

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