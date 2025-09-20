<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| List of Useful Generic Functions
|--------------------------------------------------------------------------
*/

/*
|----------------------------------------------------------------------
| Get Parameters from $_GET, $_POST, and $_FILES Function
|----------------------------------------------------------------------
| Note: Because almost all method that connected to AJAX is using this
| funtion, XSS is auto enabled. XSS or Cross Site Scripting Hack
| prevention filter which can either run automatically to filter all
| POST and COOKIE data that is encountered, or you can run it on a per
| item basis
|
| @return array
*/
/* function get_params($xss = TRUE)
{
	$CI =& get_instance();

	$get = $CI->input->get(NULL, $xss) ? $CI->input->get(NULL, $xss) : array();
	$post = $CI->input->post(NULL, $xss) ? $CI->input->post(NULL, $xss) : array();
	$params = array_merge(array_map('_secure_param', array_merge($get, $post)), $_FILES);

	return $params;
} */

function get_params($xss = TRUE, $force_get = FALSE)
{

	$CI =& get_instance();
	$post = $get = $files = array();


	$post = $CI->input->post(NULL, $xss) ? $CI->input->post(NULL, $xss) : array();

	$post = array_map('_strip_tags', $post);

	if( $CI->input->is_ajax_request() OR $force_get )
	{

		$get = $CI->input->get(NULL, $xss) ? $CI->input->get(NULL, $xss) : array();

		$get = array_map('_strip_tags', $get);
	}


	if( ! empty($_FILES) )
		$files = $_FILES;


	$params = array_merge(array_map('_secure_param', array_merge($get, $post)), $files);

	if( empty($params) )
		throw new Exception('Invalid request');
	else
		return $params;
}

function _strip_tags($value)
{
	if (is_array($value)) {
		return array_map('_strip_tags', $value);
	} else {
		return strip_tags($value);
	}
}

/* Single Parameter */
function get_param($key, $xss = TRUE) {
	if (is_string($key) && !empty($key)) {
		$params = get_params($xss);
		return (array_key_exists($key, $params)) ? $params[$key] : '';
	}

	return FALSE;
}

function _secure_param($value)
{
	if (is_array($value)) {
		return array_map('_secure_param', $value);
	} else {
		return urldecode(trim($value));
	}
}


/*
|----------------------------------------------------------------------
| Get Settings Function
|----------------------------------------------------------------------
| Get a specific site setting detail
|
| @param string $type
| @param string $name
|
| @return string
*/

function get_setting($type, $name)
{
	$CI =& get_instance();
	$CI->load->model("settings_model");

	$setting = $CI->settings_model->get_specific_setting($type, $name);

	return $setting["setting_value"];
}

function get_settings($type, array $extra = array())
{
	$CI =& get_instance();
	$CI->load->model("settings_model");

	$order 				= array();

	if( !EMPTY( $extra ) )
	{
		if( ISSET( $extra['order'] ) )
		{
			$order 		= $extra['order'];
		}
	}

	$settings = $CI->settings_model->get_settings_value($type, $order);

	return $settings;
}


/*
|----------------------------------------------------------------------
| Generate Salt Function
|----------------------------------------------------------------------
| @param boolean $high_risk	: set to TRUE if the value being hashed
| 							  needs to be more secured (e.g. password)
|
| @return string
*/

function gen_salt($high_risk = FALSE) {
	if($high_risk){
		return hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
	} else {
		return substr(hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE)), 0, SALT_LENGTH);
	}
}


/*
|----------------------------------------------------------------------
| Generate Token Function
|----------------------------------------------------------------------
| @param string $id			: specifies the value of the id
| @param string $salt		: specifies the value of the salt
| @param boolean $high_risk	: set to TRUE if the value being hashed
| 							  needs to be more secured (e.g. password)
|
| @return string
*/

function in_salt($id, $salt, $high_risk = FALSE) {
	if($high_risk){
		return hash('sha512', sha1($id) . $salt);
	} else {
		return substr(hash('sha512', sha1($id) . $salt), 0, SALT_LENGTH);
	}
}


/*
|----------------------------------------------------------------------
| Check Salt Function
|----------------------------------------------------------------------
| Check if the token or salt were not maliciously changed
|
| @param string $id
| @param string $salt
| @param string $token
*/

function check_salt($id, $salt, $token, $action_id = NULL)
{
	$CI =& get_instance();

	$args 		= func_get_args();
	$cnt_args	= count( $args );

	if( $cnt_args > 4 )
	{
		$id_dec 	= base64_url_decode( $id );

		if( !EMPTY( $id_dec ) AND $id_dec != 0 )
		{
			$id 	= $id_dec;
		}

		if( ISSET( $action_id ) )
		{
			if(EMPTY($action_id) OR ( $id != 0 AND EMPTY($id) ) OR EMPTY($salt) OR EMPTY($token))
			{
				throw new Exception($CI->lang->line('invalid_action'));
			}

			unset( $args[0] );
			unset( $args[1] );
			unset( $args[2] );
			unset( $args[3] );

			$id_concat 	= $id.'/'.$action_id;
			$id_concat 	.= '/';

			foreach( $args as $a )
			{
				$id_concat .= $a.'/';
			}

			$id_concat 			= rtrim($id_concat, '/');

			if($token != in_salt($id_concat, $salt))
			{
				throw new Exception($CI->lang->line('invalid_action'));
			}
		}
	}
	else
	{
		if( ISSET( $action_id ) )
		{
			if(EMPTY($action_id) OR EMPTY($id) OR EMPTY($salt) OR EMPTY($token))
				throw new Exception($CI->lang->line('invalid_action'));

			if($token != in_salt($id . '/' . $action_id, $salt))
				throw new Exception($CI->lang->line('invalid_action'));
		}
		else
		{
			 if($token != in_salt($id, $salt))
			{
				throw new Exception($CI->lang->line('invalid_action'));
			}
		}
	}

}

/*
|----------------------------------------------------------------------
| URL Encode Function
|----------------------------------------------------------------------
| Encodes a string to safely pass values in the URL
|
| @param string $input
|
| return string
*/

function base64_url_config($CI)
{
	$key 	= $CI->config->item('encryption_key');

	return array(
		'raw_data' 		=> FALSE,
		'hmac_key' 		=> $key,
		'hmac_digest' 	=> 'sha224',
		'cipher' 		=> 'aes-128',
		'mode' 			=> 'ecb',
		'key' 			=> $key
	);
}

function base64_url_encode($input)
{
	$CI 	=& get_instance();

	$config = base64_url_config($CI);

	$cipher = $CI->encryption->encrypt($input, $config);

	return strtr($cipher, '+/=', '.~-');
}


/*
|----------------------------------------------------------------------
| URL Decode Function
|----------------------------------------------------------------------
| Decodes any encoded values in the given string
|
| @param string $input
|
| return string
*/

