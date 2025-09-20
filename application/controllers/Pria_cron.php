<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_cron extends Base_Controller
{
 
	public function __construct() 
	{
		parent::__construct();

		$this->load->model('Pria_cron_model', 'pria_cron_model');
	}

	/**
	 * Delete date from temp tables
	 */
	public function delete_tmp_tables()
	{
		if($this->input->is_cli_request())
		{

			$table_names = array(
				PORTAL_TMP_QA_IO,
				PORTAL_TMP_QA_PO,
				PORTAL_TMP_QA_PR,
				PORTAL_TMP_QA_SOA,
				PORTAL_TMP_QA_DR,
				PORTAL_TMP_QA_GR,
				PORTAL_TMP_QA_APV
			);

			foreach ($table_names as $table_name) {
				
				$this->pria_cron_model->delete_temp($table_name);
			
			}
			
		}
		else
		{
			echo "Error! Should not be accessed from URL address.";
		}
	}

	/**
	 * CRON for email notification for reminders
	 * Create a CRONJOBS to be run daily and call this function
	 */

	public function send_email_fhr()
	{
		try{
			//get
			// $daily_fhrs = $this->pria_cron_model->get_pending_fhrs();
			$daily_fhrs = FALSE;

			if($daily_fhrs){

				foreach ($daily_fhrs as $key => $daily_fhr)
				{
	            	$rem_task_details = $this->pria_mailer_model->get_task_details(
		                $daily_fhr['pria_task_id'],
		                EMAIL_NOTIF_SUB_IO_TASK_COMPLETED
		            );

	            	$roles = array(ROLE_BC_ADMIN, ROLE_CG_SUP);

		            if(!EMPTY($roles)){

		                if(ISSET($rem_task_details['org_code']) AND !EMPTY($rem_task_details['org_code']))
		                {
		                    $orgs_raw       = $this->pria_mailer_model->get_trans_orgs($rem_task_details['org_code'], $rem_task_details['org_type_code']);

		                    $orgs           = (COUNT($orgs_raw) > 0) ? array_column($orgs_raw, 'org_code'): array();
		                }
		                
		                //System Notifications
		                //$this->pria_notification->import_reminder($roles, $module_code, $notification, $message, $orgs, $user_id);

		                //Email Notifications
		                // $this->_trigger_notifications($roles, $orgs, $rem_task_details);
		            }

		            $recipient			= [];

		            $vendor_code		= (ISSET($rem_task_details['vendor_code']) AND !EMPTY($rem_task_details['vendor_code']))? $rem_task_details['vendor_code']: NULL;

					if(!EMPTY($vendor_code))
					{
						$vendors		= $this->pria_mailer_model->get_vendor_users_by_vendor_code($vendor_code);
						$recipient		= ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array();
					}
		            
		            // $this->_trigger_notifications([], [], $rem_task_details, $recipient);
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

	private function _trigger_notifications($roles, $orgs, $task_details, $vendor_users = [])
	{
		try
		{
            $base_url_cron	= get_sys_param_val(SYS_PARAM_CRON_SETTINGS, SYS_PARAM_BASE_URL_CRON);
            $base_url_cron	= (ISSET($base_url_cron['sys_param_value']) AND !EMPTY($base_url_cron['sys_param_value']))? $base_url_cron['sys_param_value']: NULL;

			if(!EMPTY($roles))
			{
				$recipients = $this->pria_mailer_model->get_users_per($roles, $orgs);

				$to_recipients = array_column($recipients,'user_id');

				if(ISSET($to_recipients) AND !EMPTY($to_recipients))
				{
					$transaction_params				= array(
							'to_user_ids'			=> $to_recipients,
							'reference_num'			=> $task_details['reference_num'],
							'ag_name'				=> $task_details['account_group_code'],
							'redirect'				=> PORTAL_TRANSACTIONS . "/contract_growers#tab_internal_orders",
							'base_url_cron'			=> $base_url_cron
					);
					
					$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_DAILY_FHR, NULL, NULL, NULL, NULL, $this->session->user_id, NULL, $transaction_params);
				}
			}

			if(is_array($vendor_users) AND count($vendor_users) > 0)
			{
				$transaction_params				= array(
						'to_user_ids'			=> $vendor_users,
						'reference_num'			=> $task_details['reference_num'],
						'ag_name'				=> $task_details['account_group_code'],
						'redirect'				=> PORTAL_TRANSACTIONS . "/contract_growers#tab_internal_orders",
						'base_url_cron'			=> $base_url_cron
				);
				
				$this->pria_mailer->pria_email_notifications(NOTIF_TRIGGER_ACTION, EMAIL_NOTIF_IMMEDIATE, EMAIL_NOTIF_DAILY_FHR, NULL, NULL, NULL, NULL, $this->session->user_id, NULL, $transaction_params);
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