<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH .'/libraries/REST_Controller.php');
require(APPPATH .'/libraries/Format.php');
use Restserver\Libraries\REST_Controller;

class Soa extends REST_Controller{
  public function __construct(){
    parent::__construct();
    $this->load->model('Pria_api_model', 'pria_api_model');
  }
  
  public function submit_soa_document_post(){
    try{
      $success              = 0;

      $reference_num        = $this->post('reference_num');
      $document_type_code   = $this->post('document_type_code');
      $file_name            = $this->post('file_name');
      $sys_file_name        = $this->post('sys_file_name');
      $created_by           = $this->post('created_by');
      $now                  = date(FORMAT_DB_DATE);

      if(EMPTY($reference_num))
        throw new Exception($this->lang->line('err_required_reference_number'));

      if(EMPTY($document_type_code))
        throw new Exception($this->lang->line('err_required_document_type_code'));

      if(EMPTY($file_name))
        throw new Exception($this->lang->line('err_required_soa_file'));

      if(EMPTY($sys_file_name))
        throw new Exception($this->lang->line('err_required_soa_sys_file_name'));

      if(EMPTY($created_by))
        throw new Exception($this->lang->line('err_required_user_id'));
      
      Portal_Model::beginTransaction();   

      switch ($document_type_code) {
        case DOC_TYPE_SOA:
          $reference_id = $this->pria_api_model->get_soa_id_by_reference_id($document_type_code, $reference_num);

          if(EMPTY($reference_id[0]['soa_id']))
            throw new Exception($this->lang->line('err_reference_number_not_found'));

          $insert_fields =   [
                        'reference'             => $reference_id[0]['soa_id'],
                        'document_type_code'    => $document_type_code,
                        'file_name'             => $file_name,
                        'sys_file_name'         => $sys_file_name,
                        'version'               => 1,
                        'created_by'            => $created_by,
                        'created_date'          => $now
                      ];

          $this->pria_api_model->insert_document($insert_fields);

          break;
        default:
          break;
      }

      Portal_Model::commit();

      $success = 1;
      $msg  = $this->lang->line('data_saved');
    }catch(PDOException $e){
      switch ($e->getCode()) {
        case 23000:
          $msg = $this->lang->line('err_duplicate_entry');
          break;
        default:
          $msg = $e->getMessage();
          break;
      }

      Portal_Model::rollback();
    }catch(Exception $e){
      $msg = $e->getMessage();
      Portal_Model::rollback();
    }

    $response = [
      'msg'       => $msg,
      'flag'      => $success
    ];

    $this->response($response);
  }
}