function base64_url_decode($input)
{
	$CI 	=& get_instance();

	$config = base64_url_config($CI);

	return $CI->encryption->decrypt(strtr($input, '.~-', '+/='), $config);
}


/*
|----------------------------------------------------------------------
| Get Values Function
|----------------------------------------------------------------------
| Function for queries that needs dependency. This will run the query
| from a particular model without the need of a controller
|
| @param string $model		: name of the model where the query is
| 							  needed to be executed
| @param string $function	: name of the function located in the
| 							  specified model
| @param array $params		: array of values needed by the query for
| 							  dependency
| @param string $module		: name of the project where the function is
| 							  located
|
| @return array
*/

function get_values($model, $function, $params = array(), $module = NULL)
{
	$mod = (!IS_NULL($module))? $module."/".$model : $model;
	$CI =& get_instance();

	if(!$CI->load->is_model_loaded($mod))
		$CI->load->model($mod);

	try{

		$values = $CI->$model->$function($params);

		return $values;
	}
	catch(PDOException $e)
	{
		throw new PDOException($e->getMessage());
	}
	catch(Exception $e)
	{
		throw new Exception($e->getMessage());
	}
}


/*
|----------------------------------------------------------------------
| Time Ago Function
|----------------------------------------------------------------------
| Used for comments and other form of communication to tell the time
| in seconds/minutes/hours/days/months/years/decades ago instead of the
| exact time which might not be correct to some in another time zone
|
| @param datetime $time		: specifies the date/time to be converted
| @param int $ago			: set to 1 to append "ago" in the returned
| 							  value
| @param int $period_only	: set to 1 to exclude date in the returned
| 							  value
|
| @return string
*/

function get_date_format($time, $ago = 0, $period_only = 0)
{
	$date = $prefix = $suffix = "";
	$convert_time = strtotime($time);
	$month_total_days = date("t");
	$periods = array("second", "minute", "hour", "day", "month", "year", "decade");
	$lengths = array("60","60","24",$month_total_days,"12","10");

	$now = time();

	IF($ago){
	   $difference = $now - $convert_time;
	   $suffix = " ago";
	}else{
	   $difference = $convert_time - $now;
	   $prefix = "In ";
	}

	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	   $difference /= $lengths[$j];
	}

	$difference = floor($difference);

	if($difference > 1)
		$periods[$j].= "s";

	switch($periods[$j]){
		/* IF CURRENT PERIOD IS DAY */
		case $periods[3]:
			if($difference > 7)
				$date = date(', D M jS', strtotime($time));
		break;

		/* IF CURRENT PERIOD IS MONTH */
		case $periods[4]:
			$date = date(', D M jS', strtotime($time));
			$difference = ($difference == 1) ? "a" : $difference;
		break;
	}

	$time_ago = ($difference == 0 && is_numeric($difference)) ? "Just Now" : $prefix . $difference . ' ' . $periods[$j] . $suffix;
	$time_ago = ($period_only) ? $time_ago : $time_ago . $date;

	return $time_ago;
}


/*
|----------------------------------------------------------------------
| Generate Years Dropdown Function
|----------------------------------------------------------------------
| @Create dropdown of years
|
| @param int $start_year	: specifies the year when the list will start
| @param int $end_year		: specifies the year when the list will end
| @param string $id			: the name and id of the select object
| @param int $selected		: the value to be selected from the dropdown
|
| @return string
*/

function create_years($start_year, $end_year, $id = 'year_select', $selected = NULL)
{

	/* CURRENT YEAR */
	$selected = is_null($selected) ? date('Y') : $selected;

	/* RANGE OF YEARS */
	$r = range($end_year, $start_year);

	/* CREATE SELECT OBJECT */
	$select = '<select name="'.$id.'" id="'.$id.'" class="selectize">';
	foreach( $r as $year )
	{
		$select .= '<option value="'.$year.'"';
		$select .= ($year==$selected) ? ' selected="selected"' : '';
		$select .= '>'.$year.'</option>\n';
	}
	$select .= '</select>';

	return $select;
}


/*
|----------------------------------------------------------------------
| Generate Months Dropdown Function
|----------------------------------------------------------------------
| @Create dropdown list of months
|
| @param string $id		: the name and id of the select object
| @param int $selected	: the value to be selected from the dropdown
| @param boolean $all	: set to FALSE to remove 'ALL' option from the
| 						  dropdown list
|
| @return string
*/

function create_months($id = 'month_select', $selected = NULL, $all = TRUE)
{
	/* ARRAY OF MONTHS */
	$months = array(
			1=>'January',
			2=>'February',
			3=>'March',
			4=>'April',
			5=>'May',
			6=>'June',
			7=>'July',
			8=>'August',
			9=>'September',
			10=>'October',
			11=>'November',
			12=>'December');

	/*** current month ***/
	$selected = is_null($selected) ? date('m') : $selected;

	$select = '<select name="'.$id.'" id="'.$id.'" class="selectize">';

	if($all)
		$select .= '<option value="0">All</option>\n';

	foreach($months as $key=>$mon)
	{
		$select .= "<option value=\"$key\"";
		$select .= ($key==$selected) ? ' selected="selected"' : '';
		$select .= ">$mon</option>\n";
	}
	$select .= '</select>';
	return $select;
}


/*
 |----------------------------------------------------------------------
 | Create Dropdown List of Days
 |----------------------------------------------------------------------
 | @Create dropdown list of days
 |
 | @param string $id The name and id of the select object
 |
 | @param int $selected
 |
 | @return string
 */

function create_days($id='day_select', $selected=null)
{
	/*** range of days ***/
	$r = range(1, 31);

	/*** current day ***/
	$selected = is_null($selected) ? date('d') : $selected;

	$select = "<select name=\"$id\" id=\"$id\" class='selectize'>\n";
	foreach ($r as $day)
	{
		$select .= "<option value=\"$day\"";
		$select .= ($day==$selected) ? ' selected="selected"' : '';
		$select .= ">$day</option>\n";
	}
	$select .= '</select>';
	return $select;
}


/*
 |----------------------------------------------------------------------
 | To have a universal way of parse json data
 |----------------------------------------------------------------------
 | Para pwedeng imanipulate yung output in case kailangan
 |
 */

function parse_json($params)
{
	echo json_encode(format_output($params));
}


function format_output($params)
{
	if(is_array($params))
		return array_map('format_output', $params);
	else
		return utf8_encode($params);
}

/*
|----------------------------------------------------------------------
| File Size Convert
|----------------------------------------------------------------------
| Converts bytes into human readable file size
|
| @param string $bytes
|
| @return string human readable file size (2,87 Мб)
*/

function file_size_convert($bytes)
{
	$result = '';
	$bytes 	= floatval($bytes);
		$arr_bytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);

	foreach($arr_bytes as $arr_item)
	{
		if($bytes >= $arr_item["VALUE"])
		{
			$result = $bytes / $arr_item["VALUE"];
			$result = str_replace(".", "." , strval(round($result, 2)))." ".$arr_item["UNIT"];
			break;
		}
	}
	return $result;
}

