<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recommend_approval extends Task_Controller 
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

            $site_id        = $this->task_details['reference_id'];

            $fields         = [
                'a.site_id',
				'a.site_num',
				'b.name',
                'a.official_store_name',
                'a.sn_recommendation',
                'a.sn_recommendation_bh',
                'a.sn_recommendation_rh'
            ];

            $site_details   = $this->sn_model->get_site_details($site_id, $fields);
            
            $this->task_view_data['site_details']    = $site_details;

            $this->task_details['task_reference_id'] = $site_id;

            $this->task_view_data['w_edit_recom']       =  $this->task_details['core_workflow_task_id'] == CORE_TASK_SITES_RECOM_SITE_NOMINATION ? true : false;
            $this->task_view_data['w_edit_recom_bh']    =  $this->task_details['core_workflow_task_id'] == CORE_TASK_SITES_RECOM_SITE_REGIONAL ? true : false;
            $this->task_view_data['w_edit_recom_rh']    =  $this->task_details['core_workflow_task_id'] == CORE_TASK_SITES_RECOM_SITE_PRES ? true : false;
//print_var_export($this->task_view_data); die;
            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
            $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
          //  $this->data['sub_nav_left']       = $this->construct_lists($sub_nav_left_config); 

            //Load the content of the task
            $this->data['page_title']         = 'Site Nomination Requirements: '.$site_details['site_num'];    
            $this->task_page                  = '/recommend_approval';

            $this->task_view_data['core_workflow_task_id']        = $this->task_details['core_workflow_task_id'];
            
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
    
    /** 
     * @Author: kevin villarojo 
     * @Date: 2019-08-20 09:53:52 
     * @Desc:  Used for task "Recommend Site Nomination Approval"
     */    
    public function process()
    {
        try
        { 
            $flag       = ERROR;
            $params     = get_params();
            $doc_ref    = '';    

            Portal_Model::beginTransaction();
           
            $task_id        = decrypt_id($params['etd']);
            $task_details   = $this->tm_model->get_task_details($task_id);

            $params = $this->set_filter( $params )
            ->filter_string('recommendation')
            ->filter();

            $required = [];

            if($params['task_status'] == TASK_STATUS_DONE)
            {
                $required['recommendation'] = 'Recommendation';
            }
      
            
            $constraints['recommendation'] = [
                'data_type'			=> 'string',
                'name'				=> 'Recommendation'
            ];

            $constraints['task_status'] = [
                'data_type'   => 'db_value',
                'name'        => 'Task Status',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'action_id',
                'table'       =>  Portal_Model::CORE_PARAM_TASK_ACTIONS
            ];

            $task_document = $this->dm_model->get_document(['document_type_code' => DOC_TYPE_SITE_FORM, 'reference' => $task_details['reference_id']]);
           
            if(EMPTY($task_document))
            {
                $required['doc_site_form'] = 'Site Form';

                $constraints['doc_site_form'] = [
                    'data_type'			=> 'string',
                    'name'				=> 'Site Form'
                ];
            }

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $this->validate_inputs($params, $constraints);

            $task_details = $this->tm_model->get_task_details($task_id);
         
            $site_id      = $task_details['reference_id'];

            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_SITES;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];   
       
            $where        = ['site_id' => $site_id]; 

            $audit_action = [AUDIT_UPDATE];	
            $prev_detail  = [$this->sn_model->get_details_for_audit( $table, $where)];
            $activity     = sprintf($this->lang->line('audit_trail_add'), ' Site Recommendation');
         
            $this->sn_model->update_site(['sn_recommendation' => $params['recommendation']], $where);

            $curr_detail  = [ $this->sn_model->get_details_for_audit( $table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
            //if(EMPTY($task_details['task_reference_id']))
          
            $doc_ref      = encrypt_id($site_id);

            $this->tag_task($task_id, $params['task_status'], ['reference' => $site_id]);

            Portal_Model::commit();
        
            $flag       = SUCCESS;
            $msg        = $this->lang->line('data_saved');
        }
        catch(PDOException $e)
        {
            $msg     = $this->get_user_message($e);
        
            Portal_Model::rollback();
        }
        catch(Exception $e)
        {
            $msg      = $this->rlog_error($e, TRUE);
        
            Portal_Model::rollback();
        }
        
        echo json_encode([
            'flag'   => $flag,
            'msg'    => $msg,
           // 'doc_ref'=> $doc_ref
        ]);
    }
}