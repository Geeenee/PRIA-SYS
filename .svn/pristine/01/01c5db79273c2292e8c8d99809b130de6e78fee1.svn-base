<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Renewal_contract extends Base_Controller
{
 
	public function __construct() 
	{
		parent::__construct();

		$this->load->model('Pria_cron_model', 'pria_cron_model');
		$this->load->model(PORTAL_TRANSACTIONS . '/' . FOLDER_CONTRACTS.'/contracts_model');

	}

	/**
	 * Update contract status if contract is overdue 
	 */
	public function check_contracts()
	{
		if($this->input->is_cli_request())
		{

			/* $renewal_contracts 			= $this->pria_cron_model->check_contracts(CRON_CONTRACT);
			$overdue_renewal_contracts 	= $this->pria_cron_model->check_contracts(CRON_OVERDUE_CONTRACT);

			if(!EMPTY($renewal_contracts)){
				foreach ($renewal_contracts as $key => $renewal_contract) {
					$where = array(
						'contract_id' => $renewal_contract['contract_id']
					);

					$fields = array(
						'contract_status_code' => CONTRACT_OVERDUE
					);

					//Update Status of contract to OVERDUE
					$this->pria_cron_model->update_contract_status($where, $fields);
					
					// Insert to reminders
		            $roles 			= array(ROLE_ROH); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];

                    $org_code 		= ISSET($renewal_contract['org_code']) ? array($renewal_contract['org_code']) : array();
                    $notification   = "<font color='#000000'> <b>Contract</b> with CN</font><font color='#e23b3b'>".$ref_number." </font><font color='#000000'> is due for renewal. You may now create and upload your <b>site renewal recommendation</b></font>";
                    
                    $this->pria_notification->import_reminder($roles, MODULE_PORTAL_TRANS_LESSORS, $notification, $message, $org_code);
		            //Ends

		            //Email Notification
		            //$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, $task_action);
				}
			}

			if(!EMPTY($overdue_renewal_contracts)){
				foreach ($overdue_renewal_contracts as $key => $overdue_renewal_contract) {
					$where = array(
						'contract_id' => $overdue_renewal_contract['contract_id']
					);

					$fields = array(
						'contract_status_code' => CONTRACT_DUE_RENEWAL
					);

					//Update Status of contract to OVERDUE
					$this->pria_cron_model->update_contract_status($where, $fields);
					
					// Insert to reminders
		            $roles 			= array(ROLE_ROH); //specify role to be notified
                    $message        = '';       //notification for mobile
                    $ref_number     = $task_details['reference_num'];

                    $org_code 		= ISSET($overdue_renewal_contract['org_code']) ? array($overdue_renewal_contract['org_code']) : array();
                    $notification   = "<font color='#000000'> <b>Contract</b> with CN</font><font color='#e23b3b'>".$ref_number." </font><font color='#000000'> is due for renewal. You may now create and upload your <b>site renewal recommendation</b></font>";
                    
                    $this->pria_notification->import_reminder($roles, MODULE_PORTAL_TRANS_LESSORS, $notification, $message, $org_code);
		            //Ends

		            //Email Notification
		            //$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, NULL, $core_task_id, $pria_task_id, $task_action);
				}
			} */


			$contracts  = $this->pria_cron_model->get_all_contracts_for_update();

			$overdue    = get_sys_param_val(SYS_PARAM_TYPE_CONTRACT_STATUS, SYS_PARAM_CONTRACT_OVERDUE);
            $overdue	= (ISSET($overdue['sys_param_value']) AND !EMPTY($overdue['sys_param_value']))? $overdue['sys_param_value']: NULL;
			$due_renew  = get_sys_param_val(SYS_PARAM_TYPE_CONTRACT_STATUS, SYS_PARAM_CONTRACT_DUE_RENEWAL);
            $due_renew	= (ISSET($due_renew['sys_param_value']) AND !EMPTY($due_renew['sys_param_value']))? $due_renew['sys_param_value']: NULL;

			$date_today = date(FORMAT_DB_DATE);

            $base_url_cron	= get_sys_param_val(SYS_PARAM_CRON_SETTINGS, SYS_PARAM_BASE_URL_CRON);
            $base_url_cron	= (ISSET($base_url_cron['sys_param_value']) AND !EMPTY($base_url_cron['sys_param_value']))? $base_url_cron['sys_param_value']: NULL;
			
			if( is_array($contracts) AND count($contracts) > 0)
			{
				foreach($contracts as $c)
				{
					$con_end_date 	= $c['date_to'];

					$due_renew_date = date(FORMAT_DB_DATE, strtotime("-".$due_renew." months", strtotime($con_end_date)));
            		$overdue_date   = date(FORMAT_DB_DATE, strtotime("-".$overdue." months", strtotime($con_end_date)));

					/*$date_diff		= dateDifference($con_end_date, $date_today);	

					if($date_diff == $due_renew)
					{
						$status 		= CONTRACT_DUE_RENEWAL;
						$status_name	= 'due for renewal';
					}

					if($date_diff == $overdue)		
					{
						$status 		= CONTRACT_OVERDUE;
						$status_name	= 'overdue';
					}

					if($date_diff < $overdue &&  strtotime($date_now) > strtotime($con_end_date) )	
					{
						$status 		= CONTRACT_EXPIRED;
						$status_name 	= 'expired';
					}	*/

					$status = $c['contract_status_code'];

					if($con_end_date <= $date_today)
		            {
		                $status					= CONTRACT_EXPIRED;
						$status_name 			= 'expired';
						$status_name_content 	= 'has expired';
		            }
		            else if($overdue_date <= $date_today)
		            {
		                $status					= CONTRACT_OVERDUE;
						$status_name			= 'overdue';
						$status_name_content	= 'is overdue';
		            }
		            else if($due_renew_date <= $date_today)
		            {
		                $status					= CONTRACT_DUE_RENEWAL;
						$status_name			= 'due for renewal';
						$status_name_content	= 'is due for renewal';
		            }

		            $additional_msg		= $this->lang->line('contract_status_update_message');

		            if($status != CONTRACT_NEW AND $status != $c['contract_status_code'])
		            {
						$where = array(
							'contract_id' => $c['contract_id']
						);

						$fields = array(
							'contract_status_code' => $status
						);

						$this->pria_cron_model->update_contract_status($where, $fields);

						$roles 			= array(ROLE_ROTI_ADMIN, ROLE_ROH, ROLE_BC_HEAD); //specify role to be notified
	                    $message        = '';       //notification for mobile
	                    $ref_number     = $c['contract_code'];
	                    $ref_number_link= "<a href='" . get_link_url(MODULE_PORTAL_TRANS_LESSORS, $ref_number, NULL, $base_url_cron) . "#tab_contracts'>$ref_number</a>";
	                    $org_code 		= ISSET($c['org_code']) ? array($c['org_code']) : array();

	                    $notification   = "<font color='#000000'> <b>Contract</b> with CN</font> <font color='#e23b3b'>".$ref_number_link." </font><font color='#000000'> $status_name_content. You may now create and upload your <b>contract renewal recommendation</b></font>";
	                    
	                    $this->pria_notification->import_reminder($roles, MODULE_PORTAL_TRANS_LESSORS, $notification, $message, $org_code, ADMINISTRATOR_UID, $ref_number);

		                $bc_details         = $this->contracts_model->get_organization(['org_code' => $c['org_code']], ['name org_name']);
		                $vendor_details     = $this->contracts_model->get_vendor(['vendor_code' => $c['vendor_code']], ['vendor_name']);
		                $site_details       = $this->contracts_model->get_site(['site_id' => $c['site_id']], ['official_store_name']);

		                $business_center_name   = (ISSET($bc_details['org_name']) AND !EMPTY($bc_details['org_name']))? $bc_details['org_name']: "";
		                $vendor_name            = (ISSET($vendor_details['vendor_name']) AND !EMPTY($vendor_details['vendor_name']))? $vendor_details['vendor_name']: "";
		                $official_store_name    = (ISSET($site_details['official_store_name']) AND !EMPTY($site_details['official_store_name']))? $site_details['official_store_name']: "";

	                    $transaction_params	= [
								'reference_num'			=> $ref_number,
								'ag_name'				=> "Lessors",
		                        'business_center_name'  => $business_center_name,
		                        'vendor_name'           => $vendor_name,
		                        'official_store_name'   => $official_store_name,
								'is_msg'				=> $status_name,
								'is_msg_content'		=> $status_name_content,
								'additional_msg'		=> $additional_msg,
								'base_url_cron'			=> $base_url_cron
	                    ];

	                    $this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_SUB_CONTRACT_TRANSACTION_IS_ADDTL_MSG, NULL, NULL, NULL, $org_code, NULL, NULL, $transaction_params);
	                }
				}
			}

			$contracts = $this->contracts_model->get_contracts_for_payment_processing();
			
			if( ! EMPTY($contracts))
			{
				foreach($contracts as $c)
				{
					$roles 				= array(ROLE_BC_FIN_PERS);	//specify role to be notified
					$message        	= '';	//notification for mobile
					$ref_number     	= $c['contract_code'];
                    $ref_number_link	= "<a href='" . get_link_url(MODULE_PORTAL_TRANS_LESSORS, $ref_number, NULL, $base_url_cron) . "#tab_contracts'>$ref_number</a>";

					$org_code 		= (ISSET($c['org_code']) AND !EMPTY($c['org_code'])) ? array($c['org_code']) : array();
					
					$notification   = "<font color='#000000'> <b>Contract Payment</b> for </font><font color='#e23b3b'>".$ref_number." </font><font color='#000000'> is due on <b>".date('F d, Y', strtotime($c['billing_date']))."</b>. You may now prepare <b>APV</b> and import to PRIA</font>";

					$this->pria_notification->import_reminder($roles, MODULE_PORTAL_TRANS_LESSORS, $notification, $message, $org_code, ADMINISTRATOR_UID, $ref_number);

					// UPDATE NOTIFIED FLAG OF SENT REMINDER
					$fields			= [
							'notified'		=> ENUM_YES
					];

					$where			= [
							'contract_id'	=> $c['contract_id'],
							'billing_date'	=> $c['billing_date'],
							'reminder_date'	=> $c['reminder_date']
					];

					$this->contracts_model->update_contract_billing($fields, $where);
				}
			}
		}
		else
		{
			echo "Error! Should not be accessed from URL address.";
		}
	}
}