<?php 

$email = "";
$username = "";
$pw_note = "";
$status = "checked";
$contact_only_flag = "";
$create_flag = "checked";
$user_vendor_code = "";
$user_orgs = array();

if(ISSET($user)){
	$sp_status = get_sys_param_val(SYS_PARAM_STATUS, $user["status"]);
	
	$email = (!EMPTY($user["email"]))? $user["email"] : "";
	$username = (!EMPTY($user["username"]))? $user["username"] : "";
	$pw_note = "Type in a new password below to reset / change current password.";
	$status = ($sp_status["sys_param_value"] == ACTIVE)? "checked" : "";
	$contact_only_flag = ($user["contact_flag"]) ? "checked" : "";
	$create_flag = (!$user["contact_flag"]) ? "checked" : "";

	$user_vendor_code = (!EMPTY($user["vendor_code"]))? $user["vendor_code"] : "";
	$user_orgs = (ISSET($org_codes))? $org_codes : array();

	$vendor_select		= (in_array(TASK_ROLE_VENDOR, $main_role))? TRUE: FALSE;
	$user_vendor_code	= (!$vendor_select)? "": $user_vendor_code;
}

?>
<div class="row m-b-n">
	<div class="col s12">
		<div class="input-field">
			<label class="active required" for="contact_type">Account Type</label>
			<div class="row labelauty-list m-n">
				<div class="col s3 p-n">
					<input type="radio" class="labelauty contact_flag" name="contact_type" id="contact_user" value="0" data-labelauty="Create this user an account" <?php echo $create_flag ?>/>
				</div>
				<div class="col s3 p-n">
					<input type="radio" class="labelauty contact_flag" name="contact_type" id="contact_only" value="1" data-labelauty="Tag user as contact only" <?php echo $contact_only_flag ?>/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row m-t-md m-b-n">
	<div class="col s6">
		<div class="input-field">
			<input type="email" name="email" id="email" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" data-parsley-type="email" value="<?php echo $email ?>" class="white"/>
			<label for="email" class="required">Email Address</label>
		</div>
	</div>
	<div class="col s6">
		<div class="input-field">
			<input type="text" name="username" id="username" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" value="<?php echo $username ?>" class="white"/>
			<label for="username" class="required">Username</label>
		</div>
	</div>
</div>

<?php 
	if( $admnin_set_password ) :
?>
<div class="row m-b-n m-t-md">
	<div class="col s6">
		<div class="input-field">
			<input type="password" name="password" id="password" <?php if(!ISSET($user)){ ?> data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" <?php } ?> class="white"/>
			<label for="password" <?php if(!ISSET($user)){ ?>class="required" <?php } ?>>Password</label>
			<div class="help-text"><?php echo $pw_note ?></div>
		</div>
	</div>
	<div class="col s6">
		<div class="input-field">
			<input type="password" name="confirm_password" id="confirm_password" <?php if(!ISSET($user)){ ?> data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" <?php } ?> class="white"/>
			<label for="confirm_password" <?php if(!ISSET($user)){ ?>class="required" <?php } ?>>Confirm Password</label>
		</div>
	</div>
</div>
<?php endif; ?>
<div class="row m-b-n m-t-md">
	<div class="col s5">
		<div class="input-field">
			<select name="main_role[]" id="main_role" class="selectize" placeholder="Select Main User Role" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="change">
				<option value="">Select Main User Role</option>
				<?php //foreach ($roles as $role): 
					//$sel_main_role 	= ( !EMPTY( $main_role ) AND in_array($role['role_code'], $main_role) ) ? 'selected' : '';
				?>

				<!--option value="<?php echo $role["role_code"] ?>" <?php echo $sel_main_role ?>><?php echo $role["role_name"] ?></option-->
				<?php //endforeach; ?>
			</select>
			<label for="main_role" class="active required">Main Role</label>
			<div class="help-text m-t-sm">Assign main role to this account</div>
		</div>
	</div>
	<div class="col s5 <?php echo ($vendor_select) ? 'hide' : ''; ?>" id="div_role">
		<div class="input-field">
			<select name="role[]" id="role" class="selectize" placeholder="Select Other Role" multiple data-parsley-required="false" data-parsley-validation-threshold="0" data-parsley-trigger="keyup">
				<option value="">Select Other Role</option>
				<?php //foreach ($roles as $role): ?>
				<!--option value="<?php echo $role["role_code"] ?>"><?php echo $role["role_name"] ?></option-->
				<?php //endforeach; ?>
			</select>
			<label for="role" class="active">Other Role</label>
		</div>
	</div>
	
	<div class="col s5 <?php echo ($vendor_select) ? '' : 'hide'; ?>" id="div_vendor">
		<div class="input-field">
			<select name="vendor" id="vendor" class="selectize" placeholder="Select Vendor" data-parsley-required="false" data-parsley-validation-threshold="0" data-parsley-trigger="keyup">
				<option value="">Select Vendor</option>
				<?php //foreach ($vendors as $vendor): ?>
				<!--option <?php echo ($user_vendor_code == $vendor['vendor_code']) ? 'selected' : ''; ?> value="<?php echo $vendor["vendor_code"] ?>"><?php echo $vendor["vendor_name"] ?></option-->
				<?php //endforeach; ?>
			</select>
			<label for="role" class="active">Vendor</label>
		</div>
	</div>


	<div class="col s2">
		<div class="input-field">
			<label for="status" class="active required">Status</label>
			<input type="checkbox" class="labelauty" name="status" id="status" value="<?php echo ACTIVE ?>" data-labelauty="Inactive|Active" <?php echo $status ?>/>
		</div>
	</div>
</div>

<div class="row m-b-n m-t-md">
	<div class="col s5">
		<div class="input-field">
			<label class="label block required">Department/Agency/Organization</label>
			<select name="org[]" id="org" class="selectize" multiple placeholder="Select Agency" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup">
				<option value="">Select Agency</option>
				<?php 
					/*if( ISSET( $real_orgs ) AND !EMPTY( $real_orgs ) ) :
					echo $real_orgs;
					endif;*/
				?>
			</select>
		</div>
	</div>
</div>

<div class="row m-b-n m-t-md hide">
	<div class="col s5">
		<div class="input-field">
			<select name="groups[]" multiple="" id="groups_user_sel" class="selectize" placeholder="Select Groups" data-parsley-validation-threshold="0" data-parsley-trigger="change">
				<option value="">Select Groups</option>
				<?php //foreach ($all_groups as $group): 
					//$id_group 	= base64_url_encode( $group['group_id'] );
					//$sel_grp 	= ( !EMPTY( $user_groups ) AND in_array($group['group_id'], $user_groups) ) ? 'selected' : '';
				?>

				<!--option value="<?php echo $id_group ?>" <?php echo $sel_grp ?>><?php echo $group['group_name'] ?></option-->
				<?php //endforeach; ?>
			</select>
			<label for="groups_user_sel" class="">Groups</label>
		</div>
	</div>
</div>

<input type="hidden" id="xorg" value="<?php print $xorg; ?>" />
<input type="hidden" id="xmain_role" value="<?php print $xmain_role; ?>" />
<input type="hidden" id="xother_role" value="<?php print $xother_role; ?>" />
<input type="hidden" id="xvendor_code" value="<?php print $user_vendor_code; ?>" />