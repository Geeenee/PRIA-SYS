<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transmit_soa extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        // $this->module_code      = MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA;
        $this->folder           = FOLDER_SOA;
        
        $this->load->model($this->folder.'/Soa_model', 'soa_model'); 
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');

        // $this->permissions      = check_permission($this->module_code);
        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{
            $params    = get_params(TRUE, TRUE);

            $task_id                = base64_url_decode($params['t'] );

            //get task details
            $task = $this->tm_model->get_task_details($task_id);

            $task_workflow = $this->tm_model->get_task_workflow($task_id);

            //get account group
            $ag_code = $task['account_group_code'];
            
            //get core workflow id
            $workflow_id = $task_workflow['workflow_id'];

            //get tab module details
            $tab_module_details = $this->tm_model->get_tab_module(
                ['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC, 'transaction_tab' => TRANS_TAB_SOA] );
            
            // $this->module_code      = base64_url_decode($params['mid'] );
            // $this->permissions      = check_permission($this->module_code);
            
            //  if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $tab_module_code = $tab_module_details['tab_module_code'];
      
            
            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];

            $this->tab_module_code  = $tab_module_code;


            $this->permissions  = check_permission($tab_module_code);
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));
            
            $this->_initialize_task($task_id);

            // $common    = $this->_get_common_task_resource($task_id);
            // $resources = $common['resources'];
            // $data      = $common['data'];
            // $access    = $common['access'];
            // $task      = $data['task'];

            //Set up resources to be used
            $this->task_resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $this->task_resources['load_js'][]     = $this->module_task_js;
            $this->task_resources['load_js'][]     = JS_DATETIMEPICKER;
            $this->task_resources['load_css'][]    = CSS_DATETIMEPICKER;

            // $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];

            //Get SOA details
            $fields         = ['*'];
            $where          = ['soa_id' => $this->task_details['reference_id']];
            $soa_details    = $this->soa_model->get_soa($where, $fields);

            $soa_id = $soa_details['soa_id'];

            $fields             = ['*'];
           // $where              = ['soa_id' => $this->task_details['reference_id']];
            $soa_transmittals   = $this->soa_model->get_soa_transmittal_by_soa_id($this->task_details['reference_id'], $fields);

            if( ! EMPTY($soa_transmittals[0]))
            {
                $this->task_view_data['soa_transmittals'] = $soa_transmittals[0];
                
            }

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.   
            // $sub_nav_left_config     = [
            //     'title'         => 'SOA',
            //     'placeholder'   => 'Search SOA No.',
            //     'data'          => []
            // ];

            // $data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config); 

            // $data['enc_task_id']  = encrypt_id($task_id);

            // $soa_data = ['view' => $access['view'], 'class_label' => $access['class_label']];
            // $soa_data['soa_details'] = $soa_details;

            //Load the content of the task
            $this->data['page_title']       = 'SOA Number: '.$soa_details['soa_num'];
            $this->task_page                = '/transmit_soa';

            $this->_load_task_view();
            //$this->template->load($this->tpl_task_container, $data, $resources, Portal_Controller::$system);
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
            $table        = Portal_Model::PORTAL_TABLE_SOA;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];
            $task_details = $this->tm_model->get_task_details($task_id);

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];

            $document_type_det  = $this->soa_model->get_specific_task_document_type($where, $field_select,array(), FALSE);
            $document_type_code = $document_type_det['document_type_code'];

            //Get SOA details   
            $fields         = array('*');
            $where          = array('soa_id' => $task_details['reference_id']);            
            $soa_details    = $this->soa_model->get_soa($where, $fields);

            //Get SOA details   
            $fields         = array('*');
            $where          = array('soa_id' => $task_details['reference_id']);            
            //$soa_trans      = $this->soa_model->get_soa_transmittal($where, $fields);
            $soa_trans      = $this->soa_model->get_soa_transmittal_by_soa_id($task_details['reference_id'], $fields);

            //Start the db transaction                
            Portal_Model::beginTransaction();   
            //If reference id is empty action will be update

            if(EMPTY($soa_trans))
            {
                $audit_action = [AUDIT_INSERT];

                $prev_detail  = [ $this->soa_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' SOA report');
                $soa_id       = $this->tm_model->get_task_workflow_reference_id($task_id);

                //insert to transmittal table
                $insert_trans = array(
                    'transmittal_date'      => std_db_date_format($data['transmittal_date']),
                    'courier_waybill_num'   => $data['courier'],
                    'created_by'            => $this->session->user_id,
                    'created_date'          => date(FORMAT_DB_DATE)
                );

                $transmittal_id = $this->soa_model->insert_transmittals($insert_trans);
                //ends

                //insert to soa transmittal table
                $insert_soa_trans = array(
                    'soa_id'            => $soa_id,
                    'transmittal_id'    => $transmittal_id
                );

                $this->soa_model->insert_soa_transmittals($insert_soa_trans);
                //ends

                $where        = ['soa_id' => $soa_id];
                $curr_detail  = [ $this->soa_model->get_details_for_audit( $table, $where) ];

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $soa_id]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $soa_id
                ], NULL, $soa_details['recipient_id']);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;
                $msg                 = $this->lang->line('data_saved');
            }else{

                $audit_action = [AUDIT_INSERT];

                $prev_detail  = [ $this->soa_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' SOA report');
                $soa_id       = $this->tm_model->get_task_workflow_reference_id($task_id);

                //Update soa
                $where  = array(
                    'transmittal_id'        => $soa_trans[0]['transmittal_id']
                );

                $update_trans = array(
                    'transmittal_date'      => std_db_date_format($data['transmittal_date']),
                    'courier_waybill_num'   => $data['courier'],
                    'modified_by'           => $this->session->user_id,
                    'modified_date'         => date(FORMAT_DB_DATE)
                );

                $this->soa_model->update_soa_transmittals($where, $update_trans);

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $soa_id]);

                $this->tag_task($task_id, $task_status_id, [
                    'reference'         => $soa_id
                ]);

                // $response['actor']   = $ongoing['actor_name'];
                // $status              = TASK_STATUS_ONGOING;
                $msg                 = $this->lang->line('data_updated');
            }

            //$this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

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
            
            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_date('transmittal_date')
            ->filter_string('courier')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'transmittal_date'  => 'Transmittal Date',
                'courier'           => 'Courier'
            ];

            $constraints['transmittal_date']    = [
                'data_type'         => 'date',
                'name'              => 'Transmittal Date'
            ];

            $constraints['courier']    = [
                'data_type'         => 'string',
                'name'              => 'Courier'
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

            $transmittal_date   = strtotime($data['transmittal_date']);
            $curr_date        = strtotime(date(FORMAT_DB_DATE));

            if($transmittal_date > $curr_date)
                throw new Exception('Transmittal date must not be greater than current date.');

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