function get_pass_error_msg()
{
	$CI =& get_instance();

	if(!$CI->load->is_model_loaded('settings_model'))
		$CI->load->model('settings_model');

	return $CI->settings_model->get_pass_error_msg();

}


/*
|----------------------------------------------------------------------
| Relative Date
|----------------------------------------------------------------------
| Return a string with a date relative to today
|
| @param datetime strtotime($time)
|
| @return string
*/

function relative_date($time) {

	$today		= strtotime(date('M j, Y'));
	$reldays	= ($time - $today)/86400;

	if ($reldays >= 0 && $reldays < 1) {
		return 'Today';
	} else if ($reldays >= 1 && $reldays < 2) {
		return 'Tomorrow';
	} else if ($reldays >= -1 && $reldays < 0) {
		return 'Yesterday';
	}

	if (abs($reldays) < 7) {
		if ($reldays > 0) {
			$reldays = floor($reldays);
			return 'In ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
		} else {
			$reldays = abs(floor($reldays));
			return $reldays . ' day' . ($reldays != 1 ? 's' : '') . ' ago';
		}
	}

	if (abs($reldays) < 182) {
		//return date('l, j F',$time ? $time : time());
		return date('l',$time ? $time : time());
	} else {
		//return date('l, j F, Y',$time ? $time : time());
		return date('l',$time ? $time : time());
	}
}


/*
|----------------------------------------------------------------------
| CONVERT TO ROMAN
|----------------------------------------------------------------------
| Converts Number to Roman Numerals
|
| @param int
|
| @return string
*/

function convert_to_roman($integer, $upcase = true)
{
    $table = array(
		'M'=>1000,
		'CM'=>900,
		'D'=>500,
		'CD'=>400,
		'C'=>100,
		'XC'=>90,
		'L'=>50,
		'XL'=>40,
		'X'=>10,
		'IX'=>9,
		'V'=>5,
		'IV'=>4,
		'I'=>1
	);

    $return = '';
    while($integer > 0)
    {
        foreach($table as $rom=>$arb)
        {
            if($integer >= $arb)
            {
                $integer -= $arb;
                $return .= $rom;
                break;
            }
        }
    }

	$return = ($upcase) ? $return : strtolower($return);

    return $return;
}


/*
|----------------------------------------------------------------------
| Generation of Breadcrumbs
|----------------------------------------------------------------------
| Getters & setters of breadcrumbs
|
*/

function set_breadcrumbs($breadcrumbs, $overwrite=FALSE)
{
	$CI =& get_instance();
	//LENGTH OF PASSED BREADCRUMBS
	$index_length = COUNT($breadcrumbs) - 1; //2
	//GET ALL THE KEYS
	$array_keys	= array_keys($breadcrumbs); //0 = view 1 = case
	//GET THE KEY OF THE LAST BREADCRUMB ARRAY
	$last_array_key	= $array_keys[$index_length]; //[CASE TITLE]
	//GET THE CURRENT BREADCRUMB IN SESSION
	$current_breadcrumbs = $CI->session->breadcrumbs;
	$new_breadcrumbs = array();

	if($overwrite):
		$new_breadcrumbs = $breadcrumbs;
	else:
		if(array_key_exists($last_array_key, $current_breadcrumbs)):

			foreach($current_breadcrumbs as $name => $link):

				$new_breadcrumbs[$name] = $link;

				if($last_array_key == $name) break;

			endforeach;

		else:

			$new_breadcrumbs = array_merge($current_breadcrumbs, $breadcrumbs);
		endif;
	endif;

	$CI->session->set_userdata('breadcrumbs', $new_breadcrumbs);
}

function get_breadcrumbs($home = CORE_HOME_PAGE)
{
	$CI =& get_instance();
	$base_url = base_url();

	$html = <<<EOH
				<ul class="list-breadcrumbs"><li><a href="{$base_url}{$home}">Home</a></li>
EOH;

	$breadcrumbs = $CI->session->breadcrumbs;
	$count	= count($breadcrumbs);

	$x = 1;

	if(!EMPTY($breadcrumbs)):
		foreach($breadcrumbs AS $key => $val):
			$onlick  = ( ! EMPTY($val) && ($count > $x) && $val !="#") ?  $base_url.$val : '#';
			$class = ( ! EMPTY($val) && ($count > $x)) ? '' : 'active';
			$html .= <<<EOH
						<li><a href="{$onlick}" class = "{$class}">{$key}</a></li>
EOH;
			$x++;
		endforeach;
	endif;
			$html .= <<<EOH
				</ul>
EOH;

	echo $html;
}

/*
|----------------------------------------------------------------------
| Generation of systems dropdown
|----------------------------------------------------------------------
| Choose a specific system from an integrated platform
|
*/

function get_systems()
{
	$CI =& get_instance();
	$CI->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);

	$systems = $CI->systems->get_systems();
	$options = "";

	if(count($systems) > 1)
	{
		$curr_sys_logo = $CI->session->current_system_logo;

		$options .= '<div class="app-logo">';

		if( !EMPTY( $curr_sys_logo ) )
		{
			$options .= '<img id="system_logo_menu" src="'.$curr_sys_logo.'" />';
		}

		$options .= '</div><div class="input-field col s12">';

		$options .= '<select id="app-selector" name="app-selector" onchange="set_system(this.value);">';
		$options .= get_system_options();

		$options .= '</select></div>';
	}

	echo $options;
}

function get_system_options()
{
	$CI =& get_instance();

	$CI->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);

	$options 	  = "";
	$user_systems = $CI->session->user_systems;
	$systems 	  = $CI->systems->get_systems();

	if( !EMPTY( $user_systems ) )
	{
		$root_path 	= get_root_path();

		foreach ($systems as $system):
			// CHECK IF USER HAS ACCESS TO THE SYSTEM
			if(in_array($system['system_code'], $user_systems))
			{
				$img_src 		= base_url().PATH_SYSTEMS_UPLOADS . "default_logo.jpg";

				if( !EMPTY( $system["logo"] ) )
				{

					$photo_path = $root_path.PATH_SYSTEMS_UPLOADS.$system["logo"];
					$photo_path = str_replace(array('\\','/'), array(DS,DS), $photo_path);

					if( file_exists( $photo_path ) )
					{
						$img_src = output_image($system["logo"], PATH_SYSTEMS_UPLOADS);
					}
				}

				/*$img_src = (@getimagesize(base_url() . PATH_SYSTEMS_UPLOADS . $system["logo"])) ? PATH_SYSTEMS_UPLOADS . $system["logo"] : PATH_SYSTEMS_UPLOADS . "default_logo.png";*/

				$selected = ($CI->session->current_system == $system["system_code"])? "selected" : "";
				$options .= "<option value='".$system["system_code"]."' data-icon='".$img_src."' class='left circle' ".$selected.">".$system["system_code"]."</option>";
			}
		endforeach;
	}

	return $options;
}

