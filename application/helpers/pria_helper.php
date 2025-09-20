<?php  if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 |--------------------------------------------------------------------------
 | List of Useful Generic Functions
 |--------------------------------------------------------------------------
 */
function throw_var_export($arr)
{
    throw new Exception('<pre>'.var_export($arr, true).'</pre>');
}

function print_var_export(/*$arr*/)
{
    $arg_list = func_get_args();

    echo '<pre>';

    foreach ($arg_list as $arr) {
        print_r($arr);
    }

    echo '</pre>';
}


function check_permission($module_code, $action_code='')
{
    $CI 	=& get_instance();
    
    if (! empty($action_code)) {
        return $CI->permission->check_permission($module_code, $action_code);
    } else {
        $CI->load->model(CORE_COMMON.'/sys_param_model', 'sys_param', true);

        $permissions 		= array();
        $params 			= array();

        $params['fields'] 	= array('sys_param_value');
        $params['where'] 	= array('sys_param_type' => SYS_PARAM_ACTIONS);
        $params['multiple'] = true;

        $actions 			= array_column($CI->sys_param->get_sys_param($params), 'sys_param_value');
            
        foreach ($actions as $val) {
            $permissions[$val] = $CI->permission->check_permission($module_code, $val);
        }
        
        return $permissions;
    }
}

function encrypt_id($id)
{
    $enc_id = base64_url_encode($id);
    $salt   = gen_salt();
    $token  = in_salt($id, $salt);


    return base64_url_encode(implode('/', [$enc_id, $salt, $token]));
}

function decrypt_id($encrypted_values, $check_salt=true)
{
    $decrypted = base64_url_decode($encrypted_values);
    $values    = explode('/', $decrypted);
    
    $id		   = base64_url_decode($values[0]);
    $salt      = $values[1];
    $token     = $values[2];

    if ($check_salt) {
        check_salt($id, $salt, $token);
    }

    return $id;
}

/**
 * @Author: NinoSkopac
 * @Date: 2019-05-21 09:32:24
 * @TakenFrom : Taken from https://github.com/NinoSkopac/array_column_recursive
 */
function array_column_recursive(array $haystack, $needle)
{
    $found = [];
    array_walk_recursive($haystack, function ($value, $key) use (&$found, $needle) {
        if ($key == $needle) {
            $found[] = $value;
        }
    });
    return $found;
}

function std_datepicker_format($date)
{
    if ($date) {
        return date(FORMAT_DATEPICKER_DATE, strtotime($date));
    }
}

function std_date_format($date)
{
    if ($date) {
        return date(FORMAT_DATE, strtotime($date));
    }
}

function std_db_date_format($date)
{
    if ($date) {
        return date(FORMAT_DB_DATE, strtotime($date));
    }
}

function std_db_datetime_format($date)
{
    if ($date) {
        return date(FORMAT_DATETIME_FORMAT, strtotime($date));
    }
}

function create_img_tag($img, $img_path, $options, $return=true)
{
    $CI				= &get_instance();
    $href_path  	= base_url().$img_path.$img;
    $img_dir_path 	= str_replace(array('\\','/'), array(DS,DS), $img_path).$img;
    $name 			= (isset($options['name'])) ? $options['name'] : $CI->session->name;
    $tpl			=<<<EOS
		<img style="{$options['style']}" class="{$options['class']} %s" %s>
EOS;
    
    if (file_exists($img_dir_path) == true && ! empty($img)) {
        $tpl = sprintf($tpl, '', 'src="'.$href_path.'"');
    } else {
        $tpl = sprintf($tpl, 'letter-avatar ', 'data-name="'.$name.'"');
    }
    

    if ($return) {
        return $tpl;
    } else {
        echo $tpl;
    }
}

