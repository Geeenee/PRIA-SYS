<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doc_dr extends Task_Controller 
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

        $this->load->model('Documents_model', 'document_model');
        
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
            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_UPLOAD;

            //Get IO details
            $fields     = ['io_num'];
            $where      = ['io_id' => $this->task_details['reference_id']];                      
            $io         = $this->io_model->get_internal_order($where, $fields);

        

            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $dgr_id                     = $this->task_details['task_reference_id'];
            
            $this->task_view_data['open_last_dr']   = FALSE;
            $this->task_view_data['dependent_task'] = NULL;

            if( ! EMPTY($dgr_id))
            {
                $fields             = [ 'dr_num', 'dr_date', 'actual_placement_date', 'last_dr_flag', 'dr_gr_id' ];

                $this->task_view_data['doc_dr_details'] = $this->dgr_model->get_delivery_goods_receipt(['dr_gr_id' => $dgr_id], $fields);
                $this->task_view_data['core_task_id']   = $this->task_details['core_workflow_task_id'];

                $dependent_tasks = $this->dgr_model->get_dependent_tasks($this->task_details['core_workflow_task_id'], $task_id);

                if(ISSET($dependent_tasks['pria_task_id']) AND !EMPTY($dependent_tasks['pria_task_id']) AND EMPTY($dependent_tasks['pending_ongoing']) AND !EMPTY($dependent_tasks['dependent_task']))
                {
                    if($dependent_tasks['user_id'] == $this->session->user_id)
                        $this->task_view_data['open_last_dr']   = TRUE;

                    $this->task_view_data['dependent_task'] = $dependent_tasks['dependent_task'];
                }
            }    

            
            //Load the content of the task
            $this->data['page_title']       = 'Internal Order: '.$io['io_num'];    
            $this->task_page                = '/upload_doc_dr';

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
            $doc_ref      = '';
            $flag         = ERROR;
            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            $data         = $this->_validate();
            
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            //Set initial fields that is present for both insert and update action
            $fields       = [
                'dr_num'                    => $data['doc_dr_number'],
                'dr_date'                   => std_db_date_format($data['doc_dr_date']),
                'actual_placement_date'     => std_db_date_format($data['actual_placement_date']),
                //'dr_file_name'              => $data['doc_dr'],
                'last_dr_flag'              => ISSET($data['doc_dr_deliveries']) ? $data['doc_dr_deliveries']: ENUM_NO
            ];
            
            $task_id            = $data['task_id'];

            $task_status_id     = $data['task_status'];

            $task_details       = $this->tm_model->get_task_details($task_id);

            $other_drs          = $this->dgr_model->get_last_delivery(DR_DOCDR, $task_details['reference_id'], ['a.dr_gr_id']);

            $dgr_id             = $task_details['task_reference_id'];

            $where              = ['document_type_code' => DOC_TYPE_DOC_DR, 'pria_task_id' => $task_id];
            $dr_form            = $this->document_model->get_document($where);
            
            $where              = ['dr_gr_id' => $dgr_id];


            //Start the db transaction                
            Portal_Model::beginTransaction();
            
            //If reference id is empty action will be insert
            if( EMPTY($dgr_id))
            {
                //Validates if there's a last dr that's already existing
                if( ! EMPTY($other_drs) &&  $fields['last_dr_flag'] == ENUM_YES )
                    throw new Exception($this->lang->line('err_last_dr_exist'));

                //validate if file exist
                IF(EMPTY($data['doc_dr'])){
                    throw new Exception($this->lang->line('err_required_doc_dr_file'));
                }

                if(EMPTY($dr_form)){
                    if(empty($data['doc_dr'])){
                        throw new Exception('DR File is required.');
                    }
                } 

                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT];
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' DOC DR');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);
                $io_details   = $this->io_model->get_internal_order(['io_id' => $io_id], ['vendor_code']);

                //Set up fields that will be inserted 
                $insert     = array_merge([
                    'account_group_code' => AG_CONTRACT_GROWERS,
                    'vendor_code'        => $io_details['vendor_code'],
                    'pria_task_id'       => $task_id,
                    'dr_type_code'       => DR_DOCDR,
                    'created_by'         => $this->session->user_id,
                    'created_date'       => $now
                ], $fields); 

            
                $dgr_id     = $this->dgr_model->insert_delivery_goods_receipt($insert); 

                //Set up fields that will be inserted
                $insert     = [
                    'io_id'     => $io_id,
                    'dr_gr_id'  => $dgr_id
                ];

                $this->dgr_model->insert_delivery_goods_reference($insert);

                $where        = ['dr_gr_id' => $dgr_id];
                $curr_detail  = [ $this->io_model->get_details_for_audit($table, $where) ];

                //Update the reference of the task and status ( w/other details )
                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $dgr_id, 
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => std_db_date_format($data['doc_dr_date']),
                    'actual_end_date'   => std_db_date_format($data['actual_placement_date'])  
                ]);

             
                $msg                 = $this->lang->line('data_saved');

                $doc_ref             = encrypt_id($dgr_id);   
            }
            else
            {   
                //Validates if there's a last dr that's already existing
                if($fields['last_dr_flag'] == ENUM_YES && ! EMPTY($other_drs) && $other_drs['dr_gr_id'] != $dgr_id)
                    throw new Exception($this->lang->line('err_last_dr_exist'));


                if(EMPTY($dr_form)){
                    if(empty($data['doc_dr'])){
                        throw new Exception('DR File is required.');
                    }
                }  

                $audit_action = [AUDIT_UPDATE];
                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' DOC DR');

                $where        = ['dr_gr_id' => $dgr_id];

                $this->dgr_model->update_delivery_goods_receipt($where, $fields);

                $where        = ['dr_gr_id' => $dgr_id];
                $curr_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
 

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $dgr_id,
                    'actual_start_date' => std_db_date_format($data['doc_dr_date']),
                    'actual_end_date'   => std_db_date_format($data['actual_placement_date'])  
                ]);

             
                $msg                    = $this->lang->line('data_updated');

                $doc_ref             = encrypt_id($dgr_id); 
            }

            $this->audit_trail->log_audit_trail($activity, $this->tab_module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag = SUCCESS;
            
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
            'flag'  => $flag,
            'msg'   => $msg,
           /*  'task'   => ISSET($response) ? $response : '',
            'status' => ISSET($status) ? $status : '', */
            'doc_ref'=> $doc_ref
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            if( ! $this->permissions[ACTION_EDIT]) throw new Exception($this->lang->line('err_unauthorized_access'));
            
            $params = get_params();

            //Filters the data inputted by the user 
			$params = $this->set_filter( $params )
            ->filter_string('doc_dr_number')
            ->filter_date('doc_dr_date')
            ->filter_date('actual_placement_date')
            ->filter_string('doc_dr')
            /*->filter_string('doc_dr_sysfile') */
            ->filter_string('doc_dr_deliveries')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'doc_dr_number'         => 'DOC DR Number',
                'doc_dr_date'           => 'DOC DR Date.',
                'actual_placement_date' => 'Actual Placement Date.'
                //'doc_dr'                => 'DOC DR File.'
            /*    'doc_dr_sysfile'        => 'DOC DR System File.' */
            ];

            $constraints['doc_dr_number'] = [
                'data_type'         => 'string',
                'name'              => 'DOC DR Number.'
            ];

            $constraints['doc_dr_date']	= [
                'data_type'			=> 'date',
                'name'				=> 'DOC DR Date.'
            ];
    
            $constraints['actual_placement_date'] = [
                'data_type'			=> 'date',
                'name'				=> 'Actual Placement Date'
            ];
            
            $constraints['doc_dr']	= [
                'data_type'			=> 'string',
                'name'				=> 'DOC DR File'
            ];
    /*
            $constraints['doc_dr_sysfile'] = [
                'data_type'         => 'string',
                'name'              => 'DOC DR System File'
            ];
 */
            $constraints['task_id'] = [
                'data_type'   => 'db_value',
                'name'        => 'ETD',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'pria_task_id',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
            ];

            if( ISSET($params['doc_dr_deliveries']) )
            {   
                $constraints['doc_dr_deliveries']	= array(
                    'data_type'			=> 'enum',
                    'name'				=> 'Last DOC Delivery'
                );
            }

            if( ISSET($params['task_status']) )
            {
                $constraints['task_status'] = [
                    'data_type'   => 'db_value',
                    'name'        => 'Task Status',
                    'field'       => 'COUNT( 1 ) as check_row',
                    'check_field' => 'check_row',
                    'where'       => 'action_id',
                    'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
                ];
            }
            

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            $start_date = strtotime($params['doc_dr_date']);
            $end_date   = strtotime($params['actual_placement_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            /* if($start_date != $end_date)
                throw new Exception('DOC DR date must be the same as actual placement date.');
 */
            if($start_date > $end_date)
                throw new Exception('DOC DR date must not be greater than actual placement date.');

            if($start_date > $curr_date)
                throw new Exception('DOC DR date must not be greater than current date.');
                
            if($end_date > $curr_date)
                throw new Exception('actual placement date must not be greater than current date.');

            if (preg_match('#[0-9]#',$params['doc_dr_number'])){

            }else{
                throw new Exception('DOC DR number must have a numeric character.');
            }

            /* Validate constraints */
            $data = $this->validate_inputs($params, $constraints);

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