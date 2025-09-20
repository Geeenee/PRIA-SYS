<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Official_store_name extends Task_Controller 
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
				'a.suggested_store_name',
				'b.name',
				'a.official_store_name'
            ];

            $site_details   = $this->sn_model->get_site_details($site_id, $fields);
            
            $this->task_view_data['site_details']       = $site_details;
            $this->task_view_data['task_status_id']     = $this->task_details['task_status_id'];

            $this->task_details['task_reference_id']    = $site_id;


          /*   $where          = ['document_type_code' => DOC_TYPE_SITE_FORM, 'reference' => $site_id];
            $site_form      = $this->dm_model->get_document($where);

            $this->task_view_data['site_form'] = ( ! EMPTY($site_form)) ? create_document_tag($site_form, TRUE) : ''; */

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
           

            //Load the content of the task
            $this->data['page_title']         = 'Site Nomination Requirements: '.$site_details['site_num'];    
            $this->task_page                  = '/official_store_name';
            
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
            $flag         = ERROR;
            $msg          = '';

            $params       = $this->_validate();

            $task_id      = $params['task_id'];

            $task_details = $this->tm_model->get_task_details($task_id);

            $site_id      = $task_details['reference_id'];

            $site_dup     = $this->sn_model->get_site(['TRIM(official_store_name)' => trim($params['official_store_name']), 'status_code' => ['!=' => PARAM_STATUS_DISAPPROVED]], ['site_id']);

            if( ! EMPTY($site_dup) && $site_dup['site_id'] != $site_id )
                throw new Exception('Official Store Name already taken!');

             //Start the db transaction                
            Portal_Model::beginTransaction();
            $table        = Portal_Model::PORTAL_TABLE_SITES;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $where        = ['site_id' => $site_id];    

            $audit_action = [AUDIT_UPDATE];	
            $prev_detail  = [$this->sn_model->get_details_for_audit( $table, $where)];
            $activity     = sprintf($this->lang->line('audit_trail_add'), ' Official Store Name');

            $this->sn_model->update_site(['official_store_name' => $params['official_store_name']], $where);

            $curr_detail  = [ $this->sn_model->get_details_for_audit( $table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
            //if(EMPTY($task_details['task_reference_id']))
               // $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $site_id]);

            $this->tag_task($task_id, $params['task_status'], ['reference' => $site_id]);


            Portal_Model::commit();

            $flag        = SUCCESS;
            $msg         = $this->lang->line('data_saved');
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
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            if( ! $this->permissions[ACTION_EDIT]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params = get_params();
            
             //Filters the data inputted by the user 
			$params = $this->set_filter( $params )
            ->filter_string('official_store_name')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $required = [
                'official_store_name' => 'Official Store Name',
            ];

            $constraints['official_store_name']	= [
                'data_type'			=> 'string',
                'name'				=> 'Official Store Name'
            ];

            $constraints['task_id'] = [
                'data_type'   => 'db_value',
                'name'        => 'ETD',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'pria_task_id',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
            ];

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
            return $this->validate_inputs($params, $constraints);
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