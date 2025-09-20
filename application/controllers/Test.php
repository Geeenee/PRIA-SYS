<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function send_email()
    {

    	$recipient = 'jtbelandres@bountyagro.com.ph';
    	$subject = 'Test Email';
    	$message = 'Test Only...';


        if(empty($recipient) || empty($subject) || empty($message)) {
            $email_result = [
                'result'  => FALSE,
                'Message' => 'Parameter Empty'
            ];
            $this->_store_email_log($email_result, $recipient);
            return $email_result;
        }

        $config = [ 
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://server10.synermaxx.net',
            'smtp_port' => 587,
            'smtp_user' => 'pria@bountyagro.com.ph',
            'smtp_pass' => '$uppli3rsp0rt@l',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        ];
        $this->load->library('email', $config);
        $this->email->clear();
        $this->email->set_newline("\r\n")
                    ->from('pria@bountyagro.com.ph', 'ISSC Dev Teammmm...')
                    ->to($recipient)
                    ->subject($subject)
                    ->message($message);

        if($this->email->send()){
            $email_result = [
                'result'  => TRUE,
                'Message' => 'Email Sent'
            ];
            $this->_store_email_log($email_result, $recipient);
            return $email_result;
        }else{
            $email_result = [
                'result'  => FALSE,
                'Message' => $this->email->print_debugger()
            ];
            $this->_store_email_log($email_result, $recipient);
            return $email_result;
        }
    }
}