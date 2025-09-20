<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pr extends Task_Controller
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
        $this->folder           = FOLDER_PURCHASE_REQUESTS;

        $this->load->model($this->folder.'/Pr_model', 'pr_model');
        $this->load->model(FOLDER_BOQ.'/Boq_model', 'boq_model');
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        $this->path_task_views .= $this->folder;
        $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;
    }

    public function modal_add_pr($tab_module = NULL)
    {
        try
        {
            $data = $resources = array();

            $resources['load_css']  = array();
            $resources['load_js']   = array($this->module_js);

            $resources['loaded_init']   = array(
                    'datepicker_init();',
                    'selectize_init();'
            );

            $data['tab_module']     = encrypt_id($tab_module);
            $data['tab_module_raw'] = $tab_module;

            //account groups
            // $where = array('tab_module_code' => $tab_module);
            // $data['account_groups'] = $this->pr_model->get_tab_module($where, array("*, GROUP_CONCAT(CONCAT('''', ag_code, '''' )) as ag_codes"), array(), FALSE);

            // $available_ag_codes = $data['account_groups']['ag_codes'];

            //get tab module details
            $where = array('tab_module_code' => $tab_module);
            $pria_tab_det = $this->pr_model->get_tab_module($where, array('*'), array(), TRUE);

            //account groups
            $where = array('dashboard_flag' => YES_FLAG);
            $account_groups = $this->pr_model->get_all_account_groups($where);

            $pria_tab_det = array_column($pria_tab_det,'ag_code');

            foreach ($account_groups as $account_group) {
                if(in_array($account_group['account_group_code'], $pria_tab_det)){
                    $data['account_groups'][] = $account_group;
                }
            }

            $scope_details  = get_scope_details($tab_module);

            if($tab_module == MODULE_PORTAL_TRANS_CONTRACTORS_PR)
            {
                $organizations              =  get_organizations_by_org_type_w_scope($tab_module);
                $data['organizations']      = $organizations;

                $data['boqs']               = [];
                $data['projs']              = [];

                $resources['loaded_init'][] = "Pr.save(false);";
                $resources['loaded_init'][] = "Pr.boq_proj_loader();";

                $modal                      = "modals/add_pr_contractor";
            }
            else
            {
                // $code = $this->pr_model->get_account_group_names($available_ag_codes);

                //purchasing group
                $data['purchasing_groups']  = $this->pr_model->get_all_purchasing_group();

                //item
                $data['items']              = $this->pr_model->get_all_pr_item_types();

                //cost center
                $where                      = array('status_code' => SITE_STATUS_COMPLETED, 'cost_center_code' => 'IS NOT NULL');

                if( ! EMPTY($scope_details['orgs']))
                    $where['org_code']      = ['IN', $scope_details['orgs']];

                $site_order                 = array('official_store_name' => 'ASC');
                $sites                      = $this->pr_model->get_all_sites($where, array("a.cost_center_code value", "CONCAT('[', a.cost_center_code, '] - ', a.official_store_name) text"), $site_order);
                $data['sites']              = $sites;

                //gl accounts
                $data['gl_accounts']        = $this->pr_model->get_all_gl_accounts();

                //gl accounts
                // $data['requested_by'] = $this->pr_model->get_pr_requested_users();

                $resources['loaded_init'][] = "Pr.save(true);";
                $resources['loaded_init'][] = "Pr.load_cc(".json_encode($sites).");";

                $modal                      = "modals/add_pr";
            }
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
        try
        {
            $flag         = 0;
            $status       = ERROR;
            $response     = [];

            $data         = $this->_validate();
            // throw new Exception(var_export($data, TRUE));

            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_PURCHASE_REQUISITIONS;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update

            //check if po number already exist
            $where      = array( 'pr_num' => $data['pr_num']);
            $has_pr     = $this->pr_model->get_prs($where);

            if($has_pr){
                throw new Exception('PR number already exist.');
            }

            if( !EMPTY($data['pr_num'])){
                $audit_action = [AUDIT_INSERT];

                $prev_detail  = [];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' PR report');

                //add ag code to where
                $where = [
                    'ag_code'         => $data['account_group'],
                    'tab_module_code' => $data['tab_module']
                ];

                $tab_module_details   = $this->pr_model->get_tab_module($where);

                $extra_data = array(
                    'user_id'               => $this->session->user_id,
                    'account_group_code'    => $tab_module_details['ag_code'],
                    'reference_num'         => $data['pr_num']
                );

                $workflow_det = $this->pria_workflow->copy_worfklow($tab_module_details['core_workflow_id'], $extra_data);

                $insert  = array(
                    'account_group_code'        => $data['account_group'],
                    'pr_num'                    => (ISSET($data['pr_num']) AND !EMPTY($data['pr_num']))? $data['pr_num']: NULL,
                    'pr_item_type'              => (ISSET($data['item']) AND !EMPTY($data['item']))? $data['item']: NULL,
                    //'cost_center_code'          => ISSET($data['cost_center']) ? $data['cost_center'] : NULL ,
                    'gl_account_code'           => (ISSET($data['gl_account']) AND !EMPTY($data['gl_account']))? $data['gl_account']: NULL,
                    'purchasing_group_code'     => (ISSET($data['purchasing_group']) AND !EMPTY($data['purchasing_group']))? $data['purchasing_group']: NULL,
                    'pr_file_name'              => NULL,
                    'pr_sys_file_name'          => NULL,
                    'requestor'                 => array(filter_var($data['requested_by'], FILTER_SANITIZE_STRING), 'ENCRYPT'),
                    // 'current_task_id'           => $data['soa_document_recipient'],
                    'additional_flag'           => (ISSET($data['additional_flag']) AND !EMPTY($data['additional_flag']))? $data['additional_flag']: 0,
                    'created_by'                => $this->session->user_id,
                    'created_date'              => $now
                );

                $pr_id = $this->pr_model->insert_pr($insert);

                if($data['tab_module'] == MODULE_PORTAL_TRANS_CONTRACTORS_PR)
                {
                    $boq_pr_fields  = ['boq_id' => $data['boq_ref'], 'pr_id' => $pr_id];
                    $this->boq_model->insert_boq_pr($boq_pr_fields);

                    if(ISSET($data['project_ref']) AND !EMPTY($data['project_ref']))
                    {
                        $pr_proj_fields  = ['project_id' => $data['project_ref'], 'pr_id' => $pr_id];
                        $this->pr_model->insert_pr_project($pr_proj_fields);
                    }
                }
                else
                {
                    //insert multiple cost center code
                    $this->_insert_cost_center($pr_id, $data['cost_center']);
                }

                //update actual table
                $workflow_fields    = array('reference_id' => $pr_id);
                $workflow_where     = array('pria_workflow_id' => $workflow_det['workflow_id']);

                $this->pr_model->update_workflow($workflow_fields, $workflow_where);

                //update workflow
                //if account group is bavi, assigned logged in person as a person to upload PR.
                if(in_array($data['account_group'], [AG_GOODS_BAVI, AG_CONTRACTORS]))
                {
                    $user_id = $this->session->userdata('user_id');
                    $this->pria_workflow->update_task_assignment($workflow_det['task_id'], $user_id);
                }

                //update actual table
                $up_fields  = array('current_task_id' => $workflow_det['task_id']);
                $up_where   = array('pr_id' => $pr_id);
                $this->pr_model->update_pr($up_where, $up_fields);

                $where        = ['pr_id' => $pr_id];
                $curr_detail  = [ $this->pr_model->get_details_for_audit( $table, $where) ];

                $parent_module_code = $this->get_module_code_per_task_ag_code($data['account_group']);

                $overview_details   = [
                    'transaction_num'    => $data['pr_num'],
                    'transaction_msg'    => $this->lang->line('add_transaction_pr'),
                    'reference'          => ISSET($id) ? $id : NULL,
                    'created_by'         => $this->session->userdata('user_id'),
                    'created_date'       => date('Y-m-d H:i:s'),
                    'account_group_code' => $tab_module_details['ag_code'],
                    'tab_module_code'	 => $data['tab_module'],
                    'parent_module_code' => $parent_module_code,
                    'keyword'            => $data['pr_num']
                ];


                //As per Kim, remove overview in PR
                //September 16, 2019
                //$this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);

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

            // throw new Exception(var_export($params, TRUE));

            // throw new Exception(var_export($params, TRUE));

            //Filters the data inputted/uploaded by the user
            if($params['tab_module'] == MODULE_PORTAL_TRANS_CONTRACTORS_PR)
            {
                $params = $this->set_filter( $params )
                    ->filter_string('account_group')
                    ->filter_number('additional_flag')
                    ->filter_string('pr_num')
                    ->filter_string('business_center')
                    ->filter_string('boq_ref')
                    ->filter_string('project_ref')
                    ->filter_string('requested_by')
                    ->filter();
                    
                //Define the required fields.
                $required = [
                    'account_group'     => 'Account Group',
                    'pr_num'            => 'PR Number',
                    'business_center'   => 'Business Center',
                    'boq_ref'           => 'BOQ Number'
                ];

                if(ISSET($params['additional_flag']) AND !EMPTY($params['additional_flag']))
                    $required['project_ref']    = 'Project Reference Number';

                $constraints['account_group']   = [
                    'data_type'         => 'string',
                    'name'              => 'Account Group'
                ];

                $constraints['pr_num']  = [
                    'data_type'         => 'string',
                    'name'              => 'PR Number'
                ];

                $constraints['business_center']  = [
                    'data_type'         => 'string',
                    'name'              => 'Business Center'
                ];

                $constraints['boq_ref']  = [
                    'data_type'         => 'string',
                    'name'              => 'BOQ Number'
                ];

                $constraints['additional_flag']  = [
                    'data_type'         => 'enum',
                    'name'              => 'Project Reference Number',
                    'allowed_values'    => [INITIAL_YES, INITIAL_NO]
                ];

                $constraints['project_ref']  = [
                    'data_type'         => 'string',
                    'name'              => 'Project Reference Number'
                ];
            }
            else
            {
                $params = $this->set_filter( $params )
                    ->filter_string('account_group')
                    ->filter_string('purchasing_group')
                    ->filter_string('pr_num')
                    ->filter_string('item')
                    //->filter_string('cost_center')
                    //->filter_string('gl_account')
                    ->filter_string('requested_by')
                    ->filter();

                //Define the required fields.
                $required = [
                    'account_group'     => 'Account Group',
                    'purchasing_group'  => 'Purchasing Group',
                    'pr_num'            => 'PR Number',
                    'item'              => 'Item',
                    'cost_center'       => 'Cost Center',
                    //'gl_account'        => 'gl account'
                ];

                $constraints['account_group']    = [
                    'data_type'         => 'string',
                    'name'              => 'Account Group'
                ];

                $constraints['purchasing_group']    = [
                    'data_type'         => 'string',
                    'name'              => 'Purchasing Group'
                ];

                $constraints['pr_num']    = [
                    'data_type'         => 'string',
                    'name'              => 'PR Number'
                ];

                $constraints['item']    = [
                    'data_type'         => 'string',
                    'name'              => 'Item'
                ];

                $constraints['cost_center']    = [
                    'data_type'         => 'string',
                    'name'              => 'Cost Center'
                ];

                $constraints['gl_account']    = [
                    'data_type'         => 'string',
                    'name'              => 'GL Account'
                ];
            }

            $required['requested_by']       = 'Requested By';

            $constraints['requested_by']    = [
                'data_type'                 => 'string',
                'name'                      => 'Requested By'
            ];

            $constraints['tab_module']      = [
                'data_type'                 => 'string',
                'name'                      => 'Tab Module'
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            if (preg_match('#[0-9]#', $data['pr_num'])){

            }else{
                throw new Exception('PR Number must have a numeric character.');
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

    private function _insert_cost_center($pr_id, $cost_centers)
    {
        try
        {

            $data = array();
            foreach ($cost_centers as $cost_center) {

                $data['pr_id']              = $pr_id;
                $data['cost_center_code']   = $cost_center;

                $this->pr_model->insert_pr_cost_center($data);
            }
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

    public function get_pr_per_boq()
    {
        $params = get_params();
        $status = ERROR;
        $msg = '';
        $data = [];
        try{
            $data = $this->pr_model->get_pr_per_boq($params['boq_id']);
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

	public function get_pr_per_boy()
    {
        $params = get_params();
        $status = ERROR;
        $msg = '';
        $data = [];
        try{
            $data = $this->pr_model->get_pr_per_boq($params['boq_id']);

        }catch(PDOException $e)
        {
            echo $e->getMessage();
            $msg    = $this->get_user_message($e);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            $msg    = $this->rlog_error($e, TRUE);
        }
        // $response = [
        //     'msg'   => $msg,
        //     'flag'  => $status,
        //     'data'  => $data
        // ];
        // echo json_encode($data);
	}

    //Retaining for future references
    public function search_pr($keyword){
        try{
            $data = $this->pr_model->get_prs_search($keyword);
            echo json_encode($data);
        }catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);
        }
    }

    public function get_bc_boq()
    {
        $params         = get_params();
        $status         = ERROR;
        $msg            = '';
        $json_results   = [];

        try
        {
            $options        = $this->pr_model->get_bc_boq($params['org_code']);

            $json_results   = [];

            if(is_array($options) AND count($options))
            {
                foreach ($options as $key => $option)
                {
                    $json_results[] = ['value' => $option['boq_id'], 'text' => htmlspecialchars_decode($option['boq_code'], ENT_QUOTES)];
                }
            }

            $status = SUCCESS;
        }
        catch(PDOException $e)
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
            'data'  => $json_results
        ];

        echo json_encode($response);
    }

    public function get_bc_proj()
    {
        $params         = get_params();
        $status         = ERROR;
        $msg            = '';
        $json_results   = [];

        try
        {
            $options        = $this->pr_model->get_bc_proj($params['org_code']);

            $json_results   = [];

            if(is_array($options) AND count($options))
            {
                foreach ($options as $key => $option)
                {
                    $json_results[] = ['value' => $option['project_id'], 'text' => htmlspecialchars_decode($option['project_code'], ENT_QUOTES)];
                }
            }

            $status = SUCCESS;
        }
        catch(PDOException $e)
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
            'data'  => $json_results
        ];

        echo json_encode($response);
    }

    public function get_boq_details()
    {
        $params         = get_params();
        $status         = ERROR;
        $msg            = '';
        $json_results   = [];

        try
        {
            $boq_details    = $this->boq_model->get_boq_details($params['boq_id']);

            $status         = SUCCESS;
        }
        catch(PDOException $e)
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
            'data'  => $boq_details
        ];

        echo json_encode($response);
    }
}