<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Notes By: Gene | On : 2025-10-17
|------------------------------------------------------------------------------------------
| This is the controller for the "(+) Add Transmittal" button in the Transmittal Tab
| 
| 
|------------------------------------------------------------------------------------------
|
|------------------------------------------------------------------------------------------
*/

class Document_transmittal_modal extends Task_Controller 
{
    protected $controller;
    protected $folder;
    protected $module_js;

    // protected $soa_day_from;
    // protected $soa_day_to;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('Pria_workflow');
        $this->load->library('Pria_overview');

        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_TRANSMITTAL;
        
        // $soa_day_from_details   = get_sys_param_val(SYS_PARAM_SOA_PERIOD_DAY, SYS_PARAM_SOA_PERIOD_DAY_FROM);
        // $soa_day_to_details     = get_sys_param_val(SYS_PARAM_SOA_PERIOD_DAY, SYS_PARAM_SOA_PERIOD_DAY_TO);

        $this->load->model($this->folder.'/Document_transmittal_model', 'dt_model'); 
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        // $this->load->model(PORTAL_TRANSACTIONS . '/dr/Delivery_goods_model', 'dgm_model');

        $this->path_task_views .= $this->folder;
        $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

        // $this->soa_day_from     = (ISSET($soa_day_from_details['sys_param_value']))? $soa_day_from_details['sys_param_value']: DEFAULT_SOA_PERIOD_DAY_FROM;
        // $this->soa_day_to       = (ISSET($soa_day_to_details['sys_param_value']))? $soa_day_to_details['sys_param_value']: DEFAULT_SOA_PERIOD_DAY_TO;
    }

    public function modal_add_document_transmittal($tab_module = NULL, $security = NULL)
    {
        try 
        {
            $data = $resources = array();
            
            $resources['load_js']   = array(JS_NUMBER, $this->module_js);
            
            $resources['loaded_init']   = array(
                    'selectize_init();',
                    'datepicker_init();',
                    'DocumentTransmittal.save();'
            );                  
            
            $tab_module_details     = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module]);

            $data['tab_module']     = encrypt_id($tab_module);
            $data['ag_code']        = encrypt_id($tab_module_details['ag_code']);
            $data['security']       = $security;

            $doc_transmittal_id     = (!EMPTY($security))? decrypt_id($security): NULL;

//             $soa_details            = $this->soa_model->get_soa(['soa_id' => $soa_id]);
//             $soa_transmittal        = $this->soa_model->get_soa_transmittal_by_soa_id($soa_id);
//             $soa_drs                = $this->soa_model->get_soa_drs_by_soa_id($soa_id);
//             $soa_org_code           = (ISSET($soa_details['org_code']) AND !EMPTY($soa_details['org_code']))? $soa_details['org_code']: NULL;
//             $soa_vendor_code        = (ISSET($soa_details['vendor_code']) AND !EMPTY($soa_details['vendor_code']))? $soa_details['vendor_code']: NULL;

//             $data['soa_details']    = (is_array($soa_details) AND count($soa_details) > 0)? $soa_details: [];
//             $data['soa_drs']        = (is_array($soa_drs) AND count($soa_drs) > 0)? array_column($soa_drs, 'dr_gr_id'): [];

//             $data['show_po']                = FALSE;
//             $data['show_receipt']           = FALSE;
//             $data['show_central']           = FALSE;
//             $data['show_normal']            = FALSE;
//             $data['show_soa_receipt']       = TRUE;
//             $data['show_soa_doc_receipt']   = TRUE;

//             $po_module              = NULL;
//             $recipient_info         = array();
//             $drs                    = array();

            $data['organizations']  = get_organizations_by_org_type_w_scope($tab_module);            
            // $organizations          = $data['organizations'];

//             $scope_details          = get_scope_details($tab_module);
//             $scope_vendor_code      = $scope_details['vendor_code'];

//             $po_ags                 = array();

//             $user_role_arr          = $this->session->userdata('user_roles');

//             $user_vendors           = array();

//             if(in_array(WORKFLOW_FOR_VENDOR, $user_role_arr))
//             {
//                 $user_vendor    = $this->soa_model->get_data(array('vendor_code'), Portal_Model::PORTAL_TABLE_VENDOR_USERS, TRUE, array('user_id' => $this->session->user_id));

//                 $user_vendors   = ($user_vendor AND COUNT($user_vendor) > 0)? array_column($user_vendor, 'vendor_code'): array();
//             }