function create_document_tag($file_details, $return=true, $status_id = null, $has_approval = false, $is_returned = false, $force_show_version = false, $stand_alone = FALSE)
{
    $CI 			= &get_instance();
    
    $CI->load->model(PORTAL_TRANSACTIONS.'/documents_model', 'dm_model');

    $document_id 		= base64_url_encode($file_details['document_id']);
    $reference 			= base64_url_encode($file_details['reference']);
    $file_name  		= $file_details['file_name'];
    $sys_file_name  	= $file_details['sys_file_name'];
    $version  			= $file_details['version'];
    $document_type  	= base64_url_encode($file_details['document_type_code']);
    $file_ext 			= explode('.', $file_details['file_name']);
    // $created_by     	= $CI->dm_model->get_user_fullname($file_details['created_by']);

    $created_by     	= (! empty($file_details['modified_by'])) ? $CI->dm_model->get_user_fullname($file_details['modified_by']) : $CI->dm_model->get_user_fullname($file_details['created_by']);

    $created_date  		= (! empty($file_details['modified_date'])) ? std_db_datetime_format($file_details['modified_date']) : std_db_datetime_format($file_details['created_date']);

    $path 			= PATH_UPLOADED_FILES.$sys_file_name;
    $path 			= str_replace(array('\\','/'), array(DS,DS), $path);

    $http_path	    = base_url().$path;
    $file_size 		= '';
    
    $dl_path 		= base_url().'pria_file/download?file='.$sys_file_name;
    $vw_path 		= base_url().'pria_file/view?file='.$sys_file_name;

    if (file_exists($path)) {
        $file_size         = file_size_convert(filesize($path));
        $file_size_num     = filesize($path);
    }

    $file_ext_end 	=  end($file_ext);

    $del_cont = '';
    $view_cont= '';
    $del_cont_link 	= '';
    $view_cont_link = '';

    $vc  = <<<EOS
			<button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="New Version" onclick="modal_file_version_init('$reference/$document_type/$document_id', 'File Version')" data-target="modal_file_version"><i class="material-icons valign-middle">add</i></button>
EOS;
    $vcl =  <<<EOS
				<a href="#modal_file_version" id="view" class="tooltipped" data-position="top" data-tooltip="New Version" onclick="modal_file_version_init('$reference/$document_type/$document_id', 'File Version')" data-delay='50' >New Version</a>
EOS;

    if ($has_approval) {
        $del_cont 		= '';
        $view_cont		= '';
        $new_version 	= '';
        $del_cont_link 	= '';
        $view_cont_link = '';
    } else {
            switch ($status_id) {
                case TASK_STATUS_ONGOING:
                    if ($file_details['created_by'] == $CI->session->user_id) {
                        $view_cont		= '';
                        $new_version 	= '';
                        
                        if($file_details['access'] == DOCUMENT_ACCESS_ADD)
                        {
                            if ($is_returned === false) {
                                $del_cont 		= <<<EOS
        						<button type="button" id="delete" class="tooltipped" data-position="top" data-tooltip="Delete" onclick="content_delete('attachment','$document_id')" class='tooltipped' data-tooltip='Delete' data-position='top' data-delay='50' ><i class="material-icons valign-middle">delete</i></button>
EOS;
                                $del_cont_link = <<<EOS
        						<a href="javascript:;" id="delete" class="tooltipped" data-position="top" data-tooltip="Delete" onclick="content_delete('attachment','$document_id')" data-delay='50' >Delete</a>
EOS;
                            } else {
                                $view_cont		= $vc;
                                $view_cont_link = $vcl;
                            }
                        }
                    }

                    break;
                case TASK_STATUS_RETURNED:
                    
                    if ($file_details['created_by'] == $CI->session->user_id)
                    {
                        if($file_details['access'] == DOCUMENT_ACCESS_ADD)
                        {
                            $view_cont		= <<<EOS
    				            <button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="New Version" onclick="modal_file_version_init('$reference/$document_type/$document_id', 'File Version')" data-target="modal_file_version"><i class="material-icons valign-middle">add</i></button>
EOS;
                    
                            $view_cont_link = <<<EOS
    						  <a href="#modal_file_version" id="view" class="tooltipped" data-position="top" data-tooltip="New Version" onclick="modal_file_version_init('$reference/$document_type/$document_id', 'File Version')" data-delay='50' >New Version</a>
EOS;
                            $new_version 	= '';

                            $del_cont 		= '';
                        }
                    }

                    break;
                
                default:
                    $del_cont = '';

                    if ($file_details['created_by'] == $CI->session->user_id) {
                        $view_cont = '';
                        // 				$view_cont = <<<EOS
                        // 				<button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="View" onclick="modal_file_version_init('$reference/$document_type/$document_id', 'File Version')" data-target="modal_file_version"><i class="material-icons valign-middle">add</i>New Version</button>
                        // EOS;
                    }
                    break;
        }
    }
    
    if ($status_id == TASK_STATUS_ONGOING OR $stand_alone) {
        if ($force_show_version) {
            $view_cont		= $vc;
            $view_cont_link = $vcl;
        }
    }

    //Template copied from upload_doc_dr.php
    $tpl 			= <<<EOS
		<div class="file-wrapper $file_ext_end">
			<div class="type" data-file-type="$file_ext_end"></div>
			<div class="contents valign-top">
				<div class="filename truncate">
					<span class="red-text"><b>v$version</b></span> | $file_name
				</div>
				uploaded $created_date by $created_by.  $file_size
				
				<div class="action-links">
					<a target="_blank" href="$http_path" >View</a>
					<a target="_blank" href="$http_path">Download</a>

					$view_cont_link

					$del_cont_link
				</div>
			</div>

			<div class="actions valign-middle right-align">
				
				<a target="_blank" href="$vw_path" >
					<button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="View"><i class="material-icons valign-middle ">search</i></button>
				</a>
				<a target="_blank" href="$dl_path" download>
					<button type="button" id="download" class="tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons valign-middle ">file_download</i> </button>
				</a>
				
				$view_cont

				$del_cont
			</div>
		</div>
EOS;

    if ($return) {
        return $tpl;
    } else {
        echo $tpl;
    }
}

