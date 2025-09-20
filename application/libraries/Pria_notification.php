<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_notification {
    
    protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        
        $this->CI->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');
        $this->CI->load->model('pria_mailer_model', 'pria_mailer_model');
    }


    public function system_notification($task_id, $task_details = array(), $module_code = NULL, $roles = array(), $task_return_id = NULL, $user_id=NULL, $from_ret_flag = FALSE, $status_label = '', $task_orig_details = '', $ref_orgs = array(), $vendor_code = NULL)
	{
		try{
			$remarks = '';

			//Starts			
			$user_id 		= ( ! EMPTY($user_id)) ? $user_id : $this->CI->session->user_id;

        	if($task_orig_details['remarks']){
        		$remarks    = " (Remarks: ".$task_orig_details['remarks'].")";
        	}

        	if($task_orig_details['reference_num']){
        		$ref_n      = "<font color='8F44A9'> ".$task_details['reference_num']." </font>";
        	}

            $orig_task_id   = (ISSET($task_orig_details['pria_task_id']) AND !EMPTY($task_orig_details['pria_task_id']))? $task_orig_details['pria_task_id']: array();
            
            $encode_link    = '/'.$task_orig_details['controller'].'?t='.base64_url_encode($orig_task_id);

            $recipient      = array();

            //insert to system notification
            //for return
            if($task_return_id)
            {
            	//$notification   = "<font color='#e23b3b'>".$task_details['actor']."</font> <font color='#000000'>returned </font><font color='#8F44A9'>".$task_details['task_name']."</font> <font color='#e23b3b'>(List: ".$task_details['reference_num'].")</font>";

                $prev_tasks             = $this->CI->pria_mailer_model->get_pria_task_range($orig_task_id, $task_return_id);

                foreach($prev_tasks AS $key => $prev_task)
                {
                    $ret_task_details   = $this->CI->pria_mailer_model->get_task_details($prev_task['pria_task_id']);

                    $resource_id        = (ISSET($ret_task_details['resource_id']) AND !EMPTY($ret_task_details['resource_id']))? $ret_task_details['resource_id']: NULL;

                    if(!EMPTY($resource_id))
                    {
                        $recipient      = array_merge($recipient, array($resource_id));
                    }
                }

                /*if($roles){
                	//call private function for sending email
	            	$this->_send_system_notification($roles, $module_code, $notification, $encode_link, $user_id, $ref_orgs);
                }else{
                	$this->CI->notify->insert_notification($notification, $notify_who, $user_id);	
                }*/

               	// else for completed
            }else{
            	/*$notification   = "<font color='#e23b3b'>".$task_orig_details['doc_name']."</font> ".$ref_n." <font color='#000000'>has been ".$status_label." </font>";*/

            	//$notification   = "<font color='#e23b3b'>".$task_details['actor']."</font> <font color='#000000'>completed </font><font color='#8F44A9'>".$task_details['task_name']."</font> <font color='#e23b3b'>(List: ".$task_details['reference_num'].")</font>";
                
            	/*if($roles){
            		//call private function for sending email
            		$this->_send_system_notification($roles, $module_code, $notification, $encode_link, $user_id, $ref_orgs);
            	}else{
            		foreach ($predecessors as $predecessor):
		                if($predecessor['pria_task_id'])
                        {
		                    $where = array( 'pria_task_id' => $predecessor['pria_task_id']);
		                    $roles = $this->CI->tm_model->get_task_roles($where);
		                    
		                    //call private function for sending email
		                    $this->_send_system_notification($roles, $module_code, $notification, $encode_link, $user_id, $ref_orgs);
		                }
		            endforeach;
            	}*/
                $where          = array('pre_pria_task_id' => $orig_task_id);
                $predecessors   = $this->CI->tm_model->get_next_predecessors($where);

                if(ISSET($roles[0]['role_code']) AND !EMPTY($roles[0]['role_code']))
                {
                    $roles_arr  = explode(',', $roles[0]['role_code']);

                    if(in_array(TASK_ROLE_VENDOR, $roles_arr))
                    {
                        $roles_arr          = array_diff($roles_arr, array(TASK_ROLE_VENDOR));

                        if(!EMPTY($vendor_code))
                        {
                            $vendors        = $this->CI->pria_mailer_model->get_vendor_users_per_task($orig_task_id, $vendor_code);
                            $recipient      = array_merge($recipient, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());
                        }
                    }

                    if(COUNT($roles_arr) > 0)
                    {
                        $recipients = $this->CI->pria_mailer_model->get_users_per($roles_arr, $ref_orgs);
                        $recipient  = array_merge($recipient, (($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array()));
                    }
                }

                if(is_array($predecessors) AND count($predecessors) > 0)
                {
                    foreach($predecessors as $predecessor)
                    {
                        $pre_task_details   = $this->CI->pria_mailer_model->get_task_details($predecessor['pria_task_id']);

                        $resource_id        = (ISSET($pre_task_details['resource_id']) AND !EMPTY($pre_task_details['resource_id']))? $pre_task_details['resource_id']: NULL;

                        if(!EMPTY($resource_id))
                        {
                            $recipient      = array_merge($recipient, array($resource_id));
                        }
                        else
                        {
                            $recipients     = $this->CI->pria_mailer_model->get_users_per_task($predecessor['pria_task_id'], NULL, $ref_orgs);
                            $recipient      = array_merge($recipient, (($recipients AND COUNT($recipients) > 0)? array_column($recipients, 'user_id'): array()));

                            if(!EMPTY($vendor_code))
                            {
                                $vendors    = $this->CI->pria_mailer_model->get_vendor_users_per_task($pre_task_details['pria_task_id'], $vendor_code, 1);
                                $recipient  = array_merge($recipient, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());                                      
                            }
                        }
                    }
                }

                if(in_array($task_orig_details['task_status_id'], array(TASK_STATUS_APPROVED, TASK_STATUS_DISAPPROVED)))
                {
                    $stage_users        = $this->CI->pria_mailer_model->get_pria_task_stage_users($orig_task_id);
                    $recipient          = array_merge($recipient, (($stage_users AND COUNT($stage_users) > 0)? array_column($stage_users, 'user_id'): array()));
                }
            }

            $recipient  = array_unique($recipient);

            $for        = "";

            if(!EMPTY($task_orig_details['core_workflow_stage_id']) AND in_array($task_orig_details['core_workflow_stage_id'], array(CORE_WORKFLOW_STAGE_MEDVAC, CORE_WORKFLOW_STAGE_DOC_DR, CORE_WORKFLOW_STAGE_DR_BFFI, CORE_WORKFLOW_STAGE_DR, CORE_WORKFLOW_STAGE_MEDVAC_APPEND, CORE_WORKFLOW_STAGE_DOC_DR_APPEND)))
            {
                $for    = "<font color='#000000'>for</font> ";
            }

            foreach ($recipient as $rkey => $rvalue)
            {
                $notification   = "<font color='#e23b3b'>".$task_orig_details['doc_name']."</font> ".$for.$ref_n." <font color='#000000'>has been ".$status_label."</font><font color='#e23b3b'>".$remarks."</font>";

                $notify_who     = array(
                    'notification_icon'         => 'speaker_notes',
                    'notification_mobile'       => NULL,
                    'notify_users'              => array($rvalue),
                    'notify_orgs'               => array(), //no default data
                    'notify_roles'              => array(),
                    'module_code'               => $module_code,
                    'displayed_socket_flag'     => NO_FLAG,
                    'listed_flag'               => YES_FLAG,
                    'reminder_flag'             => NO_FLAG
                );

                $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;

                $this->CI->notify->insert_notification($notification, $notify_who, $user_id);
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


	public function import_system_notification($roles = array(), $module_code = NULL, $notification = NULL, $link = NULL, $user_id=NULL, $orgs = array(), $vendor_code = NULL)
	{
		try
        {
            $this->_send_system_notification($roles, $module_code, $notification, $link, $user_id, $orgs, $vendor_code);
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

	public function import_reminder($roles = array(), $module_code = NULL, $notification = NULL, $message = NULL, $orgs = array(), $user_id = NULL, $ref_number= NULL, $base_url_cron = NULL)
	{
		try{

            //$recipients = $this->CI->pria_mailer_model->get_users_per($roles, $org_codes);
            //pending filter of BC
            
            $recipients = $this->CI->pria_mailer_model->get_users_per($roles, $orgs);
            //$recipients = $this->CI->pria_mailer_model->get_users_per($roles['role_code'], $orgs);
            
			$prev_users = array();

            if($recipients){
                
                foreach ($recipients as $recipient):
                    
                    if(!in_array($recipient['user_id'], $prev_users)){
                        $notify_who = array(
                            'notification_icon'         => 'alarm',
                            'notification_mobile'       => NULL,
                            'notify_users'              => array($recipient['user_id']),
                            'notify_orgs'               => array(), //no default data
                            'notify_roles'              => array(),
                            'module_code'               => $module_code,
                            'displayed_socket_flag'     => NO_FLAG,
                            'listed_flag'               => YES_FLAG,
                            'reminder_flag'             => YES_FLAG
                        );

                        $message = get_link_url($module_code,$ref_number,NULL,$base_url_cron);
                        // $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;
                        
                        $notify_who['notification_html'] = $message;

                        $this->CI->notify->insert_notification($notification, $notify_who, $user_id);
                    }
                    $prev_users[] = $recipient['user_id'];
                endforeach;
            }
                

			// foreach ($roles as $role):
   //              //get user with this role $roles
   //              $where = array('role_code' => $role);
   //              $users = $this->CI->tm_model->get_users_role($where);
                
   //              if($users){
                	
   //                  foreach ($users as $user):
                        
   //                      if(!in_array($user['user_id'], $prev_users)){
   //                          $notify_who = array(
   //                              'notification_icon'         => 'alarm',
   //                              'notification_mobile'       => NULL,
   //                              'notify_users'              => array($user['user_id']),
   //                              'notify_orgs'               => array(), //no default data
   //                              'notify_roles'              => array(),
   //                              'module_code'               => $module_code,
   //                              'displayed_socket_flag'     => NO_FLAG,
   //                              'listed_flag'               => YES_FLAG,
   //                              'reminder_flag'             => YES_FLAG
   //                          );

   //                          // $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;
   //                          $notify_who['notification_html'] = $message;

   //                          $this->CI->notify->insert_notification($notification, $notify_who, $user_id);
   //                      }
   //                      $prev_users[] = $user['user_id'];
   //                  endforeach;
   //              }
                
   //          endforeach;
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

    private function _send_system_notification($roles = array(), $module_code = NULL, $notification = NULL, $link = NULL, $user_id=NULL, $orgs = array(), $vendor_code = NULL)
    {
        try
        {
            $recipients = array();

            if(in_array(TASK_ROLE_VENDOR, $roles))
            {
                $roles              = array_diff($roles, array(TASK_ROLE_VENDOR));

                if(!EMPTY($vendor_code))
                {
                    $vendors        = $this->CI->pria_mailer_model->get_vendor_users_by_vendor_code($vendor_code);
                    $recipients     = array_merge($recipients, ($vendors AND COUNT($vendors) > 0)? array_column($vendors, 'user_id'): array());
                }
            }

            if(COUNT($roles) > 0)
            {
                $role_users = $this->CI->pria_mailer_model->get_users_per($roles, $orgs);
                $recipients = array_merge($recipients, (($role_users AND COUNT($role_users) > 0)? array_column($role_users, 'user_id'): array()));
            }

            $recipients = array_unique($recipients);

            //If session user id is empty get user id passed to method (for mobile)
            $user_id    = !empty($user_id) ? $user_id : $this->CI->session->user_id;

            foreach ($recipients as $recipient)
            {
                $notify_who = array(
                    'notification_icon'         => 'speaker_notes',
                    'notification_mobile'       => NULL,
                    'notify_users'              => array($recipient),
                    'notify_orgs'               => array(), //no default data
                    'notify_roles'              => array(),
                    'module_code'               => $module_code,
                    'displayed_socket_flag'     => NO_FLAG,
                    'listed_flag'               => YES_FLAG,
                    'reminder_flag'             => NO_FLAG
                );

                $notify_who['notification_html'] = (!EMPTY($link))? (base_url().$link): "";

                $this->CI->notify->insert_notification($notification, $notify_who, $user_id);
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