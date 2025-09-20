<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Portal_Controller extends Base_Controller
{
	private $module;
	private $module_qa_js;

	protected static $system 	= SYSTEM_PORTAL;
	protected $system_js_path 	= HMVC_FOLDER.DS.SYSTEM_PORTAL.DS;

	protected $enum_yes 		= [ENUM_YES];
	protected $enum_yes_num     = [INITIAL_YES, INITIAL_NO];
	
	public function __construct()
	{
		parent::__construct();

		$this->load->library('Pria_mailer');

		$this->module_qa_js		= $this->system_js_path.PORTAL_QUICK_ADD.DS.'quick_add';
		$this->module_filter_js	= $this->system_js_path.PORTAL_COMMON.DS.'filter';
		$this->module_task_js	= $this->system_js_path.PORTAL_TRANSACTIONS.DS.'task';

		$this->last_ag_tasks	= array(
				AG_CONTRACT_GROWERS	=> array(CORE_TASK_FHR_FINAL_APPROVAL),
				AG_GOODS_BAVI		=> array(CORE_TASK_SOA_APPROVE),
				AG_GOODS_BFFI		=> array(CORE_TASK_PO_UPLOAD_WO_APPROVAL),
				AG_GOODS_MARINADES	=> array(CORE_TASK_SOA_APPROVE),
				AG_TOLL_PARTNERS	=> array(CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED),
				AG_CONTRACTORS		=> array(CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL),
				AG_FORWARDERS		=> array(CORE_TASK_SOA_ACCEPT_CALAMBA),
				AG_INBOUND_CENTRAL	=> array(CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED),
				AG_INBOUND_NORMAL	=> array(CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL),
				AG_OUTBOUND			=> array(CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL),
				AG_FEEDMILL			=> array(CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL),
				AG_LESSORS			=> array(CORE_TASK_RENEWED_CONTRACT),
				AG_MANPOWER			=> array(CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER),
				AG_SOA_BASED		=> array(CORE_TASK_SOA_ACCEPT_SOA_BASED)
		);
	}

	protected function get_common_resources($module)
	{
		$css  	= array();
		$js   	= array($this->module_qa_js, $this->module_filter_js, $this->module_task_js);
		$init 	= array(
				'Quick_add.import_qa("'.$module.'");',
				'Quick_add.process_import();',
				'Quick_add.selectAll();',
				'Quick_add.close_import_modal();',
				'Quick_add.attach_file();',
				'Quick_add.file_vesioning_func();'
		);

		$modal 	= array(
				'modal_quick_add' 		=> array(
						'size' 			=> 'sm-w md-h',
						'title' 		=> 'Import file',
						'module' 		=> PORTAL_COMMON,
						'method' 		=> 'modal_quick_add',
						'controller' 	=> PORTAL_QUICK_ADD,
						'custom_button'	=> array(
								'Import'	=> array(
										'type' 		=> 'button',
										'action' 	=> 'Import'
								)
						),
						'dismissible'	=> TRUE
				),
				'modal_generated_file' 	=> array(
						'size' 			=> 'full',
						'title' 		=> 'Import file',
						'module' 		=> PORTAL_COMMON,
						'method' 		=> 'modal_generated_file',
						'controller' 	=> PORTAL_QUICK_ADD,
						'custom_button'	=> array(
							'Upload Again'		=> array(
									'type' 		=> 'button',
									'action' 	=> 'Upload',
									'class' 	=> 'green lighten-1'
							),
							'Import Records'	=> array(
									'type' 		=> 'button',
									'action' 	=> 'Import_record'
							)
						),
						'has_scroll'	=> TRUE,
						'dismissible'	=> TRUE,
						'post'			=> TRUE
				),
				'modal_file_version' 	=> array(
						'size' 			=> 'lg-w md-h',
						'title' 		=> 'File Versions',
						'module' 		=> PORTAL_COMMON,
						'method' 		=> 'modal_file_version',
						'controller' 	=> PORTAL_FILE_VERSION,
						'custom_button'	=> array()
				),
				'modal_upload_file' 	=> array(
						'size' 			=> 'sm',
						'title' 		=> 'File Versions',
						'module' 		=> PORTAL_COMMON,
						'method' 		=> 'modal_upload_file',
						'controller' 	=> PORTAL_FILE_VERSION,
						'custom_button'	=> array(
								'Upload' 			=> array(
										'type' 		=> 'button',
										'action' 	=> 'Upload',
										'class' 	=> 'green lighten-1'
								)
						)
				),
				'modal_task_upload' 	=> array(
						'size' 			=> 'sm-w md-h',
						'title' 		=> 'Task Attachment',
						'module' 		=> PORTAL_COMMON,
						'method' 		=> 'modal_task_upload',
						'controller' 	=> PORTAL_TASK_ATTACHMENT,
						'custom_button'	=> array(
								'Attach' 			=> array(
										'type' 		=> 'button',
										'action' 	=> 'Attach',
										'class' 	=> 'green lighten-1'
								)
						)
				)
		);

 	   	$upload = array(
				'attachments' => array(
						'path'                  => PATH_UPLOADED_FILES,
						'allowed_types'         => '*',
						'multiple'              => FALSE,
						'max_file'              => 999,
						'drag_drop' 			=> TRUE,
						'show_preview'          => TRUE,
						'show_download'			=> TRUE,
						'show_progress'			=> TRUE,
						'successCallback'       => "Task.successCallback();",
				)
        );


		$resources		= array( 
				'css'	=> $css, 
				'js'	=> $js
		);

		if( ISSET($modal) )
			$resources['modal'] = $modal;		

		if( ISSET($init))
			$resources['init']	= $init;

		if( ISSET($upload))
			$resources['upload']	= $upload;

		return $resources;
	}

	
	public function construct_main_container($content = array(), $width = '200')
	{
		$data 			= array();
		$resources		= array();

		try
		{
			
			$data['width']			= $width;
			$data['id']				= $content['id'];
			$data['content']		= $content['data'];
			
			$resources				= $content['resources'];
			$view_page 				= PORTAL_COMMON.'/nav/nav_container';
			
			$html = $this->load->view($view_page, $data, TRUE);
			$html .= $this->load_resources->get_resource($resources, TRUE);
			return $html;
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	

			$this->error_index( $msg );
		}
	}

	protected function validate_security(&$params)
	{
		try 
		{
			/*
			 * TODO
			 * START: Check security variables
			 */
			$security					= explode('/', $params['security']);
			$params['hash_id']			= $security[0];
			$params['salt']				= $security[1];
			$params['token']			= $security[2];
			$params['security_action']	= $security[3];
			
			check_salt($params['hash_id']. '/' . $params['security_action'], $params['salt']	, $params['token']);
			/*
			 * END: Check security variables
			 */
		}
		catch(Exception $e)
		{
			throw $e;
		}
	} 

    public function download_log($message='')
    {
        try
        {
        	if(EMPTY($message))
        	{
        		$params		= get_params();
        		$message	= $params['message'];
        	}

        	header('Content-Disposition: attachment; filename="Log File.txt"');
			header('Content-Type: text/plain');
			echo $message;
/*
        	header ("Content-Type: application/octet-stream");
			header ("Content-disposition: attachment; filename=Log File.txt");*/
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }       
    }

    public function get_scoped_org_by_ag($module_code, $vendor = NULL, $ag_code = NULL, $org_type = NULL)
    {
    	try
    	{
    		$orgs = get_scope_details($module_code);
    		$orgs_having = (ISSET($orgs['having']))? $orgs['having']: "";

    		return $this->tm_model->get_all_business_centers($vendor, $ag_code, $org_type, $orgs_having);
    	}
        catch(Exception $e)
        {
            throw $e;
        }  
    }
}