function get_scope_details_mobile($module_code, $user_id='', $and_str=false)
{
    $CI =& get_instance();

    $CI->load->model('permissions_model', 'pm_model');
    $CI->load->model('pria_api_model', 'pria_api_model');
    
    $CI->load->model('user_management/users_model', 'u_model');

    $role_arr  = $CI->pria_api_model->get_users_role(['user_id' => $user_id], ['role_code']);
    $details   = $CI->pm_model->get_module_scopes_by_role_codes_arr($module_code, $role_arr);

    // Get the highest scope
    $scope 	 		= $details[0];
    $orgs 	 		= [];
    $having  		= '';
    $vendor_code 	= '';
    $having_keyword = ($and_str) ? "AND " : "HAVING ";

    switch ($scope) {
        case SCOPE_SYSTEM:
        break;
        
        case SCOPE_REGION:
            $user_orgs = $CI->u_model->get_user_orgs($user_id);

            $orgs 	   = [];
            foreach ($user_orgs as $uo) {
                $org_code 	= $uo['org_code'];
                $orgs[] 	= $org_code;

                $orgs 		= check_child_org($org_code, $orgs);
            }

            $orgs 		= array_unique($orgs);
            $orgs_str 	= implode('\',\'', $orgs);

            $having 	= $having_keyword."org_code IN ('$orgs_str')";
        break;

        // For vendor
        case SCOPE_AGENCY:
            $CI->load->model('tasks/task_model', 'tm_model');
            $CI->load->model('code_libraries/vendor_model', 'vendor_model');

            $vendor_dets = $CI->tm_model->get_vendor_user(['user_id' => $user_id], ['vendor_code']);

            if (empty($vendor_dets)) {
                throw new Exception('Wrong setup for scope!');
            }

            $vendor_code = $vendor_dets['vendor_code'];

            $bc_details  = $CI->vendor_model->get_vendor_business_centers(['vendor_code' => $vendor_code]);
                
            $having 	 = $having_keyword."vendor_code = '$vendor_code'";
            $orgs 		 = array_column($bc_details, 'org_code');
        break;
    }

    return [
        'scope'  		=> $scope,
        'orgs'   		=> $orgs,
        'having' 		=> $having,
        'vendor_code' 	=> $vendor_code
    ];
}

