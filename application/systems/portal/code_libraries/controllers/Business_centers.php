<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business_centers extends Portal_Controller 
{
  private $module;
  private $module_folder;
  private $controller;
  private $module_js;

  private $permission_view;
  private $permission_add;
  private $permission_edit;
  private $permission_delete;

  protected $model_name = 'Business_center_model';
  
  public function __construct()
  {
    parent::__construct();

    $this->module        = MODULE_PORTAL_BUSINESS_CENTERS;
    $this->module_folder = PORTAL_CODE_LIBRARIES;
    $this->controller    = strtolower(__CLASS__);
    

    $this->load->model($this->module_folder. '/Business_center_model', 'business_center_model');

    try{
      $this->permission_view    = $this->permission->check_permission($this->module, ACTION_VIEW);
      $this->permission_add   = $this->permission->check_permission($this->module, ACTION_ADD);
      $this->permission_edit    = $this->permission->check_permission($this->module, ACTION_EDIT);
      $this->permission_delete  = $this->permission->check_permission($this->module, ACTION_DELETE);
    }
    catch (PDOException $e)
    {
      $this->is_construct_error = TRUE;
      $this->construct_error_msg  = $this->get_user_message($e);
    }
    catch (Exception $e)
    {
      $this->is_construct_error = TRUE;
      $this->construct_error_msg  = $e->getMessage();
    }

    $hash_module = $this->hash($this->module);

    $this->security_action_add    = $hash_module . $this->hash(ACTION_ADD);
    $this->security_action_edit   = $hash_module . $this->hash(ACTION_EDIT);
    $this->security_action_delete = $hash_module . $this->hash(ACTION_DELETE);
    $this->security_action_view   = $hash_module . $this->hash(ACTION_VIEW);
  }

  public function index()
  {
    try
    {

      $data       = array();
      $resources    = array();

      $common_resource                      = $this->get_common_resources(MODULE_PORTAL_BUSINESS_CENTERS);

      $this->module_js                      = HMVC_FOLDER."/".SYSTEM_PORTAL."/".$this->module_folder."/business_centers";

      $resources['load_css']                = array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER));
      $resources['load_js']                 = array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js));
      $resources['loaded_init']             = $common_resource['init'];
      
      $modal = array(
            'modal_add_business_center' => array(
                     'title'      => 'Add Business Center',
                     'size'       => 'sm',
                     'module'     => PORTAL_CODE_LIBRARIES,
                     'controller'  => PORTAL_BUSINESS_CENTERS,
                      'method'      => 'modal_add_business_center',
                      'multi_save'  => TRUE
                  ),
                  'modal_edit_business_center' => array(
                    'title'       => 'Edit Business Center',
                    'size'        => 'sm',
                    'module'      => PORTAL_CODE_LIBRARIES,
                    'controller'  => PORTAL_BUSINESS_CENTERS,
                    'method'      => 'modal_edit_business_center'
                  )
            );

      $resources['load_materialize_modal']  = array_merge($common_resource['modal'], $modal);

      $table_options = array(
          'table_id'      => 'tbl_business_center',
          'path'        => $this->module_folder.'/business_centers/get_business_center_list',
          'advanced_filter' => TRUE
      );

      $resources['datatable'] = $table_options;

      $options_encoded            = json_encode($table_options);
      $resources['loaded_init']   = array_merge($common_resource['init'], array("Business_centers.initialize('".$options_encoded."')"));

      $hash_id  = $this->hash(0);
      $salt     = gen_salt();
      $token    = in_salt($hash_id . '/' . $this->security_action_add, $salt);

      $security = $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_add;

    }
    catch( PDOException $e )
    {
      $msg  = $this->get_user_message($e);

      $this->error_index( $msg );
    }
    catch( Exception $e )
    {
      $msg    = $this->rlog_error($e, TRUE);  

      $this->error_index( $msg );
    }

    $data['security']   = $security;
    $data['permission_add'] = $this->permission_add;

    $this->template->load('business_center', $data, $resources, Portal_Controller::$system);
  }

  public function modal_add_business_center($hash_id, $salt, $token, $security_action)
  {
    $this->_modal($hash_id, $salt, $token, $security_action);
  }

  public function modal_edit_business_center($hash_id, $salt, $token, $security_action)
  {
    $this->_modal($hash_id, $salt, $token, $security_action);
  }

  private function _modal($hash_id, $salt, $token, $security_action)
  {
    try
    {
      $data       = $resources = array();
      $security     = "";
      $active     = "checked";
      $key      = $this->get_hash_key('bc_code');
      $where      = array();
      $where[$key]  = $hash_id;
      $info       = $this->business_center_model->get_specific_business_center($where);
      
      $bc_code    = $info['bc_code'];

      switch($security_action)
      {
        case $this->security_action_add:
          if( ! empty($bc_code) )
            throw new Exception($this->lang->line('err_unauthorized_add'));

          if($this->permission_add === FALSE)
            throw new Exception($this->lang->line('err_unauthorized_add'));
            break;

        case $this->security_action_edit:

          if(empty($bc_code) )
            throw new Exception($this->lang->line('err_unauthorized_edit'));

          if($this->permission_edit === FALSE)
            throw new Exception($this->lang->line('err_unauthorized_edit'));

            $data['info'] = $info;
            $active     = !empty($info['deleted_flag']) ? "" : "checked";
            break;

        default:
          throw new Exception($this->lang->line('err_unauthorized_access'));
          break;
      }

      $resources['load_css']  = array();
      $resources['load_js'] = array();
      $resources['loaded_init'] = array(
          "Business_centers.save();"
      );

      $salt     = gen_salt();
      $token    = in_salt($hash_id . '/' . $security_action, $salt);

      $security = $hash_id . '/' . $salt . '/' . $token . '/' . $security_action;

      $modal_page = 'modals/business_centers';
    }
    catch(PDOException $e)
    {
      $msg  = $this->get_user_message($e);

      $this->error_modal($msg);
    }
    catch(Exception $e)
    {
      $msg  = $this->rlog_error($e, TRUE);

      $this->error_modal($msg);
    }

    $data['active']   = $active;
    $data['security'] = $security;

    $this->load->view($modal_page, $data);
    $this->load_resources->get_resource($resources);
  }

  public function get_business_center_list()
  {
    $flag       = $total_records = $display_records = 0;
    $table_data = array();

    try
    {
      $params = get_params();

      $total_records    = $this->business_center_model->get_business_center_list();
      $records_info     = $this->business_center_model->get_business_center_list($params);

      $records          = $records_info['records'];
      $display_records  = $records_info['display_records'];

      foreach($records as $records)
      {
        $hash_id  = $this->hash($records['bc_code']);
        $salt   = gen_salt();

        $actions  = "";
        if($this->permission_edit)
        {
          $token  = in_salt($hash_id . '/' . $this->security_action_edit, $salt);
          $url  = $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_edit;

          $actions.= "<a class='tooltipped' data-tooltip='Edit' href='#modal_edit_business_center' onclick=\"modal_edit_business_center_init('".$url."')\"><i class='material-icons'>mode_edit</i></a>";
        }

        if($this->permission_delete)
        {
          $token  = in_salt($hash_id . '/' . $this->security_action_delete, $salt);
          $url  = $hash_id . '/' . $salt . '/' . $token . '/' . $this->security_action_delete;

          $onclick = 'content_delete("business center", "'.$url.'")';
          $actions.= "<a href='javascript:;' onclick='".$onclick."' class='tooltipped' data-tooltip='Delete' data-position='bottom' data-delay='50'><i class='material-icons'>delete</i></a>";
        }
          
        $table_data[] = array(
            $records['bc_code'],
            $records['bc_name'],
            date('M d, Y', strtotime($records['created_date'])),
            //$records['status'],
            "<div class='table-actions'>" . $actions . "</div>"
        );
      }
      
      $flag = 1;
      $msg  = "";
    }
    catch(PDOException $e)
    {
      $msg = $this->get_user_message($e);
    }
    catch(Exception $e)
    {
      $msg = $this->rlog_error($e, TRUE);
    }

    echo json_encode(
      array(
        'aaData'        => $table_data,
        'sEcho'         => intval($params['sEcho']),
        'iTotalRecords'     => $total_records,
        'iTotalDisplayRecords'  => $display_records,
        'flag'          => $flag,
        'msg'         => $msg
        )
      );
  }

  public function process()
  {
    try 
    {
      $status       = ERROR;
      $msg        = "";
      $flag         = 0;
      $options_encoded  = "";

      $params = get_params();
      
      $validated_data = $this->_validate_form($params);
      $key      = $this->get_hash_key('bc_code');
      
      
      $where      = array();
      $where[$key]  = $this->hash($params['bc_code']);

      $old_info     = $this->business_center_model->get_specific_business_center($where);

      $bc_code  = $old_info['bc_code'];

      Portal_Model::beginTransaction();

      switch($params['security_action'])
      {
        case $this->security_action_add:
        
        if($this->permission_add === FALSE)
          throw new Exception($this->lang->line('err_unauthorized_add'));

          $this->_check_unique($validated_data["bc_name"]);

          $fields = array(
            "bc_code"         => $validated_data["bc_code"],
            "bc_name"         => $validated_data["bc_name"],
            "created_by"      => $this->session->userdata('user_id'),
            "created_date"    => date('Y-m-d H:i:s')
          );

          $bc_code = $this->business_center_model->insert_business_center($fields);

          $audit_action[] = AUDIT_INSERT;
          $audit_table[]  = Portal_Model::PORTAL_TABLE_BUSINESS_CENTER;
          $audit_schema[] = DB_PORTAL;
          $prev_detail[]  = array();
          $curr_detail[]  = array($fields);
          $activity       = $validated_data["bc_name"] . " has been added in the system.";

          $msg  = $this->lang->line('data_saved');
          break;

        case $this->security_action_edit:

          if($this->permission_edit === FALSE)
            throw new Exception($this->lang->line('err_unauthorized_add'));

          if($validated_data["bc_name"] != $old_info['bc_name'])
            $this->_check_unique($validated_data["bc_name"]);

          $fields = array(
            "bc_code"   => $validated_data["bc_code"],
            "bc_name"     => $validated_data["bc_name"],
            "modified_by"   => $this->session->userdata('user_id'),
            "modified_date"   => date('Y-m-d H:i:s')
          );

          $this->business_center_model->update_business_center($fields, array('bc_code' => $bc_code));

          $audit_action[] = AUDIT_UPDATE;
          $audit_table[]  = Portal_Model::PORTAL_TABLE_BUSINESS_CENTER;
          $audit_schema[] = DB_PORTAL;
          $prev_detail[]  = array($old_info);
          $curr_detail[]  = array($fields); 
          $activity   = $validated_data["bc_name"] . " has been updated in the system.";
          $msg  = $this->lang->line('data_updated');
          break;

      default:
          throw new Exception($this->lang->line('err_unauthorized_access'));
          break;
      }

      $this->audit_trail->log_audit_trail($activity, $this->module, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

      $flag   = 1;
      $status = SUCCESS;

      $table_options = array(
          'table_id'      => 'tbl_business_center',
          'path'        => $this->module_folder.'/Business_centers/get_business_center_list',
          'advanced_filter' => TRUE
      );
      $options_encoded = json_encode($table_options);


      Portal_Model::commit();
    }
    catch(PDOException $e)
    {
      $msg  = $this->get_user_message($e);

      Portal_Model::rollback();
    }
    catch(Exception $e)
    {
      $msg    = $this->rlog_error($e, TRUE);  

      Portal_Model::rollback();
    }

    echo json_encode(
      array(
          'status'  => $status,
          'msg'   => $msg,
          'action'  => $params['btn_action'],
          'datatable' => $options_encoded
      )
    );
  }

  private function _validate_form(&$params)
  {
    try{

      $this->validate_security($params);

      $fields               = array();
      $fields['bc_code']    = $params['bc_code'];
      $fields['bc_name']    = $params['bc_name'];

      $this->check_required_fields($params, $fields);
          
      return $this->_validate_inputs($params);
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }

  private function _validate_inputs($params)
  {
    try
    {
      /* Validate constraints */
      $validation = array();

      $validation['bc_code']  = array(
        'data_type' => 'string',
        'name'    => 'BC Code',
        'min_len' => 1,
        'max_len'   => 45
      );

      $validation['bc_name']  = array(
        'data_type' => 'string',
        'name'    => 'BC Name',
        'min_len' => 1,
        'max_len'   => 100
      );

      return $this->validate_inputs($params, $validation);
    }
    catch ( Exception $e )
    {
      throw $e;
    }
  }

  public function delete_business_center()
  {
    try
    {
      $status   = ERROR;
      $params   = get_params();

      $params['security'] = $params['param_1'];

      $this->validate_security($params);
      
      $key      = $this->get_hash_key('bc_code');

      $where      = array();
      $where[$key]  = $params['hash_id'];
      
      $old_info     = $this->business_center_model->get_specific_business_center($where);

      $bc_code  = $old_info['bc_code'];

      IF($this->permission_delete === TRUE){
        if(empty($bc_code))
          throw new Exception($this->lang->line('err_unauthorized_access'));

        Portal_Model::beginTransaction();

        $fields = array(
              "deleted_flag" => ACTIVE_FLAG
            );

            $this->business_center_model->update_business_center($fields, array('bc_code' => $bc_code));

        $audit_action[] = AUDIT_DELETE;
        $audit_table[]  = Portal_Model::PORTAL_TABLE_BUSINESS_CENTER;
        $audit_schema[] = DB_PORTAL;
        $prev_detail[]  = array($old_info);
        $curr_detail[]  = array();
        $activity   = $old_info['bc_name'] . " has been deleted.";

        $this->audit_trail->log_audit_trail($activity, $this->module, $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema);

        Portal_Model::commit();

        $status = SUCCESS;
        $msg  = $this->lang->line('data_deleted');
      }
    }
    catch(PDOException $e)
    {
      Portal_Model::rollback();

      $msg = $this->get_user_message($e);
    }
    catch(Exception $e)
    {
      Portal_Model::rollback();

      $msg = $this->rlog_error($e, TRUE);
    }

    $table_options = array(
        'table_id'        => 'tbl_business_center',
        'path'            => $this->module_folder.'/business_centers/get_business_center_list',
        'advanced_filter' => TRUE
    );

    $info = array(
        "status"            => $status,
        "msg"               => $msg,
        "reload"            => 'datatable',
        "datatable_options" => $table_options
    );

    echo json_encode($info);
  }

  private function _check_unique($value)
  {
    $info = $this->business_center_model->get_specific_business_center(array('TRIM(UPPER(bc_name))' => trim(strtoupper($value))));
    
    if( ! empty($info['bc_name']))
      throw new Exception(sprintf($this->lang->line('duplicate_data'), $value));
  }
}