function get_organizations()
{
	$CI =& get_instance();

	$CI->load->model(CORE_USER_MANAGEMENT.'/Organizations_model', 'org_mod', TRUE);

	$user_orgs = $CI->org_mod->get_org_details($CI->session->org_code);
	$user_orgs = array($user_orgs);

	if(isset($user_orgs) && ! empty($user_orgs))
	{
		$html = '<div class="cd-side-app">';
		//$html .= 	'<div class="app-logo">';
		//$html .= 		'<img id="system_logo_menu" />';
		//$html .= 	'</div>';
		$html .= 	'<div class="input-field p-n m-n p-t-md"><span>You are currently viewing</span>';
		$html .= 		'<select id="org-selector" name="org-selector">';
		$html .= get_organization_options();
		$html .= 		'</select>';
		$html .= 	'</div>';
		$html .= '</div>';

		return $html;
	}
}

function get_organization_options()
{
	$CI =& get_instance();
	$CI->load->model(CORE_USER_MANAGEMENT.'/Organizations_model', 'org_mod', TRUE);

	$user_orgs = $CI->org_mod->get_org_details($CI->session->org_code);
	$user_orgs = array($user_orgs);

	$html 	= "";

	foreach($user_orgs AS $org)
	{
		$selected = (strtolower($CI->session->org_code) == strtolower($org["org_code"]) ? 'selected': '');
		$html.= "<option value='".$org["org_code"]."' class='left circle' ".$selected.">".$org["short_name"]."</option>";
	}

	return $html;
}

/*
|----------------------------------------------------------------------
| Generation of Menu
|----------------------------------------------------------------------
| Gets modules from database and generates menu
|
*/

function get_menu($system_code)
{
	try
	{
		$CI =& get_instance();
		$params = array();

		$menu = "<ul class=''>";

		$CI->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);

		$sys_param = get_sys_param_code(SYS_PARAM_MODULE_LOCATION, "LEFT");

		$exempt_modules = unserialize(EXEMPT_CORE_MODULES);

		// PARENT MODULES WITH GROUPING
		$where_header = array("parent_module" => "IS NULL", "group_header_flag" => 1, "module_location" => $sys_param["sys_param_code"], "enabled_flag" => 1, "hide_flag" => 0);
		$modules_header = $CI->permissions_model->get_modules(NULL, $system_code, NULL, $where_header);

		foreach($modules_header as $module_header):

			if( !EMPTY( $exempt_modules ) AND in_array($module_header['module_code'], $exempt_modules) )
			{
				continue;
			}

			$where = array("parent_module" => $module_header["module_code"], "group_header_flag" => 1, "module_location" => $sys_param["sys_param_code"], "enabled_flag" => 1, "hide_flag" => 0);
			$modules = $CI->permissions_model->get_modules(NULL, $system_code, NULL, $where);

			$menu .= "<li class='cd-label'>".$module_header["module_name"]."</li>";

			foreach($modules as $module):

				if( !EMPTY( $exempt_modules ) AND in_array($module['module_code'], $exempt_modules) )
				{
					continue;
				}

				$children = $CI->permissions_model->get_modules(NULL, $system_code, $module["module_code"], array("module_location" => $sys_param["sys_param_code"]));
				$has_children = (!EMPTY($children))? "has-children" : "";
				$link = (!EMPTY($module["link"]))? "href='".base_url().$module["link"]."'" : "";
				$selected = ($CI->session->active_module == $module["module_code"]) || ($CI->session->active_parent_module == $module["module_code"]) ? "selected" : "";

				$menu .= "<li class='".strtolower($module['module_code'])." ".$has_children." ".$selected." menu-item' id='".$module['module_code']."'>
							<a ".$link."><i class='material-icons'>".$module["icon"]."</i> ".$module["module_name"]."</a>";

							if(!EMPTY($children)){
								$menu .= "<ul>";
									foreach($children as $child):
										if( !EMPTY( $exempt_modules ) AND in_array($child['module_code'], $exempt_modules) )
										{
											continue;
										}

										if( EMPTY( $child['hide_flag'] ) )
										{
											$selected_child = ($CI->session->active_module == $child["module_code"]) ? "selected" : "";

											$menu .= "<li class='".$selected_child." menu-item child' id='".$child["module_code"]."'><a href='".base_url().$child["link"]."'>".$child["module_name"]."</a></li>";
										}
									endforeach;
								$menu .= "</ul>";
							}

				$menu .= "</li>";

			endforeach;

		endforeach;

		// PARENT MODULES WITHOUT GROUPING
		$where = array("parent_module" => "IS NULL", "group_header_flag" => 0, "module_location" => $sys_param["sys_param_code"], "enabled_flag" => 1, "hide_flag" => 0);
		$modules = $CI->permissions_model->get_modules(NULL, $system_code, NULL, $where);

		foreach($modules as $module):

			if( !EMPTY( $exempt_modules ) AND in_array($module['module_code'], $exempt_modules) )
			{
				continue;
			}

			$children = $CI->permissions_model->get_modules(NULL, $system_code, $module["module_code"]);
			$has_children = (!EMPTY($children))? "has-children" : "";
			$link = (!EMPTY($module["link"]))? "href='".base_url().$module["link"]."'" : "";
			$has_parent_access = $CI->permission->check_permission($module["module_code"]);
			if($has_parent_access === TRUE)
			{
				$menu .= "<li class='".strtolower($module['module_code'])." ".$has_children."'>
							<a ".$link."><i class='material-icons'>".$module["icon"]."</i> ".$module["module_name"]."</a>";

							if(!EMPTY($children)){
								$menu .= "<ul>";
									foreach($children as $child):

										if( !EMPTY( $exempt_modules ) AND in_array($child['module_code'], $exempt_modules) )
										{
											continue;
										}

										$has_child_access = $CI->permission->check_permission($child["module_code"]);
										if( EMPTY( $child['hide_flag'] ) )
										{
											if($has_child_access === TRUE)
											{
												$menu .= "<li><a href='".base_url().$child["link"]."'>".$child["module_name"]."</a></li>";
											}
										}
									endforeach;
								$menu .= "</ul>";
							}

				$menu .= "</li>";
			}

		endforeach;

		$menu .= "</ul>";

		echo $menu;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		die;
	}
}
function get_sys_param_code($sys_param_type, $sys_param_value)
{
	$CI =& get_instance();
	$params = array();

	$CI->load->model(CORE_COMMON.'/sys_param_model', 'sys_param', TRUE);

	$params["fields"] = array("sys_param_code");
	$params["where"] = array("sys_param_type" => $sys_param_type, "sys_param_value" => $sys_param_value);
	$params["multiple"] = FALSE;
	$sys_param = $CI->sys_param->get_sys_param($params);

	return $sys_param;
}