//             switch ($tab_module_details['ag_code']) {
//                 case AG_GOODS:
//                 case AG_GOODS_BAVI:
//                 case AG_GOODS_BFFI:
//                 case AG_GOODS_MARINADES:
//                     $data['show_po']                = TRUE;
//                     // $data['show_central']        = TRUE;

//                     if($tab_module_details['ag_code'] == AG_GOODS_MARINADES)
//                     {
//                         $po_module  = MODULE_PORTAL_TRANS_GOODS_M_PO;
//                         $po_ags = array(AG_GOODS_MARINADES);
//                     }
//                     else
//                     {
//                         $po_module  = MODULE_PORTAL_TRANS_GOODS_G_PO;
//                         $po_ags = array(AG_GOODS_BAVI);
//                     }
//                 break;

//                 case AG_FORWARDERS:
//                     $data['show_receipt']           = TRUE;

//                     $params = array();
//                     if (isset($_REQUEST['drs'])) {
//                         $params                     = get_params();
//                     }

//                     //delivery receipt number
//                     $where = array(
//                         'account_group_code'    => $tab_module_details['ag_code']
//                     );

//                     if(EMPTY($security))
//                     {
//                         $where['dr_status']     = 'IS NULL';
//                     }
//                     else
//                     {
//                         $other_drs  = $this->soa_model->get_used_dr(['soa_id' => ['!=' => $soa_id], 'dr_gr_id' => 'IS NOT NULL'], ['dr_gr_id']);
//                         $other_drs  = (is_array($other_drs) AND count($other_drs) > 0)? array_column($other_drs, 'dr_gr_id'): [];

//                         $where['dr_gr_id']      = ['NOT IN', $other_drs];
//                     }

//                     if( ! EMPTY($params['drs']))
//                     {
//                         $drs_info   = $this->soa_model->get_data(['org_code', 'vendor_code'], Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT, FALSE, ['dr_gr_id' => $params['drs'][0]['dr_gr_id']]);
//                         $drs_org    = (ISSET($drs_info['org_code']) AND !EMPTY($drs_info['org_code']))? $drs_info['org_code']: NULL;
//                         $drs_vendor = (ISSET($drs_info['vendor_code']) AND !EMPTY($drs_info['vendor_code']))? $drs_info['vendor_code']: NULL;

//                         $other_drs  = $this->soa_model->get_used_dr(['dr_gr_id' => 'IS NOT NULL'], ['dr_gr_id']);
//                         $other_drs  = (is_array($other_drs) AND count($other_drs) > 0)? array_column($other_drs, 'dr_gr_id'): [];

//                         $where['dr_gr_id']      = ['NOT IN', $other_drs];
//                         $data['selected_drs']   = array_column($params['drs'], 'dr_gr_id');
//                         $data['w_selected_dr']  = TRUE;
//                     }

//                     if(!EMPTY($soa_vendor_code))
//                     {
//                         $where['vendor_code']   = $soa_vendor_code;
//                     }
//                     else if(!EMPTY($drs_vendor))
//                     {
//                         $where['vendor_code']   = $drs_vendor;
//                     }
//                     else
//                     {
//                         if(COUNT($user_vendors) > 0)
//                         {
//                             $where['vendor_code']   = array('IN', $user_vendors);
//                         }
//                     }

//                     if(!EMPTY($soa_org_code))
//                     {
//                         $where['org_code']      = $soa_org_code;
//                     }
//                     else if(!EMPTY($drs_org))
//                     {
//                         $where['org_code']      = $drs_org;
//                     }
                    
//                     $drs            = $this->soa_model->get_all_delivery_goods_receipts($where, ['dr_gr_id', 'dr_num', 'org_code', 'vendor_code']);

//                     $orgs           = array_unique(array_column($drs, 'org_code'));
//                     $org_code       = $orgs[0];

//                     $vends          = array_unique(array_column($drs, 'vendor_code'));
//                     $vendor_code    = (!EMPTY($soa_vendor_code))? $soa_vendor_code: $vends[0];

//                     $scope_vendor_code  = $vendor_code;
                    
                // $organizations  = $this->soa_model->get_organizations(['org_code' => array('IN', $orgs)], ['org_code', 'name']);

                // $data['organizations'] = $organizations;
//                     /* $data['orgs'] = array_unique(array_column($drs, 'org_code'));
//                     $data['vend'] = array_unique(array_column($drs, 'vendor_code')); */

//                 break;

