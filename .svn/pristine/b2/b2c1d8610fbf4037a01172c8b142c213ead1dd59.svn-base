<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_accept_soa extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA;
        $this->folder           = FOLDER_SOA;
        
        $this->load->model($this->folder.'/Toll_partners_model', 'toll_partners_model'); 
        $this->permissions      = check_permission($this->module_code);
        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params    = get_params(TRUE, TRUE);
            $task_id   = base64_url_decode($params['t'] );

            $common    = $this->_get_common_task_resource($task_id);
            $resources = $common['resources'];
            $data      = $common['data'];
            $access    = $common['access'];
            $task      = $data['task'];

            //Set up resources to be used
            $resources['load_js'][]     = HMVC_FOLDER.'/'.SYSTEM_PORTAL.'/'.PORTAL_TRANSACTIONS.'/'.strtolower(__CLASS__);
            $resources['load_js'][]     = $this->module_task_js;
            $resources['load_js'][]     = JS_DATETIMEPICKER;

            $resources['load_css'][]    = CSS_DATETIMEPICKER;

            $resources['loaded_init']   = ['Task.initPage("'.$task['controller'].'")'];

            //Get SOA details               
            $soa_details = $this->toll_partners_model->get_specific_soa($task['reference_id']);

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.   
            $sub_nav_left_config     = [
                'title'         => 'SOA',
                'placeholder'   => 'Search SOA No.',
                'data'          => []
            ];

            $data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config); 

            $data['enc_task_id']  = encrypt_id($task_id);

            $soa_data = ['view' => $access['view'], 'class_label' => $access['class_label']];
            $soa_data['soa_details'] = $soa_details[0];

            //Load the content of the task
            $data['page_title']         = $soa_details[0]['soa_num'];
            $data['task']['content']    = $this->load->view($this->path_task_views.'/return_accept_soa', $soa_data, TRUE);

            $this->template->load($this->tpl_task_container, $data, $resources, Portal_Controller::$system);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_page( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);	
            
			$this->error_page( $msg );
		}
	}
}