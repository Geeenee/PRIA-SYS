<?php 
$lname = "";
$fname = "";
$mname = "";
$nickname = "";
$female = "";
$male = "checked";
$job_title = "";
$contact_no = "";
$mobile_no = "";
$photo = "";

if(ISSET($user)){
	$sp_status = get_sys_param_val(SYS_PARAM_STATUS, $user["status"]);
	$sp_gender = get_sys_param_val(SYS_PARAM_GENDER, $user["gender"]);
	
	$lname = (!EMPTY($user["lname"]))? $user["lname"] : "";
	$fname = (!EMPTY($user["fname"]))? $user["fname"] : "";
	$mname = (!EMPTY($user["mname"]))? $user["mname"] : "";
	
	$nickname = (!EMPTY($user["nickname"]))? $user["nickname"] : "";
	$female = ($sp_gender["sys_param_value"] == FEMALE)? "checked" : "";
	$male = ($sp_gender["sys_param_value"] == MALE)? "checked" : "";
	$job_title = (!EMPTY($user["job_title"]))? $user["job_title"] : "";
	$contact_no = (!EMPTY($user["contact_no"]))? $user["contact_no"] : "";
	$mobile_no = (!EMPTY($user["mobile_no"]))? $user["mobile_no"] : "";
	
	$photo = (!EMPTY($user["photo"]))? $user["photo"] : "";
}
?>
<input type="hidden" name="image" id="avatar" value="<?php echo $photo ?>"/>

<div class="row m-b-n">
	<div class="col s12">
		<div class="row m-b-n">
			<div class="col s5 p-l-n">
				<div class="input-field">
					<input type="text" name="lname" id="lname" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" value="<?php echo $lname ?>" class="white" />
					<label for="lname" class="required">Last Name</label>
				</div>
			</div>
			<div class="col s5">
				<div class="input-field">
					<input type="text" name="fname" id="fname" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup" value="<?php echo $fname ?>" class="white"/>
					<label for="fname" class="required">First Name</label>
				</div>
			</div>
			<div class="col s2 p-r-n">
				<div class="input-field">
					<input type="text" name="mname" id="mname" value="<?php echo $mname ?>" class="white"/>
					<label for="mname">Middle Initial</label>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row m-b-n">
	<div class="col s5 p-t-md">
		<div class="input-field">
			<input type="text" name="nickname" id="nickname" value="<?php echo $nickname ?>" class="white"/>
			<label for="nickname">Nickname</label>
		</div>
	</div>
	<div class="col s2 p-t-md">
		<div class="input-field">
			<label for="user_gender_male" class="active required">Sex</label>
			<input type="radio" class="labelauty" name="gender" id="user_gender_male" value="<?php echo MALE ?>" data-labelauty="Male" <?php echo $male ?>/>
		</div>
	</div>
	<div class="col s3 p-t-md p-l-n m-l-n-lg">
		<div class="input-field">
			<input type="radio" class="labelauty" name="gender" id="user_gender_female" value="<?php echo FEMALE ?>" data-labelauty="Female" <?php echo $female ?>/>
		</div>
	</div>
</div>
<div class="row m-t-md">
	<div class="col s6">
		<div class="input-field">
			<input type="text" name="contact_no" id="contact_no" value="<?php echo $contact_no ?>" data-parsley-trigger="keyup" data-parsley-pattern="^[0-9-()+ ]+$" data-parsley-pattern-message="Telephone no. must contain parenthesis '()', dash '-', or numeric values only" class="white"/>
			<label for="contact_no">Telephone No.</label>
		</div>
	</div>
	<div class="col s6">
		<div class="input-field">
			<div class="input-group">
				<div class="input-group-addon">+ 63</div>
				<input type="text" name="mobile_no" id="mobile_no" value="<?php echo $mobile_no ?>" data-parsley-required="false" class="white"/>
				<label for="mobile_no">Mobile No.</label>
			</div>
		</div>
	</div>
</div>
<div class="row m-t-md">
	<!-- <div class="col s9">
		<div class="input-field">
			<label class="label block required">Department/Agency/Organization</label>
			<select name="org" id="org" class="selectize" placeholder="Select Agency" data-parsley-required="true" data-parsley-validation-threshold="0" data-parsley-trigger="keyup">
				<option value="">Select Agency</option>
				<?php 
					//if( ISSET( $real_orgs ) AND !EMPTY( $real_orgs ) ) :
				?>	
				<?php 
					//echo $real_orgs;
				?>
				<?php
					//endif;
				?>
			</select>
		</div>
	</div> -->
	<div class="col s3">
		<div class="input-field m-t-md">
			<input type="text" name="job_title" id="job_title" value="<?php echo $job_title ?>" class="white"/>
			<label for="job_title">Job Title</label>
		</div>
	</div>
</div>
<div class="row">
	<div class="col s12">
		<h5 class="form-header">Display Image</h5>
		<div class="help-text">Select and upload your latest photo to help others recognize this account.</div>
		<div id="avatar_upload">Select File</div>
	</div>
</div>