function get_scope_details($module_code, $user_id='', $and_str=false)
{
    $CI =& get_instance();

    $CI->load->model('permissions_model', 'pm_model');
    
    $CI->load->model('user_management/users_model', 'u_model');

    if (empty($user_id)) {
        $role_arr = $CI->session->userdata('user_roles');

        $user_id  = $CI->session->userdata('user_id');
    }
    //	print_var_export($module_code, $role_arr);
    $details   = $CI->pm_model->get_module_scopes_by_role_codes_arr($module_code, $role_arr);

    //Get the highest scope
    $scope 	 		= $details[0];
    $orgs 	 		= [];
    $having  		= '';
    $vendor_code 	= '';
    $having_keyword = ($and_str) ? "AND " : "HAVING ";
    //echo $scope; die;

    switch ($scope) {
        case SCOPE_SYSTEM:

        break;
        
        case SCOPE_REGION:
            $user_orgs = $CI->u_model->get_user_orgs($user_id);

            $orgs 	   = [];
            foreach ($user_orgs as $uo) {
                $org_code 	= $uo['org_code'];
                $orgs[] 	= $org_code;

                $orgs 		= check_child_org($org_code, $orgs);
            }

            $orgs 		= array_unique($orgs);
            $orgs_str 	= implode('\',\'', $orgs);

            $having 	= $having_keyword."org_code IN ('$orgs_str')";
        break;

        //For vendor
        case SCOPE_AGENCY:
            $CI->load->model('tasks/task_model', 'tm_model');
            $CI->load->model('code_libraries/vendor_model', 'vendor_model');

            $vendor_dets = $CI->tm_model->get_vendor_user(['user_id' => $user_id], ['vendor_code']);

            if (empty($vendor_dets)) {
                throw new Exception('Wrong setup for scope!');
            }

            $vendor_code = $vendor_dets['vendor_code'];

            $bc_details  = $CI->vendor_model->get_vendor_business_centers(['vendor_code' => $vendor_code]);
                
            $having 	 = $having_keyword."vendor_code = '$vendor_code'";
            $orgs 		 = array_column($bc_details, 'org_code');
        break;
    }

    return [
        'scope'  		=> $scope,
        'orgs'   		=> $orgs,
        'having' 		=> $having,
        'vendor_code' 	=> $vendor_code
    ];
}

//Specifically made to be a partner of get_scope()
function check_child_org($org_code, $orgs)
{
    $CI =& get_instance();
    
    $CI->load->model('tasks/task_model', 'tm_model');

    $org_dets    = $CI->tm_model->get_organizations(['org_parent' => $org_code], ['org_code']);
    
    foreach ($org_dets as $od) {
        $org_code = $od['org_code'];

        $orgs[]   = $org_code;

        $orgs 	  =  check_child_org($org_code, $orgs);
    }

    return $orgs;
}

/**
     * @Author: kevin villarojo
     * @Date: 2019-10-10 17:07:30
     * @Desc:
     * @Referenced: SOA.php, PO.php, Sites.php
     */
function get_organizations_by_org_type_w_scope($module, $org_type=ORG_TYPE_BUSINESS_CENTER)
{
    $CI =& get_instance();

    $CI->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');

    $scope_details   = get_scope_details($module);
    $org_codes       = ['org_type_code' => $org_type];

    if (! empty($scope_details['orgs'])) {
        $org_codes = ['org_code' => ['IN', $scope_details['orgs']], 'org_type_code' => ORG_TYPE_BUSINESS_CENTER];
    }
    
    return $CI->tm_model->get_organizations($org_codes, ['org_code', 'name']);
}



function dateDifference($date_1, $date_2, $differenceFormat = '%a')
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
   
    $interval = date_diff($datetime1, $datetime2);
   
    return $interval->format($differenceFormat);
}