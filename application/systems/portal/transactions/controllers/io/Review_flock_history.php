<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Review_flock_history extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACT_GROWERS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CG_IO;
        $this->folder           = FOLDER_INTERNAL_ORDER;
        
        $this->load->model($this->folder.'/internal_order_model', 'io_model'); 
        $this->load->model(FOLDER_DELIVERY_GOODS.'/delivery_goods_model', 'dgr_model');
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        
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

            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $io_id = $this->task_details['reference_id'];

            //Set this for the attachment;
            $this->task_details['task_reference_id'] =  $io_id;
            
            if( ! EMPTY($io['fhr_document_num']))
            {
                //Get Task Document Type
               /*  $field_select       = ['*'];
                $where              = ['pria_task_id' => $task_id];
                $document_type_det  = $this->io_model->get_specific_task_document_type($where, $field_select);
                $document_type_code = $document_type_det['document_type_code']; */

                $fields  = ['fhr_document_num', 'fhr_submit_date', 'pre_placement', 'brooding_audit', 'biosecurity_audit', 'io_id'];

                $this->task_view_data['fhr_details'] = $this->io_model->get_internal_order(['io_id' => $io_id], $fields);

                /* $fields  = ['*'];
                $this->task_view_data['fhr_file_details'] = $this->io_model->get_internal_order_document(['io_id' => $io_id, 'document_type_code' => $document_type_code], $fields);
                
                if($io['created_by'])
                    $this->task_view_data['user_info'] = $this->users_model->get_user_details($io['created_by']);  */
            }

            //Load the content of the task
            $this->data['page_title']       = 'Internal Order: '.$io['io_num'];
            $this->task_page                = '/review_flock_history';

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