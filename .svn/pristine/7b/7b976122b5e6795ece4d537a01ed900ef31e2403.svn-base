<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_soa extends Task_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->controller       = strtolower(__CLASS__);
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
            $task_reference   = $task['reference'];

           /*  print_var_export($common); die; */

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

            $data['task']['content']    = $this->load->view($this->path_task_views.'/upload_soa', $soa_data, TRUE);        
            
            $this->template->load($this->tpl_task_container, $data, $resources, Portal_Controller::$system);
        }
        catch( PDOException $e )
        {
            $msg    = $this->get_user_message($e);

            $this->error_page( $msg );
        }
        catch( Exception $e )
        {
            $msg    = $this->rlog_error($e, TRUE);  
            
            $this->error_page( $msg );
        }
    }
    
    public function process()
    {
        try
        {
            $flag         = ERROR;
            $data         = $this->_validate();
            $status       = TASK_STATUS_ONGOING;
            $response     = [];
            $now          = date(FORMAT_DB_DATE);
            //Initial audit trail config
            $table        = Portal_Model::PORTAL_TABLE_SOA;
            $audit_schema = [DB_PORTAL];
            $audit_table  = [$table];
            
            $task_id      = $data['task_id'];

            $task_details = $this->tm_model->get_task_details($task_id);

            //Get SOA details               
            $soa_details = $this->toll_partners_model->get_specific_soa($task_details['reference_id']);
            
            //Set initial fields that is present for both insert and update action
            $fields       = [
                'submission_date'   => std_db_date_format($now)
            ];
            //Start the db transaction                
            Portal_Model::beginTransaction();   
            //If reference id is empty action will be insert

            if( EMPTY($soa_details['submission_date'])){
                //Set audit trail config for insert
                $audit_action = [AUDIT_INSERT]; 
                $prev_detail  = [array()];
                $activity     = sprintf($this->lang->line('audit_trail_add'), ' SOA Report');

                $soa_id       = $this->tm_model->get_task_workflow_reference_id($task_id);
                $soa_details = $this->toll_partners_model->get_specific_soa($soa_id);

                //Update SOA
                $where = array('soa_id' => $soa_id);
                $this->toll_partners_model->update_soa($where, $fields); 

                //Set up fields that will be inserted 
                $insert       = [
                    'soa_id'                 => $soa_id,
                    'document_type_code'    => 'DOC_SOA',
                    'file_name'             => $data['soa_report_file'],
                    'sys_file_name'         => $data['soa_report_file'],
                    'version'               => 1,
                    'created_by'            => $this->session->user_id,
                    'created_date'          => $now
                ];

                $this->toll_partners_model->insert_soa_document($insert);     
                
                $where          = ['soa_id' => $soa_id];
                $curr_detail    = [ $this->toll_partners_model->get_details_for_audit($table, $where) ];
                
                $ongoing             = $this->pria_workflow->tag_task_status($task_id, TASK_STATUS_ONGOING, ['reference' => $dgr_id]);

                $response['actor']   = $ongoing['actor_name'];                
                $status              = TASK_STATUS_ONGOING;
            }

            /*
            else
            {
                $audit_action = [AUDIT_UPDATE];

                $prev_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
                $activity     = sprintf($this->lang->line('audit_trail_update'), ' Harvest report');

                $soa_id        = $this->tm_model->get_task_workflow_reference_id($task_id);                

                //Update internal orders
                $where  = array('soa_id' => $soa_id);
                $update = array(
                    'harvest_rep_submit_date'   => std_db_date_format($data['date_submitted'])
                );
                $this->toll_partners_model->update_internal_order($where, $update);                


                $update       = [
                    'file_name'             => $data['soa_report_file'],
                    'sys_file_name'         => $data['soa_report_file'],
                    'modified_by'           => $this->session->user_id,
                    'modified_date'         => $now
                ];

                $this->io_model->update_internal_order_document($where, $update);

                $where          = ['soa_id' => $soa_id];
                $curr_detail  = [ $this->io_model->get_details_for_audit( $table, $where) ];
            }
            */

            $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

            Portal_Model::commit();

            $flag = SUCCESS;
            $msg  = $this->lang->line('data_saved');
        }
        catch(PDOException $e)
        {
            $msg    = $this->get_user_message($e);

            Portal_Model::rollback();
        }
        catch(Exception $e)
        {
            $msg    = $this->rlog_error($e, TRUE);  

            Portal_Model::rollback();
        }

        echo json_encode([
            'flag'  => $flag,
            'msg'   => $msg,
            'task'   => $response,
            'status' => $status
        ]);
    }
    
   
    private function _validate()
    {
        try
        {
            $params = get_params();

            //Filters the data inputted/uploaded by the user
            $params = $this->set_filter( $params )
            ->filter_string('soa_report_file')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            //Define the required fields.
            $constraints['soa_report_file']    = [
                'data_type'         => 'string',
                'name'              => 'SOA Report File'
            ];    

            $constraints['task_id'] = [
                'data_type'   => 'db_value',
                'name'        => 'ETD',
                'field'       => 'COUNT( 1 ) as check_row',
                'check_field' => 'check_row',
                'where'       => 'pria_task_id',
                'table'       =>  DB_PORTAL.'.'.Portal_Model::PORTAL_TABLE_PRIA_TASKS
            ];

            /* Validate the required fields */
            $this->check_required_fields($params, $required);

            /* Validate constraints */
            $data       = $this->validate_inputs($params, $constraints);

            return $data;
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