function get_sys_param_val($sys_param_type, $sys_param_code)
{
	$CI =& get_instance();
	$params = array();

	$CI->load->model(CORE_COMMON.'/sys_param_model', 'sys_param', TRUE);

	$params["fields"] = array("sys_param_value");
	$params["where"] = array("sys_param_type" => $sys_param_type, "sys_param_code" => $sys_param_code);
	$params["multiple"] = FALSE;
	$sys_param = $CI->sys_param->get_sys_param($params);

	return $sys_param;
}

function convert_ucwords($name)
{
	return mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
}

function convert_strtolower($name)
{
	return mb_strtolower($name, "UTF-8");
}

function convert_strtoupper($name)
{
	return mb_strtolower($name, "UTF-8");
}

function number_to_words($number)
{
    $integer = (int) $number;
	$num = number_format($number, 2, ".", ",");
	$fraction = substr(strrchr($num, "."), 1);

    $output = "";

    if ($integer{0} == "-")
    {
        $output = "negative ";
        $integer    = ltrim($integer, "-");
    }
    else if ($integer{0} == "+")
    {
        $output = "positive ";
        $integer    = ltrim($integer, "+");
    }

    if ($integer{0} == "0")
    {
        $output .= "Zero";
    }
    else
    {
        $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
        $group   = rtrim(chunk_split($integer, 3, " "), " ");
        $groups  = explode(" ", $group);

        $groups2 = array();
        foreach ($groups as $g)
        {
            $groups2[] = convertThreeDigit($g{0}, $g{1}, $g{2});
        }

        for ($z = 0; $z < count($groups2); $z++)
        {
            if ($groups2[$z] != "")
            {
                $output .= $groups2[$z] . convertGroup(11 - $z) . (
                        $z < 11
                        && !array_search('', array_slice($groups2, $z + 1, -1))
                        && $groups2[11] != ''
                        && $groups[11]{0} == '0'
                            ? " and "
                            : ", "
                    );
            }
        }

        $output = rtrim($output, ", ");
    }

    if ($fraction > 0)
    {
		$fraction = rtrim(chunk_split($fraction, 1, " "), " ");
        $fraction = explode(" ", $fraction);
		$fraction = convertTwoDigit($fraction[0], $fraction[1]);
		return $output .= ' Pesos and '.$fraction.' Centavos only';

    } else {

		return $output. ' Pesos only';

	}
}

function convertGroup($index)
{
    switch ($index)
    {
        case 11:
            return " Decillion";
        case 10:
            return " Nonillion";
        case 9:
            return " Octillion";
        case 8:
            return " Septillion";
        case 7:
            return " Sextillion";
        case 6:
            return " Quintrillion";
        case 5:
            return " Quadrillion";
        case 4:
            return " Trillion";
        case 3:
            return " Billion";
        case 2:
            return " Million";
        case 1:
            return " Thousand";
        case 0:
            return "";
    }
}

function convertThreeDigit($digit1, $digit2, $digit3)
{
    $buffer = "";

    if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
    {
        return "";
    }

    if ($digit1 != "0")
    {
        $buffer .= convertDigit($digit1) . " Hundred";
        if ($digit2 != "0" || $digit3 != "0")
        {
            $buffer .= " and ";
        }
    }

    if ($digit2 != "0")
    {
        $buffer .= convertTwoDigit($digit2, $digit3);
    }
    else if ($digit3 != "0")
    {
        $buffer .= convertDigit($digit3);
    }

    return $buffer;
}

function convertTwoDigit($digit1, $digit2)
{
    if ($digit2 == "0")
    {
        switch ($digit1)
        {
            case "1":
                return "Ten";
            case "2":
                return "Twenty";
            case "3":
                return "Thirty";
            case "4":
                return "Forty";
            case "5":
                return "Fifty";
            case "6":
                return "Sixty";
            case "7":
                return "Seventy";
            case "8":
                return "Eighty";
            case "9":
                return "Ninety";
        }
    } else if ($digit1 == "1")
    {
        switch ($digit2)
        {
            case "1":
                return "Eleven";
            case "2":
                return "Twelve";
            case "3":
                return "Thirteen";
            case "4":
                return "Fourteen";
            case "5":
                return "Fifteen";
            case "6":
                return "Sixteen";
            case "7":
                return "Seventeen";
            case "8":
                return "Eighteen";
            case "9":
                return "Nineteen";
        }
    } else
    {
        $temp = convertDigit($digit2);
        switch ($digit1)
        {
            case "2":
                return "Twenty-$temp";
            case "3":
                return "Thirty-$temp";
            case "4":
                return "Forty-$temp";
            case "5":
                return "Fifty-$temp";
            case "6":
                return "Sixty-$temp";
            case "7":
                return "Seventy-$temp";
            case "8":
                return "Eighty-$temp";
            case "9":
                return "Ninety-$temp";
        }
    }
}

function convertDigit($digit)
{
    switch ($digit)
    {
        case "0":
            return "Zero";
        case "1":
            return "One";
        case "2":
            return "Two";
        case "3":
            return "Three";
        case "4":
            return "Four";
        case "5":
            return "Five";
        case "6":
            return "Six";
        case "7":
            return "Seven";
        case "8":
            return "Eight";
        case "9":
            return "Nine";
    }
}

function limit_string( $str, $maxlen = 100, $append =' ...' ,$allowed = NULL )
{

	$str 	= strip_tags( html_entity_decode( $str ) , $allowed);

    if ( strlen( $str ) <= $maxlen ) return $str;

    $newstr = substr( $str, 0, $maxlen );

    if ( substr( $newstr, -1, 1 ) != ' ' )
    {
    	if( !EMPTY( strrpos( $newstr, " " ) ) )
    	{
    		$newstr = substr( $newstr, 0, strrpos( $newstr, " " ) );
    	}

    }

    return $newstr.$append;
}

