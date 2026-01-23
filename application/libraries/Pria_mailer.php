<?php if (!defined('BASEPATH')) exit('No direct script access is allowed'); 

class Pria_mailer
{
	protected $CI;
	protected $filtered_roles;

	public function __construct() 
	{
		$this->CI =& get_instance();

		$this->CI->load->library('Notification_queues');
		$this->CI->load->model('Pria_mailer_model', 'pria_mailer_model');
		$this->CI->lang->load('email', 'english');

		$this->base_url			= NULL;
	}

	//Added the user_id so that it can support offline(logged out) response thru email
	public function pria_email_notifications($trigger, $sched=EMAIL_NOTIF_IMMEDIATE, $type=NULL, $encrypt_task_id=NULL, $encrypt_pria_task_id=NULL, $pria_task_action=NULL, $office_code=NULL, $user_id=NULL, $encrypt_return_pria_task_id=NULL, $transaction_params=array())
	{
		try
		{
			$this->base_url			= (ISSET($transaction_params['base_url_cron']) AND !EMPTY($transaction_params['base_url_cron']))? $transaction_params['base_url_cron']: base_url();
			$user_id				= (!EMPTY($user_id))? $user_id: ((ISSET($this->CI->session->user_id))? $this->CI->session->user_id: ADMINISTRATOR_UID);
			
			$pria_task_id			= ((!EMPTY($encrypt_pria_task_id))? decrypt_id($encrypt_pria_task_id): NULL);
			$return_pria_task_id	= ((!EMPTY($encrypt_return_pria_task_id))? decrypt_id($encrypt_return_pria_task_id): NULL);

			$setup_params			= array(
					'trigger'		=> $trigger,
					'sched'			=> $sched,
					'type'			=> $type,
					'core_task_id'	=> ((!EMPTY($encrypt_task_id))? decrypt_id($encrypt_task_id): NULL),
					'task_action'	=> ((!EMPTY($pria_task_action))? $pria_task_action: NULL)
			);
		
			$pria_notifications		= $this->CI->pria_mailer_model->get_notification_setups($setup_params);
	
			if(COUNT($pria_notifications) > 0)
			{
				foreach($pria_notifications AS $key => $pria_notification)
				{
					$this->process_email_notification($pria_notification, $pria_task_id, $office_code, $user_id, $return_pria_task_id, $transaction_params);
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

	public function process_email_notification($pria_notification, $pria_task_id, $office_code, $user_id, $return_pria_task_id, $transaction_params)
	{
		try
		{
			$system_title 						= get_setting(GENERAL, "system_title");
			$system_tagline						= get_setting(GENERAL, "system_tagline");
			$system_email						= get_setting(GENERAL, "system_email");
			$pria_logo							= "logo-v2.png"; //get_setting(GENERAL, "system_logo");
			$bounty_logo						= "bounty-logo.png";
			$pria_logo_src						= $this->base_url . PATH_IMAGES . "logo_white.png";
			$bounty_logo_src					= "";

			if( !EMPTY( $pria_logo ) )
			{
				$pria_logo_path 				= FCPATH . PATH_IMAGES . $pria_logo;
				$pria_logo_path 				= str_replace(array('\\','/'), array(DS,DS), $pria_logo_path);

				if( file_exists( $pria_logo_path ) )
				{
					$pria_logo_src				= $this->base_url . PATH_IMAGES . $pria_logo;
					$pria_logo_src				= getimagesize($pria_logo_path) ? $pria_logo_src : $this->base_url . PATH_IMAGES . "logo_white.png";
				}
			}

			if( !EMPTY( $bounty_logo ) )
			{
				$bounty_logo_path 				= FCPATH . PATH_IMAGES . $bounty_logo;
				$bounty_logo_path 				= str_replace(array('\\','/'), array(DS,DS), $bounty_logo_path);

				if( file_exists( $bounty_logo_path ) )
				{
					$bounty_logo_src 			= $this->base_url . PATH_IMAGES . $bounty_logo;
					$bounty_logo_src 			= getimagesize($bounty_logo_path) ? $bounty_logo_src : $this->base_url . PATH_IMAGES . "no.png";
				}
			}

			$email_data							= array(
					'from_email'				=> $system_email,
					'from_name'					=> $system_title,
					'subject'					=> NULL
			);

			$template_data						= array(
					'logo'						=> $pria_logo_src,
					'bounty_logo'				=> $bounty_logo_src,
					'tagline'					=> $system_tagline,
					'mail_content'				=> NULL
			);

			$template_path						= "emails/mail_notifications";

			if(ISSET($transaction_params['to_user_ids']) AND COUNT($transaction_params['to_user_ids']) > 0)
			{
				$to_users						= array_unique($transaction_params['to_user_ids']);
			}
			else
			{
				$to_users						= array_unique($this->_construct_mail_recipient($pria_notification, $pria_task_id, $office_code, $return_pria_task_id));
			}
			
			$to_users							= array_diff($to_users, array($user_id));
			
			if(COUNT($to_users) > 0)
			{
				$this->filtered_roles	= $this->_get_filtered_role();
				
				foreach($to_users AS $key => $to_user)
				{
					$user_details					= $this->_get_user_details($to_user);
					$pria_notification['email_notification_portal']	= (in_array($user_details['role_code'], $this->filtered_roles))? ENUM_NO: ENUM_YES;

					$mail_content					= $this->_construct_mail_content($to_user, $pria_notification, $pria_task_id, $user_id, $transaction_params);
					
					if(ISSET($mail_content['cc_email']) AND COUNT($email_data["cc_email"]) > 0)
					{
						$email_data['cc_email']		= $mail_content['cc_email'];
					}

					$email_data['subject']			= $mail_content['subject'];
					$template_data['mail_content']	= $mail_content['content'];
					$message						= ($pria_notification['email_notification_portal'] == ENUM_YES)? $this->CI->load->view($template_path, $template_data, TRUE): $mail_content['content'];

					$from_user 						= $user_id;
					//  print_var_export($user_details, $mail_content); die;
					if(ISSET($transaction_params['print_only']) AND !EMPTY($transaction_params['print_only']))
					{
						echo "<pre>";
						print_var_export($pria_notification);
						print_r($from_user . "<br/>");
						print_r($to_user . "<br/>");
						print_r($email_data);
						echo "</pre>";
						print_r($message);
						print_r("<br/>");
					}
					else
					{
						$this->CI->notification_queues->insert_email_queues($message, $from_user, $to_user, $email_data);
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

	private function _construct_mail_content($to_user, $pria_notification, $pria_task_id, $user_id, $transaction_params)
	{
		try
		{
			$content							= array();
			$task_details						= array();

			$content['subject']					= $pria_notification['email_notification_subject'];

			$email_template						= $this->CI->pria_mailer_model->get_email_template_details($pria_notification['email_template_id']);

			if(!ISSET($email_template['email_template_id']) OR EMPTY($email_template['email_template_id']))
			{
				return FALSE;
			}

			$email_content						= $email_template['email_template_content'];

			$email_template_params				= $this->_get_template_params($email_template['email_template_type'], $pria_notification['email_notification_type'], $pria_notification['email_notification_title'], $pria_task_id, $email_template['email_template_parameter'], $pria_notification['email_notification_subject'], $to_user, $user_id, $pria_notification['email_notification_portal'], $pria_notification['email_notification_task_action'], $transaction_params);

			$email_detail_content				= array();

			if(ISSET($pria_notification['email_detail_template_id']) AND !EMPTY($pria_notification['email_detail_template_id']))
			{

				$email_detail_template			= $this->CI->pria_mailer_model->get_email_template_details($pria_notification['email_detail_template_id']);

				if(ISSET($email_detail_template['email_template_id']) AND !EMPTY($email_detail_template['email_template_id']))
				{
					$email_detail_content		= $email_detail_template['email_template_content'];
					
					$detail_template_params		= $this->_construct_mail_details($email_detail_template['email_template_type'], $pria_notification['email_notification_type'], $pria_notification['email_notification_title'], $pria_task_id, $email_detail_template['email_template_parameter'], $pria_notification['email_detail_extend_template_id'], $pria_notification['email_notification_portal'], $pria_notification['email_notification_task_action'], $transaction_params);
				
					if(COUNT($detail_template_params) > 0)
					{
						$email_detail_content	= vsprintf($email_detail_content, $detail_template_params);
					}

					$email_detail_content		= array($email_detail_content);
				}
			}

			if(ISSET($email_template_params['subject']) AND !EMPTY($email_template_params['subject']))
			{
				$content['subject']				= $email_template_params['subject'];
			}

			if(COUNT($email_template_params['params']) > 0)
			{
				$email_content					= vsprintf($email_content, $email_template_params['params']);
			}
			
			if(COUNT($email_detail_content) > 0)
			{
				$email_content					= vsprintf($email_content, $email_detail_content);
			}

			$email_content						= str_replace('%s', '', $email_content);

			$content['content']					= $email_content;

			return $content;
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

	private function _get_template_params($template_type, $type, $template_title, $pria_task_id, $notif_template_params, $template_subject, $to_user, $user_id, $email_notification_portal, $email_notification_task_action, $transaction_params)
	{
		try
		{
			$template_params							= array();
			$general_task_details						= array();
			$next_task_details							= array();
			$pria_link									= $this->base_url;
			$action_link								= "";
			$task_link									= "";
			$task_details								= "%s";
			$trans_details								= "%s";
			$footer										= "";

			$encrypt_user_id							= encrypt_id($to_user);
			$encoded_type								= base64_url_encode($type);

			$param_subject								= $template_subject;

			if(COUNT(explode(', ', $notif_template_params)) > 0)
			{
				if(!EMPTY($to_user))
				{
					$to_user_details					= $this->_get_user_details($to_user);
					//$salutation							= ($email_notification_portal == ENUM_YES AND !EMPTY($to_user_details['gender']))? (($to_user_details['gender'] == GENDER_MALE)? "Sir": (($to_user_details['gender'] == GENDER_FEMALE)? "Ma'am": NULL)): NULL;
					$salutation 						= '';
					$template_params['params'][]		= $salutation . ((!EMPTY($salutation))? " ": "") . $to_user_details['full_name'];
				}

				if(!EMPTY($pria_task_id))
				{
					$general_task_details				= $this->CI->pria_mailer_model->get_task_details($pria_task_id);
					$user_full_name						= $general_task_details['full_name'];

					$next_task_details					= $this->CI->pria_mailer_model->get_prev_next_task_details($pria_task_id, ENUM_YES);
				}
				else
				{
					//	$user_id						= ISSET($this->CI->session->user_id)? $this->CI->session->user_id: ADMINISTRATOR_UID;
					$user_details						= $this->_get_user_details($user_id);

					$user_full_name						= $user_details['full_name'];
				}

				switch($template_type)
				{
					case EMAIL_NOTIF_TYPE_UPLOAD_LIST:
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $pria_link;
					break;
					case EMAIL_NOTIF_TYPE_UPLOAD_FILE:
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['ag_name'];
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_TASK, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type);
					break;
					case EMAIL_NOTIF_TYPE_FOR_APPROVAL_SIMPLE:
						$task_link						= $this->base_url . "index.php?redirect=" . PORTAL_TRANSACTIONS . "/" . $general_task_details['controller'] . "?t=" . base64_url_encode($pria_task_id);

						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $general_task_details['ag_name'];
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $task_link;
					break;
					case EMAIL_NOTIF_TYPE_FOR_APPROVAL_REVIEW_DETAILED:
						$next_pria_task_id				= NULL;
						$next_controller				= NULL;

						if(COUNT($next_task_details) > 0)
						{
							$next_pria_task_id			= $next_task_details[0]['pria_task_id'];
							$next_controller			= $next_task_details[0]['controller'];
						}

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_APPROVAL, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type, $next_pria_task_id, $next_controller);
					break;
					case EMAIL_NOTIF_TYPE_FOR_APPROVAL_W_ACTION:
						$next_pria_task_id				= NULL;
						$next_controller				= NULL;

						if(COUNT($next_task_details) > 0)
						{
							$next_pria_task_id			= $next_task_details[0]['pria_task_id'];
							$next_controller			= $next_task_details[0]['controller'];
						}

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_APPROVAL, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type, $next_pria_task_id, $next_controller);
					break;
					case EMAIL_NOTIF_TYPE_FOR_APPROVAL_FINAL_W_ACTION:
						$next_pria_task_id				= NULL;
						$next_controller				= NULL;

						if(COUNT($next_task_details) > 0)
						{
							$next_pria_task_id			= $next_task_details[0]['pria_task_id'];
							$next_controller			= $next_task_details[0]['controller'];
						}

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_APPROVAL, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type, $next_pria_task_id, $next_controller);
					break;
					case EMAIL_NOTIF_TYPE_FINAL_APPROVED_DETAILED:
						$form_name						= "";
						$descriptive_reference			= "";
						$full_name						= "";
						$details_title					= "";

						switch($type)
						{
							case EMAIL_NOTIF_SUB_SRR_FINAL_APPROVED_DETAILED:
								$form_name				= "site nomination form";
								$descriptive_reference	= "SN Number: " . $general_task_details['reference_num'];
								$full_name				= $user_full_name;
								$details_title			= "nominated site";
							break;
						}

						$template_params['params'][]	= $form_name;
						$template_params['params'][]	= $descriptive_reference;
						$template_params['params'][]	= $full_name;
						$template_params['params'][]	= $details_title;
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $pria_link;
					break;
					case EMAIL_NOTIF_TYPE_FOR_ACCEPTANCE_W_ACTION:

						if(COUNT($next_task_details) > 0)
						{
							$encrypt_id					= encrypt_id($next_task_details[0]['pria_task_id']);
							$task_link					= $this->base_url . "index.php?redirect=" . PORTAL_TRANSACTIONS . "/" . $next_task_details[0]['controller'] . "?t=" . base64_url_encode($next_task_details[0]['pria_task_id']);
							$action_link				= $this->_generate_action_link($to_user, $user_id, $next_task_details[0]['pria_task_id'], $type);
						}

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= ($email_notification_portal == ENUM_YES)? $task_link: $action_link;
					break;
					case EMAIL_NOTIF_TYPE_FOR_ACCEPTANCE_W_ACTION_TAT:
						$details_title					= "";
						$due_date						= "";

						if(COUNT($next_task_details) > 0)
						{
							$encrypt_id					= encrypt_id($next_task_details[0]['pria_task_id']);
							$task_link					= $this->base_url . "index.php?redirect=" . PORTAL_TRANSACTIONS . "/" . $next_task_details[0]['controller'] . "?t=" . base64_url_encode($next_task_details[0]['pria_task_id']);
							$action_link				= $this->_generate_action_link($to_user, $user_id, $next_task_details[0]['pria_task_id'], $type);
						}

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $details_title;
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $details_title;
						$template_params['params'][]	= ($email_notification_portal == ENUM_YES)? $task_link: $action_link;
						$template_params['params'][]	= $details_title;
						$template_params['params'][]	= $due_date;
					break;
			
					case EMAIL_NOTIF_TYPE_TASK_COMPLETED:
						$task_action					= ($general_task_details['returned_flag'] == ENUM_NO)? $general_task_details['task_action_name']: "Re-submitted";
						$path							= "";

						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= strtolower($task_action);
						$template_params['params'][]	= $general_task_details['task_name'];
						$template_params['params'][]	= $task_details;
					//	$template_params['params'][]	= $pria_link;
						
						$path                			= $this->base_url.'index.php?redirect='.PORTAL_TRANSACTIONS.'/'.$general_task_details['controller'].'?t='.base64_url_encode($pria_task_id);
					
						$template_params['params'][]	= $path;

						$param_subject					= (EMPTY($param_subject))? $task_action . ": " . $general_task_details['task_name']: $param_subject;
					break;
					case EMAIL_NOTIF_TYPE_TASK_RETURNED:
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['reference_num'];
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $general_task_details['remarks'];
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $pria_link;

						$param_subject					= (EMPTY($param_subject))? "Returned: " . $general_task_details['task_name']: $param_subject;
					break;
					case EMAIL_NOTIF_TYPE_TASK_COMPLETED_NEW:
						$task_action					= ($general_task_details['returned_flag'] == ENUM_NO)? $general_task_details['task_action_name']: "Re-submitted";

						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= strtolower($task_action);
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $general_task_details['ag_name'];
						$template_params['params'][]	= $task_details;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_TASK, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type);

						$param_subject					= (EMPTY($param_subject))? $task_action . ": " . $general_task_details['task_name']: $param_subject;
					break;
					case EMAIL_NOTIF_TYPE_TRANSACTION_W_ACTION:
						$transaction_action				= (ISSET($transaction_params['transaction_action']) AND !EMPTY($transaction_params['transaction_action']))? $transaction_params['transaction_action']: NULL;
						$reference_num					= (ISSET($transaction_params['reference_num']) AND !EMPTY($transaction_params['reference_num']))? $transaction_params['reference_num']: NULL;
						$redirect						= (ISSET($transaction_params['redirect']) AND !EMPTY($transaction_params['redirect']))? $transaction_params['redirect']: NULL;

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $reference_num;
						$template_params['params'][]	= (ISSET($transaction_params['ag_name']) AND !EMPTY($transaction_params['ag_name']))? $transaction_params['ag_name']: NULL;
						$template_params['params'][]	= strtolower($transaction_action);
						$template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_PRIA, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type, NULL, NULL, $redirect);

						$param_subject					= $transaction_action . ": " . $param_subject . ((!EMPTY($reference_num))? " [" . $reference_num . "]": "");
					break;
					case EMAIL_NOTIF_TYPE_TRANSACTION_IS_ADDTL_MSG:
						$reference_num					= (ISSET($transaction_params['reference_num']) AND !EMPTY($transaction_params['reference_num']))? $transaction_params['reference_num']: NULL;
						$is_msg							= (ISSET($transaction_params['is_msg']) AND !EMPTY($transaction_params['is_msg']))? $transaction_params['is_msg']: NULL;
						$is_msg_content					= (ISSET($transaction_params['is_msg_content']) AND !EMPTY($transaction_params['is_msg_content']))? $transaction_params['is_msg_content']: NULL;
						$additional_msg					= (ISSET($transaction_params['additional_msg']) AND !EMPTY($transaction_params['additional_msg']))? $transaction_params['additional_msg']: NULL;

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $reference_num;
						$template_params['params'][]	= (ISSET($transaction_params['ag_name']) AND !EMPTY($transaction_params['ag_name']))? $transaction_params['ag_name']: NULL;
						$template_params['params'][]	= strtolower($is_msg_content);
						$template_params['params'][]	= $trans_details;
						$template_params['params'][]	= $additional_msg;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_PRIA, $email_notification_portal);

						$param_subject					= ucwords($is_msg) . ": " . $param_subject . ((!EMPTY($reference_num))? " [" . $reference_num . "]": "");
					break;

					case EMAIL_NOTIF_TYPE_APV:
						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $pria_link;
					break;

					case EMAIL_NOTIF_DAILY_FHR:
						$transaction_action				= (ISSET($transaction_params['transaction_action']) AND !EMPTY($transaction_params['transaction_action']))? $transaction_params['transaction_action']: NULL;
						$reference_num					= (ISSET($transaction_params['reference_num']) AND !EMPTY($transaction_params['reference_num']))? $transaction_params['reference_num']: NULL;
						$redirect						= (ISSET($transaction_params['redirect']) AND !EMPTY($transaction_params['redirect']))? $transaction_params['redirect']: NULL;

						$template_params['params'][]	= $template_title;
						$template_params['params'][]	= $reference_num;
						$template_params['params'][]	= (ISSET($transaction_params['ag_name']) AND !EMPTY($transaction_params['ag_name']))? $transaction_params['ag_name']: NULL;
						// $template_params['params'][]	= strtolower($transaction_action);
						// $template_params['params'][]	= $user_full_name;
						$template_params['params'][]	= $this->_construct_mail_footer(EMAIL_FOOTER_PRIA, $email_notification_portal, $general_task_details['pria_task_id'], $template_title, $general_task_details['controller'], $to_user, $user_id, $type, NULL, NULL, $redirect);

						$param_subject					= $transaction_action . ": " . $param_subject . ((!EMPTY($reference_num))? " [" . $reference_num . "]": "");
					break;
				}

				$template_params['subject']				= $param_subject . ((ISSET($general_task_details['reference_num']) AND !EMPTY($general_task_details['reference_num']))? " [" . $general_task_details['reference_num']. "]": "");
			}
			
			return $template_params;
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

	private function _construct_mail_details($template_type, $type, $template_title, $pria_task_id, $notif_template_params, $email_detail_extend_template_id, $email_notification_portal, $email_notification_task_action, $transaction_params = [])
	{	
		try
		{
			$template_params				= array();
			$email_detail_extend_template	= array();

			if(COUNT(explode(', ', $notif_template_params)) > 0)
			{
				// The function for retrieving data from the database, for the detailed email templates' use
				$detailed_task_details		= $this->CI->pria_mailer_model->get_task_details($pria_task_id, $type);
				// print_var_export($detailed_task_details); die;

				
				if(!EMPTY($email_detail_extend_template_id))
				{
					$email_detail_extend_template	= $this->CI->pria_mailer_model->get_email_template_details($email_detail_extend_template_id);
				}

				$task_details_extend_type	= (ISSET($email_detail_extend_template['email_template_type']) AND !EMPTY($email_detail_extend_template['email_template_type']))? $email_detail_extend_template['email_template_type']: NULL;
				$task_details_extend		= (ISSET($email_detail_extend_template['email_template_content']) AND !EMPTY($email_detail_extend_template['email_template_content']))? $email_detail_extend_template['email_template_content']: NULL;

				switch($template_type)
				{
					case EMAIL_NOTIF_TYPE_DTR: //This case is used for status. Pending, Submitted/Resubmitted, Completed and Returned
						$attachments		= "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= $document_type_name_arr[$key] . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						$template_params[]	= $detailed_task_details['transmittal_date'];
						$template_params[]	= $detailed_task_details['document_tracer_batch_number'];
						$template_params[]	= $detailed_task_details['business_center_name'];
						$template_params[]	= $detailed_task_details['courier_tracking_number'];
						$template_params[]	= $detailed_task_details['transmittal_document_sender'];

						switch($type)
						{
							case EMAIL_NOTIF_SUB_DTR_TASK_RETURNED:
								$template_params[]	= $detailed_task_details['return_remarks'];	
							break;
							case EMAIL_NOTIF_SUB_DTR_TASK_COMPLETED:
								$template_params[]	= $detailed_task_details['release_date'];
							break;
						}

						$template_params[]	= $attachments;
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_SOA_CENTRAL:
						$attachments		= "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= $document_type_name_arr[$key] . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						$template_params[]	= $detailed_task_details['soa_num'];
						$template_params[]	= $detailed_task_details['business_center_name'];
						$template_params[]	= $detailed_task_details['vendor_name'];
						$template_params[]	= $detailed_task_details['soa_date'];
						$template_params[]	= $detailed_task_details['soa_amount'];
						$template_params[]	= $attachments;
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL_BUDGETED:
						$amount_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "Additional": "Recommended";

						/*$budgeted			= "
							<tr>
								<td><b>Budgeted:</b></td>
								<td>" . (ISSET($detailed_task_details['budgeted_flag']) AND !EMPTY($detailed_task_details['budgeted_flag']) AND $detailed_task_details['budgeted_flag'] == 1)? "Yes": "No" . "</td>
							</tr>";*/

						$proj_reco_label	= "Project Engineer ";

						$addtl_reco			= "
							<tr>
								<td valign='top'><b>Remarks on Recommendations:</b></td>
								<td>" . nl2br((!EMPTY($detailed_task_details['boq_recommendation_bh']))? $detailed_task_details['boq_recommendation_bh']: "N/A") . "</td>
							</tr>";

						$addtl_template_params	= [$detailed_task_details['budget_amount_civil_works'], $detailed_task_details['budget_amount_signage']];

					case EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL:
						$proj_reco_label	= (ISSET($proj_reco_label))? $proj_reco_label: "";
						$addtl_reco			= (ISSET($addtl_reco))? $addtl_reco: "";
						$amount_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "Additional": "Recommended";
						$boq_file_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "As Built Plan": "BOQ";

						$attachments		= "N/A";
						$message			= "";

						$remarks			= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_code_arr	= explode(',', $detailed_task_details['document_type_codes']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= (($document_type_code_arr[$key] == DOC_TYPE_BOQ)? str_replace('BOQ', $boq_file_label, $document_type_name_arr[$key]): $document_type_name_arr[$key]) . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						if(in_array($detailed_task_details['task_status_id'], array(TASK_STATUS_APPROVED)))
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Remarks:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}

						$recommendations	= "
							<tr>
								<td valign='top'><b>".$proj_reco_label."Recommendations:</b></td>
								<td>" . nl2br((!EMPTY($detailed_task_details['boq_recommendation']))? $detailed_task_details['boq_recommendation']: "N/A") . "</td>
							</tr>";

						$recommendations	.= $addtl_reco;

						$template_params[]	= $detailed_task_details['reference_num'];	
						$template_params[]	= $detailed_task_details['business_center_name'];	
						$template_params[]	= $detailed_task_details['official_store_name'];	
						$template_params[]	= $detailed_task_details['recommended_vendor'];
						$template_params[]	= $amount_label;
						$template_params[]	= $detailed_task_details['reco_amount_civil_works'];
						$template_params[]	= $amount_label;
						$template_params[]	= $detailed_task_details['reco_amount_signage'];

						/*if(ISSET($budgeted) AND !EMPTY($budgeted))
						{
							$template_params[]	= $budgeted;	
						}*/

						if(ISSET($addtl_template_params) AND count($addtl_template_params) > 0)
							$template_params	= array_merge($template_params, $addtl_template_params);

						$template_params[]	= $recommendations;
						$template_params[]	= $attachments;
						$template_params[]	= $message;
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL_W_FINAL_AMOUNT:
						$attachments		= "N/A";
						$recommendations	= "";
						$message			= "";

						$amount_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "Additional": "Recommended";
						$boq_file_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "As Built Plan": "BOQ";

						if(in_array($detailed_task_details['core_workflow_task_id'], array(CORE_TASK_BOQ_MCS_APPROVED)))
						{
							$roh_recommendation	= (ISSET($detailed_task_details['boq_recommendation']) AND !EMPTY($detailed_task_details['boq_recommendation']))? $detailed_task_details['boq_recommendation']: "N/A";
							$bch_recommendation	= (ISSET($detailed_task_details['boq_recommendation_bh']) AND !EMPTY($detailed_task_details['boq_recommendation_bh']))? $detailed_task_details['boq_recommendation_bh']: "N/A";
							$rh_recommendation	= (ISSET($detailed_task_details['boq_justification']) AND !EMPTY($detailed_task_details['boq_justification']))? $detailed_task_details['boq_justification']: "N/A";

							if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_CONTRACTS_UPLOAD)
							{
								$recommendations .= "
								<tr>
									<td valign='top'><b>Recommendations:</b></td>
									<td>" . nl2br($roh_recommendation) . "</td>
								</tr>";
							}
							else
							{
								$recommendations .= "
									<tr>
										<td valign='top'><b>ROH Recommendations:</b></td>
										<td>" . nl2br($roh_recommendation) . "</td>
									</tr>";

								if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL)
								{
									$recommendations .= "
									<tr>
										<td valign='top'><b>Remarks on Recommendation:</b></td>
										<td>" . nl2br($bch_recommendation) . "</td>
									</tr>";
								}
								else
								{
									$recommendations .= "
										<tr>
											<td valign='top'><b>BCH Remarks on Recommendation:</b></td>
											<td>" . nl2br($bch_recommendation) . "</td>
										</tr>";

									if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL)
									{
										$recommendations .= "
										<tr>
											<td valign='top'><b>Remarks on Recommendation:</b></td>
											<td>" . nl2br($rh_recommendation) . "</td>
										</tr>";
									}
									else
									{
										$recommendations .= "
											<tr>
												<td valign='top'><b>Justification for Approval:</b></td>
												<td>" . nl2br($rh_recommendation) . "</td>
											</tr>";
									}
								}
							}
						}

						$remarks			= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_code_arr	= explode(',', $detailed_task_details['document_type_codes']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= (($document_type_code_arr[$key] == DOC_TYPE_BOQ)? str_replace('BOQ', $boq_file_label, $document_type_name_arr[$key]): $document_type_name_arr[$key]) . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						if(in_array($detailed_task_details['task_status_id'], array(TASK_STATUS_APPROVED)) AND 1==0)
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Remarks:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}

						$template_params[]	= $detailed_task_details['reference_num'];	
						$template_params[]	= $detailed_task_details['business_center_name'];	
						$template_params[]	= $detailed_task_details['official_store_name'];
						$template_params[]	= $detailed_task_details['recommended_vendor'];	
						$template_params[]	= $amount_label;
						$template_params[]	= $detailed_task_details['final_amount_civil_works'];
						$template_params[]	= $amount_label;
						$template_params[]	= $detailed_task_details['final_amount_signage'];
						$template_params[]	= $detailed_task_details['budget_amount_civil_works'];
						$template_params[]	= $detailed_task_details['budget_amount_signage'];
						$template_params[]	= $recommendations;
						$template_params[]	= $attachments;
						$template_params[]	= $message;
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL_AWARDED:
						$attachments		= "N/A";
						$message			= "";

						$remarks			= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= $document_type_name_arr[$key] . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						if(in_array($detailed_task_details['task_status_id'], array(TASK_STATUS_APPROVED)) AND 1==0)
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Remarks:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}

						$template_params[]	= $detailed_task_details['reference_num'];	
						$template_params[]	= $detailed_task_details['business_center_name'];	
						$template_params[]	= $detailed_task_details['official_store_name'];	
						$template_params[]	= $detailed_task_details['recommended_vendor'];	
						$template_params[]	= $detailed_task_details['awarded_contractor'];
						$template_params[]	= $attachments;
						$template_params[]	= $message;
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_SN_APPROVAL:
						$attachments		= "N/A";
						$recommendations	= "";
						$message			= "";

						$remarks			= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= $document_type_name_arr[$key] . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						if(in_array($detailed_task_details['core_workflow_task_id'], array(CORE_TASK_SITES_RECOM_SITE_NOMINATION, CORE_TASK_SITES_RECOM_SITE_REGIONAL, CORE_TASK_SITES_RECOM_SITE_PRES, CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION)))
						{
							$roh_recommendation	= (ISSET($detailed_task_details['sn_recommendation']) AND !EMPTY($detailed_task_details['sn_recommendation']))? $detailed_task_details['sn_recommendation']: "N/A";
							$bch_recommendation	= (ISSET($detailed_task_details['sn_recommendation_bh']) AND !EMPTY($detailed_task_details['sn_recommendation_bh']))? $detailed_task_details['sn_recommendation_bh']: "N/A";
							$rh_recommendation	= (ISSET($detailed_task_details['sn_recommendation_rh']) AND !EMPTY($detailed_task_details['sn_recommendation_rh']))? $detailed_task_details['sn_recommendation_rh']: "N/A";

							if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_SITES_RECOM_SITE_NOMINATION)
							{
								$recommendations .= "
								<tr>
									<td valign='top'><b>Recommendations:</b></td>
									<td>" . nl2br($roh_recommendation) . "</td>
								</tr>";
							}
							else
							{
								$recommendations .= "
									<tr>
										<td valign='top'><b>ROH Recommendations:</b></td>
										<td>" . nl2br($roh_recommendation) . "</td>
									</tr>";

								if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_SITES_RECOM_SITE_REGIONAL)
								{
									$recommendations .= "
									<tr>
										<td valign='top'><b>Remarks on Recommendation:</b></td>
										<td>" . nl2br($bch_recommendation) . "</td>
									</tr>";
								}
								else
								{
									$recommendations .= "
										<tr>
											<td valign='top'><b>BCH Remarks on Recommendation:</b></td>
											<td>" . nl2br($bch_recommendation) . "</td>
										</tr>";

									if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_SITES_RECOM_SITE_PRES)
									{
										$recommendations .= "
										<tr>
											<td valign='top'><b>Remarks on Recommendation:</b></td>
											<td>" . nl2br($rh_recommendation) . "</td>
										</tr>";
									}
									else
									{
										$recommendations .= "
											<tr>
												<td valign='top'><b>RH Remarks on Recommendation:</b></td>
												<td>" . nl2br($rh_recommendation) . "</td>
											</tr>";
									}
								}
							}
						}

						if(in_array($detailed_task_details['task_status_id'], array(TASK_STATUS_APPROVED)) AND 1==0)
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Remarks:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}
						
						$template_params[]	= $detailed_task_details['reference_num'];	
						$template_params[]	= $detailed_task_details['business_center_name'];	
						$template_params[]	= $detailed_task_details['official_store_name'];	
						$template_params[]	= $attachments;
						$template_params[]	= $recommendations;
						$template_params[]	= $message;
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_SRR:
						$attachments		= "N/A";
						$recommendations	= "";
						$message			= "";

						$remarks			= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= $document_type_name_arr[$key] . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						if(in_array($detailed_task_details['core_workflow_task_id'], array(CORE_TASK_CONTRACTS_UPLOAD, CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL, CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL, CORE_TASK_PRES_APPROVE_CONTRACT_RENEWAL)))
						{
							$roh_recommendation	= (ISSET($detailed_task_details['cn_recommendation']) AND !EMPTY($detailed_task_details['cn_recommendation']))? $detailed_task_details['cn_recommendation']: "N/A";
							$bch_recommendation	= (ISSET($detailed_task_details['cn_recommendation_bh']) AND !EMPTY($detailed_task_details['cn_recommendation_bh']))? $detailed_task_details['cn_recommendation_bh']: "N/A";
							$rh_recommendation	= (ISSET($detailed_task_details['cn_recommendation_rh']) AND !EMPTY($detailed_task_details['cn_recommendation_rh']))? $detailed_task_details['cn_recommendation_rh']: "N/A";

							if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_CONTRACTS_UPLOAD)
							{
								$recommendations .= "
								<tr>
									<td valign='top'><b>Recommendations:</b></td>
									<td>" . nl2br($roh_recommendation) . "</td>
								</tr>";
							}
							else
							{
								$recommendations .= "
									<tr>
										<td valign='top'><b>ROH Recommendations:</b></td>
										<td>" . nl2br($roh_recommendation) . "</td>
									</tr>";

								if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL)
								{
									$recommendations .= "
									<tr>
										<td valign='top'><b>Remarks on Recommendation:</b></td>
										<td>" . nl2br($bch_recommendation) . "</td>
									</tr>";
								}
								else
								{
									$recommendations .= "
										<tr>
											<td valign='top'><b>BCH Remarks on Recommendation:</b></td>
											<td>" . nl2br($bch_recommendation) . "</td>
										</tr>";

									if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL)
									{
										$recommendations .= "
										<tr>
											<td valign='top'><b>Remarks on Recommendation:</b></td>
											<td>" . nl2br($rh_recommendation) . "</td>
										</tr>";
									}
									else
									{
										$recommendations .= "
											<tr>
												<td valign='top'><b>RH Remarks on Recommendation:</b></td>
												<td>" . nl2br($rh_recommendation) . "</td>
											</tr>";
									}
								}
							}
						}

						if(in_array($detailed_task_details['task_status_id'], array(TASK_STATUS_APPROVED)) AND 1==0)
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Remarks:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}

						$template_params[]	= $detailed_task_details['reference_num'];
						$template_params[]	= $detailed_task_details['business_center_name'];
						$template_params[]	= $detailed_task_details['official_store_name'];
						$template_params[]	= $detailed_task_details['vendor_name'];
						$template_params[]	= $attachments;
						$template_params[]	= $recommendations;
						$template_params[]	= $message;
					break;
					case EMAIL_NOTIF_TYPE_DETAILS_SRR_W_MSG:
						$template_params[]	= (ISSET($detailed_task_details['business_center_name']) AND !EMPTY($detailed_task_details['business_center_name']))? $detailed_task_details['business_center_name']: "N/A";
						$template_params[]	= (ISSET($detailed_task_details['official_store_name']) AND !EMPTY($detailed_task_details['official_store_name']))? $detailed_task_details['official_store_name']: "N/A";
						$template_params[]	= (ISSET($detailed_task_details['vendor_name']) AND !EMPTY($detailed_task_details['vendor_name']))? $detailed_task_details['vendor_name']: "N/A";
						$template_params[]	= (ISSET($detailed_task_details['file_name']) AND !EMPTY($detailed_task_details['file_name']))? $detailed_task_details['file_name']: "N/A";
						$template_params[]	= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";
					break;

					case EMAIL_NOTIF_TYPE_DETAILS_PO:
					case EMAIL_NOTIF_PO_RELEASED:
					case EMAIL_NOTIF_TYPE_DETAILS_PO_RELEASED:
						$attachments		= "N/A";

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= $document_type_name_arr[$key] . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
						}

						$template_params[]	= $detailed_task_details['reference_num'];
						$template_params[]	= $detailed_task_details['business_center_name'];
						$template_params[]	= $detailed_task_details['vendor_name'];
						$template_params[]	= $detailed_task_details['po_date'];
						$template_params[]	= $detailed_task_details['po_amount'];

						if($template_type == EMAIL_NOTIF_TYPE_DETAILS_PO_RELEASED)
						{
							$template_params[]	= $detailed_task_details['po_released_date'];
						}

						$template_params[]	= $attachments;
					break;
					case EMAIL_NOTIF_TYPE_DETAILS_TASK_COMPLETED:
						$template_params[]	= $detailed_task_details['task_name'];
						$template_params[]	= (!EMPTY($detailed_task_details['due_date_status']))? $detailed_task_details['due_date_status']: "N/A";
						$template_params[]	= (!EMPTY($detailed_task_details['full_name']))? $detailed_task_details['full_name']: "N/A";
						$template_params[]	= $detailed_task_details['reference_num'];
						$template_params[]	= $detailed_task_details['ag_name'];

						$task_details_extend_params	= array();

						switch($task_details_extend_type)
						{
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_IO:
								$task_details_extend_params	= array($detailed_task_details['business_center_name']);
							break;
						}

						if(COUNT($task_details_extend_params) > 0)
						{
							$task_details_extend			= vsprintf($task_details_extend, $task_details_extend_params);
						}

						$template_params[]					= $task_details_extend;

						$next_task_details					= $this->CI->pria_mailer_model->get_prev_next_task_details($pria_task_id, ENUM_YES);

						$task_completed_next_task_param		= "";

						if($email_notification_portal == ENUM_YES AND COUNT($next_task_details) > 0)
						{
							$task_completed_next_task_template				= $this->CI->pria_mailer_model->get_email_template_details(EMAIL_TEMPLATE_DETAILS_EXTEND_TASK_COMPLETED_NEXT_TASK);

							$next_task_params								= array();

							if(ISSET($task_completed_next_task_template['email_template_parameter']) AND COUNT(explode(', ', $task_completed_next_task_template['email_template_parameter'])) > 0)
							{								
								$task_completed_next_task_details_template	= $this->CI->pria_mailer_model->get_email_template_details(EMAIL_TEMPLATE_DETAILS_EXTEND_TASK_COMPLETED_NEXT_TASK_DETAILS);

								$next_task_details_params					= "";

								if(ISSET($task_completed_next_task_details_template['email_template_parameter']) AND COUNT(explode(', ', $task_completed_next_task_details_template['email_template_parameter'])) > 0)
								{
									foreach($next_task_details AS $key => $next_task_detail)
									{
										$next_task_details_params			.= (!EMPTY($next_task_details_params))? "<br/>": "";

										$task_completed_next_task_detail	= $this->CI->pria_mailer_model->get_task_details($next_task_detail['pria_task_id']);

										$next_task_resource					= (!EMPTY($task_completed_next_task_detail['full_name']))? $task_completed_next_task_detail['full_name']: $task_completed_next_task_detail['task_roles'];
										$next_task_due						= (!EMPTY($task_completed_next_task_detail['due_date_status']))? $task_completed_next_task_detail['due_date_status']: "N/A";

										$next_task_name_link				= "<a href='".$this->base_url."index.php?redirect=".PORTAL_TRANSACTIONS."/".$next_task_detail['controller']."?t=".base64_url_encode($next_task_detail['pria_task_id'])."' target='_blank'>" . $task_completed_next_task_detail['task_name'] . "</a>";

										$next_task_details_params			.= vsprintf($task_completed_next_task_details_template['email_template_content'], array($next_task_name_link, $next_task_resource, $next_task_due));
									}
								}

								$next_task_params							= array($next_task_details_params);
							}

							$task_completed_next_task_param					= vsprintf($task_completed_next_task_template['email_template_content'], $next_task_params);
						}

						$template_params[]	= $task_completed_next_task_param;
					break;
					case EMAIL_NOTIF_TYPE_DETAILS_TASK_COMPLETED_NEW:
						$bc_vendor				= "";
						$main_attachment		= "";
						$message				= "";

						$remarks				= (ISSET($detailed_task_details['remarks']) AND !EMPTY($detailed_task_details['remarks']))? $detailed_task_details['remarks']: "N/A";

						if(!in_array($type, array(EMAIL_NOTIF_SUB_PR_TASK_COMPLETED)))
						{
							$bc_vendor			.= "
								<tr>
									<td><b>Business Center:</b></td>
									<td>" . $detailed_task_details['business_center_name'] . "</td>
								</tr>";

							if(!in_array($type, array(EMAIL_NOTIF_SUB_SN_TASK_COMPLETED, EMAIL_NOTIF_SUB_SN_TASK_SIMPLE, EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED, EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_W_ACTION)))
							{
								$lessor_vendor		= array(EMAIL_NOTIF_SUB_SRR_TASK_COMPLETED);
								$contractor_vendor	= array(EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED);
								$vendor_label		= (!in_array($type, $lessor_vendor))? "Vendor Name": "Lessor";
								$vendor_label		= (!in_array($type, $contractor_vendor))? $vendor_label: "Confirmed Contractor";
								$bc_vendor			.= "
									<tr>
										<td><b>" . $vendor_label . ":</b></td>
										<td>" . $detailed_task_details['vendor_name'] . "</td>
									</tr>";
							}
						}

						if(ISSET($detailed_task_details['main_document_path']) AND !EMPTY($detailed_task_details['main_document_path']))
						{
							$attachments		= "";

							$boq_file_label			= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "As Built Plan": "BOQ";

							$attachment_arr			= explode(',', $detailed_task_details['main_document_path']);
							$document_name_arr		= explode(',', $detailed_task_details['main_document_name']);
							$document_type_code_arr	= explode(',', $detailed_task_details['document_type_codes']);
							$document_type_name_arr	= explode(',', $detailed_task_details['document_type_names']);

							if(COUNT($attachment_arr) > 0)
							{
								foreach($attachment_arr AS $key => $attachment)
								{
									$attachments	.= (!EMPTY($attachments))? "<br/>": "";
									$attachments	.= (($document_type_code_arr[$key] == DOC_TYPE_BOQ)? str_replace('BOQ', $boq_file_label, $document_type_name_arr[$key]): $document_type_name_arr[$key]) . "<br/>" . $this->_construct_anchor_tag($document_name_arr[$key], $attachment, TRUE);
								}
							}
							else
							{
								$attachments	= "N/A";
							}

							$main_attachment	.= "
								<tr>
									<td valign='top'><b>Main Attachment:</b></td>
									<td>" . $attachments . "</td>
								</tr>";
						}

						if(ISSET($detailed_task_details['recommendation']) AND !EMPTY($detailed_task_details['recommendation']))
						{
							$message	.= "
								<tr>
									<td valign='top'><b>Recommendation:</b></td>
									<td>" . nl2br($detailed_task_details['recommendation']) . "</td>
								</tr>";
						}

						if($email_notification_task_action == TASK_ACTION_RETURNED)
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Reason for Return:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}

						if($email_notification_task_action == TASK_ACTION_DISAPPROVED)
						{
							$message		.= "
								<tr>
									<td valign='top'><b>Reason for Disapproval:</b></td>
									<td>" . nl2br($remarks) . "</td>
								</tr>";
						}
						else
						{
							if(in_array($detailed_task_details['core_workflow_task_id'], array(CORE_TASK_CONTRACTS_UPLOAD, CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL, CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL, CORE_TASK_PRES_APPROVE_CONTRACT_RENEWAL)))
							{
								$roh_recommendation	= (ISSET($detailed_task_details['cn_recommendation']) AND !EMPTY($detailed_task_details['cn_recommendation']))? $detailed_task_details['cn_recommendation']: "N/A";
								$bch_recommendation	= (ISSET($detailed_task_details['cn_recommendation_bh']) AND !EMPTY($detailed_task_details['cn_recommendation_bh']))? $detailed_task_details['cn_recommendation_bh']: "N/A";
								$rh_recommendation	= (ISSET($detailed_task_details['cn_recommendation_rh']) AND !EMPTY($detailed_task_details['cn_recommendation_rh']))? $detailed_task_details['cn_recommendation_rh']: "N/A";

								if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_CONTRACTS_UPLOAD)
								{
									$message .= "
									<tr>
										<td valign='top'><b>Recommendations:</b></td>
										<td>" . nl2br($roh_recommendation) . "</td>
									</tr>";
								}
								else
								{
									$message .= "
										<tr>
											<td valign='top'><b>ROH Recommendations:</b></td>
											<td>" . nl2br($roh_recommendation) . "</td>
										</tr>";

									if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL)
									{
										$message .= "
										<tr>
											<td valign='top'><b>Remarks on Recommendation:</b></td>
											<td>" . nl2br($bch_recommendation) . "</td>
										</tr>";
									}
									else
									{
										$message .= "
											<tr>
												<td valign='top'><b>BCH Remarks on Recommendation:</b></td>
												<td>" . nl2br($bch_recommendation) . "</td>
											</tr>";

										if($detailed_task_details['core_workflow_task_id'] == CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL)
										{
											$message .= "
											<tr>
												<td valign='top'><b>Remarks on Recommendation:</b></td>
												<td>" . nl2br($rh_recommendation) . "</td>
											</tr>";
										}
										else
										{
											$message .= "
												<tr>
													<td valign='top'><b>RH Remarks on Recommendation:</b></td>
													<td>" . nl2br($rh_recommendation) . "</td>
												</tr>";
										}
									}
								}
							}
							else
							{
								if(in_array($detailed_task_details['task_status_id'], array(TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED)))
								{
									if(!in_array($type, array(EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED, EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_W_ACTION)))
									{
										$message	.= "
										<tr>
											<td valign='top'><b>Remarks:</b></td>
											<td>" . nl2br($remarks) . "</td>
										</tr>";
									}
								}
							}
						}

						$template_params[]	= $detailed_task_details['reference_num'];
						$template_params[]	= $bc_vendor;

						$task_details_extend_params	= array();

						switch($task_details_extend_type)
						{
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SOA:
								$task_details_extend_params	= array(
										$detailed_task_details['soa_date'],
										$detailed_task_details['soa_amount']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_INITIAL:
								$task_details_extend_params	= array($detailed_task_details['suggested_store_name']);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_OFFICIAL:
								$task_details_extend_params	= array(
										$detailed_task_details['suggested_store_name'],
										$detailed_task_details['official_store_name']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_OFFICIAL_ONLY:
								$task_details_extend_params	= array($detailed_task_details['official_store_name']);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_RECOMMENDED:
								$amount_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "Additional": "Recommended";

								$recommendations	= "
									<tr>
										<td valign='top'><b>Project Engineer Recommendations:</b></td>
										<td>" . nl2br($detailed_task_details['boq_recommendation']) . "</td>
									</tr>
									<tr>
										<td valign='top'><b>Remarks on Recommendations:</b></td>
										<td>" . nl2br((!EMPTY($detailed_task_details['boq_recommendation_bh']))? $detailed_task_details['boq_recommendation_bh']: "N/A") . "</td>
									</tr>";

								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['recommended_vendor'],
										$amount_label,
										$detailed_task_details['reco_amount_civil_works'],
										$amount_label,
										$detailed_task_details['reco_amount_signage'],
										$recommendations
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_BUDGETED:
								$amount_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "Additional": "Recommended";
								$boq_file_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "As Built Plan": "BOQ";

								$recommendations	= "
									<tr>
										<td valign='top'><b>Project Engineer Recommendations:</b></td>
										<td>" . nl2br($detailed_task_details['boq_recommendation']) . "</td>
									</tr>
									<tr>
										<td valign='top'><b>Remarks on Recommendations:</b></td>
										<td>" . nl2br((!EMPTY($detailed_task_details['boq_recommendation_bh']))? $detailed_task_details['boq_recommendation_bh']: "N/A") . "</td>
									</tr>";

								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['recommended_vendor'],
										$amount_label,
										$detailed_task_details['reco_amount_civil_works'],
										$amount_label,
										$detailed_task_details['reco_amount_signage'],
										$detailed_task_details['budget_amount_civil_works'],
										$detailed_task_details['budget_amount_signage'],
										$recommendations
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_FINAL_AMOUNT:
								$amount_label		= (ISSET($detailed_task_details['additional_flag']) AND !EMPTY($detailed_task_details['additional_flag']))? "Additional": "Recommended";

								$final_label		= ($detailed_task_details['core_workflow_task_id'] == CORE_TASK_BOQ_MCS_APPROVED)? "Final": "Approved";

								$recommendations	= "
									<tr>
										<td valign='top'><b>Project Engineer Recommendations:</b></td>
										<td>" . nl2br($detailed_task_details['boq_recommendation']) . "</td>
									</tr>
									<tr>
										<td valign='top'><b>Remarks on Recommendations:</b></td>
										<td>" . nl2br((!EMPTY($detailed_task_details['boq_recommendation_bh']))? $detailed_task_details['boq_recommendation_bh']: "N/A") . "</td>
									</tr>";
								
								$recommendations .= "
										<tr>
											<td valign='top'><b>Justification for Approval:</b></td>
											<td>" . nl2br((!EMPTY($detailed_task_details['boq_justification']))? $detailed_task_details['boq_justification']: "N/A") . "</td>
										</tr>";

								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['recommended_vendor'],
										$final_label . " " . $amount_label,
										$detailed_task_details['final_amount_civil_works'],
										$final_label . " " . $amount_label,
										$detailed_task_details['final_amount_signage'],
										$detailed_task_details['budget_amount_civil_works'],
										$detailed_task_details['budget_amount_signage'],
										$recommendations
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_PO:
								$task_details_extend_params	= array(
										$detailed_task_details['boq_num'],
										$detailed_task_details['po_date'],
										$detailed_task_details['po_amount'],
										$detailed_task_details['po_released_date']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO:
								$task_details_extend_params	= array(
										$detailed_task_details['po_date'],
										$detailed_task_details['po_amount']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO_RELEASED:
								$task_details_extend_params	= array(
										$detailed_task_details['po_date'],
										$detailed_task_details['po_amount'],
										$detailed_task_details['po_released_date']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO_DR:
								$task_details_extend_params	= array(
										$detailed_task_details['dr_num']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO_GR:
								$task_details_extend_params	= array(
										$detailed_task_details['dr_num'],
										$detailed_task_details['gr_num']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_MOCK_UP:
								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['recommended_vendor'],
										$detailed_task_details['layout_specifications']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_CODES:
								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['cost_center_code'],
										$detailed_task_details['site_code']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_MOBILIZATION:
								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['mobilization_date']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_TURNOVER:
								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['turnover_date']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_OPENING:
								$task_details_extend_params	= array(
										$detailed_task_details['official_store_name'],
										$detailed_task_details['opening_date']
								);
							break;
							case EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PR_CON:
								$task_details_extend_params	= array(
										$detailed_task_details['business_center_name'],
										$detailed_task_details['project_type'],
										$detailed_task_details['requestor_name'],
										$this->_construct_anchor_tag($detailed_task_details['rfa_document_name'], $detailed_task_details['rfa_document_path'], TRUE)
								);
							break;
						}

						if(COUNT($task_details_extend_params) > 0)
						{
							$task_details_extend			= vsprintf($task_details_extend, $task_details_extend_params);
						}

						$template_params[]					= $task_details_extend;

						$template_params[]					= $main_attachment;
						$template_params[]					= $message;


						$next_task_details					= $this->CI->pria_mailer_model->get_prev_next_task_details($pria_task_id, ENUM_YES);

						$task_completed_next_task_param		= "";

						if($email_notification_portal == ENUM_YES AND !in_array($email_notification_task_action, [TASK_ACTION_RETURNED, TASK_ACTION_DISAPPROVED]) AND COUNT($next_task_details) > 0)
						{
							$task_completed_next_task_template				= $this->CI->pria_mailer_model->get_email_template_details(EMAIL_TEMPLATE_DETAILS_EXTEND_TASK_COMPLETED_NEXT_TASK);

							$next_task_params								= array();

							if(ISSET($task_completed_next_task_template['email_template_parameter']) AND COUNT(explode(', ', $task_completed_next_task_template['email_template_parameter'])) > 0)
							{								
								$task_completed_next_task_details_template	= $this->CI->pria_mailer_model->get_email_template_details(EMAIL_TEMPLATE_DETAILS_EXTEND_TASK_COMPLETED_NEXT_TASK_DETAILS);

								$next_task_details_params					= "";

								if(ISSET($task_completed_next_task_details_template['email_template_parameter']) AND COUNT(explode(', ', $task_completed_next_task_details_template['email_template_parameter'])) > 0)
								{
									foreach($next_task_details AS $key => $next_task_detail)
									{
										$next_task_details_params			.= (!EMPTY($next_task_details_params))? "<br/>": "";

										$task_completed_next_task_detail	= $this->CI->pria_mailer_model->get_task_details($next_task_detail['pria_task_id']);

										$next_task_resource					= (!EMPTY($task_completed_next_task_detail['full_name']))? $task_completed_next_task_detail['full_name']: $task_completed_next_task_detail['task_roles'];
										$next_task_due						= (!EMPTY($task_completed_next_task_detail['due_date_status']))? $task_completed_next_task_detail['due_date_status']: "N/A";

										$next_task_name_link				= "<a href='".$this->base_url."index.php?redirect=".PORTAL_TRANSACTIONS."/".$next_task_detail['controller']."?t=".base64_url_encode($next_task_detail['pria_task_id'])."' target='_blank'>" . $task_completed_next_task_detail['task_name'] . "</a>";

										$next_task_details_params			.= vsprintf($task_completed_next_task_details_template['email_template_content'], array($next_task_name_link, $next_task_resource, $next_task_due));
									}
								}

								$next_task_params							= array($next_task_details_params);
							}

							$task_completed_next_task_param					= vsprintf($task_completed_next_task_template['email_template_content'], $next_task_params);
						}

						$template_params[]	= $task_completed_next_task_param;
					break;
					case EMAIL_NOTIF_TYPE_DETAILS_PR:
						$template_params[]	= $detailed_task_details['requestor_name'];
					break;
					case EMAIL_NOTIF_TYPE_DETAILS_CONTRACT:
						$template_params[]	= $transaction_params['business_center_name'];
						$template_params[]	= $transaction_params['official_store_name'];
						$template_params[]	= $transaction_params['vendor_name'];
					break;
				}
			}
		
			return $template_params;
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

	private function _construct_mail_recipient($pria_notification, $pria_task_id, $office_code, $return_pria_task_id)
	{
		try
		{
			$recipient						= array();

			$curr_task_types				= array(EMAIL_RECIPIENT_ROLE, EMAIL_RECIPIENT_MAIN_ROLE, EMAIL_RECIPIENT_REF_ROLE);
			$next_task_types				= array(EMAIL_RECIPIENT_NEXT_ROLE, EMAIL_RECIPIENT_NEXT_MAIN_ROLE, EMAIL_RECIPIENT_NEXT_REF_ROLE);
			$prev_task_types				= array(EMAIL_RECIPIENT_PREV_ROLE, EMAIL_RECIPIENT_PREV_MAIN_ROLE, EMAIL_RECIPIENT_PREV_REF_ROLE);

			$orgs							= array();
			
			switch($pria_notification['email_notification_send_to'])
			{
				case EMAIL_RECIPIENT_SPECIFIC_USER:
					$recipient_codes		= $this->CI->pria_mailer_model->get_recipient_type($pria_notification['email_notification_id']);
					$recipient				= ($recipient_codes AND COUNT($recipient_codes) > 0)? array_column($recipient_codes, 'recipient_code'): array();
				break;
				case EMAIL_RECIPIENT_SPECIFIC_ROLE:
					$recipient_codes		= $this->CI->pria_mailer_model->get_recipient_type($pria_notification['email_notification_id']);
					$recipient_codes		= ($recipient_codes AND COUNT($recipient_codes) > 0)? array_column($recipient_codes, 'recipient_code'): array();

					if(!EMPTY($pria_task_id))
					{
						$task_details		= $this->CI->pria_mailer_model->get_task_details($pria_task_id, $pria_notification['email_notification_type']);

						if(ISSET($task_details['org_code']) AND !EMPTY($task_details['org_code']))
						{
							$orgs_raw		= $this->CI->pria_mailer_model->get_trans_orgs($task_details['org_code'], $task_details['org_type_code']);

							$orgs			= (COUNT($orgs_raw) > 0)? array_column($orgs_raw, 'org_code'): array();
						}
					}
					else
					{
						$orgs				= is_array($office_code)? ((COUNT($office_code) > 0)? array_unique($office_code): array()): ((!EMPTY($office_code))? array($office_code): array());
					}

					if(in_array(TASK_ROLE_VENDOR, $recipient_codes))
					{
						$recipient_codes	= array_diff($recipient_codes, array(TASK_ROLE_VENDOR));

						$vendor_code		= (ISSET($task_details['vendor_code']) AND !EMPTY($task_details['vendor_code']))? $task_details['vendor_code']: NULL;

						if(!EMPTY($vendor_code))
						{
							$vendors		= $this->CI->pria_mailer_model->get_vendor_users_by_vendor_code($vendor_code);
							$recipient		= array_merge($recipient, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());
						}
					}

					if(COUNT($recipient_codes) > 0)
					{								
						$recipients			= $this->CI->pria_mailer_model->get_users_per($recipient_codes, $orgs);
						$recipient			= array_merge($recipient, (($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array()));
					}
				break;
				case EMAIL_RECIPIENT_SPECIFIC_OFFICE:
					$recipient_codes		= $this->CI->pria_mailer_model->get_recipient_type($pria_notification['email_notification_id']);
					$recipient_codes		= ($recipient_codes AND COUNT($recipient_codes) > 0)? array_column($recipient_codes, 'recipient_code'): array();

					if(COUNT($recipient_codes) > 0)
					{
						$recipients			= $this->CI->pria_mailer_model->get_users_per(array(), $recipient_codes);
						$recipient			= ($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array();
					}
				break;
				default:
					if(!EMPTY($pria_notification['core_workflow_task_id']))
					{
						if(!EMPTY($pria_task_id))
						{
							if(in_array($pria_notification['email_notification_send_to'], $curr_task_types))
							{
								$task_details		= $this->CI->pria_mailer_model->get_task_details($pria_task_id, $pria_notification['email_notification_type']);

								if(ISSET($task_details['org_code']) AND !EMPTY($task_details['org_code']))
								{
									$orgs_raw		= $this->CI->pria_mailer_model->get_trans_orgs($task_details['org_code'], $task_details['org_type_code']);

									$orgs			= (COUNT($orgs_raw) > 0)? array_column($orgs_raw, 'org_code'): array();
								}

								/*$resource_id		= (ISSET($task_details['resource_id']) AND !EMPTY($task_details['resource_id']))? $task_details['resource_id']: NULL;*/

								$vendor_code		= (ISSET($task_details['vendor_code']) AND !EMPTY($task_details['vendor_code']))? $task_details['vendor_code']: NULL;

								$main_role_flag		= NULL;

								if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_MAIN_ROLE)
								{
									$main_role_flag	= 1;
								}
								else if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_REF_ROLE)
								{
									$main_role_flag	= 0;
								}

								$recipients			= $this->CI->pria_mailer_model->get_users_per_task($pria_task_id, $main_role_flag, $orgs);
								$recipient			= ($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array();

								if(!EMPTY($vendor_code))
								{
									$vendors		= $this->CI->pria_mailer_model->get_vendor_users_per_task($pria_task_id, $vendor_code, $main_role_flag);
									$recipient		= array_merge($recipient, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());
								}
							}
							else if(in_array($pria_notification['email_notification_send_to'], $next_task_types))
							{
								$next_tasks						= $this->CI->pria_mailer_model->get_prev_next_task_details($pria_task_id, ENUM_YES);

								$exempt_core_task_ids			= array();

								$main_role_flag					= NULL;

								if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_NEXT_MAIN_ROLE)
								{
									$main_role_flag				= 1;
								}
								else if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_NEXT_REF_ROLE)
								{
									$main_role_flag				= 0;
								}

								if(COUNT($next_tasks) > 0)
								{
									foreach($next_tasks AS $key => $next_task)
									{
										$task_details			= $this->CI->pria_mailer_model->get_task_details($next_task['pria_task_id'], $pria_notification['email_notification_type']);

										if(ISSET($task_details['org_code']) AND !EMPTY($task_details['org_code']))
										{
											$orgs_raw	= $this->CI->pria_mailer_model->get_trans_orgs($task_details['org_code'], $task_details['org_type_code']);

											$orgs		= (COUNT($orgs_raw) > 0)? array_column($orgs_raw, 'org_code'): array();
										}

										$exempt_core_task_ids[]	= $task_details['core_workflow_task_id'];

										$resource_id			= (ISSET($task_details['resource_id']) AND !EMPTY($task_details['resource_id']))? $task_details['resource_id']: NULL;

										$vendor_code			= (ISSET($task_details['vendor_code']) AND !EMPTY($task_details['vendor_code']))? $task_details['vendor_code']: NULL;
									
										if(!EMPTY($resource_id))
										{
											$recipient			= array_merge($recipient, array($resource_id));
										}
										else
										{
											$recipients			= $this->CI->pria_mailer_model->get_users_per_task($next_task['pria_task_id'], $main_role_flag, $orgs);
											$recipient			= array_merge($recipient, (($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array()));

											if(!EMPTY($vendor_code))
											{
												$vendors		= $this->CI->pria_mailer_model->get_vendor_users_per_task($next_task['pria_task_id'], $vendor_code, $main_role_flag);
												$recipient		= array_merge($recipient, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());										
											}
										}
									}
								}

								$core_recipients				= $this->CI->pria_mailer_model->get_users_from_next_core_task($pria_notification['core_workflow_task_id'], $exempt_core_task_ids, $main_role_flag, $orgs);
								$recipient						= array_merge($recipient, (($core_recipients AND COUNT($core_recipients) > 0)? array_column($core_recipients, 'user_id'): array()));
							}
							else if(in_array($pria_notification['email_notification_send_to'], $prev_task_types))
							{
								if(EMPTY($return_pria_task_id))
								{
									$prev_tasks					= $this->CI->pria_mailer_model->get_prev_next_task_details($pria_task_id, ENUM_NO);
									$prev_key					= "pre_pria_task_id";
								}
								else
								{
									$prev_tasks					= $this->CI->pria_mailer_model->get_pria_task_range($pria_task_id, $return_pria_task_id);
									$prev_key					= "pria_task_id";
								}

								$main_role_flag					= NULL;

								if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_PREV_MAIN_ROLE)
								{
									$main_role_flag				= 1;
								}
								else if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_PREV_REF_ROLE)
								{
									$main_role_flag				= 0;
								}

								if(COUNT($prev_tasks) > 0)
								{
									foreach($prev_tasks AS $key => $prev_task)
									{
										$task_details			= $this->CI->pria_mailer_model->get_task_details($prev_task[$prev_key], $pria_notification['email_notification_type']);

										if(ISSET($task_details['org_code']) AND !EMPTY($task_details['org_code']))
										{
											$orgs_raw	= $this->CI->pria_mailer_model->get_trans_orgs($task_details['org_code'], $task_details['org_type_code']);

											$orgs		= (COUNT($orgs_raw) > 0)? array_column($orgs_raw, 'org_code'): array();
										}

										$resource_id			= (ISSET($task_details['resource_id']) AND !EMPTY($task_details['resource_id']))? $task_details['resource_id']: NULL;

										$vendor_code			= (ISSET($task_details['vendor_code']) AND !EMPTY($task_details['vendor_code']))? $task_details['vendor_code']: NULL;

										if(!EMPTY($resource_id))
										{
											$recipient			= array_merge($recipient, array($resource_id));
										}
										else
										{
											$recipients			= $this->CI->pria_mailer_model->get_users_per_task($prev_task[$prev_key], $main_role_flag, $orgs);
											$recipient			= array_merge($recipient, (($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array()));

											if(!EMPTY($vendor_code))
											{
												$vendors		= $this->CI->pria_mailer_model->get_vendor_users_per_task($prev_task[$prev_key], $vendor_code, $main_role_flag);
												$recipient		= array_merge($recipient, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());
											}
										}
									}
								}
							}
							else if($pria_notification['email_notification_send_to'] == EMAIL_RECIPIENT_PRIA_TASK_STAGE)
							{
								$recipients			= $this->CI->pria_mailer_model->get_pria_task_stage_users($pria_task_id);
								$recipient			= array_merge($recipient, (($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array()));
							}
						}
					}
				break;
			}

			return $recipient;
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

	private function _get_user_details($user_id)
	{
		try
		{
			return $this->CI->pria_mailer_model->get_user_details($user_id);
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

	private function _get_filtered_role()
	{
		try
		{
			$roles	=  $this->CI->pria_mailer_model->get_filtered_role();
			return (COUNT($roles) > 0)? array_column($roles, 'filtered_role'): array();
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

	private function _construct_anchor_tag($filename, $sysfilename, $rename_flag = FALSE)
	{
		try
		{
			$path = $this->base_url . (($rename_flag)? 'pria_file/view?file=': PATH_UPLOADED_FILES) . $sysfilename;

			return <<<EOS
				<a href="$path" target="_blank">$filename</a>
EOS;
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

	private function _generate_action_link($to_user, $user_id, $pria_task_id, $type)
	{
		try
		{
			$fields = [
				'to_user'					=> $to_user,
				'pria_task_id' 				=> $pria_task_id,
				'email_notification_type' 	=> $type,
				'created_by' 				=> $user_id,
				'created_date'				=> date(FORMAT_DB_DATETIME)
			];

			$id = $this->CI->pria_mailer_model->insert_task_email_link($fields);

			return  $this->base_url . "mail_action/pria_validate/".base64_url_encode($id);
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

	private function _construct_mail_footer($footer_type, $portal_flag, $pria_task_id=NULL, $template_title=NULL, $controller=NULL, $to_user_id=NULL, $from_user_id=NULL, $type=NULL, $next_pria_task_id=NULL, $next_controller=NULL, $redirect=NULL)
	{
		try
		{
			$mail_footer	= "";

			$pria_link		= $this->base_url;
			$task_link		= $this->base_url;
			$action_link	= "";

			$redirect_link	= (!EMPTY($redirect))? "index.php?redirect=" . $redirect: NULL;

			if(!EMPTY($pria_task_id))
			{
				$encrypt_id		= encrypt_id($pria_task_id);
				$task_link		= $pria_link . "index.php?redirect=" . PORTAL_TRANSACTIONS . "/" . $controller . "?t=" . base64_url_encode($pria_task_id);

				if(!EMPTY($next_pria_task_id))
				{
					$next_task_link	= $pria_link . "index.php?redirect=" . PORTAL_TRANSACTIONS . "/" . $next_controller . "?t=" . base64_url_encode($next_pria_task_id);
					$action_link	= $this->_generate_action_link($to_user_id, $from_user_id, $next_pria_task_id, $type);
				}
			}

			if($portal_flag == ENUM_YES)
			{
				switch($footer_type)
				{
					case EMAIL_FOOTER_PRIA:
						$pria_link		.= (!EMPTY($redirect_link))? $redirect_link: "";
						$mail_footer	.= sprintf($this->CI->lang->line('email_footer_bavi_pria'), $pria_link);
					break;
					case EMAIL_FOOTER_TASK:
						$mail_footer	.= sprintf($this->CI->lang->line('email_footer_bavi_pria'), $task_link);
					break;
					case EMAIL_FOOTER_APPROVAL:
						$mail_footer	.= sprintf($this->CI->lang->line('email_footer_bavi_approval'), $template_title, $action_link, $next_task_link);
						$mail_footer	.= "<br/><br/>";
						$mail_footer	.= sprintf($this->CI->lang->line('email_footer_bavi_pria'), $task_link);
					break;
				}

				$mail_footer			.= (!EMPTY($mail_footer))? "<br/><br/>": "";

				$mail_footer			.= $this->CI->lang->line('email_footer_bavi');
			}
			else
			{
				switch($footer_type)
				{
					case EMAIL_FOOTER_APPROVAL:
						$mail_footer	.= sprintf($this->CI->lang->line('email_footer_simple_approval'), $template_title, $action_link);
					break;
				}

				$mail_footer			.= (!EMPTY($mail_footer))? "<br/><br/>": "";

				$mail_footer			.= $this->CI->lang->line('email_footer_simple');
			}

			return $mail_footer;
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