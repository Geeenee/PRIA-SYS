<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_po extends Task_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_PURCHASE_ORDERS;

        $this->load->model($this->folder.'/Po_model', 'po_model');
        $this->load->model('Documents_model', 'document_model');
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');

        // $this->permissions      = check_permission($this->module_code);
        $this->path_task_views .= $this->folder;
    }

    public function index()
    {
        try
        {
            $params = get_params(TRUE, TRUE);

            $task_id = base64_url_decode($params['t'] );

            //get task details
            $task = $this->tm_model->get_task_details($task_id);

            //get tab module code
            //$tab_module_code    = base64_url_decode($params['mid']);

            $task_workflow = $this->tm_model->get_task_workflow($task_id);

            //get account group
            $ag_code = $task['account_group_code'];


            //get core workflow id
            $workflow_id = $task_workflow['workflow_id'];

            //get tab module details
            $tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC, 'transaction_tab' => TRANS_TAB_PO]);

            $tab_module_code = $tab_module_details['tab_module_code'];

            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];

            $this->tab_module_code  = $tab_module_code;

            $this->permissions  = check_permission($tab_module_code);
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_UPLOAD;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_UPLOAD;

            //Get PO details
            $fields         = ['*'];
            $where          = ['po_id' => $this->task_details['reference_id']];
            $po_details     = $this->po_model->get_po_details($this->task_details['reference_id']);

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;

            $resources['load_css'][]    = CSS_DATETIMEPICKER;
            $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];

            //Get Task Document Type
            $field_select       = ['*'];
            $where              = ['pria_task_id' => $task_id];
            $document_type_det  = $this->po_model->get_specific_task_document_type($where, $field_select, array(), FALSE);

            $this->task_view_data['po_details'] = $po_details;
            $this->task_view_data['account_group_code'] =  $ag_code;

            $this->task_view_data['require_release_date'] = TRUE;
            $this->task_view_data['require_receiving_num'] = TRUE;

            switch ($ag_code) {
                case AG_GOODS_BAVI:
                    $this->task_view_data['require_release_date'] = FALSE;
                    break;

                default:

                    break;
            }

            //get vendor info
            $ven_info = $this->po_model->get_all_vendors([ 'vendor_code' => $po_details['vendor_code']], array('*'), array(), FALSE );
            $this->task_view_data['vendor_info'] = $ven_info;

            //Load the content of the task
            $this->data['page_title']       = 'PO number: '.$po_details['po_num'];
            $this->task_page                = 'upload_po';

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
            $response     = [];
            $doc_ref      = '';
            $now          = date(FORMAT_DB_DATE);
            $data         = $this->_validate();

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $task_id        = $data['task_id'];
            $task_status_id = $data['task_status'];
            $task_details   = $data['task_details'];


            $where = array('reference' => $task_details['reference_id'], 'document_type_code' => DOC_TYPE_PO);
            $doc_info = $this->document_model->get_document($where);

            //Get module code
            // $module_code = $this->get_module_code_per_task_ag_code($task_details['account_group_code']);

            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            $po_id               = $this->tm_model->get_task_workflow_reference_id($task_id);

            if(EMPTY($doc_info)){
                if(empty($data['doc_po'])){
                    throw new Exception('PO Document File is required.');
                }
                $doc_ref             = encrypt_id($po_id);
            }

            if($task_details['account_group_code'] != AG_GOODS_BAVI AND EMPTY($data['po_released_date']))
            {
                throw new Exception('Released Date is required.');
            }

            $where   = array('po_id' => $po_id);
            $sub_val = [
                'receiving_report_num' => $data['receiving_report_num'],
                'po_released_date' => (!EMPTY($data['po_released_date']))? date(FORMAT_DB_DATE, strtotime($data['po_released_date'])): NULL
            ];
            $this->po_model->update_po($where, $sub_val);

            // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
            //     'reference'         => $po_id,
            //     'start_date'        => date(FORMAT_DB_DATETIME),
            //     'actual_start_date' => date(FORMAT_DB_DATETIME)
            // ]);

            $this->tag_task($task_id, $task_status_id, [
                'reference'         => $po_id,
                'start_date'        => date(FORMAT_DB_DATETIME),
                'actual_start_date' => date(FORMAT_DB_DATETIME)
            ]);

            // $response['actor']   = $ongoing['actor_name'];
            $status              = TASK_STATUS_ONGOING;
            // $msg                 = $this->lang->line('data_saved');

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
            'flag'  => $flag,
            'msg'   => $msg,
            'task'   => $response,
            'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }


    private function _validate()
    {
        try
        {
            $params = get_params();

            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_string('doc_po')
            ->filter_string('receiving_report_num')
            ->filter_string('po_released_date')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            $task_details = $this->tm_model->get_task_details($params['task_id']);

            //Define the required fields.
            $required = ($task_details['account_group_code'] == AG_CONTRACTORS) ? ['receiving_report_num' => 'Receiving Report #'] : [];

            $constraints['po_date'] = [
                'data_type'         => 'date',
                'name'              => 'PO Date'
            ];

            $constraints['po_released_date']    = [
                'data_type'         => 'date',
                'name'              => 'Released Date'
            ];

            $constraints['doc_po']    = [
                'data_type'         => 'string',
                'name'              => 'DOC PO'
            ];

            $constraints['receiving_report_num']    = [
                'data_type'         => 'string',
                'name'              => 'Receiving Report #'
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
            $data['task_details'] = $task_details;
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