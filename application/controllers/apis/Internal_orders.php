<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH .'/libraries/REST_Controller.php');
require(APPPATH .'/libraries/Format.php');
use Restserver\Libraries\REST_Controller;

class Internal_orders extends REST_Controller
{
  public function __construct() {
    parent::__construct();
    $this->load->model('Pria_api_model', 'pria_api_model');
  }

  public function get_io_list_get(){
    try{
      $success        = 0;
      $pageStart      = $this->get('pageStart');
      $pageEnd        = $this->get('pageEnd');

      $internal_orders     = $this->pria_api_model->get_io_list($pageStart, $pageEnd);

      $success  = 1;
    }catch (Exception $e){
      $msg      = $e->getMessage();      
    }

    if($success == 1){
      $response = [
        'io_list' => $internal_orders
      ];
    }else{
      $response = [
        'msg'       => $msg,
        'flag'      => $success
      ];
    }

    $this->response($response);
  }
}