<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_template {

	protected $email_error = array();
	protected $CI;
	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('email');
	}
	
	/**
	 * $email_data - array of required data such as: 
	 * 		from_email - email of the sender
	 * 		from_name - name of the sender
	 * 		to_email - email of the recipient (it can be an array of emails)
	 * 		subject - topic/subject of the email
	 * 
	 * $template - the view page where the content of the email is located. The file should be created in (application/views/emails/) folder
	 *  
	 * $template_data - optional array of data that can be use to a particular template
	 * 
	 * $template_data_indexes - 
	 *	if the recipient is a set of multiple emails, 
	 *	specify here the indexes of data that should be reset for every template of a specific email
	 * 		ex : $email_data["to_email"] = array('kmanalo@asiagate.com', 'mvibal@asiagate.com', 'jab@asiagate.com', 'rsatuitob@asiagate.com');
	 *			 $template_data_indexes["name"] = array(
	 *				'kmanalo@asiagate.com' => 'kenneth', 
	 *				'mvibal@asiagate.com' => 'meg', 
	 *				'jab@asiagate.com' => 'jaja', 
	 *				'rsatuitob@asiagate.com' => 'rodel');
	 *			 $template_data_indexes["contact_info"] = array(
	 *				'kmanalo@asiagate.com' => '123', 
	 *				'mvibal@asiagate.com' => '246', 
	 *				'jab@asiagate.com' => '789', 
	 *				'rsatuitob@asiagate.com' => '012');
	 *			 $template_data_indexes["company"] = "asiagate";
	 *			 
	 *			 -- no need to include company since its a single data
	 * 			 $template_data_indexes = array("email", "name", "contact_info");    
	 */
	
	public function send_email_template($email_data, $template, $template_data = array(), $template_data_indexes = array())
	{	
		try 
		{	
            // return 1;
			@set_time_limit(-1);
			error_reporting(E_ERROR);
			$config = array();
			$params = array();
			
			$params["fields"] = array("sys_param_name", "sys_param_value");
			$params["where"] = array("sys_param_type" => SYS_PARAM_SMTP);
			$params["multiple"] = TRUE;
			$email_params = get_values("sys_param_model", "get_sys_param", $params, CORE_COMMON);
			
			if(!EMPTY($email_params))
			{
				foreach($email_params as $item):
					$config[strtolower($item['sys_param_name'])] = $item['sys_param_value'];
				endforeach;
			} else {
				throw new Exception($this->CI->lang->line('email_not_initialized'));
			}
			
			$config['smtp_timeout'] = '7';
			$config['validate'] = TRUE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$this->CI->email->initialize($config);
			$this->CI->email->set_newline("\r\n");

			foreach ($email_data as $key => $info):
				if(EMPTY($info) AND $key != 'cc_email') 
					throw new Exception(sprintf($this->CI->lang->line('email_field_empty'), $key));
			endforeach;
			
			// This is for individual emails that require a specific set of information for every message sent 
			if(ISSET($email_data["to_email"]) AND !EMPTY($email_data["to_email"])){
				foreach ($email_data["to_email"] as $to_email):
					$data = array();
				
					if(!EMPTY($template_data_indexes) AND count($email_data["to_email"]) > 0){
						
						foreach ($template_data_indexes as $key => $indexes):
							$data[$key] = $template_data_indexes[$key][$to_email];
						endforeach;
						
						$template_data = array_merge($template_data, $data);
					} 
					
					$msg = $this->CI->load->view($template, $template_data, true);
					
					if(EMPTY($msg)) 
						throw new Exception(sprintf($this->CI->lang->line('email_field_empty'), "Message"));
					
					$this->CI->email->clear();
					
					$this->CI->email->from($email_data["from_email"], $email_data["from_name"]);
					$this->CI->email->to($to_email);

					if(ISSET($email_data["cc_email"]) AND ((is_array($email_data["cc_email"]) AND COUNT($email_data["cc_email"]) > 0) OR !EMPTY($email_data["cc_email"])))
					{
						$this->CI->email->cc($email_data["cc_email"]);
					}
						
					$this->CI->email->subject($email_data["subject"]);
					$this->CI->email->message($msg);
			
					if($this->CI->email->send()){
						$flag = 1;
					} else {

						$flag = $this->CI->email->print_debugger();
						$this->email_error[] 	= $this->CI->email->print_debugger();
						return $flag;
					}

				endforeach;
				
				return $flag;

			}

			// This is for bulk emails containing the same set of message
			if(ISSET($email_data["bulk_email"]) AND !EMPTY($email_data["bulk_email"])){
					$msg = $this->CI->load->view($template, $template_data, true);
					
					if(EMPTY($msg)) 
						throw new Exception(sprintf($this->CI->lang->line('email_field_empty'), "Message"));
					
					$this->CI->email->clear();
					
					$this->CI->email->from($email_data["from_email"], $email_data["from_name"]);
					$this->CI->email->to($email_data["bulk_email"]);

					if(ISSET($email_data["cc_email"]) AND ((is_array($email_data["cc_email"]) AND COUNT($email_data["cc_email"]) > 0) OR !EMPTY($email_data["cc_email"])))
					{
						$this->CI->email->cc($email_data["cc_email"]);
					}
						
					$this->CI->email->subject($email_data["subject"]);
					$this->CI->email->message($msg);
			
					if($this->CI->email->send()){
						$flag = 1;
					} else {
						$flag = $this->CI->email->print_debugger();
						$this->email_error[] 	= $this->CI->email->print_debugger();
					}
					
					return $flag;
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

	
	public function get_email_errors()
	{

		return $this->email_error;

	}
	
}