//                 /*case AG_INBOUND_NORMAL:
//                     // $data['show_central']       = TRUE;
//                     // $data['show_normal']        = TRUE;
//                 break;

//                 case AG_INBOUND_CENTRAL:
//                     // $data['show_central']       = TRUE;
//                     // $data['show_normal']        = TRUE;
//                 break;*/

//                 case AG_OUTBOUND:
//                     // $data['show_central']       = TRUE;
//                 break;

//                 case AG_TOLL_PARTNERS:
//                 case AG_INBOUND_CENTRAL:
//                 case AG_FEEDMILL:
//                 case AG_MANPOWER:
//                     $data['show_soa_receipt']           = FALSE;
//                     $data['show_soa_doc_receipt']       = FALSE;
//                 break;

//                 default:
//                     # code...
//                     // $data['show_central']       = TRUE;
//                 break;
//             }

//             if(in_array(WORKFLOW_FOR_VENDOR, $user_role_arr)){
//                 $data['show_normal']        = TRUE;
//                 $data['show_central']       = FALSE;
//             }else{
//                 $data['show_normal']        = FALSE;
//                 $data['show_central']       = TRUE;
//             }
           
//             $vendors         = [];

//              /*  
//                 $data['organizations']  = $this->get_organizations_by_org_type_w_scope($tab_module);
//                 $data['organizations']  = get_organizations_by_org_type_w_scope($tab_module);
                
//                 $organizations = $data['organizations'];  
//             */
            
           
//            // print_var_export($scope_details); die;
//             $ag_codes               = $this->get_ag_per_tab_module($tab_module);
//             $result                 = [];

//             if(COUNT($data['organizations']) == 1 || !EMPTY($soa_org_code))
//             {
//                 $org_code   = !EMPTY($soa_org_code)? $soa_org_code: $organizations[0]['org_code'];

//                 $vendors    = $this->soa_model->get_vendor_by_org_code_arr_and_ag_arr($ag_codes, $org_code, $scope_vendor_code, ['a.vendor_code', 'a.vendor_name']);   

//                 // $result  = $this->soa_model->get_bavi_recipients_by_org($organizations[0]['org_code']);
//                 // $data['recipient_select'] = $result;

//                 $result     = $this->soa_model->get_finance_recipient($org_code);
//             }

//             //Added by Christian
//             /* $where      = array(
//                 'finance_flag' => FINANCE_YES_FLAG
//             );
//             $result     = $this->soa_model->get_finance_recipient($where);
//  */
//             $data['recipient_select'] = $result;
//             //Ends

            // $data['vendors'] = $vendors;

//             //purchase order references
//             if($data['show_po'])
//             {
//                 $org_codes             = (count($data['organizations']) > 0)? array_column($data['organizations'], 'org_code'): array();
//                 $data['po_references'] = $this->soa_model->get_approved_released_po($po_module, $po_ags, $user_vendors, $org_codes);
//             }

