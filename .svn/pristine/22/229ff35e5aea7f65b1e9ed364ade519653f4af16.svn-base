<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_approve_delivery_receipt extends Task_Controller 
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
            
            //Starts
            $stage_info         = $this->tm_model->get_stage_tasks_details(array($task_workflow['pria_stage_id']));
            $task_reference_info  = $stage_info[$task_workflow['pria_stage_id']][0]['task_reference_id'];
            //Ends

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
            /* print_var_export($this->tab_module_code, $this->permissions); die; */
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

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
            
            $where          = ['po_id' => $this->task_details['reference_id']];
            $dr_ref_details = $this->dr_model->get_delivery_goods_reference($where);

            $this->task_details['task_reference_id'] =  $task_reference_info;//$dr_ref_details['dr_gr_id'];
            
            //support center
            $this->task_view_data['support_centers'] = $this->dr_model->get_all_organizations();
            
            //Get GR details
            if($task_reference_info){
                $fields             = ['*'];
                $where              = ['dr_gr_id' => $task_reference_info];
                $dgr_details        = $this->dr_model->get_delivery_goods_receipt($where, $fields);
                
                $this->task_view_data['delivery_receipt_details'] = $dgr_details;
            }

            if($dgr_details['site_id'])
                $this->task_view_data['site_info'] = $this->dr_model->get_site(['site_id' => $dgr_details['site_id']]);

            if($dgr_details['dr_recipient_id']){
                $this->task_view_data['recipient_id'] = $dgr_details['dr_recipient_id'];
                $this->task_view_data['recipient_info'] = $this->dr_model->get_dr_doc_recipient(['user_id' => $dgr_details['dr_recipient_id']]); 
            }
                
            //Load the content of the task
            $this->data['page_title']       = 'Purchase order: '.$po_details['po_num'];
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