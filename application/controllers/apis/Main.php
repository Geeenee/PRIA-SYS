<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH .'/libraries/REST_Controller.php');
require(APPPATH .'/libraries/Format.php');
use Restserver\Libraries\REST_Controller;

class Main extends REST_Controller{

  public function __construct(){
    parent::__construct();

    $this->load->model('auth_model', 'auth_model');
    $this->load->model('Pria_api_model', 'pria_api_model');
  }

  public function login_user_post(){
    try{
      $success      = 0;
      $username     = $this->post('username');
      $password     = $this->post('password'); 
      if(EMPTY($username))
        throw new Exception($this->lang->line('err_required_username_or_email'));

      if(EMPTY($password))
        throw new Exception($this->lang->line('err_required_password'));

      // Get the user info
      $user_info = $this->auth_model->get_active_user($username);

      // Checks if user exists in database
      if(EMPTY($user_info))
        throw new Exception($this->lang->line('err_required_user_does_not_exist'));

      /*
      $vendor_role_exist = $this->pria_api_model->check_if_vendor_role_exists($user_info['user_id']);

      if(!$vendor_role_exist)
        throw new Exception($this->lang->line('err_required_invalid_role'));
      */
      
      // Token based on the inputted password
      $token = in_salt($password, $user_info["salt"], TRUE);   

      // Checks if username and password matches
      if($token != $user_info['password'])
        throw new Exception($this->lang->line('err_required_credentials_invalid'));

      $success = 1;
      $msg     = $this->lang->line('msg_credentials_valid');
    
    }catch (Exception $e){
      $msg = $e->getMessage();
    }

    $response = [
      'msg'       => $msg,
      'flag'      => $success
    ];

    $this->response($response);
  }
  
  public function get_personal_info_get(){
    try{
      $success      = 0;
      $username     = $this->get('username');

      // Checks if param has username
      if(EMPTY($username))
        throw new Exception($this->lang->line('err_required_username'));

      // Get the user info
      $user_info = $this->auth_model->get_active_user($username);  
      // Checks if user exists in database
      if(EMPTY($user_info))
        throw new Exception($this->lang->line('err_required_user_does_not_exist'));

      $success = 1;
    }catch (Exception $e){
      $msg = $e->getMessage();      
    }

    if($success == 1){
      $vendor_code = $this->pria_api_model->get_vendor_code_by_user_id($user_info['user_id']);
      $org_code = $this->pria_api_model->get_org_code_by_vendor_code($vendor_code);

      $personal_info = [
        'user_id'     => $user_info['user_id'],
        'username'    => $user_info['username'],
        'email'       => $user_info['email'],
        'name'        => $user_info['name'],
        'photo'       => $user_info['photo'],
        'vendor_code' => ($vendor_code == null) ? "" : $vendor_code,
        'org_code'    => ($vendor_code == null) ? "" : $org_code
      ];

      $response = [
        'personalInfo' => array($personal_info)
      ];
    }else{
      $response = [
        'msg'       => $msg,
        'flag'      => $success
      ];
    }

    $this->response($response);
  }

  public function send_forgot_password_link_post(){
    $flag   = 0;
    $msg  = "";
    
    try{
      $status = ERROR;
      //$email = filter_var(, FILTER_SANITIZE_EMAIL);
      $email    = $this->post('email');
      
      if(EMPTY($email)) throw new Exception($this->lang->line('email_required'));

      $salt     = gen_salt(TRUE);
  
      $user_info  = $this->auth_model->get_active_user($email, BY_EMAIL);

      if(EMPTY($user_info)) throw new Exception($this->lang->line('contact_admin'));

      $allowed_status   = array(
        STATUS_ACTIVE
      );

      if( !in_array( $user_info['status'], $allowed_status ) ){
        throw new Exception($this->lang->line('contact_admin'));
      }
      
      $username   = $user_info['username'];
      
      // SEND RESET PASSWORD INSTRUCTION
      $this->_send_reset_password($username, $email, $salt);
      
      // BEGIN TRANSACTION
      SYSAD_Model::beginTransaction();
      
      $this->auth_model->update_reset_salt($salt, $username);
      
      SYSAD_Model::commit();
  
      $status = SUCCESS;
      $msg = $this->lang->line('reset_password');
  
    }
    catch(PDOException $e)
    {
      SYSAD_Model::rollback();
      $msg = $e->getMessage();
    }
    catch(Exception $e)
    {
      SYSAD_Model::rollback();
      $msg = $e->getMessage();
    }
  
    $response     = array(
      "status"  => $status,
      "msg"     => $msg
    );

    $this->response($response);
  }

