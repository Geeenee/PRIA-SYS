<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_complete_soa extends Base_Controller
{
 
	public function __construct() 
	{
		parent::__construct();

		$this->load->model('Pria_cron_model', 'pria_cron_model');
		$this->load->model('task_model', 'tm_model');
        $this->load->library('Pria_workflow');
	}

	/**
	 * check due soas to auto complete.
	 */
	public function check_soas()
	{

		if($this->input->is_cli_request())
		{
			$auto_soas = $this->pria_cron_model->get_vendor_approval_soas();
			
			if(is_array($auto_soas) AND count($auto_soas) > 0)
            {
				foreach ($auto_soas as $key => $auto_soa)
                {
					$task_details  = $this->tm_model->get_task_details($auto_soa['pria_task_id']);

                    $user_id       = (ISSET($task_details['user_id']) AND !EMPTY($task_details['user_id']))? $task_details['user_id']: ADMINISTRATOR_UID;
                    
                    $this->pria_workflow->tag_task($task_details['pria_task_id'], TASK_STATUS_APPROVED, ['apv_sent_flag' => YES_FLAG, 'skip_overview' => TRUE, 'skip_reminder' => TRUE], $user_id, NULL, TRUE);
					
					$module_code   = $this->_get_module_code_per_task_ag_code($task_details['account_group_code']);

					$this->_insert_reminder($task_details, $module_code, '', $user_id);
				}
			}
		}
		else
		{
			echo "Error! Should not be accessed from URL address.";
		}
	}

	/** 
     * @Author: Christian Aquino
     * @Date: 2019-10-23 05:11:51 
     * @Desc:  Insert reminder for user, it appears in dashboard
     */ 
    private function _insert_reminder($task_details, $module_code, $actor = '', $user_id = NULL)
    {
        try
        {   
            $roles              = array(); //specify role to be notified
            $orgs               = array(); //specify role to be notified
            $rem_task_details   = NULL;
            $multiple           = FALSE;

            $base_url_cron      = get_sys_param_val(SYS_PARAM_CRON_SETTINGS, SYS_PARAM_BASE_URL_CRON);
            $base_url_cron      = (ISSET($base_url_cron['sys_param_value']) AND !EMPTY($base_url_cron['sys_param_value']))? $base_url_cron['sys_param_value']: NULL;

            switch ($task_details['core_workflow_task_id']) {
                case CORE_TASK_SOA_APPROVE:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $roles = array(ROLE_BC_FIN_PERS, ROLE_HO_PAY_FIN, ROLE_PAY_FIN_PERS); //specify role to be notified

                    /*if(!EMPTY($task_details['account_group_code']) AND in_array($task_details['account_group_code'], array(AG_GOODS_BFFI, AG_GOODS_MARINADES)))
                    {
                        $roles[]    = ROLE_PAY_FIN_PERS;
                    }*/

                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved</font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font> <font color='#000000'>and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message, $orgs);

                    break;
                
                case CORE_TASK_SOA_ACCEPT_CALAMBA:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_PAY_FIN_PERS, ROLE_HO_PAY_FIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved </font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_SOA_BASED:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_BC_FIN_PERS, ROLE_HO_PAY_FIN, ROLE_PAY_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved</font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_PAY_FIN_PERS, ROLE_HO_PAY_FIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved</font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_BC_FIN_PERS, ROLE_BC_FIN_HEAD); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved</font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER:
                	
                	$rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );
                    
                    $roles          = array(ROLE_PAY_FIN_PERS, ROLE_HO_PAY_FIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved</font><font color='#000000'>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";
                    
                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_FEEDS_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_soa'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>SOA</b> </font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been automatically approved</font>. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_FHR_FINAL_APPROVAL:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_IO_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_CG_LIQ_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_internal_orders'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>FHR</b></font><font color='#000000'> with FHR </font> <font color='#e23b3b'>".$ref_number_link." </font> <font color='#000000'> has been approved. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_PR_UPLOAD_BAVI_APPROVAL:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED
                    );
                    
                    $roles          = array(ROLE_PURCH_PERS,ROLE_PURCH_HEAD); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_purchase_requests'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>PR</b> </font> <font color='#e23b3b'>".$ref_number_link." </font> <font color='#000000'> has been approved. You may now create </font><font color='#354575'><b>PO</b></font><font color='#000000'> and upload to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_PR_UPLOAD_CON_APPROVAL:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED
                    );
                    
                    $roles          = array(ROLE_PURCH_PERS_CON,ROLE_PURCH_HEAD); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_purchase_requests'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>PR</b> </font> <font color='#e23b3b'>".$ref_number_link." </font> <font color='#000000'> has been approved. You may now create </font><font color='#354575'><b>PO</b></font><font color='#000000'> and upload to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_ENCODE_ASSET_CODE:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_PROJ_ENG,ROLE_BC_ADMIN,ROLE_ROTI_ADMIN); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_boq'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>Asset Code</b> and <b>IO</b> </font> <font color='#000000'> have been encoded for </font><font color='#e23b3b'>".$ref_number_link."</font><font color='#000000'>. You may now create</font> <font color='#354575'><b>Purchase Request</b></font><font color='#000000'> and upload to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);https://pria.dev.asiagate.com/dashboard/dashboard
                    break;

                case CORE_TASK_ENCODE_BOQ_PROGRESS:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_PAY_FIN_PERS,ROLE_BC_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_projects'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>BOQ Progress</b></font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been approved. You may now release payment check.</font>";
  
                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_CONTRACTS_UPLOAD:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_BC_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_contracts'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>Contract</b></font> with CN <font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been uploaded. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";

                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);
                    break;

                case CORE_TASK_PROJ_COMPLETION_APPROVED:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED
                    );

                    $roles          = array(ROLE_PAY_FIN_PERS); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_projects'>$ref_number</a>";
                    $notification   = "<font color='#354575'> <b>Project Completion Files</b></font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> have been approved. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'> and import to PRIA.</font>";
  
                    // $this->pria_notification->import_reminder($roles, $module_code, $notification, $message);

                    break;

                case CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL:

                    $rem_task_details = $this->pria_mailer_model->get_task_details(
                        $task_details['pria_task_id'],
                        EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED
                    );

                    $roles          = array([ROLE_PROJ_ENG], [ROLE_PAY_FIN_PERS]); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];
                    $ref_number_link     = "<a href='" . get_link_url($module_code, $ref_number, NULL, $base_url_cron) . "#tab_purchase_orders'>$ref_number</a>";
                    $notification   = ["<font color='#354575'> <b>Released Purchase Order </b></font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been uploaded. You may now add </font><font color='#354575'><b>Project</b></font><font color='#000000'>.</font>", "<font color='#354575'> <b>Released Purchase Order </b></font><font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> has been uploaded. You may now prepare </font><font color='#354575'><b>APV</b></font><font color='#000000'>.</font>"];

                    $multiple       = TRUE;
                
                default:
                    # code...
                    break;
            }

            if(!EMPTY($roles)){

                if(ISSET($rem_task_details['org_code']) AND !EMPTY($rem_task_details['org_code']))
                {
                    $orgs_raw       = $this->pria_mailer_model->get_trans_orgs($rem_task_details['org_code'], $rem_task_details['org_type_code']);

                    $orgs           = (COUNT($orgs_raw) > 0) ? array_column($orgs_raw, 'org_code'): array();
                }

                if($multiple AND is_array($notification))
                {
                    foreach ($notification as $key => $value)
                    {
                        if(!EMPTY($roles[$key]))
                        {
                            $this->pria_notification->import_reminder($roles[$key], $module_code, $value, $message, $orgs, $user_id, $ref_number, $base_url_cron);
                        }
                    }
                }
                else
                {
                    $this->pria_notification->import_reminder($roles, $module_code, $notification, $message, $orgs, $user_id, $ref_number, $base_url_cron);
                }

                //Email Notifications
                //$this->_trigger_notifications($roles, $orgs, $task_details);
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

    private function _get_module_code_per_task_ag_code($ag_code)
	{
		try
		{
		  $row = $this->tm_model->get_module_account_group(['account_group_code' => $ag_code]);

		  return $row[0]['module_code'];
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

    public function _trigger_notifications($roles, $orgs, $task_details)
	{
		try
		{

			if(!EMPTY($roles))
			{
				$recipients = $this->pria_mailer_model->get_users_per($roles['role_code'], $orgs);

				$to_recipients = array_column($recipients,'user_id');

				if(ISSET($to_recipients) AND !EMPTY($to_recipients))
				{
					// $org_code						= $dr_details['org_code'];

					$transaction_params				= array(
							'to_user_ids'			=> $to_recipients,
							'reference_num'			=> $task_details['reference_num'],
							'ag_name'				=> $task_details['account_group_code'],
							'transaction_action'	=> 'Approved',
							'redirect'				=> PORTAL_TRANSACTIONS . "/forwarders#tab_drs"
					);

					$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_APV_APPROVAL, NULL, NULL, NULL, NULL, $this->session->user_id, NULL, $transaction_params);
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
}