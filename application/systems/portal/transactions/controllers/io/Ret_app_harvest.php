<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ret_app_harvest extends Task_Controller 
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

            $this->task_details['task_reference_id'] = $io_id;

            if( ! EMPTY($io['harvest_rep_submit_date']))
            {
                $fields  = ['harvest_rep_submit_date', 'created_date'];

                $this->task_view_data['harvest_details'] = $this->io_model->get_internal_order(['io_id' => $io_id], $fields);
            }   

            //Load the content of the task
            $this->data['page_title']       = 'Internal Order: '.$io['io_num'];
            $this->task_page                = '/ret_app_harvest_report';

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
            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            //Set initial fields that is present for both insert and update action
            $fields       = [
                'actual_clean_up_date'  => std_db_date_format($data['actual_clean_up_date']),
                'harvested_heads_num'   => $data['harvested_head'],  
                'delivered_feeds_num'   => $data['feeds_delivered'],
                'feeds_used_num'        => $data['feeds_used']
            ];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

            $task_details = $this->tm_model->get_task_details($task_id);

            $dgr_id       = $task_details['reference'];

            $where        = ['io_id' => $dgr_id];
            //Start the db transaction                
            Portal_Model::beginTransaction();	
            //If reference id is empty action will be insert
            if( EMPTY($dgr_id))
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
                
                $where        = ['dr_gr_id' => $dgr_id];
                $curr_detail  = [ $this->io_model->get_details_for_audit($table, $where) ];

                //Update the reference of the task
                // $this->pria_workflow->tag_task_ongoing($task_id, $dgr_id);

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                //     'reference'         => $dgr_id,
                //     'start_date'        => date(FORMAT_DB_DATETIME),
                //     'actual_start_date' => date(FORMAT_DB_DATETIME)
                // ]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $dgr_id,
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => date(FORMAT_DB_DATETIME)
                ]);

                // $response['actor']   = $ongoing['actor_name'];                
                // $status              = TASK_STATUS_ONGOING;
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

                $this->dgr_model->update_delivery_goods_receipt($where, $fields);
                
                $curr_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
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
            // 'task'   => $response,
            // 'status' => $status
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
            ->filter_string('harvested_head')
            ->filter_string('feeds_delivered')
            ->filter_string('feeds_used')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'actual_clean_up_date'  => 'Actual Clean Up Date',
                'harvested_head'        => 'Harvested Head',
                'feeds_delivered'       => 'Feeds Delivery',
                'feeds_used'            => 'Feeds Used'
            ];

            $constraints['actual_clean_up_date'] = [
                'data_type'         => 'date',
                'name'              => 'Actual Clean Up Date'
            ];

            $constraints['harvested_head']    = [
                'data_type'         => 'string',
                'name'              => 'Harvest Head'
            ];

            $constraints['feeds_delivered']	= [
                'data_type'			=> 'string',
                'name'				=> 'Feeds Delivered'
            ];

            $constraints['feeds_used'] = [
                'data_type'         => 'string',
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