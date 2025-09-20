<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encode_good_receipt extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        // $this->module_code      = MODULE_PORTAL_TRANS_CG_IO;
        $this->folder           = FOLDER_DELIVERY_GOODS;
        $this->po_folder        = FOLDER_PURCHASE_ORDERS;
        
        $this->load->model($this->folder.'/Delivery_goods_model', 'dr_model');
        $this->load->model($this->po_folder.'/po_model', 'po_model');
        
        // $this->permissions      = check_permission($this->module_code);

        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{  
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

            $this->tab_module_code      = $tab_module_code;
            
            $this->permissions  = check_permission($tab_module_code);
            
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_SELECTIZE;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_SELECTIZE;

            //Get PO details
            $fields         = ['*'];
            $where          = ['po_id' => $this->task_details['reference_id']];
            $po_details     = $this->po_model->get_po($where, $fields);

            $this->task_view_data['po_details'] = $po_details;

            $where          = ['po_id' => $po_details['po_id']];
            $dr_ref_details = $this->dr_model->get_delivery_goods_reference($where);
            $dgr_id         = $task_reference_info;

            //Get DR details
            if($dgr_id){
                $fields             = ['*'];
                $where              = ['dr_gr_id' => $dgr_id];
                $dgr_details        = $this->dr_model->get_delivery_goods_receipt($where, $fields);
                
                $this->task_view_data['delivery_receipt_details'] = $dgr_details;
            }

            //Load the content of the task
            $this->data['page_title']       = 'Purchase order: '.$po_details['po_num'];
            $this->task_page                = '/encode_good_receipt';

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
            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            $data         = $this->_validate();

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

            //Start the db transaction                
            Portal_Model::beginTransaction();

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

            $this->module_code = $tab_module_code;

            $po_id          = $this->tm_model->get_task_workflow_reference_id($task_id);
            $po_details     = $this->po_model->get_po(['po_id' => $po_id], ['*']);
            
            $where          = ['po_id' => $po_id];
            $dr_ref_details = $this->dr_model->get_delivery_goods_reference($where);

            if($dr_ref_details['dr_gr_id']){
                $fields             = ['*'];
                $where              = ['dr_gr_id' => $task_reference_info];
                $dgr_details        = $this->dr_model->get_delivery_goods_receipt($where, $fields);
            }

            //If reference id is empty action will be insert            
            $audit_action = [AUDIT_UPDATE];

            $where        = array('dr_gr_id' => $task_reference_info);
            $prev_detail  = [ $this->dr_model->get_details_for_audit( $table, $where) ];
            $activity     = sprintf($this->lang->line('audit_trail_update'), ' Delivery Goods Receipt report');

            //Set up fields that will be inserted 
            $update       = [
                'gr_num'            => $data['gr_num'],
                'gr_date'           => std_db_date_format($data['gr_date']),
                'modified_by'       => $this->session->user_id,
                'modified_date'     => $now
            ];

            //Update Delivery Goods Receipt
            $where = array('dr_gr_id' => $task_reference_info);
            $this->dr_model->update_delivery_goods_receipt($where, $update);
            
            $curr_detail         = [ $this->dr_model->get_details_for_audit( $table, $where) ];

            // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
            //     'start_date'        => date(FORMAT_DB_DATETIME),
            //     'actual_start_date' => date(FORMAT_DB_DATETIME),
            //     'actual_end_date'   => std_db_date_format($data['gr_date'])
            // ]);

            $this->tag_task($task_id, $task_status_id, [
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME),
                'actual_end_date'   => std_db_date_format($data['gr_date'])
            ]);

            // $response['actor']   = $ongoing['actor_name'];                
            $status              = TASK_STATUS_ONGOING;

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg  = $this->lang->line('data_updated');
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
            $params = get_params();

            //Filters the data inputted by the user 
			$params = $this->set_filter( $params )
            ->filter_string('gr_num')
            ->filter_date('gr_date')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'gr_num'    => 'GR Number',
                'gr_date'   => 'GR Date'
            ];

            $constraints['gr_num'] = [
                'data_type'         => 'string',
                'name'              => 'GR Number'
            ];

            $constraints['gr_date']    = [
                'data_type'         => 'date',
                'name'              => 'GR Date'
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

            $end_date   = strtotime($params['gr_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('GR date must not be greater than current date.');

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            if (preg_match('#[0-9]#',$data['gr_num'])){

            }else{
                throw new Exception('GR Number must have a numeric character.');
            }

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