<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification_cron extends SYSAD_Controller
{
	protected static $INIT_FLAG		= false; // indicator if this class is already loaded or not
	protected static $EMAIL_FLAG 	= false; // indicator if send_email() is currently sending email
	protected static $SMS_FLAG 		= false; // indicator if send_sms() is currently sending SMS

	public function __construct() 
	{
		// if INIT_FLAG is TRUE, it means this class
		// is already loaded
		if ( !EMPTY( static::$INIT_FLAG ) )
		{
			return;
		}

		static::$INIT_FLAG = true;

		parent::__construct();

		$this->load->library('Notification_queues');
		$this->load->model(CORE_SETTINGS.'/Site_settings_model', 'ssm');
	}

	public function run_every_one_sec()
	{
		try
		{
			$this->load->library('Ci_react');

			$loop   	= React\EventLoop\Factory::create();
			
			$loop->addPeriodicTimer(1, function()
			{
				$this->cron_one();
			});

			$loop->run();
		}
		catch( PDOException $e )
		{
			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
		catch( Exception $e )
		{
			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}	
	}

	public function cron_one()
	{
		try
		{
			$notification_cron 				 	= get_setting(NOTIFICATION_CRON, "notification_cron");
			
			if( !EMPTY( $notification_cron ) )
			{
				return;
			}

			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 1), 'notification_cron');

			$this->send_email(TRUE);
			$this->send_sms(TRUE);

			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');
		}
		catch( PDOException $e )
		{
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');
			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
		catch( Exception $e )
		{
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');
			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}	
	}

	public function cron()
	{

		try
		{

			$notification_cron 				 	= get_setting(NOTIFICATION_CRON, "notification_cron");
			/*if( !EMPTY( $notification_cron ) )
			{	
				return;
			}*/

			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 1), 'notification_cron');
			
			$this->send_email();
			$this->send_sms();

			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');
		}
		catch( PDOException $e )
		{
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');
			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
		catch( Exception $e )
		{
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');
			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}	
	}

	/**
	 * send_email()
	 * 		sends an e-mail message to user
	 * 
	 * 
	 */
	public function send_email( $single = FALSE )
	{
		// we need to make sure that only one email is 
		// being process by the server
		if ( !EMPTY( static::$EMAIL_FLAG ) )
		{
			return;
		}

		try
		{
			static::$EMAIL_FLAG = true;

			if( $single )
			{
				$this->notification_queues->start_email_queue_single();
			}
			else
			{
				$this->notification_queues->start_email_queue_multi();
			}
			static::$EMAIL_FLAG = false;
		}
		catch( PDOException $e )
		{
			static::$EMAIL_FLAG = true;
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');

			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
		catch( Exception $e )
		{
			static::$EMAIL_FLAG = true;
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');

			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
	}

	/**
	 * send_sms() 
	 * 		sends an sms to user
	 * 
	 * NOTE: this function is called every 1 second.
	 */
	public function send_sms( $single = FALSE )
	{
		// we need to make sure that only one sms is
		// being process by the server
		if ( !EMPTY( static::$SMS_FLAG ) )
		{
			return;
		}

		try
		{
		
			static::$SMS_FLAG = true;
			if( $single )
			{
				$this->notification_queues->start_sms_queue_single();
			}
			else
			{
				$this->notification_queues->start_sms_queue_multi();
			}
			
			static::$SMS_FLAG = false;
		}
		catch( PDOException $e )
		{
			static::$SMS_FLAG = true;
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');

			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
		catch( Exception $e )
		{
			static::$SMS_FLAG = true;
			$this->ssm->update_settings(NOTIFICATION_CRON, array('notification_cron' => 0), 'notification_cron');

			$msg 	= $e->getLine() . ': ' . $e->getMessage(). ': '. $e->getTraceAsString();

			RLog::error($msg);
		}
	}
}