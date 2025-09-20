<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_boq_recom extends Task_Controller 
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

            $this->task_resources['load_js'][]  = JS_NUMBER;
            $this->task_resources['load_css'][] = CSS_SELECTIZE;
            $this->task_resources['load_js'][]  = JS_SELECTIZE;

            $boq_id         = $this->task_details['reference_id'];

            $boq_details    = $this->bq_model->get_boq_details($boq_id);

            $this->task_view_data['boq_details'] = $boq_details;

            //Always set something to sub_nav_left or sub_nav_right in order for it to be loaded.	
         /*    $sub_nav_left_config	 = [
				'title'  		=> 'IO Number',
				'placeholder' 	=> 'IO #',
				'data' 		 	=> []
            ];
            
            $this->data['sub_nav_left'] = $this->construct_lists($sub_nav_left_config);  */

            $this->task_view_data['next_approvers']           = unserialize(NEXT_RECO_APPROVERS);

            //Load the content of the task
            $this->data['page_title']         = 'BOQ Requirements: '.$boq_details['boq_code'];    
            $this->task_page                  = '/upload_boq_recom';
            
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

            $task_id      = decrypt_id($params['etd']);

            $task_details = $this->tm_model->get_task_details($task_id);

            $params       = $this->_validate($params, $task_details);

            //Start the db transaction                
            Portal_Model::beginTransaction();

            $boq_id       = $task_details['reference_id'];

            $table        = Portal_Model::PORTAL_TABLE_PRIA_BOQ;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];

            $where        = ['boq_id' => $boq_id];    

            $audit_action = [AUDIT_UPDATE];	
            $prev_detail  = [$this->bq_model->get_details_for_audit( $table, $where)];
            $actvy_index  = EMPTY($task_details['task_reference_id']) ? 'audit_trail_add' : 'audit_trail_update';
            $activity     = sprintf($this->lang->line($actvy_index), ' Upload BOQ Recommendation');

            $fields        = [
                'reco_amount_civil_works'   => $params['reco_amount_civil_works'],
                'reco_amount_signage'       => (ISSET($params['reco_amount_signage']) AND !EMPTY($params['reco_amount_signage']))? $params['reco_amount_signage']: "0.00",
                'boq_recommendation'        => (ISSET($params['recommendation']) AND !EMPTY($params['recommendation']))? $params['recommendation']: NULL,
                'boq_reco_approver'         => $params['boq_reco_approver'],
                'modified_by'               => $this->session->user_id,
                'modified_date'             => date(FORMAT_DB_DATE)
            ];   

            $this->bq_model->update_boq($fields, $where);

            $curr_detail  = [ $this->bq_model->get_details_for_audit( $table, $where) ];

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            if($params['boq_reco_approver'] == ROLE_MCS_ENG)
            {
                $this->_update_task_role($task_details['pria_stage_id'], ROLE_FPA);

            }
            else
            {
                $this->_update_task_role($task_details['pria_stage_id'], $params['boq_reco_approver']);
            }

            $this->tag_task($task_id, $params['task_status'], ['reference' => $boq_id]);

            if($params['task_status'] == TASK_STATUS_DONE AND $params['boq_reco_approver'] == ROLE_MCS_ENG)
            {
                $next_tasks = $this->bq_model->get_next_predecessors(['pre_pria_task_id' => $task_id]);

                foreach($next_tasks AS $next_task)
                {
                    $this->tag_task($next_task['pria_task_id'], TASK_STATUS_SKIPPED, ['reference' => $boq_id, 'skip_overview' => TRUE]);
                }
            }

            $doc_ref      = encrypt_id($boq_id);

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
            'status' => $status,
            'doc_ref'=> $doc_ref
        ]);
    }
    
    private function _validate($params, $task_details)
    {
       // $insert = EMPTY($task_details['task_reference_id']) ? true : false;
        //Filters the data inputted by the user 
        $params = $this->set_filter( $params )
        ->filter_float('reco_amount_civil_works')
        ->filter_float('reco_amount_signage')
        ->filter_string('recommendation')
        ->filter_string('doc_boq')
        ->filter_string('doc_rfa')
        ->filter();

        //$params['task_id'] = decrypt_id($params['etd']);

        $reco_addtl_label   = (!EMPTY($params['additional_flag']))? "Additional": "Recommended";

        //Define the required fields.
        $required = [
            'reco_amount_civil_works'   => $reco_addtl_label . ' Amount for Civil Works'
        ];

        if(EMPTY($params['additional_flag']))
        {
            $required['reco_amount_signage']    = $reco_addtl_label . ' Amount for Signage';
        }

        $constraints['reco_amount_civil_works'] = [
            'data_type'         => 'amount',
            'name'              => $reco_addtl_label . ' Amount for Civil Works'
        ];

        $constraints['reco_amount_signage'] = [
            'data_type'         => 'amount',
            'name'              => $reco_addtl_label . ' Amount for Signage'
        ];

        $task_id       = decrypt_id($params['etd']);
        $task_document = $this->dm_model->get_document(['pria_task_id' => $task_id, 'document_type_code' => DOC_TYPE_BOQ]);

        $as_built_label     = (!EMPTY($params['additional_flag']))? "As Built Plan": "BOQ";
         
        if(EMPTY($task_document))
        {
            $required['doc_boq'] = $as_built_label . ' File';

            $constraints['doc_boq'] = [
                'data_type'			=> 'string',
                'name'				=> $as_built_label . ' File'
            ];
        }

        $task_document = $this->dm_model->get_document(['pria_task_id' => $task_id, 'document_type_code' => DOC_TYPE_RFA]);
         
        if(EMPTY($task_document))
        {
            $required['doc_rfa'] = 'RFA Form File';

            $constraints['doc_rfa'] = [
                'data_type'         => 'string',
                'name'              => 'RFA Form File'
            ];
        }

        if(ISSET($params['task_status']) AND $params['task_status'] == TASK_STATUS_DONE)
        {
            $required['recommendation']     = 'Recommendations';
            $required['boq_reco_approver']  = 'Next Approver';
        }

        $constraints['recommendation'] = [
            'data_type'         => 'string',
            'name'              => 'Recommendations'
        ];

        $constraints['boq_reco_approver'] = [
            'data_type'         => 'enum',
            'name'              => 'Next Approver',
            'allowed_values'    => [ROLE_ENG_HEAD, ROLE_BC_HEAD, ROLE_MCS_ENG]
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

    private function _update_task_role($stage_id = NULL, $assign_role = NULL)
    {
        try
        {
            if(!EMPTY($stage_id) AND !EMPTY($assign_role))
            {
                $where  = array(
                    'pria_stage_id' => $stage_id,
                    'sequence_no'   => SEQUENCE_NO_THREE
                );

                $task_info      = $this->bq_model->get_task_ref($where);
                $pria_task_id   = $task_info['pria_task_id'];

                $this->tm_model->delete_task_role(['pria_task_id' => $pria_task_id, 'actor_flag' => INITIAL_YES]);

                $this->tm_model->insert_task_role(['actor_flag' => INITIAL_YES, 'role_code' => $assign_role, 'pria_task_id' => $pria_task_id]);
            }
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