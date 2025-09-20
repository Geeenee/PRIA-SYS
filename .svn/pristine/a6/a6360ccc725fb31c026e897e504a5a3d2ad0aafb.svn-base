<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business_center_model extends Portal_Model{

  private $tbl_business_center;
  private $tbl_users;

  public function __construct()
  {
    parent::__construct();
    $this->tbl_business_center  = Portal_Model::PORTAL_TABLE_BUSINESS_CENTER;
    $this->tbl_users            = Portal_Model::CORE_TABLE_USERS;
  }

  public function get_specific_business_center($where)
  {
    try
    {
      $fields = array(
        "bc_code",
        "bc_name",
        "deleted_flag",
        "created_date",
        "modified_by",
        "modified_date"
      );

      return $this->select_data($fields, $this->tbl_business_center, $multiple=FALSE, $where);
    }
    catch(PDOException $e)
    {
      throw $e;
    }
  }

  public function get_business_center_list($params=NULL)
  {
    try
    {
      $val = $filters = array();
      $where = $order = $limit = "";

      if($params === NULL)
      {
        $select_fields  = 'COUNT(A.bc_code) total';
      }
      else
      {        
        $select_fields = "
          SQL_CALC_FOUND_ROWS
            A.bc_code,
            A.bc_name,
            A.created_date
        ";

        $filters  = array(
            "A-bc_code",
            "A-bc_name",
            "created_date_start" => function() use( &$params )
            {
              $date_from  = date_format( date_create( $params['created_date_start'] ), 'Y-m-d' );
              $date_to  = date_format( date_create( $params['created_date_end'] ) , 'Y-m-d' );
              $str    = " DATE(A.created_date) BETWEEN '".$date_from."' AND '".$date_to."' ";
              
              return array(
                  'where' => $str
              );
              },
            "created_date_end" => function() use( &$params )
            {
              $date_to  = date_format( date_create( $params['created_date_end'] ) , 'Y-m-d' );
              $str    = " DATE(A.created_date) <= '".$date_to."' ";
              
              return array(
                  'where' => $str
              );
            }
            );
        
        $orders = array(
            "A.bc_code",
            "A.bc_name",
            "A.created_date"
        );

        $filter = $this->filtering($filters, $params, TRUE);
        $order  = $this->ordering($orders, $params);
        $limit  = $this->paging($params);

        $where  = $filter["search_str"];
        $val  = $filter["search_params"];

        // print_r($where);
        // print_r($val);
        // die();
      }

      $query = <<<EOS
        SELECT
          $select_fields
        FROM $this->tbl_business_center A
        LEFT JOIN $this->tbl_users B ON A.created_by = B.user_id
        WHERE A.deleted_flag = 0
        $where 
        $order
        $limit
EOS;
      
      if(empty($params))
      {
        $total = $this->query($query, $val, TRUE, FALSE);

        return $total['total'];
      }
      else
      {
        return array(
            'records'     => $this->query($query, $val, TRUE),
            'display_records' => $this->_get_display_records()
        );
      }

    }
    catch(PDOException $e)
    {
      throw $e;
    }
  }

  public function _get_display_records()
  {
    try
    {
      $query = "SELECT FOUND_ROWS() cnt";

      $count = $this->query($query, NULL, TRUE, FALSE);

      return $count['cnt'];
    }
    catch (PDOException $e)
    {
      throw $e;
    }
  }

  public function insert_business_center($fields)
  {

    try
    {
      return $this->insert_data($this->tbl_business_center, $fields, TRUE);
    }
    catch(PDOException $e)
    {
      throw $e;
    }

  }
  public function update_business_center($fields, $where)
  {

    try
    {
      return $this->update_data($this->tbl_business_center, $fields, $where);
    }
    catch(PDOException $e)
    {
      throw $e;
    }

  }

  public function delete_business_center($bc_code)
  {

    try
    {
      return $this->delete_data($this->tbl_business_center, array('bc_code' => $bc_code));
    }
    catch(PDOException $e)
    {
      throw $e;
    }

  }
}