//             $data['delivery_receipts'] = $drs;
            
            $modal              = "modals/add_document_transmittal";

            /* print_var_export($data['recipient_select']); die; */

            $this->load->view($modal, $data);
            $this->load_resources->get_resource($resources);

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
    }


    public function process()
    {
        try
        {
            $flag         = 0;
            $status       = ERROR;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            $curr_datetime= date(FORMAT_DB_DATETIME);
            $data         = $this->_validate();
            // print_var_export($data); die;
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_DOCUMENT_TRANSMITTALS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            //Start the db transaction                
            Portal_Model::beginTransaction();   
            //If reference id is empty action will be update

            $where = [
                'ag_code'         => $data['ag_code'],
                'tab_module_code' => $data['tab_module']
            ];

            $tab_module_details   = $this->dt_model->get_tab_module($where); // this method is from Portal_Model. Extended by Document_transmittal_model

            // [tab_module_code] => PORTAL_DOCUMENT_TRANSMITTAL_TRANSMITTAL
            // [core_workflow_id] => 25
            // [ag_code] => DTR
            // [root_module] => PORTAL_TRANSACTIONS
            // [parent_module_code] => PORTAL_DOCUMENT_TRANSMITTAL
            // [transaction_tab] => DTR
            // [to_tab_module_code] => 

            if(EMPTY($data['security']))
            {   

                // if(false){ //skip this check for now
                //check if document transmittal number already exist
                $where      = array( 'document_tracer_batch_number' => $data['document_tracer_batch_number'], 'vendor_code' => $data['vendor']);
                $has_doc_transmittals    = $this->dt_model->get_document_transmittals($where);

                if($has_doc_transmittals){
                    throw new Exception('Document transmittal number already exists.');
                }
                // }

                if(!EMPTY($data['document_tracer_batch_number'])){ 
                    $audit_action = [AUDIT_INSERT];

                    $prev_detail  = [];
                    $activity     = sprintf($this->lang->line('audit_trail_add'), ' Document Transmittal report');

                    //add ag code to where            
                    $extra_data = array(
                        'user_id'               => $this->session->user_id,
                        'account_group_code'    => $tab_module_details['ag_code'],
                        'workflow_for_type'     => WORKFLOW_FOR_VENDOR,
                        'workflow_for_id'       => $data['vendor'],
                        'reference_num'         => $data['document_tracer_batch_number'],
                        'org_code'              => $data['business_center'],
                        'vendor_code'           => $data['vendor']
                    );

                    // ["user_id"]=>
                    // string(4) "9444"
                    // ["account_group_code"]=>
                    // string(3) "DTR"
                    // ["workflow_for_type"]=>
                    // string(6) "VENDOR"
                    // ["workflow_for_id"]=>
                    // string(7) "1000000"
                    // ["reference_num"]=>
                    // NULL
                    // ["org_code"]=>
                    // string(4) "1020"
                    // ["vendor_code"]=>
                    // string(7) "1000000"

                    // Creates the workflow runtime
                    $workflow_det = $this->pria_workflow->copy_workflow(
                        $tab_module_details['core_workflow_id'],
                        $extra_data
                    );

                    // ["workflow_id"]=>
                    // string(5) "44987"
                    // ["task_id"]=>
                    // string(6) "215116"
                    // ["core_workflow_task_id"]=>
                    // string(2) "87"
                    
                    // Creates the document transmittal record
                    $insert  = array(
                        'transmittal_date'               => $data['transmittal_date'],
                        'document_tracer_batch_number'   => $data['document_tracer_batch_number'],
                        'org_code'                       => $data['business_center'],
                        'vendor_code'                    => $data['vendor'],
                        'document_transmittal_date'      => $data['document_transmittal_date'],
                        'number_of_documents'            => $data['number_of_documents'],
                        'account_group_code'             => $tab_module_details['ag_code'],
                        'date_from'                      => $data['date_from'],
                        'date_to'                        => $data['date_to'],

                        // 'soa_amount'            => $data['soa_amount'],
                        // 'po_reference_number' => ISSET($data['po_reference_number']) ? $data['po_reference_number'] : NULL,
                        //'delivery_receipt_number' => ISSET($data['delivery_receipt_number']) ? $data['delivery_receipt_number'] : NULL,
                        // 'recipient_id'          => $data['soa_document_recipient'], //finance in-charge

                        'transmittal_document_requestor' => $data['transmittal_document_requestor'], //added by christian
                        'created_by'                     => $this->session->user_id,
                        'created_date'                   => $now
                    );

                    // print_var_export($insert); die;


                    $document_transmittal_id = $this->dt_model->insert_document_transmittal($insert);

                    //insert to pria_references table
                    // IF(ISSET($data['po_reference_number'])){
                    //     foreach($data['po_reference_number'] AS $po_key => $po_id)
                    //     {
                    //         $fields = array('soa_id' => $soa_id, 'po_id' => $po_id);
                    //         $this->soa_model->insert_soa_transmittals($fields);
                    //     }
                    // }

                    //insert to pria_references table
                    // IF(ISSET($data['delivery_receipt_number']))
                    // {
                    //     //print_var_export($data['deliver']);
                    //     foreach($data['delivery_receipt_number'] as $d)
                    //     {
                    //         $fields = array('soa_id' => $soa_id, 'dr_gr_id' => $d); 

                    //         $this->soa_model->insert_soa_transmittals($fields);     

                    //         $this->dgm_model->update_delivery_goods_receipt(['dr_gr_id' => $d], ['dr_status' => DR_FOR_SOA]);
                    //     }
                    //    /*  $fields = array('soa_id' => $soa_id, 'dr_gr_id' => $data['delivery_receipt_number']);
                    //     $this->soa_model->insert_soa_transmittals($fields); */
                    // }

                    //Added by Christian Nov 07, 2019 update starts date in pria_workflow table
                    //update actual table
                    //Starts
                    $pria_workflow_details  = $this->pwm->get_workflow(['pria_workflow_id' => $workflow_det['workflow_id']]);
                    $expected_end_date      = date(FORMAT_DB_DATETIME, strtotime($curr_datetime.' + '.floor($pria_workflow_details['tat']).' days'));
                    //Ends

                    $workflow_fields        = array(
                        'reference_id'      => $document_transmittal_id, 
                        'start_date'        => $curr_datetime, 
                        'expected_end_date' => $expected_end_date
                    );

                    $workflow_where = array('pria_workflow_id' => $workflow_det['workflow_id']);

                    $this->dt_model->update_workflow($workflow_fields, $workflow_where);

                    $this->tag_task($workflow_det['task_id'], TASK_STATUS_ONGOING, [
                        'reference' => $document_transmittal_id, 'manual_get' => ENUM_YES
                    ]);

                    $where          = ['document_transmittal_id' => $document_transmittal_id];
                    $curr_detail    = [ $this->dt_model->get_details_for_audit( $table, $where) ];

                    $msg            = $this->lang->line('data_saved');
                }

                $parent_module_code = $this->get_module_code_per_task_ag_code($data['ag_code']);
                // print_var_export($parent_module_code, $data['ag_code']); die;
                $overview_type      = OVERVIEW_TYPE_ADD_TRANSACTION;

                $overview_details   = [  
                    'transaction_num'                  => $data['document_tracer_batch_number'],
                    'transaction_msg'                  => $this->lang->line('add_transaction_doc_transmittal'),
                    'reference'                        => ISSET($document_transmittal_id) ? $document_transmittal_id: NULL,
                    'created_by'                       => $this->session->userdata('user_id'),
                    'created_date'                     => date('Y-m-d H:i:s'),
                    'account_group_code'               => $tab_module_details['ag_code'],
                    'tab_module_code'                  => $data['tab_module'],
                    'parent_module_code'               => $parent_module_code,
                    'keyword'                          => $data['document_tracer_batch_number'],
                    'core_workflow_id'                 => $tab_module_details['core_workflow_id']
                ];

                // print_var_export($overview_details); die;

                $this->pria_overview->log_overview($parent_module_code, $overview_type, $overview_details);
                $this->audit_trail->log_audit_trail($activity, $data['tab_module'], $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);
            }
            // else
            // {   
            //     $soa_id = decrypt_id($data['security']);

            //     $audit_action = [AUDIT_UPDATE];

            //     $soa    = $this->soa_model->get_soa(['soa_id' => $soa_id]);

            //     $prev_detail  = [$soa];

            //     $update = array(
            //             'soa_num'               => $data['soa_num'],
            //             'soa_date'              => $data['soa_date'],
            //             'vendor_code'           => $data['vendor'],
            //             'org_code'              => $data['business_center'],
            //             'soa_type'              => $data['soa_type'],
            //             'account_group_code'    => $tab_module_details['ag_code'],
            //             'submission_date'       => $data['soa_date_submitted'],
            //             'date_to'               => $data['date_to'],
            //             'date_from'             => $data['date_from'],
            //             'soa_amount'            => $data['soa_amount'],
            //             'recipient_id'          => $data['soa_document_recipient'], //finance in-charge
            //             'doc_recipient'         => $data['doc_recipient'], //added by christian
            //             'modified_by'           => $this->session->user_id,
            //             'modified_date'         => $now
            //     );

            //     $this->soa_model->update_soa(['soa_id' => $soa_id], $update);

            //     // $prev_drs  = $this->soa_model->get_used_dr(['soa_id' => $soa_id, 'dr_gr_id' => 'IS NOT NULL'], ['dr_gr_id']);
            //     // $prev_drs  = (is_array($prev_drs) AND count($prev_drs) > 0)? array_column($prev_drs, 'dr_gr_id'): [];

            //     // $this->soa_model->update_soa_drs(['dr_status' => NULL], ['dr_gr_id' => ['IN', $prev_drs]]);

            //     // $this->soa_model->delete_soa_drs(['soa_id' => $soa_id, 'dr_gr_id' => 'IS NOT NULL']);

            //     // if(ISSET($data['delivery_receipt_number']))
            //     // {
            //     //     foreach($data['delivery_receipt_number'] as $d)
            //     //     {
            //     //         $fields = array('soa_id' => $soa_id, 'dr_gr_id' => $d); 

            //     //         $this->soa_model->insert_soa_transmittals($fields);     

            //     //         $this->dgm_model->update_delivery_goods_receipt(['dr_gr_id' => $d], ['dr_status' => DR_FOR_SOA]);
            //     //     }
            //     // }

            //     $soa    = $this->soa_model->get_soa(['soa_id' => $soa_id]);

            //     $curr_detail  = [$soa];

            //     $activity     = sprintf($this->lang->line('audit_trail_update'), ' Document Transmittal report');

            //     $this->audit_trail->log_audit_trail($activity, $data['tab_module'], $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);
            // }

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
            $params                 = get_params();

            $params['tab_module']   = decrypt_id($params['tab_module']);
            $params['ag_code']      = decrypt_id($params['ag_code']);

            //check if what soa type: central/normal is selected in inbound soa module
            // if( $params['tab_module'] == MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA){
            //     if($params['soa_type'] == SOA_CENTRAL){
            //         $params['ag_code'] = AG_INBOUND_CENTRAL;
            //     }else{
            //         $params['ag_code'] = AG_INBOUND_NORMAL;
            //     }
            // }

            // $tab_module_details = $this->tm_model->get_tab_module(['tab_module_code' => $params['tab_module']]);

            // switch ($tab_module_details['ag_code']) {
            //     case AG_GOODS_BAVI:
            //     case AG_GOODS_BFFI:
            //     case AG_GOODS_MARINADES:
            //    // case AG_SOA_BASED:
            //         //Filters the data inputted/uploaded by the user
            //         $params = $this->set_filter( $params )
            //         ->filter_date('soa_date')
            //         ->filter_string('soa_type')
            //         ->filter_string('soa_num')
            //         ->filter_string('vendor')
            //         ->filter_string('business_center')
            //         ->filter_date('date_from')
            //         ->filter_date('date_to')
            //         ->filter_float('soa_amount')
            //         ->filter_date('soa_date_submitted')
            //         ->filter_string('po_reference_number')
            //         ->filter();

            //         $required = [
            //             'soa_date'                  => 'SOA File',
            //             // 'soa_type'                  => 'SOA Type',
            //             'soa_num'                   => 'SOA Number',
            //             'vendor'                    => 'Vendor',
            //             'business_center'           => 'Business Center',
            //             'soa_date_submitted'        => 'SOA Date Submitted',
            //             'date_from'                 => 'Period Covered',
            //             'date_to'                   => 'Period Covered',
            //             'soa_amount'                => 'SOA Amount',
            //             'soa_document_recipient'    => 'Finance in-charge',
            //             'po_reference_number'       => 'PO Reference'
            //         ];

            //         $constraints['po_reference_number']    = [
            //             'data_type'         => 'string',
            //             'name'              => 'PO Reference Number'
            //         ];

            //         break;
            //     case AG_FORWARDERS:
            //         //Filters the data inputted/uploaded by the user
            //         $params = $this->set_filter( $params )
            //         ->filter_date('soa_date')
            //         ->filter_string('soa_type')
            //         ->filter_string('soa_num')
            //         ->filter_string('vendor')
            //         ->filter_string('business_center')
            //         ->filter_date('date_from')
            //         ->filter_date('date_to')
            //         ->filter_float('soa_amount')
            //         ->filter_date('soa_date_submitted')
            //         ->filter_string('soa_document_recipient')
            //         ->filter_string('delivery_receipt_number')
            //         ->filter();

            //         $required = [
            //             'soa_date'                  => 'SOA File',
            //             // 'soa_type'                  => 'SOA Type',
            //             'soa_num'                   => 'SOA Number',
            //             'vendor'                    => 'Vendor',
            //             'business_center'           => 'Business Center',
            //             'soa_date_submitted'        => 'SOA Date Submitted',
            //             'date_from'                 => 'Period Covered',
            //             'date_to'                   => 'Period Covered',
            //             'soa_amount'                => 'SOA Amount',
            //             'soa_document_recipient'    => 'Finance in-charge',
            //             'delivery_receipt_number'   => 'Delivery Receipt Number'
            //         ];

            //         $constraints['delivery_receipt_number']    = [
            //             'data_type'         => 'string',
            //             'name'              => 'Delivery Receipt Number'
            //         ];
            //     break;

            //     default:

            //         //Filters the data inputted/uploaded by the user
            //         $params = $this->set_filter( $params )
            //         ->filter_date('soa_date')
            //         ->filter_string('soa_type')
            //         ->filter_string('soa_num')
            //         ->filter_string('vendor')
            //         ->filter_string('business_center')
            //         ->filter_date('date_from')
            //         ->filter_date('date_to')
            //         ->filter_float('soa_amount')
            //         ->filter_date('soa_date_submitted')
            //         ->filter_string('soa_document_recipient')
            //         ->filter_string('doc_recipient')
            //         ->filter();

            //         //Define the required fields.
            //         $required = [
            //             'soa_date'                  => 'SOA File',
            //            // 'soa_type'                  => 'SOA Type',
            //             'soa_num'                   => 'SOA Number',
            //             'vendor'                    => 'Vendor',
            //             'business_center'           => 'Business Center',
            //             'soa_date_submitted'        => 'SOA Date Submitted',
            //             'date_from'                 => 'Period Covered',
            //             'date_to'                   => 'Period Covered',
            //             'soa_amount'                => 'SOA Amount'
            //         ];

            //         $user_role_arr = $this->session->userdata('user_roles');

            //         if(!in_array($params['ag_code'], array(AG_TOLL_PARTNERS, AG_INBOUND_CENTRAL, AG_FEEDMILL, AG_MANPOWER)))
            //         {
            //             $required['soa_document_recipient'] = 'Finance in-charge';
            //             $required['doc_recipient']          = 'SOA Document Recipient';
            //         }

            //      break;
            // }


            $constraints['transmittal_date']    = [
                'data_type'         => 'date',
                'name'              => 'Transmittal Date'
            ];

            $constraints['document_tracer_batch_number']    = [
                'data_type'         => 'string',
                'name'              => 'Document Tracer Batch Number'
            ];

            // $constraints['soa_type']    = [
            //     'data_type'         => 'string',
            //     'name'              => 'SOA Type'
            // ];

            // $constraints['soa_num']    = [
            //     'data_type'         => 'string',
            //     'name'              => 'SOA Number'
            // ];

            $constraints['business_center']    = [
                'data_type'         => 'string',
                'name'              => 'business_center'
            ];

            $constraints['vendor']    = [
                'data_type'         => 'string',
                'name'              => 'Vendor'
            ];

            $constraints['document_transmittal_date']    = [
                'data_type'         => 'date',
                'name'              => 'Document Transmittal Date'
            ];

            $constraints['number_of_documents']    = [
                'data_type'         => 'amount',
                'name'              => 'Number of Documents',
                'max'               => 99999999.99
            ];

            $constraints['date_from']    = [
                'data_type'         => 'date',
                'name'              => 'Week period from'
            ];

            $constraints['date_to']    = [
                'data_type'         => 'date',
                'name'              => 'Week period from'
            ];

            $constraints['transmittal_document_requestor']    = [
                'data_type'         => 'string',
                'name'              => 'Document Requestor'
            ];

            // $constraints['doc_recipient']    = [
            //     'data_type'         => 'string',
            //     'name'              => 'SOA Document Recipient'
            // ];

            $constraints['tab_module']    = [
                'data_type'         => 'string',
                'name'              => 'Module Tab'
            ];

            $constraints['ag_code']    = [
                'data_type'         => 'string',
                'name'              => 'Account Group Code'
            ];
            
            /* Validate the required fields */
            // $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            // if (preg_match('#[0-9]#',$data['soa_num'])){

            // }else{
            //     throw new Exception('SOA number must have a numeric character.');
            // }


            // if (strtotime($data['date_from']) > strtotime($data['date_to'])){
            //     throw new Exception('Week period from cannot be later than week period to .');
            // }


            //Validate SOA week period needs to be monday to friday if central.
            /*
            if($data['soa_type'] == SOA_CENTRAL){
                if($this->soa_day_from != date('l', strtotime($data['date_from'])))
                {
                    throw new Exception(sprintf($this->lang->line('invalid_value_against_value'), "Week Period From", "Week Period From", $this->soa_day_from));
                }

                if($this->soa_day_to != date('l', strtotime($data['date_to'])))
                {
                    throw new Exception(sprintf($this->lang->line('invalid_value_against_value'), "Week Period To", "Week Period To", $this->soa_day_to));
                }
            }
            */

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

    public function get_business_center()
    {
        try
        {
            $params     = get_params();

            $options    = $this->soa_model->get_all_business_centers($params['vendor']);

        }
        catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);  
        }

        echo json_encode($options);
    }

    public function get_vendors()
    {
        try
        {
            $params     = get_params();
            $options    = [];   

            $tab_module = decrypt_id($params['tab_module']);
            //Value : PORTAL_DOCUMENT_TRANSMITTAL_TRANSMITTAL

            $tab_module_details     = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module],['*'],[],TRUE);

            $ag_codes = array_column($tab_module_details,'ag_code');

            if( ! EMPTY($params['business_center']))
            {
                $scope_details  = get_scope_details($tab_module);

                // print_var_export($scope_details);
                $options        = $this->dt_model->get_vendor_by_org_code_arr_and_ag_arr($ag_codes, $params['business_center'], $scope_details['vendor_code'], ['a.vendor_code as value', 'CONCAT("[", a.vendor_code, "] ", a.vendor_name) as text']);

                // die($options);
                $json_results   = [];

                if(is_array($options) AND count($options))
                {
                    foreach ($options as $key => $option)
                    {
                        $json_results[] = ['value' => $option['value'], 'text' => htmlspecialchars_decode($option['text'], ENT_QUOTES)];
                    }
                }
            }

        }
        catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);  
        }

        echo json_encode($json_results, JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    public function get_recipients()
    {
        try
        {
            $params     = get_params();

            $options    = [];
            
            $result     = $this->soa_model->get_finance_recipient($params['org_code']);

            foreach($result as $recipient)
                $options[] = ['text' => '['.$recipient['role_names'].']-'.$recipient['fullname'], 'value' => $recipient['user_id']];

/*             $result     = $this->soa_model->get_bavi_recipients_by_org($params['org_code']);

            foreach($result as $r)
                $options[] = ['text' => '['.$r['role_code'].']-'.$r['fullname'], 'value' => $r['user_id']]; */
        }
         catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);  
        }

        echo json_encode($options);
    }

    // public function get_drs()
    // {
    //     try
    //     {
    //         $params     = get_params();

    //         $where      = array(
    //                 'account_group_code'    => decrypt_id($params['ag_code']),
    //                 'dr_status'             => 'IS NULL'
    //         );

    //         if(!EMPTY($params['org_code']))
    //         {
    //             $where['org_code']  = $params['org_code'];
    //         }

    //         if(!EMPTY($params['vendor_code']))
    //         {
    //             $where['vendor_code']  = $params['vendor_code'];
    //         }
    //         else
    //         {
    //             $where['vendor_code']  = "IS NULL";
    //         }

    //         $options    = [];
            
    //         $result     = $this->soa_model->get_all_delivery_goods_receipts($where, ['dr_gr_id', 'dr_num', 'org_code', 'vendor_code']);

    //         foreach($result as $dr)
    //             $options[] = ['text' => $dr['dr_num'], 'value' => $dr['dr_gr_id']];
    //     }
    //      catch(PDOException $e)
    //     {
    //         $msg    = $this->get_user_message($e);
    //     }
    //     catch(Exception $e)
    //     {
    //         $msg    = $this->rlog_error($e, TRUE);  
    //     }

    //     echo json_encode($options);
    // }

    // public function get_pos()
    // {
    //     try
    //     {
    //         $params         = get_params();

    //         $soa_tab        = decrypt_id($params['tab_module']);
    //         $po_ags         = array(decrypt_id($params['ag_code']));

    //         $po_tab         = NULL;

    //         if($soa_tab == MODULE_PORTAL_TRANS_GOODS_G_SOA)
    //         {
    //             $po_tab     = MODULE_PORTAL_TRANS_GOODS_G_PO;
    //         }
    //         else if($soa_tab == MODULE_PORTAL_TRANS_GOODS_M_SOA)
    //         {
    //             $po_tab     = MODULE_PORTAL_TRANS_GOODS_M_PO;
    //         }

    //         if(!EMPTY($params['org_code']))
    //         {
    //             $orgs       = array($params['org_code']);
    //         }

    //         if(!EMPTY($params['vendor_code']))
    //         {
    //             $vendors    = array($params['vendor_code']);
    //         }

    //         $options    = [];
            
    //         $result     = $this->soa_model->get_approved_released_po($po_tab, $po_ags, $vendors, $orgs);

    //         foreach($result as $dr)
    //             $options[] = ['text' => $dr['po_num'], 'value' => $dr['po_id']];
    //     }
    //      catch(PDOException $e)
    //     {
    //         $msg    = $this->get_user_message($e);
    //     }
    //     catch(Exception $e)
    //     {
    //         $msg    = $this->rlog_error($e, TRUE);  
    //     }

    //     echo json_encode($options);
    // }
}