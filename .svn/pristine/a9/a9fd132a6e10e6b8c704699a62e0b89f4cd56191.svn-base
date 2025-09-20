<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_site_nomination extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACTORS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CONTRACTORS_SN;
        $this->folder           = FOLDER_SITE_NOMINATIONS;
        
        $this->load->model($this->folder.'/site_nominations_model', 'sn_model'); 
        
        $this->permissions      = check_permission($this->tab_module_code);

        $this->path_task_views .= $this->folder;
	}
    
	public function index()
	{
		try
		{  
            if( ! $this->permissions[ACTION_VIEW]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params         = get_params(TRUE, TRUE);

            $task_id        = base64_url_decode($params['t']);

            $this->_initialize_task($task_id);

            $this->task_resources['load_css'][]      = CSS_DATETIMEPICKER;
            $this->task_resources['load_js'][]       = JS_DATETIMEPICKER;

            $site_id        = $this->task_details['reference_id'];

            $fields         = [
                'a.site_id',
				'a.site_num',
				'a.suggested_store_name',
				'b.name',
            ];

            $site_details   = $this->sn_model->get_site_details($site_id, $fields);
            
            $this->task_view_data['site_details'] = $site_details;

            $where          = ['document_type_code' => DOC_TYPE_SITE_FORM, 'reference' => $site_id];
            $site_form      = $this->dm_model->get_document($where);

            //If There's an uploaded file hide save because edit will be in versioning
            /* if( ! EMPTY($site_form))
                $this->task_access['hide_btn'] = TRUE; */

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
          

            //Load the content of the task
            $this->data['page_title']         = 'Site Nomination Requirements: '.$site_details['site_num'];    
            $this->task_page                  = '/upload_site_nomination';
            
            $this->_load_task_view();
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
    
    public function process()
    {
        try
        {
            $params       = get_params();
                        
            $flag         = ERROR;
            $status       = '';
            $msg          = '';
            $task         = '';
            $doc_ref      = '';    
            $required     = [];
            $constraints  = [];
            $task_id      = decrypt_id($params['etd']);

            $task_details  = $this->tm_model->get_task_details($task_id);

            $task_document = $this->dm_model->get_document(['pria_task_id' => $task_id, 'document_type_code' => DOC_TYPE_SITE_FORM]);
            
            if(EMPTY($task_document))
            {
                $required = ['doc_site_form' => 'Site Form'];

                $constraints['doc_site_form'] = [
                    'data_type'			=> 'string',
                    'name'				=> 'Site Form'
                ];
            }

            $constraints['task_status'] = [
                'data_type'   => 'db_value',
                'name'        => 'Task Status',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'action_id',
                'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $this->validate_inputs($params, $constraints);

            $site_id      = $task_details['reference_id'];

            //Update the reference of the task and status ( w/other details )
           // $ongoing      = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $site_id]);

            $this->tag_task($task_id, $params['task_status'], ['reference' => $site_id]);

            $doc_ref      = encrypt_id($site_id);
            $flag         = SUCCESS;
            $msg          = $this->lang->line('data_saved');
        }
        catch(PDOException $e)
        {
            $msg 	= $this->get_user_message($e);

            Portal_Model::rollback();
        }
        catch(Exception $e)
        {
            $msg  	= $this->rlog_error($e, TRUE);	

            Portal_Model::rollback();
        }

        echo json_encode([
            'flag'   => $flag,
            'msg'    => $msg,
            'task'   => $task,
            'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }
    
 
}