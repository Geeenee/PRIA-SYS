<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doc_gr extends Task_Controller 
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

            $this->task_resources['load_css'][] = CSS_SELECTIZE;
            $this->task_resources['load_js'][]  = JS_SELECTIZE;
            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;

            //Get IO details
            $fields     = ['io_num'];
            $where      = ['io_id' => $this->task_details['reference_id']];                      
            $io         = $this->io_model->get_internal_order($where, $fields);


            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $dgr_id               = $this->task_details['task_reference_id'];
            
            if( ! EMPTY($dgr_id))
            {
                // Change request 12.21.22 Starts Here
                // $fields  = [ 'dr_num', 'gr_num', 'gr_date' ,'dr_reference'];
                $fields  = [ 'dr_num', 'gr_num', 'gr_date' ,'dr_reference', 'gross_placement', 'net_placement'];
                // Change request 12.21.22 Ends Here

                //$doc_gr_data['doc_gr_details'] = $this->dgr_model->get_delivery_goods_receipt(['dr_gr_id' => $dgr_id], $fields);
                $doc_gr_details                         = $this->dgr_model->get_delivery_goods_receipt(['dr_gr_id' => $dgr_id], $fields);
                $this->task_view_data['doc_gr_details'] = $doc_gr_details;
                
                if($doc_gr_details){
                    $fields  = [ '*' ];
                    $this->task_view_data['doc_dr_details'] = $this->dgr_model->get_delivery_goods_receipt(['dr_gr_id' => $doc_gr_details['dr_reference']], $fields);
                }   
            }
            
         /*    // $where         = array('drg_code' => DR_TYPE, 'dr_gr_id' => $this->task_details['reference_num']);
            // $deliveries    = $this->dgr_model->get_delivery_goods_receipts($where);
            $deliveries    = $this->dgr_model->get_delivery_goods_receipt_ref(DR_TYPE,$this->task_details['reference_num']); 
            
            $this->task_view_data['deliveries'] = $deliveries; */

            $docdr      = $this->pwm_model->get_task_predecessors(['pria_task_id' => $task_id], ['pre_pria_task_id']);
            $docdr_ptid = $docdr[0]['pre_pria_task_id'];


            $docdr_dets = $this->dgr_model->get_delivery_goods_receipt(['pria_task_id' => $docdr_ptid], ['dr_num']);

            $this->task_view_data['doc_dr_no']  = $docdr_dets['dr_num'];

            //print_var_export($docdr_dets);  die;
           
            //Load the content of the task
            $this->data['page_title']           = 'Internal Order: '.$io['io_num'];
            $this->task_page                    = '/upload_doc_gr';

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
            $flag           = ERROR;
            $data           = $this->_validate();
            $now            = date(FORMAT_DB_DATE);

            //Initial audit trail config
            $table          = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema   = [DB_PORTAL];
            $audit_table    = [$table];
            $task_id        = $data['task_id'];

            $docdr          = $this->pwm_model->get_task_predecessors(['pria_task_id' => $task_id], ['pre_pria_task_id']);
            $docdr_ptid     = $docdr[0]['pre_pria_task_id'];
            $docdr_dets     = $this->dgr_model->get_delivery_goods_receipt(['pria_task_id' => $docdr_ptid], ['dr_gr_id', 'last_dr_flag']);

            $dgr_id          = $docdr_dets['dr_gr_id'];

            //Set initial fields that is present for both insert and update action
            // Change request 12.21.22 Starts Here
            // $fields         = [
            //     'dr_reference'    => $docdr_dets['dr_gr_id'],
            //     'gr_num'          => $data['doc_gr_no'],
            //     'gr_date'         => std_db_date_format($data['doc_gr_date']),
            //     'last_dr_flag'    => $docdr_dets['last_dr_flag'],
            //     'gr_pria_task_id' => $task_id
            // ];

            $fields         = [
                'dr_reference'    => $docdr_dets['dr_gr_id'],
                'gr_num'          => $data['doc_gr_no'],
                'gr_date'         => std_db_date_format($data['doc_gr_date']),
                'last_dr_flag'    => $docdr_dets['last_dr_flag'],
                'gr_pria_task_id' => $task_id,

                'gross_placement' => $data['gross_placement'],
                'net_placement'   => $data['net_placement'],
            ];
            // Change request 12.21.22 Ends Here

            $task_status_id = $data['task_status'];
            $task_details   = $this->tm_model->get_task_details($task_id);
          
            $where          = ['dr_gr_id' => $dgr_id];

            //Start the db transaction
            Portal_Model::beginTransaction();

            $audit_action   = [AUDIT_UPDATE];
            $prev_detail    = [ $this->io_model->get_details_for_audit( $table, $where) ];
            $activity       = sprintf($this->lang->line('audit_trail_update'), ' DOC GR');

            $this->dgr_model->update_delivery_goods_receipt($where, $fields);

            $curr_detail    = [ $this->io_model->get_details_for_audit( $table, $where) ];

            $this->tag_task($task_id, $task_status_id, [
                'reference'         => $dgr_id,
                'actual_start_date' => std_db_date_format($data['doc_gr_date']),
                'actual_end_date'   => std_db_date_format($data['doc_gr_date'])
            ]);


