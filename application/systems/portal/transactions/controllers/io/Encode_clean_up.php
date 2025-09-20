<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encode_clean_up extends Task_Controller 
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

        $this->load->library('Notify');
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
            

            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $io_id          = $this->task_details['task_reference_id'];
            
            if( ! EMPTY($io_id))
            {
               /*  $fields  = 'actual_clean_up_date, harvested_heads_num, delivered_feeds_num, feeds_used_num, harvested_kilos_num';
                $this->task_view_data['clean_up_details'] = $this->dgr_model->get_delivery_goods_receipt_io(['dgr_id' => $dgr_id], $fields, DR_CLEANUP, $io['io_id']); */

             /*    $fields = [
                    'actual_clean_up_date',
                    'harvested_heads_num',
                    'delivered_feeds_num',
                    'feeds_used_num',
                    'harvested_kilos_num',
                ];
                
                $this->task_view_data['clean_up_details']= $this->io_model->get_internal_order(['io_id' => $io_id], $fields); */


                $this->task_view_data['clean_up_details'] = $io;
    
            }
            
            //Gets MEDVAC DR numbers
            $this->task_view_data['doc_medvac_numbers']    = $this->dgr_model->get_gr_dr_nums(DR_MEDVAC, 'dr_num', $io['io_id']);

            //Gets DOC GR Numbers
            $this->task_view_data['doc_gr_numbers']        = $this->dgr_model->get_gr_dr_nums(DR_DOCDR, 'gr_num', $io['io_id']);
            
            //print_var_export($this->task_view_data); die;

            //Load the content of the task
            $this->data['page_title']       = 'Internal Order: '.$io['io_num'];
            $this->task_page                = '/encode_clean_up_report';

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
            $flag         = ERROR;
            $data         = $this->_validate();
            $now          = date(FORMAT_DB_DATE);
            
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_INTERNAL_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            //Set initial fields that is present for both insert and update action
            $fields       = [
                'actual_clean_up_date'  => std_db_date_format($data['actual_clean_up_date']),
                'harvested_heads_num'   => $data['harvested_head'],  
                'harvested_kilos_num'   => $data['harvested_kilos'],  
                'delivered_feeds_num'   => $data['feeds_delivered'],
                'feeds_used_num'        => $data['feeds_used']
            ];
            
            $task_id        = $data['task_id'];

            $task_status_id = $data['task_status'];

            $task_details   = $this->tm_model->get_task_details($task_id);

            $io_id          = $task_details['task_reference_id'];

            $where          = ['io_id' => $io_id];

            //Start the db transaction                
            Portal_Model::beginTransaction();
            //If reference id is empty action will be insert
            if( EMPTY($io_id))
            {
                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT];
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Clean-up report');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);
                $io_details   = $this->io_model->get_internal_order(['io_id' => $io_id], ['vendor_code']);

                //Update internal orders
                $where = array('io_id' => $io_id);
                $this->io_model->update_internal_order($where, $fields); 

                //Set up fields that will be inserted 
              /*
                **This block of code is candidate for deletion   
                $insert       = [
                    'account_group_code' => AG_CONTRACT_GROWERS,
                    'vendor_code'        => $io_details['vendor_code'],
                    'pria_task_id'       => $task_id,
                    'dr_type_code'       => DR_CLEANUP,
                    'created_by'         => $this->session->user_id,
                    'created_date'       => $now
                ];

                $dgr_id     = $this->dgr_model->insert_delivery_goods_receipt($insert);     
            
                //Set up fields that will be inserted
                $insert     = [
                    'io_id'     => $io_id,
                    'dr_gr_id'  => $dgr_id
                ];

                $this->dgr_model->insert_delivery_goods_reference($insert); 
                
                */


                $where        = ['io_id' => $io_id];
                $curr_detail  = [ $this->io_model->get_details_for_audit($table, $where) ];

                //Update the reference of the task
                //$this->pria_workflow->tag_task_ongoing($task_id, $dgr_id)
                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $io_id,
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => date(FORMAT_DB_DATETIME),
                    'actual_end_date'   => std_db_date_format($data['actual_clean_up_date'])
                ]);

 
            }
            else
            {   
                $audit_action = [AUDIT_UPDATE];
                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Clean-up report');

                $io_id        = $this->tm_model->get_task_workflow_reference_id($task_id);

                //Update internal orders
                $where = array('io_id' => $io_id);
                $this->io_model->update_internal_order($where, $fields);

            /*  
                **This block of code is candidate for deletion

                $where      = ['dr_gr_id' => $dgr_id];
                $fields     = array('dr_recipient_id' => NULL);
                $this->dgr_model->update_delivery_goods_receipt($where, $fields);
                 */
                $curr_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];

                $this->tag_task($task_id, $task_status_id, [
                    'actual_end_date'   => std_db_date_format($data['actual_clean_up_date'])
                ]);

                
            }

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
            'msg'   => $msg
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
            ->filter_date('actual_clean_up_date')
            ->filter_number('harvested_head')
            ->filter_float('harvested_kilos')
            ->filter_number('feeds_delivered')
            ->filter_number('feeds_used')
            ->filter();
            
            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'actual_clean_up_date'  => 'Actual Clean Up Date',
                'harvested_head'        => 'Harvested Head',
                'harvested_kilos'       => 'Harvested Kilos',
                'feeds_delivered'       => 'Feeds Delivery',
                'feeds_used'            => 'Feeds Used'
            ];

            $constraints['actual_clean_up_date'] = [
                'data_type'         => 'date',
                'name'              => 'Actual Clean Up Date'
            ];


            $constraints['harvested_kilos']    = [
                'data_type'         => 'amount',
                'name'              => 'Harvest '
            ];

            $constraints['harvested_head']    = [
                'data_type'         => 'number',
                'name'              => 'Harvest Head'
            ];

            $constraints['feeds_delivered']	= [
                'data_type'			=> 'number',
                'name'				=> 'Feeds Delivered'
            ];

            $constraints['feeds_used'] = [
                'data_type'         => 'number',
                'name'              => 'Feeds Used'
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

            $end_date   = strtotime($params['actual_clean_up_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('Actual clean up date must not be greater than current date.');

            if($params['feeds_used'] > $params['feeds_delivered'])
                throw new Exception('Feeds used must not be greater than feeds delivered.');

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