  private function _send_reset_password($username, $email, $salt){
    try{
      $email_data   = array();
      $template_data  = array();
  
      
      $system_title   = get_setting(GENERAL, "system_title");
      
      // required parameters for the email template library
      $email_data["from_email"]   = get_setting(GENERAL, "system_email");
      $email_data["from_name"]  = $system_title;
      $email_data["to_email"]   = array($email);
      $email_subject        = 'Reset Password';
      $email_data["subject"]    = $email_subject;
        
      // additional set of data that will be used by a specific template
      $sys_logo           = get_setting(GENERAL, "system_logo");
      $system_logo_src      = base_url() . PATH_IMAGES . "logo_white.png";

      if( !EMPTY( $sys_logo ) )
      {
        $root_path      = $this->_get_root_path();

        $sys_logo_path    = $root_path. PATH_SETTINGS_UPLOADS . $sys_logo;
        $sys_logo_path    = str_replace(array('\\','/'), array(DS,DS), $sys_logo_path);

        if( file_exists( $sys_logo_path ) )
        {
          $system_logo_src = output_image($sys_logo, PATH_SETTINGS_UPLOADS);

          $system_logo_src = getimagesize($sys_logo_path) ? $system_logo_src : base_url() . PATH_IMAGES . "logo_white.png";
        }
      }

      $template_data["logo"]  = $system_logo_src;
      
      $template_data["email_subject"] = $email_subject;
      $template_data["email"]     = $email;
      $template_data["system_name"]   = $system_title;
      $template_data["username"]    = $username;
      $template_data["salt"]      = $salt;
        
      $this->email_template->send_email_template($email_data, "emails/reset_password", $template_data);
    }
    catch(PDOException $e){     
      $this->rlog_error($e, TRUE);
    }
    catch(Exception $e){
      $this->rlog_error($e, TRUE);
    }
  }

  private function _get_root_path(){
    $path   = FCPATH;

    try
    {
      $path   = get_root_path();
    }
    catch( PDOException $e )
    {
      throw $e;
    }
    catch( Exception $e )
    {
      throw $e;
    }

    return $path;
  }

  public function get_all_notifications_get(){
    try{
      $success = 0;
      $notify_users = $this->get('notify_users');
      $show_reminders = $this->get('show_reminders');
      $limit = empty($this->get('limit')) ? null : $this->get('limit');
      $is_count = !empty($this->get('is_count'));
      $show_all = !empty($this->get('show_all'));

      if(empty($notify_users))
        throw new Exception($this->lang->line('err_required_notify_users'));

      $notifs   = $this->pria_api_model->get_all_notifications($notify_users, $show_reminders, $limit, $is_count, $show_all);

      $success  = 1;
    }catch (Exception $e){
      $msg      = $e->getMessage();      
    }

    if($success == 1){
      $response = [
        'notifs' => $notifs
      ];
    }else{
      $response = [
        'msg'       => $msg,
        'flag'      => $success
      ];
    }

    $this->response($response);
  }

  public function register_user_post(){
    try{
      $flag         = ERROR;
      $status       = '';
      $response     = [];
      $now          = date(FORMAT_DB_DATE);

      $fname            = $this->post('fname');
      $mname            = $this->post('mname');
      $lname            = $this->post('lname');
      $email            = $this->post('email');
      $gender           = $this->post('gender');
      $org_code         = $this->post('org_code');
      $location_code    = $this->post('location_code');
      $vendor_code      = $this->post('vendor_code');

      if(EMPTY($fname))
        throw new Exception('First name is required.');

      if(EMPTY($mname))
        throw new Exception('Middle name is required.');

      if(EMPTY($lname))
        throw new Exception('Last name is required.');

      if(EMPTY($email))
        throw new Exception('Email is required.');

      if(EMPTY($gender))
        throw new Exception('Gender is required.');

      if(EMPTY($org_code))
        throw new Exception('Org Code is required.');

      if(EMPTY($location_code))
        throw new Exception('Location is required.');

      if(EMPTY($vendor_code))
        throw new Exception('Vendor Code is required.');

      Portal_Model::beginTransaction();

      //Set up fields that will be inserted 
      $insert_user_fields      = [
          'location_code'         => $location_code,
          'org_code'              => $org_code,
          'fname'                 => array(filter_var($fname, FILTER_SANITIZE_STRING), 'ENCRYPT'),
          'lname'                 => array(filter_var($lname, FILTER_SANITIZE_STRING), 'ENCRYPT'),
          'mname'                 => array(filter_var($mname, FILTER_SANITIZE_STRING), 'ENCRYPT'),
          'gender'                => $gender,
          'email'                 => array(filter_var($email, FILTER_SANITIZE_STRING), 'ENCRYPT'),
          'created_date'          => $now
      ];

      //insert to pria_core.users table
      $user_id = $this->pria_api_model->insert_user($insert_user_fields, TRUE);

      $user_roles_fields      = [
          'user_id'         => $user_id,
          'role_code'       => TASK_ROLE_VENDOR,
          'main_role_flag'  => 1
      ];

      //insert to pria_core.user_roles table
      $this->pria_api_model->insert_user_roles($user_roles_fields);

      $insert_vendor_user_fields      = [
          'vendor_code'   => $vendor_code,
          'user_id'       => $user_id
      ];

      //insert to vendor_users table
      $this->pria_api_model->insert_vendor_user($insert_vendor_user_fields);

      Portal_Model::commit();

      $flag     = SUCCESS;
      $msg      = 'User successfully registered.';
    }catch(PDOException $e){
      $msg = $e->getMessage();
      Portal_Model::rollback();
    }catch(Exception $e){
      $msg = $e->getMessage();
      Portal_Model::rollback();
    }

    $response = [
      'msg'       => $msg,
      'flag'      => $flag
    ];

    $this->response($response);
  }
}