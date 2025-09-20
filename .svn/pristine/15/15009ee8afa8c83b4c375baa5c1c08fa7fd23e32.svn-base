<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Po extends Task_Controller
{
    protected $controller;
    protected $folder;
    protected $module_js;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Pria_workflow');
        $this->load->library('Pria_overview');

        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_PURCHASE_ORDERS;
        $this->folder_boq       = FOLDER_BOQ;

        $this->load->model($this->folder.'/Po_model', 'po_model');
        $this->load->model($this->folder_boq.'/Boq_model', 'boq_model');
        $this->load->model(FOLDER_PURCHASE_REQUESTS.'/Pr_model', 'pr_model');
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        $this->path_task_views .= $this->folder;
        $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

        $this->load->model(FOLDER_BOQ.'/boq_model', 'bq_model');

        $this->vendor_ag_code_goods = [AG_GOODS_BAVI, AG_GOODS_BFFI, AG_GOODS_MARINADES];
    }

    public function modal_add_po($tab_module = NULL)
    {
        try
        {

            $data = $resources = array();

            $resources['load_js']   = array(JS_NUMBER, $this->module_js);

            $resources['loaded_init']  = array(
                'selectize_init();',
                'datepicker_init();',
                'Po.save();',
                'Po.initModal();',
            );

            $data['tab_module'] = encrypt_id($tab_module);

            //get tab module details
            $where              = array('tab_module_code' => $tab_module);
            $pria_tab_det       = $this->po_model->get_tab_module($where, array('*'), array(), TRUE);

            //account groups
            $where              = array('dashboard_flag'  => YES_FLAG);
            $account_groups     = $this->po_model->get_all_account_groups($where);

            $pria_tab_det       = array_column($pria_tab_det,'ag_code');

            foreach ($account_groups as $account_group) {
                IF(in_array($account_group['account_group_code'], $pria_tab_det)){
                    $data['account_groups'][] = $account_group;
                }
            }

            $data['show_pr_field']  = TRUE;
            $data['show_pr_text']   = FALSE;
            $data['show_boq_ref']   = FALSE;
            $data['show_project_type']  = FALSE;

            if($pria_tab_det[0] == AG_CONTRACTORS)
            {
                //$data['show_pr_text'] = TRUE;
                $data['show_boq_ref']  = TRUE;
                $data['show_project_type']  = TRUE;
                // $data['show_pr_field'] = FALSE;
            }

            $vendors                = [];
           // $data['organizations']  =  $this->get_organizations_by_org_type_w_scope($tab_module);
            $organizations          =  get_organizations_by_org_type_w_scope($tab_module);
            $data['organizations']  = $organizations;

            if(COUNT($data['organizations']) == 1)
                $vendors        = $this->po_model->get_vendor_by_org_code_arr($organizations[0]['org_code'], $scope_details['vendor_code'], ['a.vendor_code', 'a.vendor_name']);

            $data['vendors'] = $vendors;

            // //BOQ reference
            // $data['boqs']    = $this->po_model->get_all_boqs(['status_code' => STATUS_COMPLETED, 'inhouse' => ENUM_NO]);

            $data['boqs'] = $this->boq_model->get_boqs([
                'additional_flag' => 0,
                'status_code' => STATUS_COMPLETED,
                'inhouse' => ENUM_NO
            ]);

            $data['purchase_requisitions'] = $this->pr_model->get_prs();

            $modal           = "modals/add_po";
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

    protected function setup_ag_contractors_data($data) {
        try
        {
            //Add PR because as of writing, contractors doesn't require a PR.
            // $fields = ['account_group_code' => $data['account_group']];
            // $fields['pr_num'] = $data['purchase_request'];
            // $data['purchase_request'] = $this->pr_model->insert_pr($fields, TRUE);
            foreach($data['purchase_request'] AS $pr_key => $pr_id)
            {
                $this->boq_model->insert_boq_pr([
                    'boq_id' => $data['boq_ref'],
                    'pr_id' => $pr_id
                ]);
            }

            return $data;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function process()
    {
        try
        {
            $flag         = 0;
            $status       = ERROR;

            $data         = $this->_validate();

            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update
            //check if po number already exist
            $where      = array( 'po_num' => $data['po_num']);
            $has_po    = $this->po_model->get_pos($where);
            if($has_po){
                throw new Exception('PO number already exist.');
            }

            //get po documents
            if( !EMPTY($data['po_num'])){
                $audit_action = [AUDIT_INSERT];

                $prev_detail  = [];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' PO report');

                //add ag code to where
                $where = [
                    'ag_code'         => $data['account_group'],
                    'tab_module_code' => $data['tab_module'],
                    'transaction_tab' => APV_REF_TYPE_CODE_PO
                ];

                $tab_module_details   = $this->po_model->get_tab_module($where);

                $extra_data = array(
                    'user_id'               => $this->session->user_id,
                    'account_group_code'    => $tab_module_details['ag_code'],
                    'workflow_for_type'     => WORKFLOW_FOR_VENDOR,
                    'workflow_for_id'       => $data['vendor'],
                    'reference_num'         => $data['po_num'],
                    'org_code'              => $data['business_center'],
                    'vendor_code'           => $data['vendor']
                );

                $workflow_det = $this->pria_workflow->copy_worfklow($tab_module_details['core_workflow_id'], $extra_data);

                $insert = array(
                    'po_num'            => $data['po_num'],
                    'po_date'           => $data['po_date'],
                    'po_amount'         => $data['amount'],
                    'remaining_amount'  => $data['amount'],
                    'po_released_date'  => (!EMPTY($data['released_date']))? $data['released_date']: NULL,
                    'vendor_code'       => $data['vendor'],
                    'org_code'          => $data['business_center'],
                    'created_by'        => $this->session->user_id,
                    'created_date'      => $now,
                    'additional_flag'   => $data['additional_flag']
                );

                $po_id = $this->po_model->insert_po($insert);
            
                $this->tag_task($workflow_det['task_id'], TASK_STATUS_ONGOING, ['reference' => $po_id, 'manual_get' => ENUM_YES]);

                foreach($data['purchase_request'] AS $pr_key => $pr_id)
                {
                    $this->po_model->insert_po_pr(['pr_id' => $pr_id, 'po_id' => $po_id]);
                }


                if($data['account_group'] == AG_CONTRACTORS){
                    $data =  $this->setup_ag_contractors_data($data);
                }

                //update actual table
                $workflow_fields    = array('reference_id' => $po_id);
                $workflow_where     = array('pria_workflow_id' => $workflow_det['workflow_id']);

                $this->po_model->update_workflow($workflow_fields, $workflow_where);

                //Starts
                $curr_sequence_no = 1;

                $workflows    = $this->tm_model->get_appendable_workflow_by_pria_workflow_id($workflow_det['workflow_id']);

                IF(ISSET($workflows[0]['core_workflow_stage_id'])){
                    $curr_sequence_no      =  $this->tm_model->get_stage_seq_where_to_append($tab_module_details['core_workflow_id'], $workflows[0]['core_workflow_stage_id'], $workflow_det['workflow_id']);
                }ELSE{
                    $curr_sequence_no = 1;
                }

                if($data['account_group'] != AG_CONTRACTORS){
                    if($data['account_group'] == AG_GOODS_BAVI OR $data['account_group'] == AG_GOODS_BFFI)
                    {
                       if($data['account_group'] == AG_GOODS_BAVI){
                            $core_workflow_id = CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL;
                       }else{
                            $core_workflow_id = CORE_WORKFLOW_DELIVERY_W_TRANSMITTAL;
                       }
                        $this->pria_workflow->append_stage($workflow_det['workflow_id'], $curr_sequence_no, $core_workflow_id, $this->session->user_id, TRUE, TRUE);
                    }else{
                        $this->pria_workflow->append_stage($workflow_det['workflow_id'], $curr_sequence_no, CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL, $this->session->user_id, TRUE, TRUE);
                    }
                }
                //Ends


                //update actual table
                // $up_fields  = array('current_task_id' => $workflow_det['task_id']);
                // $up_where   = array('po_id' => $po_id);
                // $this->po_model->update_po($up_where, $up_fields);

                $where        = ['po_id' => $po_id];
                $curr_detail  = [ $this->po_model->get_details_for_audit( $table, $where) ];

                $parent_module_code = $this->get_module_code_per_task_ag_code($data['account_group']);

                $overview_details   = [
                    'transaction_num'    => $data['po_num'],
                    'transaction_msg'    => $this->lang->line('add_transaction_po'),
                    'reference'          => ISSET($po_id) ? $po_id : NULL,
                    'created_by'         => $this->session->userdata('user_id'),
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => $tab_module_details['ag_code'],
                    'tab_module_code'	 => $data['tab_module'],
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $data['po_num'],
                    'core_workflow_id'   => $tab_module_details['core_workflow_id']
                ];

                $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

                $msg          = $this->lang->line('data_saved');
            }

            $this->audit_trail->log_audit_trail($activity, $data['tab_module'], $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

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

    public function process_bk()
    {
        try
        {
            $flag         = 0;
            $status       = ERROR;

            $data         = $this->_validate();

            //check if exist *Change by kebs from pr_num to pr_id
            $where          = array('pr_id' => ['IN' => $data['purchase_request']], 'account_group_code' => $data['account_group']);
            $pr_ref_info    = $this->po_model->get_purch_requisitions_multiple($where);

            if(EMPTY($pr_ref_info))
            {
                //Change by kebs because Contractor PO doesn't have PO ref number but we must require to insert in PR table.
                $fields = ['account_group_code' => $data['account_group']];

                if( ! EMPTY($data['purchase_request']))
                    $fields['pr_num'] = $data['purchase_request'];

                $data['purchase_request'] = $this->pr_model->insert_pr($fields, TRUE);
            }


            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_PURCHASE_ORDERS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            //check if po number already exist
            $where      = array( 'po_num' => $data['po_num']);
            $has_po    = $this->po_model->get_pos($where);

            if($has_po){
                throw new Exception('PO number already exist.');
            }

            //if contractor
            if($data['account_group'] == AG_CONTRACTORS){
                $ref_boq_id = $data['boq_ref'];
                $ref_pr_id  = $data['purchase_request'];

                $this->boq_model->insert_boq_pr(array('boq_id' => $ref_boq_id, 'pr_id' => $ref_pr_id));
            }

            //get po documents

            if( !EMPTY($data['po_num'])){
                $audit_action = [AUDIT_INSERT];

                $prev_detail  = [];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' PO report');

                //add ag code to where
                $where = [
                    'ag_code'         => $data['account_group'],
                    'tab_module_code' => $data['tab_module'],
                    'transaction_tab' => APV_REF_TYPE_CODE_PO
                ];

                $tab_module_details   = $this->po_model->get_tab_module($where);

                $extra_data = array(
                    'user_id'               => $this->session->user_id,
                    'account_group_code'    => $tab_module_details['ag_code'],
                    'workflow_for_type'     => WORKFLOW_FOR_VENDOR,
                    'workflow_for_id'       => $data['vendor'],
                    'reference_num'         => $data['po_num'],
                    'org_code'              => $data['business_center'],
                    'vendor_code'           => $data['vendor']
                );

                $workflow_det = $this->pria_workflow->copy_worfklow($tab_module_details['core_workflow_id'], $extra_data);

                $insert = array(
                    'po_num'            => $data['po_num'],
                    'po_date'           => $data['po_date'],
                    'po_amount'         => $data['amount'],
                    'remaining_amount'  => $data['amount'],
                    'po_released_date'  => (!EMPTY($data['released_date']))? $data['released_date']: NULL,
                    'vendor_code'       => $data['vendor'],
                    'org_code'          => $data['business_center'],
                    'created_by'        => $this->session->user_id,
                    'created_date'      => $now
                );

                $po_id = $this->po_model->insert_po($insert);

                foreach($data['purchase_request'] AS $pr_key => $pr_id)
                {
                    $this->po_model->insert_po_pr(['pr_id' => $pr_id, 'po_id' => $po_id]);
                }

                //update actual table
                $workflow_fields    = array('reference_id' => $po_id);
                $workflow_where     = array('pria_workflow_id' => $workflow_det['workflow_id']);

                $this->po_model->update_workflow($workflow_fields, $workflow_where);

                //Starts
                $curr_sequence_no = 1;

                $workflows    = $this->tm_model->get_appendable_workflow_by_pria_workflow_id($workflow_det['workflow_id']);

                IF(ISSET($workflows[0]['core_workflow_stage_id'])){
                    $curr_sequence_no      =  $this->tm_model->get_stage_seq_where_to_append($tab_module_details['core_workflow_id'], $workflows[0]['core_workflow_stage_id'], $workflow_det['workflow_id']);
                }ELSE{
                    $curr_sequence_no = 1;
                }

                if($data['account_group'] != AG_CONTRACTORS){
                    if($data['account_group'] == AG_GOODS_BAVI OR $data['account_group'] == AG_GOODS_BFFI)
                    {
                       if($data['account_group'] == AG_GOODS_BAVI){
                            $core_workflow_id = CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL;
                       }else{
                            $core_workflow_id = CORE_WORKFLOW_DELIVERY_W_TRANSMITTAL;
                       }
                        $this->pria_workflow->append_stage($workflow_det['workflow_id'], $curr_sequence_no, $core_workflow_id, $this->session->user_id, TRUE, TRUE);
                    }else{
                        $this->pria_workflow->append_stage($workflow_det['workflow_id'], $curr_sequence_no, CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL, $this->session->user_id, TRUE, TRUE);
                    }
                }
                //Ends


                //update actual table
                // $up_fields  = array('current_task_id' => $workflow_det['task_id']);
                // $up_where   = array('po_id' => $po_id);
                // $this->po_model->update_po($up_where, $up_fields);

                $where        = ['po_id' => $po_id];
                $curr_detail  = [ $this->po_model->get_details_for_audit( $table, $where) ];

                $parent_module_code = $this->get_module_code_per_task_ag_code($data['account_group']);

                $overview_details   = [
                    'transaction_num'    => $data['po_num'],
                    'transaction_msg'    => $this->lang->line('add_transaction_po'),
                    'reference'          => ISSET($po_id) ? $po_id : NULL,
                    'created_by'         => $this->session->userdata('user_id'),
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => $tab_module_details['ag_code'],
                    'tab_module_code'	 => $data['tab_module'],
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $data['po_num'],
                    'core_workflow_id'   => $tab_module_details['core_workflow_id']
                ];

                $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

                $msg          = $this->lang->line('data_saved');
            }

            $this->audit_trail->log_audit_trail($activity, $data['tab_module'], $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

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

            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_string('account_group')
            ->filter_string('purchase_request')
            ->filter_string('additional_flag')
            ->filter_string('po_num')
            ->filter_date('po_date')
            ->filter_string('vendor')
            ->filter_string('business_center')
            ->filter_float('amount')
            ->filter_string('released_date')
            ->filter_string('boq_ref')
            ->filter();

            //Define the required fields.
            $required = [
                'account_group'     => 'Account Group',
                'po_num'            => 'PO Reference Number',
                'po_date'           => 'PO Date',
                'vendor'            => 'Vendor',
                'business_center'   => 'Business Center',
                'amount'            => 'Amount',
                //'boq_ref'         => 'BOQ Reference Number'
            ];

            if($params['account_group'] != AG_CONTRACTORS) {
                $required['purchase_request'] = 'Purchase Request';
            }
            else {
                // $required['additional_flag'] = 'Project Type';
            }

            if( in_array($params['account_group'], [AG_GOODS_BFFI, AG_GOODS_MARINADES]) )
                $required['released_date'] = 'Released Date';

            $constraints['account_group']    = [
                'data_type'         => 'string',
                'name'              => 'Account Group'
            ];

            $constraints['purchase_request']    = [
                'data_type'         => 'string',
                'name'              => 'Purchase Request'
            ];

            $constraints['additional_flag']    = [
                'data_type'         => 'string',
                'name'              => 'Project Type'
            ];

            $constraints['po_num']    = [
                'data_type'         => 'string',
                'name'              => 'PO Number'
            ];

            $constraints['po_date']    = [
                'data_type'         => 'date',
                'name'              => 'PO Date'
            ];

            $constraints['vendor']    = [
                'data_type'         => 'string',
                'name'              => 'Vendor'
            ];

            $constraints['business_center']    = [
                'data_type'         => 'string',
                'name'              => 'business_center'
            ];

            $constraints['amount']    = [
                'data_type'         => 'amount',
                'name'              => 'Amount',
                'max'               => 99999999.99
            ];

            $constraints['released_date']    = [
                'data_type'         => 'date',
                'name'              => 'Released Date'
            ];

            $constraints['boq_ref']    = [
                'data_type'         => 'string',
                'name'              => 'BOQ Reference Number'
            ];

            $constraints['tab_module']    = [
                'data_type'         => 'string',
                'name'              => 'Tab Module'
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            if (preg_match('#[0-9]#',$data['po_num'])){

            }else{
                throw new Exception('PO number must have a numeric character.');
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

    public function get_business_center()
    {
        try
        {
            $params     = get_params();
            $options    = $this->po_model->get_all_business_centers($params['vendor']);

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

    public function get_pr_options()
    {
        try
        {
            $params     = get_params();

            $options    = [];

            if(ISSET($params['account_group']) AND !EMPTY($params['account_group']))
            {
                $pr_last_tasks  = ($params['account_group'] == AG_GOODS_BAVI)? [CORE_TASK_PR_UPLOAD_BAVI]: [CORE_TASK_PR_UPLOAD_BFFI_MARINADES];
                $options    = $this->po_model->get_all_pr_ref($params['account_group'], $pr_last_tasks);
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

        echo json_encode($options);
    }

    public function get_vendors()
    {
        try
        {
            $params     = get_params();
            $options    = [];
            $json_results   = [];

            $tab_module = decrypt_id($params['tab_module']);

            if( ! EMPTY($params['business_center']))
            {
                $scope_details  = get_scope_details($tab_module);

                $options        = $this->po_model->get_vendor_by_org_code_arr_and_ag_arr([$params['account_group']], $params['business_center'], $scope_details['vendor_code'], ['a.vendor_code as value', 'a.vendor_name as text']);

                if(is_array($options) AND count($options))
                {
                    foreach ($options as $key => $option)
                    {
                        $json_results[] = ['value' => $option['value'], 'text' => '['.$option['value'].'] '. htmlspecialchars_decode($option['text'], ENT_QUOTES)];
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

	public function get_boq_bc_n_vendor()
	{
		try
		{
			$flag  	              = ERROR;
			$params               = get_params();
            $boq_id               = $params['boq_id'];
            $organizations        = [];
            $vendors              = [];
            $msg                  = '';

            $boq_details          = $this->bq_model->get_boq_details($boq_id);

            $vendor_code          = $boq_details['vendor_code'];
            $bc_code              = $boq_details['org_code'];

            $organizations        = $this->po_model->get_organizations(['org_code' => $bc_code], ['org_code as value', 'name as title']);

            $vendors              = $this->bq_model->get_contractors_by_boq_id($boq_id);

			$flag                 = SUCCESS;
		}
		catch(PDOException $e)
		{
			$msg     = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg      = $this->rlog_error($e, TRUE);
		}

		echo json_encode([
			'flag'          => $flag,
            'msg'           => $msg,
            'orgs'          => $organizations,
            'vendors'       => $vendors
		]);
	}

	public function get_filtered_boq()
    {
        $params = get_params();
        $status = ERROR;
        $msg = '';
        $data = [];
        try{
            $data = $this->boq_model->get_boqs([
                'additional_flag' => $params['isAdditional'],
                'status_code' => STATUS_COMPLETED,
                'inhouse' => ENUM_NO
            ]);
            $status = SUCCESS;
        }catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);
        }
        $response = [
            'msg'   => $msg,
            'flag'  => $status,
            'data'  => $data
        ];
        echo json_encode($response);
	}
}