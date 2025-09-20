<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encode_flock_history extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
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

            $task_id   = base64_url_decode($params['t'] );

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
            $io_id = $this->task_details['task_reference_id'];
            
            if( ! EMPTY($io['fhr_document_num']))
            {

            // Change request 12.21.22 Starts Here
            // $fields  = ['fhr_document_num', 'fhr_submit_date', 'io_id'];
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

                $this->task_view_data['flock_history_details'] = $this->io_model->get_internal_order(['io_id' => $io_id], $fields);

            }      

            //Load the content of the task
            $this->data['page_title'] = 'Internal Order: '.$io['io_num'];
            $this->task_page          = '/encode_flock_history';

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

            $doc_ref      = '';
            $response     = [];
            $now          = date(FORMAT_DB_DATE);

            $data         = $this->_validate();
            
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
                        
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

            $task_details = $this->tm_model->get_task_details($task_id);

            $where          = ['document_type_code' => DOC_TYPE_FHR, 'reference' => $task_details['reference_id']];
            $fhr_form       = $this->document_model->get_document($where);

            //Get IO details
            $fields     = ['*'];
            $where      = ['io_id' => $task_details['reference_id']];
            $io         = $this->io_model->get_internal_order($where, $fields);

            //Set initial fields that is present for both insert and update action

            // Change request 12.21.22 Starts Here

            // $fields     = [
            //     'fhr_document_num'  => $data['fhr_document_num'],
            //     'fhr_submit_date'   => std_db_date_format($data['date_submitted'])
            // ];

            $fields     = [
                'fhr_document_num'  => $data['fhr_document_num'],
                'fhr_submit_date'   => std_db_date_format($data['date_submitted']),

                'pre_placement'        => ISSET($data['pre_placement']) ? $data['pre_placement'] : NULL,
                'brooding_audit'       => ISSET($data['brooding_audit']) ? $data['brooding_audit'] : NULL,
                'biosecurity_audit'    => ISSET($data['biosecurity_audit']) ? $data['biosecurity_audit'] : NULL,
                'feeds_retrieval'      => $data['feeds_retrieval'],
                'fmis_transacted_flag' => ISSET($data['feeds_retrieval']) ? $data['fmis_transacted_flag'] : 'N',
            ];
            
            
            // Change request 12.21.22 Ends Here

            //Start the db transaction                
            Portal_Model::beginTransaction();
            //If reference id is empty action will be insert

            if( EMPTY($io['fhr_document_num']))
            {

                //validate fhr file
                if(EMPTY($fhr_form)){
                    if(EMPTY($data['doc_fhr'])){
                        throw new Exception('Flock History Report File is required.');
                    }
                }

                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT]; 
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Flock history report ');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);
                $io_details   = $this->io_model->get_internal_order(['io_id' => $io_id], ['vendor_code']);

                //Update internal orders
                $where = array('io_id' => $io_id);

                $this->io_model->update_internal_order($where, $fields);

                //Set up fields that will be inserted 
                $where               = ['io_id' => $io_id];
                $curr_detail         = [ $this->io_model->get_details_for_audit($table, $where) ];
                
                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                //     'reference'         => $io_id,
                //     'start_date'        => date(FORMAT_DB_DATETIME),
                //     'actual_start_date' => date(FORMAT_DB_DATETIME)
                // ]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $io_id,
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => date(FORMAT_DB_DATETIME)
                ]);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;

                $doc_ref             = encrypt_id($io_id);
            }
            else
            {   
                $audit_action = [AUDIT_UPDATE];

                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Flock history report');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);                

                //validate fhr file
                if(EMPTY($fhr_form)){
                    if(EMPTY($data['doc_fhr'])){
                        throw new Exception('Flock History Report File is required.');
                    }
                }

                //Update internal orders
                $where  = array('io_id' => $io_id);

                // Change request 12.21.22 Starts Here
                // $update = array(
                //     'fhr_document_num'   => $data['fhr_document_num'],
                //     'fhr_submit_date'    => std_db_date_format($data['date_submitted']),
                // );
                $update = array(
                    'fhr_document_num'   => $data['fhr_document_num'],
                    'fhr_submit_date'    => std_db_date_format($data['date_submitted']),

                    'pre_placement'        => ISSET($data['pre_placement']) ? $data['pre_placement'] : NULL,
                    'brooding_audit'       => ISSET($data['brooding_audit']) ? $data['brooding_audit'] : NULL,
                    'biosecurity_audit'    => ISSET($data['biosecurity_audit']) ? $data['biosecurity_audit'] : NULL,
                    'feeds_retrieval'      => $data['feeds_retrieval'],
                    'fmis_transacted_flag' => ISSET($data['feeds_retrieval']) ? $data['fmis_transacted_flag'] : 'N',
                );
                // Change request 12.21.22 Ends Here

                $this->io_model->update_internal_order($where, $update);                

                $where              = ['io_id' => $io_id];
                $curr_detail        = [ $this->io_model->get_details_for_audit( $table, $where) ];

                // $ongoing            = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $io_id]);
                
                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $io_id
                ]);

                // $response['actor']  = $ongoing['actor_name'];
                // $status             = TASK_STATUS_ONGOING;
                
                $doc_ref             = encrypt_id($io_id);
            }
            
            $this->audit_trail->log_audit_trail($activity, $this->tab_module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

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
            'flag'    => $flag,
            'msg'     => $msg,
            // 'task'    => $response,
            // 'status'  => $status,
            'doc_ref' => $doc_ref
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            if( ! $this->permissions[ACTION_EDIT]) throw new Exception($this->lang->line('err_unauthorized_access'));
            
            $params = get_params();

            //Filters the data inputted by the user 


            // Change request 12.21.22 Starts Here

    //         $params = $this->set_filter( $params )
    //         ->filter_date('date_submitted')
    //         ->filter_string('fhr_document_num')
    // /*          ->filter_string('flock_history_file')
    //         ->filter_string('fhr_sysfile') */
    //         ->filter();


            $params = $this->set_filter( $params )
            ->filter_date('date_submitted')
            ->filter_string('fhr_document_num')
            ->filter_float('pre_placement')
            ->filter_float('brooding_audit')
            ->filter_float('biosecurity_audit')
            ->filter_number('feeds_retrieval')
            ->filter_string('fmis_transacted_flag')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);


            IF(!EMPTY($params['pre_placement'])){

                $acceptable = TRUE;

                $constraints['pre_placement'] = [
                    'data_type'         => 'amount',
                    'name'              => 'Pre placement'
                ];
            }

            IF(!EMPTY($params['brooding_audit'])){

                $acceptable = TRUE;

                $constraints['brooding_audit'] = [
                    'data_type'         => 'amount',
                    'name'              => 'Brooding audit'
                ];
            }

            IF(!EMPTY($params['biosecurity_audit'])){

                $acceptable = TRUE;

                $constraints['biosecurity_audit'] = [
                    'data_type'         => 'amount',
                    'name'              => 'Biosecurity Audit'
                ];
            }

            IF(!EMPTY($params['feeds_retrieval'])){

                $acceptable = TRUE;

                $constraints['feeds_retrieval']    = [
                    'data_type'         => 'number',
                    'name'              => 'Feeds Retrieval'
                ];

                $required = [
                    'date_submitted'       => 'Date Submitted',
                    'fhr_document_num'     => 'FHR Document No.',
                    'fmis_transacted_flag' => 'Transacted in FMIS?',
                ];


                $constraints['fmis_transacted_flag']	= array(
                    'data_type'			=> 'enum',
                    'name'				=> 'Transacted in FMIS',
                    'allowed_values' 	=> $this->enum_yes
                );
            } else {
                $required = [
                    'date_submitted'    => 'Date Submitted',
                    'fhr_document_num'  => 'FHR Document No.',
                ];
            }

            //Define the required fields.

            // $required = [
            //     'date_submitted'     => 'Date Submitted',
            //     'fhr_document_num'   => 'FHR Document No.'
            //     // 'doc_fhr'            => 'Flock History Report File'
            // ];


            $constraints['doc_harvest_sum'] = [
                'data_type'         => 'string',
                'name'              => 'Harvest Summary File'
            ];

            $constraints['doc_feeds_sum'] = [
                'data_type'         => 'string',
                'name'              => 'Feeds Summary File'
            ];

            $constraints['doc_audit_report'] = [
                'data_type'         => 'string',
                'name'              => 'Audit Report File'
            ];

            // Change request 12.21.22 Ends Here

            $constraints['date_submitted'] = [
                'data_type'         => 'date',
                'name'              => 'Date Submitted'
            ];

            $constraints['fhr_document_num']    = [
                'data_type'         => 'string',
                'name'              => 'FHR Document No.'
            ]; 

            $constraints['doc_fhr'] = [
                'data_type'         => 'string',
                'name'              => 'Flock History Report File'
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

            $end_date   = strtotime($params['date_submitted']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('FHR date must not be greater than current date.');

            if (preg_match('#[0-9]#',$params['fhr_document_num'])){

            }else{
                throw new Exception('FHR Document number must have a numeric character.');
            }

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