function findTimeAgo($past, $now = "now")
{
    // sets the default timezone if required
    // list of supported timezone identifiers
    // http://php.net/manual/en/timezones.php
    // date_default_timezone_set("Asia/Calcutta");
    $secondsPerMinute = 60;
    $secondsPerHour = 3600;
    $secondsPerDay = 86400;
    $secondsPerMonth = 2592000;
    $secondsPerYear = 31104000;
    // finds the past in datetime
    $past = strtotime($past);
    // finds the current datetime
    $now = strtotime($now);

    // creates the "time ago" string. This always starts with an "about..."
    $timeAgo = "";

    // finds the time difference
    $timeDifference = $now - $past;

    // less than 29secs
    if($timeDifference <= 29) {
      $timeAgo = "a few seconds";
    }
    // more than 29secs and less than 1min29secss
    else if($timeDifference > 29 && $timeDifference <= 89) {
      $timeAgo = "1 minute";
    }
    // between 1min30secs and 44mins29secs
    else if($timeDifference > 89 &&
      $timeDifference <= (($secondsPerMinute * 44) + 29)
    ) {
      $minutes = floor($timeDifference / $secondsPerMinute);
      $timeAgo = $minutes." minutes";
    }
    // between 44mins30secs and 1hour29mins29secs
    else if(
      $timeDifference > (($secondsPerMinute * 44) + 29)
      &&
      $timeDifference < (($secondsPerMinute * 89) + 29)
    ) {
      $timeAgo = "about 1 hour";
    }
    // between 1hour29mins30secs and 23hours59mins29secs
    else if(
      $timeDifference > (
        ($secondsPerMinute * 89) +
        29
      )
      &&
      $timeDifference <= (
        ($secondsPerHour * 23) +
        ($secondsPerMinute * 59) +
        29
      )
    ) {
      $hours = floor($timeDifference / $secondsPerHour);
      $timeAgo = $hours." hours";
    }
    // between 23hours59mins30secs and 47hours59mins29secs
    else if(
      $timeDifference > (
        ($secondsPerHour * 23) +
        ($secondsPerMinute * 59) +
        29
      )
      &&
      $timeDifference <= (
        ($secondsPerHour * 47) +
        ($secondsPerMinute * 59) +
        29
      )
    ) {
      $timeAgo = "1 day";
    }
    // between 47hours59mins30secs and 29days23hours59mins29secs
    else if(
      $timeDifference > (
        ($secondsPerHour * 47) +
        ($secondsPerMinute * 59) +
        29
      )
      &&
      $timeDifference <= (
        ($secondsPerDay * 29) +
        ($secondsPerHour * 23) +
        ($secondsPerMinute * 59) +
        29
      )
    ) {
      $days = floor($timeDifference / $secondsPerDay);
      $timeAgo = $days." days";
    }
    // between 29days23hours59mins30secs and 59days23hours59mins29secs
    else if(
      $timeDifference > (
        ($secondsPerDay * 29) +
        ($secondsPerHour * 23) +
        ($secondsPerMinute * 59) +
        29
      )
      &&
      $timeDifference <= (
        ($secondsPerDay * 59) +
        ($secondsPerHour * 23) +
        ($secondsPerMinute * 59) +
        29
      )
    ) {
      $timeAgo = "about 1 month";
    }
    // between 59days23hours59mins30secs and 1year (minus 1sec)
    else if(
      $timeDifference > (
        ($secondsPerDay * 59) +
        ($secondsPerHour * 23) +
        ($secondsPerMinute * 59) +
        29
      )
      &&
      $timeDifference < $secondsPerYear
    ) {
      $months = round($timeDifference / $secondsPerMonth);
      // if months is 1, then set it to 2, because we are "past" 1 month
      if($months == 1) {
        $months = 2;
      }

      $timeAgo = $months." months";
    }
    // between 1year and 2years (minus 1sec)
    else if(
      $timeDifference >= $secondsPerYear
      &&
      $timeDifference < ($secondsPerYear * 2)
    )
    {
		$timeAgo = "about 1 year";
    }
    // 2years or more
    else
    {
		$years = floor($timeDifference / $secondsPerYear);
		$timeAgo = "over ".$years." years";
    }

    return $timeAgo." ago";
 }

 /**
 * Use this helper function to replace the all {replace} in a string usually use for notification
 * so that it will be more readable to the devs what is the whole message.
 * i.e. $document_year = 2017, $document_type_name = 'Test' $org_name = 'Test'
 * 		$message_details = array( 'document_year' => 2017, 'document_type_name' => 'test', 'org_name' => 'Test' )
 * i.e. $message = {document_year} {document_type_name} of {org_name}.
 * The  result will be 2017 test of Test.
 *
 * @param  $message_details - required. key value of what will be the value of the replacements
 * @param  $message - required. Message
 * @return string
 */
function construct_message(array $message_details, $message)
{
	$message_str 			= '';

	try
	{
		foreach( $message_details as $variable => $value )
		{
			$$variable 		= $value;
		}

		$check_match 	= preg_match_all('/\{(.*?)\}/', $message, $match);

		if( !EMPTY( $check_match ) AND !EMPTY( $match ) )
		{
			if( ISSET( $match[1] ) )
			{
				$str_rep_arr 		= array();
				$str_find_arr 		= array();

				foreach( $match[1] as $key => $place_name )
				{
					$str_find_arr[] = '{'.$place_name.'}';
					$str_rep_arr[] 	= $$place_name;
				}

				$message_str 		= str_replace($str_find_arr, $str_rep_arr, $message);
			}
		}
	}
	catch( PDOException $e )
	{
		throw $e;
	}
	catch( Exception $e )
	{
		throw $e;
	}

	return $message_str;
}


function generate_password($length = 8)
{
	$chars 	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . '0123456789';

	$str 	= '';
	$max 	= strlen($chars) - 1;

	for ($i=0; $i < $length; $i++)
	{
    	$str .= $chars[mt_rand(0, $max)];
    }

	return $str;
}

function convertNumberToWord($num = false)
{
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}

function flattened_array( array $arr )
{

	$flat_arr 			= iterator_to_array(

		new RecursiveIteratorIterator(

			new RecursiveArrayIterator( $arr )

		), false

	);

	return $flat_arr;

}

function convert_ordinal($number)
{
    $ends 	= array('th','st','nd','rd','th','th','th','th','th','th');

    if ( (($number % 100) >= 11) AND (($number%100) <= 13) )
    {
        return $number. 'th';
    }
    else
    {
        return $number. $ends[$number % 10];
    }
}

/**
 * Use this helper function to get the correct base path or root path where the uploaded file will be stored
 *
 * @return string
 */
