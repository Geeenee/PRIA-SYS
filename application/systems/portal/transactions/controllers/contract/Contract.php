<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contract extends Task_Controller 
{
    protected $controller;
    protected $folder;
    protected $module_js;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('Pria_workflow');

        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_CONTRACTS;
        
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        $this->path_task_views .= $this->folder;
        $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

        $this->load->model(FOLDER_CONTRACTS.'/contracts_model','contracts_model');
        $this->load->model(PORTAL_CODE_LIBRARIES.'/vendor_model', 'vm_model');
        $this->load->model(PORTAL_CODE_LIBRARIES. '/Site_model', 'site_model');

      
    }
    
    public function modal_add_contract($tab_module = NULL)
    {
        try 
        {
            $data = $resources = array();
            $vendor_code = '';

            $resources['load_css']      = array(CSS_UPLOAD, CSS_DATETIMEPICKER, CSS_SELECTIZE);
            $resources['load_js']       = array(JS_UPLOAD, JS_DATETIMEPICKER, JS_SELECTIZE, $this->module_js);

            $resources['loaded_init']   = array(
                'Contract.save();'
            );
            
            $where                  = array('document_type_code' => DOC_TYPE_CONTRACT);
            $doc_type               = $this->dm_model->get_param_document_types($where);
			$task_attachment_files  = $doc_type[0]['allowed_extensions'];
           
            $resources['upload']    = ['contract_file'  => [
                'path'                  => PATH_UPLOADED_FILES,
                'allowed_types'         => $task_attachment_files,
                'multiple'              => FALSE,
                'max_file'              => 1,
                'max_file_size'         => PRIA_UPLOAD_MAX_FILE_SIZE,
                'drag_drop'             => FALSE,
                'show_preview'          => TRUE,
                'show_download'         => TRUE,
                'auto_submit'           => TRUE,
                'multiple_obj'          => TRUE,
                'successCallback'       => "Contract.successCallback(files,data,xhr,pd);",
                'dont_delete_in_server' => TRUE
            ]];

            $tab_module_details = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module]);

            $data['tab_module'] = encrypt_id($tab_module);
            $data['ag_code']    = encrypt_id($tab_module_details['ag_code']);

            //Contract number
            // $contract_max       = $this->contracts_model->get_max_contract();
            // $pieces             = explode("-", $contract_max['max_num']);
            // $last               = str_pad($pieces[2]+1,8,"0",STR_PAD_LEFT);
            // $next_num           = $pieces[0].'-'.$pieces[1].'-'.$last;
            // $data['next_num']   = $next_num;
            //Ends

            //Store Names
            $where = array(
                'status_code' => SITE_STATUS_COMPLETED
            );
            
            $scope_details       = get_scope_details($tab_module);
           // print_var_export($scope_details); die;
            $vendors             = [];

            $org_codes = '';
            /*
            if(!EMPTY($scope_details['orgs'])){
                $org_codes = ['org_code' => ['IN', $scope_details['orgs']]];
            }
            
             $organizations          = $this->contracts_model->get_organizations($org_codes, ['org_code', 'name']);
            
            //If isa lang or get all vendors under niya
            if(COUNT($organizations) == 1)
                $vendors        = $this->contracts_model->get_vendor_by_org_code_arr($organizations[0]['org_code'], $scope_details['vendor_code'], ['a.vendor_code', 'a.vendor_name']);

            if($vendors)
                $vendor_code = ['vendor_code' => ['IN', array_column($vendors,'vendor_code') ]];

            $org_codes  = array();

            IF($vendor_code){
                $vendor_site_det    = $this->site_model->get_vendor_sites($vendor_code);
                
                $org_codes = [
                    'site_code'         => ['IN', array_column($vendor_site_det,'site_code') ],
                    'site_type_code'    => SITE_TYPE_STORE
                ];
            } */

          /*   $where = ['site_type_code' => SITE_TYPE_STORE, 'status_code' => STATUS_COMPLETED, 'site_code' => 'IS NOT NULL'];

            if( ! EMPTY($scope_details['orgs']))
                $where['org_code'] = ['IN', $scope_details['orgs']];

            $data['store_names'] = $this->contracts_model->get_all_sites($where, ['org_code', 'official_store_name','status_code','site_id'], ['official_store_name' => 'ASC']); */
            //print_var_export($scope_details); die;
            $data['lessors']        = $this->vm_model->get_vendor_by_ag_code_n_org_code([AG_LESSORS], $scope_details['orgs']);

            $data['store_names']    = $this->contracts_model->get_available_sites($scope_details['orgs']);
            //Payment Terms
            $data['payment_terms']  = $this->contracts_model->get_all_payment_terms();

            $modal                  = "modals/add_contract";
        }
        catch (PDOException $e)
        {
            $msg  = $this->get_user_message($e);

            $this->error_modal( $msg );
        } 
        
        catch (Exception $e) 
        {
             $msg  = $this->get_user_message($e);

             $this->error_modal( $msg );
        }

        $this->load->view($modal, $data);
        $this->load_resources->get_resource($resources);
    }

    public function process()
    {
        try{

            $flag           = 0;
            $process_status = ERROR;

            $data         = $this->_validate();
           
            $table_options = array(
                    'table_id'        => 'tbl_contracts',
                    'path'            => PORTAL_TRANSACTIONS .'/tabs/contracts/get_contracts_list',
                    'advanced_filter' => TRUE
            );

            $options_encoded = json_encode($table_options);

            $now          = date(FORMAT_DB_DATE);

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_CONTRACTS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            $prev_detail  = [];
            
            //Start the db transaction
            Portal_Model::beginTransaction();

            /* set status */
            $status     = CONTRACT_NEW;
            $exp        = $data['expiration_date'];

            $overdue    = get_sys_param_val(SYS_PARAM_TYPE_CONTRACT_STATUS, SYS_PARAM_CONTRACT_OVERDUE);
            $overdue    = (ISSET($overdue['sys_param_value']) AND !EMPTY($overdue['sys_param_value']))? $overdue['sys_param_value']: NULL;
            $due_renew  = get_sys_param_val(SYS_PARAM_TYPE_CONTRACT_STATUS, SYS_PARAM_CONTRACT_DUE_RENEWAL);
            $due_renew  = (ISSET($due_renew['sys_param_value']) AND !EMPTY($due_renew['sys_param_value']))? $due_renew['sys_param_value']: NULL;

            $due_renew_date = date(FORMAT_DB_DATE, strtotime("-".$due_renew." months", strtotime($exp)));
            $overdue_date   = date(FORMAT_DB_DATE, strtotime("-".$overdue." months", strtotime($exp)));

            if ($exp <= $now)
            {
                $status = CONTRACT_EXPIRED;
                $status_name            = 'expired';
                $status_name_content    = 'has expired';
            }
            else if($overdue_date <= $now)
            {
                $status = CONTRACT_OVERDUE;
                $status_name            = 'overdue';
                $status_name_content    = 'is overdue';
            }
            else if($due_renew_date <= $now)
            {
                $status = CONTRACT_DUE_RENEWAL;
                $status_name            = 'due for renewal';
                $status_name_content    = 'is due for renewal';
            }

            $additional_msg     = $this->lang->line('contract_status_update_message');

            if(EMPTY($data['security']))
            {
                //If reference id is empty action will be update

                IF($data['store_name']){
                    $where_store   = array('site_id' => $data['store_name']);
                    $store_det     = $this->site_model->get_specific_site($where_store);    
                }

                $ag_code    = decrypt_id($data['ag_code']);

                $fields     = array(
                    'vendor_code'           => $data['lessor'],
                    'contract_status_code'  => $status,
                    'org_code'              => ISSET($store_det['org_code']) ? $store_det['org_code'] : NULL,
                    'date_from'             => $data['effectivity_date'],
                    'date_to'               => $data['expiration_date'],
                    'site_id'               => $data['store_name'],
                    'original_start_date'   => $data['effectivity_date'],
                    'expiration_date'       => $data['expiration_date'],
                    'payment_term_code'     => $data['payment_terms'],
                    'contract_file'         => $data['contract_file'],
                    'account_group_code'    => $ag_code,
                    'deleted_flag'          => NOT_DEL_FLAG,
                    'saved_flag'            => MAINTAINER_YES,
                    'created_by'            => $this->session->user_id,
                    'created_date'          => $now
                );

                $contract_id        = $this->contracts_model->insert_contract($fields, TRUE);

                if(ISSET($store_det['site_code']) AND !EMPTY($store_det['site_code']))
                {
                    $where_store        = array('site_code' => $store_det['site_code']);
                    $vendor_site_det    = $this->site_model->get_vendor_site($where_store);   

                    if( ! EMPTY($vendor_site_det))
                        $this->vm_model->update_vendor_sites(['vendor_code' => $data['lessor']], ['site_code' => $store_det['site_code']]);
                    else
                        $this->vm_model->insert_vendor_sites(['vendor_code'=> $data['lessor'], 'site_code' => $store_det['site_code']]);
                }

                $contract_details   = $this->contracts_model->get_specific_contract($contract_id);

                if($status != CONTRACT_EXPIRED)
                    $this->_insert_contract_billing($contract_details);
                    
                $parent_module_code = $this->get_module_code_per_task_ag_code($ag_code);
                    
                $overview_details   = [
                    'transaction_num'    => $contract_details['contract_code'],
                    'transaction_msg'    => $this->lang->line('add_transaction_contract_new'),
                    'reference'          => ISSET($contract_id) ? $contract_id : NULL,
                    'created_by'         => $this->session->user_id,
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => $ag_code,
                    'tab_module_code'	 => MODULE_PORTAL_TRANS_LESSORS_CONTRACTS,
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $contract_details['contract_code']
                ];
                
                $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

                $this->dm_model->update_document(['reference' => $contract_id], ['sys_file_name' => $data['contract_file']]);

                $org_code                       = (ISSET($contract_details['org_code']))? $contract_details['org_code']: NULL;

                $transaction_params             = array(
                        'reference_num'         => $contract_details['contract_code'],
                        'ag_name'               => 'Lessors',
                        'transaction_action'    => 'Added',
                        'redirect'              => PORTAL_TRANSACTIONS . "/lessors#tab_contracts"
                );

                $this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_CONTRACT_ADD_TRANSACTION_W_ACTION, NULL, NULL, NULL, $org_code, $this->session->user_id, NULL, $transaction_params);

                $where        = ['contract_id' => $contract_id];
                $curr_detail  = [ $this->contracts_model->get_details_for_audit($table, $where) ];

                $audit_action = [AUDIT_INSERT];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Contract');
            }
            else {
                $contract_id    = decrypt_id($data['security']);

                $where          = ['contract_id' => $contract_id];

                $contract       = $this->contracts_model->get_details_for_audit($table, $where);

                $prev_detail    = $contract;

                $contract       = $contract[0];

                $fields     = array(
                    'date_from'             => $data['effectivity_date'],
                    'date_to'               => $data['expiration_date'],
                    'payment_term_code'     => $data['payment_terms'],
                    'contract_status_code'  => $status,
                    'modified_by'           => $this->session->user_id,
                    'modified_date'         => $now
                );

                if(!EMPTY($contract['reference_contract_id'])) {
                    $fields['original_start_date']  = $data['effectivity_date'];
                    $fields['expiration_date']      = $data['expiration_date'];
                }

                $this->contracts_model->update_contract($fields, $where);

                $contract       = $this->contracts_model->get_details_for_audit($table, $where);
                $curr_detail    = $contract;

                $contract       = $contract[0];

                $this->contracts_model->delete_contract_billing_dates(['contract_id' => $contract_id, 'notified' => ['!=' => ENUM_YES]]);

                if($status != CONTRACT_EXPIRED)
                    $this->_insert_contract_billing($contract);

                $org_code           = (ISSET($contract['org_code']))? $contract['org_code']: NULL;

                $parent_module_code = $this->get_module_code_per_task_ag_code(AG_LESSORS);
                    
                $overview_details   = [
                    'transaction_num'    => $contract['contract_code'],
                    'transaction_msg'    => $this->lang->line('update_transaction_contract'),
                    'reference'          => ISSET($contract_id) ? $contract_id : NULL,
                    'created_by'         => $this->session->user_id,
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => AG_LESSORS,
                    'tab_module_code'    => MODULE_PORTAL_TRANS_LESSORS_CONTRACTS,
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $contract['contract_code']
                ];
                
                $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

                $audit_action   = [AUDIT_UPDATE];
                $activity       = sprintf($this->lang->line('audit_trail_update'), ' Contract');
            }

            $contract_code    = (ISSET($contract_details['contract_code']))? $contract_details['contract_code']: $contract['contract_code'];
            $vendor_code      = (ISSET($contract_details['vendor_code']))? $contract_details['vendor_code']: $contract['vendor_code'];
            $site_id          = (ISSET($contract_details['site_id']))? $contract_details['site_id']: $contract['site_id'];

            if($status != CONTRACT_NEW AND $status != $prev_detail[0]['contract_status_code'])
            {
                $roles          = array(ROLE_ROTI_ADMIN, ROLE_ROH, ROLE_BC_HEAD); //specify role to be notified
                $message        = '';       //notification for mobile
                $ref_number     = $contract_code;
                $ref_number_link= "<a href='" . get_link_url(MODULE_PORTAL_TRANS_LESSORS, $ref_number) . "#tab_contracts'>$ref_number</a>";

                $notification   = "<font color='#000000'> <b>Contract</b> with CN</font> <font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> $status_name_content. You may now create and upload your <b>contract renewal recommendation</b></font>";
                
                $this->pria_notification->import_reminder($roles, MODULE_PORTAL_TRANS_LESSORS, $notification, $message, [$org_code], ADMINISTRATOR_UID, $ref_number);

                $bc_details         = $this->contracts_model->get_organization(['org_code' => $org_code], ['name org_name']);
                $vendor_details     = $this->contracts_model->get_vendor(['vendor_code' => $vendor_code], ['vendor_name']);
                $site_details       = $this->contracts_model->get_site(['site_id' => $site_id], ['official_store_name']);

                $business_center_name   = (ISSET($bc_details['org_name']) AND !EMPTY($bc_details['org_name']))? $bc_details['org_name']: "";
                $vendor_name            = (ISSET($vendor_details['vendor_name']) AND !EMPTY($vendor_details['vendor_name']))? $vendor_details['vendor_name']: "";
                $official_store_name    = (ISSET($site_details['official_store_name']) AND !EMPTY($site_details['official_store_name']))? $site_details['official_store_name']: "";

                $transaction_params = [
                        'reference_num'         => $contract_code,
                        'ag_name'               => "Lessors",
                        'business_center_name'  => $business_center_name,
                        'vendor_name'           => $vendor_name,
                        'official_store_name'   => $official_store_name,
                        'is_msg'                => $status_name,
                        'is_msg_content'        => $status_name_content,
                        'additional_msg'        => $additional_msg
                ];

                $this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_CONTRACT_TRANSACTION_IS_ADDTL_MSG, NULL, NULL, NULL, $org_code, NULL, NULL, $transaction_params);
            }

            $this->audit_trail->log_audit_trail($activity, MODULE_PORTAL_TRANS_LESSORS_CONTRACTS, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $msg            = $this->lang->line('data_saved');
            $flag           = 1;
            $process_status = SUCCESS;
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
            'flag'      => $flag,
            'msg'       => $msg, 
            'status'    => $process_status,
            'datatable' => $options_encoded
        ]);
    }

    private function _insert_contract_billing($contract_details)
    {
        try
        {
            //GET MONTH 
            $start    = $month = strtotime($contract_details['date_from']);
            $end      = strtotime($contract_details['date_to']);
            $curr     = strtotime(date(FORMAT_DB_DATE));

            $ann_mons = [];
            $qrt_mons = [];
            $sem_mons = [];
            $mons 	  = [];
            $inc 	  = 1;

            $sys_param_val  = get_sys_param_val(SYS_PARAM_TYPE_REMINDER, SYS_PARAM_CONTRACT_NOTIF_MONTH);

            $month_prior    = $sys_param_val['sys_param_value'];

            $fields         = [
                    'contract_id' => $contract_details['contract_id'],
                    'notified'    => ENUM_NO
            ];
            
            do
            {
                $month = strtotime("+1 month", $month);
                $month = ($month > $end)? $end: $month;

                $date = date(FORMAT_DB_DATE, $month);

                $prev_month = strtotime("-".$month_prior." month", $month);
                $prev_month = ($prev_month < $start)? $start: $prev_month;
                $prev_month = ($prev_month < $curr)? $curr: $prev_month;
                $prev_month = date(FORMAT_DB_DATE, $prev_month);

                $fields['billing_date']     = NULL;
                $fields['reminder_date']    = NULL;

                if($month <= $end AND $month > $curr)
                {
                    switch($contract_details['payment_term_code'])
                    {
                        case PAYMENT_TERM_ANNUALLY:
                                if(($inc % 12) === 0 OR $month == $end)
                                {
                                    $fields['billing_date']     = $date;   
                                    $fields['reminder_date']    = $prev_month;   
                                }
                            break;

                        case PAYMENT_TERM_MONTHLY:
                                    $fields['billing_date']     = $date;   
                                    $fields['reminder_date']    = $prev_month; 
                            break;
                        
                        case PAYMENT_TERM_QUARTERLY:
                                if(($inc % 3) === 0 OR $month == $end)
                                {
                                    $fields['billing_date']     = $date;   
                                    $fields['reminder_date']    = $prev_month;   
                                }
                            break;

                        case PAYMENT_TERM_SEMI_ANNUALLY:
                                if(($inc % 6) === 0 OR $month == $end)
                                {
                                    $fields['billing_date']     = $date;   
                                    $fields['reminder_date']    = $prev_month;   
                                }
                            break;
                    }

                    if(ISSET($fields['billing_date']) AND !EMPTY($fields['billing_date'])
                    AND ISSET($fields['reminder_date']) AND !EMPTY($fields['reminder_date']))
                    {
                        $this->contracts_model->insert_contract_billing_dates($fields);
                    }
                }

                $inc++;

            } while($month < $end);
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

    private function _validate()
    {
        try
        {
            $params = get_params();
            
            //Filters the data inputted by the user 
            $params = $this->set_filter( $params )
            ->filter_string('store_name')
            ->filter_string('lessor')
            ->filter_string('payment_terms')
            ->filter_string('ag_code')
            ->filter_date('effectivity_date')
            ->filter_date('expiration_date')
            ->filter_string('contract_file')
            ->filter();

            //Define the required fields.
            $required = [
                'effectivity_date'      => 'Effectivity Date',
                'expiration_date'       => 'Expiration Date',
                'payment_terms'         => 'Payment Terms'
            ];

            if(!ISSET($params['security']) OR EMPTY($params['security'])) {
                $required['store_name']     = 'Store Name';
                $required['lessor']         = 'Lessor';
                $required['ag_code']        = 'Account Group';
                $required['contract_file']  = 'Contract File';

                $constraints['store_name'] = [
                    'data_type'         => 'string',
                    'name'              => 'Store Name'
                ];

                $constraints['contract_file'] = [
                    'data_type'         => 'string',
                    'name'              => 'Contract File'
                ];

                $constraints['lessor']    = [
                    'data_type'         => 'string',
                    'name'              => 'Lessor'
                ];

                $constraints['ag_code'] = [
                    'data_type'         => 'string',
                    'name'              => 'Account Group'
                ];
            }

            $constraints['effectivity_date'] = [
                'data_type'         => 'date',
                'name'              => 'Effectivity Date'
            ];

            $constraints['expiration_date'] = [
                'data_type'         => 'date',
                'name'              => 'Expiration Date'
            ];

            $constraints['payment_terms'] = [
                'data_type'         => 'string',
                'name'              => 'Payment Terms'
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            $data['security']   = (ISSET($params['security']) AND !EMPTY($params['security']))? $params['security']: NULL;

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

    public function modal_view_contract($contract_id = NULL, $edit = FALSE)
    {
        try
        {   $data       = array();
            $resources  = array();

            $resources['load_css']      = array(CSS_DATETIMEPICKER, CSS_SELECTIZE);
            $resources['load_js']       = array(JS_DATETIMEPICKER, JS_SELECTIZE, $this->module_js);

            $resources['loaded_init']   = array(
                'Contract.save();'
            );

            $where      = ['reference' => $contract_id, 'module_code' => MODULE_PORTAL_TRANS_LESSORS];

            $contract_info = $this->contracts_model->get_specific_contract($contract_id);

            if( ! EMPTY($contract_info['reference_contract_id']))
            {
                $where['document_type_code'] = DOC_TYPE_SIGNED_RENEWAL;
            }
            
            $documents     = $this->dm_model->get_document($where);

            $data['security']       = encrypt_id($contract_id);

            //Payment Terms
            $data['payment_terms']  = $this->contracts_model->get_all_payment_terms();

            $data['contract_info']  = $contract_info;
            $data['documents']      = $documents;
            $data['edit']           = $edit;
            
            $modal_page = 'modals/view_contract';
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }

        $this->load->view($modal_page, $data);
        $this->load_resources->get_resource($resources);
    }


    public function save_document()
    {
        try
        {
            $params = get_params();
            $status = ERROR;
            $msg    = '';
            $insert = [
                'document_type_code'    => DOC_TYPE_CONTRACT,
                'reference'             => 'TC-'.strtotime('now'),
                'created_by'            => $this->session->user_id,
                'created_date'          => date(FORMAT_DB_DATETIME),
                'file_name'             => $params['filename'],
                'sys_file_name'         => $params['sysfilename'],
                'version'               => INITIAL_YES,
                'module_code'           => MODULE_PORTAL_TRANS_LESSORS,
                'account_group_code'    => AG_LESSORS,
            ];

            //Start the db transaction
            Portal_Model::beginTransaction();

           /*  print_var_export($insert); die; */
            $document_id   = $this->dm_model->insert_document($insert);
            $status        = SUCCESS;

            Portal_Model::commit();
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
        $response                   = array(
            'msg'                   => $msg,
            'status'                => $status
        );

        echo json_encode( $response );
    }
}