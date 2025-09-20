<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ret_app_fhr extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACT_GROWERS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CG_IO;
        $this->folder           = FOLDER_INTERNAL_ORDER;
        
        $this->load->model($this->folder.'/internal_order_model', 'io_model'); 
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');

        // Change request 12.21.22 Starts Here
        $this->load->model(FOLDER_DELIVERY_GOODS.'/delivery_goods_model', 'dgr_model');
        // Change request 12.21.22 Ends Here
        
        $this->permissions      = check_permission($this->tab_module_code);

        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params    = get_params(TRUE, TRUE);

            $task_id   = base64_url_decode($params['t']);

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            
            //Get IO details
            $fields     = ['*'];
            $where      = ['io_id' => $this->task_details['reference_id']];                      
            $io         = $this->io_model->get_internal_order($where, $fields);

            // Change request 12.21.22 Starts Here

            $this->task_view_data['io']             = $io;

            $doc_gr_id      = $this->dgr_model->get_gdr_task_ref($this->task_details['pria_workflow_id']);
            $doc_gr_details = $this->dgr_model->get_delivery_goods_receipt(['dr_gr_id' => $doc_gr_id['reference']]);
            $this->task_view_data['doc_gr_details'] = $doc_gr_details;

            // Change request 12.21.22 Ends Here

            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $io_id                                   = $this->task_details['reference_id'];
            $this->task_details['task_reference_id'] = $io_id;
            
            if( ! EMPTY($io['fhr_document_num']))
            {
                // Change request 12.21.22 Starts Here
                // $fields  = ['fhr_document_num', 'fhr_submit_date', 'pre_placement', 'brooding_audit', 'biosecurity_audit', 'io_id'];
                $fields  = ['fhr_document_num', 'fhr_submit_date', 'io_id', 
                    'pre_placement', 
                    'brooding_audit', 
                    'biosecurity_audit',
                    'actual_clean_up_date',
                    'harvested_heads_num',
                    'delivered_feeds_num',
                    'feeds_used_num',
                    'harvested_kilos_num',
                    'feeds_retrieval',
                    'fmis_transacted_flag',
                ];
                // Change request 12.21.22 Ends Here

                $this->task_view_data['fhr_details'] = $this->io_model->get_internal_order(['io_id' => $io_id], $fields);

            }  

            //Load the content of the task
            $this->data['page_title']       = 'Internal Order: '.$io['io_num'];
            $this->task_page                = '/ret_app_fhr';

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