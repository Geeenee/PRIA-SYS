<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transmit_dr_duplicate extends Task_Controller 
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
            $tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC, 'transaction_tab' => TRANS_TAB_DR]);

            $tab_module_code = $tab_module_details['tab_module_code'];
            
            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];
            
            $this->tab_module_code = $tab_module_code;

            $this->permissions  = check_permission($tab_module_code);
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            //Set up resources to be used
            $this->task_resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $this->task_resources['load_js'][]     = $this->module_task_js;
            $this->task_resources['load_js'][]     = JS_DATETIMEPICKER;
            $this->task_resources['load_css'][]    = CSS_DATETIMEPICKER;

            // $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];


            //Starts
            //support center
            $this->task_view_data['support_centers'] = $this->dr_model->get_all_organizations();

            //Get PO details
            $fields             = ['*'];
            $where              = ['po_id' => $this->task_details['reference_id']];
            $po_details         = $this->po_model->get_po($where, $fields);
            $this->task_view_data['po_details'] = $po_details;

            //Get DR Reference   
            $fields         = array('*');
            $where          = array('po_id' => $this->task_details['reference_id'], 'dr_gr_id' => 'IS NOT NULL');
            $dr_trans       = $this->dr_model->get_dr_transmittal($where, $fields);

            //Get Goods Receipt details   
            $fields         = array('*');
            $where          = array('dr_gr_id' => $task_reference_info);
            $gr_details     = $this->dr_model->get_delivery_goods_receipt($where, $fields);

            $this->task_details['task_reference_id'] =  $gr_details['dr_gr_id'];
            
            $this->task_view_data['delivery_receipt_details'] = $gr_details;

            //Get DR Transmittal   
            $fields         = array('*');
            //$where          = array('dr_gr_id' => $gr_details['dr_gr_id']);

            $where          = array('transmittal_id' => 'IS NOT NULL', 'dr_gr_id' => $gr_details['dr_gr_id']);
            $transmittal    = $this->dr_model->get_dr_transmittal($where, $fields);

            if($transmittal['transmittal_id']){
                $fields                 = array('*');
                $where                  = array('transmittal_id' => $transmittal['transmittal_id']);
                $transmittal_details    = $this->dr_model->get_transmittal($where, $fields);

                $this->task_view_data['transmittal_details'] = $transmittal_details;
            }
            //Ends

            if($gr_details['site_id'])
                $this->task_view_data['site_info'] = $this->dr_model->get_site(['site_id' => $gr_details['site_id']]);

            if($gr_details['dr_recipient_id']){
                $this->task_view_data['recipient_id'] = $gr_details['dr_recipient_id'];
                $this->task_view_data['recipient_info'] = $this->dr_model->get_dr_doc_recipient(['user_id' => $gr_details['dr_recipient_id']]); 
            }
            
            //Load the content of the task
            $this->data['page_title']       = 'Purchase order: '.$po_details['po_num'];
            $this->task_page                = 'transmit_dr';

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
            $data         = $this->_validate();
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_TRANSMITTALS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

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

            $task_details = $this->tm_model->get_task_details($task_id);

            //Get PO details
            $fields             = ['*'];
            $where              = ['po_id' => $task_details['reference_id']];
            $po_details         = $this->po_model->get_po($where, $fields);

            //Get DR Reference   
            $fields         = array('*');
            $where          = array('po_id' => $task_details['reference_id'], 'dr_gr_id' => 'IS NOT NULL');            
            $dr_trans       = $this->dr_model->get_dr_transmittal($where, $fields);

            //Get Goods Receipt details   
            $fields         = array('*');
            $where          = array('dr_gr_id' => $task_reference_info);
            $gr_details     = $this->dr_model->get_delivery_goods_receipt($where, $fields);

            //Get DR Transmittal   
            $fields         = array('*');
            $where          = array('dr_gr_id' => $gr_details['dr_gr_id'],'transmittal_id' => 'IS NOT NULL');
            $transmittal    = $this->dr_model->get_dr_transmittal($where, $fields);

            //Start the db transaction                
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            if(EMPTY($transmittal['transmittal_id']))
            {
                $audit_action = [AUDIT_INSERT];
                $prev_detail  = array();
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Transmittal Details');

                //insert to dr transmittal table
                $insert = array(
                    'transmittal_date'      => std_db_date_format($data['transmittal_date']),
                    'courier_waybill_num'   => $data['waybill_number'],
                    'created_by'            => $this->session->user_id,
                    'created_date'          => date(FORMAT_DB_DATE)
                );

                $transmittal_id = $this->dr_model->insert_transmittals($insert);

                $insert_reference = array(
                    'transmittal_id'        => $transmittal_id,
                    'dr_gr_id'              => $task_reference_info
                );

                $this->dr_model->insert_dr_transmittals($insert_reference);

                $where        = array('transmittal_id' => $transmittal_id);
                $curr_detail  = [ $this->dr_model->get_details_for_audit( $table, $where) ];

                //Starts
                //Formula for deadline is every next FRIDAY from DR DATE 
                $n      = date("w", strtotime($gr_details['dr_date']));
                $tat    = 0;

                if($n > DV_FRIDAY){
                    $tat = 6;
                }else{
                    $tat = DV_FRIDAY - $n;
                }
                //Ends

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                //     'reference'         => $dr_trans['dr_gr_id'],
                //     'start_date'        => date(FORMAT_DB_DATETIME),
                //     'actual_start_date' => date(FORMAT_DB_DATETIME),
                //     'tat'               => $tat,
                //     'actual_end_date'   => std_db_date_format($data['transmittal_date'])
                // ]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $task_reference_info,
                    'start_date'        => date(FORMAT_DB_DATETIME),
                    'actual_start_date' => date(FORMAT_DB_DATETIME),
                    'tat'               => $tat,
                    'actual_end_date'   => std_db_date_format($data['transmittal_date'])
                ]);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;
                $msg                 = $this->lang->line('data_saved');
            }else{

                $audit_action = [AUDIT_UPDATE];

                $where        = array('transmittal_id' => $transmittal['transmittal_id']);
                $prev_detail  = [ $this->dr_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Transmittal Details');

                //Update DR
                $where  = array(
                    'transmittal_id'        => $transmittal['transmittal_id']
                );

                $update_trans = array(
                    'transmittal_date'      => std_db_date_format($data['transmittal_date']),
                    'courier_waybill_num'   => $data['waybill_number'],
                    'modified_by'           => $this->session->user_id,
                    'modified_date'         => date(FORMAT_DB_DATE)
                );

                $this->dr_model->update_dr_transmittals($where, $update_trans);

                $where        = ['transmittal_id' => $transmittal['transmittal_id']];
                $curr_detail  = [ $this->dr_model->get_details_for_audit( $table, $where) ];

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                //     'reference'         => $dr_trans['dr_gr_id'],
                //     'actual_end_date'   => std_db_date_format($data['transmittal_date'])
                // ]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $task_reference_info,
                    'actual_end_date'   => std_db_date_format($data['transmittal_date'])
                ]);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;
                $msg                 = $this->lang->line('data_updated');
            }

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();
            $flag = SUCCESS;
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
            
            // $params['transmittal_date'] = '20/10/2019';

            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_date('transmittal_date')
            ->filter_string('waybill_number')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'transmittal_date'  => 'Transmittal Date',
                'waybill_number'    => 'Waybill/Tracking Number'
            ];

            $constraints['transmittal_date']    = [
                'data_type'         => 'date',
                'name'              => 'Transmittal Date'
            ];

            $constraints['waybill_number']    = [
                'data_type'         => 'string',
                'name'              => 'Waybill/Tracking Number'
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

            $end_date   = strtotime($params['transmittal_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('transmittal date must not be greater than current date.');

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