function get_root_path()
{
	$path 					= FCPATH;

	try
	{
		$change_upload_path	= get_setting(MEDIA_SETTINGS, "change_upload_path");

		if( !EMPTY( $change_upload_path ) )
		{
			$check_new_path 	= get_setting(MEDIA_SETTINGS, "new_upload_path");

			if( !EMPTY( $check_new_path ) )
			{
				$path 			= $check_new_path;

				$path 			= rtrim($path, '/');
				$path  			= rtrim($path, '\\');

				$path 			= $path.DS;
			}
		}
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

function output_image($file, $path)
{
	$CI =& get_instance();
	$CI->load->helper('file');

	$root_path 	= get_root_path();

	$new_path 	= $root_path.$path.$file;
	$new_path 	= str_replace(array('/', '\\'), array(DS, DS), $new_path);

	$mime 		= get_mime_by_extension($new_path);

	$check_upl 	= check_custom_path();

	if( file_exists( $path ) )
	{
		if( !EMPTY( $check_upl ) )
		{

			$image 		= base64_encode( file_get_contents( $new_path ) );

			$src 		= "data:$mime;base64,$image";
		}
		else
		{
			$src 		= base_url().$path.$file;
		}
	}
	else
	{
		$src 		= "";
	}

	return 	$src;
}

function check_custom_path()
{
	$checked_upload_path 	= false;

	try
	{

		$change_upload_path     = get_setting(MEDIA_SETTINGS, "change_upload_path");

		$checked_upload_path    = ( !EMPTY( $change_upload_path ) ) ? true : false;
	}
	catch( PDOException $e )
	{
		throw $e;
	}
	catch( Exception $e )
	{
		throw $e;
	}

	return $checked_upload_path;
}


function aes_crypt($field, $crypt_type=TRUE, $alias=TRUE, $return_string=TRUE)
{
	if( ! is_array($field))
		$field = array($field);


	$crypt = array();

	foreach($field as $field)
	{
		if($crypt_type === TRUE)
		{
			$crypt[]= 'AES_ENCRYPT('.$field.', UNHEX(SHA2("'.SECURITY_PASSPHRASE.'", 512)))';
		}
		else
		{
			if($alias)
			{
				if(strpos($field, '.') !== FALSE)
				{
					$ex_field 	= explode('.', $field);
					$alias		= $ex_field[1];
				}
				else
				{
					$alias	= $field;
				}

				$alias = ' AS ' . $alias;
			}
			else
			{
				$alias = '';
			}
			$crypt[]= 'AES_DECRYPT('.$field.', UNHEX(SHA2("'.SECURITY_PASSPHRASE.'", 512)))' . $alias;
		}
	}


	if($return_string)
		$crypt = implode(',' , $crypt);

	return $crypt;
}

function get_system_modules_menu($system_code, $active_sub_menu='')
{
    $CI =& get_instance();
    $params = array();
    $menu = "<ul class='cd-top-mod-nav m-l-md'>";


    $CI->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);
	$CI->load->model('code_libraries/vendor_model', 'vm_model', TRUE);


    $sys_param 		= get_sys_param_code(SYS_PARAM_MODULE_LOCATION, "LEFT");

	//  GET MENU LAYOUT VALUES
	$menu_type		= get_setting(MENU_LAYOUT, "menu_type");
	$menu_display	= get_setting(MENU_LAYOUT, "menu_child_display");

	$child_menu_class 	= ($menu_type == MENU_CLASSIC) ? "normal-sub" : "";
	$style 				= ($menu_type == MENU_CLASSIC) ? "" : "position:unset!important;";

	$child_child_menu_class = ($menu_type == MENU_CLASSIC) ? "normal-sub-child" : "";

    $exempt_modules = unserialize(EXEMPT_CORE_MODULES);

    // PARENT MODULES WITHOUT GROUPING
    $where = array("parent_module" => "IS NULL", "group_header_flag" => 0, "module_location" => $sys_param["sys_param_code"], "enabled_flag" => 1, "hide_flag" => 0);
	$modules = $CI->permissions_model->get_modules(NULL, $system_code, NULL, $where);

	//Added by kebs	( to limit modules seen by vendor depending on their account group )
	$account_groups = [];
	$ag_modules 	= [];
	$user_roles 	= $CI->session->userdata('user_roles');

	if( in_array(TASK_ROLE_VENDOR, $user_roles) )
	{
		$vendor_user 	= $CI->vm_model->get_vendor_user(['user_id' => $CI->session->userdata('user_id')],['vendor_code', 'user_id'], []);
		$vendor_ags 	= $CI->vm_model->get_vendor_account_groups(['vendor_code' => $vendor_user[0]['vendor_code']]);
		$account_groups = array_column($vendor_ags, 'account_group_code');

		$ag_modules_raw = $CI->vm_model->get_account_group_modules(['account_group_code' => ['IN', $account_groups]]);

		$ag_modules 	= array_column($ag_modules_raw, 'module_code');

		//print_var_export($ag_modules); die;
	}

    foreach ($modules as $module):

    	if( ! empty( $exempt_modules ) AND in_array($module['module_code'], $exempt_modules) )
    		continue;

        $children = $CI->permissions_model->get_modules(NULL, $system_code, $module["module_code"], array("module_location" => $sys_param["sys_param_code"]));
        $has_children = ( ! empty($children) ) ? "has-children" : "";
        $has_children_menu = ( ! empty($children) ) ? "main-menu-has-children" : "main-menu";
        $link = ( ! empty($module["link"]) ) ? "href='".base_url().$module["link"]."'" : "";
        $has_parent_access = $CI->permission->check_permission($module["module_code"]);
        $arrow_icon			= ! empty($children) ? "<span class='arrow-ico'></span>" : "";
        if ($has_parent_access === TRUE)
        {
        	$active = ( $module['module_code'] == $active_sub_menu ) ? ' current-active' : '';

	        $menu .= "<li id='".strtolower($module['module_code'])."' class='".strtolower($module['module_code']).$active." ".$has_children."' style='".$style."'>
	                    <a class='".$has_children_menu."' ".$link."><i class='material-icons'>".$module["icon"]."</i>".$module["module_name"]." ".$arrow_icon."</a>";

            if( ! empty($children) )
            {
			/* 	 */
                $ul_cnt = 1;
                $menu .= "<ul class='".$child_menu_class."'>";
                foreach($children as $child):

                	if( ! empty( $exempt_modules ) AND in_array($child['module_code'], $exempt_modules) )
						continue;

					if( $module['module_code'] == MODULE_PORTAL_TRANSACTIONS && in_array(TASK_ROLE_VENDOR, $user_roles) == TRUE && in_array($child['module_code'], $ag_modules) == FALSE)
						continue;

        			$child_children		= $CI->permissions_model->get_modules(NULL, $system_code, $child["module_code"], array("module_location" => $sys_param["sys_param_code"]));
       				$child_link			= ( ! empty($child["link"]) ) ? "href='".base_url().$child["link"]."'" : "";
                	$has_child_access	= $CI->permission->check_permission($child["module_code"]);

        			$normal_sub_has_children	= ( ! empty($child_children) ) ? "normal-sub-has-children" : "";

                	if( empty( $child['hide_flag'] ) )
                	{
                		if($has_child_access === TRUE)
                		{
                    		$menu .= "<li class='".$normal_sub_has_children."' id='".strtolower($child['module_code'])."'><a ".$child_link." class='".strtolower($menu_display)."'>";

							if($menu_display == DISPLAY_TITLE_ICON || $menu_display == DISPLAY_TITLE_ICON_DESC)
								$menu .= "<i class='material-icons'>".$child["icon"]."</i>";

							$menu .= "<div class='menu-module'>".$child["module_name"]."</div>";

							if($menu_display == DISPLAY_TITLE_DESC || $menu_display == DISPLAY_TITLE_ICON_DESC)
								$menu .= "<div class='menu-module-desc'>".$child["description"]."</div>";

							$menu .= "</a>";

							if( ! empty($child_children) )
            				{
            					$menu .= "<ul class='".$child_child_menu_class."'>";
                					foreach($child_children as $child_child):

                						if( ! empty( $exempt_modules ) AND in_array($child_child['module_code'], $exempt_modules) )
										continue;

										if( $module['module_code'] == MODULE_PORTAL_TRANSACTIONS && in_array(TASK_ROLE_VENDOR, $user_roles) == TRUE && in_array($child_child['module_code'], $ag_modules) == FALSE)
										continue;

       									$child_child_link		= ( ! empty($child_child["link"]) ) ? "href='".base_url().$child_child["link"]."'" : "";

                						$has_child_child_access = $CI->permission->check_permission($child_child["module_code"]);

                						if( empty( $child_child['hide_flag'] ) )
                						{
					                		if($has_child_child_access === TRUE)
					                		{
					                			$menu .= "<li><a ".$child_child_link." class='".strtolower($menu_display)."' style='padding: 8px 20px !important;'>";

												if($menu_display == DISPLAY_TITLE_ICON || $menu_display == DISPLAY_TITLE_ICON_DESC)
													$menu .= "<i class='material-icons'>".$child_child["icon"]."</i>";

												$menu .= "<div class='menu-module'>".$child_child["module_name"]."</div>";

												if($menu_display == DISPLAY_TITLE_DESC || $menu_display == DISPLAY_TITLE_ICON_DESC)
													$menu .= "<div class='menu-module-desc'>".$child_child["description"]."</div>";

												$menu .= "</a></li>";
					                		}
                						}

                					endforeach;
                				$menu .= "</ul>";
            				}

							$menu .= "</li>";
                		}
                    }
                endforeach;
                $menu .= "</ul>";
            }

	        $menu .= "</li>";
        }

    endforeach;

    $menu .= "</ul>";

    echo $menu;

}

function get_system_apps()
{
	$CI =& get_instance();

	$CI->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);

	$html 	  		= "";
	$user_systems 	= $CI->session->user_systems;
	$systems 	  	= $CI->systems->get_systems();

	if( !EMPTY( $user_systems ) )
	{
		$root_path 	= get_root_path();

		$html .= "<ul>";
		foreach ($systems as $system):
			// CHECK IF USER HAS ACCESS TO THE SYSTEM
			if(in_array($system['system_code'], $user_systems))
			{
				$img_src 		= base_url().PATH_IMAGES."default_app_logo.png";

				if( !EMPTY( $system["logo"] ) )
				{

					$photo_path = $root_path.PATH_SYSTEMS_UPLOADS.$system["logo"];
					$photo_path = str_replace(array('\\','/'), array(DS,DS), $photo_path);

					if( file_exists( $photo_path ) )
					{
						$img_src = output_image($system["logo"], PATH_SYSTEMS_UPLOADS);
					}
				}

				$selected = ($CI->session->current_system == $system["system_code"])? "selected" : "";
				$html .= "<li class='".$selected."'><a href='".base_url() . $system["link"]."'><img src='".$img_src."' /> ".$system["system_name"]."</a></li>";
			}
		endforeach;
		$html .= "</ul>";
	}

	return $html;
}

