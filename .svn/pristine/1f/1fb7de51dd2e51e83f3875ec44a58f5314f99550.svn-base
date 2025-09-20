<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_email extends Portal_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		try
		{
			$flag 			= 0;
			$email_data 	= array();
			$template_data 	= array();
			
			$system_title 	= get_setting(GENERAL, "system_title");
			
			// required parameters for the email template library
			$email_data["from_email"] 	= get_sys_param_val("SMTP", "CONF_USERNAME")['sys_param_value'];
			$email_data["from_name"] 	= $system_title;
			$email_data["to_email"] 	= array("kpoyaoan.asiagate@gmail.com");
			$email_data["subject"] 		= 'Test Email';
			
			
			// additional set of data that will be used by a specific template
			$template_data["message"] 		= $email_data["subject"] . " kpoyaoan.asiagate@gmail.com " . date('Y-m-d H:i:s');
					
			$status = $this->email_template->send_email_template($email_data, "emails/email_message", $template_data);
			print_r($status);
		}
		catch(Exception $e)
		{
			echo "Error";
		}
	}

	public function test_native()
	{
		try
		{
			$this->load->library('email');

			$config['protocol']		= 'smtp';
			$config['smtp_host']    = 'smtp.office365.com';
			$config['smtp_port']    = '587';
			$config['smtp_user']    = 'kpoyaoan.asiagate@outlook.com';
			$config['smtp_pass']    = '<password>';
			$config['smtp_crypto']  = 'tls';
			$config['charset']		= 'utf-8';
			$config['newline']		= "\r\n";
			$config['mailtype']		= 'text'; // or html
			$config['validation']	= TRUE; // bool whether to validate email or not      

			$this->email->initialize($config);

			$this->email->from('kpoyaoan.asiagate@outlook.com', 'Email Notifications');
			$this->email->to('kpoyaoan.asiagate@gmail.com'); 
			$this->email->subject('Email Notification '.date('Y-m-d'));
			$this->email->message('This email should be received by the recipient.');  
			$this->email->send();

			echo $this->email->print_debugger();
		}
		catch(Exception $e)
		{
			echo "Error";
		}
	}

	public function mail_tls()
	{
		try
		{
			$flag 			= 0;
			$email_data 	= array();
			$template_data 	= array();
			
			$system_title 	= get_setting(GENERAL, "system_title");
			
			// required parameters for the email template library
			$email_data["from_email"] 	= get_sys_param_val("SMTP", "CONF_USERNAME")['sys_param_value'];
			$email_data["from_name"] 	= $system_title;
			$email_data["to_email"] 	= array("<email receiver>");
			$email_data["subject"] 		= 'Test Email';
			$email_data["smtp_crypto"] 	= 'tls';
			
			
			// additional set of data that will be used by a specific template
			$template_data["email"] 		= "<email sender>";
			$template_data["email_subject"]	= $email_data["subject"];
		
			$template_data["name"] 			= "Raymund";
			
			$template_data["system_name"] 	= $system_title;
			$template_data["id"] 			= 1;
					
			$status = $this->email_template->send_email_template($email_data, "emails/successfull_logged_in", $template_data);
			print_r($status);
		}
		catch(Exception $e)
		{
			echo "Error";
		}
	}
}