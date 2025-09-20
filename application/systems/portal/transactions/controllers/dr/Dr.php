<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dr extends Task_Controller 
{
    protected $controller;
    protected $folder;
    protected $module_js;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('Pria_workflow');

        $this->controller       = strtolower(__CLASS__);
        $this->folder           = FOLDER_DELIVERY_GOODS;
        
        $this->load->model(CORE_USER_MANAGEMENT.'/users_model', 'users_model');
        $this->path_task_views .= $this->folder;
        //$this->module_js        = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

        $this->load->model(FOLDER_DELIVERY_GOODS.'/dr_model','dr_model');
        $this->load->model('Pria_mailer_model');
    }
    
    public function modal_cancel_dr($drs = NULL)
    {
        try 
        {
            $data   = $resources = array();
            $params = get_params();

            /* print_var_export($params); die; */

            $dr_arr = explode('-', $drs);
            
            $check_delivery_rec = $this->dr_model->get_checked_drs_records($dr_arr);

            $data['delivery_rec'] = $params['drs'];
            
            $resources['load_css']      = array(CSS_UPLOAD, CSS_DATETIMEPICKER, CSS_SELECTIZE);
            $resources['load_js']       = array(JS_UPLOAD, JS_DATETIMEPICKER, JS_SELECTIZE /*, $this->module_js*/);

            $resources['loaded_init']   = array(
                    // 'Dr.selectAll();',
                    // 'Dr.cancel_dr();'
            );

            $modal              = "modals/cancel_dr";
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

    public function get_checked_drs()
    {
        try
        {    
            $data       = [];
            $flag       = ERROR;
            $msg        = '';
            $params     = get_params();
            $checkAll   = $params['checkAll'];
            $fields     =  ['dr_gr_id', 'dr_num', 'org_code', 'vendor_code'];
            //print_var_export($params);
            if($checkAll == 'true')
            {
                $where          = ['account_group_code' => AG_FORWARDERS, 'dr_status' => 'IS NULL'];
                $scope_details  = get_scope_details(MODULE_PORTAL_TRANS_FORWARDER_DR);

                if( ! EMPTY($scope_details['orgs']))
                    $where['org_code'] = ['IN',  $scope_details['orgs']];

                $data  = $this->dr_model->get_drs($where, $fields);
            }
            else
            {
                $data  = $this->dr_model->get_checked_drs_records(array_values($params['checkboxes']), $fields);
            }

            if( ISSET($params['same']) && $params['same'] == 'true' )
            {
                $drs  = $params['checkboxes'];
                $orgs = array_unique(array_column($data, 'org_code'));
                $vend = array_unique(array_column($data, 'vendor_code'));

                if(COUNT($orgs) > 1)
                    throw new Exception('DRs from different Business Centers is not allowed.');
                  
                if(COUNT($vend) > 1)
                    throw new Exception('DRs from different Vendors is not allowed.');    
            }

            $flag = SUCCESS;
        }
        catch(PDOException $e)
        {
            $msg  = $this->get_user_message($e);

            $this->error_modal( $msg );
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);  
        }

        echo json_encode([
            'flag' => $flag,
            'msg'  => $msg,
            'drs'  => $data
        ]);
    }

    public function process()
    {
        try{
            $flag           = 0;
            $params         = get_params();

            $drs = explode(',',$params['dr_rec']);

            $status       = ERROR;
            $now          = date(FORMAT_DB_DATE);

            if(EMPTY($params['dr_rec'])){
                throw new Exception('Please select DR');
            }
            
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_DELIVERY_GOODS_RECEIPT;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            $prev_detail  = [];
            $audit_action = [AUDIT_INSERT];
            $activity     = sprintf($this->lang->line('audit_trail_add'), ' Requested DR');
            
            //Start the db transaction
            Portal_Model::beginTransaction();
            //If reference id is empty action will be update
            
            foreach ($drs as $key => $dr_gr_id) {
                $fields = array(
                    'dr_status'                 => DR_FOR_CANCELLATION,
                    'dr_cancellation_requestor' => $this->session->user_id,
                    'dr_remarks'                => $params['remarks']
                );

                $where = array('dr_gr_id' => $dr_gr_id);

                $this->dr_model->update_canceled_delivery_goods_receipt($where, $fields);
            }

            $roles 			= array(ROLE_CSS_HEAD);
            $message        = '';
            $ref_number     = $task_details['reference_num'];
            $actor_name     = $this->_task_actor_name($this->session->user_id);
            
            $org_code 		= [];
            $notification   = "<font color='#000000'><b>DR cancellation request</b> has been submitted by <b>$actor_name</b></font>";
            
            $this->pria_notification->import_reminder($roles, MODULE_PORTAL_TRANS_FORWARDER_DR, $notification, $message, $org_code);

              
            $email_details                   = $this->dr_model->get_dr_gr_details_for_email_request($drs);

            $overview_msg   = "requested for cancellation of DR";

            foreach($email_details as $ed)
            {   
                $css_heads = $this->dr_model->get_users_by_role_n_org(ROLE_CSS_HEAD, $ed['org_code']);
                    
                if(EMPTY($css_heads))    
                    throw new Exception('No CSS Head found.');

                $transaction_params  = array(
                    'to_user_ids'           => array_column($css_heads, 'user_id'),
                    'reference_num'         => $ed['dr_num'],
                    'ag_name'               => $ed['account_group_name'],
                    'transaction_action'    => 'requested',
                    'redirect'              => PORTAL_TRANSACTIONS . "/forwarders#tab_drs"
                ); 

                $this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_DR_CANCELLATION_TRANSACTION_W_ACTION, NULL, NULL, NULL, $ed['org_code'], $this->session->user_id, NULL, $transaction_params);

                $this->_trigger_overview($ed['dr_gr_id'], $ed['dr_num'], $overview_msg);


                $this->_trigger_notifications($ed['dr_gr_id'], DR_FOR_CANCELLATION, TRUE);
            }
            
            $curr_detail  = [];
            $this->audit_trail->log_audit_trail($activity, MODULE_PORTAL_TRANS_FORWARDER_DR, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

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


    public function approve_dr_process()
    {
        try{
            $flag   = 0;
            $status = ERROR;
            $msg    = $this->lang->line('data_saved');

            //Start the db transaction
            Portal_Model::beginTransaction();

            $params = get_params();

            $fields = array(
                    'dr_status'                 => DR_CANCELLED,
                    'dr_remarks'                => $params['comments'],
                    'approver'                  => $this->session->user_id,
                    'approved_date'             => date(FORMAT_DB_DATE)
                );

            $where = array('dr_gr_id' => $params['dr_id']);

            $this->dr_model->update_canceled_delivery_goods_receipt($where, $fields);

            $this->_trigger_notifications($params['dr_id'], DR_CANCELLED);

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

    public function disapprove_dr_process()
    {
        try{
            $flag   = 0;
            $status = ERROR;
            $msg    = $this->lang->line('data_saved');

            //Start the db transaction
            Portal_Model::beginTransaction();

            $params = get_params();

            $fields = array(
                    'dr_status'                 => NULL,
                    'dr_remarks'                => $params['comments'],
                    'rejector'                  => $this->session->user_id,
                    'rejected_date'             => date(FORMAT_DB_DATE)
                );

            $where = array('dr_gr_id' => $params['dr_id']);
            $this->dr_model->update_canceled_delivery_goods_receipt($where, $fields);

            $this->_trigger_notifications($params['dr_id'], DR_APPROVED);

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

	public function _trigger_notifications($dr_gr_id, $action, $notif_only = FALSE)
	{
		try
		{
			if(!EMPTY($dr_gr_id) AND !EMPTY($action))
			{				
				$dr_details	= $this->dr_model->get_dr_gr_details($dr_gr_id);

				if(ISSET($dr_details['dr_gr_id']) AND !EMPTY($dr_details['dr_gr_id']))
				{
					$transaction_action				= "";

					if($action == DR_CANCELLED)
					{
						$transaction_action			= "Approved";
                        $overview_msg               = "approved DR Cancellation";
					}
					else if($action == DR_APPROVED)
					{
						$transaction_action			= "Disapproved";
                        $overview_msg               = "disapproved DR Cancellation";
					}
                    else if($action == DR_FOR_CANCELLATION)
                    {
                        $transaction_action         = "Requested";
                    }

					$org_code						= $dr_details['org_code'];

                    $receivers                      = array($dr_details['dr_cancellation_requestor']);

                    if(!$notif_only)
                    {
                        $transaction_params             = array(
                                'to_user_ids'           => array($dr_details['dr_cancellation_requestor']),
                                'reference_num'         => $dr_details['dr_num'],
                                'ag_name'               => $dr_details['account_group_name'],
                                'transaction_action'    => $transaction_action,
                                'redirect'              => PORTAL_TRANSACTIONS . "/forwarders#tab_drs"
                        );

                        $this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_DR_CANCELLATION_TRANSACTION_W_ACTION, NULL, NULL, NULL, $org_code, $this->session->user_id, NULL, $transaction_params);

                        $this->_trigger_overview($dr_details['dr_gr_id'], $dr_details['dr_num'], $overview_msg);
                    }
                    else
                    {
                        $receivers      = array();
                        $org_details    = $this->dr_model->get_organization(array('org_code' => $dr_details['org_code']), array('org_code', 'org_type_code'));

                        if(ISSET($org_details['org_type_code']) AND !EMPTY($org_details['org_type_code']))
                        {
                            $org_raw    = $this->pria_mailer_model->get_trans_orgs($dr_details['org_code'], $org_details['org_type_code']);

                            $orgs       = ($org_raw AND COUNT($org_raw) > 0)? array_unique(array_column($org_raw, 'org_code')): array();

                            $recipients = $this->pria_mailer_model->get_users_per(array(ROLE_CSS_HEAD), $orgs);

                            $receivers  = ($recipients AND COUNT($recipients) > 0)? array_unique(array_column($recipients, 'user_id')): array();
                        }
                    }

                    foreach ($receivers as $key => $receiver)
                    {
                        $notify_who = array(
                            'notification_icon'         => 'speaker_notes',
                            'notification_mobile'       => NULL,
                            'notify_users'              => array($receiver),
                            'notify_orgs'               => array(), //no default data
                            'notify_roles'              => array(),
                            'module_code'               => MODULE_PORTAL_TRANS_FORWARDER_DR,
                            'displayed_socket_flag'     => NO_FLAG,
                            'listed_flag'               => YES_FLAG,
                            'reminder_flag'             => NO_FLAG
                        );

                        $notify_who['notification_html']    = base_url() . PORTAL_TRANSACTIONS . "/forwarders?keyword=".$dr_details['dr_num']."#tab_drs";

                        $notification   = "DR Cancellation <font color='#e23b3b'>".$dr_details['dr_num']."</font><font color='#000000'> has been ".strtolower($transaction_action).".</font>";

                        $this->notify->insert_notification($notification, $notify_who, $user_id);
                    }
				}
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

    public function _trigger_overview($dr_gr_id, $reference_num, $overview_msg)
    {
        try
        {
            $account_group_code                     = AG_FORWARDERS;
            
            $parent_module_code                     = $this->get_module_code_per_task_ag_code($account_group_code);

            $overview_details                       = array(
                    'reference'                     => $dr_gr_id,
                    'transaction_num'               => $reference_num,
                    'transaction_msg'               => $overview_msg,
                    'created_by'                    => $this->session->user_id,
                    'created_date'                  => date(FORMAT_DB_DATETIME),
                    'account_group_code'            => $account_group_code,
                    'tab_module_code'               => MODULE_PORTAL_TRANS_FORWARDER_DR,
                    'parent_module_code'            => $parent_module_code,
                    'keyword'                       => $reference_num
            );

            $this->pria_overview->log_overview($parent_module_code, OVERVIEW_TYPE_ADD_TRANSACTION, $overview_details);
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