/*    //If reference id is empty action will be insert
            if( EMPTY($dgr_id))
            {
                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT];	
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' DOC GR');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);
                $io_details   = $this->io_model->get_internal_order(['io_id' => $io_id], ['vendor_code']);

                //Set up fields that will be inserted 
                $insert     = array_merge([
                    'account_group_code' => AG_CONTRACT_GROWERS,
                    'vendor_code'        => $io_details['vendor_code'],
                    'pria_task_id'       => $task_id,
                    'dr_type_code'       => DR_DOCGR,
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

                //Update the reference of the task
                //$this->pria_workflow->tag_task_ongoing($task_id, $dgr_id);
                
                //Update the reference of the task and status ( w/other details )
                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $dgr_id,
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => std_db_date_format($data['doc_gr_date']),
                    'actual_end_date'   => std_db_date_format($data['doc_gr_date'])
                ]);

//                $response['actor']   = $ongoing['actor_name'];                
 //               $status              = TASK_STATUS_ONGOING;
            }
            else
            {   
                $audit_action = [AUDIT_UPDATE];
                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' DOC GR');

                $this->dgr_model->update_delivery_goods_receipt($where, $fields);

                $curr_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $dgr_id,
                    'actual_start_date' => std_db_date_format($data['doc_gr_date']),
                    'actual_end_date'   => std_db_date_format($data['doc_gr_date'])
                ]);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;

            }
 */
            $this->audit_trail->log_audit_trail($activity, $this->tab_module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg  = $this->lang->line('data_saved');
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
           /*  'task'   => $response,
            'status' => $status */
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            if( ! $this->permissions[ACTION_EDIT]) throw new Exception($this->lang->line('err_unauthorized_access'));
            
            $params = get_params();
            
            // Change request 12.21.22 Starts Here

            //Filters the data inputted by the user 
			// $params = $this->set_filter( $params )
            // ->filter_string('doc_gr_no')
            // ->filter_date('doc_gr_date')
            // ->filter();

			$params = $this->set_filter( $params )
            ->filter_string('doc_gr_no')
            ->filter_date('doc_gr_date')
            ->filter_number('gross_placement')
            ->filter_number('net_placement')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            // $required = [
            //     'doc_gr_no'     => 'DOC GR No.',
            //     'doc_gr_date'   => 'DOC GR Date.',
            // ];

            $required = [
                'doc_gr_no'       => 'DOC GR No.',
                'doc_gr_date'     => 'DOC GR Date.',
                'gross_placement' => 'Gross Placement',
                'net_placement'   => 'Net Placement',
            ];

            $constraints['gross_placement']    = [
                'data_type'         => 'number',
                'name'              => 'Gross Placement'
            ];

            $constraints['net_placement']    = [
                'data_type'         => 'number',
                'name'              => 'Net Placement'
            ];
            // Change request 12.21.22 Ends Here


            $constraints['doc_gr_no']	= [
                'data_type'			=> 'string',
                'name'				=> 'DOC GR No.'
            ];

   
            $constraints['doc_gr_date'] = [
                'data_type'			=> 'date',
                'name'				=> 'DOC GR Date'
            ];

            $constraints['task_id'] = [
                'data_type'   => 'db_value',
                'name'        => 'ETD',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'pria_task_id',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
            ];


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

            $end_date   = strtotime($params['doc_gr_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('DOC GR date must not be greater than current date.');

            if (preg_match('#[0-9]#',$params['doc_gr_no'])){

            }else{
                throw new Exception('DOC GR Number must have a numeric character.');
            }

            /* Validate constraints */
            return $this->validate_inputs($params, $constraints);
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