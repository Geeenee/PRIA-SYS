<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_rfa extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACTORS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CONTRACTORS_BOQ;
        $this->folder           = FOLDER_BOQ;
        
        $this->load->model($this->folder.'/boq_model', 'bq_model'); 
        
        $this->permissions      = check_permission($this->tab_module_code);

        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{  
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params         = get_params(TRUE, TRUE);

            $task_id        = base64_url_decode($params['t']);

            $this->_initialize_task($task_id);

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details']     = $boq_details;

            $this->task_details['task_reference_id'] = $boq_id;

            $where          = ['document_type_code' => DOC_TYPE_RFA, 'reference' => $boq_id];
            $iview_map      = $this->dm_model->get_document($where);

            //If There's an uploaded file hide save because edit will be in versioning
          /*   if( ! EMPTY($iview_map))
                $this->task_access['hide_btn'] = TRUE; */

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
          /*   $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */

            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_page                  = '/upload_rfa';
            
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
    
    public function process()
    {
        try
        {
            $params       = get_params();
            $flag         = ERROR;
            $status       = '';
            $msg          = '';
            $task         = '';
            $doc_ref      = '';    
            $required     = [];
            $constraints  = [];

            $task_id      = decrypt_id($params['etd']);

            $task_details  = $this->tm_model->get_task_details($task_id);

            $boq_id        = $task_details['reference_id'];

            $where         = ['document_type_code' => DOC_TYPE_RFA, 'reference' => $boq_id];
            $rfa_form      = $this->dm_model->get_document($where);

            if( EMPTY($rfa_form))
            {
                $required = ['doc_rfa' => 'RFA'];

                $constraints['doc_rfa'] = [
                    'data_type'			=> 'string',
                    'name'				=> 'RFA'
                ];

            }

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
            $this->validate_inputs($params, $constraints);

            //Update the reference of the task and status ( w/other details )
          //  $ongoing      = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $boq_id]);
            $this->tag_task($task_id, $params['task_status'], ['reference' => $boq_id]);

            $doc_ref      = encrypt_id($boq_id);
            $flag         = SUCCESS;
            $msg          = $this->lang->line('data_saved');
        }
        catch(PDOException $e)
        {
            $msg 	= $this->get_user_message($e);

            Portal_Model::rollback();
        }
        catch(Exception $e)
        {
            $msg  	= $this->rlog_error($e, TRUE);	

            Portal_Model::rollback();
        }

        echo json_encode([
            'flag'   => $flag,
            'msg'    => $msg,
            'task'   => $task,
            'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }
    
    private function _validate($params, $task_details)
    {
        $insert = EMPTY($task_details['task_reference_id']) ? true : false;
        //Filters the data inputted by the user 
        $params = $this->set_filter( $params )
        ->filter_float('amount')
        ->filter_string('doc_boq')
        ->filter();

        //$params['task_id'] = decrypt_id($params['etd']);

        //Define the required fields.
        $required = [
            'amount'  => 'Amount',
        ];

        if($insert)
        {
            $required['doc_boq'] = 'BOQ File';

            $constraints['doc_boq'] = [
                'data_type'			=> 'string',
                'name'				=> 'BOQ File'
            ];
        }

        $constraints['amount'] = [
            'data_type'			=> 'amount',
            'name'				=> 'Amount'
        ];

        $constraints['task_id'] = [
            'data_type'   => 'db_value',
            'name'        => 'ETD',
            'field'       => 'COUNT( 1 ) as check_row',
            'check_field' => 'check_row',
            'where'       => 'pria_task_id',
            'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
        ];

         /* Validate the required fields */
         $this->check_required_fields($params, $required);

         /* Validate constraints */
         return $this->validate_inputs($params, $constraints);
    }
}