function get_system_logo($system_code)
{
	$CI =& get_instance();

	$CI->load->model(CORE_COMMON.'/systems_model', 'systems', TRUE);

	$detail = $CI->systems->get_systems($system_code);
	$logo	= $detail['logo'];

	$org_pic 			= base_url().PATH_IMAGES.DEFAULT_ORG_LOGO;
	$change_upload_path	= get_setting(MEDIA_SETTINGS, "change_upload_path");

	$ROOT_PATH 			= get_root_path();

	if( !EMPTY( $logo ) )
	{
		$org_pic_path 		= $ROOT_PATH. PATH_SYSTEMS_UPLOADS . $logo;
		$org_pic_path 		= str_replace(array('\\','/'), array(DS,DS), $org_pic_path);

		if( file_exists( $org_pic_path ) )
		{
			$org_pic_src	 = base_url() . PATH_SYSTEMS_UPLOADS . $logo;

			if( !EMPTY( $change_upload_path ) )
			{
				$org_pic_src = output_image($logo, PATH_SYSTEMS_UPLOADS);
			}

			$org_pic 	 	 = $org_pic_src;
		}
	}

	return $org_pic;
}

function get_link_url($module_code = NULL, $ref_no = NULL,  $tab = NULL, $base_url_cron = NULL)
{
	$module_folder 	= PORTAL_TRANSACTIONS;
	$dir_name		= '';
	$keyword		= (!EMPTY($ref_no))? '?keyword='.$ref_no: '';
	$tab			= (!EMPTY($tab))? $tab: '';

	switch ($module_code) {
		//Contract Growers
		case MODULE_PORTAL_TRANS_CONTRACT_GROWERS:
			$dir_name = PORTAL_CONTRACT_GROWER;
			break;

		//Goods Goods
		case MODULE_PORTAL_TRANS_GOODS_GOODS:
			$dir_name = PORTAL_GOODS_GOOD;
			break;

		//Goods Marinades
		case MODULE_PORTAL_TRANS_GOODS_MARINADES:
			$dir_name = PORTAL_GOODS_MARINADE;
			break;

		//Toll Partners
		case MODULE_PORTAL_TRANS_TOLL_PARTNERS:
			$dir_name = PORTAL_TOLL_PARTNERS;
			break;

		//Forwarders
		case MODULE_PORTAL_TRANS_FORWARDERS:
			$dir_name = PORTAL_FORWARDER;
			break;

		//Inbound Truckers
		case MODULE_PORTAL_TRANS_INBOUND_TRUCKERS:
			$dir_name = PORTAL_INBOUND_TRUCKER;
			break;

		//Outbound Truckers
		case MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS:
			$dir_name = PORTAL_OUTBOUND_TRUCKER;
			break;

		//Feedmill Truckers
		case MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS:
			$dir_name = PORTAL_FEEDMILL_TRUCKER;
			break;

		//Contractors
		case MODULE_PORTAL_TRANS_CONTRACTORS:
			$dir_name = PORTAL_CONTRACTOR;
			break;

		//Lessors
		case MODULE_PORTAL_TRANS_LESSORS:
			$dir_name = PORTAL_LESSOR;
			break;

		//Manpower Agency
		case MODULE_PORTAL_TRANS_MANPOWER:
			$dir_name = PORTAL_MANPOWERS;
			break;

		//SOA Based
		case MODULE_PORTAL_TRANS_SOA_BASED:
			$dir_name = PORTAL_SOA_BASED;
			break;

		//Import Reference
		case MODULE_PORTAL_IMPORT_REFERENCE:
			$module_folder = PORTAL_IMPORT_REFERENCE;
			$dir_name = 'index';
			break;

		default:
			# code...
			break;
	}

	$link_url = ((EMPTY($base_url_cron))? base_url(): $base_url_cron) . $module_folder.'/'.$dir_name.$keyword.$tab;

	return $link_url;
}

function display_standard_amount($amount){
	return !EMPTY($amount) ? number_format($amount, DECIMAL_PLACES) : '0.00';
}