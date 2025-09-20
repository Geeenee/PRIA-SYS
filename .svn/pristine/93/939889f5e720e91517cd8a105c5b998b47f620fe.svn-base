<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class Tasks extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Pria_api_model', 'pria_api_model');
        $this->load->model('apis/Internal_order_model', 'io_model');
        $this->load->model('apis/Tasks_model', 'tm_model2');
        $this->load->model('Pria_workflow_model', 'pw_model');

        $this->load->model(PORTAL_TRANSACTIONS . '/task_model', 'tm_model');
        $this->load->model(PORTAL_TRANSACTIONS . '/dr/Delivery_goods_model', 'dr_model');

        $this->load->library('Pria_workflow', 'pria_workflow');
        $this->load->library('Pria_overview', 'pria_overview');
    }

    public function get_assigned_tasks_get()
    {
        $assigned_tasks = array();
        try {
            $success = 0;
            $user_id = $this->get('user_id');
            $account_group_name = $this->get('account_group_name');
            $task_status = $this->get('task_status');
            $task_name = $this->get('task_name');
            $org_code = $this->get('org_code');
            $vendor_code = $this->get('vendor_code');
            $user_org_code = $this->get('user_org_code');
            $user_vendor_code = $this->get('user_vendor_code');

            if (empty($user_id)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }
            $where = [
                'user_id' => $user_id,
                'account_group_name' => $account_group_name,
                'task_status' => $task_status,
                'task_name' => $task_name,
                'org_code' => $org_code,
                'vendor_code' => $vendor_code,
                'user_org_code' => $user_org_code,
                'user_vendor_code' => $user_vendor_code
            ];
            $assigned_tasks_res = $this->pria_api_model->get_all_assigned_task($where);
          
            foreach($assigned_tasks_res as $key => $item){
                $params = [
                    "core_workflow_id" => $item['core_workflow_id'],
                    "ag_code" => $item['account_group_code'],
                    "ref_num" => $item['reference_num']
                ];
                $trans_detail = $this->pria_api_model->get_bc_name_vs2($params);
                $item["bc_name"] = count($trans_detail) ? $trans_detail[0]['bc_name'] : "";
                // if($item['task_status_name'] == "Returned"){
                //     if($item['created_by_user'] == "YES"){
                //         $assigned_tasks[] =  $item;
                //     }
                // }else{
                    $assigned_tasks[] =  $item;
                // }
                
            }

            // var_dump(json_encode($assigned_tasks));
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'assigned_tasks' => $assigned_tasks
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_task_status_names_for_dropdown_get()
    {
        try {
            $success = 0;
            $flag = '';
            // $task_status_names = $this->pria_api_model->get_task_status_names_for_dropdown();
            $task_status_names = $this->pria_api_model->get_task_status_names_for_dropdown("Cancelled", "Disapproved");
            $task_status_names[] = ['action_name' => 'Pending'];
            $task_status_names[] = ['action_name' => 'Resubmitted'];
            $action_names = array_column($task_status_names, 'action_name');
            array_multisort($action_names, SORT_ASC, $task_status_names);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }


        if ($success == 1) {
            $response = [
                'task_status_names' => $task_status_names
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $flag
            ];
        }

        $this->response($response);
    }

    public function get_remaining_amount_by_po_num_get()
    {
        try {
            $success = 0;
            $po_num = $this->get('po_num');

            $remaining_amount = $this->pria_api_model->get_remaining_amount_by_po_num($po_num);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = $remaining_amount;
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_assigned_task_name_for_dropdown_get()
    {
        try {
            $success = 0;
            $workflow_for_id = $this->get('workflow_for_id');
            $role_code = $this->get('role_code');
            $user_id = $this->get('user_id');

            if (empty($workflow_for_id)) {
                throw new Exception($this->lang->line('err_required_workflow_for_id'));
            }

            if (empty($role_code)) {
                throw new Exception($this->lang->line('err_required_role_code'));
            }

            if (empty($user_id)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            $where = [
                'workflow_for_id' => $workflow_for_id,
                'role_code' => $role_code,
                'user_id' => $user_id,
            ];

            $assigned_tasks = $this->pria_api_model->get_all_assigned_task_name_for_dropdown($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'assigned_tasks' => $assigned_tasks,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_soa_by_soa_id_get()
    {
        try {
            $success = 0;
            $soa_id = $this->get('soa_id');
            $pria_task_id = $this->get('pria_task_id');

            if (empty($soa_id)) {
                throw new Exception($this->lang->line('err_required_soa_id'));
            }

            $response = $this->pria_api_model->get_soa_by_soa_id($pria_task_id, $soa_id);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        $response = $success == 1 ? $response[0] : ['msg' => $msg, 'flag' => $success];

        $this->response($response);
    }

    public function get_specific_task_get()
    {
        try {
            $success = 0;
            $pria_task_id = $this->get('pria_task_id');
            $task_type = $this->get('task_type');

            if (empty($pria_task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($task_type)) {
                throw new Exception($this->lang->line('err_required_task_type'));
            }

            $specific_task = $this->pria_api_model->get_specific_task($pria_task_id, $task_type);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $file_size = '';
            $path = PATH_UPLOADED_FILES . $specific_task[0]['sys_file_name'];
            $path = str_replace(array('\\', '/'), array(DS, DS), $path);

            if (file_exists($path)) {
                $file_size = file_size_convert(filesize($path));
                $file_size_num = filesize($path);
            }

            $file_ext = explode('.', $specific_task[0]['file_name']);

            if (is_null($specific_task[0]['document_type_code'])) {
                $specific_task[0]['document_type_code'] = "";
            }

            if (is_null($specific_task[0]['sys_file_name'])) {
                $specific_task[0]['file_size'] = "";
                $specific_task[0]['file_ext'] = "";
                $specific_task[0]['created_by'] = "";
                $specific_task[0]['modified_by'] = "";
                $specific_task[0]['file_name'] = "";
                $specific_task[0]['sys_file_name'] = "";
                $specific_task[0]['version'] = "";
                $specific_task[0]['created_date'] = "";
                $specific_task[0]['modified_date'] = "";
            } else {
                $specific_task[0]['file_size'] = $file_size;
                $specific_task[0]['file_ext'] = end($file_ext);
                $specific_task[0]['created_date'] = std_db_datetime_format($specific_task[0]['created_date']);
            }

            $response = $specific_task[0];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_documents_by_user_id_get()
    {
        try {
            $success = 0;
            $user_id = $this->get('user_id');
            $pattern = $this->get('pattern');

            if (empty($user_id)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            $params = [
                'user_id' => $user_id,
                'pattern' => $pattern
            ];

            $documents = $this->pria_api_model->get_all_documents_by_user_id($params);

            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $revised_documents = array();

            foreach ($documents as $key => $document) {
                $file_size = '';

                $path = PATH_UPLOADED_FILES . $document['sys_file_name'];
                $path = str_replace(array('\\', '/'), array(DS, DS), $path);

                if (file_exists($path)) {
                    $file_size = file_size_convert(filesize($path));
                    $file_size_num = filesize($path);
                }

                $file_ext = explode('.', $document['file_name']);

                if (is_null($document['sys_file_name'])) {
                    $document['file_size'] = "";
                    $document['file_ext'] = "";
                    $document['created_by'] = "";
                    $document['modified_by'] = "";
                    $document['file_name'] = "";
                    $document['sys_file_name'] = "";
                    $document['version'] = "";
                    $document['created_date'] = "";
                    $document['modified_date'] = "";
                } else {
                    $document['file_size'] = $file_size;
                    $document['file_ext'] = end($file_ext);
                    $document['created_date'] = std_db_datetime_format($document['created_date']);
                }

                array_push($revised_documents, $document);
            }

            $response = [
                'documents' => $revised_documents,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function check_if_soa_number_exists_get()
    {
        $soa_num = $this->get('soa_num');

        //check if soa number already exists
        $where      = array('soa_num' => $soa_num);
        $has_soa    = $this->pria_api_model->get_soas($where);

        $response = [
            'msg' => $has_soa ? 'SOA number already exists.' : 'SOA number does not exists.'
        ];

        $this->response($response);
    }

    public function get_account_group_code_by_po_num_get()
    {
        $po_num = $this->get('po_num');

        $account_group_code = $this->pria_api_model->get_account_group_code_by_po_num($po_num);

        $response = [
            'account_group_code' => $account_group_code
        ];

        $this->response($response);
    }

    public function add_soa_post()
    {
        try {
            $flag = ERROR;
            $status = '';
            $response = [];
            $now = date("Y-m-d H:i:s");
            $soa_id = '';
            $task_id = '';

            $po_num = $this->post('po_num');
            $account_group_code = $this->post('account_group_code') == 'GOODS' ? $this->pria_api_model->get_account_group_code_by_po_num($po_num) : $this->post('account_group_code');
            $soa_date = $this->post('soa_date');
            $submission_date = $this->post('soa_date_submitted');
            $soa_num = $this->post('soa_num');
            $vendor_code = $this->post('vendor_code');
            $org_code = $this->post('org_code');
            $recipient_id = $this->post('recipient_id');
            $soa_amount = $this->post('soa_amount');
            $date_from = $this->post('date_from');
            $date_to = $this->post('date_to');
            $task_status_id = $this->post('task_status_id');
            $fhr_file_name = $this->post('fhr_file_name');
            $fhr_sys_file_name = $this->post('fhr_sys_file_name');
            $user_id = $this->post('user_id');
            $po_id = $this->post('po_id');
            $is_add_soa = $this->post('is_add_soa');
            $doc_recipient = $this->post('doc_recipient');

            if (empty($account_group_code)) {
                throw new Exception($this->lang->line('err_required_account_group_code'));
            }

            if (empty($soa_date)) {
                throw new Exception($this->lang->line('err_required_soa_date'));
            }

            if (empty($submission_date)) {
                throw new Exception($this->lang->line('err_required_submission_date'));
            }

            if (empty($soa_num)) {
                throw new Exception($this->lang->line('err_required_soa_num'));
            }

            if (empty($vendor_code)) {
                throw new Exception($this->lang->line('err_required_vendor_code'));
            }

            if (empty($org_code)) {
                throw new Exception($this->lang->line('err_required_org_code'));
            }

            if (empty($recipient_id)) {
                throw new Exception($this->lang->line('err_required_recipient_id'));
            }

            if (empty($soa_amount)) {
                throw new Exception($this->lang->line('err_required_soa_amount'));
            }

            if (empty($date_from)) {
                throw new Exception($this->lang->line('err_required_date_from'));
            }

            if (empty($date_to)) {
                throw new Exception($this->lang->line('err_required_date_to'));
            }

            if (empty($task_status_id)) {
                throw new Exception($this->lang->line('err_required_task_status_id'));
            }

            if (empty($user_id)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_SOA;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            $tab_details = $this->pria_api_model->get_tab_details($account_group_code);
            $tab_module_code = $tab_details[0]['tab_module_code'];
            $core_workflow_id = $tab_details[0]['core_workflow_id'];

            if ($is_add_soa == 'true') {
                $audit_action = [AUDIT_INSERT];

                $prev_detail  = [];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' SOA report');

                $extra_data = array(
                    'user_id'               => $user_id,
                    'account_group_code'    => $account_group_code,
                    'workflow_for_type'     => WORKFLOW_FOR_VENDOR,
                    'workflow_for_id'       => $vendor_code,
                    'reference_num'         => $soa_num,
                    'org_code'              => $org_code,
                    'vendor_code'           => $vendor_code
                );

                $workflow_det = $this->pria_workflow->copy_worfklow(
                    $core_workflow_id,
                    $extra_data
                );

                $insert  = array(
                    'soa_num'               => $soa_num,
                    'soa_date'              => std_db_date_format($soa_date),
                    'vendor_code'           => $vendor_code,
                    'org_code'              => $org_code,
                    'soa_type'              => SOA_NORMAL,
                    'account_group_code'    => $account_group_code,
                    'submission_date'       => std_db_date_format($submission_date),
                    'date_to'               => std_db_date_format($date_to),
                    'date_from'             => std_db_date_format($date_from),
                    'soa_amount'            => $soa_amount,
                    'recipient_id'          => $recipient_id,
                    'doc_recipient'         => $doc_recipient,
                    'created_by'            => $user_id,
                    'created_date'          => $now
                );

                $soa_id = $this->pria_api_model->insert_soa($insert);

                //insert to pria_references table
                if (!empty($po_num)) {
                    $fields = array('soa_id' => $soa_id, 'po_id' => $po_id);
                    $this->pria_api_model->insert_soa_transmittals($fields);
                }

                //update actual table
                $workflow_fields    = array(
                    'reference_id' => $soa_id
                );
                $workflow_where     = array('pria_workflow_id' => $workflow_det['workflow_id']);

                $this->pria_api_model->update_workflow($workflow_fields, $workflow_where);

                $this->pria_workflow->tag_task($workflow_det['task_id'], $task_status_id, ['reference' => $soa_id], $user_id, $recipient_id);
                //$module_code = $this->_get_module_code_per_task_ag_code($account_group_code);

                $task_id = $workflow_det['task_id'];

                $document = $this->pria_api_model->get_document_count($task_id);

                $document_count = $document[0]['document_count'];
                
                if ($document_count == '0') {
                    //Set up fields that will be inserted
                    $insert_fields2 = [
                        'reference' => $soa_id,
                        'pria_task_id' => $task_id,
                        'document_type_code' => DOC_TYPE_SOA,
                        'module_code' => $tab_module_code, //will ask if correct
                        'file_name' => $fhr_file_name,
                        'sys_file_name' => $fhr_sys_file_name,
                        'version' => 1,
                        'created_by' => $user_id,
                        'created_date' => $now,
                    ];

                    $this->pria_api_model->insert_document($insert_fields2);
                }
                

                $where        = ['soa_id' => $soa_id];
                $curr_detail  = [$this->pria_api_model->get_details_for_audit($table, $where)];
            }

            $parent_module_code = $this->tm_model->get_module_account_group(['account_group_code' => $account_group_code])[0]['module_code'];

            $overview_type          = OVERVIEW_TYPE_ADD_TRANSACTION;

            $overview_details   = [
                'transaction_num'    => $soa_num,
                'transaction_msg'    => $this->lang->line('add_transaction_soa'),
                'reference'          => isset($soa_id) ? $soa_id : null,
                'created_by'         => $user_id,
                'created_date'       => date('Y-m-d H:i:s'),
                'account_group_code' => $account_group_code,
                'tab_module_code'     => $tab_module_code,
                'parent_module_code' => $parent_module_code,
                'keyword'            =>  $soa_num
            ];

            $this->pria_overview->log_overview($parent_module_code, $overview_type, $overview_details);
            $this->audit_trail->log_audit_trail($activity, $tab_module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema, $user_id);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg = $this->lang->line('data_saved');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
            'soa_id' => $soa_id,
            'task_id' => $task_id
        ];

        $this->response($response);
    }

    public function update_soa_post()
    {
        try {
            $flag = ERROR;
            $response = [];
            $now = date(FORMAT_DB_DATETIME);

            $account_group_code = $this->post('account_group_code');
            $task_id = $this->post('task_id');
            $fhr_file_name = $this->post('fhr_file_name');
            $fhr_sys_file_name = $this->post('fhr_sys_file_name');
            $task_status_id = $this->post('task_status_id');
            $soa_id = $this->post('soa_id');
            $user_id = $this->post('user_id');
            $recipient_id = $this->post('recipient_id');
            $isCreatedByUser = $this->post('isCreatedByUser');

            if (empty($account_group_code)) {
                throw new Exception('err_required_account_group_code');
            }

            if (empty($task_id)) {
                throw new Exception('err_required_task_id');
            }

            if (empty($task_status_id)) {
                throw new Exception('err_required_task_status_id');
            }

            if (empty($soa_id)) {
                throw new Exception('err_required_soa_id');
            }

            if (empty($user_id)) {
                throw new Exception('err_required_user_id');
            }
            $apply_recipient_id = false;
            if(!empty($isCreatedByUser)){
                if(strtoupper($isCreatedByUser) == "YES"){
                    $apply_recipient_id = true;
                }
            }

            $tab_details = $this->pria_api_model->get_tab_details($account_group_code);
            $tab_module_code = $tab_details[0]['tab_module_code'];

            $document = $this->pria_api_model->get_document_count($task_id);

            $document_count = $document[0]['document_count'];

            if ($document_count == '0') {
                //Set up fields that will be inserted
                $insert_fields = [
                    'reference' => $soa_id,
                    'pria_task_id' => $task_id,
                    'document_type_code' => DOC_TYPE_SOA,
                    'module_code' => $tab_module_code, //will ask if correct
                    'file_name' => $fhr_file_name,
                    'sys_file_name' => $fhr_sys_file_name,
                    'version' => 1,
                    'created_by' => $user_id,
                    'created_date' => $now,
                ];

                $this->pria_api_model->insert_document($insert_fields);
            }
            if($apply_recipient_id){
                $this->pria_workflow->tag_task($task_id, $task_status_id, ['reference' => $soa_id], $user_id, $recipient_id);
            }else{
                $this->pria_workflow->tag_task($task_id, $task_status_id, ['reference' => $soa_id], $user_id);
            }
            Portal_Model::commit();

            $flag = SUCCESS;
            $msg = $this->lang->line('data_saved');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg
        ];

        $this->response($response);
    }

    public function encode_delivery_receipt_post()
    {
        try {
            $flag = ERROR;
            $status = '';
            $response = [];
            $now = date(FORMAT_DB_DATETIME);
            //Initial audit trail config
            $table = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema = [DB_PORTAL];
            $audit_table = [$table];

            $dr_num = $this->post('dr_num');
            //$dr_amount = $this->post('dr_amount');
            $dr_recipient_id = $this->post('dr_recipient_id');
            $dr_date = $this->post('dr_date');
            $site_id = $this->post('site_id');
            $account_group_code = $this->post('account_group_code');
            $vendor_code = $this->post('vendor_code');
            $last_dr_flag = $this->post('last_dr_flag');
            $logged_in_user = $this->post('logged_in_user');
            $dr_type_code = APV_REF_TYPE_CODE_PO; //param_dr_types
            $org_code = $this->post('org_code');
            $task_status_id = $this->post('task_status_id');
            $fhr_file_name = $this->post('fhr_file_name');
            $fhr_sys_file_name = $this->post('fhr_sys_file_name');
            $po_id = $this->post('po_id');
            $is_add_delivery = $this->post('is_add_delivery');
            $task_id = $this->post('pria_task_id');
            $site_type_code = $this->post('site_type_code');
            //$new_remaining_amount = $this->post('new_remaining_amount');

            if (empty($dr_num)) {
                throw new Exception($this->lang->line('err_required_dr_num'));
            }

            /*if (empty($dr_amount)) {
                throw new Exception($this->lang->line('err_required_dr_amount'));
            }*/

            if (empty($dr_recipient_id)) {
                throw new Exception($this->lang->line('err_required_dr_recipient'));
            }

            if (empty($dr_date)) {
                throw new Exception($this->lang->line('err_required_dr_date'));
            }

            if (empty($site_id)) {
                throw new Exception($this->lang->line('err_required_site_id'));
            }

            if (empty($vendor_code)) {
                throw new Exception($this->lang->line('err_required_vendor_code'));
            }

            if (empty($po_id)) {
                throw new Exception($this->lang->line('err_required_po_id'));
            }

            if ($is_add_delivery == 'true') {
                //Starts
                $curr_sequence_no = 1;

                $workflow_det = $this->pria_api_model->get_workflow_id_by_po_id($po_id);

                $account_group_code = $workflow_det[0]['account_group_code'];

                $workflows = $this->tm_model->get_appendable_workflow_by_pria_workflow_id($workflow_det[0]['pria_workflow_id']);

                $curr_sequence_no = $this->tm_model->get_stage_seq_where_to_append($workflow_det[0]['core_workflow_id'], $workflows[0]['core_workflow_stage_id'], $workflow_det[0]['pria_workflow_id']);

                if (empty($curr_sequence_no)) {
                    $max_stage = $this->tm_model->get_pria_workflow_stage_details(['pria_workflow_id' => $workflow_det[0]['pria_workflow_id']], ['MAX(sequence_no) as sequence_no']);
                    $curr_sequence_no = $max_stage[0]['sequence_no'];
                }

                $core_workflow_id = CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL;

                if ($account_group_code != AG_CONTRACTORS) {
                    if ($account_group_code == AG_GOODS_BAVI or $account_group_code == AG_GOODS_BFFI) {
                        $core_workflow_id = $account_group_code == AG_GOODS_BAVI ? CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL : CORE_WORKFLOW_DELIVERY_W_TRANSMITTAL;
                    }

                    $task_id = $this->pria_workflow->append_stage($workflow_det[0]['pria_workflow_id'], $curr_sequence_no, $core_workflow_id, $logged_in_user, true, true);
                }
            }

            //Get Task Document Type
            $field_select = ['*'];
            $where = ['pria_task_id' => $task_id];
            $task_details = $this->tm_model2->get_task_details($task_id);

            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be insert

            if (empty($task_details['task_reference_id'])) {
                $insert_fields = [
                    'pria_task_id' => $task_id,
                    'account_group_code' => $account_group_code,
                    'dr_num' => $dr_num,
                    'org_code' => $org_code,
                    'dr_date' => std_db_date_format($dr_date),
                    'site_id' => $site_id,
                    //'dr_amount' => $dr_amount,
                    'dr_recipient_id' => $dr_recipient_id,
                    'vendor_code' => $vendor_code,
                    'created_by' => $logged_in_user,
                    'created_date' => $now,
                    'dr_type_code' => DR_PO,
                    'last_dr_flag' => $last_dr_flag,
                ];

                //insert delivery goods receipt
                $dr_gr_id = $this->pria_api_model->insert_delivery_goods_receipt($insert_fields, true);

                $fields = [
                    'dr_gr_id' => $dr_gr_id,
                    'po_id' => $po_id,
                ];

                $this->pria_api_model->insert_into_pria_references($fields);

                $reference_id = $dr_gr_id;

                //print_var_export($insert_fields, $reference_id); die;
                //update pria_tasks (reference column)
                $this->pria_api_model->update_reference_in_task(['reference' => $dr_gr_id], ['pria_task_id' => $task_id]);
            } else {
                $task_dets = $this->tm_model2->get_task_details($task_id);
                $reference_id = $task_dets['task_reference_id'];
                $where = array('dr_gr_id' => $reference_id);

                $update_fields = [
                    'dr_num' => $dr_num,
                    'org_code' => $org_code,
                    'dr_date' => std_db_date_format($dr_date),
                    'site_id' => $site_id,
                    //'dr_amount' => $dr_amount,
                    'dr_recipient_id' => $dr_recipient_id,
                    'last_dr_flag' => $last_dr_flag,
                ];

                $this->pria_api_model->update_delivery_goods_receipt($update_fields, $where);
            }

            $module_code = $this->_get_module_code_per_task_ag_code($account_group_code);

            $document = $this->pria_api_model->get_document_count($task_id);

            $document_count = $document[0]['document_count'];

            if ($document_count == '0') {
                //Set up fields that will be inserted
                $insert_fields2 = [
                    'reference' => $reference_id,
                    'pria_task_id' => $task_id,
                    'document_type_code' => DOC_TYPE_DOC_DR,
                    'module_code' => $module_code, //will ask if correct
                    'file_name' => $fhr_file_name,
                    'sys_file_name' => $fhr_sys_file_name,
                    'version' => 1,
                    'created_by' => $logged_in_user,
                    'created_date' => $now,
                ];

                $this->pria_api_model->insert_document($insert_fields2);
            }

            if ($account_group_code == AG_GOODS_MARINADES) {
                $role =  ($site_type_code == SITE_TYPE_DRESSING_PLANT) ? ROLE_PROD_FIN_PERS : ROLE_CSS;
                $pria_stage_id = $this->pria_api_model->get_pria_stage_id_by_task_id($task_id);
                $this->update_task_role($pria_stage_id, $role);
            }

            $this->pria_workflow->tag_task($task_id, $task_status_id, ['reference' => $reference_id], $logged_in_user, $dr_recipient_id);

            //$this->pria_api_model->update_remaining_amount(['po_id' => $po_id], ['remaining_amount' => $new_remaining_amount]);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg = $this->lang->line('data_saved');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg    = $this->rlog_error($e, TRUE);
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
            'task_id' => $task_id,
        ];

        $this->response($response);
    }

    public function update_task_role($stage_id = null, $assign_role = null)
    {
        try {
            if (!empty($stage_id) and !empty($assign_role)) {
                $where  = array(
                    'pria_stage_id' => $stage_id,
                    'sequence_no'   => SEQUENCE_NO_THREE
                );

                $task_info      = $this->dr_model->get_task_ref($where);
                $pria_task_id   = $task_info['pria_task_id'];

                $this->pria_api_model->delete_task_role(['pria_task_id' => $pria_task_id, 'actor_flag' => INITIAL_YES]);
                $this->pria_api_model->insert_task_role(['actor_flag' => INITIAL_YES, 'role_code' => $assign_role, 'pria_task_id' => $pria_task_id]);
            }
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function get_org_code_by_user_id_get()
    {
        try {
            $success = 0;

            $user_id = $this->get('user_id');

            $org_code = $this->pria_api_model->get_org_code_by_user_id($user_id);

            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'org_code' => $org_code,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_org_code_by_po_num_get()
    {
        try {
            $success = 0;

            $po_id = !EMPTY($this->get('po_id'))? $this->get('po_id'): NULL;
            $po_num = !EMPTY($this->get('po_num'))? $this->get('po_num'): NULL;

            $org_code = $this->pria_api_model->get_org_code_by_po_num($po_num, $po_id);

            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'org_code' => $org_code,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function encode_fhr_post()
    {
        try {
            $flag = ERROR;
            $now = date(FORMAT_DB_DATETIME);
            //Initial audit trail config

            $task_id = $this->post('task_id');
            $fhr_document_num = $this->post('fhr_document_num');
            $date_submitted = $this->post('date_submitted');
            $fhr_file_name = $this->post('fhr_file_name');
            $fhr_sys_file_name = $this->post('fhr_sys_file_name');
            $logged_in_user = $this->post('logged_in_user');
            $task_status_id = $this->post('task_status_id');

            if (empty($task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($fhr_document_num)) {
                throw new Exception($this->lang->line('err_required_fhr_document_num'));
            }

            if (empty($date_submitted)) {
                throw new Exception($this->lang->line('err_required_fhr_date_submitted'));
            }

            if (empty($logged_in_user)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            if (empty($fhr_file_name)) {
                throw new Exception($this->lang->line('err_required_fhr_file'));
            }

            //Get Task Document Type
            $field_select = ['*'];
            $where = ['pria_task_id' => $task_id];
            $document_type_det = $this->io_model->get_specific_task_document_type($where, $field_select);

            $document_type_code = $document_type_det[0]['document_type_code'];

            $task_details = $this->tm_model2->get_task_details($task_id);

            //Get IO details
            $fields = ['*'];
            $where = ['io_id' => $task_details['reference_id']];
            $io = $this->io_model->get_internal_order($where, $fields);

            //Set initial fields that is present for both insert and update action
            $fields = [
                'fhr_document_num' => $fhr_document_num,
                'fhr_submit_date' => std_db_date_format($date_submitted),
            ];

            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be insert

            if (empty($io['fhr_document_num'])) {
                $reference_id = $this->tm_model2->get_task_workflow_reference_id($task_id);
                $io_details = $this->io_model->get_internal_order(['io_id' => $reference_id], ['vendor_code']);

                $module_code = $this->_get_module_code_per_task_ag_code($task_details['account_group_code']);

                //Update internal orders
                $where = array('io_id' => $reference_id);
                $this->io_model->update_internal_order($where, $fields);
            } else {
                $reference_id = $this->tm_model2->get_task_workflow_reference_id($task_id);

                //Update internal orders
                $where = array('io_id' => $reference_id);
                $update = array(
                    'fhr_document_num' => $fhr_document_num,
                    'fhr_submit_date' => std_db_date_format($date_submitted),
                );
                $this->io_model->update_internal_order($where, $update);

                /*$update       = [
                'file_name'             => $fhr_file_name,
                'sys_file_name'         => $fhr_sys_file_name,
                'modified_by'           => $logged_in_user,
                'modified_date'         => $now
                ];

                $where          = ['reference' => $reference_id, 'document_type_code'    => $document_type_code];
                $this->pria_api_model->update_document($where, $update);*/

                $module_code = $this->_get_module_code_per_task_ag_code($task_details['account_group_code']);
            }

            $document = $this->pria_api_model->get_document_count($task_id);

            $document_count = $document[0]['document_count'];

            if ($document_count == '0') {
                //Set up fields that will be inserted
                $insert_fields = [
                    'reference' => $reference_id,
                    'pria_task_id' => $task_id,
                    'document_type_code' => DOC_TYPE_FHR,
                    'module_code' => $module_code, //will ask if correct
                    'file_name' => $fhr_file_name,
                    'sys_file_name' => $fhr_sys_file_name,
                    'version' => 1,
                    'created_by' => $logged_in_user,
                    'created_date' => $now,
                ];

                $this->pria_api_model->insert_document($insert_fields);
            }

            $this->pria_workflow->tag_task($task_id, $task_status_id, ['reference' => $reference_id], $logged_in_user);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg = $this->lang->line('data_saved');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
        ];

        $this->response($response);
    }

    public function tag_task_complete_post()
    {
        try {
            $flag = ERROR;
            $completed_by = '';
            $cleared_task = [];

            Portal_Model::beginTransaction();

            $task_id = $this->post('task_id');
            $logged_in_user = $this->post('logged_in_user');

            if (empty($task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($logged_in_user)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            $task_details = $this->tm_model2->get_task_details($task_id);

            $end_date = date(FORMAT_DB_DATETIME);

            if ($logged_in_user != $task_details['user_id']) {
                throw new Exception($this->lang->line('invalid_action'));
            }

            $this->pria_workflow->tag_task_completed($task_id, $end_date);

            $completed_by = std_date_format($end_date) . ' by ' . $task_details['actor'];

            $cleared_task = $this->_check_dependent_tasks($task_id);

            $overview_details = [
                'reference' => $task_id,
                'created_by' => $logged_in_user,
                'created_date' => $end_date,
            ];

            $overview_details = array_merge($task_details, $overview_details);
            $module_code = $this->_get_module_code_per_task_ag_code($task_details['account_group_code']);

            $this->pria_overview->log_overview($module_code, OVERVIEW_TYPE_CHANGE_TASK_STATUS, $overview_details);

            Portal_Model::commit();

            $flag = SUCCESS;

            $msg = $this->lang->line('succ_tagging_task_complete');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
            'completed_by' => $completed_by,
            'cleared_task' => $cleared_task,
        ];

        $this->response($response);
    }

    public function tag_task_return_post()
    {
        try {
            $flag = ERROR;

            $returned_by = '';
            $cleared_task = [];

            Portal_Model::beginTransaction();

            $task_id = $this->post('task_id');
            $logged_in_user = $this->post('logged_in_user');

            if (empty($task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($logged_in_user)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            $task_details = $this->tm_model2->get_task_details($task_id);

            $end_date = date(FORMAT_DB_DATETIME);

            // if($this->session->user_id != $task_details['user_id'])
            // throw new Exception($this->lang->line('invalid_action'));

            //predecessors_id
            // $predecessor_ids = array($params['task_return']);

            // June 26, 2019 by Tristan
            //change $params['task_return']
            $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_RETURNED, array(), $params['task_return']);

            $returned_by = std_date_format($end_date) . ' by ' . $task_details['actor'];

            $cleared_task = $this->_check_dependent_tasks($task_id);
            $return_val = $this->_check_return_values($task_id);

            $overview_details = [
                'reference' => $task_id,
                'created_by' => $logged_in_user,
                'created_date' => $end_date,
            ];

            $overview_details = array_merge($task_details, $overview_details);
            $module_code = $this->_get_module_code_per_task_ag_code($task_details['account_group_code']);

            $this->pria_overview->log_overview($module_code, OVERVIEW_TYPE_CHANGE_TASK_STATUS, $overview_details);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg = $this->lang->line('succ_tagging_task_returned');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
            'cleared_task' => $cleared_task,
            'return_val' => $return_val,
        ];

        $this->response($response);
    }

    public function delete_file_post()
    {
        try {
            $flag = ERROR;

            Portal_Model::beginTransaction();

            $pria_task_id = $this->post('pria_task_id');
            $document_type_code = $this->post('document_type_code');

            if (empty($pria_task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($document_type_code)) {
                throw new Exception($this->lang->line('err_required_document_type_code'));
            }

            $where = [
                'pria_task_id' => $pria_task_id,
                'document_type_code' => $document_type_code,
            ];

            $this->pria_api_model->delete_file($where);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg = $this->lang->line('succ_deleting_file');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
        ];

        $this->response($response);
    }

    public function tag_task_post()
    {
        try {
            $flag = ERROR;

            $pria_task_id = $this->post('pria_task_id');
            $task_status_id = $this->post('task_status_id');
            $user_id = $this->post('user_id');
            $remarks = $this->post('remarks');
            $task_return_id = $this->post('task_return_id');

            if (empty($pria_task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($task_status_id)) {
                throw new Exception($this->lang->line('err_required_task_status_id'));
            }

            if (empty($user_id)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }
            
				

            $columns = array(
                'remarks'        => $remarks, 
                'task_return_id' => $task_return_id
            );

            $this->pria_workflow->tag_task($pria_task_id, $task_status_id, $columns, $user_id);
            $msg = '';

            switch ($task_status_id) {
                case TASK_STATUS_ONGOING:
                    $msg = 'Task tagged as ongoing.';
                    break;
                case TASK_STATUS_DONE:
                    $msg = 'Task tagged as done.';
                    break;
                case TASK_STATUS_APPROVED:
                    $msg = $this->lang->line('succ_tagging_task_approved');
                    break;
                case TASK_STATUS_DISAPPROVED:
                    $msg = $this->lang->line('succ_tagging_task_disapproved');
                    break;
                case TASK_STATUS_RETURNED:
                    $msg = $this->lang->line('succ_tagging_task_returned');
                        if(EMPTY($task_return_id)){
                            throw new Exception('Return to is required.');
                        }
                    break;
                case TASK_STATUS_SKIPPED:
                    $msg = 'Task tagged as skipped.';
                    break;
            }

            $flag = SUCCESS;
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
        ];

        $this->response($response);
    }

    public function get_pre_pria_task_id_get()
    {
        try {
            $success = 0;
            $pria_task_id = $this->get('pria_task_id');

            if (empty($pria_task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            $pre_pria_task_id = $this->pria_api_model->get_pre_pria_task_id($pria_task_id);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'pre_pria_task_id' => $pre_pria_task_id,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function tag_task_approve_post()
    {
        try {
            $flag = ERROR;

            $returned_by = '';
            $cleared_task = [];

            Portal_Model::beginTransaction();

            $task_id = $this->post('task_id');
            $logged_in_user = $this->post('logged_in_user');
            $remarks = $this->post('remarks');

            if (empty($task_id)) {
                throw new Exception($this->lang->line('err_required_pria_task_id'));
            }

            if (empty($logged_in_user)) {
                throw new Exception($this->lang->line('err_required_user_id'));
            }

            $task_details = $this->tm_model2->get_task_details($task_id);

            $end_date = date(FORMAT_DB_DATETIME);

            if ($this->session->user_id != $task_details['user_id']) {
                throw new Exception($this->lang->line('invalid_action'));
            }

            $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_DONE);

            $flag = SUCCESS;
            $msg = $this->lang->line('succ_tagging_task_approved');
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Portal_Model::rollback();
        }

        $response = [
            'flag' => $flag,
            'msg' => $msg,
            'cleared_task' => $cleared_task,
        ];

        $this->response($response);
    }

    private function _get_module_code_per_task_ag_code($ag_code)
    {
        switch ($ag_code) {
            case AG_CONTRACT_GROWERS:
                return MODULE_PORTAL_TRANS_CONTRACT_GROWERS;
                break;
        }
    }

    private function _check_dependent_tasks($task_id)
    {
        try {
            $cleared_task = [];
            $dependents = $this->tm_model2->get_dependent_tasks($task_id);

            $dependents_ids = array_column($dependents, 'pria_task_id');
            //Get the predecessors of the dependent tasks
            $dependents_prede = $this->tm_model2->get_task_predecessors($dependents_ids);

            //Return something kung may walang dependents
            //print_var_export($dependents, $dependents_prede); throw_var_export('asfd');

            foreach ($dependents as $d) {
                $dep_id = $d['pria_task_id'];
                $path = base_url() . PORTAL_TRANSACTIONS . '/' . $d['controller'] . '?t=' . base64_url_encode($dep_id);

                //Clear next task status ( to PENDING) Added by Christian june 6, 2019
                $fields = array('task_status_id' => null);
                $where = array('pria_task_id' => $dep_id);
                $this->pw_model->update_task($fields, $where);
                //Ends

                //Set the response per task cleared. E.g. path and num id to be used in javascript
                $response = [
                    'num' => $dep_id,
                    'anchor' => <<<EOS
            <a href="$path">{$d['task_name']}</a> <span class="completed-by"></span>
EOS
                ];

                //Check if dependent has other predecessors. If no, enable, else, check.
                if (isset($dependents_prede[$dep_id])) {
                    $result = $this->_check_dependent_predecessor($dependents_prede[$dep_id]);

                    if ($result) {
                        $cleared_task[] = $response;
                    }
                } else {
                    $cleared_task[] = $response;
                }
            }

            return $cleared_task;
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function _check_return_values($task_id)
    {
        try {
            $return = true;
            $return = $this->tm_model2->get_return_tasks($task_id);

            return $return;
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function _check_dependent_predecessor($predecessors)
    {
        try {
            $return = true;

            foreach ($predecessors as $p) {
                if (in_array($p['task_status_id'], [TASK_STATUS_ONGOING, TASK_STATUS_RETURNED])) {
                    $return = false;
                }
            }

            return $return;
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function _validate($params)
    {
        try {
            //Filters the data inputted by the user
            $params = $this->set_filter($params)
                ->filter_date('date_submitted')
                ->filter_string('fhr_document_num')
                ->filter_string('flock_history_file')
                ->filter_string('fhr_sysfile')
                ->filter();

            //Define the required fields.
            $required = [
                'date_submitted' => 'Date Submitted',
                'fhr_document_num' => 'FHR Document No.',
                'flock_history_file' => 'Flock History Report File',
            ];

            $constraints['date_submitted'] = [
                'data_type' => 'date',
                'name' => 'Date Submitted',
            ];

            $constraints['fhr_document_num'] = [
                'data_type' => 'string',
                'name' => 'FHR Document No.',
            ];

            $constraints['flock_history_file'] = [
                'data_type' => 'string',
                'name' => 'Flock History Report File',
            ];

            $constraints['fhr_sysfile'] = [
                'data_type' => 'string',
                'name' => 'Flock History Report File',
            ];

            $constraints['task_id'] = [
                'data_type' => 'db_value',
                'name' => 'ETD',
                'field' => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where' => 'pria_task_id',
                'table' => DB_PORTAL . '.' . Portal_Model::PORTAL_TABLE_PRIA_TASKS,
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data = $this->validate_inputs($params, $constraints);

            return $data;
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function get_all_payments_get()
    {
        try {
            $payments = array();
            $success = 0;
            $vendor_code = empty($this->get('vendor_code')) ? null : $this->get('vendor_code');
            $apv_num = $this->get('apv_num');
            $cv_num = $this->get('cv_num');
            $apv_status = $this->get('apv_status');
            $ag_code = $this->get('ag_code');
            $org_code = $this->get('org_code');
            $user_org_code = $this->get('user_org_code');
            $user_id = $this->get('user_id');

            $params = [
                'vendor_code' => $vendor_code,
                'apv_num' => $apv_num,
                'cv_num' => $cv_num,
                'apv_status' => $apv_status,
                'ag_code' => $ag_code,
                'org_code' => $org_code,
                'user_org_code' => $user_org_code,
                'user_id' => $user_id
            ];
            
            // $payments = $this->pria_api_model->get_all_payments($params);
            $scope_details = get_scope_details_mobile(MODULE_PORTAL_DASHBOARD, $user_id, true);
            if($ag_code){
                $payments = $ag_code == AG_LESSORS || $ag_code == null ? $this->pria_api_model->get_all_payments_lessors_revamped($params, $scope_details['having']) : $this->pria_api_model->get_all_payments_revamped($params, $scope_details['having']);
            }else{
                $account_groups = $this->pria_api_model->get_all_account_groups_for_dropdown($params['vendor_code'], null, $params['user_id']);
                foreach($account_groups as $key => $val){
                    $data_res = array();
                    if($val['account_group_code'] == AG_LESSORS){
                      $data_res =  $this->pria_api_model->get_all_payments_lessors_revamped($params, $scope_details['having']);
                    }else{
                        $params['ag_code'] = $val['account_group_code'];
                        $data_res = $this->pria_api_model->get_all_payments_revamped($params, $scope_details['having']);
                    }
                    if(count($data_res)){
                        foreach($data_res as $data_key => $data_val){
                            array_push($payments, $data_val);
                        }
                        
                    }
                }
            }
           
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'payments' => $payments,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_ref_num_by_ag_code_get()
    {
        try {
            $success = 0;
            $ag_code = $this->get('ag_code');

            if (empty($ag_code)) {
                throw new Exception($this->lang->line('err_required_account_group_code'));
            }

            $where = [
                'ag_code' => $ag_code,
            ];

            $ref_nums = $this->pria_api_model->get_all_ref_num_by_ag_code($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'ref_nums' => $ref_nums,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_ref_num_by_core_workflow_id_get()
    {
        try {
            $success = 0;
            $core_workflow_id = $this->get('core_workflow_id');

            if (empty($core_workflow_id)) {
                throw new Exception($this->lang->line('err_required_transaction_tab'));
            }

            $where = [
                'core_workflow_id' => $core_workflow_id,
            ];

            $ref_nums = $this->pria_api_model->get_all_ref_num_by_core_workflow_id($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'ref_nums' => $ref_nums,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_ref_num_by_org_code_vendor_code_get()
    {
        try {
            $success = 0;
            $org_code = $this->get('org_code');
            $user_id = $this->get('user_id');
            $vendor_code = $this->get('vendor_code');
            $ag_code = $this->get('ag_code');
            $core_workflow_id = $this->get('core_workflow_id');
            $selected_tab = $this->get('selected_tab');
            $where = [
                'org_code' => $org_code,
                'user_id' => $user_id,
                'vendor_code' => $vendor_code,
                'ag_code' => $ag_code,
                'core_workflow_id' => $core_workflow_id,
                'selected_tab' => $selected_tab
            ];

            $ref_nums = $this->pria_api_model->get_all_ref_num_by_org_code_vendor_code($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'ref_nums' => $ref_nums,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_transaction_tabs_by_ag_code_get()
    {
        try {
            $success = 0;
            $ag_code = $this->get('ag_code');

            if (empty($ag_code)) {
                throw new Exception($this->lang->line('err_required_account_group_code'));
            }

            $where = [
                'ag_code' => $ag_code,
            ];

            $transaction_tabs = array();
            $res = $this->pria_api_model->get_all_transaction_tabs_by_ag_code($where);
            foreach ($res as $key => $item) {
                $tab_name = '';
                switch ($item['transaction_tab']) {
                    case 'IO':
                        $tab_name = 'INTERNAL ORDERS';
                        break;
                        break;
                    case 'PO':
                        $tab_name = 'PURCHASE ORDERS';
                        break;
                    case 'PR':
                        $tab_name = 'PURCHASE REQUESTS';
                        break;
                    case 'PROJ':
                        $tab_name = 'PROJECTS';
                        break;
                    case 'SITE_NOM':
                        $tab_name = 'SITE NOMINATIONS';
                        break;
                    case 'DR':
                        $tab_name = 'DRS';
                        break;
                    default:
                        $tab_name = $item['transaction_tab'];
                        break;
                }

                $transaction_tabs[] = [
                    'core_workflow_id' => $item['core_workflow_id'],
                    'transaction_tab' => $item['transaction_tab'],
                    'transaction_tab_name' =>  $tab_name
                ];
            }
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'transaction_tabs' => $transaction_tabs,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_workflow_stages_get()
    {
        try {
            $success = 0;
            $ag_code = $this->get('ag_code');
            $ref_num = $this->get('ref_num');
            $org_code = $this->get('org_code');
            $vendor_code = $this->get('vendor_code');
            $core_workflow_id = $this->get('core_workflow_id');
            $selected_tab = $this->get('selected_tab');

            if (empty($ag_code)) {
                throw new Exception($this->lang->line('err_required_account_group_code'));
            }

            if (empty($ref_num)) {
                throw new Exception($this->lang->line('err_required_reference_number'));
            }

            $where = [
                'ag_code' => $ag_code,
                'ref_num' => $ref_num,
                'org_code' => $org_code,
                'vendor_code' => $vendor_code,
                'core_workflow_id' => $core_workflow_id,
                'selected_tab'  => $selected_tab
            ];

            $workflow_stages = $this->pria_api_model->get_all_workflow_stages($where);
            $that = $this;
            $workflow_stages = array_map(function ($value) use ($that, $where) {
                $where['pria_stage_id'] = $value['pria_stage_id'];
                $value['tasks'] = $that->pria_api_model->get_all_workflow_stage_tasks($where);
                return $value;
            }, $workflow_stages);

            $workflow_stage = reset($workflow_stages);
            $vendor_name = $workflow_stage['vendor_name'];
            $bc_name = reset($workflow_stage['tasks'])['business_center_name'];

            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }
        $trans_detail = $this->pria_api_model->get_bc_name_vs2($where);
        if ($success == 1) {
            $response = [
                'workflow_stages' => $workflow_stages,
                'vendor_name' => $vendor_name,
                'bc_name' => count($trans_detail) ? ($trans_detail[0]['bc_name'] != '' ? $trans_detail[0]['bc_name'] : $bc_name) : $bc_name,
                'trans_detail' => $trans_detail
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_all_workflow_stage_tasks_get()
    {
        try {
            $success = 0;
            $ag_code = $this->get('ag_code');
            $ref_num = $this->get('ref_num');
            $pria_stage_id = $this->get('pria_stage_id');

            if (empty($ag_code)) {
                throw new Exception($this->lang->line('err_required_account_group_code'));
            }

            if (empty($ref_num)) {
                throw new Exception($this->lang->line('err_required_reference_number'));
            }

            if (empty($pria_stage_id)) {
                throw new Exception($this->lang->line('err_required_pria_stage_id'));
            }

            $where = [
                'ag_code' => $ag_code,
                'ref_num' => $ref_num,
                'pria_stage_id' => $pria_stage_id,
            ];

            $workflow_stage_tasks = $this->pria_api_model->get_all_workflow_stage_tasks($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'workflow_stage_tasks' => $workflow_stage_tasks,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_allowed_extensions_get()
    {
        try {
            $success = 0;
            $document_type_code = $this->get('document_type_code');

            $where = [
                'document_type_code' => $document_type_code,
            ];

            $allowed_extensions = $this->pria_api_model->get_allowed_extensions($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'allowed_extensions' => $allowed_extensions,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }

    public function get_reference_num_rows_get()
    {
        try {
            $success = 0;
            $reference_num = $this->get('reference_num');
            $account_group_code = $this->get('account_group_code');

            if (empty($account_group_code)) {
                throw new Exception($this->lang->line('err_required_account_group_code'));
            }

            if (empty($reference_num)) {
                throw new Exception($this->lang->line('err_required_reference_number'));
            }

            $where = [
                'reference_num' => $reference_num,
                'account_group_code' => $account_group_code,
            ];

            $reference_num_rows = $this->pria_api_model->get_reference_num_rows($where);
            $success = 1;
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }

        if ($success == 1) {
            $response = [
                'reference_num_rows' => $reference_num_rows,
            ];
        } else {
            $response = [
                'msg' => $msg,
                'flag' => $success,
            ];
        }

        $this->response($response);
    }
}
