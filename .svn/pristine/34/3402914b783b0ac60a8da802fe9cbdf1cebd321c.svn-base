<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encode_delivery_receipt extends Task_Controller
{
	public function __construct()
	{
		parent::__construct();

        $this->controller 		= strtolower(__CLASS__);
        // $this->module_code      = MODULE_PORTAL_TRANS_CG_IO;
        $this->folder           = FOLDER_DELIVERY_GOODS;
        $this->po_folder        = FOLDER_PURCHASE_ORDERS;

        $this->load->model($this->folder.'/Delivery_goods_model', 'dr_model');
        $this->load->model('Documents_model', 'document_model');
        $this->load->model($this->po_folder.'/po_model', 'po_model');

        // $this->permissions      = check_permission($this->module_code);

        $this->path_task_views .= $this->folder;

        $this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/dr";
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

            //get account group
            $ag_code = $task['account_group_code'];

            //get core workflow id
            $workflow_id = $task_workflow['workflow_id'];

            //get tab module details
            $this->tab_module_code  = ($ag_code != AG_GOODS_MARINADES) ? MODULE_PORTAL_TRANS_GOODS_G_PO :  MODULE_PORTAL_TRANS_GOODS_M_PO;

            $tab_module_dets        = $this->tm_model->get_module(['module_code' => $this->tab_module_code], ['link', 'module_name', 'parent_module']);

            $this->module_code      = $tab_module_dets['parent_module'];

            $this->permissions  = check_permission($this->tab_module_code);
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][] = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][] = CSS_SELECTIZE;
            $this->task_resources['load_js'][]  = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]  = JS_SELECTIZE;

            $this->task_resources['load_js'][]      = $this->module_js;
            $this->task_resources['loaded_init'][]  = 'Dr.dr_location();';

            //Get PO details
            $fields         = ['*'];
            $where          = ['po_id' => $this->task_details['reference_id']];
            $po_details     = $this->po_model->get_po($where, $fields);

           // print_var_export($po_details); die;

            $this->task_view_data['po_details'] = $po_details;

            $dgr_id         = $this->task_details['task_reference_id'];

            //Get DR details
            if($dgr_id){
                $fields             = ['*'];
                $where              = ['dr_gr_id' => $dgr_id];
                $dgr_details        = $this->dr_model->get_delivery_goods_receipt($where, $fields);

                $this->task_view_data['dgr_details'] = $dgr_details;
            }

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.
            // $sub_nav_left_config	 = [
			// 	'title'  		=> 'PO Number',
			// 	'placeholder' 	=> 'PO #',
			// 	'data' 		 	=> []
			// ];

            //$this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);

            //Check if task has already a reference saved. If yes, action must be update. Retrieve data from table.
            $dgr_id          = $this->task_details['task_reference_id'];

            $this->task_view_data['po_details'] = $po_details;

            if( ! EMPTY($dgr_id))
            {
                $fields  = array('*');
                $this->task_view_data['delivery_receipt_details'] = $this->dr_model->get_delivery_goods_receipt(['dr_gr_id' => $dgr_id], $fields);
            }

            //support center
            $this->task_view_data['support_centers']        = $this->po_model->get_pr_orgs($this->task_details['reference_id']);

            //org_code
            if(!EMPTY($dgr_details['org_code']))
            {
                $org_code   = $dgr_details['org_code'];
            }
            else
            {
                $org_code   = (count($this->task_view_data['support_centers']) == 1)? $this->task_view_data['support_centers'][0]['org_code']: NULL;
            }

            if(!EMPTY($org_code))
            {
                //location
                $this->task_view_data['locations']              = $this->dr_model->get_site_selection($ag_code, $org_code);
                $this->task_view_data['dr_document_recipients'] = $this->dr_model->get_dr_recipients_by_org($org_code);
            }

            $this->task_view_data['po_org_code']                = $org_code;

            $this->task_view_data['location_required']          = (!in_array($ag_code, [AG_GOODS_MARINADES]))? FALSE: TRUE;

            //Load the content of the task
            $this->data['page_title']       = 'Purchase order: '.$po_details['po_num'];
            $this->task_page                = '/encode_delivery_receipt';

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
            $flag          = ERROR;
            $status        = TASK_STATUS_ONGOING;
            $response      = [];
            $now           = date(FORMAT_DB_DATE);
            $doc_ref       = '';
            $data          = $this->_validate();
            $extra_columns = [];

            //Start the db transaction
            Portal_Model::beginTransaction();

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $task_id      = $data['task_id'];
            $task_status_id = $data['task_status'];

            //get task details
            $task = $this->tm_model->get_task_details($task_id);

            $other_drs = $this->dr_model->get_last_delivery(DR_PO, $task['reference_id'], ['a.dr_gr_id']);

            $task_workflow = $this->tm_model->get_task_workflow($task_id);

            //get account group
            $ag_code = $task['account_group_code'];

            if($data['location']){
                $where      = array('site_id' => $data['location']);
                $site_info  = $this->tm_model->get_spec_site($where, $fields=array('*'), $order = array(), FALSE);
            }

            //throw new Exception('asdf');
            //Assiged specific roles for (Encode GR) task
            switch ($ag_code) {
                case AG_GOODS_MARINADES:

                        /* if($site_info['site_type_code'] == SITE_TYPE_DRESSING_PLANT){
                            $this->_update_task_role($task['pria_stage_id'] , ROLE_PROD_FIN_PERS);
                        }else{
                            //SITE_TYPE_WAREHOUSE
                            $this->_update_task_role($task['pria_stage_id'] , ROLE_CSS);
                        } */

                        //$this->tm_model->delete_task_role(['pria_task_id' => $task_id, 'actor_flag' => INITIAL_YES]);

                        $role =  ($site_info['site_type_code'] == SITE_TYPE_DRESSING_PLANT) ? ROLE_PROD_FIN_PERS : ROLE_CSS;

                        $this->_update_task_role($task['pria_stage_id'], $role);
                    break;

                default:
                    # code...
                    break;
            }


            //get core workflow id
            $workflow_id = $task_workflow['workflow_id'];

            //get tab module details
            $tab_module_details = $this->tm_model->get_tab_module(['ag_code' => $ag_code, 'core_workflow_id' => $workflow_id , 'root_module' => ROOT_TRANSAC]);

            $tab_module_code = $tab_module_details['tab_module_code'];

            $this->module_code      = $tab_module_code;
            $this->tab_module_code  = $tab_module_code;


            $po_id          = $this->tm_model->get_task_workflow_reference_id($task_id);
            $po_details     = $this->po_model->get_po(['po_id' => $po_id], ['*']);
            $po_amount      = $po_details['po_amount'];

            $where          = ['po_id' => $po_id];
            $dr_ref_details = $this->dr_model->get_delivery_goods_reference($where);

            $where          = ['document_type_code' => DOC_TYPE_DOC_DR, 'reference' => $task['task_reference_id']];
            $dr_form        = $this->document_model->get_document($where);


            $last_dr_flag   = ISSET($data['doc_dr_deliveries']) ? $data['doc_dr_deliveries']: ENUM_NO;
            //If reference id is empty action will be insert
            if( EMPTY($data['action_dr_num']))
            {
                //starts
                //Check if dr amount exceeded to po amount
               /*  $where                  = ['po_id' => $po_id];
                $dr_ref_amount_details  = $this->dr_model->get_delivery_goods_reference($where, array('*'), TRUE);

                if($dr_ref_amount_details){
                   $dr_refs = array_column($dr_ref_amount_details,'dr_gr_id');

                    if(!EMPTY($dr_refs)){
                        $imploded_dr_gr_ids = implode(',',$dr_refs);

                        $this->_validate_amount($imploded_dr_gr_ids, $po_details['po_amount'], $data['dr_amount']);
                    }
                } */
                //ends

                //validate fhr file
                // if(EMPTY($data['doc_dr'])){
                //     throw new Exception('DR File is required.');
                // }

                if(EMPTY($dr_form)){
                    if(empty($data['doc_dr'])){
                        throw new Exception('DR File is required.');
                    }
                }

                //Validates if there's a last dr that's already existing
                if( ! EMPTY($other_drs) &&  ISSET($data['doc_dr_deliveries']) && $data['doc_dr_deliveries'] == ENUM_YES )
                    throw new Exception($this->lang->line('err_last_dr_exist'));


                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT];
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' Delivery Receipt report');

                //Set up fields that will be inserted
                $insert       = [
                    'pria_task_id'      => $task_id,
                    'account_group_code' => $tab_module_details['ag_code'],
                    'dr_num'            => $data['dr_num'],
                    'org_code'          => $data['support_center'],
                    'dr_date'           => std_db_date_format($data['dr_date']),
                    'site_id'           => (ISSET($data['location']) AND !EMPTY($data['location']))? $data['location']: NULL,
                   // 'dr_amount'         => $data['dr_amount'],
                    'dr_recipient_id'   => $data['dr_document_recipient'],
                    'vendor_code'       => $po_details['vendor_code'],
                    'created_by'        => $this->session->user_id,
                    'created_date'      => $now,
                    'dr_type_code'      => DR_PO,
                    //'last_dr_flag'      => ISSET($data['doc_dr_deliveries']) ? $data['doc_dr_deliveries']: ENUM_NO
                    'last_dr_flag'      => $last_dr_flag
                ];

                $dgr_id     = $this->dr_model->insert_delivery_goods_receipt($insert);

                //Set up fields that will be inserted
                $insert     = [
                    'dr_gr_id'  => $dgr_id,
                    'po_id'     => $po_id
                ];

                $this->dr_model->insert_delivery_goods_reference($insert);

                $where        = ['dr_gr_id' => $dgr_id];
                $curr_detail  = [ $this->dr_model->get_details_for_audit($table, $where) ];

                //Update the reference of the task
                //$this->pria_workflow->tag_task_ongoing($task_id, $dgr_id);

                // $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, [
                //     'reference'         => $dgr_id,
                //     'start_date'        => date(FORMAT_DB_DATETIME),
                //     'actual_start_date' => date(FORMAT_DB_DATETIME)
                // ]);

            /*     $this->tag_task($task_id, $task_status_id, [
                    'reference'             => $dgr_id,
                    'start_date'            => date(FORMAT_DB_DATETIME),
                    'actual_start_date'     => date(FORMAT_DB_DATETIME)
                ], NULL, $data['dr_document_recipient']);
 */

                $extra_columns = [
                    'reference'             => $dgr_id,
                    'start_date'            => date(FORMAT_DB_DATETIME),
                    'actual_start_date'     => date(FORMAT_DB_DATETIME)
                ];

                $doc_ref             = encrypt_id($dgr_id);

                // $response['actor']   = $ongoing['actor_name'];
                $status                 = TASK_STATUS_ONGOING;
            }
            else
            {

                //Starts
                //Check if dr amount exceeded to po amount
               /*  $where                      = ['po_id' => $po_id];
                $where['dr_gr_id']['!=']    = $task['task_reference_id'];
                $dr_ref_amount_details      = $this->dr_model->get_delivery_goods_reference($where, array('*'), TRUE);

                if($dr_ref_amount_details){
                   $dr_refs = array_column($dr_ref_amount_details,'dr_gr_id');

                    if($dr_refs){
                        $imploded_dr_gr_ids = implode(',',$dr_refs);

                        $this->_validate_amount($imploded_dr_gr_ids, $po_details['po_amount'], $data['dr_amount']);
                    }
                } */
                //Ends

                if(EMPTY($dr_form)){
                    if(empty($data['doc_dr'])){
                        throw new Exception('DR File is required.');
                    }
                }

                $data['doc_dr_deliveries'] = ISSET($data['doc_dr_deliveries']) ? $data['doc_dr_deliveries']: ENUM_NO;


                //Validates if there's a last dr that's already existing
                if($data['doc_dr_deliveries'] == ENUM_YES && ! EMPTY($other_drs) && $other_drs['dr_gr_id'] != $task['task_reference_id'])
                    throw new Exception($this->lang->line('err_last_dr_exist'));

                $audit_action = [AUDIT_UPDATE];

                $where        = array('dr_gr_id' => $task['task_reference_id']);
                $prev_detail  = [ $this->dr_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Delivery Receipt report');

                //Set up fields that will be inserted
                $update       = [
                    'pria_task_id'      => $task_id,
                    'account_group_code' => $tab_module_details['ag_code'],
                    'dr_num'            => $data['dr_num'],
                    'org_code'          => $data['support_center'],
                    'dr_date'           => std_db_date_format($data['dr_date']),
                    'site_id'           => (ISSET($data['location']) AND !EMPTY($data['location']))? $data['location']: NULL,
                    //'dr_amount'         => $data['dr_amount'],
                    'dr_recipient_id'   => $data['dr_document_recipient'],
                    'vendor_code'       => $po_details['vendor_code'],
                    'modified_by'       => $this->session->user_id,
                    'modified_date'     => $now,
                    //'last_dr_flag'      => ISSET($data['doc_dr_deliveries']) ? $data['doc_dr_deliveries']: ENUM_NO
                    'last_dr_flag'      => $last_dr_flag
                ];

                //Update Delivery Receipt
                $where = array('dr_gr_id' => $task['task_reference_id']);
                $this->dr_model->update_delivery_goods_receipt($where, $update);

                $curr_detail         = [ $this->dr_model->get_details_for_audit( $table, $where) ];

                $doc_ref             = encrypt_id($task['task_reference_id']);

                $status              = TASK_STATUS_ONGOING;
            }

            /* $total_dr_amount  = $this->dr_model->get_total_delivery_amount_by_po_id($po_id);

            $remaining_amount = $po_amount - $total_dr_amount;

            $this->po_model->update_po(['po_id' => $po_id], ['remaining_amount' => $remaining_amount]);

            $this->_additional_validations($po_id, $po_amount, $remaining_amount, $last_dr_flag); */

            //if(EMPTY(round($remaining_amount)) &)

            $this->tag_task($task_id, $task_status_id, $extra_columns, NULL, $data['dr_document_recipient']);

            //$this->audit_trail->log_audit_trail($activity, $this->tab_module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg  = $this->lang->line('data_saved');
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
            'msg'   => $msg,
           /*  'task'   => $response,
            'status' => $status, */
            'doc_ref'=> $doc_ref
        ]);
    }


    private function _validate()
    {
        try
        {
            $params = get_params();

            //Filters the data inputted by the user
			$params = $this->set_filter( $params )
            // ->filter_string('doc_dr')
            ->filter_string('dr_num')
            ->filter_string('support_center')
            ->filter_date('dr_date')
            ->filter_string('location')
           // ->filter_float('dr_amount')
            ->filter_string('dr_document_recipient')
            ->filter_string('doc_dr_deliveries')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            $ag_details = $this->tm_model->get_task_details($params['task_id']);
            $ag_code    = $ag_details['account_group_code'];

            //Define the required fields.
            $required = [
                'dr_num'                => 'DR Number',
                'support_center'        => 'Business/Support Center',
                'dr_date'               => 'DR Date',
                'location'              => 'Location',
               // 'dr_amount'             => 'Total Amount',
                'dr_document_recipient' => 'PIC for GR'
                // 'doc_dr'                => 'DR File'
            ];

            if(!in_array($ag_code, [AG_GOODS_MARINADES]))
            {
                unset($required['location']);
            }

            $constraints['action_dr_num'] = [
                'data_type'         => 'string',
                'name'              => 'Action DR Number'
            ];

            $constraints['dr_num'] = [
                'data_type'         => 'string',
                'name'              => 'DR Number'
            ];

            $constraints['support_center']    = [
                'data_type'         => 'string',
                'name'              => 'Business/Support Center'
            ];

            $constraints['dr_date']	= [
                'data_type'			=> 'date',
                'name'				=> 'DR Date'
            ];

            $constraints['location'] = [
                'data_type'         => 'string',
                'name'              => 'Location'
            ];

        /*     $constraints['dr_amount'] = [
                'data_type'         => 'string',
                'name'              => 'Total Amount'
            ]; */

            $constraints['dr_document_recipient'] = [
                'data_type'         => 'string',
                'name'              => 'PIC for GR'
            ];

            $constraints['doc_dr'] = [
                'data_type'         => 'string',
                'name'              => 'DR File'
            ];

            $constraints['tab_module']    = [
                'data_type'         => 'string',
                'name'              => 'Module Tab'
            ];

            $constraints['task_id'] = [
                'data_type'   => 'db_value',
                'name'        => 'ETD',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'pria_task_id',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
            ];

            if( ISSET($params['doc_dr_deliveries']) )
            {
                $constraints['doc_dr_deliveries']   = array(
                    'data_type'         => 'enum',
                    'name'              => 'Last DOC Delivery'
                );
            }

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

            $end_date   = strtotime($params['dr_date']);
            $curr_date  = strtotime(date(FORMAT_DB_DATE));

            if($end_date > $curr_date)
                throw new Exception('DR date must not be greater than current date.');

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            if (preg_match('#[0-9]#',$data['dr_num'])){

            }else{
                throw new Exception('DR Number must have a numeric character.');
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

    //private function _validate_amount($drg_ids = NULL, $po_amount = '', $dr_amount = '')
    private function _additional_validations($po_id, $po_amount, $remaining_amount, $last_dr_flag)
    {
        try
        {

          /*   if(!EMPTY($drg_ids[0])){
               $po_reference_info  = $this->dr_model->check_amounts($drg_ids,$po_amount, $dr_amount);

                if(!EMPTY($po_reference_info['total_dr_amount'])){
                    $total_amount = $po_reference_info['total_dr_amount'] + $dr_amount;
                }
            }else{
                $total_amount = $dr_amount;
            }


            if(!EMPTY($total_amount)){
                if ($total_amount > $po_amount){
                    throw new Exception('Total DR amount exceeded from PO amount.');
                }
            } */

            $total_dr_amount  = $this->dr_model->get_total_delivery_amount_by_po_id($po_id);

            if($total_dr_amount > $po_amount)
                throw new Exception('Total DR amount exceeded the PO amount.');

            $w_last_dr = $this->pria_workflow->_check_w_last_dr(DR_PO, $po_id);

            if($w_last_dr == FALSE && EMPTY(round($remaining_amount)) && $last_dr_flag == ENUM_NO)
                throw new Exception('PO amount maxed out. Please tag this as the last delivery.');
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

    private function _update_task_role($stage_id = NULL, $assign_role = NULL)
    //private function _update_task_role($pria_task_id = NULL, $assign_role = NULL)
    {
        try{
            if(!EMPTY($stage_id) AND !EMPTY($assign_role))
            {

            /*     $where  = array(
                    'pria_stage_id' => $stage_id,
                    'sequence_no'   => SEQUENCE_NO_THREE
                );

                $task_info = $this->dr_model->get_task_ref($where);

                $where  = array(
                    'pria_task_id' => $task_info['pria_task_id']
                );

                $fields = array(
                    'role_code' => $assign_role
                );

                $this->dr_model->update_task_roles($where, $fields);       */

                $where  = array(
                    'pria_stage_id' => $stage_id,
                    'sequence_no'   => SEQUENCE_NO_THREE
                );

                $task_info      = $this->dr_model->get_task_ref($where);
                $pria_task_id   = $task_info['pria_task_id'];

                $this->tm_model->delete_task_role(['pria_task_id' => $pria_task_id, 'actor_flag' => INITIAL_YES]);

                $this->tm_model->insert_task_role(['actor_flag' => INITIAL_YES, 'role_code' => $assign_role, 'pria_task_id' => $pria_task_id]);
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

    public function get_locations()
    {
        try
        {
            $params     = get_params();

            $where      = array('org_code' => $params['support_center'], 'status_code' => STATUS_COMPLETED, 'site_code' => 'IS NOT NULL');
            $options    = $this->dr_model->get_all_sites($where, ['*'], ['official_store_name' => 'ASC']);

            $json_results   = [];

            if(is_array($options) AND count($options))
            {
                foreach ($options as $key => $option)
                {
                    $json_results[] = ['value' => $option['site_id'], 'text' => htmlspecialchars_decode($option['official_store_name'], ENT_QUOTES)];
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

        echo json_encode($json_results);
    }

    public function get_dr_recipients()
    {
        try
        {
            $params     = get_params();

            $options    = $this->dr_model->get_dr_recipients_by_org($params['support_center']);

            $json_results   = [];

            if(is_array($options) AND count($options))
            {
                foreach ($options as $key => $option)
                {
                    $json_results[] = ['value' => $option['user_id'], 'text' => htmlspecialchars_decode($option['fullname'], ENT_QUOTES)];
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

        echo json_encode($json_results);
    }
}