<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encode_asset extends Task_Controller 
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
            $this->task_resources['loaded_init'][]  = 'EncodeAsset.init();';

            $boq_id         = $this->task_details['reference_id'];
          
            $boq_details    = $this->bq_model->get_boq_details($boq_id);
            
            $this->task_view_data['boq_details']         = $boq_details;

            $this->task_view_data['categories']          = $this->bq_model->get_param_contractor_process_categories_by_process_type(CONTRACTOR_PROCESS_BOQ);
			
            $this->task_view_data['boq_asset_codes']     = $this->bq_model->get_boq_asset_codes_joined_to_params($boq_id);
            //$this->task_view_data['boq_asset_codes']     = $this->bq_model->get_boq_asset_codes(['boq_id' => $boq_id], ['asset_code', 'internal_order', 'asset_type'], ['seq_no' => 'ASC']);
            //print_var_export($this->task_view_data['boq_asset_codes']); die;
            
            $this->task_view_data['boq_asset_codes_view'] = $this->load->view($this->path_task_views.'/boq_asset_codes',  $this->task_view_data, TRUE);

            $this->task_details['task_reference_id']    = $boq_id;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
        /*     $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */

            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_page                  = '/encode_asset';
            
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
            //print_var_export($params); die;
            $task_id      = $params['task_id'];
            $task_details = $this->tm_model->get_task_details($task_id);

            //Start the db transaction                
            Portal_Model::beginTransaction();

            $boq_id       = $task_details['reference_id'];

            $table        = Portal_Model::PORTAL_TABLE_PRIA_BOQ_ASSET;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $where        = ['boq_id' => $boq_id];    

            $audit_action = [AUDIT_INSERT];	
            $prev_detail  = [$this->bq_model->get_details_for_audit( $table, $where)];
            $actvy_index  = EMPTY($task_details['task_reference_id']) ? 'audit_trail_add' : 'audit_trail_update';
            $activity     = sprintf($this->lang->line($actvy_index), $task_details['task_name']);

         //   $this->bq_model->delete_boq_asset($where);

            $seq = 1;
            foreach($params['boq_category'] as $key => $val)
            {
                $fields = [
                    'boq_id'         => $boq_id,
                    //'asset_type'     => $val,
                    'asset_code'     => $params['asset_code'][$key],
                    'internal_order' => $params['internal_order'][$key],
                  //  'seq_no'         => $seq
                ];
                
                $where  = ['boq_id' => $boq_id, 'asset_type' => $val];
                //$this->bq_model->insert_boq_asset($fields);
                $this->bq_model->update_boq_asset($fields, $where);
                //$seq++;
            }

            $curr_detail  = [ $this->bq_model->get_details_for_audit($table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            //Update the reference of the task and status ( w/other details )
          /*   if(EMPTY($task_details['task_reference_id']))
                $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $boq_id]);

 */
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
        $params = get_params();
        
        $params['task_id'] = decrypt_id($params['etd']);
   
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

         /* Validate constraints */
        $this->validate_inputs($params, $constraints);

        $fields = ['task_id' => $params['task_id'], 'task_status' => $params['task_status']];
        
        foreach($params['category'] as $key => $val)
        {
            $p = [
                'boq_category'      => base64_url_decode($val),
                'asset_code'        => $params['asset_code'][$key],
                'internal_order'    => $params['internal_order'][$key],
            ];

            $p = $this->set_filter( $p )
                ->filter_string('internal_order')
                ->filter_string('boq_category')
                ->filter_string('asset_code')
                ->filter();        

            $constraints['internal_order']	= [
                'data_type'			=> 'string',
                'name'				=> 'Internal Order'
            ];

            $constraints['asset_code']  	= [
                'data_type'			=> 'digit',
                'name'				=> 'Asset Code'
            ];

            $constraints['boq_category'] = [
                'data_type'   => 'db_value',
                'name'        => 'Category',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'category_code',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_PARAM_CONTRACTOR_PROCESS_CATEGORIES
            ]; 

            $required = [
                // 'internal_order'    => 'Internal Order',
                'boq_category'      => 'Category',
                'asset_code'        => 'Asset Code',
            ];

            /* Validate the required fields */
            $this->check_required_fields($p, $required);

            /* Validate constraints */
            $data = $this->validate_inputs($p, $constraints);

            $fields['boq_category'][]   = $data['boq_category'];
            $fields['internal_order'][] = $data['internal_order'];
            $fields['asset_code'][]     = $data['asset_code'];
        }

        $duplicates = array_count_values($fields['boq_category']);

        foreach($duplicates as $key => $d)
        {
            if($d > 1)
                throw new Exception('Duplicate '.ucfirst(strtolower($key)).' category');
        } 

        return $fields;
    }
}