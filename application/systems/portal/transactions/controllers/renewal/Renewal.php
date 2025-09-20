<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Renewal extends Task_Controller 
{
    protected $controller;
    protected $folder;
    protected $module_js;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('Pria_workflow');

        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_RENEWAL;
        
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        $this->path_task_views .= $this->folder;
        $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

        $this->load->model(FOLDER_RENEWAL.'/renewal_model','renewal_model');
    }
    
    public function modal_add_renewal($tab_module = NULL, $security = NULL)
    {
        try 
        {
            $data = $resources = array();
            

            $resources['load_js']       = array($this->module_js);

            $resources['loaded_init']   = array(
                'selectize_init();',
                'datepicker_init();',
                'Renewal.save();',
                'Renewal.store();'
            );

            $data['tab_module']    = encrypt_id($tab_module);
           
            $scope_details         = get_scope_details($tab_module);

            $data['security']      = $security;

            $contract_id           = (!EMPTY($security))? decrypt_id($security): NULL;

            $where                  = ['contract_id' => $contract_id];
            $fields                 = array('*');
            $contract               = $this->renewal_model->get_contract($where, $fields);

            $reference_contract_id  = (ISSET($contract['reference_contract_id']))? $contract['reference_contract_id']: NULL;
            $site_id                = (ISSET($contract['site_id']))? $contract['site_id']: NULL;
            $vendor_code            = (ISSET($contract['vendor_code']))? $contract['vendor_code']: NULL;

            $ref_contract           = $this->renewal_model->get_contract(['contract_id' => $reference_contract_id]);

            //Store Names
            $status_code           = SITE_STATUS_COMPLETED;
            $data['store_names']   = $this->renewal_model->get_site_with_contracts($status_code, [CONTRACT_OVERDUE, CONTRACT_DUE_RENEWAL, CONTRACT_EXPIRED], $scope_details['orgs']);
            $data['store']         = $this->renewal_model->get_all_sites(['site_id' => $site_id]);
            $data['vendor']        = $this->renewal_model->get_vendor(['vendor_code' => $vendor_code]);

            //Payment Terms
            $data['payment_terms'] = $this->renewal_model->get_all_payment_terms();

            $data['contract']      = $contract;
            $data['ref_contract']  = $ref_contract;

            $modal                 = "modals/add_renewal";
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

            $flag         = 0;
            $status       = ERROR;
            $data         = $this->_validate();
            $now          = date(FORMAT_DB_DATE);
            
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_CONTRACTS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            //Start the db transaction
            Portal_Model::beginTransaction();

            //add ag code to where
            $where = [
                'tab_module_code' => $data['tab_module']
            ];

            $tab_module_details   = $this->renewal_model->get_tab_module($where);

            if(EMPTY($data['security']))
            {
                $audit_action = [AUDIT_UPDATE];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Renewal Contract');

                //Get previous details
                $contract_id  = $data['contract_id'];

                $where        = ['contract_id' => $contract_id];
                $prev_detail  = [ $this->renewal_model->get_details_for_audit($table, $where) ];

                $where              = ['contract_id' => $contract_id];
                $fields             = array('*');
                $contract_details   = $this->renewal_model->get_contract($where, $fields);

                //Starts
                if($contract_details['contract_code']){

                    $contract_count = $this->renewal_model->check_contracts($contract_details['contract_code']);
                }
                
                if( $contract_count['cnt'] > 0 ){
                    throw new Exception($this->lang->line('contract_exist').' for '.$contract_details['contract_code']);
                }
                //Ends

                $where  = array('contract_id' => $contract_id);

                $update = array(
                    'contract_status_code'  => CONTRACT_FOR_RENEWAL,
                    'renewal_flag'          => NOT_RENEWED_FLAG,
                    'recommended_date_from' => $data['recommended_date_from'],
                    'recommended_date_to'   => $data['recommended_date_to'],
                    'recommended_payment_term_code' => $data['payment_terms'],
                    'modified_by'           => $this->session->userdata('user_id'),
                    'modified_date'         => $now
                );

                $this->renewal_model->update_contract($where, $update);

                $insert = array(
                    'vendor_code'           => $contract_details['vendor_code'],
                    'org_code'              => $contract_details['org_code'],
                    //'contract_code'         => $contract_details['contract_code'],
                    //'contract_status_code'  => CONTRACT_FOR_RENEWAL,
                    'date_from'             => $contract_details['date_from'],
                    'date_to'               => $contract_details['date_to'],
                    'original_contract_id'  => !EMPTY($contract_details['original_contract_id']) ? $contract_details['original_contract_id'] : $contract_details['contract_id'],
                    'reference_contract_id' => $contract_details['contract_id'],
                    'site_id'               => $data['store_name'],
                    'payment_term_code'     => $contract_details['payment_term_code'],
                    'original_start_date'   => !EMPTY($contract_details['original_start_date']) ? $contract_details['original_start_date'] : $contract_details['date_from'],
                    'expiration_date'   => !EMPTY($contract_details['expiration_date']) ? $contract_details['expiration_date'] : $contract_details['date_to'],
                    'recommended_date_from' => $data['recommended_date_from'],
                    'recommended_date_to'   => $data['recommended_date_to'],
                    'recommended_payment_term_code' => $data['payment_terms'],
                    'account_group_code'    => $contract_details['account_group_code'],
                    'renewal_flag'          => RENEWED_FLAG,
                    'created_by'            => $this->session->userdata('user_id'),
                    'created_date'          => $now,
                    'modified_by'           => $this->session->userdata('user_id'),
                    'modified_date'         => $now
                );

                $new_contract_id    = $this->renewal_model->insert_contract($insert, TRUE);

                $where              = array('contract_id' => $new_contract_id);
                $contract_details   = $this->renewal_model->get_contract($where);

                $extra_data = array(
                    'user_id'               => $this->session->user_id,
                    'account_group_code'    => $tab_module_details['ag_code'],
                    'reference_num'         => $contract_details['contract_code'],
                    'reference_id'          => $new_contract_id,
                    'org_code'              => $contract_details['org_code'],
                    'vendor_code'           => $contract_details['vendor_code']
                );

                $workflow_det = $this->pria_workflow->copy_worfklow($tab_module_details['core_workflow_id'], $extra_data);


                $this->tag_task($workflow_det['task_id'], TASK_STATUS_ONGOING, [
                        'reference'     => $new_contract_id,
                        'manual_get'    => ENUM_YES
                ]);

                // Remove update because no changes
                /*//update actual table
                $workflow_fields    = array('reference_id' => $new_contract_id);
                $workflow_where     = array('pria_workflow_id' => $workflow_det['workflow_id']);

                $this->renewal_model->update_workflow($workflow_fields, $workflow_where);*/

                $ag_code            = $tab_module_details['ag_code'];
                $parent_module_code = $this->get_module_code_per_task_ag_code($ag_code);

                $overview_details   = [
                    'transaction_num'    => $contract_details['contract_code'],
                    'transaction_msg'    => $this->lang->line('add_transaction_contract_renewal'),
                    'reference'          => ISSET($new_contract_id) ? $new_contract_id : NULL,
                    'created_by'         => $this->session->userdata('user_id'),
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => $ag_code,
                    'tab_module_code'	 => MODULE_PORTAL_TRANS_LESSORS_RENEWAL,
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $contract_details['contract_code'],
                    'core_workflow_id'   => $tab_module_details['core_workflow_id']
                ];
                
                $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

                //Get current details
                $where        = ['contract_id' => $new_contract_id];
                $curr_detail  = [ $this->renewal_model->get_details_for_audit($table, $where) ];
            }
            else
            {
                $contract_id  = decrypt_id($data['security']);

                $audit_action = [AUDIT_UPDATE];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Renewal Contract');

                $where        = ['contract_id' => $contract_id];
                $prev_detail  = $this->renewal_model->get_details_for_audit($table, $where);

                $update = array(
                    'recommended_date_from' => $data['recommended_date_from'],
                    'recommended_date_to'   => $data['recommended_date_to'],
                    'recommended_payment_term_code' => $data['payment_terms'],
                    'modified_by'           => $this->session->user_id,
                    'modified_date'         => $now
                );

                $this->renewal_model->update_contract($where, $update);

                $curr_detail  = $this->renewal_model->get_details_for_audit($table, $where);

                $contract     = $curr_detail[0];

                $parent_module_code = $this->get_module_code_per_task_ag_code(AG_LESSORS);

                $overview_details   = [
                    'transaction_num'    => $contract['contract_code'],
                    'transaction_msg'    => $this->lang->line('update_transaction_contract_renewal'),
                    'reference'          => $contract_id,
                    'created_by'         => $this->session->user_id,
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => AG_LESSORS,
                    'tab_module_code'    => MODULE_PORTAL_TRANS_LESSORS_RENEWAL,
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $contract['contract_code'],
                    'core_workflow_id'   => $tab_module_details['core_workflow_id']
                ];
                
                $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);
            }

            $this->audit_trail->log_audit_trail($activity, MODULE_PORTAL_TRANS_LESSORS_RENEWAL, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $msg  = $this->lang->line('data_saved');
            $flag   = 1;
            $status = SUCCESS;
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
            'status' => $status
        ]);
    }

    private function _validate()
    {
        try
        {
            $params = get_params();
            $params['tab_module']   = decrypt_id($params['tab_module']);

            //Filters the data inputted by the user
            $params = $this->set_filter( $params )
            ->filter_string('contract_id')
            ->filter_string('store_name')
            ->filter_string('payment_terms')
            ->filter_date('recommended_date_from')
            ->filter_date('recommended_date_to')
            // ->filter_string('recommended_duration')
            ->filter();
            
            //Define the required fields.
            $required = [
                'payment_terms'         => 'Payment Terms',
                'recommended_date_from' => 'Recommended Date From',
                'recommended_date_to'   => 'Recommended Date To'
                // 'recommended_duration'  => 'Recommended Duration'
            ];

            if(EMPTY($params['security']))
            {
                $required['contract_id']    = 'Contract ID';
                $required['store_name']     = 'Store NameD';
            }

            $constraints['contract_id'] = [
                'data_type'         => 'string',
                'name'              => 'Contract ID'
            ];

            $constraints['store_name'] = [
                'data_type'         => 'string',
                'name'              => 'Store Name'
            ];

            $constraints['payment_terms'] = [
                'data_type'         => 'string',
                'name'              => 'Payment Terms'
            ];

            // $constraints['recommended_duration'] = [
            //     'data_type'         => 'string',
            //     'name'              => 'Recommended Duration'
            // ];

            $constraints['recommended_date_from'] = [
                'data_type'         => 'string',
                'name'              => 'Recommended Date From'
            ];

            $constraints['recommended_date_to'] = [
                'data_type'         => 'string',
                'name'              => 'Recommended Date To'
            ];

            $constraints['tab_module']    = [
                'data_type'         => 'string',
                'name'              => 'Tab Module'
            ];
            
            $start_date = strtotime($params['recommended_date_from']);
            $end_date   = strtotime($params['recommended_date_to']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($start_date > $end_date)
                throw new Exception('Recommended Date from must not be greater than ecommended Date to.');

         /*    if($start_date > $curr_date)
                throw new Exception('Recommended Date from must not be greater than current date.'); */
                
            if($end_date < $curr_date)
                throw new Exception('Recommended Date to must not be less than current date.');

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data = $this->validate_inputs($params, $constraints);

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

    public function get_store_details()
    {
        try
        {
            $params           = get_params();

            $fields           = array('*');

            $contract_details = $this->renewal_model->get_site_contract($params['store'], $fields);

            $two_digit_year   = date('y');

            $latest_contract  = $this->renewal_model->get_latest_contract_no($two_digit_year);

            $new_contract     = '';

            if(EMPTY($latest_contract))
            {
                $new_contract = 'CON-'.$two_digit_year.'-00001';
            }
            else
            {
                $running_no    = intval(substr($latest_contract, 7, 5)) + 1;
                
                $new_contract  = 'CON-'.$two_digit_year.'-'.str_pad($running_no, 5, '0', STR_PAD_LEFT);
            }

            $store_info      = [
                'new_contract'      => $new_contract,
                'contract_code'     => $contract_details['contract_code'],
                'vendor_name'       => $contract_details['vendor_name'],
                'contract_id'       => $contract_details['contract_id'],
            ];  
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }

        echo json_encode(
            array(
                'store_info'    => $store_info
            )
        );
    }
}