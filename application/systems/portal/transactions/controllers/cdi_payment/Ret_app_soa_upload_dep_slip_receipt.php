<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
* @Author      : Jhun Baria
* @Date        : 2023-08-11 22: 00: 00 
* @Desc        : The Following Controller is Made for the purpose of CDI Store Renovation Transaction
* @ReferencedBy: 
*/

class Ret_app_soa_upload_dep_slip_receipt extends Task_Controller
{
	public function __construct()
	{
		parent::__construct();

        $this->controller      = strtolower(__CLASS__);
        $this->module_code     = MODULE_PORTAL_TRANS_STORE_RENOVATION;
        $this->tab_module_code = MODULE_PORTAL_TRANS_STORE_RENOVATION_PAYMENTS;
        $this->folder          = FOLDER_PAYMENTS;

        $this->permissions      = check_permission($this->tab_module_code);

        $this->load->model(FOLDER_SOA.'/Soa_model', 'soa_model'); 
		$this->load->model(FOLDER_CDI.'/cdi_payments_model', 'cp_model');

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

            $this->task_resources['load_css'][]    = CSS_DATETIMEPICKER;
            $this->task_resources['load_css'][]    = CSS_SELECTIZE;
            $this->task_resources['load_js'][]     = JS_DATETIMEPICKER;
            $this->task_resources['load_js'][]     = JS_SELECTIZE;

            $payment_id  = $this->task_details['reference_id'];

            $this->task_view_data['file_list'] = $this->tm_model->get_pria_task_documents($task_id, $payment_id);

            $fields         = [
                'a.org_code',
                'b.name',
                'c.official_store_name',
                'c.bom_num',

                'a.rfp_no',
                'a.rfp_date',

                'a.soa_document_date',
                'a.soa_submission_date',
                'a.soa_num',

                'a.finance_in_charge',

                "CONCAT(
                    AGDEC(d.fname), ' ',
                    IFNULL(AGDEC(d.mname),''), ' ',
                    IFNULL(AGDEC(d.lname),''),' ',
                    IFNULL(AGDEC(d.ext_name),'')
                ) as fullname",

                'a.deposit_date',
                
            ];

            $payment_details   = $this->cp_model->get_payment_details($payment_id, $fields);

            $this->task_view_data['payment_details'] = $payment_details;
            $this->task_details['task_reference_id'] = $payment_id;

            //If There's an uploaded file hide save because edit will be in versioning
           /*  $where          = ['document_type_code' => DOC_TYPE_PAYMENT_PROGRESS, 'reference' => $proj_id];
            $payment_progress   = $this->dm_model->get_document($where); */

            if( ! EMPTY($payment_progress) )
                $this->task_access['hide_btn'] = TRUE;

            //Load the content of the task
            $this->data['page_title'] = 'Payment Plan Requirements: '.$payment_details['payment_num'];
            $this->task_page          = strtolower(__CLASS__);

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
            $payment_id       = $task_details['reference_id'];
            $params       = $this->_validate();

            //Start the db transaction
            Portal_Model::beginTransaction();

            //Update the reference of the task and status ( w/other details )
            $this->_store_payment_info($payment_id, $params);
            $this->tag_task($task_id, $params['task_status'], ['reference' => $payment_id]);

            Portal_Model::commit();

            $doc_ref      = ( ! EMPTY($data)) ? encrypt_id($payment_id) :  '';
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


    private function _validate()
    {
        try
        {
            if( ! $this->permissions[ACTION_EDIT]) throw new Exception($this->lang->line('err_unauthorized_access'));

            $params = get_params();

             //Filters the data inputted by the user 
			$params = $this->set_filter( $params )
            ->filter_date('deposit_date')
            ->filter();

            $params['task_id'] = decrypt_id($params['etd']);

            $task_document = $this->dm_model->get_document(['pria_task_id' => $params['task_id'], 'document_type_code' => DOC_TYPE_DEP_SLIP_REC_DOC]);
            if(EMPTY($task_document))
            {
                $required = [ 'dep_slip_rec_doc' => 'Deposit Slip and Receipt' ];

                $constraints['dep_slip_rec_doc'] = [
                    'data_type' => 'string',
                    'name'      => 'Deposit Slip and Receipt'
                ];

                $this->check_required_fields($params, $required);
                $this->validate_inputs($params, $constraints);
            }
            

            //Define the required fields.
            $required = [ 
                'deposit_date' => 'Deposit Date',
            ];

            $constraints['deposit_date']	= [
                'data_type' => 'date',
                'name'      => 'Deposit Date'
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

    private function _store_payment_info($payment_id, $params) 
    {
        $task_id      = $params['task_id'];
        $table        = PORTAL_TABLE_CDI_PAYMENTS;
        $audit_schema = [ DB_PORTAL ];
        $audit_table  = [ $table ];
        $where        = [ 'payment_id' => $payment_id ];
        $audit_action = [ AUDIT_UPDATE ];
        $prev_detail  = [ $this->cp_model->get_details_for_audit( $table, $where) ];
        $activity     = sprintf($this->lang->line('audit_trail_add'), 'SOA Upload Deposit Slip & Receipt');
        $update       = [ 
            'deposit_date' => $params['deposit_date'],
        ];

        $update['modified_by']   = $this->session->user_id;
        $update['modified_date'] = date(FORMAT_DB_DATETIME);
        $this->cp_model->update_payment($update, $where);
        $curr_detail  = [ $this->cp_model->get_details_for_audit( $table, $where) ];
        $this->audit_trail->log_audit_trail($activity, $this->module_code, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);
    }
}