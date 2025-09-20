<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Indicate_contractor extends Task_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
        $this->controller 		= strtolower(__CLASS__);
        $this->module_code      = MODULE_PORTAL_TRANS_CONTRACTORS;
        $this->tab_module_code  = MODULE_PORTAL_TRANS_CONTRACTORS_BOQ;
        $this->folder           = FOLDER_BOQ;
        
        $this->load->model($this->folder.'/boq_model', 'bq_model'); 
        
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

            $this->task_resources['load_css'][]     = CSS_SELECTIZE;
            $this->task_resources['load_js'][]      = JS_SELECTIZE;
            $this->task_resources['load_js'][]      = JS_ADD_ROW;
            $this->task_resources['load_js'][]      = $this->module_js_task_path.strtolower(__CLASS__);
            $this->task_resources['load_js'][]      = $this->module_js_task_path.strtolower(__CLASS__);
            $this->task_resources['loaded_init'][]  = 'IndicateContractor.init();';

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details'] = $boq_details;
            
            $this->task_view_data['contractors'] = $this->bq_model->get_contractors_by_org_code($boq_details['org_code']);

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
        /*     $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */

            $this->task_view_data['categories']         = $this->bq_model->get_param_contractor_process_categories_by_process_type(CONTRACTOR_PROCESS_BOQ);
            
            $this->task_view_data['boq_asset_codes']    = $this->bq_model->get_boq_asset_codes_grouped_by_contractor($boq_id);

            $this->task_view_data['contractor_view']    = $this->load->view($this->path_task_views.'/boq_indicate_categories',  $this->task_view_data, TRUE);


            //print_var_export( $this->task_view_data['boq_asset_codes']); die;
            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_view_data['started']  = (!EMPTY($this->task_details['user_id']) AND (EMPTY($this->task_details['task_status_id']) OR $this->task_details['task_status_id'] == TASK_STATUS_ONGOING))? TRUE: FALSE;
            $this->task_page                  = '/indicate_contractor';
            
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
            $status       = '';
            $msg          = '';
            $task         = '';

            $params       = $this->_validate();
            $task_id      = $params['task_id'];
            $task_details = $this->tm_model->get_task_details($task_id);

            //Start the db transaction                
            Portal_Model::beginTransaction();

            $boq_id       = $task_details['reference_id'];
            $boqs         = $params['boqs'];
            $vendors      = $params['vendors'];

            $table        = Portal_Model::PORTAL_TABLE_PRIA_BOQ_ASSET;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $where        = ['boq_id' => $boq_id];    

            $audit_action = [AUDIT_INSERT];	
            //$prev_detail  = [$this->bq_model->get_details_for_audit( $table, $where)];
            $prev_detail  = [];
            $actvy_index  = EMPTY($task_details['task_reference_id']) ? 'audit_trail_add' : 'audit_trail_update';
            $activity     = sprintf($this->lang->line($actvy_index), $task_details['task_name']);


            $this->bq_model->delete_boq_asset(['boq_id' => $boq_id]);
            
            $insert = [];
            $seq    = 1;
            foreach($vendors as $key => $val)
            {
                $ks          = explode('_', $key);
                $index       = $ks[1];
                $vendor_code = $val[0];

                foreach($boqs['boq_category_'.$index] as $key => $val)
                {
                    $insert = [
                        'boq_id'                => $boq_id, 
                        'asset_type'            => $val,
                        'confirmed_contractor'  => $vendor_code,
                        'seq_no'                => $seq
                    ];
                   // print_var_export($insert);
                    $this->bq_model->insert_boq_asset($insert);
                }

                $seq++;
            }

          //  $this->bq_model->update_boq(['awarded_contractor' => $params['vendor']], $where);

            $curr_detail  = [ $this->bq_model->get_details_for_audit($table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
      /*       if(EMPTY($task_details['task_reference_id']))
                $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $boq_id]); */

            $this->tag_task($task_id, $params['task_status'], ['reference' => $boq_id]);


            Portal_Model::commit();
            
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
            'status' => $status
        ]);
    }
    
    private function _validate()
    {
        $params     = get_params();
        //print_var_export($params);
        //Filter vendors and boqs
        $vendors    = array_filter($params, function($v, $k){
           return (preg_match('/^vendor_/',$k)) ? TRUE : FALSE;
        }, ARRAY_FILTER_USE_BOTH); 

        $boqs       = array_filter($params, function($v, $k){
            return (preg_match('/^boq_category_/',$k)) ? TRUE : FALSE;
        }, ARRAY_FILTER_USE_BOTH);

        //Get all values for both vendors and boqs
        $check_boq = [];
        foreach(array_values($boqs) as $val)
            $check_boq = array_merge($check_boq, $val);

        $check_ven = [];
        foreach(array_values($vendors) as $val)
            $check_ven = array_merge($check_ven, $val);    

        //Check duplicate values for both vendors and categories
        $dup_boq = [];
        foreach(array_count_values($check_boq) as $key => $val)
            if($val > 1) 
                $dup_boq[] = $key;

        $dup_ven = [];
        foreach(array_count_values($check_ven) as $key => $val)
            if($val > 1)
              $dup_ven[] = $key;
        
        //Retrieves the actual names for category and vendor      
        if( ! EMPTY($dup_boq))   
        {
            $boq_details = $this->bq_model->get_param_boq_categories(['category_code' => ['IN', $dup_boq]], ['GROUP_CONCAT(category_name) as category_names']);

            throw new Exception('Duplicate values for the following category :'.$boq_details[0]['category_names']);
        }

        if( ! EMPTY($dup_ven))   
        {
            $ven_details = $this->bq_model->get_vendors(['vendor_code' => ['IN', $dup_ven]], ['GROUP_CONCAT(vendor_name) as vendor_names']);

            throw new Exception('Duplicate values for the following vendor :'.$ven_details[0]['vendor_names']);
        }
        /*print_var_export($params, $boqs, $vendors);
 die;*/
        $params['task_id'] = decrypt_id($params['etd']);
        //Define the required fields.
        $required = [];

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
          $validated = $this->validate_inputs($params, $constraints);

          return [
            'task_id'       => $validated['task_id'],
            'task_status'   => $validated['task_status'],
            'boqs'          => $boqs,
            'vendors'       => $vendors,
          ];
    }
}